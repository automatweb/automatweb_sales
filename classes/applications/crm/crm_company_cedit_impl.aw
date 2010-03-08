<?php
/*
@classinfo  maintainer=markop
*/
class crm_company_cedit_impl extends core
{
	function crm_company_cedit_impl()
	{
		$this->init();
	}

	function _get_phone_tbl(&$t, $arr)
	{
		$org_fixed = 0;
		$query = $this->parse_url_parse_query($arr["request"]["return_url"]);
		if($query["class"] == "crm_company" && $this->can("view", $query["id"]))
		{
			$org_fixed = $query["id"];
		}
		$pn = "phone_id";
		if ($arr["obj_inst"]->class_id() == CL_CRM_PERSON)
		{
			$pn = "phone";
		}
		$conns = array();
		$cns2wrs = array();
		if (is_oid($arr["obj_inst"]->id()))
		{
			$conns = $arr["obj_inst"]->connections_from(array(
				"type" => "RELTYPE_PHONE",
			));

			$cns2wrs = $arr["obj_inst"]->connections_from(array(
				"type" => 67,		// RELTYPE_CURRENT_JOB
			));
		}
		$i = get_instance(CL_CRM_PHONE);
		$ptypes = $i->get_phone_types();
		$wrs = array();
		foreach($cns2wrs as $cn2wr)
		{
			$wr = $cn2wr->to();
			if($wr->prop("org") != $org_fixed && $org_fixed != 0)
			{
				continue;
			}
			if($this->can("view", $wr->prop("org")))
			{
				$wr_org = obj($wr->prop("org"));
				$wrs[$wr->id()] = $wr_org->name();
			}
			else
			{
				$wrs[$wr->id()] = t("<i>ORGANISATSIOON M&Auml;&Auml;RAMATA</i>");
			}

			if($this->can("view", $wr->prop("profession")))
			{
				$wr_prof = obj($wr->prop("profession"));
				$wrs[$wr->id()] .= ", ".$wr_prof->name()." ";
			}
			else
			{
				$wrs[$wr->id()] .= " ";
			}

			foreach($wr->connections_from(array("type" => 8)) as $cn2ph)
			{
				$conns[$cn2ph->id()] = $cn2ph;
				$cns2phs[$cn2ph->conn["from"]][$cn2ph->conn["to"]] = 1;
			}
		}

		foreach($conns as $conn)
		{
			$obj = $conn->to();
			$obj->conn_id = $conn->id();
			$chooser = html::radiobutton(array(
				"name" => "cedit[".$arr["prop"]["name"]."]",
				"value" => $obj->id(),
				"checked" => $arr["obj_inst"]->prop($pn) == $obj->id()?1:0,
			));
			$ch_url = aw_url_change_var("cedit_tbl_edit", $obj->id());

			if (isset($arr["request"]["cedit_tbl_edit"]) and $arr["request"]["cedit_tbl_edit"] == $obj->id())
			{
				$types = array();
				foreach($ptypes as $_type_id => $_type_val)
				{
					$types[] = html::radiobutton(array(
						"name" => "cedit_phone[".$obj->id()."][type]",
						"checked" => $obj->prop("type") == $_type_id,
						"caption" => $_type_val,
						"value" => $_type_id
					));
				}
				$t->define_data(array(
					"sel" => $obj->id(),
					"choose" => $chooser,
					"number" => html::textbox(array(
						"name" => "cedit_phone[".$obj->id()."][name]",
						"value" => $obj->name(),
						"size" => 15
					)).html::hidden(array(
						"name" => "cedit_phone[".$obj->id()."][conn_id]",
						"value" => $conn->id(),
					)),
					"is_public" => html::checkbox(array(
						"name" => "cedit_phone[".$obj->id()."][is_public]",
						"checked" => $obj->prop("is_public"),
						"value" => 1
					)),
					"type" => html::select(array(
						"name" => "cedit_phone[-1][type]",
						"options" => $ptypes
					)),	//join(" ", $types),
					"change" => html::href(array(
						"caption" => t("Muuda"),
						"url" => $ch_url,
					)),
				));
			}
			else
			{
				$popup_menu = get_instance("vcl/popup_menu");
				$popup_menu->begin_menu("c2wr".$obj->id());
				foreach($wrs as $wr_id => $wr_name)
				{
					$popup_menu->add_item(array(
							"text" => $wr_name.($cns2phs[$wr_id][$obj->id()] == 1 ? t("(eemalda)") : t("(seosta)")),
							"link" => $this->mk_my_orb("c2wr", array(
								"id" => $arr["obj_inst"]->oid,
								"wrid" => $wr_id,
								"toid" => $obj->id(),
								"reltype" => 8,
								"return_url" => get_ru(),
							), CL_CRM_PERSON)
					));
				}
				$popup_menu->add_item(array(
						"text" => t("Isiklik"),
						"link" => $this->mk_my_orb("c2wr", array(
							"id" => $arr["obj_inst"]->oid,
							"wrid" => 0,
							"toid" => $obj->id(),
							"reltype" => 13,
							"return_url" => get_ru(),
						), CL_CRM_PERSON)
				));
				$t->define_data(array(
					"sel" => $obj->id(),
					"choose" => $chooser,
					"number" => $obj->name(),
					"is_public" => $obj->prop("is_public") == 1 ? t("Jah") : t("Ei"),
					"type" => $ptypes[$obj->prop("type")],
					"change" => html::href(array(
						"caption" => t("Muuda"),
						"url" => $ch_url,
					)),
					"rels" => $popup_menu->get_menu(),
				));
			}
		}

		if (empty($arr["request"]["cedit_tbl_edit"]))
		{
			$chooser = html::radiobutton(array(
				"name" => "cedit[".$arr["prop"]["name"]."]",
				"value" => -1,
				"checked" => $this->can("view", $arr["obj_inst"]->prop($pn)) ? 0 : 1,
			));
			$types = array();
			foreach($ptypes as $_type_id => $_type_val)
			{
				$types[] = html::radiobutton(array(
					"name" => "cedit_phone[-1][type]",
					"value" => $_type_id,
					"caption" => $_type_val
				));
			}
			$t->define_data(array(
				"choose" => $chooser,
				"number" => html::textbox(array(
					"name" => "cedit_phone[-1][name]",
					"value" => "",
					"size" => 15
				)),
				"is_public" => html::checkbox(array(
					"name" => "cedit_phone[-1][is_public]",
					"checked" => true,
					"value" => 1
				)),
				"type" => html::select(array(
					"name" => "cedit_phone[-1][type]",
					"options" => $ptypes
				)),//join(" ", $types),
				"change" => ""
			));
		}
		$t->set_sortable(false);
	}

