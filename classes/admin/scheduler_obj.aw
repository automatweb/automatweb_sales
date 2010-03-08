<?php
/*
@classinfo relationmgr=yes syslog_type=ST_SCHEDULER_OBJ maintainer=kristo
@default table=objects
@default field=meta
@default method=serialize
@default group=general

	@property login_uid type=textbox 
	@caption Kasutaja

	@property login_password type=password
	@caption Parool

@default group=recurrence

	@property recurrence type=releditor reltype=RELTYPE_RECURRENCE use_form=emb mode=manager
	@caption Kordused

@groupinfo recurrence caption=Kordused submit=no

@reltype TARGET_OBJ value=1 clid=CL_XML_IMPORT,CL_LIVELINK_IMPORT
@caption Sihtobjekt

@reltype RECURRENCE value=2 clid=CL_RECURRENCE
@caption Kordus

@reltype AW_LOGIN value=3 clid=CL_AW_LOGIN
@caption AW login

*/
class scheduler_obj extends class_base
{
	function scheduler_obj()
	{
		$this->init(array(
			"clid" => CL_SCHEDULER
		));
	}

	function callback_post_save($arr)
	{
		// use only the first for now
		$conns = $arr["obj_inst"]->connections_from(array(
			"type" => "RELTYPE_RECURRENCE",
		));

		if (sizeof($conns) > 0)
		{
			$first = reset($conns);
			$rep_id = $first->prop("to");
		};

		$targets = $arr["obj_inst"]->connections_from(array(
			"type" => "RELTYPE_TARGET_OBJ",
		));
		foreach($targets as $target)
		{
			$target_id = $target->prop("to");
			$target_obj = $target->to();

			// try and schedule the event
			if ($rep_id && $target_id)
			{
				$sch = get_instance("scheduler");
				$event_url = $this->mk_my_orb("invoke",array("id" => $target_id),$target_obj->class_id());
				$sch->add(array(
					"event" => str_replace("/automatweb", "", $event_url),
					"rep_id" => $rep_id,
					"uid" => $arr["obj_inst"]->prop("login_uid"),
					"password" => $arr["obj_inst"]->prop("login_password"),
				));
			};
		}
	}
}
?>
