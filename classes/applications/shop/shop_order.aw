<?php
// shop_order.aw - Tellimus
/*

@classinfo syslog_type=ST_SHOP_ORDER relationmgr=yes no_status=1

@default table=objects
@default group=general

@property confirmed type=checkbox ch_value=1 table=aw_shop_orders field=confirmed
@caption Kinnitatud

@property orderer_person type=relpicker reltype=RELTYPE_PERSON table=aw_shop_orders field=aw_orderer_person
@caption Tellija esindaja

@property orderer_company type=relpicker reltype=RELTYPE_ORG table=aw_shop_orders field=aw_orderer_company
@caption Tellija

@property seller_person type=relpicker reltype=RELTYPE_PERSON table=objects field=meta method=serialize
@caption M&uuml;&uuml;ja esindaja

@property seller_company type=relpicker reltype=RELTYPE_ORG table=objects field=meta method=serialize
@caption M&uuml;&uuml;ja

@property oc type=relpicker reltype=RELTYPE_ORDER_CENTER table=aw_shop_orders field=aw_oc_id
@caption Tellimiskeskkond

@property warehouse type=relpicker reltype=RELTYPE_WAREHOUSE table=aw_shop_orders field=aw_warehouse_id
@caption Ladu

@tableinfo aw_shop_orders index=aw_oid master_table=objects master_index=brother_of

@groupinfo items caption="Tellimuse sisu"

@property items_toolbar type=toolbar group=items no_caption=1
@caption Tellimuste toolbar

@property items_orderer group=items type=text store=no
@caption Tellija andmed

@property items group=items field=meta method=serialize type=table
@caption Tellitud tooted

@property sum type=textbox table=aw_shop_orders field=aw_sum group=items
@caption Summa

@property pickup type=text store=no
@caption Klient tuleb j&auml;rele

@reltype PRODUCT value=1 clid=CL_SHOP_PRODUCT,CL_SHOP_PACKET
@caption tellimuse toode

@reltype EXPORT value=2 clid=CL_SHOP_WAREHOUSE_EXPORT
@caption lao v&auml;ljaminek

@reltype PERSON value=3 clid=CL_CRM_PERSON
@caption tellija esindaja

@reltype ORG value=4 clid=CL_CRM_COMPANY
@caption tellija organisatsioon

@reltype WAREHOUSE value=5 clid=CL_SHOP_WAREHOUSE
@caption ladu

@reltype ORDER_CENTER value=6 clid=CL_SHOP_ORDER_CENTER
@caption tellimiskeskkond

@reltype ORDER_TABLE_LAYOUT value=7 clid=CL_SHOP_PRODUCT_TABLE_LAYOUT
@caption Tellimuste tabeli kujundus

@reltype ROW value=8 clid=CL_SHOP_ORDER_ROW
@caption Tellimuse rida
*/

class shop_order extends class_base
{
	var $order_item_data = array();

	function shop_order()
	{
		$this->init(array(
			"tpldir" => "applications/shop/shop_order",
			"clid" => CL_SHOP_ORDER
		));
	}

	function get_property($arr)
	{
		$data = &$arr["prop"];
		$retval = PROP_OK;
		switch($data["name"])
		{
			case "pickup":
				$ud = $arr["obj_inst"]->meta("user_data");
				$data["value"] = $ud["user7"]."  ".$ud["user8"].":00";
				break;

			case "items":
				$this->do_ord_table($arr);
				break;

			case "items_toolbar":
				$t = &$arr["prop"]["vcl_inst"];
				$t->add_button(array(
					"name" => "delete",
					"tooltip" => t("Eemalda tellimusest tooted"),
					"confirm" => t("Oled kindel, et soovitud valitud tooted tellimusest eemaldada?"),
					"action" => "remove_items",
					"img" => "delete.gif",
				));
				break;

			case "confirmed":
				if ($arr["obj_inst"]->prop("confirmed") == 1)
				{
					// can't unconfirm after confirmation
					return PROP_IGNORE;
				}
				break;

			case "sum":
				$data["value"] = $this->get_price($arr["obj_inst"]);
				break;

			case "items_orderer":
				// use the ordering form for the cart
				//$this->_disp_order_data($arr);
//				return PROP_OK;
				$data["value"] = "";
				if ($arr["obj_inst"]->prop("orderer_person"))
				{
					$po = obj($arr["obj_inst"]->prop("orderer_person"));
					$data["value"] = $po->name();
				}
				if ($arr["obj_inst"]->prop("orderer_company"))
				{
					$co = obj($arr["obj_inst"]->prop("orderer_company"));
					if ($data["value"] != "")
					{
						$data["value"] .= " / ";
					}
					$data["value"] .= $co->name();
				}
				// separator between the saved user data and the user data which comes from
				// order object
				$data['value'] .= "<br /><br />";
				$user_data = $arr['obj_inst']->meta("user_data");
				// so lets print out the orderer data
				// XXX maybe i should but it in a table property?
				$data['value'] .= sprintf(t("Kliendi nr: %s<br />"), $user_data['user1']);
				$data['value'] .= sprintf(t("Eesnimi:<b> %s</b><br />"), $user_data['user4']);
				$data['value'] .= sprintf(t("Perekonnanimi:<b> %s</b><br />"), $user_data['user2']);
//				$data['value'] .= sprintf("S&uuml;nnip&auml;ev: %s<br />", $user_data['user4']);
				$data['value'] .= sprintf(t("Aadress: <b>%s</b><br />"), $user_data['user5']);
				$data['value'] .= sprintf(t("Postiindeks: <b>%s</b><br />"), $user_data['user6']);
				$data['value'] .= sprintf(t("Linn: <b>%s</b><br />"), $user_data['user7']);
				$data['value'] .= sprintf(t("E-mail: <b>%s</b><br />"), $user_data['user8']);
				$data['value'] .= sprintf(t("Telefon kodus: <b>%s</b><br />"), $user_data['user17']);
				$data['value'] .= sprintf(t("Telefon t&ouml;&ouml;l: <b>%s</b><br />"), $user_data['user18']);
				$data['value'] .= sprintf(t("Mobiil: <b>%s</b><br />"), $user_data['user19']);
				$data['value'] .= sprintf(t("Isikukood: <b>%s</b><br />"), $user_data['user20']);

				$data['value'] .= sprintf(t("Tellimuse kuup&auml;ev: <b>%s</b><br />"), date("d.m.Y H:i" , $arr["obj_inst"]->modified()));
				$data['value'] .= sprintf(t("Tellimuse number: <b>%s</b><br />"), $arr["obj_inst"]->id());

				break;
		};
		return $retval;
	}

	function set_property($arr = array())
	{
		$data = &$arr["prop"];
		$retval = PROP_OK;
		switch($data["name"])
		{
			case "items":
				$this->save_ord_table($arr);
				break;

			case "confirmed":
				if ($arr["obj_inst"]->prop("confirmed") != 1 && $data["value"] == 1)
				{
					// confirm was clicked, do the actual add
					$this->do_confirm($arr["obj_inst"]);
				}
				break;

			case "sum":
				$data["value"] = $this->get_price($arr["obj_inst"]);
				break;
		}
		return $retval;
	}

	function callback_post_save($arr)
	{
		if ($arr["new"])
		{
			// check if the current user has an organization
			$us = get_instance(CL_USER);
			if (($p = $us->get_current_person()))
			{
				$arr["obj_inst"]->connect(array(
					"to" => $p,
					"reltype" => 3 // RELTYPE_PERSON
				));
				$arr["obj_inst"]->set_prop("orderer_person", $p);
			}

			if (($p = $us->get_current_company()))
			{
				$arr["obj_inst"]->connect(array(
					"to" => $p,
					"reltype" => 4 // RELTYPE_COMPANY
				));
				$arr["obj_inst"]->set_prop("orderer_company", $p);
			}
			$arr["obj_inst"]->save();
		}
	}

	function get_order_rows($obj)
	{
		$conn = $obj->connections_from(array(
			"type" => "RELTYPE_ROW",
		));
		if(count($conn))
		{
			foreach($conn as $c)
			{
				$row = $c->to();
				$res[$row->prop("prod")][$row->id()] = array(
					"items" => $row->prop("items"),
				);
			}
			return $res;
		}
		else
		{
			$old = $obj->meta("ord_item_data");
			if(count($old))
			{
				if($this->create_order_rows($obj, $old))
				{
					return $this->get_order_rows($obj, $old);
				}
				else
				{
					return array();
				}
			}
		}
		return false;
	}