	function _get_fax_tbl(&$t, $arr)
	{
		$org_fixed = 0;
		$query = $this->parse_url_parse_query($arr["request"]["return_url"]);
		if($query["class"] == "crm_company" && $this->can("view", $query["id"]))
		{
			$org_fixed = $query["id"];
		}
		$pn = "telefax_id";
		$tp = "RELTYPE_TELEFAX";
		if ($arr["obj_inst"]->class_id() == CL_CRM_PERSON)
		{
			$pn = "fax";
			$tp = "RELTYPE_FAX";
		}

		$conns = $arr["obj_inst"]->connections_from(array(
			"type" => $tp,
		));

		$cns2wrs = $arr["obj_inst"]->connections_from(array(
			"type" => 67,		// RELTYPE_CURRENT_JOB
		));
		foreach($cns2wrs as $cn2wr)
		{
			$wr = $cn2wr->to();
			if($wr->prop("org") != $org_fixed && $org_fixed != 0)
			{
				continue;
			}
			if($this->can("view", $wr->prop("org")))
			{
				$wr_org = obj($wr->prop("org"));
				$wrs[$wr->id()] = $wr_org->name();
			}
			else
			{
				$wrs[$wr->id()] = t("<i>ORGANISATSIOON M&Auml;&Auml;RAMATA</i>");
			}

			if($this->can("view", $wr->prop("profession")))
			{
				$wr_prof = obj($wr->prop("profession"));
				$wrs[$wr->id()] .= ", ".$wr_prof->name()." ";
			}
			else
			{
				$wrs[$wr->id()] .= " ";
			}

			foreach($wr->connections_from(array("type" => 10)) as $cn2fx)
			{
				$conns[$cn2fx->id()] = $cn2fx;
				$cns2fxs[$cn2fx->conn["from"]][$cn2fx->conn["to"]] = 1;
			}
		}
		$i = get_instance(CL_CRM_PHONE);
		foreach($conns as $conn)
		{
			$obj = $conn->to();
			$obj->conn_id = $conn->id();
			$chooser = html::radiobutton(array(
				"name" => "cedit[".$arr["prop"]["name"]."]",
				"value" => $obj->id(),
				"checked" => $arr["obj_inst"]->prop($pn) == $obj->id()?1:0,
			));
			$ch_url = aw_url_change_var("cedit_tbl_edit_f", $obj->id());

			if (isset($arr["request"]["cedit_tbl_edit_f"]) and $arr["request"]["cedit_tbl_edit_f"] == $obj->id())
			{
				$t->define_data(array(
					"sel" => $obj->id(),
					"choose" => $chooser,
					"number" => html::textbox(array(
						"name" => "cedit_fax[".$obj->id()."][name]",
						"value" => $obj->name(),
						"size" => 15
					)).html::hidden(array(
						"name" => "cedit_phone[".$obj->id()."][conn_id]",
						"value" => $conn->id(),
					)),
					"change" => html::href(array(
						"caption" => t("Muuda"),
						"url" => $ch_url,
					)),
				));
			}
			else
			{
				$popup_menu = get_instance("vcl/popup_menu");
				$popup_menu->begin_menu("c2wr".$obj->id());
				foreach($wrs as $wr_id => $wr_name)
				{
					$popup_menu->add_item(array(
							"text" => $wr_name.($cns2fxs[$wr_id][$obj->id()] == 1 ? t("(eemalda)") : t("(seosta)")),
							"link" => $this->mk_my_orb("c2wr", array(
								"id" => $arr["obj_inst"]->oid,
								"wrid" => $wr_id,
								"toid" => $obj->id(),
								"reltype" => 10,
								"return_url" => get_ru(),
							), CL_CRM_PERSON)
					));
				}
				$popup_menu->add_item(array(
						"text" => t("Isiklik"),
						"link" => $this->mk_my_orb("c2wr", array(
							"id" => $arr["obj_inst"]->oid,
							"wrid" => 0,
							"toid" => $obj->id(),
							"reltype" => 13,
							"return_url" => get_ru(),
						), CL_CRM_PERSON)
				));
				$t->define_data(array(
					"sel" => $obj->id(),
					"choose" => $chooser,
					"number" => $obj->name(),
					"change" => html::href(array(
						"caption" => t("Muuda"),
						"url" => $ch_url,
					)),
					"rels" => $popup_menu->get_menu(),
				));
			}
		}

		if (empty($arr["request"]["cedit_tbl_edit_f"]))
		{
			$chooser = html::radiobutton(array(
				"name" => "cedit[".$arr["prop"]["name"]."]",
				"value" => -1,
				"checked" => $this->can("view", $arr["obj_inst"]->prop($pn)) ? 0 : 1,
			));
			$t->define_data(array(
				"choose" => $chooser,
				"number" => html::textbox(array(
					"name" => "cedit_fax[-1][name]",
					"value" => "",
					"size" => 15
				)),
				"change" => ""
			));
		}
		$t->set_sortable(false);
	}

