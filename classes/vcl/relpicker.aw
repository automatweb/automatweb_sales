<?php

class relpicker extends  core
{
	function relpicker()
	{
		$this->init("");
	}

	/**
		@attrib name=create_relpicker params=name api=1

		@param name required type=string
			String to indetify the relpicker

		@param reltype required type=string
			The reltype the relpicker uses

		@param oid required type=int
			The object's ID the relpicker picks relations for

		@param property required type=int
			The property's name that relpicker picks relations for

		@param multiple optional type=int

		@param size optional type=int
			Select size for multiple relpickers

		@param no_sel optional type=int
			Show no selection options

		@param automatic optional type=int

		@param no_edit optional type=int
			Don't show new/search/edit buttons

		@param no_search optional type=int
			Don't show search button

		@param add_edit_autoreturn optional type=int
			When adding a new related object or editing a selected one, return immediately back to caller view after saving

		@param options optional type=array
			Options to be displayed in the relpicker select box. Array(oid => caption).

		@param buttonspos optional type=string
			Position for buttons. Values: right, bottom. Default: right

		@param value optional
			Value for relpicker. array if multiple, int otherwise

		@param width optional type=int
			Select width

		@param disabled optional type=int

		@returns The HTML of the relpicker.

		@examples

		$relpicker = get_instance(CL_RELPICKER);
		$relpicker->create_relpicker(array(
			"name" => "myRelpicker",
			"reltype" => 1,
			"oid" => 123,
			"property" => "myProperty",
		));

		$myOptions = array(
			1 => "Object1",
			2 => "Object2",
			3 => "Object3",
		);
		$relpicker = get_instance(CL_RELPICKER);
		$relpicker->create_relpicker(array(
			"name" => "myRelpicker",
			"reltype" => "RELTYPE_FOO",
			"oid" => 123,
			"property" => "myProperty",
			"options" => $myOptions,
		));

	**/
	function create_relpicker($arr)
	{
		extract($arr);
		$oid = isset($arr["oid"]) ? $arr["oid"] : 0;
		$value = isset($arr["value"]) ? $arr["value"] : 0;
		$options = isset($arr["options"]) ? $arr["options"] : array();
		$no_sel = isset($arr["no_sel"]) ? $arr["no_sel"] : 0;
		$no_edit = isset($arr["no_edit"]) ? $arr["no_edit"] : 0;
		$no_search = isset($arr["no_search"]) ? $arr["no_search"] : 0;
		$disabled = isset($arr["disabled"]) ? $arr["disabled"] : 0;
		$multiple = isset($arr["multiple"]) ? $arr["multiple"] : 0;
		$size = isset($arr["size"]) ? $arr["size"] : 1;
		$width = isset($arr["width"]) ? $arr["width"] : 0;
		$automatic = isset($arr["automatic"]) ? $arr["automatic"] : 0;
		$buttonspos = isset($arr["buttonspos"]) ? $arr["buttonspos"] : 0;

		if(!$this->can("view", $oid))
		{
			return false;
		}

		$o = new object($oid);
		$relinfo = $o->get_relinfo();
		$clids = isset($relinfo[$reltype]["clid"]) ? $relinfo[$reltype]["clid"] : array();

		if($value)
		{
			$selected = $value;
		}
		elseif($o->is_property($property))
		{
			$selected = $o->prop($property);
		}
		else
		{
			$selected = 0;
		}

		if($no_sel != 1)
		{
			$options = array(0 => t("--vali--")) + $options;
		}

		// generate option list
		// if automatic is set, then create a list of all properties of that type
		if ($automatic)
		{
			foreach($clids as $clid)
			{
				if (!empty($clid))
				{
					$olist = new object_list(array(
						"class_id" => $clid,
						"brother_of" => new obj_predicate_prop("id")
					));
					$names = $olist->names();
					asort($names);
					$options = $options + $names;
				}
			}
		}
		else
		{
			$conns = $o->connections_from(array(
				"type" => $reltype
			));

			foreach($conns as $conn)
			{
				$options[$conn->prop("to")] = $conn->prop("to.name");
			}
		}

		$r = html::select(array(
			"name" => $name,
			"options" => $options,
			"selected" => $selected,
			"multiple" => $multiple,
			"size" => $size,
			"width" => $width,
			"disabled" => $disabled,
		));

		if($buttonspos === "bottom")
		{
			$r .= "<br>";
		}

		if (!$no_search)
		{
			$url = $this->mk_my_orb("do_search", array(
				"id" => $oid,
				"pn" => $name,
				"in_popup" => "1",
				"clid" => $clids,
				"multiple" => $multiple
			), "popup_search", false, true);

			$r .= " ".html::href(array(
				"url" => "javascript:aw_popup_scroll(\"$url\",\"Otsing\",".popup_search::PS_WIDTH.",".popup_search::PS_HEIGHT.")",
				"caption" => "<img src='".aw_ini_get("baseurl")."/automatweb/images/icons/search.gif' border=0>",
				"title" => t("Otsi"),
			));
		}

		if(!is_array($selected) && $this->can("edit", $selected) && !$no_edit)
		{
			$selected_obj = new object($selected);
			$selected_clid = $selected_obj->class_id();
			$r .= " ".html::href(array(
				"url" => $this->mk_my_orb("change", array(
					"id" => $selected,
					"save_autoreturn" => !empty($add_edit_autoreturn),
					"return_url" => get_ru()
				), $selected_clid),
				"caption" => "<img src='".aw_ini_get("baseurl")."/automatweb/images/icons/edit.gif' border=0>",
				"title" => t("Muuda")
			));
		}
		elseif(is_array($selected) && !$no_edit)
		{
			$pm = new popup_menu();
			$pm->begin_menu(str_replace(array("[", "]"), "", $name)."_rp_editbtn");
			foreach($selected as $id)
			{
				if($this->can("edit", $id))
				{
					$pm->add_item(array(
						"text" => obj($id)->name(),
						"link" => html::get_change_url($id, array("return_url" => get_ru())),
					));
				}
			}
			$r .= " ".$pm->get_menu(array(
				"icon" => "edit.gif",
			));
		}

		if(!$no_edit)
		{
			$clss = aw_ini_get("classes");
			if (count($clids) > 1)
			{
				$pm = new popup_menu();
				$pm->begin_menu($name."_relp_pop");
				foreach($clids as $clid)
				{
					$pm->add_item(array(
						"text" => $clss[$clid]["name"],
						"link" => html::get_new_url(
							$clid,
							$oid,
							array(
								"alias_to" => $oid,
								"alias_to_prop" => $property,
								"reltype" => $reltype,
								"save_autoreturn" => !empty($add_edit_autoreturn),
								"return_url" => get_ru()
							)
						)
					));
				}
				$r .= " ".$pm->get_menu(array(
					"icon" => "new.gif",
					"alt" => t("Lisa")
				));
			}
			else
			{
				foreach($clids as $clid)
				{
					$r .= " ".html::href(array(
						"url" => html::get_new_url(
							$clid,
							$oid,
							array(
								"alias_to_prop" => $property,
								"alias_to" => $oid,
								"reltype" => $reltype,
								"save_autoreturn" => !empty($add_edit_autoreturn),
								"return_url" => get_ru()
							)
						),
						"caption" => "<img src='".aw_ini_get("baseurl")."/automatweb/images/icons/new.gif' border=0>",
						"title" => sprintf(t("Lisa uus %s"), $clss[$_clid]["name"]),
					));
				}
			}
		}

		return $r;
	}

