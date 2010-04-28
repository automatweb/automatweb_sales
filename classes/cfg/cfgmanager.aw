<?php

namespace automatweb;
/*
@default table=objects
@default group=general

@property def_cfgmanager type=checkbox ch_value=1 field=subclass
@caption Default seadete haldur

@default field=meta
@default method=serialize

@property config type=table store=no group=config no_caption=1
@caption Tabel

@reltype C_GROUP value=1 clid=CL_GROUP
@caption Grupp

@reltype C_CFGFORM value=2 clid=CL_CFGFORM
@caption Seadete vorm

@groupinfo config caption=Seaded
@classinfo relationmgr=yes syslog_type=ST_CFGMANAGER maintainer=kristo

*/
class cfgmanager extends class_base
{
	const AW_CLID = 122;

	private $formlist = array();

	function cfgmanager($args = array())
	{
		$this->init(array(
			"clid" => CL_CFGMANAGER,
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$rv = PROP_OK;
		switch($prop["name"])
		{
			case "config":
				$this->get_config_table(&$arr);
				break;
		};
		return $rv;
	}

	function set_property($arr)
	{
		$prop = &$arr["prop"];
		$rv = PROP_OK;
		switch($prop["name"])
		{
			case "config":
				$arr["obj_inst"]->set_meta("use_form",$arr["request"]["use_form"]);
				break;
		};
		return $rv;
	}

	// there are 2 distinct roles
	// 1 - apply cfgform by group
	// 2 - apply cfgform to all classes
	private function get_config_table($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "group",
			"caption" => t("Grupp"),
		));

		// I need a list of all connected groups
		$groups = $arr["obj_inst"]->connections_from(array(
			"type" => "RELTYPE_C_GROUP",
		));

		$grplist = array();
		foreach($groups as $group)
		{
			$grplist[$group->prop("to")] = $group->prop("to.name");
		};
		
		$forms = $arr["obj_inst"]->connections_from(array(
			"type" => "RELTYPE_C_CFGFORM",
		));

		$by_subclass = array();

		// I need a subclass for each too
		$this->formlist = array("" => "");
		foreach($forms as $form)
		{
			$target = $form->to();
			$subclid = $target->subclass();
			if (empty($this->formlist[$subclid]))
			{
				$this->formlist[$subclid][""] = "";
			};
			$this->formlist[$subclid][$form->prop("to")] = $form->prop("to.name");
			$by_subclass[$target->subclass()]++;
		};
		
		$tmp = aw_ini_get("classes");
		foreach($by_subclass as $subclid => $fubar)
		{
			$t->define_field(array(
				"name" => "f_" . $subclid,
				"caption" => $tmp[$subclid]["name"],
				"callback" => array(&$this, "callb_form_picker"),
				"callb_pass_row" => true,
			));
		}


		$this->use_form = $arr["obj_inst"]->meta("use_form");

		foreach($grplist as $grpid => $grpname)
		{
			$data = array(
				"group" => $grpname,
				"grpid" => $grpid,
			);
			foreach($by_subclass as $subclid => $fubar)
			{
				$data["f_" . $subclid] = 0;
			};
			$t->define_data($data);
		};
	}

	function callb_form_picker($arr)
	{
		// how do I figure the current cell?
		list(,$subclid) = explode("_",$arr["_this_cell"]);
		return html::select(array(
			"name" => "use_form[$subclid][" . $arr["grpid"] . "]",
			"options" => $this->formlist[$subclid],
			"selected" => $this->use_form[$subclid][$arr["grpid"]],
		));
	}
};
?>
