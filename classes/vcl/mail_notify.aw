<?php
// reminder UI VCL component
/*
@classinfo maintainer=markop
*/
class mail_notify extends core
{
	function mail_notify()
	{
		$this->init("");
		$GLOBALS["add_mod_reforb"] = array("add_selected_people" => "");
		// so I have to somehow check, whether the table I need for saving my things, exists.
	}

	function init_vcl_property($arr)
	{
		$GLOBALS["add_mod_reforb"] = array("add_selected_people" => "");
		$rv = array();

		$rv["mail_notify_toolbar"] = $this->callback_mail_notify_toolbar($arr);

		$rv["mail_notify_table"] =  $this->callback_mail_notify_table($arr);


/*
		$rv["mail_notify_table"] = array(
			"type" => "table",
			"name" => "[mail_notify_table]",
			"caption" => t("Valitud isikud"),
			"value" => $this->_sp_table($arr),
		);

		$rv["mail_notify_find_name"] = array(
			"type" => "textbox",
			"name" => "mail_notify_find_name",
			"caption" => t("Isik"),
			"value" => $search_data["name"],
			"store" => "no",
		);
		$rv["mail_notify_find_company"] = array(
			"type" => "textbox",
			"name" => "mail_notify_find_company",
			"caption" => t("Organisatsioon"),
			"value" => $search_data["co"],
			"store" => "no",
		);
		$rv["mail_notify_find_submit"] = array(
			"type" => "submit",
			"name" => "[mail_notify_find_submit]",
			"caption" => t("Otsi"),
			"value" => t("Otsi"),
			"store" => "no",
		);

		$rv["mail_notify_find_result"] = array(
			"type" => "table",
			"name" => "[mail_notify_find_result]",
			"caption" => t("Otsingu tulemused"),
			"value" => $this->_sp_s_res($arr),
			"store" => "no",
		);
*/
		$rv["mail_notify_mail_settings"] = $this->callback_mail_notify_mail_settings($arr);

		$rv["mail_notify_text"] = $this->callback_mail_notify_text($arr);

		$rv["mail_notify_subject"] = $this->callback_mail_notify_subject($arr);

		$rv["mail_notify_from"] = $this->callback_mail_notify_from($arr);

		$rv["mail_notify_content"] = $this->mail_notify_content($arr);

		return $rv;
	}

	function callback_mail_notify_toolbar($arr)
	{
		$ret = array(
			"type" => "toolbar",
			"name" => "[mail_notify_toolbar]",
			"value" => $this->_sp_tb($arr),
			"store" => "no",
		)  + $arr["prop"];
		unset($ret["vcl_inst"]);
		return $ret;
	}

	function callback_mail_notify_table($arr)
	{
		$ret = array(
			"type" => "table",
			"name" => "[mail_notify_table]",
			"value" => $this->_sp_table($arr),
//			"value" => $this->_get_mail_notify_table($arr),
			"store" => "no",
		) + $arr["prop"];
		unset($ret["vcl_inst"]);
		return $ret;
	}

	function callback_mail_notify_mail_settings($arr)
	{
		return array(
			"type" => "text",
			"name" => "[mail_notify_mail_settings]",
			"subtitle" => 1,
			"value" => t("Maili seaded"),
			"caption" => t("Maili seaded"),
			"store" => "no",
		);
	}

	function callback_mail_notify_text($arr)
	{
		return array(
			"type" => "text",
			"name" => "[Sisu v&otilde;imalike asenduste tekst]",
			"no_caption" => 1,
			"value" => t("V&otilde;imalikud asendused")."\n<br>#file# - ".t("faili nimi"). "\n<br>#file_url# - ".t("link failile")." \n<br>#user_name# - ".t("muutja nimi"),
			"store" => "no",
		);
	}

	function callback_mail_notify_subject($arr)
	{
		return array(
			"type" => "textbox",
			"name" => "mail_notify_subject",
			"caption" => t("Teema"),
			"value" => $this->_get_subject($arr),
			"store" => "no",
		);
	}

	function callback_mail_notify_from($arr)
	{
		return array(
			"type" => "textbox",
			"name" => "mail_notify_from",
			"caption" => t("Kellelt"),
			"value" => $this->_get_from($arr),
			"store" => "no",
		);
	}

	function callback_mail_notify_content($arr)
	{
		return  array(
			"type" => "textarea",
			"cols" => 50,
			"rows" => 5,
			"name" => "mail_notify_content",
			"caption" => t("Sisu"),
			"value" => $this->_get_content($arr),
			"store" => "no",
		);
	}

