<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/budgeting/budgeting_tax_group.aw,v 1.4 2008/05/08 11:37:32 markop Exp $
// budgeting_tax_group.aw - Eelarvestamise maksu grupp 
/*

@classinfo syslog_type=ST_BUDGETING_TAX_GROUP relationmgr=yes no_status=1 prop_cb=1 maintainer=kristo

@default table=objects
@default group=general



@groupinfo flow caption="&Uuml;lekanded" submit=no save=no
@default group=flow
	@layout flow_split type=hbox width="20%:80%" 

		@layout flow_left type=vbox parent=flow_split
	
			@layout tax_tree parent=flow_left type=vbox closeable=1 area_caption=Maksude&nbsp;puu

				@property tax_tree type=treeview parent=tax_tree store=no no_caption=1

			@layout tax_search parent=flow_left type=vbox closeable=1 area_caption=Otsing

				@property tax_s_from_acct type=textbox parent=tax_search store=no captionside=top size=20
				@caption Kontolt
	
				@property tax_s_to_acct type=textbox parent=tax_search store=no captionside=top size=20
				@caption Kontole
	
				@property tax_s_date_from type=date_select format=day_textbox,month_textbox,year_textbox parent=tax_search store=no captionside=top default=-1
				@caption Alates
	
				@property tax_s_date_to type=date_select format=day_textbox,month_textbox,year_textbox parent=tax_search store=no captionside=top default=-1
				@caption Kuni
	
				@property tax_s_sbt type=submit parent=tax_search store=no no_caption=1
				@caption Otsi


		@layout tax_table parent=flow_split type=vbox

			@property flow_table type=table parent=tax_table store=no no_caption=1

*/

class budgeting_tax_group extends class_base
{
	const AW_CLID = 1246;

	function budgeting_tax_group()
	{
		$this->init(array(
			"tpldir" => "applications/budgeting/budgeting_tax_group",
			"clid" => CL_BUDGETING_TAX_GROUP
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "tax_tree":
				$tv = &$arr["prop"]["vcl_inst"];
				unset($arr["request"]["tax_s_tax"]);
				$tv->start_tree (array (
					"type" => TREE_DHTML,
					"tree_id" => "taxgroup_tree",
					"branch" => 1,
					"has_root" => 1,
					"root_name" => $arr["obj_inst"]->name(),
					"root_url" => $this->mk_my_orb("change", $arr["request"]),
				));

				$ol = new object_list(array(
					"class_id" => CL_BUDGETING_TAX,
					"site_id" => array(),
					"lang_id" => array(),
					"tax_grp" => $arr["obj_inst"]->id(),
					"sort_by" => "objects.name asc",
				));

				foreach($ol->arr() as $tax)
				{
					$arr["request"]["tax_s_tax"] = $tax->id();
					$tv->add_item(0, array(
						"id" => $tax->id(),
						"name" => $tax->name(),
						"url" => $this->mk_my_orb("change", $arr["request"])
					));
				}

				break;
			case "tax_s_from_acct":
			case "tax_s_to_acct":
			case "tax_s_date_from":
			case "tax_s_date_to":
				$prop["value"] = $arr["request"][$prop["name"]];
				break;
		};
		return $retval;
	}

	function _get_flow_table($arr)
	{
		$r = $arr["request"];
		
		if($this->can("view" , $r["tax_s_tax"]))
		{
			$tol = new object_list();
			$tol->add($r["tax_s_tax"]);
		}
		else
		{
			$tol = new object_list(array(
				"class_id" => CL_BUDGETING_TAX,
				"site_id" => array(),
				"lang_id" => array(),
				"tax_grp" => $arr["obj_inst"]->id(),
			));
		}

		$accounts = $to_accounts = array();

		foreach($tol->arr() as $tax)
		{
			$from_data = explode("_" , $tax->prop("from_place"));
			$accounts[] = $from_data[1];
			$to_accounts[] = $tax->prop("to_acct");
		}

		$filter = array(
			"class_id" => CL_BUDGETING_TRANSFER,
			"lang_id" => array(),
			"site_id" => array(),
			//"limit" => 100,
			"sort_by" => "objects.created desc",
			"from_acct" => $accounts,
			"to_acct" => $to_accounts,
		);


		if($r["tax_s_from_acct"])
		{
			$account_list = new object_list(array(
				"class_id" => array(CL_BUDGETING_ACCOUNT,CL_CRM_CATEGORY,CL_CRM_PROJECT,CL_TASK,CL_BUG,CL_CRM_MEETING,CL_CRM_CALL, CL_CRM_PERSON),
				"lang_id" => array(),
				"site_id" => array(),
				"name" => "%".$r["tax_s_from_acct"]."%",
			));

			$filter["from_acct"] = array_intersect($filter["from_acct"], $account_list->ids()) + array(1);//et kui tyhjaks jaab siis k6iki ei naitaks
		}
		if($r["tax_s_to_acct"])
		{
			$account_list = new object_list(array(
				"class_id" => array(CL_BUDGETING_ACCOUNT,CL_CRM_CATEGORY,CL_CRM_PROJECT,CL_TASK,CL_BUG,CL_CRM_MEETING,CL_CRM_CALL, CL_CRM_PERSON),
				"lang_id" => array(),
				"site_id" => array(),
				"name" => "%".$r["tax_s_to_acct"]."%",
			));
			$filter["to_acct"] = array_intersect($filter["to_acct"], $account_list->ids()) + array(1);//et kui tyhjaks jaab siis k6iki ei naitaks
		}

		if(date_edit::get_timestamp($r["tax_s_date_from"]) > 1)
		{
			if(date_edit::get_timestamp($r["tax_s_date_to"]) > 1)
			{
				$filter["when"] = new obj_predicate_compare(OBJ_COMP_BETWEEN, date_edit::get_timestamp($r["tax_s_date_from"]), date_edit::get_timestamp($r["tax_s_date_to"]));
			}
			else
			{
				$filter["when"] = new obj_predicate_compare(OBJ_COMP_GREATER_OR_EQ, date_edit::get_timestamp($r["tax_s_date_to"]));
			}
		}
		elseif(date_edit::get_timestamp($r["tax_s_date_to"]) > 1)
		{
			$filter["when"] = new obj_predicate_compare(OBJ_COMP_LESS_OR_EQ, date_edit::get_timestamp($r["tax_s_date_to"]));
		}
//arr($filter);
		$data = new object_list($filter);
		$arr["prop"]["vcl_inst"]->table_from_ol(
			$data,
			array("from_acct", "to_acct", "in_project", "amount", "when"),
			CL_BUDGETING_TRANSFER
		);
		
		$sum = 0;
		foreach($data->arr() as $transfer)
		{
			$sum = $sum + $transfer->prop("amount");
		}
		$arr["prop"]["vcl_inst"]->set_sortable(false);
		$arr["prop"]["vcl_inst"]->define_data(array(
			"in_project" => t("Kokku:"),
			 "amount" => $sum,
		));
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

	function callback_mod_retval($arr)
	{
		$arr["args"]["tax_s_from_acct"] = $arr["request"]["tax_s_from_acct"];
		$arr["args"]["tax_s_to_acct"] = $arr["request"]["tax_s_to_acct"];
		$arr["args"]["tax_s_date_from"] = $arr["request"]["tax_s_date_from"];
		$arr["args"]["tax_s_date_to"] = $arr["request"]["tax_s_date_to"];
		$arr["args"]["tax_s_tax"] = $arr["request"]["tax_s_tax"];
	}

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}
}
?>
