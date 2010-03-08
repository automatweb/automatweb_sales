<?php

// open tpldir, scan subfolders, ignore CVS folders, grep for each template and if it is not found say it aint so.

$awroot = getcwd();

$files = explode("\n", `find ./templates/ -type f | grep -v CVS | grep -v templates/automatweb/documents`);
echo "\nThis script searches for unused templates by finding all templates\n";
echo "in the template folder and its subfolders (except the document templates folder)\n";
echo "and then grepping (-wirl) the whole source for each template.\n\n";
echo "found ".count($files)." templates, scanning. \n\n";
$unused = array();
$cnt = 1;
foreach($files as $file)
{
	if ($file == "")
	{
		continue;
	}
	$fn = basename($file);
	//echo "exec grep -irl $fn * \n";
	echo ".";
	flush();
	$res = trim(`grep -wirl $fn * | grep -v CVS`);
	if ($res == "")
	{
		//echo "\ntemplate $file seems not to be used! \n";
		$unused[] = $file;
	}
	if ($cnt && ($cnt % 10) == 0)
	{
		echo " ";
	}
	if ($cnt && ($cnt % 70) == 0)
	{
		echo " $cnt \n";
	}
	$cnt++;
}

echo "\n\nunused templates seem to be: \n";
echo join("\n", $unused)."\n\n";
?>