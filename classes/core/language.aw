<?php
// language.aw - Keel

/*

@classinfo relationmgr=yes no_status=1 no_comment=1
@tableinfo languages index=oid master_table=objects master_index=oid


@default table=objects

@default group=general
	@property aw_lang_id table=languages type=hidden field=aw_lid
	@caption Keele ID

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

	@property lang_sel_lang type=select table=languages field=lang_code
	@caption Keel

	@property lang_acceptlang table=languages type=select field=acceptlang
	@caption Keele kood

	@property lang_site_id table=languages type=select multiple=1 field=site_id
	@caption Saidid kus keel on valitav

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



@groupinfo langs caption="K&otilde;ik keeled"
@default group=langs
	@property langs type=table field=meta method=serialize store=no
	@caption Keeled


//DEPRECATED
@groupinfo texts caption="Tekstid"
@default group=texts
	//DEPRECATED
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

		$this->trans_props = array(
			"lang_name", "lang_trans_msg"
		);
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = class_base::PROP_OK;
		switch($prop["name"])
		{

			case "name":
				$retval = class_base::PROP_IGNORE;
				break;

			case "lang_site_id":
				$sl = site_list::get_local_site_ids();

				if (count($sl))
				{
					$sli = new site_list();
					foreach($sl as $site_id)
					{
						$sl[$site_id] = $sli->get_url_for_site($site_id) ? $sli->get_url_for_site($site_id) : $site_id;
					}
					$prop["options"] = $sl;
					$prop["value"] = isset($prop["value"]) ? $this->make_keys(explode(",", $prop["value"])) : "";
				}
				elseif (!empty($prop["value"]))
				{
					$prop["type"] = "text";
				}
				else
				{
					$retval = class_base::PROP_IGNORE;
				}
				break;

			case "langs":
				$this->_get_langs_tbl($arr);
				break;

			case "lang_sel_lang":
				if ($this->awcb_ds_id->is_saved() and $id = $this->awcb_ds_id->prop("aw_lang_id"))
				{
					$prop["type"] = "text";
					$prop["value"] = aw_ini_get("languages.list.{$id}.name");
				}
				else
				{
					$languages = aw_ini_get("languages.list");
					foreach($languages as $aw_lid => $langdata)
					{
						$languages[$aw_lid] = $langdata["name"];
					}
					$prop["options"] = $languages;
				}
				break;

			case "lang_acceptlang":
				$retval = class_base::PROP_IGNORE;
				break;

			case "texts":
				$this->do_texts_table($arr);
				break;
		}
		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = class_base::PROP_OK;
		switch($prop["name"])
		{
			case "langs":
				$ol = new object_list(array(
					"class_id" => CL_LANGUAGE
				));
				for($o = $ol->begin(); !$ol->end(); $o = $ol->next())
				{
					$changed = false;
					if (!empty($arr["request"]["act"][$o->id()]) xor object::STAT_ACTIVE == $o->prop("lang_status"))
					{
						$new_status = empty($arr["request"]["act"][$o->id()]) ? object::STAT_NOTACTIVE : object::STAT_ACTIVE;
						$o->set_status($new_status);
						$o->set_prop("lang_status", $new_status);
						$changed = true;
						languages::set_status($o->prop("aw_lang_id"), $new_status);
					}

					if ($changed)
					{
						$o->save();
					}
				}
				break;

			case "lang_acceptlang":
				$retval = class_base::PROP_IGNORE;
				break;

			case "lang_site_id":
				$prop["value"] = join(",", array_values(is_array($prop["value"]) ? $prop["value"] : array()));
				$arr["obj_inst"]->set_prop("lang_site_id", $prop["value"]);
				$retval = class_base::PROP_IGNORE;
				break;

			case "lang_sel_lang":
				if ($arr["new"])
				{
					$arr["obj_inst"]->set_prop("aw_lang_id", $prop["value"]);
				}
				$retval = class_base::PROP_IGNORE;
				break;

			case "texts":
				$this->save_texts_table($arr);
				break;

			case "lang_status":
				$stat = object::STAT_NOTACTIVE;
				if ($prop["value"])
				{
					$stat = object::STAT_ACTIVE;
				}
				$arr["obj_inst"]->set_status($stat);
				break;
		}
		return $retval;
	}

	function _get_langs_tbl($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "lang",
			"caption" => t("Keel"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "act",
			"caption" => t("Aktiivne"),
			"align" => "center"
		));

		$ol = new object_list(array(
			"class_id" => CL_LANGUAGE
		));
		for($o = $ol->begin(); !$ol->end(); $o = $ol->next())
		{
			$t->define_data(array(
				"lang" => $o->prop("name"),
				"act" => html::checkbox(array(
					"name" => "act[".$o->id()."]",
					"value" => 1,
					"checked" => ($o->prop("lang_status") == 2)
				))
			));
		}
	}

	//DEPRECATED
	function _init_texts_table($t)
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

	//DEPRECATED
	function do_texts_table($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
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

	//DEPRECATED
	function save_texts_table($arr)
	{return;
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

	//DEPRECATED
	function do_insert_texts($that)
	{
		$loid = aw_global_get("lang_oid");
		if ($loid)
		{
			$o = obj($loid);
			$txts = new aw_array($o->meta("texts"));
			$that->vars($txts->get());
		}
	}

	function callback_mod_tab($arr)
	{
		if ($arr["id"] === "transl" && aw_ini_get("user_interface.content_trans") != 1)
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
		$pm = new popup_menu();
		$pm->begin_menu("lang_pop");
		$ll = languages::get_list();
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
				$url = core::mk_my_orb("set_active", array(
					"id" => $lid,
					"return_url" => empty($arr["url"]) ? aw_ini_get("baseurl") : $arr["url"]
				), "languages");

				$pm->add_item(array(
					"text" => $ld,
					"link" => $url
				));
			}
		}

		header("Content-type: text/html; charset=".AW_USER_CHARSET);
		if (aw_ini_get("user_interface.full_content_trans"))
		{
			$ld = languages::fetch(aw_global_get("ct_lang_id"));
		}
		else
		{
			$ld = languages::fetch(aw_global_get("lang_id"));
		}
		die($pm->get_menu(array(
			"text" => $ld["name"] . ' <img src="' . aw_ini_get("baseurl") . 'automatweb/images/aw06/ikoon_nool_alla.gif" alt="#" width="5" height="3" border="0" class="nool" />'
		)));
	}

	function do_db_upgrade($t, $f)
	{
		$r = false;
		if ("languages" === $t)
		{
			if (
				"show_logged" === $f or
				"show_others" === $f or
				"show_not_logged" === $f
			)
			{
				$this->db_add_col($t, array("name" => $f, "type" => "int"));
				$r = true;
			}
			elseif ("lang_code" === $f)
			{
				$this->db_add_col("languages", array("name" => "lang_code", "type" => "char(3)"));
				$r = true;
			}
		}
		return $r;
	}
}
