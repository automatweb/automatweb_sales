<?php
/*
@classinfo  maintainer=kristo
*/
class ss_parser_file_list extends ss_parser_base
{
	function ss_parser_file_list($url)
	{
		$this->url = $url;
	}
	
	function get_links()
	{
		$ret = array();
		$fs = file($this->url);
		echo "read file ".$this->url." lines = ".count($fs)." <br>\n";
		flush();
		foreach($fs as $idx => $line)
		{
			if (($idx % 100) == 1)
			{
				echo "idx = $idx <br>\n";
				flush();
			}
			$line = trim($line);
			if ($line == "")
			{
				continue;
			}
			$fn = basename($line);
			if (strpos($fn, ".") === false)
			{
				continue;
			}
			
			if ($line{strlen($line)-1} == "m")
			{
				continue; // skip .htm files
			}
			$ret[] = $line;
		}
		echo "parsed lines <br>\n";
		flush();
		return $ret;
	}

	function get_text_content()
	{
		return NULL;
	}

	function get_last_modified()
	{
		return NULL;
	}

	function get_title()
	{
		return NULL;
	}
}
?>
