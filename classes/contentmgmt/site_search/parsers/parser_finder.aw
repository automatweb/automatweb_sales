<?php
/*
@classinfo  maintainer=kristo
*/
classload("contentmgmt/site_search/parsers/parser_base");
classload("contentmgmt/site_search/parsers/ss_parser_html");
classload("contentmgmt/site_search/parsers/ss_parser_doc");
classload("contentmgmt/site_search/parsers/ss_parser_xls");
classload("contentmgmt/site_search/parsers/ss_parser_ppt");
classload("contentmgmt/site_search/parsers/ss_parser_pdf");
classload("contentmgmt/site_search/parsers/ss_parser_dir");
classload("contentmgmt/site_search/parsers/ss_parser_sxw");
classload("contentmgmt/site_search/parsers/ss_parser_sxc");
classload("contentmgmt/site_search/parsers/ss_parser_txt");
classload("contentmgmt/site_search/parsers/ss_parser_xml");
classload("contentmgmt/site_search/parsers/ss_parser_rtf");
classload("contentmgmt/site_search/parsers/ss_parser_file_list");
class parser_finder
{
	function instance($url)
	{
		$known_exts = array("html", "doc", "ppt", "pdf", "xls", "rtf", "sxw", "sxc", "txt", "xml", "file_list");

		// if it is a filesystem path and contains a directory, get directory parser
		if ($url{0} == "/" && is_dir($url))
		{
			return new ss_parser_dir($url);
		}

		if ($url{0} == "/" && is_file($url))
		{
			$pos = strrpos($url, ".");
			if ($pos !== false)
			{
				$ext = substr($url, $pos+1);
			}
		}
		else
		{
			// get extension from url

			$parts = parse_url($url);
			if (!isset($parts["scheme"]))
			{
				$url = "http://".$url;
				$parts = parse_url($url);
			}

			$ext = "html";

			if ($parts["path"] != "")
			{
				//echo "path = $parts[path] <br>";
				$pos = strrpos($parts["path"], ".");
				//echo "pos = $pos <br>";
				if ($pos !== false)
				{
					$ext = substr($parts["path"], $pos+1);
				}
			}
		}

		if ($ext == "htm")
		{
			$ext = "html";
		}
		if ($ext == "php")
		{
			$ext = "html";
		}

		if (!in_array($ext, $known_exts))
		{
			return NULL;
		}
		$cln = "ss_parser_".$ext;

		if (!class_exists($cln))
		{
			echo "parts = ".dbg::dump($parts)." pos = $pos cln = $cln <br>";
		}

		return new $cln($url);
	}
}

?>