	function _set_cedit_phone_tbl($arr)
	{
		$pn = "phone_id";
		if ($arr["obj_inst"]->class_id() == CL_CRM_PERSON)
		{
			$pn = "phone";
		}
		foreach(safe_array($arr["request"]["cedit_phone"]) as $id => $data)
		{
			if ($this->can("view", $id))
			{
				$o = obj($id);
				foreach($data as $k => $v)
				{
					$o->set_prop($k, $v);
				}
				$o->set_prop("is_public", $data["is_public"]);
				$o->save();
			}
			else
			if ($id == -1)
			{
				if(!$data["name"]) continue;
				$o = obj();
				$o->set_parent($arr["obj_inst"]->id());
				$o->set_class_id(CL_CRM_PHONE);
				$o->set_name($data["name"]);
				$has = false;
				foreach($data as $k => $v)
				{
					if ($v != "" && $v != "work")
					{
						$has = true;
					}
					$o->set_prop($k, $v);
				}
				if ($has)
				{
					$o->save();
					$arr["obj_inst"]->connect(array(
						"to" => $o->id(),
						"type" => "RELTYPE_PHONE"
					));
					if ($arr["request"]["cedit"]["cedit_phone_tbl"] == -1)
					{
						$arr["obj_inst"]->set_prop($pn, $o->id());
					}
				}
			}
		}

		if ($this->can("view", $arr["request"]["cedit"]["cedit_phone_tbl"]))
		{
			$arr["obj_inst"]->set_prop($pn, $arr["request"]["cedit"]["cedit_phone_tbl"]);
		}
	}

	function _set_cedit_telefax_tbl($arr)
	{
		$pn = "telefax_id";
		$tp = "RELTYPE_TELEFAX";
		if ($arr["obj_inst"]->class_id() == CL_CRM_PERSON)
		{
			$pn = "fax";
			$tp = "RELTYPE_FAX";
		}
		foreach(safe_array($arr["request"]["cedit_fax"]) as $id => $data)
		{
			if ($this->can("view", $id))
			{
				$o = obj($id);
				$o->set_name($data["name"]);
				$o->conn_id = $data["conn_id"];
				$o->save();
			}
			else
			if ($id == -1)
			{
				$o = obj();
				$o->set_parent($arr["obj_inst"]->id());
				$o->set_class_id(CL_CRM_PHONE);
				$o->set_name($data["name"]);
				$has = false;
				foreach($data as $k => $v)
				{
					if ($v != "")
					{
						$has = true;
					}
					$o->set_prop($k, $v);
				}
				if ($has)
				{
					$o->save();
					$arr["obj_inst"]->connect(array(
						"to" => $o->id(),
						"type" => $tp
					));
					if ($arr["request"]["cedit"]["cedit_telefax_tbl"] == -1)
					{
						$arr["obj_inst"]->set_prop($pn, $o->id());
					}
				}
			}
		}

		if ($this->can("view", $arr["request"]["cedit"]["cedit_telefax_tbl"]))
		{
			$arr["obj_inst"]->set_prop($pn, $arr["request"]["cedit"]["cedit_telefax_tbl"]);
		}
	}

	function _get_url_tbl(&$t, $arr)
	{
		$pn = "url_id";
		if ($arr["obj_inst"]->class_id() == CL_CRM_PERSON)
		{
			$pn = "url";
		}
		$conns = $arr["obj_inst"]->connections_from(array(
			"type" => "RELTYPE_URL",
		));
		foreach($conns as $conn)
		{
			$obj = $conn->to();
			$chooser = html::radiobutton(array(
				"name" => "cedit[".$arr["prop"]["name"]."]",
				"value" => $obj->id(),
				"checked" => $arr["obj_inst"]->prop($pn) == $obj->id()?1:0,
			));
			$ch_url = aw_url_change_var("cedit_tbl_edit_u", $obj->id());

			if (isset($arr["request"]["cedit_tbl_edit_u"]) and $arr["request"]["cedit_tbl_edit_u"] == $obj->id())
			{
				$t->define_data(array(
					"sel" => $obj->id(),
					"choose" => $chooser,
					"url" => html::textbox(array(
						"name" => "cedit_url[".$obj->id()."][url]",
						"value" => $obj->name(),
						"size" => 15
					)),
					"change" => html::href(array(
						"caption" => t("Muuda"),
						"url" => $ch_url,
					)),
				));
			}
			else
			{
				$t->define_data(array(
					"sel" => $obj->id(),
					"choose" => $chooser,
					"url" => $obj->name(),
					"change" => html::href(array(
						"caption" => t("Muuda"),
						"url" => $ch_url,
					)),
				));
			}
		}

		if (empty($arr["request"]["cedit_tbl_edit_u"]))
		{
			$chooser = html::radiobutton(array(
				"name" => "cedit[".$arr["prop"]["name"]."]",
				"value" => -1,
				"checked" => $this->can("view", $arr["obj_inst"]->prop($pn)) ? 0 : 1,
			));
			$t->define_data(array(
				"choose" => $chooser,
				"url" => html::textbox(array(
					"name" => "cedit_url[-1][url]",
					"value" => "http://",
					"size" => 15
				)),
				"change" => ""
			));
		}
		$t->set_sortable(false);
	}

