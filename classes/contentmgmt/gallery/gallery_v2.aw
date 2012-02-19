<?php
// gallery.aw - gallery management

/*

@classinfo syslog_type=ST_GALLERY relationmgr=yes

@groupinfo page_1 caption=Lehek&uuml;lg&nbsp;1
@groupinfo page_2 caption=Lehek&uuml;lg&nbsp;2
@groupinfo page_3 caption=Lehek&uuml;lg&nbsp;3
@groupinfo page_4 caption=Lehek&uuml;lg&nbsp;4
@groupinfo page_5 caption=Lehek&uuml;lg&nbsp;5
@groupinfo page_6 caption=Lehek&uuml;lg&nbsp;6
@groupinfo page_7 caption=Lehek&uuml;lg&nbsp;7
@groupinfo page_8 caption=Lehek&uuml;lg&nbsp;8
@groupinfo page_9 caption=Lehek&uuml;lg&nbsp;9

@groupinfo import caption=Impordi
@groupinfo preview caption=Eelvaade

@default table=objects
@default group=general
@default field=meta
@default method=serialize

@property conf_id type=text size=3
@caption Konfiguratsioon:

@property reinit_layout type=checkbox ch_value=1
@caption Uuenda layout (kustutab k&otilde;ik pildid!)

@property num_pages type=textbox size=3
@caption Mitu lehte:

@property rate_redir type=textbox
@caption Suuna p&auml;rast h&auml;&auml;letamist

@property pg_1_content type=text group=page_1 no_caption=1
@property pg_2_content type=text group=page_2 no_caption=1
@property pg_3_content type=text group=page_3 no_caption=1
@property pg_4_content type=text group=page_4 no_caption=1
@property pg_5_content type=text group=page_5 no_caption=1
@property pg_6_content type=text group=page_6 no_caption=1
@property pg_7_content type=text group=page_7 no_caption=1
@property pg_8_content type=text group=page_8 no_caption=1
@property pg_9_content type=text group=page_9 no_caption=1

@property preview type=text group=preview no_caption=1

@default group=import

@property import_ftp type=checkbox ch_value=1
@caption Impordi FTP serverist

@property ftp_login type=relpicker reltype=RELTYPE_FTP_LOGIN
@caption FTP Server

@property ftp_folder type=textbox
@caption FTP Serveri kataloog

@property import_local type=checkbox ch_value=1 default=1
@caption Impordi kataloogist

@property local_folder type=textbox
@caption Kataloog

@property import_zip type=checkbox ch_value=1
@caption Impordi ZIP failist

@property zip_file type=fileupload store=no
@caption Uploadi ZIP fail

@property import_overwrite type=checkbox ch_value=1
@caption Importimisel kirjuta olemasolevad pildid &uuml;le

@property import_add_pages type=checkbox ch_value=1
@caption Importimisel lisa vajadusel lehek&uuml;lgi

@property do_import type=checkbox ch_value=1
@caption Teosta import

@classinfo no_status=1

@reltype FTP_LOGIN value=1 clid=CL_FTP_LOGIN
@caption ftp login

*/

classload("image");
class gallery_v2 extends class_base
{
	function gallery_v2($id = 0)
	{
		$this->init(array(
			"tpldir" => "gallery",
			"clid" => CL_GALLERY_V2
		));
		$this->sub_merge = 1;
	}

	function parse_alias($args = array())
	{
		return $this->show(array(
			"oid" => $args["alias"]["target"]
		));
	}


	/**

		@attrib name=view params=name nologin="1" default="0"

		@param id optional type=int
		@param page optional type=int
		@param col optional type=int
		@param row optional type=int

		@returns


		@comment

	**/
	function view($args = array())
	{
		return $this->show(array(
			"oid" => $args["id"],
		));
	}

	/**
		@attrib name=submit_rates nologin="1" no_login=1
	**/
	function submit_rates($arr)
	{
		$has = false;
		foreach(safe_array($arr["rate"]) as $oid => $rval)
		{
			$ri = get_instance(CL_RATE);
			$ri->add_rate(array("no_redir" => 1, "oid" => $oid, "rate" => $rval));
			if ($rval)
			{
				$has = true;
			}
		}
		$go = obj($arr["gid"]);
		if ($has && $go->prop("rate_redir") != "")
		{
			return $go->prop("rate_redir");
		}
		return $arr["r"];

	}

	function get_property(&$arr)
	{
		$prop =& $arr['prop'];
		if ($prop['name'] == "preview")
		{
			$prop['value'] = $this->show(array(
				"oid" => $arr['obj_inst']->id(),
			));
		}
		else
		if ($prop['name'] == "conf_id")
		{
			if (!($pt = $arr['obj_inst']->parent()))
			{
				$pt = $arr['request']['parent'];
			}
			$cid = $this->_get_conf_for_folder($pt);
			if (!$cid)
			{
				$prop['value'] = t("Sellele kataloogile pole konfiguratsiooni valitud!");
			}
			else
			{
				$prop['value'] = html::href(array(
					'url' => $this->mk_my_orb('change', array('id' => $cid), 'gallery_conf'),
					'caption' => 'Muuda'
				));
			}
		}
		else
		if (substr($prop['name'], 0, 3) == 'pg_')
		{
			$prop['value'] = $this->_get_edit_page(array(
				"oid" => $arr['obj_inst']->id(),
				"page" => (int)substr($prop['name'], 3, 1)
			));
		}
		else
		if ($prop['name'] == "do_import")
		{
			if (!($arr['obj_inst']->prop('import_local') == 1 || $arr['obj_inst']->prop('import_ftp') == 1 || $arr['obj_inst']->prop('import_zip') == 1))
			{
				return PROP_IGNORE;
			}
			//$prop['value'] = "Impordi";
		}
		else
		if ($prop['name'] == "ftp_host" || $prop['name'] == "ftp_user" || $prop['name'] == "ftp_pass" || $prop['name'] == "ftp_folder")
		{
			classload("protocols/file/ftp");
			if (!ftp::is_available())
			{
				return PROP_IGNORE;
			}
		}

		return PROP_OK;
	}

	function _get_conf_for_folder($pt)
	{
		$i = get_instance(CL_IMAGE);
		return $i->_get_conf_for_folder($pt);
	}

	function callback_mod_tab($parm)
	{
		$id = $parm['id'];
		$od = $parm["obj_inst"];
		if (substr($id, 0, 5) == 'page_')
		{
			$pgnr = substr($id, 5);
			if ($pgnr > $od->prop('num_pages'))
			{
				return false;
			}
		}
		return true;
	}

