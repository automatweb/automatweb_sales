<?php

/*

HANDLE_MESSAGE_WITH_PARAM(MSG_POPUP_SEARCH_CHANGE, CL_MRP_CASE, on_popup_search_change)

@classinfo syslog_type=ST_MRP_CASE relationmgr=yes no_status=1 prop_cb=1 confirm_save_data=1 maintainer=voldemar

@tableinfo mrp_case index=oid master_table=objects master_index=oid
@tableinfo mrp_case_schedule index=oid master_table=objects master_index=oid

	@groupinfo grp_general caption="&Uuml;ldine" parent=general
	@groupinfo grp_case_data caption="Projekti andmed" parent=general
@groupinfo grp_case_formula caption="Tooteretsept"
	@groupinfo grp_case_workflow caption="T&ouml;&ouml;voog" parent=grp_case_formula
	@groupinfo grp_case_materials caption="Materjalid" parent=grp_case_formula
@groupinfo grp_case_view caption="Vaatleja t&ouml;&ouml;laud" submit=no
@groupinfo grp_case_schedule caption="Tellimuse t&ouml;&ouml;voog" submit=no
	@groupinfo grp_case_schedule_gantt caption="T&ouml;&ouml;voo diagramm" submit=no parent=grp_case_schedule
	@groupinfo grp_case_schedule_google caption="Graafikud" submit=no parent=grp_case_schedule
@groupinfo grp_case_comments caption="Kommentaarid"
@groupinfo grp_case_log caption="Ajalugu" submit=no


@property workflow_toolbar type=toolbar store=no no_caption=1 group=grp_case_schedule_gantt,grp_general,grp_case_workflow,grp_case_materials,grp_case_data editonly=1
@property workflow_errors type=text store=no no_caption=1 group=grp_case_schedule_gantt,grp_general,grp_case_workflow,grp_case_data
@property header type=text store=no no_caption=1 group=grp_general,grp_case_data

@default group=grp_general
	@property name type=textbox table=objects field=name group=grp_case_schedule_gantt,grp_general,grp_case_workflow,grp_case_materials,grp_case_data parent=general_info
	@caption Projekti nr.

	@property comment type=textbox table=objects field=comment
	@caption Projekti nimetus

@default table=mrp_case
	@property order_quantity type=textbox datatype=int
	@caption Tellimuse kogus

	@property workspace type=hidden

	@property state type=text group=grp_case_schedule_gantt,grp_general,grp_case_workflow,grp_case_materials,grp_case_data editonly=1 parent=general_info
	@caption Staatus

	@property starttime type=datetime_select
	@caption Alustamisaeg (materjalide saabumine)

	@property due_date type=datetime_select
	@caption Valmimist&auml;htaeg

	@property project_priority type=textbox maxlength=10
	@caption Projekti prioriteet

	@property customer type=relpicker reltype=RELTYPE_MRP_CUSTOMER clid=CL_CRM_COMPANY group=grp_case_schedule_gantt,grp_general,grp_case_workflow,grp_case_materials,grp_case_data editonly=1 parent=general_info
	@caption Klient

	@property customer_relation type=relpicker reltype=RELTYPE_CUSTOMER_RELATION editonly=1
	@caption Kliendisuhe

	@property progress type=hidden
	@property extern_id type=hidden

@default table=mrp_case_schedule
	@property planned_date type=text editonly=1
	@caption Planeeritud valmimisaeg

@default table=mrp_case
	@property started type=text editonly=1
	@caption Alustatud

	@property finished type=text editonly=1
	@caption L&otilde;petatud

	@property archived type=text editonly=1
	@caption Arhiveeritud

	@property purchasing_manager type=select editonly=1
	@caption Ladu

@default table=objects
@default field=meta
@default method=serialize
	@property sales_priority type=textbox size=5
	@caption Prioriteedihinnang m&uuml;&uuml;gimehelt


@default group=grp_case_data
	@property format type=textbox
	@caption Formaat

	@property sisu_lk_arv type=textbox
	@caption Sisu lk arv

	@property kaane_lk_arv type=textbox
	@caption Kaane lk arv

	@property sisu_varvid type=textbox
	@caption Sisu v&auml;rvid

	@property sisu_varvid_notes type=textbox
	@caption Sisu v&auml;rvid Notes

	@property sisu_lakk_muu type=textbox
	@caption Sisu lakk/muu

	@property kaane_varvid type=textbox
	@caption Kaane v&auml;rvid

	@property kaane_varvid_notes type=textbox
	@caption Kaane v&auml;rvid Notes

	@property kaane_lakk_muu type=textbox
	@caption Kaane lakk/muu

	@property sisu_paber type=textbox
	@caption Sisu paber

	@property kaane_paber type=textbox
	@caption Kaane paber

// deprecated, use 'order_quantity' instead
	@property trykiarv type=textbox
	@caption Tr&uuml;kiarv

	@property trykise_ehitus type=textbox
	@caption Tr&uuml;kise ehitus

	@property kromaliin type=textbox
	@caption Kromalin

	@property makett type=textbox
	@caption Makett

	@property naidis type=textbox
	@caption N&auml;idis

	@property plaate type=textbox
	@caption Plaate

	@property transport type=textbox
	@caption Transport

	@property soodustus type=textbox
	@caption Soodustus

	@property markused type=textbox
	@caption M&auml;rkused

	@property allahindlus type=textbox
	@caption Allahindlus

	// @property vahendustasu type=textbox
	@property vahendustasu type=hidden
	@caption Vahendustasu

	// @property myygi_hind type=textbox
	@property myygi_hind type=hidden
	@caption M&uuml;&uuml;gi hind


@default group=grp_case_comments
	@property user_comments type=comments
	@caption Kommentaarid juhtumi ja t&ouml;&ouml;de kohta


@default group=grp_case_workflow
	@layout vsplitbox type=hbox width=25%:75% group=grp_case_workflow,grp_case_materials,grp_case_schedule_gantt
		@layout left_pane type=vbox parent=vsplitbox group=grp_case_workflow,grp_case_materials,grp_case_schedule_gantt
			@layout general_info type=vbox parent=left_pane area_caption=Projekti&nbsp;&uuml;ldandmed closeable=1 group=grp_case_workflow,grp_case_materials,grp_case_schedule_gantt
			@layout resource_tree type=vbox parent=left_pane area_caption=Ressursid&nbsp;kategooriate&nbsp;kaupa closeable=1
				@property resource_tree type=text store=no no_caption=1 parent=resource_tree
		@property workflow_table type=table store=no no_caption=1 parent=vsplitbox

@default group=grp_case_materials
	#layout vsplitbox type=hbox width=25%:75%
		#layout left_pane type=vbox parent=vsplitbox
		@layout materials_tree type=vbox parent=left_pane area_caption=Vali&nbsp;kasutatavad&nbsp;materjalid closeable=1
				@property materials_tree type=treeview store=no no_caption=1 parent=materials_tree
		@property materials_table type=table store=no no_caption=1 parent=vsplitbox


@default group=grp_case_schedule_gantt

	@property schedule_chart type=text store=no no_caption=1 parent=vsplitbox

@default group=grp_case_schedule_google

	@layout charts type=hbox width=50%:50%

		@layout states_chart type=vbox area_caption=T&ouml;&ouml;d&nbsp;staatuste&nbsp;kaupa parent=charts closeable=1

			@property states_chart type=google_chart no_caption=1 parent=states_chart store=no

		@layout recources_chart type=vbox area_caption=Kestused&nbsp;ressursside&nbsp;kaupa parent=charts closeable=1

			@property recources_chart type=google_chart no_caption=1 parent=recources_chart store=no

	@layout job_charts type=vbox area_caption=T&ouml;&ouml;de&nbsp;kestuste&nbsp;v&otilde;rdlus closeable=1

		@property job_charts_tbl type=table store=no no_caption=1 parent=job_charts

@default group=grp_case_log
	@property log type=table store=no no_caption=1


@default group=grp_case_view

	@layout view_general_info type=hbox area_caption=Projekti&nbsp;&uuml;ldandmed closeable=1 width=50%:50%

		@layout view_general_info_left type=vbox parent=view_general_info

			@property vgi_name type=text parent=view_general_info_left
			@caption Projekti nr.

			@property vgi_comment type=text parent=view_general_info_left
			@caption Projekti nimetus

			@property vgi_customer type=text parent=view_general_info_left
			@caption Klient

		@layout view_general_info_right type=vbox parent=view_general_info

			@property vgi_state type=text parent=view_general_info_right
			@caption Staatus

			@property vgi_due_date type=text parent=view_general_info_right
			@caption Valmimist&auml;htaeg

			@property vgi_starttime type=text parent=view_general_info_right
			@caption Alustamisaeg (materjalide saabumine)

	@property case_view type=table no_caption=1 store=no


// --------------- RELATION TYPES ---------------------

@reltype MRP_MANAGER value=1 clid=CL_USER
@caption M&uuml;&uuml;gimees/Projektijuht

@reltype MRP_CUSTOMER value=2 clid=CL_CRM_COMPANY
@caption Klient

@reltype CUSTOMER_RELATION value=6 clid=CL_CRM_COMPANY_CUSTOMER_DATA
@caption Kliendisuhe

@reltype MRP_PROJECT_JOB value=3 clid=CL_MRP_JOB
@caption T&ouml;&ouml;

@reltype MRP_USED_RESOURCE value=4 clid=CL_MRP_RESOURCE
@caption Kasutatav ressurss

// DEPRECATED
@reltype MRP_OWNER value=5 clid=CL_MRP_WORKSPACE
@caption Projekti omanik

*/


/*

CREATE TABLE `mrp_case` (
  `oid` int(11) NOT NULL default '0',
  `starttime` int(10) unsigned default NULL,
  `started` int(10) unsigned default NULL,
  `progress` int(10) unsigned default NULL,
  `due_date` int(10) unsigned default NULL,
  `project_priority` int(10) unsigned default NULL,
  `state` tinyint(2) unsigned default '1',
  `extern_id` int(11) unsigned default NULL,
  `customer` int(11) unsigned default NULL,
  `customer_relation` int(11) unsigned default NULL,
  `finished` int(10) unsigned default NULL,
  `archived` int(10) unsigned default NULL,

	PRIMARY KEY  (`oid`)
) TYPE=MyISAM;

ALTER TABLE `mrp_case` ADD `finished` INT(10) UNSIGNED;
ALTER TABLE `mrp_case` ADD `archived` INT(10) UNSIGNED;

CREATE TABLE `mrp_case_schedule` (
	`oid` int(11) NOT NULL default '0',
	`planned_date` int(10) unsigned default NULL,

	PRIMARY KEY  (`oid`)
) TYPE=MyISAM;

*/

require_once "mrp_header.aw";

class mrp_case extends class_base
{
	protected $workspace; // mrp_workspace object
	protected $mrp_error = "";
	protected $states = array();

	function mrp_case()
	{
		$this->states = array (
			mrp_case_obj::STATE_NEW => t("Uus"),
			mrp_case_obj::STATE_PLANNED => t("Planeeritud"),
			mrp_case_obj::STATE_INPROGRESS => t("T&ouml;&ouml;s"),
			mrp_case_obj::STATE_ABORTED => t("Katkestatud"),
			mrp_case_obj::STATE_DONE => t("Valmis"),
			mrp_case_obj::STATE_LOCKED => t("Lukustatud"),
			mrp_case_obj::STATE_DELETED => t("Kustutatud"),
			mrp_case_obj::STATE_ONHOLD => t("Plaanist v&auml;ljas"),
			mrp_case_obj::STATE_ARCHIVED => t("Arhiveeritud"),

			mrp_job_obj::STATE_NEW => t("Uus"),
			mrp_job_obj::STATE_PLANNED => t("Planeeritud"),
			mrp_job_obj::STATE_INPROGRESS => t("T&ouml;&ouml;s"),
			mrp_job_obj::STATE_ABORTED => t("Katkestatud"),
			mrp_job_obj::STATE_DONE => t("Valmis"),
			mrp_job_obj::STATE_LOCKED => t("Lukustatud"),
			mrp_job_obj::STATE_PAUSED => t("Paus"),
			mrp_job_obj::STATE_SHIFT_CHANGE => t("Paus"),
			mrp_job_obj::STATE_DELETED => t("Kustutatud")
		);

		$this->init(array(
			"tpldir" => "mrp/mrp_case",
			"clid" => CL_MRP_CASE
		));
	}

