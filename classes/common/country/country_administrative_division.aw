<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_COUNTRY_ADMINISTRATIVE_DIVISION relationmgr=yes no_comment=1 no_status=1 maintainer=voldemar

@default table=objects
@default group=general
	@property administrative_structure type=hidden field=meta method=serialize

	@property type type=select field=meta method=serialize
	@comment Haldusüksuse tüüp
	@caption Tüüp

	@property parent_division_show type=text field=meta method=serialize
	@caption Kõrgem haldusüksus

	@property parent_division type=relpicker reltype=RELTYPE_PARENT_ADMINISTRATIVE_DIVISION clid=CL_COUNTRY,CL_COUNTRY_ADMINISTRATIVE_DIVISION,CL_COUNTRY_ADMINISTRATIVE_UNIT,CL_COUNTRY_CITY,CL_COUNTRY_CITYDISTRICT automatic=1 field=meta method=serialize
	@comment Haldusüksus, millesse käesolev haldusüksus kuulub
	@caption Kõrgem haldusüksus

	@property division_name type=textbox
	@caption Haldusüksuse nimetus

	@property division_name_as_suffix type=textbox
	@caption Haldusüksuse nimetus lauses

	@property ext_id_1 type=textbox
	@caption Identifikaator v&auml;lises s&uuml;steemis 1

	@property jrk type=textbox datatype=int
	@comment Positiivne täisarv (vahemikus 1 kuni 1000000)
	@caption Järjekord


// --------------- RELATION TYPES ---------------------

@reltype PARENT_ADMINISTRATIVE_DIVISION value=1 clid=CL_COUNTRY_ADMINISTRATIVE_DIVISION,CL_COUNTRY_ADMINISTRATIVE_UNIT,CL_COUNTRY_CITY,CL_COUNTRY_CITYDISTRICT,CL_COUNTRY
@caption Kõrgem haldusüksus

*/

require_once(aw_ini_get("basedir") . "/classes/common/address/as_header.aw");

class country_administrative_division extends class_base
{
	const AW_CLID = 1034;

	function country_administrative_division()
	{
		$this->init(array(
			"tpldir" => "common/country",
			"clid" => CL_COUNTRY_ADMINISTRATIVE_DIVISION
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
			case "parent_division":
				$divisions = aw_global_get ("address_system_parent_select_divisions");

				if (is_array ($divisions))
				{
					$prop["options"] = $divisions;
				}
				break;

			case "parent_division_show":
				if (aw_global_get ("address_system_administrative_structure"))
				{//!!! vaja releditori muuta et n2idataks kui ainult table_fieldsis on prop aga props-is pole
					$parent = $this_object->get_first_obj_by_reltype("RELTYPE_PARENT_ADMINISTRATIVE_DIVISION");
					$prop["value"] = $parent->name ();
				}
				else
				{
					return PROP_IGNORE;
				}
				break;

			case "type":
				$prop["options"] = array (
					CL_COUNTRY_ADMINISTRATIVE_UNIT => t("Haldusüksus"),
					CL_COUNTRY_CITY => t("Linna tüüpi haldusüksus"),
					CL_COUNTRY_CITYDISTRICT => t("Linnaosa tüüpi haldusüksus"),
				);
				break;
		}

		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		$this_object =& $arr["obj_inst"];

		switch($prop["name"])
		{
			case "parent_division":
				if (is_oid ($prop["value"]))
				{
					$parent = obj ($prop["value"]);
					$this_object->set_prop ("parent_division_show", $parent->name());
				}
				break;
		}

		return $retval;
	}

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}
}

?>
