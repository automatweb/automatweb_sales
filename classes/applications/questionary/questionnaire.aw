<?php

namespace automatweb;
// questionnaire.aw - D&uuml;naamiline k&uuml;simustik
/*

@classinfo syslog_type=ST_QUESTIONNAIRE relationmgr=yes no_comment=1 no_status=1 prop_cb=1

@default table=objects
@default group=general

	@groupinfo conf parent=general caption=Seaded
	@default group=conf

		@property name type=textbox field=name
		@caption Nimi

		#@property qlimit type=textbox size=3 field=meta method=serialize
		#@caption K&uuml;simusi korraga
		#@comment 0 = unlimited

		@property q_by_one type=checkbox ch_value=1 field=meta method=serialize
		@caption K&uuml;simused &uuml;hekaupa

		@property str_answerer_with_qs type=checkbox field=meta method=serialize
		@caption Vastaja andmed samal lehel k&uuml;simustega
		@comment Toimib ainult siis, kui k&uuml;simused on &uumlhel lehel. Ei saa kasutada seadete vormi.

		@property dsply_qcomment type=chooser field=meta method=serialize
		@caption Kuva k&uuml;simuse kommentaari
		@comment Kuvatakse kommentaare, mille pikkus on > 0.

		@property dsply_acomment type=chooser field=meta method=serialize
		@caption Kuva vastuse kommentaari
		@comment Kuvatakse kommentaare, mille pikkus on > 0.

		@property correct_msg type=textbox field=meta method=serialize
		@caption &Otilde;ige vastuse teade

		@property false_msg type=textbox field=meta method=serialize
		@caption Vale vastuse teade

		@property dsply_correct2wrong type=checkbox ch_value=1 field=meta method=serialize
		@caption Vale vastuse korral kuva &otilde;iged

		@property dsply_correct2wrong_caption_single type=textbox field=meta method=serialize
		@caption &Otilde;igete vastuste caption (ainsus)
		@comment Kuvatakse vale vastuse korral

		@property dsply_correct2wrong_caption_multiple type=textbox field=meta method=serialize
		@caption &Otilde;igete vastuste caption (mitmus)
		@comment Kuvatakse vale vastuse korral

		@property dsply_correct2correct type=checkbox ch_value=1 field=meta method=serialize
		@caption &Otilde;ige vastuse korral kuva k&otilde;ik &otilde;iged

		@property dsply_correct2correct_caption_single type=textbox field=meta method=serialize
		@caption &Otilde;igete vastuste caption (ainsus)
		@comment Kuvatakse &otilde;ige vastuse korral

		@property dsply_correct2correct_caption_multiple type=textbox field=meta method=serialize
		@caption &Otilde;igete vastuste caption (mitmus)
		@comment Kuvatakse &otilde;ige vastuse korral

		@property comment2nothing type=textbox field=meta method=serialize
		@caption Kommentaar, kui vastus on t&uuml;hi
		@comment Kuvatakse vastuse kommentaari v&auml;ljas

		@property str_rslts type=checkbox ch_value=1 field=meta method=serialize
		@caption Salvesta vastamised

		@property str_answerer type=chooser field=meta method=serialize
		@caption Salvesta vastaja andmed

		@property str_answerer_cfgform type=relpicker reltype=RELTYPE_ASWERER_DATA_CFGFORM store=connect
		@caption Vastaja salvestamise seadete vorm

	@groupinfo pics parent=general caption=Pildid
	@default group=pics

		@property p_correct type=relpicker reltype=RELTYPE_IMAGE field=meta method=serialize
		@caption &Otilde;ige vastuse pilt

		@property p_false type=relpicker reltype=RELTYPE_IMAGE field=meta method=serialize
		@caption Vale vastuse pilt

	@groupinfo dsply_rslts parent=general caption=Tulemuste&nbsp;kuvamine
	@default group=dsply_rslts

		@property rd_percent type=checkbox field=meta method=serialize
		@caption &Otilde;igete vastuste protsent

		@property rd_fraction type=checkbox field=meta method=serialize
		@caption &Otilde;igeid vastuseid hariliku murruna

		@property rd_results type=checkbox field=meta method=serialize
		@caption Kuva &otilde;igesti/valesti vastatud k&uuml;simusi

		@property rd_text type=textarea field=meta method=serialize
		@caption Tekst

		@property rd_percent_text type=table store=no
		@caption Tekst &otilde;igete vastuste protsendi j&auml;rgi

	@groupinfo answerers_tab parent=general caption=Vastajate&nbsp;vaade
	@default group=answerers_tab

		@property table_cols_conf type=table store=no no_caption=1

@groupinfo questions caption=K&uuml;simused submit=no
@default group=questions

	@property qtlbr type=toolbar no_caption=1 store=no

	@property qtbl type=table no_caption=1 store=no

@groupinfo answerers submit=no caption=Vastajad
@default group=answerers

	@property atlb type=toolbar store=no no_caption=1
	@caption Vastajate toolbar

	@property acnt type=text store=no
	@caption Vastajate arv

	@property atbl type=table store=no
	@caption Vastajad

@reltype QUESTION value=1 clid=CL_QUESTIONNAIRE_QUESTION
@caption K&uuml;simus

@reltype IMAGE value=2 clid=CL_IMAGE
@caption Pilt

@reltype ANSWERER value=3 clid=CL_QUESTIONNAIRE_ANSWERER
@caption Vastaja

@reltype ASWERER_DATA_CFGFORM value=4 clid=CL_CFGFORM
@caption Vastaja salvestamise seadete vorm

*/

class questionnaire extends class_base
{
	const AW_CLID = 1393;

	const UNANSWERED = 0;
	const WRONG = 1;
	const CORRECT = 2;
	const NOT_RATABLE = 3;

