<?php
// events_manager.aw - Kuhu minna moodul
/*

@classinfo syslog_type=ST_EVENTS_MANAGER relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=markop

@default table=objects
@default group=general

	@property name type=textbox group=settings
	@caption Nimi

@default field=meta
@default method=serialize

@groupinfo events caption="S&uuml;ndmused" submit_method=GET
@default group=events

	@property events_tb type=toolbar store=no no_caption=1
	@caption P&otilde;hitoolbar

		@layout events_top type=hbox closeable=1 width=30%:70%

		@layout events_top_left type=vbox parent=events_top closeable=1 area_caption=S&uuml;ndmuste&nbsp;Otsing
			@property e_find_sectors type=select multiple=1 parent=events_top_left store=no
			@caption Valdkonnad

			@property e_find_editor type=select multiple=1 parent=events_top_left store=no
			@caption Toimetaja

			@property e_find_text type=textbox parent=events_top_left size=20 store=no
			@caption Tekst

			@property e_find_county type=select parent=events_top_left store=no
			@caption Maakond

			@property e_find_news type=chooser no_caption=1 parent=events_top_left store=no
			@caption Arhiivist, Uute hulgast

			@property event_search_button type=submit store=no
			@caption Otsi

		@layout events_top_right type=vbox parent=events_top
			@property event_table type=table no_caption=1 parent=events_top_right store=no
			@caption S&uumlndmuste tabel

			@property per_page_str type=text no_caption=1 parent=events_top_right store=no
			@caption N&auml;ita s&uuml;ndmusi rida

@groupinfo places caption="Toimumiskohad" submit=no
@default group=places
	@property places_tb type=toolbar no_caption=1
	@caption Toimumiskohtade toolbar

	@property places_table type=table no_caption=1
	@caption Toimumiskohtade tabel

@groupinfo similar_find caption="Sarnased s&uuml;ndmused" submit=no
@default group=similar_find
	@property similar_tb type=toolbar no_caption=1
	@caption sarnaste s&uuml;ndmuste toolbar

	@property days_from_today type=select
	@caption P&auml;evi t&auml;nasest:

	@property similar_table type=table no_caption=1
	@caption Leitud sarnased s&uuml;ndmused


@groupinfo organiser caption="Korraldajad" submit=no
@default group=organiser
	@property organiser_tb type=toolbar no_caption=1
	@caption Korraldajate toolbar

	@property organiser_table type=table no_caption=1
	@caption Korraldajate tabel


@groupinfo sectors caption="Valdkonnad" submit=no
@default group=sectors
	@property sectors_tb type=toolbar no_caption=1 store=no
	@caption toolbar

	@property sectors_tree type=treeview no_caption=1 store=no
	@caption Valdkonnad


@groupinfo editors caption="Toimetajad" submit=no
@default group=editors
	@property editor_tb type=toolbar no_caption=1
	@caption Toimetajad toolbar

	@property editors_table type=table no_caption=1


@groupinfo settings caption="Seaded"
@default group=settings
	@property owner type=relpicker reltype=RELTYPE_OWNER
	@caption Omanik

	@property languages type=relpicker multiple=1 reltype=RELTYPE_LANGUAGE
	@caption Sisu keeled

	@property mapserver_url type=textbox
	@caption Kaardiserveri url

	@property similar_time type=textbox
	@caption Sarnaste s&uuml;ndmuste kattumisaeg (tundides)

	@property events_per_page type=textbox
	@caption S&uuml;ndmusi &uuml;hel lehel

	@property events_per_page type=textbox
	@caption S&uuml;ndmusi &uuml;hel lehel

	@property editors_groups type=relpicker multiple=1 reltype=RELTYPE_GROUP automatic=1
	@caption Toimetajate kasutajagrupid

	@property preview_object type=relpicker reltype=RELTYPE_DOCUMENT
	@caption Eelvaate objekt

	@property forms_caption type=text store=no subtitle=1
	@caption Vormid

		@property event_form type=relpicker reltype=RELTYPE_CFGMANAGER field=meta method=serialize table=objects
		@caption S&uuml;ndmuste seadete haldur

		@property places_form type=relpicker reltype=RELTYPE_CFGMANAGER field=meta method=serialize table=objects
		@caption Toimumiskohtade seadete haldur

		@property organiser_form type=relpicker reltype=RELTYPE_CFGMANAGER field=meta method=serialize table=objects
		@caption Korraldajate (organisatsioonid) seadete haldur

		@property organiser_person_form type=relpicker reltype=RELTYPE_CFGMANAGER field=meta method=serialize table=objects
		@caption Korraldajate (isikud) seadete haldur

		@property sector_form type=relpicker reltype=RELTYPE_CFGMANAGER field=meta method=serialize table=objects
		@caption Valdkondade seadete haldur

		@property editor_form type=relpicker reltype=RELTYPE_CFGMANAGER field=meta method=serialize table=objects
		@caption Toimetajate seadete haldur

	@layout menus_top type=hbox closeable=1 width=30%:70% area_caption=Kataloogid
		@layout menus_top_left type=vbox parent=menus_top area_caption=Kataloogid&nbsp;lugemiseks
			@property event_menu_source type=relpicker multiple=1 reltype=RELTYPE_EVENT_MENU parent=menus_top_left
			@caption S&uuml;ndmuste kataloog

			@property places_menu_source type=relpicker multiple=1 reltype=RELTYPE_PLACE_MENU parent=menus_top_left
			@caption Toimumiskohtade kataloog

			@property organiser_menu_source type=relpicker multiple=1 reltype=RELTYPE_ORGANISER_MENU parent=menus_top_left
			@caption Korraldajate kataloog

			@property sector_menu_source type=relpicker multiple=1 reltype=RELTYPE_SECTOR_MENU parent=menus_top_left
			@caption Valdkondade kataloog

		@layout menus_top_right type=vbox parent=menus_top area_caption=Kataloogid&nbsp;kirjutamiseks
			@property event_menu type=relpicker reltype=RELTYPE_EVENT_MENU parent=menus_top_right
			@caption S&uuml;ndmuste kataloog kirjutamiseks

			@property places_menu type=relpicker reltype=RELTYPE_PLACE_MENU parent=menus_top_right
			@caption Toimumiskohtade kataloog kirjutamiseks

			@property organiser_menu type=relpicker reltype=RELTYPE_ORGANISER_MENU parent=menus_top_right
			@caption Korraldajate kataloog kirjutamiseks

			@property sector_menu type=relpicker reltype=RELTYPE_SECTOR_MENU parent=menus_top_right
			@caption Valdkondade kataloog kirjutamiseks



########## RELTYPES ###########

@reltype SECTOR value=1 clid=CL_CRM_SECTOR
@caption Valdkond

@reltype EVENT_MENU value=2 clid=CL_MENU
@caption S&uuml;ndmuste kataloog

@reltype PLACE_MENU value=3 clid=CL_MENU
@caption Toimumiskohtade kataloog

@reltype ORGANISER_MENU value=4 clid=CL_MENU
@caption Korraldajate kataloog

@reltype SECTOR_MENU value=5 clid=CL_MENU
@caption Valdkondade kataloog

@reltype OWNER value=6 clid=CL_CRM_COMPANY
@caption Omanik

@reltype LANGUAGE value=7 clid=CL_LANGUAGE
@caption Keel

@reltype EDITOR value=9 clid=CL_CRM_PERSON
@caption Toimetaja

@reltype CFGMANAGER value=10 clid=CL_CFGMANAGER
@caption Seadete haldur

@reltype GROUP value=11 clid=CL_GROUP
@caption Kasutajagrupp

@reltype DOCUMENT value=12 clid=CL_DOCUMENT
@caption Dokument
*/

class events_manager extends class_base
{
	const INDENT_STRING = "&nbsp;&nbsp;";
	public $type_idx = array(
		"event" => CL_CALENDAR_EVENT,
		"organiser" => CL_CRM_COMPANY,
		"organiser_person" => CL_CRM_PERSON,
		"editor" => CL_CRM_PERSON,
		"sector" => CL_CRM_SECTOR,
		"places" => CL_SCM_LOCATION
	);

	private $sectors_tree_array = array();

	function events_manager()
	{
		$this->init(array(
			"tpldir" => "applications/events_manager/events_manager",
			"clid" => CL_EVENTS_MANAGER
		));
	}

