<?php

namespace automatweb;


class warehouse_import_obj extends _int_object
{
	const AW_CLID = 1535;


	private $controller_inst;
	private $controller_id;
	private $prod_fld;
	private $db_obj;

	private function init_vars()
	{
		// ERROR REPORTING
	//	automatweb::$instance->mode(automatweb::MODE_DBG);

		// controller for short product codes ...
		if($this->controller_id = $this->prop("code_ctrl"))
		{
			$this->controller_inst = get_instance(CL_CFGCONTROLLER);
		}
	//	$this->prod_fld = $this->get_products_folder;
		$this->db_obj = $GLOBALS["object_loader"]->cache;
	}

	private function apply_controller($code)
	{
		// XXX need to fix this later ! --dragut
		// it should use the short code controller, which should be gotten from warehouse config!
		return str_ireplace(array(" ","-"), array("",""), $code);

		if($this->controller_inst)
		{
			return $this->controller_inst->check_property($this->controller_id, null, $code, null, null, null);
		}
		return false;
	}

	private function get_products_folder()
	{
		// I actually doubt that it is necessary to have products folder here
		// It belongs to this soon-to-come products import to warehouses class
		// where i can configure how and where the prods will be imported
		$wh = $this->prop("warehouse");

		if($this->can("view", $wh))
		{
			$who = obj($wh);
			$cid = $who->prop("conf");
			$this->warehouse = $wh;
		}
		if($this->can("view", $cid))
		{
			$co = obj($cid);
			$prod_fld = $co->prop("prod_fld");
		}

		if(!$prod_fld)
		{
			die(t("Lao toodete kataloog on m&auml;&auml;ramata"));
		}
		elseif(!$this->can("add", $prod_fld))
		{
			die(t("Lao toodete kataloogi alla ei ole &otilde;igusi lisamiseks"));
		}
		return $prod_fld;
	}

	public function list_aw_warehouses()
	{
		$wh_conns = $this->connections_from(array(
			'type' => 'RELTYPE_WAREHOUSE',
			'sort_by_num' => 'to.jrk',
			'sort_dir' => 'asc'
		));

		$matrix = $this->meta("connection_matrix");

		$result = array();
		foreach ($wh_conns as $conn)
		{
			$result[$conn->prop('to')] = array(
				"name" => $conn->prop('to.name'),
				"imp_products" => $matrix[$conn->prop("to")]["products"],
				"imp_amounts" => $matrix[$conn->prop("to")]["amounts"],
				"imp_prices" => $matrix[$conn->prop("to")]["prices"],
				"imp_price_list" => $matrix[$conn->prop("to")]["price_list"],
			);
		}
		return $result;
	}

	// data is: [aw wh id][products|amounts|prices|price_list] = ext id
	public function set_import_matrix($data)
	{
		$this->set_meta("connection_matrix", $data);
	}

	public function list_external_warehouses($only_act = false)
	{
		if (!$this->prop("data_source"))
		{
			return array();
		}
		$inst = get_instance($this->prop("data_source"));
		$data = $inst->get_warehouse_list();	// name, data
		$used = $this->meta("used_external");
		foreach($data as $id => $d)
		{
			$data[$id]["used"] = $used[$id];
		}

		if ($only_act)
		{
			foreach($data as $id => $d)
			{
				if (!$d["used"])
				{
					unset($data[$id]);
				}
			}
		}
		return $data;
	}

	public function set_used_external_warehouses($list)
	{
		$this->set_meta("used_external", $list);
	}

	// maybe this one should be price_list functionality at the first place?
	// same goes with saving the price list data ....
	public function get_price_list_matrix($price_list_oid)
	{
		$data = array();
		$ol = new object_list(array(
			"class_id" => CL_SHOP_PRICE_LIST_CUSTOMER_DISCOUNT,
			"pricelist" => $price_list_oid
		));
		foreach($ol->arr() as $o)
		{
			$data[$o->prop("prod_category")][$o->prop("crm_category")] = $o->id();
		}
		return $data;
	}

	// maybe this one should be price list functionality as well ?
	public function get_product_categories()
	{
		$ol = new object_list(array(
			'class_id' => CL_SHOP_PRODUCT_CATEGORY
		));

		$result = array();
		foreach ($ol->arr() as $id => $o)
		{
			$result[$id] = $o->name();
		}
		return $result;
	}

	// and this one should be price_list functionality?
	public function get_client_categories()
	{
		$ol = new object_list(array(
			'class_id' => CL_CRM_CATEGORY
		));

		$result = array();
		foreach ($ol->arr() as $id => $o)
		{
			$result[$id] = $o->name();
		}
		return $result;
	}

	// I need to add here, that one can configure pricelist in import object as well ...
	public function get_price_list()
	{
		// I'm not really sure about this here - I think it is logical, if the price_list is asked from warehouse - cause what is the other purpose for the price list anyway ? --dragut
		$price_list_id = $this->prop('price_list');
		if ($this->can('view', $price_list_id))
		{
			return new object($price_list_id);
		}

		$whs = $this->list_aw_warehouses();
		$first_wh_id = key($whs);

		if ($this->can('view', $first_wh_id))
		{
			$wh_obj = new object($first_wh_id);
			$wh_conf_id = $wh_obj->prop('conf');
		}

		if ($this->can('view', $wh_conf_id))
		{
			$wh_conf = new object($wh_conf_id);
			$price_list_id = $wh_conf->prop('def_price_list');
		}

		if ($this->can('view', $price_list_id))
		{
			return new object($price_list_id);
		}
	}

