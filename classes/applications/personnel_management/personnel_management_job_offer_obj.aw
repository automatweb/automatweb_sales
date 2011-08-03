<?php

class personnel_management_job_offer_obj extends _int_object
{
	const CLID = 285;

	function set_prop($k, $v)
	{
		if($k == "notify_me")
		{
			$p = get_instance(CL_USER)->get_person_for_uid(aw_global_get("uid"));
			if($v == 1)
			{
				parent::connect(array(
					"to" => $p->id(),
					"type" => "RELTYPE_NOTIFY_ME",
				));
			}
			else
			if(is_oid(parent::id()))
			{
				$conns = connection::find(array(
					"from" => parent::id(),
					"to" => $p->id(),
					"reltype" => "RELTYPE_NOTIFY_ME",
				));
				if(count($conns) > 0)
				{
					parent::disconnect(array(
						"from" => $p->id(),
						"type" => "RELTYPE_NOTIFY_ME",
					));
				}
			}
			return true;
		}
		else
		{
			return parent::set_prop($k, $v);
		}
	}

	function get_end()
	{
		if($this->prop("endless"))
		{
			return t("T&auml;htajatu");
		}
		return get_lc_date($this->prop("end"));
	}

	function prop($k)
	{
		if($k == "notify_me")
		{
			$p = get_instance(CL_USER)->get_person_for_uid(aw_global_get("uid"));
			$conns = connection::find(array(
				"from" => parent::id(),
				"to" => $p->id(),
				"reltype" => "RELTYPE_NOTIFY_ME",
			));
			if(count($conns) > 0)
			{
				return 1;
			}
			else
			{
				return 0;
			}
		}
		elseif($k == "end" && $this->prop("endless"))
		{
			// Endless - as BIG as possible!
			return pow(2, 31) - 1;
		}
		else
		{
			return parent::prop($k);
		}
	}
	
	function awobj_get_company()
	{
		if(!is_oid($this->id()))
		{
			return user::get_current_company();
		}
		else
		{
			return parent::prop("company");
		}
	}
	
	function awobj_get_contact()
	{
		if(!is_oid($this->id()))
		{
			return user::get_current_person();
		}
		else
		{
			return parent::prop("contact");
		}
	}
	
	function get_candidates($arr = array())
	{
		$ret = new object_list();

		$i = get_instance(CL_FILE);
		foreach(parent::connections_from(array("type" => 1)) as $conn)
		{
			if(!isset($arr["status"]) || $conn->conn["to.status"] == $arr["status"])
			{
				$to = $conn->to();
				if($i->can("view", $to->prop("person")))
				{
					$ret->add($to->prop("person"));
				}
			}
		}

		return $ret;
	}

	/**
		@attrib name=handle_show_cnt api=1 params=name

		@param action required type=string

		@param id required type=OID

	**/
	public function handle_show_cnt($arr)
	{
		extract($arr);

		$show_cnt_conf = get_instance("personnel_management_obj")->get_show_cnt_conf();
		$usr = get_instance(CL_USER);
		$u = $usr->get_current_user();
		$g = ifset($show_cnt_conf, CL_PERSONNEL_MANAGEMENT_JOB_OFFER, $action, "groups");
		if($usr->is_group_member($u, $g) && is_oid($id))
		{
			$o = obj($id);
			$o->show_cnt = $o->show_cnt + 1;
			aw_disable_acl();
			$o->save();
			aw_restore_acl();
		}
	}

	function notify_me_of_confirmation()
	{
		$pm_obj = obj(get_instance(CL_PERSONNEL_MANAGEMENT)->get_sysdefault());
		if(!is_oid($this->id()))
		{
			parent::save();
		}
		foreach($this->connections_from(array("type" => "RELTYPE_NOTIFY_ME_OF_CONFIRMATION")) as $conn)
		{
			$awm = get_instance("protocols/mail/aw_mail");
			$awm->set_header("Content-Type","text/plain; charset=\"".aw_global_get("charset")."\"");
			$awm->create_message(array(
				"froma" => $pm_obj->prop("notify_froma"),
				"fromn" => $pm_obj->prop("notify_fromn"),
				"subject" => sprintf(t("Job offer '%s' is confirmed!"), $this->name()),
				"body" => sprintf(t("To whom it may concerne,

Job offer '%s' has been confirmed.

Regards,
Your Personnel Management"), $this->name()),
				"to" => $conn->to()->mail,
				"cc" => $cc,
				"bcc" => $bcc,
			));
			$awm->gen_mail();
		}
	}

	public function save($check_state = false)
	{
		if(parent::prop("confirmed") && !parent::meta("confirmation_mail_sent"))
		{
			$this->notify_me_of_confirmation();
			parent::set_meta("confirmation_mail_sent", 1);
		}
		return parent::save($check_state);
	}
}

?>
