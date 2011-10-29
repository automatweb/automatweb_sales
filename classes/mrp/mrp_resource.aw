<?php
/*

@classinfo relationmgr=yes no_status=1 prop_cb=1 confirm_save_data=1

	@groupinfo grp_general2 caption="Andmed" parent=general
	@groupinfo grp_resource_settings caption="Seaded" parent=general
	@groupinfo grp_resource_ability caption="J&otilde;udlus" parent=general
@groupinfo grp_resource_schedule caption="Kalender"
@groupinfo grp_resource_gantt caption="T&ouml;&ouml;voog" submit=no
@groupinfo grp_resource_joblist caption="T&ouml;&ouml;leht" submit=no
	@groupinfo grp_resource_joblist_todo caption="Eesseisvad t&ouml;&ouml;d" submit=no parent=grp_resource_joblist
	@groupinfo grp_resource_joblist_done caption="Tehtud t&ouml;&ouml;d" submit=no parent=grp_resource_joblist
	@groupinfo grp_resource_joblist_aborted caption="Katkestatud t&ouml;&ouml;d" submit=no parent=grp_resource_joblist
	@groupinfo grp_resource_joblist_aborted caption="Katkestatud t&ouml;&ouml;d" submit=no parent=grp_resource_joblist
@groupinfo grp_resource_maintenance caption="Hooldus"
@groupinfo grp_resource_unavailable caption="T&ouml;&ouml;ajad"
	@groupinfo grp_resource_unavailable_work caption="T&ouml;&ouml;ajad" parent=grp_resource_unavailable
	@groupinfo grp_resource_unavailable_una caption="Kinnised ajad" parent=grp_resource_unavailable
@groupinfo grp_resource_materials caption="Materjalid"
	@groupinfo grp_resource_materials_report caption="Aruanne" parent=grp_resource_materials
	@groupinfo grp_resource_materials_config caption="Seaded" submit=no confirm_save_data=1 parent=grp_resource_materials
@groupinfo grp_resource_operators caption="Inimesed" submit=no confirm_save_data=1

@default table=objects

@default group=grp_general2
	@property name type=textbox
	@caption Nimi

	@property comment type=textbox
	@caption Kommentaar

@default field=meta
@default method=serialize
	@property workspace type=hidden editonly=1

	@property state type=text group=grp_general2,grp_resource_maintenance,grp_resource_settings
	@caption Ressursi staatus

@default group=grp_general2
	@property category type=text editonly=1
	@caption Kategooria

@default group=grp_resource_schedule
	@property resource_calendar type=text store=no no_caption=1
	@caption T&ouml;&ouml;d

@default group=grp_resource_gantt
	@property resource_gantt type=text store=no no_caption=1
	@caption T&ouml;&ouml;d

@default group=grp_resource_joblist_todo,grp_resource_joblist_done,grp_resource_joblist_aborted
	@layout job_list type=hbox width=20%:80%
		@layout job_forest type=vbox parent=job_list
			@layout job_time_tree type=vbox area_caption=Vali&nbsp;kuvatavate&nbsp;t&ouml;&ouml;de&nbsp;ajavahemik parent=job_forest
				@property job_time_tree type=treeview store=no no_caption=1 parent=job_time_tree
			@layout job_client_tree type=vbox area_caption=Vali&nbsp;klient parent=job_forest
				@property job_client_tree type=treeview store=no no_caption=1 parent=job_client_tree
		@layout job_list_right type=vbox parent=job_list
			@layout resource_deviation_chart type=vbox closeable=1 area_caption=Ressursi&nbsp;h&auml;lbe&nbsp;muutus&nbsp;ajas parent=job_list_right
				@property resource_deviation_chart type=google_chart no_caption=1 parent=resource_deviation_chart store=no
			@layout job_list_table type=vbox area_caption=&nbsp; parent=job_list_right
				@property job_list type=table store=no editonly=1 no_caption=1 parent=job_list_table

@default group=grp_resource_maintenance
	@property out_of_service type=checkbox store=no ch_value=1
	@caption Ressurss hoolduses

	@property maintenance_history type=comments
	@caption Hoolduskommentaarid


@default group=grp_resource_settings
	@property type type=select
	@caption T&uuml;&uuml;p

	@property thread_data type=textbox default=1
	@comment Positiivne t&auml;isarv
	@caption Samaaegseid t&ouml;id enim

	@property default_pre_buffer type=textbox
	@caption Vaikimisi eelpuhveraeg (h)

	@property default_post_buffer type=textbox
	@caption Vaikimisi j&auml;relpuhveraeg (h)

	@property global_buffer type=textbox default=14400
	@caption P&auml;eva &uuml;ldpuhver (h)

	@property default_batch_size type=textbox default=1 datatype=int
	@comment Mitu eksemplari valmib sel ressursil korraga v6i mitut soovitakse k&auml;sitleda partiina. Partii suurus.
	@caption T&ouml;&ouml;tluspartii normsuurus

	@property default_min_batches_to_continue_wf type=textbox default=0
	@comment Mitu partiid peab olema valmis, et selle t&ouml;&ouml;tlemist saaks j&auml;tkata j&auml;rgmisel ressursil. 0 - kogu tootmistellimus, t&auml;isarv - kogus. T&auml;isarv, mille j&auml;rel on protsendim&auml;rk - mitu protsenti tootmistellimuse mahust peab olema valmis (n&auml;iteks 20%)
	@caption T&ouml;&ouml;tluspartiisid t&ouml;&ouml;voo j&auml;tkamiseks

	@property production_feedback_option_values type=textarea rows=5
	@comment Valikud valiminud partiide arvu sisestamiseks t&ouml;&ouml; vaates (ressursioperaatori vaates). T&auml;isarvud, koguste puhul ratsionaalarvud.
	@caption Tootmise tagasiside valikud

	@property products type=relpicker multiple=1 reltype=RELTYPE_PRODUCT store=connect
	@caption Ressursil valmistatavad tooted

	@property error_pct type=textbox size=5 field=meta method=serialize
	@caption Paberi raiskamise %


@default group=grp_resource_unavailable_work

	@property work_hrs_recur type=releditor reltype=RELTYPE_RECUR_WRK mode=manager props=name,start,end,time,length table_fields=name,start,end,time,length
	@caption T&ouml;&ouml;ajad

@default group=grp_resource_unavailable_una

	@property unavailable_recur type=releditor reltype=RELTYPE_RECUR use_form=emb mode=manager props=name,start,end,time,length,recur_type,interval_daily,interval_weekly,interval_yearly table_fields=name,start,end,time,length,recur_type
	@caption Kinnised ajad

	@property unavailable_weekends type=checkbox ch_value=1
	@caption Ei t&ouml;&ouml;ta n&auml;dalavahetustel

	@property unavailable_dates type=textarea rows=5 cols=50
	@comment Formaat: alguskuup&auml;ev.kuu, tund:minut - l&otilde;ppkuup&auml;ev.kuu, tund:minut; alguskuup&auml;ev.kuu, ...
	@caption Kinnised p&auml;evad (Formaat: <span style="white-space: nowrap;">p.k, h:m - p.k, h:m;</span><br /><span style="white-space: nowrap;">p.k, h:m - p.k, h:m;</span><br /> ...)

@default group=grp_resource_materials_config

	@property materials_tb type=toolbar no_caption=1

		@layout materials_split type=hbox width=25%:75%

			@layout materials_tree_box parent=materials_split type=vbox area_caption=Materjalid closeable=1

				@property materials_tree type=treeview parent=materials_tree_box no_caption=1

		@layout materials_split_right parent=materials_split type=vbox

			 @property materials_sel_tbl type=table parent=materials_split_right no_caption=1

@default group=grp_resource_materials_report

	@layout materials_report_split type=hbox width=25%:75%

		@layout materials_report_left parent=materials_report_split type=vbox

			@layout materials_report_time_tree type=vbox parent=materials_report_left area_caption=Vali&nbsp;ajavahemik closeable=1

				@property materials_report_time_tree type=treeview store=no no_caption=1 parent=materials_report_time_tree

			@layout materials_report_materials_tree type=vbox parent=materials_report_left area_caption=Vali&nbsp;materjalid closeable=1

				@property materials_report_materials_tree type=treeview store=no no_caption=1 parent=materials_report_materials_tree

		@layout materials_report_right parent=materials_report_split type=vbox

			@property materials_report_table type=table store=no parent=materials_report_right

@default group=grp_resource_operators

	@property operators_tlb type=toolbar no_caption=1 store=no

	@property operators_tbl type=table no_caption=1 store=no
	@caption Ressursi operaatorid

@groupinfo transl caption=T&otilde;lgi
@default group=transl

	@property transl type=callback callback=callback_get_transl store=no
	@caption T&otilde;lgi


@default group=grp_resource_ability

	@property ability_toolbar type=toolbar store=no no_caption=1
	@caption Ressursi j&otilde;udluse toolbar

	@property ability_tbl type=table store=no no_caption=1
	@caption Ressursi j&otilde;udluse tabel

// --------------- RELATION TYPES ---------------------

@reltype MRP_SCHEDULE value=2 clid=CL_PLANNER
@caption Ressursi kalender

//////// DEPRECATED /////////
@reltype MRP_OWNER value=3 clid=CL_MRP_WORKSPACE
@caption Ressursi omanik
/////////////////////////////////

@reltype RECUR value=4 clid=CL_RECURRENCE
@caption Kordus

@reltype RECUR_WRK value=5 clid=CL_RECURRENCE
@caption T&ouml;&ouml;aja kordus

@reltype PRODUCT value=6 clid=CL_SHOP_PRODUCT
@caption Ressursil valmistatav toode

@reltype RESOURCE_ABILITY_ENTRY value=7 clid=CL_MRP_RESOURCE_ABILITY
@caption Ressursi j&otilde;udluse kirje

@reltype CONTAINING_OBJECT value=8
@caption Ressurssi kasutav objekt

*/

