<?php
/*
@classinfo  maintainer=kristo
*/

class ss_parser_sxw extends ss_parser_base
{
	function ss_parser_sxw($url)
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
		$this->_init_content();

		// write to temp file
		$fn = tempnam(aw_ini_get("server.tmpdir"), "ss-sxw2txt");

		$this->_put($fn,$this->content);

		$zip = aw_ini_get("server.unzip_path");
		$tn = aw_ini_get("server.tmpdir")."/".gen_uniq_id();
		mkdir($tn,0777);

		$cmd = $zip." -d $tn $fn";
		$op = shell_exec($cmd);

		$contents_file = $tn."/content.xml";

		$fc = $this->_get($contents_file);

		$this->text_content = "";

		// parse text from xml
		$xml_parser = xml_parser_create();
		// use case-folding so we are sure to find the tag in $map_array
		xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, true);
		xml_set_character_data_handler($xml_parser, array(&$this, "_chard"));

		if (!xml_parse($xml_parser, $fc, true)) 
		{
			error::raise(array(
				"id" => "ERR_XML",
				"msg" => t("ss_parser_sxw::get_text_content(): ").sprintf(t("XML error: %s at line %d"),
					xml_error_string(xml_get_error_code($xml_parser)),
					xml_get_current_line_number($xml_parser))
			));
		}
		xml_parser_free($xml_parser);

		// remove temp dir contents
		$this->_rmdir($tn);
		unlink($fn);

		return trim($this->text_content);
	}

	function _chard($parser, $data)
	{
		$this->text_content .= " ".iconv("UTF-8", "ISO-8859-4//TRANSLIT", $data);
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

	function _rmdir($tn)
	{
		if ($dir = @opendir($tn)) 
		{
			while (($file = readdir($dir)) !== false) 
			{
				if (!($file == "." || $file == ".."))
				{
					$fn = $tn."/".$file;
					if (is_dir($fn))
					{
						$this->_rmdir($fn);
						rmdir($fn);
					}
					else
					{
						unlink($fn);
					}
				}
			}  
			closedir($dir);
		}
		rmdir($tn);
	}
}
?>
