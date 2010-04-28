<?php

namespace automatweb;

/*

@classinfo syslog_type=ST_LAYOUT relationmgr=yes maintainer=kristo

@groupinfo settings caption=M&auml;&auml;rangud
@groupinfo layout caption=Tabel
@groupinfo styles caption=Stiilid
@groupinfo aliases caption="Tabeli sisu"
@groupinfo hfoot caption="P&auml;is ja jalus"
@groupinfo import caption=Import
@groupinfo preview caption=Eelvaade

@default table=objects
@default field=meta
@default method=serialize

@property rows type=textbox group=general size=3 store=no
@caption Ridu

@property columns type=textbox group=general size=3 store=no
@caption Tulpi

@property cell_style_folders type=relpicker reltype=RELTYPE_CELLSTYLE_FOLDER group=settings multiple=1
@caption Stiilide kataloogid

@property grid type=callback group=layout 
@caption Tabel

@property table_style type=select group=styles
@caption Vali vaikimisi tabeli stiil

@property sel_style type=select store=no group=styles
@caption Vali elemendi stiil

@property grid_styles type=callback group=styles 
@caption Vali element

@property row_widths type=callback callback=get_row_widths group=settings store=no
@caption Ridade laiused

@property row_heights type=callback callback=get_row_heights group=settings store=no
@caption Ridade k&otilde;rgused

@property grid_aliases type=callback group=aliases 
@caption Aliased

@property grid_aliases_list type=aliasmgr group=aliases store=no
@caption Aliaste manager

@property grid_preview type=callback group=preview 
@caption Eelvaade

@property import_file type=fileupload group=import 
@caption Uploadi .csv fail

@property import_remove_empty type=checkbox ch_value=1 group=import 
@caption Kas eemaldame t&uuml;hjad read l&otilde;pust

@property import_sep type=textbox size=1 group=import 
@caption Mis m&auml;rgiga on tulbad eraldatud?

@property show_in_folders type=relpicker reltype=RELTYPE_SHOW_FOLDER multiple=1 rel=1 group=general
@caption Millistes kataloogides n&auml;idatakse

@property header type=textarea rows=10 cols=50 field=meta method=serialize group=hfoot
@caption P&auml;is

@property footer type=textarea rows=10 cols=50 field=meta method=serialize group=hfoot
@caption Jalus

@reltype CELLSTYLE_FOLDER value=1 clid=CL_MENU
@caption celli stiilide kataloog

@reltype SHOW_FOLDER value=2 clid=CL_MENU
@caption näita selles kataloogis

@classinfo no_status=1
			

*/

class layout extends class_base
{
	const AW_CLID = 178;

	function layout()
	{
		$this->init(array(
			'tpldir' => 'grid_editor',
			'clid' => CL_LAYOUT
		));
	}

	////
	// !this will be called if the object is put in a document by an alias and the document is being shown
	// parameters
	//    alias - array of alias data, the important bit is $alias[target] which is the id of the object to show
	function parse_alias($args)
	{
		extract($args);

		// if oid is in the arguments check whether that object is attached to
		// this document and display it instead of document
		$oid = aw_global_get("oid");
		if (is_oid($oid) && $this->can("view", $oid))
		{
			$obj = obj($oid);
			foreach($obj->connections_to(array("from" => $alias["target"])) as $c)
			{
				if ($c->prop("from.class_id") == CL_FILE)
				{
					$fi = new file();
					$fl = $fi->get_file_by_id($c->prop("to"));
					return $fl["content"];
				}
			}
		}

		$ob = obj($alias["target"]);
		$ge = get_instance("vcl/grid_editor");
		$grid = $ob->meta('grid');
		$grid['table_style'] = $ob->meta('table_style');

		$tmp = $ge->show($grid, $alias["target"], &$tpls);
		//$tmp = str_replace("\n", "<br/>", $tmp);

		$al = get_instance("alias_parser");
		if ($ob->prop("header") != "")
		{
			$h_tmp = create_email_links(nl2br($ob->prop("header")));
			$al->parse_oo_aliases($ob->id(), &$h_tmp);
			if ($grid["table_style"])
			{
				$st = get_instance(CL_STYLE);
				$h_tmp = $st->apply_style_to_text($grid["table_style"], $h_tmp, array("is_header" => true));
			}
			$tmp = $h_tmp.$tmp;
		}

		if ($ob->prop("footer") != "")
		{
			$h_tmp = create_email_links(nl2br($ob->prop("footer")));
			$al->parse_oo_aliases($ob->id(), &$h_tmp);
			if ($grid["table_style"])
			{
				$st = get_instance(CL_STYLE);
				$h_tmp = $st->apply_style_to_text($grid["table_style"], $h_tmp, array("is_footer" => true));
			}
			$tmp .= $h_tmp;
		}

		$d = get_instance(CL_DOCUMENT);
		$d->create_relative_links($tmp);
		if (strpos($tmp, "<a") !== false || strpos($tmp, "< a") !== false || strpos($tmp, "<A") !== false)
		{
			return $tmp;
		}
		return create_email_links($tmp);
	}

