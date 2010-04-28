<?php

function parse_config_to_ini($file, $include = false)
{
	// put result lines in here
	$res = array();
	$linenum = 0;
	$fd = file($file);
	foreach($fd as $line)
	{
		$linenum++;
		$oline = $line;
		$add = true;
		// ok, parse line
		// 1st, strip comments
		if (($pos = strpos($line,"#")) !== false)
		{
			$line = substr($line,0,$pos);
		}
		// now, strip all whitespace
		$line = trim($line);

		if ($line != "")
		{
//echo "line = $line \n";
			if (substr($line, 0, strlen("include")) == "include")
			{
				// process include
				$line = preg_replace('/\$\{(.*)\}/e','aw_ini_get("\\1")',$line);
				$ifile = trim(substr($line, strlen("include")));
				if (!file_exists($ifile) || !is_readable($ifile))
				{
					fwrite($GLOBALS["stderr"], "Failed to open include file on line $linenum in file $file ($ifile) \n");
					return false;
				}
				$incl_res = parse_config_to_ini($ifile, true);
				if ($incl_res === false)
				{
					fwrite($GLOBALS["stderr"], "\tthat was included from line $linenum in file $file \n");
					return false;
				}
				else
				{
					foreach($incl_res as $iline)
					{
						$res[] = $iline;
					}
					$add = false;
					$res[] = "\n";// to cope with files not ending with newline
				}
			}
			else
			{
				// now, config opts are class.variable = value
				$eqpos = strpos($line," = ");
				if ($eqpos !== false)
				{
					$var = trim(substr($line,0,$eqpos));
					$varvalue = trim(substr($line,$eqpos+3));

					// now, replace all variables in varvalue
					try
					{
						$varvalue = preg_replace('/\$\{(.*)\}/e', 'aw_ini_get("\\1")', $varvalue);
						$var = preg_replace('/\$\{(.*)\}/e', 'aw_ini_get("\\1")', $var);
					}
					catch(\Exception $e)
					{
						// echo "ex for $varvalue / $var \n";
					}

					if (true || ($dotpos = strpos($var,".")) === false)
					{
						$varname = $var;
						aw_ini_set($varname, $varvalue);
						// echo "set $varname => $varvalue \n";
					}
				}
			}
		}

		if ($add)
		{
			$res[] = $oline;
		}
	}

	if (!$include)
	{
		$res =
		"######################################################################\n".
		"# THIS IS AN AUTOMATICALLY GENERATED FILE!!!                         #\n".
		"# DO NOT EDIT THIS!!                                                 #\n".
		"#                                                                    #\n".
		"# Instead, edit aw.ini.root and/or the files included from it.       #\n".
		"# after editing, to regenerate this file execute cd \$AWROOT;make ini #\n".
		"######################################################################\n\n\n" .
		join ("", $res);
	}

	return $res;
}

?>