	function callback_on_load ($arr)
	{
		if (empty($arr["request"]["id"]))
		{
			if (!automatweb::$request->arg_isset("mrp_workspace"))
			{
				$this->mrp_error .= t("Uut projekti saab luua vaid ressursihalduskeskkonnast.");
				return;
			}

			$ws_oid = automatweb::$request->arg("mrp_workspace");
			$this->workspace = obj ($ws_oid, array(), CL_MRP_WORKSPACE);
		}
		else
		{
			$this_object = obj($arr["request"]["id"]);
			$this->workspace = $this_object->prop("workspace");

			if (!$this->workspace)
			{
				$this->mrp_error .= t("Projektil puudub ressursihalduskeskkond. ");
			}
		}

		if ($this->mrp_error)
		{
			echo t("Viga! ") . $this->mrp_error;
		}
	}

	function callback_mod_reforb ($arr)
	{
		if (isset($arr["request"]["mrp_workspace"]))
		{
			$arr["mrp_workspace"] = $arr["request"]["mrp_workspace"];
		}

		if ($this->workspace)
		{
			$arr["mrp_workspace"] = $this->workspace->id ();
		}
	}

	function get_property($arr)
	{
		if ($this->mrp_error)
		{
			return PROP_IGNORE;
		}

		$prop =& $arr["prop"];
		$retval = PROP_OK;
		$this_object = $arr["obj_inst"];

		$txt_grps = array("grp_case_schedule", "grp_case_schedule_gantt", "grp_case_workflow", "grp_case_formula", "grp_case_materials", "grp_case_view");

		if(substr($prop["name"], 0, 4) === "vgi_")
		{
			$prop["name"] = substr($prop["name"], 4);
			$prop["value"] = $arr["obj_inst"]->prop($prop["name"]);
		}

		switch($prop["name"])
		{
			case "name":
			case "comment":
				if(isset($arr["request"]["group"]) and in_array($arr["request"]["group"], $txt_grps))
				{
					$prop["type"] = "text";
					$prop["caption"] = trim($prop["caption"], "\.").":";
				}
				break;

			case "customer":
				if(isset($arr["request"]["group"]) and in_array($arr["request"]["group"], $txt_grps))
				{
					$prop["type"] = "text";
					$prop["value"] = $arr["obj_inst"]->prop($prop["name"].".name");
					$prop["caption"] .= ":";
				}
				break;

			case "header":
				if ($arr["new"])
				{
					return PROP_IGNORE;
				}

				$prop["value"] = $this->get_header($arr);
				break;

			case "workflow_errors":
				if (empty ($arr["request"]["errors"]))
				{
					$prop["value"] = "";
				}
				else
				{
					$errors = $arr["request"]["errors"];
					$this->dequote ($errors);
					$errors = unserialize ($errors);

					if (!empty ($errors))
					{
						$prop["value"] = ' <div style="color: #DF0D12; margin: 5px;">' . t('Esinenud t&otilde;rked: ') . implode (". ", $errors) . '.</div>';
						unset ($arr["request"]["errors"]);
					}
				}
				break;

			case "state":
				$prop["value"] = empty($this->states[$prop["value"]]) ? t("M&auml;&auml;ramata") : $this->states[$prop["value"]];
				if(isset($arr["request"]["group"]) and in_array($arr["request"]["group"], $txt_grps))
				{
					$prop["caption"] .= ":";
				}
				break;

			case "planned_date":
				$date = empty($prop["value"]) ? t("Planeerimata") : date(MRP_DATE_FORMAT, $prop["value"]);
				$prop["value"] = $date;
				break;

			case "due_date":
			case "starttime":
				if ($arr["new"])
				{
					$prop["value"] = mktime (18, 00, 00);
				}
				if(isset($arr["request"]["group"]) and in_array($arr["request"]["group"], $txt_grps))
				{
					$prop["type"] = "text";
					$prop["caption"] .= ":";
					$prop["value"] = aw_locale::get_lc_date($prop["value"], 7);
				}
				break;

			case "finished":
				$prop["value"] = ($this_object->prop ("state") == mrp_case_obj::STATE_DONE) ? date(MRP_DATE_FORMAT, $prop["value"]) : t("Pole veel l&otilde;petatud");
				break;

			case "archived":
				$prop["value"] = ($this_object->prop ("state") == mrp_case_obj::STATE_ARCHIVED) ? date(MRP_DATE_FORMAT, $prop["value"]) : t("Pole veel arhiveeritud");
				break;

			case "schedule_chart":
				### project states for showing its schedule chart
				$applicable_states = array (
					mrp_case_obj::STATE_PLANNED,
					mrp_case_obj::STATE_INPROGRESS
				);

				if (in_array ($this_object->prop ("state"), $applicable_states))
				{
					if ($this->workspace)
					{
						### update schedule
						$schedule = new mrp_schedule();
						$schedule->create (array("mrp_workspace" => $this->workspace->id()));
					}
					else
					{
						$prop["value"] = t("T&ouml;&ouml; pole loodud ressursihalduskeskkonna kaudu, planeerimine pole v&otilde;imalik!");
						return;
					}
				}

				### project states for showing its schedule chart
				$applicable_states = array (
					mrp_case_obj::STATE_PLANNED,
					mrp_case_obj::STATE_INPROGRESS,
					mrp_case_obj::STATE_DONE,
					mrp_case_obj::STATE_ARCHIVED
				);

				if (in_array ($this_object->prop ("state"), $applicable_states))
				{
					$prop["value"] = $this->create_schedule_chart ($arr);
				}
				else
				{
					$prop["value"] = t("Projekt pole plaanis");
				}
				break;

			case "resource_tree":
				$this->create_resource_tree ($arr);
				break;

			case "workflow_toolbar":
				$this->create_workflow_toolbar ($arr);
				break;

			case "workflow_table":
				### project states for updating schedule
				$applicable_states = array (
					mrp_case_obj::STATE_PLANNED,
					mrp_case_obj::STATE_INPROGRESS
				);

				if (in_array ($this_object->prop ("state"), $applicable_states))
				{
					if ($this->workspace)
					{
						### update schedule
						$schedule = new mrp_schedule();
						$schedule->create (array("mrp_workspace" => $this->workspace->id()));
					}
					else
					{
						$prop["value"] = t("T&ouml;&ouml; pole loodud ressursihalduskeskkonna kaudu, planeerimine pole v&otilde;imalik!");
						return;
					}
				}

				$this->create_workflow_table ($arr);
				break;

			case "log":
				$this->_do_log($arr);
				break;

			case "states_chart":
				$c = $arr["prop"]["vcl_inst"];
				$c->set_type(GCHART_PIE_3D);
				$c->set_size(array(
					"width" => 500,
					"height" => 100,
				));
				$c->add_fill(array(
					"area" => GCHART_FILL_BACKGROUND,
					"type" => GCHART_FILL_SOLID,
					"colors" => array(
						"color" => "e9e9e9",
					),
				));
				$data = array();
				$labels = array();
				$colors = array();
				$conns = $this_object->connections_from(array ("type" => "RELTYPE_MRP_PROJECT_JOB", "class_id" => CL_MRP_JOB));
				foreach($conns as $conn)
				{
					$job = $conn->to();
					if(!isset($data[$job->state]))
					{
						$data[$job->state] = 1;
					}
					else
					{
						$data[$job->state]++;
					}
					$colors[$job->state] = strtolower(preg_replace("/[^0-9A-Za-z]/", "", mrp_workspace::$state_colours[$job->state]));
					$labels[$job->state] = $this->states[$job->state]." (".$data[$job->state].")";
				}
				$c->add_data($data);
				$c->set_colors($colors);
				$c->set_labels($labels);
				$c->set_title(array(
					"text" => t("T&ouml;&ouml;d staatuste kaupa"),
					"color" => "666666",
					"size" => 11,
				));
				break;

			case "recources_chart":
				$c = $arr["prop"]["vcl_inst"];
				$c->set_type(GCHART_PIE_3D);
				$c->set_size(array(
					"width" => 500,
					"height" => 100,
				));
				$c->add_fill(array(
					"area" => GCHART_FILL_BACKGROUND,
					"type" => GCHART_FILL_SOLID,
					"colors" => array(
						"color" => "e9e9e9",
					),
				));
				$data = array();
				$labels = array();
				$odl = new object_data_list(
					array(
						"class_id" => CL_MRP_JOB,
						"CL_MRP_JOB.RELTYPE_MRP_PROJECT_JOB(CL_MRP_CASE)" => $this_object->id(),
					),
					array(
						CL_MRP_JOB => array("resource" => "res", "resource(CL_MRP_RESOURCE).name" => "res_nm", "length"),
					)
				);
				foreach($odl->arr() as $oid => $odata)
				{
					$data[$odata["res"]] = $odata["length"];
					$labels[$odata["res"]] = $odata["res_nm"];
				}
				$c->add_data($data);
				$c->set_labels($labels);
				$c->set_title(array(
					"text" => t("Planeeritud kestused ressursside kaupa"),
					"color" => "666666",
					"size" => 11,
				));
				break;

			case "purchasing_manager":
				$ws = $arr["obj_inst"]->prop("workspace");
				if($ws && $whs = $ws->prop("purchasing_manager"))
				{
					$ol = new object_list(array(
						"oid" => $whs,
					));
					$prop["options"] = array_merge(array("" => t("--vali--")), $ol->names());
				}
				break;
		}

		return $retval;
	}

	function _get_materials_tree($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];

		$odl = new object_data_list(
			array(
				"class_id" => CL_MRP_JOB,
				"CL_MRP_JOB.RELTYPE_MRP_PROJECT_JOB(CL_MRP_CASE)" => $arr["obj_inst"]->id(),
				new obj_predicate_sort(array("exec_order" => "ASC")),
			),
			array(
				CL_MRP_JOB => array("resource", "state"),
			)
		);

		$t->add_item(0, array(
			"id" => "all_jobs",
			"name" => t("Materjalid t&ouml;&ouml;de kaupa"),
			"url" => aw_url_change_var("job_id", NULL),
		));

		$msg = t("Valitud t&ouml;&ouml; staatus ei luba materjale lisada!");
		$applicable_states = array(
			mrp_job_obj::STATE_NEW,
			mrp_job_obj::STATE_PLANNED,
			mrp_job_obj::STATE_ABORTED
		);

		foreach($odl->arr() as $jid => $jdata)
		{
			$disabled = !in_array($jdata["state"], $applicable_states);
			$t->add_item("all_jobs", array(
				"id" => $jid,
				"name" => $jdata["name"],
				"url" => aw_url_change_var("job_id", $jid),
				"iconurl" => icons::get_icon_url(CL_MENU),
			));
			foreach(mrp_resource_obj::get_materials(array("id" => $jdata["resource"], "odl" => true))->arr() as $mid => $mdata)
			{
				$t->add_item($jid, array(
					"id" => $jid."_".$mid,
					"name" => $mdata["name"],
					"url" => $disabled ? "javascript:(alert('$msg'))" : "javascript:add_material($mid, $jid)",
					"onclick" => "alert(this.value);",
					"iconurl" => icons::get_icon_url(CL_SHOP_PRODUCT),
					/*"ajax" => array(
						array(
							"prop" => "materials_table",
							"url" => $this->mk_my_orb("", array()),
						)
					),*/
				));
			}
		}

		$t->add_item(0, array(
			"id" => "all_materials",
			"name" => t("Materjalid kategooriate kaupa"),
			"url" => "javascript:void(0)",
		));

		$whi = get_instance(CL_SHOP_WAREHOUSE);
		$resi = get_instance(CL_MRP_RESOURCE);
		$owner = $arr["obj_inst"]->prop("workspace");
		if($owner)
		{
			$whs = $owner->prop("purchasing_manager");
		}

