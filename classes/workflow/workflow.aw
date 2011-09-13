<?php
/*

	@classinfo syslog_type=ST_WORKFLOW maintainer=kristo
	@classinfo relationmgr=yes

	@groupinfo general caption=Üldine

	@default table=objects
	@default group=general

	@property config type=relpicker reltype=RELTYPE_WORKFLOW_CONFIG field=meta method=serialize
	@caption Konfiguratsioon

	// --views
	
	@default view=show
	
	@property id type=hidden table=objects field=oid group=show_actors,show_actions,show_entities,show_processes

	@default store=no
	
	@property show_entities type=callback callback=callback_show_entities group=show_entities
	@caption Juhtumid

	@property show_actions type=callback callback=callback_show_actions group=show_actions
	@caption Tegevused
	
	@property show_processes type=callback callback=callback_show_processes group=show_processes 
	@caption Protsessid
	
	@property show_actors type=callback callback=callback_show_actors group=show_actors
	@caption Tegijad
	
	@property show_documentation type=text group=show_documentation
	@caption Juhendmaterjalid
	
	@groupinfo show_entities caption=Juhtumid submit=no
	@groupinfo show_processes caption=Protessid submit=no
	@groupinfo show_actions caption=Tegevused submit=no
	@groupinfo show_actors caption=Tegijad submit=no
	@groupinfo show_documentation caption=Juhendmaterjalid submit=no

	@reltype WORKFLOW_CONFIG clid=CL_WORKFLOW_CONFIG value=1
	@caption Konfiguratsioon 

*/

class workflow extends class_base
{
	function workflow()
	{
		$this->init(array(
			"tpldir" => "workflow",
			"clid" => CL_WORKFLOW
		));
	}

	function get_property($arr)
	{
		$data = &$arr["prop"];
		$name = $data["name"];
		$retval = PROP_OK;
		switch($name)
		{
			case "preview":
				$data["value"] = html::href(array(
					"url" => $this->mk_my_orb("view",array("id" => $arr["obj_inst"]->id())),
					"caption" => t("Näita"),
					"target" => "_blank",
				));
				break;

		};
		return $retval;
	}

	function set_property($arr)
	{
		$data = &$arr["prop"];
		$retval = PROP_OK;
		switch($data["name"])
		{
			case "show_entities":
				if ($arr["request"]["subgroup"] == "add_entity")
				{
					$this->create_entity($arr);
				}
				else
				{
					// advance existing entities - if there is anything
					// to advance at all
					$this->process_entities($arr);
				}
				break;
		};
		return $retval;
	}

	function callback_mod_retval($arr = array())
	{
		$args = &$arr["args"];
		if (isset($arr["request"]["treeroot"]))
		{
			$args["treeroot"] = $arr["request"]["treeroot"];
		};
	}

	function init_callback_view(&$data,$args = array())
	{
		// try and load the configuration object
		$retval = PROP_OK;

		$cfgid = $args["obj_inst"]->prop("config");


		if (empty($cfgid))
		{
			$data["error"] = t("Konfiguratsiooniobjekt on valimata!");
			return PROP_ERROR;
		};
		
		$this->cfg_obj = new object($cfgid);

		$entity_rootmenu_id = $this->cfg_obj->prop("entity_rootmenu");

		if (empty($entity_rootmenu_id))
		{
			$data["error"] = t("Juhtumite rootmenüü on valimata!");
			return PROP_ERROR;
		};

		$this->entity_rootmenu = new object($entity_rootmenu_id);


		$entity_instance_rootmenu_id = $this->cfg_obj->prop("entity_instance_rootmenu");

		if (empty($entity_rootmenu_id))
		{
			$data["error"] = t("Juhtumite sisestuste rootmenüü on valimata!");
			return PROP_ERROR;
		};

		$this->entity_instance_rootmenu = new object($entity_instance_rootmenu_id);


		$action_rootmenu_id = $this->cfg_obj->prop("action_rootmenu");

		if (empty($action_rootmenu_id))
		{
			$data["error"] = t("Tegevuste rootmenüü on valimata!");
			return PROP_ERROR;
		}

		$this->action_rootmenu = new object($action_rootmenu_id);

		$process_rootmenu_id = new object($this->cfg_obj->prop("process_rootmenu"));

		if (empty($process_rootmenu_id))
		{
			$data["error"] = t("Protsesside rootmenüü on valimata!");
			return PROP_ERROR;
		};

		$this->process_rootmenu = new object($process_rootmenu_id);

		$actor_rootmenu_id = $this->cfg_obj->prop("actor_rootmenu");

		if (empty($actor_rootmenu_id))
		{
			$data["error"] = t("Tegijate rootmenüü on valimata!");
			return PROP_ERROR;
		}
		else
		{
			$this->actor_rootmenu = new object($actor_rootmenu_id);
		};

		return $retval;

	}

