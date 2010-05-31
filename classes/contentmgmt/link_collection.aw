<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/contentmgmt/link_collection.aw,v 1.5 2008/04/30 12:27:10 kristo Exp $
// link_collection.aw - Lingikogu 
/*

@classinfo syslog_type=ST_LINK_COLLECTION relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo
@tableinfo aw_link_collection index=aw_oid master_index=brother_of master_table=objects
@default table=aw_link_collection

@default group=general

	@property type type=select field=aw_type
	@caption T&uuml;&uuml;p

	@property ordering type=select field=aw_ordering
	@caption Linkide j&auml;rjestamine

	@property kw_ordering type=select field=aw_kw_ordering
	@caption V&otilde;tmes&otilde;nade j&auml;rjestamine

	
@default group=folders

	@property folders type=table store=no no_caption=1
	@caption Kataloogid

@groupinfo folders caption="Kataloogid"

@reltype FOLDER value=1 clid=CL_MENU
@caption Kataloog
*/

define("LINKCOLL_TYPE_FLD", 0);
define("LINKCOLL_TYPE_KW", 1);

define("LINKCOLL_SORT_ALPHA", 0);
define("LINKCOLL_SORT_ORD", 1);
class link_collection extends class_base
{
	const AW_CLID = 1062;

	function link_collection()
	{
		$this->init(array(
			"tpldir" => "contentmgmt/link_collection",
			"clid" => CL_LINK_COLLECTION
		));

		$this->types = array(
			LINKCOLL_TYPE_FLD => t("Kaustadep&otilde;hine"),
			LINKCOLL_TYPE_KW => t("V&otilde;tmes&otilde;nade p&otilde;hine")
		);

		$this->orderings = array(
			LINKCOLL_SORT_ORD => t("J&auml;rjekord"),
			LINKCOLL_SORT_ALPHA => t("T&auml;hestik")
		);
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "type":
				$prop["options"] = $this->types;
				break;

			case "ordering":
				$prop["options"] = $this->orderings;
				break;

			case "folders":
				$this->_folders($arr);
				break;

			case "kw_ordering":
				if ($arr["obj_inst"]->prop("type") != LINKCOLL_TYPE_KW)
				{
					return PROP_IGNORE;
				}
				$prop["options"] = $this->orderings;
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
			case "folders":
				$arr["obj_inst"]->set_meta("subf", $arr["request"]["subf"]);
				break;
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

		if ($ob->prop("type") == LINKCOLL_TYPE_FLD)
		{
			$this->_disp_fold($ob);
		}
		else
		{
			$this->_disp_kw($ob);
		}

		return $this->parse();
	}

	function _disp_fold($o)
	{
		// get all folders
		$flds = $this->get_folders($o);
		if (!count($flds))
		{
			return;
		}

		$links = new object_list(array(
			"class_id" => CL_EXTLINK,
			"parent" => $flds,
			"sort_by" => $o->prop("ordering") == LINKCOLL_SORT_ALPHA ? "name" : "jrk"
		));
		$l2parent = array();
		foreach($links->arr() as $link)
		{
			$l2parent[$link->parent()][] = $link;
		}

		$fld = "";
		foreach($flds as $fld_id)
		{
			if (!is_array($l2parent[$fld_id]) || !count($l2parent[$fld_id]))
			{
				continue;
			}
			$fld_o = obj($fld_id);
			$lk = "";
			foreach(safe_array($l2parent[$fld_id]) as $link)
			{
				$this->vars(array(
					"link" => $link->prop("url"),
					"target" => ($link->prop("newwindow") == 1 ? "target=\"_blank\"" : ""),
					"text" => parse_obj_name($link->name()),
					"comment" => $link->prop("comment")
				));

				$lk .= $this->parse("LINK");
			}

			$this->vars(array(
				"LINK" => $lk,
				"name" => $fld_o->name()
			));

			$fld .= $this->parse("FOLDER");
		}

		$this->vars(array(
			"FOLDER" => $fld
		));
	}

	function _disp_kw($o)
	{
		// get all folders
		$flds = $this->get_folders($o);
		if (!count($flds))
		{
			return;
		}

		// get keywords from folders
		$kw_list = new object_list(array(
			"class_id" => CL_KEYWORD,
			"parent" => $flds,
			"sort_by" => $o->prop("kw_ordering") == LINKCOLL_SORT_ALPHA ? "name" : "jrk"
		));

		// to get links find connections from links to kws
		$c = new connection();
		$conns = $c->find(array(
			"from.class_id" => CL_EXTLINK,
			"to" => $kw_list->ids()
		));
		$l2kw = array();
		foreach($conns as $con)
		{
			$link = obj($con["from"]);
			$l2kw[$con["to"]][] = $link;
		}
		$fld = "";
		foreach($kw_list->arr() as $kw)
		{
			if (!is_array($l2kw[$kw->id()]) || !count($l2kw[$kw->id()]))
			{
				continue;
			}
			$lk = "";
			foreach(safe_array($l2kw[$kw->id()]) as $link)
			{
				$this->vars(array(
					"link" => $link->prop("url"),
					"target" => ($link->prop("newwindow") == 1 ? "target=\"_blank\"" : ""),
					"text" => parse_obj_name($link->name()),
					"comment" => $link->prop("comment")
				));

				$lk .= $this->parse("LINK");
			}

			$this->vars(array(
				"LINK" => $lk,
				"name" => $kw->name()
			));

			$fld .= $this->parse("FOLDER");
		}

		$this->vars(array(
			"FOLDER" => $fld
		));
	}

	function get_folders($o)
	{
		$ret = array();
		$sf = $o->meta("subf");
		foreach($o->connections_from(array("type" => "RELTYPE_FOLDER")) as $c)
		{
			$ret[$c->prop("to")] = $c->prop("to");
			if ($sf[$c->prop("to")] == 1)
			{
				$ot = new object_tree(array(
					"parent" => $c->prop("to")
				));
				foreach($ot->ids() as $id)
				{
					$ret[$id] = $id;
				}
			}
		}

		$ol = new object_list(array(
			"oid" => array_keys($ret),
			"lang_id" => array(),
			"site_id" => array()
		));
		return $this->make_keys($ol->ids());
	}

	function _init_folders_t(&$t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Kaust"),
			"align" => "left",
			"sortable" => 1
		));

		$t->define_field(array(
			"name" => "subf",
			"caption" => t("K.A. alamobjektid"),
			"align" => "center"
		));
	}

	function _folders($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_folders_t($t);

		$subf = safe_array($arr["obj_inst"]->meta("subf"));

		foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_FOLDER")) as $c)
		{
			$o = $c->to();
			$t->define_data(array(
				"name" => html::get_change_url($o->id(), array("return_url" => get_ru()), parse_obj_name($o->path_str())),
				"subf" => html::checkbox(array(
					"name" => "subf[".$o->id()."]",
					"value" => 1,
					"checked" => $subf[$o->id()] == 1
				))
			));
		}

		$t->set_default_sortby("name");
		$t->sort_by();
	}

	function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_link_collection (aw_oid int primary key, aw_type int, aw_ordering int)");
			return true;
		}

		switch($f)
		{
			case "aw_kw_ordering":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				return true;
		}
	}
}
?>
