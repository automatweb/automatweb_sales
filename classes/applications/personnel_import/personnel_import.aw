<?php

namespace automatweb;
// personnel_import.aw - Personali import
/*

@classinfo syslog_type=ST_PERSONNEL_IMPORT relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kaarel

@default table=objects
@default group=general
@default field=meta
@default method=serialize

	@property slave_obj type=relpicker reltype=RELTYPE_SLAVE_OBJ
	@caption Massiivi vormistaja

	@property last_import type=text
	@caption Viimane import toimus

	@property import type=text store=no
	@caption Import

@groupinfo settings caption="Seaded"
@default group=settings

	@property crm_db_id type=relpicker reltype=RELTYPE_CRM_DB
	@caption Kasutatav kliendibaas

@groupinfo autoimport caption="Automaatne import"
@default group=autoimport

	@property recur_edit type=releditor reltype=RELTYPE_RECURRENCE use_form=emb group=autoimport rel_id=first
	@caption Automaatse impordi seadistamine

@reltype SLAVE_OBJ value=1 clid=CL_UT_IMPORT,CL_TAKET_AFP_IMPORT
@caption Massiivi vormistaja

@reltype CRM_DB value=2 clid=CL_CRM_DB
@caption kliendibaas

@reltype RECURRENCE value=3 clid=CL_RECURRENCE
@caption Kordus

*/

class personnel_import extends class_base
{
	const AW_CLID = 1386;

	function personnel_import()
	{
		$this->init(array(
			"tpldir" => "applications/personnel_import/personnel_import",
			"clid" => CL_PERSONNEL_IMPORT
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
			case "last_import":
				$prop["value"] = aw_locale::get_lc_date($prop["value"],6) . date(" H:i",$prop["value"]);
				break;

			case "import":
				$prop["value"] = html::href(array(
					"url" => $this->mk_my_orb("invoke", array("id" => $arr["obj_inst"]->id())),
					"caption" => t("K&auml;ivita import"),
				));
				break;
		}

		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
		}

		return $retval;
	}	

