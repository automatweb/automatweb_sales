<?php
// the root of all good.
//
// ------------------------------------------------------------------
// Do not be writink any HTML in this class, it defeats half
// of the purpose of this class. If you really absolutely
// must, then do it in the htmlclient class.
// ------------------------------------------------------------------
//
// Common properties for all classes
/*
	@default table=objects
	@default group=general

	@property name type=textbox rel=1 trans=1
	@caption Nimi

	@property comment type=textbox
	@caption Kommentaar

	// translated objects have their own status fields .. they don't
	// have to sync with the original .. allthu .. I do feel that
	// we need to do this in a different way
	@property status type=status trans=1 default=1
	@caption Aktiivne

	@property needs_translation type=checkbox field=flags method=bitmask ch_value=2 // OBJ_NEEDS_TRANSLATION
	@caption Vajab t&otilde;lget

	// see peaks olemas olema ainult siis, kui sellel objekt on _actually_ mingi asja t&otilde;lge
	property is_translated type=checkbox field=flags method=bitmask ch_value=4 trans=1 // OBJ_IS_TRANSLATED
	caption T&otilde;lge kinnitatud

	@groupinfo general caption=&Uuml;ldine default=1 icon=edit focus=name

	@forminfo add onload=init_storage_object
	@forminfo edit onload=load_storage_object

*/
// some contants for internal use

// possible return values for set_property
// everything's ok, property can be saved
define('PROP_OK',1);

// drop this property from the save queue
define('PROP_IGNORE',2);

// error occured while saving this property, notify
// the user, but still save the rest of the
// object data (if any)
define('PROP_ERROR',3);

// something went very very wrong,
// notify the user and DO NOT display the form/save the object
define('PROP_FATAL_ERROR',4);

// translation
define('RELTYPE_TRANSLATION',102);
define('RELTYPE_ORIGINAL',103);

class class_base extends aw_template
{
	var $id; // loaded storage object id
	var $clid; // loaded storage object class id
	var $cli; // output client reference
	var $clfile;
	var $tmp_cfgform;
	var $cfgform_id;
	var $groupinfo;
	var $classinfo;
	var $cfgmanager;
	var $embedded;
	var $changeform_target;
	var $new;
	var $no_buttons;
	var $inst; //FIXME: an instance of the same class embedded, any reason?

	var $group;
	var $use_group;
	var $active_group;
	var $active_groups = array();
	var $grpmap = array();
	var $subgroup;

	var $use_mode;
	var $view;
	var $no_rte;
	var $cb_values;
	var $output_client;
	var $orb_class;
	var $layout_mode;
	var $leftout_layouts;
	var $_do_call_vcl_mod_reforbs;
	var $no_mod_view = 0;
	var $translation_lang_var_name = "awcb_347c92e42_trans_lid";
	var $translation_lang_id;
	var $transl_grp_name;
	var $is_translated;
	var $request = array();
	var $_do_call_vcl_mod_retvals = array();

	public $form_only = false;
	public $no_form = false;

	protected $_cfg_props;
	protected $classconfig;
	protected $name_prefix = "";

	private $cfgform_obj;
	private $cfg_debug = false;

	private $data_processing_result_status = PROP_OK;
	private $vcl_has_getter = array();

	function class_base($args = array())
	{
		$this->init();
	}

	function init($arg = array())
	{
		$this->output_client = "htmlclient";
		$this->default_group = "general";
		$this->features = array();

		// XXX: this is also temporary
		$this->vcl_has_getter = array(
			"classificator" => 1,
		);

		if (!isset($this->clid) && !empty($arg["clid"]))
		{
			$this->clid = $arg["clid"];
		}
		parent::init($arg);
	}

	function init_storage_object($arr)
	{
		if (!isset($arr["class"]))
		{
			throw new aw_exception("Class not defined");
		}

		$class = $arr["class"];

		if (!aw_ini_isset("class_lut.".$class))
		{
			throw new aw_exception("Invalid class '$class'");
		}

		$clid = aw_ini_get("class_lut.".$class);

		if ("menu" === $class)
		{
			$this->obj_inst = new object();
		}
		else
		{
			$ca = isset($arr["constructor_args"]) ? $arr["constructor_args"] : array();
			$this->obj_inst = obj(null, $ca, $clid);
		}

		$this->use_mode = "new";
		$this->parent = $arr["parent"];
		$this->id = "";
		$this->new = 1;
		$this->reltype = isset($arr["reltype"]) ? $arr["reltype"] : "";
	}

	function load_storage_object($arr)
	{
		if (isset($arr["id"]))
		{
			$id = (int) $arr["id"];
			if (!$id)
			{
				throw new aw_exception("Invalid object id");
			}

			// try to load object with $id
			$this->obj_inst = new object($id);

			if (isset($arr["class"]))
			{
				$class = $arr["class"];
				if (!aw_ini_isset("class_lut.".$class))
				{
					throw new aw_exception("Invalid class '$class'");
				}

				$clid = aw_ini_get("class_lut.".$class);

				if ($this->obj_inst->class_id() != $clid)
				{
					throw new aw_exception("Invalid class '$class'");
				}
			}

			$this->parent = "";
			$this->use_mode = "edit";
			$this->subgroup = isset($args["subgroup"]) ? $args["subgroup"] : "";
			$this->id = $id;
		}
		else
		{
			$this->init_storage_object($arr);
		}
	}


	/** Generate a form for adding or changing an object

		@attrib name=new params=name all_args="1"

		@param class required
		@param parent optional type=int acl="add"
		@param period optional
		@param alias_to optional
		@param alias_to_prop optional
		@param save_autoreturn optional
		@param return_url optional
		@param reltype optional type=int

		@returns data formatted by the currently used output client. For example a HTML form if htmlclient is used

		@comment

	**/
	function new_change($args)
	{
		return $this->change($args);
	}

	/**

		@attrib name=change params=name all_args="1"

		@param id optional type=int acl="edit"
		@param group optional
		@param period optional
		@param alias_to optional
		@param alias_to_prop optional
		@param save_autoreturn optional
		@param return_url optional
		@param view_property optional
		@param view_layout optional

		@returns data formatted by the currently used output client. For example a HTML form if htmlclient is used


		@comment
		id _always_ refers to the objects table. Always. If you want to load
		any other data, then you'll need to use other field name

	**/
	function change($args = array())
	{
		// Avoid view_propert/view_layout spreading into URLS.
		foreach(array("view_layout", "view_property") as $arg)
		{
			if(!empty($args[$arg]))
			{
				$GLOBALS[$arg] = $args[$arg];

				if(isset($_GET[$arg]))
				{
					unset($_GET[$arg]);
				}
				unset($args[$arg]);
				if(automatweb::$request->arg_isset($arg))
				{
					automatweb::$request->unset_arg($arg);
				}
			}
		}

		foreach($args as $k => $v)
		{
			if (!is_array($v))
			{
				$args[$k] = urldecode($v);
			}
		}

		$args["return_url"] = isset($_GET["return_url"]) ? $_GET["return_url"] : "";
		$this->init_class_base();
		$cb_values = aw_global_get("cb_values");
		$has_errors = false;

		if (!empty($cb_values))
		{
			$this->cb_values = $cb_values;
			$has_errors = true;
		}

		if (!isset($args["action"]))
		{
			$args["action"] = "view";
		}

		$cfgform_id = "";
		$this->subgroup = $this->reltype = "";
		$this->is_rel = false;
		$this->orb_action = $args["action"];
		$this->is_translated = 0;

		if(!empty($args["no_buttons"]))
		{
			$this->no_buttons = true;
		}

		if ($args["action"] === "new")
		{
			$this->init_storage_object($args);
		}
		elseif (($args["action"] === "change") || ($args["action"] === "view"))
		{
			try
			{
				$this->load_storage_object($args);
			}
			catch (aw_lock_exception $e)
			{
				$return_url = $_SERVER["HTTP_REFERER"];
				exit("Objekt lukustatud. <br><br>&lt;&lt; <a href='{$return_url}'>Tagasi</a>");
			}

			if ($this->obj_inst->class_id() == CL_RELATION)
			{
				// this is a relation!
				$this->is_rel = true;
				$def = $this->_ct[$this->clid]["def"];
				$meta = $this->obj_inst->meta("values");
				$this->values = $meta[$def];
				$this->values["name"] = $this->obj_inst->name();
			}
		}

		// added in connection with class interface display through cfgform alias
		if (!empty($args["awcb_display_mode"]))
		{
			$this->awcb_display_mode = $args["awcb_display_mode"];
		}
		else
		{
			$this->awcb_display_mode = "default";
		}

		if (isset($args["awcb_cfgform"]) && is_oid($args["awcb_cfgform"]))
		{
			$cfgform_id = $args["awcb_cfgform"];
		}
		else
		{
			// yees, this means that other forms besides add and edit cannot use custom config forms
			// at least not yet.
			// a class should be able to override it
			$cfgform_id = $this->get_cfgform_for_object(array(
				"obj_inst" => $this->obj_inst,
				"args" => $args,
			));
		}

		if (empty($cfgform_id) && is_object($this->obj_inst))
		{
			$cfgform_id = $this->obj_inst->meta("cfgform_id");
		}

		if (isset($_SESSION["cfg_admin_mode"]) and $_SESSION["cfg_admin_mode"] == 1)
		{
			$cfgform_id = null;
		}

		$this->load_cfgform($cfgform_id);


		// well, i NEED to get the right property group BEFORE i get the right view..
		// i hope this doesn't qualify as a hack, because nobody changes the args
		// anyway -- ahz
		$filter = array(
			"clid" => $this->clid,
			"clfile" => $this->clfile,
			"cfgform_id" => $this->cfgform_id,
			"cb_part" => isset($args["cb_part"]) ? $args["cb_part"] : "",
		);

		if (isset($args["group"]))
		{
			$filter["group"] = $args["group"];
		}


		if (!empty($args["form"]))
		{
			$filter["form"] = $args["form"];
		}

		if ($this->is_rel)
		{
			$filter["rel"] = 1;
		}

		// XXX: temporary -- duke
		if (!empty($args["fxt"]))
		{
			$this->layout_mode = "fixed_toolbar";
			$filter["layout_mode"] == "fixed_toolbar";
		}

		$properties = $this->get_property_group($filter, $args);
		$this->inst->use_group = $this->use_group;

		/////////////////////

		$defview = 0;

		if($this->cfgform_obj and $args["action"] != "view")
		{
			$grps = safe_array($this->cfgform_obj->meta("cfg_groups"));
			if(!empty($grps[$this->use_group]["grpview"]))
			{
				$defview = 1;
			}
		}

		if($defview == 1)
		{
			// if by cfgform, the default is view, then take retour here to get the view
			$args["action"] = "view";
			$args["view"] = 1;
			$this->no_mod_view = 1;
			//unset($args["return_url"]);
			//return $this->mk_my_orb("change", $args, $args["class"]);
		}

		if (!empty($args["view"]))
		{
			$this->view = true;
		}

		$use_form = isset($args["form"]) ? $args["form"] : "";
		$this->use_form = $use_form;

		if (method_exists($this->inst,"callback_on_load"))
		{
			$this->inst->callback_on_load(array(
				"request" => $args,
			));
		}

		if (!empty($args["no_active_tab"]))
		{
			$this->no_active_tab = 1;
		}

		unset($properties["is_translated"]);

		if (!aw_ini_get("user_interface.content_trans"))
		{
			unset($properties["needs_translation"]);
		}


		// XXX: temporary -- duke
		if (!empty($args["fxt"]))
		{
			$this->set_classinfo(array("name" => "hide_tabs","value" => 1));
		}

		if (!empty($args["form"]))
		{
			$onload_method = $this->forminfo(array(
				"form" => $args["form"],
				"attr" => "onload",
			));

			if (method_exists($this->inst,$onload_method))
			{
				$this->inst->$onload_method($args);
			}
		}

		$this->request = $args;
		$gdata = !empty($this->subgroup) ? $this->groupinfo[$this->subgroup] : $this->groupinfo[$this->use_group];

		if (!empty($this->id))
		{
			// it is absolutely essential that pre_edit is called
			// only for existing objects
			if (method_exists($this->inst,"callback_pre_edit"))
			{
				$fstat = $this->inst->callback_pre_edit(array(
					"id" => $this->id,
					"request" => $this->request,
					"obj_inst" => $this->obj_inst,
					"group" => $this->use_group,
					"classinfo" => &$this->classinfo,
				));

				if (is_array($fstat) && !empty($fstat["error"]))
				{
					// callback_pre_edit can block saving if it wants to
					// do not mix this with property error codes
					$properties = array();
					$properties["error"] = array(
						"type" => "text",
						"error" => $fstat["errmsg"],
					);
					$gdata["submit"] = "no";
				}
			}
		}

		// the whole freaking fixed toolbar trickery was implemented
		// only because IE does not support positon: fixed like other
		// modern browsers do. Once it does, this whole crap with
		// frames can be taken out again

		// turn off submit button, if the toolbar is being shown

		$lm = $this->classinfo(array("name" => "fixed_toolbar"));
		if (!empty($lm))
		{
			$gdata["submit"] = "no";
		}

		$user_obj = obj(aw_global_get("uid_oid"));
		if (!empty($args["no_rte"]) || $user_obj->prop("rte_disabled"))
		{
			$this->no_rte = 1;
		}

		// and, if we are in fixed toolbar layout mode, then we should
		// probably remap all
		// the links in the toolbar .. augh, how the hell do I do that?
		if (!empty($lm) && empty($args["cb_part"]))
		{
			$new_uri = aw_url_change_var(array("cb_part" => 1));
			$cli = get_instance("cfg/" . $this->output_client, array("layout_mode" => "fixed_toolbar"));
			$cli->used_cfgform = $this->cfgform_id;

			if (!empty($args["no_rte"]))
			{
				$new_uri .= "&no_rte=1";
			}
			$cli->rte_type = $this->classinfo(array("name" => "allow_rte"));
			$properties["iframe_container"] = array(
				"type" => "iframe",
				"src" => $new_uri,
				"value" => " ",
			);

			$this->layout_mode = "fixed_toolbar";

			// show only the elements and not the frame
			// (because it contains some design
			// elements and "<form>" tag that I really do not need
			// this really could use some generic solution!
			$this->raw_output = 1;
		}
		else
		{
			$template = $this->forminfo(array(
				"form" => isset($args["form"]) ? $args["form"] : "",
				"attr" => "template",
			));

			$o_arr = array();
			if(!empty($gdata["no_form"]))
			{
				$o_arr["no_form"] = true;
			}


			if (!empty($template))
			{
				$o_arr["template"] = $template;
			}

			if ($this->embedded)
			{
				$o_arr["embedded"] = true;
			};

			// if there no class in the request URI, then we are embedded
			if (false === strpos(aw_global_get("REQUEST_URI"),"class="))
			{
				$o_arr["embedded"] = true;
			}

			$cli = get_instance("cfg/" . $this->output_client,$o_arr);
			$cli->used_cfgform = $this->cfgform_id;

			if (!empty($lm))
			{
				if ($this->use_mode === "new")
				{
					$cli->set_form_target("_parent");
				};
			};

			if ($this->changeform_target)
			{
					$cli->set_form_target($this->changeform_target);
			}

			if (!empty($o_arr["no_form"]) or $this->no_form)
			{
				$cli->set_opt("no_form",1);
			}

			$use_layout = isset($this->classinfo["layout"]) ? $this->classinfo["layout"] : "";
			// XXX: cfgform seemingly overwrites classinfo
			if ($use_layout === "boxed")
			{
				$cli->set_form_layout($use_layout);
				if (is_callable(array($this->inst,"get_content_elements")))
				{
					$els = $this->inst->get_content_elements(array(
						"obj_inst" => $this->obj_inst,
						"new" => $this->new,
					));
					if (is_array($els))
					{
						foreach($els as $location => $_content)
						{
							$cli->add_content_element($location,$_content);
						}
					}
				}
			}
		}

		$this->cli = $cli;
		$this->cli->use_group = $this->use_group;
		$this->cli->object_id = $this->id;
		$this->cli->class_name = get_class($this->inst);

		// aga mis siis, kui see on sama aken?
		$cbtrans = new cb_translate();
		$trans_default_id = $cbtrans->get_sysdefault();

		if ($trans_default_id)
		{
			$translate_url = html::href(array(
				"caption" => t("T&otilde;lgi"),
				"url" => $this->mk_my_orb("change",array(
						"clid" => $this->clid,
						"group" => "translation_sub",
					"grpid" => isset($args["group"]) ? $args["group"] : null,
					"id" => $trans_default_id->id(),
					"area" => "groupedit",
				),
				"cb_translate"),
			));
		}


		$add_txt = "";
		if (method_exists($this->inst,"callback_get_add_txt"))
		{
			$add_txt = $this->inst->callback_get_add_txt(array(
				"request" => $args,
			));
		}
		$cli->configure(array(
			"help_url" => $this->mk_my_orb("browser",array(
				"clid" => $this->clid,
				"group" => isset($args["group"]) ? $args["group"] : null,
			),
			"help"),

			"translate_url" => in_array(aw_global_get("uid"), safe_array(aw_ini_get("class_base.show_trans"))) && !empty($translate_url) ? $translate_url." | ":"",
			"more_help_text" => t("Rohkem infot"),
			"close_help_text" => t("Peida &auml;ra"),
			"open_help_text" => t("Abiinfo"),
			// sellest teeme ini settingu
			"show_help" => aw_ini_get("class_base.show_help"),
			"add_txt" => $add_txt,
		));
		$cli->rte_type = $this->classinfo(array("name" => "allow_rte"));

		// k&auml;es ongi .. see asi eeldab, et layoutile on grupp peale v&auml;&auml;natud ..
		// samas ei pruugi see &uuml;ldse case olla. sitta sellest grupist .. propertyle &ouml;eldakse
		// mis grupis ta on ja kui seal mingi layout ka ringi t&ouml;llerdab eks ma siis lihtsalt
		// kasutan seda

		// aga mul on kuidagi vaja need layout asjad ka &otilde;igesse kohta saada .. how the fuck do I do that?
		if (is_array($this->layoutinfo) && method_exists($cli,"set_layout"))
		{
			$tmp = array();
			// export only layout information for the current group
			$layout_callback = (method_exists($this->inst,"callback_mod_layout")) ? true : false;
			foreach($this->layoutinfo as $key => $val)
			{
				if($layout_callback)
				{
					$tmpval = $val;
					$tmpval["name"] = $key;
					$tmpval["obj_inst"] = $this->obj_inst;
					$tmpval["request"] = &$_GET;
					if(!$this->inst->callback_mod_layout($tmpval))
					{
						$this->_get_sub_layouts($key);
						continue;
					}

					if (!empty($tmpval["area_caption"]) and $tmpval["area_caption"] != $val["area_caption"])
					{
						$this->layoutinfo[$key]["area_caption"] = $tmpval["area_caption"];
						$val["area_caption"] = $tmpval["area_caption"];
					}
				}
				$_lgroups = is_array($val["group"]) ? $val["group"] : array($val["group"]);
				if (in_array($this->use_group,$_lgroups))
				{
					$tmp[$key] = $val;
				};
			}

			$cli->set_layout($tmp);
		}

		// cb_parts is again used by fixed_toolbar mode
		if (!empty($args["cb_part"]))
		{
			// tabs and YAH are in the upper frame, so we don't show them below
			$this->set_classinfo(array("name" => "hide_tabs","value" => 1));
			$this->set_classinfo(array("name" => "no_yah","value" => 1));
		}

		// parse the properties - resolve generated properties and
		// do any callbacks
		// and the only user of that is the crm_company class. Would be _really_ nice
		// to beg rid of all that shit
		$this->inst->relinfo = $this->relinfo;
		$panel = array(
			"name" => "tabpanel",
			"type" => "tabpanel",
			"caption"=> t("tab panel"),
		);
		$properties = array("tabpanel" => $panel) + $properties;
		$resprops = $this->parse_properties(array(
			"properties" => &$properties,
			"obj_inst" => $this->obj_inst
		));

		// what exactly is going on with that subgroup stuff?
		if (isset($resprops["subgroup"]))
		{
			$this->subgroup = $resprops["subgroup"]["value"];
		}

		if ($has_errors)
		{
			// give the output client a chance to display a message stating
			// that there were errors in entered data. Individual error
			// messages will be next to their respective properties, this
			// is just the caption
			$this->push_msg(t("Viga sisendandmetes"), "ERROR");
		}

		// so now I have a list of properties along with their values,

		// and, if we are in that other layout mode, then we should probably remap all
		// the links in the toolbar .. augh, how the hell do I do that?
		if ($this->view)
		{
			$cli->view_mode = 1;
		}

		foreach($resprops as $_k => $val)
		{
			if(!in_array(isset($val["parent"]) ? $val["parent"] : null, safe_array($this->leftout_layouts)))
			{
				$cli->add_property($val);
			}
		}

		$orb_class = $this->_ct[$this->clid]["file"];
		if (empty($orb_class))
		{
			$orb_class = $this->clfile;
		}

		if (basename($orb_class) === "document")
		{
			$orb_class = "doc";
		}

		$argblock = array(
			"id" => $this->id,
			// this should refer to the active group
			"group" => isset($this->request["group"]) ? $this->request["group"] : $this->use_group,
			"orb_class" => basename($orb_class),
			"parent" => $this->parent,
			"section" => aw_global_get("section"),
			"period" => isset($this->request["period"]) ? $this->request["period"] : "",
			"alias_to" => isset($this->request["alias_to"]) ? $this->request["alias_to"] : "",
			"alias_to_prop" => isset($this->request["alias_to_prop"]) ? $this->request["alias_to_prop"] : "",
			"save_autoreturn" => isset($this->request["save_autoreturn"]) ? $this->request["save_autoreturn"] : "",
			"cfgform" => empty($this->auto_cfgform) && $this->cfgform_id && is_numeric($this->cfgform_id) ? $this->cfgform_id : "",
			"return_url" => !empty($this->request["return_url"]) ? $this->request["return_url"] : "",
			"subgroup" => $this->subgroup,
			"awcb_display_mode" => $this->awcb_display_mode,
			"all_trans_status" => 0,
		) + (isset($this->request["extraids"]) && is_array($this->request["extraids"]) ? array("extraids" => $this->request["extraids"]) : array());

		if(!empty($this->reltype))
		{
			$argblock["reltype"] = $this->reltype;
		}

		// class_base should not rely on storage, because this seriosly
		// limits it's functionality!
		if (isset($this->request["object_type"]) && is_oid($this->request["object_type"]) && $this->can("view",$this->request["object_type"]))
		{
			$ot_obj = new object($this->request["object_type"]);
			if ($ot_obj->class_id() == CL_OBJECT_TYPE)
			{
				$argblock["_object_type"] = $this->request["object_type"];
			};
		}

		if (!empty($args["no_rte"]))
		{
			$argblock["no_rte"] = 1;
		}

		if (!empty($args["is_sa"]))
		{
			$argblock["is_sa"] = 1;
		}

		if (!empty($args["pseh"]))
		{
			$argblock["pseh"] = $args["pseh"];
		}

		if (!empty($_GET["pseh"]))
		{
			$argblock["pseh"] = $_GET["pseh"];
		}

		if (!empty($_GET["in_popup"]))
		{
			$argblock["in_popup"] = $_GET["in_popup"];
		}

		$argblock["post_ru"] = post_ru();

		if (method_exists($this->inst,"callback_mod_reforb"))
		{
			$this->inst->callback_mod_reforb($argblock, $this->request);
		}

		if(isset($GLOBALS["add_mod_reforb"]) and is_array($GLOBALS["add_mod_reforb"])) // et saaks m6nest x kohast veel v2lju juurde, n2iteks mingist komponendist
		{
			foreach($GLOBALS["add_mod_reforb"] as $key => $val)
			{
				$argblock[$key] = $val;
			}
		}

		if (is_array($this->_do_call_vcl_mod_reforbs))
		{
			foreach($this->_do_call_vcl_mod_reforbs as $vcl_mro)
			{
				$vcl_mro[0]->$vcl_mro[1]($argblock, $this->request);
			}
		}

		$submit_action = "submit";

		$form_submit_action = $this->forminfo(array(
			"form" => $use_form,
			"attr" => "onsubmit",
		));

		if (!empty($form_submit_action))
		{
			$submit_action = $form_submit_action;
		}

		// forminfo can override form post method
		$form_submit_method = $this->forminfo(array(
			"form" => $use_form,
			"attr" => "method",
		));

		$method = "post";

		if (!empty($form_submit_method))
		{
			$method = "get";
		}

		if (!empty($gdata["submit_method"]))
		{
			$method = "get";
			$submit_action = $args["action"];
		}

		if (!empty($gdata["submit_action"]))
		{
			$submit_action = $gdata["submit_action"];
		}

		if ($method === "get")
		{
			if (!is_admin() and method_exists($cli, "set_handler"))
			{
				$cli->set_handler("index");
			}

			$argblock["no_reforb"] = 1;
		}

		if (aw_global_get("changeform_target") != "")
		{
			$cli->set_form_target(aw_global_get("changeform_target"));
		}

		$gen_scripts_args = array(
			"request" => isset($this->request) ? $this->request : "",
			"obj_inst" => $this->obj_inst,
			"groupinfo" => &$this->groupinfo,
			"new" => $this->new,
			"view" => $this->view,
		);
		$scripts = $this->callback_generate_scripts_from_class_base($gen_scripts_args);
		if (method_exists($this, "callback_generate_scripts"))
		{
			$scripts .= $this->callback_generate_scripts($gen_scripts_args);
		}

		// find help data for current tab
		$cls = aw_ini_get("classes");
		$__a_class = $argblock["orb_class"];
		if ($__a_class == "doc")
		{
			$__a_class = "document";
		}
		foreach($cls as $clid => $cl)
		{
			if(isset($cl["file"]) && basename($cl["file"]) == $__a_class)
			{
				$current_clid = $clid;
				break;
			}
		}
		$po_loc = aw_ini_get("basedir")."/lang/trans/".aw_global_get("LC")."/po/".$argblock["orb_class"].".po";
		$cfgu = new cfgutils();
		$groups = $cfgu->get_groupinfo();
		$msgid_grp_cpt = isset($groups[$argblock["group"]]["caption"]) ? $groups[$argblock["group"]]["caption"] : "";
		$msgid = "Grupi ".$msgid_grp_cpt." (".$argblock["group"].") comment";
		$help = strlen(t2($msgid))?"<div>".t($msgid)."</div>":t("Lisainfo grupi kohta puudub");
		//
		$cli->view_layout = !empty($GLOBALS["view_layout"]) ? $GLOBALS["view_layout"] : false;
		$cli->view_outer = true;//$args["view_outer"]?$args["view_layout"]:false;
		$cli->finish_output(array(
			"method" => $method,
			"action" => $submit_action,
			// hm, dat is weird!
			"submit" => isset($gdata["submit"]) ? $gdata["submit"] : "",
			"back_button" => isset($gdata["back_button"]) ? $gdata["back_button"] : null,
			"forward_button" => isset($gdata["forward_button"]) ? $gdata["forward_button"] : null,
			"data" => $argblock,
			// focus contains the name of the property that
			// should be focused by the output client
			"focus" => isset($gdata["focus"]) ? $gdata["focus"] : null,
			"help" => $help,
			"scripts" => $scripts,
			"is_sa_changed" => isset($this->request["is_sa_changed"]) ? $this->request["is_sa_changed"] : null
		));

		extract($args);

		// it would be nice to get the errors and other stuff from the object also,
		// so we unset cb_values here -- ahz
		if(isset($this->cb_values))
		{
			aw_session_del("cb_values");
		}


		if (isset($args["form"]) && ($args["form"] === "new" || $args["form"] === "change"))
		{
			$orb_action = "change";
		}
		else
		if($this->no_mod_view == 1)
		{
			$orb_action = "change";
		}
		else
		{
			$orb_action = $args["action"];
		}

		if (isset($this->view) && $this->view == 1 && !$this->no_mod_view)
		{
			$orb_action = "view";
		}

		// LETS ROCK
		$u = get_instance(CL_USER);
		$u = $u->get_obj_for_uid(aw_global_get("uid"));
		if (!empty($u))
		{
			$ulev = !strlen($_t = ($u->prop("warning_notification")))?0:$_t;
		}

		if($ulev)
		{
			foreach(safe_array(warning_prop()) as $oid => $properties)
			{
				if(!$this->can("view", $oid))
				{
					continue;
				}
				$o = obj($oid);
				$prplist = $o->get_property_list();
				unset($wprops);
				$maxlevel = 0;
				foreach($properties as $prop => $level)
				{
					// level check
					if($level < $ulev)
					{
						continue;
					}
					if($_t = trim($prplist[$prop]["caption"]))
					{
						$wprops[] = $_t;
						$maxlevel = ($maxlevel < $level)?$level:$maxlevel;
					}

				}
				if(count($wprops))
				{
					$url = $this->mk_my_orb("change", array(
						"id" => $o->id(),
						"return_url" => get_ru(),
					), $o->class_id());
					$warn = "Objektis <b><a href=\"".$url."\">".(strlen($_t = $o->name())?$_t:t("Nimetu"))."</a></b> on m&auml;&auml;ramata v&auml;&auml;rtused: '".join("', '", $wprops)."'";
					warning($warn, $maxlevel);
				}
			}

			$final_warns = array();
			foreach(safe_array(warning()) as $level => $warns)
			{
				if($level < $ulev)
				{
					continue;
				}
				$warns = array_unique($warns);
				$final_warns[] = join("<br/>", $warns);
			}
			$final_warns = array_unique($final_warns);
			// LETS STOP ROKKIN'
			$this->cli->config = count($final_warns)?($this->cli->config + array( "warn" => join("<br/>", $final_warns))):$this->cli->config;
		}
		$rv = $this->gen_output(array(
			"parent" => $this->parent,
			"content" => isset($content) ? $content : "",
			//"orb_action" => $this->view == 1 ? "view" : "",
			"orb_action" => $orb_action,
			"view_property" => ifset($GLOBALS, "view_property"),
		));

		if(!empty($GLOBALS["view_property"]))
		{
			$rv = iconv(aw_global_get("charset"), "UTF-8", $rv);
			die($rv);
		}
		else
		{
			return $rv;
		}
	}

