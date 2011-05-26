<?php

class taket_afp_import_obj extends _int_object
{
	const CLID = 1496;

	private $prod_fld;
	private $org_fld;

	private $warehouse; // ???
	private $warehouses = array();

	// short code controller
	private $controller_inst;
	private $controller_id;

	// this is a cache class, i use it to make db queries
	private $db_obj; 

	private function init_vars()
	{
		// ERROR REPORTING
		automatweb::$instance->mode(automatweb::MODE_DBG);

		// this will take some time
		aw_set_exec_time(AW_LONG_PROCESS);

		// controller for short product codes ...
		if($this->controller_id = $this->prop("code_ctrl"))
		{
			$this->controller_inst = get_instance(CL_CFGCONTROLLER);	
		}

		$this->db_obj = $GLOBALS["object_loader"]->ds;
		
		$this->warehouses = $this->get_warehouses();

		$this->prod_fld = $this->get_products_folder();
		$this->org_fld = $this->get_suppliers_folder();
	}

	private function download_amounts_file()
	{
		$warehouses = $this->get_warehouses();

		$this->print_line("Download amounts files ... ");
		// download amounts files
		foreach ($warehouses as $oid => $name)
		{
			$o = new object($oid);
			$adr = $o->comment();

			$download_urls[$oid] = $adr."/amounts.csv";
		}

		$res = $this->parallel_url_fetch($download_urls);

		foreach ($res as $oid => $v)
		{
			$filename = aw_ini_get('site_basedir').'/files/amounts_'.$oid.'.csv';
			file_put_contents($filename, $v);
			$this->print_line("saved file: ".$filename);
		}
	}

	public function get_suppliers()
	{
		$ol = new object_list(array(
			'class_id' => CL_CRM_COMPANY,
			'parent' => $this->org_fld
		));

		return $ol->arr();
	}

	private function update_suppliers($suppliers)
	{
		$ol = new object_list(array(
			'class_id' => CL_CRM_COMPANY,
			'code' => array_keys($suppliers)
		));

		// update existing supplier organisation objects:
		foreach ($ol->arr() as $oid => $o)
		{
			echo "Update supplier obj (".$oid.") <strong>".$o->name()."</strong> ";
			if ($o->name() != $suppliers[$o->prop('code')])
			{
				echo "set to <strong>".$suppliers[$o->prop('code')]."</strong> ";

				$o->set_name($suppliers[$o->prop('code')]);
				$o->save();
			}
			echo "code: [".$o->prop('code')."]<br />\n";
			unset($suppliers[$o->prop('code')]);
		}

		// remaining suppliers are not present in aw, so lets add them:
		foreach ($suppliers as $id => $name)
		{
			echo "Create new organisation (".$id.") - <strong>".$name."</strong> ... ";
			$org = obj();
			$org->set_class_id(CL_CRM_COMPANY);
			$org->set_name($name);
			$org->set_parent($this->org_fld);
			$org->set_prop("code", $id);
			$org->save();
			echo "[ok]<br />\n";
		}
		echo "Suppliers updated<br />\n";
	}

	// obsolete
	private function add_product($product)
	{
		$code = urldecode($product["product_code"]);

		$o = $product['oid'];

		if(!$o)
		{
			$o = obj();
			$o->set_class_id(CL_SHOP_PRODUCT);
			$o->set_parent($this->prod_fld);
			$o->save();

			$o->connect(array(
				"type" => "RELTYPE_WAREHOUSE",
				"to" => $this->warehouse,
			));

			$org_id = urldecode($product["supplier_id"]);
			$ol = new object_list(array(
				"class_id" => CL_CRM_COMPANY,
				"site_id" => array(),
				"lang_id" => array(),
				"code" => $org_id,
			));
			$org = $ol->begin();

			if(!$org)
			{
				$org = obj();
				$org->set_class_id(CL_CRM_COMPANY);
				$org->set_name(urldecode($product["supplier_name"]));
				$org->set_parent($this->org_fld);
				$org->set_prop("code", $org_id);
				$org->save();
			}

			for($i = 0; $i < 6; $i++)
			{
				$p_o = obj();
				$p_o->set_class_id(CL_SHOP_PRODUCT_PURVEYANCE);
				$p_o->set_parent($o->id());
				$p_o->set_name(sprintf(t("%s tarnetingimus"), $o->name()));
				$p_o->set_prop("warehouse", $this->whs[$i]);
				$p_o->set_prop("company", $org->id());
				$p_o->set_prop("product", $o->id());
				$p_o->save();
			}

			echo $k.' -- '.urldecode($v['product_name']).' ('.$code.') created ...<br>'."\n";
		}
		else
		{
			echo $k.' -- '.urldecode($v['product_name']).' ('.$code.') already existed ...<br>'."\n";
			$o = new object($o);
		}

		$o->set_name(urldecode($product["product_name"]));

		$o->set_prop("code", $code);

		if($ctrli)
		{
			$short_code = $ctrli->check_property($cid, null, $code, null, null, null);
			if ($o->prop('short_code') != $short_code)
			{
				$o->set_prop("short_code", $short_code);
				$changed = true;
			}
		}
		$o->set_prop("search_term", urldecode($product["search_term"]));
		$o->set_prop("user1", urldecode($product["replacement_product_code"]));
		$o->save();
	}

