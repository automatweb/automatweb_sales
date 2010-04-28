<?php

namespace automatweb;
/*

@classinfo syslog_type=ST_MRP_JOB relationmgr=yes no_status=1 confirm_save_data=1 maintainer=voldemar

@tableinfo mrp_job index=oid master_table=objects master_index=oid
@tableinfo mrp_schedule index=oid master_table=objects master_index=oid
@tableinfo mrp_job_rows index=aw_job_id master_table=objects master_index=brother_of
@tableinfo mrp_job_progress index=aw_job_id master_table=objects master_index=brother_of

@groupinfo workflow caption="T&ouml;&ouml;voog"
@groupinfo materials caption="Materjalid"

@property job_toolbar type=toolbar no_caption=1 store=no group=general,workflow

@default group=general

	@layout general_split type=hbox width=25%:75%

		@layout general_left type=vbox parent=general_split closeable=1 area_caption=T&ouml;&ouml;&nbsp;&uuml;ldandmed

			@property project_name type=text parent=general_left store=no
			@caption Projekti nr:

			@property project_comment type=text parent=general_left store=no
			@caption Projekti nimetus:

			@property project_due_date type=text parent=general_left store=no
			@caption Projekti valmimist&auml;htaeg:

			@property project_customer type=text parent=general_left store=no
			@caption Klient:

			@property resource type=text parent=general_left table=mrp_job
			@caption Ressurss:

			@property prerequisites type=text parent=general_left table=mrp_job
			@caption Eeldust&ouml;&ouml;d:

		@layout general_right type=vbox parent=general_split

			@layout general_left_general type=vbox parent=general_right area_caption=Nimi&nbsp;ja&nbsp;kommentaarid closeable=1

				@property name type=text parent=general_left_general
				@caption Nimi

				@property comment type=textarea table=objects field=comment rows=5 parent=general_left_general
				@caption Tootmise kommentaar

@default table=mrp_job

				@property sales_comment type=textarea field=aw_sales_comment rows=5 parent=general_left_general
				@caption M&uuml;&uuml;gi kommentaar

			@layout general_left_time type=vbox parent=general_right area_caption=Ajalised&nbsp;seaded closeable=1

				@property length type=textbox default=0 parent=general_left_time
				@caption T&ouml;&ouml; pikkus (h)

				@property pre_buffer type=textbox default=0 parent=general_left_time
				@caption Eelpuhveraeg (h)

				@property post_buffer type=textbox default=0 parent=general_left_time
				@caption J&auml;relpuhveraeg (h)

				@property minstart type=datetime_select parent=general_left_time
				@comment Enne seda kuup&auml;eva, kellaaega ei alustata t&ouml;&ouml;d
				@caption Varaseim alustusaeg

				@property remaining_length type=textbox default=0 parent=general_left_time
				@comment Arvatav ajakulu t&ouml;&ouml; j&auml;reloleva osa tegemiseks
				@caption L&otilde;petamiseks kuluv aeg (h)

			@layout general_left_quantity type=vbox parent=general_right area_caption=T&uuml;kiarvestuse&nbsp;seaded closeable=1

				@property component_quantity type=textbox datatype=int default=1 parent=general_left_quantity
				@comment Tellimuse eksemplari jaoks vajalik selle t&ouml;&ouml; eksemplaride arv (mitu selle t88 tulemusel valmivat komponenti/eksemplari vaja yhe l6ppeksemplari jaoks)
				@caption Vajalik eksemplaride arv

				@property batch_size type=textbox default=1 datatype=int parent=general_left_quantity
				@comment Mitut eksemplari selles t&ouml;&ouml;s soovitakse k&auml;sitleda partiina. Vaikimisiv&auml;&auml;rtus m&auml;&auml;ratud ressursi juures.
				@caption T&ouml;&ouml;tluspartii suurus

				@property min_batches_to_continue_wf type=textbox default=0 datatype=int parent=general_left_quantity
				@comment 0 - kogu tellimuse maht
				@caption Partiisid j&auml;rgmisel ressursil alustamiseks


@default group=workflow
	@property workflow_errors type=text store=no no_caption=1

@default table=mrp_schedule
	@property starttime type=text
	@caption Plaanitud t&ouml;&ouml;sseminekuaeg

	@property planned_length type=text
	@caption Planeeritud kestus (h)


@default table=mrp_job
	@property person type=relpicker reltype=RELTYPE_PERSON multiple=1 store=connect
	@caption Isikud, kes on seda t&ouml;&ouml;d teinud

	@property started type=text
	@caption Alustatud

	@property finished type=text
	@caption L&otilde;petatud

	@property aborted type=text
	@caption Katkestatud

	@property done type=text default=0 datatype=int
	@caption Valminud eksemplare

	@property project type=hidden
	@caption Projekt

	@property real_length type=hidden
	@caption Tegelik kestus

	@property length_deviation type=hidden
	@caption Kestuse absoluutne h&auml;lve

	@property exec_order type=hidden
	@caption T&ouml;&ouml; jrk. nr.

	@property state type=text
	@caption Staatus

@default group=materials

	@property real_material_table type=table store=no no_caption=1
	@caption Materjalikulu

	@property materials_sel_tbl type=table no_caption=1

	@property materials_tbl type=table no_caption=1

@default table=objects
@default field=meta
@default method=serialize
	@property advised_starttime type=datetime_select
	@comment Allhankijaga kokkulepitud aeg, millal t&ouml;&ouml; alustada.
	@caption Soovitav algusaeg

	@property used_materials type=hidden



// --------------- RELATION TYPES ---------------------

@reltype MRP_PROJECT value=2 clid=CL_MRP_CASE
@caption Projekt

@reltype PERSON value=3 clid=CL_CRM_PERSON
@caption Isik, kes on seda t&ouml;&ouml;d teinud

*/