	private static function push_msg($text, $class)
	{
		if (is_string($text) and strlen($text) > 1)
		{
			$ui_messages = aw_global_get("awcb__global_ui_messages");
			if (strlen($ui_messages) > 1)
			{
				$ui_messages = unserialize($ui_messages);
				if (!is_array($ui_messages))
				{
					$ui_messages = array();
				}
			}
			else
			{
				$ui_messages = array();
			}

			$ui_messages[$text] = $class;
			aw_session_set("awcb__global_ui_messages", serialize($ui_messages));
		}
	}

	/** Shows message text to user indicating that an error occurred
	@attrib api=1 params=pos
	@param text type=string
	**/
	public static function show_error_text($text)
	{
		self::push_msg($text, "ERROR");
	}

	/** Shows a success message text to user
	@attrib api=1 params=pos
	@param text type=string
	**/
	public static function show_success_text($text)
	{
		self::push_msg($text, "OK");
	}

	/** Shows an informative message to user
	@attrib api=1 params=pos
	@param text type=string
	**/
	public static function show_msg_text($text)
	{
		self::push_msg($text, "");
	}

	function _get_sub_layouts($lay)
	{
		$this->leftout_layouts[$lay] = $lay;
		foreach($this->layoutinfo as $key => $val)
		{
			if(ifset($val, "parent") == $lay)
			{
				$this->_get_sub_layouts($key);
			}
		}
	}

	/**
		@attrib name=draft api=1 params=name

		@param prop required type=string
			The name of the property the draft is asked for.
		@param id optional type=int
			The OID of the object the draft is asked for. Either OID or clid must be given!
		@param class optional type=string
			The class of the object the draft is asked for. Either OID or clid must be given!
	**/
	function draft($arr)
	{
		if(is_oid($arr["id"]))
		{
			$o = obj($arr["id"]);
		}
		else
		{
			$o = obj();
			$o->set_class_id(constant("CL_".strtoupper($arr["class"])));
		}
		//return $o->draft($arr["prop"]);
		die(iconv(aw_global_get("charset"), "UTF-8", $o->draft($arr["prop"])));
	}

	/**
		@attrib name=set_draft api=1 params=name

		@param prop required type=string
			The name of the property to be drafted.
		@param value required type=string
			The value of the property to be drafted.
		@param id optional type=int
			The OID of the object to be drafted. Either OID or clid must be given!
		@param class optional type=string
			The class of the object to be drafted. Either OID or clid must be given!
	**/
	function set_draft($arr)
	{
		$arr["value"] = iconv("UTF-8", aw_global_get("charset")."//IGNORE", $arr["value"]);
		if(is_oid($arr["id"]))
		{
			$o = obj($arr["id"]);
		}
		else
		{
			$o = obj();
			$o->set_class_id(constant("CL_".strtoupper($arr["class"])));
		}
		$o->set_draft($arr["prop"], $arr["value"]);
	}

	/**
		 @attrib name=remove_drafts api=1 params=name

		@param id optional type=int
			The OID of the object to remove the drafts for. Either OID or clid must be given!
		@param class optional type=string
			The class of the object to remove the drafts for. Either OID or clid must be given!
	**/
	function remove_drafts($arr)
	{
		$params = array(
			"class_id" => CL_DRAFT,
			"draft_object" => $arr["id"],
			"draft_user" => get_instance("user")->get_current_user(),
		);
		if(!is_oid($arr["id"]))
		{
			unset($params["draft_object"]);
			$params["draft_new"] = is_class_id($arr["class"]) ? $arr["class"] : constant("CL_".strtoupper($arr["class"]));
		}
		$ol = new object_list($params);
		$ol->delete();
	}

	/** calls submit and returns to previous view if save was successful
		@attrib name=submit_and_return params=name
	**/
	function submit_and_return($args = array())
	{
		$r = $this->submit($args);
		$no_return_url_msg = t("Tagasiviidet eelmisele vaatele ei leitud");
		if ($this->data_processed_successfully() and !empty($args["return_url"]))
		{
			try
			{
				$r = new aw_uri($args["return_url"]);
				$r = $r->get();
			}
			catch (Exception $e)
			{
				$this->show_msg_text($no_return_url_msg);
			}
		}
		else
		{
			$this->show_msg_text($no_return_url_msg);
		}
		return $r;
	}

	/** Saves the data that comes from the form generated by change
		@attrib name=submit params=name
		@returns
		@comment
	**/
	function submit($args = array())
	{
		if (!empty($args["posted_by_js"]))
		{
			$args = iconv_array("utf-8", aw_global_get("charset"), $args);
		}
		$form_data = null;
		// since submit should never change the return url, make sure we get at it later
		$real_return_url = empty($args["return_url"]) ? "" : $args["return_url"];

		// check whether this current class is based on class_base
		$this->init_class_base();
		$this->orb_action = empty($args["action"]) ? "submit" : $args["action"];

		$this->is_translated = 0;
		// object framework does it's own quoting
		//$this->quote($args);

		$request = $args;
		$id = isset($args["id"]) ? $args["id"] : null;
		$group = isset($args["group"]) ? $args["group"] : null;
		$extraids = isset($args["extraids"]) ? $args["extraids"] : null;
		$action = isset($args["action"]) ? $args["action"] : null;
		$all_trans_status = isset($args["all_trans_status"]) ? $args["all_trans_status"] : null;

		// I need to know the id of the configuration form, so that I
		// can load it. Reason being, the properties can be grouped
		// differently in the config form then they are in the original
		// properties
		$this->is_rel = false;
		if (!empty($id))
		{
			// aha .. so .. if we are editing an relation object, then set $this->is_rel to true
			$_tmp = new object($id);
			if ($_tmp->class_id() == CL_RELATION)
			{
				$this->is_rel = true;
			}
		}

		$tmp = $args;	// avoid recursive structure
		$args["rawdata"] = $tmp;

		try
		{
			$save_ok = $this->process_data($args);
		}
		catch (Exception $e)
		{
			$save_ok = $this->process_submit_error($e);
		}

		if (!empty($args["submit_and_forward"]))
		{
			$gd = $this->groupinfo[$group];
			if (!empty($gd["forward_button"]))
			{
				$group = $gd["forward_button"];
			}
		}

		if (!empty($args["submit_and_back"]))
		{
			$gd = $this->groupinfo[$group];
			if (!empty($gd["back_button"]))
			{
				$group = $gd["back_button"];
			}
		}

		$args = array(
			"id" => $this->id,
			"group" => $group,
			"return" => isset($args["return"]) ? $args["return"] : null,
			"period" => aw_global_get("period"),
			"alias_to" => isset($request["alias_to"]) ? $request["alias_to"] : null,
			"alias_to_prop" => isset($request["alias_to_prop"]) ? $request["alias_to_prop"] : null,
			"return_url" => isset($request["return_url"]) ? $request["return_url"] : "",
		) + ((isset($extraids) && is_array($extraids)) ? $extraids : array());

		if (!$save_ok)
		{
			$args["parent"] = $request["parent"];
			if ($this->new)
			{
				$action = "new";
				unset($args["id"]);
			}
			else
			{
				$action = "change";
			}
		}
		else
		{
			$action = "change";
		}

		$orb_class = is_object($this->orb_class) ? get_class($this->orb_class) : $this->orb_class;
		$class = aw_global_get("class");
		if (is_oid($class))
		{
			$orb_class = $class;
		}

		if ($save_ok)
		{
			if (isset($request["is_sa"]) and $request["is_sa"] == 1)
			{
				$args["is_sa"] = 1;
				$args["is_sa_changed"] = 1;
			}

			if (!empty($request["in_popup"]))
			{
				$args["in_popup"] = 1;
			}

			if (method_exists($this->inst, "callback_mod_retval"))
			{
				$tmp = array(
					"action" => &$action,
					"args" => &$args,
					"request" => &$request,
					"orb_class" => &$orb_class,
					"clid" => $this->clid,
					"new" => $this->new
				);
				$this->inst->callback_mod_retval($tmp);

				if (!empty($args["goto"]))
				{
					return $args["goto"];
				}
			}

			if (isset($this->_do_call_vcl_mod_retvals) && is_array($this->_do_call_vcl_mod_retvals))
			{
				foreach($this->_do_call_vcl_mod_retvals as $vcl_mro)
				{
					$vcl_mro[0]->$vcl_mro[1](array(
						"action" => &$action,
						"args" => &$args,
						"request" => &$request,
						"orb_class" => &$orb_class,
						"clid" => $this->clid,
						"new" => $this->new,
					));
				}
			}

			if (isset($request["cfgform"]) and $this->can("view", $request["cfgform"]))
			{
				$cfgform_o = obj($request["cfgform"]);
				if (!is_admin() and $cfgform_o->trans_get_val("cfgview_ru" . ($this->new ? "" : "_change")) != "")
				{
					$retval = $cfgform_o->trans_get_val("cfgview_ru" . ($this->new ? "" : "_change"));
					if($cfgform_o->prop("cfgview_ru_id_param") != "")
					{
						$retval = aw_url_change_var($cfgform_o->prop("cfgview_ru_id_param"), $this->obj_inst->id(), $retval);
					}
				}
				if(!is_admin() && is_oid($cfgform_o->prop("cfgview_ru_cntrl")) && $this->can("view", $cfgform_o->prop("cfgview_ru_cntrl")))
				{
					$nothing = NULL;
					$i = get_instance(CL_CFGCONTROLLER);
					$retval = $i->check_property($cfgform_o->prop("cfgview_ru_cntrl"), $this->id, $nothing, $request, $retval, obj($this->id));
				}

				// call mod retval controller(s) from cfgform object if defined
				$cfg_cntrl = (array) $cfgform_o->prop("mod_retval_controllers");

				if (count($cfg_cntrl))
				{
					$controller_inst = get_instance(CL_CFGCONTROLLER);

					foreach ($cfg_cntrl as $cfg_cntrl_id)
					{
						if (is_oid($cfg_cntrl_id))
						{
							$tmp = null;
							$retval = $controller_inst->check_property($cfg_cntrl_id, $this->id, $tmp, $args, $request, $tmp);
						}
					}
				}
			}
			// Remove drafts by the current user on save.
			$this->remove_drafts(array(
				"id" => $this->new ? 0 : $this->id,
				"class" => $this->clid,
			));
		}

		$si = __get_site_instance();
		if(method_exists($si, "override_retval"))
		{
			$override_retval = $si->override_retval(array(
				"request" => $request,
			));
			$retval = $override_retval ? $override_retval : $retval;
		}

		if (empty($retval))
		{
			if (isset($this->id_only))
			{
				$retval = $this->id;
			}
			else
			{
				//$use_orb = true;
				if (!empty($request["section"]))
				{
					$args["section"] = $request["section"];
					//$args["_alias"] = get_class($this);
					$use_orb = false;
				};
				if (!empty($request["XUL"]))
				{
					$args["XUL"] = 1;
				}

				$args["return_url"] = $real_return_url;

				if ($this->new && isset($_POST["cfgform"]))
				{
					$args["cfgform"] = $_POST["cfgform"];
				}

				$retval = $this->mk_my_orb($action,$args,$orb_class,false, (!empty($request["ret_to_orb"]) ? true : false), "&", false);

				if (is_numeric($class))
				{
					$retval = aw_url_change_var("class",$class,$retval);
				}

				if (isset($request["awcb_display_mode"]) and "cfg_embed" === $request["awcb_display_mode"])
				{
					$retval = aw_url_change_var(array(
						"class" => NULL,
						"action" => NULL,
						"alias_to" => NULL,
						"alias_to_prop" => NULL,
						"save_autoreturn" => NULL
					), false, $retval);
				}

				if (isset($request["return"]) and $request["return"] === "id")
				{
					$retval = $this->id;
				}
			}
		}

		if (!$save_ok)
		{
			return $this->abort_action($args);
		}

		// add translation subgroup lang id
		if (!empty($this->inst->translation_lang_id))
		{
			$retval = aw_url_change_var($this->translation_lang_var_name, $this->inst->translation_lang_id, $retval);
		}

		if($all_trans_status != 0)
		{
			$o = obj($id);
			$langs = aw_ini_get("languages");
			foreach(array_keys($langs["list"]) as $lid)
			{
				$o->set_meta("trans_".$lid."_status", 2 - $all_trans_status);
			}
			$o->save();
		}

		if (!empty($request["save_autoreturn"]))
		{
			if (!empty($request["return_url"]))
			{
				$retval = $request["return_url"];
			}
			elseif (!empty($request["post_ru"]))
			{
				try
				{
					$post_ru = new aw_uri($request["post_ru"]);
					if ($post_ru->arg("return_url"))
					{
						$return_url = new aw_uri($post_ru->arg("return_url"));
						$retval = $return_url->get();
					}
					else
					{
						$retval = $request["post_ru"];
					}
				}
				catch (Exception $e)
				{
					$retval = $request["post_ru"];
				}
			}
		}
		return $retval;
	}

	protected function process_submit_error(Exception $caught_exception)
	{
		$this->show_error_text(t("Andmete salvestamisel esines viga"));
		$this->data_processing_result_status = PROP_FATAL_ERROR;
		trigger_error("Caught exception " . get_class($caught_exception) . " while saving data. Thrown in '" . $caught_exception->getFile() . "' on line " . $caught_exception->getLine() . ": '" . $caught_exception->getMessage() . "' <br> Backtrace:<br>" . dbg::process_backtrace($caught_exception->getTrace(), -1, true), E_USER_WARNING);
		return false;
	}

	function get_cfgform_for_object($args = array())
	{
		// or, if configuration form should be loaded from somewhere
		// else, this is the place to do it

		if (!empty($_SESSION["cfg_admin_mode"]) && !isset($args["ignore_cfg_admin_mode"]))
		{
			return "";
		}

		$action = isset($args["args"]["action"]) ? $args["args"]["action"] : "";
		$retval = "";
		$cgid = false;

		// first priority -- cfgform defined by application
		$application = automatweb::$request->get_application();
		if ($application->implements_interface("application_interface"))
		{
			$cfgform = $application->get_cfgform_for_object($args["obj_inst"]);
			if (is_object($cfgform))
			{
				return $cfgform->id();
			}
		}

		// check if the classs has a callback_get_cfgform method
		if (method_exists($this, "callback_get_cfgform"))
		{
			$cfid = $this->callback_get_cfgform(array(
				"obj_inst" => $args["obj_inst"],
			));
			if ($this->can("view", $cfid))
			{
				return $cfid;
			}
		}

		// 1. if there is a cfgform specified in the url, then we will use that
		if (!empty($args["args"]["cfgform"]))
		{
			// I need additional check, whether that config form really exists!
			$cgid = $args["args"]["cfgform"];
			// I need to check whether that config form is really
			// a config form with correct subclass
			if ($this->can("view", $cgid))
			{
				return $cgid;
			}
		}

		// 2. seadete vorm kasutajagrupist, seostatud cfgform
		if ($action === "change")
		{
			$gl = aw_global_get("gidlist_pri_oid");
			asort($gl);
			foreach($gl as $id => $pri)
			{
				if($this->can("view" , $id))
				{
					$groupobj = obj($id);
					foreach($groupobj->connections_from(array(
						"type" => "RELTYPE_CFG_FORM",
					)) as $c)
					{
						$cfgformobj = obj($c->to());
						if($cfgformobj->prop("subclass") == $args["obj_inst"]->class_id())
						{
							return $cfgformobj->id();
						}
					}
				}
			}
		}


		// 3. failing that, if there is a config form specified in the object metainfo,
		//  we will use it
		// DEPRECATED. considered a bad practice. skip this step
		/*
		if (($action === "change") && is_oid($args["obj_inst"]->meta("cfgform_id")))
		{
			$cgid = $args["obj_inst"]->meta("cfgform_id");
			if (!$this->can("view", $cgid))
			{
				//!!! cfgf on defineeritud ja peaks m6juma kuid talle pole 6igusi.
			}
			else
			{
				return $cgid;
			}
		}
		*/

		// 4. failing that too, we will check whether this class has a default cfgform
		// and if so, use it
		if ($this->clid == CL_DOCUMENT)
		{
			// I should be able to override this from the doc class somehow
			$def_cfgform = aw_ini_get("document.default_cfgform");
			if (!empty($def_cfgform) && $this->can("view",$def_cfgform))
			{
				return $cgid;
			}
		}


		// If uses configform manager then use this.
		if(!empty($this->tmp_cfgform))
		{
			return $this->tmp_cfgform;
		}

		// XXX: this happens for classes created with class_designer
		if (empty($this->clid))
		{
			return false;
		}

		$ol = new object_list(array(
			"class_id" => CL_CFGFORM,
			"subclass" => $this->clid,
			"lang_id" => array(),
			"flags" => array(
				"mask" => OBJ_FLAG_IS_SELECTED,
				"flags" => OBJ_FLAG_IS_SELECTED
	        	),
		));

		if (sizeof($ol->ids()) > 0)
		{
			$first = $ol->begin();
			return $first->id();
		}

		// okey, I need a helper class. Something that I can create, something can load
		// properties into and then query them. cfgform is taken, what name will I use?



		// right now .. only document class has the default config form .. or the default
		// file. Ungh.

		// 4. failing that, we will check whether this class has a default cfgform _file_
		// and if so, use it

		// 5. if all above fails, simply load the default properties. that means do nothing
		return false;
	}

