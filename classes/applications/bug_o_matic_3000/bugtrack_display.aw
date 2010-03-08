<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/bug_o_matic_3000/bugtrack_display.aw,v 1.28 2009/05/11 07:56:04 robert Exp $
// bugtrack_display.aw - &Uuml;lesannete kuvamine 
/*

@classinfo syslog_type=ST_BUGTRACK_DISPLAY relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=robert

@default table=objects
@default group=general

	@groupinfo data caption=Andmed parent=general
	@default group=data

		@property name type=textbox rel=1 trans=1 table=objects
		@caption Nimi

		@property bugtrack type=relpicker reltype=RELTYPE_BUGTRACK field=meta method=serialize store=connect
		@caption Bugtrack

		@property type_var type=relpicker reltype=RELTYPE_BT_TYPE_VAR field=meta method=serialize store=connect
		@caption Bugi t&uuml&uuml;bi muutuja

		@property bug_doc type=relpicker reltype=RELTYPE_BUG_DOC field=meta method=serialize store=connect
		@caption Bugi dokument

		@property bug_cfgform type=relpicker reltype=RELTYPE_BUG_FORM field=meta method=serialize store=connect
		@caption Bugi seadete vorm

		@property order_doc type=relpicker reltype=RELTYPE_ORDER_DOC field=meta method=serialize store=connect
		@caption Tellimuse dokument

		@property order_cfgform type=relpicker reltype=RELTYPE_ORDER_FORM field=meta method=serialize store=connect
		@caption Tellimuse seadete vorm

	@groupinfo groups caption=Kasutajad submit=no parent=general
	@default group=groups
		
		@property type_var_table type=table store=no
		@caption Peakasutajad
	
	@groupinfo approvals caption=Koosk&otilde;lastamisel submit=no parent=general
	@default group=approvals
		
		@property approvals_table type=table no_caption=1 store=no

	@groupinfo tables caption=Tabelid
		
		@groupinfo task_settings caption=&Uuml;lesanded parent=tables
			
			@property table_settings_tb type=toolbar store=no no_caption=1 group=task_settings,solved_settings,devo_settings,closed_settings
			@property table_settings_table type=table store=no no_caption=1 group=task_settings,solved_settings,devo_settings,closed_settings
		
			@property table_settings_sort type=chooser field=meta method=serialize group=task_settings,solved_settings,devo_settings,closed_settings
			@caption Tabeli sorteerimine

		@groupinfo devo_settings caption=Arendustellimused parent=tables

		@groupinfo solved_settings caption=Lahendatud parent=tables

		@groupinfo closed_settings caption=Suletud parent=tables

	@groupinfo tasks caption=&Uuml;lesanded submit=no
		
		@property tasks_table type=table no_caption=1 store=no group=tasks,solved,closed

	@groupinfo devos caption="Koosk&otilde;lastamisel" submit=no
		
		@property devo_confirm_needed type=table no_caption=1 store=no group=devos
		@property table_filter type=select store=no group=devos,tasks,solved,closed
		@caption Filtreeri

	@groupinfo solved caption=Lahendatud submit=no
	@groupinfo closed caption=Suletud submit=no

	
	
@reltype BUGTRACK value=1 clid=CL_BUG_TRACKER
@caption Bugtrack

@reltype BUG_DOC value=2 clid=CL_DOCUMENT
@caption Bugi dokument

@reltype ORDER_DOC value=3 clid=CL_DOCUMENT
@caption Tellimuse dokument

@reltype BT_TYPE_VAR value=5 clid=CL_META
@caption Bugi t&uuml;&uuml;bi muutuja

@reltype ORDER_FORM value=6 clid=CL_CFGFORM
@caption Tellimuse vorm

@reltype BUG_FORM value=7 clid=CL_CFGFORM
@caption Bugi vorm
*/

class bugtrack_display extends class_base
{
	function bugtrack_display()
	{
		$this->init(array(
			"tpldir" => "applications/bug_o_matic_3000/bugtrack_display",
			"clid" => CL_BUGTRACK_DISPLAY
		));
	}
	
