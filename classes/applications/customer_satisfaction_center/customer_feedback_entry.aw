<?php

// customer_feedback_entry.aw - Kliendi tagasiside sisestus
/*

@classinfo syslog_type=ST_CUSTOMER_FEEDBACK_ENTRY relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo

@tableinfo aw_customer_feedback index=aw_oid master_index=brother_of master_table=objects

@default table=objects
@default group=general

@property rules type=text store=no
@caption Reeglid

@property person_t type=textbox field=meta method=serialize
@caption Isik

@property co_t type=textbox field=meta method=serialize
@caption Organisatsioon

@property fb_class type=textbox size=5 table=aw_customer_feedback field=aw_d_class
@caption Klassi t&uuml;&uuml;p

@property fb_object type=text store=no
@caption Objekti nimi

@property fb_object_grp type=textbox table=aw_customer_feedback field=aw_object_grp
@caption Objekti grupp

@property comment_ta type=textarea rows=10 cols=50 table=aw_customer_feedback field=aw_comment_ta
@caption Kommentaar

@property file_1 type=text
@caption Failid

@property seriousness type=chooser table=aw_customer_feedback field=aw_seriousness
@caption T&otilde;sidus

@property fb_type type=chooser table=aw_customer_feedback field=aw_fb_type
@caption Soovin tagasisidet

@property fb_email type=textbox table=aw_customer_feedback field=aw_fb_email
@caption Tagasiside meiliaadress

@property fb_phone type=textbox table=aw_customer_feedback field=aw_fb_phone
@caption Tagasiside telefon


@default group=dev_status
	@property dev_status type=select table=aw_customer_feedback field=aw_dev_status default=1
	@caption Arendaja staatus

	@property dev_deadline type=datepicker time=0 from=now table=aw_customer_feedback field=aw_dev_deadline
	@caption Arendaja t&auml;htaeg


@groupinfo dev_status caption="Arendaja andmed"


@reltype PERSON value=1 clid=CL_CRM_PERSON
@caption Isik

@reltype CO value=2 clid=CL_CRM_COMPANY
@caption Organisatsioon

@reltype OBJECT value=3 clid=
@caption Objekti

@reltype FILE1 value=4 clid=CL_FILE
@caption Fail 1

@reltype FILE2 value=5 clid=CL_FILE
@caption Fail 2

@reltype FILE3 value=6 clid=CL_FILE
@caption Fail 3


*/

