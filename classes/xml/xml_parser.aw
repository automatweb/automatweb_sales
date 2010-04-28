<?php
// $Header: /home/cvs/automatweb_dev/classes/xml/xml_parser.aw,v 1.4 2008/01/31 13:55:43 kristo Exp $
// xml_parser.aw - Wrapper for the xml parsing stuff
// right now this class can only bitch and die
// in the future it should wrap all the parser functions

namespace automatweb;

class xml_parser
{
	function xml_parser()
	{
	}

	function bitch_and_die(&$parser,&$contents)
	{
		$err = xml_error_string(xml_get_error_code($parser));
		print "Viga lähteandmetes<br />";
		print "<font color='red'><strong>$err</strong></font><br />";
		$b_idx = xml_get_current_byte_index($parser);
		$frag = substr($contents,$b_idx - 100, 200);
		$pref = htmlspecialchars(substr($frag,0,100));
		$suf = htmlspecialchars(substr($frag,101));
		$offender = htmlspecialchars(substr($frag,100,1));
		print "Tekstifragment: <pre>" .  $pref . "<font color='red'><strong> ---&gt;&gt;$offender&lt;&lt;---</strong></font>$suf" . "</pre>";
		die();
	}
};
?>
