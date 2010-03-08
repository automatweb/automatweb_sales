<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/calendar/calendar_registration.aw,v 1.10 2007/12/06 14:32:55 kristo Exp $
// calendar_registration.aw - Kalendri sündmusele registreerumine 
/*

@classinfo syslog_type=ST_CALENDAR_REGISTRATION relationmgr=yes no_comment=1 no_status=1 maintainer=kristo

@default table=objects
@default group=general
@default field=meta
@default method=serialize

@property event_comment type=textbox 
@caption S&uuml;ndmuse kommentaari

@groupinfo emails caption="Meilid"
@default group=emails

@property mail_from_addr type=textbox 
@caption Maili From aadress

@property mail_from_name type=textbox 
@caption Maili From nimi

@property mail_subj type=textbox 
@caption Maili subjekt

@property mail_legend type=text store=no
@caption Asenduste legend

@property mail_content type=textarea rows=20 cols=80
@caption Maili sisu


@reltype CALENDAR value=1 clid=CL_PLANNER
@caption kalender

*/

class calendar_registration extends class_base
{
	function calendar_registration()
	{
		$this->init(array(
			"tpldir" => "applications/calendar/calendar_registration",
			"clid" => CL_CALENDAR_REGISTRATION
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "cal":
				$this->gen_event_list($arr);
				break;

			case "mail_legend":
				$prop["value"] = t("Meili sisus on v&otilde;imalik kasutada j&auml;rgnevaid asendusi:<br><br>#reg_data# - registreeruja andmed ja sisu<br>
					#name# - kalendri omaniku nimi<br>
					#phone# - kalendri omaniku telefon<br>
					#email# - kalendri omaniku e-mail<br>
					#addr# - kalendri omaniku aadress<br>
					#date# - kohtumise kuup&auml;ev<br>
					#time_from# - kohtumise alguskellaaeg<br>
					#time_to# - kohtumise k&otilde;ppkellaaeg<br>");
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

		}
		return $retval;
	}	

	function parse_alias($arr)
	{
		return $this->show(array("id" => $arr["alias"]["target"]));
	}

	function show($arr)
	{
		if (!is_oid($arr["id"]) || !$this->can("view", $arr["id"]))
		{
			return "";
		}
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");

		$from = time();
		$to = mktime(0,0,0,date("m")+1, 1, date("Y"));

		foreach($ob->connections_from(array("type" => "RELTYPE_CALENDAR")) as $c)
		{
			$cal = $c->to();
			$this->_insert_pdata($cal);

			$events = "";

			// now vacancies
			$ol = new object_list(array(
				"parent" => $cal->prop("event_folder"),
				new object_list_filter(array(
					"logic" => "OR",
					"conditions" => array(
						new object_list_filter(array(
							"logic" => "AND",
							"conditions" => array(
								"class_id" => CL_CALENDAR_VACANCY,
							)
						)),
						new object_list_filter(array(
							"logic" => "AND",
							"conditions" => array(
								"class_id" => CL_CRM_MEETING,
								"flags" => array(
									"mask" => OBJ_WAS_VACANCY,
									"flags" => OBJ_WAS_VACANCY
								)
							)
						))
					)
				)),
				new object_list_filter(array("non_filter_classes" => CL_CRM_MEETING)),
				"sort_by" => "planner.start",
				"CL_CRM_MEETING.start1" => new obj_predicate_compare(OBJ_COMP_BETWEEN, $from, $to)
			));
			foreach($ol->arr() as $event)
			{
				if ($event->class_id() == CL_CALENDAR_VACANCY)
				{
					$this->vars(array(
						"reg_link" => $this->mk_my_orb("register_to_event", array("id" => $ob->id(), "cal" => $cal->id(), "event" => $event->id(), "section" => aw_global_get("section"))),
						"date" => get_lc_date($event->prop("start"),LC_DATE_FORMAT_LONG_FULLYEAR),
						"time_from" => date("H:i", $event->prop("start")),
						"time_to" => date("H:i", $event->prop("end"))
					));
					$events .= $this->parse("VACANCY");
				}
				else
				{
					$this->vars(array(
						"reg_link" => $this->mk_my_orb("register_to_event", array("id" => $ob->id(), "cal" => $cal->id(), "event" => $event->id(), "section" => aw_global_get("section"))),
						"date" => get_lc_date($event->prop("start1"),LC_DATE_FORMAT_LONG_FULLYEAR),
						"time_from" => date("H:i", $event->prop("start1")),
						"time_to" => date("H:i", $event->prop("end"))
					));
					$events .= $this->parse("TAKEN");
				}
			}

			$this->vars(array(
				"VACANCY" => $events,
				"TAKEN" => ""
			));
			$calstr .= $this->parse("CALENDAR");
		}

		$this->vars(array(
			"CALENDAR" => $calstr
		));
		return $this->parse();
	}

	/**

		@attrib name=register_to_event nologin="1"

		@param id required type=int acl=view
		@param cal required type=int acl=view
		@param event required type=int acl=view;edit;delete
		@param section optional 
		@param fail_fld optional 
		@param vals optional 

	**/
	function register_to_event($arr)
	{
		// show the reg form
		$this->read_template("reg_form.tpl");

		$person = get_instance(CL_CRM_PERSON);

		$cal = obj($arr["cal"]);
		$event = obj($arr["event"]);
		$this->_insert_pdata($cal);

		$this->vars(array(
			"date" => get_lc_date($event->prop("start")),
			"time_from" => date("H:i", $event->prop("start")),
			"time_to" => date("H:i", $event->prop("end")),
			"reforb" => $this->mk_reforb("submit_register_event", array(
				"id" => $arr["id"],
				"cal" => $arr["cal"],
				"event" => $arr["event"],
				"section" => $arr["section"]
			))
		));
		if (is_array($arr["vals"]))
		{
			$this->vars($arr["vals"]);
		}

		if (is_array($arr["fail_fld"]) && count($arr["fail_fld"]) > 0)
		{
			foreach($arr["fail_fld"] as $fld)
			{
				$this->vars(array(
					"FAIL_".$fld => $this->parse("FAIL_".$fld)
				));
			}
		}

		return $this->parse();
	}

	/**

		@attrib name=submit_register_event nologin="1"

	**/
	function submit_register_event($arr)
	{
		$pass = true;
		$req = array("first_name", "last_name", "code", "email", "phone");
		$dat = array(
			"first_name" => t("Eesnimi:\n"), 
			"last_name" => t("Perekonnanimi:\n"), 
			"email" => t("E-mai:\n"),
			"phone" => t("Telefon:\n"),
			"code" => t("Isikukood:\n"),
			"content" => t("Kohtumise sisu:\n")
		);
		$fail_fld = array();
		foreach($req as $req_f)
		{
			if (trim($arr["reg"][$req_f]) == "")
			{
				$pass = false;
				$fail_fld[] = $req_f;
			}
			else
			if ($req_f == "code")
			{
				// personal code is 3780607xxxx
				if (strlen(trim($arr["reg"][$req_f])) != 11)
				{
					$pass = false;
					$fail_fld[] = $req_f;
				}
			}
		}

		if (!$pass)
		{
			aw_session_set("no_cache", 1);
			return $this->mk_my_orb("register_to_event",array(
				"id" => $arr["id"],
				"cal" => $arr["cal"],
				"event" => $arr["event"],
				"section" => $arr["section"],
				"fail_fld" => $fail_fld,
				"vals" => $arr["reg"]
			));
		}

		$conf = obj($arr["id"]);

		// morph the event to a crm_meeting and
		$calvac = get_instance(CL_CALENDAR_VACANCY);
		$new_id = $calvac->reserve_slot(array(
			"id" => $arr["event"],
			"clid" => CL_CRM_MEETING,
			"ret_id" => true
		));
		$o = obj($new_id);
		// put entered data in description.
		
		$desc = "";
		foreach($dat as $fld => $f_desc)
		{
			$desc .= $f_desc.$arr["reg"][$fld]."\n\n";
		}
		$o->set_prop("content", $desc);
		$o->set_name($arr["reg"]["first_name"]." ".$arr["reg"]["last_name"]." ".$arr["email"]." ".$arr["phone"]);
		$o->set_comment($conf->prop("event_comment"));
		$o->set_meta("register_session", session_id());
		$o->save();

		$cal = obj($arr["cal"]);
		$event = obj($arr["event"]);
		$owner_user = $cal->get_first_obj_by_reltype(8 /* CL_PLANNER.RELTYPE_CALENDAR_OWNERSHIP */);
		$owner_person = $owner_user->get_first_obj_by_reltype(2 /* CL_USER.RELTYPE_PERSON */);
		$owner_person->connect(array(
			"to" => $o->id(),
			"reltype" => 8 // CRM_PERSON.RELTYPE_PERSON_MEETING
		));

		$this->do_create_person($arr, $o);

		$this->do_send_confirm_mail($arr, $o, $arr["reg"]["email"]);

		// show confirmation page
		return $this->mk_my_orb("register_confirm", array(
			"id" => $arr["id"],
			"cal" => $arr["cal"],
			"event" => $o->id(),
			"section" => $arr["section"],
		));
	}

	/**

		@attrib name=register_confirm nologin="1"

		@param id required type=int acl=view
		@param cal required type=int acl=view
		@param event required type=int acl=view;edit;delete
		@param section optional 
	**/
	function register_confirm($arr)
	{
		$this->read_template("reg_confirm.tpl");
		
		$person = get_instance(CL_CRM_PERSON);
		
		$cal = obj($arr["cal"]);
		$event = obj($arr["event"]);

		if ($event->meta("register_session") != session_id())
		{
			$this->read_template("reg_no_sess.tpl");
			return $this->parse();
		}

		$this->_insert_pdata($cal);
		$this->vars(array(
			"date" => get_lc_date($event->prop("start1")),
			"time_from" => date("H:i", $event->prop("start1")),
			"time_to" => date("H:i", $event->prop("end")),
			"content" => nl2br($event->prop("content"))
		));
		
		return $this->parse();
	}

	/** sends confirmation e-mail if the registration object is so configured

		@param id required 
	**/
	function do_send_confirm_mail($arr, $event, $mail_to)
	{
		$o = obj($arr["id"]);
		if ($o->prop("mail_from_addr") != "" && $o->prop("mail_subj") != "")
		{
			$cal = obj($arr["cal"]);
			$owner_user = $cal->get_first_obj_by_reltype(8 /* CL_PLANNER.RELTYPE_CALENDAR_OWNERSHIP */);
			$owner_person = $owner_user->get_first_obj_by_reltype(2 /* CL_USER.RELTYPE_PERSON */);

			$person = get_instance(CL_CRM_PERSON);
			$pdata = $person->fetch_person_by_id(array(
				"id" => $owner_person->id()
			));

			$content = $o->prop("mail_content");
			$vars = array(
				"#reg_data#" => preg_replace("/#(\w*)#/","",$event->prop("content")),
				"#name#" => $pdata["name"],
				"#phone#" => $pdata["phone"],
				"#email#" => $pdata["email"],
				"#addr#" => $pdata["address"],
				"#date#" => get_lc_date($event->prop("start1")),
				"#time_from#" => date("H:i", $event->prop("start1")),
				"#time_to#" => date("H:i", $event->prop("end"))
			);

			foreach($vars as $vn => $vv)
			{
				$content = str_replace($vn, $vv, $content);
			}

			// calendar owner and email addr
			$from = $o->prop("mail_from_addr");
			if ($o->prop("mail_from_name") != "")
			{
				$from = $o->prop("mail_from_name")." <".$o->prop("mail_from_addr").">";
			}
			send_mail($mail_to, $o->prop("mail_subj"), $content, "From: $from\n");
			send_mail($pdata["email"], $o->prop("mail_subj"), $content, "From: $from\n");
		}
	}

	function do_create_person($arr, $o)
	{
		// see is there is a person that has the same name and code
		$ol = new object_list(array(
			"class_id" => CL_CRM_PERSON,
			"firstname" => $arr["reg"]["first_name"],
			"lastname" => $arr["reg"]["last_name"],
			"personal_id" => $arr["reg"]["code"]
		));

		aw_disable_acl();
		if ($ol->count() == 0)
		{
			$u = get_instance(CL_USER);
			$u_o = obj($u->get_current_user());

			$p = obj();
			$p->set_class_id(CL_CRM_PERSON);
			$p->set_parent($u_o->parent());
			$p->set_name($arr["reg"]["first_name"]." ".$arr["reg"]["last_name"]);
			$p->set_prop("personal_id", $arr["reg"]["code"]);
			$p->set_prop("firstname", $arr["reg"]["first_name"]);
			$p->set_prop("lastname", $arr["reg"]["last_name"]);
			aw_disable_acl();
			$p->save();
			aw_restore_acl();
			// now, connect user to person
			$u_o->connect(array(
				"to" => $p->id(),
				"reltype" => 2
			));
		}
		else
		{
			$p = $ol->begin();
		}

		// now connect person to event
		$p->connect(array(
			"to" => $o->id(),
			"reltype" => 8 // CRM_PERSON.RELTYPE_PERSON_MEETING
		));
		aw_restore_acl();

	}

	function _insert_pdata($cal)
	{
		$person = get_instance(CL_CRM_PERSON);
		$owner_user = $cal->get_first_obj_by_reltype(8 /* CL_PLANNER.RELTYPE_CALENDAR_OWNERSHIP */);
		$pdata = array();
		if ($owner_user)
		{
			$owner_person = $owner_user->get_first_obj_by_reltype(2 /* CL_USER.RELTYPE_PERSON */);

			$pdata = $person->fetch_person_by_id(array(
				"id" => $owner_person->id()
			));
		}

		$this->vars(array(
			"person" => $pdata["name"],
			"person_mail" => $pdata["email"],
			"person_phone" => $pdata["phone"],
			"person_rank" => $pdata["rank"],
			"person_address" => $pdata["address"]
		));
	}
}
?>
