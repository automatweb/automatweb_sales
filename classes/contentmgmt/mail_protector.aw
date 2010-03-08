<?php
/*
@classinfo  maintainer=kristo
*/

class mail_protector
{
	function protect($str)
	{
		if ( aw_ini_get("menuedit.protect_emails_v2") == 1 || aw_ini_get("plugin.protect_emails") == 1 )
		{
			return $this->protect_v2($str);
		}
		
		$js = "
		<script type=\"text/javascript\">
		function aw_proteml(n,d,f = 0)
		{
			var e = (n + \"@\" + d);
			if (!f) { f = e; }
			document.write( \"<a \"+\"hr\"+\"ef=\\\"mailto:\" + e + \"\\\">\" + f + \"</a>\");
		}
		</script>
		";
		$xhtml_slash = '';
		if (aw_ini_get("content.doctype") == "xhtml")
		{
			$xhtml_slash = " /";
		}
		// also try to do already existing email links that have as text the mail address
		$repl = "<script type=\"text/javascript\">aw_proteml(\"\\3\",\"\\4\");</script>";

		$str = preg_replace("/<a([^>]*) href=([\"|'])mailto:([^\"']*)@([\w|\.]*)([\"|'])([^>]*)>\\3@\\4<\/a>/imsU",$repl, $str);

		// finally links that go to mail but have the text different from the address
		$repl = "<script type=\"text/javascript\">aw_proteml(\"\\3\",\"\\4\",\"\\7\");</script>";
		$str = preg_replace("/<a([^>]*)href=([\"|'])mailto:([^\"']*)@(.*)([\"|'])(.*)>(.*)<\/a>/imsU",$repl, $str);

		$repl = "\\1<script type=\"text/javascript\">aw_proteml(\"\\2\",\"\\3\");</script>";
		$punct = preg_quote("!#$%&'()*+,/:;<=>?@[\]_`{|}~", "/");
		$str = preg_replace("/([\s|^|>|^".$punct."])([-_.[:alnum:]]+)@([-_[:alnum:]][-._[:alnum:]]+\.[[:alpha:]]{2,6})/",$repl, $str);
		return $str;
	}
	
	function protect_v2($str)
	{
		$xhtml_slash = '';
		if (aw_ini_get("content.doctype") == "xhtml")
		{
			$xhtml_slash = " /";
		}
		// also try to do already existing email links that have as text the mail address
		$repl = '<span class="_aw_email">\\3<img src="'.aw_ini_get("baseurl").'/automatweb/images/at.png" alt="" style="vertical-align: middle;"'.$xhtml_slash.'>\\4</span>';

		$str = preg_replace("/<a([^>]*) href=([\"|'])mailto:([^\"']*)@([\w|\.]*)([\"|'])([^>]*)>\\3@\\4<\/a>/imsU",$repl, $str);

		// finally links that go to mail but have the text different from the address
		$repl = '<span class="_aw_email">\\3<img src="'.aw_ini_get("baseurl").'/automatweb/images/at.png" alt="" style="vertical-align: middle;"'.$xhtml_slash.'>\\4 \\7</span>';
		$str = preg_replace("/<a([^>]*)href=([\"|'])mailto:([^\"']*)@(.*)([\"|'])(.*)>(.*)<\/a>/imsU",$repl, $str);

		$repl = '\\1<span class="_aw_email">\\2<img src="'.aw_ini_get("baseurl").'/automatweb/images/at.png" alt="" style="vertical-align: middle;"'.$xhtml_slash.'>\\3</span>';
		$punct = preg_quote("!#$%&'()*+,/:;<=>?@[\]_`{|}~", "/");
		$str = preg_replace("/([\s|^|>|^".$punct."])([-_.[:alnum:]]+)@([-_[:alnum:]][-._[:alnum:]]+\.[[:alpha:]]{2,6})/",$repl, $str);
		return $str;
	}
}
?>
