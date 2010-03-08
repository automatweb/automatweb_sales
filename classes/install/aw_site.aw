<?php
// $Header: /home/cvs/automatweb_dev/classes/install/aw_site.aw,v 1.60 2009/05/28 08:32:37 dragut Exp $
/*

@classinfo syslog_type=ST_SITE relationmgr=yes no_comment=1 maintainer=kristo

@groupinfo templates caption=Templated
@groupinfo db caption=Andmebaas

@default table=objects
@default group=general
@default field=meta
@default method=serialize

@property site_url type=textbox
@caption Saidi url

@property default_user type=textbox
@caption Default kasutaja

@property default_user_pwd type=password
@caption Default kasutaja parool

@property use_existing_database type=checkbox ch_value=1 group=db
@caption Kasuta olemasolevat andmebaasi

@property select_db type=select group=db
@caption Vali andmebaas

@property select_parent_folder type=select group=db
@caption Vali kataloog olemasolevas baasis, kuhu alla sait lisatakse

@property use_existing_templates type=checkbox ch_value=1 group=templates
@caption Vali olemasolevad templated

@property select_tpl_sites type=select multiple=1 size=10 group=templates
@caption Vali saidid, mille templatesid valida saad

@property select_tpl_folders type=select multiple=1 size=20 group=templates
@caption Vali templatede kataloogid, mis uude saiti kopeerida

property tpls_are_linked type=checkbox ch_value=1 group=templates
caption Kas templated linkida, mitte kopeerida

@property select_imgcss_sites type=select group=templates
@caption Vali sait, millelt v&otilde;tta css ja pildid

@property gen_site type=checkbox ch_value=1
@caption Genereeri sait!

@property upd_site type=checkbox ch_value=1
@caption Uuenda saidi m&auml;&auml;rangud!

@property ch_warnings type=text store=no
@caption Uuendamise hoiatused

@property site_errmsg type=text
@caption Miks saiti ei saa genereerida

@property site_warnmsg type=text
@caption Hoiatused:

@reltype LAYOUT value=1 clid=CL_LAYOUT
@caption saidi layout
*/


class aw_site extends class_base
{
	function aw_site()
	{
		$this->init(array(
			'tpldir' => 'install/aw_site',
			'clid' => CL_SITE
		));
	}

	function get_property(&$arr)
	{
		$prop =&$arr['prop'];
		switch($prop['name'])
		{
			case "site_errmsg":
				if ($this->err_str == "")
				{
					return PROP_IGNORE;
				}
				$prop['value'] = $this->err_str;
				break;

			case "site_warnmsg":
				if ($this->warning_str == "")
				{
					return PROP_IGNORE;
				}
				$prop['value'] = $this->warning_str;
				break;

			case "upd_site":
				if (!$arr["obj_inst"]->meta("site_id"))
				{
					return PROP_IGNORE;
				}

				$this->ch_warnings = array();
				$changes = $this->_get_changes($arr["obj_inst"], $this->ch_warnings);

				if (!$changes)
				{
					return PROP_IGNORE;
				}
				break;

			case "ch_warnings":
				if (is_array($this->ch_warnings) && count($this->ch_warnings) > 0)
				{
					$prop['value'] = join("<br>", $this->ch_warnings);
				}
				else
				{
					return PROP_IGNORE;
				}
				break;

			case "gen_site":
				if ($arr["obj_inst"]->meta("site_id"))
				{
					return PROP_IGNORE;
				}

				if ($arr['obj_inst']->id())
				{
					$site = $this->get_site_def($arr['obj_inst']->id());
					if (!$this->is_site_ok($site))
					{
						return PROP_IGNORE;
					}
				}

				if ($arr['obj_inst']->meta('site_url') == '')
				{
					$this->err_str = t("Saidi url on m&auml;&auml;ramata!");
					return PROP_IGNORE;
				}
				break;

			case "select_db":
				if ($arr['obj_inst']->meta('use_existing_database') == 1)
				{
					if (!is_array($this->server_site_list))
					{
						if ($this->get_server_site_list() == PROP_IGNORE)
						{
							return PROP_IGNORE;
						}
					}

					$sl = $this->server_site_list;

					if (!is_array($sl))
					{
						$this->err_str = t("Ei saanud lugeda serveri saitide nimekirja! (vali andmebaas)");
						return PROP_IGNORE;
					}

					$prop['options'] = array("" => "");
					foreach($sl as $sid => $sd)
					{
						$prop['options'][$sd['url']] = $sd['name'];
					}
				}
				else
				{
					return PROP_IGNORE;
				}
				break;

			case "select_parent_folder":
				if ($arr['obj_inst']->meta('use_existing_database') == 1 && $arr['obj_inst']->meta('select_db') != "")
				{
					// get list of folders for the site
					$serv = str_replace("http://","",$arr['obj_inst']->meta('select_db'));
					$flds = $this->do_orb_method_call(array(
						"class" => "objects",
						"action" => "get_list",
						"params" => array(
							"rootobj" => -1
						),
						"method" => "xmlrpc",
						"server" => $serv,
						"no_errors" => true
					));
					if (!is_array($flds))
					{
						$this->err_str = sprintf(t("Ei saanud lugeda kataloogide nimekirja saidist %s!"), $serv);
					}
					$prop['options'] = $flds;
				}
				else
				{
					return PROP_IGNORE;
				}
				break;

			case "select_tpl_sites":
				if ($arr['obj_inst']->meta('use_existing_templates') != 1)
				{
					return PROP_IGNORE;
				}

				if (!is_array($this->server_site_list))
				{
					if ($this->get_server_site_list() == PROP_IGNORE)
					{
						return PROP_IGNORE;
					}
				}

				$sl = $this->server_site_list;

				if (!is_array($sl))
				{
					$this->err_str = t("Ei saanud lugeda serveri saitide nimekirja! (vali templatede sait)");
					return PROP_IGNORE;
				}

				$prop['options'] = array("" => "");
				foreach($sl as $sid => $sd)
				{
					$prop['options'][$sd['url']] = $sd['name'];
				}
				break;

			case "select_imgcss_sites":
				if ($arr['obj_inst']->meta('use_existing_templates') != 1)
				{
					return PROP_IGNORE;
				}

				if (!is_array($this->server_site_list))
				{
					if ($this->get_server_site_list() == PROP_IGNORE)
					{
						return PROP_IGNORE;
					}
				}

				$sl = $this->server_site_list;

				if (!is_array($sl))
				{
					$this->err_str = t("Ei saanud lugeda serveri saitide nimekirja! (vali piltide ja css sait)");
					return PROP_IGNORE;
				}

				$prop['options'] = array("" => "");
				foreach($sl as $sid => $sd)
				{
					$prop['options'][$sd['url']] = $sd['name'];
				}
				break;

			case "select_tpl_folders":
				$isar = is_array($arr['obj_inst']->meta('select_tpl_sites')) && count($arr['obj_inst']->meta('select_tpl_sites')) > 0;
				if ($arr['obj_inst']->meta('use_existing_templates') != 1 || !$isar || $arr['obj_inst']->meta('tpls_are_linked') == 1)
				{
					return PROP_IGNORE;
				}

				// now get list for all selected sites
				$fl = array();
				foreach($arr['obj_inst']->meta('select_tpl_sites') as $sn)
				{
					$sn = str_replace("http://","",$sn);

					$_t = $this->do_orb_method_call(array(
						"class" => "templatemgr",
						"action" => "get_template_folder_list",
						"method" => "xmlrpc",
						"server" => $sn,
						"no_errors" => true
					));


					if (is_array($_t))
					{
						foreach($_t as $folder)
						{
							if (substr($folder, -3)  != "CVS")
							{
								// make folder names more prettier for the user
								$prop['options'][$folder] = $folder;
							}
						}
					}
				}
				break;

			case "select_layout":
				if (!($arr['obj_inst']->meta('use_existing_database') && $arr['obj_inst']->meta('select_db') == "http://aw.struktuur.ee"))
				{
					return PROP_IGNORE;
				}
				break;
		}
		return PROP_OK;
	}

