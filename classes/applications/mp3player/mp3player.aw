<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/mp3player/mp3player.aw,v 1.9 2008/10/29 15:57:34 hannes Exp $
// mp3player.aw - MP3 pleier 
/*

@classinfo syslog_type=ST_MP3PLAYER relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=hannes

@default table=objects
@default group=general

@property control type=text field=meta method=serialize
@caption Kontroll

@property search type=textbox field=meta method=serialize
@caption Otsing

@property all_songs_table type=table store=no no_caption=1

@groupinfo settings caption=Seaded
@default group=settings

@property name type=textbox
@caption Nimi


*/

class mp3player extends class_base
{
	const AW_CLID = 1353;

	function mp3player()
	{
		$this->init(array(
			"tpldir" => "applications/mp3player",
			"clid" => CL_MP3PLAYER
		));
	}
	
	function callback_post_save($arr)
	{
		if ($arr["new"])
		{
			$o = & $arr["obj_inst"];
			$o->set_prop("name", aw_global_get("uid") . " " . t("mp3 player"));
			$o->save();
		}
	}
	
	function get_cover_url($s_keywords)
	{
		$s_google_search = "http://images.google.com/images?svnum=100&um=1&hl=et&lr=&rls=en&q=".urlencode($s_keywords)	;
		$s_result = file_get_contents($s_google_search);
		if (preg_match ( "/dyn.Img\(\".*\".*\".*\".*\".*\".*\"(.*)\".*\)/imsU", $s_result, $a_matches ))
		{
			return $a_matches[1]; // first image
		}
	}
	
	function get_play_url($id,$name)
	{
		$retval = str_replace("automatweb/","",$this->mk_my_orb("play", array("id" => $id),"mp3player", false,true,"/"));
		return $retval;
	}
	
	function _get_control($arr)
	{
		$o = & $arr["obj_inst"];
		
		if(strlen($o->name())>0)
		{
			$s_link = html::href(array(
				"url" => "JavaScript: void(0)",
				"caption" => t("m&auml;ngi"),
				"onclick" => 'myRef = window.open("'.$this->get_play_url($o->id(), $s_filename).'","","left="+((screen.width/2)-(250/2))+",top="+screen.height/5+",width=250,height=450,toolbar=0,resizable=0,location=0,directories=0,status=0,menubar=0,scrollbars=0")',
			));
			
			$arr["prop"]["value"] = $s_link;
		}
	}
	
	function _get_all_songs_table($arr)
	{
		$table =& $arr["prop"]["vcl_inst"];
		$o_player = & $arr["obj_inst"];
		
		$s_search = trim($o_player->prop("search"));
		$a_list = $this->get_playlist($s_search);
		
		$table->define_field(array(
			"name" => "title",
			"caption" => t("Loo nimi"),
			"sortable" => 1,
			"align" => "left",
		));
		
		$table->define_field(array(
			"name" => "time",
			"caption" => t("Kestvus"),
			"sortable" => 1,
			"align" => "left",
		));
		
		$table->define_field(array(
			"name" => "artist",
			"caption" => t("Artist"),
			"sortable" => 1,
			"align" => "left"
		));
		
		$table->define_field(array(
			"name" => "album",
			"caption" => t("Album"),
			"sortable" => 1,
			"align" => "left"
		));
		
		$table->define_field(array(
			"name" => "genre",
			"caption" => t("&#142;anr"),
			"sortable" => 1,
			"align" => "left"
		));
		
		$table->define_field(array(
			"name" => "rate",
			"caption" => t("Rate"),
			"sortable" => 1,
			"align" => "left"
		));
		
		$table->define_field(array(
			"name" => "play_count",
			"caption" => t("M&auml;ngitud"),
			"sortable" => 1,
			"align" => "left"
		));
		
		$table->define_field(array(
			"name" => "play",
			"caption" => t("M&auml;ngi"),
			"sortable" => 1,
			"align" => "left"
		));
		
		$a_list_keys = array_keys($a_list);
		for ($i=0;$i<count($a_list);$i++)
        {
			$s_key = $a_list_keys[$i];
			classload("applications/mp3player/mp3");
			$s_link = mp3::get_play_url($a_list[$s_key]["id"],$a_list[$s_key]["file_name"]);
		
			$table->define_data(array(
				"title" => html::href(array(
					"url" => "JavaScript: void(0)",
					"caption" => $a_list[$s_key]["title"],
					"onclick" => 'myRef = window.open("'.$s_link.'","","left="+((screen.width/2)-(350/2))+",top="+screen.height/5+",width=350,height=150,toolbar=0,resizable=0,location=0,directories=0,status=0,menubar=0,scrollbars=0")',
				)),
				"time" => $a_list[$s_key]["time"],
				 "artist" =>  $a_list[$s_key]["artist"],
				 "album" =>  $a_list[$s_key]["album"],
 				 "genre" =>  $a_list[$s_key]["genre"],
				 "play_count" => $a_list[$s_key]["play_count"],
				"play" => "",
				"time" => $a_list[$s_key]["time"],
				"play" => html::href(array(
					"url" => $this->mk_my_orb("change", array("id" => $a_list[$s_key]["id"]),"mp3", false,true),
					"caption" => "info",
				)),
				
			));
        }
		
		$table->define_pageselector(array(
				"type"=>"lb",
				"records_per_page"=>100,
				"position"=>"both",
		));
	}
	