	function _get_edit_page($arr)
	{
		extract($arr);
		$obj = obj($oid);

		$tmp = $obj->meta("page_data");
		$page_data = $tmp[$page]['layout'];
		if (!$page_data && ($def_layout = $this->_get_default_layout($obj)))
		{
			// this the first time this page is edited, so get the default layout for it
			$l = get_instance(CL_LAYOUT);
			$page_data = $l->get_layout($def_layout);
		}

		$ge = new grid_editor();
		return $ge->on_edit($page_data, $oid, array(
			"cell_content_callback" => array(&$this, "_get_edit_cell_content", array("obj" => $obj, "page" => $page))
		));
	}

	function _get_edit_cell_content($params, $row, $col)
	{
		$obj = $params['obj'];
		$page = $params['page'];

		$tmp = $obj->meta("page_data");
		$pd = $tmp[$page]['content'][$row][$col];
		$this->read_template("grid_edit_cell.tpl");
		$this->vars(array(
			'page' => $page,
			'row' => $row,
			'col' => $col,
			"imgurl" => image::check_url($pd['tn']['url']),
			"bigurl" => image::check_url($pd['img']['url']),
			'caption' => $pd['caption'],
			'date' => $pd['date'],
			'has_textlink' => checked($pd['has_textlink']),
			'textlink' => $pd['textlink'],
			'ord' => $pd['ord'],
		));

		$this->vars(array("HAS_IMG" => ""));
		if ($pd['tn']['id'])
		{
			$this->vars(array(
				"HAS_IMG" => $this->parse("HAS_IMG")
			));
		}
		$this->vars(array("BIG" => ""));
		if ($pd['img']['id'])
		{
			$this->vars(array(
				"BIG" => $this->parse("BIG")
			));
		}
		return $this->parse();
	}

	function set_property(&$arr)
	{
		$prop = &$arr['prop'];
		$obj = $arr["obj_inst"];
		$meta = $arr["obj_inst"]->meta();
		if (substr($prop['name'],0,3) == "pg_")
		{
			$page_number = (int)substr($prop['name'], 3, 1);
			$pg_data = $arr["obj_inst"]->meta("page_data");

			$page_data = $pg_data[$page_number]['layout'];
			// _get_default_layout only needs a parent
			if (!$page_data && ($def_layout = $this->_get_default_layout($arr["obj_inst"])))
			{
				// this the first time this page is edited, so get the default layout for it
				$l = get_instance(CL_LAYOUT);
				$page_data = $l->get_layout($def_layout);
			}

			$this->_page_content = $pg_data[$page_number]['content'];

			// _get_image_folder also only needs the parent
			// this obj thingie gets passed to _set_edit_cell_content, which only uses
			// that image_folder value
			$obj->set_meta("image_folder",$this->_get_image_folder($obj));

			$ge = new grid_editor();
			$pg_data[$page_number]['layout'] = $ge->on_edit_submit(
				$page_data,
				$arr['request'],
				array(
					"cell_content_callback" => array(&$this, "_set_edit_cell_content", array("obj" => $obj, "page" => $page_number))
				)
			);
			$pg_data[$page_number]['content'] = $this->_page_content;
			$arr['obj_inst']->set_meta("page_data", $pg_data);
		}
		if ($prop['name'] == "reinit_layout" && $prop["value"] == 1)
		{
			$arr['obj_inst']->set_meta('page_data', array());
			$prop['value'] = 0;
		}
		if ($prop["name"] == "zip_file")
		{
			global $zip_file;
			if (is_uploaded_file($zip_file))
			{
				$tn = aw_ini_get("server.tmpdir")."/".gen_uniq_id();
				if (move_uploaded_file($zip_file, $tn))
				{
					$arr["obj_inst"]->set_meta("up_zip_file",$tn);
					chmod($tn, 0666);
				}
				else
				{
					$this->raise_error("move_uploaded_file ($zip_file, $tn) failed!");
				}
			}
		}
		return PROP_OK;
	}

