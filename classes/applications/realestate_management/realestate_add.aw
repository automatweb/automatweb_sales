<?php

// realestate_add.aw - Kinnisvaraobjekti lisamine
/*

@classinfo syslog_type=ST_REALESTATE_ADD relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=markop

@default table=objects
@default group=general
@default field=meta
@default method=serialize

@property realestate_type type=select
@caption Kinnisvaraobjekti tüüp

@property realestate_environment type=relpicker reltype=RELTYPE_MANEGER
@caption Kinnisvarahalduse keskkond

property redir_object type=relpicker reltype=RELTYPE_REDIR_OBJECT rel=1
caption Dokument millele suunata

@groupinfo required_fields caption="Kohustuslikud väljad"
@default group=required_fields

@property required_fields type=callback callback=callback_get_fields store=no no_caption=1
@caption väljad

@groupinfo levels caption=Tasemed
@default group=levels

@property levels type=table store=no no_caption=1
@caption Tasemed

@property help type=text
@caption Abi:

-----------------------------------------------------------------------

@groupinfo bank caption="Pankade info"
@default group=bank

@property bank type=callback callback=callback_bank store=no no_caption=1

@groupinfo import caption="Import"
@default group=import

@property import type=textarea store=no
@caption see tuleb arvatavasti iga uue impordiga ümber progeda, et momendil töötab juhul kui tabel on kujul (misiganes	Asula_nimi		Asula_tüüp(5-väikelinn , 6-linnaosa)	misiganes	parent parent_nimi	maakond	maakonna_nimi) ja esimestes kahes reas kasulikku infor pole

//tõlgitavaks
@groupinfo transl caption=T&otilde;lgi
@default group=transl

@property transl type=callback callback=callback_get_transl
@caption T&otilde;lgi

@reltype MANEGER value=1 clid=CL_REALESTATE_MANAGER
@caption Saatja

reltype REDIR_OBJECT value=2 clid=CL_DOCUMENT
caption ümbersuunamine

*/

class realestate_add extends class_base
{
	var $opt = array(
		CL_REALESTATE_HOUSE => "Maja",
		CL_REALESTATE_ROWHOUSE => "Ridaelamu",
		CL_REALESTATE_COTTAGE => "Suvila",
		CL_REALESTATE_HOUSEPART => "Majaosa",
		CL_REALESTATE_APARTMENT => "Korter",
		CL_REALESTATE_COMMERCIAL => "Äripind",
		CL_REALESTATE_GARAGE => "Garaaz",
		CL_REALESTATE_LAND => "Maa",
	);

	function realestate_add()
	{
		// change this to the folder under the templates folder, where this classes templates will be,
		// if they exist at all. Or delete it, if this class does not use templates
		$this->init(array(
			"tpldir" => "realestate_add",
			"clid" => CL_REALESTATE_ADD
		));
		$this->trans_props = array(
			"comment",
			"levels",
		);
	}