	function create_order_rows($obj, $data)
	{
		$pi = get_instance(CL_SHOP_PRODUCT);
		$created = 0;
		foreach($data as $oid => $prod)
		{
			foreach($prod as $item)
			{
				if($this->can("view", $oid))
				{
					$po = obj($oid);
					$o = obj();
					$o->set_class_id(CL_SHOP_ORDER_ROW);
					$o->set_name($obj->name().t(" rida: ").$po->name());
					$o->set_prop("prod", $oid);
					$o->set_prop("items", $item["items"]);
					$o->set_prop("prod_name", $po->name());
					$o->set_prop("price", $pi->get_price($po));
					$o->set_parent($obj->id());
					$o->save();
					$obj->connect(array(
						"type" => "RELTYPE_ROW",
						"to" => $o->id(),
					));
					$created = 1;
				}
			}
		}
		if(!$created)
		{
			return false;
		}
		else
		{
			return true;
		}
	}

	function do_ord_table(&$arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		if(file_exists($this->site_template_dir."/add_script.tpl"))
		{
			$this->read_site_template("add_script.tpl", true);
			$t->table_header = $this->parse();
		}
		$pd = $arr["obj_inst"]->meta("ord_content");
		$pd_data = new aw_array($this->get_order_rows($arr["obj_inst"]));
//		$t->define_field(array(
//			"name" => "user2",
//			"caption" => t("Artikli kood"),
//			"chgbgcolor" => "color",
//		));
		$pd_data = $pd_data->get();
		foreach($arr["obj_inst"]->meta("ord_item_data") as $key => $val)
		{
			$pd_data[$key] = $val;

		}
		$item_data = $arr["obj_inst"]->meta("ord_item_data");

		$t->define_field(array(
			"name" => "name",
			"caption" => t("Toode"),
			"chgbgcolor" => "color",
		));
		$t->define_field(array(
			"name" => "count",
			"caption" => t("Tellitav kogus"),
			"align" => "center",
			"chgbgcolor" => "color",
		));
		$t->parse_xml_def("shop/shop_order_items");
		$matchers = array();
		foreach($t->get_defined_fields() as $row)
		{
			if($row["name"] == "name" || $row["name"] == "count")
			{
				continue;
			}
			$matchers[$row["name"]] = $row["name"];
		}

		$t->define_chooser(array(
			"field" => "id",
			"name" => "sel",
			"chgbgcolor" => "color",
		));
		$add_fields = array();
		$cfgform = get_instance(CL_CFGFORM);
		$conf = $arr["obj_inst"]->prop("confirmed") == 1;
		//$arr["obj_inst"]->set_prop("confirmed" , 0);$arr["obj_inst"]->save();
		$arrz = array("name", "comment", "status", "item_count", "item_type", "price", "must_order_num", "brother_of", "parent", "class_id", "lang_id" ,"period", "created", "modified", "periodic");
		foreach($pd_data as $id => $prod)
		{
			if(!is_oid($id) || !$this->can("view", $id))
			{
				$cont = 1;
				foreach($prod as $x => $data)
				{
					if($this->can("view", $x))
					{
						$row = obj($x);
						$t->define_data(array(
							"name" => $row->prop("prod_name"),
							"count" => $row->prop("items"),
							"id" => $id."_".$row->id(),
						));
					}
				}
			}
			if($cont)
			{
				continue;
			}
			$to = obj($id);
			$cfg_id = $to->meta("cfgform_id");
			if(is_oid($cfg_id) && $this->can("view", $cfg_id))
			{
				$props = $cfgform->get_props_from_cfgform(array("id" => $to->meta("cfgform_id")));
			}
			else
			{
				$to_i = $to->instance();
				$props = $to_i->load_defaults();
			}
			foreach($to->properties() as $key => $val)
			{
				if(!empty($val["value"]) && $val["type"] != "text" && !in_array($key, $arrz))
				{
					$add_fields[$key] = $props[$key]["caption"];
				}
			}
			$name = $to->name();

			if($this->can("edit", $id))
			{
				$name = html::get_change_url($id, array(), $name);
			}
			$prod_conn = reset($to->connections_to(array("from.class_id" => "CL_SHOP_PRODUCT")));
			if ($prod_conn)
			{
				$name = t("Pakend: ").$name.",  ".t("Toode: ").html::get_change_url($prod_conn->prop("from"), array(), $prod_conn->prop("from.name"));
			}
			$prod = new aw_array($prod);
			foreach($prod->get() as $x => $val)
			{
				$_tmp = array();
				$vals = array();
				$xtmp = $to->properties();
				foreach($add_fields as $field => $bs)
				{
					if($props[$field]["type"] == "classificator" && is_oid($xtmp[$field]) && $this->can("view", $xtmp[$field]))
					{
						$cls_obj = obj($xtmp[$field]);
						$vals[$field] = $cls_obj->name();
					}
					else
					{
						$vals[$field] = $xtmp[$field];
					}
				}
				foreach(safe_array($val) as $fl => $valx)
				{
					$vals[$fl] = html::textbox(array(
						"name" => "prod_data[$id][$x][$fl]",
						"value" => $valx,
						"size" => 10,
					));
					$_tmp[$fl] = $fl;
				}
				$leftover = array_diff($matchers, $_tmp);
				foreach($leftover as $key)
				{
					$vals[$key] = html::textbox(array(
						"name" => "prod_data[$id][$x][$key]",
						"size" => 10,
						"value" => $item_data[$id][$x][$key],
					));
				}

				if ($conf)
				{
					$cnt = $val["items"];
				}
				else
				{
					$cnt = html::textbox(array(
						"name" => "prod_data[$id][$x][items]",
						"value" => $val["items"],
						"size" => 5
					));
				}
				// forgive me for doing this hack :(( -- ahz
				foreach($vals as $key => $item)
				{
					if($key == "duedate" || $key == "tduedate" || $key == "bill")
					{
						$vals[$key] .= '<a href="javascript:void(0);" onClick="cal.select(changeform.prod_data_'.$id.'__'.$x.'__'.$key.'_,\'anchor'.$id.'\',\'dd/MM/yy\'); return false;" title="Vali kuupaev" name="anchor'.$id.'" id="anchor'.$id.'">vali</a>';
					}
					if($key == "tduedate")
					{
						$vals[$key] .= ' <a href="javascript:void(0);" onclick="document.changeform.prod_data_'.$id.'__'.$x.'__tduedate_.value = document.changeform.prod_data_'.$id.'__'.$x.'__duedate_.value">sama</a>';
					}
				}
				if($val["unsent"])
				{
					$vals["color"] = "#FFCCCC";
				}

				$t->define_data(array(
					"name" => $name,
					"count" => $cnt,
					"id" => $id."_".$x,
				) + $vals);
			}
		}

		//votab tarne taitmise ara ja asemele Taidetud tellimus/arve number
	//	$add_fields[t("Tarne t&auml;itmine")] = t("T&auml;idetud tellimus/arve number");

		foreach($add_fields as $key => $val)
		{
			$t->define_field(array(
				"name" => $key,
				"caption" => $val,
				"chgbgcolor" => "color",
			));
		}

		//teeb miski sorteerimise ka oma aranagemise jargi
		//	Kood, Toode (praegune Nimi), Uhik, Pakend (praegune Kogus), Tellitav kogus (senine Mitu - vaadata ka ule, kas ikka kuvatakse selle vaartust), Soovitav tarne taitmine (praegune Tarne taitmistahtaeg), Tarne taitmine/arve nr, Osaline tarne taitmine/tarnitud (praegune Tarne taitmine osaliselt), Osaline tarne taitmine/tarnimata (praegune Saatmata kogus), Eritoon, Varvikaart, Erihind (uus vali, nagu varvikaart, kuid mida kuvatakse ainult adminnis)
		$order = array("user2" , "name" , "uservar1" , "user1" , "count" , "duedate" , "bill" , "pduedate" , "unsent" , "special" , "colorcard" , "special_price");

		$tmp_defs = array();

		foreach($t->get_defined_fields() as  $def)
		{
			$tmp_defs[$def["name"]] = $def;
			$t->remove_field($def["name"]);
		}

	//	unset($t->rowdefs);

		foreach($order as $ord)
		{
			if($tmp_defs[$ord])
			{
				$t->define_field($tmp_defs[$ord]);
				unset($tmp_defs[$ord]);
			}
		}
		foreach($tmp_defs as $def)
		{
			$t->define_field($def);
		}
	}

	function save_ord_table(&$arr)
	{
		$meta = $arr["obj_inst"]->meta("ord_item_data");
		foreach($arr["request"]["prod_data"] as $prod => $stuff)
		{
			foreach($stuff as $id => $row)
			{
				if($this->can("view", $id))
				{
					$rowo = obj($id);
					if($rowo->is_property("items"))
					{
						$rowo->set_prop("items", $row["items"]);
						$rowo->save();
					}
				}
				foreach($row as $key => $val)
				{
					$meta[$prod][$id][$key] = $val;
				}
			}
		}
		$arr["obj_inst"]->set_meta("ord_item_data",$meta);

//		arr($meta);arr($arr["request"]["prod_data"]);
//		arr(array_merge_recursive($meta , $arr["request"]["prod_data"]));

		//$arr["obj_inst"]->set_meta("ord_content", $arr["request"]["pd"]);
	}