	function callback_post_save($arr)
	{
		$ob = obj($arr["id"]);
		$meta = $ob->meta();
		$ometa = $meta;

		if ($meta['do_import'] != "" && $arr["request"]["group"] == "import")
		{
			set_time_limit(14400);
			if ($meta['import_overwrite'] == 1)
			{
				$this->_clear_images($meta);
			}

			if ($meta["import_zip"] == 1)
			{
				// un_zip_file was set in set_property
				$zf = escapeshellarg($arr["obj_inst"]->meta("up_zip_file"));
				$zip = aw_ini_get("server.unzip_path");
				$tn = aw_ini_get("server.tmpdir")."/".gen_uniq_id();
				mkdir($tn,0777);
				$cmd = $zip." -d $tn $zf";
				$op = shell_exec($cmd);
				$meta["import_local"] = 1;
				$meta["local_folder"] = $tn;
				$ometa["import_local"] = 1;
				$ometa["local_folder"] = $tn;
				chmod($tn, 0777);
			}

			if ($meta["import_ftp"] == 1)
			{
				$ftp = get_instance(CL_FTP_LOGIN,array("verbose" => true));
				if (!$ftp->is_available())
				{
					return;
				}

				$ftp->connect(array(
					"host" => $meta['ftp_host'],
					"user" => $meta['ftp_user'],
					"pass" => $meta['ftp_pass'],
				));
				$files = $ftp->dir_list($meta['ftp_folder']);
			}
			else
			if ($meta["import_local"] == 1)
			{
				$files = array();
				if ($dir = @opendir($meta['local_folder']))
				{
					while (($file = readdir($dir)) !== false)
					{
						if (!($file == "." || $file == ".."))
						{
							$files[] = $file;
						}
					}
					closedir($dir);
				}
				else
				{
					$this->raise_error(ERR_NO_FOLDER,sprintf(t("Sellist kataloogi pole serveris! (%s)"), $meta["local_folder"]), false, true);
				}
				sort($files);
			}

			$img = get_instance(CL_IMAGE);
			$img_folder = $this->_get_image_folder($ob);

			$conf = get_instance(CL_GALLERY_CONF);
			$conf_id = $this->_get_conf_for_folder($ob->parent());
			$conf_o = obj($conf_id);

			echo "Impordin faile, palun oodake... <br /><br />\n\n";
			flush();
			foreach($files as $file)
			{
				echo "Leidsin pildi $file <br />\n";
				flush();

				if ($ometa["import_ftp"] == 1)
				{
					$fc = $ftp->get($ometa["ftp_folder"]."/".$file);
				}
				else
				if ($ometa["import_local"] == 1)
				{
					$fc = $this->get_file(array("file" => $ometa['local_folder']."/".$file));
				}

				$img = get_instance("core/converters/image_convert");
				$img->load_from_string($fc);

				// get image size
				list($i_width, $i_height) = $img->size();

				$xydata = $this->_get_xydata(array(
					"i_width" => $i_width,
					"i_height" => $i_height,
					"conf_o" => $conf_o
				));
				extract($xydata);

				// the conf object may specify a different size for images, so resize if necessary
				if (($width && ($width != $i_width)) || ($height && ($height != $i_height)))
				{
					$img->resize_simple($width, $height);
				}

				$_t = $img->copy();

				if ($conf_o->meta("insert_logo") == 1)
				{
					$_t = $this->_do_logo($_t, $conf_o);
				}

				$fc = $_t->get(IMAGE_JPEG);

				$img_inst = get_instance(CL_IMAGE);
				$idata = $img_inst->add_image(array(
					"str" => $fc,
					"orig_name" => $file,
					"parent" => $img_folder
				));

				// now we gots to make a thumbnail and add that as well.
				$n_img = $img->copy();

				// now if the user specified that the thumbnail is subimage, then cut it out first
				if ($tn_is_subimage && $tn_si_width && $tn_si_height)
				{
					$n_img->resize(array(
						"x" => $tn_si_left,
						"y" => $tn_si_top,
						"width" => $tn_si_width,
						"height" => $tn_si_height,
						"new_width" => $tn_width,
						"new_height" => $tn_height
					));
				}
				else
				{
					$n_img->resize_simple($tn_width, $tn_height);
				}
				$img->destroy();
				$img = $n_img;

				if ($conf_o->meta("tn_insert_logo") == 1)
				{
					$img = $this->_do_logo($img, $conf_o, "tn_");
				}

				$fc = $img->get(IMAGE_JPEG);

				$tn_idata = $img_inst->add_image(array(
					"str" => $fc,
					"orig_name" => $file,
					"parent" => $img_folder
				));

				// and now we need to add the image to the first empty slot
				$r = $this->_get_next_free_pos($meta, $_pg, $_row, $_col, $ob);
				if ($r === false && $meta['import_add_pages'] == 1)
				{
					// add page to the end
					$this->_add_page($meta, $ob);
					$r = $this->_get_next_free_pos($meta, $_pg, $_row, $_col, $ob);
				}

				if ($r != false)
				{
					$_pg = $r[0];
					$_row = $r[1];
					$_col = $r[2];
					$meta['page_data'][$_pg]['content'][$_row][$_col]['img'] = $idata;
					$meta['page_data'][$_pg]['content'][$_row][$_col]['tn'] = $tn_idata;
					$ob->set_meta("page_data", $meta["page_data"]);
					$this->db_query("INSERT INTO g_img_rel(img_id, tn_id) VALUES('".$idata["id"]."','".$tn_idata["id"]."')");
				}
				$ob->set_meta("do_import","");
				$ob->save();
				$meta = $ob->meta();
			}
			if ($meta["import_zip"] == 1)
			{
				@unlink($meta["up_zip_file"]);
				$ob->set_meta("import_local", 0);
				$ob->set_meta("local_folder", "");
				@unlink($meta["up_zip_file"]);
				if ($dir = @opendir($tn))
				{
					while (($file = readdir($dir)) !== false)
					{
						if (!($file == "." || $file == ".."))
						{
							@unlink($tn."/".$file);
						}
					}
					closedir($dir);
				}
				@rmdir($tn);
			}

			$ob->set_meta("do_import", "");
			$ob->save();
			echo "Valmis! <a href='".$this->mk_my_orb("change", array("id" => $arr["id"], "group" => "import"))."'>Tagasi</a><br />\n";
			die();
		}
	}

	function _add_page(&$meta, $ob)
	{
		$l = get_instance(CL_LAYOUT);
		$page_data = $l->get_layout($this->_get_default_layout($ob));

		$ob->set_meta("num_pages", $meta['num_pages']+1);
		$meta['page_data'][$meta['num_pages']]['layout'] = $page_data;
		$ob->set_meta("page_data", $meta["page_data"]);
	}

	function _get_next_free_pos(&$meta, $page, $row, $col, $ob)
	{
		// ok, we start from the pos, and scan left->right and up->down and then pages, until we find a place
		// that is empty

		if (!$page)
		{
			$page = 1;
		}
		for ($_page = 1; $_page <= $meta['num_pages']; $_page++)
		{
			if (!$meta['page_data'][$_page]['layout'])
			{
				// insert default layout
				$l = get_instance(CL_LAYOUT);
				$meta['page_data'][$_page]['layout'] = $l->get_layout($this->_get_default_layout($ob));
			}

			for ($_row = 0; $_row < $meta['page_data'][$_page]['layout']['rows']; $_row++)
			{
				for ($_col = 0; $_col < $meta['page_data'][$_page]['layout']['cols']; $_col++)
				{

					$col_data = $meta['page_data'][$_page]['content'][$_row][$_col];
					if ($_page == $page && $_row >= $row)
					{
						if ($_row == $row)
						{
							if ($_col >= $col)
							{
								if (!$col_data["img"]["id"])
								{
									return array($_page, $_row, $_col);
								}
							}
						}
						else
						{
							if (!$col_data["img"]["id"])
							{
								return array($_page, $_row, $_col);
							}
						}
					}
					else
					if ($_page > $page)
					{
						if (!$col_data["img"]["id"])
						{
							return array($_page, $_row, $_col);
						}
					}
				}
			}
		}
		return false;
	}

	function _clear_images(&$meta)
	{
		for ($_page = 1; $_page < $meta['num_pages']; $_page++)
		{
			for ($_row = 0; $_row < $meta['page_data'][$_page]['layout']['rows']; $_row++)
			{
				for ($_col = 0; $_col < $meta['page_data'][$_page]['layout']['cols']; $_col++)
				{
					$meta['page_data'][$_page]['content'][$_row][$_col]['img'] = array();
					$meta['page_data'][$_page]['content'][$_row][$_col]['tn'] = array();
				}
			}
		}
	}

