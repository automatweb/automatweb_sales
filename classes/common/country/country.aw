<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_COUNTRY relationmgr=yes no_comment=1 no_status=1 maintainer=voldemar prop_cb=1

@groupinfo grp_settings caption="Seaded"


@default table=objects
@default field=meta
@default method=serialize
@default group=general

@default group=grp_settings
	@property administrative_structure type=relpicker reltype=RELTYPE_ADMINISTRATIVE_STRUCTURE clid=CL_COUNTRY_ADMINISTRATIVE_STRUCTURE
	@caption Haldusjaotus

	@property code type=textbox
	@comment Kahetäheline riigi kood (ISO 3166-1 alpha-2)
	@caption Kood


// --------------- RELATION TYPES ---------------------

@reltype ADMINISTRATIVE_STRUCTURE value=1 clid=CL_COUNTRY_ADMINISTRATIVE_STRUCTURE
@caption Haldusjaotus

*/

class country extends class_base
{
	const AW_CLID = 949;

	function country()
	{
		$this->init(array(
			"tpldir" => "common/country",
			"clid" => CL_COUNTRY
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
			case "xxx":
				$addresses_using_this = "";

				if ( ($addresses_using_this > 0) and (is_oid ($prop["value"])) )
				{
					$prop["error"] = sprintf (t("%s aadressi kasutab hetkel valitud haldusjaotust! Muudatuste salvestamisel ..."), $addresses_using_this);//!!! t2psustada mis juhtub kui uus struktuur m22rata.
				}
				break;
		}

		return $retval;
	}

	function callback_mod_reforb(&$arr)
	{
		$arr["post_ru"] = post_ru();
	}
}

?>