	function satisfy_any($arr)
	{
		if (isset($arr["action"]) && empty($this->actions[$arr["action"]]))
		{
			$this->actions[$arr["action"]] = obj($arr["action"]);
		};	
		
		if (isset($arr["actor"]) && empty($this->actors[$arr["actor"]]))
		{
			$this->actors[$arr["actor"]] =  obj($arr["actor"]);
		};	
		
		if (isset($arr["process"]) && empty($this->processes[$arr["process"]]))
		{
			$this->processes[$arr["process"]] = obj($arr["process"]);
		};	
	}


	function callback_show_actors($args = array())
	{
		$status = $this->init_callback_view(&$data,$args);
		if ($status == PROP_ERROR)
		{
			return $status;
		};

		$troot = obj($_GET["tree_filter"] ? $_GET["tree_filter"] : $this->actor_rootmenu);
		if ($troot->class_id() == CL_MENU)
		{
			$actor_tree_filter = new object_tree(array(
				"parent" => $troot->id(),
				"class_id" => array(CL_MENU,CL_ACTOR)
			));
			$actor_list = $actor_tree_filter->to_list();
		}
		else
		{
			$actor_list = new object_list();
			$actor_list->add($troot);
		}

		$tb = get_instance("vcl/toolbar");

		$etype_list = new object_list(array(
			"class_id" => CL_ENTITY,
			"entity_actor" => $actor_list->ids()
		));
			
		$process_list = new object_list();
		for($o = $etype_list->begin(); !$etype_list->end(); $o = $etype_list->next())
		{
			$process_list->add($o->prop("entity_process"));
		}

		switch($_GET["sub_tab"])
		{
			default:
			case "entity":
				$list_html = $this->_do_entity_table(array(
					"filter" => array(
						"entity_type" => $etype_list->ids()
					)
				));

				$tb->add_button(array(
					"name" => "save",
					"tooltip" => t("Salvesta"),
					"url" => "javascript:document.changeform.submit();",
					"img" => "save.gif",
					"class" => "menuButton",
				));
				break;

			case "process":
				$list_html = $this->_do_process_table(array(
					"filter" => array(
						"oid" => $process_list->ids()
					)
				));
				break;

			case "actions":
				$list_html = $this->_do_actions_table(array(
					"filter" => array(
						"processes" => $process_list->ids()
					)
				));
				break;

			case "actors":
				$list_html = $this->_do_actor_table(array(
					"filter" => array(
						"oid" => $actor_list->ids()
					)
				));
				break;
		}

		return $this->_finalize_data(array(
			"rootmenu" => $this->actor_rootmenu,
			"tb" => $tb,
			"list_html" => $list_html,
			"data" => $data,
			"class_id" => CL_ACTOR
		));
	}
	
