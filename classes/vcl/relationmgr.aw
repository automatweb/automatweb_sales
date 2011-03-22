<?php
 // this is the new relationmanager, unfortunately for now, still template based, let's hope
 // that class_base will one day be powerful enough to replace these with properties without
 // a headache -- ahz

class relationmgr extends aw_template
{
	function relationmgr()
	{
		$this->init("relationmgr");
	}

	function init_vcl_property($arr)
	{
		$arr["request"] = safe_array($arr["request"]) + $_REQUEST;
		if(in_array($arr["obj_inst"]->class_id() , get_container_classes()))
		{
			$this->parent = $arr["obj_inst"]->id();
		}
		else
		{
			$this->parent = $arr["obj_inst"]->parent();
		}
		$this->_init_relations($arr);
		if(isset($arr["request"]["srch"]) && $arr["request"]["srch"] == 1)
		{
			return $this->_show_search($arr);
		}
		else
		{
			return $this->_show_relations($arr);
		}
	}

	/**
		@attrib name=disp_relmgr all_args=1
	**/
	function disp_relmgr($arr)
	{
		$oi = obj($arr["id"]);
		$arg = array(
			"request" => $arr,
			"obj_inst" => $oi,
			"relinfo" => $oi->get_relinfo()
		);

		// filter by add tree conf
		$atc = new add_tree_conf();
		$ccf = $atc->get_current_conf();
		if ($ccf)
		{
			$filt = $atc->get_usable_filter($ccf);
			foreach($arg["relinfo"] as $idx => $dat)
			{
				foreach($dat["clid"]  as $idx2 => $clid)
				{
					if (!isset($filt[$clid]))
					{
						unset($arg["relinfo"][$idx]["clid"][$idx2]);
					}
					if (count($arg["relinfo"][$idx]["clid"]) == 0)
					{
						unset($arg["relinfo"][$idx]);
					}
				}
			}
		}
		$r = $this->init_vcl_property($arg);
		$cli = new htmlclient();
		foreach($r as $pn => $pd)
		{
			if ($pd["type"] === "toolbar")
			{
				$pd["value"] = $pd["vcl_inst"]->get_toolbar();
			}
			$cli->add_property($pd);
		}
		$cli->finish_output(array(
			"method" => "GET",
			"action" => !empty($arr["srch"]) ? "disp_relmgr" : "submit",
			"submit" => "no",
			"data" => array(
				"orb_class" => "relationmgr",
				"id" => $arr["id"],
			)
		));
		return $cli->get_result();
	}

	/**
		@attrib name=submit all_args=1
	**/
	function submit($arr)
	{
		$arg = array(
			"request" => $arr,
			"obj_inst" => obj($arr["id"]),
		);
		$this->process_vcl_property($arg);
		return $this->mk_my_orb("disp_relmgr", array("id" => $arr["id"]));
	}

	/**
		@attrib name=rel_cut all_args=1
	**/
	function rel_cut($arr)
	{
		$i = new class_base();
		$i->rel_cut($arr);
		return $this->mk_my_orb("disp_relmgr", array("id" => $arr["id"]));
	}

	/**
		@attrib name=rel_copy all_args=1
	**/
	function rel_copy($arr)
	{
		$i = new class_base();
		$i->rel_copy($arr);
		return $this->mk_my_orb("disp_relmgr", array("id" => $arr["id"]));
	}

	/**
		@attrib name=rel_paste all_args=1
	**/
	function rel_paste($arr)
	{
		$i = new class_base();
		$arr["silent"] = 1;
		if ($i->rel_paste($arr) === "err")
		{
			die(html::href(array(
				"url" => $url,
				"caption" => t("Kliki siia j&auml;tkamiseks")
			)));
		}
		return $this->mk_my_orb("disp_relmgr", array("id" => $arr["id"]));
	}

	function _get_reltypes($clid)
	{
		$reltypes[0] = t("Alias");
		$reltypes[RELTYPE_BROTHER] = t("Too vend");
		$reltypes[RELTYPE_ACL] = t("&Otilde;igus");
		$tmpo = obj();
		$tmpo->set_class_id($clid);
		$relinfo = $tmpo->get_relinfo();
		foreach($relinfo as $key => $rel)
		{
			if(empty($reltypes[$rel["value"]]) && empty($rel["hidden"]))
			{
				$reltypes[$rel["value"]] = empty($rel["caption"]) ? t("[nimetu]") : $rel["caption"];
			}
		}
		return $reltypes;
	}

