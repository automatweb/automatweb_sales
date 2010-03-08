<?php
/*
@classinfo  maintainer=kristo
*/
class comment_list extends aw_template
{
	function show($args)
	{
		global $XX1;
		if ($XX1)
		{
			print "in plugin.comment_list<br />";
			print "<pre>";
			print_r($args);
			print "</pre>";
		};
	}

}
?>