	function callback_show_actions($args = array())
	{
		$data = array();
		$status = $this->init_callback_view(&$data,$args);
		if ($status == PROP_ERROR)
		{
			return $status;
		};

		$troot = obj($_GET["tree_filter"] ? $_GET["tree_filter"] : $this->action_rootmenu);
		if ($troot->class_id() == CL_MENU)
		{
			$action_tree_filter = new object_tree(array(
				"parent" => $troot->id(),
				"class_id" => array(CL_MENU,CL_ACTION)
			));
			$action_list = $action_tree_filter->to_list();
		}
		else
		{
			$action_list = new object_list();
			$action_list->add($troot);
		}

		$tb = get_instance("vcl/toolbar");

		// get processes for actions
		// to do that, we get all aliases that go TO the list of actions
		$process_list = new object_list();

		$al = $action_list->ids();

		if (count($al) > 0)
		{
			$con = new connection();
			foreach($con->find(array("to" => $al)) as $c)
			{
				// now get the from , that's the process
				$process_list->add($c["from"]);
			}
		}

		switch($_GET["sub_tab"])
		{
			default:
			case "entity":
				// get entity types for processes
				$etype_list = new object_list(array(
					"class_id" => CL_ENTITY,
					"entity_process" => $process_list->ids()
				));

				$list_html = $this->_do_entity_table(array(
					"filter" => array(
						"entity_type" => $etype_list->ids()
					)
				));

				$tb->add_button(array(
					"name" => "save",
					"tooltip" => t("Salvesta"),
					"url" => "javascript:document.changeform.submit();",
					"img" => "save.gif",
					"class" => "menuButton",
				));
				break;

			case "process":
				$list_html = $this->_do_process_table(array(
					"filter" => array(
						"oid" => $process_list->ids()
					)
				));
				break;

			case "actions":
				$list_html = $this->_do_actions_table(array(
					"filter" => array(
						"oid" => $action_list->ids()
					)
				));
				break;

			case "actors":
				$entity_list = new object_list(array(
					"class_id" => CL_ENTITY,
					"entity_process" => $process_list->ids()
				));

				$actors = array();
				for ($o = $entity_list->begin(); !$entity_list->end(); $o = $entity_list->next())
				{
					$actors[$o->prop("entity_actor")] = $o->prop("entity_actor");
				}
				$list_html = $this->_do_actor_table(array(
					"filter" => array(
						"oid" => $actors
					)
				));
				break;
		}

		return $this->_finalize_data(array(
			"rootmenu" => $this->action_rootmenu,
			"tb" => $tb,
			"list_html" => $list_html,
			"data" => $data,
			"class_id" => CL_ACTION
		));
	}
	
	function callback_show_entities($args = array())
	{
		$request = $args["request"];
		if (isset($request["subgroup"]) && $request["subgroup"] == "add_entity")
		{
			$retval = $this->callback_add_entity($args);
			return $retval;
		};
		
		// transparent redirect to the "add new entity" form
		if (isset($request["subgroup"]) && $request["subgroup"] == "entity_log")
		{
			$retval = $this->callback_entity_log($args);
			return $retval;
		};

		$data = array();
		$status = $this->init_callback_view(&$data,$args);
		if ($status == PROP_ERROR)
		{
			return $status;
		};

		$entity_tree_filter = new object_tree(array(
			"parent" => ($_GET["tree_filter"] ? $_GET["tree_filter"] : $this->entity_rootmenu),
			"class_id" => array(CL_MENU,CL_ENTITY)
		));
		$entity_list = $entity_tree_filter->to_list();

		$tb = get_instance("vcl/toolbar");

		switch($_GET["sub_tab"])
		{
			default:
			case "entity":
				$tb->add_menu_button(array(
					"name" => "add",
					"tooltip" => t("Uus"),
				));

				// get entity type list from ot
				$entity_types = array();
				for ($o = $entity_list->begin(); !$entity_list->end(); $o = $entity_list->next())
				{
					if ($o->class_id() == CL_ENTITY)
					{
						$entity_types[] = $o->id();

						$tb->add_menu_item(array(
							"parent" => "add",
							"link" => $this->mk_my_orb("view",array(
								"id" => $args["obj_inst"]->id(),
								"group" => "show_entities",
								"subgroup" => "add_entity",
								"entity_id" => $o->id(),
							)),
							"text" => $o->name(),
						));
					}
				}

				$list_html = $this->_do_entity_table(array(
					"filter" => array(
						"entity_type" => $entity_types
					)
				));

				$tb->add_button(array(
					"name" => "save",
					"tooltip" => t("Salvesta"),
					"url" => "javascript:document.changeform.submit();",
					"img" => "save.gif",
					"class" => "menuButton",
				));

				break;

			case "process":
				// get list of processes, filtered by entity type
				$processes = array();
				for ($o = $entity_list->begin(); !$entity_list->end(); $o = $entity_list->next())
				{
					if ($o->class_id() == CL_ENTITY)
					{
						$processes[$o->prop("entity_process")] = $o->prop("entity_process");
					}
				}
				$list_html = $this->_do_process_table(array(
					"filter" => array(
						"oid" => $processes
					)
				));
				break;

			case "actions":
				// get list of processes, filtered by entity type
				$processes = array();
				for ($o = $entity_list->begin(); !$entity_list->end(); $o = $entity_list->next())
				{
					if ($o->class_id() == CL_ENTITY)
					{
						$processes[$o->prop("entity_process")] = $o->prop("entity_process");
					}
				}
				$list_html = $this->_do_actions_table(array(
					"filter" => array(
						"processes" => $processes
					)
				));
				break;

			case "actors":
				$actors = array();
				for ($o = $entity_list->begin(); !$entity_list->end(); $o = $entity_list->next())
				{
					if ($o->class_id() == CL_ENTITY)
					{
						$actors[$o->prop("entity_actor")] = $o->prop("entity_actor");
					}
				}
				$list_html = $this->_do_actor_table(array(
					"filter" => array(
						"oid" => $actors
					)
				));
				break;
		}

		return $this->_finalize_data(array(
			"rootmenu" => $this->entity_rootmenu,
			"tb" => $tb,
			"list_html" => $list_html,
			"data" => $data,
			"class_id" => CL_ENTITY
		));
	}