	////
	// !This checks whether we have all required data and sets up the correct
	// environment if so.
	function init_class_base()
	{
		$_ct = aw_ini_get("classes");
		// only classes which have defined properties
		// can use class_base

		if (aw_ini_get("debug_mode") == 1 && !empty($_REQUEST["CFG_DEBUG"]))
		{
			$this->cfg_debug = true;
		}

		// create an instance of the class servicing the object ($this->inst)
		// set $this->clid and $this->clfile
		$cfgu = new cfgutils();
		$orb_class = $_ct[$this->clid]["file"];
		if (empty($orb_class) && is_object($this->orb_class))
		{
			$orb_class = get_class($this->orb_class);
		}

		if (empty($orb_class) && is_string($this->orb_class))
		{
			$orb_class = $this->orb_class;
		}

		if (isset($this->orb_class) && is_object($this->orb_class))
		{
			$orb_class = get_class($this->orb_class);
		}

		if ($orb_class === "document")
		{
			$orb_class = "doc";
		}

		$has_properties = $cfgu->has_properties(array("file" => $orb_class));
		if (!$has_properties)
		{
			error::raise(array(
				"msg" => sprintf("this class (%s/%d) does not have any defined properties ",$orb_class, $this->clid),
			));
		}

		$clid = $this->clid;

		if (empty($clid) && is_object($this->orb_class) && method_exists($this->orb_class, "get_opt"))
		{
			$clid = $this->orb_class->get_opt("clid");
		}
		$clfile = $_ct[$clid]["file"];

		// temporary - until we switch document editing back to new interface
		if ($clid == 7)
		{
			$clfile = "doc";
		}

		if (empty($clfile))
		{
			$this->clfile = $orb_class;
		}
		else
		{
			$this->clfile = $clfile;
		}

		$this->clid = $clid;
		$this->_ct = $_ct;

		// get an instance of the class that handles this object type
		// fuck me plenty! .. orb.aw sets $this->orb_class
		if (isset($this->orb_class) && is_object($this->orb_class))
		{
			$this->inst = $this->orb_class;
		}
		// but I'm keeping the old approach too, just to be sure that
		// nothing breaks
		else
		{
			$this->inst = get_instance($this->clfile);
		}
	}

	function gen_output($args = array())
	{
		$classname = $this->_ct[$this->clid]["name"];
		if (is_object($this->obj_inst))
		{
			$name = $this->obj_inst->name();
		};
		$return_url = !empty($this->request["return_url"]) ? $this->request["return_url"] : "";
		$is_container = in_array($this->clid,get_container_classes());
		// XXX: pathi peaks htmlclient tegema
		$title = isset($args["title"]) ? $args["title"] : "";
		if (is_oid($this->id))
		{
			if (empty($title))
			{
				$title = $name;
			}

			if ($is_container)
			{
				$title = html::href(array(
					"url" => admin_if::get_link_for_obj($this->id),
					"caption" => $name,
				));
				$title .= " / " . t("Muuda");
			}
			$parent = $this->obj_inst->parent();
		}
		else
		{
			if (empty($title))
			{
				$title = t("Uus") . " ${classname}";
			}
			$parent = $args["parent"];
		}

		// let the class specify it's own title
		if (method_exists($this->inst,"callback_gen_path"))
		{
			$title = $this->inst->callback_gen_path(array(
				"id" => $this->id,
				"parent" => $args["parent"],
			));
		};

		if (!empty($this->request["return_url"]))
		{
			$parent = -1;
			if (strpos($this->request["return_url"],"b1=1"))
			{
				$target = "_top";
			}
			else
			{
				$target = "_self";
			};
			$title = html::href(array(
				"url" => automatweb::$request->arg("return_url"),
				"caption" => t("Tagasi"),
				"target" => $target,
			));
		}
		else
		{
			$title = "";
		}

		$no_yah = $this->classinfo(array("name" => "no_yah"));
		// but that doesn't really belong to classinfo
		if (empty($no_yah))
		{
			if (automatweb::$request->class_name() === "admin_if")
			{
				$parent = automatweb::$request->arg("parent");
			}

			if ($this->obj_inst->class_id() == CL_MENU)
			{
				$this->mk_path($this->obj_inst->id(),$title,aw_global_get("period"));
			}
			else
			{
				$this->mk_path($parent,$title,aw_global_get("period"));
			}
		}


		// I need a way to let the client (the class using class_base to
		// display the editing form) to add it's own tabs.

		if (empty($this->group))
		{
			$this->group = "";
		}

		$activegroup = isset($this->activegroup) ? $this->activegroup : $this->group;
		$activegroup = isset($this->action) ? $this->action : $activegroup;
		$activegroup = $this->use_group;

		$orb_action = isset($args["orb_action"]) ? $args["orb_action"] : "";
		if (!isset($orb_action))
		{
			$orb_action = "change";
		}

		$link_args = new aw_array(array(
			"id" => isset($this->id) ? $this->id : false,
			"group" => "",
			"return_url" => ($return_url),
		));

		// so .. what .. do I add tabs as well now?
		$tab_callback = (method_exists($this->inst,"callback_mod_tab")) ? true : false;

		$hide_tabs = isset($this->classinfo["hide_tabs"]);
		if($this->can("view" , aw_global_get("section")))
		{
			$sec_obj = obj(aw_global_get("section"));
			if($sec_obj->prop("submenus_from_cb"))
			{//FIXME: a block of dubious code. cfgform from meta only, mixed sources for request arguments, ...
				$hide_tabs = 1;
				$subcb = obj($_GET["id"]);
				if($subcb->meta("cfgform_id"))
				{
					$this->cfgform_id = $subcb->meta("cfgform_id");
				}

				if (!empty($this->request["return_url"]))
				{
					$GLOBALS["yah_end"]=$this->make_tabscb_yah($this->request["return_url"]).
					" > ".html::href(array(
						"url" => aw_url_change_var(array("group" => null)),
						"caption" => $subcb->name()?$subcb->name():t("Nimetu"),
					));
				}

				if(automatweb::$request->arg("group") && is_object($subcb))
 				{
 					$cfgform_i = get_instance(CL_CFGFORM);
 					$cfgform = $subcb->meta("cfgform_id");
 					$cfgform_i->cff_init_from_class($subcb, $subcb->class_id(), false);
 					if(is_oid($cfgform) && $this->can("view", $cfgform))
 					{
 						$cfgform_i->get_cfg_groups($cfgform);
 					}

 					if (isset($cfgform_i->cfg_groups[automatweb::$request->arg("group")]))
					{
						$GLOBALS["yah_end"].= " > " .$cfgform_i->cfg_groups[automatweb::$request->arg("group")]["caption"];
					}
 				}

				if (!empty($this->request["return_url"]))
				{
					$GLOBALS["yah_end"].= " > ".html::href(array(
						"url" => $_GET["return_url"],
						"caption" => t("Tagasi"),
					));
				}
			}
		}

		if ("cfg_embed" == $this->awcb_display_mode)
		{
			$hide_tabs = true;
		}

		$orb_class = get_class($this->orb_class);

		if (is_oid(aw_global_get("class")))
		{
			$orb_class = aw_global_get("class");
		}

		if (!$hide_tabs)
		{
			$groupinfo = $this->get_visible_groups();
			foreach($groupinfo as $key => $val)
			{
				if ($this->id)
				{
					$link_args->set_at("group",$key);
					if (aw_global_get("section"))
					{
						$link_args->set_at("section",aw_global_get("section"));
					};
					if (!empty($_REQUEST["cb_part"]))
					{
						$link_args->set_at("cb_part",$_REQUEST["cb_part"]);
					};
					if (isset($this->embedded))
					{
						$link_args->set_at("_alias",get_class($this));
					};
					$link = $this->mk_my_orb($orb_action,$link_args->get(),$orb_class);
					if (is_numeric($orb_class))
					{
						$link = aw_url_change_var("class",$orb_class,$link);
					};
				}
				elseif (!empty($this->use_form) && $this->use_form != "new")
				{
					$link_args->set_at("group",$key);
					$link = $this->mk_my_orb($orb_action,$link_args->get(),$orb_class);
				}
				else
				{
					$link = !empty($val["active"]) ? "#" : "";
				}

				if (!empty($val["set_link"]))
				{
					$link = $val["set_link"];
				}

				if (!empty($_GET["in_popup"]))
				{
					$link = aw_url_change_var("in_popup", 1, $link);
				}

				$tabinfo = array(
					"link" => &$link,
					"caption" => &$val["caption"],
					"id" => $key,
					"obj_inst" => $this->obj_inst,
					"request" => $this->request,
					"activegroup" => $activegroup,
					"tabgroup" => &$val["tabgroup"],
					"new" => isset($this->new) ? $this->new : false,
					"classinfo" => &$this->classinfo,
				);

				if (!empty($val["set_link_target"]))
				{
					$tabinfo["target"] = $val["set_link_target"];
				}

				$res = true;

				if ($tab_callback)
				{
					// mod_tab can block the display of a tab
					$res = $this->inst->callback_mod_tab($tabinfo);
				}

				// XXX: temporary hack to hide general tab
				if ($key == "general" && isset($this->hide_general))
				{
					$res = false;
				}

				if ($res !== false)
				{
					$active = !empty($val["active"]);
					// why exactly is that thing good?
					if (isset($this->no_active_tab))
					{
						$active = false;
					}

					$this->cli->add_tab(array(
						"id" => $tabinfo["id"],
						"encoding" => isset($val["encoding"]) ? $val["encoding"] : null,
						"level" => $val["level"],
						"parent" => isset($val["parent"]) ? $val["parent"] : false,
						"link" => $tabinfo["link"],
						"caption" => $tabinfo["caption"],
						"active" => $active,
						"tabgroup" => $val["tabgroup"],
						"target" => isset($tabinfo["target"]) ? $tabinfo["target"] : null,
					));
				}
			}
		}

		if (!empty($this->classinfo["disable_relationmgr"]))
		{
			$this->classinfo["relationmgr"] = false;
		}

		// temporary workaround to hide relationmgr
		if (isset($this->hide_relationmgr))
		{
			$this->classinfo["relationmgr"] = false;
		}

		if(method_exists($this->inst, "callback_just_saved_msg"))
		{
			$this->show_success_text($this->inst->callback_just_saved_msg($this->request));
		}

		$cli_args = array(
			"raw_output" => isset($this->raw_output) ? $this->raw_output : false,
			"content" => $args["content"],
			"confirm_save_data" => (isset($this->classinfo["confirm_save_data"]) || isset($GLOBALS["confirm_save_data"]) || !empty($this->groupinfo[$this->use_group]["confirm_save_data"]))
		);

		if(!empty($GLOBALS["view_property"]))
		{
			$cli_args["element_only"] = 1;
		}

		if($this->form_only)
		{
			$cli_args["form_only"] = true;
		}

		if ($this->cfgform_obj)
		{
			$cli_args["awcb_cfgform"] = $this->cfgform_obj;
		}

		$ui_messages = aw_global_get("awcb__global_ui_messages");
		if (strlen($ui_messages) > 0 and empty($this->no_form))
		{
			$ui_messages = unserialize($ui_messages);
			if (is_array($ui_messages))
			{
				$neutral_msgs = array();
				$positive_msgs = array();
				$error_msgs = array();
				foreach ($ui_messages as $text => $class)
				{
					if ("OK" === $class)
					{
						$positive_msgs[$text] = $class;
					}
					elseif ("ERROR" === $class)
					{
						$error_msgs[$text] = $class;
					}
					elseif ("" === $class)
					{
						$neutral_msgs[$text] = $class;
					}
				}

				foreach ($error_msgs as $text => $class)
				{
					$this->cli->push_msg($text, "ERROR");
				}
				foreach ($positive_msgs as $text => $class)
				{
					$this->cli->push_msg($text, "OK");
				}
				foreach ($neutral_msgs as $text => $class)
				{
					$this->cli->push_msg($text, "");
				}
			}
			aw_session_del("awcb__global_ui_messages");
		}

		$content = $this->cli->get_result($cli_args);
		return $content;
	}

	function make_tabscb_yah($ru)
	{
		$ret = "";
		$parsed_url = parse_url($ru);
		parse_str($parsed_url["query"], $output);
		if(is_oid($output["id"]) && $this->can("view" , $output["id"]))
		{
			$pa_obj = obj($output["id"]);
			$ret.= " > ".html::href(array(
				"url" => $ru,
				"caption" => $pa_obj->name(),
			));
			if($output["group"])
			{
				$cfgform_i = get_instance(CL_CFGFORM);
				$cfgform = $pa_obj->meta("cfgform_id");
				$cfgform_i->cff_init_from_class($pa_obj, $pa_obj->class_id(), false);
				if(is_oid($cfgform) && $this->can("view", $cfgform))
				{
					$cfgform_i->get_cfg_groups($cfgform);
				}

				$ret.= " > ".html::href(array(
					"url" => $ru,
					"caption" => $cfgform_i->cfg_groups[$output["group"]]["caption"],
				));
			}
		}
		if($output["return_url"])
		{
			$ret.=  $this->make_tabscb_yah($output["return_url"]).$ret;
		}

		return $ret;
	}

	////
	// !Returns a list of properties for generating an output
	// or saving data.
	// DEPRECATED!!
	function get_active_properties($args = array())
	{
		$no_group = !empty($args["all"]) ? $args["all"] : false;

		$this->get_all_properties(array(
			"classonly" => isset($args["classonly"]) ? $args["classonly"] : "",
			"content" => isset($args["content"]) ? $args["content"] : "",
			"rel" => isset($args["rel"]) ? $args["rel"] : "",
			"type" => isset($args["type"]) ? $args["type"] : "",
			"form" => isset($args["form"]) ? $args["form"] : "",
		));

		// figure out which group is active
		// it the group argument is a defined group, use that
		if (isset($this->action))
		{
			$use_group = $this->action;
		}
		elseif ( $args["group"] && !empty($this->groupinfo[$args["group"]]) )
		{
			$use_group = $args["group"];
		}
		else
		{
			// otherwise try to figure out whether any of the groups
			// has been set to default, if so, use it
			foreach($this->groupinfo as $gkey => $ginfo)
			{
				if (isset($ginfo["default"]))
				{
					$use_group = $gkey;
				}
			}
		}

		if (empty($this->id))
		{
			$use_group = "general";
		}


		// and if nothing suitable was found, use the first group from the list
		if (empty($use_group))
		{
			reset($this->groupinfo);
			list($use_group,) = each($this->groupinfo);
		}

		// this does something with second level groups
		if (isset($this->grp_children[$use_group]))
		{
			list(,$use_group) = each($this->grp_children[$use_group]);
		}


		if (!empty($this->groupinfo[$use_group]["parent"]) && isset($this->groupinfo[$this->groupinfo[$use_group]["parent"]]))
		{
			$sub_group = $use_group;
			$use_group = $this->groupinfo[$use_group]["parent"];
		}


		$this->activegroup = $use_group;
		$this->subgroup = $sub_group;

		// now I know the group
		$property_list = array();

		$retval = array();

		foreach($this->all_props as $key => $val)
		{
			// multiple groups for properties are supported too
			// no_group needs to return all properties
			if ($no_group === false)
			{
				$_tgr = new aw_array($val["group"]);
				foreach($_tgr->get() as $_grp)
				{
					$tmp = $val;
					if (isset($sub_group) && $_grp == $sub_group)
					{
						$tmp["group"] = $this->activegroup;
						$property_list[$key] = $tmp;
					}
					elseif (isset($sub_group) && $_grp == $this->activegroup && $sub_group == $this->grp_children[$this->activegroup][0])
					{
						// remap to the first child group
						$property_list[$key] = $tmp;
					}
					elseif (empty($sub_group) && $_grp == $this->activegroup)
					{
						$property_list[$key] = $tmp;
					}
					elseif ($args["load_defaults"] && !empty($tmp["default"]))
					{
						// what exactly does this thing do?
						$tmp["group"] = $this->activegroup;
						$property_list[$key] = $tmp;
					}
				};
			}
			else
			{
				$property_list[$key] = $val;
			}
		}

		// I need to replace this with a better check if I want to be able
		// to use config forms in other situations besides editing objects

		foreach($property_list as $key => $val)
		{
			$property = $this->all_props[$key];

			// give it the default value to silence warnings
			$property["store"] = isset($property["store"]) ? $property["store"] : "";
			// it escapes me why a property would not have a type. but some do not. -- duke
			$property["type"] = isset($property["type"]) ? $property["type"] : "";


			if (isset($property_list[$key]["caption"]))
			{
				$property["caption"] = $property_list[$key]["caption"];
			}


			// properties with no group end up in default group
			if (isset($val["group"]))
			{
				if (is_array($val["group"]))
				{
					$in_groups = $val["group"];
				}
				else
				{
					$in_groups = array($val["group"]);
				};
			}
			else
			{
				$in_groups = array($this->default_group);
			}

			if ($no_group || (in_array($use_group,$in_groups)))
			{
				$retval[$key] = $property;
			}
		}

		return $retval;
	}

	////
	// !Load all properties for the current class
	// DEPRECATED!!!!
	function get_all_properties($args = array())
	{
		$filter = !empty($args["rel"]) ? array("rel" => 1) : "";

		if (isset($this->cfgform["meta"]["cfg_proplist"]))
		{
			// load a list of properties and groups in the config form
			$proplist = $this->cfgform["meta"]["cfg_proplist"];
			$grplist = $this->cfgform["meta"]["cfg_groups"];
		}

		$cfgu = new cfgutils();

		// content comes from the config form
		if (!empty($args["content"]))
		{
			$tmp = $cfgu->parse_cfgform(array(
				"content" => $args["content"],
			));
			$_all_props = $tmp[0];
		}
		else
		// this handles some embedding cases
		if (!empty($args["classonly"]))
		{
			$_all_props = $cfgu->load_class_properties(array(
				"clid" => $this->clid,
			));
		}
		// and this handles the generic cases
		else
		{
			if (!empty($args["form"]))
			{
				$filter["form"] = $args["form"];
			};

			$_all_props = $cfgu->load_properties(array(
				"file" => empty($this->clid) ? $this->clfile : "",
				"clid" => $this->clid,
				"filter" => $filter,
			));

		};

		if (!is_array($this->classinfo))
		{
			$this->classinfo = array();
		};

		$clif = new aw_array($cfgu->get_classinfo());
		$this->classinfo = $this->classinfo + $clif->get();
		$this->relinfo = $cfgu->get_relinfo();
		$this->forminfo = $cfgu->get_forminfo();

		// this comes from the forum thingie
		if (is_array($this->classconfig))
		{
			$this->classinfo = array_merge($this->classinfo,$this->classconfig);
		};


		$group_el_cnt = $this->all_props = array();

		// use the group list defined in the config form, if we are indeed using a config form
		if (!isset($grplist) or !is_array($grplist))
		{
			$grplist = $cfgu->get_groupinfo();
		}

		$this->grp_children = array();

		// I need a hook somewhere to add those dynamic properties

		foreach($grplist as $key => $val)
		{
			// don't even try that
			if (!empty($val["parent"]) && $val["parent"] != $key)
			{
				$this->grp_children[$val["parent"]][] = $key;
			};

			// first default group is used
			if (isset($val["default"]) && empty($this->default_group))
			{
				$this->default_group = $key;
			};
		}


		$tmp = $this->cfgform_obj ? $proplist : $_all_props;
		foreach($tmp as $k => $val)
		{
			// if a config form is loaded, then ignore stuff that isn't
			// defined in there. I really shouldn't cause any problems
			// with well working code.
			if (!empty($args["type"]) && $_all_props[$val["name"]]["type"] != $args["type"])
			{
				continue;
			}

			if ($this->cfgform_obj)
			{
				// we can have as many relpickers as we want
				if ($val["type"] === "relpicker")
				{
				}
				// but for other element types we ignore things that
				// are not defined by the class
				else if (empty($_all_props[$val["name"]]))
				{
					continue;
				}
			}

			// override original property definitions with those in config form
			$orig = $val;
			if ($this->cfgform_obj)
			{
				$val = array_merge($_all_props[$k],$val);
				// use the default caption, if the one in config form
				// is empty. oh, and for consistency, I should do the
				// same when I save the config form
				if (empty($val["caption"]))
				{
					$val["caption"] = $_all_props[$k]["caption"];
				};

				// reset the richtext attribute, if it was disabled in the config form
				if (($_all_props[$k]["type"] === "textarea") && (empty($orig["richtext"])))
				{
					unset($val["richtext"]);
				};

				$allow_rte = $this->classinfo(array(
					"name" => "allow_rte",
				));

				if ($allow_rte < 1)
				{
					unset($val["richtext"]);
				};
			}

			// if it is a translated object, then don't show properties that can't be translated
			if ($this->is_translated && $val["trans"] != 1 && $val["name"] !== "is_translated")
			{
				continue;
			};

			if (!$this->is_translated && $val["name"] === "is_translated")
			{
				continue;
			}

			$argblock = array(
				"id" => isset($this->id) ? $this->id : "",
			);

			// generated elements count as one for this purpose
			if (isset($val["group"]))
			{
				$_grplist = is_array($val["group"]) ? $val["group"] : explode(",",$val["group"]);
				foreach($_grplist as $_grp)
				{
					if (isset($group_el_cnt[$_grp]))
					{
						$group_el_cnt[$_grp]++;
					}
					else
					{
						// subgroups count as children of the parent group as well
						if (isset($grplist[$_grp]["parent"]))
						{
							$group_el_cnt[$grplist[$_grp]["parent"]] = 1;
						}
						$group_el_cnt[$_grp] = 1;
					}
				}
			}

			if (isset($val["type"]) && isset($val["generator"]) && ($val["type"] == "generated") && method_exists($this->inst,$val["generator"]))
			{
				$meth = $val["generator"];
				$vx = new aw_array($this->inst->$meth($argblock));
				foreach($vx->get() as $vxk => $vxv)
				{
					if (empty($vxv["group"]))
					{
						$vxv["group"] = $val["group"];
					};
					$this->all_props[$vxk] = $vxv;
				}
			}
			else
			{
				$this->all_props[$k] = $val;
			}
		}


		$grpinfo = array();

		if (is_array($grplist))
		{
			foreach($grplist as $key => $val)
			{
				if (!empty($args["type"]) || in_array($key,array_keys($group_el_cnt)))
				{
					// skip the group, if it is not listed in the config form object
					if ($this->cfgform_obj && empty($grplist[$key]) )
					{
						continue;
					}
					else
					{
						// grplist comes from CL_CFGFORM and can be used
						// to override the default settings for a group

						// XX: add a list of settings that can be overrided,
						// allowing everything is probably not a good idea
						if (is_array($grplist) && isset($grplist[$key]))
						{
							$val = array_merge($val,$grplist[$key]);
						};

						$grpinfo[$key] = $val;

					};
				};
			};
		};

		$this->groupinfo = $grpinfo;
		$this->tableinfo = $cfgu->get_opt("tableinfo");

		if (is_object($this->inst))
		{
			$this->inst->all_props = $this->all_props;
		}

		return $this->all_props;
	}

