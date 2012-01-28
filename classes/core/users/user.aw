<?php

/*

HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_DELETE, CL_USER, on_delete_user)
HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_ALIAS_DELETE_FROM, CL_USER, on_delete_alias)
HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_ALIAS_ADD_FROM, CL_USER, on_add_alias)
EMIT_MESSAGE(MSG_USER_CREATE);

*/

/*

@classinfo relationmgr=yes no_status=1 prop_cb=1

@groupinfo chpwd caption="Salas&otilde;na muutmine"
@groupinfo groups caption=Kasutajagrupid
@groupinfo settings caption=Seaded
@groupinfo settings_general caption=&Uuml;ldine parent=settings
@groupinfo settings_shortcuts caption=Shortcutid parent=settings

@groupinfo jdata caption="Liitumise info"
@groupinfo stat caption=Statistika
@groupinfo aclwizard caption="ACL Maag"

@tableinfo users index=oid master_table=objects master_index=brother_of

@default table=users
@default group=general
	@property uid field=uid type=text group=general editonly=1
	@caption Kasutajanimi

	@property link_to_p type=text store=no
	@caption Isik

	@property uid_entry store=no type=textbox group=general
	@caption Kasutajanimi

	@property logins field=logins type=text store=yes
	@caption Sisselogimisi

	@property lastaction field=lastaction type=text
	@caption Viimane sisselogimine

	@property created field=created type=text table=objects
	@caption Loodud

	@property createdby field=createdby type=text table=objects
	@caption Looja

	@property blocked field=blocked type=checkbox ch_value=1
	@caption Blokeeritud

	@property real_name type=textbox table=users field=config method=serialize
	@caption Nimi

	@property email field=email type=textbox
	@caption E-mail

	@property notify field=notify type=checkbox ch_value=1
	@caption Uuest mailist teavitamine

	@property warning_notification field=warning_notification type=textbox default=5
	@caption Hoiatuste kuvamise tase
	@comment Kasutajale kuvatavate hoiatuste tase. V&auml;iksem number t&auml;hendab v&auml;hemt&otilde;sist hoiatust

	@property comment type=textarea rows=5 cols=30 table=objects field=comment
	@caption Kommentaar

	@property password_hash type=hidden table=users field=config method=serialize
	@property password_hash_timestamp type=hidden table=users field=config method=serialize
	@property join_grp type=hidden table=users field=join_grp

	@property extern_id type=textbox size=10 field=aw_extern_id table=users
	@caption Siduss&uuml;steemi id

@default group=chpwd

	@property passwd type=password store=no
	@caption Salas&otilde;na

	@property passwd_again type=password store=no
	@caption Salas&otilde;na uuesti

	@property password type=hidden table=users field=password

	@property gen_pwd store=no type=text
	@caption Genereeri parool

	@property genpwd store=no type=textbox
	@caption Genereeritud parool

	@property resend_welcome store=no type=checkbox ch_value=1
	@caption Saada tervitusmeil

@default group=groups

	@property groups type=text  store=no no_caption=1

	@property home_folder type=hidden field=home_folder table=users

@default group=jdata

	@property jdata type=callback callback=callback_jdata  store=no no_caption=1
	@caption Liitumise andmed

@default group=stat

	@property stat type=text store=no no_caption=1
	@caption Statistika

@default group=aclwizard

	@property aclwizard_q type=text store=no
	@caption Millised on kasutaja

	@property aclwiz type=hidden table=objects field=meta method=serialize

	@property aclwizard_a type=text store=no
	@caption

@groupinfo userdef caption="User-defined"

	@property userch1 type=checkbox ch_value=1 table=objects field=meta method=serialize group=userdef user=1
	@caption User-defined checkbox 1

	@property userch2 type=checkbox ch_value=1 table=objects field=meta method=serialize group=userdef user=1
	@caption User-defined checkbox 2

	@property userch3 type=checkbox ch_value=1 table=objects field=meta method=serialize group=userdef user=1
	@caption User-defined checkbox 3

	@property userch4 type=checkbox ch_value=1 table=objects field=meta method=serialize group=userdef user=1
	@caption User-defined checkbox 4

	@property userch5 type=checkbox ch_value=1 table=objects field=meta method=serialize group=userdef user=1
	@caption User-defined checkbox 5

	@property join_form_entry type=hidden table=users field=join_form_entry

@default group=settings_general

	@property lg_hdh type=text subtitle=1 store=no
	@caption Ajaloo seaded

	@property history_size type=textbox table=objects field=meta method=serialize size=5
	@caption Mitu viimati k&uuml;lastatud objekti on ajaloos

	@property history_has_folders type=checkbox ch_value=1 table=objects field=meta method=serialize
	@caption Ajalugu on jagatud kataloogideks

	@property lg_hdl type=text subtitle=1 store=no
	@caption Keelte seaded

	@property ui_language type=select table=objects field=meta method=serialize
	@caption Liidese keel

	@property lg_hd type=text subtitle=1 store=no
	@caption T&otilde;lkekeskkond

	@property base_lang type=select field=meta method=serialize table=objects
	@caption Baaskeel

	@property target_lang type=select field=meta method=serialize table=objects multiple=1
	@caption Sihtkeel

	@property rd_hd type=text subtitle=1 store=no
	@caption Suunamine

	@property after_login_redir type=textbox field=meta method=serialize table=objects
	@caption P&auml;rast sisse logimist suunamine

	@property rte_hd type=text subtitle=1 store=no
	@caption Liidese seaded

	@property rte_disabled type=checkbox ch_value=1 field=meta method=serialize table=objects
	@caption Keela RTE

        @property object_tree_always_visible type=checkbox ch_value=1 field=meta method=serialize table=objects
        @caption Objektipuu alati n&auml;htaval

        @property object_tree_classes_inherit_from type=relpicker field=meta method=serialize table=objects reltype=RELTYPE_USER
        @caption P&auml;ri puu objektid kasutajalt

        @property object_tree_classes type=select field=meta method=serialize table=objects multiple=1
        @caption Objektipuu alati n&auml;htaval klassides

	@property nfy_hd type=text subtitle=1 store=no
	@caption Teavituste seaded

		@property nfy_meeting type=select field=meta method=serialize table=objects
		@caption Kohtumine

		@property nfy_task type=select field=meta method=serialize table=objects
		@caption Toimetus

	@property draft_hd type=text subtitle=1 store=no
	@caption Mustandi seaded

		@property draft_timeout type=textbox field=meta method=serialize table=objects
		@caption Kui tihti omaduste mustandeid salvestatakse? (aeg sekundites)

	@property cfg_admin_mode type=checkbox ch_value=1 field=meta method=serialize table=objects
	@caption Seadete administreerimise vaade

	@property stoppers type=hidden table=objects field=meta method=serialize no_caption=1

	@default group=settings_shortcuts
	property Shortcutid

		@layout settings_shortcuts_top type=hbox closeable=1 area_caption=&Uuml;ldine width=100%

			@layout settings_shortcuts_top_left type=vbox parent=settings_shortcuts_top

				@property settings_shortcuts_shortcut_sets type=chooser field=meta method=serialize table=objects parent=settings_shortcuts_top_left no_caption=1
				@caption Aktiivne set

	@layout settings_shortcuts_bot area_caption=Aktiivsed&nbsp;shortcutid closeable=1 type=hbox width=100%

		@layout settings_shortcuts_bot_left type=vbox parent=settings_shortcuts_bot

			@property settings_shortcuts_settings_shortcuts type=table parent=settings_shortcuts_bot_left no_caption=1
			@caption Shortcutid



//  ============ RELTYPES ===============
@reltype GRP value=1 clid=CL_GROUP
@caption Grupp

@reltype PERSON value=2 clid=CL_CRM_PERSON
@caption isik

@reltype EMAIL value=6 clid=CL_ML_MEMBER
@caption Email

@reltype FG_PROFILE value=7 clid=CL_FORM_ENTRY
@caption FG profiil

@reltype ACCESS_FROM_IP value=8 clid=CL_IPADDRESS
@caption ligip&auml;&auml;su aadress

@reltype JOIN_SITE value=9 clid=CL_JOIN_SITE
@caption liitumise andmed

@reltype USER value=10 clid=CL_USER
@caption Kasutaja

@reltype SHORTCUT_SET value=11 clid=CL_SHORTCUT_SET
@caption Kiirviidete&nbsp;kogu



*/

class user extends class_base
{
	private $_set_pwd = "";
	private $users;

