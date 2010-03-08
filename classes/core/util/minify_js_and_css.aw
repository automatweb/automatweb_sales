<?php
// minify_js_and_css.aw - Paki css ja javascript
/*
@classinfo  maintainer=hannes
*/
class minify_js_and_css extends class_base
{
	public static function compress_js($script, $use_javascriptpacker=false)
	{
		// JavaScriptPacker seems to create lag. opera shows white page for a sec some times
		// so dont use it until there's less javascript in aw admin
		if ($use_javascriptpacker)
		{
			require_once("../addons/packer.php-1.0/class.JavaScriptPacker.php");

			$packer = new JavaScriptPacker($script, 'Normal', true, false);
			$packed = $packer->pack();
		}
		else
		{
			$packed = minify_js_and_css::remove_js_comments($script);
		}

		return $packed;
	}


	public static function remove_js_comments($str)
	{
		$res = '';
		$maybe_regex = true;
		$i=0;
		$current_char = '';

		while ($i+1<strlen($str))
		{
				if ($maybe_regex && $str{$i}==='/' && $str{$i+1}!=='/' && $str{$i+1}!=='*')
				{//regex detected
					if (strlen($res) && $res{strlen($res)-1} === '/')
					{
						$res .= ' ';
					}
					do
					{
						if ($str{$i} === '\\')
						{
							$res .= $str{$i++};
						} elseif ($str{$i} === '[')
						{
							do
							{
								if ($str{$i} === '\\')
								{
									$res .= $str{$i++};
								}
								$res .= $str{$i++};
							} while ($i<strlen($str) && $str{$i}!==']');
						}
						$res .= $str{$i++};
					} while ($i<strlen($str) && $str{$i}!=='/');
					$res .= $str{$i++};
					$maybe_regex = false;
					continue;
				} elseif ($str{$i}==='"' || $str{$i}==="'")
				{
					//quoted string detected
					$quote = $str{$i};
					do
					{
						if ($str{$i} === '\\')
						{
							$res .= $str{$i++};
						}
						$res .= $str{$i++};
					} while ($i<strlen($str) && $str{$i}!=$quote);
					$res .= $str{$i++};
					continue;
				} elseif ($str{$i}.$str{$i+1}==='/*')
				{
					//multi-line comment detected
					$i+=3;
					while ($i<strlen($str) && $str{$i-1}.$str{$i}!=='*/')
					{
						$i++;
					}
					if ($current_char === "\n")
					{
						$str{$i} = "\n";
					}
					else
					{
						$str{$i} = ' ';
					}
				} elseif ($str{$i}.$str{$i+1}==='//')
				{
					//single-line comment detected
					$i+=2;
					while ($i<strlen($str) && $str{$i}!=="\n")
					{
						$i++;
					}
				}

				$LF_needed = false;
				if (preg_match('/[\n\r\t ]/', $str{$i}))
				{
					if (strlen($res) && preg_match('/[\n ]/', $res{strlen($res)-1}))
					{
						if ($res{strlen($res)-1} === "\n") $LF_needed = true;
						$res = substr($res, 0, -1);
					}
					while ($i+1<strlen($str) && preg_match('/[\n\r\t ]/', $str{$i+1}))
					{
						if (!$LF_needed && preg_match('/[\n\r]/', $str{$i}))
						{
							$LF_needed = true;
						}
						$i++;
					}
				}

				if (strlen($str) <= $i+1)
				{
					break;
				}

				$current_char = $str{$i};

				if ($LF_needed)
				{
					$current_char = "\n";
				} elseif ($current_char === "\t")
				{
					$current_char = " ";
				} elseif ($current_char === "\r")
				{
					$current_char = "\n";
				}

				// detect unnecessary white spaces
				if ($current_char === " ")
				{
				if (strlen($res) && (preg_match('/^[^(){}[\]=+\-*\/%&|!><?:~^,;"\']{2}$/', $res{strlen($res)-1}.$str{$i+1}) ||
											preg_match('/^(\+\+)|(--)$/', $res{strlen($res)-1}.$str{$i+1}) // for example i+ ++j;
				))
				{
					$res .= $current_char;
				}
			} elseif ($current_char === "\n")
			{
				if (strlen($res) && (	preg_match('/^[^({[=+\-*%&|!><?:~^,;\/][^)}\]=+\-*%&|><?:,;\/]$/', $res{strlen($res)-1}.$str{$i+1}) ||
											(strlen($res)>1 && preg_match('/^(\+\+)|(--)$/', $res{strlen($res)-2}.$res{strlen($res)-1})) ||
											preg_match('/^(\+\+)|(--)$/', $current_char.$str{$i+1}) ||
											preg_match('/^(\+\+)|(--)$/', $res{strlen($res)-1}.$str{$i+1})// || // for example i+ ++j;
				))
				{
					$res .= $current_char;
				}
			} else $res .= $current_char;

			// if the next charachter be a slash, detects if it is a divide operator or start of a regex
			if (preg_match('/[({[=+\-*\/%&|!><?:~^,;]/', $current_char))
			{
				$maybe_regex = true;
			}
			elseif (!preg_match('/[\n ]/', $current_char))
			{
				$maybe_regex = false;
			}

			$i++;
		}
		if ($i<strlen($str) && preg_match('/[^\n\r\t ]/', $str{$i}))
		{
			$res .= $str{$i};
		}
		return $res;
	}


