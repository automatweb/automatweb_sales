<?php
// $Header: /home/cvs/automatweb_dev/classes/import/deliciousimport.aw,v 1.3 2007/12/16 22:22:19 hannes Exp $
// deliciousimport.aw - del.icio.us import 
/*

@classinfo syslog_type=ST_DELICIOUSIMPORT relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=hannes

@default table=objects
@default group=general

@property name table=objects field=name type=textbox
@caption Kasutajanimi

@property links_count type=text store=no
@caption Linkide arv

@property import_time_estimate type=text store=no
@caption Umbkaudne impordi aeg

@property import_folder type=relpicker reltype=RELTYPE_FOLDER field=meta method=serialize
@caption Kuhu importida

@property document_cfgform type=relpicker reltype=RELTYPE_CFGFORM field=meta method=serialize
@caption Dokumendivorm

@property do_import type=checkbox ch_value=1 store=no
@caption Impordi

@reltype FOLDER value=1 clid=CL_MENU
@caption Impordikaust

@reltype CFGFORM value=2 clid=CL_CFGFORM
@caption Dokumendi seadetevorm

*/

class deliciousimport extends class_base
{
	function deliciousimport()
	{
		$this->init(array(
			"tpldir" => "import/deliciousimport",
			"clid" => CL_DELICIOUSIMPORT
		));
		aw_set_exec_time(AW_LONG_PROCESS);
		$this->first_page;
	}
	
	function _set_do_import($arr)
	{
		$prop = & $arr['prop'];
		if ($prop["value"] == 1)
		{
			$this->get_delicious_links($arr);
		}
	}
	
	function _get_name($arr)
	{
		/*
		$ol = new object_list(array(
			"parent" => 429081,
		));
		$ol->delete(true);
		die();
		*/
		$this->get_first_page($arr);
	}
	
	function _get_import_time_estimate($arr)
	{
		$prop = & $arr['prop'];
		preg_match  ( "/<h2 id=\"page-desc\">.*All.*items.*\((.*)\).*<\/h2>/imsU"  , $this->first_page, $a_match);
		$prop["value"] = 0.19558964525407478427612655800575* $a_match[1]. t(" sekundit");
	}
	
	function get_first_page($arr)
	{
		$obj_inst = $arr["obj_inst"];
		$s_link = "http://del.icio.us/".$obj_inst->prop("name");
		$s_page = utf8_decode (core::get_file(array("file" => $s_link)));
		$this->first_page = $s_page;
	}
	
	function _get_links_count($arr)
	{
		$prop = & $arr['prop'];
		preg_match  ( "/<h2 id=\"page-desc\">.*All.*items.*\((.*)\).*<\/h2>/imsU"  , $this->first_page, $a_match);
		$prop["value"] = $a_match[1];
	}
	
	// links are divided onto pages.. this gets the count
	function get_delicious_page_count($arr)
	{
	$obj_inst = $arr["obj_inst"];
		$s_link = "http://del.icio.us/".$obj_inst->prop("name");
		$s_page = utf8_decode (core::get_file(array("file" => $s_link)));
		
	
		preg_match  ( "/<p class=\"pager\">.*page.*of(.*)<\/p>/imsU"  , $s_page, $a_match);
		
		return trim($a_match[1]);
	}
	
