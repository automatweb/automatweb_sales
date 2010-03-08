<?php
// $Header: /home/cvs/automatweb_dev/classes/contentmgmt/gallery/mini_gallery.aw,v 1.50 2008/10/23 08:42:08 kristo Exp $
// mini_gallery.aw - Minigalerii 
/*

@classinfo syslog_type=ST_MINI_GALLERY relationmgr=yes no_status=1 maintainer=kristo

@default table=objects

@default group=data
	
	@property name type=textbox rel=1 trans=1 table=objects
	@caption Nimi

	@property folder type=relpicker multiple=1 reltype=RELTYPE_IMG_FOLDER field=meta method=serialize 
	@caption Piltide kataloog

	@property cols type=textbox size=5 field=meta method=serialize default=2
	@caption Tulpi

	@property rows type=textbox size=5 field=meta method=serialize
	@caption Ridu

@default group=settings

	@property comments type=checkbox field=flags method=bitmask ch_value=1
	@caption Pildid kommenteeritavad
	
	@property style type=relpicker reltype=RELTYPE_STYLE field=meta method=serialize
	@caption Piltide stiil

	@property sorter type=select field=meta method=serialize
	@caption Piltide j&auml;rjestamine

	@property addheight type=textbox size=5 field=meta method=serialize default=0
	@caption Pildi aknale lisatav k&otilde;rgus

	@property addwidth type=textbox size=5 field=meta method=serialize default=0
	@caption Pildi aknale lisatav laius

@default group=import

	@property zip_file type=fileupload store=no
	@caption Uploadi ZIP fail

	@property refresh type=checkbox ch_value=1 store=no
	@caption V&auml;rskenda

@default group=manage_img

	@property mg_tb type=toolbar no_caption=1 store=no

	@property mg_table type=table no_caption=1 store=no

@default group=manage_fld

	@property mg_fld_table type=table no_caption=1 store=no

@groupinfo import caption="Import"
@groupinfo manage caption="Halda" submit=no
	@groupinfo manage_img caption="Halda pilte" submit=no parent=manage
	@groupinfo manage_fld caption="Halda kaustu" parent=manage

	@groupinfo data caption=Andmed parent=general
	@groupinfo settings caption=Seaded parent=general

@reltype IMG_FOLDER value=1 clid=CL_MENU
@caption Piltide kataloog

@reltype STYLE value=2 clid=CL_CSS
@caption Stiil

*/

class mini_gallery extends class_base
{
	function mini_gallery()
	{
		$this->init(array(
			"tpldir" => "contentmgmt/gallery/mini_gallery",
			"clid" => CL_MINI_GALLERY
		));
		$this->sorts = array(
			"objects.name" => t("Nimi"),
			"objects.jrk" => t("J&auml;rjekord"), 
			"objects.created" => t("Loomise kuup&auml;ev vanemad enne"), 
			"objects.modified" => t("Muutmise kuup&auml;ev vanemad enne"),
			"images.aw_date_taken" => t("Pildistamise kuup&auml;ev vanemad enne"),
			"objects.created desc" => t("Loomise kuup&auml;ev uuemad enne"), 
			"objects.modified desc" => t("Muutmise kuup&auml;ev uuemad enne"),
			"images.aw_date_taken desc" => t("Pildistamise kuup&auml;ev uuemad enne"),
		);
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "mg_fld_table":
				$this->_get_mg_fld_table($arr);
				break;
				
			case "sorter":
				$prop["options"] = array("" => "") + $this->sorts;
				break;

			case "mg_tb":
				$this->_mg_tb($arr);
				break;

			case "mg_table":
				$this->_mg_table($arr);
				break;
		};
		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "zip_file":
				if (is_uploaded_file($_FILES["zip_file"]["tmp_name"]))
				{
					$this->_do_zip_import($arr["obj_inst"], $_FILES["zip_file"]["tmp_name"]);
				}
				break;

