<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/crm/crm_job_entry.aw,v 1.28 2008/10/29 15:55:03 markop Exp $
// crm_job_entry.aw - T88 kirje
/*

@classinfo syslog_type=ST_CRM_JOB_ENTRY no_comment=1 no_status=1 prop_cb=1 maintainer=markop

@default table=objects
@default group=general
@default field=meta
@default method=serialize

@property cust_d type=text subtitle=1
@caption Kliendi andmed

@property cust_type type=select
@caption Kliendi t&uuml;&uuml;p

@property sel_cust type=select
@caption Vali olemasolev klient

@property ettevotlusvorm type=select
@caption &Otilde;iguslik vorm

@property cust_n type=textbox
@caption Nimetus


@property sel_cust_p type=select
@caption Vali olemasolev klient

@property custp_fn type=textbox
@caption Eesnimi

@property custp_ln type=textbox
@caption Perenimi

@property custp_phone type=textbox
@caption Telefon

@property custp_email type=textbox
@caption E-post


@property addr type=textbox
@caption Aadress

@property addr_linn type=textbox
@caption Linn

@property post_index type=textbox size=8
@caption Postiindeks

@property maakond type=select
@caption Maakond

@property riik type=textbox default=Eesti
@caption Riik

@property cust_mgr type=select
@caption Kliendihaldur

@property cont_d type=text subtitle=1
@caption Kontaktisiku andmed

@property ct_fn type=textbox
@caption Eesnimi

@property ct_ln type=textbox
@caption Perenimi

@property ct_phone type=textbox
@caption Telefon

@property ct_email type=textbox
@caption E-post


@property proj_header type=text subtitle=1
@caption Projekti andmed

@property proj_name type=textbox
@caption  Nimetus

@property proj_desc type=textarea rows=10 cols=50
@caption Kirjeldus

@property proj_parts type=select multiple=1
@caption Osalejad

@property proj_type type=select multiple=1
@caption Liik

@property task_desc type=text subtitle=1
@caption Tegevuse andmed

@property task_type type=select
@caption Liik

@property task_start type=datetime_select
@caption Algus

@property task_end type=datetime_select
@caption L&otilde;pp

@property task_content type=textarea rows=10 cols=50
@caption Sisu

@property res_desc type=text subtitle=1
@caption Ressursid

@property resource_sel type=chooser multiple=1
@caption Ressursid
*/