	function do_confirm($o)
	{
		if ($o->prop("confirmed") == 1)
		{
			// make sure we don't re-confirm orders
			return;
		}

		// create wh_export, add products to that and confirm THAT
		// how to find the folder where to create the export -
		// find the warehouse, from that get config, from that exp folder
		// find warehouse - order is connected to warehouse

		$parent = 0;

		$warehouse = obj($o->prop("warehouse"));

		$conf = obj($warehouse->prop("conf"));

		$parent = $conf->prop("export_fld");

		error::raise_if(!$parent, array(
			"id" => ERR_ORDER,
			"msg" => t("shop_order::do_confirm(): could not find parent folder for warehouse export!")
		));


		$e = obj();
		$e->set_class_id(CL_SHOP_WAREHOUSE_EXPORT);
		$e->set_parent($parent);
		$e->set_name(sprintf(t("Lao v&auml;ljaminek tellimuse %s p&otilde;hjal"), $o->name()));
		$e->set_meta("exp_content", $o->meta("ord_content"));
		$e->save();

		// go over all products in order
		foreach($o->connections_from(array("type" => 1)) as $c)
		{
			$e->connect(array(
				"to" => $c->prop("to"),
				"reltype" => 1 // RELTYPE_PRODUCT
			));
		}

		// also connect the export to warehouse
		$warehouse->connect(array(
			"to" => $e,
			"reltype" => 4 // RELTYPE_STORAGE_EXPORT
		));

		$o->connect(array(
			"to" => $e->id(),
			"reltype" => 2 // RELTYPE_EXPORT
		));

		$e->connect(array(
			"to" => $o->id(),
			"reltype" => 2 // RELTYPE_ORDER
		));

		// now, also confirm export
		$exp = get_instance(CL_SHOP_WAREHOUSE_EXPORT);
		$exp->do_confirm($e);

		$o->set_prop("confirmed", 1);
		$o->save();
	}

	function get_price($o)
	{
		$d = $this->get_order_rows($o);
		$sum = 0;
		foreach($d as $id => $prod)
		{
			$cont = 0;
			if(!is_oid($id) || !$this->can("view", $id))
			{
				$cont = 1;
				foreach($prod as $rowid => $data)
				{
					if($this->can("view", $rowid))
					{
						$row = obj($rowid);
						$sum += $row->prop("price")*$row->prop("items");
					}
				}
			}
			if($cont)
			{
				continue;
			}
			$it = obj($id);
			$inst = $it->instance();
			$price = $inst->get_price($it);
			$prod = new aw_array($prod);
			foreach($prod->get() as $x => $val)
			{
				$sum += $price * $val["items"];
			}
		}
		return number_format($sum, 2);
	}

	function start_order($warehouse, $oc = NULL)
	{
		$this->order_items = array();
		$this->order_warehouse = $warehouse;
		$this->order_center = $oc;
	}

	function add_item($arr)
	{
		extract($arr);
		if($item_data["items"] > 0)
		{
			$this->order_items[$iid][$it] = $item_data;
		}
	}