	function _set_cedit_url_tbl($arr)
	{
		$pn = "url_id";
		if ($arr["obj_inst"]->class_id() == CL_CRM_PERSON)
		{
			$pn = "url";
		}
		else
		{
			$seti = get_instance(CL_CRM_SETTINGS);
			$sts = $seti->get_current_settings();
		}
		if($sts && $sts->prop("org_link_menu"))
		{
			$parent = $sts->prop("org_link_menu");
		}
		else
		{
			$parent = $arr["obj_inst"]->id();
		}

		foreach(safe_array($arr["request"]["cedit_url"]) as $id => $data)
		{
			if ($this->can("view", $id))
			{
				$o = obj($id);
				$o->set_name($data["url"]);
				$o->set_prop("url",$data["url"]);
				$o->save();
			}
			else
			if ($id == -1)
			{
				$o = obj();
				$o->set_parent($parent);
				$o->set_class_id(CL_EXTLINK);
				$o->set_name($data["url"]);
				$has = false;
				foreach($data as $k => $v)
				{
					if ($v != "" && $v != "http://")
					{
						$has = true;
					}
					$o->set_prop($k, $v);
				}
				if ($has)
				{
					$o->save();
					$arr["obj_inst"]->connect(array(
						"to" => $o->id(),
						"type" => "RELTYPE_URL"
					));
					if ($arr["request"]["cedit"]["cedit_url_tbl"] == -1)
					{
						$arr["obj_inst"]->set_prop($pn, $o->id());
					}
				}
			}
		}

		if ($this->can("view", $arr["request"]["cedit"]["cedit_url_tbl"]))
		{
			$arr["obj_inst"]->set_prop($pn, $arr["request"]["cedit"]["cedit_url_tbl"]);
		}
	}

	function _get_email_tbl(&$t, $arr)
	{
		$org_fixed = 0;
		$mail_inst = get_instance(CL_ML_MEMBER);
		$query = $this->parse_url_parse_query($arr["request"]["return_url"]);
		if($query["class"] == "crm_company" && $this->can("view", $query["id"]))
		{
			$org_fixed = $query["id"];
		}
		$pn = "email_id";
		if ($arr["obj_inst"]->class_id() == CL_CRM_PERSON)
		{
			$pn = "email";
		}

		$conns = array();
		if (is_oid( $arr["obj_inst"]->id()))
		{
			$conns = $arr["obj_inst"]->connections_from(array(
				"type" => "RELTYPE_EMAIL",
			));
		}

		$cns2wrs = array();
		if (is_oid($arr["obj_inst"]->id()))
		{
			$cns2wrs = $arr["obj_inst"]->connections_from(array(
				"type" => 67,		// RELTYPE_CURRENT_JOB
			));
		}

		$wrs = array();
		foreach($cns2wrs as $cn2wr)
		{
			$wr = $cn2wr->to();
			if($wr->prop("org") != $org_fixed && $org_fixed != 0)
			{
				continue;
			}
			if($this->can("view", $wr->prop("org")))
			{
				$wr_org = obj($wr->prop("org"));
				$wrs[$wr->id()] = $wr_org->name();
			}
			else
			{
				$wrs[$wr->id()] = t("<i>ORGANISATSIOON M&Auml;&Auml;RAMATA</i>");
			}

			if($this->can("view", $wr->prop("profession")))
			{
				$wr_prof = obj($wr->prop("profession"));
				$wrs[$wr->id()] .= ", ".$wr_prof->name()." ";
			}
			else
			{
				$wrs[$wr->id()] .= " ";
			}

			foreach($wr->connections_from(array("type" => 9)) as $cn2ml)
			{
				$conns[$cn2ml->id()] = $cn2ml;
				$cns2mls[$cn2ml->conn["from"]][$cn2ml->conn["to"]] = 1;
			}
		}

		foreach($conns as $conn)
		{
			$obj = $conn->to();
			$obj->conn_id = $conn->id();
			$chooser = html::radiobutton(array(
				"name" => "cedit[".$arr["prop"]["name"]."]",
				"value" => $obj->id(),
				"checked" => $arr["obj_inst"]->prop($pn) == $obj->id()?1:0,
			));
			$ch_url = aw_url_change_var("cedit_tbl_edit_e", $obj->id());

			if (isset($arr["request"]["cedit_tbl_edit_e"]) and $arr["request"]["cedit_tbl_edit_e"] == $obj->id())
			{
				$t->define_data(array(
					"sel" => $obj->id(),
					"choose" => $chooser,
					"email" => html::textbox(array(
						"name" => "cedit_email[".$obj->id()."][email]",
						"value" => $obj->prop("mail"),
						"size" => 15
					)).html::hidden(array(
						"name" => "cedit_email[".$obj->id()."][conn_id]",
						"value" => $conn->id(),
					)),
					"type" => html::select(array(
						"caption" => t("T&uuml;&uuml;p"),
						"name" => "cedit_email[".$obj->id()."][type]",
						"value" => $obj->prop("contact_type"),
						"options" => $mail_inst->types,
					)),
					"change" => html::href(array(
						"caption" => t("Muuda"),
						"url" => $ch_url,
					)),
				));
			}
			else
			{
				$popup_menu = get_instance("vcl/popup_menu");
				$popup_menu->begin_menu("c2wr".$obj->id());
				foreach($wrs as $wr_id => $wr_name)
				{
					$popup_menu->add_item(array(
							"text" => $wr_name.($cns2mls[$wr_id][$obj->id()] == 1 ? t("(eemalda)") : t("(seosta)")),
							"link" => $this->mk_my_orb("c2wr", array(
								"id" => $arr["obj_inst"]->oid,
								"wrid" => $wr_id,
								"toid" => $obj->id(),
								"reltype" => 9,
								"return_url" => get_ru(),
							), CL_CRM_PERSON)
					));
				}
				$popup_menu->add_item(array(
						"text" => t("Isiklik"),
						"link" => $this->mk_my_orb("c2wr", array(
							"id" => $arr["obj_inst"]->oid,
							"wrid" => 0,
							"toid" => $obj->id(),
							"reltype" => 11,
							"return_url" => get_ru(),
						), CL_CRM_PERSON)
				));
				$t->define_data(array(
					"sel" => $obj->id(),
					"choose" => $chooser,
					"email" => $obj->prop("mail"),
					"change" => html::href(array(
						"caption" => t("Muuda"),
						"url" => $ch_url,
					)),
					"type" => $mail_inst->types[$obj->prop("contact_type")],
					"rels" => $popup_menu->get_menu(),
				));
			}
		}

		if (empty($arr["request"]["cedit_tbl_edit_e"]))
		{
			$chooser = html::radiobutton(array(
				"name" => "cedit[".$arr["prop"]["name"]."]",
				"value" => -1,
				"checked" => $this->can("view", $arr["obj_inst"]->prop($pn)) ? 0 : 1,
			));
			$t->define_data(array(
				"choose" => $chooser,
				"email" => html::textbox(array(
					"name" => "cedit_email[-1][email]",
					"value" => "",
					"size" => 15
				)),
				"type" => html::select(array(
					"caption" => t("T&uuml;&uuml;p"),
					"name" => "cedit_email[-1][type]",
					"options" => $mail_inst->types,
				)),
				"change" => ""
			));
		}
		$t->define_field(array("name" => "type" , "caption" => t("T&uuml;&uuml;p")));
		$t->set_sortable(false);
	}