	function _get_mail_notify_table($arr)
	{
		classload("vcl/table");
		$t = new vcl_table();
		$search_data = $arr["obj_inst"] -> meta("not_mail_ppl_search");

		$t->define_field(array(
			"name" => "search",
			"caption" => "",
			"align" => "left",
			"width" => "180px",
		));
		$t->define_field(array(
			"name" => "results",
			"caption" => "",
			"align" => "center",
		));

		$search = "";

		$search.= t("Isik")."\n<br>";
		$search.= html::textbox(array(
			"name" => "mail_notify_find_name",
			"value" => $search_data["name"],
			"size" => 20
		))."\n<br>\n<br>";

		$search.= t("Organisatsioon")."\n<br>";
		$search.= html::textbox(array(
			"name" => "mail_notify_find_company",
			"value" => $search_data["co"],
			"size" => 20
		))."\n<br>\n<br>";

		$search.= html::submit(array(
				"value" => t("Otsi"),
				"name" => "mail_notify_find_submit",
		));

		$t->define_data(array(
			"search"=> $search,
			"results" => 	$this->_sp_table($arr).
					$this->_sp_s_res($arr),
		));
		return $t->draw();
	}

	function _get_subject($arr)
	{
		if(strlen($arr["obj_inst"]->meta("sp_subject")))
		{
			return $arr["obj_inst"]->meta("sp_subject");
		}
		else
		{
			return t("Teavitus muutunud dokumendist");
		}
	}

	function _get_from($arr)
	{
		if(strlen($arr["obj_inst"]->meta("sp_from")))
		{
			return $arr["obj_inst"]->meta("sp_from");
		}
		else
		{
			return aw_ini_get("baseurl");
		}
	}

	function _get_content($arr)
	{
		if(strlen($arr["obj_inst"]->meta("sp_content")))
		{
			return $arr["obj_inst"]->meta("sp_content");
		}
		else
		{
			$u = get_instance(CL_USER);
			$person = $u->get_person_for_uid(aw_global_get("uid"));
			$user_name = "";
			if(is_object($person))
			{
				$user_name = $person->name();
			}
			return  sprintf(
					t("User %s has added/changed the following file %s. Please click the link below to view the document \n %s") ,
					$user_name ,
					$arr["obj_inst"]->name() ,
					html::get_change_url($arr["obj_inst"]->id())
			);
		}
	}

	function _sp_s_res($arr)
	{
		classload("vcl/table");
		$t = new vcl_table();
		$this->_init_p_tbl($t);
		$t->set_caption(t("Otsingu tulemused"));
		$search_data = $arr["obj_inst"] -> meta("not_mail_ppl_search");

		if ($search_data["name"] != "" || $search_data["co"] != "")
		{
			$param = array(
				"class_id" => CL_CRM_PERSON,
				"lang_id" => array(),
				"site_id" => array(),
				"name" => "%".$search_data["name"]."%"
			);
			if ($arr["request"]["sp_p_co"] != "")
			{
				$param["CL_CRM_PERSON.RELTYPE_CURRENT_JOB.org.name"] = "%".$search_data["co"]."%";
			}
			$ol = new object_list($param);
			foreach($ol->arr() as $p)
			{
				$t->define_data(array(
					"name" => html::obj_change_url($p),
					"co" => html::obj_change_url($p->company_id()),
					"phone" => $p->prop("phone.name"),
					"email" => $p->prop("email.mail") ? html::href(array(
						"url" => "mailto:".$p->prop("email.mail"),
						"caption" => $p->prop("email.mail")
					)) : "",
					"oid" => $p->id()
				));
			}
		}
		if(!$p)
		{
			return "";//tyhja tabeliga ei hakka l2bustama
		}
		return $t->get_html();
	}