	private function _get_xml_fn($type)
	{
		$fn = aw_ini_get("site_basedir")."/files/warehouse_import";
		if (!is_dir($fn))
		{
			mkdir($fn);
			chmod($fn, 0777);
		}

		list($tm) = $this->full_import_status($type);

		$fn .= "/xml_".$type."_".$tm.".xml";
		return $fn;
	}

	private function _write_xml_file($type, $xml)
	{
		$fn = $this->_get_xml_fn($type);
		$f = fopen($fn, "w");
		fwrite($f, $xml);
		fclose($f);
	}

	public function update_price_list()
	{
		$this->_start_import("pricelists");
		$this->_update_status("pricelists", warehouse_import_if::STATE_FETCHING, null, 0);

		$ds = get_instance($this->prop('data_source'));

		// get the pricelist data as XML
		$xml_data = $ds->get_pricelist_xml();
		$this->_write_xml_file("pricelists", $xml_data);

		$xml = new SimpleXMLElement($xml_data);

		$price_list_obj = $this->get_price_list();

		$product_categories = $this->get_product_categories();
		$client_categories = $this->get_client_categories();

		$price_list_matrix = $this->get_price_list_matrix($price_list_obj->id());

		$total = count($xml->product_category);
		$this->_update_status("pricelists", warehouse_import_if::STATE_PROCESSING, null, 0, $total);

		$counter = 0;
		foreach ($xml->product_category as $cat)
		{
			// need to create product_category
			$prod_cat_oid = array_search($cat->name, $product_categories);
			if ($prod_cat_oid === false)
			{
				$prod_cat = new object();
				$prod_cat->set_class_id(CL_SHOP_PRODUCT_CATEGORY);
				$prod_cat->set_parent($price_list_obj->id());
				$prod_cat->set_name($cat->name);
				$prod_cat_oid = $prod_cat->save();

				$product_categories[$prod_cat_oid] = (string)$cat->name;

				echo "Add new product category ".$cat->name."<br />\n";
			}

			if ( !$price_list_obj->is_connected_to(array('to' => $prod_cat_oid)) )
			{
				$price_list_obj->connect(array(
					"type" => "RELTYPE_MATRIX_CATEGORY",
					"to" => $prod_cat_oid,
				));
			}

			foreach ($cat->client_category as $client)
			{
				$client_cat_oid = array_search($client->name, $client_categories);
				if ($client_cat_oid === false)
				{
					$client_cat = new object();
					$client_cat->set_name($client->name);
					$client_cat->set_class_id(CL_CRM_CATEGORY);
					$client_cat->set_parent($price_list_obj->id());
					$client_cat_oid = $client_cat->save();

					$client_categories[$client_cat_oid] = (string)$client->name;

					echo "Add new client category ".$client->name."<br />\n";
				}

				if ( !$price_list_obj->is_connected_to(array('to' => $client_cat_oid)) )
				{
					$price_list_obj->connect(array(
						"type" => "RELTYPE_MATRIX_ORG_CAT",
						"to" => $client_cat_oid,
					));
				}

				$discount = (string)$client->value;

				// checking CL_SHOP_PRICELIST_CUSTOMER_DISCOUNT
				if($oid = $price_list_matrix[$prod_cat_oid][$client_cat_oid])
				{
					$cust_disc_o = obj($oid);
					$cust_disc_o->set_prop("discount", $discount);
					$cust_disc_o->save();
				}
				else
				{
					$cust_disc_o = obj();
					$cust_disc_o->set_class_id(CL_SHOP_PRICE_LIST_CUSTOMER_DISCOUNT);
					$cust_disc_o->set_name(sprintf(t("%s kliendigrupi allahindlus"), $price_list_obj->name()));
					$cust_disc_o->set_parent($price_list_obj->id());
					$cust_disc_o->set_prop("pricelist", $price_list_obj->id());
					$cust_disc_o->set_prop("crm_category", $client_cat_oid);
					$cust_disc_o->set_prop("prod_category", $prod_cat_oid);
					$cust_disc_o->set_prop("discount", $discount);
					$cust_disc_o->save();
				}
			}

			$this->_update_status("pricelists", warehouse_import_if::STATE_PROCESSING, null, ++$counter, $total);
			if ($this->need_to_stop_now("pricelists"))
			{
				$this->_end_import_from_flag("pricelists");
				die("stopped for flag");
			}
		}

		$this->_end_import("pricelists");
	}

	public function clear_price_list()
	{
		$price_list = $this->get_price_list();
		$matrix = $this->get_price_list_matrix($price_list->id());
		$to_delete = array();

		foreach ($matrix as $prod_cat_oid => $clients)
		{
			$to_delete[$prod_cat_oid] = $prod_cat_oid;
			foreach ($clients as $client_cat_oid => $customer_discount_oid)
			{
				$to_delete[$client_cat_oid] = $client_cat_oid;
				$to_delete[$customer_discount_oid] = $customer_discount_oid;
			}
		}

		foreach($to_delete as $oid)
		{
			try {
			$o = new object($oid);
			$o->delete(true);
			} catch (\Exception $e) {}
		}

	}

	public function start_prod_import($callback)
	{
		// check if import is not running already
		if ($this->_prod_import_is_running())
		{
			return;
		}

		$prod_imp = new warehouse_products_import();
	//	$prod_imp->set_wh_import(obj($this->id()));
	//	$prod_imp->set_callback($callback);
		$prod_imp->bg_control(array('id' => $this->id(), 'do' => 'start'));
		exit();

		// let datasource fetch data, give it an orb action to report back to
		$inst = get_instance($this->prop("data_source"));
		$inst->fetch_product_xml(
			obj($this->id()),
			$callback,
			1
		);
		// finish
		die("done");
	}

