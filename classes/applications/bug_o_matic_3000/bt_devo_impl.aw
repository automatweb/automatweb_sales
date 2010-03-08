<?php
/*
@classinfo  maintainer=robert
*/

class bt_devo_impl extends core
{
	function bt_devo_impl()
	{
		$this->init();
	}

	function _get_dev_orders_tb($arr)
	{
		$pt = $arr["request"]["tf"] ? $arr["request"]["tf"] : $arr["obj_inst"]->id();
		$tb =& $arr["prop"]["vcl_inst"];
		$tb->add_menu_button(array(
			"name" => "add_req",
			"tooltip" => t("Lisa"),
			"img" => "new.gif",
		));
		$tb->add_menu_item(array(
			"parent" => "add_req",
			"text" => t("Kategooria"),
			"link" => html::get_new_url(CL_DEVELOPMENT_ORDER_CAT, $pt, array(
				"return_url" => get_ru(),
			))
		));
		$tb->add_menu_item(array(
			"parent" => "add_req",
			"text" => t("Arendustellimus"),
			"link" => html::get_new_url(CL_DEVELOPMENT_ORDER, $pt, array(
				"return_url" => get_ru(),
			))
		));
		$tb->add_button(array(
			"name" => "delete",
			"tooltip" => t("Kustuta"),
			"img" => "delete.gif",
			"action" => "delete",
			"confirm" => t("Oled kindel, et soovid tellimusi kustutada?"),
		));
		$tb->add_separator();
		$tb->add_button(array(
			"name" => "cut",
			"tooltip" => t("L&otilde;ika"),
			"img" => "cut.gif",
			"action" => "cut_b",
		));
		if (is_array($_SESSION["bt"]["cut_bugs"]) && count($_SESSION["bt"]["cut_bugs"]))
		{
			$tb->add_button(array(
				"name" => "paste",
				"tooltip" => t("Kleebi"),
				"img" => "paste.gif",
				"action" => "paste_b",
			));
		}
	}

	function _get_dev_orders_tree($arr)
	{
		classload("core/icons");
		if(true || $arr["obj_inst"]->prop("order_tree_conf"))
		{
			$i = get_instance(CL_BUG_TRACKER);
			$arr["prop"]["name"] = "bug_tree";
			$i->get_property($arr);
			return;
			$t = &$arr["prop"]["vcl_inst"];
			$t->start_tree(array(
				"type" => TREE_DHTML,
				"has_root" => 1,
				"tree_id" => "bt_devos_b",
				"persist_state" => 1,
				"root_name" => $arr["obj_inst"]->name(),
				"root_icon" => icons::get_icon_url(CL_MENU),
				"root_url" => aw_url_change_var(array("tf" => 0))
			));
			$ol = new object_list(array(
				"class_id" => array(CL_DEVELOPMENT_ORDER_CAT, CL_DEVELOPMENT_ORDER, CL_BUG),
				"parent" => $arr["obj_inst"]->id()
			));
			foreach($ol->ids() as $oid)
			{
				$ol2 = new object_list(array(
					"class_id" => array(CL_DEVELOPMENT_ORDER_CAT, CL_DEVELOPMENT_ORDER, CL_BUG),
					"parent" => $oid
				));
				if(count($ol2->ids()))
				{
					$o = obj($oid);
					$t->add_item(0,array(
						"id" => $oid,
						"name" => $o->name(),
						"iconurl" => icons::get_icon_url(CL_MENU),
						"url" => aw_url_change_var(array(
							"tf"=> $oid,
						))
					));
				}
			}
		}
		else
		{
			$arr["prop"]["vcl_inst"] = treeview::tree_from_objects(array(
				"tree_opts" => array(
					"type" => TREE_DHTML, 
					"persist_state" => true,
					"tree_id" => "bt_devos",
				),
				"root_item" => $arr["obj_inst"],
				"ot" => new object_tree(array(
					"parent" => $arr["obj_inst"]->id(),
					"lang_id" => array(),
					"site_id" => array(),
					"class_id" => CL_DEVELOPMENT_ORDER_CAT
				)),
				"var" => "tf",
				"icon" => icons::get_icon_url(CL_MENU)
			));
		}
	}

