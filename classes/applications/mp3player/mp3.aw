<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/mp3player/mp3.aw,v 1.5 2008/05/16 09:28:15 hannes Exp $
// mp3.aw - MP3 
/*

@classinfo syslog_type=ST_MP3 no_comment=1 no_status=1 prop_cb=1 maintainer=hannes

@tableinfo mp3 index=aw_oid  master_index=brother_of master_table=objects

@default table=objects
@default group=general

	@property name type=text
	@caption Nimi

	@property file type=hidden store=yes table=mp3 field=file
	
	@property md5 type=hidden store=yes table=mp3 field=md5
	
	@property play_count type=text table=mp3 field=play_count
	@caption M&auml;ngitud

	@property fileupload type=fileupload store=no
	@caption Fail
	
@groupinfo info caption=Info
@default group=info
	
	@groupinfo info_id3 caption=ID3 parent=info
	@default group=info_id3
	
		@property track type=text table=mp3 field=track
		@caption Mitmes lugu
		
		@property title type=text table=mp3 field=title
		@caption Pealkiri
		
		@property artist type=text table=mp3 field=artist
		@caption Artist
		
		@property album type=text table=mp3 field=album
		@caption Album
		
		@property year type=text table=mp3 field=year
		@caption Aasta
		
		@property genre type=text table=mp3 field=genre
		@caption &#142;anr
		 
		@property comment type=text table=mp3 field=comment
		@caption Kommentaar
		
		@property composer type=text table=mp3 field=composer
		@caption Helilooja
		
		@property orig_artist type=text table=mp3 field=orig_artist
		@caption Algup&auml;rane artist
		
		@property copyright type=text table=mp3 field=copyright
		@caption Koopia&otilde;igus
		
		@property url type=text table=mp3 field=url
		@caption URL
		
		@property encoded_by type=text table=mp3 field=encoded_by
		@caption Kodeeritud
		
	@groupinfo info_mpeg caption="MPEG info" parent=info
	@default group=info_mpeg
	
		@property mpeg_info_filesize type=text table=mp3 field=mpeg_info_filesize
		@caption Suurus
		
		@property mpeg_info_channels type=text table=mp3 field=mpeg_info_channels
		@caption Kanaleid
		
		@property mpeg_info_sample_rate type=text table=mp3 field=mpeg_info_sample_rate
		@caption sample_rate
		
		@property mpeg_info_bitrate type=text table=mp3 field=mpeg_info_bitrate
		@caption bitrate
		
		@property mpeg_info_channelmode type=text table=mp3 field=mpeg_info_channelmode
		@caption channelmode
		
		@property mpeg_info_bitrate_mode type=text table=mp3 field=mpeg_info_bitrate_mode
		@caption bitrate_mode
		
		@property mpeg_info_codec type=text table=mp3 field=mpeg_info_codec
		@caption codec
		
		@property mpeg_info_encoder type=text table=mp3 field=mpeg_info_encoder
		@caption encoder
		
		@property mpeg_info_lossless type=text table=mp3 field=mpeg_info_lossless
		@caption Kadudeta
		
		@property mpeg_info_encoder_options type=text table=mp3 field=mpeg_info_encoder_options
		@caption encoder_options
		
		@property mpeg_info_compression_ratio type=text table=mp3 field=mpeg_info_compression_ratio
		@caption Pakkimise j&otilde;ud
		
		@property mpeg_info_playtime_seconds type=text table=mp3 field=mpeg_info_playtime_seconds
		@caption Kestus sekundites
		
		@property mpeg_info_playtime_string type=text table=mp3 field=mpeg_info_playtime_string
		@caption Kestus
		
		@property mpeg_info_original type=text table=mp3 field=mpeg_info_original
		@caption Originaal?
		
		@property mpeg_info_emphasis type=text table=mp3 field=mpeg_info_emphasis
		@caption emphasis
		
		@property mpeg_info_copyright type=text table=mp3 field=mpeg_info_copyright
		@caption copyright
*/

class mp3 extends class_base implements admin_if_plugin
{
	const AW_CLID = 1352;

	function mp3()
	{
		$this->init(array(
			"tpldir" => "applications/awplayer/mp3",
			"clid" => CL_MP3
		));
	}
	