	function _get_approvals_table($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
	}

	function _define_table_from_settings($data, &$t, $arr)
	{
		foreach($data as $id=>$field)
		{
			if($field["in_table"] && $id>0)
			{
				$tfield = array(
					"name" => "f".$id,
					"caption" => $field["caption"],
					"order" => $field["order"],
					"sortable" => $field["sortable"]?1:0
				);
				switch($field["bugprop"])
				{
					case "created":
					case "deadline":
					case "pdeadline":
					case "cust_live_date":
					case "num_hrs_guess":
					case "prognosis":
						$tfield["type"] = "time";
						$tfield["format"] = "d.m.Y";
						$tfield["numeric"] = 1;
					break;
				}
				$t->define_field($tfield);
			}
		}
		if($data["default_sort"])
		{
			$t->set_default_sortby("f".$data["default_sort"]);
		}
		switch($arr["request"]["group"])
		{
			case "closed":
				$sort_prop = "table_settings_sort_closed";
				break;
			case "solved":
				$sort_prop = "table_settings_sort_solved";
				break;
			case "devos":
				$sort_prop = "table_settings_sort_devo";
				break;
			default:
				$sort_prop = "table_settings_sort_task";
		}
		if(!($so = $arr["obj_inst"]->meta($sort_prop)))
		{
			$so = "asc";
		}
		$t->set_default_sorder($so);
	}

	function _insert_data_to_table_from_settings($data, &$t, $ol, $arr)
	{
		foreach($ol->arr() as $oid => $obj)
		{
			if($arr["request"]["group"] == "solved" && (!in_array($obj->prop("bug_status"), array(3,6,7,8,9,13,15))))
			{
				continue;
			}
			if($arr["request"]["group"] == "closed" && $obj->prop("bug_status")!=5)
			{
				continue;
			}
			elseif($arr["request"]["group"] == "tasks" && in_array($obj->prop("bug_status"), array(3,5,6,7,8,9,13,15)))
			{
				continue;
			}
			elseif($obj->class_id()==CL_BUG)
			{
				$c = $obj->connections_from(array(
					"type" => "RELTYPE_DEV_ORDER"
				));
				if(count($c))
				{
					continue;
				}
				$objp = obj($obj->parent());
				if($objp->class_id() == CL_DEVELOPMENT_ORDER)
				{
					continue;
				}
			}
			else
			{
				$bug = get_instance(CL_BUG);
				$bt = $bug->_get_bt($obj);
				if(!$bt || $bt->id() != $arr["obj_inst"]->prop("bugtrack"))
				{
					continue;
				}
			}
			$fields = array();
			foreach($data as $id=>$field)
			{
				if($field["in_table"] && $id>0)
				{
					if($obj->class_id() == CL_BUG)
					{
						$value = $obj->prop($field["bugprop"]);
						if($field["bugprop"] == "bug_status")
						{
							$b = get_instance(CL_BUG);
							$statuses = $b->get_status_list();
							$value = $statuses[$value];
						}
						elseif($field["bugprop"] == "cust_status")
						{
							$b = get_instance(CL_BUG);
							$statuses = $b->get_status_list();
							$value = $statuses[$value];
						}
						elseif($field["bugprop"] == "name")
						{
							$bug_doc = $arr["obj_inst"]->prop("bug_doc");
							if($bug_doc)
							{
								$value = html::href(array(
									"caption" => $value?$value : t("Nimetu"),
									"url" => $this->mk_my_orb("change",array(
										"section" => $bug_doc,
										"return_url" => get_ru(),
										"id" => $oid
									), CL_BUG)
								));
							}
						}
						elseif($field["bugprop"] == "type")
						{
							$value = t("&Uuml;lesanne");
						}
					}
					else
					{
						$value = $obj->prop($field["orderprop"]);
						if($field["orderprop"] == "name")
						{
							$order_doc = $arr["obj_inst"]->prop("order_doc");
							if($order_doc)
							{
								$value = html::href(array(
									"caption" => $value,
									"url" => $this->mk_my_orb("change",array(
										"section" => $order_doc,
										"return_url" => get_ru(),
										"id" => $oid
									), CL_DEVELOPMENT_ORDER)
								));
							}
						}
						elseif($field["orderprop"] == "bug_status")
						{
							$o = get_instance(CL_DEVELOPMENT_ORDER);
							$statuses = $o->get_status_list();
							$value = $statuses[$value];
						}
						elseif($field["orderprop"] == "type")
						{
							$value = t("Arendustellimus");
						}
					}
					if($field["orderprop"]=="bug_type")
					{
						if(is_oid($value))
						{
							$o = obj($value);
							$value = $o->name();
						}
					}
					if($field["orderprop"]=="bug_app")
					{
						if(is_oid($value))
						{
							$o = obj($value);
							$value = $o->name();
						}
					}
					if($field["orderprop"] == "createdby")
					{
						$u = get_instance(CL_USER);
						$user = $u->get_person_for_uid($obj->createdby());
						$value = $user->name();
					}
					elseif($field["orderprop"] == "modifiedby")
					{
						$u = get_instance(CL_USER);
						$user = $u->get_person_for_uid($obj->modifiedby());
						$value = $user->name();
					}
					$fields["f".$id] = $value;
				}
			}
			$t->define_data($fields);
		}
	}

