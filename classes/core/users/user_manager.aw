<?php

// user_manager.aw - Kasutajate haldus
/*
HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_NEW, CL_GROUP, on_create_group)
HANDLE_MESSAGE_WITH_PARAM(MSG_POPUP_SEARCH_CHANGE,CL_USER_MANAGER, on_popup_search_change)

@classinfo syslog_type=ST_USER_MANAGER relationmgr=yes no_comment=1 prop_cb=1


@default table=objects
@default group=general

	@property root type=relpicker reltype=RELTYPE_ROOT field=meta method=serialize no_edit=1
	@caption Vaikimisi grupp
	@comment Hallata saab selle objekti all olevaid gruppe ja kasutajaid

	@property all_roots type=relpicker reltype=RELTYPE_ROOT multiple=1 store=connect
	@caption Juurkaustad/-grupid
	@comment Hallata saab selle objekti all olevaid gruppe ja kasutajaid

	@property default_loginmenu type=select field=meta method=serialize
	@caption Vaikimisi loginmen&uuml;&uuml; uutel gruppidel
	@comment Seos Loginmen&uuml;&uuml;de juurkaust peab olema loodud

@groupinfo users caption=Kasutajad
@default group=users

	@layout hbox_toolbar type=hbox

		@property users_tb type=toolbar store=no no_caption=1 editonly=1 parent=hbox_toolbar

	@layout hbox_data type=hbox width=20%:80%

		@layout left_bit type=vbox parent=hbox_data

			@layout vbox_users_tree type=vbox parent=left_bit closeable=1 area_caption=Kasutajate&nbsp;puu

				@property groups_tree type=treeview no_caption=1 store=no parent=vbox_users_tree
				@caption Puu

			@layout vbox_users_search type=vbox parent=left_bit closeable=1 area_caption=Kasutajate&nbsp;otsing

				@layout vbox_users_search_s1 type=vbox parent=vbox_users_search

					@property search_user type=textbox size=20 store=no parent=vbox_users_search_s1 captionside=top
					@caption Kasutajanimi

					@property search_person type=textbox size=20 store=no parent=vbox_users_search_s1 captionside=top
					@caption Isiku nimi

					@property search_groups type=textbox size=20 store=no parent=vbox_users_search_s1 captionside=top
					@caption Grupid (komandega eraldatult)

					@property search_active_time type=textbox size=4 store=no parent=vbox_users_search_s1 captionside=top
					@caption Viimati aktiivne (p&auml;eva tagasi)

					@property search_blocked type=chooser store=no parent=vbox_users_search_s1 captionside=top
					@caption Kasutaja blokeering

				@layout vbox_users_search_s2 type=vbox_sub no_padding=1 parent=vbox_users_search closeable=1 area_caption=Otsingu&nbsp;asukoht

					@property search_users_from type=chooser store=no parent=vbox_users_search_s2 captionside=top no_caption=1 orient=vertical

				@property search_sbt type=submit store=no parent=vbox_users_search size=20 captionside=top no_caption=1
				@caption Otsi

	@layout vbox_users_content type=vbox parent=hbox_data

		@property table_users type=table store=no no_caption=1 parent=vbox_users_content
		@caption Kasutajad

@groupinfo usergroups caption=Kasutajagrupid
@default group=usergroups

	@property usergroups_tb type=toolbar store=no no_caption=1

	@layout ug_hbox_data type=hbox width=20%:80%

		@layout ug_left_bit type=vbox parent=ug_hbox_data

			@layout vbox_ug_tree type=vbox parent=ug_left_bit closeable=1 area_caption=Kasutajagruppide&nbsp;puu

				@property usergroups_tree type=treeview no_caption=1 store=no parent=vbox_ug_tree

			@layout vbox_ug_search type=vbox parent=ug_left_bit closeable=1 area_caption=Kasutajagruppide&nbsp;otsing

				@layout vbox_ug_search_name type=vbox parent=vbox_ug_search

					@property ug_search_txt type=textbox store=no parent=vbox_ug_search_name size=20 captionside=top
					@caption Grupi nimi

				@layout vbox_ug_search_u type=vbox_sub parent=vbox_ug_search closeable=1 area_caption=Otsi&nbsp;kasutajate&nbsp;gruppe no_padding=1

					@property search_users_usergroups type=chooser store=no parent=vbox_ug_search_u no_caption=1 orient=vertical

				@layout vbox_ug_search_g type=vbox_sub parent=vbox_ug_search closeable=1 area_caption=Otsi&nbsp;kasutajagruppe no_padding=1

					@property search_usergroups store=no type=chooser parent=vbox_ug_search_g no_caption=1 orient=vertical

				@property ug_search_sbt type=submit store=no parent=vbox_ug_search size=20 captionside=top no_caption=1
				@caption Otsi

	@layout vbox_ug_content type=vbox parent=ug_hbox_data

		@property table_usergroups type=table store=no no_caption=1 parent=vbox_ug_content

@groupinfo content_packages caption=Sisupaketid

	@groupinfo packages caption=Paketid parent=content_packages
	@default group=packages

		@property packages_tlb type=toolbar no_caption=1 store=no

		@property packages_tbl type=table no_caption=1 store=no

	@groupinfo price_conditions caption=Hinnatingimused parent=content_packages
	@groupinfo conditions caption=Sisutingimused parent=content_packages
	@default group=price_conditions,conditions

		@property price_conditions_tlb type=toolbar no_caption=1 store=no group=price_conditions
		@property conditions_tlb type=toolbar no_caption=1 store=no group=conditions

		@layout conditions_ type=hbox width=20%:80%

			@layout conditions_left type=vbox parent=conditions_

				@layout conditions_tree type=vbox parent=conditions_left closeable=1 area_caption=Sisupaketid

					@property conditions_tree type=treeview no_caption=1 store=no parent=conditions_tree

			@layout conditions_right type=vbox parent=conditions_

				@property price_conditions_tbl type=table no_caption=1 store=no parent=conditions_right group=price_conditions
				@property conditions_tbl type=table no_caption=1 store=no parent=conditions_right group=conditions

	@groupinfo cp_settings caption=Seaded parent=content_packages
	@default group=cp_settings

		@property cp_warehouse type=relpicker reltype=RELTYPE_PACKAGE_WAREHOUSE store=connect
		@caption Ladu
		@comment Ladu, kuhu luuakse toote objektid iga paketi jaoks

@reltype ROOT value=1 clid=CL_GROUP,CL_MENU
@caption Juurkaust/-grupp

@reltype LOGIN_ROOT value=2 clid=CL_MENU
@caption Loginmen&uuml;&uuml;de juurkaust

@reltype LOGIN_CONF value=3 clid=CL_CONFIG_LOGIN_MENUS
@caption Loginmen&uuml;&uuml; seadete objekt

@reltype PACKAGE_WAREHOUSE value=4 clid=CL_SHOP_WAREHOUSE
@caption Ladu

*/

class user_manager extends class_base
{
	var $parent = null;
	var $permissions_form;

	function user_manager()
	{
		// HTML for permissions_form used in popup when linking group to folders and objects
		$this->permissions_form = "<p class='plain'>".t("Vali &otilde;igused").":<br>".html::checkbox(array(
			'name' => 'sel_can_view',
			'caption' => t("Vaatamine"),
		));
		$this->permissions_form .= "<br>".html::checkbox(array(
			'name' => 'sel_can_edit',
			'caption' => t("Muutmine"),
		));
		$this->permissions_form .= "<br>".html::checkbox(array(
			'name' => 'sel_can_delete',
			'caption' => t("Kustutamine"),
		));
		$this->permissions_form .= "<br>".html::checkbox(array(
			'name' => 'sel_can_add',
			'caption' => t("Lisamine"),
		));
		$this->permissions_form .= "<br>".html::checkbox(array(
			'name' => 'sel_can_admin',
			'caption' => t("ACL Muutmine"),
		));
		$this->permissions_form .= "</p>";

		// change this to the folder under the templates folder, where this classes templates will be,
		// if they exist at all. Or delete it, if this class does not use templates
		$this->init(array(
			"clid" => user_manager_obj::CLID
		));
	}

