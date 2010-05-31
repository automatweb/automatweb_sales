<?php

namespace automatweb;
// crm_people_search.aw - Isikute otsing
/*

@classinfo syslog_type=ST_CRM_PEOPLE_SEARCH relationmgr=yes no_comment=1 no_status=1 prop_cb=1

@default table=objects
@default group=general
@default field=meta
@default method=serialize

@property webview type=relpicker reltype=RELTYPE_WEBVIEW store=connect
@caption Isikud veebis

property people_search_template type=select 
caption Isikute otsinguvormi template

@groupinfo sel_props caption="Vali elemendid"

@property search_properties type=table store=no group=sel_props
@caption Otsingus kasutatavad v&auml;ljad

@reltype WEBVIEW value=1 clid=CL_PERSONS_WEBVIEW
@caption Isikud veebis

*/

class crm_people_search extends class_base
{
	const AW_CLID = 1390;

	function crm_people_search()
	{
		$this->init(array(
			"tpldir" => "applications/crm/crm_people_search",
			"clid" => CL_CRM_PEOPLE_SEARCH
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
			case "search_properties":
				$this->_do_search_props($arr);
				break;
			case "people_search_template":
				$tm = get_instance("templatemgr");
				$prop["options"] = $tm->template_picker(array(
					"folder" => "applications/crm/crm_people_search"
				));
				if(!(sizeof($prop["options"]) > 1))
				{
					$prop["type"] = "text";
					$prop["value"] = t("pole &uuml;htegi templeiti kaustas : ")."templates/applications/crm/crm_people_search";
				}
				break;
		}

		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
			case "search_properties":
				$arr["obj_inst"]->set_search_props($arr["request"]["search_props"]);	
				break;
		}
		return $retval;
	}

	function callback_mod_reforb(&$arr)
	{
		$arr["post_ru"] = post_ru();
	}

	/** parse alias 
		@attrib name=parse_alias is_public="1"
	**/
	function parse_alias($arr)
	{
		//esimesel juhul on juba otsing toimunud, et siis võtaks selle va personali veebid objektist edasi
		$this->search_obj = obj($arr["alias"]["to"]); // dokumendis aliasena
//		if(!$this->search_obj->prop("people_search_template"))
//		{
//			return t("Otsingu template valimata!");
//		}
		//$this->read_template($this->search_obj->prop("people_search_template"));

		if(is_array($_POST) && sizeof($_POST))
		{
			$pwv = get_instance("crm/persons_webview");
			return $pwv->parse_alias(array(
				"search_results" => $this->get_workers_search_results(),
				"alias" => array(
					"to" => $this->search_obj->prop("webview")
				)
			));
		}

		$person_props = $this->_get_person_props();

		$data = array();
		$props = $this->search_obj->get_visible_props();
		$pi = get_instance(CL_CRM_PERSON);

		$htmlc = get_instance("cfg/htmlclient");
		$htmlc->start_output();

		$good_prop_types = array("select" , "chooser" , "classificator");

		foreach($props as $prop)
		{arr($person_props[$prop]);


			if(in_array($person_props[$prop]["type"] , $good_prop_types))
			{
				$pi->get_property(array("prop" => &$person_props[$prop]));
			}
			if($person_props[$prop]["type"] == "text")
			{
				$person_props[$prop]["type"] = "textbox";
			}
			if($person_props[$prop]["type"] == "relpicker")
			{
				$person_props[$prop]["type"] = "textbox";
			}
			$htmlc->add_property($person_props[$prop]);

//			if($this->is_template(strtoupper($prop)))
//			{
//				$this->vars(array($prop => $prop));
//				$data[strtoupper($prop)] = $this->parse(strtoupper($prop));
//			}
		}

		$htmlc->add_property(array(
			"name" => "search_button",
			"type" => "submit",
			"caption" => t("Otsi"),
		));


		$htmlc->finish_output();
		$html = $htmlc->get_result(array(
			"raw_output" => 1
		));

		return "<form method=POST name=people_search action='".get_ru()."'>".$html."</form>";
		return "jogaboo";	
	}

	function get_workers_search_results()
	{
		$filter = array(
			"class_id" => CL_CRM_PERSON,
			"site_id" => array(),
			"lang_id" => array(),
		);
		$props = $this->_get_person_props();
		foreach($_POST as $key => $val)
		{
			if(array_key_exists($key , $props) && $val)
			{
				$property = $props[$key];
				if($property["type"] == "textbox" || $property["type"] == "text")
				{
					$filter[$key] = "%".$val."%";
				}
				if($property["type"] == "relpicker")
				{
					$filter[$key.".name"] = "%".$val."%";
				}
				else
				{
					$filter[$key] = $val;
				}
			}
		}
		$ol = new object_list($filter);
		return $ol->ids();
//		arr($_POST);
	}

	function _get_person_props()
	{
		$clss = aw_ini_get("classes");

		$clid = CL_CRM_PERSON;
		$cln = basename($clss[$clid]["file"]);

		// get properties for clid
		$cfgu = get_instance("cfg/cfgutils");
		$props = $cfgu->load_properties(array(
			"file" => $cln,
			"clid" => $clid
		));


		return $props;
	}

	function _init_jp_table(&$t)
	{
		$t->define_field(array(
			"name" => "jrk",
			"caption" => t("Jrk"),
			"align" => "center",
			"width" => "10"
		));
		$t->define_field(array(
			"name" => "prop",
			"caption" => t("Omadus")
		));
		$t->define_field(array(
			"name" => "visible",
			"caption" => t("N&auml;htav"),
			"align" => "center",
			"sortable" => 1,
		));
//		$t->define_field(array(
//			"name" => "required",
//			"caption" => t("N&otilde;utav"),
//			"align" => "center"
//		));
		$t->set_sortable(false);
	}

	function _do_search_props($arr)
	{
		$prop =& $arr["prop"];

		$this->_init_jp_table($prop["vcl_inst"]);

		$search_props = $arr["obj_inst"]->get_search_props();

		$clss = aw_ini_get("classes");

		$clid = CL_CRM_PERSON;
		$cln = basename($clss[$clid]["file"]);

		// get properties for clid
		$cfgu = get_instance("cfg/cfgutils");
		$props = $cfgu->load_properties(array(
			"file" => $cln,
			"clid" => $clid
		));

		$prop["vcl_inst"]->set_caption(t("Omadused"));

		foreach($props as $nprop)
		{
			$prop["vcl_inst"]->define_data(array(
				"prop" => str_repeat("&nbsp;", 10).$nprop["caption"]." (".$nprop["name"].")",
				"visible" => html::checkbox(array(
					"name" => "search_props[".$nprop["name"]."][visible]",
					"value" => 1,
					"checked" => ($search_props[$nprop["name"]]["visible"] == 1)
				)),
				"jrk" => html::textbox(array(
					"name" => "search_props[".$nprop["name"]."][jrk]",
					"value" => $search_props[$nprop["name"]]["jrk"],
					"size" => 4,
				)),
				"v" => 1 -$search_props[$nprop["name"]]["visible"],
				"j" => $search_props[$nprop["name"]]["jrk"],
			));

		}//arr($prop["vcl_inst"]);
		$prop["vcl_inst"]->set_numeric_field("j");
		$prop["vcl_inst"]->set_sortable("true");
		$prop["vcl_inst"]->set_default_sortby(array("v" , "j"));
		$prop["vcl_inst"]->set_default_sorder("asc");
		$prop["vcl_inst"]->sort_by();
		//$prop["vcl_inst"]->sort_by();
		//$prop["vcl_inst"]->set_sortable(false);
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
}

?>
