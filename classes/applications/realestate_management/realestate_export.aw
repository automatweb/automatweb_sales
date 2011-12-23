<?php
// realestate_export.aw - Kinnisvaraobjektide eksport
/*

@classinfo syslog_type=ST_REALESTATE_EXPORT relationmgr=yes no_comment=1 no_status=1 maintainer=voldemar

@groupinfo grp_settings caption="Seaded" parent=general
@groupinfo grp_users caption="Kasutajanimed" parent=general

@default table=objects
@default group=grp_settings
@default field=meta
@default method=serialize
	@property realestate_manager type=relpicker reltype=RELTYPE_OWNER clid=CL_REALESTATE_MANAGER automatic=1
	@comment Kinnisvarahalduskeskkond, mille objekte soovitakse eksportida
	@caption Kinnisvarahalduskeskkond

	@property city24export_xsl type=textbox
	@caption City24 ekspordi xsl faili url

	@property city24export_encoding type=relpicker reltype=RELTYPE_ENCODING clid=CL_COUNTRY_ADMINISTRATIVE_STRUCTURE_ENCODING automatic=1
	@caption Koodid aadresside City24 ekspordi jaoks

	@property last_city24export type=text datatype=int
	@caption Viimase ekspordi staatus City24

	@property last_city24export_time type=text
	@caption Viimane eksport City24

	@property last_city24export_error type=text
	@caption Viimasel ekspordil esinenud vead

@default group=grp_users
	@property box type=text no_caption=1 store=no
	@layout vsplitbox type=hbox width="30%:70%"
	@property user_names_definition_tree type=treeview store=no no_caption=1 parent=vsplitbox
	@property user_names_definition_table type=table store=no no_caption=1 parent=vsplitbox

// --------------- RELATION TYPES ---------------------

@reltype OWNER clid=CL_REALESTATE_MANAGER value=1
@caption Kinnisvaraobjektide halduskeskkond

@reltype ENCODING clid=CL_COUNTRY_ADMINISTRATIVE_STRUCTURE_ENCODING value=2
@caption Aadresside vastavuskoodid

*/

define ("REALESTATE_TIME_FORMAT", "j/m/Y H.i.s");
define ("NEWLINE", "<br />\n");
define ("RE_EXPORT_CITY24USER_VAR_NAME", "realestate_city24username");

class realestate_export extends class_base
{
	var $realestate_manager;
	var $export_objlist;
	var $from_date;
	var $realestate_classes = array (
		CL_REALESTATE_HOUSE,
		CL_REALESTATE_ROWHOUSE,
		CL_REALESTATE_COTTAGE,
		CL_REALESTATE_HOUSEPART,
		CL_REALESTATE_APARTMENT,
		CL_REALESTATE_COMMERCIAL,
		CL_REALESTATE_GARAGE,
		CL_REALESTATE_LAND,
	);

	function realestate_export()
	{
		$this->init(array(
			"tpldir" => "applications/realestate_management/realestate_export",
			"clid" => CL_REALESTATE_EXPORT
		));
	}

	// @attrib name=init_local
	// @param id required type=int
	function init_local ($arr)
	{
		$this_object = obj ($arr["id"]);

		if ($this->can ("view", $this_object->prop ("realestate_manager")))
		{
			$this->realestate_manager = obj ($this_object->prop ("realestate_manager"));
		}
		else
		{
			return t("Kinnisvarahalduskeskkond m22ramata v6i puudub juurdep22su6igus.\n");
		}
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
			case "user_names_definition_table":
				$this->_user_names_definition_table ($arr);
				break;

			case "user_names_definition_tree":
				$this->_user_names_definition_tree ($arr);
				break;

			case "last_city24export_time":
				$prop["value"] = date (REALESTATE_TIME_FORMAT, $prop["value"]);
				break;
		}

