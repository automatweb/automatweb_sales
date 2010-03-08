<?php
// quickmessagebox.aw - Kiirs5numite haldus
/*

@classinfo syslog_type=ST_QUICKMESSAGEBOX maintainer=voldemar prop_cb=1

@groupinfo message_box caption="Messages" submit=no
	@groupinfo message_inbox caption="Inbox" parent=message_box submit=no
	@groupinfo message_outbox caption="Outbox" parent=message_box submit=no
@groupinfo general caption="Settings"

@default table=objects
@default field=meta
@default method=serialize

@default group=general
	@property owner type=relpicker automatic=1 newonly=1 reltype=RELTYPE_OWNER clid=CL_USER
	@caption Select owner

	@property is_contactlist_add_approval_required type=checkbox ch_value=1
	@caption Require approval
	@If checked, other users can't add you to their contact list without your approval

	@property approved_senders type=select
	@caption Allow messages from

	@property show_addressees type=select
	@caption Allow sending to

	@property contactlist type=relpicker multiple=1 size=7 reltype=RELTYPE_CONTACT automatic=1 no_edit=1
	@caption Contact list
	@comment Select people you want in your contact list

@property msg_toolbar type=toolbar store=no group=message_inbox,message_outbox
@caption Message list toolbar

@property msg_inbox_tbl type=table store=no group=message_inbox
@caption Received messages

@property msg_outbox_tbl type=table store=no group=message_outbox
@caption Sent messages


////////////////// RELTYPES ////////////////////
@reltype OWNER value=4 clid=CL_USER
@caption Owner

@reltype CONTACT value=3 clid=CL_USER
@caption Contact

@reltype READ_MESSAGE value=5 clid=CL_QUICKMESSAGE
@caption Read message

@reltype UNREAD_MESSAGE value=6 clid=CL_QUICKMESSAGE
@caption Unread message

*/

class quickmessagebox extends class_base
{
	const COLOUR_READ = "grey";
	const COLOUR_UNREAD = "#CCEECC";
	const DATE_FORMAT = "d.m Y H:i:s";

	function quickmessagebox()
	{
		$this->init(array(
			"tpldir" => "applications/quickmessage/quickmessagebox",
			"clid" => CL_QUICKMESSAGEBOX
		));
	}

	function _get_msg_inbox_tbl($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->init_msg_tbl($t, "inbox");
		$cl_user = get_instance(CL_USER);

		$msgs = $arr["obj_inst"]->get_read_msgs();
		$msgs->sort_by(array("prop" => "created", "order" => "asc"));
		$i = 0;
		foreach ($msgs->arr() as $msg)
		{
			$from = new object($cl_user->get_person_for_user(new object($msg->prop("from"))));
			$t->define_data(array(
				"oid" => $msg->id(),
				"from" => $from->name(),
				"msg" => $msg->prop("html") ? $msg->prop("msg") : htmlspecialchars($msg->prop("msg")),
				"sent" => $msg->created(),
				// "colour" => self::COLOUR_READ,
				"i" => str_pad(++$i, 6, "0", STR_PAD_LEFT)
			));
		}

		$msgs = $arr["obj_inst"]->read_new_msgs();
		$msgs->sort_by(array("prop" => "created", "order" => "asc"));
		foreach ($msgs->arr() as $msg)
		{
			$from = new object($cl_user->get_person_for_user(new object($msg->prop("from"))));
			$t->define_data(array(
				"oid" => $msg->id(),
				"from" => $from->name(),
				"msg" => $msg->prop("html") ? $msg->prop("msg") : htmlspecialchars($msg->prop("msg")),
				"sent" => $msg->created(),
				"colour" => self::COLOUR_UNREAD,
				"i" => str_pad(++$i, 6, "0", STR_PAD_LEFT)
			));
		}

		return PROP_OK;
	}

	function _get_msg_outbox_tbl($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->init_msg_tbl($t, "outbox");
		$cl_user = get_instance(CL_USER);

		$msgs = $arr["obj_inst"]->get_sent_msgs();
		$msgs->sort_by(array("prop" => "created", "order" => "asc"));
		$i = 0;
		foreach ($msgs->arr() as $msg)
		{
			$to = array();
			foreach ($msg->prop("to") as $to_oid)
			{
				$tmp = new object($cl_user->get_person_for_user(new object($to_oid)));
				$to[] = $tmp->name();
			}
			$to = implode(", ", $to);

			$t->define_data(array(
				"oid" => $msg->id(),
				"to" => $to,
				"msg" => $msg->prop("html") ? $msg->prop("msg") : htmlspecialchars($msg->prop("msg")),
				"sent" => $msg->created(),
				"i" => str_pad(++$i, 6, "0", STR_PAD_LEFT)
			));
		}

		return PROP_OK;
	}