	function set_property($arr)
	{
		$prop =& $arr["prop"];
		switch($prop["name"])
		{
			case "site_url":
				if (!is_valid("url", $prop["value"]))
				{
					$prop["error"] = t("Saidi urlis v&otilde;ivad sisalduda ainult numbrid, t&auml;hed, punkt ja sidekriips. URL'i ette pole vaja panna http://'d!");
					return PROP_FATAL_ERROR;
				}
				break;
		}
		return PROP_OK;
	}

	function callback_post_save($arr)
	{
		extract($arr);
		$ob = obj($id);
		if ($ob->meta('upd_site') && ($arr["request"]["group"] == "general" || $arr["request"]["group"] == ""))
		{
			$site = $this->get_site_def($id);
			$this->_do_update_site($ob);
			$ob->set_meta("upd_site", 0);
			$ob->set_meta("old_site_opts", $site);
			$ob->save();
		}
		else
		if ($ob->meta('gen_site') && ($arr["request"]["group"] == "general"  || $arr["request"]["group"] == ""))
		{
			aw_set_exec_time(AW_LONG_PROCESS);
			obj_set_opt("no_cache", 1);
			aw_global_set("no_cache_flush", 1);
			// clear objects list
			$GLOBALS["objects"] = array();
			$site = $this->get_site_def($id);

			unset($_SESSION["non_logged_in_users_group_oid"]);

			$tmp = obj(aw_global_get("uid_oid"));

			if (!$this->is_site_ok($site))
			{
				$this->raise_error(ERR_SITE_CFG, sprintf(t("error in site config: %s"), $this->err_str),true,false);
			}

			echo t("Loon saiti! \n<br />HOIATUS! Saidi loomine v&otilde;tab paar minutit aega!<br /><br />\n");
			flush();

			$ini_opts = array();

			aw_global_set("__is_install",1);

			// now, do the actual thing.

			// start logger
			$log = get_instance("install/aw_site_gen_log");
			$log->start_log(array(
				"parent" => $ob->parent(),
				"name" => sprintf(t("Saidi %s loomise log"), $site["url"])
			));

			// first, create site folders
			$this->create_site_folders($site, $ini_opts, &$log);

			// create site name in nameserver if it does not exist
			$this->create_site_name($site, $ini_opts, &$log);

			// get new site_id for site
			$this->get_site_id($site, $ini_opts, &$log);

			// now, create database
			$this->create_site_database($site, $ini_opts, &$log);

			// now let each class that is registered handle the install process
			// each class gets an instance of a dummy class, where it can set
			// properties and then those properties will get written to the ini file
			$this->do_init_classes($site, $ini_opts, &$log);

			// now write options to the ini file
			$this->create_ini_file($site, $ini_opts, &$log);

			//$log->finish_log();

			// set the site id to the current object so we only let the user change it
			/*$ob->set_meta("site_id", $ini_opts["site_id"]);
			$ob->set_meta("gen_site", 0);
			$ob->set_meta("old_site_opts", $site);
			$ob->save();*/

			// now restart webserver
			//echo "restarting webserver ... <br />\n";
			aw_global_set("__is_install", 0);
			flush();
			touch("/tmp/ap_reboot");
			echo t("<br><br><b>Valmis! sait on kasutatav 30 sekundi p&auml;rast!</b><br />\n");
			flush();
			die();
		}
	}

	function _do_add_folder($fld, &$log)
	{
		$si = get_instance("install/su_exec");
		$si->add_cmd("mkdir ".$fld);
		$si->add_cmd("chmod 777 ".$fld);
		$si->exec();
		$stat = t("OK");
		if (!is_dir($fld))
		{
			$stat = t("Kataloogi ei ole!");
		}
		$log->add_line(array(
			"uid" => "System",
			"msg" => t("Lisas kataloogi"),
			"comment" => $fld,
			"result" => $stat
		));
	}

	function create_site_folders($site, &$ini_opts, &$log)
	{
		//echo "Loon katalooge .... <br />\n";
		//flush();

		// generate the script that creates the folders for the site

		// create the needed folders
		$this->_do_add_folder($site["docroot"], &$log);
		$ini_opts['site_basedir'] = $site['docroot'];

		$this->_do_add_folder($site['docroot']."/files", &$log);

		$this->_do_add_folder($site['docroot']."/lang", &$log);
		$si = get_instance("install/su_exec");
		$si->add_cmd("copy -r ".$this->cfg["basedir"]."/install/site_template/lang/* ".$site['docroot']."/lang/");
		$si->add_cmd("find ".$site['docroot']."/lang/ -type f -exec chmod 666 {} \;");
		$si->add_cmd("find ".$site['docroot']."/lang/ -type d -exec chmod 777 {} \;");

		$si->add_cmd("copy -r ".$this->cfg["basedir"]."/install/site_template/files/* ".$site['docroot']."/files/");
		$si->add_cmd("find ".$site['docroot']."/files/ -type f -exec chmod 666 {} \;");
		$si->add_cmd("find ".$site['docroot']."/files/ -type d -exec chmod 777 {} \;");

		$si->exec();
		$log->add_line(array(
			"uid" => "System",
			"msg" => t("Kopeeris default keelefailid"),
			"comment" => "",
			"result" => t("OK")
		));

		$this->_do_add_folder($site['docroot']."/pagecache", &$log);
		$ini_opts['cache.page_cache'] = "\${site_basedir}/pagecache";

		$this->_do_add_folder($site['docroot']."/public", &$log);

		if (!$site["site_obj"]["tpls_are_linked"])
		{
			$this->_do_add_folder($site['docroot']."/templates", &$log);
			$ini_opts['tpldir'] = "\${site_basedir}/templates";
		}

		// now copy base templates to the just-created templates folder
		$si = get_instance("install/su_exec");
		$si->add_cmd("copy -r ".$this->cfg["basedir"]."/install/site_template/templates/* ".$site['docroot']."/templates/");
		$si->add_cmd("find ".$site['docroot']."/templates/ -type f -exec chmod 666 {} \;");
		$si->add_cmd("find ".$site['docroot']."/templates/ -type d -exec chmod 777 {} \;");
		$si->exec();
		$log->add_line(array(
			"uid" => "System",
			"msg" => t("Kopeeris default kujunduse"),
			"comment" => "",
			"result" => t("OK")
		));


		$this->_do_add_folder($site['logroot'], &$log);

		// create apache vhost file
		$vhost_template = $this->get_file(array("file" => $this->cfg["tpldir"] . "/apache_conf/vhost.conf"));
		$vars = array(
			"date" => $this->time2date(time(),2),
			"servername" => $site['url'],
			"docroot" => $site['docroot'],
			"logroot" => $site['logroot'],
			"ip" => aw_ini_get("install.default_ip"),
		);
		$vhost_conf = localparse($vhost_template,$vars);
		$vhost_file_name = aw_ini_get("server.tmpdir")."/aw_install_vhost.conf";
		$this->put_file(array(
			"file" => $vhost_file_name,
			"content" => $vhost_conf
		));

		$si = get_instance("install/su_exec");
		$si->add_cmd("copy $vhost_file_name ".$site["vhost_file"]);
		$si->exec();

		$stat = t("OK");
		if (!file_exists($site['vhost_file']))
		{
			$stat = t("Faili ei ole!");
		}
		$log->add_line(array(
			"uid" => "System",
			"msg" => t("Tegi virtualhost konfi!"),
			"comment" => $site["vhost_file"],
			"result" => $stat
		));

		// create the link for the automatweb folder
		$si = get_instance("install/su_exec");
		$si->add_cmd("ln -s ".($this->cfg["basedir"]."/automatweb")." ".$site["docroot"]."/public/automatweb");
		$si->exec();
		$stat = t("OK");
		if (!is_link($site["docroot"]."/public/automatweb"))
		{
			$stat = t("Linki ei ole!");
		}
		$log->add_line(array(
			"uid" => "System",
			"msg" => t("Linkis automatweb kataloogi"),
			"comment" => $site["docroot"]."/public/automatweb",
			"result" => $stat
		));

		// copy the default code files to public folder
		// files: index.aw . login.aw , orb.aw, reforb.aw, site.aw, site_header.aw, site_footer.aw
		$si = get_instance("install/su_exec");
		$si->add_cmd("copy -r ".$this->cfg["basedir"]."/install/site_template/public/* ".$site["docroot"]."/public/");
		$si->add_cmd("chmod 666 $site[docroot]/public/*aw");
		$si->add_cmd("chmod 777 $site[docroot]/public/css");
		$si->add_cmd("chmod 777 $site[docroot]/public/img");
		$si->add_cmd("chmod 777 $site[docroot]/public/js");
		$si->add_cmd("chmod 666 $site[docroot]/public/img/*");
		$si->add_cmd("chmod 666 $site[docroot]/public/css/*");
		$si->add_cmd("chmod 666 $site[docroot]/public/js/*");
		$si->exec();

		// now, if the user said, that we gots to copy some foldres from other sites, then do that as well
		if ($site['site_obj']['use_existing_templates'] == 1)
		{
			$this->do_copy_existing_templates($site);
		}


		$log->add_line(array(
			"uid" => "System",
			"msg" => t("Kopeeris saidi koodi"),
			"comment" => "",
			"result" => t("OK")
		));

		// now, make the const.aw file from the template
		$constaw_template = $this->get_file(array("file" => $this->cfg["tpldir"] . "/apache_conf/const.aw.tpl"));
		$vars = array(
			"aw_dir" => $this->cfg["basedir"],
			"site_dir" => $site["docroot"]
		);
		$constaw = localparse($constaw_template,$vars);
		$constaw_file_name = aw_ini_get("server.tmpdir")."/aw_install_const.aw";
		$this->put_file(array(
			"file" => $constaw_file_name,
			"content" => $constaw
		));

		$si = get_instance("install/su_exec");
		$si->add_cmd("copy $constaw_file_name ".$site["docroot"]."/public/const.aw");
		$si->exec();
		$stat = t("OK");
		if (!file_exists($site["docroot"]."/public/const.aw"))
		{
			$stat = t("Faili ei ole!");
		}
		$log->add_line(array(
			"uid" => "System",
			"msg" => t("Tegi const.aw faili"),
			"comment" => $site["docroot"]."/public/const.aw",
			"result" => $stat
		));
	}

