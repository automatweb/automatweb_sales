<?php
// otv_ds_obj.aw - Objektinimekirja AW datasource
/*

@classinfo syslog_type=ST_OTV_DS_OBJ relationmgr=yes no_status=1 no_comment=1 maintainer=kristo
@default table=objects


@default group=general

	@property show_notact type=checkbox ch_value=1 field=meta method=serialize
	@caption N&auml;ita mitteaktiivseid objekte

	@property show_notact_folder type=checkbox ch_value=1 field=meta method=serialize
	@caption N&auml;ita mitteaktiivseid katalooge

	@property show_notact_noclick type=checkbox ch_value=1 field=meta method=serialize
	@caption Mitteaktiivsed pole klikitavad

	@property file_show_comment type=checkbox ch_value=1 field=meta method=serialize
	@caption Failil nime asemel kommentaar

	@property ignore_site_id type=checkbox ch_value=1 field=meta method=serialize
	@caption Objektid k&otilde;ikidest saitidest

	@property sort_by type=select field=meta method=serialize
	@caption Objekte sorteeritakse

	@property fld_sort_by type=select field=meta method=serialize
	@caption Katalooge sorteeritakse

	@property use_meta_as_folders type=checkbox ch_value=1 field=meta method=serialize
	@caption Kasuta kaustade puu joonistamiseks muutujaid

	@property show_via_cfgform type=relpicker reltype=RELTYPE_SHOW_CFGFORM field=meta method=serialize
	@caption Objekti vaatamine l&auml;bi seadete vormi

@default group=folders

	@property folders type=table store=no callback=callback_get_menus editonly=1
	@caption Kataloogid

@default group=types

	@property view_ots type=relpicker multiple=1 automatic=1 reltype=RELTYPE_SHOW_TYPE field=meta method=serialize
	@caption N&auml;idatavad objektit&uuml;&uuml;bid

	@property add_ots type=relpicker multiple=1 automatic=1 reltype=RELTYPE_ADD_TYPE field=meta method=serialize
	@caption Lisatavad objektit&uuml;&uuml;bid

@groupinfo folders caption="Kataloogid"
@groupinfo types caption="Objektit&uuml;&uuml;bid"


@reltype FOLDER value=1 clid=CL_MENU
@caption kataloog

@reltype ADD_TYPE value=2 clid=CL_OBJECT_TYPE
@caption lisatav objektit&uuml;&uuml;p

@reltype SHOW_TYPE value=3 clid=CL_OBJECT_TYPE
@caption n&auml;idatav objektit&uuml;&uuml;p

@reltype SHOW_CFGFORM value=4 clid=CL_CFGFORM
@caption v&auml;ljundi seadete vorm

@reltype META value=6 clid=CL_META
@caption Muutuja

@reltype TRANSFORM value=7 clid=CL_OTV_DATA_FILTER
@caption andmete muundaja
*/