/*

CREATE TABLE `mrp_job` (
  `oid` int(11) NOT NULL default '0',
  `length` int(10) unsigned NOT NULL default '0',
  `planned_length` int(10) unsigned NOT NULL default '0',
  `resource` int(11) unsigned default NULL,
  `workspace` int(11) unsigned default NULL,
  `exec_order` smallint(5) unsigned NOT NULL default '1',
  `project` int(11) unsigned default NULL,
  `minstart` int(10) unsigned default NULL,
  `starttime` int(10) unsigned default NULL,
  `prerequisites` char(255) default NULL,
  `state` tinyint(2) unsigned default '1',
  `pre_buffer` int(10) unsigned default NULL,
  `post_buffer` int(10) unsigned default NULL,
  `started` int(10) unsigned default NULL,
  `finished` int(10) unsigned default NULL,
  `aborted` int(10) unsigned default NULL,
  `remaining_length` int(10) unsigned default NULL,

	PRIMARY KEY  (`oid`)
) TYPE=MyISAM;

CREATE TABLE `mrp_schedule` (
	`oid` int(11) NOT NULL default '0',
	`planned_length` int(10) unsigned NOT NULL default '0',
	`starttime` int(10) unsigned default NULL,

	PRIMARY KEY  (`oid`)
) TYPE=MyISAM;

*/

require_once "mrp_header.aw";

class mrp_job extends class_base
{
	const AW_CLID = 826;

	var $mrp_error = false;
	protected $project;
	protected $resource;
	protected $workspace;

	function mrp_job ()
	{
		$this->init(array(
			"tpldir" => "mrp/mrp_job",
			"clid" => CL_MRP_JOB,
		));
	}

	function callback_on_load ($arr)
	{
		if (!is_oid ($arr["request"]["id"]))
		{
			$this->mrp_error .= t("Uut t&ouml;&ouml;d saab luua vaid ressursihalduskeskkonnas. ");
		}
		else
		{
			$this_object = obj($arr["request"]["id"]);
			$project_id = $this_object->prop ("project");
			$resource_id = $this_object->prop ("resource");

			if ( $this->can("view", $project_id) and $this->can("view", $resource_id) )
			{
				$this->project = obj ($project_id);
				$this->resource = obj ($resource_id);
				$this->workspace = $this->project->prop("workspace");

				if (!$this->workspace or !$this->project or !$this->resource)
				{
					$this->mrp_error .= t("T&ouml;&ouml;l puudub ressurss, projekt v&otilde;i ressursihalduskeskkond. ");
				}
			}
			else
			{
				if (is_oid($project_id))
				{
					$this->mrp_error .= t("T&ouml;&ouml;l puudub ressurss. ");
				}
				elseif (is_oid($resource_id))
				{
					$this->mrp_error .= t("T&ouml;&ouml;l puudub projekt. ");
				}
				else
				{
					$this->mrp_error .= t("T&ouml;&ouml;l puudub ressurss ja projekt. ");
				}
			}
		}

		if ($this->mrp_error)
		{
			echo t("Viga! ") . $this->mrp_error;
		}
	}

	function init_storage_object($arr)
	{
		$resource = "";
		$arr["constructor_args"] = array("resource" => $resource);
		return parent::init_storage_object($arr);
	}

