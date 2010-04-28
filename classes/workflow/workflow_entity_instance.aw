<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/workflow/workflow_entity_instance.aw,v 1.3 2008/01/31 13:55:40 kristo Exp $
// workflow_entity_instance.aw - Protsessi kulg 
/*

@classinfo syslog_type=ST_WORKFLOW_ENTITY_INSTANCE relationmgr=yes maintainer=kristo

@default group=general

@tableinfo aw_wf_entities index=aw_id master_table=objects master_index=brother_of

@property entity_type type=textbox field=aw_entity_type_id table=aw_wf_entities
@caption Juhtumi t&uuml;&uuml;p

@property state type=textbox field=aw_state table=aw_wf_entities
@caption Olek

@property obj_id type=textbox field=aw_obj_id table=aw_wf_entities
@caption Sisuobjekt

*/

class workflow_entity_instance extends class_base
{
	const AW_CLID = 254;

	function workflow_entity_instance()
	{
		$this->init(array(
			"tpldir" => "workflow/workflow_entity_instance",
			"clid" => CL_WORKFLOW_ENTITY_INSTANCE
		));
	}

	//////
	// class_base classes usually need those, uncomment them if you want to use them

	/*
	function get_property($arr)
	{
		$data = &$arr["prop"];
		$retval = PROP_OK;
		switch($data["name"])
		{

		};
		return $retval;
	}
	*/

	/*
	function set_property($arr = array())
	{
		$data = &$arr["prop"];
		$retval = PROP_OK;
		switch($data["name"])
                {

		}
		return $retval;
	}	
	*/

	function get_current_state($oid)
	{
		$ei = obj($oid);
		if (!$ei->prop("state"))
		{
			$entity_type = obj($ei->prop("entity_type"));
			$process = obj($entity_type->prop("entity_process"));
			return obj($process->prop("root_action"));
		}
		return obj($ei->prop("state"));
	}

	function get_possible_next_states($oid)
	{
		$ei = obj($oid);
		$entity_type = obj($ei->prop("entity_type"));

		$cur_state = $this->get_current_state($oid);

		$ol = new object_list(array(
			"class_id" => CL_WORKFLOW_TRANSITION,
			"process_id" => $entity_type->prop("entity_process"),
			"from_act" => $cur_state->id()
		));

		$ret = array();
		for($o = $ol->begin(); !$ol->end(); $o = $ol->next())
		{
			$ta = obj($o->prop("to_act"));
			$ret[$ta->id()] = "(".$o->name().") -> ".$ta->name();
		}

		return $ret;
	}
}
?>
