<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_MRP_ORDER relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo
@tableinfo aw_mrp_order master_index=brother_of master_table=objects index=aw_oid

@default table=aw_mrp_order
@default group=general

	@property workspace type=hidden field=aw_workspace
	@caption T&ouml;&ouml;laud

	@property customer type=relpicker reltype=RELTYPE_CUSTOMER field=aw_customer no_edit=1 
	@caption Klient

	@property orderer_person type=relpicker reltype=RELTYPE_ORDERER_PERSON field=aw_orderer_person
	@caption Kliendipoolne kontakt

	@property seller_person type=relpicker reltype=RELTYPE_SELLER_PERSON field=aw_seller_person
	@caption Teostajapoolne kontakt

	@property mrp_case type=hidden field=aw_mrp_case
	@caption Arendustoode

	@property mrp_case_view type=text store=no
	@caption Arendustoode

	@property mrp_pricelist type=relpicker reltype=RELTYPE_MRP_PRICELIST field=aw_mrp_pricelist
	@caption Hinnakiri

	@property state type=select field=aw_state
	@caption Staatus

@reltype WORKSPACE value=1 clid=CL_MRP_ORDER_CENTER
@caption T&ouml;&ouml;laud

@reltype CUSTOMER value=2 clid=CL_CRM_COMPANY,CL_CRM_PERSON
@caption Klient

@reltype ORDERER_PERSON value=3 clid=CL_CRM_PERSON
@caption Tellija isik

@reltype CASE value=4 clid=CL_MRP_CASE
@caption Arendustoode

@reltype SELLER_PERSON value=5 clid=CL_CRM_PERSON
@caption M&uuml;&uuml;ja isik

@reltype MRP_PRICELIST value=6 clid=CL_MRP_PRICELIST
@caption ERP Hinnakiri

*/

class mrp_order extends class_base
{
	const AW_CLID = 1519;

	function mrp_order()
	{
		$this->init(array(
			"tpldir" => "mrp/orders/mrp_order",
			"clid" => CL_MRP_ORDER
		));
	}

	/** Returns a list of possible states
		@attrib api=1
	**/
	public static function get_state_list()
	{
		return array(
			0 => t("Uus"),
			1 => t("Koostamisel"),
			2 => t("Saadetud"),
			3 => t("T&auml;psustamisel"),
			4 => t("Kinnitatud"),
			5 => t("Tagasi l&uuml;katud"),
			6 => t("T&uuml;histatud")
		);
	}

	function _get_state($arr)
	{	
		$arr["prop"]["options"] = self::get_state_list();
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
			case "workspace":
				if (!$arr["prop"]["value"] && !$arr["new"])
				{
					$ol = new object_list(array(
						"class_id" => CL_MRP_WORKSPACE,
						"lang_id" => array(),
						"site_id" => array(),
					));
					$o = $ol->begin();
					$arr["prop"]["value"] = $o->id();
				}
				break;
		}

		return $retval;
	}

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
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
			$this->db_query("CREATE TABLE aw_mrp_order(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "aw_workspace":
			case "aw_customer":
			case "aw_orderer_person":
			case "aw_seller_person":
			case "aw_mrp_case":
			case "aw_state":
			case "aw_mrp_pricelist":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				return true;
		}
	}

	function _get_customer($arr)
	{	
		$ol = new object_data_list(array(
			"class_id" => CL_CRM_COMPANY_CUSTOMER_DATA,
			"seller" => $arr["new"] ? obj($arr["request"]["ws"])->owner_co : $arr["obj_inst"]->workspace()->owner_co,
			"lang_id" => array(),
			"site_id" => array()
		), array(
			CL_CRM_COMPANY_CUSTOMER_DATA => array("buyer" => "buyer")
		));
		$ids = array_values($ol->get_element_from_all("buyer"));
		if (count($ids))
		{
			$ol = new object_list(array(
				"oid" => $ids,
				"lang_id" => array(),
				"site_id" => array()
			));
			$arr["prop"]["options"] = $ol->names();
		}
	}

	function _get_orderer_person($arr)
	{
		if (!($crel = $arr["obj_inst"]->get_customer_relation()))
		{
			return PROP_IGNORE;
		}

		if ($this->can("view", $crel->contact_person))
		{
			$arr["prop"]["options"][$crel->contact_person] = $crel->contact_person()->name();
		}
		if ($this->can("view", $crel->contact_person2))
		{
			$arr["prop"]["options"][$crel->contact_person2] = $crel->contact_person2()->name();
		}
		if ($this->can("view", $crel->contact_person3))
		{
			$arr["prop"]["options"][$crel->contact_person3] = $crel->contact_person3()->name();
		}
	}

	function _get_seller_person($arr)
	{	
		if (!($crel = $arr["obj_inst"]->get_customer_relation()))
		{
			return PROP_IGNORE;
		}

		if ($this->can("view", $crel->client_manager))
		{
			$arr["prop"]["options"][$crel->client_manager] = $crel->client_manager()->name();
		}
	}

	function _get_mrp_pricelist($arr)
	{
		$ws = $arr["new"] ? obj($arr["request"]["ws"]) : $arr["obj_inst"]->workspace();
		if (!is_oid($ws->id()))
		{
			return;
		}
		$ap = $ws->get_default_pricelist();

		if (!$ap)
		{
			return;
		}
		$arr["prop"]["options"][$ap->id()] = $ap->name();

		if (!$arr["prop"]["value"])
		{
			$arr["prop"]["value"] = $ap->id();
		}
	}

	function _get_mrp_case_view($arr)
	{
		if (!is_oid($arr["obj_inst"]->id()) || !($case = $arr["obj_inst"]->get_case()))
		{
			return PROP_IGNORE;
		}
		$arr["prop"]["value"] = html::get_change_url($case->id(), array("return_url" => get_ru()), t("Vaata"));
	}

	function _get_workspace($arr)
	{
		if ($arr["new"])
		{
			$arr["prop"]["value"] = $arr["request"]["ws"];
		}
	}
}

?>
