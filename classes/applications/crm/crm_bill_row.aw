<?php
// crm_bill_row.aw - Arve rida
/*

@classinfo syslog_type=ST_CRM_BILL_ROW relationmgr=yes no_status=1 prop_cb=1
@tableinfo aw_crm_bill_rows index=aw_oid master_index=brother_of master_table=objects

@default table=aw_crm_bill_rows
@default group=general


@property name type=textarea rows=5 cols=40 table=objects field=name
@caption Nimi

@property comment type=textbox table=objects field=comment
@caption Tekst arvel

@property amt type=textbox size=5 field=aw_amt
@caption Kogus

@property prod type=relpicker reltype=RELTYPE_PROD field=aw_prod
@caption Toode

@property price type=textbox size=7 field=aw_price
@caption Hind

@property sum type=text store=no
@caption Summa

@property unit type=textbox field=aw_unit
@caption &Uuml;hik

@property writeoff type=checkbox ch_value=1 field=aw_writeoff
@caption Maha kantud

@property has_tax type=checkbox ch_value=1 field=aw_has_tax
@caption Lisandub k&auml;ibemaks?

@property tax type=checkbox ch_value=1 field=aw_tax
@caption K&auml;ibemaksu %

@property date type=textbox field=aw_date
@caption Kuup&auml;ev

@property desc type=textarea rows=5 cols=30 field=aw_desc
@caption Kirjeldus

@property name_group_comment type=textarea rows=2 cols=30 field=aw_name_group_comment
@caption Koondkommentaar

@property people type=relpicker reltype=RELTYPE_PEOPLE multiple=1 table=objects field=meta method=serialize
@caption Isikud

@property task_row type=relpicker reltype=RELTYPE_TASK_ROW store=connect multiple=1
@caption Toimetuse read

@property project type=select
@caption Projekt

// RELTYPES
@reltype PROD value=1 clid=CL_SHOP_PRODUCT
@caption Toode

@reltype TASK_ROW value=2 clid=CL_TASK_ROW
@caption Toimetuse rida

@reltype TASK value=3 clid=CL_TASK
@caption Toimetus

@reltype PEOPLE value=4 clid=CL_CRM_PERSON
@caption Isik

@reltype EXPENSE value=5 clid=CL_CRM_EXPENSE
@caption Muu kulu

@reltype BUG value=6 clid=CL_BUG
@caption Bugi

*/

class crm_bill_row extends class_base
{
	function crm_bill_row()
	{
		$this->init(array(
			"tpldir" => "applications/crm/crm_bill_row",
			"clid" => CL_CRM_BILL_ROW
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "sum":
				$prop["value"] = str_replace(",", ".", $arr["obj_inst"]->prop("amt")) * str_replace(",", ".", $arr["obj_inst"]->prop("price"));
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
		if ($table === "aw_crm_bill_rows" && $field == "")
		{
			$this->db_query("create table aw_crm_bill_rows (
				aw_oid int primary key,
				aw_amt double,
				aw_prod int,
				aw_price double,
				aw_unit varchar(100),
				aw_has_tax tinyint,
				aw_date varchar(255),
				aw_writeoff int
			)");
			return true;
		}

		switch($field)
		{
			case "aw_desc":
				$this->db_add_col($table, array(
					"name" => $field,
					"type" => "text"
				));
				// convert data as well
				$ol = new object_list(array(
					"class_id" => CL_CRM_BILL_ROW
				));
				foreach($ol->arr() as $o)
				{
					$o->set_name($o->name());
					$o->save();
				}
				return true;

			case "aw_name_group_comment":
				$this->db_add_col($table, array(
					"name" => "aw_name_group_comment",
					"type" => "text"
				));
				return true;

			case "project":
			case "aw_writeoff":
				$this->db_add_col($table, array(
					"name" => $field,
					"type" => "int"
				));
				return true;

			case "aw_tax":
				$this->db_add_col($table, array(
					"name" => $field,
					"type" => "double"
				));
				return true;
		}
	}
}
