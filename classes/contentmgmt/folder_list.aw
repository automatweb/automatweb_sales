<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/contentmgmt/folder_list.aw,v 1.15 2008/01/31 13:52:14 kristo Exp $
// folder_list.aw - Kaustade nimekiri 
/*

@classinfo syslog_type=ST_FOLDER_LIST relationmgr=yes maintainer=kristo

@default table=objects
@default group=general

@property rootmenu type=relpicker reltype=RELTYPE_FOLDER field=meta method=serialize
@caption Juurkaust

@property template type=select field=meta method=serialize
@caption Kujundusmall

@property sort_by type=select field=meta method=serialize
@caption Kuidas sortida

@property show_comment type=chooser field=meta method=serialize
@caption N&auml;ita kausta all

@property no_folder_links type=checkbox ch_value=1 field=meta method=serialize
@caption &Auml;ra lingi kaustu

@property only_act type=checkbox ch_value=1 field=meta method=serialize
@caption N&auml;ita ainult aktiivseid kaustu

@property link_only_act type=checkbox ch_value=1 field=meta method=serialize
@caption Lingi ainult aktiivsed kaustad

@reltype FOLDER clid=CL_MENU value=1
@caption juurkaust

*/

class folder_list extends class_base
{
	const AW_CLID = 255;

	function folder_list()
	{
		$this->init(array(
			"tpldir" => "contentmgmt/folder_list",
			"clid" => CL_FOLDER_LIST
		));
	}

	function get_property($arr)
	{
		$data = &$arr["prop"];
		$retval = PROP_OK;
		switch($data["name"])
		{
			case "template":
				$tm = get_instance("templatemgr");
				$data["options"] = $tm->template_picker(array(
					"folder" => "contentmgmt/folder_list"
				));
				break;

			case "sort_by":
				$data["options"] = array(
					"objects.name" => "Nimi",
					"objects.jrk" => "J&auml;rjekord"
				);
				break;

			case "show_comment":
				$data["options"] = array(
					"comment" => "Kommentaari",	
					"doc" => "Esimest dokumenti"
				);
				break;
		};
		return PROP_OK;
	}

	/*
	function set_property($arr = array())
	{
		$data = &$arr["prop"];
		$retval = PROP_OK;
		switch($data["name"])
                {

		}
		return $retval;
	}	
	*/

	////
	// !this will be called if the object is put in a document by an alias and the document is being shown
	// parameters
	//    alias - array of alias data, the important bit is $alias[target] which is the id of the object to show
	function parse_alias($arr)
	{
		return $this->show(array("id" => $arr["alias"]["target"]));
	}

	function show($arr)
	{
		$ob = new object($arr["id"]);

		$tpl = "show.tpl";
		if ($ob->prop("template") != "")
		{
			$tpl = $ob->prop("template");
		}

		$sby = "objects.name";
		if ($ob->prop("sort_by") != "")
		{
			$sby = $ob->prop("sort_by");
		}

		if ($this->read_site_template($tpl, true) === false)
		{
			$this->read_template($tpl);
		}

		$ol = new object_list(array(
			"parent" => $ob->prop("rootmenu"),
			"class_id" => CL_MENU,
			"sort_by" => $sby,
			"status" => $ob->prop("only_act") ? STAT_ACTIVE : array(STAT_ACTIVE, STAT_NOTACTIVE),
			"site_id" => array()
		));

		$ssh = get_instance("contentmgmt/site_show");
		$d = get_instance(CL_DOCUMENT);

		$fls = "";
		for ($o = $ol->begin(); !$ol->end(); $o = $ol->next())
		{
			if ($this->is_template("SUBFOLDER"))
			{
				$sf = "";
				$sol = new object_list(array(
					"parent" => $o->id(),
					"class_id" => CL_MENU,
					"sort_by" => $sby,
					"status" => $ob->prop("only_act") ? STAT_ACTIVE : array(STAT_ACTIVE, STAT_NOTACTIVE)
				));
				for ($so = $sol->begin(); !$sol->end(); $so = $sol->next())
				{
					$this->vars(array(
						"name" => $so->name(),
						"link" => $ssh->make_menu_link($so),
						"selected" => selected($so->id() == aw_global_get("section")),
						"comment" => nl2br($so->comment())
					));
					$sf .= $this->parse("SUBFOLDER");
				}

				$this->vars(array(
					"SUBFOLDER" => $sf
				));
			}

			$this->vars(array(
				"name" => $o->name(),
				"link" => $ssh->make_menu_link($o),
				"selected" => selected($o->id() == aw_global_get("section")),
				"comment" => nl2br($o->comment())
			));

			if ($ob->prop("show_comment") == "doc")
			{
				$docs = $ssh->get_default_document(array(
					"obj" => $o
				));
				$doc = $docs;
				if (is_array($docs))
				{
					$doc = reset($docs);
				}
				$doco = obj($doc);
				$this->vars(array(
					"comment" => preg_replace("/#(\w+?)(\d+?)(v|k|p|)#/i","",$doco->prop("lead"))
				));
				$this->vars(array(
					"SHOW_COMMENT" => $this->parse("SHOW_COMMENT")
				));
			}
			else
			if ($ob->prop("show_comment") == "comment")
			{
				$this->vars(array(
					"SHOW_COMMENT" => $this->parse("SHOW_COMMENT")
				));
			}
			else
			{
				$this->vars(array(
					"SHOW_COMMENT" => ""
				));
			}

			if ($ob->prop("no_folder_links") || ($ob->prop("link_only_act") && $o->status() == STAT_NOTACTIVE))
			{
				$this->vars(array(
					"NO_LINK" => $this->parse("NO_LINK"),
					"HAS_LINK" => ""
				));
			}
			else
			{
				$this->vars(array(
					"NO_LINK" => "",
					"HAS_LINK" => $this->parse("HAS_LINK")
				));
			}
			$fls .= $this->parse("FOLDER");
		}

		if (is_oid($ob->prop("rootmenu")) && $this->can("view", $ob->prop("rootmenu")))
		{
			$rm = obj($ob->prop("rootmenu"));

			$this->vars(array(
				"FOLDER" => $fls,
				"root_name" => $rm->prop("name"),
				"root_link" => $ssh->make_menu_link($rm),
			));
		}
		return $this->parse();
	}
}
?>
