<?php

/*

this message will be sent when the contents of the popup search listbox change
so that clients can perform actions based on the change
EMIT_MESSAGE(MSG_POPUP_SEARCH_CHANGE)

*/
class popup_search extends aw_template implements vcl_interface, orb_public_interface
{
	const PS_WIDTH = 800;
	const PS_HEIGHT = 500;

	const REQVAR_FILTER_CLASS_ID = "c";
	const REQVAR_JAVASCRIPT_CALLBACK = "jcb";
	const REQVAR_FILTER_CALLBACK = "fcb";
	const REQVAR_RELOAD_LAYOUT = "rl";
	const REQVAR_RELOAD_PROPERTY = "rp";
	const REQVAR_RELOAD_WINDOW = "rw";

	protected static $valid_filter_callbacks = array(
		0 => array("popup_search", "get_filter"),
		1 => array("shop_product", "get_categories_popup_filter")
	);

	protected $req;
	protected $clid = array();
	protected $filter_callback = 0;

	protected $action;
	protected $property;
	protected $reload_property;
	protected $reload_layouts;
	protected $reload_window = false;

	protected $search_results_limit = 50;

	public function __construct()
	{
		load_javascript("reload_properties_layouts.js");
		$this->init("popup_search");
	}

	/** Sets orb request to be processed by this object
		@attrib api=1 params=pos
		@param request type=aw_request
		@returns void
	**/
	public function set_request(aw_request $request)
	{
		$this->req = $request;
	}

	public function init_vcl_property($arr)
	{
		if (isset($arr["property"]["style"]) && $arr["property"]["style"] === "relpicker")
		{
			$i = new relpicker();
			return $i->init_vcl_property($arr);
		}

		if (isset($arr["request"]["action"]) && $arr["request"]["action"] === "view")
		{
			$p = $arr["prop"];
			$p["type"] = "text";
			$p["value"] = html::obj_change_url($p["value"]);
			return array($p["name"] => $p);
		}

		$style = isset($arr['property']['style']) ? $arr['property']['style'] : 'default'; // Options: default, relpicker
		$reltype = "";

		$options = array();

		if ($style === 'default')
		{
			$name = "popup_search[".$arr["property"]["name"]."]";
			if (is_object($arr["obj_inst"]))
			{
				if (is_array($arr["obj_inst"]->meta($name)))
				{
					$options +=  $arr["obj_inst"]->meta($name);
				}
			}

			if (count($options) > 0)
			{
				$ol = new object_list(array(
					"oid" => $options
				));
				$options = $ol->names();
			}
		}
		else if ($style === 'relpicker' || $arr["property"]["type"] !== "popup_search")
		{
			if (is_object($arr["obj_inst"]) && isset($arr['property']['reltype']) && isset($arr['relinfo'][$arr['property']['reltype']])  && is_oid($arr['obj_inst']->id()))
			{
				$reltype = $arr['property']['reltype'];
				$conn = $arr['obj_inst']->connections_from(array(
						"type" => $reltype
				));
				foreach($conn as $c)
				{
					$options[$c->prop("to")] = $c->prop("to.name");
				}
			}
		}

		$tmp = $arr["property"];

		$tmp["type"] = "text";
		if (!$tmp["clid"] && $tmp["reltype"])
		{
			$clid = new aw_array($arr["relinfo"][$reltype]["clid"]);
			$tmp["clid"] = array();
			foreach($clid->get() as $clid)
			{
				$tmp["clid"][] = aw_ini_get("classes.{$clid}.def");
			}
		}

		$clid = array();
		$awa = new aw_array($tmp["clid"]);
		foreach($awa->get() as $clid_str)
		{
			$clid[] = constant($clid_str);
		}

		if (is_object($arr["obj_inst"]))
		{
			$url = $this->mk_my_orb("do_search", array(
				"id" => $arr["obj_inst"]->id(),
				"pn" => $tmp["name"],
				"clid" => $clid,
				"start_empty" => true,
				"multiple" => !empty($arr["property"]["multiple"]) ? 1 : null
			));
		}

		if (isset($tmp["options"]) and is_array($tmp["options"]) and count($tmp["options"]))
		{
			$options = $tmp["options"];
		}

		$sel = isset($arr["property"]["value"]) ? $arr["property"]["value"] : null;
		if (!empty($arr["property"]["multiple"]))
		{
			if (!(is_array($sel) && count($sel)) && is_object($arr["obj_inst"]))
			{
				$sel =  $arr["obj_inst"]->prop($arr["property"]["name"]);
			}
		}
		else
		if (!$this->can("view", $sel) && is_object($arr["obj_inst"]))
		{
			$sel =  $arr["obj_inst"]->prop($arr["property"]["name"]);
		}

		if (isset($arr["property"]["style"]) and $arr["property"]["style"] === "autocomplete")
		{
			$selstr = "";
			if ($this->can("view", $sel))
			{
				$selstr = obj($sel);
				$selstr = $selstr->name();
			}

			if($arr["property"]["autocomplete_source"])
			{
				$as = $arr["property"]["autocomplete_source"];
			}
			else
			{
				$as = $this->mk_my_orb("autocomplete_source", array("pn" => $arr["property"]["name"], "clid" => $clid));
			}

			if(!$arr["property"]["autocomplete_params"])
			{
				$arr["property"]["autocomplete_params"] = array($arr["property"]["name"]);
			}

			$as = parse_url ($as);
			$as = $as["path"] . "?" . $as["query"];
			$tmp["value"] = html::textbox(array(
				"name" => $arr["property"]["name"],
				"content" => $selstr,
				"value" => $sel,
				"autocomplete_source" => $as,
				"autocomplete_params" => ($arr["property"]["autocomplete_params"]) ? $arr["property"]["autocomplete_params"] : null,
				"option_is_tuple" => true
			));
		}
		else
		{
			$tmp["value"] = html::select(array(
				"name" => $arr["property"]["name"],
				"options" => array("" => t("--Vali--")) + $options,
				"selected" => $sel,
				"multiple" => !empty($arr["property"]["multiple"])
			));
		}

		if (is_object($arr["obj_inst"]) && is_oid($arr["obj_inst"]->id()))
		{
			$tmp["value"] .= html::href(array(
				"url" => "javascript:aw_popup_scroll('$url','Otsing',".popup_search::PS_WIDTH.",".popup_search::PS_HEIGHT.")",
				"caption" => html::img(array("url" => icons::get_std_icon_url("magnifier"))),
				"title" => t("Otsi")
			));

			if ($this->can("view", ($_id = $arr["obj_inst"]->prop($arr["property"]["name"]))))
			{
				$tmp["value"] .= " ";
				$tmp["value"] .= html::href(array(
					"url" => html::get_change_url($_id, array("return_url" => get_ru())),
					"caption" => html::img(array("url" => icons::get_std_icon_url("pencil"))),
					"title" => t("Muuda")
				));

			}
			// add new
			$cu = new cfgutils();
			$pl = $cu->load_properties(array("clid" => $arr["obj_inst"]->class_id()));
			if (!empty($pl[$arr["property"]["name"]]["reltype"]) )
			{
				$rt = $pl[$arr["property"]["name"]]["reltype"];
				$clid = new aw_array($arr["relinfo"][$rt]["clid"]);
				$rel_val = $arr["relinfo"][$rt]["value"];
				if ($clid->count() > 1)
				{
					$pm = new popup_menu();
					$pm->begin_menu($arr["property"]["name"]."_relp_pop");
					foreach($clid->get() as $_clid)
					{
						$pm->add_item(array(
							"text" => aw_ini_get("classes.{$_clid}.name"),
							"link" => html::get_new_url(
								$_clid,
								$arr["obj_inst"]->id(),
								array(
									"alias_to" => $arr["obj_inst"]->id(),
									"reltype" => $rel_val,
									"return_url" => get_ru()
								)
							)
						));
					}
					$tmp["value"] .= " ".$pm->get_menu(array(
						"icon" => "new.gif",
						"alt" => t("Lisa")
					));
				}
				else
				{
					foreach($clid->get() as $cl)
					{
						$tmp["value"] .= " ".html::href(array(
							"url" => html::get_new_url(
								$cl,
								$arr["obj_inst"]->id(),
								array(
									"alias_to" => $arr["obj_inst"]->id(),
									"reltype" => $rel_val,
									"return_url" => get_ru()
								)
							),
							"caption" => html::img(array("url" => icons::get_std_icon_url("add"))),
							"title" => sprintf(t("Lisa uus %s"), aw_ini_get("classes.{$cl}.name"))
						));
					}
				}
			}
			// link to unlinkink page if there are any options and if we'ere in relpicker mode i guess
			if (count($options) && isset($arr['property']['style']) && $arr['property']['style'] === 'relpicker' && isset($arr['property']['reltype']))
			{
				$url2 = $this->mk_my_orb("do_unlink", array(
					"id" => $arr["obj_inst"]->id(),
					"pn" => $tmp["name"],
					"clid" => $clid,
				));
				$tmp["value"] .= " ".html::href(array(
					"url" => "javascript:aw_popup_scroll(\"$url2\",\"Eemalda\",".popup_search::PS_WIDTH.",".popup_search::PS_HEIGHT.")",
					"caption" => html::img(array("url" => icons::get_std_icon_url("delete"))),
					"title" => t("Eemalda")
				));
			}
		}

		return array(
			$arr["property"]["name"] => $tmp,
		);
	}