			case "mg_fld_table":
				$this->_set_mg_fld_table($arr);
				break;
		}
		return $retval;
	}	

	function parse_alias($arr)
	{
		$res = $this->show(array("id" => $arr["alias"]["target"]));
		if (isset($arr["tpls"]["mini_gallery_inplace"]))
		{
			$res = array(
				"replacement" => localparse($arr["tpls"]["mini_gallery_inplace"], array("content" => $res)),
				"inplace" => "mini_gallery_inplace"
			);
		}
		return $res;
	}

	function __sort_imgs($a, $b)
	{
		if ($a->parent() == $b->parent())
		{
			// hard case, sort by the sort order defined in gallery
			$sby = $this->ob->prop("sorter");

			switch($sby)
			{
				case "objects.name":
					return strcmp($a->name(), $b->name());

				case "objects.jrk":
					return $a->ord() - $b->ord();

				case "objects.created":
					return $a->created() - $b->created();

				case "objects.modified":
					return $a->modified() - $b->modified();

				case "images.aw_date_taken":
					return $a->prop("date_taken") - $b->prop("date_taken");

				case "objects.created desc":
					return $b->created() - $a->created();

				case "objects.modified desc":
					return $b->modified() - $a->modified();
				
				case "images.aw_date_taken desc":
					return $b->prop("date_taken") - $a->prop("date_taken");

				default:
					$rv = $a->ord() - $b->ord();
					if ($rv == 0)
					{
						return $a->created() - $b->created();
					}
					return $rv;
			}
		}
		// easy case, sort by either the folder order set or folder's order
		if (isset($this->fld_orders[$a->parent()]))
		{
			$a_val = $this->fld_orders[$a->parent()];
		}
		else
		{
			$tmp = obj($a->parent());
			$a_val = $tmp->ord();
		}

		if (isset($this->fld_orders[$b->parent()]))
		{
			$b_val = $this->fld_orders[$b->parent()];
		}
		else
		{
			$tmp = obj($b->parent());
			$b_val = $tmp->ord();
		}

		return $a_val - $b_val;
	}

	function _pic_list($ob)
	{

		$sby = "objects.jrk,objects.created desc";
		if ($ob->prop("sorter") != "")
		{
			$sby = $ob->prop("sorter");
		}


		$images = new object_list(array(
			"class_id" => CL_IMAGE,
			"parent" => $ob->prop("folder"),
			"sort_by" => "objects.parent,".$sby,
			"lang_id" => array(),
			"site_id" => array(),
			new object_list_filter(array(
				"non_filter_classes" => CL_IMAGE
			))
		));
		return $images;
	}

	function show($arr)
	{
		$ob = $this->ob = new object($arr["id"]);
		$this->read_template("show.tpl");

		lc_site_load("mini_gallery", &$this);

		$s_id = $ob->prop("style");
		$use_style = null;
		if(is_oid($s_id) && $this->can("view", $s_id))
		{
			$style_i = get_instance(CL_STYLE);
			active_page_data::add_site_css_style($s_id);
			$use_style = $style_i->get_style_name($s_id);
		}

		$images = $this->_pic_list($ob);
		if (!$images->count())
		{
			return;
		}

		$this->fld_orders = $ob->meta("fld_order");
		$images->sort_by_cb(array(&$this, "__sort_imgs"));

		$img_c = $images->count();
		if ($ob->prop("cols") == 0)
		{
			$rows = $img_c;
			$cols = 1;
		}
		else
		if ($ob->prop("rows"))
		{
			$rows = $ob->prop("rows");
			$cols = $ob->prop("cols");
		}
		else
		{
			$rows = ceil($img_c / $ob->prop("cols"));
			$cols = $ob->prop("cols");
		}
		$img = $images->begin(); 

		if ($ob->prop("rows"))
		{
			$this->_do_pageselector($ob, $img_c, $rows, $cols);
		}

		if (!empty($_GET["mg_pg"]))
		{
			for($i = 0; $i < ($_GET["mg_pg"] * $rows * $cols); $i++)
			{
				$img = $images->next();
				$img_c--;
			}
		}

		$tplar = array();
		$f_tplar = array();
		
		$tpls = array(
			"IMAGE" => "image",
			"IMAGE_LINKED" => "image_linked",
			"IMAGE_HAS_BIG" => "image_has_big",
			"IMAGE_BIG_LINKED" => "image_big_linked"
		);
		foreach($tpls as $uc_name => $lc_name)
		{
			$f_uc_name = "FIRST_".$uc_name;
				
			if ($this->is_template($uc_name))
			{
				$imtpl = $this->get_template_string($uc_name);
				$tplar[$lc_name] = $imtpl;
				$f_tplar[$lc_name] = $imtpl;
			}
			if ($this->is_template($f_uc_name))
			{
				$imtpl = $this->get_template_string($f_uc_name);
				$f_tplar[$lc_name] = $imtpl;
			}
		}

		$numbr = 1;
		$str = "";
		if ($img && is_array($ob->prop("folder")) && count($ob->prop("folder")))
		{
			$fo = obj($img->parent());
			$this->vars(array(
				"folder_name" => $fo->trans_get_val("name"),
				"col_count" => $ob->prop("cols")?$ob->prop("cols"):100,
			));
			$str .= $this->parse("FOLDER_CHANGE");
		}
		$cur_folder = $img->parent();
		$ii = get_instance(CL_IMAGE);

		$imgc = 0;
		for ($r = 0; $r < $rows; $r++)
		{
			$l = "";
			for($c = 0; $c < $cols; $c++)
			{
				if ($imgc < $img_c)
				{
					$args = array(
						"alias" => array(
							"target" => $img->id()
						),
						"tpls" => $numbr == 1 ? $f_tplar : $tplar,
						"use_style" => $use_style,
						"force_comments" => $ob->prop("comments"),
						"link_prefix" => empty($arr['link_prefix']) ? "" : $arr['link_prefix'],
						"add_show_link_arr" => array(
							"minigal" => $ob->id(),
						),
						"add_vars" => array(
							"count" => $numbr
						)
					);
					$addheight = $ob->prop("addheight");
					if($addheight)
					{
						$args['addheight'] = $addheight;
					}
					$addwidth = $ob->prop("addwidth");
					if($addwidth)
					{
						$args['addwidth'] = $addwidth;
					}
					$tmp = $ii->parse_alias($args);
					$this->vars(array(
						"imgcontent" => $tmp["replacement"],
					));
					$img = $images->next();
					$imgc ++;
				}
				else
				{
					$this->vars(array(
						"imgcontent" => ""
					));
				}
				$l .= $this->parse("COL");

				if ($img && $cur_folder != $img->parent())
				{
					$r++;
					$rows++;
					$c = -1;
					$this->vars(array(
						"COL" => $l
					));
					$str .= $this->parse("ROW");
					$l = "";
					$fo = obj($img->parent());
					$this->vars(array(
						"folder_name" => $fo->trans_get_val("name")
					));
					$str .= $this->parse("FOLDER_CHANGE");
					$cur_folder = $img->parent();
				}
				$numbr++;
			}

			$this->vars(array(
				"COL" => $l
			));
			$str .= $this->parse("ROW");
		}

		$this->vars(array(
			"ROW" => $str
		));

		return $this->parse();
	}

	function _do_zip_import($o, $zip)
	{
		$fld = $o->prop("folder");
		if (is_array($fld))
		{
			$fld = reset($fld);
		}
		if (!$this->can("add", $fld))
		{
			die(t("Valitud piltide kataloogi ei ole &otilde;igusi objekte lisada!"));
		}

		if (extension_loaded("zip"))
		{
			$folder = aw_ini_get("server.tmpdir")."/".gen_uniq_id();
			mkdir($folder, 0777);
			$tn = $folder;
			$zip = zip_open($zip);
			while ($zip_entry = zip_read($zip)) 
			{
				zip_entry_open($zip, $zip_entry, "r");
				$fn = $folder."/".basename(zip_entry_name($zip_entry));
				$fc = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
				$this->put_file(array(
					"file" => $fn,
					"content" => $fc
				));
				$inf = @getimagesize($fn);
				if(!in_array($inf[2], array(IMAGETYPE_JPEG, IMGETYPE_PNG, IMAGETYPE_GIF)))
				{
					@unlink($fn);
					continue;
				}
				$files[] = basename($fn);
			}
		}
		else
		{
			$zf = escapeshellarg($zip);
			$zip = aw_ini_get("server.unzip_path");
			$tn = aw_ini_get("server.tmpdir")."/".gen_uniq_id();
			mkdir($tn,0777);
			$cmd = $zip." -d $tn $zf";
			$op = shell_exec($cmd);


			$files = array();
			if ($dir = @opendir($tn)) 
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
		}

		$imgi = get_instance(CL_IMAGE);
		$fi = get_instance(CL_FILE);
		foreach($files as $file)
		{
			echo "leidsin faili $file <br>\n";
			flush();
			$fp = $tn."/".$file;

			if ($_POST["refresh"] == 1)
			{
				// try to find image with same name in gallery
				$ol = new object_list(array(
					"class_id" => CL_IMAGE,
					"name" => $file,
					"parent" => $fld,
					"lang_id" => array(),
					"site_id" => array()
				));
				if ($ol->count())
				{
					$img = $ol->begin();
				}
				else
				{
					$img = obj();
				}
			}
			else
			{
				$img = obj();
			}
			$img->set_class_id(CL_IMAGE);
			$img->set_parent($fld);
			$img->set_status(STAT_ACTIVE);
			$img->set_name($file);
			
			if (function_exists("exif_read_data"))
			{
				$dat = exif_read_data($fp);
				$dt = $dat["DateTime"];
				$dt = strptime($dt, "%Y:%m:%d %H:%M:%S");
				$img->set_prop("date_taken", $dt);
			}

			$fl = $fi->_put_fs(array(
				"type" => substr($file, strrpos($file, ".")),
				"content" => $this->get_file(array("file" => $fp))
			));
			$img->set_prop("file", $fl);

			$img->save();

			$imgi->do_apply_gal_conf($img);

			@unlink($fp);
		}
		echo "valmis<br>\n";
		flush();
		@rmdir($tn);
	}

	function _do_pageselector($ob, $img_c, $rows, $cols)
	{
		$rows = (int)$rows;
		if ((int)$rows * $cols >= $img_c)
		{
			return;
		}

		$mg_pg = isset($_GET["mg_pg"]) ? $_GET["mg_pg"] : 0;

		$prev_page = $next_page = "";
		$num_pgs = $img_c / ($rows * $cols);
		for($i = 0; $i < $num_pgs; $i++)
		{
			$this->vars(array(
				"page_link" => aw_url_change_var("mg_pg", $i),
				"page_nr" => $i+1
			));

			if ($mg_pg == $i)
			{
				$pgs[] = $this->parse("PAGE_SEL");
			}
			else
			{
				$pgs[] = $this->parse("PAGE");
			}

			if ($i+1 == $mg_pg)
			{
				$prev_page = $this->parse("PREV_PAGE");
			}
			if ($i-1 == $mg_pg)
			{
				$next_page = $this->parse("NEXT_PAGE");
			}
		}

		$this->vars(array(
			"PAGE" => join($this->parse("PAGE_SEPRATOR"), $pgs),
			"PAGE_SEPARATOR" => "",
			"PAGE_SEL" => "",
			"PREV_PAGE" => $prev_page,
			"NEXT_PAGE" => $next_page
		));

		$this->vars(array(
			"PAGESELECTOR" => $this->parse("PAGESELECTOR")
		));
	}

	function callback_post_save($arr)
	{
		if ($arr["request"]["new"])
		{
			// create folders and set props
			$folder = obj();
			$folder->set_parent($arr["obj_inst"]->parent());
			$folder->set_name(t("Galerii ").$arr["obj_inst"]->name().t(" pildid"));
			$folder->set_class_id(CL_MENU);
			$folder->save();
			$arr["obj_inst"]->set_prop("folder", $folder->id());
			$arr["obj_inst"]->save();
		}
	}

	function _mg_tb($arr)
	{
		$tb =& $arr["prop"]["vcl_inst"];
		$pt = $arr["obj_inst"]->prop("folder");
		if (is_array($pt))
		{
			$pt = reset($pt);
		}

		$tb->add_button(array(
			'name' => 'new',
			'img' => 'new.gif',
			'tooltip' => t('Lisa uus pilt'),
			'url' => html::get_new_url(CL_IMAGE, $pt, array("return_url" => get_ru()))
		));
		$tb->add_button(array(
			'name' => 'save',
			'img' => 'save.gif',
			'tooltip' => t('Salvesta pildid'),
			'action' => 'save_image_list',
		));
		$tb->add_button(array(
			'name' => 'del',
			'img' => 'delete.gif',
			'tooltip' => t('Kustuta valitud pildid'),
			'action' => 'delete_images',
			'confirm' => t("Kas oled kindel et soovid valitud pildid kustudada?")
		));
	}

	function _init_mg_table(&$t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"align" => "center",
		));

		$t->define_field(array(
			"name" => "ord",
			"caption" => t("J&auml;rjekord"),
			"align" => "center",
		));

		$t->define_field(array(
			"name" => "modifiedby",
			"caption" => t("Muutja"),
			"align" => "center",
		));

		$t->define_field(array(
			"name" => "modified",
			"caption" => t("Muudetud"),
			"align" => "center",
			"type" => "time",
			"numeric" => 1,
			"format" => "d.m.Y H:i"
		));

		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid"
		));
	}

	function _mg_table($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_mg_table($t);

		$images = new object_list(array(
			"class_id" => CL_IMAGE,
			"parent" => $arr["obj_inst"]->prop("folder"),
			"sort_by" => "objects.jrk",
			"lang_id" => array(),
			"site_id" => array()
		));
		foreach($images->arr() as $im)
		{
			$t->define_data(array(
				"name" => html::obj_change_url($im),
				"ord" => html::textbox(array(
					"name" => "ord[".$im->id()."]",
					"value" => $im->ord(),
					"size" => 5
				)),
				"modifiedby" => $im->modifiedby(),
				"modified" => $im->modified(),
				"oid" => $im->id(),
				"h_ord" => $im->ord()
			));
		}
		$t->set_sortable(false);
	}

	/**
		@attrib name=save_image_list
	**/
	function save_image_list($arr)
	{
		$o = obj($arr["id"]);
		$images = new object_list(array(
			"class_id" => CL_IMAGE,
			"parent" => $o->prop("folder"),
			"sort_by" => "objects.jrk",
			"lang_id" => array(),
			"site_id" => array()
		));
		foreach($images->arr() as $im)
		{
			if ($arr["ord"][$im->id()] != $im->ord())
			{
				$im->set_ord($arr["ord"][$im->id()]);
				$im->save();
			}
		}
		return $arr["post_ru"];
	}

	/**
		@attrib name=delete_images
	**/
	function delete_images($arr)
	{
		object_list::iterate_list($arr["sel"], "delete");
		return $arr["post_ru"];
	}

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function _init_mg_flt_t(&$t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Kausta nimi"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "ord",
			"caption" => t("J&auml;rjekord"),
			"align" => "center"
		));
	}

	function _get_mg_fld_table($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_mg_flt_t($t);

		$d = $arr["obj_inst"]->meta("fld_order");
		foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_IMG_FOLDER")) as $c)
		{
			$t->define_data(array(
				"name" => html::obj_change_url($c->prop("to")),
				"ord" => html::textbox(array(
					"name" => "f[".$c->prop("to")."]",
					"size" => 5,
					"value" => $d[$c->prop("to")]
				))
			));
		}
	}

	function _set_mg_fld_table($arr)
	{
		$arr["obj_inst"]->set_meta("fld_order", $arr["request"]["f"]);
	}
}
?>
