<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/questionary/question_group.aw,v 1.7 2007/12/06 14:33:53 kristo Exp $
// question_group.aw - K&uml;simustegrupp 
/*

@classinfo syslog_type=ST_QUESTION_GROUP relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=tarvo

@default table=objects
@default group=general
@default field=meta
@default method=serialize

	@property jrk type=textbox field=jrk method=none size=3
	@caption Jrk

	@property no_answer type=checkbox ch_value=1
	@caption Kas n&auml;idata "ei vasta" v&auml;lja

@groupinfo topics caption=Teemad
@default group=topics
	
	@property topic_tb no_caption=1 type=toolbar
	@property topic_tbl no_caption=1 type=table

@groupinfo questions caption=K&uuml;simused
@default group=questions
	
	@property question_tb no_caption=1 type=toolbar
	@property question_tbl no_caption=1 type=table


@reltype TOPIC value=1 clid=CL_QUESTION
@caption K&uuml;simuste teema

@reltype QUESTION value=2 clid=CL_QUESTION
@caption K&uuml;simus
*/

class question_group extends class_base
{
	const AW_CLID = 1154;

	function question_group()
	{
		$this->init(array(
			"tpldir" => "applications/questionary/question_group",
			"clid" => CL_QUESTION_GROUP
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			//-- get_property --//
			case "topic_tb":
				$tb = &$prop["vcl_inst"];
				$tb->add_button(array(
					"name" => "action",
					"img" => "new.gif",
					"tooltip" => t("Uus teema"),
					"url" => $this->mk_my_orb("new", array(
						"alias_to" => $arr["obj_inst"]->id(),
						"reltype" => 1,
						"parent" => $arr["obj_inst"]->id(),
						"return_url" => get_ru(),
					), CL_QUESTION), 
				));
			break;
			case "topic_tbl":
				$t = &$prop["vcl_inst"];
				$t->define_field(array(
					"name" => "name",
					"caption" => t("Nimi"),
				));
				foreach($this->get_topics($arr["obj_inst"]->id()) as $oid => $obj)
				{
					$url = $this->mk_my_orb("change", array(
						"id" => $oid,
						"return_url" => get_ru(),
					), CL_QUESTION);
					$t->define_data(array(
						"name" => html::href(array(
							"caption" => $obj->name(),
							"url" => $url,
						)),
					));
				}
			break;
			case "question_tb":
				$tb = &$prop["vcl_inst"];
				$tb->add_button(array(
					"name" => "action",
					"img" => "new.gif",
					"tooltip" => t("Uus teema"),
					"url" => $this->mk_my_orb("new", array(
						"alias_to" => $arr["obj_inst"]->id(),
						"reltype" => 2,
						"parent" => $arr["obj_inst"]->id(),
						"return_url" => get_ru(),
					), CL_QUESTION), 
				));
			break;
			case "question_tbl":
				$t = &$prop["vcl_inst"];
				$t->define_field(array(
					"name" => "name",
					"caption" => t("Nimi"),
				));
				foreach($this->get_questions($arr["obj_inst"]->id()) as $oid => $obj)
				{
					$url = $this->mk_my_orb("change", array(
						"id" => $oid,
						"return_url" => get_ru(),
					), CL_QUESTION);
					$t->define_data(array(
						"name" => html::href(array(
							"caption" => $obj->name(),
							"url" => $url,
						)),
					));
				}
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
			//-- set_property --//
		}
		return $retval;
	}	

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	////////////////////////////////////
	// the next functions are optional - delete them if not needed
	////////////////////////////////////

	/** this will get called whenever this object needs to get shown in the website, via alias in document **/
	function show($arr)
	{
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");
		$this->vars(array(
			"name" => $ob->prop("name"),
		));
		return $this->parse();
	}

//-- methods --//

	function _get_q($arr)
	{
		$c = new connection();
		$conns = $c->find(array(
			"from" => $arr["oid"],
			//"from.class_id" => CL_QUESTION_GROUP,
			//"to.class_id" => CL_QUESTION,
			"type" => $arr["type"],
		));
		foreach($conns as $cdata)
		{
			$ret[$cdata["to"]] = obj($cdata["to"]);
		}
		uasort($ret, create_function('$a,$b', 'return $a->prop("jrk") - $b->prop("jrk");'));
		return $ret;
	}

	function get_topics($oid)
	{
		return $this->_get_q(array(
			"oid" => $oid,
			"type" => 1,
		));
	}

	function get_questions($oid)
	{
		return $this->_get_q(array(
			"oid" => $oid,
			"type" => 2,
		));
	}

}
?>
