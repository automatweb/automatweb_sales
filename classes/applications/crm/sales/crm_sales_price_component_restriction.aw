<?php
/*
@classinfo syslog_type=ST_CRM_SALES_PRICE_COMPONENT_RESTRICTION relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=SYSTEM
@tableinfo aw_crm_sales_price_component_restriction master_index=brother_of master_table=objects index=aw_oid

@default table=aw_crm_sales_price_component_restriction
@default group=general

	@property has_lower_tolerance type=hidden field=aw_has_lower_tolerance
	@property has_upper_tolerance type=hidden field=aw_has_upper_tolerance

	@property price_component type=objpicker clid=CL_CRM_SALES_PRICE_COMPONENT field=aw_price_component
	@caption Hinnakomponent

	@property subject type=objpicker field=aw_subject
	@caption Objekt, millele piirang m&otilde;jub

	@property lower_tolerance type=textbox maxlength=20 field=aw_lower_tolerance
	@caption Alumine tolerants

	@property upper_tolerance type=textbox maxlength=20 field=aw_upper_tolerance
	@caption &Uuml;lemine tolerants

	@property compulsory type=checkbox field=aw_compulsory
	@caption Kohustuslik

*/

class crm_sales_price_component_restriction extends class_base
{
	public function crm_sales_price_component_restriction()
	{
		$this->init(array(
			"tpldir" => "applications/crm/sales/crm_sales_price_component_restriction",
			"clid" => CL_CRM_SALES_PRICE_COMPONENT_RESTRICTION
		));
	}

	public function _get_subject($arr)
	{
		$arr["prop"]["clid"] = array(CL_CRM_SECTION, CL_CRM_PERSON_WORK_RELATION, CL_CRM_PROFESSION);

		return PROP_OK;
	}

	public function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_crm_sales_price_component_restriction(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "aw_lower_tolerance":
			case "aw_upper_tolerance":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "decimal(19,4)"
				));
				return true;

			case "aw_price_component":
			case "aw_subject":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int(11)"
				));
				return true;

			case "aw_has_lower_tolerance":
			case "aw_has_upper_tolerance":
			case "aw_compulsory":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "boolean"
				));
				return true;
		}
	}
}

?>