	function user()
	{
		$this->init(array(
			'tpldir' => 'core/users/user',
			'clid' => CL_USER
		));
		$this->users = new users();
	}

	function get_property(&$arr)
	{
		$prop =& $arr["prop"];
		switch($prop["name"])
		{
			case "nfy_meeting":
			case "nfy_task":
				$prop["options"] = array(
					0 => t("&Auml;ra saada e-kirja"),
					2 => t("Saada e-kiri, kui mind lisatakse osalejaks"),
					1 => $prop["name"] == "nfy_meeting" ? t("Saada e-kiri, kui minu kohtumist muudetakse") : t("Saada e-kiri, kui minu toimetust muudetakse"),
				);
				break;

			case "link_to_p":
				if (!is_oid($arr["obj_inst"]->id()))
				{
					return class_base::PROP_IGNORE;
				}
				$p = $this->get_person_for_user($arr["obj_inst"]);
				$prop["value"] = html::obj_change_url($p);
				break;

			case "name":
				return class_base::PROP_IGNORE;

			case "lastaction";
				$prop["value"] = $this->db_fetch_field("SELECT lastaction FROM users WHERE uid = '".$arr["obj_inst"]->prop("uid")."'", "lastaction");
				$prop["value"] = $this->time2date($prop['value'],2);
				break;

			case "uid_entry":
				if (is_oid($arr["obj_inst"]->id()))
				{
					return class_base::PROP_IGNORE;
				}
				break;

			case "created":
				if (isset($prop['value']))
				{
					$prop['value'] = $this->time2date($prop['value'], 2);
				}
				break;

			case "base_lang":
			case "target_lang":
				$prop["options"] = languages::get_list();
				break;

			case "groups":
				$prop['value'] = $this->get_group_membership($arr["obj_inst"], $arr["obj_inst"]->id());
				break;

			case "gen_pwd":
				$prop["value"] =
					"
						<script language=\"javascript\">
						function gp()
						{
							pwd = new String(\"\");
							for (i = 0; i < 8; i++)
							{
								rv = Math.random()*(123-97);
								rn = parseInt(rv);
								rt = rn+97;
								pwd = pwd + String.fromCharCode(rt);
							}
							document.changeform.passwd.value = pwd;
							document.changeform.passwd_again.value = pwd;
							document.changeform.genpwd.value = pwd;
						}
						</script>
					".
					html::href(array(
					"url" => "#",
					"onClick" => "gp();",
					"caption" => t("Genereeri parool")
				));
				break;

			case "aclwizard_q":
				$mt = $arr["obj_inst"]->meta("aclwiz");
				$prop["value"] = "".html::textbox(array(
					"name" => "aclwizard[user]",
					"value" => $mt["user"],
					"size" => "15"
				))." &otilde;igused objektile ".html::textbox(array(
					"name" => "aclwizard[object]",
					"value" => $mt["object"],
					"size" => 8
				))."?";
				break;

			case "aclwizard_a":
				$mt = $arr["obj_inst"]->meta("aclwiz");
				if ($mt["user"] != "" && is_oid($mt["object"]))
				{
					$prop["value"] = $this->aclwizard_ponder(array(
						"user" => $mt["user"],
						"oid" => $mt["object"],
						"type" => $mt["type"]
					));
				}
				break;

			case "ui_language":
				$prop["options"] = html::get_empty_option() + aw_translations::lang_selection();
				break;

			case "history_size":
				$_SESSION["user_history_count"] = $arr["obj_inst"]->prop("history_size");
				break;

			case "history_has_folders":
				$_SESSION["user_history_has_folders"] = $arr["obj_inst"]->prop("history_has_folders");
				break;
		}

		return class_base::PROP_OK;
	}

	function set_property(&$arr)
	{
		$prop =& $arr["prop"];

		switch($prop['name'])
		{
			case "blocked":
				if ($arr["obj_inst"]->prop("blocked") == 1 && $prop["value"] == 0 && $this->can("view", $arr["obj_inst"]->prop("join_grp")))
				{
					$jo = obj($arr["obj_inst"]->prop("join_grp"));
					if (!$jo->prop("send_join_mail") && $jo->prop("users_blocked_by_default"))
					{
						$js = new join_site();
						$js->_do_send_join_mail(array(
							"obj" => $jo,
							"uid" => $arr["obj_inst"]->prop("uid"),
							"email" => $arr["obj_inst"]->prop("email"),
						));
					}
				}
				break;

			case "uid_entry":
				if (!is_oid($arr["obj_inst"]->id()))
				{
					if (strtolower($this->db_fetch_field("SELECT uid FROM users WHERE uid = '".$prop["value"]."'", "uid")) == strtolower($prop["value"]))
					{
						$prop["error"] = t("Selline kasutaja on juba olemas!");
						return class_base::PROP_FATAL_ERROR;
					}
					if (!is_valid("uid", $prop["value"]))
					{
						$prop["error"] = t("Selline kasutajanimi pole lubatud!");
						return class_base::PROP_FATAL_ERROR;
					}
				}
				break;

			case "passwd_again":
				if (!empty($prop['value']))
				{
					if ($prop['value'] !== $arr['request']['passwd'])
					{
						$prop["error"] = t("Paroolid pole samad!");
						return class_base::PROP_FATAL_ERROR;
					}
					elseif (!is_valid("password", $prop['value']))
					{
						$prop["error"] = t("Parool sisaldab lubamatuid t&auml;hem&auml;rke v&otilde;i on liiga l&uuml;hike!");
						return class_base::PROP_FATAL_ERROR;
					}
					else
					{
						// change pwd
						$arr["obj_inst"]->set_meta("password_change_time", time());
						$arr["obj_inst"]->set_password($prop["value"]);
						$this->_set_pwd = $prop["value"];
					}
				}
				break;

			case "resend_welcome":
				if ($prop['value'] == 1)
				{
					$this->users->send_welcome_mail(array(
						"u_uid" => $arr["obj_inst"]->id(),
						"pass" => $arr['request']['passwd']
					));
				}
				break;

			case "groups":
				$prop['value'] = $this->_set_group_membership($arr["obj_inst"], $arr["request"], $arr["obj_inst"]->id());
				break;

			case "aclwiz":
				if ($arr["request"]["aclwizard"]["user"] != "")
				{
					$ol = new object_list(array(
						"class_id" => user_obj::CLID,
						"name" => $arr["request"]["aclwizard"]["user"]
					));
					if ($ol->count() < 1)
					{
						$prop["error"] = t("Sellist kasutajat pole!");
						return class_base::PROP_FATAL_ERROR;
					}
				}
				$prop["value"] = $arr["request"]["aclwizard"];
				break;

			case "jdata":
				$o = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_JOIN_SITE");
				$tmp = $arr["request"];
				$tmp["id"] = $o->id();
				$ji = new join_site();
				$ji->submit_update_form($tmp, array(
					"uid" => $arr["obj_inst"]->prop("uid")
				));
				break;
		}
		return PROP_OK;
	}

	function _get_settings_shortcuts_shortcut_sets($arr)
	{
		$prop = & $arr["prop"];
		$ou = obj(aw_global_get("uid_oid")); // temp
		$p = get_current_person();

		$ol = new object_list(array(
			"class_id" => CL_SHORTCUT_SET,
		));

		$a_shortcut_sets = array();
		$i = 0;
		for ($o = $ol->begin(); !$ol->end(); $o =& $ol->next())
		{
			if ($prop["value"]=="" && $i==0)
			{
				$ou->set_prop("settings_shortcuts_shortcut_sets", $o->id());
				$ou->save();
			}

			$a_shortcut_sets[$o->id()] = html::href(array(
				"url" => $this->mk_my_orb("change", array(
					"id" => $o->id(),
					"return_url" => get_ru(),
				), CL_SHORTCUT_SET),
				"caption" => $o->prop("name"),
				));
			$i++;
		}

		$prop["options"] = $a_shortcut_sets;
	}

	function _get_settings_shortcuts_settings_shortcuts($arr)
	{
		$prop = & $arr["prop"];
		$t = $this->_start_shortcuts_table();
		$o = $arr["obj_inst"];

		if (!$o->prop("settings_shortcuts_shortcut_sets"))
		{
			return;
		}
		$o_shortcut_set = obj($o->prop("settings_shortcuts_shortcut_sets"));

		$conns = $o_shortcut_set->connections_from(array(
			"type" => "RELTYPE_SHORTCUT"
		));
		foreach($conns as $con)
		{
			$data = array();
			$o_shortcut = obj($con->prop("to"));
			$data["oid"] = $o_shortcut->id();
			$data["name"] = html::href(array(
				"url" => $this->mk_my_orb("change", array(
					"id" => $o_shortcut->id(),
					"return_url" => get_ru(),
				), CL_SHORTCUT),
				"caption" => $o_shortcut->prop("name"),
				));
			$o_shortcut->name();
			$data["keycombo"] = html::div(array(
						"id" => "aw_shortcut_" . $o_shortcut->id(),
						"class" => "aw_shorcut_object",
						"content" => $o_shortcut->prop("keycombo"),
					));
			$t->define_data($data);
		}

		$t->set_default_sortby("keycombo");
		$t->sort_by();

		$data = array();
		$data["name"] = html::textbox(array(
			"name" => "new_shortcut[name]",
			"class" => "new_shortcut_title",
			"style" => "width: 200px",
		));
		$data["keycombo"] = html::textbox(array(
			"name" => "new_shortcut[keycombo]",
			"class" => "keycombo",
			"style" => "width: 200px",
		));

		$t->define_data($data);

		$prop["value"] = $t->draw();
	}

	function _set_settings_shortcuts_settings_shortcuts($arr)
	{
		$o_user = $arr["obj_inst"];
		$shortcut_parent = $o_user->prop("settings_shortcuts_shortcut_sets");

		// delete shortcuts
		if (!empty($arr["request"]["delete"]))
		{
			foreach($arr["request"]["delete"] as $key => $var)
			{
				$o = obj($var);
				$o -> delete();
			}
		}

		// modify shortcuts
		if (!empty($arr["request"]["modify_shortcut"]))
		{
			foreach($arr["request"]["modify_shortcut"] as $key => $var)
			{
				$o = obj($key);
				$o -> set_prop("keycombo", $var);
				$o -> save();
			}
		}

		// add new shortcut
		if (!empty($arr["request"]["new_shortcut"]["name"]))
		{
			// get parent for shortcuts
			{
				$ol = new object_list(array(
					"class_id" => menu_obj::CLID,
					"parent" => $shortcut_parent,
					"name" => "shortcuts"
				));
				if (count($ol->list)===0)
				{
					$o = new object(array(
						"class_id" => menu_obj::CLID,
						"parent" => $shortcut_parent,
						"name" => "shortcuts"
					));
					$o -> save();
				}
				else
				{
					$o = $ol->begin();
				}
				$shortcuts_dir_id = $o -> id();
			}

			// save shortcut
			{
				$o2 = new object();
				$o2 -> set_parent($shortcuts_dir_id);
				$o2 -> set_name($arr["request"]["new_shortcut"]["name"]);
				$o2 -> set_class_id(CL_SHORTCUT);
				$o2 -> set_prop("keycombo", $arr["request"]["new_shortcut"]["keycombo"]);
				$o2 -> save();
			}

			// make relations
			{
				$shortcut_set_oid = $arr["obj_inst"]->prop("settings_shortcuts_shortcut_sets");
				$o3 = obj($shortcut_set_oid);
				$o3->connect(array(
					"to" => $o2,
					"reltype" => "RELTYPE_SHORTCUT",
				));
			}
		}
	}

	function _start_shortcuts_table()
	{
		$t = new aw_table(array("layout" => "generic","prefix" => "uglist"));

		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"sortable" => 1,
		));

