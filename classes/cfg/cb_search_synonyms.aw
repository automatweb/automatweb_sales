<?php
/*
@classinfo syslog_type=ST_CB_SEARCH_SYNONYMS relationmgr=yes no_status=1 maintainer=kristo

@default table=objects
@default group=general

@property desc type="text" store=no 

@property syntb type=callback callback=callback_get_syntb store=no
@caption S&uuml;non&uuml;mise sisestamine

*/

class cb_search_synonyms extends class_base
{
	function cb_search_synonyms()
	{
		$this->init(array(
			"tpldir" => "cfg/cb_search_synonyms",
			"clid" => CL_CB_SEARCH_SYNONYMS
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "desc":
				$prop["value"] = t("Sisestage ridadele sama t&auml;hendusega s&otilde;nad, eraldatud komadega");
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
			case "syntb":
				$this->do_save_syns($arr);
				break;
		}
		return $retval;
	}	

	private function do_save_syns($arr)
	{
		$syns = array();
		foreach(safe_array($arr["request"]["old"]) as $syn)
		{
			if ($syn != "")
			{
				$syns[] = $syn;
			}
		}
		foreach(safe_array($arr["request"]["new"]) as $syn)
		{
			if ($syn != "")
			{
				$syns[] = $syn;
			}
		}
		$arr["obj_inst"]->set_meta("syns", $syns);
	}

	function callback_get_syntb($arr)
	{
		$ret = array();

		$curs = safe_array($arr["obj_inst"]->meta("syns"));
		foreach($curs as $nr => $syn)
		{
			$ret["old[".$nr."]"] = array(
				"name" => "old[".$nr."]",
				"type" => "textbox",
				"size" => 80,
				"value" => $syn
			);
		}

		for ($i = 0; $i < 5; $i++)
		{
			$ret["new[".$i."]"] = array(
				"name" => "new[".$i."]",
				"type" => "textbox",
				"size" => 80,
			);
		}

		return $ret;
	}
}
?>