	function get_property ($arr)
	{
		if ($this->mrp_error)
		{
			return PROP_IGNORE;
		}

		$this_object = $arr["obj_inst"];
		$prop =& $arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
			case "project_name":
			case "project_comment":
				$prop["value"] = obj($arr["obj_inst"]->prop("project"))->prop(substr($prop["name"], 8));
				break;

			case "project_customer":
				$prop["value"] = obj($arr["obj_inst"]->prop("project"))->prop("customer.name");
				break;

			case "project_due_date":
				$prop["value"] = aw_locale::get_lc_date(obj($arr["obj_inst"]->prop("project"))->prop("due_date"), 7);
				break;

			case "person":
				$ids = array_merge(array(-1), safe_array(ifset($prop, "value")));
				$ol = new object_list(array(
					"oid" => $ids,
				));
				$prop["type"] = "text";
				$prop["value"] = implode(", ", $ol->names());
				unset($prop["post_append_text"]);
				break;

			case "prerequisites":
				try
				{
					$prerequisites = $this_object->awobj_get_prerequisites()->arr();
					$prerequisite_orders = array();

					foreach ($prerequisites as $prerequisite)
					{
						$prerequisite_orders[] = $prerequisite->prop ("exec_order");
					}

					$prop["value"] = implode (",", $prerequisite_orders);
				}
				catch (\Exception $e)
				{
					$prop["error"] = t("Eeldust&ouml;&ouml;de definitsioon katki v&otilde;i puudub osale neist juurdep&auml;&auml;s");
					$retval = PROP_ERROR;
				}
				break;

			case "resource":
				if (is_object($this->resource))
				{
					$prop["value"] = html::get_change_url(
						$this->resource->id(),
						array("return_url" => get_ru()),
						$this->resource->name ()
					);
				}
				break;

			case "workflow_errors":
				if (!empty ($arr["request"]["errors"]))
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

			case "length":
			case "planned_length":
			case "pre_buffer":
			case "post_buffer":
			case "remaining_length":
				$prop["value"] = round (($prop["value"] / 3600), 2);
				break;

			case "advised_starttime":
				if ($this->resource->prop("type") != mrp_resource_obj::TYPE_SUBCONTRACTOR)
				{
					return PROP_IGNORE;
				}
				break;

			case "state":

				$prop["value"] = "<span style='padding: 5px; background: ".mrp_workspace::$state_colours[$prop["value"]]."'>".mrp_job_obj::get_state_names($prop["value"])."<span>";
				break;

			case "starttime":
				$can_start = $this_object->can_start(false, true);
				$can_start = $can_start === true ? "" : (t(" Ei saa alustada sest: ") . $can_start);
				$prop["value"] = $prop["value"] ? date(MRP_DATE_FORMAT, $prop["value"]) . $can_start : t("Planeerimata");
				break;

			case "started":
				$prop["value"] = $prop["value"] ? date(MRP_DATE_FORMAT, $prop["value"]) : t("T&ouml;&ouml;d pole veel alustatud");
				break;

			case "finished":
				$prop["value"] = ($this_object->prop ("state") == mrp_job_obj::STATE_DONE) ? date(MRP_DATE_FORMAT, $prop["value"]) : t("T&ouml;&ouml;d pole veel l&otilde;petatud");
				break;

			case "aborted":
				if($this_object->prop("state") == mrp_job_obj::STATE_ABORTED)
				{
					$prop["value"] = date(MRP_DATE_FORMAT, $prop["value"]);
				}
				else
				{
					return PROP_IGNORE;
				}
				break;

			case "job_toolbar":
				$this->create_job_toolbar ($arr);
				break;

			case "real_material_table":
				$this->draw_expense_list_table($arr["prop"]["vcl_inst"] , $arr["obj_inst"]);
				break;

			case "materials_sel_tbl":
				$this->create_materials_sel_tbl($arr);
				break;

			case "materials_tbl":
				$this->create_materials_tbl($arr);
				break;
		}

