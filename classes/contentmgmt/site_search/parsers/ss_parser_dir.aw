<?php
/*
@classinfo  maintainer=kristo
*/
class ss_parser_dir extends ss_parser_base
{
	function ss_parser_dir($url)
	{
		$this->url = $url;
		$this->content = NULL;
		$this->headers = NULL;
	}
	
	function get_links()
	{
		$ret = array();

		// get subdirs
		if ($dh = opendir($this->url)) 
		{
			while (($file = readdir($dh)) !== false)
			{
				if ($file != "." && $file != "..")
				{
					$fn = $this->url."/".$file;
					$ret[$fn] = $fn;
				}
			}
		}
		closedir($dh);
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
