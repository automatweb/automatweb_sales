<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/contentmgmt/video.aw,v 1.11 2009/04/27 14:19:29 hannes Exp $
// video.aw - Video 
/*

@classinfo syslog_type=ST_VIDEO relationmgr=yes no_comment=1 no_status=1 maintainer=kristo


@default table=objects
@default field=meta
@default method=serialize


@default group=general

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


*/

class video extends class_base
{
	const AW_CLID = 833;

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

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{

		};
		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "transl":
				$this->trans_save($arr, $this->trans_props);
				break;
		}
		return $retval;
	}	

	function parse_alias($arr)
	{
		return $this->show(array("id" => $arr["alias"]["target"]));
	}

	function show($arr)
	{
		$ob = new object($arr["id"]);
		if ($ob->prop("show_right_away"))
		{
			$this->read_template("show_right_away.tpl");
			$this->Vars(array(
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

		$im = get_instance(CL_IMAGE);

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
	
	function request_execute($o)
	{
		$this->read_template("autoplay.tpl");
		$this->vars($o->properties());
		die($this->parse());
	}

	function callback_mod_tab($arr)
	{
		if ($arr["id"] == "transl" && aw_ini_get("user_interface.content_trans") != 1)
		{
			return false;
		}
		else
		if ($arr["id"] == "trans" && aw_ini_get("user_interface.content_trans") == 1)
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
?>