class otv_ds_obj extends class_base
{
	function otv_ds_obj()
	{
		$this->init(array(
			"tpldir" => "contentmgmt/object_treeview/otv_ds_obj",
			"clid" => CL_OTV_DS_OBJ
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "sort_by":
				$prop["options"] = array(
					"objects.modified DESC" => t("Objekti muutmise kuup&auml;eva j&auml;rgi"),
					"objects.jrk" => t("Objektide j&auml;rjekorra j&auml;rgi"),
					"objects.name" => t("Objektide nime j&auml;rgi")
				);
				break;

			case "fld_sort_by":
				$prop["options"] = array(
					"objects.jrk" => t("Objektide j&auml;rjekorra j&auml;rgi"),
					"objects.modified DESC" => t("Objekti muutmise kuup&auml;eva j&auml;rgi"),
					"objects.name" => t("Objektide nime j&auml;rgi")
				);
				break;

			case "show_via_cfgform":
				if ($arr["new"])
				{
					return PROP_IGNORE;
				}
				// list all cfgforms in system according to show types
				$sts = array();
				foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_SHOW_TYPE")) as $c)
				{
					$tp = $c->to();
					$sts[$tp->prop("type")] = $tp->prop("type");
				}

				$ol = new object_list(array(
					"class_id" => CL_CFGFORM,
					"ctype" => $sts
				));
				$prop["options"] = array("" => "--vali--") + $ol->names();
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
			case "folders":
				$arr['obj_inst']->set_meta("include_submenus",$arr["request"]["include_submenus"]);
				$arr['obj_inst']->set_meta("ignoreself",$arr["request"]["ignoreself"]);
				break;

			case "clids":
				$_clids = array();
				$a = get_class_picker();
				foreach($a as $clid => $clname)
				{
					$rt = "clid_".$clid;
					if (isset($arr["request"][$rt]) && $arr["request"][$rt] == 1)
					{
						$_clids[$clid] = $clid;
					}
				}
				$arr["obj_inst"]->set_meta("clids", $_clids);
				break;
		}
		return $retval;
	}

	function callback_get_menus($args = array())
	{
		$prop = $args["prop"];
		$nodes = array();

		// now I have to go through the process of setting up a generic table once again
		load_vcl("table");
		$this->t = new aw_table(array(
			"prefix" => "ot_menus",
			"layout" => "generic"
		));
		$this->t->define_field(array(
			"name" => "oid",
			"caption" => t("ID"),
			"talign" => "center",
			"align" => "center",
			"nowrap" => "1",
			"width" => "30",
		));
		$this->t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"talign" => "center",
		));
		$this->t->define_field(array(
			"name" => "check",
			"caption" => t("k.a. alammen&uuml;&uuml;d"),
			"talign" => "center",
			"width" => 80,
			"align" => "center",
		));
		$this->t->define_field(array(
			"name" => "ignoreself",
			"caption" => t("&auml;ra n&auml;ita peamen&uuml;&uuml;d"),
			"talign" => "center",
			"width" => 80,
			"align" => "center",
		));

		$include_submenus = $args["obj_inst"]->meta("include_submenus");
		$ignoreself = $args["obj_inst"]->meta("ignoreself");

		$opts = array();
		$use_meta_as_folders = $args['obj_inst']->prop("use_meta_as_folders");
		if(empty($use_meta_as_folders))
		{
			$opts['reltype'] = RELTYPE_FOLDER;
			$opts['class'] = CL_MENU;
		}
		else
		{
			$opts['reltype'] = RELTYPE_META;
			$opts['class'] = CL_META;
		}


		$conns = $args["obj_inst"]->connections_from(array(
			"type" => $opts['reltype'],
		));

		foreach($conns as $conn)
		{
			$c_o = $conn->to();
			$c_o_id = $c_o->id();

			$chk = "";
			if ($c_o->class_id() == $opts['class'])
			{
				$chk = html::checkbox(array(
					"name" => "include_submenus[".$c_o_id."]",
					"value" => $c_o_id,
					"checked" => $include_submenus[$c_o_id],
				));
			}

			$this->t->define_data(array(
				"oid" => $c_o_id,
				"name" => $c_o->path_str(array(
					"max_len" => 3
				)),
				"check" => $chk,
				"ignoreself" => html::checkbox(array(
					"name" => "ignoreself[".$c_o_id."]",
					"value" => $c_o_id,
					"checked" => $ignoreself[$c_o_id],
				)),
			));
		};

