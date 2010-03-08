<?php
ob_implicit_flush(true);
$basedir = realpath(".");
include($basedir . "/automatweb.aw");

function _file_get_contents($name)
{
	$f = @fopen($name, "r");
	if (!$f)
	{
		echo "\nERROR: file $name not found!\n\n";
		exit(1);
	}
	$fc = fread($f, filesize($name));
	fclose($f);
	return $fc;
}

function _file_put_contents($name, $fc)
{
	$f = @fopen($name, "w");
	if (!$f)
	{
		echo "\nERROR: could not create file $name!\n\n";
		exit(1);
	}

	$fc = fwrite($f, $fc);
	fclose($f);
}

function make_keys($arr)
{
	$ret = array();
	if (is_array($arr))
	{
		foreach($arr as $v)
		{
			$ret[$v] = $v;
		}
	}
	return $ret;
}

/*if ($_SERVER["argc"] < 2)
{
	echo("usage: mk_class new_class_name new_class_def [folder]\n");
	echo("  REQUIRED:\n");
	echo("	  new_class_name: the name of the class and the file of the class\n");
	echo("	  new_class_def: the class def in ini file (CL_BLAH)\n");
	echo("  OPTIONAL:\n");
	echo("    folder: the folder where the class file will be created, under AWROOT/classes\n");
	exit(1);
}*/

#chdir("../..");

$args_from_caller = isset($args["mkcl_file"]);
$stdin = fopen("php://stdin", "r");
$class = array();

///////////////////////////////////////////////////////////////////
// ask the user the needed info
//////////////////////////////////////////////////////////////////

echo "Hello! I am the AW class-o-maker 3000!\n";
echo "You will answer these questions:\n\n";

$continue = false;
while(!$continue)
{
	echo "Folder where the class file is (created under AWROOT/classes): " . ($args_from_caller ? $args["mkcl_folder"] : "");
	$class['folder'] = $args_from_caller ? trim($args["mkcl_folder"]) : trim(fgets($stdin));
	if (is_dir(($args_from_caller ? AW_DIR : "") . "classes/" . $class["folder"]))
	{
		$continue = true;
	}
	else
	{
		echo "Folder does not exist, create it (1/0): ? " . ($args_from_caller ? "yes" : "");
		$answer = $args_from_caller ? 1 : fgets($stdin);
		echo "\n";
		if ($answer == 1)
		{
			$continue = true;
		}
	}
}

echo "Class file (foo_bar): " . ($args_from_caller ? $args["mkcl_file"] : "");
$class['file'] = $args_from_caller ? trim($args["mkcl_file"]) : trim(fgets($stdin));

// make these automatically, then we can be sure they foillow standard and are unique
$class['def'] = "CL_".strtoupper($class['file']);
$class['syslog.type'] = "ST_".strtoupper($class['file']);

echo "Class name, users see this, so be nice (Foo bar): " . ($args_from_caller ? $args["mkcl_name"] : "");
$class['name'] = $args_from_caller ? trim($args["mkcl_name"]) : trim(fgets($stdin));

echo "Can the user add this class? (1/0): " . ($args_from_caller ? $args["mkcl_can_add"] : "");
$class['can_add'] = $args_from_caller ? trim($args["mkcl_can_add"]) : trim(fgets($stdin));

echo "Class parent folder id(s) (from classfolders.ini): " . ($args_from_caller ? $args["mkcl_parents"] : "");
$class['parents'] = $args_from_caller ? trim($args["mkcl_parents"]) : trim(fgets($stdin));

echo "Alias (if you leave this empty, then the class can't be added as an alias): " . ($args_from_caller ? $args["mkcl_alias"] : "");
$class['alias'] = $args_from_caller ? trim($args["mkcl_alias"]) : trim(fgets($stdin));

echo "Class is remoted? (1/0): " . ($args_from_caller ? $args["mkcl_is_remoted"] : "");
$class['is_remoted'] = $args_from_caller ? trim($args["mkcl_is_remoted"]) : trim(fgets($stdin));

