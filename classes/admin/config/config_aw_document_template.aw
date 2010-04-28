<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_CONFIG_AW_DOCUMENT_TEMPLATE no_status=1 maintainer=kristo
@tableinfo template index=obj_id master_table=objects master_index=brother_of

@default table=objects
@default group=general

	@property type type=select table=template field=type datatype=int
	@caption Template t&uuml;&uuml;p

	@property filename type=textbox table=template field=filename
	@caption Template fail

	@property t_id type=hidden table=template field=id
	@property t_name type=hidden table=template field=name
	@property t_site_id type=hidden table=template field=site_id datatype=int

@default group=grp_source
	@property source_html type=textarea rows=50 cols=74 table=objects field=meta method=serialize
	@caption L&auml;htekood


@groupinfo grp_source caption="L&auml;htekood"
*/

class config_aw_document_template extends class_base
{
	const AW_CLID = 252;

	function config_aw_document_template()
	{
		$this->init(array(
			"tpldir" => "admin/config/config_aw_document_template",
			"clid" => CL_CONFIG_AW_DOCUMENT_TEMPLATE,
		));
	}

	function get_property($arr)
	{
		$data = &$arr["prop"];
		$retval = PROP_OK;
		switch($data["name"])
		{
			case "type":
				$data["options"] = array(
					"1" => t("Lead"),
					"2" => t("Vaatamine")
				);
				break;
		};
		return $retval;
	}

	function set_property($arr = array())
	{
		$data = &$arr["prop"];
		$retval = PROP_OK;
		switch($data["name"])
		{
			case "t_name":
				$data["value"] = $arr["request"]["name"];
				break;

			case "t_id":
			case "t_site_id":
				$prop["value"] = (int)$prop["value"];
				break;
		}
		return $retval;
	}

	function callback_post_save($arr)
	{
		if ($arr["new"])
		{
			$arr["obj_inst"]->set_prop("t_id", $arr["obj_inst"]->id());
			$arr["obj_inst"]->set_prop("t_name", $arr["obj_inst"]->name());
			$arr["obj_inst"]->set_prop("t_site_id", aw_ini_get("site_id"));
			$arr["obj_inst"]->save();
		}
	}
}
?>