	function _get_profession_tbl(&$t, $arr)
	{
		$pn = "profession_id";
		if ($arr["obj_inst"]->class_id() == CL_CRM_PERSON)
		{
			$pn = "profession";
		}
		if (is_oid($arr["obj_inst"]->id()))
		{
			$conns = $arr["obj_inst"]->connections_from(array(
				"type" => "RELTYPE_PREVIOUS_JOB",
			));
		}
		else
		{
			$conns = array();
		}
		foreach($conns as $conn)
		{
			$obj = $conn->to();
			$chooser = html::radiobutton(array(
				"name" => "cedit[".$arr["prop"]["name"]."]",
				"value" => $obj->id(),
				"checked" => $arr["obj_inst"]->prop($pn) == $obj->id()?1:0,
			));
			$ch_url = aw_url_change_var("cedit_tbl_edit_e", $obj->id());

			if (isset($arr["request"]["cedit_tbl_edit_e"]) and $arr["request"]["cedit_tbl_edit_e"] == $obj->id())
			{
				$t->define_data(array(
					"sel" => $obj->id(),
					"choose" => $chooser,
					"email" => html::textbox(array(
						"name" => "cedit_email[".$obj->id()."][email]",
						"value" => $obj->prop("mail"),
						"size" => 15
					)),
					"change" => html::href(array(
						"caption" => t("Muuda"),
						"url" => $ch_url,
					)),
				));
			}
			else
			{
				if(is_oid($obj->prop("org")))
				{
					$org_obj = new object($obj->prop("org"));
					$org_data = $org_obj->name();
				}
				else
				{
					$org_data = "";
				}
				if(is_oid($obj->prop("profession")))
				{
					$profession_obj = new object($obj->prop("profession"));
					$profession_data = $profession_obj->name();
				}
				else
				{
					$profession_data = "";
				}
				$start_data = $obj->prop("start");
				$end_data = $obj->prop("end");
				$ch_url_profession = $this->mk_my_orb(
					"change",
					array(
						"id" => $obj->id(),
						"return_url" => aw_ini_get("baseurl").aw_global_get("REQUEST_URI"),
					),
					"crm_person_work_relation"
				);
				$t->define_data(array(
					"sel" => $obj->id(),
					"choose" => $chooser,
					"org" => $org_data,
					"profession" => $profession_data,
					"start" => (empty($start_data) || $end_data < $start_data) ? t("M&auml;&auml;ramata") : date("d.m.Y", $start_data),
					"end" => (empty($end_data) || $end_data < $start_data) ? t("M&auml;&auml;ramata") : date("d.m.Y", $end_data),
					"change" => html::href(array(
						"caption" => t("Muuda"),
						"url" => $ch_url_profession,
					)),
				));
			}
		}

		if (empty($arr["request"]["cedit_tbl_edit_e"]))
		{
			/*
			$chooser = html::radiobutton(array(
				"name" => "cedit[".$arr["prop"]["name"]."]",
				"value" => -1,
				"checked" => $this->can("view", $arr["obj_inst"]->prop($pn)) ? 0 : 1,
			));
			$t->define_data(array(
				"choose" => $chooser,
				"org" => html::textbox(array(
					"name" => "cedit_email[-1][email]",
					"value" => "",
					"size" => 15
				)),
				"change" => ""
			));
			*/
		}
		$t->set_sortable(false);
	}

	function _set_cedit_email_tbl($arr)
	{
		$pn = "email_id";
		if ($arr["obj_inst"]->class_id() == CL_CRM_PERSON)
		{
			$pn = "email";
		}
		foreach(safe_array($arr["request"]["cedit_email"]) as $id => $data)
		{
			if ($this->can("view", $id))
			{
				$o = obj($id);
				$o->set_name($data["email"]);
				$o->set_prop("mail",$data["email"]);
				$o->set_prop("contact_type" , $data["type"]);
				$o->conn_id = $data["conn_id"];
				$o->save();
			}
			else
			if ($id == -1)
			{
				$o = obj();
				$o->set_parent($arr["obj_inst"]->id());
				$o->set_class_id(CL_ML_MEMBER);
				$o->set_name($data["email"]);
				$o->set_prop("contact_type" , $data["type"]);
				$has = false;
				foreach($data as $k => $v)
				{
					if($k != "email")
					{
						continue;
					}
					if ($v != "")
					{
						$has = true;
					}
					if($k == "email")
					{
						$o->set_prop("mail", $v);
					}
				}
				if ($has)
				{
					$o->save();
					$arr["obj_inst"]->connect(array(
						"to" => $o->id(),
						"type" => "RELTYPE_EMAIL"
					));
					if ($arr["request"]["cedit"]["cedit_email_tbl"] == -1)
					{
						$arr["obj_inst"]->set_prop($pn, $o->id());
					}
				}
			}
		}

		if ($this->can("view", $arr["request"]["cedit"]["cedit_email_tbl"]))
		{
			$arr["obj_inst"]->set_prop($pn, $arr["request"]["cedit"]["cedit_email_tbl"]);
		}
	}


