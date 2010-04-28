<?php

namespace automatweb;
// questionnaire_question.aw - D&uuml;naamilise k&uuml;simustiku k&uuml;simus
/*

@classinfo syslog_type=ST_QUESTIONNAIRE_QUESTION relationmgr=yes no_comment=1 no_status=1 prop_cb=1

@default table=objects
@default group=general

	@groupinfo conf parent=general caption=Seaded
	@default group=conf

		@property name type=textbox field=name
		@caption K&uuml;simus

		@property ratable type=checkbox ch_value=1 field=meta method=serialize
		@caption Hinnatav

		@property jrk type=textbox size=4 field=jrk
		@caption J&auml;rjekord

		@property ans_type type=chooser field=meta method=serialize
		@caption Vastuse t&uuml;&uuml;p

		@property comm type=textarea field=comment
		@caption Kommentaar

		@property dsply_acomment type=chooser field=meta method=serialize
		@caption Kuva vastuse kommentaari
		@comment Kuvatakse ka siis, kui k&uuml;simustiku seadetes pole seda lubatud.

		@property show_answer_in_results type=checkbox ch_value=1 field=meta method=serialize
		@caption N&auml;ita vastust vastajate tabelis

	@groupinfo pics parent=general caption=Pildid
	@default group=pics

		@property pics type=relpicker multiple=1 reltype=RELTYPE_IMAGE field=meta method=serialize
		@caption K&uuml;simuse pildid

		@property p_correct type=relpicker reltype=RELTYPE_IMAGE field=meta method=serialize
		@caption &Otilde;ige vastuse pilt

		@property p_false type=relpicker reltype=RELTYPE_IMAGE field=meta method=serialize
		@caption Vale vastuse pilt

@groupinfo answers submit=no caption=Vastused
@default group=answers

	@property answers type=relpicker reltype=RELTYPE_ANSWER multiple=1 store=connect
	@caption Vastused

	@property atlbr type=toolbar no_caption=1 submit=no

	@property atbl type=table no_caption=1 submit=no

@reltype ANSWER value=1 clid=CL_QUESTIONNAIRE_ANSWER
@caption Vastus

@reltype IMAGE value=2 clid=CL_IMAGE
@caption Pilt

*/

class questionnaire_question extends class_base
{
	const AW_CLID = 1394;

