<?php
// $Header: /home/cvs/automatweb_dev/classes/contentmgmt/pullout.aw,v 1.1 2008/02/21 19:44:17 kristo Exp $
// pullout.aw - Pullout manager

/*
	@classinfo relationmgr=yes syslog_type=ST_PULLOUT maintainer=kristo
	
	@default table=objects
	@default field=meta
	@default method=serialize
	@default group=general

	@property groups type=select multiple=1 size=15
	@caption Vali grupid, kellele pullouti n&auml;idatakse

	@property docs type=relpicker reltype=RELTYPE_DOCUMENT
	@caption Vali dokument, mida n&auml;idata

	@property align type=select
	@caption Align

	@property right type=textbox size=10
	@caption Paremalt

	@property width type=textbox size=10
	@caption Laius

	@property template type=select
	@caption Template

@reltype DOCUMENT value=1 clid=CL_DOCUMENT
@caption n&auml;idatav dokument

*/
				
class pullout extends class_base
{
	function pullout()
	{
		$this->init(array(
			"tpldir" => "pullout",
			"clid" => CL_PULLOUT,
		));
		$this->align = array(
			"left" => "Vasak",
			"center" => "Keskel",
			"right" => "Paremal"
		);
	}

	function get_property($args)
	{
		$data = &$args["prop"];
		$retval = true;
		switch($data["name"])
		{
			case "align":
				$data["options"] = $this->align;
				break;

			case "groups":
				$ol = new object_list(array(
					"class_id" => CL_GROUP,
					"site_id" => array(),
					"lang_id" => array()
				));
				$data["options"] = $ol->names(array(
					"add_folders" => true
				));
				break;

			case "template":
				$data["options"] = $this->get_template_picker();
				break;

		}
		return PROP_OK;
	}

	////
	// !Aliaste parsimine
	function parse_alias($args = array())
	{
		extract($args);
		if (!$alias)
		{
			return "";
		}
		return $this->view(array("id" => $alias["target"],"doc" => $oid));
	}

	function view($arr)
	{
		extract($arr);
		$o = obj($id);

		$gidlist = aw_global_get("gidlist_oids");
		$found = false;
		$mg = $o->meta("groups");
		if (is_array($mg))
		{
			foreach($mg as $gid)
			{
				if ($gidlist[$gid] == $gid)
				{
					$found = true;
				}
			}
			if (count($mg) < 1)
			{
				$found = true;
			}
		}
		else
		{
			$found = true;
		}

		if (count($mg) < 1)
		{
			$found = true;
		}

		if (!$found || $o->meta("docs") == $oid)
		{
			return "";
		}

		$do = get_instance(CL_DOCUMENT);
		if ($o->meta("template") == "" || $o->meta("template") == "0")
		{
			if ($GLOBALS["print"] == 1)
			{
				$o->set_meta("template","print.tpl");
			}
			else
			{
				$o->set_meta("template","plain.tpl");
			}
		}
		$old_print = $GLOBALS["print"];
		$GLOBALS["print"] = 0;
		$_GET["print"] = 0;
		aw_global_set("print", 0);
		$this->read_template("pullout.tpl" /*$o->meta("template")*/);
		$this->vars(array(
			"width" => $o->meta("width"),
			"align" => $o->meta("align"),
			"right" => $o->meta("right"),
			"content" => $do->gen_preview(array(
				"docid" => $o->meta("docs"),
				"tpl" => $o->meta("template")
			)),
			"title" => $o->name()
		));
		$GLOBALS["print"] = $old_print;
		$_GET["print"] = $old_print;
		aw_global_set("print", $old_print);
		return $this->parse();
	}

	function get_template_picker()
	{
		$ret = array("0" => "");
		
		$ol = new object_list(array(
			"class_id" => CL_CONFIG_AW_DOCUMENT_TEMPLATE,
			"lang_id" => array(),
			"site_id" => array()
		));
		for($o = $ol->begin(); !$ol->end(); $o = $ol->next())
		{
			$ret[$o->prop("filename")] = $o->name();
		}
		return $ret;
	}
}
?>