		return $retval;
	}

	function set_property ($arr = array())
	{
		if ($this->mrp_error)
		{
			return PROP_FATAL_ERROR;
		}

		$this_object = $arr["obj_inst"];
		$prop =& $arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
			case "person":
				return PROP_IGNORE;

			case "advised_starttime":
				if ($this->resource->prop("type") != mrp_resource_obj::TYPE_SUBCONTRACTOR)
				{
					return PROP_IGNORE;
				}
				break;

			case "length":
			case "pre_buffer":
			case "post_buffer":
				$prop["value"] = round ($prop["value"] * 3600);
				break;

			case "materials_tbl":
				$arr["obj_inst"]->save_materials($arr["request"]);
				break;
		}

		return $retval;
	}

	function callback_post_save ($arr)
	{
		$this->workspace->save ();
	}

	function create_job_toolbar ($arr = array())
	{
		$toolbar = $arr["prop"]["toolbar"];
		$this_object = $arr["obj_inst"];

		### start button
		if ($this_object->can_start())
		{
			$disabled = false;
		}
		else
		{
			$disabled = true;
		}

		$toolbar->add_button(array(
			"name" => "start",
			"tooltip" => t("Alusta"),
			"action" => "start",
			"confirm" => t("Oled kindel et soovid t&ouml;&ouml;d alustada?"),
			"disabled" => $disabled
		));

		### done, abort, pause, end_shift buttons
		if ($this_object->prop ("state") == mrp_job_obj::STATE_INPROGRESS)
		{
			$disabled_inprogress = false;
		}
		else
		{
			$disabled_inprogress = true;
		}

		// done quantity feedback buttons
		foreach ($this->resource->prop("production_feedback_option_values") as $value)
		{
			$url = $this->mk_my_orb("done", array(
				"id" => $this_object->id(),
				"quantity" => $value * $this_object->prop("batch_size"),
				"return_url" => get_ru()
			), "mrp_job");
			$toolbar->add_button(array(
				"name" => "done{$value}",
				"tooltip" => $value . ($value == 1 ? t(" partii") : t(" partiid")),
				"url" => $url,
				"disabled" => $disabled_inprogress
			));
		}

		$toolbar->add_button(array(
			"name" => "done",
			"tooltip" => t("Valmis"),
			"action" => "done",
			"confirm" => t("Oled kindel et soovid t&ouml;&ouml;d l&otilde;petada?"),
			"disabled" => $disabled_inprogress
		));
		$toolbar->add_button(array(
			"name" => "pause",
			"tooltip" => t("Paus"),
			"action" => "pause",
			"confirm" => t("Oled kindel et soovid t&ouml;&ouml;d pausile panna?"),
			"disabled" => $disabled_inprogress
		));
		$toolbar->add_button(array(
			"name" => "end_shift",
			"confirm" => t("L&otilde;peta vahetus ja logi v&auml;lja?"),
			"tooltip" => t("Vahetuse l&otilde;pp"),
			"action" => "end_shift",
			"disabled" => $disabled_inprogress
		));

		### continue button
		if ($this_object->prop("state") == mrp_job_obj::STATE_PAUSED || $this_object->prop("state") == mrp_job_obj::STATE_SHIFT_CHANGE)
		{
			$disabled = false;
			$action = "scontinue";
		}
		elseif ($this_object->prop("state") == mrp_job_obj::STATE_ABORTED)
		{
			$disabled = false;
			$action = "acontinue";
		}
		else
		{
			$action = "";
			$disabled = true;
		}

		$toolbar->add_button(array(
			"name" => "continue",
			"tooltip" => t("J&auml;tka"),
			"action" => $action,
			"disabled" => $disabled,
			"confirm" => t("Oled kindel et soovid t&ouml;&ouml;d j&auml;tkata?"),
		));

		$toolbar->add_button(array(
			"name" => "abort",
			"tooltip" => t("Katkesta"),
			//"action" => "abort",
			"url" => "#",
			"confirm" => t("Katkesta t&ouml;&ouml;?"),
			"onClick" => "if (document.changeform.pj_change_comment.value.replace(/\\s+/, '') != '') { submit_changeform('abort') } else { alert('" . t("Kommentaar peab olema t&auml;idetud!") . "'); }",
			"disabled" => $disabled_inprogress,
		));

		$toolbar->add_button(array(
			"name" => "save_material",
			"tooltip" => t("Salvesta materjali kulu"),
			"caption" => html::div(array(
				"content" => t("Salvesta materjali kulu"),
				"id" => "save_pr_material_button",
			)),
			"onClick" => "
				$.ajax({
					type: 'POST',
					url: '/automatweb/orb.aw?class=mrp_job&action=js_post_material',
					data: $('input[name^=material_amount]').serialize()  + '&' + $('input[name=pj_job]').serialize()  + '&' + $('input[name=id]').serialize(),
					success: function(msg){
						alert('".t("Materjali kulu salvestatud")."' + msg);
					}
				});
				x=document.getElementById('save_pr_material_button');
				x.innerHTML='".t("Salvesta materjali kulu")."';
			",
		));

	}

	function state_changed($job, $com) // DEPRECATED
	{ $job->state_changed($com); }

	function stats_start($job) // DEPRECATED
	{ $job->stats_start(); }

	function stats_done($job) // DEPRECATED
	{ $job->stats_done(); }


/**
	@attrib name=start
	@param id required type=int
	@param return_url optional type=string
**/
	function start ($arr)
	{
		$errors = array ();
		$return_url = empty($arr["return_url"]) ? $this->mk_my_orb("change", array(
			"id" => $arr["id"],
			"return_url" => $arr["return_url"],
			"group" => $arr["group"],
			"subgroup" => $arr["subgroup"],
		), "mrp_job") : $arr["return_url"];

		if (is_oid ($arr["id"]))
		{
			$this_object = obj ($arr["id"]);
		}
		else
		{
			$errors[] = t("T&ouml;&ouml; id vale");
			$errors = (serialize($errors));
			return aw_url_change_var ("errors", $errors, $return_url);
		}

		try
		{
			$this_object->load_data();
			$comment = isset($arr["pj_change_comment"]) ? $arr["pj_change_comment"] : "";
			$this_object->start($comment);
		}
		catch (awex_mrp_case_state $e)
		{
			$errors[] = t("Projekt pole t&ouml;&ouml;s ega planeeritud");
		}
		catch (awex_mrp_job_state $e)
		{
			$errors[] = t("T&ouml;&ouml; pole planeeritud");
		}
		catch (awex_mrp_job_prerequisites $e)
		{
			$errors[] = t("Eeldust&ouml;&ouml;d tegemata");
		}
		catch (awex_mrp_resource_unavailable $e)
		{
			$errors[] = t("Ressurss kinni");
		}
		catch (awex_mrp_case $e)
		{
			$errors[] = t("Projekti alustamine eba&otilde;nnestus");
		}
		catch (awex_mrp_resource $e)
		{
			$errors[] = t("Ressurssil esines viga");
		}
		catch (\Exception $e)
		{
			$errors[] = t("Tundmatu viga");
		}

		if ($errors)
		{
			$errors = (serialize($errors));
			$return_url = aw_url_change_var ("errors", $errors, $return_url);
		}

		return $return_url;
	}



