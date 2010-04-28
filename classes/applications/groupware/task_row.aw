<?php

namespace automatweb;

// task_row.aw - Toimetuse rida
/*

@classinfo syslog_type=ST_TASK_ROW relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=markop

@tableinfo aw_task_rows index=aw_oid master_table=objects master_index=brother_of

@default table=objects

@default group=general

	@property parent type=hidden table=objects field=parent
	@caption Parent

	@property content type=textarea rows=5 cols=50 table=aw_task_rows field=aw_content
	@caption Sisu

	@property time type=time_select field=meta method=serialize
	@caption Aeg

	@property date type=date_select table=aw_task_rows field=aw_date
	@caption Kuup&auml;ev

	@property orderer type=relpicker reltype=RELTYPE_ORDERER table=aw_task_rows field=aw_orderer
	@caption Tellija

	@property impl type=objpicker clid=CL_CRM_PERSON table=aw_task_rows field=aw_impl
	@caption Teostaja

	@property customer type=relpicker reltype=RELTYPE_CUSTOMER multiple=1 store=connect
	@caption Klient

	@property project type=relpicker reltype=RELTYPE_PROJECT multiple=1 store=connect
	@caption Projekt

	@property skill_used style=select table=aw_task_rows field=skill_used parent=settings_col1 captionside=top
	@caption Kasutatav P&auml;devus

	@property time_guess type=textbox size=5 table=aw_task_rows field=aw_time_guess
	@caption Prognoositud tunde

	@property time_real type=textbox size=5 table=aw_task_rows field=aw_time_real
	@caption Kulunud tunde

	@property time_to_cust type=textbox size=5 table=aw_task_rows field=aw_time_to_cust
	@caption Tunde kliendile

	@property done type=checkbox ch_value=1 table=aw_task_rows field=aw_done
	@caption Tehtud

	@property primary type=checkbox ch_value=1 table=aw_task_rows field=aw_primary
	@caption Esmane rida (isikule aegade jagamiseks)

	@property on_bill type=checkbox ch_value=1 table=aw_task_rows field=aw_on_bill
	@caption Arvele

	@property bill_id type=relpicker reltype=RELTYPE_BILL table=aw_task_rows field=aw_bill_id
	@caption Arve

	@property to_bill_date type=date_select table=aw_task_rows field=aw_to_bill_date
	@caption Arvele m&auml;&auml;ramise kuup&auml;ev

	@property ord type=text table=aw_task_rows field=aw_task_ord no_caption=1
	@caption Jrk

	@property task type=relpicker table=aw_task_rows field=aw_task reltype=RELTYPE_TASK
	@caption Toimetus


//bugist
@property prev_state type=select table=aw_task_rows field=aw_prev_state
@caption Eelmine staatus

@property new_state type=select table=aw_task_rows field=aw_new_state
@caption Uus staatus

@property activity_stats_type type=hidden table=aw_task_rows field=aw_activity_stats_type
@caption Statistika t&uuml;&uuml;p

@property show_to_all type=hidden table=aw_task_rows field=aw_show_to_all
@caption N&auml;ita k&otilde;igile


@default group=comments

	@property comments type=comments
	@caption Kommentaarid

@groupinfo comments caption="Kommentaarid"

@reltype BILL value=1 clid=CL_CRM_BILL
@caption Arve

@reltype ORDERER value=3 clid=CL_CRM_PERSON
@caption Tellija

@reltype PROJECT value=4 clid=CL_PROJECT
@caption Projekt

@reltype CUSTOMER value=5 clid=CL_CRM_COMPANY,CL_CRM_PERSON
@caption Klient

@reltype TASK value=6 clid=CL_TASK,CL_CRM_MEETING,CL_BUG,CL_CRM_CALL
@caption Tegevus
*/

class task_row extends class_base
{
	const AW_CLID = 1050;