	function _set_edit_cell_content($params, $row, $col, $post_data)
	{
		$obj = $params['obj'];
		$page = $params['page'];

		$old = $this->_page_content[$row][$col];
		// check uploaded images and shit
		$cd = $post_data['g'][$page][$row][$col];
		$this->_page_content[$row][$col]["caption"] = $cd["caption"];
		$this->_page_content[$row][$col]["date"] = $cd["date"];
		$this->_page_content[$row][$col]["has_textlink"] = $cd["has_textlink"];
		$this->_page_content[$row][$col]["textlink"] = $cd["textlink"];
		$this->_page_content[$row][$col]["ord"] = $cd["ord"];

		// also upload images
		$img_n = "g_".$page."_".$row."_".$col."_img";

		$imgfolder = $obj->meta("image_folder");
		if (!$imgfolder)
		{
			$imgfolder = $obj->parent();
		}

		if (trim($_FILES[$img_n]["type"]) == "application/pdf" || ($old["img"]["is_file"] == 1 && $_FILES[$img_n]["tmp_file"] == ""))
		{
			$f = get_instance(CL_FILE);
			$this->_page_content[$row][$col]["img"] = $f->add_upload_image(
				$img_n,
				$imgfolder,
				$old['img']['id']
			);
			$this->_page_content[$row][$col]["img"]["is_file"] = 1;
		}
		else
		if ($_FILES[$img_n]["tmp_name"] != "")
		{
			$f = get_instance(CL_IMAGE);
			$this->_page_content[$row][$col]["img"] = $f->add_upload_image(
				$img_n,
				$imgfolder,
				$old['img']['id']
			);
			$this->_page_content[$row][$col]["img"]["is_file"] = 0;
		}

		$img_n = "g_".$page."_".$row."_".$col."_tn";

		$f = get_instance(CL_IMAGE);
		$this->_page_content[$row][$col]["tn"] = $f->add_upload_image(
			$img_n,
			$imgfolder,
			$old['tn']['id']
		);

		$d = $this->db_fetch_row("SELECT * FROM g_img_rel WHERE img_id = '".$this->_page_content[$row][$col]["img"]["id"]."'");
		if (!is_array($d))
		{
			$this->db_query("INSERT INTO g_img_rel(img_id, tn_id) VALUES('".$this->_page_content[$row][$col]["img"]["id"]."','".$this->_page_content[$row][$col]["tn"]["id"]."')");
		}
		else
		{
			$this->db_query("UPDATE g_img_rel SET tn_id = '".$this->_page_content[$row][$col]["tn"]["id"]."' WHERE img_id = '".$this->_page_content[$row][$col]["img"]["id"]."'");
		}

		$del = $post_data['erase'][$page][$row][$col];
		if ($del)
		{
			$this->_page_content[$row][$col]["img"] = array();
			$this->_page_content[$row][$col]["tn"] = array();
		}
	}

	function _get_image_folder($obj)
	{
		if (is_object($obj))
		{
			$parent = $obj->parent();
		}
		else
		{
			$parent = $obj["parent"];
		}
		// get it from conf
		$cf = get_instance(CL_GALLERY_CONF);
		$tmp = $cf->get_image_folder($this->_get_conf_for_folder($parent));

		if (is_oid($tmp) && $this->can("view", $tmp))
		{
			return $tmp;
		}
		return $parent;
	}

	function _show_rate_objs($ob,$robj)
	{
		$rate = array();
		// I want to access that array from show to validate our argument
		$this->rateobjs = $this->_get_rate_objs($ob);
		foreach($this->rateobjs as $oid => $name)
		{
			$this->vars(array(
				"link" => $this->mk_link(array("section" => aw_global_get("section"),"oid" => $oid,"date" => aw_global_get("date")),true),
				"name" => $name,
			));
			$rate[] = $this->parse($oid == $robj ? "RATE_OBJ_SEL" : "RATE_OBJ");
		}
		$this->vars(array(
			"RATE_OBJ" => join($this->parse("RATE_OBJ_SEP"), $rate),
			"RATE_OBJ_SEP" => "",
			"RATE_OBJ_SEL" => "",
		));
	}

	function _show_pageselector($ob, $page, $callback = "_page_has_images")
	{
		// now pageselector
		$pages = array();
		$ps_back = "";
		$ps_fwd = "";
		$num_pages = $ob->meta('num_pages');

		$sp = 1;
		$p2rp = array();
		$cur_sp = 0;
		for($pg = 1; $pg <= $num_pages; $pg++)
		{
			if (!$this->$callback($ob, $pg))
			{
				continue;
			}
			$p2rp[$sp] = $pg;
			if ($pg == $page)
			{
				$cur_sp = $sp;
			}
			$sp++;
		}

		foreach($p2rp as $sp => $pg)
		{
			$url = aw_url_change_var("page", $pg);
			$this->vars(array(
				"link" => $url,
				"page_num" => $sp,
				"section" => aw_global_get("section")
			));
			if ($pg == $page)
			{
				$pages[] = $this->parse("SEL_PAGE");
			}
			else
			{
				$pages[] = $this->parse("PAGE");
			}

			if ($cur_sp-1 == $sp)
			{
				$ps_back = $this->parse("PAGESEL_BACK");
			}

			if ($cur_sp+1 == $sp)
			{
				$ps_fwd = $this->parse("PAGESEL_FWD");
			}
		}

		$this->vars(array(
			"PAGE" => join($this->parse("PAGE_SEP"), $pages),
			"SEL_PAGE" => "",
			"PAGESEL_BACK" => $ps_back,
			"PAGESEL_FWD" => $ps_fwd
		));

		if ($sp > 1)
		{
			$this->vars(array(
				"HAS_PAGESEL" => $this->parse("HAS_PAGESEL")
			));
		}
	}

	function show($arr)
	{
		extract($arr);
		global $page;

		if (empty($ob))
		{
			$ob = obj($oid);
		}

		if ($page < 1 || $page > $ob->meta('num_pages'))
		{
			$page = 1;
		}

		$pd = $ob->meta("page_data");
		$c = $pd[$page]['content'];
		$l = $pd[$page]['layout'];

		$this->read_any_template("show_v2.tpl");

		// decide which rate object to use
		$robj = aw_global_get("oid");

		// ok, do draw, first draw all rate objs
	//	var_dump($robj); var_dump($ob);
		$this->_show_rate_objs($ob,$robj);

		// and if that doesn't belong here, then show the default view
		if (empty($this->rateobjs[$robj]))
		{
			unset($robj);
		};

		$this->rating = get_instance(CL_RATE);

		if (!empty($robj))
		{
			// get the order, I dunno why, but rating->show wants a reference
			// to the array
			$tmp = array(
				"id" => $robj,
				"from_oid" => $oid,
			);

			// show is misleading, it merely returns the image order array
			$imorder = $this->rating->show($tmp);

			$this->reorder($ob,$imorder);
		}

		$this->_show_pageselector($ob, $page);

		// now all images

		// get all hit counts for all images, so that we won't do a query for each image
		$tmp = $ob->meta("page_data");
		$pd = $tmp[$page]['content'];
		$this->hits = $this->_get_hit_counts($pd);

		$li = new grid_editor();
		$li->_init_table($l);

		$this->has_images = false;

		$this->vars(array(
			"num_cols" => $li->get_num_cols(),
			"layout" => $li->show_tpl($l, $oid, array(
				"tpl" => "gallery/show_v2_layout.tpl",
				"cell_content_callback" => array(&$this, "_get_show_cell_content", array("obj" => $ob, "page" => $page)),
				"ignore_empty" => true
			)),
			"name" => $ob->name(),
			"reforb" => $this->mk_reforb("submit_rates", array(
				"r" => get_ru(),
				"gid" => $ob->id()
			))
		));

		$ret = $this->parse();

		if (!$this->has_images)
		{
			return "";
		}

		return $ret;
	}

