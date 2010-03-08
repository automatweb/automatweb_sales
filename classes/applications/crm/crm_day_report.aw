<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/crm/crm_day_report.aw,v 1.3 2007/12/06 14:33:17 kristo Exp $
// crm_day_report.aw - P&auml;eva raport 
/*

@classinfo syslog_type=ST_CRM_DAY_REPORT relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=markop

@default table=objects
@default group=general

@tableinfo aw_crm_day_report index=aw_oid master_index=brother_of master_table=objects

@property date type=date_select table=aw_crm_day_report field=aw_date
@caption Kuup&auml;ev

@property reporter type=text table=aw_crm_day_report field=aw_reporter
@caption Esitaja

@property content type=textarea rows=20 cols=50 table=aw_crm_day_report field=aw_content
@caption Tegevused

@property num_hrs type=textbox size=5 table=aw_crm_day_report field=aw_num_hrs
@caption T&ouml;&ouml;tundide arv

*/

class crm_day_report extends class_base
{
	function crm_day_report()
	{
		$this->init(array(
			"tpldir" => "applications/crm/crm_day_report",
			"clid" => CL_CRM_DAY_REPORT
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "date":
				if ($arr["new"])
				{
					$prop["value"] = time();
				}
				break;

			case "reporter":
				if ($prop["value"] == "")
				{
					$u = get_instance(CL_USER);
					$prop["value"] = $u->get_current_person();
				}

				if ($this->can("view", $prop["value"]))
				{
					$o = obj($prop["value"]);
					$prop["value"] = html::get_change_url($prop["value"], array("return_url" => get_ru()), $o->name());
				}
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
			case "reporter":
				if ($arr["obj_inst"]->prop("reporter") == "")
				{
					$u = get_instance(CL_USER);
					$prop["value"] = $u->get_current_person();
					$arr["obj_inst"]->set_prop("reporter", $prop["value"]);
				}
				break;
		}
		return $retval;
	}	

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}
}
?>
