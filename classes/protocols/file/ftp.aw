<?php
/*

@classinfo syslog_type=ST_FTP_LOGIN mantainer=kristo prop_cb=1

@default table=objects
@default group=general
@default field=meta
@default method=serialize

@property server type=textbox 
@caption Server

@property username type=textbox 
@caption Kasutajanimi

@property password type=password 
@caption Parool

@property default_folder type=textbox
@caption Vaikimisi kataloog

@default group=browser

	@layout split type=hbox width=30%:70%

		@layout tree_layout type=vbox closeable=1 area_caption="Kataloogid" parent=split

			@property ftp_tree type=treeview no_caption=1 store=no parent=tree_layout

		@property ftp_table type=table no_caption=1 store=no parent=split

@groupinfo browser caption="Brauser" submit=no save=no


*/

// XXX: what exactly is the point of those constants?
define("FTP_ERR_CONNECT", 1);
define("FTP_ERR_LOGIN", 2);
define("FTP_ERR_NOTCONNECTED", 3);

class ftp extends class_base
{
	var $handle = null;
	var $verbose = false;

	function ftp($arr = array())
	{
		$this->init(array(
			'clid' => CL_FTP_LOGIN
		));
		if (!empty($arr["verbose"]))
		{
			$this->verbose = $arr["verbose"];
		};
	}

	function is_available()
	{
		return extension_loaded("ftp");
	}

	
	/**
	@attrib api=1 params=name
		
	@param host required type=string
		The FTP server address.
		This parameter shouldn't have any trailing slashes and shouldn't be prefixed with ftp://
	@param user required type=string
	@param pass required type=string
	@param timeout optional type=int default=10;
		it does nothing
	
	@returns 
		FTP_ERR_CONNECT - if cant connect
		FTP_ERR_LOGIN - if cant login

	@examples
		$ftp_inst = get_instance("protocols/file/ftp");
		$ftp_inst->connect(array(
			"host" => "media.struktuur.ee",
			"user" => "keegi",
			"pass" => "kalakala",
		));
		$files = $ftp_inst->dir_list("files/);
		foreach($files as $file)
		{
			$fdat = $ftp_inst->get_file($file);
			$ftp_inst->delete($file);
		}
		if ($ftp_inst->cd(array("path" => "cool_files/") == true) echo 'this folder really does exist';
		$ftp_inst->disconnect();
	
	@comment creates connection to ftp server
	**/
	function connect($arr)
	{
		extract($arr);
		if (!isset($timeout))
		{
			$timeout = 10;
		}
	
		if ($this->verbose)
		{
			echo "connect, ".dbg::dump($arr)." <br />";
		};
		if (($this->handle = ftp_connect($host)) == FALSE)
		{
			if ($this->verbose)
			{
				echo "err_connect! <br />";
			};
			return FTP_ERR_CONNECT;
		}
		if (ftp_login($this->handle, $user, $pass) == FALSE)
		{
			if ($this->verbose)
			{
				echo "err login! <br />";
			};
			return FTP_ERR_LOGIN;
		}
		if ($this->verbose)
		{
			echo "success , $this->handle <br />";
		};
	}

	/**
	@attrib api=1
	@comment closes FTP connection
	
	@examples ${connect}
	**/
	function disconnect()
	{
		if ($this->verbose)
		{
			echo "closing connection ";
		};
		ftp_close($this->handle);
	}