	// I'll assume here, that the product object doesn't exist in aw
	// So I need to add a line in objects table, aw_shop_products table and make those purveyance objects
	private function add_product_sql($data)
	{
		$obj_base = $this->db_obj->db_fetch_array("select * from objects where class_id = '".CL_SHOP_PRODUCT."' limit 1");
		$obj_base = reset($obj_base);
		$obj_base['oid'] = 0;
		$obj_base['createdby'] = '110';
		$obj_base['modifiedby'] = '110';

		$obj_base['name'] = addslashes($data['product_name']);
		$obj_base['comment'] = addslashes($data['product_code']);
		$sql = "
			INSERT INTO 
				objects 
			VALUES (".implode(',', map('"%s"', $obj_base)).");
		";

		$this->db_obj->db_query($sql);

		$oid = $this->db_obj->db_last_insert_id();

		// brother_of value has to be the same as oid
		$this->db_obj->db_query("UPDATE objects set brother_of = ".$oid." WHERE oid = ".$oid);

		$sql = "
			INSERT INTO 
				aw_shop_products 
			SET
				aw_oid = ".$oid.",
				code = '".addslashes($data['product_code'])."',
				search_term = '".addslashes($data['search_term'])."',
				type_code = '".addslashes($data['replacement_product_code'])."',
				short_code = '".$this->apply_controller($data['product_code'])."'
		";
		$this->db_obj->db_query($sql);
		return $oid;
	}

	// the product object exists in the system, so i need to update the data
	private function update_product_sql($oid, $data)
	{
		$sql = "
			UPDATE 
				objects
			SET
				name = '".addslashes($data['product_name'])."',
				comment = '".addslashes($data['product_code'])."',
				brother_of = ".$oid."
			WHERE
				oid = ".$oid."
		";
		$this->db_obj->db_query($sql);

		$sql = "
			UPDATE
				aw_shop_products
			SET
				code = '".addslashes($data['product_code'])."',
				search_term = '".addslashes($data['search_term'])."',
				type_code = '".addslashes($data['replacement_product_code'])."',
				short_code = '".$this->apply_controller($data['product_code'])."'
			WHERE
				aw_oid = ".$oid."
		";
		$this->db_obj->db_query($sql);
	}

	// maybe i don't have to make this one with sql-s?
	private function delete_product_sql($oid)
	{
		$sql = "DELETE FROM objects WHERE oid = ".$oid;
		$this->db_obj->db_query($sql);

		$sql = "DELETE FROM aw_shop_products WHERE aw_oid = ".$oid;
		$this->db_obj->db_query($sql);
	}


	private function update_purveyances($data)
	{
		$sql = "
			SELECT
				oid,
				name,
				warehouse,
				company,
				product
			FROM
				objects LEFT JOIN aw_shop_product_purveyance ON (oid = aw_oid)
			WHERE
				parent = ".$data['product_oid']." and
				class_id = '".CL_SHOP_PRODUCT_PURVEYANCE."'
		";
		$res = $this->db_obj->db_fetch_array($sql);

		$purveyances = array();
		foreach ($res as $v)
		{
			$purveyances[$v['warehouse']] = $v;
		}
		$wh_counter = 1;
		foreach ($this->warehouses as $wh_oid => $wh)
		{
			if (!isset($purveyances[$wh_oid]))
			{
				$this->add_purveyance_sql(array(
					'parent' => $data['product_oid'],
					'product_oid' => $data['product_oid'],
					'product_name' => $data['product_name'],
					'warehouse_oid' => $wh_oid,
					'supplier_oid' => 0
				));
				echo "[ ".$wh_counter." ] Added purveyance: ".$data['product_name']." => ".$wh."<br />\n";
			}
			else
			{
			//	$this->update_purveyance_sql($purveyances[$wh_oid]['oid'], array(
			//		'parent' => $data['product_oid'],
			//		'product_oid' => $data['product_oid'],
			//		'product_name' => $data['product_name'],
			//		'warehouse_oid' => $wh_oid,
			//		'supplier_oid' => 0
			//	));
			//	echo "Updated purveyance: ".$data['product_name']." => ".$wh."<br />\n";
			//	echo "[ ".$wh_counter." ] Updated purveyance: ".$data['product_name']." => ".$wh."<br />\n";
			}
			$wh_counter++;
		}

	}

	////
	// it needs params:
	// - parent - product objects oid
	// - product_name
	// - supplier_oid
	// - warehouse_oid
	// - product_oid
	private function add_purveyance_sql($arr)
	{
		$obj_base = $this->db_obj->db_fetch_array("select * from objects where class_id = '".CL_SHOP_PRODUCT_PURVEYANCE."' limit 1");
		$obj_base = reset($obj_base);
		$obj_base['oid'] = 0;
		$obj_base['createdby'] = '110';
		$obj_base['modifiedby'] = '110';
		$obj_base['parent'] = $arr['parent'];
		// i should add prod code as well actually, to make the name more informative and therefore useful
		$obj_base['name'] = sprintf(t("%s tarnetingimus"), addslashes($arr['product_name']));

		$sql = "
			INSERT INTO 
				objects 
			VALUES (".implode(',', map('"%s"', $obj_base)).");
		";

		$this->db_obj->db_query($sql);

		$oid = $this->db_obj->db_last_insert_id();
		
		// brother_of value has to be the same as oid
		$this->db_obj->db_query("UPDATE objects set brother_of = ".$oid." WHERE oid = ".$oid);

		$sql = "
			INSERT INTO 
				aw_shop_product_purveyance
			SET
				aw_oid = ".$oid.",
				warehouse = ".$arr['warehouse_oid'].",
				company = ".$arr['supplier_oid'].",
				product = ".$arr['product_oid']."
		";
		$this->db_obj->db_query($sql);
		echo "Insert new product puveyance: <br />\n";
	}

	private function update_purveyance_sql($oid, $data)
	{
		$sql = "
			UPDATE 
				objects
			SET
				name = '".addslashes($data['product_name'])."',
				brother_of = ".$oid."
			WHERE
				oid = ".$oid."
		";
		$this->db_obj->db_query($sql);

		$sql = "
			UPDATE
				aw_shop_product_purveyance
			SET
				warehouse = ".$data['warehouse_oid'].",
				company = ".$data['supplier_oid'].",
				product = ".$data['product_oid']."
			WHERE
				aw_oid = ".$oid."
		";
		$this->db_obj->db_query($sql);
	
	}

	private function delete_purveyance_sql($oid)
	{
		$sql = "DELETE FROM objects WHERE oid = ".$oid;
		$this->db_obj->db_query($sql);

		$sql = "DELETE FROM aw_shop_product_purveyance WHERE aw_oid = ".$oid;
		$this->db_obj->db_query($sql);
	
	}