	function _get_acct_tbl(&$t, $arr)
	{
		$conns = $arr["obj_inst"]->connections_from(array(
			"type" => "RELTYPE_BANK_ACCOUNT",
		));
		$banks_ol = new object_list(array(
			"class_id" => CL_CRM_BANK,
			"lang_id" => array(),
			"site_id" => array()
		));
		$banks = array("" => t("--vali--")) + $banks_ol->names();
		foreach($conns as $conn)
		{
			$obj = $conn->to();
			$chooser = html::radiobutton(array(
				"name" => "cedit[".$arr["prop"]["name"]."]",
				"value" => $obj->id(),
				"checked" => $arr["obj_inst"]->prop("aw_bank_account") == $obj->id()?1:0,
			));
			$ch_url = aw_url_change_var("cedit_tbl_edit_a", $obj->id());

			if (isset($arr["request"]["cedit_tbl_edit_a"]) and $arr["request"]["cedit_tbl_edit_a"] == $obj->id())
			{
				$t->define_data(array(
					"sel" => $obj->id(),
					"choose" => $chooser,
					"name" => html::textbox(array(
						"name" => "cedit_acct[".$obj->id()."][name]",
						"value" => $obj->name(),
						"size" => 15
					)),
					"account" => html::textbox(array(
						"name" => "cedit_acct[".$obj->id()."][acct_no]",
						"value" => $obj->prop("acct_no"),
						"size" => 15
					)),
					"bank" => html::select(array(
						"name" => "cedit_acct[".$obj->id()."][bank]",
						"value" => $obj->prop("bank"),
						"options" => $banks
					)),
					"office_code" => html::textbox(array(
						"name" => "cedit_acct[".$obj->id()."][sort_code]",
						"value" => $obj->prop("sort_code"),
						"size" => 15
					)),
					"change" => html::href(array(
						"caption" => t("Muuda"),
						"url" => $ch_url,
					)),
				));
			}
			else
			{
				$t->define_data(array(
					"sel" => $obj->id(),
					"choose" => $chooser,
					"name" => $obj->name(),
					"account" => $obj->prop("acct_no"),
					"bank" => $obj->prop("bank.name"),
					"office_code" => $obj->prop("sort_code"),
					"change" => html::href(array(
						"caption" => t("Muuda"),
						"url" => $ch_url,
					)),
				));
			}
		}
		if (empty($arr["request"]["cedit_tbl_edit_a"]))
		{
			$chooser = html::radiobutton(array(
				"name" => "cedit[".$arr["prop"]["name"]."]",
				"value" => -1,
				"checked" => $this->can("view", $arr["obj_inst"]->prop("aw_bank_account")) ? 0 : 1,
			));
			$t->define_data(array(
				"choose" => $chooser,
				"name" => html::textbox(array(
					"name" => "cedit_acct[-1][name]",
					"value" => "",
					"size" => 15
				)),
				"account" => html::textbox(array(
					"name" => "cedit_acct[-1][acct_no]",
					"value" => "",
					"size" => 15
				)),
				"bank" => html::select(array(
					"name" => "cedit_acct[-1][bank]",
					"value" => "",
					"options" => $banks
				)),
				"office_code" => html::textbox(array(
					"name" => "cedit_acct[-1][sort_code]",
					"value" => "",
					"size" => 15
				)),
				"change" => ""
			));
		}
		$t->set_sortable(false);
	}

	function _set_cedit_bank_account_tbl($arr)
	{
		foreach(safe_array($arr["request"]["cedit_acct"]) as $id => $data)
		{
			if ($this->can("view", $id))
			{
				$o = obj($id);
				$o->set_name($data["name"]);
				foreach($data as $k => $v)
				{
					$o->set_prop($k, $v);
				}
				$o->save();
			}
			else
			if ($id == -1)
			{
				$o = obj();
				$o->set_parent($arr["obj_inst"]->id());
				$o->set_class_id(CL_CRM_BANK_ACCOUNT);
				$o->set_name($data["name"]);
				$has = false;
				foreach($data as $k => $v)
				{
					if ($v != "")
					{
						$has = true;
					}
					$o->set_prop($k, $v);
				}
				if ($has)
				{
					$o->save();
					$arr["obj_inst"]->connect(array(
						"to" => $o->id(),
						"type" => "RELTYPE_BANK_ACCOUNT"
					));
					if ($arr["request"]["cedit"]["cedit_bank_account_tbl"] == -1)
					{
						$arr["obj_inst"]->set_prop("aw_bank_account", $o->id());
					}
				}
			}
		}

		if ($this->can("view", $arr["request"]["cedit"]["cedit_bank_account_tbl"]))
		{
			$arr["obj_inst"]->set_prop("aw_bank_account", $arr["request"]["cedit"]["cedit_bank_account_tbl"]);
		}
	}

