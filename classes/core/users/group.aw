<?php

/*

HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_DELETE, CL_GROUP, on_delete_grp)

HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_ALIAS_ADD_FROM, CL_GROUP, on_add_alias_to_group)

HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_ALIAS_DELETE_FROM, CL_GROUP, on_remove_alias_from_group)

HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_ALIAS_ADD_TO, CL_GROUP, on_add_alias_for_group)

HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_ALIAS_DELETE_TO, CL_GROUP, on_remove_alias_for_group)
*/

/*


@classinfo relationmgr=yes no_comment=1 no_status=1

@groupinfo dyn_search caption=Otsing submit=no
@groupinfo import caption=Import
@groupinfo objects caption="Objektid ja &Otilde;igused"
@groupinfo admin_rm caption="Admin rootmen&uuml;&uuml;"
@groupinfo img caption="Pilt"
@groupinfo if_acl caption="Liidese &otilde;igused"

@tableinfo groups index=oid master_table=objects master_index=oid

@default table=groups


@default group=general

	@property gid field=gid type=text
	@caption Grupi ID

	@property name field=name type=textbox table=objects
	@caption Nimi

	@property gp_name field=name type=hidden

	@property priority field=priority type=textbox size=15 warning=0
	@caption Prioriteet

	@property modified type=text table=objects field=modified
	@caption Muudetud

	@property mmodifiedby type=text store=no editonly=1
	@caption Kes muutis

	@property created type=text field=created table=objects
	@caption Loodud

	@property mcreatedby type=text store=no editonly=1
	@caption Kes l&otilde;i

	@property type type=select warning=0
	@caption T&uuml;&uuml;p

	@property search_form type=relpicker reltype=RELTYPE_SEARCHFORM
	@caption Otsinguvorm

	@property grp_frontpage type=callback callback=get_grp_frontpage field=meta method=serialize table=objects
	@caption Esileht

	@property require_change_pass type=checkbox ch_value=1 field=meta table=objects method=serialize
	@caption N&otilde;ua parooli vahetust esimesel logimisel

	@property for_not_logged_on_users type=checkbox ch_value=1 field=meta table=objects method=serialize
	@caption Sisselogimata kasutajatele

	@property default_acl type=callback callback=callback_get_default_acl store=no rel=1
	@caption Default ACL

@default group=dyn_search

	@property data type=text no_caption=1

@default group=import

	@property import type=fileupload store=no
	@caption Impordi kasutajaid

	@property import_desc type=text store=no

@default group=objects

	@property objects type=text store=no no_caption=1
	@caption Objektid

	@property obj_acl type=callback callback=get_acls store=no

	@property gp_parent type=hidden field=parent table=groups
	@property gp_gid type=hidden field=gid table=groups

@default group=admin_rm

	@property inherit_rm type=checkbox ch_value=1 field=meta method=serialize table=objects
	@caption P&auml;ri suurmen&uuml;&uuml; alumistele gruppidele

	@property admin_rootmenu2 type=callback callback=get_admin_rootmenus field=meta method=serialize table=objects
	@caption Administreerimisliidese juurkaust

@default group=img

	@property picture type=releditor reltype=RELTYPE_PICTURE rel_id=first props=file field=meta method=serialize table=objects
	@caption Pilt/foto

@default group=if_acl

	@property can_admin_interface type=checkbox ch_value=1 field=meta table=objects method=serialize
	@caption Kas saab administreerimiskeskkonda

	@property if_acls_set type=checkbox ch_value=1 field=meta table=objects method=serialize
	@caption Liidese &otilde;igused on piiratud

	@property can_quick_add type=checkbox ch_value=1 field=meta table=objects method=serialize
	@caption Kas saab kasutada kiirlisamist

	@property can_bm type=checkbox ch_value=1 field=meta table=objects method=serialize
	@caption Kas saab kasutada j&auml;rjehoidjat

	@property can_history type=checkbox ch_value=1 field=meta table=objects method=serialize
	@caption Kas saab kasutada ajalugu

	@property can_search type=checkbox ch_value=1 field=meta table=objects method=serialize
	@caption Kas saab kasutada otsingut

	@property default_yah_ct type=textbox field=meta table=objects method=serialize
	@caption Vaikimisi asukohariba

	@property disp_person type=checkbox ch_value=1 field=meta table=objects method=serialize
	@caption Kuva isiku muutmislinki

	@property disp_person_view type=checkbox ch_value=1 field=meta table=objects method=serialize
	@caption Kuva isiku vaatamislinki

	@property disp_person_text type=checkbox ch_value=1 field=meta table=objects method=serialize
	@caption Kuva ainult isiku nime

	@property disp_co_edit type=checkbox ch_value=1 field=meta table=objects method=serialize
	@caption Kuva organisatsiooni muutmislinki

	@property disp_co_view type=checkbox ch_value=1 field=meta table=objects method=serialize
	@caption Kuva organisatsiooni vaatamislinki

	@property disp_co_text type=checkbox ch_value=1 field=meta table=objects method=serialize
	@caption Kuva ainult organisatsiooni nime

	@property disp_object_type type=checkbox ch_value=1 field=meta table=objects method=serialize
	@caption Kuva objektit&uuml;&uuml;pi

	@property disp_object_link type=checkbox ch_value=1 field=meta table=objects method=serialize
	@caption Kuva objekti muutmislinki

	@property editable_settings type=select field=meta table=objects method=serialize multiple=1
	@caption Vali muudetavad seaded



@reltype SEARCHFORM value=1 clid=CL_FORM
@caption Otsinguvorm

@reltype MEMBER value=2 clid=CL_USER
@caption Liige

@reltype ADMIN_ROOT value=4 clid=CL_MENU
@caption Rootmen&uuml;&uuml;

@reltype ADD_TREE value=5 clid=CL_ADD_TREE_CONF
@caption Lisamise puu

@reltype PICTURE value=6 clid=CL_IMAGE
@caption Pilt

@reltype CFG_FORM value=7 clid=CL_CFGFORM
@caption Seadete vorm

@reltype BOOKMARK value=8 clid=CL_EXTLINK,CL_MENU
@caption Kohustuslik j&auml;rjehoidja

@reltype MEMBERSHIP value=9 clid=CL_GROUP_MEMBERSHIP
@caption Grupikuuluvus
*/