	function _prod_import_is_running()
	{
		return false;
	}


////////////////////////////////////////////////// process management support

	function getpidinfo($pid, $ps_opt="aux")
	{
		$ps=shell_exec("ps ".$ps_opt."p ".$pid);
		$ps=explode("\n", $ps);

		if(count($ps) < 2)
		{
			return false;
		}

		foreach($ps as $key=>$val)
		{
			$ps[$key]=explode(" ", ereg_replace(" +", " ", trim($ps[$key])));
		}

		foreach($ps[0] as $key=>$val)
		{
			$pidinfo[$val] = $ps[1][$key];
			unset($ps[1][$key]);
		}

		if(is_array($ps[1]))
		{
			$pidinfo[$val].=" ".implode(" ", $ps[1]);
		}

		if ($pidinfo["PID"] == null)
		{
			return false;
		}
		return $pidinfo;
	}


	static private function _status_fn($type, $wh_id = "")
	{
		return aw_ini_get("server.tmpdir")."/aw_wh_imp_".aw_ini_get("site_id")."_".$type."_".$wh_id;
	}

	function import_is_running($type, $wh_id = null)
	{
		$tf = self::_status_fn($type, $wh_id);
		if (file_exists($tf))
		{
			list($start_time, $pid, $state) = explode("\n", file_get_contents($tf));
			if (($pd = $this->getpidinfo($pid)) === false)
			{
				$this->write_import_end_log_entry($type, t("Staatuse kontrollis avastati protsessi kadumine"), false, $wh_id);
				unlink($tf);
				return false;
			}
			return $pid;
		}
		return false;
	}

	function get_import_log($type, $wh_id = "")
	{
		return $this->meta("import_log_".$type."_".$wh_id);
	}

	function import_status($type, $wh_id = "")
	{
		$tf = self::_status_fn($type, $wh_id);
		if (file_exists($tf))
		{
			list($pid, $state, $count) = explode("\n", file_get_contents($tf));
			return $state;
		}
		return "Viga";
	}

	function full_import_status($type, $wh_id = "")
	{
		$tf = self::_status_fn($type, $wh_id);
		if (file_exists($tf))
		{
			return explode("\n", file_get_contents($tf), 7);
		}
		return "Viga";
	}

	function import_count($type, $wh_id = "")
	{
		$tf = self::_status_fn($type, $wh_id);
		if (file_exists($tf))
		{
			list($pid, $state, $count) = explode("\n", file_get_contents($tf));
			return $count;
		}
		return "Viga";
	}

	function _int_stop($type, $wh_id = "")
	{
		$this->_update_status($type, warehouse_import_if::STATE_FINISHING, $wh_id);

		$sf = self::_status_fn($type, $wh_id);
		unlink($sf);
		if (file_exists($sf."_stop_flag"))
		{
			unlink($sf."_stop_flag");
		}
	}

	function _end_import_from_flag($type, $wh_id = "")
	{
		$this->write_import_end_log_entry($type, t("Kasutaja n&otilde;udis protsessi peatamist manuaalselt"), false, $wh_id);
		$this->_int_stop($type);
	}

	function _end_import($type, $wh_id = "")
	{
		$this->write_import_end_log_entry($type, t("L&otilde;ppes edukalt"), true, $wh_id);
		$this->_int_stop($type, $wh_id);
	}

	function reset_import($type, $wh_id = "")
	{
		$this->write_import_end_log_entry($type, t("Kasutaja resettis protsessi manuaalselt"), false, $wh_id);
		$sf = self::_status_fn($type, $wh_id);
		if (file_exists($sf))
		{
			unlink($sf);
		}
		if (file_exists($sf."_stop_flag"))
		{
			unlink($sf."_stop_flag");
		}
	}

	function stop_import($type, $wh_id = "")
	{
		if ($this->import_is_running($type, $wh_id))
		{
			$tf = self::_status_fn($type, $wh_id)."_stop_flag";
			touch($tf);
		}
	}

	function need_to_stop_now($type, $wh_id = "")
	{
		if ($this->import_is_running($type, $wh_id))
		{
			$tf = self::_status_fn($type, $wh_id)."_stop_flag";
			if (file_exists($tf))
			{
				//unlink($tf);
				return true;
			}
		}
		return false;
	}

	function _start_import($type, $wh_id = "")
	{
		$this->_update_status($type, warehouse_import_if::STATE_PREPARING, $wh_id);
	}

	function _update_status($type, $status, $wh_id = null, $count = null, $total = null, $info = null)
	{
		$tf = self::_status_fn($type, $wh_id);
		if (!file_exists($tf))
		{
			$start_time = time();
		}
		else
		{
			list($start_time, $t1, $t2, $t3, $t4, $t5, $t6) = explode("\n", file_get_contents($tf), 7);
			if ($count === null)
			{
				$count = $t4;
			}
			if ($total === null)
			{
				$total = $t5;
			}
			if ($info === null)
			{
				$info = $t6;
			}
		}
		$f = fopen($tf, "w");
		fwrite($f, $start_time."\n".getmypid()."\n".$status."\n".$wh_id."\n".$count."\n".$total."\n".$info);
		fclose($f);
	}


