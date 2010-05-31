<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/crm/crm_authorization.aw,v 1.4 2008/01/02 11:57:33 markop Exp $
// crm_authorization.aw - Volitus 
/*

@tableinfo aw_authorizations index=aw_oid master_table=objects master_index=brother_of maintainer=markop

@classinfo syslog_type=ST_CRM_AUTHORIZATION relationmgr=yes no_comment=1 no_status=1 prop_cb=1

@default table=aw_authorizations
@default group=general

	@property our_company type=relpicker store=connect reltype=RELTYPE_OUR_COMPANY
	@caption Meie firma

	@property customer_company type=relpicker store=connect reltype=RELTYPE_CUSTOMER_COMPANY
	@caption Klientfirma

	@property authorized_person type=relpicker store=connect reltype=RELTYPE_PERSON
	@caption Volitatav isik

	@property start type=date_select
	@caption Kehtib alates

	@property end type=date_select
	@caption Kehtib kuni

	@property authorization_add type=submit store=no
	@caption Lisa volitus

@reltype PERSON value=1 clid=CL_CRM_PERSON
@caption Isik

@reltype OUR_COMPANY value=2 clid=CL_CRM_COMPANY
@caption Organisatsioon

@reltype CUSTOMER_COMPANY value=3 clid=CL_CRM_COMPANY
@caption Organisatsioon


*/

class crm_authorization extends class_base
{
	const AW_CLID = 1368;

	function crm_authorization()
	{
		$this->init(array(
			"tpldir" => "applications/crm/crm_authorization",
			"clid" => CL_CRM_AUTHORIZATION
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "our_company":
			case "customer_company":
				if($arr["new"])
				{
					if(is_oid($arr["request"][$prop["name"]]))
					{
						$o = obj($arr["request"][$prop["name"]]);
						$prop["value"] = $arr["request"][$prop["name"]];
						$prop["options"][ $arr["request"][$prop["name"]]] =  $o->name();
					}
				}
				break;
			case "authorized_person":
				if($arr["new"])
				{
					if(is_oid($arr["request"]["person"]))
					{
						$o = obj($arr["request"]["person"]);
						$prop["value"] = $arr["request"]["person"];
						$prop["options"][ $arr["request"]["person"]] =  $o->name();
					}
				};
				break;
			case "authorization_add":
				if(!$arr["request"]["return_after_save"])
				{
					return PROP_IGNORE;
				}
				//oleks vaja nüüd js siia propertile ümber manada, et eelmise vaare refreshiks ja selle akna kinni paneks
				break;

			//-- get_property --//
		};
		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			//-- set_property --//
		}
		return $retval;
	}	

	function callback_mod_reforb(&$arr)
	{
		$arr["post_ru"] = post_ru();
		$arr["return_after_save"] = $_GET["return_after_save"];
	}

	function callback_mod_retval($arr)
	{
		if($arr['request']['return_after_save'])
		{
			$arr['args']['return_after_save'] = ($arr['request']['return_after_save']);
		}
	}

	function callback_generate_scripts($arr)
	{
		$sc = "";
		if($arr["request"]["return_after_save"] && $arr["request"]["just_saved"])
		{
			$sc = "
				window.opener.document.changeform.submit();
				window.close();";
		}

		return $sc;
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

	function do_db_upgrade($t, $f)
	{
		if ($f == "" && $t == "aw_authorizations")
		{
			$this->db_query("CREATE TABLE aw_authorizations(aw_oid int primary key,
				our_company int,
				customer_company int,
				authorized_person int,
				start int,
				end int
			)");
			return true;
		}
		return false;
	}

//-- methods --//
}
?>
