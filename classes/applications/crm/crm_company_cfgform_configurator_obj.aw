<?php

class crm_company_cfgform_configurator_obj extends _int_object
{
	public function set_prop($k, $v)
	{
		if(in_array($k, array("create_customer_data", "make_user", "mu_send_welcome_mail", "mu_login", "check_email")))
		{
			$v = (int) $v;
		}

		return parent::set_prop($k, $v);
	}

	public function gen_controller($key, $arr)
	{
		if(!is_oid($this->prop("org_cfgform")) || !$this->can("view", $this->prop("org_cfgform")))
		{
			return false;
		}

		$ips = array(
			"change_params" => array("ca_change"),
			"add_user" => array("make_user", "mu_send_welcome_mail", "mu_login", "mu_generate_username", "mu_add_to_groups", "mu_welcome_mail_to"),
			"check_email" => array("check_email"),
			"sector_limit" => array("sector_limit"),
			"customer_data" => array("create_customer_data", "ccd_sample_object"),
		);
		$mod = false;

		foreach($ips[$key] as $ip)
		{
			if($arr[$ip] != $this->prop($ip))
			{
				$this->set_prop($ip, $arr[$ip]);
				$mod = true;
			}
		}

		$fn = "gen_".$key."_controller";
		if($mod && is_callable(array(&$this, $fn)))
		{
			$this->$fn($arr);
		}
		$this->save();
	}

	function gen_customer_data_controller($arr)
	{
		$cff = obj($this->prop("org_cfgform"));
		
		if(!$this->prop("create_customer_data") || !$this->can("view", $this->prop("ccd_sample_object")))
		{
			$c = $cff->prop("post_save_controllers");
			if(($k = array_search($this->prop("controller_customer_data"), $c)) !== false)
			{
				unset($c[$k]);
			}
			$cff->set_prop("post_save_controllers", $c);
			$cff->save();
			return true;
		}
		else
		{
			$cid = $this->prop("controller_customer_data");
			if(is_oid($cid) && $this->can("view", $cid))
			{
				$c = obj($cid);
			}
			else
			{
				$c = obj();
				$c->set_class_id(CL_CFGCONTROLLER);
				$c->set_parent($cff->id());
			}
			$c->set_name(t("Loo kliendisuhe"));
			$c->set_prop("formula", '$ol = new object_list(array(
	"class_id" => CL_CRM_COMPANY_CUSTOMER_DATA,
	"buyer" => $obj_inst->id(),
	"lang_id" => array(),
	"site_id" => array(),
	"limit" => 1,
));
if($ol->count() === 0)
{
	$o = obj(obj('.$this->prop("ccd_sample_object").')->save_new($obj_inst->id()));
	$o->set_prop("name", sprintf("Kliendisuhe %s => %s", obj('.$this->prop("ccd_sample_object").')->prop("seller.name"), $obj_inst->name()));
	$o->set_prop("buyer", $obj_inst->id());
	$o->save();
}');
			$c->save();

			$this->set_prop("controller_customer_data", $c->id());
		}