	function init_cedit_tables($t, $fields)
	{
		$t->define_chooser(array(
			"name" => "select",
			"field" => "sel",
			"width" => "60",
		));
		foreach($fields as $name => $caption)
		{
			if($name === "choose")
			{
				$width = "10%";
			}
			elseif($name === "change")
			{
				$width = "60px";
			}
			else
			{
				$width = "";
			}
			$t->define_field(array(
				"name" => $name,
				"caption" => $caption,
				"width" => $width,
			));
		}
		$t->define_field(array(
			"name" => "change",
			"caption" => t("Muuda"),
			"width" => "80",
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "choose",
			"caption" => t("Vali &uuml;ks"),
			"width" => "60",
			"align" => "center",
		));
	}

	function _get_adr_tbl(&$t, $arr)
	{
		$conns = array();
		if (is_oid($arr["obj_inst"]->id()))
		{
			$conns = $arr["obj_inst"]->connections_from(array(
				"type" => "RELTYPE_ADDRESS",
			));
		}

		$pp = "contact";
		if (is_oid($arr["obj_inst"]->id()) && !$arr["obj_inst"]->is_property("contact"))
		{
			$pp = "address";
		}

		foreach($conns as $conn)
		{
			$obj = $conn->to();
			$chooser = html::radiobutton(array(
				"name" => "cedit[".$arr["prop"]["name"]."]",
				"value" => $obj->id(),
				"checked" => $arr["obj_inst"]->prop($pp) == $obj->id()?1:0,
			));
			$ch_url = aw_url_change_var("cedit_tbl_edit_a", $obj->id());

			if (isset($arr["request"]["cedit_tbl_edit_a"]) and $arr["request"]["cedit_tbl_edit_a"] == $obj->id())
			{
				$t->define_data(array(
					"sel" => $obj->id(),
					"choose" => $chooser,
					"aadress" => html::textbox(array(
						"name" => "cedit_adr[".$obj->id()."][aadress]",
						"value" => $obj->prop("aadress"),
						"size" => 15
					)),
					"postiindeks" => html::textbox(array(
						"name" => "cedit_adr[".$obj->id()."][postiindeks]",
						"value" => $obj->prop("postiindeks"),
						"size" => 15
					)),
					"linn" => html::textbox(array(
						"name" => "cedit_adr[".$obj->id()."][linn]",
						"value" => $obj->prop("linn.name"),
						"size" => 15,
						"autocomplete_source" => $this->mk_my_orb("adr_city_ac"),
						"autocomplete_params" => array("cedit_adr[".$obj->id()."][linn]")
					)),
					"maakond" => html::textbox(array(
						"name" => "cedit_adr[".$obj->id()."][maakond]",
						"value" => $obj->prop("maakond.name"),
						"size" => 15,
						"autocomplete_source" => $this->mk_my_orb("adr_city_ac"),
						"autocomplete_params" => array("cedit_adr[".$obj->id()."][maakond]")
					)),
					"piirkond" => html::textbox(array(
						"name" => "cedit_adr[".$obj->id()."][piirkond]",
						"value" => $obj->prop("piirkond.name"),
						"size" => 15,
						"autocomplete_source" => $this->mk_my_orb("adr_city_ac"),
						"autocomplete_params" => array("cedit_adr[".$obj->id()."][piirkond]")
					)),
					"riik" => html::textbox(array(
						"name" => "cedit_adr[".$obj->id()."][riik]",
						"value" => $obj->prop("riik.name"),
						"size" => 15,
						"autocomplete_source" => $this->mk_my_orb("adr_city_ac"),
						"autocomplete_params" => array("cedit_adr[".$obj->id()."][riik]")
					)),
					"change" => html::href(array(
						"caption" => t("Muuda"),
						"url" => $ch_url,
					)),
				));
			}
			else
			{
				$t->define_data(array(
					"sel" => $obj->id(),
					"choose" => $chooser,
					"aadress" => $obj->prop("aadress"),
					"postiindeks" => $obj->prop_str("postiindeks"),
					"linn" => $obj->prop("linn.name"),
					"maakond" => $obj->prop("maakond.name"),
					"piirkond" => $obj->prop("piirkond.name"),
					"riik" => $obj->prop("riik.name"),
					"change" => html::href(array(
						"caption" => t("Muuda"),
						"url" => $ch_url,
					)),
				));
			}
		}
		if (empty($arr["request"]["cedit_tbl_edit_a"]))
		{
			$chooser = html::radiobutton(array(
				"name" => "cedit[".$arr["prop"]["name"]."]",
				"value" => -1,
				"checked" => $this->can("view", $arr["obj_inst"]->prop($pp)) ? 0 : 1,
			));
			$t->define_data(array(
				"choose" => $chooser,
				"aadress" => html::textbox(array(
					"name" => "cedit_adr[-1][aadress]",
					"value" => "",
					"size" => 15
				)),
				"postiindeks" => html::textbox(array(
					"name" => "cedit_adr[-1][postiindeks]",
					"value" => "",
					"size" => 15
				)),
				"linn" => html::textbox(array(
					"name" => "cedit_adr[-1][linn]",
					"value" => "",
					"size" => 15,
					"autocomplete_source" => $this->mk_my_orb("adr_city_ac"),
					"autocomplete_params" => array("cedit_adr[-1][linn]")
				)),
				"maakond" => html::textbox(array(
					"name" => "cedit_adr[-1][maakond]",
					"value" => "",
					"size" => 15,
					"autocomplete_source" => $this->mk_my_orb("adr_city_ac"),
					"autocomplete_params" => array("cedit_adr[-1][maakond]")
				)),
				"piirkond" => html::textbox(array(
					"name" => "cedit_adr[-1][piirkond]",
					"value" => "",
					"size" => 15,
					"autocomplete_source" => $this->mk_my_orb("adr_city_ac"),
					"autocomplete_params" => array("cedit_adr[-1][piirkond]")
				)),
				"riik" => html::textbox(array(
					"name" => "cedit_adr[-1][riik]",
					"value" => "",
					"size" => 15,
					"autocomplete_source" => $this->mk_my_orb("adr_city_ac"),
					"autocomplete_params" => array("cedit_adr[-1][riik]")
				)),
				"change" => ""
			));
		}
		$t->set_sortable(false);
	}

