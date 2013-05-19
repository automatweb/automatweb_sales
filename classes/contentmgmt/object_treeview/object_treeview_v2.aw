<?php

// object_treeview_v2.aw - Objektide nimekiri v2
/*

@classinfo relationmgr=yes no_status=1 no_comment=1

@default table=objects
@default group=general
@default field=meta
@default method=serialize

/// frontpage edit props

@property ds type=relpicker reltype=RELTYPE_DATASOURCE editonly=1
@caption Andmed

@property no_cache_page type=checkbox ch_value=1 editonly=1
@caption Lehte ei cacheta

@property search type=relpicker reltype=RELTYPE_SEARCH editonly=1
@caption Otsing

/// frontpage add props

@property add_new_ds type=text store=no subtitle=1 newonly=1
@caption Lisa uus andmeallikas

@property add_new_ds_type type=select newonly=1 store=no
@caption Uue andmeallika t&uuml;&uuml;p

@property copy_ds type=text store=no subtitle=1 newonly=1
@caption Kopeeri olemasolev andmeallikas

@property sel_copy_ds type=select newonly=1 store=no
@caption Vali kopeeritav andmeallikas

@property connect_ds type=text store=no subtitle=1 newonly=1
@caption Seos olemasoleva andmeallikaga

@property sel_connect_ds type=select newonly=1 store=no
@caption Vali seostatav andmeallikas


@property inherit_from type=text store=no subtitle=1 newonly=1
@caption P&auml;ri omadused objektinimekirjast

@property sel_inherit_from type=select newonly=1 store=no
@caption Vali p&auml;ritav objektinimekiri

@groupinfo showing caption="N&auml;itamine"
@default group=showing

@property inherit_view_props_from type=select
@caption P&auml;ri n&auml;itamise omadused objektist

@property show_folders type=checkbox ch_value=1
@caption N&auml;ita katalooge

@property show_add type=checkbox ch_value=1
@caption N&auml;ita t&ouml;&ouml;riistariba

@property show_search_btn type=checkbox ch_value=1
@caption N&auml;ita otsingulinki t&ouml;&ouml;riistaribal

@property show_link_new_win type=checkbox ch_value=1
@caption Vaata link uues aknas

@property show_link_field type=select
@caption Millises v&auml;ljas vaata linki n&auml;idata

@property url_field type=select
@caption Millises v&auml;ljas lingi url

@property hide_content_table_by_default type=checkbox ch_value=1
@caption Vaikimisi &auml;ra n&auml;ita sisu tabelit

@property show_empty_table_header type=checkbox ch_value=1
@caption N&auml;ita t&uuml;hja tabeli p&auml;ist

@property tree_type type=chooser default=TREE_DHTML
@caption Puu n&auml;itamise meetod

@property show_tpl type=chooser
@caption Layout

@property folders_table_column_count type=textbox size=5
@caption Mitu tulpa n&auml;idata kaustade tabelis

@property per_page type=textbox size=5
@caption Mitu rida lehel

@property show_hidden_cols type=checkbox ch_value=1  default=1
@caption N&auml;ita peidetud tulpasid?

@property sortbl type=table store=no
@caption Andmete sorteerimine

@property filter_table type=table store=no
@caption Andmete filtreerimine

@property group_by_folder type=select
@caption Kaustade j&auml;rgi grupeeritav v&auml;li

@property group_in_table type=select
@caption Millise v&auml;lja j&auml;rgi tabel grupeerida

@property filter_by_char_field type=select
@caption Millise v&auml;lja v&auml;&auml;rtuse esit&auml;he j&auml;rgi filtreeritakse

@property filter_by_char_order type=select
@caption Kuidas sorteerida

@property alphabet_in_lower_case type=checkbox ch_value=1
@caption T&auml;hestiku kuvamisel kasutada v&auml;iket&auml;hti

@property add_table_anchor_to_url type=checkbox ch_value=1
@caption Lisa #table URL-i l&otilde;ppu
@comment Lisab #table kataloogide URL-i l&otilde;ppu

@property sproc_params type=textbox
@caption Andmeallika parameetrid

@groupinfo styles caption="Stiilid"
@default group=styles
@property title_bgcolor type=colorpicker
@caption Pealkirja taustav&auml;rv

@property even_bgcolor type=colorpicker
@caption Paaris rea taustav&auml;rv

@property even_css_text type=textbox
@caption Paaris rea css

@property odd_bgcolor type=colorpicker
@caption Paaritu rea taustav&auml;rv

@property odd_css_text type=textbox
@caption Paaritu rea css

@property group_header_bgcolor type=colorpicker
@caption Grupeeriva rea taustav&auml;rv

@property table_css type=relpicker reltype=RELTYPE_CSS
@caption Tabeli stiil

@property header_css type=relpicker reltype=RELTYPE_CSS
@caption Pealkirja stiil

@property group_css type=relpicker reltype=RELTYPE_CSS
@caption Grupeeriva rea stiil

@groupinfo columns caption=Tulbad
@groupinfo columns_def caption="Tulpade defineerimine" parent=columns
@default group=columns_def
	@property columns type=table no_caption=1
	@caption Tulbad

@groupinfo columns_modify caption="Andmete muundamine" parent=columns
@default group=columns_modify
	@property columns_modify type=table no_caption=1
	@caption Tulbade andmete muundamine

@groupinfo inherit caption="P&auml;rimine"

	@property is_inheritable type=checkbox ch_value=1 field=meta method=serialize group=inherit
	@caption Kasutatav p&auml;rimiseks

@groupinfo search caption="Otsing" submit_method=get

	@property search_show type=callback callback=search_gen_els group=search
	@caption Tabeli v&auml;ljad

	@property search_res type=table store=no no_caption=1 group=search

@reltype DATASOURCE value=1 clid=CL_OTV_DS_OBJ,CL_OTV_DS_POSTIPOISS,CL_OTV_DS_ROADINFO,CL_DB_TABLE_CONTENTS,CL_SERVER_FOLDER,CL_FTP_LOGIN
@caption andmed

@reltype CSS value=2 clid=CL_CSS
@caption css stiil

@reltype SEARCH value=3 clid=CL_OBJECT_TREEVIEW_V2_SEARCH
@caption otsing

@reltype TRANSFORM value=4 clid=CL_OTV_DATA_FILTER
@caption andmete muundaja

@reltype VIEW_CONTROLLER value=5 clid=CL_CFG_VIEW_CONTROLLER
@caption Tulpade n&auml;itamise kontroller

@reltype ROW_CONTROLLER value=6 clid=CL_CFG_VIEW_CONTROLLER
@caption Rea kontroller
*/

class object_treeview_v2 extends class_base
{
	var $alphabet = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z", "&Otilde;", "&Auml;", "&Ouml;", "&Uuml;");