class customer_feedback_entry extends class_base
{
	function customer_feedback_entry()
	{
		$this->init(array(
			"tpldir" => "applications/customer_satisfaction_center/customer_feedback_entry",
			"clid" => CL_CUSTOMER_FEEDBACK_ENTRY
		));

		$this->severities = array(
			1 => t("Fataalne viga"),
			2 => t("Segav viga"),
			3 => t("Tr&uuml;kiviga"),
			4 => t("Soovitus")
		);

		$this->fb_types = array(
			1 => t("Meilile"),
			2 => t("Telefonile"),
			3 => t("Ei soovi")
		);


		$this->statuses = array(
			1 => t("Lahtine"),
			2 => t("Tegemisel"),
			3 => t("Valmis"),
			4 => t("Testitud"),
			5 => t("Suletud"),
			6 => t("Vale teade"),
			7 => t("Kordamatu"),
			8 => t("Parandamatu"),
			9 => t("Ei paranda"),
			10 => t("Vajab tagasisidet")
		);

		classload("core/icons");
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "name":
				return PROP_IGNORE;

			case "rules":
				return PROP_IGNORE;
				$prop["value"] = t("&Auml;ra jama, mees! m&otilde;tled ka mis teed v&auml;?");
				break;

			case "person_t":
				$prop["type"] = "text";
				if(empty($prop["value"]))
				{
					$p = get_current_person();
					$prop["value"] = $p->name();
				}
				break;

			case "co_t":
				$prop["type"] = "text";
				if(empty($prop["value"]))
				{
					$p = get_current_company();
					$prop["value"] = $p->name();
				}
				break;

			case "fb_class":
				if (isset($arr["request"]["d_class"]))
				{
					$prop["value"] = clid_for_name($arr["request"]["d_class"]);
				}
				$prop["type"] = "text";

				if (!empty($prop["value"]))
				{
					$var = "classes.{$prop["value"]}.name";
					$prop["value"] = aw_ini_isset($var) ? aw_ini_get($var) : t("[m&auml;&auml;ramata]");
				}
				break;

			case "fb_object_grp":
				if (isset($arr["request"]["object_grp"]))
				{
					$prop["value"] = $arr["request"]["object_grp"];
				}
				$prop["type"] = "text";

				if (isset($arr["request"]["d_class"]))
				{
					$d_clid = clid_for_name($arr["request"]["d_class"]);
					if (!$d_clid)
					{
						if (isset($arr["request"]["d_obj"]) && $this->can("view", $arr["request"]["d_obj"]))
						{
							$tmp = obj($arr["request"]["d_obj"]);
							$d_clid = $tmp->class_id();
						}

						if (!$d_clid)
						{
							$d_clid = $arr["obj_inst"]->prop("d_class");

							if (!$d_clid)
							{
								$o = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_OBJECT");
								if ($o)
								{
									$d_clid = $o->class_id();
								}
							}
						}
					}

					if ($d_clid)
					{
						// get the human readable name for the tab
						$o = obj();
						$o->set_class_id($d_clid);
						$grps = $o->get_group_list();
						$prop["value"] = isset($prop["value"]) ? $grps[$prop["value"]]["caption"] : t("[m&auml;&auml;ramata]");
					}
				}
				break;

			case "fb_object":
				if (isset($arr["request"]["d_obj"]))
				{
					$prop["value"] = $arr["request"]["d_obj"];
				}

				if (empty($prop["value"]) && empty($arr["new"]))
				{
					$o = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_OBJECT");
					if ($o)
					{
						$prop["value"] = $o->id();
					}
				}

				if (isset($prop["value"]) and $this->can("view", $prop["value"]))
				{
					$t = obj($prop["value"]);
					$prop["value"] = $t->name();
				}
				break;

			case "seriousness":
				$prop["options"] = $this->severities;
				break;

			case "fb_type":
				$prop["options"] = $this->fb_types;
				break;

			case "fb_phone":
				$p = get_current_person();
				if (empty($prop["value"]))
				{
					$prop["value"] = $p->prop_str("phone.name");
				}
				break;

			case "fb_email":
				$p = get_current_person();
				if (empty($prop["value"]))
				{
					$prop["value"] = $p->prop_str("email.mail");
				}
				break;

			case "file_1":
				$this->_file_1($arr);
				break;

			case "file_1_t":
				if (!empty($arr["new"]))
				{
					return PROP_IGNORE;
				}
				$f = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_FILE1");
				if (!$f)
				{
					return PROP_IGNORE;
				}
				$fi = new file();
				$prop["value"] = html::href(array(
					"url" => $fi->get_url($f->id(), $f->name()),
					"caption" => html::img(array(
						"url" => icons::get_icon_url(CL_FILE,$f->name()),
						"border" => "0"
						))." ".$f->name(),
					"target" => "_blank",
				));
				break;

			case "file_2_t":
				if (!empty($arr["new"]))
				{
					return PROP_IGNORE;
				}
				$f = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_FILE2");
				if (!$f)
				{
					return PROP_IGNORE;
				}
				$fi = new file();
				$prop["value"] = html::href(array(
					"url" => $fi->get_url($f->id(), $f->name()),
					"caption" => html::img(array(
						"url" => icons::get_icon_url(CL_FILE,$f->name()),
						"border" => "0"
						))." ".$f->name(),
					"target" => "_blank",
				));
				break;

			case "file_3_t":
				if (!empty($arr["new"]))
				{
					return PROP_IGNORE;
				}
				$f = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_FILE3");
				if (!$f)
				{
					return PROP_IGNORE;
				}
				$fi = new file();
				$prop["value"] = html::href(array(
					"url" => $fi->get_url($f->id(), $f->name()),
					"caption" => html::img(array(
						"url" => icons::get_icon_url(CL_FILE,$f->name()),
						"border" => "0"
						))." ".$f->name(),
					"target" => "_blank",
				));
				break;

			case "dev_status":
				$prop["options"] = $this->statuses;
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
			case "fb_class":
			case "fb_object_grp":
				return PROP_IGNORE;

			case "person_t":
				if(empty($prop["value"]))
				{
					$p = get_current_person();
					$prop["value"] = $p->name();
				}

			case "co_t":
				if(empty($prop["value"]))
				{
					$p = get_current_company();
					$prop["value"] = $p->name();
				}
				$arr["obj_inst"]->set_prop($prop["name"], $prop["value"]);
				$arr["obj_inst"]->save();
				break;
		}
		return $retval;
	}

	function callback_mod_reforb(&$arr)
	{
		$arr["post_ru"] = post_ru();
		$arr["d_class"] = automatweb::$request->arg("d_class");
		$arr["object_grp"] = automatweb::$request->arg("object_grp");
		$arr["d_obj"] = automatweb::$request->arg("d_obj");
	}

	/**
		@attrib name=redir_new_feedback
		@param d_class required
		@param d_obj optional
		@param object_grp optional
		@param url optional
	**/
	function redir_new_feedback($arr)
	{
		// make sure we have a center
		$center = new customer_feedback_manager();
		$co = $center->init_manager();

		// now direct the user to adding a new feedback under the center
		header("Location: ".html::get_new_url(CL_CUSTOMER_FEEDBACK_ENTRY, $co->id(), array(
			"d_class" => isset($arr["d_class"]) ? $arr["d_class"] : "",
			"d_obj" => isset($arr["d_obj"]) ? $arr["d_obj"] : "",
			"object_grp" => isset($arr["object_grp"]) ? $arr["object_grp"] : "",
			"return_url" => $arr["url"]
		)));
		die();
	}

	function callback_pre_save($arr)
	{
		if (!empty($arr["new"]))
		{
			if (isset($arr["request"]["d_class"]))
			{
				$arr["obj_inst"]->set_prop("fb_class", clid_for_name($arr["request"]["d_class"]));
			}

			if (isset($arr["request"]["object_grp"]))
			{
				$arr["obj_inst"]->set_prop("fb_object_grp", $arr["request"]["object_grp"]);
			}
			$arr["obj_inst"]->set_meta("auth_code", substr(gen_uniq_id(),0,6));
		}
	}

	function do_db_upgrade($table, $field)
	{
		$r = false;

		if ("aw_customer_feedback" === $table)
		{
			if (empty($field))
			{
				$this->db_query("CREATE TABLE aw_customer_feedback(
					aw_oid int primary key,
					aw_d_class int,
					aw_object_grp varchar(255),
					aw_seriousness int,
					aw_fb_type int,
					aw_fb_email varchar(255),
					aw_fb_phone varchar(255))");
				$r = true;
			}
			elseif ("aw_comment_ta" === $field)
			{
				$this->db_add_col($t, array("name" => "aw_comment_ta", "type" => "text"));
				$r = true;
			}
			elseif ("aw_dev_status" === $field)
			{
				$this->db_add_col($t, array("name" => "aw_dev_status", "type" => "text"));
				$r = true;
			}
			elseif ("aw_dev_deadline" === $field)
			{
				$this->db_add_col($t, array("name" => "aw_dev_deadline", "type" => "text"));
				$r = true;
			}
		}

		return $r;
	}

	function callback_post_save($arr)
	{
		if (!empty($arr["new"]))
		{
			$arr["obj_inst"]->connect(array(
				"to" => get_current_person(),
				"type" => "RELTYPE_PERSON"
			));
			$arr["obj_inst"]->connect(array(
				"to" => get_current_company(),
				"type" => "RELTYPE_CO"
			));

			if (isset($arr["request"]["d_obj"]) and $this->can("view", $arr["request"]["d_obj"]))
			{
				$arr["obj_inst"]->connect(array(
					"to" => $arr["request"]["d_obj"],
					"type" => "RELTYPE_OBJECT"
				));
			}
		}

		$fi = new file();
		$f1 = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_FILE1");
		$rv = $fi->add_upload_image("file_1", $arr["obj_inst"]->id(), $f1 ? $f1->id() : 0);
		if ($rv["id"] && (($f1 && $rv["id"] != $f1->id()) || !$f1))
		{
			if ($f1)
			{
				$arr["obj_inst"]->disconnect(array("from" => $f1->id()));
			}
			$arr["obj_inst"]->connect(array("to" => $rv["id"], "type" => "RELTYPE_FILE1"));
		}

		$f2 = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_FILE2");
		$rv = $fi->add_upload_image("file_2", $arr["obj_inst"]->id(), $f2 ? $f2->id() : 0);
		if ($rv["id"] && (($f2 && $rv["id"] != $f2->id()) || !$f2))
		{
			if ($f2)
			{
				$arr["obj_inst"]->disconnect(array("from" => $f2->id()));
			}
			$arr["obj_inst"]->connect(array("to" => $rv["id"], "type" => "RELTYPE_FILE2"));
		}

		$f3 = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_FILE3");
		$rv = $fi->add_upload_image("file_3", $arr["obj_inst"]->id(), $f3 ? $f3->id() : 0);
		if ($rv["id"] && (($f3 && $rv["id"] != $f3->id()) || !$f3))
		{
			if ($f3)
			{
				$arr["obj_inst"]->disconnect(array("from" => $f3->id()));
			}
			$arr["obj_inst"]->connect(array("to" => $rv["id"], "type" => "RELTYPE_FILE3"));
		}

		// if this is a new entry, then send e-mail to support@automatweb.com
		if (!empty($arr["new"]))
		{
			$this->_send_support_mail($arr["obj_inst"]);
		}
	}

	function _send_support_mail($o)
	{
		$ct = "Tere!\n\n";
		$ct .= "Klient saidilt ".aw_ini_get("baseurl")." saatis tagasisidet:\n\n";
		$p = $o->get_first_obj_by_reltype("RELTYPE_PERSON");
		$ct .= "Isik: ".$p->name()." (".$p->id().")\n";
		$co = $o->get_first_obj_by_reltype("RELTYPE_CO");
		$ct .= "Organisatsioon: ".$co->name()." (".$co->id().")\n";
		$ct .= "Klass: " . (aw_ini_isset("classes." .  $o->prop("fb_class")) ? aw_ini_get("classes." . $o->prop("fb_class") . ".name") : "-")."\n";
		$ob = $o->get_first_obj_by_reltype("RELTYPE_OBJECT");
		if($ob)
		{
			$object = $ob->name()." (".$ob->id().")";
		}
		else
		{
			$object = "";
		}
		$ct .= "Objekt: ".$object."\n";

		$tmp = obj();
		$tmp->set_class_id($o->prop("fb_class"));
		$gl = $tmp->get_group_list();
		$ct .= "Objekti grupp: ".$gl[$o->prop("fb_object_grp")]["caption"]." \n";

		$ct .= "Kommentaar:\n".$o->prop("comment_ta")."\n";
		$ct .= html_entity_decode("T&otilde;sidus: ".(isset($this->severities[$o->prop("seriousness")]) ? $this->severities[$o->prop("seriousness")] : "-") ."\n");
		$ct .= html_entity_decode("Tagasiside t&uuml;&uuml;p: ".(isset($this->fb_types[$o->prop("fb_type")]) ?  $this->fb_types[$o->prop("fb_type")] : "-")."\n");
		$ct .= "Tagasiside email: ".$o->prop("fb_email")."\n";
		$ct .= "Tagasiside telefon: ".$o->prop("fb_phone")."\n";
		$ct .= "\n\nMuutmise aadress: \n".$this->mk_my_orb("change", array("id" => $o->id(), "auth_code" => $o->meta("auth_code")));
		try
		{
			$support_email_address = aw_ini_get("customer_feedback.support_email_address");
			$chk = aw_ini_get("customer_feedback.btsite");

			if ($chk)
			{
				$ct .= "\n\n Bugi loomine: \n".$this->mk_my_orb("create_bug", array("id" => $o->id()));
			}
		}
		catch(Exception $e)
		{
			$support_email_address = "support@automatweb.com";
		}

		$awm = new aw_mail();
		$uri = new aw_uri(aw_ini_get("baseurl"));
		$awm->create_message(array(
			"froma" => $o->prop("fb_email") ? $o->prop("fb_email") :  $p->prop("username") . "@" . $uri->get_host(),
			"subject" => "Tagasiside saidilt ".aw_ini_get("baseurl"),
			"to" => $support_email_address,
			"body" => $ct
		));
		$mimeregistry = new aw_mime_types();
		$f = $o->get_first_obj_by_reltype("RELTYPE_FILE1");
		if ($f)
		{
			$awm->fattach(array(
				"path" => $f->prop("file"),
				"contenttype"=> $mimeregistry->type_for_file($f->name()),
				"name" => $f->name(),
			));
		}
		$f = $o->get_first_obj_by_reltype("RELTYPE_FILE2");
		if ($f)
		{
			$awm->fattach(array(
				"path" => $f->prop("file"),
				"contenttype"=> $mimeregistry->type_for_file($f->name()),
				"name" => $f->name(),
			));
		}
		$f = $o->get_first_obj_by_reltype("RELTYPE_FILE3");
		if ($f)
		{
			$awm->fattach(array(
				"path" => $f->prop("file"),
				"contenttype"=> $mimeregistry->type_for_file($f->name()),
				"name" => $f->name(),
			));
		}
		$awm->gen_mail();
		$awm->clean();
	}

	function callback_mod_tab($arr)
	{
		if ($arr["id"] === "dev_status")
		{
			if (aw_global_get("authenticated_as_customer_care_personnel"))
			{
				return true;
			}

			if (isset($arr["request"]["auth_code"]) and $arr["request"]["auth_code"] == $arr["obj_inst"]->meta("auth_code"))
			{
				return true;
			}
			return false;
		}
		return true;
	}

	function callback_pre_edit($arr)
	{
		if (!empty($arr["request"]["auth_code"]) && $arr["obj_inst"]->meta("auth_code") == $arr["request"]["auth_code"])
		{
			aw_session_set("authenticated_as_customer_care_personnel", 1);
		}
	}

	function callback_get_default_group($arr)
	{
		if (!empty($arr["request"]["id"]))
		{
			$obj = obj($arr["request"]["id"]);
			if (!empty($arr["request"]["auth_code"]) && $obj->meta("auth_code") == $arr["request"]["auth_code"])
			{
				return "dev_status";
			}
		}
	}

	function _file_1($arr)
	{
		$f1 = $f2 = $f3 = "";
		if (empty($arr["new"]))
		{
			$f = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_FILE1");
			if ($f)
			{
				$fi = new file();
				$f1 = html::href(array(
					"url" => $fi->get_url($f->id(), $f->name()),
					"caption" => html::img(array(
						"url" => icons::get_icon_url(CL_FILE,$f->name()),
						"border" => "0"
						))." ".$f->name(),
					"target" => "_blank",
				));
			}
			$f = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_FILE2");
			if ($f)
			{
				$fi = new file();
				$f2 = html::href(array(
					"url" => $fi->get_url($f->id(), $f->name()),
					"caption" => html::img(array(
						"url" => icons::get_icon_url(CL_FILE,$f->name()),
						"border" => "0"
						))." ".$f->name(),
					"target" => "_blank",
				));
			}
			$f = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_FILE3");
			if ($f)
			{
				$fi = new file();
				$f3 = html::href(array(
					"url" => $fi->get_url($f->id(), $f->name()),
					"caption" => html::img(array(
						"url" => icons::get_icon_url(CL_FILE,$f->name()),
						"border" => "0"
						))." ".$f->name(),
					"target" => "_blank",
				));
			}
		}

		$f1u = html::fileupload(array("name" => "file_1"));
		$f2u = html::fileupload(array("name" => "file_2"));
		$f3u = html::fileupload(array("name" => "file_3"));
		$arr["prop"]["value"] = "<table border=0 width='100%'>
			<tr>
				<td class='aw04contentcellright'>{$f1}&nbsp;</td>
				<td class='aw04contentcellright'>{$f2}&nbsp;</td>
				<td class='aw04contentcellright'>{$f3}&nbsp;</td>
			</tr>
			<tr>
				<td class='aw04contentcellright'>{$f1u}</td>
				<td class='aw04contentcellright'>{$f2u}</td>
				<td class='aw04contentcellright'>{$f3u}</td>
			</tr>
		</table>";
	}

	/**
	@attrib name=create_bug all_args=1
	**/
	function create_bug($arr)
	{
		if(is_oid($arr["id"]) && $site = aw_ini_get("customer_feedback.btsite"))
		{
			$o = obj($arr["id"]);
			$o2 = $o->get_first_obj_by_reltype("RELTYPE_OBJECT");
			$cldata = aw_ini_get("classes");
			$params = array(
				"site" => aw_ini_get("baseurl"),
				"person" => $o->prop("person_t"),
				"company" => $o->prop("co_t"),
				"fb_class" => $o->prop("fb_class"),
				"name" => ($o2)?$o2->name():"(puudub)",
				"oid" => ($o2)?$o2->id():"",
				"comment" => $o->prop("comment_ta"),
				"group" => $o->prop("fb_object_grp"),
				"seriousness" => $o->prop("seriousness"),
				"fb_oid" =>  $arr["id"],
			);
			$url = $this->do_orb_method_call(array(
				"action" => "create_feedback_bug",
				"class" => "bug_tracker",
				"params" => $params,
				"method" => "xmlrpc",
				"server" => $site
			));
			if($url)
			{
				return $url;
			}
		}
	}
}
?>
