<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/crm/crm_company_status.aw,v 1.4 2008/02/22 10:03:29 robert Exp $
// crm_company_status.aw - Organisatsiooni Staatus 
/*

@classinfo syslog_type=ST_CRM_COMPANY_STATUS relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=markop

@default table=objects
@default group=general

@property category type=select field=meta method=serialize
@caption Tüüp

*/

class crm_company_status extends class_base
{
	const AW_CLID = 1327;

	function crm_company_status()
	{
		$this->init(array(
			"tpldir" => "crm/crm_company_status",
			"clid" => CL_CRM_COMPANY_STATUS
		));
	}

	/**
	@attrib api=1
	**/
	function categories($id)
	{
		$categories = array(
			1 => t('Tegevusvaldkond'),
			2 => t('Suhte vanus/t&uuml&uuml;p'),
			3 => t('Suhte iseloom'),
			4 => t('Majanduslik skaala'),
			5 => t('B2B mudel'),
			6 => t('M&uuml;&uuml;gipotentsiaal'),
		);
		if($id)
		{
			return $categories[$id];
		}
		else
		{
			return $categories;
		}
	}
	
	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "category":
				if($arr["request"]["action"] == "new" && $arr["request"]["category"] == 0)
				{
					$prop["options"] = $this->categories(0);
				}
				elseif($arr["request"]["action"] == "new")
				{
					$prop["type"] = "text";
					$prop["value"] = $this->categories($arr["request"]["category"]);
				}
				else
				{
					$prop["type"] = "text";
					$prop["value"] = $this->categories($prop["value"]);
					$prop["store"] = "no";
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
			case "category":
				if($arr["request"]["category"])
				{
					$prop["value"] = $arr["request"]["category"];
				}
				else
				{
					$prop["store"] = "no";
				}
			break;
		}
		return $retval;
	}	

	function callback_mod_reforb(&$arr)
	{
		if($_GET["category"])
		{
			$arr["category"] = $_GET["category"];
		}
		$arr["post_ru"] = post_ru();
	}

	////////////////////////////////////
	// the next functions are optional - delete them if not needed
	////////////////////////////////////

	/** this will get called whenever this object needs to get shown in the website, via alias in document **/
	function show($arr)
	{
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");
		$this->vars(array(
			"name" => $ob->prop("name"),
		));
		return $this->parse();
	}

//-- methods --//
}
?>