	/** returns order id
	**/
	function finish_order($params = array())
	{
		if (!$params["user_data"] && $_POST["user_data"])
		{
			$params["user_data"] = $_POST["user_data"];
		}
		extract($params);

		$wh = $this->order_warehouse->instance();

		$oi = obj();
		$oi->set_parent($wh->get_order_folder($this->order_warehouse));

		$oi->set_name(sprintf(t("Tellimus laost %s"), $this->order_warehouse->name()));
		$oi->set_class_id(CL_SHOP_ORDER);
		$oi->set_prop("warehouse", $this->order_warehouse->id());

		if ($params["user_data"])
		{
			$oi->set_meta("user_data", $params["user_data"]);
		}
		else
		{
			$oi->set_meta("user_data", $cart["user_data"]);
		}

		$oi->set_meta("discount", $params["discount"]);
		$oi->set_meta("prod_paging", $params["prod_paging"]);
		$oi->set_meta("postal_price", $params["postal_price"]);
		$oi->set_meta("payment", $params["payment"]);
		$oi->set_meta("payment_type", $params["payment_type"]);
		if ($this->order_center)
		{
			$oi->set_prop("oc", $this->order_center->id());
			$oi->set_meta("prod_group_by", $this->order_center->prop("mail_group_by"));
			$oi->set_meta("discount", $this->order_center->prop("web_discount"));
			$name_ctr = $this->order_center->get_first_obj_by_reltype("RELTYPE_ORDER_NAME_CTR");
			if ($name_ctr)
			{
				$ctr_i = $name_ctr->instance();
				$oi->set_name($ctr_i->eval_controller($name_ctr->id(), $oi, $this->order_items));
			}
		}
		$id = $oi->save();

		$oi->connect(array(
			"to" => $this->order_warehouse->id(),
			"reltype" => 5 // RELTYPE_WAREHOUSE
		));
		// also, warehouse -> order connection
		$this->order_warehouse->connect(array(
			"to" => $oi->id(),
			"reltype" => 5 // RELTYPE_ORDER
		));

		if ($this->order_center)
		{
			$oi->connect(array(
				"to" => $this->order_center->id(),
				"reltype" => 6 // RELTYPE_ORDER_CENTER
			));
		}



		// connect to current person/company
		if (!$pers_id)
		{
			$us = get_instance(CL_USER);
			$pers_id = $us->get_current_person();
		}

		if ($pers_id)
		{
			$oi->connect(array(
				"to" => $pers_id,
				"reltype" => 3 // RELTYPE_PERSON
			));
			$oi->set_prop("orderer_person", $pers_id);
			$p_o = obj($pers_id);
			$p_o->connect(array(
				"to" => $oi->id(),
				"reltype" => 20 // RELTYPE_ORDER
			));
		}

		if (!$com_id)
		{
			$com_id = $us->get_current_company();
		}

		if ($com_id)
		{
			$oi->connect(array(
				"to" => $com_id,
				"reltype" => 4 // RELTYPE_ORG
			));
			$oi->set_prop("orderer_company", $com_id);
			$p_o = obj($com_id);
			$p_o->connect(array(
				"to" => $oi->id(),
				"reltype" => 27 // RELTYPE_ORDER
			));
		}

		// seller, seller_company fro current user
		if (aw_global_get("uid") != "")
		{
			$us = get_instance(CL_USER);
			$c_com_id = $us->get_current_company();
			if ($wh->is_manager_co($this->order_warehouse, $c_com_id))
			{
				$oi->connect(array(
					"to" => $c_com_id,
					"reltype" => 4 // RELTYPE_ORG
				));
				$oi->set_prop("seller_company", $c_com_id);

				$c_per_id = $us->get_current_person();

				$oi->connect(array(
					"to" => $c_per_id,
					"reltype" => 3 // RELTYPE_PERSON
				));
				$oi->set_prop("seller_person", $c_per_id);
			}
		}


		// now, products
		$mp = array();
		$sum = 0;
		$rows = array();
		foreach($this->order_items as $iid => $quantx)
		{
			if(!is_oid($iid) || !$this->can("view", $iid))
			{
				continue;
			}
			$i_o = obj($iid);

			$si = __get_site_instance();
			if (method_exists($si, "handle_product_display"))
			{
				$si->handle_product_display($i_o);
			}

			$orig_count = $i_o->prop("item_count");
			$i_inst = $i_o->instance();
			$price = $i_inst->get_calc_price($i_o);
			$oi->connect(array(
				"to" => $iid,
				"reltype" => "RELTYPE_PRODUCT",
			));
			$quantx = new aw_array($quantx);
			foreach($quantx->get() as $x => $quant)
			{
				if (count($quant) < 1)
				{
					continue;
				}
				$mp[$iid] = $quant["items"];
				$sum += ($quant["items"] * $price);
				$this->order_items[$iid][$x]["unsent"] = $quant["items"];
				if ($i_o->is_property("item_count"))
				{
					$i_o->set_prop("item_count", $i_o->prop("item_count")-$quant["items"]);
				}
			}
			if ($i_o->prop("item_count") != $orig_count)
			{
				aw_disable_acl();
				$i_o->save();
				aw_restore_acl();
			}
		}
		$oi->set_meta('ord_item_data', $this->order_items);
		$oi->set_meta("ord_content", $mp);
		$oi->set_prop("sum", $sum);
		$oi->save();
		foreach($this->order_items as $iid => $quantx)
		{
			$i_o = obj($iid);
			$quantx = new aw_array($quantx);
			foreach($quantx->get() as $x => $quant)
			{
				$row = obj();
				/*$row->set_class_id(CL_SHOP_ORDER_ROW);
				$row->set_prop("items", $quant["items"]);
				$row->set_name($i_o->name());
				$row->set_prop("price", $price);
				$row->set_prop("prod", $iid);
				$row->set_parent($oi->id());
				$row->save();
				$oi->connect(array(
					"to" => $row->id(),
					"type" => "RELTYPE_ROW",
				));*/
			}
		}

		$email_subj = sprintf(t("Tellimus laost %s"), $this->order_warehouse->name());
		$mail_from_addr = "automatweb@automatweb.com";
		$mail_from_name = str_replace("http://", "", aw_ini_get("baseurl"));

		$oc_id = $this->order_warehouse->prop("order_center");

		// process delivery
		if ($this->can("view", $oc_id))
		{
			$oc = obj($oc_id);
		}
		else
		{
			$oc = $this->order_center;
		}

		if (is_object($oc) and $oc->prop("show_delivery") and $this->can("view", $oc->prop("delivery_exec_controller")))
		{
			$params["order_id"] = $oi->id();
			$ctrl = get_instance(CL_FORM_CONTROLLER);
			$delivery_vars = $ctrl->eval_controller($oc->prop("delivery_exec_controller"), $oc, &$params);
		}

		//
		if ($this->can("view", $oc_id))
		{
			$order_center = obj($oc_id);
			if (is_oid($order_center->prop("cart")) && $this->can("view", $order_center->prop("cart")))
			{
				$cart_o = obj($order_center->prop("cart"));
				if ($cart_o->prop("email_subj") != "")
				{
					$email_subj = $cart_o->prop("email_subj");
				}
			}
			if ($order_center->prop("mail_from_addr"))
			{
				$mail_from_addr = $order_center->prop("mail_from_addr");
			}
			if ($order_center->prop("mail_from_name"))
			{
				$mail_from_name = $order_center->prop("mail_from_name");
			}
		}
		if($this->order_center)
		{
			//kui pank ise teeb paringu tagasi, siis votab miski muu keeele milles maili saata, et jargnev siis selle vastu
			if($params["lang_id"])
			{
				$l = get_instance("languages");
				$_SESSION["ct_lang_id"] = $params["lang_id"];
				$_SESSION["ct_lang_lc"] = $params["lang_lc"];
				aw_global_set("ct_lang_lc", $_SESSION["ct_lang_lc"]);
				aw_global_set("ct_lang_id", $_SESSION["ct_lang_id"]);
			}
			$awm = get_instance("protocols/mail/aw_mail");

			$ud = $oi->meta("user_data");

			// also, if the warehouse has any e-mails, then generate html from the order and send it to those dudes
			$emails = $this->order_warehouse->connections_from(array("type" => "RELTYPE_EMAIL"));
			//echo "emails = ".dbg::dump($emails)." <br>";
			$at = $this->order_center->prop("send_attach");

	//echo "hier <br>";
			$html = "";
			if (($_el = $this->order_center->prop("mail_to_seller_in_el")))
			{
				//echo "_el = $_el <br>";
				$val = $ud[$_el];
				if (is_oid($val) && $this->can("view", $val))
				{
					$_tmp = obj($val);
					$val = $_tmp->comment();
				}
				if (is_email($val))
				{
					$html = $this->show(array(
						"id" => $oi->id(),
						"template" => $this->order_center->prop("mail_template") != "" ?  $this->order_center->prop("mail_template") : null
					));
	//echo "send to $val , from = $mail_from_addr , content = $html <br>";
					foreach(explode(",", $val) as $_to)
					{
						$awm->clean();
						$awm->create_message(array(
							"froma" => $mail_from_addr,
							"fromn" => $mail_from_name,
							"subject" => $email_subj,
							"to" => $_to,
							"body" => "see on html kiri",
						));
						$awm->htmlbodyattach(array(
							"data" => $this->_eval_buffer($html),
						));
						if (!$params["no_mail"])
						{
							$awm->gen_mail();
						}
						//echo "sent to $_to , from $mail_from_addr <br>";
					}
				}
			}

			if (count($emails) > 0)
			{
				// if send mails by grp el is set, do this crap different
				if ($this->order_center->prop("mails_sep_by_el"))
				{
					// go over prods, gather mail => prods relations and get html and do the whole mailing things
					$eml2prod = array();
					$__fld = $this->order_center->prop("mail_group_by");
					foreach($this->order_items as $iid => $quantx)
					{
						$_po = obj($iid);
						$eml2prod[$_po->prop($__fld)][] = $iid;
					}

					$to_send = array();
					foreach($eml2prod as $eml => $_prods)
					{
						if ($eml == "")
						{
							// make content for empty prods
							$_html = $this->show(array(
								"id" => $oi->id(),
								"show_only_prods_with_val" => $eml,
								"template" => $this->order_center->prop("mail_template") != "" ?  $this->order_center->prop("mail_template") : null
							));
							foreach($emails as $c)
							{
								$eml = $c->to();
								$to_send[$eml->prop("mail")] = array($_html, "");
							}
						}
						else
						{
							$_html = $this->show(array(
								"id" => $oi->id(),
								"show_only_prods_with_val" => $eml,
								"template" => $this->order_center->prop("mail_template") != "" ?  $this->order_center->prop("mail_template") : null
							));
							$eml_o = obj($eml);
							$to_send[$eml_o->comment()] = array($_html, $eml);
						}
					}

					foreach($to_send as $eml => $html)
					{
						$awm->clean();
						$awm->create_message(array(
							"froma" => $mail_from_addr,
							"fromn" => $mail_from_name,
							"subject" => $email_subj,
							"to" => $eml,
							"body" => "see on html kiri",
						));
						$awm->htmlbodyattach(array(
							"data" => $this->_eval_buffer($html[0]),
						));
						if($at == 1)
						{
							$vars = array(
								"id" => $oi->id(),
								"show_only_prods_with_val" => $html[1]
							);
							if(file_exists($this->site_template_dir."/show_attach.tpl"))
							{
								$vars["template"] = "show_attach.tpl";
							}
							$org = obj($c_com_id);
							$htmla = $this->show($vars);
							$awm->fattach(array(
								"contenttype" => "application/vnd.ms-excel",
								"name" => $oi->id()."_".date("dmy")."_".$org->name().".xls",
								"content" => $htmla,
							));
						}
						//strip_tags(str_replace("<br>", "\n",$html))
						if (!$params["no_mail"])
						{
							$awm->gen_mail();
						}
						//echo "sent to $eml , from $mail_from_addr <br>";
					}
				}
				else
				{
					if ($html == "")
					{
						$html = $this->show(array(
							"id" => $oi->id(),
							"template" => $this->order_center->prop("mail_template") != "" ?  $this->order_center->prop("mail_template") : null
						));
					}

					foreach($emails as $c)
					{
						$eml = $c->to();
						$awm->clean();
						$awm->create_message(array(
							"froma" => $mail_from_addr,
							"fromn" => $mail_from_name,
							"subject" => $email_subj,
							"to" => $eml->prop("mail"),
							"body" => "see on html kiri",
						));
						$awm->htmlbodyattach(array(
							"data" => $this->_eval_buffer($html),
						));
						if($at == 1)
						{
							$vars = array(
								"id" => $oi->id(),
							);
							if(file_exists($this->site_template_dir."/show_attach.tpl"))
							{
								$vars["template"] = "show_attach.tpl";
							}
							$org = obj($c_com_id);
							$htmla = $this->show($vars);
							$awm->fattach(array(
								"contenttype" => "application/vnd.ms-excel",
								"name" => $oi->id()."_".date("dmy")."_".$org->name().".xls",
								"content" => $htmla,
							));
						}
						//strip_tags(str_replace("<br>", "\n",$html))
						if (!$params["no_mail"])
						{
							$awm->gen_mail();
						}
						//echo "sent to ".$eml->prop("mail")." from $mail_from_addr <br>";
					}
				}
			}

			if (isset($delivery_vars["add_emails"]))
			{
				foreach ($delivery_vars["add_emails"] as $email)
				{
					$awm->clean();
					$awm->create_message(array(
						"froma" => $mail_from_addr,
						"fromn" => $mail_from_name,
						"subject" => $email_subj,
						"to" => $email,
						"body" => "see on html kiri",
					));
					$awm->htmlbodyattach(array(
						"data" => $this->_eval_buffer($html),
					));
					if($at == 1)
					{
						$vars = array(
							"id" => $oi->id(),
						);
						if(file_exists($this->site_template_dir."/show_attach.tpl"))
						{
							$vars["template"] = "show_attach.tpl";
						}
						$org = obj($c_com_id);
						$htmla = $this->show($vars);
						$awm->fattach(array(
							"contenttype" => "application/vnd.ms-excel",
							"name" => $oi->id()."_".date("dmy")."_".$org->name().".xls",
							"content" => $htmla,
						));
					}
					if (!$params["no_mail"])
					{
						$awm->gen_mail();
					}
				}
			}

			// if the order center has an e-mail element selected, send the order to that one as well
			// but using a different template
			//echo "mail to el = ".$this->order_center->prop("mail_to_el")." <br>";
			// this is one ugly mess and i don't really want to sort it out, so i'll just make
			// a backdoor for myself -- ahz
			if ((!$arr["no_send_mail"] && $this->order_center->prop("mail_to_el") != "" && ($_send_to = $ud[$this->order_center->prop("mail_to_el")]) != "") || ($this->order_center->prop("mail_to_client") == 1 && is_oid($pers_id) && $this->can("view", $pers_id)))
			{
				if ($this->order_center->prop("mail_cust_content") != "")
				{
					$html = nl2br($this->order_center->prop("mail_cust_content"));
				}
				else
				{
					$html = $this->show(array(
						"id" => $oi->id(),
						"template" => $this->order_center->prop("mail_template") != "" ?  $this->order_center->prop("mail_template") : "show_cust.tpl"
					));
				}

				//echo "sent to $_send_to content = $html <br>";
				if ($_send_to == "" && aw_global_get("uid") != "")
				{
					$uo = obj(aw_global_get("uid_oid"));
					$_send_to = $uo->prop("email");
				}
				$awm->clean();

				$awm->create_message(array(
					"froma" => $mail_from_addr,
					"fromn" => $mail_from_name,
					"subject" => $email_subj,
					"to" => $_send_to,
					"body" => strip_tags(str_replace("<br>", "\n",$html)),
				));
				$awm->htmlbodyattach(array(
					"data" => $this->_eval_buffer($html)
				));
				if (!$params["no_mail"])
				{
					$awm->gen_mail();
				}
			}
		} // if(this->order_center) end

		return $oi->id();
	}

