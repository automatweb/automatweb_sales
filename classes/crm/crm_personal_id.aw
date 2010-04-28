<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/crm/crm_personal_id.aw,v 1.2 2008/01/31 13:54:15 kristo Exp $
// crm_personal_id.aw - Isikutunnistus 
/*

@classinfo syslog_type=ST_CRM_PERSONAL_ID relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=markop

@tableinfo crm_personal_id index=oid master_table=objects master_index=oid

@default table=objects
@default group=general

	@property category type=select table=crm_personal_id
	@caption Liik

	@property number type=textbox table=crm_personal_id
	@caption Dokumendi nr.

	@property publisher type=textbox table=crm_personal_id
	@caption V&auml;ljaandja

	@property publish_time type=date_select table=crm_personal_id
	@caption V&auml;ljaandmise aeg

	@property valid type=date_select table=crm_personal_id
	@caption Kehtib kuni

	@property info type=textarea table=crm_personal_id
	@caption Lisainfo
*/

class crm_personal_id extends class_base
{
	const AW_CLID = 1043;

	function crm_personal_id()
	{
		// change this to the folder under the templates folder, where this classes templates will be, 
		// if they exist at all. Or delete it, if this class does not use templates
		$this->init(array(
			"tpldir" => "crm/crm_personal_id",
			"clid" => CL_CRM_PERSONAL_ID
		));
	}

	//////
	// class_base classes usually need those, uncomment them if you want to use them
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

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	////////////////////////////////////
	// the next functions are optional - delete them if not needed
	////////////////////////////////////

	////
	// !this will be called if the object is put in a document by an alias and the document is being shown
	// parameters
	//    alias - array of alias data, the important bit is $alias[target] which is the id of the object to show
	function parse_alias($arr)
	{
		return $this->show(array("id" => $arr["alias"]["target"]));
	}

	////
	// !this shows the object. not strictly necessary, but you'll probably need it, it is used by parse_alias
	function show($arr)
	{
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");
		$this->vars(array(
			"name" => $ob->prop("name"),
		));
		return $this->parse();
	}

	function _get_category($arr)
	{
		$arr['prop']['options'] = array(
			"pass" => t("Pass"),
			"juhiluba" => t("Juhiluba"),
			"id_kaart" => t("ID kaart"),
		);
		return PROP_OK;
	}

	/**
		DB UPGRADE
	**/
	function do_db_upgrade($table, $field, $query, $error)
	{
		// this should be the way to detect, if table exist:
		if (empty($field))
		{
			$this->db_query('CREATE TABLE '.$table.' (oid INT PRIMARY KEY NOT NULL)');
			return true;
		}

		switch ($field)
		{
			case 'category':
			case 'number':
			case 'publisher':
				$this->db_add_col($table, array(
					'name' => $field,
					'type' => 'varchar(255)'
				));
				return true;
			case 'publish_time':
			case 'valid':
				$this->db_add_col($table, array(
					'name' => $field,
					'type' => 'int'
				));
				return true;
			case 'info':
				$this->db_add_col($table, array(
					'name' => $field,
					'type' => 'text'
				));
				return true;
		}

		return false;
	}

}
?>