	function task_row()
	{
		$this->init(array(
			"tpldir" => "applications/groupware/task_row",
			"clid" => CL_TASK_ROW
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "prev_state":
			case "new_state":
				$bi = get_instance(CL_BUG);
				$prop["options"] = array("" => t("--vali--"))+ $bi->get_status_list();
				break;
			case "parent": return PROP_IGNORE;
			case "skill_used":
				if(!is_object($arr["obj_inst"])) return PROP_IGNORE;
				$prop["options"] = array();
				if(is_oid($arr["obj_inst"]->prop("impl")))
				{
					$who = $arr["obj_inst"]->prop("impl");
					$prop["options"] = $who->get_skill_names();
				}
				break;
			case "task":
				if($this->can("view" , $prop["value"]))
				{
					$o = obj($prop["value"]);
					$prop["options"] = $prop["options"] + array($prop["value"] => $o->name());
				}
		}
		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "parent": return PROP_IGNORE;
		}
		return $retval;
	}

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	/*
	function handle_stopper_stop($o, $data)
	{
		$o->set_prop("time_real", $o->prop("time_real") + $data["hours"]);
		if ($data["desc"] != "")
		{
			$o->set_prop("content", $o->prop("content")." ".$data["desc"]);
		}
		$o->save();

		return 1;
	}
	*/