	public static function compress_css($script)
	{
		// remove comments
		$packed = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $script);
		// remove tabs, spaces, newlines, etc.
		$packed = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $packed);

		return $packed;
	}

		/** outputs file

		@attrib name=get_js params=name nologin="1" default="0" is_public="1"

		@param name required

		@returns

		@comment

	**/
	public static function get_js($arr)
	{
		$s_salt = "this_is_a_salty_string_";
		ob_start ("ob_gzhandler");
		header ("Content-type: text/javascript; charset: UTF-8");
		header("Expires: ".gmdate("D, d M Y H:i:s", time()+43200)." GMT");
		header("Cache-Control: max-age=315360000");

		$cache = get_instance('cache');
		echo $cache->file_get($s_salt.$arr["name"]);
		die();
	}

		/** outputs file

		@attrib name=get_css params=name nologin="1" default="0" is_public="1"

		@param name required

		@returns

		@comment

	**/
	public static function get_css($arr)
	{
		$s_salt = "this_is_a_salty_string_";
		ob_start ("ob_gzhandler");
		header ("content-type: text/css; charset: UTF-8");
		header("Expires: ".gmdate("D, d M Y H:i:s", time()+43200)." GMT");
		header("Cache-Control: max-age=315360000");

		$cache = get_instance('cache');
		echo $cache->file_get($s_salt.$arr["name"]);
		die();
	}

	public static function parse_admin_header($str)
	{
		$s_out ="";
		$s_salt = "this_is_a_salty_string_";
		$f_cache_filename_js = 'aw_admin.js';
		$f_cache_filename_css = 'aw_admin.css';
		$s_js_contents = "";

		$cache = get_instance('cache');
		if (strlen($cache->file_get($s_salt.$f_cache_filename_js))==0 &&
			strlen($cache->file_get($s_salt.$f_cache_filename_css))==0 )
		{
			$s_js_contents = '';
			$s_css_contents = '';
			if (preg_match_all ( "/<script.*src=['\"\s](.*)['\"\s]>/imsU", $str, $matches) )
			{
				for ($i = 0; $i < count($matches[1]); $i++)
				{
					$s_js_contents .= core::get_file(array("file" => $matches[1][$i]));
				}
			}

			if (preg_match_all ( "/<link.*href=['\"\s](.*)['\"\s].*>/imsU", $str, $matches) )
			{
				for ($i = 0; $i < count($matches[1]); $i++)
				{
					$s_css_contents .= core::get_file(array ("file" => $matches[1][$i]));
				}
			}

			$s_js_contents = minify_js_and_css::compress_js($s_js_contents);
			$s_css_contents = minify_js_and_css::compress_css($s_css_contents);

			$cache->file_set($s_salt.$f_cache_filename_js, $s_js_contents);
			$cache->file_set($s_salt.$f_cache_filename_css, $s_css_contents);

		}
//		$s_out = '<link rel="stylesheet" type="text/css" href="'.aw_ini_get("baseurl").'/orb.aw?class=minify_js_and_css&amp;action=get_css&amp;name=aw_admin.css">'."\n";
//		$s_out .= '<script src="'.aw_ini_get("baseurl").'/orb.aw?class=minify_js_and_css&amp;action=get_js&amp;name=aw_admin.js" type="text/javascript"></script>';

		$s_out = '	<link rel="stylesheet" type="text/css" href="'.aw_ini_get("baseurl").'/automatweb/get_min_css.aw?name=aw_admin.css">'."\n";
		$s_out .= '	<script src="'.aw_ini_get("baseurl").'/automatweb/get_min_css.aw?name=aw_admin.js" type="text/javascript"></script>';

		return $s_out;
	}

	public static function parse_site_header($that)
	{
		if ($that->is_template("MINIFY_JS_AND_CSS") )
		{
			$str = $that->parse("MINIFY_JS_AND_CSS");
			$s_out ="";
			$s_prefix = "this_is_a_salty_string_";

			// create filenames
			$s_hash  = md5($that->template_filename);
			$f_cache_filename_js = $s_hash.".js";
			$f_cache_filename_css =  $s_hash.".css";

			$cache = get_instance('cache');

			if (strlen($cache->file_get($s_prefix.$f_cache_filename_js))==0 &&
				strlen($cache->file_get($s_prefix.$f_cache_filename_css))==0 )
			{
				if (preg_match_all ( "/<script.*src=['\"\s](.*)['\"\s]>/imsU", $str, $matches) )
				{
					for ($i=0;$i<count($matches[1]);$i++)
					{
						$s_js_contents .= core::get_file(array("file"=>$matches[1][$i]));
					}
				}

				if (preg_match_all ( "/<link.*href=['\"\s](.*)['\"\s].*>/imsU", $str, $matches) )
				{
					for ($i=0;$i<count($matches[1]);$i++)
					{
						$s_css_contents .= core::get_file(array ("file" => $matches[1][$i]));
					}
				}

				$minify = get_instance(CL_MINIFY_JS_AND_CSS);
				$s_js_contents = $minify->compress_js($s_js_contents);
				$s_css_contents = $minify->compress_css($s_css_contents);

				$cache->file_set($s_prefix.$f_cache_filename_js, $s_js_contents);
				$cache->file_set($s_prefix.$f_cache_filename_css, $s_css_contents);
			}
			$xhtml_slash = "";
			if (aw_ini_get("content.doctype") === "xhtml")
			{
				$xhtml_slash = " /";
			}
			if (strlen($cache->file_get($s_prefix.$f_cache_filename_css))>0)
			{
				$s_out .= '<link rel="stylesheet" type="text/css" href="'.aw_ini_get("baseurl").'/orb.aw?class=minify_js_and_css&amp;action=get_css&amp;name='.$f_cache_filename_css.'"'.$xhtml_slash.'>'."\n";
			}
			if (strlen($cache->file_get($s_prefix.$f_cache_filename_js))>0)
			{
				$s_out .= '<script src="'.aw_ini_get("baseurl").'/orb.aw?class=minify_js_and_css&amp;action=get_js&amp;name='.$f_cache_filename_js.'" type="text/javascript"></script>';
			}

			$that->vars(array("MINIFY_JS_AND_CSS" => $s_out ));
		}
	}
}
?>
