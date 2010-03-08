<?php
/*

@classinfo maintainer=dragut

*/
class package_obj extends _int_object
{
	function get_dependencies()
	{
		$ol = new object_list();
		$dep_conns = $this->connections_from(array(
			"class_id" => CL_PACKAGE,
			"type" => "RELTYPE_DEPENDENCY",
		));
		foreach($dep_conns as $conn)
		{
			$ol->add($conn->to());
		}
		return $ol;
	}

	function get_files()
	{
		$ol = new object_list();
		$file_conns = $this->connections_from(array(
			"class_id" => CL_FILE,
			"type" => "RELTYPE_FILE",
		));
		foreach($file_conns as $conn)
		{
			$ol->add($conn->to());
		}
		return $ol;
	}

	function get_sites_used()
	{
		$ret = array();
		$conns = $this->connections_from(array(
			"type" => "RELTYPE_SITE_RELATION",
		));
		foreach($conns as $conn)
		{
			$o = $conn->to();
			$ret[] = $o->prop("site");
		}
		return $ret;
	}

	function download_package()
	{
		$file_objects = $this->get_files();
		foreach($file_objects->arr() as $file_object)
		{
			$data = $file_object->get_file();
		}

		
/*		header ("Content-Type: application/xml");

		$out_charset = "ISO-8859-4";
		$this_object = obj ($arr["id"]);
		$import_url = $this_object->prop ("city24_import_url");
		$xml = file_get_contents ($import_url);
		// $xml = iconv ("UTF-8", $out_charset, $xml);
		// $xml = preg_replace ('/encoding\=\"UTF\-8\"/Ui', 'encoding="' . $out_charset . '"', $xml, 1);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $import_url);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
		// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_TIMEOUT, 600);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 100);
		// $xml = curl_exec($ch);
		curl_exec($ch);
		curl_close($ch);

		// echo $xml;
		exit;
*/

		header("Content-type: application/zip");
		header("Content-length: ".filesize($data["properties"]["file"]));
		header("Content-disposition: inline; filename=".$data["name"].";");
		print $data["content"];
		flush();
		die();
	}

	function get_package_file_size()
	{
		$file_objects = $this->get_files();
		foreach($file_objects->arr() as $file_object)
		{
			$data = $file_object->get_file();
		}
		return filesize($data["properties"]["file"]);
	}

	function add_site($site_id)
	{
		$rel = new object();
		$rel->set_class_id(CL_PACKAGE_SITE_RELATION);
		$rel->set_parent($this->id());
		$rel->set_prop("site" , $site_id);
		$rel->save();
		$this->connect(array(
			"to" => $rel->id(),
			"reltype" => "RELTYPE_SITE_RELATION"
		));
		return $rel->id();
	}

	function get_package_file_names()
	{
		$files = array();
		$file_objects = $this->get_files();
		foreach($file_objects->arr() as $file_object)
		{
			if(!$file_object->prop("file"))
			{
				continue;
			}
			$data = $file_object->get_file();
			$zip = new ZipArchive;
			$zip->open($data["properties"]["file"]);
			for ($i=0; $i<$zip->numFiles;$i++) {
				$dat =  $zip->statIndex($i);
    				if($dat["comp_method"])
				{
					$files[] = $dat["name"];
				}
			}
		}
		if(!sizeof($files))
		{
			$files[] = t("failiobjekt ei sisalda &uuml;htegi faili");
		}
		return $files;
	}

	function set_package_file_names()
	{
		$files = $this->get_package_file_names();
		$filenamestring = join("<br>\n" , $files);
		$this->set_prop("file_names" , $filenamestring);
		$this->save();
	}

	function get_class_folders($dir)
	{
		$dp = opendir($dir);
		$bd = aw_ini_get("basedir");
		$folders = array();
		while (false !== ($filename = readdir($dp)))
		{
			if($filename != '.' && $filename != '..' && $filename != "CVS")
			{
				$this->fid++;
				if(is_dir($dir.'/'.$filename))
				{
					$folder = array();
					$newdir = $dir.'/'.$filename;
					$folder["id"] = $this->fid;
					$folder["name"] = $filename;
					$folder["folder"] = $newdir;
					$folder["level"] = $this->get_class_folders($newdir);
					$folders[] = $folder;
				}
			}
		}
		usort($folders, array(self, "__file_arr_sort"));
		return $folders;
	}

	function get_contents_class_files($dir)
	{
		$dp = opendir($dir);
		$files = array();
		while (false !== ($filename = readdir($dp)))
		{
			if($filename != '.' && $filename != '..' && substr($filename, 0, 2) != ".#")
			{
				$this->fid++;
				if(!is_dir($dir.'/'.$filename))
				{
					$file = array();
					$file["id"] = $this->fid;
					$file["name"] = $filename;
					$files[] = $file;
				}
			}
		}
		usort($files, array(self, "__file_arr_sort"));
		return $files;
	}

	function __file_arr_sort($a, $b)
	{
		return strcasecmp($a["name"], $b["name"]);
	}