	function convert_element(&$val)
	{
		// no type? get out then
		if (empty($val["type"]))
		{
			return false;
		}

		// so I can access this later
		$val["orig_type"] = $val["type"];

		if ($this->view && empty($val["view_element"]))
		{
			if ($val["type"] === "date_select")
			{
				if ($val["save_format"] === "iso8601")
				{
					list($y, $m, $d) = explode("-", $val["value"]);
					if ($y == 0)
					{
						$val["value"] = "";
					}
					else
					{
						$val["value"] = get_lc_date(mktime(0,0,0, $m, $d, $y));
					}
				}
				else
				if ($val["value"] == -1 || $val["value"] === "")
				{
					$val["value"] = "";
				}
				else
				{
					$val["value"] = get_lc_date($val["value"]);
				}
			}
			elseif ("datepicker" === $val["type"])
			{
				$val["value"] = get_lc_date($val["value"]);
			}
			$val["type"] = "text";
		}

		if (
			$this->view == 1
			&& isset($val["orig_type"]) && $val["orig_type"] === "select"
			&& isset($val["value"]) && is_scalar($val["value"])
			&& isset($val["options"][$val["value"]]) && $val["options"][$val["value"]] != ""
		)
		{
			$val["value"] = $val["options"][$val["value"]];
		}

		if($val["type"] === "layout")
		{
			$val["group"] = $this->use_group;
			$val["type"] = $val["rtype"];
			$this->layoutinfo[$val["name"]] = $val;
			unset($val);
		}
		// XXX: move get_html calls out of here, they really do not belong
		elseif (($val["type"] === "toolbar") && is_object($val["vcl_inst"]))
		{
			$val["value"] = $val["vcl_inst"]->get_toolbar();
		}
		elseif (($val["type"] === "relmanager") && is_object($val["vcl_inst"]))
		{
			$val["value"] = $val["vcl_inst"]->get_html();
		}
		elseif (($val["type"] === "releditor") && is_object($val["vcl_inst"]))
		{
			$val["value"] = $val["vcl_inst"]->get_html();
		}
		elseif (($val["type"] === "calendar") && is_object($val["vcl_inst"]))
		{
			$val["value"] = $val["vcl_inst"]->get_html();
		}
		elseif (($val["type"] === "range") && is_object($val["vcl_inst"]))
		{
			$val["value"] = $val["vcl_inst"]->get_html();
		}
		elseif(($val["type"] === "google_chart") && is_object($val["vcl_inst"]))
		{
			$val["value"] = $val["vcl_inst"]->get_html();
		}
		elseif (empty($val["value"]) && ($val["type"] === "aliasmgr") && isset($this->id))
		{
			$link = $this->mk_my_orb(
				"disp_relmgr",
				array(
					"group" => "relationmgr",
					"id" => $this->obj_inst->brother_of(),
					"no_op" => 1
				),
				"relationmgr",
				false,
				true
			);
			$val["value"] = "<iframe width='100%' name='aliasmgr' height='800' frameborder='0' src='$link'></iframe>";
			$val["no_caption"] = 1;
		}
		elseif ($val["type"] === "form")
		{
			$filter = array("form" => $val["sform"]);
			$cfgu = new cfgutils();
			$_all_props = $cfgu->load_properties(array(
				"file" => basename($val["sclass"]),
				"filter" => $filter,
			));
			// and how I get the class_instance?
			$clx_name = $val["sclass"];
			//$clx_name = "crm/" . $val["sclass"];
			$clx_inst = get_instance($clx_name);
			$clx_inst->orb_class = $clx_name;
			$clx_inst->init_class_base();
			$forminfo = $cfgu->get_forminfo();
			$form_onload = $forminfo[$val["sform"]]["onload"];
			if (isset($form_onload) && is_callable(array($clx_inst,$form_onload)))
			{
				$clx_inst->$form_onload(array());
			};
			$clx_inst->request = $_REQUEST[$val["name"]];

			$xprops = $clx_inst->parse_properties(array(
				"properties" => $_all_props,
				"name_prefix" => $val["name"],
			));
			$val = array();
			foreach($xprops as $rkey => $rprop)
			{
				$rprop["emb"] = 1;
				$val[$rkey] = $rprop;
			}
		}
	}

	////
	// !Figures out the value for property
	function get_value(&$property)
	{
		// cb_values comes from session and is set, if the previous process_request
		// run encounterend any PROP_ERRORS, this takes care of displaying the
		// error messages in correct places

		//if (is_array($this->cb_values) && !empty($this->cb_values[$property["name"]]["value"]))
		if (is_array($this->cb_values))
		{
			if (!empty($this->cb_values[$property["name"]]["value"]))
			{
				if ($property["type"] === "date_select" || $property["type"] === "datetime_select")
				{
					 $property["value"] = date_edit::get_timestamp($this->cb_values[$property["name"]]["value"]);
				}
				elseif ($property["type"] === "datepicker")
				{
					 $property["value"] = datepicker::get_timestamp($this->cb_values[$property["name"]]["value"]);
				}
				else
				{
					$property["value"] = $this->cb_values[$property["name"]]["value"];
				}
			}

			if (!empty($this->cb_values[$property["name"]]["error"]))
			{
				$property["error"] = $this->cb_values[$property["name"]]["error"];
			}
			return;
		}

		$nm = isset($property["name"]) ? $property["name"] : "";

		try
		{
			$property_value_from_obj = $this->obj_inst->prop($nm);
		}
		catch (Exception $e)
		{
			$property_value_from_obj = null;
			$property["error"] = t("Viga v&auml;&auml;rtuse lugemisel");
			trigger_error("Caught exception " . get_class($e) . " while reading property '{$nm}' from '" . $this->obj_inst->id() . "'. Thrown in '" . $e->getFile() . "' on line " . $e->getLine() . ": " . $e->getMessage(), E_USER_WARNING);
		}

		// if this is a new object and the property has a default value, use it
		if (empty($this->id) && isset($property["default"]))
		{
			$property["value"] = $property["default"];
		}
		else
		// current time for datetime_select properties for new objects
		// XXX: for now this->use_form is empty for add/change forms, this
		// will probably change in the future
		if (empty($this->id) && empty($this->use_form) && ($property["type"] === "datetime_select" || $property["type"] === "date_select") && (!isset($property["no_default"]) || !$property["no_default"]) && empty($property["value"]))
		{
			$property["value"] = time();
		}
		else
		// this values thingie is a hack
		if (isset($this->values) && is_array($this->values))
		{
			if (isset($this->values[$property["name"]]))
			{
				$property["value"] = $this->values[$property["name"]];
			}
		}
		elseif (
			empty($property["emb"]) &&
			is_object($this->obj_inst) &&
			(!isset($property["store"]) || $property["store"] != "no") &&
			empty($property["value"]) &&
			$property_value_from_obj != NULL )
		{
			// I need to implement this in storage .. so that $obj->prop('blag')
			// gives the correct result .. all connections of that type
			if ($this->view && empty($property["view_element"]))
			{
				$property["value"] = create_email_links($this->obj_inst->prop_str($property["name"]));
				if (strpos($property["value"], "\n") !== false && strpos($property["value"], "<br") === false)
				{
					$property["value"] = nl2br($property["value"]);
				}
			}
			else
			{
				$property["value"] = $property_value_from_obj;
			}
		}

		if (isset($property["method"]) && $property["method"] === "bitmask")
		{
			if (!isset($property["value"]))
			{
				$property["value"] = 0;
			}
			$property["value"] = $property["value"] & $property["ch_value"];
		}
	}

	function process_view_controllers(&$properties, $controllers, $argblock)
	{
		$view_controller_inst = get_instance(CL_CFG_VIEW_CONTROLLER);
		foreach ($controllers as $key => $value)
		{
			$parse_props = array($key);
			$op = $properties[$key]["otherprops"];
			if(is_array($op))
			{
				$parse_props = array_merge($parse_props, $op);
			}
			if($value)
			{
				$val = is_array($value) ? $value : array($value);
				foreach($val as $value)
				{

					foreach($parse_props as $prop)
					{
						// &$properties[$key], $value, $argblock
						// $prop, $controller_oid, $arr
						$retval = $view_controller_inst->check_property($properties[$prop], $value, $argblock);
						/*
						$retval = $view_controller_inst->check_property(array(
							"prop" => &$properties[$key],
							"controller_oid" => $value,
							"arr" => $argblock,
						));
						*/
						if($retval == PROP_IGNORE)
						{
							unset($properties[$prop]);
						}
					}
				}
			}
		}
	}

	/**
		@attrib name=parse_properties params=name
		@param properties
		@param classinfo
		@param obj_inst
		@param target_obj
		@param name_prefix
		@param object_type_id
	**/
	function parse_properties($args = array())
	{
		$properties = &$args["properties"];

		if (!is_array($properties))
		{
			return false;
		}

		if(isset($args["classinfo"]) && is_array($args["classinfo"]))
		{
			foreach($args["classinfo"] as $k => $val)
			{
				$this->classinfo[$k] = $val;
			}
		}

		if (isset($args["obj_inst"]) && is_object($args["obj_inst"]))
		{
			$this->obj_inst = $args["obj_inst"];
			$this->id = $this->obj_inst->id();
		}

		if (!empty($args["name_prefix"]))
		{
			$this->name_prefix = $args["name_prefix"];
			$this->inst->name_prefix = $args["name_prefix"];
		}

		if (!is_array($this->relinfo))
		{
			$this->load_defaults();
		}

		// only relation object uses this. But hey, if the relation object
		// thingie is now done differently then I do not need this, yees?
		if (isset($args["target_obj"]))
		{
			$this->target_obj = $args["target_obj"];
		}
		else
		{
			$this->target_obj = $this->id;
		};

		// I really doubt that get_property appears out of blue
		// while we are generating the output form
		$callback = method_exists($this->inst,"get_property");

		// get_XXX (where XXX is the name of the property) is also supported
		$class_methods = get_class_methods($this->inst);

		$resprops = array();

		$argblock = array(
			"request" => isset($this->request) ? $this->request : "",
			"obj_inst" => $this->obj_inst,
			"groupinfo" => &$this->groupinfo,
			"new" => $this->new,
			"view" => $this->view,
			"name_prefix" => isset($args["name_prefix"]) ? $args["name_prefix"] : null,
		);

		$this->cfgu = new cfgutils();

		$remap_children = false;

		// how do I stop parsing of properties that _are_ already parsed?

		//

		$resprops = array();

		foreach($properties as $key => $val)
		{
			if (isset($val["callback"]) && method_exists($this->inst,$val["callback"]))
			{
				if ($this->new && !empty($val["editonly"]) || !$this->new && !empty($val["newonly"]))
				{
					continue;
				}

				$meth = $val["callback"];
				$argblock["prop"] = &$val;
				$vx = $this->inst->$meth($argblock);
				if (is_array($vx))
				{
					foreach($vx as $ekey => $eval)
					{
						$this->convert_element($eval);
						if($eval["orig_type"] != "layout")
						{
							$resprops[$ekey] = $eval;
						}
					}
				}
			}
			else
			{
				$resprops[$key] = $val;
			}
		}

		$properties = $resprops;
		$resprops = array();

		// First we resolve all callback properties, so that get_property calls will
		// be valid for those as well
		$has_rte = false;
		$skip_types = array("button", "submit", "reset");

		foreach($properties as $key => $val)
		{
			// if the action is view and there is set that no buttons should be shown,
			// then skip the buttons
			if($this->view && $this->no_buttons && in_array($val["type"], $skip_types))
			{
				continue;
			}
			if (!empty($val["editonly"]) && empty($this->id))
			{
				continue;
			};

			if ( isset($val["newonly"]) && !empty($this->id))
			{
				// and this as well
				continue;
			};

			// eventually all VCL components will have to implement their
			// own init_vcl_property method
			if (aw_ini_isset("class_base.vcl_register.{$val["type"]}") && empty($val["_parsed"]) && (!isset($val["vcl_inst"]) || !is_object($val["vcl_inst"])) && !aw_ini_get("class_base.vcl_register.{$val["type"]}.delayed"))
			{
				$vcl_class = aw_ini_get("class_base.vcl_register.{$val["type"]}.class");
				$ot = new $vcl_class();

				if (is_callable(array($ot,"init_vcl_property")))
				{
					$res = $ot->init_vcl_property(array(
						"prop" => &$val,
						"object_type_id" => isset($args["object_type_id"]) ? $args["object_type_id"] : null,
						"new" => $this->new,
						"request" => $this->request,
						"property" => &$val,
						"id" => $this->id,
						"clid" => $this->clid,
						"obj_inst" => $this->obj_inst,
						"relinfo" => $this->relinfo,
						"view" => $this->view,
					));

					if (is_array($res))
					{
						foreach($res as $rkey => $rval)
						{
							$this->convert_element($rval);
							$resprops[$rkey] = $rval;
							$resprops[$rkey]["capt_ord"] = isset($val["capt_ord"]) ? $val["capt_ord"] : "";
							$resprops[$rkey]["wf_capt_ord"] = isset($val["wf_capt_ord"]) ? $val["wf_capt_ord"] : "";
						}
					}
				}

				if (is_callable(array($ot, "callback_mod_reforb")))
				{
					$this->_do_call_vcl_mod_reforbs[] = array($ot, "callback_mod_reforb");
				}
			}
			elseif ($val["type"] === "form")
			{
				// I need a list of those bloody properties, eh?
				// how?
				$filter = array("form" => $val["sform"]);
				$cfgu = new cfgutils();

				// oh, but that is wrong .. I need to query the class somehow
				// and not read properties directly. They properties _need_
				// to come through that classes get_property and whatever else
				// calls

				// so .. I need to load the class instance, invoke get_property
				// calls on it .. and then get the results and inject those
				// into my output stream. Uh, that's going to be hard.


				$_all_props = $cfgu->load_properties(array(
					"file" => basename($val["sclass"]),
					"filter" => $filter,
				));

				// and how I get the class_instance?
				$clx_name = $val["sclass"];
				$clx_inst = new $clx_name();

				$clx_inst->orb_class = $clx_name;
				$clx_inst->init_class_base();

				$forminfo = $cfgu->get_forminfo();
				$form_onload = $forminfo[$val["sform"]]["onload"];
				if (isset($form_onload) && is_callable(array($clx_inst,$form_onload)))
				{
					$clx_inst->$form_onload(array());
				};

				// this needs to change the form method, urk, urk
				//$clx_inst->request = $this->request[$val["name"]];
				$clx_inst->request = $_REQUEST[$val["name"]];

				$xprops = $clx_inst->parse_properties(array(
					"properties" => $_all_props,
					"name_prefix" => $val["name"],
				));

				foreach($xprops as $rkey => $rprop)
				{
					$rprop["emb"] = 1;
					$resprops[$rkey] = $rprop;
					$resprops[$rkey]["capt_ord"] = $val["capt_ord"];
					$resprops[$rkey]["wf_capt_ord"] = $val["wf_capt_ord"];
				};

			}
			else
			{
				$resprops[$key] = $val;
			}

			if (isset($val["richtext"]) && 1 == $val["richtext"])
			{
				$has_rte = true;
			};
		}
		if ($this->classinfo(array("name" => "allow_rte")) < 1)
		{
			$has_rte = false;
		}
		else
		{
			$has_rte = true;
		};


		if ($this->no_rte)
		{
			$has_rte = false;
		};


		$properties = $resprops;
		$resprops = array();

		// need to cycle over the property nodes, do replacements
		// where needed and then cycle over the result and generate
		// the output

		if (isset($properties["needs_translation"]) && $this->new )
		{
			$properties["needs_translation"]["value"] = 2;
		}

		foreach($properties as $key => $val)
		{
			if (isset($val["name"]) && $val["name"] === "tabpanel" && $this->view)
			{
				continue;
			};

			if (isset($val["name"]) && $val["name"] === "name" && !empty($this->classinfo["no_name"]))
			{
				continue;
			};


			// XXX: need to get rid of that "text" index
			if (isset($val["name"]) && $val["name"] === "status" && !empty($this->classinfo["no_status"]))
			{
				continue;
			};

			if (isset($val["name"]) && $val["name"] === "comment" && !empty($this->classinfo["no_comment"]))
			{
				continue;
			}

			if ($val["type"] === "textarea" && !$has_rte)
			{
				unset($val["richtext"]);
			}

			if (isset($val["emb"]) && $val["emb"] == 1)
			{
				// embedded properties have already passed through parse_properties
				// and there is no need to do that again
				$resprops[$key] = $val;
				continue;
			}

			if ($val["type"] === "toolbar")
			{
				if ($this->layout_mode === "fixed_toolbar")
				{
					$val["vcl_inst"]->set_opt("button_target","contentarea");
				}
			}
			elseif (($val["type"] === "relmanager") && !is_object($val["vcl_inst"]))
			{
				$val["vcl_inst"] = new relmanager();
			}
			elseif ( ($val['type'] === 'range') && !is_object($val['vcl_inst']) )
			{
				$val['vcl_inst'] = new range();
			}
			elseif (($val["type"] === "calendar") && (!isset($val["vcl_inst"]) or !is_object($val["vcl_inst"])))
			{
				$val["vcl_inst"] = new vcalendar();
			}

			if (!empty($val["parent"]) && empty($this->layoutinfo[$val["parent"]]))
			{
				$remap_children = true;
			}

			$name = isset($val["name"]) ? $val["name"] : "";
			if (is_array($val) && $val["type"] !== "callback" && $val["type"] !== "submit")
			{
				$this->get_value($val);
				// fuck me plenty
				if ($this->view && isset($val["orig_type"]) && $val["orig_type"] === "select" && isset($val["value"]) && is_oid($val["value"]) && !$val["view_element"])
				{
					if (!$this->can("view", $val["value"]))
					{
						$val["value"] = "";
					}
					else
					{
						$tmp = new object($val["value"]);
						$val["value"] = $tmp->name();
					}
				}
				elseif ($this->view && isset($val["orig_type"]) &&  $val["orig_type"] === "select" && isset($val["value"]) && is_array($val["value"]) && count($val["value"]) > 0 && !$val["view_element"])
				{
					$tmp_ol = new object_list(array("oid" => $val["value"]));
					$val["value"] = join(", ", $tmp_ol->names());
				}
			}


			$argblock["prop"] = &$val;

			if ($val["type"] === "select")
			{
				//$val["options"] = $this->make_keys($val["options"]);
			};

			if ( isset($val["editonly"]) && empty($this->id))
			{
				// this should be form depenent
				continue;
			}
			else
			if ($val["type"] === "aliasmgr" && empty($this->id))
			{
				// do not show alias manager if  no id
				// and this too
				continue;
			}
			else
			if ( isset($val["newonly"]) && !empty($this->id))
			{
				// and this as well
				continue;
			}

			$pname = isset($val["name"]) ? $val["name"] : "";
			$getter = "_get_" . $pname;
			$status = null;
			if ( !empty($this->classinfo['prop_cb']) && in_array($getter,$class_methods))
			{
				$status = $this->inst->$getter($argblock);
			}
			else
			// callbackiga saad muuta &uuml;he konkreetse omaduse sisu
			if ($callback)
			{
				$status = $this->inst->get_property($argblock);
			}

			$val["_parsed"] = 1;

			if ($this->cfg_debug && $status != PROP_OK)
			{
				print "status is " . $status . " for " . $val["name"] . "<br>";
			}

			if ($status === PROP_IGNORE)
			{
				// do nothing
			}
			elseif ($status === PROP_ERROR)
			{
				$val["type"] = "text";
				$val["value"] = empty($val["error"]) ? t("Viga") : "Viga: ".$val["error"];
				$resprops[$key] = $val;
			}
			elseif ($val["type"] === "hidden")
			{
				$resprops[$name] = $val;
			}
			else
			{
				if (aw_ini_isset("class_base.vcl_register.{$val["type"]}") and aw_ini_get("class_base.vcl_register.{$val["type"]}.delayed"))
				{
					$vcl_class = aw_ini_get("class_base.vcl_register.{$val["type"]}.class");
					$ot = new $vcl_class();
					if (is_callable(array($ot,"init_vcl_property")))
					{
						$res = $ot->init_vcl_property(array(
							"property" => &$val,
							"id" => $this->id,
							"clid" => $this->clid,
							"obj_inst" => $this->obj_inst,
							"relinfo" => $this->relinfo,
							"view" => $this->view,
							"request" => $this->request,
						));

						if (is_array($res))
						{
							foreach($res as $rkey => $rval)
							{
								$this->convert_element($rval);
								if(is_array(reset($rval)))
								{
									foreach($rval as $rkey2 => $rval2)
									{
										$resprops[$rkey2] = $rval2;
									}
								}
								else
								{
									$resprops[$rkey] = $rval;
								}

								$resprops[$rkey]["capt_ord"] = isset($val["capt_ord"]) ? $val["capt_ord"] : 0;
								$resprops[$rkey]["wf_capt_ord"] = isset($val["wf_capt_ord"]) ? $val["wf_capt_ord"] : 0;
							};
						};
					};
					continue;
				}

				if ($val["type"] === "relmanager")
				{
					$argblock["prop"]["clid"] = $this->relinfo[$val["reltype"]]["clid"];
					$val["vcl_inst"]->init_rel_manager($argblock);
				}

				if (!empty($val["orig_type"]) and aw_ini_isset("class_base.vcl_register.{$val["orig_type"]}.class") and isset($this->vcl_has_getter[$val["orig_type"]]))
				{
					$vcl_class = aw_ini_get("class_base.vcl_register.{$val["orig_type"]}.class");
					$ot = new $vcl_class();
					if (is_callable(array($ot,"get_vcl_property")))
					{
						$ot->get_vcl_property(array(
							"property" => &$val,
						));
						$resprops[$key] = $val;
					}
					continue;
				}

				if ($val["type"] === "releditor")
				{
					if (!isset($val["vcl_inst"]) || !is_object($val["vcl_inst"]))
					{
						$val["vcl_inst"] = new releditor();
					}

					$argblock["prop"] = &$val;
					$target_reltype = $val["reltype"];
					$argblock["prop"]["reltype"] = $this->relinfo[$target_reltype]["value"];
					$argblock["prop"]["clid"] = $this->relinfo[$target_reltype]["clid"];

					if (is_array($this->cb_values[$val["name"]]))
					{
						$argblock["cb_values"] = $this->cb_values[$val["name"]];
					}

					// init_rel_editor returns an array of properties to be embbeded
					$relres = $val["vcl_inst"]->init_rel_editor($argblock);
					$releditor_error_not_processed = true;
					if (is_array($relres))
					{
						foreach($relres as $rkey => $rval)
						{
							$this->convert_element($rval);

							if ($releditor_error_not_processed)
							{
								$releditor_error_not_processed = false;
								if (!empty($val["error"]))
								{
									$errprop_name = $val["name"] . "__aw_releditor_error_display";
									$errprop_rkey = $rkey . "__aw_releditor_error_display";
									$resprops[$errprop_rkey] = array(
										"name" => $errprop_rkey,
										"type" => "text",
										"class" => "awerror",//!!! teha normaalne. hetkel tegemata sest htmlclienti default.tpl-is on vigade elementidel mitte klass vaid dom id
										"caption" => t("Viga!"),
										"value" => "<span style=\"color:red\">{$val["error"]}</span>"///!!! teha formaat dom klassi abil
									) + $rval;
									$resprops[$errprop_rkey]["wf_capt_ord"] = isset($val["wf_capt_ord"]) ? $val["wf_capt_ord"] : "";
									$resprops[$errprop_name]["otherprops"][$errprop_rkey] = $errprop_rkey;
								}
							}

							$resprops[$rkey] = $rval;
							$resprops[$rkey]["wf_capt_ord"] = isset($val["wf_capt_ord"]) ? $val["wf_capt_ord"] : "";
							if ($resprops[$rkey]["type"] === "hidden")
							{
								unset($resprops[$rkey]["parent"]);
							}
							$resprops[$val["name"]]["otherprops"][$rkey] = $rkey;
						}
					}

					if (isset($val["vcl_inst"]) && is_callable(array($val["vcl_inst"], "callback_mod_reforb")))
					{
						$this->_do_call_vcl_mod_reforbs[] = array($val["vcl_inst"], "callback_mod_reforb");
					}
					continue;
				}

				if ($val["type"] === "toolbar")
				{
					if ($this->layout_mode === "fixed_toolbar")
					{
						//$this->groupinfo = $this->groupinfo();
						$no_rte = automatweb::$request->arg("no_rte");
						foreach($this->groupinfo as $grp_id => $grp_data)
						{
							// disable all other buttons besides the general when
							// adding a new object
							if ($this->use_mode == "new" && $grp_id != $this->active_group)
							{
								continue;
							};

							$has_props = false;
							// if group is empty, then don't show it.
							foreach($this->_cfg_props as $pn => $pd)
							{
								if ($pd["group"] == $grp_id)
								{
									$has_props = true;
								}
							}


							if (!$has_props)
							{
								continue;
							}

							$target = "contentarea";
							$cb_part = 1;
							if ($no_rte)
							{
								$target = "_self";
								$cb_part = null;
							};
							$val["vcl_inst"]->add_button(array(
								"name" => "grp_" . $grp_id,
								"img" => empty($grp_data["icon"]) ? "" : $grp_data["icon"] . ".gif",
								"tooltip" => $grp_data["caption"],
								"target" => $target,
								"url" => $this->mk_my_orb("change",array("id" => $this->id,"group" => $grp_id,"cb_part" => $cb_part)),
							));

						}
					}

					// if we are using rte, then add RTE buttons to the toolbar
					//if (1 == $this->has_feature("has_rte"))
					if (($has_rte || $this->no_rte == 1) && (!isset($val["vcl_inst"]->closed) || !$val["vcl_inst"]->closed) && (!isset($val["no_rte_button"]) || !$val["no_rte_button"]))
					{
						if($this->classinfo(array("name" => "allow_rte")) == 2)
						{
							$rte = get_instance("vcl/fck_editor");
							$rte->get_rte_toolbar(array(
								"toolbar" => &$val["vcl_inst"],
								"no_rte" => $this->no_rte,
							));
						}
						else
						{
							$rte = get_instance("vcl/rte");
							$rte->get_rte_toolbar(array(
								"toolbar" => &$val["vcl_inst"],
								"target" => $this->layout_mode === "fixed_toolbar" ? "contentarea" : "",
								"no_rte" => $this->no_rte,
							));
						}
					};
				}

				// this deals with subitems .. what a sucky approach
				if (isset($val["items"]) && is_array($val["items"]) && sizeof($val["items"]) > 0)
				{
					$tmp = array();
					foreach($val["items"] as $item)
					{
						$this->convert_element($item);
						$tmp[] = $item;
					};
					$val["items"] = $tmp;
				};

				$this->convert_element($val);

				// hm, how the fuck can the name be empty anyway?
				if (empty($name))
				{
					$name = $key;
				}

				$resprops[$name] = $val;
			}
		}

		if (!is_object($this->cfgform_obj) && $this->clid == CL_DOCUMENT)
		{
			$this->load_cfgform(aw_ini_get("document.default_cfgform"));
		}

		if(is_object($this->cfgform_obj) && $controllers = $this->get_all_view_controllers($this->cfgform_id))
		{
			$this->process_view_controllers($resprops, $controllers, $argblock);
		}

		// if name_prefix given, prefixes all element names with the value
		// e.g. if name_prefix => "emb" and there is a property named comment,
		// then the result will be name => emb[comment], this simplifies
		// processing of embedded config forms
		if (!empty($args["name_prefix"]))
		{
			$tmp = $resprops;
			$resprops = array();
			$newname_index = array ();

			foreach($tmp as $key => $el)
			{
				$bracket = strpos($el["name"],"[");
				// I need to rename the parent attribute as well
				if ($bracket > 0)
				{
					$pre = substr($el["name"],0,$bracket);
					$aft = substr($el["name"],$bracket);
					$newname = $args["name_prefix"] . "[$pre]" . $aft;
				}
				else
				{
					$newname = $args["name_prefix"] . "[" . $el["name"] . "]";
					if (!empty($el["parent"]))
					{
						$el["parent"] = $args["name_prefix"] . "_" . $el["parent"];
					};
				};
				$newname_index[$el["name"]] = $newname;
				$el["name"] = $newname;
				// just to get an hopefully unique name ..
				$resprops[$args["name_prefix"] . "_" . $key] = $el;
			}

			### add prefixes to property names whose values will be parameters for ... (e.g. autocomplete source method)
			foreach ($tmp as $key => $el)
			{
				if (isset($el["autocomplete_params"]) && is_array($el["autocomplete_params"]))
				{
					foreach ($el["autocomplete_params"] as $param_key => $param_name)
					{
						$el["autocomplete_params"][$param_key] = $newname_index[$param_name];
					}
				}

				$resprops[$args["name_prefix"] . "_" . $key]["autocomplete_params"] = isset($el["autocomplete_params"]) ? $el["autocomplete_params"] : "";
			}
		}

		// now check whether any properties had parents. if so, remap them
		if ($remap_children)
		{
			$tmp = $resprops;
			foreach($tmp as $key => $prop)
			{
				if (!empty($prop["parent"]))
				{
					$resprops[$prop["parent"]]["items"][] = $prop;
					unset($resprops[$key]);
				}
			}
		}

		return $resprops;
	}


