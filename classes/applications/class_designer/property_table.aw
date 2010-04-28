<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/class_designer/property_table.aw,v 1.10 2007/12/06 14:33:03 kristo Exp $
// property_table.aw - Tabel 
/*

@classinfo syslog_type=ST_PROPERTY_TABLE relationmgr=yes no_comment=1 no_status=1 maintainer=kristo

@default table=objects
@default field=meta
@default method=serialize

@default group=general
	@property demo_data_source type=select
	@caption Demo andmete allikas

	@property demo_data_source_rel type=select
	@caption Seose t&uuml;&uuml;p

@default group=designer

	@property design_table type=table no_caption=1
	@caption Veerud

@default group=data

	@property demo_data_table type=table no_caption=1
	@caption Demo andmed

@default group=data_rels

	@property demo_rels_data_table type=table no_caption=1
	@caption Demo seoste andmed

@default group=settings

	@property default_sort_col type=select
	@caption Default sortimine

	@property vertical_group_t type=table
	@caption Vertikaalne grupeerimine

@groupinfo designer caption="Tulbad"
@groupinfo data caption="Demo andmed"
@groupinfo data_rels caption="Demo andmed"
@groupinfo settings caption="Seaded"

*/

class property_table extends class_base
{
	const AW_CLID = 886;

	function property_table()
	{
		$this->init(array(
			"clid" => CL_PROPERTY_TABLE
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "design_table":
				$this->generate_design_table(&$arr);
				break;

			case "demo_data_table":
				$this->generate_demo_data_table(&$arr);
				break;

			case "demo_rels_data_table":
				$this->generate_demo_rels_data_table(&$arr);
				break;

			case "demo_data_source":
				$prop["options"] = array(
					"user" => t("Kasutaja poolt sisestatud andmed"),
					"rels" => t("Seostatud objektid")
				);
				break;

			case "demo_data_source_rel":
				if ($arr["obj_inst"]->prop("demo_data_source") != "rels")
				{
					return PROP_IGNORE;
				}
				$do = $this->_get_designer($arr["obj_inst"]);
				$ol = new object_list($do->connections_from(array("type" => "RELTYPE_RELATION")));
				$prop["options"] = array("" => t("--vali--")) + $ol->names();
				break;
	
			case "default_sort_col":
				$ol = new object_list(array(
					"parent" => $arr["obj_inst"]->id(),
					"class_id" => CL_PROPERTY_TABLE_COLUMN
				));
				$prop["options"] = array("" => "") + $ol->names();
				break;

			case "vertical_group_t":
				$this->_vertical_group_t($arr);
				break;
		};
		return $retval;
	}

	function _get_designer($o)
	{
		$pt = $o->path();
		foreach($pt as $p)
		{
			if ($p->class_id() == CL_CLASS_DESIGNER)
			{
				return $p;
			}
		}
		return NULL;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "design_table":
				$this->update_columns($arr);
				break;

			case "demo_data_table":
				$this->save_demo_data_t($arr);
				break;

			case "demo_rels_data_table":
				$this->save_demo_rels_data_t($arr);
				break;

			case "vertical_group_t":
				$t = array();
				foreach($arr["request"]["vert_grp_cols"] as $cl)
				{
					if ($cl != "")
					{
						$t[] = $cl;
					}
				}
				$arr["obj_inst"]->set_meta("vert_grp_cols", $t);
				break;
		}
		return $retval;
	}	