	function _get_id3v2_artist($arr)
	{
		$prop = & $arr["prop"];
		
		if (strlen($prop["value"])>0)
		{
				$s_link_lasering = html::href(array(
				"url" => "JavaScript: void(0)",
				"caption" => t("lasering.ee"),
				"onclick" => 'myRef = window.open("'.$this->get_lasering_url($prop["value"]).'","Amazon","left=0,top=0,width="+screen.width+",height="+screen.height+",toolbar=1,resizable=1,location=1,directories=0,status=1,menubar=1,scrollbars=1")',
			));
		
			$s_link_amazon = html::href(array(
				"url" => "JavaScript: void(0)",
				"caption" => t("amazon.com"),
				"onclick" => 'myRef = window.open("'.$this->get_amazon_url($prop["value"]).'","Amazon","left=0,top=0,width="+screen.width+",height="+screen.height+",toolbar=1,resizable=1,location=1,directories=0,status=1,menubar=1,scrollbars=1")',
			));
		
			$s_link_wiki = html::href(array(
				"url" => "JavaScript: void(0)",
				"caption" => t("wikipedia"),
				"onclick" => 'myRef = window.open("'.$this->get_wiki_url($prop["value"]).'","Wikipedia","left=0,top=0,width="+screen.width+",height="+screen.height+",toolbar=1,resizable=1,location=1,directories=0,status=1,menubar=1,scrollbars=1")',
			));
			
			$prop["value"] = $prop["value"] . " (" . $s_link_lasering . " | " . $s_link_amazon . " | " . $s_link_wiki . ")";
		}
	}
	
	function _get_id3v2_album($arr)
	{
		$prop = & $arr["prop"];
		
		if (strlen($prop["value"])>0)
		{
			$s_link_lasering = html::href(array(
				"url" => "JavaScript: void(0)",
				"caption" => t("lasering.ee"),
				"onclick" => 'myRef = window.open("'.$this->get_lasering_url($prop["value"]).'","Amazon","left=0,top=0,width="+screen.width+",height="+screen.height+",toolbar=1,resizable=1,location=1,directories=0,status=1,menubar=1,scrollbars=1")',
			));
		
			$s_link_amazon = html::href(array(
				"url" => "JavaScript: void(0)",
				"caption" => t("amazon.com"),
				"onclick" => 'myRef = window.open("'.$this->get_amazon_url($prop["value"]).'","Amazon","left=0,top=0,width="+screen.width+",height="+screen.height+",toolbar=1,resizable=1,location=1,directories=0,status=1,menubar=1,scrollbars=1")',
			));
			
			$s_link_wiki = html::href(array(
				"url" => "JavaScript: void(0)",
				"caption" => t("wikipedia"),
				"onclick" => 'myRef = window.open("'.$this->get_wiki_url($prop["value"]).'","Wikipedia","left=0,top=0,width="+screen.width+",height="+screen.height+",toolbar=1,resizable=1,location=1,directories=0,status=1,menubar=1,scrollbars=1")',
			));
			
			$prop["value"] = $prop["value"] . " (" . $s_link_lasering . " | " . $s_link_amazon . " | " . $s_link_wiki . ")";
		}
	}
	
	/** N&auml;itab mp3'e. DUH. 
		
		@attrib name=log_play_statistics params=name nologin="1" default="0" is_public="1"
		
		@returns
		
		@comment

	**/
	function log_play_statistics($arr)
	{
		extract($_POST, EXTR_PREFIX_SAME, "post_");
		
		if ($state == "start")
		{
			$o = new object($id);
			$o->set_prop("play_count", $o->prop("play_count")+1);
			$o->save();
		}
		
		die();
	}
	
	function get_lasering_url($s_keywords)
	{
		$s_url = "http://www.lasering.ee/index.php?make=search_item&PHPSESSID=&search=&db%5Btext%5D=".$s_keywords."&x=0&y=0";
		return $s_url;
	}

	
	function get_wiki_url($s_keywords)
	{
		$s_url = "http://en.wikipedia.org/wiki/Special:Search?search=$s_keywords&go=Go";
		return $s_url;
	}
	
	function get_amazon_url($s_keywords)
	{
		$s_url = "http://www.amazon.com/s/ref=nb_ss_m/104-9120387-4085522?url=search-alias%3Dpopular&field-keywords=".$s_keywords;
		return $s_url;
	}
	
