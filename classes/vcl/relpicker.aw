<?php

class relpicker extends core implements orb_public_interface
{
	function relpicker()
	{
		$this->init("");
	}

	/** Sets orb request to be processed by this object
		@attrib api=1 params=pos
		@param request type=aw_request
		@returns void
	**/
	public function set_request(aw_request $request)
	{
		$this->req = $request;
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

		@param no_sel type=bool default=FALSE
			Show no selection options

		@param automatic type=bool default=FALSE
			Whether to load all valid objects for this relation to selection options

		@param no_edit type=bool default=FALSE
			Don't show new/search/edit buttons

		@param no_add type=bool default=FALSE
			Don't show add button

		@param no_search type=bool default=FALSE
			Don't show search button

		@param add_edit_autoreturn type=bool default=FALSE
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
		$show_sel = empty($arr["no_sel"]);
		$show_edit = empty($arr["no_edit"]);
		$show_add = empty($arr["no_add"]);
		$show_search = empty($arr["no_search"]);
		$disabled = isset($arr["disabled"]) ? $arr["disabled"] : 0;
		$multiple = isset($arr["multiple"]) ? $arr["multiple"] : 0;
		$size = isset($arr["size"]) ? $arr["size"] : 1;
		$width = isset($arr["width"]) ? $arr["width"] : 0;
		$automatic = !empty($arr["automatic"]);
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

		if($show_sel)
		{
			$options = html::get_empty_option(0) + $options;
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
			$r .= html::linebreak();
		}

		// add search button
		if ($show_search)
		{
			$url = $this->mk_my_orb("do_search", array(
				"id" => $oid,
				"pn" => $name,
				"in_popup" => "1",
				"start_empty" => "1",
				"clid" => $clids,
				"multiple" => $multiple
			), "popup_search", false, true);

			$r .= " ".html::href(array(
				"url" => "javascript:aw_popup_scroll(\"$url\",\"Otsing\",".popup_search::PS_WIDTH.",".popup_search::PS_HEIGHT.")",
				"caption" => html::img(array("url" => icons::get_std_icon_url("magnifier"), "border" => "0")),
				"title" => t("Otsi"),
			));
		}

		// add edit button
		if(!is_array($selected) && $this->can("edit", $selected) && $show_edit)
		{
			$selected_obj = new object($selected);
			$selected_clid = $selected_obj->class_id();
			$r .= " ".html::href(array(
				"url" => $this->mk_my_orb("change", array(
					"id" => $selected,
					"save_autoreturn" => !empty($add_edit_autoreturn),
					"return_url" => get_ru()
				), $selected_clid),
				"caption" => html::img(array("url" => icons::get_std_icon_url("pencil"), "border" => "0")),
				"title" => t("Muuda")
			));
		}
		elseif(is_array($selected) && $show_edit)
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
				"icon" => "pencil"
			));
		}

		// add create button
		if($show_add and $show_edit)
		{
			if (count($clids) > 1)
			{
				$pm = new popup_menu();
				$pm->begin_menu($name."_relp_pop");
				foreach($clids as $clid)
				{
					$pm->add_item(array(
						"text" => aw_ini_get("classes.{$clid}.name"),
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
					"icon" => "add",
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
						"caption" => html::img(array("url" => icons::get_std_icon_url("add"), "border" => "0")),
						"title" => sprintf(t("Lisa uus %s"), aw_ini_get("classes.{$clid}.name"))
					));
				}
			}
		}

		//TODO: show control and buttons on same line
		// $r = html::span(array("content" => $r, "nowrap" => true));

		return $r;
	}

	function init_vcl_property($arr)
	{//TODO: KASUTADA create_relpicker()-it !!!
		$prop = &$arr["property"];
		$this->obj = $arr["obj_inst"];
		if (isset($prop["mode"]) && $prop["mode"] === "autocomplete")
		{
			return $this->init_autocomplete_relpicker($arr);
		}

		$prop["post_append_text"] = isset($prop["post_append_text"]) ? $prop["post_append_text"] : "";

		if((empty($prop["store"]) or "connect" !== $prop["store"]) and empty($prop["no_sel"]))
		{
			$options = html::get_empty_option();
		}
		else
		{
			$options = array();
		}

		$reltype = isset($prop["reltype"]) ? $prop["reltype"] : null;
		// generate option list
		if (isset($prop["options"]) && is_array($prop["options"]))
		{
			$prop["type"] = "select";
		}
		else
		{
			// if automatic is set, then create a list of all properties of that type
			if (isset($prop["automatic"]))
			{
				$clid = $arr["relinfo"][$reltype]["clid"];
				$prop["type"] = "select";
				if (!empty($clid))
				{
					$olist = new object_list(array(
						"class_id" => $clid,
						"brother_of" => new obj_predicate_prop("id")
					));
					$names = $olist->names();
					asort($names);
					$prop["options"] = $options + $names;
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
						$prop["value"] = $sel;
					};*/
					// since when do automatic relpickers get all relations selected?!?!
				}
			}
			else
			{
				$prop["options"] = array();
				if ($arr["id"])
				{
					$o = obj($arr["id"]);
					$conn = array();

					if (!empty($prop["clid"]))
					{
						$clids = (array) $prop["clid"];
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
					$prop["options"] = $options;
				}
			}
		}

		if (isset($prop["store"]) and "connect" === $prop["store"])
		{
			$prop["value"] = array_keys($prop["options"]);
		}

		$prop["type"] = (isset($prop["display"]) && $prop["display"] === "radio") ? "chooser" : "select";

		$buttons = "";

		// search button
		if ($prop["type"] === "select" /*&& is_object($this->obj)*/)
		{
			$clid = isset($arr["relinfo"][$reltype]["clid"]) ? (array)$arr["relinfo"][$reltype]["clid"] : array();

			// search button
			if(!is_object($arr["obj_inst"]) || empty($prop["parent"]))
			{
				// I only want the search button. No edit or new buttons!
				if (empty($prop["no_search"]))
				{
					$url = $this->mk_my_orb("do_search", array(
						"id" => is_object($arr["obj_inst"]) ? $arr["obj_inst"]->id() : null,
						"pn" => $arr["property"]["name"],
						"in_popup" => "1",
						"start_empty" => "1",
						"clid" => $clid,
						"multiple" => isset($arr["property"]["multiple"]) && $arr["property"]["multiple"] ? $arr["property"]["multiple"] : NULL
					), "popup_search", false, true);

					$buttons .= " ".html::href(array(
						"url" => "javascript:aw_popup_scroll('$url','Otsing',".popup_search::PS_WIDTH.",".popup_search::PS_HEIGHT.")",
						"caption" => html::img(array("url" => icons::get_std_icon_url("magnifier"), "border" => "0")),
						"title" => t("Otsi"),
					));
				}
			}//selle paneks peaaegu alati t88le kui suudaks loadida relpickereid
			elseif(empty($prop["no_search"]))
			{
				$ps = new popup_search();
				$ps->set_class_id($clid);
				$ps->set_id($arr["obj_inst"]->id());
				$ps->set_reload_property($prop["name"]);
				$ps->set_property($arr["property"]["name"]);
				if(!empty($prop["parent"]))
				{
					$ps->set_reload_layout($prop["parent"]);
				}
				else
				{
					$ps->set_reload_property($prop["name"]);
				}
				$buttons = $ps->get_search_button();
			}
		}

		// edit button
		if (
			isset($prop["type"]) && $prop["type"] === "select" &&
			is_object($this->obj) && ((isset($prop["value"]) && is_oid($prop["value"]) && $this->can("edit", $prop["value"])) ||
			(is_object($this->obj) && is_oid($this->obj->id()) && $this->obj->is_property($prop["name"]) && is_oid($this->obj->prop($prop["name"])) && $this->can("edit", $this->obj->prop($prop["name"]))) ) &&
			empty($prop["no_edit"])
		)
		{
			try
			{
				$change_url = html::get_change_url($this->obj->prop($prop["name"]), array(
					"save_autoreturn" => !empty($prop["add_edit_autoreturn"]),
					"return_url" => get_ru()
				));
				$buttons .= " ".html::href(array(
					"url" => $change_url,
					"caption" => html::img(array("url" => icons::get_std_icon_url("pencil"), "pencil" => "0")),
					"title" => t("Muuda")
				));
			}
			catch (Exception $e)
			{
				$prop["post_append_text"] .= " " . t("Objekt on kustutatud");
			}
		}
		elseif (
			isset($prop["type"]) && $prop["type"] === "select" &&
			is_object($this->obj) && ((isset($prop["value"]) && is_array($prop["value"]) && $this->can("edit", $prop["value"])) ||
			(is_object($this->obj) && is_oid($this->obj->id()) && $this->obj->is_property($prop["name"]) && is_array($this->obj->prop($prop["name"])))) &&
			empty($prop["no_edit"])
		)
		{
			$pm = new popup_menu();
			$pm->begin_menu(str_replace(array("[", "]"), "", $prop["name"])."_rp_editbtn");
			foreach($this->obj->prop($prop["name"]) as $id)
			{
				if($this->can("edit", $id))
				{
					$pm->add_item(array(
						"text" => obj($id)->name(),
						"link" => html::get_change_url($id, array("return_url" => get_ru()))
					));
				}
			}
			$buttons .= " ".$pm->get_menu(array(
				"icon" => "pencil"
			));
		}

		// delete button
		$allow_delete = false;
		if(
			isset($prop["delete_button"]) && $prop["delete_button"] ||
			isset($prop["delete_rels_button"]) && $prop["delete_rels_button"] ||
			isset($prop["delete_rels_popup_button"]) && $prop["delete_rels_popup_button"]
		)
		{
			$oids = array();
			$allow_delete = true;

			if(isset($prop["value"]) && is_oid($prop["value"]))
			{
				$oids[] = $prop["value"];
			}
			else
			if(isset($prop["value"]) && is_array($prop["value"]))
			{
				$oids = $prop["value"];
			}
			else
			if(is_object($this->obj) && is_oid($this->obj->id()) && $this->obj->is_property($prop["name"]) && $this->can("edit", $this->obj->prop($prop["name"])))
			{
				$oids[] = $this->obj->prop($prop["name"]);
			}
			else
			if(is_object($this->obj) && is_oid($this->obj->id()) && $this->obj->is_property($prop["name"]) && is_array($this->obj->prop($prop["name"])))
			{
				$oids = $this->obj->prop($prop["name"]);
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

		if($prop["type"] === "select" && is_object($this->obj) && $allow_delete)
		{
			if(!empty($prop["delete_button"]))
			{
				foreach($oids as $i => $oid)
				{
					$awucv["sel[".$i."]"] = $oid;
				}
				$awucv["action"] = "delete_objects";
				$awucv["post_ru"] = post_ru();
				$buttons .= " ".html::href(array(
					"url" => aw_url_change_var($awucv),
					//"url" => "javascript:submit_change_form('delete_objects')",
					"caption" => html::img(array("url" => icons::get_std_icon_url("delete"), "border" => "0")),
					"title" => t("Kustuta valitud objektid"),
					"onclick" => "if(!alert('".t("Oled kindel, et soovid valitud objektid kustutada?")."')) { return false; };",
				));
			}

			if(!empty($prop["delete_rels_button"]))
			{
				foreach($oids as $i => $oid)
				{
					$awucv["sel[".$i."]"] = $oid;
				}
				$awucv["action"] = "delete_rels";
				$awucv["post_ru"] = post_ru();
				$buttons .= " ".html::href(array(
					"url" => aw_url_change_var($awucv),
					//"url" => "javascript:submit_change_form('delete_rels')",
					"caption" => html::img(array("url" => icons::get_std_icon_url("delete"), "border" => "0")),
					"title" => t("Kustuta valitud seosed"),
					"onclick" => "if(!alert('".t("Oled kindel, et soovid valitud seosed kustutada?")."')) { return false; };",
				));
			}

			if(!empty($prop["delete_rels_popup_button"]))
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
				$buttons .= $button;
			}
		}

		// create button
		if ($prop["type"] === "select" && is_object($this->obj) && is_oid($this->obj->id()) && empty($prop["no_edit"]) && empty($prop["no_add"]))
		{
			$clid = isset($arr["relinfo"][$reltype]["clid"]) ? (array)$arr["relinfo"][$reltype]["clid"] : array();
			$rel_val = isset($arr["relinfo"][$reltype]["value"]) ? $arr["relinfo"][$reltype]["value"] : null;

			if (count($clid) > 1)
			{
				$pm = new popup_menu();
				$pm->begin_menu($arr["property"]["name"]."_relp_pop");
				foreach($clid as $_clid)
				{
					$pm->add_item(array(
						"text" => aw_ini_get("classes.{$_clid}.name"),
						"link" => html::get_new_url(
							$_clid,
							(isset($arr["prop"]["parent"]) && $arr["prop"]["parent"] === "this.parent") ? $this->obj->parent() : $this->obj->id(),
							array(
								"alias_to" => $this->obj->id(),
								"alias_to_prop" => $arr["property"]["name"],
								"reltype" => $rel_val,
								"save_autoreturn" => !empty($prop["add_edit_autoreturn"]),
								"return_url" => get_ru()
							)
						)
					));
				}
				$buttons .= " ".$pm->get_menu(array(
					"icon" => "add",
					"alt" => t("Lisa")
				));
			}
			else
			{
				foreach($clid as $_clid)
				{
					$buttons .= " ".html::href(array(
						"url" => html::get_new_url(
							$_clid,
							(!empty($arr["prop"]["parent"]) && $arr["prop"]["parent"] === "this.parent") ? $this->obj->parent() : $this->obj->id(),
							array(
								"alias_to_prop" => ifset($arr, "prop", "name"),
								"alias_to" => $this->obj->id(),
								"reltype" => $rel_val,
								"save_autoreturn" => !empty($prop["add_edit_autoreturn"]),
								"return_url" => get_ru()
							)
						),
						"caption" => html::img(array("url" => icons::get_std_icon_url("add"), "border" => "0")),
						"title" => sprintf(t("Lisa uus %s"), aw_ini_get("classes.{$_clid}.name"))
					));
				}
			}
		}

		// show buttons on same line
		//TODO: buttons and control both on same line
		// $buttons = html::span(array("content" => $buttons, "nowrap" => true));
		$prop["post_append_text"] .= $buttons;

		return array($prop["name"] => $prop);
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
			if ($arr["new"] || count($conns) === 0)
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

		$clids = isset($ri[$arr["prop"]["reltype"]]["clid"]) ? $ri[$arr["prop"]["reltype"]]["clid"] : null;

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
		@attrib name=get_relp_opts all_args=1
	**/
	function get_relp_opts($arr)
	{
		if (isset($arr[$arr["requester"]]) and strlen($arr[$arr["requester"]]) > 1)
		{
			$ol = new object_list(array(
				"class_id" => $arr["clids"],
				"name" => $arr[$arr["requester"]]."%",
				new obj_predicate_sort(array("name" => obj_predicate_sort::ASC)),
				new obj_predicate_limit(50) //TODO: konfitavaks
			));
		}
		else
		{
			$ol = new object_list();
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

		$autocomplete_options = $ol->names();

		ob_start("ob_gzhandler");
		header("Content-Type: application/json");
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		header("Pragma: no-cache"); // HTTP/1.0
		exit (json_encode($option_data));
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