	//////
	// Display stuff
	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "root":
				$ids = array();
				foreach($arr["obj_inst"]->all_roots as $oid)
				{
					$ot = new object_tree(array(
						"parent" => $oid,
						"class_id" => group_obj::CLID,
						"lang_id" => array(),
						"site_id" => array(),
					));
					$ids[$oid] = $oid;
					$ids = array_merge($ids, $ot->ids());
				}
				if(count($ids) > 0)
				{
					$ol = new object_list(array(
						"class_id" => group_obj::CLID,
						"oid" => $ids,
						"lang_id" => array(),
						"site_id" => array(),
						new obj_predicate_sort(array(
							"jrk" => "ASC",
							"name" => "ASC",
						)),
					));
					$prop["options"] = array(0 => t("--vali--")) + $ol->names();
				}
				break;

			case "search_blocked":
				$prop["options"] = array(
					"0" => t("K&otilde;ik"),
					"1" => t("Blokeeritud"),
					"2" => t("Blokeerimata"),
				);
				$srch_data = $arr["obj_inst"]->meta("ug_search_by_".aw_global_get("uid"));
				$prop["value"] = $srch_data[$prop["name"]] ? $srch_data[$prop["name"]] : 0;
				break;

			case "search_users_from":
				$prop["options"] = array(
					1 => t("T&ouml;&ouml;laua seest"),
					2 => t("Kogu s&uuml;steemist"),
				);
				$srch_data = $arr["obj_inst"]->meta("search_by_".aw_global_get("uid"));
				$prop["value"] = $srch_data[$prop["name"]] ? $srch_data[$prop["name"]] : 1;
				break;

			case "search_users_usergroups":
			case "search_usergroups":
				$prop["options"] = array(
					0 => t("Mitte kuskilt"),
					1 => t("T&ouml;&ouml;laua seest"),
					2 => t("Kogu s&uuml;steemist"),
				);
			case "ug_search_txt":
				$srch_data = $arr["obj_inst"]->meta("ug_search_by_".aw_global_get("uid"));
				$prop["value"] = $srch_data[$prop["name"]] ? $srch_data[$prop["name"]] : 0;
				break;

			case "search_active_time":
			case "search_user":
			case "search_person":
			case "search_groups":
				$srch_data = $arr["obj_inst"]->meta("search_by_".aw_global_get("uid"));
				$prop["value"] = $srch_data[$prop["name"]];
				break;

			case "ug_search_sbt":
			case "search_sbt":
				$prop["onclick"] = "aw_get_el('search_button_pressed').value = 1;";
				break;

			case 'inactive_tb':
				$this->parent = null;
			case "usergroups_tb":
			case 'users_tb':
				$this->do_users_toolbar($prop['toolbar'], $arr);
			break;
			case 'table_selected_groups':
				$this->do_table_selected_groups($prop['vcl_inst'], $arr);
			break;
			case 'table_usergroups':
				$this->do_table_groups($prop['vcl_inst'], $arr);
			break;
			case 'table_users':
				$arr['type'] = 'grouprelated';
			case 'table_inactive':
				$arr['type'] = $arr['type'] ? $arr['type'] : 'inactive';
				if($arr["request"]["search_button_pressed"])
				{
					$arr['type'] = "search";
				}
				$this->do_table_users($prop['vcl_inst'], $arr);
			break;
			case "usergroups_tree":
				$prop['vcl_inst']->start_tree(array(
					'root_name' => $this->parent != "root" ? t("Juurkaustad/-grupid") : "<b>".t("Juurkaustad/-grupid")."</b>",
					'root_url' => aw_url_change_var(array("parent" => "root", "search_button_pressed" => 0)),
					'has_root' => true,
				));
			case 'groups_tree':
				$default = $arr['obj_inst']->prop('root');
				$parent = $arr['obj_inst']->prop('all_roots');
				if (!$parent)
				{
					$prop['error'] = t("Juurkaustad/-grupid valimata");
					return PROP_ERROR;
				}
				$this->do_groups_tree($prop['vcl_inst'], $parent, 0);
			break;
			case 'default_loginmenu':
				if ($arr["new"])
				{
					return PROP_IGNORE;
				}
				// create list from selected folder's 2nd level children
				$root = $arr['obj_inst']->get_first_obj_by_reltype('RELTYPE_LOGIN_ROOT');
				if (is_object($root))
				{
					$list = array();

					$kids = new object_list(array(
						'parent' => $root->id(),
						'class_id' => CL_MENU,
						"lang_id" => array(),
						"site_id" => array(),
					));
					for ($k = $kids->begin(); !$kids->end(); $k = $kids->next())
					{
						$prefix = $k->name() . ' &gt; ';

						$grandkids  = new object_list(array(
							'parent' => $k->id(),
							'class_id' => CL_MENU,
							"lang_id" => array(),
							"site_id" => array(),
						));

						for ($gk = $grandkids->begin(); !$grandkids->end(); $gk = $grandkids->next())
						{
							$list[$gk->id()] = $prefix.$gk->name();
						}
					}
					$prop['options'] = $list;
				}

