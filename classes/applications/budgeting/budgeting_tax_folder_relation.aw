<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/budgeting/budgeting_tax_folder_relation.aw,v 1.6 2008/05/14 15:45:20 markop Exp $
// budgeting_tax_folder_relation.aw - Eelarvestamise maksu kausta seos 
/*

@classinfo syslog_type=ST_BUDGETING_TAX_FOLDER_RELATION relationmgr=yes no_comment=1 no_status=1 maintainer=kristo prop_cb=1

@tableinfo aw_budgeting_tax_relation master_table=objects master_index=brother_of index=aw_oid

@default table=aw_budgeting_tax_relation
@default group=general

@property tax type=relpicker reltype=RELTYPE_TAX field=aw_tax
@caption Maks

@property folder type=textbox field=aw_folder
@caption Asukoht

	@property use_different_settings type=checkbox size=5 field=aw_use_different_settings
	@caption Kasuta eraldi seadeid

	@property use_used_settings type=select size=5 field=aw_use_used_settings
	@caption Kasuta olemasolevaid seadeid

	@property amount_final type=textbox size=5 field=aw_amt_final
	@caption Summa t&auml;isarv

	@property amount type=textbox size=5 field=aw_amt
	@caption Summa %

	@property max_deviation_minus type=textbox size=5 field=aw_max_deviation_minus
	@caption Maksimaalne projektip&otilde;hine muudatus -

	@property max_deviation_plus type=textbox size=5 field=aw_max_deviation_plus
	@caption Maksimaalne projektip&otilde;hine muudatus +

	@property pri type=textbox size=5 field=aw_pri
	@caption Prioriteet

	@property term type=textbox size=10 field=aw_term
	@caption Tingimus


@reltype TAX value=1 clid=CL_BUDGETING_TAX
@caption Maks

*/

class budgeting_tax_folder_relation extends class_base
{
	function budgeting_tax_folder_relation()
	{
		$this->init(array(
			"tpldir" => "applications/budgeting/budgeting_tax_folder_relation",
			"clid" => CL_BUDGETING_TAX_FOLDER_RELATION
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "folder":
				if($this->can("view" , $_GET["folder"]) && !$prop["value"])
				{
					$m = get_instance("applications/budgeting/budgeting_model");
					$prop["value"] = $m->_get_cat_id_from_obj(obj($_GET["folder"]));
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
		}
		return $retval;
	}	

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function do_db_upgrade($t,$f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_budgeting_tax_relation (aw_oid int primary key,aw_tax int, aw_folder varchar(50))");
			return true;
		}
		switch($f)
		{
			case "aw_term":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "varchar(255)"
				));
				return true;
			case "aw_pri":
			case "aw_use_different_settings":
			case "aw_use_used_settings":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				return true;
			case "aw_amt_final":
			case "aw_amt":
			case "aw_max_deviation_minus":
			case "aw_max_deviation_plus":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "double"
				));
				return true;
		}

	}
}
?>