/**
	@attrib name=done
	@param id required type=int
	@param quantity optional type=int
	@param return_url optional type=string
**/
	function done ($arr)
	{
		$errors = array ();
		$return_url = $arr["return_url"];

		if (is_oid ($arr["id"]))
		{
			$this_object = obj ($arr["id"]);
		}
		else
		{
			$errors[] = t("T&ouml;&ouml; id vale");
			$errors = (serialize($errors));
			return aw_url_change_var ("errors", $errors, $return_url);
		}

		$quantity = empty($arr["quantity"]) ? null : $arr["quantity"];
		$resource = new object($this_object->prop("resource"));
		$options = $resource->prop("production_feedback_option_values");
		if (null !== $quantity and !in_array($quantity/$this_object->prop("batch_size"), $options))
		{
			$errors[] = t("Sellist tk. arvestuse valikut ei ole");
			$errors = (serialize($errors));
			return aw_url_change_var ("errors", $errors, $return_url);
		}

		try
		{
			$this_object->load_data();
			$comment = isset($arr["pj_change_comment"]) ? $arr["pj_change_comment"] : "";
			$this_object->done($quantity, $comment);

			foreach (safe_array($arr["material_amount"]) as $prod => $amount)
			{
				$this_object->set_used_material(array(
					"product" => $prod,
					"amount" => $amount
				));
			}
		}
		catch (awex_mrp_job_state $e)
		{
			$errors[] = t("T&ouml;&ouml; staatus sobimatu");
		}
		catch (awex_mrp_resource $e)
		{
			$errors[] = t("Ressurssil esines viga");
		}
		catch (\Exception $e)
		{
			$errors[] = t("Tundmatu viga");
		}

		if ($errors)
		{
			$errors = (serialize($errors));
			$return_url = aw_url_change_var ("errors", $errors, $return_url);
		}

		return $return_url;
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
		), "mrp_job");

		if (is_oid ($arr["id"]))
		{
			$this_object = obj ($arr["id"]);
		}
		else
		{
			$errors[] = t("T&ouml;&ouml; id vale");
			$errors = (serialize($errors));
			return aw_url_change_var ("errors", $errors, $return_url);
		}

		try
		{
			$this_object->load_data();
			$comment = isset($arr["pj_change_comment"]) ? $arr["pj_change_comment"] : "";
			$this_object->abort($comment);
		}
		catch (awex_mrp_job_state $e)
		{
			$errors[] = t("T&ouml;&ouml; pole tegemisel");
		}
		catch (awex_mrp_resource_unavailable $e)
		{
			$errors[] = t("Ressurss kinni");
		}
		catch (awex_mrp_case $e)
		{
			$errors[] = t("Projekti alustamine eba&otilde;nnestus");
		}
		catch (awex_mrp_resource $e)
		{
			$errors[] = t("Ressurssil esines viga. Ressursi vabastamine v&otilde;is eba&otilde;nnestuda");
		}
		catch (\Exception $e)
		{
			$errors[] = t("Tundmatu viga");
		}

		if ($errors)
		{
			$errors = (serialize($errors));
			$return_url = aw_url_change_var ("errors", $errors, $return_url);
		}

		return $return_url;
	}

/**
	@attrib name=pause
	@param id required type=int
	@param return_url optional type=string
**/
	function pause($arr)
	{
		$errors = array ();
		$return_url = empty($arr["return_url"]) ? $this->mk_my_orb("change", array(
			"id" => $arr["id"],
			"return_url" => $arr["return_url"],
			"group" => $arr["group"],
			"subgroup" => $arr["subgroup"],
		), "mrp_job") : $arr["return_url"];

		if (is_oid ($arr["id"]))
		{
			$this_object = obj ($arr["id"]);
		}
		else
		{
			$errors[] = t("T&ouml;&ouml; id vale");
			$errors = (serialize($errors));
			return aw_url_change_var ("errors", $errors, $return_url);
		}

		try
		{
			$this_object->load_data();
			$comment = isset($arr["pj_change_comment"]) ? $arr["pj_change_comment"] : "";
			$this_object->pause($comment);
		}
		catch (awex_mrp_job_state $e)
		{
			$errors[] = t("T&ouml;&ouml; pole tegemisel");
		}
		catch (\Exception $e)
		{
			$errors[] = t("Tundmatu viga");
		}

		if ($errors)
		{
			$errors = (serialize($errors));
			$return_url = aw_url_change_var ("errors", $errors, $return_url);
		}

		return $return_url;
	}