	/*function process_properties($arr)
	{
		// if name_prefix given, prefixes all element names with the value
		// e.g. if name_prefix => "emb" and there is a property named comment,
		// then the result will be name => emb[comment], this simplifies
		// processing of embedded config forms
		if ($arr["name_prefix"])
		{
			$tmp = $arr["properties"];
			$resprops = array();
			foreach($tmp as $key => $el)
			{
				$bracket = strpos($el["name"],"[");
				// I need to rename the parent attribute as well
				if ($bracket > 0)
				{
					$pre = substr($el["name"],0,$bracket);
					$aft = substr($el["name"],$bracket);
					$newname = $args["name_prefix"] . "[$pre]" . $aft;
				}
				else
				{
					$newname = $args["name_prefix"] . "[" . $el["name"] . "]";
					if (!empty($el["parent"]))
					{
						$el["parent"] = $args["name_prefix"] . "_" . $el["parent"];
					};
				};
				$el["name"] = $newname;
				// just to get an hopefully unique name ..
				$resprops[$args["name_prefix"] . "_" . $key] = $el;
			}
		}
		return $resprops;
	}*/

	/** _serialize replacement for class_base based objects
		@attrib name=ng_serialize params=name
		@param oid required type=int
		@returns
		@comment
	**/
	function _serialize($args = array())
	{
		$this->init_class_base();
		$this->id = $args["oid"];

		$realprops = $this->get_active_properties(array(
			"clfile" => $this->clfile,
			"all" => true,
		));

		$this->obj_inst = new object($this->id);
		$result = array();

		foreach($realprops as $key => $val)
		{
			$this->get_value($val);
			if (!empty($val["value"]) || $val["store"] !== "no")
			{
				if ($val["type"] === "fileupload" && is_readable($val["value"]))
				{
					$name = $val["name"];
					$src = $this->get_file(array(
						"file" => $val["value"],
					));
					$result[$name] = array(
						"type" => "imagetype",
						"contents" => base64_encode($src)
					);
				}
				else
				{
					$result[$val["name"]] = $val["value"];
				};
			};
		}


		// also add relations
		$obj = new object($this->id);
		$conns = $obj->connections_from();
		foreach($conns as $conn)
		{
			// I also need to the connection type (at least)
			$result["connections"][] = array(
				"to" => $conn->prop("to"),
				"reltype" => $conn->prop("reltype"),
			);
		};

		if (aw_global_get("__is_rpc_call"))
		{
			$result["class_id"] = $this->clid;
			$retval = $result;
		}
		else
		{
			$retval = isset($args["raw"]) ? $result : aw_serialize($result, SERIALIZE_NATIVE);
		};
		return $retval;
	}

	////
	// !_unserialize replacement for class_base based objects
	function _unserialize($args = array())
	{
		$raw = isset($args["raw"]) ? $args["raw"] : aw_unserialize($args["str"]);

		$this->init_class_base();

		// quoting thins here _seriosly_ fucks us over with binary data
		//$this->quote(&$raw);

		$this->process_data(array(
			"parent" => $args["parent"],
			"rawdata" => $raw,
			"is_paste" => true
		));

		foreach(safe_array($raw["connections"]) as $con)
		{
			$this->obj_inst->connect(array(
				"to" => $con["to"],
				"reltype" => $con["reltype"]
			));
		}

		return $this->id;
	}

	//This function returns all submit controllers current configform has.
	function get_all_controllers($config_id)
	{
		if (!$this->can("view", $config_id))
		{
			return false;
		}
		$obj = obj($config_id);
		return $obj->meta("controllers");
	}

	function get_all_view_controllers($config_id)
	{
		try
		{
			$obj = obj($config_id);
			return $obj->meta("view_controllers");
		}
		catch (awex_obj $e)
		{
			return false;
		}
	}

	////
	// !You give it a class id and a list of properties .. it performs a validation on all the data
	// and returns something eatable

	// this was not ment to be used from outside the class
	function validate_data($arr)
	{
		if (empty($arr["props"]))
		{
			if (is_oid($arr["cfgform_id"]) && $this->can("view", $arr["cfgform_id"]))
			{
				$cf = get_instance(CL_CFGFORM);
				$props = $cf->get_props_from_cfgform(array("id" => $arr["cfgform_id"]));
			}
			else
			{
				$props = $this->load_defaults(array(
					"clid" => $this->clid,
				));
			};
		}
		else
		{
			$props = &$arr["props"];
		};

		if (!$arr["cfgform_id"] && is_object($arr["obj_inst"]) && $arr["obj_inst"]->class_id() == CL_DOCUMENT && aw_ini_get("document.default_cfgform"))
		{
			$arr["cfgform_id"] = aw_ini_get("document.default_cfgform");
		}

		$controllers = array();
		if (is_oid($arr["cfgform_id"]) && $this->can("view", $arr["cfgform_id"]) )
		{
			$controller_inst = get_instance(CL_CFGCONTROLLER);
			$controllers = $this->get_all_controllers($arr["cfgform_id"]);
		}

		$res = array();

		if (!is_array($arr["request"]))
		{
			return $res;
		}

		foreach($props as $key => $tmp)
		{
			// skiping text controllers.. you can't save anything with them, aight? -- ahz
			// seems that i need to do that --dragut
			// seems it CANNOT be removed! --dragut
			if($tmp["type"] === "text")
			{
				continue;
			}

			$val = null;
			if (isset($arr["request"][$key]))
			{
				$val = $arr["request"][$key];

				if (!empty($tmp["required"]))
				{
					$check_val = $val;
					if (is_array($val))
					{
						if ("date_select" === $tmp["type"] or "datetime_select" === $tmp["type"])
						{
							$check_val = date_edit::get_timestamp($val);
						}
						if ("datepicker" === $tmp["type"])
						{
							$check_val = datepicker::get_timestamp($val);
						}
						else
						{
							$check_val = 1;
						}
					}

					if (strlen($check_val) < 1)
					{
						$res[$key] = array(
							"error" => t("See v&auml;li ei tohi olla t&uuml;hi!"),
						);
					}
				}
			}

			$prpdata = &$props[$key];
			if (isset($prpdata["validate"]) and "email" === $prpdata["validate"])
			{
				if (!is_email($val))
				{
					$res[$key] = array(
						"msg" => t("See pole korrektne e-posti aadress!"),
					);
				}
			}

			$rvs = array();
			if(isset($controllers[$key]))
			{
				$controller = is_array($controllers[$key]) ? $controllers[$key] : array($controllers[$key]);
				foreach($controller as $contr)
				{
					$controller_id = $contr;
					$prpdata["value"] = $val;
					$props[$key]["value"] = $val;
					// $controller_id, $args["id"], &$prpdata, &$arr["request"], $val, &$this->obj_inst
					// $controller_oid, $obj_id, &$prop, $request, $entry, $obj_inst
					if ($this->cfg_debug)
					{
						print "validating " . $prpdata["name"] . " against controller $controller_id<br>";
					};
					$controller_ret = $controller_inst->check_property($controller_id, $args["id"], $prpdata, $arr["request"], $val, $this->obj_inst);
					/*
					$controller_ret = $controller_inst->check_property(array(
						"controller_oid" => $controller_id,
						"obj_id" => $args["id"],
						"prop" => &$prpdata,
						"request" => &$arr["request"],
						"entry" => $val,
						"obj_inst" => &$this->obj_inst,
					));
					*/
					if ($controller_ret !== PROP_OK && is_oid($controller_id))
					{
						if ($this->cfg_debug)
						{
							print "validation failed!<br>";
						}

						$ctrl_obj = new object($controller_id);
						$errmsg = $ctrl_obj->trans_get_val("errmsg");
						if (empty($errmsg))
						{
							$errmsg = "Entry was blocked by a controller, but no error message is available";
						}
						$errmsg = str_replace("%caption", $prpdata["caption"], $errmsg);
						$rvs[] = $errmsg;
					}
				}

				if(!empty($rvs))
				{
					$res[$key]["msg"] = implode("<br />\n", $rvs);
				}
			}
		}
		return $res;
	}

