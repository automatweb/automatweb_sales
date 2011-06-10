<?php

/*
@classinfo maintainer=voldemar
*/

class quickmessage_obj extends _int_object
{
	const CLID = 335;

	const TYPE_GENERIC = 1;
	const TYPE_SYS = 2;

	public function awobj_get_from_display()
	{
		$u_oid = $this->prop("from");

		try
		{
			$u_o = new object($u_oid);
			$p_oid = user::get_person_for_user($u_o);
			$p_o = new object($p_oid);
			$p_name = $p_o->name();
		}
		catch (awex_obj_acl $e)
		{
			$p_name = t("[No access to user data]");
		}

		return $p_name;
	}

	/**
	@attrib api=1
	@returns array
		Options for selecting message recipient. Key: user oid, value: person name.
	@errors
		Throws awex_qmsg_cfg on 'show_addressees' config error.
	**/
	public function get_to_options()
	{
		try
		{
			$u_oid = aw_global_get("uid_oid");
			$current_user = new object($u_oid);
			$msgbox = quickmessagebox_obj::get_msgbox_for_user($current_user);
		}
		catch (Exception $e)
		{
			return;
		}

		$options = array();
		$user_i = new user();
		$addressees_setting = (int) $msgbox->prop("show_addressees");

		if (!$addressees_setting)
		{
			$addressees_setting = constant("quickmessagebox_obj::" . aw_ini_get("quickmessaging.show_addressees"));
		}

		if ($addressees_setting === quickmessagebox_obj::ADDRESSEES_CONTACTS)
		{
			$contactlist = $msgbox->prop("contactlist");
			$user_i = new user();
			foreach ($contactlist as $u_oid)
			{
				try
				{
					$u_o = new object($u_oid);
					$p_oid = $user_i->get_person_for_user($u_o);
					$p_o = new object($p_oid);
					$options[$u_oid] = $p_o->name();
				}
				catch (Exception $e)
				{
				}
			}
		}
		elseif ($addressees_setting === quickmessagebox_obj::ADDRESSEES_EVERYONE)
		{
			$users = new object_list(array(
				"class_id" => CL_USER,
				"site_id" => array(),
				"lang_id" => array(),
				"brother_of" => new obj_predicate_prop("id")
			));

			for ($u_o = $users->begin(); !$users->end(); $u_o = $users->next())
			{
				try
				{
					$p_oid = $user_i->get_person_for_user($u_o);
					$p_o = new object($p_oid);
					$options[$u_o->id()] = $p_o->name();
				}
				catch (Exception $e)
				{
				}
			}
		}
		else
		{
			throw new awex_qmsg_cfg("Addressees setting invalid: " . var_export($addressees_setting, true));
		}

		unset($options[$current_user->id()]);
		return $options;
	}

	public function awobj_set_to($value)
	{
		if (!is_array($value) or !count($value))
		{
			throw new awex_qmsg_param("Invalid message recipient parameter specified: " . var_export($value, true));
		}

		foreach ($value as $id)
		{
			if (!is_oid($id))
			{
				throw new awex_qmsg_param("Invalid message recipient id specified: " . var_export($id, true));
			}
		}

		$value = implode(",", $value);
		return parent::set_prop("to", $value);
	}

	public function awobj_set_msg($value)
	{
		$max_len = aw_ini_get("quickmessaging.msg_max_len");
		if ($max_len and $max_len < strlen($value))
		{
			throw new awex_qmsg_param("Message text too long.");
		}
		return parent::set_prop("msg", $value);
	}

	public function awobj_get_to()
	{
		return explode(",", parent::prop("to"));
	}

	/**
	@attrib api=1
	@errors
		Throws awex_qmsg_box if can't get messagebox for recipient.
		Forwards awex_obj_acl if no access to recipient's user object.
	**/
	public function save($exclusive = false, $previous_state = null)
	{
		$new = !$this->obj["oid"];

		if ($new)
		{
			try
			{
				$to_o = new object($this->prop("to"));
				$msgbox = quickmessagebox_obj::get_msgbox_for_user($to_o);
				$u_oid = get_instance(CL_USER)->get_obj_for_uid(aw_global_get("uid"))->id();
				$this->set_prop("from", $u_oid);
				$this->set_name(aw_global_get("uid") . " => " .  $to_o->name() . " @ " . date("d.M. Y H:i:s"));
			}
			catch (awex_obj_acl $e)
			{
				throw $e;
			}
			catch (aw_exception $e)
			{
				throw new awex_qmsg_box("Messagebox not defined. Can't send message.");
			}
		}

		$retval = parent::save($exclusive, $previous_state);

		if ($new)
		{
			$msgbox->post_msg(new object($this->id()));
		}

		return $retval;
	}
}

?>