class crm_job_entry extends class_base
{
	function crm_job_entry()
	{
		$this->init(array(
			"tpldir" => "applications/crm/crm_job_entry",
			"clid" => CL_CRM_JOB_ENTRY
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "task_start":
			case "task_end":
				$p = get_instance(CL_PLANNER);
				$cal = $p->get_calendar_for_user();
				if ($cal)
				{
					$calo = obj($cal);
					$prop["minute_step"] = $calo->prop("minute_step");
				}
				break;

			case "cust_mgr":
				$u = get_instance(CL_USER);
				$c = get_instance(CL_CRM_COMPANY);
				$prop["options"] = $c->get_employee_picker(obj($u->get_current_company()), true);
				$prop["value"] = $u->get_current_person();

				if (isset($prop["options"]) && !isset($prop["options"][$prop["value"]]) && $this->can("view", $prop["value"]))
				{
					$tmp = obj($prop["value"]);
					$prop["options"][$prop["value"]] = $tmp->name();
				}
				break;

			case "post_index":
				$oncl = "window.open('http://www.post.ee/?id=1069&op=sihtnumbriotsing&tanav='+document.changeform.addr.value.replace(/[-0-9]+/, '')+'&linn='+document.changeform.addr_linn.value+'&x=30&y=6');";
				$prop["post_append_text"] = sprintf(" <a href='#' onClick=\"$oncl\">%s</a>", t("Otsi postiindeksit"));
				break;

			case "cust_type":
				$prop["options"] = array(CL_CRM_COMPANY => t("Organisatsioon"), CL_CRM_PERSON => t("Isik"));
				$prop["onchange"] = "if (navigator.userAgent.toLowerCase().indexOf('msie')>=0){d = 'block';} else { d = 'table-row';} if (this.selectedIndex == 1) {document.getElementById('cont_d').parentNode.parentNode.style.display = 'none';document.getElementById('ettevotlusvorm').parentNode.parentNode.style.display = 'none';document.getElementById('sel_cust').parentNode.parentNode.style.display = 'none';/*document.getElementById('cust_nAWAutoCompleteTextbox').parentNode.parentNode.style.display = 'none';*/document.getElementById('ct_fn').parentNode.parentNode.style.display = 'none';document.getElementById('ct_ln').parentNode.parentNode.style.display = 'none';document.getElementById('ct_phone').parentNode.parentNode.style.display = 'none';document.getElementById('ct_email').parentNode.parentNode.style.display = 'none';document.getElementById('sel_cust_p').parentNode.parentNode.style.display = d;document.getElementById('custp_fn').parentNode.parentNode.style.display = d; document.getElementById('custp_ln').parentNode.parentNode.style.display = d;document.getElementById('custp_phone').parentNode.parentNode.style.display = d;document.getElementById('custp_email').parentNode.parentNode.style.display = d; } else { document.getElementById('ettevotlusvorm').parentNode.parentNode.style.display = d;document.getElementById('sel_cust').parentNode.parentNode.style.display = d;/*document.getElementById('cust_nAWAutoCompleteTextbox').parentNode.parentNode.style.display = d;*/document.getElementById('ct_fn').parentNode.parentNode.style.display = d;document.getElementById('cont_d').parentNode.parentNode.style.display = d;document.getElementById('ct_ln').parentNode.parentNode.style.display = d;document.getElementById('ct_phone').parentNode.parentNode.style.display = d;document.getElementById('ct_email').parentNode.parentNode.style.display = d;document.getElementById('sel_cust_p').parentNode.parentNode.style.display = 'none';document.getElementById('custp_fn').parentNode.parentNode.style.display = 'none'; document.getElementById('custp_ln').parentNode.parentNode.style.display = 'none';document.getElementById('custp_phone').parentNode.parentNode.style.display = 'none';document.getElementById('custp_email').parentNode.parentNode.style.display = 'none';}";
				break;

			case "proj_type":
				$cl = get_instance(CL_CLASSIFICATOR);
				$prop["options"] = $cl->get_options_for(array(
					"name" => "proj_type",
					"clid" => CL_PROJECT
				));
				break;

			case "name":
				return PROP_IGNORE;

			case "cust_n":
				$prop["autocomplete_source"] = "/automatweb/orb.aw?class=crm_company&action=name_autocomplete_source";
				$prop["autocomplete_params"] = array("cust_n");
				break;

			case "sel_cust":
				$i = get_instance(CL_CRM_COMPANY);
				$my_c = $i->get_my_customers();
				if (!count($my_c))
				{
					return PROP_IGNORE;
				}
				$ol = new object_list(array("oid" => $my_c, "class_id" => CL_CRM_COMPANY));
				$prop["options"] = array("" => t("--Vali--")) + $ol->names();
				break;

			case "sel_cust_p":
				$i = get_instance(CL_CRM_COMPANY);
				$my_c = $i->get_my_customers();
				if (!count($my_c))
				{
					return PROP_IGNORE;
				}
				$ol = new object_list(array("oid" => $my_c, "class_id" => CL_CRM_PERSON));
				$prop["options"] = array("" => t("--Vali--")) + $ol->names();
				break;

			case "ettevotlusvorm":
				$ol = new object_list(array(
					"class_id" => CL_CRM_CORPFORM,
					"lang_id" => array(),
					"site_id" => array()
				));
				$prop["options"] = array("" => t("--Vali--")) + $ol->names();
				break;

			case "maakond":
				$ol = new object_list(array(
					"class_id" => CL_CRM_COUNTY,
					"lang_id" => array(),
					"site_id" => array()
				));
				$prop["options"] = array("" => t("--Vali--")) + $ol->names();
				break;

			case "proj_parts":
				$u = get_instance(CL_USER);
				$co = obj($u->get_current_company());
				$i = $co->instance();

				$prop["options"] = $i->get_employee_picker($co);
				break;

			case "task_type":
				$clss = aw_ini_get("classes");
				$prop["options"] = array(
					CL_TASK => $clss[CL_TASK]["name"],
					CL_CRM_MEETING => $clss[CL_CRM_MEETING]["name"],
					CL_CRM_CALL => $clss[CL_CRM_CALL]["name"],
					CL_CRM_OFFER => $clss[CL_CRM_OFFER]["name"],
				);
				break;

			case "resource_sel":
				$u = get_instance(CL_USER);
				$co = obj($u->get_current_company());
				$i = $co->instance();
				$res = $i->get_my_resources();
				$prop["options"] = $res->names();
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
			case "sel_cust":
				if (!$prop["value"] && $arr["request"]["cust_n"] == "" && $arr["request"]["custp_fn"] == "" && $arr["request"]["custp_ln"] == "" && $arr["request"]["sel_cust_p"] == "")
				{
					$prop["error"] = t("Kliendi nimi peab olema t&auml;idetud v&otilde;i olemasolev klient peab olema valitud");
					return PROP_FATAL_ERROR;
				}
				break;

			case "proj_name":
				if ($prop["value"] == "")
				{
					$prop["error"] = t("Projekti nimi peab olema t&auml;idetud");
					return PROP_FATAL_ERROR;
				}
				break;

			case "resource_sel":
				$ts = date_edit::get_timestamp($arr["request"]["task_start"]);
				$te = date_edit::get_timestamp($arr["request"]["task_end"]);
				if ($ts < 100 | $te < 100)
				{
					return PROP_IGNORE;
				}
				foreach(safe_array($prop["value"]) as $res)
				{
					$res = obj($res);
					$i = $res->instance();
					if (($desc = $i->is_available_for_range($res, $ts, $te)) !== true)
					{
						$prop["error"] = $res->name().": ".$desc;
						return PROP_FATAL_ERROR;
					}
				}
				break;
		}
		return $retval;
	}

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function callback_pre_save($arr)
	{
		// create cust
		if ($arr["request"]["cust_type"] == CL_CRM_PERSON)
		{
			$ol = new object_list(array(
				"class_id" => CL_CRM_PERSON,
				"lang_id" => array(),
				"site_id" => array(),
				"firstname" => $arr["request"]["custp_fn"],
				"lastname" => $arr["request"]["custp_ln"]
			));
		}
		else
		{
			$ol = new object_list(array(
				"class_id" => CL_CRM_COMPANY,
				"lang_id" => array(),
				"site_id" => array(),
				"name" => $arr["request"]["cust_n"]
			));
		}

		if ($this->can("view", $arr["request"]["sel_cust_p"]))
		{
			$c = obj($arr["request"]["sel_cust_p"]);
		}
		else
		if ($this->can("view", $arr["request"]["sel_cust"]))
		{
			$c = obj($arr["request"]["sel_cust"]);
		}
		else
		if ($ol->count())
		{
			$c = $ol->begin();
		}
		else
		{
			$c = obj();
			$c->set_class_id($arr["request"]["cust_type"] == CL_CRM_PERSON ? CL_CRM_PERSON : CL_CRM_COMPANY);
			$c->set_parent($arr["request"]["parent"]);
			$c->set_name($arr["request"]["cust_n"]);
			if ($c->is_property("ettevotlusvorm"))
			{
				$c->set_prop("ettevotlusvorm", $arr["request"]["ettevotlusvorm"]);
			}
			$c->set_prop("client_manager", $arr["request"]["cust_mgr"]);
			if ($c->class_id() == CL_CRM_COMPANY)
			{
				$c->set_prop("cust_contract_date", time());
			}
			$c->save();

			// create address
			$addr = obj();
			$addr->set_class_id(CL_CRM_ADDRESS);
			$addr->set_parent($c->id());
			$addr->set_prop("aadress", $arr["request"]["addr"]);
			$addr->set_prop("postiindeks", $arr["request"]["post_index"]);
			$addr->save();
			$this->set_by_n($addr, "linn", $arr["request"]["addr_linn"], CL_CRM_CITY, $addr->id());
			$addr->set_prop("maakond", $arr["request"]["maakond"]);
			$this->set_by_n($addr, "riik", $arr["request"]["riik"], CL_CRM_COUNTRY, $addr->id());
			$name = array();
			$form = $arr["request"];
			$name[] = $form['addr'];
			$name[] = $form['addr_linn'];
			$name[] = $form['maakond'];
			$addr->set_name(join(",  ", $name));
			$addr->save();

			$c->set_prop($c->class_id() == CL_CRM_COMPANY ? "contact" : "address", $addr->id());
			$c_i = $c->instance();
			if ($c->class_id() == CL_CRM_COMPANY)
			{
				$c_i->_gen_company_code($c);
			}

			if ($c->class_id() == CL_CRM_PERSON)
			{
				$c_i->gen_code($c);
				$c->set_name($arr["request"]["custp_fn"]." ".$arr["request"]["custp_ln"]);
				$c->set_prop("firstname", $arr["request"]["custp_fn"]);
				$c->set_prop("lastname", $arr["request"]["custp_ln"]);
				$this->set_by_n($c, "phone", $arr["request"]["custp_phone"], CL_CRM_PHONE, $c->id());
				$this->set_by_n($c, "email", $arr["request"]["custp_email"], CL_ML_MEMBER, $c->id());
				$c->set_prop("is_customer", 1);
			}

			$c->save();
		}


		if ($c->class_id() == CL_CRM_COMPANY)
		{
			// check if such a person already exists in that co
			$c_i = $c->instance();
			$emp_p = array_flip($c_i->get_employee_picker($c));
			if (!isset($emp_p[$arr["request"]["ct_fn"]." ".$arr["request"]["ct_ln"]]))
			{
				// kontaktisik
				$pers = obj();
				$pers->set_class_id(CL_CRM_PERSON);
				$pers->set_parent($c->id());
				$pers->set_name($arr["request"]["ct_fn"]." ".$arr["request"]["ct_ln"]);
				$pers->set_prop("firstname", $arr["request"]["ct_fn"]);
				$pers->set_prop("lastname", $arr["request"]["ct_ln"]);
				$this->set_by_n($pers, "phone", $arr["request"]["ct_phone"], CL_CRM_PHONE, $c->id());
				$this->set_by_n($pers, "email", $arr["request"]["ct_email"], CL_ML_MEMBER, $c->id());
				$pers->save();

				// add person as employee
				/* This porperty no longer exists.
				$pers->set_prop("work_contact", $c->id());
				$pers->save();
				*/
				$pers->add_work_relation(array("org" => $c->id()));

//				$c->connect(array(
//					"to" => $pers->id(),
//					"type" => "RELTYPE_WORKERS"
//				));
				$c->set_prop("contact_person", $pers->id());
				$c->save();

				// add as important person for me
				$u = get_instance(CL_USER);
				$cur_p = obj($u->get_current_person());
				$cur_p->connect(array(
					"to" => $pers->id(),
					"type" => "RELTYPE_IMPORTANT_PERSON"
				));
			}
		}

		$u = get_instance(CL_USER);
		// create proj
		$p = obj();
		$p->set_class_id(CL_PROJECT);
		$p->set_parent($arr["request"]["parent"]);
		$p->set_name($arr["request"]["proj_name"]);
		$p->set_prop("orderer", $c->id());
		$p->set_prop("description", $arr["request"]["proj_desc"]);
		$p->set_prop("start", date_edit::get_timestamp($arr["request"]["task_start"]));
		$p->set_prop("end", date_edit::get_timestamp($arr["request"]["task_end"]));

		$ppt = $arr["request"]["proj_parts"];
		if (!is_array($ppt) || count($ppt) == 0)
		{
			$u = get_instance(CL_USER);
			$curp = $u->get_current_person();
			$ppt = array($curp => $curp);
		}
		$p->set_prop("participants", $ppt);
		$p->set_prop("proj_type", $arr["request"]["proj_type"]);
		$p->set_prop("implementor", $u->get_current_company());
		$p->save();
		$si = __get_site_instance();
		if (method_exists($si, "project_gen_code"))
		{
			$p->set_prop("code", $si->project_gen_code(array(
				"prop" => array("value" => ""),
				"obj_inst" => $p,
			)));
			$p->save();
		}

		// create task
		$t = obj();
		$t->set_class_id($arr["request"]["task_type"]);
		$t->set_parent($p->id());
		$t->set_name($arr["request"]["proj_name"]);
		$t->set_prop("start1", date_edit::get_timestamp($arr["request"]["task_start"]));
		$t->set_prop("end", date_edit::get_timestamp($arr["request"]["task_end"]));
		$t->set_prop("content", $arr["request"]["task_content"]);
		if ($t->is_property("participants"))
		{
			$t->set_prop("participants", safe_array($ppt));
		}
		if ($t->class_id() == CL_CRM_OFFER)
		{
			$t->set_prop("orderer", $c->id());
		}
		else
		if ($t->class_id() != CL_CRM_CALL)
		{
			$t->set_prop("customer", $c->id());
			$t->set_prop("project", $p->id());
		}

		$t->save();

		// add participants to task from project
		$task_inst = get_instance(CL_TASK);
		foreach(safe_array($ppt) as $person)
		{
			$task_inst->add_participant($t, obj($person));
		}

		switch($t->class_id())
		{
			case CL_TASK:
				foreach((array)$ppt as $part)
				{
					if (!$this->can("view", $part))
					{
						continue;
					}
					$_pers = obj($part);
					$_pers->connect(array(
						"to" => $t->id(),
						"type" => "RELTYPE_PERSON_TASK"
					));
				}

				// connect resources
				foreach(safe_array($arr["request"]["resource_sel"]) as $res_id)
				{
					$t->connect(array(
						"to" => $res_id,
						"type" => "RELTYPE_RESOURCE"
					));
				}

				$conns = $t->connections_to(array());
				foreach($conns as $conn)
				{
					if($conn->prop('from.class_id')==CL_CRM_PERSON)
					{
						$pers = $conn->from();
						// get profession
						$rank = $pers->prop("rank");
						if (is_oid($rank) && $this->can("view", $rank))
						{
							$rank = obj($rank);
							$t->set_prop("hr_price",$rank->prop("hr_price"));
							$t->save();
						}
					}
				}

				header("Location: ".html::get_change_url($t->id(), array("group" => "rows", "return_url" => $arr["request"]["return_url"])));
				die();
				break;

			case CL_CRM_CALL:
				foreach((array)$ppt as $part)
				{
					if (!$this->can("view", $part))
					{
						continue;
					}
					$_pers = obj($part);
					$_pers->connect(array(
						"to" => $t->id(),
						"type" => "RELTYPE_PERSON_CALL"
					));
				}
				break;

			case CL_CRM_MEETING:
				foreach((array)$ppt as $part)
				{
					if (!$this->can("view", $part))
					{
						continue;
					}
					$_pers = obj($part);
					$_pers->connect(array(
						"to" => $t->id(),
						"type" => "RELTYPE_PERSON_MEETING"
					));
				}

				// connect resources
				foreach(safe_array($arr["request"]["resource_sel"]) as $res_id)
				{
					$t->connect(array(
						"to" => $res_id,
						"type" => "RELTYPE_RESOURCE"
					));
				}
				break;
		}

		header("Location: ".html::get_change_url($t->id(), array("return_url" => $arr["request"]["return_url"])));
		die();
	}

	function set_by_n($ro, $prop, $val, $clid, $pt)
	{
		$ol = new object_list(array(
			"class_id" => $clid,
			"name" => $val,
			"lang_id" => array(),
			"site_id" => array()
		));
		if ($ol->count())
		{
			$o = $ol->begin();
		}
		else
		{
			$o = obj();
			$o->set_class_id($clid);
			$o->set_parent($pt);
			$o->set_name($val);
			if ($clid == CL_ML_MEMBER)
			{
				$o->set_prop("mail", $val);
			}
			$o->save();
		}
		$ro->set_prop($prop, $o->id());
	}

	function callback_generate_scripts()
	{
		return "if (navigator.userAgent.toLowerCase().indexOf('msie')>=0){d = 'block';} else { d = 'table-row';} document.getElementById('ettevotlusvorm').parentNode.parentNode.style.display = d;document.getElementById('sel_cust').parentNode.parentNode.style.display = d;/*document.getElementById('cust_nAWAutoCompleteTextbox').parentNode.parentNode.style.display = d;*/document.getElementById('ct_fn').parentNode.parentNode.style.display = d;document.getElementById('ct_ln').parentNode.parentNode.style.display = d;document.getElementById('ct_phone').parentNode.parentNode.style.display = d;document.getElementById('ct_email').parentNode.parentNode.style.display = d;document.getElementById('sel_cust_p').parentNode.parentNode.style.display = 'none';document.getElementById('custp_fn').parentNode.parentNode.style.display = 'none'; document.getElementById('custp_ln').parentNode.parentNode.style.display = 'none';document.getElementById('custp_phone').parentNode.parentNode.style.display = 'none';document.getElementById('custp_email').parentNode.parentNode.style.display = 'none';";
	}
}
?>