	function process_vcl_property(&$arr)
	{
		if ($arr["obj_inst"]->is_property($arr["prop"]["name"]))
		{
			$arr["obj_inst"]->set_prop($arr["prop"]["name"], $arr["prop"]["value"]);
		}
	}

	/**
		@attrib name=do_unlink

		@param id required type=int acl=view
		@param pn required
		@param rem optional

		@comment
			With style=relpicker enables unlinking of the relations

	**/
	function do_unlink($arr)
	{
		$ob = obj($arr['id']);
		$props = $ob->get_property_list();
		$prop = $props[$arr['pn']];
		if (isset($prop['style']) && $prop['style'] === 'relpicker' && isset($prop['reltype']))
		{
			$return = "";
			if (isset($arr['id']) && is_oid($arr['id']) && $this->can('view', $arr['id']))
			{
				// If POSTed, handle results
				if ($_SERVER['REQUEST_METHOD'] === 'POST')
				{
					$value = $ob->prop($arr['pn']);
					$possible_value = null;
					$reltype = $prop['reltype'];
					foreach($ob->connections_from(array("type" => $reltype)) as $c)
					{
						$to = $c->to();
						if (isset($arr['rem'][$to->id()]))
						{
							// If unlinkable object is also prop value, set to no value
							if ($value == $to->id())
							{
								$value = null; // Actual saving after the loop
							}
							// Unlink
							$c->delete();
						}
						else if (empty($possible_value))
						{
							$possible_value = $to->id();
						}
					}
					if (empty($value))
					{
						$ob->set_prop($arr['pn'], $possible_value);
						$ob->save();
					}

					die("
						<html><body><script language='javascript'>
							window.opener.location.reload();
							window.close();
						</script></body></html>
					");
				}
				else
				{
					$htmlc = new htmlclient(array(
						'template' => "default",
					));
					$htmlc->in_popup_mode(true);
					$htmlc->start_output();
					$htmlc->add_property(array(
						"caption" => t("Vali eemaldatavad objektid"),
					));

					foreach($ob->connections_from(array("type" => $prop['reltype'])) as $c)
					{
						$o = $c->to();
						$htmlc->add_property(array(
							"name" => "rem[".$o->id()."]",
							"type" => "checkbox",
							"caption" => $o->name(),
						));
					}

					$htmlc->add_property(array(
						"name" => "s[submit]",
						"type" => "submit",
						"value" => t("Vali"),
						"class" => "sbtbutton"
					));

					$htmlc->finish_output(array(
						"action" => "do_unlink",
						"method" => "POST",
						"data" => array(
							"id" => $arr["id"],
							"pn" => $arr["pn"],
							"append_html" => htmlspecialchars(ifset($arr,"append_html"), ENT_QUOTES),
							"orb_class" => "popup_search",
							"reforb" => 0
						)
					));

					$html = $htmlc->get_result();

					return $html;
				}
			}
		}
	}

	/**

		@attrib name=do_search

		@param id optional
		@param pn optional
		@param pi optional
			pn or pi is required
		@param multiple optional
		@param clid optional
		@param fcb optional
		@param jcb optional
		@param s optional
		@param append_html optional
		@param no_submit optional
		@param start_empty optional type=bool
			If true, initially search results table is empty.

		@comment
			clid - not filtered by, if clid == 0
			append_html - additional html, inserted to tmpl {VAR:append}
		@returns
			returns the html for search form & results
	**/
	function do_search($arr)
	{
		$form_html = $this->_get_form($arr);
		$res_html = $this->_get_results($arr);
		return $form_html.html::linebreak().$res_html;
	}

	/**
		@attrib params=pos
		@param htmlc required type=object
			cfg/htmlclient object instance reference
		@param arr required type=array
			array of params, same as #do_search 'es params.
		@comment
			inserts additional props to search form.
			This function can be overridden by extending class to use add some specific properties.
			Example is taken from crm_participant_search class
		@examples
			// simple overriding example basing crm_participant_search_class
			// actually this is kinda' half of the example but gives the idea. For full example look into crm_participant_search.

			class crm_participant_search extends popup_search
			{
				function crm_participant_search()
				{
					$this->popup_search();
				}


				function _insert_form_props(&$htmlc, $arr)
				{
					parent::_insert_form_props($htmlc, $arr);

					$htmlc->add_property(array(
						"name" => "s[search_co]",
						"type" => "textbox",
						"value" => $arr["s"]["search_co"],
						"caption" => t("Organisatsioon"),
					));
				}

				function _get_filter_props(&$filter, $arr)
				{
					parent::_get_filter_props($filter, $arr);

					if (!$_GET["MAX_FILE_SIZE"])
					{
						$arr["s"]["show_vals"]["cur_co"] = 1;
					}

					if ($arr["s"]["search_co"] != "")
					{
						$filter["CL_CRM_PERSON.work_contact.name"] = map("%%%s%%", array_filter(explode(",", $arr["s"]["search_co"]), create_function('$a','return $a != "";')));
					}

					if (is_array($filter["oid"]) && !count($filter["oid"]))
					{
						$filter["oid"] = -1;
					}
			}
	**/
	protected function _insert_form_props($htmlc, $arr)
	{
		$htmlc->add_property(array(
			"name" => "s[name]",
			"type" => "textbox",
			"value" => ifset($arr, "s", "name"),
			"caption" => t("Nimi")
		));

		$htmlc->add_property(array(
			"name" => "s[oid]",
			"type" => "textbox",
			"value" => ifset($arr, "s", "oid"),
			"caption" => t("Objekti id")
		));
	}

	function _get_form($arr)
	{
		$htmlc = new htmlclient();
		$htmlc->in_popup_mode(true);
		$htmlc->start_output();

		$this->_insert_form_props($htmlc, $arr);

		if (!empty($arr["tbl_props"]))
		{
			foreach ($arr["tbl_props"] as $key => $value)
			{
				$htmlc->add_property(array(
					"name" => "tbl_props[" . $key . "]",
					"type" => "hidden",
					"value" => $value,
				));
			}
		}

		$htmlc->add_property(array(
			"name" => "s[submit]",
			"type" => "submit",
			"value" => t("Otsi"),
			"caption" => t("Otsi")
		));

		$data = array(
			"id" => isset($arr["id"]) ? $arr["id"] : "",
			"pn" => isset($arr["pn"]) ? $arr["pn"] : "",
			"pi" => isset($arr["pi"]) ? $arr["pi"] : "",
			"npn" => isset($arr["npn"]) ? $arr["npn"] : "",
			"npi" => isset($arr["npi"]) ? $arr["npi"] : "",
			"multiple" => isset($arr["multiple"]) ? $arr["multiple"] : "",
			"clid" => isset($arr["clid"]) ? $arr["clid"] : "",
			self::REQVAR_FILTER_CALLBACK => isset($arr[self::REQVAR_FILTER_CALLBACK]) ? $arr[self::REQVAR_FILTER_CALLBACK] : "",
			self::REQVAR_JAVASCRIPT_CALLBACK => isset($arr[self::REQVAR_JAVASCRIPT_CALLBACK]) ? $arr[self::REQVAR_JAVASCRIPT_CALLBACK] : "",
			"no_submit" => ifset($arr, "no_submit"),
			"append_html" => htmlspecialchars(ifset($arr,"append_html"), ENT_QUOTES),
			"orb_class" => $_GET["class"],
			"in_popup" => 1,
			"reforb" => 0
		);
		$this->_process_reforb_args($data);
		$htmlc->finish_output(array(
			"action" => "do_search",
			"method" => "GET",
			"data" => $data
		));

		$html = $htmlc->get_result();
		return $html;
	}

	protected function _process_reforb_args(&$data)
	{
	}

	/**
		@attrib params=pos
		@param filter required type=array
			filter array instance
		@param arr required type=array
			array of params, same as #do_search 'es params.
		@comment
			manages data coming from the form and makes it readable for filter.
			This function can be overridden from extending class. This is needed when you extend a class to popup_search() and add some
			specific new pops with overriding _insert_form_props() function, then you have to manage the data coming from these new props
			in here.
			Little example is shown on the #_insert_form_props function documentation
		@examples
			#_insert_form_props
	**/
	protected function _get_filter_props(&$filter, $arr)
	{
		if (isset($arr["s"]["name"]) && $arr["s"]["name"] != "")
		{
			$bits = explode(",", $arr["s"]["name"]);
			foreach($bits as $k => $v)
			{
				$bits[$k] = trim($v);
			}
			$filter["name"] = map("%%%s%%", $bits);
		}
		if (isset($arr["s"]["oid"]) && $arr["s"]["oid"] != "")
		{
			$bits = explode(",", $arr["s"]["oid"]);
			foreach($bits as $k => $v)
			{
				$bits[$k] = trim($v);
			}
			$filter["oid"] = map("%s", $bits);
		}
	}

	function _get_results($arr)
	{
		$this->read_template("table.tpl");

		$t = new aw_table(array(
			"layout" => "generic"
		));

		$t->define_field(array(
			"name" => "icon",
			"caption" => t("&nbsp;")
		));

		if (isset($arr["tbl_props"]) && is_array($arr["tbl_props"]))
		{
			$clid = $arr["clid"];
			if (is_array($clid))
			{
				$clid = reset($clid);
			}
			$tmpo = obj();
			$tmpo->set_class_id($clid);
			$proplist = $tmpo->get_property_list();
			foreach($arr["tbl_props"] as $pn)
			{
				switch ($pn)
				{
					case "oid":
						$caption = t("Objekti id");
						break;

					case "parent":
						$caption = t("&Uuml;lemobjekti id");
						break;

					default:
						$caption = $proplist[$pn]["caption"];
						break;
				}

				$t->define_field(array(
					"name" => $pn,
					"caption" => $caption,
					"sortable" => 1
				));
			}
		}
		else
		{
			$t->define_field(array(
				"name" => "oid",
				"caption" => t("OID"),
				"sortable" => 1,
			));

			$t->define_field(array(
				"name" => "name",
				"sortable" => 1,
				"caption" => t("Nimi")
			));

			$t->define_field(array(
				"name" => "parent",
				"sortable" => 1,
				"caption" => t("Asukoht")
			));

			$t->define_field(array(
				"name" => "modifiedby",
				"sortable" => 1,
				"caption" => t("Muutja")
			));

			$t->define_field(array(
				"name" => "modified",
				"caption" => t("Muudetud"),
				"sortable" => 1,
				"format" => "d.m.Y H:i",
				"type" => "time"
			));
		}

		$t->define_field(array(
			"name" => "select_this",
			"caption" => t("Vali"),
		));

		if (!empty($arr["multiple"]))
		{
			$t->define_field(array(
				"name" => "sel",
				"caption" => "<a href='javascript:void(0)' onclick='aw_sel_chb(document.cf,\"sel\")'>".t("Vali")."</a>"
			));
		}

		$t->set_default_sortby("name");

		$filter = array();
		$count = 0;
		if (!empty($arr['clid']))
		{
			$filter['class_id'] = $arr['clid'];
			$count = 1;
		}

		$this->_get_filter_props($filter, $arr);
		$count = !($count === count($filter) and !empty($arr["start_empty"]));
		$filter[] = new obj_predicate_limit(30);

		if (count($filter) > 1)
		{
			// Pre-check checkboxes for relpicker
			$checked = array ();
			if (isset($arr['id']) && acl_base::can("view", $arr['id']))
			{
				$ob = obj($arr['id']);
				$props = $ob->get_property_list();
				$prop = isset($props[$arr['pn']]) ? $props[$arr['pn']] : array();
				if (isset($prop['style']) && $prop['style'] === 'relpicker' && isset($prop['reltype']))
				{
					foreach($ob->connections_from(array("type" => $prop['reltype'])) as $c)
					{
						$checked[$c->prop("to")] = 1;
					}
				}
			}

			// get custom filter if defined
			$self = get_class($this);
			$filter_callback = isset($arr[self::REQVAR_FILTER_CALLBACK]) ? (int) $arr[self::REQVAR_FILTER_CALLBACK] : 0;
			$filter_callback = isset($self::$valid_filter_callbacks[$filter_callback]) ? $self::$valid_filter_callbacks[$filter_callback] : $self::$valid_filter_callbacks[0];
			$filter = array_merge($filter, call_user_func($filter_callback, $arr));

			$ol = new object_list($filter);

			if (isset($arr["pn"])) {
				$elname = $arr["pn"];
				$elname_n = $arr["pn"];
				$elname_l = $arr["pn"];

				if (!empty($arr["multiple"]))
				{
					$elname .= "[]";
					$elname_n .= "][]";
				}
			}

			for($count and $o = $ol->begin(); !$ol->end(); $o = $ol->next())
			{
				$dat = array(
					"oid" => $o->id(),
					"name" => html::obj_change_url($o),
					"parent" => $o->path_str(array("max_len" => 3)),
					"modifiedby" => $o->modifiedby(),
					"modified" => $o->modified(),
					"select_this" => html::href(array(
						"url" => "javascript:void(0)",
						"caption" => t("Vali see"),
						"onclick" => (isset($arr["pi"]) ?
							('window.opener.document.getElementById("' .$arr["pi"] . '").value = "' . $o->id() . '";' . (!empty($arr["npi"]) ? ('window.opener.document.getElementById("' . $arr["npi"] . '").value = "' . $o->name . '";') : "")) :
							("el=aw_get_el(\"{$elname}\",window.opener.document.changeform);")."if (!el) { el=aw_get_el(\"{$elname_n}\", window.opener.document.changeform);} if (!el) { el=aw_get_el(\"{$elname_l}\", window.opener.document.changeform);} if (el.options) {sz= el.options.length;el.options.length=sz+1;el.options[sz].value=".$o->id()."; el.options[sz].selected = 1;} else {el.value = ".$o->id().";} ".(!empty($arr["npn"])?("el=aw_get_el(\"{$arr["npn"]}\",window.opener.document.changeform);el.value=\"".str_replace('"', '\"', $o->name)."\";"):"")
						).(!empty($arr["no_submit"])?"":"window.opener.document.changeform.submit();").(!empty($arr[self::REQVAR_JAVASCRIPT_CALLBACK])?("(function(){" . $arr[self::REQVAR_JAVASCRIPT_CALLBACK] . "})(); "):"")."window.close()"
					)),
					"icon" => html::img(array("url" => icons::get_icon_url($o->class_id())))
				);

				if (!empty($arr["multiple"]))
				{
					$dat["sel"] = html::checkbox(array(
						"name" => "sel[]",
						"value" => $o->id(),
						"checked" => 0 //isset($checked[$o->id()]) ? $checked[$o->id()] : 0,
					));
				}

				if (isset($arr["tbl_props"]) && is_array($arr["tbl_props"]))
				{
					foreach($arr["tbl_props"] as $pn)
					{
						$dat[$pn] = $o->prop_str($pn);
					}
				}
				$t->define_data($dat);
			}
		}

		if (!empty($arr["multiple"]))
		{
			$this->vars(array(
				"select_text" => t("Vali")
			));
			$submit_button = $this->parse("SUBMIT_BUTTON");
		}
		else
		{
			$submit_button = "";
		}

		$t->sort_by();
		$this->vars(array(
			"table" => $t->draw(),
			"SUBMIT_BUTTON" => $submit_button,
			"reforb" => $this->mk_reforb("final_submit", array(
				"id" => isset($arr["id"]) ? $arr["id"] : "",
				"multiple" => isset($arr["multiple"]) ? $arr["multiple"] : "",
				"pn" => isset($arr["pn"]) ? $arr["pn"] : "",
				"pi" => isset($arr["pi"]) ? $arr["pi"] : "",
				"clid" => !empty($arr["clid"]) ? $arr["clid"] : "0",
				"no_submit" => ifset($arr, "no_submit"),
				"append_html" => htmlspecialchars(ifset($arr,"append_html"), ENT_QUOTES),
			), $_GET["class"]),
			"append" => ifset($arr,"append_html"),
		));

		return $this->parse();
	}

	/**

		@attrib name=final_submit all_args="1"

	**/
	function final_submit($arr)
	{
		// available options are in metadata, selected option value of the property
		if ($this->can("view", $arr["id"]))
		{
			$o = obj($arr["id"]);
			$o->set_meta("popup_search[".$arr["pn"]."]", $this->make_keys($arr["sel"]));
			if (is_array($arr["sel"]) && count($arr["sel"]) == 1 && $o->is_property($arr["pn"]))
			{
				$o->set_prop($arr["pn"], $arr["sel"][0]);
			}
			$o->save();

			// if relpicker, define relations
			$props = $o->get_property_list();
			$prop = $props[$arr['pn']];
			if (isset($prop['style']) && $prop['style'] === 'relpicker' && isset($prop['reltype']))
			{
				$reltype = $prop['reltype'];
				foreach($o->connections_from(array("type" => $reltype)) as $c)
				{
					$c->delete();
				}
				if (isset($arr['sel']) && is_array($arr['sel']))
				{
					foreach($arr['sel'] as $i => $id)
					{
						if (is_oid($id) && $this->can("view", $id))
						{
							$object = obj($id);
							$o->connect(array(
								"to" => $object->id(),
								"reltype" => $reltype,
							));
						}
					}
				}
			}

			// emit message so objects can update crap
			$params = array(
				"oid" => $o->id(),
				"prop" => $arr["pn"],
				"options" => $this->make_keys($arr["sel"]),
				"arr" => $arr,
			);
			post_message_with_param(MSG_POPUP_SEARCH_CHANGE, $o->class_id(), $params);
		}

		if ($arr["multiple"] == 1)
		{
			$str = "
				<html><body><script language='javascript'>
function aw_get_el(name,form)
{
    if (!form)
	{
        form = document.changeform;
	}
    for(i = 0; i < form.elements.length; i++)
	{
        el = form.elements[i];
        if (el.name.indexOf(name) != -1)
		{
			return el;
		}
	}
}

					el = aw_get_el('".$arr["pn"]."[]', window.opener.document.changeform);
					if (!el)
					el = aw_get_el('".$arr["pn"]."][]', window.opener.document.changeform);
					if (!el)
					el = aw_get_el('".$arr["pn"]."', window.opener.document.changeform);
					//el.selectedIndex = 0;
					if (el.options)
					{
			";
			foreach(safe_array($arr["sel"]) as $idx => $val)
			{
				$str .= "sz = el.options.length;";
				$str .= "el.options.length=sz+1;";
				$str .= "el.options[sz].value = $val;el.options[sz].selected = 1;";
			}
			$str .= "
					}
					else
					{
			";
			$str .= "el.value = '".join(",", $arr["sel"])."';"; //$val;";
			$str .= "		}";
			if(!$arr["no_submit"])
			{
				$str .= "
					window.opener.document.changeform.submit();";
			}
			$str .=	"
				window.close()
				</script></body></html>
			";
			die($str);
		}
		else
		{
			$str = "
				<html><body><script language='javascript'>
				if(window.opener.document.changeform.".$arr["pn"].")
				{
					if (window.opener.document.changeform.".$arr["pn"].".options)
					{
						window.opener.document.changeform.".$arr["pn"].".selectedIndex=0;
						window.opener.document.changeform.".$arr["pn"].".options[0].value=\"".$arr["sel"][0]."\";
					}
					else
					{
						window.opener.document.changeform.".$arr["pn"].".value = '".join(",", $arr["sel"])."';
					}
				}
					";
			if(!$arr["no_submit"])
			{
				$str .= "
					window.opener.document.changeform.submit();";
			}
			$str .=	"

					window.close()
				</script></body></html>
			";
			die($str);
		}
	}

	/** sets the options for the given objects given popup search property

		@param obj required
		@param prop required
		@param opts required

		@comment
			obj - the object whose options to set
			prop - the property's options in that object to set
			opts - array of object id's that the user can select from that property
	**/
	function set_options($arr)
	{
		$arr["obj"]->set_meta("popup_search[".$arr["prop"]."]", $this->make_keys($arr["opts"]));
		if (count($arr["opts"]) == 1)
		{
			$first = reset($arr["opts"]);
			$arr["obj"]->set_prop($arr["prop"], $first);
		}
		$arr["obj"]->save();
	}

	/** Returns the html that displays search button for the user
		@attrib api=1 params=name

		@param pn required type=string
			The html element name to stick the search results to

		@param multiple optional type=bool
			If the element is a multiple select

		@param clid optional type=array
			The class id to search

		@param confirm optional type=string
			javascript confirmation popup caption

		@comment
			Returns the thml that displays search button for the user
		@returns string
			html code for search button..
	**/
	function get_popup_search_link($arr)
	{
		$c = !empty($arr["confirm"])?" onclick=\"if(!confirm('$arr[confirm]')) {return false;}\"":"";
		unset($arr["confirm"]);
		$url = $this->mk_my_orb("do_search", $arr);
		$s = t("Otsi");
		return "<a class=\"aw04toolbarbutton\" title=\"$s\" alt=\"$s\" href='javascript:aw_popup_scroll(\"$url\",\"$s\",".popup_search::PS_WIDTH.",".popup_search::PS_HEIGHT.")'$c onMouseOver=\"this.className='aw04toolbarbuttonhover'\" onMouseOut=\"this.className='aw04toolbarbutton'\" onMouseDown=\"this.className='aw04toolbarbuttondown'\" onMouseUp=\"this.className='aw04toolbarbuttonhover'\"><img alt=\"$s\" src='".aw_ini_get("baseurl")."automatweb/images/icons/search.gif' border=0></a>";
	}

	/**
		@attrib name=autocomplete_source all_args=1
	**/
	function autocomplete_source($arr)
	{
		header ("Content-Type: text/html; charset=" . aw_global_get("charset"));
		$cl_json = new json();

		$errorstring = "";
		$error = false;
		$autocomplete_options = array();

		$option_data = array(
			"error" => &$error,// recommended
			"errorstring" => &$errorstring,// optional
			"options" => &$autocomplete_options,// required
			"limited" => false,// whether option count limiting applied or not. applicable only for real time autocomplete.
		);

		$ol = new object_list(array(
			"class_id" => $arr["clid"],
			"name" => $arr[$arr["pn"]]."%",
			"limit" => 30
		));
		$autocomplete_options = $ol->names();
		foreach($autocomplete_options as $k => $v)
		{
			$autocomplete_options[$k] = parse_obj_name($v);
		}
		exit ($cl_json->encode($option_data));

	}

	/** Post-processes popup_search and creates the needed connections
		@attrib api=1 params=pos
		@param o required type=object
			The object to connect to

		@param val required type=string
			The value from the popup search class

		@param rt required type=string
			The reltype to use for connection

	**/
	function do_create_rels($o, $val, $rt)
	{
		if ($val != "")
		{
			foreach(explode(",", $val) as $item)
			{
				if (acl_base::can("view", $item))
				{
					$o->connect(array(
						"to" => $item,
						"type" => $rt
					));
				}
			}
		}
	}

	//----------------- Marko teeb siia miskit uut varianti.... katsetab
	public function set_class_id($clid)
	{
		$this->clid = safe_array($clid);
	}

	public function set_filter_callback($id)
	{
		settype($id, "int");
		$self = get_class($this);
		if (!isset($self::$valid_filter_callbacks[$id]))
		{
			throw new awex_param_type("Invalid filter id '{$id}'");
		}

		$this->filter_callback = $id;
	}

	public function set_id($id)
	{
		$this->oid = $id;
	}

	public function set_reload_layout($layouts)
	{
		$this->reload_layouts = $layouts;
	}

	public function set_reload_window($value = true)
	{
		$this->reload_window = (bool) $value;
	}

	public function set_reload_property($prop)
	{
		$this->reload_property = $prop;
	}

	public function set_property($prop)
	{
		$this->property = $prop;
	}

	public function set_action($action)
	{
		$this->action = $action;
	}

	public function get_search_button()
	{
		$ret = 	html::href(array(
			"url" => "javascript:;",
			"onclick" => 'win = window.open("'.$this->get_popup_url().'" ,"awVclPopupSearch","width=720,height=600,statusbar=yes, scrollbars=yes");',
			"caption" => html::img(array("url" => icons::get_std_icon_url("magnifier")))
		));

		return $ret;
	}

	function get_popup_url()
	{
		$args = array(
			"id" => $this->oid,
			"in_popup" => "1",
			"start_empty" => "1",
			"action" => $this->action,
			"property" => $this->property
		);

		if (isset($this->reload_layouts)) $args["reload_layout"] = $this->reload_layouts;
		if (isset($this->reload_property)) $args["reload_property"] = $this->reload_property;
		if (!empty($this->reload_window)) $args["reload_window"] = $this->reload_window;
		if (!empty($this->clid)) $args["clid"] = $this->clid;
		if (!empty($this->filter_callback)) $args[self::REQVAR_FILTER_CALLBACK] = $this->filter_callback;

		$url = core::mk_my_orb("do_ajax_search", $args, get_class($this));
		return $url;
	}

	/**
		@attrib name=do_ajax_search
		@param id optional
		@param multiple optional
		@param clid optional type=array
		@param fcb optional
		@param property optional
		@param reload_property optional type=string
		@param reload_layout optional type=string
		@param reload_window type=bool default=FALSE
		@param no_submit optional
		@param start_empty optional type=bool
			If true, initially search results table is empty.
		@returns
			returns the html for search form & results
	**/
	public function do_ajax_search($arr)
	{
		$form_html = $this->_get_search_form($arr);
		$arr["return"] = 1;
		$res_html = html::div(array("id" => "result" , "content" => $this->get_search_results($arr)));

		return $form_html . html::linebreak() . $res_html;
	}

	/**

		@attrib name=ajax_set_property api=1
		@param id optional
		@param property optional
		@param value optional

	**/
	function ajax_set_property($arr)
	{
		$o = obj($arr["id"]);
		$cx = new cfgutils();
		$properties = $cx->load_class_properties(array(
			"clid" => $o->class_id(),
		));
		if(isset($properties[$arr["property"]]["multiple"]) and $properties[$arr["property"]]["multiple"] > 0)
		{
			$val = $o->prop($arr["property"]);
			$val[] = $arr["value"];
		}
		else
		{
			$val = $arr["value"];
		}

		try
		{
			$o->set_prop($arr["property"] , $val);
			$o->save();
			exit($o->prop($arr["property"]));
		}
		catch (awex_obj $e)
		{
			exit("Error");//TODO: something.
		}
	}

	// $arr -- from request
	protected function _get_search_form($arr)
	{
		$htmlc = new htmlclient();
		$htmlc->in_popup_mode(true);
		$htmlc->start_output();

		$property_dfns = $this->_get_search_form_property_definitions($arr);
		foreach ($property_dfns as $name => $definition)
		{
			$htmlc->add_property($definition);
		}

		$clid = empty($arr["clid"]) ? "" : (is_array($arr["clid"]) ? implode("," , $arr["clid"]) : $arr["clid"]);
		$filter_callback = empty($arr[self::REQVAR_FILTER_CALLBACK]) ? "" : "\n '".self::REQVAR_FILTER_CALLBACK."' : '".$arr[self::REQVAR_FILTER_CALLBACK]."',";
		$reload_layout = isset($arr["reload_layout"]) ? "\n'reload_layout' : '".$arr["reload_layout"]."'," : "";
		$reload_property = isset($arr["reload_property"]) ? "\n'reload_property' : '".$arr["reload_property"]."'," : "";
		$reload_window = !empty($arr["reload_window"]) ? "\n'reload_window' : '1'," : "";

		$htmlc->add_property(array(
			"name" => "s[submit]",
			"type" => "submit",
			"value" => t("Otsi"),
			"caption" => t("Otsi")
		));

		// attach loading function to button click and form submit event handlers
		$class = get_class($this);
		$id = isset($arr["id"]) ? $arr["id"] : "0";
		$search_params = "";
		foreach ($property_dfns as $name => $data)
		{
			$search_params .= <<<PARAM
{$name} : $("input[name='s[{$name}]']").val(),

PARAM;
		}

		active_page_data::add_javascript(
<<<ENDJS
	$('#s_name_').focus();
	$('#changeform').submit(function(event) {
		var div = $('#result');
		$.please_wait_window.show({
			'target': div
		});
		javascript:$.get('/automatweb/orb.aw', {class: '{$class}',
				action: 'get_search_results',
				id: '{$id}',
				{$search_params}
				clid: '{$clid}',
				{$filter_callback}{$reload_property}{$reload_layout}{$reload_window}
				property: '{$arr["property"]}'
			}, function (html) {
			x=document.getElementById('result');
			x.innerHTML=html;
			$.please_wait_window.hide();
		});
		return false;
	});
ENDJS
		, "bottom");

		$data = array(
			"id" => isset($arr["id"]) ? $arr["id"] : 0,
			"clid" => $clid,
			"no_submit" => ifset($arr, "no_submit"),
			"append_html" => htmlspecialchars(ifset($arr,"append_html"), ENT_QUOTES),
			"orb_class" => $_GET["class"],
			"reforb" => 0
		);
		$this->_process_reforb_args($data);

		$htmlc->finish_output(array(
			"action" => "do_search",
			"method" => "GET",
			"data" => $data,
			"submit" => "no"
		));

		$html = $htmlc->get_result();

		return $html;
	}

	protected function _get_search_form_property_definitions(array $arr)
	{
		return array(
			"name" => array(
				"name" => "s[name]",
				"type" => "textbox",
				"value" => ifset($arr, "s", "name"),
				"caption" => t("Nimi")
			),
			"oid" => array(
				"name" => "s[oid]",
				"type" => "textbox",
				"value" => ifset($arr, "s", "oid"),
				"caption" => t("Objekti id")
			)
		);
	}

	public function get_filter(array $arr)
	{
		return array();
	}

	/**
		@attrib name=get_search_results orb=1
		@param id optional
		@param oid optional
		@param name type=string default=""
		@param action type=string default=""
		@param multiple optional
		@param clid optional
		@param fcb optional
		@param property type=string default=""
		@param reload_layout type=string default=""
		@param reload_window type=bool default=FALSE
		@param no_submit optional
		@param start_empty type=bool default=FALSE
			If true, initially search results table is empty.
		@returns
			returns the html for search form & results
	**/
	public function get_search_results($arr)
	{
		$this->read_template("table.tpl");

		if(empty($arr["clid"]))
		{
			$arr["clid"] = array();
		}
		elseif (!is_array($arr["clid"]))
		{
			$arr["clid"] = explode("," , $arr["clid"]);
		}

		$t = new aw_table(array(
			"layout" => "generic"
		));

		$search_results_fields = $this->_get_search_results_fields($arr);
		foreach ($search_results_fields as $prop_name => $field_dfn)
		{
			$t->define_field($field_dfn);
		}

		$t->define_field(array(
			"name" => "select_this",
			"caption" => t("Vali"),
		));

		$t->set_default_sortby("name");

		$filter = $this->_get_search_results_filter($arr);

		if (count($filter))
		{
			$ol = new object_list($filter);
			if($ol->count())
			{
				$o = $ol->begin();

				do
				{
					$dat = array();
					foreach ($search_results_fields as $prop_name => $field_dfn)
					{
						// add defined field values
						$prop_getter = "_get_search_result_prop_{$prop_name}";
						if (method_exists($this, $prop_getter))
						{
							$dat[$field_dfn["name"]] = $this->$prop_getter($o, $arr);
						}
						else
						{
							$dat[$field_dfn["name"]] = $o->prop_str($prop_name);
						}

						// add selecting field
						$dat["select_this"] = html::href(array(
							"url" => "javascript:void(0)",
							"caption" => t("Vali see"),
							"onclick" => "set_prop(\"".$o->id()."\")"
						));
					}

					$t->define_data($dat);
				}
				while ($o = $ol->next());
			}
		}

		if (!empty($arr["reload_window"]))
		{
			$reload = "window.opener.location.reload();";
		}
		elseif (!empty($arr["action"]))
		{
			//TODO: popup akent avava klassi action valimisel v2lja kutsuda kui on m22ratud
		}
		else
		{
			$reload = "";
			if (!empty($arr["reload_property"]))
			{
				$reload .= "window.opener.reload_property('".$arr["reload_property"]."');";
			}

			if (!empty($arr["reload_layout"]))
			{
				$reload .= "window.opener.reload_layout('".$arr["reload_layout"]."');";
			}
		}

		$class = get_class($this);
		$javascript = "<script language='javascript'>
			function set_prop(value)
			{
				$.get('/automatweb/orb.aw', {class: '{$class}',
					action: 'ajax_set_property',
					id: '".(!empty($arr["id"]) ? $arr["id"] : "0")."',
					value: value,
					property: '".$arr["property"]."'
				}, function (html) {
					".$reload."
					window.close();
				});
			}
			</script>
		";


		$t->sort_by();
		$this->vars(array(
			"select_text" => t("Vali"),
			"table" => $t->draw().$javascript,
		));

		if(!empty($arr["return"]))
		{
			return $this->parse();
		}
		else
		{
			die($this->parse());
		}
	}

	// $arr -- from request
	protected function _get_search_results_filter(array $arr)
	{
		$filter = array();

		if(!empty($arr["name"]))
		{
			$filter["name"] = "%".$arr["name"]."%";
		}

		if(!empty($arr["oid"]))
		{
			$filter["oid"] = $arr["oid"]."%";
		}

		if (count($filter) or empty($arr["start_empty"])) // don't show default search results if start_empty parameter true
		{
			if($arr["clid"])
			{
				$filter["class_id"] = $arr["clid"];
			}

			$filter[] = new obj_predicate_limit($this->search_results_limit);
		}


		$self = get_class($this);
		$filter_callback = isset($arr[self::REQVAR_FILTER_CALLBACK]) ? (int) $arr[self::REQVAR_FILTER_CALLBACK] : 0;
		$filter_callback = isset($self::$valid_filter_callbacks[$filter_callback]) ? $self::$valid_filter_callbacks[$filter_callback] : $self::$valid_filter_callbacks[0];
		$filter = array_merge($filter, call_user_func($filter_callback, $arr));

		return $filter;
	}

	/*
	must return:
	array(
		property name => aw table field definition parameter array,
		...
	)
	if this class contains a method _get_search_result_prop_PROPERTYNAME then
	that method will be called to get search result table field value. otherwise
	property name will be given directly as parameter to table row aw object prop_str method
	*/
	protected function _get_search_results_fields(array $arr)
	{
		return array(
			"icon" => array(
				"name" => "icon",
				"caption" => t("&nbsp;")
			),
			"oid" => array(
				"name" => "oid",
				"caption" => t("OID")
			),
			"name" => array(
				"name" => "name",
				"sortable" => 1,
				"caption" => t("Nimi")
			),
			"parent" => array(
				"name" => "parent",
				"sortable" => 1,
				"caption" => t("Asukoht")
			),
			"modifiedby" => array(
				"name" => "modifiedby",
				"sortable" => 1,
				"caption" => t("Muutja")
			),
			"modified" => array(
				"name" => "modified",
				"caption" => t("Muudetud"),
				"sortable" => 1,
				"format" => "d.m.Y H:i",
				"type" => "time"
			)
		);
	}

	protected function _get_search_result_prop_name(object $o, array $arr)
	{
		return html::obj_change_url($o);
	}

	protected function _get_search_result_prop_parent(object $o, array $arr)
	{
		return $o->path_str(array("max_len" => 3));
	}

	protected function _get_search_result_prop_icon(object $o, array $arr)
	{
		return html::img(array("url" => icons::get_icon_url($o->class_id())));
	}

	//----------------------------------------------------------
}
