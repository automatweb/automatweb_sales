<?php
// customer_problem_ticket.aw - Probleem
/*

@classinfo syslog_type=ST_CUSTOMER_PROBLEM_TICKET relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=robert
@tableinfo aw_problem_tickets index=aw_oid master_table=objects master_index=brother_of
@default table=aw_problem_tickets
@default group=general

	@property customer type=relpicker reltype=RELTYPE_CUSTOMER field=aw_customer
	@caption Klient

	@property project type=relpicker reltype=RELTYPE_PROJECT field=aw_project
	@caption Projekt

	@property requirement type=relpicker reltype=RELTYPE_REQ field=aw_requirement
	@caption N&otilde;ue

	@property from_dev_order type=relpicker reltype=RELTYPE_DEV_ORDER field=aw_from_dev_order
	@caption Arendustellimusest

	@property from_bug type=relpicker reltype=RELTYPE_FROM_BUG field=aw_from_bug
	@caption Arendus&uuml;lesanne

	@property orderer_co type=relpicker reltype=RELTYPE_ORDERER_CO field=aw_orderer_co
	@caption Tellija organisatsioon

	@property orderer_unit type=relpicker reltype=RELTYPE_UNIT field=aw_orderer_unit
	@caption Tellija &uuml;ksus

	@property content type=textarea rows=20 cols=50 field=aw_content
	@caption Sisu

	@property fileupload type=releditor reltype=RELTYPE_FILE1 rel_id=first use_form=emb field=aw_f1
	@caption Fail1

	@property fileupload2 type=releditor reltype=RELTYPE_FILE2 rel_id=first use_form=emb field=aw_f2
	@caption Fail2

	@property fileupload3 type=releditor reltype=RELTYPE_FILE3 rel_id=first use_form=emb field=aw_f3
	@caption Fail3

@default group=bugs

	@property bug_toolbar type=toolbar no_caption=1 store=no

	@property bug_list type=table store=no no_caption=1

@default group=reqs

	@property req_toolbar type=toolbar no_caption=1 store=no

	@property req_list type=table store=no no_caption=1

@groupinfo bugs caption="Arendus&uuml;lesanded"
@groupinfo reqs caption="N&otilde;uded"


@reltype CUSTOMER value=1 clid=CL_CRM_COMPANY
@caption Klient

@reltype PROJECT value=2 clid=CL_PROJECT
@caption Projekt

@reltype FILE1 value=3 clid=CL_FILE
@caption Fail1

@reltype FILE2 value=4 clid=CL_FILE
@caption Fail2

@reltype FILE3 value=5 clid=CL_FILE
@caption Fail3

@reltype BUG value=6 clid=CL_BUG
@caption Arendus&uuml;lesanne

@reltype REQ value=7 clid=CL_PROCUREMENT_REQUIREMENT
@caption N&otilde;ue

@reltype DEV_ORDER value=8 clid=CL_DEVELOPMENT_ORDER
@caption Arendustellimus

@reltype FROM_BUG value=9 clid=CL_BUG
@caption Arendus&uuml;lesandest

@reltype HAS_REQ value=10 clid=CL_PROCUREMENT_REQUIREMENT
@caption N&otilde;ue

@reltype ORDERER_CO value=11 clid=CL_CRM_COMPANY
@caption Organisatsioon

@reltype UNIT value=12 clid=CL_CRM_SECTION
@caption &Uuml;ksus

*/

class customer_problem_ticket extends class_base
{
	function customer_problem_ticket()
	{
		$this->init(array(
			"tpldir" => "applications/bug_o_matic_3000/customer_problem_ticket",
			"clid" => CL_CUSTOMER_PROBLEM_TICKET
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "orderer_co":
				if ($arr["new"])
				{
					$co = get_current_company();
					$prop["options"] = array("" => t("--vali--"), $co->id() => $co->name());
					$prop["value"] = $co->id();
				}
				else
				{
					$co = get_current_company();
					$prop["options"][$co->id()] = $co->name();
				}
				break;

			case "orderer_unit":
				if ($this->can("view", $arr["obj_inst"]->prop("orderer_co")))
				{
					$co = obj($arr["obj_inst"]->prop("orderer_co"));
				}
				else
				{
					$co = get_current_company();
				}
				$co_i = $co->instance();
				$sects = $co_i->get_all_org_sections($co);
				$prop["options"] = array("" => t("--vali--"));
				if (count($sects))
				{
					$ol = new object_list(array("oid" => $sects, "lang_id" => array(), "site_id" => array()));
					$prop["options"] += $ol->names();
				}
				$p = get_current_person();
				if ($arr["new"])
				{
					$prop["value"] = $p->prop("org_section");
				}
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
		}
		return $retval;
	}

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_problem_tickets(aw_oid int primary key, aw_customer int, aw_project int, aw_content mediumtext, aw_f1 int, aw_f2 int, aw_f3 int)");
			return true;
		}

		switch($f)
		{
			case "aw_f1":
			case "aw_f2":
			case "aw_f3":
			case "aw_requirement":
			case "aw_from_dev_order":
			case "aw_from_bug":
			case "aw_orderer_co":
			case "aw_orderer_unit":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				return true;
		}
	}

	function _get_bug_toolbar($arr)
	{
		$tb =& $arr["prop"]["vcl_inst"];
		$tb->add_new_button(array(CL_BUG), $arr["obj_inst"]->id(), 6 /* RELTYPE_BUG */, array("from_problem" => $arr["obj_inst"]->id()));
		$tb->add_delete_button();
	}

	function _get_bug_list($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		/*$t->table_from_ol(
			,
			array("name", "createdby", "created"),
			CL_BUG
		);*/
		$ol = new object_list($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_BUG")));
		$bt = get_instance(CL_BUG_TRACKER);
		$bt->_init_bug_list_tbl($t);
		$bt->populate_bug_list_table_from_list($t, $ol, array("bt" => $arr["obj_inst"]));
		$t->set_caption(t("Probleemist tulenenud &uuml;lesanded"));
	}

	function _get_req_toolbar($arr)
	{
		$tb =& $arr["prop"]["vcl_inst"];
		$tb->add_new_button(array(CL_PROCUREMENT_REQUIREMENT), $arr["obj_inst"]->id(), 10 /* RELTYPE_HAS_REQ */);
		$tb->add_delete_button();
	}

	function _get_req_list($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$t->table_from_ol(
			new object_list($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_HAS_REQ"))),
			array("name", "createdby", "created"),
			CL_PROCUREMENT_REQUIREMENT
		);
		$t->set_caption(t("Probleemist tulenenud n&otilde;uded"));
	}
}
?>
