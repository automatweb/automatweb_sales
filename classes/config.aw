<?php

class config extends aw_template implements orb_public_interface
{
	function config()
	{
		$this->init("automatweb/config");
		$this->sub_merge = 1;
	}

	/** Sets orb request to be processed by this object
		@attrib api=1 params=pos
		@param request type=aw_request
		@returns void
	**/
	public function set_request(aw_request $request)
	{
		$this->req = $request;
	}

	/** sets the value for a configuration key
		@attrib api=1 params=pos

		@param ckey required type=string
			The config key name

		@param value required type=string
			The config key value

		@comment
			This can be used to store generic configuration settings, that apply for the entire site. Settings that don't fit in any object. The method can be used both as statically or via an instance.

		@example
			config::set_simple_config("default_field", "allah");
			echo config::get_simple_config("default_field"); // echoes allah
	**/
	public static function set_simple_config($ckey, $value)
	{
		// 1st, check if the necessary key exists
		$uid = aw_global_get("uid");
		object_loader::ds()->quote($uid);
		object_loader::ds()->quote($ckey);
		object_loader::ds()->quote($value);
		$time = time();
		$sql = <<<SQL
INSERT INTO `config`
	(`ckey`, `content`, `modified`, `modified_by`)
VALUES
	('{$ckey}', '{$value}', {$time}, '{$uid}')
ON DUPLICATE KEY UPDATE
	`content` = '{$value}',
	`modified` = {$time},
	`modified_by` = '{$uid}'
SQL;
		object_loader::ds()->db_query($sql);
	}

	/** gets the value for a configuration key
		@attrib api=1 params=pos

		@param ckey required type=string
			The config key name

		@comment
			This can be used to read generic configuration settings, that apply for the entire site. Settings that don't fit in any object. The method can be used both as statically or via an instance.

		@example
			config::set_simple_config("default_field", "allah");
			echo config::get_simple_config("default_field"); // echoes allah
	**/
	public static function get_simple_config($ckey)
	{
		object_loader::ds()->quote($ckey);
		$q = "SELECT content FROM config WHERE ckey = '{$ckey}'";
		return object_loader::ds()->db_fetch_field($q,"content");
	}

	function get_grp_redir()
	{
		$es = $this->get_simple_config("login_grp_redirect_".aw_ini_get("site_id")."_".aw_global_get("LC"));
		if ($es == false)
		{
			$es = $this->get_simple_config("login_grp_redirect_".aw_global_get("LC"));
			if ($es == false)
			{
				$es = $this->get_simple_config("login_grp_redirect");
			}
		}
		$this->dequote($es);
		return aw_unserialize($es);
	}

	/** lets the user set it so that different groups get redirected to diferent pages when logging in
		@attrib name=grp_redirect params=name
	**/
	function grp_redirect($arr)
	{
		$this->read_template("login_grp_redirect.tpl");
		$ea = $this->get_grp_redir();

		$ol = new object_list(array(
			"class_id" => CL_GROUP,
			"type" => new obj_predicate_not(aw_groups::TYPE_DEFAULT),
			"join_strategy" => "data"
		));

		$li = "";
		foreach($ol->arr() as $o)
		{
			$this->vars(array(
				"grp_id" => $o->prop("gid"),
				"grp_name" => $o->prop("name"),
				"url" => $ea[$o->prop("gid")]["url"],
				"priority" => $ea[$o->prop("gid")]["pri"]
			));
			$li .= $this->parse("LINE");
		}

		$this->vars(array(
			"LINE" => $li,
			"reforb" => $this->mk_reforb("submit_grp_redirect", array())
		));

		return $this->parse();
	}

	/**
		@attrib name=submit_grp_redirect params=name
	**/
	function submit_grp_redirect($arr)
	{
		extract($arr);
		$ea = $this->get_grp_redir();

		if (is_array($grps))
		{
			foreach($grps as $grp => $gar)
			{
				$ea[$grp]["url"] = $gar["url"];
				$ea[$grp]["pri"] = $gar["pri"];
			}
		}

		$ss = aw_serialize($ea, SERIALIZE_XML);
		$this->quote($ss);
		$this->set_simple_config("login_grp_redirect_".aw_ini_get("site_id")."_".aw_global_get("LC"), $ss);
		return $this->mk_my_orb("grp_redirect", array());
	}

	/**
		@attrib name=favicon params=name nologin="1"
	**/
	function show_favicon($arr)
	{
		header("Content-type: image/x-icon");
		die($this->get_simple_config("favicon"));
	}