	//////
	// class_base classes usually need those, uncomment them if you want to use them
	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "realestate_type":
				$options = array(
						"" => t(""),
						CL_REALESTATE_HOUSE => t("Maja"),
						CL_REALESTATE_ROWHOUSE => t("Ridaelamu"),
						CL_REALESTATE_COTTAGE => t("Suvila"),
						CL_REALESTATE_HOUSEPART => t("Majaosa"),
						CL_REALESTATE_APARTMENT => t("Korter"),
						CL_REALESTATE_COMMERCIAL => t("Äripind"),
						CL_REALESTATE_GARAGE => t("Garaaz"),
						CL_REALESTATE_LAND => t("Maa"),
					);
				//kui kinnisvaraobjekti tüüp valitud, siis teda enam muuta ei saa
				if(($arr["obj_inst"]->prop("realestate_type")))
				{
					$prop["type"] = "text";
					$prop["value"] =  $options[$prop["value"]];
				}
				else
				{
					$prop["options"] = $options;
				}
				break;
			case "levels":
				$this->do_table($arr);
				break;
			case "help":
				$template_dir = $this->site_template_dir;
				$prop["value"] = nl2br(htmlentities("
				peab olema määratud nii template faili nimi, kui ka taseme nimi.
				Template fail peab asuma kataloogis :".$template_dir.".
				Kui tahad templates näha valmis tehtud property't koos õigete valikutega jne, siis kasuta templates muutujuat {VAR:property}, kui vaja läheb vaid property väärtust , kasuta muutujat {VAR:property_value} (property asemele siis vastava property nimi, mille saab Kohustuslike väljade alt...sulgudes olev tekst).
				Kui miski property kohta märkida, et see on kohustluslik, siis töötab asi nii, et juhul , kui mingisse teplate'i kirjutatakse vastava property nimi, siis sealt edasi ei lasta , enne kui ta miski väärtuse kaasa saab.

				Et saaks erinevatele tasemetele tagasi minna, siis tuleks kasutada template'is miskit taolist asja:
				<!-- SUB: ACT_LEVEL -->
				<a href={VAR:level_url}>{VAR:level_name}</a>
				<!-- END SUB: ACT_LEVEL -->
				<!-- SUB: LEVEL -->
				{VAR:level_name}
				<!-- END SUB: LEVEL -->
				kus siis {VAR:level_name} asemele tekkivad kõik tasemete nimed ja {VAR:level_url} asemele tasemete urlid... vaid juhul kui vastavale tasemele pääsemiseks on vajalikud väljad juba täidetud. {VAR:reforb} oleks ka kasulik kuskile formi sisse panna

				Kasutuses veel (vajalikud xmlrewquest jaoks):
				{VAR:url}
				{VAR:admin_structure_id} ,Riigi haldusjaotuse ID
				{VAR:div0}-{VAR:div4}, vastavalt siis maakonna, linna, linnaosa, valla ja asula/küla haldusüksuse IDd

				miski suvalise kasutaja lisatud kinnisvaraobjektide nimekirja genereerimiseks peab olema õiges kaustas fail list.tpl
				muutujad:
				name - kinnisvaraobjekti nimi
				id - objekti id... kui see urliks panna, siis näitab objekti andmeid
				change - url, millelt saab antud kinnisvaraobjekti muuta

				kui objekt on lisatud saidilt miski dokumendi kaudu mis siiamaani eksisteerib, siis muutmisel kasutatakse sama dokumenti... kui seda pole, siis on vajalik default_change.tpl nimeline fail

				piltide muutujad:
				{VAR:picture0} - {VAR:picture9} pildid img tag'ina
				{VAR:picture0del} - {VAR:picture9del} piltide kustutamise chexkboxid
				pildiuploadide nimed peavad olema picture0upload - picture9upload.

				"));
				break;
		};
		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "required_fields":
				$this->submit_meta($arr);
				break;
			case "levels":
				$this->submit_meta($arr);
				break;
			case "bank":
				$this->submit_meta($arr);
				break;
			case "realestate_type":
				if($arr["obj_inst"]->prop("realestate_type"))
				{
					$prop["value"] = $arr["obj_inst"]->prop("realestate_type");
				}
				break;
			case "import":
				$this->import($arr);

			case "transl":
				$this->trans_save($arr, $this->trans_props);
			break;
		}
		return $retval;
	}

	function callback_get_transl($arr)
	{
		return $this->trans_callback($arr, $this->trans_props);
	}

	function import($arr)//Eesti haldusjaotuse puhul töötab miski 10 minutit... saaks ju teha kiiremaks , kuid kuna asi on suht ühekordseks kasutamiseks....
	{
		$targ = obj($arr["alias"]["target"]);
		$clid = $arr["obj_inst"]->prop("realestate_type");
		$parent = $arr["obj_inst"]->prop("realestate_environment");
		$address_props = $this->get_address_props($parent);
		$realestate_environment_obj = obj($parent);
		$admin_structure_id = $realestate_environment_obj->prop("administrative_structure");
		$admin_structure = obj($admin_structure_id);

		$rows = explode("\n" , $arr["prop"]["value"]);
		unset($rows[0],$rows[1]);
		$data = array();
		foreach($rows as $row)
		{
			$params = explode("\t" , $row);
			$data[] = array(
				"id" => $params[0],
				"name" => $params[1],
				"nr" => $params[3],
				"type_name" => $params[4],
				"parent" =>  $params[5],
				"parent_name" => $params[6],
				"county" => $params[7],
				"county_name" => $params[8],
			);
		}
		$citys = array();
		$countys = array();
		$vallad = array();
		foreach($data as $row)
		{
			if(!array_key_exists($row["county"] , $countys))
			{
				$countys[$row["county"]] = array(
					"id" => $row["county"],
					"name" => trim($row["county_name"]).'maa',
				);
			}

			if(substr_count($row["parent_name"], ' linn') == 1)
			{
				$citys[$row["parent"]] = array(
					"id" => $row["parent"],
					"name" => substr($row["parent_name"], 0, (strlen($row["parent_name"])-5)),
					"parent" => $row["county"],
				);
			}
			if(substr_count($row["parent_name"], ' vald') == 1)
			{
				$vallad[$row["parent"]] = array(
					"id" => $row["parent"],
					"name" => substr($row["parent_name"], 0, (strlen($row["parent_name"])-5)),
					"parent" => $row["county"],
				);
			}
		}
		foreach($countys as $county)
		{
			$param = array(
				"name" => $county["name"],
				"parent" => $admin_structure_id, // required. aw object or oid
				"division" => $address_props["county"],
				"type" => CL_COUNTRY_ADMINISTRATIVE_DIVISION,
			);
			$county_o = $admin_structure->set_prop("unit_by_name", $param);
			print '--------------------maakond'.$county["name"].'--------------------<br>';
			foreach($citys as $city)
			{
				if($city["parent"] == $county["id"])
				{
					print "lisatud linn - ".$city["name"].'<br>Linnaosad:<br>';
					foreach($data as $row)
					{
						if(($row["parent"] == $city["id"]) && ($row["nr"] == 6))
						{
							print $row["name"].'<br>';
						}
					}
				}
			}
			foreach($vallad as $vald)
			{
				if($vald["parent"] == $county["id"])
				{
					$param = array(
						"name" => $vald["name"],
						"parent" => $county_o->id(),
						"division" => $address_props["vald"],
						"type" => CL_COUNTRY_ADMINISTRATIVE_DIVISION,
					);
					$vald_o = $admin_structure->set_prop("unit_by_name", $param);
					print "lisatud vald - ".$vald["name"].'<br>';
					print 'Alevikud, külad ja muud getod:<br>';
					foreach($data as $row)
					{
						if($row["parent"] == $vald["id"])
						{
							if($row["nr"] == 5)
							{
								echo "linn - ".$row["name"].'<br>';
								continue;
							}
							$param = array(
								"name" => $row["name"],
								"parent" => $vald_o->id(),
								"division" => $address_props["settlement"],
								"type" => CL_COUNTRY_ADMINISTRATIVE_DIVISION,
							);
							$settlement_id = $admin_structure->set_prop("unit_by_name", $param);
							print $row["name"].'<br>';
						}
					}
				}
			}
		}
	}

	function submit_meta($arr = array())
	{
		$meta = $arr["request"]["meta"];
		//praagib välja tasemed, kus ei ole kas adekvaatset template faili või nime
		if(($arr["prop"]["name"] == "levels") && is_array($meta))
		{
			$temp_arr = array();
			foreach($meta as $metadata)
			{
				if((strlen($metadata["name"]) > 0) && strlen($metadata["template"]) > 4)
				{
					$temp_arr[] = $metadata;
				}
			}
			$meta = $temp_arr;
		}
		if (is_array($meta))
		{
			$so = new object($arr["obj_inst"]->id());
			$so->set_name($arr["obj_inst"]->name());
			$so->set_meta($arr["prop"]["name"], $meta);
			$so->save();
		};
	}

	//tekitab võimalike pankade ja propertyte nimekirja
	function callback_bank($arr)
	{
		$bank_payment = get_instance(CL_BANK_PAYMENT);
		$meta = $arr["obj_inst"]->meta("bank");
		foreach($bank_payment->for_all_banks as $key => $val)
		{
			$ret[] = array(
				"name" => "meta[".$key."]",
				"caption" => $val,
				"type" => "textbox" ,
				"value" => $meta[$key],
			);
		}

		foreach($bank_payment->banks as $key => $val)
		{
			$ret[] = array(
				"name" => "meta[".$key."][use]",
				"type" => "chechbox" ,
				"ch_value" => 1 ,
				"value" => $meta["key"],
				"caption" => $val,
			);
			foreach($bank_payment->bank_props as $prop=>$caption)
			{
				$ret[] = array(
					"name" => "meta[".$key."][".$prop."]",
					"type" => "textbox",
					"value" => $meta[$key][$prop],
					"caption" => $caption
				);
			}
		}
		return $ret;
	}

	//tekitab vastava kinnisvara objektide propertite nimekirja,
	//kust siis saab valida, mida on kohustuslik täita jne
	function callback_get_fields($arr)
	{
		if(($arr["obj_inst"]->prop("realestate_type")))
		{
			$clid = $arr["obj_inst"]->prop("realestate_type");
			$ret = array();
			$cfgu = get_instance("cfg/cfgutils");
//			$clss = aw_ini_get("classes");
//			$class_entry = $clss[$clid];
//			$file = $class_entry["file"];

			$o = obj();
			$o->set_class_id($clid);
			$props = array_merge(
				$o->get_property_list(),
				$cfgu->load_class_properties(array(
					"clid" => CL_REALESTATE_PROPERTY,
				))
			);

			$groups = $cfgu->get_groupinfo();
			$meta = $arr["obj_inst"]->meta("required_fields");

			foreach($props as $name => $prop)
			{
				if($prop["caption"])
				{
					$value = 0;
					if($meta[$prop["name"]])
					{
						$value = 1;
					}
					$ret[] = array(
						"name" => "meta[".$name."]",
						"caption" => //"{VAR:".$prop["name"]."}",
						$prop["caption"].' ('.$prop["name"].')',
						"type" => "checkbox" ,
						"ch_value" => 1 ,
						"value" => $value,
					);
				}
			}
		}
		return $ret;
	}

	function do_table($arr)
	{
		$levels = $arr["obj_inst"]->meta("levels");
		$t = &$arr["prop"]["vcl_inst"];

		$t->define_field(array(
			"name" => "id",
			"caption" => t("Tase"),
//			"sortable" => 1,
		));
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Etapi nimi"),
		));

		aw_global_set("output_charset", "utf-8");
		$lg = get_instance("languages");
		$langdata = $lg->get_list();
		foreach($langdata as $id => $lang)
		{
			if($arr["obj_inst"]->lang_id() != $id)
			{
				$t->define_field(array(
					"name" => $id,
					"lang_id" => $id,
					"caption" => t($lang),
				));
			}
		}

		$t->define_field(array(
			"name" => "template",
			"caption" => t("Template"),
		));
		$transyes = $arr["obj_inst"]->prop("transyes");
