<?php
/*
@classinfo syslog_type=ST_SHOP_PRODUCT_PURVEYANCE relationmgr=yes no_status=1 prop_cb=1
@tableinfo aw_shop_product_purveyance master_index=brother_of master_table=objects index=aw_oid

@default table=aw_shop_product_purveyance
@default group=general

@property code type=textbox
@caption Kood

#	{
#		DEPRECATED PROPERTIES! ONLY HERE TO ENSURE BACKWARD COMPATIBILITY
		@property product type=hidden field=aw_object
		@property packaging type=hidden field=aw_object
		@property packet type=hidden field=aw_object
#	}

@property object type=objpicker field=aw_object
@caption Tarnitav artikkel

@property company type=relpicker reltype=RELTYPE_COMPANY
@caption Tarnija

@property warehouse type=relpicker reltype=RELTYPE_WAREHOUSE
@caption Ladu

@property weekdays type=weekdays multiple=1 field=aw_weekdays
@caption Tarnep&auml;evad

@property time_from type=timepicker field=aw_time_from
@caption Tarneaja algus

@property time_to type=timepicker field=aw_time_to
@caption Tarneaja lõpp

@property days type=textbox
@caption Tarneaeg p&auml;evades

###

@reltype COMPANY value=2 clid=CL_CRM_COMPANY
@caption Tarnija

@reltype WAREHOUSE value=3 clid=CL_SHOP_WAREHOUSE
@caption Ladu
*/

class shop_product_purveyance extends class_base
{
	function shop_product_purveyance()
	{
		$this->init(array(
			"tpldir" => "applications/shop/shop_product_purveyance",
			"clid" => CL_SHOP_PRODUCT_PURVEYANCE
		));
	}

	function _get_packet($arr)
	{
		return PROP_IGNORE;
	}

	function _get_product($arr)
	{
		return PROP_IGNORE;
	}

	function _get_packaging($arr)
	{
		return PROP_IGNORE;
	}

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_shop_product_purveyance(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "aw_object":
			case "company":
			case "warehouse":
			case "days":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				return true;
				
			case "aw_time_from":
			case "aw_time_to":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "mediumint"
				));
				return true;

			case "aw_weekdays":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "tinyint"
				));
				return true;

			case "code":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "varchar(100)"
				));
				return true;
		}
	}
}
