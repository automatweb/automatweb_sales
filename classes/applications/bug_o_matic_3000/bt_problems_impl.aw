<?php
/*
@classinfo  maintainer=robert
*/

class bt_problems_impl extends core
{
	function bt_problems_impl()
	{
		$this->init();
	}

	function _get_problems_tb($arr)
	{
		$tb =& $arr["prop"]["vcl_inst"];
		$tb->add_new_button(array(CL_CUSTOMER_PROBLEM_TICKET), $arr["obj_inst"]->id());
		$tb->add_delete_button();
	}

	function _get_problems_table($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$ol = new object_list(array(
			"class_id" => CL_CUSTOMER_PROBLEM_TICKET,
			"lang_id" => array(),
			"site_id" => array(),
		));
		$t->table_from_ol($ol, array("name", "createdby", "created", "orderer_co", "orderer_unit", "customer", "project", "requirement", "from_dev_order", "from_bug"), CL_CUSTOMER_PROBLEM_TICKET);
		$t->set_caption(t("Nimekiri probleemidest"));
	}

	function _get_pu_tree($arr)
	{
		$ol = new object_list(array(
			"class_id" => CL_CUSTOMER_PROBLEM_TICKET,
			"lang_id" => array(),
			"site_id" => array()
		));
		$cos = array();
		$co2sect = array();
		foreach($ol->arr() as $o)
		{
			$cos[$o->prop("orderer_co")]++;
			$co2sect[$o->prop("orderer_co")][$o->prop("orderer_unit")]++;
		}

		$t =& $arr["prop"]["vcl_inst"];
		$i = get_instance(CL_CUSTOMER_PROBLEM_TICKET);
		foreach($cos as $co => $cnt)
		{
			if (!is_oid($co))
			{
				continue;
			}
			$po = obj($co);
			$nm = $po->name()." ($cnt)";
			if ($arr["request"]["co"] == $co && !$arr["request"]["asect"])
			{
				$nm = "<b>".$nm."</b>";
			}
			$t->add_item(0, array(
				"id" => "p_".$co,
				"parent" => 0,
				"name" => $nm,
				"url" => aw_url_change_var(array(
					"co" => $co,
					"asect" => null,
				))
			));

			// add al org sections under the tree
			foreach($co2sect[$co] as $sect => $s_cnt)
			{
				$po = obj($sect);
				$nm = $po->name()." ($cnt)";
				if ($arr["request"]["co"] == $co && $arr["request"]["asect"] == $sect)
				{
					$nm = "<b>".$nm."</b>";
				}
				$t->add_item("p_".$co, array(
					"id" => "s_".$sect,
					"parent" => "p_".$co,
					"name" => $nm,
					"url" => aw_url_change_var(array(
						"co" => $co,
						"asect" => $sect,
					))
				));
				
			}
		}
	}

	function _get_pu_table($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];

		$f = array(
			"class_id" => CL_CUSTOMER_PROBLEM_TICKET,
			"lang_id" => array(),
			"site_id" => array(),
		);
		$pto = obj();
		if ($arr["request"]["co"])
		{
			$f["orderer_co"] = $arr["request"]["co"];
			$pto = obj($f["orderer_co"]);
		}
		if ($arr["request"]["asect"])
		{
			$f["orderer_unit"] = $arr["request"]["asect"];
		}

		$ol = new object_list($f);
		$t->table_from_ol($ol, array("name", "createdby", "created", "customer", "project", "requirement", "from_dev_order", "from_bug"), CL_CUSTOMER_PROBLEM_TICKET);
		$t->set_caption(sprintf(t("Probleemid organisatsioonil %s"), $pto->name()));
	}

	function _get_pp_tree($arr)
	{
		$ol = new object_list(array(
			"class_id" => CL_CUSTOMER_PROBLEM_TICKET,
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
		$i = get_instance(CL_CUSTOMER_PROBLEM_TICKET);
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

	function _get_pp_table($arr)
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
			"class_id" => CL_CUSTOMER_PROBLEM_TICKET,
			"project" => $p,
			"lang_id" => array(),
			"site_id" => array(),
		);
		$ol = new object_list($f);
		$t->table_from_ol($ol, array("name", "createdby", "created", "orderer_co", "orderer_unit", "customer", "project", "requirement", "from_dev_order", "from_bug"), CL_CUSTOMER_PROBLEM_TICKET);
		$t->set_caption(sprintf(t("Probleemid projektis %s"), $pto->name()));
	}

	function _get_pr_tree($arr)
	{
		
		$arr["prop"]["vcl_inst"] = treeview::tree_from_objects(array(
			"tree_opts" => array(
				"type" => TREE_DHTML, 
				"persist_state" => true,
				"tree_id" => "bt_pr_reqs",
			),
			"root_item" => $arr["obj_inst"],
			"ot" => new object_tree(array(
				"parent" => $arr["obj_inst"]->id(),
				"lang_id" => array(),
				"site_id" => array(),
				"class_id" => array(CL_REQUIREMENT_CATEGORY,CL_PROCUREMENT_REQUIREMENT)
			)),
			"var" => "tf",
			"icon" => icons::get_icon_url(CL_MENU)
		));
	}

	function _get_pr_table($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		if (!$arr["request"]["tf"])
		{
			return;
		}
		/*$req_tree = new object_tree(array(
			"parent" => $arr["request"]["tf"],
			"lang_id" => array(),
			"site_id" => array(),
			"class_id" => array(CL_REQUIREMENT_CATEGORY,CL_PROCUREMENT_REQUIREMENT)
		));*/

		$pto = obj($arr["request"]["tf"]);
		$f = array(
			"class_id" => CL_CUSTOMER_PROBLEM_TICKET,
			"requirement" => $arr["request"]["tf"], //$req_tree->ids(),
			"lang_id" => array(),
			"site_id" => array(),
		);
		$ol = new object_list($f);
		$t->table_from_ol($ol, array("name", "createdby", "created", "orderer_co", "orderer_unit", "customer", "project", "requirement", "from_dev_order", "from_bug"), CL_CUSTOMER_PROBLEM_TICKET);
		$t->set_caption(sprintf(t("Probleemid n&otilde;udel %s"), $pto->name()));
	}

	function _get_pu_tb($arr)
	{
		return PROP_IGNORE;
	}

	function _get_pp_tb($arr)
	{
		return PROP_IGNORE;
	}

	function _get_pr_tb($arr)
	{
		return PROP_IGNORE;
	}
}