	function questionnaire()
	{
		$this->init(array(
			"tpldir" => "applications/questionary/questionnaire",
			"clid" => CL_QUESTIONNAIRE
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
			case "str_answerer":
				$prop["options"] = array(
					0 => t("&Auml;ra salvesta"),
					1 => t("Enne vastamist"),
					2 => t("P&auml;rast vastamist"),
				);
				break;

			case "dsply_qcomment":
				$prop["options"] = array(
					"0" => t("Mitte kunagi"),
					"1" => t("Alati"),
					"2" => t("Ainult &otilde;ige vastuse korral"),
					"3" => t("Ainult vale vastuse korral"),
					"4" => t("Suvalise vastuse korral")
				);
				if(!$prop["value"])
					$prop["value"] = 0;
				break;
			case "dsply_acomment":
				$prop["options"] = array(
					"0" => t("Mitte kunagi"),
					"1" => t("Alati"),
					"2" => t("Ainult &otilde;ige vastuse korral"),
					"3" => t("Ainult vale vastuse korral"),
				);
				if(!$prop["value"])
					$prop["value"] = 0;
				break;

			case "qtlbr":
				$t = &$prop["vcl_inst"];
				$t->add_new_button(array(CL_QUESTIONNAIRE_QUESTION), $arr["obj_inst"]->id(), 1);
				$t->add_delete_rels_button();
				$t->add_save_button();
				break;

			case "rd_percent_text":
				$this->_get_rd_percent_text($arr);
				break;
			
			case "qtbl":
				$this->_get_atbl($arr);
				break;

			case "acnt":
				if(!$arr["obj_inst"]->prop("str_rslts"))
					$prop["value"] = t("Vastajaid ei salvestata!");
				else
					$prop["value"] = count($arr["obj_inst"]->connections_to(array("from.class_id" => CL_QUESTIONNAIRE_ANSWERER, "type" => "RELTYPE_QUESTIONNAIRE")));
				break;
		}

		return $retval;
	}

	function _get_table_cols_conf($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];

		$t->define_field(array(
			"name" => "prop",
			"caption" => t("Omadus"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "use_in_tbl",
			"caption" => t("Kuva tabelis"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "caption",
			"caption" => t("Pealkiri"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "jrk",
			"caption" => t("J&auml;rjekord"),
			"align" => "center"
		));
		$table_cols_conf = safe_array($arr["obj_inst"]->meta("table_cols_conf"));
		$props = is_oid($arr["obj_inst"]->str_answerer_cfgform) ? get_instance(CL_CFGFORM)->get_props_from_cfgform(array("id" => $arr["obj_inst"]->str_answerer_cfgform)) : get_instance(CL_CFGFORM)->get_default_proplist(array("clid" => CL_CRM_PERSON));
		foreach($props as $prop)
		{
			$t->define_data(array(
				"prop" => $prop["name"],
				"use_in_tbl" => html::checkbox(array(
					"name" => "table_cols_conf[".$prop["name"]."][use]",
					"value" => 1,
					"checked" => $table_cols_conf[$prop["name"]]["use"] == 1,
				)),
				"caption" => html::textbox(array(
					"name" => "table_cols_conf[".$prop["name"]."][caption]",
					"value" => isset($table_cols_conf[$prop["name"]]["caption"]) ? $table_cols_conf[$prop["name"]]["caption"] : $prop["caption"],
				)),
				"jrk" => html::textbox(array(
					"name" => "table_cols_conf[".$prop["name"]."][jrk]",
					"value" => (int) $table_cols_conf[$prop["name"]]["jrk"],
				)),
				"jrk_" => (int) $table_cols_conf[$prop["name"]]["jrk"],
			));
		}
		$t->set_default_sortby("jrk_");
	}

	function _get_atlb($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		$t->add_delete_button();
		$t->add_button(array(
			'name' => 'csv',
			'img' => 'ftype_xls.gif',
			'tooltip' => 'CSV',
			"url" => aw_url_change_var("get_csv_file", 1)
		));
	}

	function _get_atbl($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];

		// VASTAJA ANDMED
		$props = is_oid($arr["obj_inst"]->str_answerer_cfgform) ? get_instance(CL_CFGFORM)->get_props_from_cfgform(array("id" => $arr["obj_inst"]->str_answerer_cfgform)) : get_instance(CL_CFGFORM)->get_default_proplist(array("clid" => CL_CRM_PERSON));
		$conf = $arr["obj_inst"]->meta("table_cols_conf");
		uasort($conf, array($this, "conf_cmp_function"));
		foreach($conf as $prop => $prop_data)
		{
			if(!isset($prop_data["use"]) || $prop_data["use"] != 1)
			{
				continue;
			}

			$t->define_field(array(
				"name" => $prop,
				"caption" => strlen(trim($prop_data["caption"])) > 0 ? $prop_data["caption"] : $props[$prop]["caption"],
				"align" => "center",
			));
		}

		// KYIMUSED
		$ol = new object_list($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_QUESTION")));
		$ids = $ol->ids();
		$ol = new object_data_list(
			array(
				"class_id" => CL_QUESTIONNAIRE_QUESTION,
				"oid" => $ids,
				"site_id" => array(),
				"lang_id" => array(),
				"sort_by" => "objects.jrk ASC",
			),
			array(
				CL_QUESTIONNAIRE_QUESTION => array("name", "ratable", "answers", "show_answer_in_results"),
			)
		);
		$cnt = 1;
		$questions = $ol->arr();
		foreach($questions as $oid => $odata)
		{
			$name = $odata["name"];
			$t->define_field(array(
				"name" => "q_".$oid,
				"caption" => sprintf(t("%u."), $cnt),
				"chgbgcolor" => "bgcolor_".$oid,
				"align" => "center",
			));
			$cnt++;

			// object_data_list ei funka
			if(is_oid($questions["answers"]))
			{
				$questions["answers"] = (array)$questions["answers"];
			}
			
			// For the results this 27/68
			if($odata["ratable"])
			{
				$cnt_qs++;
			}
		}
		// Raalime v2lja 6iged vastused, et hiljem saaks vastajaid kontrollida.
		$as = new object_data_list(
			array(
				"class_id" => CL_QUESTIONNAIRE_ANSWER,
				"CL_QUESTIONNAIRE_ANSWER.RELTYPE_ANSWER(CL_QUESTIONNAIRE_QUESTION)" => array_keys($questions),
				"lang_id" => array(),
				"site_id" => array(),
				"correct" => 1,
			),
			array(
				CL_QUESTIONNAIRE_ANSWER => array("oid", "name"),
			)
		);
		$ans_ids = array_keys($as->arr());
		$ans_names = $as->get_element_from_all("name");
		foreach($questions as $qid => $qdata)
		{
			$questions[$qid]["correct"] = array_intersect((array)($qdata["answers"]), $ans_ids);
			$questions[$qid]["correct_names"] = array();
			foreach($questions[$qid]["correct"] as $aid)
			{
				$questions[$qid]["correct_names"][] = $ans_names[$aid];
			}
		}

		// DEFAULT V2LJAD
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid",
		));
		$t->define_field(array(
			"name" => "result",
			"caption" => t("Tulemus"),
			"align" => "center",
			"sortable" => 1,
		));
		$t->define_field(array(
			"name" => "time",
			"caption" => t("Vastamise aeg"),
			"align" => "center",
			"sortable" => 1,
			"sorting_field" => "tm",
		));

		// Vastused
		$as = new object_data_list(
			array(
				"class_id" => CL_QUESTIONNAIRE_ANSWERER,
				"CL_QUESTIONNAIRE_ANSWERER.RELTYPE_QUESTIONNAIRE" => $arr["obj_inst"]->id(),
				"siet_id" => array(),
				"lang_id" => array(),
			), 
			array(
				CL_QUESTIONNAIRE_ANSWERER => array("oid", "person", "answers", "txtanswers", "created"),
			)
		);
		$answers_oids = array();
		foreach($as->get_element_from_all("answers") as $answers_oid)
		{
			// object_data_list ei funka
			if(is_oid($answers_oid))
			{
				$answers_oid = (array)$answers_oid;
			}
			$answers_oids = array_merge($answers_oids, safe_array($answers_oid));
		}
		if(count($answers_oids) > 0)
		{
			$answers_odl = new object_data_list(
				array(
					"class_id" => CL_QUESTIONNAIRE_ANSWER,
					"oid" => $answers_oids,
				),
				array(
					CL_QUESTIONNAIRE_ANSWER => array("name"),
				)
			);
		}

		$txtanswers_oids = array();
		foreach($as->get_element_from_all("txtanswers") as $txtanswers_oid)
		{
			// object_data_list ei funka
			if(is_oid($txtanswers_oid))
			{
				$txtanswers_oid = (array)$txtanswers_oid;
			}
			$txtanswers_oids = array_merge($txtanswers_oids, safe_array($txtanswers_oid));
		}
		$txtanswers = array();
		if(count($txtanswers_oids) > 0)
		{
			$txtanswers_odl = new object_data_list(
				array(
					"class_id" => CL_QUESTIONNAIRE_TXT,
					"oid" => $txtanswers_oids,
				),
				array(
					CL_QUESTIONNAIRE_TXT => array("name"),
				)
			);
			$txtanswers = $txtanswers_odl->get_element_from_all("name");
		}

		foreach($as->arr() as $a)
		{
			$a["person"] = is_array($a["person"]) ? reset($a["person"]) : $a["person"];
			$row = array();
			// Configured properties
			if(is_oid($a["person"]) && $this->can("view", $a["person"]))
			{
				$p_obj = obj($a["person"]);
				foreach(array_keys($conf) as $prop)
				{
					switch($prop)
					{
						case "email":
						case "fake_email":
							$v = $p_obj->prop("email.mail");
							break;

						case "phone":
						case "fake_phone":
							$v = $p_obj->prop("phone.name");
							break;

						case "name":
							$v = is_oid($a["person"]) ? html::obj_change_url($a["person"]) : "";
							break;

						default:
							$v = $p_obj->prop_str($prop);
							break;
					}
					$row[$prop] = $v;
				}
			}
			$ans_data = array();
			$ans_data_str = array();
			foreach(connection::find(array("from.class_id" => CL_QUESTIONNAIRE_ANSWERER, "type" => "RELTYPE_ANSWER", "from" => $a["oid"])) as $conn)
			{
				$ans_data[$conn["data"]][] = $conn["to"];
				$ans_data_str[$conn["data"]][] = $conn["to.name"];
			}
			foreach(connection::find(array("from.class_id" => CL_QUESTIONNAIRE_ANSWERER, "type" => "RELTYPE_TXTANSWER", "from" => $a["oid"])) as $conn)
			{
				$ans_data[$conn["data"]][] = $conn["to.comment"];
				$ans_data_str[$conn["data"]][] = $conn["to.comment"];
			}

			$cnt_correct = 0;
			foreach($ans_data as $ad_qid => $ad_aids)
			{
				if($questions[$ad_qid]["ratable"] == 1)
				{
					if(!is_oid(reset($ad_aids)))
					{
						$correct = count(array_intersect($ad_aids, $questions[$ad_qid]["correct_names"])) == count($ad_aids);
					}
					else
					{
						$correct = count(array_intersect($ad_aids, $questions[$ad_qid]["correct"])) == count($ad_aids);
					}
					if($correct)
					{
						$row["q_".$ad_qid] = t("+");
						$row["bgcolor_".$ad_qid] = t("#0000CC");
						$cnt_correct++;
					}
					else
					{
						$row["q_".$ad_qid] = t("-");
						$row["bgcolor_".$ad_qid] = t("#FF0000");
					}
				}
				if($questions[$ad_qid]["show_answer_in_results"] == 1)
				{
					$row["q_".$ad_qid] = nl2br(implode("\n", $ans_data_str[$ad_qid]));
				}
			}
			$row = array_merge($row, array(
				"oid" => $a["oid"],
				"time" => date("d.m.Y H:i:s", $a["created"]),
				"tm" => $a["created"],
				"result" => $cnt_correct."/".$cnt_qs,
			));
			$t->define_data($row);
		}

		if(isset($_GET["get_csv_file"]) && $_GET["get_csv_file"] == 1)
		{
			header('Content-type: application/octet-stream');
			header('Content-disposition: root_access; filename="csv_output.xls"');
			die($t->get_csv_file("\t"));
		}
	}

	function _get_rd_percent_text($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "from",
			"caption" => t("Protsent alates"),
			"align" => "center",
			"width" => 100,
		));
		$t->define_field(array(
			"name" => "to",
			"caption" => t("Protsent kuni"),
			"align" => "center",
			"width" => 100,
		));
		$t->define_field(array(
			"name" => "text",
			"caption" => t("Tekst"),
			"align" => "center",
		));
		$ms = $arr["obj_inst"]->meta("rd_percent_text");
		foreach($ms as $i => $m)
		{
			$t->define_data(array(
				"from" => html::textbox(array(
					"name" => "pc_txt[".$i."][from]",
					"size" => 3,
					"value" => $m["from"],
				)),
				"to" => html::textbox(array(
					"name" => "pc_txt[".$i."][to]",
					"size" => 3,
					"value" => $m["to"],
				)),
				"text" => html::textarea(array(
					"name" => "pc_txt[".$i."][text]",
					"cols" => 80,
					"rows" => 5,
					"value" => $m["text"],
				)),
				"from_hidden" => $m["from"],
			));
		}
		$t->define_data(array(
			"from" => html::textbox(array(
				"name" => "pc_txt[new][from]",
				"size" => 3,
			)),
			"to" => html::textbox(array(
				"name" => "pc_txt[new][to]",
				"size" => 3,
			)),
			"text" => html::textarea(array(
				"name" => "pc_txt[new][text]",
				"cols" => 80,
				"rows" => 5,
			)),
			"from_hidden" => 99999,
		));
		$t->sort_by(array(
			"field" => "from_hidden",
			"sorder" => "ASC",
		));
	}

	function _get_qtbl($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid",
		));
		$t->define_field(array(
			"name" => "jrk",
			"caption" => t("J&auml;rjekord"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "question",
			"caption" => t("K&uuml;simus"),
			"align" => "center",
		));
		$rows = array();
		foreach($arr["obj_inst"]->connections_from(array("type" => 1)) as $conn)
		{
			$rows[] = array(
				"oid" => $conn->conn["to"],
				"question" => html::get_change_url($conn->conn["to"], array("return_url" => get_ru()), $conn->conn["to.name"]),
				"jrk" => html::hidden(array(
					"name" => "jrk_old[".$conn->conn["to"]."]",
					"value" => $conn->conn["to.jrk"],
				)).html::textbox(array(
					"name" => "jrk[".$conn->conn["to"]."]",
					"value" => $conn->conn["to.jrk"],
					"size" => 4
				)),
				"jrk_hidden" => (int)$conn->conn["to.jrk"],
			);
		}
		usort($rows, array($this, "q_cmp_fn"));
		// Stupid hack. Somewhy the table switches the 1st and 2nd row. :S -kaarel
		$row = $rows[1];
		$rows[1] = $rows[0];
		$rows[0] = $row;
		// End of stupid hack
		foreach(array_reverse($rows) as $row)
		{
			$t->define_data($row);
		}
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
			case "q_by_one":
				// It doesn't work at the moment.
				$retval = PROP_IGNORE;
				break;

			case "table_cols_conf":
				$arr["obj_inst"]->set_meta("table_cols_conf", $arr["request"]["table_cols_conf"]);
				break;

			case "qtbl":
				foreach($arr["request"]["jrk"] as $i => $v)
				{
					if($arr["request"]["jrk_old"][$i] == $v)
						continue;

					$o = obj($i);
					$o->set_prop("jrk", $v);
					$o->save();
				}
				break;

			case "rd_percent_text":
				$m = array();
				$i = 0;
				foreach($arr["request"]["pc_txt"] as $v)
				{
					if(!$v["from"] && !$v["to"] && !$v["text"])
						continue;

					$m[$i] = $v;
					$i++;
				}
				$arr["obj_inst"]->set_meta("rd_percent_text", $m);
				break;
		}

		return $retval;
	}

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function array_search_by_column($n, $a, $s)
	{
		foreach($a as $i => $v)
		{
			if($v[$s] == $n)
				return $i;
		}
		return 0;
	}

	function correct_percent($a)
	{
		$ccnt = $acnt = 0;
		foreach($a as $v)
		{
			if($v != questionnaire::NOT_RATABLE)
			{
				$acnt++;
			}
			if($v == questionnaire::CORRECT)
			{
				$ccnt++;
			}
		}
		return round($ccnt*100/$acnt, 0);
	}

	function correct_fraction($a)
	{
		$ccnt = $acnt = 0;
		foreach($a as $v)
		{
			if($v != questionnaire::NOT_RATABLE)
			{
				$acnt++;
			}
			if($v == questionnaire::CORRECT)
			{
				$ccnt++;
			}
		}
		return $ccnt."/".$acnt;
	}

	function show($arr)
	{
		$this->_qs = aw_unserialize(aw_global_get("questions_".$arr["id"]));
		$this->_myas = aw_unserialize(aw_global_get("my_answers_".$arr["id"]));
		$set_qs = !is_array($this->_qs);
		if(aw_global_get("questions_".$arr["id"]) == "end")
		{
			$set_qs = true;
		}
		if($set_qs)
		{
			$this->_qs = array();
		}
		if(isset($_GET["pid"]) && is_oid($_GET["pid"]))
		{
			// So it won't get lost in the process
			aw_session_set("questionnaire_pid", $_GET["pid"]);
		}
		$this->Q_PID = isset($_GET["pid"]) && is_oid($_GET["pid"]) ? $_GET["pid"] : aw_global_get("questionnaire_pid");
		/* $this->_qs values:
		0 - undone
		1 - done, wrong
		2 - done, correct

		arr($this->_qs);

		Array
		(
			[276979] => 1
			[276983] => 0
		)

		*/

		$this->o = $o = new object($arr["id"]);
		$i = get_instance(CL_IMAGE);
		$this->read_template("show.tpl");

		if(!is_oid($this->Q_PID) && !isset($_GET["qid"]) && $o->str_answerer == 1 && $o->str_answerer_with_qs && !$o->q_by_one)
		{
			$this->vars(array(
				"PERSON_DATA_INSERTION" => $this->parse("PERSON_DATA_INSERTION"),
			));
		}
		elseif(!is_oid($this->Q_PID) && !isset($_GET["qid"]) && $o->str_answerer == 1 && $this->can("view", $o->str_answerer_cfgform))
		{
			// Set the redirect for the cfgform
			$c = obj($o->prop("str_answerer_cfgform"));
			$cfgview_ru = get_ru();
			$c->set_prop("cfgview_ru", $cfgview_ru);
			$c->set_prop("cfgview_ru_id_param", "pid");
			aw_disable_acl();
			$c->save();
			aw_restore_acl();


			$this->vars(array(
				"insertion_form" => $c->instance()->parse_alias(array(
					"alias" => array("target" => $c->id()),
				)),
			));
			$this->vars(array(
				"PERSON_DATA_INSERTION" => $this->parse("PERSON_DATA_INSERTION"),
			));
			return $this->parse();
		}
		else
		if($_GET["qid"] == "data")
		{
			if($this->can("view", $o->prop("str_answerer_cfgform")) && $o->prop("str_answerer") == 2)
			{
				// Avoid entering the answerer's data twice
				if(is_oid($this->Q_PID))
				{					
					// Move on show the results.
					$_GET["qid"] = "end";
				}
				else
				{
					// Set the redirect for the cfgform
					$c = obj($o->prop("str_answerer_cfgform"));
					$cfgview_ru = aw_ini_get("baseurl").aw_url_change_var("qid", "end");
					$c->set_prop("cfgview_ru", $cfgview_ru);
					$c->set_prop("cfgview_ru_id_param", "pid");
					aw_disable_acl();
					$c->save();
					aw_restore_acl();


					$this->vars(array(
						"insertion_form" => $c->instance()->parse_alias(array(
							"alias" => array("target" => $c->id()),
						)),
					));
					$this->vars(array(
						"PERSON_DATA_INSERTION" => $this->parse("PERSON_DATA_INSERTION"),
					));
					return $this->parse();
				}
			}
			else
			{
				// Save the data
				$a = obj();
				$a->set_class_id(CL_QUESTIONNAIRE_ANSWERER);
				$a->set_parent($o->id());
				$a->set_name(sprintf(t("Vastus d&uuml;naamilisele k&uuml;simustikule %s"), $o->name()));
				aw_disable_acl();
				$a->save();
				aw_restore_acl();
				$a->connect(array(
					"to" => $o->id(),
					"reltype" => "RELTYPE_QUESTIONNAIRE",
				));
				foreach($this->_qs as $qid => $v)
				{
					if($v == 1 || $v == 2)
					{
						$a->connect(array(
							"to" => $qid,
							"type" => $v == 1 ? "RELTYPE_WRONG" : "RELTYPE_CORRECT",
						));
					}
				}
				// And move on show the results.
				$_GET["qid"] = "end";
			}
		}
		if(isset($_GET["qid"]) && $_GET["qid"] == "end")
		{
			if(!aw_global_get("questionnaire_done_".$arr["id"]))
			{
				if(!$o->q_by_one && $o->str_answerer_with_qs && isset($_POST["person"]))
				{
					$this->set_custom_person_data($o);
				}
				if(isset($_POST["answers"]) && is_array($_POST["answers"]))
				{
					$this->handle_answer_submit();
				}
				// If a person is created, store the answers and connect 'em to that person
				if($this->can("view", $this->Q_PID))
				{
					aw_session_del("questionnaire_pid");
					// Save the data
					$a = obj();
					$a->set_class_id(CL_QUESTIONNAIRE_ANSWERER);
					$a->set_parent($o->id());
					$a->set_name(sprintf(t("%s vastus d&uuml;naamilisele k&uuml;simustikule %s"), obj($this->Q_PID)->name(), $o->name()));
					aw_disable_acl();
					$a->save();
					aw_restore_acl();
					$a->connect(array(
						"to" => $this->Q_PID,
						"reltype" => "RELTYPE_PERSON",
					));
					$a->connect(array(
						"to" => $o->id(),
						"reltype" => "RELTYPE_QUESTIONNAIRE",
					));
					foreach($this->_myas as $qid => $aid)
					{
						if(is_array($aid) || is_oid($aid))
						{
							foreach((array)$aid as $aid_)
							{
								$a->connect(array(
									"to" => $aid_,
									"data" => $qid,
									"type" => "RELTYPE_ANSWER",
								));
							}
						}
						else
						{
							$a_ = obj();
							$a_->set_class_id(CL_QUESTIONNAIRE_TXT);
							$a_->set_parent($a->id());
							$a_->set_name(sprintf(t("Tekstivastus k&uuml;simusele %s"), $qid));
							$a_->set_comment($aid);
							aw_disable_acl();
							$a_->save();
							aw_restore_acl();
							$a_->connect(array(
								"to" => $qid,
								"type" => "RELTYPE_QUESTION",
							));

							$a->connect(array(
								"to" => $a_->id(),
								"data" => $qid,
								"type" => "RELTYPE_TXTANSWER",
							));
						}
					}
				}
				aw_session_set("questionnaire_done_".$arr["id"], 1);
			}
			$this->vars(array(
				"results_fraction" => $this->correct_fraction($this->_qs),
				"results_percent" => $this->correct_percent($this->_qs),
			));
			if($o->prop("rd_percent"))
			{
				$this->vars(array(
					"RESULTS_PERCENT" => $this->parse("RESULTS_PERCENT"),
				));
			}
			if($o->prop("rd_fraction"))
			{
				$this->vars(array(
					"RESULTS_FRACTION" => $this->parse("RESULTS_FRACTION"),
				));
			}
			if($o->prop("rd_results"))
			{
				$RESULTS_ANSWERED = "";
				$ol = new object_list($o->connections_from(array("type" => "RELTYPE_QUESTION")));
				$ids = $ol->ids();
				$ol = new object_list(array(
					"class_id" => CL_QUESTIONNAIRE_QUESTION,
					"oid" => $ids,
					"site_id" => array(),
					"lang_id" => array(),
					"sort_by" => "objects.jrk ASC",
				));
				foreach($ol->arr() as $qo)
				{
					// Skip the ones that are not ratable
					if(!$qo->ratable)
					{
						continue;
					}

					// Checkboxes
					if(is_array($this->_myas[$qo->id()]))
					{
						$myans = "";
						foreach($this->_myas[$qo->id()] as $myans_id)
						{
							$myans .= strlen($myans) > 0 ? "<br />" : "";
							$myans .= obj($myans_id)->trans_get_val("name");
						}
					}
					// Radiobuttons
					elseif(is_oid($this->_myas[$qo->id()]))
					{
						$myans = obj($this->_myas[$qo->id()])->trans_get_val("name");
					}
					// Textbox/-area
					else
					{
						$myans = $this->_myas[$qo->id()];
					}

					$this->vars(array(
						"results_question" => $qo->trans_get_val("name"),
						"results_my_answer" => $myans,
					));

					$ol2 = new object_list($qo->connections_from(array("type" => "RELTYPE_ANSWER")));
					$ids2 = $ol2->ids();
					$ol2 = new object_list(array(
						"class_id" => CL_QUESTIONNAIRE_ANSWER,
						"oid" => count($ids2) > 0 ? $ids2 : -1,
						"site_id" => array(),
						"lang_id" => array(),
						"correct" => 1,
						"sort_by" => "objects.jrk ASC",
					));

					$RESULTS_CORRECT_ANSWER = "";
					foreach($ol2->arr() as $ao)
					{
						$this->vars(array(
							"results_correct_answer" => $ao->trans_get_val("name"),
						));
						$RESULTS_CORRECT_ANSWER .= $this->parse("RESULTS_CORRECT_ANSWER");
					}
					$this->vars(array(
						"RESULTS_CORRECT_ANSWER" => $RESULTS_CORRECT_ANSWER,
					));
					$RESULTS_ANSWERED .= $this->parse($this->_qs[$qo->id()] == questionnaire::CORRECT ? "RESULTS_CORRECTLY_ANSWERED" : "RESULTS_WRONGLY_ANSWERED");
				}
				$this->vars(array(
					"RESULTS_ANSWERED" => $RESULTS_ANSWERED,
					"RESULTS_CORRECTLY_ANSWERED" => "",
					"RESULTS_WRONGLY_ANSWERED" => "",
				));
				$this->vars(array(
					"RESULTS_ANSWERS" => $this->parse("RESULTS_ANSWERS"),
				));
			}
			if($o->prop("rd_text"))
			{
				$this->vars(array(
					"results_text" => $o->prop("rd_text"),
				));
			}
			foreach($o->meta("rd_percent_text") as $m)
			{
				if(($m["from"] <= $this->correct_percent($this->_qs) || strlen($m["from"]) == 0) && ($m["to"] >= $this->correct_percent($this->_qs) || strlen($m["to"]) == 0))
				{
					$this->vars(array(
						"results_text_by_percent" => $m["text"],
					));
					$RESULTS_TEXT_BY_PERCENT .= $this->parse("RESULTS_TEXT_BY_PERCENT");
				}
			}
			$this->vars(array(
				"RESULTS_TEXT_BY_PERCENT" => $RESULTS_TEXT_BY_PERCENT,
			));

			$RESULTS = $this->parse("RESULTS");
			$this->vars(array(
				"RESULTS" => $RESULTS,
			));

			aw_session_set("questions_".$arr["id"], aw_serialize($this->_qs));
			aw_session_set("my_answers_".$arr["id"], aw_serialize($this->_myas));
			return $this->parse();
		}

		if($o->q_by_one)
		{
			aw_session_del("questionnaire_done_".$arr["id"]);
			$conns = $o->connections_from(array("type" => "RELTYPE_QUESTION"));
			foreach($conns as $conn)
			{
				if($set_qs)
				{
					$this->_qs[$conn->conn["to"]] = 0;
				}

				$qs[$conn->conn["to"]]["oid"] = $conn->conn["to"];
				$qs[$conn->conn["to"]]["caption"] = $conn->conn["to.name"];
				$qs[$conn->conn["to"]]["jrk"] = $conn->conn["to.jrk"];
			}
			foreach ($qs as $k => $r)
			{
				$jrk[$k]  = $r["jrk"];
			}
			array_multisort($jrk, SORT_ASC, $qs);
			if(count($qs) == 0)
			{
				return false;
			}

			$qs_id = $this->array_search_by_column($_GET["qid"], $qs, "oid");
			$q = $qs[$qs_id];
			$q_obj = obj($q["oid"]);
			// If it's the first question and it's not submitted right now, we start over again.
			if($qs_id == 0 && !$_POST["qid"])
			{
				foreach($qs as $q_temp)
				{
					$this->_qs[$q_temp["oid"]] = 0;
				}
			}
			foreach($q_obj->connections_from(array("type" => 1)) as $conn)
			{
				$as[$conn->conn["to"]]["oid"] = $conn->conn["to"];
				$as[$conn->conn["to"]]["caption"] = $conn->conn["to.name"];
				$as[$conn->conn["to"]]["jrk"] = $conn->conn["to.jrk"];
			}
			unset($jrk);
			foreach ($as as $k => $r)
			{
				$jrk[$k]  = $r["jrk"];
			}
			array_multisort($jrk, SORT_ASC, $as);

			if($q_obj->prop("ans_type"))
			{
				$this->vars(array(
					"answer_value" => $_POST["answer"],
				));
				$ANSWER_TEXTBOX = $this->parse("ANSWER_TEXTBOX");
				$this->vars(array("ANSWER_TEXTBOX" => $ANSWER_TEXTBOX));
			}
			else
			{
				foreach($as as $a)
				{
					$answer_checked = ($a["oid"] == $_POST["answer"]) ? "checked" : "";
					$this->vars(array(
						"answer_oid" => $a["oid"],
						"answer_caption" => $a["caption"],
						"answer_checked" => $answer_checked,
					));
					$ANSWER_RADIO .= $this->parse("ANSWER_RADIO");
				}
				$this->vars(array("ANSWER_RADIO" => $ANSWER_RADIO));
			}

			if(array_key_exists(($qs_id + 1), $qs))
			{
				$next_caption = t("J&auml;rgmine");
				$next_url = aw_url_change_var("qid", $qs[$qs_id + 1]["oid"]);
			}
			elseif($o->prop("str_rslts"))
			{
				$next_caption = t("L&otilde;peta!");
				$next_url = aw_url_change_var("qid", "data");
			}
			else
			{
				$next_caption = t("L&otilde;peta");
				$next_url = aw_url_change_var("qid", "end");
			}

			foreach($q_obj->prop("pics") as $pic_id)
			{
				if(!is_oid($pic_id))
					continue;

				$this->vars(array(
					"picture" => $i->make_img_tag_wl($pic_id),
				));
				$PICTURE .= $this->parse("PICTURE");
			}
			$this->vars(array(
				"PICTURE" => $PICTURE,
			));

			$dsply_acomment = $o->prop("dsply_acomment");
			$dsply_qcomment = $o->prop("dsply_qcomment");

			// If this is set for the question, we override the settings set in the questionnaire conf
			if($q_obj->prop("dsply_acomment"))
			{
				$dsply_acomment = $q_obj->prop("dsply_acomment");
			}

			if($_POST["qid"] && $_POST["answer"])
			{
				if($q_obj->prop("ans_type"))
				{
					$correct = false;
					foreach($as as $a)
					{
						if($a["caption"] == $_POST["answer"])
						{
							$a_obj = obj($a["oid"]);
							if($a_obj->prop("correct"))
							{
								$correct = true;
								break;
							}
						}
					}
					if($correct)
					{
						switch($dsply_acomment)
						{
							case 1:
								$acomment = $a_obj->prop("comm");
								break;
							case 2:
								if($correct)
								{
									$acomment = $a_obj->prop("comm");
								}
								break;
							case 3:
								if(!$correct)
								{
									$acomment = $a_obj->prop("comm");
								}
								break;
						}
					}
				}
				else
				{
					$a_obj = obj($_POST["answer"]);
					$correct = $a_obj->prop("correct");
					switch($dsply_acomment)
					{
						case 1:
							$acomment = $a_obj->prop("comm");
							break;
						case 2:
							if($correct)
							{
								$acomment = $a_obj->prop("comm");
							}
							break;
						case 3:
							if(!$correct)
							{
								$acomment = $a_obj->prop("comm");
							}
							break;
					}
				}
				$correct_vs_false = $correct ? $o->prop("correct_msg") : $o->prop("false_msg");
				$this->vars(array(
					"correct_vs_false" => $correct_vs_false,
				));

				switch($dsply_qcomment)
				{
					case 1:
					case 4:
						$qcomment .= $q_obj->prop("comm");
						break;
					case 2:
						if($correct)
						{
							$qcomment .= $q_obj->prop("comm");
						}
						break;
					case 3:
						if(!$correct)
						{
							$qcomment .= $q_obj->prop("comm");
						}
						break;
				}
				$this->_qs[$_POST["qid"]] = $correct ? 2 : 1;
				$this->_myas[$_POST["qid"]] = $_POST["answer"];

				// If picture for correct answer is set in the question object, we'll override whatever is in the questionnaire object.
				if($q_obj->prop("p_correct"))
				{
					$o->set_prop("p_correct", $q_obj->prop("p_correct"));
				}

				// If picture for wrong answer is set in the question object, we'll override whatever is in the questionnaire object.
				if($q_obj->prop("p_false"))
				{
					$o->set_prop("p_false", $q_obj->prop("p_false"));
				}

				if($o->prop("p_correct") && $correct)
				{
					$this->vars(array(
						"picture" => $i->view(array("id" => $o->prop("p_correct"))),
					));
					$ANSWER_PICTURE = $this->parse("ANSWER_PICTURE");
					$this->vars(array(
						"ANSWER_PICTURE" => $ANSWER_PICTURE,
					));
				}
				if($o->prop("p_false") && !$correct)
				{
					$this->vars(array(
						"picture" => $i->view(array("id" => $o->prop("p_false"))),
					));
					$ANSWER_PICTURE = $this->parse("ANSWER_PICTURE");
					$this->vars(array(
						"ANSWER_PICTURE" => $ANSWER_PICTURE,
					));
				}
				if((!$correct && $o->prop("dsply_correct2wrong")) || ($correct && $o->prop("dsply_correct2correct")))
				{
					$correct_answer_count = 0;
					foreach($as as $a)
					{
						$a_obj = obj($a["oid"]);
						if($a_obj->prop("correct") && $a_obj->prop("name") != $_POST["answer"] && $a["oid"] != $_POST["answer"])
						{
							$answer = $a_obj->prop("name");
							$this->vars(array(
								"answer" => $answer,
							));
							$CORRECT_ANSWER .= $this->parse("CORRECT_ANSWER");
							$correct_answer_count++;
						}
					}
					if(!$correct && $o->prop("dsply_correct2wrong"))
					{
						$correct_answer_caption = ($correct_answer_count == 1) ? $o->prop("dsply_correct2wrong_caption_single") : $o->prop("dsply_correct2wrong_caption_multiple");
					}
					else
					{
						$correct_answer_caption = ($correct_answer_count == 1) ? $o->prop("dsply_correct2correct_caption_single") : $o->prop("dsply_correct2correct_caption_multiple");
					}
					$this->vars(array(
						"correct_answer_caption" => $correct_answer_caption,
						"CORRECT_ANSWER" => $CORRECT_ANSWER,
					));
					$CORRECT_ANSWERS = $this->parse("CORRECT_ANSWERS");
					if($correct_answer_count)
						$this->vars(array(
							"CORRECT_ANSWERS" => $CORRECT_ANSWERS,
						));
				}
			}
			elseif($_POST["qid"])
			{
				$acomment = $o->prop("comment2nothing");
				$this->vars(array(
					"acomment" => $acomment,
				));
			}
			else
			{
				if($dsply_qcomment == 1)
				{
					$qcomment = $q_obj->prop("comm");
				}
			}

			if($this->_qs[$q["oid"]] != 1 && $this->_qs[$q["oid"]] != 2)
			{
				$submit = html::submit(array(
					"value" => "Vasta",
				));
				$this->vars(array(
					"submit" => $submit,
				));
				$next_caption = "";
			}

			$this->vars(array(
				"question" => $q["caption"],
				"next_url" => $next_url,
				"next_caption" => $next_caption,
				"question_id" => $q["oid"],
				"acomment" => $acomment,
				"qcomment" => $qcomment,
			));

			$this->vars(array(
				"QUESTION" => $this->parse("QUESTION"),
			));

			$QUESTIONNAIRE = $this->parse("QUESTIONNAIRE");
			$this->vars(array(
				"QUESTIONNAIRE" => $QUESTIONNAIRE,
			));

			aw_session_set("questions_".$arr["id"], aw_serialize($this->_qs));
			aw_session_set("my_answers_".$arr["id"], aw_serialize($this->_myas));
		}
		else
		{
			$this->show_all_questions();
			aw_session_del("questionnaire_done_".$arr["id"]);
			aw_session_set("questions_".$arr["id"], aw_serialize($this->_qs));
		}

		return $this->parse();
	}

	function show_all_questions()
	{
		$QUESTION = "";
		foreach($this->sort_questions() as $q)
		{
			if(!isset($this->_qs[$q->id()]))
			{
				$this->_qs[$q->id()] = questionnaire::UNANSWERED;
			}
			$QUESTION .= $q->instance()->show(array("id" => $q->id(), "conf" => array(
				"dsply_acomment" => $this->o->prop("dsply_acomment"),
				"dsply_qcomment" => $this->o->prop("dsply_qcomment"),
			)));
		}
		$this->vars(array(
			"QUESTION" => $QUESTION,
		));

		$this->vars(array(
			"QUESTIONNAIRE" => $this->parse("QUESTIONNAIRE"),
		));
	}

	private function sort_questions()
	{
		$ol = new object_list($this->o->connections_from(array("type" => "RELTYPE_QUESTION")));
		$ol_arr = $ol->arr();
		uasort($ol_arr, array($this, "cmp_function"));
		return $ol_arr;
	}

	private function handle_answer_submit()
	{
		$qodl = new object_data_list(
			array(
				"class_id" => CL_QUESTIONNAIRE_QUESTION,
				"oid" => array_keys($_POST["answers"]),
				"lang_id" => array(),
				"site_id" => array(),
			), 
			array(
				CL_QUESTIONNAIRE_QUESTION => array("ratable", "answers"),
			)
		);
		$qodl_arr = $qodl->arr();
		// object_data_list ei funka
		foreach($qodl_arr as $k => $v)
		{
			if($k == "answers" && is_oid($v))
			{
				$k = (array)$v;
			}
		}

		$odl = new object_data_list(
			array(
				"class_id" => CL_QUESTIONNAIRE_ANSWER,
				"CL_QUESTIONNAIRE_ANSWER.RELTYPE_ANSWER(CL_QUESTIONNAIRE_QUESTION)" => array_keys($_POST["answers"]),
				"lang_id" => array(),
				"site_id" => array(),
			), 
			array(
				CL_QUESTIONNAIRE_ANSWER => array("name", "correct"),
			)
		);
		$odl_arr = $odl->arr();
		foreach($_POST["answers"] as $qid => $aid)
		{
			// Checkboxes
			if(is_array($aid))
			{
				$correct_answers = array();
				foreach($qodl_arr[$qid]["answers"] as $aid_)
				{
					if($odl_arr[$aid_]["correct"] == 1)
					{
						$correct_answers[] = $aid_;
					}
				}
				$this->_qs[$qid] = count(array_intersect($correct_answers, $aid)) == count($aid) ? questionnaire::CORRECT : questionnaire::WRONG;
				$this->_myas[$qid] = $aid;
			}
			// Radiobutton
			elseif(is_oid($aid))
			{
				$this->_qs[$qid] = $odl_arr[$aid]["correct"] == 1 ? questionnaire::CORRECT : questionnaire::WRONG;
				$this->_myas[$qid] = $aid;
			}
			// Textbox/-area
			else
			{
				$this->_qs[$qid] = questionnaire::WRONG;
				foreach(safe_array($qodl_arr[$qid]["answers"]) as $aid_)
				{
					if(strcmp($odl_arr[$aid_]["name"], $aid) == 0)
					{
						$this->_qs[$qid] = questionnaire::CORRECT;
						break;
					}
				}
				$this->_myas[$qid] = $aid;
			}
			if($qodl_arr[$qid]["ratable"] != 1)
			{
				$this->_qs[$qid] = questionnaire::NOT_RATABLE;
			}
		}
	}

	private function cmp_function($a, $b)
	{
		return $a->ord() > $b->ord();
	}

	private function set_custom_person_data($o)
	{
		$p = obj();
		$p->set_class_id(CL_CRM_PERSON);
		$p->set_parent($o->id());
		aw_disable_acl();
		$p->save();
		foreach($_POST["person"] as $k => $v)
		{
			$p->set_prop($k, $v);
		}
		$p->save();
		aw_restore_acl();
		$this->Q_PID = $p->id();
	}

	private function conf_cmp_function($a, $b)
	{
		return $a["jrk"] > $b["jrk"];
	}

	private function q_cmp_fn($a, $b)
	{
		return $a["jrk_hidden"] > $b["jrk_hidden"];
	}
}

?>
