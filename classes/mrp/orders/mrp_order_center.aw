<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_MRP_ORDER_CENTER relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo
@tableinfo aw_mrp_order_center master_index=brother_of master_table=objects index=aw_oid

@default table=aw_mrp_order_center
@default group=gen_gen

	@property owner_co type=relpicker reltype=RELTYPE_OWNER field=aw_owner
	@caption Omanik

	@property mrp_workspace type=relpicker reltype=RELTYPE_MRP_WORKSPACE field=aw_mrp_workspace
	@caption Ressursihalduse t&ouml;&ouml;laud

	@property mail_template type=relpicker reltype=RELTYPE_MAIL_TEMPLATE field=aw_mail_template
	@caption Saadetud pakkumise alus

@default group=order_order

	@property order_tb type=toolbar no_caption=1 store=no

	@layout order_split type=hbox width=20%:80%

		@layout order_left type=hbox parent=order_split

			@layout order_left_top type=vbox parent=order_left closeable=1 area_caption=Filter

				@property order_filter_tree type=treeview store=no no_caption=1 parent=order_left_top


		@property order_list type=table store=no no_caption=1 parent=order_split

@default group=customer

	@property customer_tb type=toolbar no_caption=1 store=no

	@layout customer_split type=hbox width=20%:80%

		@layout customer_left type=hbox parent=customer_split

			@layout customer_left_top type=vbox parent=customer_left closeable=1 area_caption=Filter

				@property customer_filter_tree type=treeview store=no no_caption=1 parent=customer_left_top


		@property customer_list type=table store=no no_caption=1 parent=customer_split

@default group=pricelists

	@property pr_tb type=toolbar store=no no_caption=1

	@property pr_table type=table store=no no_caption=1

@default group=covers

	@property co_tb type=toolbar store=no no_caption=1

	@layout cover_split type=hbox width=20%:80%

		@layout cover_left type=vbox parent=cover_split 

			@layout cover_left_top type=vbox parent=cover_left closeable=1 area_caption=Kategooriad
	
				@property cover_tree_cats type=treeview store=no no_caption=1 parent=cover_left_top
	
			@layout cover_left_bottom type=vbox parent=cover_left closeable=1 area_caption=Millele&nbsp;kehtib
	
				@property cover_tree type=treeview store=no no_caption=1 parent=cover_left_bottom

	@property co_table type=table store=no no_caption=1 parent=cover_split

@default group=stats

	@layout stats_split type=hbox width=20%:80%

		@layout left_bit type=vbox parent=stats_split

			@layout stats_status_lay type=vbox parent=left_bit closeable=1 area_caption=Filtreeri&nbsp;staatuse&nbsp;j&auml;rgi

				@property stats_status type=treeview store=no no_caption=1 parent=stats_status_lay

			@layout stats_period_lay type=vbox parent=left_bit closeable=1 area_caption=Filtreeri&nbsp;perioodi&nbsp;j&auml;rgi
	
				@property stats_period type=treeview store=no no_caption=1 parent=stats_period_lay

			layout stats_coverg_lay type=vbox parent=left_bit closeable=1 area_caption=Filtreeri&nbsp;kattegrupi&nbsp;j&auml;rgi
	
				property stats_coverg type=treeview store=no no_caption=1 parent=stats_coverg_lay

			@layout stats_customer_lay type=vbox parent=left_bit closeable=1 area_caption=Filtreeri&nbsp;kliendi&nbsp;j&auml;rgi

				@property stats_customer type=treeview store=no no_caption=1 parent=stats_customer_lay

			@layout stats_unit_lay type=vbox parent=left_bit closeable=1 area_caption=Filtreeri&nbsp;&uuml;ksuse&nbsp;j&auml;rgi

				@property stats_unit type=treeview store=no no_caption=1 parent=stats_unit_lay


		@property stats_table type=table store=no no_caption=1 parent=stats_split


@groupinfo gen_gen caption="&Uuml;ldine" parent=general
@groupinfo pricelists caption="Hinnakirjad" submit=no parent=general
@groupinfo covers caption="Katted" submit=no parent=general


@groupinfo customer caption="Kliendid" submit=no

@groupinfo order caption="Tellimused"
	@groupinfo order_order caption="Tellimused" parent=order submit=no

@groupinfo stats caption="Aruanded" submit=no


@reltype OWNER value=1 clid=CL_CRM_COMPANY
@caption Omanik

@reltype MRP_WORKSPACE value=2 clid=CL_MRP_WORKSPACE
@caption ERP T&ouml;&ouml;laud

@reltype MRP_PRICELIST value=4 clid=CL_MRP_PRICELIST
@caption Hinnakiri

@reltype MRP_COVER value=5 clid=CL_MRP_ORDER_COVER,CL_MRP_ORDER_COVER_GROUP
@caption Kate

@reltype MAIL_TEMPLATE value=6 clid=CL_MRP_ORDER_SENT
@caption Saadetud pakkumise alus

*/

class mrp_order_center extends class_base
{
	const AW_CLID = 1518;