		$cff->set_prop("post_save_controllers", array_merge(safe_array($cff->prop("post_save_controllers")), array($c->id())));
		$cff->save();
	}

	private function gen_change_params_controller($arr)
	{
		if(
			!is_oid($this->prop("org_parent")) || !$this->can("view", $this->prop("org_parent")) ||
			$this->prop("ca_change") == 2 && !$this->can("view", $this->prop("ca_change_org"))
		)
		{
			return false;
		}

		$cff = obj($this->prop("org_cfgform"));
			
		$id = $this->prop("ca_change") == 2 ? $this->prop("ca_change_org") : 'get_instance(CL_USER)->get_current_company()';
		$parent = $this->prop("org_parent");

		$cid = $cff->prop("cfgview_change_params_from_controller");
		if(is_oid($cid) && $this->can("view", $cid))
		{
			$c = obj($cid);
		}
		else
		{
			$c = obj();
			$c->set_class_id(CL_CFG_VIEW_CONTROLLER);
			$c->set_parent($cff->id());
		}
		$c->set_name(t("Orgasatsiooni muutmisvaate kontroller"));
		$c->set_comment(t("Annab objektile ette parenti ja OID"));
		$c->set_prop("formula", sprintf(
'if (aw_global_get("uid") == "")
{
	$params["id"] = null;
}
else
{
	$params["id"] = %s;
}
$params["parent"] = %u;', $id, $parent));
		$cff->set_prop("cfgview_change_params_from_controller", $c->save());
		$cff->set_prop("cfgview_new_params_from_controller", $c->id());

		$cff->save();
	}

	private function gen_add_user_controller($arr)
	{
		if(strlen($this->prop("mu_generate_username")) === 0)
		{
			return false;
		}

		$cff = obj($this->prop("org_cfgform"));

		if(!$this->prop("make_user"))
		{
			$c = $cff->prop("post_save_controllers");
			if(($k = array_search($this->prop("controller_add_user"), $c)) !== false)
			{
				unset($c[$k]);
			}
			$cff->set_prop("post_save_controllers", $c);
			$cff->save();
			return true;
		}

		switch($this->prop("mu_generate_username"))
		{
			default:
				// a'la use_prop.fake_email
				$p = substr($this->prop("mu_generate_username"), 9);
				$uid = '$request["'.$p.'"]';
				break;
		}

		$to = '$request["'.$this->prop("mu_welcome_mail_to").'"]';

		$grps = '';
		foreach($this->prop("mu_add_to_groups") as $grp)
		{
			$grps .= '
	get_instance(CL_GROUP)->add_user_to_group($user_obj, obj('.$grp.'));';
		}

		$cid = $this->prop("controller_add_user");
		if(is_oid($cid) && $this->can("view", $cid))
		{
			$c = obj($cid);
		}
		else
		{
			$c = obj();
			$c->set_class_id(CL_CFGCONTROLLER);
			$c->set_parent($cff->id());
		}
		$c->set_name(t("Organisatsioonile kasutaja tegemise kontroller"));
		$c->set_comment($this->prop("mu_send_welcome_mail") ? t("Teeb organisatsioonile kasutaja ja saadab kasutajale logimisandmetega e-kirja") : t("Teeb organisatsioonile kasutaja"));
		$c->set_prop("formula", 
'if(!is_oid(aw_global_get("uid_oid")) || strlen(aw_global_get("uid")) == 0)
{
	$passwd = generate_password();
	$user_obj = get_instance(CL_USER)->add_user(array(
		"uid" => '.$uid.',
		"password" => $passwd,
	));'.$grps.'

	aw_disable_acl();
	$obj_inst->acl_set(obj($user_obj->get_default_group()), array("can_view" => 1, "can_edit" => 1, "can_delete" => 1, "can_add" => 1));
	aw_restore_acl();'.($this->prop("mu_send_welcome_mail") ? '

	$conf = obj('.$this->id().');
	$from = $conf->trans_get_val("mu_welcome_mail_from");
	$subject = $conf->trans_get_val("mu_welcome_mail_subject");
	$content = $conf->trans_get_val("mu_welcome_mail_content");

	$content = str_replace("%user", '.$uid.', $content);
	$content = str_replace("%passwd", $passwd, $content);
	send_mail('.$to.', $subject, $content, "From: $from");
	'.($this->prop("mu_login") ? 'get_instance("users")->login(array(
		"uid" => '.$uid.',
		"password" => $passwd,
	));' : '') : '').'
}');
		$c->save();
		$this->set_prop("controller_add_user", $c->id());
		$cff->set_prop("post_save_controllers", array_merge(safe_array($cff->prop("post_save_controllers")), array($c->id())));

		$cff->save();
	}

	private function gen_check_email_controller($arr)
	{
		$cff = obj($this->prop("org_cfgform"));
		$cntrls = $cff->meta("controllers");

		if(!$this->prop("check_email"))
		{
			if(isset($cntrls["fake_email"]) && ($k = array_search($this->prop("controller_check_email"), $cntrls["fake_email"])) !== false)
			{
				unset($cntrls["fake_email"][$k]);
			}
		}
		else
		{
			$cid = $this->prop("controller_check_email");
			if(is_oid($cid) && $this->can("view", $cid))
			{
				$c = obj($cid);
			}
			else
			{
				$c = obj();
				$c->set_class_id(CL_CFGCONTROLLER);
				$c->set_parent($cff->id());
			}
			$c->set_name(t("Kontrolli e-posti 6igsust"));
			$c->set_prop("formula", 'if(!is_email($prop["value"])){$retval = PROP_ERROR;}');
			$c->set_prop("errmsg", t("Sisestatud e-postiaadress pole korrektne!"));
			$c->save();
			$cntrls["fake_email"][] = $c->id();
			$cff->connect(array(
				"to" => $c->id(),
				"reltype" => "RELTYPE_CONTROLLER",
			));

			$this->set_prop("controller_check_email", $c->id());
		}

		$cff->set_meta("controllers", $cntrls);
		$cff->save();
	}

	function gen_sector_limit_limit($arr)
	{
		$cff = obj($this->prop("org_cfgform"));
		$cntrls = $cff->meta("view_controllers");

		if(!$this->prop("sector_limit"))
		{
			if(isset($cntrls["pohitegevus"]) && ($k = array_search($this->prop("controller_sector_limit"), $cntrls["pohitegevus"])) !== false)
			{
				unset($cntrls["pohitegevus"][$k]);
			}
		}
		else
		{
			$cid = $this->prop("controller_sector_limit");
			if(is_oid($cid) && $this->can("view", $cid))
			{
				$c = obj($cid);
			}
			else
			{
				$c = obj();
				$c->set_class_id(CL_CFG_VIEW_CONTROLLER);
				$c->set_parent($cff->id());
			}
			$c->set_name(t("Tegevusalade arvu piirang"));
			$c->set_prop("formula", '');
			$c->save();
			$cntrls["pohitegevus"][] = $c->id();
			$cff->connect(array(
				"to" => $c->id(),
				"reltype" => "RELTYPE_VIEWCONTROLLER",
			));

			$this->set_prop("controller_sector_limit", $c->id());
		}

		$cff->set_meta("view_controllers", $cntrls);
		$cff->save();
	}
}

?>
