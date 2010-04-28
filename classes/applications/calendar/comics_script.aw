<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/calendar/comics_script.aw,v 1.3 2007/12/06 14:32:55 kristo Exp $
// comics_script.aw - Koomiksi skript 
/*

@classinfo syslog_type=ST_COMICS_SCRIPT no_status=1 maintainer=kristo

@default table=objects
@default group=general

@property name type=textbox
@caption Pealkiri

@property comment type=textarea
@caption Sisukokkuvõte

@property content type=textarea cols=60 rows=20 field=meta
@caption Sisu

*/

class comics_script extends class_base
{
	const AW_CLID = 915;

	function comics_script()
	{
		$this->init(array(
			"clid" => CL_COMICS_SCRIPT
		));
	}
}
?>