	////
	// !Processes and saves form data
	function process_data($args = array())
	{
		$processing_status = $status = PROP_OK;
		$this->init_class_base();
		if (method_exists($this->inst,"callback_on_load"))
		{
			$this->inst->callback_on_load(array(
				"request" => $args,
			));
		}

		// and this of course should handle both creating new objects and updating existing ones

		$callback = method_exists($this->inst,"set_property");
		$class_methods = get_class_methods($this->inst);

		$new = false;
		$this->id = isset($args["id"]) ? $args["id"] : "";
		$group = isset($args["group"]) ? $args["group"] : "";

		// basically, if this is a new object, then I need to load all the properties
		// that have default values and add them to the bunch.

		// only create the object, if one of the tables used by the object
		// is the objects table

		// this object creation thingie should also only be defined in the forminfo

		if (empty($this->id) and !empty($args["parent"]) and is_oid($args["parent"]))
		{
			$parent = $args["parent"];
			$o = new object();
			$o->set_class_id($this->clid);
			$o->set_parent($parent);
			$o->set_status(isset($args["status"]) ? $args["status"] : object::STAT_ACTIVE);

			if (isset($args["period"]))
			{
				$o->set_period($args["period"]);
			}

			if (isset($args["rawdata"]["lang_id"]))
			{
				$lg = get_instance("languages");
				$o->set_lang($lg->get_langid($args["rawdata"]["lang_id"]));
			}

			$new = true;
		}

		$args["new"] = $this->new = $new;

		// the question is .. should I call set_property for those too?
		// and how do I load the stuff with defaults?
		if (!$new)
		{
			$this->obj_inst = new object($this->id);
		}
		else
		{
			$this->obj_inst = $o;
		}

		if (isset($args["_object_type"]) and $this->can("view", $args["_object_type"]))
		{
			$ot_obj = new object($args["_object_type"]);
			$this->obj_inst->set_meta("object_type",$args["_object_type"]);
		}

		$filter = array();
		$filter["clfile"] = $this->clfile;
		$filter["clid"] = $this->clid;
		$filter["group"] = $group;
		$filter["rel"] = $this->is_rel;
		$filter["ignore_layout"] = 1;

		if (!empty($args["cfgform"]))
		{
			$this->load_cfgform($args["cfgform"]);
			$filter["cfgform_id"] = $args["cfgform"];
		}
		else
		{
			$filter["cfgform_id"] = $this->obj_inst->meta("cfgform_id");
		}

		if (!empty($args["cb_existing_props_only"]) || !empty($args["is_paste"]))
		{
			$properties = $this->load_defaults();
		}
		else
		{
			$properties = $this->get_property_group($filter, $args);
		}

		if (isset($this->groupinfo[$group]["save"]) and $this->groupinfo[$group]["save"] === "no")
		{
			return true;
		}

		if ($this->new && is_array($this->_cfg_props))
		{
			foreach($this->_cfg_props as $key => $val)
			{
				if (!empty($val["default"]))
				{
					$this->obj_inst->set_prop($key,$val["default"]);
				}
			}
		}

		$errors = $this->validate_data(array(
			"request" => $args,
			"cfgform_id" => is_object($this->cfgform_obj) ? $this->cfgform_id : NULL,
			"props" => &$properties,
			"obj_inst" => $this->obj_inst
		));

		$pvalues = array();

		// this is here so I can keep things in the session
		$propvalues = array();
		$tmp = array();

		$this->stop_processing = false;
		// first, gather all the values.
		foreach($properties as $key => $property)
		{
			// XXX: temporary workaround to save only these properties which were present in the form
			// required by releditor
			if (!empty($args["cb_existing_props_only"]) && !isset($args[$key]))
			{
				continue;
			}

			//do not call set_property for edit_only properties when a new
			// object is created.
			if ($new && isset($property["editonly"]))
			{
				continue;
			}

			// and also skip the properties that are newonly
			if(!$new && isset($property["newonly"]))
			{
				continue;
			}

			// don't save or display un-translatable fields if we are editing a translated object
			if (!$this->is_translated && $property["name"] === "is_translated")
			{
				$xval = 1;
			}
			elseif ($this->is_translated && $property["trans"] != 1)
			{
				continue;
			}

			$name = $property["name"];
			$type = $property["type"];
			$xval = isset($args["rawdata"][$name]) ? $args["rawdata"][$name] : "";

			if (isset($property["value"]))
			{
				$xval = $property["value"];
			}

			if ($property["type"] === "checkbox")
			{
				// set value to 0 for unchecked checkboxes
				// well, shit, I need to figure out another way to do checkboxes
				// because if I do not have a group identifier with me, then
				// I might not be able to get a value for an item.

				// also .. what if there are readlonly attributes on some
				// fields .. those will then not have a value either and saving
				// such a form would case a disaster.
				$xval = (int)$xval;
			}

			if (isset($property["method"]) && $property["method"] === "bitmask" && empty($pvalues[$name]))
			{
				$pvalues[$name] = $this->obj_inst->prop($name);
			}

			$property["value"] = $xval;

			$tmp[$key] = $property;

			$propvalues[$property["name"]] = array(
				"value" => $property["value"],
			);
		}

		$realprops = $tmp;

		// now do the real job.
		foreach($realprops as $key => $property)
		{
			$name = $property["name"];
			$type = $property["type"];
			$argblock = array(
				"prop" => &$property,
				"request" => &$args["rawdata"],
				"new" => $new,
				"obj_inst" => $this->obj_inst,
				"relinfo" => $this->relinfo,
			);
			$processing_status = max($status, $processing_status);
			$status = PROP_OK;

			// give the class a possiblity to execute some action
			// while we are saving it.

			// for callback, the return status of the function decides
			// whether to save the data or not, so please, make sure
			// that your set_property returns PROP_OK for stuff
			// that you want to save
			$setter = "_set_" . $name;
			if ( isset($this->classinfo['prop_cb']) && ($this->classinfo['prop_cb'] == 1) && in_array($setter,$class_methods))
			{
				$status = $this->inst->$setter($argblock);
			}
			elseif ($callback)
			{
				$status = $this->inst->set_property($argblock);
				// XXX: what if one set_property changes a value and
				// other raises an error. Then we will have the original
				// value in the session. Is that a problem?
			}

			// what the duke is going on here? errors?
			if (isset($errors[$name]["msg"]))
			{
				$status = PROP_FATAL_ERROR;
				$argblock["prop"]["error"] = $errors[$name]["msg"];
			}

			if ($status == PROP_OK && !empty($property["datatype"]) && "int" === $property["datatype"])
			{
				$val = $property["value"];
				$val = str_replace(",",".",$val);
				if (empty($val))
				{
					$property["value"] = 0;
				}
				else if (is_numeric($val) === false)
				{
					$status = PROP_ERROR;
					$property["error"] = $property["caption"] . " - siia saab sisestada ainult arvu!";
				}
			}

			if (PROP_ERROR == $status)
			{
				$propvalues[$name]["error"] = $argblock["prop"]["error"];
				aw_session_set("cb_values",$propvalues);
				$this->cb_values = $propvalues;
				$status = PROP_IGNORE;
			}


			if (PROP_FATAL_ERROR == $status)
			{
				// so what the fuck do I do now?
				// I need to give back a sensible error message
				// and allow the user to correct the values in the form
				// I need to remember the values .. oh fuck, fuck, fuck, fuck

				// now register the variables in the session

				// I don't even want to think about serializers right about now.
				if (isset($args["prefix"]))
				{
					$prefix = $args["prefix"];
					$propvalues[$prefix][$name]["error"] = $argblock["prop"]["error"];
				}
				else
				{
					$propvalues[$name]["error"] = $argblock["prop"]["error"];
				}

				foreach($realprops as $k => $v)
				{
					$propvalues[$k]["edit_data"] = isset($args[$k."_data"]) ? $args[$k."_data"] : null;
				}

				$this->cb_values = $propvalues;
				aw_session_set("cb_values",$propvalues);
				$this->stop_processing = true;
			}

			// oh well, bail out then.
			if ($status !== PROP_OK)
			{
				continue;
			}

			// don't care about text elements
			// but i do --dragut
			// seems that it CANNOT be removed! --dragut
			// why? -- duke
			if ($type === "text")
			{
				continue;
			}

			if ($property["type"] === "releditor")
			{
				/// XXX: right now I can only have one type of rel editor
				$vcl_inst = new releditor();

				$argblock["prop"] = $property;
				$target_reltype = $property["reltype"];
				$argblock["prop"]["reltype"] = $this->relinfo[$target_reltype]["value"];
				$argblock["prop"]["clid"] = $this->relinfo[$target_reltype]["clid"];
				$res = $vcl_inst->process_releditor($argblock);

				if (PROP_ERROR === $res)
				{
					$propvalues[$name]["error"] = $argblock["prop"]["error"];
					aw_session_set("cb_values",$propvalues);
					$this->cb_values = $propvalues;
					return false;
				}

				if (isset($args[$name."_edit_data"]))
				{
					$propvalues[$name]["reledit_data"] = $args[$name."_edit_data"];
				}
			}

			// the current behaviour is to call set_property and not ever
			// call process_vcl_property if set_property returns false

			if (isset($property["type"]) and aw_ini_isset("class_base.vcl_register.{$property["type"]}"))
			{
				$vcl_class = aw_ini_get("class_base.vcl_register.{$property["type"]}.class");
				$ot = new $vcl_class();
				if (is_callable(array($ot,"process_vcl_property")))
				{
					$argblock["prop"] = &$property;
					$argblock["clid"] = $this->clid;
					$res = $ot->process_vcl_property($argblock);

					if (PROP_ERROR == $res || PROP_FATAL_ERROR == $res)
					{
						$propvalues[$name]["error"] = $argblock["prop"]["error"];
						aw_session_set("cb_values",$propvalues);
						$this->cb_values = $propvalues;
						return false;
					}

					if(PROP_FATAL_ERROR == $res)
					{
						$this->stop_processing = true;
					}
				}

				if (is_callable(array($ot, "callback_mod_retval")))
				{
					$this->_do_call_vcl_mod_retvals[] = array($ot, "callback_mod_retval");
				}
			}

			if (isset($property["store"]) && $property["store"] === "no")
			{
				continue;
			}

			// XXX: create a VCL component out of this
			// would be nice if one VCL component could handle multiple property types
			if (($type === "date_select") || ($type === "datetime_select"))
			{
				if (is_array($args["rawdata"][$name]))
				{
					if (isset($property["save_format"]) and $property["save_format"] === "iso8601")
					{
						$dt = $args["rawdata"][$name];
						if ($dt["year"] < 1 || $dt["month"] < 1 || $dt["day"] < 1)
						{
							$property["value"] = "";
						}
						else
						{
							$property["value"] = sprintf("%04d-%02d-%02d",$dt["year"],$dt["month"],$dt["day"]);
						}
					}
					else
					{
						$property["value"] = date_edit::get_timestamp($args["rawdata"][$name]);
					}
				}
			}
			elseif ("datepicker" === $type and isset($args["rawdata"][$name]) and is_array($args["rawdata"][$name]))
			{
				$property["value"] = datepicker::get_timestamp($args["rawdata"][$name]);
			}
			elseif ($type === "relmanager")
			{
				$argblock["prop"] = &$property;
				//$target_reltype = $this->relinfo[$property["reltype"]];
				//$argblock["prop"]["reltype"] = $target_reltype;
				//var_dump($this->relinfo);
				$argblock["prop"]["relinfo"] = $this->relinfo[$property["reltype"]];

				$vcl_inst = new relmanager();
				// XXX: would be nice if this could return an error message as well
				$vcl_inst->process_relmanager($argblock);
			}
			elseif (($type === "select") && isset($property["multiple"]))
			{
				$property["value"] = $this->make_keys($args["rawdata"][$name]);
			}

			if (isset($property["method"]) && $property["method"] === "bitmask")
			{
				// shift to the left, shift to the right
				// pop, push, pop, push
				if (!isset($pvalues[$name]))
				{
					$pvalues[$name] = 0;
				}

				if (!isset($args["rawdata"][$name]))
				{
					$args["rawdata"][$name] = 0;
				}

				if ( ($pvalues[$name] & $property["ch_value"]) && !($args["rawdata"][$name] & $property["ch_value"]))
				{
					$pvalues[$name] -= $property["ch_value"];
				}
				elseif (!($pvalues[$name] & $property["ch_value"]) && ($args["rawdata"][$name] & $property["ch_value"]))
				{
					$pvalues[$name] += $property["ch_value"];
				}
			}

			if ($this->is_rel)
			{
				if ($name === "name")
				{
					$this->obj_inst->set_name($property["value"]);
				}
				else
				{
					$values[$name] = $property["value"];
				}
			}
			else
			{
				if (isset($property["method"]) && $property["method"] === "bitmask")
				{
					$val = ($property["ch_value"] == $property["value"]) ? $property["ch_value"] : 0;
					if ($this->obj_inst->is_property($name))
					{
						$this->obj_inst->set_prop($name,$val);
					}
				}
				elseif ($type !== "releditor" && $this->obj_inst->is_property($name))	// cause it submits CRAP
				{
					$this->obj_inst->set_prop($name,$property["value"]);
				}
			}
		}

		$this->data_processing_result_status = $processing_status;

		if ($this->stop_processing)
		{
			return false;
		}

		if ($this->is_rel && is_array($values) && sizeof($values) > 0)
		{
			$def = $this->_ct[$this->clid]["def"];
			$_tmp = new object($this->id);
			$old = $_tmp->meta("values");

			$old2 = $old[$def];
			$new = array_merge($old2,$values);
			$old[$def] = $new;

			$this->obj_inst->set_meta("values",$old);
		}

		// translation. if the object is is_translated or needs_translation, it gets the has_translation flag
		if ($this->obj_inst->flag(OBJ_NEEDS_TRANSLATION) || $this->obj_inst->flag(OBJ_IS_TRANSLATED) || $this->obj_inst->prop("needs_translation") || $this->obj_inst->prop("is_translated"))
		{
			$this->obj_inst->set_flag(OBJ_HAS_TRANSLATION, true);
		}

		// gee, I wonder how many pre_save handlers do I have to fix to get this thing working
		// properly

		if (method_exists($this->inst, "callback_pre_save"))
		{
			try
			{
				$this->inst->callback_pre_save(array(
					"new" => $new,
					"id" => $this->id,
					"request" => &$args,
					"obj_inst" => $this->obj_inst
				));
			}
			catch (Exception $e)
			{
				$this->show_error_text(t("Esines olulisi vigu. Andmed v&otilde;ivad j&auml;&auml;da osaliselt salvestamata"));
			}
		}

		// this is here to solve the line break problems with RTE
		if (isset($args["cb_nobreaks"]) && is_array($args["cb_nobreaks"]))
		{
			$this->obj_inst->set_meta("cb_nobreaks",$args["cb_nobreaks"]);
		}

		// there is a bug somewhere which causes certain objects to get a
		// status of 0, until I figure it out, the first part of this if clause
		// deals with it -- duke
		if ($this->obj_inst->prop("status") == 0 || !empty($this->classinfo["no_status"]))
		{
			$this->obj_inst->set_status(STAT_ACTIVE);
		}

		$this->obj_inst->save();
		$this->id = $this->obj_inst->id();

		$this->show_success_text(t("Andmed salvestatud!"));

		if ($new)
		{
			if (!empty($alias_to) || !empty($args["rawdata"]["alias_to"]))
			{
				$_to = obj(($args["rawdata"]["alias_to"] ? $args["rawdata"]["alias_to"] : $alias_to));

				// XXX: reltype in the url is numeric, it probably should not be
				$reltype = $args["rawdata"]["reltype"] ? $args["rawdata"]["reltype"] : $reltype;
				$_to->connect(array(
					"to" => $this->obj_inst->id(),
					"type" => $reltype
				));
				// now scan the bloody properties
				// but I need all the properties


				// XXX: this invokes load_defaults, which in turn overwrites variables
				// in $this instance. This might lead to some nasty bugs
				$bt = $this->get_properties_by_type(array(
					"type" => array("relpicker","relmanager", "popup_search"),
					"clid" => $_to->class_id()
				));

				$symname = "";

				// figure out symbolic name for numeric reltype
				foreach($this->relinfo as $key => $val)
				{
					if (substr($key,0,7) === "RELTYPE")
					{
						if ($reltype == $val["value"])
						{
							$symname = $key;
						}
					}
				}

				// figure out which property to check
				foreach($bt as $item_key => $item)
				{
					// double check just in case
					if (!empty($symname) && ($item["type"] === "popup_search" || $item["type"] === "relpicker" || $item["type"] === "relmanager") && ($item["reltype"] == $symname))
					{
						$target_prop = $item_key;
					}
				}


				// now check, whether that property has a value. If not,
				// set it to point to the newly created connection
				if (!empty($symname) && !empty($target_prop))
				{
					$conns = $_to->connections_from(array(
						"type" => $symname,
					));
					$conn_count = sizeof($conns);
				};

				// this is after the new connection has been made
				if (!empty($target_prop) && ($conn_count == 1 || empty($bt[$target_prop]["multiple"])))
				{
					if (empty($alias_to_prop) and empty($args["rawdata"]["alias_to_prop"])) // avoid double save
					{
						$_to->set_prop($target_prop, $this->obj_inst->id());
						$_to->save();
					}
				}

				if (!empty($alias_to_prop) || !empty($args["rawdata"]["alias_to_prop"]))
				{
					$altp = !empty($alias_to_prop) ? $alias_to_prop : $args["rawdata"]["alias_to_prop"];
					$curpv = $_to->prop($altp);
					$property_info = $_to->get_property_list();

					if (!empty($property_info[$altp]["multiple"]) || is_array($curpv))
					{
						$curpv[$this->obj_inst->id()] = $this->obj_inst->id();
					}
					else
					{
						$curpv = $this->obj_inst->id();
					}
					/**
					 check, if the property, where the connected objects id should be saved
					 really exist or not, save it only when it exist.
					 why it is needed?
					because, when i create relpicker properties somewhere in callback method
					and give it names like foo[$someid], then such property does not exist
					and i am not 100% that this is the place to check this thing, but right now
					let it be, until some further investigation and some other opinions
						--dragut
					**/
					if ( $_to->is_property($altp) )
					{
						$_to->set_prop($altp, $curpv);
					}

					$_to->save();
				}
			}
		}

		foreach($properties as $key => $prop)
		{
			// load callback_post_save in multifile upload if not done allready
			$inst = isset($prop["vcl_inst"]) ? $prop["vcl_inst"] : null;
			if (!is_object($inst))
			{
				switch($prop["type"])
				{
					case "releditor":
						$inst = get_instance("vcl/releditor");
						break;

					case "multifile_upload":
						$inst = get_instance("vcl/multifile_upload");
						break;
				}
			}

			if (is_object($inst) && method_exists($inst, "callback_post_save"))
			{
			        $inst->callback_post_save(array(
						"id" => $this->obj_inst->id(),
						"request" => &$args,
						"obj_inst" => $this->obj_inst,
						"new" => $new,
						"prop" => $prop,
			        ));
			}
		}

		if (method_exists($this->inst,"callback_post_save"))
		{
			// you really shouldn't attempt something fancy like trying
			// to set properties in there. Well, you probably can
			// but why would you want to, it's called post_save handler
			// for a reason
			$this->inst->callback_post_save(array(
				"id" => $this->obj_inst->id(),
				"request" => &$args,
				"obj_inst" => $this->obj_inst,
				"new" => $new,
			));
		}

		// call post save controller from cfgform object if defined
		$cfgform_id = $filter["cfgform_id"];

		if (is_oid($cfgform_id) and $this->can("view", $cfgform_id))
		{
			$cfgform_o = new object($cfgform_id);
			$cfg_cntrl = (array) $cfgform_o->prop("post_save_controllers");

			if (count($cfg_cntrl))
			{
				$controller_inst = get_instance(CL_CFGCONTROLLER);

				foreach ($cfg_cntrl as $cfg_cntrl_id)
				{
					if (is_oid($cfg_cntrl_id))
					{
						$tmp = $tmp2 = array();
						$controller_inst->check_property($cfg_cntrl_id, $this->obj_inst->id(), $tmp, $args, $tmp2, $this->obj_inst);
					}
				}
			}
		}

		if (!empty($_POST["pseh"]) && isset($_SESSION["ps_event_handlers"][$_POST["pseh"]]) && $_SESSION["ps_event_handlers"][$_POST["pseh"]][3] == $this->obj_inst->class_id())
		{
			$pseh_dat = $_SESSION["ps_event_handlers"][$_POST["pseh"]];
			$pseh_i = get_instance($pseh_dat[0]);
			$pseh_i->$pseh_dat[1]($this->obj_inst, $pseh_dat[2]);
			unset($_SESSION["ps_event_handlers"][$_POST["pseh"]]);
		}

		return true;
	}

	//////////////////////////////////////////////////////////////////////
	//
	// init functions for classes that do not use automatic form generator
	//
	//////////////////////////////////////////////////////////////////////

	function _change_init($args, $classname, $tpl = "")
	{
		if (!$this->can("edit", $args["id"]))
		{
			error::raise(array(
				"id" => ERR_ACL,
				"msg" => sprintf(t("ACL error saidil %s: CAN_%s denied for oid %s"), aw_ini_get("baseurl"),"edit", $args["id"])
			));
		}
		$ob = new object($args["id"]);
		$self_url = aw_global_get("REQUEST_URI");
		if ($args["return_url"] != "")
		{
			$this->mk_path(0,"<a href='$args[return_url]'>Tagasi</a> / <a href='$self_url'>Muuda $classname</a>");
		}
		else
		{
			$this->mk_path($ob->parent(), "<a href='$self_url'>Muuda $classname</a>");
		}
		if ($tpl != "")
		{
			$this->read_template($tpl);
		}
		return $ob;
	}

	function get_rel_types()
	{
		$reltypes = array();
		if (sizeof($this->relinfo) > 0)
		{
			foreach($this->relinfo as $item)
			{
				$reltypes[$item["value"]] = $item["caption"];
				$clidlist = array();
				$_tmp = new aw_array($item["clid"]);
				foreach($_tmp->get() as $clid)
				{
					$clidlist[] = $clid;
				};
				$this->relclasses[$item["value"]] = $clidlist;
			}
		}

		$reltypes[0] = "alias";
		return $reltypes;
	}

	/**

		@attrib name=view params=name all_args="1"

		@param id required type=int acl="view"
		@param group optional
		@param period optional

		@returns


		@comment

	**/
	function view($arr = array())
	{
		$arr["view"] = 1;
		error::view_check($arr["id"]);
		return $this->change($arr);
	}

	function alter_property($arr)
	{
		$arr["type"] = "text";
	}

	////
	// !Returns a list of config forms used by this
	function get_cfgform_list($args = array())
	{
		$ol = new object_list(array(
			"class_id" => CL_CFGFORM,
			"site_id" => array(),
			"lang_id" => array(),
			"subclass" => $this->clid
		));
		$rv = array();
		$l = get_instance("languages");
		$lid = $l->get_langid_for_code(aw_global_get("user_adm_ui_lc"));
		foreach($ol->arr() as $o)
		{
			// this must use user interface language, not site content language
			$trs = $o->meta("translations");
			if ($lid && isset($trs[$lid]) && $lid != $o->lang_id())
			{
				$rv[$o->id()] = $trs[$lid]["name"];
			}
			else
			{
				$rv[$o->id()] = $o->name();
			}
		}
		return $rv;
	}

	// returns array of configured properties in $group
	// needs either clid or clfile
	function get_property_group($arr, $args = array())
	{
		$force_mgr = false;
		if (method_exists($this->inst, "callback_get_cfgmanager"))
		{
			$force_mgr = true;
			$this->inst->cfgmanager = $this->callback_get_cfgmanager(array(
				"request" => $args,
			));
		}

		// load defaults (from the generated properties XML file) first

		$filter = array();
		if (!empty($arr["form"]))
		{
			$filter["form"] = $arr["form"];
		}

		if (!empty($arr["rel"]))
		{
			$filter["rel"] = 1;
		}

		if (empty($arr["clid"]) && !empty($this->clid))
		{
			$arr["clid"] = $this->clid;
		}

		// XXX: add some checks
		$all_properties = $this->load_defaults(array(
			"clid" => $arr["clid"],
			"clfile" => $arr["clfile"],
			"filter" => $filter,
		));

		// nii .. ja kuidas ma n&uuml;&uuml;d saan teada k&otilde;ik omadused, mis mind huvitavad?

		// I could use a different approach here ... for example, if I'm saving then
		// only the properties that should be saved should be returned. or not?

		$this->features["has_rte"] = false;
		$cfg_props = $all_properties;
		$tmp = array();

		if ($this->can("view", $arr["cfgform_id"]) && !$force_mgr)
		{
			$cfg_props = $this->load_from_storage(array(
				"id" => $arr["cfgform_id"],
			));

			if ($this->cfg_debug)
			{
				print "loading from " . $arr["cfgform_id"] . "<br>";
			}
			// if there is a bug in config form which caused the groupdata
			// to be empty, then this is the place where we should fix it.
		}
		else
		{
			// no config form? alright, load the default one then!
			if ($arr["clid"] == CL_DOCUMENT && !$force_mgr)
			{
				$def_cfgform = aw_ini_get("document.default_cfgform");
				if ($this->can("view",$def_cfgform))
				{
					$cfg_props = $this->load_from_storage(array(
						"id" => $def_cfgform,
					));

					if ($this->cfg_debug)
					{
						print "loading cfgform $def_cfgform specified by document.default_cfgform";
					}
				}
				else
				{
					if ($this->cfg_debug)
					{
						print "loading the most basic document template";
					}

					list($cfg_props,$grplist) = $this->load_from_file();

					if (empty($this->groupinfo) || !empty($grplist))
					{
						$this->groupinfo = $grplist;
					}
				}
			}
			else
			if (is_oid($this->inst->cfgmanager))
			{
				// load a configuration manager if a class uses it
				$cfg_loader = new object($this->inst->cfgmanager);
				$mxt = $cfg_loader->meta("use_form");

				$forms = $mxt[$arr["clid"]];
				$gx = aw_global_get("gidlist_pri_oid");

				// I have a list of forms for groups and I have a list of group oids..
				// find the first usable one
				$found_form = false;
				if (is_array($gx) && is_array($forms))
				{
					// start from group with highest priority
					arsort($gx);
					foreach($gx as $grp_oid => $grp_pri)
					{
						if (!empty($forms[$grp_oid]) && empty($found_form))
						{
							$found_form = $forms[$grp_oid];
							$this->tmp_cfgform = $found_form;
						};
					};
				}

				// gruppides ei muutu mitte midagi

				// use it, if we found one, otherwise fall back to defaults
				if ($this->can("view",$found_form))
				{
					$cfg_props = $this->load_from_storage(array(
						"id" => $found_form,
					));

					if ($this->cfg_debug)
					{
						print "loading default cfgform " . $found_form;
						print "<br>";
					}
				}
			}
			else
			{
				// foreach makes a copy of the array so this should be safe
				foreach($cfg_props as $key => $val)
				{
					if (isset($val["user"]) && 1 == $val["user"])
					{
						unset($cfg_props[$key]);
					}
				}
			}
		}

		// redefine relmgr
		$cfg_props["relationmgr"] =Array(
			"name" => "relationmgr",
			"type" => "relationmgr",
			"caption" => t("Seostehaldur"),
			"store" => "no",
			"group" => "relationmgr"
		);
		$this->_cfg_props = $cfg_props;

		// I need group and caption from each one

		// alright, I need to do this in a better way. perhaps like the way it was done in the tree

		$default_group = false;

		$si = __get_site_instance();
		$has_cb = method_exists($si, "callback_get_group_display");
		$groupmap = $rgroupmap = array();

		// now, how do I make sure what level a group has?
		foreach($this->groupinfo as $gkey => $ginfo)
		{
			if (!empty($ginfo["parent"]))
			{
				$groupmap[$ginfo["parent"]][] = $gkey;
				$rgroupmap[$gkey] = $ginfo["parent"];
			}

			if ($has_cb)
			{
				if ($si->callback_get_group_display($arr["clid"], $gkey, $ginfo) == PROP_IGNORE)
				{
					continue;
				}
			}

			if (!empty($ginfo["grphide"]))
			{
				unset($this->groupinfo[$gkey]);
				continue;
			}

			$parent = empty($ginfo["parent"]) ? 0 : $ginfo["parent"];

			if (
				($parent === 0 and empty($this->grpmap[0])) or // take the very first first level group
				isset($ginfo["default"]) // is predefined default grp
			)
			{
				$default_group = $gkey;
			}

			$this->grpmap[$parent][$gkey] = $ginfo;
		}

		// the very default group comes from arr
		// groupinfo contains a flat list of all groups
		// I need to figure out which group should I actually be using
		// if there is one in the url and it actually exists, then use it
		$use_group = "";

		if (isset($arr["group"]) and isset($this->groupinfo[$arr["group"]]))
		{
			$use_group = $arr["group"];
		}
		else
		{
			if (method_exists($this->inst,"callback_get_default_group"))
			{
				$use_group = $this->inst->callback_get_default_group(array(
					"request" => $args,
				));
			}

			if (!isset($use_group) || !isset($this->groupinfo[$use_group]))
			{
				$use_group = $default_group;
			}
		}

		if (!$use_group)
		{
			$use_group = "general";
		}

		if (!empty($arr["group"]) and $arr["group"] != $use_group)
		{ // unavailable or inexistent group requested
			$url = aw_ini_get("baseurl") . aw_url_change_var("group", $use_group);
			header("Location: {$url}"); //!!! kas siin v6ib tekkida redirectide suletud tsykkel? n2ib et v6ib
		}

		if (!empty($this->use_group))
		{
			$use_group = $this->use_group;
		}

		$this->active_groups[] = $use_group;
		// but .. if this is the case and the group is a first level group then I should
		// right in this place calculate the correct group as well
		$current_group = $use_group;

		while (isset($this->grpmap[$use_group]))
		{
			// good lord, but that is some really good code here!
			reset($this->grpmap[$use_group]);
			list($use_group,) = each($this->grpmap[$use_group]);
			$this->active_groups[] = $use_group;
		}

		$grpinfo = $this->groupinfo[$use_group];
		// and climb back down again, e.g. make sure we always have _all_ active groups
		// (active meaning in the path)
		while(isset($grpinfo["parent"]) && isset($this->groupinfo[$grpinfo["parent"]]))
		{
			if (!in_array($grpinfo["parent"],$this->active_groups))
			{
				$this->active_groups[] = $grpinfo["parent"];
			}
			$grpinfo = $this->groupinfo[$grpinfo["parent"]];
		}

		$this->use_group = $use_group;
		$this->grp_path = array();
		$this->prop_by_group = array();
		$tmp = array();
		$property_groups = array();

		// do it 2 cycles, first figure out which groups have properties
		// so that I can select a new default group
		foreach($cfg_props as $key => $val)
		{
			// ignore properties that are not defined in the defaults
			if (!isset($all_properties[$key]) && !isset($val["force_display"]))
			{
				continue;
			};

			if (isset($val["display"]) && $val["display"] === "none")
			{
				continue;
			};

			if (empty($val["group"]))
			{
				continue;
			}


			// defaults from class, cfgform can override things
			// XXX: should I implement some kind of safety checks here?
			if (isset($all_properties[$key]))
			{
				$propdata = array_merge($all_properties[$key],$val);
			}
			else
			{
				$propdata = $val;
			}

			// deal with properties belonging to multiple groups
			$propgroups = is_array($val["group"]) ? $val["group"] : array($val["group"]);

			// ah, I alright .. propgroups decides whether a group is shown
			// and I need to know that information for each group in my path

			// now, what do I do with groups that are more than one level deep .. well, I guess
			// I'm just ignoring those for now
			foreach($propgroups as $k => $pkey)
			{
				if (is_array($pkey))
				{
					foreach ($pkey as $pkey2)
					{
						if (!empty($rgroupmap[$pkey2]))
						{
							$propgroups[] = $rgroupmap[$pkey2];
						}
					}
					unset($propgroups[$k]);
				}
				elseif (!empty($rgroupmap[$pkey]))
				{
					$propgroups[] = $rgroupmap[$pkey];
				}
			}

			//$this->prop_by_group = array_merge($this->prop_by_group,array_flip($propgroups));
			$this->prop_by_group = $this->prop_by_group + array_flip($propgroups);

			$property_groups[$key] = $propgroups;
		}

		foreach($cfg_props as $key => $val)
		{
			// ignore properties that are not defined in the defaults
			if (!isset($all_properties[$key]) && !isset($val["force_display"]))
			{
				continue;
			}

			if (isset($val["display"]) && $val["display"] === "none")
			{
				continue;
			}

			if (isset($all_properties[$key]))
			{
				$propdata = array_merge($all_properties[$key],$val);
				// Miks siin yldse mergetakse? Kui cfgform midagi 2ra v6tab, siis siin pannakse see ju tagasi! :@ -kaarel 25.02.2009
				if(is_array($val) && count($val) > 0 && !isset($val["parent"]))
				{
					unset($propdata["parent"]);
				}
			}
			else
			{
				$propdata = $val;
			}

			if (isset($propdata["type"]) && $propdata["type"] === "submit")
			{
				$propdata["value"] = ifset($propdata, "caption");
			}

			// XXX: cfgform defaults are supported for checkboxes only right now
			if (isset($propdata["type"]) && $propdata["type"] === "checkbox" && empty($val["default"]))
			{
				unset($propdata["default"]);
			}

			$propgroups = isset($property_groups[$key]) ? $property_groups[$key] : array();

			if (count($propgroups) < 1)
			{
				continue;
			}

			// skip anything that is not in the active group
			if (empty($this->cb_no_groups) && !in_array($use_group,$propgroups))
			{
				if (($key === "needs_translation" || $key === "is_translated") && ($use_group === "general2" || $use_group === "general_sub"))
				{
					$tmp[$key] = $propdata;
				}
				if (!(($key === "needs_translation" || $key === "is_translated") && ($use_group === "general2" || $use_group === "general_sub")))
				{
					continue;
				}
			}

			if (!empty($propdata["richtext"]) && empty($this->classinfo["allow_rte"]))
			{
				unset($propdata["richtext"]);
			};

			if (!empty($propdata["richtext"]))
			{
				$this->features["has_rte"] = true;
			};

			// return only toolbar, if this is a config form with fixed toolbar
			if (empty($arr["ignore_layout"]) && !empty($this->classinfo["fixed_toolbar"]) && empty($arr["cb_part"]))
			{
				if ($propdata["type"] !== "toolbar")
				{
					continue;
				}
			}

			// shouldn't I do some kind of overriding?
			$tmp[$key] = $propdata;
		}

		// If view_property is given we actually only want the HTML of that one property!
		if(!empty($GLOBALS["view_property"]))
		{
			$tmp = isset($tmp[$GLOBALS["view_property"]]) ? array($GLOBALS["view_property"] => $tmp[$GLOBALS["view_property"]]) : array();
		}

		$this->use_group = $use_group;
		return $tmp;
	}