	private function check_tables()
	{
/*		$sql = "DROP TABLE `products`;";
		$res = $this->db->db_query($sql);
		$this->print_line("Droppis tabeli");		
*/
		$sql = "CREATE TABLE IF NOT EXISTS `products` (
			id INT NOT NULL AUTO_INCREMENT,
			code VARCHAR(32),
			name VARCHAR(128),
			rep_code VARCHAR(32),
			short_code VARCHAR(32),
			short_term VARCHAR(52),
			search_term VARCHAR(52),
			price double,
			special_price double,
			supplier_id int,
			disc_code TINYINT,
			PRIMARY KEY (id)
		);";
		$res = $this->db->db_query($sql);


/*		$sql = "CREATE INDEX product_search ON products(short_code,short_term);";
		$res = $this->db->db_query($sql);
*//*
		$this->print_line("yritas uut toodete tabelit luua");	

		$sql = "CREATE INDEX code_index ON products(code);";
		$res = $this->db->db_query($sql);
		$sql = "CREATE INDEX name_index ON products(name);";
		$res = $this->db->db_query($sql);


		$sql = "CREATE INDEX rep_code_index ON products(rep_code);";
		$res = $this->db->db_query($sql);
/*		$sql = "CREATE INDEX short_index ON products(short_code);";
		$res = $this->db->db_query($sql);
		$sql = "CREATE INDEX term_index ON products(short_term);";
		$res = $this->db->db_query($sql);
		$this->print_line("Indeks valmis");	
*/



/*		$sql = "DROP TABLE `amounts`;";
		$res = $this->db->db_query($sql);
*/
		$sql = "CREATE TABLE IF NOT EXISTS `amounts` (
			id INT,
			code VARCHAR(32),
			warehouse INT,
			amount INT,
			foreign key (id) references products(id)
		);";
		$res = $this->db->db_query($sql);
/*		$sql = "CREATE INDEX code_index ON amounts(code);";
		$res = $this->db->db_query($sql);
		$sql = "CREATE INDEX warehouse_index ON amounts(warehouse,amount);";
		$res = $this->db->db_query($sql);
		$sql = "CREATE INDEX amount_index ON amounts(amount,warehouse);";
		$res = $this->db->db_query($sql);*/
		//yritas tootekoguste tabelit luua

/*
		$sql = "ALTER TABLE aw_crm_bill CHANGE aw_bill_no aw_bill_no INT;";
		$res = $this->db->db_query($sql);
*/
		$sql = "CREATE TABLE IF NOT EXISTS `discount` (
			code TINYINT,
			customer INT,
			discount double
		);";
		$res = $this->db->db_query($sql);
	}

	public function do_import()
	{
		$this->db = $GLOBALS["object_loader"]->ds;
		$this->check_tables();
		$this->product_codes = array();

		$sql = "select id,code from products";
		$this->db->db_query($sql);
		while ($row = $this->db->db_next()){
			$this->product_codes[$this->fuck($row["code"])] = $row["id"] ;
		}
		$this->print_line('Products count '.sizeof($this->product_codes));

		$warehouses = $this->warehouse_list();
		foreach($warehouses as $warehouse)
		{
			$this->import_warehouse_data($warehouse);
		}

		die("valmis");
	}

	private function import_warehouse_data($whd)
	{
		arr("impordib andmeid laost: ".$whd["name"]);
		if($whd["id"] == aw_ini_get("main_warehouse"))
		{
			$this->do_products_import($whd);
	//		$this->do_delivery_import($whd);

	//		$this->do_discounts_import($whd);
		//	$this->do_users_import($wd); 
		}
	//	$this->do_afp_users_import($whd);
	
	//	$this->do_amounts_import($whd);

	}

	function do_afp_users_import($wh)
	{
		if($wh["id"] != "6411")
		{
			return;
		}
		$this->print_line("AFP users import ..... get file" , false);
		$url = new aw_uri($wh["info"].'/index.php');
		$url->set_arg('get_afp_users', 1);

		$taket = obj(aw_ini_get("taket_co"));
		$result = file($url->get());
//		arr($result);
		$this->print_line('[done]');
	//	$result = unserialize($result);

		$count = 0;

		foreach($result as $res)
		{
	//		arr($res);
			$res = str_replace('"' , '' , $res);
			arr($res);
			$count++;
			if($count < 2) continue;
		//	if($count > 4) break;
			$data = explode("	" , $res);

			$name = $data[5];
			$name = utf8_encode($this->fuck($name));
			$firstname = utf8_encode($this->fuck($data[3]));
			$lastname = utf8_encode($this->fuck($data[4]));



			$ol = new object_list(array(
				"name" => $name,
				"class_id" => CL_CRM_PERSON,
				"firstname" => $firstname,
				"lastname" => $lastname,
			));

			if($ol->count())
			{
				$person = $ol->begin();
			}
			else
			{
				$person = new object();
				$person->set_parent($taket->id());
				$person->set_name($name);
				$person->set_class_id(CL_CRM_PERSON);
				$person->set_prop("firstname" , $firstname);
				$person->set_prop("lastname" , $lastname);
				$person->save();

				$person->add_work_relation(array(
					"org" => $taket,
				));

				$person->set_meta("taket" , $data);
				$person->add_phone($data[12]);
				$person->add_address(utf8_encode($this->fuck($data[6])));

				$person->save();

				$user = new object();
				$user->set_class_id(CL_USER);
				$user->set_name(utf8_encode($this->fuck($data[0])));
				$user->set_parent($taket->id());
				$user->set_prop("uid" , utf8_encode($this->fuck($data[0])));
				$user->set_password(utf8_encode($this->fuck($data[1])));
				$user->save();
	//			$user->add_to_group($group);//keyword
				$user->connect(array(
					"to" => $person->id(),
					"reltype" => "RELTYPE_PERSON"
				));
			}
		}
	}

