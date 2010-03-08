<?php
/*
@classinfo syslog_type=ST_POSTAL_CODES relationmgr=yes no_comment=1 no_status=1 prop_cb=1

@default table=objects
@default group=general

	@property general_tb type=toolbar store=no no_caption=1

	@property name type=textbox table=objects
	@caption Nimi

	@property register_url type=textbox field=meta method=serialize
	@caption Registri aadress

@groupinfo from_file caption="CSV fail" 
@default group=from_file

	@property file_tb type=toolbar store=no no_caption=1

	@property file type=relpicker reltype=RELTYPE_FILE store=connect
	@caption Andmefail (CSV)

	@property file_tbl type=table store=no no_caption=1
		
@reltype FILE clid=CL_FILE value=1
@caption Andmefail

*/

class postal_codes extends class_base
{
	function postal_codes()
	{
		$this->init(array(
			"tpldir" => "common/postal_codes",
			"clid" => CL_POSTAL_CODES
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
		}

		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
		}

		return $retval;
	}

	function _get_general_tb($arr)
	{
		$tb = &$arr["prop"]["vcl_inst"];
		$tb->add_button(array(
			"name" => "import_register",
			"tooltip" => t("Impordi registrist"),
			"img" => "import.gif",
			"action" => "import_register",
			"confirm" => t("Oled kindel? Vanad andmed kustutatakse"),
		));
	}

	function _get_file_tb($arr)
	{
		$tb = &$arr["prop"]["vcl_inst"];
		$tb->add_save_button();
		$tb->add_button(array(
			"name" => "import_csv",
			"tooltip" => t("Impordi failist"),
			"img" => "import.gif",
			"action" => "import_csv",
			"confirm" => t("Oled kindel? Vanad andmed kustutatakse"),
		));
		$tb->add_button(array(
			"name" => "export",
			"tooltip" => t("Ekspordi csv"),
			"img" => "export.gif",
			"action" => "export",
		));
	}

	function _get_file_tbl($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		try
		{
			$csv_fields = $arr["obj_inst"]->get_file_fields($arr["obj_inst"]);
		}
		catch(awex_pcodes_badfile $e)
		{
			$arr["prop"]["error"] = t("Fail on m&auml;&auml;ramata");
			return;
		}
		$this->_init_file_tbl($t);
		$db_fields = $arr["obj_inst"]->get_db_fields();
		$values = $arr["obj_inst"]->meta("file_values");
		foreach($db_fields as $field => $name)
		{
			$t->define_data(array(
				"db_name" => $name,
				"csv_name" => html::select(array(
					"name" => "values[".$field."][csv]",
					"options" => $csv_fields,
					"value" => $values[$field]["csv"],
				)),
				"default" => html::textbox(array(
					"name" => "values[".$field."][default]",
					"value" => $values[$field]["default"],
				)),
			));
		}
	}

	function _init_file_tbl(&$t)
	{
		$t->define_field(array(
			"caption" => t("Andmebaasi v&auml;li"),
			"name" => "db_name",
		));
		$t->define_field(array(
			"caption" => t("CSV v&auml;li"),
			"name" => "csv_name",
			"align" => "center",
		));
		$t->define_field(array(
			"caption" => t("Vaikimisi v&auml;&auml;rtus"),
			"name" => "default",
			"align" => "center",
		));
		$t->set_default_sortby("db_name");
		$t->set_default_sorder("asc");
	}

	function _set_file_tbl($arr)
	{
		$arr["obj_inst"]->set_meta("file_values", $arr["request"]["values"]);
		$arr["obj_inst"]->save();
	}

	/**
	@attrib name=export all_args=1
	**/
	function export($arr)
	{
		$o = obj($arr["id"]);
		$o->export_csv($arr);
		return $arr["post_ru"];
	}

	/**
	@attrib name=import_csv all_args=1
	**/
	function import_csv($arr)
	{
		$o = obj($arr["id"]);
		try
		{
			$o->import_from_csv($arr);
		}
		catch(awex_pcodes_xmlrpc $e)
		{
		}
		return $arr["post_ru"];
	}

	/**
	@attrib name=import_register all_args=1
	**/
	function import_from_register($arr)
	{
		$o = obj($arr["id"]);
		try
		{
			$o->import_from_register($arr);
		}
		catch(awex_pcodes_badfile $e)
		{
		}
		return $arr["post_ru"];
	}

	/**
	@attrib name=get_postal_codes api=1 nologin=1
	@returns array of postal codes
	@param from optional
	@param count optional
	**/
	function get_postal_codes($arr)
	{
		$pc = postal_codes_obj::get_postal_codes($arr);
		return $pc;
	}

	/**
	@attrib name=get_code api=1
	@param country optional type=string
	@param state optional type=string
	@param city optional type=string
	@param street optional type=string
	@param house optional type=string
	@returns postal code if found, false otherwise
	**/
	function get_code($arr)
	{
		$pc = postal_codes_obj::get_code($arr);
		return $pc;
	}

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function show($arr)
	{
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");
		$this->vars(array(
			"name" => $ob->prop("name"),
		));
		return $this->parse();
	}

	/**
	@attrib name=get_locations nologin=1 all_args=1
	**/
	function get_locations_ajax($arr)
	{
		$loc = postal_codes_obj::get_locations_from_param($arr);
		$res = array();
		foreach($loc as $l)
		{
			$res[] = $l[$arr["find"]];
		}
		$res = iconv(aw_global_get("charset"), "UTF-8", implode("|", $res));
		die($res);
	}
}

?>