	function _set_cedit_adr_tbl($arr)
	{
		foreach(safe_array($arr["request"]["cedit_adr"]) as $id => $data)
		{
			if ($this->can("view", $id))
			{
				$o = obj($id);
				$o->set_prop("aadress", $data["aadress"]);
				$o->set_prop("postiindeks", $data["postiindeks"]);
				$this->set_rel_by_val($o, "linn", $data["linn"]);
				$this->set_rel_by_val($o, "maakond", $data["maakond"]);
				$this->set_rel_by_val($o, "piirkond", $data["piirkond"]);
				$this->set_rel_by_val($o, "riik", $data["riik"]);
				$i = get_instance(CL_CRM_ADDRESS);
				$o->set_name($i->get_name_from_adr($o));
				$o->save();
			}
			else
			if ($id == -1)
			{
				$o = obj();
				$o->set_parent($arr["obj_inst"]->id());
				$o->set_class_id(CL_CRM_ADDRESS);
				$has = false;
				foreach($data as $k => $v)
				{
					if ($v != "")
					{
						$has = true;
					}
				}
				$o->set_prop("aadress", $data["aadress"]);
				$o->set_prop("postiindeks", $data["postiindeks"]);
				$this->set_rel_by_val($o, "linn", $data["linn"]);
				$this->set_rel_by_val($o, "maakond", $data["maakond"]);
				$this->set_rel_by_val($o, "piirkond", $data["piirkond"]);
				$this->set_rel_by_val($o, "riik", $data["riik"]);

				if ($has)
				{
					$i = get_instance(CL_CRM_ADDRESS);
					$o->set_name($i->get_name_from_adr($o));
					$o->save();
					$arr["obj_inst"]->connect(array(
						"to" => $o->id(),
						"type" => "RELTYPE_ADDRESS"
					));
					if ($arr["request"]["cedit"]["cedit_adr_tbl"] == -1)
					{
						if ($arr["obj_inst"]->is_property("contact"))
						{
							$arr["obj_inst"]->set_prop("contact", $o->id());
						}
						else
						{
							$arr["obj_inst"]->set_prop("address", $o->id());
						}
					}
				}
			}
		}

		if ($this->can("view", $arr["request"]["cedit"]["cedit_adr_tbl"]))
		{
			if ($arr["obj_inst"]->is_property("contact"))
			{
				$arr["obj_inst"]->set_prop("contact", $arr["request"]["cedit"]["cedit_adr_tbl"]);
			}
			else
			{
				$arr["obj_inst"]->set_prop("address", $arr["request"]["cedit"]["cedit_adr_tbl"]);
			}
		}
	}

	function set_rel_by_val($o, $prop, $val)
	{
		$pl = $o->get_property_list();
		$reli = $o->get_relinfo();
		$p = $pl[$prop];
		$clid = $reli[$p["reltype"]]["clid"][0];
		$ol = new object_list(array(
			"class_id" => $clid,
			"lang_id" => array(),
			"site_id" => array(),
			"name" => $val
		));
		if ($ol->count())
		{
			$fo = $ol->begin();
		}
		else
		{
			$fo = obj();
			$fo->set_class_id($clid);
			$fo->set_parent($o->id() ? $o->id() : $_POST["id"]);
			$fo->set_name($val);
			$fo->save();
		}
		$o->set_prop($prop, $fo->id());
	}

	/**
		@attrib name=adr_city_ac all_args=1
	**/
	function adr_city_ac($arr)
	{
		header ("Content-Type: text/html; charset=" . aw_global_get("charset"));
		$cl_json = get_instance("protocols/data/json");

		$errorstring = "";
		$error = false;
		$autocomplete_options = array();

		$option_data = array(
			"error" => &$error,// recommended
			"errorstring" => &$errorstring,// optional
			"options" => &$autocomplete_options,// required
			"limited" => false,// whether option count limiting applied or not. applicable only for real time autocomplete.
		);

		$bit = "";
		$c_lut = array(
			"linn" => CL_CRM_CITY,
			"maakond" => CL_CRM_COUNTY,
			"piirkond" => CL_CRM_AREA,
			"riik" => CL_CRM_COUNTRY
		);
		if (is_array($arr["cedit_adr"]))
		{
			foreach($arr["cedit_adr"] as $lar)
			{
				foreach($lar as $k => $v)
				{
					$bit = $v;
					$clid = $c_lut[$k];
				}
			}
		}
		$ol = new object_list(array(
			"class_id" => $clid,
			"name" => iconv("UTF-8", aw_global_get("charset"), $bit)."%",
			"lang_id" => array(),
			"site_id" => array(),
			"limit" => 500,
		));
		$autocomplete_options = $ol->names();
		foreach($autocomplete_options as $k => $v)
		{
			$autocomplete_options[$k] = iconv(aw_global_get("charset"), "UTF-8", parse_obj_name($v));
		}
		$autocomplete_options = array_unique($autocomplete_options);
		header("Content-type: text/html; charset=utf-8");
		exit ($cl_json->encode($option_data));
	}

	function parse_url_parse_query($return_url)
	{
		$url = parse_url($return_url);
		$query = explode("&", $url["query"]);
		foreach($query as $q)
		{
			$t = explode("=", $q);
			$ret[$t[0]] = isset($t[1]) ? $t[1] : "";
		}
		return $ret;
	}

}
