<?php
// $Header: /home/cvs/automatweb_dev/classes/contentmgmt/server_folder.aw,v 1.12 2008/01/31 13:52:15 kristo Exp $
// server_folder.aw - Serveri Kataloog 
/*

@classinfo syslog_type=ST_SERVER_FOLDER no_comment=1 no_status=1 maintainer=kristo

@default table=objects
@default group=general

@property folder field=meta method=serialize type=textbox
@caption Kataloog serveris kust failid v&otilde;tta

@forminfo ch_file onload=init_chfile onsubmit=submit_chfile

@default form=ch_file

@property chf_view_file type=text form=ch_file
@caption Vaata faili

@property chf_file type=fileupload form=ch_file
@caption Uploadi fail


@forminfo add_file onload=init_addfile onsubmit=submit_addfile

@default form=add_file

@property addf_file type=fileupload form=add_file
@caption Uploadi fail

*/


class server_folder extends class_base
{
	function server_folder()
	{
		$this->init(array(
			"tpldir" => "contentmgmt/server_folder",
			"clid" => CL_SERVER_FOLDER
		));
	}

	function get_contents($o)
	{
		$fs = $this->get_directory(array(
			"dir" => $o->prop("folder")
		));
		$ret = array();
		foreach($fs as $file)
		{
			//$file = $o->id().":".$file;
			$ret[$file] = $file;
		}
		return $ret;
	}

	/** shows the file given in the argument from the server. 
		
		@attrib params=name name=show_file

		@param fid required

	**/
	function show_file($arr)
	{
		extract($arr);
		list($oid, $fname) = explode(":", $fid);
		error::raise_if(!is_oid($oid), array(
			"id" => ERR_PARAM,
			"msg" => sprintf(t("server_folder::show_file(%s): the fid parameter does not contain a valid object id!"), $fid)
		));
		$o = obj($oid);
		$fname = urldecode($fname);
		$fname = str_replace($o->prop("folder"), "", $fname);
		$fqfn = $o->prop("folder")."/".str_replace("..", "", $fname);
		if (!file_exists($fqfn))
		{
			$fqfn = $o->prop("folder")."/".urlencode(basename($fname));
		}
		
		$mt = get_instance("core/aw_mime_types");
		
		header("Content-type: ".$mt->type_for_file($fname));
		header("Content-Disposition: filename=".urlencode($fname));
		header("Pragma: no-cache");
		readfile($fqfn);
		die();
	}

	////
	// !deletes the given file from the server
	function del_file($fid)
	{
		list($oid, $fname) = explode(":", $fid);
		error::raise_if(!is_oid($oid), array(
			"id" => ERR_PARAM,
			"msg" => sprintf(t("server_folder::del_file(%s): the fid parameter does not contain a valid object id!"), $fid)
		));
		$o = obj($oid);
		$fqfn = $o->prop("folder")."/".basename($fname);

		@unlink($fqfn);
	}

	/** lets the user modify a file

		@attrib name=change_file params=name

		@param fid required
		@param section optional
	**/
	function change_file($arr)
	{
		$arr["form"] = "ch_file";
		return $this->change($arr);
	}

	function init_chfile($arr)
	{
		extract($arr);
		list($oid, $fname) = explode(":", $fid);

		error::raise_if(!is_oid($oid), array(
			"id" => ERR_PARAM,
			"msg" => sprintf(t("server_folder::change_file(%s): the fid parameter does not contain a valid object id!"), $fid)
		));
		$o = obj($oid);
		$fqfn = $o->prop("folder")."/".basename($fname);

		$this->obj_inst = $o;
		$this->fqfn = $fqfn;
		$this->fname = $fname;
		$this->fid = $fid;
	}

	/** saves the file to the server. called from classbase submit action
		
		@attrib params=name name=submit_chfile

	**/
	function submit_chfile($arr)
	{
		extract($arr);

		list($oid, $fname) = explode(":", $fid);
		error::raise_if(!is_oid($oid), array(
			"id" => ERR_PARAM,
			"msg" => sprintf(t("server_folder::change_file(%s): the fid parameter does not contain a valid object id!"), $fid)
		));
		$o = obj($oid);
		$old_fqfn = $o->prop("folder")."/".urldecode(basename($fname));
		$new_fqfn = $o->prop("folder")."/".urldecode(basename($_FILES["chf_file"]["name"]));
		if (is_uploaded_file($_FILES["chf_file"]["tmp_name"]))
		{
			move_uploaded_file($_FILES["chf_file"]["tmp_name"], $new_fqfn);
			unlink($old_fqfn);
			$fid = $oid.":".basename($_FILES["chf_file"]["name"]);
		}

		return $this->mk_my_orb("change_file", array("fid" => $fid, "section" => $section));
	}

	function get_property($arr)
	{
		$prop =& $arr["prop"];

		switch($prop["name"])
		{
			case "chf_view_file":
				$prop["value"] = html::href(array(
					"url" => $this->mk_my_orb("show_file", array("fid" => $arr["request"]["fid"])),
					"caption" => $this->fname
				));
				break;
		}
		return PROP_OK;
	}

	function callback_mod_reforb($arr)
	{
		if ($this->fid)
		{
			$arr["fid"] = $this->fid;
		}

		if ($this->id)
		{
			$arr["id"] = $this->id;
		}
	}

