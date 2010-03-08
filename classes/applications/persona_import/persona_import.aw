<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/persona_import/persona_import.aw,v 1.48 2008/09/16 07:25:44 instrumental Exp $
// persona_import.aw - Persona import 
/*

@classinfo syslog_type=ST_PERSONA_IMPORT relationmgr=yes maintainer=knummert

@default table=objects
@default group=general
@default field=meta
@default method=serialize

@property last_import type=text
@caption Viimane import toimus

@property invk type=text store=no
@caption Import

@property show_xml type=text store=no
@caption XML

@groupinfo settings caption="Seaded"
@default group=settings

	@groupinfo genset caption="&Uuml;ldised seaded" parent=settings
	@default group=genset

		@property xml_link type=relpicker reltype=RELTYPE_LINK_REL
		@caption Impordi objekt

		@property xml_image_folder type=textbox
		@caption Pildifailide kataloog (kui on eraldi)

		@property aw_image_folder type=relpicker reltype=RELTYPE_FOLDER
		@caption Pildiobjektide kataloog

		@property crm_db_id type=relpicker reltype=RELTYPE_CRM_DB
		@caption Kasutatav kliendibaas

	@groupinfo ftp caption="FTP seaded" parent=settings
	@default group=ftp

		@property ftp_settings type=releditor reltype=RELTYPE_DATA_SERVER rel_id=first props=server,username,password
		@caption FTP seaded

		@property xml_folder type=textbox
		@caption XML faili asukoht serveris

		@property xml_filename type=textbox
		@caption XML faili nimi

		@property xml_personnel_file type=textbox
		@caption T&ouml;&ouml;tajate XML fail

		@property xml_education_file type=textbox
		@caption Haridusk&auml;igu XML fail

		@property xml_work_relations_ending_file type=textbox
		@caption T&ouml;&ouml;suhete peatumise XML fail

		@property xml_structure_file type=textbox
		@caption Struktuuri&uuml;ksuste XML fail

@groupinfo tags caption="Tagid XMLs"
@default group=tags

	@groupinfo tags_workers caption="T&ouml;&ouml;tajad" parent=tags
	@default group=tags_workers
	
		@property tootajad_tag type=textbox
		@caption T&ouml;&ouml;tajad
	
		@property tootaja_tag type=textbox
		@caption T&ouml;&ouml;taja
	
		@property tootaja_id_tag type=textbox
		@caption T&ouml;&ouml;taja ID
	
		@property eesnimi_tag type=textbox
		@caption Eesnimi
	
		@property perekonnanimi_tag type=textbox
		@caption Perekonnanimi
	
		@property synniaeg_tag type=textbox
		@caption S&uuml;nniaeg
	
		@property haridustase_tag type=textbox
		@caption Haridustase
	
		@property telefon_tag type=textbox
		@caption Telefon
	
		@property mobiiltelefon_tag type=textbox
		@caption Mobiiltelefon
	
		@property lyhinumber_tag type=textbox
		@caption L&uuml;hinumber
	
		@property e_post_tag type=textbox
		@caption E-posti aadress
	
		@property ametikoht_nimetus_tag type=textbox
		@caption Ametinimetus
	
		@property ametikirjeldus_viit_tag type=textbox
		@caption Ametijuhendi link
	
		@property ruum_tag type=textbox
		@caption Ruum
	
		@property asutus_tag type=textbox
		@caption Asutus
	
		@property allasutus_tag type=textbox
		@caption Allasutus
	
		@property yksus_tag type=textbox
		@caption &Uuml;ksus
	
		@property yksus_id_tag type=textbox
		@caption &Uuml;ksuse ID
	
		@property prioriteet_tag type=textbox
		@caption Prioriteet
	
		@property on_peatumine_tag type=textbox
		@caption T&ouml;&ouml;suhe on peatatud
	
		@property peatumine_pohjus_tag type=textbox
		@caption T&ouml;&ouml;suhte peatamise p&otilde;hjus
	
		@property asutusse_tulek_tag type=textbox
		@caption Asutusse tuleku aeg
	
		@property on_asendaja_tag type=textbox
		@caption On asendaja
	
		@property asendamine_tookoht_tag type=textbox
		@caption Asendatav t&ouml;&ouml;koht

	@groupinfo tags_educations caption="Haridusk&auml;igud" parent=tags
	@default group=tags_educations
	
		@property hariduskaigud_tag type=textbox
		@caption Haridusk&auml;igud
	
		@property hariduskaik_tag type=textbox
		@caption Haridusk&auml;ik
	
		@property edu_tootaja_id_tag type=textbox
		@caption T&ouml;&ouml;taja ID
	
		@property oppeasutus_tag type=textbox
		@caption &Otilde;ppeasutus
	
		@property on_opilane_tag type=textbox
		@caption On &otilde;pilane
	
		@property eriala_tag type=textbox
		@caption Eriala
	
		@property on_pohieriala_tag type=textbox
		@caption On p&otilde;hieriala
	
		@property diplom_number_tag type=textbox
		@caption Diplomi number
	
		@property akadeemiline_kraad_tag type=textbox
		@caption Akadeemiline kraad
	
		@property keel_tag type=textbox
		@caption Omandamise keel
	
		@property diplom_kuupaev_tag type=textbox
		@caption Diplomi kuup&auml;ev

	@groupinfo tags_end_wr caption="T&ouml;&ouml;suhte peatumised" parent=tags
	@default group=tags_end_wr
	
		@property toosuhte_peatumised_tag type=textbox
		@caption T&ouml;&ouml;suhte peatumised
	
		@property toosuhte_peatumine_tag type=textbox
		@caption T&ouml;&ouml;suhte peatumine
	
		@property ewr_tootaja_id_tag type=textbox
		@caption Tootaja ID
	
		@property alguskuupaev_tag type=textbox
		@caption Alguskuup&auml;ev
	
		@property loppkuupaev_tag type=textbox
		@caption L&otilde;ppkuup&auml;ev
	
		@property peatumise_liik_tag type=textbox
		@caption Peatumise liik
	
		@property asendaja_id_tag type=textbox
		@caption Asendaja ID

@groupinfo autoimport caption="Automaatne import"

	@property recur_edit type=releditor reltype=RELTYPE_RECURRENCE use_form=emb group=autoimport rel_id=first
	@caption Automaatse impordi seadistamine

@reltype CRM_DB value=1 clid=CL_CRM_DB
@caption kliendibaas

@reltype DATA_SERVER value=2 clid=CL_FTP_LOGIN
@caption FTP kasutaja

@reltype FOLDER value=3 clid=CL_MENU
@caption Kaust

@reltype LOGFILE value=4 clid=CL_FILE
@caption Logifail

@reltype RECURRENCE value=5 clid=CL_RECURRENCE
@caption Kordus

@reltype LINK_REL value=22 clid=CL_TAAVI_IMPORT
@caption Impordi objekt

*/

