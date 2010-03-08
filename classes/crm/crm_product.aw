<?php
/*
	@tableinfo kliendibaas_toode index=oid master_table=objects master_index=oid
	@default table=objects
	@default group=general

	@property toode type=textbox size=40 table=kliendibaas_toode
	@caption Toote nimetus

	@property toode_en type=textbox size=40 table=kliendibaas_toode
	@caption Inglise keelne tootenimetus
		
	@property comment type=textarea field=comment
	@caption Kirjeldus

	@property kood type=textbox size=8 table=kliendibaas_toode
	@caption Toote kood	

	@classinfo no_status=1 syslog_type=ST_CRM_PRODUCT maintainer=markop
	
*/


/*
CREATE TABLE `kliendibaas_toode` (
  `oid` int(11) NOT NULL default '0',
  `kood` varchar(30) default NULL,
  `toode` text,
  `toode_en` text,
  `kirjeldus` text,
  PRIMARY KEY  (`oid`),
  UNIQUE KEY `oid` (`oid`),
  KEY `kood_i` (`kood`)
) TYPE=MyISAM

*/

class crm_product extends class_base
{
	function crm_product()
	{
		$this->init(array(
			'clid' => CL_CRM_PRODUCT,
		));
	}
	function get_property($arr)
	{
		$data = &$arr['prop'];
		$retval = PROP_OK;
		switch($data["name"])
		{
			case 'name':
				$retval = PROP_IGNORE;
				break;			
		}
		return  $retval;
	}
	
	
	function set_property($arr)
	{
		$data = &$arr["prop"];
		$retval = PROP_OK;
		$form = &$arr["request"];
		switch($data["name"])
		{
			case 'kood':
				$arr["obj_inst"]->set_name(($form['kood'] ? ''.$form['kood'].' ' : '').$form['toode']);
				break;
			break;
		};
		return $retval;
	}		
	
}
?>
