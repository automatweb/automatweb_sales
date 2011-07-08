<?php

class keyword_selector extends class_base
{
	function keyword_selector()
	{
		$this->init("vcl/keyword_selector");
	}

	function init_vcl_property($arr)
	{
		$tp = $arr["prop"];
		$tp["type"] = "text";

		$content = isset($arr["prop"]["hide_selected"]) && $arr["prop"]["hide_selected"] ? "" : $this->_draw_existing_kws($arr)."<br><br><br>";
		$content .= $this->_draw_alphabet($arr);

		$tp["value"] = $content;
		return array($tp["name"] => $tp);
	}

	function callback_mod_reforb($arr, $r)
	{
		$arr["kw_sel_filt"] = $r["kw_sel_filt"];
	}

	function callback_mod_retval($arr)
	{
		$arr["args"]["kw_sel_filt"] = $arr["request"]["kw_sel_filt"];
	}

	function process_vcl_property($arr)
	{
		if (!is_object($arr["obj_inst"]) || !is_oid($arr["obj_inst"]->id()))
		{
			return;
		}
		$data = safe_array($arr["request"]["kw_sel_".$arr["prop"]["name"]]);
		$filt = $arr["request"]["kw_sel_filt"];
		if (empty($filt))
		{
			$filt = "_all";
		}
		$cf_p = array("to.class_id" => CL_KEYWORD, "type" => "RELTYPE_KEYWORD");
		if ($filt != "_all")
		{
			$cf_p["to.name"] = $filt."%";
		}
		$conns = $arr["obj_inst"]->connections_from($cf_p);
		// go over and delete all that do not exist in submit
		foreach($conns as $con_id => $con)
		{
			if (!isset($data[$con->prop("to")]))
			{
				$con->delete();
			}
		}

		// add new
		foreach($data as $kwid => $one)
		{
			$arr["obj_inst"]->connect(array("to" => $kwid, "type" => "RELTYPE_KEYWORD"));
		}

		if (count($arr["obj_inst"]->connections_from(array("to.class_id" => CL_KEYWORD, "type" => "RELTYPE_KEYWORD"))))
		{
			$arr["obj_inst"]->set_meta("has_kwd_rels", 1);
		}
		else
		{
			$arr["obj_inst"]->set_meta("has_kwd_rels", 0);
		}
	}

	function _init_kw_t(&$t, $n)
	{
		for($i = 1; $i <= $n; $i++)
		{
			$t->define_field(array(
				"name" => "name_".$i,
				"caption" => t("Nimi"),
				"align" => "right"
			));

	/*		$t->define_field(array(
				"name" => "sel_1",
				"caption" => t("Vali"),
				"align" => "center"
			));*/
		}
	}

	function _draw_alphabet($arr)
	{
		classload("vcl/table");
		$t = new aw_table();
		$n = isset($arr["prop"]["keyword_per_row"]) && (int) $arr["prop"]["keyword_per_row"] > 0 ? (int) $arr["prop"]["keyword_per_row"] : 3;
		$this->_init_kw_t($t, $n);
		// That Nimi, Nimi, Nimi, Nimi isn't very informative anyway... -kaarel 11.01.2009
		$t->set_titlebar_display(false);

		if (!is_object($arr["obj_inst"]))
		{
			return;
		}

		$filt = array(
			"class_id" => CL_KEYWORD
		);
		if (empty($arr["request"]["kw_sel_filt"]))
		{
			$arr["request"]["kw_sel_filt"] = "_all";
		}
		if ($arr["request"]["kw_sel_filt"] != "_all")
		{
			$filt["name"] = $arr["request"]["kw_sel_filt"]."%";
		}
		$filt["sort_by"] = "objects.parent,objects.jrk,objects.name";
		$ol = new object_list($filt);
		if(is_oid($arr["obj_inst"]->id()))
		{
			$used_kws = new object_list($arr["obj_inst"]->connections_from(array("to.class_id" => CL_KEYWORD, "type" => "RELTYPE_KEYWORD")));
			$used_kws = $this->make_keys($used_kws->ids());
		}
		else
		{
			$user_keys = array();
		}
		$data = array_values($ol->arr());
		$num = 0;
		foreach($ol->arr() as $kw)
		{
			if (!isset($prev_parent))
			{
				$prev_parent = $kw->parent();
				$po = obj($kw->parent());
				$cur_row = array(
					"parent" => html::bold(parse_obj_name($po->name()))
				);
			}

			if ($prev_parent != $kw->parent())
			{
				$num = 15;
			}

			$num++;
			if ($num > $n)
			{
				$po = obj($kw->parent());
				$t->define_data($cur_row);
				$cur_row = array(
					"parent" => html::bold(parse_obj_name($po->name())),
					"row_num" => ++$rn
				);
				$num = 1;
			}

			$cur_row["name_".$num] = html::obj_change_url($kw)." ".html::checkbox(array(
				"name" => "kw_sel_".$arr["prop"]["name"]."[".$kw->id()."]",
				"value" => 1,
				"checked" => isset($used_kws[$kw->id()]),
			));
			$prev_parent = $kw->parent();
		}
		$po = obj($prev_parent);
		$t->define_data($cur_row);
		$cur_row = array(
			"parent" => html::bold(parse_obj_name($po->name()))
		);

		if(!isset($arr["prop"]["no_header"]) || !$arr["prop"]["no_header"])
		{
			$t->set_header($this->_get_alpha_list($arr["request"]));
		}
		if(!isset($arr["prop"]["no_folder_names"]) || !$arr["prop"]["no_folder_names"])
		{
			$t->sort_by(array(
				"rgroupby" => array("parent" => "parent"),
				"field" => array("row_num" => "row_num")
			));
		}
		return $t->draw();
	}

	function _get_alpha_list($r)
	{
		if (empty($r["kw_sel_filt"]))
		{
			$r["kw_sel_filt"] = "_all";
		}
		$list = array();
		for($i = ord('A'); $i <= ord('Z'); $i++)
		{
			if ($r["kw_sel_filt"] == chr($i))
			{
				$list[] = chr($i);
			}
			else
			{
				$list[] = html::href(array(
					"caption" => chr($i),
					"url" => aw_url_change_var("kw_sel_filt",chr($i))
				));
			}
		}


		if ($r["kw_sel_filt"] == "_all")
		{
			$list[] = t("K&otilde;ik");
		}
		else
		{
			$list[] = html::href(array(
				"caption" => t("K&otilde;ik"),
				"url" => aw_url_change_var("kw_sel_filt", "_all")
			));
		}

		$rv = join(" ", $list);
		$rv .= " / ".html::get_new_url(CL_KEYWORD, $_GET["id"], array("return_url" => get_ru()), t("Lisa uus"));
		return $rv;
	}

	function _draw_existing_kws($arr)
	{
		if (!is_object($arr["obj_inst"]) || !is_oid($arr["obj_inst"]->id()))
		{
			return;
		}
		$kws = new object_list($arr["obj_inst"]->connections_from(array("to.class_id" => CL_KEYWORD, "type" => $arr["prop"]["reltype"])));
		return t("Valitud:")." ".html::obj_change_url($kws->arr());
	}
}