	/**
		@attrib name=invoke nologin=1
		@param id required type=int
	**/
	function invoke($arr)
	{
		// NO CACHE
		obj_set_opt("no_cache", 1);
		aw_global_set("no_cache_flush", 1);

		aw_set_exec_time(AW_LONG_PROCESS);
		ini_set("memory_limit", "800M");

		$o = new object($arr["id"]);

		$crm_db_id = $o->prop("crm_db_id");

		if (!is_oid($crm_db_id))
		{
			die(t("Kliendibaas valimata!"));
		};

		$crm_db = new object($crm_db_id);

		$folder_person = $crm_db->prop("folder_person");
		//arr($crm_db->properties());

		if (!is_oid($folder_person))
		{
			die(t("Isikute kataloog valimata!"));
		};

		$folder_address = $crm_db->prop("dir_address");
		if (!is_oid($folder_address))
		{
			die(t("Aadresside kataloog valimata!"));
		};

		$dir_default = $crm_db->prop("dir_default");
		if (!is_oid($dir_default))
		{
			die(t("Default kataloog valimata!"));
		};

		print "Gathering data of existing CL_CRM_COMPANY objects...<br>";
		flush();
		$organizations_parent = $crm_db->prop('dir_firma');
		if (empty($organizations_parent))
		{
			$organizations_parent = $dir_default;
		}
		$organizations_arr = new object_data_list(
			array(
				"class_id" => CL_CRM_COMPANY,
				"parent" => $organizations_parent
			),
			array
			(
				CL_CRM_COMPANY => array("oid" => "oid", "subclass" => "ext_id")
			)
		);

		foreach($organizations_arr->list_data as $lde)
		{
			if(empty($lde["ext_id"]))
			{
				continue;
			}
			if(isset($organizations[$lde["ext_id"]]) && is_oid($organizations[$lde["ext_id"]]))
			{
				print "SKIPPED! We already have an object (".$organizations[$lde["ext_id"]].") with external ID (".$lde["ext_id"]."). Why do we have this here? -> ".$lde["oid"]."<br>";
				flush();
				continue;
			}
			$organizations[$lde["ext_id"]] = $lde["oid"];
			$doomed_organizations[$lde["oid"]] = 1;
		}


		print "Gathering data of existing CL_CRM_PROFESSION objects...<br>";
		flush();
		$professions_arr = new object_data_list(
			array(
				"class_id" => CL_CRM_PROFESSION,
				"parent" => $dir_default
			),
			array
			(
				CL_CRM_PROFESSION => array("oid" => "oid", "name" => "name")
			)
		);
//		arr($professions_arr->list_data);
		foreach($professions_arr->list_data as $lde)
		{
			if(empty($lde["name"]))
				continue;
			if(is_oid($professions[$lde["name"]]))
			{
				print "SKIPPED! We already have an object (".$professions[$lde["name"]].") with name (".$lde["name"]."). Why do we have this here? -> ".$lde["oid"]." Delete, of course!<br>";
				flush();
				$doomed_profession_obj = obj($lde["oid"]);
				aw_disable_acl();
				$doomed_profession_obj->delete();
				aw_restore_acl();
				continue;
			}
			$professions[$lde["name"]] = $lde["oid"];
//			$doomed_professions[$lde["oid"]] = 1;
		}
//		arr($professions);
//		exit;


		print "Gathering data of existing CL_CRM_SECTION objects...<br>";
		flush();
		$sections_arr = new object_data_list(
			array(
				"class_id" => CL_CRM_SECTION,
				"parent" => $dir_default
			),
			array
			(
				CL_CRM_SECTION => array("oid" => "oid", "subclass" => "ext_id")
			)
		);
//		arr($sections_arr->list_data);
		foreach($sections_arr->list_data as $lde)
		{
			if(empty($lde["ext_id"]))
				continue;
			if(is_oid($sections[$lde["ext_id"]]))
			{
				print "SKIPPED! We already have an object (".$sections[$lde["ext_id"]].") with external ID (".$lde["ext_id"]."). Why do we have this here? -> ".$lde["oid"]."<br>";
				flush();
//				$doomed_section_obj = obj($lde["oid"]);
				aw_disable_acl();
//				$doomed_section_obj->delete();
				aw_restore_acl();
				continue;
			}
			$sections[$lde["ext_id"]] = $lde["oid"];
			$doomed_sections[$lde["oid"]] = 1;
		}
//		arr($sections);

		print "Gathering data of existing CL_CRM_PERSON objects...<br>";
		flush();
		$persons_arr = new object_data_list(
			array(
				"class_id" => CL_CRM_PERSON,
				"parent" => $dir_person
			),
			array
			(
				CL_CRM_PERSON => array("oid" => "oid", "personal_id" => "pid", "firstname" => "firstname", "lastname" => "lastname", "subclass" => "ext_id")
			)
		);
//		arr($persons_arr->list_data);
//		exit;
		foreach($persons_arr->list_data as $lde)
		{
			if(empty($lde["pid"]))
			{
				$persons_byextid[$lde["ext_id"]] = $lde["oid"];
				$persons_byname[$lde["lastname"]][$lde["firstname"]] = $lde["oid"];
				continue;
			}
			if(isset($persons[$lde["pid"]]) && is_oid($persons[$lde["pid"]]))
			{
				print "SKIPPED! We already have an object (".$persons[$lde["pid"]].") with personal ID (".$lde["pid"]."). Why do we have this here? -> ".$lde["oid"]."<br>";
				flush();
//				$doomed_person_obj = obj($lde["oid"]);
				aw_disable_acl();
//				$doomed_person_obj->delete();
				aw_restore_acl();
				continue;
			}
			$persons[$lde["pid"]] = $lde["oid"];
			$doomed_persons[$lde["oid"]] = 1;
		}
//		arr($persons);
//		arr($persons_byname);
//		exit;

		$slave_obj = obj($o->prop("slave_obj"));
		$slave_inst = $slave_obj->instance();
		$arr["slave_obj_id"] = $o->prop("slave_obj");

		$file_name_r = aw_ini_get("site_basedir")."/files/personnel_import_array.txt";
		$file_name_status = aw_ini_get("site_basedir")."/files/personnel_import_status.txt";
		
		// now check whether we have a half completed status file
		$status_file_content = $this->get_file(array(
			"file" => $file_name_status,
		));

		$r_file_content = $this->get_file(array(
			"file" => $file_name_r,
		));

		if($status_file_content && $r_file_content)
		{
			// load status array back into memory
			print "Trying to continue aborted import process...<br>";
			$status = aw_unserialize($status_file_content);

			$r = aw_unserialize($r_file_content);
			$count_to_go = sizeof($r["PERSONS"]) + sizeof($r["SECTIONS"]) + sizeof($r["ORGANIZATIONS"]);
			$count_to_go -= sizeof($status["PERSONS"]) + sizeof($status["SECTIONS"]) + sizeof($status["ORGANIZATIONS"]);
			print $count_to_go." to go.<br>";
		}
		else
		{
			$r = $slave_inst->make_master_import_happy($arr);
			
			$this->put_file(array(
				"file" => $file_name_r,
				"content" => aw_serialize($r),
			));
		}

		print "<h2>ORGANIZATIONS</h2>";
		flush();
			
		foreach($r["ORGANIZATIONS"] as $ext_id => $organization)
		{
			if($status["ORGANIZATIONS"][$ext_id] == 1)
			{
				print "CL_CRM_COMPANY object with external ID - ".$ext_id." already imported. Skipping.<br>";
				unset($doomed_organizations[$organizations[$ext_id]]);
				continue;
			}
			if(is_oid($organizations[$ext_id]))
			{
				$so = obj($organizations[$ext_id]);
				print "Using existing CL_CRM_COMPANY object. External ID - ".$ext_id.".<br>";
				unset($doomed_organizations[$organizations[$ext_id]]);
			}
			else
			{
				print "Creating new CL_CRM_COMPANY object. External ID - ".$ext_id.".<br>";
				$so = new object;
				$so->set_class_id(CL_CRM_COMPANY);
				$so->set_parent($organizations_parent);
			}
			$so->set_name($organization["NAME"]);
			$so->set_subclass($ext_id);
			aw_disable_acl();
			$so->save();
			aw_restore_acl();
			$soid = $so->id();
			$organizations[$ext_id] = $soid;

			print "Done with this one. ID - ".$organizations[$ext_id].".<br><br>";
			$status["OGANIZATIONS"][$ext_id] = 1;
			$this->put_file(array(
				"file" => $file_name_status,
				"content" => aw_serialize($status),
			));
		}

		print "<h2>SECTIONS</h2>";
		flush();
			
		foreach($r["SECTIONS"] as $ext_id => $section)
		{
			if($status["SECTIONS"][$ext_id] == 1)
			{
				print "CL_CRM_SECTION object with external ID - ".$ext_id." already imported. Skipping.<br>";
				unset($doomed_sections[$sections[$ext_id]]);
				continue;
			}
			if(is_oid($sections[$ext_id]))
			{
				$so = obj($sections[$ext_id]);
				$t = $so->meta("translations");
				print "Using existing CL_CRM_SECTION object. External ID - ".$ext_id.".<br>";
				unset($doomed_sections[$sections[$ext_id]]);
				foreach($so->connections_from(array("type" => 1)) as $conn)
				{
					$to_obj = $conn->to();
					if(!isset($doomed_sec_conns[$sections[$ext_id]][$to_obj->id()]))
					{
						$doomed_sec_conns[$sections[$ext_id]][$to_obj->id()] = 0;
					}
				}
			}
			else
			{
				print "Creating new CL_CRM_SECTION object. External ID - ".$ext_id.".<br>";
				$so = new object;
				$so->set_class_id(CL_CRM_SECTION);
				$so->set_parent($dir_default);
				$t = array();
			}
			/* Don't think I have to do it, actually.
			if(is_oid($sections[$section["PARENT"]]))
			{
				print "Parent -> ".$sections[$section["PARENT"]]."<br>";
				flush();
				$so->set_parent($sections[$section["PARENT"]]);
			}
			/**/
			$so->set_name($section["NAME"]);
			$t[$li_en]["name"] = $section["EN"]["NAME"];
			$so->set_prop("code", $section["CODE"]);
			$so->set_prop("jrk", $section["QUEUE_NR"]);
			$so->set_subclass($ext_id);
			aw_disable_acl();
			$so->save();
			aw_restore_acl();
			$soid = $so->id();
			$sections[$ext_id] = $soid;
			if(is_oid($organizations[$section["PARENT_ORG"]]))
			{
				print "Connecting to organization -> ".$organizations[$section["PARENT_ORG"]]."<br>";
				flush();
				$yl_o = obj($organizations[$section["PARENT_ORG"]]);
				$yl_o->connect(array(
					"to" => $soid,
					"reltype" => 28,		// RELTYPE_SECTION
				));
//				$doomed_sec_conns[$sections[$section["PARENT"]]][$soid] = 1;
			}
			elseif(is_oid($sections[$section["PARENT_SEC"]]))
			{
				print "Connecting to parent section -> ".$sections[$section["PARENT_SEC"]]."<br>";
				flush();
				$yl_o = obj($sections[$section["PARENT_SEC"]]);
				$yl_o->connect(array(
					"to" => $soid,
					"reltype" => 1,		// RELTYPE_SECTION
				));
				$doomed_sec_conns[$sections[$section["PARENT_SEC"]]][$soid] = 1;
			}

			if(!empty($section["ADDRESS"]))
			{
				$addr_done = false;
				foreach($so->connections_from(array("type" => 6)) as $conn)
				{
					if(!isset($doomed_conns[$conn->id]))
					{
						$doomed_conns[$conn->id()] = 0;
					}
					$addr = $conn->to();
					if($addr->name() == $section["ADDRESS"] && !$addr_done)
					{
						$doomed_conns[$conn->id()] = 1;
						print "Using existing CL_CRM_ADDRESS object. ID - ".$addr->id().".<br>";
						flush();
						$addr_done = true;
					}
				}
				if(!$addr_done && !empty($section["ADDRESS"]))
				{
					print "Creating new CL_CRM_ADDRESS object.";
					flush();
					$addr = new object;
					$addr->set_class_id(CL_CRM_ADDRESS);
					$addr->set_parent($folder_address);
					$addr->set_comment($section["ADDRESS"]);
					$addr->set_name($section["ADDRESS"]);
					aw_disable_acl();
					$addr->save();
					aw_restore_acl();

					$c = new connection();
					$c->change(array(
						"from" => $soid,
						"to" => $addr->id(),
						"reltype" => 6,		//RELTYPE_ADDRESS
					));
					$doomed_conns[$c->prop("id")] = 1;
					print " Connected.<br>";
					flush();
				}
			}

			foreach($section["EMAILS"] as $email)
			{
				$ml_done = false;
				foreach($so->connections_from(array("type" => 7)) as $conn)
				{
					if(!isset($doomed_conns[$conn->id()]))
					{
						$doomed_conns[$conn->id()] = 0;
					}
					$ml = $conn->to();
					if($ml->prop("mail") == $email && !$ml_done)
					{
						$doomed_conns[$conn->id()] = 1;
						print "Using existing CL_ML_MEMBER object. ID - ".$ml->id().".<br>";
						flush();
						$ml_done = true;
					}
				}
				if(!$ml_done && !empty($email))
				{
					print "Creating new CL_ML_MEMBER object. ".$email;
					flush();
					$ml = new object;
					$ml->set_class_id(CL_ML_MEMBER);
					$ml->set_parent($soid);
					$ml->set_name($email);
					$ml->set_prop("mail", $email);
					aw_disable_acl();
					$ml->save();
					aw_restore_acl();

					$c = new connection();
					$c->change(array(
						"from" => $soid,
						"to" => $ml->id(),
						"reltype" => 7,		//RELTYPE_EMAIL
					));
					$doomed_conns[$c->prop("id")] = 1;
					print " Connected.<br>";
					flush();
				}
			}

			foreach($section["URLS"] as $url_e)
			{
				$url_done = false;
				foreach($so->connections_from(array("type" => 12)) as $conn)
				{
					if(!isset($doomed_conns[$conn->id()]))
					{
						$doomed_conns[$conn->id()] = 0;
					}
					$url = $conn->to();
					if($url->name() == $url_e && !$url_done)
					{
						$doomed_conns[$conn->id()] = 1;
						print "Using existing CL_EXTLINK object. ID - ".$url->id().".<br>";
						flush();
						$url_done = true;
					}
				}
				if(!$url_done && !empty($url_e))
				{
					print "Creating new CL_EXTLINK object. ".$url_e;
					flush();
					$url = new object;
					$url->set_class_id(CL_EXTLINK);
					$url->set_parent($soid);
					$url->set_name($url_e);
					aw_disable_acl();
					$url->save();
					aw_restore_acl();

					$c = new connection();
					$c->change(array(
						"from" => $soid,
						"to" => $url->id(),
						"reltype" => 12,		//RELTYPE_URL
					));
					$doomed_conns[$c->prop("id")] = 1;
					print " Connected.<br>";
					flush();
				}
			}

			foreach($section["PHONES"] as $ph_type => $phones)
			{
				foreach($phones as $phone)
				{
					$ph_done = false;
					foreach($so->connections_from(array("type" => 8)) as $conn)
					{
						if(!isset($doomed_conns[$conn->id()]))
						{
							$doomed_conns[$conn->id()] = 0;
						}
						$ph = $conn->to();
						if($ph->name() == $phone && $ph->prop("type") == strtolower($ph_type) && !$ph_done)
						{
							$doomed_conns[$conn->id()] = 1;
							print "Using existing CL_CRM_PHONE object. ID - ".$ph->id().".<br>";
							flush();
							$ph_done = true;
						}
/*						elseif(!empty($phone))
						{
							print $ph->name()." == ".$phone."<br>";
							print $ph->prop("type")." == ".strtolower($ph_type)."<br>";
						}*/
					}
					if(!$ph_done && !empty($phone))
					{
						print "Creating new CL_CRM_PHONE object. ".$phone;
						$ph = new object;
						$ph->set_class_id(CL_CRM_PHONE);
						$ph->set_parent($dir_default);
						$ph->set_name($phone);
						$ph->set_prop("type", strtolower($ph_type));
						aw_disable_acl();
						$ph->save();
						aw_restore_acl();
						$c = new connection();
						$c->change(array(
							"from" => $soid,
							"to" => $ph->id(),
							"reltype" => 8,		//RELTYPE_PHONE
						));
						$doomed_conns[$c->prop("id")] = 1;
						print " Connected.<br>";
						flush();
					}
				}
			}

			print "Deleting non-existing connections.<br>";
			flush();

			foreach($doomed_conns as $conn_id => $val)
			{
				if($val == 0 && $conn_id > 0)
				{
					print "Connection ID - ".$conn_id.".";
					$c = new connection($conn_id);
					aw_disable_acl();
					$c->delete(true);
					aw_restore_acl();
					unset($doomed_conns[$conn_id]);
					print " Deleted.<br>";
				}
			}

			print "Done with this one. ID - ".$sections[$ext_id].".<br><br>";
			$status["SECTIONS"][$ext_id] = 1;
			$this->put_file(array(
				"file" => $file_name_status,
				"content" => aw_serialize($status),
			));
		}
		/*
		print "Deleting non-existing section connections.<br>";
		foreach($doomed_sec_conns as $from_id => $from_arr)
		{
			foreach($from_arr as $to_id => $val)
			{
				if()
				{

				}
			}
		}

		/**/

		print "Deleting non-existing sections.<br>";
		var_dump($doomed_sections);
		print "<br>";
		flush();
		foreach($doomed_sections as $doomed_section_id => $val)
		{
			$doomed_section = new object($doomed_section_id);
			aw_disable_acl();
			$doomed_section->delete(true);
			aw_restore_acl();
		}

		foreach($r["PERSONS"] as $ext_id => $person)
		{
			/*
			if(empty($person["PID"]))
			{
//				print "No personal ID! Name ".$person["FNAME"]." ".$person["LNAME"].", external ID ".$ext_id.". Skipping.<br>";
			}
			*/
			if($status["PERSONS"][$person["PID"]] == 1)
			{
				print "CL_CRM_PERSON object with personal ID - ".$person["PID"]." already imported. Skipping.<br>";
				unset($doomed_persons[$persons[$person["PID"]]]);
				continue;
			}
			if(is_oid($persons[$person["PID"]]))
			{
				$so = obj($persons[$person["PID"]]);
				print "Using existing CL_CRM_PERSON object. External ID - ".$ext_id.". Personal ID - ".$person["PID"].". Name - ".$person["FNAME"]." ".$person["LNAME"].".<br>";
				unset($doomed_persons[$persons[$person["PID"]]]);
			}
			elseif(is_oid($persons_byextid[$ext_id]) && empty($person["PID"]))
			{
				$so = obj($persons_byextid[$ext_id]);
				print "Using existing CL_CRM_PERSON object. External ID - ".$ext_id.". Personal ID - ".$person["PID"].". Name - ".$person["FNAME"]." ".$person["LNAME"].".<br>";
			}
			elseif(is_oid($persons_byname[$person["LNAME"]][$person["FNAME"]]) && empty($person["PID"]))
			{
				$so = obj($persons_byname[$person["LNAME"]][$person["FNAME"]]);
				print "Using existing CL_CRM_PERSON object. External ID - ".$ext_id.". Personal ID - ".$person["PID"].". Name - ".$person["FNAME"]." ".$person["LNAME"].".<br>";
			}
			else
			{
				print "Creating new CL_CRM_PERSON object. External ID - ".$ext_id.". Personal ID - ".$person["PID"].". Name - ".$person["FNAME"]." ".$person["LNAME"].".<br>";
				$so = new object;
				$so->set_class_id(CL_CRM_PERSON);
				$so->set_parent($folder_person);
				$t = array();
			}
			$so->set_name($person["FNAME"]." ".$person["LNAME"]);
			$so->set_prop("firstname", $person["FNAME"]);
			$so->set_prop("lastname", $person["LNAME"]);
			$so->set_prop("personal_id", $person["PID"]);
			$so->set_prop("gender", $person["SEX"]);
			$so->set_prop("birthday", $person["DOB"]);
			$so->set_prop("birthday_hidden", (1 - $person["SHOW"]["DOB"]));
//			$so->set_prop("", $person[""]);
			$so->set_subclass($ext_id);
			aw_disable_acl();
			$so->save();
			aw_restore_acl();
			$soid = $so->id();
			$persons[$person["PID"]] = $soid;

			$pers_to_sec = array();
			foreach($so->connections_from(array("type" => "RELTYPE_SECTION")) as $conn)
			{
				if(!isset($doomed_conns[$conn->id()]))
				{
					$doomed_conns[$conn->id()] = 0;
					$pers_to_sec[$conn->conn["to"]] = $conn->id();
				}
			}

			foreach($person["EMAILS"] as $email)
			{
				$ml_done = false;
				foreach($so->connections_from(array("type" => 11)) as $conn)
				{
					if(!isset($doomed_conns[$conn->id()]))
					{
						$doomed_conns[$conn->id()] = 0;
					}
					$ml = $conn->to();
					if($ml->prop("mail") == $email && !$ml_done)
					{
						$doomed_conns[$conn->id()] = 1;
						print "Using existing CL_ML_MEMBER object. ID - ".$ml->id().".<br>";
						flush();
						$ml_done = true;
					}
				}
				if(!$ml_done && !empty($email))
				{
					print "Creating new CL_ML_MEMBER object. ".$email;
					flush();
					$ml = new object;
					$ml->set_class_id(CL_ML_MEMBER);
					$ml->set_parent($soid);
					$ml->set_name($email);
					$ml->set_prop("mail", $email);
					aw_disable_acl();
					$ml->save();
					aw_restore_acl();

					$c = new connection();
					$c->change(array(
						"from" => $soid,
						"to" => $ml->id(),
						"reltype" => 11,		//RELTYPE_EMAIL
					));
					$doomed_conns[$c->prop("id")] = 1;
					print " Connected.<br>";
					flush();
				}
			}

			foreach($person["PHONES"] as $ph_type => $phones)
			{
				foreach($phones as $phone)
				{
					$ph_done = false;
					foreach($so->connections_from(array("type" => 13)) as $conn)
					{
						if(!isset($doomed_conns[$conn->id()]))
						{
							$doomed_conns[$conn->id()] = 0;
						}
						$ph = $conn->to();
						if($ph->name() == $phone && $ph->prop("type") == strtolower($ph_type) && !$ph_done)
						{
							$doomed_conns[$conn->id()] = 1;
							print "Using existing CL_CRM_PHONE object. ID - ".$ph->id().".<br>";
							flush();
							$ph_done = true;
						}
					}
					if(!$ph_done && !empty($phone))
					{
						print "Creating new CL_CRM_PHONE object. ".$phone;
						$ph = new object;
						$ph->set_class_id(CL_CRM_PHONE);
						$ph->set_parent($dir_default);
						$ph->set_name($phone);
						$ph->set_prop("type", strtolower($ph_type));
						aw_disable_acl();
						$ph->save();
						aw_restore_acl();

						$c = new connection();
						$c->change(array(
							"from" => $soid,
							"to" => $ph->id(),
							"reltype" => 13,		//RELTYPE_PHONE
						));
						$doomed_conns[$c->prop("id")] = 1;
						print " Connected.<br>";
						flush();
					}
				}
			}

//			arr($person["PROFESSIONS"]);
			foreach($person["PROFESSIONS"] as $profession)
			{
				if($pers_to_sec[$sections[$profession["SECTION"]]] > 0)
				{
					$doomed_conns[$pers_to_sec[$sections[$profession["SECTION"]]]] = 1;
					print "CL_CRM_PERSON ".$soid." object is connected to CL_CRM_SECTION object ".$sections[$profession["SECTION"]].".<br>";
				}
				else
				{
					if(is_oid($sections[$profession["SECTION"]]))
					{
						$so->connect(array(
							"to" => $sections[$profession["SECTION"]],
							"reltype" => 21,		// RELTYPE_SECTION
						));
					}
					print "Connecting CL_CRM_PERSON ".$soid." object to CL_CRM_SECTION object ".$sections[$profession["SECTION"]].".<br>";
				}

				$prof_done = false;
				foreach($so->connections_from(array("type" => 67)) as $conn)
				{
					if(!isset($doomed_conns[$conn->id()]))
					{
						$doomed_conns[$conn->id()] = 0;
					}
					$prof = $conn->to();
					if($prof->name() == $profession["NAME"] && $prof->prop("section") == $sections[$profession["SECTION"]] && !$prof_done)
					{
						print "Using existing CL_CRM_PERSON_WORK_RELATION object. ID - ".$prof->id().".<br>";

						$prof->set_prop("org", $organizations[$profession["ORGANIZATION"]]);
						$prof->set_comment($profession["COMMENT"]);
						$prof->set_prop("room", $profession["ROOM"]);
						$prof->set_prop("load", $profession["LOAD"]);
						aw_disable_acl();
						$prof->save();
						aw_restore_acl();
						$prof_id = $prof->id();

						$prof_rank_done = false;
						if(is_oid($prof->prop("profession")) && $this->can("view", $prof->prop("profession")) && in_array($prof->prop("profession"), $professions))
						{
							$prof_rank = new object($prof->prop("profession"));
							if($prof_rank->name() == $profession["NAME"])
							{
								print "Using existing CL_CRM_PROFESSION object. ID - ".$prof_rank->id().".<br>";
								$prof_rank->set_prop("jrk", $profession["QUEUE_NR"]);
								aw_disable_acl();
								$prof_rank->save();
								aw_restore_acl();

								foreach($prof->connections_from(array("type" => 6)) as $conn2)
								{
									if(!isset($doomed_conns[$conn2->id()]))
									{
										$doomed_conns[$conn2->id()] = 0;
									}
									$contract_stop = $conn2->to();
									if($profession["CONTRACT_STOPPED"] == 1 && !$contract_stop_done && $contract_stop->name() == $profession["NAME"])
									{
										print "Using existing CL_CRM_CONTRACT_STOP object. ID - ".$contract_stop->id().".<br>";
										$doomed_conns[$conn2->id()] = 1;
										$contract_stop_done = true;
									}
								}
								$prof_rank_done = true;
							}
						}

						if(!$prof_rank_done)
						{
							if(is_oid($professions[$profession["NAME"]]) && $this->can("view", $professions[$profession["NAME"]]))
							{
								print "Using existing CL_CRM_PROFESSION object, ID - ".$professions[$profession["NAME"]].". ".$profession["NAME"]."<br>";
		//						$prof_rank = new object($professions[$profession["NAME"]]);
								$prof_rank_id = $professions[$profession["NAME"]];
							}
							else
							{
								print "Creating new CL_CRM_PROFESSION object. ".$profession["NAME"]."<br>";
								$prof_rank = new object;
								$prof_rank->set_class_id(CL_CRM_PROFESSION);
								$prof_rank->set_parent($dir_default);
								$prof_rank->set_name($profession["NAME"]);
								$prof_rank->set_prop("jrk", $profession["QUEUE_NR"]);
								aw_disable_acl();
								$prof_rank->save();
								aw_restore_acl();
								$prof_rank_id = $prof_rank->id();
								$professions[$profession["NAME"]] = $prof_rank_id;
							}
							
							$c = new connection();
							$c->change(array(
								"from" => $prof_id,
								"to" => $prof_rank_id,
								"reltype" => 3,		//RELTYPE_PROFESSION
							));
							$prof->set_prop("profession", $prof_rank_id);
							aw_disable_acl();
							$prof->save();
							aw_restore_acl();
							$doomed_conns[$c->prop("id")] = 1;
							print "Connected CL_CRM_PROFESSION object to CL_CRM_WORK_RELATION object.<br>";
						}

						if($profession["CONTRACT_STOPPED"] == 1 && !$contract_stop_done)
						{
							print "Creating new CL_CRM_CONTRACT_STOP object. ".$profession["NAME"]."<br>";
							$contract_stop = new object;
							$contract_stop->set_class_id(CL_CRM_CONTRACT_STOP);
							$contract_stop->set_parent($dir_default);
							$contract_stop->set_name($profession["NAME"]);
							aw_disable_acl();
							$contract_stop->save();
							aw_restore_acl();
							$prof->connect(array(
								"to" => $contract_stop->id(),
								"reltype" => 6,		// RELTYPE_CONTRACT_STOP
							));
						}

						$doomed_conns[$conn->id()] = 1;
						flush();
						$prof_done = true;
					}
				}
				if(!$prof_done && !empty($profession["NAME"]))
				{
					if(is_oid($professions[$profession["NAME"]]) && $this->can("view", $professions[$profession["NAME"]]))
					{
						print "Using existing CL_CRM_PROFESSION object, ID - ".$professions[$profession["NAME"]].". ".$profession["NAME"]."<br>";
//						$prof_rank = new object($professions[$profession["NAME"]]);
						$prof_rank_id = $professions[$profession["NAME"]];
					}
					else
					{
						print "Creating new CL_CRM_PROFESSION object. ".$profession["NAME"]."<br>";
						$prof_rank = new object;
						$prof_rank->set_class_id(CL_CRM_PROFESSION);
						$prof_rank->set_parent($dir_default);
						$prof_rank->set_name($profession["NAME"]);
						$prof_rank->set_prop("jrk", $profession["QUEUE_NR"]);
						aw_disable_acl();
						$prof_rank->save();
						aw_restore_acl();
						$prof_rank_id = $prof_rank->id();
						$professions[$profession["NAME"]] = $prof_rank_id;
					}

					print "Creating new CL_CRM_PERSON_WORK_RELATION object. ".$profession["NAME"]."<br>";
					$prof = new object;
					$prof->set_class_id(CL_CRM_PERSON_WORK_RELATION);
					$prof->set_parent($dir_default);
					$prof->set_comment($profession["COMMENT"]);
					$prof->set_name($profession["NAME"]);
					$prof->set_prop("room", $profession["ROOM"]);
					$prof->set_prop("load", $profession["LOAD"]);
					aw_disable_acl();
					$prof->save();
					aw_restore_acl();
					$prof_id = $prof->id();

					$c = new connection();
					$c->change(array(
						"from" => $prof_id,
						"to" => $prof_rank_id,
						"reltype" => 3,		//RELTYPE_PROFESSION
					));

					$doomed_conns[$c->prop("id")] = 1;
					print "Connected CL_CRM_PROFESSION object to CL_CRM_WORK_RELATION object.<br>";
					flush();

					if(is_oid($sections[$profession["SECTION"]]))
					{
						$prof->connect(array(
							"to" => $sections[$profession["SECTION"]],
							"reltype" => 7,		// RELTYPE_SECTION
						));
						print "Connected CL_CRM_SECTION object to CL_CRM_WORK_RELATION object.<br>";
						flush();
					}

					$c = new connection();
					$c->change(array(
						"from" => $soid,
						"to" => $prof_id,
						"reltype" => 67,		//RELTYPE_CURRENT_JOB
					));
					$doomed_conns[$c->prop("id")] = 1;
					print " Connected.<br>";
					flush();
					
					if($profession["CONTRACT_STOPPED"] == 1)
					{
						print "Creating new CL_CRM_CONTRACT_STOP object. ".$profession["NAME"];
						$contract_stop = new object;
						$contract_stop->set_class_id(CL_CRM_CONTRACT_STOP);
						$contract_stop->set_parent($dir_default);
						$contract_stop->set_name($profession["NAME"]);
						aw_disable_acl();
						$contract_stop->save();
						aw_restore_acl();
						$contract_stop_id = $contract_stop->id();

						$prof->connect(array(
							"to" => $contract_stop_id,
							"reltype" => 6,		// RELTYPE_CONTRACT_STOP
						));
						print "Connected CL_CRM_CONTRACT_STOP object to CL_CRM_WORK_RELATION object.<br>";
						flush();
						$prof->set_prop("contract_stop", $contract_stop_id);
					}
					
					$prof->set_prop("section", $sections[$profession["SECTION"]]);
					$prof->set_prop("org", $organizations[$profession["ORGANIZATION"]]);
					$prof->set_prop("profession", $prof_rank_id);
					aw_disable_acl();
					$prof->save();
					aw_restore_acl();
				}
			}

			$deg_done = false;
			foreach($so->connections_from(array("type" => 75)) as $conn)
			{
				if(!isset($doomed_conns[$conn->id()]))
				{
					$doomed_conns[$conn->id()] = 0;
				}
				$deg = $conn->to();
				if($deg->prop("name") == $person["DEGREE"]["NAME"] && $deg->prop("subject") == $person["DEGREE"]["SUBJECT"] && !$deg_done)
				{
					$doomed_conns[$conn->id()] = 1;
					print "Using existing CL_CRM_DEGREE object. ID - ".$deg->id().".<br>";
					flush();
					$deg_done = true;
				}
			}
			if(!$deg_done && !empty($person["DEGREE"]["NAME"]))
			{
				print "Creating new CL_CRM_DEGREE object. ".$person["DEGREE"]["NAME"];
				flush();
				$deg = new object;
				$deg->set_class_id(CL_CRM_DEGREE);
				$deg->set_parent($dir_default);
				$deg->set_name($person["DEGREE"]["NAME"]);
				$deg->set_prop("subject", $person["DEGREE"]["SUBJECT"]);
				aw_disable_acl();
				$deg->save();
				aw_restore_acl();
				
				$c = new connection();
				$c->change(array(
					"from" => $soid,
					"to" => $deg->id(),
					"reltype" => 75,		//RELTYPE_DEGREE
				));
				$doomed_conns[$c->prop("id")] = 1;
				print " Connected.<br>";
				flush();
			}

			print "Deleting non-existing connections.<br>";
			flush();

			foreach($doomed_conns as $conn_id => $val)
			{
				if($val == 0 && $conn_id > 0)
				{
					print "Connection ID - ".$conn_id.".";
					$c = new connection($conn_id);
					aw_disable_acl();
					$c->delete(true);
					aw_restore_acl();
					unset($doomed_conns[$conn_id]);
					print " Deleted.<br>";
				}
			}

			print "Done with this one. ID - ".$persons[$person["PID"]].".<br><br>";
			$status["PERSONS"][$person["PID"]] = 1;			
			$this->put_file(array(
				"file" => $file_name_status,
				"content" => aw_serialize($status),
			));
		}

		print "Deleting non-existing persons.<br>";
		var_dump($doomed_persons);
		print "<br>";
		flush();
		foreach($doomed_persons as $doomed_person_id => $val)
		{
			$doomed_person = new object($doomed_person_id);
			aw_disable_acl();
			$doomed_person->delete(true);
			aw_restore_acl();
		}

		print "<h1>ALL DONE!</h1>";
		$o->set_meta("last_import", time());
		print "Unlinking status file and array file.<br>";
		unlink($file_name_r);
		unlink($file_name_status);
		print "Flushing cache...<br>";
		flush();
		$cache = get_instance("cache");
		$cache->full_flush();
		exit;
	}

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function show($arr)
	{
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");
		$this->vars(array(
			"name" => $ob->prop("name"),
		));
		return $this->parse();
	}
}

?>