/*
    [0] => MP_KAYTTAJATUNNUS
    [1] => MP_SALASANA
    [2] => MP_KAYTTAJATASO
    [3] => MP_ETUNIMI
    [4] => MP_SUKUNIMI
    [5] => MP_KOKONIMI
    [6] => MP_KATUOSOITE
    [7] => MP_POSTINUMERO
    [8] => MP_POSTITOIMIPAIKKA
    [9] => MP_PUHELIN_TYO
    [10] => MP_PUHELIN_KOTI
    [11] => MP_SAHKOPOSTI
    [12] => MP_PUHELIN_GSM
    [13] => MP_PUHELIN_FAX
    [14] => MP_OSASTO
    [15] => MP_OSASTOKOODI
*/

	private function do_delivery_import($wh)
	{
		require(aw_ini_get("basedir")."addons/ixr/IXR_Library.inc.php");
		$client = new IXR_Client($wh["host"], $wh["path"], $wh["port"]);
		$client->query('server.getTransportTypes',array());
		$data2 = $client->getResponse();
		$this->transport_types = $data2;
//		arr($this->transport_types);
		$ol = new object_list(array(
			"class_id" => CL_SHOP_DELIVERY_METHOD,
		));

		$saast = array();
		foreach($ol->arr() as $o)
		{
			$saast[$o->comment()] = $o->id();
		}
		foreach($this->transport_types as $type)
		{
			if(empty($saast[$type["transport_id"]]))
			{
				$o = new object();
				$o->set_class_id(CL_SHOP_DELIVERY_METHOD);
				$o->set_parent(aw_ini_get("main_warehouse"));
				$o->set_name($type["transport_name"]);
				$o->set_comment($type["transport_id"]);
				$o->save();
			}
		}
	}

	function do_users_import()
	{

		$taket = obj(aw_ini_get("taket_co"));

//-------------------- organisatsioonid ------------------ 

		$orgs = array();
		$gud = file("http://84.50.96.150:8080/xmlrpc/index.php?get_users_data=1");
		$count = 0;


		foreach($gud as $key => $val)
		{
			if($count%100 == 0)
			{
				print $count."<br>";
				flush();
			}

			if($count > 1)
			{
				$data = explode("	" , $val);
				//kategooria
				$org = $this->add_co_if_no_ex(utf8_encode($data[3]), $taket->id());
				$count++;
				if(isset($orgs[$org->id()]))
				{
					continue;
				}
				$orgs[$org->id()] = 1;
				$org->set_prop("reg_nr" , $data[20]);
				$org->set_prop("tax_nr" , $data[19]);

/*				$cat = $taket->add_cat_if_there_is_none($data[18]);
				$rel = $taket->get_customer_relation(2, $org, true);
				$rel->add_category(obj($cat));
				$org->add_phone($data[8]);
				$org->add_phone($data[9]);
				$org->add_phone($data[10]);
				$org->add_phone($data[11]);
				$org->add_address(array(
					"use_existing" => 1,
					"address" => utf8_encode($data[4]),
					"index" => $data[6],
					));
				$org->add_mail($data[7]);
				$org->add_url($data[12]);
				$org->set_meta("taket" , $data);
				$org->set_prop("code" , $data[0]);*/
				$org->save();
			}
			else $count++;

		}
//------------------------------------------------------- isikud
/*		$gpd = file("http://88.196.208.74:8888/xmlrpc/index.php?get_persons_data=1");

		$count = 0;
		foreach($gpd as $key => $val)
		{
			if($count%100 == 0)
			{
				print $count."<br>";
				flush();
			}
			if(true)// $count > 1)
			{
				$data = explode("	" , $val);
				//kategooria
				$count++;
				$person = $this->add_person_if_no_ex($data[0], $taket->id() , utf8_encode($data[4]));
				$org = $this->get_org_by_code($data[1]);

				$person->add_work_relation(array(
					"org" => $org,
					"profession" => utf8_encode($data[5]),
				));

				$person->set_meta("taket" , $data);
				$person->add_mail($data[9]);
				$person->add_phone($data[6]);
				$person->add_phone($data[7]);
				$person->set_prop("firstname" , utf8_encode($data[2]));
				$person->set_prop("lastname" , utf8_encode($data[3]));
				$person->set_prop("code" , $data[1]);
				$person->save();
			}
			else $count++;

		}

*/

/*		$cl_user_creator = new crm_user_creator();

		
		$ol = new object_list(array(
			"class_id" => CL_CRM_PERSON,
		));
		$cnt = 0;
		$group = obj(aw_ini_get("taket_group"));
arr($ol->count());
$persons = array();
		foreach($ol-> arr() as $o)
		{
			if(!$o->awobj_get_username())
			{
				$co = $o->company_id();
				if($co)
				{
					$company = obj($co);
					if($company->prop("code"))
					{
						$cuol = new object_list(array("class_id" => CL_USER , "name" => $company->prop("code")));
						if($cuol->count())
						{
							$copy_user = $cuol->begin();

							$username = $cl_user_creator->get_uid_for_person($o , false , true);
							if($username)
							{
								$props = $copy_user->properties();

								$user = new object();
								$user->set_class_id(CL_USER);
								$user->set_name($username);
								$user->set_parent($co);
								$user->set_prop("uid" , $username);
								$user->set_prop("password" , $props["password"]);
								$user->set_prop("home_folder" , $props["home_folder"]);
								$user->save();
								$user->add_to_group($group);
								$user->connect(array(
									"to" => $o->id(),
									"reltype" => "RELTYPE_PERSON"
								));
								$user->set_prop("password" , $props["password"]);
								$user->save();
						//	arr($copy_user->properties());
						//		arr($user->properties());
						//		arr($o->company_id());
						arr($username);

							}
else {$cnt++;arr($o->name());arr($cl_user_creator->get_uid_for_person($o , true));}

	//					arr($o->name());

						}
					}
				}


			}

*/

		die();

	}

	function get_org_by_code($code)
	{
		$ol = new object_list(array(
			"class_id" => CL_CRM_COMPANY,
			"code" => $code
		));
		$ids = $ol->ids();
		$id = reset($ids);
		return $id  ;

	}

	function add_co_if_no_ex($name, $parent)
	{
		$ol = new object_list(array(
			"name" => $name,
			"class_id" => CL_CRM_COMPANY,
		));
		if($ol->count())
		{
			return $ol->begin();
		}
			else
		{
			$o = new object();
			$o->set_parent($parent);
			$o->set_name($name);
			$o->set_class_id(CL_CRM_COMPANY);
			$o->save($name);
			return $o;
		}
	}

	function add_person_if_no_ex($code, $parent,$name)
	{
		$ol = new object_list(array(
			"external_id" => $code,
			"class_id" => CL_CRM_PERSON,
		));
		if($ol->count())
		{
			return $ol->begin();
		}
			else
		{
			$o = new object();
			$o->set_parent($parent);
			$o->set_name($name);
			$o->set_class_id(CL_CRM_PERSON);
			$o->set_prop("external_id" , $code);
			$o->save();
			return $o;
		}
	}

	function do_products_import($wh)
	{
		$this->print_line('L2hev toodete importi tegema');

		$dest_fld = aw_ini_get('site_basedir').'/files/warehouse_import/products.csv';
		
		$url = new aw_uri($wh["info"].'/index.php');
		$url->set_arg('create_products_file', 1);

		$this->print_line("Creating products file ... ", false);
//		$result = file_get_contents($url->get());

		$this->print_line('['.$result.']');
		$adr = new aw_uri($wh["info"]."/prods.csv");

	//		var_dump($result);die();
		$wget_command = 'wget -O '.$dest_fld.' "'.$adr->get().'"';
		$this->print_line("Download products file ... ", false);
		shell_exec($wget_command);
		$this->print_line('filesize '.filesize($dest_fld).']');
		$this->print_line("[done]");

		$file = file($dest_fld);
//		$max = array();
//		$strings = array();

		$r = mysql_query("SHOW TABLE STATUS LIKE 'products' ");
		$row = mysql_fetch_array($r);
		$Auto_increment = $row['Auto_increment'];
		mysql_free_result($r);


		foreach($file as $linenr => $row)
		{
			if($linenr % 10000 == 0) $this->print_line($linenr);
			if(!$linenr)//esimene rida
			{
				continue;
			}

	//		if($linenr > 15) die();
			$rowdata = explode("	" , $row);

			$code = $this->fuck($rowdata[0]);
			$rep_code = $this->fuck($rowdata[1]);
/*			if($code == $rep_code)
			{
				$rep_code = "";
			}*/
			$short_term = $this->short_code($rowdata[3]);
			$short_code = $this->short_code($rowdata[0]);	

			if(!array_key_exists($code , $this->product_codes)) //kui ei ole olemas toodet,siis lisab selle
			{
				$sql = "INSERT INTO products (code, name, rep_code, short_code, search_term, short_term, price , special_price , supplier_id, disc_code)
				VALUES ('".$code."', '".$this->fuck($rowdata[2])."', '".$rep_code."', '".$short_code."', '".$this->fuck($rowdata[3])."', '".$short_term."',    '".$this->fuck($rowdata[6])."', '".$this->fuck($rowdata[7])."', '".$this->fuck($rowdata[4])."' ,'".ord($rowdata[5])."')
				;";

				$this->product_codes[$code] = $Auto_increment;
				$Auto_increment++;
			}
			else
			{
				$sql = "UPDATE products 
					SET price='".$this->fuck_number($rowdata[6])."', special_price='".$this->fuck_number($rowdata[7])."', search_term='".$this->fuck($rowdata[3])."',
					short_term='".$short_term."'
					WHERE id='".$this->product_codes[$code]."';";
			}
			$res = $this->db->db_query($sql);
/* v'ljade pikkused		*/	
/*			foreach($rowdata as $key => $val)
			{


				if(empty($max[$key]) || $max[$key] < strlen($val))
				{
					$max[$key] = strlen($val);
					$strings[$key] = $val;

				}
			}*/
		}
//		arr($max);
//		arr($strings);

	}

	function do_amounts_import($wh)
	{
		$dest_fld = aw_ini_get('site_basedir').'/files/amounts_'.$wh["id"].'.csv';
		$url = new aw_uri($wh["info"].'/index.php');
		$url->set_arg('create_amounts_file', 1);
		$this->print_line("Creating amounts file ... ");
		$cont = $url->get();
		$this->print_line("url: ".$cont);

		$result = file_get_contents($url->get());
		$this->print_line('['.$result.']');
		$adr = new aw_uri($wh["info"]."/amounts.csv");
		$wget_command = 'wget -O '.$dest_fld.' "'.$adr->get().'"';
		shell_exec($wget_command);
		$this->print_line("Download amounts file ... ", false);
		$this->print_line('filesize '.filesize($dest_fld).']');
		$this->print_line("[done]");

//selle lao olemasolevad
		$amounts = array();
		$sql = "select id from amounts where warehouse=".$wh["id"];
		$this->db->db_query($sql);
		while ($row = $this->db->db_next()){
			$amounts[$row["id"]] = $row["id"] ;
		}
		$this->print_line('Amounts count '.sizeof($amounts));



		$file = file($dest_fld);
		$max = array();
		$strings = array();
		$errors = 0;
$count = 0;
		foreach($file as $linenr => $row)
		{
			$count++;
			if($linenr % 10000 == 0)$this->print_line($linenr);
			if(!$linenr)//esimene rida
			{
				continue;
			}

	//		if($linenr > 10) die();
			$rowdata = explode("	" , $row);
			$code = $this->fuck($rowdata[0]);
			$prodid = $this->product_codes[$code];
			if(!$prodid)
			{
//				$this->print_line('ERROR - product code:'.$code);
//				$this->print_line($row);
				$errors++;
				continue;
			}
			if(!array_key_exists($prodid , $amounts)) //kui ei ole olemas toodet,siis lisab selle
			{
				$sql = "INSERT INTO amounts (id, code, warehouse, amount)
				VALUES ('".$prodid."','".$code."', '".$wh["id"]."', '".$this->fuck($rowdata[1])."') 
				;";
			}
			else
			{
				$sql = "UPDATE amounts 
					SET amount='".$this->fuck_number($rowdata[1])."'
					WHERE id='".$prodid."' AND warehouse='".$wh["id"]."';";
			}
			$res = $this->db->db_query($sql);
		}
		$this->print_line("errors: ".$errors);
		$this->print_line("Amounts '".$wh["name"]."' done");
	}


	function do_discounts_import($wh)
	{
		$this->print_line("Discount rules import ..... get file" , false);
		$url = new aw_uri($wh["info"].'/index.php');
		$url->set_arg('get_discount_rules', 1);

		$result = file_get_contents($url->get());
		$this->print_line('[done]');
		$result = unserialize($result);

		$discounts = array();
		$sql = "select * from discount";
		$this->db->db_query($sql);
		while ($row = $this->db->db_next()){
			$discounts[$row["code"]] = $row ;
		}

		foreach($result as $res)
		{
			$x = 0;
			while($x < 15)
			{
				if(isset($res["KAT_ALE".$x]))
				{
					if(!isset($discounts[ord($res["KAT_KOODI"])]))
					{
						$sql = "INSERT INTO discount (code, customer, discount)
						VALUES ('".ord($res["KAT_KOODI"])."', '".$x."', '".$res["KAT_ALE".$x]."') 
						;";
					}
					else
					{
						$sql = "UPDATE discount
							SET discount='".$res["KAT_ALE".$x]."'
							WHERE code='".ord($res["KAT_KOODI"])."' AND customer='".$x."';";
					}
					arr($sql);
					$this->db->db_query($sql);
				}
				$x++;
			}
		}
		$this->print_line("Discount rules import finished");
	}

	function fuck($val)
	{
		$val = urldecode($val);
		$val = preg_replace('!\s+!', ' ', $val);
		$val = addslashes($val);
		$val = trim($val);
		return $val;
	}

	function fuck_number($val)
	{
		$val=trim($val);
		$val = urldecode($val);
		return (double)$val;
	}

	private function short_code($code)
	{
		$ret = $code;
		$ret = str_ireplace(array(" ","-","."), array("","",""), $ret);
		return $this->fuck($ret);	
	}

	private function warehouse_list()
	{
		$ret = array();
		$ol = new object_list(array(
			"class_id" => CL_SHOP_WAREHOUSE,
			"status" => 2,
		));
		foreach($ol->arr() as $o)
		{
			$asd = explode(":" , $o->comment());
			$psd = explode("/" , $asd[2]);
			$port = $psd[0];
			unset($psd[0]);
			$wh = array(
					"name" => $o->name(),	
					"host" => str_replace("/" , "" , $asd[1]),
					"port" => $port,
					"path" => "/".join("/", $psd).(sizeof($psd) ? "/" : "")."index.php",
					"ord" => $o->prop("ord"),
					"id" => $o->id(),
					"short" => $o->prop("short_name"),
					"status" => $o->prop("status"),
					"info" => $o->comment(),
				);
			$this->warehouses[$o->id] = $wh;
		}
		return $this->warehouses;
	}

	public function import_amounts($arr, $that)
	{
		$that->_update_status("amounts", warehouse_import_if::STATE_FETCHING, 0);

		$this->init_vars();

		// i need here:
		// product objects ids <-> product codes lookup table
		// so i can check if the product code has amount object with current warehouse or not ...
		// create the product_oids <-> product_codes lut
		$products_lut = array();
		$sql = "
			select
				aw_oid,
				code
			from 
				aw_shop_products
		";
		$this->db_obj->db_query($sql);
		while ($row = $this->db_obj->db_next()){
			$products_lut[$row['code']] = $row['aw_oid'];
		}

		$total = count($products_lut);
		$that->_update_status("amounts", warehouse_import_if::STATE_PROCESSING, 0, $total);

		foreach ($this->get_warehouses() as $oid)
		{
			$wh_obj = new object($oid);
			echo "Update amounts in warehouse ".$wh_obj->name().": <br />\n";
			flush();
			$this->update_amounts($oid, $products_lut, $that);
		}
	
		exit();
	}

	// update amounts data in a warehouse
	private function update_amounts($warehouse_oid, $products_lut, $that)
	{
		if (!$this->can('view', $warehouse_oid))
		{
			echo "Couldn't load the Warehouse object (".$warehouse_oid.") <br />\n";
			return false;
		}

		// I need existing amounts data from AW:
		$sql = "
			select
				aw_oid,
				amount,
				warehouse,
				product
			from
				aw_shop_warehouse_amount
			where
				warehouse = ".$warehouse_oid."
		";
		$existing_amounts_data = array();
		$this->db_obj->db_query($sql);
		while ($r = $this->db_obj->db_next())
		{
			$existing_amounts_data[$r['product']] = $r;
		}
		
		$wh_obj = new object($warehouse_oid);
		$amounts_data = file($wh_obj->comment()."/amounts.csv");
		$total = count($amounts_data);
		foreach ($amounts_data as $line)
		{
			$items = explode("\t", $line);
			$product_oid = $products_lut[trim($items[0])];
			if (empty($product_oid))
			{
				continue;
			}
			
			if (empty($existing_amounts_data[$product_oid]))
			{
				// we don't have the amount object for this product in current warehouse
				$this->add_amount_sql(array(
					'product_code' => $items[0],
					'product_oid' => $product_oid,
					'amount' => trim($items[1]),
					'warehouse_name' => $wh_obj->name(),
					'warehouse_oid' => $warehouse_oid
				));
				echo "INSERT: ".$wh_obj->name()." - ".$items[0]." - ".$product_oid." - ".$items[1]."<br />\n";
			}
			else
			{
			
				$this->update_amount_sql($existing_amounts_data[$product_oid]['aw_oid'], array(
					'product_code' => $items[0],
					'product_oid' => $product_oid,
					'amount' => trim($items[1]),
					'warehouse_name' => $wh_obj->name(),
					'warehouse_oid' => $warehouse_oid
				));
				echo "UPDATE: ".$wh_obj->name()." - ".$items[0]." - ".$product_oid." - ".$items[1]."<br />\n";
			
			}
			flush();

			if ((++$counter % 100) == 1)
			{
				$that->_update_status("amounts", warehouse_import_if::STATE_PROCESSING, $counter, $total, sprintf(t("Import laost %s"), $wh_obj->name()));
				if ($that->need_to_stop_now("amounts"))
				{
					$that->_end_import_from_flag("amounts");
					die("stopped for flag");
				}
			} 
		}
	}

	////
	// product_code
	// warehouse_name
	// warehouse_oid
	// amount
	// product_oid
	private function add_amount_sql($data)
	{
		$obj_base = $this->db_obj->db_fetch_array("select * from objects where class_id = '".CL_SHOP_WAREHOUSE_AMOUNT."' limit 1");
		$obj_base = reset($obj_base);
		$obj_base['oid'] = 0;
		$obj_base['createdby'] = '110';
		$obj_base['modifiedby'] = '110';
		$obj_base['parent'] = $data['parent'];
		// i should add prod code as well actually, to make the name more informative and therefore useful
		$name = sprintf(t("Toote %s laoseis %s laos"), $data['product_code'], $data['warehouse_name']);
		$obj_base['name'] = addslashes($name);

		$sql = "
			INSERT INTO 
				objects 
			VALUES (".implode(',', map('"%s"', $obj_base)).");
		";

		$this->db_obj->db_query($sql);

		$oid = $this->db_obj->db_last_insert_id();

		// brother_of value has to be the same as oid
		$this->db_obj->db_query("UPDATE objects set brother_of = ".$oid." WHERE oid = ".$oid);

		$sql = "
			INSERT INTO 
				aw_shop_warehouse_amount
			SET
				aw_oid = ".$oid.",
				warehouse = ".$data['warehouse_oid'].",
				amount = ".$data['amount'].",
				product = ".$data['product_oid']."
		";
		$this->db_obj->db_query($sql);
	
	}

	////
	// product_code
	// warehouse_name
	// warehouse_oid
	// amount
	// product_oid
	private function update_amount_sql($oid, $data)
	{
		$name = sprintf(t("Toote %s laoseis %s laos"), $data['product_code'], $data['warehouse_name']);
		$sql = "
			UPDATE 
				objects
			SET
				name = '".addslashes($name)."',
				brother_of = ".$oid."
			WHERE
				oid = ".$oid."
		";
		$this->db_obj->db_query($sql);

		$sql = "
			UPDATE
				aw_shop_warehouse_amount
			SET
				warehouse = ".$data['warehouse_oid'].",
				amount = ".$data['amount'].",
				product = ".$data['product_oid']."
			WHERE
				aw_oid = ".$oid."
		";
		$this->db_obj->db_query($sql);
	}

	private function delete_amount_sql($oid)
	{
		$sql = "DELETE FROM objects WHERE oid = ".$oid;
		$this->db_obj->db_query($sql);

		$sql = "DELETE FROM aw_shop_warehouse_amount WHERE aw_oid = ".$oid;
		$this->db_obj->db_query($sql);
	}

	public function import_prices($arr, $that)
	{
		$this->init_vars();

		$that->_update_status("prices", warehouse_import_if::STATE_FETCHING, 0);

		$prices_data = $this->get_prices_data();
		$products_data = $this->get_products_lut();

		$existing_prices_data = array();
		$sql = "select oid,parent from objects where class_id = ". CL_SHOP_ITEM_PRICE." and status > 0";
		$this->db_obj->db_query($sql);
		while ($row = $this->db_obj->db_next())
		{
			$existing_prices_data[$row['parent']] = $row['oid'];
		}
		$total = count($products_data);
		arr($total);

		$that->_update_status("prices", warehouse_import_if::STATE_PROCESSING, 0, $total);
		$counter = 0;
		foreach ($products_data as $code => $prod_oid)
		{
			if (!isset($prices_data[$code]))
			{
				echo "No price for product ".$code." (".$prod_oid.")<br />\n";
				continue;
			}
			$prices = $prices_data[$code];
			if (empty($existing_prices_data[$prod_oid]))
			{
				$this->add_price_sql(array(
					'product_oid' => $prod_oid,
					'product_code' => $code,
					'price' => $prices['price']
				));
				echo "Add price ".$prices['price']." to product ".$code." (".$prod_oid.")<br />\n";
			}
			else
			{
				$this->update_price_sql($existing_prices_data[$prod_oid], array(
					'product_oid' => $prod_oid,
					'product_code' => $code,
					'price' => $prices['price']
				));
				echo "Update price ".$prices['price']." to product ".$code." (".$prod_oid.")<br />\n";
			
			}

			if ((++$counter % 100) == 1)
			{
				$that->_update_status("prices", warehouse_import_if::STATE_PROCESSING, $counter, $total);
				if ($that->need_to_stop_now("prices"))
				{
					$that->_end_import_from_flag("prices");
					die("stopped for flag");
				}
			} 
		}
	}

	private function add_price_sql($data)
	{
		$obj_base = $this->db_obj->db_fetch_array("select * from objects where class_id = '".CL_SHOP_ITEM_PRICE."' limit 1");
		$obj_base = reset($obj_base);
		$obj_base['oid'] = 0;
		$obj_base['createdby'] = '110';
		$obj_base['modifiedby'] = '110';
		$obj_base['parent'] = $data['product_oid'];
		// i should add prod code as well actually, to make the name more informative and therefore useful
		$name = sprintf(t("%s hind"), $data['product_code']);
		$obj_base['name'] = addslashes($name);
		$obj_base['acldata'] = '';

		$sql = "
			INSERT INTO 
				objects 
			VALUES (".implode(',', map('"%s"', $obj_base)).");
		";

		$this->db_obj->db_query($sql);

		$oid = $this->db_obj->db_last_insert_id();

		// brother_of value has to be the same as oid
		$this->db_obj->db_query("UPDATE objects set brother_of = ".$oid." WHERE oid = ".$oid);

		$sql = "
			INSERT INTO 
				aw_shop_item_prices
			SET
				aw_oid = ".$oid.",
				price = ".(int)$data['price'].",
				product = ".$data['product_oid']."
		";
		$this->db_obj->db_query($sql);
	
	}

	////
	// product_code
	// warehouse_name
	// warehouse_oid
	// amount
	// product_oid
	private function update_price_sql($oid, $data)
	{
		$name = sprintf(t("%s hind"), $data['product_code']);
		$obj_base['name'] = addslashes($name);

		$sql = "
			UPDATE 
				objects
			SET
				name = '".addslashes($name)."',
				brother_of = ".$oid."
			WHERE
				oid = ".$oid."
		";
		$this->db_obj->db_query($sql);

		$sql = "
			UPDATE
				aw_shop_item_prices
			SET
				price = ".(int)$data['price'].",
				product = ".$data['product_oid']."
			WHERE
				aw_oid = ".$oid."
		";
		$this->db_obj->db_query($sql);
	}

	private function delete_price_sql($oid)
	{
		$sql = "DELETE FROM objects WHERE oid = ".$oid;
		$this->db_obj->db_query($sql);

		$sql = "DELETE FROM aw_shop_item_prices WHERE aw_oid = ".$oid;
		$this->db_obj->db_query($sql);
	}

	public function get_prices_data()
	{
		$data_file = $this->get_products_file();
		$lines = file($data_file);
		
		unset($lines[0]);

		$result = array();
		foreach ($lines as $line)
		{
			$fields = explode("\t", $line);
			$result[trim($fields[0])] = array(
				'price' => $fields[6],
				'special_price' => $fields[7]
			);
		}
		return $result;
	}

	public function get_products_lut()
	{
		$products_lut = array();
		$sql = "
			select
				aw_oid,
				code
			from 
				aw_shop_products
		";
		$this->db_obj->db_query($sql);
		while ($row = $this->db_obj->db_next()){
			$products_lut[$row['code']] = $row['aw_oid'];
		}
		return $products_lut;
	}

	public function get_products_file()
	{
		return aw_ini_get('site_basedir').'/files/products.csv';
	}

	private function apply_controller($code)
	{
		if($this->controller_inst)
		{
			return $this->controller_inst->check_property($this->controller_id, null, $code, null, null, null);
		}
		return false;
	}

	// query warehouses
	public function get_warehouses()
	{
		if (!empty($this->warehouses))
		{
			return $this->warehouses;
		}

		$conns = $this->connections_from(array(
			'type' => 'RELTYPE_WAREHOUSE',
			'sort_by_num' => 'to.jrk',
			'sort_dir' => 'asc'
		));

		foreach ($conns as $conn)
		{
			$this->warehouses[$conn->prop('to')] = $conn->prop('to');
		}
		return $this->warehouses;
	}

	// this is for personnel import and it should return php array with data
	// and the personnel import can make persons and stuff out of this data (hopefully)
	public function get_users_data($arr)
	{
		$whs = $this->get_warehouses();	
		$wh = new object(reset($whs));
		$adr = $wh->comment();
		$data = file_get_contents($adr.'/index.php?get_users_data=1');

		arr($data);

		$r = array();

		$lines = explode("\n", $data);
		$keys = explode("\t", $lines[0]);
		unset($lines[0]);
		foreach ($lines as $id => $line)
		{

			$items = array_combine($keys, explode("\t", $line));
			$r['PERSONS'][$items['CLIENT_NR']]['USER'] = $items['CLIENT_NR'];
			$r['PERSONS'][$items['CLIENT_NR']]['PID'] = $items['CLIENT_NR'];
			$r['PERSONS'][$items['CLIENT_NR']]['FNAME'] = $items['FIRST_NAME'];
			$r['PERSONS'][$items['CLIENT_NR']]['LNAME'] = $items['LAST_NAME'];
			$r['PERSONS'][$items['CLIENT_NR']]['EMAILS'][0] = $items['EMAIL'];
			$r['PERSONS'][$items['CLIENT_NR']]['PHONES']['MOBILE'][0] = $items['GSM'];
			// company name ? $items['COMPANY_NAME'];
			$r['ORGANIZATIONS'][$items['CLIENT_NR']]['NAME'] = $items['COMPANY_NAME'];
		}
		return $r;

		
	}

	function parallel_url_fetch($d)
	{
		$mh = curl_multi_init();

		$ch = array();
		foreach($d as $nr => $url)
		{
			$ch[$nr] = curl_init();
			curl_setopt($ch[$nr], CURLOPT_URL, $url);
			curl_setopt($ch[$nr], CURLOPT_HEADER, 0);
			curl_setopt($ch[$nr], CURLOPT_RETURNTRANSFER, true);
			curl_multi_add_handle($mh, $ch[$nr]);
		}

		$running = null;
		//execute the handles
		do 
		{
		    curl_multi_exec( $mh, $running );
		} 
		while ( $running > 0 );

		$rv = array();
		foreach($d as $nr => $url)
		{
			$rv[$nr] = curl_multi_getcontent($ch[$nr]);
			curl_multi_remove_handle($mh, $ch[$nr]);
		}

		curl_multi_close($mh);

		return $rv;
	}

	private function microtime_float()
	{
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}

	private function print_line($str, $break = true)
	{
		echo $str;
		if ($break === true)
		{
			echo "<br />\n";
		}
		flush();
	}
}

?>
