<?php
/*
@classinfo  maintainer=kristo
*/
class ss_parser_html extends ss_parser_base
{
	function ss_parser_html($url)
	{
		$this->url = $url;
		$this->url_parsed = parse_url($this->url);
		$this->content = NULL;
		$this->headers = NULL;
	}

	function get_links()
	{
		$this->_init_content();

		$ret = array();

		$base = trim($this->url);
		// split off from ? to complete base
		$qpos = strpos($base, "?");
		if ($qpos)
		{
			$base = substr($base, 0, $qpos);
		}
		if (substr($base, "-1") != "/")
		{
			$base = dirname($base); //$this->url_parsed["scheme"]."://".$this->url_parsed["host"];
		}

		// I guess we need to figure out all <a tags
		preg_match_all("/<a(.*)>/imsU", $this->content, $mt, PREG_PATTERN_ORDER);
		// also, frames
		preg_match_all("/<frame(.*)>/imsU", $this->content, $mt_fr, PREG_PATTERN_ORDER);
		if (is_array($mt_fr))
		{
			if (!is_array($mt))
			{
				$mt = array(1 => array());
			}
			foreach($mt_fr[1] as $item)
			{
				$mt[1][] = $item;
			}
		}
		if (is_array($mt))
		{
			foreach($mt[1] as $match)
			{
				// $match should contain "target="foo" href="bla" ..."
				$match = str_replace("href = ", "href=", $match);
				$match = str_replace("SRC=", "href=", $match);
				if (preg_match("/href=(.*)/i", str_replace("src=", "href=", $match), $mt2))
				{
					// it might still contain trailing crap and we can not rely on there being an end terminator
					// so manually parse this thang here
					$str = $mt2[1];
					if ($str[0] == "\"" || $str[0] == "'") 
					{
						$delim = $str[0];
					}
					else
					{
						$delim = " ";
					}

					$pos = 1;
					$len = strlen($str);
					while(($str[$pos] != $delim && $str[$pos-1] != "\\") && $pos < $len)
					{
						$pos++;
					}
					$str = substr($str, 1, $pos-1);
					if (substr($str, 0, 10) == "javascript")
					{
						continue;
					}
					if (substr($str, 0, 6) == "mailto")
					{
						continue;
					}
//echo "str = $str base = $base  <br>";
					// now, if baseurl is not included, add that
					if ($str[0] == "/")
					{
						$str = $base.$str;
					}
					else
					if ($str[0] == "?")
					{
						$str = $base."/".$str;
					}

					// remove trailing #asdasd anchors
					$pos = strpos($str, "#");
					if ($pos !== false)
					{
						$str = substr($str, 0, $pos);
					}
//echo "str = $str base = $base <br>";
					// now, if the final url is just a filename, then get the dir from the current url and prepend that
					$pu = parse_url($str);
					if (!$pu["scheme"])
					{
						if (substr($this->url, -1) == "/")
						{
							$str = $this->url.$str;
						}
						else
						{
							$str = $base."/".$str;
						}
					}
					$str = $this->resolve_path($str);
					$ret[$str] = $str;
					//echo "found url ".$mt2[1]." , turned into $str <br>";
				}
			}
		}
		//echo (dbg::dump($ret));
		return $ret;
	}

	function get_text_content($indexer)
	{
		$this->_init_content();

		$ct = $this->content;
		if (is_object($indexer) && $indexer->prop("content_regex") != "")
		{
			if( preg_match($indexer->prop("content_regex"), $ct, $mt))
			{
				$ct = $mt[1];
			}
		}

		// also remove javascript content
		$fc = preg_replace("/<script(.*)<\/script>/imsU","", $ct);
		// and css styles
		$fc = preg_replace("/<style(.*)<\/style>/imsU","", $fc);
		// and html comments
		$fc = preg_replace("/<!--(.*)-->/imsU","", $fc);

		$r = trim(strip_tags($fc));
		return $r;
	}

	function get_last_modified()
	{
		return NULL;
	}

	function get_title($o)
	{
		$this->_init_content();

		if ($o->prop("title_regex") != "")
		{
			if (preg_match($o->prop("title_regex"), $this->content, $mt))
			{
				return trim(strip_tags($mt[1]));
			}
		}

		if (preg_match("/<TITLE>(.*)<\/TITLE>/iUs", $this->content, $mt))
		{
			return trim(strip_tags($mt[1]));
		}
		return NULL;
	}
	
	function resolve_path($url)
	{
		$pu = parse_url($url);
		
		$path = $pu["path"];

		$path = explode('/', str_replace('//', '/', $path));
		for ($i=0; $i<count($path); $i++) 
		{
			if ($path[$i] == '.') 
			{
				unset($path[$i]);
				$path = array_values($path);
				$i--;
			} 
			else
			if ($path[$i] == '..' AND ($i > 1 OR ($i == 1 AND $path[0] != '') ) ) 
			{
				unset($path[$i]);
				unset($path[$i-1]);
				$path = array_values($path);
				$i -= 2;
			} 
			else
			if ($path[$i] == '..' AND $i == 1 AND $path[0] == '') 
			{
				unset($path[$i]);
				$path = array_values($path);
				$i--;
			} 
			else 
			{
				continue;
			}
		}
		$path = implode('/', $path);
		
		$url = "";
		if (isset($pu["scheme"]))
		{
			$url .= $pu["scheme"]."://";
		}
		if (isset($pu["user"]))
		{
			$url .= $pu["user"];
		}
		if (isset($pu["pass"]))
		{
			$url .= ":".$pu["pass"]."@";
		}
		if (isset($pu["host"]))
		{
			$url .= $pu["host"];
		}
		
		$url .= $path;
		
		if (isset($pu["query"]))
		{
			$url .= "?".$pu["query"];
		}
		
		if (isset($pu["fragment"]))
		{
			$url .= "#".$pu["fragment"];
		}
		
		return $url;
	}
}
?>