	/**
	@attrib api=1 params=pos
		
	@param folder required
		The directory to be listed.
		This parameter can also include arguments, eg. ftp_nlist($conn_id, "-la /your/dir");
		Note that this parameter isn't escaped so there may be some issues with filenames 
		containing spaces and other characters
	
	@returns an array of filenames in the current server in folder $folder on success
		FTP_ERR_NOTCONNECTED if not connected

	@examples ${connect}
	**/
	function dir_list($folder, $full_info = false)
	{
		if (!$this->handle)
		{
			echo "notkonnekted! <br />";
			return FTP_ERR_NOTCONNECTED;
		}
		if ($full_info)
		{
			$syst = ftp_systype($this->handle);
			$_t = ftp_rawlist($this->handle, $folder);
			$ret = array();
			foreach($_t as $folder)
			{
				switch($syst)
				{
					case "NETWARE":
						$current = preg_split("/[\s]+/",$folder,8);
						if (trim($current[0]) == "total")
						{
							continue;
						}
           
						$narr= explode("/", $current[7]);
	
						$struc['perms']    = $current[1];
						$struc['owner']    = $current[2];
						$struc['group']    = $current[2];
						$struc['size']    = $current[3];
						$struc['month']    = $current[4];
						$struc['day']    = $current[5];
						$struc['time']    = $current[6];
						$struc['name']    = $narr[count($narr)-1];
						$struc["type"] = $current[0] == "d" ? "dir" : "file";
						$ret[] = $struc;
						break;
				
					default:
						$current = preg_split("/[\s]+/",$folder,9);
           
						$struc['perms']    = $current[0];
						$struc['number']= $current[1];
						$struc['owner']    = $current[2];
						$struc['group']    = $current[3];
						$struc['size']    = $current[4];
						$struc['month']    = $current[5];
						$struc['day']    = $current[6];
						$struc['time']    = $current[7];
						$struc['name']    = str_replace('//','',$current[8]);
						$struc["type"] = $struc["perms"][0] == "d" ? "dir" : "file";
						$ret[] = $struc;
						break;
				}
			}
			return $ret;
		}
		else
		{
			$_t = ftp_nlist($this->handle, $folder);
			$arr = new aw_array($_t);
			return $arr->get();
		}
	}

	/**
	@attrib api=1 params=pos
		
	@param file required type=string
		The remote file path
	@returns 
		contents of file $file in the current server
		FTP_ERR_NOTCONNECTED - if not connected
		FALSE if there is no file with that name
	
	@examples ${connect}
	**/
	function get_file($file)
	{
		if (!$this->handle)
		{
			return FTP_ERR_NOTCONNECTED;
		}

		$fn = tempnam(aw_ini_get("server.tmpdir"), "aw_ftp");
		$res = @ftp_get($this->handle, $fn, $file, FTP_BINARY);

		if ($res)
		{
			$fc = file_get_contents($fn);
			unlink($fn);
			return $fc;
		}
		else
		{
			if ($this->verbose)
			{
				echo "cannot find $file on server ";
			};
			return false;
		};

	}
	
	/**
	@attrib api=1 params=pos
		
	@param remote_file required type=string
		The remote file path
	@param file required type=string
		The local file
	@returns 
		FTP_ERR_NOTCONNECTED - if not connected
		TRUE on success
		FALSE on failure.
	
	@examples
	@comments 
		puts file to the current server
	**/
	function put_file($remote_file,$content)
	{
		if (!$this->handle)
		{
			return FTP_ERR_NOTCONNECTED;
		};
		$fn = tempnam(aw_ini_get("server.tmpdir"), "aw_ftp");

		$fh = fopen($fn,"w");
		fwrite($fh,$content);
		fclose($fh);

		$res = ftp_put($this->handle,$remote_file,$fn,FTP_BINARY);
		unlink($fn);
		return $res;
	}
	
	/**
	@attrib api=1 params=name
		
	@param file required type=string
		The file to delete
	@returns
		FTP_ERR_NOTCONNECTED - if not connected
		TRUE on success
		FALSE on failure
	
	@comments
		deletes $file on the current server
	@examples ${connect}
	**/
	function delete($arr)
	{
		if (!$this->handle)
		{
			return FTP_ERR_NOTCONNECTED;
		}
		if (ftp_delete($this->handle, $arr['file']))
		{
			return true;
		}
		return false;
	}

