<?php
/*
@classinfo syslog_type=ST_CRM_OFFER_ROW relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=SYSTEM
@tableinfo aw_crm_offer_row master_index=brother_of master_table=objects index=aw_oid
@tableinfo aw_crm_offer_row_price_components index=aw_row_id,aw_price_component_id

@default table=aw_crm_offer_row
@default group=general

	@property offer type=objpicker clid=CL_CRM_OFFER field=aw_offer
	@caption Pakkumine

	@property object type=objpicker field=aw_object
	@caption Objekt

	@property unit type=objpicker clid=CL_UNIT field=aw_unit
	@caption &Uuml;hik

	@property amount type=textbox field=aw_amount
	@caption Kogus

	#	Price components are stored in separate database table and are not stored as objects.
	@property price_components type=table store=no
	@caption Hinnakomponendid

*/

class crm_offer_row extends class_base
{
	function crm_offer_row()
	{
		$this->init(array(
			"tpldir" => "applications/crm/sales/crm_offer_row",
			"clid" => CL_CRM_OFFER_ROW
		));
	}

	function do_db_upgrade($t, $f)
	{
		if ($f == "" && $t === "aw_crm_offer_row")
		{
			$this->db_query("CREATE TABLE aw_crm_offer_row(aw_oid int primary key)");
			return true;
		}
		if ($f == "" && $t === "aw_crm_offer_row_price_components")
		{
			$this->db_query("
			CREATE TABLE aw_crm_offer_row_price_components (
				aw_row_id int,
				aw_price_component_id int,
				aw_value decimal(19,4),
				PRIMARY KEY (aw_row_id, aw_price_component_id)
			);
			");
			return true;
		}

		switch($f)
		{
			//	aw_crm_offer_row

			case "aw_offer":
			case "aw_object":
			case "aw_unit":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int(11)"
				));
				return true;

			case "aw_amount":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "decimal(13,4)"
				));
				return true;

			//	aw_crm_offer_row_price_components

			case "":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => ""
				));
				return true;
		}
	}
}

?>
