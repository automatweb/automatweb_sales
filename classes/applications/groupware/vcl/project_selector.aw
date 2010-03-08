<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/groupware/vcl/project_selector.aw,v 1.15 2009/04/28 14:03:56 robert Exp $
/*
@classinfo maintainer=kristo
*/
class project_selector extends core
{
	function project_selector()
	{
		$this->init("");
	}

	function init_vcl_property($arr)
	{
		$orig = $arr["obj_inst"]->get_original();

		$olist = new object_list(array(
			"brother_of" => $orig->id(),
		));
		$prjlist = array();
		$xlist = array();
		foreach($olist->arr() as $o)
		{
			$xlist[$o->parent()] = 1;
		};
		$all_props = array();
		$prop = $arr["prop"];

		$propname = $prop["name"];
		$by_parent = array();

		// create a list of projects sorted by parent for better overview
		// default behaviour is to show all projects the current user participiates in
		// or if all_project is set then all project in the system
		if (1 == $prop["all_projects"])
		{
			$olist = new object_list(array(
				"class_id" => CL_PROJECT,
			));
		
			foreach($olist->arr() as $o)
			{
				$pr = new object($o->parent());
				$id = $o->id();

				$by_parent[$pr->id()][$o->id()] = $o->name();
			};

			$all_props = array();

		}
		else
		{
			$users = get_instance("users");
			$user = new object(aw_global_get("uid_oid"));
			$conns = $user->connections_to(array(
				"from.class_id" => CL_PROJECT,
				"sort_by" => "from.name",
			));


			foreach($conns as $conn)
			{
				$from = $conn->from();
				$by_parent[$from->parent()][$from->id()] = $from->name();
			};
		};

		$tree = aw_ini_get("project.tree");
		
		if (1 == aw_ini_get("project.tree"))
		{
			$c = new connection();
			$conns = $c->find(array(
				"from.class_id" => CL_PROJECT,
				"to.class_id" => CL_PROJECT,
				"type" => 1,
			));

			$links = array();
			foreach($conns as $conn)
			{
				$links[$conn["from"]] = $conn["to"];
			};
			
		};

		// now put together a nice layout
		foreach ($by_parent as $parent_id => $items)
		{
			$pr_obj = new object($parent_id);
			$all_props["${propname}${parent_id}tx"] = array(
				"name" => "${propname}[${parent_id}tx]",
				"type" => "text",
				"subtitle" => 1,
				"value" => $pr_obj->name(),
			);
			asort($items);
			foreach($items as $item_id => $item_name)
			{
				$name = $propname . $item_id;
				$item = new object($item_id);
				$color = ($links[$item_id]) ? "black" : "red";
				$all_props[$propname . $item_id] = array(
					"type" => "checkbox",
					"name" => $propname . "[" . $item_id . "]",
					"caption" => is_admin() ? html::href(array(
						"url" => $this->mk_my_orb("change",array("id" => $item_id),CL_PROJECT),
						"caption" => 
							"<font color='$color'>" . $item->name() . "</font>",
					)) : $item->name(),
					"ch_value" => isset($xlist[$item_id]) ? $xlist[$item_id] : 0,
					"value" => 1,
				);
			};

		}
		return $all_props;
	}

	function process_vcl_property($arr)
	{
		$event_obj = $arr["obj_inst"];
		// 1) retreieve all connections that this event has to projects
		// 2) remove those that were not explicitly checked in the form
		// 3) create new connections which did not exist before
		$orig = $arr["obj_inst"]->get_original();
		
		// figure out all current brothers
		$olist = new object_list(array(
			"brother_of" => $orig->id(),
			"site_id" => array(),
			"lang_id" => array(),
		));

		// determine all projects that this event is part of,
		// compare that list to the selected items in the form
		// and put the event (create a brother) into all the projects
		// that it wasn't already a part of
		$xlist = array();
		foreach($olist->arr() as $o)
		{
			// hm, originaali n2idatakse aga listi ei panda. Ongi nii v6i?
			$p_o = new object($o->parent());
			if ($p_o->class_id() != CL_PROJECT)
			{
				continue;
			};

			if ($o->id() != $o->brother_of())
			{
				//$xlist[$o->id()] = $o->parent();
				$xlist[$o->parent()] = $o->id();
			};
		};

		// now, how do I know which projects are on the lowest level?

		// simple, I ask for connections and the projects which do not have outgoing connections
		// will be used as masters.

		$new_ones = array();
		if (is_array($arr["prop"]["value"]))
		{
			$new_ones = $arr["prop"]["value"];
		}

		foreach($xlist as $folder_id => $obj_id)
		{
			if (!$new_ones[$folder_id])
			{
				//print "deleting $obj_id<br>";
				$bo = new object($obj_id);
				$bo->delete();
			};
			unset($new_ones[$folder_id]);
		};

		if (1 == aw_ini_get("project.tree"))
		{
			$real_parent = $orig->parent();
			
			$c = new connection();
			$conns = $c->find(array(
				"from.class_id" => CL_PROJECT,
				"to.class_id" => CL_PROJECT,
				"type" => 1,
			));

			$links = array();
			foreach($conns as $conn)
			{
				$links[$conn["from"]] = $conn["to"];
			};
			
			$olist = new object_list(array(
				"class_id" => CL_PROJECT,
			));

			$parentcount = 0;
			foreach($new_ones as $new_id => $whatever)
			{
				if (empty($links[$new_id]))
				{
					$parentcount++;
					$new_parent = $new_id;
				};
			};

			if ($parentcount > 1)
			{
				$arr["prop"]["error"] = "S&uuml;ndmus ei saa korraga olla mitmes viimase taseme projektis!";	
				return PROP_ERROR;
			};

			if ($new_parent)
			{
				// that easy huh?
				if ($event_obj->is_brother())
				{
					$orig->set_parent($new_parent);
					$orig->save();
					$new_ones[$event_obj->parent()] = 1;
				}
				else
				{
					$new_ones[$event_obj->parent()] = 1;
					$arr["obj_inst"]->set_parent($new_parent);
					$event_obj->set_parent($new_parent);
					$event_obj->save();

				};
				
				// do not create a brother if there is an object there already
				unset($new_ones[$new_parent]);

				//print "duh?";
			};

			// so now, what do I have to do?

			// change the parent of the original
			// create brothers under all others


		};

		foreach($new_ones as $new_id => $whatever)
		{
			//print "creating brother under $new_id<br>";
			$event_obj->create_brother($new_id);
		};


	}
};
?>
