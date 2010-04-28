<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/layout/show_site_content.aw,v 1.8 2008/02/05 09:23:30 kristo Exp $
/*

@classinfo syslog_type=ST_SITE_CONTENT maintainer=kristo

@default table=objects
@default group=general

*/

class show_site_content extends class_base
{
	const AW_CLID = 182;

	function show_site_content()
	{
		$this->init(array(
			'clid' => CL_SITE_CONTENT
		));
	}

	////
	// !this will be called if the object is put in a document by an alias and the document is being shown
	// parameters
	//    alias - array of alias data, the important bit is $alias[target] which is the id of the object to show
	function parse_alias($args)
	{
		extract($args);
		return $this->show(array('id' => $alias['target']));
	}

	////
	// !this shows the object. 
	function show($arr)
	{
		extract($arr);
		$pd = get_instance("layout/active_page_data");
		$mned = get_instance("contentmgmt/site_show");

 		if (($txt = $pd->get_text_content()) != "")
		{
			return $txt;
		}
		else
		{
			return $mned->show_documents($pd->get_active_section(), 0);
		}
	}
}
?>