	/** lets the user add a file to the server folder

		@attrib name=add_file

		@param id required type=int
		@param section optional type=int

	**/
	function add_file($arr)
	{
		$arr["form"] = "add_file";
		return $this->change($arr);
	}

	function init_addfile($arr)
	{
		extract($arr);
		$this->id = $id;
	}


	/** saves the new file to the server
		
		@attrib params=name name=submit_addfile

	**/
	function submit_addfile($arr)
	{
		extract($arr);

		error::raise_if(!is_oid($id), array(
			"id" => ERR_PARAM,
			"msg" => sprintf(t("server_folder::submit_addfile(%s): the id parameter does not contain a valid object id!"), $id)
		));

		$o = obj($id);
		$fqfn = $o->prop("folder")."/".urldecode(basename($_FILES["addf_file"]["name"]));

		if (is_uploaded_file($_FILES["addf_file"]["tmp_name"]))
		{
			move_uploaded_file($_FILES["addf_file"]["tmp_name"], $fqfn);
		}

		$fid = $id.":".$_FILES["addf_file"]["name"];
		return $this->mk_my_orb("change_file", array("fid" => $fid, "section" => $section));
	}

	/////// object treeview interface functions

	function get_folders($ob, $tree_type = NULL)
	{
		classload("image");
		$list = array();
		$fld = $ob->prop("folder");
		$this->_recur_dir_list($fld, $list);

		$nl = array();

		foreach($list as $k => $d)
		{
			$k = str_replace($fld, "", $k);
			$d["id"] = str_replace($fld, "", $d["id"]);

			if ($d["parent"] == $fld)
			{
				$d["parent"] = 0;
			}
			else
			{
				$d["parent"] = str_replace($fld, "", $d["parent"]);
			}
			
			$nl[$k] = $d;
		}
		return $nl;
	}

	function _recur_dir_list($dir, &$list)
	{
		if ($dh = @opendir($dir)) 
		{
			while (false !== ($file = readdir($dh))) 
			{ 
				if ($file == "." || $file == "..")
				{
					continue;
				}

				$fn = $dir . "/" . $file;
				if (is_dir($fn))
				{
					$list[$fn] = array(
						"id" => $fn,
						"parent" => $dir,
						"name" => $file,
						"add_date" => filemtime($fn),
						"mod_date" => filemtime($fn),
						"icon" => image::make_img_tag(icons::get_icon_url(CL_MENU, "")),
						"jrk" => $num++
					);
					$this->_recur_dir_list($fn, $list);
				}
			}
			closedir($dh);
		}
	}

	function get_fields($ob)
	{
		return array(
			"name" => t("Nimi"),
			"file_size" => t("Faili suurus"),
			"created" => t("Loomise kuup&auml;ev"),
			"modified" => t("Muutmise kuup&auml;ev")
		);
	}

	function has_feature($f)
	{
		return false;
	}

	function get_objects($ob, $fld = NULL, $tv_sel = NULL, $params = array())
	{
		if ($tv_sel == "")
		{
			$dir = $ob->prop("folder");
		}
		else
		{
			$dir = $this->_get_safe_path($ob,$tv_sel);
		}

		$nmps = aw_ini_get("server.name_mappings");
		$list = array();
		if ($dh = @opendir($dir)) 
		{
			while (false !== ($file = readdir($dh))) 
			{ 
				$fn = $dir . "/" . $file;
				if (is_file($fn))
				{
					$udata = posix_getpwuid(fileowner($fn));
					$adder = $udata["name"];
					$file_p = str_replace($ob->prop("folder"), "", $fn);
					$fid = $ob->id().":".$file_p;

					if ($params["get_server_paths"])
					{
						$url = $fn;
						foreach($nmps as $_sname => $_spath)
						{
							$url = str_replace($_spath, $_sname, $url);
						}
						$url = "file://\\".str_replace("/", "\\", $url);
						$iurl = $this->mk_my_orb("show_file", array("fid" => $fid));
					}
					else
					{
						$url = $iurl = $this->mk_my_orb("show_file", array("fid" => $fid));
					}
					$list[$file_p] = array(
						"id" => $file_p,
						"name" => $file,
						"file_size" => filesize($fn),
						"add_date" => filemtime($fn),
						"mod_date" => filemtime($fn),
						"adder" =>	$adder,
						"modder" => $adder,
						"icon" => image::make_img_tag(icons::get_icon_url(CL_FILE, $fn)),
						"jrk" => $num++,
						"url" => $url,
						"inet_url" => $iurl,
						"change_url" => $this->mk_my_orb("change_file", array("fid" => $fid, "section" => aw_global_get("section"))),
						/*"change" => html::href(array(
							"url" => $this->mk_my_orb("change_file", array("fid" => $fid, "section" => aw_global_get("section"))),
							"caption" => html::img(array(
								"url" => aw_ini_get("baseurl")."/automatweb/images/icons/edit.gif",
								"border" => 0
							))
						))*/
					);
				}
			}
			closedir($dh);
		}
		return $list;
	}

	function check_acl($acl, $o, $id)
	{
		$fp = $this->_get_safe_path($o, $id);
		switch($acl)	
		{
			case "edit":
			case "add":
			case "delete":
				return is_writable($fp);
				break;
		}
		return false;
	}

	function _get_safe_path($o, $fn)
	{
		return $o->prop("folder").str_replace("..", "", urldecode($fn));
	}
}
?>