	function get_download_url($id,$name)	
	{
		$retval = str_replace("automatweb/","",$this->mk_my_orb("download", array("id" => $id),"mp3", false,true,"/"))."/".str_replace("/","_",$name);
//		$retval = $this->mk_my_orb("preview", array("id" => $id),"file", false,true);
		return $retval;
	}
	
	function get_play_url($id,$name)	
	{
		$retval = str_replace("automatweb/","",$this->mk_my_orb("play", array("id" => $id),"mp3", false,true,"/"))."/".str_replace("/","_",$name);
//		$retval = $this->mk_my_orb("preview", array("id" => $id),"file", false,true);
		return $retval;
	}
	
	/** changes some estonians characters to ascii chars like &ouml; to 8 and spaces to _
	
	@param s_name required
	
	@errors
	        none
	
	@returns
	        string
	
	**/
	function normalize_name($s_name)
	{
		$s_name = strtolower($s_name);
		
		for($i = 0; $i < strlen($s_name); $i++)
		{
			if ( ord($s_name{$i}) == 184 ||ord($s_name{$i}) == 180 ) // zcaron or Zcaron
			{
				$tmp .= "z";
			}
			else if (ord($s_name{$i}) == 168 ||ord($s_name{$i}) == 166) // scaron or Zcaron
			{
				$tmp .= "s";
			}
			else if (ord($s_name{$i}) == 233 || ord($s_name{$i}) == 201) //e with acute
			{
				$tmp .= "e";
			}
			else if (ord($s_name{$i}) == 252 || ord($s_name{$i}) == 220) // &uuml; or &Uuml;
			{
				$tmp .= "u";
			}
			else if (ord($s_name{$i}) == 245 || ord($s_name{$i}) == 213 
				|| ord($s_name{$i}) == 246 || ord($s_name{$i}) == 214 ) //&otilde; or &Otilde; or &ouml; or &Ouml;
			{
				$tmp .= "o";
			}
			else if (ord($s_name{$i}) == 228 || ord($s_name{$i}) == 196) //&auml; or &Auml;
			{
				$tmp .= "a";
			}
			else if (ord($s_name{$i}) == 32)// space
			{
				$tmp .= "_";
			}
			else
			{
				$tmp .= $s_name[$i];
			}
		}
		
		$s_name = str_replace ("_-_", "-", $tmp);
		return $s_name;
	}
	
	function _get_name($arr)
	{
		$o = & $arr["obj_inst"];
		
		if(strlen($o->name())>0)
		{
			$s_filename = $this->normalize_name($o->name()).".mp3";
			
			$s_link = html::href(array(
				"url" => "JavaScript: void(0)",
				"caption" => $arr["prop"]["value"],
				"onclick" => 'myRef = window.open("'.$this->get_play_url($o->id(), $s_filename).'","AW MP3 M&auml;ngija","left="+((screen.width/2)-(350/2))+",top="+screen.height/5+",width=350,height=150,toolbar=0,resizable=0,location=0,directories=0,status=0,menubar=0,scrollbars=0")',
			));
			
			$s_download = html::href(array(
				"url" => $this->get_download_url($o->id(), $s_filename),
				"caption" => t("lae alla"),
			));

			
			$arr["prop"]["value"] = $s_link . " (" . $s_download . ")";
		}
	}
	