if ($class['is_remoted'])
{
	echo "Default server to remote to (http://www.foo.ee): " . ($args_from_caller ? $args["mkcl_default_remote_server"] : "");
	$class['default_remote_server'] = $args_from_caller ? trim($args["mkcl_default_remote_server"]) : trim(fgets($stdin));
}

////////////////////////////////////////////////////////////////////
// check if a class by this name does not already exist!
////////////////////////////////////////////////////////////////////

$clnf = ($class['folder'] == "" ? $class['file'].AW_FILE_EXT : $class['folder']."/".$class['file'].AW_FILE_EXT);
$clnf_oo = ($class['folder'] == "" ? $class['file']."_obj".AW_FILE_EXT : $class['folder']."/".$class['file']."_obj".AW_FILE_EXT);
$tpnf = ($class['folder'] == "" ? $class['file'] : $class['folder']."/".$class['file']);

if (file_exists(($args_from_caller ? AW_DIR : "") . "classes/{$clnf}"))
{
	echo "\nERROR: file classes/$clnf already exists!\n\n";
	exit(1);
}

if (file_exists(($args_from_caller ? AW_DIR : "") . "classes/$clnf_oo"))
{
	echo "\nERROR: file classes/$clnf_oo already exists!\n\n";
	exit(1);
}

if (file_exists(($args_from_caller ? AW_DIR : "") . "xml/orb/$class.xml"))
{
	echo "\nERROR: file xml/orb/$class.xml already exists!\n\n";
	exit(1);
}

////////////////////////////////////////////////////////////////////
// now the hard bit - ini file parsing and modifying
////////////////////////////////////////////////////////////////////

////////////////////
// write classes.ini
////////////////////

echo "\n\nRequesting new class id...\n";

if (!$args_from_caller)
{
	automatweb::start();
	//automatweb::$instance->mode(automatweb::MODE_DBG);
	automatweb::$instance->bc();
	aw_global_set("no_db_connection", 1);
	aw_ini_set("baseurl", "automatweb");
	automatweb::shutdown();
}

$classlist = get_instance("core/class_list");
$new_clid = $classlist->register_new_class_id(array(
	"data" => $class
));

echo "...got new class_id = $new_clid ... \nwriting to classes.ini:...\n";

$new_clini  = "\nclasses[$new_clid][def] = ".$class['def']."\n";
$new_clini .= "classes[$new_clid][name] = ".$class['name']."\n";

if ($class['folder'] != '')
{
	$cl_fname = $class['folder']."/".$class['file'];
	$cl_fname_oo = $class['folder']."/".$class['file']."_obj";
}
else
{
	$cl_fname = $class['file'];
	$cl_fname_oo = $class['file']."_obj";
}

$new_clini .= "classes[$new_clid][file] = ".$cl_fname."\n";
$new_clini .= "classes[$new_clid][can_add] = ".$class['can_add']."\n";
$new_clini .= "classes[$new_clid][object_override] = ".$cl_fname_oo."\n";
if ($class['parents'] != '')
{
	$new_clini .= "classes[$new_clid][parents] = ".$class['parents']."\n";
}
if ($class['alias'] != '')
{
	$new_clini .= "classes[$new_clid][alias] = ".$class['alias']."\n";
}

if ($class['is_remoted'] == 1)
{
	$new_clini .= "classes[$new_clid][is_remoted] = ".$class['default_remote_server']."\n";
}

$fp = fopen(($args_from_caller ? AW_DIR : "") . 'config/ini/classes.ini','a');
fputs($fp, $new_clini);
fclose($fp);

echo $new_clini;
echo "\n";

///////////////////////////////////
// write syslog.ini
///////////////////////////////////

echo "parsing and adding to config/ini/syslog.ini..\n";

// read and find the biggest number
$sysini = _file_get_contents(($args_from_caller ? AW_DIR : "") . 'config/ini/syslog.ini');
$new_sysid = $new_clid;

$first_match = false;
$inserted = false;

