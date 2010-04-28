<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/calendar/event_time.aw,v 1.10 2008/08/05 10:32:52 markop Exp $
// event_time.aw - Toimumisaeg 
/*

HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_ALIAS_ADD_FROM, CL_CALENDAR_EVENT, on_connect_event_to_time)

@tableinfo aw_event_time index=aw_oid master_table=objects master_index=brother_of 
@classinfo syslog_type=ST_EVENT_TIME relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo

@default group=general
@default table=aw_event_time

default table=objects
default group=general

#GENERAL
@property start type=datetime_select field=start
@caption Algab

@property end type=datetime_select field=end
@caption L&otilde;peb

@property location type=relpicker reltype=RELTYPE_LOCATION field=location
@caption Toimumiskoht

@property event type=relpicker reltype=RELTYPE_EVENT field=event
@caption S&uuml;ndmus


#RELTYPES
@reltype LOCATION value=1 clid=CL_SCM_LOCATION
@caption Toimumiskoht

@reltype EVENT value=1 clid=CL_CALENDAR_EVENT
@caption Toimumiskoht

*/

class event_time extends class_base
{
	const AW_CLID = 1322;

	function event_time()
	{
		$this->init(array(
			"tpldir" => "applications/calendar/event_time",
			"clid" => CL_EVENT_TIME
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "location":
				$u = parse_url($_SESSION["aw_session_track"]["server"]["referer"]);
				parse_str($u["query"],$output);
				if(is_oid($_GET["id"]))
				{
					$ao = obj($_GET["id"]);
				}
				elseif(is_oid($output["id"]))
				{
					$ao = obj($output["id"]);
				}
				if(is_object($ao) && $ao->class_id() == CL_CALENDAR_EVENT)
				{
					$prop["options"] = array("" => "") + $ao->get_locations();
				}
				elseif(is_oid($arr["obj_inst"]->id()))
				{
					$prop["options"] = array("" => "") + $arr["obj_inst"]->get_locations();
				}
				$link = $this->mk_my_orb("locations_search" , array("field" => $arr["name_prefix"]),CL_EVENT_TIME);
				$prop["post_append_text"] = html::href(array(
					"onclick" => "aw_popup_scroll(\"".$link."\",\"\",700,500);return false;",
					"caption" => "<img src='".aw_ini_get("baseurl")."/automatweb/images/icons/search.gif' border=0>",
					"url" => "javascript:;",
					"title" => t("Otsi toimumiskohti")
				));
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
			//-- set_property --//
		}
		return $retval;
	}	

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	////////////////////////////////////
	// the next functions are optional - delete them if not needed
	////////////////////////////////////

	/** this will get called whenever this object needs to get shown in the website, via alias in document **/
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
		if ($f == "" && $t == "aw_event_time")
		{
			$this->db_query("CREATE TABLE aw_event_time(
				aw_oid int primary key,
				start int,
				end int,
				location int
			)");
			return true;
		}
		else
		{
			switch($f)
			{
				case "start":
				case "end":
				case "location":
				case "event":
					$this->db_add_col($t, array(
						"name" => $f,
						"type" => "int"
					));
					break;
			}
			return true;
		}
		return false;
	}

	/** searches locations 
		@attrib name=locations_search
		@param loc optional
			Location oids
		@param  location_name optional type=string
			Location name
		@param  field optional
			Number of times
	**/
	function locations_search($arr)
	{
		$content = "";
		if((is_array($arr["loc"]) && sizeof($arr["loc"])))
		{
			$js ="";
			foreach($arr["loc"] as $locid)
			{
				if($this->can("view" , $locid))
				{
					$loco = obj($locid);
				}
		//see kontrollib kas on olemas selline valik, ja kui pole, siis lisab selle ja paneb v22rtuseks
				$js.= $this->set_value_and_add_option_js(array(
					"field_id" => $arr["field"]."[location]",
					"val" => $loco->id(),
					"text" => $loco->name(),
				));
			}
			die("<script language='javascript'>
				$js
				window.close();
			</script>");
		}


		$htmlc = get_instance("cfg/htmlclient");
		$htmlc->start_output();

		$htmlc->add_property(array(
			"name" => "location_name",
			"type" => "textbox",
			"value" => $arr["location_name"],
			"caption" => t("Toimumiskoht"),
		));
		$htmlc->add_property(array(
			"name" => "submit",
			"type" => "submit",
			"value" => t("Otsi"),
			"caption" => t("Otsi")
		));

		classload("vcl/table");
		$t = new vcl_table(array(
			"layout" => "generic",
		));

		$t->define_chooser(array(
			"name" => "loc",
			"field" => "oid",
		));
		$t->define_field(array(
			"name" => "location_name",
			"caption" => t("Toimumiskoht"),
		));
		
		$t->define_field(array(
			"name" => "choose",
			"caption" => "",
		));

		$filter = array(
			"class_id" => CL_SCM_LOCATION,
			"lang_id" => array(),
			"limit" => 100,
		);

		if($arr["location_name"])
		{
			$filter["name"] = "%".$arr["location_name"]."%";
		}

		$ol = new object_list($filter);

		foreach($ol->arr() as $o)
		{
			$t->define_data(array(
				"oid" => $o->id(),
				"location_name" => $o->name(),
				"choose" => html::href(array(
					"caption" => t("Vali see"),
					"url" => "#",
					"onclick" => $this->set_value_and_add_option_js(array(
						"field_id" => $arr["field"]."[location]",
						"val" => $o->id(),
						"text" => $o->name(),
					))."window.close();",
				)),
			));
		}

		$htmlc->add_property(array(
			"name" => "table",
			"type" => "text",
			"value" => $t->draw(),
			"no_caption" => 1,
		));

		$htmlc->add_property(array(
			"name" => "submit2",
			"type" => "submit",
			"value" => t("Vali"),
			"caption" => t("Vali")
		));

		$data = array(
			"row" => $arr["row"],
			"orb_class" => $_GET["class"]?$_GET["class"]:$_POST["class"],
			"reforb" => 0,
			"field" => $arr["field"],
		);

		$htmlc->finish_output(array(
			"action" => "locations_search",
			"method" => "POST",
			"data" => $data
		));

		$content.= $htmlc->get_result();
		return $content;
	}

	//selle peaks t6stma mujale ma arvan, v6ib vaja minna
	/**
		@attrib api=1 params=name
		@param val required
			option value
		@param text optional
			option caption
		@param field_id required
			field id
	**/
	function set_value_and_add_option_js($arr)
	{
		$val = $arr["val"];
		$text = $arr["text"];
		$ret = "el=window.opener.document.getElementById(\"".$arr["field_id"]."\");
			sz= el.options.length;
			var n=0;
			var add=1;
			while(n<el.options.length)
			{
				if(el.options[n].value == \"".$val."\")
				{
					add=0;
					break;
				}
				n++;
			}
				if (add)
			{
			var elOptNew = document.createElement(\"option\");
			elOptNew.text = \"".$text."\";
			elOptNew.value = \"".$val."\";
			var elOptOld = el.options[sz+1];
			try {
			el.add(elOptNew, elOptOld); 
			}
			catch(ex) {
			el.add(elOptNew, elSel.selectedIndex);
			}
			}
			el.value=\"".$val."\";";
		return $ret;
	}

	function on_connect_event_to_time($arr)
	{
		$conn = $arr["connection"];
		$target_obj = $conn->to();
		if ($target_obj->class_id() == CL_EVENT_TIME)
		{
			$target_obj->event = $conn->prop("from");
			$target_obj->save();
		}
	}
}
?>
