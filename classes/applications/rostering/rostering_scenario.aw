<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/rostering/rostering_scenario.aw,v 1.3 2007/12/06 14:34:03 kristo Exp $
// rostering_scenario.aw - Planeerimise stsenaarium 
/*

@classinfo syslog_type=ST_ROSTERING_SCENARIO relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo

@tableinfo aw_rostering_scenario master_table=objects master_index=brother_of index=aw_oid

@default table=objects
@default group=settings_sect

	@property cedit_tb type=toolbar no_caption=1 store=no

	@layout contacts_edit type=hbox

		@layout contacts_edit_tree type=hbox parent=contacts_edit closeable=1 area_caption=Struktuur

			@property cedit_tree type=treeview store=no parent=contacts_edit_tree no_caption=1

		@layout contacts_edit_table type=hbox parent=contacts_edit 
			@property cedit_table type=table store=no parent=contacts_edit_table no_caption=1

@default group=settings_gen

	@property cycles type=relpicker reltype=RELTYPE_CYCLE automatic=1 multiple=1 store=connect
	@caption Ts&uuml;klid

	@property  work_hrs_per_week type=textbox size=5 table=aw_rostering_scenario field=aw_work_hrs_per_week
	@caption T&ouml;&ouml;tunde n&auml;dalas

	@property  no_plan_night type=checkbox ch_value=1 table=aw_rostering_scenario field=aw_no_plan_night
	@caption &Auml;ra planeeri &ouml;&ouml;seks

	@property  max_overtime type=textbox size=5 table=aw_rostering_scenario field=aw_max_overtime
	@caption Maksimaalne &uuml;letundide arv

	@property  free_days_after_night_shift type=textbox size=5 table=aw_rostering_scenario field=aw_free_days_after_night_shift
	@caption Vabu p&auml;evi peale &ouml;&ouml;t&ouml;&ouml;d

@groupinfo settings caption="Seaded" submit=no
	@groupinfo settings_gen caption="&Uuml;ldised" parent=settings
	@groupinfo settings_sect caption="Spetsiifiised" submit=no parent=settings

	
@reltype CYCLE value=1 clid=CL_PERSON_WORK_CYCLE
@caption Ts&uuml;kkel
*/