		if(count($whs))
		{
			$arr["warehouses"] = $whs;
			$pt = $whi->get_warehouse_configs($arr, "prod_type_fld");
			$ol = new object_list(array(
				"parent" => $pt,
				"class_id" => CL_SHOP_PRODUCT_CATEGORY,
				"site_id" => array(),
				"lang_id" => array(),
			));
			$this->materials_tree_data = array();
			$this->insert_materials_tree_stuff($ol->names());
			$this->insert_materials_tree_branches($t);
		}
	}

	protected function insert_materials_tree_branches($t)
	{
		foreach($this->materials_tree_data as $pt => $datas)
		{
			foreach($datas as $data)
			{
				$t->add_item($pt, $data);
			}
		}
	}

	protected function insert_materials_tree_stuff($datas)
	{
		foreach($datas as $oid => $data)
		{
			if(!is_array($data))
			{
				$data = array(
					"parent" => "all_materials",
					"name" => $data,
					"class_id" => CL_SHOP_PRODUCT_CATEGORY,
				);
			}

			$this->materials_tree_data[$data["parent"]][$oid] = array(
				"id" => $oid,
				"name" => $data["name"],
				"url" => $data["class_id"] == CL_SHOP_PRODUCT ? "javascript:add_material($oid, 0)" : "javascript:void(0)",
				"iconurl" => icons::get_icon_url(($data["class_id"] == CL_SHOP_PRODUCT) ? $data["class_id"] : CL_MENU),
			);

			if(!empty($data["category"]))
			{
				foreach((array)$data["category"] as $cat)
				{
					$this->materials_tree_data[$cat][$oid] = array(
						"id" => $oid,
						"name" => $data["name"],
						"url" => $data["class_id"] == CL_SHOP_PRODUCT ? "javascript:add_material($oid, 0)" : "javascript:void(0)",
						"iconurl" => icons::get_icon_url(($data["class_id"] == CL_SHOP_PRODUCT) ? $data["class_id"] : CL_MENU),
					);
				}
			}
		}

		$odl = new object_data_list(
			array(
				"class_id" => array(CL_SHOP_PRODUCT, CL_SHOP_PRODUCT_CATEGORY),
				new object_list_filter(array(
					"logic" => "OR",
					"conditions" => array(
						new object_list_filter(array(
							"logic" => "AND",
							"conditions" => array(
								"class_id" => CL_SHOP_PRODUCT,
								"CL_SHOP_PRODUCT.RELTYPE_CATEGORY" => array_keys($datas),
							),
						)),
						"parent" => array_keys($datas),
					),
				))
			),
			array(
				CL_SHOP_PRODUCT => array("class_id", "parent", "CL_SHOP_PRODUCT.RELTYPE_CATEGORY.oid" => "category"),
				CL_SHOP_PRODUCT_CATEGORY => array("class_id", "parent"),
			)
		);
		if($odl->count())
		{
			$this->insert_materials_tree_stuff($odl->arr());
		}
	}

	function _init_materials_table($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];

		$t->define_field(array(
			"name" => "material",
			"caption" => t("Materjal"),
		));
		$t->define_field(array(
			"name" => "amount",
			"caption" => t("Planeeritud kogus"),
			"align"=> "center",
		));
		$t->define_field(array(
			"name" => "unit",
			"caption" => t("&Uuml;hik"),
			"align"=> "center",
		));
		$t->define_field(array(
			"name" => "planning",
			"caption" => t("Tarnetingimus planeerimisel"),
			"align"=> "center",
		));
		$t->define_field(array(
			"name" => "movement",
			"caption" => t("Materjali liikumine materjalilaost"),
			"align"=> "center",
		));
		$t->define_field(array(
			"name" => "job",
			"caption" => t("T&ouml;&ouml;"),
			"align"=> "center",
		));
	}

	function _get_materials_table($arr)
	{
		$this->_init_materials_table($arr);

		$t = &$arr["prop"]["vcl_inst"];

		$jobs = $arr["obj_inst"]->get_job_names();

		$odl = mrp_job_obj::get_material_expenses(array(
			"id" => isset($arr["request"]["job_id"]) ? $arr["request"]["job_id"] : array_keys($jobs),
			"odl" => true,
		));

		$odl_without_job = $arr["obj_inst"]->get_material_expenses_without_job(array(
			"odl" => true,
		));

		$data = $odl->arr() + $odl_without_job->arr();

		$mec_o = obj(null, array(), CL_MATERIAL_EXPENSE_CONDITION);
		$plan_ops = $mec_o->planning_options();
		$move_ops = $mec_o->movement_options();

		$applicable_states = array(
			mrp_job_obj::STATE_NEW,
			mrp_job_obj::STATE_PLANNED,
			mrp_job_obj::STATE_ABORTED
		);

		foreach($data as $oid => $odata)
		{
			$disabled = is_oid($odata["job"]) && !in_array($odata["job.state"], $applicable_states);
			$prod_obj = obj($odata["product"]);
			$t->define_data(array(
				"material" => html::obj_change_url($prod_obj).($disabled ? "" : html::hidden(array(
					"name" => "materials_table[$oid][product]",
					"value" => $odata["product"],
				))),
				"amount" => html::textbox(array(
					"name" => "materials_table[$oid][amount]",
					"size" => 4,
					"value" => $odata["amount"],
					"disabled" => $disabled,
				)),
				"unit" => mrp_job::get_materials_unitselect($prod_obj, $odata["unit"], false, "materials_table[$oid][unit]"),
				"planning" => html::select(array(
					"name" => "materials_table[$oid][planning]",
					"options" => $plan_ops,
					"value" => $odata["planning"],
					"disabled" => $disabled,
				)),
				"movement" => html::select(array(
					"name" => "materials_table[$oid][movement]",
					"options" => $move_ops,
					"value" => $odata["movement"],
					"disabled" => $disabled,
				)),
				"job" => html::select(array(
					"name" => "materials_table[$oid][job]",
					"options" => array("" => t("--vali--")) + $jobs,
					"value" => $odata["job"],
					"disabled" => $disabled,
				)),
			));
		}
	}

	function _set_materials_table($arr)
	{
		$applicable_states = array(
			mrp_job_obj::STATE_NEW,
			mrp_job_obj::STATE_PLANNED,
			mrp_job_obj::STATE_ABORTED
		);

		$material_expenses = array();
		$material_expenses_without_job = array();

		foreach($arr["request"]["materials_table"] as $tmp)
		{
			if(!empty($tmp["job"]))
			{
				$material_expenses[$tmp["job"]]["amount"][$tmp["product"]] = $tmp["amount"];
				$material_expenses[$tmp["job"]]["movement"][$tmp["product"]] = $tmp["movement"];
				$material_expenses[$tmp["job"]]["planning"][$tmp["product"]] = $tmp["planning"];
				$material_expenses[$tmp["job"]]["unit"][$tmp["product"]] = $tmp["unit"];
			}
			else
			{
				$material_expenses_without_job[$tmp["product"]] = $tmp;
			}
		}

		$arr["obj_inst"]->save_materials_without_job($material_expenses_without_job);

		foreach($material_expenses as $jid => $data)
		{
			if($this->can("view", $jid) && in_array(obj($jid)->prop("state"), $applicable_states))
			{
				$job = obj($jid);
				$job->save_materials($data);
			}
		}
	}

	function _get_job_charts_tbl($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->set_titlebar_display(false);
		$t->set_sortable(false);

		$perrow = 3;

		for($i = 0; $i < $perrow; $i++)
		{
			$t->define_field(array(
				"name" => "chart_".$i,
				"align" => "center",
			));
		}

		$odl = new object_data_list(
			array(
				"class_id" => CL_MRP_JOB,
				"CL_MRP_JOB.RELTYPE_MRP_PROJECT_JOB(CL_MRP_CASE)" => $arr["obj_inst"]->id(),
				new obj_predicate_sort(array("exec_order" => "ASC")),
			),
			array(
				CL_MRP_JOB => array("length", "real_length", "length_deviation"),
			)
		);
		$jobs = array_values($odl->arr());

		$j = 0;
		while(isset($jobs[$j]))
		{
			$t_data = array();
			for($i = 0; $i < $perrow && isset($jobs[$j]); $i++)
			{
				$job = $jobs[$j];
				$c = new google_chart();
				$c->set_id($arr["request"]["class"].".".$arr["prop"]["name"].".".$j.".".$arr["obj_inst"]->id());
				$c->set_type(GCHART_BAR_GV);
				$c->set_size(array(
					"width" => 350,
					"height" => 200,
				));
				$c->add_fill(array(
					"area" => GCHART_FILL_BACKGROUND,
					"type" => GCHART_FILL_SOLID,
					"colors" => array(
						"color" => "e1e1e1",
					),
				));
				$plan = $job["length"];
				$real = $job["real_length"];
				$deviation = $job["length_deviation"];
				$real = round($real/3600, 1);
				$plan = round($plan/3600, 1);
				$deviation = round($deviation/3600, 1);
				$c->add_data(array(
					"plan" => $plan,
				));
				$c->add_data(array(
					"real" => $real,
				));
				$c->add_data(array(
					"d" => $deviation,
				));
				$max = max($plan, $real, $deviation);
				$c->set_title(array(
					"text" => $job["name"],
					"color" => "666666",
					"size" => 11,
				));
				$c->set_bar_sizes(array(
					"width" => 70,
					"bar_spacing" => 3,
					"bar_group_spacing" => 8,
				));
				$c->set_legend(array(
					"labels" => array(
						t("Planeeritud"),
						t("Tegelik"),
						$deviation > 0 ? t("&Uuml;lekulu") : t("Alakulu"),
					),
						"position" => GCHART_POSITION_RIGHT,
				));
				$c->add_marker(array(
					"type" => GCHART_MARKER_TEXT,
					"text" => $plan.t(" h"),
					"color" => "000000",
					"dataset" => 0,
					"datapoint" => 0,
					"size" => 11,
					"order" => GCHART_ORDER_TOP
				));
				$c->add_marker(array(
					"type" => GCHART_MARKER_TEXT,
					"text" => $real.t(" h"),
					"color" => "000000",
					"dataset" => 1,
					"datapoint" => 0,
					"size" => 11,
					"order" => GCHART_ORDER_TOP
				));
				$c->add_marker(array(
					"type" => GCHART_MARKER_TEXT,
					"text" => $deviation.sprintf(t(" h (%s %%)"), $plan == 0 && $deviation == 0 ? "0" : ($plan == 0 ? "&#8734;" : round(($deviation/$plan)*100, 1))),
					"color" => "000000",
					"dataset" => 2,
					"datapoint" => 0,
					"size" => 11,
					"order" => GCHART_ORDER_TOP
				));
				$c->set_axis(array(
					GCHART_AXIS_LEFT,
				));
				$c->add_axis_range(0, array($max*-1.1, $max*1.2));
				$c->set_data_scales(array(array(-110, 120)));
				$c->set_colors(array(
					"5b9f44", "996600", $deviation > 0 ? "ff0000" : "00ff00"
				));

				$t_data["chart_".$i] = $c->get_html();
				$j++;
			}
			$t->define_data($t_data);
		}
	}

	function set_property($arr = array())
	{
		if ($this->mrp_error)
		{
			return PROP_FATAL_ERROR;
		}

		$this_object = $arr["obj_inst"];
		$prop =& $arr["prop"];
		$retval = PROP_OK;

		### post rescheduling msg where necessary
		$applicable_planning_states = array(
			mrp_case_obj::STATE_INPROGRESS,
			mrp_case_obj::STATE_PLANNED
		);

		switch ($prop["name"])
		{
			case "due_date":
			case "project_priority":
			case "starttime":
				if ($this->workspace &&  in_array ($this_object->prop ("state"), $applicable_planning_states) and ($this_object->prop ($prop["name"]) != $prop["value"]) )
				{
					$this->workspace->set_prop("rescheduling_needed", 1);
				}
				break;

			case "user_comments":
				$ws = get_instance(CL_MRP_WORKSPACE);
				$ws->mrp_log($arr["obj_inst"]->id(), NULL, "", $prop["value"]["comment"]);
				break;

			case "workspace":
				return PROP_IGNORE;

			case "order_quantity":
				if(empty($prop["value"]) or $prop["value"] < 1)
				{
					$prop["error"] = t("Tellimuse kogus peab olema nullist suurem");
					$retval = PROP_FATAL_ERROR;
				}
				break;

			case "name":
				if(isset($arr["request"]["group"]) and in_array($arr["request"]["group"], array("grp_case_schedule", "grp_case_schedule_gantt", "grp_case_workflow", "grp_case_formula", "grp_case_materials",  "grp_case_view")))
				{
					return PROP_IGNORE;
				}
				// see if any other projects have the same name
				$ol = new object_list(array(
					"class_id" => CL_MRP_CASE,
					"lang_id" => array(),
					"site_id" => array(),
					"name" => $prop["value"]
				));
				if (is_oid($arr["obj_inst"]->id()))
				{
					$ol->remove($arr["obj_inst"]->id());
				}
				if ($ol->count() > 0)
				{
					$prop["error"] = t("Ei tohi olla rohkem kui &uuml;ks sama numbriga projekt!");
					return PROP_FATAL_ERROR;
				}
				break;

			case "customer":
				if(isset($arr["request"]["group"]) and in_array($arr["request"]["group"], array("grp_case_schedule", "grp_case_schedule_gantt", "grp_case_workflow", "grp_case_formula", "grp_case_materials", "grp_case_view")))
				{
					return PROP_IGNORE;
				}
				$arr["obj_inst"]->awobj_set_customer($prop["value"]);
				return PROP_IGNORE;
				break;

			case "workflow_table":
				$save = $this->save_workflow_data ($arr);

				if ($save !== PROP_OK)
				{
					$prop["error"] = $save;
					return PROP_FATAL_ERROR;
				}
				break;
		}

		if (!$arr["new"] and $arr["obj_inst"]->prop($prop["name"]) != $prop["value"] && in_array($prop["type"], array("textbox", "datetime_select")))
		{
			if ($prop["type"] === "textbox")
			{
				$v1 = $arr["obj_inst"]->prop($prop["name"]);
				$v2 = $prop["value"];
			}
			elseif ($prop["type"] === "datetime_select")
			{
				$v1 = date("d.m.Y H:i", $arr["obj_inst"]->prop($prop["name"]));
				$v2 = date("d.m.Y H:i", date_edit::get_timestamp($prop["value"]));
				if ($v1 == $v2)
				{
					return $retval;
				}
			}

			$this->mrp_log(
				$arr["obj_inst"]->id(),
				NULL,
				"Projekti omaduse ".
					$prop["caption"]." v&auml;&auml;rtust muudeti ".
					$v1." => ".$v2
			);
		}

		return $retval;
	}

	function callback_pre_save ($arr)
	{
		$arr["obj_inst"]->set_prop("workspace", obj($arr["request"]["mrp_workspace"]));

		if (is_string ($arr["request"]["mrp_resourcetree_data"]))
		//!!! data_var argument changed in treeview. this here not to be updated until treeview updated in mail.prismaprint.ee
		{
			### create new jobs based on resources chosen from tree
			$added_resources = explode (",", $arr["request"]["mrp_resourcetree_data"]);

			foreach ($added_resources as $resource_id)
			{
				if (is_oid ($resource_id))
				{
					$arr["mrp_new_job_resource"] = $resource_id;
					$this->add_job ($arr);
				}
			}
		}
	}

	/** Get mrp_workspace object associated with this project
		@attrib api=1 params=name
		@param id optional type=oid acl=view
			this object id
		@param obj_inst optional type=object
			this object instance
		@returns mrp_workspace object
	**/
	public function get_current_workspace ($arr)
	{
		if (isset($arr["id"]) and $this->can("view", $arr["id"]))
		{
			$this_object = obj ($arr["id"]);
		}

		if (isset($arr["obj_inst"]) and is_object ($arr["obj_inst"]) and CL_MRP_CASE == $arr["obj_inst"]->class_id())
		{
			$this_object = $arr["obj_inst"];
		}

		$workspace = $this_object->prop("workspace");
		return $workspace;
	}

	function create_schedule_chart($arr)
	{
		$time =  time();
		$this_object = $arr["obj_inst"];
		$chart = get_instance ("vcl/gantt_chart");
		$columns = (int) (!empty($arr["request"]["mrp_chart_length"]) ? $arr["request"]["mrp_chart_length"] : 7);
		$hilighted_project = $this_object->id();
		$workspace = $this->get_current_workspace ($arr);

		### get range start according to project state
		$ol = new object_list(array(
			"class_id" => CL_MRP_JOB,
			"parent" => $workspace->prop ("jobs_folder"),
			"project" => $hilighted_project,
			"exec_order" => 1,
		));
		$o = $ol->begin();

		switch ($this_object->prop ("state"))
		{
			case mrp_case_obj::STATE_INPROGRESS:
			case mrp_case_obj::STATE_DONE:
			case mrp_case_obj::STATE_ARCHIVED:
				$project_start = is_object($o) ? $o->prop("started") : $this_object->prop ("starttime");
				break;

			default:
				$project_start = (is_object($o) and 1 < $o->prop("starttime")) ? $o->prop("starttime") : $this_object->prop ("starttime");
		}

		$range_start = mktime (0, 0, 0, date ("m", $project_start), date ("d", $project_start), date("Y", $project_start));
		$range_start = (int) (!empty($arr["request"]["mrp_chart_start"]) ? $arr["request"]["mrp_chart_start"] : $range_start);

		$range_end = (int) ($range_start + $columns * 86400);
		$hilighted_jobs = array ();

		switch ($columns)
		{
			case 1:
				$subdivisions = 24;
				break;

			default:
				$subdivisions = 3;
		}

		### add row dfn-s, resource names
		$connections = $this_object->connections_from(array("type" => "RELTYPE_MRP_PROJECT_JOB", "class_id" => CL_MRP_JOB));
		$project_resources = array ();

		foreach ($connections as $connection)
		{
			$job = $connection->to();
			$project_resources[] = $job->prop("resource");
		}

		### add rows
		$project_resources = array_unique ($project_resources);
		$mrp_schedule = get_instance(CL_MRP_SCHEDULE);

		foreach ($project_resources as $resource_id)
		{
			if ($this->can("view", $resource_id))
			{
				$resource = obj ($resource_id);
				$chart->add_row (array (
					"name" => $resource_id,
					"title" => $resource->name(),
					"uri" => html::get_change_url(
						$resource_id,
						array("return_url" => get_ru())
					)
				));

				if (empty($arr["request"]["chart_customer"]))
				{
					### add reserved times for resources, cut off past
					$reserved_times = $mrp_schedule->get_unavailable_periods_for_range(array(
						"mrp_resource" => $resource_id,
						"mrp_start" => $range_start,
						"mrp_length" => $range_end - $range_start
					));

					foreach($reserved_times as $rt_start => $rt_end)
					{
						if ($rt_end > $time)
						{
							$rt_start = ($rt_start < $time) ? $time : $rt_start;
							$chart->add_bar(array(
								"row" => $resource_id,
								"start" => $rt_start,
								"length" => $rt_end - $rt_start,
								"nostartmark" => true,
								"colour" => MRP_COLOUR_UNAVAILABLE,
								"url" => "#",
								"layer" => 2,
								"title" => sprintf(t("Kinnine aeg %s - %s"), date(MRP_DATE_FORMAT, $rt_start), date(MRP_DATE_FORMAT, $rt_end))
							));
						}
					}
				}
			}
		}


		### get job id-s for hilighted project if requested
		if ($hilighted_project)
		{
			$list = new object_list (array (
				"class_id" => CL_MRP_JOB,
				"parent" => $workspace->prop ("jobs_folder"),
				"project" => $hilighted_project,
			));
			$hilighted_jobs = $list->ids ();
		}

		$jobs = array ();

		### job states that are shown in chart past
		$applicable_states = array (
			mrp_job_obj::STATE_DONE,
			mrp_job_obj::STATE_INPROGRESS,
			mrp_job_obj::STATE_PAUSED,
			mrp_job_obj::STATE_SHIFT_CHANGE
		);

		$this->db_query (
		"SELECT job.oid,job.project,job.state,job.started,job.finished,job.resource,job.exec_order,schedule.*,o.metadata " .
		"FROM " .
			"mrp_job as job " .
			"LEFT JOIN objects o ON o.oid = job.oid " .
			"LEFT JOIN mrp_schedule schedule ON schedule.oid = job.oid " .
		"WHERE " .
			"job.state IN (" . implode (",", $applicable_states) . ") AND " .
			"o.status > 0 AND " .
			"o.parent = '" . $workspace->prop ("jobs_folder") . "' AND " .
			"((!(job.started < {$range_start})) OR ((job.state = " . mrp_job_obj::STATE_DONE . " AND job.finished > {$range_start}) OR (job.state != " . mrp_job_obj::STATE_DONE . " AND {$time} > {$range_start}))) AND " .
			"job.started < {$range_end} AND " .
			"job.project > 0 AND " .
			"job.length > 0 AND " .
			"job.resource > 0 " .
		"");

		while ($job = $this->db_next())
		{
			if ($this->can("view", $job["oid"]))
			{
				$metadata = aw_unserialize ($job["metadata"]);
				$job["paused_times"] = isset($metadata["paused_times"]) ? $metadata["paused_times"] : array();
				$jobs[] = $job;
			}
		}

		### job states that are shown in chart future
		$applicable_states = array (
			mrp_job_obj::STATE_PLANNED,
			mrp_job_obj::STATE_ABORTED,
		);

		$this->db_query (
		"SELECT job.oid,job.project,job.state,job.started,job.finished,job.resource,job.exec_order,schedule.*,o.metadata " .
		"FROM " .
			"mrp_job as job " .
			"LEFT JOIN objects o ON o.oid = job.oid " .
			"LEFT JOIN mrp_schedule schedule ON schedule.oid = job.oid " .
		"WHERE " .
			"job.state IN (" . implode (",", $applicable_states) . ") AND " .
			"o.status > 0 AND " .
			"o.parent = '" . $workspace->prop ("jobs_folder") . "' AND " .
			"schedule.starttime < {$range_end} AND " .
			"schedule.starttime > {$time} AND " .
			"((!(schedule.starttime < {$range_start})) OR ((schedule.starttime + schedule.planned_length) > {$range_start})) AND " .
			"job.project > 0 AND " .
			"job.length > 0 AND " .
			"job.resource > 0 " .
		"");

		while ($job = $this->db_next())
		{
			if ($this->can("view", $job["oid"]))
			{
				$metadata = aw_unserialize($job["metadata"]);
				$job["paused_times"] = isset($metadata["paused_times"]) ? $metadata["paused_times"] : array();
				$jobs[] = $job;
			}
		}


		foreach ($jobs as $job)
		{
			if (!$this->can("view", $job["project"]))
			{
				continue;
			}

			$project = obj ($job["project"]);

			### project states that are shown in chart
			$applicable_states = array (
				mrp_case_obj::STATE_PLANNED,
				mrp_case_obj::STATE_INPROGRESS,
				mrp_case_obj::STATE_DONE,
				mrp_case_obj::STATE_ARCHIVED
			);

			if (!in_array ($project->prop ("state"), $applicable_states))
			{
				continue;
			}

			### get start&length according to job state
			switch ($job["state"])
			{
				case mrp_job_obj::STATE_DONE:
					$start = $job["started"];
					$length = $job["finished"] - $job["started"];
// /* dbg */ echo date(MRP_DATE_FORMAT, $start) . "-" . date(MRP_DATE_FORMAT, $start + $length) . "<br>";
					break;

				case mrp_job_obj::STATE_PLANNED:
					$start = $job["starttime"];
					$length = $job["planned_length"];
					break;

				case mrp_job_obj::STATE_SHIFT_CHANGE:
				case mrp_job_obj::STATE_PAUSED:
				case mrp_job_obj::STATE_INPROGRESS:
					$start = $job["started"];
					$length = (($start + $job["planned_length"]) < $time) ? ($time - $start) : $job["planned_length"];
					break;
			}

			$resource = obj ($job["resource"]);
			$job_name = $project->name () . "-" . $job["exec_order"] . " - " . $resource->name ();

			### set bar colour
			$colour = mrp_workspace::$state_colours[$job["state"]];
			$colour = in_array ($job["oid"], $hilighted_jobs) ? MRP_COLOUR_HILIGHTED : $colour;

			$bar = array (
				"id" => $job["oid"],
				"row" => $resource->id (),
				"start" => $start,
				"colour" => $colour,
				"length" => $length,
				"layer" => 0,
				"uri" => aw_url_change_var ("mrp_hilight", $project->id ()),
				"title" => $job_name . " (" . date (MRP_DATE_FORMAT, $start) . " - " . date (MRP_DATE_FORMAT, $start + $length) . ")"
/* dbg */ . " [res:" . $resource->id () . " t&ouml;&ouml;:" . $job["oid"] . " proj:" . $project->id () . "]"
			);

			$chart->add_bar ($bar);

			### add paused bars
			foreach(safe_array($job["paused_times"]) as $pd)
			{
				if ($pd["start"] && $pd["end"])
				{
					$bar = array (
						"row" => $resource->id (),
						"start" => $pd["start"],
						"nostartmark" => true,
						"layer" => 1,
						"colour" => mrp_workspace::$state_colours[mrp_job_obj::STATE_PAUSED],
						"length" => ($pd["end"] - $pd["start"]),
						"uri" => aw_url_change_var ("mrp_hilight", $project->id ()),
						"title" => $job_name . ", paus (" . date (MRP_DATE_FORMAT, $pd["start"]) . " - " . date (MRP_DATE_FORMAT, $pd["end"]) . ")"
					);

					$chart->add_bar ($bar);
				}
			}
		}

		### config
		$ws = get_instance(CL_MRP_WORKSPACE);
		$chart->configure_chart (array (
			"chart_id" => "project_schedule_chart",
			"style" => "aw",
			"start" => $range_start,
			"end" => $range_end,
			"columns" => $columns,
			"subdivisions" => $subdivisions,
			"timespans" => $subdivisions,
			"width" => 850,
			"row_height" => 10,
			"caption" => t("Projekt t&ouml;&ouml;voog"),
			"footer" => $ws->draw_colour_legend(),
			"navigation" => $this->create_chart_navigation($arr)
		));

		### define columns
		$i = 0;
		$days = array ("P", "E", "T", "K", "N", "R", "L");

		while ($i < $columns)
		{
			$day_start = ($range_start + ($i * 86400));
			$day = date ("w", $day_start);
			$date = date ("j/m/Y", $day_start);
			$uri = aw_url_change_var ("mrp_chart_length", 1);
			$uri = aw_url_change_var ("mrp_chart_start", $day_start, $uri);
			$chart->define_column (array (
				"col" => ($i + 1),
				"title" => $days[$day] . " - " . $date,
				"uri" => $uri,
			));
			$i++;
		}

		return $chart->draw_chart ();
	}

	function create_chart_navigation ($arr)
	{
		$start = (int) (isset($arr["request"]["mrp_chart_start"]) ? $arr["request"]["mrp_chart_start"] : time ());
		$start_nav = array ();
		$start_uri = aw_url_change_var ("mrp_chart_length", "");
		$start_uri = aw_url_change_var ("mrp_chart_start", "", $start_uri);
		$period_length = (isset ($arr["request"]["mrp_chart_length"]) ? $arr["request"]["mrp_chart_length"] : 7) * 86400;

		$start_nav[] = html::href (array (
			"caption" => t("<< Tagasi"),
			"url" => aw_url_change_var ("mrp_chart_start", ($start - $period_length)),
		));
		$start_nav[] = html::href (array (
			"caption" => t("Algusesse"),
			"url" => $start_uri,
		));
		$start_nav[] = html::href (array (
			"caption" => t("Edasi >>"),
			"url" => aw_url_change_var ("mrp_chart_start", ($start + $period_length + 1)),
		));

		$navigation = '&nbsp;&nbsp;' . implode (" &nbsp;&nbsp; ", $start_nav);
		return $navigation;
	}

	function create_resource_tree ($arr = array ())
	{
		$this_object = $arr["obj_inst"];
		$workspace = $this->get_current_workspace ($arr);
		$resources_folder = $workspace->prop ("resources_folder");

		if (!is_oid($resources_folder))
		{
			$arr["prop"]["value"] = t("Ressursikaust defineerimata");
			return;
		}

		$resource_tree = new object_tree (array (
			"parent" => $resources_folder,
			"class_id" => array (CL_MENU, CL_MRP_RESOURCE),
			"sort_by" => "objects.jrk"
		));

		if (!count($resource_tree->ids()))
		{
			$arr["prop"]["value"] = t("Ressursse pole defineeritud");
			return;
		}

		// don't display MRP_STATUS_RESOURCE_INACTIVE resources. objtree allows currently only simple filtering
		$applicable_states = array(
				MRP_STATUS_RESOURCE_INUSE,
				MRP_STATUS_RESOURCE_AVAILABLE,
				MRP_STATUS_RESOURCE_OUTOFSERVICE
		);
		function mrp_filter_resource_tree($o, $param)
		{
			if (CL_MRP_RESOURCE == $o->class_id() and !in_array($o->prop("state"), $param[1]))
			{
				$param[0]->remove($o->id());
			}
		}
		$resource_tree->foreach_cb(array(
			"func" => "mrp_filter_resource_tree",
			"param" => array($resource_tree, $applicable_states)
		));

		$tree = new treeview();
		$tree = $tree->tree_from_objects (array (
			"tree_opts" => array (
				"type" => TREE_DHTML_WITH_BUTTONS,
				"tree_id" => "resourcetree",
				"persist_state" => true,
				"checkbox_data_var" => "mrp_resourcetree_data"
			),
			"root_item" => obj ($resources_folder),
			"ot" => $resource_tree,
			"var" => "mrp_resource_tree_active_item",
			"checkbox_class_filter" => array (CL_MRP_RESOURCE),
			"no_root_item" => true
		));
		$tree->set_only_one_level_opened(1);

		$arr["prop"]["value"] = $tree->finalize_tree ();
	}

	function create_workflow_toolbar ($arr = array ())
	{
		$this_object = $arr["obj_inst"];
		$toolbar = $arr["prop"]["toolbar"];

		### delete button
		if (isset($arr["request"]["group"]) and in_array($arr["request"]["group"], array("grp_case_workflow", "grp_case_formula", "grp_case_materials")))
		{
			$disabled = false;
		}
		else
		{
			$disabled = true;
		}

		$toolbar->add_button(array(
			"name" => "save",
			"img" => "save.gif",
			"tooltip" => t("Salvesta"),
			"action" => "submit"
			// "disabled" => $disabled,
		));

		$toolbar->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"tooltip" => t("Kustuta valitud t&ouml;&ouml;(d)"),
			"confirm" => t("Kustutada k&otilde;ik valitud t&ouml;&ouml;d?"),
			"action" => "delete",
			"disabled" => $disabled
		));

		$toolbar->add_separator();

		// $toolbar->add_button(array(
			// "name" => "test",
			// "img" => "preview.gif",
			// "tooltip" => t("Testi/hinda valmimisaega"),
			// "action" => "test",
		// ));

		### states for scheduling a project
		$applicable_states = array(
			mrp_case_obj::STATE_NEW,
			mrp_case_obj::STATE_ABORTED,
			mrp_case_obj::STATE_ONHOLD
		);

		if (in_array($this_object->prop("state"), $applicable_states))
		{
			$disabled = false;
		}
		else
		{
			$disabled = true;
		}

		$toolbar->add_button(array(
			"name" => "plan_btn",
			// "img" => "plan.gif",
			"tooltip" => t("Planeeri"),
			"action" => "plan",
			"disabled" => $disabled
		));


		### states for taking a project out of scheduling
		$applicable_states = array(
			mrp_case_obj::STATE_PLANNED
		);

		if (in_array($this_object->prop("state"), $applicable_states))
		{
			$disabled = false;
		}
		else
		{
			$disabled = true;
		}

		$toolbar->add_button(array(
			"name" => "onhold_btn",
			// "img" => "set_on_hold.gif",
			"tooltip" => t("Plaanist v&auml;lja"),
			"action" => "set_on_hold",
			"confirm" => t("Projekt plaanist v&auml;lja?"),
			"disabled" => $disabled
		));

		### states for aborting a project
		$applicable_states = array(
			mrp_case_obj::STATE_INPROGRESS
		);

		if (in_array($this_object->prop("state"), $applicable_states))
		{
			$disabled = false;
		}
		else
		{
			$disabled = true;
		}

		$toolbar->add_button(array(
			"name" => "abort_btn",
			// "img" => "abort.gif",
			"tooltip" => t("Katkesta"),
			"confirm" => t("Katkestada projekt?"),
			"action" => "abort",
			"disabled" => $disabled
		));

		### states for finishing a project
		$applicable_states = array(
			mrp_case_obj::STATE_INPROGRESS
		);

		if (in_array($this_object->prop("state"), $applicable_states))
		{
			$disabled = false;
		}
		else
		{
			$disabled = true;
		}

		$toolbar->add_button(array(
			"name" => "finish_btn",
			// "img" => "finish.gif",
			"tooltip" => t("Valmis"),
			"action" => "finish",
			"confirm" => t("Projekt on t&ouml;&ouml;s. Olete kindel, et soovite m&auml;&auml;rata projekti staatuseks 'valmis' ?"),
			"disabled" => $disabled
		));

		### states for archiving a project
		$applicable_states = array(
			mrp_case_obj::STATE_DONE
		);

		if (in_array($this_object->prop("state"), $applicable_states))
		{
			$disabled = false;
		}
		else
		{
			$disabled = true;
		}

		$toolbar->add_button(array(
			"name" => "archive_btn",
			// "img" => "archive.gif",
			"tooltip" => t("Arhiveeri"),
			"action" => "archive",
			"disabled" => $disabled
		));
	}

	function create_workflow_table ($arr)
	{
		$this_object = $arr["obj_inst"];

		### init. table
		$table = $arr["prop"]["vcl_inst"];
		$table->define_field(array(
			"name" => "exec_order",
			"caption" => t("Nr.")
		));
		$table->define_field(array(
			"name" => "prerequisites",
			"caption" => t("Eel&shy;dus&shy;t&ouml;&ouml;d"),
		));
		$table->define_field(array(
			"name" => "name",
			"caption" => t("Nimi")
		));
		$table->define_field(array(
			"name" => "length",
			"caption" => t("P"),
			"tooltip" => t("Pikkus (h)")
		));
		$table->define_field(array(
			"name" => "pre_buffer",
			"caption" => t("EP"),
			"tooltip" => t("Eel&shy;puh&shy;ver (h)")
		));
		$table->define_field(array(
			"name" => "post_buffer",
			"caption" => t("JP"),
			"tooltip" => t("J&auml;rel&shy;puh&shy;ver (h)")
		));
		$table->define_field(array(
			"name" => "comment",
			"caption" => t("Kommen&shy;taar"),
			"align" => "center"
		));
		$table->define_field(array(
			"name" => "minstart",
			"caption" => t("Min. algusaeg"),
			"align" => "center"
		));
		$table->define_field(array(
			"name" => "status",
			"caption" => t("Staatus")
		));
		$table->define_field(array(
			"name" => "starttime",
			"caption" => t("T&ouml;&ouml;sse")
		));

		if (empty($arr["no_edit"]))
		{
			$table->define_chooser(array(
				"name" => "selection",
				"field" => "job_id",
			));
		}

		$table->set_numeric_field ("exec_order");
		$table->set_default_sortby ("exec_order");
		$table->set_default_sorder ("asc");
		$table->set_caption(t("Projekti t&ouml;&ouml;voog"));

		### define data for each connected job
		$connections = $this_object->connections_from(array ("type" => "RELTYPE_MRP_PROJECT_JOB", "class_id" => CL_MRP_JOB));

		foreach ($connections as $connection)
		{
			$job = $connection->to ();
			$job_id = $job->id ();

			unset($GLOBALS["objects"][$job_id]);//!!! ajutine. et peale planeerimist loetaks t88objektid uuesti sisse.
			$job = obj($job_id);

			$resource_id = $job->prop ("resource");
			if (!$this->can("view", $resource_id))
			{
				continue;
			}
			$resource = obj ($resource_id);
			$disabled = false;

			switch ($job->prop ("state"))
			{
				case mrp_job_obj::STATE_INPROGRESS:
				case mrp_job_obj::STATE_PAUSED:
				case mrp_job_obj::STATE_SHIFT_CHANGE:
				case mrp_job_obj::STATE_DONE:
					$disabled = true;
					break;
			}

			$state = '<span style="color: ' . mrp_workspace::$state_colours[$job->prop ("state")] . ';">' . $this->states[$job->prop ("state")] . '</span>';

			### translate prerequisites from object id-s to execution orders
			try
			{
				$prerequisites = $job->prop ("prerequisites");
			}
			catch (awex_mrp_job_data $e)
			{
				$prerequisites = new object_list();
			}

			$prerequisites_translated = array ();

			foreach ($prerequisites->arr() as $prerequisite_job)
			{
				$prerequisites_translated[] = $prerequisite_job->prop("exec_order");
			}

			$prerequisites = implode (",", $prerequisites_translated);

			### get & process field values
			$resource_name = $resource->name () ? $resource->name () : "...";
			$starttime = $job->prop ("starttime");
			$planned_start = $starttime ? date (MRP_DATE_FORMAT, $starttime) : "Planeerimata";

			if (!empty($arr["no_edit"]))
			{
				$comment = htmlspecialchars($job->prop("comment"));
			}
			else
			{
				$comment = html::textbox(array(
					"name" => "comments[".$job->id()."]",
					"value" => htmlspecialchars($job->prop("comment")),
					"size" => 10,
					"textsize" => "11px"
				));
			}

			$t_length = round ((($job->prop ("length"))/3600), 2);
			$t_pre_buffer = round ((($job->prop ("pre_buffer"))/3600), 2);
			$t_post_buffer = round ((($job->prop ("post_buffer"))/3600), 2);
			$t_minstart = (($job->prop ("minstart")) ? $job->prop ("minstart") : time());
			$job_name = $job->name() ? $job->name () : ($this_object->name() . " - " . $resource_name);

			$table->define_data(array(
				"name" => empty($arr["no_edit"]) ? html::get_change_url(
					$job->id(),
					array("return_url" => get_ru()),
					$job_name
				) : $job_name,
				"length" => empty($arr["no_edit"]) ? html::textbox(array(
					"name" => "mrp_workflow_job-" . $job_id . "-length",
					"size" => "1",
					"textsize" => "11px",
					"value" => $t_length,
					"disabled" => $disabled,
					)
				) : $t_length,
				"pre_buffer" => empty($arr["no_edit"]) ? html::textbox(array(
					"name" => "mrp_workflow_job-" . $job_id . "-pre_buffer",
					"size" => "1",
					"textsize" => "11px",
					"value" => $t_pre_buffer,
					"disabled" => $disabled,
					)
				) : $t_pre_buffer,
				"post_buffer" => empty($arr["no_edit"]) ? html::textbox(array(
					"name" => "mrp_workflow_job-" . $job_id . "-post_buffer",
					"size" => "1",
					"textsize" => "11px",
					"value" => $t_post_buffer,
					"disabled" => $disabled,
					)
				) : $t_post_buffer,
				"prerequisites" => empty($arr["no_edit"]) ? html::textbox(array(
					"name" => "mrp_workflow_job-" . $job_id . "-prerequisites",
					"size" => "5",
					"textsize" => "11px",
					"value" => $prerequisites,
					"disabled" => $disabled,
					)
				) : $prerequisites,
				"minstart" => empty($arr["no_edit"]) ? ('<span style="white-space: nowrap;">' . html::datetime_select(array(
					"name" => "mrp_workflow_job-" . $job_id . "-minstart",
					"value" => $t_minstart,
					"disabled" => $disabled,
					"day" => "text",
					"month" => "text",
					"textsize" => "11px",
					)
				) . '</span>') : date("d.m.Y H:i", $t_minstart),
				"exec_order" => $job->prop ("exec_order"),
				"starttime" => $planned_start,
				"status" => $state,
				"job_id" => $job_id,
				"comment" => $comment,
			));
		}
	}

	function save_workflow_data ($arr)
	{
		$this_object = $arr["obj_inst"];
		$orders = array ();
		$errors = false;
		$connections = $this_object->connections_from(array ("type" => "RELTYPE_MRP_PROJECT_JOB", "class_id" => CL_MRP_JOB));
		$jobs = array ();
		$workflow = array ();

		### non-changeable job states
		$applicable_states = array(
			mrp_job_obj::STATE_INPROGRESS,
			mrp_job_obj::STATE_PAUSED,
			mrp_job_obj::STATE_SHIFT_CHANGE,
			mrp_job_obj::STATE_DONE
		);

		foreach ($connections as $connection)
		{
			$job = $connection->to ();
			$jobs[$job->prop ("exec_order")] = $job->id ();

			### add non-changeable jobs to workflow
			if (in_array ($job->prop ("state"), $applicable_states))
			{
				$prerequisites = $job->prop ("prerequisites")->ids();
				$workflow[$job->id ()] = $prerequisites;
			}
		}

		foreach ($arr["request"] as $name => $value)
		{
			$property = explode ("-", $name);

			if ($property[0] === "mrp_workflow_job")
			{
				if (is_oid ($property[1]))
				{
					$job = obj ($property[1]);
					$property = $property[2];

					if (!in_array ($job->prop ("state"), $applicable_states))
					{
						switch ($property)
						{
							case "prerequisites":
								### translate prerequisites from execution orders to object id-s
								$prerequisites_userdata = explode (",", $value);
								$prerequisites = array ();

								foreach ($prerequisites_userdata as $prerequisite)
								{
									settype ($prerequisite, "integer");
									$job_id = $jobs[$prerequisite];
									$prerequisites[] = (int) $job_id;
								}

								$workflow[$job->id ()] = $prerequisites;
								break;

							case "length":
							case "pre_buffer":
							case "post_buffer":
								$value = aw_math_calc::string2float($value);
								$job->set_prop ($property, (ceil($value * 3600)));
								break;

							case "minstart":
								$minstart = mktime ($value["hour"], $value["minute"], 0, $value["month"], $value["day"], $value["year"]);
								$job->set_prop ("minstart", $minstart);
								break;
						}

						if ($job->comment() != $arr["request"]["comments"][$job->id()])
						{
							$job->set_comment($arr["request"]["comments"][$job->id()]);
							$workspace_i = get_instance(CL_MRP_WORKSPACE);
							$workspace_i->mrp_log($job->prop("project"), $job->id(), t("Lisas kommentaari"), $arr["request"]["comments"][$job->id()]);
						}

						aw_disable_acl();
						$job->save ();
						aw_restore_acl();
					}
				}
				else
				{
					$errors .= t("T&ouml;&ouml; objekti-id katkine");
				}
			}
		}

		if ($errors)
		{
			return $errors;
		}
		else
		{
			$applicable_planning_states = array(
				mrp_case_obj::STATE_INPROGRESS,
				mrp_case_obj::STATE_PLANNED
			);

			if (in_array ($this_object->prop ("state"), $applicable_planning_states))
			{
				### post rescheduling msg
				$workspace = $this_object->prop("workspace");

				if ($workspace)
				{
					$workspace->request_rescheduling();
				}
				else
				{
					return t("Ressursihalduskeskkond defineerimata");
				}
			}

			### check & save workflow
			if (!empty ($workflow))
			{
				$error = $this->order_jobs($arr, $workflow);

				if ($error)
				{
					return $error;
				}
				else
				{
					foreach ($workflow as $job_id => $prerequisites)
					{
						$prerequisites = count($prerequisites) ? new object_list(array("oid" => $prerequisites, "site_id" => array(), "lang_id" => array())) : new object_list();
						$job = obj ($job_id);
						$job->set_prop ("prerequisites", $prerequisites);
						aw_disable_acl();
						$job->save ();
						aw_restore_acl();
					}
				}
			}

			return PROP_OK;
		}
	}

	/**
		@attrib name=order_jobs
		@param oid required type=int
	**/
	function order_jobs ($arr = array (), $workflow = false)
	{
		### get project object
		if (!empty ($arr["oid"]))
		{
			$project = obj ($arr["oid"]);
		}

		if (is_object ($arr["obj_inst"]))
		{
			$project = $arr["obj_inst"];
		}

		$connections = $project->connections_from (array ("type" => "RELTYPE_MRP_PROJECT_JOB", "class_id" => CL_MRP_JOB));

		if (!is_array ($workflow))
		{
			### read project jobs
			$workflow = array ();

			foreach ($connections as $connection)
			{
				$job = $connection->to ();

				### exclude jobs just about to be deleted
				if ($job->prop ("state") != mrp_job_obj::STATE_DELETED)
				{
					$prerequisites = $job->prop ("prerequisites")->ids();
					$workflow[$job->id ()] = $prerequisites;
				}
			}
		}
		elseif (count ($workflow) != count ($connections))
		{
			return t("T&ouml;&ouml;voog ei sisalda k&otilde;iki projekti t&ouml;id");
		}

		foreach ($workflow as $job_id => $prerequisites)
		{
			### throw away erroneous definitions
			foreach ($prerequisites as $key => $prerequisite)
			{
				if (!is_oid ((int) $prerequisite))
				{
					unset ($prerequisites[$key]);
				}
			}

			### explicitly indicate absence of prerequisites
			if (empty ($prerequisites))
			{
				$prerequisites[] = "none";
			}

			$workflow[$job_id] = $prerequisites;
		}

		### sort workflow topologically, halt on cycle
		$jobs = array ();

		$cycle = $this->check_prerequisites_cycle($workflow);
		if($cycle !== false)
		{
			return $cycle;
		}

		foreach ($workflow as $job_id => $prerequisites)
		{
			$degree = 0;
			$nodes = array ($job_id);

			### recursively go through all current job's prerequisites
			do
			{
				if ($degree > count ($workflow))
				{
					return t("T&ouml;&ouml;voog sisaldab ts&uuml;klit!");
				}

				$current_nodes = $nodes;

				foreach ($current_nodes as $current_node)
				{
					if (isset($workflow[$current_node][0]) and $workflow[$current_node][0] !== "none")
					{ ### prerequisites exist for current node
						### add new prerequisites
						$nodes = array_merge ($nodes, $workflow[$current_node]);
					}

					### remove current node from nodes to visit
					$checked_node = array_keys ($nodes, $current_node);
					$checked_node = $checked_node[0];
					unset ($nodes[$checked_node]);
				}

				### increment arc count
				$degree++;
			}
			while (!empty ($nodes));

			$jobs[$degree][] = $job_id;
		}

		### sort by degree
		ksort ($jobs);

		### convert topology to sequence
		$order = array ();

		foreach ($jobs as $degree => $degree_jobs)
		{
			$order = array_merge ($order, $degree_jobs);
		}

		### save job orders
		foreach ($order as $key => $job_id)
		{
			$job = obj ($job_id);
			$job->set_prop ("exec_order", ($key + 1));
			aw_disable_acl();
			$job->save ();
			aw_restore_acl();
		}
	}

	function check_prerequisites_cycle($workflow)
	{
		foreach($workflow as $job_id => $prerequisites)
		{
			foreach($prerequisites as $prerequisite)
			{
				if($prerequisite === "none")
				{
					continue;
				}
				if($this->check_prerequistes_cycle_for_one_job($workflow, $job_id, $prerequisite))
				{
					return t("T&ouml;&ouml;voog sisaldab ts&uuml;klit!");
				}
			}
		}
		return false;
	}

	function check_prerequistes_cycle_for_one_job($workflow, $job_id, $prerequisite)
	{
		### go through the prerequisites to see if any of those is the current job
		if (isset($workflow[$prerequisite]))
		{
			foreach($workflow[$prerequisite] as $_prerequisite)
			{
				if(!isset($workflow[$_prerequisite]))
				{
					continue;
				}
				if($_prerequisite == $job_id)
				{
					return true;
				}
				### go through the prerequisites of the prerequisite to see if any of those is the current job
				return $this->check_prerequistes_cycle_for_one_job($workflow, $job_id, $_prerequisite);
			}
		}
		return false;
	}

