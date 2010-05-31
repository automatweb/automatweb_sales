<?php

namespace automatweb;
// stats_viewer.aw - Statistika
/*

@classinfo syslog_type=ST_STATS_VIEWER relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo

@default table=objects
@default group=general

@default group=data

	@property data_table type=table store=no no_caption=1

@default group=stats_filter
@default field=meta
@default method=serialize

	@layout a type=vbox
	@property s_tb type=toolbar store=no no_caption=1 parent=a

	@property saved_filter_name type=textbox parent=a store=no
	@caption Salvestatava filtri nimi
	@comment J&auml;ta t&uuml;hjaks kui ei soovi salvestatud filtrite nimekirja hulka salvestada

	@layout dates type=vbox closeable=1 area_caption=Kuup&auml;evad
		@layout dates_top type=hbox parent=dates width=50%:50%
			@layout dates_left type=vbox parent=dates_top
				@property s_date_from type=date_select parent=dates_left
				@caption Alates

				@property s_date_to type=date_select parent=dates_left
				@caption Kuni

			@layout dates_right type=vbox parent=dates_top
				@property s_hr_from type=select parent=dates_right
				@caption Kellaaeg alates

				@property s_hr_to type=select parent=dates_right
				@caption Kellaaeg kuni

		@property s_wd type=chooser multiple=1 parent=dates
		@caption N&auml;dalap&auml;ev

		@property s_period type=chooser parent=dates
		@caption Periood
		@comment Kui periood valitud siis Alates ja Kuni v&auml;lju ei arvestata

	@layout us type=vbox area_caption=Kasutajad closeable=1
		@layout us_top type=hbox parent=us width=50%:50%
			@layout us_left type=vbox parent=us_top
				@property s_username type=table parent=us_left no_caption=1
				@caption Kasutajanimi

				@property s_username_except type=checkbox ch_value=1 parent=us_left no_caption=1
				@caption Kasutajanimi v&auml;ljaarvatud

			@layout us_right type=vbox parent=us_top

				@property s_user_group type=table parent=us_right no_caption=1
				@caption Kasutajagrupp

				@property s_user_group_except type=checkbox ch_value=1  parent=us_right no_caption=1
				@caption Kasutajagrupp v&auml;ljaarvatud

		@property vis1 type=text parent=us store=no no_caption=1

	@layout obj type=vbox area_caption=Objektid closeable=1
		@layout obj_top type=hbox parent=obj width=50%:50%
			@layout obj_left type=vbox parent=obj_top
				@property s_root_o type=table parent=obj_left no_caption=1
				@caption Juurobjekt

			@layout obj_right type=vbox parent=obj_top
				@property s_clid type=select multiple=1 size=5 parent=obj_right captionside=top
				@caption Objektit&uuml;&uuml;p

		@property vis2 type=text parent=obj store=no no_caption=1


	@layout act type=hbox width=50%:50%

		@layout act_left type=vbox closeable=1 area_caption=Tegevus parent=act

			@property s_action type=select multiple=1 parent=act_left size=5
			@caption Tegevus

			@property s_action_except type=checkbox ch_value=1 parent=act_left no_caption=1
			@caption Tegevus v&auml;ljaarvatud

		@layout act_right type=vbox closeable=1 area_caption=Maa parent=act

			@property s_country type=select multiple=1 size=5 parent=act_right
			@caption Asukohamaa

			@property s_country_except type=checkbox ch_value=1 parent=act_right no_caption=1
			@caption Asukohamaa v&auml;ljaarvatud

	@layout s type=hbox width=50%:50%

		@layout s_left type=vbox closeable=1 area_caption=Sessioon&nbsp;&&nbsp;IP parent=s

			@property s_sessid type=textbox parent=s_left
			@caption Sessiooni ID

			@property s_ip type=textbox  parent=s_left
			@caption IP Aadress

			@property s_ip_except type=checkbox ch_value=1  parent=s_left no_caption=1
			@caption IP Aadress v&auml;ljaarvatud

			@property s_referer type=textbox parent=s_left
			@caption Referer

		@layout s_right type=vbox closeable=1 area_caption=Tulemused parent=s

			@property s_res_type type=select parent=s_right
			@caption Tulemused kuva

			@property s_add_res_type type=chooser multiple=1 parent=s_right
			@caption Lisaks kuva

			@property s_limit type=select parent=s_right
			@caption Mitu rida

@default group=stats_disp

	@property s_res_tb type=toolbar store=no no_caption=1
	@property s_res type=table store=no no_caption=1
	@property s_res_add type=table store=no no_caption=1
	@property s_res_add_2 type=table store=no no_caption=1

@default group=stats_eex

	@property eex_from type=date_select
	@caption Alates

	@property eex_to type=date_select
	@caption Kuni

	@property stats_eex_inf type=text store=no
	@caption Unikaalseid k&uuml;lastusi

	@property stats_eex_entry type=table store=no no_caption=1
	@property stats_eex_exit type=table store=no no_caption=1

@default group=browser

		@property browser_tb type=toolbar no_caption=1 store=no

		@layout browser_split type=hbox width="20%:80%"

			@layout browser_tree parent=browser_split type=hbox closeable=1 area_caption=Kuup&auml;evad

			@property browser_tree type=treeview no_caption=1 store=no parent=browser_split parent=browser_tree

			@property browser_tbl type=table no_caption=1 store=no  parent=browser_split


@groupinfo data caption="Andmed" submit=no
@groupinfo stats caption="Statistika"
	@groupinfo stats_filter caption="Koosta filter" parent=stats
	@groupinfo stats_disp caption="Tulemused" parent=stats submit=no
	@groupinfo stats_eex caption="Entry & Exit pages" parent=stats request_method=get

@groupinfo browser caption="Salvestatud p&auml;ringud" submit=no

@reltype OBJ value=1 clid=CL_MENU
@caption Objekt

@reltype USER value=2 clid=CL_USER
@caption Kasutaja

@reltype GROUP value=3 clid=CL_GROUP
@caption Kasutajagrupp

@reltype HTML value=4 clid=CL_FILE
@caption Aruanne

*/

class stats_viewer extends class_base
{
	const AW_CLID = 1145;

	private $stat_sel_flt; // if user has selected a filter, this contains its id string
	private $saved_filters = array(); // if user has selected or is adding a filter, this contains all saved filters. Format is array(filter1_id => (array) data, ...)
	private $stats_disp_filter_parameters; // used by stats display views to create human readable filter descriptions for table headers.

	private $weekday_options = array(); // weekdays
	private $period_options = array(); // relative time periods for stats

	const PERIOD_CUR_MON = 1;
	const PERIOD_PREV_MON = 2;
	const PERIOD_PREV_MON_TO_NOW = 3;
	const PERIOD_CUR_WEEK = 4;
	const PERIOD_PREV_WEEK = 5;
	const PERIOD_TODAY = 6;
	const PERIOD_YESTERDAY = 7;

	function stats_viewer()
	{
		$this->init(array(
			"tpldir" => "applications/stats/stats_viewer",
			"clid" => CL_STATS_VIEWER
		));
		$this->m = get_instance("applications/stats/stats_model");

		$this->res_types = array(
			"det" => t("Detailselt"),
			"day" => t("P&auml;evade l&otilde;ikes"),
			"wd" => t("N&auml;dalap&auml;evade l&otilde;ikes"),
			"mon" => t("Kuude l&otilde;ikes"),
			"tm" => t("Kellaaja j&auml;rgi"),
			"obj" => t("Objektide l&otilde;ikes"),
			"uid" => t("Kasutajate l&otilde;ikes"),
			"grp" => t("Kasutajagruppide l&otilde;ikes"),
			"act" => t("Tegevuste l&otilde;ikes"),
			"obt" => t("Objektit&uuml;&uuml;pide l&otilde;ikes"),
			"ipa" => t("IP aadressite l&otilde;ikes"),
			"ctr" => t("Asukohamaade l&otilde;ikes")
		);

		$this->period_options = array(
			0 => t("M&auml;&auml;ramata"),
			self::PERIOD_CUR_MON => t("Jooksev kuu"),
			self::PERIOD_PREV_MON => t("Eelmine kuu"),
			self::PERIOD_PREV_MON_TO_NOW => t("Eelmise kuu algusest t&auml;naseni"),
			self::PERIOD_CUR_WEEK => t("Jooksev n&auml;dal"),
			self::PERIOD_PREV_WEEK => t("Eelmine n&auml;dal"),
			self::PERIOD_TODAY => t("T&auml;na"),
			self::PERIOD_YESTERDAY => t("Eile")
		);

		$this->weekday_options = array(
			2 => t("Esmasp&auml;ev"),
			3 => t("Teisip&auml;ev"),
			4 => t("Kolmap&auml;ev"),
			5 => t("Neljap&auml;ev"),
			6 => t("Reede"),
			7 => t("Laup&auml;ev"),
			1 => t("P&uuml;hap&auml;ev")
		);
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		if ($this->stat_sel_flt and isset($this->saved_filters[$this->stat_sel_flt][$prop["name"]]))
		{
			$prop["value"] = $this->saved_filters[$this->stat_sel_flt][$prop["name"]];
		}

		switch($prop["name"])
		{
			case "stats_eex_entry":
				$this->_stats_eex_entry($arr);
				break;

			case "stats_eex_exit":
				$this->_stats_eex_exit($arr);
				break;

			case "stats_eex_inf":
				$this->_stats_eex_inf($arr);
				break;

			case "s_tb":
				$this->_s_tb($arr);
				break;

			case "data_table":
				return $this->_data_table($arr);

			case "s_wd":
				$prop["options"] = $this->weekday_options;
				break;

			case "s_period":
				$prop["options"] = $this->period_options;
				break;

			case "s_hr_from":
			case "s_hr_to":
				$prop["options"] = array("" => t("--vali--")) + range(0,24);
				break;

			case "s_root_o":
				$this->_s_root_o($arr);
				break;

			case "s_username":
				$this->_s_username($arr);
				break;

			case "s_user_group":
				$this->_s_user_group($arr);
				break;

			case "s_action":
				$acts = aw_ini_get("syslog.actions");
				$opt = array();
				foreach($acts as $id => $dat)
				{
					$opt[$id] = $dat["name"];
				}
				$prop["options"] = $opt;
				break;

			case "s_clid":
				$prop["options"] = get_class_picker();
				break;

			case "s_res_type":
				$prop["options"] = $this->res_types;
				break;

			case "s_limit":
				$prop["options"] = array(
					100 => 100,
					250 => 250,
					500 => 500,
					1000 => 1000
				);
				break;

			case "s_country":
				$prop["options"] = $this->make_keys($this->m->get_country_list());
				break;

			case "s_res":
				$this->_s_res($arr);
				break;

			case "s_res_add":
				$s_add_res_type = $this->stat_sel_flt ? $this->saved_filters[$this->stat_sel_flt]["s_add_res_type"] : $arr["obj_inst"]->prop("s_add_res_type");

				if (!is_array($s_add_res_type) || count($s_add_res_type) < 1)
				{
					$retval = PROP_IGNORE;
				}
				else
				{
				$this->_s_res_add($arr);
				}
				break;

			case "s_res_add_2":
				$s_add_res_type = $this->stat_sel_flt ? $this->saved_filters[$this->stat_sel_flt]["s_add_res_type"] : $arr["obj_inst"]->prop("s_add_res_type");

				if (!is_array($s_add_res_type) || count($s_add_res_type) < 2)
				{
					$retval = PROP_IGNORE;
				}
				else
				{
				$this->_s_res_add_2($arr);
				}
				break;

			case "s_add_res_type":
				$prop["options"] = array(
					"ctr" => t("Top 30 asukohamaad"),
					"ipa" => t("Top 30 IP-d")
				);
				break;

			case "s_res_tb":
				$this->_s_res_tb($arr);
				break;

			case "browser_tb":
				$this->_browser_tb($arr);
				break;

			case "browser_tree":
				$this->_browser_tree($arr);
				break;

			case "browser_tbl":
				$this->_browser_tbl($arr);
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
			case "s_root_o":
				if ($this->stat_sel_flt)
				{  // save filter to selection
					$this->saved_filters[$this->stat_sel_flt]["subs"] = $arr["request"]["incl_subs"];
				}
				else
				{ // save default filter
				$arr["obj_inst"]->set_meta("subs", $arr["request"]["incl_subs"]);
				}

			case "s_user_group":
			case "s_username":
				$prop_name = $prop["name"] . "_use";

				if ($this->stat_sel_flt)
				{  // save filter to selection
					$this->saved_filters[$this->stat_sel_flt][$prop_name] = $arr["request"][$prop_name];
				}
				else
				{ // save default filter
					$arr["obj_inst"]->set_meta($prop_name, $arr["request"][$prop_name]);
				}
				break;

			case "saved_filter_name":
			case "s_date_from":
			case "s_date_to":
			case "s_hr_from":
			case "s_hr_to":
			case "s_wd":
			case "s_username":
			case "s_username_except":
			case "s_user_group":
			case "s_user_group_except":
			case "s_clid":
			case "s_action":
			case "s_action_except":
			case "s_country":
			case "s_country_except":
			case "s_sessid":
			case "s_ip":
			case "s_ip_except":
			case "s_referer":
			case "s_res_type":
			case "s_add_res_type":
			case "s_limit":
				if ($this->stat_sel_flt)
				{// save filter to selection
					$this->saved_filters[$this->stat_sel_flt][$prop["name"]] = $prop["value"];
					$retval = PROP_IGNORE;
				}
				break;
		}
		return $retval;
	}