	function get_property($arr)
	{
		$this_o = $arr["obj_inst"];
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
			case "per_page_str":return PROP_IGNORE;
				if(!($GLOBALS["event_result_count"] > $this_o->prop("events_per_page")))
				{
					return PROP_IGNORE;
				}
				$x = 0;
				while($x < $GLOBALS["event_result_count"])
				{
					$str = " ";
					$str.= $x;
					$str.= " - ";
					$str.= ($x + $this_o->prop("events_per_page") > $GLOBALS["event_result_count"]) ? $GLOBALS["event_result_count"]:$x + $this_o->prop("events_per_page");
					if($_GET["ev_page_start"] != $x)
					{
						$prop["value"].= html::href(array(
							"url" => aw_url_change_var("ev_page_start", $x),
							"caption" => $str,
						));
					}
					else
					{
						$prop["value"].= $str;
					}
					$x = $x + $this_o->prop("events_per_page");
				}
				break;
			case "events_tb":
				$this->_get_events_tb($arr);
				break;

			case "e_find_text":
				$prop["value"] = $arr["request"][$prop["name"]];
				break;

			case "e_find_sectors":
				$prop["value"] = $arr["request"][$prop["name"]];

				if ($this->can("view", $this_o->prop("sector_menu")))
				{
					$parent = $this_o->prop("sector_menu");
					$sectors = new object_tree(array(
						"parent" => $parent,
						"lang_id" => array()
					));

					if (count($sectors->ids()))
					{
						$this->add_sectors_to_tree_array($sectors, $parent);
					}
				}

				foreach ($this_o->prop("sector_menu_source") as $parent)
				{
					if ($this->can("view", $parent))
					{
						$sectors = new object_tree(array(
							"parent" => $parent,
						));

						if (count($sectors->ids()))
						{
							$this->add_sectors_to_tree_array($sectors, $parent);
						}
					}
				}

				foreach ($this->sectors_tree_array as $sector_data)
				{
					$sector = $sector_data["sector"];
					$level = $sector_data["level"];

					if (CL_CRM_SECTOR === ((int) $sector->class_id()) and $sector->id() !== $this_o->id())
					{
						$indent = str_repeat(self::INDENT_STRING, $level);
						$prop["options"][$sector->id()] = $indent . $sector->prop("tegevusala");
					}
				}
				break;

			case "e_find_county":
				$ol = new object_list(array(
					"class_id" => CL_CRM_COUNTY,
					"lang_id" => array(),
					"site_id" => array()
				));
				$prop["value"] = $arr["request"][$prop["name"]];
				$prop["options"] = array("" => "") + $ol->names();
				break;
			case "e_find_editor":
				$prop["value"] = $arr["request"][$prop["name"]];
				$groups_list = new object_list(array(
					"class_id" => CL_GROUP,
					"lang_id" => array(),
					"site_id" => array(),
					"type" => new obj_predicate_not(1)
				));
				$editors_list = new object_list($this_o->connections_from(array("type" => "RELTYPE_EDITOR")));
				$prop["options"] = $groups_list->names() + $editors_list->names();
				break;

			case "e_find_news":
				$prop["value"] = $arr["request"][$prop["name"]];
				$prop["options"] = array(
					"arch" => t("Arhiivist"),
//					"curr" => t("Kestvatest"),
					"new" => t("Aktiivsetest")
				);
				break;

			case "event_table":
				$this->_get_event_table($arr);
				break;

			case "places_table":
				$this->_get_places_table($arr);
				break;

			case "days_from_today":
				if($_SESSION["events_manager"]["dft"])
				{
					$prop["options"]["value"] = $_SESSION["events_manager"]["dft"];
				}
				$prop["options"] = array(1=> 1,7=> 7,15=> 15,30=> 30,60=> 60,90=> 90,180=> 180,365=> 365);
				$prop["onchange"] = "javascript:document.changeform.submit();";
				break;
		}

