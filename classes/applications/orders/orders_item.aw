<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/orders/orders_item.aw,v 1.6 2007/11/23 11:00:53 markop Exp $
// orders_item.aw - Tellimuse rida 
/*

@classinfo syslog_type=ST_ORDERS_ITEM relationmgr=yes maintainer=markop
@tableinfo aw_orders_item index=oid master_table=objects master_index=brother_of

@default table=aw_orders_item
@default group=general

@property name type=textbox table=objects
@caption Toote nimetus

@property product_code type=textbox
@caption Kood

@property product_unit type=textbox
@caption M&otilde;&otilde;t&uuml;hik

@property product_color type=textbox
@caption Värvus

@property product_size type=textbox
@caption Suurus

@property product_count type=textbox
@caption Kogus

@property product_count_undone type=textbox
@caption Tarnimata kogus

@property product_price type=textbox
@caption Hind

@property product_duedate type=textbox
@caption Soovitav tarne t&auml;itmine

@property product_bill type=textbox
@caption Tarne t&auml;itmine/arve nr

@property product_page type=textbox
@caption Lehekülg

@property product_image type=textbox
@caption Pilt

#udef muutujad

@property udef_textbox1 type=textbox
@property udef_textbox2 type=textbox
@property udef_textbox3 type=textbox
@property udef_textbox4 type=textbox
@property udef_textbox5 type=textbox
@property udef_textbox6 type=textbox
@property udef_textbox7 type=textbox

@property udef_textarea1 type=textarea
@property udef_textarea2 type=textarea
@property udef_textarea3 type=textarea
@property udef_textarea4 type=textarea
@property udef_textarea5 type=textarea

@property udef_picker1 type=classificator
@property udef_picker2 type=classificator
@property udef_picker3 type=classificator
@property udef_picker4 type=classificator
@property udef_picker5 type=classificator

*/

class orders_item extends class_base
{
	function orders_item()
	{
		// change this to the folder under the templates folder, where this classes templates will be, 
		// if they exist at all. Or delete it, if this class does not use templates
		$this->init(array(
			"clid" => CL_ORDERS_ITEM
		));
	}

	//////
	// class_base classes usually need those, uncomment them if you want to use them

	/*
	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "order_form_id":
				$prop["value;
			break;
		};
		return $retval;
	}*/
	
	/*
	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{

		}
		return $retval;
	}	
	*/
	function do_db_upgrade($t, $f)
	{
		switch($f)
		{
			case "product_count_undone":
			case "product_duedate":
			case "product_bill":
			case "product_unit":
			case "udef_textbox1":
			case "udef_textbox2":
			case "udef_textbox3":
			case "udef_textbox4":
			case "udef_textbox5":
			case "udef_textbox6":
			case "udef_textbox7":
			case "udef_textarea1":
			case "udef_textarea2":
			case "udef_textarea3":
			case "udef_textarea4":
			case "udef_textarea5":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "text"
				));
				break;
			case "udef_picker1":
			case "udef_picker2":
			case "udef_picker3":
			case "udef_picker4":
			case "udef_picker5":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				break;
		}
		return true;
	}
}
?>