	function do_init_classes($site, &$ini_opts, &$log)
	{
		// ok, fuck it, we fake the site_id so that the objects all get the correct site_id
		$osid = aw_ini_get("site_id");
		aw_global_set("real_site_id", $osid);
		aw_ini_set("site_id", $ini_opts["site_id"]);

		// connect to the site database
		$dbi = get_instance("class_base");
		$dbi->db_connect(array(
			'driver' => 'mysql',
			'server' => $ini_opts['db.host'],
			'base' => $ini_opts['db.base'],
			'username' => $ini_opts['db.user'],
			'password' => $ini_opts['db.pass']
		));

		// right. now we must somehow assume the identity of the new site.
		// quiestion is, how the hell do we do that?
		// ok, what the hell, right now just update objects.site_id to have the new id after doing stuff
		// createdby uids will still be incorrect, but we can update that later as well

		// execute object script under the new datasource
		$old_ds = $GLOBALS["object_loader"]->switch_db_connection($dbi->dc[$dbi->default_cid]);
		// turn off acl checks, they'd fail
		$GLOBALS["cfg"]["acl"]["no_check"] = 1;
		// turn off storage cache, it concerns the other site and would be wrong
		obj_set_opt("no_cache", 1);
		// set the default datasource to the new db as well, so that instanced classes via messages will deal with
		// the new db
		// remember old default db
		$default_db = aw_global_get("db::".$this->default_cid);
		aw_global_set("db::".$this->default_cid, $dbi->dc[$dbi->default_cid]);

		aw_global_set("__install_db", $dbi->dc[$dbi->default_cid]);

		$clss = aw_ini_get("install.init_classes");
		foreach($clss as $class)
		{
			$inst = get_instance($class);
			if (method_exists($inst, "on_site_init"))
			{
				$inst->on_site_init($dbi, $site, $ini_opts, $log);
			}
		}

		if (!$site['site_obj']['use_existing_database'])
		{
			// start it all. create the root object. the father. the ROOT of all things - both good and evil. may life treat it well.
			// farewell, my darling! go, and flourish!
			$dbi->db_query("INSERT INTO objects(oid, name, class_id, parent, status) values(1,'root',1,0,2)");
			$dbi->db_query("INSERT INTO menu(id, type) values(1,".MN_CLIENT.")");
			$_root_o = 1;
			$script = $this->cfg["basedir"]."/scripts/install/object_scripts/simple.ojs";
		}
		else
		{
			$_root_o = $site['site_obj']['select_parent_folder'];
			$script = $this->cfg["basedir"]."/scripts/install/object_scripts/existing_db.ojs";
		}

		$osi = get_instance("install/object_script_interpreter");
		$osi->exec_file(array(
			"file" => $script,
			"vars" => array(
				"parent" => $_root_o,
				"url" => $site["name"],
				"default_user" => $site["site_obj"]["default_user"],
				"default_user_pwd" => md5($site["site_obj"]["default_user_pwd"]),
			)
		));
		$ini_opts += $osi->_get_ini_settings();
		$osi_vars = $osi->_get_sym_table();

		$clss = aw_ini_get("install.init_classes_after");
		foreach($clss as $class)
		{
			$inst = get_instance($class);
			if (method_exists($inst, "on_site_init"))
			{
				$inst->on_site_init($dbi, $site, $ini_opts, $log, $osi_vars);
			}
		}

		echo "finish init classes <br>\n";
		flush();

		if (!$site['site_obj']['use_existing_database'])
		{
			$dbi->db_query("UPDATE objects SET lang_id = 1");
			$dbi->db_query("UPDATE objects SET parent = ".$ini_opts["groups.tree_root"]." WHERE class_id = ".CL_GROUP);

			echo "updated lang id and paren <br>\n";
			flush();

			// acl
			$acls = array(
				"can_edit" => 1,
				"can_add" => 1,
				"can_admin" => 1,
				"can_delete" => 1,
				"can_view" => 1
			);
			$no_acc = array(
				"can_edit" => 0,
				"can_add" => 0,
				"can_admin" => 0,
				"can_delete" => 0,
				"can_view" => 0
			);
			$dbi->db_query("select gid,oid FROM groups WHERE type = 0 AND gid != ".$ini_opts["groups.all_users_grp"]);
			while ($row = $dbi->db_next())
			{
				echo "gid = $row[gid] <br>\n";
				flush();

				$dbi->save_handle();
				// access to root menu
				$dbi->add_acl_group_to_obj($row["gid"], $ini_opts["admin_rootmenu2"]);
				$dbi->save_acl($ini_opts["admin_rootmenu2"], $row["gid"], $osi_vars["groups.admins"] == $row["oid"] ? $acls : $no_acc);

				// also, acl rel
				aw_disable_messages();
				$go = obj($ini_opts["admin_rootmenu2"]);
				$go->connect(array(
					"to" => $row["oid"],
					"reltype" => RELTYPE_ACL
				));
				aw_restore_messages();

				// access to users folder
				$dbi->add_acl_group_to_obj($row["gid"], $osi_vars["users"]);
				$dbi->save_acl($osi_vars["users"], $row["gid"], $acls);

				// also, acl rel
				aw_disable_messages();
				$go = obj($osi_vars["users"]);
				$go->connect(array(
					"to" => $row["oid"],
					"reltype" => RELTYPE_ACL
				));
				aw_restore_messages();

				$dbi->restore_handle();
			}
		}

		echo "gid init done <br>\n";
		flush();

		// now, create the menus based on subs in main.tpl
		$this->_do_create_menus_from_template($dbi, $site, $ini_opts, $log, $osi_vars);

		echo "did menus from template <br>\n";
		flush();

		if (!$site['site_obj']['use_existing_database'])
		{
			// fix user object names
			$dbi->db_query("SELECT uid,oid FROM users");
			while ($row = $dbi->db_next())
			{
				$dbi->save_handle();
				$dbi->db_query("UPDATE objects SET name = '$row[uid]' WHERE brother_of = '$row[oid]'");
				$dbi->restore_handle();
			}

			$dbi->db_query("UPDATE objects SET site_id = ".$ini_opts["site_id"]);
			$dbi->db_query("UPDATE objects SET createdby = '".$site["site_obj"]["default_user"]."', modifiedby = '".$site["site_obj"]["default_user"]."'");
			$dbi->db_query("UPDATE objects SET lang_id = 1");
		}

		echo t("did user object names <br>\n");
		flush();

		echo t("Convert acl to new version");
		flush();
		// convert acl to new version
		$c = get_instance("admin/converters");
		$c->acl_to_objtbl(array("no_die" => 1));
		$ini_opts["acl.use_new_acl"] = 1;
		$ini_opts["acl.use_new_acl_final"] = 1;
		$ini_opts["document.use_new_parser"] = 1;
		$ini_opts["promo.version"] = 2;
		$ini_opts["menuedit.404redir"] = "${baseurl}/error";

		aw_ini_set("site_id", $osid);

		$GLOBALS["object_loader"]->switch_db_connection($old_ds);
		$GLOBALS["cfg"]["acl"]["no_check"] = 0;
		aw_global_set("db::".$this->default_cid, $default_db);

		echo t("init classes exit <br>\n");
		flush();
	}