	//this should maybe look for some more files, not just xml props and orbs
	function get_other_files($file)
	{
		$fname = aw_ini_get("basedir").$file;
		$data = @file_get_contents($fname);
		$others = array();
		if($data)
		{
			if(strpos($data, "extends class_base"))
			{
				$name = str_replace(".aw", "", basename($file));
				$xml = array("properties", "orb");
				foreach($xml as $x)
				{
					$check = "/xml/".$x."/".$name.".xml";
					if(file_exists(aw_ini_get("basedir").$check))
					{
						$others[] = $check;
					}
				}
				$dir = "/templates".str_replace("classes/", "", str_replace(".aw", "", $file));
				$tpldir = aw_ini_get("basedir").$dir;
				if(is_dir($tpldir))
				{
					$files = $this->get_contents_class_files($tpldir);
					foreach($files as $file)
					{
						$others[] = $dir."/".$file["name"];
					}
				}
			}
		}
		return $others;
	}

	function get_file_version($file)
	{
		$fn = basename($file);
		$dir = substr($file,1,(strlen($file) - strlen($fn)-1));
		$cvs_file = "/www/dev/marko/automatweb_dev/".$dir."CVS/Entries";
		$handle = fopen($cvs_file, "r");
		$contents = fread($handle, filesize($cvs_file));
		fclose($handle);
		$content_rows = explode("\n",$contents);
		foreach($content_rows as $row)
		{
			$content_data = explode("/",$row);
			if($content_data[1] ==$fn)
			{
				return $content_data[2];
			}
		}
		return "";
	}

	function create_package_zip($o)
	{
		$files = $o->meta("package_contents");
		//et faili versioone hakkaks 6igest kohast v6tma
		chdir('..');
		$versions = array();

		if(!is_array($files) || !count($files))
		{
			return;
		}
		$i = new file_archive;
		$fi = get_instance(CL_FILE);
		$zipname = str_replace(" " , "_" , $this->name()).(($v = $this->prop("version"))?"-".$v:"").".zip";
		$ftype = "application/zip";
		$fpath = $fi->generate_file_path(array("file_name" => $zipname, "type" => $ftype));
		$folders = array();
		foreach($files as $filepath)
		{
			$versions[$filepath] = $this->get_file_version($filepath);
			$pieces = explode("/", $filepath);
			$path = "";
			foreach($pieces as $piece)
			{
				if(empty($piece))
				{
					continue;
				}
				$add = "/".$piece;
				$file = aw_ini_get("basedir").$path.$add;
				if(is_dir($file) && array_search($path.$add, $folders) === false)
				{
					$to = substr($path, 1);
					$i->add_folder($piece, $to);
					$path .= $add;
					$folders[] = $path;
				}
				elseif(is_dir($file))
				{
					$path .= $add;
				}
				elseif(is_file($file))
				{
					$i->add_file_fs($file, "", $path);
				}
			}
		}
		$this->set_meta("file_versions" , $versions);
		$this->save();
		$i->save_as_file($fpath);
		$fo = obj();
		$fo->set_class_id(CL_FILE);
		$fo->set_name($zipname);arr($fpath);
		$fo->set_prop("type", $ftype);
		$fo->set_prop("file", $fpath);
		$fo->set_parent($this->id());
		$fo->save();
		$this->connect(array(
			"to" => $fo->id(),
			"type" => "RELTYPE_FILE",
		));arr($fo->id());
	}

	/** Returns package dependency names and versions
		@attrib api=1
		@returns array(name => version , ...)
	**/
	public function get_dep_versions()
	{
		$ret = $this->meta("dependency_packages");
		if(!is_array($ret))
		{
			$ret = array();
		}
		$dep_conns = $this->connections_from(array(
			"type" => "RELTYPE_DEPENDENCY",
		));
		foreach($dep_conns as $conn)
		{
			$pack = $conn->to();
			$ret[$pack->name()] = $pack->prop("version");
		}
		return $ret;
	}

	/** Adds dependency
		@attrib api=1 params=pos
		@param package required type=oid
			package object id
		@returns 1, if success.... else false
	**/
	public function add_dependency($package = null)
	{
		if(is_oid($package))
		{
			$this->connect(array(
				"to" => $package,
				"type" => "RELTYPE_DEPENDENCY",
			));
			return 1;
		}
		else return false;
	}

	private function get_next_version()
	{
		$version = $this->prop("version");
		$explode = explode("." , $version);
		if(!isset($explode[2]))
		{
			$explode[2] = 0;
		}
		$explode[2]++;
		$version = join("." , $explode);
		return $version;
	}

	/** Creates new version of package using the same files
		@attrib api=1
		@returns 1, if success.... else false
	**/
	public function create_new_package()
	{
		$o = new object();
		$o->set_name($this->name());
		$o->set_class_id(CL_PACKAGE);
		$o->set_parent($this->parent());
		$o->set_prop("version" , $this->get_next_version());
		$o->set_prop("description" , $this->prop("description"));
		$o->set_meta("package_contents" , $this->meta("package_contents"));
		$o->save();
		$o->create_package_zip($this);arr($this->get_dependencies());
		foreach($this->get_dependencies()->ids() as $dep)
		{
			$o->add_dependency($dep);
		}
		return $o->id();
	}

}
?>
