<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/crm/crm_area.aw,v 1.9 2008/10/01 11:34:46 markop Exp $
// crm_area.aw - Piirkond 
/*


@classinfo syslog_type=ST_CRM_AREA relationmgr=yes no_status=1 prop_cb=1 maintainer=markop

@default table=objects
@default group=general

@default field=meta

	@property country type=relpicker reltype=RELTYPE_COUNTRY
	@caption Riik

	@property comment type=textarea cols=40 rows=3 table=objects field=comment
	@caption Kommentaar
		
@groupinfo transl caption=T&otilde;lgi
@default group=transl

	@property transl type=callback callback=callback_get_transl store=no
	@caption T&otilde;lgi

@reltype COUNTRY value=1 clid=CL_CRM_COUNTRY
@caption Riik

*/

class crm_area extends class_base
{
	const AW_CLID = 1107;

	function crm_area()
	{
		$this->init(array(
			"tpldir" => "applications/crm/crm_area",
			"clid" => CL_CRM_AREA
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

	function callback_mod_reforb(&$arr)
	{
		$arr["post_ru"] = post_ru();
	}

	////////////////////////////////////
	// the next functions are optional - delete them if not needed
	////////////////////////////////////

	/** this will get called whenever this object needs to get shown in the website, via alias in document **/
	function show($arr)
	{
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");
		$this->vars(array(
			"name" => $ob->prop("name"),
		));
		return $this->parse();
	}
	
	function callback_get_transl($arr)
	{
		return $this->trans_callback($arr, $this->trans_props);
	}

//-- methods --//
}
?>
