<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/crm/crm_report_entry.aw,v 1.3 2009/02/04 17:27:57 markop Exp $
// crm_report_entry.aw - Aruanne 
/*

@classinfo syslog_type=ST_CRM_REPORT_ENTRY relationmgr=yes no_comment=1 no_status=1  maintainer=markop


@default group=general

	@property cust type=textbox table=objects field=meta method=serialize
	@caption Klient

	@property cust_type type=chooser table=objects field=meta method=serialize
	@caption Kliendi t&uuml;&uuml;p

	@property proj type=textbox table=objects field=meta method=serialize
	@caption Projekt

	@property worker type=textbox table=objects field=meta method=serialize
	@caption T&ouml;&ouml;taja

	@property worker_sel type=select multiple=1 table=objects field=meta method=serialize
	@caption T&ouml;&ouml;taja

	@property project_mgr type=select table=objects field=meta method=serialize
	@caption Projektijuht

	@property from type=date_select table=objects field=meta method=serialize
	@caption Alates

	@property to type=date_select table=objects field=meta method=serialize
	@caption Kuni

	@property time_sel type=select table=objects field=meta method=serialize
	@caption Ajavahemik

	@property state type=select table=objects field=meta method=serialize
	@caption Toimetuse staatus

	@property bill_state type=select table=objects field=meta method=serialize
	@caption Arve staatus

	@property only_billable type=checkbox ch_value=1 table=objects field=meta method=serialize
	@caption Arvele minevad tunnid ainult

	@property area type=select table=objects field=meta method=serialize
	@caption Valdkond

	@property res_type type=select table=objects field=meta method=serialize
	@caption Tulemused


@default group=view

	@property res type=table store=no no_caption=1

@groupinfo view caption="Vaata"
*/

class crm_report_entry extends class_base
{
	const AW_CLID = 1052;

	function crm_report_entry()
	{
		$this->init(array(
			"tpldir" => "applications/crm/crm_report_entry",
			"clid" => CL_CRM_REPORT_ENTRY
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "from":
			case "cust_type":
			case "state":
			case "res_type":
			case "bill_state":
			case "area":
			case "worker_sel":
			case "time_sel":
				static $stats_impl;
				if (!$stats_impl)
				{
					$stats_impl = get_instance("applications/crm/crm_company_stats_impl");
				}
				$fn = "_get_stats_s_".$prop["name"];
				$val = $prop["value"];
				$arr["obj_inst"] = obj(get_current_company());
				$retval = $stats_impl->$fn($arr);
				$prop["value"] = $val;
				break;

			case "res":
				$si = get_instance("applications/crm/crm_company_stats_impl");
				$fn = "_get_stats_s_".$prop["name"];
				$val = $prop["value"];
				$tmp = $arr;
				$r = array("MAX_FILE_SIZE" => 1);
				foreach($arr["obj_inst"]->properties() as $pn => $pv)
				{
					$r["stats_s_".$pn] = $pv;
				}
				$tmp["request"] = $r;
				$retval = $si->$fn($tmp);
				$prop["value"] = $val;
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
		}
		return $retval;
	}	

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}
}
?>