	function _get_tasks_table($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		$t->set_caption(t("&Uuml;lesanded"));
		switch($arr["request"]["group"])
		{
			case "closed":
				$data = $arr["obj_inst"]->meta("closed_settings");
				break;
			case "solved":
				$data = $arr["obj_inst"]->meta("solved_settings");
				break;
			case "devos":
				$data = $arr["obj_inst"]->meta("devo_settings");
				break;
			default:
				$data = $arr["obj_inst"]->meta("task_settings");
		}

		$this->_define_table_from_settings($data, $t, $arr);		

		$u = get_instance(CL_USER);
		$cur_p = get_current_person();
		$uo = obj($u->get_current_user());
		$cur_u = $uo->name();
		$cur = $cur_p->id();
		$sects = $this->get_all_sects();
		if($arr["request"]["sct_filter"] == "all")
		{
			$filt = array(
				"class_id" => array(CL_BUG, CL_DEVELOPMENT_ORDER),
				new object_list_filter(array(
					"logic" => "OR",
					"conditions" => array(
						new object_list_filter(array(
							"logic" => "OR",
							"conditions" => array(
								"who" => $cur,
								"monitors" => $cur,
								"orderer" => $cur,
								"bug_feedback_p" => $cur,
								"createdby" => $cur_u,
								"contactperson" => $cur,
							)
						)),
						new object_list_filter(array(
							"logic" => "OR",
							"conditions" => array(
								"CL_BUG.orderer_unit" => $sects,
								"CL_DEVELOPMENT_ORDER.orderer_unit" => $sects,
							),
						)),
					),
				)),
			);
		}
		elseif($arr["request"]["sct_filter"] && array_search($arr["request"]["sct_filter"], $sects) !== false)
		{
			$sects = array($arr["request"]["sct_filter"]);
			$this->_recur_get_all_sects($sects, obj($arr["request"]["sct_filter"]));
			$filt = array(
				"class_id" => array(CL_BUG, CL_DEVELOPMENT_ORDER),
				new object_list_filter(array(
					"logic" => "OR",
					"conditions" => array(
						"CL_BUG.orderer_unit" => $sects,
						"CL_DEVELOPMENT_ORDER.orderer_unit" => $sects,
					),
				)),
			);
		}
		else
		{
			$filt = array(
				"class_id" => array(CL_BUG, CL_DEVELOPMENT_ORDER),
				new object_list_filter(array(
					"logic" => "OR",
					"conditions" => array(
						"createdby" => $cur_u,
						"CL_DEVELOPMENT_ORDER.bug_createdby" => $cur_u,
					),
				)),
			);
		}
		$filt["site_id"] = array();
		$filt["lang_id"] = array();
		$ol = new object_list($filt);
		$this->_insert_data_to_table_from_settings($data, $t, $ol, $arr);
		
	}
	function _get_type_var_table($arr)
	{
		$cur = $arr["obj_inst"];
		$type_var = $cur->prop("type_var");
		if(is_oid($type_var))
		{
			
			$t = &$arr["prop"]["vcl_inst"];
			$t->set_caption(t("T&uuml;&uuml;bid"));
			$t->define_field(array(
				"name" => "name",
				"caption" => t("Nimi")
			));
			$t->define_field(array(
				"name" => "user",
				"caption" => t("Peakasutaja")
			));
			$ol = new object_list(array(
				"parent" => $type_var,
				"class_id" => array(CL_META)
			));
			foreach($ol->list as $oid)
			{
				$o = obj($oid);
				$u = $arr["obj_inst"]->meta("type".$oid);
				$username = '';
				if($this->can("view", $u))
				{
					$user = obj($u);
					$username = html::get_change_url($user->id(), array(), $user->name());
				}
				$url = $this->mk_my_orb("do_search", array(
					"pn" => "main_user".$oid,
					"clid" => array(
						CL_CRM_PERSON
					),"multiple"=>0,
				),"popup_search");
				$url = "javascript:aw_popup_scroll(\"".$url."\",\"".t("Otsi")."\",550,500)";
				$username=html::href(array(
					"caption" => html::img(array(
						"url" => "images/icons/search.gif",
						"border" => 0
					)),
					"url" => $url
				))." ".$username;
				$t->define_data(array(
					"name" => html::get_change_url($o->id(),array(),$o->name()),
					"user" => $username
				));
			}
		}
	}