	function callback_pre_save($arr)
	{
		if ($this->stat_sel_flt)
		{
			$arr["obj_inst"]->set_meta("saved_filters", $this->saved_filters);
		}
	}

	function callback_post_save($arr)
	{
		$pops = new popup_search();
		$pops->do_create_rels($arr["obj_inst"], $arr["request"]["obj_h"], "RELTYPE_OBJ");
		$pops->do_create_rels($arr["obj_inst"], $arr["request"]["user_h"], "RELTYPE_USER");
		$pops->do_create_rels($arr["obj_inst"], $arr["request"]["ugroup_h"], "RELTYPE_GROUP");
	}

	function callback_mod_reforb(&$arr)
	{
		$arr["post_ru"] = post_ru();
		$arr["obj_h"] = 0;
		$arr["user_h"] = 0;
		$arr["ugroup_h"] = 0;
	}

	function callback_mod_retval($arr)
	{
		if ($this->stat_sel_flt)
		{
			$arr["args"]["stat_sel_flt"] = $this->stat_sel_flt;
		}
	}

	function callback_on_load($arr)
	{
		// saved filters
		if (is_a($this->obj_inst, "object"))
		{
			// old objects upgrade
			if (!$this->obj_inst->meta("stats_viewer_version") && is_oid($this->obj_inst->id())) // version 1 has changes in filter parameter format for root obj, user and group
			{
				foreach($this->obj_inst->connections_from(array("type" => array("RELTYPE_OBJ", "RELTYPE_USER", "RELTYPE_GROUP"))) as $c)
				{
					switch ($c->prop("to.class_id"))
					{
						case CL_USER:
							$s_username_use[$c->prop("to")] = 1;
							break;

						case CL_GROUP:
							$s_user_group_use[$c->prop("to")] = 1;
							break;

						case CL_MENU:
							$s_root_o_use[$c->prop("to")] = 1;
							break;
					}
				}

				$this->obj_inst->set_meta("s_root_o_use", $s_root_o_use);
				$this->obj_inst->set_meta("s_username_use", $s_username_use);
				$this->obj_inst->set_meta("s_user_group_use", $s_user_group_use);
				$this->obj_inst->set_meta("stats_viewer_version", 1);
				$this->obj_inst->save();
			}

			if ("stats_filter" === $arr["request"]["group"] or "stats_disp" === $arr["request"]["group"] or "stats" === $arr["request"]["group"])
			{
				// for GET request load filters always (for selectors)
				$this->saved_filters = safe_array($this->obj_inst->meta("saved_filters"));

				if (!empty($arr["request"]["stat_sel_flt"]) and isset($this->saved_filters[$arr["request"]["stat_sel_flt"]]))
				{ // set selected
					$this->stat_sel_flt = $arr["request"]["stat_sel_flt"];
				}
			}
		}
		elseif (!empty($arr["request"]["saved_filter_name"]) and is_oid($arr["request"]["id"]))
		{ // for POST request
			$o = new object($arr["request"]["id"]);
			$saved_filters = safe_array($o->meta("saved_filters"));

			// search for existing filter by user submitted name
			foreach ($saved_filters as $id => $data)
			{
				if ($data["saved_filter_name"] === $arr["request"]["saved_filter_name"])
				{
					$this->stat_sel_flt = $id;
					$this->saved_filters = $saved_filters;
					break;
				}
			}

			if (!$this->stat_sel_flt)
			{ // name not found, saving a new filter
				// new filter id
				$i = 1;

				while (isset($saved_filters[$i]))
				{
					$i++;
				}

				$this->stat_sel_flt = $i;
				$this->saved_filters = $saved_filters;
			}
		}
	}

	/** this will get called whenever this object needs to get shown in the website, via alias in document **/
	function show($arr)
	{
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");
		$this->vars(array(
			"name" => $ob->prop("name"),
		));
		return $this->parse();
	}