	function do_db_upgrade($t, $f)
	{
		if($t === "aw_task_rows" && $f == "")
		{
			$this->db_query("CREATE TABLE aw_task_rows (aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "aw_to_bill_date":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				return true;
			case "aw_task_ord":
			case "skill_used":
			case "aw_task":
			case "aw_orderer":
			case "aw_impl":
			case "aw_primary":
			case "aw_prev_state":
			case "aw_new_state":
			case "aw_activity_stats_type":
			case "aw_show_to_all":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
			return true;
		}
	}
		//Toimima peaks see siis nii, et kui Toimetuses on ainult 1 rida, siis pannakse kokkuleppehind
	// sinna rea taha kirja. Kui on kaks v6i rohkem 0 tundidega rida, siis jagatakse kokkuleppehind
	//v6rdselt nendele ridadele. Kui on osa ridu tundidega ja osa ilma, siis jagatakse kokkuleppehind
	//ainult tundidega ridade vahel 2ra.

	//tagastab statistikale sobiliku summa rea kohta, juhul kui on tegu kokkuleppehinnaga
	function get_row_ageement_price($row, $task = null)
	{
		if(is_oid($row))
		{
			$row = obj($row);
		}
		if(is_object($row))
		{
			$row_cnt = 0;
			$time_cnt = 0;
			$sum = 0;
			//arvest
			if(is_oid($row->prop("bill_id")))
			{
				$bill = obj($row->prop("bill_id"));
				$rows_list = new object_list(array(
					"class_id" => CL_TASK_ROW,
					"lang_id" => array(),
					"CL_TASK_ROW.bill_id" => $bill->id(),
				));
				$agreement = $bill->meta("agreement_price");
				//kui kokkuleppehinnal on summa arvutatud juba
				if($agreement[0]["sum"])
				{
					$sum = $agreement[0]["sum"];
				}
				else
				{
					//m6nikord 2kki ei viitsita koguseks 1 m2rkida
					if(!$agreement[0]["amt"])
					{
						$agreement[0]["amt"] = 1;
					}
					$sum = $agreement[0]["amt"] + $agreement[0]["price"];
				}
				foreach ($rows_list->arr() as $key => $row)
				{
					$row_cnt ++;
					$time_cnt = $time_cnt + $row->prop("time_to_cust");
				}
			}
			//toimetusest
			else
			{
				if(is_oid($task))
				{
					$task = obj($task);
				}
				if(!is_object($task))
				{
					$task_conn = reset($row->connections_to(array(
						"type" => 7,
					)));
					$task = obj($task_conn->from());
				}
				if(!is_object($task))
				{
					return 0;
				}
				$sum = $task->prop("deal_price");
				$cs = $task->connections_from(array(
					"type" => "RELTYPE_ROW",
				));
				foreach ($cs as $key => $ro)
				{
					$ob = $ro->to();
					$row_cnt ++;
					$time_cnt = $time_cnt + $ob->prop("time_to_cust");

				}
			}
			//kui on ainuke rida
			if($row_cnt == 1)
			{
				return $sum;
			}
			//kui on mitu rida , mille aeg n2itab 0
			if($row_cnt > 1 && $time_cnt == 0)
			{
				return $sum / $row_cnt;
			}
			//kui on mitu rida ja m6nel on aeg, teistel mitte
			if($row_cnt > 1 && $time_cnt > 0)
			{
				return ($row->prop("time_to_cust")/$time_cnt) * $sum;
			}
		}
		return 0;
	}

	function stopper_autocomplete($requester, $params)
	{
		switch($requester)
		{
			case "task":
				$l = new object_list(array(
					"class_id" => CL_TASK,
				));
				foreach($l->arr() as $obj)
				{
					$ret[$obj->id()] = $obj->name();
				}
			break;
			default:
				$ret = array();
				break;
		}
		return $ret;
	}


	function gen_stopper_addon($fafa)
	{
		$props = array(
			array(
				"name" => "task",
				"type" => "textbox",
				"autocomplete" => true,
				"caption" => t("Toimetus"),
			),
			array(
				"name" => "desc",
				"type" => "textbox",
				"caption" => t("Tegevus"),
			),
			array(
				"name" => "isdone",
				"type" => "checkbox",
				"caption" => t("Tehtud"),
				"ch_value" => 1,
				"value" => 1,
			),
			array(
				"name" => "tobill",
				"type" => "checkbox",
				"caption" => t("Arvele"),
			),
			array(
				"name" => "explanation",
				"type" => "text",
				"no_caption" => 1,
				"value" => "<b>".t("Aegade puhul t&auml;hendab t&uuml;hi lahter stopperi aega.")."</b>",
			),
			array(
				"name" => "timeguess",
				"type" => "textbox",
				"value" => "0",
				"caption" => t("Prognoositud tunde"),
			),
			array(
				"name" => "timereal",
				"type" => "textbox",
				"caption" => t("Kulunud tunde"),
			),
			array(
				"name" => "timetocust",
				"type" => "textbox",
				"caption" => t("Tunde kliendile"),
			),
		);
		return $props;
	}

	function gen_existing_stopper_addon($fafa)
	{
		$props = array(
			array(
				"name" => "isdone",
				"type" => "checkbox",
				"caption" => t("Tehtud"),
				"ch_value" => 1,
				"value" => 1,
			),
			array(
				"name" => "tobill",
				"type" => "checkbox",
				"caption" => t("Arvele"),
			),
		);
		return $props;
	}

	function handle_stopper_stop($arr)
	{
		if(!$this->can("view", $arr["oid"]))
		{
			if(!$this->can("view", $arr["data"]["task"]["value"]) || !strlen($arr["data"]["desc"]["value"]))
			{
				return t("Toimetus ja tegevus peavad m&auml;&auml;ratud olema");
			}
			$o = obj($arr["data"]["task"]["value"]);
			$cp = get_current_person();

			$row = $o->add_row();
/*			$row = obj();
			$row->set_parent($o->id());
			$row->set_class_id(CL_TASK_ROW);*/
			$row->set_prop("content", $arr["data"]["desc"]["value"]);
			$row->set_prop("date", $arr["start"]);
			$row->set_prop("impl", array($cp->id() => $cp->id()));
			$row->save();
			$arr["oid"] = $row->id();
/*			$o->connect(array(
				"to" => $row->id(),
				"type" => "RELTYPE_ROW"
			));*/
		}

		$row = obj($arr["oid"]);

		$row->set_prop("time_guess", strlen($arr["data"]["timeguess"]["value"])?$arr["data"]["timeguess"]["value"]:($row->prop("time_guess")+$arr["hours"]));
		$row->set_prop("time_real", strlen($arr["data"]["timereal"]["value"])?$arr["data"]["timereal"]["value"]:($row->prop("time_real") + $arr["hours"]));
		$row->set_prop("time_to_cust", strlen($arr["data"]["timetocust"]["value"])?$arr["data"]["timetocust"]["value"]:($row->prop("time_to_cust") + $arr["hours"]));

		$row->set_prop("done", $arr["data"]["isdone"]["value"]?1:0);
		$row->set_prop("on_bill", $arr["data"]["tobill"]["value"]?1:0);
		$row->save();
	}

}
?>
