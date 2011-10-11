<?php

class personnel_management_obj extends _int_object
{
	const CLID = 275;

	function prop($k)
	{
		if($k == "perpage" && !is_numeric(parent::prop($k)))
		{
			return 20;
		}
		return parent::prop($k);
	}

	/**
	@attrib name=notify_of_new_cv params=name

	@param person_obj required type=object acl=view

	@param to required type=string

	@param pm_obj optional type=object acl=view

	**/
	function notify_of_new_cv($arr)
	{
		$arr["lang"] = is_object($pm_obj) && is_oid($pm_obj->prop("notify_lang")) ? $pm_obj->prop("notify_lang.lang_acceptlang") : languages::lid2acceptlang(AW_REQUEST_CT_LANG_ID);
		$this->send_cv_by_email($arr);
	}

	/** Sends CV to e-mail.
		@attrib name=send_cv_by_email api=1 params=name

		@param person_obj required type=object
			The person object to be sent.
		@param pm_obj optional type=object
			The personnel management object to be used. If not set, the system default will be used.
		@param to optional type=string
			The e-mail addresses to send the CV to.
		@param cc optional type=string
			The e-mail addresses to send the CV to.
		@param bcc optional type=string
			The e-mail addresses to send the CV to.
		@param lang optional type=string
			The language code. A'la en, ru, et, jp etc..
	**/
	function send_cv_by_email($arr)
	{
		extract($arr);
		if(isset($lang))
		{
			aw_ini_set("user_interface.default_language", $lang);
		}
		if(!isset($pm_obj) || !is_object($pm_obj))
		{
			if(is_oid(parent::id()))
			{
				$pm_obj = obj(parent::id());
			}
			else
			{
				$pm_obj = obj(get_instance(CL_PERSONNEL_MANAGEMENT)->get_sysdefault());
			}
		}

		$message = get_instance(CL_CRM_PERSON)->show_cv(array(
			"id" => $person_obj->id(),
			"cv" => "cv/".basename($pm_obj->prop("cv_tpl")),
		));
		$real_lang_id = aw_ini_get("user_interface.full_content_trans") ? aw_global_get("ct_lang_id") : aw_global_get("lang_id");
		$lang_id = aw_global_get("lang_id");
		aw_session_set("lang_id", $real_lang_id);

		$subject = strlen($pm_obj->prop("notify_subject")) > 0 ? $pm_obj->prop("notify_subject") : t("Uus CV on lisatud");
		$subject = "=?".aw_global_get("charset")."?B?".base64_encode($subject)."?=\n";

		$awm = get_instance("protocols/mail/aw_mail");
		$awm->set_header("Content-Type","text/plain; charset=\"".aw_global_get("charset")."\"");
		$awm->create_message(array(
			"froma" => $pm_obj->prop("notify_froma"),
			"fromn" => $pm_obj->prop("notify_fromn"),
			"subject" => $subject,
			"to" => $to,
			"cc" => $cc,
			"bcc" => $bcc,
		));
		$message = str_replace("<br />", "<br />\n" ,$message);
		$message = str_replace("<br>", "<br />\n" ,$message);
		$message = str_replace("</p>", "</p>\n" ,$message);
		$awm->htmlbodyattach(array(
			"data" => $message,
		));
		$awm->gen_mail();
		aw_session_set("lang_id", $lang_id);
	}

	function on_add_person($arr)
	{
		$this->send_naughtyfication_mail_if_necessary($arr);
	}

	// Checks if I have to send notification mail.
	private function send_naughtyfication_mail_if_necessary($arr)
	{
		$o = obj($arr["oid"]);
		$pm = obj(get_instance(CL_PERSONNEL_MANAGEMENT)->get_sysdefault());

		$notify_by_loc = $pm->meta("notify_loc_tbl");

		$to = $pm->notify_mail;
		$tos = array();
		foreach($o->connections_from(array("type" => "RELTYPE_WORK_WANTED")) as $conn)
		{
			$jw = $conn->to();
			foreach(array_merge((array)$jw->location, (array)$jw->location_2) as $loc)
			{
				if(isset($notify_by_loc[$loc]) && strlen($notify_by_loc[$loc]) > 0)
				{
					// strtolower to make life easier for array_unique()
					$tos[] = strtolower($notify_by_loc[$loc]);
				}
			}
		}
		$tos = array_unique($tos);
		$to = count($tos) > 0 ? implode(",", $tos) : $to;

		if($pm->persons_fld == $o->parent() && strlen(trim($pm->notify_mail)) > 0 && ($pm->notify_candidates || !is_oid(aw_global_get("job_offer_obj_id_for_candidate"))))
		{
			$this->notify_of_new_cv(array(
				"person_obj" => $o,
				"to" => $to,
				"pm_obj" => $pm,
			));
		}
	}