	/**
		@attrib name=config params=name
	**/
	function gen_config()
	{
		$this->mk_path(0, t("Saidi seaded"));
		$this->read_template("config.tpl");

		$al = $this->get_simple_config("after_login");
		$doc = $this->get_simple_config("orb_err_mustlogin");
		$err = $this->get_simple_config("error_redirect");
		$al = $this->get_simple_config("useradd::autologin");
		$ipp = $this->get_cval("ipaddresses::default_folder");

		$us = new users();
		$li = languages::get_list(array("all_data" => true));
		$r_al = "";
		foreach($li as $lid => $ld)
		{
			$tal = $this->get_simple_config("after_login_".$ld["acceptlang"]);
			if (!$tal)
			{
				$tal = $al;
			}
			$this->vars(array(
				"lang_id" => $ld["acceptlang"],
				"lang" => $ld["name"],
				"after_login" => $tal
			));
			$r_al .= $this->parse("AFTER_LOGIN");
		}

		$r_ml = "";
		foreach($li as $lid => $ld)
		{
			$tml = $this->get_simple_config("orb_err_mustlogin_".$ld["acceptlang"]);
			if (!$tml)
			{
				$tml = $doc;
			}
			$this->vars(array(
				"lang_id" => $ld["acceptlang"],
				"lang" => $ld["name"],
				"mustlogin" => $tml
			));
			$r_ml .= $this->parse("MUSTLOGIN");
		}

		$r_el = "";
		foreach($li as $lid => $ld)
		{
			$tml = $this->get_simple_config("error_redirect_".$ld["acceptlang"]);
			if (!$tml)
			{
				$tml = $err;
			}
			$this->vars(array(
				"lang_id" => $ld["acceptlang"],
				"lang" => $ld["name"],
				"error_redirect" => $tml
			));
			$r_el .= $this->parse("ERROR_REDIRECT");
		}

		$fvi = "";
		if ($this->get_simple_config("favicon"))
		{
			$fvi = "<img src='".$this->mk_my_orb("favicon", array(),"config", false,true)."'>";
		}

		$this->vars(array(
			"AFTER_LOGIN" => $r_al,
			"favicon" => $fvi,
			"search_doc" => $this->mk_orb("search_doc", array(),"links"),
			"MUSTLOGIN" => $r_ml,
			"ERROR_REDIRECT" => $r_el,
			"reforb" => $this->mk_reforb("submit_loginaddr"),
			"autologin" => checked($al),
			"ipp" => $this->picker($ipp, $this->get_menu_list())
		));
		automatweb::$request->set_arg("in_popup", "1");
		return $this->parse();
	}

	/**
		@attrib name=submit_loginaddr params=name
	**/
	function submit_loaginaddr($arr)
	{
		extract($arr);
		$li = languages::get_list(array("all_data" => true));
		foreach($li as $lid => $ld)
		{
			$var = "after_login_".$ld["acceptlang"];
			$this->set_simple_config($var, $$var);
		}

		foreach($li as $lid => $ld)
		{
			$var = "mustlogin_".$ld["acceptlang"];
			$this->set_simple_config("orb_err_mustlogin_".$ld["acceptlang"], $$var);
		}

		$this->set_simple_config("error_redirect",$error_redirect);
		foreach($li as $lid => $ld)
		{
			$var = "error_redirect_".$ld["acceptlang"];
			$this->set_simple_config($var, $$var);
		}
		$this->set_simple_config("useradd::autologin",$autologin);
		$this->set_simple_config("ipaddresses::default_folder",$ipp);

		// if favicon was uploaded, handle it.
		global $favicon;
		if (is_uploaded_file($favicon))
		{
			$f = fopen($favicon,"r");
			$fc = fread($f,filesize($favicon));
			$this->quote($fc);
			fclose($f);
			$this->set_simple_config("favicon", $fc);
		}
		return $this->mk_my_orb("config");
	}

	/**
		@attrib name=join_mail params=name
	**/
	function join_mail($arr)
	{
		$this->read_template("join_mail.tpl");
		$ll = languages::listall();

		foreach($ll as $lid => $ldata)
		{
			$this->vars(array(
				"join_mail" => $this->get_simple_config("join_mail".$ldata["acceptlang"]),
				"pwd_mail" => $this->get_simple_config("remind_pwd_mail".$ldata["acceptlang"]),
				"join_mail_subj" => $this->get_simple_config("join_mail_subj".$ldata["acceptlang"]),
				"pwd_mail_subj" => $this->get_simple_config("remind_pwd_mail_subj".$ldata["acceptlang"]),
				"join_hash_section" => $this->get_simple_config("join_hash_section".$ldata["acceptlang"]),
				"acceptlang" => $ldata["acceptlang"],
				"name" => $ldata["name"]
			));
			$lb.=$this->parse("LANG");
		}

		$this->vars(array(
			"LANG" => $lb,
			"reforb" => $this->mk_reforb("submit_join_mail", array()),
			"join_send_also" => $this->get_simple_config("join_send_also"),
		));

		return $this->parse();
	}

	/**
		@attrib name=submit_join_mail params=name
	**/
	//XXX: mis see? miks siin? mida t2hendab default 0 orbi definitsioonis?
	function submit_join_mail($arr)
	{
		extract($arr);

		$ll = languages::listall();

		foreach($ll as $lid => $ldata)
		{
			$this->set_simple_config("join_mail".$ldata["acceptlang"],$join_mail[$ldata["acceptlang"]]);
			$this->set_simple_config("remind_pwd_mail".$ldata["acceptlang"],$pwd_mail[$ldata["acceptlang"]]);
			$this->set_simple_config("join_mail_subj".$ldata["acceptlang"],$join_mail_subj[$ldata["acceptlang"]]);
			$this->set_simple_config("remind_pwd_mail_subj".$ldata["acceptlang"],$pwd_mail_subj[$ldata["acceptlang"]]);
			$this->set_simple_config("join_hash_section".$ldata["acceptlang"],$join_hash_section[$ldata["acceptlang"]]);
		}
		$this->set_simple_config("join_send_also",$join_send_also);

		return $this->mk_orb("join_mail", array());
	}
}
