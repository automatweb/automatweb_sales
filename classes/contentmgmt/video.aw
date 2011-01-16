<?php

// video.aw - Video
/*

@classinfo relationmgr=yes no_comment=1 no_status=1 prop_cb=1


@default table=objects
@default field=meta
@default method=serialize


@default group=general
	@property video_file type=fileuploader
	@caption Fail

	@property image type=releditor reltype=RELTYPE_IMAGE use_form=emb rel_id=first
	@caption Pilt

	@property caption type=textarea rows=3 cols=20
	@caption Allkiri

	@property author type=textbox
	@caption Autor

	@property origin type=textbox
	@caption Allikas

	@property origin_url type=textbox
	@caption Allika URL

	@property date type=date_select
	@caption Kuup&auml;ev

	@property show_right_away type=checkbox ch_value=1
	@caption N&auml;ita kohe

	@property width type=textbox size=5
	@caption Laius

	@property height type=textbox size=5
	@caption K&otilde;rgus

	@property src_rp type=textbox
	@caption URL (RealPlayer)

	@property src_wm type=textbox
	@caption URL (Windows Media)

	@property video_galleries type=relmanager reltype=RELTYPE_GALLERY
	@comment Videogaleriid mille alla see video kuulub
	@caption Galeriid

@default group=trans

	@property trans type=translator group=trans props=name
	@caption T&otilde;ge

@default group=transl

	@property transl type=callback callback=callback_get_transl
	@caption T&otilde;lgi


@groupinfo trans caption="T&otilde;lkimine"
@groupinfo transl caption=T&otilde;lgi

@reltype IMAGE value=1 clid=CL_IMAGE
@caption Video pilt

@reltype GALLERY value=2 clid=CL_VIDEO_GALLERY
@caption Videogalerii


*/

class video extends class_base
{
	function video()
	{
		$this->init(array(
			"tpldir" => "contentmgmt/video",
			"clid" => CL_VIDEO
		));

		$this->trans_props = array(
			"name", "caption", "origin"
		);
	}

	function _set_transl(&$arr)
	{
		$retval = PROP_OK;
		$this->trans_save($arr, $this->trans_props);
		return $retval;
	}

	function _set_video_file(&$arr)
	{
		$retval = PROP_IGNORE;
		$prop = $arr["prop"];

		if ($prop["value"])
		{
			$fileuploader = new fileuploader();
			$uploaded_file = $fileuploader->tmp_dir.$prop["value"];

			$dir = aw_ini_get("site_basedir")."/files/videos/";
			if (!is_dir($dir))
			{
				mkdir($dir, 0744);
			}

			if ($arr["obj_inst"]->prop("video_file") and $arr["obj_inst"]->prop("video_file") !== $prop["value"] and file_exists($dir.$arr["obj_inst"]->prop("video_file")))
			{ // remove old
				unlink($dir.$arr["obj_inst"]->prop("video_file"));
			}

			if (file_exists($fileuploader->tmp_dir.$prop["value"]))
			{
				copy($uploaded_file, $dir.$prop["value"]);
				unlink($uploaded_file);
				$retval = PROP_OK;
			}
		}

		return $retval;
	}

	function parse_alias($arr = array())
	{
		return $this->show(array("id" => $arr["alias"]["target"]));
	}

	function get_url($o)
	{
		$file = $o->prop("file");
		if ($file)
		{
			$url = str_replace("/automatweb/", "/", $this->mk_my_orb("show", array("fastcall" => 1, "file" => basename($file)), "video", false, true, "/"));
		}
		else
		{
			$url = "";
		}

		return $url;
	}

	function show($arr)
	{
		if (!empty($arr["fastcall"]) and !empty($arr["file"]))
		{
			$this->show_file($arr);
		}

		if (!$this->can("view", $arr["id"]))
		{
			return "";
		}

		$ob = new object($arr["id"]);

		if ($ob->prop("file"))
		{
			$im = new image();
			$poster_image_url = "";
			$imc = reset($ob->connections_from(array("type" => "RELTYPE_IMAGE")));
			if ($imc)
			{
				$imid = $imc->prop("to");
				$poster_image_url = $im->get_url_by_id($imid);
			}

			$video_file_url = $this->get_url($ob);

			$this->read_template("strobeplayer.tpl");
			$this->vars(array(
				"video_file_url" => $video_file_url,
				"skin_file_url" => "js/strobeplayer.xml",
				"poster" => $poster_image_url ? "&poster={$poster_image_url}" : ""
			));
			return $this->parse();
		}
		elseif ($ob->prop("show_right_away"))
		{
			$this->read_template("show_right_away.tpl");
			$this->vars(array(
				"mpeg_url" => $ob->prop("src_wm"),
				"mpeg_fn" => basename($ob->prop("src_wm")),
				"width" => $ob->prop("width"),
				"height" => $ob->prop("height")
			));
			return $this->parse();
		}
		else
		{
			$this->read_template("show.tpl");
		}

		$im = new image();

		$image = "";
		$imc = reset($ob->connections_from(array("type" => "RELTYPE_IMAGE")));
		if ($imc)
		{
			$imid = $imc->prop("to");
			$image = $im->make_img_tag($im->get_url_by_id($imid));
		}

		$this->vars(array(
			"name" => $this->trans_get_val($ob, "name"),
			"image" => $image,
			"caption" => $this->trans_get_val($ob, "caption"),
		));

		$dat = array(
			array("src_rp", "capt_rp", "HAS_RP"),
			array("src_wm", "capt_wm", "HAS_WM"),
		);

		foreach($dat as $format)
		{
			if ($ob->prop($format[0]))
			{
				$this->vars(array(
					"vid_url" => $this->trans_get_val($ob, $format[0]),
					//"vid_url_capt" => $ob->prop($format[1]),
					"vid_url_capt" => str_replace("&", "&amp;", $this->trans_get_val($ob, "name")),
				));
				$this->vars(array(
					$format[2] => $this->parse($format[2])
				));
			}
		}

		return $this->parse();
	}

	function show_file($arr)
	{
		$file = realpath(aw_ini_get("site_basedir") . "/files/videos/" . basename($arr["file"]));

		if (is_readable($file))
		{
			header("Content-type: video/x-flv");
			header("Content-length: ".filesize($file));
			@readfile($file);
			exit;
		}
		else
		{
			header("HTTP/1.x 404 Not Found");
			exit;
		}
	}

	function request_execute($o)
	{
		$this->read_template("autoplay.tpl");
		$this->vars($o->properties());
		die($this->parse());
	}

	function callback_mod_tab($arr)
	{
		if ($arr["id"] === "transl" && aw_ini_get("user_interface.content_trans") != 1)
		{
			return false;
		}
		else
		if ($arr["id"] === "trans" && aw_ini_get("user_interface.content_trans") == 1)
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