require_once "mrp_header.aw";

class mrp_resource extends class_base
{
	private $mrp_error = false;
	private $workspace;

	function mrp_resource()
	{
		$this->resource_states = array(
			0 => "M&auml;&auml;ramata",
			mrp_resource_obj::STATE_AVAILABLE => t("Vaba"),
			mrp_resource_obj::STATE_UNAVAILABLE => t("Kasutusel"),
			mrp_resource_obj::STATE_OUTOFSERVICE => t("Suletud"),
			mrp_resource_obj::STATE_INACTIVE => t("Arhiveeritud")
		);

		$this->states = array (
			MRP_STATUS_NEW => t("Uus"),
			MRP_STATUS_PLANNED => t("Planeeritud"),
			MRP_STATUS_INPROGRESS => t("T&ouml;&ouml;s"),
			MRP_STATUS_ABORTED => t("Katkestatud"),
			MRP_STATUS_DONE => t("Valmis"),
			MRP_STATUS_LOCKED => t("Lukustatud"),
			MRP_STATUS_PAUSED => t("Paus"),
			MRP_STATUS_SHIFT_CHANGE => t("Paus"),
			MRP_STATUS_DELETED => t("Kustutatud"),
			MRP_STATUS_ONHOLD => t("Plaanist v&auml;ljas"),
			MRP_STATUS_ARCHIVED => t("Arhiveeritud")
		);

		$this->trans_props = array(
			"name", "comment"
		);

		$this->init(array(
			"tpldir" => "mrp/mrp_resource",
			"clid" => CL_MRP_RESOURCE
		));
	}

	function callback_on_load ($arr)
	{
		if (!isset($arr["request"]["id"]) or !is_oid($arr["request"]["id"]))
		{
			if (is_oid ($arr["request"]["mrp_workspace"]))
			{
				$this->workspace = obj ($arr["request"]["mrp_workspace"], array(), CL_MRP_WORKSPACE);
			}
			else
			{
				$this->show_error_text = t("Uut ressurssi saab luua vaid ressursihalduskeskkonnast.");
			}
		}
		else
		{
			$this_object = obj ($arr["request"]["id"], array(), CL_MRP_RESOURCE);
			$this->workspace = $this_object->prop("workspace");

			if (!$this->workspace)
			{
				$this->show_error_text = t("Ressurss ei kuulu &uuml;hessegi ressursihalduss&uuml;steemi.");
			}
		}

		if ($this->mrp_error)
		{
			echo t("Viga! ") . $this->mrp_error;
		}
	}

	function get_property($arr)
	{
		if ($this->mrp_error)
		{
			return PROP_IGNORE;
		}

		$prop = &$arr["prop"];
		$retval = PROP_OK;
		$this_object = $arr["obj_inst"];

		switch($prop["name"])
		{
			case "production_feedback_option_values":
				$prop["value"] = implode(", ", $prop["value"]);
				break;

			case "category":
				$resources_folder_id = $this->workspace->prop ("resources_folder");
				$parent_folder_id = $this_object->parent();
				$parents = "";

				while ($resources_folder_id and $parent_folder_id and ($parent_folder_id != $resources_folder_id))
				{
					$parent = obj ($parent_folder_id);
					$parents = "/" . $parent->name () . $parents;
					$parent_folder_id = $parent->parent ();
				}

				$prop["value"] = t("/Ressursid") . $parents;
				break;

			case "resource_calendar":
				### update schedule
				$schedule = new mrp_schedule();
				$schedule->create (array("mrp_workspace" => $this->workspace->id()));

				$prop["value"] = $this->create_resource_calendar ($arr);
				break;

			case "type":
				$prop["options"] = $this_object->get_type_options();
				break;

			case "state":
				list($prop["value"], $num_jobs) = $this->get_resource_state($arr["obj_inst"]);
				$prop["value"] = $this->resource_states[$prop["value"]]." (".$num_jobs.")";
				break;

			case "out_of_service":
				switch ($this_object->prop("state"))
				{
					case mrp_resource_obj::STATE_UNAVAILABLE:
						$prop["disabled"] = true;
						break;

					case mrp_resource_obj::STATE_AVAILABLE:
						$prop["value"] = 0;
						break;

					case mrp_resource_obj::STATE_OUTOFSERVICE:
						$prop["value"] = 1;
						break;
				}
				break;

			case "job_list":
				### update schedule
				$schedule = new mrp_schedule();
				$schedule->create (array("mrp_workspace" => $this->workspace->id()));

				if($arr["request"]["group"] === "grp_resource_joblist_aborted")
				{
					$this->_get_job_list_aborted($arr);
				}
				else
				{
					$this->create_job_list_table ($arr);
				}
				break;

			case "default_pre_buffer":
			case "default_post_buffer":
			case "global_buffer":
				$prop["value"] = isset($prop["value"]) ? ($prop["value"] / 3600) : 0;
				break;

			case "materials_tb":
				$this->mk_materials_tb($arr);
				break;

			case "materials_tree":
				$this->mk_materials_tree($arr);
				break;

			case "materials_sel_tbl":
				$this->mk_materials_sel_tbl($arr);
				break;
		}

		return $retval;
	}

	function get_resource_state($resource)
	{
		if (!is_oid($resource->id()))
		{
			return array(0, 0);
		}

		if ($resource->prop("state") == mrp_resource_obj::STATE_OUTOFSERVICE)
		{
			return array(mrp_resource_obj::STATE_OUTOFSERVICE, 0);
		}

		if ($resource->prop("state") == mrp_resource_obj::STATE_INACTIVE)
		{
			return array(mrp_resource_obj::STATE_INACTIVE, 0);
		}

		$max_jobs = $resource->prop("thread_data");
		$available = $resource->is_available();
		if (!$available)
		{
			return array(mrp_resource_obj::STATE_UNAVAILABLE, $max_jobs);
		}
		return array(mrp_resource_obj::STATE_AVAILABLE, $max_jobs-$available);
	}

	function callback_mod_reforb (&$arr)
	{
		if ($this->workspace)
		{
			$arr["mrp_workspace"] = $this->workspace->id ();
		}

		if($arr["group"] === "grp_resource_materials_config")
		{
			$arr["pgtf"] = automatweb::$request->arg("pgtf");
			$arr["add_ids"] = "";
 		}

		$arr["post_ru"] = get_ru();
	}

	function callback_mod_retval($arr)
	{
		if(isset($arr["request"]["pgtf"]))
		{
			$arr["args"]["pgtf"] = $arr["request"]["pgtf"];
		}
	}

	function set_property ($arr = array ())
	{
		if ($this->mrp_error)
		{
			return PROP_FATAL_ERROR;
		}

		$prop = &$arr["prop"];
		$retval = PROP_OK;
		$this_object = $arr["obj_inst"];

		switch ($prop["group"])
		{
			case "grp_resource_unavailable_work":
			case "grp_resource_unavailable_una":
			case "grp_resource_unavailable":
				$this->workspace->request_rescheduling();
				break;
		}

		switch ($prop["name"])
		{
			case "workspace":
				$prop["value"] = $arr["obj_inst"]->prop("workspace");
				break;

			case "default_pre_buffer":
			case "default_post_buffer":
			case "global_buffer":
				$prop["value"] = round ($prop["value"] * 3600);
				break;

			case "production_feedback_option_values":
				$strchrs = str_split($prop["value"]);
				$nrs = array();
				$i = 0;
				$nrs[$i] = "";
				foreach ($strchrs as $chr)
				{
					if (is_numeric($chr))
					{
						$nrs[$i] .= $chr;
					}
					elseif(strlen($nrs[$i]))
					{
						++$i;
						$nrs[$i] = "";
					}
				}
				$prop["value"] = $nrs;
				break;

			case "maintenance_history":
				if (strlen(trim($prop["value"]["comment"])) < 2)
				{
					$retval = PROP_IGNORE;
				}
				break;

			case "work_hrs_recur":
				if (($arr["request"]["work_hrs_recur_action"] !== "delete") and is_array ($prop["value"]))
				{
					$prop["value"]["recur_type"] = recurrence::RECUR_DAILY;
					$prop["value"]["interval_daily"] = 1;
				}

			case "unavailable_recur":
				if (($arr["request"]["work_hrs_recur_action"] !== "delete") and ($arr["request"]["unavailable_recur_action"] !== "delete") and is_array ($prop["value"]))
				{
					$applicable_types = array (
						recurrence::RECUR_DAILY,
						recurrence::RECUR_WEEKLY,
						recurrence::RECUR_YEARLY
					);

					if (!in_array ($prop["value"]["recur_type"], $applicable_types))
					{
						$prop["error"] .= t("Seda t&uuml;&uuml;pi kordust ei saa kasutada. ") . MRP_NEWLINE;
					}

					### validate
					if (empty ($prop["value"]["time"]))
					{
						$prop["value"]["time"] = "00:00";
					}

					$time = explode (":", $prop["value"]["time"]);
					$time_h = abs ((int) $time[0]);
					$time_min = abs ((int) $time[1]);

					### check for user errors
					if ((23 < $time_h) or (59 < $time_min) or (count ($time) < 2))
					{
						$prop["error"] .= t("Viga kellaaja m&auml;&auml;rangus. ") . MRP_NEWLINE;
					}

					$interval_daily = $prop["value"]["interval_daily"] ? $prop["value"]["interval_daily"] : 1;
					$interval_weekly = $prop["value"]["interval_weekly"] ? $prop["value"]["interval_daily"] : 1;
					$interval_yearly = $prop["value"]["interval_yearly"] ? $prop["value"]["interval_daily"] : 1;

					if (
						((recurrence::RECUR_DAILY == $prop["value"]["recur_type"]) and ((24*$interval_daily) < $prop["value"]["length"]))
						or ((recurrence::RECUR_WEEKLY == $prop["value"]["recur_type"]) and ((24*7*$interval_weekly) < $prop["value"]["length"]))
						or ((recurrence::RECUR_YEARLY == $prop["value"]["recur_type"]) and ((24*365*$interval_yearly) < $prop["value"]["length"]))
					)
					{
						$prop["error"] .= t("Pikkus ei saa olla suurem kui korduse periood. ") . MRP_NEWLINE;
					}

					if (empty ($prop["value"]["length"]))
					{
						$prop["error"] .= t("Pikkus ei saa olla null. ");
					}

					$start =  mktime(0, 0, 0, $prop["value"]["start"]["month"], $prop["value"]["start"]["day"], $prop["value"]["start"]["year"]);
					$end =  mktime(1, 0, 0, $prop["value"]["end"]["month"], $prop["value"]["end"]["day"], $prop["value"]["end"]["year"]);

					if ($start >= $end)
					{
						$prop["error"] .= t("'Alates' peab olema varasem aeg kui 'Kuni'. ") . MRP_NEWLINE;
					}

					if (!empty ($prop["error"]))
					{
						return PROP_ERROR;
					}

					$prop["value"]["time"] = $time_h . ":" . $time_min;
				}
				break;

			case "out_of_service":
				switch ($this_object->prop("state"))
				{
					case mrp_resource_obj::STATE_UNAVAILABLE:
						if ($prop["value"] == 1)
						{
							$prop["error"] = t("Ressurss on kasutusel. Ei saa hooldusse panna. ");
							$retval = PROP_ERROR;
						}
						break;

					case mrp_resource_obj::STATE_AVAILABLE:
						if ($prop["value"] == 1)
						{
							$this_object->set_prop("state", mrp_resource_obj::STATE_OUTOFSERVICE);
						}
						break;

					case mrp_resource_obj::STATE_OUTOFSERVICE:
						if ($prop["value"] == 0)
						{
							$this_object->set_prop("state", mrp_resource_obj::STATE_AVAILABLE);
						}
						break;
				}
				break;

			case "transl":
				$this->trans_save($arr, $this->trans_props);
				break;
		}

		return $retval;
	}