	function callback_add_entity(&$data)
	{
		if (!$data["request"]["entity_id"])
		{
			die(t("you did not pick a process<br />"));
		};

		$tdata = array();
		$this->init_callback_view(&$tdata, $data);

		// load entity_type
		$entity_type = obj($data["request"]["entity_id"]);

		// load cfgform so we can get the class_id
		$cfgform = obj($entity_type->prop("entity_cfgform"));

		// create class object
		$cl_o = obj();
		$cl_o->set_class_id($cfgform->prop("subclass"));
		$cl_o->set_parent($this->entity_instance_rootmenu->id());
		$cl_o->save();

		$en_inst = obj();
		$en_inst->set_class_id(CL_WORKFLOW_ENTITY_INSTANCE);
		$en_inst->set_parent($this->entity_instance_rootmenu->id());
		$en_inst->set_prop("entity_type", $data["request"]["entity_id"]);
		$en_inst->set_prop("obj_id", $cl_o->id());
		$en_inst->save();

		$cl_o->set_meta("entity_instance", $en_inst->id());
		$cl_o->save();

		$clss = aw_ini_get("classes");
		$fl = $clss[$cfgform->prop("subclass")]["file"];
		if ($fl == "document")
		{
			$fl = "doc";
		}
		$ru = $this->mk_my_orb("change", array("id" => $data["request"]["id"], "group" => "show_entities", "cb_view" => "show"));

		header("Location: ".$this->mk_my_orb("change", array("id" => $cl_o->id(),"cfgform" => $cfgform->id(), "return_url" => $ru), $fl));
		die();
		return;
	}
	
	function callback_entity_log($args = array())
	{
		$data = array();
		$data["type"] = "text";
		$data["caption"] = t("Log");
		$oid = $args["request"]["oid"];
		load_vcl("table");
		$this->t = new aw_table(array("xml_def" => "workflow/entity_log","layout" => "generic"));
		$q = "SELECT * FROM logtrail WHERE obj_id = $oid";
		$this->db_query($q);
		while($row = $this->db_next())
		{
			$this->satisfy_any(array(
				"actor" => $row["actor_id"],
				"action" => $row["action_id"],
				"process" => $row["process_id"],
			));

			$this->t->define_data(array(
				"tm" => $this->time2date($row["tm"]),
				"actor" => $this->actors[$row["actor_id"]]->name(),
				"action" => $this->actions[$row["action_id"]]->name(),
				"process" => $this->processes[$row["process_id"]]->name(),
				"uid" => $row["uid"],
			));
		};
		$data["value"] = $this->t->draw();
		return array($data);

	}