	////
	// !Returns a list of properties having the requested type
	function get_properties_by_type($arr)
	{
		// load defaults first
		$all_properties = $this->load_defaults(array(
			"clid" => $arr["clid"],
			"clfile" => empty($arr["clfile"]) ? null : $arr["clfile"]
		));

		$rv = array();

		$type_filter = is_array($arr["type"]) ? $arr["type"] : array($arr["type"]);

		foreach($all_properties as $key => $val)
		{
			if (in_array($val["type"],$type_filter))
			{
				$rv[$key] = $val;
			};
		};

		return $rv;

	}

	////
	// !Returns a list of properties having the requested name
	function get_properties_by_name($arr)
	{
		// load defaults first
		$all_properties = $this->load_defaults(array(
			"clid" => $arr["clid"],
			"clfile" => $arr["clfile"],
		));

		$rv = array();

		$name_filter = is_array($arr["props"]) ? $arr["props"] : array($arr["props"]);

		foreach($all_properties as $key => $val)
		{
			if (in_array($val["name"],$name_filter))
			{
				$rv[$key] = $val;
			};
		};

		return $rv;

	}

	// and then I'll also need a method to load properties by their names
	// relmanager and releditor need it

	////
	// !id - config form id
	function load_from_storage($arr)
	{
		$id = $arr["id"];

		if (!is_array($this->classinfo))
		{
			$this->classinfo = array();
		}

		$cfg_flags = array(
			"classinfo_fixed_toolbar" => "fixed_toolbar",
			"classinfo_allow_rte" => "allow_rte",
			"classinfo_disable_relationmgr" => "disable_relationmgr",
		);
		$rv = false;

		try
		{
			$cfgform_obj = obj($id, array(), CL_CFGFORM);
			$ci = $cfgform_obj->instance();

			// get property cfg
			$rv = $ci->get_cfg_proplist($cfgform_obj->id());
			// get layout cfg
			$layoutinfo = $ci->get_cfg_layout($cfgform_obj);

			if (is_array($layoutinfo))
			{
				$this->layoutinfo = $layoutinfo;
			}

			// get group cfg
			$grps = $ci->get_cfg_groups($cfgform_obj->id());

			foreach($cfg_flags as $key => $val)
			{
				$this->classinfo[$val] = $cfgform_obj->prop($key);
			}

			// sometimes the grplist is empty in config form.
			// I don't know why, but it is, and in this case
			// I'll load the groups from the file
			$sbc = $cfgform_obj->prop("subclass");

			// config form overloads original properties
			if ($sbc == CL_DOCUMENT)
			{
				list($prplist,$grplist) = $this->load_from_file(null);
				if (empty($grps))
				{
					$grps = $grplist;
				}
				else
				{
					foreach($grps as $gkey => $gitem)
					{
						if (empty($gitem["icon"]) && !empty($grplist[$gkey]["icon"]))
						{
							$grps[$gkey]["icon"] = $grplist[$gkey]["icon"];
						}
					}
				}
			}

			$tmp = array();
			foreach($grps as $gkey => $gval)
			{
				// use the "submit" setting from the original group
				if (!empty($this->groupinfo[$gkey]["submit"]))
				{
					$gval["submit"] = $this->groupinfo[$gkey]["submit"];
				}

				if (!empty($this->groupinfo[$gkey]["tabgroup"]))
				{
					$gval["tabgroup"] = $this->groupinfo[$gkey]["tabgroup"];
				}

				if ((isset($this->groupinfo[$gkey]["submit_method"]) && $this->groupinfo[$gkey]["submit_method"]) || (isset($gval["submit_method"]) && $gval["submit_method"]))
				{
					$gval["submit_method"] = $this->groupinfo[$gkey]["submit_method"];
				}
				$tmp[$gkey] = $gval;
			}

			if (!$cfgform_obj->prop("classinfo_disable_relationmgr"))
			{
				$tmp["relationmgr"] = $this->groupinfo["relationmgr"];
			}

			$this->groupinfo = $tmp;
		}
		catch (awex_obj $e)
		{
		}

		return $rv;
	}

	// right now only the document class supports this
	// added $str parameter, cause image_convert.aw overrides this function with that parameter and gives [STRICT] level error because of that --dragut@
	function load_from_file($str)
	{
		$cfgu = new cfgutils();
		$def = $this->get_file(array("file" => (aw_ini_get("basedir") . "/xml/documents/def_cfgform.xml")));
		$rv = $cfgu->parse_cfgform(array("xml_definition" => $def));
		if (!is_array($this->classinfo))
		{
			$this->classinfo = array();
		};
		$tmp = $cfgu->get_classinfo();
		$this->classinfo = array_merge($tmp,$this->classinfo);
		return $rv;
	}

	// Defaults always get loaded, even if only for validation purposes
	// holy fuck, this sucks
	function load_defaults($arr = array())
	{
		$cfgu = new cfgutils();

		$defaults = $cfgu->load_properties(array(
			"file" => empty($arr["clid"]) ? (isset($arr["clfile"]) ? $arr["clfile"] : "" ) : "",
			"clid" => !empty($arr["clid"]) ? $arr["clid"] : $this->clid,
			"filter" => isset($arr["filter"]) ? $arr["filter"] : "",
			"system" => 1,
		));

		$this->groupinfo = $cfgu->get_groupinfo();
		$this->forminfo = $cfgu->get_forminfo();

		if (!is_array($this->classinfo))
		{
			$this->classinfo = array();
		}
		$this->classinfo = array_merge($this->classinfo,$cfgu->get_classinfo());

		$this->groupinfo["relationmgr"] = array(
			"caption" => t("Seostehaldur"),
			"submit" => "no",
		);

		if(!empty($_REQUEST["srch"]))
		{
			$this->groupinfo["relationmgr"]["submit_method"] = "get";
		}

		$defaults["relationmgr"] = array(
			"name" => "relationmgr",
			"type" => "relationmgr",
			"caption" => t("Seostehaldur"),
			"store" => "no",
			"group" => "relationmgr"
		);

		if (isset($this->classinfo["no_status"]))
		{
			unset($defaults["status"]);
		};

		if (isset($this->classinfo["no_comment"]))
		{
			unset($defaults["comment"]);
		};
		$this->layoutinfo = $cfgu->get_layoutinfo();

		$this->relinfo = $cfgu->get_relinfo();

		return $defaults;

	}

	////
	// !Can be used to query classinfo
	function classinfo($arr)
	{
		return isset($this->classinfo[$arr["name"]]) ? $this->classinfo[$arr["name"]] : false;
	}

	// name - name
	// value - value
	function set_classinfo($arr)
	{
		$this->classinfo[$arr["name"]] = $arr["value"];
	}

	function groupinfo($arr = array())
	{
		return $this->groupinfo;
	}

	function set_groupinfo($name,$val)
	{
		$this->groupinfo[$name] = $val;
	}

	////
	// !Returns a list of currently visible groups, should be called after property retrieving
	function get_visible_groups()
	{
		$rv = array();
		// the rationale is this .. all first level groups are always visible
		$visible_groups = array();

		// always show first level groups
		if (isset($this->grpmap[0]))
		{
			foreach($this->grpmap[0] as $gkey => $gval)
			{
				$visible_groups[] = $gkey;
			}
		}

		foreach($this->active_groups as $act_group)
		{
			if (isset($this->grpmap[$act_group]))
			{
				foreach($this->grpmap[$act_group] as $gkey => $gval)
				{
					$visible_groups[] = $gkey;
				}
			}
		}

		foreach($visible_groups as $vgr)
		{
			$gval = $this->groupinfo[$vgr];

			// remove groups with no properties. CL_RELATION is one of the big
			// users of this
			if (!isset($this->prop_by_group[$vgr]) or !empty($gval["grphide"]))
			{
				continue;
			}

			if (empty($gval["parent"]))
			{
				$gval["level"] = 1;
			}
			else
			{
				$par_count = 1;
				$gdat = $this->groupinfo[$vgr];

				while(isset($gdat["parent"]) && $gdat["parent"])
				{
					$par_count++;
					$gdat = $this->groupinfo[$gdat["parent"]];
				}

				$gval["level"] = $par_count;

			}

			if (in_array($vgr,$this->active_groups))
			{
				$gval["active"] = 1;
			}

			$rv[$vgr] = $gval;
		}

		if (aw_ini_get("config.trans.separate_tabs") and aw_ini_get("user_interface.content_trans") and !empty($this->inst->translation_lang_id))
		{ // add virtual language-subgroups
			$l = get_instance("languages");
			$ll = $l->get_list(array(
				"set_for_user" => true,
				"all_data" => true
			));

			foreach ($ll as $lang_id => $lang)
			{
				if ($lang_id != $this->obj_inst->lang_id())
				{
					$id = $this->translation_lang_var_name . $lang_id;
					$rv[$id] = array(
						"caption" => $lang["name"],
						"parent" => $this->inst->transl_grp_name,
						"level" => 2,
						"set_link" => aw_url_change_var($this->translation_lang_var_name, $lang_id)
					);

					if ($lang_id == $this->inst->translation_lang_id)
					{
						$rv[$id]["active"] = 1;
					}
				}
			}
		}

		return $rv;
	}

	////
	// form - name of the form
	// attr - name of the attribute
	function forminfo($arr = array())
	{
		return isset($this->forminfo[$arr["form"]][$arr["attr"]]) ? $this->forminfo[$arr["form"]][$arr["attr"]] : null;
	}

	function has_feature($name)
	{
		return $this->features[$name];
	}

	/** helper method to generate return url-s for actions

	**/
	function finish_action($arr)
	{
		$rv = $this->mk_my_orb("change",array(
			"group" => $arr["group"],
			"_alias" => get_class($this),
			"page" => $arr["page"],
			"alias" => $arr["alias"],
			"topic" => $arr["topic"],
			"id" => $arr["id"],
			"section" => $arr["section"],
			"group_parent" => $arr["group_parent"],
		));
		// XXX: I need to lose class from the url
		if (!empty($arr["_alias"]))
		{
			$rv = aw_url_change_var("class",false,$rv);
		};
		return $rv;

	}

	/** helper method to generate return url if PROP_FATAL_ERROR occured

	**/
	function abort_action($arr)
	{
		// this maybe be called for a not logged in user for an embedded object
		aw_session_set("no_cache", 1);
		return aw_global_get("HTTP_REFERER");
	}

	/////////////////////////////////////////////
	// sorta-automatic translation helpers
	function trans_save($arr, $props, $props_if = array())
	{
		$o = $arr["obj_inst"];
		$o->set_no_modify(true);
		if ($o->is_brother())
		{
			$o = $o->get_original();
		}
		$l = get_instance("languages");
		$ll = $l->get_list(array(
			"all_data" => true,
			"set_for_user" => true
		));
		$all_vals = $o->meta("translations");
		$repls = array(
			chr(197).chr(161) => "&scaron;",
			chr(197).chr(160) => "&Scaron;",
			chr(197).chr(190) => "&#158;",
			chr(197).chr(189) => "&#142;",
			chr(195).chr(182) => "&ouml;",
			chr(195).chr(164) => "&auml;",
			chr(195).chr(188) => "&uuml;",
			chr(195).chr(181) => "&otilde;",
			chr(195).chr(156) => "&Uuml;",
			chr(195).chr(149) => "&Otilde;",
			chr(195).chr(150) => "&Ouml;",
			chr(195).chr(132) => "&Auml;",
			chr(196).chr(171) => "&#299;",
			chr(196).chr(129) => "&#257;",
			chr(196).chr(147) => "&#275;",
			chr(197).chr(179) => "&#371;",
			chr(196).chr(141) => "&#269;",
			chr(197).chr(171) => "&#363;"
		);
		$all_vals = $o->meta("translations");
		$time = time();

		if (aw_ini_get("config.trans.separate_tabs") and aw_ini_get("user_interface.content_trans") and array_key_exists($arr["request"][$this->translation_lang_var_name], $ll))
		{
			$lang = $ll[$arr["request"][$this->translation_lang_var_name]];
			$lid = $lang["id"];

			if ($lid != $o->lang_id())
			{
				$this->translation_lang_id = $lid;
				$mod = false;

				foreach(safe_array($props_if) as $pi)
				{
					$props[] = $pi;
				}
				foreach($props as $p)
				{
					$nm = "trans_".$lid."_".$p;

					if (isset($arr["request"][$nm]))
					{
						$str = $arr["request"][$nm];

						// replace estonian chars in other languages with entities
						if ($lang["acceptlang"] != "et")
						{
							foreach($repls as $r1 => $r2)
							{
								$str = str_replace($r1, $r2, $str);
							}
						}

						$str = str_replace(chr(226).chr(128).chr(147), "-", $str);
						$nv = iconv("UTF-8", $lang["charset"]."//IGNORE", $str);

						if (!isset($all_vals[$lid][$p]) or $nv != $all_vals[$lid][$p])
						{
							$mod = true;
						}

						$all_vals[$lid][$p] = $nv;
					}
				}

				$o->set_meta("trans_".$lid."_status", $arr["request"]["act_".$lid]);
				$arr["obj_inst"]->set_meta("trans_".$lid."_status", $arr["request"]["act_".$lid]);

				if ($mod)
				{
					$o->set_meta("trans_".$lid."_modified", $time);
					$arr["obj_inst"]->set_meta("trans_".$lid."_modified", $time);
				}
			}
		}
		else
		{
			foreach($ll as $lid => $lang)
			{
				if ($lid == $o->lang_id())
				{
					continue;
				}

				$mod = false;

				foreach($props as $p)
				{
					$nm = "trans_".$lid."_".$p;

					if (isset($arr["request"][$nm]))
					{
						$str = $arr["request"][$nm];

						// replace estonian chars in other languages with entities
						if ($lang["acceptlang"] != "et")
						{
							foreach($repls as $r1 => $r2)
							{
								$str = str_replace($r1, $r2, $str);
							}
						}

						$str = str_replace(chr(226).chr(128).chr(147), "-", $str);
						$nv = iconv("UTF-8", $lang["charset"]."//IGNORE", $str);

						if (!isset($all_vals[$lid][$p]) or $nv != $all_vals[$lid][$p])
						{
							$mod = true;
						}

						$all_vals[$lid][$p] = $nv;
					}
				}

				$o->set_meta("trans_".$lid."_status", $arr["request"]["act_".$lid]);
				$arr["obj_inst"]->set_meta("trans_".$lid."_status", $arr["request"]["act_".$lid]);

				if ($mod)
				{
					$o->set_meta("trans_".$lid."_modified", $time);
					$arr["obj_inst"]->set_meta("trans_".$lid."_modified", $time);
				}
			}
		}

		$o->set_meta("translations", $all_vals);
		$o->save();
		$arr["obj_inst"]->set_meta("translations", $all_vals);
	}

	function trans_callback($arr, $props, $props_if_filled = null)
	{
		aw_global_set("output_charset","UTF-8");
		$ret = array();

		// get langs
		$l = get_instance("languages");
		$ll = $l->get_list(array(
			"set_for_user" => true,
			"all_data" => true
		));

		$pl = $arr["obj_inst"]->get_property_list();

		$cfgform_id = $this->get_cfgform_for_object(array(
			"obj_inst" => $arr["obj_inst"],
			"args" => $arr["request"],
		));
		$ppl = $pl;

		if ($this->can("view", $cfgform_id))
		{
			$cf = get_instance(CL_CFGFORM);
			$pl = $cf->get_props_from_cfgform(array("id" => $cfgform_id));
			$ppl = $cf->get_cfg_proplist($cfgform_id);
			// also, get group list and then throw out all the props that are not in visible groups
			$gps = $cf->get_cfg_groups($cfgform_id);

			foreach($pl as $k => $v)
			{
				if ($gps[$ppl[$k]["group"]]["grphide"] == 1)
				{
					unset($pl[$k]);
				}
			}

			if (!count($ppl))
			{
				$ppl = $pl;
			}
			else
			{
				foreach($ppl as $pn => $pd)
				{
					$ppl[$pn] = array_merge($pl[$pn], $pd);
				}
			}
		}

		$o = $arr["obj_inst"];
		$o = $o->get_original();
		$all_vals = $o->meta("translations");
		$original_lang_id = $o->lang_id();

		if (aw_ini_get("config.trans.separate_tabs") and aw_ini_get("user_interface.content_trans"))
		{
			$this->transl_grp_name = $arr["request"]["group"];

			if (isset($arr["request"][$this->translation_lang_var_name]) and isset($ll[$arr["request"][$this->translation_lang_var_name]]))
			{
				$lang = $ll[$arr["request"][$this->translation_lang_var_name]];
			}
			else
			{
				$lang = reset($ll);

				while ($lang["id"] === $original_lang_id)
				{
					$lang = next($ll);
				}
			}

			$this->translation_lang_id = $lang["id"];
			if ($lang["id"] != $original_lang_id)
			{
				$vals = $all_vals[$lang["id"]];

				// get prop values in user's source language
				$src_lang_id = $original_lang_id;

				if (aw_global_get("uid"))
				{
					$current_user = obj(aw_global_get("uid_oid"));
					if (isset($all_vals[$current_user->prop("base_lang")]))
					{
						$src_lang_id = $current_user->prop("base_lang");
					}
				}

				$src_lang_vals = $all_vals[$src_lang_id];

				foreach(safe_array($props_if_filled) as $p)
				{
					if ($arr["obj_inst"]->prop($p) != "")
					{
						$props[] = $p;
					}
				}
				//
				$so = 0;
				foreach($props as $p)
				{
					if (!isset($ppl[$p]))
					{
						continue;
					}
					// source language value
					$nm = "src_lng_val_".$p;
					$ret[$nm]["name"] = $nm;
					$ret[$nm]["caption"] = $ppl[$p]["caption"] . "<small>" . t(" (l&auml;htetekst)") . "</small>";
					$ret[$nm]["type"] = "text";
					$ret[$nm]["value"] = iconv(aw_global_get("charset"), "UTF-8", (isset($src_lang_vals[$p]) ? $src_lang_vals[$p] : $o->is_property($p) ? $o->prop_str($p) : ""));
					$ret[$nm]["ord"] = ++$so;
					unset($ret[$nm]["parent"]);
					unset($ret[$nm]["group"]);

					// translation field
					$nm = "trans_".$lang["id"]."_".$p;
					$ret[$nm] = $ppl[$p];
					$ret[$nm]["caption"] .=  "<small>" . t(" (t&otilde;lge)") . "</small>";
					$ret[$nm]["name"] = $nm;
					$ret[$nm]["value"] = iconv($lang["charset"], "UTF-8", $vals[$p]);
					$ret[$nm]["ord"] = ++$so;
					unset($ret[$nm]["parent"]);
					unset($ret[$nm]["group"]);
				}
				$nm = "act_".$lang["id"];
				$ret[$nm] = array(
					"name" => $nm,
					"caption" => t("T&otilde;lge aktiivne"),
					"type" => "checkbox",
					"ch_value" => 1,
					"value" => $o->meta("trans_".$lang["id"]."_status"),
					"ord" => ++$so
				);

				$nm = "sbt_".$lang["id"];
				$ret[$nm] = array(
					"name" => $nm,
					"caption" => t("Salvesta"),
					"type" => "submit",
					"ord" => ++$so
				);

				$nm = $this->translation_lang_var_name;
				$ret[$nm] = array(
					"name" => $nm,
					"type" => "hidden",
					"value" => $lang["id"]
				);
			}
		}
		else
		{
			foreach($ll as $lid => $lang)
			{
				if ($lid == $original_lang_id)
				{
					continue;
				}

				$nm = "sep_$lid";
				$ret[$nm] = array(
					"name" => $nm,
					"type" => "text",
					"cols" => $pl[$p]["cols"],
					"rows" => $pl[$p]["rows"],
					"value" => iconv($lang["charset"], "UTF-8", $vals[$p]),
					"caption" => $lang["name"]."<a name='#$lid'></a>",
					"subtitle" => 1,
				);

				$vals = $all_vals[$lid];

				foreach($props as $p)
				{
					$nm = "trans_".$lid."_".$p;
					$ret[$nm] = $ppl[$p];
					$ret[$nm]["name"] = $nm;
					$ret[$nm]["value"] = iconv($lang["charset"], "UTF-8", $vals[$p]);
				}

				$nm = "act_".$lid;
				$ret[$nm] = array(
					"name" => $nm,
					"caption" => t("T&otilde;lge aktiivne"),
					"type" => "checkbox",
					"ch_value" => 1,
					"value" => $o->meta("trans_".$lid."_status")
				);
				$nm = "sbt_".$lid;
				$ret[$nm] = array(
					"name" => $nm,
					"caption" => t("Salvesta"),
					"type" => "submit",
				);
			}

			foreach(safe_array($props_if_filled) as $p)
			{
				if ($arr["obj_inst"]->prop($p) != "")
				{
					$nm = "trans_".$lid."_".$p;
					$ret[$nm] = array(
						"name" => $nm,
						"caption" => t($pl[$p]["caption"]),
						"type" => $pl[$p]["type"],
						"value" => iconv($lang["charset"], "UTF-8", $vals[$p])
					);
					if ($pl[$p]["richtext"] == 1)
					{
						$ret[$nm]["richtext"] = 1;
					}
				}
			}
			$nm = "act_".$lid;
			$ret[$nm] = array(
				"name" => $nm,
				"caption" => t("T&otilde;lge aktiivne"),
				"type" => "checkbox",
				"ch_value" => 1,
				"value" => $o->meta("trans_".$lid."_status")
			);
			$nm = "sbt_".$lid;
			$ret[$nm] = array(
				"name" => $nm,
				"caption" => t("Salvesta"),
				"type" => "submit",
			);
		}
		return $ret;
	}

	function trans_get_val($obj, $prop, $lang_id = false, $ignore_status = false)
	{
		if ($obj->is_brother())
		{
			$obj = $obj->get_original();
		}
		return $obj->trans_get_val($prop, $lang_id, $ignore_status);
	}