	function callback_pre_save($arr)
	{
		if ($arr["new"] and is_oid ($arr["request"]["mrp_workspace"]))
		{
			$arr["obj_inst"]->set_prop("workspace", obj($arr["request"]["mrp_workspace"]));
		}
	}

	function callback_post_save ($arr)
	{
		$this_object = $arr["obj_inst"];
		$this->workspace->save ();

		if(isset($arr["request"]["group"]) and $arr["request"]["group"] === "grp_resource_materials_config")
		{
			$add_ids = explode(",", $arr["request"]["add_ids"]);
			foreach($add_ids as $oid)
			{
				if(isset($arr["request"]["rem_ids"][$oid]) || !$oid)
				{
					continue;
				}
				$prod = obj($oid);
				$arr["obj_inst"]->add_input_product($prod);
			}
			foreach($arr["request"]["planning"] as $oid => $pl)
			{
				$o = obj($oid);
				$o->set_prop("movement", $arr["request"]["movement"][$oid]);
				$o->set_prop("planning", $pl);
				$o->save();
			}
		}
	}

	function _init_job_list_table(&$table, $times = false)
	{
		/*||
			Ava | Staatus |
		    Projekti nr. | Klient |
			Projekti nimetus | Tr&uuml;kitud (ehk algus ehk esimese t&ouml;&ouml; t&ouml;&ouml;sse minek) [dd-kuu-yyyy] |
			T&auml;htaeg [dd-kuu-yyyy] |
			Tr&uuml;kiarv: |
			T&uuml;kiarv Notes: ||*/

		$table->define_field(array(
			"name" => "modify",
			"caption" => t("Ava"),
			"align" => "center"
		));
		$table->define_field(array(
			"name" => "state",
			"caption" => t("Staatus"),
			"sortable" => 1,
			"align" => "center"
		));
		$table->define_field(array(
			"name" => "proj_nr",
			"caption" => t("Projekti nr."),
			"sortable" => 1,
			"align" => "center"
		));

		$table->define_field(array(
			"name" => "client",
			"caption" => t("Klient"),
			"sortable" => 1,
			"align" => "center"
		));

		$table->define_field(array(
			"name" => "proj_com",
			"caption" => t("Projekti nimetus"),
			"sortable" => 1,
			"align" => "center"
		));

		$table->define_field(array(
			"name" => "starttime",
			"caption" => t("Algus"),
			"sortable" => 1,
			"align" => "center",
			"type" => "time",
			"numeric" => 1,
			"format" => "d-m-Y"
		));

		$table->define_field(array(
			"name" => "deadline",
			"caption" => t("T&auml;htaeg"),
			"sortable" => 1,
			"align" => "center",
			"type" => "time",
			"numeric" => 1,
			"format" => "d-m-Y"
		));

		if($times)
		{
			$table->define_field(array(
				"name" => "planned_length",
				"caption" => t("Planeeritud kestus"),
				"sortable" => 1,
				"align" => "center",
				"numeric" => 1,
			));

			$table->define_field(array(
				"name" => "real_length",
				"caption" => t("Tegelik kestus"),
				"sortable" => 1,
				"align" => "center",
				"numeric" => 1,
			));

			$table->define_field(array(
				"name" => "deviation",
				"caption" => t("H&auml;lve"),
				"sortable" => 1,
				"align" => "center",
			));
		}

		$table->define_field(array(
			"name" => "trykiarv",
			"caption" => t("Tr&uuml;kiarv"),
			"sortable" => 1,
			"align" => "center",
			"numeric" => 1,
		));

		$table->define_field(array(
			"name" => "trykiarv_notes",
			"caption" => t("Tr&uuml;kiarv Notes"),
			"sortable" => 1,
			"align" => "center"
		));
	}

	function _init_job_list_aborted_table(&$table, $times = false)
	{
		$table->define_field(array(
			"name" => "modify",
			"caption" => t("Ava"),
			"align" => "center"
		));
		$table->define_field(array(
			"name" => "state",
			"caption" => t("Staatus"),
			"sortable" => 1,
			"align" => "center"
		));
		$table->define_field(array(
			"name" => "proj_nr",
			"caption" => t("Projekti nr."),
			"sortable" => 1,
			"align" => "center"
		));

		$table->define_field(array(
			"name" => "client",
			"caption" => t("Klient"),
			"sortable" => 1,
			"align" => "center"
		));

		$table->define_field(array(
			"name" => "proj_com",
			"caption" => t("Projekti nimetus"),
			"sortable" => 1,
			"align" => "center"
		));

		$table->define_field(array(
			"name" => "remaining_length",
			"caption" => t("L&otilde;petamiseks kuluv aeg (h)"),
			"sortable" => 1,
			"align" => "center",
		));

		$table->define_field(array(
			"name" => "starttime",
			"caption" => t("Arvatav j&auml;tkamisaeg"),
			"sortable" => 1,
			"align" => "center",
			"type" => "time",
			"numeric" => 1,
			"format" => "d-m-Y"
		));

		$table->define_field(array(
			"name" => "deadline",
			"caption" => t("T&auml;htaeg"),
			"sortable" => 1,
			"align" => "center",
			"type" => "time",
			"numeric" => 1,
			"format" => "d-m-Y"
		));

		$table->define_field(array(
			"name" => "trykiarv",
			"caption" => t("Tr&uuml;kiarv"),
			"sortable" => 1,
			"align" => "center",
			"numeric" => 1,
		));


		$table->define_field(array(
			"name" => "trykiarv_notes",
			"caption" => t("Tr&uuml;kiarv Notes"),
			"sortable" => 1,
			"align" => "center"
		));
	}