	/**
		@attrib name=bank_return nologin=1
		@param id required type=int acl=view
	**/
	function bank_return($arr)
	{
		$o = obj($arr["id"]);
		$l = get_instance("languages");
		//arr($o->prop("lang_id")); arr($o->prop("lang_lc"));
		if($o->meta("lang_id"))
		{
			$_SESSION["ct_lang_id"] = $o->meta("lang_id");
			$_SESSION["ct_lang_lc"] = $o->meta("lang_lc");
			aw_global_set("ct_lang_lc", $_SESSION["ct_lang_lc"]);
			aw_global_set("ct_lang_id", $_SESSION["ct_lang_id"]);
		}

	//	$order_id = shop_order_cart::do_create_order_from_cart($oc, NULL,array("no_mail" => 1));
		$order_center = obj($o->prop("oc"));
		aw_disable_acl();
		$o->set_prop("confirmed" , 1);
		$o->save();
		aw_restore_acl();

		// process content packages
		if(is_oid($arr["id"]) && $this->can("view", $arr["id"]) && is_oid(aw_global_get("uid_oid")) && $this->can("view", aw_global_get("uid_oid")))
		{
			$ol = new object_list(
				array(
					"class_id" => CL_CONTENT_PACKAGE,
					"CL_CONTENT_PACKAGE.cp_sp(CL_SHOP_PRODUCT).RELTYPE_PRODUCT(CL_SHOP_ORDER).id" => $arr["id"],
					"lang_id" => array(),
					"site_id" => array(),
				),
				array(
					CL_CONTENT_PACKAGE => array("cp_ug"),
				)
			);
			$inst = get_instance(CL_CONTENT_PACKAGE);
			foreach($ol->ids() as $cp_oid)
			{
				$inst->add_subscriber(array(
					"user" => aw_global_get("uid_oid"),
					"content_package" => $cp_oid
				));
			}
		}

		// process delivery
		if (is_object($order_center) and $order_center->prop("show_delivery") and $this->can("view", $order_center->prop("delivery_exec_controller")))
		{
			$arr["order_id"] = $o->id();
			$arr["user_data"] = $o->meta("user_data");
			$arr["mail_sent"] = $o->meta("mail_sent");
			$ctrl = get_instance(CL_FORM_CONTROLLER);
			$delivery_vars = $ctrl->eval_controller($order_center->prop("delivery_exec_controller"), $order_center, $arr);
		}

		// send mail
		if(!$o->meta("mail_sent"))
		{
			aw_disable_acl();
			$o->set_meta("mail_sent" , 1);
			$o->save();
			aw_restore_acl();

			$email_subj = t("Tellimus laost ");
			$mail_from_addr = "automatweb@automatweb.com";
			$mail_from_name = str_replace("http://", "", aw_ini_get("baseurl"));


			if (is_oid($order_center->prop("cart")) && $this->can("view", $order_center->prop("cart")))
			{
				$cart_o = obj($order_center->prop("cart"));
				if ($cart_o->prop("email_subj") != "")
				{
					$email_subj = $cart_o->prop("email_subj");
				}
				if(is_oid($cart_o->prop("subject_handler")) && $this->can("view", $cart_o->prop("subject_handler")))
				{
					$ctr = get_instance(CL_FORM_CONTROLLER);
					$email_subj = $ctr->eval_controller_ref($cart_o->prop("subject_handler"), NULL, $cart_o, $o->id());
				}
			}
			if ($order_center->prop("mail_from_addr"))
			{
				$mail_from_addr = $order_center->prop("mail_from_addr");
			}
			if ($order_center->prop("mail_from_name"))
			{
				$mail_from_name = $order_center->prop("mail_from_name");
			}

			if($o->meta("user_data"))
			{
				$uta = $o->meta("user_data");
				$_send_to = $uta["user6"];
			}
			elseif($o->prop("orderer_person"))
			{
				$po = obj($o->prop("orderer_person"));
				if(is_oid($po->prop("email")))
				{
					$mo = obj($po->prop("email"));
					$_send_to = $mo->prop("mail");
				}
			}

			$awm = get_instance("protocols/mail/aw_mail");
	//		$awm->dbg = 1;
			$html = $this->show(array("id" => $o->id(), "template" => $order_center->prop("mail_template")));
			//if(aw_global_get("uid") == "struktuur"){arr($_send_to);arr($html);arr($o->meta("user_data"));die(); }
			$awm->create_message(array(
				"froma" => $mail_from_addr,
				"fromn" => $mail_from_name,
				"subject" => $email_subj,
				"to" => $_send_to,
				"body" => strip_tags(str_replace("<br>", "\n",$html)),
			));
			$awm->htmlbodyattach(array(
				"data" => $html
			));
			$awm->gen_mail();


			// also, if the warehouse has emails set, then send those
			$warehouse = $order_center->prop("warehouse");
			$wo = obj($warehouse);
			$emails = $wo->connections_from(array("type" => "RELTYPE_EMAIL"));
	//if (aw_global_get("uid") == "struktuur") { echo "emails = ".dbg::dump($emails)." <br>"; }
			if (count($emails) > 0)
			{
				// if send mails by grp el is set, do this crap different
				if ($order_center->prop("mails_sep_by_el"))
				{
					// go over prods, gather mail => prods relations and get html and do the whole mailing things
					$eml2prod = array();
					$__fld = $order_center->prop("mail_group_by");
					foreach($o->meta('ord_item_data') as $iid => $quantx)
					{
						$_po = obj($iid);
						$eml2prod[$_po->prop($__fld)][] = $iid;
					}
					$to_send = array();
					foreach($eml2prod as $eml => $_prods)
					{
						if ($eml == "")
						{
							// make content for empty prods
							$i = get_instance(CL_SHOP_ORDER);
							$_html = $i->show(array(
								"id" => $oi->id(),
								"show_only_prods_with_val" => $eml
							));
							foreach($emails as $c)
							{
								$eml = $c->to();
								$to_send[$eml->prop("mail")] = array($_html, "");
							}
						}
						else
						{
							$i = get_instance(CL_SHOP_ORDER);
							$_html = $i->show(array(
								"id" => $oi->id(),
								"show_only_prods_with_val" => $eml
							));
							$eml_o = obj($eml);
							$to_send[$eml_o->comment()] = array($_html, $eml);
						}
					}

					foreach($to_send as $eml => $html)
					{
						$awm->clean();
						$awm->create_message(array(
							"froma" => $mail_from_addr,
							"fromn" => $mail_from_name,
							"subject" => $email_subj,
							"to" => $eml,
							"body" => "see on html kiri",
						));
						$awm->htmlbodyattach(array(
							"data" => $html[0],
						));
						if($at == 1)
						{
							$vars = array(
								"id" => $oi->id(),
								"show_only_prods_with_val" => $html[1]
							);
							$i = get_instance(CL_SHOP_ORDER);
							if(file_exists($i->site_template_dir."/show_attach.tpl"))
							{
								$vars["template"] = "show_attach.tpl";
							}
							$org = obj($c_com_id);
							$htmla = $i->show($vars);
							$awm->fattach(array(
								"contenttype" => "application/vnd.ms-excel",
								"name" => $oi->id()."_".date("dmy")."_".$org->name().".xls",
								"content" => $htmla,
							));
						}
	//                                         if (aw_global_get("uid") == "struktuur") echo strip_tags(str_replace("<br>", "\n",$html))." <br>";
						$awm->gen_mail();
	//if (aw_global_get("uid") == "struktuur") echo "sent to $eml , from $mail_from_addr <br>";
					}
				}
				else
				{
					if ($html == "")
					{
						$html = $i->show(array(
							"id" => $oi->id()
						));
					}

					foreach($emails as $c)
					{
						$eml = $c->to();
						$awm->clean();
						$awm->create_message(array(
							"froma" => $mail_from_addr,
							"fromn" => $mail_from_name,
							"subject" => $email_subj,
							"to" => $eml->prop("mail"),
							"body" => "see on html kiri",
						));
						$awm->htmlbodyattach(array(
							"data" => $html,
						));
						if($at == 1)
						{
							$vars = array(
								"id" => $oi->id(),
							);
							$i = get_instance(CL_SHOP_ORDER);
							if(file_exists($i->site_template_dir."/show_attach.tpl"))
							{
								$vars["template"] = "show_attach.tpl";
							}
							$org = obj($c_com_id);
							$htmla = $i->show($vars);
							$awm->fattach(array(
								"contenttype" => "application/vnd.ms-excel",
								"name" => $oi->id()."_".date("dmy")."_".$org->name().".xls",
								"content" => $htmla,
							));
						}
//                                                if (aw_global_get("uid") == "struktuur") echo strip_tags(str_replace("<br>", "\n",$html))."<br>";
						$awm->gen_mail();
//                                                if (aw_global_get("uid") == "struktuur") echo "sent to ".$eml->prop("mail")." from $mail_from_addr <br>";
					}
				}
			}

			if (isset($delivery_vars["add_emails"]))
			{
				foreach ($delivery_vars["add_emails"] as $email)
				{
					$awm->clean();
					$awm->create_message(array(
						"froma" => $mail_from_addr,
						"fromn" => $mail_from_name,
						"subject" => $email_subj,
						"to" => $email,
						"body" => "see on html kiri",
					));
					$awm->htmlbodyattach(array(
						"data" => $html,
					));
					if($at == 1)
					{
						$vars = array(
							"id" => $oi->id(),
						);
						$i = get_instance(CL_SHOP_ORDER);
						if(file_exists($i->site_template_dir."/show_attach.tpl"))
						{
							$vars["template"] = "show_attach.tpl";
						}
						$org = obj($c_com_id);
						$htmla = $i->show($vars);
						$awm->fattach(array(
							"contenttype" => "application/vnd.ms-excel",
							"name" => $oi->id()."_".date("dmy")."_".$org->name().".xls",
							"content" => $htmla,
						));
					}
					$awm->gen_mail();
				}
			}

			aw_disable_acl();
//			$o->set_meta("mail_sent" , 1);
			$o->save();
			aw_restore_acl();
		}

		if($arr["do_not_die"])
		{
			return;
		}

		if(is_oid($o->meta("bank_payment_id")))
		{
			$p = obj($o->meta("bank_payment_id"));
			if($p->class_id() == CL_BANK_PAYMENT && $p->prop("bank_return_url"))
			{
				return $p->prop("bank_return_url");
			}
		}
		return $this->mk_my_orb("show", array("id" => $o->id()), "shop_order");
	}

