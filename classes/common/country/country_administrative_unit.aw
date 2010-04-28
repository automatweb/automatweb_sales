<?php

namespace automatweb;
/*

@classinfo syslog_type=ST_COUNTRY_ADMINISTRATIVE_UNIT relationmgr=yes no_comment=1 no_status=1 maintainer=voldemar

@tableinfo aw_administrative_unit index=aw_oid master_index=brother_of master_table=objects

@default table=objects
@default group=general
	@property name type=textbox
	@caption Nimi

	@property subclass type=text
	@caption Tüüp

	@property complete_name type=textbox field=meta method=serialize
	@caption T&auml;isnimi

	@property alt_name type=textbox field=meta method=serialize
	@caption Paralleelnimi

	@property parent type=text
	@comment Halduspiirkond, millesse käesolev halduspiirkond kuulub
	@caption Kõrgem halduspiirkond

	@property parent_show type=text store=no
	@caption Kõrgem halduspiirkond

	@property parent_select type=relpicker reltype=RELTYPE_PARENT_ADMINISTRATIVE_UNIT clid=CL_COUNTRY_ADMINISTRATIVE_UNIT,CL_COUNTRY_CITY,CL_COUNTRY_CITYDISTRICT store=no
	@comment Halduspiirkond, millesse käesolev halduspiirkond kuulub
	@caption Vali kõrgem halduspiirkond

@default table=aw_administrative_unit
	@property ext_id_1 type=textbox datatype=int
	@caption Identifikaator v&auml;lises s&uuml;steemis 1

	@property administrative_structure type=hidden datatype=int
	@property indexed type=hidden datatype=int default=0


// --------------- RELATION TYPES ---------------------

@reltype PARENT_ADMINISTRATIVE_UNIT value=1 clid=CL_COUNTRY_ADMINISTRATIVE_UNIT,CL_COUNTRY_CITY,CL_COUNTRY_CITYDISTRICT
@caption Kõrgem halduspiirkond

*/

class country_administrative_unit extends class_base
{
	const AW_CLID = 953;

	function country_administrative_unit ()
	{
		$this->init(array(
			"tpldir" => "common/country",
			"clid" => CL_COUNTRY_ADMINISTRATIVE_UNIT
		));
	}

	function get_property ($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		$this_object = $arr["obj_inst"];

		switch ($prop["name"])
		{
			case "parent_show":
				break;

			case "subclass":
				if (is_oid ($prop["value"]))
				{
					$administrative_division_or_unit = obj ($prop["value"]);
					$prop["value"] = $administrative_division_or_unit->name ();
				}
				else
				{
					return PROP_IGNORE;
				}
				break;
		}

		return $retval;
	}

	function set_property ($arr = array ())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		$this_object = $arr["obj_inst"];

		switch($prop["name"])
		{
			case "parent_select":
				if (is_oid ($prop["value"]))
				{
					$parent = obj ($prop["value"]);
					$this_object->set_parent ($parent->id ());
					$this_object->set_prop ("parent_show", $parent->name ());
				}
				break;
		}

		return $retval;
	}

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function do_db_upgrade($table, $field, $query, $error)
	{
		$return_val = false;

		if ("aw_administrative_unit" === $table)
		{
			if (empty($field))
			{
				$this->db_query("CREATE TABLE `aw_administrative_unit` (
					`aw_oid` int(11) UNSIGNED NOT NULL default '0',
					`administrative_structure` int(11) UNSIGNED NOT NULL default '0',
					`ext_id_1` int(11) UNSIGNED default NULL,
					`indexed` int(1) default '0',
					PRIMARY KEY  (`aw_oid`)
				) ");
				$return_val = true;
			}
		}

		return $return_val;
	}
}

?>
