<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/contentmgmt/object_treeview/otv_ds_pp_search.aw,v 1.15 2009/05/06 13:41:44 markop Exp $
// otv_ds_pp_search.aw - Objektinimekirja pp andmeallika otsing 
/*

@classinfo syslog_type=ST_OTV_DS_PP_SEARCH relationmgr=yes no_status=1 no_comment=1 maintainer=kristo

@default table=objects
@default group=general
@default field=meta
@default method=serialize

@property pp type=relpicker reltype=RELTYPE_PP
@caption Postipoisi andmeallikas, kust otsida

@groupinfo sform caption="Otsinguvorm"
@default group=sform

@property sform_t type=table no_caption=1
@caption Otsinguvorm

@property no_submit type=checkbox ch_value=1 
@caption &Auml;ra n&auml;ita otsi nuppu

@property no_search_form type=checkbox ch_value=1 
@caption &Auml;ra n&auml;ita otsingu vormi (otsing ainult urli kaudu)

@groupinfo stbl caption="Tulemuste tabel"
@default group=stbl

@property stbl_t type=table no_caption=1
@caption Otsinguvormi tulemuste tabel

@property result_default_order type=select
@caption Otsinguvormi vaikimisi j&auml;rjestuse suund


@groupinfo srch caption="Otsi" submit_method=get
@default group=srch

@property srch type=callback callback=callback_get_srch

@property srch_res type=table no_caption=1



@reltype PP value=1 clid=CL_OTV_DS_POSTIPOISS
@caption postipoisi andmeallikas

@reltype CONTROLLER value=2 clid=CL_CFG_CONTROLLER
@caption Kontroller

*/

class otv_ds_pp_search extends class_base
{
	const AW_CLID = 820;