	function write_import_end_log_entry($type, $reason, $success = true, $wh_id = null)
	{
		// need to reload meta from database
		parent::__construct($GLOBALS["object_loader"]->ds->get_objdata($this->id()));

		$typedata = $this->meta("import_log_".$type."_".$wh_id);
		if (!is_array($typedata))
		{
			$typedata = array();
		}
		if (count($typedata) > 9)
		{
			// cut off from the end
			array_pop($typedata);
		}

		$s = $this->full_import_status($type, $wh_id);

		array_unshift($typedata, array(
			"finish_tm" => time(),
			"full_status" => $s,
			"reason" => $reason,
			"success" => $success
		));
		$this->set_meta("import_log_".$type."_".$wh_id, $typedata);
		$this->save();
	}


	function warehouse_mapper($type, $aw_oid)
	{
		$m = $this->meta("connection_matrix");
		return $m[$aw_oid][$type];
	}

////////////////////////////////////// real imports


	function start_prices_import()
	{
		while (ob_get_level()) { ob_end_clean(); }

		$this->_start_import("prices");

		// status fetch xml
		$this->_update_status("prices", warehouse_import_if::STATE_FETCHING, null);

		$i = get_instance($this->prop("data_source"));
		$xml = $i->get_prices_xml();

		$this->_write_xml_file("prices", $xml);

		$sx = new SimpleXMLElement($xml);
		$total = count($sx->product);

		$this->_update_status("prices", warehouse_import_if::STATE_PROCESSING, null, 0, $total);

		// process
		$this->_do_prices_import_process($sx);

		// finish
		$this->_end_import("prices");
	}