	function _init_data_table(&$t)
	{
		$t->define_field(array(
			"name" => "timespan",
			"caption" => t("Ajavahemik"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "status",
			"caption" => t("Staatus"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "action",
			"caption" => t("&nbsp;"),
			"align" => "center",
		));
	}

	function _data_table($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_data_table($t);

		// fetch info on the archived periods
		$pers = $this->m->get_available_periods();
		foreach($pers as $per_id => $per_data)
		{
			$action = "";
			if ($per_data["status"] != "online")
			{
				$action = html::href(array(
					"url" => $this->mk_my_orb("bring_online", array("per_id" => $per_id, "return_url" => get_ru())),
					"caption" => t("Too on-line"),
				));
			}
			$t->define_data(array(
				"timespan" => date("d.m.Y", $per_data["from"])." - ".date("d.m.Y", $per_data["to"]),
				"status" => $per_data["status"] == "online" ? t("On-line") : t("Arhiveeritud"),
				"ts" => $per_id,
				"start" => $per_data["from"],
				"action" => $action
			));
		}
		$t->set_default_sortby("start");
		return PROP_OK;
	}

	/**
		@attrib name=bring_online
		@param per_id required
		@param return_url required
	**/
	function bring_online($arr)
	{
		$this->m->bring_period_online(array(
			"period_id" => $arr["per_id"]
		));
		return $arr["return_url"];
	}

	function _init_s_root_o(&$t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Objekti nimi"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "incl_subs",
			"caption" => t("Ka alamobjektid"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "use",
			"caption" => t("Kasuta filtris"),
			"align" => "center"
		));
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid"
		));
		$t->set_caption(t("Juurobjekt"));
	}

	function _s_root_o($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$o = $arr["obj_inst"];
		$this->_init_s_root_o($t);
		$incl_subs = $this->stat_sel_flt ? $this->saved_filters[$this->stat_sel_flt]["subs"] : $o->meta("subs");
		$s_root_o_use = $this->stat_sel_flt ? $this->saved_filters[$this->stat_sel_flt]["s_root_o_use"] : $o->meta("s_root_o_use");

		foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_OBJ")) as $c)
		{
			$t->define_data(array(
				"name" => html::obj_change_url($c->prop("to")),
				"use" => html::checkbox(array(
					"name" => "s_root_o_use[".$c->prop("to")."]",
					"value" => 1,
					"checked" => $s_root_o_use[$c->prop("to")]
				)),
				"incl_subs" => html::checkbox(array(
					"name" => "incl_subs[".$c->prop("to")."]",
					"value" => 1,
					"checked" => $incl_subs[$c->prop("to")]
				)),
				"oid" => $c->prop("to")
			));
		}
	}

	function _init_s_username_t(&$t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Objekti nimi"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "use",
			"caption" => t("Kasuta filtris"),
			"align" => "center"
		));
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid"
		));
	}

	function _s_username($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_s_username_t($t);
		$o = $arr["obj_inst"];
		$s_username_use = $this->stat_sel_flt ? $this->saved_filters[$this->stat_sel_flt]["s_username_use"] : $o->meta("s_username_use");

		foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_USER")) as $c)
		{
			$t->define_data(array(
				"name" => html::obj_change_url($c->prop("to")),
				"use" => html::checkbox(array(
					"name" => "s_username_use[".$c->prop("to")."]",
					"value" => 1,
					"checked" => $s_username_use[$c->prop("to")]
				)),
				"oid" => $c->prop("to")
			));
		}
		$t->set_caption(t("Kasutajad"));
	}

	function _init_s_user_group_t(&$t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Objekti nimi"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "use",
			"caption" => t("Kasuta filtris"),
			"align" => "center"
		));
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid"
		));
	}

	function _s_user_group($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_s_user_group_t($t);
		$o = $arr["obj_inst"];
		$s_user_group_use = $this->stat_sel_flt ? $this->saved_filters[$this->stat_sel_flt]["s_user_group_use"] : $o->meta("s_user_group_use");

		foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_GROUP")) as $c)
		{
			$t->define_data(array(
				"name" => html::obj_change_url($c->prop("to")),
				"use" => html::checkbox(array(
					"name" => "s_user_group_use[".$c->prop("to")."]",
					"value" => 1,
					"checked" => $s_user_group_use[$c->prop("to")]
				)),
				"oid" => $c->prop("to")
			));
		}
		$t->set_caption(t("Kasutajagrupp"));
	}

	function _s_tb($arr)
	{
		$tb =& $arr["prop"]["vcl_inst"];
		$tb->add_menu_button(array(
			"name" => "search",
			"tooltip" => t("Otsi"),
			"img" => "search.gif"
		));

		$url = $this->mk_my_orb("do_search", array("pn" => "obj_h", "clid" => array(
		)), "popup_search");
		$tb->add_menu_item(array(
			"parent" => "search",
			"text" => t("Juurobjekt"),
			"link" => "javascript:aw_popup_scroll('$url','".t("Otsi")."',550,500)",
			"multiple" => 1
		));
		$url = $this->mk_my_orb("do_search", array("pn" => "user_h", "clid" => CL_USER, "multiple" => 1), "popup_search");
		$tb->add_menu_item(array(
			"parent" => "search",
			"text" => t("Kasutaja"),
			"link" => "javascript:aw_popup_scroll('$url','".t("Otsi")."',550,500)",
		));
		$url = $this->mk_my_orb("do_search", array("pn" => "ugroup_h", "clid" => CL_GROUP,"multiple" => 1), "popup_search");
		$tb->add_menu_item(array(
			"parent" => "search",
			"text" => t("Kasutajagrupp"),
			"link" => "javascript:aw_popup_scroll('$url','".t("Otsi")."',550,500)",
		));

		if ($this->stat_sel_flt)
		{
		$tb->add_button(array(
			"name" => "delete",
				"tooltip" => t("Kustuta valitud filter"),
				"img" => "delete.gif",
				"action" => "delete_saved_filter"
			));
		}
		else
		{
			$tb->add_button(array(
				"name" => "delete",
				"tooltip" => t("Kustuta seosed"),
			"img" => "delete.gif",
			"action" => "del_rels"
		));
	}

		// saved filters
		$ss = array($this->mk_my_orb("change", array(
			"id" => $arr["obj_inst"]->id(),
			"group" => "stats_filter",
			"return_url" => $arr["request"]["return_url"])) => ""
		);
		$selected = null;

		foreach($this->saved_filters as $id => $data)
		{
			$opr = array();
			$opr["id"] = $arr["obj_inst"]->id();
			$opr["group"] = "stats_filter";
			$opr["stat_sel_flt"] = $id;
			$url = $this->mk_my_orb("change", $opr);
			$ss[$url] = $data["saved_filter_name"];

			if ($id == $this->stat_sel_flt)
			{
				$selected = $url;
			}
		}

		$html = html::select(array(
			"options" => $ss,
			"onchange" => "el=document.changeform.tb_stat_sel_flt;window.location=el.options[el.selectedIndex].value",
			"name" => "tb_stat_sel_flt",
			"selected" => $selected
		));
		$tb->add_cdata($html);
	}

	/**
		@attrib name=del_rels
	**/
	function del_rels($arr)
	{
		$o = obj($arr["id"]);

		if (is_array($arr["sel"]))
		{
			foreach($arr["sel"] as $item)
			{
				$o->disconnect(array("from" => $item));
			}
		}

		return $arr["post_ru"];
	}

	/**
		@attrib name=delete_saved_filter
		@param id required type=int acl=view
	**/
	function delete_saved_filter($arr)
	{
		$o = obj($arr["id"]);

		if (!empty($arr["saved_filter_name"]))
		{
			$saved_filters = safe_array($o->meta("saved_filters"));

			// search for filter by user submitted name
			foreach ($saved_filters as $id => $data)
			{
				if ($data["saved_filter_name"] === $arr["saved_filter_name"])
				{
					// delete filter
					unset($saved_filters[$id]);
					$o->set_meta("saved_filters", $saved_filters);
					$o->save();
					break;
				}
			}
		}

		return $arr["post_ru"];
	}

	function _s_res($arr)
	{
		$s_res_type = $this->stat_sel_flt ? $this->saved_filters[$this->stat_sel_flt]["s_res_type"] : $arr["obj_inst"]->prop("s_res_type");
		$fn = "_s_res_".($s_res_type ? $s_res_type : "det");
		$this->$fn($arr);

		// create desc from opts
		$arr["prop"]["vcl_inst"]->set_caption($this->res_types[($s_res_type ? $s_res_type : "det")]);
		$arr["prop"]["vcl_inst"]->set_header($this->get_filter_description($this->stats_disp_filter_parameters));

		if ($arr["request"]["get_csv_file"] == 1)
		{
			header("Content-type: text/csv");
			header("Content-disposition: inline; filename=stats.csv;");
			die($arr["prop"]["vcl_inst"]->get_csv_file(";"));
		}

		if ($arr["request"]["get_html_file"] == 1)
		{
			$GLOBALS["__aw_op_handler"] = array(&$this, "op_handler");
		}
	}

	private function get_filter_description ()
	{
		$r = $this->stats_disp_filter_parameters;
		$fd = array();

		// date
		 if (array_key_exists($r["s_period"], $this->period_options))
		 {
			 $fd[] = $this->period_options[$r["s_period"]];
		 }
		 else
		 {
			 $fd[] = t("Periood: ") . date("d.m.Y", $r["s_date_from"]) . "-" . date("d.m.Y", $r["s_date_from"]);
		 }

		 if ($r["s_hr_from"] and $r["s_hr_to"])
		 {
			 $fd[] = t("Kell: ") . $r["s_hr_from"] . "-" . $r["s_hr_to"];
		 }

		 if (!empty($r["wd"]))
		 {
			 $tmp = array_intersect_key($this->weekday_options, $r["wd"]);
			 $fd[] = t("P&auml;evad: ") . implode(", ", $tmp);
		 }

		// users
		if (!empty($r["s_uids"]))
		{
			$fd[] = ($r["s_username_except"] ? t("V&auml;lja arvatud kasutajad: ") : t("Kasutajad: ")) . implode(", ", $r["s_uids"]);
		}

		if (!empty($r["s_group"]))
		{
			$fd[] = ($r["s_user_group_except"] ? t("V&auml;lja arvatud kasutajagrupid: ") : t("Kasutajagrupid: ")) . implode(", ", $r["s_group"]);
		}

		// objects
		if (!empty($r["s_objs"]))
		{
			 $tmp = $r["s_objs"];
			 foreach ($tmp as $key => $oid)
			 {
				 $o = new object($oid);
				 $tmp[$key] = $o->name() . " [" . t("id: ") . $oid . "]";
			 }
			 $fd[] = t("Juurobjektid: ") . implode(", ", $tmp);
		}

		if (!empty($r["s_clid"]))
		{
			 $tmp = array_intersect_key(get_class_picker(), $r["s_clid"]);
			 $fd[] = t("Objekti&uuml;&uuml;bid: ") . implode(", ", $tmp);
		}

		// actions
		 if (!empty($r["s_action"]))
		 {
			$tmp = aw_ini_get("syslog.actions");
			foreach($tmp as $id => $dat)
			{
				$tmp[$id] = $dat["name"];
			}
			 $tmp = array_intersect_key($tmp, $r["s_action"]);
			 $fd[] = ($r["s_action_except"] ? t("V&auml;lja arvatud tegevused: ") : t("Tegevused: ")) . implode(", ", $tmp);
		 }

		// countries
		if (!empty($r["s_country"]))
		{
			$fd[] = ($r["s_country_except"] ? t("V&auml;lja arvatud maad: ") : t("Maad: ")) . implode(", ", $r["s_country"]);
		}

		// ip
		if (!empty($r["s_ip"]))
		{
			$fd[] = ($r["s_ip_except"] ? t("V&auml;lja arvatud IP aadress: ") : t("IP aadress: ")) . $r["s_ip"];
		}

		// session
		if (!empty($r["s_sessid"]))
		{
			$fd[] = t("Sessiooni ID: ") . $r["s_sessid"];
		}

		// referer
		if (!empty($r["s_referer"]))
		{
			$fd[] = t("Referer: ") . $r["s_referer"];
		}

		// limit
		if (!empty($r["s_limit"]))
		{
			$fd[] = sprintf(t("Limiit %s rida"), $r["s_limit"]);
		}

		return t("Valikud -- ") . implode("; ", $fd);
	}

	function op_handler($op)
	{
		$o = obj($_GET["id"]);
		$f = new file();
		$id = $f->create_file_from_string(array(
			"parent" => $o->id(),
			"content" => $op
		));
		$o->connect(array(
			"to" => $id,
			"type" => "RELTYPE_HTML"
		));
		echo $op;
	}

	function _s_res_add($arr)
	{
		$s_add_res_type = $this->stat_sel_flt ? $this->saved_filters[$this->stat_sel_flt]["s_add_res_type"] : $arr["obj_inst"]->prop("s_add_res_type");

		foreach(safe_array($s_add_res_type) as $tpid)
		{
			$fn = "_s_res_".$tpid;
			$limit = 30;

			if ($this->stat_sel_flt)
			{
				$this->saved_filters[$this->stat_sel_flt]["s_limit"] = $limit;
			}
			else
			{
				$arr["obj_inst"]->set_prop("s_limit", $limit);
			}

			$this->$fn($arr);
			$arr["prop"]["vcl_inst"]->set_caption($this->res_types[$tpid]);
			return;
		}
	}

	function _s_res_add_2($arr)
	{
		$s_add_res_type = $this->stat_sel_flt ? $this->saved_filters[$this->stat_sel_flt]["s_add_res_type"] : $arr["obj_inst"]->prop("s_add_res_type");
		$f = true;

		foreach(safe_array($s_add_res_type) as $tpid)
		{
			if ($f)
			{
				$f = false;
				continue;
			}

			$fn = "_s_res_".$tpid;
			$limit = 30;

			if ($this->stat_sel_flt)
			{
				$this->saved_filters[$this->stat_sel_flt]["s_limit"] = $limit;
			}
			else
			{
				$arr["obj_inst"]->set_prop("s_limit", $limit);
			}

			$this->$fn($arr);
			$arr["prop"]["vcl_inst"]->set_caption($this->res_types[$tpid]);
			return;
		}
	}

	function _init_det_t(&$t)
	{
		$t->define_field(array(
			"name" => "obj",
			"caption" => t("Objekt"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "act",
			"caption" => t("Tegevus"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "tm",
			"caption" => t("Aeg"),
			"align" => "center",
			"type" => "time",
			"format" => "d.m.Y H:i:s",
			"numeric" => 1,
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "ip",
			"caption" => t("IP Aadress"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "uid",
			"caption" => t("Kasutaja"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "gid",
			"caption" => t("Kasutajagrupp"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "session",
			"caption" => t("Sessioon"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "ctry",
			"caption" => t("Riik"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "referer",
			"caption" => t("Referer"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "mail_id",
			"caption" => t("E-maili tuvastus"),
			"align" => "center"
		));
	}

	function _get_where_bit($r)
	{
		$sql = array();

		if ($r["s_period"])
		{
			switch ($r["s_period"])
			{
				case self::PERIOD_CUR_MON:
					$sql[] = " created_month = " . date("n");
					$sql[] = " created_year = " . date("Y");
					break;

				case self::PERIOD_PREV_MON:
					$month = date("n") - 1;
					$year = date("Y");
					if (0 === $month)
					{
						$month = 12;
						--$year;
					}
					$sql[] = " created_month = " . $month;
					$sql[] = " created_year = " . $year;
					break;

				case self::PERIOD_PREV_MON_TO_NOW:
					$time = mktime(0, 0, 0, date("n") - 1, 1, date("Y"));
					$sql[] = " tm >= ".$time;
					break;

				case self::PERIOD_CUR_WEEK:
					classload("core/date/date_calc");
					$time = get_week_start();
					$sql[] = " tm >= " . $time;
					break;

				case self::PERIOD_PREV_WEEK:
					classload("core/date/date_calc");
					$to = get_week_start();
					$wd = date("N");
					$wd = $wd > 3 ? 8 : 5;
					$from = get_week_start(time() - $wd * 86400);
					$sql[] = " tm >= ".$from;
					$sql[] = " tm < ".$to;
					break;

				case self::PERIOD_TODAY:
					$time = mktime(0, 0, 0, date("n"), date("j"), date("Y"));
					$sql[] = " tm >= ".$time;
					break;

				case self::PERIOD_YESTERDAY:
					$from = mktime(0, 0, 0, date("n"), date("j") - 1, date("Y"));
					$to = mktime(0, 0, 0, date("n"), date("j"), date("Y"));
					$sql[] = " tm >= ".$from;
					$sql[] = " tm < ".$to;
					break;
			}
		}
		else
		{
			if ($r["s_date_from"] != -1)
			{
				$sql[] = " tm >= ".$r["s_date_from"];
			}
			if ($r["s_date_to"] != -1)
			{
				$sql[] = " tm <= ".$r["s_date_to"];
			}
		}

		if ($r["s_hr_from"] > 0)
		{
			$sql[] = " created_hour >= ".$r["s_hr_from"];
		}
		if ($r["s_hr_to"] > 0)
		{
			$sql[] = " created_hour <= ".$r["s_hr_to"];
		}

		if (count($r["s_action"]))
		{
			if ($r["s_action_except"] == 1)
			{
				$awa = new aw_array($r["s_action"]);
				$sql[] = " s.act_id NOT IN (".$awa->to_sql().") ";
			}
			else
			{
				$awa = new aw_array($r["s_action"]);
				$sql[] = " s.act_id IN (".$awa->to_sql().") ";
			}
		}
		if (count($r["s_clid"]))
		{
			$awa = new aw_array($r["s_clid"]);
			$sql[] = " o.class_id IN (".$awa->to_sql().") ";
		}

		if (is_array($r["s_wd"]) && count($r["s_wd"]))
		{
			$awa = new aw_array($r["s_wd"]);
			$sql[] = " created_wd IN (".$awa->to_sql().") ";
		}

		if ($r["s_sessid"] != "")
		{
			$sql[] = " s.session_id = '".$r["s_sessid"]."' ";
		}
		if ($r["s_ip"] != "")
		{
			if ($r["s_ip_except"])
			{
				$sql[] = " s.ip != '".$r["s_ip"]."' ";
			}
			else
			{
				$sql[] = " s.ip = '".$r["s_ip"]."' ";
			}
		}

		if ($r["s_referer"] != "")
		{
			$sql[] = " s.referer like '".$r["s_referer"]."' ";
		}

		if (count($r["s_objs"]))
		{
			$awa = new aw_array($r["s_objs"]);
			$sql[] = " s.oid IN (".$awa->to_sql().") ";
		}

		if (count($r["s_uids"]))
		{
			if ($r["s_username_except"] == 1)
			{
				$awa = new aw_array($r["s_uids"]);
				$sql[] = " s.uid NOT IN (".$awa->to_sql().") ";
			}
			else
			{
				$awa = new aw_array($r["s_uids"]);
				$sql[] = " s.uid IN (".$awa->to_sql().") ";
			}
		}

		if (count($r["s_group"]))
		{
			if ($r["s_user_group_except"] == 1)
			{
				$awa = new aw_array($r["s_group"]);
				$sql[] = " s.g_oid NOT IN (".$awa->to_sql().") ";
			}
			else
			{
				$awa = new aw_array($r["s_group"]);
				$sql[] = " s.g_oid IN (".$awa->to_sql().") ";
			}
		}

		if (count($r["s_country"]) && !(count($r["s_country"]) == 1 && isset($r["s_country"][""])))
		{
			if ($r["s_country_except"] == 1)
			{
				$awa = new aw_array($r["s_country"]);
				$sql[] = " s.country NOT IN (".$awa->to_sql().") ";
			}
			else
			{
				$awa = new aw_array($r["s_country"]);
				$sql[] = " s.country IN (".$awa->to_sql().") ";
			}
		}

		$res =  join(" AND ", $sql);
		if ($res != "")
		{
			$res = " WHERE  ".$res;
		}
		return $res;
	}

	function get_s_params_from_obj($o)
	{
		$r = $o->properties();

		if ($this->stat_sel_flt)
		{
			$r = array_merge($r, $this->saved_filters[$this->stat_sel_flt]);
		}

		$s_root_o_use = $this->stat_sel_flt ? $this->saved_filters[$this->stat_sel_flt]["s_root_o_use"] : $o->meta("s_root_o_use");
		$objs = array();
		$subs = $this->stat_sel_flt ? $this->saved_filters[$this->stat_sel_flt]["subs"] : $o->meta("subs");
		$s_clid = $this->stat_sel_flt ? $this->saved_filters[$this->stat_sel_flt]["s_clid"] : $o->prop("s_clid");
		foreach($o->connections_from(array("type" => "RELTYPE_OBJ")) as $c)
		{
			$to_oid = $c->prop("to");

			if (isset($s_root_o_use[$to_oid]))
			{
				$objs[$to_oid] = $to_oid;
				if ($subs[$to_oid])
				{
					$clid = array(CL_MENU,CL_PROMO,CL_DOCUMENT,CL_EXTLINK);
						if (is_array($s_clid) && count($s_clid))
					{
						$clid = array(CL_MENU,CL_PROMO,CL_DOCUMENT,CL_EXTLINK);
							foreach($s_clid as $_cl)
						{
							$clid[] = $_cl;
						}
					}
					$ot = new object_tree(array(
						"parent" => $to_oid,
						"class_id" => $clid
					));
					foreach($ot->ids() as $id)
					{
						$objs[$id] = $id;
					}
				}
			}
		}
		$r["s_objs"] = $objs;

		$s_username_use = $this->stat_sel_flt ? $this->saved_filters[$this->stat_sel_flt]["s_username_use"] : $o->meta("s_username_use");
		$uids = array();
		foreach($o->connections_from(array("type" => "RELTYPE_USER")) as $c)
		{
			if (isset($s_username_use[$c->prop("to")]))
			{
				$t = $c->to();
				$uids[$t->prop("uid")] = $t->prop("uid");
			}
		}
		$r["s_uids"] = $uids;

		$s_user_group_use = $this->stat_sel_flt ? $this->saved_filters[$this->stat_sel_flt]["s_user_group_use"] : $o->meta("s_user_group_use");
		$gps = array();
		foreach($o->connections_from(array("type" => "RELTYPE_GROUP")) as $c)
		{
			if (isset($s_user_group_use[$c->prop("to")]))
			{
				$gps[$c->prop("to")] = $c->prop("to");
			}
		}
		$r["s_group"] = $gps;

		if (is_array($r["s_date_from"]))
		{
			$r["s_date_from"] = mktime(0, 0, 0, $r["s_date_from"]["month"], $r["s_date_from"]["day"], $r["s_date_from"]["year"]);
		}
		if (is_array($r["s_date_to"]))
		{
			$r["s_date_to"] = mktime(0, 0, 0, $r["s_date_to"]["month"], $r["s_date_to"]["day"], $r["s_date_to"]["year"]);
		}

		$this->stats_disp_filter_parameters = $r;
		return $r;
	}

	function _get_limit($r, $o)
	{
		$lim = $r["s_limit"];
		if ($lim == "")
		{
			$lim = 1000;
		}
		return $lim;
	}

	function _s_res_det($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_det_t($t);
		$r = $this->get_s_params_from_obj($arr["obj_inst"]);

		$g_ol = new object_list(array("class_id" => CL_GROUP, "site_id" => array(), "lang_id" => array()));
		$gs = $g_ol->names();

		$q = "
			SELECT
				s.*,
				o.*
			FROM
				syslog_archive s
				LEFT JOIN objects o ON o.oid = s.oid
			".$this->_get_where_bit($r, $arr["obj_inst"])."
			ORDER BY
				id DESC
			LIMIT
				".$this->_get_limit($r, $arr["obj_inst"])."
		";
		//echo "q = $q <br>";
		$acts = aw_ini_get("syslog.actions");
		$this->db_query($q);
		$base = $this->mk_my_orb("find_obj_redir", array("id" => 0));
		while ($row = $this->db_next())
		{
			$t->define_data(array(
				"obj" => html::href(array(
					"url" => $base.$row["oid"],
					"caption" => parse_obj_name($row["name"])
				)),
				"act" => $acts[$row["act_id"]]["name"],
				"tm" => $row["tm"],
				"ip" => $row["ip"]." / ".$row["ip_resolved"],
				"uid" => $row["uid"],
				"gid" => $gs[$row["g_oid"]],
				"session" => $row["session_id"],
				"ctry" => $row["country"],
				"referer" => $row["referer"] != "" ? html::href(array(
					"url" => $row["referer"],
					"caption" => t("Referer")
				)) : "",
				"mail_id" => $row["mail_id"]
			));
		}
		$t->set_default_sortby("tm");
		$t->set_default_sorder("desc");
		$t->sort_by();
		$t->set_sortable(false);
	}

	function _init_day_t(&$t)
	{
		$t->define_field(array(
			"name" => "day",
			"caption" => t("P&auml;ev"),
			"align" => "center",
			"sortable" => 1,
			"width" => "20%"
		));
		$t->define_field(array(
			"name" => "views",
			"caption" => t("Vaatamisi"),
			"align" => "center",
			"sortable" => 1,
			"numeric" => 1,
			"width" => "10%"
		));
		$t->define_field(array(
			"name" => "views_pct",
			"caption" => t("%"),
			"align" => "left",
			"sortable" => 1,
			"numeric" => 1,
			"width" => "30%"
		));
		$t->define_field(array(
			"name" => "visits",
			"caption" => t("K&uuml;lastusi"),
			"align" => "center",
			"sortable" => 1,
			"numeric" => 1,
			"width" => "10%"
		));
		$t->define_field(array(
			"name" => "visits_pct",
			"caption" => t("%"),
			"align" => "left",
			"sortable" => 1,
			"numeric" => 1,
			"width" => "30%"
		));
	}

	function _s_res_day($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_day_t($t);
		$r = $this->get_s_params_from_obj($arr["obj_inst"]);

		$q = "
			SELECT
				count(*) as total,
				count(distinct(session_id)) as sessions,
				created_year,
				created_month,
				created_day
			FROM
				syslog_archive s
				LEFT JOIN objects o ON o.oid = s.oid
			".$this->_get_where_bit($r, $arr["obj_inst"])."
			GROUP BY
				created_year,created_month,created_day
			LIMIT
				".$this->_get_limit($r, $arr["obj_inst"])."
		";
		//echo "q = $q <br>";
		$acts = aw_ini_get("syslog.actions");
		$this->db_query($q);
		$base = $this->mk_my_orb("find_obj_redir", array("id" => 0));
		$rows = array();
		$t_sum = 0;
		$s_sum = 0;
		while ($row = $this->db_next())
		{
			$rows[] = $row;
			$t_sum += $row["total"];
			$s_sum += $row["sessions"];
		}

		foreach($rows as $row)
		{
			$tm = mktime(0,0,0, $row["created_month"], $row["created_day"], $row["created_year"]);
			$v_pct = ($row["total"]*100.0)/$t_sum;
			$s_pct = ($row["sessions"]*100.0)/$s_sum;
			$t->define_data(array(
				"tm" => $tm,
				"day" => sprintf("%02d", $row["created_day"]).".".sprintf("%02d", $row["created_month"]).".".sprintf("%02d", $row["created_year"]),
				"views" => $row["total"],
				"visits" => $row["sessions"],
				"views_pct" => html::img(array(
					"url" => $this->cfg['baseurl'].'/automatweb/images/bar.gif',
					"height" => 5,
					"width" => $v_pct*2
				))." ".number_format($v_pct,2)." %",
				"visits_pct" => html::img(array(
					"url" => $this->cfg['baseurl'].'/automatweb/images/bar.gif',
					"height" => 5,
					"width" => $s_pct*2
				))." ".number_format($s_pct,2)." %",
			));
		}
		$t->set_default_sortby("tm");
		$t->set_default_sorder("desc");
		$t->sort_by();
		$t->set_sortable(false);
	}

	function _init_wd_t(&$t)
	{
		$t->define_field(array(
			"name" => "day",
			"caption" => t("N&auml;dalap&auml;ev"),
			"align" => "center",
			"sortable" => 1,
			"width" => "20%"
		));
		$t->define_field(array(
			"name" => "views",
			"caption" => t("Vaatamisi"),
			"align" => "center",
			"sortable" => 1,
			"numeric" => 1,
			"width" => "10%"
		));
		$t->define_field(array(
			"name" => "views_pct",
			"caption" => t("%"),
			"align" => "left",
			"sortable" => 1,
			"numeric" => 1,
			"width" => "30%"
		));
		$t->define_field(array(
			"name" => "visits",
			"caption" => t("K&uuml;lastusi"),
			"align" => "center",
			"sortable" => 1,
			"numeric" => 1,
			"width" => "10%"
		));
		$t->define_field(array(
			"name" => "visits_pct",
			"caption" => t("%"),
			"align" => "left",
			"sortable" => 1,
			"numeric" => 1,
			"width" => "30%"
		));
	}

	function _s_res_wd($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_wd_t($t);
		$r = $this->get_s_params_from_obj($arr["obj_inst"]);

		$q = "
			SELECT
				count(*) as total,
				count(distinct(session_id)) as sessions,
				created_wd
			FROM
				syslog_archive s
				LEFT JOIN objects o ON o.oid = s.oid
			".$this->_get_where_bit($r, $arr["obj_inst"])."
			GROUP BY
				created_wd
			LIMIT
				".$this->_get_limit($r, $arr["obj_inst"])."
		";
		//echo "q = $q <br>";
		//die("<pre>".$q);
		$acts = aw_ini_get("syslog.actions");
		$this->db_query($q);
		$rows = array();
		$t_sum = 0;
		$s_sum = 0;
		while ($row = $this->db_next())
		{
			$rows[] = $row;
			$t_sum += $row["total"];
			$s_sum += $row["sessions"];
		}

		foreach($rows as $row)
		{
			$tm = $row["created_wd"];
			$tm = $tm == 1 ? 7 : $tm-1;
			$v_pct = ($row["total"]*100.0)/$t_sum;
			$s_pct = ($row["sessions"]*100.0)/$s_sum;
			$t->define_data(array(
				"tm" => $tm,
				"day" => aw_locale::get_lc_weekday($tm),
				"views" => $row["total"],
				"visits" => $row["sessions"],
				"views_pct" => html::img(array(
					"url" => $this->cfg['baseurl'].'/automatweb/images/bar.gif',
					"height" => 5,
					"width" => $v_pct*2
				))." ".number_format($v_pct,2)." %",
				"visits_pct" => html::img(array(
					"url" => $this->cfg['baseurl'].'/automatweb/images/bar.gif',
					"height" => 5,
					"width" => $s_pct*2
				))." ".number_format($s_pct,2)." %",
			));
		}
		$t->set_default_sortby("tm");
		$t->set_default_sorder("asc");
		$t->sort_by();
		$t->set_sortable(false);
	}

	function _init_mon_t(&$t)
	{
		$t->define_field(array(
			"name" => "day",
			"caption" => t("Kuu"),
			"align" => "center",
			"sortable" => 1,
			"width" => "20%"
		));
		$t->define_field(array(
			"name" => "views",
			"caption" => t("Vaatamisi"),
			"align" => "center",
			"sortable" => 1,
			"numeric" => 1,
			"width" => "10%"
		));
		$t->define_field(array(
			"name" => "views_pct",
			"caption" => t("%"),
			"align" => "left",
			"sortable" => 1,
			"numeric" => 1,
			"width" => "30%"
		));
		$t->define_field(array(
			"name" => "visits",
			"caption" => t("K&uuml;lastusi"),
			"align" => "center",
			"sortable" => 1,
			"numeric" => 1,
			"width" => "10%"
		));
		$t->define_field(array(
			"name" => "visits_pct",
			"caption" => t("%"),
			"align" => "left",
			"sortable" => 1,
			"numeric" => 1,
			"width" => "30%"
		));
	}

	function _s_res_mon($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_mon_t($t);
		$r = $this->get_s_params_from_obj($arr["obj_inst"]);

		$q = "
			SELECT
				count(*) as total,
				count(distinct(session_id)) as sessions,
				created_month
			FROM
				syslog_archive s
				LEFT JOIN objects o ON o.oid = s.oid
			".$this->_get_where_bit($r, $arr["obj_inst"])."
			GROUP BY
				created_month
			LIMIT
				".$this->_get_limit($r, $arr["obj_inst"])."
		";
		//echo "q = $q <br>";
		$this->db_query($q);
		$rows = array();
		$t_sum = 0;
		$s_sum = 0;
		while ($row = $this->db_next())
		{
			$rows[] = $row;
			$t_sum += $row["total"];
			$s_sum += $row["sessions"];
		}

		foreach($rows as $row)
		{
			$tm = $row["created_month"];
			$v_pct = ($row["total"]*100.0)/$t_sum;
			$s_pct = ($row["sessions"]*100.0)/$s_sum;
			$t->define_data(array(
				"tm" => $tm,
				"day" => aw_locale::get_lc_month($tm),
				"views" => $row["total"],
				"visits" => $row["sessions"],
				"views_pct" => html::img(array(
					"url" => $this->cfg['baseurl'].'/automatweb/images/bar.gif',
					"height" => 5,
					"width" => $v_pct*2
				))." ".number_format($v_pct,2)." %",
				"visits_pct" => html::img(array(
					"url" => $this->cfg['baseurl'].'/automatweb/images/bar.gif',
					"height" => 5,
					"width" => $s_pct*2
				))." ".number_format($s_pct,2)." %",
			));
		}
		$t->set_numeric_field("tm");
		$t->set_default_sortby("tm");
		$t->set_default_sorder("asc");
		$t->sort_by();
		$t->set_sortable(false);
	}

	function _init_tm_t(&$t)
	{
		$t->define_field(array(
			"name" => "day",
			"caption" => t("Kellaaeg"),
			"align" => "center",
			"sortable" => 1,
			"width" => "20%"
		));
		$t->define_field(array(
			"name" => "views",
			"caption" => t("Vaatamisi"),
			"align" => "center",
			"sortable" => 1,
			"numeric" => 1,
			"width" => "10%"
		));
		$t->define_field(array(
			"name" => "views_pct",
			"caption" => t("%"),
			"align" => "left",
			"sortable" => 1,
			"numeric" => 1,
			"width" => "30%"
		));
		$t->define_field(array(
			"name" => "visits",
			"caption" => t("K&uuml;lastusi"),
			"align" => "center",
			"sortable" => 1,
			"numeric" => 1,
			"width" => "10%"
		));
		$t->define_field(array(
			"name" => "visits_pct",
			"caption" => t("%"),
			"align" => "left",
			"sortable" => 1,
			"numeric" => 1,
			"width" => "30%"
		));
	}

	function _s_res_tm($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_tm_t($t);
		$r = $this->get_s_params_from_obj($arr["obj_inst"]);

		$q = "
			SELECT
				count(*) as total,
				count(distinct(session_id)) as sessions,
				created_hour
			FROM
				syslog_archive s
				LEFT JOIN objects o ON o.oid = s.oid
			".$this->_get_where_bit($r, $arr["obj_inst"])."
			GROUP BY
				created_hour
			LIMIT
				".$this->_get_limit($r, $arr["obj_inst"])."
		";
		//echo "q = $q <br>";
		$this->db_query($q);
		$rows = array();
		$t_sum = 0;
		$s_sum = 0;
		while ($row = $this->db_next())
		{
			$rows[] = $row;
			$t_sum += $row["total"];
			$s_sum += $row["sessions"];
		}

		foreach($rows as $row)
		{
			$tm = $row["created_hour"];
			$v_pct = ($row["total"]*100.0)/$t_sum;
			$s_pct = ($row["sessions"]*100.0)/$s_sum;
			$t->define_data(array(
				"tm" => $tm,
				"day" => sprintf("%02d", $tm).":00 - ".sprintf("%02d", $tm+1).":00",
				"views" => $row["total"],
				"visits" => $row["sessions"],
				"views_pct" => html::img(array(
					"url" => $this->cfg['baseurl'].'/automatweb/images/bar.gif',
					"height" => 5,
					"width" => $v_pct*2
				))." ".number_format($v_pct,2)." %",
				"visits_pct" => html::img(array(
					"url" => $this->cfg['baseurl'].'/automatweb/images/bar.gif',
					"height" => 5,
					"width" => $s_pct*2
				))." ".number_format($s_pct,2)." %",
			));
		}
		$t->set_numeric_field("tm");
		$t->set_default_sortby("tm");
		$t->set_default_sorder("asc");
		$t->sort_by();
		$t->set_sortable(false);
	}

	function _init_obj_t(&$t)
	{
		$t->define_field(array(
			"name" => "day",
			"caption" => t("Objekt"),
			"align" => "center",
			"sortable" => 1,
			"width" => "20%"
		));
		$t->define_field(array(
			"name" => "views",
			"caption" => t("Vaatamisi"),
			"align" => "center",
			"sortable" => 1,
			"numeric" => 1,
			"width" => "10%"
		));
		$t->define_field(array(
			"name" => "views_pct",
			"caption" => t("%"),
			"align" => "left",
			"sortable" => 1,
			"numeric" => 1,
			"width" => "30%"
		));
		$t->define_field(array(
			"name" => "visits",
			"caption" => t("K&uuml;lastusi"),
			"align" => "center",
			"sortable" => 1,
			"numeric" => 1,
			"width" => "10%"
		));
		$t->define_field(array(
			"name" => "visits_pct",
			"caption" => t("%"),
			"align" => "left",
			"sortable" => 1,
			"numeric" => 1,
			"width" => "30%"
		));
	}

	function _s_res_obj($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_obj_t($t);
		$r = $this->get_s_params_from_obj($arr["obj_inst"]);

		$q = "
			SELECT
				count(*) as total,
				count(distinct(session_id)) as sessions,
				s.oid,
				o.name as name
			FROM
				syslog_archive s
				LEFT JOIN objects o ON o.oid = s.oid
			".$this->_get_where_bit($r, $arr["obj_inst"])."
			GROUP BY
				s.oid
			LIMIT
				".$this->_get_limit($r, $arr["obj_inst"])."
		";
		//echo "q = $q <br>";
		$this->db_query($q);
		$rows = array();
		$t_sum = 0;
		$s_sum = 0;
		while ($row = $this->db_next())
		{
			$rows[] = $row;
			$t_sum += $row["total"];
			$s_sum += $row["sessions"];
		}

		$base = $this->mk_my_orb("find_obj_redir", array("id" => 0));
		foreach($rows as $row)
		{
			$tm = $row["oid"];
			$v_pct = ($row["total"]*100.0)/$t_sum;
			$s_pct = ($row["sessions"]*100.0)/$s_sum;
			$t->define_data(array(
				"tm" => $tm,
				"day" => html::href(array(
					"url" => $base.$row["oid"],
					"caption" => parse_obj_name($row["name"])
				)),
				"views" => $row["total"],
				"visits" => $row["sessions"],
				"views_pct" => html::img(array(
					"url" => $this->cfg['baseurl'].'/automatweb/images/bar.gif',
					"height" => 5,
					"width" => $v_pct*2
				))." ".number_format($v_pct,2)." %",
				"visits_pct" => html::img(array(
					"url" => $this->cfg['baseurl'].'/automatweb/images/bar.gif',
					"height" => 5,
					"width" => $s_pct*2
				))." ".number_format($s_pct,2)." %",
			));
		}
		$t->set_numeric_field("views");
		$t->set_default_sortby("views");
		$t->set_default_sorder("desc");
		$t->sort_by();
		$t->set_sortable(false);
	}

	function _init_uid_t(&$t)
	{
		$t->define_field(array(
			"name" => "day",
			"caption" => t("Kasutaja"),
			"align" => "center",
			"sortable" => 1,
			"width" => "20%"
		));
		$t->define_field(array(
			"name" => "views",
			"caption" => t("Vaatamisi"),
			"align" => "center",
			"sortable" => 1,
			"numeric" => 1,
			"width" => "10%"
		));
		$t->define_field(array(
			"name" => "views_pct",
			"caption" => t("%"),
			"align" => "left",
			"sortable" => 1,
			"numeric" => 1,
			"width" => "30%"
		));
		$t->define_field(array(
			"name" => "visits",
			"caption" => t("K&uuml;lastusi"),
			"align" => "center",
			"sortable" => 1,
			"numeric" => 1,
			"width" => "10%"
		));
		$t->define_field(array(
			"name" => "visits_pct",
			"caption" => t("%"),
			"align" => "left",
			"sortable" => 1,
			"numeric" => 1,
			"width" => "30%"
		));
	}

	function _s_res_uid($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_uid_t($t);
		$r = $this->get_s_params_from_obj($arr["obj_inst"]);

		$q = "
			SELECT
				count(*) as total,
				count(distinct(session_id)) as sessions,
				s.uid
			FROM
				syslog_archive s
				LEFT JOIN objects o ON o.oid = s.oid
			".$this->_get_where_bit($r, $arr["obj_inst"])."
			GROUP BY
				s.uid
			LIMIT
				".$this->_get_limit($r, $arr["obj_inst"])."
		";
		//echo "q = $q <br>";
		$this->db_query($q);
		$rows = array();
		$t_sum = 0;
		$s_sum = 0;
		while ($row = $this->db_next())
		{
			$rows[] = $row;
			$t_sum += $row["total"];
			$s_sum += $row["sessions"];
		}

		$base = $this->mk_my_orb("find_obj_redir", array("id" => 0));
		foreach($rows as $row)
		{
			$tm = $row["uid"];
			$v_pct = ($row["total"]*100.0)/$t_sum;
			$s_pct = ($row["sessions"]*100.0)/$s_sum;
			$t->define_data(array(
				"tm" => $tm,
				"day" => $row["uid"],
				"views" => $row["total"],
				"visits" => $row["sessions"],
				"views_pct" => html::img(array(
					"url" => $this->cfg['baseurl'].'/automatweb/images/bar.gif',
					"height" => 5,
					"width" => $v_pct*2
				))." ".number_format($v_pct,2)." %",
				"visits_pct" => html::img(array(
					"url" => $this->cfg['baseurl'].'/automatweb/images/bar.gif',
					"height" => 5,
					"width" => $s_pct*2
				))." ".number_format($s_pct,2)." %",
			));
		}
		$t->set_numeric_field("views");
		$t->set_default_sortby("views");
		$t->set_default_sorder("desc");
		$t->sort_by();
		$t->set_sortable(false);
	}

	function _init_grp_t(&$t)
	{
		$t->define_field(array(
			"name" => "day",
			"caption" => t("Grupp"),
			"align" => "center",
			"sortable" => 1,
			"width" => "20%"
		));
		$t->define_field(array(
			"name" => "views",
			"caption" => t("Vaatamisi"),
			"align" => "center",
			"sortable" => 1,
			"numeric" => 1,
			"width" => "10%"
		));
		$t->define_field(array(
			"name" => "views_pct",
			"caption" => t("%"),
			"align" => "left",
			"sortable" => 1,
			"numeric" => 1,
			"width" => "30%"
		));
		$t->define_field(array(
			"name" => "visits",
			"caption" => t("K&uuml;lastusi"),
			"align" => "center",
			"sortable" => 1,
			"numeric" => 1,
			"width" => "10%"
		));
		$t->define_field(array(
			"name" => "visits_pct",
			"caption" => t("%"),
			"align" => "left",
			"sortable" => 1,
			"numeric" => 1,
			"width" => "30%"
		));
	}

	function _s_res_grp($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_grp_t($t);
		$r = $this->get_s_params_from_obj($arr["obj_inst"]);

		$q = "
			SELECT
				count(*) as total,
				count(distinct(session_id)) as sessions,
				s.g_oid as g_oid
			FROM
				syslog_archive s
				LEFT JOIN objects o ON o.oid = s.oid
			".$this->_get_where_bit($r, $arr["obj_inst"])."
			GROUP BY
				s.g_oid
			LIMIT
				".$this->_get_limit($r, $arr["obj_inst"])."
		";
		//echo "q = $q <br>";
		$this->db_query($q);
		$g_ol = new object_list(array("class_id" => CL_GROUP, "site_id" => array(), "lang_id" => array()));
		$gs = $g_ol->names();

		$rows = array();
		$t_sum = 0;
		$s_sum = 0;
		while ($row = $this->db_next())
		{
			$rows[] = $row;
			$t_sum += $row["total"];
			$s_sum += $row["sessions"];
		}

		$base = $this->mk_my_orb("find_obj_redir", array("id" => 0));
		foreach($rows as $row)
		{
			$tm = $row["g_oid"];
			$v_pct = ($row["total"]*100.0)/$t_sum;
			$s_pct = ($row["sessions"]*100.0)/$s_sum;
			$t->define_data(array(
				"tm" => $tm,
				"day" => $gs[$row["g_oid"]],
				"views" => $row["total"],
				"visits" => $row["sessions"],
				"views_pct" => html::img(array(
					"url" => $this->cfg['baseurl'].'/automatweb/images/bar.gif',
					"height" => 5,
					"width" => $v_pct*2
				))." ".number_format($v_pct,2)." %",
				"visits_pct" => html::img(array(
					"url" => $this->cfg['baseurl'].'/automatweb/images/bar.gif',
					"height" => 5,
					"width" => $s_pct*2
				))." ".number_format($s_pct,2)." %",
			));
		}
		$t->set_numeric_field("views");
		$t->set_default_sortby("views");
		$t->set_default_sorder("desc");
		$t->sort_by();
		$t->set_sortable(false);
	}

	function _init_act_t(&$t)
	{
		$t->define_field(array(
			"name" => "day",
			"caption" => t("Tegevus"),
			"align" => "center",
			"sortable" => 1,
			"width" => "20%"
		));
		$t->define_field(array(
			"name" => "views",
			"caption" => t("Vaatamisi"),
			"align" => "center",
			"sortable" => 1,
			"numeric" => 1,
			"width" => "10%"
		));
		$t->define_field(array(
			"name" => "views_pct",
			"caption" => t("%"),
			"align" => "left",
			"sortable" => 1,
			"numeric" => 1,
			"width" => "30%"
		));
		$t->define_field(array(
			"name" => "visits",
			"caption" => t("K&uuml;lastusi"),
			"align" => "center",
			"sortable" => 1,
			"numeric" => 1,
			"width" => "10%"
		));
		$t->define_field(array(
			"name" => "visits_pct",
			"caption" => t("%"),
			"align" => "left",
			"sortable" => 1,
			"numeric" => 1,
			"width" => "30%"
		));
	}

	function _s_res_act($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_act_t($t);
		$r = $this->get_s_params_from_obj($arr["obj_inst"]);

		$q = "
			SELECT
				count(*) as total,
				count(distinct(session_id)) as sessions,
				s.act_id as act_id
			FROM
				syslog_archive s
				LEFT JOIN objects o ON o.oid = s.oid
			".$this->_get_where_bit($r, $arr["obj_inst"])."
			GROUP BY
				s.act_id
			LIMIT
				".$this->_get_limit($r, $arr["obj_inst"])."
		";
		//echo "q = $q <br>";
		$this->db_query($q);

		$rows = array();
		$t_sum = 0;
		$s_sum = 0;
		while ($row = $this->db_next())
		{
			$rows[] = $row;
			$t_sum += $row["total"];
			$s_sum += $row["sessions"];
		}
		$acts = aw_ini_get("syslog.actions");
		$base = $this->mk_my_orb("find_obj_redir", array("id" => 0));
		foreach($rows as $row)
		{
			$tm = $row["act_id"];
			$v_pct = ($row["total"]*100.0)/$t_sum;
			$s_pct = ($row["sessions"]*100.0)/$s_sum;
			$t->define_data(array(
				"tm" => $tm,
				"day" => $acts[$row["act_id"]]["name"],
				"views" => $row["total"],
				"visits" => $row["sessions"],
				"views_pct" => html::img(array(
					"url" => $this->cfg['baseurl'].'/automatweb/images/bar.gif',
					"height" => 5,
					"width" => $v_pct*2
				))." ".number_format($v_pct,2)." %",
				"visits_pct" => html::img(array(
					"url" => $this->cfg['baseurl'].'/automatweb/images/bar.gif',
					"height" => 5,
					"width" => $s_pct*2
				))." ".number_format($s_pct,2)." %",
			));
		}
		$t->set_numeric_field("views");
		$t->set_default_sortby("views");
		$t->set_default_sorder("desc");
		$t->sort_by();
		$t->set_sortable(false);
	}

	function _init_obt_t(&$t)
	{
		$t->define_field(array(
			"name" => "day",
			"caption" => t("Objektit&uuml;&uuml;p"),
			"align" => "center",
			"sortable" => 1,
			"width" => "20%"
		));
		$t->define_field(array(
			"name" => "views",
			"caption" => t("Vaatamisi"),
			"align" => "center",
			"sortable" => 1,
			"numeric" => 1,
			"width" => "10%"
		));
		$t->define_field(array(
			"name" => "views_pct",
			"caption" => t("%"),
			"align" => "left",
			"sortable" => 1,
			"numeric" => 1,
			"width" => "30%"
		));
		$t->define_field(array(
			"name" => "visits",
			"caption" => t("K&uuml;lastusi"),
			"align" => "center",
			"sortable" => 1,
			"numeric" => 1,
			"width" => "10%"
		));
		$t->define_field(array(
			"name" => "visits_pct",
			"caption" => t("%"),
			"align" => "left",
			"sortable" => 1,
			"numeric" => 1,
			"width" => "30%"
		));
	}

	function _s_res_obt($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_obt_t($t);
		$r = $this->get_s_params_from_obj($arr["obj_inst"]);

		$q = "
			SELECT
				count(*) as total,
				count(distinct(session_id)) as sessions,
				o.class_id as class_id
			FROM
				syslog_archive s
				LEFT JOIN objects o ON o.oid = s.oid
			".$this->_get_where_bit($r, $arr["obj_inst"])."
			GROUP BY
				o.class_id
			LIMIT
				".$this->_get_limit($r, $arr["obj_inst"])."
		";
		//echo "q = $q <br>";
		$this->db_query($q);

		$rows = array();
		$t_sum = 0;
		$s_sum = 0;
		while ($row = $this->db_next())
		{
			$rows[] = $row;
			$t_sum += $row["total"];
			$s_sum += $row["sessions"];
		}
		$clss = aw_ini_get("classes");
		$base = $this->mk_my_orb("find_obj_redir", array("id" => 0));
		foreach($rows as $row)
		{
			$tm = $row["class_id"];
			$v_pct = ($row["total"]*100.0)/$t_sum;
			$s_pct = ($row["sessions"]*100.0)/$s_sum;
			$t->define_data(array(
				"tm" => $tm,
				"day" => $clss[$row["class_id"]]["name"],
				"views" => $row["total"],
				"visits" => $row["sessions"],
				"views_pct" => html::img(array(
					"url" => $this->cfg['baseurl'].'/automatweb/images/bar.gif',
					"height" => 5,
					"width" => $v_pct*2
				))." ".number_format($v_pct,2)." %",
				"visits_pct" => html::img(array(
					"url" => $this->cfg['baseurl'].'/automatweb/images/bar.gif',
					"height" => 5,
					"width" => $s_pct*2
				))." ".number_format($s_pct,2)." %",
			));
		}
		$t->set_numeric_field("views");
		$t->set_default_sortby("views");
		$t->set_default_sorder("desc");
		$t->sort_by();
		$t->set_sortable(false);
	}

	function _init_ipa_t(&$t)
	{
		$t->define_field(array(
			"name" => "day",
			"caption" => t("IP Aadress"),
			"align" => "center",
			"sortable" => 1,
			"width" => "20%"
		));
		$t->define_field(array(
			"name" => "views",
			"caption" => t("Vaatamisi"),
			"align" => "center",
			"sortable" => 1,
			"numeric" => 1,
			"width" => "10%"
		));
		$t->define_field(array(
			"name" => "views_pct",
			"caption" => t("%"),
			"align" => "left",
			"sortable" => 1,
			"numeric" => 1,
			"width" => "30%"
		));
		$t->define_field(array(
			"name" => "visits",
			"caption" => t("K&uuml;lastusi"),
			"align" => "center",
			"sortable" => 1,
			"numeric" => 1,
			"width" => "10%"
		));
		$t->define_field(array(
			"name" => "visits_pct",
			"caption" => t("%"),
			"align" => "left",
			"sortable" => 1,
			"numeric" => 1,
			"width" => "30%"
		));
	}

	function _s_res_ipa($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_ipa_t($t);
		$r = $this->get_s_params_from_obj($arr["obj_inst"]);

		$q = "
			SELECT
				count(*) as total,
				count(distinct(session_id)) as sessions,
				s.ip as ip,
				s.ip_resolved as ip_resolved
			FROM
				syslog_archive s
				LEFT JOIN objects o ON o.oid = s.oid
			".$this->_get_where_bit($r, $arr["obj_inst"])."
			GROUP BY
				s.ip
			LIMIT
				".$this->_get_limit($r, $arr["obj_inst"])."
		";
		//echo "q = $q <br>";
		$this->db_query($q);

		$rows = array();
		$t_sum = 0;
		$s_sum = 0;
		while ($row = $this->db_next())
		{
			$rows[] = $row;
			$t_sum += $row["total"];
			$s_sum += $row["sessions"];
		}
		$clss = aw_ini_get("classes");
		$base = $this->mk_my_orb("find_obj_redir", array("id" => 0));
		foreach($rows as $row)
		{
			$tm = $row["ip"];
			$v_pct = ($row["total"]*100.0)/$t_sum;
			$s_pct = ($row["sessions"]*100.0)/$s_sum;
			$t->define_data(array(
				"tm" => $tm,
				"day" => $row["ip"]." / ".$row["ip_resolved"],
				"views" => $row["total"],
				"visits" => $row["sessions"],
				"views_pct" => html::img(array(
					"url" => $this->cfg['baseurl'].'/automatweb/images/bar.gif',
					"height" => 5,
					"width" => $v_pct*2
				))." ".number_format($v_pct,2)." %",
				"visits_pct" => html::img(array(
					"url" => $this->cfg['baseurl'].'/automatweb/images/bar.gif',
					"height" => 5,
					"width" => $s_pct*2
				))." ".number_format($s_pct,2)." %",
			));
		}
		$t->set_numeric_field("views");
		$t->set_default_sortby("views");
		$t->set_default_sorder("desc");
		$t->sort_by();
		$t->set_sortable(false);
	}

	function _init_ctr_t(&$t)
	{
		$t->define_field(array(
			"name" => "day",
			"caption" => t("Maa"),
			"align" => "center",
			"sortable" => 1,
			"width" => "20%"
		));
		$t->define_field(array(
			"name" => "views",
			"caption" => t("Vaatamisi"),
			"align" => "center",
			"sortable" => 1,
			"numeric" => 1,
			"width" => "10%"
		));
		$t->define_field(array(
			"name" => "views_pct",
			"caption" => t("%"),
			"align" => "left",
			"sortable" => 1,
			"numeric" => 1,
			"width" => "30%"
		));
		$t->define_field(array(
			"name" => "visits",
			"caption" => t("K&uuml;lastusi"),
			"align" => "center",
			"sortable" => 1,
			"numeric" => 1,
			"width" => "10%"
		));
		$t->define_field(array(
			"name" => "visits_pct",
			"caption" => t("%"),
			"align" => "left",
			"sortable" => 1,
			"numeric" => 1,
			"width" => "30%"
		));
	}

	function _s_res_ctr($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_ctr_t($t);
		$r = $this->get_s_params_from_obj($arr["obj_inst"]);

		$q = "
			SELECT
				count(*) as total,
				count(distinct(session_id)) as sessions,
				s.country as country
			FROM
				syslog_archive s
				LEFT JOIN objects o ON o.oid = s.oid
			".$this->_get_where_bit($r, $arr["obj_inst"])."
			GROUP BY
				s.country
			LIMIT
				".$this->_get_limit($r, $arr["obj_inst"])."
		";
		//echo "q = $q <br>";
		$this->db_query($q);

		$rows = array();
		$t_sum = 0;
		$s_sum = 0;
		while ($row = $this->db_next())
		{
			$rows[] = $row;
			$t_sum += $row["total"];
			$s_sum += $row["sessions"];
		}
		$clss = aw_ini_get("classes");
		$base = $this->mk_my_orb("find_obj_redir", array("id" => 0));
		foreach($rows as $row)
		{
			$tm = $row["country"];
			$v_pct = ($row["total"]*100.0)/$t_sum;
			$s_pct = ($row["sessions"]*100.0)/$s_sum;
			$t->define_data(array(
				"tm" => $tm,
				"day" => $row["country"] == "" ? t("Tundmatu") : $row["country"],
				"views" => $row["total"],
				"visits" => $row["sessions"],
				"views_pct" => html::img(array(
					"url" => $this->cfg['baseurl'].'/automatweb/images/bar.gif',
					"height" => 5,
					"width" => $v_pct*2
				))." ".number_format($v_pct,2)." %",
				"visits_pct" => html::img(array(
					"url" => $this->cfg['baseurl'].'/automatweb/images/bar.gif',
					"height" => 5,
					"width" => $s_pct*2
				))." ".number_format($s_pct,2)." %",
			));
		}
		$t->set_numeric_field("views");
		$t->set_default_sortby("views");
		$t->set_default_sorder("desc");
		$t->sort_by();
		$t->set_sortable(false);
	}

	function _s_res_tb($arr)
	{
		$tb =& $arr["prop"]["vcl_inst"];
		$tb->add_button(array(
			"name" => "export_csv",
			"tooltip" => t("Ekspordi CSV fail"),
			"url" => aw_url_change_var("get_csv_file", 1)
		));
		$tb->add_button(array(
			"name" => "save_as_html",
			"tooltip" => t("Salvesta HTML"),
			"url" => aw_url_change_var("get_html_file", 1)
		));

		// saved filters
		$ss = array($this->mk_my_orb("change", array(
			"id" => $arr["obj_inst"]->id(),
			"group" => "stats_disp",
			"return_url" => $arr["request"]["return_url"])) => ""
		);
		$selected =  null;

		foreach($this->saved_filters as $id => $data)
		{
			$opr = array();
			$opr["id"] = $arr["obj_inst"]->id();
			$opr["group"] = "stats_disp";
			$opr["stat_sel_flt"] = $id;
			$url = $this->mk_my_orb("change", $opr);
			$ss[$url] = $data["saved_filter_name"];

			if ($id == $this->stat_sel_flt)
			{
				$selected = $url;
			}
		}

		$html = html::select(array(
			"options" => $ss,
			"onchange" => "el=document.changeform.tb_stat_sel_flt;window.location=el.options[el.selectedIndex].value",
			"name" => "tb_stat_sel_flt",
			"selected" => $selected
		));
		$tb->add_cdata($html);
	}

	/**
		@attrib name=find_obj_redir
		@param id required type=int acl=view
	**/
	function find_obj_redir($arr)
	{
		return html::get_change_url($arr["id"]);
	}

	function _init_stats_eex_t(&$t)
	{
		$t->define_field(array(
			"name" => "page",
			"caption" => t("Lehek&uuml;lg"),
			"align" => "center",
			"width" => "30%"
		));
		$t->define_field(array(
			"name" => "entries",
			"caption" => t("Sisenemisi"),
			"align" => "left"
		));
	}

	function _stats_eex_entry($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_stats_eex_t($t);

		$from = 0;
		$to = time();
		$where = array(" 1 = 1 ");
		if ($arr["obj_inst"]->prop("eex_from") != -1)
		{
			$from = $arr["obj_inst"]->prop("eex_from");
			if ($from)
			{
				$where[] = " tm_s >= ".$from." ";
			}
		}
		if ($arr["obj_inst"]->prop("eex_to") != -1)
		{
			$to = $arr["obj_inst"]->prop("eex_to");
			if ($to)
			{
				$where[] = " tm_s <= ".$to." ";
			}
		}
		$this->db_query("SELECT
				count(*) as cnt,
				entry_page
			FROM
				syslog_archive_sessions
			WHERE
				".join(" AND ", $where)."
			GROUP BY
				entry_page
			ORDER BY
				cnt desc
		");
		$rows = array();
		$sum = 0;
		while ($row = $this->db_next())
		{
			$rows[] = $row;
			$sum += $row["cnt"];
		}

		foreach($rows as $row)
		{
			$s_pct = ($row["cnt"]*100.0)/$sum;
			$t->define_data(array(
				"page" => html::href(array(
					"url" => aw_ini_get("baseurl")."/".$row["entry_page"],
					"caption" => aw_ini_get("baseurl")."/".$row["entry_page"]
				)),
				"entries" => html::img(array(
					"url" => $this->cfg['baseurl'].'/automatweb/images/bar.gif',
					"height" => 5,
					"width" => $s_pct*2
				))." ".number_format($s_pct,2)." %",
			));
		}

		$t->set_caption("Sisenemislehed");
		$t->set_numeric_field("entries");
		$t->set_default_sortby("entries");
		$t->set_default_sorder("desc");
		$t->sort_by();
		$t->set_sortable(false);
	}

	function _init_stats_eexx_t(&$t)
	{
		$t->define_field(array(
			"name" => "page",
			"caption" => t("Lehek&uuml;lg"),
			"align" => "center",
			"width" => "30%"
		));
		$t->define_field(array(
			"name" => "entries",
			"caption" => t("V&auml;ljumisi"),
			"align" => "left"
		));
	}

	function _stats_eex_exit($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_stats_eexx_t($t);

		$from = 0;
		$to = time();
		$where = array(" 1 = 1 ");
		if ($arr["obj_inst"]->prop("eex_from") != -1)
		{
			$from = $arr["obj_inst"]->prop("eex_from");
			if ($from)
			{
				$where[] = " tm_e >= ".$from." ";
			}
		}
		if ($arr["obj_inst"]->prop("eex_to") != -1)
		{
			$to = $arr["obj_inst"]->prop("eex_to");
			if ($to)
			{
				$where[] = " tm_e <= ".$to." ";
			}
		}
		$this->db_query("SELECT
				count(*) as cnt,
				exit_page
			FROM
				syslog_archive_sessions
			WHERE
				".join(" AND ", $where)."
			GROUP BY
				exit_page
			ORDER BY
				cnt desc
		");
		$rows = array();
		$sum = 0;
		while ($row = $this->db_next())
		{
			$rows[] = $row;
			$sum += $row["cnt"];
		}

		foreach($rows as $row)
		{
			$s_pct = ($row["cnt"]*100.0)/$sum;
			$t->define_data(array(
				"page" => html::href(array(
					"url" => aw_ini_get("baseurl")."/".$row["exit_page"],
					"caption" => aw_ini_get("baseurl")."/".$row["exit_page"]
				)),
				"entries" => html::img(array(
					"url" => $this->cfg['baseurl'].'/automatweb/images/bar.gif',
					"height" => 5,
					"width" => $s_pct*2
				))." ".number_format($s_pct,2)." %",
			));
		}

		$t->set_caption("V&auml;ljumislehed");
		$t->set_numeric_field("entries");
		$t->set_default_sortby("entries");
		$t->set_default_sorder("desc");
		$t->sort_by();
		$t->set_sortable(false);
	}

	function _stats_eex_inf($arr)
	{
		$from = 0;
		$to = time();
		$where = array(" 1 = 1 ");
		if ($arr["obj_inst"]->prop("eex_from") != -1)
		{
			$from = $arr["obj_inst"]->prop("eex_from");
			if ($from)
			{
				$where[] = " tm_e >= ".$from." ";
			}
		}
		if ($arr["obj_inst"]->prop("eex_to") != -1)
		{
			$to = $arr["obj_inst"]->prop("eex_to");
			if ($to)
			{
				$where[] = " tm_e <= ".$to." ";
			}
		}
		$where = join(" AND ", $where);
		$arr["prop"]["value"] = $this->db_fetch_field("SELECT
				count(*) as cnt
			FROM
				syslog_archive_sessions
			WHERE
				$where
		","cnt");
	}

	function _browser_tb($arr)
	{
		$tb =& $arr["prop"]["vcl_inst"];
		$tb->add_button(array(
			"name" => "delete",
			"tooltip" => t("Kustuta"),
			"img" => "delete.gif",
			"action" => "del_fs"
		));
	}

	function _browser_tree($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$dates = array();
		foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_HTML")) as $c)
		{
			$o = $c->to();
			$dates[date("Y", $o->created())][date("m", $o->created())][date("d", $o->created())] = $c->prop("to");
		}

		$t->start_tree(array(
			'type' => TREE_DHTML,
			'root_name' => 'some_tree',
			'tree_id' => 'stats_browser',
			'persist_state' => true,
		));

		foreach($dates as $y => $mons)
		{
			$t->add_item(0, array(
				"id" => "y_".$y,
				"name" => $y == $arr["request"]["year"] ? "<b>".$y."</b>" : $y,
				"url" => aw_url_change_var(array(
					"year" => $y,
					"month" => null,
					"day" => null
				))
			));
			foreach($mons as $m => $days)
			{
				$nm = aw_locale::get_lc_month($m);
				$t->add_item("y_".$y, array(
					"id" => "m_".$m,
					"name" => $m == $arr["request"]["month"] ? "<b>".$nm."</b>" : $nm,
					"url" => aw_url_change_var(array(
						"year" => $y,
						"month" => $m,
						"day" => null
					))
				));
				foreach($days as $d => $id)
				{
					$nm = sprintf("%02d", $d);
					$t->add_item("m_".$m, array(
						"id" => "d_".$d,
						"name" => $d == $arr["request"]["day"] ? "<b>".$nm."</b>" : $nm,
						"url" => aw_url_change_var(array(
							"year" => $y,
							"month" => $m,
							"day" => $d
						))
					));
				}
			}
		}
	}

	function _init_browser_tbl(&$t)
	{
		$t->define_field(array(
			"name" => "created",
			"caption" => t("Loodud"),
			"align" => "center",
			"type" => "time",
			"format" => "d.m.Y H:i",
			"numeric" => 1
		));
		$t->define_field(array(
			"name" => "createdby",
			"caption" => t("Looja"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "view",
			"caption" => t("Vaata"),
			"align" => "center",
		));
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid"
		));
	}

	function _browser_tbl($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_browser_tbl($t);

		$f = new file();
		foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_HTML")) as $c)
		{
			$o = $c->to();
			if ($arr["request"]["year"] && date("Y", $o->created()) != $arr["request"]["year"])
			{
				continue;
			}
			if ($arr["request"]["month"] && date("m", $o->created()) != $arr["request"]["month"])
			{
				continue;
			}
			if ($arr["request"]["day"] && date("d", $o->created()) != $arr["request"]["day"])
			{
				continue;
			}
			$t->define_data(array(
				"created" => $o->created(),
				"createdby" => $o->createdby(),
				"view" => html::href(array(
					"url" => $f->get_url($c->prop("to"), $c->prop("to.name")),
					"caption" => t("Vaata")
				)),
				"oid" => $o->id()
			));
		}
		$t->set_default_sortby("created");
	}

	/**
		@attrib name=del_fs
	**/
	function del_fs($arr)
	{
		object_list::iterate_list($arr["sel"], "delete");
		return $arr["post_ru"];
	}
}
?>