		return $retval;
	}

	private function add_sectors_to_tree_array($tree, $parent, $level_count = 0)
	{
		$level = $tree->level($parent);

		foreach ($level as $o)
		{
			if ($o->parent() === $parent)
			{
				$this->sectors_tree_array[] = array(
					"sector" => $o,
					"level" => $level_count
				);
			}

			$this->add_sectors_to_tree_array($tree, $o->id(), ($level_count + 1));
		}
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
			case "days_from_today":
				$_SESSION["events_manager"]["dft"] = $arr["request"]["days_from_today"];
				break;
		}

		return $retval;
	}

	function _get_places_table($arr)
	{
		if(!$this->can("add", $arr["obj_inst"]->prop("places_menu")))
		{
			$arr["prop"]["error"] = t("Toimumiskohtade kataloog m&auml;&auml;ramata v&otilde;i puudub ligip&auml;&auml;s.");
			return PROP_ERROR;
		}

		$filter = array(
			"class_id" => array(CL_SCM_LOCATION),
			"parent" => $arr["obj_inst"]->prop("places_menu"),
			"lang_id" => array(),
			"oid" => $this->translated_objs(CL_SCM_LOCATION),
		);

		// to editors show only their own objects
		$groups = aw_global_get("gidlist_oid");
		$editors_groups = $arr["obj_inst"]->prop("editors_groups");

		if (count(array_intersect($groups, $editors_groups)))
		{
			$filter["createdby"] = aw_global_get("uid");
		}

		$ol = new object_list($filter);
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_places_table($t);

		$cfg = $this->get_cgf_from_manager($arr["obj_inst"], "places");

		foreach($ol->arr() as $o)
		{
			$t->define_data(array(
				"name" => (!$this->can("edit" , $o->id())) ? parse_obj_name($o->trans_get_val("name")) :
					 html::get_change_url($o->id(), array("cfgform" => $cfg, "return_url" => get_ru()) + (aw_global_get("section") ? array("section" => aw_global_get("section")) : array()), (parse_obj_name($o->trans_get_val("name")) ? parse_obj_name($o->trans_get_val("name")) : "(".t("Nimetu").")")),
				"name_str" => parse_obj_name($o->trans_get_val("name")),
				"comment" => $o->trans_get_val("comment"),
				"oid" => $o->id(),
			));
		}
		$t->set_default_sortby(array("name_str"));
		$t->sort_by();
	}

	function get_cgf_from_manager($this_o, $type)
	{
		$cfgform = null;

		if (array_key_exists($type, $this->type_idx))
		{
			$clid = $this->type_idx[$type];

			if(is_oid($this_o->prop($type."_form")))
			{
				$cfg_loader = new object($this_o->prop($type."_form"));
				$mxt = $cfg_loader->meta("use_form");
				$forms = $mxt[$clid];
				$gx = aw_global_get("gidlist_pri_oid");
				$cfgform = false;
				if (is_array($gx) && is_array($forms))
				{
					// start from group with highest priority
					arsort($gx);
					foreach($gx as $grp_oid => $grp_pri)
					{
						if ($forms[$grp_oid] && empty($cfgform))
						{
							$cfgform = $forms[$grp_oid];
						}
					}
				}
			}
		}

		return $cfgform;
	}

	function _get_organiser_table($arr)
	{
		if(!$this->can("add", $arr["obj_inst"]->prop("organiser_menu")))
		{
			$arr["prop"]["error"] = t("Korraldajate kataloog m&auml;&auml;ramata v&otilde;i puudub ligip&auml;&auml;s.");
			return PROP_ERROR;
		}

		$ids = $this->translated_objs(CL_CRM_COMPANY);
		foreach($this->translated_objs(CL_CRM_PERSON) as $id)
		{
			$ids[] = $id;
		}

		$filter = array(
			"class_id" => array(CL_CRM_COMPANY, CL_CRM_PERSON),
			"parent" => $arr["obj_inst"]->prop("organiser_menu"),
			"lang_id" => array(),
			"oid" => $ids,
		);

		// to editors show only their own objects
		$groups = aw_global_get("gidlist_oid");
		$editors_groups = $arr["obj_inst"]->prop("editors_groups");

		if (count(array_intersect($groups, $editors_groups)))
		{
			$filter["createdby"] = aw_global_get("uid");
		}

		$ol = new object_list($filter);
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_organiser_table($t);
		$cfg_o = $this->get_cgf_from_manager($arr["obj_inst"], "organiser");
		$cfg_p = $this->get_cgf_from_manager($arr["obj_inst"], "organiser_person");

		foreach($ol->arr() as $o)
		{
			$cfg = (CL_CRM_COMPANY === (int) $o->class_id()) ? $cfg_o : $cfg_p;
			$t->define_data(array(
				"name" => (!$this->can("edit" , $o->id()))?parse_obj_name($o->trans_get_val("name")):
					 html::get_change_url($o->id(), array("cfgform" => $cfg, "return_url" => get_ru()) + (aw_global_get("section") ? array("section" => aw_global_get("section")) : array()), (parse_obj_name($o->trans_get_val("name")))),
//				"address" => $o->prop("contact.name"),
				"address" => $this->can("view", $o->prop("contact")) ? parse_obj_name(obj($o->prop("contact"))->trans_get_val("name")) : "",
				"oid" => $o->id(),
				"name_str" => $o->name(),
			));
		}
		$t->set_default_sortby(array("name_str"));
		$t->sort_by();
	}

	function _get_sectors_tree($arr)
	{
		if(!$this->can("add", $arr["obj_inst"]->prop("sector_menu")))
		{
			$arr["prop"]["error"] = t("Valdkondade kataloog m&auml;&auml;ramata v&otilde;i puudub ligip&auml;&auml;s.");
			return PROP_ERROR;
		}

		$parent = new object($arr["obj_inst"]->prop("sector_menu"));
		$ot = new object_tree(array(
			"class_id" => CL_CRM_SECTOR,
			"parent" => $parent,
			"lang_id" => array(),
			"oid" => $this->translated_objs(CL_CRM_SECTOR),
		));
		$cfg = $this->get_cgf_from_manager($arr["obj_inst"], "sector");
		$target_url = $this->mk_my_orb("change", array("cfgform" => $cfg, "return_url" => get_ru()) + (aw_global_get("section") ? array("section" => aw_global_get("section")) : array()), "crm_sector");

		$arr["prop"]["vcl_inst"] = treeview::tree_from_objects(array(
			"tree_opts" => array(
				"type" => TREE_DHTML_WITH_CHECKBOXES,
				"tree_id" => "sectorstree",
				"checkbox_data_var" => "evmgr_objsel",
				"persist_state" => true,
			),
			"root_item" => $parent,
			"target_url" => $target_url,
			"no_root_item" => true,
			"item_name_props" => array(
				CL_CRM_SECTOR => "tegevusala",
			),
			"ot" => $ot,
			"var" => "evmgr_sectortree_sel",
			"node_actions" => array (
				CL_CRM_SECTOR => "change",
			)
		));
		$t =& $arr["prop"]["vcl_inst"];
	}

	function _get_editors_table($arr)
	{
		$u = get_instance(CL_USER);
		$ol = new object_list(array(
			"site_id" => array(),
			"lang_id" => array(),
			"class_id" => CL_CRM_PERSON,
			"parent" => $arr["obj_inst"]->id(),
		));
		foreach(
			$arr["obj_inst"]->connections_from(array(
				"type" => "RELTYPE_EDITOR"
			))
			as $c)
		{
			$ol->add($c->to());
		}

		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_editors_table($t);
		foreach($ol->arr() as $o)
		{
			$gro = $c = $org = $org_c = null;
//			$cons = $o->connections_from(array(
//				"type" => "RELTYPE_WORK"
//			));
//			$org_c = reset($cons);
//			if($org_c) $org = $org_c->prop("to");
			
			$org = $o->company_id();

			if($this->can("view" , $org))
			{
				$co = obj($org);
				$c = $this->can("edit" , $co->id()) ? html::obj_change_url($co) :  $co->name();
			}

			$user_list = new object_list(array("class_id" => CL_USER, "lang_id" => array(), "CL_USER.RELTYPE_PERSON.id"=>$o->id()));
			if(sizeof($user_list->arr()))
			{
				$user = reset($user_list->arr());
			}
			if(is_object($user))
			{
				 $gro = $u->get_highest_pri_grp_for_user($user->prop("uid"));
			}
			$t->define_data(array(
				"name" => (!$this->can("edit" , $o->id())) ? $o->name() : html::obj_change_url($o->id()),
				"user" => is_object($user)?$user->prop("uid"):"",
				"oid" => $o->id(),
				"group" => is_object($gro)?$gro->name():"",
				"company" => $c,
			));
		}
	}


	function _get_places_tb($arr)
	{
		$this_o = $arr["obj_inst"];
		$cfg = $this->get_cgf_from_manager($arr["obj_inst"], "places");
		$arr["prop"]["vcl_inst"]->add_button(array(
			"name" => "new",
			"img" => "new.gif",
			"disabled" => !$this->can("add", $this_o->prop("places_menu")),
			"tooltip" => t("Lisa uus"),
			"url" => $this->mk_my_orb("new", array(
				"parent" => $this_o->prop("places_menu"),
				"cfgform" => $cfg,
				"section" => $section,
				"return_url" => get_ru()
			) + (aw_global_get("section") ? array("section" => aw_global_get("section")) : array()), "scm_location")
		));
		$arr["prop"]["vcl_inst"]->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"tooltip" => t("Kustuta m&auml;rgistatud s&uuml;ndmused"),
			"action" => "delete",
			"confirm" => t("Olete kindel, et soovite kustudada k&otilde;ik valitud toimumiskohad?"),
		));
	}

	function _get_sectors_tb($arr)
	{
		$this_o = $arr["obj_inst"];
		$cfg = $this->get_cgf_from_manager($arr["obj_inst"], "sector");
		$arr["prop"]["vcl_inst"]->add_button(array(
			"name" => "new",
			"img" => "new.gif",
			"disabled" => !$this->can("add", $this_o->prop("sector_menu")),
			"tooltip" => t("Lisa uus"),
			"url" => $this->mk_my_orb("new", array(
				"parent" => $this_o->prop("sector_menu"),
				"cfgform" => $cfg,
				"return_url" => get_ru()
			) + (aw_global_get("section") ? array("section" => aw_global_get("section")) : array()), "crm_sector")
		));
		$arr["prop"]["vcl_inst"]->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"tooltip" => t("Kustuta m&auml;rgistatud valdkonnad"),
			"action" => "delete",
			"confirm" => t("Olete kindel, et soovite kustudada k&otilde;ik valitud valdkonnad?"),
		));
	}

	function _get_organiser_tb($arr)
	{
		$this_o = $arr["obj_inst"];
		$arr["prop"]["vcl_inst"]->add_menu_button(array(
			"name" => "add",
			"img" => "new.gif",
			"tooltip" => t("Lisa uus"),
		));

		$cfg = $this->get_cgf_from_manager($arr["obj_inst"], "organiser");
		$arr["prop"]["vcl_inst"]->add_menu_item(array(
			"parent" => "add",
			"text" => t("Korraldaja (organisatsioon)"),
			"disabled" => !$this->can("add", $this_o->prop("organiser_menu")),
			"link" => $this->mk_my_orb("new", array(
				"parent" => $this_o->prop("organiser_menu"),
				"cfgform" => $cfg,
				"return_url" => get_ru()
			) + (aw_global_get("section") ? array("section" => aw_global_get("section")) : array()), "crm_company")
		));

		$cfg = $this->get_cgf_from_manager($arr["obj_inst"], "organiser_person");
		$arr["prop"]["vcl_inst"]->add_menu_item(array(
			"parent" => "add",
			"text" => t("Korraldaja (isik)"),
			"disabled" => !$this->can("add", $this_o->prop("organiser_menu")),
			"link" => $this->mk_my_orb("new", array(
				"parent" => $this_o->prop("organiser_menu"),
				"cfgform" => $cfg,
				"return_url" => get_ru()
			) + (aw_global_get("section") ? array("section" => aw_global_get("section")) : array()), "crm_person")
		));

		$arr["prop"]["vcl_inst"]->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"tooltip" => t("Kustuta m&auml;rgistatud korraldajad"),
			"action" => "delete",
			"confirm" => t("Olete kindel, et soovite kustudada k&otilde;ik valitud korraldajad?"),
		));
	}

	function _get_editor_tb($arr)
	{
		$arr["prop"]["vcl_inst"]->add_button(array(
			"name" => "new",
			"img" => "new.gif",
			"tooltip" => t("Lisa uus"),
			"action" => "add_new_editor",
		));
		$arr["prop"]["vcl_inst"]->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"tooltip" => t("Kustuta m&auml;rgistatud toimetajad"),
			"action" => "delete",
			"confirm" => t("Olete kindel, et soovite kustudada k&otilde;ik valitud toimetajad?"),
		));

		$url = $this->mk_my_orb("do_search", array(
			"pn" => "editor",
			"id" => $arr["obj_inst"]->id(),
			"multiple" => 1,
			"clid" => CL_CRM_PERSON,
			"section" => $arr["request"]["section"],
		), "popup_search");

		$arr["prop"]["vcl_inst"]->add_button(array(
			"tooltip" => t("Otsi"),
			"name" => "search",
			"img" => "search.gif",
			"url" => "javascript:aw_popup_scroll('$url','".t("Otsi")."',550,500)"
		));
	}

	function &_get_event_list($this_o, &$args)
	{
/*		aw_disable_acl();
		$x = 0;
		set_time_limit(1800);
		while($x < 50000)
		{
			$fi = array("class_id" => CL_CALENDAR_EVENT, "limit" => $x.",100");
			$ces = new object_list($fi);
			foreach($ces-> arr() as $ce)
			{
				$ce->set_start_end();unset($ce);
			}
			$x = $x+100;

			unset($ces);
			arr($x);
			flush();
		}
aw_restore_acl();
*/
		$time = time();
		$filter = array(
			"class_id" => array(CL_CALENDAR_EVENT),
			"parent" => array($this_o->prop("event_menu")) + (array) $this_o->prop("event_menu_source"),
//			"end" => new obj_predicate_compare(OBJ_COMP_GREATER, $time)
			"lang_id" => array(),
			"oid" => $this->translated_objs(CL_CALENDAR_EVENT),
		);

		$filter2 = array(
			"class_id" => array(CL_EVENT_TIME),
//			"event.parent" => array($this_o->prop("event_menu")) + (array) $this_o->prop("event_menu_source"),
		);

		$groups = aw_global_get("gidlist_oid");
		$editors_groups = $this_o->prop("editors_groups");

		if(!empty($args["e_find_sectors"]))
		{
			foreach ((array) $args["e_find_sectors"] as $oid)
			{
				$filter["CL_CALENDAR_EVENT.RELTYPE_SECTOR.id"][] = (int) $oid;
//				$filter2["CL_EVENT_TIME.event.CL_CALENDAR_EVENT.RELTYPE_SECTOR.id"][] = (int) $oid;
			}
		}

		if (count(array_intersect($groups, $editors_groups)))
		{
			$filter["createdby"] = aw_global_get("uid");
//			$filter2["CL_EVENT_TIME.event.createdby"] = aw_global_get("uid");
		}
		elseif(!empty($args["e_find_editor"]))
		{
			foreach ((array) $args["e_find_editor"] as $uid)
			{
				$filter["createdby"][] = (string) $uid;
//				$filter2["CL_EVENT_TIME.event.createdby"][] = (string) $uid;
			}
		}

//		if(!empty($args["sort_by_created"]))
//		{

//		}

		if(!empty($args["archived"]))
		{
			$args["e_find_news"] = "arch";
		}

		if(!empty($args["e_find_text"]))
		{
			$filter[] = new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					"name" => "%".$args["e_find_text"]."%",
					"title" => "%".$args["e_find_text"]."%",
					"description" => "%".$args["e_find_text"]."%",
				)
			));
