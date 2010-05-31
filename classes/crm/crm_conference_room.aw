<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/crm/crm_conference_room.aw,v 1.2 2008/01/31 13:54:12 kristo Exp $
// room.aw - Ruum 
/*

@classinfo syslog_type=ST_CRM_CONFERENCE_ROOM relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=markop
@tableinfo room index=oid master_table=objects master_index=oid

@default table=room
@default group=general

@layout split type=hbox
	@layout left parent=split type=vbox closeable=1 area_caption=&Uuml;ldine
		@property name type=textbox parent=left table=objects
		@caption Nimi

		@property area type=textbox parent=left
		@caption Pindala
		
		@property description type=textarea rows=8 cols=35 parent=left
		@caption Kirjeldus

	@layout right parent=split type=vbox closeable=1 area_caption=Maks.kohti
		@property type_chair type=textbox parent=right
		@caption Toolid

		@property type_chair_table type=textbox parent=right
		@caption Tool + Laud
		
		@property type_conference_table type=textbox parent=right
		@caption N&otilde;upidamislaud
		
		@property type_u_table type=textbox parent=right
		@caption U-kujuline laud

		@property type_oval_table type=textbox parent=right
		@caption &Uuml;marlaud

		@property type_chair_diag type=textbox parent=right
		@caption Ainult toolid diagonaalis


*/

class crm_conference_room extends class_base
{
	const AW_CLID = 1143;

	function crm_conference_room()
	{
		$this->init(array(
			"tpldir" => "crm/crm_conference_room",
			"clid" => CL_CRM_CONFERENCE_ROOM
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
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
	function do_db_upgrade($t, $f)
	{
		if(!$this->db_table_exists($t))
		{
			$this->db_query("create table ".$t." (oid INT PRIMARY KEY)");
		}

		switch($f)
		{
			case "description":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "text"
				));
				return true;
			case "area":
			case "type_chair":
			case "type_chair_table":
			case "type_conference_table":
			case "type_u_table":
			case "type_oval_table":
			case "type_chair_diag":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int",
				));
				return true;
				break;
		}

	}
}
?>