		/** Playis mp3's that are listed in aw player
		
		@attrib name=update_playlist params=name nologin="1" default="0" is_public="1"
		
		@param mp3player_oid required
		
		@returns
		
		
		@comment

	**/
	function update_playlist($arr)
	{
		extract($_POST);
		
		$o = new object($arr["mp3player_oid"]);
		$o->set_prop("search", $str);
		$o->save();
		
		die();
	}
	
	/** Playis mp3's that are listed in aw player
		
		@attrib name=play params=name nologin="1" default="0" is_public="1"
		
		@param id required
		
		@returns
		
		
		@comment

	**/
	function play($arr)
	{
		classload("applications/mp3player/mp3");
		$o = new object($arr["id"]);
		
		$this->read_template("mp3player.tpl");
			$this->vars(array(
			"mp3player_oid" => $o->id(),
			"file_name" => mp3::normalize_name($o->name()),
			"search_string" => $o->prop("search"),
			"version" => $this->get_version(),
		));
		
		echo $this->parse();
		
		die();
	}
	
	function get_version()
	{
		$fn = aw_ini_get("basedir")."/classes/applications/mp3player/mp3player.aw";
		$fh = fopen($fn, 'r');
		$s_data = fread($fh, 100);
		fclose($fh);
		preg_match ( "/Header:.*aw,v\s(.*)\s/imsU", $s_data, $matches);
		return $matches[1];
	}
	
	function get_playlist($s_search)
	{
		$a_search_fields = array("title", "album", "genre", "artist");
		$a_list = array();
		if (strlen($s_search)>0)
		{
			$a_search_keys = "%$s_search%";
			
			for ($i=0;$i<count($a_search_fields);$i++)
			{
				$ol = new object_list(array(
					"class_id" => CL_MP3,
					"lang_id" => array(),
					$a_search_fields[$i] => $a_search_keys,
				));
				for ($o = $ol->begin(); !$ol->end(); $o =& $ol->next())
				{
					$a_list[$o->prop("md5")] = array(
							"id" => $o->id(),
							"title" => $o->prop("title"),
							"album" => $o->prop("album"),
							"artist" => $o->prop("artist"),
							"file_name" => $o->name(),
							"time" => $o->prop("mpeg_info_playtime_string"),
							"genre" =>  $o->prop("genre"),
							"play_count" => $o->prop("play_count"),
					);
				}
			}
		}
		else
		{
			$ol = new object_list(array(
					"class_id" => CL_MP3,
					"lang_id" => array(),
			));
			for ($o = $ol->begin(); !$ol->end(); $o =& $ol->next())
			{
				$a_list[$o->prop("md5")] = array(
						"id" => $o->id(),
						"title" => $o->prop("title"),
						"album" => $o->prop("album"),
						"artist" => $o->prop("artist"),
						"file_name" => $o->name(),
						"time" => $o->prop("mpeg_info_playtime_string"),
						"genre" =>  $o->prop("genre"),
						"play_count" => $o->prop("play_count"),
				);
			}
		}
		
		return $a_list;
	}
	
	/** Loob playlisti
		
		@attrib name=playlist params=name nologin="1" default="0" is_public="1"
		
		@param id required
		
		@returns
		
		@comment
	**/
	function playlist($arr)
	{
		$arr["id"] = reset(explode("?", $arr["id"]));
		classload("applications/mp3player/mp3");
		$o = new object($arr["id"]);
		
		$s_search = trim($o->prop("search"));
		$a_list = $this->get_playlist($s_search);
		
		$this->read_template("playlist.tpl");
		$this->submerge=1;

		$tmp='';
		$a_list_keys = array_keys($a_list);
		for ($i=0;$i<count($a_list_keys);$i++)
        {
			$s_key = $a_list_keys[$i];
			$this->vars(array(
				"id"=> $a_list[$s_key]["id"],
				"title"=> $a_list[$s_key]["title"],
				"artist" => $a_list[$s_key]["artist"],
				"album" => $a_list[$s_key]["album"],
				"url_mp3" => mp3::get_download_url($a_list[$s_key]["id"],"fail.mp3"),
				"url_info" => mp3::get_lasering_url($a_list[$s_key]["album"]),
				"url_image" => $this->get_cover_url($a_list[$s_key]["artist"]." ".$a_list[$s_key]["album"]),
			));
			$tmp.= $this->parse("TRACK");
		}
		
		$this->vars(array(
				"TRACK" => $tmp,
		));
		
		die(utf8_encode($this->parse()));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			//-- get_property --//
		};
		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			//-- set_property --//
		}
		return $retval;
	}	

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	////////////////////////////////////
	// the next functions are optional - delete them if not needed
	////////////////////////////////////

	/** this will get called whenever this object needs to get shown in the website, via alias in document **/
	function show($arr)
	{
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");
		$this->vars(array(
			"name" => $ob->prop("name"),
		));
		return $this->parse();
	}

//-- methods --//
}
?>