			break;
			case 'inactive_period':
				$prop['options'] = array (
					1 => '1 ' . t('p&auml;ev'),
					7 => '1 ' . t('n&auml;dal'),
					31 => '1 ' . t('kuu'),
					62 => '2 ' . t('kuud'),
					92 => '3 ' . t('kuud'),
					183 => '6 ' . t('kuud'),
					365 => '1 ' . t('aasta'),
				);
			break;
		}
		return $retval;
	}

	// Store stuff
	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "conditions_tbl":
				foreach($arr["request"]["conditions_tbl"] as $id => $data)
				{
					$u = obj($id);
					$u->set_prop("price", $data["price"]);
					$u->set_prop("acl_change", isset($data["acls"]["acl_change"]) ? 1 : 0);
					$u->set_prop("acl_add", isset($data["acls"]["acl_add"]) ? 1 : 0);
					$u->set_prop("acl_admin", isset($data["acls"]["acl_admin"]) ? 1 : 0);
					$u->set_prop("acl_delete", isset($data["acls"]["acl_delete"]) ? 1 : 0);
					$u->set_prop("acl_view", isset($data["acls"]["acl_view"]) ? 1 : 0);
					$u->save();
				}
				break;

			case "packages_tbl":
				foreach($arr["request"]["packages_tbl"] as $id => $data)
				{
					$data["date_start"] = mktime(0, 0, 0, $data["date_start"]["month"], $data["date_start"]["day"], $data["date_start"]["year"]);
					$data["date_end"] = mktime(0, 0, 0, $data["date_end"]["month"], $data["date_end"]["day"], $data["date_end"]["year"]);
					$o = obj($id);
					foreach($data as $k => $v)
					{
						$o->set_prop($k, $v);
					}
					$o->save();
				}
				break;

			case "search_sbt":
				$srch_prps = array("search_blocked", "search_active_time", "search_user", "search_person", "search_groups", "search_users_from");
				$srch_data = array();
				foreach($srch_prps as $srch_prp)
				{
					if(!empty($arr["request"][$srch_prp]))
					{
						$srch_data[$srch_prp] = $arr["request"][$srch_prp];
					}
				}
				$arr["obj_inst"]->set_meta("search_by_".aw_global_get("uid"), $srch_data);
				break;

			case "ug_search_sbt":
				$srch_prps = array("search_users_usergroups", "search_usergroups", "ug_search_txt");
				$srch_data = array();
				foreach($srch_prps as $srch_prp)
				{
					if(isset($arr["request"][$srch_prp]))
					{
						$srch_data[$srch_prp] = $arr["request"][$srch_prp];
					}
				}
				$arr["obj_inst"]->set_meta("ug_search_by_".aw_global_get("uid"), $srch_data);
				break;

			case 'table_groups':
			case "table_usergroups":
				// Save priority values
				if (isset($arr['request']['priority']) && is_array($arr['request']['priority']))
				{
					foreach ($arr['request']['priority'] as $oid => $value)
					{
						$o = obj($oid);
						if (is_numeric($value) && is_oid($oid) && $this->can('edit', $oid) && $o->class_id() == group_obj::CLID
							&& $o->prop('priority') != $value)
						{
							$o->set_prop('priority', $value);
						}
						$o->set_prop("status", isset($arr["request"]["status"][$oid]) && $arr["request"]["status"][$oid] == object::STAT_ACTIVE ? object::STAT_ACTIVE : object::STAT_NOTACTIVE);
						$o->set_prop('type', $arr["request"]["type"][$oid]);
						$o->save();
					}
				}

				foreach(safe_array($arr["request"]["old_can_admin_interface"]) as $g_oid => $val)
				{
					$go = obj($g_oid);
					if ($arr["request"]["can_admin_interface"][$g_oid] != $go->prop("can_admin_interface"))
					{
						$go->set_prop("can_admin_interface", $arr["request"]["can_admin_interface"][$g_oid]);
						$go->save();
					}
				}

				foreach ($arr["request"] as $k => $v)
				{
					if (substr($k, 0, 9) == "rootmenu_" && is_oid($v))
					{
						list(, $g_oid) = explode("_", $k);
						// set rootmenu for group
						$o = obj($g_oid);
						$rootmenus = $o->prop('admin_rootmenu2');
						$rootmenus[aw_global_get("lang_id")][0] = $v;
						$o->set_prop("admin_rootmenu2", $rootmenus);
						$o->save();
					}
				}
			break;

			case "table_users":
				foreach($arr["request"]["table_users"] as $oid => $data)
				{
					$o = obj($oid);
					if(!isset($data["blocked"]) && $data["old_blocked"] == 1 || $data["blocked"] == 1 && $data["old_blocked"] == 0)
					{
						$o->set_prop('blocked', !$o->prop("blocked"));
					}
					$o->set_prop('rte_disabled', !$data["allow_rte"]);
					$o->set_prop('ui_language', $data["set_ui_lang"]);
					$o->save();
				}
				break;

		}
		return $retval;
	}

	// Carry to POST some variables
	function callback_mod_reforb(&$arr, $request)
	{
		$arr['last_parent'] = $this->parent;
		$arr['ob_group'] = 'um';
		if(isset($this->_add_vars))
		{
			foreach(safe_array($this->_add_vars) as $var)
			{
				$arr[$var] = "0";
			}
		}
		$arr["post_ru"] = post_ru();
		$arr["search_button_pressed"] = 0;
	}

	// Unset parent if searching
	function callback_pre_edit($arr)
	{
		$this->parent = $this->find_parent($arr['obj_inst']);
	}

	function callback_mod_retval (&$arr)
	{
		$vars = array(
			"ug_search_txt" => "ug_search_txt",
			"search_users_usergroups" => "search_users_usergroups",
			"search_users_from" => "search_users_from",
			"search_usergroups" => "search_usergroups",
			"search_blocked" => "search_blocked",
			"search_active_time" => "search_active_time",
			"search_user" => "search_user",
			"search_person" => "search_person",
			"search_groups" => "search_groups",
			"parent" => "last_parent",
			"search_button_pressed" => "search_button_pressed",
		);
		foreach($vars as $arg => $req)
		{
			if(!empty($arr["request"][$req]))
			{
				$arr["args"][$arg] = $arr["request"][$req];
			}
		}
	}

	// Adds content to users toolbar
	function do_users_toolbar($tb, $arr)
	{
		if(isset($this->parent))
		{
			$tb->add_new_button(array(CL_USER, group_obj::CLID), $this->parent);
			$tb->add_separator();
		}
		if(!$arr["request"]["search_button_pressed"])
		{
			$arr["request"]["search_button_pressed"] = 0;
		}

		$tb->add_button(array(
            "name" => "save",
            "img" => "save.gif",
            "action" => "",
            "tooltip" => t("Salvesta"),
			"onClick" => "aw_get_el('search_button_pressed').value = ".$arr["request"]["search_button_pressed"].";",
        ));


		// Copypaste buttons
		$this->do_objectbuffer_toolbar(array(
			'toolbar' => $tb,
			'ob_group' => 'um',
		));

		$tb->add_separator();

		$tb->add_button(array(
			'name' => 'delete',
			'tooltip' => t("Kustuta valitud"),
			'img' => 'delete.gif',
			"url" => "javascript:if(confirm('".t("Kustutada valitud objektid?")."')){submit_changeform('delete')};",
		));

	}

	// Create cut, copy and paste buttons, if needed
	function do_objectbuffer_toolbar($arr)
	{
		if (!isset($arr['toolbar']))
		{
			return;
		}
		$cut_action = isset($arr['cut_action']) ? $arr['cut_action'] : 'ob_cut';
		$copy_action = isset($arr['copy_action']) ? $arr['copy_action'] : 'ob_copy';
		$paste_action = isset($arr['paste_action']) ? $arr['paste_action'] : 'ob_paste';
		$prefix = isset($arr['ob_group']) ? $arr['ob_group'].'_' : '';


		$tb = $arr['toolbar'];
		$tb->add_button(array(
			'name' => 'cut',
			'tooltip' => t("L&otilde;ika"),
			'img' => 'cut.gif',
			'action' => $cut_action,
		));
		$tb->add_button(array(
			'name' => 'copy',
			'tooltip' => t("Kopeeri"),
			'img' => 'copy.gif',
			'action' => $copy_action,
		));

		$tooltip = "Ei saa asetada";
		$disabled = true;
		if($arr["request"]["group"] == "users")
		{
			$cut_objects = safe_array(aw_global_get('user_management_cut_users'));
			$copy_objects = safe_array(aw_global_get('user_management_copied_users'));
		}
		else
		{
			$cut_objects = safe_array(aw_global_get('cut_objects'));
			$copy_objects = safe_array(aw_global_get('user_management_copied_groups'));
		}
		if (isset($this->parent) && (count($cut_objects) || count($copy_objects)) && $this->can('view', $this->parent))
		{
			$tooltip = "";
			$names = array();
			foreach (array_keys($cut_objects) as $oid)
			{
				$o = obj($oid);
				$names[] = $o->name();
			}
			if (count($names))
			{
				$tooltip = t('L&otilde;igatud').": ".join(",",$names);
				$tooltip .= " \n";
			}
			$names = array();
			foreach (array_keys($copy_objects) as $oid)
			{
				$o = obj($oid);
				$names[] = $o->name();
			}
			if (count($names))
			{
				$tooltip .= t('Kopeeritud').": ".join(",",$names);
			}
			$disabled = false;
		}

		if (!$disabled)
		{
			$tb->add_button(array(
				'name' => 'paste',
				'tooltip' => t("Aseta")."\n".' ('.$tooltip.')',
				'img' => 'paste.gif',
				'action' => $paste_action,
			//	'disabled' => $disabled,
			));
		}

	}

	/** deletes selected objects

		@attrib name=delete

		@param sel_u optional
		@param sel_g optional
		@param post_ru optional
	**/
	function delete($arr)
	{
		$selected = safe_array(ifset($arr,'sel_u')) + safe_array(ifset($arr,'sel_g'));
		foreach($selected as $oid)
		{
			obj($oid)->delete();
		}
		return $arr['post_ru'];
	}

	/** cuts objects. wrapper.

		@attrib name=ob_cut
		@param sel_u optional
		@param sel_g optional
		@param post_ru optional
		@parem parent optional

	**/
	function ob_cut($arr)
	{
		$selected = safe_array(ifset($arr,'sel_u')) + safe_array(aw_global_get('user_management_cut_users'));
		if (count($selected))
		{
			aw_session_del('user_management_copied_users');
			aw_session_set("user_management_users_cut_from_group", aw_global_get("user_management_last_parent"));
			aw_session_set('user_management_cut_users', $selected);
		}

		$selected = safe_array(ifset($arr,'sel_g'));// + safe_array(aw_global_get('user_management_cut_groups'));
		if (count($selected))
		{
//			aw_session_del('user_management_copied_groups');
//			aw_session_set("user_management_groups_cut_from_group", aw_global_get("user_management_last_parent"));
//			aw_session_set('user_management_cut_groups', $selected);

			$o = get_instance("admin/admin_if");
			return $o->if_cut(array('sel' => $selected, 'return_url' => $arr['post_ru']));
		}
		return $arr["post_ru"];
	}

	/** copies objects. wrapper.

		@attrib name=ob_copy

		@param sel_u optional
		@param sel_g optional
		@param post_ru optional

	**/
	function ob_copy($arr)
	{
		$selected = safe_array(ifset($arr,'sel_u')) + safe_array(aw_global_get('user_management_copied_users'));
		if (count($selected))
		{
			aw_session_del('user_management_cut_users');
			aw_session_set('user_management_copied_users', $selected);
		}

		$selected = safe_array(ifset($arr,'sel_g')) + safe_array(aw_global_get('user_management_copied_groups'));
		if (count($selected))
		{
			aw_session_set('user_management_copied_groups', $selected);
		}
		return $arr["post_ru"];
	}

	/** pastes the cut/copied objects. wrapper.

		@attrib name=ob_paste

	**/
	function ob_paste($arr)
	{
		$gi = get_instance(group_obj::CLID);
		$parent = obj($arr["last_parent"]);
		$previous_parent = obj(aw_global_get("user_management_users_cut_from_group"));
		if(!is_oid(ifset($arr,'last_parent')))
		{
			return $arr["post_ru"];
		}

		foreach(safe_array(aw_global_get("user_management_cut_users")) as $oid)
		{
			$gi->remove_user_from_group(obj($oid), $previous_parent);
			$gi->add_user_to_group(obj($oid), $parent);
		}
		aw_session_del("user_management_cut_users");

		foreach(safe_array(aw_global_get("user_management_copied_users")) as $oid => $xml)
		{
			$gi->add_user_to_group(obj($oid), $parent);
		}
		aw_session_del("user_management_copied_users");

		foreach(safe_array(aw_global_get("user_management_copied_groups")) as $oid => $xml)
		{
			$o = obj($oid);
			$o->create_brother($arr["last_parent"]);
		}
		aw_session_del("user_management_copied_groups");

		if(is_oid(ifset($arr,'last_parent')))
		{
			$o = get_instance("admin/admin_if");
			return $o->if_paste(array('parent' => $arr['last_parent'], 'return_url' => $arr['post_ru']));
		}

		return $arr["post_ru"];
	}

	/** blocks/unblocks a user

		@attrib name=block_u

		@param oid required type=int acl=edit class=CL_USER
		@param post_ru required

	**/
	function block_u($arr)
	{
		$o = obj($arr['oid']);
		$o->set_prop('blocked', !$o->prop('blocked'));
		$o->save();
		return $arr['post_ru'];
	}

	// Recursively populates groups tree
	function do_groups_tree($tree, $parents, $treeroot = 1)
	{
		foreach($parents as $parent)
		{
			$parent_obj = obj($parent);
			$treedata = new object_tree(array(
				'parent' => $parent,
				'class_id' => group_obj::CLID
			));
			$treenames = $treedata->to_list()->names();
			foreach($treedata->tree as $pt => $items)
			{
				foreach($items as $item)
				{
					if($this->parent == $item)
					{
						$tree->set_selected_item($item);
					}
					$tree->add_item($pt, array(
						"id" => $item,
						"name" => parse_obj_name($treenames[$item])." (".count($treedata->tree[$item]).")".html::obj_change_url($item, t(" (M)")),
						"reload" => array(
							"layouts" => array("vbox_users_content", "vbox_ug_content"),
							"params" => array("parent" => $item, "search_button_pressed" => NULL),
						),
						"iconurl" => icons::get_icon_url(group_obj::CLID,""),
					));
				}
			}
			if($this->parent == $parent)
			{
				$tree->set_selected_item($parent);
			}
			$tree->add_item(0, array(
				"id" => $parent,
				"name" => parse_obj_name($parent_obj->name())." (".count($treedata->tree[$parent]).")".html::obj_change_url($parent, t(" (M)")),
				"reload" => array(
					"layouts" => array("vbox_users_content", "vbox_ug_content"),
					"params" => array("parent" => $parent),
				),
				"iconurl" => icons::get_icon_url($parent_obj->class_id(),""),
			));
		}
	}

	// Search functionality is in here, too
	function do_table_selected_groups ($table, $arr)
	{
		if (isset($arr['request']["search_blocked"]) || isset($arr['request']["search_active_time"]) || isset($arr['request']["search_user"]) || isset($arr['request']["search_person"]) || isset($arr['request']["search_groups"])) // This deals with searching
		{
			if (!is_oid($arr['obj_inst']->prop('root')))
			{
				return;
			}
			$search = $arr['request']['search_txt'];
			$parent = $arr['obj_inst']->prop('root');
			$ol = new object_list(array(
				'name' => '%'.$search.'%'
			));

			// Sweep through all found groups, check paths
			$groups = array();
			for ($o = $ol->begin(); !$ol->end(); $o = $ol->next())
			{
				if ($o->class_id() != group_obj::CLID)
				{
					continue;
				}
				foreach ($o->path() as $p)
				{
					if ($p->oid == $parent)
					{
						$groups[] = $o->id();
						break;
					}
				}
			}

			$arr['groups_list'] = $groups;
			switch(count($groups))
			{
				case 0:
					// No matches
					$arr['title'] = t("Ei leitud midagi");
				break;
				case 1:
					// One match, make it parent, continue as usual
					$arr['title'] = t("Grupp '%s'");
					$this->parent = $groups[0];
				break;
				default:
					// Many matches, just list them
					$arr['title'] = t("Leitud grupid");
				break;
			}
			return $this->do_table_groups($table, $arr);
		}
		else if (is_oid($this->parent))
		{
			$parent = obj($this->parent);
			if ($parent->class_id() != group_obj::CLID)
			{
				return;
			}
			$arr['groups_list'] = array($this->parent);
			$arr['title'] = t("Grupp '%s'");
			return $this->do_table_groups($table, $arr);
		}
	}

	// Defines and populates users table
	function do_table_users ($table, $arr)
	{
		if ($arr['type'] == 'grouprelated' && (!isset($this->parent) || !is_oid($this->parent)))
		{
			return;
		}
		print '<script src="/automatweb/js/popup_menu.js" type="text/javascript"></script>';
		$fields = array(
			array(
				'name' => 'username',
				'caption' => t("Kasutajanimi"),
			),
			array(
				'name' => 'name',
				'caption' => t("Isik"),
			),
			array(
				'name' => 'company',
				'caption' => t("Organisatsioon"),
			),
			array(
				'name' => 'mail',
				'caption' => t("E-post"),
			),
			array(
				'name' => 'last_active',
				'caption' => t("Viimane tegevus"),
			),
			array(
				'name' => 'block',
				'caption' => t("B?"),
				'tooltip' => t("Kasutaja blokeerimine s&uuml;steemist"),
			),
			array(
				'name' => 'if_lang',
				'caption' => t("Liidese keel"),
			),
			array(
				'name' => 'allow_rte',
				'caption' => t("RTE"),
				'tooltip' => t("Kas RTE on lubatud?"),
			),
			array(
				'name' => 'groups',
				'caption' => t("Grupid"),
			),
		);
		foreach ($fields as $f)
		{
			 // By default fields are sortable and aligned to right
			$f['sortable'] = isset($f['sortable']) ? $f['sortable'] : true;
			$f['align'] = isset($f['align']) ? $f['align'] : 'right';
			$f['chgbgcolor'] = 'cutcopied';
			$table->define_field($f);
		}
		$table->define_chooser(array(
			'field' => 'oid',
			'name' => 'sel_u',
			'chgbgcolor' => 'cutcopied',
		));


		// Now, find data for the table
		$users = array();
		switch ($arr['type'])
		{
			case 'grouprelated':
				$parent_obj = obj($this->parent);
				$table->set_caption(sprintf(t("Grupi '%s' kasutajad"), $parent_obj->name).$link);
				$g = obj($this->parent);
				$users = $g->get_group_members()->ids();
			break;

			case 'inactive':
				$table->set_header(t("Mitteaktiivsed kasutajad").$link);
				// Find period of idleness needed to be listed
				$period = $arr['obj_inst']->prop('inactive_period');
				if (empty($period))
				{
					$period = 31; // 31 p2eva
				}
				$ol = new object_list(array(
					'class_id' => CL_USER,
					'lastaction' => new obj_predicate_compare(OBJ_COMP_LESS, time()-$period*24*3600),  // Last activity less than period days ago
					'brother_of' => new obj_predicate_prop('id'),
					//'status' => STAT_NOTACTIVE,
					"lang_id" => array(),
					"site_id" => array(),
				));
				$users = $ol->ids();
			break;

			case "search":
				$table->set_caption("Otsingutulemused");
				$r = $arr["request"];
				$ol_args = array(
					"class_id" => CL_USER,
					"brother_of" => new obj_predicate_prop("id"),
					"parent" => array(),
					"status" => array(),
					"lang_id" => array(),
					"site_id" => array(),
				);
				if($r["search_blocked"] == 1)
				{
					$ol_args["CL_USER.blocked"] = 1;
				}
				elseif($r["search_blocked"] == 2)
				{
					$ol_args["CL_USER.blocked"] = new obj_predicate_not(1);
				}
				if($r["search_active_time"])
				{
					$ol_args["CL_USER.lastaction"] = new obj_predicate_compare(OBJ_COMP_LESS, time() - (int)$r["search_active_time"]*24*3600);
				}
				if($r["search_user"])
				{
					$ol_args["CL_USER.name"] = "%".$r["search_user"]."%";
				}
				if($r["search_person"])
				{
					$ol_args["CL_USER.RELTYPE_PERSON.name"] = "%".$r["search_person"]."%";
				}
				if($r["search_groups"])
				{
					$conditions = array();
					$grps = explode(",", $r["search_groups"]);
					foreach($grps as $grp)
					{
						$conditions[] = "%".trim($grp)."%";
					}
					if(aw_ini_get("users.use_group_membership") == 1)
					{
						$ol_args["CL_USER.RELTYPE_USER(CL_GROUP_MEMBERSHIP).RELTYPE_GROUP.name"] = $conditions;
					}
					else
					{
						$ol_args["CL_USER.RELTYPE_GRP.name"] = $conditions;
					}
				}
				if($r["search_users_from"] == 1)
				{
					foreach($arr["obj_inst"]->all_roots as $oid)
					{
						$ot = new object_tree(array(
							'parent' => $oid,
							'class_id' => group_obj::CLID,
							"lang_id" => array(),
							"site_id" => array(),
						));
						$ids[] = $oid;
						$ids = array_merge($ids, $ot->ids());
					}
					if(aw_ini_get("users.use_group_membership") == 1)
					{
						$ol_args["CL_USER.RELTYPE_USER(CL_GROUP_MEMBERSHIP).RELTYPE_GROUP"] = $ids;
					}
					else
					{
						$ol_args["CL_USER.RELTYPE_GRP"] = $ids;
					}
				}
				$ol = new object_list($ol_args);
				$users = $ol->ids();
				break;
		}
		if (!count($users))
		{
			$table = "";
		}

		$df = aw_ini_get('config.dateformats');
		$user_inst = new user;
		foreach ($users as $u)
		{
			$o = obj($u);
			if (!$this->can('view', $o->id()))
			{
				continue;
			}

			$ccp = (isset($_SESSION["copied_objects"][$o->id()]) || isset($_SESSION["cut_objects"][$o->id()]) ? "#E2E2DB" : "");

			// Find user's groups
			$conns = $o->connections_to(array(
				'type' => 'RELTYPE_MEMBER',
				'from.class_id' => group_obj::CLID,
			));
			$groups = array();
			foreach ($conns as $c)
			{
				$from = $c->from();
				$groups[ html::href(array(
					'caption' => $c->prop('from.name'),
					'url' => aw_url_change_var(array("parent" => $c->prop('from'), "search_button_pressed" => 0)),
				)) ] = $from->prop('priority');;
			}
			arsort($groups); // Sort groups by priority
			$groups = array_keys($groups);
			// Find user's company, if CL_USER has CL_CRM_PERSON
			$companies = array();
			if ($person = $o->get_first_obj_by_reltype('RELTYPE_PERSON'))
			{
				$comps = $person->get_org_selection();
				$companies = array();
				foreach ($comps as $c => $nm)
				{
					$companies[] = html::href(array(
						'caption' => parse_obj_name($nm),
						'url' => $this->mk_my_orb("change", array(
							'id' => $c,
							'return_url' => get_ru()
						), CL_CRM_COMPANY),
					));
				}
			}

			$items = array( // Edit-menu items
				$this->mk_my_orb("change", array(
						'id' => $o->id(),
						'return_url' => get_ru()
					), CL_USER) => t("Muuda"),
				$this->mk_my_orb("block_u", array("oid" => $o->id(), "post_ru" => get_ru())) => $o->prop('blocked') ? t("Blokeering maha") : t("Blokeeri"),
				$this->mk_my_orb("ob_cut", array("sel_u[".$o->id()."]" => 1, "post_ru" => get_ru())) => t("L&otilde;ika"),
				$this->mk_my_orb("ob_copy", array("sel_u[".$o->id()."]" => 1, "post_ru" => get_ru())) => t("Kopeeri"),
				$this->mk_my_orb("change", array(
						'id' => $o->id(),
						'return_url' => get_ru(),
						'group' => 'chpwd'
					), CL_USER) => t("Muuda parooli"),
				$this->mk_my_orb("change", array(
						'id' => $o->id(),
						'return_url' => get_ru(),
						'group' => 'stat'
					), CL_USER) => t("Vaata statistikat"),
			);

			$popup_menu = $this->get_user_popupmenu($o->id());
			$i = get_instance("core/trans/pot_scanner");

			$row = array(
				'username' => $popup_menu->get_menu(array(
					"text" => parse_obj_name($o->prop('uid')),
				)),
				"name" => html::obj_change_url($user_inst->get_person_for_user($o)),
				/*
				'name' => html::href(array(
					'caption' => strlen($o->prop('real_name')) ? $o->prop('real_name') : '('.t("nimetu").')',
					'url' => $this->mk_my_orb("change", array(
						'id' => $o->id(),
						'return_url' => get_ru()
					), CL_USER),
				)),
				*/
				'company' => join(', ', $companies),
				'mail' => $o->prop('email'),
				'last_active' => $o->prop('lastaction') ? date($df[2], $o->prop('lastaction')) : t("Pole kunagi sisse loginud"),
				"block" => html::hidden(array(
					"name" => "table_users[".$o->id()."][old_blocked]",
					"value" => $o->prop('blocked'),
				)).html::checkbox(array(
					"name" => "table_users[".$o->id()."][blocked]",
					"value" => 1,
					"checked" => $o->prop('blocked'),
				)),
				"if_lang" => html::select(array(
					"name" => "table_users[".$o->id()."][set_ui_lang]",
					"options" => array("" => "") + $i->get_langs(),
					"value" => $o->prop('ui_language'),
				)),
				"allow_rte" => html::checkbox(array(
					"name" => "table_users[".$o->id()."][allow_rte]",
					"value" => 1,
					"checked" => !$o->prop('rte_disabled'),
				)),
				'groups' => join(', ', $groups),
				'action' => $this->_get_menu(array(
					'id' => $o->id(),
					'items' => $items,
				)),
				'oid' => $o->id(),
				'cutcopied' => $ccp,
			);
			$table->define_data($row);
		}
	}

	// Defines and populates groups table
	function do_table_groups ($table, $arr)
	{
		if (!isset($this->parent))
		{
			return;
		}
		$do_loginmenus = false;
		if ($arr['obj_inst']->get_first_conn_by_reltype('RELTYPE_LOGIN_ROOT') && ($loginconf = $arr['obj_inst']->get_first_obj_by_reltype('RELTYPE_LOGIN_CONF')) )
		{
			$do_loginmenus = true;
			$lm = $loginconf->meta('lm');
			$users = get_instance("users");
		}
		$fields = array(
			/*
			array(
				'name' => 'action',
				'caption' => t("Tegevus"),
				'sortable' => false,
				'align' => 'center',
			),
			*/
			array(
				'name' => 'gid',
				'caption' => t("Grupi ID"),
				'numeric' => 1,
			),
			array(
				'name' => 'name',
				'caption' => t("Nimi"),
			),
			array(
				'name' => 'priority',
				'caption' => t("Prioriteet"),
			),
			array(
				'name' => 'type',
				'caption' => t("T&uuml;&uuml;p"),
			),
			array(
				'name' => 'aw',
				'caption' => t("AW"),
				'tooltip' => t("Lubada administreerimiskeskkonda"),
			),
			array(
				'name' => 'status',
				'caption' => t("Aktiivne"),
			),
			array(
				'name' => 'modified',
				'caption' => t("Muutmise aeg"),
			),
			array(
				'name' => 'modified_by',
				'caption' => t("Muutja"),
			),
			array(
				'name' => 'members',
				'caption' => t("Liikmeid"),
				'numeric' => 1,
			),
			array(
				'name' => 'rootfolders',
				'caption' => t("Juurkaustad"),
			),
			array(
				'name' => 'loginmenu',
				'caption' => t("Loginmen&uuml;&uuml;"),
			),
		);
		foreach ($fields as $f)
		{
			 // By default fields are sortable and aligned to right
			$f['sortable'] = isset($f['sortable']) ? $f['sortable'] : true;
			$f['align'] = isset($f['align']) ? $f['align'] : 'right';
			$f['chgbgcolor'] = 'cutcopied';
			$table->define_field($f);
		}
		$table->define_chooser(array(
			'field' => 'oid',
			'name' => 'sel_g',
			'chgbgcolor' => 'cutcopied',
		));

		if($this->can("view", $this->parent) && is_oid($this->parent))
		{
			$g = obj($this->parent);
			$title = isset($arr['title']) ? $arr['title'] : t("Grupi '%s' alamgrupid");
			$table->set_caption(sprintf($title, $g->name() ? $g->name() : '('.t("nimetu").' '.$g->id().')'));
		}
		$df = aw_ini_get('config.dateformats');


		// Now, find data for the table

		if (isset($arr['groups_list']))
		{
			foreach ($arr['groups_list'] as $g)
			{
				$target[$g] = obj($g);
			}
		}
		elseif($this->parent == "root")
		{
			foreach($arr["obj_inst"]->all_roots as $oid)
			{
				$target[] = obj($oid);
			}
			$table->set_caption("Juurkaustad/-grupid");
		}
		elseif($arr["request"]["search_button_pressed"])
		{
			$table->set_caption("Otsingutulemused");
			$r = $arr["request"];

			$ids = array();
			foreach($arr["obj_inst"]->all_roots as $oid)
			{
				$ot = new object_tree(array(
					'parent' => $oid,
					'class_id' => group_obj::CLID
				));
				$ids[] = $oid;
				$ids += $ot->ids();
			}
			if(!$r["search_usergroups"] && !$r["search_users_usergroups"] || !count($ids))
			{
				$target = array();
			}
			else
			{
				$ol_args = array(
					'class_id' => group_obj::CLID
				);

				if($r["ug_search_txt"])
				{
					$ol_args["name"] = "%".$r["ug_search_txt"]."%";
				}
				$mcond = array();
				if($r["search_users_usergroups"] == 2 || $r["search_users_usergroups"] == 1 & count($ids))
				{
					$cond = array(
						"oid" => $ids,
						"type" => group_obj::TYPE_DEFAULT,
					);
					if($r["search_users_usergroups"] == 2)
					{
						unset($cond["oid"]);
					}
					$mcond[] = new object_list_filter(array(
						"logic" => "AND",
						"conditions" => $cond,
					));
				}
				if($r["search_usergroups"] == 2 || $r["search_usergroups"] == 1 & count($ids))
				{
					$cond = array(
						"oid" => $ids,
						"type" => new obj_predicate_not(group_obj::TYPE_DEFAULT),
					);
					if($r["search_usergroups"] == 2)
					{
						unset($cond["oid"]);
					}
					$mcond[] = new object_list_filter(array(
						"logic" => "AND",
						"conditions" => $cond,
					));
				}
				if(count($mcond))
				{
					$ol_args[] = new object_list_filter(array(
						"logic" => "OR",
						"conditions" => $mcond,
					));
					$ol = new object_list($ol_args);
					$target = $ol->arr();
				}
				else
				{
					$target = array();
				}
			}
		}
		else
		{
			$ol = new object_list(array(
				'parent' => $this->parent,
				'class_id' => group_obj::CLID
			));
			$target = $ol->arr();
		}

		if (!count($target) && !isset($arr['title'])) // the title is the message.
		{
			//$table = "";
		}

		foreach ($target as $oid => $o)
		{
			// Check permissions
			if (!$this->can('view', $o->id()) || $o->class_id() != group_obj::CLID)
			{
				continue;
			}

			// Copypaste
			$ccp = (isset($_SESSION["copied_objects"][$o->id()]) || isset($_SESSION["cut_objects"][$o->id()]) ? "#E2E2DB" : "");

			// Find MEMBERS count
			$conns = $o->connections_from(array(
				'type' => 'RELTYPE_MEMBER',
				'class' => CL_USER,
			));
			$members = count($conns);

			// Find ROOTFOLDERS
			$rootfolders = array();
			$rootmenus = $o->prop('admin_rootmenu2');
			if (isset($rootmenus[aw_global_get('lang_id')]))
			{
				foreach ($rootmenus[aw_global_get('lang_id')] as $jrk => $menu)
				{
					$o_menu = obj($menu);
					$rootfolders[] = $o_menu->name();
				}
			}

			// Deletion url
			$delurl = $this->mk_my_orb("delete", array(
				"sel_g[".$o->id()."]" => "1",
				'post_ru' => get_ru(),
			));
			$delurl = "javascript:if(confirm('".t("Kustutada valitud objekt?")."')){window.location='$delurl';};";

			// Create links for Rootfolder and Objects popup items
			$html = $this->permissions_form . html::hidden(array(
					'name' => 'oid_rootf',
					'value' => $o->id(),
				));

			// More crapola to pick new rootfolders in a popup
			$url_rootfolder = "javascript:aw_popup_scroll('".
				$this->mk_my_orb("do_search", array(
					"id" => $arr["obj_inst"]->id(),
					"pn" => "rootmenu_".$o->id(),
					"clid" => CL_MENU,
					"append_html" => (str_replace(array("'","\n"),"",$html))
				), 'popup_search')
				."','Vali',550,500)";
			$this->_add_vars[] = "rootmenu_".$o->id();

			$html = $this->permissions_form . html::hidden(array(
					'name' => 'oid_objects',
					'value' => $o->id(),
				));
			$url_objects = "javascript:aw_popup_scroll('".$this->mk_my_orb("do_search", array(
						"id" => $arr["obj_inst"]->id(),
						"pn" => "table_groups",
						"clid" => 0, // Any class
						"append_html" => (((str_replace("&","%26",str_replace(array("'","\n"),"",($html)))))),
					), 'popup_search')."','".t("M&auml;&auml;ra &otilde;igused")."',550,500)";


			// Edit-menu items
			$items = array(
				$this->mk_my_orb("change", array(
						'id' => $o->id(),
						'return_url' => get_ru()
					), group_obj::CLID) => t("Muuda"),
				$delurl => t("Kustuta"),
				$this->mk_my_orb("ob_cut", array("sel_g[".$o->id()."]" => 1, "post_ru" => get_ru())) => t("L&otilde;ika"),
				$this->mk_my_orb("change", array(
						'id' => $o->id(),
						'group' => 'import',
						'return_url' => get_ru()
					), group_obj::CLID) => t("Impordi"),
				$url_rootfolder => t("Juurkaust"),
			//	$url_loginmenu => t("Login men&uuml;&uuml;"),
				$url_objects => t("Objektid"),
			);

			// Login menu selecter
			$loginmenu = "";
			if ($do_loginmenus) {

				$url_loginmenu = "javascript:aw_popup_scroll('".$this->mk_my_orb("popup_loginmenu", array(
						"id" => $arr["obj_inst"]->id(),
						"group" => $o->id(),
					), 'user_manager')."','".t("Vali loginmen&uuml;&uuml;")."',550,500)";

				// Edit-menu items
				$items[$url_loginmenu] = t("Loginmen&uuml;&uuml;");

				// Find value for table
				$gid = $o->prop("gid");
				if (isset($lm[$gid]) && isset($lm[$gid]['menu']) && is_oid($lm[$gid]['menu']))
				{
					$loginmenu = obj($lm[$gid]['menu']);
					$loginmenu = $loginmenu->name();
				}

			}

			// Define a table row
			$row = array(
				'gid' => $o->prop('gid'),
				'name' => html::href(array(
					'caption' => strlen($o->name()) ? $o->name() : '('.t("nimetu").' '.$o->id().')',
					'url' => aw_url_change_var(array("parent" => $o->id(), "search_button_pressed" => 0)),
				)),
				'priority' => html::textbox(array(
					'name' => 'priority['.$o->id().']',
					'size' => 6,
					'value' => $o->prop('priority'),
					'disabled' => $this->can('edit', $o->id()) ? false : true
				)),
				"type" => html::select(array(
					"name" => "type[".$o->id()."]",
					"options" => array(
						group_obj::TYPE_REGULAR => t('Tavaline'),
						group_obj::TYPE_DYNAMIC => t("D&uuml;naamiline"),
						group_obj::TYPE_DEFAULT => t("Kasutaja")
					),
					"value" => $o->type,
				)),
				'modified' => date($df[2], $o->prop('modified')),
				'modified_by' => $o->modifiedby(),
				'aw' => html::checkbox(array(
					"name" => "can_admin_interface[".$o->id()."]",
					"value" => 1,
					"checked" => $o->prop('can_admin_interface')
				)).html::hidden(array(
					"name" => "old_can_admin_interface[".$o->id()."]",
					"value" => "0".$o->prop('can_admin_interface')
				)),
				'status' => html::checkbox(array(
					"name" => "status[".$o->id()."]",
					"value" => object::STAT_ACTIVE,
					"checked" => $o->prop('status') == object::STAT_ACTIVE,
				)),
				'members' => $members,
				'rootfolders' => join(', ', $rootfolders),
				'action' => $this->_get_menu(array(
					'id' => $o->id(),
					'items' => $items,
				)),
				'oid' => $o->id(),
				'cutcopied' => $ccp,
				'loginmenu' => $loginmenu,
			);
			$table->define_data($row);
		}
	}

	// Returns current parent group/menu OID.
	// ?parent=<parent oid>
	function find_parent($manager)
	{
		$parent = $manager->prop('root');

		if (!$parent)
		{
			return null;
		}

		if (isset($_GET['parent']) && is_oid($_GET['parent']) && $this->can('view', $_GET['parent'])
			&& ($p = obj($_GET['parent'])) && in_array($p->class_id(), array(CL_MENU,group_obj::CLID)) )
		{
			if(in_array($_GET["parent"], $manager->prop("all_roots")))
			{
				$parent = $_GET['parent'];
			}
			else
			{
				foreach ($p->path() as $i => $ancestor)
				{
					if (in_array($ancestor->id(), $manager->prop("all_roots")))
					{
						$parent = $_GET['parent'];
						break;
					}
				}
			}
		}

		if(automatweb::$request->arg("parent") === "root")
		{
			$parent = "root";
		}
		if(automatweb::$request->arg("search_button_pressed"))
		{
			$parent = "search";
		}

		aw_session_set("user_management_last_parent", $parent);
		return $parent;
	}

	// Creates popup menu html
	/*
	 	Yanked from class_designer_manager, there should be something easier

		id - menu id (may be any oid)
		icon - url for icon file
	*/
	function _get_menu($arr)
	{
		if (!isset($arr['id']) || !is_oid($arr['id']) || !$this->can('view', $arr['id']) || !isset($arr['items']) || !is_array($arr["items"]))
		{
			return "";
		}
		$items = $arr['items'];

		$this->tpl_init("automatweb/menuedit");
		$this->read_template("js_popup_menu.tpl");

		$this->vars(array(
			"menu_id" => "menu-".$arr['id'],
			"menu_icon" => $this->cfg["baseurl"]."/automatweb/images/blue/obj_settings.gif",
		));

		$mi = "";
		foreach($items as $url => $txt)
		{
			$this->vars(array(
				"link" => $url,
				"text" => $txt
			));
			$mi .= $this->parse("MENU_ITEM");
		}

		$this->vars(array(
			"MENU_ITEM" => $mi
		));
		return $this->parse();
	}

	/** message handler for the MSG_POPUP_SEARCH_CHANGE message
	    used for linking root folders to groups and giving rights to objects
		warning : achtung : following code is an ugly fuck : achtung : warning
	**/
	function on_popup_search_change($arr)
	{
		$arr = $arr['arr'];
		if (isset($arr['oid_rootf']) && is_oid($arr['oid_rootf']) && $this->can('edit', $arr['oid_rootf']) )
		{
			$o = obj($arr['oid_rootf']);

			// First find rootmenu active values
			$m = $o->prop('admin_rootmenu2');
			$lang = aw_global_get('lang_id');
			if (!is_array(ifset($m,$lang)))
			{
				$m[$lang] = array();
			}


			// Create connections from group to objects
			foreach (safe_array(ifset($arr,'sel')) as $x => $id)
			{
				$o->connect(array(
					'to' => $id,
					'reltype' => 'RELTYPE_ADMIN_ROOT',
				));
				$m[$lang][] = $id;
			}
			$m[$lang] = array_unique($m[$lang]);

			// Update group rootmenu property
			$o->set_prop('admin_rootmenu2', $m);
			$o->save();

			$arr['oid_objects'] = $arr['oid_rootf']; // This enables next section to set permissions to rootmenus
		}

		if (isset($arr['oid_objects']) && is_oid($arr['oid_objects']) && $this->can('edit', $arr['oid_objects']) )
		{
			$o = obj($arr['oid_objects']);
			if ($o->class_id() != group_obj::CLID)
			{
				return;
			}
			$o_i = $o->instance();
 			$gid = $o->prop("gid");
			$a = $o_i->acl_list_acls();

			$acl = array();
			// Create ACL settings array
			foreach ($a as $a_bp => $a_name)
			{
				$acl[$a_name] = ifset($arr,'sel_'.$a_name);
			}
			// Create connections from selected objects to group
			foreach (safe_array(ifset($arr,'sel')) as $x => $id)
			{
				$s = obj($id);
				$s->connect(array(
					'to' => $o->id(),
					'reltype' => RELTYPE_ACL,
				));
				$s->save();
				$o_i->add_acl_group_to_obj($gid, $id, $acl);
			}
		}
	}

	/** Contents of loginmenu popup window
		@attrib name=popup_loginmenu

		@param id required type=int acl=view class_id=CL_USER_MANAGER
		@param group required type=int acl=edit class_id=CL_GROUP
		@param menu optional type=int acl=view class_id=CL_MENU
	**/
	function popup_loginmenu ($arr)
	{
		$manager = obj($arr['id']);
		$rootfolder = $manager->get_first_obj_by_reltype('RELTYPE_LOGIN_ROOT');
		if (!is_object($rootfolder))
		{
			return t("Seostamata menyyde juurikas");
		}

		if (isset($arr['menu']))
		{
			// Set menu to loginmenu for 'group'
			$conf = $manager->get_first_obj_by_reltype('RELTYPE_LOGIN_CONF');
			if (!is_object($conf))
			{
				return t("Seostamata confiobject");
			}
			// Create connection to menu
			if (!$conf->is_connected_to(array('to' => $arr['menu'])))
			{
				$conf->connect(array(
					'to' => $arr['menu'],
					'type' => 'RELTYPE_FOLDER',
				));
			}
			$lm = $conf->meta('lm');
			$go = obj($arr["group"]);
			$gid = $go->prop("gid");
			$lm[$gid] = array(
				'menu' => $arr['menu'],
				'pri' => 100, // Priority
			);

			$confinst = $conf->instance();
			$confinst->set_property(array(
				'obj_inst' => $conf,
				'prop' => array(
					'name' => 'login_menus',
				),
				'request' => array('lm' => $lm),
			));
			$conf->save();
			return '<script type="text/javascript">window.opener.location.reload(true);window.close(); </script>';
		}
		else
		{
			$url = $this->mk_my_orb("popup_loginmenu", array(
				"id" => $arr['id'],
				"group" => $arr['group'],
				"menu" => "%s",
			), 'user_manager');

			$return .= "<h2 class='user_manager_popup'>".t("Vali loginmen&uuml;&uuml;")."</h2>";

			$kids = new object_list(array(
				'parent' => $rootfolder->id(),
				'class_id' => CL_MENU
			));

			$return .= "<ul class='user_manager_popup'>";
			for ($k = $kids->begin(); !$kids->end(); $k = $kids->next())
			{
				$return .= "<li>".$k->name();

				$grandkids  = new object_list(array(
					'parent' => $k->id(),
					'class_id' => CL_MENU
				));

				$return .= "<ul>";
				for ($gk = $grandkids->begin(); !$grandkids->end(); $gk = $grandkids->next())
				{
					$return .= "<li>" . html::href(array(
						'url' => sprintf($url, $gk->id()),
						'caption' => $gk->name(),
					));
				}
				$return .= "</ul>";
			}
			$return .= "</ul>";
		}
		return $return;
	}

	/** When new group is created, this function assigns to it a login_menu
		@attrib on_create_group

		@param oid required type=int class_id=CL_GROUP
	**/
	function on_create_group ($arr)
	{
		$oid = $arr['oid'];
		if (!$this->can('view', $oid))
		{
			return;
		}
		// Find all user_manager objects
		$ol = new object_list(array(
			'class_id' => CL_USER_MANAGER,
			'status' => STAT_ACTIVE
		));
		$users = get_instance("users");
		for ($manager = $ol->begin(); !$ol->end(); $manager = $ol->next())
		{
			// Set menu to loginmenu for 'group'
			$conf = $manager->get_first_obj_by_reltype('RELTYPE_LOGIN_CONF');
			// Find the default menu
			$menu = $manager->prop('default_loginmenu');
			if (!$this->can('view', $manager->id()) || !is_object($conf) || !is_oid($menu))
			{
				continue;
			}

			// Create connection to menu
			if (!$conf->is_connected_to(array('to' => $menu)))
			{
				$conf->connect(array(
					'to' => $menu,
					'type' => 'RELTYPE_FOLDER',
				));
			}
			$lm = $conf->meta('lm');
			$go = obj($oid);
			$gid = $go->prop("gid");
			$lm[$gid] = array(
				'menu' => $menu,
				'pri' => 100, // Priority
			);

			$confinst = $conf->instance();
			$confinst->set_property(array(
				'obj_inst' => $conf,
				'prop' => array(
					'name' => 'login_menus',
				),
				'request' => array('lm' => $lm),
			));
			$conf->save();
			return;
		}
	}

	function get_user_popupmenu($oid)
	{
		$pm = get_instance("vcl/popup_menu");
		$pm->begin_menu($oid);
		$gl = aw_global_get("gidlist_oid");
		foreach($gl as $g_oid)
		{
			$o = obj($g_oid);

			if ($o->prop("type") == 1 || $o->prop("type") == 3)
			{
				continue;
			}
			if ($o->prop("priority") > $can_adm_max && $o->prop("if_acls_set"))
			{
				// all settings except can use admin depend on if_acls_set being true
				$dyc = $o->prop("editable_settings");
				$can_adm_max = $o->prop("priority");
			}
		}

		$u = obj($oid);
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
			if ($dyc && !$dyc[$gn])
			{
				continue;
			}
			$pm->add_item(array(
				"text" => $d["caption"],
				"link" => html::get_change_url($u->id(), array("group" => $gn, "return_url" => get_ru()))
			));
		}

		return $pm;
	}

	function init_packages_tbl($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->define_chooser(array(
			"field" => "oid",
			"name" => "sel",
		));
		$t->define_field(array(
			"name" => "priority",
			"caption" => t("Prioriteet"),
			"align" => "center",
			"sortable" => true,
			"sorting_field" => "priority_numeric",
		));
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"align" => "center",
			"sortable" => true,
			"sorting_field" => "name_string",
		));
		$t->define_field(array(
			"name" => "date_start",
			"caption" => t("Tellimisaja algus"),
			"align" => "center",
			"sortable" => true,
			"sorting_field" => "date_start_numeric"
		));
		$t->define_field(array(
			"name" => "date_end",
			"caption" => t("Tellimisaja l&otilde;pp"),
			"align" => "center",
			"sortable" => true,
			"sorting_field" => "date_end_numeric"
		));
		$t->define_field(array(
			"name" => "change",
			"caption" => t("Muuda"),
			"align" => "center",
		));
		$t->set_numeric_field(array("priority_numeric", "date_start_numeric", "date_end_numeric"));
		return $t;
	}

	function _get_packages_tbl($arr)
	{
		$t = $this->init_packages_tbl($arr);
		$odl = $this->get_content_packages();
		foreach($odl->arr() as $id => $od)
		{
			$date_edit = new date_edit();
			$date_edit->configure(array(
				"day" => "",
				"month" => "",
				"year" => "",
			));
			$t->define_data(array(
				"oid" => $id,
				"priority" => html::textbox(array(
					"name" => "packages_tbl[".$id."][priority]",
					"value" => $od["priority"],
					"size" => 15,
				)),
				"priority_numeric" => $od["priority"],
				"name" => html::textbox(array(
					"name" => "packages_tbl[".$id."][name]",
					"value" => $od["name"],
				)),
				"name_string" => $od["name"],
				"date_start" => $date_edit->gen_edit_form(
					"packages_tbl[".$id."][date_start]",
					$od["date_start"],
					$start = min(date("Y"), date("Y", $od["date_start"])),
					$start + 10
				),
				"date_start_numeric" => $od["date_start"],
				"date_end" => $date_edit->gen_edit_form(
					"packages_tbl[".$id."][date_end]",
					$od["date_end"],
					$start = min(date("Y"), date("Y", $od["date_end"])),
					$start + 10
				),
				"date_end_numeric" => $od["date_end"],
				"change" => html::obj_change_url($id),
			));
		}
		$t->set_default_sortby(array("priority", "name"));
		$t->set_default_sorder(array("priority" => "desc", "name" => "asc"));
	}

	function _get_packages_tlb($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->add_new_button(array(CL_CONTENT_PACKAGE), $arr["obj_inst"]->id());
		$t->add_save_button();
		$t->add_delete_button();
	}

	function _get_conditions_tlb($arr)
	{
		return get_instance(CL_CONTENT_PACKAGE)->_get_conditions_tlb($arr, true);
	}

	function _get_conditions_tree($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		foreach($this->get_content_packages()->arr() as $id => $od)
		{
			$t->add_item(0, array(
				"id" => (int)$id,
				"name" => $od["name"],
				"reload" => array(
					"layouts" => array("conditions_right"),
					"params" => array("contpack" => $id),
				),
			));
		}
		$t->set_selected_item((int)automatweb::$request->arg("contpack"));
	}

	function get_content_packages()
	{
		return new object_data_list(
			array(
				"class_id" => CL_CONTENT_PACKAGE
			),
			array(
				CL_CONTENT_PACKAGE => array("name", "priority", "date_start", "date_end"),
			)
		);
	}

	function _get_conditions_tbl($arr)
	{
		return get_instance(CL_CONTENT_PACKAGE)->_get_conditions_tbl($arr, true);
	}

	function _get_price_conditions_tbl($arr)
	{
		return get_instance(CL_CONTENT_PACKAGE)->_get_prices_tbl($arr, true);
	}

	function _get_price_conditions_tlb($arr)
	{
		return get_instance(CL_CONTENT_PACKAGE)->_get_prices_tlb($arr, true);
	}
}