/**
    @attrib name=add_job
	@param oid required type=int
**/
	function _add_job ($arr)
	{
		$arr["obj_inst"] = obj ($arr["oid"]);
		$this->add_job ($arr);

		$return_url = $this->mk_my_orb("change", array(
			"id" => $arr["oid"],
			"return_url" => $arr["return_url"],
			"group" => $arr["group"],
			"subgroup" => $arr["subgroup"],
		), "mrp_case");
		return $return_url;
	}

	function add_job ($arr)
	{
		$this_object = $arr["obj_inst"];
		$resource = is_oid($arr["mrp_new_job_resource"]) ? new object($arr["mrp_new_job_resource"]) : null;
		$job = $this_object->add_job($resource);
		return $job->id();
	}

	/**
		@attrib name=delete
	**/
	function delete ($arr)
	{
		$sel = $arr["selection"];

		if (is_array($sel))
		{
			$ol = new object_list(array(
				"oid" => array_keys($sel),
			));

			for ($o = $ol->begin(); !$ol->end(); $o = $ol->next())
			{
				if ($this->can("delete", $o->id()))
				{
					$class = $o->class_id ();
					$o->delete ();
				}
			}

			$arr["oid"] = $arr["id"];
		}

		$return_url = $this->mk_my_orb("change", array(
			"id" => $arr["id"],
			"return_url" => $arr["return_url"],
			"group" => $arr["group"],
			"subgroup" => $arr["subgroup"],
		), "mrp_case"); //echo $return_url;

		return $return_url;
	}

	function on_save_case($arr) // DEPRECATED. nothing changed or written in prisma_import::write_proj() anyway
	{ // save data to prisma server
		$i = get_instance(CL_MRP_PRISMA_IMPORT);  $i->write_proj($arr["oid"]);}

	function mrp_log($proj, $job, $msg, $comment = '')
	{
		$this->db_query("INSERT INTO mrp_log (
					project_id,job_id,uid,tm,message,comment
				)
				values(
					".((int)$proj).",".((int)$job).",'".aw_global_get("uid")."',".time().",'$msg','$comment'
				)
		");
	}

	function _init_log_t(&$t)
	{
		$t->define_field(array(
			"name" => "tm",
			"caption" => t("Millal"),
			"type" => "time",
			"align" => "center",
			"format" => "d.m.Y H:i:s",
			"numeric" => 1,
			"sortable" => 1
		));

		$t->define_field(array(
			"name" => "job_id",
			"caption" => t("T&ouml;&ouml"),
			"align" => "center",
			"sortable" => 1
		));

		$t->define_field(array(
			"name" => "uid",
			"caption" => t("Kasutaja"),
			"align" => "center",
			"sortable" => 1
		));

		$t->define_field(array(
			"name" => "message",
			"caption" => t("sisu"),
			"align" => "center",
			"sortable" => 1
		));

		$t->define_field(array(
			"name" => "comment",
			"caption" => t("Kommentaar"),
			"align" => "center",
			"sortable" => 1
		));
	}

	function _do_log($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_log_t($t);

		$this->db_query("SELECT tm,objects.name as job_id, uid,message,mrp_log.comment as comment FROM mrp_log left join objects on objects.oid = mrp_log.job_id  WHERE project_id = ".$arr["obj_inst"]->id()." ORDER BY tm DESC");
		while ($row = $this->db_next())
		{
			$row["message"] = nl2br($row["message"]);
			$row["comment"] = nl2br($row["comment"]);
			$t->define_data($row);
		}
		$t->set_default_sortby("tm");
		$t->set_default_sorder("desc");
		$t->sort_by();
	}

	function get_header($arr)
	{
		$ws = $arr["obj_inst"]->prop("workspace");

		if ($ws)
		{
			if (is_oid($ws->prop("case_header_controller")) && $this->can("view", $ws->prop("case_header_controller")))
			{
				$ctr = obj($ws->prop("case_header_controller"));
				$i = $ctr->instance();
				$res = $i->eval_controller($ctr->id(), $arr["obj_inst"]);
				return $res;
			}
		}
	}

	function callback_mod_tab($arr)
	{
		if ($arr["id"] === "grp_case_details")
		{
			return false;
		}

		return true;
	}

	function callback_mod_layout(&$arr)
	{
		switch($arr["name"])
		{
			case "states_chart":
				$arr["area_caption"] = sprintf(t("Projekti '%s' t&ouml;&ouml;d staatuste kaupa"), $arr["obj_inst"]->name());
				break;

			case "recources_chart":
				$arr["area_caption"] = sprintf(t("Projekti '%s' planeeritud kestused ressursside kaupa"), $arr["obj_inst"]->name());
				break;

			case "job_charts":
				$arr["area_caption"] = sprintf(t("Projekti '%s' t&ouml;&ouml;de kestuste v&otilde;rdlused"), $arr["obj_inst"]->name());
				break;
		}
		return true;
	}

