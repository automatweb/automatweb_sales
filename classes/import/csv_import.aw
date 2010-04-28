<?php

namespace automatweb;

/*

@classinfo syslog_type=ST_CSV_IMPORT relationmgr=yes no_status=1 maintainer=voldemar prop_cb=1

@groupinfo import caption="Import"

@default table=objects
@default field=meta
@default method=serialize
@default group=general
	@property import_class type=select
	@caption Imporditavate objektide klass

	@property folder type=relpicker reltype=RELTYPE_FOLDER
	@caption Kaust kuhu importida

	@property csv_record_separator type=textbox
	@comment Eris&uuml;mbolid: reavahetused UNIX formaadis fail - '\n', Windows - '\r\n', Mac - '\r', tabulaator - '\t'
	@caption CSV kirjete eraldaja

	@property csv_field_separator type=textbox
	@comment Eris&uuml;mbolid: reavahetused UNIX formaadis fail - '\n', Windows - '\r\n', Mac - '\r', tabulaator - '\t'
	@caption CSV v&auml;ljade eraldaja

	@property csv_fields_in_record type=textbox datatype=int
	@caption CSV v&auml;ljade arv kirjes

	@property csv_row_comment_token type=textbox
	@comment M&auml;rgij&auml;rjend, millega algavaid ridu ei t&ouml;&ouml;delda
	@caption Kommentaaris&uuml;mbol

	@property property_map type=hidden editonly=1
	// array with numeric keys corresponding to csv data field order numbers and
	// values in array format containing configuration settings
	// array(
	//		"property" => property name that the field is imported to
	//		"unique" => if TRUE, this value is considered unique and object is changed only if "reimport" setting is also TRUE
	//		"reimport" => if TRUE, existing objects found via this field configuration will be overwritten with new data from csv source
	//		"controller" => controler object id for processing this field (gets imported object, field nr and field raw value as arguments)
	// )

	@property property_map_table type=table store=no editonly=1
	@caption Omaduste vastavused csv v&auml;ljadele

@default group=import
	@property import_file type=fileupload store=no
	@caption Impordi failist

	@property import_url type=textbox store=no
	@caption Impordi urlilt

	@property import_submit type=submit
	@caption Impordi

@reltype FOLDER value=1 clid=CL_MENU
@caption Kaust

@reltype CONTROLLER value=2 clid=CL_CFGCONTROLLER
@caption Kontroller

*/

class csv_import extends class_base
{
	const AW_CLID = 1562;

	function __construct ()
	{
		$this->init(array(
			"tpldir" => "import/csv_import",
			"clid" => CL_CSV_IMPORT
		));
	}

	function _set_import_url(&$arr)
	{
		$url = new aw_uri($arr["prop"]["value"]);
		$this_o = $arr["obj_inst"];
		$data = file_get_contents($url->get());
		$this_o->import($data);
	}

	function _get_import_class(&$arr)
	{
		$r = PROP_OK;
		$arr["prop"]["options"] = get_class_picker();
		return $r;
	}

	function _set_import_class(&$arr)
	{
		$r = PROP_OK;
		if (!is_class_id($arr["prop"]["value"]))
		{
			$r = PROP_FATAL_ERROR;
			$arr["prop"]["error"] = t("Klass m&auml;&auml;ramata");
		}
		return $r;
	}

	function _get_property_map_table(&$arr)
	{
		$r = PROP_OK;
		$table = $arr["prop"]["vcl_inst"];
		$this_o = $arr["obj_inst"];

		// define table structure
		$table->define_field(array(
			"name" => "field_nr",
			"caption" => t("CSV v&auml;lja nr.")
		));
		$table->define_field(array(
			"name" => "property",
			"caption" => t("Vastav objekti omadus")
		));
		$table->define_field(array(
			"name" => "unique",
			"caption" => t("Unikaalne v&auml;&auml;rtus")
		));
		$table->define_field(array(
			"name" => "reimport",
			"caption" => t("Kirjuta olemasolev &uuml;le")
		));
		$table->define_field(array(
			"name" => "controller",
			"caption" => t("Kontroller")
		));

		// define mapping definition rows
		$fields = $this_o->prop("csv_fields_in_record");
		$cfgutils = new cfgutils();

		// get property names
		$properties = $cfgutils->load_properties(array(
			"clid" => $this_o->prop("import_class")
		));
		foreach ($properties as $name => $data)
		{
			$properties[$name] = (empty($data["caption"]) ? "" : "{$data["caption"]} ") . "[{$name}]";
		}

		$property_map = $this_o->prop("property_map");

		while ($field_nr = $fields--)
		{
			$property_select = html::select(array(
				"name" => "property_map_data[{$field_nr}][property]",
				"options" => $properties,
				"value" => isset($property_map[$field_nr]["property"]) ? $property_map[$field_nr]["property"] : ""
			));
			$controller_relpicker = new relpicker();
			$controller_relpicker = $controller_relpicker->create_relpicker(array(
				"name" => "property_map_data[{$field_nr}][controller]",
				"reltype" => "RELTYPE_CONTROLLER",
				"no_edit" => 1,
				"oid" => $this_o->id(),
				"property" => "property_map_data[{$field_nr}][controller]",
				"value" => isset($property_map[$field_nr]["controller"]) ? $property_map[$field_nr]["controller"] : 0,
				"automatic" => 1
			));
			$unique_checkbox = html::checkbox(array(
				"name" => "property_map_data[{$field_nr}][unique]",
				"ch_value" => 1,
				"checked" => isset($property_map[$field_nr]["unique"]) ? $property_map[$field_nr]["unique"] : 0
			));
			$reimport_checkbox = html::checkbox(array(
				"name" => "property_map_data[{$field_nr}][reimport]",
				"ch_value" => 1,
				"checked" => isset($property_map[$field_nr]["reimport"]) ? $property_map[$field_nr]["reimport"] : 0
			));

			$unique_checkbox;
			$table->define_data(array(
				"field_nr" => $field_nr,
				"property" => $property_select,
				"unique" => $unique_checkbox,
				"reimport" => $reimport_checkbox,
				"controller" => $controller_relpicker,
			));
		}

		return $r;
	}

	function _set_property_map(&$arr)
	{
		$arr["prop"]["value"] = $arr["request"]["property_map_data"];
		return PROP_OK;
	}
}

?>
