<?php
// language.aw - Keel
/*

@classinfo syslog_type=ST_LANGUAGE relationmgr=yes no_status=1 no_comment=1  maintainer=kristo

@default table=objects
@default group=general

@groupinfo langs caption="K&otilde;ik keeled"

@tableinfo languages index=oid master_table=objects master_index=oid

@property lang_name table=languages type=textbox field=name
@caption Nimi

@property lang_status table=languages type=status field=status
@caption Aktiivne


@property show_not_logged type=checkbox ch_value=1 prop_cb=1 table=languages field=show_not_logged
@caption N&auml;htav v&auml;lja loginud kasutajatele

@property show_logged type=checkbox ch_value=1 prop_cb=1 table=languages field=show_logged
@caption N&auml;htav sisse loginud kasutajatele

@property show_others type=checkbox ch_value=1 prop_cb=1 table=languages field=show_others
@caption N&auml;htav muudele applikatsioonidele



@property lang_sel_lang type=select store=no
@caption Keel

@property lang_charset table=languages type=select field=charset
@caption Charset

@property lang_acceptlang table=languages type=select field=acceptlang
@caption Keele kood

@property lang_site_id table=languages type=select multiple=1 field=site_id
@caption Saidid kus keel on valitav

@property db_lang_id table=languages type=hidden field=id
@caption Keele ID

@property lang_trans_msg table=languages type=textarea rows=5 cols=30 field=meta method=serialize
@caption T&otilde;lkimata sisu teade

@property lang_img table=languages type=relpicker reltype=RELTYPE_IMAGE field=meta method=serialize
@caption Keele pilt

@property lang_img_act table=languages type=relpicker reltype=RELTYPE_IMAGE field=meta method=serialize
@caption Aktiivse keele pilt

@property fp_text table=languages type=textbox field=meta method=serialize
@caption Esilehe nimi

@property lang_group table=languages type=textbox field=meta method=serialize
@caption Grupp

@property temp_redir_url table=languages type=textbox field=meta method=serialize
@caption Ajutine AW-v&auml;line aadress sisse logimata kasutajatele

@property langs type=table group=langs field=meta method=serialize store=no
@caption Keeled

@groupinfo texts caption="Tekstid"
@default group=texts

@property texts type=table no_caption=1

@groupinfo transl caption=T&otilde;lgi
@default group=transl

	@property transl type=callback callback=callback_get_transl
	@caption T&otilde;lgi

@reltype IMAGE value=1 clid=CL_IMAGE
@caption pilt

*/

class language extends class_base
{
	function language()
	{
		$this->init(array(
			"tpldir" => "languages",
			"clid" => CL_LANGUAGE
		));

		// check if we need to upgrade
		//$tbl = $this->db_get_table("languages");
		/*if (!isset($tbl["fields"]["oid"]))
		{
			die("Keeled tuleb konvertida uuele s&uuml;steemile, seda saab teha ".html::href(array(
				"url" => $this->mk_my_orb("lang_new_convert", array(), "converters"),
				"caption" => t("siit")
			)));
		}*/

		$this->trans_props = array(
			"lang_name", "lang_trans_msg"
		);
	}