/*			$filter2[] = new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					"CL_EVENT_TIME.event.name" => "%".$args["e_find_text"]."%",
					"CL_EVENT_TIME.event.title" => "%".$args["e_find_text"]."%",
					"CL_EVENT_TIME.event.description" => "%".$args["e_find_text"]."%",
				)
			));
*/		}

		if(!empty($args["e_find_county"]))
		{
			$filter["CL_CALENDAR_EVENT.RELTYPE_LOCATION.address.maakond"] = $args["e_find_county"];
//			$filter2["CL_EVENT_TIME.event.CL_CALENDAR_EVENT.RELTYPE_LOCATION.address.maakond"] = $args["e_find_county"];
		}

		switch ($args["e_find_news"])
		{
			case "arch":
				//$filter["end"] = new obj_predicate_compare(OBJ_COMP_LESS, time());
/*				$filter[] = new object_list_filter(array(
					"logic" => "OR",
					"conditions" => array(
						"end" => new obj_predicate_compare(OBJ_COMP_LESS, time()),
						"CL_CALENDAR_EVENT.RELTYPE_EVENT_TIME.end" => new obj_predicate_compare(OBJ_COMP_LESS, time()),
					)
				));*/
				$filter["end"] =  new obj_predicate_compare(OBJ_COMP_LESS, time());
//				$filter["CL_CALENDAR_EVENT.RELTYPE_EVENT_TIME.end"] =  new obj_predicate_compare(OBJ_COMP_LESS, time());
//				$filter2["end"] = new obj_predicate_compare(OBJ_COMP_LESS, time());
				break;
			case "curr":
/*				$filter[] = new object_list_filter(array(
					"logic" => "AND",
					"conditions" => array(
//						"start1" => new obj_predicate_compare(OBJ_COMP_LESS, time()),
//						"end" => new obj_predicate_compare(OBJ_COMP_GREATER, time()),
						new object_list_filter(array(
							"logic" => "OR",
							"conditions" => array(
								"start1" => new obj_predicate_compare(OBJ_COMP_LESS, time()),
								"CL_CALENDAR_EVENT.RELTYPE_EVENT_TIME.start" => new obj_predicate_compare(OBJ_COMP_LESS, time()),
							)
						)),
						new object_list_filter(array(
							"logic" => "OR",
							"conditions" => array(
								"end" => new obj_predicate_compare(OBJ_COMP_GREATER, time()),
								"CL_CALENDAR_EVENT.RELTYPE_EVENT_TIME.end" => new obj_predicate_compare(OBJ_COMP_GREATER, time()),
							)
						))
					)
				));
				$filter[] = new object_list_filter(array(
					"logic" => "AND",
					"conditions" => array(
							"CL_CALENDAR_EVENT.RELTYPE_EVENT_TIME.start" => new obj_predicate_compare(OBJ_COMP_LESS, time()),
							"CL_CALENDAR_EVENT.RELTYPE_EVENT_TIME.end" => new obj_predicate_compare(OBJ_COMP_GREATER, time()),
						)
					));
				$filter2[] = new object_list_filter(array(
					"logic" => "AND",
					"conditions" => array(
						"start" => new obj_predicate_compare(OBJ_COMP_LESS, time()),
						"end" => new obj_predicate_compare(OBJ_COMP_GREATER, time()),
					)
				));*/

				$filter[] = new object_list_filter(array(
					"logic" => "AND",
					"conditions" => array(
							"start1" => new obj_predicate_compare(OBJ_COMP_LESS, time()),
							"end" => new obj_predicate_compare(OBJ_COMP_GREATER, time()),
						)
					));

				break;
			case "new":
				//$filter["start1"] = new obj_predicate_compare(OBJ_COMP_GREATER, time());
/*				$filter[] = new object_list_filter(array(
					"logic" => "OR",
					"conditions" => array(
						"start1" => new obj_predicate_compare(OBJ_COMP_GREATER, time()),
						"CL_CALENDAR_EVENT.RELTYPE_EVENT_TIME.start" => new obj_predicate_compare(OBJ_COMP_GREATER, time()),
					)
				));*/
//				$filter["CL_CALENDAR_EVENT.RELTYPE_EVENT_TIME.start"] = new obj_predicate_compare(OBJ_COMP_GREATER, time());
//				$filter2["start"] = new obj_predicate_compare(OBJ_COMP_GREATER, time());
				$filter["start1"] = new obj_predicate_compare(OBJ_COMP_GREATER, time());
				break;
			default ://see annab uued ja kestvad, juhul kui vastavat nuppu vajutada
/*				$filter[] = new object_list_filter(array(
					"logic" => "OR",
					"conditions" => array(
						"end" => new obj_predicate_compare(OBJ_COMP_GREATER, time()),
						"CL_CALENDAR_EVENT.RELTYPE_EVENT_TIME.end" => new obj_predicate_compare(OBJ_COMP_GREATER, time()),
					)
				));*/
/*				$filter[] = new object_list_filter(array(
					"logic" => "OR",
					"conditions" => array(
						"end" => new obj_predicate_compare(OBJ_COMP_GREATER, time()),
						"CL_CALENDAR_EVENT.RELTYPE_EVENT_TIME.end" => new obj_predicate_compare(OBJ_COMP_GREATER, time()),
					)
				));
				$filter2["end"] = new obj_predicate_compare(OBJ_COMP_GREATER, time());
*/
				$filter["end"] = new obj_predicate_compare(OBJ_COMP_GREATER, time());
//
		}