	////
	// reorders images based using information in a rate object
	//	ob - gallery object
	//	order - array of image ids, the order they are shown in
	function reorder(&$ob,$order)
	{
		//extract($arr);
		global $page;

		$newd = array();
		$used = array();

		// reorder images as per order
		for($_page = 1; $_page <= $ob->meta('num_pages'); $_page++)
		{
			$tmp = $ob->meta("page_data");
			$pd_l = $tmp[$_page]['layout'];
			$newd[$_page]['layout'] = $pd_l;
			for($row = 0; $row < $pd_l['rows']; $row++)
			{
				for($col = 0; $col < $pd_l['cols']; $col++)
				{
					list($imid) = each($order);
					$used[$imid] = $imid;
					// now find the image in the current gallery
					$dat = $this->_find_image($imid, $ob);
					if (is_array($dat))
					{
						$newd[$_page]['content'][$row][$col] = $dat;
					}
					else
					{
						$dat = $this->_find_next_unused($used, $ob);
						$used[$dat['img']['id']] = $dat['img']['id'];
						$newd[$_page]['content'][$row][$col] = $dat;
					}
				}
			}
		}

		$ob->set_meta('page_data',$newd);
	}

	function _find_image($id, $ob)
	{
		if (!$id)
		{
			return false;
		}

		for($page = 1; $page <= $ob->meta('num_pages'); $page++)
		{
			$tmp = $ob->meta("page_data");
			$pd_l = $tmp[$page]['layout'];
			$pd = $tmp[$page]['content'];
			for($row = 0; $row < $pd_l['rows']; $row++)
			{
				for ($col = 0; $col < $pd_l['cols']; $col++)
				{
					if ($pd[$row][$col]['img']['id'] == $id || $pd[$row][$col]['tn']['id'] == $id)
					{
						$retval = $pd[$row][$col];
						// I need to know _where_ the image was y'know
						$retval["row"] = $row;
						$retval["col"] = $col;
						$retval["page"] = $page;
						return $retval;
					}
				}
			}
		}

		return false;
	}

	function _find_next_unused($used, $ob)
	{
		for($page = 1; $page <= $ob->meta('num_pages'); $page++)
		{
			$tmp = $ob->meta("page_data");
			$pd_l = $tmp[$page]['layout'];
			$pd = $tmp[$page]['content'];
			for($row = 0; $row < $pd_l['rows']; $row++)
			{
				for ($col = 0; $col < $pd_l['cols']; $col++)
				{
					if (!isset($used[$pd[$row][$col]['img']['id']]))
					{
						return $pd[$row][$col];
					}
				}
			}
		}
		return false;
	}


	function _get_show_cell_content($params, $row, $col)
	{
		$obj = $params['obj'];
		$page = $params['page'];

		$tmp = $obj->meta("page_data");
		$pd = $tmp[$page]['content'][$row][$col];

		if (!$pd['img']['id'] && !$pd['tn']['id'])
		{
			return "";
		}

		if ($pd['img']['id'])
		{
			$w = $pd['img']['sz'][0];
			$h = $pd['img']['sz'][1]+70;
		}
		else
		{
			$w = $pd['tn']['sz'][0];
			$h = $pd['tn']['sz'][1]+70;
		}

		if ($pd["img"]["is_file"])
		{
			$fi = get_instance(CL_FILE);
			$link = $fi->get_url($pd["img"]["id"],"sisu.pdf");
		}
		else
		{
			$link = $this->mk_my_orb("show_image", array(
				"id" => $obj->id(),
				"page" => $page,
				"row" => $row,
				"col" => $col,
				"img_id" => $pd['img']['id']
			));
		}

		if (empty($w))
		{
			$w = 700;
		};
		if ($h == 70)
		{
			$h = 600;
		};

		if ($pd["img"]["is_file"])
		{
			$fi = get_instance(CL_FILE);
			$link = $fi->get_url($pd["img"]["id"],"sisu.pdf");
		}
		else
		if ($pd['img']['id'])
		{
			$link = $this->mk_my_orb("show_image", array(
				"id" => $obj->id(),
				"page" => $page,
				"row" => $row,
				"col" => $col,
				"img_id" => $pd['img']['id']
			));
		}
		else
		{
			$link = $this->mk_my_orb("show_image", array(
				"id" => $obj->id(),
				"page" => $page,
				"row" => $row,
				"col" => $col,
				"img_id" => $pd['tn']['id']
			));
		}
		$this->has_images = true;
		$rating = $this->rating->get_rating_for_object($pd['img']['id'] ? $pd['img']['id'] : $pd['tn']['id']);

		$tp = new aw_template;
		$tp->tpl_init("gallery");
		$tp->read_template("show_v2_cell_content.tpl");
		$tp->vars(array(
			"width" => $w,
			"height" => $h,
			"link" => $link,
			"img" => image::make_img_tag(image::check_url($pd['tn']['url'])),
			"rating" => $rating,
			"hits" => $this->hits[$pd['img']['id']],
			"date" => $pd["date"],
			"caption" => $pd["caption"],
			"img_id" => $pd['img']['id'] ? $pd['img']['id'] : $pd['tn']['id']
		));

		$rsi = "";
		if (count($this->_get_rate_objs($obj)) > 0)
		{
			$sc = get_instance(CL_RATE_SCALE);
			$scale = $sc->get_scale_for_obj(($pd['img']['id'] ? $pd['img']['id'] : $pd['tn']['id']));
			foreach($scale as $sci_val => $sci_name)
			{
				$tp->vars(array(
					"rate_link" => $this->mk_my_orb("rate", array(
						"oid" => ($pd['img']['id'] ? $pd['img']['id'] : $pd['tn']['id']),
						"return_url" => $post_rate_url,
						"rate" => $sci_val
					), "rate"),
					"scale_value" => $sci_name
				));
				$rsi.=$tp->parse("RATING_SCALE_ITEM");
			}
		}

		$tp->vars(array(
			"RATING_SCALE_ITEM" => $rsi,
		));
		if ($rating != "")
		{
			$tp->vars(array(
				"HAS_RATING_SCALE" => $this->parse("HAS_RATING_SCALE")
			));
		}

		if ($pd["date"] != "")
		{
			$tp->vars(array(
				"HAS_DATE" => $tp->parse("HAS_DATE")
			));
		}
		if ($pd["caption"] != "")
		{
			$tp->vars(array(
				"HAS_CAPTION" => $tp->parse("HAS_CAPTION")
			));
		}
		return $tp->parse();
	}

