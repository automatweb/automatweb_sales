<?php

class obj_predicate_acl
{
	private $user = null;
	private $action = "view";

	public function __construct($action = "view", object $user = null)
	{
		if ($user and !$user->is_a(user_obj::CLID))
		{
			throw new awex_obj_param("Invalid user object parameter.");
		}

		if (empty($user))
		{
			$u_oid = user::get_current_user(); //TODO:  tmp teha edasi.
			$user = $u_oid ? obj($u_oid) : null;
		}

		$this->user = $user;
		$this->action = $action;
	}

	public function get_user()
	{
		return $this->user;
	}

	public function get_action()
	{
		return $this->action;
	}
}
