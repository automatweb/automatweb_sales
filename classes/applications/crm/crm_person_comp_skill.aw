<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/crm/crm_person_comp_skill.aw,v 1.2 2007/12/06 14:33:17 kristo Exp $
// crm_person_comp_skill.aw - Arvutioskus 
/*

@classinfo syslog_type=ST_CRM_PERSON_COMP_SKILL no_name=1 no_comment=1 no_status=1 maintainer=markop

@default table=objects
@default group=general

@property program type=textbox field=name
@caption Keel/Programm

@default field=meta
@default method=serialize

@property level type=select
@caption Tase

*/

class crm_person_comp_skill extends class_base
{
	function crm_person_comp_skill()
	{
		$this->init(array(
			"clid" => CL_CRM_PERSON_COMP_SKILL
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "level":
				$prop["options"] = array(
					0 => t("-- vali --"),
					1 => t("algaja"),
					2 => t("tavakasutaja"),
					3 => t("edasijõudnu"),
					4 => t("ekspert"),
				);
				break;
		}
		return $retval;
	}
}
?>