	function _get_rate_objs($obj)
	{
		$ret = array();
		$fold = $this->_get_conf_for_folder($obj->parent());
		if(empty($fold))
		{
			return $ret;
		}
		$cf = get_instance(CL_GALLERY_CONF);
		$ros = new aw_array($cf->get_rate_objects($fold));

		$this->db_query("SELECT oid,name FROM objects WHERE oid IN(".$ros->to_sql().")");
		while ($row = $this->db_next())
		{
			$ret[$row['oid']] = $row['name'];
		}
		return $ret;
	}

	/**

		@attrib name=show_image params=name nologin="1" default="0"

		@param id optional type=int
		@param page optional type=int
		@param col optional type=int
		@param row optional type=int
		@param img_id optional type=int

		@returns


		@comment

	**/
	function show_image($arr)
	{
		extract($arr);
		if(empty($col))
		{
			$col = "";
		}


		$ob = obj($id);
		if ($img_id)
		{
			$pd = $this->_find_image($img_id, $ob);
		}
		else
		{
			$tmp = $ob->meta("page_data");
			$pd = $tmp[$page]['content'][$row][$col];
		}

		$this->read_any_template("show_v2_image.tpl");

		$p_page = $n_page = $page;
		$p_row = $n_row = $row;
		$p_col = $n_col = $col;
		$tmp = $ob->meta("page_data");
		do {
			list($p_page, $p_row, $p_col) = $this->_get_prev_img($ob, $p_page, $p_row, $p_col);
		} while ($p_page > 0 && (!$tmp[$p_page]['content'][$p_row][$p_col]['img']['id']));

		do {
			list($n_page, $n_row, $n_col) = $this->_get_next_img($ob, $n_page, $n_row, $n_col);
		} while ($n_page <= $ob->meta('num_pages') && (!$tmp[$n_page]['content'][$n_row][$n_col]['img']['id']));

		if ($n_page > $ob->meta('num_pages'))
		{
			$post_rate_url = $this->mk_my_orb("show_image", array(
				"id" => $id,
				"page" => $page,
				"row" => $row,
				"col" => $col
			));
		}
		else
		{
			$post_rate_url = $this->mk_my_orb("show_image", array(
				"id" => $id,
				"page" => $n_page,
				"row" => $n_row,
				"col" => $n_col
			));
		}

		$rsi = "";
		if (count($this->_get_rate_objs($ob)) > 0)
		{
			$sc = get_instance(CL_RATE_SCALE);
			$scale = $sc->get_scale_for_obj(($pd['img']['id'] ? $pd['img']['id'] : $pd['tn']['id']));
			foreach($scale as $sci_val => $sci_name)
			{
				$this->vars(array(
					"rate_link" => $this->mk_my_orb("rate", array(
						"oid" => ($pd['img']['id'] ? $pd['img']['id'] : $pd['tn']['id']),
						"return_url" => $post_rate_url,
						"rate" => $sci_val
					), "rate"),
					"scale_value" => $sci_name
				));
				$rsi.=$this->parse("RATING_SCALE_ITEM");
			}
		}

		$this->add_hit($pd['img']['id']);

		$email_link = $this->mk_my_orb("send", array("id" => $id, "page" => $page, "row" => $row, "col" => $col), "", false, true);

		$r = get_instance(CL_RATE);
		$this->vars(array(
			"avg_rating" => $r->get_rating_for_object($pd['img']['id'] ? $pd['img']['id'] : $pd['tn']['id'], RATING_AVERAGE),
			"print_link" => "javascript:window.print()",
			"email_link" => $email_link,
			"image" => image::make_img_tag(image::check_url($pd['img']['id'] ? $pd['img']['url'] : $pd['tn']['url'])),
			"views" => (int)$this->db_fetch_field("SELECT hits FROM hits WHERE oid = '".$pd['img']['id']."'", "hits"),
			"RATING_SCALE_ITEM" => $rsi,
			"name" => $ob->name(),
			"prev_image_url" => $this->mk_my_orb("show_image", array("id" => $id, "page" => $p_page, "row" => $p_row , "col" => $p_col)),
			"next_image_url" => $this->mk_my_orb("show_image", array("id" => $id, "page" => $n_page, "row" => $n_row , "col" => $n_col)),
			"imgcaption" => $pd['caption']
		));

		if ($rsi != "")
		{
			$this->vars(array(
				"HAS_RATING_SCALE" => $this->parse("HAS_RATING_SCALE")
			));
		}

		if ($n_page <= $ob->meta('num_pages'))
		{
			$this->vars(array(
				"HAS_NEXT_IMAGE" => $this->parse("HAS_NEXT_IMAGE")
			));
		}

		if ($p_page > 0)
		{
			$this->vars(array(
				"HAS_PREV_IMAGE" => $this->parse("HAS_PREV_IMAGE")
			));
		}
		die($this->parse());
	}

	function _get_hit_counts($pd)
	{
		$imgids = new aw_array();
		if (is_array($pd))
		{
			foreach($pd as $r_id => $r_dat)
			{
				foreach($r_dat as $c_id => $c_dat)
				{
					if ($c_dat["img"]["id"])
					{
						$imgids->set($c_dat["img"]["id"]);
					}
				}
			}
		}
		$hits = array();
		$this->db_query("SELECT oid,hits FROM hits WHERE oid IN(".$imgids->to_sql().")");
		while ($row = $this->db_next())
		{
			$hits[$row['oid']] = $row['hits'];
		}
		return $hits;
	}

