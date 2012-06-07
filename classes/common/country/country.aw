<?php
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

	@property install_estonia type=text no_caption=1

// --------------- RELATION TYPES ---------------------

@reltype ADMINISTRATIVE_STRUCTURE value=1 clid=CL_COUNTRY_ADMINISTRATIVE_STRUCTURE
@caption Haldusjaotus

*/

class country extends class_base
{
	function country()
	{
		$this->init(array(
			"tpldir" => "common/country",
			"clid" => CL_COUNTRY
		));
	//	include("G:\htdocs\automatweb_sales\scripts\create_scripts\administrative_structures\ee.aw");
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
			case "install_estonia":
				if($this->can("view" , $arr["obj_inst"]->prop("administrative_structure")))
				{
					return PROP_IGNORE;
				}
				$prop["value"] = html::href(array(
					'url' => $this->mk_my_orb("install_estonia", array("id" => $arr["obj_inst"]->id(), "return_url" => get_ru())),
					'caption' => t("installi eesti aadressisysteem"),
				));
		}

		return $retval;
	}

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function install_estonia($arr)
	{
		$o = obj($arr["id"]);
		$country_oid = $arr["id"];
		$parent_oid = $arr["id"];

		require_once $GLOBALS["aw_dir"]."scripts/create_scripts/administrative_structures/ee.aw";

		print html::href(array(
			'url' => $arr["return_url"],
			'caption' => t("Tagasi"),
		));
		die();
	}
}

?>
