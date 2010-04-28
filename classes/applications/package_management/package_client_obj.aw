<?php

namespace automatweb;


class package_client_obj extends _int_object
{
	const AW_CLID = 1450;

	/** Returns server package list
		@attrib api=1 params=name
		@param filter optional type=array
		@return array
	**/
	public function get_packages($filter)
	{
		$inst = $this->instance();
		$packages = array();
		if($this->prop("packages_server"))
		{
			$packages = $inst->do_orb_method_call(array(
				"class" => "package_server",
				"action" => "download_package_list",
				"method" => "xmlrpc",
				"server" => $this->prop("packages_server"),
				"no_errors" => true,
				"params" => $filter,
			));
		}
		return $packages;
	}

	/** Returns downloaded package list
		@attrib api=1
		@return array
	**/
	public function get_downloaded_packages()
	{
		$filter = array("site_id" => $this->site_id());
		$inst = $this->instance();
		$packages = array();
		if($this->prop("packages_server"))
		{
			$packages = $inst->do_orb_method_call(array(
				"class" => "package_server",
				"action" => "download_package_list",
				"method" => "xmlrpc",
				"server" => $this->prop("packages_server"),
				"no_errors" => true,
				"params" => $filter,
			));
		}
		return $packages;
	}

	/** Returns downloaded package list
		@attrib api=1
		@return array
	**/
	public function get_installed_packages()
	{
		$packages = array();
		$files = $this->get_installed_files_data();
		foreach($files as $file)
		{
			$key = $file["package_id"].$file["package_name"].$file["package_version"];
			$packages[$key] = array(
				"package_id" => $file["package_id"],
				"package_name" => $file["package_name"],
				"package_version" => $file["package_version"],
			);
		}
		return $packages;
	}

	/** Returns made packages
		@attrib api=1
		@return object list
			package object list
	**/
	public function get_made_packages()
	{
		$filter = array(
			'class_id' => CL_PACKAGE,
			'parent' => $this->prop('packages_folder_aw'),
			'site_id' => array(),
			'lang_id' => array(),
			'available' => new obj_predicate_not(1),
			'installed' => new obj_predicate_not(1),
		);

		$ol = new object_list($filter);

		$server_packages = $this->get_packages();
//see j22b aeglaseks varsti, a kyll siis ymber teeb
		foreach($ol->arr() as $o)
		{
			foreach($server_packages as $p)
			{
				if($o->name() == $p["name"] && $o->prop("version") == $p["version"])
				{
			//		$ol->remove($o->id());
					continue;
				}
			}
		}
		
		return $ol;
	}

	/** Download and install package
		@attrib api=1 params=pos
		@param id required type=oid
			package object id in package server
	**/
	public function download_package($id)
	{
		if($this->prop("packages_server"))
		{
			$url = $this->prop("packages_server")."/orb.aw?class=package_server&action=download_package_file&id=".$id."&site_id=".$this->site_id();
			$inst = $this->instance();

			$fs = $inst->do_orb_method_call(array(
				"class" => "package_server",
				"action" => "get_package_file_size",
				"method" => "xmlrpc",
				"server" => $this->prop("packages_server"),
				"no_errors" => true,
				"params" => array(
					'id' => $id,
				),
			));

			$data = $inst->do_orb_method_call(array(
				"class" => "package_server",
				"action" => "download_package_properties",
				"method" => "xmlrpc",
				"server" => $this->prop("packages_server"),
				"no_errors" => true,
				"params" => array(
					'id' => $id,
				),
			));
//
//			$handle = fopen($url, "r");//arr($handle);
//			$contents = fread($handle, $fs);arr($contents);
//$contents = $this->curl_get_file_contents($url);arr($contents);
			$contents = file_get_contents($url);
			$fn = aw_ini_get("server.tmpdir")."/".gen_uniq_id().".zip";
			$fp = fopen($fn, 'w');
			fwrite($fp, $contents);
			fclose($fp);

			$package_id = $this->add_installed_package(array(
				"name" => $data["name"],
				"version" => $data["version"],
				"description" => $data["description"],
				"file" => $fn,
				"file_versions" => $data["file_versions"],
			));
			$data["package_id"] = $package_id;
			$data["file_name"] = $fn;
			$this->install_package($data);
		}
	}