	function _get_xydata($arr)
	{
		extract($arr);
		if (!is_oid($conf_o->id()))
		{
			// return original dimensions
			return array(
				"width" => $i_width,
				"height" => $i_height,
				"tn_width" => $i_width,
				"tn_height" => $i_height,
				"tn_is_subimage" => false,
			);
		}

		if ($i_width > $i_height)
		{
			$tn_width = $conf_o->meta("h_tn_width");
			$tn_height = $conf_o->meta("h_tn_height");
			$width = $conf_o->meta("h_width");
			$height = $conf_o->meta("h_height");
			$is_subimage = $conf_o->meta("h_tn_subimage") == 1;
			if ($is_subimage)
			{
				$si_top = $conf_o->meta("h_tn_subimage_top");
				$si_left = $conf_o->meta("h_tn_subimage_left");
				$si_width = $conf_o->meta("h_tn_subimage_width");
				$si_height = $conf_o->meta("h_tn_subimage_height");
			}
		}
		else
		{
			$tn_width = $conf_o->meta("v_tn_width");
			$tn_height = $conf_o->meta("v_tn_height");
			$width = $conf_o->meta("v_width");
			$height = $conf_o->meta("v_height");
			$is_subimage = $conf_o->meta("v_tn_subimage") == 1;
			if ($is_subimage)
			{
				$si_top = $conf_o->meta("v_tn_subimage_top");
				$si_left = $conf_o->meta("v_tn_subimage_left");
				$si_width = $conf_o->meta("v_tn_subimage_width");
				$si_height = $conf_o->meta("v_tn_subimage_height");
			}
		}

		// check if the user only specified one of width/height and then calc the other one
		if ($width && !$height)
		{
			if ($width{strlen($width)-1} == "%")
			{
				$height = $width;
			}
			else
			{
				$ratio = $width / $i_width;
				$height = (int)($i_height * $ratio);
			}
		}

		if (!$width && $height)
		{
			if ($height{strlen($height)-1} == "%")
			{
				$width = $height;
			}
			else
			{
				$ratio = $height / $i_height;
				$width = (int)($i_width * $ratio);
			}
		}


		if ($tn_width && !$tn_height)
		{
			if ($tn_width{strlen($tn_width)-1} == "%")
			{
				$tn_height = $tn_width;
			}
			else
			{
				$ratio = $tn_width / $i_width;
				$tn_height = (int)($i_height * $ratio);
			}
		}

		if (!$tn_width && $tn_height)
		{
			if ($tn_height{strlen($tn_height)-1} == "%")
			{
				$tn_width = $tn_height;
			}
			else
			{
				$tn_ratio = $tn_height / $i_height;
				$tn_width = (int)($i_width * $ratio);
			}
		}

		if ($si_width && !$si_height)
		{
			if ($si_width{strlen($si_width)-1} == "%")
			{
				$si_height = $si_width;
			}
			else
			{
				$ratio = $si_width / $i_width;
				$si_height = (int)($i_height * $ratio);
			}
		}

		if (!$si_width && $si_height)
		{
			if ($si_height{strlen($si_height)-1} == "%")
			{
				$si_width = $si_height;
			}
			else
			{
				$ratio = $si_height / $i_height;
				$si_width = (int)($i_width * $ratio);
			}
		}


		if (!$width)
		{
			$width = $i_width;
		}
		if (!$height)
		{
			$height = $i_height;
		}

		// now convert to pixels
		if ($width{strlen($width)-1} == "%")
		{
			$width = (int)($i_width * (((int)substr($width, 0, -1))/100));
		}
		if ($height{strlen($height)-1} == "%")
		{
			$height = (int)($i_height * (((int)substr($height, 0, -1))/100));
		}

		if ($tn_width{strlen($tn_width)-1} == "%")
		{
			$tn_width = (int)($width * (((int)substr($tn_width, 0, -1))/100));
		}
		if ($tn_height{strlen($tn_height)-1} == "%")
		{
			$tn_height = (int)($height * (((int)substr($tn_height, 0, -1))/100));
		}

		if ($si_width{strlen($si_width)-1} == "%")
		{
			$si_width = (int)($width * (((int)substr($si_width, 0, -1))/100));
		}
		if ($si_height{strlen($si_height)-1} == "%")
		{
			$si_height = (int)($height * (((int)substr($si_height, 0, -1))/100));
		}

		return array(
			"width" => $width,
			"height" => $height,
			"tn_width" => $tn_width,
			"tn_height" => $tn_height,
			"tn_is_subimage" => $is_subimage,
			"tn_si_top" => $si_top,
			"tn_si_left" => $si_left,
			"tn_si_width" => $si_width,
			"tn_si_height" => $si_height
		);
	}

	function _do_logo($img, $conf_o, $p = "")
	{
		// first, get the damn image
		if (!$conf_o->meta($p."logo_img"))
		{
			return $img;
		}

		$iinst = get_instance(CL_IMAGE);
		$_img = $iinst->get_image_by_id($conf_o->meta($p."logo_img"));

		$l_img = get_instance("core/converters/image_convert");
		$l_img->load_from_file($iinst->_mk_fn($_img["file"]));

		list($img_x, $img_y) = $img->size();
		list($l_img_x, $l_img_y) = $l_img->size();

		// now, find where to put the damn thing
		if ($conf_o->meta($p."logo_corner") == CORNER_LEFT_TOP)
		{
			$img->merge(array(
				"source" => $l_img,
				"x" => $conf_o->meta($p."logo_dist_x"),
				"y" => $conf_o->meta($p."logo_dist_y"),
				"pct" => ($conf_o->meta($p."logo_transparency") ? $conf_o->meta($p."logo_transparency") : 99)
			));
		}
		else
		if ($conf_o->meta($p."logo_corner") == CORNER_LEFT_BOTTOM)
		{
			$img->merge(array(
				"source" => $l_img,
				"x" => $conf_o->meta($p."logo_dist_x"),
				"y" => $img_y - ($conf_o->meta($p."logo_dist_y") + $l_img_y),
				"pct" => ($conf_o->meta($p."logo_transparency") ? $conf_o->meta($p."logo_transparency") : 99)
			));
		}
		else
		if ($conf_o->meta($p."logo_corner") == CORNER_RIGHT_TOP)
		{
			$img->merge(array(
				"source" => $l_img,
				"x" => $img_x - ($conf_o->meta($p."logo_dist_x") + $l_img_x),
				"y" => $conf_o->meta($p."logo_dist_y"),
				"pct" => ($conf_o->meta($p."logo_transparency") ? $conf_o->meta($p."logo_transparency") : 99)
			));
		}
		else
		if ($conf_o->meta($p."logo_corner") == CORNER_RIGHT_BOTTOM)
		{
			$img->merge(array(
				"source" => $l_img,
				"x" => $img_x - ($conf_o->meta($p."logo_dist_x") + $l_img_x),
				"y" => $img_y - ($conf_o->meta($p."logo_dist_y") + $l_img_y),
				"pct" => ($conf_o->meta($p."logo_transparency") ? $conf_o->meta($p."logo_transparency") : 99)
			));
		}
		return $img;
	}

