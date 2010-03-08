<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/crm/crm_email.aw,v 1.2 2007/12/06 14:33:17 kristo Exp $
// crm_email.aw - CRM Meil 
/*

@classinfo syslog_type=ST_CRM_EMAIL relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=markop
@default table=objects
@tableinfo aw_crm_mails index=aw_oid master_table=objects master_index=brother_of


@default group=general

	@property name type=textbox table=objects field=name
	@caption Teema

	@property customer type=relpicker reltype=RELTYPE_CUSTOMER table=aw_crm_mails field=aw_customer
	@caption Klient

	@property project type=relpicker reltype=RELTYPE_PROJECT table=aw_crm_mails field=aw_project
	@caption Projekt

	@property is_done type=checkbox field=flags method=bitmask ch_value=8 // OBJ_IS_DONE
	@caption Tehtud

	@property from type=text table=aw_crm_mails field=aw_from
	@caption Kellelt

	@property to type=text table=aw_crm_mails field=aw_to
	@caption Kellele

	@property date type=text table=aw_crm_mails field=aw_date
	@caption Millal

	@property content type=text table=aw_crm_mails field=aw_content
	@caption Sisu

	@property task type=relpicker reltype=RELTYPE_TASK table=aw_crm_mails field=aw_task
	@caption Toimetus

	@property imap_id type=hidden table=aw_crm_mails field=aw_imap_id
	@caption IMAP id


@reltype CUSTOMER value=1 clid=CL_CRM_COMPANY,CL_CRM_PERSON
@caption Klient

@reltype PROJECT value=2 clid=CL_PROJECT
@caption Projekt

@reltype TASK value=3 clid=CL_TASK,CL_CRM_CALL,CL_CRM_MEETING
@caption Toimetus

@reltype ATTACH value=4 clid=CL_FILE
@caption Manus

@reltype PARTICIPANT value=5 clid=CL_CRM_PERSON
@caption Osaleja

*/

class crm_email extends class_base
{
	function crm_email()
	{
		$this->init(array(
			"tpldir" => "applications/crm/crm_email",
			"clid" => CL_CRM_EMAIL
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "from":
			case "to":

				if (strpos($prop["value"], "<") === false)
				{
					$prop["value"] = html::href(array(
						"url" => "mailto:".$prop["value"],
						"caption" => $prop["value"]
					));
				}
				else
				{
					preg_match("/(.*)<(.*)>/imsU", $prop["value"], $mt);
					$prop["value"] = $mt[1]." &lt;".html::href(array(
						"url" => "mailto:".$mt[2],
						"caption" => $mt[2]
					))."&gt;";
				}
				break;

			case "date":
				$prop["value"] = date("d.m.Y H:i:s", $prop["value"]);
				break;
	
			case "content":
				$prop["value"] = nl2br(htmlspecialchars($prop["value"]));
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

	function do_db_upgrade($table, $field, $q, $err)
	{
		if ($table == "aw_crm_mails" && $field == "")
		{
			$this->db_query("create table aw_crm_mails (
				aw_oid int primary key, 
				aw_customer int,
				aw_project int,
				aw_from varchar(100),
				aw_to varchar(100),
				aw_date int, 
				aw_content mediumtext,
				aw_task int,
				aw_imap_id int
			)");
			return true;
		}
	}
}
?>
