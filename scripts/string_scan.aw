<?php

$basedir = realpath(".");
include($basedir . "/automatweb.aw");

automatweb::start();
include AW_DIR . "const" . AW_FILE_EXT;
include(AW_DIR . "classes/defs.aw");
$awt = new aw_timer();
aw_global_set("no_db_connection", 1);

$files = array(aw_ini_get("basedir")."/classes/crm/crm_company.aw");

define("OUT", 1);
define("IN_DBL", 2);
define("IN_SGL", 3);
define("IN_COMMENT_SGL", 4);
define("IN_COMMENT_MUL", 5);

foreach($files as $file)
{
	// slurp in file and try to scan for strings delimited by ' or "
	$fc = file_get_contents($file);

	$state = OUT;
	$line = 1;

	$len = strlen($fc);
	for($i = 0;  $i < $len; $i++)
	{
		if ($fc{$i} == "\n")
		{
			$line++;
		}

		if ($fc{$i} == "/" && $fc{$i+1} == "*" && $state == OUT)
		{
			ct(IN_COMMENT_MUL);
			$i++;
			continue;
		}

		if ($fc{$i} == "*" && $fc{$i+1} == "/" && $state == IN_COMMENT_MUL)
		{
			ct(OUT);
			$i++;
			continue;
		}

		if ($fc{$i} == "/" && $fc{$i+1} == "/" && $state != IN_COMMENT_MUL)
		{
			ct(IN_COMMENT_SGL);
			$i++;
			continue;
		}

		if (($fc{$i} == "\r" || $fc{$i} == "\n") && $state == IN_COMMENT_SGL)
		{
			ct(OUT);
			continue;
		}

		if ($state == IN_COMMENT_MUL || $state == IN_COMMENT_SGL)
		{
			continue;
		}

		if ($fc{$i} == "\"" || $fc{$i} == "'")
		{
			switch($state)
			{
				case IN_SGL:
					if ($fc{$i} == "\"")
					{
						continue;
					}

					// check for quote
					if ($fc{$i-1} != "\\")
					{
						// end of string
						$strings[] = array(
							"start" => $start,
							"end" => $i,
							"str" => substr($fc, $start, ($i-$start)+1),
							"line" => $line
						);
					}
					ct(OUT);
					break;

				case IN_DBL:
					if ($fc{$i} == "'")
					{
						continue;
					}

					// check for quote
					if ($fc{$i-1} != "\\")
					{
						// end of string
						$strings[] = array(
							"start" => $start,
							"end" => $i,
							"str" => substr($fc, $start, ($i-$start)+1),
							"line" => $line
						);
					}
					ct(OUT);
					break;

				case OUT:
					$start = $i;
					if ($fc{$i} == "'")
					{
						ct(IN_SGL);
					}
					else
					{
						ct(IN_DBL);
					}
					break;
			}

		}
	}


	$strings = elim($strings, $fc);
	disp($strings, $fc);
	//write($strings, $file, $fc);
}

automatweb::shutdown();


function ct($nst)
{
	//echo "change state from ".gc($GLOBALS["state"])." to ".gc($nst)." on line $GLOBALS[line] \n";
	$GLOBALS["state"] = $nst;
}

function gc($ct)
{
	$lut = array(
		OUT => "OUT",
		IN_DBL => "IN_DBL",
		IN_SGL => "IN_SGL",
		IN_COMMENT_SGL => "IN_COMMENT_SGL",
		IN_COMMENT_MUL => "IN_COMMENT_MUL"
	);
	return $lut[$ct];
}

function disp($a, $fc)
{
	echo "LINE: STRING\n";
	foreach($a as $s)
	{
		echo sprintf("% 4s", $s["line"]).": ".$s["str"]."\n";
	}
}

function elim($s, $fc)
{
	$tmp = array();
	foreach($s as $string)
	{
		if (!($fc{$string["start"]-1} == "(" && $fc{$string["end"]+1} == ")" && $fc{$string["start"]-2} == "t"))
		{
			continue;
		}

		$tmp[] = $string;
	}
	return $tmp;
}

function write($s, $f, $fc)
{
	$trans = $f.".trans";
	$fp = fopen($trans, "w");
	$add = 0;

	foreach($s as $str)
	{
		$fc = substr($fc, 0, $str["start"]+$add)."t(".$str["str"].")".substr($fc, $str["end"]+$add+1);
		$add += 3;
	}
	fwrite($fp, $fc);
	fclose($fp);
	echo "wrote file $trans \n";
}
?>