/**
    @attrib name=start
	@param id required type=int
**/
	function start ($arr)
	{
		$errors = array ();
		$return_url = $this->mk_my_orb("change", array(
			"id" => $arr["id"],
			"return_url" => $arr["return_url"],
			"group" => $arr["group"],
			"subgroup" => $arr["subgroup"],
		), "mrp_case");

		if (is_oid($arr["id"]))
		{
			$project = obj($arr["id"]);
		}
		else
		{
			$errors[] = t("Projekti id vale");
			$errors = (serialize($errors));
			return aw_url_change_var ("errors", $errors, $return_url);
		}

		try
		{
			$project->start();
		}
		catch (awex_mrp_state $e)
		{
			$errors[] = t("Projekti staatus sobimatu");
		}
		catch (Exception $e)
		{
			$errors[] = t("Projekti ei saand alustada");
		}

		if ($errors)
		{
			$errors = (serialize($errors));
			return aw_url_change_var ("errors", $errors, $return_url);
		}
		else
		{
			return $return_url;
		}
	}

/**
    @attrib name=finish
	@param id required type=int
**/
	function finish ($arr)
	{
		$errors = array ();
		$return_url = $this->mk_my_orb("change", array(
			"id" => $arr["id"],
			"return_url" => $arr["return_url"],
			"group" => $arr["group"],
			"subgroup" => $arr["subgroup"],
		), "mrp_case");

		if (is_oid($arr["id"]))
		{
			$project = obj($arr["id"]);
		}
		else
		{
			$errors[] = t("Projekti id vale");
			$errors = (serialize($errors));
			return aw_url_change_var ("errors", $errors, $return_url);
		}

		try
		{
			$project->finish();
		}
		catch (awex_mrp_state $e)
		{
			$errors[] = t("Projekti staatus sobimatu");
		}
		catch (awex_mrp_case_not_completed $e)
		{
			$errors[] = t("Projekti ei saa l&otilde;petada. K&otilde;ik projekti t&ouml;&ouml;d pole valmis");
		}
		catch (Exception $e)
		{
			$errors[] = t("Projekti ei saand l&otilde;petada");
		}

		### ...
		if ($errors)
		{
			$errors = (serialize($errors));
			return aw_url_change_var ("errors", $errors, $return_url);
		}
		else
		{
			return $return_url;
		}
	}

