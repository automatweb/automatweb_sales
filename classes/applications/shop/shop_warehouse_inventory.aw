<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/shop/shop_warehouse_inventory.aw,v 1.6 2009/04/02 08:22:24 robert Exp $
// shop_warehouse_inventory.aw - Inventuur 
/*

@classinfo syslog_type=ST_SHOP_WAREHOUSE_INVENTORY relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo

@tableinfo shop_warehouse_inventory index=oid master_table=objects master_index=oid

@default table=objects
@default group=general

	@property date type=datetime_select table=shop_warehouse_inventory
	@caption Kuup&auml;ev

	@property warehouse type=relpicker reltype=RELTYPE_WAREHOUSE table=shop_warehouse_inventory multiple=1 store=connect
	@caption Ladu

	@property confirmed type=checkbox ch_value=1 table=shop_warehouse_inventory field=aw_confirmed
	@caption Kinnitatud

@groupinfo products caption="Tooted"
@default group=products

	@property toolbar type=toolbar no_caption=1
	@caption T&ouml;&ouml;riistariba

	@layout products_frame type=hbox width=20%:80%

		@layout product_search_params_frame type=vbox parent=products_frame closeable=1 area_caption=Otsing
			
			@property product_code type=textbox store=no parent=product_search_params_frame captionside=top size=30
			@caption Kood

			@property product_barcode type=textbox store=no parent=product_search_params_frame captionside=top size=30
			@caption Ribakood

			@property product_name type=textbox store=no parent=product_search_params_frame captionside=top size=30
			@caption Nimetus

			@property product_group type=select store=no parent=product_search_params_frame captionside=top
			@caption Tootegrupp

			@property product_sbmt type=submit store=no parent=product_search_params_frame captionside=top
			@caption Otsi

		@layout product_result_frame type=vbox parent=products_frame

			@property product_result type=table parent=product_result_frame no_caption=1

@reltype WAREHOUSE value=1 clid=CL_SHOP_WAREHOUSE
@caption Ladu
	
*/