	function object_treeview_v2()
	{
		$this->all_cols = array(
			"icon" => t("Ikoon"),
			"name" => t("Nimi"),
			"size" => t("Suurus"),
			"class_id" => t("T&uuml;&uuml;p"),
			"modified" => t("Muutmise kuup&auml;ev"),
			"modifiedby" => t("Muutja"),
			"created" => t("Loomise kuup&auml;ev"),
			"createdby" => t("Looja"),
			"change" => t("Muuda"),
			"select" => t("Vali")
		);

		$this->tpls = array(
			"show" => t("default"),
			"show_search" => t("otsing ja puu vasakul")
		);

		$this->init(array(
			"tpldir" => "contentmgmt/object_treeview/object_treeview_v2",
			"clid" => CL_OBJECT_TREEVIEW_V2
		));

		$this->tr_i = get_instance(CL_OTV_DATA_FILTER);
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		$ob = $arr["obj_inst"];

		// $inherited gets set to true the first time if_check is done and the value is later
		// used to determine whether some properties will be shown or not
		static $inherited = false;
		static $ih_check_done = false;
		static $ih_ob;

		if (!$ih_check_done)
		{
			if (is_oid($ob->prop("inherit_view_props_from")) && $this->can("view", $ob->prop("inherit_view_props_from")))
			{
				$inherited = true;
				$ih_ob = obj($ob->prop("inherit_view_props_from"));
			}
			else
			{
				$ih_ob = $ob;
			};
			$ih_check_done = true;
		}

		static $col_list;

		if (!$col_list)
		{
			$col_list = $this->_get_col_list(array(
				"o" => $ih_ob,
				"hidden_cols" => ($ih_ob->prop("show_hidden_cols") == 1) ? true : false,
			));
		}

		$col_list = array_merge(array("" => ""), $col_list);

		switch($prop["name"])
		{
			case "inherit_view_props_from":
			// duke: what if I would implement lazy initialization for objpicker properties?
			// this way you could add additional parameters to the object_list created by
			// objpicker?
			case "sel_inherit_from":
				$ol = new object_list(array(
					"class_id" => CL_OBJECT_TREEVIEW_V2,
					"is_inheritable" => 1,
					"lang_id" => array(),
					"site_id" => array()
				));
				$prop["options"] = array("" => "") + $ol->names();
				break;

			case "show_link_field":
				if ($inherited)
				{
					return PROP_IGNORE;
				}

				// another hack, just to make possible to set, that show link won't appear
				// in any column, uh.
				unset($col_list['']);
				$col_list = array_merge(array("" => "", "---" => "---"), $col_list);
				$prop['options'] = $col_list;
				break;

			case "show_tpl":
				$prop['options'] = $this->tpls;
				break;

			case "url_field":
				if ($inherited)
				{
					return PROP_IGNORE;
				}
				$prop['options'] = $col_list;
				break;

			case "tree_type":
				if ($inherited)
				{
					return PROP_IGNORE;
				}

				$prop["options"] = array(
					TREE_DHTML => t("DHTML"),
					TREE_TABLE => t("Tabel"),
					TREE_COMBINED => t("Kombineeritud")
				);
				// if tree_type isn't set, TREE_DHTML will be used
				// eh, i definitely need a better solution to handle existing objects
				// cause right i now there are at least 2 more checks to make sure, that DHTML
				// will be used when nothing is set

				if (empty($prop['value']))
				{
					$prop['value'] = TREE_DHTML;
				}

				break;

			case "sortbl":
				if ($inherited)
				{
					return PROP_IGNORE;
				}
				$this->do_sortbl($arr);
				break;

			case "filter_table":
				$this->do_filter_table($arr);
				break;

			case "group_by_folder":
////
// here i should check, if AW object list uses meta objects to draw
// folders or not. If it doesn't, i think theres nothing to do with that
// property so just hide it.
				$ds_obj = $arr['obj_inst']->get_first_obj_by_reltype("RELTYPE_DATASOURCE");
				if(!empty($ds_obj) && ($ds_obj->class_id() == CL_OTV_DS_OBJ) && ($ds_obj->prop("use_meta_as_folders") == 1))
				{
					$prop['options'] = $col_list;
				}
				else
				{
					$retval = PROP_IGNORE;
				}
				break;
			case "group_in_table":
				if ($inherited)
				{
					return PROP_IGNORE;
				}
				$prop['options'] = $col_list;
				break;

			case "filter_by_char_field":
				if ($inherited)
				{
					return PROP_IGNORE;
				}
				$prop['options'] = $col_list;
				break;

			case "filter_by_char_order":
				if ($inherited)
				{
					return PROP_IGNORE;
				}
				$prop['options'] = array(
					"" => "",
					"asc" => "A - Z",
					"desc" => "Z - A",
				);
				break;
			case "group_table":
				$this->do_group_table($arr);
				break;
			case "access":
				$this->do_access_tbl($arr);
				break;

			case "columns":
				if ($inherited)
				{
					return PROP_IGNORE;
				}
				$this->_do_columns($arr);
				break;

			case "ds":
			case "show_folders":
			case "show_add":
			case "show_link_new_win":
			case "hide_content_table_by_default":
			case "per_page":
			case "show_hidden_cols":
			case "alphabet_in_lower_case":
			case "folders_table_column_count":
			case "no_cache_page":
				if ($inherited)
				{
					return PROP_IGNORE;
				}
				break;

			case "search_res":
				$this->_search_res($arr);
				break;

			case "columns_modify":
				$this->_columns_modify($arr);
				break;

			case "add_new_ds_type":
				$reli = $arr["obj_inst"]->get_relinfo();
				$clids = $reli["RELTYPE_DATASOURCE"]["clid"];
				$tps = array("" => "");
				$clss = aw_ini_get("classes");
				foreach($clids as $clid)
				{
					$tps[$clid] = $clss[$clid]["name"];
				}
				$prop["options"] = $tps;
				break;

			case "sel_copy_ds":
			case "sel_connect_ds":
				$reli = $arr["obj_inst"]->get_relinfo();
				$clids = $reli["RELTYPE_DATASOURCE"]["clid"];

				$ol = new object_list(array(
					"class_id" => $clids
				));
				$prop["options"] = array("" => "") + $ol->names();
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
			case "columns":
				$arr["obj_inst"]->set_meta("sel_columns", $arr["request"]["column"]);
				$arr["obj_inst"]->set_meta("sel_columns_ord", $arr["request"]["column_ord"]);
				$arr["obj_inst"]->set_meta("sel_columns_text", $arr["request"]["column_text"]);
				$arr["obj_inst"]->set_meta("sel_columns_sep_before", $arr["request"]["column_sep_before"]);
				$arr["obj_inst"]->set_meta("sel_columns_sep_after", $arr["request"]["column_sep_after"]);
				$arr["obj_inst"]->set_meta("sel_columns_editable", $arr["request"]["column_edit"]);
				$arr["obj_inst"]->set_meta("sel_columns_sortable", $arr["request"]["column_sortable"]);
				$arr["obj_inst"]->set_meta("sel_columns_controller", $arr["request"]["column_view_controller"]);
				$arr["obj_inst"]->set_meta("sel_columns_date_as_text", $arr["request"]["column_date_as_text"]);
				$arr["obj_inst"]->set_meta("sel_columns_show_as_date", $arr["request"]["column_show_as_date"]);

////
// don't save empty fields
				$valid_column_fields = array();

				foreach(safe_array($arr["request"]["column_fields"]) as $key => $value)
				{
					foreach($value as $k => $v)
					{
						if(empty($v['field']))
						{
							unset($value[$k]);
						}
					}
					if(!empty($value))
					{
						$valid_column_fields[$key] = $value;
					}
				}
				$arr["obj_inst"]->set_meta("sel_columns_fields", $valid_column_fields);
				break;

			case "sortbl":
				$this->do_save_sortbl($arr);
				break;

			case "filter_table":
				$this->do_save_filter_table($arr);
				break;

			case "group_table":
				$arr['obj_inst']->set_meta("saved_groups", $arr['request']['group_field']);
				break;

			case "columns_modify":
				$arr["obj_inst"]->set_meta("transform_cols", $arr["request"]["transform_cols"]);
				break;

			case "add_new_ds_type":
				if ($prop["value"] != "")
				{
					$o = obj();
					$o->set_class_id($prop["value"]);
					$o->set_name(sprintf(t("%s andmeallikas"), $arr["request"]["name"]));
					$o->set_parent($arr["request"]["parent"]);
					$o->save();

					$arr["obj_inst"]->set_prop("ds", $o->id());
				}
				break;

			case "sel_copy_ds":
				if ($prop["value"] != "")
				{
					$o = obj($prop["value"]);
					$o->set_name(sprintf(t("%s andmeallikas"), $arr["request"]["name"]));
					$o->set_parent($arr["request"]["parent"]);
					$o->save_new();

					// also rels
					$old = obj($prop["value"]);
					foreach($old->connections_from() as $c)
					{
						$o->connect(array(
							"to" => $c->prop("to"),
							"reltype" => $c->prop("reltype")
						));
					}

					$arr["obj_inst"]->set_prop("ds", $o->id());
				}
				break;

			case "sel_connect_ds":
				if ($prop["value"] != "")
				{
					$arr["obj_inst"]->set_prop("ds", $prop["value"]);
				}
				break;

			case "sel_inherit_from":
				$arr["obj_inst"]->set_prop("inherit_view_props_from", $prop["value"]);
				break;
		}
		return $retval;
	}

	function parse_alias($arr = array())
	{
		return $this->show(array(
			"id" => $arr["alias"]["target"],
			"oid" => $arr['oid'],
		));
	}

