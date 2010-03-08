<?php
/*
@tableinfo banner_clients index=id master_table=objects master_index=brother_of
@classinfo syslog_type=ST_BANNER_LOCATION relationmgr=yes maintainer=kristo

@default table=objects
@default group=general

	@property cont_place type=select store=no
	@caption Koht veebis

	@property cont_doc_tpl type=select store=no
	@caption Dokumendi template

	@property dynamic_switch_interval type=textbox size=5 field=meta method=serialize
	@caption D&uuml;naamilise vaheldumise intervall

	@property html type=textarea cols=80 rows=10 table=banner_clients 
	@caption Asukoha HTML


*/

class banner_client extends class_base
{
	function banner_client()
	{
		$this->init(array(
			"tpldir" => "banner",
			"clid" => CL_BANNER_CLIENT
		));

		$this->def_html = "<a href='/orb.aw?class=banner&action=proc_banner&gid=%s&click=1&ss=[ss]'><img src='/orb.aw?class=banner&action=proc_banner&gid=%s&ss=[ss]' border=0></a>";
	}

	function get_property($arr)
	{
		$prop =& $arr["prop"];
		switch($prop["name"])
		{
			case "cont_place":
				if (!$arr["request"]["mgr"])
				{
					return PROP_IGNORE;
				}
				$mg = obj($arr["request"]["mgr"]);
				$pls = aw_ini_get("promo.areas");
				$prop["options"] = array();
				foreach(safe_array($mg->prop("container_places")) as $plid)
				{
					$prop["options"][$plid] = $pls[$plid]["name"];
				}
				break;

			case "cont_doc_tpl":
				if (!$arr["request"]["mgr"])
				{
					return PROP_IGNORE;
				}
				$mg = obj($arr["request"]["mgr"]);
				$prop["options"] = array();
				foreach(safe_array($mg->prop("document_templates")) as $plid)
				{
					$tmp = obj($plid);
					$prop["options"][$tmp->prop("t_id")] = $tmp->name();
				}
				break;
		}

		return PROP_OK;
	}

	function set_property($arr)
	{
		$prop =& $arr["prop"];
		switch($prop["name"])
		{
		}

		return PROP_OK;
	}

	function callback_post_save($arr)
	{
		if ($arr["obj_inst"]->prop("html") == "")
		{
			$html = sprintf($this->def_html, $arr["obj_inst"]->id(),$arr["obj_inst"]->id());
			$arr["obj_inst"]->set_prop("html", $html);
			$arr["obj_inst"]->save();
		}
	}

	function show($arr)
	{
		return "[bloc".$arr["id"]."]";
	}	

	/**
		@attrib name=fetch_banner_content
		@param loc required
	**/
	public function fetch_banner_content($arr)
	{
		die(get_instance(CL_BANNER)->get_banner_html($arr["loc"], null, false));
	}
}
?>