	function _get_prev_img($ob, $page, $row, $col)
	{
		$p_col = $col - 1;
		$tmp = $ob->meta("page_data");
		if ($p_col < 0)
		{
			$p_row = $row - 1;
			$p_col = $tmp[$page]['layout']['cols']-1;
		}
		else
		{
			$p_row = $row;
		}

		if ($p_row < 0)
		{
			$p_page = $page-1;
			$p_row = $tmp[$p_page]['layout']['rows']-1;
			$p_col = $tmp[$p_page]['layout']['cols']-1;
		}
		else
		{
			$p_page = $page;
		}

		return array($p_page, $p_row, $p_col);
	}

	function _get_next_img($ob, $page, $row, $col)
	{
		$n_col = $col + 1;
		$tmp = $ob->meta("page_data");
		if ($n_col >= $tmp[$page]['layout']['cols'])
		{
			$n_row = $row+1;
			$n_col = 0;
		}
		else
		{
			$n_row = $row;
		}

		if ($n_row >= $tmp[$page]['layout']['rows'])
		{
			$n_page = $page+1;
			$n_row = 0;
			$n_col = 0;
		}
		else
		{
			$n_page = $page;
		}

		return array($n_page, $n_row, $n_col);
	}

	/**

		@attrib name=send params=name nologin="1" default="0"

		@param id required
		@param page required
		@param col required
		@param row required

		@returns


		@comment

	**/
	function send($arr)
	{
		extract($arr);
		$this->read_any_template("send.tpl");

		$this->vars(array(
			"reforb" => $this->mk_reforb("submit_send", $arr)
		));
		return $this->parse();
	}

	/**

		@attrib name=submit_send params=name nologin="1" default="0"

		@param id required
		@param page required
		@param col required
		@param row required

		@returns


		@comment

	**/
	function submit_send($arr)
	{
		extract($arr);

		$gal = obj($id);
		$cid = $this->_get_conf_for_folder($gal->parent());

		$headers = NULL;
		if ($cid)
		{
			$co = obj($cid);
			if ($co->prop("send_from_addr") != "")
			{
				$headers = "From: ".$co->prop("send_from_addr")."\n";
			}
		}

		$link = $this->mk_my_orb("show_image", array("id" => $id, "page" => $page, "row" => $row, "col" => $col));

		send_mail($to, $subject, $message."\n\n".$link."\n\n", $headers);

		return $link;
	}

	function _page_has_images($ob, $page)
	{
		$tmp = $ob->meta("page_data");
		$rows = $tmp[$page]['layout']["rows"];
		$cols = $tmp[$page]['layout']["cols"];
		$pd = $tmp[$page]['content'];

		for ($row = 0; $row < $rows; $row++)
		{
			for ($col = 0; $col < $cols; $col++)
			{
				if ($pd[$row][$col]['img']['id'])
				{
					return true;
				}
			}
		}

		return false;
	}

	function _get_default_layout($obj)
	{
		if (!is_object($obj))
		{
			$parent = $obj["parent"];
		}
		else
		{
			$parent = $obj->parent();
		}
		$conf = get_instance(CL_GALLERY_CONF);
		return $conf->get_default_layout($this->_get_conf_for_folder($parent));
	}

	function get_contained_objects($arr)
	{
		extract($arr);
		$ret = array();

		$ob = obj($oid);

		$_pd = new aw_array($ob->meta('page_data'));
		foreach($_pd->get() as $page => $pd)
		{
			$_ct = new aw_array($pd['content']);
			foreach($_ct->get() as $row => $cd)
			{
				$_cd = new aw_array($cd);
				foreach($_cd->get() as $col => $cell)
				{
					if ($cell["img"]["id"])
					{
						$ret[$cell["img"]["id"]] = $cell["img"]["id"];
					}
				}
			}
		}
		return $ret;
	}

	/** this is a content generator - right now used by the calendar only

		@attrib name=show_aliased params=name nologin="1" caption="Näita galeriid" default="0"

		@param id required type=int

		@returns


		@comment
		shows a gallery attached to an object - or event
		it should probably be improved a bit, since right now it uses a tambov
		constant to decide WHICH attached gallery to show

	**/
	function show_aliased($args = array())
	{
		extract($args);
		if (empty($args["id"]))
		{
			return false;
		};
		$retval = "";
		$tgt = $this->_get_target_gallery($args["id"]);
		if ($tgt)
		{
			$retval = $this->show(array(
				"oid" => $tgt,
			));
		};
		return $retval;
	}

	/** This is another content generator, except that this one shows the image

		@attrib name=show_top_image params=name nologin="1" caption="Parim pilt" default="0"

		@param id required type=int

		@returns


		@comment
		with the highest rate from the attached gallery, and it has the same
		flaw as the above show_aliased, since it uses a seemingly random attached
		gallery

	**/
	function show_top_image($args = array())
	{
		extract($args);
		if (empty($args["id"]))
		{
			return false;
		};
		$tgt = $this->_get_target_gallery($args["id"]);
		if (empty($tgt))
		{
			return false;
		};
		$glv = obj($tgt);

		$this->img = get_instance(CL_IMAGE);
		$c_oid = $glv->id();
		$retval = "";
		if (empty($c_oid))
		{
			return "";
		};

		$c_objs = $this->get_contained_objects(array(
			"oid" => $c_oid,
		));

		if (is_array($c_objs) && (sizeof($c_objs) > 0))
		{
			// try to locate the highest rated image
			$where = " objects.oid IN ( " . join(",",array_keys($c_objs)) . ")";
			$sql = "
				SELECT
					objects.oid as oid,
					rating as val ,
					objects.name as name,
					objects.class_id as class_id,
					hits.hits as hits,
					images.file as img_file
				FROM
					objects
				LEFT JOIN ratings ON ratings.oid = objects.oid
				LEFT JOIN g_img_rel ON objects.oid = g_img_rel.img_id
				LEFT JOIN hits ON hits.oid = objects.oid
				LEFT JOIN images ON images.id = objects.oid
				WHERE
					$where
				GROUP BY
					objects.oid
				ORDER BY rating DESC
				LIMIT 1";
			$this->db_query($sql);
			$row = $this->db_next();
			$pd = $this->_find_image($row["oid"],$glv);
			if ($row)
			{
				$retval = image::make_img_tag(image::check_url($pd['tn']['url']));
			};
		}
		return $retval;
	}

	////
	// !Used by the above content generators to decide which attached gallery to use
	function _get_target_gallery($id)
	{
		$q = "SELECT target FROM aliases WHERE source = '$id' AND type = " . $this->clid;
		return $this->db_fetch_field($q,"target");
	}
}
