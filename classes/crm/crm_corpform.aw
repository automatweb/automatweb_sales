<?php

/*
	@tableinfo kliendibaas_ettevotlusvorm index=oid master_table=objects master_index=oid

	@default table=objects
	@default group=general

	@property shortname type=textbox field=shortname size=10 table=kliendibaas_ettevotlusvorm
	@caption L�hend

	@property comment type=textarea field=comment cols=40 rows=3
	@caption Kommentaar

//	@default table=kliendibaas_ettevotlusvorm

//	@property vorm type=textbox size=10
//	@caption vorm

	@classinfo no_status=1
*/


/*
CREATE TABLE `kliendibaas_ettevotlusvorm` (
  `oid` int(11) NOT NULL default '0',
  `name` varchar(255) default NULL,
  `shortname` varchar(25) default,
  `vorm` varchar(255) default NULL,
  `comment` text,
  PRIMARY KEY  (`oid`),
  UNIQUE KEY `oid` (`oid`)
) TYPE=MyISAM;

*/

class crm_corpform extends class_base
{
	function crm_corpform()
	{
		$this->init(array(
			'clid' => CL_CRM_CORPFORM,
		));
	}

}