		/** N&auml;itab mp3'e. DUH. 
		
		@attrib name=play params=name nologin="1" default="0" is_public="1"
		
		@param id required
		
		@returns
		
		
		@comment

	**/
	function play($arr)
	{
		$o = new object($arr["id"]);
		
		$s_html = '
		<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
		   "http://www.w3.org/TR/html4/loose.dtd">
		
		<html>
		<head>
			<title>AW MP3 M&auml;ngija</title>
			<script type="text/javascript" src="'.aw_ini_get("baseurl").'/automatweb/js/jw_mp3_player/swfobject.js"></script>
			<style>
			body, p {margin: 0;padding: 0;}
			</style>
		</head>
		<body>
		
		<p id="player2"><a href="http://www.macromedia.com/go/getflashplayer">Get the Flash Player</a> to see this player.</p>
		<script type="text/javascript">
			var s2 = new SWFObject("'.aw_ini_get("baseurl").'/automatweb/js/jw_mp3_player/mp3player.swf", "single", "350", "150", "7");
			//s2.addVariable("file","http://hannes.dev.struktuur.ee/automatweb/orb.aw?class=mp3&action=preview&id=404771");
			s2.addVariable("file","http://hannes.dev.struktuur.ee/orb.aw/class=mp3/action=download/id='.$arr["id"].'/'.$this->normalize_name($o->name()).'.mp3");
			s2.addVariable("title","'.$o->name().'");
			s2.addVariable("autostart", true);
			s2.addVariable("autoscroll", true);
			s2.addVariable("shownavigation", true);
			//s2.addVariable("displaywidth", 150);
			s2.addVariable("showeq", true);
			s2.addVariable("backcolor","0x00000");
			s2.addVariable("frontcolor","0xEECCDD");
			s2.addVariable("lightcolor","0xCC0066");
			s2.addVariable("displayheight","100");
			s2.addVariable("width","350");
			s2.addVariable("height","150");
			s2.write("player2");
		</script>
		
		
		
		</body>
		</html>
		';
	echo $s_html;
	die();
	}
	
	/** N&auml;itab mp3'e. DUH. 
		
		@attrib name=download params=name nologin="1" default="0" is_public="1"
		
		@param id required
		
		@returns
		
		
		@comment

	**/
	function download($arr)
	{
		if (is_array($arr))
		{
			extract($arr);
		}
		// allow only integer id-s
		$id = (int)$id;
		error::view_check($id);

		
		// if the user has access and imgbaseurl is set, then we can redirect the user to that
		// and let apache do the serving the file, that can take quite some time, if the file is large
		$fo = obj($id);
		$s_file_path = $fo->prop("file");
		$fc = $this->get_file(array("file" => $s_file_path));
		$pi = pathinfo( $s_file_path);
		$mimeregistry = get_instance("core/aw_mime_types");
		$tmp = $mimeregistry->type_for_ext($pi["extension"]);
		if ($tmp != "")
		{
			$s_ext = $tmp;
		}
		header("Accept-Ranges: bytes");
		header("Content-Length: ".strlen($fc));
		header("Content-type: ".$s_ext);
		header("Cache-control: public");
		header("Content-Disposition: inline; filename=\"".basename(get_ru())."\"");
		//header("Content-Length: ".strlen($fc["content"]));
		//header("Pragma: no-cache");
		die($fc);
	}
	
	
	function _get_mpeg_info_sample_rate($arr)
	{
		$arr["prop"]["value"] = $arr["prop"]["value"] . "Hz	";
	}
	
	function _get_mpeg_info_filesize($arr)
	{
		$i_size = $arr["prop"]["value"];
		$_size_h = $i_size / 1024 / 1024;
		$_size_h = round ($_size_h, 1) ;
		$arr["prop"]["value"] = $arr["prop"]["value"] . " baiti (".$_size_h."MB)";
	}
	
