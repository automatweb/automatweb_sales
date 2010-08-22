<?php
/*
	@classinfo no_status=1
	@tableinfo kliendibaas_linn index=oid master_table=objects master_index=oid

	@default table=objects
	@default group=general

	@property name type=textbox size=20
	@caption Linna nimetus

	@default field=meta
	@property country type=relpicker reltype=RELTYPE_COUNTRY
	@caption Riik

	@property area type=relpicker reltype=RELTYPE_AREA
	@caption Piirkond

	@property county type=relpicker reltype=RELTYPE_COUNTY
	@caption Maakond

	@property comment type=textarea cols=40 rows=3 table=objects field=comment
	@caption Kommentaar

@groupinfo transl caption=T&otilde;lgi
@default group=transl

	@property transl type=callback callback=callback_get_transl store=no
	@caption T&otilde;lgi


@reltype COUNTRY value=1 clid=CL_CRM_COUNTRY
@caption Riik

@reltype AREA value=2 clid=CL_CRM_AREA
@caption Piirkond

@reltype COUNTY value=3 clid=CL_CRM_COUNTY
@caption Maakond





	//@property location type=textarea
	//@caption Asukoha kirjeldus

	//@default table=kliendibaas_linn

*/
/*
CREATE TABLE `kliendibaas_linn` (
  `oid` int(11) NOT NULL default '0',
  `name` varchar(255) default NULL,
  `comment` text,
  `location` text,
  PRIMARY KEY  (`oid`),
  UNIQUE KEY `oid` (`oid`)
) TYPE=MyISAM;
*/
class crm_city extends class_base
{
	function crm_city()
	{
		$this->init(array(
			'clid' => CL_CRM_CITY,
		));
		$this->trans_props = array(
			"name", "comment"
		);
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "transl":
				$this->trans_save($arr, $this->trans_props);
				break;
		}
		return $retval;
	}

	function callback_get_transl($arr)
	{
		return $this->trans_callback($arr, $this->trans_props);
	}
}