	function mrp_order_center()
	{
		$this->init(array(
			"tpldir" => "mrp/orders/mrp_order_center",
			"clid" => CL_MRP_ORDER_CENTER
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
		}

		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
		}

		return $retval;
	}

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
		$arr["apply"] = automatweb::$request->arg("apply");
	}

	function show($arr)
	{
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");
		$this->vars(array(
			"name" => $ob->prop("name"),
		));
		return $this->parse();
	}

	function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_mrp_order_center(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "aw_owner":
			case "aw_mrp_workspace":
			case "aw_mail_template":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				return true;
		}
	}

	function _get_order_filter_tree($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->add_item(0, array(
			"id" => "stat",	
			"name" => t("Staatused"),
			"url" => aw_url_change_var(array("stat" => null, "custmgr" => null, "slr" => null))
		));

		$odl = new object_data_list(array(
			"class_id" => CL_MRP_ORDER_PRINT,
			"lang_id" => array(),	
			"site_id" => array(),
			"workspace" => $arr["obj_inst"]->id()
		), array(
			CL_MRP_ORDER_PRINT => array(
				"state" => "state", 
				"orderer_person" => "orderer_person",
				"seller_person" => "seller_person"
			)
		));
		$cnts = array();
		$cnts_orderer = array();
		$cnts_seller = array();
		$cnts_orderer_stat = array();
		$cnts_seller_stat = array();

		foreach($odl->arr() as $oid => $d)
		{
			$cnts[$d["state"]] = isset($cnts[$d["state"]]) ? $cnts[$d["state"]]++ : 1;
			$cnts_orderer[$d["orderer_person"]] = isset($cnts_orderer[$d["orderer_person"]]) ? $cnts_orderer[$d["orderer_person"]]++ : 1;
			$cnts_seller[$d["seller_person"]] = isset($cnts_seller[$d["seller_person"]]) ? $cnts_seller[$d["seller_person"]]++ : 1;
			$cnts_orderer_stat[$d["orderer_person"]][$d["state"]] = isset($cnts_orderer_stat[$d["orderer_person"]][$d["state"]]) ? $cnts_orderer_stat[$d["orderer_person"]][$d["state"]]++ : 1;
			$cnts_seller_stat[$d["seller_person"]][$d["state"]] = isset($cnts_seller_stat[$d["seller_person"]][$d["state"]]) ? $cnts_seller_stat[$d["seller_person"]][$d["state"]]++ : 1;
		}

		foreach(mrp_order::get_state_list() as $idx => $stat)
		{
			$t->add_item("stat", array(
				"id" => "stat_".$idx,	
				"name" => $stat." (".((int)ifset($cnts, $idx)).")",
				"url" => aw_url_change_var(array("stat" => "stat_".$idx, "custmgr" => null, "slr" => null))
			));
		}

		$t->add_item(0, array(
			"id" => "slr",	
			"name" => t("Teostajapoolne kontakt"),
			"url" => aw_url_change_var(array("stat" => null, "custmgr" => null, "slr" => null))
		));
		foreach($arr["obj_inst"]->get_all_seller_contacts() as $id => $name)
		{
			$t->add_item("slr", array(
				"id" => "slr_".$id,	
				"name" => $name." (".((int)$cnts_seller[$id]).")",
				"url" => aw_url_change_var(array("stat" => null, "custmgr" => null, "slr" => $id))
			));

			foreach(mrp_order::get_state_list() as $idx => $stat)
			{
				$t->add_item("slr_".$id, array(
					"id" => "slr_".$id."_stat_".$idx,	
					"name" => $stat." (".((int)ifset($cnts_seller_stat, $id, $idx)).")",
					"url" => aw_url_change_var(array("stat" => "stat_".$idx, "custmgr" => null, "slr" => $id))
				));
			}
		}

		// list all customers
		$odl = new object_data_list(array(
			"class_id" => CL_MRP_ORDER_PRINT,
			"lang_id" => array(),
			"site_id" => array(),
			"workspace" => $arr["obj_inst"]->id()
		),
		array(
			CL_MRP_ORDER_PRINT => array(new obj_sql_func(OBJ_SQL_UNIQUE, "customer", "customer"))
		));

		$cust_ids = array();
		foreach($odl->arr() as $item)
		{
			$cust_ids[] = $item["customer"];
		}
		$ol = new object_list(array(
			"lang_id" => array(),
			"site_id" => array(),
			"oid" => $cust_ids
		));
		$custs = array();
		foreach($ol->names() as $id => $nm)
		{
			$tnm = strtoupper($nm);
			$custs[substr($tnm, 0, 1)][] = array($id, $nm);
		}

		$t->add_item(0, array(
			"id" => "cust",	
			"name" => t("Kliendid"),
			"url" => aw_url_change_var(array("stat" => null, "custmgr" => null, "slr" => null, "cust" => null))
		));

		foreach($custs as $char => $items)
		{
			$t->add_item("cust", array(
				"id" => "cust_".$char,	
				"name" => $char,
				"url" => aw_url_change_var(array("stat" => null, "custmgr" => null, "slr" => null, "cust" => null))
			));
			foreach($items as $cust_entry)
			{
				$t->add_item("cust_".$char, array(
					"id" => "cust_".$cust_entry[0],	
					"name" => $cust_entry[1],
					"url" => aw_url_change_var(array("stat" => null, "custmgr" => null, "slr" => null, "cust" => null))
				));
			}
		}		

		$stat = ifset($arr["request"], "stat");
		$cm = ifset($arr["request"], "custmgr");
		$slr = ifset($arr["request"], "slr");

		if ($slr && $stat)
		{
			$t->set_selected_item("slr_".$slr."_".$stat);
		}
		else
		if ($cm && $stat)
		{
			$t->set_selected_item("custmgr_".$cm."_".$stat);
		}
		else
		if ($slr)
		{
			$t->set_selected_item("slr_".$slr);
		}
		else
		if ($cm)
		{
			$t->set_selected_item("custmgr_".$cm);
		}
		else
		if ($stat)
		{
			$t->set_selected_item($stat);
		}
	}

	private function _init_order_list_table($t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "customer",
			"caption" => t("Klient"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "orderer_person",
			"caption" => t("Kliendi kontakt"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "seller_person",
			"caption" => t("Teostaja kontakt"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "state",
			"caption" => t("Staatus"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "price",
			"caption" => t("hind"),
			"align" => "right",
			"sortable" => 1,
			"numeric" => 1
		));
		$t->define_field(array(
			"name" => "when",
			"caption" => t("Loomise kp"),
			"align" => "center",
			"sortable" => 1,
			"numeric" => 1,
			"type" => "time",
			"format" => "d.m.Y H:i"
		));
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "id"
		));
	}

	function _get_order_list($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_order_list_table($t);

		$states = mrp_order::get_state_list();
		foreach($this->_get_orders_in_list($arr["request"], $t, $arr["obj_inst"]) as $order)
		{
			$t->define_data(array(
				"customer" => html::obj_change_url($order->customer),
				"orderer_person" => html::obj_change_url($order->orderer_person),
				"state" => $states[$order->state],
				"when" => $order->created,
				"name" => html::obj_change_url($order),
				"id" => $order->id,
				"price" => $order->final_price
			));
		}
	}

	private function _get_orders_in_list($r, $t, $o)
	{
		$filt = array(
			"class_id" => CL_MRP_ORDER_PRINT,
			"lang_id" => array(),
			"site_id" => array(),
			"workspace" => $o->id()
		);
		if (!empty($r["stat"]) && !empty($r["custmgr"]))
		{
			$filt["orderer_person"] = $r["custmgr"];
			list(, $tmp) = explode("_", $r["stat"]);
			$filt["state"] = $tmp;
			$sl = mrp_order::get_state_list();
			$t->set_caption(sprintf(t("Tellimused kliendipoolse kontaktiga %s ja staatusega %s"), obj($r["custmgr"])->name, $sl[$filt["state"]]));
		}
		else
		if (!empty($r["stat"]) && !empty($r["slr"]))
		{
			$filt["seller_person"] = $r["slr"];
			list(, $tmp) = explode("_", $r["stat"]);
			$filt["state"] = $tmp;
			$sl = mrp_order::get_state_list();
			$t->set_caption(sprintf(t("Tellimused teostajapoolse kontaktiga %s ja staatusega %s"), obj($r["slr"])->name, $sl[$filt["state"]]));
		}
		else
		if (!empty($r["stat"]))
		{
			list(, $tmp) = explode("_", $r["stat"]);
			$filt["state"] = $tmp;
			$sl = mrp_order::get_state_list();
			$t->set_caption(sprintf(t("Tellimused staatusega %s"), $sl[$filt["state"]]));
		}
		else
		if (!empty($r["custmgr"]))
		{
			$filt["orderer_person"] = $r["custmgr"];
			$t->set_caption(sprintf(t("Tellimused kliendipoolse kontaktiga %s"), obj($r["custmgr"])->name));
		}
		else
		if (!empty($r["slr"]))
		{
			$filt["seller_person"] = $r["slr"];
			$t->set_caption(sprintf(t("Tellimused teostajapoolse kontaktiga %s"), obj($r["slr"])->name));
		}
		else
		{
			$t->set_caption(t("Tellimused"));
		}
		$ol = new object_list($filt);
		return $ol->arr();
	}

	function _get_order_tb($arr)
	{
		$tb = $arr["prop"]["vcl_inst"];
		$tb->add_new_button(array(CL_MRP_ORDER_PRINT), $arr["obj_inst"]->id(), null, array("ws" => $arr["obj_inst"]->id()));
		$tb->add_delete_button();
	}

	function _get_customer_tb($arr)
	{
		$tmp = array(
			"prop" => $arr["prop"],
			"obj_inst" => $arr["obj_inst"]->owner_co(),
			"request" => $arr["request"]
		);
		get_instance("applications/crm/crm_company_cust_impl")->_get_my_customers_toolbar($tmp);
	}

	function _get_customer_filter_tree($arr)
	{
		$tmp = array(
			"prop" => $arr["prop"],
			"obj_inst" => $arr["obj_inst"]->owner_co(),
			"request" => $arr["request"]
		);
		get_instance("applications/crm/crm_company_cust_impl")->_get_customer_listing_tree($tmp);
	}

	function _get_customer_list($arr)
	{
		$tmp = array(
			"prop" => $arr["prop"],
			"obj_inst" => $arr["obj_inst"]->owner_co(),
			"request" => $arr["request"]
		);
		get_instance("applications/crm/crm_company_cust_impl")->_get_my_customers_table($tmp);
	}

	function _get_pr_tb($arr)
	{
		$arr["prop"]["vcl_inst"]->add_new_button(array(CL_MRP_PRICELIST, CL_SHOP_PRICE_LIST), $arr["obj_inst"]->id(), 4 /* MRP_PRICELIST */);
		$arr["prop"]["vcl_inst"]->add_delete_button();
	}

	function _get_pr_table($arr)
	{
		$ol = new object_list($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_MRP_PRICELIST")));
		$ol->add(new object_list(array("class_id" => CL_SHOP_PRICE_LIST, "lang_id" => array(), "site_id" => array())));

		$t = $arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "created",
			"caption" => t("Loodud"),
			"align" => "center",
			"sortable" => 1,
			"type" => "time",
			"format" => "d.m.Y H:i:s",
			"numeric" => 1
		));
		$t->define_field(array(
			"name" => "act_from",
			"caption" => t("Kehtib alates"),
			"align" => "center",
			"sortable" => 1,
			"type" => "time",
			"format" => "d.m.Y H:i:s",
			"numeric" => 1
		));
		$t->define_field(array(
			"name" => "act_to",
			"caption" => t("Kehtib kuni"),
			"align" => "center",
			"sortable" => 1,
			"type" => "time",
			"format" => "d.m.Y H:i:s",
			"numeric" => 1
		));
		$t->define_field(array(
			"name" => "class_id",
			"caption" => t("Hinnakirja t&uuml;&uuml;p"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid"
		));

		$clss = aw_ini_get("classes");
		foreach($ol->arr() as $o)
		{
			$t->define_data(array(
				"name" => html::obj_change_url($o),
				"oid" => $o->id(),
				"created" => $o->created(),
				"act_from" => $o->act_from,
				"act_to" => $o->act_to,
				"class_id" => $clss[$o->class_id]
			));
		}
	}

	function _get_co_tb($arr)
	{
		$pt = isset($arr["request"]["cov_cat"]) ? $arr["request"]["cov_cat"] : $arr["obj_inst"]->id();

		$tb =& $arr["prop"]["vcl_inst"];
		$tb->add_new_button(isset($arr["request"]["cov_cat"]) ? array(CL_MRP_ORDER_COVER,CL_MRP_ORDER_COVER_GROUP) : array(CL_MRP_ORDER_COVER_GROUP), $pt, 5);
		$tb->add_delete_button();

		$arr["prop"]["vcl_inst"]->add_delete_button();

		$arr["prop"]["vcl_inst"]->add_button(array(
			"name" => "copy",
			"img" => "copy.gif",
			"action" => "copy_covers",
			"tooltip" => t("Kopeeri")
		));

		$can_add = false;
		$apply = ifset($arr["request"], "apply");
		if (is_oid($apply))
		{
			$o = obj($apply);
			if ($o->class_id() != CL_MENU)
			{
				$can_add = true;
			}
		}
		else
		if ($apply == "general")
		{
			$can_add = true;
		}

		if (is_array($_SESSION["moc_copy"]) && count($_SESSION["moc_copy"]) && $can_add)
		{
			$arr["prop"]["vcl_inst"]->add_button(array(
				"name" => "paste",
				"img" => "paste.gif",
				"action" => "paste_covers",
				"tooltip" => t("Kleebi")
			));
		}
		return;

		if ($can_add)
		{
			if ($apply == "general")
			{
				$arr["prop"]["vcl_inst"]->add_new_button(array(CL_MRP_ORDER_COVER, CL_MRP_ORDER_COVER_GROUP), $arr["obj_inst"]->id(), 5 /* MRP_COVER */, array("apply" => $apply));
			}
			else
			{
				$arr["prop"]["vcl_inst"]->add_new_button(array(CL_MRP_ORDER_COVER), $arr["obj_inst"]->id(), 5 /* MRP_COVER */, array("apply" => $apply));
			}
		}
	}

	private function _init_co_table($t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"align" => "center"
		));
	}

	function _get_co_table($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_co_table($t);

		$apply = ifset($arr["request"], "apply");

		$filt = array(
			"class_id" => CL_MRP_ORDER_COVER,
			"lang_id" => array(),
			"site_id" => array(),
		);


		if (!empty($arr["request"]["cov_cat"]) || (empty($arr["request"]["cov_cat"]) && empty($arr["request"]["apply"])))
		{
			$t1 = t("Katted, mis on kaustas %s");
			if (empty($arr["request"]["cov_cat"]))
			{
				$filt["CL_MRP_ORDER_COVER.RELTYPE_MRP_COVER(CL_MRP_ORDER_CENTER)"] = $arr["obj_inst"]->id();
				$where = $arr["obj_inst"]->name();
			}
			else
			{
				$filt["CL_MRP_ORDER_COVER.RELTYPE_MRP_COVER(CL_MRP_ORDER_COVER_GROUP)"] = $arr["request"]["cov_cat"];
				$where = obj($arr["request"]["cov_cat"])->name();
			}
		}
		else
		{
			$t1 = t("Katted, mis kehtivad %s");
			$where = t("igalpool");

			if ($apply == "general")
			{
				$filt["applies_all"] = 1;
			}
			else
			if ($apply == "resource")
			{
				$filt["CL_MRP_ORDER_COVER.RELTYPE_APPLIES_RESOURCE.id"] = new obj_predicate_compare(OBJ_COMP_GREATER, 0);
				$where = t("ressurssidele");
			}
			else
			if ($apply == "prod")
			{
				$filt["CL_MRP_ORDER_COVER.RELTYPE_APPLIES_PROD.id"] = new obj_predicate_compare(OBJ_COMP_GREATER, 0);
				$where = t("materjalidele");
			}
			else
			if ($this->can("view", $apply))
			{
				$o = obj($apply);
				switch($o->class_id())
				{
					case CL_MRP_RESOURCE:
						$filt["CL_MRP_ORDER_COVER.RELTYPE_APPLIES_RESOURCE.id"] = $o->id();
						$where = sprintf(t("ressursile %s"), $o->name());
						break;

					case CL_SHOP_PRODUCT:
						$filt["CL_MRP_ORDER_COVER.RELTYPE_APPLIES_PROD.id"] = $o->id();
						$where = sprintf(t("materjalile %s"), $o->name());
						break;

					case CL_MRP_ORDER_COVER_GROUP:
						$filt["CL_MRP_ORDER_COVER.RELTYPE_APPLIES_GROUP.id"] = $o->id();
						$where = sprintf(t("grupis %s"), $o->name());
						break;

					default:
						$filt["lang_id"] = -1;
						break;
				}
			}
		}

		$ol = new object_list($filt);
		$arr["prop"]["vcl_inst"]->table_from_ol(
			$ol,
			array("name", "created"),
			CL_MRP_ORDER_COVER
		);


		$arr["prop"]["vcl_inst"]->set_caption(sprintf($t1, $where));
	}

	function _cover_count($name, $applies)
	{
		$filt = array(
			"class_id" => CL_MRP_ORDER_COVER,
			"lang_id" => array(),
			"site_id" => array()
		);

		if (is_oid($applies))
		{
			$o = obj($applies);
			if ($o->class_id() == CL_SHOP_PRODUCT)
			{
				$filt["CL_MRP_ORDER_COVER.RELTYPE_APPLIES_PROD"] = $applies;
			}
			else
			if ($o->class_id() == CL_MRP_RESOURCE)
			{
				$filt["CL_MRP_ORDER_COVER.RELTYPE_APPLIES_RESOURCE"] = $applies;
			}
			else
			if ($o->class_id() == CL_MENU)
			{
				$ot = new object_tree(array(
					"class_id" => array(CL_MENU, CL_SHOP_PRODUCT, CL_MRP_RESOURCE),
					"parent" => $o->id(),
					"lang_id" => array(),
					"site_id" => array()
				));
				if (!count($ot->ids()))
				{
					$filt["oid"] = -1;
				}
				else
				{
					$filt[] = new object_list_filter(array(
						"logic" => "OR",
						"conditions" => array(
							"CL_MRP_ORDER_COVER.RELTYPE_APPLIES_PROD" => $ot->ids(),
							"CL_MRP_ORDER_COVER.RELTYPE_APPLIES_RESOURCE" => $ot->ids(),
						)
					));
				}
			}
			else
			{
				$filt["oid"] = -1;
			}
		}
		else
		{
			$filt["applies_all"] = 1;
		}
		$ol = new object_list($filt);
		return $name." (".$ol->count().")";
	}

	function _get_cover_tree($arr)
	{
		$tv = $arr["prop"]["vcl_inst"];
		$tv->add_item(0, array(
			"id" => "general",
			"name" => $this->_cover_count(t("Kehtivad kogusummale"), "all"),
			"url" => aw_url_change_var(array("apply" => "general", "cov_cat" => null))
		));
	//	$this->_insert_folder_items($tv, $arr["obj_inst"], "general");
		$tv->add_item(0, array(
			"id" => "resource",
			"name" => t("Kehtivad resurssidele"),
			"url" => aw_url_change_var(array("apply" => "resource", "cov_cat" => null))
		));
		$this->_insert_resource_items($tv, $arr["obj_inst"], "resource");
		$tv->add_item(0, array(
			"id" => "prod",
			"name" => t("Kehtivad materjalidele"),
			"url" => aw_url_change_var(array("apply" => "prod", "cov_cat" => null))
		));
		$this->_insert_prod_items($tv, $arr["obj_inst"], "prod");

		$tv->set_selected_item(ifset($arr["request"], "apply"));
	}

	private function _insert_folder_items($tv, $o, $parent)
	{
		$fld = $o->id;
		$ot = new object_tree(array(
			"parent" => $fld,
			"class_id" => array(CL_MRP_ORDER_COVER_GROUP),
			"lang_id" => array(),
			"site_id" => array()
		));
		$ol = $ot->to_list();
		foreach($ol->arr() as $item)
		{
			$tv->add_item($item->parent() == $fld ? $parent : $item->parent(), array(
				"id" => $item->id(),
				"name" => $item->name(),
				"url" => aw_url_change_var(array("apply" => $item->id(), "cov_cat" => null)),
				"iconurl" => icons::get_icon_url($item)
			));
		}
	}

	private function _insert_resource_items($tv, $o, $parent)
	{
		$fld = $o->mrp_workspace()->resources_folder;
		$ot = new object_tree(array(
			"parent" => $fld,
			"class_id" => array(CL_MENU, CL_MRP_RESOURCE),
			"lang_id" => array(),
			"site_id" => array()
		));
		$ol = $ot->to_list();
		foreach($ol->arr() as $item)
		{
			$tv->add_item($item->parent() == $fld ? $parent : $item->parent(), array(
				"id" => $item->id(),
				"name" => $this->_cover_count($item->name(), $item->id()),
				"url" => aw_url_change_var(array("apply" => $item->id(), "cov_cat" => null)),
				"iconurl" => icons::get_icon_url($item)
			));
		}
	}

	private function _insert_prod_items($tv, $o, $parent)
	{
		$fld = $o->mrp_workspace()->prop("RELTYPE_PURCHASING_MANAGER.conf.prod_fld");

		$ot = new object_tree(array(
			"parent" => $fld,
			"class_id" => array(CL_MENU, CL_SHOP_PRODUCT),
			"lang_id" => array(),
			"site_id" => array()
		));
		$ol = $ot->to_list();
		foreach($ol->arr() as $item)
		{
			$tv->add_item($item->parent() == $fld ? $parent : $item->parent(), array(
				"id" => $item->id(),
				"name" => $this->_cover_count($item->name(), $item->id()),
				"url" => aw_url_change_var(array("apply" => $item->id(), "cov_cat" => null)),
				"iconurl" => icons::get_icon_url($item)
			));
		}
	}

	/**
		@attrib name=copy_covers
	**/
	function copy_covers($arr)
	{
		$_SESSION["moc_copy"] = $arr["sel"];
		return $arr["post_ru"];
	}

	/**
		@attrib name=paste_covers
	**/
	function paste_covers($arr)
	{
		foreach(safe_array($_SESSION["moc_copy"]) as $item)
		{
			$item = obj($item);
			if ($arr["apply"] == "general")
			{
				$item->add_applies_general();
			}
			else
			if (is_oid($arr["apply"]))
			{
				$o = obj($arr["apply"]);
				if ($o->class_id() == CL_MRP_RESOURCE)
				{
					$item->add_applies_resource($o);
				}
				else
				if ($o->class_id() == CL_SHOP_PRODUCT)
				{
					$item->add_applies_prod($o);
				}
				else
				if ($o->class_id() == CL_MRP_ORDER_COVER_GROUP)
				{
					$item->move_to_group($o);
				}
			}
		}
		unset($_SESSION["moc_copy"]);
		return $arr["post_ru"];
	}

	function _get_stats_period($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->add_item(0, array(
			"id" => "cur_week",
			"name" => $this->_count_name(t("Jooksev n&auml;dal"), $arr["request"], "s_period", "cur_week"),
			"url" => aw_url_change_var("s_period", "cur_week")
		));
		$t->add_item(0, array(
			"id" => "prev_week",
			"name" => $this->_count_name(t("Eelmine n&auml;dal"), $arr["request"], "s_period", "prev_week"),
			"url" => aw_url_change_var("s_period", "prev_week")
		));
		$t->add_item(0, array(
			"id" => "cur_mon",
			"name" => $this->_count_name(t("Jooksev kuu"), $arr["request"], "s_period", "cur_mon"),
			"url" => aw_url_change_var("s_period", "cur_mon")
		));
		$t->add_item(0, array(
			"id" => "prev_mon",
			"name" => $this->_count_name(t("Eelmine kuu"), $arr["request"], "s_period", "prev_mon"),
			"url" => aw_url_change_var("s_period", "prev_mon")
		));

		// count years
		$filt = array(
			"class_id" => CL_MRP_ORDER_PRINT,
			"lang_id" => array(),
			"site_id" => array(),
			new obj_predicate_sort(array("created" => "asc")),
			new obj_predicate_limit(1)
		);
		$ol = new object_list($filt);
		if ($ol->count())
		{
			$first = $ol->begin()->created();
			for ($year = date("Y", $first); $year <= date("Y"); $year++)
			{

				$t->add_item(0, array(
					"id" => "year_".$year,
					"name" => $this->_count_name($year, $arr["request"], "s_period", "year_".$year),
					"url" => aw_url_change_var("s_period", "year_".$year)
				));

				$mon = $year == date("Y", $first) ? date("m", $first) : 1;
				$last_mon = $year == date("Y") ? date("m") : 12;
				for(; $mon <= $last_mon; $mon++)
				{
					$mon_id = "month_".$year."_".$mon;
					$t->add_item("year_".$year, array(
						"id" => $mon_id,
						"name" => $this->_count_name(aw_locale::get_lc_month($mon), $arr["request"], "s_period", $mon_id),
						"url" => aw_url_change_var("s_period", $mon_id)
					));
				}
			}
		}

		$t->add_item(0, array(
			"id" => "total",
			"name" => $this->_count_name(t("K&otilde;ik perioodid"), $arr["request"], "s_period", "total"),
			"url" => aw_url_change_var("s_period", "total")
		));

		$act = "cur_mon";
		if (isset($arr["request"]["s_period"]))
		{
			$act = $arr["request"]["s_period"];
		}
		$t->set_selected_item($act);
	}

	function _get_stats_customer($arr)
	{
		$t = $arr["prop"]["vcl_inst"];

		// cust groups

		$co = $arr["obj_inst"]->owner_co();
		$this->_req_customer_tree($t, $co, 0, $arr);

		$t->add_item(0, array(
			"id" => "total",
			"name" => $this->_count_name(t("K&otilde;ik kliendid"), $arr["request"], "s_customer", null),
			"url" => aw_url_change_var("s_customer", null)
		));

		$act = "total";
		if (isset($arr["request"]["s_customer"]))
		{
			$act = $arr["request"]["s_customer"];
		}
		$t->set_selected_item($act);
	}

	private function _req_customer_tree($t, $co, $pt, $arr)
	{
		foreach($co->connections_from(array("type" => "RELTYPE_CATEGORY")) as $c)
		{
			$t->add_item(0, array(
				"id" => $c->prop("to"),
				"name" => $this->_count_name($c->prop("to.name"), $arr["request"], "s_customer", $c->prop("to")),
				"url" => aw_url_change_var("s_customer", $c->prop("to"))
			));
			$this->_req_customer_tree($t, $c->to(), $c->prop("to"), $arr);
		}
	}

	function _get_stats_status($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		foreach(get_instance("mrp/orders/mrp_order")->get_state_list() as $id => $name)
		{
			$t->add_item(0, array(
				"id" => "state_".$id,
				"name" => $this->_count_name($name, $arr["request"], "s_state", "s_".$id),
				"url" => aw_url_change_var("s_state", "s_".$id)
			));
		}

		$t->add_item(0, array(
			"id" => "state_all",
			"name" => $this->_count_name(t("K&otilde;ik"), $arr["request"], "s_state", null),
			"url" => aw_url_change_var("s_state", null)
		));

		$act = "state_all";
		if (isset($arr["request"]["s_state"]))
		{
			list(, $real_state) = explode("_", $arr["request"]["s_state"]);
			$act = "state_".$real_state;
		}
		$t->set_selected_item($act);
	}

	private function _init_stats_table($t)
	{
		$t->define_field(array(
			"name" => "customer",
			"caption" => t("Klient"),
			"align" => "left",
			"sortable" => 1,
			"colspan" => "set_colspan"
		));
		$t->define_field(array(
			"name" => "state",
			"caption" => t("Staatus"),
			"align" => "left",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "order",
			"caption" => t("Tellimus"),
			"align" => "left",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "created",
			"caption" => t("Loodud"),
			"align" => "left",
			"sortable" => 1,
			"numeric" => 1,
			"type" => "time",
			"format" => "d.m.Y H:i"
		));
		$t->define_field(array(
			"name" => "price",
			"caption" => t("Hind"),
			"align" => "right"
		));
		$t->define_field(array(
			"name" => "mat_price",
			"caption" => t("Materjalide"),
			"align" => "right",
			"parent" => "price",
			"numeric" => 1,
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "res_price",
			"caption" => t("Resursside"),
			"align" => "right",
			"parent" => "price",
			"numeric" => 1,
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "cover_price",
			"caption" => t("Katete"),
			"align" => "right",
			"parent" => "price",
			"numeric" => 1,
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "tot_price",
			"caption" => t("Kokku"),
			"align" => "right",
			"parent" => "price",
			"numeric" => 1,
			"sortable" => 1
		));
	}
	
	function _count_name($name, $r, $k, $v)
	{
		$ol = new object_list($this->_param2filt($r, $k, $v));
		return $name." (".$ol->count().") ";
	}

	function _param2filt($r, $k, $v)
	{
		$filt = array(
			"class_id" => CL_MRP_ORDER_PRINT,
			"lang_id" => array(),
			"site_id" => array(),
		);
		if ($v === null)
		{
			unset($r[$k]);
		}
		else
		{
			$r[$k] = $v;
		}
		$this->_request2filt($filt, $r);
		return $filt;
	}

	function _get_stats_table($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_stats_table($t);

		$filt = array(
			"class_id" => CL_MRP_ORDER_PRINT,
			"lang_id" => array(),
			"site_id" => array(),
			new obj_predicate_sort(array("customer" => "asc", "created" => "desc"))
		);
		$this->_request2filt($filt, $arr["request"]);
		
		$ol = new object_list($filt);

		$sums = array("mat_price" => 0, "res_price" => 0, "tot_price" => 0);
		$states = get_instance("mrp/orders/mrp_order")->get_state_list();
		$pc = "";
		$cust_line_count = 0;
		foreach($ol->arr() as $o)
		{
			if ($pc != $o->customer)
			{
				// add customer totals row
				if ($cust_line_count > 0)
				{
					$t->define_data(array(
						"set_colspan" => 4,
						"customer" => html::strong(t("Kokku kliendile")),
						"order" => "",
						"state" => "",
						"created" => "",
						"mat_price" => html::strong(number_format($tot_mat_price, 2)),
						"res_price" => html::strong(number_format($tot_res_price, 2)),
						"cover_price" => html::strong(number_format($tot_cover_price, 2)),
						"tot_price" => html::strong(number_format($tot_tot_price, 2)),
					));
				}
				$tot_mat_price = 0;
				$tot_tot_price = 0;
				$tot_res_price = 0;
				$tot_cover_price = 0;
				$cust_line_count = 0;
			}
			$cust_line_count++;
			$pc = $o->customer;
			$mat_price = $o->get_materials_price();
			$res_price = $o->get_resource_price();
			$tot_price = $o->get_total_price();
			$cover_price = $o->get_cover_price();

			$tot_mat_price += $mat_price;
			$tot_tot_price += $tot_price;
			$tot_res_price += $res_price;
			$tot_cover_price += $cover_price;

			$t->define_data(array(
				"order" => html::obj_change_url($o),
				"customer" => html::obj_change_url($o->customer()),
				"state" => $states[$o->state],
				"created" => $o->created(),
				"mat_price" => number_format($mat_price, 2),
				"res_price" => number_format($res_price, 2),
				"cover_price" => number_format($cover_price, 2),
				"tot_price" => number_format($tot_price, 2)
			));

			$sums["mat_price"] += $mat_price;
			$sums["res_price"] += $res_price;
			$sums["tot_price"] += $tot_price;
			$sums["cover_price"] += $cover_price;
		}

		$t->define_data(array(
			"customer" => html::strong(t("Kokku kliendile")),
			"order" => "",
			"state" => "",
			"created" => "",
			"mat_price" => html::strong(number_format($tot_mat_price, 2)),
			"res_price" => html::strong(number_format($tot_res_price, 2)),
			"cover_price" => html::strong(number_format($tot_cover_price, 2)),
			"tot_price" => html::strong(number_format($tot_tot_price, 2)),
			"set_colspan" => 4
		));

//		$t->set_default_sortby("created");

//		$t->sort_by(array(
	//		"rgroupby" => array("customer" => "customer"),
		//));

		$t->set_sortable(false);
		$t->define_data(array(
			"order" => html::strong(t("Summa")),
			"customer" => "",
			"state" => "",
			"created" => "",
			"mat_price" => html::strong(number_format($sums["mat_price"], 2)),
			"res_price" => html::strong(number_format($sums["res_price"], 2)),
			"tot_price" => html::strong(number_format($sums["tot_price"], 2)),
			"cover_price" => html::strong(number_format($sums["cover_price"], 2)),
		));
	}

	function _request2filt(&$filt, $r)
	{
		if (!empty($r["s_state"]))
		{
			list(, $real_state) = explode("_", $r["s_state"]);
			$filt["state"] = $real_state;
		}

		if (!empty($r["s_customer"]))
		{
			// get customers for category
			$custs = array();
			foreach(obj($r["s_customer"])->connections_from(array("type" => 3)) as $c)
			{
				$custs[] = $c->prop("to");
			}
			if (count($custs) == 0)
			{
				$custs = -1;
			}
			$filt["customer"] = $custs;
		}

		if (empty($r["s_period"]) || $r["s_period"] == "cur_mon")
		{
			$filt["created"] = new obj_predicate_compare(OBJ_COMP_GREATER, mktime(0,0, 0, date("m"), 1, date("Y")));
		}
		else
		{
			if (substr($r["s_period"], 0, 5) == "year_")
			{
				list(, $year) = explode("_", $r["s_period"]);
				$filt["created"] = new obj_predicate_compare(OBJ_COMP_BETWEEN, mktime(0,0, 0,  1, 1, $year), mktime(0,0, 0, 1, 1, $year+1));
			}
			else
			if (substr($r["s_period"], 0, 6) == "month_")
			{
				list(, $year, $month) = explode("_", $r["s_period"]);
				$filt["created"] = new obj_predicate_compare(OBJ_COMP_BETWEEN, mktime(0,0, 0,  $month, 1, $year), mktime(0,0, 0, $month+1, 1, $year));
			}
			else
			{
				switch($r["s_period"])
				{
					case "prev_mon":
						$filt["created"] = new obj_predicate_compare(OBJ_COMP_BETWEEN, mktime(0,0, 0,  date("m")-1, 1, date("Y")), mktime(0,0, 0, date("m"), 1, date("Y")));
						break;

					case "total":
					default:
						break;
				}
			}
		}

		if (!empty($r["unit"]))
		{
			$filt["CL_MRP_ORDER_PRINT.seller_person.RELTYPE_SECTION"] = $r["unit"];
		}
	}

	function _get_stats_unit($arr)
	{
		$co = $arr["obj_inst"]->owner_co();
		$arr["prop"]["vcl_inst"]->add_item(0, array(
			"id" => $co->id(),
			"name" => $this->_count_name($co->name(), $arr["request"], "unit", null),
			"url" => aw_url_change_var(array(
				"unit" => NULL,
			)),
		));

		$i = $co->instance();
		$i->active_node = $arr["request"]["unit"];
		$i->generate_tree(array(
			"tree_inst" => &$arr["prop"]["vcl_inst"],
			"obj_inst" => $co,
			"node_id" => $co->id(),
			"url" => aw_url_change_var("unit", null),
			"attrib" => "unit",
			"name_format_cb" => array(&$this, "_format_unit_name")
		));
	}

	function _format_unit_name(&$item_data)
	{
		$tmp = $_REQUEST;
		$u = new aw_uri($item_data["url"]);
		$item_data["name"] = $this->_count_name($item_data["name"], $tmp, "unit", $u->arg("unit"));
	}

	public function _get_cover_tree_cats($arr)
	{
		$this->_req_cover_tree_cats($arr["prop"]["vcl_inst"], $arr["obj_inst"], 0);
		$arr["prop"]["vcl_inst"]->set_selected_item($arr["request"]["cov_cat"]);
		$arr["prop"]["vcl_inst"]->set_root_url(aw_url_change_var(array("cov_cat" => null, "apply" => null)));
		$arr["prop"]["vcl_inst"]->set_root_name($arr["obj_inst"]->name());
		$arr["prop"]["vcl_inst"]->set_root_icon(icons::get_icon_url(CL_MENU));
	}

	private function _req_cover_tree_cats($t, $pto, $pt)
	{
		foreach($pto->connections_from(array("type" => "RELTYPE_MRP_COVER")) as $c)
		{
			$o = $c->to();
			if ($o->class_id() == CL_MRP_ORDER_COVER_GROUP)
			{
				$t->add_item($pt, array(
					"id" => $o->id(),
					"name" => $this->_cover_count_f($o->name(), $o->id()),
					"url" => aw_url_change_var(array("cov_cat" => $o->id(), "apply" => null)),
					"icon" => icons::get_icon_url($o->class_id() == CL_MRP_ORDER_COVER_GROUP ? CL_MENU : $o->class_id())
				));
				$this->_req_cover_tree_cats($t, $o, $o->id());
			}
		}
	}

	function _cover_count_f($name, $applies)
	{
		$o = obj($applies);
		$cnt = count($o->connections_from(array("type" => "RELTYPE_MRP_COVER")));
		return $name." (".$cnt.")";
	}
}

?>
