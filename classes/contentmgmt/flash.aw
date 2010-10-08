<?php

// flash.aw - Deals with flash applets
/*

	@default table=objects
	@default group=general
	@default method=serialize

	@property file type=fileupload field=meta
	@caption Vali fail

	@property file_url type=textbox field=meta method=serialize
	@caption Sisesta faili aadress

	@property click_tag type=textbox field=meta method=serialize
	@caption Link (Click Tag)

	@property width type=textbox size=4 field=meta
	@caption Laius

	@property height type=textbox size=4 field=meta
	@caption K&otilde;rgus

	@property preview type=text store=no
	@caption Eelvaade

@default group=transl

	@property transl type=callback callback=callback_get_transl
	@caption T&otilde;lgi

@groupinfo transl caption=T&otilde;lgi

*/

class flash extends class_base
{
	function flash()
	{
		$this->init(array(
			'tpldir' => 'flash',
			'clid' => CL_FLASH
		));

		$this->trans_props = array(
			"click_tag"
		);
	}

	function get_property($arr = array())
	{
		$data = &$arr["prop"];
		$retval = PROP_OK;
		switch($data["name"])
		{
			case "preview":
				if ($arr["obj_inst"]->prop("file"))
				{
					$data["value"] = $this->view(array("id" => $arr["obj_inst"]->id()));
				}
				else
				{
					$retval = PROP_IGNORE;
				};
				break;

			case "file":
				$data["value"] = "";
				break;

		};
		return $retval;
	}


	function set_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		if ($prop["name"] == "transl")
		{
			$this->trans_save($arr, $this->trans_props);
			return PROP_OK;
		}
		if ($prop["name"] == "file")
		{
			$fdata = $_FILES["file"];
			if (($fdata["type"] == "application/x-shockwave-flash" || $fdata["type"] == "application/x-director")  && is_uploaded_file($fdata["tmp_name"]))
			{
				$awf = get_instance(CL_FILE);
				$final_name = $awf->generate_file_path(array(
					"type" => $fdata["type"],
				));

				$imgdata = getimagesize($fdata["tmp_name"]);
				if (true || is_array($imgdata) && ($fc != ""))
				{
					$this->real_width = $imgdata[0];
					$this->real_height = $imgdata[1];
					move_uploaded_file($fdata["tmp_name"],$final_name);
					$prop["value"] = $final_name;

					if (!$arr["obj_inst"]->prop("name"))
					{
						$arr["obj_inst"]->set_prop("name",$fdata["name"]);
					}
				}
			}
			else
			{
				$retval = PROP_IGNORE;
			};
		};
		return $retval;
	}

	function callback_pre_save($arr = array())
	{
		// right now it's impossible to set those in the file upload
		// handler, because the original values from the form
		// will overwrite the values I'm going to set there
		if (isset($this->real_width) && isset($this->real_height))
		{
			$arr["obj_inst"]->set_prop("width",$this->real_width);
			$arr["obj_inst"]->set_prop("height",$this->real_height);
		}
	}

	function get_url($url)
	{
		if ($url)
		{
			$url = $this->mk_my_orb("show", array("file" => basename($url)),"flash",false,true,"&amp;");
			$url = str_replace("automatweb/","",$url);
		}
		else
		{
			$url = "";
		};
		return $url;
	}

	////
	// !this will be called if the object is put in a document by an alias and the document is being shown
	// parameters
	//    alias - array of alias data, the important bit is $alias[target] which is the id of the object to show
	function parse_alias($args)
	{
		extract($args);
		return $this->view(array('id' => $alias['target']));
	}

	function request_execute($obj_inst)
	{
		die($this->view(array("id" => $obj_inst->id())));
	}


	/**

		@attrib name=show params=name nologin="1" default="0"

		@param file required

		@returns

		@comment

	**/
	function show($arr)
	{
		session_write_close();
		extract($arr);
		$rootdir = aw_ini_get("site_basedir");
		$f1 = substr($file,0,1);
		$fname = $rootdir . "/img/$f1/" . $file;
		if ($file)
		{
			if (strpos("/",$file) !== false)
			{
				header("Content-type: text/html");
				print "access denied,";
			}

			// the site's img folder
			$passed = false;
			if (is_file($fname) && is_readable($fname))
			{
				$passed = true;
			}

			if (!$passed)
			{
				$rootdir = aw_ini_get("site_basedir");
				$fname = $rootdir . "/files/$f1/" . $file;
				if (is_file($fname) && is_readable($fname))
				{
					$passed = true;
				}
			}

			if ($passed)
			{
				header("Content-type: application/x-shockwave-flash");
				//for IE (flash over https)
				if ($_SERVER['SERVER_PORT'] == '443')
				{
					header("Pragma: cache");
				}
				readfile($fname);
			}
			else
			{
				print "access denied:";
			};
		}
		else
		{
			print "access denied;";
		}
		die();
	}

	function view($args = array())
	{
		extract($args);

		$ob = new object($id);

		$this->read_template('show.tpl');
		if ($ob->prop("file_url") != "")
		{
			$url = $ob->prop("file_url");
		}
		else
		{
			$url = $this->get_url($ob->prop("file"));
		}

		if (!empty($args["clickTAG"]))
		{
			$url = aw_url_change_var("clickTAG", $args["clickTAG"], $url);
			$url = str_ireplace("&clickTAG=", "&amp;clickTAG=", $url);
		}
		elseif ($ob->trans_get_val("click_tag"))
		{
			$url = aw_url_change_var("clickTAG", $ob->trans_get_val("click_tag"), $url);
			$url = str_ireplace("&clickTAG=", "&amp;clickTAG=", $url);
		}

		$tmp = '';
		if (aw_global_get("class") === "flash")
		{
			$tmp = $this->parse("IN_ADMIN");
		}

		$this->vars(array(
			"id" => "aw_flash_".$ob->id(),
			"url" => $url,
			"width" => $ob->prop("width"),
			"height" => $ob->prop("height"),
			"IN_ADMIN" => $tmp,
		));

		return $this->parse();
	}

	function callback_mod_tab($arr)
	{
		if ($arr["id"] === "transl" && aw_ini_get("user_interface.content_trans") != 1)
		{
			return false;
		}
		return true;
	}

	function callback_get_transl($arr)
	{
		return $this->trans_callback($arr, $this->trans_props);
	}
}
