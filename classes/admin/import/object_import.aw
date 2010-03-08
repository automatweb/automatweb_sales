<?php
/*

@classinfo syslog_type=ST_OBJECT_IMPORT relationmgr=yes no_comment=1 no_status=1 maintainer=kristo

@default table=objects
@default group=general
@default field=meta
@default method=serialize

@property folder type=relpicker reltype=RELTYPE_FOLDER
@caption Kataloog, kuhu pannakse objektid

@property object_type type=relpicker reltype=RELTYPE_OBJECT_TYPE
@caption Imporditava objekti t&uuml;&uuml;p

@property unique_id type=select multiple=1
@caption Unikaalne omadus

@property folder_field type=select
@caption Tulp, mille j&auml;rgi jagatakse kataloogidesse

@property folder_is_parent type=checkbox ch_value=1
@caption Objektid samsse kataloogi, kus imporditavad

@layout ds_hbox type=hbox
@caption Andmeallikas

	@property ds type=relpicker reltype=RELTYPE_DATASOURCE parent=ds_hbox no_caption=1

	@property ds_file type=select parent=ds_hbox editonly=1
	@caption Fail andmeallikast

@property do_import type=checkbox ch_value=1
@caption Teosta import

@property import_status type=text
@caption Impordi staatus

@property auto_import_status type=text
@caption J&auml;rgmine automaatne import

@property last_import_log type=text field=meta method=serialize
@caption Viimase impordi logi

@groupinfo props caption="Omadused"
@property props type=table store=no no_caption=1 group=props

@groupinfo connect caption="Seosta tulbad"
@property connect_props type=table store=no no_caption=1 group=connect

@groupinfo folders caption="Kataloogid"
@property folders type=table store=no no_caption=1 group=folders

@groupinfo sched caption="Automaatne import"
@default group=sched

@property recurrence type=relpicker reltype=RELTYPE_RECURRENCE
@caption Kordus

@property aimp_uid type=textbox
@caption Kasutajanimi

@property aimp_pwd type=password
@caption Parool

@groupinfo del caption="Kustutamine"
@default group=del

@property auto_del type=checkbox ch_value=1
@caption Kustuta andmeallikas puuduvad objektid

@property del_max type=textbox datatype=int size=5
@caption Maksimaalne objektide arv mida kustutada

@reltype OBJECT_TYPE value=1 clid=CL_OBJECT_TYPE
@caption imporditav klass

@reltype FOLDER value=2 clid=CL_MENU,CL_PROJECT
@caption kataloog kuhu objektid panna

@reltype DATASOURCE value=3 clid=CL_ABSTRACT_DATASOURCE
@caption andmeallikas

@reltype EXCEPTION value=4 clid=CL_OBJECT_IMPORT_EXCEPTION
@caption erand

@reltype RECURRENCE value=5 clid=CL_RECURRENCE
@caption kordus

*/