	function callback_show_processes($args = array())
	{
		// transparent redirect to the "add new entity" form
		if (isset($args["request"]["subgroup"]))
		{
			if ($args["request"]["subgroup"] == "add_process")
			{
				$retval = $this->callback_add_process($args);
				return $retval;
			};
			if ($args["request"]["subgroup"] == "mod_process")
			{
				$retval = $this->callback_mod_process($args);
				return $retval;
			};
		};
		$data = array();
		$status = $this->init_callback_view(&$data,$args);
		if ($status == PROP_ERROR)
		{
			return $status;
		};

		$process_tree_filter = new object_tree(array(
			"parent" => ($_GET["tree_filter"] ? $_GET["tree_filter"] : $this->process_rootmenu),
			"class_id" => array(CL_MENU,CL_PROCESS)
		));
		$process_list = $process_tree_filter->to_list();

		$tb = get_instance("vcl/toolbar");

		switch($_GET["sub_tab"])
		{
			default:
			case "entity":
				$etype_list = new object_list(array(
					"class_id" => CL_ENTITY,
					"entity_process" => $process_list->ids()
				));
				$list_html = $this->_do_entity_table(array(
					"filter" => array(
						"entity_type" => $etype_list->ids()
					)
				));

				$tb->add_button(array(
					"name" => "save",
					"tooltip" => t("Salvesta"),
					"url" => "javascript:document.changeform.submit();",
					"img" => "save.gif",
					"class" => "menuButton",
				));
				break;

			case "process":
				$tb->add_button(array(
					"name" => "add",
					"tooltip" => t("Uus protsess"),
					"url" => $this->mk_my_orb("view",array("id" => $args["obj_inst"]->id(),"group" => "show_processes","subgroup" => "add_process")),
					"img" => "new.gif",
					"class" => "menuButton",
				));

				$list_html = $this->_do_process_table(array(
					"filter" => array(
						"oid" => $process_list->ids()
					)
				));
				break;

			case "actions":
				$list_html = $this->_do_actions_table(array(
					"filter" => array(
						"processes" => $process_list->ids()
					)
				));
				break;

			case "actors":
				$entity_list = new object_list(array(
					"class_id" => CL_ENTITY,
					"entity_process" => $process_list->ids()
				));

				$actors = array();
				for ($o = $entity_list->begin(); !$entity_list->end(); $o = $entity_list->next())
				{
					if ($o->class_id() == CL_ENTITY)
					{
						$actors[$o->prop("entity_actor")] = $o->prop("entity_actor");
					}
				}
				$list_html = $this->_do_actor_table(array(
					"filter" => array(
						"oid" => $actors
					)
				));
				break;
		}

		return $this->_finalize_data(array(
			"rootmenu" => $this->process_rootmenu,
			"tb" => $tb,
			"list_html" => $list_html,
			"data" => $data,
			"class_id" => CL_PROCESS
		));
	}
	
	function callback_add_process($args = array())
	{
		// this is SO bad .. adding processes should go through the alias manager somehow
		$this->read_template("process_wrapper.tpl");
		$this->cfg_obj = new object($args["obj_inst"]->prop("config"));
		$return_url = $this->mk_my_orb("view",array("id" => $args["obj_inst"]->id(),"group" => "show_processes","b1" => 1));
		$process_rootmenu_id = $this->cfg_obj->prop("process_rootmenu");
		$this->vars(array(
			"add_process_link" => $this->mk_my_orb("new",array("parent" => $process_rootmenu_id,"return_url" => $return_url),"process"),
		));
		$data = array(
			"value" => $this->parse(),
			"type" => "text",
			"no_caption" => 1,
		);
		return array($data);
	}
	
	function callback_mod_process($args = array())
	{
		$this->read_template("process_wrapper.tpl");
		$this->cfg_obj = new object($args["obj_inst"]->prop("config"));
		$return_url = $this->mk_my_orb("view",array("id" => $args["obj_inst"]->id(),"group" => "show_processes","b1" => 1));
		$process_rootmenu_id = $this->cfg_obj->prop("process_rootmenu");
		$this->vars(array(
			"add_process_link" => $this->mk_my_orb("change",array("id" => $args["obj_inst"]->id(),"return_url" => $return_url),"process"),
		));
		$data = array(
			"value" => $this->parse(),
			"type" => "text",
			"no_caption" => 1,
		);
		return array($data);
	}