		return $retval;
	}

	function _user_names_definition_table ($arr = array ())
	{
		if (is_oid ($arr["request"]["unit"]))
		{
			$unit = obj ($arr["request"]["unit"]);

			if ($unit->class_id () != CL_CRM_SECTION)
			{
				return;
			}

			$table =& $arr["prop"]["vcl_inst"];
			$table->define_field(array(
				"name" => "name",
				"caption" => t("Nimi"),
				"sortable" => 1,
			));

			$table->define_field(array(
				"name" => "city24user",
				"caption" => t("Kasutajanimi City24 s&uuml;steemis"),
			));

			$table->set_default_sortby ("name");
			$table->set_default_sorder ("asc");

			$workers = $unit->get_workers();

			foreach($workers->arr() as $person)
			{
				$tdata = array(
					"name" => $person->prop ('name'),
					"city24user" => html::textbox(array(
						"name" => "person[" . $person->id () . "]",
						"value" => $person->meta (RE_EXPORT_CITY24USER_VAR_NAME),
						"size" => 30,
						"textsize" => "11px"
					)),
				);
				$table->define_data($tdata);
			}
		}
	}

	function _user_names_definition_tree ($arr = array ())
	{
		$this_object = $arr["obj_inst"];

		if (!is_oid ($this_object->prop ("realestate_manager")))
		{
			return;
		}

		$manager = obj ($this_object->prop ("realestate_manager"));
		$tree = $arr["prop"]["vcl_inst"];
		$trees = array ();

		foreach($manager->connections_from(array("type" => "RELTYPE_REALESTATEMGR_USER")) as $c)
		{
			$o = $c->to();

			if ($this->can("view", $o->id()))
			{
				$tree->add_item(0, array(
					"name" => $o->name(),
					"id" => $o->id(),
					"url" => aw_url_change_var("company", $o->id()),
				));

				$treeview = new treeview();
				$treeview->init_vcl_property ($arr);
				$arr["prop"]["vcl_inst"] = $treeview;
				$this->_delegate_co_v($arr, "_get_unit_listing_tree", $o);
				$trees[$o->id ()] = $arr["prop"]["vcl_inst"];
			}
		}

		### merge trees to one
		foreach ($trees as $id => $subtree)
		{
			foreach ($subtree->itemdata as $item)
			{
				### find item parent
				foreach ($subtree->items as $parent => $items)
				{
					foreach ($items as $itemdata)
					{
						if ($itemdata["id"] == $item["id"])
						{
							$item_parent = $parent;
							break;
						}
					}
				}

				$item_parent = $item_parent ? $id . $item_parent : $id;

				###...
				if (!preg_match ("/(\&|\?)cat=\d{0,11}/U", $item["url"]))
				{
					$tree->add_item($item_parent , array(
						"name" => $item["name"],
						"id" => $id . $item["id"],
						"url" => $item["url"],
						"iconurl" => $item["iconurl"],
						"class_id" => $item["class_id"],
					));
				}
			}
		}

		if (is_oid ($arr["request"]["company"]))
		{
			$tree->set_selected_item ($arr["request"]["company"]);
		}

		$arr["prop"]["vcl_inst"] = $tree;
		unset ($tree);
		unset ($trees);
	}

	function _delegate_co_v($arr, $fun, &$o)
	{
		$tmp = $arr["obj_inst"];
		$arr["obj_inst"] = $o;
		$co = get_instance("applications/crm/crm_company_people_impl");
		$co->$fun($arr);
		$arr["obj_inst"] = $tmp;
		unset ($tmp);
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
			case "user_names_definition_table":
				$save = $this->_save_user_names_data ($arr);

				if ($save !== PROP_OK)
				{
					$prop["error"] = $save;
					return PROP_FATAL_ERROR;
				}
				break;

		}

		return $retval;
	}

	function _save_user_names_data ($arr)
	{
		foreach ($arr["request"]["person"] as $oid => $value)
		{
			$person = obj ($oid);
			$person->set_meta (RE_EXPORT_CITY24USER_VAR_NAME, $value);
			$person->save ();
		}

		return PROP_OK;
	}

	function callback_mod_retval($arr)
	{
		$arr["args"]["unit"] = $arr["request"]["realestate_unit"];
	}

	function callback_mod_reforb($arr, $request)
	{
		if ($request["unit"])
		{
			$arr["realestate_unit"] = $request["unit"];
		}
	}

	// @attrib name=get_objects
	function get_objects ($arr)
	{
		$realestate_folders = array (
			$this->realestate_manager->prop ("houses_folder"),
			$this->realestate_manager->prop ("rowhouses_folder"),
			$this->realestate_manager->prop ("cottages_folder"),
			$this->realestate_manager->prop ("houseparts_folder"),
			$this->realestate_manager->prop ("apartments_folder"),
			$this->realestate_manager->prop ("commercial_properties_folder"),
			$this->realestate_manager->prop ("garages_folder"),
			$this->realestate_manager->prop ("land_estates_folder"),
		);

		$this->export_objlist = new object_list (array (
			"class_id" => $this->realestate_classes,
			"parent" => $realestate_folders,
			"modified" => new obj_predicate_compare (OBJ_COMP_GREATER, $this->from_date),
			"site_id" => array (),
		));
	}

