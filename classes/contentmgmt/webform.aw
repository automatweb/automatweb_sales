<?php
// webform.aw - Veebivorm
/*

@classinfo relationmgr=yes no_status=1
@default table=objects

------------- general -------------

@groupinfo general_sub caption="&Uuml;ldine" parent=general
@groupinfo general_redir caption="Suunamine" parent=general

@default group=general_sub

@property name type=textbox rel=1 trans=1
@caption Nimi
@comment Objekti nimi

@default method=serialize
@default field=meta

@property comment type=textbox
@caption Kommentaar
@comment Vabas vormis tekst objekti kohta

@property on_init type=hidden newonly=1
@caption Initsialiseeri objekt

@property form_type type=select newonly=1
@caption Vormi t&uuml;&uuml;p

@property form_type_value type=text editonly=1
@caption Vormi t&uuml;&uuml;p

@property def_name type=textbox
@caption Saatja nimi

@property def_mail type=textbox
@caption Saatja e-mail

@property obj_name type=select multiple=1 size=3
@caption Millised sisestatud v&auml;&auml;rtused pannakse nimeks

@property error_location type=chooser multiple=1 default=0
@caption Kuva veateateid

@property show_confirm_page type=checkbox ch_value=1
@caption Kinnitusvaade?

@property confirm_page_template type=select
@caption Kinnitusvaate templeit

@property after_confirm_edit type=checkbox ch_value=1
@caption Peale kinnitusvaadet mine muutmisele

@property send_all_rows_to_mail type=checkbox ch_value=1
@caption Saada k&otilde;ik read meilile

@default group=general_redir

	@property redirect type=textbox
	@caption Kuhu suunata peale t&auml;tmist

	@property disp_after_entry type=select
	@caption L&otilde;puvaade template
	@comment Kui valitud, siis n&auml;idatakse l&otilde;puvaadet

	@property disp_after_entry_print type=select
	@caption L&otilde;pu printvaate template

@groupinfo send_mails caption="Saada meilid" parent=general
@default group=send_mails

	@property mails_tb type=toolbar no_caption=1

	@property mails_tbl type=table no_caption=1

	@property search_mail_info type=text

	@property search_mail_name type=textbox
	@caption Nimi

	@property search_mail_email type=textbox
	@caption E-mail

------------- end: general -------------


------------- form -------------
@groupinfo form caption="Vorm"

@property navtoolbar type=toolbar group=form no_caption=1
@caption Toolbar

@property form type=callback callback=callback_form group=form no_caption=1
@caption Vorm

@property subaction type=hidden store=no group=form,props
@caption Subaction

@property aliasmgr2 type=aliasmgr no_caption=1 store=no group=form
@caption Seostehaldur
------------- end: form -------------


------------- props -------------
@groupinfo props caption="Omadused" submit=no
@default group=props

@property availtoolbar type=toolbar no_caption=1
@caption Toolbar

@property props type=callback callback=callback_props no_caption=1
@caption Omadused
------------- end: props -------------

------------- styles -------------
@groupinfo styles caption="Stiilid"
@default group=styles

@property style_folder type=relpicker reltype=RELTYPE_STYLE_FOLDER
@caption Stiilide kaust

//@property form_output_style type=select
//@caption V&auml;ljundi tekstide stiil

@property def_caption_style type=select
@caption Vaikimisi pealkirja stiil

@property def_comment_style type=select
@caption Vaikimisi kommentaari stiil

@property def_prop_style type=select
@caption Vaikimisi elemendi stiil

@property def_form_style type=select
@caption Tabeli stiil

@property styles type=callback callback=callback_styles no_caption=1
@caption Stiilid
------------- end: styles -------------

------------- preview -------------
@groupinfo preview caption="Eelvaade" submit=no

@property preview type=callback callback=callback_preview group=preview no_caption=1
@caption Eelvaade
------------- end: preview -------------


@groupinfo show_entries caption="Sisestused" submit=no


------------- search -------------
@groupinfo search caption="Otsing" parent=show_entries submit_method=get submit=no
@default group=search

@property entries_toolbar type=toolbar group=search,entries no_caption=1
@caption Sisestuste toolbar

@property search type=text no_caption=1
@caption Otsing
------------- end: search -------------

------------- entries -------------
@groupinfo entries caption="N&auml;ita sisestusi" submit=no parent=show_entries

@property entries type=table group=entries no_caption=1
@caption Sisestused
------------- end: entries -------------




@groupinfo controllers caption="Kontrollerid" submit=no


------------- set_controllers -------------
@groupinfo set_controllers caption="Salvestamine" parent=controllers
@default group=set_controllers

@property set_controller_folder type=relpicker reltype=RELTYPE_CONTROLLER_FOLDER
@caption Kontrollerite kaust

@property submit_controllers type=callback callback=callback_submit_controllers no_caption=1
@caption Kontrollerid
------------- end: set_controllers -------------


------------- get_controllers -------------
@groupinfo get_controllers caption="N&auml;itamine" parent=controllers
@default group=get_controllers

@property get_controller_folder type=relpicker reltype=RELTYPE_CONTROLLER_FOLDER
@caption Kontrollerite kaust

@property view_controllers type=callback callback=callback_view_controllers no_caption=1
@caption Kontrollerid
------------- end: get_controllers -------------


@groupinfo transl caption=T&otilde;lgi
@default group=transl

	@property transl type=callback callback=callback_get_transl store=no
	@caption T&otilde;lgi

------------- relations -------------

@reltype METAMGR value=1 clid=CL_METAMGR
@caption Muutujate haldur

@reltype CFGFORM value=2 clid=CL_CFGFORM
@caption Seadete vorm

@reltype CONTROLLER value=3 clid=CL_CFGCONTROLLER
@caption Salvestamise kontroller

@reltype VIEWCONTROLLER value=4 clid=CL_CFG_VIEW_CONTROLLER
@caption N&auml;itamise kontroller

@reltype EMAIL value=5 clid=CL_ML_MEMBER
@caption Meiliaadress

@reltype OBJECT_TYPE value=6 clid=CL_OBJECT_TYPE
@caption Objekti t&uuml;&uuml;p

@reltype REGISTER value=7 clid=CL_REGISTER
@caption Register

@reltype STYLE value=8 clid=CL_CSS
@caption Stiil

@reltype STYLE_FOLDER value=9 clid=CL_MENU
@caption Stiilide kaust

@reltype CONTROLLER_FOLDER value=10 clid=CL_MENU
@caption Kontrollerite kaust

@reltype OBJECT_EXPORT value=11 clid=CL_OBJECT_EXPORT
@caption Objektide eksport

@reltype CAL_REG_FORM value=12 clid=CL_CALENDAR_REGISTRATION_FORM
@caption S&uuml;ndmuse vorm

@reltype CAL_REG_FORM_CONF value=13 clid=CL_CALENDAR_REGISTRATION_FORM_CONF
@caption S&uuml;ndmuse vormi konf

@reltype AFTER_SAVE_CONTROLLER value=14 clid=CL_CFGCONTROLLER
@caption p&auml;rast salvestamist kontroller

*/

class webform extends class_base
{
	function webform()
	{
		$this->init(array(
			"tpldir" => "contentmgmt/webform",
			"clid" => CL_WEBFORM,
		));
		// this stuff we won't translate
		$this->no_trans = array("submit", "reset", "text", "button");
		$this->n_props = array("checkboxes", "radiobuttons");
		$this->trans_names = array(
			"text" => t("Tekst"),
			"textbox" => t("V&auml;ike tekstikast"),
			"classificator" => t("Valikv&auml;li"),
			"date_select" => t("Kuup&auml;evavalik"),
			"checkbox"=> t("Liitu mailinglistiga"),
			"textarea" => t("Suur tekstikast"),
			"hidden" => t("Peidetud v&auml;li"),
			"submit" => t("Saada nupp"),
			"reset" => t("T&uuml;hista nupp"),
			"button" => t("Prindi nupp"),
			"releditor_im" => t("Pildi &uuml;leslaadimine"),
			"releditor_fl" => t("Faili &uuml;leslaadimine"),
		);
		$this->def_props = array(
			"firstname" => t("Eesnimi"),
			"lastname" => t("Perekonnanimi"),
			"co_name" => t("Organisatsioon"),
			"address" => t("Aadress"),
			"phone" => t("Telefon"),
			"fax" => t("Faks"),
			"email" => t("E-post"),
		);
		$this->form_types = array(
			CL_REGISTER_DATA => t("Tavaline vorm"),
			CL_CALENDAR_REGISTRATION_FORM => t("S&uuml;ndmusele registreerimine"),
			CL_SHOP_PRODUCT => t("Toode"),
		);
		$this->no_props = $this->make_keys(array("status", "name", "comment", "register_id", "person_id"));

		$this->trans_props = array(
			"redirect"
		);
	}

	function callback_on_load($arr)
	{
		$this->cfgform_i = get_instance(CL_CFGFORM);
		if(is_oid($arr["request"]["id"]) && $this->can("view", $arr["request"]["id"]))
		{
			$obj_inst = obj($arr["request"]["id"]);
			if($this->cfgform = $obj_inst->get_first_obj_by_reltype("RELTYPE_CFGFORM"))
			{
				$this->cfgform_i->_init_cfgform_data($this->cfgform);
			}
			if(!$arr["new"])
			{
				$form = $this->make_keys(array_keys($this->form_types));
				if(in_array($obj_inst->prop("form_type"), $form))
				{
					$this->p_clid = $form[$obj_inst->prop("form_type")];
				}
				else
				{
					$this->p_clid = CL_REGISTER_DATA;
				}
				if($obj_inst->prop("form_type") != $this->p_clid)
				{
					$obj_inst->set_prop("form_type", $this->p_clid);
					$obj_inst->save();
				}
			}
		}
	}