	function _init_relations($arr)
	{
		$this->clids = array();
		$classes = aw_ini_get("classes");

		// maybe it would be nice to get this kind of relinfo from core
		// or storage, so i wouldn't have to make it here? -- ahz
		$this->clids[CL_MENU] = basename($classes[CL_MENU]["file"]);
		$this->clids[CL_SHOP_PRODUCT] = basename($classes[CL_SHOP_PRODUCT]["file"]);
		$this->clids[CL_SHOP_PACKET] = basename($classes[CL_SHOP_PACKET]["file"]);
		$this->clids[CL_SHOP_PRODUCT_PACKAGING] = basename($classes[CL_SHOP_PRODUCT_PACKAGING]["file"]);
		$this->clids[CL_GROUP] = basename($classes[CL_GROUP]["file"]);
		$tmp = array();
		foreach($classes as $key => $class)
		{
			if(isset($class["alias"]) && $class["alias"])
			{
				$tmp[$key] = $class["name"];
				$this->clids[$key] = basename($class["file"]);
			}
		}
		$this->rel_classes[0] = $tmp;
		$this->rel_classes[RELTYPE_ACL] = array(
			CL_GROUP => $classes[CL_GROUP]["name"]
		);
		$this->rel_classes[RELTYPE_BROTHER] = array(
			CL_MENU => $classes[CL_MENU]["name"],
			CL_SHOP_PRODUCT => $classes[CL_SHOP_PRODUCT]['name'],
			CL_SHOP_PACKET => $classes[CL_SHOP_PACKET]['name'],
			CL_SHOP_PRODUCT_PACKAGING => $classes[CL_SHOP_PRODUCT_PACKAGING]['name'],
		);
		foreach($arr["relinfo"] as $key => $rel)
		{
			if(empty($rel["hidden"]))
			{
				$tmp = array();
				foreach($rel["clid"] as $val)
				{
					$tmp[$val] = $classes[$val]["name"];
					$this->clids[$val] = basename($classes[$val]["file"]);
				}
				$this->rel_classes[$rel["value"]] = $tmp;
			}
		}
		$this->reltypes = $this->_get_reltypes($arr["obj_inst"]->class_id());
		if (isset($arr["property"]["configured_rels"]) && is_array($arr["property"]["configured_rels"]))
		{
			$this->rel_classes = $this->rel_classes + $arr["property"]["configured_rels"];
			$this->reltypes = $this->reltypes + $arr["property"]["configured_rel_names"];
		};
		$atc = new add_tree_conf();
		$filt = false;
		if (($adc_id = $atc->get_current_conf()))
		{
			$adc = obj($adc_id);
			$filt = $atc->get_usable_filter($adc_id);
		}
		if($filt)
		{
			$this->true_rel_classes = $atc->can_access_classes($adc, $this->rel_classes, false);
			$al_tmp = $atc->can_access_classes($adc, array(0 => $this->true_rel_classes[0]), true);
			$this->true_rel_classes[0] = $al_tmp[0];
			$tmp = array();
			foreach($this->clids as $key => $val)
			{
				if (array_key_exists($key, $filt))
				{
					$tmp[$key] = $val;
				}
			}
		}
		else
		{
			$this->true_rel_classes = $this->rel_classes;
		}
		foreach($this->clids as $key => $val)
		{
			$this->clid_list .= 'clids['.$key.'] = "'.$val.'";'."\n";
		}
		foreach($this->true_rel_classes as $id => $val)
		{
			asort($val);
			if($id == 0 || $id == RELTYPE_BROTHER)
			{
				$val = array("capt_new_object" => aw_html_entity_decode(t("Objekti t&uuml;&uuml;p"))) + $val;
			}
			$this->true_rel_classes[$id] = $val;
		}
	}

	function _show_search($arr)
	{
		$pr = array();
		$this->reltype = $arr["request"]["reltype"];
		$props = $this->_init_search($arr);

		$tb = $this->_make_toolbar($arr);
		$this->read_template("rel_search.tpl");
		$req = safe_array($arr["request"]);
		unset($req["action"]);
		unset($req["return_url"]);
		$reforb = $this->mk_reforb("change", array("no_reforb" => 1, "search" => 1) + $req, $req["class"]);
		$defcs = array(CL_IMAGE => "image.default_folder", CL_FILE => "file.default_folder", CL_EXTLINK => "links.default_folder");
		$def_str = "";
		$return_url = get_ru();
		foreach($defcs as $def_clid => $def_ini)
		{
			$def_val = aw_ini_get($def_ini);
			if (is_oid($def_val) && $this->can("view", $def_val) && $this->can("add", $def_val))
			{
				$this->vars(array(
					"def_parent" => $def_val,
					"id" => $arr["obj_inst"]->id(),
					"return_url" => urlencode($return_url),
					"def_fld_clid" => $def_clid
				));
				$def_str .= $this->parse("HAS_DEF_FOLDER");
			}
		}
		$this->vars(array(
			"HAS_DEF_FOLDER" => $def_str,
			"parent" => $this->parent,
			"clids" => $this->clid_list,
			"period" => automatweb::$request->arg("period"),
			"id" => $arr["obj_inst"]->id(),
			"saveurl" => $this->mk_my_orb("submit", array("reltype" => $this->reltype, "group" => $req["group"], "return_url" => get_ru(), "reforb" => 1, "id" => $req["id"]), $req["class"]),
		));
		$this->vars["saveurl"] = aw_url_change_var("class",$req["class"],$this->vars["saveurl"]);
		$tb->add_cdata($this->parse());
		$pr = array(
			"rel_toolbar" => array(
				"name" => "rel_toolbar",
				"type" => "toolbar",
				"no_caption" => 1,
				"vcl_inst" => $tb,
			),
		) + $props;
		return $pr;
	}