	function create_job_list_table ($arr, $for_workspace = false)
	{
		$this_object = $arr["obj_inst"];
		$table = $arr["prop"]["vcl_inst"];
		$done = $arr["request"]["group"] === "grp_resource_joblist_done" || $for_workspace;
		$this->_init_job_list_table($table, $done);

		$table->set_default_sortby ("starttime");
		$table->set_default_sorder ("asc");
		$table->draw_text_pageselector (array (
			"records_per_page" => 50,
		));

		### states for resource joblist
		if($for_workspace)
		{
			$applicable_project_states = array(
				mrp_case_obj::STATE_DONE,
				mrp_case_obj::STATE_PLANNED,
				mrp_case_obj::STATE_INPROGRESS
			);
			$applicable_states = array(
				mrp_job_obj::STATE_DONE,
				mrp_job_obj::STATE_PLANNED,
				mrp_job_obj::STATE_PAUSED,
				mrp_job_obj::STATE_SHIFT_CHANGE,
				mrp_job_obj::STATE_INPROGRESS
			);
		}
		elseif($done)
		{
			$applicable_states = array(
				mrp_job_obj::STATE_DONE
			);
			$applicable_project_states = array(
				mrp_case_obj::STATE_DONE,
				mrp_case_obj::STATE_PLANNED,
				mrp_case_obj::STATE_INPROGRESS
			);
		}
		else
		{
			$applicable_project_states= array(
				mrp_case_obj::STATE_PLANNED,
				mrp_case_obj::STATE_ONHOLD,
				mrp_case_obj::STATE_INPROGRESS
			);
			$applicable_states = array(
				mrp_job_obj::STATE_PLANNED,
				mrp_job_obj::STATE_PAUSED,
				mrp_job_obj::STATE_SHIFT_CHANGE,
				mrp_job_obj::STATE_INPROGRESS
			);
		}

		$prms = array(
			"class_id" => CL_MRP_JOB,
			"resource" => $this_object->id (),
			"state" => $applicable_states,
			// "starttime" => new obj_predicate_compare (OBJ_COMP_LESS, (time () + 886400)),
			new obj_predicate_sort(array("starttime" => "ASC")),
			"CL_MRP_JOB.project(CL_MRP_CASE).state" => $applicable_project_states,
		);

		$clientspan = automatweb::$request->arg("clientspan");
		if($this->can("view", $clientspan))
		{
			$clientspan_obj = obj($clientspan);
			if($clientspan_obj->is_a(CL_CRM_COMPANY))
			{
				$prms["CL_MRP_JOB.project(CL_MRP_CASE).customer"] = $clientspan;
			}
			elseif($clientspan_obj->is_a(CL_CRM_CATEGORY))
			{
				// Whattabout alamkategooriad?? Fuck.
				$prms["CL_MRP_JOB.project(CL_MRP_CASE).customer(CL_CRM_COMPANY).RELTYPE_CUSTOMER(CL_CRM_CATEGORY).id"] = $clientspan;
			}
		}
		elseif(!empty($clientspan))
		{
			$prms["CL_MRP_JOB.project(CL_MRP_CASE).customer(CL_CRM_COMPANY).name"] = $clientspan."%";
		}

		$list = new object_data_list(
			$prms,
			array(
				CL_MRP_JOB => array("project", "exec_order", "state", "starttime", "resource(CL_MRP_RESOURCE).name", "length"),
			)
		);
		$jobs = $list->arr();

		foreach($jobs as $oid => $o)
		{
			if(!$this->can("view", $o["project"]))
			{
				unset($jobs[$oid]);
			}
		}

		if(count($jobs) > 0)
		{
			$this->draw_job_list_table_from_list($table, $jobs, $done);
		}
	}

	function draw_job_list_table_from_list(&$table, $jobs, $times)
	{
		$perpage = 20;
		if(count($jobs) > $perpage)
		{
			$s = isset($_GET["ft_page"]) ? $_GET["ft_page"] * $perpage : 0;
			$table->define_pageselector(array(
				"type" => "lbtxt",
				"records_per_page" => $perpage,
				"d_row_cnt" => count($jobs),
				"no_recount" => true,
			));
			$jobs = array_slice($jobs, $s, $perpage, true);
		}
		foreach($jobs as $oid => $job)
		{
			### get project and client name
			$project = $client = "";

			$p = obj($job["project"]);
			$project = html::get_change_url($p->id(), array("return_url" => get_ru()), ($p->name() . "-" . $job["exec_order"]));

			if ($this->can("view", $p->prop("customer")))
			{
				$c = obj($p->prop("customer"));
				$client = html::get_change_url($c->id(), array("return_url" => get_ru()), $c->name());
			}

			### colour job status
			$state = '<span style="color: ' . mrp_workspace::$state_colours[$job["state"]] . ';">' . $this->states[$job["state"]] . '</span>';
			$change_url = html::get_change_url($oid, array("return_url" => get_ru()));

			$data = array (
				"modify" => html::href (array (
					"caption" => t("Ava"),
					"url" => $change_url,
					)),
				"project" => $project,
				"proj_nr" => html::obj_change_url($p),
				"proj_com" => $p->comment(),
				"state" => $state,
				"starttime" => $job["starttime"],
				"remaining_length" => isset($job["remaining_length"]) ? number_format($job["remaining_length"]/3600, 2) : 0,
				"client" => $client,
				"deadline" => $p->prop("due_date"),
				"trykiarv" => $p->prop("order_quantity"),
				"trykiarv_notes" => $p->prop("trykiarv_notes"),
				"resource" => $job["resource(CL_MRP_RESOURCE).name"],
			);
			if($times)
			{
				$planned_length = (float) ($job["length"] / 3600);

				$data += array(
					"planned_length" => round($planned_length, 2),
				);
				if(mrp_job_obj::STATE_DONE == $job["state"])
				{
					// ARVUTA TEGELIK
					$this->db_query("SELECT * FROM mrp_stats WHERE job_oid = ".$oid);
					$real_len = 0;
					while ($row = $this->db_next())
					{
						$real_len += $row["length"];
					}
					$real_len = $real_len/3600;
					$deviation_float = (float) ($real_len - $planned_length);
					$deviation_percent = $planned_length != 0 ? $deviation_float / $planned_length * 100 : 0;
					$data += array(
						"real_length" => round($real_len, 2),
						"deviation" => $planned_length != 0 ? sprintf(t("%.2f (%.2f%%)"), round($deviation_float, 2), round($deviation_percent, 2)) : t("N/A"),
					);
				}
			}
			$table->define_data ($data);
		}
	}

	function create_resource_calendar ($arr)
	{
		$this_object = $arr["obj_inst"];
		$date = isset($arr["request"]["date"]) ? $arr["request"]["date"] : null;
		$view_type = isset($arr["request"]["viewtype"]) ? $arr["request"]["viewtype"] : null;

		$calendar = new vcalendar (array ("tpldir" => "mrp_calendar"));
		$calendar->init_calendar (array ());
		$calendar->configure (array (
			"overview_func" => array (&$this, "get_overview"),
			"full_weeks" => true,
		));
		$range = $calendar->get_range (array (
			"date" => $date,
			"viewtype" => $view_type,
		));
		$start = $range["start"];
		$end = $range["end"];

		### states for resource joblist
		$applicable_states = array (
			mrp_job_obj::STATE_PLANNED,
			mrp_job_obj::STATE_PAUSED,
			mrp_job_obj::STATE_SHIFT_CHANGE,
			mrp_job_obj::STATE_INPROGRESS,
			mrp_job_obj::STATE_DONE
		);

		$list = new object_list(array(
			"class_id" => CL_MRP_JOB,
			"state" => $applicable_states,
			"resource" => $this_object->id (),
			"starttime" => new obj_predicate_compare (OBJ_COMP_BETWEEN, $start, $end),
		));

		$this->cal_items = array();
		if ($list->count () > 0)
		{
			for ($job = $list->begin(); !$list->end(); $job = $list->next())
			{
/* dbg */ if (!is_oid ($job->prop ("project"))) { echo "project is not an object. job:" . $job->id () . " proj:" . $job->prop ("project") ."<br>"; }
				if (!$this->can("view", $job->prop("project")))
				{
					continue;
				}

				### show only applicable projects' jobs
				$project = obj ($job->prop ("project"));
				$applicable_states = array (
					mrp_case_obj::STATE_PLANNED,
					mrp_case_obj::STATE_INPROGRESS,
					mrp_case_obj::STATE_DONE
				);

				if (in_array ($project->prop ("state"), $applicable_states))
				{
					$project_name = $project->name () ? $project->name () : "...";

					### set timestamp according to state
					$timestamp = ($job->prop ("state") == mrp_job_obj::STATE_DONE) ? $job->prop ("started") : $job->prop ("starttime");

					### colour job status
					$state = '<span style="color: ' . mrp_workspace::$state_colours[$job->prop ("state")] . ';">' . $this->states[$job->prop ("state")] . '</span>';

					### ...
					$calendar->add_item (array (
						"timestamp" => $timestamp,
						"data" => array(
							"name" => '<span  style="white-space: nowrap;">' . $project_name . "-" . $job->prop ("exec_order") . " [" . $state . "]</span>",
							"link" => html::get_change_url($job->id(), array("return_url" => get_ru()))   /*$this->mk_my_orb ("change",array ("id" => $job->id ()), "mrp_job")*/,
						),
					));
					$this->cal_items[$timestamp] = html::get_change_url($job->id(), array("return_url" => get_ru()));
				}
			}
		}
		$list = new object_list(array(
			"class_id" => array(CL_CRM_MEETING, CL_TASK),
			"CL_TASK.RELTYPE_RESOURCE" => $this_object->id(),
		));
		foreach($list->arr() as $task)
		{
			$calendar->add_item (array (
				"item_start" => $task->prop("start1"),
				"item_end" => $task->prop("end"),
				"data" => array(
					"name" => $task->name(),
					"link" => html::get_change_url($task->id(), array("return_url" => get_ru())),
				),
			));
			$this->cal_items[$task->prop("start1")] = html::get_change_url($task->id(), array("return_url" => get_ru()));
		}

		return $calendar->get_html ();
	}

	function get_overview ($arr = array())
	{
		/*$start = time() - (24*3600*60);
		$end = time() + (24*3600*60);

		for($i = $start; $i < $end; $i += (24*3600))
		{
			$ret[$i] = aw_url_change_var("viewtype", "week", aw_url_change_var("date", date("d", $i)."-".date("m", $i)."-".date("Y", $i)));
		}*/

		return $this->cal_items;
	}

	function get_unavailable_periods ($resource, $start, $end) // DEPRECATED
	{ return $resource->get_unavailable_periods($start, $end); }

	function get_recurrent_unavailable_periods ($resource, $start, $end) // DEPRECATED
	{ return $resource->get_recurrent_unavailable_periods($start, $end); }