class group extends class_base
{
	function group()
	{
		$this->init(array(
			'tpldir' => 'core/users/group',
			'clid' => group_obj::CLID
		));
		$this->users = get_instance("users");
	}

	function get_property(&$arr)
	{
		$prop =& $arr["prop"];

		switch($prop['name'])
		{
			case "name":
				if ($arr["obj_inst"]->class_id() == CL_RELATION)
				{
					$c = new connection();
					list(, $c_d) = each($c->find(array("relobj_id" => $arr["obj_inst"]->id())));
					$c = new connection($c_d["id"]);

					if (!$this->can("admin", $c->prop("from")))
					{
						error::raise(array(
							"id" => "ERR_ACL",
							"msg" => sprintf(t("Teil ei ole &otilde;igust muuta objekti %s &otilde;igusi!"), $c->prop("from"))
						));
					}
					return PROP_IGNORE;
				}
				if ($prop["value"] == "" && $arr["obj_inst"]->name() != "")
				{
					$prop["value"] = $arr["obj_inst"]->name();
				}
				break;

			case "data":
				$f = get_instance(CL_FORM);
				$prop['value'] = $f->gen_preview(array(
					"id" => $arr["obj_inst"]->prop("search_form"),
					"entry_id" => $prop['value'],
					"extraids" => array(
						"group_id" => $arr["obj_inst"]->id(),
					),
					"tpl" => "show_noform.tpl"
				));
				break;

			case "modified";
				$prop['value'] = $this->time2date($prop['value'], 2);
				break;

			case "created";
				$prop['value'] = $this->time2date($prop['value'], 2);
				break;

			case "mcreatedby":
				$o = $arr["obj_inst"];
				$prop['value'] = $o->createdby();
				break;

			case "mmodifiedby":
				$o = $arr["obj_inst"];
				$prop['value'] = $o->modifiedby();
				break;

			case "type":
				$prop['options'] = array(
					group_obj::TYPE_REGULAR => t('Tavaline'),
					group_obj::TYPE_DYNAMIC => t("D&uuml;naamiline"),
					group_obj::TYPE_DEFAULT => t("Kasutaja")//XXX: kas peaks saama seda valida?
				);
				break;

			case "objects":
				$prop["value"] = $this->_get_objects($this->db_fetch_field("SELECT gid FROM groups WHERE oid = ".$arr["obj_inst"]->id(),"gid"));
				break;

			case "import_desc":
				$info = t('Kasutajate importimise faili formaat on j&auml;rgmine:
kasutajanimi,parool,nimi,e-post,aktiivne alates,aktiivne kuni
v&auml;ljad on eraldatud komadega, iga kasutaja on eraldi real
kuup&auml;evade formaadi <a href=\"http://www.gnu.org/software/tar/manual/html_node/tar_109.html\">t&auml;pne kirjeldus</a>
n&auml;ide:
kix,parool,Kristo Iila, kristo@struktuur.ee, 2003-09-17, 2005-09-17

v&auml;ljad nimi,email,aktiivne_alates, aktiivne kuni v&otilde;ib soovi korral &auml;ra j&auml;tta');

				$prop['value'] = nl2br($info);
				break;

			case "editable_settings":
				$o = obj(aw_global_get("uid_oid"));
				$prop["options"] = array();
				foreach($o->get_group_list() as $gid => $gd)
				{
					$prop["options"][$gid] = $gd["caption"];
				}
				break;
		}
		return PROP_OK;
	}

	function set_property(&$arr)
	{
		$prop =& $arr["prop"];
		$gid = $arr["obj_inst"]->prop("gid");

		if ($prop['name'] == 'data')
		{
			if ($this->can("view", $arr["obj_inst"]->prop("search_form")))
			{
				$f = get_instance(CL_FORM);
				$f->process_entry(array(
					"id" => $arr["obj_inst"]->prop("search_form"),
					"entry_id" => $arr["entry_id"]
				));
				$eid = $f->entry_id;

				$this->db_query("UPDATE groups SET data = '$eid' WHERE gid = '$gid'");
			}
		}
		else
		if ($prop['name'] == 'import')
		{
			global $import;
			$imp = $import;
			if (!is_uploaded_file($import))
			{
				return PROP_OK;
			}

			$us = get_instance(CL_USER);
			echo t("Impordin kasutajaid ... <br />");
			$first = true;
			$f = fopen($imp,"r");
			while(($row = fgetcsv($f, 10000,",")))
			{
				if ($first && $first_colheaders)
				{
					$first = false;
					continue;
				}

				$uid = $row[0];
				$pass = $row[1];
				$name = $row[2];
				$email = $row[3];
				$act_to = ($row[5] == "NULL" || $row[5] == "" ? -1 : strtotime($row[5]));
				$act_from = ($row[4] == "NULL" || $row[4] == "" ? -1 : strtotime($row[4]));

				$row = $this->db_fetch_row("SELECT uid,oid FROM users WHERE uid = '$uid'");
				if (!is_array($row))
				{
					$uo = $us->add_user(array(
						"uid" => $uid,
						"password" => $pass,
						"email" => $email,
						"real_name" => $name
					));
				}
				else
				{
					echo "kasutaja $uid ($name) on juba olemas, lisan ainult gruppi ja ei muuda parooli!<br>";
					if (is_oid($row["oid"]) && $this->can("view", $row["oid"]))
					{
						$uo = obj($row["oid"]);
					}
				}

				if ($uo)
				{
					// add to specified group
					$this->add_user_to_group($uo,$arr["obj_inst"]);
				}

				if ($act_from)
				{
					$uo->set_prop("act_from", $act_from);
				}

				if ($act_to)
				{
					$uo->set_prop("act_to", $act_to);
				}
				$uo->save();

				echo "Importisin kasutaja $uid ... <br />\n";
				flush();
				$first = false;
			}
		}
		else
		if ($prop['name'] == "obj_acl")
		{
			// read all acls from request and set them
			$ea = $arr["request"]["edit_acl"];
			if ($ea)
			{
				$a = $this->acl_list_acls();
				$acl = array();
				foreach($a as $a_bp => $a_name)
				{
					$acl[$a_name] = $arr["request"]["acl_".$a_bp];
				}
				$this->save_acl($ea, $gid, $acl);
			}
		}
		else
		if ($prop["name"] == "default_acl")
		{
			$this->_do_save_def_acl($arr);
		}

		return PROP_OK;
	}

	function _do_save_def_acl($arr)
	{
		$da = array();
		$aclids = aw_ini_get("acl.ids");
		foreach($aclids as $acln)
		{
			$da[$acln] = isset($arr["request"]["acl_{$acln}"]) ? $arr["request"]["acl_{$acln}"] : 0;
		}

		if ($arr["obj_inst"]->class_id() == CL_RELATION)
		{
			// FIXME: classbase will automatically give the connection as a parameter, but
			// currently we do this ourselves

			$c = new connection();
			list(, $c_d) = each($c->find(array("relobj_id" => $arr["obj_inst"]->id())));
			$c = new connection($c_d["id"]);

			// now set the real acl from the connection
			$grp = $c->to();
			$this->save_acl($c->prop("from"), $grp->prop("gp_gid"), $da);
		}
		else
		{
			$arr["obj_inst"]->set_meta("default_acl", $da);
		}
	}

	function callback_mod_retval(&$arr)
	{
		if (!empty($arr["request"]["edit_acl"]))
		{
			$arr["args"]["edit_acl"] = $arr["request"]["edit_acl"];
		}
	}

	function _get_objects($gid)
	{
		// now, get all the folders that have access set for these groups
		$dat = $this->acl_get_acls_for_groups(array("grps" => array($gid)));

		$t = $this->_init_obj_table(array(
			"exclude" => array("grp_name")
		));

		$ml = $this->get_menu_list();

		foreach($dat as $row)
		{
			if (!$this->can("view", $row["oid"]))
			{
				continue;
			}
			$o = obj($row['oid']);
			$row['obj_name'] = html::href(array(
				'url' => $this->mk_my_orb('change',array(
					'id' => $row['oid'],
					'return_url' => get_ru(),
				), $o->class_id()),
				'caption' => $row['obj_name'],
			));
			$row['obj_parent'] = $ml[$row['obj_parent']];
			$row["acl"] = html::href(array(
				"caption" => t("Muuda"),
				"url" => aw_url_change_var("edit_acl", $row["oid"])
			));
			$t->define_data($row);
		}
		$t->set_default_sortby("obj_name");
		$t->sort_by();
		return $t->draw(array(
			"has_pages" => true,
			"records_per_page" => 100,
			"pageselector" => "text"
		));
	}

	function &_init_obj_table($arr)
	{
		if (!isset($arr["exclude"]))
		{
			$arr["exclude"] = array();
		}
		extract($arr);

		load_vcl("table");
		$t = new aw_table(array("layout" => "generic"));

		if (!in_array("obj_name",$exclude))
		{
			$t->define_field(array(
				"name" => "obj_name",
				"caption" => t("Objekti Nimi"),
				"sortable" => 1,
			));
		}

		if (!in_array("obj_parent",$exclude))
		{
			$t->define_field(array(
				"name" => "obj_parent",
				"caption" => t("Objekti Asukoht"),
				"sortable" => 1,
			));
		}

		if (!in_array("grp_name",$exclude))
		{
			$t->define_field(array(
				"name" => "grp_name",
				"caption" => t("Grupi Nimi"),
				"sortable" => 1,
			));
		}

		if (!in_array("acl",$exclude))
		{
			$t->define_field(array(
				"name" => "acl",
				"caption" => t("&Otilde;igused"),
				"sortable" => 1,
			));
		}

		return $t;
	}

	function get_acls($arr)
	{
		$acls = array();

		if (!empty($arr["request"]["edit_acl"]))
		{
			$ea = $arr["request"]["edit_acl"];
			$o = obj($ea);
			$acls["acl_desc"] = array(
				'name' => "acl_desc",
				'type' => 'text',
				'store' => 'no',
				'group' => 'objects',
				'value' => sprintf(t('Muuda objekti %s &otilde;igusi'), $o->name())
			);
			$acls["edit_acl"] = array(
				'name' => "edit_acl",
				'type' => 'hidden',
				'store' => 'no',
				'value' => $ea
			);

			// get active acl
			$act_acl = $this->get_acl_for_oid_gid($ea, $arr["obj_inst"]->prop("gid"));

			$a = $this->acl_list_acls();
			foreach($a as $a_bp => $a_name)
			{
				$rt = "acl_".$a_bp;
				$acls[$rt] = array(
					'name' => $rt,
					'caption' => $a_name,
					'type' => 'checkbox',
					'ch_value' => 1,
					'store' => 'no',
					'group' => 'objects',
					'value' => $act_acl[$a_name]
				);
			}
		}

		return $acls;
	}

	function callback_pre_save($arr)
	{
		if (isset($arr["request"]["name"]))
		{
			$arr["obj_inst"]->set_name($arr["request"]["name"]);
		}
	}

	function callback_mod_tab($parm)
	{
		$id = $parm['id'];
		if ($id === 'dyn_search')
		{
			if ($parm['obj_inst']->prop("type") != group_obj::TYPE_DYNAMIC)
			{
				return false;
			}
		}
		return true;
	}

	function get_admin_rootmenus($arr)
	{
		$ret = array();
		$la = get_instance("languages");
		$ll = $la->get_list(array(
			"ignore_status" => true
		));

		$meta = $arr["obj_inst"]->meta();

		$ol = new object_list($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_ADMIN_ROOT")));
		$oopts = array("" => t("--vali--")) + $ol->names();

		foreach($ll as $lid => $lname)
		{
			$opts = $oopts;

			if (!is_array($meta["admin_rootmenu2"]))
			{
				$meta["admin_rootmenu2"] = array();
			}

			foreach((array) $meta["admin_rootmenu2"][$lid] as $k => $v)
			{
				if (!isset($opts[$v]) && $this->can("view", $v))
				{
					$o = obj($v);
					$opts[$v] = $o->name();
				}
			}

			$ret["admin_rootmenu2[$lid]"] = array(
				"name" => "admin_rootmenu2[$lid]",
				"type" => "relpicker",
				"group" => "settings",
				"table" => "objects",
				"field" => "meta",
				"method" => "serialize",
				"multiple" => 1,
				"caption" => sprintf(t("Administreerimisliidese juurkaust (%s)"), $lname),
				"value" => isset($meta["admin_rootmenu2"][$lid]) ? $meta["admin_rootmenu2"][$lid] : 0,
				"reltype" => "RELTYPE_ADMIN_ROOT",
				"options" => $opts
			);
		}
		return $ret;
	}

	function get_grp_frontpage($arr)
	{
		$ret = array();
		$la = get_instance("languages");
		$ll = $la->get_list();
		$meta = $arr["obj_inst"]->meta();

		if (!is_array($meta))
		{
			$meta = array();
		}

		foreach($ll as $lid => $lname)
		{
			$ret["grp_frontpage[$lid]"] = array(
				"name" => "grp_frontpage[$lid]",
				"type" => "relpicker",
				"group" => "settings",
				"table" => "objects",
				"field" => "meta",
				"method" => "serialize",
				"caption" => sprintf(t("Esileht (%s)"), $lname),
				"value" => isset($meta["grp_frontpage"]) ? (is_array($meta["grp_frontpage"]) ? $meta["grp_frontpage"][$lid] : $meta["grp_frontpage"]) : null,
				"reltype" => "RELTYPE_ADMIN_ROOT"
			);
		}
		return $ret;
	}

	function on_remove_alias_from_group($arr)
	{
		$uid_o = $arr["connection"]->to();
		$grp_o = $arr["connection"]->from();

		// delete all brothers from the current group
		$user_brothers = new object_list(array(
			"parent" => $grp_o->id(),
			"brother_of" => $uid_o->id()
		));
		$user_brothers->delete();

		// delete alias from user to this group
		if (count($uid_o->connections_from(array("to" => $grp_o->id()))) > 0)
		{
			$uid_o->disconnect(array(
				"from" => $grp_o->id()
			));
		}

		// get all subgroups
		$ot = new object_tree(array(
			"parent" => $grp_o->id(),
			"class_id" => group_obj::CLID
		));
		$ol = $ot->to_list();
		for($item = $ol->begin(); !$ol->end(); $item = $ol->next())
		{
			// remove all brothers from those groups
			$user_brothers = new object_list(array(
				"parent" => $item->id(),
				"brother_of" => $uid_o->id()
			));
			$user_brothers->delete();

			// remove all aliases from those groups
			foreach($item->connections_from(array("to" => $uid_o->id())) as $c)
			{
				$c->delete();
			}

			// also remove all aliases from user to the group
			if (count($uid_o->connections_from(array("to" => $item->id()))) > 0)
			{
				$uid_o->disconnect(array(
					"from" => $item->id()
				));
			}
		}
		cache::file_clear_pt("acl");
	}

	function on_add_alias_to_group($arr)
	{
		if($arr["connection"]->prop("reltype") == 2 && $arr["connection"]->prop("from.class_id") == CL_USER && aw_ini_get("users.use_group_membership") == 1)
		{
			// Selliseid krdi seoseid ei tohiks yldse luua!
			arr(debug_backtrace(), true);
		}

		if ($arr["connection"]->prop("reltype") == 2) //RELTYPE_MEMBER
		{
			$group = $arr["connection"]->from();
			$user = $arr["connection"]->to();

			// we must also add an alias to the user object pointing to this group
			$user->connect(array(
				"to" => $group->id(),
				"reltype" => "RELTYPE_GRP" // from user
			));

			// do our own sync here
			// add a brother below this group
			$user->create_brother($group->id());

			// go over all parent groups
			foreach($group->path() as $p_o)
			{
				if ($p_o->id() == $group->id())
				{
					continue;
				}

				if ($p_o->class_id() == group_obj::CLID)
				{

					// add a brother below all parent groups
					$user->create_brother($p_o->id());

					// add an alias to the user to all parent groups
					$p_o->connect(array(
						"to" => $user->id(),
						"reltype" => "RELTYPE_MEMBER",
					));

					// add a reverse alias to the user for all groups
					$user->connect(array(
						"to" => $p_o->id(),
						"reltype" => "RELTYPE_GRP" // from user
					));
				}
			}
		}
		cache::file_clear_pt("acl");
	}

	function on_delete_grp($arr)
	{
		extract($arr);

		// check if this is the user's default group and if so, block delete
		$g_o = obj($oid);
		if ($g_o->prop("type") == 1)
		{
			die(t("Kasutaja vaikimisi gruppi ei saa kustutada, palun kustutage kasutaja objekt!"));
		}
		cache::file_clear_pt("acl");
	}

	function callback_get_default_acl($arr)
	{
		$ret = array();

		if ($arr["obj_inst"]->class_id() != CL_RELATION)
		{
			$da = $arr["obj_inst"]->meta("default_acl");
		}
		else
		{
			// handle relation objects
			// FIXME: classbase will automatically give the connection as a parameter, but
			// currently we do this ourselves

			$c = new connection();
			$tmp = $c->find(array("relobj_id" => $arr["obj_inst"]->id()));
			list(, $c_d) = each($tmp);
			$c = new connection($c_d["id"]);


			// now get the real acl from the connection
			$grp = $c->to();
			$acld = $this->get_acl_for_oid_gid($c->prop("from"), $grp->prop("gp_gid"));
			$aclids = aw_ini_get("acl.ids");
			$da = array();
			foreach($aclids as $aclid)
			{
				$da[$aclid] = ($acld[$aclid] == aw_ini_get("acl.allowed") ? 1 : 0);
			}
		}

		$ret["acl_INFO_TEXT"] = array(
			"name" => "acl_INFO_TEXT",
			"no_caption" => 1,
			"type" => "text",
			"store" => "no",
			"value" => t("Default &otilde;igused seose loomisel")
		);

		$aclids = aw_ini_get("acl.ids");
		$aclns = aw_ini_get("acl.names");
		foreach($aclids as $acln)
		{
			$ret["acl_".$acln] = array(
				"name" => "acl_".$acln,
				"caption" => $aclns[$acln],
				"type" => "checkbox",
				"ch_value" => 1,
				"store" => "no",
				"value" => $da[$acln]
			);
		}

		return $ret;
	}

	function on_add_alias_for_group($arr)
	{
		if($arr["connection"]->prop("reltype") == 2  /*RELTYPE_MEMBER tmp fix*/ && $arr["connection"]->prop("from.class_id") == group_obj::CLID && aw_ini_get("users.use_group_membership") == 1)
		{//FIXME: siia ei satuta kunagi sest konstant RELTYPE_MEMBER ei eksisteeri ja reltype pole string 'RELTYPE_MEMBER'.
			// Selliseid krdi seoseid ei tohiks yldse luua!
			arr(debug_backtrace(), true);
		}

		if ($arr["connection"]->prop("reltype") == RELTYPE_ACL)
		{//FIXME: siia ei satuta kunagi sest konstant RELTYPE_ACL ei eksisteeri ja reltype pole string 'RELTYPE_ACL'.
			// handle acl add
			$from = $arr["connection"]->from();
			$grp = $arr["connection"]->to();
			$gid = $grp->prop("gp_gid");

			$this->add_acl_group_to_obj($gid, $from->id());
			$this->save_acl($from->id(), $gid, $grp->meta("default_acl"));
		}
	}

	function on_remove_alias_for_group($arr)
	{
		if ($arr["connection"]->prop("reltype") == RELTYPE_ACL)
		{//FIXME: siia ei satuta kunagi sest konstant RELTYPE_ACL ei eksisteeri ja reltype pole string 'RELTYPE_ACL'.
			// handle acl add
			$from = $arr["connection"]->from();
			$grp = $arr["connection"]->to();
			$this->remove_acl_group_from_obj($grp, $from->id());
		}
	}

	/** adds the user $user to group $group (storage objects)

		@attrib params=pos api=1
		@param user required type=object
			User object to be added into group
		@param group required type=object
			Group object to what the user will be added
		@param args optional type=array
			Array of arguments (start, end, brother_done)

		@comment
		Adds the $user to the $group.
	**/
	public static function add_user_to_group($user, $group, $arr = array())
	{
		// for each group in path from the to-add group
		foreach($group->path() as $p_o)
		{
			if ($p_o->class_id() != group_obj::CLID)
			{
				continue;
			}

			if(aw_ini_get("users.use_group_membership") == 1)
			{
				// I can't see why we need two membership objects with EXACTLY the same attributes.
				$ol_args = array(
					"class_id" => CL_GROUP_MEMBERSHIP,
					"status" => array(),	// If it's inactive, we'll activate it! ;)
					"parent" => array(),	// The parent doesn't make a difference here.
					"gms_user" => $user->id(),
					"gms_group" => $group->id(),
				);
				if(isset($arr["start"]) && isset($arr["end"]))
				{
					$ol_args["date_start"] = $arr["start"];
					$ol_args["date_end"] = $arr["end"];
				}
				else
				{
					$ol_args["membership_forever"] = 1;
				}
				$ol = new object_list($ol_args);
				if($ol->count() > 0)
				{
					$gms = $ol->begin();
					$gms->set_status(object::STAT_ACTIVE);
					$gms->save();
				}
				else
				{
					$gms = obj();
					$gms->set_class_id(CL_GROUP_MEMBERSHIP);
					$gms->set_parent($user->id());
					$gms->set_name(sprintf(t("%s kuulub gruppi %s"), $user->uid, $group->name));
					$gms->set_status(object::STAT_ACTIVE);
					$gms->gms_user = $user->id();
					$gms->gms_group = $group->id();
					if(isset($arr["start"]) && isset($arr["end"]))
					{
						$gms->date_start = $arr["start"];
						$gms->date_end = $arr["end"];
					}
					else
					{
						$gms->membership_forever = 1;
					}
					$gms->save();
				}
				$arr["brother_done"] = true;
			}
			else
			{
				// connection from user to group
				$user->connect(array(
					"to" => $p_o->id(),
					"reltype" => "RELTYPE_GRP",
				));

				// connection to group from user
				$p_o->connect(array(
					"to" => $user->id(),
					"reltype" => "RELTYPE_MEMBER",
				));
			}

			// brother under group
			if(empty($arr["brother_done"]))
			{
				$brother_id = $user->create_brother($p_o->id());
			}
		}

		cache::file_clear_pt("acl");
	}

	/** removes user $user from group $group

		@attrib params=pos api=1
		@param user required type=object
			User object to be removed from $group
		@param group required type=object
			The group object from where the user is removed

		@comment
		Removes the $user from $group.

	**/
	public static function remove_user_from_group($user, $group)
	{
		// delete all brothers from the current group
		$user_brothers = new object_list(array(
			"parent" => $group->id(),
			"brother_of" => $user->id()
		));
		$user_brothers->delete();

		$tmp = obj_set_opt("no_cache", 1);
		if(aw_ini_get("users.use_group_membership") == 1)
		{
			// Deactivate all valid membership objects for this group
			$ol = group_membership_obj::get_valid_memberships(array("group" => $group->id(), "user" => $user->id()));
			foreach($ol->arr() as $o)
			{
				$o->set_status(object::STAT_NOTACTIVE);
				$o->save();
			}
		}
		else
		// delete alias from user to this group
		if ($group->id() && count($user->connections_from(array("to" => $group->id()))) > 0)
		{
			// delete alias from user to this group
			if (count($user->connections_from(array("to" => $group->id()))) > 0)
			{
				$user->disconnect(array(
					"from" => $group->id()
				));
			}
		}
		obj_set_opt("no_cache", $tmp);

		// get all subgroups
		$ot = new object_tree(array(
			"parent" => $group->id(),
			"class_id" => group_obj::CLID
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

			if(aw_ini_get("users.use_group_membership") != 1)
			{
				// remove all aliases from those groups
				foreach($item->connections_from(array("to" => $user->id())) as $c)
				{
					$c->delete();
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
		if(aw_ini_get("users.use_group_membership") == 1 && $ol->count() > 0)
		{
			// Deactivate all valid membership objects for subgroups
			$ol = group_membership_obj::get_valid_memberships(array("group" => $ol->ids(), "user" => $user->id()));
			foreach($ol->arr() as $o)
			{
				$o->set_status(object::STAT_NOTACTIVE);
				$o->save();
			}
		}
		cache::file_clear_pt("acl");
	}

	/** Returns an array of user objects in the given group
		@attrib api=1
		@param group required type=object
	**/
	function get_group_members($g)
	{
		$ol = $g->get_group_members();
		return $ol->arr();
	}

	/** adds a group
		@attrib api=1 params=pos

		@param parent required type=oid
			The group object parent

		@param name required type=string
			The name of the group

		@param type required type=int
			The group type, one of the defined group type constants

		@param priority required type=int
			The group priority

		@returns
			the new group's oid

		@comment
			This is just a wrapper for object adding, nothing special is going on
	**/
	public static function add_group($parent, $name, $type, $priority)
	{
		$o = obj();
		$o->set_class_id(group_obj::CLID);
		$o->set_name($name);
		$o->set_status(STAT_ACTIVE);
		$o->set_parent($parent);
		$o->set_prop("name", $name);
		$o->set_prop("type", $type);
		$o->set_prop("priority", $priority);
		return $o->save();
	}

	/** returns a list of groups in the system
		@attrib api=1 params=name

		@param type optional type=array
			Array of group types to return

		@returns
			array { group oid => group name }
	**/
	function get_group_picker($arr)
	{
		$filt = array(
			"class_id" => group_obj::CLID
		);
		if ($arr["type"])
		{
			$filt["type"] = $arr["type"];
		}
		$ol = new object_list($filt);
		return $ol->names();
	}

	// DEPRECATED. use user_manager_obj::get_not_logged_in_group()
	static public function get_non_logged_in_group()
	{
		static $cache;
		if ($cache !== null)
		{
			return $cache;
		}

		$c = get_instance("config");

		if (!empty($_SESSION["non_logged_in_users_group_oid"]))
		{
			$nlg_oid = $_SESSION["non_logged_in_users_group_oid"];
		}
		else
		{
			$nlg_oid = $c->get_simple_config("non_logged_in_users_group_oid");
		}

		if (!$nlg_oid)
		{
			$nlg_gid = $c->get_simple_config("non_logged_in_users_group");
			if ($nlg_gid)
			{
				// convert to oid and store that
				$ol = new object_list(array(
					"class_id" => group_obj::CLID,
					"gid" => $nlg_gid
				));
				if ($ol->count())
				{
					$go = $ol->begin();
					$nlg_oid = $go->id();
					$c->set_simple_config("non_logged_in_users_group_oid", $nlg_oid);
				}
				else
				{
					throw awex_no_group(sprintf(t("could not find the group oid for gid %s"), $nlg_gid));
				}
			}
			else
			{
				// create nlg group
				$grpp = aw_ini_get("groups.tree_root");
				$nlg_o = obj(self::add_group($grpp, "Sisse logimata kasutajad", group_obj::TYPE_REGULAR, 1));
				$c->set_simple_config("non_logged_in_users_group_oid", $nlg_o->id());
				$nlg_oid = $nlg_o->id();
			}
		}
		$cache = $nlg_oid;
		$_SESSION["non_logged_in_users_group_oid"] = $nlg_oid;
		return $nlg_oid;
	}
}

class awex_no_group extends aw_exception {};