	function _init_search($arr)
	{
//@property server type=select group=advsearch
//@caption Server
		$rval = array();
		$rval["srch"] = array(
			"name" => "srch",
			"type" => "hidden",
			"value" => 1,
		);
		$o = obj($arr["request"]["id"]);
		if($arr["request"]["return_url"] && $o->class_id() == CL_DOCUMENT)
		$rval["link"] = array(
			"name" => "link",
			"type" => "text",
			"caption" => "",
			"value" => html::href(array("target" => "aliasmgr", "url" => urldecode($arr["request"]["return_url"]), "caption" => t("Tagasi")))
		);
		$rval["name"] = array(
			"name" => "name",
			"type" => "textbox",
			"caption" => t("Nimi"),
			"value" => automatweb::$request->arg("name"),
			"post_append_text" => "<script language=javascript>el=document.getElementById('name');el.focus();</script>"
		);
		$rval["comment"] = array(
			"name" => "comment",
			"type" => "textbox",
			"caption" => t("Kommentaar"),
			"value" => automatweb::$request->arg("comment"),
		);
//@property class_id type=select multiple=1 size=10 group=search,advsearch
//@caption T&uuml;&uuml;p
		$rval["oid"] = array(
			"name" => "oid",
			"type" => "textbox",
			"caption" => t("OID"),
			"value" => automatweb::$request->arg("oid"),
		);
		$rval["createdby"] = array(
			"name" => "createdby",
			"type" => "textbox",
			"caption" => t("Looja"),
			"value" => automatweb::$request->arg("createdby"),
		);
		$rval["modifiedby"] = array(
			"name" => "modifiedby",
			"type" => "textbox",
			"caption" => t("Muutja"),
			"value" => automatweb::$request->arg("modifiedby"),
		);
		$rval["sparent"] = array(
			"name" => "sparent",
			"type" => "textbox",
			"caption" => t("Asukoht"),
			"value" => automatweb::$request->arg("sparent"),
		);
		$rval["status"] = array(
			"name" => "status",
			"type" => "chooser",
			"caption" => t("Staatus"),
			"options" => array(
				"3" => t("K&otilde;ik"),
				"2" => t("Aktiivsed"),
				"1" => t("Deaktiivsed"),
			),
			"value" => automatweb::$request->arg("status"),
		);
//@property alias type=textbox group=advsearch
//@caption Alias
		$lg = new languages();
		$rval["lang_id"] = array(
			"name" => "lang_id",
			"type" => "chooser",
			"caption" => t("Keel"),
			"options" => $lg->get_list(array("ignore_status" => true)),
			"value" => automatweb::$request->arg("lang_id"),
		);
//@property site_id type=select group=advsearch
//@caption Saidi ID
		$rval["search_bros"] = array(
			"name" => "search_bros",
			"type" => "checkbox",
			"ch_value" => 1,
			"caption" => t("Otsi vendi"),
			"checked" => automatweb::$request->arg("search_bros"),
		);
		$rval["searched"] = array(
			"name" => "searched",
			"type" => "hidden",
			"value"=> 1,
		);
		$rval["sbt"] = array(
			"name" => "sbt",
			"type" => "submit",
			"caption" => t("Otsi"),
		);

		$this->_init_search_fields($arr["request"]);
		if($this->do_search)
		{
			$tbl = $this->_get_search_table($arr);
			$rval["result_table"] = array(
				"name" => "result_table",
				"type" => "table",
				"no_caption" => 1,
				"vcl_inst" => $tbl,
			);
		}
		return $rval;
	}

