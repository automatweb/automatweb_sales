<?php

namespace automatweb;
// flv_file.aw - FLV fail
/*

@classinfo syslog_type=ST_FLV_FILE relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=hannes

@default table=objects
@default group=general

	@property name type=text
	@caption Nimi
	
	@property file type=hidden field=meta method=serialize
	
	@property fileupload type=fileupload store=no
	@caption Fail
	
	@property preview_img type=relpicker reltype=RELTYPE_PREVIEWIMG store=connect
	@caption Eelvaate pilt

	@property width type=textbox field=meta method=serialize
	@caption Laius (px)

	@property height type=textbox field=meta method=serialize
	@caption K&otilde;rgus (px)
	
@groupinfo meta caption=Meta
@default group=meta
	
	@property meta_duration type=text
	@caption Kestvus
	
	@property meta_width type=text
	@caption Laius
	
	@property meta_height type=text
	@caption K&ouml;rgus
	
	@property meta_video_data_rate type=text
	@caption video_data_rate
	
	@property meta_audio_data_rate type=text
	@caption audio_data_rate
	
	@property meta_frame_rate type=text
	@caption frame_rate
	
	@property meta_creation_date type=text
	@caption Loomisaeg	
	
	@reltype PREVIEWIMG value=1 clid=CL_IMAGE
	@caption Eelvaate pilt
	
*/

class flv_file extends class_base
{
	const AW_CLID = 1382;

	function flv_file()
	{
		$this->init(array(
			"tpldir" => "applications/flv_player",
			"clid" => CL_FLV_FILE
		));
	}
	
	
	function get_property($arr)
	{
		$o = &$arr["obj_inst"];
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		
		// here we read meta like fps, creation time etc ..
		// all meta data is red from files header
		// all i have to know is the location, length and that 
		// it's in binary is in double and written in reverse
		$f   = fopen($o->prop("file"), 'r');
		fseek($f, 27, SEEK_CUR);
		$s_on_meta_data = fread($f, 10);
		
		switch($prop["name"])
		{
			case "meta_duration":
				if ($s_on_meta_data == "onMetaData")
				{
					fseek($f, 16, SEEK_CUR);
					$duration = fread($f, 8);
					$duration = strrev($duration);
					list(,$duration) = unpack("d", $duration);
					$prop["value"] = $duration. t(" sekundit");
				}
			break;
			case "meta_width":
				if ($s_on_meta_data == "onMetaData")
				{
					fseek($f, 32, SEEK_CUR);
					$width = fread($f, 8);
					$width = strrev($width);
					list(,$width) = unpack("d", $width);
					$prop["value"] = $width."px";
				}
			break;
			case "meta_height":
				if ($s_on_meta_data == "onMetaData")
				{
					fseek($f, 49, SEEK_CUR);
					$height = fread($f, 8);
					$height = strrev($height);
					list(,$height) = unpack("d", $height);
					$prop["value"] = $height."px";
				}
			break;
			case "meta_video_data_rate":
				if ($s_on_meta_data == "onMetaData")
				{
					fseek($f, 73, SEEK_CUR);
					$video_data_rate = fread($f, 8);
					$video_data_rate = strrev($video_data_rate);
					list(,$video_data_rate) = unpack("d", $video_data_rate);
					$prop["value"] = $video_data_rate;
				}
			break;
			case "meta_audio_data_rate":
				if ($s_on_meta_data == "onMetaData")
				{
					fseek($f, 140, SEEK_CUR);
					$audio_rate = fread($f, 8);
					$audio_rate = strrev($audio_rate);
					list(,$audio_rate) = unpack("d", $audio_rate);
					$prop["value"] = $audio_rate;
				}
			break;
			case "meta_frame_rate":
				if ($s_on_meta_data == "onMetaData")
				{
					fseek($f, 93, SEEK_CUR);
					$frame_rate = fread($f, 8);
					$frame_rate = strrev($frame_rate);
					list(,$frame_rate) = unpack("d", $frame_rate);
					$prop["value"] = $frame_rate."fps";
				}
			break;
			case "meta_creation_date":
				if ($s_on_meta_data == "onMetaData")
				{
					fseek($f, 232, SEEK_CUR);
					$creation_date = fread($f, 24);
					$tmp = explode ( " ", $creation_date) ;
					$day = $tmp[2];
					$month = $tmp[1];
					$year = $tmp[4];
					$hour = substr($tmp[3], 0, 2);
					$minute = substr($tmp[3], 3, 2);
					$second = substr($tmp[3], 6, 2);
					$prop["value"] = "$day $month $year $hour:$minute:$second";
				}
			break;
		}
		
		fclose($fr);
		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
			case "fileupload":
				$src_file = "";
				$oldfile = $arr["obj_inst"]->prop($prop["file"]);
				
				if (!empty($prop["value"]["tmp_name"]))
				{
					// this happens if for example releditor is used
					$src_file = $prop["value"]["tmp_name"];
					$ftype = $prop["value"]["type"];
				};
				
				if (is_uploaded_file($_FILES[$prop["name"]]["tmp_name"]))
				{
					// this happens if file is uploaded from the flv_file class directly
					$src_file = $_FILES[$prop["name"]]["tmp_name"];
					$ftype = $_FILES[$prop["name"]]["type"];
				};
				
				// if a file was found, then move it to wherever it should be located
				if (is_uploaded_file($src_file))
				{
					$_fi = new file();
					$final_name = $_fi->generate_file_path(array(
						"type" => "video/flv",
					));
					
					move_uploaded_file($src_file, $final_name);
					
					// get rid of the old file
					if (file_exists($oldfile))
					{
						// also, we should check if any OTHER file objects point to this file.
						// if they do, then don't delete the old one. this is sort-of like reference counting:P
						// because copy/paste on images creates a new object that points to the same file. 
						$ol = new object_list(array(
							"class_id" => CL_FLV_FILE,
							"lang_id" => array(),
							"site_id" => array(),
							"file" => "%".basename($oldfile)."%",
							"oid" => new obj_predicate_not($arr["obj_inst"]->id())
						));
						if (!$ol->count())
						{
							@unlink($oldfile);
						}
					}
					
					if ($arr["obj_inst"]->name() == "")
					{
						if ($prop["value"]["name"] != "")
						{
							$arr["obj_inst"]->set_prop("name", $prop["value"]["name"]);
						}
						else
						{
							$arr["obj_inst"]->set_prop("name", $_FILES[$prop["name"]]["name"]);
						}
					}
					$arr["obj_inst"]->set_prop("file", $final_name);
				}
			break;
		}
		
