<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/calendar/calendar_registration_form_conf.aw,v 1.43 2008/11/06 18:51:54 markop Exp $
// calendar_registration_form_conf.aw - Kalendri s&uuml;ndmusele registreerimise vorm 
/*
HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_ALIAS_ADD_TO, CL_MENU, on_rfconnect_to)
HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_ALIAS_ADD_TO, CL_CALENDAR_REGISTRATION_FORM_CONF, on_connect_event)

@classinfo syslog_type=ST_CALENDAR_REGISTRATION_FORM_CONF relationmgr=yes no_status=1 no_comment=1 maintainer=kristo

@default table=objects
@default group=general

@default field=meta 
@default method=serialize

@property event type=relpicker reltype=RELTYPE_EVENT
@caption S&uuml;ndmus millele registreeruda

@property form type=relpicker reltype=RELTYPE_WEBFORM
@caption Registreerumisvorm

//@property ot type=relpicker reltype=RELTYPE_OT
//@caption Registreerumisvormi objektit&uuml;&uuml;p

@property max_pers type=textbox datatype=int
@caption Maksimaalne registreerujate arv

@property person_folder type=relpicker reltype=RELTYPE_FOLDER
@caption Isikute kaust

@property show_content type=select
@caption Kuva dokumendis s&uuml;ndmuse sisu

@property link_caption type=textbox 
@caption Lingi nimi

@property redirect type=textbox
@caption Kuhu suunata peale t&auml;itmist

@property entry_check type=select multiple=1
@caption Sisestuste kontroll

@groupinfo emails caption="E-mailid"
@default group=emails

@property mail_to_user type=checkbox ch_value=1 default=1
@caption Saada regisreerijale kinnitus

@property mail_to_addr type=textbox 
@caption Kellele lisaks mail saata

@property mail_from_name type=textbox 
@caption Maili saatja nimi

@property mail_from_addr type=textbox 
@caption Maili saatja aadress

@property mail_subj type=textbox 
@caption Maili pealkiri

@property mail_legend type=text store=no
@caption Asenduste legend

@property mail_content type=textarea rows=20 cols=80
@caption Maili sisu


@reltype EVENT value=1 clid=CL_CRM_MEETING,CL_TASK,CL_CRM_CALL
@caption S&uuml;ndmus

//@reltype OT value=2 clid=CL_OBJECT_TYPE
//@caption Registreerumisvormi t&uuml;&uuml;p

@reltype REG_ENTRY value=3 clid=CL_CALENDAR_REGISTRATION_FORM_CONF
@caption Sisestus

@reltype FOLDER value=4 clid=CL_MENU
@caption Isikute kaust

@reltype WEBFORM value=5 clid=CL_WEBFORM
@caption S&uuml;ndmuse vorm

*/

class calendar_registration_form_conf extends class_base
{
	function calendar_registration_form_conf()
	{
		$this->init(array(
			"tpldir" => "applications/calendar/calendar_registration_form_conf",
			"clid" => CL_CALENDAR_REGISTRATION_FORM_CONF
		));
	}
	
	function on_rfconnect_to($arr)
	{
		$conn = $arr["connection"];
		//arr($arr);
		if($conn->prop("from.class_id") == CL_CALENDAR_REGISTRATION_FORM_CONF && $conn->prop("reltype") == 4)
		{
			$dir = $conn->to();
			$group = obj(group::get_non_logged_in_group());
			$rights = array("can_add" => 1, "can_view" => 1);
			$dir->acl_set($group, $rights);
			$dir->save();
		}
	}