	function create_site_name($site, &$ini_opts, &$log)
	{
		echo "Muudan nimeserveri konfiguratsiooni...<br />\n";
		flush();

		$mgr_server = $this->get_dns_manager_for_url($site["url"]);
		//echo "mgr_server = $mgr_server <br />";
		if ($mgr_server !== false)
		{
			//echo "doing rpc call to change $site[url] 's ip to ",aw_ini_get("install.default_ip")," <br />";
			$this->do_orb_method_call(array(
				"class" => "dns_server_manager",
				"action" => "add_or_update_site",
				"params" => array(
					"domain" => $site["url"],
					"ip" => aw_ini_get("install.default_ip")
				),
				"method" => "xmlrpc",
				"server" => $mgr_server
			));
		}
		$ini_opts["baseurl"] = "http://".$site['url'];
		$ini_opts["stitle"] = $site['url'];
		$log->add_line(array(
		"uid" => "System",
			"msg" => t("Konfigureeris nimeserveri"),
			"comment" => aw_ini_get("install.default_ip"),
			"result" => t("OK")
		));
	}

	function create_site_database($site, &$ini_opts, &$log)
	{
echo "create db <br>\n";
flush();
		if ($site['site_obj']['use_existing_database'])
		{
			//echo "reading database access data from the existing site<br />\n";
			flush();

			$db_dat = $this->do_orb_method_call(array(
				"class" => "objects",
				"action" => "aw_ini_get_mult",
				"method" => "xmlrpc",
				"server" => str_replace("http://","",$site['site_obj']['select_db']),
				"no_errors" => 1,
				"params" => array(
					"db.user",
					"db.host",
					"db.base",
					"db.pass"
				)
			));

			$ini_opts['db.user'] = $db_dat['db.user'];
			$ini_opts['db.host'] = $db_dat['db.host'];
			$ini_opts['db.base'] = $db_dat['db.base'];
			$ini_opts['db.pass'] = $db_dat['db.pass'];
			//echo "got db inf = <pre>", var_dump($db_dat),"</pre> <br />";
		}
		else
		{
			echo "Loon andmebaasi...<br />\n";
			flush();
			echo "creating database .. <br />";
			$dbi = get_instance("class_base");
			$dbi->db_connect(array(
				'driver' => 'mysql',
				'server' => aw_ini_get('install.mysql_host'),
				'base' => 'mysql',
				'username' => aw_ini_get('install.mysql_user'),
				'password' => aw_ini_get('install.mysql_pass')
			));

			// create database
			$q = "CREATE DATABASE $site[db_name]";
			echo "exec $q <br />";
			$dbi->db_query($q);

			// grant permission
			$q = "
				GRANT ALL PRIVILEGES
					ON $site[db_name].*
					TO $site[db_user]@".aw_ini_get("install.mysql_client")."
					IDENTIFIED BY '$site[db_pwd]'
			";
			echo "exec $q <br />";
			$dbi->db_query($q);

			$ini_opts['db.user'] = $site['db_user'];
			$ini_opts['db.host'] = aw_ini_get("install.mysql_host");
			$ini_opts['db.base'] = $site['db_name'];
			$ini_opts['db.pass'] = $site['db_pwd'];
			$ini_opts["site_search_content.has_fulltext_index"] = 1;
			$log->add_line(array(
				"uid" => aw_ini_get('install.mysql_user'),
				"msg" => t("L&otilde;i saidi andmebaasi"),
				"comment" => $site["db_name"],
				"result" => t("OK")
			));
		}
echo "db done <br>\n";
flush();
	}


	function is_site_ok($site)
	{
		// check if the site base folder exists
		if (is_dir($site['docroot']))
		{
			$this->err_str = sprintf(t("Saidi baaskataloog on juba olemas! (%s) "), $site["docroot"]);
			return false;
		}

		// check if the log folder exists
		if (is_dir($site['logroot']))
		{
			$this->err_str = sprintf(t("Saidi logide kataloog on juba olemas! (%s) "), $site["logroot"]);
			return false;
		}

		// check if the site vhost file exists
		if (file_exists($site['vhost_file']))
		{
			$this->err_str = sprintf(t("Saidi apache konfiguratsioon on juba olemas! (%s) "), $site["vhost_file"]);
			return false;
		}

		// if we selected that we want to use templates, then make sure we have selected some
		if ($site['site_obj']['use_existing_templates'])
		{
			if (!is_array($site['site_obj']['select_tpl_sites']) || count($site['site_obj']['select_tpl_sites']) < 1)
			{
				$this->err_str = t("Saidid, kust templatesid kopeerida on valimata! ");
				return false;
			}
			else
			if (!$site["site_obj"]["tpls_are_linked"] && (!is_array($site['site_obj']['select_tpl_folders']) || count($site['site_obj']['select_tpl_folders']) < 1))
			{
				$this->err_str = t("Kataloogid, kust templatesid kopeerida on valimata! ");
				return false;
			}
		}

		// if we are writing to an existing database, check if we have selected all the necessary stuff
		// and then if we can access the database (if the code version is big enough to support xmlrpc)
		if ($site['site_obj']['use_existing_database'])
		{
			if ($site['site_obj']['select_db'] == "")
			{
				$this->err_str = t("Kasutatav andmebaas on valimata!");
				return false;
			}

			if (!$site['site_obj']['select_parent_folder'])
			{
				$this->err_str = t("Uue saidi root kataloog olemasolevas andmebaasis on valimata!");
				return false;
			}

			// try to fetch db login data
			$db_dat = $this->do_orb_method_call(array(
				"class" => "objects",
				"action" => "aw_ini_get_mult",
				"method" => "xmlrpc",
				"server" => str_replace("http://","",$site['site_obj']['select_db']),
				"no_errors" => 1,
				"params" => array("baseurl")
			));
			if (!is_array($db_dat))
			{
				$this->err_str = t("Sait on liiga vana koodiversiooniga et selle p&otilde;hjal uut luua!");
				return false;
			}
		}
		else
		{
			// check if the database exists
			$dbi = get_instance("class_base");
	//die(aw_ini_get('install.mysql_user'));		
$dbi->db_connect(array(
				'driver' => 'mysql',
				'server' => aw_ini_get('install.mysql_host'),
				'base' => 'mysql',
				'username' => aw_ini_get('install.mysql_user'),
				'password' => aw_ini_get('install.mysql_pass')
			));
			$dbi->db_list_databases();
			$found = false;
			while ($db = $dbi->db_next_database())
			{
				if ($db['name'] == $site['db_name'])
				{
					$found = true;
					break;
				}
			}

			if ($found)
			{
				$this->err_str = sprintf(t("Saidi andmebaas on juba olemas! (%s) "), $site["db_name"]);
				return false;
			}
		}


		// check if we can manage the nameserver for the url
		// if we can not, then check if the domain name exists
		if (!$this->is_managed_nameserver($site['url']))
		{
			$ip = gethostbyname($site['url']);
			if ($ip == $site['url'])
			{
				$this->warning_str = sprintf(t("Saidi domeeni nimeserver ei ole Automatwebi poolt hallatav ja saidi domeeni pole registreeritud! (%s) "), $site["url"]);
			}
			if ($ip != aw_ini_get("install.default_ip"))
			{
				$this->err_str = sprintf(t("Saidi domeeni nimeserver ei ole Automatwebi poolt hallatav ja saidi domeen viitab valele IP aadressile! (vajalik = %s domeeni ip = %s)"), aw_ini_get("install.default_ip"), $ip);
			}
		}
		else
		{
			if (($_ip = gethostbyname($site['url'])) != $site['url'])
			{
				$this->warning_str = sprintf(t("Saidi domeen on juba registreeritud! (domeen = %s ip = %s) Kui selle domeeni pealt k&auml;ib m&otilde;ni teine sait, siis seda ei saa p&auml;rast uue saidi loomist kasutada!"), $site["url"], $_ip);
			}
		}

		// check if a site by that url is already defined in the site db
		$site_id = $this->do_orb_method_call(array(
			"class" => "site_list",
			"action" => "get_site_id_by_url",
			"params" => array(
				'url' => $site['url']
			),
			"method" => "xmlrpc",
			"server" => "register.automatweb.com"
		));
		if ($site_id)
		{
			$this->err_str = sprintf(t("Saitide registris on juba sait, mille url on %s !"), $site["url"]);
			return false;
		}

		// check if a server is defined in the server db
		$server_id = $this->do_orb_method_call(array(
			"class" => "site_list",
			"action" => "get_server_id_by_ip",
			"params" => array(
				"ip" => aw_ini_get("install.default_ip"),
			),
			"method" => "xmlrpc",
			"server" => "register.automatweb.com"
		));
		if (!$server_id)
		{
			$this->err_str = sprintf(t("IP aadressi %s jaoks pole serverite registris kirjet!"), aw_ini_get("install.default_ip"));
			return false;
		}

		// check if we got root access
		$sue = get_instance("install/su_exec");
		if (!$sue->is_ok())
		{
			$this->err_str = t("Ei saa root kasutaja &otilde;iguseid kasutada!");
			return false;
		}
		return true;
	}