/**
	@attrib name=scontinue
	@param id required type=int
	@param return_url optional type=string
**/
	function scontinue($arr)
	{
		$errors = array ();
		$return_url = empty($arr["return_url"]) ? $this->mk_my_orb("change", array(
			"id" => $arr["id"],
			"return_url" => $arr["return_url"],
			"group" => $arr["group"],
			"subgroup" => $arr["subgroup"],
		), "mrp_job") : $arr["return_url"];

		if (is_oid ($arr["id"]))
		{
			$this_object = obj ($arr["id"]);
		}
		else
		{
			$errors[] = t("T&ouml;&ouml; id vale");
			$errors = (serialize($errors));
			return aw_url_change_var ("errors", $errors, $return_url);
		}

		try
		{
			$this_object->load_data();
			$comment = isset($arr["pj_change_comment"]) ? $arr["pj_change_comment"] : "";
			$this_object->scontinue($comment);
		}
		catch (awex_mrp_job_state $e)
		{
			$errors[] = t("T&ouml;&ouml; pole pausil");
		}
		catch (\Exception $e)
		{
			$errors[] = t("Tundmatu viga");
		}

		if ($errors)
		{
			$errors = (serialize($errors));
			$return_url = aw_url_change_var ("errors", $errors, $return_url);
		}

		return $return_url;
	}

/**
	@attrib name=acontinue
	@param id required type=int
	@param return_url optional type=string
**/
	function acontinue($arr)
	{
		$errors = array ();
		$return_url = empty($arr["return_url"]) ? $this->mk_my_orb("change", array(
			"id" => $arr["id"],
			"return_url" => $arr["return_url"],
			"group" => $arr["group"],
			"subgroup" => $arr["subgroup"],
		), "mrp_job") : $arr["return_url"];

		if (is_oid ($arr["id"]))
		{
			$this_object = obj ($arr["id"]);
		}
		else
		{
			$errors[] = t("T&ouml;&ouml; id vale");
			$errors = (serialize($errors));
			return aw_url_change_var ("errors", $errors, $return_url);
		}

		try
		{
			$this_object->load_data();
			$comment = isset($arr["pj_change_comment"]) ? $arr["pj_change_comment"] : "";
			$this_object->acontinue($comment);
		}
		catch (awex_mrp_case_state $e)
		{
			$errors[] = t("Projekt pole j&auml;tkatav");
		}
		catch (awex_mrp_job_state $e)
		{
			$errors[] = t("T&ouml;&ouml; pole katkestatud");
		}
		catch (awex_mrp_job_prerequisites $e)
		{
			$errors[] = t("Eeldust&ouml;&ouml;d tegemata");
		}
		catch (awex_mrp_resource_unavailable $e)
		{
			$errors[] = t("Ressurss kinni");
		}
		catch (awex_mrp_case $e)
		{
			$errors[] = t("Projektil esines viga");
		}
		catch (awex_mrp_resource $e)
		{
			$errors[] = t("Ressurssil esines viga");
		}
		catch (\Exception $e)
		{
			$errors[] = t("Tundmatu viga");
		}

		if ($errors)
		{
			$errors = (serialize($errors));
			$return_url = aw_url_change_var ("errors", $errors, $return_url);
		}

		return $return_url;
	}

/**
	@attrib name=js_post_material all_args=1
**/
	public function js_post_material($arr)
	{
		if(!$this->can("view" , $arr["pj_job"]))
		{
			$arr["pj_job"] = $arr["id"];
		}
		$job = obj($arr["pj_job"]);
		foreach($arr["material_amount"] as $prod => $amount)
		{
			$job->set_used_material_amount(obj($prod),$amount);
		}
		die();
	}

/**
	@attrib name=end_shift
	@param id required type=int
	@param return_url optional type=string
**/
	function end_shift($arr)
	{
		$errors = array ();
		$return_url = empty($arr["return_url"]) ? $this->mk_my_orb("change", array(
			"id" => $arr["id"],
			"return_url" => $arr["return_url"],
			"group" => $arr["group"],
			"subgroup" => $arr["subgroup"],
		), "mrp_job") : $arr["return_url"];

		if (is_oid ($arr["id"]))
		{
			$this_object = obj ($arr["id"]);
		}
		else
		{
			$errors[] = t("T&ouml;&ouml; id vale");
			$errors = (serialize($errors));
			return aw_url_change_var ("errors", $errors, $return_url);
		}

		try
		{
			$this_object->load_data();
			$comment = isset($arr["pj_change_comment"]) ? $arr["pj_change_comment"] : "";
			$this_object->end_shift($comment);
		}
		catch (awex_mrp_job_state $e)
		{
			$errors[] = t("T&ouml;&ouml; pole tegemisel");
		}
		catch (\Exception $e)
		{
			$errors[] = t("Tundmatu viga");
		}

		if ($errors)
		{
			$errors = (serialize($errors));
			$return_url = aw_url_change_var ("errors", $errors, $return_url);
		}

		return $return_url;
	}

	function job_prerequisites_are_done($arr) // DEPRECATED
	{ if (is_oid ($arr["job"])) { $job = obj ($arr["job"]); } else { return false; } return $job->job_prerequisites_are_done(); }