	/**

		@attrib name=show nologin=1

		@param id required type=int acl=view
		@param tv_sel optional type=int
		@param char optional
	**/
	function show($arr)
	{
		extract($arr);
		if (!is_oid($id))
		{
			return "";
		}

		$ob = obj($id);
		$search_i = false;

		if (!empty($_GET["otv2srch"]) and "1" === $_GET["otv2srch"] and $ob->prop("show_search_btn") and $this->can("view", $ob->prop("search")))
		{// show search form and table instead
			// toolbar
			$tb = new toolbar();
			$url = aw_url_change_var("otv2srch", null);
			$tb->add_button(array(
				"name" => "browse",
				"tooltip" => t("Sirvi"),
				"url" => $url,
				"img" => "archive.gif",
				"class" => "menuButton",
			));
			$res = $tb->get_toolbar();

			// search
			$res .= $search_i->show(array("id" => $ob->prop("search"), "extra_args" => array("otv2srch" => "1")));
			return $res;
		}

		$this->set_parse_method("eval");
		if (is_oid($ob->prop("inherit_view_props_from")) && $this->can("view", $ob->prop("inherit_view_props_from")))
		{
			$ih_ob = obj($ob->prop("inherit_view_props_from"));
		}
		else
		{
			$ih_ob = $ob;
		}

		if ($ih_ob->prop("no_cache_page") == 1)
		{
			aw_global_set("no_cache", 1);
		}

		$tpl = ($ob->prop("show_tpl") != "" ? $ob->prop("show_tpl") : "show") . ".tpl";
		$this->read_template($tpl);

		if ("show_search.tpl" === $tpl and $this->can("view", $ob->prop("search")))
		{ // search form
			$search_i = get_instance(CL_OBJECT_TREEVIEW_V2_SEARCH);
			$search_form = $search_i->show(array("id" => $ob->prop("search"), "show_table" => false));
			$this->vars(array(
				"SEARCH" => $search_form
			));
		}

		// init driver
		$d_o = obj($ih_ob->prop("ds"));
		$d_inst = $d_o->instance();

		$this->_insert_styles($ih_ob);


		// returns an array of object id's that are folders that are in the object
		$tree_type = $ob->prop("tree_type");
		if (empty($tree_type))
		{
			$tree_type = TREE_DHTML;
		}

		$fld = $d_inst->get_folders($d_o, $tree_type);
		// get all objects to show
		// if is checked, that objects won't be shown by default, then don't show them, unless
		// there are set some url params (tv_sel, char)
		$params = array(
			"sproc_params" => $ob->prop("sproc_params")
		);

		$edit_columns = safe_array($ih_ob->meta("sel_columns_editable"));

		if (($ih_ob->prop("hide_content_table_by_default") == 1) && empty($_GET['tv_sel']) && empty($_GET['char']))
		{
			$ol = array();
		}
		else
		{
			///
			// here i have to check, if datasource can filter the data
			// if it can, then i pass filter to datasource via get_objects method
			// if it cannot, i will filter the data here, in otv class (which is
			// going to be pretty slow when there is a lot of data to deal with -
			// and it takes place in memory, and everytime, ALL objects will be
			// queried from datasource, no matter how much of it passes the filtering
			if ($d_inst->has_feature("filter"))
			{
				$sc = safe_array($ih_ob->meta("sel_columns"));
				foreach(safe_array($ih_ob->meta("sel_columns_fields")) as $__k => $__v)
				{
					foreach($__v as $___k => $___v)
					{
						$sc[$___v["field"]] = 1;
					}
				}
				// make itemsorts fields also selected, no matter if they actually are selected or not
				foreach(safe_array($ih_ob->meta("itemsorts")) as $item)
				{
					$sc[$item['element']] = 1;
				}
				// and we need to mark this field also selected, which is used to create groups in table
				$sc[$ih_ob->prop("group_in_table")] = 1;

				$params = array(
					"filters" => array(
						"saved_filters" => new aw_array($ob->meta("saved_filters")),
						"group_by_folder" => $ob->prop("group_by_folder"),
						"filter_by_char_field" => $ih_ob->prop("filter_by_char_field"),
						"char" =>  isset($_GET['char']) ? ($_GET['char'] == "all" ? $_GET['char'] : $_GET['char']{0}) : null,
					),
					"sproc_params" => $ob->prop("sproc_params"),
					"sel_cols" => $sc,
					'edit_columns' => $edit_columns
				);
				$ol = $d_inst->get_objects($d_o, $fld, isset($_GET['tv_sel']) ? $_GET['tv_sel'] : null, $params);
			}
			else
			{
				$ol = $d_inst->get_objects($d_o, $fld, isset($_GET['tv_sel']) ? $_GET['tv_sel'] : null, $params);
				$ol = $this->filter_data(array(
					"ol" => $ol,
					"otv_obj" => $ob,
					"ds_obj" => $d_o,
					"folders" => $fld,
					"otv_obj_ih" => $ih_ob
				));
			}
		}

		// get folders parent for folders section caption
		$folders_parent_caption = "";

		foreach ($fld as $id => $data)
		{
			if (!array_key_exists($data["parent"], $fld) && $this->can("view", $id))
			{
				$tmp = obj($id);
				if ($this->can("view", $tmp->parent()))
				{
					$tmp = obj($tmp->parent());
					$folders_parent_caption = $tmp->name();
					break;
				}
			}
		}

		// make yah -- top parent in tree to current selection
		$yah = t("");

		if (isset($_GET["tv_sel"]) and $this->can("view", $_GET["tv_sel"]) and array_key_exists($_GET["tv_sel"], $fld))
		{
			$yah_data = array();
			$current = obj($_GET["tv_sel"]);

			do
			{
				$yah_data[] = $current->name();
				if ($this->can("view", $current->parent()))
				{
					$current = obj($current->parent());
				}
				else
				{
					$current = obj();
				}
			}
			while (array_key_exists($current->id(), $fld));

			$yah_data = array_reverse($yah_data);
			$yah = implode(" / ", $yah_data);
		}
		elseif (!empty($_GET["s"]))
		{
			$yah = t("Search results");
		}

		//
		$fld_str = "";
		if ($tree_type == 'TREE_COMBINED')
		{
			$table_folders = $d_inst->get_folders($d_o, 'TREE_TABLE');
			$fld_str = $this->_draw_folders($ob, $ol, $table_folders, $oid, $ih_ob, 'TREE_TABLE');
			$fld_str .= $this->_draw_folders($ob, $ol, $fld, $oid, $ih_ob, 'TREE_DHTML');
		}
		else
		{
			$fld_str = $this->_draw_folders($ob, $ol, $fld, /* FIXME! */ isset($oid) ? $oid : null, $ih_ob, $tree_type);
		}

		if (!empty($_GET["s"]) and is_object($search_i))
		{ // search result table
			$table = $search_i->show(array("id" => $ob->prop("search"), "show_form" => false));
		}
		else
		{
			// get all related object types
			// and their cfgforms
			// and make a nice little lut from them.
			$class2cfgform = array();
			foreach($ob->connections_from(array("type" => "RELTYPE_ADD_TYPE")) as $c)
			{
				$addtype = $c->to();
				if ($addtype->prop("use_cfgform"))
				{
					$class2cfgform[$addtype->prop("type")] = $addtype->prop("use_cfgform");
				}
			}

			// ok, lets get those fields - aw datasource object seems to give me full prop info too
			if (method_exists($d_inst, "get_fields"))
			{
				$sel_columns_full_prop_info = $d_inst->get_fields($d_o, true);
			}

			// if there are set some datasource fields to be displayed in one table field

			$sel_columns_fields = new aw_array($ih_ob->meta("sel_columns_fields"));
			if ($sel_columns_fields->count() != 0)
			{
				$ol_result = array();
				foreach($ol as $ol_item)
				{
					foreach($sel_columns_fields->get() as $sel_columns_fields_key => $sel_columns_fields_value)
					{
						if ($sel_columns_fields_key == "modifiedby")
						{
							$sel_columns_fields_key = "modder";
						}
						if ($sel_columns_fields_key == "createdby")
						{
							$sel_columns_fields_key = "adder";
						}
						foreach($sel_columns_fields_value as $key => $value)
						{
							if ($value["field"] == "modified")
							{
								$value["field"] = "mod_date";
							}
							if ($value["field"] == "created")
							{
								$value["field"] = "add_date";
							}

							if (empty($ol_item[$value['field']]))
							{
								$ol_item[$sel_columns_fields_key] .= "";
							}
							else
							{
								$ol_item[$sel_columns_fields_key] .= $value['sep'];
							}
							$ol_item[$sel_columns_fields_key] .= $value['left_encloser'];
							if ($value["field"] == "mod_date")
							{
								$scf_val = date("d.m.Y H:i", $ol_item[$value['field']]);
							}
							else
							if ($value["field"] == "add_date")
							{
								$scf_val = date("d.m.Y H:i", $ol_item[$value['field']]);
							}
							else
							if ($sel_columns_full_prop_info[$value['field']]["type"] == "date_select")
							{
								$scf_val = date("d.m.Y", $ol_item[$value['field']]);
							}
							else
							{
								$scf_val = $ol_item[$value['field']];
							}
							if ($value["field"] == "name")
							{
								// make link from name field
								$scf_val = $this->_get_link($scf_val, $ol_item["url"], $ih_ob);
							}

							$ol_item[$sel_columns_fields_key] .= $scf_val;
							$ol_item[$sel_columns_fields_key] .= $value['right_encloser'];

						}
					}
					array_push($ol_result, $ol_item);
				}
				$ol = $ol_result;
			}

			$this->cnt = 0;
			$c = "";
			$sel_cols = $ih_ob->meta("sel_columns");

			// if the controller sets $retval to PROP_IGNORE then this column will not
			// be shown in table
			// later, if some other data will be needed in controller, then use the first or
			// third param of the check_property fn.
			$controllers = $ih_ob->meta("sel_columns_controller");
			$view_controller_inst = get_instance(CL_CFG_VIEW_CONTROLLER);
			if (is_array($controllers))
			{
				foreach ($controllers as $controller_key => $controller_value)
				{
					// the controller have to be connected
					if (!empty($controller_value) && $ih_ob->is_connected_to(array("to" => $controller_value)))
					{
						$null = null;
						if ($view_controller_inst->check_property($null, $controller_value, array()) == PROP_IGNORE)
						{
							unset($sel_cols[$controller_key]);
						}
					}
				}
			}
			$col_list = $this->_get_col_list(array(
				"o" => $ih_ob,
				"hidden_cols" => true,
			));
	// well, if char is present in the url, then sort only by
	// the field which is set to be filtered according to char

			$tmp_order = $ih_ob->prop("filter_by_char_order");
			if(!empty($_GET['char']) && !empty($tmp_order))
			{
				$itemsorts = new aw_array(array(
					array(
						"element" => $ih_ob->prop("filter_by_char_field"),
						"ord" => $tmp_order,
					),
				));
			}
			else
			if (isset($_GET['sort_by']) and array_key_exists($_GET['sort_by'], $col_list))
			{

				$tmp = new aw_array($ih_ob->meta("itemsorts"));

				$tmp_group_field = $ih_ob->prop("group_in_table");
				$itemsorts = new aw_array();
				foreach ($tmp->get() as $tmp_key => $tmp_value)
				{

					if ($tmp_value['element'] == $tmp_group_field)
					{
						$itemsorts->set($tmp_value);
					}
				}
				$date_as_text = $ih_ob->meta("sel_columns_date_as_text");
				$itemsorts->set(array(
					"element" => $_GET['sort_by'],
					"ord" => ($_GET['sort_order'] == "asc") ? "asc" : "desc",
					"is_date" => !empty($date_as_text[$_GET['sort_by']])
				));
			}
			else
			{
				$tmp = new aw_array($ih_ob->meta("itemsorts"));
				$first_itemsort = $tmp->first();
				$tmp_group_field = $ih_ob->prop("group_in_table");
				$itemsorts = new aw_array();
				if ($first_itemsort['value']['element'] != $tmp_group_field)
				{
					$itemsorts->set(array(
						"element" => $tmp_group_field,
						"ord" => "asc",
					));
					foreach ($tmp->get() as $value)
					{
						$itemsorts->set($value);
					}
				}
				else
				{
					$itemsorts = $tmp;
				}
			}
			$this->__is = $itemsorts->get();
			if (count($this->__is))
			{
				usort($ol, array(&$this, "__is_sorter"));
			}

			// now do pages
			if ($ih_ob->prop("per_page"))
			{
				$this->do_pageselector($ol, $ih_ob->prop("per_page"));
			}

			$has_access_to = false;
			$has_add_access = false;

			foreach($ol as $okey => $odata)
			{
				if ($d_inst->check_acl("edit", $d_o, $odata["id"]))
				{
					$has_access_to = true;
				}
				else
				{
					// if there is no edit permission to an object, then don't show the edit link eather
					$ol[$okey]['change'] = $odata['change'] = "";

				}
				$last_o = $odata;
			}
		// moved it at the beginning of function, cause i need to pass it to datasource
		// when requesting objects --dragut
		//	$edit_columns = safe_array($ih_ob->meta("sel_columns_editable"));

			if (!$has_access_to)
			{
				unset($col_list["change"]);
				unset($col_list["select"]);

				// also unset all edit columns
				foreach($edit_columns as $coln => $_tmp)
				{
					unset($col_list[$coln]);
				}
				$edit_columns = array();
			}

			if ($last_o)
			{
				if (!$d_inst->check_acl("add", $d_o, ($last_o["parent"] ? $last_o["parent"] : $last_o["id"])))
				{
					$ih_ob->set_prop("show_add", false);
				}

				if (!$d_inst->check_acl("delete", $d_o, $last_o["id"]))
				{
					$ih_ob->set_meta("no_delete", true);
				}
			}
			else
			if ($_GET["tv_sel"])
			{
				if (!$d_inst->check_acl("add", $d_o, $_GET["tv_sel"]))
				{
					$ih_ob->set_prop("show_add", false);
				}
				if (!$d_inst->check_acl("delete", $d_o, $_GET["tv_sel"]))
				{
					$ih_ob->set_meta("no_delete", true);
				}
			}

			$style_obj = $ih_ob;

			$group_field = $ih_ob->prop("group_in_table");
			$group_name = "";
			$sel_cols_count = count($sel_cols);
	// parsing table rows - if the field value, which is used to make table groups
	// changes, i'll create group header line and put it in the table
	// groups are not made, if char param is present in url

			/// row controllers from this object
			$controllers = array();
			$conn = $ob->connections_from(array(
				"type" => "RELTYPE_ROW_CONTROLLER"
			));
			foreach($conn as $cn)
			{
				$controllers[] = $cn->prop("to");
			}

			/// row controllers from inherited object
			$conn = $ih_ob->connections_from(array(
				"type" => "RELTYPE_ROW_CONTROLLER"
			));
			foreach($conn as $cn)
			{
				$controllers[] = $cn->prop("to");
			}

			$view_controller_inst = get_instance(CL_CFG_VIEW_CONTROLLER);
			foreach($ol as $odata)
			{
				foreach ($controllers as $controller)
				{
					$V2GAT2HTISMUUTUJA = array();
					if ($view_controller_inst->check_property($odata, $controller, $V2GAT2HTISMUUTUJA) == PROP_IGNORE)
					{
						continue;
					}
				}

				if((!isset($odata[$group_field]) && $group_name != null || isset($odata[$group_field]) && $group_name != $odata[$group_field]) && empty($_GET['char']))
				{
					$this->vars(array(
						"content" => "<a name=\"".$this->_mk_anch($odata[$ih_ob->prop("group_in_table")])."\" ></a>".$odata[$ih_ob->prop("group_in_table")],
						"cols_count" => $sel_cols_count,
	//					"group_bgcolor" => $group_header_color_code,
					));
					$c .= $this->parse("FILE_GROUP");
				}

				$c .= $this->_do_parse_file_line($odata, $d_inst, $d_o, array(
					"tree_obj" => $ob,
					"tree_obj_ih" => $ih_ob,
					"sel_cols" => $sel_cols,
					"col_list" => $col_list,
					"edit_columns" => $edit_columns,
					"pfk" => $ob,
					"style_obj" => $style_obj,
					"sel_columns_full_prop_info" => $sel_columns_full_prop_info,
				));
				$group_name = isset($odata[$group_field]) ? $odata[$group_field] : null;
			}

			$tb = "";
			$no_tb = "";
			if ($ih_ob->prop("show_add"))
			{
				$tb = $this->parse("HEADER_HAS_TOOLBAR");
			}
			else
			{
				$no_tb = $this->parse("HEADER_NO_TOOLBAR");
			}

			// if table anchor should be added at the end of the url
			$anchor = "";
			if ($ih_ob->prop("add_table_anchor_to_url"))
			{
				$anchor = "#table";
			}

			// checking, if there is set a field, which values should be use to filter by first character
			// and according to this i'm showing or not showing the alphabet list
			$filter_by_char_field = $ih_ob->meta("filter_by_char_field");
			if(!empty($filter_by_char_field))
			{
				$alphabet_parsed = "";
				foreach($this->alphabet as $character)
				{
					$this->vars(array(
						"char" => ($ih_ob->prop("alphabet_in_lower_case")) ? strtolower($character) : $character,
						"char_url" => aw_ini_get("baseurl")."/".$oid."?char=".$character.$anchor,
					));
					if ($character == htmlentities(urldecode($_GET['char'])))
					{
						$alphabet_parsed .= $this->parse("ALPHABET_SEL");
					}
					else
					{
						$alphabet_parsed .= $this->parse("ALPHABET");
					}
				}

				// lets put a link at the end of the alphabet to make all fields to show
				$this->vars(array(
					"char" => t("K&otilde;ik"),
					"char_url" => aw_ini_get("baseurl")."/".$oid."?char=all".$anchor,
				));
				// and of course we need to make it selected if is selected
				if ($_GET['char'] == "all")
				{
					$alphabet_parsed .= $this->parse("ALPHABET_SEL");
				}
				else
				{
					$alphabet_parsed .= $this->parse("ALPHABET");
				}
			}

			$this->vars(array(
				"ALPHABET" => isset($alphabet_parsed) ? $alphabet_parsed : null,
				"FILE" => $c,
				"HEADER_HAS_TOOLBAR" => $tb,
				"HEADER_NO_TOOLBAR" => $no_tb,
				"reforb" => $this->mk_reforb("submit_show", array(
					"return_url" => aw_global_get("REQUEST_URI"),
					"subact" => "0",
					"id" => $ob->id(),
					"edit_mode" => count($edit_columns),
					"tv_sel" => isset($_GET["tv_sel"]) ? $_GET["tv_sel"] : null
				))
			));

			$udef_cols = $ih_ob->meta("sel_columns_text");
			$sortable_cols = $ih_ob->meta("sel_columns_sortable");
			if (!is_array($udef_cols))
			{
				$udef_cols = $col_list;
			}
			if (!$ob->prop("show_empty_table_header") and ((($ih_ob->meta("hide_content_table_by_default") == 1) && empty($_GET['tv_sel']) && empty($_GET['char'])) || empty($ol)))
			{

			}
			else
			{
				// columns
				$h_str = "";
				foreach($col_list as $colid => $coln)
				{
					$str = "";
					if (isset($sel_cols[$colid]) && $sel_cols[$colid] == 1)
					{
						if (isset($sortable_cols[$colid]) && $sortable_cols[$colid] == 1)
						{
							$tmp_url = aw_global_get("REQUEST_URI");
							if ($_GET['sort_order'] == "asc" && $_GET['sort_by'] == $colid)
							{
								$tmp_sort_order = "desc";
							}
							else
							{
								$tmp_sort_order = "asc";
							}
	//						$tmp_sort_order = ($_GET['sort_order'] == "asc") ? "desc" : "asc";
							if (!empty($_GET))
							{
								$tmp_url = aw_url_change_var("char", $_GET['char'], $tmp_url);
								$tmp_url = aw_url_change_var("tv_sel", $_GET['tv_sel'], $tmp_url);
							//	$tmp_url .= "&sort_by=".$colid."&sort_order=".$tmp_sort_order;

							}

							$tmp_url = aw_url_change_var("sort_by", $colid, $tmp_url);
							$tmp_url = aw_url_change_var("sort_order", $tmp_sort_order, $tmp_url);

							$this->vars(array(
								"h_text" => html::href(array(
							//		"url" => $arr['oid'].$tmp_url.$anchor,
									"url" => $tmp_url.$anchor,
									"caption" => $udef_cols[$colid],
								)),
							));
						}
						else
						{
							$this->vars(array(
								"h_text" => ($udef_cols[$colid])
							));
						}

						$str = $this->parse("HEADER");

						$this->vars(array(
							"HEADER" => $str
						));
						$h_str .= $this->parse("HEADER");
					}
				}

				$this->vars(array(
					"HEADER" => $h_str
				));
			}

			$table = $this->parse("TABLE");
		}

		$this->vars(array(
			"yah" => $yah,
			"folders_parent_caption" => $folders_parent_caption,
			"FOLDERS" => $fld_str,
			"TABLE" => $table
		));
		$res = $this->parse();

		if ($ih_ob->prop("show_add"))
		{
			$res = $this->_get_add_toolbar($ih_ob).$res;
		}

		if (strpos($res, "<a") !== false || strpos($res, "< a") !== false || strpos($res, "<A") !== false)
		{
			return $res;
		}

		return create_email_links($res);
	}