	function get_name($a_id3, $s_orig_filename)
	{
		if (isset($a_id3["title"]) && isset($a_id3["artist"]))
		{
			return trim( $a_id3["artist"] ) . " - " . trim($a_id3["title"]);
		}
		else
		{
			return $s_orig_filename;
		}
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
	
	function new_mp3($i_parent, $src_file)
	{
			// if a file was found, then move it to wherever it should be located
			if (is_file($src_file))
			{
				$o = new object(array(
					"parent" => $i_parent,
					"class_id" => CL_MP3
				));
				$o->set_class_id(CL_MP3);
				
				$_fi = new file();
				$final_name = $_fi->generate_file_path(array(
					"type" => "audio/mp3",
				));
				
				copy($src_file, $final_name);
				
				// get idv3v1 tags to object
				{
					$a_file_id3 = array();
					$a_file_id3 = id3_get_tag($final_name);
					$o->set_prop("md5", md5_file($final_name) );
					$a_file_id3["filesize"] = filesize($final_name);
					$a_file_id3 = array_merge($a_file_id3, mp3::mp3info($final_name));
					
					if (isset($a_file_id3["track"]))
					{
						$o->set_prop("track", $a_file_id3["track"]);
					}
					
					if (isset($a_file_id3["title"]))
					{
						$o->set_prop("title", $a_file_id3["title"]);
					}
					
					if (isset($a_file_id3["artist"]))
					{
						$o->set_prop("artist", $a_file_id3["artist"]);
					}
					
					if (isset($a_file_id3["album"]))
					{
						$o->set_prop("album", $a_file_id3["album"]);
					}
					
					if (isset($a_file_id3["year"]))
					{
						$o->set_prop("year", $a_file_id3["year"]);
					}
					
					if  (isset($a_file_id3["genre"]))
					{
						$o->set_prop("genre", $a_file_id3["genre"]);
					}
				}
				
				// make playtime string
				$playtime_mins = floor ($a_file_id3["seconds"] / 60);
        		$playtime_secs = $a_file_id3["seconds"] % 60;
				$a_file_id3["playtime_string"] = $playtime_mins.":".$playtime_secs;
				
				// mpeg info
				{
					$o->set_prop("mpeg_info_filesize", $a_file_id3["filesize"]);
					$o->set_prop("mpeg_info_bitrate", $a_file_id3["bitrate"]);
					$o->set_prop("mpeg_info_playtime_seconds", $a_file_id3["seconds"]);
					$o->set_prop("mpeg_info_playtime_string", $a_file_id3["playtime_string"]);
				}
				
				$o->set_name(mp3::get_name($a_file_id3, t("nimetu.mp3")));
				$o->set_prop("file", $final_name);
				$o->save();
			}
	}

	function set_property($arr = array())
	{
		$obj_inst = $arr["obj_inst"];
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "fileupload":
			$src_file = $ftype = "";
			$oldfile = $arr["obj_inst"]->prop($prop["file"]);
			
			if (!empty($prop["value"]["tmp_name"]))
			{
				// this happens if for example releditor is used
				$src_file = $prop["value"]["tmp_name"];
				$ftype = $prop["value"]["type"];
				// I'm not quite sure how the type can be empty, but the code was here before,
				// so it must be needed
				if (empty($ftype))
				{
					$ftype = "audio/mp3";
				};
			};
			
			if (is_uploaded_file($_FILES[$prop["name"]]["tmp_name"]))
			{
				// this happens if file is uploaded from the image class directly
				$src_file = $_FILES[$prop["name"]]["tmp_name"];
				$ftype = $_FILES[$prop["name"]]["type"];
			};
			
			// if a file was found, then move it to wherever it should be located
			if (is_uploaded_file($src_file))
			{
				$_fi = new file();
				$final_name = $_fi->generate_file_path(array(
					"type" => "audio/mp3",
				));
				
				move_uploaded_file($src_file, $final_name);
				
				// get idv3v1 tags to object
				{
					//$a_file_id3 = id3_get_tag($final_name);
					$a_file_id3 = array();
					$a_file_id3 = id3_get_tag($final_name);
					$obj_inst->set_prop("md5", md5_file($final_name) );
					
					$a_file_id3["filesize"] = filesize($final_name);
					
					$a_file_id3 = array_merge($a_file_id3, $this->mp3info($final_name));
					
					if (isset($a_file_id3["track"]))
					{
						$obj_inst->set_prop("track", $a_file_id3["track"]);
					}
					
					if (isset($a_file_id3["title"]))
					{
						$obj_inst->set_prop("title", $a_file_id3["title"]);
					}
					
					if (isset($a_file_id3["artist"]))
					{
						$obj_inst->set_prop("artist", $a_file_id3["artist"]);
					}
					
					if (isset($a_file_id3["album"]))
					{
						$obj_inst->set_prop("album", $a_file_id3["album"]);
					}
					
					if (isset($a_file_id3["year"]))
					{
						$obj_inst->set_prop("year", $a_file_id3["year"]);
					}
					
					if  (isset($a_file_id3["genre"]))
					{
						$obj_inst->set_prop("genre", $a_file_id3["genre"]);
					}
				}
				
				// make playtime string
				$playtime_mins = floor ($a_file_id3["seconds"] / 60);
        		$playtime_secs = $a_file_id3["seconds"] % 60;
				$a_file_id3["playtime_string"] = $playtime_mins.":".$playtime_secs;
				
				// mpeg info
				{
					$obj_inst->set_prop("mpeg_info_filesize", $a_file_id3["filesize"]);
					$obj_inst->set_prop("mpeg_info_bitrate", $a_file_id3["bitrate"]);
					$obj_inst->set_prop("mpeg_info_playtime_seconds", $a_file_id3["seconds"]);
					$obj_inst->set_prop("mpeg_info_playtime_string", $a_file_id3["playtime_string"]);
				}
				
				// get rid of the old file
				if (file_exists($oldfile))
				{
					// also, we should check if any OTHER file objects point to this file.
					// if they do, then don't delete the old one. this is sort-of like reference counting:P
					// because copy/paste on files creates a new object that points to the same file. 
					$ol = new object_list(array(
						"class_id" => CL_MP3,
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
						$arr["obj_inst"]->set_name($this->get_name($a_file_id3, $prop["value"]["name"]));
					}
					else
					{
						$arr["obj_inst"]->set_name($this->get_name($a_file_id3, $_FILES[$prop["name"]]["name"]));
					}
				}
				$obj_inst->set_prop("file", $final_name);
			}
			else
			{
				$retval = PROP_IGNORE;
			};
				break;
			}
		return $retval;
	}
	
	//to pad binary strings so they represent the correct 8 bits.
	function mp3info_padd (&$inf)
	{
		while (strlen($inf)<8)
		{
			$inf = "0".$inf;
		}
	}
	
	function mp3info($filename)
	{
		$file = fopen($filename, 'r') or die("Can't open file");
		
		// Checking to make sure I can find Frame Sync
		while (!feof($file))
		{
			$tmp=fgetc($file);
			if (ord($tmp)==255)
			{
				$tmp=fgetc($file);
				if (substr((decbin(ord($tmp))),0,3)=="111")
				{
					break;
				}
			}
		}
		// If end of file is reached before Frame Sync is found then bail...
		if (feof($file))
		{
			fclose($file);
			die('Are you sure this is a MP3?');
		}
		// We have declared all engines go.
		// Assign filesize
		$filesize=filesize($filename);
		// Assign all important information to $bitstream variable.
		$inf=decbin(ord($tmp));
		mp3::mp3info_padd($inf);
		$bitstream = $inf;
		$tmp=fgetc($file);
		$inf=decbin(ord($tmp));
		mp3::mp3info_padd($inf);
		$bitstream = $bitstream.$inf;
		$tmp=fgetc($file);
		$inf=decbin(ord($tmp));
		mp3::mp3info_padd($inf);
		$bitstream = $bitstream.$inf;
		// $bitstream now totals the 3 important bytes of the header of this frame.
		// Determine Version of Mpeg.
		switch (substr($bitstream,3,2))
		{
			case "00":
				$version="2.5";
				break;
			case "01":
				$version="0";
				break;
			case "10":
				$version="2";
				break;
			case "11":
				$version="1";
				break;
		}
		// Determine Layer.
		switch (substr($bitstream,5,2))
		{
			case "00":
				$layer="0";
				break;
			case "01":
				$layer="3";
				break;
			case "10":
				$layer="2";
				break;
			case "11":
				$layer="1";
				break;
		}
		// Determine CRC checking enabled / disabled 1==disabled
		$crc = substr($bitstream,7,1);
		// Determine Bitrate
		// Setting an index variable ... trust me in this state tis the only way I can think of doing it...
		if (($version=="1")&($layer=="1"))
		{
			$index="1";
		}
		elseif (($version=="1")&($layer=="2"))
		{
			$index="2";
		}
		elseif ($version=="1")
		{
			$index="3";
		}
		elseif ($layer=="1")
		{
			$index="4";
		}
		else	
		{
			$index="5";
		}
		switch (substr($bitstream,8,4))
		{
			case "0000":
				$bitrate="free";
				break;
			case "0001":
				if (($layer>1)and($version>1))
					{
						$bitrate="8000";
					}
				else
					{
						$bitrate="32000";
					}
				break;
			case "0010":
				switch ($index)
					{
						case "1":
							$bitrate="64000";
							break;
						case "2":
							$bitrate="48000";
							break;
						case "3":
							$bitrate="40000";
							break;
						case "4":
							$bitrate="48000";
							break;
						case "5":
							$bitrate="16000";
							break;
					}
				break;
			case "0011":
				switch ($index)
					{
						case "1":
							$bitrate="96000";
							break;
						case "2":
							$bitrate="56000";
							break;
						case "3":
							$bitrate="48000";
							break;
						case "4":
							$bitrate="56000";
							break;
						case "5":
							$bitrate="24000";
							break;
					}
				break;
			case "0100":
				switch ($index)
					{
						case "1":
							$bitrate="128000";
							break;
						case "2":
							$bitrate="64000";
							break;
						case "3":
							$bitrate="56000";
							break;
						case "4":
							$bitrate="64000";
							break;
						case "5":
							$bitrate="32000";
							break;
					}
				break;
			case "0101":
				switch ($index)
					{
						case "1":
							$bitrate="160000";
							break;
						case "2":
							$bitrate="80000";
							break;
						case "3":
							$bitrate="64000";
							break;
						case "4":
							$bitrate="80000";
							break;
						case "5":
							$bitrate="40000";
							break;
					}
				break;
			case "0110":
				switch ($index)
					{
						case "1":
							$bitrate="192000";
							break;
						case "2":
							$bitrate="96000";
							break;
						case "3":
							$bitrate="80000";
							break;
						case "4":
							$bitrate="96000";
							break;
						case "5":
							$bitrate="48000";
							break;
					}
				break;
			case "0111":
				switch ($index)
					{
						case "1":
							$bitrate="224000";
							break;
						case "2":
							$bitrate="112000";
							break;
						case "3":
							$bitrate="96000";
							break;
						case "4":
							$bitrate="112000";
							break;
						case "5":
							$bitrate="56000";
							break;
					}
				break;
			case "1000":
				switch ($index)
					{
						case "1":
							$bitrate="256000";
							break;
						case "2":
							$bitrate="128000";
							break;
						case "3":
							$bitrate="112000";
							break;
						case "4":
							$bitrate="128000";
							break;
						case "5":
							$bitrate="64000";
							break;
					}
				break;
			case "1001":
				switch ($index)
					{
						case "1":
							$bitrate="288000";
							break;
						case "2":
							$bitrate="160000";
							break;
						case "3":
							$bitrate="128000";
							break;
						case "4":
							$bitrate="144000";
							break;
						case "5":
							$bitrate="80000";
							break;
					}
				break;
			case "1010":
				switch ($index)
					{
						case "1":
							$bitrate="320000";
							break;
						case "2":
							$bitrate="192000";
							break;
						case "3":
							$bitrate="160000";
							break;
						case "4":
							$bitrate="160000";
							break;
						case "5":
							$bitrate="96000";
							break;
					}
				break;
			case "1011":
				switch ($index)
					{
						case "1":
							$bitrate="352000";
							break;
						case "2":
							$bitrate="224000";
							break;
						case "3":
							$bitrate="192000";
							break;
						case "4":
							$bitrate="176000";
							break;
						case "5":
							$bitrate="112000";
							break;
					}
				break;
			case "1100":
				switch ($index)
					{
						case "1":
							$bitrate="384000";
							break;
						case "2":
							$bitrate="256000";
							break;
						case "3":
							$bitrate="224000";
							break;
						case "4":
							$bitrate="192000";
							break;
						case "5":
							$bitrate="128000";
							break;
					}
				break;
			case "1101":
				switch ($index)
					{
						case "1":
							$bitrate="416000";
							break;
						case "2":
							$bitrate="320000";
							break;
						case "3":
							$bitrate="256000";
							break;
						case "4":
							$bitrate="224000";
							break;
						case "5":
							$bitrate="144000";
							break;
					}
				break;
			case "1110":
				switch ($index)
					{
						case "1":
							$bitrate="448000";
							break;
						case "2":
							$bitrate="384000";
							break;
						case "3":
							$bitrate="320000";
							break;
						case "4":
							$bitrate="256000";
							break;
						case "5":
							$bitrate="160000";
							break;
					}
				break;
			case "1111":
				$bitrate="bad";
				break;
		}
		// Determine Sample Rate
		switch ($version)
		{
			case "1":
				switch (substr($bitstream,12,2))
					{
						case "00":
							$sample="44100";
							break;
						case "01":
							$sample="48000";
							break;
						case "10":
							$sample="32000";
							break;
						case "11":
							$sample="reserved";
							break;
					}
				break;
			case "2":
				switch (substr($bitstream,12,2))
					{
						case "00":
							$sample="22050";
							break;
						case "01":
							$sample="24000";
							break;
						case "10":
							$sample="16000";
							break;
						case "11":
							$sample="reserved";
							break;
					}
				break;
			case "2.5":
				switch (substr($bitstream,12,2))
					{
						case "00":
							$sample="11025";
							break;
						case "01":
							$sample="12000";
							break;
						case "10":
							$sample="8000";
							break;
						case "11":
							$sample="reserved";
							break;
					}
				break;
		}
		// Determine whether padding is set on. 0 == no & 1 == yes
		$padding = substr($bitstream,14,1);
		// Determine the private bit's value. Dont know what for though?
		$private = substr($bitstream,15,1);
		// Determine Channel mode
		switch (substr($bitstream,16,2))
		{
			case "00":
				$cmode="Stereo";
				break;
			case "01":
				$cmode="Joint Stereo";
				break;
			case "10":
				$cmode="Dual Channel";
				break;
			case "11":
				$cmode="Mono";
				break;
		}
		
		// Determine number of frames.
		if ((isset($sample)) and (isset($bitrate)))
		{
			if ($layer=="1")
			{
				$frames=floor($filesize/(floor(((12*$bitrate)/($sample+$padding))*4)));	
			}
			else
			{
				$frames=floor($filesize/(floor((144*$bitrate)/($sample))));
			}
			
			// todo: this can actually lie
			// Determine number of seconds in song.
			if ($layer=="1")
			{
				$seconds=floor((384/$sample)*$frames);
			}
			else
			{
				$seconds=floor((1152/$sample)*$frames);
			}
		}
		
		fclose($file);
		$fred = array(
			"seconds" => $seconds,
			"bitrate" => $bitrate,
			"sample" => $sample,
			"cmode" => $cmode,
			"version" => $version,
			"crc" => $crc,
			"layer" => $layer,
		);
		return($fred);
	}
	
	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}
	
