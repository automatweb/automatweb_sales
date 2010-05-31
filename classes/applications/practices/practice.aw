<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_PRACTICE relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=dragut
@tableinfo aw_practice master_index=brother_of master_table=objects index=aw_oid

@default table=aw_practice
@default group=general

@property author type=relpicker reltype=RELTYPE_AUTHOR
@caption Autor

@layout frame type=hbox width=50%:50% closeable=1 area_caption="Sisu"

	@layout left_pane type=vbox parent=frame

		@property description type=textarea cols=70 rows=30 captionside=top parent=left_pane
		@caption Kirjeldus
	
	@layout right_pane type=vbox parent=frame

		@property attachment type=multifile_upload reltype=RELTYPE_ATTACHMENT parent=right_pane captionside=top store=no max_files=99
		@caption Fail

@reltype AUTHOR value=1 clid=CL_CRM_PERSON
@caption Autor

@reltype ATTACHMENT value=2 clid=CL_FILE,CL_IMAGE
@caption Manus

*/

class practice extends class_base
{
	const AW_CLID = 1526;

	function practice()
	{
		$this->init(array(
			"tpldir" => "applications/practices/practice",
			"clid" => CL_PRACTICE
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
		}

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

	function callback_mod_reforb(&$arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function show($arr)
	{
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");
		$this->vars(array(
			"name" => $ob->prop("name"),
		));
		return $this->parse();
	}

	function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_practice(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "description":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "text"
				));
				return true;
			case "attachment":
			case "author":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				return true;
		}
	}
}

?>
