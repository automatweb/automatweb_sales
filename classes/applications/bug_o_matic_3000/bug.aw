<?php
/*

@classinfo relationmgr=yes no_comment=1 no_status=1 r2=yes prop_cb=1

@tableinfo aw_bugs index=aw_id master_index=brother_of master_table=objects

@property created type=hidden table=objects field=created

@default group=general
@default table=aw_bugs

@property bug_tb type=toolbar no_caption=1 store=no



@layout name type=vbox closeable=1 area_caption=L&uuml;hikirjeldus

	@layout name_way type=hbox parent=name

	@property name type=textbox table=objects parent=name_way no_caption=1
	@property expl_txt type=text store=no no_caption=1 parent=name_way

@layout settings_wrap type=vbox closeable=1 area_caption=M&auml;&auml;rangud
@layout settings type=hbox parent=settings_wrap

	@layout settings_col1 type=vbox parent=settings
		@property bug_status type=select parent=settings_col1 captionside=top
		@caption Staatus

		@property bug_feedback_p type=relpicker reltype=RELTYPE_FEEDBACK_P parent=settings_col1 captionside=top field=aw_bug_feedback_p
		@caption Tagasiside kellelt

		@property bug_priority type=select parent=settings_col1 captionside=top
		@caption Prioriteet

		@property who type=crm_participant_search style=relpicker reltype=RELTYPE_MONITOR table=aw_bugs field=who parent=settings_col1 captionside=top
		@caption Kellele

		@property skill_used type=select table=aw_bugs field=skill_used parent=settings_col1 captionside=top
		@caption Kasutatav P&auml;devus

		@property is_order type=checkbox ch_value=1 parent=settings_col1
		@caption Arendustellimus


	@layout settings_col2 type=vbox parent=settings
		@property project type=relpicker reltype=RELTYPE_PROJECT parent=settings_col2 captionside=top
		@caption Projekt

		@property bug_type type=select table=aw_bugs field=bug_type captionside=top parent=settings_col2
		@caption T&uuml;&uuml;p

		@property bug_app type=select field=aw_bug_app captionside=top parent=settings_col2 table=aw_bugs
		@caption Rakendus

		@property bug_severity type=select parent=settings_col2 captionside=top
		@caption T&otilde;sidus

		@property bug_class type=select parent=settings_col2 captionside=top
		@caption Klass

		@property bug_property type=select parent=settings_col2 captionside=top field=aw_bug_property
		@caption Klassi omadus

	@layout settings_col3 type=vbox parent=settings
		@property monitors type=relpicker reltype=RELTYPE_MONITOR multiple=1 size=5 store=connect parent=settings_col3 captionside=top
		@caption J&auml;lgijad

		@property prognosis type=datepicker time=0 default=-1 parent=settings_col3 captionside=top
		@caption Prognoositav kuup&auml;ev

		@property deadline type=datepicker time=0 parent=settings_col3 captionside=top
		@caption T&auml;htaeg

		@property actual_live_date type=datepicker time=0 captionside=top parent=settings_col3 field=meta method=serialize table=objects default=-1
		@caption Tegelik Live kuup&auml;ev

		@property finance_type type=chooser captionside=top parent=settings_col3 table=aw_bugs field=aw_finance_type
		@caption Kulud kaetakse

		@property hr_price type=textbox size=10 captionside=top parent=settings_col3 table=aw_bugs field=aw_hr_price
		@caption Tunnihind

		@property send_bill type=checkbox ch_value=1 parent=settings_col3 table=aw_bugs field=aw_send_bill
		@caption Arvele

	@property vb_d1 type=hidden store=no no_caption=1 parent=settings


@layout url type=vbox closeable=1 area_caption=URL
	@property bug_url type=textbox size=80 no_caption=1 parent=url
	@caption URL


@layout content type=hbox width=20%:80%
	@layout bc type=vbox parent=content closeable=1 area_caption=Sisu
		@layout bc_lay1 parent=bc type=hbox

		@property bug_content type=textarea rows=23 cols=60 parent=bc_lay1 captionside=top no_caption=1
		@caption Sisu

		@property bug_comments type=text no_caption=1 parent=bc_lay1 store=no
		@caption Kommentaarid

		@layout bug_add_times type=hbox parent=bc

			@property bug_add_guess type=textbox size=5 parent=bug_add_times captionside=top store=no
			@caption Prognoosile lisanduv aeg

			@property bug_add_real type=textbox size=5 parent=bug_add_times captionside=top store=no
			@caption Kulunud aeg

			@property bug_comment_for_all type=checkbox ch_value=1 parent=bug_add_times store=no no_caption=1
			@caption Kuva k&otilde;igile ja saada e-mail

		@layout bc_lay2 parent=bc type=hbox

			@property bug_content_comm type=textarea rows=18 cols=60 parent=bc_lay2 store=no editonly=1 captionside=top draft=1
			@caption Lisa kommentaar

	@layout content_right type=vbox parent=content

		@layout data type=vbox parent=content_right closeable=1 area_caption=Tundide&nbsp;arv

			@layout data_time type=hbox parent=data width=40%:40%:20%

				@property num_hrs_guess type=textbox size=5 parent=data_time captionside=top
				@caption Prognoositav

				@property num_hrs_real type=text parent=data_time captionside=top
				@caption Tegelik

				@property num_hrs_to_cust type=textbox size=5 parent=data_time captionside=top
				@caption Kliendile

		@layout data_cust type=vbox parent=content_right closeable=1 area_caption=Klient

			@layout data_cust_hb type=hbox parent=data_cust width=40%:40%:20%

				@property customer type=relpicker reltype=RELTYPE_CUSTOMER parent=data_cust_hb captionside=top
				@caption Organisatsioon

				@property customer_unit type=relpicker reltype=RELTYPE_CUSTOMER_UNIT parent=data_cust_hb captionside=top
				@caption &Uuml;ksus

				@property customer_person type=relpicker reltype=RELTYPE_CUSTOMER_PERSON parent=data_cust_hb captionside=top
				@caption Isik

		@layout data_ord type=vbox parent=content_right closeable=1 area_caption=Tellija

			@layout data_ord_hb type=hbox parent=data_ord width=40%:40%:20%

				@property orderer type=relpicker reltype=RELTYPE_ORDERER parent=data_ord_hb captionside=top
				@caption Organisatsioon

				@property orderer_unit type=relpicker reltype=RELTYPE_ORDERER_UNIT parent=data_ord_hb captionside=top
				@caption &Uuml;ksus

				@property orderer_person type=relpicker reltype=RELTYPE_ORDERER_PERSON parent=data_ord_hb captionside=top
				@caption Isik

		@layout data_r_bot type=vbox parent=content_right closeable=1 area_caption=Andmed

			@layout data_r_bot_s type=hbox parent=data_r_bot

				@layout data_r_bot_left type=vbox parent=data_r_bot_s

					@property aw_spec type=relpicker reltype=RELTYPE_AW_SPEC  parent=data_r_bot_left captionside=top
					@caption Spetsifikatsioon

					@property bug_component type=textbox parent=data_r_bot_left captionside=top size=15
					@caption Komponent


				@layout data_r_bot_right type=vbox parent=data_r_bot_s

					@property multifile_upload type=multifile_upload reltype=RELTYPE_FILE parent=data_r_bot_right captionside=top store=no max_files=99
					@caption Fail

					@property bug_predicates type=textbox parent=data_r_bot_right captionside=top field=aw_bug_predicates size=15
					@caption Eeldusbugid

					@property bug_mail type=textbox parent=data_r_bot_right captionside=top size=15
					@caption Bugmail CC

		@layout data_r_charts type=vbox parent=content_right closeable=1 area_caption=Graafikud

				@property persons_chart type=google_chart no_caption=1 parent=data_r_charts store=no

				@property times_chart type=google_chart no_caption=1 parent=data_r_charts store=no

		@layout data_r_sitecopy type=vbox parent=content_right closeable=1 area_caption=Arenduskoopia

				@property site_copy type=text no_caption=1 parent=data_r_sitecopy store=no


	@property submit2 type=submit store=no no_caption=1
	@caption Salvesta

@default group=cust

	@property team type=relpicker reltype=RELTYPE_TEAM field=aw_team
	@caption Tiim

	@property ocurrence type=select field=aw_ocurrence
	@caption Vea esinemine

	@property density type=select field=aw_density
	@caption Vea sagedus

	@property cust_responsible type=relpicker reltype=RELTYPE_CUST_RESPONSIBLE field=aw_cust_responsible
	@caption Kliendipoolne vastutaja

	@property cust_status type=select field=aw_cust_status
	@caption Kliendipoolne staatus

	@property cust_tester type=relpicker reltype=RELTYPE_CUST_TESTER field=aw_cust_tester
	@caption Kliendipoolne testija

	@property cust_solution type=textarea rows=10 cols=50 field=aw_cust_solution
	@caption Kliendipoolne lahendus

	@property cust_live_date type=datepicker time=0 field=aw_cust_live_date
	@caption Kasutusvalmis

	@property wish_live_date type=datepicker time=0 field=meta method=serialize table=objects
	@caption Soovitav Live kuup&auml;ev

	@property cust_crit type=textarea rows=10 cols=50 field=aw_cust_crit
	@caption Vastuv&otilde;tu kriteeriumid

	@property cust_budget type=textbox field=aw_cust_budget size=5
	@caption Eelarve

	@property cust_comments type=text store=no
	@caption Kliendipoolsed kommentaarid

@default group=problems

	@property problems_tb type=toolbar no_caption=1 store=no

	@property problems_table type=table no_caption=1 store=no

@default group=comments

	@property comments_table type=table no_caption=1 store=no

@groupinfo cust caption="Kliendi andmed"
@groupinfo problems caption="Probleemid"
@groupinfo comments caption="Kommentaarid"

@property to_bill_date type=hidden table=aw_bugs field=aw_to_bill_date
@caption Arvele m&auml;&auml;ramise kuup&auml;ev


// RELTYPES
@reltype MONITOR value=1 clid=CL_CRM_PERSON
@caption J&auml;lgija

@reltype FILE value=2 clid=CL_FILE
@caption Fail

@reltype CUSTOMER value=3 clid=CL_CRM_COMPANY
@caption Klient

@reltype PROJECT value=4 clid=CL_PROJECT
@caption Projekt

@reltype BUGTYPE value=5 clid=CL_META
@caption Bugi t&uuml;&uuml;p

@reltype COMMENT value=6 clid=CL_TASK_ROW
@caption Kommentaar

@reltype TEAM value=7 clid=CL_PROJECT_TEAM
@caption Tiim

@reltype CUST_RESPONSIBLE value=8 clid=CL_CRM_PERSON
@caption Kliendipoolne vastutaja

@reltype CUST_TESTER value=9 clid=CL_CRM_PERSON
@caption Kliendipoolne testija

@reltype FEEDBACK_P value=10 clid=CL_CRM_PERSON
@caption Tagasiside isik

@reltype CUSTOMER_UNIT value=11 clid=CL_CRM_SECTION
@caption Kliendi &uuml;ksus

@reltype CUSTOMER_PERSON value=12 clid=CL_CRM_PERSON
@caption Kliendi isik

@reltype ORDERER value=13 clid=CL_CRM_COMPANY
@caption Tellija

@reltype ORDERER_UNIT value=14 clid=CL_CRM_SECTION
@caption Tellija &uuml;ksus

@reltype ORDERER_PERSON value=15 clid=CL_CRM_PERSON
@caption Tellija isik

@reltype FROM_PROBLEM value=16 clid=CL_CUSTOMER_PROBLEM_TICKET
@caption Probleem

@reltype DEV_ORDER value=17 clid=CL_DEVELOPMENT_ORDER
@caption Arendustellimus

@reltype AW_SPEC value=18 clid=CL_AW_SPEC
@caption Spetsifikatsioon

@reltype BILL value=19 clid=CL_CRM_BILL
@caption Arve
*/

// DEPRACETED

define("BUG_OPEN", 1);
define("BUG_INPROGRESS", 2);
define("BUG_DONE", 3);
define("BUG_TESTED", 4);
define("BUG_CLOSED", 5);
define("BUG_INCORRECT", 6);
define("BUG_NOTREPEATABLE", 7);
define("BUG_NOTFIXABLE", 8);
define("BUG_WONTFIX", 9);
define("BUG_FEEDBACK", 10);
define("BUG_FATALERROR", 11);
define("BUG_TESTING", 12);
define("BUG_VIEWING", 13);
define("BUG_DEVORDER", 14);
define("BUG_VIEWED", 15);

class bug extends class_base
{
	const BUG_OPEN = 1;
	const BUG_INPROGRESS = 2;
	const BUG_DONE = 3;
	const BUG_TESTED = 4;
	const BUG_CLOSED = 5;
	const BUG_INCORRECT = 6;
	const BUG_NOTREPEATABLE = 7;
	const BUG_NOTFIXABLE = 8;
	const BUG_WONTFIX = 9;
	const BUG_FEEDBACK = 10;
	const BUG_FATALERROR = 11;
	const BUG_TESTING = 12;
	const BUG_VIEWING = 13;
	const BUG_DEVORDER = 14;
	const BUG_VIEWED = 15;

	private $_set_feedback = "";
	private $_ac_old_state;
	private $_ac_new_state;
	private $_change_status;
	private $who_set;
	private $_acc_add_wh;
	private $_acc_add_wh_cust;
	private $_acc_add_wh_guess;
	private $add_comments = array();
	private $comment_for_all;
	private $new_bug;
	private $notify_monitors;
	private $parent_data = array();
	private $parent_options = array();

	function bug()
	{
		$this->init(array(
			"tpldir" => "applications/bug_o_matic_3000/bug",
			"clid" => CL_BUG
		));

		$this->bug_statuses = array(
			self::BUG_OPEN => t("Lahtine"),
			self::BUG_INPROGRESS => t("Tegemisel"),
			self::BUG_DONE => t("Valmis"),
			self::BUG_VIEWING => t("&Uuml;levaatamisel"),
			self::BUG_VIEWED => t("&Uuml;le vaadatud"),
			self::BUG_TESTING => t("Testimisel"),
			self::BUG_TESTED => t("Testitud"),
			self::BUG_CLOSED => t("Suletud"),
			self::BUG_INCORRECT => t("Vale teade"),
			self::BUG_NOTREPEATABLE => t("Kordamatu"),
			self::BUG_NOTFIXABLE => t("Parandamatu"),
			self::BUG_WONTFIX => t("Ei tee"),
			self::BUG_FEEDBACK => t("Vajab tagasisidet"),
			self::BUG_FATALERROR => t("Fatal error"),
			self::BUG_DEVORDER => t("Arendustellimus"),
		);

		$this->occurrences = array(
			1 => t("Esmakordne"),
			2 => t("Korduv")
		);

		$this->densities = array(
			1 => t("&Uuml;ksikjuht"),
			2 => t("Puudutab suurt osa"),
			3 => t("Puudutab k&otilde;iki")
		);
	}

	function callback_mod_reforb(&$arr, $request)
	{
		$arr["from_problem"] = ifset($request, "from_problem");
		$arr["do_split"] = "0";
		$arr["post_ru"] = get_ru();
	}

