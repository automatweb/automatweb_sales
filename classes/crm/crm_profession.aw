<?php

// crm_profession.aw - Ametikoht
/*
@classinfo relationmgr=yes
@tableinfo kliendibaas_amet index=oid master_table=objects master_index=oid

@default table=objects
@default group=general

@classinfo no_status=1

@property main_toolbar type=toolbar_standard_obj store=no
@caption T&ouml;&ouml;riistad

@property ext_id field=subclass type=textbox
@caption Siduss&uuml;steemi ID

@property hr_price field=meta method=serialize type=textbox
@caption Tunnihind

@property jrk type=textbox size=4
@caption J&auml;rk

@property directive_link type=textbox field=meta method=serialize
@caption Viit ametijuhendile

@property directive type=relpicker reltype=RELTYPE_DESC_FILE field=meta method=serialize
@caption Ametijuhend

@property organization type=hidden table=kliendibaas_amet
@property section type=hidden table=kliendibaas_amet

@property name_in_plural type=textbox table=kliendibaas_amet
@caption Nimi mitmuses
@comment Ametinimetus mitmuses

@property skills type=relpicker reltype=RELTYPE_SKILL store=connect multiple=1 automatic=1
@caption P&auml;devused

property trans type=translator store=no group=trans props=name,name_in_plural
caption T&otilde;lkimine

@groupinfo transl caption=T&otilde;lgi
@default group=transl

	@property transl type=callback callback=callback_get_transl store=no
	@caption T&otilde;lgi

@groupinfo trans caption="T&otilde;lkimine"

@reltype SIMILARPROFESSION value=1 clid=CL_CRM_PROFESSION
@caption Sarnane amet

@reltype GROUP value=2 clid=CL_GROUP
@caption grupp

@reltype DESC_FILE value=3 clid=CL_FILE
@caption Ametijuhend

@reltype SKILL value=4 clid=CL_PERSON_SKILL
@caption Oskus

*/

class crm_profession extends class_base
{
	function crm_profession()
	{
		$this->init(array(
			"clid" => CL_CRM_PROFESSION
		));
		$this->trans_props = array(
			"name","name_in_plural"
		);
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "transl":
				$this->trans_save($arr, $this->trans_props);
				break;
		}
		return $retval;
	}

	function callback_get_transl($arr)
	{
		return $this->trans_callback($arr, $this->trans_props);
	}

	function do_db_upgrade($table, $field, $q, $err)
	{
		$ret_val = false;
		$migrate = false;

		if ("kliendibaas_amet" === $table)
		{
			if (empty($field))
			{
				$this->db_query("
					CREATE TABLE `kliendibaas_amet` (
						`oid` int(11) UNSIGNED NOT NULL default '0',
						`organization` int(11) UNSIGNED NOT NULL default '0',
						`section` int(11) UNSIGNED NOT NULL default '0',
						`name_in_plural` varchar(255) default '',
						PRIMARY KEY (`oid`),
						INDEX (`organization`)
					);
				");
				$ret_val = true;
			}
			elseif ("organization" === $field)
			{
				$this->db_add_col($table, array(
					"name" => "organization",
					"type" => "int(11) UNSIGNED NOT NULL default '0'"
				));
				$ret_val = true;
			}
			elseif ("section" === $field)
			{
				$this->db_add_col($table, array(
					"name" => "section",
					"type" => "int(11) UNSIGNED NOT NULL default '0'"
				));
				$ret_val = true;
			}
		}

		return $ret_val;
	}
}

