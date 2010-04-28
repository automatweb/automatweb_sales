<?php

namespace automatweb;
/*

@classinfo syslog_type=ST_WORKFLOW_CONFIG no_status=1 maintainer=kristo
@classinfo relationmgr=yes

@groupinfo general caption=Üldine

@default table=objects
@default group=general
@default field=meta
@default method=serialize

@property actor_rootmenu type=relpicker reltype=RELTYPE_ROOTMENU group=config_actor
@caption Tegijate juurmenüü

@property action_rootmenu type=relpicker reltype=RELTYPE_ROOTMENU group=config_action
@caption Tegevuste juurmenüü

@property entity_rootmenu type=relpicker reltype=RELTYPE_ROOTMENU group=config_entity
@caption Juhtumite juurmenüü

@property entity_instance_rootmenu type=relpicker reltype=RELTYPE_ROOTMENU group=config_entity
@caption Juhtumite sisestuste juurmenüü

@property process_rootmenu type=relpicker reltype=RELTYPE_ROOTMENU group=config_process
@caption Protsesside juurmenüü

@groupinfo config_actor caption="Tegijate seaded"
@groupinfo config_action caption="Tegevuste seaded"
@groupinfo config_entity caption="Juhtumite seaded"
@groupinfo config_process caption="Protsesside seaded"

@reltype ROOTMENU value=1 clid=CL_MENU
@caption juurmenüü


*/

class workflow_config extends class_base
{
	const AW_CLID = 177;

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