	/**  
		
		@attrib name=view params=name default="0"
		
		@param id required type=int
		@param group optional
		@param subgroup optional
		@param treeroot optional
		@param sgid optional
		@param entity_id optional
		@param oid optional type=int
		@param sub_tab optional
		@param entity_filter optional
		
		@returns
		
		
		@comment

	**/
	function view($args = array())
	{
		return $this->change(array(
			"id" => $args["id"],
			"oid" => $args["oid"],
			"cb_view" => "show",
			"action" => "change",
			"group" => $args["group"],
			"sgid" => $args["sgid"],
			"subgroup" => $args["subgroup"],
			"entity_id" => $args["entity_id"],
			"treeroot" => $args["treeroot"],
		));
	}

	function create_entity($args = array())
	{
		die(t("create ent"));
		if (!$args["request"]["entity_id"])
		{
			die(t("you did not pick a process<br />"));
		};

		$data = array();
		$this->init_callback_view(&$data, $args);

		// load entity_type
		$entity_type = obj($args["request"]["entity_id"]);

		// create class object
		$cl_o = obj();
		$cl_o->set_class_id($entity_type->prop("entity_cfgform"));
		$cl_o->set_parent($this->entity_instance_rootmenu->id());
		$cl_o->save();

		$en_inst = obj();
		$en_inst->set_class_id(CL_WORKFLOW_ENTITY_INSTANCE);
		$en_inst->set_parent($this->entity_instance_rootmenu->id());
		$en_inst->set_prop("entity_type", $args["request"]["entity_id"]);
		$en_inst->set_prop("obj_id", $cl_o->id());
		$en_inst->save();

		$clss = aw_ini_get("classes");
		header("Location: ".$this->mk_my_orb("change", array("id" => $cl_o->id()), $clss[$entity_type->prop("entity_cfgform")]["class"]));
		die();
	}

	/**  
		
		@attrib name=process_entity params=name default="0"
		
		@param id required type=int
		@param entity_id required type=int
		@param process_id required type=int
		@param actor_id required type=int
		
		@returns
		
		
		@comment

	**/
	function process_entity($args = array())
	{
		extract($args);
		// create a new record in logtrail database
		$_entity_tm = time();
		$_entity_uid = aw_global_get("uid");
		$_entity_id = $args["entity_id"];
		$_entity_process = $args["process_id"];
		$_entity_actor = $args["actor_id"];
		$_entity_action = $args["action_id"];
		
		$q = "INSERT INTO logtrail (obj_id,actor_id,action_id,process_id,tm,uid)
			VALUES ('$_entity_id','$_entity_actor','$_entity_action','$_entity_process',
				'$_entity_tm','$_entity_uid')";

		$this->db_query($q);

		/*$current_logtrail = array(
			"actor" => $_entity_actor,
			"action" => $_entity_action,
			"process" => $_entity_process,
		);*/

		$ent_obj = new object($_entity_id);
		//$ent_obj->set_meta("current_logtrail",$current_logtrail);
		$ent_obj->set_prop("state", $_entity_action);
		$ent_obj->save();
	}

	function gen_view_toolbar($args = array())
	{
		$tb = get_instance("vcl/toolbar");
		$tb->add_button(array(
			"name" => "add",
			"tooltop" => t("Uus"),
			"url" => "#",
			"img" => "new.gif",
			"class" => "menuButton",
		));
		return $tb;

	}

	////
	// !Returns the ID of the next action in this process
	// this is where we should use the heavy logic of relation objects
	// to follow branches and stuff
	function get_next_action_for_process($args = array())
	{
		$this->save_handle();
		$process_id = $args["process_id"];
		$action_id = $args["action_id"];
		// load the processes on demand
		$this->satisfy_any(array("process" => $args["process_id"]));

		$meta = $this->processes[$process_id]->meta();

		$action_info = $meta["action_info"];
		$next_actions = array();
		$next = false;

		if (isset($action_info[$args["action_id"]]))
		{
			$next = $action_info[$args["action_id"]];

			if (is_array($next))
			{
				foreach($next as $val)
				{
					$this->satisfy_any(array("action" => $val));

					if (isset($this->actions[$val]))
					{
						$next_actions[$val] = $this->actions[$val]->name();
					};	
				};	
			};	
		};

		return $next_actions;
	}

