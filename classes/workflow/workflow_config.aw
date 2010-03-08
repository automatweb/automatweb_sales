<?php
/*

@classinfo syslog_type=ST_WORKFLOW_CONFIG no_status=1 maintainer=kristo
@classinfo relationmgr=yes

@groupinfo general caption=�ldine

@default table=objects
@default group=general
@default field=meta
@default method=serialize

@property actor_rootmenu type=relpicker reltype=RELTYPE_ROOTMENU group=config_actor
@caption Tegijate juurmen��

@property action_rootmenu type=relpicker reltype=RELTYPE_ROOTMENU group=config_action
@caption Tegevuste juurmen��

@property entity_rootmenu type=relpicker reltype=RELTYPE_ROOTMENU group=config_entity
@caption Juhtumite juurmen��

@property entity_instance_rootmenu type=relpicker reltype=RELTYPE_ROOTMENU group=config_entity
@caption Juhtumite sisestuste juurmen��

@property process_rootmenu type=relpicker reltype=RELTYPE_ROOTMENU group=config_process
@caption Protsesside juurmen��

@groupinfo config_actor caption="Tegijate seaded"
@groupinfo config_action caption="Tegevuste seaded"
@groupinfo config_entity caption="Juhtumite seaded"
@groupinfo config_process caption="Protsesside seaded"

@reltype ROOTMENU value=1 clid=CL_MENU
@caption juurmen��


*/

class workflow_config extends class_base
{
	function workflow_config()
	{
		$this->init(array(
			'clid' => CL_WORKFLOW_CONFIG
		));
	}

	function get_property($args = array())
	{
		$data = &$args["prop"];
		$name = $data["name"];
		$retval = PROP_OK;
	}
}
?>