	function _set_type_var_table($arr)
	{
		$cur = $arr["obj_inst"];
		$type_var = $cur->prop("type_var");
		if(is_oid($type_var))
		{
			$ol = new object_list(array(
				"parent" => $type_var,
				"class_id" => array(CL_META)
			));
			foreach($ol->list as $oid)
			{
				if($arr["request"]["main_user".$oid])
				{
					$arr["obj_inst"]->set_meta("type".$oid, $arr["request"]["main_user".$oid]);
				}
			}
		}
	}

	function _get_table_settings_tb($arr)
	{
		$tb = &$arr["prop"]["vcl_inst"];
		$tb->add_button(array(
			"name" => "submit",
			"img" => "save.gif",
			"url" => "#",
			"onClick" => "document.changeform.submit()"
		));
	}
	
	function _get_table_settings_table($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		$t->set_caption(t("V&auml;ljad"));
		$t->define_field(array(
			"name" => "nr",
			"caption" => t("Nr."),
			"align" => "center",
			"numeric" => 1
		));
		$t->define_field(array(
			"name" => "order",
			"caption" => t("J&auml;rjekord"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "bugprop",
			"caption" => t("Bugi omadus"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "orderprop",
			"caption" => t("Tellimuse omadus"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "in_table",
			"caption" => t("Tabelis"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "sortable",
			"caption" => t("Sorditav"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "default_sort",
			"caption" => t("Vaikimisi sort"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "caption",
			"caption" => t("Tulba pealkiri"),
			"align" => "center"
		));
		$t->set_default_sortby("nr");
		$bug_form = $arr["obj_inst"]->prop("bug_cfgform");
		$order_form = $arr["obj_inst"]->prop("order_cfgform");
		if($order_form && $bug_form)
		{
			switch($arr["request"]["group"])
			{
				case "closed_settings":
					$data = $arr["obj_inst"]->meta("closed_settings");
					break;
				case "solved_settings":
					$data = $arr["obj_inst"]->meta("solved_settings");
					break;
				case "devo_settings":
					$data = $arr["obj_inst"]->meta("devo_settings");
					break;
				default:
					$data = $arr["obj_inst"]->meta("task_settings");
			}
			$cff = get_instance(CL_CFGFORM);
			$o_props = array(
				"oid" => t("Id"),
				"type" => t("T&uuml;&uuml;p"),
				"created" => t("Loomise kuup&auml;ev"),
				"createdby" => t("Looja"),
				"modifiedby" => t("Muutja"),
			);
			$bug_props = $o_props;
			foreach($cff->get_cfg_proplist($bug_form) as $pn => $pd)
			{
				if($pd["caption"])
				{
					$bug_props[$pn] = $pd["caption"];
				}
			}
			$order_props = $o_props;
			foreach($cff->get_cfg_proplist($order_form) as $pn => $pd)
			{
				if($pd["caption"])
				{
					$order_props[$pn] = $pd["caption"];
				}
			}
			$end=0;
			for($i=1;$end==0;$i++)
			{
				$t->define_data(array(
					"nr" => $i,
					"order" => html::textbox(array(
						"name" => "order[".$i."]",
						"value" => $data[$i]["order"],
						"size" => 3
					)),
					"bugprop" => html::select(array(
						"name" => "bugprop[".$i."]",
						"options" => $bug_props,
						"selected" => $data[$i]["bugprop"]
					)),
					"orderprop" => html::select(array(
						"name" => "orderprop[".$i."]",
						"options" => $order_props,
						"selected" => $data[$i]["orderprop"]
					)),
					"in_table" => html::checkbox(array(
						"name" => "in_table[".$i."]",
						"value" => 1,
						"checked" => ($data[$i]["in_table"]?1:0)
					)),
					"sortable" => html::checkbox(array(
						"name" => "sortable[".$i."]",
						"value" => 1,
						"checked" => ($data[$i]["sortable"]?1:0)
					)),
					"default_sort" => html::radiobutton(array(
						"name" => "default_sort",
						"value" => $i,
						"checked" => (($data["default_sort"]==$i)?1:0)
					)),
					"caption" => html::textbox(array(
						"name" => "caption[".$i."]",
						"value" => $data[$i]["caption"],
						"size" => 15
					)),
				));
				if($i%10==0)
				{
					if(!$data[$i]["in_table"])
					{
						$end = 1;
					}
				}
			}
		}
	}
	
	function _set_table_settings_table($arr)
	{
		$data = $arr["request"];
		$end = 0;
		$savedata = array();
		for($i=1;$end==0;$i++)
		{
			$savedata[$i]["order"] = $data["order"][$i];
			$savedata[$i]["bugprop"] = $data["bugprop"][$i];
			$savedata[$i]["orderprop"] = $data["orderprop"][$i];
			$savedata[$i]["caption"] = $data["caption"][$i];
			$savedata[$i]["in_table"] = $data["in_table"][$i];
			$savedata[$i]["sortable"] = $data["sortable"][$i];
			if(!$data["bugprop"][$i+1])
			{
				$end = 1;
			}
		}
		$savedata["default_sort"] = $data["default_sort"];
		switch($arr["request"]["group"])
		{
			case "closed_settings":
				$arr["obj_inst"]->set_meta("closed_settings", $savedata);
				break;
			case "solved_settings":
				$arr["obj_inst"]->set_meta("solved_settings", $savedata);
				break;
			case "devo_settings":
				$arr["obj_inst"]->set_meta("devo_settings", $savedata);
				break;
			default:
				$arr["obj_inst"]->set_meta("task_settings", $savedata);
		}
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "table_settings_sort":
				$prop["options"] = array(
					"asc" => t("Kasvav"),
					"desc" => t("Kahanev"),
				);
				switch($arr["request"]["group"])
				{
					case "closed_settings":
						$sort_prop = "table_settings_sort_closed";
						break;
					case "solved_settings":
						$sort_prop = "table_settings_sort_solved";
						break;
					case "devo_settings":
						$sort_prop = "table_settings_sort_devo";
						break;
					default:
						$sort_prop = "table_settings_sort_task";
				}
				$prop["value"] = $arr["obj_inst"]->meta($sort_prop);
				break;
		};
		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "table_settings_sort":
				switch($arr["request"]["group"])
				{
					case "closed_settings":
						$sort_prop = "table_settings_sort_closed";
						break;
					case "solved_settings":
						$sort_prop = "table_settings_sort_solved";
						break;
					case "devo_settings":
						$sort_prop = "table_settings_sort_devo";
						break;
					default:
						$sort_prop = "table_settings_sort_task";
				}
				$arr["obj_inst"]->set_meta($sort_prop, $prop["value"]);
				$arr["obj_inst"]->save();
				return PROP_IGNORE;
		}
		return $retval;
	}	

	function callback_mod_reforb($arr)
	{
		$cur = obj($arr["id"]);
		$type_var = $cur->prop("type_var");
		if(is_oid($type_var) && $arr["group"] == "groups")
		{
			$ol = new object_list(array(
				"parent" => $type_var,
				"class_id" => array(CL_META)
			));
			foreach($ol->list as $oid)
			{
				$arr["main_user".$oid] = 0;
			}
		}
		$arr["post_ru"] = post_ru();
	}
	

	function show($arr)
	{
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");

		$this->vars(array(
			"name" => $ob->prop("name"),
		));
		return $this->parse();
	}

	function _get_devo_confirm_needed($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$data = $arr["obj_inst"]->meta("devo_settings");
		$this->_define_table_from_settings($data, $t, $arr);

		$cur_p = get_current_person();
		$cur = array($cur_p->id() => $cur_p->id());

		$co_i = get_instance(CL_CRM_COMPANY);

		// list all ppl on the same level as me and if I have highest pri, then I get to see lots more
		foreach(safe_array($cur_p->prop("org_section")) as $sect_id)
		{
			if (!$this->can("view", $sect_id))
			{
				continue;
			}
			$my_sect = obj($sect_id);
			$hi = null;
			foreach($my_sect->connections_from(array("type" => "RELTYPE_PROFESSIONS")) as $c)
			{
				$prof = $c->to();
				if ($hi == null || $prof->prop("jrk") > $hi->prop("jrk"))
				{
					$hi = $prof;
				}
			}
			if ($hi && $hi->id() == $cur_p->prop("rank"))
			{
				// I'm the boss in this section, show all ppl from it
				foreach($co_i->get_employee_picker($my_sect) as $_id => $nm)
				{
					$cur[$_id] = $_id;
				}
			}
		}

		if ($this->can("view", $arr["request"]["sct_filter"]) && count($cur))
		{
			$cur = array();
			$co_i = get_instance(CL_CRM_COMPANY);
			foreach($co_i->get_employee_picker(obj($arr["request"]["sct_filter"])) as $_id => $nm)
			{
				$cur[$_id] = $_id;
			}
		}
		$cp_ppl = array();
		if($this->can("view", $arr["request"]["sct_filter"]))
		{
			$view_sects = array($arr["request"]["sct_filter"]);
			$this->_recur_get_all_sects($view_sects, obj($arr["request"]["sct_filter"]));
		}
		elseif($arr["request"]["sct_filter"] == "all")
		{
			$view_sects = $this->get_all_sects();
		}
		foreach($view_sects as $sect)
		{
			if($this->can("view", $sect))
			{
				$sect = obj($sect);
				foreach($co_i->get_employee_picker($sect) as $_id => $nm)
				{
					$cp_ppl[$_id] = $_id;
				}
			}
		}
		$cur_u = array();
		$p = get_instance(CL_CRM_PERSON);
		foreach($cur as $c)
		{
			$po = obj($c);
			if($u = $p->has_user($po))
			{
				$uid = $u->name();
				$cur_u[] = $uid;
			}
		}
		$sects = $this->get_all_sects();
		if($arr["request"]["sct_filter"] == "all")
		{
			$filt = array(
				"bug_status" => 1,
				"class_id" => array(CL_DEVELOPMENT_ORDER),
				new object_list_filter(array(
					"logic" => "OR",
					"conditions" => array(
						new object_list_filter(array(
							"logic" => "OR",
							"conditions" => array(
								"contactperson" => $cur,
								"monitors" => $cur,
								"orderer" => $cur,
								"bug_feedback_p" => $cur,
								"monitors" => $cur,
								"orderer" => $cur,
								"bug_feedback_p" => $cur,
								"createdby" => $cur_u,
							)
						)),
						new object_list_filter(array(
							"logic" => "OR",
							"conditions" => array(
								"orderer_unit" => $sects,
								"contactperson" => $cp_ppl,
							),
						)),
					),
				)),
			);
		}
		elseif($arr["request"]["sct_filter"] && array_search($arr["request"]["sct_filter"], $sects) !== false)
		{
			$sects = array($arr["request"]["sct_filter"]);
			$this->_recur_get_all_sects($sects, obj($arr["request"]["sct_filter"]));
			$filt = array(
				"bug_status" => 1,
				"class_id" => array(CL_DEVELOPMENT_ORDER),
				new object_list_filter(array(
					"logic" => "OR",
					"conditions" => array(
						"orderer_unit" =>  $sects,
						"contactperson" => $cp_ppl,
					),
				)),
			);
		}
		else
		{
			$filt = array(
				"bug_status" => 1,
				"class_id" => array(CL_DEVELOPMENT_ORDER),
				new object_list_filter(array(
					"logic" => "OR",
					"conditions" => array(
						"createdby" => $p->has_user(get_current_person())->name(),
						"bug_createdby" => $p->has_user(get_current_person())->name(),
					),
				)),
			);
		}
		$ol = new object_list($filt);
		$this->_insert_data_to_table_from_settings($data, $t, $ol, $arr);
	}

	function _get_table_filter($arr)
	{
		$p = get_current_person();
		$sect_id = $p->prop("org_section");
		$sects = array(aw_url_change_var("sct_filter", "all", get_ru()) => t("K&otilde;ik"),
			aw_url_change_var("sct_filter", null, get_ru()) => t("Minu lisatud"),
		);
		if($sect_id)
		{
			$sects[aw_url_change_var("sct_filter", $sect_id, get_ru())] = obj($sect_id)->name();
		}
		if ($this->can("view", $sect_id))
		{
			$this->_recur_sect_list($sects, obj($sect_id));
		}
		$arr["prop"]["options"] = $sects;
		$arr["prop"]["value"] = aw_url_change_var("sct_filter", null, get_ru());
		if($arr["request"]["sct_filter"])
		{
			foreach($sects as $url => $tmp)
			{
				if(strpos($url, "sct_filter=".$arr["request"]["sct_filter"])!==false)
				{
					$arr["prop"]["value"] = $url;
				}
			}
		}
		$arr["prop"]["onchange"] = "window.location.href=this.options[this.selectedIndex].value";
	}

	function _recur_sect_list(&$data, $section)
	{
		$this->_sect_level++;
		foreach($section->connections_from(array("type" => "RELTYPE_SECTION")) as $c)
		{
			$data[aw_url_change_var("sct_filter", $c->prop("to"), get_ru())] = str_repeat("&nbsp;", ($this->_sect_level) * 4).$c->prop("to.name");
			$this->_recur_sect_list($data, $c->to());
		}
		$this->_sect_level--;
	}

	function get_all_sects()
	{
		$p = get_current_person();
		$sect_id = $p->prop("org_section");
		if ($this->can("view", $sect_id))
		{
			$sects = array($sect_id);
			$this->_recur_get_all_sects($sects, obj($sect_id));
		}
		else
		{
			$sects = array(-1);
		}
		return $sects;
	}

	function _recur_get_all_sects(&$data, $section)
	{
		foreach($section->connections_from(array("type" => "RELTYPE_SECTION")) as $c)
		{
			$data[] = $c->prop("to");
			$this->_recur_get_all_sects($data, $c->to());
		}
		return $data;
	}
}
?>