	/** shows thes order

		@attrib name=show nologin="1"

		@param id required type=int acl=view
	**/
	function show($arr)
	{
		if (!$arr["template"])
		{
			$arr["template"] = "show.tpl";
		}
		$this->read_any_template($arr["template"]);
		lc_site_load("shop_order_cart", &$this);
		$o = obj($arr["id"]);
		$tp = $o->meta("ord_content");
		$ord_item_data = new aw_array($o->meta('ord_item_data'));



		// we need to sort the damn products based on their page values. if they are set of course. blech.
		// so go over prods, make sure all have page numbers and then sort by page numbers
		$prods = array();
		$pages = $o->meta("prod_paging");
		if (!is_array($pages))
		{
			$pages = array(1 => 1);
		}
		foreach($o->connections_from(array("type" => "RELTYPE_PRODUCT")) as $c)
		{
			$prod = $c->to();
			if (!$pages[$prod->id()])
			{
				$pages[$prod->id()] = max($pages);
			}
			$prods[] = $prod;
		}
		$this->__sp = $pages;
		usort($prods, array(&$this, "__prod_show_sort"));

		if ((($fld = $o->meta("prod_group_by")) != ""))
		{
			if (isset($arr["show_only_prods_with_val"]))
			{
				$__tmp = $arr["show_only_prods_with_val"];
				$ord_it_d = array();
				foreach($ord_item_data->get() as $k => $v)
				{
					$a = obj($k);
					if ($a->prop($fld) == $__tmp)
					{
						$ord_it_d[$k] = $v;
					}
				}
				$ord_item_data = new aw_array($ord_it_d);
			}
			else
			{
				// sort by that field
				$ord_it_d = $ord_item_data->get();
				$this->_sby_fld = $fld;
				uksort($ord_it_d, array(&$this, "__prod_show_sort_gpby"));
				$ord_item_data = new aw_array($ord_it_d);
			}
		}

		$p = "";
		$total = 0;
		$prev_fld_val = "";
		foreach($ord_item_data->get() as $id => $prodx)
		{
			if(!is_oid($id) || !$this->can("view", $id))
			{
				continue;
			}
			$prodx = new aw_array($prodx);
			$prod = obj($id);

			$si = __get_site_instance();
			if (method_exists($si, "handle_product_display"))
			{
				$si->handle_product_display($prod);
			}

			if ($fld != "")
			{
				$nv = $prod->prop_str($fld);
				if ($nv != $prev_fld_val)
				{
					$this->vars(array(
						"group" => $nv
					));
					$p .= $this->parse("GRP_SEP");
				}
				$prev_fld_val = $nv;
			}

			$inst = $prod->instance();
			$pr = $inst->get_calc_price($prod);
			if ($product_info = reset($prod->connections_to(array(
				"from.class_id" => CL_SHOP_PRODUCT,
			))))
			{
				$product_info = $product_info->from();
			}
			else
			{
				$product_info = $prod;
			}
			$product_info_i = $product_info->instance();
			$calc_price = $product_info_i->get_calc_price($product_info);
			foreach($prodx->get() as $x => $val)
			{
				//naitab ainult neid kus on miskit veel saatmata, kui nii on tahetud
				if($arr["unsent"] && !$val["unsent"])
				{
					continue;
				}

				for($i = 1; $i < 21; $i++)
				{
					$ui = $product_info->trans_get_val("user".$i);
					if ($i == 16 && aw_ini_get("site_id") == 139 && $product_info->prop("userch5"))
					{
						$ui = $prod->trans_get_val("user3");
					}
					$this->vars(array(
						'user'.$i => $ui,
						"packaging_user".$i => $prod->trans_get_val("user".$i),
						"packaging_uservar".$i => $prod->trans_get_val_str("uservar".$i)
					));
				}
				$cur_tot = $val["items"] * $calc_price;
				$prod_total += $cur_tot;
				$this->vars(array(
					"prod_name" => $product_info->trans_get_val("name"),
					"prod_price" => $product_info_i->get_price($product_info),
					"prod_tot_price" => number_format($cur_tot, 2)
				));
				foreach(safe_array($val) as $__nm => $__vl)
				{
					$this->vars(array(
						"order_data_".$__nm => $__vl
					));
					if ($__nm == "read_price")
					{
						$read_price_total += $val["items"] * str_replace(",", "", $__vl);
						$read_price_total_sum += str_replace(",", "", $__vl);
					}
				}
				$this->vars(array(
					"name" => $prod->trans_get_val("name"),
					"p_name" => ($product_info ? $product_info->trans_get_val("name") : $prod->trans_get_val("name")),
					"quant" => $val["items"],
					"price" => number_format($pr,2),
					"obj_price" => number_format($pr, 2),
					"obj_tot_price" => number_format(((int)($val["items"]) * $pr), 2),
					"order_data_color" => $val["color"],
					"order_data_size" => $val['size'],
					"order_data_price" => $val['price'],
					"logged" => (aw_global_get("uid") == "" ? "" : $this->parse("logged"))
				));
				$total += ($pr * $val["items"]);

				$p .= $this->parse("PROD");
			}
		}

		$this->vars(array(
			"print_link" => aw_url_change_var("print", 1),
			"read_price_total" => number_format($read_price_total, 2),
			"read_price_total_sum" => number_format($read_price_total_sum, 2)
		));
		$this->vars(array(
			"NOT_IN_PRINT" => (!$_GET["print"] ? $this->parse("NOT_IN_PRINT") : "")
		));
		$this->vars(array("order_date" => date("d.m.Y" , $o->created())));
		$objs = array();

		$oc = obj($o->prop("oc"));
		$oc_i = $oc->instance();

		// get person
		if ($o->prop("orderer_person"))
		{
			$po = obj($o->prop("orderer_person"));
			$this->vars(array(
				"person_name" => $po->trans_get_val("name"),
			));
			$objs["user_data_person_"] = $po;
		}
		else
		if (($pp = $oc->prop("data_form_person")))
		{
			$_ud = $o->meta("user_data");
			$this->vars(array(
				"person_name" => $ud[$pp],
			));
		}

		if ($o->prop("orderer_company") && $this->can("view", $o->prop("orderer_company")))
		{
			$co = obj($o->prop("orderer_company"));
			$this->vars(array(
				"company_name" => $co->trans_get_val("name"),
			));

			$objs["user_data_org_"] = $co;
		}
		else
		if (($pp = $oc->prop("data_form_company")))
		{
			$_ud = $o->meta("user_data");
			$this->vars(array(
				"person_name" => $ud[$pp],
			));
		}
		if (aw_global_get("uid") != "")
		{
			$vars = array();
			foreach($objs as $prefix => $obj)
			{
				$ops = $obj->properties();
				foreach($ops as $opk => $opv)
				{
					if($opk == "email_id" && is_oid($opv) && $this->can("view", $opv))
					{
						$ob = obj($opv);
						$vars[$prefix."email_value"] = $ob->trans_get_val("mail");
					}
					elseif($opk == "phone_id" && is_oid($opv) && $this->can("view", $opv))
					{
						$ob = obj($opv);
						$vars[$prefix."phone_value"] = $ob->trans_get_val("name");
					}
					elseif($opk == "contact" && is_oid($opv) && $this->can("view", $opv))
					{
						$ob = obj($opv);
						$vars[$prefix."address_value"] = $ob->trans_get_val("name");
					}

					$vars[$prefix.$opk] = $opv;
				}
			}
			$vars["logged"] = $this->parse("logged");
			$vars["username"] = aw_global_get("uid");
			$this->vars($vars);
		}

		$awa = new aw_array($o->meta("user_data"));

		$tmp_register_data_obj = obj();
		$tmp_register_data_obj->set_class_id(CL_REGISTER_DATA);
		$register_data_prop_info = $tmp_register_data_obj->get_property_list();

		foreach($awa->get() as $ud_k => $ud_v)
		{
			if (is_array($ud_v) && $ud_v["year"] != "")
			{
				$ud_v = $ud_v["day"].".".$ud_v["month"].".".$ud_v["year"];
			}
			if ($register_data_prop_info[$ud_k]['type'] == "classificator")
			{
				$ud_string = array();
				if(!is_array($ud_v))
				{
					$ud_v = array($ud_v);
				}
				foreach($ud_v as $ud_id)
				{
					if(is_oid($ud_id) && $this->can("view", $ud_id))
					{
						$ud_v_obj = obj($ud_id);
						$ud_string[]= $ud_v_obj->trans_get_val("name");
					}
				}
				if(sizeof($ud_string)) $ud_v = join("\n<br>" ,$ud_string);
			}
			$this->vars(array(
				"user_data_".$ud_k => $ud_v
			));
		}


		$pl = "";
		if ($this->is_template("PROD_LONG"))
		{
			$prev_page = NULL;
			foreach($prods as $prod)
			{
				$pb = "";
				if ($pages[$prod->id()] != $prev_page && $prev_page != NULL)
				{
					$pb = $this->parse("PAGE_BREAK");
				}
				$inst = $prod->instance();

				$this->vars(array(
					"prod_html" => $inst->do_draw_product(array(
						"prod" => $prod,
						"layout" => $oc_i->get_long_layout_for_prod(array(
							"soc" => $oc,
							"prod" => $prod
						)),
						"oc_obj" => $oc,
						"quantity" => $tp[$prod->id()],
					)),
					"PAGE_BREAK" => $pb
				));

				$pl .= $this->parse("PROD_LONG");
				$prev_page = $pages[$prod->id()];
			}
		}
		// sellers
		$hs = "";
		if (is_oid($o->prop("seller_company")) && $this->can("view", $o->prop("seller_company")))
		{
			$seller_comp = obj($o->prop("seller_company"));
			$seller_person = obj();
			if (is_oid($o->prop("seller_person")) && $this->can("view", $o->prop("seller_person")))
			{
				$seller_person = obj($o->prop("seller_person"));
			}
			$this->vars(array(
				"seller_company" => $seller_comp->name(),
				"seller_person" => $seller_person->name()
			));
			$hs = $this->parse("HAS_SELLER");
		}
		else
		{
			$hs = $this->parse("NO_SELLER");
		}

		$total += $o->meta("postal_price");

		$total_incl_disc = ($total - ($total * ($o->meta("discount") / 100.0)));

		if ($oc->prop("show_delivery") and $this->can("view", $oc->prop("delivery_show_controller")))
		{
			$ctrl = get_instance(CL_FORM_CONTROLLER);
			$arr["total_incl_disc"] = $total_incl_disc;
			$cart = array("user_data" => $o->meta("user_data"));
			$delivery_vars = $ctrl->eval_controller($oc->prop("delivery_show_controller"), $oc, $cart, $arr);
		}
		else
		{
			$delivery_vars = array();
		}

		$this->vars($delivery_vars + array(
			"HAS_SELLER" => $hs,
			"NO_SELLER" => "",
			"PROD" => $p,
			"PROD_LONG" => $pl,
			"total" => number_format($total,2),
			"prod_total" => number_format($prod_total,2),
			"total_incl_disc" => number_format($total_incl_disc,2),
			"id" => $o->id(),
			"order_pdf" => $this->mk_my_orb("gen_pdf", array("id" => $o->id())),
			"discount" => $o->meta("discount"),
			"discount_value" => number_format(($total * ($o->meta("discount") / 100.0)),2),
			"postal_price" => number_format($o->meta("postal_price"))
		));

		if (!$arr["is_pdf"])
		{
			$this->vars(array(
				"IS_NOT_PDF" => $this->parse("IS_NOT_PDF")
			));
		}

		$ll = $lln = "";
		if (aw_global_get("uid") != "")
		{
			$ll = $this->parse("logged");
		}
		else
		{
			$lln = $this->parse("not_logged");
		}

		$this->vars(array(
			"logged" => $ll,
			"not_logged" => $lln
		));

		if (($imp = aw_ini_get("otto.import")) && $o->meta("payment_type") == "rent" && $this->is_template("HAS_RENT"))
		{
			$i = obj($imp);
			$cl_pgs = $this->make_keys(explode(",", $i->prop("jm_clothes")));
			$ls_pgs = $this->make_keys(explode(",", $i->prop("jm_lasting")));
			$ft_pgs = $this->make_keys(explode(",", $i->prop("jm_furniture")));
			foreach($prods as $prod)
			{
				$quant = $tp[$prod->id()];

				$pr = $prod;
				if ($pr->class_id() == CL_SHOP_PRODUCT_PACKAGING)
				{
					$c = reset($pr->connections_to(array("from.class_id" => CL_SHOP_PRODUCT)));
					$pr = $c->from();
				}

				$product_info = reset($prod->connections_to(array(
					"from.class_id" => CL_SHOP_PRODUCT,
				)));

				if (is_object($product_info))
				{
					$product_info = $product_info->from();
				}

				if (!is_object($product_info))
				{
					$product_info = $prod;
				}

				for( $i=1; $i<21; $i++)
				{
					$ui = $product_info->prop("user".$i);
					$this->vars(array(
						'user'.$i => $ui,
						"packaging_user".$i => $prod->prop("user".$i),
						"packaging_uservar".$i => $prod->prop_str("uservar".$i)
					));
				}

				$product_info_i = $product_info->instance();
				$cur_tot = $tp[$prod->id()] * $product_info_i->get_calc_price($product_info);
				$prod_total += $cur_tot;
				$this->vars(array(
					"prod_name" => $product_info->name(),
					"prod_price" => $product_info_i->get_price($product_info),
					"prod_tot_price" => number_format($cur_tot, 2)
				));

				$_oid = $ord_item_data->get();
				foreach(safe_array($_oid[$prod->id()][0]) as $__nm => $__vl)
				{
					$this->vars(array(
						"order_data_".$__nm => $__vl
					));
				}

				$_pr = $inst->get_calc_price($prod);

				$this->vars(array(
					"name" => $prod->name(),
					"p_name" => ($product_info ? $product_info->name() : $prod->name()),
					"quant" => $tp[$prod->id()],
					"price" => number_format($_pr,2),
					"obj_tot_price" => number_format(((int)($tp[$prod->id()]) * $_pr), 2),
					'order_data_color' => $_oid[$prod->id()][0]['color'],
					'order_data_size' => $_oid[$prod->id()][0]['size'],
					'order_data_price' => $_oid[$prod->id()][0]['price'],
				));

				//$pr_price= ($_pr * $tp[$prod->id()]);

				$p .= $this->parse("PROD");

				if (get_class($inst) == "shop_product_packaging")
				{
					$pr_price = ($quant * $inst->get_prod_calc_price($prod));
				}
				else
				{
					$pr_price = ($quant * $inst->get_price($prod));
				}

				if ( $cl_pgs[$pr->parent()] || (!$ft_pgs[$pr->parent()] && !$ls_pgs[$pr->parent()]))
				{
					$cl_total += $pr_price;
					$cl_str .= $this->parse("PROD");
				}
				else
				if ($ft_pgs[$pr->parent()])
				{
					$ft_total += $pr_price;
					$ft_str .= $this->parse("PROD");
				}
				else
				if ($ls_pgs[$pr->parent()])
				{
					$ls_total += $pr_price;
					$ls_str .= $this->parse("PROD");
				}
			}

			$pmt = $o->meta("payment");
			$npc = max(2,$pmt["num_payments"]["clothes"]);
			$cl_payment = ($cl_total+($cl_total*($npc)*1.25/100))/($npc+1);
			$cl_tot_wr = ($cl_payment * ($npc+1));

			$ft_npc = max(2,$pmt["num_payments"]["furniture"]);
			$ft_first_payment = ($ft_total/5);
			$ft_payment = ($ft_total-$ft_first_payment+(($ft_total-$ft_first_payment)*$ft_npc*1.25/100))/($ft_npc+1);
			$ft_total_wr = $ft_payment * ($ft_npc+1) + $ft_first_payment;

			$ls_npc = max(2,$pmt["num_payments"]["last"]);
			$ls_payment = ($ls_total+($ls_total*($ls_npc)*1.25/100))/($ls_npc+1);
			$ls_total_wr = ($ls_payment * ($ls_npc+1));

			$this->vars(array(
				"PROD_RENT_CLOTHES" => $cl_str,
				"PROD_RENT_FURNITURE" => $ft_str,
				"PROD_RENT_LAST" => $ls_str,
				"total_clothes_price" => number_format($cl_total,2),
				"num_payments_clothes" => $npc+1,
				"payment_clothes" => number_format($cl_payment,2),
				"total_clothes_price_wr" => number_format($cl_tot_wr,2),
				"total_furniture_price" => number_format($ft_total,2),
				"first_payment_furniture" => number_format($ft_total/5,2),
				"num_payments_furniture" => $ft_npc+1,
				"payment_furniture" => number_format($ft_payment,2),
				"total_furniture_price_wr" => number_format($ft_total_wr,2),
				"total_last_price" => number_format($ls_total,2),
				"num_payments_last" => $ls_npc+1,
				"payment_last" => number_format($ls_payment,2),
				"total_last_price_wr" => number_format($ls_total_wr,2),
				"total_price_rent" => number_format($cl_tot_wr + $ft_total_wr + $ls_total_wr,2),
				"total_price_rent_w_pst" => number_format($cl_tot_wr + $ft_total_wr + $ls_total_wr + $o->meta("postal_price"),2),
				"postal_price" => number_format($o->meta("postal_price"))
			));
			if ($cl_tot_wr > 0)
			{
				$this->vars(array(
					"HAS_PROD_RENT_CLOTHES" => $this->parse("HAS_PROD_RENT_CLOTHES"),
				));
			}
			if ($ft_total_wr > 0)
			{
				$this->vars(array(
					"HAS_PROD_RENT_FURNITURE" => $this->parse("HAS_PROD_RENT_FURNITURE"),
				));
			}
			if ($ls_total_wr > 0)
			{
				$this->vars(array(
					"HAS_PROD_RENT_LAST" => $this->parse("HAS_PROD_RENT_LAST"),
				));
			}
			$this->vars(array(
				"HAS_RENT" => $this->parse("HAS_RENT")
			));
			$str = "";
		}
		else
		{
			$this->vars(array(
				"NO_RENT" => $this->parse("NO_RENT")
			));
		}

		//kontrollerist suvaliste muutujate muutmiseks
		//$form_ref seest saab info ja $entry sisse peab panema uue info - kontrolleris
		if ($this->can("view", $oc->prop("order_show_controller")))
		{
			$ctrl = get_instance(CL_FORM_CONTROLLER);
			$changed_vars = array();
			$asd = $ctrl->eval_controller($oc->prop("order_show_controller") , &$changed_vars, $this->vars);
		}
		else
		{
			$changed_vars = array();
		}
		$this->vars($changed_vars);

		return $this->parse();
	}