		$nodes[$prop["name"]] = array(
			"type" => "text",
			"caption" => $prop["caption"],
			"value" => $this->t->draw(),
		);
		return $nodes;
	}

	/** returns data about folders that the datasource object $o contains

		@comment

			returns an array, key is entry id, value is array(
				id => id,
				parent => parent
				name => name,
				url => url,
				target => target,
				comment => comment
				type => type,
				add_date => add_date,
				mod_date => mod_date,
				adder => adder
				modder => modder,
				icon => icon,
				fileSizeBytes,
				fileSizeKBytes,
				fileSizeMBytes
			)

			bot id and parent are opaque strings
	**/
	function get_folders($ob, $tree_type = NULL)
	{
		if (!is_oid($ob->id()))
		{
			return;
		}
		// go over all related menus and add subtree id's together if the user has so said.
		$ret = array();

		$sub = $ob->meta("include_submenus");
		$igns = $ob->meta("ignoreself");

		classload("core/icons", "image");

		$fld_sort_by = $ob->prop("fld_sort_by");
		$sort = (empty($fld_sort_by) ? "objects.jrk" : $fld_sort_by) . " ASC";

		$opts = array();
		$use_meta_as_folders = $ob->prop("use_meta_as_folders");
		if(empty($use_meta_as_folders))
		{
			$opts['reltype'] = "RELTYPE_FOLDER";
			$opts['class'] = CL_MENU;
		}
		else
		{
			$opts['reltype'] = RELTYPE_META;
			$opts['class'] = CL_META;
		}

		$conns = $ob->connections_from(array(
			"type" => $opts['reltype'],
		));
		foreach($conns as $conn)
		{
			$c_o = $conn->to();
			if (!isset($this->first_folder))
			{
				$this->first_folder = $c_o->id();
			}
			$root_id = $c_o->id();
			$cur_ids = array();
			if ($sub[$root_id])
			{
				$_ot = new object_tree(array(
					"class_id" => $opts['class'],
					"parent" => $root_id,
					"status" => $ob->prop("show_notact_folder") ? array(STAT_ACTIVE,STAT_NOTACTIVE) : STAT_ACTIVE,
					"lang_id" => array(),
					"sort_by" => $sort
				));

				if ($tree_type == "TREE_TABLE")
				{
					$_ot->filter(array("parent" => $root_id), false);
				}

				if ($tree_type == "TREE_COMBINED" && ($_GET['tv_sel'] || $_GET['table']))
				{
				//	$root_id = (int)($_GET['tv_sel']);
					$root_id = (int)($_GET['table_sel']);

					$_ot = $_ot->subtree($root_id);
				}
				$cur_ids = $_ot->ids();
			}

			if (!$igns[$root_id])
			{
				$cur_ids[] = $root_id;
			}

			foreach($cur_ids as $t_id)
			{
				$t = obj($t_id);
				if ($igns[$root_id] && $t->parent() == $root_id)
				{
					$pt = 0;
				}
				else
				if ($t_id == $root_id)
				{
					$pt = 0;
				}
				else
				{
					$pt = $t->parent();
				}
				$ret[$t->id()] = array(
					"id" => $t->id(),
					"parent" => $pt,
					"name" => parse_obj_name($t->name()),
					"comment" => $t->comment(),
					"add_date" => $t->created(),
					"mod_date" => $t->modified(),
					"adder" => $t->createdby(),
					"modder" => $t->modifiedby(),
					"icon" => image::make_img_tag(icons::get_icon_url($t->class_id(), $t->name())),
					"jrk" => $t->ord()
				);
			}
		}

		switch ($fld_sort_by)
		{
			case "objects.jrk":
				uasort($ret, create_function('$a,$b', 'return ($a["jrk"] == $b["jrk"] ? 0 : ($a["jrk"] > $b["jrk"] ? 1 : -1));'));
				break;
			case "objects.name":
				uasort($ret, create_function('$a,$b', 'return (strcmp($a["name"], $b["name"]));'));
				break;
		}

		return $ret;
	}

	function _filt_get_fields($ob)
	{
		$ret = array();

		$ot = get_instance(CL_OBJECT_TYPE);

		$clids = array();
		$cttt = $ob->connections_from(array("type" => "RELTYPE_SHOW_TYPE"));
		foreach($cttt as $c)
		{
			$ps = $ot->get_properties($c->to());
			foreach($ps as $pn => $pd)
			{
				if ($pd["store"] == "no")
				{
					continue;
				}

				if ($pd["method"] == "serialize")
				{
					continue;
				}
				$ret[$pn] = $pd["caption"];
			}
		}

		$ret["jrk"] = "J&auml;rjekord";
		return $ret;
	}

	function get_fields($ob, $full_props = false)
	{
		$ret = array();

		$ot = get_instance(CL_OBJECT_TYPE);

		$clids = array();
		$cttt = $ob->connections_from(array("type" => "RELTYPE_SHOW_TYPE"));
		foreach($cttt as $c)
		{
			$ps = $ot->get_properties($c->to());
			$to_o = $c->to();
			foreach($ps as $pn => $pd)
			{
				if ($pd["store"] == "no" && !strstr($pn, "userim"))
				{
					continue;
				}

				if ($full_props)
				{
					$pd["class_id"] = $to_o->subclass();
					$pd["object_type"] = $to_o->id();
					$ret[$pn] = $pd;
				}
				else
				{
					$ret[$pn] = $pd["caption"];
				}

			}
		}

		$ret["jrk"] = t("J&auml;rjekord");
		$ret["id"] = t("OID");
		foreach($ob->connections_from(array("type" => "RELTYPE_TRANSFORM")) as $c)
		{
			$tr = $c->to();
			$tr_i = $tr->instance();
			$tr_i->transform($tr, $ret);
		}
		return $ret;
	}

	function has_feature($str)
	{
		if ($str == "filter")
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function get_objects($ob, $fld = NULL, $tv_sel = NULL, $params = array())
	{
		enter_function("otv_ds_obj::get_objects");
		$ret = array();

		enter_function("otv_ds_obj::get_objects::init");
		// if the folder is specified in the url, then show that
		// if use_meta_as_folders option is set, then ignore tv_sel here
		$use_meta_as_folders = $ob->prop("use_meta_as_folders");

		if ($GLOBALS["tv_sel"] && empty($use_meta_as_folders))
		{
			$parent = $GLOBALS["tv_sel"];
		}
		else
		// right. if the user has said, that no tree should be shown
		// then get files in all selected folders
		if (!$ob->meta('show_folders'))
		{

			$con = $ob->connections_from(array(
				"type" => "RELTYPE_FOLDER"
			));
			$inc_subs = safe_array($ob->meta("include_submenus"));

			$parent = array();
			foreach($con as $c)
			{
				$parent[$c->prop("to")] = $c->prop("to");
				if ($inc_subs[$c->prop("to")])
				{
					$ot = new object_tree(array(
						"parent" => $c->prop("to"),
						"lang_id" => array(),
						"site_id" => array(),
						//"class_id" => CL_MENU
					));
					foreach($ot->ids() as $p_id)
					{
						$parent[$p_id] = $p_id;
					}
				}
			}

		}
		if (!is_oid($ob->id()))
		{
			exit_function("otv_ds_obj::get_objects");
			return;
		}
		if (!$parent)
		{
			// if parent can't be found. then get the objects from all the root folders
			$con = $ob->connections_from(array(
				"type" => "RELTYPE_FOLDER"
			));

			$ignoreself = $ob->meta("ignoreself");

			$parent = array();
			foreach($con as $c)
			{
				// but only those that are to be ignored!
				if ($ignoreself[$c->prop("to")])
				{
					$parent[$c->prop("to")] = $c->prop("to");
				}
			}
		}

		$props = array();
		$clids = array();
		$cttt = $ob->connections_from(array("type" => "RELTYPE_SHOW_TYPE"));
		foreach($cttt as $c)
		{
			$c_o = $c->to();
			$clids[] = $c_o->subclass();

			$_tmp = obj();
			$_tmp->set_class_id($c_o->subclass());
			$pl = $_tmp->get_property_list();
			foreach($pl as $pn => $pd)
			{
				$pd["clid"] = $c_o->subclass();
				$props[$pn] = $pd;
			}
		}

		$awa = new aw_array($parent);
		if (count($awa->get()) < 1)
		{

			$parent = $this->first_folder;
		}

		$sby = "objects.modified DESC";
		if ($ob->prop("sort_by") != "")
		{
			$sby = $ob->prop("sort_by");
		}
// seems that if i want to filter something - i need to do it here:
		exit_function("otv_ds_obj::get_objects::init");

		enter_function("otv_ds_obj::get_objects::make_filter");
		$clss = aw_ini_get("classes");
		$_ft = array(
			"parent" => $parent,
			"status" => $ob->prop("show_notact") ? array(STAT_ACTIVE, STAT_NOTACTIVE) : STAT_ACTIVE,
			"class_id" => $clids,
			"sort_by" => $sby,
			"lang_id" => array()
		);
		// if there is $params['filters'] array then lets filter
		if(!empty($params['filters']) && is_array($params['filters']))
		{
			// make array by group
			$filt_by_grp = array();
			foreach($params['filters']['saved_filters']->get() as $filter)
			{
				if (!isset($filter["group"]))
				{
					$filter["group"] = "";
				}

				if ($filter["field"] == "status")
				{
					unset($_ft["status"]);
				}
				if (strpos($filter["value"], "<?php") !== false)
				{
					eval(str_replace("<?php", "", $filter["value"]));
					$filter["value"] = $ret;
				}
				$filt_by_grp[$filter["group"]][] = $filter;
			}

			// filtering by these filters which are saved in otv
			foreach($filt_by_grp as $grp => $filters)
			{
				$cur_filt = array();
				foreach($filters as $filter)
				{
					$filter_value = $filter['value'];
					// if the filter value is not marked as strict match:
					if ($filter['is_strict'] != 1)
					{
						$filter_value = "%".$filter_value."%";
					}
					// if it is set, that the filter value should not be in the field
					// also considers the previous strict/not strict option
					if ($filter['is_not'] == 1)
					{
						$filter_value = new obj_predicate_not($filter_value);
					}
					if ($filter["field"] == "__fulltext")
					{
						$cond = array();
						foreach($this->_filt_get_fields($ob) as $_fn => $_fc)
						{
							$cond[$_fn] = "%".$filter["value"]."%";
						}
						$cur_filt[] = new object_list_filter(array(
							"logic" => $params["predicate"],
							"conditions" => $cond
						));
					}
					else
					{
						$p = $props[$filter['field']];
						if ($p["type"] == "classificator")
						{
							if ($p["store"] == "connect")
							{
								$cur_filt[$clss[$p["clid"]]["def"].".".$p["reltype"].".name"] = $filter_value;

							}
							else
							{
								$cur_filt[$clss[$p["clid"]]["def"].".".$filter['field'].".name"] = $filter_value;

							}
						}
						else
						{
							// handle multiple filters on same field
							if ($cur_filt[$filter['field']] != "" || is_array($cur_filt[$filter['field']]))
							{
								if (!is_array($cur_filt[$filter['field']]))
								{
									$cur_filt[$filter['field']] = array($cur_filt[$filter['field']]);
								}
								$cur_filt[$filter['field']][] = $filter_value;
							}
							else
							{
								$cur_filt[$filter['field']] = $filter_value;
							}
						}
					}
				}

				$_ft[] = new object_list_filter(array(
					"logic" => $params["predicate"] ? $params["predicate"] : "AND",
					"conditions" => $cur_filt
				));
			}
			// filtering by $tv_sel
			if (($ob->prop("use_meta_as_folders") == 1) && empty($params['filters']['char']) && !empty($tv_sel))
			{
				$_ft[$params['filters']['group_by_folder']] = $tv_sel;
			}
			else
			// filtering by char
			if (!empty($params['filters']['char']) && empty($tv_sel))
			{
				// check if char's value is not "all", cause then all object should be returned
				if ($params['filters']['char'] != "all")
				{
					$_ft[$params['filters']['filter_by_char_field']] = $params['filters']['char']."%";
				}
			}
		}

		if ($ob->prop("ignore_site_id"))
		{
			$_ft["site_id"] = array();
		}
		exit_function("otv_ds_obj::get_objects::make_filter");
		enter_function("otv_ds_obj::get_objects::list");
/*if ($_GET["otvdbg"])
{
	die(dbg::dump($_ft));
}*/
		$ol = new object_list($_ft);
		//$ol->sort_by_cb(array(&$this, "_obj_list_sorter"));
		exit_function("otv_ds_obj::get_objects::list");


		enter_function("otv_ds_obj::get_objects::get_fields");
		$classlist = aw_ini_get("classes");
		$fields = $this->get_fields($ob, true);
		exit_function("otv_ds_obj::get_objects::get_fields");


		$ret = array();
		classload("core/icons", "image");

		enter_function("otv_ds_obj::get_objects::arr");
		$ar = $ol->arr();
		exit_function("otv_ds_obj::get_objects::arr");

		enter_function("otv_ds_obj::get_objects::loop");

		foreach($ar as $t)
		{
			$url = $target = $fileSizeBytes = $fileSizeKBytes = $fileSizeMBytes = "";
			$caption = $t->trans_get_val("name");
			$clid = $t->class_id();
			$proplist = $t->get_property_list();
			if ($clid == CL_EXTLINK)
			{
				$li = get_instance("contentmgmt/links_display");
				list($url,$target,$caption) = $li->draw_link($t->id());
			}
			else
			if ($clid == CL_FILE)
			{
				$fi = get_instance(CL_FILE);
				$url = $fi->get_url($t->id(),$t->trans_get_val("name"));

				if ($fd["newwindow"])
				{
					$target = "target=\"_blank\"";
				}
				$fileSizeBytes = number_format(file::get_file_size($t->prop('file')),2);
				$fileSizeKBytes = number_format(file::get_file_size($t->prop('file'))/(1024),2);
				$fileSizeMBytes = number_format(file::get_file_size($t->prop('file'))/(1024*1024),2);
			}
			else
			if ($clid == CL_MENU)
			{
				$url = aw_url_change_var("tv_sel", $t->id());
			}
			else
			{
				if (($_cff = $ob->prop("show_via_cfgform")))
				{
					$url = $this->mk_my_orb("view", array(
						"id" => $t->id(),
						"cfgform" => $_cff,
						"section" => aw_global_get("section")
					), $t->class_id());
				}
				else
				{
					$url = $this->cfg["baseurl"]."/".$t->id();
				}
			}

			if ($ob->prop("show_notact_noclick") && $t->status() == STAT_NOTACTIVE)
			{
				$url = "";
			}

			if ($clid == CL_FILE && $ob->prop("file_show_comment"))
			{
				$_name = parse_obj_name($t->trans_get_val("comment"));
			}
			else
			{
				$_name = parse_obj_name($t->trans_get_val("name"));
			}
			$ret[$t->id()] = array(
				"id" => $t->id(),
				"parent" => $t->parent(),
				"name" => $_name,
				"url" => $url,
				"target" => $target,
				"comment" => $t->trans_get_val("comment"),
				"type" => $classlist[$clid]["name"],
				"add_date" => $t->created(),
				"mod_date" => $t->modified(),
				"adder" => $t->createdby(),
				"modder" => $t->modifiedby(),
				"object_icon" => image::make_img_tag(icons::get_icon_url($clid, $t->trans_get_val("name"))),
				"fileSizeBytes" => $fileSizeBytes,
				"fileSizeKBytes" => $fileSizeKBytes,
				"fileSizeMBytes" => $fileSizeMBytes,
				"change" => html::href(array(
					"url" => $this->mk_my_orb("change", array("id" => $t->id(), "section" => aw_global_get("section")), $clid),
					"caption" => html::img(array(
						"url" => aw_ini_get("baseurl")."/automatweb/images/icons/edit.gif",
						"border" => 0
					))//"Muuda"
				)),
				"jrk" => $t->ord()
			);
			foreach($fields as $ff_n => $ff_d)
			{
				if ($ff_n != "url")
				{
					if ($ff_n != "type")
					{
						if (isset($params["sel_cols"]))
						{
							if ($params["sel_cols"][$ff_n] == 1)
							{
								if ($params['edit_columns'][$ff_n] == 1)
								{
									$ret[$t->id()][$ff_n] = nl2br($t->prop($ff_n));
								}
								else
								{
									$ret[$t->id()][$ff_n] = nl2br($t->trans_get_val_str($ff_n));
								}
							}
						}
						else
						{
							if ($params['edit_columns'][$ff_n] == 1)
							{
								$ret[$t->id()][$ff_n] = nl2br($t->prop($ff_n));
							}
							else
							{

								$ret[$t->id()][$ff_n] = $t->trans_get_val_str($ff_n);
								if ($proplist[$ff_n]["type"] == "textarea")
								{
									$ret[$t->id()][$ff_n] = nl2br($ret[$t->id()][$ff_n]);
								}
							}
						}
					}
					else
					{
						$ret[$t->id()][$ff_n] = $classlist[$t->class_id()]["name"];
					}
				}
				if (strstr($ff_n, "userim"))
				{
					$img_obj = $t->get_first_obj_by_reltype($ff_d['reltype']);

					$ret[$t->id()][$ff_n] = (empty($img_obj)) ? "" : $img_obj->id();
				}
			}

			if ($t->class_id() == CL_FILE && $ob->prop("file_show_comment"))
			{
				$_name = parse_obj_name($t->trans_get_val("comment"));
			}
			else
			{
				$_name = parse_obj_name($t->trans_get_val("name"));
			}
			$ret[$t->id()]["name"] = $_name;
			$ret[$t->id()]["jrk"] = $t->ord();
			$ret[$t->id()]["id"] = $t->id();
		}
		exit_function("otv_ds_obj::get_objects::loop");
		enter_function("otv_ds_obj::transform");
		foreach($ob->connections_from(array("type" => "RELTYPE_TRANSFORM")) as $c)
		{
			$tr = $c->to();
			$tr_i = $tr->instance();
			$tr_i->transform($tr, $ret);
		}
		exit_function("otv_ds_obj::transform");

		exit_function("otv_ds_obj::get_objects");



/*

$all_keys = array_keys($ret);
arr($all_keys);
$conn_obj = new connection();

$images = $conn_obj->find(array("from" => $all_keys));
arr($images);
foreach($images as $i)
{
	$ret[$i['from']][''] = $i['to']
}

*/

		return $ret;
	}

	function _obj_list_sorter($a, $b)
	{
		if ($a->class_id() == CL_MENU && $b->class_id() != CL_MENU)
		{
			return -1;
		}
		else
		if ($a->class_id() != CL_MENU && $b->class_id() == CL_MENU)
		{
			return 1;
		}
		else
		if ($a->class_id() != CL_MENU && $b->class_id() != CL_MENU)
		{
			return $a->modified() < $b->modified();
		}
		else
		if ($a->class_id() == CL_MENU && $b->class_id() == CL_MENU)
		{
			return $a->modified() < $b->modified();
		}
	}

	function check_acl($acl, $o, $id)
	{
		return $this->can($acl, $id);
	}

	function get_add_types($o)
	{
		$ret = array();
		foreach($o->connections_from(array("type" => "RELTYPE_ADD_TYPE")) as $c)
		{
			$ret[] = $c->to();
		}

		$conns = $o->connections_from(array(
			"type" => "RELTYPE_FOLDER"
		));
		$c = reset($conns);

		if (!$c)
		{
			return array(false, $ret);
		}
		return array($GLOBALS["tv_sel"] ? $GLOBALS["tv_sel"] : $c->prop("to"), $ret);
	}

	function do_delete_objects($o, $arr)
	{
		foreach($arr as $oid)
		{
			$o = obj($oid);
			$o->delete();
		}
	}

	/** saves editable fields (given in $ef) to object $id, data is in $data

		@attrib api=1


	**/
	function update_object($ef, $id, $data)
	{
		if ($data === NULL)
		{
			return;
		}
		$o = obj($id);
		$mod = false;
		foreach($ef as $fn => $tmp)
		{
			if ($fn == "jrk")
			{
				if ($data[$fn] != $o->ord())
				{
					$o->set_ord($data[$fn]);
					$mod = true;
				}
			}
			else
			{
				if ($o->prop($fn) != $data[$fn])
				{
					$o->set_prop($fn, $data[$fn]);
					$mod = true;
				}
			}
		}
		if ($mod)
		{
			$o->save();
		}
	}
}
?>
