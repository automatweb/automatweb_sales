<?php

namespace automatweb;
// html_popup.aw - a class to deal with javascript popups
// $Header: /home/cvs/automatweb_dev/classes/contentmgmt/html_popup.aw,v 1.12 2008/10/07 10:21:25 markop Exp $

/*
	@classinfo relationmgr=yes syslog_type=ST_HTML_POPUP maintainer=kristo
	
	@default table=objects
	@default field=meta
	@default method=serialize
	@default group=general

	@property show_obj type=relpicker reltype=RELTYPE_OBJ
	@caption Sisu 

	@property popup_type type=checkbox ch_value=1 
	@caption Kasuta layerit
	
	@property width type=textbox size=4 maxlength=4
	@caption Laius

	@property height type=textbox size=4 maxlength=4
	@caption K&otilde;rgus
	
	@property scrollbars type=checkbox ch_value=1 default=0
	@caption Kerimisribad

	@property only_once type=checkbox ch_value=1
	@caption Ainult &uuml;he korra sessiooni jooksul
	
	@property menus type=text callback=callback_get_menus method=serialize
	@caption Men&uuml;&uuml;d

	@reltype OBJ value=2 clid=CL_DOCUMENT,CL_FILE,CL_IMAGE
	@caption sisu objekt

	@reltype FOLDER value=1 clid=CL_MENU
	@caption kataloog


*/

class html_popup extends class_base
{
	const AW_CLID = 80;

	function html_popup($args = array())
	{
		$this->init(array(
			"tpldir" => "contentmgmt/html_popup",
			"clid" => CL_HTML_POPUP,
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		$o = $arr["obj_inst"];
		if (1 == $o->prop("popup_type") && ($prop["name"] == "width" || $prop["name"] == "height"))
		{
			$retval = PROP_IGNORE;
		};
		switch($prop["name"])
		{
		};
		return $retval;
	}

	function callback_get_menus($args = array())
	{
		$prop = $args["prop"];
		$nodes = array();
		$section_include_submenus = $args["obj_inst"]->meta("section_include_submenus");
		// now I have to go through the process of setting up a generic table once again
		load_vcl("table");
		$this->t = new aw_table(array(
			"prefix" => "pup_menus",
			"layout" => "generic"
		));
		$this->t->define_field(array(
			"name" => "oid",
			"caption" => t("ID"),
			"talign" => "center",
			"align" => "center",
			"nowrap" => "1",
			"width" => "30",
		));
		$this->t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"talign" => "center",
		));
		$this->t->define_field(array(
			"name" => "check",
			"caption" => t("k.a. alammen&uuml;&uuml;d"),
			"talign" => "center",
			"width" => 80,
			"align" => "center",
		));

		if (is_oid($args["obj_inst"]->id()))
		{
			$obj = $args["obj_inst"];
			$conns = $obj->connections_from(array(
				"type" => "RELTYPE_FOLDER"
			));
			foreach($conns as $c)
			{
				$c_o = $c->to();
				$cid = $c_o->id();

				$this->t->define_data(array(
					"oid" => $cid,
					"name" => $c_o->path_str(array(
						"max_len" => 3
					)),
					"check" => html::checkbox(array(
						"name" => "include_submenus[".$cid."]",
						"value" => $cid,
						"checked" => $section_include_submenus[$cid],
					)),
				));
			}
		}
 
		$nodes[$prop["name"]] = array(
			"type" => "text",
			"caption" => $prop["caption"],
			"value" => $this->t->draw(),
		);
		return $nodes;
	}

	function callback_on_submit_relation_list($args = array())
	{
		// this is where we put data back into object metainfo, for backwards compatibility
		$obj =& obj($args["id"]);

		$oldaliases = $obj->connections_from(array(
			"type" => "RELTYPE_FOLDER"
		));
	
		$section = array();

		foreach($oldaliases as $alias)
		{
			if ($alias->prop("reltype") == RELTYPE_FOLDER)
			{
				$section[$alias->prop("target")] = $alias->prop("target");
			};
		};

		$obj->set_meta("menus",$section);
		$obj->save();
	}

	function callback_on_addalias($args = array())
	{
		$obj =&obj($args["id"]);
		$data = $obj->meta("menus");

		$obj_list = explode(",",$args["alias"]);
		foreach($obj_list as $val)
		{
			$data[$val] = $val;
		};

		$obj->set_meta("menus",$data);
		$obj->save();
	}

	function set_property($args = array())
	{
		$data = &$args["prop"];
		$retval = PROP_OK;
		if ($data["name"] == "menus")
		{
			$args["obj_inst"]->set_meta("section_include_submenus",$args["request"]["include_submenus"]);
		};
		return $retval;
	}

	function get_popup_data($o)
	{
		$rv = "";
		if (1 == $o->prop("popup_type"))
		{
			$show_obj = new object($o->prop("show_obj"));
			if (CL_DOCUMENT == $show_obj->class_id())
			{
				$t = get_instance(CL_DOCUMENT);
				$content = $t->gen_preview(array(
					"docid" => $show_obj->id(),
					"tpl_auto" => 1,
					"no_strip_lead" => 1,
				));
			
				$this->read_template("dhtml_popup.tpl");
				$this->vars(array(
					"content" => $content,
				));
				$rv .= $this->parse();
			};
		}
		else
		{
			$url = $this->mk_my_orb("show", array("id" => $o->prop("show_obj"), "print" => 1), "objects");

			if (!(is_oid($o->meta("show_obj")) && $this->can("view", $o->meta("show_obj"))))
			{
				return "";
			}
			$tmp = obj($o->meta("show_obj"));
			if ($tmp->class_id() == CL_DOCUMENT)
			{
				$url = aw_ini_get("baseurl")."/".$o->meta("show_obj");
			}
			else
			{
				$url = $this->mk_my_orb("show", array("id" => $o->meta("show_obj"), "print" => 1), "objects");
			}

		$url = $this->mk_my_orb("show", array("id" => $o->id(), "print" => 1));


			$rv .= sprintf("<script type='text/javascript'>window.open('%s','htpopup','margin=0,padding=0,top=0,left=0,toolbar=0,location=0,menubar=0,scrollbars=%s,width=%s,height=%s');</script>", $url, (int)$o->prop("scrollbars"), (int)$o->prop("width"), (int)$o->prop("height"));

		};
		return $rv;
	}

	/**
		@attrib name=show params=name nologin="1" 
		@param id required type=oid
	**/
	function show($arr)
	{
		$id = $arr["id"];
		$o = obj($id);
		$show_obj = new object($o->prop("show_obj"));
		if (CL_DOCUMENT == $show_obj->class_id())
		{
			$t = get_instance(CL_DOCUMENT);
			$content = $t->gen_preview(array(
				"docid" => $show_obj->id(),
				"tpl_auto" => 1,
				"no_strip_lead" => 1,
			));
		
		}
		if (CL_IMAGE == $show_obj->class_id())
		{
			$instance = get_instance(CL_IMAGE);
			$content = $instance->make_img_tag_wl($show_obj->id());
		}

		$this->read_template("dhtml_popup.tpl");
		$this->vars(array(
			"content" => $content,
		));
		return $this->parse();

	}
}
?>