class persona_import extends class_base
{
	function persona_import()
	{
		$this->init(array(
			"clid" => CL_PERSONA_IMPORT
		));

		$this->stat_file = aw_ini_get("site_basedir")."/files/persona_import_in_progress.txt";
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			// T88tajad
			case "tootajad_tag":
			case "tootaja_tag":
			case "tootaja_id_tag":
			case "eesnimi_tag":
			case "perekonnanimi_tag":
			case "synniaeg_tag":
			case "haridustase_tag":
			case "telefon_tag":
			case "mobiiltelefon_tag":
			case "lyhinumber_tag":
			case "e_post_tag":
			case "ametikoht_nimetus_tag":
			case "ametikirjeldus_viit_tag":
			case "ruum_tag":
			case "asutus_tag":
			case "allasutus_tag":
			case "yksus_tag":
			case "yksus_id_tag":
			case "prioriteet_tag":
			case "on_peatumine_tag":
			case "peatumine_pohjus_tag":
			case "asutusse_tulek_tag":
			case "on_asendaja_tag":
			case "asendamine_tookoht_tag":
			// Haridusk2igud
			case "hariduskaigud_tag":
			case "hariduskaik_tag":
			case "edu_tootaja_id_tag":
			case "oppeasutus_tag":
			case "on_opilane_tag":
			case "eriala_tag":
			case "on_pohieriala_tag":
			case "diplom_number_tag":
			case "akadeemiline_kraad_tag":
			case "keel_tag":
			case "diplom_kuupaev_tag":
			// T88suhte peatumised
			case "toosuhte_peatumised_tag":
			case "toosuhte_peatumine_tag":
			case "ewr_tootaja_id_tag":
			case "alguskuupaev_tag":
			case "loppkuupaev_tag":
			case "peatumise_liik_tag":
			case "asendaja_id_tag":
				$tags = $this->get_tags($arr["obj_inst"]);
				$prop["value"] = $tags[$prop["name"]];
				break;

			case "last_import":
				$prop["value"] = aw_locale::get_lc_date($prop["value"],6) . date(" H:i",$prop["value"]);
				break;

			case "show_xml":
				$prop["value"] = html::href(array(
					"url" => $this->mk_my_orb("show_xml",array("id" => 
						$arr["obj_inst"]->id())),
					"caption" => t("N&auml;ita XMLi"),
				));
				break;

			case "invk":
				$prop["value"] = html::href(array(
					"url" => $this->mk_my_orb("invoke",array("id" => $arr["obj_inst"]->id())),
					"caption" => t("K&auml;ivita import"),
				));
				// now check whether we have a half completed status file
				$status_file_content = $this->get_file(array(
					"file" => $this->stat_file,
				));
				if ($status_file_content)
				{
					$sdata = aw_unserialize($status_file_content);
					$done = 0;
					$total = sizeof($sdata);
					foreach($sdata as $key => $val)
					{
						if ($val == 1)
						{
							$done++;
						};
					};

					$capt = " $done / $total";
					$prop["value"] = html::href(array(
						"url" => $this->mk_my_orb("invoke",array(
							"id" => $arr["obj_inst"]->id(),
						)),
						"caption" => t("J&auml;tka poolikut importi") . $capt,
					));
				};
				$prop["value"] .= " | ";
				$prop["value"] .= html::href(array(
					"url" => $this->mk_my_orb("import_images",array("id" => $arr["obj_inst"]->id())),
					"caption" => t("Impordi pildid"),
				));
				break;

		};
		return $retval;
	}

	/*
	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{

		}
		return $retval;
	}	
	*/

	function get_config($obj)
	{
		if(!is_object($obj))
		{
			$obj = obj($obj["id"]);
		}
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

		if (is_oid($obj->prop("aw_image_folder")))
		{
			$rv["image_folder"] = $obj->prop("aw_image_folder");
		};
		return $rv;


	}

	/**
		@attrib name=show_xml 
		@param id required type=int
	**/
	function show_xml($arr)
	{
		// aw_disable_acl();

		$obj = new object($arr["id"]);
		header("Content-type: text/xml");
		die($this->get_xml_data($obj, false));
	}

	/**
		@attrib name=invoke nologin="1"
		@param id required type=int
	**/
	function invoke($arr)
	{
		$this->check_in_progress();
		/*
		$ol = new object_list(array(
			"parent" => 26366,
			"class_id" => array(),
		));
		$ol->delete();
		$cache = get_instance("cache");
		$cache->full_flush();
		exit;
		*/
		// aw_disable_acl();
		$obj = new object($arr["id"]);
		
		$import_id = $obj->prop("xml_link");
		if(!$this->can("view" , $import_id))
		{
			$config = $this->get_config($obj);
		}

		
		
		if (sizeof($config["ftp"]) == 0 && !$this->can("view" , $import_id))
		{
			die(t("You forgot to enter server data"));
		};

		$crm_db_id = $obj->prop("crm_db_id");

		if (!is_oid($crm_db_id))
		{
			//die(t("Nii ei saa ju rallit s&otilde;ita!"));
			die(t("Kasutatav kliendibaas valimata"));
		};

		$crm_db = new object($crm_db_id);

		$folder_person = $crm_db->prop("folder_person");
		//arr($crm_db->properties());


		if (!is_oid($folder_person))
		{
			die(t("Isikute kataloog valimata"));
		};

		$folder_address = $crm_db->prop("dir_address");
		if (!is_oid($folder_address))
		{
			die(t("Aadresside kataloog valimata"));
		};

		$dir_default = $crm_db->prop("dir_default");
		if (!is_oid($dir_default))
		{
			die(t("Default kataloog valimata"));
		};

		$dir_company = $crm_db->prop("dir_firma");
		if (!is_oid($dir_company))
		{
			$dir_company = $dir_default;
		};

		// figure out variable manager
		$mt_conns = $crm_db->connections_from(array(
			"type" => "RELTYPE_METAMGR",
		));
		if (sizeof($mt_conns) == 0)
		{
			die(t("Kliendibaasil puudub muutujate haldur"));
		};

		$first = reset($mt_conns);
		$metamgr = new object($first->prop("to"));

		$meta1list = new object_list(array(
			"parent" => $metamgr->id(),
			"class_id" => CL_META,
			"site_id" => array(),
		));

		$meta1 = array_flip($meta1list->names());

		print "Creating variable categories, if any ...<br>";
		flush();

		// doing this by name sucks of course, but since this is a one-time application ..
		// well .. it just has to work
		if (!$meta1["Puhkuste liigid"])
		{
			$m1 = new object();
			$m1->set_parent($metamgr->id());
			$m1->set_class_id(CL_META);
			$m1->set_status(STAT_ACTIVE);
			$m1->set_name("Puhkuste liigid");
			aw_disable_acl();
			$m1->save();
			aw_restore_acl();
			$meta_cat["puhkused"] = $m1->id();
		}
		else
		{
			$meta_cat["puhkused"] = $meta1["Puhkuste liigid"];
		};
		
		if (!$meta1["Peatumiste liigid"])
		{
			$m1 = new object();
			$m1->set_parent($metamgr->id());
			$m1->set_class_id(CL_META);
			$m1->set_status(STAT_ACTIVE);
			$m1->set_name("Peatumiste liigid");
			aw_disable_acl();
			$m1->save();
			aw_restore_acl();
			$meta_cat["peatumised"] = $m1->id();
		}
		else
		{
			$meta_cat["peatumised"] = $meta1["Peatumiste liigid"];
		};

		print t("Getting source data<br>");
		flush();

		$fdat = $this->get_xml_data($obj);

		if (strlen($fdat) <= 62)
		{
			die(t("Not enough data to process<br>"));
		};

		print "<h6>";
		print strlen($fdat) . " bytes of data to process";
		print "</h6>";

		/*
		print "<pre>";
		print htmlspecialchars($fdat);
		print "</pre>";
		die();
		*/

		print t("got data<br>");
	
		flush();

		$p = xml_parser_create();
		xml_parse_into_struct($p, $fdat, $vals, $index);
		xml_parser_free($p);

		/*
		print "<h1>vals</h1>";
		arr($vals);
		die();
		*/

		print "<h1>done</h1>";

		print t("parse finished, processing starts<br>");
		flush();


		$workers = array();

		$process_workers = $process_stops = false;

		$obj->set_prop("last_import",time());
		aw_disable_acl();
		$obj->save();
		aw_restore_acl();

		$tags = $this->get_tags($obj);
		extract($tags);

		$interesting_containers = array($tootajad_tag,"PEATUMISED","PUHKUSED","YKSUSED",$hariduskaigud_tag,$toosuhte_peatumised_tag);

		$w_open = false;
		$tmp = array();
		$target = false;
		$data = array();

		$processing = array();

		// I have to monitor how much time has passed from the start of the exection
		// and for this ... I have to write a file. Would like to create an object, but
		// that takes some time

		// so a file is preferred

		// status - check .. open the file, count data. if > 1 persons left, 
		// do the import

		$stat_file = $this->stat_file;

		foreach($vals as $val)
		{
			if (in_array($val["tag"],$interesting_containers))
			{
				if ("open" == $val["type"])
				{
					$processing[$val["tag"]] = true;
					$target = $val["tag"];
					print "setting target to $target<br>";
					flush();
				}
				elseif ("close" == $val["type"])
				{
					$processing[$val["tag"]] = false;
					$target = false;
				};
			};


			if ($target && ($tootaja_tag == $val["tag"] || $hariduskaik_tag == $val["tag"] || $toosuhte_peatumine_tag == $val["tag"]))
			{
				if ("open" == $val["type"])
				{
					$w_open = true;
				}
				elseif ("close" == $val["type"])
				{
					$data[$target][] = $tmp;
					$tmp = array();
					$w_open = false;
				};
			};
			
			if ($target && $w_open && "complete" == $val["type"])
			{
				// iconv seemed to have a problem with &otilde; character. Better safe than sorry.
				$tmp[$val["tag"]] = str_replace("&otilde;", "&otilde;", $val["value"]);
				$tmp[$val["tag"]] = str_replace("&auml;", "&auml;", $tmp[$val["tag"]]);
				$tmp[$val["tag"]] = str_replace("&ouml;", "&ouml;", $tmp[$val["tag"]]);
				$tmp[$val["tag"]] = str_replace("&uuml;", "&uuml;", $tmp[$val["tag"]]);
				$tmp[$val["tag"]] = str_replace("&otilde;", "&Otilde;", $tmp[$val["tag"]]);
				$tmp[$val["tag"]] = str_replace("&auml;", "&Auml;", $tmp[$val["tag"]]);
				$tmp[$val["tag"]] = str_replace("&ouml;", "&Ouml;", $tmp[$val["tag"]]);
				$tmp[$val["tag"]] = str_replace("&uuml;", "&Uuml;", $tmp[$val["tag"]]);
			};
		};

		$persona_import_started = aw_global_get("persona_import_started");

		$persona_to_process = array();

		$stat_file_content = $this->get_file(array(
			"file" => $stat_file,
		));

		if (!$stat_file_content)
		{
			// fill the array with pairs of ext_id => person_done pairs
			// at the start everyone gets flagged with 0
			foreach($data[$tootajad_tag] as $worker)
			{
				$ext_id = $worker[$tootaja_id_tag];
				$persona_to_process[$ext_id] = 0;
			};
		
			$this->put_file(array(
				"file" => $stat_file,
				"content" => aw_serialize($persona_to_process),
			));
		}
		else
		{
			// load status array back into memory
			print "Trying to continue aborted import process<br>";
			flush();
			$persona_to_process = aw_unserialize($stat_file_content);
		};

		// $folder for persons is defined in the connected crm_db
		print "creating person list<br>";
		flush();
		$person_list = new object_list(array(
			"parent" => $folder_person,
			"class_id" => CL_CRM_PERSON,
			"site_id" => array(),
//			"lang_id" => array(),
		));
		
		foreach($person_list->arr() as $person_obj)
		{
			/*
			print "ID = ".$person_obj->id()."<br>";
			print "ext_id = ".$person_obj->prop("ext_id")."<br>";
			print "ext_id_alphanumeric = ".$person_obj->prop("ext_id_alphanumeric")."<br>";
			print "subclass = ".$person_obj->subclass()."<br>";
			arr($person_match);
			print "<br>";
			*/
			$ext_id = $person_obj->prop("ext_id");
			/*if ($person_obj->id() == 196855)
			{
				echo "got ext id as $ext_id <br>".dbg::dump($person_obj->subclass());
			}*/
			if (!$ext_id)
			{
				$ext_id = $person_obj->subclass();
			}
			/*
			var_dump($person_obj->id());
			print " = ";
			var_dump($ext_id);
			var_dump($person_obj->prop("ext_id"));
			print "<br>";
			arr($person_obj->properties());
			*/

			// After updating the import, we save the TOOTAJA_ID into ext_id_alphanumeric.
			if (!$ext_id)
			{
				$ext_id = $person_obj->prop("ext_id_alphanumeric");
			}

			if ($ext_id)
			{
				if(is_oid($person_match[$ext_id]) && $person_match[$ext_id] != $person_obj->id())
				{					
					// If we have multiple objects with the same external ID, we delete 'em. Only need one.
					// We keep the object with the smaller ID.
					if($person_match[$ext_id] > $person_obj->id())
					{
						$doomed_person_obj = obj($person_match[$ext_id]);
						$person_match[$ext_id] = $person_obj->id();
						$person_match_[$ext_id] = $person_obj->id();

						print "worker object with external id (TOOTAJA_ID) ".$ext_id." already exists. deleting object with id ".$doomed_person_obj->id()."<br>";
						$doomed_person_obj->delete(true);
					}
					else
					{
						print "worker object with external id (TOOTAJA_ID) ".$ext_id." already exists. deleting object with id ".$person_obj->id()."<br>";
						$person_obj->delete(true);
					}
				}
				else
				{
					$person_match[$ext_id] = $person_obj->id();
					$person_match_[$ext_id] = $person_obj->id();
				}
			};
		};

		print "creating address list<br>";
		flush();

		// list of addresses
		$addr_list = new object_list(array(
			"class_id" => CL_CRM_ADDRESS,
			"site_id" => array(),
//			"lang_id" => array()
		));

		$addr = $addr_list->names();
		// ----------------------------

		$c = new connection();
		$email_connections = $c->find(array(
			"from.class_id" => CL_CRM_PERSON,
			"to.class_id" => CL_ML_MEMBER,
		));

		// this will now contain links between persons and e-mail objects. I will
		// use that information later to decide whether a new e-mail object
		// needs to created or an existing one updated
		$email_links = array();
		foreach($email_connections as $email_connection) 
		{
			$email_links[$email_connection["from"]] = $email_connection["to"];
		};
 
		print "creating phone list<br>";
		flush();

		// list of phone numbers
		$phone_list = new object_list(array(
			"class_id" => CL_CRM_PHONE,
			"parent" => $dir_default,
			"site_id" => array(),
//			"lang_id" => array()
		));

		$phones = array_flip($phone_list->names());
		// -------------------------------

		//$person_match = array();

		$phone_type = array(
			$telefon_tag => "work",
			$mobiiltelefon_tag => "mobile",
			$lyhinumber_tag => "short",
		);

		$simple_attribs = array(
			/*
			$haridustase_tag => array(
				"reltype" => 23, // RELTYPE_EDUCATION
				"prop" => "education",
				"clid" => CL_CRM_PERSON_EDUCATION,
			),
			/**/
			"AADRESS" => array(
				"reltype" => 1, // RELTYPE_ADDRESS
				"prop" => "address",
				"clid" => CL_CRM_ADDRESS,
			),
			/*
			$ametikoht_nimetus_tag => array(
				"reltype" => 7, // RELTYPE_PROFESSION
				"prop" => "rank",
				"clid" => CL_CRM_PROFESSION,
			),
			*/
		);

		$simple_data = array();

		foreach($simple_attribs as $key => $sdata)
		{
			$olist = new object_list(array(
				"class_id" => $sdata["clid"],
				"parent" => $dir_default,
				"site_id" => array(),
//				"lang_id" => array()
			));
			$simple_data[$key] = array_flip($olist->names());

		};
		
		/*
		arr($simple_data);
		exit;
		*/
		print "creating company list<br>";
		flush();
		$ol = new object_list(array(
			"class_id" => CL_CRM_COMPANY,
			"parent" => array($dir_default, $dir_company),
		));

		$impd_objs_comp = array_flip($ol->names());
		foreach($impd_objs_comp as $impd_objs_comp_name => $impd_objs_comp_el)
		{
			print "got company named '".$impd_objs_comp_name."'<br>";
			flush();
			$ol->remove($impd_objs_comp_el);
		}
		/*
		if($ol->count() > 0)
		{
			print "deleting duplicate companies<br>";
			arr($ol->ids());
			flush();
			aw_disable_acl();
			$ol->delete();
			aw_restore_acl();
		}
		*/

		
		print "creating substitute list<br>";
		flush();

		$subst_ol = new object_list(array(
			"parent" => $dir_default,
			"class_id" => CL_CRM_PROFESSION,
		));
		$subst_ol_arr = $subst_ol->arr();


		print t("creating yksused objects<br>");
		flush();
		/*
			 <yksused>
				  <rida>
				   <yksus_id>1</yksus_id>
				   <ylemyksus_id></ylemyksus_id>
				   <nimetus>Keskkonnaministeerium</nimetus>
				   <pohimaarus_viit></pohimaarus_viit>
				   <aadress>Toompuiestee 24, 15172 Tallinn</aadress>
				  </rida>


		*/

		$seco = new object_list(array(
			"class_id" => CL_CRM_SECTION,
			"site_id" => array(),
		));

		$sections = array();
		$sections_byname = array();
		foreach($seco->arr() as $sec_obj)
		{
			//$sections[$sec_obj->prop("ext_id")] = $sec_obj->id();
			$sections[$sec_obj->subclass()] = $sec_obj->id();
			$sections_byname[$sec_obj->name()] = $sec_obj->id();
		};

		//arr($data["YKSUSED"]);
		//arr($sections);


		$links = array();
		foreach($data["YKSUSED"] as $yksus)
		{
			//arr($yksus);

			$name = iconv("UTF-8", "ISO-8859-4",$yksus["NIMETUS"]);
			$ext_id = $yksus[$yksus_id_tag];
			$ylem = $yksus["YLEMYKSUS_ID"];


			// aga n&uuml;&uuml;d vaja kontrollida, et kas sellise ext_id-ga &uuml;ksus on olemas, kui on, siis
			// uut pole vaja teha
			//$links = $sections[$ylem][] = $sections[$ext_id];

			if (!empty($ylem))
			{
				$links[$ylem][] = $ext_id;
			};

			if (empty($sections[$ext_id]))
			{
				print t("created new section<br>");
				$yk = new object();
				$yk->set_parent($dir_default);
				$yk->set_class_id(CL_CRM_SECTION);
				$yk->set_prop("ext_id",$ext_id);
				$yk->set_name($name);
				aw_disable_acl();
				$yk->save();
				aw_restore_acl();

				$ykid = $yk->id();
				$sections[$ext_id] = $ykid;
			}
			else
			{
				print t("updating existing section<br>");
				$yk = new object($sections[$ext_id]);
				$yk->set_name($name);
				aw_disable_acl();
				$yk->save();
				aw_restore_acl();
				print "done<br>";
			};
		}

		// n&uuml;&uuml;d on vaja leida olemasolevad seosed
		$c = new connection();
		$existing = $c->find(array(
			"from.class_id" => CL_CRM_SECTION,
			"to.class_id" => CL_CRM_SECTION,
			"type" => 1 // RELTYPE_SECTION, ehk alam&uuml;ksus
		));

		/*
		foreach($existing as $conn)
		{
			arr($conn);
		};
		*/

		foreach($links as $parent_section => $link_section)
		{
			foreach($link_section as $child_section)
			{
				$o1 = new object($sections[$parent_section]);
				$o2 = new object($sections[$child_section]);

				// check, that if the one to connect to is crm_company
				// and the section already is connected to some other section, then don't do it
				/*if ($o1->class_id() == CL_CRM_COMPANY && count($o2->connections_to(array("type" => 1))))
				{
					echo "do not connect section ".$o2->name()." to company, because it already is on a deeper level <br>";
					continue;
				}*/
		
				print "connecting ";
				//print $sections[$parent_section];
				print $o1->name();
				print " to ";
				print $o2->name();
				print "<bR>";
				$o1->connect(array(
					"to" => $o2->id(),
					"reltype" => 1, // RELTYPE_SECTION,
				));
				//print $sections[$child_section];
				print "connect done<br>";
				//arr($o1->properties());
				//arr($o2->properties());
			};
		};	

		classload("core/util/timer");
		$aw_timer = new aw_timer();


		/*
		print t("<h1>sections</h1>");
		arr($sections);
		print t("<h1>sections done</h1>");
		*/

		$persona_persons_done = aw_global_get("persona_persons_done");

		/*
		print "pmatch";
		arr($person_match);
		print "match done<br>";
		*/

		$persons = array();

		print t("creating person objects<br>");
		$persons_per_batch = 10;
		$batchcounter = 0;

		// if we exceed this, restart import
		$time_limit = 90;

		$aw_timer->start("personimport");
		foreach($data[$tootajad_tag] as $worker)
		{
			$ext_id = $worker[$tootaja_id_tag];
			$worker[$eesnimi_tag] = iconv("UTF-8", "ISO-8859-4",$worker[$eesnimi_tag]);
			$worker[$perekonnanimi_tag] = iconv("UTF-8", "ISO-8859-4",$worker[$perekonnanimi_tag]);

			$person_name = $worker[$eesnimi_tag]." ".$worker[$perekonnanimi_tag];
			if ($persona_to_process[$ext_id] == 1)
			{
				print "$person_name has already been imported in this import, skipping<br>";
				unset($person_match[$ext_id]);
				continue;
			};
			if (!is_oid($person_match[$ext_id]))
			{
				// create new object
				$person_obj = new object();
				$person_obj->set_parent($folder_person);
				$person_obj->set_class_id(CL_CRM_PERSON);
				print t("<br>creating new person object<br>");
			}
			else
			{
				$person_obj = new object($person_match[$ext_id]); 
				unset($person_match[$ext_id]);
				print t("updating existing person object<br>");
			};	

			print "SA = " . $worker[$synniaeg_tag];
//			$bd_parts = explode(".",$worker[$synniaeg_tag]);
//			$bd_parts = unpack("a2day/a2mon/a4year",$worker[$synniaeg_tag]);
//			$bday = mktime(0,0,0,$bd_parts[1],$bd_parts[0],$bd_parts[2]);
			$bd_parts = unpack("a4year/a2mon/a2day",$worker[$synniaeg_tag]);
			$bday = mktime(0,0,0,$bd_parts["mon"],$bd_parts["day"],$bd_parts["year"]);
			
			$wrds_parts = unpack("a4year/a2mon/a2day",$worker[$asutusse_tulek_tag]);
			$work_relation_date_start = mktime(0, 0, 0, $wrds_parts["mon"], $wrds_parts["day"], $wrds_parts["year"]);

			print "tm = " . $bday . "<br>";
			;
			print "<b>Processing $person_name</b><br>";
			print "id = " . $person_obj->id() . "<br>";
			print html::href(array(
				"url" => $this->mk_my_orb("change",array("id" => $person_obj->id()),CL_CRM_PERSON),
				"caption" => t("Vaata"),
			));
			print "<br>";
			$person_obj->set_name($person_name);
			$person_obj->set_prop("firstname",$worker[$eesnimi_tag]);
			$person_obj->set_prop("lastname",$worker[$perekonnanimi_tag]);
			$person_obj->set_prop("ext_id",$ext_id);
			$person_obj->set_prop("ext_id_alphanumeric", $worker[$tootaja_id_tag]);
			$person_obj->set_prop("birthday",date("Y-m-d", $bday));
			$person_obj->set_ord($worker[$prioriteet_tag]);
			$person_obj->set_status(STAT_ACTIVE);

			if (!in_array($worker["AADRESS"],$addr))
			{
				print "creating address object<br>";
				print $worker["ADDRESS"];
				print "<br>";
				// parent?
			};

			// ametikoht_nimetus
			// eriala - aga see k&auml;ib vist haridusega kokku?

 			if (!empty($worker[$e_post_tag]))
			{
				// I need to replace e-mail address, not create a new one

				// at this point person_obj already exists, so I need to check
				// whether an e-mail address is connected
	
				if ($email_links[$person_obj->id()])
				{
					$ml = new object($email_links[$person_obj->id()]);
					print "Updating existing e-mail object<br>";
				}
				else
				{
					print "creating e-mail object<br>";
					print $worker[$e_post_tag];
					print "<br>";
					$ml = new object();
					$ml->set_parent($dir_default);
					$ml->set_class_id(CL_ML_MEMBER);
				};
	
				$ml->set_name($worker[$e_post_tag]);
				$ml->set_prop("mail",$worker[$e_post_tag]);
				aw_disable_acl();
				$ml->save();
				aw_restore_acl();
	
				$mid = $ml->id();
							
				$person_obj->connect(array(
					"to" => $mid,
					"reltype" => 11,
				));

				$person_obj->set_prop("email",$mid);
			};


			foreach($simple_attribs as $skey => $sdata)
			{
				if (empty($worker[$skey]))
				{
					continue;
				};
				
				
				$_name = $worker[$skey];
				$_name = iconv("UTF-8", "ISO-8859-4",$_name);
				
				if ($simple_data[$skey][$_name])
				{
					$tmp_o = new object($simple_data[$skey][$_name]);
					print "connecting to $skey object<br>";
				}
				else
				{
					$tmp_o = new object();
					$tmp_o->set_parent($dir_default);
					$tmp_o->set_class_id($sdata["clid"]);
					$tmp_o->set_status(STAT_ACTIVE);
					aw_disable_acl();
					$tmp_o->save();
					aw_restore_acl();
					print "creating and connecting to $skey object<br>";
				};

				$tmp_o->set_name($_name);

				print "name is $_name<br>";
				$tmp_id = $tmp_o->id();
				print "oid is " . $tmp_id . "<br>";
				print html::href(array(
					"url" => $this->mk_my_orb("change",array("id" => $tmp_id),$sdata['clid']),
					"caption" => t("Vaata"),
				));
				print "<br>";

				$simple_data[$skey][$_name] = $tmp_id;

				aw_disable_acl();
				$tmp_o->save();
				aw_restore_acl();

				$person_obj->connect(array(
					"to" => $tmp_id,
					"reltype" => $sdata["reltype"],
				));

				$person_obj->set_prop($sdata["prop"],$tmp_id);

				//print "name = " . $tmp_o->name();
			};
		
			// one person can have different types of phone numbers, each has it's own
			// tag in the XML. phone_type (defined above) contains all possible types
			// all existing phone numbers are stored in $phones as phone_number => obj_id 
			// pairs. Phone number is key for faster access
		
			// For each person cycle over all phone types, check whether the given number
			// exists (in $phones) - if so, then connect person to the phone number object,
			// if not, then create a new phone number object and connect.
			
			// it is not necessary to check whether the connection already exists,
			// storage basically ignores the connect() if this is the case
			$_pers_phones = array();
			foreach($phone_type as $pkey => $pval)
			{
				//if (!empty($worker[$pkey]) && !in_array($worker[$pkey],$phones))
				print "checking $pkey<br>";
				if (!empty($worker[$pkey]) && !$phones[$worker[$pkey]])
				{
					print "creating $pkey phone number object<br>";
					$this->_create_and_connect_phone(array(
						"person_obj" => $person_obj,
						"folder" => $dir_default,
						"type" => $pval,
						"phone" => $worker[$pkey],
					));
				};
				if ($this->can("view", $phones[$worker[$pkey]]))
				{
					$po = new object($phones[$worker[$pkey]]);
					if($po->prop("type") != $pval)
					{
						$po->set_prop("type",$pval);
						aw_disable_acl();
						$po->save();
						aw_restore_acl();
					}

					print "connecting to existing $pkey phone object " . $po->id() . "<br>";

					$person_obj->connect(array(
						"to" => $po->id(),
						"reltype" => 13 // phone 
					));
					
					
				};
				$_pers_phones[$worker[$pkey]] = $worker[$pkey];
			};

			if (is_oid($person_obj->id()))
			{
				// now, go over all the phones connected to the person and disconnect the ones that are not in persona
				foreach($person_obj->connections_from(array("type" => "RELTYPE_PHONE")) as $c)
				{
					if (!isset($_pers_phones[$c->prop("to.name")]))
					{
						$person_obj->disconnect(array("from" => $c->prop("to")));
					}
				}
			}

			print "phones connected<bR>";
			flush();

			aw_disable_acl();
			$person_obj->save();
			aw_restore_acl();

			/*
			// I accidentally generated quite a few of aliases. Need to get rid of 'em.
			$conns_alias = $person_obj->connections_from(array());
			foreach($conns_alias as $conn_alias)
			{
				$conn_alias_arr = $conn_alias->conn;
				if($conn_alias_arr["to.class_id"] == 487 && $conn_alias_arr["reltype"] != 23)
				{
					$conn_alias->delete(true);
				}
			}
			/**/

			if(!empty($worker[$haridustase_tag]))
			{
				$haridustase = iconv("UTF-8", "ISO-8859-4", $worker[$haridustase_tag]);
				// Whatta hack!
				$person_obj->set_prop("edulevel", str_replace("koorg", "korg", preg_replace("/[^a-zA-Z]/", "o", $haridustase)));
				aw_disable_acl();
				$person_obj->save();
				aw_restore_acl();
				print "Setting education level to ".$haridustase.".<br>";
				flush();
			}
			if(!empty($worker[$ametikoht_nimetus_tag]))
			{
				$ametikoht_nimetus = iconv("UTF-8", "ISO-8859-4", $worker[$ametikoht_nimetus_tag]);
				$asutus =  iconv("UTF-8", "ISO-8859-4", $worker[$asutus_tag]);
				if(empty($asutus))
					$asutus = $worker[$asutus_tag];
				$prevjob_done = false;

				if(!empty($asutus))
				{
					if(array_key_exists($asutus, $impd_objs_comp))
					{
						$company_obj = obj($impd_objs_comp[$asutus]);
					}
					else
					{
						print "creating company object ".$asutus."<br>";
						flush();
						$company_obj = new object;
						$company_obj->set_class_id(CL_CRM_COMPANY);
						$company_obj->set_parent($dir_company);
						$company_obj->set_prop("name", $asutus);
						aw_disable_acl();
						$company_obj->save();
						aw_restore_acl();
					}
					/*
					print "connecting section ".iconv("UTF-8", "ISO-8859-4", $worker[$yksus_tag])." to company object ".$asutus."<br>";
					$company_obj->connect(array(
						"to" => $sections[$worker[$yksus_id_tag]],
						"reltype" => "RELTYPE_SECTION",
					));
					/**/
					$company_id = $company_obj->id();
					$impd_objs_comp[$asutus] = $company_id;
				}

				unset($profession_id);
				foreach($person_obj->connections_from(array("type" => 7)) as $conn)
				{
					$rank = $conn->to();
//					print $rank->meta("external_id")." == ".$worker[$tootaja_id_tag]."<br>";
//					print $rank->name()." == ".$ametikoht_nimetus."<br>";
					if($rank->meta("external_id") == $worker[$tootaja_id_tag] && $rank->name() == $ametikoht_nimetus)
					{
						$profession_id = $rank->id();
						$person_obj->set_prop("rank", $profession_id);
						print "using existing crm_profession object ".$ametikoht_nimetus." ID - ".$profession_id.".<br>";
					}
					else
					{
						$conn->delete();
					}
				}
				if(!isset($profession_id))
				{					
					print "creating profession object ".$ametikoht_nimetus."<br>";
					$rank = new object;
					$rank->set_class_id(CL_CRM_PROFESSION);
					$rank->set_parent($dir_default);
					$rank->set_status(STAT_ACTIVE);
					$rank->set_prop("name", $ametikoht_nimetus);
					$rank->set_meta("external_id", $worker[$tootaja_id_tag]);
					aw_disable_acl();
					$rank->save();
					aw_restore_acl();
					$profession_id = $rank->id();
					print "connecting profession object<br>";
					$person_obj->connect(array(
						"to" => $rank->id(),
						"type" => 7,
					));
					$person_obj->set_prop("rank", $profession_id);
				}

				foreach($person_obj->connections_from(array("type" => "RELTYPE_PREVIOUS_JOB")) as $conn)
				{
					$ok_count = 0;

					$prevjob = $conn->to();

					foreach($prevjob->connections_from(array("type" => "RELTYPE_ORG")) as $conn_to_org)
					{
						$org_obj = $conn_to_org->to();						
						if($org_obj->name() == $asutus)
						{
							$ok_count++;
							break;
						}
					}
					foreach($prevjob->connections_from(array("type" => "RELTYPE_PROFESSION")) as $conn_to_profession)
					{
						$profession_obj = $conn_to_profession->to();
						if($profession_obj->id() == $profession_id)
						{
							$ok_count++;
							break;
						}
					}
					if($prevjob->prop("start") == $work_relation_date_start)
					{
						$ok_count++;
					}

					if($ok_count == 3)
					{
						$prevjob_done = true;
						break;
					}
				}

				if(!$prevjob_done)
				{
					print "creating work relation object ".$ametikoht_nimetus."<br>";
					flush();

					$prevjob = new object;
					$prevjob->set_parent($dir_default);
					$prevjob->set_class_id(CL_CRM_PERSON_WORK_RELATION);
					$prevjob->set_status(STAT_ACTIVE);
					$prevjob->set_prop("name", $ametikoht_nimetus);
					$prevjob->set_prop("start", $work_relation_date_start);
//					$prevjob->set_prop("end", );
					$prevjob->set_prop("org", $company_id);
					$prevjob->set_prop("profession", $profession_id);
					aw_disable_acl();
					$prevjob->save();
					aw_restore_acl();
					/*
					$prevjob->connect(array(
						"to" => ,
						"reltype" => "RELTYPE_SUBSTITUTE");
					*/

					print "connecting to work relation object<br>";
					flush();
					$person_obj->connect(array(
						"to" => $prevjob->id(),
						"reltype" => "RELTYPE_PREVIOUS_JOB",
					));
				}
			}

//echo __FILE__."::".__LINE__." <br>\n";flush();
			if($worker[$on_asendaja_tag] == 1 && !empty($worker[$asendamine_tookoht_tag]))
			{
				$asendamine_tookoht = iconv("UTF-8", "ISO-8859-4", $worker[$asendamine_tookoht_tag]);
				$subst_done = false;

				foreach($prevjob->connections_from(array("type" => "RELTYPE_SUBSTITUTE")) as $conn)
				{
					$substitute_obj = $conn->to();
//					print $substitute_obj->name()." == ".$asendamine_tookoht."<br>";
//					print $substitute_obj->meta("external_id")." == ".$worker[$tootaja_id_tag]."<br>";
					if($substitute_obj->name() == $asendamine_tookoht && $substitute_obj->meta("external_id") == $worker[$tootaja_id_tag])
					{
						print "connected to existing profession object ".$asendamine_tookoht."<br>";
						flush();
						$subst_done = true;
						break;
					}
					else
					{
						print "DEL<br>";
						$conn->delete();
					}
				}

				if(!$subst_done)
				{
					unset($substitute_id);

					foreach($subst_ol_arr as $subst_obj)
					{
//						print $subst_obj->meta("external_id")." == ".$worker[$tootaja_id_tag]."<br>";
//						print $subst_obj->name()." == ".$asendamine_tookoht."<br>";
						if($subst_obj->meta("external_id") == $worker[$tootaja_id_tag] && $subst_obj->name() == $asendamine_tookoht)
						{
							$substitute_id = $subst_obj->id();
							break;
						}
					}
					if(!isset($substitute_id))
					{
						print "creating profession object ".$asendamine_tookoht."<br>";
						$subst = new object;
						$subst->set_class_id(CL_CRM_PROFESSION);
						$subst->set_parent($dir_default);
						$subst->set_status(STAT_ACTIVE);
						$subst->set_prop("name", $asendamine_tookoht);
						$subst->set_meta("external_id", $worker[$tootaja_id_tag]);
						aw_disable_acl();
						$subst->save();
						aw_restore_acl();
						$substitute_id = $subst->id();
						$subst_ol_arr[$substitute_id] = $subst;
					}

					print "connecting to profession object ".$asendamine_tookoht."<br>";
					$prevjob->connect(array(
						"to" => $substitute_id,
						"reltype" => 4,		//RELTYPE_SUBSTITUTE
					));
				}
			}

			unset($ylem_ykid);
			if(!empty($worker[$allasutus_tag]))
			{
				$worker[$allasutus_tag] = iconv("UTF-8", "ISO-8859-4", $worker[$allasutus_tag]);

				if(is_oid($sections_byname[$worker[$allasutus_tag]]))
				{
					print "using existing section ".$worker[$allasutus_tag]."<br>";
					$ylem_yksus = new object($sections_byname[$worker[$allasutus_tag]]);
					$ylem_ykid = $sections_byname[$worker[$allasutus_tag]];
				}
				else
				{
					print "creating new section ".$worker[$allasutus_tag]."<br>";
					$ylem_yksus = new object;
					$ylem_yksus->set_class_id(CL_CRM_SECTION);
					$ylem_yksus->set_parent($dir_default);
					$ylem_yksus->set_name($worker[$allasutus_tag]);
					aw_disable_acl();
					$ylem_yksus->save();
					aw_restore_acl();
					$ylem_ykid = $ylem_yksus->id();
					$sections_byname[$worker[$allasutus_tag]] = $ylem_ykid;
				}
				$company_obj->connect(array(
					"to" => $ylem_ykid,
					"reltype" => 28, //RELTYPE_SECTION,
				));
				print "connected section ".$worker[$allasutus_tag]." to company ".$worker[$asutus_tag]."<br>";
				
				/*
				if($worker[$allasutus_tag] == iconv("UTF-8", "ISO-8859-4", $worker[$yksus_tag]))
				{
					$ylem_yksus->set_prop("ext_id", $worker[$yksus_id_tag]);
					$ylem_yksus->set_subclass($worker[$yksus_id_tag]);
					$ylem_yksus->save();
					$sections[$worker[$yksus_id_tag]] = $ylem_ykid;
				}
				/**/
			}

//			if (!empty($worker[$yksus_id_tag]) || !empty($worker[$yksus_tag]))
			if (!empty($worker[$yksus_tag]))
			{
				$worker[$yksus_tag] = iconv("UTF-8", "ISO-8859-4", $worker[$yksus_tag]);

				/*
				if(is_oid($sections[$worker[$yksus_id_tag]]))
				{
					print "connecting to section ".$worker[$yksus_tag]." (using ID) - ".$sections[$worker[$yksus_id_tag]]."<br>";
					$person_obj->connect(array(
						"to" => $sections[$worker[$yksus_id_tag]],
						"reltype" => 21, //RELTYPE_SECTION,
					));

					$person_obj->set_prop("org_section",$sections[$worker[$yksus_id_tag]]);
					print "sect connect done<br>";
				}
				else
				/**/	
				if(is_oid($sections_byname[$worker[$yksus_tag]]))
				{
					print "connecting to section ".$worker[$yksus_tag]." (using name) - ".$sections_byname[$worker[$yksus_tag]]."<br>";
					$person_obj->connect(array(
						"to" => $sections_byname[$worker[$yksus_tag]],
						"reltype" => 21, //RELTYPE_SECTION,
					));

					$person_obj->set_prop("org_section", array($sections_byname[$worker[$yksus_tag]]));
					print "sect connect done<br>";
				}
				else
				{					
					// create yksus
					print "creating new section ".$worker[$yksus_tag]."<br>";
					$yk = new object();
					$yk->set_parent($dir_default);
					$yk->set_class_id(CL_CRM_SECTION);
					$yk->set_prop("ext_id", $worker[$yksus_id_tag]);
					$yk->set_subclass($worker[$yksus_id_tag]);
					$yk->set_name($worker[$yksus_tag]);
					aw_disable_acl();
					$yk->save();
					aw_restore_acl();

					$ykid = $yk->id();
					$sections[$worker[$yksus_id_tag]] = $ykid;
					$sections_byname[$worker[$yksus_tag]] = $ykid;

					print "connectiong to section ".$worker[$yksus_tag]."<br>";
					$person_obj->connect(array(
						"to" => $ykid,
						"reltype" => 21, //RELTYPE_SECTION,
					));

					$person_obj->set_prop("org_section",$ykid);
					print "sect connect done<br>";
				}
				if(is_oid($company_id))
				{
					$person_obj->set_prop("work_contact", $company_id);
					print "company connect done<br>";
				}
				if($worker[$yksus_tag] != iconv("UTF-8", "ISO-8859-4", $worker[$allasutus_tag]) && isset($ylem_ykid) && !empty($worker[$yksus_id_tag]) && $ylem_yksus->id() != $sections[$worker[$yksus_id_tag]])
				{
					if($this->can("view", $sections[$worker[$yksus_id_tag]]))
					{
						$ylem_yksus->connect(array(
							"to" => $sections[$worker[$yksus_id_tag]],
							"reltype" => 1,		//RELTYPE_SECTION
						));
					}
					else
					/*
					if($this->can("view", $sections_byname[$worker[$yksus_tag]]) && $ylem_yksus->id() != $sections_byname[$worker[$yksus_tag]])
					{
						$ylem_yksus->connect(array(
							"to" => $sections_byname[$worker[$yksus_tag]],
							"reltype" => 1,		//RELTYPE_SECTION
						));
					}
					$doomed_conns = $ylem_yksus->connections_from(array(
						"type" => "RELTYPE_SECTION",
					));
					/**/
					foreach($doomed_conns as $doomed_conn)
					{
						$doomed_conn_to = $doomed_conn->to();
						if($doomed_conn_to->id() == $ylem_yksus->id())
						{
							$doomed_conn->delete(true);						
						}
					}
					$doomed_conns = $company_obj->connections_from(array(
						"type" => "RELTYPE_SECTION",
					));
					foreach($doomed_conns as $doomed_conn)
					{
						$doomed_conn_to = $doomed_conn->to();
						if($doomed_conn_to->id() == $sections[$worker[$yksus_id_tag]] || $doomed_conn_to->id() == $sections_byname[$worker[$yksus_tag]])
						{
							$doomed_conn->delete(true);						
						}
					}
					print "section ".$worker[$yksus_tag]." connected to parent section ".$worker[$allasutus_tag]."<br>";
				}
			}

			$ametikirjeldus_viit = iconv("UTF-8", "ISO-8859-4", $worker[$ametikirjeldus_viit_tag]);
			print "setting 'Viit ametijuhendile' property for crm_profession object ".$ametikoht_nimetus." ID - ".$rank->id()."<br>";
			print $ametikirjeldus_viit."<br>";
			$rank->set_prop("directive_link", $ametikirjeldus_viit);
			aw_disable_acl();
			$rank->save();
			aw_restore_acl();
			/*
			print "setting 'Viit ametijuhendile' property for work relation object ".$prevjob->name()."<br>";
//				$ametijuhend_viit = iconv("UTF-8", "ISO-8859-4", $worker["AMETIJUHEND_VIIT"]);
//				$prevjob->set_prop("directive_link", $ametijuhend_viit);
			$prevjob->set_prop("directive_link", $ametikirjeldus_viit);
			$prevjob->save();
			*/

			// let us keep track of all existing workers, so I can properly assign vacations and contract_stops
			$persons[$ext_id] = $person_obj->id();

			aw_disable_acl();
			$person_obj->save();
			aw_restore_acl();
			print "person done<br><br>";
			flush();

			//arr($worker);
			$persona_persons_done[$ext_id] = $ext_id;
			// mul on vaja seda folderi id, mille alla t&ouml;&ouml;taja objekte teha
	
			// 1 means done
			$persona_to_process[$ext_id] = 1;
			$batchcounter++;

			if ($batchcounter >= $persons_per_batch)
			{
				print "<h2>Batch completed, writing status</h2>";
				$this->put_file(array(
					"file" => $stat_file,
					"content" => aw_serialize($persona_to_process),
				));
				$batchcounter = 0;
				//print "aitab kah";
				//exit;

				/*
				if ($aw_timer->get("personimport") >= $time_limit)
				{
					print "Getting too close to time limit, restarting import from beginning<br>";
					$request_uri = aw_ini_get("baseurl") . "/" . aw_global_get("REQUEST_URI");
					
					// This one doesn't seem to work.
//					header("Location: " . $request_uri);
//					exit;
					
					// Therefore using this instead					
					print "<head><meta http-equiv=\"REFRESH\" content=\"1;url=".$request_uri."\"></head>";
					exit;
				};
				*/
			};


			// ja siia siis .. kui on m&ouml;&ouml;das rohkem kui XX &uuml;hikut, siis die ja 
			// header("Location: self");

		};

		print "deleting non-existing persons<br>";
		print "<pre>";
		var_dump($person_match);
		print "</pre>";
		flush();

		// siia j&auml;&auml;vad ainult j&auml;&auml;gid, need m&auml;rgime deaktiivseks ja ongi k&otilde;ik
		foreach($person_match as $ext_id => $obj_id)
		{
			$person_obj = new object($obj_id);
			$person_obj->delete();
			#$person_obj->set_status(STAT_NOTACTIVE);
			#$person_obj->save();
		};

		if(!$this->can("view" , $import_id))
		{
			$this->import_images($arr);		
		}
	
		print "<h1>T&Ouml;&Ouml;TAJAD done</h1>";

		print "teeme peatuste ja puhkuste objektid<br><br>";

		// There was a problem if the import was interrupted. The persons array was incomplete.
		foreach($person_match_ as $person_match_element => $person_match_value)
		{
			$persons[$person_match_element] = $person_match_value;
		}

		// lisaks on vaja siin tekitada mingid muutujad. Selleks on vaja muutujate haldurit ..
		// ja seal omakorda on mul tarvis mingid oksad eraldada
		$mx = new object_list(array(
			"class_id" => CL_META,
			"parent" => $meta_cat["peatumised"],
		));

		$mxlist = array_flip($mx->names());
//		arr($mxlist);

		// ei, aga p&otilde;him&otilde;tteliselt, kui t&ouml;&ouml;tajal juba on puhkus v&otilde;i peatumine, siis teist me ei tee

		// aga n&uuml;&uuml;d .. ma tahan person klassile lisada meetodid 
		// add_or_update_vacation
		// add_or_update_contract_stop

		foreach($data[$toosuhte_peatumised_tag] as $peatumine)
		{
			$peatumise_liik = iconv("UTF-8", "ISO-8859-4", $peatumine[$peatumise_liik_tag]);

			$a = $this->timestamp_from_xml($peatumine[$alguskuupaev_tag]);
			$b = $this->timestamp_from_xml($peatumine[$loppkuupaev_tag]);
			if(!is_oid($persons[$peatumine[$ewr_tootaja_id_tag]]))
			{
				print "<br>No worker with ID ".$persons[$peatumine[$ewr_tootaja_id_tag]].", original ID ".$peatumine[$ewr_tootaja_id_tag].". Ignoring contract stop.<br>";
				flush();
				continue;
			}
			$t = new object($persons[$peatumine[$ewr_tootaja_id_tag]]);

			print "<br><b>Person: ".$t->name()."</b><br>";
			
			$stop_done = false;
			foreach($t->connections_from(array("type" => "RELTYPE_PREVIOUS_JOB")) as $conn_t)
			{
				$t2 = $conn_t->to();
				if(!$stop_done)
				{
					foreach($t2->connections_from(array("type" => "RELTYPE_CONTRACT_STOP")) as $conn_t2)
					{
						$t3 = $conn_t2->to();
						if(is_oid($t3->prop("type")))
						{
							$t33 = obj($t3->prop("type"));
							if($t33->name() == $peatumise_liik && $t3->prop("start1") == $a)
							{
								$stop = $t3;
								$stop->set_prop("end", $b);
								aw_disable_acl();
								$stop->save();
								aw_restore_acl();
								print "connected to existing contract stop object ".$stop->name()."<br>";
								flush();
								$stop_done = true;
								//break;
							}
						}
						// Somewhy the $mxlist array was empty. Anomaly.
						/*
						if($t3->prop("type") == $mxlist[$peatumise_liik] && $t3->prop("start1") == $a)
						{
							$stop = $t3;
							$stop->set_prop("end", $b);
							$stop->save();
							print "connected to existing contract stop object ".$stop->name()."<br>";
							flush();
							$stop_done = true;
							//break;
						}
						/**/
					}
				}
			}

			if(!$stop_done)
			{
				print "creating new contract stop object ".$peatumise_liik."<br>";

				if ($mxlist[$peatumise_liik])
				{
					$xo = new object($mxlist[$peatumise_liik]);
					print "using existing peatumine ".$xo->id()."<br>";
				}
				else
				{
					print "creating new PEATUMISE LIIK variable: ".$peatumise_liik."<br>";
					$xo = new object();
					$xo->set_parent($meta_cat["peatumised"]);
					$xo->set_class_id(CL_META);
					$xo->set_status(STAT_ACTIVE);
					$xo->set_name($peatumise_liik);
					aw_disable_acl();
					$xo->save();
					aw_restore_acl();
					$mxlist[$peatumise_liik] = $xo->id();
				};

				$stop = new object();
				$stop->set_class_id(CL_CRM_CONTRACT_STOP);
				$stop->set_parent($dir_default);
				$stop->set_status(STAT_ACTIVE);
				$stop->set_prop("start1",$a);
				if($b > 0)
				{
					$stop->set_prop("end",$b);	
				}
				$stop->set_prop("type", $xo->id());
				//$stop->set_name($t->name());
				$stop->set_name($peatumise_liik);
				aw_disable_acl();
				$stop->save();
				aw_restore_acl();
				

				// I was told to connect these to 'T&ouml;&ouml;suhe' object instead
				/*
				$t->connect(array(
					"to" => $stop->id(),
					"reltype" => 42, //RELTYPE_CONTRACT_STOP,
				));
				*/
				// So here I go (again on my own. Goin' down the only road I've ever known...) Mkay. Back to work.
				foreach($t->connections_from(array("type" => "RELTYPE_PREVIOUS_JOB")) as $conn)
				{
					$t2 = $conn->to();
					print "connecting ".$t->name()." -> ".$t2->name()." to new contract stop object ".$stop->name()."<br>";
					$t2->connect(array(
						"to" => $stop->id(),
						"reltype" => 6, //RELTYPE_CONTRACT_STOP,
					));
				}

				print "name = " . $t->name() . "<br>";
				print "from $a to $b<br>";
			}

			if(!empty($peatumine[$asendaja_id_tag]))
			{
				print "Setting substitute...<br>";
				if(!is_oid($persons[$peatumine[$asendaja_id_tag]]))
				{
					print "No person with ID ".$persons[$peatumine[$asendaja_id_tag]].", original ID ".$peatumine[$asendaja_id_tag].". Ignoring.<br>";
					flush();
				}
				else
				{
					$subst_pers = new object($persons[$peatumine[$asendaja_id_tag]]);
					print "name = ".$subst_pers->name()."<br>";

					$stop->connect(array(
						"to" => $persons[$peatumine[$asendaja_id_tag]],
						"reltype" => 2,		//RELTYPE_SUBSTITUTE
					));
					print "Connected.<br>";
				}
			}
			/**/

			// alright, so far, so good .. I have person object, now I need to create
			// vacation object. and somehow I need to determine whether this person
			// already has an entered vacation.

			// for this I need to check start and end I guess .. and worker_id. really 
			// no other way to do it

			// also, whatever should I do with the variable manager?
		};

		print "<h1>T&Ouml;&Ouml;SUHTE PEATUMISED done!</h1>";

		$degree = array(
			"pohiharidus" => "Pohiharidus",
			"keskharidus" => "Keskharidus",
			"keskeriharidus" => "Kesk-eriharidus",
			"diplom" => "Diplom",
			"bakalaureus" => "Bakalaureus",
			"magister" => "Magister",
			"doktor" => "Doktor",
			"teadustekandidaat" => "Teaduste kandidaat",
		);
		$degree = array_flip($degree);
	
		foreach($data[$hariduskaigud_tag] as $hariduskaik)
		{
			$hariduskaik[$akadeemiline_kraad_tag] = iconv("UTF-8", "ISO-8859-4", $hariduskaik[$akadeemiline_kraad_tag]);
			$oppeasutus = iconv("UTF-8", "ISO-8859-4", $hariduskaik[$oppeasutus_tag]);
			if(empty($oppeasutus))
				$oppeasutus = $hariduskaik[$oppeasutus_tag];
//			arr($hariduskaik);
			if(empty($hariduskaik[$edu_tootaja_id_tag]))
			{
				print "No person ID specified. Ignoring.<br>";
				flush();
				continue;
			}
			if(!is_oid($persons[$hariduskaik[$edu_tootaja_id_tag]]))
			{
				print "No person with ID ".$persons[$hariduskaik[$edu_tootaja_id_tag]].", original ID ".$hariduskaik[$edu_tootaja_id_tag].". Ignoring.<br>";
				flush();
				continue;
			}
			$t = new object($persons[$hariduskaik[$edu_tootaja_id_tag]]);
			print "<b>Person: ".$t->name()."</b><br>";

			$edu_done = false;
			foreach($t->connections_from(array("type" => "RELTYPE_EDUCATION")) as $edu_conn)
			{
				if($haridus_conns[$t->id()][$edu_conn->id()] != 2 || !isset($haridus_conns[$t->id()][$edu_conn->id()]))
				{
					$haridus_conns[$t->id()][$edu_conn->id()] = 1;
				}
				$education = $edu_conn->to();
				$end_date = $education->prop("end_date");
				
				if($education->prop("name") == $oppeasutus && ($t->prop("edulevel") == "keskharidus" || $education->prop("speciality") == iconv("UTF-8", "ISO-8859-4", $hariduskaik[$eriala_tag])) && (empty($end_date) || //$education->prop("end_date") == $this->timestamp_from_xml($hariduskaik["DIPLOM_KP_LOPETAMINE"], 1)))
				$education->prop("end_date") == $this->timestamp_from_xml($hariduskaik[$diplom_kuupaev_tag])))
				{
					$haridus_conns[$t->id()][$edu_conn->id()] = 2;
					print "connected to existing education object ".$education->name()."<br>";
					if($t->prop("edulevel") != "keskharidus")
					{
						$education->set_prop("speciality", iconv("UTF-8", "ISO-8859-4", $hariduskaik[$eriala_tag]));
					}
					else
					{
						$education->set_prop("speciality", "");
					}
					$education->set_prop("main_speciality", $hariduskaik[$on_pohieriala_tag]);					
					$education->set_prop("in_progress", $hariduskaik[$on_opilane_tag]);
					$education->set_prop("diploma_nr", $hariduskaik[$diplom_number_tag]);
					// P&otilde;hiharidus might cause some drama. We wanna avoid that.
					$education->set_prop("degree", $degree[str_replace("koorg", "korg", preg_replace("/[^a-zA-Z]/", "o", $hariduskaik[$akadeemiline_kraad_tag]))]);
					$education->set_prop("obtain_language", $hariduskaik[$keel_tag]);
//					if(!empty($hariduskaik["DIPLOM_KP_LOPETAMINE"]))
					if(!empty($hariduskaik[$diplom_kuupaev_tag]))
					{
//						$education->set_prop("end_date", $this->timestamp_from_xml($hariduskaik["DIPLOM_KP_LOPETAMINE"], 1));
						$education->set_prop("end_date", $this->timestamp_from_xml($hariduskaik[$diplom_kuupaev_tag]));
						// Do ya think I should import the same value here as well?
//						$education->set_prop("end", $this->timestamp_from_xml($hariduskaik["DIPLOM_KP_LOPETAMINE"], 1));
					}
					aw_disable_acl();
					$education->save();
					aw_restore_acl();

					$edu_done = true;
//					break;
				}
			}
			
			if(!$edu_done)
			{
				print "creating new education object ".$oppeasutus."<br>";
				$education = new object;
				$education->set_class_id(CL_CRM_PERSON_EDUCATION);
				$education->set_parent($dir_default);
				$education->set_prop("name", $oppeasutus);
				if($t->prop("edulevel") != "keskharidus")
				{
					$education->set_prop("speciality", iconv("UTF-8", "ISO-8859-4", $hariduskaik[$eriala_tag]));
				}
				$education->set_prop("main_speciality", $hariduskaik[$on_pohieriala_tag]);
				$education->set_prop("in_progress", $hariduskaik[$on_opilane_tag]);
				$education->set_prop("diploma_nr", $hariduskaik[$diplom_number_tag]);
				// P&otilde;hiharidus might cause some drama. We wanna avoid that.
				$education->set_prop("degree", $degree[str_replace("koorg", "korg", preg_replace("/[^a-zA-Z]/", "o", $hariduskaik[$akadeemiline_kraad_tag]))]);
				$education->set_prop("obtain_language", iconv("UTF-8", "ISO-8859-4", $hariduskaik[$keel_tag]));
//				if(!empty($hariduskaik["DIPLOM_KP_LOPETAMINE"]))
				if(!empty($hariduskaik[$diplom_kuupaev_tag]))
				{
//					$education->set_prop("end_date", $this->timestamp_from_xml($hariduskaik["DIPLOM_KP_LOPETAMINE"], 1));
					$education->set_prop("end_date", $this->timestamp_from_xml($hariduskaik[$diplom_kuupaev_tag]));
					// Do ya think I should import the same value here as well?
//					$education->set_prop("end", $this->timestamp_from_xml($hariduskaik["DIPLOM_KP_LOPETAMINE"], 1));
				}
				aw_disable_acl();
				$education->save();
				aw_restore_acl();

				print "connecting ".$t->name()." to new education object ".$oppeasutus."<br>";
				/*
				$t->connect(array(
					"to" => $education->id(),
					"reltype" => 23,		//RELTYPE_EDUCATION
				));
				*/
				$c = new connection();
				$c->change(array(
					"from" => $t->id(),
					"to" => $education->id(),
					"reltype" => 23,		//RELTYPE_EDUCATION
				));
				$haridus_conns[$t->id()][$c->prop("id")] = 2;
			}
		}

		print "<br>deleting non-existing connections<br>";
		flush();

		foreach($haridus_conns as $hc_t => $hc_a)
		{
			foreach($hc_a as $hc_id => $hc_val)
			{
				if($hc_val == 1)
				{
					print "TOOTAJA_ID: ".$hc_t."<br>";
					print "CONNECTION: ".$hc_id."<br>";
					$hc_doomed_conn = new connection($hc_id);
					$hc_doomed_conn->delete(true);
				}
			}
		}

		print "<h1>HARIDUSK&Auml;IGUD done</h1>";

		print "Unlinking status file<br>";
		unlink($stat_file);
	
		aw_session_del("persona_import_started");
		aw_session_del("persona_persons_done");
		
		print "flushing cache<br>";
		flush();
		$cache = get_instance("cache");
		$cache->full_flush();

		print "everything is done<br>";

		exit;

		$mx = new object_list(array(
			"parent" => $meta_cat["puhkused"],
			"class_id" => CL_META,
		));

		$mxlist = array_flip($mx->names());
		foreach($data["PUHKUSED"] as $puhkus)
		{
			print "pu = ";
			arr($puhkus);
			$a = $this->timestamp_from_xml($puhkus["ALGUS"]);
			$b = $this->timestamp_from_xml($puhkus["LOPP"]);
			$t = new object($persons[$puhkus["TOOTAJA_ID"]]);
			$stop = new object();
			$stop->set_class_id(CL_CRM_VACATION);
			$stop->set_parent($meta_cat["puhkused"]);
			$stop->set_name($puhkus["PUHKUSE_LIIK"]);
			//$stop->set_name($t->name());
			$stop->set_prop("start1",$a);
			$stop->set_prop("end",$b);
			$stop->set_status(STAT_ACTIVE);
			aw_disable_acl();
			$stop->save();
			aw_restore_acl();

			$t->connect(array(
				"to" => $stop->id(),
				"reltype" => 41, //RELTYPE_VACATION,
			));
			
			if ($mxlist[$puhkus["PUHKUSE_LIIK"]])
			{
				$xo = new object($mxlist[$puhkus["PUHKUSE_LIIK"]]);
			}
			else
			{
				$xo = new object();
				$xo->set_parent($meta_cat["puhkused"]);
				$xo->set_class_id(CL_META);
				$xo->set_status(STAT_ACTIVE);
				$xo->set_name($puhkus["PUHKUSE_LIIK"]);
				aw_disable_acl();
				$xo->save();
				aw_restore_acl();
				$mxlist[$puhkus["PUHKUSE_LIIK"]] = $xo->id();
			};

			$stop->connect(array(
				"to" => $xo->id(),
				"reltype" => 1,
			));
			print "name = " . $t->name() . " ";
			print "from $a to $b<br>";
		};

		print "finished<br>";
		
		


		// puhkusi ning ka peatumisi n&auml;idatakse eraldi tabelis
		// ainus asi mis neid eristab on t&ouml;&ouml;taja ID. sama peatumise kohta. Therefore I have no way in hell
		// of deleting old vacations .. it simply is not going to happen

		/* puhkus
		<rida>
			<tootaja_id>190</tootaja_id>
			<puhkuse_liik>p&otilde;hipuhkus</puhkuse_liik>
			<algus>20041222T00:00:00</algus>
			<lopp>20041229T00:00:00</lopp>
			<kestus>6</kestus>
		</rida>
		*/

		/* peatumine
			<rida>
				<tootaja_id>67</tootaja_id> 
				<liik>lapsehoolduspuhkus</liik>
				<algus>20010806T00:00:00</algus>
				<lopp>20060906T00:00:00</lopp>
			</rida>

		*/


		print "all done";



		/*
			<taitmata_ametikohad>
			<rida>
			   <nimetus>looduskaitse peaspetsialist</nimetus>
			   <kood>2470</kood>
			   <yksus_id>6</yksus_id>
			   <prioriteet></prioriteet>
			  </rida>
			</taitmata_ametikohad>

			<yksused>
			  <rida>
			   <yksus_id>1</yksus_id>
			   <ylemyksus_id></ylemyksus_id>
			   <nimetus>Keskkonnaministeerium</nimetus>
			   <pohimaarus_viit></pohimaarus_viit>
			   <aadress>Toompuiestee 24, 15172 Tallinn</aadress>
			  </rida>

			<puhkused>
			  <rida>
			   <tootaja_id>190</tootaja_id>
			   <puhkuse_liik>p&otilde;hipuhkus</puhkuse_liik>
			   <algus>20041222T00:00:00</algus>
			   <lopp>20041229T00:00:00</lopp>
			   <kestus>6</kestus>
			  </rida>

			<peatumised>
			  <rida>
			   <tootaja_id>67</tootaja_id>
			   <liik>lapsehoolduspuhkus</liik>
			   <algus>20010806T00:00:00</algus>
			   <lopp>20060906T00:00:00</lopp>
			  </rida>


		


		*/
	}

	/**
		@attrib name=import_images
		@param id required type=int
	**/
	function import_images($arr)
	{
		
		$obj = new object($arr["id"]);
		
		$config = $this->get_config($obj);

		if (sizeof($config["ftp"]) == 0)
		{
			die(t("You forgot to enter server data"));
		};

		aw_set_exec_time(AW_LONG_PROCESS);	

		$c = get_instance(CL_FTP_LOGIN);


		//$config["ftp"]["host"] = "ftp.envir.ee";

		$c->connect($config["ftp"]);

		$p = xml_parser_create();

		$fqdn = $obj->prop("xml_folder") . "/";
		$files = $c->dir_list($fqdn);

		$persons = new object_list(array(
			"class_id" => CL_CRM_PERSON,
			"status" => STAT_ACTIVE,
		));	
		$px = array();
		foreach($persons->arr() as $person_obj)
		{
			//$px[$person_obj->prop("ext_id")] = $person_obj->id();
			$px[$person_obj->subclass()] = $person_obj->id();
			
		};

		$rpx = array_flip($px);

		// isiku puhul on tegemist lihtsalt esimese vastavat t&uuml;&uuml;pi seosega pildiobjektiga

		$cx = new connection();
		$existing = $cx->find(array(
			"from.class_id" => CL_CRM_PERSON,
			"to.class_id" => CL_IMAGE,
			"type" => 3 // RELTYPE_PICTURE, ehk pilt
		));

		$existing_images = array();
		foreach($existing as $conn)
		{
			//$existing_images[$conn["from"]] = $conn["to"];
			$existing_images[$rpx[$conn["from"]]] = $conn["to"];
		};

		//arr($px);

		$ti = get_instance(CL_IMAGE);
		$base_len = strlen(aw_ini_get("baseurl"));



		foreach($files as $file)
		{
			// XXX: implement proper detection of image files
			//if (strpos($file,"intranet_pilt"))
			if (strpos($file,"pildid.xml"))
			{
				print "retrieving and parsing $file<br>";
				$fdat = $c->get_file($file);
				if(strlen($fdat) == $o->meta("XML_file_size_".$file))
				{
					print "XML file size same as last time. Skipping.";
					continue;
				}
				$o->set_meta("XML_file_size_".$file, strlen($fdat));
				$p = xml_parser_create();
				xml_parse_into_struct($p, $fdat, $vals, $index);
				xml_parser_free($p);
				$tootaja_id = false;
				foreach($vals as $tag)
				{
					if ($tootaja_id && $tag["tag"] == "PILT" && $tag["type"] == "complete")
					{
						$pilt_data = base64_decode($tag["value"]);
						$tootaja_oid = $px[$tootaja_id];
						//print $existing_images[$tootaja_id];
						if (!is_oid($tootaja_oid))
						{
							print "No worker with id $tootaja_id, ignoring image<br>";
							continue;
						};
						print "assigning " . strlen($pilt_data) . " bytes to $tootaja_id / $tootaja_oid<br>";
						print "ix = ";
						$t = new object($tootaja_oid);
						if (is_oid($config["image_folder"]))
						{
							$image_folder = $config["image_folder"];
						}
						else
						{
							$image_folder = $t->id();
						};
						$pilt_name = $t->name() . ".jpg";
						if (empty($existing_images[$tootaja_id]))
						{
							print "creating image<br>";

							$emb = array();
							$emb["group"] = "general";
							$emb["parent"] = $image_folder;
							$emb["return"] = "id";
							$emb["cb_existing_props_only"] = 1;
							$emb["file"] = array(
								"name" => $pilt_name,
								"contents" => $pilt_data,
								"type" => "image/jpg",
							);
							
							aw_disable_acl();
							$timg = $ti->submit($emb);
							aw_restore_acl();

							$t->connect(array(
								"to" => $timg,
								"reltype" => 3,
							));

							$t->set_prop("picture",$timg);
							aw_disable_acl();
							$t->save();
							aw_restore_acl();
						}
						else
						{
							// change the parent
							$img_o = new object($existing_images[$tootaja_id]);
							if ($img_o->parent() != $image_folder)
							{
								print "changing parent<br>";
								$img_o->set_parent($image_folder);
								aw_disable_acl();
								$img_o->save();
								aw_restore_acl();
							};
							if ($t->prop("picture") != $img_o->id())
							{
								$t->set_prop("picture",$img_o->id());
								print "setting pic property<br>";
								aw_disable_acl();
								$t->save();
								aw_restore_acl();
							};
							$url = substr($ti->get_url($img_o->prop("file")),$baselen);
							print "url = $url<br>";

							print "updating parent of ";
							arr($img_o->properties());
							print "<bR>";
							print "imf = " . $config["image_folder"];
							print "<br>";
						}
						
						// re-save image from $pilt_data 
						$img_o = obj($t->prop("picture"));
						$fn = $img_o->prop("file");
						if (!is_writable($fn))
						{
							classload("image");
							$fn = image::_mk_fn(gen_uniq_id()).".jpg";
							$img_o->set_prop("file", $fn);
							$img_o->save();
						}
						if(!empty($fn) && !empty($pilt_data))
						{
							$this->put_file(array(
								"file" => $fn,
								"content" => $pilt_data
							));
						}
						else
						{
							print "put_file failed. File name is empty.<br>";
						}
						$ti->do_apply_gal_conf($img_o);

						// n
						print $t->name();
						print "<br>";
						$tootaja_id = false;
					};

					if ($tag["tag"] == "TOOTAJA_ID" && $tag["type"] == "complete")
					{
						$tootaja_id = $tag["value"];
						print "tootaja_id = $tootaja_id<br>";
						$pxo = new object($px[$tootaja_id]);
						print $pxo->name() . "<br>";
					};
				};
			};
		};

		$c->disconnect();

	}

	function timestamp_from_xml($xml_stamp, $type = 0)
	{
		switch ($type)
		{
			case 0: 
				// 20060906T00:00:00
				$p = unpack("a4year/a2mon/a2day/c1e/a2hour/a2min/a2sec",$xml_stamp);
				return mktime($p["hour"],$p["min"],$p["sec"],$p["mon"],$p["day"],$p["year"]);
				break;
			case 1: 
				// 23.06.1966 OR 1989
				$p = explode(".", $xml_stamp);
				$mon = (sizeof($p) > 1) ? $p[1] : 1;
				$day = (sizeof($p) > 1) ? $p[0] : 1;
				$year = (sizeof($p) > 1) ? $p[2] : $p[0];
				return mktime(0, 0, 0, $mon, $day, $year);
				break;
			default:
				// 20060906T00:00:00
				$p = unpack("a4year/a2mon/a2day/c1e/a2hour/a2min/a2sec",$xml_stamp);
				return mktime($p["hour"],$p["min"],$p["sec"],$p["mon"],$p["day"],$p["year"]); 
				break;
		}
	}

	function _create_and_connect_phone($arr)
	{
		$ml = new object();
		$ml->set_parent($arr["folder"]);
		$ml->set_class_id(CL_CRM_PHONE);
		$ml->set_name($arr["phone"]);
		$ml->set_prop("type",$arr["type"]);
		// Work phone is public by default
		if($arr["type"] == "work")
		{
			$ml->set_prop("is_public", 1);
		}
		aw_disable_acl();
		$ml->save();
		aw_restore_acl();

		$mid = $ml->id();

		$arr["person_obj"]->connect(array(
			"to" => $mid,
			"reltype" => 13,
		));

		if (!is_oid($arr["person_obj"]->prop("phone")))
		{
			$arr["person_obj"]->set_prop("phone",$mid);
		};

	}

	function _create_and_connect_attrib($arr)
	{



	}

	function get_xml_data($obj, $print = true)
	{		
		$import_id = $obj->prop("xml_link");

		if($this->can("view" , $import_id))
		{
			$import_obj = get_instance(CL_TAAVI_IMPORT);
			$fdat = $import_obj->export_xml($import_id);
		}
		
		else
		{
			$config = $this->get_config($obj);
			
			$c = get_instance(CL_FTP_LOGIN);
			$c->connect($config["ftp"]);

			$fs = array("xml_filename", "xml_personnel_file", "xml_education_file", "xml_work_relations_ending_file");

			$fdat = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
			$fdat .= "<XML_DATA>\n";

			foreach($fs as $f)
			{				
				$fqfn = $obj->prop("xml_folder") . "/" . $obj->prop($f);
				// THESE SHOULD BE DONE WITH preg_replace()
				$fdat .= str_replace("<?xml version=\"1.0\" encoding=\"UTF-8\"?>", "", $c->get_file($fqfn));
				if($print)
				{
					print "<h5>" . $fqfn . "</h5>";
				}
			}

			$fdat .= "</XML_DATA>\n";

			$c->disconnect();
		}
		return $fdat;
	}

	function check_in_progress()
	{
		// In order to avoid persona import being triggered from both intranet.envir.ee and www.envir.ee
		$config_inst = get_instance("config");
		$inp = $config_inst->get_simple_config("persona_import_in_progress");
		$inp_url = $config_inst->get_simple_config("persona_import_in_progress_url");
		if((time() - $inp) < 3600 * 10 && $inp_url != aw_ini_get("baseurl"))
		{
			die("already in progress with url ".$inp_url);
		}
		$config_inst->set_simple_config("persona_import_in_progress", time());
		$config_inst->set_simple_config("persona_import_in_progress_url", aw_ini_get("baseurl"));
	}

	function callback_post_save($arr)
	{
		$o = $arr["obj_inst"];
		$conns = $o->connections_from(array(
			"type" => "RELTYPE_RECURRENCE",
		));
		// iga asja kohta on vaja teada seda, et millal ta v&auml;lja kutsutakse
		$sch = get_instance("scheduler");
		foreach($conns as $conn)
		{
			$rep_id = $conn->prop("to");
			$event_url = $this->mk_my_orb("invoke",array("id" => $o->id()));
			// lisab iga &uuml;hendatud recurrence objekti kohta kirje scheduleri
			$sch->add(array(
			 	"event" => $event_url,
				"rep_id" => $rep_id,
			));
		};
	}

	function get_tags($o)
	{
		$arr = array(
			// T88taja
			"tootajad_tag" => "TOOTAJAD",
			"tootaja_tag" => "TOOTAJA",
			"tootaja_id_tag" => "TOOTAJA_ID",
			"eesnimi_tag" => "EESNIMI",
			"perekonnanimi_tag" => "PEREKONNANIMI",
			"synniaeg_tag" => "SYNNIAEG",
			"haridustase_tag" => "HARIDUSTASE",
			"telefon_tag" => "TELEFON",
			"mobiiltelefon_tag" => "MOBIILTELEFON",
			"lyhinumber_tag" => "LYHINUMBER",
			"e_post_tag" => "E_POST",
			"ametikoht_nimetus_tag" => "AMETIKOHT_NIMETUS",
			"ametikirjeldus_viit_tag" => "AMETIKIRJELDUS_VIIT",
			"ruum_tag" => "RUUM",
			"asutus_tag" => "ASUTUS",
			"allasutus_tag" => "ALLASUTUS",
			"yksus_tag" => "YKSUS",
			"yksus_id_tag" => "YKSUS_ID",
			"prioriteet_tag" => "PRIORITEET",
			"on_peatumine_tag" => "ON_PEATUMINE",
			"peatumine_pohjus_tag" => "PEATUMINE_POHJUS",
			"asutusse_tulek_tag" => "ASUTUSSE_TULEK",
			"on_asendaja_tag" => "ON_ASENDAJA",
			"asendamine_tookoht_tag" => "ASENDAMINE_TOOKOHT",
			// Haridusk2igud
			"hariduskaigud_tag" => "HARIDUSKAIGUD",
			"hariduskaik_tag" => "HARIDUSKAIK",
			"edu_tootaja_id_tag" => "TOOTAJA_ID",
			"oppeasutus_tag" => "OPPEASUTUS",
			"on_opilane_tag" => "ON_OPILANE",
			"eriala_tag" => "ERIALA",
			"on_pohieriala_tag" => "ON_POHIERIALA",
			"diplom_number_tag" => "DIPLOM_NUMBER",
			"akadeemiline_kraad_tag" => "AKADEEMILINE_KRAAD",
			"keel_tag" => "KEEL",
			"diplom_kuupaev_tag" => "DIPLOM_KUUPAEV",
			// T88suhte peatumised
			"toosuhte_peatumised_tag" => "TOOSUHTE_PEATUMISED",
			"toosuhte_peatumine_tag" => "TOOSUHTE_PEATUMINE",
			"ewr_tootaja_id_tag" => "TOOTAJA_ID",
			"alguskuupaev_tag" => "ALGUSKUUPAEV",
			"loppkuupaev_tag" => "LOPPKUUPAEV",
			"peatumise_liik_tag" => "PEATUMISE_LIIK",
			"asendaja_id_tag" => "ASENDAJA_ID",
		);
		foreach($arr as $key => $val)
		{
			if(strlen($o->prop($key)) > 0)
			{
				$arr[$key] = strtoupper($o->prop($key));
			}
		}
		return $arr;
	}

}
?>