/**
    @attrib name=can_start
	@param job required type=int
**/
	function can_start ($arr) // DEPRECATED
	{ if (is_oid ($arr["job"])) { $job = obj ($arr["job"]); } else { return false; } return $job->can_start(); }

	function add_comment($job, $comment) //DEPRECATED
	{  $job = obj($job); $job->add_comment($comment); }

	function init_materials_tbl($t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Materjal"),
			"chgbgcolor" => "color",
		));
		$t->define_field(array(
			"name" => "amount",
			"caption" => t("Planeeritud kogus"),
			"align"=> "center",
			"chgbgcolor" => "color",
		));
		$t->define_field(array(
			"name" => "unit",
			"caption" => t("&Uuml;hik"),
			"align"=> "center",
			"chgbgcolor" => "color",
		));
		$t->define_field(array(
			"name" => "planning",
			"caption" => t("Tarnetingimus planeerimisel"),
			"align"=> "center",
			"chgbgcolor" => "color",
		));
		$t->define_field(array(
			"name" => "movement",
			"caption" => t("Materjali liikumine materjalilaost"),
			"align"=> "center",
			"chgbgcolor" => "color",
		));
		$t->set_rgroupby(array("category" => "category"));
	}

	function create_materials_sel_tbl($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		$this->init_materials_tbl(&$t);
		$t->set_caption(sprintf(t("Tooteretsepti x poolt t&ouml;&ouml;s %s kasutatavad materjalid"), $arr["obj_inst"]->name()));
		$conn = $arr["obj_inst"]->connections_to(array(
			"from.class_id" => CL_MATERIAL_EXPENSE,
		));
		$mec_o = obj(null, array(), CL_MATERIAL_EXPENSE_CONDITION);
		foreach($conn as $c)
		{
			$prod = $c->from()->prop("product");
			$po = obj($prod, array(), CL_SHOP_PRODUCT);
			$unitselect = self::get_materials_unitselect($po, $c->from()->prop("unit"));
			$t->define_data(array(
				"name" => html::obj_change_url($po),
				"amount" => html::textbox(array(
					"name" => "amount[".$prod."]",
					"size" => 4,
					"value" => $c->from()->prop("amount"),
				)),
				"unit" => $unitselect,
				"category" => ($cat = $po->get_first_obj_by_reltype("RELTYPE_CATEGORY")) ? $cat->name() : "",
				"color" => "#EAEAEA",
				"planning" => html::select(array(
					"name" => "planning[".$prod."]",
					"options" => $mec_o->planning_options(),
					"value" => $c->from()->prop("planning"),
				)),
				"movement" => html::select(array(
					"name" => "movement[".$prod."]",
					"options" => $mec_o->movement_options(),
					"value" => $c->from()->prop("movement"),
				)),
			));
		}
	}

	function create_materials_tbl($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->init_materials_tbl(&$t);
		$res = obj($arr["obj_inst"]->prop("resource"));
		if($res)
		{
			$t->set_caption(sprintf(t("Ressursil %s kasutatavad materjalid"), $res->name()));
			$conn = $res->connections_to(array(
				"from.class_id" => CL_MATERIAL_EXPENSE_CONDITION,
			));
			$conn2 = $arr["obj_inst"]->connections_to(array(
				"from.class_id" => CL_MATERIAL_EXPENSE,
			));
			$has_ids = array();
			foreach($conn2 as $c)
			{
				$prod = $c->from()->prop("product");
				$has_ids[$prod] = $prod;
			}
			foreach($conn as $c)
			{
				$prod = $c->from()->prop("product");
				if(isset($has_ids[$prod]))
				{
					continue;
				}
				if(!$prod)
				{
					continue;
				}
				$po = obj($prod);
				$unitselect = self::get_materials_unitselect($po);
				$t->define_data(array(
					"name" => html::obj_change_url($po),
					"amount" => html::textbox(array(
						"name" => "amount[".$prod."]",
						"size" => 4,
						"value" => 0,
					)),
					"unit" => $unitselect,
					"category" => ($cat = $po->get_first_obj_by_reltype("RELTYPE_CATEGORY")) ? $cat->name() : "",
					"color" => "#EAEAEA",
					"planning" => html::select(array(
						"name" => "planning[".$prod."]",
						"options" => $c->from()->planning_options(),
						"value" => $c->from()->prop("planning"),
					)),
					"movement" => html::select(array(
						"name" => "movement[".$prod."]",
						"options" => $c->from()->movement_options(),
						"value" => $c->from()->prop("movement"),
					)),
				));
			}
		}
	}

	public static function get_materials_unitselect($po, $value = null, $set_job_oid = false, $name_template = false)
	{
		enter_function("mrp_job::get_materials_unitselect");

		if(!$name_template)
		{
			$name_template = $set_job_oid ? "jobs[$set_job_oid][unit][%u]" : "unit[%u]";
		}

		$units = $po->instance()->get_units($po);
		foreach($units as $i => $unit)
		{
			if(!$unit)
			{
				unset($units[$i]);
			}
			else
			{
				$unitopts[$unit] = obj($unit)->name();
			}
		}
		if(count($units) == 1)
		{
			$_name = sprintf($name_template, $po->id());
			$unitselect = obj(reset($units))->name().html::hidden(array(
				"name" => $_name,
				"value" => reset($units),
			));
		}
		elseif(count($units))
		{
			$unitselect = "";
			foreach($unitopts as $unit => $name)
			{
				$_name = sprintf($name_template, $po->id());
				$unitselect .= html::radiobutton(array(
					"name" => $_name,
					"value" => $unit,
					"checked" => $value ? ($value == $unit) : ($unit == $units[0]),
				)).$name." ";
			}
		}
		else
		{
			$unitselect = "-";
		}
		exit_function("mrp_job::get_materials_unitselect");
		return $unitselect;
	}

	function draw_expense_list_table($t , $job)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Materjal"),
			"sortable" => 1,
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "prog_expense",
			"caption" => t("Prognoositud kulu"),
			"sortable" => 1,
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "expense",
			"caption" => t("Tegelik kulu"),
			"sortable" => 1,
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "unit",
			"caption" => t("&Uuml;hik"),
			"sortable" => 1,
			"align" => "center"
		));

		$t->set_default_sortby ("name");
		$t->set_caption(t("Materjali kulu"));
		$t->set_default_sorder ("asc");
		foreach($job->get_material_expense_list() as $id => $material)
		{
			$t->define_data(array(
				"name" => $material->name(),
				"prog_expense" => $material->prop("amount"),
				"expense" => html::textbox(array(
					"name" => "material_amount[".$id."]",
					"size" => 5,
					"value" => $material->prop("used_amount"),
					"onChange" => "x=document.getElementById('save_pr_material_button');
						x.innerHTML='"."<font color=red>".t("Salvesta materjali kulu")."</font>"."';",
				)),
				"unit" => $material->prop("unit.name"),
			));
		}

	}

	function do_db_upgrade($table, $field, $q, $err)
	{
		if ("mrp_job" === $table)
		{
			ini_set("ignore_user_abort", "1");

			switch($field)
			{
				case "workspace":
				case "done":
					$this->db_add_col($table, array(
						"name" => $field,
						"type" => "INT(11) UNSIGNED"
					));
					return true;

				case "aborted":
				case "real_length":
					$this->db_add_col($table, array(
						"name" => $field,
						"type" => "INT(10) UNSIGNED"
					));
					return true;

				case "component_quantity":
				case "batch_size":
					$this->db_add_col($table, array(
						"name" => $field,
						"type" => "INT(10) UNSIGNED NOT NULL DEFAULT 1"
					));
					return true;

				case "min_batches_to_continue_wf":
					$this->db_add_col($table, array(
						"name" => $field,
						"type" => "INT(10) UNSIGNED NOT NULL DEFAULT 0"
					));
					return true;

				case "length_deviation":
					$this->db_add_col($table, array(
						"name" => $field,
						"type" => "INT(10)"
					));
					return true;

				case "aw_sales_comment":
					$this->db_add_col($table, array(
						"name" => $field,
						"type" => "varchar(255)"
					));
					return true;
			}
		}
		elseif("mrp_job_rows" === $table)
		{
			switch($field)
			{
				case "":
					$this->db_query("CREATE TABLE mrp_job_rows (
						aw_row_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
						aw_job_id INT NOT NULL,
						aw_case_id INT NOT NULL,
						aw_resource_id INT NOT NULL,
						aw_uid CHAR(50) NOT NULL,
						aw_uid_oid INT NOT NULL,
						aw_previous_pid INT NOT NULL,
						aw_pid INT NOT NULL,
						aw_job_previous_state TINYINT(2) UNSIGNED NOT NULL,
						aw_job_state TINYINT(2) UNSIGNED NOT NULL,
						aw_job_last_duration INT(10) UNSIGNED NOT NULL,
						aw_tm INT NOT NULL
					);");
					return true;

				case "aw_previous_pid":
					$this->db_add_col($table, array(
						"name" => $field,
						"type" => "INT",
					));
					$this->db_query("
					UPDATE
						mrp_job_rows c,
						mrp_job_rows p
					SET
						c.aw_previous_pid = p.aw_pid
					WHERE
						p.aw_job_id = c.aw_job_id AND
						p.aw_job_state = c.aw_job_previous_state AND
						c.aw_tm BETWEEN (p.aw_tm + c.aw_job_last_duration - 5) AND (p.aw_tm + c.aw_job_last_duration + 5);");
					return true;
			}
		}
		elseif("mrp_job_progress" === $table)
		{
			switch($field)
			{
				case "":
					$this->db_query("CREATE TABLE mrp_job_progress (
						aw_row_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
						aw_job_id INT(11) UNSIGNED NOT NULL,
						aw_case_id INT(11) UNSIGNED NOT NULL,
						aw_resource_id INT(11) UNSIGNED NOT NULL,
						aw_uid_oid INT(11) UNSIGNED NOT NULL,
						aw_pid_oid INT(11) UNSIGNED NOT NULL,
						aw_quantity INT(11) UNSIGNED NOT NULL,
						aw_entry_time INT(11) UNSIGNED NOT NULL
					);");
					return true;

				case "aw_pid_oid":
					$this->db_add_col($table, array(
						"name" => $field,
						"type" => "INT(11) UNSIGNED NOT NULL",
					));
					break;
			}
		}
	}
}

?>