	function on_connect_event($arr)
	{
		$c = $arr['connection'];
		$valid_classes = array(CL_CRM_MEETING, CL_TASK, CL_CRM_CALL);
		if ( in_array( $c->prop('from.class_id'), $valid_classes ) )
		{
			$o = $c->to();
			$o->connect(array(
				'to' => $c->from(),
				'type' => 'RELTYPE_EVENT'
			));
		}
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "entry_check":
				$opts = array();
				$form = $arr["obj_inst"]->prop("form");
				if(is_oid($form) && $this->can("view", $form))
				{
					$webform = obj($form);
					if($cfgform = $webform->get_first_obj_by_reltype("RELTYPE_CFGFORM"))
					{
						$no_check = array("name", "status", "comment", "person_id");
						$props = $this->get_property_group(array(
							"clid" => CL_CALENDAR_REGISTRATION_FORM,
							"group" => "general",
						));
						$props2 = safe_array($cfgform->meta("cfg_proplist"));
						foreach($props as $key => $prp)
						{
							if(!in_array($key, $no_check) && isset($props2[$key]))
							{
								$opts[$key] = $prp["caption"];
							}
						}
					}
				}
				$prop["options"] = array(0 => "-- vali --") + $opts;
				break;
				
			case "mail_legend":
				$prop["value"] = t("Meili sisus on v&otilde;imalik kasutada j&auml;rgnevaid asendusi:<br><br>#reg_data# - kalendri s&uuml;ndmuse sisu<br>
					#date# - kohtumise kuup&auml;ev<br>
					#time_from# - kohtumise alguskellaaeg<br>
					#time_to# - kohtumise k&otilde;ppkellaaeg<br>lisaks v&otilde;ib kasutada elemente andmete vormist, trellidega eraldatult");
				break;

			case "show_content":
				$prop["options"] = array(
					0 => t("&Auml;ra kuva"),
					1 => t("Kuva dokumendis s&uuml;ndmuse infot")
				);
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

	function callback_post_save($arr)
	{
		if ( $arr['new'] )
		{
			$name = sprintf( t('Isikute kaust (%s)'), $arr['obj_inst']->name() );
			$o = new object();
			$o->set_name($name);
			$o->set_parent($arr['obj_inst']->parent());
			$o->set_class_id(CL_MENU);
			$o->save();
			$arr['obj_inst']->connect(array(
				'to' => $o,
				'type' => 'RELTYPE_FOLDER'
			));
			$arr['obj_inst']->set_prop('person_folder', $o->id());
			$arr['obj_inst']->save();

		}

	}

	function parse_alias($arr)
	{
		$id = $arr["alias"]["target"];
		$obj = new object($id);
		
		$f_id = $obj->prop("form");
		if(is_oid($f_id) && $this->can("view", $f_id))
		{
			$form = obj($f_id);
			$form_i = $form->instance();
			return $form_i->show(array(
				"id" => $f_id,
				"ef" => $id,
				"link" => $arr["alias"]["aliaslink"],
			));
		}
		return false;
	}
	
	function _insert_event_inf($e, $o)
	{
		$start = $e->class_id() == CL_CRM_MEETING ? $e->prop("start") : $e->prop("start");
		$end = $e->prop("end");
		$this->vars(array(
			"ev_title" => $e->name(),
			"ev_start" => aw_locale::get_lc_date($start, LC_DATE_FORMAT_LONG_FULLYEAR)." ".date("H:i",$end),
			"ev_end" => aw_locale::get_lc_date($end, LC_DATE_FORMAT_LONG_FULLYEAR)." ".date("H:i",$end),
			"ev_content" => nl2br($e->prop("content"))
		));

		$ct = "";
		if ($o->prop("show_content") == 1)
		{
			$ct = $this->parse("SHOW_CONTENT");
		}
		$this->vars(array(
			"SHOW_CONTENT" => $ct
		));
	}

	/**

		@attrib name=submit_register nologin=1 

	**/
	function submit_register($arr)
	{
		if(strpos(strtolower($_SERVER["REQUEST_URI"]), "/automatweb") !== false && (!is_oid($arr["id"]) || !$this->can("view", $arr["id"])))
		{
			error::raise(array(
				"id" => "ERR_NO_EVENT",
				"msg" => t("Veebivorm s&uuml;ndmusele registreerimise vormiga t&ouml;&ouml;tab ainult l&auml;bi s&uuml;ndmusele registreerimise"),
			));
		}
		$ob = new object($arr["id"]);
		$event = $ob->get_first_obj_by_reltype("RELTYPE_EVENT");
		$f_id = $ob->prop("form");

		if(is_oid($f_id) && $this->can("view", $f_id))
		{
			$form = obj($f_id);
			$ot = $form->get_first_obj_by_reltype("RELTYPE_OBJECT_TYPE");
		}
		else
		{
			$ot = $ob->get_first_obj_by_reltype("RELTYPE_OT");
			if (!$event || !$ot)
			{
				error::raise(array(
					"id" => "ERR_NO_REQ",
					"msg" => sprintf(t("calendar_registration_form::show(%s): event and object type must be related!"), $arr["id"])
				));
			}
		}

		// validate data
		//$cf = get_instance(CL_CFGFORM);
		//$props = $cf->get_props_from_cfgform(array("id" => $ot->prop("use_cfgform")));
		$calendar_registration_form_i = get_instance(CL_CALENDAR_REGISTRATION_FORM);
		$calendar_registration_form_i->init_class_base();
		$is_valid = $calendar_registration_form_i->validate_data(array(
			//"props" => $props,
			"request" => &$arr,
			"cfgform_id" => $ot->prop("use_cfgform"), 
		));

		if (is_array($is_valid) && count($is_valid))
		{
			aw_session_set("wf_errors", $is_valid);
			aw_session_set("no_cache", 1);
			aw_session_set("wf_data", $arr);
			$redirect = $arr["return_url"];
			return (strpos(strtolower($redirect), "http://") !== false ? $redirect : (substr($redirect, 0, 1) == "/" ?  aw_ini_get("baseurl").$redirect : aw_ini_get("baseurl")."/".$redirect));
		}

		$person_id = $this->do_create_person(array(
			"arr" => $arr, 
			"event" => $event, 
			"reg" => $ob,
		));

		// save the damn object
		$o = obj();
		$o->set_class_id(CL_CALENDAR_REGISTRATION_FORM);
		$o->set_parent($event->id());
		foreach($o->get_property_list() as $pn => $pd)
		{
			$o->set_prop($pn, $arr[$pn]);
		}
		$o->set_meta("object_type", $ot->id());
		$o->set_meta("cfgform_id", $ot->prop("use_cfgform"));
		$o->set_prop("person_id", $person_id);
		$o->set_name($arr["firstname"]." ".$arr["lastname"]);
		$o->save();
		$cfgform = obj($ot->prop("use_cfgform"));
		$prplist = safe_array($cfgform->meta("cfg_proplist"));


		$o->connect(array(
			"to" => $event->id(),
			"reltype" => "RELTYPE_DATA"
		));

		$ob->connect(array(
			"to" => $o->id(),
			"reltype" => "RELTYPE_REG_ENTRY"
		));

		$this->do_send_confirm_mail(array(
			"arr" => $arr, 
			"event" => $event, 
			"mail_to" => $arr["email"], 
			"data_o" => $o,
			"prplist" => $prplist,
			"ot" => $ot->id(),
		));
		$redirect = $ob->prop("redirect");
		$rval = strpos(strtolower($redirect), "http://") !== false ? $redirect : (substr($redirect, 0, 1) == "/" ?  aw_ini_get("baseurl").$redirect : aw_ini_get("baseurl")."/".$redirect);
		return !empty($arr["subaction"]) ? $this->mk_my_orb("show_form", array("id" => $form->id(), "fid" => $o->id(), "url" => $rval), CL_WEBFORM) : $rval;
		/*
		}
		return $this->mk_my_orb("show_data", array("id" => $arr["id"], "data" => $o->id(), "section" => aw_global_get("section")));
		*/
	}

	function do_create_person($args)
	{
		extract($args);
		/*
		firstname -> crm_person
		lastname -> crm_person
		co_name -> crm_person -> crm_company
		address -> crm_person -> comment
		phone -> crm_person -> phone -> crm_phone -> name
		fax -> -crm_person -> phone -> crm_phone -> name, type = fax
		email -> crm_person -> email -> ml_member
		*/
		$form_id = $reg->prop("form");
		if(is_oid($form_id) && $this->can("view", $form_id))
		{
			$form = obj($form_id);
			$cfgform = $form->get_first_obj_by_reltype("RELTYPE_CFGFORM");
			$props = $cfgform->meta("cfg_proplist");
			foreach($props as $prop)
			{
				if($prop["type"] == "checkbox")
				{
					$ch_prop = $prop;
					break;
				}
			}
			if($arr[$ch_prop["name"]] == 1 && is_oid($ch_prop["folder_id"]) && $this->can("view", $ch_prop["folder_id"]))
			{
				$folder_id = $ch_prop["folder_id"];
			}
		}
		//arr($props);
		// see is there is a person that has the same name and code
		$checks = safe_array($reg->prop("entry_check"));
		unset($checks[0]);
		
		$tol = new object_list(array(
			"class_id" => CL_CRM_PERSON,
			"firstname" => $arr["firstname"],
			"lastname" => $arr["lastname"],
		));
		$ol = new object_list();
		
		foreach($tol->arr() as $tmp_o)
		{
			if (is_oid($tmp_o->prop("email")) && $this->can("view", $tmp_o->prop("email")))
			{
				$tmp_addr = obj($tmp_o->prop("email"));
				if ($tmp_addr->prop("mail") == $arr["email"])
				{
					$ol->add($tmp_o->id());
				}
			}
		}


		aw_disable_acl();
		$parent = $reg->prop("person_folder");
		if ($ol->count() == 0 && is_oid($parent))
		{
			$tmp = $arr;
			foreach($tmp as $key => $val)
			{
				$arr[$key] = trim($val);
			}
			$fn = $arr["firstname"]." ".$arr["lastname"];
			$p = obj();
			$p->set_class_id(CL_CRM_PERSON);
			$p->set_parent($parent);
			$p->set_name($fn);
			$p->set_prop("firstname", $arr["firstname"]);
			$p->set_prop("lastname", $arr["lastname"]);
			$p->set_prop("comment", $arr["address"]);
			$p->save();
			$phones = array();
			// create email addr
			if ($arr["email"] != "")
			{
				$e = obj();
				$e->set_class_id(CL_ML_MEMBER);
				$e->set_parent($folder_id ? $folder_id : $p->id());
				$e->set_name($fn." <".$arr["email"].">");
				$e->set_prop("name", $fn);
				$e->set_prop("mail", $arr["email"]);
				$e->save();
				$p->connect(array(
					"to" => $e->id(),
					"reltype" => 11 // RELTYPE_EMAIL
				));
				$p->set_prop("email", $e->id());
				$p->save();
			}
			if($arr["phone"] != "")
			{
				$e = obj();
				$e->set_class_id(CL_CRM_PHONE);
				$e->set_parent($p->id());
				$e->set_name($arr["phone"]);
				$e->set_prop("name", $arr["phone"]);
				$e->save();
				$p->connect(array(
					"to" => $e->id(),
					"reltype" => 13 // RELTYPE_PHONE
				));
				$phones[$e->id()] = $e->id();
			}
			if($arr["fax"] != "")
			{
				$e = obj();
				$e->set_class_id(CL_CRM_PHONE);
				$e->set_parent($p->id());
				$e->set_name($arr["fax"]);
				$e->set_prop("name", $arr["fax"]);
				$e->set_prop("type", "fax");
				$e->save();
				$p->connect(array(
					"to" => $e->id(),
					"reltype" => 13, // RELTYPE_PHONE
				));
				$phones[$e->id()] = $e->id();
			}
			// if the user entered a company name, try for that
			if ($arr["co_name"] != "")
			{
				$co_ol = new object_list(array(
					"class_id" => CL_CRM_COMPANY,
					"name" => $arr["co_name"],
				));
				if ($co_ol->count() == 0)
				{
					$co = obj();
					$co->set_class_id(CL_CRM_COMPANY);
					$co->set_parent($parent);
					$co->set_name($arr["co_name"]);
					$co->save();
				}
				else
				{
					$co = $co_ol->begin();
				}

				$p->add_work_relation(array("org" => $co->id()));
			}
			$p->set_prop("phone", $phones);
			$p->save();
		}
		else
		{
			$p = $ol->begin();
		}
		$id = "";
		// now connect person to event
		if(is_object($p))
		{
			$p->connect(array(
				"to" => $event->id(),
				"reltype" => 8 // CRM_PERSON.RELTYPE_PERSON_MEETING
			));
			$id = $p->id();
		}
		aw_restore_acl();
		return $id;
	}

	/** sends confirmation e-mail if the registration object is so configured

		@param id required 
	**/
	function do_send_confirm_mail($args)
	{
		extract($args);
		$o = obj($arr["id"]);
		
		$webform = obj($o->prop("form"));
		$emails = $webform->connections_from(array(
			"type" => "RELTYPE_EMAIL",
		));
		// calendar owner and email addr
		$from = $o->prop("mail_from_addr");
		if ($o->prop("mail_from_name") != "")
		{
			$from = $o->prop("mail_from_name")." <".$o->prop("mail_from_addr").">";
		}
		$nm = aw_global_get("global_name");
		$subj = $o->prop("mail_subj");
		// if subject is not configured, then use reg. form name as subject
		if (empty($subj))
		{
			$subj = $o->name();
		}
		if(!empty($nm))
		{
			$from = $nm;
		}
		$tmp = $arr;
		
		$parsed_fields = array();

		$cls = get_instance(CL_CLASSIFICATOR);
		$no_trans = array("submit", "reset", "text", "button");
		foreach($tmp as $key => $val)
		{
			if(!array_key_exists($key, $prplist) || in_array($prplist[$key]["type"], $no_trans) || empty($val))
			{
				continue;
			}
			if($prplist[$key]["type"] == "date_select")
			{
				$val = $val["day"].".".$val["month"].".".$val["year"];
			}
			if($prplist[$key]["type"] == "classificator")
			{
				list($choices,,) = $cls->get_choices(array(
					"clid" => CL_CALENDAR_REGISTRATION_FORM,
					"name" => $key,
					"object_type_id" => $ot,
				));
				$choices = $choices->names();
				$vals = array();
				$val = is_array($val) ? $val : array($val);
				foreach($val as $valx)
				{
					$vals[] = $choices[$valx];
				}
				$val = implode(", ", $vals);
			}
			$body .= $prplist[$key]["caption"].": ".$val."\n";
			// Besides composing the body string, lets remember those parsed values in an array too
			// so i can use them later to replace the variables in mail content
			$parsed_fields[$key] = $val;
		}

		$content = $o->prop("mail_content");
		$vars = array(
			"#reg_data#" => preg_replace("/#(\w*)#/","",$event->prop("content")),
			"#date#" => get_lc_date($event->prop("start1")),
			"#time_from#" => date("H:i", $event->prop("start1")),
			"#time_to#" => date("H:i", $event->prop("end")),
		);

		foreach($data_o->properties() as $k => $v)
		{
			$value = $v;
			if (is_oid($v) && $this->can("view", $v))
			{
				$tmp = new object($v);
				$value = $tmp->name();
			}
			// deal with arrays as well
			if (is_array($v))
			{
				$values = array();
				foreach($v as $v_oid)
				{
					if (is_oid($v_oid) && $this->can("view",$v_oid))
					{
						$tmp = new object($v_oid);
						$values[] = $tmp->name();
					};
				}
				$value = join(',',$values);
			};

			if ( array_key_exists($k, $parsed_fields) )
			{
				$value = $parsed_fields[$k];
			}
			$vars["#".$k."#"] = $value;
		}

		foreach($vars as $vn => $vv)
		{
			$content = str_replace($vn, $vv, $content);
		}
		
		// get rid of these variables in the mail_content string which aren't parsed:
		$content = preg_replace("/(#)(\w+?)(\d+?)(v|k|p|)(#)/i", '', $content);

		// sending emails:
		foreach($emails as $eml)
		{
			$email = $eml->to();
			send_mail($email->prop("mail"), $subj, $body, "From: $from\n");
		}
		if ($o->prop("mail_to_addr") != "")
		{
			send_mail($o->prop("mail_to_addr"), $subj, $content, "From: $from\n");
		}
		if($o->prop("mail_to_user") == 1)
		{
			send_mail($mail_to, $subj, $content, "From: $from\n");
		}
	}

	/**

		@attrib name=show_data nologin="1"

		@param id required type=int acl=view
		@param data required type=int acl=view

	**/
	function show_data($arr)
	{
		$this->read_template("show_data.tpl");
		
		$ob = new object($arr["id"]);
		$event = $ob->get_first_obj_by_reltype("RELTYPE_EVENT");
		$ot = $ob->get_first_obj_by_reltype("RELTYPE_OT");
		if (!$event || !$ot)
		{
			error::raise(array(
				"id" => "ERR_NO_REQ",
				"msg" => sprintf(t("calendar_registration_form::show_data(%s): event and object type must be related!"), $arr["id"])
			));
		}

		$this->_insert_event_inf($event, $ob);

		$d_o = obj($arr["data"]);

		$cf = get_instance(CL_CFGFORM);
		$props = $cf->get_props_from_ot(array(
			"ot" => $ot->id(),
			"values" => $d_o->properties(),
			"for_show" => true
		));

		foreach($props as $pn => $pd)
		{
			$this->vars(array(
				"caption" => $pd["caption"],
				"value" => $pd["value"]
			));
			$l .= $this->parse("ROW");
		}

		$this->vars(array(
			"ROW" => $l
		));
		return $this->parse();
	}

	function get_count_for_event($event)
	{
		$conns = $event->connections_to(array(
			"from.class_id" => CL_CALENDAR_REGISTRATION_FORM,
			"type" => 3 // regform.RELTYPE_DATA
		));

		/*
		$conns = $event->connections_to(array(
			"from.class_id" => CL_CRM_PERSON,
			"type" => 8 // CL_CRM_PERSON.RELTYPE_PERSON_MEETING
		));
		*/
		return count($conns);
	}
}
?>