	function is_managed_nameserver($url)
	{
		if ($this->get_dns_manager_for_url($url) === false)
		{
			return false;
		}
		return true;
	}

	function get_site_def($id)
	{
		$ob = obj($id);
		$site_url = str_replace("http://","",$ob->meta('site_url'));
		$site_url = str_replace("/","",$site_url);

		$site_name = str_replace("http://","",$ob->name());
		$site_name = str_replace("/","",$site_name);

		$site = array();
		$site['url'] = $site_url;
		$site['name'] = $site_name;
		$site['docroot'] = aw_ini_get('install.docroot').$site_url;
		$site['logroot'] = aw_ini_get('install.logroot').$site_url;
		$site['vhost_file'] = aw_ini_get('install.vhost_folder').$site_url;
		$site['server_ip'] = aw_ini_get('install.default_ip');
		$site['admin_folder'] = aw_ini_get('install.admin_folder');
		$site['db_name'] = str_replace(array(".", "-"),"",$site_url);
		// db users in mysql MUST begin with a letter, not number ...
		$site['db_user'] = "a".substr(md5(str_replace(".","",$site_url)), 0, 14);
		$site['db_pwd'] = generate_password();
		$site['site_obj'] = $ob->meta();
		return $site;
	}

	function get_dns_manager_for_url($url)
	{
		$dns = get_instance("core/util/dns");
		$ns = $dns->get_record_NS(array(
			"domain" => $url,
		));
		// try to send an xmlrpc call to all returned nameservers
		// if it succeeds, then assume that we can manage the nameserver
		foreach($ns as $server)
		{
			$res = $this->do_orb_method_call(array(
				"class" => "dns_server_manager",
				"action" => "can_manage_server",
				"params" => array("server" => $server),
				"method" => "xmlrpc",
				"server" => $server,
				"no_errors" => true
			));
			if ($res == true)
			{
				return $server;
			}
		}
		return false;
	}

	function create_ini_file($site, &$ini_opts, &$log)
	{
		//echo "ini_opts = <pre>", var_dump($ini_opts),"</pre> <br />";
		// create temp ini file, then use su_exec to copy it to the correct place
		$tmpnam = tempnam(aw_ini_get("server.tmpdir"),"aw_install_ini");

		$ini_opts["menuedit.promo_lead_only"] = 1;

		if ($ini_opts["tpldir"] == "")
		{
			$ini_opts["tpldir"] = "\${site_basedir}/templates";
		}

		$fc = join("\n", map2('%s = %s', $ini_opts));
		$this->put_file(array(
			'file' => $tmpnam,
			"content" => $fc
		));

		$sue = get_instance("install/su_exec");
		$sue->add_cmd("copy $tmpnam ".$site['docroot']."/aw.ini");
		$sue->add_cmd("chmod 666 $site[docroot]/aw.ini");
		$sue->exec();
		$status = t("OK");
		if (!file_exists($site['docroot']."/aw.ini"))
		{
			$status = t("Faili ei ole!");
		}
		$log->add_line(array(
			"uid" => "System",
			"msg" => t("Tegi ini faili"),
			"comment" => $site['docroot']."/aw.ini",
			"result" => $status
		));
	}

	function get_site_id($site, &$ini_opts, &$log)
	{
		$server_id = $this->do_orb_method_call(array(
			"class" => "site_list",
			"action" => "get_server_id_by_ip",
			"params" => array(
				"ip" => aw_ini_get("install.default_ip"),
			),
			"method" => "xmlrpc",
			"server" => "register.automatweb.com"
		));
		echo "got server_id = $server_id <br />\n";
		flush();

		$site_id = $this->do_orb_method_call(array(
			"class" => "site_list",
			"action" => "update_site",
			"params" => array(
				"name" => $site['url'],
				"url" => "http://".$site['url'],
				"server_id" => $server_id,
				"ip" => aw_ini_get("install.default_ip"),
				"site_used" => true,
				"code_branch" => "HEAD",
			),
			"method" => "xmlrpc",
			"server" => "register.automatweb.com"
		));
		echo "got site id $site_id <br />";
		$ini_opts["site_id"] = $site_id;
		$log->add_line(array(
			"uid" => aw_global_get("uid"),
			"msg" => t("K&uuml;sis uue saidi id"),
			"comment" => $site_id,
			"result" => t("OK")
		));
	}

	function get_server_site_list()
	{
		// get current server id
		$server_id = $this->do_orb_method_call(array(
			"class" => "site_list",
			"action" => "get_server_id_by_ip",
			"params" => array(
				"ip" => aw_ini_get("install.default_ip")
			),
			"method" => "xmlrpc",
			"server" => "register.automatweb.com",
			"no_errors" => true
		));
		if (!$server_id)
		{
			$this->err_str = t("Ei saanud lugeda serveri id'd! (vali andmebaas)");
			return PROP_IGNORE;
		}

		// ok, here we must figoure out a list of sites in that server
		$this->server_site_list = $this->do_orb_method_call(array(
			"class" => "site_list",
			"action" => "get_site_list",
			"params" => array(
				//"server_id" => $server_id
			),
			"method" => "xmlrpc",
			"server" => "register.automatweb.com",
			"no_errors" => true
		));
	}