$new_sysini = array();
$syslines = explode("\n", $sysini);
foreach($syslines as $sl)
{
	if (trim($sl) != '')
	{
		if (!$first_match)
		{
			// check if we found the first line
			if (strpos($sl, "syslog.types[") !== false)
			{
				$first_match = true;
			}
		}
		else
		{
			if (strpos($sl, "syslog.types") === false && !$inserted)
			{
				// if we reached the end of types definitions, then add the new typedef to the end
				$new_sysini[] = "syslog.types[".$new_sysid."][def] = ".$class['syslog.type'];
				echo "wrote...syslog.types[".$new_sysid."][def] = ".$class['syslog.type']."\n";
				$new_sysini[] = "syslog.types[".$new_sysid."][name] = ".$class['name'];
				echo "wrote...syslog.types[".$new_sysid."][name] = ".$class['name']."\n";
				$new_sysini[] = "";
				$inserted = true;
			}
		}
	}

	// also add the new type to the end of SA_ADD and SA_CHANGE
	if (strpos($sl, "syslog.actions[1][types]") !== false)
	{
		$sl = trim($sl).",".$new_sysid;
	}
	if (strpos($sl, "syslog.actions[3][types]") !== false)
	{
		$sl = trim($sl).",".$new_sysid;
	}
	$new_sysini[] = $sl;
}

_file_put_contents(($args_from_caller ? AW_DIR : "") . 'config/ini/syslog.ini',join("\n",$new_sysini));

echo "\n";


///////////////////////////////////////////////////////
// now create the actual class files
///////////////////////////////////////////////////////

echo "\nmaking class $clnf...\n\n";

if ($class['folder'] != "")
{
	// check if the directory exists
	if (!is_dir(($args_from_caller ? AW_DIR : "") . "classes/".$class['folder']))
	{
		// mkdir can only create one level of directories at a time
		// so if the folders has several levels, we need to create all of them.
		$dir = $args_from_caller ? (AW_DIR . "classes") : "classes";
		$dirs = explode("/", $class['folder']);
		foreach($dirs as $fld)
		{
			$dir .= "/".$fld;
			if (!is_dir($dir))
			{
				mkdir($dir,0775);
				echo "created $dir ...\n";
			}
		}
	}
}

$fc = str_replace("__classdef", $class['def'], _file_get_contents(($args_from_caller ? AW_DIR : "") . "install/class_template/classes/base" . AW_FILE_EXT));
$fc = str_replace("__tplfolder", $tpnf, $fc);
$fc = str_replace("__syslog_type", $class['syslog.type'], $fc);
$fc = str_replace("__name", $class['name'], $fc);
$fc = str_replace("__classname", $class['file'], $fc);
$fc = str_replace("__table_name", "aw_".$class['file'], $fc);
$fc = str_replace("__maintainer", get_current_user(), $fc);

_file_put_contents(($args_from_caller ? AW_DIR : "") . "classes/$clnf",$fc);
echo "created classes/$clnf...\n";

$fc = _file_get_contents(($args_from_caller ? AW_DIR : "") . "install/class_template/classes/class.aw");
$fc = str_replace("__classname", $class['file'] . "_obj", $fc);
_file_put_contents(($args_from_caller ? AW_DIR : "") . "classes/$clnf_oo",$fc);
echo "created classes/$clnf_oo...\n";

$folder = $class['folder'] != "" ? "folder=\"".$class['folder']."\"" : "";
$fc = str_replace("__classname", $class['file'], _file_get_contents(($args_from_caller ? AW_DIR : "") . "install/class_template/xml/orb/base.xml"));
_file_put_contents(($args_from_caller ? AW_DIR : "") . "xml/orb/".$class['file'].".xml",str_replace("__classfolder", $folder, $fc));
echo "created xml/orb/".$class['file'].".xml...\n";


echo "\n\nmaking ini file...\n\n";
if ($args_from_caller)
{
	$this->do_orb_method_call(array(
		"class" => "sys",
		"action" => "make_ini"
	));
}
else
{
	passthru('make ini');
}

echo "\n\nmaking properties...\n\n";
if ($args_from_caller)
{
	$this->do_orb_method_call(array(
		"class" => "sys",
		"action" => "make_prop"
	));
}
else
{
	passthru('make properties');
}

echo "\n\nall done! \n\n";

if (!$args_from_caller)
{
	automatweb::shutdown();
}

?>