	////
	// !this shows the object. not strictly necessary, but you'll probably need it, it is used by parse_alias
	function show($arr)
	{
		extract($arr);
		$ob = obj($id);
		$ge = get_instance("vcl/grid_editor");
		return create_email_links($ge->show($ob->meta('grid'), $id));
	}

	function get_property(&$arr)
	{
		$prop = &$arr['prop'];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "grid":
				$ge = get_instance("vcl/grid_editor");
				$prop['value'] = $ge->on_edit($arr['obj_inst']->meta('grid'), $arr['obj_inst']->id());
				break;

			case "grid_aliases":
				$ge = get_instance("vcl/grid_editor");
				$prop['value'] = $ge->on_aliases_edit($arr['obj_inst']->meta('grid'), $arr['obj_inst']->id());
				break;

			case "sel_style":
				$prop["options"] = $this->get_pickable_styles($arr["obj_inst"]->id());
				break;

			case "grid_styles":
				$ge = get_instance("vcl/grid_editor");
				$grid = $arr['obj_inst']->meta('grid');
				$grid["table_style"] = $arr["obj_inst"]->meta("table_style");
				$prop['value'] = $ge->on_styles_edit(
					$grid, 
					$arr['obj_inst']->id() 
				);
				break;

			case "grid_preview":
				$ge = get_instance("vcl/grid_editor");
				$grid = $arr['obj_inst']->meta('grid');
				$grid["table_style"] = $arr["obj_inst"]->meta("table_style");
				$tmp = $ge->show($grid, $arr['obj_inst']->id());
				if ($arr['obj_inst']->prop("header") != "")
				{
					$tmp = nl2br($arr['obj_inst']->prop("header")).$tmp;
				}
				if ($arr['obj_inst']->prop("footer") != "")
				{
					$tmp .= nl2br($arr['obj_inst']->prop("footer"));
				}
				$d = get_instance(CL_DOCUMENT);
				$d->create_relative_links($tmp);
				if (strpos($tmp, "<a") !== false)
				{
					$prop["value"] = $tmp;
				}
				else
				{
					$prop["value"] = create_email_links($tmp);
				}

				break;

			case "table_style":
				$st = get_instance(CL_STYLE);
				$prop['options'] = $st->get_table_style_picker();
				break;

			case "rows":
				$ge = get_instance("vcl/grid_editor");
				$ge->_init_table($arr['obj_inst']->meta('grid'));
				$prop['value'] = $ge->get_num_rows();
				break;

			case "columns":
				$ge = get_instance("vcl/grid_editor");
				$ge->_init_table($arr['obj_inst']->meta('grid'));
				$prop['value'] = $ge->get_num_cols();
				break;
		}
		return $retval;
	}

	function set_property(&$arr)
	{
		$prop = &$arr['prop'];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "grid":
				$ge = get_instance("vcl/grid_editor");
				$prop['value'] = $ge->on_edit_submit($arr['obj_inst']->meta('grid'), $arr['request']);
				break;

			case "grid_aliases":
				$ge = get_instance("vcl/grid_editor");
				$arr['obj_inst']->set_meta("grid",$ge->on_aliases_edit_submit($arr['obj_inst']->meta('grid'), $arr['request']));
				break;

			case "grid_styles":
				$this->submit_styles($arr["obj_inst"], $arr["request"]);
				break;

			case "rows":
				$ge = get_instance("vcl/grid_editor");
				$ge->_init_table($arr['obj_inst']->meta('grid'));
				$ge->set_num_rows($arr["request"]["rows"]);
				$ge->set_num_cols($arr["request"]["columns"]);
				$arr["obj_inst"]->set_meta("grid",$ge->_get_table());
				break;

			case "columns":
				$ge = get_instance("vcl/grid_editor");
				$ge->_init_table($arr['obj_inst']->meta('grid'));
				$ge->set_num_cols($arr["request"]["columns"]);
				$ge->set_num_rows($arr["request"]["rows"]);
				$arr["obj_inst"]->set_meta("grid",$ge->_get_table());
				break;

			case "import_file":
				$import_file = $_FILES["import_file"]["tmp_name"];
				if (is_uploaded_file($import_file))
				{
					$ge = get_instance("vcl/grid_editor");
					$arr["obj_inst"]->set_meta("grid",$ge->do_import(array(
						"sep" => $arr["request"]["import_sep"],
						"remove_empty" => $arr["request"]["import_remove_empty"],
						"file" => $import_file
					)));
				}
				break;

			case "row_widths":
				$ge = get_instance("vcl/grid_editor");
				$ge->_init_table($arr["obj_inst"]->meta("grid"));
				for($i = 0; $i < $ge->get_num_cols(); $i++)
				{
					$ge->set_col_width($i, $arr["request"]["colw"][$i]);
				}
				$arr['obj_inst']->set_meta('grid',$ge->_get_table());
				break;

			case "row_heights":
				$ge = get_instance("vcl/grid_editor");
				$ge->_init_table($arr['obj_inst']->meta('grid'));
				for($i = 0; $i < $ge->get_num_cols(); $i++)
				{
					$ge->set_col_height($i, $arr["request"]["colh"][$i]);
				}
				$arr['obj_inst']->set_meta('grid',$ge->_get_table());
				break;

			case "import_sep":
				if (trim($prop["value"]) === "")
				{
					$prop["error"] = "Eraldaja peab olema sisestatud!";
					return PROP_ERROR;
				}
				break;
		}
		return $retval;
	}

	function _do_import($arr)
	{
		extract($arr);

	}

	function submit_styles($obj, $request)
	{
		$ge = get_instance("vcl/grid_editor");
		$ge->_init_table($obj->meta('grid'));

		// now we need to figure out where to apply the style
		foreach($request as $r_k => $r_v)
		{
			if ($r_v == "")
			{
				continue;
			}

			if (substr($r_k, 0, 3) == "dr_")
			{
				$ge->set_row_style(substr($r_k, 3), $request["sel_style"]);
			}
			if (substr($r_k, 0, 3) == "dc_")
			{
				$ge->set_col_style(substr($r_k, 3), $request["sel_style"]);
			}

			if (substr($r_k, 0, 8) == "sel_row=")
			{
				if (preg_match("/sel_row=(\d+);col=(\d+)/ims", $r_k, $mt))
				{
					$ge->set_cell_style($mt[1], $mt[2], $request["sel_style"]);
				}
			}
		}

		$obj->set_meta("grid", $ge->_get_table());
	}

	////
	// !returns the layout data that can be fed to grid editor. useful when you can select a default layout
	function get_layout($oid)
	{
		if (!is_oid($oid))
		{
			return array();
		}
		$ob = new object($oid);
		return $ob->meta("grid");
	}

	function get_row_widths($arr)
	{
		$ret = array();
		$ge = get_instance("vcl/grid_editor");
		$ge->_init_table($arr['obj_inst']->meta('grid'));
		for($i = 0; $i < $ge->get_num_cols(); $i++)
		{
			$ret["colw[$i]"] = array(
				"name" => "colw[$i]",
				"type" => "textbox",
				"size" => 6,
				"group" => "settings",
				"table" => "objects",
				"field" => "meta",
				"method" => "serialize",
				"caption" => sprintf(t("%d tulba laius"), ($i+1)),
				"value" => $ge->get_col_width($i)
			);
		}
		return $ret;
	}
	
	function get_row_heights($arr)
	{
		$ret = array();
		$ge = get_instance("vcl/grid_editor");
		$ge->_init_table($arr['obj_inst']->meta('grid'));
		for($i = 0; $i < $ge->get_num_cols(); $i++)
		{
			$ret["colh[$i]"] = array(
				"name" => "colh[$i]",
				"type" => "textbox",
				"size" => 6,
				"group" => "settings",
				"table" => "objects",
				"field" => "meta",
				"method" => "serialize",
				"caption" => sprintf(t("%d tulba k&otilde;rgus"), ($i+1)),
				"value" => $ge->get_col_height($i)
			);
		}
		return $ret;
	}

	function get_pickable_styles($oid)
	{
		$ob = obj($oid);

		// make style pick list
		// folders:
		$folders = new aw_array($ob->meta('cell_style_folders'));

		$ol = new object_list(array(
			"parent" => $folders,
			"class_id" => CL_CSS,
			"lang_id" => array(),
			"sort_by" => "objects.site_id,objects.jrk,objects.name"
		));
		$ret = array("" => "");
		$sl = get_instance("install/site_list");
		foreach($ol->arr() as $o)
		{
			// did something just change? because this object_list returns objets
                        // where parent == 0.
                        if (0 == $o->parent())
                        {
                                continue;
                        };
			$pt = obj($o->parent());
			$ret[$o->id()] = $sl->get_url_for_site($o->site_id()).": ".$pt->name()." / ".$o->name();
		}
		$ol = new object_list(array(
			"parent" => $folders,
			"class_id" => CL_CSS,
			"lang_id" => array(),
			"site_id" => new obj_predicate_not(aw_ini_get("site_id")),
			"sort_by" => "objects.site_id,objects.jrk,objects.name"
		));
		$sl = get_instance("install/site_list");
		foreach($ol->arr() as $o)
		{
			$pt = obj($o->parent());
			$ret[$o->id()] = $sl->get_url_for_site($o->site_id()).": ".$pt->name()." / ".$o->name();
		}
		return $ret;
	}

	function _unserialize($args)
	{
		$raw = isset($args["raw"]) ? $args["raw"] : aw_unserialize($args["str"]);
		$o = obj();
		$o->set_parent($args["parent"]);
		$o->set_period((int)$args["period"]);
		$o->set_class_id(CL_LAYOUT);
	
		foreach(safe_array($raw) as $k => $v)
		{
			if ($o->is_property($k))
			{
				$o->set_prop($k, $v);
			}
		}
		$o->save();

		foreach(safe_array($raw["connections"]) as $con)
		{
			$o->connect(array(
				"to" => $con["to"],
				"reltype" => $con["reltype"]
			));
		}

		return $o->id();
	}
}
?>