	function do_copy_existing_templates($site, $do_css = true, $do_perms = true, $do_tpls = true)
	{
		//echo "copy existing templates! <br>";
		// get list of all folders
		$fmap = array();

		$sue = get_instance("install/su_exec");

		if ($do_tpls)
		{
			foreach($site['site_obj']['select_tpl_sites'] as $sn)
			{
				$sn = str_replace("http://","",$sn);
				$_t = $this->do_orb_method_call(array(
					"class" => "objects",
					"action" => "aw_ini_get_mult",
					"method" => "xmlrpc",
					"server" => $sn,
					"params" => array(
						"vals" => array(
							"site_basedir",
							"tpldir"
						)
					),
					"no_errors" => true
				));

				if (is_array($_t))
				{
					foreach($_t as $n => $base_folder)
					{
						if ($n == "site_basedir")
						{
							$fmap[] = $base_folder;
						}
						else
						if ($n == "tpldir")
						{
							$template_folder = $base_folder;
						}
					}
				}
			}

			if ($site["site_obj"]["tpls_are_linked"] == 1)
			{
				$sue->add_cmd("ln -s $template_folder ".$site['docroot']."/templates");
				$ini_opts['tpldir'] = "\${site_basedir}/templates";
			}
			else
			{
				$ttt = $site['site_obj']['select_tpl_folders'];
				sort($ttt);
				foreach($ttt as $from_fld)
				{
					//echo "from_fld = $from_fld <br>";
					$to_fld = $from_fld;
					foreach($fmap as $base)
					{
						$to_fld = str_replace($base, "", $to_fld);
					}

					$to_fld = $site['docroot']."/".$to_fld;
					//echo "got to_fld as $to_fld <br>";

					$sue->add_cmd("mkdir $to_fld");
					$sue->add_cmd("copy $from_fld/*tpl $to_fld/");
					//echo "added cmd mkdir $to_fld <br />\n";
					//echo "addes cmd copy $from_fld/*tpl $to_fld/ <br />\n";
					flush();
				}
				$sue->add_cmd("find $site[docroot]/templates -type d -exec chmod 777 {} \;");
				$sue->add_cmd("find $site[docroot]/templates -type f -exec chmod 666 {} \;");
			}
		}

		// also, if selected, copy images and css files.
		if ($site["site_obj"]["select_imgcss_sites"] != "" && $do_css)
		{
			$sn = str_replace("http://","",$site["site_obj"]["select_imgcss_sites"]);
			$_t = $this->do_orb_method_call(array(
				"class" => "objects",
				"action" => "aw_ini_get_mult",
				"method" => "xmlrpc",
				"server" => $sn,
				"params" => array(
					"vals" => array(
						"site_basedir"
					)
				),
				"no_errors" => true
			));
			$bf = $_t["site_basedir"];
			if ($bf != "")
			{
				$to_fld = $site['docroot']."/public/img";
				$sue->add_cmd("copy -r $bf/public/img/* $to_fld/");
				echo "add cmd "."copy -r $bf/public/img/* $to_fld/"." <br>";
				$to_fld = $site['docroot']."/public/css";
				$sue->add_cmd("copy -r $bf/public/css/* $to_fld/");
				echo "add cmd "."copy -r $bf/public/css/* $to_fld/"." <br>";
			}
		}

		if ($do_perms)
		{

			$sue->add_cmd("chmod 777 $site[docroot]/public/css");
			$sue->add_cmd("chmod 777 $site[docroot]/public/img");
			$sue->add_cmd("chmod 666 $site[docroot]/public/img/*");
			$sue->add_cmd("chmod 666 $site[docroot]/public/css/*");
		}

		$sue->exec();
	}

	function _do_create_menus_from_template(&$dbi, &$site, &$ini_opts, &$log, &$osi_vars)
	{
		// get main.tpl
		$tpl = new aw_template;
		$tpl->read_tpl(file($site["docroot"]."/templates/automatweb/menuedit/main.tpl"));
		echo "tried to read template ".$site["docroot"]."/templates/automatweb/menuedit/main.tpl <br>";
		$tpls = $tpl->get_subtemplates_regex("(MENU_.*)");
		$_tpls = array();
 		foreach($tpls as $tpl)
		{
			list($tpl) = explode(".", $tpl);
			$_tpls[] = $tpl;
		}
		$tpls = array_unique($_tpls);

		// now, make array that says how many for each area
		// MENU_VASAK_L2_ITEM_FOO
		$areas = array();
		foreach($tpls as $tpl)
		{
			list($_t, $area, $level) = explode("_", $tpl);
			$areas[$area] = max(substr($level, 1), $areas[$area]);
		}
		foreach($areas as $area => $levels)
		{
			if ($area == "LOGGED")
			{
				// this gets special treatment
				continue;
			}
			echo "got menu area $area , with levels $levels <br>\n";
			flush();

			$crss = false;
			$astr = strtoupper(substr($area, 0,1)).strtolower(substr($area, 1));
			if ($astr == "P6hi")
			{
				$crss = true;
			}

			$astr = str_replace("6", "&otilde;", $astr);
			$astr = str_replace("y", "&uuml;", $astr);
			$astr = str_replace("Y", "&Uuml;", $astr);
			if ($astr == "P&otilde;hi")
			{
				$fn = $astr."men&uuml;&uuml;";
			}
			else
			if ($astr == "Footer")
			{
				$fn = "Jalus";
			}
			else
			{
				$fn = $astr." men&uuml;&uuml;";
			}

			// create root
			$o = obj();
			$o->set_class_id(CL_MENU);
			$o->set_parent($osi_vars["site_root"]);
			$o->set_status(STAT_ACTIVE);
			$o->set_prop("type", MN_CLIENT);
			$o->set_name($fn);
			$o->save();

			if ($crss)
			{
				$this->_create_rss($o, $osi_vars);
				$this->_create_demo_objects($o, $osi_vars, $dbi);
			}

			$pt = $o->id();
			echo "created root menu for area, name = ".$astr." men&uuml;&uuml; id = $pt <br>\n";
			// also set ini opt
			$ini_opts["menuedit.menu_defs[$pt]"] = $area;
			for ($i = 0; $i < $levels; $i++)
			{
				$o = obj();
				$o->set_class_id(CL_MENU);
				$o->set_parent($pt);
				$o->set_status(STAT_ACTIVE);
				$o->set_prop("type", MN_CONTENT);
				$o->set_name($astr." tase ".($i+1));
				$o->save();

				echo "created level $i menu for area, name = ".$astr." tase ".($i+1)." under obj $pt <br>\n";
				$pt = $o->id();
			}
		}

		$sn = "";
		if (is_array($site['site_obj']['select_tpl_sites']))
		{
			reset($site['site_obj']['select_tpl_sites']);
			list(, $sn) = each($site['site_obj']['select_tpl_sites']);
			$sn = str_replace("http://","",$sn);
		}

		if ($sn == "")
		{
			// try the imcss site
			$sn = $site['site_obj']['select_imgcss_sites'];
			$sn = str_replace("http://","",$sn);
		}

		if ($sn != "")
		{
			$_pa = $this->do_orb_method_call(array(
				"class" => "objects",
				"action" => "aw_ini_get_mult",
				"method" => "xmlrpc",
				"server" => $sn,
				"params" => array(
					"vals" => array(
						"promo.areas",
					)
				),
				"no_errors" => true
			));
		}

		$pa = $_pa["promo.areas"];
		if (is_array($pa) && count($pa) > 0)
		{
			$templates = $pa;
		}
		else
		{
			// make demo promo boxes
			$tpl = new aw_template;
			$tpl->read_tpl(file($site["docroot"]."/templates/automatweb/menuedit/main.tpl"));
			$tpls = $tpl->get_subtemplates_regex("(.*_PROMO)");
			$_tpls = array();
 			foreach($tpls as $tpl)
			{
				list($tpl) = array_reverse(explode(".", $tpl));
				if (substr($tpl, -5) == "PROMO")
				{
					$_tpls[] = $tpl;
				}
			}
			$tpls = array_unique($_tpls);

			$_templates = array(
				"SCROLL_PROMO" => "scroll",
				"LEFT_PROMO" => "0",
				"RIGHT_PROMO" => 1,
				"UP_PROMO" => "2",
				"DOWN_PROMO" => "3",
			);

			$_templates_n = array(
				"SCROLL_PROMO" => t("Skrolliv"),
				"LEFT_PROMO" => t("Vasak"),
				"RIGHT_PROMO" => t("Parem"),
				"UP_PROMO" => t("&Uuml;lemine"),
				"DOWN_PROMO" => t("Alumine"),
			);

			$templates = array();
			foreach($tpls as $tpl)
			{
				list($pre) = explode("_", $tpl);
				$templates[$_templates[$tpl]]["def"] = $pre;
				$templates[$_templates[$tpl]]["name"] = $_templates_n[$tpl];
			}
		}


		foreach($templates as $id => $dat)
		{
			$ini_opts["promo.areas[$id][def]"] = $dat["def"];
			$ini_opts["promo.areas[$id][name]"] = $dat["name"];

			$astr = $dat["name"];

			$o = obj();
			$o->set_class_id(CL_PROMO);
			$o->set_parent($osi_vars["cont"]);
			$o->set_status(STAT_ACTIVE);
			$o->set_name(sprintf(t("%s konteiner"), $astr));
			$o->set_prop("tpl_lead", 2);
			$o->set_prop("type", $id);
			$o->set_prop("all_menus", 1);
			$o->save();

			$do = obj();
			$do->set_class_id(CL_DOCUMENT);
			$do->set_parent($o->id());
			$do->set_status(STAT_ACTIVE);
			$do->set_prop("lead", sprintf(t("%s konteineri sisu"), $astr));
			$do->set_prop("title", t("pealkiri"));
			$do->set_name(t("pealkiri"));
			$do->save();
		}
	}

