<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/core/users/auth/auth_remote_settings.aw,v 1.7 2008/01/31 13:54:00 kristo Exp $
// auth_remote_settings.aw - Automaatne sissep&auml;&auml;s 
/*

@classinfo syslog_type=ST_AUTH_REMOTE_SETTINGS relationmgr=yes no_comment=1 no_status=1 maintainer=kristo

@default table=objects
@default group=general

@groupinfo sel_sites caption="Vali saidid"
@groupinfo sel_users caption="Vali kasutajad"
@groupinfo activity caption="Aktiivsus"


@default group=sel_sites
	@property sites type=table no_caption=1

@default group=sel_users
	@property users type=table no_caption=1

@default group=activity
	@property activity type=table no_caption=1

@reltype USER value=1 clid=CL_USER
@caption kasutaja

*/

class auth_remote_settings extends class_base
{
	const AW_CLID = 879;

	function auth_remote_settings()
	{
		$this->init(array(
			"tpldir" => "core/users/auth/auth_remote_settings",
			"clid" => CL_AUTH_REMOTE_SETTINGS
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "activity":
				$this->mk_activity_table($arr);
				break;

			case "sites":
				$this->_do_sites_t($arr);
				break;

			case "users":
				$this->_do_sel_users_t($arr);
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
			case "activity":
				$ol = new object_list(array(
					"class_id" => CL_AUTH_REMOTE_SETTINGS,
				));
				for ($o = $ol->begin(); !$ol->end(); $o = $ol->next())
				{
					if ($o->flag(OBJ_FLAG_IS_SELECTED) && $o->id() != $arr["request"]["active"])
					{
						$o->set_flag(OBJ_FLAG_IS_SELECTED, false);
						$o->save();
					}
					else
					if ($o->id() == $arr["request"]["active"] && !$o->flag(OBJ_FLAG_IS_SELECTED))
					{
						$o->set_flag(OBJ_FLAG_IS_SELECTED, true);
						$o->save();
					}
				}
				break;

			case "sites":
				$arr["obj_inst"]->set_meta("dat", $arr["request"]["select"]);
				break;

			case "users":
				$arr["obj_inst"]->set_meta("site2user", $arr["request"]["site2user"]);
				break;
		}
		return $retval;
	}	

	function mk_activity_table($arr)
	{
		// this is supposed to return a list of all active polls
		// to let the user choose the active one
		$table = &$arr["prop"]["vcl_inst"];
		$table->parse_xml_def("activity_list");

		$pl = new object_list(array(
			"class_id" => CL_AUTH_REMOTE_SETTINGS
		));	
		for($o = $pl->begin(); !$pl->end(); $o = $pl->next())
		{
			$actcheck = checked($o->flag(OBJ_FLAG_IS_SELECTED));
			$act_html = "<input type='radio' name='active' $actcheck value='".$o->id()."'>";
			$row = $o->arr();
			$row["active"] = $act_html;
			$table->define_data($row);
		};
	}

	function _init_sites_t(&$t)
	{
		$t->define_field(array(
			"name" => "site_id",
			"caption" => t("ID"),
			"align" => "center",
			"sortable" => 1,
			"numeric" => 1
		));

		$t->define_field(array(
			"name" => "url",
			"caption" => t("Aadress"),
			"align" => "center",
			"sortable" => 1
		));

		$t->define_field(array(
			"name" => "select",
			"caption" => t("Lubatud automaatne ligip&auml;&auml;s"),
			"align" => "center"
		));
	}

	function _do_sites_t($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_sites_t($t);
		
		$dat = $arr["obj_inst"]->meta("dat");

		$sl = get_instance(CL_INSTALL_SITE_LIST);
		$list = $sl->get_local_list();
		foreach($list as $id => $row)
		{
			$t->define_data(array(
				"site_id" => $id,
				"url" => html::href(array(
					"url" => $row["url"],
					"caption" => $row["url"]
				)),
				"select" => html::checkbox(array(
					"name" => "select[$id]",
					"value" => $id,
					"checked" => $dat[$id] == $id
				))
			));
		}
		$t->set_default_sortby("site_id");
	}

	function _init_sel_users_t(&$t)
	{
		$t->define_field(array(
			"name" => "site",
			"caption" => t("Sait"),
			"sortable" => 1,
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "user",
			"caption" => t("Kasutaja, kellena teise saidi kasutajad ligi saavad"),
			"align" => "center"
		));
	}

	function _do_sel_users_t($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_sel_users_t($t);
		$sl = get_instance(CL_INSTALL_SITE_LIST);

		$site2user = $arr["obj_inst"]->meta("site2user");

		$tmp = new object_list($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_USER")));
		$users = array("" => "") + $tmp->names();

		foreach(safe_array($arr["obj_inst"]->meta("dat")) as $id => $_id)
		{
			if (!$_id)
			{
				continue;
			}

			$url = $sl->get_url_for_site($id);

			$t->define_data(array(
				"site" => html::href(array(
					"url" => $url,
					"caption" => $url
				)),
				"user" => html::select(array(
					"name" => "site2user[$id]",
					"options" => $users,
					"value" => $site2user[$id]
				))
			));
		}
	}

	/**
		
		@attrib name=check_hash

		@param c_uid required
		@param c_hash required

	**/
	function check_hash($arr)
	{
		$row = $this->db_query("
			SELECT 
				* 
			FROM 
				remote_logins 
			WHERE 
				uid = '$arr[c_uid]' AND 
				hash = '$arr[c_hash]' AND
				tm > ".(time()-2*3600)."
		");

		$this->db_query("
			DELETE 
			FROM 
				remote_logins 
			WHERE 
				uid = '$arr[c_uid]' AND 
				hash = '$arr[c_hash]' 
		");

		if ($row)
		{
			return true;
		}
		return false;
	}

	/** autologin from remote site

		@attrib name=autologin nologin="1"

		@param remote_uid required
		@param remote_hash required
		@param remote_site required type=int
	**/
	function autologin($arr)
	{
		$sl = get_instance(CL_INSTALL_SITE_LIST);
		$srv = $sl->get_url_for_site($arr["remote_site"]);
		// check remote hash
		$res = $this->do_orb_method_call(array(
			"method" => "xmlrpc",
			"server" => $srv,
			"class" => "auth_remote_settings",
			"action" => "check_hash",
			"params" => array(
				"c_uid" => $arr["remote_uid"],
				"c_hash" => $arr["remote_hash"]
			),
			"no_errors" => 1
		));
		if ($res)
		{
			// get conf obj
			$conf = $this->_get_conf_obj();
			if ($conf)
			{
				$uid = $this->_get_uid_for_site($arr["remote_site"], $conf);
				if ($uid)
				{
					// log in
					$this->db_query("INSERT INTO user_hashes(hash, hash_time, uid)
						values('$arr[remote_hash]','".(time()+100)."','$uid')");
					$u = get_instance("users");	
					$ret = $u->login(array(
						"hash" => $arr["remote_hash"],
						"uid" => $uid,
						"dbg" => 1
					));
					if ($ret == "")
					{
						$ret = aw_ini_get("baseurl");
					}
					header("Location: $ret");
					die();
				}
			}
		}
		else
		{
			die(t("Antud link ei võimalda abikeskkonda siseneda, palun kontakteeruge <a href='mailto:support@automatweb.com'>administraatoriga</a> "));
		}
	}

	/** do autologin 

		@attrib name=remote_login default=1

		@param url required
	**/
	function remote_login($arr)
	{
		$hash = gen_uniq_id();
		$this->db_query("
			INSERT INTO 
				remote_logins(uid,hash,tm)
				values('".aw_global_get("uid")."','$hash',".time().")
		");
		header("Location: ".$arr["url"]."/orb.aw?class=auth_remote_settings&action=autologin&remote_uid=".aw_global_get("uid")."&remote_hash=$hash&remote_site=".aw_ini_get("site_id"));
	}

	function _get_conf_obj()
	{
		$ol = new object_list(array(
			"class_id" => CL_AUTH_REMOTE_SETTINGS,
			"flags" => array(
				"mask" => OBJ_FLAG_IS_SELECTED,
				"flags" => OBJ_FLAG_IS_SELECTED
			)
		));
		if ($ol->count())
		{
			return $ol->begin();
		}
		return NULL;
	}

	function _get_uid_for_site($id, $conf)
	{
		$s2u = $conf->meta("site2user");
		$uo = $s2u[$id];
		if (is_oid($uo) && $this->can("view", $uo))
		{
			$uo = obj($uo);
			return $uo->name();
		}
		return NULL;
	}
}
?>
