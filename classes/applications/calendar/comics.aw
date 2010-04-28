<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/calendar/comics.aw,v 1.5 2007/12/06 14:32:55 kristo Exp $
// comics.aw - Koomiks
/*

@classinfo syslog_type=ST_COMICS relationmgr=yes no_comment=1 r2=yes maintainer=kristo

@default table=objects
@default group=general

@tableinfo planner index=id master_table=objects master_index=brother_of
@default table=planner

@property start1 type=datetime_select field=start
@caption Avaldatakse

@property image type=releditor reltype=RELTYPE_PICTURE rel_id=first use_form=emb
@caption Pilt

@property num type=textbox size=10 table=objects field=jrk datatype=int
@caption Koomiksi number

@property content type=textarea cols=60 rows=20 field=description
@caption Sisu

@property relman type=aliasnmgr no_caption=1 store=no
@caption Seostehaldur

@groupinfo calendars caption=Kalendrid

@property calendar_selector type=calendar_selector store=no group=calendars
@caption Kalendrid

@groupinfo scripts caption="Skriptid"

@property scripts type=releditor reltype=RELTYPE_SCRIPT props=name,comment,content mode=manager field=meta method=serialize table=objects table_fields=name,comment,content group=scripts no_caption=1

@groupinfo comments caption="Kommentaarid"

	@property com_edit type=releditor reltype=RELTYPE_COMMENT mode=manager props=name,commtext table_fields=name,commtext no_caption=1 group=comments store=no

@reltype PICTURE value=1 clid=CL_IMAGE
@caption Pilt

@reltype SCRIPT value=2 clid=CL_COMICS_SCRIPT
@caption Skript

@reltype COMMENT value=3 clid=CL_COMMENT
@caption Kommentaar

*/

class comics extends class_base
{
	const AW_CLID = 913;

	function comics()
	{
		// change this to the folder under the templates folder, where this classes templates will be,
		// if they exist at all. Or delete it, if this class does not use templates
		$this->init(array(
			"tpldir" => "applications/calendar",
			"clid" => CL_COMICS
		));
	}

	//////
	// class_base classes usually need those, uncomment them if you want to use them
	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			//-- get_property --//
		};
		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			//-- set_property --//

		}
		return $retval;
	}

	function request_execute($obj)
	{
		$obj_i = $obj->instance();
		//$t = new cfgform();
		//$props = $t->get_props_from_cfgform(array("id" => $cform));
		$this->read_template("comics_show.tpl");
		$props = $obj_i->load_defaults();
		$vars = array();
		foreach($props as $propname => $propdata)
		{
			$value = $obj->prop($propname);
			if ($propdata["type"] == "datetime_select")
			{
				if ($value == -1)
				{
					continue;
				};
				$value = date("d-m-Y", $value);
			}
			if($propdata["type"] == "releditor")
			{
				if($ob = $obj->get_first_conn_by_reltype($propdata["reltype"]))
				{
					$img = get_instance(CL_IMAGE);
					$imgdata = $img->get_image_by_id($ob->prop("to"));
					$value = $imgdata["url"];
				}
			}
			$vars[$propname] = $value;
		};
		$vars["comment_url"] = aw_url_change_var("fanboys", 1);
		$so = obj(aw_global_get("section"));
		$vars["sm_n"] = $so->name();
		$this->vars($vars);

		if ($_GET["fanboys"] == 1)
		{
			$comment = "";
			foreach($obj->connections_from(array("type" => "RELTYPE_COMMENT")) as $c)
			{
				$c = $c->to();
				$this->vars(array(
					"c_title" => $c->name(),
					"c_commtext" => nl2br($c->prop("commtext"))
				));
				$comment .= $this->parse("COMMENT");
			}
			$this->vars(array(
				"COMMENT" => $comment,
				"reforb" => $this->mk_reforb("submit_comment", array("com" => $obj->id(), "ru" => post_ru()))
			));
			$this->vars(array(
				"HAS_COMMENTS" => $this->parse("HAS_COMMENTS")
			));
		}
		return $this->parse();
	}

	/**
		@attrib name=submit_comment nologin=1
	**/
	function submit_comment($arr)
	{
		$o = obj($arr["com"]);
		aw_disable_acl();
		$c = obj();
		$c->set_parent($o->id());
		$c->set_class_id(CL_COMMENT);
		$c->set_name($arr["title"]);
		$c->set_prop("commtext", $arr["ct"]);
		$c->save();

		$o->connect(array(
			"type" => "RELTYPE_COMMENT",
			"to" => $c->id()
		));
		return $arr["ru"];
	}
}
?>
