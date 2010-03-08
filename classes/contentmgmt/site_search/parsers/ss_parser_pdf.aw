<?php
/*
@classinfo  maintainer=kristo
*/
class ss_parser_pdf extends ss_parser_base
{
	function ss_parser_pdf($url)
	{
		$this->url = $url;
		$this->url_parsed = parse_url($this->url);
		$this->content = NULL;
		$this->headers = NULL;
	}

	function get_links()
	{
		return array(); // docs contain no links
	}

	function get_text_content()
	{
echo "enter get_text_content fpr pdf <br>\n";
flush();
		$this->_init_content();

		// write to temp file
		$fn = tempnam(aw_ini_get("server.tmpdir"), "ss-pdf2txt");
		$o_fn = tempnam(aw_ini_get("server.tmpdir"), "ss-pdf2txt-o");

		$this->_put($fn,$this->content);
echo "put file <br>\n";
flush();
		$cmd = aw_ini_get("server.pdftotext")." $fn $o_fn";

		$txt = `$cmd`;
echo "cmd = $cmd , res = $txt <br>\n";
flush();

		$fc = $this->_get($o_fn);

		unlink($fn);
		unlink($o_fn);
		return $fc;
	}

	function get_last_modified()
	{
		$ts = NULL;
		$lines = explode("\n", $this->headers);
		foreach($lines as $line)
		{
			list($nm, $val) = explode(" ", $line, 2);
			//echo "nm = $nm, val = $val <br>";
			if (trim($nm) == "Last-Modified:")
			{
				$ts = strtotime(trim($val));
				//echo "from $val got timestamp ".date("d.m.Y H:i:s", $ts)." <br>";
			}
		}
		return $ts;
	}

	function get_title()
	{
		$this->_init_content();

		return basename($this->url);
	}
}
?>