	function _init_cols_tbl(&$t, $o)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"sortable" => 1,
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "show",
			"caption" => t("Kas n&auml;idata"),
			"sortable" => 1,
			"align" => "center"
		));
		if (count($o->connections_from(array("type" => "RELTYPE_VIEW_CONTROLLER"))) > 0)
		{
			$t->define_field(array(
				"name" => "controller",
				"caption" => t("Kontroller"),
				"align" => "center",
			));
		}
		$t->define_field(array(
			"name" => "jrk",
			"caption" => t("Jrk"),
			"sortable" => 1,
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "sep_before",
			"caption" => t("Eraldaja enne"),
			"sortable" => 1,
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "text",
			"caption" => t("Tekst"),
			"sortable" => 1,
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "sep_after",
			"caption" => t("Eraldaja p&auml;rast"),
			"sortable" => 1,
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "editable",
			"caption" => t("Muudetav"),
			"sortable" => 1,
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "sortable",
			"caption" => t("Sorteeritav"),
			"sortable" => 1,
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "date_as_text",
			"caption" => t("Kuup&auml;ev tekstina"),
			"sortable" => 1,
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "show_as_date",
			"caption" => t("Kuup&auml;ev"),
			"sortable" => 1,
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "fields",
			"caption" => t("Milliste v&auml;ljade sisu n&auml;idata<br>Eraldaja&nbsp;&nbsp;|&nbsp;&nbsp;Vasak&nbsp;tekst&nbsp;&nbsp;|&nbsp;&nbsp;V&auml;li&nbsp;&nbsp;|&nbsp;&nbsp;Parem&nbsp;tekst"),
			"sortable" => 1,
			"align" => "center"
		));
	}

	function _do_columns($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_cols_tbl($t, $arr['obj_inst']);

		$cols = $arr["obj_inst"]->meta("sel_columns");
		$cols_ord = $arr["obj_inst"]->meta("sel_columns_ord");
		$cols_text = $arr["obj_inst"]->meta("sel_columns_text");
		$cols_sep_before = $arr["obj_inst"]->meta("sel_columns_sep_before");
		$cols_sep_after = $arr["obj_inst"]->meta("sel_columns_sep_after");
		$cols_edit = $arr["obj_inst"]->meta("sel_columns_editable");
		$cols_sortable = $arr["obj_inst"]->meta("sel_columns_sortable");
		$cols_fields = $arr["obj_inst"]->meta("sel_columns_fields");
		$cols_date_as_text = $arr["obj_inst"]->meta("sel_columns_date_as_text");
		$cols_show_as_date = $arr["obj_inst"]->meta("sel_columns_show_as_date");
		$cols_view_controllers = $arr['obj_inst']->meta("sel_columns_controller");

		$ob = $arr["obj_inst"];
		if (is_oid($ob->prop("inherit_view_props_from")) && $this->can("view", $ob->prop("inherit_view_props_from")))
		{
			$ih_ob = obj($ob->prop("inherit_view_props_from"));
		}
		else
		{
			$ih_ob = $ob;
		}

		$cold = $this->_get_col_list(array(
			"o" => $ih_ob,
			"hidden_cols" => true,
		));
		$conns_to_controllers = $ih_ob->connections_from(array(
			"type" => "RELTYPE_VIEW_CONTROLLER",
		));

		$controller_list = array("" => "");
		foreach ($conns_to_controllers as $conn_to_controller)
		{
			$controller_list[$conn_to_controller->prop("to")] = $conn_to_controller->prop("to.name");
		}
		if (!is_array($cols_text))
		{
			$cols_text = $cold;
		}
		foreach($cold as $colid => $coln)
		{
			$show_as_date = $date_as_text = $text = $editable = $sortable = $fields = $sep_before = $sep_after = $controller = "";


			if ($cols[$colid])
			{
				$text = html::textbox(array(
					"name" => "column_text[".$colid."]",
					"value" => $cols_text[$colid],
					"size" => 20
				));

				$sep_before = html::textbox(array(
					"name" => "column_sep_before[".$colid."]",
					"value" => $cols_sep_before[$colid],
					"size" => 5
				));

				$sep_after = html::textbox(array(
					"name" => "column_sep_after[".$colid."]",
					"value" => $cols_sep_after[$colid],
					"size" => 5
				));

				$editable = html::checkbox(array(
					"name" => "column_edit[".$colid."]",
					"value" => 1,
					"checked" => $cols_edit[$colid],
				));
				$sortable = html::checkbox(array(
					"name" => "column_sortable[".$colid."]",
					"value" => 1,
					"checked" => $cols_sortable[$colid],
				));
				$date_as_text = html::checkbox(array(
					"name" => "column_date_as_text[".$colid."]",
					"value" => 1,
					"checked" => $cols_date_as_text[$colid],
				));

				$show_as_date = html::checkbox(array(
					"name" => "column_show_as_date[".$colid."]",
					"value" => 1,
					"checked" => $cols_show_as_date[$colid],
				));
				$controller = html::select(array(
					"name" => "column_view_controller[".$colid."]",
					"options" => $controller_list,
					"selected" => $cols_view_controllers[$colid],
				));
				$max_id = 0;
				$fields = "";

				if (is_array($cols_fields[$colid]))
				{
					foreach($cols_fields[$colid] as $f_key => $f_val)
					{

						$fields .= html::textbox(array(
							"name" => "column_fields[".$colid."][".$f_key."][sep]",
							"value" => $cols_fields[$colid][$f_key]['sep'],
							"size" => 2,
						));

						$fields .= html::textbox(array(
							"name" => "column_fields[".$colid."][".$f_key."][left_encloser]",
							"value" => $cols_fields[$colid][$f_key]['left_encloser'],
							"size" => 2,
						));

						$fields .= html::select(array(
							"name" => "column_fields[".$colid."][".$f_key."][field]",
							"options" => array_merge(array(""=>""), $cold),
							"selected" => ($cols_fields) ? $cols_fields[$colid][$f_key]['field'] : $colid,
						));

						$fields .= html::textbox(array(
							"name" => "column_fields[".$colid."][".$f_key."][right_encloser]",
							"value" => $cols_fields[$colid][$f_key]['right_encloser'],
							"size" => 2,
						));

						$fields .= "<br />";
					}
				}
				$max_id = max($max_id, $f_key);
				$max_id++;

				$fields .= html::textbox(array(
					"name" => "column_fields[".$colid."][".$max_id."][sep]",
					"value" => "",
					"size" => 2,
				));

				$fields .= html::textbox(array(
					"name" => "column_fields[".$colid."][".$max_id."][left_encloser]",
					"value" => "",
					"size" => 2,
				));

				$fields .= html::select(array(
					"name" => "column_fields[".$colid."][".$max_id."][field]",
					"options" => array_merge(array(""=>""), $cold),
					"selected" => "",
				));

				$fields .= html::textbox(array(
					"name" => "column_fields[".$colid."][".$max_id."][right_encloser]",
					"value" => "",
					"size" => 2,
				));
			}

			$t->define_data(array(
				"name" => $coln,
				"show" => html::checkbox(array(
					"name" => "column[".$colid."]",
					"value" => 1,
					"checked" => ($cols[$colid])
				)),
				"jrk" => html::textbox(array(
					"name" => "column_ord[".$colid."]",
					"size" => 5,
					"value" => $cols_ord[$colid],
				)),
				"text" => $text,
				"editable" => $editable,
				"sortable" => $sortable,
				"fields" => $fields,
				"sep_before" => $sep_before,
				"sep_after" => $sep_after,
				"controller" => $controller,
				"date_as_text" => $date_as_text,
				"show_as_date" => $show_as_date
			));
		}

		$t->set_default_sortby("name");
		$t->sort_by();
	}

	function _insert_styles($o)
	{
		$style = "textmiddle";
		$header_css = "textmiddle";
		if ($o->prop("header_css"))
		{
			$header_css = "st".$o->prop("header_css");
			active_page_data::add_site_css_style($o->prop("header_css"));
		}

		$group_css = "textmiddle";
		if ($o->prop("group_css"))
		{
			$group_css = "st".$o->prop("group_css");
			active_page_data::add_site_css_style($o->prop("group_css"));
		}

// lets put a css style for table too
		$table_css = "textmiddle";
		if ($o->prop("table_css"))
		{
			$table_css = "st".$o->prop("table_css");
			active_page_data::add_site_css_style($o->prop("table_css"));
		}

		$header_bg = "";
		if ($o->prop("title_bgcolor"))
		{
			$header_bg = $o->prop("title_bgcolor");
			if($header_bg{0} != "#")
			{
				$header_bg = "#".$o->prop("title_bgcolor");
			}
/*			else
			{
				$header_bg = $o->prop("title_bgcolor");
			}
*/
		}

		$group_header_bg = "";
		if($o->prop("group_header_bgcolor"))
		{
			$group_header_bg = $o->prop("group_header_bgcolor");
			if(!empty($group_header_bg) && $group_header_bg{0} != "#")
			{
				$group_header_bg = "#".$group_header_bg;
			}
		}
		$this->vars(array(
			"css_class" => $style,
			"table_css_class" => $table_css,
			"header_css_class" => $header_css,
			"group_css_class" => $group_css,
			"header_bgcolor" => $header_bg,
			"group_header_bgcolor" => $group_header_bg,
		));
	}

	function _get_bgcolor($ob, $line)
	{
		$ret = "";
		if (($line % 2) == 1)
		{
			$ret = $ob->prop("odd_bgcolor");
		}
		else
		{
			$ret = $ob->prop("even_bgcolor");
		}
		if ($ret != "" && $ret{0} != "#")
		{
			$ret = "#".$ret;
		}
		return $ret;
	}

	function _get_style_text($ob, $line)
	{
		$ret = "";
		if (($line % 2) == 1)
		{
			$ret = $ob->prop("odd_css_text");
		}
		else
		{
			$ret = $ob->prop("even_css_text");
		}

		if ($ret != "")
		{
			$ret = " style=\"$ret\"";
		}
		return $ret;
	}

	function _draw_folders($ob, $ol, $folders, $oid, $ih_ob, $tree_type = "")
	{
		if (!$ih_ob->meta('show_folders'))
		{
			return;
		}
	//	$tree_type = $ob->prop("tree_type");
		if (empty($tree_type))
		{
			$tree_type = TREE_DHTML;
		}

		// if #table anchor should be added to url
		$anchor = "";
		if ($ob->prop("add_table_anchor_to_url"))
		{
			$anchor = "#table";
		}
		switch ($tree_type)
		{
			case "TREE_TABLE":
				
				$table = new vcl_table();
				$cols_count = $ih_ob->prop("folders_table_column_count");
				if (empty($cols_count))
				{
					$cols_count = 2;
				}

				$folders_count = count($folders);
				$folders_count_in_col = ceil($folders_count / $cols_count);

				for ($i = 0; $i < $cols_count; $i++)
				{
					$table->define_field(array(
						"name" => "col_".$i,
						"caption" => t("&nbsp;"),
					));
				}

				$folders[$_GET['tv_sel']]['name'] = "<strong>".$folders[$_GET['tv_sel']]['name']."</strong>";
				$tmp_fld = array_chunk($folders, $folders_count_in_col);

				for ($i = 0; $i < $folders_count_in_col; $i++)
				{
					$row = array();
					for ($j = 0; $j < $cols_count; $j++)
					{
						$row["col_".$j] = html::href(array(
							"caption" => $tmp_fld[$j][$i]['name'],
							"url" => aw_ini_get("baseurl")."/".$oid."?tv_sel=".$tmp_fld[$j][$i]['id'].$anchor,
						));
					}
					$table->define_data($row);
				}

				return $table->draw();

			case "TREE_DHTML":
				
				// use treeview widget
				$tv = get_instance("vcl/treeview");
				$tv->start_tree(array(
					"tree_id" => "folders_tree",
					"root_name" => "",
					"root_url" => "",
					"root_icon" => "",
					"type" => TREE_DHTML, //$ob->meta('tree_type'),
					"persist_state" => true,
				));

				// now, insert all folders defined
				foreach($folders as $fld)
				{
					$tv->add_item($fld["parent"], array(
						"id" => $fld["id"],
						"name" => $fld["name"],
						"url" => aw_ini_get("baseurl")."/".$oid."?tv_sel=".$fld['id'].$anchor,
						"icon" => $fld["icon"],
						"comment" => $fld["comment"],
						"data" => array(
							"changed" => $this->time2date($fld["modified"], 2)
						)
					));
				}

				$tv->set_selected_item($_GET["tv_sel"]);
				$pms = array();
				return $tv->finalize_tree($pms);
		}
	}

	function _get_add_toolbar($ob, $drv = NULL)
	{
		// must read these from the datasource
		$ds_o = obj($ob->prop("ds"));
		$ds_i = $ds_o->instance();
		list($parent, $types) = $ds_i->get_add_types($ds_o);

		$tb = get_instance("vcl/toolbar");
		$has_b = false;

		if ($parent && count($types) && $ds_i->check_acl("add", $ds_o, $parent))
		{
			$menu = "";
			$classes = aw_ini_get("classes");

			$tb->add_menu_button(array(
				"name" => "add",
				"tooltip" => t("Uus"),
				"img" => "new.gif",
			));

			$ot = get_instance(CL_OBJECT_TYPE);
			foreach($types as $c_o)
			{
				// FIXME: Undo hard coding!
				if ($c_o->type == link_fix::CLID || $c_o->type == doc_obj::CLID || $c_o->type == file_obj::CLID || $c_o->type == menu_obj::CLID)
				{
					$tb->add_menu_item(array(
						"parent" => "add",
						"onClick" => "AW.UI.object_treeview_v2.open_modal(" . $c_o->type. ", {$parent})",
						"url" => "javascript:void(0)",
						"text" => $c_o->prop("name"),
					));
				}
				else
				{
					$tb->add_menu_item(array(
						"parent" => "add",
						"url" => $ot->get_add_url(array("id" => $c_o->id(), "parent" => $parent, "section" => $parent)),
						"text" => $c_o->prop("name"),
					));
				}
			}

			$has_b = true;
		}

		$cols = $ob->meta("sel_columns");
		if ($cols["select"] && !$ob->meta("no_delete") && $this->cnt)
		{
			$tb->add_button(array(
				"name" => "del",
				"tooltip" => t("Kustuta"),
				"url" => "#",
				"onClick" => "document.objlist.subact.value='delete';document.objlist.submit()",
				"img" => "delete.gif",
				"class" => "menuButton",
				"confirm" => t("Oled kindel et tahad objekte kustutada?")
			));
			$has_b = true;
		}

		$edc = safe_array($ob->meta("sel_columns_editable"));
		if (count($edc))
		{
			$tb->add_button(array(
				"name" => "save",
				"tooltip" => t("Salvesta"),
				"url" => "#",
				"onClick" => "document.objlist.submit();return true;",
				"img" => "save.gif"
			));
			$has_b = true;
		}

		if ($ob->prop("show_search_btn") and empty($_GET["otv2srch"]))
		{
			$url = aw_url_change_var("otv2srch", "1");
			$tb->add_button(array(
				"name" => "search",
				"tooltip" => t("Otsi"),
				"url" => $url,
				"img" => "search.gif",
				"class" => "menuButton",
			));
			$has_b = true;
		}

		if ($has_b)
		{
			return $tb->get_toolbar();
		}
		return "";
	}

	function _do_parse_file_line($arr, $drv, $d_o, $parms)
	{
		extract($parms);
		extract($arr);

		$show_link = $this->_get_link($name, $url, $parms['tree_obj_ih']);
		$sep_before = $parms["tree_obj_ih"]->meta("sel_columns_sep_before");
		$sep_after = $parms["tree_obj_ih"]->meta("sel_columns_sep_after");
		$date_as_text = $parms["tree_obj_ih"]->meta("sel_columns_date_as_text");
		$show_as_date = $parms["tree_obj_ih"]->meta("sel_columns_show_as_date");

		$formatv = array(
			"show" => $url,
			"name" => $name,
			"oid" => isset($oid) ? $oid : null,
			"target" => $target,
			"sizeBytes" => $fileSizeBytes,
			"sizeKBytes" => $fileSizeKBytes,
			"sizeMBytes" => $fileSizeMBytes,
			"comment" => $comment,
			"class_id" => $type,
			"created" => date("d.m.Y H:i", $add_date),
			"modified" => date("d.m.Y H:i", $mod_date),
			"createdby" => $adder,
			"modifiedby" => $modder,
			"icon" => $object_icon,
			"act" => isset($act) ? $act : null,
			"delete" => isset($delete) ? $delete : null,
			"bgcolor" => isset($bgcolor) ? $bgcolor : null,
			"size" => ($fileSizeMBytes > 1 ? $fileSizeMBytes."MB" : ($fileSizeKBytes > 1 ? $fileSizeKBytes."kb" : $fileSizeBytes."b")),
			"change" => $change,
			"select" => html::checkbox(array(
				"name" => "sel[]",
				"value" => $id,
			)),
		);

		if ($this->is_template("DELETE"))
		{
			$del = "";
			if ($drv->check_acl("delete", $d_o, $arr["id"]))
			{
				$del = $this->parse("DELETE");
			}

			$this->vars(array(
				"DELETE" => $del
			));
		}

		$tb = "";
		$no_tb = "";
		if ($tree_obj_ih->prop("show_add"))
		{
			$tb = $this->parse("HAS_TOOLBAR");
		}
		else
		{
			$no_tb = $this->parse("NO_TOOLBAR");
		}
		$this->vars(array(
			"HAS_TOOLBAR" => $tb,
			"NO_TOOLBAR" => $no_tb
		));

		$trs = safe_array($parms['tree_obj_ih']->meta("transform_cols"));
		// columns
		$str = "";//if(aw_global_get("uid") == "struktuur"){arr($date_as_text);}
		foreach($col_list as $colid => $coln)
		{
			if (isset($sel_cols[$colid]) && $sel_cols[$colid] == 1)
			{
				if (isset($formatv[$colid]))
				{
					$content = $formatv[$colid];
				}
				else
				if (strpos($sel_columns_full_prop_info[$colid]['type'], "date") !== false || !empty($show_as_date[$colid]))
				{
					$content = date("d.m.Y", $arr[$colid]);
				}
				else
				{
					$content = $arr[$colid];
				}

				if (isset($trs[$colid]) && count($trs[$colid]))
				{
					foreach($trs[$colid] as $tr_id)
					{
						$this->tr_i->transform(obj($tr_id), $content, $arr);
					}
				}

/*				if (!empty($date_as_text[$colid]))
				{
					list($d,$m,$y) = explode(".", $content);
					list($y, $tm) = explode(" ", $y);
					list($h, $min) = explode(":", $tm);
					$y = strlen((int)$y) == 4 ? $y : ($y < 30 ? "20".$y : "19".$y);
					$content = mktime($h,$min,0,$m, $d, $y);
				}*/
				if (isset($edit_columns[$colid]) && $edit_columns[$colid] == 1)
				{
					switch($sel_columns_full_prop_info[$colid]["type"])
					{
						case "classificator":
							$clss = aw_ini_get("classes");
							$cls_i = get_instance(CL_CLASSIFICATOR);
							static $clsf_opts;
							if (!$clsf_opts)
							{
								$classificator_choices = $cls_i->get_choices(array(
									'name' => $colid,
									"clid" => $sel_columns_full_prop_info[$colid]["class_id"],
									"object_type" => $sel_columns_full_prop_info[$colid]["object_type"],

								));
								$clsf_opts = $classificator_choices[4]['list_names'];

							}
							$content = html::select(array(
								"name" => "objs[".$arr["id"]."][$colid]",
								"value" => $content,
								"options" =>  array(
									"0" => t("--Vali--")
								) + safe_array($clsf_opts),
							));
							break;

						default:
							$content = html::textbox(array(
								"name" => "objs[".$arr["id"]."][$colid]",
								"value" => $content,
								"size" => 5
							));
							break;
					}
				}
				else
				{
					// show link can only be on the column, which is not editable
					$show_link_field = $tree_obj_ih->prop("show_link_field");
					if (!empty($show_link_field) && $show_link_field == $colid)
					{
						// well, actually i don't like this hack, but if it is needed to select
						// that show link won't appear in any columns, then this is the only way
						// i can come out right now. The best way should be, that if no show_link_field
						// are selected, THEN it won't appear in any column, but it will also break
						// existing objects so this is out of question.

						if ($show_link_field != "---")
						{
							$url_field = $parms['tree_obj_ih']->prop("url_field");

							if (!empty($url_field))
							{
								$content = $this->_get_link($content, $arr[$url_field], $parms['tree_obj_ih'], !empty($arr["target"]));
							}
							else
							{
								$content = $this->_get_link($content, $url, $parms['tree_obj_ih'],!empty($arr["target"]));
							}

						}
					}
					else
					if (empty($show_link_field) && $colid == "name")
					{
						// here i will make sure, that existing objects, which don't have the
						// show_link_field property set, have show link in name field
						$content = $this->_get_link($content, $url, $parms['tree_obj_ih'], !empty($arr["target"]));
					}
				}
				$image_obj = "";
				if (strstr($colid, "userim"))
				{
					if (is_oid($arr[$colid]) && $this->can("view", $arr[$colid]))
					{
						$image_inst = get_instance(CL_IMAGE);
						$image_obj = new object($arr[$colid]);
						$content = $image_inst->get_url_by_id($arr[$colid]);
					}
					else
					{
						$sep_before[$colid] = $sep_after[$colid] = "";
					}
				}
				if ($sep_before[$colid] != "")
				{
					$content = $sep_before[$colid].$content;
				}
				if ($sep_after[$colid] != "")
				{
					$content .= $sep_after[$colid];
				}

				$this->vars(array(
					"content" => $content,
				));

				$str .= $this->parse("COLUMN");
			}
		}

		$this->cnt++;

		$this->vars(array(
			"COLUMN" => $str
		));

// get row background color

		$this->vars(array(
			"bgcolor" => $this->_get_bgcolor($style_obj, $this->cnt),
			"style_text" => $this->_get_style_text($style_obj, $this->cnt)
		));

		return $this->parse("FILE");
	}

	///
	// !Get columns list
	//  o(Object)
	//  hidden_cols(bool) - true, if hidden cols should be returned
	//
	function _get_col_list($params = array())
	{
		extract($params);

		$tmp = $o->meta("sel_columns");

		$cold = $this->all_cols;
		if ($o->prop("ds"))
		{
			$dso = obj($o->prop("ds"));
			$ds_i = $dso->instance();
			if (method_exists($ds_i, "get_fields"))
			{
				$fds = $ds_i->get_fields($dso);
				foreach($fds as $fn => $fs)
				{
					$cold[$fn] = $fs;
				}
			}
		}

		foreach($cold as $col_key => $col_val)
		{
			if(!$hidden_cols)
			{
				if($tmp[$col_key] != 1)
				{
					unset($cold[$col_key]);
				}
			}
		}

		$cold["status"] = t("Staatus");
		// sort
		$this->__sby = $o->meta("sel_columns_ord");
		uksort($cold, array($this, "__sby"));
		return $cold;
	}

	function __sby($a, $b)
	{
		if (!isset($this->__sby[$a]) && !isset($this->__sby[$b]) || isset($this->__sby[$a]) && isset($this->__sby[$b]) && $this->__sby[$a] == $this->__sby[$b])
		{
			return 0;
		}
		elseif (!isset($this->__sby[$b]))
		{
			return $this->__sby[$a] > 0 ? 1 : 0;
		}
		elseif (!isset($this->__sby[$a]))
		{
			return 0 > $this->__sby[$b] ? 1 : 0;
		}
		return $this->__sby[$a] > $this->__sby[$b] ? 1 : 0;
	}

	function get_folders_as_object_list($object, $level, $parent_o)
	{
		$this->tree_ob = $object;

		if (is_oid($object->prop("inherit_view_props_from")) && $this->can("view", $object->prop("inherit_view_props_from")))
		{
			$ih_ob = obj($object->prop("inherit_view_props_from"));
		}
		else
		{
			$ih_ob = $object;
		}
		$this->tree_ob_ih = $ih_ob;

		$ol = new object_list();

		$d_o = obj($this->tree_ob_ih->prop("ds"));
		$d_inst = $d_o->instance();

		$folders = $d_inst->get_folders($d_o);
		foreach($folders as $fld)
		{
			$i_o = obj($fld["id"]);

			if ($level == 0)
			{
				$parent = 0;
				$found = false;
				foreach($folders as $fp)
				{
					if ($fp["id"] == $i_o->parent())
					{
						$found = true;
					}
				}
				if ($found)
				{
					$parent = $i_o->parent();
				}

				if ($parent == 0)
				{
					$ol->add($fld["id"]);
				}
			}
			else
			{
				if ($parent_o->id() == $i_o->parent())
				{
					$ol->add($fld["id"]);
				}
			}
		}

		return $ol;
	}

	function make_menu_link($sect_obj, $ref = NULL)
	{
		if ($ref)
		{
			$link = $this->mk_my_orb("show", array("id" => $ref->id(), "tv_sel" => $sect_obj->id(), "section" => $sect_obj->id()));;
		}
		else
		{
			$link = $this->mk_my_orb("show", array("id" => $this->tree_ob->id(), "tv_sel" => $sect_obj->id(), "section" => $sect_obj->id()));;
		}
		return $link;
	}

	function get_yah_link($tree, $cur_menu)
	{
		return $this->mk_my_orb("show", array("id" => $tree, "tv_sel" => $cur_menu->id(), "section" => $cur_menu->id()));
	}

	function do_sortbl(&$arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_sortbl($t);

		$ob = $arr["obj_inst"];
		if (is_oid($ob->prop("inherit_view_props_from")) && $this->can("view", $ob->prop("inherit_view_props_from")))
		{
			$ih_ob = obj($ob->prop("inherit_view_props_from"));
		}
		else
		{
			$ih_ob = $ob;
		}

		$cols = $this->_get_col_list(array(
			"o" => $ih_ob,
			"hidden_cols" => ($ih_ob->prop("show_hidden_cols") == 1) ? true : false,
		));
//		$tmp = $arr["obj_inst"]->meta("sel_columns");
		$elements = array_merge(array("" => "") + $cols);
//		foreach($cols as $colid => $coln)
//		{
//			$elements[$colid] = $coln;
//		}
//		$elements = array_merge($elements + $cols)
//		arr($arr['obj_inst']->prop("only_visible_cols"));
/*
		foreach($cols as $colid => $coln)
		{
			if($arr['obj_inst']->prop("show_hidden_cols") == 1)
			{
				$elements[$colid] = $coln;
			}
			else
			{
				if (1 == $tmp[$colid])
				{
					$elements[$colid] = $coln;
				}
			}
		}

*/
		$maxi = 0;
		$is = new aw_array($arr["obj_inst"]->meta("itemsorts"));
		foreach($is->get() as $idx => $sd)
		{
			$t->define_data(array(
				"sby" => html::select(array(
					"options" => $elements,
					"selected" => $sd["element"],
					"name" => "itemsorts[$idx][element]"
				)),
				"sby_ord" => html::select(array(
					"options" => array("asc" => t("Kasvav"), "desc" => t("Kahanev")),
					"selected" => $sd["ord"],
					"name" => "itemsorts[$idx][ord]"
				)),
				"is_date" => html::checkbox(array(
					"name" => "itemsorts[$idx][is_date]",
					"value" => 1,
					"checked" => ($sd["is_date"] == 1)
				))
			));
			$maxi = max($maxi, $idx);
		}
		$maxi++;

		$t->define_data(array(
			"sby" => html::select(array(
				"options" => $elements,
				"selected" => "",
				"name" => "itemsorts[$maxi][element]"
			)),
			"sby_ord" => html::select(array(
				"options" => array("asc" => t("Kasvav"), "desc" => t("Kahanev")),
				"selected" => "",
				"name" => "itemsorts[$maxi][ord]"
			))
		));

		$t->set_sortable(false);
	}

	function do_save_sortbl(&$arr)
	{
		$awa = new aw_array($arr["request"]["itemsorts"]);
		$res = array();
		foreach($awa->get() as $idx => $dat)
		{
			if ($dat["element"])
			{
				$res[] = $dat;
			}
		}

		$arr["obj_inst"]->set_meta("itemsorts", $res);
	}

	function _init_sortbl($t)
	{
		$t->define_field(array(
			"name" => "sby",
			"caption" => t("Sorditav v&auml;li"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "sby_ord",
			"caption" => t("Kasvav / kahanev"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "is_date",
			"caption" => t("Kuup&auml;ev tekstina?"),
			"align" => "center"
		));
	}

	function __is_sorter($a, $b)
	{
		$comp_a = NULL;
		$comp_b = NULL;
		// find the first non-matching element
		foreach($this->__is as $isd)
		{
			if ($isd["element"] == "modified")
			{
				$isd["element"] = "mod_date";
			}
			if ($isd["element"] == "created")
			{
				$isd["element"] = "add_date";
			}
			$comp_a = isset($a[$isd["element"]]) ? $a[$isd["element"]] : null;
			$comp_b = isset($b[$isd["element"]]) ? $b[$isd["element"]] : null;
			if (isset($isd["is_date"]) && 1 == $isd["is_date"])
			{
				list($d, $m,$y) = explode(".", $comp_a);
				if ($y > 0)
				{
					$comp_a = mktime(0,0,0, $m,$d, $y);
				}

				list($d, $m,$y) = explode(".", $comp_b);
				if ($y > 0)
				{
					$comp_b = mktime(0,0,0, $m,$d, $y);
				}
			}
			$ord = $isd["ord"];
			if ($comp_a != $comp_b)
			{
				break;
			}
		}

		// sort by that element
		if ($comp_a  == $comp_b)
		{
			return 0;
		}

		if ($ord == "asc")
		{
			return $comp_a > $comp_b ? 1 : -1;
		}
		else
		{
			return $comp_a > $comp_b ? -1 : 1;
		}
	}

	function _init_filter_table($t)
	{
		$t->define_field(array(
			"name" => "filter_group",
			"caption" => t("Grupp"),
			"align" => "center",
		));

		$t->define_field(array(
			"name" => "filter_field",
			"caption" => t("Filtreeritav v&auml;li"),
			"align" => "center",
		));

		$t->define_field(array(
			"name" => "filter_value",
			"caption" => t("Filtreeritav v&auml;&auml;rtus"),
			"align" => "center",
		));

		$t->define_field(array(
			"name" => "filter_not",
			"caption" => t("V&auml;listav"),
			"align" => "center",
		));

		$t->define_field(array(
			"name" => "filter_strict",
			"caption" => t("Kas t&auml;pne?"),
			"align" => "center",
		));

	}

	function do_filter_table(&$arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_filter_table($t);

		$ob = $arr["obj_inst"];
		if (is_oid($ob->prop("inherit_view_props_from")) && $this->can("view", $ob->prop("inherit_view_props_from")))
		{
			$ih_ob = obj($ob->prop("inherit_view_props_from"));
		}
		else
		{
			$ih_ob = $ob;
		}

		$all_cols = $this->_get_col_list(array(
			"o" => $ih_ob,
			"hidden_cols" => ($ih_ob->prop("show_hidden_cols") == 1) ? true : false,
		));
//		$tmp = $arr["obj_inst"]->meta("sel_columns");
		$cols = array_merge(array("" => "","parent" => t("Asukoht")) + $all_cols);
		$saved_filters = new aw_array($arr['obj_inst']->meta("saved_filters"));

		$max_id = 0;
		$max_grp = 0;
		foreach($saved_filters->get() as $id => $filter_data)
		{
			$t->define_data(array(
				"filter_group" => html::textbox(array(
						"name" => "filters[".$id."][group]",
						"size" => 5,
						"value" => $filter_data['group'],
				)),
				"filter_field" => html::select(array(
						"name" => "filters[".$id."][field]",
						"options" => $cols,
						"selected" => $filter_data['field'],
				)),
				"filter_value" => html::textbox(array(
						"name" => "filters[".$id."][value]",
						"value" => $filter_data['value'],
				)),
				"filter_not" => html::checkbox(array(
						"name" => "filters[".$id."][is_not]",
						"value" => 1,
						"checked" => ($filter_data['is_not'] == 1) ? true : false,
				)),
				"filter_strict" => html::checkbox(array(
						"name" => "filters[".$id."][is_strict]",
						"value" => 1,
						"checked" => ($filter_data['is_strict'] == 1) ? true : false,
				)),
			));

			$max_id = max($max_id, $id);
			$max_grp = max($max_grp, $filter_data["group"]);
		}
		$max_id++;

		$t->define_data(array(
			"filter_group" => html::textbox(array(
					"name" => "filters[".$max_id."][group]",
					"size" => 5,
					"value" => $max_grp+1,
				)),
			"filter_field" => html::select(array(
					"name" => "filters[".$max_id."][field]",
					"options" => $cols,
					"selected" => "",
				)),
			"filter_value" => html::textbox(array(
					"name" => "filters[".$max_id."][value]",
					"value" => "",
				)),
		));

		$t->set_sortable(false);

	}

	function do_save_filter_table(&$arr)
	{
		$saved_filters = new aw_array($arr['request']['filters']);
		$valid_filters = array();
		foreach($saved_filters->get() as $filter_key => $filter_value)
		{
			if($filter_value['field'])
			{
				array_push($valid_filters, $filter_value);
			}
		}
		$arr['obj_inst']->set_meta("saved_filters", $valid_filters);
	}

	function do_pageselector(&$list, $per_page)
	{
		$page = isset($GLOBALS["page"]) ? (int)$GLOBALS["page"] : 0;
		$start = $page * $per_page;
		$end = ($page + 1) * $per_page;
		$cnt = 0;

		$num = count($list);
		$num_p = $num / $per_page;

		$tmp = array();
		foreach($list as $k => $v)
		{
			if (($cnt >= $start && $cnt < $end))
			{
				//unset($list[$k]);
				$tmp[$k] = $v;
			}
			$cnt++;
		}
		$list = $tmp;

		$ps = "";
		for ($i = 0; $i <  $num_p; $i++)
		{
			$this->vars(array(
				"url" => aw_url_change_var("page", $i),
				"page" => ($i * $per_page)." - ".min($num, ((($i+1) * $per_page)-1))
			));
			if ($i == $page)
			{
				$ps .= $this->parse("PAGE_SEL");
			}
			else
			{
				$ps .= $this->parse("PAGE");
			}
		}

		$this->vars(array(
			"PAGE_SEL" => "",
			"PAGE" => $ps
		));
	}

	/**

		@attrib name=submit_show params=name default="0"

		@param id required type=int acl=view
		@param subact required
		@param sel optional
		@param return_url required

		@returns


		@comment

	**/
	function submit_show($arr)
	{
		extract($arr);

		$ob = obj($id);

		if (is_oid($ob->prop("inherit_view_props_from")) && $this->can("view", $ob->prop("inherit_view_props_from")))
		{
			$ih_ob = obj($ob->prop("inherit_view_props_from"));
		}
		else
		{
			$ih_ob = $ob;
		}


		$d_o = obj($ih_ob->prop("ds"));
		$d_inst = $d_o->instance();

		if ($subact == "delete")
		{
			$tt = array();
			$awa = new aw_array($sel);
			$farr = $awa->get();

			// get datasource
			$d_inst->do_delete_objects($d_o, $farr);
		}

		// if has editable columns, save them
		if ($arr["edit_mode"] > 0)
		{
			$objs = safe_array($arr["objs"]);
			$ef = safe_array($ih_ob->meta("sel_columns_editable"));

			$fld = $d_inst->get_folders($d_o);
			$ol = $d_inst->get_objects($d_o, $fld, $arr["tv_sel"]);

			foreach($ol as $oid => $o)
			{
				if ($d_inst->check_acl("edit", $d_o, $oid))
				{
					$d_inst->update_object($ef, $oid, $objs[$oid]);
				}
			}
		}

		return $return_url;
	}

	function _get_link($name, $url, $pfk, $newwin = false)
	{
		$ld = array(
			"url" => $url,
			"caption" => $name,
		);
		if ($pfk->prop("show_link_new_win") || $newwin)
		{
			$ld["target"] = "_blank";
		}

		if ($url == "")
		{
			$_name = $name;
		}
		else
		{
			$_name = html::href($ld);
		}
		return $_name;
	}

// "ol" => object list which should be filtered
// "otv_obj" => otv object
	function filter_data($arr)
	{
		$ol = $arr['ol'];
		$ob = $arr['otv_obj'];
		$ih_ob = $arr['otv_obj_ih'];
		$d_o = $arr['ds_obj'];
		$fld = $arr['folders'];
		$filters = $ob->meta("saved_filters");

		// filtering is taking place if one of the following conditions are present:
		// -> $filter variable is array and its element count is bigger than zero
		// -> $_GET['tv_sel'] is not empty
		// -> $_GET['char'] is not empty
		if ((is_array($filters) && count($filters) > 0) || !empty($_GET['tv_sel']) || !empty($_GET['char']))
		{

//			$ol_result = array();
			foreach($ol as $ol_key => $ol_value)
			{
				foreach(safe_array($filters) as $filter)
				{
					if($filter['is_strict'] == 1)
					{
						if($ol_value[$filter['field']] != $filter['value'])
						{
							unset($ol[$ol_key]);
							break;
						}
					}
					else
					{
						if(strpos(strtolower($ol_value[$filter['field']]), strtolower($filter['value'])) === false)
						{
							unset($ol[$ol_key]);
							break;
						}
					}
				}

				// if meta data fields are used as folders, then i need to do
				// some filtering according to $_GET['tv_sel']

				if(($d_o->prop("use_meta_as_folders") == 1) && empty($_GET['char']))
				{

					if(!empty($_GET['tv_sel']) && ($fld[$_GET['tv_sel']]['name'] != $ol_value[$ob->prop("group_by_folder")]))
					{
						unset($ol[$ol_key]);
					}
				}
				// if there is char param set in the url, then filter objects by this fields value which is set by
				// filter_by_char_field property
				if(!empty($_GET['char']))
				{
					$f = strtolower($ol_value[$ih_ob->meta("filter_by_char_field")]);
					if((strlen($_GET['char']) == 1) && ($f{0} != strtolower($_GET['char'])))
					{
						unset($ol[$ol_key]);
					}
				}
			}

			return $ol;
		}

		return $ol;
	}

	function callback_mod_tab($arr)
	{
		if ($arr["id"] == "columns" || $arr["id"] == "styles" || $arr["id"] == "inherit")
		{
			if (is_oid($arr["obj_inst"]->prop("inherit_view_props_from")))
			{
				return false;
			}
		}

		if ($arr["id"] == "search")
		{
			if (!is_oid($arr["obj_inst"]->prop("search")) || !$this->can("view", $arr["obj_inst"]->prop("search")))
			{
				return false;
			}
		}
		return true;
	}

	function _mk_anch($txt)
	{
		return str_replace(" ", "_", $txt);
	}

	function search_gen_els($arr)
	{
		$srch = obj($arr["obj_inst"]->prop("search"));
		$srch_i = $srch->instance();
		return $srch_i->search_gen_els(array(
			"obj_inst" => $srch,
			"request" => $arr["request"]
		));
	}

	function _search_res($arr)
	{
		$srch = obj($arr["obj_inst"]->prop("search"));
		$srch_i = $srch->instance();
		$srch_i->_search_res(array(
			"obj_inst" => $srch,
			"request" => $arr["request"],
			"prop" => array(
				"vcl_inst" => &$arr["prop"]["vcl_inst"]
			)
		));
	}

	function _init_columns_modify_t($t)
	{
		$t->define_field(array(
			"name" => "col",
			"caption" => t("Tulp"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "tr",
			"caption" => t("Muundaja"),
			"align" => "center"
		));
	}

	function _columns_modify($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_columns_modify_t($t);

		$cols = $this->_get_col_list(array(
			"o" => $arr["obj_inst"]
		));

		$trs = new object_list($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_TRANSFORM")));
		$trs = $trs->names();
		$trs = array("" => "") + $trs;

		$tr_sets = $arr["obj_inst"]->meta("transform_cols");

		foreach($cols as $coln => $colstr)
		{
			$t->define_data(array(
				"col" => $colstr,
				"tr" => html::select(array(
					"name" => "transform_cols[$coln]",
					"multiple" => 1,
					"value" => $tr_sets[$coln],
					"options" => $trs
				))
			));
		}
	}
}