	function init_vcl_property($arr)
	{
		$prop = &$arr["property"];
		$this->obj = $arr["obj_inst"];
		if (isset($prop["mode"]) && $prop["mode"] === "autocomplete")
		{
			return $this->init_autocomplete_relpicker($arr);
		}

		$val = &$arr["property"];
		$val["post_append_text"] = isset($val["post_append_text"]) ? $val["post_append_text"] : "";
		if(isset($prop["no_sel"]) && $prop["no_sel"] == 1)
		{
			$options = array();
		}
		else
		{
			$options = array("0" => t("--vali--"));
		}
		$reltype = isset($prop["reltype"]) ? $prop["reltype"] : null;
		// generate option list
		if (isset($prop["options"]) && is_array($prop["options"]))
		{
			$val["type"] = "select";
		}
		else
		{
			// if automatic is set, then create a list of all properties of that type
			if (isset($prop["automatic"]))
			{
				$clid = $arr["relinfo"][$reltype]["clid"];
				$val["type"] = "select";
				if (!empty($clid))
				{
					$olist = new object_list(array(
						"class_id" => $clid,
						"site_id" => array(),
						"lang_id" => array(),
						"brother_of" => new obj_predicate_prop("id")
					));
					$names = $olist->names();
					asort($names);
					$val["options"] = $options + $names;
					/*if ($arr["id"])
					{
						$o = obj($arr["id"]);
						$conn = $o->connections_from(array(
							"type" => $reltype
						));
						$sel = array();
						foreach($conn as $c)
						{
							$sel[$c->prop("to")] = $c->prop("to");
						}
						$val["value"] = $sel;
					};*/
					// since when do automatic relpickers get all relations selected?!?!
				}
			}
			else
			{
				if ($arr["id"])
				{
					$o = obj($arr["id"]);
					$conn = array();

					if (!empty($val["clid"]))
					{
						$clids = (array) $val["clid"];
						$error = false;

						foreach ($clids as $key => $clid)
						{
							$clids[$key] = ((strlen((int) $clid)) === strlen($clid)) ? (int) $clid : constant($clid);
							$error = empty($clids[$key]) ? true : $error;
						}

						if (!$error)
						{
							$conn = $o->connections_from(array(
								"to.class_id" => $clids,
								"type" => $reltype
							));
						}
					}
					else
					{
						$conn = $o->connections_from(array(
							"type" => $reltype
						));
					}

					foreach($conn as $c)
					{
						$options[$c->prop("to")] = $c->prop("to.name");
					}
					$val["options"] = $options;
				}
			}
		}

		$val["type"] = (isset($val["display"]) && $val["display"] === "radio") ? "chooser" : "select";

		if ($val["type"] === "select" /*&& is_object($this->obj)*/)
		{
			$clid = isset($arr["relinfo"][$reltype]["clid"]) ? (array)$arr["relinfo"][$reltype]["clid"] : array();
			if(!is_object($arr["obj_inst"]) || empty($val["parent"]))
			{
				// I only want the search button. No edit or new buttons!
				if (empty($val["no_search"]))
				{
					$url = $this->mk_my_orb("do_search", array(
						"id" => is_object($arr["obj_inst"]) ? $arr["obj_inst"]->id() : null,
						"pn" => $arr["property"]["name"],
						"in_popup" => "1",
						"clid" => $clid,
						"multiple" => isset($arr["property"]["multiple"]) && $arr["property"]["multiple"] ? $arr["property"]["multiple"] : NULL
					), "popup_search", false, true);

					$val["post_append_text"] .= " ".html::href(array(
						"url" => "javascript:aw_popup_scroll('$url','Otsing',".popup_search::PS_WIDTH.",".popup_search::PS_HEIGHT.")",
						"caption" => "<img src='".aw_ini_get("baseurl")."/automatweb/images/icons/search.gif' border=0>",
						"title" => t("Otsi"),
					));
				}
			}//selle paneks peaaegu alati t88le kui suudaks loadida relpickereid
			elseif(empty($val["no_search"]))
			{
				$ps = new popup_search();
				$ps->set_class_id($clid);
				$ps->set_id($arr["obj_inst"]->id());
				$ps->set_reload_property($val["name"]);
				$ps->set_property($arr["property"]["name"]);
				if(!empty($val["parent"]))
				{
					$ps->set_reload_layout($val["parent"]);
				}
				else
				{
					$ps->set_reload_property($val["name"]);
				}
				$prop["post_append_text"] = $ps->get_search_button();
			}
		}

		if (
			isset($val["type"]) && $val["type"] === "select" &&
			is_object($this->obj) && ((isset($val["value"]) && is_oid($val["value"]) && $this->can("edit", $val["value"])) ||
			(is_object($this->obj) && is_oid($this->obj->id()) && $this->obj->is_property($val["name"]) && is_oid($this->obj->prop($val["name"])) && $this->can("edit", $this->obj->prop($val["name"]))) ) &&
			empty($val["no_edit"])
		)
		{
			try
			{
				$change_url = html::get_change_url($this->obj->prop($val["name"]), array(
					"save_autoreturn" => !empty($val["add_edit_autoreturn"]),
					"return_url" => get_ru()
				));
				$val["post_append_text"] .= " ".html::href(array(
					"url" => $change_url,
					"caption" => "<img src='".aw_ini_get("baseurl")."/automatweb/images/icons/edit.gif' border=0>",
					"title" => t("Muuda")
				));
			}
			catch (Exception $e)
			{
				$val["post_append_text"] .= " " . t("Objekt on kustutatud");
			}
		}
		elseif (
			isset($val["type"]) && $val["type"] === "select" &&
			is_object($this->obj) && ((isset($val["value"]) && is_array($val["value"]) && $this->can("edit", $val["value"])) ||
			(is_object($this->obj) && is_oid($this->obj->id()) && $this->obj->is_property($val["name"]) && is_array($this->obj->prop($val["name"])))) &&
			empty($val["no_edit"])
		)
		{
			$pm = new popup_menu();
			$pm->begin_menu(str_replace(array("[", "]"), "", $val["name"])."_rp_editbtn");
			foreach($this->obj->prop($val["name"]) as $id)
			{
				if($this->can("edit", $id))
				{
					$pm->add_item(array(
						"text" => obj($id)->name(),
						"link" => html::get_change_url($id, array("return_url" => get_ru()))
					));
				}
			}
			$val["post_append_text"] .= " ".$pm->get_menu(array(
				"icon" => "edit.gif"
			));
		}

		$allow_delete = false;
		if(
			isset($val["delete_button"]) && $val["delete_button"] ||
			isset($val["delete_rels_button"]) && $val["delete_rels_button"] ||
			isset($val["delete_rels_popup_button"]) && $val["delete_rels_popup_button"]
		)
		{
			$oids = array();
			$allow_delete = true;

			if(isset($val["value"]) && is_oid($val["value"]))
			{
				$oids[] = $val["value"];
			}
			else
			if(isset($val["value"]) && is_array($val["value"]))
			{
				$oids = $val["value"];
			}
			else
			if(is_object($this->obj) && is_oid($this->obj->id()) && $this->obj->is_property($val["name"]) && $this->can("edit", $this->obj->prop($val["name"])))
			{
				$oids[] = $this->obj->prop($val["name"]);
			}
			else
			if(is_object($this->obj) && is_oid($this->obj->id()) && $this->obj->is_property($val["name"]) && is_array($this->obj->prop($val["name"])))
			{
				$oids = $this->obj->prop($val["name"]);
			}
			else
			{
				$allow_delete = false;
			}

			foreach($oids as $oid)
			{
				if(!$this->can("edit", $oid))
				{
					$allow_delete = false;
					break;
				}
			}
		}

		if($val["type"] === "select" && is_object($this->obj) && $allow_delete)
		{
			if(!empty($val["delete_button"]))
			{
				foreach($oids as $i => $oid)
				{
					$awucv["sel[".$i."]"] = $oid;
				}
				$awucv["action"] = "delete_objects";
				$awucv["post_ru"] = post_ru();
				$val["post_append_text"] .= " ".html::href(array(
					"url" => aw_url_change_var($awucv),
					//"url" => "javascript:submit_change_form('delete_objects')",
					"caption" => "<img src='".aw_ini_get("baseurl")."/automatweb/images/icons/delete.gif' border=0>",
					"title" => t("Kustuta valitud objektid"),
					"onclick" => "if(!alert('".t("Oled kindel, et soovid valitud objektid kustutada?")."')) { return false; };",
				));
			}

			if(!empty($val["delete_rels_button"]))
			{
				foreach($oids as $i => $oid)
				{
					$awucv["sel[".$i."]"] = $oid;
				}
				$awucv["action"] = "delete_rels";
				$awucv["post_ru"] = post_ru();
				$val["post_append_text"] .= " ".html::href(array(
					"url" => aw_url_change_var($awucv),
					//"url" => "javascript:submit_change_form('delete_rels')",
					"caption" => "<img src='".aw_ini_get("baseurl")."/automatweb/images/icons/delete.gif' border=0>",
					"title" => t("Kustuta valitud seosed"),
					"onclick" => "if(!alert('".t("Oled kindel, et soovid valitud seosed kustutada?")."')) { return false; };",
				));
			}

			if(!empty($val["delete_rels_popup_button"]))
			{
				$url = $this->mk_my_orb("rels_del_popup", array(
					"id" => $arr["id"],
					"in_popup" => "1",
					"return_url" => get_ru(),
					"reltype" => $arr["prop"]["reltype"]
				));
				$button = '<a title="Eemalda" alt="Eemalda" href="javascript:void();"
					onclick="window.open(\''.$url.'\',\'\', \'toolbar=no, directories=no, status=no, location=no, resizable=yes, scrollbars=yes, menubar=no, height=600, width=800\');">
					<img src="'.aw_ini_get("baseurl").'/automatweb/images/icons/delete.gif" border=0></a>';
				$val["post_append_text"] .= $button;
			}
		}

		if ($val["type"] === "select" && is_object($this->obj) && is_oid($this->obj->id()) && empty($val["no_edit"]))
		{
			$clid = isset($arr["relinfo"][$reltype]["clid"]) ? (array)$arr["relinfo"][$reltype]["clid"] : array();
			$rel_val = isset($arr["relinfo"][$reltype]["value"]) ? $arr["relinfo"][$reltype]["value"] : null;

			$clss = aw_ini_get("classes");
			if (count($clid) > 1)
			{
				$pm = get_instance("vcl/popup_menu");
				$pm->begin_menu($arr["property"]["name"]."_relp_pop");
				foreach($clid as $_clid)
				{
					$pm->add_item(array(
						"text" => $clss[$_clid]["name"],
						"link" => html::get_new_url(
							$_clid,
							(isset($arr["prop"]["parent"]) && $arr["prop"]["parent"] === "this.parent") ? $this->obj->parent() : $this->obj->id(),
							array(
								"alias_to" => $this->obj->id(),
								"alias_to_prop" => $arr["property"]["name"],
								"reltype" => $rel_val,
								"save_autoreturn" => !empty($val["add_edit_autoreturn"]),
								"return_url" => get_ru()
							)
						)
					));
				}
				$val["post_append_text"] .= " ".$pm->get_menu(array(
					"icon" => "new.gif",
					"alt" => t("Lisa")
				));
			}
			else
			{
				foreach($clid as $_clid)
				{
					$val["post_append_text"] .= " ".html::href(array(
						"url" => html::get_new_url(
							$_clid,
							(!empty($arr["prop"]["parent"]) && $arr["prop"]["parent"] === "this.parent") ? $this->obj->parent() : $this->obj->id(),
							array(
								"alias_to_prop" => ifset($arr, "prop", "name"),
								"alias_to" => $this->obj->id(),
								"reltype" => $rel_val,
								"save_autoreturn" => !empty($val["add_edit_autoreturn"]),
								"return_url" => get_ru()
							)
						),
						"caption" => "<img src='".aw_ini_get("baseurl")."/automatweb/images/icons/new.gif' border=0>",
						"title" => sprintf(t("Lisa uus %s"), $clss[$_clid]["name"]),
					));
				}
			}
		}
		return array($val["name"] => $val);
	}