	function callback_on_load($arr)
	{
		$this->cx = new cfgutils();
		$pt = !empty($arr["request"]["parent"]) ? $arr["request"]["parent"] : $arr["request"]["id"];
		if($pt && $this->can("view", $pt) && obj($pt)->class_id() == CL_DEVELOPMENT_ORDER)
		{
			$devo = obj($pt);
			$els = array("orderer" => "orderer_co", "orderer_unit" => "orderer_unit", "orderer_person" => "orderer", "bug_app" => "bug_app", "bug_type" => "bug_type");
			foreach($els as $el => $d_el)
			{
				if(is_array($val = $devo->prop($d_el)))
				{
					foreach($val as $v)
					{
						$this->parent_options[$el][$v] = obj($v)->name();
					}
				}
				$this->parent_options[$el][$devo->prop($d_el)] = $devo->prop($d_el.".name");
				$this->parent_data[$el] = $devo->prop($d_el);
			}
			return;
		}
		elseif (!$pt || !$this->can("view", $pt) || obj($pt)->class_id() != CL_BUG)
		{
			return;
		}
		$parent = new object($pt);
		$props = $parent->properties();
		$cx_props = $this->cx->load_properties(array(
			"clid" => $parent->class_id(),
			"filter" => array(
				"group" => "general",
			),
		));
		$this->parent_options = array();
		$els = array("who", "monitors", "project", "customer", "customer_unit", "customer_person", "orderer", "orderer_unit", "orderer_person");
		foreach($els as $el)
		{
			$this->parent_options[$el] = array();
			$objs = $parent->connections_from(array(
				"type" => $cx_props[$el]["reltype"],
			));
			foreach($objs as $obj)
			{
				$this->parent_options[$el][$obj->prop("to")] = $obj->prop("to.name");
			}
		}
		$this->parent_data = array(
			"who" => $props["who"],
			"bug_class" => $props["bug_class"],
			"monitors" => $props["monitors"],
			"project" => $props["project"],
			"customer" => $props["customer"],
			"bug_status" => $props["bug_status"],
			"bug_priority" => $props["bug_priority"],
			"bug_type" => $props["bug_type"],
			"bug_app" => $props["bug_app"],
			"deadline" => $props["deadline"],
			"prognosis" => $props["prognosis"],
			"actual_live_date" => $props["actual_live_date"],
			"orderer" => $props["orderer"],
			"orderer_unit" => $props["orderer_unit"],
			"orderer_person" => $props["orderer_person"],
			"customer_unit" => $props["customer_unit"],
			"customer_person" => $props["customer_person"],
			"finance_type" => $props["finance_type"],
			"wish_live_date" => $props["wish_live_date"],
		);
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		if($arr["new"] && !empty($this->parent_data[$prop["name"]]))
		{
			$prop["value"] = $this->parent_data[$prop["name"]];
		}

		if($arr["new"] && !empty($this->parent_options[$prop["name"]]))
		{
			foreach($this->parent_options[$prop["name"]] as $val => $n)
			{
				$prop["options"][$val] = $n;
			}
		}

		switch($prop["name"])
		{
			case "site_copy":
				return PROP_IGNORE;
				/*	sitecopy is currently down
				$prop["value"] = '<div id="site_copy"></div>';
				load_javascript("site_copy.js");
				*/
				break;

			case "send_bill":
				return PROP_IGNORE;
				if($arr["new"])
				{
					$prop["value"] = 1;
				}
				break;

			case "persons_chart":
				if($arr["new"])
				{
					return PROP_IGNORE;
				}
				$c = $arr["prop"]["vcl_inst"];
				$c->set_type(GCHART_PIE_3D);
				$c->set_size(array(
					"width" => 400,
					"height" => 100,
				));
				$c->add_fill(array(
					"area" => GCHART_FILL_BACKGROUND,
					"type" => GCHART_FILL_SOLID,
					"colors" => array(
						"color" => "e9e9e9",
					),
				));
				$conn = $arr["obj_inst"]->connections_from(array(
					"type" => "RELTYPE_COMMENT",
				));
				$times = array();
				$labels = array();
				$ui = get_instance(CL_USER);
				foreach($conn as $cn)
				{
					$cmo = $cn->to();
					if($cmo->prop("add_wh"))
					{
						$times[$cmo->createdby()] = ifset($times, $cmo->createdby()) + $cmo->prop("add_wh");
					}
				}
				foreach($times as $uid => $time)
				{
					$pn = $ui->get_person_for_uid($uid)->name();
					$labels[] = $pn." (".$time.")";
				}
				$c->add_data($times);
				$c->set_labels($labels);
				$c->set_title(array(
					"text" => t("T&ouml;&ouml;tunnid isikute kaupa"),
					"color" => "666666",
					"size" => 11,
				));
				break;

			case "times_chart":
				if($arr["new"])
				{
					return PROP_IGNORE;
				}
				$c = $arr["prop"]["vcl_inst"];
				$c->set_type(GCHART_LINE_CHART);
				$c->set_size(array(
					"width" => 400,
					"height" => 150,
				));
				$c->add_fill(array(
					"area" => GCHART_FILL_BACKGROUND,
					"type" => GCHART_FILL_SOLID,
					"colors" => array(
						"color" => "e9e9e9",
					),
				));
				$conn = $arr["obj_inst"]->connections_from(array(
					"type" => "RELTYPE_COMMENT",
				));
				$times = array();
				$crd = $arr["obj_inst"]->created();
				$data = array();
				$week = 24 * 60 * 60 * 7;
				if(time() - $crd > $week * 2)
				{
					$time = $week;
				}
				else
				{
					$time = $week / 7;
				}
				$max = 0;
				foreach($conn as $cn)
				{
					$cmo = $cn->to();
					$cr = $cmo->created();
					if($time == $week)
					{
						$wd = date('N', $cr);
						$d = date('d', $cr) - ($wd - 1);
					}
					else
					{
						$d = date('d', $cr);
					}
					$key = mktime(0,0,0,date('m', $cr), $d, date('Y', $cr));
					$times[$key] = ifset($times, $key) + $cmo->prop("add_wh");
				}
				if($time == $week)
				{
					$wd = date('N', $crd);
					$d = date('d', $crd) - ($wd - 1);
				}
				else
				{
					$d = date('d', $crd);
				}
				$data = array();
				$sum = 0;
				for($i = mktime(0,0,0, date('m', $crd), $d, date('Y', $crd)); $i < time(); $i = mktime(0,0,0, date('m', $i), date('d', $i) + $time / (24 * 60 * 60), date('Y', $i)))
				{
					$sum += ifset($times, $i);
					$data[] = $sum;
				}
				if(count($data) == 1)
				{
					$data[] = $data[0];
				}
				$c->add_data($data);
				$c->set_axis(array(GCHART_AXIS_LEFT, GCHART_AXIS_BOTTOM));
				$left_axis = array();
				if ($sum > 0)
				{
					for($i = 0; $i <= $sum; $i+= $sum/4)
					{
						$left_axis[] = round($i, 2);
					}
				}
				$c->add_axis_label(0, $left_axis);
				$bot_axis = array();
				for($i = $crd; $i < time(); $i += round((time() - $crd) / 3))
				{
					$bot_axis[] = date('d.m.Y', $i);
				}
				$c->add_axis_label(1, $bot_axis);
				$c->add_axis_style(1, array(
					"color" => "999999",
					"font" => 11,
					"align" => GCHART_ALIGN_CENTER,
				));
				$c->set_grid(array(
					"xstep" => 33,
					"ystep" => 25,
				));
				$c->set_title(array(
					"text" => t("T&ouml;&ouml;tunnid aja l&otilde;ikes"),
					"color" => "666666",
					"size" => 11,
				));
				break;

			case "is_order":
				if(!$arr["new"])
				{
					$pid = $arr["obj_inst"]->parent();
				}
				else
				{
					$pid = $arr["request"]["parent"];
				}
				$p = obj($pid);
				if($p->class_id() == CL_DEVELOPMENT_ORDER)
				{
					$retval = PROP_IGNORE;
				}
				break;

			case "skill_used":
				if (!$this->can("view", $arr["obj_inst"]->prop("who")))
				{
					return PROP_IGNORE;
				}
				$who = obj($arr["obj_inst"]->prop("who"));
				$prop["options"] = $who->get_skill_names();
				if(!sizeof($prop["options"]))
				{
					$prop["type"] = "text";
					$prop["value"] = t("p&auml;devused m&auml;&auml;ramata");
				}
				break;

			case "customer_unit":
				if(!empty($this->parent_data[$prop["name"]]))
				{
					break;
				}

				if ($this->can("view", $arr["obj_inst"]->prop("customer")))
				{
					$co = obj($arr["obj_inst"]->prop("customer"));
				}
				elseif (!empty($arr["request"]["from_problem"]))
				{
					$tmp = obj($arr["request"]["from_problem"]);
					$co = obj($tmp->prop("customer"));
				}
				else
				{
					$co = get_current_company();
				}

				if(!$co or $co->class_id() != CL_CRM_COMPANY)
				{
					return;
				}
				$co_i = $co->instance();
				$sects = $co_i->get_all_org_sections($co);
				$prop["options"] = array("" => t("--vali--"));
				if(!empty($prop["value"]))
				{
					$prop["options"][$prop["value"]] = obj($prop["value"])->name();
				}

				if (count($sects))
				{
					$ol = new object_list(array("oid" => $sects, "lang_id" => array(), "site_id" => array()));
					foreach($ol->arr() as $oid => $o)
					{
						$prop["options"][$oid] = $o->name();
					}
				}
				$p = get_current_person();
				if (!empty($arr["new"]))
				{
					if (!empty($arr["request"]["from_problem"]))
					{
						$tmp = obj($arr["request"]["from_problem"]);
						$prop["value"] = $tmp->prop("orderer_unit");
					}
					else
					{
						$prop["value"] = $p->prop("org_section");
					}
				}
				break;

			case "customer_person":
				if(!empty($this->parent_data[$prop["name"]]))
				{
					break;
				}
				return $this->_get_customer_person($arr);

			case "orderer":
				if(!empty($this->parent_data[$prop["name"]]))
				{
					break;
				}
				return $this->_get_orderer($arr);

			case "orderer_unit":
				if(!empty($this->parent_data[$prop["name"]]))
				{
					break;
				}
				return $this->_get_orderer_unit($arr);

			case "orderer_person":
				if(!empty($this->parent_data[$prop["name"]]))
				{
					break;
				}
				return $this->_get_orderer_person($arr);

			case "actual_live_date":
				if(!empty($arr["new"]) && empty($this->parent_data[$prop["name"]]))
				{
					$prop["value"] = -1;
				}
				break;

			case "deadline":
				if (!empty($arr["request"]["from_req"]))
				{
					$r = obj($arr["request"]["from_req"]);
					$prop["value"] = $r->prop("planned_time");
				}

				if(!empty($arr["new"]) && is_oid($arr["request"]["parent"]) && empty($this->parent_data[$prop["name"]]))
				{
					$po = obj($arr["request"]["parent"] ? $arr["request"]["parent"] : $arr["request"]["id"]);
					$pt = $po->path();
					$bt = null;
					foreach($pt as $pi)
					{
						if ($pi->class_id() == CL_BUG_TRACKER)
						{
							$bt = $pi;
						}
					}

					if ($bt)
					{
						$bdd = $bt->prop("bug_def_deadline");
						if($bdd > 0)
						{
							$prop["value"] = time() + $bdd*24*60*60;
						}
					}
				}
				break;

			case "team":
				if ($this->can("view", $arr["obj_inst"]->prop("project")))
				{
					$po = obj($arr["obj_inst"]->prop("project"));
					$opts = array("" => t("--vali--"));
					foreach($po->connections_from(array("type" => "RELTYPE_TEAM")) as $c)
					{
						$opts[$c->prop("to")] = $c->prop("to.name");
					}
					$prop["options"] = $opts;
				}
				break;

			case "ocurrence":
				$prop["options"] = $this->occurrences;
				break;

			case "density":
				$prop["options"] = $this->densities;
				break;

			case "cust_status":
				$prop["options"] = $this->filter_bug_statuses($this->bug_statuses, $arr);
				break;

			case "cust_responsible":
			case "cust_tester":
				if ($this->can("view", $arr["obj_inst"]->prop("project")))
				{
					$opts = array("" => t("--vali--"));
					$pi = new project();
					$team = $pi->get_team(obj($arr["obj_inst"]->prop("project")));
					foreach($team as $team_id)
					{
						$mem = obj($team_id);
						$opts[$team_id] = $mem->name();
					}
					$prop["options"] = $opts;
				}
				break;

			case "name":
				if (is_oid($arr["obj_inst"]->id()))
				{
					$u = get_instance(CL_USER);
					$p = $u->get_person_for_uid($arr["obj_inst"]->createdby());
					$crea = sprintf(t("Looja: %s / %s"), $p->name(), date("d.m.Y H:i", $arr["obj_inst"]->created()));
				}
				elseif (!empty($arr["request"]["from_req"]))
				{
					$r = obj($arr["request"]["from_req"]);
					$prop["value"] = $r->name();
				}
				elseif (!empty($arr["request"]["from_problem"]))
				{
					$r = obj($arr["request"]["from_problem"]);
					$prop["value"] = $r->name();
				}
				break;

			case "expl_txt":
				$crea = "";
				if (is_oid($arr["obj_inst"]->id()))
				{
					$u = new user();
					$p = $u->get_person_for_uid($arr["obj_inst"]->createdby());
					$crea = sprintf(t("Looja: %s (%s)"), $p->name(), date("d.m.Y H:i", $arr["obj_inst"]->created()));
				}

				$link = html::href(array(
					"caption" => t("Link"),
					"url" => obj_link($arr["obj_inst"]->id())
				));
				$prop["value"] = html::span(array(
						"content" => '#'.$arr["obj_inst"]->id(),
						"textsize" => '13px',
						"fontweight" => 'bold',
					))." ".
					$link." ".
					sprintf(t("Vaade avatud: %s"), date("d.m.Y H:i"))." || ".
					$crea.
					html::linebreak().t("Aega kulunud").": ".
					html::span(array(
						"content" => '<a href="">00:00:00 (0.0000)</a>',
						"id" => "bug_stopper_watch_time",
					));
				break;

			case "bug_content":
				if (empty($arr["new"]))
				{
					return PROP_IGNORE;
				}

				if (!empty($arr["request"]["from_req"]))
				{
					$r = obj($arr["request"]["from_req"]);
					$prop["value"] = $r->prop("desc");
				}

				if (!empty($arr["request"]["from_problem"]))
				{
					$r = obj($arr["request"]["from_problem"]);
					$prop["value"] = $r->prop("content");
				}
				break;

			case "cust_comments":
				if(!empty($arr["new"]))
				{
					return PROP_IGNORE;
				}
				$prop["value"] = html::linebreak().$this->_get_comment_list($arr["obj_inst"], "asc", true, 1, true).html::linebreak();
				break;

			case "bug_comments":
				if(!empty($arr["new"]))
				{
					return PROP_IGNORE;
				}
				$prop["value"] = html::linebreak().$this->_get_comment_list($arr["obj_inst"]).html::linebreak();
				break;

			case "bug_status":
				$prop["onchange"] = "if(this.value==10){ $('#settings_col1_outer .sisu3:eq(1)').css('display', 'block') }";
				$statuses = $this->bug_statuses;
				$this->filter_bug_statuses($statuses, $arr);
				$prop["options"] = $statuses;
				break;

			case "bug_type":
				if (!empty($arr["request"]["id"]))
				{
					$o = obj($arr["request"]["id"]);
				}
				else
				{
					$o = obj($arr["request"]["parent"]);
				}
				$bt = $this->_get_bt($o);
				$options = html::get_empty_option();
				if($bt && $f = $bt->prop("bug_type_folder"))
				{
					$ol = new object_list(array(
						"class_id" => CL_META,
						"parent" => $f,
					));
					$options += $ol->names();
				}
				$prop["options"] = $options;
				$prop["onchange"] = "change_bug_app(this.value);";
				break;

			case "bug_app":
				$prop["options"] = array("" => t("--vali t&uuml;&uuml;p--"));
				$this->bug_app_value = ifset($prop, "value");
				break;

			case "bug_priority":
			case "bug_severity":
				$prop["options"] = $this->get_priority_list();
				if (!empty($arr["request"]["from_req"]))
				{
					$r = obj($arr["request"]["from_req"]);
					$prop["value"] = (int)($r->prop("pri")/2);
				}
				break;


			case "bug_feedback_p":
			case "who":
			case "monitors":
				$tmp = array();
				foreach(safe_array(ifset($this->parent_options, $prop["name"])) as $key => $val)
				{
					$key_o = obj($key);
					if ($key_o->class_id() == crm_person_obj::CLID)
					{
						$tmp[$key] = $val;
					}
				}
				// also, the current person
				$u = get_instance(CL_USER);
				$p = obj($u->get_current_person());
				$tmp[$p->id()] = $p->name();

				if (ifset($prop, "multiple") == 1 && $arr["new"])
				{
				//	$prop["value"] = $this->make_keys(array_keys($tmp));
					$prop["value"] = array($p->id(), $p->id());
				}

				// find tracker for the bug and get people list from that
				$po = obj(!empty($arr["request"]["parent"]) ? $arr["request"]["parent"] : $arr["request"]["id"]);
				$pt = $po->path();
				$bt_obj = null;
				foreach($pt as $pi)
				{
					if ($pi->class_id() == CL_BUG_TRACKER)
					{
						$bt_obj = $pi;
						$bt = $pi->instance();
						foreach($bt->get_people_list($pi) as $pid => $pnm)
						{
							$tmp[$pid] = $pnm;
						}
					}
				}

				$prop["options"] = html::get_empty_option() + $tmp;

				if (!empty($arr["request"]["from_req"]))
				{
					$r = obj($arr["request"]["from_req"]);
					$prop["options"][$r->prop("req_p")] = $r->prop("req_p.name");
				}

				if (($prop["name"] === "who" or $prop["name"] === "monitors") && (!$bt_obj || !$bt_obj->prop("bug_only_bt_ppl")))
				{ // monitors/who property. no bt specified people for this bug
					// load people from company specified by project
					if (!empty($arr["request"]["set_proj"]) or $arr["obj_inst"]->prop("project"))
					{
						$project = !empty($arr["request"]["set_proj"]) ? obj($arr["request"]["set_proj"], array(), project_obj::CLID) : new object($arr["obj_inst"]->prop("project"));
						$prop["options"] += $project->get_people()->names();
					}
					else
					{
						// no project, load nothing
					}
				}
				elseif($prop["name"] === "bug_feedback_p")
				{
					foreach(safe_array($arr["obj_inst"]->prop("monitors")) as $oid)
					{
						if($this->can("view", $oid))
						{
							$prop["options"][$oid] = obj($oid)->name();
						}
					}
				}

				if (isset($prop["value"]))
				{ // add people selected in combobox to options if for some reason they're not on the list
					if (is_array($prop["value"]))
					{
						foreach($prop["value"] as $val)
						{
							if ($this->can("view", $val))
							{
								$tmp = obj($val);
								$prop["options"][$tmp->id()] = $tmp->name();
							}
						}
					}
					elseif ($this->can("view", $prop["value"]) && !isset($prop["options"][$prop["value"]]))
					{
						$tmp = obj($prop["value"]);
						$prop["options"][$tmp->id()] = $tmp->name();
					}
				}

				$this->_sort_bug_ppl($arr);
				break;

			case "bug_class":
				$prop["options"] = array("" => "") + $this->get_class_list();
				break;

			case "project":
				if (is_object($arr["obj_inst"]) && $this->can("view", $arr["obj_inst"]->prop("customer")))
				{
					$filt = array(
						"class_id" => project_obj::CLID,
						"CL_PROJECT.RELTYPE_ORDERER" => $arr["obj_inst"]->prop("customer")
					);
					$ol = new object_list($filt);
				}
				else
				{
					$i = new crm_company();
					$prj = $i->get_my_projects();
					if (!count($prj))
					{
						$ol = new object_list();
					}
					else
					{
						$ol = new object_list(array("oid" => $prj));
					}
				}

				$prop["options"] = html::get_empty_option() + $ol->names();

				if (isset($prop["value"]) && !isset($prop["options"][$prop["value"]]) && $this->can("view", $prop["value"]))
				{
					$tmp = obj($prop["value"]);
					$prop["options"][$tmp->id()] = $tmp->name();
				}

				if (!empty($arr["request"]["set_proj"]))
				{
					$prop["value"] = $arr["request"]["set_proj"];
				}

				if(!empty($arr["new"]) and !empty($this->parent_options[$prop["name"]]))
				{
					foreach($this->parent_options[$prop["name"]] as $key => $val)
					{
						$prop["options"][$key] = $val;
					}
				}

				if (!empty($arr["request"]["from_req"]))
				{
					$r = obj($arr["request"]["from_req"]);
					$prop["value"] = $r->prop("project");
				}
				break;

			case "customer":
				$i = new crm_company();
				$cst = $i->get_my_customers();

				if (!count($cst))
				{
					$prop["options"] = html::get_empty_option();
				}
				else
				{
					$ol = new object_list(array("oid" => $cst));
					$opts = array();
					foreach($ol->arr() as $_co)
					{
						$nm = $_co->prop("short_name");
						if ($nm == "")
						{
							$nm = $_co->name();
						}
						$opts[$_co->id()] = $nm;
					}
					$prop["options"] = html::get_empty_option() + $opts;
				}

				if (isset($arr["request"]["alias_to_org"]) && $this->can("view", $arr["request"]["alias_to_org"]))
				{
					$ao = obj($arr["request"]["alias_to_org"]);
					if ($ao->class_id() == crm_person_obj::CLID)
					{
						$u = get_instance(CL_USER);
						$prop["value"] = $u->get_company_for_person($ao->id());
					}
					else
					{
						$prop["value"] = $arr["request"]["alias_to_org"];
					}
				}

				if (isset($prop["value"]) && !isset($prop["options"][$prop["value"]]) && $this->can("view", $prop["value"]))
				{
					$tmp = obj($prop["value"]);
					$prop["options"][$tmp->id()] = $tmp->name();
				}

				if(!empty($arr["new"]) and isset($this->parent_options[$prop["name"]]))
				{
					foreach($this->parent_options[$prop["name"]] as $key => $val)
					{
						$prop["options"][$key] = $val;
					}
				}

				if (!empty($arr["request"]["from_req"]))
				{
					$r = obj($arr["request"]["from_req"]);
					$prop["value"] = $r->prop("req_co");
				}

				$c = get_current_company();
				if ($c)
				{
					$prop["options"][$c->id()] = $c->name();
				}

				foreach($prop["options"] as $_id => $_nm)
				{
					if (strlen($_nm) > 15)
					{
						$prop["options"][$_id] = substr($_nm, 0, 15)."...";
					}
				}
				break;

			case "num_hrs_real":
				$url = $this->mk_my_orb("stopper_pop", array(
					"id" => $arr["obj_inst"]->id(),
					"s_action" => "start",
					"type" => $this->clid,
					"source_id" => $arr["obj_inst"]->id(),
					"name" => $arr["obj_inst"]->name()
				), CL_TASK);
				$prop["value"] = "<span style=\"font-size: 14px;\">".(isset($prop["value"]) ?  $prop["value"] : 0)."</span> <a href='javascript:void(0)' onClick='aw_popup_scroll(\"{$url}\",\"aw_timers\",800,600)'>".t("Stopper")."</a><br />\n".(($arr["request"]["action"] === "new") ? "" : $this->get_person_times($arr));
				break;

			case "num_hrs_guess":
				if($arr["request"]["action"] !== "new")
				{
					$prop["post_append_text"] = "<br />\n".$this->get_person_times($arr);
				}
				break;

			case "bug_url":
				if (!empty($prop["value"]))
				{
					$url = $prop["value"];
					if(strpos($url, "?") !== 0 && strpos($url, "orb.aw") !== 0 && strpos($url, "://") === false)
					{
						$url = "http://" . $url;
					}
					$prop["post_append_text"] = html::space() . html::href(array(
						"url" => $url,
						"caption" => t("Ava"),
						"target" => "_blank"
					));
				}
				break;

			case "bug_property":
				if ($arr["obj_inst"]->prop("bug_class"))
				{
					$prop["options"] = $this->_get_property_picker($arr["obj_inst"]->prop("bug_class"));
				}
				break;

			case "bug_tb":
				$this->_bug_tb($arr);
				break;

			case "problems_table":
				return $this->_get_problems_table($arr);
				break;

			case "comments_table":
				return $this->_comments_table($arr);

			case "finance_type":
				$prop["options"] = $this->get_finance_types();
				break;

			case "bug_add_real":
				if(empty($prop["value"]))
				{
					$prop["value"] = 0.00;
				}
				$prop["post_append_text"] = html::span(array(
					"id" => "bug_stopper_pause_link",
					"content" => "<a href=''>".t("Paus")."</a>",
				))." ".html::span(array(
					"id" => "bug_stopper_clear_link",
					"content" => "<a href=''>".t("Nulli")."</a>",
				));
				break;
			case "bug_add_guess":
				$prop["value"] = "";
				break;
		}
		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "send_bill":
				return PROP_IGNORE;
				if($arr["new"])
				{
					$prop["value"] = 1;
				}
				break;
			case "finance_type":
				if ($arr["new"] && !$arr["prop"]["value"])
				{
					$path = obj($arr["request"]["parent"])->path();
					foreach($path as $po)
					{
						if($po->class_id() == CL_BUG_TRACKER)
						{
							$bt = $po;
						}
					}

					if(!empty($bt) && $bt->prop("finance_required"))
					{
						$arr["prop"]["error"] = t("Kulude katmise aeg valimata!");
						return PROP_FATAL_ERROR;
					}
				}
				break;

			case "name":
				$po = obj($arr["request"]["parent"] ? $arr["request"]["parent"] : $arr["request"]["id"]);
				$pt = $po->path();
				$bt = null;
				foreach($pt as $pi)
				{
					if ($pi->class_id() == CL_BUG_TRACKER)
					{
						$bt = $pi;
					}
				}

				if (!$this->can("view", $arr["request"]["who"]) && !$arr["obj_inst"]->prop("who"))
				{
					if($bt)
					{
						$conn = $bt->connections_to(array(
							"from.class_id" => CL_BUGTRACK_DISPLAY,
							"type" => "RELTYPE_BUGTRACK"
						));
						foreach($conn as $c)
						{
							$bt_display = obj($c->prop("from"));
						}
					}

					if($arr["request"]["bug_type"] && $bt_display)
					{
						$user = $bt_display->meta("type".$arr["request"]["bug_type"]);
						if($user)
						{
							$arr["obj_inst"]->set_prop("who", $user);
							$this->who_set = 1;
							$err = 0;
						}
						else
						{
							$err = 1;
						}
					}
					else
					{
						$err = 1;
					}

					if($err)
					{
						$prop["error"] = t("Kellele ei tohi olla t&uuml;hi!");
						return PROP_FATAL_ERROR;
					}
				}

				if ($arr["request"]["bug_status"] == self::BUG_FATALERROR)
				{
					return PROP_OK;
				}

				$ev = date_edit::get_timestamp($arr["request"]["deadline"]);

				if ($ev == $arr["obj_inst"]->prop("deadline"))
				{
					return PROP_OK;
				}
				elseif ($ev > 300 && $ev < date_calc::get_day_start())
				{
					$prop["error"] = t("T&auml;htaeg ei tohi olla minevikus!");
					return PROP_FATAL_ERROR;
				}

				if ($bt)
				{
					$bt_i = new bug_tracker();
					if($arr["request"]["who"])
					{
						$arr["obj_inst"]->set_prop("who", $arr["request"]["who"]);
					}
					$estend = $bt_i->get_estimated_end_time_for_bug($arr["obj_inst"], $bt);
					$ovr1 = $bt_i->get_last_estimation_over_deadline_bugs();

					$opv = $arr["obj_inst"]->prop("deadline");
					$opri = $arr["obj_inst"]->prop("bug_priority");
					$osev = $arr["obj_inst"]->prop("bug_severity");
					$arr["obj_inst"]->set_prop("deadline", $ev);
					$arr["obj_inst"]->set_prop("bug_priority", $arr["request"]["bug_priority"]);
					$arr["obj_inst"]->set_prop("bug_severity", $arr["request"]["bug_severity"]);
					if($arr["request"]["bug_status"])
					{
						$arr["obj_inst"]->set_prop("bug_status", $arr["request"]["bug_status"]);
					}
					$estend = $bt_i->get_estimated_end_time_for_bug($arr["obj_inst"], $bt);
					$ovr2 = $bt_i->get_last_estimation_over_deadline_bugs();

					$arr["obj_inst"]->set_prop("deadline", $opv);
					$arr["obj_inst"]->set_prop("bug_priority", $opri);
					$arr["obj_inst"]->set_prop("bug_severity", $osev);

					$n_ovr = array();
					foreach($ovr2 as $item)
					{
						if (!isset($ovr1[$item->id()]) && $item->id() != $arr["obj_inst"]->id())
						{
							$n_ovr[] = $item;
						}
					}

					if (count($n_ovr)) //TODO: vaadata mis siin toimub -- && false) // && false on temp lahendus, eks terryf vaatab &uuml;le kui puhkuselt tuleb
					{
						$nms = array();
						foreach($n_ovr as $item)
						{
							$nms[] = html::obj_change_url($item);
						}
						$prop["error"] = sprintf(t("Selliste parameetritega ei saa salvestada, kuna see l&uuml;kkaks j&auml;rgnevad bugid &uuml;le t&auml;htaja: %s!"), join("<br>", $nms));
						return PROP_FATAL_ERROR;
					}


					if ($ev > 100 && $estend > ($ev+24*3600))
					{
						$prop["error"] = sprintf(t("Bugi ei ole v&otilde;imalik valmis saada enne %s!"), date("d.m.Y H:i", $estend));
						$arr["obj_inst"]->set_prop("name", $prop["value"]);
						$arr["obj_inst"]->save();
						return PROP_ERROR;
					}
				}
				break;

			case "bug_content":
				if (!$arr["new"])
				{
					$prop["value"] = $arr["obj_inst"]->prop("bug_content");
				}
				break;

			case "bug_predicates":
				if (($old = $arr["obj_inst"]->prop($prop["name"])) != $prop["value"])
				{
					$com = sprintf(t("Eeldusbugid muudeti %s => %s"), $old, $prop["value"]);
					$this->add_comments[] = $com;
				}
				break;

			case "bug_content_comm":
				if (trim($prop["value"]) != "" && !$arr["new"])
				{
					// save comment
					//$this->_add_comment($arr["obj_inst"], $prop["value"]);
					$this->add_comments[] = $prop["value"];
					$this->notify_monitors = true;
				}
				break;

			case "bug_comment_for_all":
				if($prop["value"])
				{
					$this->comment_for_all = 1;
				}
				break;

			case "cust_status":
				$bt = $this->_get_bt($arr["obj_inst"]);
				$this->_cust_old_status = $arr["obj_inst"]->prop("cust_status");
				$this->_cust_new_status = $prop["value"];
				$this->_change_status = "cust";
				if($bt)
				{
					$bcs = $bt->meta("bug_cust_status_conns");
					if($bcs[$prop["value"]])
					{
						$change_bug_status = 1;
					}
				}
				$com = sprintf(t("Kliendistaatus muudeti %s => %s"), $this->bug_statuses[$this->_cust_old_status], $this->bug_statuses[$prop["value"]]);
				$this->add_comments[] = $com;
				$this->notify_monitors = true;
				if($change_bug_status && $prop["value"] != $arr["obj_inst"]->prop($prop["name"]))
				{
					$this->_ac_old_state = $arr["obj_inst"]->prop("bug_status");
					$this->_ac_new_state = $prop["value"];
					$arr["obj_inst"]->set_prop("bug_status", $bcs[$prop["value"]]);
				}
				else
				{
					break;
				}

			case "bug_status":
				if(!empty($arr["request"]["is_order"]))
				{
					$c = $arr["obj_inst"]->connections_from(array(
						"type" => "RELTYPE_DEV_ORDER"
					));
					if(!count($c))
					{
						$prop["value"] = self::BUG_DEVORDER;
					}
				}

				if(!$this->_ac_old_state || !$this->_ac_new_state)
				{
					$this->_ac_old_state = $arr["obj_inst"]->prop("bug_status");
					$this->_ac_new_state = $prop["value"];
				}

				if(!$this->_change_status)
				{
					$this->_change_status = "bug";
				}

				if (empty($arr["new"]))
				{
					$retval = $this->_handle_status_change(
						$this->_ac_old_state,
						$this->_ac_new_state,
						$arr["obj_inst"],
						$prop
					);
				}

				if ($this->_ac_old_state != $prop["value"] && empty($arr["new"]))
				{
					$com = sprintf(t("Staatus muudeti %s => %s"), html_entity_decode($this->bug_statuses[$this->_ac_old_state]), html_entity_decode($this->bug_statuses[$prop["value"]]));
					$this->add_comments[] = $com;
					$this->notify_monitors = true;
				}

				$po = obj($arr["request"]["parent"] ? $arr["request"]["parent"] : $arr["request"]["id"]);
				$pt = $po->path();
				$bt_obj = null;
				foreach($pt as $pi)
				{
					if ($pi->class_id() == CL_BUG_TRACKER)
					{
						$bt = $pi;
					}
				}

				$change_bug_status = 0;
				if(!empty($bt))
				{
					$bcs = $bt->meta("cust_bug_status_conns");
					if($bcs[$prop["value"]])
					{
						$change_bug_status = 1;
					}
				}

				if($change_bug_status && $prop["value"] != $arr["obj_inst"]->prop($prop["name"]))
				{
					$arr["obj_inst"]->set_prop("cust_status", $bcs[$prop["value"]]);
				}
				else
				{
					break;
				}
				break;

			case "bug_priority":
				if (($old = $arr["obj_inst"]->prop($prop["name"])) != $prop["value"] && !$arr["new"])
				{
					$com = sprintf(t("Prioriteet muudeti %s => %s"), $old, $prop["value"]);
					//$this->_add_comment($arr["obj_inst"], $com);
					$this->add_comments[] = $com;
					$this->notify_monitors = true;
				}
				break;

			/*case "num_hrs_real":
				$prop["value"] = str_replace(",", ".", $prop["value"]);
				if (($old = $arr["obj_inst"]->prop($prop["name"])) != $prop["value"])
				{
					$com = sprintf(t("Tegelik tundide arv muudeti %s => %s"), $old, $prop["value"]);
					$this->add_comments[] = $com;
					$this->_acc_add_wh = $prop["value"] - $old;
				}
				break;*/

			case "num_hrs_guess":
				$prop["value"] = str_replace(",", ".", $prop["value"]);
				if (($old = $arr["obj_inst"]->prop($prop["name"])) != $prop["value"])
				{
					$com = sprintf(t("Prognoositud tundide arv muudeti %s => %s"), $old, $prop["value"]);
					$this->add_comments[] = $com;
				}
				break;

			case "num_hrs_to_cust":
				$prop["value"] = str_replace(",", ".", $prop["value"]);
				if (($old = $arr["obj_inst"]->prop($prop["name"])) != $prop["value"])
				{
					$com = sprintf(t("Tunde kliendile arv muudeti %s => %s"), $old, $prop["value"]);
					$this->add_comments[] = $com;
					$this->_acc_add_wh_cust = $prop["value"] - $old;
				}
				break;

			case "who":
				$nv = "";
				if ($this->can("view", $prop["value"]))
				{
					$nvo = obj($prop["value"]);
					$nv = $nvo->name();
				}

				if (($old = $arr["obj_inst"]->prop_str($prop["name"])) != $nv && !$arr["new"])
				{
					$com = sprintf(t("Kellele muudeti %s => %s"), $old, $nv);
					//$this->_add_comment($arr["obj_inst"], $com);
					$this->add_comments[] = $com;
					$this->notify_monitors = true;
					$this->newwho = $prop["value"];
				}

				if($this->who_set)
				{
					return PROP_IGNORE;
				}
				break;

			case "monitors":
				if (empty($arr["new"]) && $arr["request"]["who"])
				{
					$mon = $arr["request"]["monitors"];
					if(empty($mon[$arr["request"]["who"]]))
					{
						$mon[$arr["request"]["who"]] = $arr["request"]["who"];
						$prop["value"] = $mon;
					}
				}
				break;

			case "bug_class":
				$old_clid = (int) $arr["obj_inst"]->prop($prop["name"]);
				$new_clid = (int) $prop["value"];
				$old = aw_ini_isset("classes.{$old_clid}") ? aw_ini_get("classes.{$old_clid}.name") : "";
				$nv = aw_ini_isset("classes.{$new_clid}") ? aw_ini_get("classes.{$new_clid}.name") : "";
				if ($old != $nv && empty($arr["new"]))
				{
					$com = sprintf(t("Klass muudeti %s => %s"), $old, $nv);
					//$this->_add_comment($arr["obj_inst"], $com);
					$this->add_comments[] = $com;
				}
				break;

			case "bug_feedback_p":
				if ($arr["obj_inst"]->prop("bug_status") != self::BUG_FEEDBACK)
				{
					return PROP_IGNORE;
				}

				if ($this->_set_feedback && !$arr["obj_inst"]->prop($prop["name"]))
				{
					$prop["value"] = $this->_set_feedback;
				}

				$nv = "";
				if ($this->can("view", $prop["value"]))
				{
					$nvo = obj($prop["value"]);
					$nv = $nvo->name();
				}

				if (($old = $arr["obj_inst"]->prop_str($prop["name"])) != $nv && !$arr["new"])
				{
					$com = sprintf(t("Tagasiside kellelt muudeti %s => %s"), $old, $nv);
					$this->add_comments[] = $com;
					$this->notify_monitors = true;
				}
				break;

			case "deadline":
				$po = obj($arr["request"]["parent"] ? $arr["request"]["parent"] : $arr["request"]["id"]);
				$pt = $po->path();
				$bt_obj = null;
				foreach($pt as $pi)
				{
					if ($pi->class_id() == CL_BUG_TRACKER)
					{
						$bt = $pi;
					}
				}

				if(!empty($bt))
				{
					$bdd = $bt->prop("bug_def_deadline");
					if($arr["new"] && ((int)$bdd)>0)
					{
						$date = time() + $bdd*24*60*60;
						$arr["obj_inst"]->set_prop("wish_live_date", $date);
					}
				}
				break;

			case "comments_table":
				$total = 0;
				foreach($arr["request"]["add_wh"] as $oid => $hrs)
				{
					$com = obj($oid);
					$com->set_prop("add_wh", $hrs);
					$com->set_comment($arr["request"]["comment"][$oid]);
					$com->save();
					if($com->class_id() == CL_TASK_ROW)$com->set_prop("on_bill", isset($arr["request"]["on_bill"][$oid]) ? 1 : 0);//arr($arr["request"]);
					if($arr["request"]["time_to_cust"][$oid])
					{
						$com->set_prop("time_to_cust", $arr["request"]["time_to_cust"][$oid]);
					}
					$com->save();
					$total += $hrs;
				}
				$arr["obj_inst"]->set_prop("bug_content", $arr["request"]["comment"]["bug"]);
				$arr["obj_inst"]->set_prop("num_hrs_real", $total);
				$arr["obj_inst"]->save();
				break;

			case "bug_add_real":
				$prop["value"] = str_replace(",", ".", $prop["value"]);
				$old = $arr["obj_inst"]->prop("num_hrs_real");
				$new = $old + $prop["value"];
				$arr["obj_inst"]->set_prop("num_hrs_real", $new);
				if ($prop["value"] > 0)
				{
					$com = sprintf(t("Tegelik tundide arv muudeti %s => %s"), $old, $new);
					$this->add_comments[] = $com;
					$this->_acc_add_wh = $prop["value"];
				}
				break;

			case "bug_add_guess":
				$prop["value"] = str_replace(",", ".", $prop["value"]);
				if($arr["new"])
				{
					$old = 0;
					$old_real = 0;
				}
				else
				{
					$conn = $arr["obj_inst"]->connections_from(array(
						"type" => "RELTYPE_COMMENT",
					));
					$old = 0;
					$old_real = 0;
					foreach($conn as $c)
					{
						$cmo = $c->to();
						if($cmo->createdby() == aw_global_get("uid"))
						{
							$old += $cmo->prop("add_wh_guess");
							$old_real += $cmo->prop("add_wh");
						}
					}
				}
				$this->_acc_old_wh_guess = $old;
				$this->_acc_old_wh = $old_real;
				$cp = get_current_person();
				$p = $cp->id();
				if ($prop["value"])
				{
					$com = sprintf(t("Isiku prognoositud tundide arv muudeti %s => %s"), $old, $old + $prop["value"]);
					$this->add_comments[] = $com;
					$this->_acc_add_wh_guess = $prop["value"];
				}
				break;
		}
		return $retval;
	}

	function _sort_bug_ppl(&$arr)
	{
		$prop = &$arr["prop"];
		$tmp_options = $prop["options"];
		unset($tmp_options[""]);
		$options = array();
		if(!empty($prop["value"]))
		{
			$vals = (array)$prop["value"];
		}
		else
		{
			$vals = array();
		}
		$options[""] = $prop["options"][""];
		foreach($vals as $value)
		{
			$options[$value] = ifset($prop, "options", $value);
		}
		if(!$arr["new"])
		{
			$creator_u = $arr["obj_inst"]->createdby();
			$p = get_instance(CL_USER)->get_person_for_uid($creator_u);
			$options[$p->id()] = $p->name();
		}
		$cur = get_current_person();
		$options[$cur->id()] = $cur->name();
		foreach($prop["options"] as $val => $name)
		{
			$options[$val] = $name;
		}
		$prop["options"] = $options;
	}

	// DEPRACETED. a malpractice in its extreme
	function __sort_ppl($a, $b) { $o1 = obj($a); $o2 = obj($b); return strcasecmp($o1->prop("lastname"), $o2->prop("lastname")); }

	function notify_monitors($bug, $comment, $old_state = null, $new_state = null)
	{
		$monitors = $bug->prop("monitors");
		// if the status is right, then add the creator of the bug to the list
		$states = array(
			self::BUG_TESTED,
			self::BUG_INCORRECT,
			self::BUG_NOTREPEATABLE,
			self::BUG_NOTFIXABLE,
			self::BUG_WONTFIX,
			self::BUG_FEEDBACK
		);
		$u = get_instance(CL_USER);
		$us = get_instance("users");
		if (in_array($bug->prop("bug_status"), $states))
		{
			$crea = $bug->createdby();
			$_oid = $us->get_oid_for_uid($crea);
			if ($this->can("view", $_oid))
			{
				$monitors[] = $u->get_person_for_user(obj($_oid));
			}
		}

		// add who to the list of mail receivers
		$monitors[] = $bug->prop("who");

		// I should add a way to send CC-s to arbitraty e-mail addresses as well
		$notify_addresses = array();
		$bt = $this->_get_bt($bug);
		if ($bt && $bt->prop("def_notify_list") != "")
		{
			$notify_addresses[] = $bt->prop("def_notify_list");
		}

		$bt_send = true;
		if($bt && !$bt->prop("send_monitor_mails"))
		{
			$bt_send = false;
		}

		//development order sends mails from here too, and we need to get the right config from bugtrack
		if($bug->class_id() == CL_BUG)
		{
			if($this->_change_status)
			{
				$get_mg = true;
				$mails_var = "st_mail_groups_".$this->_change_status;
				if($this->_change_status == "bug")
				{
					$old_s = $this->_ac_old_state;
					$new_s = $this->_ac_new_state;
				}
				else
				{
					$old_s = $this->_cust_old_status;
					$new_s = $this->_cust_new_status;
				}
			}

			if(stripos($comment, "kellele muudeti") !== false && $bt && $bt->prop("send_newwho_mails"))//TODO: 'kellele muudeti' ???
			{
				$newwho = $bug->prop("who");
			}
		}
		elseif($bug->class_id() == CL_DEVELOPMENT_ORDER)
		{
			$mails_var = "st_mail_groups_devo";
			$get_mg = true;
			$old_s = $old_state;
			$new_s = $new_state;
		}

		$mg = null;
		if($bt && $get_mg)
		{
			$mg = $bt->meta($mails_var);
		}

		$pi = get_instance(CL_CRM_PERSON);
		$adrs = array();
		foreach(array_unique($monitors) as $person)
		{
			if(!$this->can("view", $person))
			{
				continue;
			}
			$person_obj = obj($person);
			// don't send to the current user, cause, well, he knows he's just done it.
			if ($person == $u->get_current_person())
			{
				continue;
			}
			//if person is in a group that should recieve mails on certain status changes, then send the mail, even if otherwise shouldn't
			$cont = false;
			if(!$bt_send)
			{
				$cont = true;
			}

			if(!$this->comment_for_all && $mg && $uo = $pi->has_user($person_obj))
			{
				$conn = $uo->connections_from(array(
					"type" => "RELTYPE_GRP",
				));
				$pgids = array();
				foreach($conn as $c)
				{
					$gid = $c->prop("to");
					$pgids[$gid] = $gid;
				}
				$send_mail_statuses = array();
				foreach($mg as $stid => $groups)
				{
					foreach($groups as $gid)
					{
						foreach($pgids as $pgid)
						{
							if($gid == $pgid)
							{
								$send_mail_statuses[] = $stid;
							}
						}
					}
				}
				if(array_search($new_s, $send_mail_statuses) !== false)
				{
					$cont = false;
				}
				if(!$bt_send && (!$old_s || $old_s == $new_s || array_search($new_s, $send_mail_statuses) === false))
				{
					$cont = true;
				}
				if(!$bt_send && $newwho && $newwho == $person)
				{
					$cont = false;
				}
			}
			elseif($this->comment_for_all  || $this->new_bug)
			{
				$cont = false;
			}

			if($cont)
			{
				continue;
			}

			$email = $person_obj->prop("email");
			if ($this->can("view", $email))
			{
				$email_obj = new object($email);
				$addr = $email_obj->prop("mail");
				if (is_email($addr) && array_search($addr, $adrs) === false)
				{
					$adrs[] = $addr;
					$notify_addresses[] = array("adr" => $addr, "person" => $person_obj);
				}
			}
		}

		$addrs = explode(",",$bug->prop("bug_mail"));
		foreach($addrs as $addr)
		{
			if (is_email($addr) && array_search($addr, $adrs) === false)
			{
				$adrs[] = $addr;
				$notify_addresses[] = $addr;
			};
		};

		if (sizeof($notify_addresses) == 0)
		{
			return false;
		}

		foreach($notify_addresses as $data)
		{
			$adr = $data["adr"] ? $data["adr"] : $data;
			$oid = $bug->id();
			$name = $bug->name();
			$uid = aw_global_get("uid");

			$admin = true;
			if($data["person"])
			{
				$uo = $pi->has_user($data["person"]);
				$conn = $uo->connections_from(array(
					"type" => "RELTYPE_GRP",
				));
				$admin = false;
				foreach($conn as $c)
				{
					$grp = $c->to();
					if($grp->prop("can_admin_interface"))
					{
						$admin = true;
					}
				}
			}

			$bug_url = $this->mk_my_orb("change", array("id" => $oid), CL_BUG, $admin);
			if(!$admin)
			{
				$ol = new object_list(array(
					"class_id" => CL_BUGTRACK_DISPLAY,
					"site_id" => array(),
					"lang_id" => array(),
				));
				$o = $ol->begin();
				if($o)
				{
					$sect = $o->prop("bug_doc");
					$bug_url = $this->mk_my_orb("change", array("section" => $sect, "id" => $oid), CL_BUG, $admin);
				}
				$bug_url = str_replace(array("orb.aw", "automatweb/"), "", $bug_url);
			}

			$msgtxt = t("Bug") . ": " . $bug_url . "\n";
			$msgtxt .= t("Summary") . ": " . $name . "\n";
			$msgtxt .= t("URL") . ": " . $bug->prop("bug_url") . "\n";
			$msgtxt .= t("Status"). ": " . html_entity_decode($this->bug_statuses[$bug->prop("bug_status")]) . "\n";
			$msgtxt .= ($bug->prop("bug_status") == self::BUG_FEEDBACK) ? t("Feedback from"). ": " . $bug->prop("bug_feedback_p.name") . "\n" : "";
			$msgtxt .= t("Priority"). ": " . $bug->prop("bug_priority") . "\n";
			$msgtxt .= t("Assigned to"). ": " . $bug->prop("who.name") . "\n";
			$msgtxt .= "-------------\n\nNew comment from " . $uid . " at " . date("Y-m-d H:i") . "\n";
			$msgtxt .= strip_tags($comment)."\n";
			$msgtxt .= strip_tags($this->_get_comment_list($bug, "desc", false));

			try
			{
				$from = aw_ini_get("bugtrack.mails_from");
			}
			catch(Exception $e)
			{
				$from = "automatweb@automatweb.com";
			}

			send_mail($adr,"Bug #" . $oid . ": " . $name . " : " . $uid . " lisas kommentaari",$msgtxt,"From: ".$from);
		}
	}

	function get_sort_priority($bug, $formula = "")
	{
		$sp_lut = array(
			self::BUG_OPEN => 100,
			self::BUG_INPROGRESS => 110,
			self::BUG_DONE => 70,
			self::BUG_TESTED => 60,
			self::BUG_CLOSED => 50,
			self::BUG_INCORRECT => 40,
			self::BUG_NOTREPEATABLE => 40,
			self::BUG_NOTFIXABLE => 40,
			self::BUG_FATALERROR => 200,
			self::BUG_FEEDBACK => 130
		);

		if (empty($formula))
		{
			$rv = (isset($sp_lut[$bug->prop("bug_status")]) ? $sp_lut[$bug->prop("bug_status")] : 0) + $bug->prop("bug_priority");
			// also, if the bug has a deadline, then we need to up the priority as the deadline comes closer
			if (($dl = $bug->prop("deadline")) > 200)
			{
				// deadline in the next 24 hrs = +3
				if ($dl < (time() - 24*3600))
				{
					$rv++;
				}
				// deadline in the next 48 hrs +2
				if ($dl < (time() - 48*3600))
				{
					$rv++;
				}
				// has deadline = +1
				$rv++;
			}

			//if customer priority set, up the bug's priority
			if($cust_priority = $bug->prop("customer.cust_priority"))
			{
				$cust_priority = ($cust_priority>99999)?99999:$cust_priority;
				$rv += 1.0 - ((double)1.0/((double)100000.0 + (double)$cust_priority));
			}

			$rv += 1.0 - ((double)1.0/((double)1000000.0 - (double)$bug->id()));
		}
		else
		{
			$bs = $bug->prop("bug_status");
			$bp = $bug->prop("bug_priority");
			$cp = $bug->prop("customer.cust_priority");
			$pp = $bug->prop("project.priority");
			$bi = $bug->prop("bug_severity");
			$dd = $bug->prop("deadline");
			$bl = $bug->prop("num_hrs_guess");
			$proj = $bug->prop("project");
			$p = 0;
			eval($formula);
			$rv = $p;
		}

		return $rv;
	}

	/**
		@attrib params=name name=handle_bug_change_status
		@param bug required type=oid
		@param status required type=int
	**/
	function hadle_bug_change_status($arr)
	{
		$o = obj($arr["bug"]);
		$o->set_prop("bug_status", $arr["status"]);
		die();
	}

	/**
		@attrib name=get_autocomplete
		@comment
			bug name autokompliit
	**/
	function get_autocomplete()
	{
		header ("Content-Type: text/html; charset=" . aw_global_get("charset"));
		$cl_json = get_instance("protocols/data/json");

		$errorstring = "";
		$error = false;
		$autocomplete_options = array();

		$option_data = array(
			"error" => &$error,// recommended
			"errorstring" => &$errorstring,// optional
			"options" => &$autocomplete_options,// required
			"limited" => false,// whether option count limiting applied or not. applicable only for real time autocomplete.
		);

		$ol = new object_list(array(
			"class_id" => CL_BUG,
		));
		foreach($ol->arr() as $oid => $el)
		{
			$obj = new object($oid);
			$autocomplete_options[$obj->name()] = $obj->name();
		}

		exit($cl_json->encode($option_data));
	}

	function stopper_autocomplete($requester, $arr)
	{
		switch($requester)
		{
			case "parent":
				$ol = new object_list(array(
					"class_id" => CL_BUG,
				));
				foreach($ol->arr() as $oid => $obj)
				{
					$ret[$oid] = $obj->name();
				}
				break;
		}
		return $ret;
	}

	function gen_stopper_addon($arr)
	{

		$props = array(
			array(
				"name" => "name",
				"type" => "textbox",
				"caption" => t("Nimi"),
			),
			array(
				"name" => "status",
				"type" => "select",
				"options" => $this->bug_statuses,
				"caption" => t("Staatus"),
			),
			array(
				"name" => "contents",
				"type" => "textarea",
				"caption" => t("Sisu"),
			),
			array(
				"name" => "parent",
				"type" => "textbox",
				"caption" => t("Vanem-bugi"),
				"autocomplete" => true,
			),
			array(
				"name" => "deadline",
				"type" => "date_select",
				"caption" => t("T&auml;htaeg"),
			),
		);
		return $props;
	}
	function gen_existing_stopper_addon($arr)
	{
		$o = obj($arr["oid"]);
		$props = array(
			array(
				"name" => "contents",
				"type" => "textarea",
				"caption" => t("Kommentaar"),
				"captionside" => "top",
				"cols" => "65",
				"rows" => "5"
			),
			array(
				"name" => "status",
				"type" => "select",
				"options" => $this->bug_statuses,
				"caption" => t("Staatus"),
				"selected" => $o->prop("bug_status"),
			),
		);
		return $props;
	}

	function handle_stopper_stop($inf)
	{
		/*
			props to take from parent bug
			customer_unit,
			customer_person,
			orderer,
			orderer_unit,
			orderer_person,
			monitors, ??
			project,
			customer,

			bug_class, ?????
		*/
		if(!$this->can("view", $inf["oid"]))
		{
			if(!strlen($inf["data"]["name"]["value"]) || !$this->can("view", $inf["data"]["parent"]["value"]) || $inf["data"]["deadline"]["value"] == -1)
			{
				return t("Nimi, vanem-bugi ja t&auml;htaeg peavad olema seatud!");
			}
		}
		if(!$this->can("view", $inf["oid"]))
		{
			$parent = obj($inf["data"]["parent"]["value"]);
			// props from parent
			$pfp = array("bug_priority", "who", "bug_severity", "monitors", "bug_class", "customer", "customer", "customer_unit", "customer_person", "orderer", "orderer_unit", "orderer_person");

			$o = new object();
			$o->set_parent($inf["data"]["parent"]["value"]);
			$o->set_name($inf["data"]["name"]["value"]);
			$o->set_class_id(CL_BUG);
			$o->set_prop("bug_content", $inf["data"]["contents"]["value"]);
			$o->set_prop("deadline", $arr["data"]["deadline"]["value"]);

			foreach($pfp as $pprop)
			{
				$o->set_prop($pprop, $parent->prop($pprop));
			}
			$o->save();
			$inf["oid"] = $o->id();
			unset($inf["data"]["contents"]["value"]);
		}

		$bug = obj($inf["oid"]);

		$inf["desc"] = $inf["data"]["contents"]["value"];
		$inf["desc"] .= sprintf(t("\nTegelik tundide arv muudeti %s => %s"), $bug->prop("num_hrs_real"), $bug->prop("num_hrs_real")+$inf["hours"]);
		$bug->set_prop("num_hrs_real", $bug->prop("num_hrs_real") + $inf["hours"]);
		if(array_key_exists($inf["data"]["status"]["value"], $this->bug_statuses))
		{
			$bug->set_prop("bug_status", $inf["data"]["status"]["value"]);
		}
		$bug->save();

		if (trim($inf["desc"]) != "")
		{
			$this->_add_comment($bug, $inf["desc"], null, null, $inf["hours"]);
		}
	}

	function _get_comment_list($o, $so = "asc", $nl2br = true, $base_com = 1, $hide_others = false)
	{
		$this->read_template("comment_list.tpl");

		$params = array(
			"class_id" => array(CL_TASK_ROW,CL_BUG_COMMENT),
			"parent" => $o->id(),
			"sort_by" => "objects.created $so"
		);

		if($hide_others)
		{
			$params[] = new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					"createdby" => aw_global_get("uid"),
					"CL_TASK_ROW.show_to_all" => 1,
				),
			));
		}

		$ol = new object_list($params);
		$com_str = "";
		$u = get_instance(CL_USER);
		foreach($ol->arr() as $com)
		{
			$comt = create_links(preg_replace("/(\&amp\;#([0-9]{4});)/", "&#\\2", htmlspecialchars(html_entity_decode($com->comment()), ENT_NOQUOTES)));
			$comt = preg_replace("/(>http:\/\/dev.struktuur.ee\/cgi-bin\/viewcvs\.cgi\/[^<\n]*)/ims", ">Diff", $comt);
			if ($nl2br)
			{
				$comt = nl2br($comt);
			}

			$comt = $this->_split_long_words($comt);

			// replace #675656 with link to bug
			$comt = preg_replace("/(?<!&)#([0-9]+)/ims", "<a href='http://intranet.automatweb.com/\\1'>#\\1</a>", $comt);
//-------- vastus divi sisse


			if(substr_count($comt, "\n&gt;"))
			{
				$comt_arr = explode("\n",$comt);
				$start_set = 0;
				foreach($comt_arr as $key => $val)
				{
					if(substr($val,0,4) == "&gt;" && !$start_set)
					{
						$comt_arr[$key] = "<div class=bug_reply_txt>".$comt_arr[$key];
						$start_set = 1;
					}
					if(substr($val,0,4) != "&gt;" && $start_set)
					{
						$comt_arr[$key-1].="</div>";
						$start_set = 0;
					}
				}
				if($start_set)
				{
					$comt_arr[$key].="</div>";
				}

				$comt = join ("\n" , $comt_arr);
			}




/*			$comt_arr = explode("\n&gt;",$comt);
			if(sizeof($comt_arr) > 1)
			{

				$last = explode("\n",$comt_arr[sizeof($comt_arr) - 1]);
				$comt_arr[0] = $comt_arr[0]."<div class=bug_reply_txt>";
				$last[0].= "</div>";
				$comt_arr[sizeof($comt_arr) - 1] = join("\n" , $last);
				$comt = join ("\n&gt;" , $comt_arr);
			}




*/
//-------- END vastus divi sisse

//			$comt = $this->parse_commited_msg($comt);

			$p = $u->get_person_for_uid($com->createdby());
			$this->vars(array(
				"com_adder" => $com->createdby(),
				"com_adder_person" => $p->name(),
				"com_date" => date("d.m.Y H:i", $com->created()),
				"com_text" => $comt,
				"txt_respond" => t("vasta"),
				"id" => $com->id(),
			));
			$com_str .= $this->parse("COMMENT");
		}
		$base_author = $o->prop("bug_createdby");
		if(!$base_author)
		{
			$base_author = $o->createdby();
		}
		if($base_com)
		{
			$main_c = "<b>".$base_author." @ ".date("d.m.Y H:i", $o->created())."</b><br>".$this->_split_long_words(nl2br(create_links(preg_replace("/(\&amp\;#([0-9]{4});)/", "&#\\2", htmlspecialchars($o->prop("bug_content"), ENT_NOQUOTES)))));
		}
		elseif($o->prop("com"))
		{
			$main_c = "<b>".$base_author." @ ".date("d.m.Y H:i", $o->created())."</b><br>".$this->_split_long_words(nl2br(create_links(preg_replace("/(\&amp\;#([0-9]{4});)/", "&#\\2", htmlspecialchars($o->prop("com"), ENT_NOQUOTES)))));
		}
		else
		{
			$main_c = '<b>'.$base_author." @ ".date("d.m.Y H:i", $o->created())."</b><br> Tellimus loodi";
		}
		$this->vars(array(
			"main_text" => $so == "asc" ? $main_c : "",
			"main_text_after" => $so == "asc" ? "" : $main_c,
			"COMMENT" => $com_str
		));
		load_javascript ("applications/bug_o_matic_3000/bug.js", "bottom");
		return $this->parse();
	}

	function _split_long_words($comt)
	{
		// split words and check for > 70 chars
		$words = preg_split("/\s+/", strip_tags(trim($comt)));
		foreach($words as $word)
		{
			if (strlen($word) > 70)
			{
				$o_w = $word;
				$n_w = "";
				$l = strlen($word);
				for ($i = 0; $i < $l; $i++)
				{
					if (($i % 70 == 0) && $i > 1)
					{
						$n_w .= "<br>";
					}
					$n_w .= $word[$i];
				}
				$comt = str_replace($o_w, $n_w, $comt);
				$comt = str_replace("\"".$n_w, "\"".$o_w, $comt);
			}
		}

		return $comt;
	}

	function _add_comment($bug, $comment, $old_state = null, $new_state = null, $add_wh = null, $notify = true, $add_wh_cust = null, $add_wh_guess = null)
	{
		if (!is_oid($bug->id()))
		{
			return;
		}
		// email any persons interested in status changes of that bug
		if ($notify || true)
		{
			$this->notify_monitors($bug, $comment, $old_state, $new_state);
		}

		$p = get_current_person()->id();
		if($bug->class_id() == CL_BUG)
		{
			$o = $bug->get_last_comment();
		}
		if(is_object($o)  && $o->created() > (time() - 180) && $o->createdby() == aw_global_get("uid"))
		{
			$o->set_comment(trim($o->comment() . "\n" . $comment));
			if(!$o->prop("prev_state") &&  $old_state)
			{
				$o->set_prop("prev_state", $old_state);
			}
			if($new_state)
			{
				$o->set_prop("new_state", $new_state);
			}

			$o->set_prop("time_real", ($o->prop("time_real") + $add_wh));
			$o->set_prop("time_to_cust", ($o->prop("time_to_cust") + $add_wh_cust));
			$o->set_prop("time_guess", ($o->prop("time_guess") + $add_wh_guess));
			$o->set_prop("show_to_all", $this->comment_for_all);
			$o->set_prop("impl" , $p);
			$o->save();
		}
		else
		{

			$o = obj();
			$o->set_parent($bug->id());
			$o->set_class_id(CL_TASK_ROW);
			$o->set_prop("done" , 1);
			$o->set_prop("task" , $bug->id());
			$o->set_comment(trim($comment));
			$o->set_prop("prev_state", $old_state);
			$o->set_prop("new_state", $new_state);
			$o->set_prop("add_wh", $add_wh);
			$o->set_prop("add_wh_cust", $add_wh_cust);
			$o->set_prop("add_wh_guess", $add_wh_guess);
			$o->set_prop("show_to_all", $this->comment_for_all);
			$o->save();
			$bug->connect(array(
				"to" => $o->id(),
				"type" => "RELTYPE_COMMENT"
			));
		}


		if($add_wh_guess)
		{
			$gbp = $bug->meta("guess_by_p");
			$gbp[$p] = $this->_acc_old_wh_guess + $add_wh_guess;
			$bug->set_meta("guess_by_p", $gbp);
		}
		$rbp = $bug->meta("real_by_p");
		if(isset($this->_acc_old_wh) || $rbp[$p])
		{
			$rbp[$p] = ($this->_acc_old_wh ? $this->_acc_old_wh : $rbp[$p]) + $add_wh;
			$bug->set_meta("real_by_p", $rbp);
		}
		$bug->save();
	}

	function _comments_table($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "user",
			"caption" => t("Kasutaja"),
		));
		$t->define_field(array(
			"name" => "comment",
			"caption" => t("Kommentaar"),
		));
		$t->define_field(array(
			"name" => "add_wh",
			"caption" => t("Tunnid"),
		));
		$t->define_field(array(
			"name" => "time_to_cust",
			"caption" => t("Tunde kliendile"),
		));
		$t->define_field(array(
			"name" => "date",
			"type" => "time",
			"numeric" => 1,
			"format" => "d.m.Y H:i",
			"caption" => t("Aeg"),
		));
		$t->define_field(array(
			"name" => "on_bill",
			"caption" => "<a href='javascript:void(0)' onClick='aw_sel_chb(document.changeform,\"on_bill\")'>".t("Arvele")."</a>",
			"align" => "center",
		));
		$t->set_sortable(false);
		$t->define_data(array(
			"comment" => html::textarea(array(
				"value" => $arr["obj_inst"]->prop("bug_content"),
				"name" => "comment[bug]",
				"rows" => 3,
				"cols" => 60,
			)),
			"user" => $arr["obj_inst"]->createdby(),
			"date" => $arr["obj_inst"]->created(),
		));
		$comments = $arr["obj_inst"]->connections_from(array(
			"type" => "RELTYPE_COMMENT",
		));
		foreach($comments as $c)
		{
			$comm = obj($c->prop("to"));
			$onbill = "";
			if ($comm->prop("bill_id"))
			{
				$onbill = sprintf(t("Arve nr %s"), $comm->prop("bill_id.bill_no"));
			}
			else
			{
				$onbill = html::checkbox(array(
					"name" => "on_bill[".$comm->id()."]",
					"value" => 1,
					"checked" => $comm->prop("on_bill")
				));
			}

			$t->define_data(array(
				"comment" => html::textarea(array(
					"value" => $comm->comment(),
					"name" => "comment[".$comm->id()."]",
					"rows" => 3,
					"cols" => 60,
				)),
				"user" => $comm->createdby(),
				"add_wh" => html::textbox(array(
					"name" => "add_wh[".$comm->id()."]",
					"value" => $comm->prop("add_wh"),
					"size" => "4",
				)),
				"time_to_cust" => html::textbox(array(
					"name" => "time_to_cust[".$comm->id()."]",
					"value" => $comm->prop("time_to_cust"),
					"size" => "4",
				)),
				"date" => $comm->created(),
				"on_bill" => $onbill,
			));
		}

	}

	function get_priority_list()
	{
		$res = array();
		$res[1] = "1 (Madalaim)";
		$res[2] = "2";
		$res[3] = "3";
		$res[4] = "4";
		$res[5] = "5 (K&otilde;rgeim)";
		return $res;
	}

	function get_status_list()
	{
		return $this->bug_statuses;
	}

	function get_class_list()
	{
		return get_class_picker();
	}

	function callback_pre_save($arr)
	{
		$po = obj($arr["request"]["parent"] ? $arr["request"]["parent"] : $arr["request"]["id"]);

		if (!is_oid($po->id()))
		{
			return;
		}

		$pt = $po->path();
		$bt = null;

		foreach($pt as $pi)
		{
			if ($pi->class_id() == CL_BUG_TRACKER)
			{
				$bt = $pi;
			}
		}

		if($bt)
		{
			$conn = $bt->connections_to(array(
				"from.class_id" => CL_BUGTRACK_DISPLAY,
				"type" => "RELTYPE_BUGTRACK"
			));
			foreach($conn as $c)
			{
				$bt_display = obj($c->prop("from"));
			}
		}

		if($arr["request"]["bug_type"] && $bt_display && !$arr["request"]["who"])
		{
			$user = $bt_display->meta("type".$arr["request"]["bug_type"]);
			if($user)
			{
				$arr["obj_inst"]->set_prop("who", $user);
				$this->who_set = 1;
				$err = 0;
			}
			else
			{
				$err = 1;
			}
		}
	}

	function callback_post_save($arr)
	{
		if (is_array($this->add_comments) && count($this->add_comments))
		{
			$this->_add_comment($arr["obj_inst"], join("\n", $this->add_comments), $this->_ac_old_state, $this->_ac_new_state, $this->_acc_add_wh, $this->notify_monitors, $this->_acc_add_wh_cust, $this->_acc_add_wh_guess);
		}

		if (!empty($arr["new"]))
		{
			$this->new_bug = 1;
			$this->notify_monitors($arr["obj_inst"], $arr["obj_inst"]->prop("bug_content"));
			// if this is a new bug, then parse the content and create sub/subsub bugs from it
			if (!empty($arr["request"]["do_split"]))
			{
				$this->_parse_add_bug_content($arr["obj_inst"]);
			}

			if (!empty($arr["request"]["from_problem"]))
			{
				$arr["obj_inst"]->connect(array(
					"to" => $arr["request"]["from_problem"],
					"type" => "RELTYPE_FROM_PROBLEM"
				));
			}
		}

		if(!empty($arr["request"]["is_order"]))
		{
			$c = $arr["obj_inst"]->connections_from(array(
				"type" => "RELTYPE_DEV_ORDER"
			));
			if(!count($c))
			{
				$this->create_dev_order($arr);
			}
		}
	}

	function create_dev_order($arr)
	{
		$o = new object();

		$bt = $this->_get_bt($arr["obj_inst"]);
		if($bt)
		{
			$parent = $bt->prop("do_folder");
		}
		if(!$parent)
		{
			$parent = $arr["obj_inst"]->parent();
		}
		$o->set_class_id(CL_DEVELOPMENT_ORDER);
		$o->set_parent($parent);
		$o->set_name($arr["obj_inst"]->name());
		$o->set_prop("bug_type",$arr["obj_inst"]->prop("bug_type"));
		$o->set_prop("bug_status", 1);
		$o->set_prop("bug_priority", $arr["obj_inst"]->prop("bug_priority"));
		$o->set_prop("bug_app", $arr["obj_inst"]->prop("bug_app"));
		$o->set_prop("deadline", $arr["obj_inst"]->prop("deadline"));
		$o->set_prop("prognosis", $arr["obj_inst"]->prop("prognosis"));
		$o->set_prop("com", $arr["obj_inst"]->prop("bug_content"));
		$o->set_prop("bug_createdby", $arr["obj_inst"]->createdby());

		$u = get_instance(CL_USER);
		$cur = obj($u->get_person_for_uid($arr["obj_inst"]->createdby()));
		$o->set_prop("contactperson", $cur->id());

		$sections = $cur->connections_from(array(
			"class_id" => CL_CRM_SECTION,
			"type" => "RELTYPE_SECTION"
		));
		foreach($sections as $s)
		{
			$sc = $s->to();
			$profs = $sc->connections_from(array(
				"class_id" => CL_CRM_PROFESSION,
				"type" => "RELTYPE_PROFESSIONS"
			));
			foreach($profs as $p)
			{
				$prof = obj($p->conn["to"]);
				if(!$highest)
				{
					$highest = $prof;
				}
				$jrk = $prof->prop("jrk");
				if($highest->prop("jrk")<$jrk)
				{
					$highest = $prof;
				}
			}
		}
		if($highest)
		{
			if($highest->prop("jrk") < 1)
			{
				foreach($sections as $s)
				{
					$highest = $this->_find_highest_prof_recur($s->to());
				}
			}
			$c = new connection();
			$wrl = $c->find(array(
				"from.class_id" => CL_CRM_PERSON_WORK_RELATION,
				"type" => "RELTYPE_PROFESSION",
				"to" => $highest->id()
			));
			foreach($wrl as $c)
			{
				foreach(obj($c["from"])->connections_to(array(
					"from.class_id" => CL_CRM_PERSON,
				)) as $c2)
				{
					$person = $c2->from();
				}
			}
		}

		$creator = obj(get_instance(CL_USER)->get_person_for_uid($arr["obj_inst"]->createdby()));

		if ($person)
		{
			$o->set_prop("orderer", array($person->id()=>$person->id()));
			$o->set_prop("orderer_co", $person->prop("work_contact"));
			$conn = $highest->connections_to(array(
				"type" => "RELTYPE_PROFESSIONS",
				"from.class_id" => CL_CRM_SECTION,
			));
			foreach($conn as $c)
			{
				$sect = $c->prop("from");
			}
			$o->set_prop("orderer_unit", $sect);
			$o->set_prop("monitors", array($creator->id(), get_current_person()->id(), $person->id()));
		}

		$o->save();

		if($com = $arr["request"]["bug_content_comm"])
		{
			$cm = obj();
			$cm->set_class_id(CL_TASK_ROW);
			$cm->set_parent($o->id());
			$cm->set_prop("done", 1);
			$cm->set_prop("task", $o->id());
			$cm->set_comment(trim($com));
			$cm->save();
			$o->connect(array(
				"to" => $cm->id(),
				"type" => "RELTYPE_COMMENT",
			));
		}

		foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_FILE")) as $c)
		{
			$o->connect(array(
				"to" => $c->prop("to"),
				"type" => "RELTYPE_FILE",
			));
		}

		$o->connect(array(
			"to" => $arr["obj_inst"]->id(),
			"type" => "RELTYPE_MAIN_BUG"
		));
		$arr["obj_inst"]->connect(array(
			"to" => $o->id(),
			"type" => "RELTYPE_DEV_ORDER"
		));
		$conn = $arr["obj_inst"]->connections_from(array(
				"type"=>"RELTYPE_FILE"
		));
		foreach($conn as $c)
		{
			$o->connect(array(
				"to" => $c->prop("to"),
				"type" => "RELTYPE_FILE"
			));
		}

		$devo_url =
		$po = obj($arr["request"]["parent"] ? $arr["request"]["parent"] : $arr["request"]["id"]);
		$pt = $po->path();
		$bt_obj = null;
		foreach($pt as $pi)
		{
			if ($pi->class_id() == CL_BUG_TRACKER)
			{
				$bt = $pi;
			}
		}

		$mails = array();
		if($person)
		{
			$mail_text = $bt->prop("dorder_mail_contents");

			$mail = $person->get_first_obj_by_reltype("RELTYPE_EMAIL");
			if(!$mail)
			{
				$mid = $person->prop("email");
				if($this->can("view", $mid))
				{
					$mail = obj($mid);
				}
			}
			$mails["person"] = $mail;
			$mail = $creator->get_first_obj_by_reltype("RELTYPE_EMAIL");
			if(!$mail)
			{
				$mid = $creator->prop("email");
				if($this->can("view", $mid))
				{
					$mail = obj($mid);
				}
			}
			$mails["creator"] = $mail;

			foreach($mails as $id => $mail)
			{
				if($mail)
				{
					$uo = get_instance(CL_CRM_PERSON)->has_user($$id);
					$conn = $uo->connections_from(array(
						"type" => "RELTYPE_GRP",
					));
					$admin = false;
					foreach($conn as $c)
					{
						$grp = $c->to();
						if($grp->prop("can_admin_interface"))
						{
							$admin = true;
						}
					}

					$bug_url = $this->mk_my_orb("change", array("id" => $o->id()), CL_DEVELOPMENT_ORDER, $admin);
					if(!$admin)
					{
						$ol = new object_list(array(
							"class_id" => CL_BUGTRACK_DISPLAY,
							"site_id" => array(),
							"lang_id" => array(),
						));
						$bdo = $ol->begin();
						if($bdo)
						{
							$sect = $bdo->prop("order_doc");
							$bug_url = $this->mk_my_orb("change", array("section" => $sect, "id" => $o->id()), CL_DEVELOPMENT_ORDER, $admin);
						}
						$bug_url = str_replace(array("orb.aw", "automatweb/"), "", $bug_url);
					}
					$find = array(
						"#added_by#",
						"#confirmation_by#",
						"#dev_url#",
						"#dev_id#",
					);
					$replace = array(
						$creator->name(),
						$person->name(),
						$bug_url,
						$o->id(),
					);
					$mail_contents = str_replace($find, $replace, $mail_text);
					$adr = $mail->prop("mail");
					send_mail($adr, t("Lisati arendustellimus"), $mail_contents, "From: bugtrack@".substr(strstr(aw_ini_get("baseurl"), "//"), 2));
				}

			}
		}

		die("<script> window.location = '".$this->mk_my_orb("change", array("id" => $o->id(), "return_url" => $arr["request"]["return_url"]), CL_DEVELOPMENT_ORDER)."' </script>");
	}

	function _find_highest_prof_recur($s)
	{
		$sections = $s->connections_to(array(
			"type" => "RELTYPE_SECTION",
			"from.class_id" => CL_CRM_SECTION,
		));
		foreach($sections as $sct)
		{
			$sc = $sct->from();
			$profs = $sc->connections_from(array(
				"class_id" => CL_CRM_PROFESSION,
				"type" => "RELTYPE_PROFESSIONS"
			));
			foreach($profs as $p)
			{
				$prof = $p->to();
				if(!$highest)
				{
					$highest = $prof;
				}
				$jrk = $prof->prop("jrk");
				if($highest->prop("jrk")<$jrk)
				{
					$highest = $prof;
				}
			}
		}
		if($highest->prop("jrk") < 1)
		{
			foreach($sections as $sct)
			{
				$highest = $this->_find_highest_prof_recur($sct->from());
			}
		}
		return $highest;
	}

	function parse_commited_msg($msg)
	{

		$row =  explode("\n" , $msg);
		//arr($row);
		$result = array("diff" => $row[0], "files" =>  str_replace("<br />" , "" ,$row[6]), "bug" => str_replace("<br />" , "" , $row[8]));
		$time_arr = explode(":" , $row[9]);
		if($time_arr[1])
		{
			$result["time"] = $time_arr[1];
		}

		$by1 = strpos($row[1], 'by') + 3;//arr($by1);
		$by2 = strpos($row[1], ' ', $by1+5);//arr($by2);
		$result["by"] = substr($row[1], $by1, $by2-$by1 );
	//	arr($row[1]);
	//	arr($result);
$diff = explode("*" , $result["diff"]);

		$msg = $result["bug"]." ".join(" " , $diff)."\n".t("Failid: ").$result["files"];
		if($result["time"]) $msg.="\n".t("Aeg:").$result["time"];
		$n = 10;
		while($row[$n])
		{
			$msg.= "\n".$row[$n];
			$n++;
		}
		return $msg;
	}

	function add_to_last_comment($bug, $msg)
	{
		$comm = $bug->get_last_comment();

		if(is_object($comm) && ($comm->modified() + 120 > time()))
		{
			$comment = $comm->comment();
			$c = explode("\n", $comment);
			$m = explode("\n", $msg);
			$new_diffs_array = explode("http", $m[0]);
			$c[0].= " http".$new_diffs_array[1];
			$new_files = substr($m[1], 8);
			$c[1].= " ".$new_files;
			$nc = join("\n", $c);
			$comm->set_prop("comment" , $nc);
			$comm->save();
			return 1;
		}
		return 0;
	}

	/**
		@attrib name=handle_commit nologin=1
		@param bugno required type=int
		@param msg optional
		@param set_fixed optional
		@param time_add optional
	**/
	function handle_commit($arr)
	{
		$u_inst = get_instance("users");
		$bug = obj($arr["bugno"]);
		$msg = trim($this->hexbin($arr["msg"]));

		$orig_msg = $msg;
		$msg = $this->parse_commited_msg($msg);

		$com = false;
		$ostat = $nstat = $bug->prop("bug_status");

		$comments = $bug->connections_from(array(
			"type" => "RELTYPE_COMMENT",
		));
		foreach($comments as $c)
		{
			$comm = obj($c->prop("to"));
		}

		if(is_object($comm) && ($comm->modified() + 120) > time())
		{
			if($this->add_to_last_comment($bug, $msg))
			{
				die(sprintf(t("Modified bug %s last comment"), $arr["bugno"]));
			}
		}

		if ($arr["set_fixed"] == 1)
		{
			$msg .= "\nStaatus muudeti ".html_entity_decode($this->bug_statuses[$bug->prop("bug_status")])." => ".html_entity_decode($this->bug_statuses[self::BUG_DONE])."\n";
			$bug->set_prop("bug_status", self::BUG_DONE);
			$nstat = self::BUG_DONE;
			$save = true;
			$com = true;
		}

		if ($arr["time_add"])
		{
			$ta = $arr["time_add"];
			// parse time
			$hrs = 0;
			if ($ta[strlen($ta)-1] == "m")
			{
				$hrs = ((double)$ta) / 60.0;
			}
			else
			{
				$hrs = (double)$ta;
			}
			// round to 0.25
			$hrs = floor($hrs * 4.0+0.5)/4.0;
			$msg .= sprintf(t("\nTegelik tundide arv muudeti %s => %s"), $bug->prop("num_hrs_real"), $bug->prop("num_hrs_real")+$hrs);
			$bug->set_prop("num_hrs_real", $bug->prop("num_hrs_real") + $hrs);
			$add_wh = $hrs;
			$save = true;
			$com = true;
		}

		// get the cvs uid to aw uid map and switch user if the map has it
		$bt = $this->_get_bt($bug);
		if ($bt)
		{
			$uid_map = $bt->prop("cvs2uidmap");
			if (preg_match("/cvs commit by ([^ ]+) in/imsU", $orig_msg, $mt))
			{
				$cvs_uid = $mt[1];
				foreach(explode("\n", $uid_map) as $map_line)
				{
					list($map_cvs_uid, $map_aw_uid) = explode("=", $map_line);
					if ($map_cvs_uid == $cvs_uid)
					{
						$_SESSION["uid_oid"] = $u_inst->get_oid_for_uid(trim($map_aw_uid));
						aw_switch_user(array("uid" => trim($map_aw_uid)));
					}
				}
			}
		}

		if ($save)
		{
			$bug->save();
		}

		$this->_add_comment($bug, $msg, $ostat, $nstat, $add_wh, $com);
		die(sprintf(t("Added comment to bug %s"), $arr["bugno"]));
	}


	function get_cvs_user_email($who)
	{
		$email = "";
		if($who)
		{
 			$ol = new object_list(array(
	 			"class_id" => CL_BUG_TRACKER
 			));
 			$users = array();
 			foreach($ol->arr() as $o)
 			{
 				$rows = explode("\n" , $o->prop("cvs2uidmap"));
 				foreach($rows as $row)
 				{
 					$usr = explode("=" , $row);
 					if($usr[0] && $usr[1])
 					{
 						$users[trim($usr[0])] = trim($usr[1]);
 					}
 				}
 			}
			if(!$users[$who])
			{
				$users[$who] = $who;
			}
 			if($users[$who])
 			{
 				$us = get_instance(CL_USER);
				$u = $us->get_obj_for_uid($users[$who]);
 				$person = $us->get_person_for_user($u);
 				if(is_oid($person))
 				{
 					$p = obj($person);
					if(is_oid($p->prop("email")))
					{
						$emailo = obj($p->prop("email"));
						$email = $emailo->prop("mail");
					}
				}
 			}
		}
		return $email;
	}

	/**
		@attrib name=send_commit_mail_to_maintainer all_args=1
	**/
	function send_commit_mail_to_maintainer($arr)
	{
return; //TODO: sort out maintainers business first
		extract($arr);
		$cmtr = $who;
		$who = "";

		$aw_loc = str_replace("automatweb_dev" , "" , $GLOBALS["awd"]);
		$myFile = $aw_loc.$path."/".$file;
		$fh = fopen($myFile, 'r');
		$theData = fread($fh, filesize($myFile));
		fclose($fh);

		ereg (".*maintainer=([^[:space:]]*)", $theData, $regs);
		foreach($regs as $reg)
		{
			$who = $reg;
		}

		if($cmtr == $who)
		{
			die();
		}

		if($who)
		{
			$email = $this->get_cvs_user_email($who);
		}

		if($email)
		{
			$text= "Class ".$file." changed\n\n".$this->hexbin($msg);

			send_mail(
				$email,
				$file." new commit",
				$text,
				"From: ".aw_ini_get("baseurl")
			);

			die("\nMail sent to class maintainer - ".$who." (".$email.")\n");
		}
		else
		{
			die("\n".$file." maintainer not found");
		}
	}

	/**
		@attrib name=start_auto_test nologin=1 all_args=1
	**/
	function start_auto_test($arr)
	{
		ignore_user_abort();
		//socket_shutdown(null, 1);
		print "Test started...... \n";
		extract($arr);
		$email = "";
 		if($who = trim($who))
 		{
 			$ol = new object_list(array(
 				"class_id" => CL_BUG_TRACKER
 			));
 			$users = array();
 			foreach($ol->arr() as $o)
 			{
 				$rows = explode("\n" , $o->prop("cvs2uidmap"));
 				foreach($rows as $row)
 				{
 					$usr = explode("=" , $row);
 					if($usr[0] && $usr[1])
 					{
 						$users[trim($usr[0])] = trim($usr[1]);
 					}
 				}
 			}
			if(!$users[$who])
			{
				$users[$who] = $who;
			}
 			if($users[$who])
 			{
 				$us = get_instance(CL_USER);
				$u = $us->get_obj_for_uid($users[$who]);
 				$person = $us->get_person_for_user($u);
 				if(is_oid($person))
 				{
 					$p = obj($person);
					if(is_oid($p->prop("email")))
					{
						$emailo = obj($p->prop("email"));
						$email = $emailo->prop("mail");
					}
				}
 			}
			if(!$email)
			{
				$email = $who;
			}

 			$url = "http://autotest.struktuur.ee/?bug=1&email=".$email."&file=".$file;
 			print $url;
			if($file && (substr_count($file, '.aw') || substr_count($file, '.php')|| substr_count($file, '.xml') || substr_count($file, '.ini')))
			{
 				$a = file_get_contents($url);
			}
			else
			{
				$a = t("seda faili ei testi");
			}
		}
		print $a;
		die();
	}

	function do_db_upgrade($tbl, $f)
	{
		switch($f)
		{
			case "aw_bug_property":
			case "aw_bug_predicates":
				$this->db_add_col($tbl, array(
					"name" => $f,
					"type" => "varchar",
					"length" => 255
				));
				return true;

			case "aw_team":
			case "aw_ocurrence":
			case "aw_density":
			case "aw_team":
			case "aw_cust_responsible":
			case "aw_cust_status":
			case "aw_cust_tester":
			case "aw_actual_live_date":
			case "aw_bug_feedback_p":
			case "project":
			case "who":
			case "deadline":
			case "customer":
			case "customer_unit":
			case "customer_person":
			case "orderer":
			case "orderer_unit":
			case "orderer_person":
			case "fileupload":
			case "is_order":
			case "bug_type":
			case "skill_used":
			case "aw_spec":
			case "aw_finance_type":
			case "prognosis":
			case "aw_send_bill":
			case "aw_to_bill_date":
			case "aw_bug_app":
				$this->db_add_col($tbl, array(
					"name" => $f,
					"type" => "int",
				));
				return true;

			case "aw_cust_solution":
			case "aw_cust_crit":
				$this->db_add_col($tbl, array(
					"name" => $f,
					"type" => "text",
				));
				return true;
			case "aw_hr_price":
			case "aw_cust_budget":
			case "num_hrs_guess":
			case "num_hrs_real":
			case "num_hrs_to_cust":
				$this->db_add_col($tbl, array(
					"name" => $f,
					"type" => "double",
				));
				return true;
		}
	}

	function _get_property_picker($clid)
	{
		$o = obj();
		$o->set_class_id($clid);
		$ret = array("" => "");
		$props = $o->get_property_list();
		foreach($o->get_group_list() as $gn => $gc)
		{
			$ret["grp_".$gn] = $gc["caption"];
			foreach($props as $pn => $pd)
			{
				if ($pd["group"] == $gn)
				{
					$ret["prop_".$pn] = str_repeat("&nbsp;", 3).substr(ifset($pd, "caption"), 0, 20);
				}
			}
		}
		return $ret;
	}

	function request_execute($o)
	{
		if (!$this->read_template("show.tpl", true))
		{
			header("Location: ".$this->mk_my_orb("change", array("id" => $o->id()), "bug", true));
			die();
		}

		return $this->show(array("id" => $o->id()));
	}

	function _bug_tb($arr)
	{
		if (!is_oid($arr["obj_inst"]->id()))
		{
			return;
		}
		$tb = $arr["prop"]["vcl_inst"];

		$tb->add_menu_button(array(
			"name" => "new",
			"tooltip" => t("Uus arendus&uuml;lesanne"),
		));

		$tb->add_menu_item(array(
			"parent" => "new",
			"text" => t("Samale tasemele"),
			"link" => html::get_new_url(CL_BUG, $arr["obj_inst"]->parent(), array("return_url" => $arr["request"]["return_url"])),
			"href_id" => "add_bug_href"
		));
		$tb->add_menu_item(array(
			"parent" => "new",
			"text" => t("Sisse"),
			"link" => html::get_new_url(CL_BUG, $arr["obj_inst"]->id(), array("return_url" => $arr["request"]["return_url"])),
			"href_id" => "add_bug_hrefp"
		));
	}

	function _parse_add_bug_content($o)
	{
		$c = $o->prop("bug_content");
		if (strpos($c, "1)") === false)
		{
			return;
		}

		$ls = explode("\n", $c);
		$bugs = array();
		foreach($ls as $line)
		{
			if (trim($line) == "")
			{
				continue;
			}

			if (preg_match("/([0-9\.]+)\)/imsU", $line, $mt))
			{
				if ($cur_b != "")
				{
					$bugs[$cur_num] = $cur_b;
				}
				$cur_num = $mt[1];
				$cur_b = str_replace($mt[0], "", $line);
			}
			else
			{
				$cur_b .= $line;
			}
		}
		if ($cur_b != "")
		{
			$bugs[$mt[1]] = $cur_b;
		}

		ksort($bugs);
		foreach($bugs as $pt => $ct)
		{
			if (strpos($pt, ".") === false)
			{
				$parent = $o->id();
			}
			else
			{
				// find the parent by the sub-bug number
				$parts = explode(".", $pt);
				foreach($num2bug as $num => $bug_id)
				{
					$tparts = explode(".", $num);
					if (count($tparts) == (count($parts) - 1) && substr($pt, 0, strlen($num)) == $num)
					{
						$parent = $bug_id;
					}
				}
			}

			$b = obj();
			$b->set_parent($parent);
			$b->set_class_id(CL_BUG);
			$b->set_name(substr($ct, 0, 50));
			$b->set_prop("bug_content", $ct);
			$b->set_prop("bug_status", $o->prop("bug_status"));
			$b->set_prop("bug_priority", $o->prop("bug_priority"));
			$b->set_prop("who", $o->prop("who"));
			$b->set_prop("bug_type", $o->prop("bug_type"));
			$b->set_prop("bug_class", $o->prop("bug_class"));
			$b->set_prop("bug_severity", $o->prop("bug_severity"));
			$b->set_prop("monitors", $o->prop("monitors"));
			$b->set_prop("deadline", $o->prop("deadline"));
			$b->set_prop("num_hrs_guess", $o->prop("num_hrs_guess"));
			$b->set_prop("num_hrs_real", $o->prop("num_hrs_real"));
			$b->set_prop("num_hrs_to_cust", $o->prop("num_hrs_to_cust"));
			$b->set_prop("customer", $o->prop("customer"));
			$b->set_prop("project", $o->prop("project"));
			$b->set_prop("bug_component", $o->prop("bug_component"));
			$b->set_prop("bug_mail", $o->prop("bug_mail"));
			$b->set_prop("bug_property", $o->prop("bug_property"));
			$b->save();
			$num2bug[$pt] = $b->id();
		}
	}

	function _get_bt($o)
	{
		if (!is_oid($o->id()) && !is_oid($o->parent()))
		{
			return null;
		}
		if (!is_oid($o->id()))
		{
			$o = obj($o->parent());
		}
		$pt = $o->path();
		foreach($pt as $pt_o)
		{
			if ($pt_o->class_id() == CL_BUG_TRACKER)
			{
				return $pt_o;
			}
		}
		return null;
	}

	function _get_customer_person($arr)
	{
		// list all ppl for the selected co
		$cust = $unit = 0;
		if (!empty($arr["new"]))
		{
			if (!empty($arr["request"]["from_problem"]))
			{
				$pr = obj($arr["request"]["from_problem"]);
				$cust = $pr->prop("customer");
			}
		}
		else
		{
			$cust = $arr["obj_inst"]->prop("customer");
			$unit = $arr["obj_inst"]->prop("customer_unit");
		}

		if ($this->can("view", $cust) && $this->can("view", $unit))
		{
			// get all ppl for the section
			try
			{
				$customer = obj($cust, array(), CL_CRM_COMPANY);
				$section = obj($unit, array(), CL_CRM_SECTION);
				$work_ol = $customer->get_employees("active", null, $section);
				$arr["prop"]["options"] = html::get_empty_option() + $work_ol->names();
			}
			catch (awex_obj $e)
			{
			}
		}
		elseif ($this->can("view", $cust))
		{
			$co = new crm_company();
			$arr["prop"]["options"] = $co->get_employee_picker(obj($cust), true);
		}
		else
		{
			$arr["prop"]["options"] = array("" => t("--vali--"));
		}

		$p = get_current_person();
		// add the current person and his boss
		$arr["prop"]["options"][$p->id()] = $p->name();

		$sect = new crm_section();
		$units = safe_array($p->prop("org_section"));
		$unit = reset($units);
		if ($this->can("view", $unit))
		{
			$work_ol = $sect->get_section_workers($unit, true);
			foreach($work_ol->names() as $id => $name)
			{
				$arr["prop"]["options"][$id] = $name;
			}
		}
	}

	function _get_orderer($arr)
	{
		if (!empty($arr["new"]))
		{
			if (!empty($arr["request"]["from_problem"]))
			{
				$pr = obj($arr["request"]["from_problem"]);
				$cust = $pr->prop("orderer_co");
			}
		}

		if (!empty($cust))
		{
			$arr["prop"]["value"] = $cust;
		}

		if (!isset($arr["prop"]["options"]) or !is_array($arr["prop"]["options"]))
		{
			$arr["prop"]["options"] = html::get_empty_option();
		}

		if (isset($arr["prop"]["value"]) && !isset($arr["prop"]["options"][$arr["prop"]["value"]]) && $this->can("view", $arr["prop"]["value"]))
		{
			$tmp = obj($arr["prop"]["value"]);
			$arr["prop"]["options"][$arr["prop"]["value"]] = $tmp->name();
		}
	}

	function _get_orderer_unit($arr)
	{
		$sects = array();
		$prop =& $arr["prop"];
		if ($this->can("view", $arr["obj_inst"]->prop("orderer")))
		{
			$co = obj($arr["obj_inst"]->prop("orderer"));
		}

		if(!isset($co) || $co->class_id() != CL_CRM_COMPANY)
		{
			$co = get_current_company();
			if ($co)
			{
				$co_i = $co->instance();
				$sects = $co_i->get_all_org_sections($co);
			}
		}

		$prop["options"] = html::get_empty_option();

		if(!empty($prop["value"]))
		{
			$prop["options"][$prop["value"]] = obj($prop["value"])->name();
		}

		if (count($sects))
		{
			$ol = new object_list(array("oid" => $sects));
			foreach($ol->arr() as $oid => $o)
			{
				$prop["options"][$oid] = $o->name();
			}
		}

		$p = get_current_person();

		if (!empty($arr["new"]))
		{
			if (!empty($arr["request"]["from_problem"]))
			{
				$tmp = obj($arr["request"]["from_problem"]);
				$prop["value"] = $tmp->prop("orderer_unit");
			}
			else
			{
				$prop["value"] = $p->prop("org_section");
			}
		}
	}

	function _get_orderer_person($arr)
	{
		// list all ppl for the selected co
		$cust = $unit = 0;
		if (!empty($arr["new"]))
		{
			if (!empty($arr["request"]["from_problem"]))
			{
				$pr = obj($arr["request"]["from_problem"]);
				$cust = $pr->prop("orderer_co");
				$unit = $pr->prop("orderer_unit");
			}
		}
		else
		{
			$cust = $arr["obj_inst"]->prop("orderer");
			$unit = $arr["obj_inst"]->prop("orderer_unit");
		}

		if ($this->can("view", $cust) && $this->can("view", $unit))
		{
			// get all ppl for the section
			$sect = get_instance(CL_CRM_SECTION);
			$work_ol = $sect->get_section_workers($unit, true);
			$arr["prop"]["options"] = html::get_empty_option();
			foreach($work_ol->arr() as $oid => $o)
			{
				$arr["prop"]["options"][$oid] =  $o->name();
			}
		}
		elseif ($this->can("view", $cust))
		{
			$co = get_instance(CL_CRM_COMPANY);
			$arr["prop"]["options"] = $co->get_employee_picker(obj($cust), true);
		}
	}

	function _get_problems_table($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$ol = new object_list($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_FROM_PROBLEM")));
		$t->table_from_ol($ol, array("name", "createdby", "created", "orderer_co", "orderer_unit", "customer", "project", "requirement", "from_dev_order", "from_bug"), CL_CUSTOMER_PROBLEM_TICKET);
	}

	function _handle_status_change($old, $new, $bug, &$prop)
	{
		$retval = PROP_OK;
		if($new == self::BUG_CLOSED && $old != self::BUG_CLOSED)
		{
			$canclose = 0;
			if(aw_global_get("uid") == $bug->createdby())
			{
				$canclose = 1;
			}
			else
			{
				$u = get_instance(CL_USER);
				$user = obj($u->get_current_user());
				$conn = $user->connections_from(array(
					"type" => "RELTYPE_GRP"
				));
				$bugtrack = $this->_get_bt($bug);
				$agroups = $bugtrack->connections_from(array(
					"type" => "RELTYPE_AGROUP"
				));
				foreach($conn as $c)
				{
					foreach($agroups as $agroup)
					{
						if($c->conn["to"] == $agroup->conn["to"])
						{
							$canclose = 1;
						}
					}
				}
			}

			if(!$canclose && false)
			{
				$retval = PROP_FATAL_ERROR;
				$prop["error"] = t("Puuduvad &otilde;igused bugi sulgeda!");
			}
		}

		if ($new == self::BUG_FEEDBACK && $old != self::BUG_FEEDBACK)
		{
			// set the creator as the feedback from person
			$u = get_instance(CL_USER);
			$p = $u->get_person_for_uid($bug->createdby());
			$bug->set_prop("bug_feedback_p", $p->id());
			$this->_set_feedback = $p->id();
		}

		return $retval;
	}

	function handle_stopper_start($o)
	{
		if ($o->prop("bug_status") == self::BUG_OPEN)
		{
			$o->set_prop("bug_status",  self::BUG_INPROGRESS);
			$o->save();
		}
	}

	function show($arr)
	{
		$o = obj($arr["id"]);
		$this->read_template("show.tpl");

		$this->vars($o->properties());

		$sl = $this->get_status_list();
		$i = $o->instance();
		$this->vars(array(
			"fileupload" => "",
			"comments" => $i->_get_comment_list($o),
			"bug_status" => $sl[$o->prop("bug_status")]
		));
		$fo = $o->get_first_obj_by_reltype("RELTYPE_FILE");

		if ($fo)
		{
			$i = $fo->instance();
			$s = $i->parse_alias(array("alias" => array("target" => $fo->id()), "htmlentities" => true));
			$this->vars(array(
				"fileupload" => $s
			));
		}

		return $this->parse();
	}

	function callback_get_cfgmanager($arr)
	{
		if (!empty($arr["request"]["id"]))
		{
			$o = obj($arr["request"]["id"]);
		}
		else
		{
			$o = obj($arr["request"]["parent"]);
		}
		$bt = $this->_get_bt($o);
		if ($bt && $bt->prop("default_cfgmanager"))
		{
			return $bt->prop("default_cfgmanager");
		}
	}

	function callback_generate_scripts($arr)
	{
		$s_bug_stopper_watch_v2 = <<<EOF
		jQuery.fn.stopper_watch = function(arr)
		{
			jQuery.stopper_watch($(this), arr);
			return this;
		};

		jQuery.stopper_watch = function(object, arr) {
			time = 0;
			var old_time = false; // value in input field
			var pause = false;
			var thisdate = new Date();
			var timestamp_start = thisdate.getTime();
			var seconds_start = 0;
			var time_before_pause = parseFloat($("#bug_add_real").val());

			_start_stopper();
			_handlers();
			// + stop visual jumping
			object.parent().css("width", "600px");

			function _handlers()
			{
				object.children().click(function () {
					_toggle_stopper(false);
					return false;
				});
				$("#bug_stopper_pause_link").click(function () {
					_toggle_stopper(false);
					return false;
				});
				$("#bug_add_real").focus(function () {
					_toggle_stopper(true);
					return false;
				});
				$("#bug_stopper_clear_link").click(function () {
					_toggle_stopper(true);
					$("#bug_add_real").val(0.00);
					return false;
				});
			}

			function _start_stopper()
			{
				$.timer(1000, function f (timer){
					thisdate = new Date();
					ts_time = thisdate.getTime()-timestamp_start;
					tmp = ts_time/1000/60/60;
					if (pause)
					{
						//alert (time_before_pause);
						time_before_pause += tmp;
						//alert (time_before_pause);
						timer.stop();
						return;
					}
					time = (time_before_pause+tmp).toFixed(4)*1.0;
					object.children().html("<a href=''>"+_return_normal_clock(seconds_start)+" ("+time+")</a>")
					seconds_start = Math.round((time_before_pause + tmp) * 60 * 60);
					tmp = time.toFixed(2)*1.0
					$("#bug_add_real").val(r2(tmp));
				})
			}

			function _toggle_stopper(force_pause)
			{
				if (pause && !force_pause)
				{
					pause = false;
					thisdate = new Date();
					timestamp_start = thisdate.getTime();
					time_before_pause = parseFloat($("#bug_add_real").val())
					_start_stopper();
					$("#bug_stopper_pause_link").children().html("Paus")
				}
				else if(!pause)
				{
					tmp = time.toFixed(2)*1.0
					$("#bug_add_real").val(r2(tmp));
					pause = true
					$("#bug_stopper_pause_link").children().html("J&auml;tka")
				}
			}

			function _return_normal_clock(seconds)
			{
				var s = seconds;
				var m = 0;
				var h = 0;
				while (s>59)
				{
					s -= 60;
					m++;
				}
				while (m>59)
				{
					m -= 60;
					h++;
				}
				if (h < 10)
				{
					h = "0"+h;
				}
				if (m < 10)
				{
					m = "0"+m;
				}
				if (s < 10)
				{
					s = "0"+s;
				}
				return h+":"+m+":"+s;
			}

			function r2(n)
			{
				ans = n * 1000
				ans = Math.round(ans /10) + ""
				while (ans.length < 3) {ans = "0" + ans}
				len = ans.length
				ans = ans.substring(0,len-2) + "." + ans.substring(len-2,len)
				return ans
			}
		}

		$("#bug_stopper_watch_time").stopper_watch();
EOF;
		$bt = $this->_get_bt($arr["obj_inst"]);
		if(!$bt && $arr["request"]["action"] === "new")
		{
			$p = $arr["request"]["parent"];
			$tmpo = obj($p);
			$path = $tmpo->path();
			foreach($path as $po)
			{
				if($po->class_id() == CL_BUG_TRACKER)
				{
					$bt = $po;
					break;
				}
			}
		}

		$maintainers = "";
/* //TODO: sort out maintainers business first
		if($bt)
		{
			$url = $this->mk_my_orb("maintainer_ajax", array("id" => $bt->id()));
			$maintainers = '
			s_url = "'.$url.'"
			function check_class_maintainer()
			{
				cl_el = aw_get_el("bug_class")
				cl = cl_el.value
				geturl = s_url + "&clid=" + cl
				data = aw_get_url_contents(geturl)
				tmp = data.split("|")
				if(tmp[0] == "found")
				{
					oid = tmp[1]
					name = tmp[2]
					who_el = aw_get_el("who")
					who_num = null
					for(i = 0; i < who_el.options.length; i++)
					{
						if(who_el.options[i].value == oid)
						{
							who_num = i
						}
					}
					if(who_num == null)
					{
						who_num = who_el.options.length
						who_el.options[who_num] = new Option(name, oid)
					}
					if(who_el.value == oid)
					{
						return 0
					}
					var chk = confirm("'.t("Panen klassi maintaineri bugi tegijaks?").'")
					if(!chk)
					{
						return 0
					}
					who_el.value = oid
					mon_el = aw_get_el("monitors")
					mon_num = null
					for(i = 0; i < mon_el.options.length; i++)
					{
						if(mon_el.options[i].value == oid)
						{
							mon_num = i
						}
					}
					if(mon_num == null)
					{
						mon_num = mon_el.options.length
						mon_el.options[mon_num] = new Option(name, oid)
					}
					mon_el.options[mon_num].selected = true
				}
			}';
		}
*/
		if (automatweb::$request->arg("group") === "general" || automatweb::$request->arg("group") == "")
		{
			$hide_fb = <<<EOF
				if ($("#bug_status").val() != 10)
				{
					$("#bug_feedback_p").parent().parent().css("display", "none");
				}
EOF;
		}

		if (!empty($arr["request"]["id"]))
		{
			$o = obj($arr["request"]["id"]);
		}
		else
		{
			$o = obj($arr["request"]["parent"]);
		}

		$bt = $this->_get_bt($o);
		$options = array(0 => t("--vali--"));
		if($bt)
		{
			$ol = new object_list(array(
				"parent" => $bt->id(),
				"class_id" => array(CL_BUG_APP_TYPE)
			));
			$ol->sort_by_cb(array($this, "__bug_app_sorter"));
			foreach($ol->arr() as $oid => $o)
			{
				$t = $o->get_first_obj_by_reltype("RELTYPE_TYPE");
				if($t)
				{
					$options[$t->id()][$oid] = $o->name();
				}
			}
		}


		$type_app = "
			var opts = new Array()";
		foreach(safe_array($options) as $t => $option)
		{
			$type_app .=  "
 				opts[$t] = new Array()";
			foreach(safe_array($option) as $oid => $name)
			{
				$type_app .= "
				opts[$t][$oid] = '$name'";
			}
		}
		$type_app .= "
			function change_bug_app(type)
			{
				var tmp = new Array();
				if(opts[type] && opts[type].length)
				{
					tmp = opts[type]
					var bug_app = aw_get_el('bug_app')
					count = 1
					bug_app.options.length = count;
					for(x in tmp)
					{
						bug_app.options[count++] = new Option( tmp[x], x)
					}
				}
			}
			change_bug_app(document.forms.changeform.bug_type.value)
			";
		if($this->bug_app_value)
		{
			$type_app .= "
			var bug_app = aw_get_el('bug_app')
			bug_app.value = '".$this->bug_app_value."';";
		}

		if (empty($arr["request"]["group"]) || empty($arr["request"]["general"]))
		{
			return $hide_fb.$maintainers.$s_bug_stopper_watch_v2.$type_app;
		}

		if (!$arr["new"])
		{
			return "";
		}

		return
		"function aw_submit_handler() {".
		"var url = '".$this->mk_my_orb("check_multiple_content")."';".
		"url = url + '&bug_content=' + document.changeform.bug_content.value;".
		"num= parseInt(aw_get_url_contents(url));".
		"if (num >0)
		{
			var ansa = confirm('Kas jaotada alambugideks?');
			if (ansa)
			{
				document.changeform.do_split.value=1;
			}
		}".
		"return true;}";
	}

	function __bug_app_sorter($a, $b)
	{
		if($a->ord() == $b->ord())
		{
			return strcasecmp($a->name(), $b->name());
		}
		else
		{
			return ($a->ord() < $b->ord()) ? -1 : 1;
		}
	}

	/**
	@attrib name=maintainer_ajax all_args=1
	**/
	function maintainer_ajax($arr)
	{
exit; //TODO: sort out maintainers business first
		$o = obj();
		$o->set_class_id($arr["clid"]);
		$dat = $o->get_classinfo();
		$mu = $dat["maintainer"];
		$bt = obj($arr["id"]);
		$map = explode(chr(13).chr(10), $bt->prop("cvs2uidmap"));
		foreach($map as $row)
		{
			$tmp = explode("=", $row);
			if($tmp[0] == $mu)
			{
				$user = $tmp[1];
			}
		}
		if($user)
		{
			$ui = get_instance(CL_USER);
			$uo = $ui->get_obj_for_uid($user);
			if($uo)
			{
				$po = $uo->get_first_obj_by_reltype("RELTYPE_PERSON");
			}
		}
		if($po)
		{
			die("found|".$po->id()."|".$po->name());
		}
		die();
	}

	/**
		@attrib name=check_multiple_content
		@param bug_content optional
	**/
	function check_multiple_content($arr)
	{
		if (strpos($arr["bug_content"], "1)") === false)
		{
			die("0");
		}
		die("1");
	}

	function get_person_times($arr)
	{
		$conn = $arr["obj_inst"]->connections_from(array(
			"type" => "RELTYPE_COMMENT"
		));
		$ppl_r_times = $ppl_g_times = array();
		foreach($conn as $c)
		{
			$cmo = $c->to();
			$ppl_r_times[$cmo->createdby()] = $cmo->prop("add_wh") + ifset($ppl_r_times, $cmo->createdby());
			$ppl_g_times[$cmo->createdby()] = $cmo->prop("add_wh_guess") + ifset($ppl_g_times, $cmo->createdby());
		}
		$ui = get_instance(CL_USER);
		$total = 0;
		foreach((($arr["prop"]["name"] === "num_hrs_real") ? $ppl_r_times : $ppl_g_times) as $u => $time)
		{
			if($time)
			{
				$person = obj($ui->get_person_for_user($ui->get_obj_for_uid($u)));
				$values[] = $person->name().": ".html::span(array(
					"content" => $time,
					"color" => ($arr["prop"]["name"] === "num_hrs_real" && $time > $ppl_g_times[$u]) ? "#FF0000" : "",
				));
				$total += $time;
			}
		}

		if($arr["prop"]["name"] === "num_hrs_guess" && isset($values) && count($values) > 1)
		{
			$values[] = t("Kokku:")." ".$total;
		}
		return isset($values) ? implode(html::linebreak(), $values) : "";
	}

	function get_finance_types()
	{
		return array(
			1 => t("T&ouml;&ouml; l&otilde;ppedes"),
			2 => t("Projekti l&otilde;ppedes"),
//			3 => t("Arendus")
		);
	}

	function filter_bug_statuses($statuses, $arr)
	{
		$po = obj(!empty($arr["request"]["parent"]) ? $arr["request"]["parent"] : $arr["request"]["id"]);
		if (!is_oid($po->id()))
		{
			return;
		}
		$pt = $po->path();
		$bt = null;
		foreach($pt as $pi)
		{
			if ($pi->class_id() == CL_BUG_TRACKER)
			{
				$bt = $pi;
			}
		}

		if($bt)
		{
			if($arr["prop"]["name"] === "bug_status")
			{
				if($arr["obj_inst"]->class_id() == CL_BUG)
				{
					$btst_prop = "status_disp_bug";
				}
				else
				{
					$btst_prop = "status_disp_devo";
				}
			}
			else
			{
				$btst_prop = "status_disp_cust";
			}
			$filter = $bt->meta($btst_prop);
			if(!is_array($filter))
			{
				$filter = array();
			}
			foreach($statuses as $stid => $status)
			{
				if(ifset($filter, $stid) === "no")
				{
					unset($statuses[$stid]);
				}
			}
		}
		return $statuses;
	}

	private function get_bug_parent($parent)
	{
		if($this->can("add" , $parent))
		{
			return $parent;
		}
		$bugtracks = new object_list(array(
			"class_id" => CL_BUG_TRACKER
		));
		$bugtrack = reset();
		foreach($bugtracks->arr() as $bugtrack)
		{
			if($bugtrack->prop("default_bug_parent"))
			{
				return $bugtrack->prop("default_bug_parent");
			}
		}
	}

	/**
		@attrib name=quick_add all_args=1
	**/
	function quick_add($arr)
	{
		$company = get_current_company();
		if($arr["bug_content"] || $arr["name"])
		{
			$o = new object();
			$o->set_class_id(CL_BUG);
			$o->set_parent($this->get_bug_parent($arr["parent"]));
			$o->set_name($arr["name"]);
			$o->set_prop("bug_status" , self::BUG_OPEN);
			foreach($arr as $key => $val)
			{
				switch($key)
				{
					case "bug_priority":
					case "bug_severity":
					case "who":
					case "bug_class":
					case "deadline":
					case "hr_price":
					case "bug_url":
					case "bug_content":
						$o->set_prop($key , $val);
						break;
				}
			}

			if($arr["customer"])
			{
				$customers = new object_list(array(
					"class_id" => CL_CRM_COMPANY,
					"name" => $arr["customer"],
					"limit" => 1
				));
				$customer = reset($customers->arr());
				if(!$customer)
				{
					$customer = obj($company->add_customer($arr["customer"]));
				}
				if(is_object($customer))
				{
					$o->set_prop("customer" ,$customer->id());
				}
			}

			if($arr["customer_unit"] && is_object($customer))
			{
				$customer_unit = $customer->get_section_by_name($arr["customer_unit"]);
				if($customer_unit)
				{
					$o->set_prop("customer_unit" ,$customer_unit);
				}
			}

			if($arr["customer_person"] && is_object($customer))
			{
				$customer_persons = new object_list(array(
					"class_id" => CL_CRM_PERSON,
					"name" => $arr["customer_person"],
					"limit" => 1
				));
				$customer_person = reset($customer_persons->ids());
				if(!$customer_person)
				{
					$customer_person = $customer->add_worker_data(array(
						"worker" => $arr["customer_person"],
						"section" => $customer_unit,
					));
				}
				if($customer_person)
				{
					$o->set_prop("customer_person" ,$customer_person);
				}
			}

			if($arr["orderer"])
			{
				$orderers = new object_list(array(
					"class_id" => CL_CRM_COMPANY,
					"name" => $arr["orderer"],
					"limit" => 1
				));
				$orderer = reset($orderers->arr());
				if(is_object($orderer))
				{
					$o->set_prop("orderer" ,$orderer->id());
				}
			}

			if($arr["orderer_unit"] && is_object($orderer))
			{
				$orderer_units = new object_list(array(
					"class_id" => CL_CRM_SECTION,
					"name" => $arr["orderer_unit"],
					"limit" => 1
				));
				$orderer_unit = $orderer->get_section_by_name($arr["orderer_unit"]);
				if($orderer_unit)
				{
					$o->set_prop("orderer_unit" ,$orderer_unit);
				}
			}

			if($arr["orderer_person"] && is_object($orderer))
			{
				$orderer_persons = new object_list(array(
					"class_id" => CL_CRM_PERSON,
					"name" => $arr["orderer_person"],
					"limit" => 1
				));
				$orderer_person = reset($orderer_persons->ids());
				if(!$orderer_person)
				{
					$orderer_person = $orderer->add_worker_data(array(
						"worker" => $arr["orderer_person"],
						"section" => $orderer_unit,
					));
				}
				if($orderer_person)
				{
					$o->set_prop("orderer_person" ,$orderer_person);
				}
			}

			if($arr["project"])
			{
				if(is_object($customer))
				{
					$projects = $customer->get_projects_as_customer();
					foreach($projects->names() as $id => $name)
					{
						if($arr["project"] == $name)
						{
							$project = $id;
							break;
						}
					}
					if(!$project)
					{
						$project = $customer->add_project_as_customer($arr["project"]);
					}
				}
				else
				{
					$projects = new object_list(array(
						"class_id" => CL_PROJECT,
						"name" => $arr["project"],
						"limit" => 1
					));
					$project = reset($projects->ids());
				}
				if($project)
				{
					$o->set_prop("project" ,$project);
				}
			}


			$u = get_instance(CL_USER);
			$p = obj($u->get_current_person());
			$o->set_prop("monitors" , array($p->id(), $p->id()));

			$o->save();

			$res = "<script language='javascript'>window.close();</script>";
			die($res);
		}
		$co_inst = get_instance(CL_CRM_COMPANY);
		$htmlc = get_instance("cfg/htmlclient");
		$htmlc->start_output();

		$htmlc->add_property(array(
			"name" => "name",
			"type" => "textbox",
			"caption" => t("L&uuml;hikirjeldus"),
		));

		$htmlc->add_property(array(
			"name" => "bug_priority",
			"type" => "select",
			"options" => $this->get_priority_list(),
			"value" => 3,
			"caption" => t("Prioriteet"),
		));
		$htmlc->add_property(array(
			"name" => "bug_severity",
			"type" => "select",
			"options" => $this->get_priority_list(),
			"caption" => t("T&otilde;sidus"),
		));


		$htmlc->add_property(array(
			"name" => "who",
			"type" => "select",
			"caption" => t("Kellele"),
			"options" => $company->get_worker_selection(),
		));

		$htmlc->add_property(array(
			"name" => "bug_class",
			"type" => "select",
			"caption" => t("Klass"),
			"options" => array("" => "") + $this->get_class_list(),
			// "onchange" => "check_class_maintainer();",//TODO: sort out maintainers business first
		));

		$htmlc->add_property(array(
			"name" => "deadline",
			"type" => "date_select",
			"caption" => t("T&auml;htaeg"),
			"value" => time() + 31*24*3600,
		));

		$htmlc->add_property(array(
			"name" => "finance_type",
			"type" => "chooser",
			"caption" => t("Kulud kaetakse"),
			"options" => $this->get_finance_types(),
		));

		$htmlc->add_property(array(
			"name" => "hr_price",
			"type" => "textbox",
			"caption" => t("Tunnihind"),
		));

		$htmlc->add_property(array(
			"name" => "bug_url",
			"type" => "textbox",
			"caption" => t("URL"),
		));

		$htmlc->add_property(array(
			"name" => "bug_content",
			"type" => "textarea",
			"caption" => t("Sisu"),
			"rows" => 20,
			"cols" => 60,
		));

		$htmlc->add_property(array(
			"name" => "klient",
			"type" => "text",
			"caption" => t("Klient"),
			"subtitle" => 1
		));

		$htmlc->add_property(array(
			"name" => "customer",
			"type" => "textbox",
			"caption" => t("Organisatsioon"),
			"autocomplete_class_id" => array(CL_CRM_COMPANY),
		));

		$htmlc->add_property(array(
			"name" => "customer_unit",
			"type" => "textbox",
			"caption" => t("&Uuml;ksus"),
			"autocomplete_source" => "/automatweb/orb.aw?class=crm_company&action=unit_options_autocomplete_source",
			"autocomplete_params" => array("customer"),
		));

		$htmlc->add_property(array(
			"name" => "customer_person",
			"type" => "textbox",
			"caption" => t("Isik"),
			"autocomplete_source" => "/automatweb/orb.aw?class=crm_company&action=worker_options_autocomplete_source",
			"autocomplete_params" => array("customer"),
		));

		$htmlc->add_property(array(
			"name" => "tellija",
			"type" => "text",
			"caption" => t("Tellija"),
			"subtitle" => 1
		));

		$htmlc->add_property(array(
			"name" => "orderer",
			"type" => "textbox",
			"caption" => t("Organisatsioon"),
			"autocomplete_class_id" => array(CL_CRM_COMPANY),
		));

		$htmlc->add_property(array(
			"name" => "orderer_unit",
			"type" => "textbox",
			"caption" => t("&Uuml;ksus"),
			"autocomplete_source" => "/automatweb/orb.aw?class=crm_company&action=unit_options_autocomplete_source",
			"autocomplete_params" => array("orderer"),
		));

		$htmlc->add_property(array(
			"name" => "orderer_person",
			"type" => "textbox",
			"caption" => t("Isik"),
			"autocomplete_source" => "/automatweb/orb.aw?class=crm_company&action=worker_options_autocomplete_source",
			"autocomplete_params" => array("orderer"),
		));

		$htmlc->add_property(array(
			"name" => "project",
			"type" => "textbox",
			"caption" => t("Projekt"),
			"autocomplete_source" => "/automatweb/orb.aw?class=crm_company&action=proj_autocomplete_source",
			"autocomplete_params" => array("customer","project"),
		));

		$htmlc->add_property(array(
			"name" => "sub",
			"type" => "button",
			"value" => t("Lisa uus &Uuml;lesanne!"),
			"onclick" => "changeform.submit();",
			"caption" => t("Lisa uus &Uuml;lesanne!")
		));
		$data = array(
			"orb_class" => $_GET["class"] ? $_GET["class"]:$_POST["class"],
			"reforb" => 0,
			"parent" => $_GET["parent"],
		);
		$htmlc->finish_output(array(
			"action" => "quick_add",
			"method" => "POST",
			"data" => $data,
			"submit" => "no"
		));

		$content = $htmlc->get_result();
		return $content;
	}

	/**
		@attrib name=orderer_autocomplete_source
		@param customer optional
		@param customer_name optional
		@param orderer optional
	**/
	function orderer_autocomplete_source($arr)
	{
		$cl_json = get_instance("protocols/data/json");
		if(!$arr["customer"])
		{
			$arr["customer"] = $arr["customer_name"];
		}
		$errorstring = "";
		$error = false;
		$autocomplete_options = array();

		$option_data = array(
			"error" => &$error,// recommended
			"errorstring" => &$errorstring,// optional
			"options" => &$autocomplete_options,// required
			"limited" => false,// whether option count limiting applied or not. applicable only for real time autocomplete.
		);
		$customers = new object_list(array(
			"class_id" => array(CL_CRM_COMPANY),
			"name" => iconv("UTF-8", aw_global_get("charset"), $arr["customer"])."%",
			"limit" => 3
		));
		$orderers = new object_list();
		foreach($customers->arr() as $cust)
		{
			$orderers->add($cust->get_workers());
		}

		$ol = new object_list(array(
			"class_id" => array(CL_PERSON),
			"name" => iconv("UTF-8", aw_global_get("charset"), $arr["orderer"])."%",
			"oid" => $orderers->ids()
		));
		$autocomplete_options = $orderers->names();

		foreach($autocomplete_options as $k => $v)
		{
			$autocomplete_options[$k] = iconv(aw_global_get("charset"), "UTF-8", parse_obj_name($v));
		}
		header("Content-type: text/html; charset=utf-8");
		exit ($cl_json->encode($option_data));
	}
}
