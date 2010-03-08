<?php
// cache_updater.aw - Cache uuendamine
/*

@classinfo syslog_type=ST_CACHE_UPDATER relationmgr=yes no_comment=1 no_status=1 maintainer=kristo

@default table=objects
@default group=general

@property repeater type=relpicker reltype=RELTYPE_REPEATER field=meta method=serialize
@caption Kordus

@reltype REPEATER value=1 clid=CL_RECURRENCE
@caption kordus

*/

class cache_updater extends class_base
{
	function cache_updater()
	{
		$this->init(array(
			"tpldir" => "core/cache_updater",
			"clid" => CL_CACHE_UPDATER
		));
	}

	function callback_post_save($arr)
	{
		if (is_oid($arr["obj_inst"]->prop("repeater")) && $this->can("view", $arr["obj_inst"]->prop("repeater")))
		{
			$this->_add_scheduler($arr["obj_inst"]);
		}
	}

	function _add_scheduler($o)
	{
		$url = str_replace("automatweb/", "", $this->mk_my_orb("do_clear", array("id" => $o->id())));

		$sc = get_instance("scheduler");
		$sc->remove(array(
			"event" => $url,
		));

		// get repeater
		$rep = obj($o->prop("repeater"));
		$rep_i = $rep->instance();

		$ts = $rep_i->get_next_event(array("id" => $rep->id()));

		$sc->add(array(
			"event" => $url,
			"time" => $ts
		));
	}

	/** clears cache and adds next repeater

		@attrib name=do_clear nologin=1

		@param id required

	**/
	function do_clear($arr)
	{
		$mt = get_instance("cache");
		$mt->file_clear_pt("menu_area_cache");
		$mt->file_clear_pt("storage_search");
		$mt->file_clear_pt("storage_object_data");
		$mt->file_clear_pt("html");

		$this->_add_scheduler(array(obj($arr["id"])));
	}
}
?>