	private function _do_prices_import_process($sx)
	{
		$this->db_obj = new file();

		$prices_data = array();
		foreach($sx->product as $pdata)
		{
			$prices_data[(string)$pdata->product_code] = array(
				"price" => (string)$pdata->price,
				"special_price" => (string)$pdata->special_price
			);
		}

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

		$this->_update_status("prices", warehouse_import_if::STATE_PROCESSING, null, 0, $total);
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
					'price' => $prices['price'],
					'special_price' => $prices['special_price'],
				));
				echo "Add price ".$prices['price']." to product ".$code." (".$prod_oid.")<br />\n";
			}
			else
			{
				$this->update_price_sql($existing_prices_data[$prod_oid], array(
					'product_oid' => $prod_oid,
					'product_code' => $code,
					'price' => $prices['price'],
					'special_price' => $prices['special_price'],
				));
				echo "Update price ".$prices['price']." to product ".$code." (".$prod_oid.")<br />\n";

			}

			if ((++$counter % 100) == 1)
			{
				$this->_update_status("prices", warehouse_import_if::STATE_PROCESSING, null, $counter, $total);
				if ($this->need_to_stop_now("prices"))
				{
					$this->_end_import_from_flag("prices");
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

		$sql = "UPDATE aw_shop_products SET aw_special_price = '".$data["special_price"]."' WHERE aw_oid = ".$data["product_oid"];
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

		$sql = "UPDATE aw_shop_products SET aw_special_price = '".$data["special_price"]."' WHERE aw_oid = ".$data["product_oid"];
		$this->db_obj->db_query($sql);
	}

	private function delete_price_sql($oid)
	{
		$sql = "DELETE FROM objects WHERE oid = ".$oid;
		$this->db_obj->db_query($sql);

		$sql = "DELETE FROM aw_shop_item_prices WHERE aw_oid = ".$oid;
		$this->db_obj->db_query($sql);
	}

	private function get_products_lut()
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

	function start_amounts_import($wh_id)
	{
		while (ob_get_level()) { ob_end_clean(); }
		$this->_start_import("amounts", $wh_id);
		// status fetch xml
		$this->_update_status("amounts", warehouse_import_if::STATE_FETCHING, $wh_id);
		$i = get_instance($this->prop("data_source"));
		$xml = $i->get_amounts_xml($this->warehouse_mapper("amounts", $wh_id));

		$this->_write_xml_file("amounts", $xml);

		$sx = new SimpleXMLElement($xml);
		$total = count($sx->product);
		$this->_update_status("amounts", warehouse_import_if::STATE_PROCESSING, $wh_id, 0, $total);
		// process

		$this->_do_amounts_import_process($wh_id, $sx);

		// finish
		$this->_end_import("amounts", $wh_id);
	}

	private function _do_amounts_import_process($wh_id, $sx)
	{
		$this->db_obj = new file();

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
		while ($row = $this->db_obj->db_next())
		{
			$products_lut[$row['code']] = $row['aw_oid'];
		}

echo "fetch 1 <br>\n";
flush();
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
				warehouse = ".$wh_id."
		";
		$existing_amounts_data = array();
		$this->db_obj->db_query($sql);
		while ($r = $this->db_obj->db_next())
		{
			$existing_amounts_data[$r['product']] = $r;
		}

		$wh_obj = obj($wh_id);
		$total = count($sx->product);
		foreach($sx->product as $pdata)
		{
			$product_oid = $products_lut[(string)$pdata->product_code];
			if (empty($product_oid))
			{
				continue;
			}


			if (empty($existing_amounts_data[$product_oid]))
			{
				// we don't have the amount object for this product in current warehouse
				$this->add_amount_sql(array(
					'product_code' => (string)$pdata->product_code,
					'product_oid' => $product_oid,
					'amount' => (string)$pdata->amount,
					'warehouse_name' => $wh_obj->name(),
					'warehouse_oid' => $wh_id
				));
				echo "INSERT: ".$wh_obj->name()." - ".((string)$pdata->product_code)." - ".$product_oid." - ".((string)$pdata->amount)."<br />\n";
			}
			else
			{
				$this->update_amount_sql($existing_amounts_data[$product_oid]['aw_oid'], array(
					'product_code' => (string)$pdata->product_code,
					'product_oid' => $product_oid,
					'amount' => (string)$pdata->amount,
					'warehouse_name' => $wh_obj->name(),
					'warehouse_oid' => $wh_id
				));
				echo "UPDATE: ".$wh_obj->name()." - ".((string)$pdata->product_code)." - ".$product_oid." - ".((string)$pdata->amount)."<br />\n";
			}
			flush();

			if ((++$counter % 100) == 1)
			{
				$this->_update_status("amounts", warehouse_import_if::STATE_PROCESSING, $wh_id, $counter, $total);
				if ($this->need_to_stop_now("amounts", $wh_id))
				{
					$this->_end_import_from_flag("amounts", $wh_id);
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


	public function process_product_xml($xml_filename)
	{
	//	while (ob_get_level()) { ob_end_clean(); }

		$wh_id = 1; // this 1 should be replaced with warehouse_id !

		$this->_start_import("products", $wh_id); 

		$path = aw_ini_get('server.tmpdir').'/warehouse_import_prods';

		if (!is_dir($path))
		{
			mkdir($path);
			chmod($path, 0777);
		}

		$this->_update_status("products", warehouse_import_if::STATE_FETCHING, $wh_id);

		// parse prods from xml
		$sx = simplexml_load_file($xml_filename);

		$this->_update_status("products", warehouse_import_if::STATE_PROCESSING, $wh_id, 0, $total); 

		$total = 'n/a';
		$counter = 1;
		$slice = array();
		foreach($sx->product as $prod)
		{
			$tmp_arr[] = $prod->asXML();
			$product_codes[] = $prod->product_code;
			if (($counter % 100) == 0)
			{
				$file = $path.'/prods_chunk_'.$counter;
				file_put_contents($file, serialize($tmp_arr));
				$tmp_arr = array();
				$h = new http;
				$url = aw_ini_get('baseurl').'/?class=warehouse_import&action=run_backgrounded&act=process_product_chunk&file='.$file.'&id='.$this->id().'&wh_id=1';
				echo $h->get($url);

				$this->_update_status("products", warehouse_import_if::STATE_PROCESSING, $wh_id, $counter, $total);
				if ($this->need_to_stop_now("products", $wh_id))
				{
					$this->_end_import_from_flag("products", $wh_id);
					die("stopped for flag");
				}

				sleep(1);
			}

			$counter++;
		}

		// finish
		$this->_end_import("products", $wh_id);

		// import new
		// update existing
		// delete old
	}

	public function process_prods_chunk($file)
	{
		list($usec, $sec) = explode(' ', microtime());
		$start = ((float)$usec + (float)$sec);

	//	file_put_contents('/tmp/warehouse_import_prods/status.txt', "process_prod_chunk\n", FILE_APPEND);
		$this->init_vars();
		$data = unserialize(file_get_contents($file));
		$products = array();
		foreach (safe_array($data) as $k => $v)
		{
			$sxmlo = simplexml_load_string($v);
			// i have to get the product codes and ask the aw objects for those codes
			// then i need to update the objects or add the objects if they don't exist
			// i should keep track what i have imported/updated, so i can delete those which are obsolote at the end
			$products[(string)$sxmlo->product_code] = array(
				'product_name' => (string)$sxmlo->product_name,
				'product_code' => (string)$sxmlo->product_code,
				'info' => (string)$sxmlo->info,
				'search_term' => (string)$sxmlo->search_term,
				'replacement_product_code' => (string)$sxmlo->replacement_product_code,
				'short_code' => (string)$sxmlo->short_code
			);
		}

		$existing_prods = $this->_list_aw_prods(array(
			'warehouse' => new object(119),
			'product_codes' => array_keys($products)
		));

		$updated_prods = array();
		foreach ($existing_prods as $oid => $prod_data)
		{
			$code = $prod_data['code'];

			// the idea is, that if I have already added or updated a product with this code
			// then now it is a duplicate, so I need to delete it:
			if (!empty($updated_prods[$code]))
			{
				$this->delete_product_sql($oid);
				arr('Delete product '.$code);
				continue;
			}

		//	arr($products[$code]);
			if (empty($products[$code]))
			{
				// there isn't a product with such product code, so lets create it:
				$this->add_product_sql($products[$code]);
				arr('add product '.$code);
			}
			else
			{
				// the product exists, so lets update it
				$this->update_product_sql($oid, $products[$code]);
				arr('update product '.$code);
			}
			$updated_prods[$code] = $code;
		}
	//	exit('end for now');
	//	file_put_contents($file, '');
		unlink($file);
		list($usec, $sec) = explode(' ', microtime());
		$end = ((float)$usec + (float)$sec);
		$time = $end - $start;
		file_put_contents('/tmp/warehouse_import_prods/log.txt', $file." [ ".$time." ] [done]\n", FILE_APPEND);
	
	}

	private function _list_aw_prods($arr)
	{
		$warehouse = $arr['warehouse'];
		$product_codes = $arr['product_codes'];

		// get all folders from warehouse so we can filter prods only in that house
		list($fld, $ot) = $warehouse->instance()->get_packet_folder_list(array("id" => $warehouse->id()));

		$ids = array($fld->id() => $fld->id());
		foreach($ot->ids() as $id)
		{
			$ids[$id] = $id;
		}

		$rv = array();
		$odl = new object_data_list(
			array(
				"class_id" => CL_SHOP_PRODUCT,
				"parent" => $ids,
				'code' => $product_codes
			),
			array(
				CL_SHOP_PRODUCT => array(
					new obj_sql_func(OBJ_SQL_UNIQUE, "oid", "oid"),
					"name" => "name",
					"comment" => "comment",
					"status" => "status",
					"short_name" => "short_name",
					"brand" => "brand",
					"price" => "price",
					"purchase_price" => "purchase_price",
					"must_order_num" => "must_order_num",
					"code" => "code",
					"short_code" => "short_code",
					"barcode" => "barcode",
					"min_order_amt" => "min_order_amt",
					"max_order_amt" => "max_order_amt",
					"tax_rate" => "tax_rate",
					"item_type" => "item_type",
					"item_count" => "item_count",
					"match_prod" => "match_prod",
					"type_code" => "type_code",
					"color" => "color",
					"height" => "height",
					"width" => "width",
					"depth" => "depth",
					"wideness" => "wideness",
					"density" => "density",
					"weight" => "weight",
					"gramweight" => "gramweight",
					"raster" => "raster",
					"bulk" => "bulk",
					"guarantee" => "guarantee",
					"userch1" => "userch1",
					"userch2" => "userch2",
					"userch3" => "userch3",
					"userch4" => "userch4",
					"userch5" => "userch5",
					"userch6" => "userch6",
					"userch7" => "userch7",
					"userch8" => "userch8",
					"userch9" => "userch9",
					"userch10" => "userch10",
					"user1" => "user1",
					"user2" => "user2",
					"user3" => "user3",
					"user4" => "user4",
					"user5" => "user5",
					"user6" => "user6",
					"user7" => "user7",
					"user8" => "user8",
					"user9" => "user9",
					"user10" => "user10",
					"user11" => "user11",
					"user12" => "user12",
					"user13" => "user13",
					"user14" => "user14",
					"user15" => "user15",
					"user16" => "user16",
					"user17" => "user17",
					"user18" => "user18",
					"user19" => "user19",
					"user20" => "user20",
					"userta1" => "userta1",
					"userta2" => "userta2",
					"userta3" => "userta3",
					"userta4" => "userta4",
					"userta5" => "userta5",
					"userta6" => "userta6",
					"userta7" => "userta7",
					"userta8" => "userta8",
					"userta9" => "userta9",
					"userta10" => "userta10",
					"uservar1" => "uservar1",
					"uservar2" => "uservar2",
					"uservar3" => "uservar3",
					"uservar4" => "uservar4",
					"uservar5" => "uservar5",
					"uservar6" => "uservar6",
					"uservar7" => "uservar7",
					"uservar8" => "uservar8",
					"uservar9" => "uservar9",
					"uservar10" => "uservar10",
					"uservarm1" => "uservarm1",
					"uservarm2" => "uservarm2",
					"uservarm3" => "uservarm3",
					"uservarm4" => "uservarm4",
					"uservarm5" => "uservarm5",
					"uservarm6" => "uservarm6",
					"uservarm7" => "uservarm7",
					"uservarm8" => "uservarm8",
					"uservarm9" => "uservarm9",
					"uservarm10" => "uservarm10",
					"search_term" => "search_term"
				),
			)
		);
		echo "listing aw products <br>\n";
		flush();
		$tmp =  $odl->arr();
		$cnt = 0;
		foreach($tmp as $oid => $data)
		{
			if ((++$cnt % 1000) == 1)
			{
				echo ".. $cnt <br>\n";
				flush();
			}
		//	$rv[$data["ItemNo"]] = $data;
			$rv[$data["oid"]] = $data;
		}
		echo "got ".count($rv)." items <br>\n";
		flush();
		return $rv;
	}

	// I'll assume here, that the product object doesn't exist in aw
	// So I need to add a line in objects table, aw_shop_products table
	/*
		product_name
		product_code
		search_term
		replacement_product_code // I should change it to type code
		short_code
	*/
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
				description = '".addslashes($data['info'])."',
				search_term = '".addslashes($data['search_term'])."',
				type_code = '".addslashes($data['replacement_product_code'])."',
				short_code = '".$this->apply_controller($data['product_code'])."'
		";
		$this->db_obj->db_query($sql);
		return $oid;
	}

	// the product object exists in the system, so i need to update the data
	/*
		oid

		product_name
		product_code
		search_term
		replacement_product_code // I should change it to type code
		short_code
	*/
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
				description = '".addslashes($data['info'])."',
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


	public function update_dnotes()
	{
		$this->_start_import("dnotes");
		$this->_update_status("dnotes", warehouse_import_if::STATE_FETCHING, null, 0);

		$ds = get_instance($this->prop('data_source'));

		// get the pricelist data as XML
		$xml_data = $ds->get_dnotes_xml();
		$this->_write_xml_file("dnotes", $xml_data);

		$xml = new SimpleXMLElement($xml_data);

		$total = count($xml->dnote);
		$this->_update_status("dnotes", warehouse_import_if::STATE_PROCESSING, null, 0, $total);


		$odl = new object_data_list(array(
			"class_id" => CL_SHOP_DELIVERY_NOTE,
			"lang_id" => array(),
			"lang_id" => array()
		),
		array(
			CL_SHOP_DELIVERY_NOTE => array("number" => "number", "oid" => "oid")
		));
		$tmp = $odl->arr();
		$data = array();
		foreach($tmp as $r)
		{
			$data[$r["number"]] = $r;
		}
		

		$counter = 0;
		foreach ($xml->dnote as $cat)
		{
			$id = (string)$cat->number;
			if (isset($data[$id]))
			{
				// update
				$this->_update_dnote(obj($data[$id]["oid"]), $cat);
//echo "update dnote <br>";
			}
			else
			{
echo "add dnote <br>";
				// create
				$o = obj();
				$o->set_class_id(CL_SHOP_DELIVERY_NOTE);
				$o->set_parent(6411);
				$this->_update_dnote($o, $cat);
			}

			$this->_update_status("dnotes", warehouse_import_if::STATE_PROCESSING, null, ++$counter, $total);
			if ($this->need_to_stop_now("dnotes"))
			{
				$this->_end_import_from_flag("dnotes");
				die("stopped for flag");
			}
		}
		
		$this->_end_import("dnotes");
	}

	function _update_dnote($o, $cat)
	{

		$o->set_name((string)$cat->number);
		$o->set_prop("number", (string)$cat->number);
		$o->set_prop("delivery_date", (string)$cat->delivery_date);
		$o->set_prop("enter_date", (string)$cat->enter_date);
		$o->set_prop("customer", $this->_resolve_customer((string)$cat->customer_ext_id));
		$o->set_prop("from_warehouse", (string)$cat->from_warehouse);
		$o->set_prop("impl", (string)$cat->impl);
		$o->set_prop("currency", (string)$cat->currency);
		$o->set_prop("state", (string)$cat->state);
		$o->save();
		echo "updated dnote ".html::obj_change_url($o)." <br>\n";
flush();

		$ex_rows = array();
		foreach($o->connections_from(array("type" => "RELTYPE_ROW")) as $c)
		{
			$r = $c->to();
			$ex_rows[$r->product()->code] = $r;
		}
		foreach($cat->rows as $row)
		{
			if (isset($ex_rows[(string)$row->prod_code]))
			{
echo "update row <br>\n";
flush();
				$r = $ex_rows[(string)$row->prod_code];
				$pcrod = $this->_resolve_prod((string)$row->prod_code);
				if ($pcrod)
				{
					$r->set_prop("product", $pcrod);
					$r->set_prop("price", (string)$row->price);
					$r->set_prop("amount", (string)$row->amount);
					$r->set_prop("warehouse", (string)$row->warehouse);
					$r->save();
				}
			}
			else
			{
				if ($pcrod)
				{
					$r = obj();	
					$r->set_parent($o->id());
					$r->set_class_id(CL_SHOP_DELIVERY_NOTE_ROW);
					$r->set_prop("product", $pcrod);
					$r->set_prop("price", (string)$row->price);
					$r->set_prop("amount", (string)$row->amount);
					$r->save();
	
					$o->connect(array(
						"to" => $r->id(),
						"type" => "RELTYPE_ROW"
					));
					echo "added row ".html::obj_change_url($r)." <br>\n";
flush();
//echo "add row via ".((string)$row->prod_code)."<br>";
				}
			}
		}
	}

	function _resolve_customer($ex)
	{
		$ol = new object_list(array(
			"class_id" => CL_CRM_COMPANY,
			"extern_id" => $ex,
			"lang_id" => array(),
			"site_id" => array()
		));	
		if ($ol->count())
		{
			return $ol->begin()->id();
		}
		return null;
	}

	function _resolve_prod($ex)
	{
		$ol = new object_list(array(
			"class_id" => CL_SHOP_PRODUCT,
			new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					"code" => $ex,
					"short_code" => $ex
				)
			)),
			"lang_id" => array(),
			"site_id" => array()
		));	
		if ($ol->count())
		{
			return $ol->begin()->id();
		}
		return null;
	}

        public function update_bills()
        {
                $this->_start_import("bills");
                $this->_update_status("bills", warehouse_import_if::STATE_FETCHING, null, 0);

                $ds = get_instance($this->prop('data_source'));

                // get the pricelist data as XML
                $xml_data = $ds->get_bills_xml();

                $this->_write_xml_file("bills", $xml_data);

                $xml = new SimpleXMLElement($xml_data);

                $total = count($xml->bill);
                $this->_update_status("bills", warehouse_import_if::STATE_PROCESSING, null, 0, $total);


                $odl = new object_data_list(array(
                        "class_id" => CL_CRM_BILL,
                        "lang_id" => array(),
                        "lang_id" => array(),
                ),
                array(
                        CL_CRM_BILL => array("bill_no" => "bill_no", "oid" => "oid")
                ));
                $tmp = $odl->arr();
                $data = array();
                foreach($tmp as $r)
                {
                        $data[$r["bill_no"]] = $r;
                }


                $counter = 0;
                foreach ($xml->bill as $cat)
                {
                        $id = (string)$cat->bill_no;
                        if (isset($data[$id]))
                        {
                                // update
//                              $this->_update_bill(obj($data[$id]["oid"]), $cat);
echo "update bill $id <br>";
                        }
                        else
                        {
echo "add bill  <br>";
                                // create
                                $o = obj();
                                $o->set_class_id(CL_CRM_BILL);
                                $o->set_parent(131);
                                $o->set_name((string)$cat->name);
                                $this->_update_bill($o, $cat);
echo ("updated bill ".html::obj_change_url($o));
                        }

                        $this->_update_status("bills", warehouse_import_if::STATE_PROCESSING, null, ++$counter, $total);
                        if ($this->need_to_stop_now("bills"))
                        {
                                $this->_end_import_from_flag("bills");
                                die("stopped for flag");
                        }
                }

                $this->_end_import("bills");
        }

        function _update_bill($o, $cat)
        {
                $o->set_prop("bill_no", (string)$cat->bill_no);
                $o->set_prop("impl", (string)$cat->impl);
                $o->set_prop("bill_date", (string)$cat->bill_date);
                $o->set_prop("bill_due_date_days", (string)$cat->bill_due_date_days);
                $o->set_prop("sum", (string)$cat->sum);
                $o->set_prop("currency", (string)$cat->currency);
                $o->set_prop("disc", (string)$cat->disc);
                $o->set_prop("approved", (string)$cat->apprived);
                $o->set_prop("customer", $this->_resolve_customer((string)$cat->customer_ext_id));
                $o->set_prop("warehouse", (string)$cat->warehouse);
                $o->save();

                $ex_rows = array();
                foreach($o->connections_from(array("type" => "RELTYPE_ROW")) as $c)
                {
                        $r = $c->to();
                        $ex_rows[$r->name()] = $r;
                }
//echo "rows = ".dbg::dump($cat->rows)." <br>";
                foreach($cat->rows as $row)
                {
                        if (isset($ex_rows[(string)$row->name]))
                        {
                                // nothing
                        }
                        else
                        {
                                $pcrod = $this->_resolve_prod((string)$row->prod);
                                if ($pcrod)
                                {
                                        $r = obj();
                                        $r->set_parent($o->id());
                                        $r->set_class_id(CL_CRM_BILL_ROW);
                                        $r->set_prop("name", (string)$row->name);
                                        $r->set_prop("amt", (string)$row->amt);
                                        $r->set_prop("prod", $pcrod);
                                        $r->set_prop("price", (string)$row->price);
                                        $r->set_prop("desc", (string)$row->desc);
                                        $r->save();

                                        $o->connect(array(
                                                "to" => $r->id(),
                                                "type" => "RELTYPE_ROW"
                                        ));
                                        echo "added row ".html::obj_change_url($r)." <br>\n";
//echo "add row via ".((string)$row->prod_code)."<br>";
                                }
                        }
                }
        }

        public function update_orders()
        {
                $this->_start_import("orders");
                $this->_update_status("orders", warehouse_import_if::STATE_FETCHING, null, 0);

                $ds = get_instance($this->prop('data_source'));

                // get the pricelist data as XML
                $xml_data = $ds->get_orders_xml();

                $this->_write_xml_file("orders", $xml_data);

                $xml = new SimpleXMLElement($xml_data);

                $total = count($xml->order);
                $this->_update_status("orders", warehouse_import_if::STATE_PROCESSING, null, 0, $total);


                $odl = new object_data_list(array(
                        "class_id" => CL_SHOP_SELL_ORDER,
                        "lang_id" => array(),
                        "lang_id" => array(),
                ),
                array(
                        CL_SHOP_SELL_ORDER => array("number" => "number", "oid" => "oid")
                ));
                $tmp = $odl->arr();
                $data = array();
                foreach($tmp as $r)
                {
                        $data[$r["number"]] = $r;
                }


                $counter = 0;
                foreach ($xml->order as $cat)
                {
                        $id = (string)$cat->number;
                        if (isset($data[$id]))
                        {
                                // update
//                              $this->_update_order(obj($data[$id]["oid"]), $cat);
echo "update order $id <br>";
                        }
                        else
                        {
echo "add order  <br>";
                                // create
                                $o = obj();
                                $o->set_class_id(CL_SHOP_SELL_ORDER);
                                $o->set_parent(131);
                                $o->set_name((string)$cat->name);
                                $this->_update_order($o, $cat);
echo ("updated order ".html::obj_change_url($o));
                        }

                        $this->_update_status("orders", warehouse_import_if::STATE_PROCESSING, null, ++$counter, $total);
                        if ($this->need_to_stop_now("orders"))
                        {
                                $this->_end_import_from_flag("orders");
                                die("stopped for flag");
                        }
                }

                $this->_end_import("orders");
        }

        function _update_order($o, $cat)
        {
                $o->set_prop("number", (string)$cat->number);
                $o->set_prop("purchaser", $this->_resolve_customer((string)$cat->purchaser_ext_id));
                $o->set_prop("date", (string)$cat->date);
                $o->set_prop("deal_date", (string)$cat->deal_date);
                $o->set_prop("currency", (string)$cat->currency);

                $o->set_prop("warehouse", (string)$cat->warehouse);
                $o->save();

                $ex_rows = array();
                foreach($o->connections_from(array("type" => "RELTYPE_ROW")) as $c)
                {
                        $r = $c->to();
                        $ex_rows[$r->name()] = $r;
                }
//echo "rows = ".dbg::dump($cat->rows)." <br>";
                foreach($cat->rows as $row)
                {
                        if (isset($ex_rows[(string)$row->name]))
                        {
                                // nothing
                        }
                        else
                        {
                                $pcrod = $this->_resolve_prod((string)$row->prod);
                                if ($pcrod)
                                {
                                        $r = obj();
                                        $r->set_parent($o->id());
                                        $r->set_class_id(CL_SHOP_ORDER_ROW);
                                        $r->set_prop("name", (string)$row->name);
                                        $r->set_prop("prod_name", (string)$row->prod_name);
                                        $r->set_prop("warehouse", (string)$row->warehouse);
                                        $r->set_prop("prod", $pcrod);
                                        $r->set_prop("amount", (string)$row->amount);
                                        $r->set_prop("price", (string)$row->price);
                                        $r->set_prop("date", (string)$row->date);
                                        $r->save();

                                        $o->connect(array(
                                                "to" => $r->id(),
                                                "type" => "RELTYPE_ROW"
                                        ));
                                        echo "added row ".html::obj_change_url($r)." <br>\n";
//echo "add row via ".((string)$row->prod)."<br>";
                                }
                        }
                }
        }

}

?>