	/**
	@attrib api=1 params=name
		
	@param path required type=string
		The target directory
	@returns
		FTP_ERR_NOTCONNECTED - if not connected
		TRUE on success
		FALSE on failure
	
	@comments
		changes the directory on the current server to $path
	@examples ${connect}
	**/
	function cd($arr)
	{
		if (!$this->handle)
		{
			return FTP_ERR_NOTCONNECTED;
		}
		if (ftp_chdir($this->handle, $arr['path']))
		{
			return true;
		}
		return false;
	}
	
	/**
	@attrib api=1 params=pos
		
	@param url required type=string
		The target directory
	@returns
		string / contents of file
		FALSE on failure
	
	@comments
		Reads entire file into a string
	**/
	function get($url)
	{
		$this->last_url = $url;
		return file_get_contents($url);
	}
	
	/**
	@attrib api=1
	
	@returns string / file type
		FALSE , if there is no info for this extension
	
	@comments
		returns type of file last used with function get()
	**/
	function get_type()
	{
		$mt = get_instance("core/aw_mime_types");
		return $mt->type_for_file($this->last_url);
	}

	function _get_ftp_tree($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$t->start_tree(array(
			"type" => TREE_DHTML,
			"has_root" => 1,
			"tree_id" => "ftp_tree",
			"persist_state" => 1,
			"root_name" => $arr["obj_inst"]->prop("server"),
			"root_url" => aw_url_change_var("b_id", null),
			"get_branch_func" => $this->mk_my_orb("fetch_tree_node", array(
				"oid" => $arr["obj_inst"]->id(),
				"parent" => " ",
			))
		));

		$this->connect(array(
			"host" => $arr["obj_inst"]->prop("server"),
			"user" => $arr["obj_inst"]->prop("username"),
			"pass" => $arr["obj_inst"]->prop("password"),
		));
		$files = $this->dir_list("/", true);
		foreach($files as $file)
		{
			if ($file["type"] == "dir")
			{
				$t->add_item(0, array(
					"id" => $file["name"],
					"name" => $file["name"],
					"url" => $this->mk_my_orb("change", array("id" => $arr["obj_inst"]->id(), "group" => "browser", "folder" => $p."/".$file["name"]))
				));
				$t->add_item($file["name"], array(
					"id" => $file["name"]."1",
					"name" => $file["name"]
				));
			}
		}
	}

	/**
		@attrib name=fetch_tree_node
		@param oid required
		@param parent optional
	**/
	function fetch_tree_node($arr)
	{
		$o = obj($arr["oid"]);
		$t = get_instance("vcl/treeview");
		$t->start_tree(array(
			"type" => TREE_DHTML,
			"tree_id" => "ftp_tree",
			"persist_state" => 1,
			"get_branch_func" => $this->mk_my_orb("fetch_tree_node", array(
				"oid" => $o->id(),
				"parent" => " ",
			))
		));

		$this->connect(array(
			"host" => $o->prop("server"),
			"user" => $o->prop("username"),
			"pass" => $o->prop("password"),
		));
		$p = trim($arr["parent"]);
		if ($p[0] != "/")
		{
			$p = "/".$p;
		}
		$files = $this->dir_list($p, true);
		foreach($files as $file)
		{
			if ($file["type"] == "dir")
			{
				$t->add_item(0, array(
					"id" => $p."/".$file["name"],
					"name" => $file["name"],
					"url" => $this->mk_my_orb("change", array("id" => $o->id(), "group" => "browser", "folder" => $p."/".$file["name"]))
				));
				$t->add_item($p."/".$file["name"], array(
					"id" => $p."/".$file["name"]."1",
					"name" => $file["name"]
				));
			}
		}
		die($t->finalize_tree());
	}