/**
	@attrib name=abort
	@param id required type=int
**/
	function abort ($arr)
	{
		$errors = array ();
		$return_url = $this->mk_my_orb("change", array(
			"id" => $arr["id"],
			"return_url" => $arr["return_url"],
			"group" => $arr["group"],
			"subgroup" => $arr["subgroup"],
		), "mrp_case");

		if (is_oid($arr["id"]))
		{
			$project = obj($arr["id"]);
		}
		else
		{
			$errors[] = t("Projekti id vale");
			$errors = (serialize($errors));
			return aw_url_change_var ("errors", $errors, $return_url);
		}

		try
		{
			$project->abort();
		}
		catch (awex_mrp_state $e)
		{
			$errors[] = t("Projekti staatus sobimatu");
		}
		catch (Exception $e)
		{
			$errors[] = t("Projekti ei saand katkestada");
		}

		if ($errors)
		{
			$errors = (serialize($errors));
			return aw_url_change_var ("errors", $errors, $return_url);
		}
		else
		{
			return $return_url;
		}
	}

/**
    @attrib name=archive
	@param id required type=int
**/
	function archive ($arr)
	{
		$errors = array ();
		$return_url = $this->mk_my_orb("change", array(
			"id" => $arr["id"],
			"return_url" => $arr["return_url"],
			"group" => $arr["group"],
			"subgroup" => $arr["subgroup"],
		), "mrp_case");

		if (is_oid($arr["id"]))
		{
			$project = obj($arr["id"]);
		}
		else
		{
			$errors[] = t("Projekti id vale");
			$errors = (serialize($errors));
			return aw_url_change_var ("errors", $errors, $return_url);
		}

		try
		{
			$project->archive();
		}
		catch (awex_mrp_state $e)
		{
			$errors[] = t("Projekti staatus sobimatu");
		}
		catch (Exception $e)
		{
			$errors[] = t("Projekti ei saand arhiveerida");
		}

		if ($errors)
		{
			$errors = (serialize($errors));
			return aw_url_change_var ("errors", $errors, $return_url);
		}
		else
		{
			return $return_url;
		}
	}