	function callback_mod_tab($arr)
	{
		if ($arr["id"] == "transl" && aw_ini_get("user_interface.content_trans") != 1)
		{
			return false;
		}
		if($arr["id"] == "show_entries")
		{
			if($arr["obj_inst"]->prop("form_type") == CL_REGISTER_DATA)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
	}

	function callback_get_transl($arr)
	{
		return $this->trans_callback($arr, $this->trans_props);
	}


	function callback_pre_save($arr)
	{
		if($this->cfgform)
		{
			$this->cfgform_i->callback_pre_save(array(
				"obj_inst" => $this->cfgform,
				"request" => array("subclass" => $this->p_clid),
			));
			$this->cfgform->save();
		}
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "error_location":
				$prop["options"] = array(
					0 => t("Elementide kohale"),
					1 => t("Vormi kohale"),
					2 => t("Vormi alla"),
				);
				break;

			case "form_type_value":
				$prop["value"] = $this->form_types[$arr["obj_inst"]->prop("form_type")];
				break;

			case "form_type":
				$prop["options"] = $this->form_types;
				break;

			case "search":
				$register = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_REGISTER");
				$s = get_instance(CL_REGISTER_SEARCH);
				$GLOBALS["search_butt"] = 1;
				$prop["value"] = $s->show(array(
					"id" => $register->prop("search_o"),
					"no_form" => 1,
				));
				break;

			case "confirm_page_template":
			//	$site_tpl_dir = $this->cfg['site_tpldir'];
			//	$admin_tpl_dir = $this->cfg['tpldir'];

				$options = array();
				if (is_dir($this->site_template_dir) === true)
				{
					$handle = opendir($this->site_template_dir);
					if ($handle !== false)
					{
						while (false !== ($file = readdir($handle)))
						{
							if (!is_dir($file) && $file{0} != '.')
							{
								$options[$file] = $file;
							}
						}
						closedir($handle);
					}
				}

				if (is_dir($this->adm_template_dir) === true)
				{
					$handle = opendir($this->adm_template_dir);
					if ($handle !== false)
					{
						while (false !== ($file = readdir($handle)))
						{
							if (!is_dir($file) && $file{0} != '.')
							{
								$options[$file] = $file;
							}
						}
						closedir($handle);
					}
				}

				$prop['options'] = $options;

				break;

			case "entries_toolbar":
				$this->entries_toolbar($arr);
				break;

			case "def_caption_style":
			case "def_comment_style":
			case "def_prop_style":
			case "def_form_style":
				$this->get_rel_props(array(
					"obj_inst" => $arr["obj_inst"],
					"prop" => "style",
				));
				$prop["options"] = array(0 => "-- Vali --") + $this->all_rels;
				break;

			case "obj_name":
				$prop["options"] = array("-- vali --");
				foreach(safe_array($this->cfgform_i->prplist) as $key => $val)
				{
					$prop["options"][$key] = $val["caption"];
				}
			case "def_name":
			case "def_mail":
			case "redirect":
				if($arr["obj_inst"]->prop("form_type") != CL_REGISTER_DATA)
				{
					return PROP_IGNORE;
				}
				break;

			case "entries":
				$register = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_REGISTER");
				$register_i = get_instance(CL_REGISTER);
				$register_i->do_data_tbl(array(
					"obj_inst" => $register,
					"prop" => &$arr["prop"],
					"request" => &$arr["request"],
				));
				break;

			case "search":
				$register = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_REGISTER");
				$s = get_instance(CL_REGISTER_SEARCH);
				$prop["value"] = $s->show(array(
					"id" => $register->prop("search_o"),
					"no_form" => 1,
				));
				break;

			case "navtoolbar":
				$tb = &$prop["vcl_inst"];

				$tb->add_button(array(
					"name" => "save",
					"tooltip" => t("Salvesta"),
					"action" => "",
					"img" => "save.gif",
				));

				$tb->add_button(array(
					"name" => "delete",
					"tooltip" => t("Kustuta valitud omadused"),
					"url" => "javascript:document.changeform.subaction.value='delete';submit_changeform();",
					"img" => "delete.gif",
					"confirm" => t("Oled kindel, et tahad antud omadused kustutada?"),
				));
				break;

			case "availtoolbar":
				$this->cfgform_i->gen_availtoolbar($arr);
				break;

			case "disp_after_entry":
			case "disp_after_entry_print":
				$prop["options"] = array("" => t("--vali--")) + $this->get_directory(array("dir" => aw_ini_get("site_tpldir")."/contentmgmt/webform/disp"));
				break;

			case "search_mail_info":
				$prop["value"] = t("Otsi aadressit");
				break;
			case "search_mail_name":
			case "search_mail_email":
				$prop["option_is_tuple"] = 1;
				$prop["autocomplete_source"] = $this->mk_my_orb($prop["name"] == "search_mail_name" ? "mail_name_autocomplete_source" : "mail_email_autocomplete_source");
				$prop["autocomplete_params"] = array($prop["name"]);
				$prop["onblur"] = "get_mail_field('".(($prop["name"]=="search_mail_name")?"search_mail_email": "search_mail_name")."', '".(($prop["name"]=="search_mail_email")?"search_mail_email": "search_mail_name")."')";
				break;

			case "mails_tb":
				$this->get_mails_tb($arr);
				break;

			case "mails_tbl":
				$this->get_mails_tbl($arr);
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
			case "transl":
				$this->trans_save($arr, $this->trans_props);
				break;

			case "on_init":
				if(!$arr["new"])
				{
					return PROP_IGNORE;
				}
				$this->_on_init($arr);
				break;

			case "view_controllers":
				if($this->cfgform)
				{
					$this->cfgform->set_meta("view_controllers", $arr["request"]["view_controllers"]);
				}
				break;

			case "submit_controllers":
				if($this->cfgform)
				{
					$this->cfgform->set_meta("controllers", $arr["request"]["controllers"]);
				}
				break;

			case "props":
				if($this->cfgform)
				{
					$this->add_new_properties($arr);
					if(($metamgr = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_METAMGR")) && ($object_type = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_OBJECT_TYPE")))
					{
						$classificator = $object_type->meta("classificator");
						foreach(safe_array($this->cfgform_i->cfg_proplist) as $key => $val)
						{
							if($this->cfgform_i->all_props[$key]["type"] == "classificator" && !$classificator[$key])
							{
								$no = obj();
								$no->set_class_id(CL_META);
								$no->set_status(STAT_ACTIVE);
								$no->set_parent($metamgr->id());
								$no->set_name($val["name"]);
								$no->save();
								$classificator[$key] = $no->id();
							}
						}
						$object_type->set_meta("classificator", $classificator);
						$object_type->save();
					}
				}
				break;

			case "form":
				if($this->cfgform)
				{
					$this->_save_form($arr);
				}
				break;
			case "entries":
				$awa = safe_array($arr["request"]["select"]);
				foreach($awa as $k => $v)
				{
					if ($k == $v)
					{
						$o = obj($k);
						$o->delete();
					}
				}
				break;
			case "styles":
				$arr["obj_inst"]->set_meta("xstyles", safe_array($arr["request"]["style"]));
				$arr["obj_inst"]->set_meta("m_styles", safe_array($arr["request"]["m_style"]));
				$arr["obj_inst"]->save();
				break;
			case "mails_tb":
				$this->set_mails_tb($arr);
				break;
		}
		return $retval;
	}

	function _save_form($arr)
	{
		if(!$object_type = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_OBJECT_TYPE"))
		{
			return PROP_IGNORE;
		}
		$classificator = $object_type->meta("classificator");
		$clf_type = $arr["request"]["clf_type"];
		$this->cfgform_i->save_layout(array(
			"obj_inst" => $this->cfgform,
			"request" => &$arr["request"],
		));
		$prplist = $this->cfgform_i->cfg_proplist;

		foreach(safe_array($arr["request"]["prp_opts"]) as $key => $val)
		{
			if($prplist[$key])
			{
				if (!isset($val["nextto"]))
				{
					$val["nextto"] = 0;
				}
				if (!isset($val["invisible"]))
				{
					$val["invisible"] = 0;
				}
				if (!isset($val["invisible_name"]))
				{
					$val["invisible_name"] = 0;
				}
				if (!isset($val["buttons"]))
				{
					$val["buttons"] = 0;
				}
				foreach($val as $key2 => $val2)
				{
					if($key2 == "defaultx" && is_array($val2))
					{
						$val2 = mktime(0, 0, 0, $val2["month"], $val2["day"], $val2["year"]);
					}
					$prplist[$key][$key2] = $val2;
				}

				$prplist[$key]["caption"] = (string) $arr["request"]["prpnames"][$key];
				$prplist[$key]["comment"] = (string) $arr["request"]["prpcomments"][$key];

				if (!empty($arr["request"]["prop_ord"][$key]))
				{
					$prplist[$key]["ord"] = (int) $arr["request"]["prop_ord"][$key];
				}
			}
		}

		if($arr["request"]["subaction"] == "delete")
		{
			$mark = $arr["request"]["mark"];
			foreach(safe_array($mark) as $pkey => $val)
			{
				if(is_oid($classificator[$pkey]) && $this->can("delete", $classificator[$pkey]))
				{
					$meta = obj($classificator[$pkey]);
					$meta->delete();
				}
				unset($classificator[$pkey]);
				unset($clf_type[$key]);
			}
		}

		foreach(safe_array($arr["request"]["prp_metas"]) as $key => $val)
		{
			$prps = explode(";", $val);
			if($prplist[$key] && !empty($prps) && is_oid($classificator[$key]) && $this->can("view", $classificator[$key]))
			{
				$e_prp_list = new object_list(array(
					"parent" => $classificator[$key],
					"class_id" => CL_META,
					"sort_by" => "objects.jrk DESC",
					//"status" => STAT_ACTIVE,
				));
				if($e_prp_list->count() > 0)
				{
					$high = $e_prp_list->begin();
					$highest = $high->ord() + 1;
				}
				else
				{
					$highest = 1;
				}
				$e_prps = $e_prp_list->names();

				foreach($prps as $prp)
				{
					$prp = trim($prp);
					if(in_array($prp, $e_prps) || empty($prp))
					{
						continue;
					}
					$e_prps[] = $prp;
					$no = obj();
					$no->set_class_id(CL_META);
					$no->set_status(STAT_ACTIVE);
					$no->set_parent($classificator[$key]);
					$no->set_name($prp);
					$no->set_ord($highest);
					$no->save();
					$highest++;
				}
			}
		}

		$object_type->set_meta("clf_type", $clf_type);
		$object_type->set_meta("classificator", $classificator);
		$object_type->save();
		$this->cfgform_i->cfg_proplist = $prplist;
	}

	function _on_init($arr)
	{
		$arr["obj_inst"]->save();
		$this->p_clid = $arr["request"]["form_type"];
		$form = obj();
		$parent = $this->p_clid == CL_SHOP_PRODUCT ? $arr["request"]["parent"] : $arr["obj_inst"]->id();
		$form->set_name($this->p_clid == CL_SHOP_PRODUCT ? $arr["request"]["name"] : $this->form_types[$this->p_clid]." ".$arr["obj_inst"]->id());
		$form->set_parent($parent);
		$form->set_class_id($this->p_clid);
		$form->set_status(STAT_ACTIVE);
		$form->save();
		$cfgform = obj();
		$cfgform->set_parent($parent);
		$cfgform->set_class_id(CL_CFGFORM);
		$cfgform->set_name("Seadete vorm ".$arr["obj_inst"]->id());
		$cfgform->set_status(STAT_ACTIVE);
		$cfgform->save();
		// well, seems that this is the only way -- ahz
		$this->cfgform_i->set_property(array(
			"new" => 1,
			"prop" => array("name" => "subclass"),
			"request" => array("subclass" => $this->p_clid),
		));
		// so, we to reverse the property adding of cfgform also -- ahz
		$cfgform->set_prop("subclass", $this->p_clid);
		$cfgform->set_meta("cfg_groups", array("data" => array("caption" => t("Andmed"))));
		$cfgform->save();
		$arr["obj_inst"]->connect(array(
			"to" => $cfgform->id(),
			"reltype" => "RELTYPE_CFGFORM",
		));

		$object_type = obj();
		$object_type->set_parent($parent);
		$object_type->set_class_id(CL_OBJECT_TYPE);
		$object_type->set_name(t("Objekti t&uuml;&uuml;p ").$arr["obj_inst"]->id());
		$object_type->set_status(STAT_ACTIVE);
		$object_type->set_prop("use_cfgform", $cfgform->id());
		$object_type->set_prop("type", $this->p_clid);
		$object_type->save();
		$arr["obj_inst"]->connect(array(
			"to" => $object_type->id(),
			"reltype" => "RELTYPE_OBJECT_TYPE",
		));
		$object_type->connect(array(
			"to" => $cfgform->id(),
			"reltype" => "RELTYPE_OBJECT_CFGFORM",
		));

		if($this->p_clid == CL_SHOP_PRODUCT)
		{
			return;
		}
		$metamgr = obj();
		$metamgr->set_parent($arr["obj_inst"]->id());
		$metamgr->set_class_id(CL_METAMGR);
		$metamgr->set_name("Muutujate haldus ".$arr["obj_inst"]->id());
		$metamgr->set_status(STAT_ACTIVE);
		$metamgr->save();
		$arr["obj_inst"]->connect(array(
			"to" => $metamgr->id(),
			"reltype" => "RELTYPE_METAMGR",
		));
		if($this->p_clid == CL_REGISTER_DATA)
		{
			$group = obj(group::get_non_logged_in_group());
			$dir = obj();
			$dir->set_parent($arr["obj_inst"]->parent());
			$dir->set_class_id(CL_MENU);
			$dir->set_name("Sisestused ".$arr["obj_inst"]->id());
			$dir->save();
			$dir->acl_set($group, array("can_add" => 1, "can_view" => 1));

			$register = obj();
			$register->set_parent($arr["obj_inst"]->parent());
			$register->set_class_id(CL_REGISTER);
			$register->set_name("Register ".$arr["obj_inst"]->id());
			$register->set_status(STAT_ACTIVE);
			$register->set_prop("data_cfgform", $cfgform->id());
			$register->set_prop("default_cfgform", 1);
			$register->set_prop("data_rootmenu", $dir->id());
			$register->set_prop("show_all", 1);
			$register->set_prop("per_page", 100);
			$register->save();
			$arr["obj_inst"]->connect(array(
				"to" => $register->id(),
				"reltype" => "RELTYPE_REGISTER",
			));
			$form->connect(array(
				"to" => $register->id(),
				"reltype" => "RELTYPE_REGISTER",
			));
			$register->connect(array(
				"to" => $dir->id(),
				"reltype" => "RELTYPE_MENU",
			));
			$register->connect(array(
				"to" => $cfgform->id(),
				"reltype" => "RELTYPE_CFGFORM",
			));

			$register_search = obj();
			$register_search->set_parent($register->id());
			$register_search->set_class_id(CL_REGISTER_SEARCH);
			$register_search->set_status(STAT_ACTIVE);
			$register_search->set_name("Registri otsing ".$arr["obj_inst"]->id());
			$register_search->set_prop("per_page", 100);
			$register_search->set_prop("register", $register->id());
			$register_search->prop("show_all_in_empty_search", 1);
			$register_search->prop("show_date", 1);
			$register_search->save();
			$register_search->connect(array(
				"to" => $register->id(),
				"reltype" => "RELTYPE_REGISTER",
			));
			$register->set_prop("search_o", $register_search->id());
			$register->save();
			$register->connect(array(
				"to" => $register_search->id(),
				"reltype" => "RELTYPE_SEARCH",
			));

			$object_export = obj();
			$object_export->set_class_id(CL_OBJECT_EXPORT);
			$object_export->set_parent($arr["obj_inst"]->id());
			$object_export->set_status(STAT_ACTIVE);
			$object_export->set_name("Objekti eksport ".$arr["obj_inst"]->id());
			$object_export->set_prop("object_type", $object_type->id());
			$object_export->set_prop("root_folder", $dir->id());
			$object_export->set_prop("csv_separator", ",");
			$object_export->save();
			$arr["obj_inst"]->connect(array(
				"to" => $object_export->id(),
				"reltype" => "RELTYPE_OBJECT_EXPORT",
			));
			$object_export->connect(array(
				"to" => $dir->id(),
				"reltype" => "RELTYPE_FOLDER",
			));
			$object_export->connect(array(
				"to" => $object_type->id(),
				"reltype" => "RELTYPE_OBJECT_TYPE",
			));
		}

// hopefully temporary
		// have to put it here right now - in the future it should work that t() way
		// so lets create an array where i hold those error messages in both languages
		$error_messages = array(
			"element_has_to_be_filled" => t("%caption peab olema t&auml;idetud"),
			"element_has_to_be_selected" => t("%caption peab olema valitud"),
			"email_address_not_correct" => t("%caption sisestatud e-mailiaadress pole korrektne"),
		);
		// now, lets get what language is used
		$languages_inst = get_instance("languages");
		$lang_data = $languages_inst->fetch(aw_global_get("lang_id"));
// end of hopefully temporary

		$set_controllers = array(
			array(
				"name" => t("M&auml;&auml;ra saaja aadressiks"),
				"formula" => 'if (is_email($prop["value"])) {$vals = aw_global_get("receivers_name");$vals[$prop["value"]] = $prop["value"]; aw_global_set("receivers_name", $vals);} else { $value = is_array($prop["value"]) ? $prop["value"] : array($prop["value"]);$vals = aw_global_get("receivers_name");foreach($value as $val){if(is_oid($val) && $this->can("view", $val)){$obj = obj($val);$vals[$val] = $obj->comment();}}if(!empty($vals)){aw_global_set("receivers_name", $vals);}}',
			),
			array(
				"name" => t("M&auml;&auml;ra saatja aadressiks"),
				"formula" => 'aw_global_set("global_name", $prop["value"]);',
			),
			array(
				"name" => t("*elemendinimi* peab olema t&auml;idetud"),
				"formula" => 'if($prop["value"] == ""){$retval = PROP_ERROR;} if (isset($_FILES[$prop["name"]]) && $_FILES[$prop["name"]]["error"]["file"] != UPLOAD_ERR_OK) { $retval = PROP_ERROR; }',
//				"errmsg" => t("%caption peab olema t&auml;idetud"),
				"errmsg" => $error_messages['element_has_to_be_filled'],
			),
			array(
				"name" => t("*elemendinimi* peab olema valitud"),
				"formula" => 'if(empty($prop["value"])){$retval = PROP_ERROR;}',
//				"errmsg" => t("%caption peab olema valitud"),
				"errmsg" => $error_messages['element_has_to_be_selected'],
			),
			array(
				"name" => t("Kontrolli e-maili &otilde;igsust"),
				"formula" => 'if(!is_email($prop["value"])){$retval = PROP_ERROR;}',
//				"errmsg" => t("%caption sisestatud e-mailiaadress pole korrektne"),
				"errmsg" => $error_messages['email_address_not_correct'],
			),
			array(
				"name" => t("Kuva sisestaja IP ja host aadress"),
				"formula" => 'if(empty($prop["value"])){$request[$prop["name"]] = "IP: ".$_SERVER["REMOTE_ADDR"];}',
			),
			array(
				"name" => t("Sisesta dokumendi pealkiri"),
				"formula" => 'if(empty($prop["value"])){if(is_oid($request["doc_id"]) && $this->can("view", $request["doc_id"])){$doc = obj($request["doc_id"]);$request[$prop["name"]] = $doc->name();}}',
			),
			//Host: ".$_SERVER["REMOTE_HOST"]."\n
		);
		$get_controllers = array(
			array(
				"name" => t("Kuva sisestaja IP ja host aadress"),
				"formula" => '$value = $arr["obj_inst"]->prop($prop["name"]);if(!empty($value)){$prop["type"] = "text";$prop["value"] = nl2br($value);}',
			),
			array(
				"name" => t("Sisesta dokumendi pealkiri"),
				"formula" => '$value = $arr["obj_inst"]->prop($prop["name"]);if(!empty($value)){$prop["type"] = "text";$prop["value"] = nl2br($value);}',
			),
			array(
				"name" => t("Sisselogitud isiku eesnimi"),
				"formula" => '$u = get_instance("core/users/user"); $p = obj($u->get_current_person()); $prop["value"] = $p->prop("firstname");',
			),
			array(
				"name" => t("Sisselogitud isiku perekonnanimi"),
				"formula" => '$u = get_instance("core/users/user"); $p = obj($u->get_current_person()); $prop["value"] = $p->prop("lastname");',
			),
		);
		$i = 0;
		foreach($set_controllers as $key => $val)
		{
			$controller = obj();
			$controller->set_class_id(CL_CFGCONTROLLER);
			$controller->set_parent($arr["obj_inst"]->id());
			$controller->set_name($val["name"]." (".$arr["obj_inst"]->name().")");
			$controller->set_prop("formula", $val["formula"]);
			$controller->set_prop("errmsg", $val["errmsg"]);
			$controller->save();
			$arr["obj_inst"]->connect(array(
				"to" => $controller->id(),
				"reltype" => "RELTYPE_CONTROLLER",
			));
			$i++;
		}
		$i = 0;
		foreach($get_controllers as $key => $val)
		{
			$controller = obj();
			$controller->set_class_id(CL_CFG_VIEW_CONTROLLER);
			$controller->set_parent($arr["obj_inst"]->id());
			$controller->set_name($val["name"]." (".$arr["obj_inst"]->name().")");
			$controller->set_prop("formula", $val["formula"]);
			$controller->save();
			$arr["obj_inst"]->connect(array(
				"to" => $controller->id(),
				"reltype" => "RELTYPE_VIEWCONTROLLER",
			));
			$i++;
		}
	}

	function entries_toolbar($arr)
	{
		$register = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_REGISTER");
		$object_export = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_OBJECT_EXPORT");
		$register_search = $register->prop("search_o");
		$tb = $arr["prop"]["vcl_inst"];
		$tb->add_button(array(
			"name" => "search",
			"tooltip" => t("Otsi"),
			"url" => html::get_change_url($arr["obj_inst"]->id(), array(
				"group" => "search",
			)),
			"img" => "search.gif",
		));
		$tb->add_button(array(
			"name" => "change",
			"tooltip" => t("Muuda otsingu seadeid"),
			"url" => html::get_change_url($register->prop("search_o"), array(
				"return_url" => html::get_change_url($arr["obj_inst"]->id(), array(
					"group" => $arr["request"]["group"],
				)),
			)),
			"img" => "../blue/obj_settings.gif",
		));
		$tb->add_button(array(
			"name" => "delete",
			"tooltip" => t("Kustuta valitud sisestused"),
			"action" => "remove_entries",
			"img" => "delete.gif",
			"confirm" => t("Oled kindel, et tahad valitud sisestused kustutada?"),
		));
		$tb->add_button(array(
			"name" => "export",
			"tooltip" => t("Ekspordi objektid"),
			"url" => html::get_change_url($object_export->id(), array(
				"group" => "mktbl",
				"return_url" => html::get_change_url($arr["obj_inst"]->id(), array(
					"group" => $arr["request"]["group"],
				)),
			)),
			"img" => "ftype_xls.gif",
		));
	}

	function callback_props($arr)
	{
		$prplist = safe_array($this->cfgform_i->prplist);
		$this->read_template("avail_props.tpl");
		$prop_order = array(
			"textbox" => 0,
			"textarea" => 1,
			"text" => 2,
			"classificator" => 3,
			"hidden" => 4,
			"date_select" => 5,
			"checkbox" => 6,
			"submit" => 7,
			"reset" => 8,
			"button" => 9,
			"releditor_im" => 10,
			"releditor_fl" => 11
		);
		$def_props = array();
		if($this->p_clid == CL_CALENDAR_REGISTRATION_FORM)
		{
			$def_props = $this->def_props;
		}
		$prp_count = array();
		$c_props = $def_props + $this->no_props;
		foreach($prplist as $key => $prop)
		{
			if(!array_key_exists($key, $c_props))
			{
				// we skip the checkboxes
				if($prp_count[$prop["type"]] == 1 && $prop["type"] === "checkbox")
				{
					continue;
				}

				if ($prop["type"] === "releditor" && substr($prop["name"], 0, 6) === "userim")
				{
					$prp_count[$prop["type"]."_im"]++;
				}
				elseif ($prop["type"] === "releditor" && substr($prop["name"], 0, 8) === "userfile")
				{
					$prp_count[$prop["type"]."_fl"]++;
				}
				else
				{
					$prp_count[$prop["type"]]++;
				}
			}
		}
		$show_props = array();
		$ext_count = array();
		// these props won't go to heaven
		foreach($this->cfgform_i->all_props as $key => $prop)
		{
			if ($prop["type"] === "releditor" && substr($prop["name"], 0, 6) === "userim")
			{
				$prop["type"] = "releditor_im";
			}
			elseif ($prop["type"] === "releditor" && substr($prop["name"], 0, 8) === "userfile")
			{
				$prop["type"] = "releditor_fl";
			}

			if(!array_key_exists($prop["type"], $prop_order))
			{
				continue;
			}

			if(!in_array($prop_order[$prop["type"]], $show_props) && !array_key_exists($key, $c_props))
			{
				$show_props[$prop_order[$prop["type"]]] = $prop;
			}

			if(!array_key_exists($key, $c_props))
			{
				if($ext_count[$prop["type"]] == 1 && $prop["type"] === "checkbox")
				{
					continue;
				}

				$ext_count[$prop["type"]]++;
			}
		}

		$sc = "";
		$vrs = array();
		$cnt = 0;

		foreach($def_props as $key => $prop)
		{
			if(!array_key_exists($key, $prplist))
			{
				$this->vars(array(
					"prp_name" => $prop,
					"prp_key" => $key,
				));
				$sc .= $this->parse("def_prop");
				$cnt++;
			}
		}
		$vrs["def_prop"] = $sc;
		if($cnt > 0)
		{
			$vrs["d_prp"] = $this->parse("d_prp");
		}
		if(!empty($show_props))
		{
			$this->vars(array(
				"type_cap" => t("T&uuml;&uuml;p"),
				"el_count_cap" => t("Mitu elementi"),
				"usage_cap" => t("Kasutus"),
			));
			$vrs["av_props"] = $this->parse("av_props");
		}
		$sc = "";
		ksort($show_props);

		foreach($show_props as $prop)
		{
			$this->vars(array(
				"prp_name" => $this->trans_names[$prop["type"]],
				"prp_type" => $prop["type"],
				"prp_used" => (int)$prp_count[$prop["type"]],
				"prp_unused" => ((int)$ext_count[$prop["type"]] - (int)$prp_count[$prop["type"]]),
				"used_cap" => t("kasutusel"),
				"unused_cap" => t("alles"),
			));
			$sc .= $this->parse("avail_property");
		}
		$this->vars(array(
			"avail_property" => $sc,
		) + $vrs);
		$this->vars(array(
			"avail" => $this->parse("avail"),
		));
		$item["value"] = $this->parse();
		return array($item);
	}

	function add_new_properties($arr)
	{
		$target = $arr["request"]["target"];
		// first check, whether a group with that id exists
		$_tgt = $this->cfgform->meta("cfg_groups");
		if (isset($_tgt[$target]))
		{
			$this->cfgform_i->_init_cfgform_data($this->cfgform);
			// and now I just have to modify the proplist, eh?
			$prplist = $this->cfgform_i->prplist;
			$prp_count = array();
			$prplist = safe_array($this->cfgform_i->prplist);
			$highest = 0;
			foreach($prplist as $key => $prop)
			{
				$prp_count[$prop["type"]]++;
				if($prop["ord"] > $highest)
				{
					$highest = $prop["ord"];
				}
			}
			$ext_count = array();
			foreach($this->cfgform_i->all_props as $key => $prop)
			{
				if ($prop["type"] == "releditor" && substr($prop["name"], 0, 6) == "userim")
				{
					$prop["type"] = "releditor_im";
				}
				else
				if ($prop["type"] == "releditor" && substr($prop["name"], 0, 8) == "userfile")
				{
					$prop["type"] = "releditor_fl";
				}

				$ext_count[$prop["type"]]++;
			}
			$mark = $arr["request"]["mark"];
			foreach(safe_array($mark) as $pkey => $pval)
			{
				if(array_key_exists($pkey, $this->cfgform_i->all_props))
				{
					$prplist[$pkey] = array(
						"name" => $pkey,
						"ord" => $highest++,
						"caption" => $this->def_props[$pkey],
						"group" => $target,
						"type" => $this->cfgform_i->all_props[$pkey]["type"],
					);
					continue;
				}
				$count = (int)$ext_count[$pkey] - (int)$prp_count[$pkey];
				$pval = (int)$pval;

				$old_pk = $pkey;
				$pkey = $pkey == "releditor_fl" ? "releditor" : $pkey == "releditor_im" ? "releditor" : $pkey;

				if($count > 0 && !empty($pval))
				{
					// now, lets count the real ammount of thing we'll add
					$pval = $pval > $count ? $count : $pval;
					for($c = $pval; $c > 0; $c--)
					{
						foreach($this->cfgform_i->all_props as $key => $val)
						{
							if($c <= 0)
							{
								break;
							}
							if(in_array($key, $this->no_props))
							{
								continue;
							}
							if(!array_key_exists($key, $prplist) && $val["type"] == $pkey)
							{
								if ($old_pk == "releditor_fl" && substr($val["name"], 0, 8) != "userfile")
								{
									continue;
								}
								if ($old_pk == "releditor_im" && substr($val["name"], 0, 6) != "userim")
								{
									continue;
								}
								$prplist[$key] = array(
									"name" => $key,
									"ord" => $highest++,
									//"caption" => $this->all_props[$pkey]["caption"],
									"group" => $target,
									"type" => $val["type"],
								);
								$c--;
							}
						}
					}
				}
			}
			$this->cfgform_i->cfg_proplist = $prplist;
		}
	}

	function callback_form($arr)
	{
		$this->read_template("layout.tpl");
		$used_props = $by_group = array();

		if (is_array($this->cfgform_i->grplist))
		{
			$this->cfgform_i->sort_grplist();
			foreach($this->cfgform_i->grplist as $key => $val)
			{
				if (!is_numeric($key))
				{
					$by_group[$key] = array();
				}
			}
		}
		if (is_array($this->cfgform_i->prplist))
		{
			foreach($this->cfgform_i->prplist as $property)
			{
				if (!empty($property["group"]))
				{
					if (!is_array($property["group"]))
					{
						$by_group[$property["group"]][] = $property;
					}
					else
					{
						list(,$first) = each($property["group"]);
						$by_group[$first][] = $property;
					}
				}
			}
		}
		$c = "";
		$cnt = 0;

		// lotsa needed options for various things
		$capt_opts = array(
			0 => "Vasakul",
			"right" => "Paremal",
			"top" => "Peal",
			"bottom" => "All",
			"in" => "Sees",
		);
		$prp_types = array(
			"" => t("-- vali --"),
			"mselect" => t("Mitmerealine rippmen&uuml;&uuml;"),
			"select" => t("Rippmen&uuml;&uuml;"),
			"checkboxes" => t("M&auml;rkeruut"),
			"radiobuttons" => t("Raadionupp"),
		);
		$sort_opts = array(
			"" => t("--vali--"),
			"objects.jrk ASC" => t("J&auml;rjekord (kasvav)"),
			"objects.jrk DESC" => t("J&auml;rjekord (kahanev)"),
			"objects.name ASC" => t("Nimi (kasvav)"),
			"objects.name DESC" => t("Nimi (kahanev)"),
			"objects.created ASC" => t("Loomisaeg (kasvav)"),
			"objects.created DESC" => t("Loomisaeg (kahanev)"),
		);
		$prp_orient = array(0 => t("horisontaalne"), "vertical" => t("vertikaalne"));
		$year_sels = $this->make_keys(range(1902, 2037));
		$mon_fors = array(0 => "S&otilde;naline", 1 => "Numbriline");

		$object_type = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_OBJECT_TYPE");
		$metamgr = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_METAMGR");
		$clf_type = $object_type->meta("clf_type");
		$classificator = $object_type->meta("classificator");

		foreach($by_group as $key => $proplist)
		{
			$this->vars(array(
				"grp_caption" => $this->cfgform_i->grplist[$key]["caption"]." ($key)",
				"grpid" => $key,
			));
			$sc = "";
			foreach($proplist as $property)
			{
				$clf1 = $clf2 = $clf3 = $clf4 = $clf5 = "";
				$cnt++;
				$prpdata = $this->cfgform_i->all_props[$property["name"]];
				if (!$prpdata)
				{
					continue;
				}
				$used_props[$property["name"]] = 1;

				$type_str = $this->trans_names[$prpdata["type"]];
				if ($prpdata["type"] == "releditor" && $prpdata["name"]{4} == "f")
				{
					$type_str = $this->trans_names["releditor_fl"];
				}
				if ($prpdata["type"] == "releditor" && $prpdata["name"]{4} == "i")
				{
					$type_str = $this->trans_names["releditor_im"];
				}
				$this->vars(array(
					"bgcolor" => $cnt % 2 ? "#C9C9C9" : "#FFFFFF",
					"prp_caption" => $property["caption"],
					"prp_comment" => $property["comment"],
					"prp_type" => $type_str,
					"prp_key" => $prpdata["name"],
					"prp_order" => $property["ord"],
					"capt_ord" => html::select(array(
						"name" => "prp_opts[".$prpdata["name"]."][wf_capt_ord]",
						"style" => "border: 1px solid #EEE; padding: 2px; background-color: #FCFCEC;",
						"options" => $capt_opts,
						"selected" => $property["wf_capt_ord"],
					)),
					"nextto" => (int)$property["nextto"] ? "checked" : "",
					"space" => $property["space"],
					"invisible" => (int)$property["invisible"] ? "checked" : "",
					"invisible_name" => (int)$property["invisible_name"] ? "checked" : "",
				));
				if($prpdata["type"] == "classificator")
				{
					$optionz = array(
						"parent" => $classificator[$prpdata["name"]],
						"class_id" => CL_META,
						"status" => STAT_ACTIVE,
					);
					if(!empty($property["sort_by"]))
					{
						$optionz["sort_by"] = $property["sort_by"];
					}
					$metas = new object_list($optionz);
					$prp_metas = implode("; ", $metas->names());
					$this->vars(array(
						"clf_type" => html::select(array(
							"name" => "clf_type[".$prpdata["name"]."]",
							"options" => $prp_types,
							"selected" => $clf_type[$prpdata["name"]],
						)),
						"metamgr_link" => html::get_change_url($metamgr->id(), array(
							"group" => "manager",
							"meta" => $classificator[$prpdata["name"]],
						)),
						"predefs" => $prp_metas,
						"sort_by" => html::select(array(
							"name" => "prp_opts[".$prpdata["name"]."][sort_by]",
							"options" => $sort_opts,
							"selected" => $property["sort_by"],
						)),
					));
					if(in_array($clf_type[$prpdata["name"]], $this->n_props))
					{
						$this->vars(array(
							"v_order" => html::select(array(
								"name" => "prp_opts[".$prpdata["name"]."][orient]",
								"options" => $prp_orient,
								"selected" => $property["orient"],
							)),
						));
						$this->vars(array(
							"ordering" => $this->parse("ordering"),
						));
					}
					else
					{
						$this->vars(array(
							"v_order" => "",
							"ordering" => "",
						));
					}
					$clf1 = $this->parse("CLF1");
				}
				elseif($prpdata["type"] == "text")
				{
					$this->vars(array(
						"prp_value" => $property["value"],
					));
					$clf2 = $this->parse("CLF2");
				}
				elseif($prpdata["type"] == "checkbox")
				{
					$x_opts = array();
					foreach($proplist as $pp)
					{
						if(!in_array($pp["type"], $this->no_trans))
						{
							$x_opts[$pp["name"]] = $pp["caption"];
						}
					}
					$this->vars(array(
						"name_select" => html::select(array(
							"name" => "prp_opts[".$prpdata["name"]."][name_f]",
							"options" => $x_opts,
							"value" => $property["name_f"],
						)),
						"email_select" => html::select(array(
							"name" => "prp_opts[".$prpdata["name"]."][email_f]",
							"options" => $x_opts,
							"value" => $property["email_f"],
						)),
						"fld_id" => $property["folder_id"],
					));
					if($this->p_clid != CL_CALENDAR_REGISTRATION_FORM)
					{
						$this->vars(array(
							"NE_SELECT" => $this->parse("NE_SELECT"),
						));
					}
					$clf3 = $this->parse("CLF3");
				}
				elseif($prpdata["type"] == "date_select")
				{
					$this->vars(array(
						"time_select" => html::date_select(array(
							"name" => "prp_opts[".$prpdata["name"]."][defaultx]",
							"value" => $property["defaultx"] ? $property["defaultx"] : time(),
						)),
						"year_from" => html::select(array(
							"name" => "prp_opts[".$prpdata["name"]."][year_from]",
							"options" => $year_sels,
							"value" => $property["year_from"] ? $property["year_from"] : date("Y") - 5,
						)),
						"year_to" => html::select(array(
							"name" => "prp_opts[".$prpdata["name"]."][year_to]",
							"options" => $year_sels,
							"value" => $property["year_to"] ? $property["year_to"] : date("Y") + 5,
						)),
						"mon_for" => html::select(array(
							"name" => "prp_opts[".$prpdata["name"]."][mon_for]",
							"options" => $mon_fors,
							"value" => $property["mon_for"],
						)),
						"buttons" => html::checkbox(array(
							"name" => "prp_opts[".$prpdata["name"]."][buttons]",
							"value" => 1,
							"checked" => ( $property["buttons"] == 1 ) ? true : false
						)),
						"default_value_today" => html::checkbox(array(
							"name" => "prp_opts[".$prpdata["name"]."][default_value_today]",
							"value" => 1,
							"checked" => ( $property["default_value_today"] == 1 ) ? true : false
						)),
						"day_format" => html::select(array(
							"name" => "prp_opts[".$prpdata["name"]."][format][day_format]",
							"options" => array(
								"day" => t("Valik"),
								"day_textbox" => t("Tekstikast"),
								"day_disable" => t("Ei n&auml;idata")
							),
							"selected" => $property["format"]["day_format"]
						)),
						"month_format" => html::select(array(
							"name" => "prp_opts[".$prpdata["name"]."][format][month_format]",
							"options" => array(
								"month" => t("Valik"),
								"month_textbox" => t("Tekstikast"),
								"month_disable" => t("Ei n&auml;idata")
							),
							"selected" => $property["format"]["month_format"]
						)),
						"year_format" => html::select(array(
							"name" => "prp_opts[".$prpdata["name"]."][format][year_format]",
							"options" => array(
								"year" => t("Valik"),
								"year_textbox" => t("Tekstikast"),
								"year_disable" => t("Ei n&auml;idata")
							),
							"selected" => $property["format"]["year_format"]
						)),
					));
					$clf4 = $this->parse("CLF4");
				}
				if($prpdata["type"] == "textbox" || $prpdata["type"] == "textarea")
				{
					//arr($prpdata);
					$height = "";
					if($prpdata["type"] == "textarea")
					{
						$this->vars(array(
							"ht" => $property["height"],
							"height_caption" => t("K&otilde;rgus"),
						));
						$height = $this->parse("HEIGHT");
					}
					$this->vars(array(
						"wt" => $property["width"],
						"HEIGHT" => $height,
						"width_caption" => t("Laius"),
					));
					$clf5 = $this->parse("CLF5");
				}
				$this->vars(array(
					"CLF1" => $clf1,
					"CLF2" => $clf2,
					"CLF3" => $clf3,
					"CLF4" => $clf4,
					"CLF5" => $clf5,
				));
				$sc .= $this->parse("property");
			}
			$this->vars(array(
				"property" => $sc,
				"jrk_caption" => t("Jrk"),
				"comment_caption" => t("Kommentaar"),
				"cpt_caption" => t("Pealkiri"),
				"cpt_loc_caption" => t("Pealkirja asukoht"),
				"type_caption" => t("T&uuml;&uuml;p"),
				"side_caption" => t("K&otilde;rvale"),
				"split_caption" => t("Vahe"),
				"sel_caption" => t("Vali"),
				"no_web_caption" => t("Ei n&auml;ita veebis"),
				"no_name_caption" => t("Ei n&auml;ita nime"),
			));
			$c .= $this->parse("group");
		}

		$this->vars(array(
			"group" => $c,
		));

		$item = $arr["prop"];
		$item["value"] = $this->parse();
		return array($item);
	}

	function get_rel_props($arr)
	{
		$prop = $arr["prop"];
		$vars = array(
			"get_controllers" => array(
				"rel" => "VIEWCONTROLLER",
				"obj" => "CFG_VIEW_CONTROLLER",
			),
			"set_controllers" => array(
				"rel" => "CONTROLLER",
				"obj" => "CFGCONTROLLER",
			),
			"style" => array(
				"rel" => "STYLE",
				"obj" => "CSS",
			),
		);
		$rels = $arr["obj_inst"]->connections_from(array(
			"type" => "RELTYPE_".$vars[$prop]["rel"],
		));
		$this->all_rels = array();
		$folder_id = $arr["obj_inst"]->prop($prop."_folder");
		if(!empty($folder_id))
		{
			$objs = new object_list(array(
				"parent" => $folder_id,
				"class_id" => constant("CL_".$vars[$prop]["obj"]),
				"site_id" => array(),
				"lang_id" => array(),
			));
			$this->all_rels = $this->all_rels + $objs->names();
		}
		foreach($rels as $rel)
		{
			$this->all_rels[$rel->prop("to")] = $rel->prop("to.name");
		}
		asort($this->all_rels);
		if($prop == "style")
		{
			classload("layout/active_page_data");
			foreach($this->all_rels as $key => $val)
			{
				active_page_data::add_site_css_style($key);
			}
		}
	}

	function callback_styles($arr)
	{
		$sel_styles = safe_array($arr["obj_inst"]->meta("xstyles"));
		$m_styles = safe_array($arr["obj_inst"]->meta("m_styles"));
		$this->get_rel_props(array(
			"obj_inst" => $arr["obj_inst"],
			"prop" => "style",
		));
		$this->all_rels = array(0 => "-- Vali --") + $this->all_rels;
		$props = array();
		$props["error"] = array(
			"name" => "m_style[error]",
			"caption" => t("Veateate stiil"),
			"type" => "select",
			"options" => $this->all_rels,
			"selected" => $m_styles["error"],
		);
		foreach($this->cfgform_i->prplist as $key => $val)
		{
			$props[$key."_capt"] = array(
				"name" => "style[$key][caption]",
				"caption" => sprintf(t("%s pealkirja stiil"), $val["caption"]),
				"type" => "select",
				"options" => $this->all_rels,
				"selected" => $sel_styles[$key]["caption"],
			);
			$props[$key."_comment"] = array(
				"name" => "style[$key][comment]",
				"caption" => sprintf(t("%s kommentaari stiil"), $val["caption"]),
				"type" => "select",
				"options" => $this->all_rels,
				"selected" => $sel_styles[$key]["comment"],
			);
			$props[$key] = array(
				"name" => "style[$key][prop]",
				"caption" => sprintf(t("%s elemendi stiil"), $val["caption"]),
				"type" => "select",
				"options" => $this->all_rels,
				"selected" => $sel_styles[$key]["prop"],
			);
		}
		return $props;
		//foreach($this->cfg_proplist
	}

	function callback_preview($arr)
	{
		return array("prop1" => array(
			"name" => "prop1",
			"type" => "text",
			"no_caption" => 1,
			"value" => $this->show(array(
				"id" => $arr["obj_inst"]->id(),
				"group" => $arr["request"]["group"],
			)),
		));
	}

	function callback_view_controllers($arr)
	{
		$controllers = $this->cfgform->meta("view_controllers");
		$retval = array();
		$this->get_rel_props(array(
			"obj_inst" => $arr["obj_inst"],
			"prop" => "get_controllers",
		));
		foreach ($this->cfgform_i->prplist as $prop)
		{
			$retval[] = array(
				"name" => "view_controllers[".$prop["name"]."]",
				"caption" => $prop["caption"],
				"type" => "select",
				"multiple" => 1,
				"size" => 3,
				"value" => $controllers[$prop["name"]],
				"options" => array("" => t("-- Vali --")) + $this->all_rels,
			);
		}
		return  $retval;
	}

	function callback_submit_controllers($arr)
	{
		$controllers = $this->cfgform->meta("controllers");
		$retval = array();
		$this->get_rel_props(array(
			"obj_inst" => $arr["obj_inst"],
			"prop" => "set_controllers",
		));
		foreach ($this->cfgform_i->prplist as $prop)
		{
			$retval[] = array(
				"name" => "controllers[".$prop["name"]."]",
				"caption" => $prop["caption"],
				"type" => "select",
				"multiple" => 1,
				"size" => 3,
				"value" => $controllers[$prop["name"]],
				"options" => array("" => t("-- Vali --")) + $this->all_rels,
			);
		}
		return  $retval;
	}

	function request_execute($o)
	{
		return $this->show(array(
			"id" => $o->id(),
		));
	}

	function parse_alias($arr = array())
	{
		$id = $arr["alias"]["target"];
		if(is_oid($id) && $this->can("view", $id))
		{
			return $this->show(array("id" => $id, "doc_id" => aw_global_get("shown_document") ? aw_global_get("shown_document") : $arr["oid"]));
		}
	}

	function show($arr)
	{
		$this->read_template("show_form.tpl");
		$obj_inst = obj($arr["id"]);
		$ftype = $obj_inst->prop("form_type");
		$this->get_rel_props(array(
			"obj_inst" => $obj_inst,
			"prop" => "style",
		));
		if (!empty($_SERVER["HTTPS"]))
		{
			$this->vars(array(
				"url_spec" => str_replace("http:", "https:", aw_ini_get("baseurl"))
			));
		}
		else
		{
			$this->vars(array(
				"url_spec" => aw_ini_get("baseurl")
			));
		}
		$object_type = $obj_inst->get_first_obj_by_reltype("RELTYPE_OBJECT_TYPE");
		$errors = aw_global_get("wf_errors");
		$values = aw_global_get("wf_data");
		if(strpos(strtolower($_SERVER["REQUEST_URI"]), "/automatweb") !== false)
		{
			$section = html::get_change_url($arr["id"], array(
				"group" => $arr["group"],
			));
		}
		else
		{
			$section = aw_ini_get("baseurl").substr($_SERVER["REQUEST_URI"], 0, -1);
		}

		if (!empty($_SERVER["HTTPS"]))
		{
			$section = str_replace("http:", "https:", $section);
		}

		$vrs = array();

		if($ftype == CL_CALENDAR_REGISTRATION_FORM)
		{
			if(!empty($arr["ef"]))
			{
				$form_conf = obj($arr["ef"]);
			}
			else
			{
				$form_conf = $obj_inst->get_first_obj_by_reltype("RELTYPE_CAL_REG_FORM_CONF");
			}
			if(is_object($form_conf))
			{
				$event = obj($form_conf->prop("event"));
				if ($event)
				{
					$form_conf_i = $form_conf->instance();

					$form_conf_i->read_template("show.tpl");

					$form_conf_i->_insert_event_inf($event, $form_conf);

					if ($form_conf->prop("max_pers") && $form_conf_i->get_count_for_event($event) >= $form_conf->prop("max_pers"))
					{
						return $form_conf_i->parse();
					}
					$ef_id = $form_conf->id();
					if($form_conf->prop("show_content") == 1)
					{
						$this->_insert_event_inf($event, $form_conf);
					}
				}
			}
		}

		if (!empty($_GET['confirm']))
		{
			return $this->draw_confirm_page($arr);
		}

		if (isset($arr["link"]) && $arr["link"] == 1 && (!isset($_GET["show"]) || $_GET["show"] != 1) && is_oid($ef_id))
		{
			$this->vars(array(
				"form" => html::href(array(
					"url" => aw_url_change_var("show", 1),
					"caption" => ($form_conf->prop("link_caption") != "" ? $form_conf->prop("link_caption") : t("Registreeru"))
				))
			));
			$rval = $this->parse();
		}
		else
		{
			$rval = $this->draw_cfgform_from_ot(array(
				"ot" => $object_type->id(),
				"reforb" => array(
					"class" => $ftype != CL_CALENDAR_REGISTRATION_FORM ? "webform" : "calendar_registration_form_conf",
					"return_url" => $section.((isset($_GET["show"]) and $_GET["show"] == 1) ? "?show=1" : ""),
					"id" => $ftype != CL_CALENDAR_REGISTRATION_FORM  ? $arr["id"] : $ef_id,
					"doc_id" => is_object($arr["doc_id"]) ? $arr["doc_id"]->id() : $arr["doc_id"],
					"subaction" => "",
					"section" => aw_global_get("section")
				),
				"errors" => $errors,
				"values" => $values,
				"obj_inst" => $obj_inst,
				"action" => $ftype != CL_CALENDAR_REGISTRATION_FORM ? "save_form_data" : "submit_register",
			));
			aw_session::del("wf_errors");
			aw_session::del("wf_data");
		}
		return $rval;
	}

	function draw_cfgform_from_ot($arr)
	{
		$object_type = obj($arr["ot"]);
		$clf_type = $object_type->meta("clf_type");
		$cfgform_i = get_instance(CL_CFGFORM);
		if (!is_admin())
		{
			$arr["site_lang"] = true;
		}
		$els = $cfgform_i->get_props_from_ot($arr);
		//sorteerimine
		$form = obj($arr["obj_inst"]->prop("use_cfgform"));
		$cfgform = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_CFGFORM");
		$cfgform_i->_init_cfgform_data($cfgform);
		$cfgform_i->sort_grplist();
		if(is_array($cfgform_i->grplist))
		{
			$sorted_els = array();
			foreach($cfgform_i->grplist as $group => $data)
			{
				foreach($els as $key => $val)
				{
					if(($val["group"]  == $group) || (in_array($group , $val["group"]))) $sorted_els[$key] = $val;
				}
			}
			$els = $sorted_els;
		}
		$sel_styles = safe_array($arr["obj_inst"]->meta("xstyles"));
		$m_styles = safe_array($arr["obj_inst"]->meta("m_styles"));
		$errs = safe_array($arr["errors"]);
		$values = safe_array($arr["values"]);
		$all_props = safe_array($cfgform->meta("cfg_proplist"));
		$ret = $errs2 = $errs1 = $sbz = array();
		$no_sbt = true;
		$nms = array();
		$chk_prps = array(
			"default" => "defaultx",
			"year_from" => "year_from",
			"year_to" => "year_to",
			"mon_for" => "mon_for",
			"buttons" => "buttons",
			"format" => "format",
			"default_value_today" => "default_value_today"
		);

		$cfg_proplist = $cfgform_i->get_cfg_proplist($cfgform->id());//juhul kui omadusi on teatud gruppidele maha keeratud, n&auml;itab
		foreach($els as $pn => $pd)
		{
			if(empty($cfg_proplist[$pn]))//see v&auml;rk siis kontrollib, kas miskile kasutajale on mingi omadus &auml;kki maha keeratud
			{
				continue;
			}

			if($pd["invisible"]) continue;
			if($pd["invisible_name"]) $pd["caption"] = null;

			if($pd["type"] === "releditor")
			{
				$nms[$pn] = $pd["caption"];
			}

			if(($pd["type"] === "textbox" || $pd["type"] === "textarea"))
			{
				if(!empty($all_props[$pn]["width"]))
				{
					if($pd["type"] == "textbox")
					{
						$pd["size"] = $all_props[$pn]["width"];
					}
					else
					{
						$pd["cols"] = $all_props[$pn]["width"];
					}
				}
				if(!empty($all_props[$pn]["height"]) && $pd["type"] == "textarea")
				{
					$pd["rows"] = $all_props[$pn]["height"];
				}
			}

			if ($pd["type"] !== "text")
			{
				$pd["value"] = isset($values[$pn]) ? $values[$pn] : null;
			}

			if($pd["type"] === "submit")
			{
				$sbz[$pn] = $pd;
				$no_sbt = false;
				continue;
			}
			elseif($pd["type"] === "reset" || $pd["type"] === "button")
			{
				$sbz[$pn] = $pd;
				continue;
			}

			if($pd["type"] === "classificator" && $all_props[$pn]["sort_by"])
			{
				$pd["sort_by"] = $all_props[$pn]["sort_by"];
			}

			if($pd["type"] === "date_select")
			{
				foreach($chk_prps as $ke => $vad)
				{
					if($pd["value"] && $vad === "defaultx")
					{
						continue;
					}
					if($all_props[$pn][$vad])
					{
						$pd[$ke] = $all_props[$pn][$vad];
					}
				}
			}

			$num = 0;
			if (isset($errs[$pn]))
			{
				$erz = array(
					"name" => $pn."_err".$num,
					"type" => "text",
					"store" => "no",
					"value" => "<font color='red' class='st".$m_styles["error"]."'>".$errs[$pn]["msg"]."</font>",
					"no_caption" => 1,
				);
				$erloc = safe_array($arr["obj_inst"]->prop("error_location"));
				if(empty($erloc))
				{
					$ret[$pn."_err".$num] = $erz;
				}
				else
				{
					foreach($erloc as $key => $erx)
					{
						switch($key)
						{
							case 1:
								$errs1[$pn."_err".$num] = $erz;
								$errs1[$pn."_err".$num]["name"] = $pn."_err".$num;
								break;
							case 2:
								$errs2[$pn."_err".$num] = $erz;
								$errs2[$pn."_err".$num]["name"] = $pn."_err".$num;
								break;
							default:
								$ret[$pn."_err".$num] = $erz;
								$ret[$pn."_err".$num]["name"] = $pn."_err".$num;
								break;
						}
						$num++;
					}
				}
			}
			$ret[$pn] = $pd;
		}
		$els = $errs1 + $ret + $errs2 + $sbz;
		// special case n shit
		if($no_sbt)
		{
			$els["submit"] = array(
				"name" => "submit2",
				"type" => "submit",
				"caption" => t("Saada"),
			);
		}
		$ftype = $arr["obj_inst"]->prop("form_type");
		$inst = empty($ftype) ? CL_REGISTER_DATA : $ftype;
		$rd = get_instance($inst);


		$dummy = new object();
		$dummy->set_class_id($ftype);

		$rd->cfgform_id = $cfgform->id();
		$rd->load_defaults();
		$els = $rd->parse_properties(array(
			"properties" => &$els,
			"obj_inst" => $dummy,
		));
		$def_caption_style = $arr["obj_inst"]->prop("def_caption_style");
		$def_comment_style = $arr["obj_inst"]->prop("def_caption_style");
		$def_prop_style = $arr["obj_inst"]->prop("def_prop_style");
		if(!empty($def_caption_style) or !empty($def_prop_style) or !empty($def_comment_style))
		{
			foreach($els as $key => $val)
			{
				if(!empty($def_caption_style))
				{
					$els[$key]["style"]["caption"] = $def_caption_style;
				}
				if(!empty($def_comment_style))
				{
					$els[$key]["style"]["comment"] = $def_comment_style;
				}
				if(!empty($def_prop_style))
				{
					$els[$key]["style"]["prop"] = $def_prop_style;
				}
			}
		}
		$id = $arr["obj_inst"]->id();
		$aliasmgr = get_instance("alias_parser");
		$tmp = $els;

		foreach($tmp as $key => $val)
		{
			if($val["type"] === "fileupload")
			{
				foreach($nms as $iv => $nm)
				{
					if(strpos($key, $iv) !== false)
					{
						$els[$key]["caption"] = $nm;
						break;
					}
				}
			}
			if(strpos($key, "_filename") !== false)
			{
				unset($els[$key]);
				continue;
			}
			$aliasmgr->parse_oo_aliases($id, &$els[$key]["caption"]);
			if($val["type"] === "text")
			{
				if(!empty($all_props[$key]["value"]) && empty($els[$key]["value"]))
				{
					$els[$key]["value"] = nl2br($all_props[$key]["value"]);
				}
				$aliasmgr->parse_oo_aliases($id, &$els[$key]["value"]);
				$els[$key]['value'] .= html::hidden(array(
					'name' => $key,
					'value' => $els[$key]['value']
				));
			}
			// some goddamn thing messes up the element captions, reorder them
			//$els[$key]["caption"] = $all_props[$key]["caption"];
			$els[$key]["capt_ord"] = $all_props[$key]["wf_capt_ord"];
			// treat all text properties as an ordinary text property
			if($all_props[$key]["type"] === "text" && empty($all_props[$key]["value"]))
			{
				$els[$key]["subtitle"] = 1;
			}
			else
			{
				unset($els[$key]["subtitle"]);
			}

			if(isset($sel_styles[$key]) and is_array($sel_styles[$key]))
			{
				if(!empty($sel_styles[$key]["caption"]))
				{
					$els[$key]["style"]["caption"] = $sel_styles[$key]["caption"];
				}
				if(!empty($sel_styles[$key]["prop"]))
				{
					$els[$key]["style"]["prop"] = $sel_styles[$key]["prop"];
				}
			}

			if($val["type"] === "hidden")
			{
				$arr["reforb"][$key] = "";
			}

			// we do this because no one uses a simple button in a form anyway, and this is the easiest
			// way to do it without messing up htmlclient
			if($val["type"] === "button")
			{
				$val["onclick"] = "document.changeform.subaction.value='print';submit_changeform();";
				$val["class"] = "sbtbutton";
				$val["parent"] = "submitx";
				$tmpx[$key] = $val;
				unset($els[$key]);
			}

			if(isset($clf_type[$key]) and in_array($clf_type[$key], $this->n_props))
			{
				$els[$key]["orient"] = $all_props[$key]["orient"];
			}

			if($all_props[$key]["type"] === "reset" || $all_props[$key]["type"] === "submit")
			{
				$val["class"] = isset($val["style"]["prop"]) ? $val["style"]["prop"] : null;
				if($all_props[$key]["type"] === "submit")
				{
					$val["parent"] = "submitx";
					$tmpx[$key] = $val;
				//	unset($els[$key]);
				}
				elseif($all_props[$key]["type"] === "reset")
				{
					$val["parent"] = "submitx";
					$tmpx[$key] = $val;
				//	unset($els[$key]);
				}
			}

			if($val["type"] === "select")
			{
				foreach(safe_array($val["options"]) as $k => $v)
				{
					if(is_oid($k) && $this->can("view", $k))
					{
						$obj = obj($k);
						$value = $obj->comment();
						if($value == 1)
						{
							$els[$key]["selected"][$k] = $k;
							if($val["multiple"] != 1)
							{
								break;
							}
						}
					}
				}
			}

			if($val["type"] === "chooser")
			{
				foreach(safe_array($val["options"]) as $k => $v)
				{
					if(is_oid($k) && $this->can("view", $k))
					{
						$obj = obj($k);
						$value = $obj->comment();
						if($value == 1)
						{
							if($val["multiple"] == 1)
							{
								$els[$key]["value"][$k] = 1;
							}
							else
							{
								$els[$key]["value"] = $k;
								break;
							}
						}
					}
				}
			}

			if ($val['type'] === 'date_select')
			{
				if ($val['default_value_today'] == 1)
				{
					$els[$key]['value'] = time();
				}
			}
		}
		$layout = array(
			"submitx" => array(
				"type" => "hbox",
			),
		);
		$null = array();
		$x = 0;
		foreach($els as $v => $el)
		{
			if($x == 0)
			{
				$last = $v;
				$x++;
			}
			if($el["nextto"] == 1 && !empty($last))
			{
				if($els[$last]["nextto"] == 1 && $last != $v)
				{
					$z = $last;
					$last = $els[$last]["pr"];
				}
				$layout[$last."box"] = array(
					"type" => "hbox",
				);
				$els[$last]["parent"] = $last."box";
				$els[$v]["parent"] = $last."box";
			}
			$els[$v]["pr"] = $last;
			$last = $v;
		}
		$els = (array)$els + (array)$tmpx;

		$htmlc = new htmlclient(array(
			"template" => "real_webform.tpl",
			"styles" => safe_array($arr["obj_inst"]->meta("m_styles")),
		));
		$htmlc->set_layout($layout);
		$htmlc->start_output();
		foreach($els as $pn => $pd)
		{
			$pd["capt_ord"] = $pd["wf_capt_ord"];
			if ($pd["capt_ord"] === "in" && empty($arr['value']))
			{
				switch ($pd['type'])
				{
					case 'select':
						if ($pd['multiple'] == 1)
						{
							$values = explode(',', $pd['caption']);
							$found = false;
							foreach ($values as $value)
							{
								$search_result = array_search($value, $pd['options']);
								if ( $search_result !== false )
								{
									$pd['selected'][] = array_search($value, $pd['options']);
									$found = true;
								}
							}
							if ($found === false)
							{
								$pd['options'] = array('' => $pd['caption']) + $pd['options'];
							}
						}
						else
						{
							$selected_option = array_search($pd['caption'], $pd['options']);
							if ( $selected_option === false )
							{
								$pd['options'][''] = $pd['caption'];
							}
							else
							{
								$pd['selected'] = $selected_option;
							}
						}
						break;
					case 'chooser':
						if ($pd['multiple'] == 1)
						{
							$values = explode(',', $pd['caption']);
							foreach ($values as $value)
							{
								$pd['value'][array_search($value, $pd['options'])] = $value;
							}
						}
						else
						{
							$pd['value'] = array_search($pd['caption'], $pd['options']);
						}
						break;

					default:
						$pd["value"] = $pd["caption"];
						$pd["onfocus"] = "if (this.value == '".$pd['value']."')this.value=''";
						$pd["onblur"] = "if (this.value == '')this.value='".$pd['value']."'";
				}

			}
			// strip tags because of hidden values
			if ($pd["type"] == "text" && $pd["subtitle"] == 1 && trim(strip_tags($pd["value"])) == "")
			{
				$pd["value"] = $pd["caption"];
			}
			if ($pd["type"] == "hidden" && isset($arr["reforb"][$pn]))
			{
				$arr["reforb"][$pn] = $pd["value"];
			}
			$htmlc->add_property($pd);
		}
		$htmlc->finish_output();

		$html = $htmlc->get_result(array(
			"raw_output" => 1,
		));

		if ($arr['obj_inst']->prop('show_confirm_page'))
		{
			$arr['reforb']['show_confirm_page'] = 1;
		}

		$spacing = '';
		if($mst = $arr["obj_inst"]->prop("def_form_style"))
		{
			$sto = obj($mst);
			if($spval = $sto->prop("padding"))
			{
				$spacing = ' cellspacing="'.$spval.'"';
			}
		}
		$this->vars(array(
			"faction" => $arr["action"],
			"form" => $html,
			"spacing" => $spacing,
			"webform_form" => "st".$arr["obj_inst"]->prop("def_form_style"),
			"reforb" => $this->mk_reforb($arr["action"], $arr["reforb"]),
		));

		return $this->parse();
	}

	/**

		@attrib name=save_form_data nologin=1 all_args=1

		@param id required type=int acl=view
		@param return_url optional
	**/
	function save_form_data($arr)
	{

		// we need a solid copy of arr, cause we alter the actual input many times
		$subaction = $arr["subaction"];
		$obj_inst = obj($arr["id"]);
		$redirect = $obj_inst->trans_get_val("redirect");
		$rval = (strpos(strtolower($redirect), "http://") !== false || strpos(strtolower($redirect), "https://") !== false ? $redirect : (substr($redirect, 0, 1) == "/" ?  aw_ini_get("baseurl").substr($redirect, 0, -1) : aw_ini_get("baseurl").$redirect));

		$object_type = $obj_inst->get_first_obj_by_reltype("RELTYPE_OBJECT_TYPE");
		$cfgform = $obj_inst->get_first_obj_by_reltype("RELTYPE_CFGFORM");
		$register = $obj_inst->get_first_obj_by_reltype("RELTYPE_REGISTER");
		if (!$register)
		{
			$register = obj();
		}
		//$prplist = safe_array($cfgform->meta("cfg_proplist"));
		$cf = $cfgform->instance();
		$prplist = $cf->get_cfg_proplist($cfgform->id());
		$register_data_i = get_instance(CL_REGISTER_DATA);
		$register_data_i->init_class_base();
		if (is_array($_FILES))
		{
			// make file uploads show up in the upload array
			foreach($_FILES as $name => $dontcare)
			{
				$arr[$name] = 1;
			};
		};

		if ($obj_inst->prop('show_confirm_page'))
		{
			if (isset($arr['confirmed']))
			{
				$arr = array_merge((array)$_SESSION['wf_data'], (array)$arr);
				unset($arr['show_confirm_page'], $_SESSION['wf_data']);
			}
			else
			if (isset($arr['not_confirmed']))
			{
				return $arr['return_url'];
			}
		}

		$is_valid = $register_data_i->validate_data(array(
			//"props" => $props,
			"request" => &$arr,
			"cfgform_id" => $cfgform->id(),
		));
		if(!empty($is_valid))
		{
			aw_session_set("no_cache", 1);
			aw_session_set("wf_errors", $is_valid);
			aw_session_set("wf_data", $arr);
			return $arr["return_url"];
		}
		else
		{

			if ($obj_inst->prop('show_confirm_page') && $arr['show_confirm_page'] == 1)
			{
				$_SESSION['wf_data'] = $arr;
				return aw_url_change_var('confirm', 1, $arr['return_url']);
			}

			$register = $obj_inst->get_first_obj_by_reltype("RELTYPE_REGISTER");
			if (!$register)
			{
				$register = obj();
			}
			$o = obj();
			$o->set_class_id(CL_REGISTER_DATA);
			$o->set_parent($register->prop("data_rootmenu"));
			$o->set_status(STAT_ACTIVE);
			$o->set_meta("cfgform_id", $cfgform->id());
			$o->set_meta("object_type", $object_type->id());
			//$o->save();
			$cls = get_instance(CL_CLASSIFICATOR);
			$relprops = $this->get_properties_by_type(array(
				"clid" => CL_REGISTER_DATA,
				"type" => array("classificator","releditor"),
			));

			$relinfo = $o->get_relinfo();

			foreach($arr as $key => $val)
			{
				if(!array_key_exists($key, $prplist))
				{
					unset($arr[$key]);
					continue;
				}
				// goddamn conversion...
				if($prplist[$key]["type"] == "date_select")
				{
					$val = mktime(0, 0, 0, $val["month"], $val["day"], $val["year"]);
				}
				if($prplist[$key]["type"] == "classificator")
				{
					$cls->process_vcl_property(array(
						"obj_inst" => $o,
						"prop" => array(
							"name" => $key,
							"value" => $val,
							"reltype" => $relprops[$key]["reltype"],
							"store" => "connect",
						),
						"clid" => CL_REGISTER_DATA,
					));
				}
				if($prplist[$key]["type"] == "releditor")
				{
					// fuck, this sucks, it should simply use classbase, but since
					// there are some groovy things going on - like for example with
					// the checkbox, I'm a bit scared about converting it over ..
					$tmp = $relprops[$key];
					$tmp["value"] = $val;
					$tmp["clid"] = $relinfo[$relprops[$key]["reltype"]]["clid"];
					$rele = get_instance("vcl/releditor");
					$rele->process_releditor(array(
						"obj_inst" => $o,
						"prop" => $tmp,
					));

				}
				if($prplist[$key]["type"] == "checkbox")
				{
					$m = obj();
					$m->set_class_id(CL_ML_MEMBER);
					$m->set_parent($prplist[$key]["folder_id"]);
					$m->set_name($arr[$prplist[$key]["name_f"]]."<".$arr[$prplist[$key]["email_f"]].">");
					$m->set_prop("name", $arr[$prplist[$key]["name_f"]]);
					$m->set_prop("mail", $arr[$prplist[$key]["email_f"]]);
					$m->save();
				}
				if ($o->is_property($key))
				{
					$o->set_prop($key, $val);
				}
			}
			$name = "";
			foreach(safe_array($obj_inst->prop("obj_name")) as $key => $val)
			{
				if ($prplist[$key]["type"] == "date_select")
				{
					if ($o->prop($key)  != -1)
					{
						$name .= " ".date("d.m.Y", $o->prop($key));
					}
				}
				else
				if ($prplist[$key]["type"] == "datetime_select")
				{
					if ($o->prop($key)  != -1)
					{
						$name .= " ".date("d.m.Y H:i", $o->prop($key));
					}
				}
				else
				{
					if (!is_oid($o->id()))
					{
						$o->save();
					}
					$name .= " ".$o->prop_str($key); //$arr[$key];
				}
			}
			$o->set_name(trim($name));
                        $o->set_prop("register_id", $register->id());
                        $o->save();

			$body = "";
			$attaches = array();
			// lets translate this stuff to real things
			foreach($arr as $key => $val)
			{
				if($prplist[$key]["type"] == "date_select")
				{
					$arr[$key] = $val["day"].".".$val["month"].".".$val["year"];
				}
				if($prplist[$key]["type"] == "classificator")
				{
					if (!is_array($val) && $this->can("view", $val))
					{
						$val = array($val);
					}

					if (is_array($val) && count($val))
					{
						$objs = new object_list(array("oid" => $val, "lang_id" => array(), "site_id" => array()));
						$nsa = array();
						foreach($objs->arr() as $tmp_o)
						{
							$nsa[] = $tmp_o->trans_get_val("name");
						}
						$arr[$key] = join(", ", $nsa);
					}
				}

				if (substr($key, 0, 6) == "userim")
				{
					if ($_FILES[$key]["name"]["file"] != "")
					{
						$body .= html_entity_decode($prplist[$key]["caption"], ENT_COMPAT, aw_global_get("charset")).": ".$_FILES[$key]["name"]["file"]."\n";
						$fo = $o->get_first_obj_by_reltype($relprops[$key]["reltype"]);
						$attaches[] = array(
							"content" => file_get_contents($fo->prop("file")),
							"contenttype" => $_FILES[$key]["type"]["file"],
							"name" => $_FILES[$key]["name"]["file"]
						);
					}
				}
				else
				if (substr($key, 0, 8) == "userfile")
				{
					if ($_FILES[$key]["name"]["file"] != "")
					{
						$body .= html_entity_decode($prplist[$key]["caption"], ENT_COMPAT, aw_global_get("charset")).": ".$_FILES[$key]["name"]["file"]."\n";
						$fo = $o->get_first_obj_by_reltype($relprops[$key]["reltype"]);
						$attaches[] = array(
							"content" => file_get_contents($fo->prop("file")),
							"contenttype" => $_FILES[$key]["type"]["file"],
							"name" => $_FILES[$key]["name"]["file"]
						);
					}
				}
				else
				if(!in_array($prplist[$key]["type"], $this->no_trans) && !(empty($arr[$key]) && !$obj_inst->prop("send_all_rows_to_mail")))
				{
					$body .= html_entity_decode($prplist[$key]["caption"], ENT_COMPAT, aw_global_get("charset")).": ".$arr[$key]."\n";
				}
			}
			foreach($obj_inst->connections_from(array("type" => "RELTYPE_AFTER_SAVE_CONTROLLER")) as $c)
			{
				$controller_obj = $c->to();
				$controller_i = $controller_obj->instance();
				$controller_i->check_property($controller_obj->id(), $o->id(), $o->properties(), $arr, $o->properties(), $o);
			}
			$emxs = $obj_inst->connections_from(array(
				"type" => "RELTYPE_EMAIL",
			));
			$emails = array();
			foreach($emxs as $emx)
			{
				$email = $emx->to();
				$emails[$email->id()] = $email->prop("mail");
			}
			$emls = safe_array(aw_global_get("receivers_name"));
			$emails = $emails + $emls;
			$nm = aw_global_get("global_name");
			if(!empty($nm))
			{
				$prx = array(
					"froma" => $nm,
				);
			}
			else
			{
				$prx = array(
					"fromn" => $obj_inst->prop("def_name"),
					"froma" => $obj_inst->prop("def_mail"),
				);
			}

			$awm = get_instance("protocols/mail/aw_mail");
			foreach($attaches as $att)
			{
				$awm->fattach($att);
			}
			foreach($emails as $eml)
			{
				$awm->create_message(array(
					"subject" => aw_global_get("email_subject") != "" ? aw_global_get("email_subject") : $obj_inst->name(),
					"to" => $eml,
					"body" => $body,
				) + $prx);
/*				$awm->htmlbodyattach(array(
					"data" => str_replace("\n", "<br>", $body)
				));*/
				$awm->gen_mail();
			}

			if ($obj_inst->prop("after_confirm_edit"))
			{
				return $this->mk_my_orb("change", array("id" => $o->id(), "section" => aw_global_get("section")), CL_REGISTER_DATA, false, false, "&", false);
			}

			if ($obj_inst->prop("disp_after_entry") != "")
			{
				return $this->mk_my_orb("show_form", array("id" => $obj_inst->id(), "fid" => $o->id(), "url" => $rval), CL_WEBFORM, false, false, "&", false);
			}
			$rv = !empty($subaction) ? $this->mk_my_orb("show_form", array("id" => $obj_inst->id(), "fid" => $o->id(), "url" => $rval), CL_WEBFORM) : $rval;
			return $rv;
		}
	}

	function draw_confirm_page($arr)
	{
		// webform object
		if ($this->can('view', $arr['id']))
		{
			$o = new object($arr['id']);
		}
		else
		{
			return false;
		}

		$this->read_template($o->prop('confirm_page_template'));

		$object_type = $o->get_first_obj_by_reltype("RELTYPE_OBJECT_TYPE");
		$cfgform_i = get_instance(CL_CFGFORM);
		$props = $cfgform_i->get_props_from_ot(array(
			"ot" => $object_type->id()
		));
		$form_data = $_SESSION['wf_data'];

		$vars = array();
		foreach ($props as $name => $prop_data)
		{
			switch ($prop_data['type'])
			{
				case 'date_select':
					foreach ($form_data['value'] as $k => $v)
					{
						$vars[$name.'_'.$k] = $v;
					}
					break;
				case 'classificator':
					$vars[$name] = '';
					if ($this->can('view', $form_data[$name]))
					{
						$classificator_obj = obj($form_data[$name]);
						$vars[$name] = $classificator_obj->name();
					}
					break;
				case 'text':
					$vars[$name] = $prop_data['value'];
					break;
				case 'releditor':
					if ("userfile" === substr($prop_data["name"], 0, -1))
					{ // user file upload confirm
					}
					else
					{ // other releditors if implemented
						$vars[$name] = $form_data[$name];
					}
					break;
				case 'textarea':
					$vars[$name] = nl2br($form_data[$name]);
					break;
				default:
					$vars[$name] = $form_data[$name];
			}
			$vars[$name.'_caption'] = $prop_data['caption'];
		}
		$vars['reforb'] = $this->mk_reforb('save_form_data', array(
			'id' => $arr['id'],
			"section" => aw_global_get("section"),
			'return_url' => aw_ini_get('baseurl').(is_object($arr['doc_id']) ? $arr["doc_id"]->id() : $arr["doc_id"]),
		), 'webform');

		$vars['confirmed_button'] = html::submit(array(
			'name' => 'confirmed',
			'value' => t('Kinnita'),
			'class' => 'confirm_button'
		));
		$vars['not_confirmed_button'] = html::submit(array(
			'name' => 'not_confirmed',
			'value' => t('Tagasi'),
			'class' => 'confirm_button'
		));
		$this->vars($vars);

		return $this->parse();
	}

	function _insert_event_inf($e, $o)
	{
		$start = $e->prop("start");
		$end = $e->prop("end");
		$this->vars(array(
			"ev_title" => $e->name(),
			"ev_start" => aw_locale::get_lc_date($start, LC_DATE_FORMAT_LONG_FULLYEAR)." ".date("H:i",$end),
			"ev_end" => aw_locale::get_lc_date($end, LC_DATE_FORMAT_LONG_FULLYEAR)." ".date("H:i",$end),
			"ev_content" => nl2br($e->prop("content"))
		));
		$ct = $this->parse("SHOW_CONTENT");
		$this->vars(array(
			"SHOW_CONTENT" => $ct
		));
	}

	/**

		@attrib name=remove_entries
		@param id required type=int acl=view
		@param group optional
		@param select required

	**/
	function remove_entries($arr)
	{
		if(is_array($arr["select"]))
		{
			foreach($arr["select"] as $val)
			{
				if(is_oid($val) && $this->can("delete", $val))
				{
					$obj = obj($val);
					if($obj->class_id() == CL_REGISTER_DATA)
					{
						$obj->delete();
					}
				}
			}
		}
		return html::get_change_url($arr["id"], array("group" => $arr["group"]));
	}

	/**
		@attrib name=show_form nologin=1
		@param id required type=int acl=view
		@param fid required type=int acl=view
		@param url required
		@param format optional
	**/
	function show_form($arr)
	{
		$obj = obj($arr["fid"]);
		$wf = obj($arr["id"]);
		if ($wf->prop("disp_after_entry") != "")
		{
			if (($arr["format"] === "print" || $arr["format"] === "pdf") && $wf->prop("disp_after_entry_print"))
			{
				$this->read_template("disp/".$wf->prop("disp_after_entry_print"));
			}
			else
			{
				$this->read_template("disp/".$wf->prop("disp_after_entry"));
			}

			$object_type = $wf->get_first_obj_by_reltype("RELTYPE_OBJECT_TYPE");
			$cfgform_i = get_instance(CL_CFGFORM);
			$els = $cfgform_i->get_props_from_ot(array(
				"ot" => $object_type->id()
			));

			foreach($obj->properties() as $pn => $pv)
			{
				$this->vars(array(
					$pn => $els[$pn]["type"] == "text" ? $els[$pn]["value"] : $obj->prop_str($pn),
					$pn."_caption" => $els[$pn]["caption"]
				));
			}
			$this->vars(array(
				"print_url" => aw_url_change_var("format", "print"),
				"pdf_url" => aw_url_change_var("format", "pdf"),
			));

			if ($arr["format"] == "print")
			{
				die($this->parse());
			}
			if ($arr["format"] == "pdf")
			{
				$conv = get_instance("core/converters/html2pdf");
				$conv->gen_pdf(array("source" => $this->parse()));
			}
			return $this->parse();
		}

		$form = $obj->instance();
		$form->init_class_base();

		$rval = $form->view(array(
			"id" => $arr["fid"],
			"class" => CL_REGISTER_DATA ? "register_data" : "calendar_registration_form",
			"action" => "view",
			"group" => "general",
			"cb_part" => 1,
			"fxt" => 1,
			"no_buttons" => 1,
		));
		$this->init(array(
			"tpldir" => "automatweb/documents",
		));
		$this->read_template("print.tpl");
		$this->vars(array(
			"text" => $rval,
		));
		return $this->parse()."<br />".html::href(array("url" => urldecode($arr["url"]), "caption" => t("Liigu edasi &raquo;")));
		//return "valleraa, siin on vorm";
	}

	/**
		@attrib api=1
		@param id required
		@comment
			returns list of properties from the webform ($id)
	**/
	function get_props_from_wf($arr)
	{
		$wf = obj($arr["id"]);
		$ot = $wf->get_first_obj_by_reltype("RELTYPE_OBJECT_TYPE");

		$cf = get_instance(CL_CFGFORM);
		return $cf->get_props_from_ot(array(
			"ot" => $ot->id()
		));
	}

	function callback_mod_reforb(&$arr)
	{
		if("send_mails" === $this->use_group)
		{
			$arr["add_search_mail"] = 0;
		}
		$arr["post_ru"] = get_ru();//XXX: vaja?
	}

	function callback_generate_scripts($arr)
	{
		if("send_mails" === $this->use_group)
		{
			$this->vars(array(
				"agurl" => $this->mk_my_orb("ajax_get_mail_field"),
			));
			$this->read_template('mails_script.tpl');
			return $this->parse();
		}
	}

	function get_mails_tb($arr)
	{
		$tb = $arr["prop"]["vcl_inst"];
		$tb->add_new_button(array(CL_ML_MEMBER), $arr["obj_inst"]->id(), 5);
		$tb->add_search_button(array(
			"pn" => "add_search_mail",
			"clid" => array(CL_ML_MEMBER),
			"multiple" => 1,
		));
		$tb->add_delete_rels_button();
	}

	function set_mails_tb($arr)
	{
		$vars = array("name", "email");
		foreach($vars as $var)
		{
			if($mid = $arr["request"]["search_mail_".$var])
			{
				if(is_oid($mid))
				{
					$mail = $mid;
				}
			}
		}
		if($mail)
		{
			$arr["request"]["add_search_mail"] = $mail;
		}
		if($add = $arr["request"]["add_search_mail"])
		{
			$mails = explode(",", $add);
			foreach($mails as $mail)
			{
				if(!$arr["obj_inst"]->is_connected_to(array("to" => $mail)))
				{
					$arr["obj_inst"]->connect(array(
						"to" => $mail,
						"type" => "RELTYPE_EMAIL",
					));
				}
			}
		}
	}

	function _init_mails_tbl($t)
	{
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid",
		));
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
		));
		$t->define_field(array(
			"name" => "mail",
			"caption" => t("Aadress"),
		));
	}

	function get_mails_tbl($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		$this->_init_mails_tbl(&$t);
		$conn = $arr["obj_inst"]->connections_from(array(
			"type" => "RELTYPE_EMAIL",
		));
		foreach($conn as $c)
		{
			$o = $c->to();
			$t->define_data(array(
				"oid" => $o->id(),
				"name" => html::obj_change_url($o),
				"mail" => $o->prop("mail"),
			));
		}

	}

	/**
		@attrib name=mail_email_autocomplete_source
		@param search_mail_email optional
	**/
	function mail_email_autocomplete_source($arr)
	{
		$ac = get_instance("vcl/autocomplete");
		$arr = $ac->get_ac_params($arr);

		$ol = new object_list(array(
			"class_id" => CL_ML_MEMBER,
			"mail" => $arr["search_mail_email"]."%",
			"lang_id" => array(),
			"site_id" => array(),
			"limit" => 200
		));
		$res = array();
		foreach($ol->arr() as $o)
		{
			$res[$o->id()] = $o->prop("mail");
		}

		return $ac->finish_ac($res);
	}

	/**
		@attrib name=mail_name_autocomplete_source
		@param search_mail_name optional
	**/
	function mail_name_autocomplete_source($arr)
	{
		$ac = get_instance("vcl/autocomplete");
		$arr = $ac->get_ac_params($arr);

		$ol = new object_list(array(
			"class_id" => CL_ML_MEMBER,
			"CL_ML_MEMBER.name" => $arr["search_mail_name"]."%",
			"limit" => 200
		));
		$res = array();
		foreach($ol->arr() as $o)
		{
			$res[$o->id()] = $o->prop("name");
		}

		return $ac->finish_ac($res);
	}

	/**
		@attrib name=ajax_get_mail_field
		@param val required
		@param find required
	**/
	function ajax_get_mail_field($arr)
	{
		if(!empty($arr["val"]))
		{
			if($arr["find"] === "search_mail_name")
			{
				$params["mail"] = $arr["val"];
				$var = "name";
			}
			elseif($arr["find"] === "search_mail_email")
			{
				$params["CL_ML_MEMBER.name"] = $arr["val"];
				$var = "mail";
			}
			$params["class_id"] = CL_ML_MEMBER;
			$ol = new object_list($params);
			$o = $ol->begin();
			if($o)
			{
				die("ok||".$o->prop($var));
			}
			else
			{
				die();
			}
		}
	}
}