		return $retval;
	}
	
	function parse_alias($args = array())
	{
		extract($args);
		$o = obj($alias["to"]);
		
		if (!empty($alias["aliaslink"]))
		{
			return $this->show(array(
				"id" => $o->id(),
				"in_popup" => true,
			));
		}
		else
		{
			return $this->show(array(
				"id" => $o->id(),
			));
		}
	}
	
	/**
		@attrib name=view params=name nologin="1" default="0"
		@param id required
		@returns
		@comment
	**/
	function view($arr)
	{
		classload("core");
		if (is_array($arr))
		{
			extract($arr);
		}
		// allow only integer id-s
		$id = (int)$id;
		
		$o = new object($id);
		$s_content = core::get_file(array ("file" => $o->prop("file")));
		$pi = pathinfo($o->prop("file"));
		$mimeregistry = get_instance("core/aw_mime_types");
		$tmp = $mimeregistry->type_for_ext($pi["extension"]);
		if ($tmp != "")
		{
			$s_type = $tmp;
		}
		header("Accept-Ranges: bytes");
		header("Content-Length: ".strlen($s_content));
		header("Content-type: ".$s_type);
		header("Cache-control: public");
		die($s_content);
	}

	function callback_mod_reforb(&$arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function show($arr)
	{
		$o = obj($arr["id"]);
		
		$s_url = $this->mk_my_orb("flv_file", array(
			"id" => $o->id(),
			"action" => "view",
		));
		
		if (aw_ini_get("image.imgbaseurl"))
		{
			$file_name = end(explode("/", $o->prop("file")));
			$s_url = aw_ini_get("baseurl") . aw_ini_get("image.imgbaseurl") . "/" . substr($file_name, 0, 1) . "/" . $file_name;
			$o_img = obj($o->prop("preview_img"));
			$image_file_name = end(explode("/", $o_img->prop("file")));
			$s_img_url = aw_ini_get("baseurl") . aw_ini_get("image.imgbaseurl") . "/" . substr($image_file_name, 0, 1) . "/" . $image_file_name;
		}
		else
		{
			$s_url = str_replace("&", "/", $s_url)."/video.flv";
			$s_url = str_replace("?", "orb.aw/", $s_url);
			$o_img = obj($o->prop("preview_img"));
			$image_file_name = end(explode("/", $o_img->prop("file")));
			$s_img_url = image::get_url($o_img->prop("file"));
		}
		
		if($arr["in_popup"])
		{
			$popup_width_extra = 0;
			$popup_height_extra = 0;
			$this->read_template("popup.tpl");
			$vars = array(
				// id has to be unique. if one puts more than 1 same vidoe on same page then it does not work in opera
				// works in firefox though. don't know about other browsers.
				"id" => "aw_flvplayer_".$o->id(),
				"name" => $o->prop("name"),
				"file" => $s_url,
				"width" => $o->prop("width")?$o->prop("width"):300,
				"height" => $o->prop("height")?$o->prop("height"):250,
				"popup_width" => $o->prop("width")?$o->prop("width")+$popup_width_extra:300+$popup_width_extra,
				"popup_height" => $o->prop("height")?$o->prop("height")+$popup_height_extra:250+$popup_height_extra,
				"image_url" => $s_img_url,
			);
		}
		else
		{
			$this->read_template("show.tpl");
			$vars = array(
				// id has to be unique. if one puts more than 1 same vidoe on same page then it does not work in opera
				// works in firefox though. don't know about other browsers.
				"id" => "aw_flvplayer_".$o->id(),
				"name" => $o->prop("name"),
				"file" => $s_url,
				"width" => $o->prop("width")?$o->prop("width"):300,
				"height" => $o->prop("height")?$o->prop("height"):250,
				"image_url" => $s_img_url,
			);
		}
		
		$this->vars($vars);
		return $this->parse();
	}
}

?>