	function _init_p_tbl(&$t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "co",
			"caption" => t("Organisatsioon"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "phone",
			"caption" => t("Telefon"),
			"align" => "center",
			"width" => "100px",
		));
		$t->define_field(array(
			"name" => "email",
			"caption" => t("E-mail"),
			"align" => "center",
			"width" => "200px",
		));
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid",
			"width" => "30px",
		));
	}

	function _sp_table($arr)
	{
		classload("vcl/table");
		$t = new vcl_table();
		$this->_init_p_tbl($t);
		foreach($this->get_people_list($arr["obj_inst"]) as $p_id => $p_nm)
		{
			$p = obj($p_id);
			$t->define_data(array(
				"name" => html::obj_change_url($p),
				"co" => html::obj_change_url($p->company_id()),
				"phone" => $p->prop("phone.name"),
				"email" => $p->prop("email.mail") ? html::href(array(
						"url" => "mailto:".$p->prop("email.mail"),
						"caption" => $p->prop("email.mail")
					)) : "",
				"oid" => $p->id()
			));
		}
		if(!$p)//tyhja tabeliga ei hakka l2bustama
		{
			return "";
		}
		$t->set_caption(t("Valitud isikud"));
		return $t->get_html();
	}

	function get_people_list($bt)
	{
		$ret = array();
		$persons = $bt->meta("imp_p");
		$persons = safe_array($persons[aw_global_get("uid")]);

		if (!count($persons))
		{
			return array();
		}

		$ol = new object_list(array(
			"oid" => $persons,
			"lang_id" => array(),
			"site_id" => array(),
			"class_id" => CL_CRM_PERSON,
		));
		$ol2 = new object_list(array(
			"oid" => $persons,
			"lang_id" => array(),
			"site_id" => array(),
			"class_id" => CL_GROUP,
		));
		$ol->add($ol2);
		return $ol->names();
	}

	function _sp_tb()
	{
		classload("vcl/toolbar");
		$tb = new toolbar;
/*		$tb->add_button(array(
			"name" => "save",
			"tooltip" => t("Salvesta"),
			"action" => "add_s_res_to_p_list",
			"img" => "save.gif",
		));*/
		$tb->add_button(array(
			"name" => "delete",
			"tooltip" => t("Kustuta valitute hulgast"),
			"img" => "delete.gif",
			"action" => "remove_p_from_l_list",
		));
		$popup_search = get_instance("vcl/popup_search");
/*		$search_butt = $popup_search->get_popup_search_link(array(
			"pn" => "add_selected_people",
			"clid" => array(CL_CRM_PERSON,CL_GROUP)
		));
*/
//		$tb->add_cdata($search_butt);

		$url1 = $popup_search->mk_my_orb("do_search", array(
			"pn" => "add_selected_people",
			"clid" => CL_CRM_PERSON
		), "popup_search");
		$url2 = $popup_search->mk_my_orb("do_search", array(
			"pn" => "add_selected_people",
			"clid" => CL_GROUP
		), "popup_search");

		$tb->add_menu_button(array(
			"name" => "search",
			"tooltip" => t("Search"),
			"img" => "search.gif",
		));

		$tb->add_menu_item(array(
			"parent" => "search",
			"text" => t("Isikuid"),
			"link" => "javascript:aw_popup_scroll('".$url1."','Search',550,500);",

		));
		$tb->add_menu_item(array(
			"parent" => "search",
			"text" => t("Gruppe"),
			"link" => "javascript:aw_popup_scroll('".$url2."','Search',550,500);",
		));


/*		$tb->add_button(array(
			"name" => "search",
			"tooltip" => t("Otsi isikuid hulgast"),
			"img" => "search.gif",
			"url" => $this->mk_my_orb("do_search", array(
				"pn" => "add_selected_people",
				"clid" => CL_CRM_PERSON
			), "popup_search"),
			//"action" => "remove_p_from_l_list",
		));*/
		$tb->add_separator();
		$tb->add_button(array(
			"name" => "send_mail",
			"tooltip" => t("Saada teavitusmeil"),
			"img" => "",
			"action" => "send_notify_mail",
		));
		return $tb->get_toolbar();
	}

	function process_vcl_property($arr)
	{
		if($arr["request"]["add_selected_people"])
		{
			$ppl = array();
			if(is_oid($arr["request"]["add_selected_people"]))
			{
				$ppl[] = $arr["request"]["add_selected_people"];
			}
			if(substr_count($arr["request"]["add_selected_people"] , ",") > 0)
			{
				foreach(explode("," , $arr["request"]["add_selected_people"]) as $p)
				{
					if(is_oid(trim($p)))
					{
						$ppl[] = trim($p);
					}
				}
			}

			$persons = $arr["obj_inst"]->meta("imp_p");
			foreach($ppl as $p_id)
			{
				$persons[aw_global_get("uid")][$p_id] = $p_id;
			}
			$arr["obj_inst"]->set_meta("imp_p", $persons);
		}

		$arr["obj_inst"]->set_meta("sp_from" , $arr["request"]["mail_notify_from"]);
		$arr["obj_inst"]->set_meta("sp_subject" , $arr["request"]["mail_notify_subject"]);
		$arr["obj_inst"]->set_meta("sp_content" , $arr["request"]["mail_notify_content"]);
		$arr["obj_inst"]->set_meta("not_mail_ppl_search" , array(
			"name" => $arr["request"]["mail_notify_find_name"],
			"co" => $arr["request"]["mail_notify_find_company"],
		));
	}

};
?>
