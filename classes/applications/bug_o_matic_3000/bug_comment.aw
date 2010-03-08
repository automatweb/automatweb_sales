<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/bug_o_matic_3000/bug_comment.aw,v 1.9 2008/10/07 13:40:07 kristo Exp $
// bug_comment.aw - Bugi kommentaar 
/*

@classinfo syslog_type=ST_BUG_COMMENT relationmgr=yes no_status=1 prop_cb=1 maintainer=robert

@tableinfo aw_bug_comments index=aw_oid master_index=brother_of master_table=objects

@default table=objects
@default group=general

@property comment type=textarea rows=5 cols=50 table=objects field=comment
@caption Kommentaar

@property prev_state type=select table=aw_bug_comments field=aw_prev_state
@caption Eelmine staatus

@property new_state type=select table=aw_bug_comments field=aw_new_state
@caption Uus staatus

@property add_wh_guess type=textbox size=5 table=aw_bug_comments field=aw_add_wh_guess
@caption Lisandunud prognoositunnid

@property add_wh type=textbox size=5 table=aw_bug_comments field=aw_add_wh
@caption Lisandunud t&ouml;&ouml;tunnid

@property add_wh_cust type=textbox size=5 table=aw_bug_comments field=aw_add_wh_cust
@caption Lisandunud tunnid kliendile

@property send_bill type=checkbox table=aw_bug_comments field=aw_send_bill ch_value=1
@caption Saata arve

@property activity_stats_type type=hidden table=aw_bug_comments field=aw_activity_stats_type
@caption Statistika t&uuml;&uuml;p

@property bill type=relpicker reltype=RELTYPE_BILL table=aw_bug_comments field=aw_bill
@caption Arve

@property bug type=relpicker reltype=RELTYPE_BUG table=aw_bug_comments field=aw_bug
@caption Arve

@reltype BILL value=1 clid=CL_CRM_BILL
@caption Arve

@reltype BUG value=2 clid=CL_BUG
@caption Bugi
*/

class bug_comment extends class_base
{
	function bug_comment()
	{
		$this->init(array(
			"tpldir" => "applications/bug_o_matic_3000/bug_comment",
			"clid" => CL_BUG_COMMENT
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "prev_state":
			case "new_state":
				$bi = get_instance(CL_BUG);
				$prop["options"] = array("" => t("--vali--"))+ $bi->get_status_list();
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

	function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_bug_comments (aw_oid int primary key, aw_prev_state int, aw_new_state int, aw_add_wh double)");
			return true;
		}

		switch($f)
		{
			case "aw_add_wh_cust":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "double"
				));
				return true;
			case "aw_add_wh_guess":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "double"
				));
				return true;
			case "aw_send_bill":
			case "aw_activity_stats_type":
			case "aw_bill":
			case "aw_bug":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				return true;
		}
	}
}
?>
