<?php

namespace automatweb;
/*

HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_NEW, CL_REALESTATE_GARAGE, on_create)
HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_DELETE, CL_REALESTATE_PROPERTY, on_delete)

@classinfo syslog_type=ST_REALESTATE_GARAGE relationmgr=yes no_comment=1 no_status=1 trans=1 maintainer=voldemar
@extends applications/realestate_management/realestate_property

@tableinfo realestate_property index=oid master_table=objects master_index=oid

@default table=objects
@default group=grp_detailed
	@property year_built type=select
	@caption Ehitusaasta

	@property legal_status type=classificator table=realestate_property
	@caption Omandivorm

	@property total_floor_area type=textbox table=realestate_property
	@caption &uuml;ldpind

	@property has_alarm_installed type=checkbox ch_value=1 field=meta method=serialize
	@caption Signalisatsioon

	@property condition type=classificator table=realestate_property
	@caption Valmidus

*/

classload("applications/realestate_management/realestate_property");

class realestate_garage extends realestate_property
{
	const AW_CLID = 946;

	function realestate_garage()
	{
		$this->init(array(
			"tpldir" => "applications/realestate_management/realestate_property",
			"clid" => CL_REALESTATE_GARAGE
		));
	}

	function callback_on_load ($arr)
	{
		parent::callback_on_load ($arr);
	}

	function get_property($arr)
	{
		$retval = PROP_OK;
		$retval = parent::get_property ($arr);
		$prop = &$arr["prop"];

		switch($prop["name"])
		{
			case "year_built":
				$empty = array ("" => "");
				$centuries = range (19,11);
				$years = range (date ("Y"), 1901);

				foreach ($years as $year)
				{
					$options[$year] = $year;
				}

				foreach ($centuries as $century)
				{
					$options[($century - 1)*100] = sprintf (t("%s saj."), $century);
				}

				$prop["options"] = $options;
				break;
		}

		return $retval;
	}

	function set_property($arr = array())
	{
		$retval = PROP_OK;
		$retval = parent::set_property ($arr);
		$prop = &$arr["prop"];

		switch($prop["name"])
		{
			case "legal_status":
				if (empty ($prop["value"]))
				{
					$prop["error"] = t("Kohustuslik v&auml;li");
					return PROP_ERROR;
				}
				break;

			case "year_built":
				if (empty ($prop["value"]))
				{
					$prop["error"] = t("Kohustuslik v&auml;li");
					return PROP_ERROR;
				}
				break;
		}

		return $retval;
	}

	function callback_mod_reforb(&$arr)
	{
		parent::callback_mod_reforb ($arr);
		$arr["post_ru"] = post_ru();
	}

	function callback_post_save ($arr)
	{
		parent::callback_post_save ($arr);
	}

	function request_execute ($o)
	{
		return parent::request_execute ($o);
	}

	function parse_alias($arr)
	{
		return $this->show(array("id" => $arr["alias"]["target"]));
	}

	function on_create ($arr)
	{
		parent::on_create ($arr);
	}

	// @attrib name=export_xml
	// @param id required type=int
	// @param no_declaration optional
	function export_xml ($arr)
	{
		return parent::export_xml ($arr);
	}

/**
	@attrib name=pictures_view nologin=1
	@param id required type=int
**/
	function pictures_view ($arr)
	{
		echo parent::pictures_view ($arr);
		exit;
	}

/**
	@attrib name=print nologin=1
	@param id required type=int
	@param contact_type required
	@param show_pictures optional
	@param view_type optional
	@param return_url optional
**/
	function print_view ($arr)
	{
		return parent::print_view ($arr);
	}

	// @attrib name=view
	// @param id required type=int
	// @param view_type required
	// @param return_url optional
	function view ($arr)
	{
		return parent::view ($arr);
	}

	// @attrib name=get_property_data
	// @param id required type=int
	function get_property_data ($arr)
	{
		return parent::get_property_data ($arr);
	}
}

?>
