<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/calendar/vcl/calendar_selector.aw,v 1.8 2007/12/06 14:32:59 kristo Exp $
/*
@classinfo maintainer=kristo
*/
class calendar_selector extends core
{
	function calendar_selector()
	{
		$this->init("");
	}


	function init_vcl_property($arr)
	{
		$brlist = new object_list(array(
			"brother_of" => $arr["obj_inst"]->id(),
			// ignore site id's for this list
			"site_id" => array(),
		));

		foreach($brlist->arr() as $o)
		{
			$plrlist[$o->parent()] = $o->id();
		};

		$all_props = array();
	
		$planners = new object_list(array(
			"class_id" => CL_PLANNER,
			"sort_by" => "name",
			"status" => STAT_ACTIVE,
			"site_id" => array(),
		));

		$propname = $arr["property"]["name"];

		foreach($planners->arr() as $planner)
		{
			$event_folder = $planner->prop("event_folder");
			if ($event_folder != 0)
			{
				$id = $planner->id();
				$all_props["${propname}${id}"] = array(
					"type" => "checkbox",
					"name" => "${propname}[${event_folder}]",
					"caption" => html::href(array(
						"url" => $this->mk_my_orb("change",array("id" => $id,"group" => "views"),CL_PLANNER),
						"caption" => 
							"<font color='black'>" . $planner->name() . "</font>",
					)),
					"ch_value" => $event_folder,
					"value" => isset($plrlist[$event_folder]) ? $event_folder : 0,
				);
			};
		};
		return $all_props;
	}

	function process_vcl_property($arr)
	{
		$event_obj  = $arr["obj_inst"];

		$parents = array();
		
		$planners = new object_list(array(
			"class_id" => CL_PLANNER,
			"status" => STAT_ACTIVE,
			"site_id" => array(),
		));

		foreach($planners->arr() as $planner_obj)
		{
			if (is_oid($planner_obj->prop("event_folder")))
			{
				$parents[] = $planner_obj->prop("event_folder");
			};
		};		

		$brlist = new object_list(array(
			"parent" => $parents,
			"brother_of" => $event_obj->id(),
		));

		$plrlist = array();

		foreach($brlist->arr() as $o)
		{
			$id = $o->id();
			// this check ensures that the original object is not deleted
			if ($id != $event_obj->id())
			{
				$plrlist[$o->parent()] = $id;
			};
		};

		$all_props = array();

		$new_ones = array();
		if (is_array($arr["prop"]["value"]))
		{
			$new_ones = $arr["prop"]["value"];
		};

		foreach($plrlist as $plid => $evid)
		{
			if (!$new_ones[$plid])
			{
				//print "deleting $evid<br>";
				$ev_obj = new object($evid);
				$ev_obj->delete();
			};
			unset($new_ones[$plid]);
		};

		// now new_ones sisaldab nende folderite id-sid, millega ma pean seose looma
		foreach($new_ones as $plid)
		{
			$plr_obj = new object($plid);
			$bro = $event_obj->create_brother($plid);
		};

	}

};
?>
