<?php

namespace automatweb;

function load_versions()
{
	if (aw_global_get("no_db_connection"))
	{
		return;
	}
	$table = "site_file_index";

	$r = mysql_connect(aw_ini_get("db.host"), aw_ini_get("db.user"), aw_ini_get("db.pass"));

	if(!$r)
	{
		return;
	}

	$versions = $GLOBALS['cfg']['versions'] = array();

	mysql_select_db(aw_ini_get("db.base"), $r);

	if(!table_exists("site_file_index"))
	{
		$that = new file();
		mysql_query('create table '.$that->db_table_name.' (
			id int not null primary key auto_increment,
			file_name varchar(255),
			file_version varchar(31),
			file_location varchar(31),
			class_name varchar(255),
			file_ext CHAR(8),
			package_name varchar(255),
			package_version varchar(31),
			package_id int,
			used int,
			installed_date int,
			dependences varchar(255)
		)');//see viimane on niisama, 2kki leiab hea lahenduse selleks
	}

	$res = mysql_query('select file_name,file_version,class_name,file_location from site_file_index where used="1"');

	if (!$res)
	{
		return;
	}

	while ($row = mysql_fetch_assoc($res))
	{
		$versions[$row['file_location'].'/'.$row['file_name']] = $row;

		//lisav m2llu ka ilma laiendi ja pathita variandi klasside jaoks
		$newfile_arr = explode("." , $row['file_name']);
		if(sizeof($newfile_arr) > 1)
		{
			$ext = end($newfile_arr);
			if($ext == "aw")
			{
				unset($newfile_arr[sizeof($newfile_arr) - 1]);
				$fs = join("." , $newfile_arr);
				$row["file_location"] = substr($row['file_location'] , 8);
				$versions[$fs] = $row;
				$versions[$row['file_location'].'/'.$row['class_name']] = $row;//peale classes/****
			}
		}
	}

/*
	echo "<pre>";
	print_r($versions);
	echo "</pre>";
*/
	$GLOBALS['cfg']['versions'] = $versions;
}

function table_exists ($table) {
// open db connection
	$result = mysql_query("show tables like '$table'") or die ('error reading database');
	if (mysql_num_rows ($result)>0)
		return true;
	else
		return false;
}

function load_versions_if_not_loaded()
{
	if(!isset($GLOBALS['cfg']['versions']))
	{
		load_versions();
	}
}

/** returns installed version class name
	@attrib api=1 params=pos
	@param class required string
		class name
	@returns string
		installed class
	@example
		$ext = "aw"; //class extension
		$path = core/obj; //class file location in classes menu
		$class_name = obj_predicate_limit; //class name

		$class = get_class_version($class_name);
		//$class = obj_predicate_limit_(version)

		$class = get_class_version($path."/".$class_name);
		//$class = core/obj/obj_predicate_limit_(version)

		$class = get_class_version("classes/".$path."/".$class_name."."$ext);
		//$class = classes/core/obj/obj_predicate_limit_(version).aw
	@comment
		if installed class not found, returns original
**/
function get_class_version($class)
{
return $class;
/*	$block_list = array("aw_request" , "aw_resource" , "tm");//ueh...ei tea mis teha... enne neid pole saidi ini sisse loetud
	if(in_array($class , $block_list))
	{
		return $class;
	}
*/
	load_versions_if_not_loaded();

	if(!isset($GLOBALS['cfg']['versions']))
	{
		return $class;
	}



	$add_aw_dir = 0;
	if(substr_count($class , AW_DIR))
	{
		$add_aw_dir = 1;
		$class = substr($class , strlen(AW_DIR));

	}

	$data = null;

	if(isset($GLOBALS['cfg']['versions'][basename($class)]))
	{
		$data = $GLOBALS['cfg']['versions'][basename($class)];
	}
//	elseif(isset($GLOBALS['cfg']['versions']["classes/".$class.".".aw_ini_get("ext")]))
//	{
//		$data = $GLOBALS['cfg']['versions']["classes/".$class.".".aw_ini_get("ext")];
//	}

	if($data)
	{
		if(substr_count($class,"/"))
		{
			$fs = $data["file_location"]."/".$data["class_name"];
		}
		else
		{
			$fs = $data["class_name"];
		}
		$ver_class = $fs."_".$data["file_version"];
	}
	else
	{
		if($add_aw_dir)
		{
			$class = AW_DIR.$class;
		}
		return $class;
	}
	if($_GET["DBG"] && $ver_class)
	{
		print "file: " . $class." , version: "; print $ver_class."<br>";
	}

	if($add_aw_dir)
	{
		$ver_class = AW_DIR.$ver_class;
	}
	return $ver_class;

}

/** returns installed file version
	@attrib api=1 params=pos
	@param file required string
		file name with path
	@returns string
		installed file name with path
	@example
		$file = "xml/properties/unit.xml"; //file with path

		$file = get_file_version($file);
		//$file = xml/properties/unit_(version).xml
	@comment
		if installed file not found, returns original
**/
function get_file_version($file)
{return $file;
//	print $file." - ".AW_DIR."<br>";

	load_versions_if_not_loaded();

	$beg = "";
	$add_aw_dir = 0;
	if(substr_count($file , AW_DIR))
	{
		$add_aw_dir = 1;
		$file = substr($file , strlen(AW_DIR));

	}
	if(substr($file , 0 , 1) == "/")
	{
		$beg = "/";
		$file = substr($file , 1);
	}

//print $file." - ".AW_DIR."<br>";

	$ver_file = "";
	$data = null;
/*	if(isset($GLOBALS['cfg']['versions'][basename($class)]))
	{
		$data = $GLOBALS['cfg']['versions'][basename($class)];
	}*/
	if(isset($GLOBALS['cfg']['versions'][$file]))
	{
		$data = $GLOBALS['cfg']['versions'][$file];
	}

	if($data)
	{
		$newfile_arr = explode("." , $file);
		if(sizeof($newfile_arr) > 1)
		{
			$ext = end($newfile_arr);
			unset($newfile_arr[sizeof($newfile_arr) - 1]);
			$fs = join("." , $newfile_arr);
		}
		else
		{
			$ext = "";
			$fs = $file;
		}
		$ver_file = $fs."_".$data["file_version"].($ext ? ".".$ext : "");
	}

	if(!empty($_GET["DBG"]) && $ver_file)
	{
		print "file :". $file." , version: "; print $ver_class."<br>";
	}

	if($ver_file)
	{
		if($add_aw_dir)
		{
			$ver_file = AW_DIR.$ver_file;
		}
		return $ver_file;
	}

	if($add_aw_dir)
	{
		$file = AW_DIR.$file;
	}

	//return $ver_class;
	return $beg.$file;
}

?>