/*			$filter2["sort_by"] = "start ".(($_GET["sort_order"] == "asc") ? "ASC" : "DESC");
			$filter2["limit"] = ($_GET["ft_page"]? ($_GET["ft_page"] * $this_o->prop("events_per_page").",") : "").$this_o->prop("events_per_page");
			$ol = new object_list();
			$filter["oid"] = array();
			$t = new object_data_list(
				$filter2,
				array(
					CL_EVENT_TIME =>  array(new obj_sql_func(OBJ_SQL_UNIQUE, "event", "event"))
				)
			);
			foreach($t->arr() as $data)
			{
				$filter["oid"][] = $data["event"];
			}

			//koguarvu jaoks
			unset($filter2["limit"]);
			$t = new object_data_list(
				$filter2,
				array(
					CL_EVENT_TIME =>  array(new obj_sql_func(OBJ_SQL_UNIQUE, "event", "event"))
				)
			);
			$this->total_events = count($t->arr());


			$ol -> add($filter["oid"]);
			return $ol;
		}
*/


//		if($_GET["sortby"] == "time")
//		{
//			$filter["sort_by"] = "aw_event_time.start ".(($_GET["sort_order"] == "asc") ? "ASC" : "DESC");
//		}
//$GLOBALS["DUKE"] = 1;
		//koguarvu jaoks
		$t = new object_data_list(
			$filter,
			array(
				CL_CALENDAR_EVENT =>  array(new obj_sql_func(OBJ_SQL_COUNT,"cnt" , "*"))
			)
		);
		$count = reset($t->arr());
		$this->total_events = $count["cnt"];

		$filter["sort_by"] = "oid DESC";
		if($args["sortby"] == "time")
		{
			$filter["sort_by"] = "start ".(($_GET["sort_order"] == "asc") ? "ASC" : "DESC");
		}
		if(!isset($_GET["ft_page"]))
		{
			$filter["limit"] = "0,".((int)$this_o->prop("events_per_page"));
		}
		else
		{
			$filter["limit"] = ($_GET["ft_page"]? ($_GET["ft_page"] * $this_o->prop("events_per_page").",") : "").((int)$this_o->prop("events_per_page"));
		}
		return new object_list($filter);
	}

	function _get_event_table($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_event_table($t,$arr);
		$cal_event = get_instance(CL_CALENDAR_EVENT);
		$ol =& $this->_get_event_list($arr["obj_inst"], $arr["request"]);
		$max_events = $arr["obj_inst"]->prop("events_per_page");

		$t->define_pageselector(array(
			"type" => "lbtxt",
			"records_per_page" => $max_events,
			"d_row_cnt" => $this->total_events,
		));
		$pageselector = $t->draw_lb_pageselector(array(
			"records_per_page" => $max_events
		));

		$post_ru = post_ru();
		$get_ru = get_ru();
		$t_publish = t("Avalda");
		$t_mk_copy = t("Tee koopia");
		$t_change = t("Muuda");
		$cfg = $this->get_cgf_from_manager($arr["obj_inst"], "event");
		$ol_count = $GLOBALS["event_result_count"] = $ol->count();


		if($_GET["sort_by"] == "time")
		{
			$ol->sort_by_cb(array($this, "__sorter"));
		}

		$cnt = 0;
		foreach($ol->ids() as $oid)
		{
	/*		if($ol_count > $max_events)
			{
				if(isset($_GET["ft_page"]) && $_GET["ft_page"]*$max_events > $cnt)
				{
					$cnt++;
					continue;
				}
				if(isset($_GET["ft_page"]) && ($_GET["ft_page"]*$max_events + $max_events) < $cnt)
				{
					break;
				}
				elseif(!$_GET["ft_page"] && $cnt > $max_events)
				{
					break;
				}
			}*/
			$o = obj($oid);
			$sec = $o->get_first_obj_by_reltype("RELTYPE_SECTOR");
			$can_edit = $this->can("edit" , $oid);

			$publish = "";
			if(!$o->prop("published"))
			{
				$publish = html::href(array(
					"url" => $this->mk_my_orb("publish" , array("id" => $oid, "post_ru" => $post_ru)),
					"title" => $t_publish,
					"caption" => $t_publish,
				));
				$col = "#CCCCCC";
			}
			else
			{
				$col = "#99CCCC";
			}

			$make_copy = html::href(array(
				"url" => $this->mk_my_orb("make_copy" , array("id" => $oid, "post_ru" => $post_ru)),
				"title" => $t_mk_copy,
				"caption" => $t_mk_copy,
			));

			$translated = "";

			if(is_array($o->meta("translations")))
			{
				$langs = array();

				foreach($o->meta("translations") as $lang => $trans)
				{
					$langs[$lang] = $GLOBALS["cfg"]["languages"]["list"][$lang]["name"];
					foreach($trans as $prop => $val)
					{
						if(!$val) unset($langs[$lang]);
						break;
					}
				}

				$translated = join (", " ,$langs);
			}

			// $name = parse_obj_name($o->name());
			$name = parse_obj_name($o->trans_get_val("name"));

			if($arr["obj_inst"]-> prop("preview_object"))
			{
				$parse_url = aw_ini_get("baseurl")."/".$arr["obj_inst"]-> prop("preview_object")."?evt_id=".$oid;
			}

			$change = html::get_change_url($oid, array("cfgform" => $cfg, "return_url" => $get_ru) + (aw_global_get("section") ? array("section" => aw_global_get("section")) : array()), $t_change);


//aeg naitamiseks, koigepealt propidest, siis objektidest
			$eventtime = "";
//			$eventtimes = array();
			$eventstart = 100000000000;
			$eventend = 1;
			if(($o->prop("start1") || $o->prop("end")))
			{
//				$eventtime = date("d.m.Y" , $o->prop("start1")). "-" .date("d.m.Y" , $o->prop("end"));
				if($o->prop("start1") > 1000 && ($eventstart >  $o->prop("start1")))
				{
					$eventstart = $o->prop("start1");
				}
				if($eventend <  $o->prop("end")) $eventend = $o->prop("end");
			}
			foreach($o->connections_from(array("type" => "RELTYPE_EVENT_TIME")) as $c)
			{
				$tm = $c->to();
				if($tm->prop("start") > 1000 && ($eventstart >  $tm->prop("start"))) $eventstart = $tm->prop("start");
				if($eventend <  $tm->prop("end")) $eventend = $tm->prop("end");
//				if($tm->prop("start") || $tm->prop("end"))
//				{
//					$eventtimes[]= date("d.m.Y" , $tm->prop("start")). "-" .date("d.m.Y" , $tm->prop("end"));
//				}
			}
			$eventtime = date("d.m.Y" , $eventstart). "-" .date("d.m.Y" , $eventend);
//			$eventtime = join ("<br>" , $eventtimes);

			$t->define_data(array(
				"name" => ($parse_url)? html::href(array("caption" => $name, "url" => $parse_url)):($can_edit ? html::get_change_url($oid, array("cfgform" => $cfg, "return_url" => $get_ru) + (aw_global_get("section") ? array("section" => aw_global_get("section")) : array()), $name) : $name),
				"time" => $eventstart.$eventend,
				"time_val" => $eventtime,
				"sector" => (is_object($sec)) ? $sec->trans_get_val("name") : "",
				"level" => $cal_event->level_options[$o->prop("level")],
				"tasks" => $make_copy . " " . $publish . " " . $change,
				"oid" => $oid,
				"region" => $o->prop("location.address.maakond.name") ." ".$o->prop("location.address.linn.name"),
				"translated" => $translated,
				"col" => $col,
			));
			$cnt++;
		}
		$t->set_sortable(false);
	}

	function __sorter($a , $b)
	{
			$eventstart = 100000000000;
			$eventend = 1;
			$o = $a;
			if(($o->prop("start1") || $o->prop("end")))
			{
				if($o->prop("start1") > 1000 && ($eventstart >  $o->prop("start1")))
				{
					$eventstart = $o->prop("start1");
				}
				if($eventend <  $o->prop("end")) $eventend = $o->prop("end");
			}
			foreach($o->connections_from(array("type" => "RELTYPE_EVENT_TIME")) as $c)
			{
				$tm = $c->to();
				if($tm->prop("start") > 1000 && ($eventstart >  $tm->prop("start"))) $eventstart = $tm->prop("start");
				if($eventend <  $tm->prop("end")) $eventend = $tm->prop("end");
			}
			$a_time = $eventstart.$eventend;
			$eventstart = 100000000000;
			$eventend = 1;
			$o = $b;
			if(($o->prop("start1") || $o->prop("end")))
			{
				if($o->prop("start1") > 1000 && ($eventstart >  $o->prop("start1")))
				{
					$eventstart = $o->prop("start1");
				}
				if($eventend <  $o->prop("end")) $eventend = $o->prop("end");
			}
			foreach($o->connections_from(array("type" => "RELTYPE_EVENT_TIME")) as $c)
			{
				$tm = $c->to();
				if($tm->prop("start") > 1000 && ($eventstart >  $tm->prop("start"))) $eventstart = $tm->prop("start");
				if($eventend <  $tm->prop("end")) $eventend = $tm->prop("end");
			}
			$b_time = $eventstart.$eventend;
			if($_GET["sort_order"] == "desc")
			{
				return $a_time - $b_time;
			}
			return $b_time - $a_time;
	}

	function _get_similar_table($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_event_table($t,$arr);
		$cal_event = get_instance(CL_CALENDAR_EVENT);
		$arr["dft"] = ($_SESSION["events_manager"]["dft"]) ? $_SESSION["events_manager"]["dft"] : 1;
		$ol = $this->_get_similar_event_list($arr);

		$t->set_sortable(false);
		foreach($ol->arr() as $o)
		{
			$sec = $o->get_first_obj_by_reltype("RELTYPE_SECTOR");
			$publish = html::href(array("url" => "#" , "title" => t("Avalda") , "caption" => t("Avalda")));
			$change_url = html::obj_change_url($o , t("Muuda"));

			$t->define_data(array(
				"name" => (!$this->can("edit" , $o->id()))?parse_obj_name($o->trans_get_val("name")):html::obj_change_url($o->id(), parse_obj_name($o->trans_get_val("name"))),
				"time_val" => date("d.m.Y" , $o->prop("start1")). "-" .date("d.m.Y" , $o->prop("end")),
				"sector" => (is_object($sec))?$sec->trans_get_val("name"):"",
				"level" => $cal_event->level_options[$o->prop("level")],
				"oid" => $o->id(),
				"time" => $eventstart.$eventend,
			));
		}
	}

	function _get_similar_event_list($arr)
	{
		$days = $arr["dft"];
		enter_function("events::sql");
		$q = "
			SELECT
			objects.oid as oid,
			objects.name as name,
			objects.parent as parent,
			objects.brother_of as brother_of,
			objects.status as status,
			objects.class_id as class_id,
			objects.acldata as acldata,
			objects.parent as parent,
 			planner.start as start,
 			planner.end as end,
 			planner.ucheck5 as location
			FROM
			objects  LEFT JOIN planner ON planner.id = objects.brother_of
			WHERE
			objects.class_id = '".CL_CALENDAR_EVENT."'
			AND (objects.metadata LIKE '%\'trans_".aw_global_get("lang_id")."_status\'=>\'1\'%' OR objects.lang_id = '".aw_global_get("lang_id")."')
			AND planner.start  <  ".(time() + 86400 * $days)."
			AND planner.end  > ".time()."
			AND  objects.status > 0
			ORDER BY planner.ucheck5, planner.`start` DESC;
		";

		$hrs = $arr["obj_inst"]->prop("similar_time") * 3600;
		$ol = new object_list();
		$this->db_query($q);
		$last = array();
		while($w = $this->db_next())
		{
			if($w["location"] == $last["location"] && ($last["start"] < $w["end"] + $hrs) && ($last["end"] + $hrs > $w["start"]))
			{
				$ol->add($w["oid"]);
				$ol->add($last["oid"]);
			}
			$last = $w;
		}
		exit_function("events::sql");
/*
		enter_function("events::ol");
		$filter = array(
			"class_id" => array(CL_CALENDAR_EVENT),
			"site_id" => array(),
			"lang_id" => array(),
			"start1" => new obj_predicate_compare(OBJ_COMP_LESS, (time() + 86400 * $days)),
			"end" => new obj_predicate_compare(OBJ_COMP_GREATER, (time())),
		);
		$ol = new object_list($filter);
		//arr($ol->names());
		exit_function("events::ol");
*/		return $ol;
	}

	function _get_events_tb(&$arr)
	{
		$this_o = $arr["obj_inst"];
		$cfg = $this->get_cgf_from_manager($arr["obj_inst"], "event");
		$arr["prop"]["vcl_inst"]->add_button(array(
			"name" => "new",
			"img" => "new.gif",
			"disabled" => !$this->can("add", $this_o->prop("event_menu")),
			"tooltip" => t("Lisa uus"),
			"url" => $this->mk_my_orb("new", array(
				"parent" => $this_o->prop("event_menu"),
				"cfgform" => $cfg,
				"return_url" => get_ru()
			) + (aw_global_get("section") ? array("section" => aw_global_get("section")) : array()), "calendar_event")
		));
		$arr["prop"]["vcl_inst"]->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"tooltip" => t("Kustuta m&auml;rgistatud s&uuml;ndmused"),
			"action" => "delete",
			"confirm" => t("Olete kindel, et soovite kustudada k&otilde;ik valitud s&uuml;ndmused?"),
		));
		$arr["prop"]["vcl_inst"]->add_button(array(
			"name" => "print",
			"img" => "print.gif",
			"action" => "print_events",
			"tooltip" => t("S&uuml;ndmuste v&auml;ljatr&uuml;kk"),
		));
