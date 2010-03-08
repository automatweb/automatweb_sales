<?php

/*

@classinfo syslog_type=ST_ENTITY maintainer=kristo
@classinfo relationmgr=yes

@tableinfo aw_wf_entity_types index=aw_id master_table=objects master_index=brother_of

@default table=objects
@default group=general

@property description type=textarea field=meta method=serialize
@caption Kirjeldus

@property entity_cfgform type=relpicker reltype=RELTYPE_ENT_CFGFORM field=aw_cfgform_id table=aw_wf_entity_types
@caption Konfivorm

@property entity_process type=relpicker reltype=RELTYPE_PROCESS field=aw_process_id table=aw_wf_entity_types
@caption Protsess

@property entity_actor type=relpicker reltype=RELTYPE_ACTOR field=aw_actor_id table=aw_wf_entity_types
@caption Tegija

@reltype INSTRUCTION clid=CL_IMAGE,CL_FILE value=1
@caption instruktsioon

@reltype ENT_CFGFORM clid=CL_CFGFORM value=2
@caption konfiguratsioonivorm

@reltype ACTOR clid=CL_ACTOR value=3
@caption tegija

@reltype PROCESS clid=CL_PROCESS value=4
@caption protsess

@reltype RESOURCE clid=CL_WORKFLOW_RESOURCE value=5
@caption ressurss

*/

class entity extends class_base
{
	function entity()
	{
		// change this to the folder under the templates folder, where this classes templates will be, 
		// if they exist at all. the default folder does not actually exist, 
		// it just points to where it should be, if it existed
		$this->init(array(
			'clid' => CL_ENTITY
		));
	}
	
	function get_property($args)
	{
		$data = &$args["prop"];
		$name = $data["name"];
		$retval = PROP_OK;
		if ($name == "comment")
		{
			return PROP_IGNORE;
		};
	}
}
?>