	function _get_search_table($arr)
	{
		$t = new vcl_table();
		$t->define_field(array(
			"name" => "oid",
			"caption" => t("ID"),
			"align" => "center",
			"sortable" => 1,
		));
		$t->define_field(array(
			"name" => "icon",
			"caption" => t("&nbsp;"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"sortable" => 1,
		));
		$t->define_field(array(
			"name" => "lang_id",
			"caption" => t("Keel"),
			"align" => "center",
			"sortable" => 1,
		));
		$t->define_field(array(
			"name" => "class_id",
			"caption" => t("T&uuml;&uuml;p"),
			"sortable" => 1,
		));
		$t->define_field(array(
			"name" => "location",
			"caption" => t("Asukoht"),
			"sortable" => 1,
		));
		$t->define_field(array(
			"name" => "created",
			"caption" => t("Loodud"),
			"type" => "time",
			"format" => "d.m.y / H:i",
			"sortable" => 1,
		));
		$t->define_field(array(
			"name" => "createdby",
			"caption" => t("Looja"),
			"sortable" => 1,
		));
		$t->define_field(array(
			"name" => "modified",
			"caption" => t("Muudetud"),
			"type" => "time",
			"format" => "d.m.y / H:i",
			"sortable" => 1,
		));
		$t->define_field(array(
			"name" => "modifiedby",
			"caption" => t("Muutja"),
			"sortable" => 1,
		));

		$t->define_field(array(
			"name" => "change",
			"caption" => t("<a href='javascript:selall(\"sel\")'>Vali</a>"),
			"align" => "center",
			"talign" => "center",
			"chgbgcolor" => "cutcopied",
		));


		if ($this->do_search)
		{
			$s_args = array(
				"lang_id" => array(),
				"site_id" => array(),
			);
			foreach($this->qparts as $qkey => $qvalue)
			{
				$s_args[$qkey] = $qvalue;
			};

			// 3 - ignore status
			if (ifset($s_args, "status") == 3)
			{
				unset($s_args["status"]);
			};

			$s_args["limit"] = 500;

			$_tmp = $this->_search_mk_call("objects", "storage_query", $s_args);

			$this->search_results = count($_tmp);
			$clinf = aw_ini_get("classes");

			foreach($_tmp as $id => $item)
			{
				$type = $clinf[$item["class_id"]]["name"];
				$icon = sprintf("<img src='%s' alt='$type' title='$type'>",icons::get_icon_url($item["class_id"]));
				$t->define_data(array(
					"name" => html::href(array(
						"caption" => $item["name"],
						"url" => $this->mk_my_orb("change",array("id" => $id),$item["class_id"]),
					)),
					"lang_id" => $item["lang_id"],
					"oid" => $id,
					"icon" => $icon,
					"created" => $item["created"],
					"createdby" => $item["createdby"],
					"modifiedby" => $item["modifiedby"],
					"modified" => $item["modified"],
					"class_id" => $clinf[$item["class_id"]]["name"],
					"location" => aw_html_entity_decode($item["path_str"]),
					"change" => "<input type='checkbox' name='check' value='$id'>",
				));
			}
		}
		return $t;
	}

	// generates contents for the class picker drop-down menu
	function _get_s_class_id()
	{
		$tar = array(0 => LC_OBJECTS_ALL) + get_class_picker(array(
			"only_addable" => 1
		));

		$atc_inst = new add_tree_conf();
		$atc_id = $atc_inst->get_current_conf();
		if (is_oid($atc_id))
		{
			$atc = obj($atc_id);

			$tmp = array();
			foreach($tar as $clid => $cln)
			{
				if ($atc_inst->can_access_class($atc, $clid))
				{
					$tmp[$clid] = $cln;
				}
			}
			$tar = $tmp;
		}

		return $tar;
	}

	function _search_mk_call($class, $action, $params)
	{
		$_parms = array(
			"class" => $class,
			"action" => $action,
			"params" => $params
		);
		if ($this->server_id)
		{
			$_parms["method"] = "xmlrpc";
			$_parms["login_obj"] = $this->server_id;
		}
		$ret =  $this->do_orb_method_call($_parms);
		return $ret;
	}

	function _init_search_fields($arr)
	{
		$this->do_search = false;
		$parts = array();
		$string_fields = array("name","createdby","modifiedby","comment","alias");
		$numeric_fields = array("oid","status","lang_id");
		foreach($string_fields as $string_field)
		{
			if (!empty($arr[$string_field]))
			{
				$r = explode(",", $arr[$string_field]);
				array_walk($r, create_function('&$a','$a = trim($a);'));
				$parts[$string_field] = map('%%%s%%', $r);

			}
		}
		foreach($numeric_fields as $numeric_field)
		{
			if (!empty($arr[$numeric_field]))
			{
				$r = explode(",", $arr[$numeric_field]);
				array_walk($r, create_function('&$a','$a = trim($a);'));
				$parts[$numeric_field] = $r;
			}
		}

		if (!empty($arr["sparent"]))
		{
			$r = explode(",", $arr["sparent"]);
			$r = array_walk($r, create_function('&$a','$a = trim($a);'));
			$parts["parent"] = $r;
		}

		if (!empty($arr["aselect"]))
		{
			$parts["class_id"] = $arr["aselect"];
		}

		$this->server_id = false;
		if (!empty($arr["server"]))
		{
			$this->server_id = $arr["server"];
		}

		$this->qparts = $parts;
		if(!empty($arr["searched"]))
		{
			$this->do_search = true;
		}
	}

	function _make_toolbar($arr)
	{
		$tb = new toolbar();
		if(empty($_SESSION["rel_reverse"][$arr["request"]["id"]]))
		{
			$objtype = ifset($arr, "request", "aselect");
			if (is_array($objtype) && (count($objtype) == 1))
			{
				$objtype = array_pop($objtype);
			}
			elseif (is_numeric($objtype = ltrim($objtype,',')))
			{
			}
			else
			{
				$objtype = NULL;
			}
			$this->read_template("selectboxes.tpl");
			$rels1 = "";
			$defaults1 = "";

			foreach($this->reltypes as $k => $v)
			{
				$single_select = "capt_new_object";

				if (isset($this->true_rel_classes[$k]))
				{
					$vals = $this->true_rel_classes[$k];
					$vals = $this->mk_kstring($vals);

					if (isset($this->true_rel_classes[$k][$objtype]))
					{
						$sele = $objtype;
					}
					else
					{
						$sele = key($this->true_rel_classes[$k]);
					}
				}
				else
				{
					$sele = $vals = null;
				}

				if(!empty($vals))
				{
					$rels1 .= 'listB.addOptions("'.$k.'",'.$vals.");\n";
				}

				if ($objtype && $this->reltype == $k)
				{
					$defaults1 .= 'listB.setDefaultOption("'.$k.'","'.$objtype.'");'."\n";
				}
				else
				{
					$defaults1 .= 'listB.setDefaultOption("'.$k.'","'.($sele ? $sele : $single_select).'");'."\n";
				}
			}

			$rels1 .= 'listB.addOptions("_","'.aw_html_entity_decode(t("Objekti t&uuml;&uuml;p")).'","capt_new_object"'.");\n";
			$defaults1 .= 'listB.setDefaultOption("_","capt_new_object");'."\n";

			$this->vars(array(
				"parent" => $this->parent,
				"rels1" => $rels1,
				"defaults1" => $defaults1,
			));

			$tb->add_cdata($this->parse());

			$tb->add_cdata(
				html::select(array(
					"options" => (count($this->reltypes) <= 1) ? $this->reltypes :(array('_' => t('Seose t&uuml;&uuml;p')) + $this->reltypes),
					"name" => "reltype",
					"selected" => isset($this->reltype) ? $this->reltype : NULL,
					'onchange' => "listB.populate();",
				))
			);
			$tb->add_cdata('<select NAME="aselect" style="width:200px"><script LANGUAGE="JavaScript">listB.printOptions()</SCRIPT></select>');
			$ru_var = get_ru();
			if(isset($arr["request"]["searched"]) && $arr["request"]["searched"] == 1)
			{
				$ru_var = urlencode($arr["request"]["return_url"]);
			}
			$tb->add_cdata('<input TYPE="hidden" VALUE="'.$ru_var.'" NAME="return_url" />');
			$tb->add_button(array(
				"name" => "new",
				"img" => "new.gif",
				"url" => "javascript:create_new_object()",
				"tooltip" => t("Lisa uus objekt"),
			));
			if(!empty($arr["request"]["srch"]))
			{
				$tb->add_button(array(
					"name" => "search",
					"img" => "search.gif",
					"tooltip" => t("Otsi"),
					"url" => "javascript:if (document.changeform.reltype.value!='_') {document.changeform.submit();} else alert('Vali seosetuup!')",
				));
			}
			else
			{
				$tb->add_button(array(
					"name" => "search",
					"img" => "search.gif",
					"tooltip" => t("Otsi"),
					"url" => "javascript:search_for_object()",
				));
			}

			$tb->add_separator();

			$tb->add_button(array(
				"name" => "refresh",
				"img" => "refresh.gif",
				"tooltip" => t("Uuenda"),
				"url" => "javascript:window.location.reload()",
			));

			if(isset($arr["request"]["srch"]) && $arr["request"]["srch"] == 1)
			{
				if (isset($this->search_results) && $this->search_results > 0)
				{
					$tb->add_button(array(
						"name" => "save",
						"tooltip" => t("Loo seos(ed)"),
						"url" => "javascript:aw_save()",
						"img" => "save.gif",
					));
				}
			}
			else
			{
				$tb->add_button(array(
					"name" => "save",
					"img" => "save.gif",
					"tooltip" => t("Salvesta"),
					"url" => "javascript:document.changeform.submit();",
				));

				$tb->add_button(array(
					"name" => "delete",
					"img" => "delete.gif",
					"tooltip" => t("Kustuta seos(ed)"),
					"url" => "javascript:awdelete()",
				));
			}

			$tb->add_separator();
			$tb->add_button(array(
				"name" => "rel_cut",
				"img" => "cut.gif",
				"tooltip" => t("L&otilde;ika seos(ed)"),
				"action" => "rel_cut",
			));
			$tb->add_button(array(
				"name" => "rel_copy",
				"img" => "copy.gif",
				"tooltip" => t("Kopeeri seos(ed)"),
				"action" => "rel_copy",
			));
			if (isset($_SESSION["rel_cut"]) && (is_array($_SESSION["rel_cut"]) && count($_SESSION["rel_cut"])) || isset($_SESSION["rel_copied"]) && (is_array($_SESSION["rel_copied"]) && count($_SESSION["rel_copied"])))
			{
				$tb->add_button(array(
					"name" => "rel_paste",
					"img" => "paste.gif",
					"tooltip" => t("Kleebi seos(ed)"),
					"action" => "rel_paste",
				));
			}
			$tb->add_separator();
		}
		$tb->add_button(array(
			"name" => "rel_reverse",
			"img" => "connectionmanager.gif",
			"tooltip" => t("N&auml;ita teistpidi seoseid"),
			"action" => "rel_reverse",
		));
		return $tb;
	}

	function mk_kstring($arr)
	{
		$alls = array();
		if (is_array($arr))
		{
			foreach($arr as $key => $val)
			{
				$alls[] ='"'.aw_html_entity_decode($val).'"';
				$alls[] ='"'.$key.'"';
			}
		}
		return implode(',', $alls);
	}

	/**
		@attrib name=list_aliases all_args=1
	**/
	function list_aliases($arr)
	{
		$o = obj($arr["id"]);
		$arr = array(
			"obj_inst" => $o,
			"request" => $arr
		);
		if(in_array($arr["obj_inst"]->class_id() , get_container_classes()))
		{
			$this->parent = $arr["obj_inst"]->id();
		}
		else
		{
			$this->parent = $arr["obj_inst"]->parent();
		}
		$this->_init_relations($arr);
		if(!empty($arr["request"]["srch"]))
		{
			$d = $this->_show_search($arr);
			$htmlc = new htmlclient();
			$htmlc->start_output();

			foreach($d as $nm => $da)
			{
				$htmlc->add_property($da);
			}
			$clss = aw_ini_get("classes");
			$htmlc->finish_output(array("data" => array(
					"class" => basename($clss[$arr["obj_inst"]->class_id()]["class"]),
					"action" => "submit_srch",
					"id" => $arr["id"],
				),
			));

			$res = $htmlc->get_result(array(
				"form_only" => 1
			));

		}
		else
		{
			$d = $this->_show_relations($arr);
			$res = $d["rel_toolbar"]["vcl_inst"]->get_toolbar();
			$res .= $d["rel_table"]["vcl_inst"]->draw();
		}
		$this->read_template("aliases.tpl");
		$this->vars(array("mgr" => $res));
		return $this->parse();
	}

	function _show_relations($arr)
	{
		if (!is_oid($arr["obj_inst"]->id()))
		{
			return;
		}

		$pr = array();
		$tb = $this->_make_toolbar($arr);
		$this->read_template("list_aliases.tpl");

		$this->vars(array(
			"id" => $arr["obj_inst"]->id(),
		));

		// table part
		$tbl = new vcl_table();
		$tbl->parse_xml_def(aw_ini_get("basedir")."/xml/generic_table.xml");

		$flds = array(
			"icon" => array(
				"name" => "icon",
				"caption" => t("&nbsp;"),
				"talign" => "center",
				"align" => "center",
				"nowrap" => "1",
				"width" => "25",
			),
			"name" => array(
				"name" => "name",
				"caption" => t("Nimi"),
				"talign" => "center",
				"sortable" => 1,
			),
			"lang" => array(
				"name" => "lang",
				"caption" => t("Keel"),
				"talign" => "center",
				"align" => "center",
				"sortable" => 1,
			),
			"comment" => array(
				"name" => "comment",
				"caption" => t("Muu info"),
				"talign" => "center",
				"sortable" => 1,
			),
			"alias" => array(
				"name" => "alias",
				"caption" => t("Alias"),
				"talign" => "center",
				"width" => 50,
				"align" => "center",
				"class" => "celltext",
			),
			"link" => array(
				"name" => "link",
				"caption" => t("Link"),
				"talign" => "center",
				"width" => 50,
				"align" => "center",
				"class" => "celltext",
				"nowrap" => "1",
			),
			"cache" => array(
				"name" => "cache",
				"caption" => t("Cache"),
				"talign" => "center",
				"width" => 50,
				"align" => "center",
				"class" => "celltext",
				"nowrap" => "1",
			),
			"modifiedby" => array(
				"name" => "modifiedby",
				"caption" => t("Muutja"),
				"align" => "center",
				"talign" => "center",
				"nowrap" => "1",
				"sortable" => 1,
			),
			"modified" => array(
				"name" => "modified",
				"caption" => t("Muudetud"),
				"talign" => "center",
				"align" => "center",
				"nowrap" => "1",
				"sortable" => 1,
				"numeric" => 1,
				"type" => "time",
				"format" => "d.m.y / H:i"
			),
			"title" => array(
				"name" => "title",
				"caption" => t("T&uuml;&uuml;p"),
				"talign" => "center",
				"align" => "center",
				"nowrap" => "1",
				"sortable" => 1,
			),
			"reltype" => array(
				"name" => "reltype",
				"caption" => t("Seose t&uuml;&uuml;p"),
				"talign" => "center",
				"align" => "center",
				"nowrap" => "1",
				"filter" => "automatic",
			),
			"location" => array(
				"name" => "location",
				"caption" => t("Asukoht"),
				"align" => "center"
			)
		);
		foreach(aw_ini_get("relationmgr.table_fields") as $fld)
		{
			$tbl->define_field($flds[$fld]);
		}

		$tbl->define_chooser(array(
			"name" => "check",
			"field" => "id",
		));

		$tbl->define_pageselector(array(
			"type" => "lb",
			"records_per_page" => 100,
		));

		$alinks = $arr["obj_inst"]->meta("aliaslinks");

		if(!empty($_SESSION["rel_reverse"][$arr["request"]["id"]]))
		{
			$conn = $arr["obj_inst"]->connections_to();
			$cn = "from";
		}
		else
		{
			$conn = $arr["obj_inst"]->connections_from();
			$cn = "to";
		}

		$conn_ids = array();
		foreach($conn as $alias)
		{
			$oid = $alias->prop($cn);
			$conn_ids[$oid] = $oid;
			$conns[$oid] = $alias;
		}

		if(count($conn_ids))
		{
			$loader_ol = new object_list(array(
				"oid" => $conn_ids
			));
		}
		else
		{
			$loader_ol = new object_list();
		}

		foreach($loader_ol->arr() as $oid => $target_obj)
		{
			$alias = $conns[$oid];
			$adat = array(
				"createdby" => $target_obj->prop("createdby"),
				"created" => $target_obj->prop("created"),
				"modifiedby" => $target_obj->prop("modifiedby"),
				"modified" => $target_obj->prop("modified"),
				"comment" => $target_obj->prop("comment"),
				"location" => $target_obj->path_str(array("max_len" => 3, "path_only" => true))
			);
			$adat["lang"] = $target_obj->lang();
			$aclid = $target_obj->prop("class_id");

			$edfile = aw_ini_get("classes.{$aclid}.file");
			if ($aclid == CL_DOCUMENT)
			{
				$edfile = "doc";
			}

			$ch = $this->mk_my_orb("change", array("id" => $alias->prop($cn), "return_url" => get_ru()), $edfile);
			$reltype_id = $alias->prop("reltype");

			$adat["icon"] = html::img(array(
			"url" => icons::get_icon_url($target_obj),
			));

			if ($reltype_id == 0)
			{
				$astr = "";
				if (aw_ini_isset("classes.{$aclid}.alias"))
				{
					list($astr) = explode(",", aw_ini_get("classes.{$aclid}.alias"));
				}
				elseif (aw_ini_isset("classes.{$aclid}.old_alias"))
				{
					list($astr) = explode(",", aw_ini_get("classes.{$aclid}.old_alias"));
				}

				$astr = sprintf("#%s%d#", $astr, $alias->prop("idx"));
				$adat["alias"] = sprintf("<input type='text' size='10' value='%s' onClick='this.select()' onblur='this.value=\"%s\"'>", $astr, $astr);
			}

			$adat["link"] = html::checkbox(array(
				"name" => "link[".$alias->prop($cn)."]",
				"value" => 1,
				"checked" => !empty($alinks[$alias->prop($cn)])
			));

			$adat["title"] = aw_ini_get("classes.{$aclid}.name");

			// for the chooser
			$adat["id"] = $alias->prop("id");

			$adat["name"] = html::href(array(
				"url" => $ch,
				"caption" => parse_obj_name($alias->prop($cn.".name")),
			));

			$adat["cache"] = html::checkbox(array(
				"name" => "cache[".$alias->prop($cn)."]",
				"value" => 1,
				"checked" => ($alias->prop("cached") == 1)
			));

			if($cn === "from")
			{
				$reltypes = $this->_get_reltypes($alias->prop($cn.".class_id"));
				$type_str = $reltypes[$reltype_id];
			}
			else
			{
				$type_str = $this->reltypes[$reltype_id];
			}

			if ($alias->prop("relobj_id"))
			{
				$adat["reltype"] = html::href(array(
					"url" => $this->mk_my_orb("change", array("id" => $alias->prop("relobj_id"),"return_url" => get_ru()), $edfile),
					"caption" => $type_str,
				));
			}
			else
			{
				$adat["reltype"] = $type_str;
			}
			$tbl->define_data($adat);
		}

		$defcs = array(CL_IMAGE => "image.default_folder", CL_FILE => "file.default_folder", CL_EXTLINK => "links.default_folder");
		$def_str = "";
		$return_url = get_ru();
		foreach($defcs as $def_clid => $def_ini)
		{
			$def_val = aw_ini_get($def_ini);
			if (is_oid($def_val) && $this->can("view", $def_val) && $this->can("add", $def_val))
			{
				$this->vars(array(
					"def_parent" => $def_val,
					"id" => $arr["obj_inst"]->id(),
					"return_url" => urlencode($return_url),
					"def_fld_clid" => $def_clid
				));
				$def_str .= $this->parse("HAS_DEF_FOLDER");
			}
		}
		$this->vars(array(
			"id" => $arr["obj_inst"]->id(),
		));

		$req = safe_array($arr["request"]);
		unset($req["action"]);
		if (!is_array($req))
		{
			$req = array();
		}
		$reforb = $this->mk_reforb("submit", $req + array("reforb" => 1), $req["class"]);
		$this->vars(array(
			"HAS_DEF_FOLDER" => $def_str,
			"class_ids" => $this->clid_list,
			"id" => $arr["obj_inst"]->id(),
			"return_url" => urlencode(get_ru()),
			"search_url" => aw_ini_get("baseurl").aw_url_change_var(array("srch" => 1)),
		));
		$tbl->set_header($this->parse());
		$tbl->set_default_sortby("title");
		$tbl->sort_by();
		$pr["rel_toolbar"] = array(
			"name" => "rel_toolbar",
			"type" => "toolbar",
			"no_caption" => 1,
			"vcl_inst" => $tb,
		);
		$pr["rel_table"] = array(
			"name" => "rel_table",
			"type" => "table",
			"vcl_inst" => $tbl,
			"no_caption" => 1,
		);
		return $pr;
	}

	function process_vcl_property($arr)
	{
		$arr["request"] = safe_array($arr["request"]) + $_REQUEST;
		if (isset($arr["request"]["subaction"]) and $arr["request"]["subaction"] === "delete")
		{
			$to_delete = new aw_array($arr["request"]["check"]);
			foreach($to_delete->get() as $alias_id)
			{
				$c = new connection($alias_id);
				$c->delete();
			}
		}
		elseif(!empty($arr["request"]["alias"]))
		{
			$alias = $arr["request"]["alias"];
			$reltype = $arr["request"]["reltype"];
			$aliases = explode(",", $alias);
			if ($reltype === "_")
			{
				$reltype = "";
			}

			foreach($aliases as $oalias)
			{
				$arr["obj_inst"]->connect(array(
					"to" => $oalias,
					"reltype" => $reltype,
					"data" => automatweb::$request->arg("data")
				));
			}
		}

		if (isset($arr["request"]["link"]))
		{
			$arr["obj_inst"]->set_meta("aliaslinks", $arr["request"]["link"]);
		}

		$arr["obj_inst"]->save();
	}

	/**
	@attrib name=rel_reverse all_args=1
	**/
	function rel_reverse($arr)
	{
		$_SESSION["rel_reverse"][$arr["id"]] = $_SESSION["rel_reverse"][$arr["id"]]?0:1;
		return $this->mk_my_orb("disp_relmgr", array("id" => $arr["id"]));;
	}
}