	function _init_ftp_table(&$t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Fail"),
			"align" => "left",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "perms",
			"caption" => t("&Otilde;igused"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "owner",
			"caption" => t("Omanik"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "group",
			"caption" => t("Grupp"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "size",
			"caption" => t("Suurus"),
			"align" => "left",
			"numeric" => 1,
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "date",
			"caption" => t("Kuup&auml;ev"),
			"align" => "center",
		));
	}

	function _get_ftp_table($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_ftp_table($t);

		$this->connect(array(
			"host" => $arr["obj_inst"]->prop("server"),
			"user" => $arr["obj_inst"]->prop("username"),
			"pass" => $arr["obj_inst"]->prop("password"),
		));

		$p = $arr["request"]["folder"] == "" ? "/" : $arr["request"]["folder"];
		$files = $this->dir_list($p, true);
		foreach($files as $file)
		{
			if ($file["type"] != "dir")
			{
				$file["name"] = html::href(array(
					"url" => $this->mk_my_orb("get_file", array("id" => $arr["obj_inst"]->id(), "file" => $p."/".$file["name"])),
					"caption" => $file["name"]
				));
				$file["date"] = $file["day"]." ".$file["month"]." ".$file["time"];
				$t->define_data($file);
			}
		}
		$t->set_caption(sprintf(t("Failid kataloogis %s"), $p));
		$this->disconnect();
	}

	/**
		@attrib name=get_file
		@param id required type=int acl=view
		@param file optional
	**/
	function orb_get_file($arr)
	{
		$o = obj($arr["id"]);
		$this->connect(array(
			"host" => $o->prop("server"),
			"user" => $o->prop("username"),
			"pass" => $o->prop("password"),
		));
		$m = get_instance("core/aw_mime_types");
		header("Content-type: ".$m->type_for_file($arr["file"]));
		die($this->get_file($arr["file"]));
	}

	// otv interface
	function get_folders($ob, $tree_type = NULL)
	{
		$this->connect(array(
			"host" => $ob->prop("server"),
			"user" => $ob->prop("username"),
			"pass" => $ob->prop("password"),
		));
		$d = array();
		$this->_req_folders($d, $ob, $ob->prop("default_folder") == "" ? "~" : $ob->prop("default_folder"));
		$this->disconnect();
		return $d;
	}

	function _req_folders(&$d, $ob, $folder)
	{
		$files = $this->dir_list($folder, true);
		foreach($files as $file)
		{
			if ($file["type"] == "dir")
			{
				$f = $folder."/".$file["name"];
				$d[$f] = array(
					"id" => $f,
					"parent" => $folder,
					"name" => $file["name"],
				);
				$this->_req_folders($d, $ob, $f);
			}
		}
	}

	function get_fields($ob, $full_props = false)
	{
		return array(
			"name" => t("Nimi"),
			"date" => t("Kuup&auml;ev"),
			"perms" => t("&Otilde;igused"),
			"owner" => t("Omanik"),
			"group" => t("Grupp"),
			"size" => t("Suurus")
		);
	}
	
	function has_feature($str)
	{
		return false;
	}

	function get_objects($ob, $fld = NULL, $tv_sel = NULL, $params = array())
	{
		$this->connect(array(
			"host" => $ob->prop("server"),
			"user" => $ob->prop("username"),
			"pass" => $ob->prop("password"),
		));
		if ($fld == NULL)
		{
			$p = $ob->prop("default_folder");
		}
		else
		{
			$p = reset($fld);
			$p = $p["parent"];
		}

		$files = $this->dir_list($p, true);
		$r = array();
		foreach($files as $file)
		{
			if ($file["type"] != "dir")
			{
				$file["date"] = $file["day"]." ".$file["month"]." ".$file["time"];
				$id = $p."/".$file["name"];
				$file["url"] = $this->mk_my_orb("get_file", array("id" => $ob->id(), "file" => $p."/".$file["name"]));
				$file["id"] = $id;
				$file["parent"] = $p;
				$file["adder"] = $file["owner"];
				$file["modder"] = $file["owner"];
				$file["fileSizeBytes"] = $file["size"];
				$file["fileSizeKBytes"] = floor($file["size"]/1024);
				$file["fileSizeMBytes"] = floor($file["size"]/(1024*1024));
				$r[$id] = $file;
			}
		}
		$this->disconnect();

		return $r;
	}

	function check_acl($acl, $o, $id)
	{
		return true;
	}
}
?>