	function callback_mod_tab($arr)
	{
		if ($arr["id"] == "db" && $arr["obj_inst"]->meta("site_id"))
		{
			return false;
		}

		return true;
	}

	function _get_changes($o, &$warnings)
	{
		$changes = false;

		$old = $o->meta("old_site_opts");

		if ($o->prop("site_url") != $old["url"])
		{
			$changes = true;
			$warnings[] = sprintf(t("Saidi url muutus %s => %s"), $old["url"], $o->prop("site_url"));
		}

		if ($o->prop("use_existing_templates") != $old["site_obj"]["use_existing_templates"])
		{
			$changes = true;
			$warnings[] = t("HOIATUS! Templatede valiku muutmisel kirjutatakse olemasolevad templated &uuml;le!");
			$warnings[] = t("Templated muutusid default templatedest teise saidi omadeks!");
		}

		if ($o->prop("select_tpl_folders") != $old["site_obj"]["select_tpl_folders"])
		{
			$changes = true;
			$warnings[] = t("Templatede kataloogide nimekiri muutus!");
		}

		if ($o->prop("tpls_are_linked") != $old["site_obj"]["tpls_are_linked"])
		{
			$changes = true;
			$warnings[] = t("Templatede linkimine muutus!");
		}

		if ($o->prop("select_imgcss_sites") != $old["site_obj"]["select_imgcss_sites"])
		{
			$changes = true;
			$warnings[] = sprintf(t("Piltide ja css sait muutus (%s => %s)!"), $old["site_obj"]["select_imgcss_sites"], $o->prop("select_imgcss_sites"));
		}

		return $changes;
	}

	function _do_update_site($o)
	{
		// get old defs
		$old = $o->meta("old_site_opts");

		// check if template sources are modified
		if ($o->prop("use_existing_templates"))
		{
			$do_copy_existing_tpls = false;
			if ($o->prop("use_existing_templates") != $old["site_obj"]["use_existing_templates"])
			{
				$do_copy_existing_tpls = true;
			}
			else
			if ($o->prop("select_tpl_folders") != $old["site_obj"]["select_tpl_folders"])
			{
				$do_copy_existing_tpls = true;
			}

			if ($do_copy_existing_tpls)
			{
				$this->_do_copy_existing_tpls($o);
			}
		}

		if ($o->prop("select_imgcss_sites") != $old["site_obj"]["select_imgcss_sites"])
		{
			$this->_do_change_imgcss($o);
		}

		// check if url changed
		if ($o->prop("site_url") != $old["url"])
		{
			$this->_do_change_url($o, $old["url"]);
		}
	}

	/** changes the site's url from $old_url => $o->prop("site_url")

		@comment
			renames everything - apache vhost, basedir, the works
	*/
	function _do_change_url($o, $old_url)
	{
		// get old basedir
		$sn = str_replace("/", "", str_replace("http://","",$old_url));
		$nu = str_replace("/", "", str_replace("http://","",$o->prop("site_url")));

		$_t = $this->do_orb_method_call(array(
			"class" => "objects",
			"action" => "aw_ini_get_mult",
			"method" => "xmlrpc",
			"server" => $sn,
			"params" => array(
				"vals" => array(
					"site_basedir",
					"cache.page_cache",
				)
			),
			"no_errors" => true
		));

		$cache = $_t["cache.page_cache"]."/*";
		$sb = $_t["site_basedir"];

		// clear pagecache
		$sue = get_instance("install/su_exec");
		echo "cache = $cache , pg = ".$_t["cache.page_cache"]." <br>";
		if ($cache == "/*" || $_t["cache.page_cache"] == "" || !is_dir($_t["cache.page_cache"]) || strpos($_t["cache.page_cache"], $sn) === false)
		{
			error::raise(array(
				"id" => ERR_XLMRPC,
				"msg" => sprintf(t("aw_site::_do_change_url(%s): pagecache folder is empty or bad ( %s )!"), $old_url, $_t["cache.page_cache"])
			));
		}
		$sue->add_cmd("rm -rf $cache");

		// rewrite apache vhost file
		$vhname = aw_ini_get("install.vhost_folder").$sn;
		$vhname_to = aw_ini_get("install.vhost_folder").$nu;

		$tmpnam = tempnam(aw_ini_get("server.tmpdir"),"aw_install_vhost");
		$fc = str_replace($sn, $nu, $this->get_file(array("file" => $vhname)));
		$this->put_file(array(
			"file" => $tmpnam,
			"content" => $fc
		));
		$sue->add_cmd("move $tmpnam $vhname");

		// rename in server:

		//	- apache vhost
		$sue->add_cmd("move $vhname $vhname_to ");


		//	- basedir
		$sb_to = aw_ini_get("install.docroot").$nu;
		$sue->add_cmd("move $sb $sb_to ");

		//	- log folder
		$vhname = aw_ini_get("install.logroot").$sn;
		$vhname_to = aw_ini_get("install.logroot").$nu;
		$sue->add_cmd("move $vhname $vhname_to ");

		// rewrite ini file
		$inif = $sb."/aw.ini";
		$inif_nu = $sb_to."/aw.ini";
		$tmpnam = tempnam(aw_ini_get("server.tmpdir"),"aw_install_ini");
		$fc = str_replace($sn, $nu, $this->get_file(array("file" => $inif)));
		$this->put_file(array(
			"file" => $tmpnam,
			"content" => $fc
		));
		$sue->add_cmd("move $tmpnam $inif_nu");
		$sue->exec();

		// add url to nameserver
		$mgr_server = $this->get_dns_manager_for_url($nu);
		if ($mgr_server !== false)
		{
			$this->do_orb_method_call(array(
				"class" => "dns_server_manager",
				"action" => "add_or_update_site",
				"params" => array(
					"domain" => $nu,
					"ip" => aw_ini_get("install.default_ip")
				),
				"method" => "xmlrpc",
				"server" => $mgr_server
			));
		}

		// change url in site list
		$this->do_orb_method_call(array(
			"class" => "site_list",
			"action" => "update_site",
			"params" => array(
				"id" => $o->meta("site_id"),
				"name" => $nu,
				"url" => "http://".$nu,
			),
			"method" => "xmlrpc",
			"server" => "register.automatweb.com"
		));

		// restart apache
		touch("/tmp/ap_reboot");
		echo t("Valmis! Sait on uue aadressi pealt kasutatav paari minuti p&auml;rast! <Br>\n");
		flush();
	}

