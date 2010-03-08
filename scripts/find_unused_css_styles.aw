<?php
// assume $AWROOT folder
$awroot = getcwd();
$cssfld = $awroot."/automatweb/css/";
chdir($cssfld);
foreach(glob("*.css") as $fn)
{
	parsecss($cssfld.$fn);
}
function parsecss($file)
{
	global $awroot;

	$grepd = array();
	$unused = array();

	echo "\nchecking file $file \n";
	$fc = file_get_contents($file);
	preg_match_all("/\.(.*)\{(.*)\}/imsU", $fc, $mt, PREG_PATTERN_ORDER);
	foreach($mt[1] as $styln)
	{
		$styln = trim($styln);
		foreach(explode("\n", $styln) as $tstyln)
		{
			$tstyln = trim($tstyln);
			foreach(explode(".", $tstyln) as $astyln)
			{
				$astyln = trim($astyln);
				foreach(explode(" ", $astyln) as $rstyln)
				{
					$rstyln = trim($rstyln);
					$rstyln = str_replace(",", "", $rstyln);
					if (($pos = strpos($rstyln,":")) !== false)
					{
						$rstyln = substr($rstyln, 0,$pos);
					}
					if ($rstyln == "a" || $rstyln == "div")
					{
						continue;
					}

					if ($grepd[$rstyln])
					{
						continue;
					}
					$grepd[$rstyln] = true;
					// now, grep the source for that file
					//echo "grep for $rstyln cmd = grep -irl $rstyln  $awroot \n";
					echo ".";
					flush();
					$res = `grep -irl $rstyln $awroot`;
					//echo "res = $res \n\n";
					// check if any file, besides css files was found
					$foundcnt = 0;
					foreach(explode("\n", $res) as $resf)
					{
						if (trim($resf) == "")
						{
							continue;
						}
						if (substr(basename($resf), -3) != "css")
						{
							$foundcnt++;
						}
					}

					if ($foundcnt == 0)
					{
						//echo "\nunused style $rstyln!!! \n";
						$unused[] = $rstyln;
					}
				}
			}
		}
	}

	echo "\nunused styles: \n".join("\n", $unused)."\n\n\n";
}
?>