	function trans_get_val_str($obj, $prop)
	{
		if ($obj->is_brother())
		{
			$obj = $obj->get_original();
		}
		$pd = $GLOBALS["properties"][$obj->class_id()][$prop];
		$type = $pd["type"];
		$val = $obj->prop($prop);
		switch($type)
		{
			// YOU *CAN NOT* convert dates to strings here - it fucks up dates in vcl tables
			case "relmanager":
			case "relpicker":
			case "classificator":
			case "popup_search":
			case "releditor":
				if ($pd["store"] == "connect")
				{
					$rels = new object_list($obj->connections_from(array(
						"type" => $pd["reltype"]
					)));
					//$_tmp = $rels->names();
					$_tmp = array();
					foreach($rels->arr() as $relo)
					{
						$_tmp[] = $this->trans_get_val($relo, "name");
					}
					if (count($_tmp))
					{
						$val = join(", ", $_tmp);
					}
					else
					{
						$val = "";
					}
					break;
				}

			case "oid":
				if (is_oid($val))
				{
					if ($GLOBALS["object_loader"]->ds->can("view", $val))
					{
						$tmp = new object($val);
						//$val = $tmp->name();
						$val = $this->trans_get_val($tmp, "name");
					}
					else
					{
						$val = "";
					}
				}
				else
				if (is_array($val))
				{
					$vals = array();
					foreach($val as $k)
					{
						if (is_oid($k))
						{
							if ($GLOBALS["object_loader"]->ds->can("view", $k))
							{
								$tmp = new object($k);
								$vals[] = $this->trans_get_val($tmp, "name");
								//$vals[] = $tmp->name();
							}
						}
					}
					$val = join(", ", $vals);
				}
				break;
		}
		if ($val === "0" || $val === 0)
		{
			$val = "";
		}


		if (aw_ini_get("user_interface.content_trans") == 1 && ($cur_lid = aw_global_get("lang_id")) != $obj->lang_id())
		{
			$trs = $obj->meta("translations");
			if (isset($trs[$cur_lid]))
			{
				if ((true || $prop == "url" || $prop == "author") && $trs[$cur_lid][$prop] == "")
				{
					return $val;
				}
				$val = $trs[$cur_lid][$prop];
			}
		}
		return $val;
	}

	/**
		@attrib name=rel_cut
	**/
	function rel_cut($arr)
	{
		$_SESSION["rel_cut"] = $arr["check"];
		$_SESSION["rel_copied"] = null;
		$url = $this->mk_my_orb("change", array(
			"id" => $arr["id"],
			"group" => $arr["group"],
			"return_url" => $arr["return_url"]
		), $arr["class"]);
		return $url;
	}

	/**
		@attrib name=rel_copy
	**/
	function rel_copy($arr)
	{
		$_SESSION["rel_copied"] = $arr["check"];
		$_SESSION["rel_cut"] = null;
		$url = $this->mk_my_orb("change", array(
			"id" => $arr["id"],
			"group" => $arr["group"],
			"return_url" => $arr["return_url"]
		), $arr["class"]);
		return $url;
	}

	/**
		@attrib name=rel_paste
	**/
	function rel_paste($arr)
	{
		foreach(safe_array($_SESSION["rel_cut"]) as $cut_item)
		{
			$c = new connection($cut_item);

			$o = obj($arr["id"]);
			$idx = $c->prop("idx");
			$conns = $o->connections_from(array('reltype' => $c->prop("reltype")));
			$idxs = array();
			foreach($conns as $conn)
			{
				if($conn->prop("reltype") == $c->prop("reltype"))
				{
					$idxs[$conn->prop("idx")] = $conn;
				}
			}
			if(array_key_exists($idx , $idxs))
			{
				$err_id = $idxs[$idx]->prop("to");
				print sprintf(
					t("Seoste l&otilde;ikamisel tekkis j&auml;rgmine t&otilde;rge: objekt id'ga %s oli juba seosega #%s# seega objekt nimega %s seoseks sai #%s#"),
					$err_id,
					$GLOBALS["cfg"]["classes"][$idxs[$idx]->prop("to.class_id")]["alias"].$idx,
					$c->prop("to.name"),
					$GLOBALS["cfg"]["classes"][$idxs[$idx]->prop("to.class_id")]["alias"].(max(array_keys($idxs)) + 1)
				)."\n<br>";

				$idx = max(array_keys($idxs)) + 1;
			}

			$c->change(array(
				"from" => $arr["id"],
				"idx" => $idx,
			));
		}

		foreach(safe_array($_SESSION["rel_copied"]) as $c_item)
		{
			$c = new connection($c_item);
			$o = obj($arr["id"]);
			$idx = $c->prop("idx");
			$conns = $o->connections_from(array('reltype' => $c->prop("reltype")));
			$idxs = array();
			foreach($conns as $conn)
			{
				if($conn->prop("reltype") == $c->prop("reltype"))
				{
					$idxs[$conn->prop("idx")] = $conn;
				}
			}
			if(array_key_exists($idx , $idxs))
			{
				$err_id = $idxs[$idx]->prop("to");
				$lidxnum = $GLOBALS["cfg"]["classes"][$idxs[$idx]->prop("to.class_id")]["alias"];
				$lidxnum .= (max(array_keys($idxs)) + 1);

				print sprintf(
					t("Seoste kopeerilisel tekkis j&auml;rgmine t&otilde;rge: objekt id'ga %s oli juba seosega #%s# seega objekt nimega %s seoseks sai #%s#"),
					$err_id,
					$GLOBALS["cfg"]["classes"][$idxs[$idx]->prop("to.class_id")]["alias"].$idx,
					$c->prop("to.name"),
					$lidxnum
				)."\n<br>";

				$idx = max(array_keys($idxs)) + 1;
			}
			$o->connect(array(
				"to" => $c->prop("to"),
				"type" => $c->prop("reltype"),
				"idx" => $idx,
			));
		}
		$_SESSION["rel_copied"] = null;
		$_SESSION["rel_cut"] = null;
		$url = $this->mk_my_orb("change", array(
			"id" => $arr["id"],
			"group" => $arr["group"],
			"return_url" => $arr["return_url"]
		), $arr["class"]);

		if ($err_id)
		{
			if ($arr["silent"])
			{
				return "err";
			}
			die(html::href(array(
				"url" => $url,
				"caption" => t("Kliki siia j&auml;tkamiseks")
			)));
		}

		return $url;
	}

	/**
	@attrib name=delete_objects
	@param sel optional type=array
	@param check optional type=array
	@param post_ru required type=string
	**/
	function delete_objects($arr)
	{
		if (!is_array($arr["sel"]) && is_array($arr["check"]))
		{
			$arr["sel"] = $arr["check"];
		}

		foreach (safe_array($arr["sel"]) as $del_obj)
		{
			$obj = obj($del_obj);
			$obj->delete();
		}

		return $arr["post_ru"];
	}

	/**
	@attrib name=delete_rels
	@param id required type=oid
	@param sel optional type=array
	@param check optional type=array
	@param post_ru required type=string
	**/
	function delete_rels($arr)
	{
		if (!is_array($arr["sel"]) && is_array($arr["check"]))
		{
			$arr["sel"] = $arr["check"];
		}
		$from = obj($arr["id"]);
		foreach (safe_array($arr["sel"]) as $del_obj)
		{
			$from->disconnect(array(
				"from" => $del_obj
			));
		}
		return  $arr["post_ru"];
	}

	/**
	@attrib name=delete_rels_id
	@param id required type=oid
	@param sel optional type=array
	@param check optional type=array
	@param post_ru required type=string
	**/
	function delete_rels_id($arr)
	{
		if (!is_array($arr["sel"]) && is_array($arr["check"]))
		{
			$arr["sel"] = $arr["check"];
		}
		foreach (safe_array($arr["sel"]) as $del_id)
		{
			$c = new connectnion($del_id);
			$c->delete();
		}
		return  $arr["post_ru"];
	}

	/**
	@attrib name=rel_reverse all_args=1
	**/
	function rel_reverse($arr)
	{
		$_SESSION["rel_reverse"][$arr["id"]] = !empty($_SESSION["rel_reverse"][$arr["id"]]) ? 0 : 1;
		$url = $this->mk_my_orb("change", array(
			"id" => $arr["id"],
			"group" => $arr["group"],
			"return_url" => $arr["return_url"]
		), $arr["class"]);
		return $url;
	}

	function callback_get_transl($arr)
	{
		return $this->trans_callback($arr, $this->trans_props);
	}

	/**
		@attrib name=generic_cut
	**/
	function generic_cut($arr)
	{
		$_SESSION["tb_cuts"][$arr["tb_cut_var"]] = $arr["sel"];
		return $arr["post_ru"];
	}

	/**
		@attrib name=generic_paste
	**/
	function generic_paste($arr)
	{
		foreach(safe_array($_SESSION["tb_cuts"][$arr["tb_cut_var"]]) as $oid)
		{
			$o = obj($oid);
			$o->set_parent($arr["tb_paste_var"]);
			$o->save();
		}
		$_SESSION["tb_cuts"][$arr["tb_cut_var"]] = null;
		return $arr["post_ru"];
	}

	function make_menu_item_from_tabs($this_o, $level, $parent_o, $site_show_i,$cnt_menus)
	{
		if($cnt_menus == 0)
		{
			$_SESSION["menu_from_cb"] = null;
			$GLOBALS["add_item_level"] = $level - 1;
		}

		$level = $level - $GLOBALS["add_item_level"];

		if($cnt_menus == 0) $_SESSION["menu_from_cb"] = null;
		if(!$_SESSION["menu_from_cb"][$level]["items"] && !$_SESSION["menu_from_cb"][$level]["count"])
		{
			extract($_GET);
			$cfgform_i = get_instance(CL_CFGFORM);
			if(is_oid($id) && $this->can("view" , $id))
			{
				$this->object = $o = obj($id);

				$cfgform = $this->get_cfgform_for_object(array(
					"obj_inst" => $this->object,
					"args" => $_GET,
				));
				if (empty($cfgform) && is_object($this->object))
				{
					$cfgform = $this->object->meta("cfgform_id");
				}

				$cfgform_i->cff_init_from_class($o, $o->class_id(), false);
				if(is_oid($cfgform) && $this->can("view", $cfgform))
				{

					$props2 = $cfgform_i->get_props_from_cfgform(array("id" => $cfgform));
					$cfgform_i->get_cfg_groups($cfgform);
				}
				else
				{
					$props2 = $cfgform_i->cfg_proplist;
				}
			}

			$groups = array();
			$prop_count = array();
			$sub_groups = array();
			foreach($props2 as $prop)
			{
				$sub_groups[$prop["group"]] = $prop["group"];
			}

			foreach($cfgform_i->cfg_groups as $key => $val)
			{
				if(($level == 2 && !$val["parent"])|| ($level == 1 && $val["parent"]) || ($val["parent"] && ($val["parent"] != $_SESSION["menu_item_tab"] && $val["parent"] != $_GET["group"] && $val["parent"] != $_GET["openedtab"])))
				{
					unset($cfgform_i->cfg_groups[$key]);
				}
				else
				{
					$cfgform_i->cfg_groups[$key]["name"] = $key;
				}
			}

			foreach($props2 as $prop)
			{
//				$cfgform_i->cfg_groups[$prop["group"]]["name"] = $prop["group"];
				if(!$cfgform_i->cfg_groups[$prop["group"]]["grphide"])
				{
					$prop_count[$prop["group"]]++;
				}
			}

			foreach($cfgform_i->cfg_groups as $key => $val)
			{
				if(!$val["grphide"] && $prop_count[$key])
				{
					$groups[] = $val;
				}
			}

			$_SESSION["menu_from_cb"][$level]["count"] = 0;
			$_SESSION["menu_from_cb"][$level]["items"] = sizeof($groups);
			$_SESSION["menu_from_cb"][$level]["crap_items"] = $groups;
		}

		while (1)
		{
			if($_SESSION["menu_from_cb"][$level]["count"] < sizeof($_SESSION["menu_from_cb"][$level]["crap_items"]))
			{
				$item = $_SESSION["menu_from_cb"][$level]["crap_items"][$_SESSION["menu_from_cb"][$level]["count"]];
				$_SESSION["menu_from_cb"][$level]["count"]++;
				$_SESSION["menu_from_cb"][$level]["crap_item"][$item["name"]] = $_SESSION["menu_from_cb"][$level]["count"];
				$vars = array (
					"section" => $_GET["section"],
					"group" => $item["name"],
					"openedtab" => ($level == 1)  ?$item["name"] :$item["parent"],
				);
				$link = aw_url_change_var($vars);
				if(is_object($this->object)) $id = $this->object->id();

				if($level == 1 && $item["parent"]) continue;
				if($level == 2 && $_GET["group"] != $item["parent"] && $item["parent"] !=$_GET["openedtab"]) continue;
			//	$_SESSION["menu_item_tab"] = $item["name"];

				$selected = 0;
				if($_GET["group"] == $item["name"] || $item["name"] == $_GET["openedtab"]) $selected = 1;

				return array(
					"text" => $item["caption"],
					"link" => $link,
					"section" => $id + $_SESSION["menu_from_cb"][$level]["count"],//$o_91_2->id(),
					"parent_section" => $id + $_SESSION["menu_from_cb"][$level]["crap_item"][$item["parent"]],//is_object($o_91_1) ? $o_91_1->id() : $o_91_2->parent(),
					"is_end" => ($_SESSION["menu_from_cb"][$level]["count"] == sizeof($_SESSION["menu_from_cb"][$level]["crap_items"])) ? 1 : 0,
					"is_selected" => $selected,
				);
			}
			else
			{
				return false;
			}
		}
	}

	function callback_generate_scripts_from_class_base($arr)
	{
		$retval = "";

		if(aw_ini_get("user_interface.content_trans") && empty($arr["new"]) && empty($arr["request"]["group"]) || isset($arr["request"]["group"]) && $arr["request"]["group"] !== "relationmgr")
		{
			if (!isset($arr["request"]["class"]))
			{
				$arr["request"]["class"] = "";
			}

			if($arr["request"]["class"] === "admin_if" || $arr["request"]["class"] === "personnel_management" && isset($arr["request"]["group"]) && isset($arr["request"]["group"]) && $arr["request"]["group"] === "offers")
			{
				$if_clause = "
				anything_changed = false;
				statuses = new Array();
				statuses_val = new Array();
				for(i = 0; i < f.elements.length; i++)
				{
					el1 = f.elements[i];
					if(el1.name.indexOf('old[status][') == 0)
					{
						el2 = aw_get_el('new[status][' + el1.name.substring(12));
						if(el1.value == el2.value && !el2.checked || el1.value != el2.value && el2.checked)
						{
							anything_changed = true;
							break;
						}
					}
				}
				if(anything_changed)";
				$asd = "aktiveerin/deaktiveerin";
				$all_trans_status_value = 1;
				$if_clause2 = "true";
			}
			else
			{
				$if_clause2 = "el_exists('status_".(($arr["obj_inst"]->status() == STAT_ACTIVE) ? 2 : 1)."') == 1";
				if($arr["request"]["class"] === "language")
				{
					$status_variable = "lang_status_".(($arr["obj_inst"]->status() == STAT_ACTIVE) ? 2 : 1);
				}
				else
				{
					$status_variable = "status_".(($arr["obj_inst"]->status() == STAT_ACTIVE) ? 2 : 1);
				}
				$asd = ($arr["obj_inst"]->status() != STAT_ACTIVE) ? "aktiveerin" : "deaktiveerin";
				$if_clause = "if(!f.".$status_variable.".checked)";
				$all_trans_status_value = ($arr["obj_inst"]->status() == STAT_ACTIVE) ? 2 : 1;
			}

			$function_check = "
			function el_exists(id)
			{
				var ret = 0;
				for(i = 0; i < document.changeform.elements.length; i++)
				{
					el = document.changeform.elements[i];
					if(el.id.indexOf(id) == 0)
					{
						ret = 1;
					}
				}
				return ret;
			}
			function check()
			{
				if(".$if_clause2.")
				{
					var f = document.forms['changeform'];
					".$if_clause."
					{
						if(confirm('Kas " . $asd . " koik tolked?'))
						{
							f.all_trans_status.value = ".$all_trans_status_value.";
						}
					}
				}
			}

			aw_submit_handler = check;";
			$retval .= $function_check;
		}
		return $retval;
	}

	//yldine autocomplete funktsioon juhuks kui on vaja leida vaid objekti nime ja klassi j2rgi
	/**
		@attrib name=object_name_autocomplete_source all_args=1
		@param class_ids optional
		@param parent optional
		@param param required
	**/
	function object_name_autocomplete_source($arr)
	{
		$cid = $arr["class_ids"];
		$ac = get_instance("vcl/autocomplete");
		$arr = $ac->get_ac_params($arr);

		$filter = array(
			"class_id" => $cid,
			"name" => $arr[$arr["param"]]."%",
			"lang_id" => array(),
			"site_id" => array(),
			"limit" => 100
		);
		if($arr["parent"])
		{
			$filter["parent"] = $arr["parent"];
		}

		$ol = new object_list($filter);
		return $ac->finish_ac($ol->names());
	}

//--------------- mailiga teavitamise komponentide orb funktsuoonid------------
//t6en4oliselt koristab need 4ra kui parema mooduse leiab kuda neid hoida
	/**
		@attrib name=co_autocomplete_source
		@param sp_p_co optional
	**/
	function co_autocomplete_source($arr)
	{
		$ac = get_instance("vcl/autocomplete");
		$arr = $ac->get_ac_params($arr);

		$ol = new object_list(array(
			"class_id" => CL_CRM_COMPANY,
			"name" => $arr["sp_p_co"]."%",
			"lang_id" => array(),
			"site_id" => array(),
			"limit" => 100
		));
		return $ac->finish_ac($ol->names());
	}

	/**
		@attrib name=p_autocomplete_source
		@param sp_p_p optional
	**/
	function p_autocomplete_source($arr)
	{
		$ac = get_instance("vcl/autocomplete");
		$arr = $ac->get_ac_params($arr);

		$ol = new object_list(array(
			"class_id" => CL_CRM_PERSON,
			"name" => $arr["sp_p_p"]."%",
			"lang_id" => array(),
			"site_id" => array(),
			"limit" => 200
		));
		return $ac->finish_ac($ol->names());
	}

	/**
		@attrib name=add_s_res_to_p_list
	**/
	function add_s_res_to_p_list($arr)
	{
		$o = obj($arr["id"]);
		$persons = $o->meta("imp_p");
		foreach(safe_array($arr["sel"]) as $p_id)
		{
			$persons[aw_global_get("uid")][$p_id] = $p_id;
		}
		$o->set_meta("imp_p", $persons);
		$o->set_meta("sp_from" , $arr["mail_notify_from"]);
		$o->set_meta("sp_subject" , $arr["mail_notify_subject"]);
		$o->set_meta("sp_content" , $arr["mail_notify_content"]);
		$o->save();
		return $arr["post_ru"];
	}

	/**
		@attrib name=remove_p_from_l_list
	**/
	function remove_p_from_l_list($arr)
	{
		$o = obj($arr["id"]);
		$persons = $o->meta("imp_p");
		foreach(safe_array($arr["sel"]) as $p_id)
		{
			unset($persons[aw_global_get("uid")][$p_id]);
		}
		$o->set_meta("imp_p", $persons);
		$o->save();
		return $arr["post_ru"];
	}

	/**
		@attrib name=send_notify_mail
	**/
	function send_notify_mail($arr)
	{
		$o = obj($arr["id"]);
		$ppl = $this->get_people_list($o);
		$u = get_instance(CL_USER);
		$person = $u->get_person_for_uid(aw_global_get("uid"));
		$user_name = "";
		if(is_object($person))
		{
			$user_name = $person->name();
		}
		foreach($ppl as $oid => $nm)
		{
			$p = obj($oid);
			if($p->class_id() == CL_GROUP)
			{
				$group_person_list = $p->get_group_persons();
				foreach($group_person_list->arr() as $group_person)
				{
					$ppl[$group_person->id()] = $group_person->name();
				}
				unset($ppl[$oid]);
			}
		}
		foreach($ppl as $oid => $nm)
		{
			if(!$o->meta("sp_content"))
			{
				$message = sprintf(t("User %s has added/changed the following file %s. Please click the link below to view the document \n %s"), $user_name , $o->name(), html::get_change_url($o->id()));
			}
			else
			{
				$message = $o->meta("sp_content");

				$replace_vars = array(
					"#file#" => $o->name(),
					"#file_url#" => html::get_change_url($o->id()),
					"#user_name#" => $user_name,
				);
				foreach($replace_vars as $var => $val)
				{
					$message = str_replace($var, $val , $message);
				}
			}

			if(!$o->meta("sst"))
			{
				$subject = t("Teavitus muutunud dokumendist");
			}
			else
			{
				$subject = $o->meta("sp_subject");
			}

			if(!$o->meta("sp_from"))
			{
				$from = "From: ".aw_ini_get("baseurl");
			}
			else
			{
				$from = $o->meta("sp_from");
			}

			$p = obj($oid);
			$email = $p->prop("email.mail");
			send_mail(
				$email,
				$subject,
				$message,
				$from
			);
		}
		return $arr["post_ru"];
	}

	function _sp_s_res($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_p_tbl($t);
		if ($arr["request"]["sp_p_name"] != "" || $arr["request"]["sp_p_co"] != "")
		{
			$param = array(
				"class_id" => CL_CRM_PERSON,
				"lang_id" => array(),
				"site_id" => array(),
				"name" => "%".$arr["request"]["sp_p_name"]."%"
			);
			if ($arr["request"]["sp_p_co"] != "")
			{
				$param["CL_CRM_PERSON.RELTYPE_CURRENT_JOB.org.name"] = "%".$arr["request"]["sp_p_co"]."%";
			}
			$ol = new object_list($param);
			foreach($ol->arr() as $p)
			{
				$t->define_data(array(
					"name" => html::obj_change_url($p),
					"co" => html::obj_change_url($p->company()),
					"phone" => $p->prop("phone.name"),
					"email" => html::href(array("url" => "mailto:".$p->prop("email.mail"),"caption" => $p->prop("email.mail"))),
					"oid" => $p->id()
				));
			}
		}
	}

	public function microtime_float(){
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}
//----------------- END mailiga teavitamise komponentide orb funktsuoonid----------


	/**
	@attrib name=gt_change api=1 params=name
		@param id required type=oid
		@param return_url optional type=string
	**/
	public function gt_change($arr)
	{
		return html::get_change_url($arr["id"] , array("return_url" => $arr["return_url"]));
	}

	/**
	@attrib api=1 params=pos
	@returns bool
		Whether submit data processing was successful. FALSE if errors occurred.
	**/
	public function data_processed_successfully()
	{
		return ($this->data_processing_result_status <= 2);
	}

	/** sets objects order values
	@attrib api=1 params=pos
		@param ord required type=array
			array(object id => order value)
	**/
	public function set_order($ord)
	{
		foreach($ord as $id => $val)
		{
			$o = obj($id);
			$o->set_ord($val);
			$o->save();
		}
	}

	/**
		@attrib name=create_new_object all_args=1
	**/
	public function create_new_object($arr)
	{
		$o = new object();
		$o->set_parent($arr["parent"]);
		$o->set_class_id($arr["clid"]);
		$o->save();

		if(!empty($arr["parent"]) && !empty($arr["connect"]))
		{
			$parent = obj($arr["parent"]);
			$parent->connect(array(
				"to" => $o->id(),
				"reltype" => $arr["connect"]
			));
		}

		foreach($arr as $prop => $val)
		{
			if($o->is_property($prop))
			{
				$o->set_prop($prop, $val);
			}
		}
		$o->save();
		die($o->id());
	}

	private function load_cfgform($cfgform_id = null)
	{
		if (!is_oid($cfgform_id))
		{
		}

		try
		{
			$this->cfgform_obj = obj($cfgform_id, array(), CL_CFGFORM);
			$this->cfgform_id = $cfgform_id;
		}
		catch (awex_obj $e)
		{
		}
	}
}
