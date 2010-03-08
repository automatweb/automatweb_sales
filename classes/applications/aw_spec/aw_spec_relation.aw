<?php
/*
@classinfo syslog_type=ST_AW_SPEC_RELATION relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo allow_rte=2
@tableinfo aw_spec_relations master_index=brother_of master_table=objects index=aw_oid

@default table=aw_spec_relations
@default group=general

	@property rel_from type=select field=aw_rel_from
	@caption Seos kust

	@property rel_to type=select field=aw_rel_to
	@caption Seos kuhu

*/

class aw_spec_relation extends class_base
{
	function aw_spec_relation()
	{
		$this->init(array(
			"tpldir" => "applications/aw_spec/aw_spec_relation",
			"clid" => CL_AW_SPEC_RELATION
		));
	}

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_spec_relations(aw_oid int primary key, aw_rel_from varchar(100), aw_rel_to varchar(100))");
			return true;
		}
	}

	function _get_rel_from($arr)
	{
		$arr["prop"]["options"] = aw_spec::get_class_picker(obj($arr["obj_inst"]->parent()));
	}

	function _get_rel_to($arr)
	{
		$arr["prop"]["options"] = aw_spec::get_class_picker(obj($arr["obj_inst"]->parent()));
	}
}

?>
