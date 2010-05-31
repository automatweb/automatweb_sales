<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_AW_SPEC_PROPERTY relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo allow_rte=2
@tableinfo aw_spec_properties master_index=brother_of master_table=objects index=aw_oid

@default table=aw_spec_properties
@default group=general


	@property prop_type type=select field=aw_prop_type
	@caption Omaduse t&uuml;&uuml;p

	@property prop_desc type=textarea field=aw_prop_desc rows=10 cols=50  richtext=1
	@caption Omaduse kirjeldus

	@property errors_and_messages type=textarea rows=10 cols=50 field=aw_errors_and_messages  richtext=1
	@caption Teated ja vead

@default group=longdesc
	
	@property longdesc type=textarea rows=80 cols=80 field=aw_longdesc  richtext=1
	@caption Pikem kirjeldus

@default group=use_cases

	@property use_cases type=releditor mode=manager reltype=RELTYPE_USE_CASE no_caption=1 store=connect props=name table_fields=name direct_links=1

@groupinfo longdesc caption="Kirjeldus"
@groupinfo use_cases caption="Kasutuslood"

@reltype USE_CASE value=10 clid=CL_AW_SPEC_USE_CASE 
@caption Kasutuslugu
*/

class aw_spec_property extends class_base
{
	const AW_CLID = 1428;

	function aw_spec_property()
	{
		$this->init(array(
			"tpldir" => "applications/aw_spec/aw_spec_property",
			"clid" => CL_AW_SPEC_PROPERTY
		));
	}

	function callback_mod_reforb(&$arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_spec_properties(aw_oid int primary key, aw_prop_type varchar(255), aw_prop_desc text)");
			return true;
		}

		switch($f)
		{
			case "aw_longdesc":
			case "aw_errors_and_messages":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "mediumtext"
				));
				return true;
		}
	}
}

?>