	/** 
		@attrib name=download_package_properties params=pos api=1
		@param id required type=oid
			package object id
		@return array
			package object property values
 	**/
	public function download_package_properties($id)
	{
		$inst = $this->instance();
		$data = $inst->do_orb_method_call(array(
			"class" => "package_server",
			"action" => "download_package_properties",
			"method" => "xmlrpc",
			"server" => $this->prop("packages_server"),
			"no_errors" => true,
			"params" => array(
				'id' => $id,
			),
		));
		return $data;
	}

	private function install_package($data)
	{
		$pi = get_instance(CL_PACKAGE);
		$this->db_table_name = "site_file_index";
		$inst = $this->instance();
		$basedir = aw_ini_get("site_basedir").'/files/packages_tmp/';

		$zip = new ZipArchive;
		$zip->open($data["file_name"]);
//		arr($zip->numFiles);
//arr($data["file_name"]);

		$tbl = $inst->db_get_table($this->db_table_name);
		if (!isset($tbl["fields"]["file_ext"]))
		{
			$inst->db_add_col($this->db_table_name, array(
				"name" => "file_ext",
				"type" => "CHAR(8)"
			));
		}
		if (!isset($tbl["fields"]["class_name"]))
		{
			$inst->db_add_col($this->db_table_name, array(
				"name" => "class_name",
				"type" => "varchar(255)"
			));
		}

//pakib failid failide kataloogi saidi juurde
		$res = $zip->extractTo($basedir);

		for ($i=0; $i<$zip->numFiles;$i++)
		{
			$dat =  $zip->statIndex($i);
			if($dat["comp_method"])
			{
				$temp_path = $basedir.$dat["name"];

				if($dat["name"] == "script.php")
				{
					include $temp_path;
					print t("Installi skript k2ivitus")."<br>\n";
					continue;
				}
				$path = aw_ini_get("basedir").'/'.$dat["name"];
//		if($dat["index"] == 2) $dir = $dat["name"];
//		$files[] = $dat["name"];//arr($dat);
//		$path = substr($dat["name"] , strpos($dat["name"],"/", -1));
//		if(!strpos($path,"/", 1))
//		{
//			continue;
//		}

//testiks
				$file_name = basename($path);
				$location =  dirname($dat["name"]);
				if($file_version)
				{
					$file_version = $data["file_versions"][$dat["name"]];
				}
				$ext = $fs = "";

				$newfile_arr = explode("." , $file_name);
				if(sizeof($newfile_arr) > 1)
				{
					$ext = end($newfile_arr);
					unset($newfile_arr[sizeof($newfile_arr) - 1]);
					$fs = join("." , $newfile_arr);
				}
				else
				{
					$ext = "";
					$fs = $file_name;
				}
				
			//andmebaasi kirje sellest installist
				if(!$file_version)$file_version = $data["file_versions"]['/'.$dat["name"]];
				$classes = array($fs => $fs);

				if($ext == "aw")//iga klass eraldi
				{
					$filea = file($path);
					foreach($filea as $file)
					{
						$strMatchesArray = array();
						preg_match_all('/class[\s]+([\w]*)/i', $file, $strMatchesArray);
						
						if($strMatchesArray[1][0])
						{
							$classes[$strMatchesArray[1][0]] = $strMatchesArray[1][0];
	
						}
					}
				}

				foreach($classes as $class)
				{
					$sql = "insert into ".$this->db_table_name."(
						file_name,
						file_version,
						file_location,
						class_name,
						file_ext,
						package_name,
						package_version,
						package_id,
						used,
						installed_date,
						dependences
					) values (
						'".$file_name."',
						'".$file_version."',
						'".$location."',
						'".$class."',
						'".$ext."',
						'".$data["name"]."',
						'".$data["version"]."',
						".$data["package_id"].",
						1,
						".time().",
						'123'
					)";
				}
				$this->uninstall_file(array(
					"name" => $file_name,
					"location" => $location,
				));
				$pi->db_query($sql);

//$path = substr($path , strpos($path,"/", 1)+1);
//selle peab paremini t88le saama... p2rast ei jaksa keegi seda jama kustutada muidu
//arr($res);arr($dat);arr($temp_path);
//$lines = file($temp_path);
//arr($lines);
//$path = str_replace($data["name"]."-".$data["version"] , "" , $path);
//$path = str_replace($data["name"] , "" , $path);
//$path = str_replace($data["name"] , "" , $path);
//arr($path);
			
			//fail kopeeritakse 6igesse kohta ja kustutatakse temp versioon

				$newfile_arr = explode("." , $file_name);
				if(sizeof($newfile_arr) > 1)
				{
					$ext = end($newfile_arr);
					unset($newfile_arr[sizeof($newfile_arr) - 1]);
					$fs = join("." , $newfile_arr);
				}
				else
				{
					$ext = "";
					$fs = $file_name;
				}
				$newfile = aw_ini_get("basedir").'/'.$location."/".$fs."_".$file_version.".".$ext;
				$newfile_without_version = aw_ini_get("basedir").'/'.$location."/".$fs.".".$ext;
				
				$success = copy($temp_path, $newfile);
				$success2 = copy($temp_path, $newfile_without_version);//selle paneb nii, et m6ni fail, mida ei kutsutaks v2lja versiooni j2rgi, m6juks ka

				unlink($temp_path);
				print ($success? "success" : "fail")." <br>\n";	
				print $newfile." <br>\n";	
			}
		}
		die("all done...");
	}

	private function uninstall_file($arr)
	{
		$pi = get_instance(CL_PACKAGE);
		$sql = "UPDATE ".$this->db_table_name." SET used=0 where file_name='".$arr["name"]."' AND file_location='".$arr["location"]."'";
		$pi->db_query($sql);
	}


	/** uninstalls package from system, id or name&&version must be set
		@attrib api=1 params=name
		@param server_id optional type=oid
			package object id in server
		@param id optional type=oid
			package object id
		@param name optional type=string
			package object name
		@param version optional type=string
			package version
	**/
	public function restore_package($arr)
	{
		$this->db_table_name = "site_file_index";
		$filter = array("class_id" => CL_PACKAGE);
		if(is_oid($arr["id"]))
		{
			$filter["oid"] = $arr["id"];
			$ol = new object_list($filter);
			$o = reset($ol->arr());
			$sql = "UPDATE ".$this->db_table_name." SET used=1 where package_id='".$arr["id"]."'";
			$inst->db_query($sql);
		}
		elseif(strlen($arr["name"]) && strlen($arr["version"]))
		{
			$filter["name"] = $arr["name"];
			$filter["version"] = $arr["version"];
			$ol = new object_list($filter);
			$o = reset($ol->arr());
			$sql = "UPDATE ".$this->db_table_name." SET used=1 where package_name='".$arr["name"]."' AND package_version='".$arr["version"]."'";
arr($sql); die();
			$inst->db_query($sql);
		}
		else
		{
			//tahaks teha kudagi nii, et kui oma systeemis leiab paketi faili , siis kasutaks seda, a kui ei leia, siis uuendab serverist
			arr($this->get_packages(array(
				"id" => $id,
			)));
		}
		return false;
	}

	/** uninstalls package from system, id or name&&version must be set
		@attrib api=1 params=name
		@param id optional type=oid
			package object id
		@param name optional type=string
			package object name
		@param version optional type=string
			package version
	**/
	public function uninstall_package($arr)
	{
		$this->db_table_name = "site_file_index";
		$inst = $this->instance();
		if(is_oid($arr["id"]))
		{
			$sql = "UPDATE ".$this->db_table_name." SET used=0 where package_id='".$arr["id"]."'";
		}
		elseif(strlen($arr["name"]) && strlen($arr["version"]))
		{
			$sql = "UPDATE ".$this->db_table_name." SET used=0 where package_name='".$arr["name"]."' AND package_version='".$arr["version"]."'";
		}
		else
		{
			return false;
		}
		$inst->db_query($sql);
		return true;
	}

	/** Adds installed package object to the system
		@attrib api=1 params=name
		@param name optional type=string
			package object name
		@param version optional type=string
			package version
		@param description optional type=string
			package description
		@param file optional type=string
			package zip file path
		@return oid
			new package object id
	**/
	function add_installed_package($params)
	{
		$o = new object();
		$o->set_class_id(CL_PACKAGE);
		$o->set_parent($this->id());
		$o->set_name($params["name"] ? $params["name"] : t("Nimetu pakett"));

		$o->set_prop("version" , $params["version"]);
		$o->set_prop("description" , $params["description"]);
		$o->set_prop("installed" , 1);
		$o->save();

		$file = new object();
		$file->set_class_id(CL_FILE);
		$file->set_parent($o->id());
		$file->set_name($o->name());
		$file->save();

		$o->connect(array(
			"to" => $file->id(),
			"reltype" => "RELTYPE_FILE",
		));

		if(file_exists($params["file"]))
		{
			$handle = fopen($params["file"], "r");
			$contents = fread($handle, filesize($params["file"]));
			$type = "zip";
		
			fclose($handle);
		
			$data["id"] = $file->id();
			$data["return"] = "id";
			$data["file"] = array(
				"content" => $contents,
				"name" => $o->name(),
				"type" => $type,
			);
			$t = new file();
			$rv = $t->submit($data);
		}
		return $o->id();
	}

	/** Uploads package to server
		@attrib api=1 params=pos
		@param id optional type=oid
	**/
	public function upload_package($id)
	{
		$client = $this->instance();
		$url = $this->prop("packages_server")."/orb.aw?class=package_server&action=upload_package&id=".$id."&site_id=".$this->site_id()."&return_url=".urlencode($client->mk_my_orb("change", array(
			"id" => $this->id(),
			"clid" => CL_PACKAGE_CLIENT,
			"group" => "packages",
		)));
		header("Location: ".$url);
		die();
		$this->do_nothing();
	}

	function do_nothing()
	{
		sleep(5);
		return ;
	}

	function curl_get_file_contents($URL)
	{
		$c = curl_init();
		curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($c, CURLOPT_URL, $URL);
		$contents = curl_exec($c);
		curl_close($c);
	
		if ($contents)
		{
			return $contents;
		}
		else
		{
			return FALSE;
		}
	}

	/** Returns package files list from server
		@attrib api=1 params=pos
		@param id required type=oid
			package object id
		@returns array
	**/
	public function get_files_list($id)
	{
		$files = array();
		if($this->prop("packages_server"))
		{
			$inst = $this->instance();
			$files = $inst->do_orb_method_call(array(
				"class" => "package_server",
				"action" => "download_package_files",
				"method" => "xmlrpc",
				"server" => $this->prop("packages_server"),
				"no_errors" => true,
				"params" => array(
					'id' => $id,
				),
			));
		}
		return $files;
	}

	/** returns installed files data
		@attrib api=1 params=name
		@param old_files optional type=int
			if set, returns uninstalled files info
		@param search_file_name optional type=string
			file name
		@returns array
	**/
	public function get_installed_files_data($arr)
	{
		$inst = $this->instance();
		$this->db_table_name = "site_file_index";
		$filt = array();
		if(isset($arr['old_files']) && $arr['old_files'])
		{
			$filt[] = "used < 1";
		}
		else
		{
			$filt[] = "used = 1";
		}
		if(isset($arr['search_file_name']) && strlen($arr['search_file_name']) > 0)
		{
			$filt[] = "file_name LIKE '%".$arr['search_file_name']."%'";
		}
		$sql = "select * FROM ".$this->db_table_name." WHERE ".join(" AND " , $filt);
//arr($sql);
		$inst->db_query($sql);
		$rv = array();
		while($row = $inst->db_next())
		{
			$row["file_exists"] = file_exists(AW_DIR.$row["file_location"]."/".$row["class_name"]."_".$row["file_version"].($row["file_ext"] ? ".".$row["file_ext"] : ""));
			$rv[] = $row;
		};
		return $rv;
	}

}

?>