	function process_entities($args = array())
	{
		$to_advance = new aw_array($args["request"]["next_action"]);
		foreach($to_advance->get() as $key => $val)
		{
			// advance those entities to the next stadium
			if ($val > 0)
			{
				$ent = new object($key);
				$ent_type = obj($ent->prop("entity_type"));

				$this->process_entity(array(
					"entity_id" => $key,
					"action_id" => $val,
					"process_id" => $ent_type->prop("entity_process"),
					"actor_id" => $ent_type->prop("entity_actor"),
				));

			};
		};
	}

	// params: filter - the object_list filter for entity_instance list	
	function _do_entity_table($arr)
	{
		load_vcl("table");
		$t = new aw_table(array(
			"xml_def" => "workflow/entity_list",
			"layout" => "generic",
		));

		if (is_array($arr["filter"]["entity_type"]) && count($arr["filter"]["entity_type"]) < 1)
		{
			$ol = new object_list();
		}
		else
		{
			$filter = array("class_id" => CL_WORKFLOW_ENTITY_INSTANCE) + $arr["filter"];
			$ol = new object_list($filter);
		}

		$wfe = get_instance("workflow/workflow_entity_instance");

		for ($e = $ol->begin(); !$ol->end(); $e = $ol->next())
		{
			$type_o = obj($e->prop("entity_type"));
			$actor_o = obj($type_o->prop("entity_actor"));
			$process_o = obj($type_o->prop("entity_process"));

			$mod = $e->modifiedby();

			// get entity instance real object
			if (!$e->prop("obj_id") || !$this->can("view", $e->prop("obj_id")))
			{
				continue;
			}
			$r_o = obj($e->prop("obj_id"));
			$clss = aw_ini_get("classes");
			$fl = $clss[$r_o->class_id()]["file"];
			if ($fl == "document")
			{
				$fl = "doc";
			}
			$name = html::href(array(
				"url" => $this->mk_my_orb("change", array("id" => $r_o->id(), "return_url" => get_ru()), $fl),
				"caption" => parse_obj_name($r_o->name())
			));

			$cur_state = $wfe->get_current_state($e->id());

			$nacts = array(0 => "") + $wfe->get_possible_next_states($e->id());
			if (count($nacts) < 2)
			{
				$na = t("Protsess on l&otilde;ppenud!");
			}
			else
			{
				// "<span class=\"reallysmall\">".   ."</span>"
				$na = html::select(array(
					"name" => "next_action[".$e->id()."]",
					"options" => $nacts,
					"class" => "reallysmall"
				));
			}
			$t->define_data(array(
				"name" => $name,
				"type" => $type_o->name(),
				"actor" => $actor_o->name(),
				"modifiedby" => $mod,
				"process" => $process_o->name(),
				"action" => $cur_state->name(),
				"next_action" => $na
			));
		}

		$t->set_default_sortby("name");
		$t->sort_by();
		return $t->draw();
	}

	// params: filter - the object_list filter for entity_instance list	
	function _do_process_table($arr)
	{
		load_vcl("table");
		$t = new aw_table(array(
			"xml_def" => "workflow/process_list",
			"layout" => "generic",
		));

		$filter = array("class_id" => CL_PROCESS) + $arr["filter"];
		
		if (is_array($filter["oid"]) && count($filter["oid"]) < 1)
		{
			$ol = new object_list();
		}
		else
		{
			$ol = new object_list($filter);
		}

		for ($e = $ol->begin(); !$ol->end(); $e = $ol->next())
		{
			$ra_o = obj($e->prop("root_action"));

			$mod = $e->modifiedby();

			$entity_types = new object_list(array(
				"class_id" => CL_ENTITY,
				"entity_process" => $e->id()
			));

			$el = new object_list(array(
				"class_id" => CL_WORKFLOW_ENTITY_INSTANCE,
				"entity_type" => $entity_types->ids()
			));

			$t->define_data(array(
				"name" => $e->name(),
				"modifiedby" => $mod,
				"root_action" => $ra_o->name(),
				"entity_count" => $el->count()
			));
		}

		$t->set_default_sortby("name");
		$t->sort_by();
		return $t->draw();
	}

