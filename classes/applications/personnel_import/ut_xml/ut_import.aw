<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/personnel_import/ut_xml/ut_import.aw,v 1.2 2008/01/31 13:50:06 kristo Exp $
// ut_import.aw - UT Import 
/*

@classinfo syslog_type=ST_UT_IMPORT relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kaarel

@default table=objects
@default group=general
@default field=meta
@default method=serialize

@property import type=text store=no
@caption Import

@property show_xml type=text store=no
@caption XML

@groupinfo settings caption="Seaded"
@default group=settings

@property http_xml_structure_file type=textbox
@caption Struktuuride XML

@property http_xml_personnel_file type=textbox
@caption Personali XML

#@property ftp_settings type=releditor reltype=RELTYPE_DATA_SERVER rel_id=first props=server,username,password
#@caption FTP seaded

#@property xml_folder type=textbox
#@caption XML faili asukoht serveris

#@property xml_personnel_file type=textbox
#@caption Töötajate XML fail

#@property xml_structure_file type=textbox
#@caption Struktuuriüksuste XML fail

#@reltype DATA_SERVER value=1 clid=CL_FTP_LOGIN
#@caption FTP kasutaja

*/

class ut_import extends class_base
{
	function ut_import()
	{
		$this->init(array(
			"tpldir" => "applications/UT_import/ut_import",
			"clid" => CL_UT_IMPORT
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "show_xml":
				$prop["value"] = html::href(array(
					"url" => $this->mk_my_orb("show_xml",array("id" => 
						$arr["obj_inst"]->id())),
					"caption" => t("N&auml;ita XMLi"),
				));
				break;

			case "import":
				$prop["value"] = html::href(array(
					"url" => $this->mk_my_orb("make_master_import_happy", array("id" => $arr["obj_inst"]->id())),
					"caption" => t("K&auml;ivita import"),
				));
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

	function get_config($arr)
	{
		$obj = new object($arr["id"]);
		$ftp_conns = $obj->connections_from(array(
			"type" => "RELTYPE_DATA_SERVER",
		));

		if (sizeof($ftp_conns) == 0)
		{
			die(t("You forgot to enter server data"));
		};
		$conn = reset($ftp_conns);
		$ftp_serv = new object($conn->prop("to"));

		$rv["ftp"] = array(
			"host" => $ftp_serv->prop("server"),
			"user" => $ftp_serv->prop("username"),
			"pass" => $ftp_serv->prop("password"),
		);

		return $rv;


	}	

	/**
		@attrib name=make_master_import_happy
		@param id required type=int
	**/
	function make_master_import_happy($arr)
	{
//		aw_disable_acl();
		if(is_oid($arr["slave_obj_id"]))
		{
			$arr["id"] = $arr["slave_obj_id"];
		}

		$obj = new object($arr["id"]);

		/*
		$config = $this->get_config($arr);
		
		if (sizeof($config["ftp"]) == 0)
		{
			die(t("You forgot to enter server data"));
		};
		*/
		
//		if (strlen($obj->prop("xml_personnel_file")) == 0)
		if (strlen($obj->prop("http_xml_personnel_file")) == 0)
		{
			die(t("You forgot to enter personnel XML file info."));
		};	
		
//		if (strlen($obj->prop("xml_structure_file")) == 0)
		if (strlen($obj->prop("http_xml_structure_file")) == 0)
		{
			die(t("You forgot to enter structure XML file info."));
		};	

		$r = array();
		$P = "PERSONS";
		$S = "SECTIONS";
		$O = "ORGANIZATIONS";

		/*
		$c = get_instance(CL_FTP_LOGIN);
		$c->connect($config["ftp"]);

		$fn = $obj->prop("xml_folder") . $obj->prop("xml_personnel_file");
		$data_p = $c->get_file($fn);
		*/
		// "http://salakala:salaparool@www.is.ut.ee/pls/xml/tootajad.xml"
		$f = fopen($obj->prop("http_xml_personnel_file"), "r");
		if ($f === false)
		{
			return false;
		}
		while (!feof($f))
		{
			$data_p .= fread($f, 4096);
		}
		fclose($f);
		$data_p = str_ireplace("Š", "&Scaron;", $data_p);
		$data_p = str_ireplace("š", "&scaron;", $data_p);
		$data_p = str_ireplace("é", "&eacute;", $data_p);
		$data_p = str_ireplace("Ü", "&Uuml;", $data_p);
		$data_p = str_ireplace("ü", "&uuml;", $data_p);
		$data_p = str_ireplace("Õ", "&Otilde;", $data_p);
		$data_p = str_ireplace("õ", "&otilde;", $data_p);
		$data_p = str_ireplace("Ö", "&Ouml;", $data_p);
		$data_p = str_ireplace("ö", "&ouml;", $data_p);
		$data_p = str_ireplace("Ä", "&Auml;", $data_p);
		$data_p = str_ireplace("ä", "&auml;", $data_p);
		$data_p = str_ireplace("&", "&amp;", $data_p);

		/*
		$fn = $obj->prop("xml_folder") . $obj->prop("xml_structure_file");
		$data_s = $c->get_file($fn);
		*/
		// "http://salakala:salaparool@www.is.ut.ee/pls/xml/struktuurid.xml"
		$f = fopen($obj->prop("http_xml_structure_file"), "r");
		if ($f === false)
		{
			return false;
		}
		while (!feof($f))
		{
			$data_s .= fread($f, 4096);
		}
		fclose($f);
		$data_s = str_ireplace("Š", "&Scaron;", $data_s);
		$data_s = str_ireplace("š", "&scaron;", $data_s);
		$data_s = str_ireplace("é", "&eacute;", $data_s);
		$data_s = str_ireplace("Ü", "&Uuml;", $data_s);
		$data_s = str_ireplace("ü", "&uuml;", $data_s);
		$data_s = str_ireplace("Õ", "&Otilde;", $data_s);
		$data_s = str_ireplace("õ", "&otilde;", $data_s);
		$data_s = str_ireplace("Ö", "&Ouml;", $data_s);
		$data_s = str_ireplace("ö", "&ouml;", $data_s);
		$data_s = str_ireplace("Ä", "&Auml;", $data_s);
		$data_s = str_ireplace("ä", "&auml;", $data_s);
		$data_s = str_ireplace("&", "&amp;", $data_s);

//		$c->disconnect();

		if (strlen($data_p) == 0 || strlen($data_s) == 0)
		{
			die(t("Not enough data to process."));
		};

		$p = xml_parser_create();
		xml_parse_into_struct($p, $data_p, $vals_p, $index_p);
		xml_parser_free($p);

		$p = xml_parser_create();
		xml_parse_into_struct($p, $data_s, $vals_s, $index_s);
		xml_parser_free($p);

		/*
		arr($vals_p);
		arr($index_p);
		arr($vals_s);
		arr($index_s);
		exit;
		/**/

		$curtid = 0;
		foreach($vals_p as $val)
		{
			if($val["tag"] == "TOOTAJA" && $val["type"] == "open")
			{
				$curtid = $val["attributes"]["ID"];
				$pid = $val["attributes"]["ISIKUKOOD"];
				$dob = mktime(0, 0, 0, substr($pid, 3, 2), substr($pid, 6, 2), "19".substr($pid, 1, 2));

				$r[$P][$curtid]["USER"] = $val["attributes"]["KASUTAJATUNNUS"];
				$r[$P][$curtid]["PID"] = $val["attributes"]["ISIKUKOOD"];
				$r[$P][$curtid]["SEX"] = (substr($val["attributes"]["ISIKUKOOD"], 0, 1) == 3) ? 1 : 2;		// 1 = Male, 2 = Female
				$r[$P][$curtid]["FNAME"] = $val["attributes"]["ENIMI"];
				$r[$P][$curtid]["LNAME"] = $val["attributes"]["PNIMI"];
				$r[$P][$curtid]["DOB"] = $dob;
				$r[$P][$curtid]["EMAILS"][0] = $val["attributes"]["EMAIL"];
				$r[$P][$curtid]["PHONES"]["MOBILE"][0] = $val["attributes"]["MOBIIL"];
				$r[$P][$curtid]["PHONES"]["EXTENSION"][0] = $val["attributes"]["SISETEL"];
				$r[$P][$curtid]["SHOW"]["DOB"] = 1 - $val["attributes"]["DONOTSHOWBIRTHDAY"];
				$cou_prof = 0;
			}
			if($val["tag"] == "AMET" && $val["type"] == "complete")
			{
				$r[$P][$curtid]["PROFESSIONS"][$cou_prof]["SECTION"] = $val["attributes"]["STRUKTUUR"];
//				$r[$P][$curtid]["PROFESSIONS"][$cou_prof][""] = $val["attributes"]["ERIALA"];
				$r[$P][$curtid]["PROFESSIONS"][$cou_prof]["NAME"] = $val["attributes"]["ERIALA_NAME"].$val["attributes"]["NIMI"];
				$r[$P][$curtid]["PROFESSIONS"][$cou_prof]["LOAD"] = $val["attributes"]["KOORMUS"];
				$r[$P][$curtid]["PROFESSIONS"][$cou_prof]["QUEUE_NR"] = $val["attributes"]["JRK"];
				$r[$P][$curtid]["PROFESSIONS"][$cou_prof]["ROOM"] = $val["attributes"]["KOHT"];
				$r[$P][$curtid]["PROFESSIONS"][$cou_prof]["COMMENT"] = $val["attributes"]["MARKUS"];
				$r[$P][$curtid]["PROFESSIONS"][$cou_prof]["CONTRACT_STOPPED"] = $val["attributes"]["TL_PEAT"];
				$cou_prof++;
			}
			if($val["tag"] == "KRAAD" && $val["type"] == "complete")
			{
				$r[$P][$curtid]["DEGREE"]["SUBJECT"] = $val["attributes"]["HARU"];
				$r[$P][$curtid]["DEGREE"]["NAME"] = $val["attributes"]["KRAAD"];
			}
		}

		$level_ids = array();
		foreach($vals_s as $val)
		{
			if($val["tag"] == "STRUKTUUR" && ($val["type"] == "open" || $val["type"] == "complete"))
			{
				$curtid = $val["attributes"]["ID"];
				/*
				$level_ids[$val["level"]] = $val["attributes"]["ID"];
				if($val["level"] > 2)
				{				
					$r[$S][$curtid]["PARENT"] = $level_ids[$val["level"] - 1];
				}
				*/
				if(empty($val["attributes"]["M_ID"]))
				{
					$r[$S][$curtid]["PARENT_ORG"] = 1;
				}
				else
				{
					$r[$S][$curtid]["PARENT_SEC"] = $val["attributes"]["M_ID"];
				}

				$r[$S][$curtid]["CODE"] = $val["attributes"]["KOOD"];
				$r[$S][$curtid]["NAME"] = $val["attributes"]["NIMETUS"];
				$r[$S][$curtid]["EN"]["NAME"] = $val["attributes"]["NAME"];
				$r[$S][$curtid]["QUEUE_NR"] = $val["attributes"]["JRK"];
				$r[$S][$curtid]["ADDRESS"] = $val["attributes"]["AADRESS"];
				$r[$S][$curtid]["EMAILS"][0] = $val["attributes"]["EMAIL"];
				$r[$S][$curtid]["URLS"][0] = $val["attributes"]["VEEB"];
				$r[$S][$curtid]["PHONES"]["WORK"][0] = $val["attributes"]["TELEFON"];
				$r[$S][$curtid]["PHONES"]["FAX"][0] = $val["attributes"]["FAKS"];
				$r[$S][$curtid]["PHONES"]["FAX"][1] = $val["attributes"]["FAX"];
			}
		}

		$r[$O][1]["NAME"] = "Tartu &Uuml;likool";

		
		/*
		arr($r);
		exit;
		/**/

		return $r;
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