class rostering_scenario extends class_base
{
	function rostering_scenario()
	{
		$this->init(array(
			"tpldir" => "applications/rostering/rostering_scenario",
			"clid" => CL_ROSTERING_SCENARIO
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "cedit_tree":
				return $this->_fwd_co($arr);

			case "cedit_table":
				return $this->_cedit_table($arr);

			case "cedit_tb":
				$this->_cedit_tb($arr);
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
			case "cedit_tb":
			case "cedit_tree":
				return $this->_setp_fwd_co($arr);

			case "cedit_table":
				return $this->_setp_cedit_table($arr);
		}
		return $retval;
	}	

	function _cedit_table($arr)
	{
		$this->_fwd_co($arr);
		$t =& $arr["prop"]["vcl_inst"];
		$t->remove_field("phone");
		$t->remove_field("email");
		$t->remove_field("section");
		$t->remove_field("rank");

		$t->define_field(array(
			"name" => "cycles",
			"caption" => t("Ts&uuml;klid"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "work_hrs_per_week",
			"caption" => t("T&ouml;&ouml;tunde n&auml;dalas"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "no_plan_night",
			"caption" => t("&Auml;ra planeeri &ouml;&ouml;seks"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "max_overtime",
			"caption" => t("Maksimaalne &uuml;letundide arv"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "free_days_after_night_shift",
			"caption" => t("Vabu p&auml;evi peale &ouml;&ouml;t&ouml;&ouml;d"),
			"align" => "center"
		));
		$settings = $arr["obj_inst"]->meta("scenario_data");
		$cycles_list = new object_list(array(
			"class_id" => CL_PERSON_WORK_CYCLE,
			"lang_id" => array(),
			"site_id" => array()
		));
		foreach($t->get_data() as $idx => $row)
		{
			$row["cycles"] = html::select(array(
				"name" => "d[".$row["id"]."][cycles]",
				"options" => $cycles_list->names(),
				"multiple" => 1,
				"value" => $settings[$row["id"]]["cycles"]
			));
			$row["work_hrs_per_week"] = html::textbox(array(
				"name" => "d[".$row["id"]."][work_hrs_per_week]",
				"size" => 5,
				"value" => $settings[$row["id"]]["work_hrs_per_week"]
			));
			$row["no_plan_night"] = html::checkbox(array(
				"name" => "d[".$row["id"]."][no_plan_night]",
				"ch_value" => 1,
				"checked" => $settings[$row["id"]]["no_plan_night"] == 1
			));
			$row["max_overtime"] = html::textbox(array(
				"name" => "d[".$row["id"]."][max_overtime]",
				"size" => 5,
				"value" => $settings[$row["id"]]["max_overtime"]
			)).html::hidden(array(
				"name" => "d[".$row["id"]."][set]",
				"value" => 1
			));

			$row["free_days_after_night_shift"] = html::textbox(array(
				"name" => "d[".$row["id"]."][free_days_after_night_shift]",
				"size" => 5,
				"value" => $settings[$row["id"]]["free_days_after_night_shift"]
			));
			$t->set_data($idx, $row);
		}
		return PROP_OK;
	}

	function _fwd_co($arr)
	{
		static $i;
		if (!$i)
		{
			$i = get_instance(CL_CRM_COMPANY);
		}
		$c = reset($arr["obj_inst"]->connections_to(array("from.class_id" => CL_ROSTERING_WORKBENCH)));
		$o = $c->from();
		$obj = obj($o->prop("owner"));
		$a2 = $arr;
		unset($a2["obj_inst"]);
		$a2["obj_inst"] = $obj;
		$a2["request"]["id"] = $obj->id();
		return $i->get_property($a2);
	}

	function _setp_fwd_co($arr)
	{
		static $i;
		if (!$i)
		{
			$i = get_instance(CL_CRM_COMPANY);
		}
		$c = reset($arr["obj_inst"]->connections_to(array("from.class_id" => CL_ROSTERING_WORKBENCH)));
		$o = $c->from();
		$obj = obj($o->prop("owner"));
		$a2 = $arr;
		unset($a2["obj_inst"]);
		$a2["obj_inst"] = $obj;
		$a2["request"]["id"] = $obj->id();
		return $i->set_property($a2);
	}

	function _setp_cedit_table($arr)
	{
		// save stuff from table
		$sd = $arr["obj_inst"]->meta("scenario_data");
		foreach(safe_array($arr["request"]["d"]) as $id => $dat)
		{
			$sd[$id] = $dat;
		}
		$arr["obj_inst"]->set_meta("scenario_data", $sd);
	}

	function callback_mod_retval($arr)
	{
		if($arr['request']['unit'])
		{
			$arr['args']['unit'] = $arr['request']['unit'];
		}

		if($arr['request']['cat'])
		{
			$arr['args']['cat'] = $arr['request']['cat'];
		}
	}

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
		$arr['unit'] = $_GET["unit"];
		$arr['cat'] = $_GET["cat"];
		$arr["sbt_data"] = 0;
		$arr["sbt_data2"] = 0;
	}

	function _cedit_tb($arr)
	{
		$tb =& $arr["prop"]["vcl_inst"];
		$tb->add_button(array(
			"name" => "save",
			"img" => "save.gif",
			"tooltip" => t("Salvesta"),
			"action" => ""
		));
	}

	function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_rostering_scenario(aw_oid int primary key, aw_work_hrs_per_week int, aw_no_plan_night int, aw_max_overtime int, aw_free_days_after_night_shift int)");
			return true;
		}
	}
}
?>