	// DEPRECATED. //!!! v6ibolla vaadata kas tasub date_calc-i omaga mergeda vms.
	function get_week_start ($time = false) //!!! somewhat dst safe (safe if error doesn't exceed 12h) //
	{
		if (!$time)
		{
			$time = time ();
		}

		$date = getdate ($time);
		$wday = $date["wday"] ? ($date["wday"] - 1) : 6;
		$week_start = $time - ($wday * 86400 + $date["hours"] * 3600 + $date["minutes"] * 60 + $date["seconds"]);
		$nodst_hour = (int) date ("H", $week_start);

		if ($nodst_hour === 0)
		{
			$week_start = $week_start;
		}
		else
		{
			if ($nodst_hour < 13)
			{
				$dst_error = $nodst_hour;
				$week_start = $week_start - $dst_error*3600;
			}
			else
			{
				$dst_error = 24 - $nodst_hour;
				$week_start = $week_start + $dst_error*3600;
			}
		}

		return $week_start;
	}

	function safe_settype_float ($value) // DEPRECATED
	{ return aw_math_calc::string2float($value); }

	function get_events_for_range($resource, $start, $end)
	{
		$applicable_states = array (
			mrp_job_obj::STATE_PLANNED,
			mrp_job_obj::STATE_PAUSED,
			mrp_job_obj::STATE_SHIFT_CHANGE,
			mrp_job_obj::STATE_INPROGRESS
		);

		$list = new object_list(array(
			"class_id" => CL_MRP_JOB,
			"state" => $applicable_states,
			"resource" => $resource->id (),
			"starttime" => new obj_predicate_compare (OBJ_COMP_BETWEEN, $start, $end),
		));

		$ret = array();
		if ($list->count () > 0)
		{
			for ($job =& $list->begin(); !$list->end(); $job =& $list->next())
			{
				if (!$this->can("view", $job->prop("project")))
				{
					continue;
				}

				### show only applicable projects' jobs
				$project = obj ($job->prop ("project"));
				$applicable_states = array (
					mrp_case_obj::STATE_PLANNED,
					mrp_case_obj::STATE_PAUSED,
					mrp_case_obj::STATE_INPROGRESS,
					mrp_case_obj::STATE_DONE
				);

				if (in_array ($project->prop ("state"), $applicable_states))
				{
					$project_name = $project->name () ? $project->name () : "...";

					### set timestamp according to state
					$timestamp = ($job->prop ("state") == mrp_job_obj::STATE_DONE) ? $job->prop ("started") : $job->prop ("starttime");

					$ret[] = array(
						"start" => $timestamp,
						"end" => $timestamp + $job->prop("planned_length"),
						"name" => $job->name()
					);
				}
			}
		}
		$list = new object_list(array(
			"class_id" => array(CL_CRM_MEETING, CL_TASK),
			"CL_TASK.RELTYPE_RESOURCE" => $resource->id(),
		));
		foreach($list->arr() as $task)
		{
			if ($task->prop("start1") > $end || $task->prop("end") < $start)
			{
				continue;
			}
			$ret[] = array(
				"start" => $task->prop("start1"),
				"end" => $task->prop("end"),
				"name" => $task->name()
			);
		}

		return $ret;
	}

	function is_available_for_range($resource, $start, $end)
	{
		$avail = true;
		$evstr = "";
		$ri = $resource->instance();
		$events = $ri->get_events_for_range(
			$resource,
			$start,
			$end
		);
		if (count($events))
		{
			$avail = false;
			$evstr = t("Ressurss on valitud aegadel kasutuses:<br>");
			foreach($events as $event)
			{
				$evstr .= date("d.m.Y H:i", $event["start"])." - ".
						  date("d.m.Y H:i", $event["end"])."  ".$event["name"]."<br>";
			}
		}

		if ($avail)
		{
			$una = $resource->get_unavailable_periods($start, $end);

			if (count($una))
			{
				$avail = false;
				$evstr = t("Ressurss ei ole valitud aegadel kasutatav!<br>Kinnised ajad:<br>");
				foreach($una as $event)
				{
					$evstr .= date("d.m.Y H:i", $event["start"])." - ".
							  date("d.m.Y H:i", $event["end"]).": ".$event["name"];
				}
			}
		}

		if ($avail)
		{
			$una = $resource->get_unavailable_periods($start, $end);
			if (count($una))
			{
				$avail = false;
				$evstr = t("Ressurss ei ole valitud aegadel kasutatav!<br>Kinnised ajad:<br>");
				foreach($una as $event)
				{
					$evstr .= date("d.m.Y H:i", $event["start"])." - ".
							  date("d.m.Y H:i", $event["end"])."<br>";
				}
			}
		}

		if ($avail)
		{
			return true;
		}
		return $evstr;
	}

	protected function resource_gantt_add_unavail($o, $chart, $row_id, $start, $length)
	{
		static $periods;
		if(!isset($periods))
		{
			$periods = get_instance(CL_MRP_SCHEDULE)->get_unavailable_periods_for_range(array(
				"mrp_resource" => $o->id(),
				"mrp_start" => $start,
				"mrp_length" => $length
			));
		}

		foreach($periods as $s => $e)
		{
			$chart->add_bar(array(
				"id" => "unavail_{$s}_{$e}_{$row_id}",
				"row" => $row_id,
				"start" => $s,
				"colour" => MRP_COLOUR_UNAVAILABLE,
				"length" => $e - $s,
				"layer" => 0,
				"title" => "Kinnine aeg (" . date (MRP_DATE_FORMAT, $s) . " - " . date (MRP_DATE_FORMAT, $e) . ")"
			));
		}
	}

	protected function _init_materials_report_table($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
	}

	public function _get_materials_report_table($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
	}

	public function _get_materials_report_time_tree($arr)
	{
		return get_instance(CL_MRP_WORKSPACE)->_get_time_tree($arr);
	}

	public function _get_operators_tlb($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		$t->add_button(array(
			"img" => "search.gif",
		));
	}