/**
    @attrib name=plan
	@param id required type=int
**/
	function plan ($arr)
	{
		$this->submit($arr);
		$errors = array ();
		$return_url = $this->mk_my_orb("change", array(
			"id" => $arr["id"],
			"return_url" => $arr["return_url"],
			"group" => $arr["group"],
			"subgroup" => $arr["subgroup"],
		), "mrp_case");

		if (is_oid($arr["id"]))
		{
			$project = obj($arr["id"]);
		}
		else
		{
			$errors[] = t("Projekti id vale");
			$errors = (serialize($errors));
			return aw_url_change_var ("errors", $errors, $return_url);
		}

		try
		{
			$project->plan();
		}
		catch (awex_mrp_state $e)
		{
			$errors[] = t("Projekti staatus sobimatu");
		}
		catch (Exception $e)
		{
			$errors[] = t("Projekti ei saand planeerida");
		}

		if ($errors)
		{
			$errors = (serialize($errors));
			return aw_url_change_var ("errors", $errors, $return_url);
		}
		else
		{
			return $return_url;
		}
	}

/**
    @attrib name=set_on_hold
	@param id required type=int
**/
	function set_on_hold ($arr)
	{
		$errors = array();
		$return_url = $this->mk_my_orb("change", array(
			"id" => $arr["id"],
			"return_url" => $arr["return_url"],
			"group" => $arr["group"],
			"subgroup" => $arr["subgroup"],
		), "mrp_case");

		if (is_oid($arr["id"]))
		{
			$project = obj($arr["id"]);
		}
		else
		{
			$errors[] = t("Projekti id vale");
			$errors = (serialize($errors));
			return aw_url_change_var ("errors", $errors, $return_url);
		}

		try
		{
			$project->set_on_hold();
		}
		catch (awex_mrp_state $e)
		{
			$errors[] = t("Projekti staatus sobimatu");
		}
		catch (Exception $e)
		{
			$errors[] = t("Projekti ei saand ootele seada");
		}

		if ($errors)
		{
			$errors = (serialize($errors));
			return aw_url_change_var ("errors", $errors, $return_url);
		}
		else
		{
			return $return_url;
		}
	}

	/** message handler for the MSG_POPUP_SEARCH_CHANGE message so we can create the correct relation
	**/
	function on_popup_search_change ($arr)
	{
		if (!is_oid($arr["oid"]))
		{
			return;
		}

		$o = obj($arr["oid"]);

		foreach($o->connections_from(array("type" => "RELTYPE_MRP_CUSTOMER")) as $c)
		{
			$c->delete();
		}

		if (is_oid($o->prop($arr["prop"])))
		{
			$customer = obj ($o->prop($arr["prop"]));
			$o->connect(array(
				"to" => $customer,
				"reltype" => "RELTYPE_MRP_CUSTOMER"
			));
		}
	}

	function safe_settype_float ($value) // DEPRECATED
	{ return aw_math_calc::string2float($value); }

	function _init_case_view_t(&$t)
	{
		$t->define_field(array(
			"name" => "ord",
			"caption" => t("Jrk"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "pred",
			"caption" => t("Eeldust&ouml;&ouml;d"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "resource",
			"caption" => t("Ressurss"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "state",
			"caption" => t("Staatus"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "length",
			"caption" => t("Kestus"),
		));
			$t->define_field(array(
				"name" => "planned_length",
				"caption" => t("Planeeritud"),
				"align" => "right",
				"parent" => "length",
			));
			$t->define_field(array(
				"name" => "real_len",
				"caption" => t("Tegelik"),
				"align" => "right",
				"parent" => "length",
			));
			$t->define_field(array(
				"name" => "len_dev",
				"caption" => t("H&auml;lve"),
				"align" => "right",
				"parent" => "length",
			));
		$t->define_field(array(
			"name" => "quantity",
			"caption" => t("T&uuml;kiarvestus"),
		));
			$t->define_field(array(
				"name" => "planned_quantity",
				"caption" => t("Planeeritud"),
				"align" => "right",
				"parent" => "quantity",
			));
			$t->define_field(array(
				"name" => "real_quantity",
				"caption" => t("Valmis"),
				"align" => "right",
				"parent" => "quantity",
			));
		$t->define_field(array(
			"name" => "start",
			"caption" => t("Algus"),
			"align" => "center",
			"type" => "time",
			"format" => "d.m.Y H:i"
		));
		$t->define_field(array(
			"name" => "end",
			"caption" => t("L&otilde;pp"),
			"align" => "center",
			"type" => "time",
			"format" => "d.m.Y H:i"
		));
		$t->define_field(array(
			"name" => "employee",
			"caption" => t("T&ouml;&ouml;tajad"),
			"align" => "center"
		));
	}

	function get_case_view_popup($o)
	{
		$pm = get_instance("vcl/popup_menu");
		$pm->begin_menu($o->id());

		### start button
		if($o->can_start())
		{
			$disabled = false;
		}
		else
		{
			$disabled = true;
		}

		$pm->add_item(array(
			"text" => t("Alusta"),
			"link" => $this->mk_my_orb("start", array(
					"id" => $o->id(),
					"return_url" => get_ru()
				), "mrp_job"),
			"disabled" => $disabled
		));

		### done, abort, pause, end_shift buttons
		if($o->prop("state") == mrp_job_obj::STATE_INPROGRESS)
		{
			$disabled_inprogress = false;
		}
		else
		{
			$disabled_inprogress = true;
		}

		foreach(obj($o->resource)->prop("production_feedback_option_values") as $value)
		{
			if(empty($value))
			{
				continue;
			}

			$url = $this->mk_my_orb("done", array(
				"id" => $o->id(),
				"quantity" => $value * $o->prop("batch_size"),
				"return_url" => get_ru()
			), "mrp_job");

			$pm->add_item(array(
				"text" => $value . ($value == 1 ? t(" partii") : t(" partiid")),
				"link" => $url,
				"disabled" => $disabled_inprogress
			));
		}

		$pm->add_item(array(
			"text" => t("Valmis"),
			"link" => $this->mk_my_orb("done", array(
					"id" => $o->id(),
					"return_url" => get_ru()
				), "mrp_job"),
			"disabled" => $disabled_inprogress
		));
		$pm->add_item(array(
			"text" => t("Paus"),
			"link" => $this->mk_my_orb("pause", array(
					"id" => $o->id(),
					"return_url" => get_ru()
				), "mrp_job"),
			"disabled" => $disabled_inprogress
		));
		$pm->add_item(array(
			"text" => t("Vahetuse l&otilde;pp"),
			"link" => $this->mk_my_orb("end_shift", array(
					"id" => $o->id(),
					"return_url" => get_ru()
				), "mrp_job"),
			"disabled" => $disabled_inprogress
		));

		### continue button
		if ($o->prop("state") == mrp_job_obj::STATE_PAUSED || $o->prop("state") == mrp_job_obj::STATE_SHIFT_CHANGE)
		{
			$disabled = false;
			$action = "scontinue";
		}
		elseif ($o->prop("state") == mrp_job_obj::STATE_ABORTED)
		{
			$disabled = false;
			$action = "acontinue";
		}
		else
		{
			$action = "";
			$disabled = true;
		}

		$pm->add_item(array(
			"text" => t("J&auml;tka"),
			"link" => $this->mk_my_orb($action, array(
					"id" => $o->id(),
					"return_url" => get_ru()
				), "mrp_job"),
			"disabled" => $disabled,
		));

		return $pm;
	}

	function _get_case_view($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_case_view_t($t);

		$this_object = $arr["obj_inst"];
		$connections = $this_object->connections_from(array ("type" => "RELTYPE_MRP_PROJECT_JOB", "class_id" => CL_MRP_JOB));

		$job_ids = array(-1);
		foreach($connections as $connection)
		{
			$job_ids[] = $connection->prop("to");
		}

		foreach ($connections as $connection)
		{
			$job = $connection->to ();
			$job_id = $job->id ();
			$resource_id = $job->prop ("resource");
			$resource = obj ($resource_id);

			$popup_menu = $this->get_case_view_popup($job);
			$state = $popup_menu->get_menu(array(
				"text" => '<span style="color: ' . mrp_workspace::$state_colours[$job->prop ("state")] . ';">' . $this->states[$job->prop ("state")] . '</span>'
			));


			### translate prerequisites from object id-s to execution orders
			$prerequisites_translated = array ();
			$prerequisites = $job->prop("prerequisites")->arr();

			foreach ($prerequisites as $prerequisite_job)
			{
				$prerequisites_translated[] = $prerequisite_job->prop ("exec_order");
			}

			$prerequisites = implode (",", $prerequisites_translated);

			### get & process field values
			$resource_name = $resource->name () ? $resource->name () : "...";
			$employees_ol = new object_list(array(
				"class_id" => CL_CRM_PERSON,
				"lang_id" => array(),
				"site_id" => array(),
				"CL_CRM_PERSON.RELTYPE_PERSON(CL_MRP_JOB).oid" => $job->id(),
			));
			$employees = $employees_ol->names();

			$t->define_data(array(
				"ord" => $job->prop("exec_order"),
				"pred" => $prerequisites,
				"resource" => $resource_name,
				"state" => $state,
				"planned_length" => round($job->prop("planned_length") / 3600.0, 2),
				"real_len" => round($job->prop("real_length") / 3600.0, 2),
				"planned_quantity" => (int)$job->prop("component_quantity") * (int)$this_object->prop("order_quantity"),
				"real_quantity" => (int)$job->prop("done"),
				"len_dev" => round($job->prop("length_deviation") / 3600.0, 2).sprintf(t(" (%s&nbsp;%%)"), $job->prop("planned_length") == 0 && $job->prop("length_deviation") == 0 ? "0" : ($job->prop("planned_length") == 0 ? "&#8734;" : round(($job->prop("length_deviation")/$job->prop("planned_length"))*100, 2))),
				"start" => $job->prop("started"),
				"end" => $job->prop("finished"),
				"employee" => implode(", ", $employees),
			));
		}
		$t->set_numeric_field("ord");
		$t->set_default_sortby("ord");
		$t->sort_by();
	}

	function do_db_upgrade($table, $field, $q, $err)
	{
		if ("mrp_case" === $table)
		{
			ini_set("ignore_user_abort", "1");

			switch($field)
			{
				case "aw_trykiarv":
					$this->db_add_col($table, array(
						"name" => $field,
						"type" => "INT(11) UNSIGNED"
					));
					$ol = new object_list(array(
						"class_id" => CL_MRP_CASE,
						"lang_id" => array(),
						"site_id" => array(),
					));
					foreach($ol->arr() as $oid => $o)
					{
						$v = $o->meta("trykiarv");
						$this->db_query("UPDATE mrp_case SET aw_trykiarv = '$v' WHERE oid = '$oid' LIMIT 1;");
					}
					return true;

				case "order_quantity":
					$this->db_add_col($table, array(
						"name" => "order_quantity",
						"type" => "INT(11) UNSIGNED NOT NULL"
					));
					aw_disable_acl();
					$prjs = new object_list(array(
						"class_id" => CL_MRP_CASE,
						"status" => new obj_predicate_compare(OBJ_COMP_GREATER, 0)
					));
					for ($prj = $prjs->begin (); !$prjs->end (); $prj = $prjs->next ())
					{
						if ($prj->prop("trykiarv"))
						{
							$prj->set_prop("order_quantity", $prj->prop("trykiarv"));
						}
						else
						{
							$prj->set_prop("order_quantity", 1);
						}
					}
					$prjs->save();
					aw_restore_acl();
					return true;

				case "purchasing_manager":
				case "workspace":
				case "customer_relation":
				case "finished":
				case "archived":
				case "started":
					$this->db_add_col($table, array(
						"name" => $field,
						"type" => "INT(11) UNSIGNED"
					));
					return true;
			}
		}
	}

	/**
		@attrib name=add_material params=name all_args=1
	**/
	public function add_material($arr)
	{
		$t = new vcl_table();
		$t->set_titlebar_display(false);
		$this->_init_materials_table(array(
			"prop" => array(
				"vcl_inst" => &$t,
			),
		));

		/*
		$material_expense = obj();
		$material_expense->set_class_id(CL_MATERIAL_EXPENSE);
		$oid = $material_expense->save();
		*/

		$mec_o = obj(null, array(), CL_MATERIAL_EXPENSE_CONDITION);
		$plan_ops = $mec_o->planning_options();
		$move_ops = $mec_o->movement_options();

		$jobs = obj($arr["id"])->get_job_names();

		$prod_obj = obj($arr["material_id"]);

		$id = $arr["job_id"]."_".$arr["material_id"];

		$t->define_data(array(
			"material" => html::obj_change_url($prod_obj).html::hidden(array(
				"name" => "materials_table[$id][product]",
				"value" => $arr["material_id"],
			)),
			"amount" => html::textbox(array(
				"name" => "materials_table[$id][amount]",
				"size" => 4,
			)),
			"unit" => mrp_job::get_materials_unitselect($prod_obj, NULL, false, "materials_table[$id][unit]"),
			"planning" => html::select(array(
				"name" => "materials_table[$id][planning]",
				"options" => $plan_ops,
			)),
			"movement" => html::select(array(
				"name" => "materials_table[$id][movement]",
				"options" => $move_ops,
			)),
			"job" => html::select(array(
				"name" => "materials_table[$id][job]",
				"options" => array("" => t("--vali--")) + $jobs,
				"value" => $arr["job_id"],
			)),
		));
		$ret = $t->get_html(true);
		$ret = str_replace(array("<table border='0' width='100%' cellspacing='1' cellpadding='3' class='awmenuedittabletag'>", "</table>"), "", $ret);
		die($ret);
	}

	function callback_generate_scripts($arr)
	{
		return '
function add_material(mid, jid)
{
	$("div[name=\'materials_table\']").children().children().children().children().each(function()
	{
		o = $(this);
		$.ajax({
			url: "'.$this->mk_my_orb("add_material", array("id" => $arr["request"]["id"])).'",
			data: "material_id="+mid+"&job_id="+jid,
			success: function(html)
			{
				o.append(html);
			}
		});
	});
}
		';
	}
}

?>