	function questionnaire_question()
	{
		$this->init(array(
			"tpldir" => "applications/questionary/questionnaire_question",
			"clid" => CL_QUESTIONNAIRE_QUESTION
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
			case "answers":
				$retval = PROP_IGNORE;
				break;

			case "ratable":
				if(!isset($prop["value"]))
				{
					$prop["value"] = $prop["ch_value"];
				}
				break;

			case "ans_type":
				$prop["options"] = array(
					"0" => t("Valikvastused (valida saab &uuml;he)"),
					"3" => t("Valikvastused (valida saab mitu)"),
					"1" => t("Tekstikast"),
					"2" => t("Tekstiala"),
				);
				if(!isset($prop["value"]))
				{
					$prop["value"] = 0;
				}
				break;

			case "dsply_acomment":
				$prop["options"] = array(
					"0" => t("Mitte kunagi"),
					"1" => t("Alati"),
					"2" => t("Ainult &otilde;ige vastuse korral"),
					"3" => t("Ainult vale vastuse korral"),
				);
				if(!isset($prop["value"]))
				{
					$prop["value"] = 0;
				}
				break;

			case "atlbr":
				$t = &$prop["vcl_inst"];
				$t->add_new_button(array(CL_QUESTIONNAIRE_ANSWER), $arr["obj_inst"]->id(), 1);
				$t->add_delete_button();
				$t->add_save_button();
				break;

			case "atbl":
				$this->_get_qtbl($arr);
				break;
		}

		return $retval;
	}

	function _get_qtbl($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		$t->set_sortable(true);
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid",
		));
		$t->define_field(array(
			"name" => "jrk",
			"caption" => t("J&auml;rjekord"),
			"sortable" => false,
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "answer",
			"caption" => t("Vastus"),
			"align" => "center",
		));
		foreach($arr["obj_inst"]->connections_from(array("type" => 1)) as $conn)
		{
			$t->define_data(array(
				"oid" => $conn->conn["to"],
				"answer" => html::get_change_url($conn->conn["to"], array("return_url" => get_ru()), $conn->conn["to.name"]),
				"jrk" => html::hidden(array(
					"name" => "jrk_old[".$conn->conn["to"]."]",
					"value" => $conn->conn["to.jrk"],
				)).html::textbox(array(
					"name" => "jrk[".$conn->conn["to"]."]",
					"value" => $conn->conn["to.jrk"],
					"size" => 4
				)),
				"jrk_hidden" => $conn->conn["to.jrk"],
			));
		}
		$t->sort_by(array(
			"field" => "jrk_hidden",
			"sorder" => "ASC",
		));
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
			case "atbl":
				foreach($arr["request"]["jrk"] as $i => $v)
				{
					if($arr["request"]["jrk_old"][$i] == $v)
						continue;

					$o = obj($i);
					$o->set_prop("jrk", $v);
					$o->save();
				}
				break;
		}

		return $retval;
	}

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function show($arr)
	{
		extract($arr["conf"]);
		// $dsply_acomment, $dsply_qcomment

		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");

		// Figure out the conf		
		if($ob->prop("dsply_acomment"))
		{
			$dsply_acomment = $ob->prop("dsply_acomment");
		}
		$this->vars(array(
			"question" => $ob->prop("name"),
			"question_id" => $ob->id(),
		));
		
		if($ob->prop("ans_type") == 1)
		{
			$this->vars(array(
				"answer_value" => isset($_POST["answers"][$ob->id()]) ? $_POST["answers"][$ob->id()] : "",
			));
			$this->vars(array("ANSWER_TEXTBOX" => $this->parse("ANSWER_TEXTBOX")));
		}
		elseif($ob->prop("ans_type") == 2)
		{
			$this->vars(array(
				"answer_value" => isset($_POST["answers"][$ob->id()]) ? $_POST["answers"][$ob->id()] : "",
			));
			$this->vars(array("ANSWER_TEXTAREA" => $this->parse("ANSWER_TEXTAREA")));
		}
		else
		{			
			$ol = new object_list($ob->connections_from(array("type" => "RELTYPE_ANSWER")));
			$ol_arr = $ol->arr();
			uasort($ol_arr, array($this, "cmp_function"));
			$ANSWER_RADIO = "";
			$ANSWER_CHBOX = "";
			foreach($ol_arr as $ans_o)
			{
				$answer_checked = (isset($_POST["answers"][$ob->id()]) && $ans_o->id() == $_POST["answers"][$ob->id()]) ? "checked=\"checked\"" : "";
				$this->vars(array(
					"answer_oid" => $ans_o->id(),
					"answer_caption" => $ans_o->name(),
					"answer_checked" => $answer_checked,
				));
				if($ob->prop("ans_type") == 3)
				{
					$ANSWER_CHBOX .= $this->parse("ANSWER_CHBOX");
				}
				else
				{
					$ANSWER_RADIO .= $this->parse("ANSWER_RADIO");
				}
			}
			$this->vars(array(
				"ANSWER_RADIO" => $ANSWER_RADIO,
				"ANSWER_CHBOX" => $ANSWER_CHBOX,
			));
		}		

		$PICTURE = "";
		foreach(safe_array($ob->prop("pics")) as $pic_id)
		{
			if(!is_oid($pic_id))
			{
				continue;
			}

			$this->vars(array(
				"picture" => $i->make_img_tag_wl($pic_id),
			));
			$PICTURE .= $this->parse("PICTURE");
		}
		$this->vars(array(
			"PICTURE" => $PICTURE,
		));
		
		if($dsply_qcomment == 1)
		{
			$this->vars(array(
				"qcomment" => $ob->prop("comm"),
			));
		}

		return $this->parse();
	}

	private function cmp_function($a, $b)
	{
		return $a->ord() > $b->ord();
	}
}

?>