	function do_db_upgrade($tbl, $field, $q, $err)
	{
		if ($tbl)
		{
			$this->db_query(
				"CREATE TABLE mp3
				(
					aw_oid INT NOT NULL AUTO_INCREMENT primary key,
					file varchar(255),
					md5 varchar(32),
					play_count int,
					track varchar(255),
					title varchar(255),
					artist varchar(255),
					album varchar(255),
					year varchar(255),
					genre varchar(255),
					comment varchar(255),
					composer varchar(255),
					orig_artist varchar(255),
					copyright varchar(255),
					url varchar(255),
					encoded_by varchar(255),
					mpeg_info_filesize varchar(255),
					mpeg_info_channels varchar(255),
					mpeg_info_sample_rate varchar(255),
					mpeg_info_bitrate varchar(255),
					mpeg_info_channelmode varchar(255),
					mpeg_info_bitrate_mode varchar(255),
					mpeg_info_codec varchar(255),
					mpeg_info_encoder varchar(255),
					mpeg_info_lossless varchar(255),
					mpeg_info_encoder_options varchar(255),
					mpeg_info_compression_ratio varchar(255),
					mpeg_info_playtime_seconds varchar(255),
					mpeg_info_playtime_string varchar(255),
					mpeg_info_original varchar(255),
					mpeg_info_emphasis varchar(255),
					mpeg_info_copyright varchar(255)
				)"
			);
		}
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

	function admin_if_modify_data(&$data)
	{
		$row_o = obj($data["oid"]);
		$chlink = "JavaScript: void(0)";
		$s_play_url = str_replace("automatweb/","",$this->mk_my_orb("play", array("id" => $data["oid"]),"mp3", false,true,"/"))."/".str_replace("/","_","fail.mp3");
		$s_mp3_onclick = 'myRef = window.open("'.$s_play_url.'","AW MP3 M&auml;ngija","left="+((screen.width/2)-(350/2))+",top="+screen.height/5+",width=350,height=150,toolbar=0,resizable=0,location=0,directories=0,status=0,menubar=0,scrollbars=0")';
		$caption = parse_obj_name($row_o->trans_get_val("name"));
		$comment = $row_o->comment();
		$data["name"] = '<a href="'.$chlink.'" title="'.$comment.'" onClick=\''.$s_mp3_onclick.'\'>'.$caption."</a>";
	}
}
?>