//		$langdata = array();
		$count = 1;
		foreach($levels as $level)
		{
			$data = array(
				"id" => $count,
			);

			$data["name"] = html::textbox(array(
				"name" => "meta[".$count."][name]",
				"size" => 30,
				"value" => $level["name"],
			));

			foreach($langdata as $lid => $lang)
			{
				 $data[$lid] = html::textbox(array(
					"name" => "meta[".$count."][tolge][".$lid."]",
					"size" => 15,
					"value" => $level["tolge"][$lid],
				));
			}

			$data["template"] = html::textbox(array(
				"name" => "meta[".$count."][template]",
				"size" => 30,
				"value" => $level["template"],
			));
			$t->define_data($data);
			$count++;

		}
		$new_data = array(
			"id" => $count,
		);

		 $new_data["name"] = html::textbox(array(
			"name" => "meta[".$count."][name]",
			"size" => 30,
			"value" => "",
		));

		foreach($langdata as $lid => $lang)
		{
			 $new_data[$lid] = html::textbox(array(
				"name" => "meta[".$count."][tolge][".$lid."]",
				"size" => 15,
				"value" => "",
			));
		}

		$new_data["template"] = html::textbox(array(
			"name" => "meta[".$count."][template]",
			"size" => 30,
			"value" => "",
		));
		$t->define_data($new_data);
		$t->set_sortable(false);
	}

	//kui kinnisvaraobjekti tüüpi pole määratud, siis pole tasemete ja kohustuslike väljade grupid eriti vajalikud
	function callback_mod_tab($arr)
	{
		if((!$arr["obj_inst"]->prop("realestate_type"))
		&& (($arr["id"] == "required_fields")
		|| ($arr["id"] == "levels")))
		{
			return false;
		}
		if ($arr["id"] == "transl" && aw_ini_get("user_interface.content_trans") != 1)
		{
//			return false;
		}
		return true;
	}

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function type_name($clid)
	{
		switch ($clid)
		{
			case CL_REALESTATE_HOUSE:
				return "house";
				break;
			case CL_REALESTATE_ROWHOUSE:
				return "rowhouse";
				break;
			case CL_REALESTATE_COTTAGE:
				return "cottage";
				break;
			case CL_REALESTATE_HOUSEPART:
				return "housepart";
				break;
			case CL_REALESTATE_APARTMENT:
				return "apartment";
				break;
			case CL_REALESTATE_COMMERCIAL:
				return "commercial";
				break;
			case CL_REALESTATE_GARAGE:
				return "garage";
				break;
			case CL_REALESTATE_LAND:
				return "land";
				break;
			default:
				return FALSE;
		}
	}

	//kontroll, kas mõni vajalik väli on jäänud täitmata
	function not_filled($arr)
	{
		$ret = FALSE;
		extract($arr);
		if(sizeof($data) > 0)
		{
			foreach($data as $key => $val)
			{
				if(!(strlen($val) > 0) && $fields[$key])
				{
					error::raise(array(
						"msg" => t("väli '".$key."' peab olema täidetud"),
						"fatal" => false,
						"show" => true,
					));
					$ret = true;
				}
			}
		}
		return $ret;
	}

	function get_address_props($parent)
	{
		$realestate_environment_obj = obj($parent);
		$address_props = array(
			"county"	=> $realestate_environment_obj->prop("address_equivalent_1"),
			"city"		=> $realestate_environment_obj->prop("address_equivalent_2"),
			"citypart"	=> $realestate_environment_obj->prop("address_equivalent_3"),
			"vald"		=> $realestate_environment_obj->prop("address_equivalent_4"),
			"settlement"	=> $realestate_environment_obj->prop("address_equivalent_5"),
			"street"	=> "street",
			"house"=> 0,
			"apartment"	=> 0,
		);
		return $address_props;
	}

	function fill_session($args)
	{
		extract($args);
		$realestate_obj = obj($id);
		$props = $realestate_obj->get_property_list();
		foreach($props as $key => $val)
		{
			if($realestate_obj->prop($key))
			{
				$_SESSION["realestate_input_data"][$key] = $realestate_obj->prop($key);
			}
		}
		$address_props = $this->get_address_props($parent);
		$address = $realestate_obj->get_first_obj_by_reltype("RELTYPE_REALESTATE_ADDRESS");
		if(is_object($address))$tmp_address_data = $address->prop("address_data");
		$address_data = array();
		foreach ($tmp_address_data as $key => $val)
		{
			$address_data[$val["division"]] = $val["id"];
		}
		foreach($address_props as $key => $val)
		{
			if((($key == "house") || ($key == "apartment")) && (is_object($address)))
			{
				$_SESSION["realestate_input_data"][$key] = $address->prop ($key, $val);
			}
			else
			{
				$_SESSION["realestate_input_data"][$key] = $address_data[$val];
				if($key == "street" && is_oid($address_data[$val])){
					$street_obj = obj($address_data[$val]);
					$_SESSION["realestate_input_data"][$key] = $street_obj->name();
				}
			}
		}
		$_SESSION["realestate_input_data"]["realestate_id"] = $id;
		$_SESSION["realestate_input_data"]["filled_level"] = 256;//lihtsalt miski suur number
	}

	function is_admin()
	{
		$grps = aw_ini_get("realestate.admin_groups");
		$gl = aw_global_get("gidlist_oid");
		$has = false;//et siis kui on tegu maakleri või adminniga peaks see trueks muutuma
		foreach(explode(",", $grps) as $grp)
		{
			if ($gl[trim($grp)])
			{
				$has = true;
			}
		}
		return $has;
	}

	function result_page($arr)
	{
		$ret = "";
		$bank_payment = get_instance(CL_BANK_PAYMENT);
		if(sizeof($_SESSION["bank_return"]["data"])>5)
		{
			if($_SESSION["bank_return"]["data"]["VK_SERVICE"] == 1901) $ret.= '<br>Maksmine ei õnnestunud<br><br>';
			if($_SESSION["bank_return"]["data"]["VK_SERVICE"] == 1101)
			{
				if(!$bank_payment->check_response()) return "mingi jama";
				$obj_id = substr($_SESSION["bank_return"]["data"]["VK_REF"], 0, (strlen($_SESSION["bank_return"]["data"]["VK_REF"])-1));
				if(is_oid($obj_id))
				{
					$realest_obj = obj($obj_id);
					$valid_for = $realest_obj->prop("weeks_valid_for");
					$realest_obj->set_prop("is_visible" , "1");
					if(!$realest_obj->prop("expire") || (time() > $realest_obj->prop("expire"))) $expire = time();
					else $expire = $realest_obj->prop("expire");
					$realest_obj->set_prop("expire" , $expire + 604800 * $valid_for);
					$ret.= "maksmine õnnestus, pakkumine nüüd leheküljel nähtav ".$valid_for." nädalat";
					$realest_obj->save();
					$this->read_template("bank_return.tpl");
					lc_site_load("realestate", $this);
					$this->level_vars(array(
					));
					return $this->parse();
				}
			}
			$_SESSION["bank_return"]["data"] = null;
		}
		$targ = obj($arr["alias"]["target"]);
		$bank_meta = $targ->meta("bank");
		if (!$realest_obj) $realest_obj = obj($_SESSION["realestate_input_data"]["realestate_id"]);
		$bank_meta["amount"] = $bank_meta["amount"]*$realest_obj->prop("weeks_valid_for");
		$_SESSION["bank_payment"] = array(
			"data"		=> $bank_meta,
			"reference_nr"	=> $_SESSION["realestate_input_data"]["realestate_id"],
		//	"test"		=> 1, // kui miskit testkeskkondades vaja katsetada.. sel juhul peab pangainfo ka testkeskkonna omaks muutma
			"url" 		=> post_ru(),
			"cancel"	=> post_ru(),
		);
		$prop_obj = get_instance(CL_REALESTATE_PROPERTY);
		$object_view = $prop_obj->request_execute($realest_obj);
//		if(file_exists($tpl))
//		{
			//kas tegu maakleriga?
			$has = $this->is_admin();
			$this->read_template("result.tpl");
			lc_site_load("realestate", $this);
			$pay = "";
			$make_visible = "";
			$tyyp = $realest_obj->prop_str("transaction_type");
			if($has || $tyyp == "Ost")
			{
				if(!$realest_obj->prop("is_visible"))
				{
					$html = get_instance("html");
					$this->vars(array("extend_popup" => $html->popup(array(
						"url" 	=> $this->mk_my_orb("extend", array("id" => $realest_obj->id(), "fast" => 1,)),
						"no_link" => 1,
					))));
					$make_visible .= $this->parse("MAKE_VISIBLE");
				}
			}
			else
			{
				$this->vars(array("pay_link"	=> $bank_payment->mk_my_orb("pay_site", array()),));
				$pay .= $this->parse("PAY");
			}

			$this->vars(array(
				"PAY"		=> $pay,
				"MAKE_VISIBLE"	=> $make_visible,
				"object_view"	=> $object_view,
				"change_link" 	=> $realest_obj->meta("added_from")."?id=".$realest_obj->id(),
				"list_link"	=> $this->mk_my_orb("my_realestate_list", array()),
				"pay_link"	=> $bank_payment->mk_my_orb("pay_site", array()),
			));
			return $this->parse();

//		}
		//juhul kui template faili pole, siis annab miski random variandi...
		$ret.= $prop_obj->request_execute($realest_obj);
		$ret.= '<br><br><a href="';
		$ret.= $realest_obj->meta("added_from")."?id=".$realest_obj->id();
		$ret.= '"> Tagasi muutmisesse</a><a href="';
		$ret.= $this->mk_my_orb("my_realestate_list", array());
		$ret.= '"> Kõik sisestatud pakkumised </a>';
		if(!($realest_obj->prop("is_visible")))
		{
			$ret.= '<a href="';
			$ret.= $bank_payment->mk_my_orb("pay_site", array());
			$ret.= '"> Maksma </a>';
		}
		return $ret;
	}

	/** Change the realestate object info.

		@attrib name=parse_alias is_public="1" caption="Change"

	**/
	function parse_alias($arr)
	{
		enter_function("realestate_add::parse_alias");
		global $end , $level, $id, $default;
		if($end) return $this->result_page($arr);//kui templated otsas või tuleb pangamakselt, siis läheb lõpuvaatesse

		if (!$this->can("edit", $arr["alias"]["target"]))
		{
			$i = get_instance("menuedit");
			$i->do_error_redir($arr["alias"]["target"]);
		}

//
//		if (!$this->can("edit", $arr["alias"]["target"]))
//		{
//			$i = get_instance("menuedit");
//			$i->do_error_redir($oid);
//		}

		$targ = obj($arr["alias"]["target"]);
		$clid = $targ->prop("realestate_type");
		$levels = $targ->meta("levels");
		$fields = $targ->meta("required_fields");
		$parent = $targ->prop("realestate_environment");
		if(!$level)//kui levelit pole määratud, siis on tegu uue sisestusega, et siis oleks abiks kui miski vana sesioon tühjaks teha
		{
			$level = 1;
			$_SESSION["realestate_input_data"] = NULL;
		}
		if($id)//see siis tähendab, et muudetakse juba olemasolevat.... st vaja sessioon infot täis toppida jne
		{
			if(!$this->can("edit", $id))
			{
				return;
			}
			$realest_obj = obj($id);
			if(array_key_exists($realest_obj->class_id() , $this->opt))
			{
				$clid = $realest_obj->class_id();
				$parent = $realest_obj->prop("realestate_manager");
				$this->fill_session(array("id" => $id , "parent" => $parent));
				$id = null;
			}
		}
		$realestate_environment_obj = obj($parent);
		$data = $_SESSION["realestate_input_data"];
		$data["level"] = NULL;
		$this->vars(array("url" => $this->mk_my_orb("get_divisions", array())));
		if(!$default)
		{
			if($this->not_filled(array("data" => $data , "fields" => $fields,)) && !$id)
			{
				$level = $level-1;
			}
			else
			{
				if($level > $_SESSION["realestate_input_data"]["filled_level"])
				{
					$_SESSION["realestate_input_data"]["filled_level"] = $level-1;
				}
			}
			$tpl = $levels[($level-1)]["template"];
			$tpl2 = $levels[$level]["template"];
			$_SESSION["realestate_input_data"]["level"] = $level+1;
		}
		else //muudetaval kinnisvaraobjektil pole teada kuda teda sisestati.... st muutmiseks läheb käiku miski default template... antud juhul "default_change.tpl", sinna soovitaks muutmist võimaldada vaid propertytele, mis kõigil erinevatel kinnisvaraobjekti tüüpidel olemas, muidu ... minupärast võib ju, kuid ei soovitaks..
		{
			$tpl = "default_change.tpl";
		}
		$this->read_template($tpl);

		lc_site_load("realestate", &$this);
		//tekitab muutujad erinevate tasemete nimede ja linkidega
		$this->level_vars(array("levels" => $levels , "data" => $data));

		//juhul , kui template faile rohkem ei ole, siis läheb edasi objekti salvestama
		if(!$tpl2)
		{
			$do = "submit";
		}
		$this->vars(array(
			"reforb" => $this->mk_reforb("subscribe",array(
				"section"	=> aw_global_get("section"),
				"level"		=> $level,
				"return_to"	=> post_ru(),
				"id"		=> $arr["alias"]["target"],
				"do"		=> $do,
				"parent"	=> $parent,
				"type"		=> $this->type_name($clid),
				"clid"		=> $clid,
				"default"	=> $default,
			)),
		));
		//property tervenisti saatmine... valmisjoonistatud kujul
		$props_html = $this->get_props_for_site(array(
			"clid"		=> $clid,
			"parent"	=> $parent,
		));
		$this->vars($props_html);
		//property väärtuse saatmine kujul "property_nimi"_value
		$data_value = array();
		foreach($_SESSION["realestate_input_data"] as $key => $value)
		{
			$data_value[$key.'_value'] = $value;
		}
		//see pole tegelt kõige õigem lahendus vist...a noh, toimib vähemalt
		if(substr_count($data_value["transaction_price_value"] , "e+") > 0)
		{
			$asd = explode("e+" , $data_value["transaction_price_value"]);
			$data_value["transaction_price_value"] = (double)$asd[0] * pow(10 , (int)$asd[1]);
		}

		$this->vars($data_value);

		$subs = array("county", "city" ,"citypart", "vald" , "settlement");
		$parents = array(
			"city" => "county",
			"citypart" => "city",
			"vald" => "county",
			"settlement" => "vald",
		);
		$admin_structure_id = $realestate_environment_obj->prop("administrative_structure");
		$division = array(
			$realestate_environment_obj->prop("address_equivalent_1") ,
			$realestate_environment_obj->prop("address_equivalent_2") ,
			$realestate_environment_obj->prop("address_equivalent_3") ,
			$realestate_environment_obj->prop("address_equivalent_4") ,
			$realestate_environment_obj->prop("address_equivalent_5")
		);
		$this->picture_vars($arr);
		//muutujad div0 - div4 vastavalt haldusüksuste IDd
		foreach ($division as $key => $div)
		{
			$this->vars(array("div".$key => $div));
		}
		//saidil läheb vast vaja ka Riigi haldusjaotuse IDd
		$this->vars(array("admin_structure_id" => $admin_structure_id));
		//$parent_division; // siia peaks miski väärtuse panema, kui tahaks get_divisions funktsioonist ühe kindla halduspiirkonna alampiirkondade nimekirja
		foreach($subs as $key => $sub)//erinevate maakondade, linnade , linnaosade , valdade jne valikud, mis loodetavasti on SUBides
		{
			//$parent_division = $_SESSION["realestate_input_data"][$sub]
			if (($sub != "county") && !($parent_division = $_SESSION["realestate_input_data"][$parents[$sub]])) continue; //et alguses ei loeks sisse muud kui maakonnad
			if ($this->is_template($sub))
			{
				$this->vars(array($sub => $this->get_divisions(array(
					"admin_structure_id"	=> $admin_structure_id,
					"division"		=> $division[$key],
					"parent"		=> $parent_division,
					"sub"			=> $sub,
				))));
			}
		}
		exit_function("realestate_add::parse_alias");
		return $this->parse();
	}

	function picture_vars($arr)//temlate'i igasugused pildi, ja pildi kustutamise checkboxi var'id
	{
		$x = 0;
		$image_inst = get_instance(CL_IMAGE);
		if(is_oid($_SESSION["realestate_input_data"]["realestate_id"]))
		{
			$this_object = obj($_SESSION["realestate_input_data"]["realestate_id"]);

			$pictures = new object_list($this_object->connections_from (array (
				"type" => "RELTYPE_REALESTATE_PICTURE",
				"class_id" => CL_IMAGE,
			)));
			$x = 0;

			$existing_pics = $pictures->ids();

				if($this_object->meta("pic_order"))
				{
					$tmp_existing_pics = array();
					foreach($this_object->meta("pic_order") as $ordered_pic)
					{
						if(in_array($ordered_pic , $existing_pics))
						{
							$tmp_existing_pics[] = $ordered_pic;
						}
					}
					foreach($existing_pics as $existing_pic)
					{
						if(!in_array($existing_pic , $tmp_existing_pics))
						{
							$tmp_existing_pics[] = $existing_pic;
						}
					}
					$existing_pics = $tmp_existing_pics;
				}

/*			foreach($pictures->arr() as $pic)
			{
				$this->vars(array(
					"picture".$x => $image_inst->make_img_tag_wl($pic->id()),
					"picture".$x."del" => "<input type='checkbox' name='delete[".$pic->id()."]' value='".$pic->id()."'/>",
				));
				$x++;
			}*/
			foreach($existing_pics as $pic)
			{
				$this->vars(array(
					"picture".$x => $image_inst->make_img_tag_wl($pic),
					"picture".$x."del" => t("Kustuta")."<input type='checkbox' name='delete[".$pic."]' value='".$pic."'/>",
				));
				$x++;
			}
		}
	}

	function level_vars($args)
	{
		extract($args);
		$c = "";
		$c_act = "";
		$lang_id = aw_global_get("lang_id");
		foreach($levels as $key => $data)
		{
			if($data["tolge"][$lang_id]) $data["name"] = iconv("UTF-8", aw_global_get("charset"),  $data["tolge"][$lang_id]);
			$key++;
			if(($_SESSION["realestate_input_data"]["filled_level"]+2) > $key)
			{
				if($this->is_template("ACT_LEVEL"))
				{
					$level_url = aw_url_change_var("level", ($key) , post_ru());
					$level_url = aw_url_change_var("id", null , $level_url);
					$this->vars(array(
						"level_name" => $data["name"],
						"level_url" => $level_url,
					));
					$c_act .= $this->parse("ACT_LEVEL");
				}
			}
			else
			{
				if($this->is_template("LEVEL"))
				{
					$this->vars(array(
						"level_name" => $data["name"],
					));
					$c .= $this->parse("LEVEL");
				}
			}
		}
		if($this->is_template("ACT_LEVEL") && $_SESSION["realestate_input_data"]["realestate_id"])
		{
			$level_url = aw_url_change_var("level", null , post_ru());
			$level_url = aw_url_change_var("id", null , $level_url);
			$level_url = aw_url_change_var("end", 1 , $level_url);
			$this->vars(array(
				"level_name" => t("Eelvaade"),
				"level_url" => $level_url,
			));
			$c .= $this->parse("ACT_LEVEL");
		}
		elseif($this->is_template("LEVEL"))
		{
			$this->vars(array(
				"level_name" => t("Eelvaade"),
			));
			$c .= $this->parse("LEVEL");
		}

		$this->vars(array(
			"ACT_LEVEL" => $c_act,
		));
		$this->vars(array(
			"LEVEL" => $c,
		));
	}

	/** get_divisions
		@attrib name=get_divisions nologin="1"
	**/
	function get_divisions($arr)
	{
		global $site , $admin_structure_id , $parent , $division;
		if($site)
		{
			$site = true;
			$arr["parent"] = $parent;
			$arr["admin_structure_id"] = $admin_structure_id;
			$arr["division"] = $division;
		}
		$admin_structure = obj($arr["admin_structure_id"]);
		$param = array(
			"prop" => "units_by_division",
			"division" => $arr["division"], // required. aw object or oid
			"parent" => $arr["parent"], // optional. int. aw oid
		);
		$unit_objlist = $admin_structure->prop($param);
		$unit_objlist->sort_by(array(
			"prop" => "name",
		));
		//juhul kui saidilt tuleb xmlhttprequest
		if($site)
		{
			header("Content-type: text/xml");
			$xml = "<?xml version=\"1.0\" encoding=\"".aw_global_get("charset")."\" standalone=\"yes\"?>\n<response>\n";
			if(is_array($unit_objlist->arr()) && is_oid($arr["parent"]))
			{
				foreach($unit_objlist->arr() as $key => $obj)
				{
					$xml .= "<item><value>".$obj->id()."</value><text>".$obj->name()."</text></item>";
				}
			}
			else
			{
				$xml .= "<item><value>0</value><text>".$arr["parent"]." </text></item>";
				$xml .= "<item><value>1</value><text>".$arr["division"]."</text></item>";
			}
			$xml .= "</response>";
			die($xml);
		}
		$c = "";
		foreach($unit_objlist->arr() as $key => $obj)
		{	$selected = "";
			if($_SESSION["realestate_input_data"][$arr["sub"]] == $obj->id() || ($_SESSION["realestate_input_data"][$arr["sub"]] == $obj->name() && $arr["sub"] == "settlement"))
			{
				$selected = "selected";
			}
			$this->vars(array(
				"division"	=> $obj->name(),
				"division_id"	=> $obj->id(),
				"selected"	=> $selected,
			));
			$c .= $this->parse($arr["sub"]);
		}
		return $c;
	}

	function get_props_for_site($arr)
	{
		extract($arr);
		if($_SESSION["realestate_input_data"]["realestate_id"])
		{
			$dummy = obj($_SESSION["realestate_input_data"]["realestate_id"]);
		}
		else
		{
			$dummy = new object();
			$dummy->set_class_id($clid);
			$dummy->set_parent($parent);
			$dummy->set_prop("realestate_manager" , $parent);
		}
		$rd = get_instance($clid);
		$rd2 = get_instance(CL_REALESTATE_PROPERTY);
		$rd->load_defaults();
		$rd2->load_defaults();
		$o = obj();
		$o->set_class_id($clid);
		$o_props = $o->get_property_list();

		//valitud propertytele leiab get_property funktsioonist väärtusi
		$props_to_get = array("year_built","transaction_broker_fee","transaction_broker_fee_type" , "transaction_rent_total" , "estate_price_total" , "legal_status" , "transaction_selling_price");
		foreach($o_props as $key => $val)
		{
			if(in_array($key , $props_to_get))
			{
			$rd->get_property(array("prop" => &$o_props[$key] , $prop, "request" => $request , "obj_inst" => $dummy));
			}
		}

		$cfgu = get_instance("cfg/cfgutils");
		$els = array_merge(
			$o_props,
			$cfgu->load_class_properties(array(
				"clid" => CL_REALESTATE_PROPERTY,
			))
		);

		//tegeleb vaid nende varidega, mis templates olemas on.... äkki teeb veidi kiiremaks
		foreach($els as $prop=>$val)
		{
			if(!$this->template_has_var($prop)) unset($els[$prop]);
		}

		$rd->load_defaults();
		$els = $rd->parse_properties(array(
			"properties" => $els,
			"obj_inst" => $dummy,
		));

		foreach($els as $key => $val)
		{
			unset($els[$key]["autocomplete_source"]);
			unset($els[$key]["autocomplete_params"]);
		}

		classload("cfg/htmlclient");
		$html = array();
		foreach($els as $key => $val)
		{
			$val["value"] = $_SESSION["realestate_input_data"][$key];
			$htmlc = new htmlclient(array(
				"template" => "real_webform.tpl",
			));
			$htmlc->set_layout($layout);
			$htmlc->start_output();
			$val["capt_ord"] = $val["wf_capt_ord"];
			$htmlc->add_property($val);
			$htmlc->finish_output();
			$html[$key] = $htmlc->get_result(array(
				"raw_output" => 1,
			));
		}
	//	
	//	$t = new vcl_table();
	//	$prop = array("name" => "address_connection", "type" => "table", "vcl_inst" =>&$t);
	//	$i = get_instance(CL_REALESTATE_PROPERTY);
	//	$i->get_property(array("prop" => &$prop, "request" => $request));
	//	$t->sort_by();
	//	$html["address_connection"] = $t->draw();
	//	arr($html);
		return $html;
	}

	/** Generate a list of realestate objects added by user

		@attrib name=my_realestate_list is_public="1" caption="Minu kinnisvaraobjektid"

	**/
	function my_realestate_list($args)
	{
		enter_function ("realestate_add::list");
		$uid = aw_global_get("uid");
		$types = array(CL_REALESTATE_HOUSE, CL_REALESTATE_ROWHOUSE ,
				CL_REALESTATE_COTTAGE ,CL_REALESTATE_HOUSEPART ,
				CL_REALESTATE_APARTMENT , CL_REALESTATE_COMMERCIAL,
				CL_REALESTATE_GARAGE , CL_REALESTATE_LAND,
		);

		$all_objects = array();
		foreach($types as $type)
		{
			$obj_list = new object_list(array(
				"class_id" => $type,
				"createdby" => $uid,
				"brother_of" => new obj_predicate_prop("id"),
			));
			enter_function ("array_merge");
			$all_objects = array_merge($all_objects,$obj_list->arr());
			exit_function ("array_merge");
		}

		$trans_types = array(
				301 => t("Müük"),
				300 => t("Ost"),
				299 => t("Üürile anda"),
		);

		$tpl = "list.tpl";
		$this->read_template($tpl);
		lc_site_load("realestate", $this);
		$html = get_instance("html");
		if(sizeof($all_objects) == 0) $this->vars(array("nothing" => "Pakkumised puuduvad"));

		$has = $this->is_admin();
		if ($this->is_template("LIST"))
		{
			$c = "";
			foreach($all_objects as $key => $rlst_object)
			{
				$tyyp = $rlst_object->prop_str("transaction_type");
//				if($rlst_object->is_brother()) continue;//ignoreerib miskiseid brothereid
				if(is_oid($rlst_object->meta("added_from"))) $change = $rlst_object->meta("added_from")."?id=".$rlst_object->id();
				else $change = $this->mk_my_orb("parse_alias", array("id" => $rlst_object->id(), "default" => 1));
				$time = time();
				$expire = (int)(($rlst_object->prop("expire") - $time)/86400);
				if($rlst_object->prop("expire") - $time<1) $expire = t("Aegunud");
				else
				{
					if($expire < 30)$expire = t("Nähtav")." ".$expire." ".t("päeva");
					else $expire = t("Nähtav");
				}
				if(!$rlst_object->prop("expire")) $expire = t("Maksmata");
				if($expire == t("Maksmata")) $extend = "PAY";
				else $extend = "EXTEND";
				$u = "";$a_e = "";$make_invisible = "";$p="";
			//	if($this->is_template($expire))
			//	{
			//		$this->vars(array($expire => $this->parse($expire)));
			//	}
				if(($has || $tyyp == "Ost") && ($expire == t("Maksmata"))||(!$rlst_object->prop("is_visible")))$expire = t("Nähtamatu");
				if(!($has || $tyyp == "Ost"))
				{
					$e = "";$p = "";
					if($extend == "EXTEND" && $this->is_template("EXTEND"))
					{
						$this->vars(array("extend_popup" => $html->popup(array(
							"url" 	=> $this->mk_my_orb("extend", array("id" => $rlst_object->id())),
							"no_link" => 1,
						))));
						$e .= $this->parse("EXTEND");
					}
					if($extend == "PAY" && $this->is_template("PAY"))
					{
						$this->vars(array("extend_popup" => $html->popup(array(
							"url" 	=> $this->mk_my_orb("extend", array("id" => $rlst_object->id())),
							"no_link" => 1,
						))));
						$p .= $this->parse("PAY");
					}
				}
				else
				{
					if(!$rlst_object->prop("is_visible"))
					{
						$this->vars(array("extend_popup" => $html->popup(array(
							"url" 	=> $this->mk_my_orb("extend", array("id" => $rlst_object->id())),
							"no_link" => 1,
						))));
						$a_e .= $this->parse("ADMIN_EXTEND");
					}
				}

				if($rlst_object->prop("is_visible"))
				{
					$this->vars(array("invisible"	=>  $this->mk_my_orb("make_invisible", array("id" => $rlst_object->id())),));
					$make_invisible = $this->parse("MAKE_INVISIBLE");
				}
				$this->vars(array(
					"name" 	 	=> $rlst_object->name(),
					"id"	 	=> $rlst_object->id(),
					"change" 	=> $change,
					"extend_url"	=> $this->mk_my_orb("extend", array("id" => $rlst_object->id())),
					"expire"	=> $expire,
					"extend"	=> $extend,
					"extend_popup"	=> $html->popup(array(
						"url" 	=> $this->mk_my_orb("extend", array("id" => $rlst_object->id())),
						"no_link" => 1,
					)),
					"type"		=> $this->opt[$rlst_object->class_id()],
					"action"	=> $trans_types[$rlst_object->prop("transaction_type")],
					"delete"	=>  $this->mk_my_orb("delete_property", array("id" => $rlst_object->id())),
					"invisible"	=>  $this->mk_my_orb("make_invisible", array("id" => $rlst_object->id())),
					"PAY"		=> $p,
					"ADMIN_EXTEND"	=> $a_e,
					"EXTEND"	=> $e,
					"MAKE_INVISIBLE"=> $make_invisible,
/*					"regio"		=> $html->form(array(
						"action" => "http://www.regio.ee/?op=body&id=24",
						"method" => "POST",
						"name"	 => "sfa",
						"content" => $html->hidden(array("name" => "sfa", "value"=> $rlst_object->name())).$html->submit(array("name" => "regio", "value" => "regio")),
					)),*/
				));
				$c .= $this->parse("LIST");
			}
			$this->vars(array(
				"LIST" => $c,
			));
		}
		exit_function ("realestate_add::list");
		return $this->parse();
	}

	function gen_name()
	{
		$ret = "";
		$data = $_SESSION["realestate_input_data"];
		$ol = new object_list(array("oid" => $data));
		$names = $ol->names();
		if($names[$data["county"]])	$ret .= $names[$data["county"]];
		if($names[$data["city"]])	$ret .= ', '.$names[$data["city"]];
		if($names[$data["citypart"]])	$ret .= ', '.$names[$data["citypart"]];
		if($names[$data["vald"]])	$ret .= ', '.$names[$data["vald"]];
		if($names[$data["settlement"]])	$ret .= ', '.$names[$data["settlement"]];
		if($data["place_name"])		$ret .= ', '.$data["place_name"];
		if($data["street"])		$ret .= ', '.$data["street"];
		if($data["house"])	$ret .= ' '.$data["house"];
		if($data["apartment"])		$ret .= ' - '.$data["apartment"];
		return $ret;
	}

	function show($arr)
	{
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");
		lc_site_load("realestate", $this);
		$this->vars(array(
			"name" => $ob->prop("name"),
		));
		return $this->parse();
	}

	/** subscribe
		@attrib name=subscribe nologin="1"
		@param id required type=int
		@param rel_id required type=int
	**/
	function subscribe($args = array())//tegeleb postitatud infoga
	{
		enter_function ("realestate_add::subscribe");
		$level = $_SESSION["realestate_input_data"]["level"];

		if(!$_SESSION["realestate_input_data"]["realestate_id"])
		{
			$clss = aw_ini_get("classes");
			$class_entry = $clss[$args["clid"]];
			$parent = $class_entry["parents"];
			$manager = get_instance(CL_REALESTATE_MANAGER);
			$realestate_obj_id = $manager->add_property(array(
				"manager"	=> $args["parent"],
				"type"		=> $args["type"],
				"section" 	=> aw_global_get("section"),
			));
			$realestate_obj = obj($realestate_obj_id);
			$realestate_obj->set_name($_SESSION["realestate_input_data"]["name"]);
			$_SESSION["realestate_input_data"]["realestate_id"] = $realestate_obj_id;
			$realestate_obj->set_meta("added_from" ,aw_global_get("section"));
//			$realestate_obj->set_prop("show_on_webpage" , "0");//muidu tahab sinna 1 tekkida
			$realestate_obj->set_prop("is_visible" , "0");
		}
		else
		{
			$realestate_obj = obj($_SESSION["realestate_input_data"]["realestate_id"]);
		}
		$uid = aw_global_get("uid_oid");
		if(is_oid($uid))
		{
			$realestate_obj->connect(array(
				"to" => $uid,
				"reltype" => "RELTYPE_REALESTATE_AGENT",
			));
			$realestate_obj->set_prop("realestate_agent1" , $uid);
		}

		$pictures = new object_list($realestate_obj->connections_from (array (
			"type" => "RELTYPE_REALESTATE_PICTURE",
			"class_id" => CL_IMAGE,
		)));
		$existing_pics = array();
		foreach($pictures->arr() as $pic)
		{
			$existing_pics[] = $pic->id();
		}
		if($realestate_obj->meta("pic_order"))
		{
			$tmp_existing_pics = array();
			foreach($realestate_obj->meta("pic_order") as $ordered_pic)
			{
				if(in_array($ordered_pic , $existing_pics))
				{
					$tmp_existing_pics[] = $ordered_pic;
				}
			}
			foreach($existing_pics as $existing_pic)
			{
				if(!in_array($existing_pic , $tmp_existing_pics))
				{
					$tmp_existing_pics[] = $existing_pic;
				}
			}
			$existing_pics = $tmp_existing_pics;
		}

		if(is_array($args["delete"]))//kustutab pildiseosed
		{
			foreach($args["delete"] as $val)
			{
				if(is_oid($val))
				{
					$realestate_obj->disconnect(array(
						"from" => $val,
						"reltype" => "RELTYPE_REALESTATE_PICTURE",
					));
				}
			}
		}

		$x = 0;
		while($x<10)
		{
			if(array_key_exists("picture".$x."upload" , $_FILES))
			{
				$image_inst = get_instance(CL_IMAGE);
				$upload_image = $image_inst->add_upload_image("picture".$x."upload", $_SESSION["realestate_input_data"]["realestate_id"]);
				// if there is image uploaded:
				if ($upload_image !== false)
				{
					$realestate_obj->connect(array(
						"to" => $upload_image['id'],
						"reltype" => "RELTYPE_REALESTATE_PICTURE",
					));
					if($x == 0)
					{
						$this->make_icon(array(
							"upload_image" => "picture".$x."upload",
							"realestate_obj" => &$realestate_obj,
						));
					}
					if(is_oid($existing_pics[$x]))
					{
						$realestate_obj->disconnect(array(
							"from" => $existing_pics[$x],
							"reltype" => "RELTYPE_REALESTATE_PICTURE",
						));

					}
					$existing_pics[$x] = $upload_image['id'];
				}
			}
			$x++;
		}
		$realestate_obj->set_meta("pic_order" , $existing_pics);
		$unwanted_props = array("is_visible");//mida pole hea mõtet sessiooni panna ega salvestada
		$has = $this->is_admin();
		$tyyp = $realestate_obj->prop_str("transaction_type");
		if($has || $tyyp == "Ost")
		{
			$unwanted_props = array();
		}
		else
		{
			$unwanted_props = array("is_visible");
		}
		$args["is_visible"] = (int)$args["is_visible"];
		foreach($args as $key => $val)
		{
			if(!(in_array($key , $unwanted_props))) $_SESSION["realestate_input_data"][$key] = $val;
		};
		$props = $realestate_obj->get_property_list();
		$address_props = $this->get_address_props($args["parent"]);
		$address = $realestate_obj->get_first_obj_by_reltype("RELTYPE_REALESTATE_ADDRESS");
		if(!is_object($address))
		{
			$address = new object();
			$address->set_class_id(CL_ADDRESS);
			$address->set_parent($realestate_obj->id());
			$address->save();
			$realestate_obj->connect(array(
				"to" => $address,
				"reltype" => "RELTYPE_REALESTATE_ADDRESS",
			));
		}

		$_SESSION["realestate_input_data"]["name"] = $this->gen_name();
		$picture_icon = $realestate_obj->prop("picture_icon");//ei tea miks, a järgmise tsükliga kaob miskipärast ära see property väärtus
		foreach($_SESSION["realestate_input_data"] as $key => $val)
		{
			if(array_key_exists($key , $props))
			{
				$realestate_obj->set_prop($key, $val);
			}
			//aadressi salvestamine - tõsine porno
			if((array_key_exists($key , $address_props)) && (is_object($address)))
			{
				if(($key == "house") || ($key == "apartment"))
				{
					$address->set_prop ($key, $val);
				}
				else
				{
					if($key == "place_name")
					{
						//kohanimi lisatakse asulate hulka
						if(strlen($val)>1)
						{
							$address->set_prop ("unit_name", array (
								"division" => $address_props[$key],
								"name" => $val,
							));
							$_SESSION["realestate_input_data"]["settlement"] = $val;
							$_SESSION["realestate_input_data"]["place_name"] = null;
						}
					}
					else
					{
						if(is_oid($val))
						{
							$adr_obj = obj($val);
							$val = $adr_obj->name();
						}
						$address->set_prop ("unit_name", array (
							"division" => $address_props[$key],
							"name" => $val,
						));
					}
				}
			$address->save();
			}
		}
		$realestate_obj->set_prop("picture_icon" , $picture_icon);
		$realestate_obj->save();
		$main_obj = obj($args["id"]);
		exit_function ("realestate_add::subscribe");
		if($args["default"])
		{
			return $args["return_to"];
		}
		if($args["do"] == "submit" )
		{
			return aw_ini_get("baseurl")."/".$args["section"]."?end=1";
//			return aw_ini_get("baseurl")."/".$main_obj->prop("redir_object");
		}
		else
		{
			return aw_url_change_var("level", $level , aw_url_change_var("id", null , $args["return_to"]));
		}
	}

	function make_icon($args)
	{
		extract($args);
		$image_inst = get_instance(CL_IMAGE);
		$upload_image = $image_inst->add_upload_image("picture0upload", $_SESSION["realestate_input_data"]["realestate_id"]);
		$o = obj($upload_image["id"]);
		$o->img = get_instance("core/converters/image_convert");
		$o->img->load_from_file($o->prop("file"));
		$o->img->resize_simple(100,(int)($upload_image["sz"][1]/($upload_image["sz"][0]/100)));
		$image_cl = get_instance(CL_IMAGE);
		$image_cl->put_file(array(
			'file' => $o->prop("file"),
			"content" => $o->img->get(IMAGE_JPEG)
		));
		$conns_from = $realestate_obj->connections_from(array (
			"type" => "RELTYPE_REALESTATE_PICTUREICON",
		));
		$ids = array();
		foreach($conns_from as $conn)
		{
			$ids[] = $conn->prop("to");
		}
		$realestate_obj->disconnect(array("from" => $ids));
		$realestate_obj->connect(array(
			"to" => $upload_image['id'],
			"reltype" => "RELTYPE_REALESTATE_PICTUREICON",
		));
		$realestate_obj->set_prop("picture_icon", $upload_image['id']);
		$realestate_obj->save();
	}

	/** delete
		@attrib name=delete_property
	**/
	function delete_property($args)
	{
		$uid = aw_global_get("uid");
		global $id;
		if($this->can("view", $id))
		{
			$property = obj($id);
			if($property->createdby() == $uid)$property->delete();
		}
		return $this->mk_my_orb("my_realestate_list", array());
	}

	/** extend offer
		@attrib name=make_invisible
	**/
	function make_invisible($args)
	{
		$uid = aw_global_get("uid");
		global $id;
		if(is_oid($id))
		{
			$property = obj($id);
			$property->set_prop("is_visible" , "0");
			if($property->createdby() == $uid) $property->save();
		}
		return $this->mk_my_orb("my_realestate_list", array());
	}

	/** extend offer
		@attrib name=extend
	**/
	function extend($args)
	{
		extract($args);
		global $id,$extend,$fast;
		$ret = "";
		if(!is_oid($id))
		{
			return "sellist pakkumist pole";
		}
		$has = $this->is_admin();
		if(is_oid($id))
		{
			$offer = obj($id);
			$tyyp = $offer->prop_str("transaction_type");
		}
		if($has || $tyyp == "Ost")//adminnidele ja maakleritele... nad ei pea maksma
		{
//			if($fast)//juhul kui just lisatud ja nagu kui kauaks väärtus on niikuinii olemas
//			{
				$offer = obj($id);
				$valid_for = $offer->prop("weeks_valid_for");
				$offer->set_prop("is_visible" , "1");
				$offer->set_prop("expire" , time() + 6048000000000000000000);
				$offer->save();
				$ret.= '<script language="javascript">
					window.opener.location.href="'.$this->mk_my_orb("my_realestate_list", array("die" => 1)).'";
					window.close();
					</script>';
				return $ret;
//			}
			$ret = '<FORM METHOD=POST action="">
				'.t("Mitu nädalat").'?
				<br><INPUT type="TEXT" NAME="extend" size="2" value="'.$extend.'">
				<BR><INPUT TYPE="submit" value="'.t("pikendan").'">
				</FORM>
				';
			if($extend)
			{
				$offer = obj($id);
				$offer->set_prop("weeks_valid_for",$extend);
				$valid_for = $extend;
				$offer->set_prop("is_visible" , "1");
				$offer->set_prop("expire" , time() + 604800 * $valid_for);
				$offer->save();

				$ret.= '<a href="javascript:window.close();" ';
				$ret.= 'onClick=window.opener.location.href="'.$this->mk_my_orb("my_realestate_list", array("die" => 1));
				$ret.= '"> '.t("Tagasi").' </a>';
			}
			die($ret);
		}

		$url = $this->mk_my_orb("extend", array(
			"id" => $id,
		), CL_REALESTATE_ADD);
		$offer = obj($id);
		if(is_oid($offer->meta("added_from"))) $targ = obj($offer->meta("added_from"));
		$list = new object_list($targ->connections_from (array (
			"class_id" => CL_REALESTATE_ADD,
		)));
		foreach($list->arr() as $mem)
		{
			$targ = $mem;
		}
		$ret = '<FORM METHOD=POST action="">
		'.t("Mitu nädalat").'?
		<br><INPUT type="TEXT" NAME="extend" size="2" value="'.$extend.'">
		<BR><INPUT TYPE="submit" value="'.t("pikendan").'">
		</FORM>
		';

		$ret_url = $this->mk_my_orb("parse_alias", array(), CL_REALESTATE_ADD);
		$ret_url = $ret_url.'&end=1';
		if($extend)
		{
			$offer->set_prop("weeks_valid_for", $extend);
			$offer->save();
			$bank_meta = $targ->meta("bank");
			$bank_meta["amount"] = $bank_meta["amount"]*$offer->prop("weeks_valid_for");
			$bank_meta["expl"] = $bank_meta["expl"].' ID='.$offer->id();
			$_SESSION["bank_payment"] = array(
				"data"		=> $bank_meta,
				"reference_nr"	=> $offer->id(),
			//	"test"		=> 1,
				"url" 		=> $ret_url,
				"cancel"	=> $ret_url,
			);
			$bank_payment = get_instance(CL_BANK_PAYMENT);
			$ret.= '<a href="javascript:window.close();" ';
			$ret.= 'onClick=window.opener.location.href="'.$bank_payment->mk_my_orb("pay_site", array("die" => 1));
			$ret.= '"> '.t("Maksma").' </a>';
		}
		else
		{
			;
		}
		die($ret);
	}

	/** Check expired objects
		@attrib name=expire is_public="1"
	**/
	function expire()
	{
		$classes = array(
			CL_REALESTATE_HOUSE => t("Maja"),
			CL_REALESTATE_ROWHOUSE => t("Ridaelamu"),
			CL_REALESTATE_COTTAGE => t("Suvila"),
			CL_REALESTATE_HOUSEPART => t("Majaosa"),
			CL_REALESTATE_APARTMENT => t("Korter"),
			CL_REALESTATE_COMMERCIAL => t("Äripind"),
			CL_REALESTATE_GARAGE => t("Garaaz"),
			CL_REALESTATE_LAND =>  t("Maa"),
		);
		foreach ($classes as $class => $val)
		{
			$realestate_list = new object_list(array(
				"class_id" => $class,
			));
			foreach($realestate_list->arr() as $mem)
			{
				$ret.= $mem->prop("is_visible");
				if(!$mem->prop("expire"))
				{
					$ret.= 'pole makstud - '.$mem->id().'<br>';
					if($mem->prop("is_visible"))
					{
						$mem->set_prop("is_visible" , "0");
						$mem->save();
					}
					continue;
				}
				if($mem->prop("expire") < time())
				{
					$ret.= 'aegunud - '.$mem->id().'<br>';
					if($mem->prop("is_visible"))
					{
						$mem->set_prop("is_visible" , "0");
						$mem->save();
					}
					continue;
				}
				if($mem->prop("expire") > time() && !($mem->prop("expire") > (time()+86400)))
				{
/*					if(!$mem->prop("show_on_webpage"))
					{
						$mem->set_prop("show_on_webpage" , "1");
						$mem->save();
					}
*/					$ret.= 'hakkab aeguma - '.$mem->id().'<br>';
 					$u = get_instance("users");
					$oid = $u->get_oid_for_uid($mem->prop("createdby"));
					if(is_oid($oid))
					{
						$user = obj($oid);
						if(!$user->prop("email")) continue;
					}
					$message = "Kuulutus hakkab aeguma.\n Uuesti leheküljel nähtavale tuua saab minnes aadressile:\n";
 					$message .= $this->mk_my_orb("extend", array("id" => $mem->id()));
 					$message .= " ja makstes uute nädalate eest";
 					$awm = get_instance("protocols/mail/aw_mail");
 					$awm->create_message(array(
 						"froma" => "",
 						"subject" => "kuulutuse aegumine",
 						"To" => $user->prop("email"),
 						//"Sender"=>"bounces@struktuur.ee",
 						"body" => $message,
					));
					$awm->gen_mail();
					}
				else
				{
					$ret.= 'nähtav veel üle 1 päeva - '.$mem->id().'<br>';
/*					if(!$mem->prop("show_on_webpage"))
					{
						$mem->set_prop("show_on_webpage" , "1");
						$mem->save();
					}
*/				}
			}
		}
		return $ret;
	}
}
?>