	// params: filter - the object_list filter for actor list
	function _do_actor_table($arr)
	{
		load_vcl("table");
		$t = new aw_table(array(
			"xml_def" => "workflow/actor_list",
			"layout" => "generic",
		));

		$filter = array("class_id" => CL_ACTOR) + $arr["filter"];
		
		if (is_array($filter["oid"]) && count($filter["oid"]) < 1)
		{
			$ol = new object_list();
		}
		else
		{
			$ol = new object_list($filter);
		}

		for ($e = $ol->begin(); !$ol->end(); $e = $ol->next())
		{
			$t->define_data(array(
				"actor" => $e->name(),
			));
		}

		$t->set_default_sortby("name");
		$t->sort_by();
		return $t->draw();
	}

	// params: processes - list of processes whose actions to display
	function _do_actions_table($arr)
	{
		load_vcl("table");
		$t = new aw_table(array(
			"xml_def" => "workflow/action_list",
			"layout" => "generic",
		));

		if (isset($arr["filter"]["processes"]))
		{
			$filter = array(
				"class_id" => CL_PROCESS,
				"oid" => $arr["filter"]["processes"]
			);

			if (count($arr["filter"]["processes"]) < 1)
			{
				$ol = new object_list();
			}
			else
			{
				$ol = new object_list($filter);
			}

			for ($e = $ol->begin(); !$ol->end(); $e = $ol->next())
			{
				$conn = $e->connections_from(array(
					"type" => 10 // RELTYPE_ACTION from process
				));
				foreach($conn as $c)
				{
					$a = $c->to();

					$t->define_data(array(
						"actor" => "",
						"uid" => $e->modifiedby(),
						"process" => $e->name(),
						"action" => $a->name()
					));
				}
			}
		}
		else
		{
			$filter = array("class_id" => CL_ACTION) + $arr["filter"];

			if (is_array($arr["filter"]["oid"]) && count($arr["filter"]["oid"]) < 1)
			{
				$ol = new object_list();
			}
			else
			{
				$ol = new object_list($filter);
			}

			for ($e = $ol->begin(); !$ol->end(); $e = $ol->next())
			{
				$process_list = new object_list();

				$con = new connection();
				foreach($con->find(array("to" => $e->id())) as $c)
				{
					// now get the from , that's the process
					$process_list->add($c["from"]);
				}
						
				$t->define_data(array(
					"actor" => "",
					"uid" => $e->modifiedby(),
					"process" => join(",", $process_list->names()),
					"action" => $e->name()
				));
			}
		}
		
		$t->set_default_sortby("action");
		$t->sort_by();
		return $t->draw();
	}

	function _get_tree($arr)
	{
		$tree = new object_tree(array(
			"parent" => $arr["rootmenu"],
			"class_id" => array(CL_MENU, $arr["class_id"])
		));

		
		$tv = treeview::tree_from_objects(array(
			"tree_opts" => array(
				"type" => TREE_DHTML,
				"tree_id" => "wrkflw",
				"persist_state" => true,
			),
			"root_item" => $arr["rootmenu"],
			"ot" => $tree,
			"var" => "tree_filter"
		));

		return $tv->finalize_tree();
	}

	function _get_tabs($content)
	{
		$tp = tabpanel::simple_tabpanel(array(
			"panel_props" => array("tpl" => "headeronly"),
			"var" => "sub_tab", 
			"default" => "entities",
			"opts" => array(
				"entities" => t("Juhtumid"),
				"process" => t("Protsessid"),
				"actions" => t("Tegevused"),
				"actors" => t("Tegijad")
			)
		));

		return $tp->get_tabpanel(array("content" => $content));
	}

	function _finalize_data($arr)
	{
		extract($arr);
		// fucking hypercube thingie

		classload("vcl/tabpanel");

		$this->read_template("entity_list.tpl");
		$this->vars(array(
			"tree" => $this->_get_tree(array(
				"rootmenu" => $rootmenu,
				"class_id" => $class_id
			)),
			"toolbar" => $tb->get_toolbar(),
			"table" => $this->_get_tabs($list_html),
		));

		$data["value"] = $this->parse();
		$data["type"] = "text";
		$data["no_caption"] = 1;

		return array($data);
	}
}
?>