	function _get_dev_orders_table($arr)
	{
		if (!$arr["request"]["tf"] && $arr["request"]["b_id"])
		{
			$arr["request"]["tf"] = $arr["request"]["b_id"];
		}
		$t =& $arr["prop"]["vcl_inst"];
		$pt = $arr["request"]["tf"] ? $arr["request"]["tf"] : $arr["obj_inst"]->id();
		$pto = obj($pt);
		$f = array(
			"class_id" => CL_DEVELOPMENT_ORDER,
			"lang_id" => array(),
			"site_id" => array(),
			"parent" => $pt
		);
		$ol = new object_list($f);
		$t->table_from_ol($ol, array("name", "created", "createdby", "orderer_co", "orderer_unit", "customer", "project"), CL_DEVELOPMENT_ORDER);
		$t->set_caption(sprintf(t("Arendustellimused kategoorias %s"), $pto->name()));
	}

	function _get_devo_p_tree($arr)
	{
		$ol = new object_list(array(
			"class_id" => CL_DEVELOPMENT_ORDER,
			"lang_id" => array(),
			"site_id" => array(),
		));
		// get all projects from those
		$p2req = array();
		foreach($ol->arr() as $r)
		{
			if ($this->can("view", $r->prop("project")))
			{
				$p2req[(int)$r->prop("project")] ++;
			}
		}
		$p2req[(int)null]++;
		$p2req[(int)null]--;
		$t =& $arr["prop"]["vcl_inst"];
		$i = get_instance(CL_DEVELOPMENT_ORDER);
		foreach($p2req as $proj => $cnt)
		{
			/*if (!is_oid($proj))
			{
				continue;
			}*/
			$po = obj($proj);
			$nm = (is_oid($proj) ? $po->name() : t("Muud t&ouml;&ouml;d"))." ($cnt)";
			if ($arr["request"]["proj"] == $proj && !$arr["request"]["state"] && !$arr["request"]["pri"] )
			{
				$nm = "<b>".$nm."</b>";
			}
			$t->add_item(0, array(
				"id" => "p_".$proj,
				"parent" => 0,
				"name" => $nm,
				"url" => aw_url_change_var(array(
					"proj" => $proj,
					"state" => null,
					"pri" => null
				))
			));
		}
	}

	function _get_devo_p_table($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$pto = obj();
		if ($arr["request"]["proj"])
		{
			$p = $arr["request"]["proj"];
			$pto = obj($p);
		}
		else
		{
			$p = new obj_predicate_compare(OBJ_COMP_NULL);
		}
		$f = array(
			"class_id" => CL_DEVELOPMENT_ORDER,
			"project" => $p,
			"lang_id" => array(),
			"site_id" => array(),
		);
		$ol = new object_list($f);
		$t->table_from_ol($ol, array("name", "created", "createdby", "orderer_co", "orderer_unit", "customer", "project"), CL_DEVELOPMENT_ORDER);
		$t->set_caption(sprintf(t("Arendustellimused projektile %s"), $pto->name()));
	}

	function _get_devo_c_tree($arr)
	{
		$ol = new object_list(array(
			"class_id" => CL_DEVELOPMENT_ORDER,
			"lang_id" => array(),
			"site_id" => array(),
		));
		// get all projects from those
		$p2req = array();
		foreach($ol->arr() as $r)
		{
			if ($this->can("view", $r->prop("customer")))
			{
				$p2req[(int)$r->prop("customer")] ++;
			}
		}
		$p2req[(int)null]++;
		$p2req[(int)null]--;
		$t =& $arr["prop"]["vcl_inst"];
		$i = get_instance(CL_DEVELOPMENT_ORDER);
		foreach($p2req as $proj => $cnt)
		{
			/*if (!is_oid($proj))
			{
				continue;
			}*/
			$po = obj($proj);
			$nm = (is_oid($proj) ? $po->name() : t("Muud t&ouml;&ouml;d"))." ($cnt)";
			if ($arr["request"]["cust"] == $proj )
			{
				$nm = "<b>".$nm."</b>";
			}
			$t->add_item(0, array(
				"id" => "p_".$proj,
				"parent" => 0,
				"name" => $nm,
				"url" => aw_url_change_var(array(
					"cust" => $proj,
					"state" => null,
					"pri" => null
				))
			));
		}
	}

	function _get_devo_c_table($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$pto = obj();
		if ($arr["request"]["cust"])
		{
			$p = $arr["request"]["cust"];
			$pto = obj($p);
		}
		else
		{
			$p = new obj_predicate_compare(OBJ_COMP_NULL);
		}
		$f = array(
			"class_id" => CL_DEVELOPMENT_ORDER,
			"customer" => $p,
			"lang_id" => array(),
			"site_id" => array(),
		);
		$ol = new object_list($f);
		$t->table_from_ol($ol, array("name", "created", "createdby", "orderer_co", "orderer_unit", "customer", "project"), CL_DEVELOPMENT_ORDER);
		$t->set_caption(sprintf(t("Arendustellimused tellijale %s"), $pto->name()));
	}
}
