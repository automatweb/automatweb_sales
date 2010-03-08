<?php

class docgen_ini_file_parser
{
	function __construct()
	{
		
	}

	function get_tree_items()
	{
		list($sets, $comms) = $this->parse_config(aw_ini_get("basedir")."/aw.ini", true);
		$rv = array(
			"__main" => t("&Uuml;ldised")
		);

		foreach($sets as $k => $d)
		{
			list($d) = explode("=", $d);
			if (strpos($d, ".") === false)
			{
				continue;
			}
			list($v) = explode(".", $d);
			$rv[$v] = $v;
		}

		return $rv;
	}

	function get_setting_data($setting)
	{
		list($sets, $comms) = $this->parse_config(aw_ini_get("basedir")."/aw.ini", true);

		$rv = array();
		foreach($sets as $k => $d)
		{
			list($d, $def) = explode("=", $d);
			if ((strpos($d, ".") === false && $setting == "__main") || substr($d, 0, strlen($setting)) == $setting)
			{
				list($f1, $f2) = explode(".", $d);
				$val = $f1.".".$f2;

				if (!isset($rv[$val]))
				{
					$rv[$val] = array(
						"comment" => $this->_format_comment($comms[$d]),
						"default_value" => $def
					);
				}
			}
		}
		return $rv;
	}

	private function _format_comment($com)
	{
		$rv = array();
		foreach(explode("\n", $com) as $line)
		{
			$line = str_replace("#", "", trim($line));
			if (trim($line) != "")
			{
				$rv[] = trim($line);
			}
		}
		return join("<br>", $rv);
	}

	private function parse_config($file, $return = false)
	{
		$comms = array();

		$fd = file($file);
		$config = array();
		$last_comment = "";

		foreach($fd as $linenum => $line)
		{
			// ok, parse line
			if (strlen(trim($line)) and $line{0} == "#") 
			{
				$last_comment .= $line;
			}
			else
			if (strlen(trim($line)) and $line{0} != "#") // exclude comments and empty lines
			{
				// now, config opts are variable = value. variable is class1. ... .classN.
				$data = explode("=", $line, 2);

				if (2 === count($data))
				{ // process regular variable
					$var = str_replace(array('["','"]',"['","']","[","]"), array(".","",".", "",".", ""), trim($data[0]));//!!! should be deprecated and only '.' notation used. kept here for back compatibility.
					$value = trim($data[1]);

					// now, replace all variables in varvalue
					$value = preg_replace('/\$\{(.*)\}/e', "aw_ini_get(\"\\1\")",$value);
					$var = preg_replace('/\$\{(.*)\}/e', "aw_ini_get(\"\\1\")",$var);

					// add setting
					if ($return)
					{
						$config[] = $var . "=" . $value;
						$comms[$var] = $last_comment;
						$last_comment = "";
					}
					else
					{
						$setting_index = explode(".", $var);
						$setting_path = "\$GLOBALS['cfg']";

						foreach ($setting_index as $key => $index)
						{
							$setting_path .= "['" . $index . "']";
	
							if (isset($setting_index[$key + 1]) and eval("return (isset(" . $setting_path . ") and !is_array(" . $setting_path . "));"))
							{
								eval($setting_path . " = array();");
							}
						}

						$setting = "\$config['" . str_replace(".", "']['", $var) . "'] = " . var_export($value, true) . ";";
						eval($setting);
					}
				}
				elseif ("include" === substr(trim($line), 0, 7))
				{ // process config file include
					$line = preg_replace('/\$\{(.*)\}/e',"aw_ini_get(\"\\1\")",$line);
					$ifile = trim(substr($line, 7));

					if (!file_exists($ifile) || !is_readable($ifile))
					{
						if (isset($GLOBALS["stderr"]))
						{
							fwrite($GLOBALS["stderr"], "Failed to open include file '" . $ifile . "' on line " . $linenum+1 . " in file '" . $file . "'\n");
						}

						return false;
					}
	
					if ($return)
					{
						$config = array_merge($config, parse_config($ifile, true));
					}
					else	
					{
						parse_config($ifile);
					}
				}
			}
		}

		if ($return)
		{
			return array($config, $comms);
		}
	}
}
?>