	/** changes the public/img public/css folder contents to the contents of the new location
	*/
	function _do_change_imgcss($o)
	{
		// ok, get the base folder
		$sn = str_replace("http://","",$o->prop("site_url"));
		$_t = $this->do_orb_method_call(array(
			"class" => "objects",
			"action" => "aw_ini_get_mult",
			"method" => "xmlrpc",
			"server" => $sn,
			"params" => array(
				"vals" => array(
					"site_basedir",
					"cache.page_cache",
				)
			),
			"no_errors" => true
		));

		$img = $_t["site_basedir"]."/public/img";
		$css = $_t["site_basedir"]."/public/css";

		$cache = $_t["cache.page_cache"]."/*";

		if ($cache == "/*" || $_t["cache.page_cache"] == "" || !is_dir($_t["cache.page_cache"]) || strpos($_t["cache.page_cache"], $sn) === false)
		{
			error::raise(array(
				"id" => ERR_XLMRPC,
				"msg" => sprintf(t("aw_site::_do_change_imgcss(): pagecache folder is empty or wrong (%s)!"), $_t["cache.page_cache"])
			));
		}

		if ($css == "/*" || $img == "/*" || $_t["site_basedir"] == "" || !is_dir($_t["site_basedir"]) || strpos($_t["site_basedir"], $sn) === false)
		{
			error::raise(array(
				"id" => ERR_XLMRPC,
				"msg" => sprintf(t("aw_site::_do_change_imgcss(): basedir is empty or bad (%s)!"), $_t["site_basedir"])
			));
		}
		$sue = get_instance("install/su_exec");
		$sue->add_cmd("rm -rf $css/*");
		$sue->add_cmd("rm -rf $img/*");
		$sue->add_cmd("rm -rf $cache");
		$sue->exec();

		$def = $this->get_site_def($o->id());
		$this->do_copy_existing_templates($def, true, false, false);

		// clear out pagecache

	}

	/** deletes the old templates from the site and copies new ones
	*/
	function _do_copy_existing_tpls($o)
	{
		// ok, get the template folder
		$sn = str_replace("http://","",$o->prop("site_url"));
		$_t = $this->do_orb_method_call(array(
			"class" => "objects",
			"action" => "aw_ini_get_mult",
			"method" => "xmlrpc",
			"server" => $sn,
			"params" => array(
				"vals" => array(
					"tpldir",
					"cache.page_cache"
				)
			),
			"no_errors" => true
		));
		$t = $_t["tpldir"];

		echo "got tpldir as $t <Br>";

		if (is_link($t))
		{
			echo "unlink $t <br>";
			unlink($t);
		}
		else
		{
			echo "rm -rf $t <br>";
			if ($t == "/*" || $_t["cache.page_cache"] == "" || !is_dir($_t["cache.page_cache"]) || strpos($_t["cache.page_cache"], $sn) === false)
			{
				error::raise(array(
					"id" => ERR_XLMRPC,
					"msg" => sprintf(t("aw_site::_do_copy_existing_tpls(): pagecache folder is empty or bad (%s)!"), $_t["cache.page_cache"])
				));
			}
			$sue = get_instance("install/su_exec");
			$sue->add_cmd("rm -rf $t");
			$sue->exec();
		}

		$cache = $_t["cache.page_cache"]."/*";
		$sue = get_instance("install/su_exec");
		$sue->add_cmd("rm -rf $cache");
		$sue->exec();

		$def = $this->get_site_def($o->id());
		$this->do_copy_existing_templates($def, false,false, true);
	}

	function _create_rss($o, $osi_vars)
	{
		$r = obj();
		$r->set_parent($o->id());
		$r->set_class_id(CL_MENU);
		$r->set_status(STAT_ACTIVE);
		$r->set_name(t("Meist"));
		$r->save();

		$r2 = obj();
		$r2->set_parent($r->id());
		$r2->set_class_id(CL_MENU);
		$r2->set_status(STAT_ACTIVE);
		$r2->set_name(t("Uudised"));
		$r2->save();

		$rss = obj($osi_vars["rss_o"]);
		$rss->connect(array(
			"to" => $r2->id(),
			"reltype" => "RELTYPE_FEED_SOURCE"
		));
		$rss->set_meta("include_subs", array($r2->id() => 1));
		$rss->save();
	}

	function _create_demo_objects($parent, $osi_vars, &$dbi)
	{
		$tmp = $osi_vars;
		$tmp["parent"] = $parent->id();

		$osi = get_instance("install/object_script_interpreter");
		$osi->exec_file(array(
			"file" => aw_ini_get("basedir")."/scripts/install/object_scripts/demo_objects.ojs",
			"vars" => $tmp
		));
		$vars = $osi->_get_sym_table();

		// do things osi can not
		// poll
		$poll = obj($vars["poll_o"]);
		$poll->set_meta("name", array(1 => t("Demo polli k&uuml;&uuml;simus")));

		$aid1 = $dbi->db_fetch_field("SELECT MAX(id) as id FROM poll_answers", "id")+1;
		$dbi->db_query("INSERT INTO poll_answers(id, answer,poll_id) values($aid1,'".t("Jah")."','".$poll->id()."')");
		$dbi->db_query("INSERT INTO poll_answers(id, answer,poll_id) values($aid1+1,'".t("ei")."','".$poll->id()."')");
		$dbi->db_query("INSERT INTO poll_answers(id, answer,poll_id) values($aid1+2,'".t("V&otilde;ibolla")."','".$poll->id()."')");

		$poll->set_meta("answers", array(1 => array($aid1 => t("Jah"), $aid1+1 => t("Ei"), $aid1+2 => t("V&otilde;ibolla"))));
		$poll->save();

		// sitemap
		$sm = obj($vars["sm_o"]);
		$sm->set_prop("menus", array($parent->id() => $parent->id()));
		$sm->save();

		// layout
		$l = obj($vars["tbl_o"]);

		$ge = get_instance("vcl/grid_editor");
		$ge->_init_table($l->meta('grid'));
		$ge->set_num_rows(3);
		$ge->set_num_cols(3);
		$ge->set_row_style(0, $vars["css1"]);
		$ge->set_row_style(1, $vars["css2"]);
		$ge->set_row_style(2, $vars["css3"]);

		$ge->set_cell_content(0,0, t("Pealkiri"));
		$ge->set_cell_content(0,1, t("Pealkiri 2"));
		$ge->set_cell_content(0,2, t("Pealkiri 3"));

		$ge->set_cell_content(1,0, t("Sisu 1"));
		$ge->set_cell_content(1,1, t("Sisu 2"));
		$ge->set_cell_content(1,2, t("Sisu 3"));

		$ge->set_cell_content(2,0, t("Veel sisu 1"));
		$ge->set_cell_content(2,1, t("Veel sisu 2"));
		$ge->set_cell_content(2,2, t("Veel sisu 3"));

		$l->set_meta("grid", $ge->_get_table());
		$l->save();

		// webform
		$wf_o = obj($vars["wf_o"]);
		$wf_i = get_instance(CL_WEBFORM);

		$wf_i->callback_on_load(array(
			"id" => NULL
		));

		$wf_i->_on_init(array(
			"obj_inst" => $wf_o,
			"request" => array(
				"form_type" => CL_REGISTER_DATA,
				"parent" => $wf_o->id(),
				"name" => $wf_o->name(),
			)
		));

		// now, we need to add some props. belch.
		// get cfgform from wf
		$cff = $wf_o->get_first_obj_by_reltype("RELTYPE_CFGFORM");
		if ($cff)
		{
			$cf_i = $cff->instance();
			$cf_i->cff_add_prop($cff, "user1", array("caption" => t("Eesnimi"), "group" => "data", "ord" => 10));
			$cf_i->cff_add_prop($cff, "user2", array("caption" => t("Perekonnanimi"), "group" => "data", "ord" => 20));
			$cf_i->cff_add_prop($cff, "user4", array("caption" => t("E-posti aadress"), "group" => "data", "ord" => 30));
			$cf_i->cff_add_prop($cff, "userta1", array("caption" => t("Minu teade"), "group" => "data", "ord" => 40));
			$cf_i->cff_add_prop($cff, "usersubmit1", array("caption" => t("Saadan"), "group" => "data", "ord" => 50));
			$cff->save();
		}

		$wf_i->callback_on_load(array(
			"request" => array(
				"id" => $wf_o->id()
			)
		));
	}
}
?>
