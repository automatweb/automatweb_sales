<?php
// $Header: /home/cvs/automatweb_dev/classes/contentmgmt/folder_context.aw,v 1.2 2008/01/31 13:52:14 kristo Exp $
// folder_context.aw - Kontekst 
/*

@classinfo syslog_type=ST_FOLDER_CONTEXT relationmgr=yes no_comment=1  maintainer=kristo

@default table=objects
@default group=general

*/

class folder_context extends class_base
{
	function folder_context()
	{
		$this->init(array(
			"tpldir" => "contentmgmt/folder_context",
			"clid" => CL_FOLDER_CONTEXT
		));
	}
}
?>
