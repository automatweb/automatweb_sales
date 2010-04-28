<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_INFRASTRUCTURE_IMPORT relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=instrumental
@tableinfo aw_infrastructure_import master_index=brother_of master_table=objects index=aw_oid

@default table=aw_infrastructure_import
@default group=general

@property countries_json type=relpicker reltype=RELTYPE_JSON_FILE field=aw_countries_json
@caption Riikide JSON fail

@property countries_parent type=relpicker reltype=RELTYPE_PARENT field=aw_countries_parent
@caption Riikide kaust

@property invoke type=text editonly=1 store=no
@caption Import

### RELTYPES

@reltype JSON_FILE value=1 clid=CL_FILE
@caption JSON fail

@reltype PARENT value=2 clid=CL_MENU
@caption Parent

*/

class infrastructure_import extends class_base
{
	const AW_CLID = 1542;

	function infrastructure_import()
	{
		$this->init(array(
			"tpldir" => "import/infrastructure_import",
			"clid" => CL_INFRASTRUCTURE_IMPORT
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
			case "invoke":
				$prop["value"] = html::href(array(
					"url" => $this->mk_my_orb("invoke", array(
						"id" => automatweb::$request->arg("id"),
						"return_url" => get_ru(),
					)),
					"caption" => t("K&auml;ivita"),
				));
				break;
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

	function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_infrastructure_import(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "aw_countries_json":
			case "aw_countries_parent":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				return true;
		}
	}

	/**
		@attrib name=invoke params=name

		@param id required type=int acl=view

		@param return_url required type=string
	**/
	public function invoke($arr)
	{
		obj($arr["id"])->invoke();
		return $arr["return_url"];
	}
}

?>
