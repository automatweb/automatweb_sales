<?php

namespace automatweb;
/*
	@classinfo  maintainer=markop
	@tableinfo kliendibaas_riik index=oid master_table=objects master_index=oid

	@default table=objects
	@default group=general

	@property name type=textbox size=20
	@caption Riigi nimetus

	@property area_code type=textbox size=20 field=meta method=serialize
	@caption Suunakood

	@property comment type=textarea field=comment
	@caption Kommentaar

	@default table=kliendibaas_riik

	@property name_en type=textbox size=20
	@caption Nimetus inglise keeles

	@property name_native type=textbox size=20
	@caption Nimetus kohalikus keeles

	@classinfo no_status=1 syslog_type=ST_SRM_COUNTRY

@groupinfo transl caption=T&otilde;lgi
@default group=transl
	
	@property transl type=callback callback=callback_get_transl store=no
	@caption T&otilde;lgi

*/

/*

CREATE TABLE `kliendibaas_riik` (
  `oid` int(11) NOT NULL default '0',
  `name` varchar(255) default NULL,
  `comment` text,
  `name_en` text,
  `name_native` text,
  `languages` text,
  `location` text,
  `lyhend` varchar(20) default NULL,
  PRIMARY KEY  (`oid`),
  UNIQUE KEY `oid` (`oid`)
) TYPE=MyISAM;

*/

class crm_country extends class_base
{
	const AW_CLID = 134;

	function crm_country()
	{
		$this->init(array(
			'clid' => CL_CRM_COUNTRY,
		));
		$this->trans_props = array(
			"name","comment"
		);
	}

	function set_property($arr = array())
	{
		$data = &$arr["prop"];
		$retval = PROP_OK;
		switch($data["name"])
		{
			case "transl":
				$this->trans_save($arr, $this->trans_props);
				break;

			case "price_cur":
				$arr["obj_inst"]->set_meta("cur_prices", $arr["request"]["cur_prices"]);
				break;

		}
		return $retval;
	}

	function callback_mod_tab($arr)
	{
		if ($arr["id"] == "transl" && aw_ini_get("user_interface.content_trans") != 1)
		{
			return false;
		}
		return true;
	}

	function callback_get_transl($arr)
	{
		return $this->trans_callback($arr, $this->trans_props);
	}
}
?>