/**
	@attrib name=city24export nologin=1
	@param id required type=int
	@param from_date optional type=int
**/
	function city24export ($arr)
	{
		$this->time = time ();
		$errors = "";
		$errors .= $this->init_local ($arr);
		$this_object = obj ($arr["id"]);
		$this->from_date = (int) (is_numeric ($arr["id"]) ? $arr["id"] : $this_object->prop ("last_city24export_time"));

		$this->get_objects ($arr);
		$objects = $this->export_objlist->arr ();
		$xml = array ();
		$xml[] = '<?xml version="1.0" encoding="iso-8859-4" ?>';
		$xml[] = '<objects>';

		### get realestate property class instances
		foreach ($this->realestate_classes as $cls_id)
		{
			$cl_instance_var = "cl_property_" . $cls_id;

			if (!is_object ($this->$cl_instance_var))
			{
				$this->$cl_instance_var = get_instance ($cls_id);
			}
		}

		### export properties
		foreach ($objects as $o)
		{
			$cl_instance_var = "cl_property_" . $o->class_id ();
			$o_xml = $this->$cl_instance_var->export_xml (array (
				"this" => $o,
				"no_declaration" => true,
				"address_encoding" => $this_object->prop ("city24export_encoding"),
			));

			if (empty ($this->$cl_instance_var->export_errors))
			{
				$xml[] = $o_xml;
			}
			else
			{
				$errors .= sprintf (t("Viga objekti ekspordil. AW id: %s.\n<blockquote>%s</blockquote>"), $o->id (), $this->$cl_instance_var->export_errors) . NEWLINE;
			}
		}

		$xml[] = '</objects>';
		$xml = implode ("\n", $xml);

/* dbg */ if ($_GET["show_input"] == 1) { header ("Content-Type: text/xml"); echo $xml; exit;}

		$tmpname = tempnam (aw_ini_get("server.tmpdir"), "realestateimport");
		$tmp = fopen ($tmpname, "w");
		fwrite ($tmp, $xml);
		fclose($tmp);
		unset($xml);

		$xslt_processor = xslt_create ();
		$export_xml = xslt_process ($xslt_processor, $tmpname, $this_object->prop ("city24export_xsl"));

		if ($errors or xslt_errno ($xslt_processor))
		{
			$this_object->set_prop ("last_city24export_time", $this->time);
			$this_object->set_prop ("last_city24export", 0);
		}
		else
		{
			$this_object->set_prop ("last_city24export", 1);
		}

		$errors = sprintf ("AW errors: <pre>%s</pre> <hr> XSLT error: <pre>%s</pre> <hr> XSLT error code: %s", $errors, xslt_error ($xslt_processor), xslt_errno ($xslt_processor));
		$this_object->set_prop ("last_city24export_error", $errors);
		$this_object->save ();
		xslt_free($xslt_processor);
		unlink($tmpname);

		header ("Content-Type: text/xml");
		echo $export_xml;
		exit;
	}


/**
	@attrib name=city24export_status
	@param id required type=int
**/
	function city24export_status ($arr)
	{
		$this_object = obj ($arr["id"]);
		echo $this_object->prop ("last_city24export");
		exit;
	}
}