	private function init_msg_tbl(&$t, $type)
	{
		$t->define_chooser(array(
			"name" => "sel",
			"chgbgcolor" => "colour",
			"field" => "oid"
		));

		switch ($type)
		{
			case "inbox":
				$t->define_field(array(
					"name" => "from",
					"caption" => t("From"),
					"chgbgcolor" => "colour",
					"sortable" => 1
				));
				break;
			case "outbox":
				$t->define_field(array(
					"name" => "to",
					"caption" => t("To"),
					"chgbgcolor" => "colour",
					"sortable" => 1
				));
				break;
		}

		$t->define_field(array(
			"name" => "msg",
			"caption" => t("Message"),
			"chgbgcolor" => "colour",
			"sortable" => 1
		));

		$t->define_field(array(
			"name" => "sent",
			"caption" => t("Sent"),
			"chgbgcolor" => "colour",
			"type" => "time",
			"format" => self::DATE_FORMAT,
			"sortable" => 1
		));

		$t->set_default_sortby("i");
		$t->set_default_sorder("desc");
	}

	function _get_msg_toolbar($arr)
	{
		$toolbar =& $arr["prop"]["vcl_inst"];

		$url = $this->mk_my_orb("new", array(
			"return_url" => get_ru(),
			"parent" => $arr["obj_inst"]->id(),
			"box" => $arr["obj_inst"]->id(),
			"section" => $arr["request"]["section"]
		), "quickmessage");

		$toolbar->add_button(array(
			"name" => "new",
			"img" => "new.gif",
			"tooltip" => t("New message"),
			"url" => $url
		));

		$toolbar->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"tooltip" => t("Delete"),
			"action" => "delete_message",
			"confirm" => t("Delete selected messages?")
		));

		return PROP_OK;
	}

	function _get_approved_senders($arr)
	{
		$arr["prop"]["options"] = quickmessagebox_obj::get_approved_senders_options();
		return PROP_OK;
	}

	function _get_show_addressees($arr)
	{
		$arr["prop"]["options"] = quickmessagebox_obj::get_addressees_options();
		return PROP_OK;
	}

	function _get_owner($arr)
	{
		$uid = aw_global_get("uid");
		return PROP_OK;
	}

	function _get_contactlist($arr)
	{
		$u_oid = reset(array_keys($arr["prop"]["options"], aw_global_get("uid")));
		unset($arr["prop"]["options"][$u_oid]);
		$user_i = new user();

		// get person names for visible options
		foreach ($arr["prop"]["options"] as $u_oid => $tmp)
		{
			if ($u_oid)
			{
				try
				{
					$u_o = new object($u_oid);
					$p_oid = $user_i->get_person_for_user($u_o);
					$p_o = new object($p_oid);
					$arr["prop"]["options"][$u_oid] = $p_o->name();
				}
				catch (Exception $e)
				{
				}
			}
		}

		return PROP_OK;
	}

	/**
		@attrib name=delete_message
		@param id required type=int acl=view
		@param sel required
	**/
	function delete_message($arr)
	{
		$o = new object($arr["id"]);

		try
		{
			$o->delete_msgs($arr["sel"]);
		}
		catch (aw_exception $e)
		{
			echo $e->getMessage();
			flush();
		}

		$keys = array("cfgform", "section", "id", "group", "return_url");
		$args = array();
		foreach ($arr as $key => $value)
		{
			if (in_array($key, $keys) and !empty($value))
			{
				$args[$key] = $value;
			}
		}

		$return_url = $this->mk_my_orb("change", $args, $arr["class"], false, false, "&", $arr["ret_to_orb"]);
		return $return_url;
	}

	/**
		@attrib name=get_new_msgs_count
		@param id required type=int acl=view
	**/
	function get_new_msgs_count($arr)
	{
		try
		{
			echo quickmessagebox_obj::get_new_msgs_count($arr["id"]);
		}
		catch (Exception $e)
		{
			echo t("Error");
		}

		exit;
	}

	/**
		@attrib name=redir_userbox all_args=1
	**/
	function redir_userbox($arr)
	{
		return quickmessagebox_obj::redir_userbox($arr["url"]);
	}

	/**
		@attrib name=redir_popup_url all_args=1
	**/
	function redir_popup_url($arr)
	{
		$box = quickmessagebox_obj::get_msgbox_for_user(obj(aw_global_get("uid_oid")));
		$box->read_new_msgs();
		return $arr["url"];
	}
}

?>