	function generate_design_table($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "ord",
			"caption" => t("Jrk"),
			"align" => "center",
		));

		$t->define_field(array(
			"name" => "parent",
			"caption" => t("&Uuml;lemtulp"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "caption",
			"caption" => t("Pealkiri"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "width",
			"caption" => t("Laius"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "sortable",
			"caption" => t("Sorteeritav"),
			"align" => "center",
		));

		$t->define_field(array(
			"name" => "align",
			"caption" => t("Joondamine"),
			"align" => "center",
		));

		$t->set_sortable(false);

		$ol = new object_list(array(
			"parent" => $arr["obj_inst"]->id(),
			"class_id" => CL_PROPERTY_TABLE_COLUMN,
		));

		foreach($ol->arr() as $o)
		{
			$this->_add_row(array(
				"t" => &$t,
				"key" => $o->id(),
				"name" => $arr["prop"]["name"],
				"data" => $o->properties(),
				"ol" => $ol
			));
		};

		$this->_add_row(array(
			"t" => &$t,
			"key" => "new",
			"name" => $arr["prop"]["name"],
			"ol" => $ol
		));
	}


	function update_columns($arr)
	{
		$name = $arr["prop"]["name"];
		$coldata = $arr["request"][$name];
		foreach($coldata as $key => $coldat)
		{
			if ($key == "new" && strlen($coldat["caption"]) > 0)
			{
				$o = new object();
				$o->set_parent($arr["obj_inst"]->id());
				$o->set_class_id(CL_PROPERTY_TABLE_COLUMN);
				$o->set_status(STAT_ACTIVE);
			}
			elseif (!is_numeric($key))
			{
				continue;
			}
			else
			{
				$o = new object($key);
			};

			$o->set_name($coldat["caption"]);
			$o->set_prop("ord",$coldat["ord"]);
			$o->set_prop("width",$coldat["width"]);
			$o->set_prop("sortable",$coldat["sortable"]);
			$o->set_prop("align",$coldat["align"]);
			$o->set_prop("c_parent", $coldat["parent"]);

			$o->save();
		};
	}

	function _add_row($arr)
	{
		// needs t 
		// needs key
		// needs row values
		$t = &$arr["t"];
		$key = $arr["key"];
		$name = $arr["name"];
		//arr($arr["data"]);
		$t->define_data(array(
			"ord" => html::textbox(array(
				"name" => "${name}[${key}][ord]",
				"size" => 2,
				"value" => $arr["data"]["ord"],
			)),
			"parent" => html::select(array(
				"name" => "${name}[${key}][parent]",
				"value" => $arr["data"]["c_parent"],
				"options" => array("" => "") + $arr["ol"]->names()
			)),
			"width" => html::textbox(array(
				"name" => "${name}[${key}][width]",
				"size" => 4,
				"value" => $arr["data"]["width"],
			)),
			"caption" => html::textbox(array(
				"name" => "${name}[${key}][caption]",
				"size" => 30,
				"value" => $arr["data"]["name"],

			)),
			"sortable" => html::checkbox(array(
				"name" => "${name}[${key}][sortable]",
				"ch_value" => 1,
				"checked" => ($arr["data"]["sortable"] == 1),
			)),
			"align" => html::select(array(
				"name" => "${name}[${key}][align]",
				"options" => array("" => "", "left" => "Vasakul", "center" => "Keskel", "right" => "Paremal"),
				"selected" => $arr["data"]["align"],
			))
		));
	}

	function generate_demo_data_table($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		$t->set_sortable(false);

		$ol = new object_list(array(
			"parent" => $arr["obj_inst"]->id(),
			"class_id" => CL_PROPERTY_TABLE_COLUMN,
			"sort_by" => "jrk"
		));

		foreach($ol->arr() as $o)
		{
			$dd = array(
				"name" => $o->name(),
				"caption" => $o->name(),
				"sortable" => $o->prop("sortable"),
				"width" => $o->prop("width"),
				"nowrap" => $o->prop("nowrap"),
				"align" => $o->prop("align"),
			);
			if ($o->prop("c_parent"))
			{
				$dd["parent"] = $o->prop_str("c_parent");
			}
			$t->define_field($dd);
		}

		$dd = safe_array($arr["obj_inst"]->meta("demo_data"));
		$dd[] = array();

		$nr = 1;
		foreach($dd as $row)
		{
			$row_def = array();
			foreach($ol->arr() as $o)
			{
				$row_def[$o->prop("name")] = html::textbox(array(
					"name" => "dd[$nr][".$o->prop("name")."]",
					"value" => $dd[$nr][$o->prop("name")],
				));
			}
			$nr++;
			$t->define_data($row_def);
		}
	}

	function save_demo_data_t($arr)
	{
		$dd = array();

		$nr = 1;
		foreach(safe_array($arr["request"]["dd"]) as $nr => $row)
		{
			$data = array();
			foreach(safe_array($row) as $k => $v)
			{
				if (trim($v) != "")
				{
					$data[$k] = $v;
				}
			}

			if (count($data))
			{
				$dd[$nr] = $data;
			}
			$nr++;
		}

		$arr["obj_inst"]->set_meta("demo_data", $dd);
	}

	function generate_get_property($arr)
	{
		$el = new object($arr["id"]);
		$sys_name = $arr["name"];
		$gpblock = "";
		$gpblock .= "\t\t\tcase \"${sys_name}\":\n";
		$gpblock .= "\t\t\t\t\$this->generate_${sys_name}(\$arr);\n";
		$gpblock .= "\t\t\t\tbreak;\n\n";
		return array(
			"get_property" => $gpblock,
			"generate_methods" => array("generate_${sys_name}"),
		);
	}

	function generate_method($arr)
	{
		$name = $arr["name"];
		$obj = new object($arr["id"]);
		$els = new object_list(array(
			"parent" => $arr["id"],
			"class_id" => CL_PROPERTY_TABLE_COLUMN,
		));
		$els->sort_by(array(
			"prop" => "ord"
		));
		$rv = "\tfunction $name(\$arr)\n";
		$rv .= "\t{\n";
		$rv .= "\t\t" . '$t = &$arr["prop"]["vcl_inst"];' . "\n";
		foreach($els->arr() as $el)
		{
			$sys_name = strtolower(preg_replace("/\s/","_",$el->name()));
			$rv .= "\t\t" . '$t->define_field(array(' . "\n";
			$rv .= "\t\t\t" . '"name" => "' . $sys_name . '",' . "\n";
			$rv .= "\t\t\t\"caption\" => \"" . $el->name() . "\",\n";
			if ($el->prop("width"))
			{
				$rv .= "\t\t\t" . '"width" => "' . $el->prop("width") . '",' . "\n";
			}
			if ($el->prop("sortable"))
			{
				$rv .= "\t\t\t" . '"sortable" => "' . $el->prop("sortable") . '",' . "\n";
			}
			if ($el->prop("align") != "")
			{
				$rv .= "\t\t\t" . '"align" => "' . $el->prop("align") . '",' . "\n";
			}
			if ($el->prop("c_parent") != "")
			{
				$rv .= "\t\t\t" . '"parent" => "' . $el->prop_str("c_parent") . '",' . "\n";
			}
			$rv .= "\t\t" . '));' . "\n";
			
		};

		// generate data as well
		if ($obj->prop("demo_data_source") != "rels")
		{
			$dd = safe_array($obj->meta("demo_data"));
			foreach($dd as $row)
			{
				$rv .= "\t\t\$t->define_data(array(\n";
				foreach(safe_array($row) as $k => $v)
				{
					$k = strtolower(preg_replace("/\s/","_",$k));
					$rv .= "\t\t\t\"$k\" => \"$v\",\n";
				}
				$rv .= "\t\t));\n";
			}
		}
		else
		{
			// gen code that reads shit from rels
			if (is_oid($obj->prop("demo_data_source_rel")) &&  
				$this->can("view", $obj->prop("demo_data_source_rel")))
			{
				$rel = obj($obj->prop("demo_data_source_rel"));

				$rv .= "\t\tforeach(\$arr[\"obj_inst\"]->connections_from(array(\"type\" => \"RELTYPE_".strtoupper($rel->name())."\")) as \$c)\n";
				$rv .= "\t\t{\n";
				$rv .= "\t\t\t\$to = \$c->to();\n";
				$rv .= "\t\t\t\$t->define_data(array(\n";

				$col2prop = safe_array($obj->meta("col2prop"));
				foreach($col2prop as $col_oid => $prop)
				{
					if (!is_oid($col_oid) || !$this->can("view", $col_oid))
					{
						continue;
					}
	
					$colo = obj($col_oid);
					$k = strtolower(preg_replace("/\s/","_",$colo->name()));
					$rv .= "\t\t\t\t\"$k\" => \$to->prop_str(\"$prop\"),\n";
				}
			
				$rv .= "\t\t\t));\n";
				$rv .= "\t\t}\n";
			}
		}

		$vgsa = safe_array($obj->meta("vert_grp_cols"));
		$vgs = array();
		foreach($vgsa as $oid)
		{
			if (is_oid($oid) && $this->can("view", $oid))
			{
				$o = obj($oid);
				$k = strtolower(preg_replace("/\s/","_",$o->name()));
				$vgs[$k] = $k;
			}
		}

		if (is_oid($obj->prop("default_sort_col")) && $this->can("view", $obj->prop("default_sort_col")))
		{
			$dsc = obj($obj->prop("default_sort_col"));
			$k = strtolower(preg_replace("/\s/","_",$dsc->name()));
			$rv .= "\t\t\$t->set_default_sortby(\"$k\");\n";
			if (!(is_oid($obj->prop("vertical_group_col")) && $this->can("view", $obj->prop("vertical_group_col"))))
			{
				$rv .= "\t\t\$t->sort_by();\n";
			}
		}

		if (count($vgs))
		{
			$dsc = obj($obj->prop("vertical_group_col"));
			$k = strtolower(preg_replace("/\s/","_",$dsc->name()));
			$rv .= "\t\t\$t->sort_by(array(\n";
			$rv .= "\t\t\t\"vgroupby\" => array(\n";
			foreach($vgs as $vgc)
			{
				$rv .= "\t\t\t\t\"$vgc\" => \"$vgc\",\n";
			}
			$rv .= "\t\t\t)\n";
			$rv .= "\t\t));\n";
			$rv .= "\t\t\$t->set_sortable(false);\n";
		}

		$rv .= "\t}\n\n";
		return $rv;
	}

	function get_visualizer_prop($el, &$propdata)
	{
		$t = new vcl_table();
		$table_items = new object_list(array(
			"parent" => $el->id(),
			"class_id" => CL_PROPERTY_TABLE_COLUMN
		));
		$table_items->sort_by(array(
			"prop" => "ord"
		));
		foreach($table_items->arr() as $table_item)
		{
			$sortable = $table_item->prop("sortable");
			$celldata = array(
				"name" => $table_item->name(),
				"caption" => $table_item->name(),
				"width" => $table_item->prop("width"),
			);
			if ($table_item->prop("sortable"))
			{
				$celldata["sortable"] = 1;
			};
			if ($table_item->prop("c_parent"))
			{
				$celldata["parent"] = $table_item->prop_str("c_parent");
			};

			if ($table_item->prop("align") != "")
			{
				$celldata["align"] = $table_item->prop("align");
			};
			$t->define_field($celldata);
		};

		// get demo data
		$dd = safe_array($el->meta("demo_data"));
		foreach($dd as $row)
		{
			$t->define_data($row);
		}

		$vgsa = safe_array($el->meta("vert_grp_cols"));
		$vgs = array();
		foreach($vgsa as $oid)
		{
			if (is_oid($oid) && $this->can("view", $oid))
			{
				$o = obj($oid);
				$vgs[$o->name()] = $o->name();
			}
		}

		if (is_oid($el->prop("default_sort_col")) && $this->can("view", $el->prop("default_sort_col")))
		{
			$dsc = obj($el->prop("default_sort_col"));
			$k = $dsc->name();
			$t->set_default_sortby($k);
			if (count($vgs))
			{
				$t->sort_by();
			}
		}

		if (count($vgs))
		{
			$dsc = obj($el->prop("vertical_group_col"));
			$k = $dsc->name();

			$t->sort_by(array(
				"vgroupby" => $vgs
			));
			$t->set_sortable(false);
		}

		$propdata["vcl_inst"] = $t;
	}

	function callback_mod_tab($arr)
	{
		$tp = $arr["obj_inst"]->prop("demo_data_source");

		if ($arr["id"] == "data" && $tp == "rels")
		{
			return false;
		}
		if ($arr["id"] == "data_rels" && $tp != "rels")
		{
			return false;
		}
		return true;
	}

	function _init_demo_rels_data_t(&$t, $levels)
	{
		$t->define_field(array(
			"name" => "col",
			"caption" => t("Tulp"),
			"align" => "center",
			"sortable" => 1
		));

		/*$t->define_field(array(
			"name" => "prop",
			"caption" => t("Omadus / Seos"),
			"align" => "center",
			"sortable" => 1
		));*/

		for ($i = 0; $i < $levels; $i++)
		{
			$t->define_field(array(
				"name" => "prop_".$i,
				"caption" => t("Omadus / Seos"),
				"align" => "center",
				"sortable" => 1
			));
		}
	}

	function generate_demo_rels_data_table($arr)
	{
		// read type of object from rel
		if (!is_oid($arr["obj_inst"]->prop("demo_data_source_rel")) || 
			!$this->can("view", $arr["obj_inst"]->prop("demo_data_source_rel")))
		{
			return;
		}

		$rel = obj($arr["obj_inst"]->prop("demo_data_source_rel"));
		$clid = $rel->prop("r_class_id");
		if (is_array($clid))
		{
			$clid = reset($clid);
		}

		$cfgu = get_instance("cfg/cfgutils");
		$ps = $cfgu->load_properties(array(
			"clid" => $clid
		));
		$rels = $cfgu->get_relinfo();

		$props = $this->_get_property_picker_from_clid($clid);
		
		// let user match property to table column
		$t =& $arr["prop"]["vcl_inst"];

		$table_items = new object_list(array(
			"parent" => $arr["obj_inst"]->id(),
			"class_id" => CL_PROPERTY_TABLE_COLUMN
		));
		$col2prop = safe_array($arr["obj_inst"]->meta("col2prop"));
		$reld = safe_array($arr["obj_inst"]->meta("rel_data"));

		$max_level = 1;
		foreach($table_items->arr() as $o)
		{
			$row_dat = array(
				"col" => $o->name(),
				"prop" => html::select(array(
					"name" => "col2prop[".$o->id()."]",
					"options" => $props,
					"value" => $col2prop[$o->id()]
				))
			);

			$cur_level = 0;
			$from_clid = $clid;
			foreach(explode(".", $col2prop[$o->id()].".") as $p_or_rel)
			{
				if (!$from_clid)
				{
					break;
				}
				$row_dat["prop_".$cur_level] = html::select(array(
					"name" => "col2prop[".$o->id()."][]",
					"options" => $this->_get_property_picker_from_clid($from_clid),
					"value" => $p_or_rel
				));

				if ($p_or_rel)
				{
					$cfgu = get_instance("cfg/cfgutils");
					$ps = $cfgu->load_properties(array(
						"clid" => $from_clid
					));
					$rels = $cfgu->get_relinfo();

					$from_clid = $this->_get_rel_class_id_for_prop_or_rel($from_clid, $ps, $rels, $p_or_rel);
				}

				$cur_level++;
				$max_level = max($cur_level, $max_level);
			}

			$t->define_data($row_dat);
		}

		$this->_init_demo_rels_data_t($t, $max_level);
	}

	function save_demo_rels_data_t($arr)
	{
		$c2p = safe_array($arr["request"]["col2prop"]);
		$res = array();
		foreach($c2p as $_id => $inf)
		{
			foreach($inf as $_k => $_v)
			{
				if (!$_v)
				{
					unset($inf[$_k]);
				}
			}
			$res[$_id] = join(".",$inf);
		}
		$arr["obj_inst"]->set_meta("col2prop", $res);
	}

	function _init_vertical_group_t(&$t)
	{
		$t->define_field(array(
			"name" => "col",
			"caption" => t("Tulp"),
			"align" => "center"
		));
	}

	function _vertical_group_t($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_vertical_group_t($t);

		$vgd = safe_array($arr["obj_inst"]->meta("vert_grp_cols"));
		$vgd[] = "";

		$cols = new object_list(array("class_id" => CL_PROPERTY_TABLE_COLUMN, "parent" => $arr["obj_inst"]->id()));

		foreach($vgd as $vge)
		{
			$t->define_data(array(
				"col" => html::select(array(
					"name" => "vert_grp_cols[]",
					"options" => array("" => "") + $cols->names(),
					"value" => $vge
				))
			));
		}

		$t->set_sortable(false);
	}

	function _get_rel_class_id_for_prop_or_rel($clid, $ps, $rels, $sel_p)
	{
		$reltype = "";
		if (isset($ps[$sel_p]))
		{
			if (!empty($ps[$sel_p]["reltype"]))
			{
				$reltype = $ps[$sel_p]["reltype"];
			}
		}
		else
		if (substr($sel_p, 0, 7) == "RELTYPE")
		{
			$reltype = $sel_p;
		}
		$clid = false;
		if ($reltype)
		{
			$clid = $rels[$reltype]["clid"][0];
		}

		return $clid;
	}

	function _get_property_picker_from_clid($clid)
	{
		$cfgu = get_instance("cfg/cfgutils");
		$ps = $cfgu->load_properties(array(
			"clid" => $clid
		));
		$rels = $cfgu->get_relinfo();

		$props = array("" => "");
		$props[] = t("Omadused:");
		foreach($ps as $pn => $pd)
		{
			if ($pd["store"] == "no")
			{
				continue;
			}
			$props[$pn] = "&nbsp;&nbsp;".$pd["caption"];
		}

		$props[] = t("Seosed:");
		foreach($rels as $rel_k => $rel_d)
		{
			if (substr($rel_k, 0, 7) == "RELTYPE")
			{
				$props[$rel_k] = "&nbsp;&nbsp;".$rel_d["caption"];
			}
		}

		return $props;
	}
}
?>
