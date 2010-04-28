<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/workflow/workflow_transition.aw,v 1.3 2008/01/31 13:55:40 kristo Exp $
// workflow_transition.aw - J&auml;rgnevus 
/*

@classinfo syslog_type=ST_WORKFLOW_TRANSITION relationmgr=yes maintainer=kristo

@tableinfo aw_wf_transitions index=aw_id master_table=objects master_index=brother_of

@default group=general

@property predicate type=textarea rows=10 cols=40 field=content table=aw_wf_transitions
@caption Predikaat

@property process_id field=aw_process_id table=aw_wf_transitions type=hidden
@property from_act field=aw_from_act_id table=aw_wf_transitions type=hidden
@property to_act field=aw_to_act_id table=aw_wf_transitions type=hidden

@reltype PROCESS clid=CL_PROCESS value=2
@caption protsess

*/

class workflow_transition extends class_base
{
	const AW_CLID = 253;

	function workflow_transition()
	{
		$this->init(array(
			"tpldir" => "workflow/workflow_transition",
			"clid" => CL_WORKFLOW_TRANSITION
		));
	}

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
}
?>