	function get_valid_job_offers($arr)
	{
		extract($arr);
		// $parent, $return_as_odl, $childs

		if($childs)
		{
			$children = new object_tree(array(
				"lang_id" => array(),
				"site_id" => array(),
				"parent" => $parent,
				"class_id" => CL_MENU,
			));
			$parent = array_merge((array) $parent, $children->to_list()->ids());
		}

		$prms = array(
			"class_id" => CL_PERSONNEL_MANAGEMENT_JOB_OFFER,
			"lang_id" => array(),
			"site_id" => array(),
			"parent" => $parent,
			"end" => new obj_predicate_compare(OBJ_COMP_GREATER_OR_EQ, mktime(0, 0, 0, date("m"), date("d"), date("Y")), NULL, "int"),
		);
		if($return_as_odl)
		{
			$ret = new object_data_list($prms, array(
				CL_PERSONNEL_MANAGEMENT_JOB_OFFER => array("name"),
			));
		}
		else
		{
			$ret = new object_list($prms);
		}
		return $ret;
	}

	function auto_archive()
	{
		$aa_last = parent::meta("last_auto_archive");
		if(time() - $aa_last > 24*3600 && parent::prop("auto_archive"))
		{
			// Let's do some auto archiving!
			$ol = new object_list(array(
				"class_id" => CL_PERSONNEL_MANAGEMENT_JOB_OFFER,
				"lang_id" => array(),
				"archive" => new obj_predicate_not(1),
				"end" => new obj_predicate_compare(OBJ_COMP_LESS, time() - parent::prop("auto_archive_days")*24*3600, NULL, "int"),
			));
			foreach($ol->arr() as $o)
			{
				$o->archive = 1;
				$o->save();
			}
			parent::set_meta("last_auto_archive", time());
			parent::save();
		}
	}

	public static function get_show_cnt_conf($arr = array())
	{
		$pm = obj(get_instance(CL_PERSONNEL_MANAGEMENT)->get_sysdefault(), array(), self::CLID);
		return array(
			CL_CRM_PERSON => array(
				"view" => array(
					"groups" => $pm->show_cnt_person,
				),
				"change" => array(
					"groups" => $pm->show_cnt_person,
				),
			),
			CL_PERSONNEL_MANAGEMENT_JOB_OFFER => array(
				"view" => array(
					"groups" => $pm->show_cnt_job_offer,
				),
				"change" => array(
					"groups" => $pm->show_cnt_job_offer,
				),
			),
		);
	}

	protected static function parse_colleague_mail($pm, $prop, $ol, $from, $public_key)
	{
		$tpl = new aw_template();
		$tpl->use_template($pm->prop("send_to_colleague_tpl.".$prop));

		$public_url = new aw_uri(aw_ini_get("baseurl"));
		$public_url->set_arg("class", "personnel_management_candidate");
		$public_url->set_arg("action", "public_show");

		$protected_url = new aw_uri(aw_ini_get("baseurl")."/automatweb");

		$CANDIDATE = $jon = "";
		if($ol->count())
		{
			$o = $ol->begin();

			do
			{
				$jon = $o->prop("job_offer.name");

				if("subject" === $prop)
				{
					break;
				}

				$hash = md5(time().$o->id());
				$o->set_prop("hash", $hash);
				$o->save();

				$public_url->set_arg("hash", $hash);

				$tpl->vars(array(
					"job_offer.name" => $o->prop("job_offer.name"),
					"person.name" => $o->prop("person.name"),
					"public_view_url" => $public_url->get(),
					"view_url" => $protected_url->get(),
				));
				$CANDIDATE .= $tpl->parse("CANDIDATE");
			}
			while ($o = $ol->next());
		}

		$tpl->vars(array(
			"job_offer.name" => $jon,
			"from.name" => $from->prop("name"),
			"CANDIDATE" => $CANDIDATE,
		));

		return $tpl->parse();
	}
}