	function otv_ds_pp_search()
	{
		$this->init(array(
			"tpldir" => "contentmgmt/object_treeview/otv_ds_pp_search",
			"clid" => CL_OTV_DS_PP_SEARCH
		));
		$this->types = array(
			"" => "",
			0 => t("Tekstikast"),
			1 => t("Valik olemasolevatest"),
			2 => t("Ajavahemik"),
			3 => t("Kuup&auml;eva vahemik"),
		);
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "sform_t":
				$this->do_sform_t_tbl($arr);
				break;

			case "stbl_t":
				$this->do_stbl_t_tbl($arr);
				break;

			case "srch_res":
				$this->do_srch_res_t($arr);
				break;
			case "result_default_order":
				$prop["options"] = array("0" => t("Kasvav") , "desc" => t("kahanev"));
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
			case "sform_t":
				$arr["obj_inst"]->set_meta("sform", $arr["request"]["td"]);
				break;

			case "stbl_t":
				$arr["obj_inst"]->set_meta("stbl", $arr["request"]["td"]);
				break;
		}
		return $retval;
	}	

	function _init_sform_t_tbl(&$t)
	{
		$t->define_field(array(
			"name" => "prop",
			"caption" => t("Element")
		));
		$t->define_field(array(
			"name" => "in_form",
			"caption" => t("Otsitav"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "ord",
			"caption" => t("J&auml;rjekord"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "type",
			"caption" => t("Otsinguv&auml;lja t&uuml;&uuml;p"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "controller",
			"caption" => t("Kontroller"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "caption",
			"caption" => t("Tekst"),
			"align" => "center"
		));
	}

	function get_controllers($o)
	{
		$ret = array("" => "");
		foreach($o->connections_from(array("type" => "RELTYPE_CONTROLLER")) as $c)
		{
			$ret[$c->prop("to")] = $c->prop("to.name");
		}
		return $ret;
	}

	function do_sform_t_tbl($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_sform_t_tbl($t);

		$td = $arr["obj_inst"]->meta("sform");

		$i = get_instance(CL_OTV_DS_POSTIPOISS);
		$flds = $i->get_fields();

		$flds["__fulltext"] = t("T&auml;istekstiotsing");

		$controller_selection = $this->get_controllers($arr["obj_inst"]);

		foreach($flds as $fldid => $fldc)
		{
			if (!is_array($td[$fldid]))
			{
				$td[$fldid] = array("caption" => $fldc);
			}

			$t->define_data(array(
				"prop" => $fldc,
				"in_form" => html::checkbox(array(
					"name" => "td[$fldid][in_form]",
					"value" => 1,
					"checked" => ($td[$fldid]["in_form"] == 1)
				)),
				"ord" => html::textbox(array(
					"name" => "td[$fldid][ord]",
					"value" => $td[$fldid]["ord"],
					"size" => 5
				)),
				"caption" => html::textbox(array(
					"name" => "td[$fldid][caption]",
					"value" => $td[$fldid]["caption"],
				)),
				"type" => html::select(array(
					"name" => "td[$fldid][type]",
					"value" => $td[$fldid]["type"],
					"options" => $this->types,
				)),
				"controller" => html::select(array(
					"name" => "td[$fldid][controller]",
					"value" => $td[$fldid]["controller"],
					"options" => $controller_selection,
				)),
			));
		}
		$t->set_sortable(false);
	}

	function _init_stbl_t_tbl(&$t)
	{
		$t->define_field(array(
			"name" => "prop",
			"caption" => t("Element")
		));
		$t->define_field(array(
			"name" => "in_tbl",
			"caption" => t("Tabelis"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "sortable",
			"caption" => t("Sorditav"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "is_date",
			"caption" => t("Kuup&auml;ev"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "is_number",
			"caption" => t("Number"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "defaultsort",
			"caption" => t("Vaikimisi sorteeritud"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "viewcol",
			"caption" => t("Vaatamise tulp"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "ord",
			"caption" => t("J&auml;rjekord"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "caption",
			"caption" => t("Tekst"),
			"align" => "center"
		));
	}

	function do_stbl_t_tbl($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_stbl_t_tbl($t);

		$td = $arr["obj_inst"]->meta("stbl");

		$i = get_instance(CL_OTV_DS_POSTIPOISS);
		$flds = $i->get_fields();

		foreach($flds as $fldid => $fldc)
		{
			if (!is_array($td[$fldid]))
			{
				$td[$fldid] = array("caption" => $fldc);
			}

			$defs = "";
			if ($td[$fldid]["sortable"])
			{
				$defs = html::radiobutton(array(
					"name" => "td[__defaultsort]",
					"value" => $fldid,
					"checked" => ($td["__defaultsort"] == $fldid)
				));
			}

			$vc = "";
			if ($td[$fldid]["in_tbl"])
			{
				$vc = html::radiobutton(array(
					"name" => "td[__viewcol]",
					"value" => $fldid,
					"checked" => ($td["__viewcol"] == $fldid)
				));
			}

			$t->define_data(array(
				"prop" => $fldc,
				"in_tbl" => html::checkbox(array(
					"name" => "td[$fldid][in_tbl]",
					"value" => 1,
					"checked" => ($td[$fldid]["in_tbl"] == 1)
				)),
				"sortable" => html::checkbox(array(
					"name" => "td[$fldid][sortable]",
					"value" => 1,
					"checked" => ($td[$fldid]["sortable"] == 1)
				)),
				"is_date" => html::checkbox(array(
					"name" => "td[$fldid][is_date]",
					"value" => 1,
					"checked" => ($td[$fldid]["is_date"] == 1)
				)),
				"is_number" => html::checkbox(array(
					"name" => "td[$fldid][is_number]",
					"value" => 1,
					"checked" => ($td[$fldid]["is_number"] == 1)
				)),
				"defaultsort" => $defs,
				"viewcol" => $vc,
				"ord" => html::textbox(array(
					"name" => "td[$fldid][ord]",
					"value" => $td[$fldid]["ord"],
					"size" => 5
				)),
				"caption" => html::textbox(array(
					"name" => "td[$fldid][caption]",
					"value" => $td[$fldid]["caption"],
				))
			));
		}
		$t->set_sortable(false);
	}

	function callback_get_srch($arr)
	{
		// make form
		return $this->get_properties_for_form($arr["obj_inst"], $arr["request"]);
	}

	function get_properties_for_form($o, $request)
	{
		$ret = array();

		$sf = safe_array($o->meta("sform"));

		uasort($sf, create_function('$a,$b', 'if ($a["ord"] == $b["ord"]) { return 0;} return $a["ord"] > $b["ord"];'));

		foreach($sf as $fld => $fld_dat)		
		{
			if (!$fld_dat["in_form"])
			{
				continue;
			}
			switch($fld_dat["type"])
			{
				case 1:
					//array("" => t("-- Vali --"));
					$selection = array();
					$q = "SELECT DISTINCT aw_$fld FROM aw_otv_ds_pp_cache";
					$this->db_query($q);
					$res = array();
					while($row = $this->db_next())
					{
						$selection[$row["aw_$fld"]] = $row["aw_$fld"];
					};
					asort($selection);
					$ret[$fld] = array(
						"name" => $fld,
						"caption" => $fld_dat["caption"],
						"value" => $request[$fld],
						"type" => "select",
						"store" => "no",
						"options" => $selection
					);
					break;
				case 2:
					$ret[$fld] = array(
						"name" => $fld,
						"caption" => $fld_dat["caption"]." " .t("alates"),
						"value" => $request[$fld],
						"type" => "datetime_select",
						"store" => "no"
					);
					$ret[$fld."_to"] = array(
						"name" => $fld."_to",
						"caption" => $fld_dat["caption"]." " .t("kuni"),
						"value" => $request[$fld."_to"],
						"type" => "datetime_select",
						"store" => "no"
					);
					break;
				case 3:
					$ret[$fld] = array(
						"name" => $fld,
						"caption" => $fld_dat["caption"]." " .t("alates"),
						"value" => $request[$fld],
						"type" => "date_select",
						"store" => "no"
					);
					$ret[$fld."_to"] = array(
						"name" => $fld."_to",
						"caption" => $fld_dat["caption"]." " .t("kuni"),
						"value" => $request[$fld."_to"],
						"type" => "date_select",
						"store" => "no"
					);
					break;
				default:
					$ret[$fld] = array(
						"name" => $fld,
						"caption" => $fld_dat["caption"],
						"value" => $request[$fld],
						"type" => "textbox",
						"store" => "no"
					);
					break;
			}
			if($this->can("view" , $fld_dat["controller"]))
			{
				$prop = "";
				$fc = get_instance(CL_FORM_CONTROLLER);
				$show = $fc->eval_controller($fld_dat["controller"], $ret[$fld]);

				if($show)
				{
					$ret[$fld] = $show;
				}
			}
		}
		
		return $ret;
	}

	function make_res_table(&$t, $o)
	{
		$td = safe_array($o->meta("stbl"));
		uasort($td, create_function('$a,$b', 'if ($a["ord"] == $b["ord"]) { return 0;} return $a["ord"] > $b["ord"];'));

		foreach($td as $tf => $tf_dat)
		{
			if (($tf_dat["in_tbl"] != 1))
			{
				continue;
			}

			$tdef = array(
				"name" => "aw_".$tf,
				"caption" => $tf_dat["caption"],
				"align" => "center",
				"sortable" => $tf_dat["sortable"]
			);

			if ($tf_dat["is_date"] == 1)
			{
				$tdef["type"] = "time";
				$tdef["numeric"] = 1;
				$tdef["format"] = "d.m.Y";
			}
			else
			if ($tf_dat["is_number"] == 1)
			{
				$tdef["numeric"] = 1;
			}
			$t->define_field($tdef);
		}

		if ($td["__defaultsort"] != "")
		{
			if($o->prop("result_default_order"))
			{
				$t->set_default_sorder($o->prop("result_default_order"));
			}
			$t->set_default_sortby("aw_".$td["__defaultsort"]);
		}
	}

	function populate_result_table(&$t, $req, $o)
	{
		$td = safe_array($o->meta("stbl"));

		$datecols = array();
/*		foreach($td as $tf_n => $tf_d)
		{
			if ($tf_d["is_date"] == 1)
			{
				$datecols[] = "aw_".$tf_n;
			}
		}
*/
		$sql = $this->get_search_sql($o, $req);
//		if(aw_global_get("uid") == "markop"){ arr($sql);}
		$this->db_query($sql);
//		if(aw_global_get("uid") == "markop"){ die();}
		while ($row = $this->db_next())
		{
			if ($td["__viewcol"])
			{
				$row["aw_".$td["__viewcol"]] = html::href(array(
					"url" => $row["aw_url"],
					"caption" => $row["aw_".$td["__viewcol"]]
				));
			}
/*
			foreach($datecols as $date_col)
			{
				list($_d, $_m, $_y) = explode(".", $row[$date_col]);
				$row[$date_col] = mktime(4,0,0, $_m, $_d, $_y);
				if(aw_global_get("uid") == "markop")arr($row[$date_col]);
			}
*/
			$ta = array();
			foreach($row as $k => $v)
			{
				$ta[$k] = convert_unicode($v);
			}
			$t->define_data($ta);
		}
		$t->sort_by();
		return;
	}

	function get_search_sql($o, $req)
	{
		$pts = array();

		$this->quote(&$req);

		$sf = safe_array($o->meta("sform"));
		$sf_tmp = $sf;
		foreach($sf as $fld => $fld_dat)		
		{
			if (!$fld_dat["in_form"])
			{
				continue;
			}

			if ($fld == "teemad" && $this->can("view" , $o->prop("pp")) && $req[$fld])
			{
				$i = get_instance(CL_OTV_DS_POSTIPOISS);
				$folders = $i->get_folders(obj($o->prop("pp")));
				$values = array($req[$fld]);
				foreach($folders as $folder)
				{
					if($folder["parent"] == $req[$fld])
					{
						$values[] = $folder["id"];
					}
				}
				$pts[] = "aw_".$fld." IN (".join("," , $values).") ";
				//arr($values);
			}
			elseif ($fld == "__fulltext")
			{
				if ($req["__fulltext"] != "")
				{
					$req["__fulltext"] = $this->char_replace($req["__fulltext"]);
					$npts = array();
					$i = get_instance(CL_OTV_DS_POSTIPOISS);
					$sf_tmp = $i->get_fields();
					foreach($sf_tmp as $fld => $fld_dat)
					{
						if (!$fld_dat["in_form"] || $fld == "__fulltext")
						{
							continue;
						}
						$npts[] = " aw_".$fld." LIKE '%".$req["__fulltext"]."%' ";
					}
					$pts[] = " (".join(" OR ", $npts).") ";
				}
			}
			else
			if ($req[$fld] != "")
			{
				switch($fld_dat["type"])
				{
					case 1:
						$pts[] = "aw_".$fld."='".$req[$fld]."' ";
						/*$pts[] = "(aw_".$fld."='".$req[$fld]."' OR 
							  aw_".$fld." LIKE '%".$req[$fld].",%' OR
							aw_".$fld." LIKE '%,".$req[$fld]."%' )";*/
						break;
					case 2:
					case 3:
						$from = date_edit::get_timestamp($req[$fld]);
						$to = date_edit::get_timestamp($req[$fld."_to"]);
						if($from != $to)
						{
							if(!($from > 1))
							{
								$pts[] = " aw_".$fld." < '".$to."'";
							}
							elseif(!($to > 1))
							{
								$pts[] = " aw_".$fld." > '".$from."'";
							}
							elseif($to > $from)
							{
								$pts[] = " aw_".$fld." BETWEEN '".$from."' AND '".$to."'";
							}
						}
						break;
					default: 
						$pts[] = " aw_".$fld." LIKE '%".$req[$fld]."%' ";
						break;
				}
			}
		}

		$ptss = join(" AND ", $pts);
		if ($ptss != "")
		{
			$ptss = "AND ( ".$ptss." ) ";
		}
		else
		{
			$ptss = " AND 1 = 0 ";
		}

		if (!$o->prop("pp"))
		{
			error::raise(array(
				"id" => "ERR_NO_PP",
				"msg" => t("otv_ds_pp_search: postipoisi andmeallikas on valimata!")
			));
		}
		return "SELECT * FROM aw_otv_ds_pp_cache WHERE aw_pp_id = ".$o->prop("pp")." ".$ptss;
	}

	function do_srch_res_t($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->make_res_table($t, $arr["obj_inst"]);

		$this->populate_result_table($t, $arr["request"], $arr["obj_inst"]);
	}

	function parse_alias($arr)
	{
		return $this->show(array("id" => $arr["alias"]["target"]));
	}

	function show($arr)
	{
		$ob = new object($arr["id"]);
		$request = $_GET;

		$props = $this->callback_get_srch(array(
			"obj_inst" => $ob,
			"request" => $request
		));

		$html = "";

		if (!$ob->prop("no_search_form"))
		{
			$htmlc = get_instance("cfg/htmlclient");
			$htmlc->start_output();
			foreach($props as $pn => $pd)
			{
				$htmlc->add_property($pd);
			}

			if (!$ob->prop("no_submit"))
			{
				$htmlc->add_property(array(
					"name" => "search",
					"caption" => t("Otsi"),
					"type" => "button",
					"store" => "no",
					"onclick" => "this.disabled=true; self.disabled=true; document.otv_ds_pp_search.submit();",
				));
			}
			$htmlc->finish_output();
	
			$html = $htmlc->get_result(array(
				"raw_output" => 1
			));
		}


		classload("vcl/table");
		$t = new aw_table(array(
			"layout" => "generic"
		));
		$this->do_srch_res_t(array(
			"prop" => array(
				"vcl_inst" => &$t
			),
			"obj_inst" => &$ob,
			"request" => $request,
		));
		$table = $t->draw();
		
		$this->read_template("show.tpl");
		$this->vars(array(
			"form" => $html,
			"section" => aw_global_get("section"),
			"table" => $table,
			
		));
		return $this->parse();
	}
	
	function char_replace($str)
	{
		$str = str_replace(chr(228), "&auml;", $str);
		$str = str_replace(chr(246), "&ouml;", $str);
		$str = str_replace(chr(245), "&otilde;", $str);
		$str = str_replace(chr(252), "&uuml;", $str);

		$str = str_replace(chr(220), "&Uuml;", $str);
		$str = str_replace(chr(213), "&Otilde;", $str);
		$str = str_replace(chr(214), "&Ouml;", $str);
		$str = str_replace(chr(196), "&Auml;", $str);
		return $str;
	}
}
?>