	/** connection deleting popup
		@attrib name=rels_del_popup all_ags=1 api=1
		@param reltype required type=string
		@param id required type=oid
		@param return_url required type=string
		@param del_rels optional
	**/
	function rels_del_popup($arr)
	{
		$o = obj($arr["id"]);

		if(!empty($arr["del_rels"]))
		{
			$o->disconnect(array(
				"from" => $arr["del_rels"],
			));
			die("<script language='javascript'>
				if (window.opener)
				{
					window.opener.location.reload();
				}
				window.close();
			</script>");
		}

		$ol = new object_list();

		$conns = $o->connections_from(array(
			"type" => $arr["reltype"],
		));

		$t = new vcl_table();

		$t->define_field(array(
			"name" => "name",
			"caption" => t("Seotud objekt"),
			"align" => "right",
		));

		$t->define_chooser(array(
			"name" => "del_rels",
			"field" => "oid"
		));

		$t->define_field(array(
			"name" => "del",
			"caption" => t("Kustuta seos"),
			"align" => "right",
		));

		foreach($conns as $con)
		{
			$t->define_data(array(
				"oid" => $con->prop("to"),
				"name" => $con->prop("to.name"),
				"del" => html::href(array("caption" => t("Kustuta"), "url" => aw_url_change_var("del_rels", $con->prop("to")),)),
			));
		}

		$htmlc = new htmlclient(array(
			'template' => "default"
		));
		$htmlc->start_output();
		$htmlc->add_property(array(
			"caption" => t("Vali eemaldatavad seosed"),
		));

		$htmlc->add_property(array(
			"name" => "rels_table",
			"no_caption" => "1",
			"type" => "text",
			"value" => $t->draw()
		));

		$htmlc->add_property(array(
			"name" => "submit",
			"type" => "submit",
			"value" => t("Kustuta valitud seosed"),
			"class" => "sbtbutton"
		));

		$htmlc->finish_output(array(
			"action" => "rels_del_popup",
			"method" => "post",
			"data" => array(
				"id" => $arr["id"],
				"return_url" => $arr["return_url"],
				"reltype" => $arr["reltype"],
				"orb_class" => "relpicker",
				"reforb" => 0
			)
		));

		$html = $htmlc->get_result();
		return $html;
	}


	function process_vcl_property($arr)
	{
		if (ifset($arr, "prop", "mode") === "autocomplete")
		{
			return $this->process_autocomplete_relpicker($arr);
		}
		$property = $arr["prop"];
		if (ifset($property, "type") === "relpicker" && ifset($property, "automatic") == 1)
		{
			$obj_inst = $arr["obj_inst"];
			$conns = array();
			$rt = $arr["relinfo"][$property["reltype"]]["value"];
			if (!$arr["new"])
			{
				$rt = $arr["relinfo"][$property["reltype"]]["value"];
				$conns = $obj_inst->connections_from(array(
					"type" => $property["reltype"],
				));
			}

			// no existing connection, create a new one
			if ($arr["new"] || sizeof($conns) == 0)
			{
				if (is_array($property["value"]))
				{
					foreach($property["value"] as $pval)
					{
						$obj_inst->connect(array(
							"to" => $pval,
							"reltype" => $rt,
						));
					}
				}
				else
				if ($property["value"] != 0)
				{
					$obj_inst->connect(array(
						"to" => $property["value"],
						"reltype" => $rt,
					));
				};
			}
			else
			{
				if (is_array($property["value"]))
				{
					foreach($conns as $conn)
					{
						if (!in_array($conn->prop("to"),$property["value"]))
						{
						//	$conn->delete();
						}
					}
				}
				else
				{
					list(,$existing) = each($conns);
					if ($property["value"] == 0)
					{
						$existing->delete();
					}
					else
					{
						$existing->change(array(
							"to" => $property["value"],
						));
					}
				}
			}
		}
	}

	function init_autocomplete_relpicker($arr)
	{
		$prop = array(
			"name" => $arr["prop"]["name"],
			"type" => "textbox",
			"store" => "no",
			"caption" => $arr["prop"]["caption"],
			"parent" => ifset($arr, "prop", "parent"),
		);
		$ri = $arr["obj_inst"]->get_relinfo();
		if(!is_array($ri) || !sizeof($ri))
		{
			$ri = $GLOBALS["relinfo"][$arr["clid"]];
		}

		$clids = $ri[$arr["prop"]["reltype"]]["clid"];

		if ($this->can("view", $arr["obj_inst"]->prop($arr["prop"]["name"])))
		{
			$prop["value"] = $arr["obj_inst"]->prop($arr["prop"]["name"].".name");
		}
		if (is_admin())
		{
			$prop["autocomplete_source"] = $this->mk_my_orb("get_relp_opts", array("clids" => $clids));
			$prop["autocomplete_params"] = array($arr["prop"]["name"]);
		}
		return array($arr["prop"]["name"] => $prop);
	}

	/**
		@attrib name=get_relp_opts all_args="1"
	**/
	function get_relp_opts($arr)
	{
		if (is_admin())
		{
			$ol = new object_list(array(
				"class_id" => $arr["clids"],
				"lang_id" => array(),
				"site_id" => array(),
				"name" => $arr[$arr["requester"]]."%"
			));
		}
		else
		{
			$ol = new object_list();
		}
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


		header ("Content-Type: text/html; charset=" . aw_global_get("charset"));
		$autocomplete_options = $ol->names();
		foreach($autocomplete_options as $key=>$val)
		{
			$autocomplete_options[$key] = iconv(aw_global_get("charset"),"UTF-8",  $autocomplete_options[$key]);
		}
		exit ($cl_json->encode($option_data));
	}

	function process_autocomplete_relpicker($arr)
	{
		// if the name is not empty, find item with that name. if not found, create new
		if (trim($arr["prop"]["value"]) != "")
		{
			$ri = $arr["obj_inst"]->get_relinfo();
			$clids = $ri[$arr["prop"]["reltype"]]["clid"];

			$ol = new object_list(array(
				"class_id" => $clids,
				"lang_id" => array(),
				"site_id" => array(),
				"name" => $arr["prop"]["value"]
			));

			if ($ol->count())
			{
				$item = $ol->begin();
			}
			else
			{
				$item = obj();
				$item->set_class_id(reset($clids));
				$item->set_parent(is_oid($arr["obj_inst"]->id()) ? $arr["obj_inst"]->id() : $arr["obj_inst"]->parent());
				$item->set_name($arr["prop"]["value"]);
				$item->save();
			}

			if ($arr["prop"]["store"] === "connect" && is_oid($arr["obj_inst"]->id()))
			{
				foreach($arr["obj_inst"]->connections_from(array("type" => $arr["prop"]["reltype"])) as $c)
				{
					$c->delete();
				}
			}

			$arr["obj_inst"]->connect(array(
				"to" => $item->id(),
				"type" => $arr["prop"]["reltype"]
			));
			$arr["prop"]["value"] = $item->id();
			$arr["obj_inst"]->set_prop($arr["prop"]["name"], $item->id());
		}
	}
}