		$t->define_field(array(
			"name" => "keycombo",
			"caption" => t("Shortcut"),
			"sortable" => 1,
		));

		$t->define_chooser(array(
			"name" => "delete",
			"caption" => t("Kustuta"),
			"field" => "oid"
		));

		return $t;
	}

	private function get_group_membership($o, $id)
	{
		$group = new group();
		$gl = $group->get_group_picker(array("type" => array(aw_groups::TYPE_REGULAR, aw_groups::TYPE_DYNAMIC)));

		// get all groups this user is member of
		$groups = $o->get_groups_for_user();

		$t = $this->_start_gm_table();
		foreach($gl as $g_oid => $g_name)
		{
			$go = obj($g_oid);
			$gd = array();
			$gd["name"] = $go->name();
			$gd["priority"] = $go->prop("priority");
			$gd["gcount"] = $go->get_member_count();
			$gd["modifiedby"] = $go->modifiedby();
			$gd["modified"] = $go->modified();

			$can_edit = true;
			if (!$this->can("edit", $g_oid))
			{
				$can_edit = false;
			}

			if ($go->prop("type") == aw_groups::TYPE_DYNAMIC)
			{
				$gd["type"] = t("D&uuml;naamiline");
				$gd["is_member"] = (isset($groups[$g_oid]) ? t("Jah") : t("Ei"));
			}
			else
			{
				$gd["type"] = t("Tavaline");
				if (!$can_edit)
				{
					$gd["is_member"] = isset($groups[$g_oid]) ? t("Jah") : t("Ei");
				}
				else
				{
					$gd["is_member"] = html::checkbox(array(
						"name" => "member[$g_oid]",
						"value" => 1,
						"checked" => isset($groups[$g_oid])
					));
				}
			}

			$t->define_data($gd);
		}

		$t->set_default_sortby("name");
		$t->sort_by();
		return $t->draw();
	}

	function _set_group_membership($o, $form_data, $id)
	{
		$member = $form_data["member"];

		// now update group membership.
		// get the groups that the user is member of
		$groups = $o->get_groups_for_user();

		// get a list of all groups, so we can throw out the dynamic groups
		$group = new group();
		$gl = $group->get_group_picker(array("type" => array(aw_groups::TYPE_REGULAR, aw_groups::TYPE_DYNAMIC)));


		// now, go over both lists and get rid of the dyn groups
		$_member = array();
		$_tm = new aw_array($member);
		foreach($_tm->get() as $g_oid => $is)
		{
			$go = obj($g_oid);
			if ($go->prop("type") != aw_groups::TYPE_DYNAMIC)
			{
				$_member[$g_oid] = $is;
			}
		}
		$member = $_member;

		$_groups = array();
		foreach($groups as $g_oid => $go)
		{
			if ($go->prop("type") != aw_groups::TYPE_DYNAMIC)
			{
				$_groups[$g_oid] = true;
			}
		}
		$groups = $_groups;

		// now, remove user from all removed groups
		foreach($groups as $g_oid => $is)
		{
			if (!$this->can("edit", $g_oid))
			{
				continue;
			}
			$group = obj($g_oid);
			if ((!isset($member[$g_oid]) || $member[$g_oid] != 1) && $is && isset($gl[$g_oid]))
			{
				group::remove_user_from_group($o, $group);
			}
		}

		// now, add to all groups
		foreach($member as $g_oid => $is)
		{
			if ($is && empty($groups[$g_oid]))
			{
				$group = obj($g_oid);
				$user = $o;

				// get groups
				$grps = $group->path();
				foreach($grps as $p_o)
				{
					if ($p_o->class_id() == CL_GROUP)
					{
						$user->connect(array(
							"to" => $p_o->id(),
							"reltype" => "RELTYPE_GRP",
						));

						// add reverse alias to group
						$p_o->connect(array(
							"to" => $user->id(),
							"reltype" => "RELTYPE_MEMBER" // from group
						));

						$user->create_brother($p_o->id());
					}
				}
			}
		}

		cache::file_clear_pt("acl");
	}

	function _start_gm_table()
	{
		$t = new aw_table(array("layout" => "generic", "prefix" => "uglist"));

		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"sortable" => 1
		));

		$t->define_field(array(
			"name" => "is_member",
			"caption" => t("Liige?"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "priority",
			"caption" => t("Prioriteet"),
			"sortable" => 1,
			"numeric" => 1,
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "gcount",
			"caption" => t("Mitu liiget"),
			"sortable" => 1,
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "modifiedby",
			"caption" => t("Muutja"),
			"sortable" => 1,
			"align" => "center"
		));

		$df = aw_ini_get("config.dateformats");
		$t->define_field(array(
			"name" => "modified",
			"caption" => t("Muudetud"),
			"sortable" => 1,
			"type" => "time",
			"format" => $df[2]
		));

		$t->define_field(array(
			"name" => "type",
			"caption" => t("T&uuml;&uuml;p"),
			"sortable" => 1,
			"align" => "center"
		));

		return $t;
	}

	////
	// !this will get automatically called if an object of this type is cut-pasted
	// params:
	//	oid
	//	new_parent
	// damn - must figure out a way to do this via storage messages... must remember what is changed for object or somesuch..
	function cut_hook($arr)
	{
		extract($arr);

		// check if it is original or brother
		// if original, do nothing
		// if brother, find new group and change group membership

		$obj = obj($oid);
		if ($new_parent == $obj->parent())
		{
			return;
		}

		if ($obj->is_brother())
		{
			$grp_o = obj($obj->parent());
			if ($grp_o->class_id() == CL_GROUP)
			{
				// get the parent obj for the user's brother
				// and remove the user from that group
				$real_user = $obj->get_original();


				// sync manually here.
				// remove alias from user to group
				if ($real_user->is_connected_to(array("to" => $grp_o->id())))
				{
					$real_user->disconnect(array(
						"from" => $grp_o->id()
					));
				}

				// remove alias from group to user
				if ($grp_o->is_connected_to(array("to" => $real_user->id())))
				{
					$grp_o->disconnect(array(
						"from" => $real_user->id()
					));
				}

				// go over all the groups below this one and remove all aliases to this user
				// and also all user brothers
				$ot = new object_tree(array(
					"parent" => $grp_o->id(),
					"class_id" => CL_GROUP
				));

				$ol = $ot->to_list();
				for($grp_o = $ol->begin(); !$ol->end(); $grp_o = $ol->next())
				{
					// get all connections from the group to the user object
					foreach($grp_o->connections_from(array("to" => $real_user->id())) as $c)
					{
						$c->delete();
					}

					// disconnect user from group as well
					if ($real_user->is_connected_to(array("to" => $grp_o->id())))
					{
						$real_user->disconnect(array(
							"from" => $grp_o->id()
						));
					}

					// get all objects below that point to the current user
					$inside_ol = new object_list(array(
						"parent" => $grp_o->id(),
						"class_id" => CL_USER,
						"brother_of" => $real_user->id()
					));
					$inside_ol->delete();
				}
			}

			$group = obj($new_parent);
			if ($group->class_id() == CL_GROUP)
			{
				// get groups
				$user = $obj->get_original();

				$grps = $group->path();
				foreach($grps as $p_o)
				{
					if ($p_o->class_id() == CL_GROUP)
					{
						$user->connect(array(
							"to" => $p_o->id(),
							"reltype" => "RELTYPE_GRP"
						));

						// add reverse alias to group
						$p_o->connect(array(
							"to" => $user->id(),
							"reltype" => "RELTYPE_MEMBER" // from group
						));

						if ($p_o->id() != $group->id())
						{
							$user->create_brother($p_o->id());
						}
					}
				}
			}
		}
	}

	// must not be deleting these, most important it is!
	function _serialize($arr = array())
	{
		$ob = obj($arr["oid"]);
		if (is_object($ob))
		{
			return aw_serialize($ob->fetch(), SERIALIZE_NATIVE);
		}
		return false;
	}

	function _unserialize($arr = array())
	{
		extract($arr);
		$row = aw_unserialize($str);
		$row['parent'] = $parent;

		$old_oid = $row["oid"];
		$u = obj($old_oid);

		// get the parent group
		$n = obj($parent);
		$path = $n->path();
		foreach(array_reverse($path) as $p_i)
		{
			if ($p_i->class_id() == CL_GROUP)
			{
				$g = get_instance(CL_GROUP);
				$g->add_user_to_group($u, $p_i);
				return 0;
			}
		}
		return -1;
	}

	function callback_on_load($arr)
	{
		if ($arr["request"]["action"] === "new")
		{
			$po = obj($arr["request"]["parent"]);
			$rm = aw_ini_get("users.root_folder");

			if ($po->class_id() != CL_GROUP && $po->id() != $rm)
			{
				// redirect to main user folder
				header("Location: ".aw_url_change_var("parent", $rm));
				die();
			}
		}
		else
		{
			$o = obj($arr["request"]["id"]);
			if ($o->id() != $o->brother_of())
			{
				header("Location: ".aw_url_change_var("id", $o->brother_of()));
				die();
			}
		}
	}

	function callback_mod_retval(&$arr)
	{
		if (!empty($arr["request"]["edit_acl"]))
		{
			$arr["args"]["edit_acl"] = $arr["request"]["edit_acl"];
		}
	}

	function callback_generate_scripts($arr)
	{
		$script = "";
		if ($this->use_group === "settings_shortcuts")
		{
			$script = <<<EOF
			function aw_handle_edit_shortcut()
			{
				$(".aw_shorcut_object").click(function(){
					// fix column jump
					$(this).parent().css("width", $(this).parent().width()+"px");
					oid = this.id.replace("aw_shortcut_","");
					$(this).replaceWith("<input id='"+this.id+"' name='modify_shortcut["+oid+"]' class='keycombo' style='width: 200px' value='"+$(this).html()+"' />");
					$("#"+this.id).focus();
					$(".keycombo").catch_keycombo();
				});
			}

			function aw_shortcut_manager_init()
			{
				aw_handle_edit_shortcut();
				$.hotkeys.remove("*");  // reset current shortcuts so they don't activate if pressed
				$.getScript("http://hannes.dev.struktuur.ee/automatweb/js/jquery/plugins/jquery_catch_keycombo.js", function(){
					$(".keycombo").catch_keycombo();
				});
			}

			aw_shortcut_manager_init();
EOF;
		}
		return $script;
	}

	function on_delete_user_bro($arr)
	{
		extract($arr);

		// check if we are deleting the real thing
		$o = obj($oid);
		// get the parent obj for the user's brother
		// and remove the user from that group
		$o = obj($oid);
		$real_user = $o->get_original();

		if (!$this->can("view", $o->parent()))
		{
			return;
		}

		aw_global_set("__from_raise_error", 1);
		$grp_o = obj($o->parent());
		if (!empty($GLOBALS["aw_is_error"]))
		{
			aw_global_set("__from_raise_error", 0);
			$GLOBALS["aw_is_error"] = 0;
			return;
		}

		if ($grp_o->class_id() == CL_GROUP)
		{
			// sync manually here.
			// Why manually??? -kaarel
			/*
			// remove alias from user to group
			if ($real_user->is_connected_to(array("to" => $grp_o->id())))
			{
				$real_user->disconnect(array(
					"from" => $grp_o->id()
				));
			}

			// remove alias from group to user
			if ($grp_o->is_connected_to(array("to" => $real_user->id())))
			{
				$grp_o->disconnect(array(
					"from" => $real_user->id()
				));
			}

			// go over all the groups below this one and remove all aliases to this user
			// and also all user brothers
			$ot = new object_tree(array(
				"parent" => $o->parent(),
				"class_id" => CL_GROUP
			));

			$ol = $ot->to_list();
			for($grp_o = $ol->begin(); !$ol->end(); $grp_o = $ol->next())
			{
				// get all connections from the group to the user object
				foreach($grp_o->connections_from(array("to" => $real_user->id())) as $c)
				{
					$c->delete();
				}

				// disconnect user from group as well
				if ($real_user->is_connected_to(array("to" => $grp_o->id())))
				{
					$real_user->disconnect(array(
						"from" => $grp_o->id()
					));
				}

				// get all objects below that point to the current user
				$inside_ol = new object_list(array(
					"parent" => $grp_o->id(),
					"class_id" => CL_USER,
					"brother_of" => $real_user->id()
				));
				$inside_ol->delete();
			}
			*/
			get_instance(CL_GROUP)->remove_user_from_group($o, $grp_o);
		}

		cache::file_clear_pt("acl");
	}

	function on_delete_alias($arr)
	{
		// now, if the alias deleted was a group alias, then
		// remove the user from that goup and do all the other movements
		if ($arr["connection"]->prop("reltype") == 1) //1 - RELTYPE_GRP
		{
			$user = $arr["connection"]->from();
			$group = $arr["connection"]->to();

			// now, delete the user from the group
			if ($group->is_connected_to(array("to" => $user->id())))
			{
				$group->disconnect(array(
					"from" => $user->id()
				));
			}

			// delete user bros
			$ol = new object_list(array(
				"parent" => $group->id(),
				"brother_of" => $user->id()
			));
			$ol->delete();

			// get all groups below the removed group
			$ot = new object_tree(array(
				"parent" => $group->id(),
				"class_id" => CL_GROUP
			));
			$ol = $ot->to_list();
			for($item = $ol->begin(); !$ol->end(); $item = $ol->next())
			{
				// remove all brothers from those groups
				$user_brothers = new object_list(array(
					"parent" => $item->id(),
					"brother_of" => $user->id()
				));
				$user_brothers->delete();

				// remove all aliases from those groups to this user
				if ($item->is_connected_to(array("to" => $user->id())))
				{
					$item->disconnect(array(
						"from" => $user->id()
					));
				}

				// also remove all aliases from user to the group
				if (count($user->connections_from(array("to" => $item->id()))) > 0)
				{
					$user->disconnect(array(
						"from" => $item->id()
					));
				}
			}
		}

		cache::file_clear_pt("acl");
	}

	function _get_stat(&$arr)
	{
		$uid = $arr["obj_inst"]->prop("uid");
		$t = $this->_init_stat_table();
		//XXX: syslog is disabled. waiting for new implementation
		// $ts = aw_ini_get('syslog.types');
		// $as = aw_ini_get('syslog.actions');
		$q = "SELECT * FROM syslog WHERE uid = '$uid' ORDER BY tm DESC LIMIT 4000";
		$this->db_query($q);
		while ($row = $this->db_next())
		{
			// $row['type'] = $ts[$row['type']]['name'];
			// $row['act_id'] = $as[$row['act_id']]['name'];
			list($row['ip'],) = inet::gethostbyaddr($row['ip']);
			$t->define_data($row);
		}
		$t->set_default_sortby('tm');
		$t->set_default_sorder('DESC');
		$t->sort_by();
		$arr["prop"]["value"] = $t->draw(array(
			"has_pages" => true,
			"records_per_page" => 200,
			"pageselector" => "text"
		));
	}

	function _init_stat_table()
	{
		$t = new aw_table(array(
			'prefix' => 'user',
			'layout' => 'generic'
		));

		$df = aw_ini_get('config.dateformats');
		$t->define_field(array(
			'name' => 'rec',
			'caption' => t('Nr'),
		));
		$t->define_field(array(
			'name' => 'tm',
			'caption' => t('Millal'),
			'sortable' => 1,
			'numeric' => 1,
			'type' => 'time',
			'format' => $df[2],
			'nowrap' => 1
		));
		$t->define_field(array(
			'name' => 'uid',
			'caption' => t('Kes'),
			'sortable' => 1,
		));
		$t->define_field(array(
			'name' => 'ip',
			'caption' => t('IP'),
			'sortable' => 1,
		));
		$t->define_field(array(
			'name' => 'type',
			'caption' => t('T&uuml;&uuml;p'),
			'sortable' => 1,
		));
		$t->define_field(array(
			'name' => 'act_id',
			'caption' => t('Tegevus'),
			'sortable' => 1,
		));
		if (aw_ini_get("syslog.has_site_id"))
		{
			$t->define_field(array(
				'name' => 'site_id',
				'caption' => t('Saidi ID'),
				'sortable' => 1,
			));
		}
		$t->define_field(array(
			'name' => 'oid',
			'caption' => t('OID'),
			'sortable' => 1,
		));
		$t->define_field(array(
			'name' => 'action',
			'caption' => t('Mida'),
			'sortable' => 1,
		));
		return $t;
	}

	function on_add_alias($arr)
	{
		if ($arr["connection"]->prop("reltype") == 1) //RELTYPE_GRP
		{
			// it was a group alias, add the user to the group and all below it
			$user = $arr["connection"]->from();
			$group = $arr["connection"]->to();

			// get groups
			$grps = $group->path();
			foreach($grps as $p_o)
			{
				if ($p_o->class_id() == group_obj::CLID)
				{
					$user->connect(array(
						"to" => $p_o->id(),
						"reltype" => "RELTYPE_GRP",
					));

					// add reverse alias to group
					$p_o->connect(array(
						"to" => $user->id(),
						"reltype" => "RELTYPE_MEMBER" // from group
					));

					if ("root" !== $user->prop("uid"))
					{
						$user->create_brother($p_o->id());
					}
				}
			}

			cache::file_clear_pt("acl");
		}
		elseif ($arr["connection"]->prop("reltype") == 7)// FG_PROFILE
		{
			// set join form entry
			$u = $arr["connection"]->from();
			$jfe = safe_array(aw_unserialize($u->prop("join_form_entry")));
			$f = get_instance(CL_FORM);
			$eid = $arr["connection"]->prop("to");
			$fid = $f->get_form_for_entry($eid);

			$jfe[$fid] = $eid;
			$u->set_prop("join_form_entry", aw_serialize($jfe, SERIALIZE_NATIVE));
			$u->save();
		}
	}

	function callback_pre_save($arr)
	{
		if ($arr["new"])
		{
			$arr["obj_inst"]->set_prop("uid", $arr["request"]["uid_entry"]);
		}

		$arr["obj_inst"]->set_name($arr["obj_inst"]->prop("uid"));
	}

	function callback_post_save($arr)
	{
		$group_inst = new group();
		$go_to = false;
		if ($arr["new"])
		{
			$arr["obj_inst"]->set_password($this->_set_pwd ? $this->_set_pwd : generate_password());
			$arr["obj_inst"]->save();

			// add user to all users grp if we are not under that
			$aug_oid = user::get_all_users_group();
			if (is_oid($aug_oid) && $aug_oid != $arr["obj_inst"]->parent())
			{
				$aug_o = obj($aug_oid);
				$group_inst->add_user_to_group($arr["obj_inst"], $aug_o);
			}

			$params = array(
				"user_oid" => $arr["obj_inst"]->id(),
			);

			post_message_with_param(
				MSG_USER_CREATE,
				$this->clid,
				$params
			);

			// now, we also must check if the user was added under a group
			$parent = obj($arr["obj_inst"]->parent());
			if ($parent->class_id() == CL_GROUP)
			{
				// we have to move the object to a new loacation
				$rm = aw_ini_get("users.root_folder");
				$arr["obj_inst"]->set_parent($rm);
				$arr["obj_inst"]->save();

				// and do the add to group thing
				$user = $arr["obj_inst"];
				$group_inst->add_user_to_group($user, $parent);

				// get groups
				$grps = $parent->path();
				foreach($grps as $p_o)
				{
					if ($p_o->class_id() == CL_GROUP)
					{
						$group_inst->add_user_to_group($user, $p_o);

						if ($p_o->id() == $parent->id())
						{
							$go_to = $last_bro;
						}
					}
				}
			}

			if ($this->_set_pwd)
			{
				$arr["obj_inst"]->set_meta("password_change_time", time());
				$arr["obj_inst"]->set_password($this->_set_pwd);
			}
		}

		// now, find the correct brother
		if ($go_to)
		{
			header("Location: ".$this->mk_my_orb("change", array("id" => $go_to), "user"));
			die();
		}

		// update connected email object
		if (
			is_email($arr["obj_inst"]->prop("email"))
			and $ml = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_EMAIL")
		)
		{
			if ($ml->prop("mail") !== $arr["obj_inst"]->prop("email"))
			{
				$ml->set_prop("mail", $arr["obj_inst"]->prop("email"));
				$ml->set_name($arr["obj_inst"]->prop("email"));
				$ml->save();
			}
		}
	}

	/** returns the user object for the given uid

		@attrib api=1
	**/
	function get_obj_for_uid($uid)
	{
		$oid = $this->users->get_oid_for_uid($uid);
		if (is_oid($oid) && $this->can("view", $oid))
		{
			return obj($oid);
		}
		return NULL;
	}

	/**
		@attrib api=1
		@returns
		Current user object id
	**/
	public static function get_current_user()
	{
		return aw_global_get("uid_oid");
	}

	/** returns the oid of the CL_CRM_PERSON object that's attached to the current user
		@attrib api=1
		@comment
		Returns current person
		@returns
		Current persons oid
	**/
	public static function get_current_person()
	{
		if (aw_global_get("uid_oid") == "")
		{
			return false;
		}
		static $retval;
		if (!$retval)
		{
			$u = obj(aw_global_get("uid_oid"));
			$i = new user();
			$retval = $i->get_person_for_user($u);
		}
		return $retval;
	}

	/**
		@attrib params=pos api=1
		@param u required type=int
		User id
		@comment
		Gets person attached to the given user.
		@returns
		Persons oid
	**/
	function get_person_for_uid($uid)
	{
		static $cache;
		if (!is_array($cache))
		{
			$cache = array();
		}

		if (isset($cache[$uid]))
		{
			return $cache[$uid];
		}

		$oid = $this->users->get_oid_for_uid($uid);
		if (!$oid || !acl_base::can("", $oid))
		{
			$cache[$uid] = obj();
			return obj();
		}

		$tmp = $this->get_person_for_user(obj($oid));
		if (!acl_base::can("", $tmp))
		{
			$cache[$uid] = obj();
			return obj();
		}

		$rv = obj($this->get_person_for_user(obj($oid)));
		$cache[$uid] = $rv;
		return $rv;
	}

	/**
		@attrib params=pos api=1
		@param user required type=cl_user
		User object
		@returns
		Person object id
	**/
	public function get_person_for_user(object $u)
	{
		$person_c = $u->connections_from(array(
			"type" => "RELTYPE_PERSON",
		));
		$person_c = reset($person_c);
		if (!$person_c)
		{
			// create new person next to user
			$p = obj();
			$p->set_class_id(crm_person_obj::CLID);
			$p->set_parent($u->id());

			$rn = $u->prop("real_name");

			$uid = $u->prop("uid");

			$p_n = ($rn != "" ? $rn : $uid);
			$p->set_name($p_n);

			if ($rn != "")
			{
				$name_data = explode(" ", $rn);
				$fn = isset($name_data[0]) ? $name_data[0] : "";
				$ln = isset($name_data[1]) ? $name_data[1] : "";
			}
			else
			{
				$name_data = explode(".", $uid);
				$fn = isset($name_data[0]) ? $name_data[0] : "";
				$ln = isset($name_data[1]) ? $name_data[1] : "";
			}

			$p->set_prop("firstname", $fn);
			$p->set_prop("lastname", $ln);
			$p->save();

			if ($uid === aw_global_get("uid"))
			{
				// set acl to the given user
				$p->acl_set(
					obj($u->get_default_group()),
					array("can_edit" => 1, "can_add" => 1, "can_view" => 1, "can_delete" => 1)
				);
			}

			// now, connect user to person
			$u->connect(array(
				"to" => $p->id(),
				"reltype" => 2
			));
			return $p->id();
		}
		else
		{
			if (aw_global_get("uid") == $u->prop("uid") && !$this->can("edit", $person_c->prop("to")))
			{
				$p = obj($person_c->prop("to"));
				$p->acl_set(
					obj($u->get_default_group()),
					array("can_edit" => 1, "can_add" => 1, "can_view" => 1, "can_delete" => 1)
				);
			}
			return $person_c->prop("to");
		}
	}

	/**
		@attrib params=pos api=1
		@param person required type=oid
		Person id
		@comment
		Gets the company attached to the person
		@returns
		The company id
	**/
	public static function get_company_for_person($person)
	{
		$p_o = obj($person);
		if ($co = $p_o->company_id())
		{
			return $co;
		}

		$org_c = $p_o->company_id();
		if (!$org_c)
		{
			$uo = obj(aw_global_get("uid_oid"));
			// create new person next to user
			$p = obj();
			$p->set_class_id(CL_CRM_COMPANY);
			$p->set_parent($p_o->parent());
			$p->set_name("CO ".$uo->prop("real_name")." ".aw_global_get("uid"));
			$p->save();
			$p_o->save();
			// now, connect user to person
			$p_o->add_work_relation(array("org" => $p->id()));
			return $p->id();
		}

		return $org_c->id();
	}

	/** returns the CL_CRM_COMPANY that is connected to the current logged in user
		@attrib api=1
		@comment
		Gets the company attached to the current user
		@returns
		The company id
	**/
	public static function get_current_company()
	{
		static $retval;
		if ($retval === null)
		{
			try
			{
				$person_oid = new aw_oid(self::get_current_person());
				$person = obj($person_oid, array(), CL_CRM_PERSON);
				$retval = $person->company_id();
			}
			catch (awex_oid $e)
			{
				$retval = false;
			}
		}
		return $retval;
	}

	/** creates a new user object and returns the object
		@attrib params=name api=1
		@param uid required type=int
		User id
		@param email optional type=string
		Users email
		@param password optional type=string
		Users password
		@param real_name optional type=string
		Users name
		@param person optional type=oid
		The OID of person object.
		@param parent optional type=oid default="users.root_folder"
			Parent for user object
		@comment
		Creates new user object
		@returns
		New users object
	**/
	public function add_user($arr)
	{
		extract($arr);
		error::raise_if(empty($uid), array(
			"id" => "ERR_NO_UID",
			"msg" => sprintf(t("users::add_user(%s): no uid specified"), $arr)
		));

		if (empty($password))
		{
			$password = generate_password();
		}

		$o = obj();
		$o->set_name($uid);
		$o->set_class_id(CL_USER);
		$pt = !empty($parent) ? $parent : aw_ini_get("users.root_folder");
		$o->set_parent($pt);
		$o->set_prop("uid", $uid);
		$o->set_password($password);

		if (!empty($arr["email"]))
		{
			$o->set_prop("email", $arr["email"]);
		}

		if (!empty($arr["real_name"]))
		{
			$o->set_prop("real_name", $arr["real_name"]);
		}

		if (!empty($arr["join_grp"]))
		{
			$o->set_prop("join_grp", $arr["join_grp"]);
		}

		$o->set_prop("home_folder", $this->users->hfid);
		$o->set_password($password);
		if(isset($person) && $this->can("view", $person))
		{
			$o->set_meta("person", $person);
		}
		$o->save();

		// add user to all users grp if we are not under that
		if (!$aug_oid)
		{
			$aug_oid = user::get_all_users_group();
		}
		if ($aug_oid != $o->parent())
		{
			$aug_o = obj($aug_oid);
			$o->connect(array(
				"to" => $aug_o->id(),
				"reltype" => "RELTYPE_GRP" // from user
			));

			// add reverse alias to group
			$aug_o->connect(array(
				"to" => $o->id(),
				"reltype" => "RELTYPE_MEMBER" // from group
			));
		}

		return $o;
	}

	function aclwizard_ponder($arr)
	{
		extract($arr);
		// user, oid
		$tmp = $this->db_fetch_field("SELECT brother_of FROM objects WHERE oid = '{$oid}'", "brother_of");
		if ($tmp != "" && $tmp != $oid)
		{
			$str = sprintf(t("Objekt %s on vend, &otilde;igusi loetakse objekti %s kaudu.<br>"), $oid, $tmp);
			$oid = $tmp;
		}

		// check if the object is deleted or under a deleted object
		list($isd, $dat) = $this->_aclw_is_del($oid);
		if ("del" === $isd)
		{
			return $str.t("Objekt on kustutatud. Pole &otilde;igusi!");
		}
		elseif ("not" === $isd)
		{
			return $str.t("Objekti pole ega pole kunagi olnud! Pole &otilde;igusi!");
		}
		elseif ("delp" === $isd)
		{
			return $str.sprintf(t("Objekti &uuml;lemobjekt (%s) on kustutatud. Pole &otilde;igusi!"), $dat);
		}

		// find the controlling acl - select all gids that user belongs to
		// order by priority desc
		// go over objects in path
		// if acl is set, match is there.

		if (aw_ini_get("acl.use_new_acl"))
		{
			$acl = array();
			$acl["add"] = $this->can("add", $oid);
			$acl["view"] = $this->can("view", $oid);
			$acl["edit"] = $this->can("edit", $oid);
			$acl["delete"] = $this->can("delete", $oid);
			$acl["admin"] = $this->can("admin", $oid);
 			return $str.sprintf(t("<br>M&auml;&auml;ratud &otilde;igused on j&auml;rgnevad:<br>
				%s"), $this->_new_acl_string($acl));
		}

		$ca = $this->_aclw_get_controlling_acl($user, $oid);
		if ($ca === false)
		{
			return $str.t("Objektile pole sellele kasutaja gruppidele &otilde;igusi m&auml;&auml;ratud, kehtib default.<br>N&auml;gemis&otilde;igus ainult.");
		}

		$o_str = "";
		if ($this->can("view", $ca["oid"]))
		{
			$o = obj($ca["oid"]);
			$o_str = html::href(array(
				"url" => $this->mk_my_orb("change", array("id" => $o->id()), $o->class_id()),
				"caption" => $o->path_str()
			));
		}
		else
		{
			$o_str = $this->db_fetch_field("select name from objects where oid = '$ca[oid]'", "name")." (oid = $ca[oid])";
		}

		if ($this->can("view", $oid))
		{
			$ro = obj($oid);
			$ro_str = html::href(array(
				"url" => $this->mk_my_orb("change", array("id" => $ro->id()), $ro->class_id()),
				"caption" => $ro->path_str()
			));
		}
		else
		{
			$ro_str = $this->db_fetch_field("select name from objects where oid = '$oid'", "name")." (oid = $oid)";
		}

		$g_o = obj($this->db_fetch_field("SELECT oid FROM groups WHERE gid = '".$ca["gid"]."'", "oid"));

		$grpstr = html::href(array(
			"url" => $this->mk_my_orb("change", array("id" => $g_o->id()), $g_o->class_id()),
			"caption" => $g_o->path_str()
		));
		return $str.sprintf(t("Info objekti %s &otilde;iguste kohta: <br><br> &Otilde;igusi m&auml;&auml;rab &otilde;igus-seos objekti %s ja grupi %s vahel.<br><br>Sellele seosele m&auml;&auml;ratud &otilde;igused on j&auml;rgnevad:<br>%s"), $ro_str, $o_str, $grpstr, $this->_aclw_acl_string($ca["acl"]));
	}

	function _aclw_is_del($oid)
	{
		if (!$this->db_fetch_field("SELECT oid FROM objects WHERE oid = '$oid'", "oid"))
		{
			return array("not");
		}

		$parent = $oid;
		while ($parent)
		{
			$dat = $this->db_fetch_row("SELECT parent,status FROM objects WHERE oid = '$parent'");
			if ($dat["status"] == STAT_DELETED)
			{
				if ($parent == $oid)
				{
					return array("del");
				}
				else
				{
					return array("delp", $parent);
				}
			}
			$parent = $dat["parent"];
		}

		return array("ok");
	}

	function _aclw_get_controlling_acl($user, $oid)
	{
		if ($user == "")
		{
			$nlg = $this->get_cval("non_logged_in_users_group");
			$this->db_query("
				SELECT
					groups.gid as gid,
					groups.priority as pri
				FROM
					groupmembers
					LEFT JOIN groups ON groupmembers.gid = groups.gid
				WHERE
					groups.gid = '$nlg'
				ORDER BY groups.priority DESC
			");
			while ($row = $this->db_next())
			{
				$this->save_handle();
				$parent = $oid;
				while ($parent)
				{
					$adat = $this->db_fetch_row("SELECT * FROM acl WHERE oid = '$parent' AND gid = '$row[gid]'");
					if (is_array($adat))
					{
						return $adat;
					}

					$parent = $this->db_fetch_field("SELECT parent FROM objects WHERE oid = '$parent'", "parent");
				}
				$this->restore_handle();
			}
		}
		else
		{
			$this->db_query("
				SELECT
					groups.gid as gid,
					groups.priority as pri
				FROM
					groupmembers
					LEFT JOIN groups ON groupmembers.gid = groups.gid
				WHERE
					groupmembers.uid = '$user'
				ORDER BY groups.priority DESC
			");
			while ($row = $this->db_next())
			{
				$this->save_handle();
				$parent = $oid;
				while ($parent)
				{
					$adat = $this->db_fetch_row("SELECT * FROM acl WHERE oid = '$parent' AND gid = '$row[gid]'");
					if (is_array($adat))
					{
						return $adat;
					}

					$parent = $this->db_fetch_field("SELECT parent FROM objects WHERE oid = '$parent'", "parent");
				}
				$this->restore_handle();
			}
		}

		return false;
	}

	function _aclw_acl_string($int)
	{
		$ids = aw_ini_get("acl.ids");
		$names = aw_ini_get("acl.names");

		$str = array();
		foreach($ids as $bp => $name)
		{
			$cn = $int & (1 << $bp);
			$str[] = $names[$name]." => ".($cn ? t("Jah") : t("Ei"));
		}

		return implode("<br />", $str);
	}

	private function _new_acl_string($acl)
	{
		$names = aw_ini_get("acl.names");

		$str = array();
		foreach($names as $key => $name)
		{
			$str[] = $name . " => " . ($acl[$key] ? t("Jah") : t("Ei"));
		}

		return implode("<br />", $str);
	}

	/** displays a form to let the user to change her password
		@attrib name=change_pwd

	**/
	function change_pwd()
	{
		print "changing tha password, eh?";
		// I need to return a class_base generated form
	}

	/**
		@attrib params=pos api=1
		@param uid required type=string
		User id (username, not user object's id)
		@comment
		Gets object list of group objects that $uid belongs to
		@returns
		Object list
	**/
	function get_groups_for_user($uid)
	{
		if(!is_valid("uid", $uid))
		{
			return new object_list();
		}
		$groups_list = new object_list();
		$tmp = $this->users->get_oid_for_uid($uid);
		if (acl_base::can("", $tmp))
		{
			$user_obj = obj($tmp);
			if(aw_ini_get("users.use_group_membership") == 1)
			{
				$groups_list = new object_list(array(
					"class_id" => group_obj::CLID,
					"status" => object::STAT_ACTIVE,
					"CL_GROUP.RELTYPE_GROUP(CL_GROUP_MEMBERSHIP).RELTYPE_USER" => $tmp,
					"CL_GROUP.RELTYPE_GROUP(CL_GROUP_MEMBERSHIP).status" => object::STAT_ACTIVE,
					new object_list_filter(array(
						"logic" => "OR",
						"conditions" => array(
							"CL_GROUP.RELTYPE_GROUP(CL_GROUP_MEMBERSHIP).membership_forever" => 1,
							new object_list_filter(array(
								"logic" => "AND",
								"conditions" => array(
									"CL_GROUP.RELTYPE_GROUP(CL_GROUP_MEMBERSHIP).date_start" => new obj_predicate_compare(
										obj_predicate_compare::LESS_OR_EQ, time()
									),
									"CL_GROUP.RELTYPE_GROUP(CL_GROUP_MEMBERSHIP).date_end" => new obj_predicate_compare(
										obj_predicate_compare::GREATER, time()
									),
								),
							)),
						),
					)),
				));
			}
			else
			{
				$groups_list = new object_list(
					$user_obj->connections_from(array(
						"type" => "RELTYPE_GRP",
					))
				);
			}
		}
		return $groups_list;
	}

	/**

		@attrib params=pos api=1
		@param uid required type=string
		User id (username, not user object's id)
		@comment
		Gets array of group priorities that $uid belongs to
		@returns
		Object list
	**/
	function get_group_pri_for_user($uid)
	{
		$groups_list = $this->get_groups_for_user($uid);
		$res = array();
		foreach($groups_list->arr() as $group)
		{
			$res[$group->id()] = $group->prop("priority");
		}
		if ($uid == "")
		{
			// return non logged in users group
			$rv = obj(group::get_non_logged_in_group());
			$res[$rv->id()]= $rv->prop("priority");
		}
		asort($res);
		return $res;
	}

	/**
		@attrib params=pos api=1
		@param uid required type=string
		User id (username, not user object's id)
		@comment
		Gets the group object with highest priority that uid belongs to
		@returns
		User group object
	**/
	function get_highest_pri_grp_for_user($uid, $no_user_grp = false)
	{
		static $cache;
		if (isset($cache[$uid][$no_user_grp]))
		{
			//return $cache[$uid][$no_user_grp];
		}
		if ($uid == "")
		{
			// return non logged in users group
			$rv = obj(group::get_non_logged_in_group());
			$cache[$uid][$no_user_grp] = $rv;
			return $rv;
		}
		$groups = $this->get_groups_for_user($uid);
		if(!$groups)
		{
			$cache[$uid][$no_user_grp] = false;
			return false;
		}
		$groups->sort_by(array(
			"prop" => "priority",
			"order" => "desc"
		));
		if ($no_user_grp)
		{
			$tmp = $groups->begin();
			if ($tmp->prop("type") == aw_groups::TYPE_DEFAULT)
			{
				$rv = $groups->next();
				$cache[$uid][$no_user_grp] = $rv;
				return $rv;
			}
		}
		$rv = $groups->begin();
		$cache[$uid][$no_user_grp] = $rv;
		return $rv;
	}

	/**
		@attrib params=pos api=1
		@param uid required type=string
		User id
		@comment
		Checks wheather the user id is taken or not
		@returns
		returns true if is taken, false otherwise
	**/
	function username_is_taken($uid)
	{
		if (trim($this->db_fetch_field("SELECT uid FROM users WHERE uid LIKE '$uid'", "uid")) != "")
		{
			return true;
		}
		return false;
	}

	/**
		@attrib params=name api=1
		@param oid required type=oid
		@comment
		Handler for user delete. does the delete itself and deletes all other needed objects as well.
		All in all, a very final function.
	**/
	function on_delete_user($arr)
	{
		$u = get_instance("users");

		$user = obj($arr["oid"]);
		if ($user->is_brother())
		{
			return $this->on_delete_user_bro($arr);
		}

		// final delete home folder
		$home_folder = $this->db_fetch_field("SELECT home_folder FROM users WHERE oid = ".$user->id(), "home_folder");
		if (is_oid($home_folder) && $this->_object_ex($home_folder))
		{
			$hf = obj($home_folder);
			$hf->delete(true);
		}

		// final delete default group
		$def_gid_oid = $user->get_default_group();
		if (is_oid($def_gid_oid) && $this->_object_ex($def_gid_oid))
		{
			$def_gid_o = obj($def_gid_oid);
			$def_gid_o->delete(true);
		}

		// final delete person
		$person_c = $user->connections_from(array(
			"type" => "RELTYPE_PERSON",
		));
		foreach($person_c as $p_c)
		{
			$person = obj($p_c->prop("to"));
			$person->delete(true);
		}

		// user's e-mail object
		$mail_c = $user->connections_from(array(
			"type" => "RELTYPE_EMAIL"
		));
		foreach($mail_c as $m_c)
		{
			$mail = obj($m_c->prop("to"));
			$mail->delete(true);
		}

		// final delete all brothers
		// final delete user object
		$user->delete(true);

		cache::file_clear_pt("acl");
		cache::file_clear_pt("storage_object_data");
		cache::file_clear_pt("storage_search");
	}

	function _object_ex($oid)
	{
		return $this->db_fetch_field("SELECT oid FROM objects WHERE oid = '$oid'", "oid") ? true : false;
	}

	function callback_mod_tab($arr)
	{
		if ($arr["id"] == "jdata")
		{
			if (!is_oid($arr["obj_inst"]->id()) || !count($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_JOIN_SITE"))))
			{
				return false;
			}
		}

		// if the user has acl set, then disable the tabs
		$gl = aw_global_get("gidlist_oid");
		foreach($gl as $g_oid)
		{
			$o = obj($g_oid);

			if ($o->prop("type") == 1 || $o->prop("type") == 3)
			{
				continue;
			}
			if ((!isset($can_adm_max) || $o->prop("priority") > $can_adm_max) && $o->prop("if_acls_set"))
			{
				// all settings except can use admin depend on if_acls_set being true
				$dyc = $o->prop("editable_settings");
				$can_adm_max = $o->prop("priority");
			}
		}

		if (isset($dyc) && (!isset($dyc[$arr["id"]]) || !$dyc[$arr["id"]]))
		{
			return false;
		}

		return true;
	}

	function callback_jdata($arr)
	{
		$o = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_JOIN_SITE");
		if (!$o)
		{
			return;
		}
		$i = get_instance(CL_JOIN_SITE);

		return $i->get_elements_from_obj($o, array(
			"err_return_url" => aw_ini_get("baseurl").aw_global_get("REQUEST_URI"),
			"uid" => $arr["obj_inst"]->prop("uid")
		));
	}

	function add_rating($uid, $rating)
	{
		$this->db_query("INSERT INTO user2rating(uid, rating, crea_by, crea)
			VALUES('$uid', '$rating', '".aw_global_get("uid")."', ".time().")
		");
	}

	function get_rating($uid)
	{
		return  $this->db_fetch_field("SELECT SUM(rating) as r FROM user2rating WHERE uid = '$uid'", "r");
	}

	function do_db_upgrade($tbl, $field, $q, $err)
	{
		switch($field)
		{
			case "notify":
				$this->db_add_col($tbl, array(
					"name" => $field,
					"type" => "char"
				));
				return true;
			break;
			case "warning_notification":
			case "aw_extern_id":
				$this->db_add_col($tbl, array(
					"name" => $field,
					"type" => "int",
				));
				break;
		}
		return false;
	}

	/**
		@attrib name=settings_lod
		@param url optional
	**/
	function settings_lod($arr)
	{
		$pm = new popup_menu();
		$pm->begin_menu("settings_pop");

		$gl = aw_global_get("gidlist_oid");
		foreach($gl as $g_oid)
		{
			$o = obj($g_oid);

			if ($o->prop("type") == 1 || $o->prop("type") == 3)
			{
				continue;
			}
			if ((!isset($can_adm_max) || $o->prop("priority") > $can_adm_max) && $o->prop("if_acls_set"))
			{
				// all settings except can use admin depend on if_acls_set being true
				$dyc = $o->prop("editable_settings");
				$can_adm_max = $o->prop("priority");
			}
		}

		$u = obj(aw_global_get("uid_oid"));
		if(!is_class_id($u->class_id()))
		{
			$u->set_class_id(CL_USER);
		}
		$gl = $u->get_group_list();

		foreach($gl as $gn => $d)
		{
			if ($gn == "userdef")
			{
				continue;
			}
			if (!empty($dyc) && empty($dyc[$gn]))
			{
				continue;
			}
			$pm->add_item(array(
				"text" => $d["caption"],
				"link" => html::get_change_url($u->id(), array("group" => $gn, "return_url" => $arr["url"]))
			));
		}
		header("Content-type: text/html; charset=".languages::USER_CHARSET);
		die($pm->get_menu(array(
			"text" => '<img src="/automatweb/images/aw06/ikoon_seaded.gif" alt="seaded" width="17" height="17" border="0" align="left" style="margin: -1px 5px -3px -2px" />'.t("Seaded").' <img src="/automatweb/images/aw06/ikoon_nool_alla.gif" alt="#" width="5" height="3" border="0" class="nool" />'
		)));
	}

	/**
		@attrib name=hist_lod
		@param url optional
	**/
	function hist_lod($arr)
	{
		$pm = new popup_menu();
		$pm->begin_menu("history_pop");

		$u = obj(aw_global_get("uid_oid"));
		if ($u->prop("history_has_folders"))
		{
			$clss = aw_ini_get("classes");
			foreach(array_reverse($_SESSION["user_history"]) as $class => $p)
			{
				$pm->add_sub_menu(array(
					"text" => $clss[clid_for_name($class)]["name"],
					"name" => $class
				));
				foreach(array_reverse($p) as $url => $capt)
				{
					// parse url and get object name / group from the url
					$pm->add_item(array(
						"text" => $capt,
						"link" => $url,
						"parent" => $class
					));
				}
			}
		}
		else
		{
			foreach(array_reverse($_SESSION["user_history"]) as $url => $capt)
			{
				// parse url and get object name / group from the url
				$pm->add_item(array(
					"text" => $capt,
					"link" => $url
				));
			}
		}
		header("Content-type: text/html; charset=".languages::USER_CHARSET);
		die($pm->get_menu(array(
			"text" => '<img src="/automatweb/images/aw06/ikoon_ajalugu.gif" alt="" width="13" height="13" border="0" class="ikoon" />'.t("Ajalugu").' <img src="/automatweb/images/aw06/ikoon_nool_alla.gif" alt="#" width="5" height="3" border="0" style="margin: 0 -3px 1px 0px" />'
		)));
	}

	public static function require_password_change($uid)
	{
		$user_inst = new user();
		$gid_obj = $user_inst->get_highest_pri_grp_for_user($uid);
		if(is_object($gid_obj) && $gid_obj->prop("require_change_pass"))
		{
			return true;
		}
	}

	function is_first_login($uid)
	{
		$user = obj($this->users->get_oid_for_uid($uid));
		if(!$user->prop("logins"))
		{
			return true;
		}
	}

	/** returns the oid of the all users group
		@attrib api=1
	**/
	static public function get_all_users_group()
	{
		$c = new config();
		$aug_oid = $c->get_simple_config("all_users_grp_oid");
		if (!$c->can("view", $aug_oid))
		{
			$aug = aw_ini_get("groups.all_users_grp");
			// convert to oid and store that
			$ol = new object_list(array(
				"class_id" => group_obj::CLID,
				"gid" => $aug
			));
			if ($ol->count())
			{
				$go = $ol->begin();
				$aug_oid = $go->id();
				$c->set_simple_config("all_users_grp_oid", $aug_oid);
			}
			else
			{
				throw new awex_no_group(sprintf(t("could not find the group oid for gid %s"), $aug));
			}
		}
		return $aug_oid;
	}

	/** Returns true if the given user is member of the given usergroup.
		@attrib api=1 params=pos

		@param user required

		@param group required
	**/
	public function is_group_member($user, $group)
	{
		return get_instance("user_obj")->is_group_member($user, $group);
	}
}