	function get_orderer($o)
	{
		$mb = $o->modifiedby();
		if (is_oid($o->prop("orderer_person")) && $this->can("view", $o->prop("orderer_person")))
		{
			$_person = obj($o->prop("orderer_person"));
			$mb = $_person->name();
		}

		if (is_oid($o->prop("orderer_company")) && $this->can("view", $o->prop("orderer_company")))
		{
			$_comp = obj($o->prop("orderer_company"));
			$mb .= " / ".$_comp->name();
		}

		return $mb;
	}

	function request_execute($o)
	{
		$vars = array(
			"id" => $o->id(),
		);
		if(file_exists($this->site_template_dir."/show_ordered.tpl"))
		{
			$vars["template"] = "show_ordered.tpl";
			$vars["unsent"] = $_GET["unsent"];
		}
		return $this->show($vars);
	}

	function get_items_from_order($ord)
	{
		return $ord->meta("ord_content");
	}

	/** generates a pdf from the order

		@attrib name=gen_pdf nologin="1"

		@param id required type=int acl=view
		@param html optional

	**/
	function gen_pdf($arr)
	{
		$o = obj($arr["id"]);
		if ($o->prop("oc"))
		{
			$oc_o = obj($o->prop("oc"));
			$arr["template"] = $oc_o->prop("pdf_template");

			$arr["is_pdf"] = 1;
			$html = $this->show($arr);

			/*if ($tpl != "")
			{
				$this->read_template($tpl);
				$this->vars(array(
					"content" => $html
				));
				$html = $this->parse();
			}*/
		}

		if ($arr["html"])
		{
			if ($arr["return"] == 1)
			{
				return $html;
			}
			die($html);
		}

		header("Content-type: application/pdf");
		$conv = get_instance("core/converters/html2pdf");
		die($conv->convert(array(
			"source" => $html
		)));
	}