	//////
	// class_base classes usually need those, uncomment them if you want to use them
	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{

			case "name":
				return PROP_IGNORE;
				break;

			case "lang_site_id":
				$adm = get_instance("core/languages");
				$sli = get_instance("install/site_list");
				$sl = $adm->_get_sl();
				foreach($sl as $idx => $a)
				{
					$sl[$idx] = $sli->get_url_for_site($idx);
				}
				$prop["options"] = $sl;
				$prop["value"] = isset($prop["value"]) ? $this->make_keys(explode(",",$prop["value"])) : "";
				break;

			case "langs":
				$this->_get_langs_tbl($arr);
				break;

			case "lang_sel_lang":
				$ol = new object_list(array(
					"class_id" => CL_LANGUAGE,
					"site_id" => array(),
					"lang_id" => array()
				));
				$ll = array();
				for($o = $ol->begin(); !$ol->end(); $o = $ol->next())
				{
					$ll[$o->prop("lang_acceptlang")] = true;
				}

				$tmp = aw_ini_get("languages.list");
				$lang_codes = array();
				foreach($tmp as $_lid => $langdata)
				{
					if ($langdata["acceptlang"] == $arr["obj_inst"]->prop("lang_acceptlang"))
					{
						$prop["selected"] = $_lid;
						$lang_codes[$_lid] = $langdata["name"];
					}
					else
					if (!isset($ll[$langdata["acceptlang"]]))
					{
						$lang_codes[$_lid] = $langdata["name"];
					}
				};
				$prop["options"] = $lang_codes;
				break;

			case "lang_acceptlang":
				return PROP_IGNORE;

			case "lang_charset":
				return PROP_IGNORE;

			case "texts":
				$this->do_texts_table($arr);
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

			case "langs":
				$ol = new object_list(array(
					"class_id" => CL_LANGUAGE,
					"status" => array(STAT_ACTIVE, STAT_NOTACTIVE),
					"lang_id" => array(),
					"site_id" => array()
				));
				for($o = $ol->begin(); !$ol->end(); $o = $ol->next())
				{
					if ($arr["request"]["act"][$o->id()] != ($o->prop("lang_status") - 1))
					{
						$o->set_status($arr["request"]["act"][$o->id()] == 1 ? STAT_ACTIVE : STAT_NOTACTIVE);
						$o->set_prop("lang_status", $arr["request"]["act"][$o->id()] == 1 ? STAT_ACTIVE : STAT_NOTACTIVE);
						$o->save();
						$al = get_instance("core/languages");
						$al->set_status($o->prop("db_lang_id"), $arr["request"]["act"][$o->id()] == 1 ? STAT_ACTIVE : STAT_NOTACTIVE);
					}
				}
				if ($arr["request"]["set_sel_lang"] != aw_global_get("lang_id"))
				{
					$l = get_instance("languages");
					$l->set_active($arr["request"]["set_sel_lang"], true);
				}
				break;

			case "lang_sel_lang":
				// set the acceptlang and charset properties based on the selection
				$tmp = aw_ini_get("languages.list");
				$arr["obj_inst"]->set_prop("lang_acceptlang", $tmp[$prop["value"]]["acceptlang"]);
				$arr["obj_inst"]->set_prop("lang_charset", $tmp[$prop["value"]]["charset"]);
				$l = get_instance("core/languages");
				$l->init_cache(true);
				break;

			case "lang_acceptlang":
				return PROP_IGNORE;

			case "lang_charset":
				return PROP_IGNORE;

			case "lang_site_id":
				$prop["value"] = join(",", array_values(is_array($prop["value"]) ? $prop["value"] : array()));
				$arr["obj_inst"]->set_prop("lang_site_id", $prop["value"]);
				return PROP_IGNORE;
				break;

			case "texts":
				$this->save_texts_table($arr);
				break;

			case "lang_status":
				$stat = STAT_NOTACTIVE;
				if ($prop["value"])
				{
					$stat = STAT_ACTIVE;
				}
				$arr["obj_inst"]->set_status($stat);
				break;
		}
		return $retval;
	}

	function callback_pre_save($arr)
	{
		if (!$arr["obj_inst"]->prop("db_lang_id"))
		{
			$id = $this->db_fetch_field("SELECT max(id) as id FROM languages", "id")+1;
			$arr["obj_inst"]->set_prop("db_lang_id", $id);
		}
		$arr["obj_inst"]->set_name($arr["obj_inst"]->prop("lang_name"));
	}

	function callback_post_save()
	{
		$l = get_instance("core/languages");
		$l->init_cache(true);
	}

	function _get_langs_tbl($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "lang",
			"caption" => t("Keel"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "act",
			"caption" => t("Staatus"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "sel",
			"caption" => t("Valitud"),
			"align" => "center"
		));

		$ol = new object_list(array(
			"class_id" => CL_LANGUAGE,
			"status" => array(STAT_ACTIVE, STAT_NOTACTIVE),
			"lang_id" => array(),
			"site_id" => array()
		));
		for($o = $ol->begin(); !$ol->end(); $o = $ol->next())
		{
			$t->define_data(array(
				"lang" => $o->prop("name"),
				"act" => html::checkbox(array(
					"name" => "act[".$o->id()."]",
					"value" => 1,
					"checked" => ($o->prop("lang_status") == 2)
				)),
				"sel" => html::radiobutton(array(
					"name" => "set_sel_lang",
					"value" => $o->prop("db_lang_id"),
					"checked" => (aw_global_get("lang_id") == $o->prop("db_lang_id"))
				))
			));
		}
	}

	function on_site_init($dbi, &$site, &$ini_opts, &$log, &$osi_vars)
	{
		echo "convert langs to new ! <br>\n";
		flush();

		$conv = get_instance("admin/converters");
		$conv->dc = $dbi->dc;
		$conv->lang_new_convert(array(
			"parent" => $osi_vars["langs"]
		));
	}

	function _init_texts_table(&$t)
	{
		$t->define_field(array(
			"name" => "text_name",
			"caption" => t("Muutuja nimi")
		));

		$t->define_field(array(
			"name" => "text_value",
			"caption" => t("Muutuja v&auml;&auml;rtus")
		));

		$t->set_sortable(false);
	}

	function do_texts_table($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_texts_table($t);

		$cnt = 0;
		$txts = new aw_array($arr["obj_inst"]->meta("texts"));
		foreach($txts->get() as $varn => $varv)
		{
			$cnt++;
			$t->define_data(array(
				"text_name" => html::textbox(array(
					"name" => "varvals[$cnt][name]",
					"value" => $varn
				)),
				"text_value" => html::textbox(array(
					"name" => "varvals[$cnt][val]",
					"value" => $varv
				)),
			));
		}

		for($i = 0; $i < 5; $i++)
		{
			$cnt++;
			$t->define_data(array(
				"text_name" => html::textbox(array(
					"name" => "varvals[$cnt][name]",
					"value" => ""
				)),
				"text_value" => html::textbox(array(
					"name" => "varvals[$cnt][val]",
					"value" => ""
				)),
			));
		}
	}

	function save_texts_table($arr)
	{
		$awa = new aw_array($arr["request"]["varvals"]);
		$tarr = $awa->get();

		$texts = array();
		foreach($tarr as $cnt => $dat)
		{
			if ($dat["val"] != "" && $dat["name"] != "")
			{
				$texts[$dat["name"]] = $dat["val"];
			}
		}

		$arr["obj_inst"]->set_meta("texts", $texts);
	}

	function do_insert_texts(&$that)
	{
		$loid = aw_global_get("lang_oid");
		if ($loid)
		{
			aw_disable_acl();
			$o = obj($loid);
			aw_restore_acl();
			$txts = new aw_array($o->meta("texts"));
			$that->vars($txts->get());
		}
	}

	function callback_mod_tab($arr)
	{
		if ($arr["id"] == "transl" && aw_ini_get("user_interface.content_trans") != 1)
		{
			return false;
		}
		return true;
	}

	function callback_get_transl($arr)
	{
		return $this->trans_callback($arr, $this->trans_props);
	}

	/**
		@attrib name=lang_pop
		@param url optional
	**/
	function lang_pop($arr)
	{
		$pm = get_instance("vcl/popup_menu");
		$pm->begin_menu("lang_pop");
		$l = get_instance("languages");
		$ll = $l->get_list();
		foreach($ll as $lid => $ld)
		{
			if (aw_ini_get("user_interface.full_content_trans"))
			{
				$pm->add_item(array(
					"text" => $ld,
					"link" => aw_url_change_var("set_ct_lang_id", $lid, $arr["url"]),
				));
			}
			else
			{
				$pm->add_item(array(
					"text" => $ld,
					"link" => aw_ini_get("baseurl")."/automatweb/index.aw?set_lang_id=".$lid,
				));
			}
		}
		header("Content-type: text/html; charset=".aw_global_get("charset"));
		if (aw_ini_get("user_interface.full_content_trans"))
		{
			$ld = $l->fetch(aw_global_get("ct_lang_id"));
		}
		else
		{
			$ld = $l->fetch(aw_global_get("lang_id"));
		}
		die($pm->get_menu(array(
			"text" => $ld["name"].' <img src="/automatweb/images/aw06/ikoon_nool_alla.gif" alt="#" width="5" height="3" border="0" class="nool" />'
		)));
	}

	function do_db_upgrade($t, $f)
	{
		switch($f)
		{
			case "show_logged":
			case "show_others":
			case "show_not_logged":
				$this->db_add_col($t, array("name" => $f, "type" => "int"));
				return true;
		}
	}
}
?>