/*		$arr["prop"]["vcl_inst"]->add_button(array(
			"name" => "archive",
			"img" => "archive_small.gif",
			"url" => aw_url_change_var("archived",($arr["request"]["archived"] == 1 ? -1 : 1)),
			"tooltip" => $arr["request"]["archived"] == 1 ? t("Uued"):t("Arhiiv"),
		));
*/
		$arr["prop"]["vcl_inst"]->add_button(array(
			"name" => "sort",
			"img" => "check_red.gif",
//			"url" => aw_url_change_var("sort_by_created",($arr["request"]["sort_by_created"] == 1 ? 1 : 1)),
			"url" => aw_url_change_var("sortby","created"),
			"tooltip" => ($arr["request"]["sort_by_created"] == 1 ? t("J&auml;rjesta laekumise j&auml;rgi") : t("J&auml;rjesta laekumise j&auml;rgi")),
		));

/*		$arr["prop"]["vcl_inst"]->add_button(array(
			"name" => "see_all",
			"img" => "class_31.gif",
			"url" => $this->mk_my_orb("change" , array("group" => "events" , "id" => $arr["obj_inst"]->id(), "section" => $arr["request"]["section"])),
			"tooltip" => t("Algseis (kestvad ja tulekul s&uuml;ndmused)"),
		));
*/
		$arcu = html::get_change_url($_GET["id"], array("archived" => 1, "return_url" => $_GET["post_ru"], "section" => $_GET["section"]));