	function __prod_show_sort($a, $b)
	{
		$a_pg = $this->__sp[$a->id()];
		$b_pg = $this->__sp[$b->id()];
		if ($a_pg == $b_pg)
		{
			return 0;
		}
		return ($a_pg > $b_pg ? -1 : 1);
	}


	/**
		@attrib name=remove_items

		@param id required type=int acl=view
		@param group optional
		@param return_url optional
		@param sel required
	**/
	function remove_items($arr)
	{
		$obj = obj($arr["id"]);
		$prp_data = $this->get_order_rows($obj);
		$prp_count = $obj->meta("ord_content");
		foreach(safe_array($arr["sel"]) as $sel)
		{
			list($sel, $x) = explode("_", $sel);
			unset($prp_data[$sel][$x]);
			unset($prp_count[$sel]);
			if(count($prp_data[$sel]) <= 0)
			{
				$obj->disconnect(array(
					"from" => $sel,
					"type" => "RELTYPE_PRODUCT",
					"errors" => false,
				));
				unset($prp_data[$sel]);
			}
			if($this->can("view", $x))
			{
				$row = obj($x);
				if($row->class_id() == CL_SHOP_ORDER_ROW)
				{
					$row->delete(true);
				}
			}
		}
		$obj->set_meta("ord_item_data", $prp_data);
		$obj->set_meta("ord_content", $prp_count);
		$obj->save();
		return html::get_change_url($arr["id"], array("group" => $arr["group"], "return_url" => $arr["return_url"]));
	}

	function __prod_show_sort_gpby($a, $b)
	{
		$a = obj($a);
		$b = obj($b);
		return strcmp($a->prop($this->_sby_fld), $b->prop($this->_sby_fld));
	}

	function _disp_order_data($arr)
	{
		$swh = get_instance(CL_SHOP_WAREHOUSE);
		$els = $swh->callback_get_order_current_form(array(
			"obj_inst" => obj($arr["obj_inst"]->prop("warehouse"))
		));
		$str = "";
		$ud = $arr["obj_inst"]->meta("user_data");
		foreach($els as $pn => $pd)
		{
			if ($pd["type"] == "classificator" || $pd["type"] == "chooser")
			{
				if ($this->can("view", $ud[$pn]))
				{
					$tmp = obj($ud[$pn]);
					$ud[$pn] = $tmp->name();
				}
			}
			$str .= $pd["caption"].": ".$ud[$pn]."<br>";
		}
		$arr["prop"]["value"] = $str;
	}

	function _eval_buffer($res)
	{
		if (strpos($res, "<?php") !== false)
		{
			ob_start();
			$tres = $res;
			$res = str_replace("<?xml", "&lt;?xml", $res);
			eval("?>".$res);
			$res = ob_get_contents();
			ob_end_clean();
			if (strpos($res, "syntax err") !== false)
			{
				return $res;
			}
		}
		return $res;
	}
}