class object_import extends class_base
{
	function object_import()
	{
		$this->init(array(
			"tpldir" => "admin/object_import",
			"clid" => CL_OBJECT_IMPORT
		));

		$this->classif_cache = array();
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "do_import":
				if (!$arr["obj_inst"]->prop("ds"))
				{
					$retval = PROP_IGNORE;
				}
				break;

			case "last_import_log":
				$prop["value"] = join("<br>", safe_array($prop["value"]));
				break;

			case "auto_import_status":
				$o = $arr["obj_inst"];
				if (is_oid($o->prop("recurrence")) && $o->prop("aimp_uid") != "" && $o->prop("aimp_pwd") != "")
				{
					$t = get_instance(CL_RECURRENCE);
					$next = $t->get_next_event(array(
						"id" => $o->prop("recurrence")
					));

					$prop["value"] = sprintf(t("%s, kell %s, kasutaja %s &otilde;igustes."),
						get_lc_date($next, LC_DATE_FORMAT_LONG_FULLYEAR),
						date("H:i", $next),
						$o->prop("aimp_uid")
					);
				}
				else
				{
					return PROP_IGNORE;
				}
				break;

			case "import_status":
				if (!$arr["obj_inst"]->meta("import_status"))
				{
					return PROP_IGNORE;
				}
				$prop["value"]  = sprintf(t("Import k&auml;ivitati %s."), date("d.m.Y H:i", $arr["obj_inst"]->meta("import_started_at")));
				$prop["value"] .= sprintf(t("Hetkel on imporditud %s rida. <br>"), $arr["obj_inst"]->meta("import_row_count"));
				$prop["value"] .= sprintf(t("Viimane muudatus toimis kell %s <br>"),date("d.m.Y H:i", $arr["obj_inst"]->meta("import_last_time")));
				$prop["value"] .= html::href(array(
					"url" => $this->mk_my_orb("do_check_import", array("oid" => $arr["obj_inst"]->id())),
					"caption" => t("J&auml;tka importi kohe")
				));
				break;

			case "folder_is_parent";
				if (!$arr["obj_inst"]->prop("ds"))
				{
					return PROP_IGNORE;
				}
				$ds = obj($arr["obj_inst"]->prop("ds"));
				if (!$ds->prop("ds"))
				{
					return PROP_IGNORE;
				}
				$dso = obj($ds->prop("ds"));
				if ($dso->class_id() != CL_OTV_DS_OBJ)
				{
					return PROP_IGNORE;
				}
				break;

			case "unique_id":
				if (!$arr["obj_inst"]->prop("object_type"))
				{
					return PROP_IGNORE;
				}
				$properties = $this->get_props_from_obj($arr["obj_inst"]);

				$prop["options"] = array("" => "");
				foreach($properties as $pn => $_prop)
				{
					$prop["options"][$pn] = $_prop["caption"];
				}
				break;

			case "folder_field":
				$prop["options"] = array("" => "") + $this->get_cols_from_ds($arr["obj_inst"]);
				break;

			case "props":
				$this->do_props_table($arr);
				break;

			case "connect_props":
				$this->do_connect_props_table($arr);
				break;

			case "folders":
				$this->do_folders_table($arr);
				break;

			case "ds_file":
				if (is_oid($arr["obj_inst"]->prop("ds")))
				{
					$ds = obj($arr["obj_inst"]->prop("ds"));
					if ($ds->prop("ds"))
					{
						$ds_f = obj($ds->prop("ds"));
						if ($ds_f->class_id() == CL_FILE)
						{
							$ol = new object_list($ds->connections_from(array("type" => "RELTYPE_DS")));
							$prop["options"] = array("" => "") + $ol->names();
							$prop["value"] = $ds->prop("ds");
							return PROP_OK;
						}
					}
				}
				return PROP_IGNORE;
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
			case "props":
				$arr["obj_inst"]->set_meta("isimp", $arr["request"]["isimp"]);
				$arr["obj_inst"]->set_meta("userval", $arr["request"]["userval"]);
				$arr["obj_inst"]->set_meta("has_ex", $arr["request"]["has_ex"]);
				$arr["obj_inst"]->set_meta("ex", $arr["request"]["ex"]);
				$arr["obj_inst"]->set_meta("dateformat", $arr["request"]["dateformat"]);
				break;

			case "connect_props":
				$arr["obj_inst"]->set_meta("p2c", $arr["request"]["p2c"]);
				break;

			case "do_import":
				if ($prop["value"] == 1)
				{
					$this->do_init_import($arr["obj_inst"]);
					$this->do_exec_import($arr["obj_inst"]);
					$prop["value"] = 0;
				}
				break;

			case "folders":
				$arr["obj_inst"]->set_meta("fld_values", $arr["request"]["values"]);
				break;

			case "status":
				$prop["value"] = STAT_ACTIVE;
				break;

			case "ds_file":
				if (is_oid($arr["obj_inst"]->prop("ds")))
				{
					$ds = obj($arr["obj_inst"]->prop("ds"));
					if ($ds->prop("ds"))
					{
						$ds_f = obj($ds->prop("ds"));
						if ($ds_f->class_id() == CL_FILE)
						{
							if ($ds->prop("ds") != $prop["value"])
							{
								$ds->set_prop("ds", $prop["value"]);
								$ds->save();
								$prop["value"] = NULL;
							}
							return PROP_OK;
						}
					}
				}
				return PROP_IGNORE;
				break;

		}
		return $retval;
	}

	private function _init_folders_table(&$t)
	{
		$t->define_field(array(
			"name" => "folder",
			"caption" => t("Kataloog"),
			"sortable" => 1
		));

		$t->define_field(array(
			"name" => "value",
			"caption" => t("V&auml;&auml;rtus, mille puhul pannakse sellesse kataloogi"),
			"align" => "center",
		));

		$t->set_default_sortby("folder");
	}

	private function do_folders_table($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_folders_table($t);

		$values = $arr["obj_inst"]->meta("fld_values");

		foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_FOLDER")) as $c)
		{
			$o = $c->to();

			$t->define_data(array(
				"folder" => $o->path_str(),
				"value" => html::textbox(array(
					"size" => 10,
					"name" => "values[".$o->id()."]",
					"value" => $values[$o->id()]
				))
			));

			$ot = new object_tree(array(
				"parent" => $o->id(),
				"class_id" => CL_MENU
			));
			$ol = $ot->to_list();
			for($o = $ol->begin(); !$ol->end(); $o = $ol->next())
			{
				$t->define_data(array(
					"folder" => $o->path_str(),
					"value" => html::textbox(array(
						"size" => 10,
						"name" => "values[".$o->id()."]",
						"value" => $values[$o->id()]
					))
				));
			}
		}

		$t->sort_by();
	}

	private function _init_props_tbl(&$t)
	{
		$t->define_field(array(
			"name" => "prop",
			"caption" => t("Omadus"),
			"sortable" => 1
		));

		$t->define_field(array(
			"name" => "isimp",
			"caption" => t("Imporditav?"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "userval",
			"caption" => t("V&auml;&auml;rtus k&auml;sitsi"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "has_ex",
			"caption" => t("Oman erandeid?"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "ex",
			"caption" => t("Erandid"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "date_format",
			"caption" => t("Kuup&auml;eva formaat"),
			"align" => "center"
		));
		$t->set_default_sortby("prop");
	}

	private function do_props_table($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_props_tbl($t);

		$isimp = $arr["obj_inst"]->meta("isimp");
		$userval = $arr["obj_inst"]->meta("userval");
		$has_ex = $arr["obj_inst"]->meta("has_ex");
		$ex = $arr["obj_inst"]->meta("ex");
		$dateformat = $arr["obj_inst"]->meta("dateformat");

		$exes = array("" => t("--vali--"));
		foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_EXCEPTION")) as $c)
		{
			$exes[$c->prop("to")] = $c->prop("to.name");
		}

		foreach($this->get_props_from_obj($arr["obj_inst"]) as $pn => $pd)
		{
			if ($pn == "needs_translation" || $pn == "is_translated")
			{
				continue;
			}
			$exs = "";
			if ($has_ex[$pn] == 1)
			{
				$exs = html::select(array(
					"multiple" => 1,
					"name" => "ex[$pn]",
					"options" => $exes,
					"selected" => $this->make_keys($ex[$pn])
				));
			}
			$df = "";
			if ($pd["type"] == "datetime_select" || $pd["type"] == "date_select")
			{
				$df = html::textbox(array(
					"name" => "dateformat[$pn]",
					"value" => $dateformat[$pn],
					"size" => 10
				));
			}

			$t->define_data(array(
				"prop" => $pd["caption"],
				"isimp" => html::checkbox(array(
					"name" => "isimp[$pn]",
					"value" => 1,
					"checked" => $isimp[$pn]
				)),
				"userval" => html::textbox(array(
					"name" => "userval[$pn]",
					"value" => $userval[$pn],
					"size" => 5
				)),
				"has_ex" => html::checkbox(array(
					"name" => "has_ex[$pn]",
					"value" => 1,
					"checked" => ($has_ex[$pn] == 1)
				)),
				"ex" => $exis,
				"date_format" => $df
			));
		}

		$t->sort_by();
	}

	private function get_props_from_obj($o)
	{
		$cf = get_instance(CL_CFGFORM);
		return $cf->get_props_from_ot(array(
			"ot" => $o->prop("object_type")
		));

		$type_o = obj($o->prop("object_type"));
		$class_id = $type_o->prop("type");
		if (!$type_o->prop("use_cfgform"))
		{
			list($properties) = $GLOBALS["object_loader"]->load_properties(array(
				"clid" => $class_id
			));
		}
		else
		{
			$class_i = get_instance($class_id == CL_DOCUMENT ? "doc" : $class_id);
			$properties = $class_i->load_from_storage(array(
				"id" => $type_o->prop("use_cfgform"),
			));
		}

		return $properties;
	}

	private function _init_connect_props_table(&$t, $cols)
	{
		$t->set_sortable(false);
		$t->define_field(array(
			"name" => "prop",
			"caption" => t(""),
			"align" => "right",
			"width" => "10%",
			"nowrap" => 1
		));

		foreach($cols as $idx => $cold)
		{
			$t->define_field(array(
				"name" => "col_".$idx,
				"caption" => $cold,
				"align" => "center"
			));
		}

		$t->define_field(array(
			"name" => "dontimp",
			"caption" => t("&Auml;ra impordi"),
			"align" => "center"
		));
	}

	private function do_connect_props_table($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];

		$cols = $this->get_cols_from_ds($arr["obj_inst"]);

		$this->_init_connect_props_table($t, $cols);

		$p2c = $arr["obj_inst"]->meta("p2c");
		$isimp = $arr["obj_inst"]->meta("isimp");

		foreach($this->get_props_from_obj($arr["obj_inst"]) as $pn => $pd)
		{
			if (!$isimp[$pn] || $pn == "needs_translation" || $pn == "is_translated")
			{
				continue;
			}
			$issel = false;
			$fields = array(
				"prop" => $pd["caption"]
			);
			foreach($cols as $idx => $cold)
			{
				$fields["col_".$idx] = html::radiobutton(array(
					"name" => "p2c[$pn]",
					"value" => $idx,
					"checked" => ($p2c[$pn] == $idx)
				));
				$issel |= ($p2c[$pn] == $idx);
			}

			$fields["dontimp"] = html::radiobutton(array(
				"name" => "p2c[$pn]",
				"value" => "dontimp",
				"checked" => (($p2c[$pn] == "dontimp") || !$issel)
			));

			$t->define_data($fields);
		}
	}

	private function get_cols_from_ds($o)
	{
		if (!$o->prop("ds"))
		{
			return array();
		}

		// let the datasource parse the data and return the first row
		$ds_o = obj($o->prop("ds"));
		$ds_i = $ds_o->instance();
		return $ds_i->get_fields($ds_o, $o->prop("ds_file"));
	}

	/**
		@attrib name=automatic_import

		@param id required
	**/
	function automatic_import($arr)
	{
		$o = obj($arr["id"]);
		$this->do_init_import($o);
		$this->do_exec_import($o);
	}


	/**
		@attrib name=do_check_import nologin="1"

		@param oid optional
	**/
	function do_check_import($arr = array())
	{
		if (false && date("H") > 8 && date("H") < 19)
		{
			echo "not during the day! <br>";
			$sc = get_instance("scheduler");
			$sc->add(array(
				"event" => $this->mk_my_orb("do_check_import"),
				"time" => time() + 60 * 60,
				"sessid" => session_id()
			));
			die();
		}
		$filt = array(
			"class_id" => CL_OBJECT_IMPORT,
			"site_id" => array(),
			"lang_id" => array()
		);
		if ($arr["oid"])
		{
			$filt["oid"] = $arr["oid"];
		}
		$ol = new object_list($filt);
		for ($o = $ol->begin(); !$ol->end(); $o = $ol->next())
		{
			if ($o->meta("import_status") == 1 && $o->meta("import_last_time") < (time() - 30))
			{
				echo "restart import for ".$o->id()." <br>";
				$this->_add_log($o, sprintf(t("Importi alustati uuesti realt %s"), $o->meta("import_row_count")));
				$this->do_exec_import($o, $o->meta("import_row_count"));
			}
			echo "for o ".$o->id()." status = ".$o->meta("import_status")." last time = ".date("d.m.Y H:i", $o->meta("import_last_time"))." <br>";

			if (is_oid($o->prop("recurrence")) && $o->prop("aimp_uid") != "" && $o->prop("aimp_pwd") != "")
			{
				$t = get_instance(CL_RECURRENCE);
				$next = $t->get_next_event(array(
					"id" => $o->prop("recurrence")
				));

				if ($next)
				{
					// add to scheduler
					$sc = get_instance("scheduler");
					$sc->add(array(
						"event" => $this->mk_my_orb("automatic_import", array("id" => $o->id())),
						"time" => $next,
						"uid" => $o->prop("aimp_uid"),
						"password" => $o->prop("aimp_pwd")
					));
				}
			}
		}

	}

	private function do_exec_import($o, $start_from_row = 0)
	{
		// for each line in the ds
		// read it
		// if there is an unique column
		//		check if there already is an object with that value
		//		if true, use that
		// else
		//		create new object
		// match col => prop
		// save
		// loop
		// js redir back to change
		if ($o->prop("ds"))
		{
			aw_set_exec_time(AW_LONG_PROCESS);
			ignore_user_abort(true);
			ini_set("memory_limit", "500M");
			if (aw_ini_get("object_import.uid") && aw_global_get("uid") == "")
			{
				$u = get_instance("users");
				$u->login(array(
					"uid" => aw_ini_get("object_import.uid"),
					"password" => aw_ini_get("object_import.password")
				));
			}
			aw_ini_set("cache.use_html_cache", 0); /// set this so we won't do a cache flush on every object save - and we're gonna do lots of saves here
			$sc = get_instance("scheduler");
			$sc->add(array(
				"event" => $this->mk_my_orb("do_check_import"),
				"time" => time() + 4 * 60,
				"sessid" => session_id()
			));

			$type_o = obj($o->prop("object_type"));
			$class_id = $type_o->prop("type");
			$p2c = $o->meta("p2c");
			$userval = $o->meta("userval");
			$has_ex = $o->meta("has_ex");
			$ex = $o->meta("ex");
			$dateformat = $o->meta("dateformat");

			$folder = $o->prop("folder");
			if (!$folder)
			{
				$folder = $o->parent();
			}

			$line_n = 0;

			$ds_o = obj($o->prop("ds"), $o->prop("ds_file"));
			$ds_i = $ds_o->instance();
			$data_rows = $ds_i->get_objects($ds_o);

			// read props
			list($properties, $tableinfo, $relinfo) = $GLOBALS["object_loader"]->load_properties(array(
				"clid" => $class_id
			));

			$ex_i = get_instance(CL_OBJECT_IMPORT_EXCEPTION);

			if (is_array($o->prop("unique_id")) && count($o->prop("unique_id")) > 0)
			{
				// get uniqueness filtr.
				// we need to load all possible objects to do this. damn.
				// storage simply can not handle this for 65k objects.
				// so we do query :(
				echo "creating uniqueness filter <br>\n";
				flush();

				$existing_objects = $this->_get_uniq_existing($o, $properties, $tableinfo);

				echo sprintf(t("created uniqueness filter for %s objects. <br>\n"), count($existing_objects));
				flush();
			}

			$row_count = count($data_rows);
			foreach($data_rows as $line)
			{
				$line_n++;
				if ($start_from_row > $line_n)
				{
					continue;
				}

				echo sprintf(t("impordin rida %s... <br>\n"),$line_n);
				flush();

				if (!is_array($o->prop("unique_id")) && $o->prop("unique_id") != "")
				{
					$o->set_prop("unique_id", array($o->prop("unique_id") => $o->prop("unique_id")));
				}
				if (is_array($o->prop("unique_id")) && count($o->prop("unique_id")) > 0)
				{
					$key = "";

					$key = $this->_get_un_key_for_obj($o, $p2c, $line, $userval, $properties, $class_id);
					$t_oid = $existing_objects[$key];

					if (is_oid($t_oid) && $this->can("view", $t_oid))
					{
						$dat = obj($t_oid);
						echo sprintf(t("leidsin juba olemasoleva objekti %s, kasutan olemasolevat objekti <br>"), $dat->name());
					}
					else
					{
						$dat = obj();
						if ($type_o->prop("use_cfgform"))
						{
							$dat->set_meta("cfgform_id", $type_o->prop("use_cfgform"));
						}
					}
				}
				else
				{
					$dat = obj();
					// if type has cfgform, set the object to use that so that when you change it, is show the correct form
					if ($type_o->prop("use_cfgform"))
					{
						$dat->set_meta("cfgform_id", $type_o->prop("use_cfgform"));
					}
				}

				$dat->set_meta("object_type", $type_o->id());
				$dat->set_class_id($class_id);
				$dat->set_parent($folder);

				$linebak = $line;
				if (!is_array($p2c))
				{
					continue;
				}
				foreach($p2c as $pn => $idx)
				{
					if ($pn == "needs_translation" || $pn == "is_translated" || $idx == "dontimp")
					{
						continue;
					}
					$line[$idx] = convert_unicode($line[$idx]);

					if ($has_ex[$pn] && is_array($ex[$pn]))
					{
						foreach($ex[$pn] as $iexp)
						{
							$line[$idx] = $ex_i->do_replace($iexp, $line[$idx]);
						}
					}
					// now, if the property is of type classificator, then we need to make a list of all options
					// search that and then select the correct one
					if ($properties[$pn]["type"] == "classificator")
					{
						$tmp = $this->_resolve_classificator(array(
							"name" => $pn,
							"clid" => $class_id,
							"object_type" => $o->prop("object_type")
						), $line[$idx]);
						if (is_oid($tmp))
						{
							$line[$idx] = $tmp;
						}
					}
					else
					if ($dateformat[$pn] != "")
					{
						$line[$idx] = $this->_get_date_value($line[$idx], $dateformat[$pn]);
					}
					if($dat->is_property($pn))
					{
						$dat->set_prop($pn, $line[$idx]);
						if ($properties[$pn]["store"] == "connect" && $this->can("view", $line[$idx]))
						{
							$rid = $relinfo[$properties[$pn]["reltype"]]["value"];
							$dat->connect(array(
								"to" => $line[$idx],
								"reltype" => $rid
							));
						}
					}
				}
				$line = $linebak;

				if ($o->prop("folder_is_parent"))
				{
					$dat->set_parent($line["parent"]);
				}
				// now, if we have separate folder settings, then move to correct place.
				if (($fldfld = $o->prop("folder_field")))
				{
					$flds = new aw_array($o->meta("fld_values"));
					$fldfld_val = $line[$fldfld];
					foreach($flds->get() as $fld_id => $fld_val)
					{
						if ($fld_val == $fldfld_val)
						{
							$dat->set_parent($fld_id);
						}
					}
				}
				// also uservals
				if (is_array($userval))
				{
					foreach($userval as $uv_pn => $uv_pv)
					{
						if ($uv_pv != "")
						{
							$dat->set_prop($uv_pn, $uv_pv);
						}
					}
				}
				$dat->save();
				echo sprintf(t("importisin objekti %s (%s) kataloogi %s <br>\n"), $dat->name(),$dat->id(),$dat->parent());
				flush();
				if (($line_n % 10) == 1)
				{
					$o->set_meta("import_last_time", time());
					$o->set_meta("import_row_count", $line_n);
					$o->save();
				}
				if (($line_n % ($row_count / 10)) == 1)
				{
					$this->_add_log($o, sprintf(t("Imporditud %s objekti"), $line_n));
				}
			}
			aw_ini_set("cache.use_html_cache", 1);
			$this->_add_log($o, $this->_delete_objects($o, $properties, $tableinfo, $data_rows, $p2c, $userval, $class_id));
			$c = get_instance("cache");
			aw_global_set("no_cache_flush", 0);
			$c->full_flush();
			$this->do_mark_finish_import($o);
			echo t("Valmis! <br>\n");
			echo "<script language=javascript>setTimeout(\"window.location='".$this->mk_my_orb("change", array("id" => $o->id()))."'\", 5000);</script>";
			die();
		}
	}

	private function do_init_import($o)
	{
		$o->set_meta("import_status", 1);
		$o->set_meta("import_started_at", time());
		$this->_start_log($o);
	}

	private function do_mark_finish_import($o)
	{
		$o->set_meta("import_status", 0);
		$this->_add_log($o, t("Import edukalt l&otilde;ppenud"));
	}

	function callback_pre_edit($arr)
	{
		if ($arr["obj_inst"]->meta("import_status") == 1)
		{
			$sc = get_instance("scheduler");
			$sc->add(array(
				"event" => $this->mk_my_orb("do_check_import"),
				"time" => time() + 4 * 60,
				"sessid" => session_id()
			));
		}
	}

	function callback_post_save($arr)
	{
		$o = $arr["obj_inst"];
		if (is_oid($o->prop("recurrence")) && $o->prop("aimp_uid") != "" && $o->prop("aimp_pwd") != "")
		{
			$t = get_instance(CL_RECURRENCE);
			$next = $t->get_next_event(array(
				"id" => $o->prop("recurrence")
			));

			if ($next)
			{
				// add to scheduler
				$sc = get_instance("scheduler");
				$sc->add(array(
					"event" => $this->mk_my_orb("automatic_import", array("id" => $o->id())),
					"time" => $next,
					"uid" => $o->prop("aimp_uid"),
					"password" => $o->prop("aimp_pwd")
				));
			}
		}
	}

	private function _resolve_classificator($arr, $str)
	{
		if (!is_array($this->classif_cache[$arr["name"]]))
		{
			$clf = get_instance(CL_CLASSIFICATOR);
			$this->classif_cache[$arr["name"]] = $clf->get_options_for(array(
				"name" => $arr["name"],
				"clid" => $arr["clid"],
				"object_type" => $arr["object_type"]
			));
		}

		$str = trim(strtolower($str));
		foreach($this->classif_cache[$arr["name"]] as $clf_id => $clf_n)
		{
			if ($str == trim(strtolower($clf_n)))
			{
				return $clf_id;
			}
		}
		echo "Hoiatus! ei leidnud vastet klassifikaatori v&auml;&auml;rtusele $str!<Br>Praegused variandid: ".join(",", $this->classif_cache[$arr["name"]])."<br><hr><br>";
		return NULL;
	}

	private function _get_uniq_existing($o, $properties, $tableinfo)
	{
		$existing_objects = array();

		$fetch = array("objects.oid as id");
		$where = array("objects.status > 0", "objects.lang_id = '".aw_global_get("lang_id")."'", "objects.site_id = ".aw_ini_get("site_id"));
		$tbls = array("objects" => "objects");

		foreach($o->prop("unique_id") as $unique_id)
		{
			$prop = $properties[$unique_id];
			if(empty($prop))
			{
				continue;
			}
			if ($prop["store"] == "no")
			{
				continue;
			}

			if (!isset($tbls[$prop["table"]]))
			{
				$tbls[$prop["table"]] = "LEFT JOIN {$prop[table]} ON {$prop[table]}.{$tableinfo[$prop[table]][index]} = {$tableinfo[$prop[table]][master_table]}.{$tableinfo[$prop[table]][master_index]}";
			}

			$tf = $prop["table"].".".$prop["field"];
			$fetch[] = $tf." as ".$unique_id;
		}

		$uns = $o->prop("unique_id");

		$sql = "SELECT ".join(",", $fetch)." FROM objects ".join(" ", $tbls)." WHERE ".join(" AND ", $where);
		$this->db_query($sql);
		while ($row = $this->db_next())
		{
			$key = "";
			foreach($uns as $un)
			{
				$key .= $un.":".$row[$un];
			}
			$existing_objects[$key] = $row["id"];
		}

		return $existing_objects;
	}

	private function _delete_objects($o, $properties, $tableinfo, $lines, $p2c, $userval, $class_id)
	{
		// check if the object says we should delete
		if (!$o->prop("auto_del"))
		{
			return;
		}

		echo t("create uniq filter for delete <br>\n");
		flush();

		// get uniqueness filter
		$uniq = $this->_get_uniq_existing($o, $properties, $tableinfo);

		echo t("got filter <Br>\n");
		flush();

		// compare filter to lines read from ds
		// make list of objects that are in uniq filter but not in ds
		foreach($lines as $line)
		{
			$key = $this->_get_un_key_for_obj($o, $p2c, $line, $userval, $properties, $class_id);
			unset($uniq[$key]);
		}

		echo t("unset keys <br>\n");
		flush();

		if (count($uniq))
		{
			$parents = array();
			foreach($o->connections_from(array("type" => "RELTYPE_FOLDER")) as $c)
			{
				$parents[$c->prop("to")] = $c->prop("to");
			}
			// XXX: this is the place that creates the huge (2M) query
			/*$ol = new object_list(array(
				"oid" => $this->make_keys(array_values($uniq)),
				"parent" => $parents
			));
			$uniq = $ol->ids();*/
			$objstr = join(",", array_values($uniq));
			$parents = join(",", $parents);
			$this->db_query("SELECT oid FROM objects WHERE status > 0 AND oid IN($objstr) AND parent IN ($parents)");
			$uniq = array();
			while ($row = $this->db_next())
			{
				$uniq[] = $row["oid"];
			}
		}

		// check if the number is less than max allowed
		if (count($uniq) > $o->prop("del_max"))
		{
			echo sprintf(t("ERROR: number of objects to delete is %s greater than the max allowed: %s <br>\n"), count($uniq), $o->prop("del_max"));
			return sprintf(t("VIGA: leitud kustutamisele minevate objektide hulk %s on suurem kui maksimaalne: %s"), count($uniq), $o->prop("del_max"));
		}

		// kill the bastards
		foreach($uniq as $oid)
		{
			if (!$this->can("view", $oid))
			{
				continue;
			}
			echo sprintf(t("delete object %s <br>\n"), $oid);
			flush();
			$o = obj($oid);
			$o->delete();
		}

		echo t("delete done. <br>\n");
		flush();
		return sprintf(t("Edukalt kustutatud %s objekti!"), count($uniq));
	}

	private function _get_un_key_for_obj($o, $p2c, $line, $userval, $properties, $class_id)
	{
		foreach($o->prop("unique_id") as $unique_id)
		{
			// get column for uniq id
			$u_col = $p2c[$unique_id];
			$val = $line[$u_col];

			// if this col has a user-defined value, use that.
			if (!empty($userval[$unique_id]))
			{
				$val = $userval[$unique_id];
			}

			// check for classificators
			if ($properties[$unique_id]["type"] == "classificator")
			{
				$tmp = $this->_resolve_classificator(array(
					"name" => $unique_id,
					"clid" => $class_id,
					"object_type" => $o->prop("object_type")
				), $line[$u_col]);
				if (is_oid($tmp))
				{
					$val = $tmp;
				}
			}
			$key .= $unique_id.":".$val;
		}

		return $key;
	}

	private function _start_log($o)
	{
		$o->set_meta("last_import_log", array(date("d.m.Y / H:i").": ".t("Importi alustati ")));
		$o->save();
	}

	private function _add_log($o, $msg)
	{
		$cur = safe_array($o->meta("last_import_log"));
		$cur[] = date("d.m.Y / H:i").": ".$msg;
		$o->set_meta("last_import_log", $cur);
		aw_disable_acl();
		$o->save();
		aw_restore_acl();
	}

	private function _get_date_value($date, $format)
	{
		return strptime($date, $format);
	}
}
?>