class shop_warehouse_inventory extends class_base
{
	function shop_warehouse_inventory()
	{
		$this->init(array(
			"tpldir" => "applications/shop/shop_warehouse_inventory",
			"clid" => CL_SHOP_WAREHOUSE_INVENTORY
		));
	}

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
		$arr["dn_wh"] = 0;
	}

	function callback_mod_retval($arr)
	{
		$search_vars = array("product_group", "product_code", "product_barcode", "product_name");
		foreach($search_vars as $var)
		{
			if(isset($arr["request"][$var]))
			{
				$arr["args"][$var] = $arr["request"][$var];
			}
		}
	}

	function _get_toolbar($arr)
	{
		$t = &$arr['prop']['vcl_inst'];
		$t->add_menu_button(array(
			"name" => "add",
			"tooltip" => t("Lisa saateleht"),
			"img" => "new.gif",
		));
		foreach($arr["obj_inst"]->prop("warehouse") as $whid)
		{
			$who = obj($whid);
			$pt = $who->prop("conf.".(($var == "_export") ? "export_fld" : "reception_fld"));
			if(!$pt)
			{
				$pt = $arr["obj_inst"]->id();
			}
			$t->add_menu_item(array(
				"parent" => "add",
				"text" => sprintf(t("Saateleht: %s"), $who->name()),
				"link" => $this->mk_my_orb("new", array(
					"parent" => $pt,
					"return_url" => get_ru()
				), CL_SHOP_DELIVERY_NOTE)
			));
		}
		$t->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"tooltip" => t("Kustuta"),
			"action" => "_delete_objects",
			"confirm" => t("Oled kindel?"),
		));
		$t->add_save_button();
		$t->add_menu_button(array(
			"name" => "create_dn",
			"img" => "class_326.gif",
			"tooltip" => t("Loo mahakandmine valitud ridadega"),
		));
		foreach($arr["obj_inst"]->prop("warehouse") as $wh)
		{
			$who = obj($wh);
			$t->add_menu_item(array(
				"parent" => "create_dn",
				"text" => $who->name(),
				"onClick" => "changeform.dn_wh.value = $wh; submit_changeform('create_new_dn');",
				"link" => "#",
			));
		}
	}

	/**
	@attrib name=create_new_dn
	**/
	function create_new_dn($arr)
	{
		if(count($arr["sel"]))
		{
			$o = obj($arr["id"]);
			$amts = array();
			$prods = array();
			$singles = array();
			$wh_amts = $o->meta("wh_amounts");
			foreach($arr["sel"] as $oid)
			{
				if(!isset($arr["r_amounts"][$arr["dn_wh"]][$oid]))
				{
					continue;
				}
				$amts[$oid] = $wh_amts[$arr["dn_wh"]][$oid] - $arr["r_amounts"][$arr["dn_wh"]][$oid];
				$singles[] = $oid;
			}
			$url = html::get_new_url(CL_SHOP_DELIVERY_NOTE, $arr["id"], array("singles" => $singles, "amounts" => $amts, "from_warehouse" => $arr["dn_wh"], "writeoff" => 1, "return_url" => $arr["post_ru"]));
			return $url;
		}
		else
		{
			return $arr["post_ru"];
		}
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		switch($prop["name"])
		{
			case "product_group":
				$wh = $arr["obj_inst"]->prop("warehouse");
				if(is_array($wh) && count($wh))
				{
					$arr["warehouses"] = $wh;
					$arr["prop"]["options"] = get_instance(CL_SHOP_WAREHOUSE)->get_cat_picker($arr);
				}

			case "product_code":
			case "product_barcode":
			case "product_name":
				$prop["value"] = $arr["request"][$prop["name"]];
				break;
		}
	}

	function set_property($arr)
	{
		$prop = &$arr["prop"];
		
		$retval = PROP_OK;

		switch($prop["name"])
		{
			case "date":
				$ts = date_edit::get_timestamp($prop["value"]);
				if($ts > time())
				{
					$prop["error"] = t("Aeg ei saa olla tulevikus!");
					$retval = PROP_FATAL_ERROR;
				}
				elseif($ts != $arr["obj_inst"]->prop($prop["name"]))
				{
					$this->calc_wh_amounts($arr);
				}
				$arr["obj_inst"]->set_prop($prop["name"], $ts);
				break;
		}

		return $retval;
	}

	function _get_product_result($arr)
	{
		$t = &$arr['prop']['vcl_inst'];
		$t->define_field(array(
			"name" => "code",
			"caption" => t("Kood"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "barcode",
			"caption" => t("Ribakood"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimetus"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "type",
			"caption" => t("T&uuml;&uuml;p"),
			"align" => "center",
		));
		foreach($arr["obj_inst"]->prop("warehouse") as $whid)
		{
			$who = obj($whid);
			$t->define_field(array(
				"name" => "wh".$whid,
				"caption" => $who->name(),
			));
			$t->define_field(array(
				"name" => "warehouse_amount".$whid,
				"caption" => t("Kogus laos"),
				"align" => "center",
				"parent" => "wh".$whid,
			));
			$t->define_field(array(
				"name" => "real_amount".$whid,
				"caption" => t("Tegelik kogus"),
				"align" => "center",
				"parent" => "wh".$whid,
			));
		}
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid",
		));
		$wh = $arr["obj_inst"]->prop("warehouse");
		if(!is_array($wh) || !count($wh))
		{
			return;
		}
		$c = new connection;
		$conn = $c->find(array(
			"from.class_id" => CL_SHOP_PRODUCT,
			"to.class_id" => CL_SHOP_WAREHOUSE,
			"to.oid" => $arr["obj_inst"]->prop("warehouse"),
			"type" => "RELTYPE_WAREHOUSE",
		));
		$ids = array();
		foreach($conn as $co)
		{
			$ids[] = $co["from"];
		}
		$params = $arr["request"];
		$params["oid"] = $ids;
		$ol = $this->get_prod_ol($params);
		$wh_amts = $arr["obj_inst"]->meta("wh_amounts");
		$r_amts = $arr["obj_inst"]->meta("r_amounts");
		$types = get_instance(CL_SHOP_PRODUCT_SINGLE)->get_types();
		foreach ($ol->arr() as $prod)
		{
			$prodid = $prod->id();
			$data = array(
				"code" => html::obj_change_url($prod, ($c = $prod->prop("code"))?$c:t("(Kood puudub)")),
				"barcode" => $prod->prop("barcode"),
				"name" => $prod->prop("name"),
				"oid" => $prodid,
			);
			$clid = $prod->class_id();
			foreach($wh as $whid)
			{
				if(isset($wh_amts[$whid][$prodid]))
				{
					$data["warehouse_amount".$whid] = html::href(array(
						"caption" => $wh_amts[$whid][$prodid],
						"url" => "#",
						"title" => t("Tegelik kogus"),
						"onclick" => "ra = aw_get_el(\"r_amounts[".$whid."][".$prodid."]\"); ra.value = \"".$wh_amts[$whid][$prodid]."\";",
					));
					if($clid != CL_SHOP_PRODUCT || (!$prod->prop("serial_number_based") && !$prod->prop("order_based")))
					{
						$data["real_amount".$whid] = html::textbox(array(
							"name" => "r_amounts[$whid][$prodid]",
							"size" => 5,
							"value" => isset($r_amts[$whid][$prodid]) ? $r_amts[$whid][$prodid] : '',
						));
					}
				}
				else
				{
					$data["warehouse_amount".$whid] = t("Puudub");
				}
			}
			if($clid == CL_SHOP_PRODUCT)
			{
				$data["type"] = t("Toode");
			}
			elseif($clid == CL_SHOP_PRODUCT_SINGLE)
			{
				$data["type"] = $types[$prod->prop("type")];
			}
			$t->define_data($data);
		}
		$t->set_default_sortby("name");
		$t->set_default_sorder("asc");
		$t->sort_by();
	}

	function _set_product_result($arr)
	{
		$arr["obj_inst"]->set_meta("r_amounts", $arr["request"]["r_amounts"]);
		$arr["obj_inst"]->save();
	}

	private function get_prod_ol($arr)
	{
		if(isset($arr["oid"]))
		{
			if(count($arr["oid"]))
			{
				$params["oid"] = $arr["oid"];
			}
			else
			{
				return new object_list();
			}
		}
		if($c = $arr["product_code"])
		{
			$params["code"] = "%".$c."%";
		}
		if($bc = $arr["product_barcode"])
		{
			$params["barcode"] = "%".$bc."%";
		}
		if($n = $arr["product_name"])
		{
			$params["name"] = "%".$n."%";
		}
		if($gid = $arr["product_group"])
		{
			$oids = get_instance(CL_SHOP_WAREHOUSE)->get_art_cat_filter($gid);
			if($params["oid"])
			{
				$params["oid"] = array_intersect($oids, $params["oid"]);
			}
			else
			{
				$params["oid"] = $oids;
			}
		}
		if(!count($params["oid"]))
		{
			$params["oid"] = array(-1);
		}
		$params["site_id"] = array();
		$params["lang_id"] = array();
		$params["class_id"] = CL_SHOP_PRODUCT;
		$p_ol = new object_list($params);
		if($p_ol->count())
		{
			$s_ol = new object_list(array(
				"site_id" => array(),
				"lang_id" => array(),
				"class_id" => CL_SHOP_PRODUCT_SINGLE,
				"product" => $p_ol->ids(),
			));
			$p_ol->add($s_ol);
		}
		return $p_ol;
	}


	/**
		@attrib name=_delete_objects
	**/
	function _delete_objects($arr)
	{

		foreach ($arr['selected_ids'] as $id)
		{
			if (is_oid($id) && $this->can("delete", $id))
			{
				$object = new object($id);
				$object->delete();
			}
		}

		return $arr['post_ru'];
	}

	/**
		DB UPGRADE
	**/
	function do_db_upgrade($table, $field, $query, $error)
	{
		// this should be the way to detect, if table exist:
		if (empty($field))
		{
			$this->db_query('CREATE TABLE '.$table.' (oid INT PRIMARY KEY NOT NULL)');
			return true;
		}

		switch ($field)
		{
			case 'date':
			case 'aw_confirmed':
				$this->db_add_col($table, array(
					'name' => $field,
					'type' => 'int'
				));
				return true;
			case 'warehouse':
				$this->db_add_col($table, array(
					'name' => $field,
					'type' => 'varchar(255)'
				));
				return true;
		}

		return false;
	}

	function _get_warehouse($arr)
	{
		if (!empty($arr["request"]["warehouse"]))
		{
			$arr["prop"]["value"] = $arr["request"]["warehouse"];
			$arr["prop"]["options"][$arr["request"]["warehouse"]] = obj($arr["request"]["warehouse"])->name();
		}
	}

	private function calc_wh_amounts($arr)
	{
		$c = new connection;
		$conn = $c->find(array(
			"from.class_id" => CL_SHOP_PRODUCT,
			"to.class_id" => CL_SHOP_WAREHOUSE,
			"to.oid" => $arr["obj_inst"]->prop("warehouse"),
			"type" => "RELTYPE_WAREHOUSE",
		));
		$pi = get_instance(CL_SHOP_PRODUCT);
		$amounts = array();
		$d = date_edit::get_timestamp($arr["request"]["date"]);
		foreach($conn as $co)
		{
			$prodid = $co["from"];
			$prod = obj($prodid);
			$stypes = array();
			if($prod->prop("serial_number_based"))
			{
				$stypes[] = 2;
			}
			if($prod->prop("order_based"))
			{
				$stypes[] = 1;
			}
			$objs = array();
			if(count($stypes))
			{
				$s_ol = new object_list(array(
					"class_id" => CL_SHOP_PRODUCT_SINGLE,
					"product" => $prodid,
					"site_id" => array(),
					"lang_id" => array(),
				));
				foreach($s_ol->arr() as $so)
				{
					$objs[] = $so;
				}
			}
			else
			{
				$objs = array($prod);
			}
			foreach($arr["obj_inst"]->prop("warehouse") as $whid)
			{
				foreach($objs as $o)
				{
					$mv_ol = new object_list(array(
						"class_id" => CL_SHOP_WAREHOUSE_MOVEMENT,
						"product" => $prodid,
						"single" => ($o->class_id() == CL_SHOP_PRODUCT_SINGLE) ? $o->id() : array(), 
						"site_id" => array(),
						"lang_id" => array(),
						new object_list_filter(array(
							"logic" => "OR",
							"conditions" => array(
								"to_wh" => $whid,
								"from_wh" => $whid,
							),
						)),
						"created" => new obj_predicate_compare(OBJ_COMP_LESS_OR_EQ, $d),
					));
					if($mv_ol->count())
					{
						$amt = 0;
					}
					else
					{
						unset($amt);
					}
					foreach($mv_ol->arr() as $mvo)
					{
						if($mvo->prop("to_wh") == $whid)
						{
							$amt += $mvo->prop("amount");
						}
						elseif($mvo->prop("from_wh") == $whid)
						{
							$amt -= $mvo->prop("amount");
						}
					}
					if(isset($amt))
					{
						$amounts[$whid][$o->id()] = $amt;
						if(count($stypes))
						{
							$amounts[$whid][$prodid] += $amt;
						}
					}
				}
			}
		}
		$arr["obj_inst"]->set_meta("wh_amounts", $amounts);
		$arr["obj_inst"]->save();
	}
}
?>