	function get_delicious_links ($arr)
	{
		$obj_inst = $arr["obj_inst"];
		$i_page_count = $this->get_delicious_page_count($arr);
		$s_cont = "";
		$k=0;
		for ($i=1;$i<$i_page_count+1;$i++)
		{
			$s_link = "http://del.icio.us/".$obj_inst->prop("name")."?page=".$i;
			$s_cont .= utf8_decode (core::get_file(array("file"=>$s_link)));
			if ($i%2==0)
			{
				echo ".";
				ob_flush();
				flush();
			}
		}
		
		//preg_match_all("/<li class=\"post\".*<h4 class=\"desc\">.*<a.*href\s*=\s*['\" ](.*)['\" ].*>(.*)<\/a>.*<div class=\"meta\">to(.*)\.\.\..*<span class=\"date\" title=\"(.*)\">/imsU",  $s_cont, $a_matches);
       preg_match_all("/<li class=\"post\".*<h4 class=\"desc\">.*<a.*href\s*=\s*['\" ](.*)['\" ].*>(.*)<\/a>.*<div class=\"meta\">to(.*)<span class=\"date\" title=\"(.*)\">/imsU",  $s_cont, $a_matches);
		
		$i_matches_count = count($a_matches[1]);
		for ($i=0;$i<$i_matches_count ;$i++)
		{
			$s_time =$a_matches[4][$i];
			$a_time["year"] = substr($s_time, 0, 4);
			$a_time["month"] = substr($s_time, 5, 2);
			$a_time["day"] = substr($s_time, 8, 2);
			$a_time["hour"] = substr($s_time, 11, 2)+2; // +2 is timezone fix
			$a_time["minute"] = substr($s_time, 14, 2);
			$a_time["second"] = substr($s_time, 17, 2);
			
			$i_created = mktime ($a_time["hour"], $a_time["minute"], $a_time["second"], $a_time["month"], $a_time["day"],$a_time["year"] );
			
			$s_tags = strip_tags($a_matches[3][$i]);
			$s_tags = preg_match("/(.*)\.\.\./imsU", $s_tags, $a_mt);
			$s_tags = trim ($a_mt[1]);
			
			$o = new object(array(
					"name" => $a_matches[2][$i],
					"parent" => $obj_inst->prop("import_folder"),
					"class_id" => CL_DOCUMENT,
			));
			$o->set_class_id(CL_DOCUMENT);
			$o->set_meta("cfgform_id", $obj_inst->prop("document_cfgform"));
			$o->set_prop("title", $a_matches[2][$i]);
			$o->set_prop("link_text", $a_matches[1][$i]);
			$o->set_prop("keywords", $s_tags);
			$o->set_status(STAT_ACTIVE);
			//$o->set_prop("created", mktime ($a_time["hour"], $a_time["minute"], $a_time["second"], $a_time["month"], $a_time["day"],$a_time["year"] ));
			$o->save();
			
			$this->db_query("UPDATE objects SET created=".$i_created." WHERE oid = ".$o->id() );
			
			if (($i+1)%20==0)
			{
				echo ($i).t(" linki valmis<br>\n");
				ob_flush();
				flush();
			}
			
			
			
			$a_links[] = array(
				"title" => $a_matches[2][$i],
				"href=" => $a_matches[1][$i],
				"tags" => $s_tags,
			);
		}
		echo ($i-1).t(" linki valmis<br>\n");
		ob_flush();
		flush();
		
		arr($a_links);
		/*
		echo '<html>
		<head>
			<meta http-equiv="refresh" content="30;url='.post_ru().'">
		</head>
		
		<body>
		
		</body>
		</html>
		';
		die();
		*/
	}
	
	function test_preg($arr)
	{
		
	
			$s_time_find = array(
					"/just posted/imsU",  //1
					"/(.*)mins\s*ago/imsU",  //2
					"/(.*)hour\s*ago/imsU", // 3
					"/(.*)hours\s*ago/imsU", //4
					"/(.*) day\s*ago/imsU", //5
					"/days\s*ago/imsU", //6
					"/... on (.*) (.*)$/imsU", // 7
					"/... on (.*) (.*), (.*)$/imsU", // 8
			);
			$s_time_replace = array(
					time(),  //1
					"\\1 (min)",  //2
					"\\1 (tundi)", //3
					"\\1 (tundi)",  //4
					"\\1 (p2ev)",  //5
					"\\1 (p2eva)", //6
					"\\1 \\2", // 7
					"\\1 (kuu) \\2 (p2ev) \\ 3 (aasta)", //8
			);
			
			$s_time = preg_replace ($s_time_find, $s_time_replace, $a_matches[3][$i]);
			$s_time = trim($s_time );
			$a_links[] = array(
				"title" => $a_matches[2][$i],
				"href" => $a_matches[1][$i],
				"time" => $s_time ,
			);
		arr($a_links);
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "do_import":
				if ($prop["value"] == 1)
				{
					arr("hh",1);
				}
				
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
			//-- set_property --//
		}
		return $retval;
	}

	function callback_post_save($arr)	
	{
		
		
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