//		$arcu = aw_url_change_var("archived", 1);
//		$newu = aw_url_change_var("archived", 0);
		$newu = html::get_change_url($_GET["id"], array("archived" => 0, "return_url" => $_GET["post_ru"], "section" => $_GET["section"]));
		$select = html::radiobutton(array(
			"name" => "arc_button",
			"id" => "arc_button",
			"caption" => t("Arhiiv"),
			"checked" => !$_GET["e_find_news"] &&  $_GET["archived"],
			"onclick" => 'javascript:window.location.href="'.$arcu.'";'
		)).html::radiobutton(array(
			"name" => "new_button",
			"id" => "new_button",
			"caption" => t("Aktiivsed"),
			"checked" => !$_GET["e_find_news"] &&  !$_GET["archived"],
			"onclick" => 'javascript:window.location.href="'.$newu.'";'
		));

		$arr["prop"]["vcl_inst"]->add_cdata($select);

	}

	function _get_similar_tb(&$arr)
	{
		$arr["prop"]["vcl_inst"]->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"tooltip" => t("Kustuta m&auml;rgistatud s&uuml;ndmused"),
			"action" => "delete",
			"confirm" => t("Olete kindel, et soovite kustudada k&otilde;ik valitud s&uuml;ndmused?"),
		));
	}

	function _init_places_table(&$t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "comment",
			"caption" => t("Kirjeldus"),
			"align" => "center"
		));
		$t->define_chooser(array(
			"name" => "evmgr_objsel",
			"field" => "oid"
		));
	}

	function _init_organiser_table(&$t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "address",
			"caption" => t("Aadress"),
			"align" => "center"
		));
		$t->define_chooser(array(
			"name" => "evmgr_objsel",
			"field" => "oid"
		));
	}

	function _init_editors_table(&$t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "company",
			"caption" => t("Organisatsioon"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "user",
			"caption" => t("Kasutajanimi"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "group",
			"caption" => t("Kasutajagrupp"),
			"align" => "center"
		));
		$t->define_chooser(array(
			"name" => "evmgr_objsel",
			"field" => "oid"
		));
	}

	function _init_event_table(&$t,$arr)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Pealkiri"),
			"align" => "center",
			"chgbgcolor" => "col",
		));
		$t->define_field(array(
			"name" => "time",
			"caption" => t("Aeg"),
			"align" => "center",
			"sortable" => 1,
			"chgbgcolor" => "col",
			"callback" =>  array(&$this, "__time_format"),
			"callb_pass_row" => true,
		));

		$t->define_field(array(
			"name" => "sector",
			"caption" => t("Valdkond"),
			"align" => "center",
			"chgbgcolor" => "col",
		));
		$t->define_field(array(
			"name" => "level",
			"caption" => t("Tase"),
			"align" => "center",
			"chgbgcolor" => "col",
		));
		$t->define_field(array(
			"name" => "region",
			"caption" => t("Regioon"),
			"align" => "center",
			"chgbgcolor" => "col",
		));
		if($arr["request"]["group"] != "similar_find")
		{
			$t->define_field(array(
				"name" => "tasks",
				"caption" => t("Tegevused"),
				"align" => "center",
				"chgbgcolor" => "col",
			));
		}
		$t->define_field(array(
			"name" => "translated",
			"caption" => t("T&otilde;lgitud"),
			"align" => "center",
			"chgbgcolor" => "col",
		));
		$t->define_chooser(array(
			"name" => "evmgr_objsel",
			"field" => "oid",
			"chgbgcolor" => "col",
		));
		$t->set_default_sortby(array("oid"));
	}

	function __time_format($val)
	{
		return $val["time_val"];
	}

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	/**
		@attrib name=delete is_public="1" caption="Change"
	**/
	function delete($arr)
	{
		if (!isset($arr["evmgr_objsel"]) and isset($_GET["evmgr_objsel"]))
		{
			$arr = $_GET;
		}

		if ("sectors" === $arr["group"])
		{
			$arr["evmgr_objsel"] = explode(",", $arr["evmgr_objsel"]);
		}

		object_list::iterate_list($arr["evmgr_objsel"], "delete");
		return $arr["post_ru"];
	}

	/**
		@attrib name=add_new_editor is_public="1" caption="Change"
	**/
	function add_new_editor($arr)
	{
		if (!$this->can("add", $arr["id"]))
		{
			print t("Parameeter vale.");
			return $arr["post_ru"];
		}

		$this_o = new object($arr["id"]);
		$editor = new object();
		$editor->set_class_id(CL_CRM_PERSON);
		$editor->set_parent($arr["id"]);
		$editor->save();
		$this_o->connect(array("to"=> $editor->id(), "type" => "RELTYPE_EDITOR"));
		$cfg = $this->get_cgf_from_manager($this_o, "editor");
		return html::get_change_url($editor->id(),array("return_url" => $arr["post_ru"], "cfgform" => $cfg) + ($arr["section"] ? array("section" => $arr["section"]) : array()));
	}

	/**
		@attrib name=publish is_public="1" all_args=1
	**/
	function publish($arr)
	{
		if (!$this->can("view", $arr["id"]))
		{
			print t("Parameeter vale.");
			return $arr["post_ru"];
		}

		$event = obj($arr["id"]);
		$event->set_prop("published" , 1);
		$event->save();
		return $arr["post_ru"];
	}

	/**
		@attrib name=make_copy is_public="1" all_args=1
	**/
	function make_copy($arr)
	{
		$event = obj($arr["id"]);

		$o = new object();
		$o->set_class_id(CL_CALENDAR_EVENT);
		$o->set_parent($event->parent());
		$o->set_name($event->name());

		$o->set_prop("published" , 0);
		$o->set_prop("start1" , $event->prop("start1"));
		$o->set_prop("end" , $event->prop("end"));
		$o->set_prop("front_event" , $event->prop("front_event"));
		$o->set_prop("level" , $event->prop("level"));
		$o->set_prop("organizer" , $event->prop("organizer"));
		$o->set_prop("location" , $event->prop("location"));
		$o->set_prop("sector" , $event->prop("sector"));
		$o->set_prop("description" , $event->prop("description"));
		$o->set_prop("short_description" , $event->prop("short_description"));
		$o->set_prop("title" , $event->prop("title"));

		$o->save();
		$o->set_meta($event->meta());
		$o->save();

		foreach($event->connections_from(array("type" => "RELTYPE_EVENT_TIME")) as $c)
		{
			$t = $c->to();
			$nt = new object();
			$nt->set_parent($o->id());
			$nt->set_class_id(CL_EVENT_TIME);
			$nt->set_name($nt->name() . " - ".t("toimumisaeg"));

			$nt->set_prop("start" , $t->prop("start"));
			$nt->set_prop("end" , $t->prop("end"));
			$nt->set_prop("location" , $t->prop("location"));
//			$nt->set_prop("event" , $t->prop("event"));
			$nt->save();

			$nt->connect(array("to" => $t->prop("location") , "reltype" => 1));

			$o->add_event_time($nt->id());
//			$o->connect(array("to" => $nt->id() , "reltype" => 9));
		}

		return $arr["post_ru"];
	}


	/**
		@attrib name=events_xml is_public="1" all_args=1
	**/
	function events_xml($arr)
	{

		$events = $this->get_xml_events($arr);

		$ret = '<?xml version="1.0" encoding="UTF-8" ?>
<!-- tase = (0->"Yleriigiline", 1->"Kohalik syndmus", 2->"Syndmus valismaal") -->
<syndmused count="'.$events->count().'">';

		foreach(array_reverse($events->arr()) as $o)
		{
			$ret.= $this->to_xml($o);
		}

		$ret.= '
</syndmused>';

		header ("Content-Type: text/xml");
		die($ret);
	}

	function to_xml($o)
	{
		$sectors = $o->connections_from(array(
			"type" => "SECTOR"
		));
		$n = 0;
		$sa = array(); // valdkondade objektid
		foreach($sectors as $c)
		{
			$s = $c->to();
			$sa[$n]["name"] = $s->name();
			$sa[$n]["id"] = $this->teemad[$s->name()];
			$n ++;
		}
		$mail = $phone = $url = $address = "";
		if(is_oid($o->prop("organizer")) && $this->can("view" , $o->prop("organizer")))
		{
			$organiser = obj($o->prop("organizer"));
			if($organiser->class_id() == CL_CRM_PERSON)
			{
				$email = $organiser->prop("email.mail");
				$phone = $organiser->prop("phone.name");
				$url = $organiser -> prop("url.name");
				$address = $organiser->prop("address.name");
			}
			else
			{
				$email = $organiser->prop("email_id.mail");
				$phone = $organiser->prop("phone_id.name");
				$url = $organiser -> prop("url_id.name");
				$address = $organiser->prop("contact.name");
			}
		}

		$ret = '
	<syndmus>
		<id>'.$o->id().'</id>
		<pealkiri><![CDATA[ '.$o->name().' ]]></pealkiri>
		<sissejuhatus><![CDATA[ '.$o->prop("title").' ]]></sissejuhatus>
		<kirjeldus><![CDATA[ '.$o->prop("description").' ]]></kirjeldus>
		<koht id="'.$o->prop("location").'"><![CDATA[ '.$o->prop("location.name").' ]]></koht>
		<maakond id="'.$o->prop("location.address.maakond").'">'.$o->prop("location.address.maakond.name").'</maakond>
		<linn id="'.$o->prop("location.address.linn").'">'.$o->prop("location.address.linn.name").'</linn>
		<riik id="'.$o->prop("location.address.riik").'">'.$o->prop("location.address.riik.name").'</riik>
		<teema1 id="0">'.$sa[0]["name"].'</teema1>
		<teema2 id="0">'.$sa[1]["name"].'</teema2>
		<teema3 id="0">'.$sa[2]["name"].'</teema3>
		<tase>'.$o->prop("level").'</tase>
		<asukoht_vabatekst><![CDATA[  ]]></asukoht_vabatekst>
		<kontakt_nimi><![CDATA[ '.$o->prop("organizer.name").' ]]></kontakt_nimi>
		<kontakt_email><![CDATA[ '.$email.' ]]></kontakt_email>
		<kontakt_tel><![CDATA[ '.$phone.' ]]></kontakt_tel>
		<kontakt_url><![CDATA[ '.$url.' ]]></kontakt_url>
		<kontakt_aadress><![CDATA[ '.$address.' ]]></kontakt_aadress>
		<timestamp>'.date("Y-m-d H:i:s" , $o->modified()).'</timestamp>
		<algallikas>'.$o->prop("utextbox1").'</algallikas>
 		'.$this->times_xml($o).'
	</syndmus>';

		return $ret;
	}

	function times_xml($o)
	{
		$times = $o->connections_from(array("type" => "RELTYPE_EVENT_TIME"));
		$ret = '<ajad count="'.count($times).'">';

		foreach($times as $c)
		{
			$t = $c->to();
			$ret.='
			<aeg>
				<id>'.$t->id().'</id>
				<algus>'.date("Y-m-d H:i:s" , $t->prop("start")).'</algus>
				<lopp>'.date("Y-m-d H:i:s" , $t->prop("end")).'</lopp>
				<markus><![CDATA[  ]]></markus>
				<timestamp>'.date("Y-m-d H:i:s" , $t->modified()).'</timestamp>
			</aeg>';
		}
		$ret.='
		</ajad>';
		return $ret;
	}

	function get_xml_events($arr)
	{
		extract($arr);
		$filter = array(
			"class_id" => array(CL_CALENDAR_EVENT),
			"site_id" => array(),
			"lang_id" => array(),
			"sort_by" => "objects.created ASC",
			"limit" => 100,
		);
		if($starttimestamp)
		{
			$start = mktime(substr($starttimestamp, 8, 2),substr($starttimestamp, 10, 2),substr($starttimestamp, 12, 2),substr($starttimestamp, 4, 2),substr($starttimestamp, 6, 2),substr($starttimestamp, 0, 4));
		}
		else
		{
			$start = time() - 24*3600;
		}

		$filter[] = new object_list_filter(array(
			"logic" => "OR",
			"conditions" => array(
				"CL_CALENDAR_EVENT.RELTYPE_EVENT_TIME.modified" => new obj_predicate_compare(OBJ_COMP_GREATER, $start),
				"modified" => new obj_predicate_compare(OBJ_COMP_GREATER, $start)
			)
		));

		$countys = array(
			1 => "Harjumaa",
			2 => "Hiiumaa",
			3 => "Ida-Virumaa",
			4 => "J&otilde;gevamaa",
			5 => "J&auml;rvamaa",
			6 => "L&auml;&auml;nemaa",
			7 => "L&auml;&auml;ne-Virumaa",
			8 => "P&otilde;lvamaa",
			9 => "P&auml;rnumaa",
			10 => "Raplamaa",
			11 => "Saaremaa",
			12 => "Tartumaa",
			13 => "Valgamaa",
			14 => "Viljandimaa",
			15 => "V&otilde;rumaa",
		);

		$citys = array(
			1 => "Haapsalu",
			2 => "J&otilde;geva",
			3 => "Kuressaare",
			4 => "K&auml;rdla",
			5 => "Narva",
			6 => "Paide",
			7 => "P&otilde;lva",
			8 => "P&auml;rnu",
			9 => "Rakvere",
			10 => "Rapla",
			11 => "Tallinn",
			12 => "Tartu",
			13 => "Valga",
			14 => "Viljandi",
			15 => "V&otilde;ru",
			16 => "J&otilde;hvi",
			17 => "Kohtla-J&auml;rve",
			18 => "Otep&auml;&auml;",
		);

		$this->teemad = array(
			1 => "muusika",
			2 => "klassikaline muusika",
			88=> "vanamuusika" ,
			3=> "p&auml;rimusmuusika" ,
			102 => "orkestrimuusika",
			100 => "koorimuusika",
			4 => "jazzmuusika",
			5 => "rock-/popmuusika",
			29 => "alternatiivmuusika",
			32 => "festival",
			6 => "teater",
			8 => "draama" ,
			30 => "kom&ouml;&ouml;dia",
			10 => "muusikal",
			9 => "ooper",
			95 => "operett",
			12 => "lasteetendus",
			98 => "koguperelavastus",
			13 => "vaba&otilde;huetendus",
			33 => "alternatiivteater",
			31 => "festival",
			7 => "draama",
			34 => "tants",
			35 => "klassikaline ballett",
			36 => "kaasaegne tants",
			84 => "rahvatants",
			37 => "showtants",
			38 => "tsirkus",
			39 => "festival",
			14 => "film ja foto",
			46 => "dokumentaalfilm",
			48 => "kunstiline film",
			47 => "animafilm",
			114 => "n&auml;itus",
			115 => "workshop",
			49 => "festival",
			15 => "kunst",
			43 => "n&auml;itus",
			45 => "performance",
			90 => "workshop",
			44 => "festival",
			16 => "kirjandus",
			135 => "raamatuesitlus",
			105 => "p&auml;rimuskultuur",
			109 => "rahvatants",
			110 => "p&auml;rimusmuusika",
			111 => "k&auml;sit&ouml;&ouml;",
			112 => "rahvakalender",
			113 => "festival",
			17 => "loengud",
			40 => "seminar",
			42 => "konverents",
			91 => "teadus",
			134 => "vestlus&otilde;htu",
			41 => "kursused",
			93 => "sport",
			106 => "n&auml;itused",
			116 => "ajalugu",
			117 => "fotograafia",
			118 => "kirjandus",
			119 => "kunst ja arhitektuur",
			120 => "loodus",
			121 => "teadus ja tehnika",
			122 => "teater ja muusika",
			22 => "varia",
			18 => "meelelahutus",
			136 => "laat",
			85 => "muuseumid",
			123 => "festivalid",
		);

//		if($maakond)
//		{
//			$filter["locaton.address.maakond.name"] = $countys[$maakond];
//			$filter[] = new object_list_filter(array(
//				"logic" => "OR",
//				"conditions" => array(
//					"CL_CALENDAR_EVENT.RELTYPE_EVENT_TIME.location.address.maakond.name" =>  $countys[$maakond],
//					"CL_CALENDAR_EVENT.location.address.maakond.name" =>  $countys[$maakond],
//				)
//			));
//		}

//		if($linn)
//		{
		//	$filter["locaton.address.linn.name"] = $citys[$linn];
//			$filter[] = new object_list_filter(array(
//				"logic" => "OR",
//				"conditions" => array(
//					"CL_CALENDAR_EVENT.RELTYPE_EVENT_TIME.location.address.linn.name" =>  $citys[$linn],
//					"CL_CALENDAR_EVENT.location.address.linn.name" =>  $citys[$linn],
//				)
//			));
//		}


		if($teema)
		{
			$filter["CL_CALENDAR_EVENT.RELTYPE_SECTOR.kood"] = $teema;

		}
//keel? est (vaikimisi), eng. Kuvatakse vastavalt eesti voi ingliskeelsed syndmused.

		$ol =  new object_list($filter);

		//kuna tuli probleeme, siis peab kirvemeetodil praakima valed linnad ja maakonnad valja
		if($linn || $maakond)
		{

			foreach($ol->arr() as $o)
			{
				$del = 1;
				$adr = array();
				if(is_oid($o->prop("location.address")))
				{
					$adr[] = obj($o->prop("location.address"));
				}
				foreach($o->connections_from(array("type" => "RELTYPE_EVENT_TIME")) as $c)
				{
					$t = $c->to();
					if(is_oid($t->prop("location.address")))
					{
						$adr[] = obj($t->prop("location.address"));
					}
				}

				foreach($adr as $address)
				{
					if($linn && $address->prop("linn.name") == $citys[$linn])
					{
						$del = 0;
						break;
					}

					if($maakond && $address->prop("maakond.name") == $countys[$maakond])
					{
						$del = 0;
						break;
					}
				}
				if($del)
				{
					$ol->remove($o->id());
				}
			}
		}
		return $ol;
	}

	private function translated_objs($clid)
	{
		$ids_sql_results = $this->db_fetch_array("SELECT oid FROM objects WHERE class_id = '".$clid."' AND (metadata LIKE '%\'trans_".aw_global_get("lang_id")."_status\'=>\'1\'%' OR lang_id = '".aw_global_get("lang_id")."')");
		$ids = array();
		foreach($ids_sql_results as $ids_sql_result)
		{
			$ids[] = $ids_sql_result["oid"];
		}
		// If no OIDs is returned in the SQL, no objects should be returned in the object list.
		if(count($ids) == 0)
		{
			$ids[] = -1;
		}
		return $ids;
	}
}
?>