	protected function _init_operators_tbl($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		$t->define_chooser();
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Isik"),
			"align" => "center",
			"sortable" => true,
		));
	}

	public function _get_operators_tbl($arr)
	{
		$this->_init_operators_tbl($arr);
		$t = &$arr["prop"]["vcl_inst"];

		foreach($arr["obj_inst"]->get_operators()->names() as $oid => $name)
		{
			$t->define_data(array(
				"oid" => $oid,
				"name" => parse_obj_name($name),
			));
		}
	}

	public function _get_resource_gantt($arr)
	{
		$workspace = $this_object = $this->workspace;

		### update schedule
		$schedule = get_instance (CL_MRP_SCHEDULE);
		$schedule->create (array("mrp_workspace" => $workspace->id()));

		$time =  time();
		$chart = get_instance ("vcl/gantt_chart");
		$columns = (int) (isset($arr["request"]["mrp_chart_length"]) ? $arr["request"]["mrp_chart_length"] : 7);
		$range_start = (int) (isset($arr["request"]["mrp_chart_start"]) ? $arr["request"]["mrp_chart_start"] : $this->get_week_start ());
		$range_end = (int) ($range_start + $columns * 86400);
		$hilighted_project = (int) (isset($arr["request"]["mrp_hilight"]) ? $arr["request"]["mrp_hilight"] : false);
		$hilighted_jobs = array ();

		switch ($columns)
		{
			case 1:
				$subdivisions = 24;
				break;

			default:
				$subdivisions = 3;
		}

		$ids = array(-1);

		$sql = "
		SELECT
			j.oid as jid
		FROM
			mrp_job j
			LEFT JOIN mrp_schedule s ON j.oid = s.oid
		WHERE
			j.resource = '".$arr["obj_inst"]->id()."' AND
			(
				s.starttime < $range_end
				AND s.starttime + j.planned_length > $range_start
				AND j.state = '".MRP_STATUS_PLANNED."'
			OR
				j.started < $range_end
				AND j.finished > $range_start
				AND j.state = '".MRP_STATUS_DONE."'
			OR
				j.started < $range_end
				AND j.started + j.planned_length > $range_start
				AND j.state IN ('".implode("','", array(MRP_STATUS_SHIFT_CHANGE, MRP_STATUS_PAUSED))."')
			OR
				j.state = '".MRP_STATUS_INPROGRESS."'
			)
		";
		foreach($this->db_fetch_array($sql) as $row)
		{
			$ids[] = $row["jid"];
		}

		$odl = new object_data_list(
			array(
				"class_id" => CL_MRP_JOB,
				"oid" => $ids,
				"resource" => $arr["obj_inst"]->id(),
				"parent" => $workspace->prop("jobs_folder"),
				new obj_predicate_sort(array(
					"starttime" => "asc"
					/*	Won't work at this moment.
					"project(CL_MRP_CASE).name" => "asc"
					*/
				)),
			),
			array(
				CL_MRP_JOB => array("project", "project(CL_MRP_CASE).name" => "project_name", "state", "started", "finished", "planned_length", "starttime"),
			)
		);

		// Have to sort by project manually, whatta bummer!
		$jobs_by_project = array();
		$project_names = array();
		foreach($odl->arr() as $o)
		{
			$jobs_by_project[$o["project"]][$o["oid"]] = $o;
			$project_names[$o["project"]] = $o["project_name"];
		}
		asort($project_names);

		foreach($project_names as $project => $project_name)
		{
			$chart->add_row (array (
				"name" => $project,
				"title" => $project_name,
				"type" => "separator",
			));

			foreach($jobs_by_project[$project] as $job)
			{
				$chart->add_row(array(
					"name" => "row_".$job["oid"],
					"title" => $job["name"],
					"uri" => html::get_change_url(
						$job["oid"],
						array("return_url" => get_ru())
					)
				));

				### get start&length according to job state
				switch ($job["state"])
				{
					case MRP_STATUS_DONE:
						$start = max($range_start, $job["started"]);
						$length = min($range_end, $job["finished"]) - $start;
	//					echo date(MRP_DATE_FORMAT, $start) . "-" . date(MRP_DATE_FORMAT, $start + $length) . "<br>";
						break;

					case MRP_STATUS_PLANNED:
						$start = max($range_start, $job["starttime"]);
						$length = $job["planned_length"];
						break;

					case MRP_STATUS_SHIFT_CHANGE:
					case MRP_STATUS_PAUSED:
					case MRP_STATUS_INPROGRESS:
						$start = max($range_start, $job["started"]);
						$length = (($start + $job["planned_length"]) < $time) ? ($time - $start) : $job["planned_length"];
						break;
				}

				$colour = mrp_workspace::$state_colours[$job["state"]];
				$colour = $job["project"] == $hilighted_project ? MRP_COLOUR_HILIGHTED : $colour;

				$chart->add_bar(array (
					"id" => $job["oid"],
					"row" => "row_".$job["oid"],
					"start" => $start,
					"colour" => $colour,
					"length" => $length,
					"layer" => 1,
					"uri" => aw_url_change_var ("mrp_hilight", $job["project"]),
					"title" => $job["name"] . " (" . date (MRP_DATE_FORMAT, $start) . " - " . date (MRP_DATE_FORMAT, $start + $length) . ")"
				));

				$this->resource_gantt_add_unavail($arr["obj_inst"], $chart, "row_".$job["oid"], $range_start, $range_end - $range_start);
			}
		}

		### config
		$chart->configure_chart (array (
			"chart_id" => "master_schedule_chart",
			"style" => "aw",
			"start" => $range_start,
			"end" => $range_end,
			"columns" => $columns,
			"caption" => sprintf(t("Ressursi '%s' t&ouml;&ouml;voog"), $arr["obj_inst"]->name()),
			"footer" => $this_object->instance()->draw_colour_legend(),
			"subdivisions" => $subdivisions,
			"timespans" => $subdivisions,
			"width" => 850,
			"row_height" => 10,
			"row_dfn" => t("Projekt/t&ouml;&ouml;"),
			"navigation" => $this_object->instance()->create_chart_navigation($arr)
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

		$arr["prop"]["value"] = $chart->draw_chart();
	}

	public function _get_resource_deviation_chart($arr)
	{
		$arr["request"]["mrp_tree_active_item"] = $arr["obj_inst"]->id();
		return get_instance("mrp_workspace")->_get_resource_deviation_chart($arr);
	}

	public function _get_job_client_tree($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->add_item(0, array(
			"id" => "all",
			"name" => t("K&otilde;ik"),
			"url" => aw_url_change_var("clientspan", NULL),
		));

		$i = new mrp_workspace();
		$i->create_customers_tree(array_merge($arr, array("obj_inst" => $arr["obj_inst"]->prop("workspace"))));

		// Hack the URLs
		foreach($t->get_item_ids() as $id)
		{
			$item = $t->get_item($id);
			if (isset($item["url"]))
			{
				$uri = new aw_uri($item["url"]);
				$special_param = strlen($uri->arg("cat")) ? $uri->arg("cat") : (strlen($uri->arg("cust")) ? $uri->arg("cust") : $uri->arg("alph"));
				$uri->unset_arg(array("cat", "cust", "alph", "clientspan"));
				if(!empty($special_param))
				{
					$uri->set_arg("clientspan", $special_param);
				}
				$item["url"] = $uri->get();
				$t->set_item($item);
			}
			elseif (isset($item["reload"]))
			{
				$reload = array(
					"layouts" => array("resource_deviation_chart", "job_list_table"),
					"props" => array("job_list", "resource_deviation_chart"),
				);
				$reload["params"]["cat"] = $reload["params"]["cust"] = NULL;
				$item["reload"] = $reload;
				$t->set_item($item);
			}
		}

		$clientspan = automatweb::$request->arg("clientspan");

		if($this->can("view", $clientspan))
		{
			$t->set_selected_item($clientspan);
		}
		elseif(!empty($clientspan))
		{
			$t->set_selected_item("alph_".$clientspan);
		}
		else
		{
			$t->set_selected_item("all");
		}
	}

	public function _get_job_time_tree($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->set_selected_item(!empty($_GET["timespan"]) ? $_GET["timespan"] : "all");
		$jobs = $this->get_jobs($arr);
		$jobs_by_year = array();
		$cwcnt = array();
		$lwcnt = array();
		$nwcnt = array();
		$cmcnt = array();
		$lmcnt = array();
		$nmcnt = array();
		foreach($jobs as $job)
		{
			switch ($arr["request"]["group"])
			{
				case "grp_resource_joblist_done":
					$tm = $job["finished"];
					break;

				case "grp_resource_joblist_aborted":
					$tm = $job["aborted"];
					break;

				default:
					$tm = $job["state"] == MRP_STATUS_PLANNED ? $job["starttime"] : $job["started"];
					break;
			}

			list($Y, $M, $D) = explode("-", date("Y-n-j", $tm));
			$jobs_by_year[$Y][$M][$D][$job["oid"]] = 1;
			// Weeks
			if(date("Y-W", $tm) === date("Y-W"))
			{
				$cwcnt[$job["oid"]] = 1;
			}
			elseif(date("Y-W", mktime(0, 0, 0, $M, $D + 7, $Y)) === date("Y-W"))
			{
				$lwcnt[$job["oid"]] = 1;
			}
			elseif(date("Y-W", mktime(0, 0, 0, $M, $D - 7, $Y)) === date("Y-W"))
			{
				$nwcnt[$job["oid"]] = 1;
			}
			// Months
			if(date("Y-m", $tm) === date("Y-m"))
			{
				$cmcnt[$job["oid"]] = 1;
			}
			elseif(date("Y-m", mktime(0, 0, 0, $M + 1, 1, $Y)) === date("Y-m"))
			{
				$lmcnt[$job["oid"]] = 1;
			}
			elseif(date("Y-m", mktime(0, 0, 0, $M - 1, 1, $Y)) === date("Y-m"))
			{
				$nmcnt[$job["oid"]] = 1;
			}
		}
		$branches = array(
			0 => array(
				"all" => t("K&otilde;ik"),
				"current_week" => sprintf(t("K&auml;esolev n&auml;dal (%u)"), count($cwcnt)),
			),
		);
		if(!in_array($arr["request"]["group"], array("grp_resource_joblist_aborted", "grp_resource_joblist_done")))
		{
			$branches[0]["next_week"] = sprintf(t("J&auml;rgmine n&auml;dal (%u)"), count($nwcnt));
		}
		else
		{
			$branches[0]["last_week"] = sprintf(t("M&ouml;&ouml;dunud n&auml;dal (%u)"), count($lwcnt));
		}

		$branches[0]["current_month"] = sprintf(t("K&auml;esolev kuu (%u)"), count($cmcnt));
		if(!in_array($arr["request"]["group"], array("grp_resource_joblist_aborted", "grp_resource_joblist_done")))
		{
			$branches[0]["next_month"] = sprintf(t("J&auml;rgmine kuu (%u)"), count($nmcnt));
		}
		else
		{
			$branches[0]["last_month"] = sprintf(t("M&ouml;&ouml;dunud kuu (%u)"), count($lmcnt));
		}
		ksort($jobs_by_year);
		foreach($jobs_by_year as $year => $jobs_by_mon)
		{
			$ycnt = 0;
			ksort($jobs_by_mon);
			foreach($jobs_by_mon as $month => $jobs_by_day)
			{
				$mcnt = 0;
				ksort($jobs_by_day);
				foreach($jobs_by_day as $day => $jobs)
				{
					$dcnt = count($jobs);
					$branches["date_".$year."_".$month]["date_".$year."_".$month."_".$day] = sprintf(t("%u. %s %u (%u)"), $day, aw_locale::get_lc_month($month), $year, $dcnt);
					$mcnt += $dcnt;
				}
				$branches["date_".$year]["date_".$year."_".$month] = sprintf(t("%s %u (%u)"), aw_locale::get_lc_month($month), $year, $mcnt);
				$ycnt += $mcnt;
			}
			$branches[0]["date_".$year] = sprintf(t("%s (%u)"), $year, $ycnt);
		}
		foreach($branches as $parent => $branch)
		{
			foreach($branch as $id => $caption)
			{
				$t->add_item($parent, array(
					"id" => $id,
					"name" => $caption,
					"url" => aw_url_change_var(array(
						"timespan" => $id !== "all" ? $id : NULL,
					)),
				));
			}
		}
	}

	function _get_job_list_aborted($arr)
	{
		$table = $arr["prop"]["vcl_inst"];
		$this->_init_job_list_aborted_table($table, false);

		$table->set_default_sortby ("starttime");
		$table->set_default_sorder ("asc");
		$table->draw_text_pageselector (array (
			"records_per_page" => 50,
		));

		$jobs = $this->get_jobs($arr);

		if(count($jobs) > 0)
		{
			$this->draw_job_list_table_from_list($table, $jobs, false);
		}
	}

	function _get_cal_tb($arr)
	{
		$tb =&  $arr["prop"]["vcl_inst"];
		$tb->add_menu_button(array(
			'name'=>'add_item',
			'tooltip'=> t('Uus')
		));

		$pl = get_instance(CL_PLANNER);
		$cal_id = $pl->get_calendar_for_user(array(
			"uid" => aw_global_get("uid"),
		));

		$clids = array(CL_TASK => 13, CL_CRM_MEETING => 11, CL_CRM_CALL => 12/*, CL_CRM_OFFER => 9*/);
		$clss = aw_ini_get("classes");

		$u = get_instance(CL_USER);
		$cur_co = $u->get_current_company();

		foreach($clids as $clid => $relt)
		{
			$url = $this->mk_my_orb('new',array(
				'add_to_cal' => $cal_id,
				'clid' => $clid,
				'title' => $clss[$clid]["name"],
				'parent' => $arr["obj_inst"]->id(),
				'return_url' => get_ru(),
				"set_resource" => $arr["obj_inst"]->id()
			), $clid);
			$tb->add_menu_item(array(
				'parent'=>'add_item',
				'text' => $clss[$clid]["name"],
				'link' => $url
			));
		}
	}

	function mk_materials_tb($arr)
	{
		$tb = $arr["prop"]["vcl_inst"];
		$tb->add_save_button();
		$tb->add_button(array(
			"name" => "rem_materials",
			"action" => "remove_materials",
			"img" => "delete.gif",
			"tooltip" => t("Eemalda valitud"),
		));
	}

	/**
	@attrib name=remove_materials all_args=1
	**/
	function remove_materials($arr)
	{
		if ($this->can("view", $arr["id"]))
		{
			$resource =  new object($arr["id"]);
			foreach($arr["rem_ids"] as $oid)
			{
				if($this->can("view", $oid))
				{
					$o = obj($oid);
					$resource->remove_input_product($o);
				}
			}
		}

		return $arr["post_ru"];
	}

	function callback_generate_scripts($arr)
	{
		if(isset($arr["request"]["group"]) and $arr["request"]["group"] === "grp_resource_materials_config")
		{
			$conn = $arr["obj_inst"]->connections_to(array(
				"from.class_id" => CL_MATERIAL_EXPENSE_CONDITION,
			));
			$prods = array();
			foreach($conn as $c)
			{
				if($prod = $c->from()->prop("product"))
				{
					$prods[] = $prod;
				}
			}
			$script = "
			var tbls = $('.awmenuedittabletag')
			var set_ids = new Array()";

			foreach($prods as $prod)
			{
				$script .= "
			set_ids[".$prod."] = 1";
			}

			$script .= "
			function add_attribute(elem, attr, value)
			{
				var newAttr = document.createAttribute(attr);
    				newAttr.nodeValue = value
				elem.setAttributeNode(newAttr);
			}

			function add_row(add_id, add_url, add_text)
			{
				if(set_ids[add_id])
				{
					alert('".t("Antud materjal on juba lisatud")."')
					return;
				}

				set_ids[add_id] = 1

				var newrow = document.createElement('tr')
				add_attribute(newrow, 'class', 'awmenuedittabletrow')

				var cell1 = document.createElement('td')
				add_attribute(cell1, 'class', 'awmenuedittabletext')
				add_attribute(cell1, 'align', 'center')
				add_attribute(cell1, 'width', '55')
				add_attribute(cell1, 'style', 'background: #CCFFCC')

				var chb1 = document.createElement('input')
				add_attribute(chb1, 'class', 'checkbox')
				add_attribute(chb1, 'type', 'checkbox')
				add_attribute(chb1, 'name', 'rem_ids['+add_id+']')
				add_attribute(chb1, 'value', add_id)
				cell1. appendChild(chb1)

				var cell2 = document.createElement('td')
				add_attribute(cell2, 'style', 'background: #CCFFCC')
				add_attribute(cell2, 'class', 'awmenuedittabletext')

				var url2 = document.createElement('a')
				add_attribute(url2, 'href', add_url)
				var urlcontent2 = document.createTextNode(add_text)
				url2.appendChild(urlcontent2)
				cell2. appendChild(url2)

				var cell3 = document.createElement('td')
				add_attribute(cell3, 'style', 'background: #CCFFCC')
				add_attribute(cell3, 'class', 'awmenuedittabletext')

				var cell4 = document.createElement('td')
				add_attribute(cell4, 'style', 'background: #CCFFCC')
				add_attribute(cell4, 'class', 'awmenuedittabletext')

				newrow.appendChild(cell1)
				newrow.appendChild(cell2)
				newrow.appendChild(cell3)
				newrow.appendChild(cell4)
				tbls[0].appendChild(newrow)

				document.forms.changeform.add_ids.value += ','+add_id
			}
			";

			return $script;
		}
	}

	function mk_materials_tree($arr)
	{
		$whi = get_instance(CL_SHOP_WAREHOUSE);
		$owner = $arr["obj_inst"]->prop("workspace");
		if($owner)
		{
			$whs = $owner->prop("purchasing_manager");
		}
		if(count($whs))
		{
			$arr["warehouses"] = $whs;
			$pt = $whi->get_warehouse_configs($arr, "prod_type_fld");
			$root_name = t("Artiklikategooriad");
			$ol = new object_list(array(
				"parent" => $pt,
				"class_id" => CL_SHOP_PRODUCT_CATEGORY,
				"site_id" => array(),
				"lang_id" => array(),
			));
			$gbf = $this->mk_my_orb("get_materials_tree_level",array(
				"set_retu" => get_ru(),
				"pgtf" => automatweb::$request->arg("pgtf"),
				"parent" => " ",
			));

			$tree = $arr["prop"]["vcl_inst"];
			$tree->start_tree(array(
				"has_root" => true,
				"root_name" => $root_name,
				"root_url" => "#",
				"root_icon" => icons::get_icon_url(CL_MENU),
				"type" => TREE_DHTML,
				"tree_id" => "materials_tree",
				"persist_state" => 1,
				"get_branch_func" => $gbf,
			));
			foreach($ol->arr() as $o)
			{
				$url = aw_url_change_var(array("pgtf" => $o->id()));
				$this->insert_materials_tree_item($tree, $o, $url);
			}
			$tree->set_selected_item(automatweb::$request->arg("pgtf"));
		}
	}

	/**
		@attrib name=get_materials_tree_level all_args=1
	**/
	function get_materials_tree_level($arr)
	{
		parse_str($_SERVER['QUERY_STRING'], $arr);
		$tree = get_instance("vcl/treeview");
		$arr["parent"] = trim($arr["parent"]);
		$tree->start_tree(array (
			"type" => TREE_DHTML,
			"branch" => 1,
			"tree_id" => "materials_tree_s",
			"persist_state" => 1,
		));

		$ol = new object_list(array(
			"parent" => $arr["parent"],
			"class_id" => CL_SHOP_PRODUCT_CATEGORY,
			"site_id" => array(),
			"lang_id" => array(),
		));

		$po = obj($arr["parent"]);
		$conn = $po->connections_to(array(
			"from.class_id" => CL_SHOP_PRODUCT,
			"type" => "RELTYPE_CATEGORY",
		));
		foreach($conn as $c)
		{
			$ol->add($c->from());
		}

		foreach($ol->arr() as $o)
		{
			if($o->class_id() != CL_SHOP_PRODUCT)
			{
				$url = "#";
			}
			else
			{
				$url = "javascript:add_row('".$o->id()."', '".$this->mk_my_orb("change", array("id" => $o->id(), "return_url" => get_ru()), CL_SHOP_PRODUCT)."', '".str_replace(array("'", '"'), array("", ""), $o->name())."')";
			}
			$this->insert_materials_tree_item($tree, $o, $url);
		}
		die($tree->finalize_tree());
	}

	private function insert_materials_tree_item($tree, $o, $url)
	{
		$clid = $o->class_id();
		$tree->add_item(0, array(
			"url" => $url,
			"name" => $o->name(),
			"id" => $o->id(),
			"iconurl" => icons::get_icon_url(($clid == CL_SHOP_PRODUCT)?$clid:CL_MENU),
		));
		$check_ol = new object_list(array(
			"parent" => $o->id(),
			"class_id" => CL_SHOP_PRODUCT_CATEGORY,
		));
		if($clid == CL_SHOP_PRODUCT_CATEGORY)
		{
			$conn = $o->connections_to(array(
				"from.class_id" => CL_SHOP_PRODUCT,
				"type" => "RELTYPE_CATEGORY",
			));
			if(count($conn))
			{
				$subitems = 1;
			}
		}

		if($check_ol->count() || $subitems)
		{
			$tree->add_item($o->id(), array());
		}
	}

	function mk_materials_sel_tbl($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->set_caption(t("Ressursil kasutatavad materjalid"));
		$t->define_field(array(
			"name" => "oid",
			"caption" => t("Vali"),
			"align" => "center",
			"width" => 30
		));
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
		));
		$t->define_field(array(
			"name" => "planning",
			"caption" => t("Tarnetingimus planeerimisel"),
		));
		$t->define_field(array(
			"name" => "movement",
			"caption" => t("Materjali liikumine materjalilaost"),
		));
		$t->set_caption(sprintf(t("Ressursil %s kasutatavad materjalid"), $arr["obj_inst"]->name()));
		$conn = $arr["obj_inst"]->connections_to(array(
			"from.class_id" => CL_MATERIAL_EXPENSE_CONDITION,
		));
		foreach($conn as $c)
		{
			$o = $c->from();
			$prodid = $o->prop("product");
			if($this->can("view", $prodid))
			{
				$prod = obj($prodid);
				$t->define_data(array(
					"oid" => html::checkbox(array(
						"name" => "rem_ids[{$prodid}]",
						"value" => $prodid,
					)),
					"name" => html::obj_change_url($o, $prod->name()),
					"planning" => html::select(array(
						"name" => "planning[".$o->id()."]",
						"options" => $o->planning_options(),
						"value" => $o->prop("planning"),
					)),
					"movement" => html::select(array(
						"name" => "movement[".$o->id()."]",
						"options" => $o->movement_options(),
						"value" => $o->prop("movement"),
					)),
				));
			}
		}
	}

	function callback_mod_tab($arr)
	{
		$trc = aw_ini_get("user_interface.trans_classes");
		if ($arr["id"] == "transl" && (aw_ini_get("user_interface.content_trans") != 1 && empty($trc[$this->clid])))
		{
			return false;
		}

		if (in_array($arr["id"], array("grp_resource_materials", "grp_resource_materials_report")))
		{
			$arr["link"] = aw_url_change_var("timespan", "current_week", $arr["link"]);
		}
		return true;
	}

	function callback_mod_layout(&$arr)
	{
		switch($arr["name"])
		{
			case "job_list_table":
				$clientspan_str = "";
				$clientspan = automatweb::$request->arg("clientspan");
				if($this->can("view", $clientspan))
				{
					$clientspan_obj = obj($clientspan);
					if($clientspan_obj->is_a(CL_CRM_COMPANY))
					{
						$clientspan_str = sprintf(t(", mille klient on '%s'"), $clientspan_obj->name());
					}
					elseif($clientspan_obj->is_a(CL_CRM_CATEGORY))
					{
						$clientspan_str = sprintf(t(", mille klient kuulub kliendikategooriasse '%s'"), $clientspan_obj->name());
					}
				}
				elseif(!empty($clientspan))
				{
					$clientspan_str = sprintf(t(", mille kliendi nime algust&auml;ht on %s"), $clientspan);
				}

				if(!empty($for_workspace))
				{
					$arr["area_caption"] = sprintf(t("Ressursi '%s' t&ouml;&ouml;d"), parse_obj_name($arr["obj_inst"]->name())).$clientspan_str;
				}
				else
				{
					if(automatweb::$request->arg("group") === "grp_resource_joblist_done")
					{
						$arr["area_caption"] = sprintf(t("Ressursi '%s' tehtud t&ouml;&ouml;d"), parse_obj_name($arr["obj_inst"]->name())).$clientspan_str;
					}
					elseif(automatweb::$request->arg("group") === "grp_resource_joblist_aborted")
					{
						$arr["area_caption"] = sprintf(t("Ressursi '%s' katkestatud t&ouml;&ouml;d"), parse_obj_name($arr["obj_inst"]->name())).$clientspan_str;
					}
					else
					{
						$arr["area_caption"] = sprintf(t("Ressursi '%s' eesseisvad t&ouml;&ouml;d"), parse_obj_name($arr["obj_inst"]->name())).$clientspan_str;
					}
				}
				break;

			case "job_client_tree":
				$arr["area_caption"] = "Vali kuvatavate t&ouml;&ouml;de klient/kliendikategooria";
		}
		return true;
	}

	function callback_get_transl($arr)
	{
		return $this->trans_callback($arr, $this->trans_props);
	}

	private function get_jobs($arr, $for_workspace = false)
	{
		$done = $arr["request"]["group"] === "grp_resource_joblist_done" || $for_workspace;
		if($for_workspace)
		{
			$applicable_project_states = $applicable_states = array(
				MRP_STATUS_DONE,
				MRP_STATUS_PLANNED,
				MRP_STATUS_PAUSED,
				MRP_STATUS_SHIFT_CHANGE,
				MRP_STATUS_INPROGRESS,
			);
		}
		elseif($done)
		{
			$applicable_states = array(
				MRP_STATUS_DONE,
			);
			$applicable_project_states = array(
				MRP_STATUS_DONE,
				MRP_STATUS_PLANNED,
				MRP_STATUS_PAUSED,
				MRP_STATUS_INPROGRESS,
			);
		}
		elseif($arr["request"]["group"] === "grp_resource_joblist_aborted")
		{
			$applicable_states = array(
				MRP_STATUS_ABORTED,
			);
			$applicable_project_states = array();
		}
		else
		{
			$applicable_project_states = $applicable_states = array(
				MRP_STATUS_PLANNED,
				MRP_STATUS_PAUSED,
				MRP_STATUS_SHIFT_CHANGE,
				MRP_STATUS_INPROGRESS,
			);
		}

		$filt = array();
		if(!empty($_GET["timespan"]) && $arr["prop"]["type"] != "treeview")
		{
			if(substr($arr["request"]["timespan"], 0, 5) === "date_")
			{
				list($Y, $M, $D) = explode("_", substr($arr["request"]["timespan"], 5));
				if($M === NULL)
				{
					$from = mktime(0, 0, 0, 1, 1, $Y);
					$to = mktime(23, 59, 59, 12, 31, $Y);
				}
				elseif($D === NULL)
				{
					$from = mktime(0, 0, 0, $M, 1, $Y);
					$to = mktime(23, 59, 59, $M+1, 0, $Y);
				}
				else
				{
					$from = mktime(0, 0, 0, $M, $D, $Y);
					$to = mktime(23, 59, 59, $M, $D, $Y);
				}
			}
			else
			{
				switch($arr["request"]["timespan"])
				{
					case "current_week":
						list($Y, $M, $D, $N) = explode("-", date("Y-n-j-N"));
						$from = mktime(0, 0, 0, $M, $D-$N+1, $Y);
						$to = mktime(23, 59, 59, $M, $D+7-$N, $Y);
						break;

					case "next_week":
						list($Y, $M, $D, $N) = explode("-", date("Y-n-j-N"));
						$from = mktime(0, 0, 0, $M, $D-$N+8, $Y);
						$to = mktime(23, 59, 59, $M, $D+14-$N, $Y);
						break;

					case "last_week":
						list($Y, $M, $D, $N) = explode("-", date("Y-n-j-N"));
						$from = mktime(0, 0, 0, $M, $D-$N-6, $Y);
						$to = mktime(23, 59, 59, $M, $D-$N, $Y);
						break;

					case "current_month":
						list($Y, $M) = explode("-", date("Y-n"));
						$from = mktime(0, 0, 0, $M, 1, $Y);
						$to = mktime(23, 59, 59, $M+1, 0, $Y);
						break;

					case "next_month":
						list($Y, $M) = explode("-", date("Y-n"));
						$from = mktime(0, 0, 0, $M+1, 1, $Y);
						$to = mktime(23, 59, 59, $M+2, 0, $Y);
						break;

					case "last_month":
						list($Y, $M) = explode("-", date("Y-n"));
						$from = mktime(0, 0, 0, $M-1, 1, $Y);
						$to = mktime(23, 59, 59, $M, 0, $Y);
						break;
				}
			}
			switch ($arr["request"]["group"])
			{
				case "grp_resource_joblist_done":
					$filt["finished"] = new obj_predicate_compare(
						OBJ_COMP_BETWEEN_INCLUDING,
						$from,
						$to,
						"int"
					);
					break;

				case "grp_resource_joblist_aborted":
					$filt["aborted"] = new obj_predicate_compare(
						OBJ_COMP_BETWEEN_INCLUDING,
						$from,
						$to,
						"int"
					);
					break;

				default:
					$filt[] = new object_list_filter(array(
						"logic" => "OR",
						"conditions" => array(
							new object_list_filter(array(
								"logic" => "AND",
								"conditions" => array(
									"CL_MRP_JOB.state" => MRP_STATUS_PLANNED,
									"CL_MRP_JOB.starttime" => new obj_predicate_compare(
										OBJ_COMP_BETWEEN_INCLUDING,
										$from,
										$to,
										"int"
									),
								),
							)),
							new object_list_filter(array(
								"logic" => "AND",
								"conditions" => array(
									"CL_MRP_JOB.state" => array(MRP_STATUS_INPROGRESS,MRP_STATUS_PAUSED,MRP_STATUS_SHIFT_CHANGE),
									"CL_MRP_JOB.started" => new obj_predicate_compare(
										OBJ_COMP_BETWEEN_INCLUDING,
										$from,
										$to,
										"int"
									),
								),
							)),
						),
					));
					break;
			}
		}

		$clientspan = automatweb::$request->arg("clientspan");
		if($this->can("view", $clientspan) && $arr["prop"]["type"] != "treeview")
		{
			$clientspan_obj = obj($clientspan);
			if($clientspan_obj->is_a(CL_CRM_COMPANY))
			{
				$filt["CL_MRP_JOB.project(CL_MRP_CASE).customer"] = $clientspan;
			}
			elseif($clientspan_obj->is_a(CL_CRM_CATEGORY))
			{
				// Whattabout alamkategooriad?? Fuck.
				$filt["CL_MRP_JOB.project(CL_MRP_CASE).customer(CL_CRM_COMPANY).RELTYPE_CUSTOMER(CL_CRM_CATEGORY).id"] = $clientspan;
			}
		}
		elseif(!empty($clientspan) && $arr["prop"]["type"] != "treeview")
		{
			$filt["CL_MRP_JOB.project(CL_MRP_CASE).customer(CL_CRM_COMPANY).name"] = $clientspan."%";
		}

		return get_instance(CL_MRP_WORKSPACE)->get_next_jobs_for_resources(array(
			"resources" => (array)$arr["obj_inst"]->id(),
			"states" => $applicable_states,
			"sort_by" => "mrp_job.started",
			"proj_states" => $applicable_project_states,
			"filter" => $filt,
		));
	}

	function do_db_upgrade($t, $f)
	{
		switch($f)
		{
			case "aw_error_pct":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "double",
				));
				return true;
		}
		return false;
	}

	function _get_ability_toolbar($arr)
	{
		$tb =& $arr["prop"]["vcl_inst"];
		$tb->add_new_button(array(CL_MRP_RESOURCE_ABILITY), $arr["obj_inst"]->id(), 7 /* RESOURCE_ABILITY_ENTRY */);
		$tb->add_delete_button();
	}

	function _get_ability_tbl($arr)
	{
		$arr["prop"]["vcl_inst"]->table_from_ol(
			new object_list($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_RESOURCE_ABILITY_ENTRY"))),
			array("name", "createdby_person", "created", "act_from", "act_to", "format", "ability_per_hr"),
			CL_MRP_RESOURCE_ABILITY
		);
		$arr["prop"]["vcl_inst"]->set_caption(sprintf(t("Ressursi %s j&otilde;udlused formaatide ja aja l&otilde;ikes"), $arr["obj_inst"]->name()));
	}
}
