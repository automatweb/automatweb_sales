<?php
// cfgform.aw - configuration form
// adds, changes and in general manages configuration forms

//!!! todo. default cfgformi m22ramine editonlyna yldise alla. koos lingiga current defauldile.

// cfgview -- cfgform is embedded as alias and requested object is shown as configured in that cfgform
// awcb -- automatweb class_base

/*
	@classinfo relationmgr=yes

	@groupinfo groupdata caption=Tabid
		@groupinfo groupdata_a caption=Tabid parent=groupdata
		@groupinfo groupdata_b caption=Liikumine parent=groupdata

	@groupinfo layout caption=Layout
		@groupinfo layout_props caption=Omadused parent=layout
		@groupinfo layout_layouts caption=Layoudid parent=layout
		@groupinfo layout_tables caption=Tabelid parent=layout

	@groupinfo avail caption="K&otilde;ik omadused"
	@groupinfo controllers caption="Kontrollerid"

	@groupinfo set_controllers caption=Salvestamine parent=controllers
	@groupinfo get_controllers caption=N&auml;itamine parent=controllers

	@groupinfo settings caption="Seaded"
	@groupinfo defaults caption="Omaduste v&auml;&auml;rtused" parent=settings
	@groupinfo system caption="Vormi seaded" parent=settings
	@groupinfo cfgview_settings caption="Klass aliasena" parent=settings
	@groupinfo orb_settings caption="ORB seaded" parent=settings
	@groupinfo view_settings caption="Liidese kuvamine" parent=settings

	@groupinfo translate caption="T&otilde;lgi"
		@groupinfo lang_1 caption="lang" parent=translate
		@groupinfo lang_2 caption="lang" parent=translate
		@groupinfo lang_3 caption="lang" parent=translate
		@groupinfo lang_4 caption="lang" parent=translate
		@groupinfo lang_5 caption="lang" parent=translate
		@groupinfo lang_6 caption="lang" parent=translate
		@groupinfo lang_7 caption="lang" parent=translate
		@groupinfo lang_8 caption="lang" parent=translate
		@groupinfo lang_9 caption="lang" parent=translate
		@groupinfo lang_10 caption="lang" parent=translate
		@groupinfo lang_11 caption="lang" parent=translate
		@groupinfo lang_12 caption="lang" parent=translate
	@groupinfo show_props caption="Omadused grupiti"
	@groupinfo transl caption="T&otilde;lgi seadete vormi"


	@default table=objects
		@property cfg_proplist type=hidden field=meta method=serialize
		@caption Omadused

		@property cfg_groups type=hidden field=meta method=serialize
		@caption Tabid


	@default group=general
		@property subclass type=select newonly=1
		@caption Klass

		@property ctype type=text editonly=1 field=subclass
		@caption T&uuml;&uuml;p

	@default field=meta
	@default method=serialize
		@property use_output type=relpicker reltype=RELTYPE_OUTPUT
		@caption V&auml;ljundvorm

		@property xml_definition type=fileupload editonly=1
		@caption Uploadi vormi fail

		@property preview type=text store=no editonly=1
		@caption Definitsioon

		@property use_in_releditor type=checkbox ch_value=1
		@caption V&otilde;imalda kasutamist releditoris


	@default group=groupdata_a
		@property edit_groups_tb type=toolbar no_caption=1 store=no
		@caption Tabide toolbar

		@property edit_groups type=table no_caption=1 store=no
		@caption Muuda tabe

		@layout add_grp type=vbox
		@caption Lisa uus tab
			@property add_grp_return type=text store=no parent=add_grp
			@caption

			@property add_grp_name type=textbox store=no parent=add_grp
			@comment S&uuml;steemne identifitseerija tabile. Ainult ladina t&auml;hestiku t&auml;hed ning alakriips ( _ ) lubatud. V&auml;him pikkus 2.
			@caption Nimi

			@property add_grp_caption type=textbox store=no parent=add_grp
			@caption Pealkiri

			@property add_grp_parent type=select store=no parent=add_grp
			@caption Millise tabi alla


	@default group=groupdata_b
		@property group_movement type=table store=no no_caption=1
		@caption Tabide vaheline liikumine


	@default group=layout_props
		@property navtoolbar type=toolbar store=no no_caption=1 editonly=1
		@caption Toolbar

		@property layout type=callback callback=callback_gen_layout store=no no_caption=1
		@caption Layout


	@default group=layout_layouts
		@property layouts_toolbar type=toolbar store=no no_caption=1 editonly=1
		@caption Toolbar

		@property layouts_table type=table store=no no_caption=1 editonly=1
		@caption Layoudid

	@default group=layout_tables
		@property tables_toolbar type=toolbar store=no no_caption=1 editonly=1
		@caption Toolbar

		@property tables_table type=table store=no no_caption=1 editonly=1
		@caption V&auml;ljad

		@property tables_controller type=relpicker reltype=RELTYPE_TABLE_CONTROLLER store=no multiple=1
		@caption N&auml;itamise kontroller

	@property availtoolbar type=toolbar group=avail store=no no_caption=1 editonly=1
	@caption Av. Toolbar

	@property availprops type=table store=no group=avail no_caption=1
	@caption K&otilde;ik omadused

	@property subaction type=hidden store=no group=layout_props,avail
	@caption Subaction (sys)

	@property post_save_controllers type=relpicker multiple=1 size=3 group=set_controllers reltype=RELTYPE_CONTROLLER
	@caption Salvestamisj&auml;rgne kontroller

	@property prop_submit_controllers type=text subtitle=1 group=set_controllers
	@caption Omaduste kontrollerid

	@property gen_submit_controllers type=callback callback=gen_controller_props group=set_controllers no_caption=1


	@property gen_view_controllers type=callback callback=gen_view_controller_props group=get_controllers
	@caption Kontrollerid

	@property default_table type=table group=defaults no_caption=1
	@caption Vaikimisi v&auml;&auml;rtused

	@property trans_tbl_capt type=text subtitle=1 group=lang_1,lang_2,lang_3,lang_4,lang_5,lang_6,lang_7,lang_8,lang_9,lang_10,lang_11,lang_12
	@caption Omadused

	@property trans_tbl type=table group=lang_1,lang_2,lang_3,lang_4,lang_5,lang_6,lang_7,lang_8,lang_9,lang_10,lang_11,lang_12 no_caption=1

	@property trans_tbl_grp_capt type=text group=lang_1,lang_2,lang_3,lang_4,lang_5,lang_6,lang_7,lang_8,lang_9,lang_10,lang_11,lang_12 subtitle=1
	@caption Tabid

	@property trans_tbl_grps type=table group=lang_1,lang_2,lang_3,lang_4,lang_5,lang_6,lang_7,lang_8,lang_9,lang_10,lang_11,lang_12 no_caption=1

	@property trans_tbl_lays type=table group=lang_1,lang_2,lang_3,lang_4,lang_5,lang_6,lang_7,lang_8,lang_9,lang_10,lang_11,lang_12 no_caption=1
	@property trans_tbl_table_capts type=table group=lang_1,lang_2,lang_3,lang_4,lang_5,lang_6,lang_7,lang_8,lang_9,lang_10,lang_11,lang_12 no_caption=1


@default group=system
	@property sysdefault type=table no_caption=1
	@caption S&uuml;steemi seaded


@default group=show_props
@layout mlist type=hbox width=20%:80%
	@property treeview type=treeview store=no parent=mlist no_caption=1
	@property props_list type=table store=no parent=mlist no_caption=1


@default group=transl
	@property transl type=callback callback=callback_get_transl
	@caption T&otilde;lgi


@default group=cfgview_settings

	@property cfgform_id_from_url type=checkbox field=meta method=serialize
	@caption Seadetevormi ID URList

	@property cfgview_action type=select field=meta method=serialize
	@caption N&auml;itamise meetod

	@property cfgview_view_params type=textbox field=meta method=serialize
	@caption Parameetrid vaatamisele (view)
	@comment Lisatakse iga kord p&auml;ringu url-ile. Formaat: param_nimi=param_v&auml;&auml;rtus&...

	@property cfgview_view_params_from_controller type=relpicker reltype=RELTYPE_PARAMS_CONTROLLER field=meta method=serialize
	@caption Parameetrid kontrollerist
	@comment Parameetreid antakse edasi muutujas $params

	@property cfgview_change_params type=textbox field=meta method=serialize
	@caption Parameetrid muutmisele (change)
	@comment Lisatakse iga kord p&auml;ringu url-ile. Formaat: param_nimi=param_v&auml;&auml;rtus&...

	@property cfgview_change_params_from_controller type=relpicker reltype=RELTYPE_PARAMS_CONTROLLER field=meta method=serialize
	@caption Parameetrid kontrollerist
	@comment Parameetreid antakse edasi muutujas $params

	@property cfgview_new_params type=textbox field=meta method=serialize
	@caption Parameetrid lisamisele (new)
	@comment Lisatakse iga kord p&auml;ringu url-ile. Formaat: param_nimi=param_v&auml;&auml;rtus&...

	@property cfgview_new_params_from_controller type=relpicker reltype=RELTYPE_PARAMS_CONTROLLER field=meta method=serialize
	@caption Parameetrid kontrollerist
	@comment Parameetreid antakse edasi muutujas $params

	@property cfgview_grps type=select multiple=1 size=10 field=meta method=serialize
	@caption N&auml;idatavad tabid

	@property cfgview_ru type=textbox field=meta method=serialize
	@caption Aadress kuhu suunata (lisamine)

	@property cfgview_ru_change type=textbox field=meta method=serialize
	@caption Aadress kuhu suunata (muutmine)

	@property cfgview_ru_cntrl type=relpicker reltype=RELTYPE_RU_CONTROLLER field=meta method=serialize
	@caption Aadress kuhu suunata (kontroller)

	@property cfgview_ru_id_param type=textbox field=meta method=serialize
	@caption OID parameeter
	@comment Parameeter, millega suunatavale aadressile lisatakse loodud/muudetud objekti OID


@default group=orb_settings
	@property orb_settings type=table store=no no_caption=12


@default group=view_settings
	@property classinfo_fixed_toolbar type=checkbox ch_value=1
	@caption Fix. toolbar

	@property classinfo_allow_rte type=chooser
	@caption RTE

	@property classinfo_disable_relationmgr type=checkbox ch_value=1
	@caption Peida seostehaldur

	@property awcb_add_id type=checkbox ch_value=1 default=0
	@caption Lisa id
	@comment Lisa klassi objekti kuvamisel konteineri id-le seadete vormi id

	@property awcb_form_only type=checkbox ch_value=1 default=0
	@caption N&auml;ita ainult vormi

	@property awcb_confirm_save_data type=checkbox ch_value=1 default=0
	@caption Salvestamise kontroll lehelt lahkumisel



// ---------- RELATIONS -------------
	@reltype PROP_GROUP value=1 clid=CL_MENU
	@caption omaduste kataloog

	@reltype CONTROLLER value=3 clid=CL_CFGCONTROLLER
	@caption Salvestamise kontroller

	@reltype VIEWCONTROLLER value=5 clid=CL_CFG_VIEW_CONTROLLER
	@caption N&auml;itamise kontroller

	@reltype OUTPUT value=4 clid=CL_CFGFORM
	@caption V&auml;ljund

	@reltype VIEW_DFN_GRP value=6 clid=CL_GROUP
	@caption Kasutajagrupp omaduste lubamiseks/keelamiseks

	@reltype PARAMS_CONTROLLER value=7 clid=CL_CFG_VIEW_CONTROLLER
	@caption Parameetrid kontrollerist

	@reltype TABLE_CONTROLLER value=8 clid=CL_CFG_VIEW_CONTROLLER
	@caption Tabeli kontroller

	@reltype RU_CONTROLLER value=9 clid=CL_CFGCONTROLLER
	@caption Aadress kuhu suunata (kontroller)

	// so, how da fuck do I implement the grid layout thingie?
	// add_item (item, row, col)

	// so .. first I have to implement a new attribute for layout thingie

	// and then I want to be able to add new widgets in the same order they are arriving

*/
class cfgform extends class_base
{
	var $all_props;

	public $cfg_proplist;
	/*
	Format: array(
		prop_name => array(
			[ord] =>
			[caption] => Nimi
			[type] => textbox
			[group] => general // parent group
            [group] => array( // property is in multiple groups
				[0] => grp_customers
				[1] => grp_projects
			)
			[parent] => co_bottom_seller_r // parent layout
			[textsize] =>
			[size] =>
			[rows] =>
			[cols] =>
			[store] =>
			[method] =>
			[table] => objects
			[field] => name
			[ch_value] => 2
			[name] => prop_name
			[rel] => 1
			[trans] => 1
			[comment] => Objekti nimi
			[orig_caption] => Nimi
			[maxlength] => 11
			[form] =>
			[year_from] => 1930
			[year_to] => 2010
			[save_format] => iso8601
			[reltype] => RELTYPE_CLIENT_MANAGER
			[multiple] => 1
			[rel_id] => first
			[mode] => manager
			[props] => start
			[props] => array(
				[0] => org
				[1] => profession
				[2] => start
				[3] => end
			)
			[table_fields] => array(
				[0] => org
				[1] => profession
				[2] => start
				[3] => end
			)
		), ...
	)
	*/

	public $cfg_groups;
	/*
	Format: array(
		group_name => array(
            [caption] => Yldine
            [default] => 1 // exclusive 1 or null
            [icon] => edit
            [focus] => name // name of property to get focus on page load. default null
            [orig_caption] => Yldine
            [parent] => work // parent group. optional
            [submit] => no // don't show submit button. default null
		), ...
	)
	*/

	public $cfg_layout;
	/*
	Format: array(
		layout_name => array(
			[area_caption] => Kliendisuhe
			[closeable] => 1
			[type] => hbox // vbox|hbox
			[width] => 50%:50% // width ratio of child layouts. applicable only when type is hbox
			[group] => cust_rel // parent group
			[caption] => // not used?
		), ...
	)
	*/

	private $default_values = array();
	private $cfgview_actions = array();
	private $default_new_layout_name = "new_layout_temporary_name";
	private $cfg_load_scope_index = array (
		"all" => array("properties", "groups", "layouts"),
		"properties" => array("properties"),
		"groups" => array("groups"),
		"layouts" => array("layouts")
	);

	function cfgform($arr = array())
	{
		$this->init(array(
			"clid" => cfgform_obj::CLID,
			"tpldir" => "cfgform",
		));
		$this->trans_props = array(
			"name", "cfgview_ru", "cfgview_ru_change"
		);
		$this->cfgview_actions = array(
			"view" => t("Vaatamine (view)"),
			"change" => t("Muutmine (change)"),
			"new" => t("Lisamine (new)"),
			"cfgview_change_new" => t("Muutmine (ka lisamine lubatud)"),
			"cfgview_view_new" => t("Vaatamine (ka lisamine lubatud)"),
		);
		$this->default_new_layout_name = t("new_layout_temporary_name");
	}

	function get_property($arr)
	{
		$data = &$arr["prop"];
		$retval = PROP_OK;

		if (!empty($arr["request"]["cfgform_add_grp"]) and "add_grp" !== substr($data["name"], 0, 7))
		{ // exclude other props from add grp form
			return PROP_IGNORE;
		}

		if (empty($arr["request"]["cfgform_add_grp"]) and "add_grp" === substr($data["name"], 0, 7))
		{ // exclude add grp form props
			return PROP_IGNORE;
		}

		switch($data["name"])
		{
			case "cfgview_ru":
				$applicable_methods = array("new", "cfgview_view_new", "cfgview_change_new");
				if (!in_array($arr["obj_inst"]->prop("cfgview_action"), $applicable_methods))
				{
					$retval = PROP_IGNORE;
				}
				break;

			case "cfgview_ru_change":
				$applicable_methods = array("change", "cfgview_change_new");
				if (!in_array($arr["obj_inst"]->prop("cfgview_action"), $applicable_methods))
				{
					$retval = PROP_IGNORE;
				}
				break;

			case "general_tb":
				$this->_general_tb($arr);
				break;

			case "edit_groups_tb":
				$this->_edit_groups_tb($arr);
				break;

			case "orb_settings":
				$this->_get_orb_settings($arr);
				break;

			case "edit_groups":
				$this->_edit_groups_tbl($arr);
				break;

			// add grp form props
			case "add_grp_parent":
				$data["options"][0] = t("Peatasemele");

				foreach ($this->grplist as $grp_name => $grp_data)
				{
					if (empty($grp_data["parent"]))
					{
						$data["options"][$grp_name] = $grp_data["caption"] . " [" . $grp_name . "]";
					}
				}
				break;

			case "add_grp_return":
				$data["value"] = html::href(array(
					"caption" => t("[ Tagasi ]"),
					"url" => aw_url_change_var("cfgform_add_grp", NULL),
				));
				break;
			// END add grp form props


			case "group_movement":
				$this->_group_movement($arr);
				break;

			case "classinfo_allow_rte":
				$data["options"] = array(
					0 => t("Ei kuva"),
					1 => t("AW RTE"),
					2 => t("FCKeditor"),
					3 => t("CodePress"),
				);
				$data["type"] = "select";
				break;

			case "sysdefault":
				$this->do_sysdefaults($arr);
				break;

			case "xml_definition":
				// I don't want to show the contents of the file here
				$data["value"] = "";
				break;

			case "preview":
				$data["value"] = "";
				break;

			case "subclass":
				$cx = new cfgutils();
				$class_list = new aw_array($cx->get_classes_with_properties());
				$cp = get_class_picker(array("field" => "def"));

				foreach($class_list->get() as $key => $val)
				{
					$data["options"][$key] = $val . " [" . substr(strtolower($cp[$key]), 3) . "]";
				}
				break;

			case "ctype":
				$clid = $arr["obj_inst"]->prop("subclass");
				$iu = html::img(array(
					"url" => icons::get_icon_url($clid,""),
				));
				try
				{
					$data["value"] = $iu . " " . aw_ini_get("classes.{$clid}.name") . " [" . basename(aw_ini_get("classes.{$clid}.file")) . "]";
				}
				catch (Exception $e)
				{
					$this->show_error_text(t("Klassi, millele seadete vorm kehtib, ei leidunud."));
				}
				break;

			case "navtoolbar":
				$this->gen_navtoolbar($arr);
				break;

			case "availtoolbar":
				$this->gen_availtoolbar($arr);
				break;

			case "availprops":
				$this->gen_avail_props($arr);
				break;

			case "default_table":
				$this->gen_default_table($arr);
				break;

			case "layouts_toolbar":
				$this->_layout_tb($arr);
				break;

			case "layouts_table":
				$this->_layout_tbl($arr);
				break;

			case "tables_toolbar":
				$this->_tables_tb($arr);
				break;

			case "tables_table":
				$this->_tables_tbl($arr);
				break;

			case "tables_controller":
				if(isset($arr["request"]["chtbl"]) and ($tbl = $arr["request"]["chtbl"]))
				{
					$conf = $arr["obj_inst"]->meta("tbl_config");
					$tbl_conf = $conf[$tbl];
					$ctrls = $tbl_conf["controllers"];
					foreach($ctrls as $ctrl)
					{
						$data["options"][$ctrl] = obj($ctrl)->name();
						$data["value"][$ctrl] = $ctrl;
					}
				}
				else
				{
					return PROP_IGNORE;
				}
				break;

			case "trans_tbl":
				$this->_trans_tbl($arr);
				break;

			case "trans_tbl_grps":
				$this->_trans_tbl_grps($arr);
				break;

			case "trans_tbl_lays":
				$this->_trans_tbl_lays($arr);
				break;

			case "trans_tbl_table_capts":
				$this->_trans_tbl_table_capts($arr);
				break;

			case "treeview":
				$this->do_meta_tree($arr);
				break;
			case "props_list":
				$retval = $this->do_table($arr);
				break;

			case "cfgview_action":
				$data["options"] = $this->cfgview_actions;
				break;

			case "cfgview_view_params":
				if($this->can("view", $arr["obj_inst"]->prop("cfgview_view_params_from_controller")))
				{
					$retval = PROP_IGNORE;
				}
			case "cfgview_view_params_from_controller":
				$applicable_methods = array("view", "cfgview_view_new");
				if (!in_array($arr["obj_inst"]->prop("cfgview_action"), $applicable_methods))
				{
					$retval = PROP_IGNORE;
				}
				break;

			case "cfgview_change_params":
				if($this->can("view", $arr["obj_inst"]->prop("cfgview_change_params_from_controller")))
				{
					$retval = PROP_IGNORE;
				}
			case "cfgview_change_params_from_controller":
				$applicable_methods = array("change", "cfgview_change_new");
				if (!in_array($arr["obj_inst"]->prop("cfgview_action"), $applicable_methods))
				{
					$retval = PROP_IGNORE;
				}
				break;

			case "cfgview_new_params":
				if($this->can("view", $arr["obj_inst"]->prop("cfgview_new_params_from_controller")))
				{
					$retval = PROP_IGNORE;
				}
			case "cfgview_new_params_from_controller":
				$applicable_methods = array("new", "cfgview_view_new", "cfgview_change_new");
				if (!in_array($arr["obj_inst"]->prop("cfgview_action"), $applicable_methods))
				{
					$retval = PROP_IGNORE;
				}
				break;

			case "cfgview_grps":
				$parent_grps = array();

				foreach ($this->grplist as $grp_name => $grp_data)
				{
					$data["options"][$grp_name] = $grp_data["caption"] . " [" . $grp_name . "]";

					if (!empty($grp_data["parent"]))
					{
						$parent_grps[] = $grp_data["parent"];
					}
				}

				foreach ($parent_grps as $name)
				{
					unset($data["options"][$name]);
				}
				break;
		}

		return $retval;
	}

	function do_meta_tree($arr)
	{
		if(empty($arr["request"]["meta"])) $arr["request"]["meta"] = $arr["obj_inst"]->meta("group_to_show");

		$tree = $arr["prop"]["vcl_inst"];
		$obj = $arr["obj_inst"];
		$grps = new aw_array($arr["obj_inst"]->meta("cfg_groups"));

		foreach($grps->get() as $name => $grp)
		{
			$parent = empty($grp["parent"]) ? 0 : $grp["parent"];
			$tree->add_item($parent,array(
				"name" => $grp["caption"],
				"id" => $name,
				"url" => aw_url_change_var(array("meta" => $name)),
			));
		}

		$tree->set_selected_item($arr["request"]["meta"]);

		// hm .. now I also need to create an object_tree, eh?
		//$arr["prop"]["value"] = $tree->finalize_tree();
	}

	function do_table(&$arr)
	{
		// get connected user groups
		$groups_list = new object_list($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_VIEW_DFN_GRP")));

		if (!$groups_list->count())
		{
			if ($arr["obj_inst"]->meta("has_show_to_groups_by_rel")) // backward compatibility check
			{
				$arr["prop"]["error"] = t("Seostatud kasutajagruppe omaduste lubamiseks/keelamiseks ei leidund");
				return PROP_ERROR;
			}
			else
			{ // backward compatibility
				$groups_list = new object_list(array(
					"class_id" => array(group_obj::CLID),
					"lang_id" => "%",
				));
			}
		}

		// table caption
		unset($arr["prop"]["no_caption"]);
		$arr["prop"]["caption"] = t("Omadused: ") . aw_ini_get("classes." . $arr["obj_inst"]->subclass() . ".name") . " > " . $this->grplist[$arr["request"]["meta"]]["caption"];
		$arr["prop"]["captionside"] = "top";

		// table layout
		$t = $arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"callback" => array(&$this, "callb_name"),
			"callb_pass_row" => true,
		));
		$t->define_field(array(
			"name" => "id",
			"caption" => t("ID"),
			"sortable" => 1,
		));

		foreach($groups_list->arr() as  $group_obj)
		{
			$t->define_field(array(
				"name" => $group_obj->id(),
				"caption" => html::href(array(
					"caption" => $group_obj->name(),
					"url" => "#",
					"onClick" => "aw_sel_chb(document.changeform,\"[".$group_obj->id()."]\");"
				))
			));
		}

		// distribute props by group
		$by_group = array();

		if (is_array($this->grplist))
		{
			foreach($this->grplist as $key => $val)
			{
				if (!is_numeric($key))
				{
					$by_group[$key] = array();
				}
			}
		}

		if (is_array($this->prplist))
		{
			foreach($this->prplist as $property)
			{
				if (!empty($property["group"]))
				{
					if (!is_array($property["group"]))
					{
						$by_group[$property["group"]][] = $property;
					}
					else
					{
						foreach($property["group"] as $gkey)
						{
							$by_group[$gkey][] = $property;
						}
					}
				}
			}
		}

		// bc for when all groups were always shown instead of only those selected by connection
		$show_to_groups = $arr["obj_inst"]->meta("show_to_groups");

		if(!$arr["request"]["meta"]) $arr["request"]["meta"] = $arr["obj_inst"]->meta("group_to_show");

		if (!is_array($show_to_groups))
		{ // no backward compatibility needed
			$arr["obj_inst"]->set_meta("has_show_to_groups_by_rel", true);
		}

		$arr["obj_inst"]->set_meta("group_to_show",$arr["request"]["meta"]);
		$arr["obj_inst"]->save();

		// data
		foreach($by_group[$arr["request"]["meta"]] as $property)
		{
			$prop_name = $property["name"];
			$row = array(
				"id" => $prop_name,
				"name" => $property["caption"],
			);

			foreach($groups_list->ids() as $gid)
			{
				$checked = 1;

				if(isset($show_to_groups[$prop_name]))
				{
					if(empty($show_to_groups[$prop_name][$gid]))
					{
						$checked = null;
					}
				}

				$row[$gid] = html::hidden(array("name" => "show_to_groups_chk[".$prop_name."][".$gid."]", "value" => 1)) . html::checkbox(array(
					"name" => "show_to_groups[".$prop_name."][".$gid."]",
					"value" => 1,
					"checked" => $checked,
				));
			}

			$t ->define_data($row);
		}

		$t->set_sortable(false);

		return PROP_OK;
	}


	function _init_trans_tbl($t, $o, $req, $str = "Omadus")
	{
		$l = new languages();
		$orig_ld = $l->fetch($o->lang_id(), false);

		$lid = substr($req["group"], 5);
		$tmp = $l->get_list(array("ignore_status" => 1));
		unset($tmp[$o->lang_id()]);
		$this->lang_inf = array(
			"ids" => array_keys($tmp),
			"names" => array_values($tmp)
		);
		$lid = $this->lang_inf["ids"][max((int)$lid-1,0)];
		$ld = $l->fetch($lid, false);

		$t->define_field(array(
			"name" => "property",
			"caption" => $str,
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "orig_str",
			"caption" => sprintf(t("Caption (%s)"), $orig_ld["name"]),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "trans_str",
			"caption" => sprintf(t("Caption (%s)"), $ld["name"]),
			"align" => "center"
		));

		if($str === "Omadus")
		{
			$t->define_field(array(
				"name" => "orig_comment",
				"caption" => sprintf(t("Kommentaar (%s)"), $orig_ld["name"]),
				"align" => "center"
			));

			$t->define_field(array(
				"name" => "trans_comment",
				"caption" => sprintf(t("Kommentaar (%s)"), $ld["name"]),
				"align" => "center"
			));
		}

		return $ld;
	}

	function _trans_tbl($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$ld = $this->_init_trans_tbl($t, $arr["obj_inst"], $arr["request"]);
		$lid = $ld["acceptlang"];

		$trans = $arr["obj_inst"]->meta("translations");

		$ps = $arr["obj_inst"]->meta("cfg_proplist");
		uasort($ps, create_function('$a, $b','return (isset($a["ord"]) ? $a["ord"] : 0) - (isset($b["ord"]) ? $b["ord"] : 0);'));
		foreach($ps as $pn => $pd)
		{
			$capt = $pd["type"] === "text" ? (isset($pd["value"]) ? $pd["value"] : "") : $pd["caption"];
			$comm = isset($pd["comment"]) ? $pd["comment"] : "";
			$v = $trans[$lid][$pn];
			$v2 = "";

			if (trim($v) === "" and isset($trans[$lid][$pn]))
			{
				$v = $trans[$lid][$pn];
				$v2 = $trans[$lid][$pn."_comment"];
			}

			$t->define_data(array(
				"property" => $pn,
				"orig_str" => $capt,
				"trans_str" => html::textbox(array(
					"name" => "dat[".$lid."][$pn]",
					"value" => $v
				)),
				"orig_comment" => $comm,
				"trans_comment" => html::textbox(array(
					"name" => "dat[".$lid."][{$pn}_comment]",
					"value" => $v2
				))
			));
		}
		$t->set_sortable(false);
	}

	function _trans_tbl_grps($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$ld = $this->_init_trans_tbl($t, $arr["obj_inst"], $arr["request"], "Tab");
		$lid = $ld["acceptlang"];

		$trans = $arr["obj_inst"]->meta("grp_translations");

		$ps = $arr["obj_inst"]->meta("cfg_groups");
		foreach($ps as $pn => $pd)
		{
			$capt = $pd["caption"];
			$v = $trans[$lid][$pn];

			if (trim($v) == "")
			{
				$v = $trans[$lid][$pn];
			}

			$t->define_data(array(
				"property" => $pn,
				"orig_str" => $capt,
				"trans_str" => html::textbox(array(
					"name" => "dat[".$lid."][$pn]",
					"value" => $v
				))
			));
		}
	}

	function _trans_tbl_lays($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$ld = $this->_init_trans_tbl($t, $arr["obj_inst"], $arr["request"], "Layout");
		$lid = $ld["acceptlang"];

		$trans = $arr["obj_inst"]->meta("layout_translations");

		$ps = $arr["obj_inst"]->meta("cfg_layout");
		foreach($ps as $pn => $pd)
		{
			$capt = $pd["area_caption"];
			$v = $trans[$lid][$pn];

			if (trim($v) == "")
			{
				$v = $trans[$lid][$pn];
			}

			$t->define_data(array(
				"property" => $pn,
				"orig_str" => $capt,
				"trans_str" => html::textbox(array(
					"name" => "dat[".$lid."][$pn]",
					"value" => $v
				))
			));
		}
		$t->set_caption(t("Layoutid"));
	}

	function _trans_tbl_table_capts($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$ld = $this->_init_trans_tbl($t, $arr["obj_inst"], $arr["request"], t("Tabelite pealkirjad"));
		$lid = $ld["acceptlang"];

		$trans = $arr["obj_inst"]->meta("tbl_capt_translations");

		$ps = $arr["obj_inst"]->meta("cfg_proplist");
		foreach($ps as $pn => $pd)
		{
			$capt = isset($pd["emb_tbl_caption"]) ? $pd["emb_tbl_caption"] : "";
			$v = $trans[$lid][$pn."_tbl_capt"];

			if (trim($v) == "")
			{
				$v = $trans[$lid][$pn."_tbl_capt"];
			}

			$t->define_data(array(
				"property" => $pn,
				"orig_str" => $capt,
				"trans_str" => html::textbox(array(
					"name" => "dat[".$lid."][".$pn."_tbl_capt]",
					"value" => $v
				))
			));
		}
		$t->set_caption(t("Tabeli tulpade pealkirjad"));
	}

	function gen_default_table($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Omadus"),
		));
		$t->define_field(array(
			"name" => "type",
			"caption" => t("T&uuml;&uuml;p"),
		));
		$t->define_field(array(
			"name" => "value",
			"caption" => t("V&auml;&auml;rtus"),
		));

		$props = $this->get_props_from_cfgform(array(
			"id" => $arr["obj_inst"]->id(),
		));


		foreach($props as $prop)
		{
			if ($prop["type"] === "checkbox")
			{
				$prx = $this->prplist[$prop["name"]];
				// so, how do I determine whether this property has a default or not?
				// kas ma pean kuhugi kirja panema selle asja?

				// eino, asi on ikka n&auml;itamises ju eksole. mitte salvestamises
				$pname = $arr["prop"]["name"];
				$t->define_data(array(
					"name" => $prop["caption"] . "(" . $prop["name"] . ")",
					"type" => $prop["type"],
					"value" => html::checkbox(array(
						"name" => $pname."[".$prop["name"]."]",
						"value" => 1,
						//"checked" => $this->prplist[$prop["name"]]["default"] == "" ? $prop["default"] : ($this->prplist[$prop["name"]]["default"] == 1),
						"checked" => !empty($this->prplist[$prop["name"]]["default"]),
					)),
				));
			}
		}
	}

	function gen_controller_props($arr)
	{
		$controllers = $arr["obj_inst"]->meta("controllers");
		$retval = array();
		foreach ($this->prplist as $prop)
		{
			$caption = empty($prop["caption"]) ? $prop["name"] : $prop["caption"];
			$retval[] = array(
				"name" => "controllers[".$prop["name"]."]",
				"caption" => $caption,
				"type" => "relpicker",
				"multiple" => 1,
				"size" => 2,
				"reltype" => "RELTYPE_CONTROLLER",
				"value" => $controllers[$prop["name"]],
			);
		}
		return  $retval;
	}

	function gen_view_controller_props($arr)
	{
		$controllers = $arr["obj_inst"]->meta("view_controllers");
		$retval = array();
		foreach ($this->prplist as $prop)
		{
			$caption = empty($prop["caption"]) ? $prop["name"] : $prop["caption"];

			$retval[] = array(
				"name" => "view_controllers[".$prop["name"]."]",
				"caption" => $caption,
				"type" => "relpicker",
				"multiple" => 1,
				"size" => 2,
				"reltype" => "RELTYPE_VIEWCONTROLLER",
				"value" => $controllers[$prop["name"]],
			);
		}
		return  $retval;
	}

	function callback_pre_edit($arr)
	{
		$this->_init_cfgform_data($arr["obj_inst"]);
	}

	function do_sysdefaults($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "act",
			"caption" => t("S&uuml;steemi default"),
			"align" => "center",
			"width" => 85,
		));

		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
		));

		$t->define_field(array(
			"name" => "group",
			"caption" => t("Tab"),
		));

		$o = $arr["obj_inst"];
		$active = 0;

		$active = $this->get_sysdefault(array("clid" => $o->subclass()));

		$ol = new object_list(array(
			"class_id" => $this->clid,
			"subclass" => $o->subclass(),
			"lang_id" => array(),
		));

		$t->set_sortable(false);

		$t->define_data(array(
			"act" => html::radiobutton(array(
				"name" => "sysdefault",
				"value" => 0,
				"checked" => 0 == $active,
			)),
			"name" => t("&Auml;ra kasuta vormi"),
		));

		foreach($ol->arr() as $o)
		{
			$oid = $o->id();
			$t->define_data(array(
				"act" => html::radiobutton(array(
					"name" => "sysdefault",
					"value" => $oid,
					"checked" => $oid == $active,
				)),
				"name" => $o->name(),
			));
		}
	}

	function _init_cfgform_data($obj)
	{
		$this->_init_properties($obj->prop("subclass"));
		$this->grplist = $this->cfg_groups = safe_array($obj->meta("cfg_groups"));
		$this->prplist = $this->cfg_proplist = safe_array($obj->meta("cfg_proplist"));
		$this->layout = $this->cfg_layout = safe_array($obj->meta("cfg_layout"));

		// fix old cfgform objects where cfg_proplist contained only configured attributes
		if (!$obj->meta("cfg_proplist_format_updated"))
		{
			foreach ($this->cfg_proplist as $name => $cfg)
			{
				$this->cfg_proplist[$name] = safe_array($this->cfg_proplist[$name]) + safe_array($this->all_props[$name]);
				if(is_array($this->cfg_proplist[$name]) and is_array($this->all_props[$name]) and empty($this->cfg_proplist[$name]["type"]))
				{
					$this->cfg_proplist[$name]["type"] = $this->all_props[$name]["type"];
				}
			}

			$obj->set_meta("cfg_proplist", $this->cfg_proplist);
			$obj->set_meta("cfg_proplist_format_updated", "1");
			$obj->save();
		}
	}

	function _init_properties($class_id)
	{
		if (aw_ini_isset("classes.{$class_id}.file"))
		{
			$fl = aw_ini_get("classes.{$class_id}.file");
			$inst = get_instance($fl);
			$this->all_props = $inst->get_all_properties();//XXX: get_all_properties on deprecated meetod
		}
		else
		{
			//TODO: vigane v6i vana clid, veateade, ...
		}
	}

	function set_property($arr)
	{
		$data = &$arr["prop"];
		$o = $arr["obj_inst"];
		$retval = PROP_OK;

		switch($data["name"])
		{
			// add grp form props
			case "orb_settings":
				$this->_set_orb_settings($arr);
				break;

			case "add_grp_parent":
				if (!empty($arr["request"]["cfgform_add_grp"]))
				{
					$this->cfgform_add_grp_ok = $this->add_group($arr["obj_inst"], $arr["request"]["add_grp_name"], $data["value"], $arr["request"]["add_grp_caption"]);

					if (true !== $this->cfgform_add_grp_ok)
					{
						$data["error"] = $this->cfgform_add_grp_ok;
						$retval = PROP_ERROR;
					}
				}
				break;

			case "add_grp_name":
			case "add_grp_caption":
				$retval = class_base::PROP_IGNORE;
				break;
			// END add grp form props

			case "transl":
				$this->trans_save($arr, $this->trans_props);
				break;

			case "group_movement":
				$arr["obj_inst"]->set_meta("buttons", $arr["request"]["bts"]);
				break;

			case "gen_submit_controllers":
				$arr["obj_inst"]->set_meta("controllers", $arr["request"]["controllers"]);
				break;

			case "gen_view_controllers":
				$arr["obj_inst"]->set_meta("view_controllers", $arr["request"]["view_controllers"]);
				break;

			case "sysdefault":
				$ol = new object_list(array(
					"class_id" => $this->clid,
					"subclass" => $o->subclass(),
					"lang_id" => array(),
				));
				foreach ($ol->arr() as $item)
				{
					if ($item->flag(OBJ_FLAG_IS_SELECTED) && $item->id() != $data["value"])
					{
						$item->set_flag(OBJ_FLAG_IS_SELECTED, false);
						$item->save();
					}
					elseif ($item->id() == $data["value"] && !$item->flag(OBJ_FLAG_IS_SELECTED))
					{
						$item->set_flag(OBJ_FLAG_IS_SELECTED, true);
						$item->save();
					};
				};
				break;

			case "cfg_proplist":

			case "cfg_groups":
				$data["value"] = urldecode($data["value"]);
				if (empty($data["value"]))
				{
					$retval = PROP_IGNORE;
				};
				break;

			case "xml_definition":
				if ($_FILES[$data["name"]]["type"] !== "text/xml")
				{
					$retval = PROP_IGNORE;
				}
				else
				if (!is_uploaded_file($_FILES[$data["name"]]["tmp_name"]))
				{
					$retval = PROP_IGNORE;
				}
				else
				{
					$contents = $this->get_file(array(
						"file" => $_FILES[$data["name"]]["tmp_name"],
					));
					if ($contents)
					{
						$data["value"] = $contents;
					};
					$retval = $this->_load_xml_definition($contents);
				}
				break;

			case "subclass":
				// do not overwrite subclass if it was not in the form
				// hum .. this is temporary fix of course. yees --duke
				if (empty($arr["request"]["subclass"]))
				{
					$retval = PROP_IGNORE;
				}
				// cfg_proplist is in "formdata" only if this a serialized object
				// being unserialized. for example, if we are copying this object
				// over xml-rpc
				elseif ($arr["new"] && empty($arr["request"]["cfg_proplist"]))
				{
					$this->cff_init_from_class($arr["obj_inst"], $arr["request"]["subclass"], false);
				}
				break;

			case "availprops":
				$this->add_new_properties($arr);
				break;

			case "layout":
				$this->save_layout($arr);
				break;

			case "layouts_table":
				$this->_save_layouts_table($arr);
				break;

			case "edit_groups":
				$this->_init_cfgform_data($arr["obj_inst"]);
				$this->update_groups($arr);
				break;

			case "default_table":
				$this->default_values = $data["value"];
				$this->_init_cfgform_data($arr["obj_inst"]);
				$this->cfg_proplist = $this->prplist;
				break;

			case "trans_tbl":
				$l = new languages();
				$trans = safe_array($arr["obj_inst"]->meta("translations"));
				foreach(safe_array($arr["request"]["dat"]) as $lid => $ldat)
				{
					$ld = $l->fetch($l->get_id_for_code($lid), false);
					foreach(safe_array($ldat) as $pn => $c)
					{
						$ldat[$pn] = $c;
					}
					$trans[$lid] = $ldat;
				}
				$arr["obj_inst"]->set_meta("translations", $trans);
				break;

			case "trans_tbl_grps":
				$trans = safe_array($arr["obj_inst"]->meta("grp_translations"));
				foreach(safe_array($arr["request"]["dat"]) as $lid => $ldat)
				{
					$ld = languages::fetch(languages::get_id_for_code($lid), false);
					foreach(safe_array($ldat) as $pn => $c)
					{
						$ldat[$pn] = $c;
					}
					$trans[$lid] = $ldat;
				}
				$arr["obj_inst"]->set_meta("grp_translations", $trans);
				break;

			case "trans_tbl_lays":
				$trans = safe_array($arr["obj_inst"]->meta("layout_translations"));
				foreach(safe_array($arr["request"]["dat"]) as $lid => $ldat)
				{
					$ld = languages::fetch(languages::get_id_for_code($lid), false);
					foreach(safe_array($ldat) as $pn => $c)
					{
						$ldat[$pn] = $c;
					}
					$trans[$lid] = $ldat;
				}
				$arr["obj_inst"]->set_meta("layout_translations", $trans);
				break;

			case "trans_tbl_table_capts":
				$trans = safe_array($arr["obj_inst"]->meta("tbl_capt_translations"));
				foreach(safe_array($arr["request"]["dat"]) as $lid => $ldat)
				{
					$ld = languages::fetch(languages::get_id_for_code($lid), false);
					foreach(safe_array($ldat) as $pn => $c)
					{
						$ldat[$pn] = $c;
					}
					$trans[$lid] = $ldat;
				}
				$arr["obj_inst"]->set_meta("tbl_capt_translations", $trans);
				break;

			case "props_list":
				$this->save_show_to_groups($arr);
				break;

			case "tables_table":
				$this->save_tables_conf($arr);
				break;

			case "tables_controller":
				if($tbl = $arr["request"]["chtbl"])
				{
					$conf = $arr["obj_inst"]->meta("tbl_config");
					$conf[$tbl]["controllers"] = $arr["request"]["tables_controller"];
					$arr["obj_inst"]->set_meta("tbl_config", $conf);
				}
				else
				{
					return PROP_IGNORE;
				}
				break;
		}
		return $retval;
	}

	private function save_show_to_groups($arr = array())
	{
		$show_to_groups = $arr["obj_inst"]->meta("show_to_groups");
		foreach($arr["request"]["show_to_groups_chk"] as $key => $val)
		{
			$show_to_groups[$key] = $arr["request"]["show_to_groups"][$key];
		}
		$arr["obj_inst"]->set_meta("show_to_groups" , $show_to_groups);
	}

	private function _load_xml_definition($contents)
	{
		// right now I can load whatever I want, but I really should validate that stuff
		// first .. and keep in mind that I want to have as many relation pickers
		// as I want to.
		$cfgu = new cfgutils();
		list($proplist,$grplist) = $cfgu->parse_cfgform(array("xml_definition" => $contents));
		$this->cfg_proplist = $proplist;
		$this->cfg_groups = $grplist;
	}

	function callback_pre_save($arr)
	{
		$obj_inst = $arr["obj_inst"];
		// if we are unzerializing the object, then we need to set the
		// subclass as well.
		if (isset($arr["request"]["subclass"]))
		{
			$obj_inst->set_prop("subclass",$arr["request"]["subclass"]);
		}

		$this->_save_cfg_groups($obj_inst);
		$this->_save_cfg_props($obj_inst);
		$this->_save_cfg_layout($obj_inst);

		return true;
	}

	function callback_get_transl($arr)
	{
		return $this->trans_callback($arr, $this->trans_props);
	}

	function callback_mod_tab($arr)
	{
		if ($arr["id"] === "transl" && aw_ini_get("user_interface.content_trans") != 1)
		{
			return false;
		}
		if (!isset($this->lang_inf))
		{
			$tmp = languages::get_list(array("ignore_status" => 1));
			unset($tmp[$arr["obj_inst"]->lang_id()]);
			$this->lang_inf = array(
				"ids" => array_keys($tmp),
				"names" => array_values($tmp)
			);
		}

		if (substr($arr["id"], 0, 5) == "lang_")
		{
			$num = substr($arr["id"], 5);

			$arr["caption"] = $this->lang_inf["names"][$num-1];
			if ($num > count($this->lang_inf["ids"]))
			{
				return false;
			}
		}
		return true;
	}

	function callback_mod_reforb(&$arr, $request)
	{
		$arr["cfgform_add_grp"] = isset($request["cfgform_add_grp"]) ? $request["cfgform_add_grp"] : "";
	}

	function callback_mod_retval(&$arr)
	{
		if (!empty($arr["request"]["cfgform_add_grp"]))
		{
			if (true === $this->cfgform_add_grp_ok)
			{
				unset($arr["args"]["cfgform_add_grp"]);
				unset($arr["request"]["cfgform_add_grp"]);
			}
			else
			{
				$arr["args"]["cfgform_add_grp"] = $arr["request"]["cfgform_add_grp"];
			}
		}

		if (!empty($arr["request"]["chtbl"]))
		{
			$arr["args"]["chtbl"] = $arr["request"]["chtbl"];
		}
	}

/* replaced by self::_sort_groups*/
	function sort_grplist()
	{
		$order = array();
		$grps = array();
		foreach($this->grplist as $key => $item)
		{
			$order[$key] = isset($item["ord"]) ? $item["ord"] : 0;
		}
		asort($order);
		foreach($order as $key => $val)
		{
			$grps[$key] = $this->grplist[$key];
		}
		$this->grplist = $grps;
	}
/**/

	// Sorts meta cfg_groups grplist by ord, retains original order where ord not defined.
	// Places unordered subgroups after their parents
	// Separately handles 2-level structure -- parent groups and subgroups.
	private function _sort_groups()
	{
		// separate unordered, parent and subgroups
		$pg = $sg = $pgo = $sgo = array(); // (unordered parent groups, unordered subgroups, ordered parent groups, ordered subgroups)

		foreach($this->cfg_groups as $grp_name => $grp_data)
		{
			$parent = empty($grp_data["parent"]) ? 0 : $grp_data["parent"];

			if (empty($grp_data["ord"]))
			{
				if (0 === $parent)
				{
					$pg[] = $grp_name;
				}
				else
				{
					$sg[$parent][] = $grp_name;
				}
			}
			else
			{
				if (0 === $parent)
				{
					$pgo[$grp_data["ord"]] = $grp_name;
				}
				else
				{
					$sgo[$parent][$grp_data["ord"]] = $grp_name;
				}
			}
		}

		// sort groups
		ksort($pgo);

		foreach ($sgo as $parent => $tmp)
		{
			ksort($sgo[$parent]);
		}

		// merge groups back together
		$grplist_tmp_sorted = array();

		foreach ($pg as $grp_name)
		{
			$grplist_tmp_sorted[$grp_name] = $this->cfg_groups[$grp_name];

			if (isset($sg[$grp_name]) and count($sg[$grp_name]))
			{
				foreach ($sg[$grp_name] as $grp_name2)
				{
					$grplist_tmp_sorted[$grp_name2] = $this->cfg_groups[$grp_name2];
				}
			}

			if (isset($sgo[$grp_name]) and count($sgo[$grp_name]))
			{
				foreach ($sgo[$grp_name] as $grp_name2)
				{
					$grplist_tmp_sorted[$grp_name2] = $this->cfg_groups[$grp_name2];
				}
			}
		}

		foreach ($pgo as $grp_name)
		{
			$grplist_tmp_sorted[$grp_name] = $this->cfg_groups[$grp_name];

			if (count($sg[$grp_name]))
			{
				foreach ($sg[$grp_name] as $grp_name2)
				{
					$grplist_tmp_sorted[$grp_name2] = $this->cfg_groups[$grp_name2];
				}
			}

			if (count($sgo[$grp_name]))
			{
				foreach ($sgo[$grp_name] as $grp_name2)
				{
					$grplist_tmp_sorted[$grp_name2] = $this->cfg_groups[$grp_name2];
				}
			}
		}

		$this->cfg_groups = $grplist_tmp_sorted;
	}

	private function _save_cfg_groups($o)
	{
		if (isset($this->cfg_groups))
		{
			$this->_sort_groups();
			$o->set_meta("cfg_groups", $this->cfg_groups);
			$o->set_meta("cfg_groups_sorted", 1);
		}
	}

	private function _save_cfg_props($o)
	{
		if (isset($this->cfg_proplist))
		{
			$tmp = array();
			$cnt = 0;
			foreach($this->cfg_proplist as $key => $val)
			{
				if (empty($val["ord"]))
				{
					$cnt++;
					$val["tmp_ord"] = $cnt;
				}

				$tmp[$key] = $val;
			}

			uasort($tmp, array($this, "__sort_props_by_ord"));

			$cnt = 0;
			$this->cfg_proplist = array();

			foreach($tmp as $key => $val)
			{
				unset($val["tmp_ord"]);

				if (!empty($this->default_values[$key]))
				{
					$val["default"] = $this->default_values[$key];
				}
				else
				{
					unset($val["default"]);
				}

				$this->cfg_proplist[$key] = $val;
			}

			$o->set_meta("cfg_proplist",$this->cfg_proplist);
		}
	}

	private function _save_cfg_layout($o)
	{
		if (isset($this->cfg_layout))
		{
			$o->set_meta("cfg_layout", $this->cfg_layout);
		}
	}

	private function _tables_tb(&$arr)
	{
		$tb = $arr["prop"]["vcl_inst"];
		$tables = $this->get_tbl_list();
		if(count($tables))
		{
			$tables = array_merge(array(0=>t("--vali--")), $tables);
			$tb->add_cdata(t("Tabel:").html::select(array(
				"name" => "chtbl",
				"options" => $tables,
				"value" => ($s = $arr["request"]["chtbl"])? $s : 0,
			)));
			$tb->add_save_button();
			if($arr["request"]["chtbl"])
			{
				$tb->add_button(array(
					"action" => "remove_tbl_field",
					"img" => "delete.gif",
					"tooltip" => t("Eemalda valitud v&auml;ljad"),
				));
			}
		}
		else
		{
			$tb->add_cdata(t("Klass ei sisalda konfigureeritavaid tabeleid."));
		}
	}

	/**
	@attrib name=remove_tbl_field
	**/
	function remove_tbl_field($arr)
	{
		$o = obj($arr["id"]);
		$cfg = $o->meta("tbl_config");
		foreach($arr["sel"] as $fieldid)
		{
			unset($cfg[$arr["chtbl"]]["fields"][$fieldid]);
		}
		$o->set_meta("tbl_config", $cfg);
		$o->save();
		return $arr["post_ru"];
	}

	private function get_tbl_list()
	{
		$tables = array();
		foreach($this->cfg_proplist as $property)
		{
			if($property["type"] == "table" && $property["configurable"])
			{
				$tables[$property["name"]] = $property["caption"] ? $property["caption"] : $property["name"];
			}
		}
		return $tables;
	}

	private function _tables_tbl($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->set_caption(t("V&auml;ljad"));

	//$arr["obj_inst"]->set_meta("tbl_config", null);$arr["obj_inst"]->save();

		if(isset($arr["request"]["chtbl"]) and ($tbl = $arr["request"]["chtbl"]))
		{
			$property = $this->cfg_proplist[$tbl];
			if($property["type"] !== "table" || !$property["configurable"])
			{
				return;
			}
			$this->_init_tables_tbl($t);
			$conf = $arr["obj_inst"]->meta("tbl_config");
			if(is_array($conf[$tbl]["fields"]))
			{
				if($conf[$tbl]["chooser"])
				{
					$t->define_data($this->get_tables_tbl_data(array(
						"field" => array(
							"name" => "add",
						),
						"tbl" => $tbl,
						"add" => 1,
						"conf" => $conf,
					)));
					$t->set_rgroupby(array("add" => "add"));
					$t->set_default_sortby("add");
					$t->set_default_sorder("asc");
					$t->sort_by();
					$t->define_chooser(array(
						"field" => "id",
						"name" => "sel",
					));
				}
				foreach($conf[$tbl]["fields"] as $field)
				{
					$order = $field["order"]?$field["order"]:$i/10;
					$t->define_data($this->get_tables_tbl_data(array(
						"field" => $field,
						"tbl" => $tbl,
					)));
				}
			}
			else
			{
				$arr["prop"]["value"] = sprintf(t("Tabelit %s pole n&auml;idatud."), $tbl);
			}
		}
	}

	private function get_tables_tbl_data($arr)
	{
		extract($arr);
		if($add)
		{
			$n = "userdef";
			$i = 1;
			while(true)
			{
				$fname = $n.$i;
				if($conf[$tbl]["fields"][$fname])
				{
					$i++;
				}
				else
				{
					break;
				}
			}
			$name = html::hidden(array(
				"name" => "tbl_conf[".$tbl."][fields][".$field["name"]."][newname]",
				"value" => $fname,
			)).$fname;
		}
		else
		{
			$name = $field["name"];
		}
		$data = array(
			"caption" => html::textbox(array(
				"value" => $field["caption"],
				"name" => "tbl_conf[".$tbl."][fields][".$field["name"]."][caption]",
				"size" => 20,
			)),
			"name" => $name,
			"hide" => html::checkbox(array(
				"name" => "tbl_conf[".$tbl."][fields][".$field["name"]."][hide]",
				"value" => 1,
				"checked" => $field["hide"],
			)),
			"ord" => html::textbox(array(
				"value" => $field["order"],
				"name" => "tbl_conf[".$tbl."][fields][".$field["name"]."][order]",
				"size" => 3,
			)),
			"filter" => html::select(array(
				"name" => "tbl_conf[".$tbl."][fields][".$field["name"]."][filter_type]",
				"options" => array(
					0 => t("--vali--"),
					"text" => t("Tekstikast"),
					"automatic" => t("Valik"),
				),
				"value" => $field["filter_type"],
			)),
			"userprop" => ($field["userdef"] || $add)?html::textbox(array(
				"name" => "tbl_conf[".$tbl."][fields][".$field["name"]."][userprop]",
				"size" => 9,
				"value" => $field["userprop"],
			)):'',
			"align" => html::select(array(
				"name" => "tbl_conf[".$tbl."][fields][".$field["name"]."][align]",
				"options" => array(
					0 => t("--vali--"),
					"left" => t("Vasakul"),
					"center" => t("Keskel"),
					"right" => t("Paremal"),
				),
				"value" => $field["align"],
			)),
			"add" => $add?t("Lisa uus") : ($field["userdef"] ? " ".t("Kasutaja defineeritud") : ""),
			"id" => $field["userdef"]?$field["name"]:null,
		);
		return $data;
	}

	function save_tables_conf($arr)
	{
		$conf = $arr["obj_inst"]->meta("tbl_config");
		foreach($arr["request"]["tbl_conf"] as $tbl => $data)
		{
			foreach($data["fields"] as $name => $field)
			{
				if($name === "add")
				{
					foreach($field as $var => $val)
					{
						if($val && $var !== "newname")
						{
							$new = 1;
						}
						elseif(!$val)
						{
							unset($field[$var]);
						}
					}
					if($new)
					{
						$field["userdef"] = 1;
						$field["name"] = $field["newname"];
						if($field["filter_type"])
						{
							$field["filter"] = $field["filter_type"];
						}
						unset($field["newname"]);
						$conf[$tbl]["fields"][$field["name"]] = $field;
					}
					continue;
				}
				foreach($field as $var => $val)
				{
					$conf[$tbl]["fields"][$name][$var] = $val;
					if(empty($val))
					{
						unset($conf[$tbl]["fields"][$name][$var]);
					}
				}
				if($field["filter_type"])
				{
					$conf[$tbl]["fields"][$name]["filter"] = $field["filter_type"];
				}
				else
				{
					unset($conf[$tbl]["fields"][$name]["filter"]);
				}
				$conf[$tbl]["fields"][$name]["hide"] = $field["hide"];
			}
		}
		$arr["obj_inst"]->set_meta("tbl_config", $conf);
		$arr["obj_inst"]->save();
	}

	private function _init_tables_tbl($t)
	{
		$t->define_field(array(
			"name" => "caption",
			"caption" => t("Pealkiri"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "userprop",
			"caption" => t("V&auml;&auml;rtus"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "align",
			"caption" => t("Joondus"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "filter",
			"caption" => t("Filter"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "ord",
			"caption" => t("Jrk"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "hide",
			"caption" => t("Peida"),
			"align" => "center",
		));
	}

	private function _init_layout_tbl($t)
	{
		$t->define_field(array(
			"name" => "ord",
			"caption" => t("Jrk."),
			"numeric" => 1
		));

		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
		));

		$t->define_field(array(
			"name" => "area_caption",
			"caption" => t("Pealkiri"),
		));

		$t->define_field(array(
			"name" => "type",
			"caption" => t("T&uuml;&uuml;p"),
		));

		$t->define_field(array(
			"name" => "group",
			"caption" => t("Tab"),
		));

		$t->define_field(array(
			"name" => "parent",
			"caption" => t("Parent"),
		));

		$t->define_field(array(
			"name" => "width",
			"caption" => t("Laiusjaotus"),
		));

		$t->define_field(array(
			"name" => "closeable",
			"caption" => t("Suletav"),
		));

		$t->define_chooser(array(
			"name" => "selection",
			"field" => "id",
		));

		$t->set_numeric_field("ordnr");
		$t->set_default_sortby("ordnr");
		$t->set_default_sorder("asc");
	}

	function _layout_tbl($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_layout_tbl($t);
		$this_o = $arr["obj_inst"];

		// available groups
		$groups = array();

		foreach ($this->grplist as $name => $data)
		{
			$groups[$name] = $data["caption"] . " [" . $name . "]";
		}

		// available layout types
		$types = array(
			"hbox" => t("Horis."),
			"vbox" => t("Vertik.")
		);

		// parent layouts
		$parents = array();
		$all_parents = array();

		foreach ($this->layout as $name => $data)
		{
			$all_parents[$name] = $name;
		}

		// remove invalid parent options
		foreach ($this->layout as $name => $data)
		{
			$parents[$name] = $all_parents;
			unset($parents[$name][$name]);
			$parent = $this->layout[$data["parent"]]["parent"];

			while (!empty($parent))
			{
				unset($parents[$name][$parent]);
				$parent = $this->layout[$parent]["parent"];
			}
		}

		$ord = 0;

		foreach ($this->layout as $name => $data)
		{
			$t->define_data(array(
				"id" => $name,
				"ordnr" => ++$ord,
				"ord" =>   html::textbox (array (
					"name" => "cfglayoutinfo-ord[" . $name . "]",
					"size" => "2",
					"textsize" => "12px",
					"value" => $ord,
				)),
				"name" => (0 === strpos($name, $this->default_new_layout_name)) ? html::textbox (array (
					"name" => "cfglayoutinfo-name[" . $name . "]",
					"size" => "15",
					"textsize" => "12px",
					"value" => $name,
				)) : $name,
				"area_caption" =>  html::textbox (array (
					"name" => "cfglayoutinfo-area_caption[" . $name . "]",
					"size" => "25",
					"textsize" => "12px",
					"value" => $data["area_caption"],
				)),
				"group" => html::select(array(
					"name" => "cfglayoutinfo-group[" . $name . "]",
					"options" => $groups,
					"textsize" => "12px",
					"value" => $data["group"]
				)),
				"parent" => html::select(array(
					"name" => "cfglayoutinfo-parent[" . $name . "]",
					"options" => array("" => "") + $parents[$name],
					"textsize" => "12px",
					"value" => $data["parent"]
				)),
				"type" =>  html::select(array(
					"name" => "cfglayoutinfo-type[" . $name . "]",
					"options" => $types,
					"textsize" => "12px",
					"value" => $data["type"],
					"onchange" => "
						tmp = this.name.split('[');
						elname = tmp[1];
						tmp = document.getElementsByName('cfglayoutinfo-width[' + elname);
						w1el = tmp[0];

						if('hbox' == this.value)
						{
							w1el.disabled = false;
						}
						else if('vbox' == this.value)
						{
							w1el.disabled = true;
						}
					"// enable width element when hbox selected, disable if vbox
				)),
				"width" => html::textbox (array (
					"name" => "cfglayoutinfo-width[" . $name . "]",
					"size" => "6",
					"textsize" => "12px",
					"value" => $data["width"],
					"disabled" => $data["type"] === "vbox"
				)),
				"closeable" => html::checkbox (array(
					"name" => "cfglayoutinfo-closeable[" . $name . "]",
					"checked" => $data["closeable"]
				)),
			));
		}
	}

	function _layout_tb($arr)
	{
		$toolbar = $arr["prop"]["toolbar"];
		$toolbar->add_button(array(
			"name" => "new",
			"tooltip" => t("Lisa uus layout"),
			"action" => "add_new_layout",
			"img" => "new.gif",
		));

		// save
		$toolbar->add_button(array(
			"name" => "save",
			"url" => "javascript:submit_changeform()",
			"img" => "save.gif",
			"tooltip" => t("Salvesta"),
		));

		$toolbar->add_button(array(
			"name" => "delete",
			"tooltip" => t("Kustuta valitud"),
			"action" => "delete_layouts",
			"confirm" => t("Kustutada valitud layoudid?"),
			"img" => "delete.gif",
		));
	}

	function _save_layouts_table($arr)
	{
		$this_o = $arr["obj_inst"];
		$this->_init_cfgform_data($this_o);

		foreach ($this->cfg_layout as $name => $data)
		{
			// set parent group
			$group = $arr["request"]["cfglayoutinfo-group"][$name];

			if (!empty($group) and array_key_exists($group, $this->grplist))
			{
				$data["group"] = $group;

				// if layout has properties, set their parent. neccessary because cb doesn't process props and layouts together.
				$this->change_layout_props_grp($name, $group);

				// if layout has child layouts, set their parent group
				foreach ($this->cfg_layout as $child_name => $child_data)
				{
					if ($name === $child_data["parent"])
					{
						$this->cfg_layout[$child_name]["group"] = $group;
					}
				}
			}

			// set parent layout
			$parent = $arr["request"]["cfglayoutinfo-parent"][$name];
			if (!empty($parent) and array_key_exists($parent, $this->cfg_layout))
			{
				$data["parent"] = $parent;

				// change group to new parent layout's one
				$data["group"] = $this->cfg_layout[$parent]["group"];

				// if layout has properties, change their parent group. neccessary because cb doesn't process props and layouts together.
				$this->change_layout_props_grp($name, $this->cfg_layout[$parent]["group"]);
			}
			else
			{
				unset($data["parent"]);
			}

			//
			if (!empty($arr["request"]["cfglayoutinfo-closeable"][$name]))
			{
				$data["closeable"] = 1;
			}
			elseif (!empty($data["closeable"]))
			{
				unset($data["closeable"]);
			}

			if (!empty($arr["request"]["cfglayoutinfo-area_caption"][$name]))
			{
				$data["area_caption"] = $arr["request"]["cfglayoutinfo-area_caption"][$name];
			}
			else
			{
				unset($data["area_caption"]);
			}

			if (!empty($arr["request"]["cfglayoutinfo-width"][$name]) and "vbox" !== $arr["request"]["cfglayoutinfo-type"][$name])
			{
				$w = explode("%:", substr(trim($arr["request"]["cfglayoutinfo-width"][$name]), 0, -1));
				unset($data["width"]);

				if (100 === array_sum($w))
				{
					$data["width"] = implode("%:", $w) . "%";
				}
				else
				{
					$pcount = 0;

					// find children
					foreach ($this->cfg_layout as $pname => $pdata)
					{
						if ($pdata["parent"] === $name)
						{
							++$pcount;
						}
					}

					if ($pcount)
					{
						// divide width equally between children
						$w = (int) 100/$pcount;
						$data["width"] = implode("%:", array_fill(0, $pcount, $w)) . "%";
					}
				}
			}
			else
			{
				unset($data["width"]);
			}

			$data["type"] = ("vbox" === $arr["request"]["cfglayoutinfo-type"][$name]) ? "vbox" : "hbox";

			// change name for new user defined layout
			if (0 === strpos($name, $this->default_new_layout_name) and !empty($arr["request"]["cfglayoutinfo-name"][$name]))
			{
				unset($this->cfg_layout[$name]);
				$name = $arr["request"]["cfglayoutinfo-name"][$name];
			}

			// set order
			$data["ord"] = $arr["request"]["cfglayoutinfo-ord"][$name];

			// set data
			$this->cfg_layout[$name] = $data;
		}

		uasort($this->cfg_layout, array($this, "_sort_by_ord"));
	}

	private function change_layout_props_grp($layout, $group)
	{
		foreach ($this->cfg_proplist as $prop_name => $prop_data)
		{
			if ($layout === $prop_data["parent"])
			{
				if (is_array($prop_data["group"]))
				{
					if (in_array($this->cfg_layout[$layout]["group"], $prop_data["group"]))
					{ // layout's current parent among prop parents. replace with new.
						foreach ($prop_data["group"] as $key => $prop_grp_name)
						{
							if ($this->cfg_layout[$layout]["group"] === $prop_grp_name)
							{
								$this->cfg_proplist[$prop_name]["group"][$key] = $group;
								break;
							}
						}
					}
					else
					{ // just add parent group
						$this->cfg_proplist[$prop_name]["group"][] = $group;
					}
				}
				else
				{
					$this->cfg_proplist[$prop_name]["group"] = $group;
				}
			}
		}
	}

	private function _sort_by_ord($a, $b)
	{
		return ($a["ord"] == $b["ord"]) ? 0 : (($a["ord"] < $b["ord"]) ? -1 : 1);
	}

	////
	// !
	function callback_gen_layout($arr = array())
	{
		$this->read_template("layout.tpl");
		$used_props = $by_group = $by_layout = $layouts_by_grp = array();
		$by_group = array();

		if (isset($this->cfg_groups))
		{
			foreach($this->cfg_groups as $key => $val)
			{
				// we should not have numeric group id-s
				// actually it's more about a few ghosts I had lying
				// around, and this will get rid of them but we
				// really don't NEED numeric group id-s
				// /me does the jedi mind trick - duke
				if (!is_numeric($key))
				{
					$by_group[$key] = array();
				}
			}
		}

		if (isset($this->cfg_proplist))
		{
			foreach($this->cfg_proplist as $property)
			{
				if (!empty($property["group"]))
				{
					$layout_grp = "";

					if (isset($property["parent"]) and array_key_exists($property["parent"], $this->cfg_layout))
					{
						$by_layout[$property["parent"]][] = $property;
						$layout_grp = $this->cfg_layout[$property["parent"]]["group"];
					}

					$i = false;
					foreach((array) $property["group"] as $gkey)
					{
						if ($gkey !== $layout_grp)
						{
							if ($i)
							{
								$property["cfgf_property_editing_disabled"] = true;
							}
							else
							{
								$i = true;
							}

							$by_group[$gkey][] = $property;
						}
					}
				}
			}
		}

		if (isset($this->cfg_layout))
		{
			foreach ($this->cfg_layout as $name => $data)
			{
				if (is_array($data["group"]))
				{
					foreach ($data["group"] as $l_group)
					{
						if (array_key_exists($l_group, $this->cfg_groups))
						{
							array_unshift($by_group[$l_group], $name);
						}
					}
				}
				else
				{
					if (array_key_exists($data["group"], $this->cfg_groups) and isset($by_group[$data["group"]]))
					{
						array_unshift($by_group[$data["group"]], $name);
					}
				}
			}
		}

		$c = "";
		$cnt = 0;
		$property_defaults = array(
			"type" => ""
		);

		foreach($by_group as $key => $proplist)
		{
			$grp_id = str_replace("_", "-", $key);
			$caption = isset($this->cfg_groups[$key]) ? $this->cfg_groups[$key]["caption"]." ($key)" : "";

			$this->vars(array(
				"grp_caption" => empty($this->cfg_groups[$key]["parent"]) ? "<b>" . $caption . "</b>" : $caption,
				"grpid" => $key,
			));

			$sc = "";
			$clid = $arr["obj_inst"]->subclass();

			foreach($proplist as $tmp)
			{
				if (is_array($tmp))
				{
					$layout = false;
					$properties = array($tmp);
				}
				elseif (isset($by_layout[$tmp]))
				{
					$layout = $tmp;
					$layout_props = "";
					$properties = $by_layout[$layout];
				}
				else
				{
					$layout = $tmp;
					$layout_props = "";
					$properties = array();
				}

				foreach (safe_array($properties) as $property)
				{
					$cnt++;
					$prpdata = isset($this->all_props[$property["name"]]) ? $this->all_props[$property["name"]] : array();

					if (!$prpdata)
					{
						continue;
					}

					$property += $property_defaults;

					if (empty($property["cfgf_property_editing_disabled"]))
					{
						// additional options
						switch ($property["type"])
						{
							case "classificator":
								$this->vars(array(
									"sort_callback_caption" => t("Sorteerimise callback"),
									"sort_callback" => $property["sort_callback"],
									"prp_key" => $property["name"],
								));
								$property["cfgform_additional_options"] = $this->parse("classificator_options");
								$this->vars(array("classificator_options" => ""));
								break;

							case "image_verification":
								$this->vars(array(
									"width_caption" => t("Laius"),
									"width" => $property["width"],
									"height_caption" => t("K&otilde;rgus"),
									"height" => $property["height"],
									"text_color_caption" => t("Teksti v&auml;rv"),
									"text_color" => $property["text_color"],
									"background_color_caption" => t("Tausta v&auml;rv"),
									"background_color" => $property["background_color"],
									"font_size_caption" => t("Teksti suurus pildid"),
									"font_size" => $property["font_size"],
									"sidetop_caption" => t("Tekstikast &uuml;leval"),
									"sidetop_ch" => checked($property["side"] == "top"),
									"sidebottom_caption" => t("Tekstikast all"),
									"sidebottom_ch" => checked($property["side"] == "bottom"),
									"sideleft_caption" => t("Tekstikast vasakul"),
									"sideleft_ch" => checked($property["side"] == "left"),
									"sideright_caption" => t("Tekstikast paremal"),
									"sideright_ch" => checked($property["side"] == "right"),
									"textbox_size_caption" => t("Tekstikasti laius"),
									"textbox_size" => $property["textbox_size"],
									"prp_key" => $property["name"],
								));
								$property["cfgform_additional_options"] = $this->parse("image_verification_options");
								$this->vars(array("image_verification_options" => ""));
								break;

							case "textarea":
								$this->vars(array(
									"richtext_caption" => t("RTE"),
									"richtext_checked" => checked(isset($property["richtext"]) and $property["richtext"] == 1),
									"richtext" => !empty($property["richtext"]),
									"rows_caption" => t("K&otilde;rgus"),
									"rows" => $property["rows"],
									"cols_caption" => t("Laius"),
									"cols" => $property["cols"],
									"maxlength" => isset($property["maxlength"]) ? $property["maxlength"] : "",
									"maxlength_caption" => t("T&auml;hem&auml;rkide piirang"),
									"prp_key" => $property["name"],
								));
								$property["cfgform_additional_options"] = $this->parse("textarea_options");
								$this->vars(array("textarea_options" => ""));
								break;

							case "textbox":
								$this->vars(array(
									"size_caption" => t("Laius"),
									"size" => empty($property["size"]) ? "" : $property["size"],
									"maxlength" => empty($property["maxlength"]) ? "" : $property["maxlength"],
									"maxlength_caption" => t("T&auml;hem&auml;rkide piirang"),
									"prp_key" => $property["name"],
								));
								$property["cfgform_additional_options"] = $this->parse("textbox_options");
								$this->vars(array("textbox_options" => ""));
								break;

							case "relpicker":
								$this->vars(array(
									"no_edit_caption" => t("Muutmine keelatud"),
									"no_edit_checked" => isset($property["no_edit"]) ? checked($property["no_edit"] == 1) : "",
									"no_edit" => isset($property["no_edit"]) ? $property["no_edit"] : "",
									"no_search_caption" => t("Otsing keelatud"),
									"no_search_checked" => isset($property["no_search"]) ? checked($property["no_search"] == 1) : "",
									"no_search" => isset($property["no_search"]) ? $property["no_search"] : "",
									"displayradio_caption" => t("Valikud"),
									"displayradio_ch" => (isset($property["display"]) and "radio" === $property["display"]) ? ' checked="1"' : "",
									"displayselect_caption" => t("Selectbox"),
									"displayselect_ch" => (isset($property["display"]) and "select" === $property["display"]) ? ' checked="1"' : "",
									"stylenormal_caption" => t("Tavaline"),
									"stylenormal_ch" => (empty($property["display"])) ? ' checked="1"' : "",
									"styleac_caption" => t("Autocomplete"),
									// Are you sure, this is 'style' not 'mode'? vcl/relpicker.aw doesn't consist the word 'style'. :S -kaarel 2.12.2008
									//"styleac_ch" => ("autocomplete" === $property["style"]) ? ' checked="1"' : "",
									"styleac_ch" => (isset($property["mode"]) and "autocomplete" === $property["mode"]) ? ' checked="1"' : "",
									/*
									"oit_caption" => t("option_is_tuple"),
									"option_is_tuple_checked" => isset($property["option_is_tuple"]) && $property["option_is_tuple"] ? ' checked="1"' : "",
									"option_is_tuple" => isset($property["option_is_tuple"]) ? $property["option_is_tuple"] : "",
									*/
									"multiple_caption" => t("Saab valida mitu"),
									"multiple_checked" => isset($property["multiple"]) && $property["multiple"] ? ' checked="1"' : "",
									"multiple" => isset($property["multiple"]) ? $property["multiple"] : "",
									"size_caption" => t("K&otilde;rgus"),
									"size" => empty($property["size"]) ? "" : $property["size"],
									"prp_key" => $property["name"]
								));
								if($property["store"] == "connect" || $property["store"] == "no")
								{
									$this->vars(array(
										"rlp_ops_mult" => $this->parse("rlp_ops_mult"),
									));
								}
								// I don't think we need this, so I'll not parse it. But I'll leave it here in case someone someday thinks it's necessary... -kaarel 3.12.2008
								/*
								if($property["mode"] == "autocomplete")
								{
									$this->vars(array(
										"rlp_ops_oit" => $this->parse("rlp_ops_oit"),
									));
								}
								*/
								$property["cfgform_additional_options"] = $this->parse("relpicker_options");
								$this->vars(array("relpicker_options" => ""));
								break;

							case "releditor":
								$this->vars(array(
									"cfgform_id_caption" => t("Seadetevormi id"),
									"cfgform_id" => $property["cfgform_id"],
									"obj_parent_caption" => t("Parent"),
									"obj_parent" => $property["obj_parent"],
									"use_form" => $property["use_form"],
									"rel_id" => $property["rel_id"],
									"mode" => $property["mode"],
									"prp_key" => $property["name"]
								));
								$property["cfgform_additional_options"] = $this->parse("releditor_options");
								$this->vars(array("releditor_options" => ""));
								break;

							case "select":
								$this->vars(array(
									"size_caption" => t("K&otilde;rgus"),
									"size" => empty($property["size"]) ? "" : $property["size"],
									"prp_key" => $property["name"]
								));
								$property["cfgform_additional_options"] = $this->parse("select_options");
								$this->vars(array("select_options" => ""));
								break;

							case "date_select":
								settype($property["format"], "array");
								$this->vars(array(
									"prp_key" => $property["name"],

									"buttons_show_caption" => t("Nupud: jah"),
									"buttons_show_ch" => (1 == $property["buttons"]) ? ' checked="1"' : "",
									"buttons_hide_caption" => t("ei"),
									"buttons_hide_ch" => ("0" === $property["buttons"]) ? ' checked="1"' : "",
									"buttons_default_caption" => t("m&auml;&auml;ramata"),
									"buttons_default_ch" => (!isset($property["buttons"])) ? ' checked="1"' : "",

									"format_caption" => t("Formaat:"),

									"format_dayselect_caption" => t("P&auml;ev: select"),
									"format_dayselect_ch" => (in_array("day", $property["format"])) ? ' checked="1"' : "",
									"format_daytext_caption" => t("text"),
									"format_daytext_ch" => (in_array("day_textbox", $property["format"])) ? ' checked="1"' : "",
									"format_daynone_caption" => t("m&auml;&auml;ramata"),
									"format_daynone_ch" => (!in_array("day", $property["format"]) and !in_array("day_textbox", $property["format"])) ? ' checked="1"' : "",

									"format_monthselect_caption" => t("Kuu: select"),
									"format_monthselect_ch" => (in_array("month", $property["format"])) ? ' checked="1"' : "",
									"format_monthtext_caption" => t("text"),
									"format_monthtext_ch" => (in_array("month_textbox", $property["format"])) ? ' checked="1"' : "",
									"format_monthnone_caption" => t("m&auml;&auml;ramata"),
									"format_monthnone_ch" => (!in_array("month", $property["format"]) and !in_array("month_textbox", $property["format"])) ? ' checked="1"' : "",

									"format_yearselect_caption" => t("Aasta: select"),
									"format_yearselect_ch" => (in_array("year", $property["format"])) ? ' checked="1"' : "",
									"format_yeartext_caption" => t("text"),
									"format_yeartext_ch" => (in_array("year_textbox", $property["format"])) ? ' checked="1"' : "",
									"format_yearnone_caption" => t("m&auml;&auml;ramata"),
									"format_yearnone_ch" => (!in_array("year", $property["format"]) and !in_array("year_textbox", $property["format"])) ? ' checked="1"' : "",

									"format_hourselect_caption" => t("Tund: select"),
									"format_hourselect_ch" => (in_array("hour", $property["format"])) ? ' checked="1"' : "",
									"format_hourtext_caption" => t("text"),
									"format_hourtext_ch" => (in_array("hour_textbox", $property["format"])) ? ' checked="1"' : "",
									"format_hournone_caption" => t("m&auml;&auml;ramata"),
									"format_hournone_ch" => (!in_array("hour", $property["format"]) and !in_array("hour_textbox", $property["format"])) ? ' checked="1"' : "",

									"format_minuteselect_caption" => t("Minut: select"),
									"format_minuteselect_ch" => (in_array("minute", $property["format"])) ? ' checked="1"' : "",
									"format_minutetext_caption" => t("text"),
									"format_minutetext_ch" => (in_array("minute_textbox", $property["format"])) ? ' checked="1"' : "",
									"format_minutenone_caption" => t("m&auml;&auml;ramata"),
									"format_minutenone_ch" => (!in_array("minute", $property["format"]) and !in_array("minute_textbox", $property["format"])) ? ' checked="1"' : ""
								));
								$property["cfgform_additional_options"] = $this->parse("date_select_options");
								$this->vars(array("date_select_options" => ""));
								break;

							case "datetime_select":
								$this->vars(array(
									"prp_key" => $property["name"],
									"dayselect_caption" => t("P&auml;ev: select"),
									"dayselect_ch" => (empty($property["day"]) or "select" === $property["day"]) ? ' checked="1"' : "",
									"daytext_caption" => t("textbox"),
									"daytext_ch" => (isset($property["day"]) and "text" === $property["day"]) ? ' checked="1"' : "",
									"monthselect_caption" => t("Kuu: select"),
									"monthselect_ch" => (empty($property["month"]) or "select" === $property["month"]) ? ' checked="1"' : "",
									"monthtext_caption" => t("textbox"),
									"monthtext_ch" => (isset($property["month"]) and "text" === $property["month"]) ? ' checked="1"' : "",
								));
								$property["cfgform_additional_options"] = $this->parse("datetime_select_options");
								$this->vars(array("datetime_select_options" => ""));
								break;

							case "chooser":
								$this->vars(array(
									"orienth_caption" => t("Horisontaalselt"),
									"orienth_ch" => isset($property["orient"]) && ("horizontal" === $property["orient"]) ? ' checked="1"' : "",
									"orientv_caption" => t("Vertikaalselt"),
									"orientv_ch" => isset($property["orient"]) && ("vertical" === $property["orient"]) ? ' checked="1"' : "",
									"prp_key" => $property["name"],
								));
								$property["cfgform_additional_options"] = $this->parse("chooser_options");
								$this->vars(array("chooser_options" => ""));
								break;

							case "layout":
								$this->vars(array(
									"hbox_caption" => t("Horisontaalne"),
									"hbox_ch" => ("hbox" === $property["type"]) ? ' checked="1"' : "",
									"hbox_caption" => t("Vertikaalne"),
									"hbox_ch" => ("vertical" === $property["type"]) ? ' checked="1"' : "",
									"prp_key" => $property["name"],
								));
								$property["cfgform_additional_options"] = $this->parse("layout_options");
								$this->vars(array("layout_options" => ""));
								break;
							case "table":
								$this->vars(array(
									"prp_key" => $property["name"],
									"configurable_caption" => t("Konfigureeritav"),
									"configurable_checked" => !empty($property["configurable"]) ? " checked=\"1\"" : "",
								));
								$property["cfgform_additional_options"] = $this->parse("table_options");
								$this->vars(array("table_options" => ""));
								break;

							case "keyword_selector":
								$this->vars(array(
									"prp_key" => $property["name"],
									"no_folder_names_caption" => t("&Auml;ra n&auml;ita kaustade nimesid"),
									"no_folder_names_checked" => isset($property["no_folder_names"]) && $property["no_folder_names"] ? " checked=\"1\"" : "",
									"no_header_caption" => t("&Auml;ra n&auml;ita tabeli p&auml;ist"),
									"no_header_checked" => isset($property["no_header"]) && $property["no_header"] ? " checked=\"1\"" : "",
									"hide_selected_caption" => t("&Auml;ra n&auml;ita valitud m&auml;rks&otilde;nu"),
									"hide_selected_checked" => isset($property["hide_selected"]) && $property["hide_selected"] ? " checked=\"1\"" : "",
									"keyword_per_row_caption" => t("Mitu v&otilde;tmes&otilde;na real"),
									"keyword_per_row" => isset($property["keyword_per_row"]) ? (int) $property["keyword_per_row"] : 0,
								));
								$property["cfgform_additional_options"] = $this->parse("keyword_selector_options");
								break;

							case "multifile_upload":
								$this->vars(array(
									"prp_key" => $property["name"],
									"max_files_caption" => t("Maksimaalne failide arv"),
									"max_files" => isset($property["max_files"]) ? $property["max_files"] : "",
								));
								$property["cfgform_additional_options"] = $this->parse("multifile_upload_options");
								break;

							default:
								$property["cfgform_additional_options"] = "";
						}

						if ($arr["obj_inst"]->prop("use_in_releditor"))
						{
							$this->vars(array(
								"prp_key" => $property["name"],
								"emb_tbl_controller_caption" => t("Tabeli kontrolleri id"),
								"emb_tbl_controller" => $property["emb_tbl_controller"],
								"show_in_emb_tbl_caption" => t("N&auml;ita releditori tabelis"),
								"show_in_emb_tbl_checked" => checked(!empty($property["show_in_emb_tbl"])),
								"show_in_emb_tbl" => isset($property["show_in_emb_tbl"]) ? $property["show_in_emb_tbl"] : null,
								"emb_tbl_caption" => isset($property["emb_tbl_caption"]) ? $property["emb_tbl_caption"] : null,
								"emb_tbl_col_num" => isset($property["emb_tbl_col_num"]) ? $property["emb_tbl_col_num"] : null,
								"emb_tbl_col_sep" => isset($property["emb_tbl_col_sep"]) ? $property["emb_tbl_col_sep"] : null,
								"emb_tbl_caption_caption" => t("Tulba pealkiri"),
								"emb_tbl_col_num_caption" => t("Tulba grupp"),
								"emb_tbl_col_sep_caption" => t("Eraldaja"),
							));
							$emb_tbl = $this->parse("emb_tbl");
						}
						else
						{
							$emb_tbl = "";
						}

						$this->vars(array(
							"prp_key" => $property["name"],
							"no_caption_caption" => t("&Auml;ra n&auml;ita pealkirja"),
							"no_caption_checked" => checked(!empty($property["no_caption"])),
							"no_caption" => empty($property["no_caption"]) ? "" : $property["no_caption"],
							"captionside_l_caption" => t("Pealkiri vasakul"),
							"captionside_l_ch" =>  (isset($property["captionside"]) and "left" === $property["captionside"]) ? ' checked="1"' : "",
							"captionside_t_caption" => t("Pealkiri &uuml;lal"),
							"captionside_t_ch" =>  (isset($property["captionside"]) and "top" === $property["captionside"]) ? ' checked="1"' : "",
							"textsize_caption" => '<span title="' . t("CSS formaadis:") . ' 12px, 0.7em, ..." style="cursor: help;">' . t("Tekstisuurus") . '</span>',
							"textsize" => empty($property["textsize"]) ? "" : $property["textsize"],
							"disabled_caption" => t("Mitteaktiivne (disabled)"),
							"disabled_checked" => checked(!empty($property["disabled"])),
							"comment_caption" => t("Kommentaar"),
							"comment" => empty($property["comment"]) ? "" : $property["comment"],
							"comment_style_text_caption" => t("Kommentaar tekstina"),
							"comment_style_popup_caption" => t("Kommentaar popupina"),
							"comment_style_text_ch" => (isset($property["comment_style"]) and "text" === $property["comment_style"]) ? ' checked="1"' : "",
							"comment_style_popup_ch" => (!isset($property["comment_style"]) or "popup" === $property["comment_style"]) ? ' checked="1"' : "",
							"disabled" => empty($property["disabled"]) ? "" : $property["disabled"],
							"prp_options" => $property["cfgform_additional_options"],
							"prp_opts_caption" => t("Lisavalikud"),
							"tmp_id" => $cnt,
							"emb_tbl" => $emb_tbl
						));
						$options = $this->parse("options");
						$this->vars(array("options" => ""));

						// type selector
						$type_options = array();

						switch ($property["type"])
						{ /// get type options
							case "textbox":
								$type_options = array(
									"textbox" => "textbox",
									"textarea" => "textarea",
									"text" => "text"
								);
								break;

							case "text":
								$type_options = array(
									"text" => "text",
									"textbox" => "textbox",
									"textarea" => "textarea"
								);
								break;

							case "textarea":
								$type_options = array(
									"textarea" => "textarea",
									"textbox" => "textbox",
									"text" => "text"
								);
								break;

							case "date_select":
								$type_options = array(
									"date_select" => "date_select",
									"datetime_select" => "datetime_select"
								);
								break;

							case "datetime_select":
								$type_options = array(
									"datetime_select" => "datetime_select",
									"date_select" => "date_select"
								);
								break;

							case "relpicker":
								$type_options = array(
									"relpicker" => "relpicker",
									"releditor" => "releditor"
								);
								break;

							case "releditor":
								$type_options = array(
									"releditor" => "releditor",
									"relpicker" => "relpicker"
								);
								break;
						}

						/// some elements' type not alterable. those also form elements and in POST request
						$disabled = !count($type_options);
						if ($disabled)
						{
							$type_options = array($property["type"] => $property["type"]);
						}

						///
						$type_selector = html::select(array(
							"name" => "prpconfig[" . $prpdata["name"] . "][type]",
							"options" => $type_options,
							"disabled" => $disabled,
							"value" => $property["type"]
						));

						//
						$used_props[$property["name"]] = 1;
						$prop_tpl = "property";
					}
					else
					{
						$options = "";
						$type_selector = $property["type"];
						$prop_tpl = "property_disabled";
					}

					$this->vars(array(
						"bgcolor" => $cnt % 2 ? "#EEEEEE" : "#FFFFFF",
						"prp_caption" => empty($property["caption"]) ? "" : $property["caption"],
						"prp_type" => $type_selector,
						"prp_mark_key" => $prpdata["name"] . "|" . $key,
						"prp_key" => $prpdata["name"],
						"prp_order" => empty($property["ord"]) ? "" : $property["ord"],
						"options" => $options,
						"grp_id" => $grp_id
					));

					if ($layout)
					{
						$layout_props .= $this->parse($prop_tpl);
					}
					else
					{
						$sc .= $this->parse($prop_tpl);
					}
				}

				if ($layout)
				{
					$this->vars(array(
						"layout_name" => $layout,
						"layout_props" => $layout_props,
						"layout_type" => $this->cfg_layout[$layout]["type"],
					));
					$sc .= $this->parse("layout");
				}
			}

			$select_toggle = "";

			if ($sc)
			{
				$this->vars(array(
					"grp_id" => $grp_id,
					"capt_prp_mark" => t("Inverteeri valik")
				));
				$select_toggle = $this->parse("select_toggle");
			}

			$this->vars(array(
				"property" => $sc,
				"layout" => "",
				"select_toggle" => $select_toggle,
				"grp_id" => $grp_id,
				"capt_prp_mark" => t("Inverteeri valik")
			));
			$c .= $this->parse("group");
		}

		$this->vars(array(
			"group" => $c,
			"capt_legend_tbl" => t("Tabi pealkiri (tabi_nimi)"),
			"capt_prp_order" => t("Jrk."),
			"capt_prp_key" => t("Nimi"),
			"capt_prp_caption" => t("Pealkiri"),
			"capt_prp_type" => t("T&uuml;&uuml;p")
		));

		$item = $arr["prop"];
		$item["value"] = $this->parse();
		return array($item);
	}

	function __sort_props_by_ord($el1,$el2)
	{
		if (empty($el1["ord"]) && empty($el2["ord"]))
		{
			return (int)($el1["tmp_ord"] - $el2["tmp_ord"]);
		}
		elseif (empty($el1["ord"]))
		{
			return -1;
		}
		elseif (empty($el2["ord"]))
		{
			return 1;
		}

		return (int)($el1["ord"] - $el2["ord"]);
	}

	////
	// !
	function gen_avail_props($arr = array())
	{
		// init table
		$t = $arr["prop"]["vcl_inst"];

		$t->define_field(array(
			"name" => "name",
			"sortable" => true,
			"caption" => t("Nimi"),
		));

		$t->define_field(array(
			"name" => "type",
			"sortable" => true,
			"caption" => t("T&uuml;&uuml;p"),
		));

		$t->define_field(array(
			"name" => "caption",
			"sortable" => true,
			"caption" => t("Pealkiri"),
		));

		$t->define_field(array(
			"name" => "default_grp",
			"sortable" => true,
			"caption" => t("Vaikimisi tab"),
			"filter" => "automatic"
		));

		$t->define_field(array(
			"name" => "in_use",
			"sortable" => true,
			"caption" => t("K"),
			"tooltip" => t("Kasutusel")
		));

		$t->define_chooser(array(
			"name" => "mark",
			"field" => "name"
		));

		if (empty($arr["request"]["sortby"]))
		{
			$t->set_sortable(false);
		}

		// get props in use
		$used_props = array();

		if (is_array($this->prplist))
		{
			foreach($this->prplist as $property)
			{
				if (!empty($property["group"]))
				{
					if (is_array($property["group"]))
					{
						$used_props[$property["name"]] = $property["group"];
					}
					else
					{
						$used_props[$property["name"]][] = $property["group"];
					}
				}
			}
		}

		foreach($this->all_props as $property)
		{
			$default_group = $groups = "";

			if (isset($used_props[$property["name"]]) and count($used_props[$property["name"]]))
			{
				$groups = implode(", ", $used_props[$property["name"]]);
			}

			if (isset($property["group"]))
			{
				$default_group = is_array($property["group"]) ? implode(", ", $property["group"]) : $property["group"];
			}

			$t->define_data(array(
				"caption" => empty($property["caption"]) ? "" : $property["caption"],
				"type" => $property["type"],
				"name" => $property["name"],
				"default_grp" => $default_group,
				"in_use" => $groups ? html::img(array(
					"url" => aw_ini_get("icons.server")."check.gif",
					"alt" => $groups,
					"title" => $groups
				)) : ""
			));
		}
	}

	function gen_navtoolbar($arr)
	{
		// which links do I need on the toolbar?
		// 1- lisa tab
		$toolbar = $arr["prop"]["toolbar"];

		$toolbar->add_button(array(
			"name" => "save",
			"tooltip" => t("Salvesta"),
			"url" => "javascript:submit_changeform()",
			"img" => "save.gif",
		));

		$toolbar->add_button(array(
			"name" => "delete",
			"tooltip" => t("Eemalda valitud omadused tab-ist"),
			"url" => "javascript:document.changeform.subaction.value='delete';submit_changeform();",
			"img" => "delete.gif",
		));

		$toolbar->add_separator();

		$toolbar->add_cdata(t("<small>Liiguta omadused:</small>"));
		$opts = array();

		if (is_array($this->grplist))
		{
			$layouts_by_grp = array();

			if (is_array($this->layout))
			{
				foreach ($this->layout as $name => $data)
				{
					if (is_array($data["group"]))
					{
						foreach ($data["group"] as $l_group)
						{
							$layouts_by_grp[$l_group]["layout:" . $name] = t("&nbsp;&nbsp;&nbsp;Layouti: ") . $name;
						}
					}
					else
					{
						$layouts_by_grp[$data["group"]]["layout:" . $name] = t("&nbsp;&nbsp;&nbsp;Layouti: ") . $name;
					}
				}
			}

			foreach($this->grplist as $name => $grpdata)
			{
				$opts["group:" . $name] = t("Tabi: ") . $grpdata["caption"] . " [" . $name . "]";

				if (isset($layouts_by_grp[$name]))
				{
					$opts = array_merge($opts, $layouts_by_grp[$name]);
				}
			}
		}
		else
		{
			$opts[""] = t("&Uuml;htegi tabi pole veel!");
		}

		$toolbar->add_cdata(html::select(array(
			"options" => $opts,
			"textsize" => "12px",
			"name" => "target_grp",
		)));

		$toolbar->add_button(array(
			"name" => "move",
			"tooltip" => t("Liiguta"),
			"url" => "javascript:document.changeform.subaction.value='move';submit_changeform();",
			"img" => "save.gif",
		));

		$toolbar->add_separator();

		$toolbar->add_cdata(t("<small>Lisa tab:</small>"));
		$toolbar->add_cdata(html::textbox(array(
			"name" => "newgrpname",
			"textsize" => "12px",
			"size" => "20",
		)));

		$toolbar->add_cdata(t("<small>Millise tabi alla:</small>"));
		$tabs = array();
		$tabs[""] = t("");
		if (is_array($this->grplist))
		{
			foreach($this->grplist as $key => $grpdata)
			{
				if (empty($grpdata["parent"]))
				{
					$tabs[$key] = $grpdata["caption"] . " [" . $key . "]";
				}
			}
		}

		$toolbar->add_cdata(html::select(array(
			"options" => $tabs,
			"textsize" => "12px",
			"name" => "target",
		)));

		$toolbar->add_button(array(
			"name" => "addgrp",
			"tooltip" => t("Lisa tab"),
			"url" => "javascript:document.changeform.subaction.value='addgrp';submit_changeform()",
			"img" => "new.gif",
		));
	}

	function gen_availtoolbar($arr)
	{
		$this_o = $arr["obj_inst"];
		$toolbar = $arr["prop"]["vcl_inst"];
		$this_oid = $this_o->id();
		$return_url = get_ru();

		// merge
		$toolbar->add_menu_button(array(
			"name" => "merge",
			"img" => "import.gif",
			"tooltip" => t("Loe klassi uuendused seadetevormi"),
		));

			// merge all
			$toolbar->add_menu_item(array(
				"parent" => "merge",
				"text" => t("K&otilde;ik"),
				"link" => $this->mk_my_orb("merge", array(
					"id" => $this_oid,
					"scope" => "all",
					"return_url" => $return_url,
				))
			));

			// merge properties
			$toolbar->add_menu_item(array(
				"parent" => "merge",
				"text" => t("Ainult omadused"),
				"link" => $this->mk_my_orb("merge", array(
					"id" => $this_oid,
					"scope" => "properties",
					"return_url" => $return_url,
				))
			));

			// merge groups
			$toolbar->add_menu_item(array(
				"parent" => "merge",
				"text" => t("Ainult tabid"),
				"link" => $this->mk_my_orb("merge", array(
					"id" => $this_oid,
					"scope" => "groups",
					"return_url" => $return_url,
				))
			));

			// merge layouts
			$toolbar->add_menu_item(array(
				"parent" => "merge",
				"text" => t("Ainult layoudid"),
				"link" => $this->mk_my_orb("merge", array(
					"id" => $this_oid,
					"scope" => "layouts",
					"return_url" => $return_url,
				))
			));

		// reload
		$toolbar->add_menu_button(array(
			"name" => "reload",
			"img" => "refresh.gif",
			"tooltip" => t("Tee seadetele alglaadimine"),
		));

			// reload all
			$toolbar->add_menu_item(array(
				"parent" => "reload",
				"text" => t("K&otilde;ik"),
				"link" => $this->mk_my_orb("reload", array(
					"id" => $this_oid,
					"scope" => "all",
					"return_url" => $return_url,
				))
			));

			// reload properties
			$toolbar->add_menu_item(array(
				"parent" => "reload",
				"text" => t("Ainult omadused"),
				"link" => $this->mk_my_orb("reload", array(
					"id" => $this_oid,
					"scope" => "properties",
					"return_url" => $return_url,
				))
			));

			// reload groups
			$toolbar->add_menu_item(array(
				"parent" => "reload",
				"text" => t("Ainult tabid"),
				"link" => $this->mk_my_orb("reload", array(
					"id" => $this_oid,
					"scope" => "groups",
					"return_url" => $return_url,
				))
			));

			// reload layouts
			$toolbar->add_menu_item(array(
				"parent" => "reload",
				"text" => t("Ainult layoudid"),
				"link" => $this->mk_my_orb("reload", array(
					"id" => $this_oid,
					"scope" => "layouts",
					"return_url" => $return_url,
				))
			));

		// move props
		$opts = array();
		if (is_array($this->grplist))
		{
			foreach($this->grplist as $key => $grpdata)
			{
				$opts[$key] = $grpdata["caption"] . " [" . $key . "]";
			}
		}
		else
		{
			$opts[""] = t("&Uuml;htegi tabi pole veel!");
		}

		$toolbar->add_cdata(html::select(array(
			"options" => $opts,
			"name" => "target",
		)));

		$toolbar->add_button(array(
			"name" => "save",
			"tooltip" => t("Salvesta"),
			"url" => "javascript:submit_changeform()",
			"img" => "save.gif",
		));
	}

	private function add_new_properties($arr)
	{
		$target = $arr["request"]["target"];
		$this->_init_cfgform_data($arr["obj_inst"]);

		// first check, whether a group with that id exists
		if (isset($this->cfg_groups[$target]) and is_array($arr["request"]["mark"]))
		{
			foreach($arr["request"]["mark"] as $pkey => $pval)
			{
				// if this is a valid property, then add it to the list
				if (!isset($this->cfg_proplist[$pkey]) and isset($this->all_props[$pkey]))
				{
					$this->cfg_proplist[$pkey] = $this->all_props[$pkey];
				}

				// need to add another group
				// add group only if prop not already in that group
				if (is_array($this->cfg_proplist[$pkey]["group"]) and !in_array($target, $this->cfg_proplist[$pkey]["group"]))
				{
					$this->cfg_proplist[$pkey]["group"][] = $target;
				}
				elseif ($target !== $this->cfg_proplist[$pkey]["group"])
				{
					$this->cfg_proplist[$pkey]["group"] = array($this->cfg_proplist[$pkey]["group"], $target);
				}
			}
		}
	}

	function save_layout($arr)
	{
		$subaction = $arr["request"]["subaction"];
		$this->_init_cfgform_data($arr["obj_inst"]);

		switch($subaction)
		{
			case "addgrp":
				$ret = $this->add_group($arr["obj_inst"], false, $arr["request"]["target"], $arr["request"]["newgrpname"]);

				if (true !== $ret)
				{
					$arr["prop"]["error"] = $ret;
					return PROP_ERROR;
				}
				break;

			case "delete":
				$mark = $arr["request"]["mark"];

				if (is_array($mark))
				{
					foreach($mark as $pkey => $val)
					{
						list($prop, $grp) = explode("|", $pkey, 2);

						if (!is_array($this->cfg_proplist[$prop]["group"]) or 1 === count($this->cfg_proplist[$prop]["group"]))
						{
							unset($this->cfg_proplist[$prop]);
						}
						elseif (in_array($grp, $this->cfg_proplist[$prop]["group"], true))
						{
							$idx = reset(array_keys($this->cfg_proplist[$prop]["group"], $grp));
							unset($this->cfg_proplist[$prop]["group"][$idx]);
						}
					}
				}
				break;

			case "move":
				$mark = $arr["request"]["mark"];

				if (is_array($mark))
				{
					$target = $arr["request"]["target_grp"];

					if (0 === strpos($target, "group:"))
					{
						$target_grp = substr($target, 6);
						$target_layout = false;
					}
					elseif (0 === strpos($target, "layout:"))
					{
						$target_layout = substr($target, 7);
						$target_grp = $this->layout[$target_layout]["group"];

						if(empty($target_grp))
						{
							$arr["prop"]["error"] = t("Layoudil puudub tab");
							return PROP_ERROR;
						}
					}
					else
					{
						$arr["prop"]["error"] = t("Viga p&auml;ringu formaadis");
						return PROP_ERROR;
					}

					foreach($mark as $pkey => $val)
					{
						list($prop, $grp) = explode("|", $pkey, 2);

						// set parent group
						if (is_array($this->cfg_proplist[$prop]["group"]) and !in_array($grp, $this->cfg_proplist[$prop]["group"]))
						{
							$this->cfg_proplist[$prop]["group"][] = $target_grp;
						}
						else
						{
							$this->cfg_proplist[$prop]["group"] = $target_grp;
						}

						// set parent layout
						if ($target_layout)
						{
							$this->cfg_proplist[$prop]["parent"] = $target_layout;
						}
						else
						{
							unset($this->cfg_proplist[$prop]["parent"]);
						}
					}
				}
				break;

			default:
				foreach ($this->cfg_proplist as $name => $data)
				{
					if (isset($arr["request"]["prpconfig"][$name]))
					{
						$cfg_data = $arr["request"]["prpconfig"][$name];

						if (isset($arr["request"]["xconfig"][$name]))
						{ // remove option configuration if checkbox not checked. required by some older html and vcl(?) classes' methods' boolean argument implementations.
							foreach ($arr["request"]["xconfig"][$name] as $ch_name => $value)
							{
								if (!empty($value) and !isset($cfg_data[$ch_name]))
								{
									unset($data[$ch_name]);
								}
							}
						}

						foreach ($cfg_data as $option_name => $option_value)
						{
							if (is_array($option_value))
							{ // remove option configuration if no array elements selected. html::date_select format argument requires this.
								$option_value_defined = false;

								foreach ($option_value as $option_value_el)
								{
									if (!empty($option_value_el))
									{
										$option_value_defined = true;
									}
								}

								if (!$option_value_defined)
								{
									unset($cfg_data[$option_name]);
								}
							}
							elseif ("buttons" === $option_name and (0 === strlen($option_value)))
							{
								unset($data[$option_name]);
								unset($cfg_data[$option_name]);
							}
						}

						$this->cfg_proplist[$name] = $cfg_data + $data;
					}
				}
				break;
		}
	}

	function update_groups($arr)
	{
		$grplist = $this->grplist;

		if (is_array($arr["request"]["grpcaption"]))
		{
			foreach($arr["request"]["grpcaption"] as $key => $val)
			{
				if (isset($grplist[$key]))
				{
					$grplist[$key]["caption"] = $val;
					$grplist[$key]["grpview"] = $arr["request"]["grpview"][$key];
					$grplist[$key]["grphide"] = (int) !$arr["request"]["grphide"][$key];
					$grplist[$key]["grpctl"] = (int) $arr["request"]["grpctl"][$key];
					$grplist[$key]["grp_d_ctl"] = (int) $arr["request"]["grp_d_ctl"][$key];
					$grplist[$key]["ord"] = (int) $arr["request"]["grpord"][$key];

					// submit
					if (empty($arr["request"]["grpsubmit"][$key]))
					{
						$grplist[$key]["submit"] = "no";
					}
					elseif ($grplist[$key]["submit"] === "no")
					{
						unset($grplist[$key]["submit"]);
					}

					// submit button text
					if (!empty($arr["request"]["grpsubmit_btn_text"][$key]))
					{
						$grplist[$key]["submit_btn_text"] = $arr["request"]["grpsubmit_btn_text"][$key];
						$found_submit_prop = false;

						// find existing submit prop in this grp
						foreach ($this->cfg_proplist as $name => $prop)
						{
							if ("submit" === $prop["type"] and (is_array($prop["group"]) and in_array($key, $prop["group"]) or $key === $prop["group"]))
							{
								$found_submit_prop = true;
								$this->cfg_proplist[$name]["caption"] = $arr["request"]["grpsubmit_btn_text"][$key];
								break;
							}
						}

						if (!$found_submit_prop)
						{ // add new
							$prop_name = $key . "_submit_button";
							$i = 0;

							while (isset($this->cfg_proplist[$prop_name]))
							{
								++$i;
								$prop_name = $key . "_submit_button_" . $i;
							}

							$this->cfg_proplist[$prop_name] = array(
								"name" => $prop_name,
								"type" => "submit",
								"store" => "no",
								"group" => $key,
								"force_display" => 1,
								"caption" => $arr["request"]["grpsubmit_btn_text"][$key],
							);
						}
					}
					else
					{ // remove submit button
						foreach ($this->cfg_proplist as $name => $prop)
						{
							if ("submit" === $prop["type"])
							{
								if ($prop["group"] === $key)
								{
									unset($this->cfg_proplist[$name]);
								}
								elseif (is_array($prop["group"]) and in_array($key, $prop["group"]))
								{
									foreach ($prop["group"] as $ord => $group)
									{
										if ($group === $key)
										{
											unset($this->cfg_proplist[$name]["group"][$ord]);
										}
									}

									if (!count($this->cfg_proplist[$name]["group"]))
									{
										unset($this->cfg_proplist[$name]);
									}
								}
							}
						}

						unset($grplist[$key]["submit_btn_text"]);
					}

					// style
					$styl = $arr["request"]["grpstyle"][$key];

					if (!empty($styl))
					{
						$grplist[$key]["grpstyle"] = $styl;
					}

					// form element to focus on load
					if (array_key_exists($arr["request"]["grpfocus"][$key], $this->prplist))
					{
						$grplist[$key]["focus"] = $arr["request"]["grpfocus"][$key];
					}
					else
					{
						unset($grplist[$key]["focus"]);
					}
				}
			}
		}

		$this->cfg_groups = $grplist;
	}

	/** returns array of properties defined in the config form given

		@attrib api=1 params=name

		@param id required type=oid
			the id of the config form object to read the properties from


		@errors
			error is thrown if the given config form object does not exist or the user has no view access to it

		@returns array of properties that are included in the config form,
			array contains all the property information for each property


		@examples

			$cf = get_instance(CL_CFGFORM);
			$props = $cf->get_props_for_cfgform(array(
				"id" => $_GET["cfgform"]
			));

			foreach($props as $pn => $pd)
			{
				echo "property name = $pd , caption = ".$pd["caption"]." <br>";
			}

	**/
	function get_props_from_cfgform($arr)
	{
		if(empty($arr["id"]))
		{
			return array();
		}

		$cf = obj($arr["id"]);
		$cfgx = new cfgutils();
		$ret = $cfgx->load_properties(array(
			"clid" => $cf->prop("subclass"),
		));

		$subclass = $cf->prop("subclass");
		// XXX: can be removed once doc and document are merged
		$inst_name = ($subclass == doc_obj::CLID) ? "doc" : $subclass;
		$class_i = get_instance($inst_name);
		$tmp = $class_i->load_from_storage(array(
			"id" => $cf->id()
		));
		$dat = array();
		foreach($tmp as $pn => $pd)
		{
			if ($pn === "needs_translation" || $pn === "is_translated" || $pn == "")
			{
				continue;
			}

			if($ret[$pn])
			{
				$dat[$pn] = $ret[$pn];
				$dat[$pn]["caption"] = empty($pd["caption"]) ? "" : $pd["caption"];
				if(!empty($pd["richtext"]))
				{
					$dat[$pn]["richtext"] = $pd["richtext"];
				}
			}
		}

		return $dat;
	}

	/** draws a config form from the given object type object

		@attrib api=1 params=name

		@param ot required type=oid
			object type object's id

		@param reforb required type=text
			the orb action (made by mk_reforb) to submit the form to

		@param errors optional type=array
			array returned from validate_data, containing errors from submit controllers/set_property

		@param values optional type=array
			array of property name => property value pairs that will be used when drawing the form

		@returns
			The html containing the form, including the <form tag. form is submitted to the reforb argument given

		@examples
			$cff = get_instance(CL_CFGFORM);
			$html = $cff->draw_cfgform_from_ot(array(
				"ot" => $object_type_id,
				"reforb" => $this->mk_reforb("handle_form_submit")
			));
			echo $html; // displays the form
	**/
	function draw_cfgform_from_ot($arr)
	{
		// get all props
		$els = $this->get_props_from_ot($arr);

		$errs = new aw_array($arr["errors"]);
		$errs = $errs->get();

		$ret = array();
		foreach($els as $pn => $pd)
		{
			if (isset($errs[$pn]))
			{
				$ret[$pn."_err"] = array(
					"name" => $pn."_err",
					"type" => "text",
					"store" => "no",
					"value" => "<font color=red>".$errs[$pn]["msg"]."</font>",
					"no_caption" => 1
				);
			}
			$ret[$pn] = $pd;
		}
		$els = $ret;
		$els["__submit"] = array(
			"name" => "__submit",
			"type" => "submit",
			"value" => t("Salvesta")
		);
		$rd = new register_data();
		$els = $rd->parse_properties(array(
			"properties" => $els,
			"name_prefix" => ""
		));

		$htmlc = new htmlclient();
		$htmlc->start_output();
		foreach($els as $pn => $pd)
		{
			$htmlc->add_property($pd);
		}
		$htmlc->finish_output();

		$html = $htmlc->get_result(array(
			"raw_output" => 1
		));

		$this->read_template("show_form.tpl");
		$this->vars(Array(
			"form" => $html,
			"reforb" => $arr["reforb"]
		));
		return $this->parse();
	}

	/** returns array of properties given object type object

		@attrib api=1

		@param ot required type=oid
			object type object's id

		@param values optional type=array
			array of property name => property value pairs

		@param for_show optional type=bool
			if true, classificator values are resolved

		@param site_lang optional type=bool
			if true, translations are read from site language, not admin

		@returns
			array of properties from the config form selected in the object type.
			array key is property name, value is property data

		@examples
			$cff = get_instance(CL_CFGFORM);
			$props = $cff->get_props_from_ot(array("ot" => $object_type_id));
			foreach($props as $property_name => $property_data)
			{
				echo "prop = $property_name , caption = $property_data[caption] \n";
			}
	**/
	function get_props_from_ot($arr)
	{
		$ot = obj($arr["ot"]);
		$class_id = $ot->prop("type");

		$cfgx = new cfgutils();
		$els = $cfgx->load_properties(array(
			"clid" => $class_id,
		));


		if ($ot->prop("use_cfgform"))
		{
			$cff = obj($ot->prop("use_cfgform"));

			$v_ctr = safe_array($cff->meta("view_controllers"));
			$ctr = safe_array($cff->meta("controllers"));

			$tmp = array();
			foreach(safe_array($cff->meta("cfg_proplist")) as $pn => $pd)
			{
				$tmp[$pn] = $els[$pn];
				foreach($pd as $k => $v)
				{
					$tmp[$pn][$k] = $v;
				}
				$tmp[$pn]["controllers"] = isset($ctr[$pn]) ? $ctr[$pn] : null;
				$tmp[$pn]["view_controllers"] = isset($v_ctr[$pn]) ? $v_ctr[$pn] : null;
			}
			$els = $tmp;

			uasort($els, create_function('$a, $b','if ($a["ord"] == $b["ord"]) { return 0;} else {return $a["ord"] > $b["ord"] ? 1 : -1;}'));

			$trans = $cff->meta("translations");

			if ($arr["site_lang"])
			{
				if (aw_ini_get("user_interface.full_content_trans"))
				{
					$lc = aw_global_get("ct_lang_lc");
				}
				else
				{
					$lc = aw_global_get("LC");
				}
			}
			else
			{
				$lc = aw_ini_get("user_interface.default_language");
			}

			if (isset($trans[$lc]) && is_array($trans[$lc]) && count($trans[$lc]))
			{
				$tc = $trans[$lc];
				foreach($els as $pn => $pd)
				{
					if ($tc[$pn] != "")
					{
						if ($pd["type"] === "text")
						{
							$els[$pn]["value"] = $tc[$pn];
						}
						else
						{
							$els[$pn]["caption"] = $tc[$pn];
						}
					}
				}
			}
		}
		$tmp = array();
		foreach($els as $pn => $pd)
		{
			if ($pn === "is_translated" || $pn === "needs_translation")
			{
				continue;
			}

			if (isset($arr["values"]) && isset($arr["values"][$pn]))
			{
				$pd["value"] = $arr["values"][$pn];
			}

			if ($pd["type"] === "classificator")
			{
				$pd["object_type_id"] = $arr["ot"];
				if ($arr["for_show"] && is_oid($pd["value"]) && $this->can("view", $pd["value"]))
				{
					$tmpo = obj($pd["value"]);
					$pd["value"] = $tmpo->name();
				}
			}

			if ($pd["type"] === "textarea" && !empty($arr["for_show"]))
			{
				$pd["value"] = nl2br($pd["value"]);
			}
			$tmp[$pn] = $pd;
		}
		$ret = $tmp;
		return $ret;
	}

	function on_site_init($dbi, $site, &$ini_opts, &$log, &$osi_vars)
	{
		// create the default document config form
		$form = obj($osi_vars["doc_conf_form"]);

		// Yldine (general)
		// 100, navtoolbar
		// 200, status
		// 300, title
		// 400, tm
		// 500, lead
		// 600, content
		// 700, moreinfo
		// 800, sbt
		// 900, aliasmgr
		// Seadistused (settings)
		// 100, show_title
		// 200, showlead
		// 300, show_print
		// 400, title_clickable

		// elements:
		$els = array(
			"general" => array(
				"navtoolbar" => array(100, t("T&ouml;&ouml;riistariba")),
				"simultaneous_warning" => array(150, t("Samaaegse muutmise hoiatus")),
				"status" => array(200, t("Aktiivne")),
				"title" => array(300, t("Pealkiri")),
				"tm" => array(400, t("Kuup&auml;ev")),
				"lead" => array(500, t("Sissejuhatus")),
				"content" => array(600, t("Sisu")),
				"moreinfo" => array(700, t("Toimetamata")),
				"sbt" => array(800, t("Salvesta")),
				"aliasmgr" => array(900, t("Seostehaldur"))
			),
			"settings" => array(
				"show_title" => array(100, t("N&auml;ita pealkirja")),
				"showlead" => array(200, t("N&auml;ita sissejuhatust")),
				"show_print" => array(300, t("N&auml;ita prindi nuppu")),
				"title_clickable" => array(400, t("Pealkiri klikitav"))
			)
		);

		$this->cff_init_from_class($form, doc_obj::CLID);

		$this->cff_remove_all_props($form);

		foreach($els as $grp => $gels)
		{
			foreach($gels as $el => $ord)
			{
				$this->cff_add_prop($form, $el, array("ord" => $ord[0], "group" => $grp, "caption" => $ord[1]));
			}
		}

		$form->save();
	}

	/** Initializes given cfgform object to contain all groups and props from the given class
		@attrib api=1

		@param o required type=object
			cfgform object

		@param clid required type=int
			class id to init from

		@examples
			$cff = get_instance(CL_CFGFORM);
			$cf_obj = obj($config_form_id);
			$cff->cff_init_from_class($cf_obj, CL_MENU);
			$cff->cff_add_prop($cf_obj, "bujaka", array("caption" => t("Bujaka"), "group" => "general"));
			$cf_obj->save();
	**/
	function cff_init_from_class($o, $clid, $save = true)
	{
		// now that's the tricky part ... this thingsbum overrides
		// all the settings in the document config form
		$this->_init_properties($clid);
		$cfgu = new cfgutils();

		if ($clid == doc_obj::CLID)
		{
			$def = join("",file(aw_ini_get("basedir") . "/xml/documents/cfgform_default.xml"));
			list($proplist,$grplist, $layout) = $cfgu->parse_cfgform(array("xml_definition" => $def), true);
			$this->cfg_proplist = $proplist;
			$this->cfg_groups = $grplist;
			$this->cfg_layout = $layout;
		}
		else
		{
			$tmp = aw_ini_get("classes");
			$fname = $tmp[$clid]["file"];
			$def = join("",file(aw_ini_get("basedir") . "/xml/properties/class_base.xml"));
			list($proplist,$grplist, $layout) = $cfgu->parse_cfgform(array("xml_definition" => $def), true);
			$this->cfg_proplist = $proplist;
			$this->cfg_groups = $grplist;
			$this->cfg_layout = $layout;

			$fname = basename($fname);
			$def = join("",file(aw_ini_get("basedir") . "/xml/properties/$fname.xml"));
			list($proplist,$grplist, $layout) = $cfgu->parse_cfgform(array("xml_definition" => $def), true);
			$this->cfg_proplist = $this->cfg_proplist + $proplist;
			$this->cfg_groups = $this->cfg_groups + $grplist;
			$this->cfg_layout = $this->cfg_layout + $layout;
		}

		if ($save)
		{
			$o->set_prop("subclass", $clid);
			$this->_save_cfg_groups($o);
			$this->_save_cfg_props($o);
			$this->_save_cfg_layout($o);
		}
	}

	/** Removes all properties from the given config form
		@attrib api=1

		@param o required type=object
			cfgform object

		@examples
			$cff = get_instance(CL_CFGFORM);
			$cf_obj = obj($config_form_id);
			$cff->cff_remove_all_props();
			$cff->cff_add_prop($cf_obj, "bujaka", array("caption" => t("Bujaka"), "group" => "general"));
			$cf_obj->save();
	**/
	function cff_remove_all_props($o)
	{
		$o->set_meta("cfg_proplist", array());
	}

	/** Adds the given property to the given config form object
		@attrib api=1

		@param o required type=object
			cfgform object

		@param pn required type=string
			name of property to add

		@param pd required type=array
			array(caption, group, ord) for the property

		@examples
			$cff = get_instance(CL_CFGFORM);
			$cf_obj = obj($config_form_id);
			$cff->cff_init_from_class($cf_obj, CL_MENU);
			$cff->cff_add_prop($cf_obj, "bujaka", array("caption" => t("Bujaka"), "group" => "general"));
			$cf_obj->save();
	**/
	function cff_add_prop($o, $pn, $pd)
	{
		$this->cfg_proplist = $o->meta("cfg_proplist");
		$this->cfg_proplist[$pn] = array(
			"name" => $pn,
			"caption" => $pd["caption"],
			"group" => $pd["group"],
			"ord" => $pd["ord"]
		);
		$this->_save_cfg_props($o);
	}

	function get_cfg_layout($o)
	{
		$ll =  $o->meta("cfg_layout");

		$lc = aw_ini_get("user_interface.default_language");
		$trans = $o->meta("layout_translations");
		if (isset($trans[$lc]) && is_array($trans[$lc]) && count($trans[$lc]))
		{
			$tc = $trans[$lc];
			foreach($ll as $pn => $pd)
			{
				if ($tc[$pn] != "")
				{
					$ll[$pn]["area_caption"] = $tc[$pn];
				}
			}
		}
		return $ll;
	}

	/** Returns the properties for the config form
		@attrib api=1

		@param id required type=oid
			The oid if the config form to load the props from

		@returns
			array of property info for the config form

		@examples
			$cff = get_instance(CL_CFGFORM);
			foreach($cff->get_cfg_proplist($cfgform_oid) as $pn => $pd)
			{
				echo "prop = $pn , caption = $pd[caption] \n";
			}
	**/
	function get_cfg_proplist($id)
	{
		$o = obj($id);
		$ret = (array) $o->meta("cfg_proplist");
		$lc = aw_ini_get("user_interface.default_language");
		$trans = $o->meta("translations");
		$tbl_capt_trans = $o->meta("tbl_capt_translations");

		// okay, here, if there is no translation for the requested language, then
		// read the captions from the translations file.

		$read_from_trans = true;
		if (isset($trans[$lc]) && is_array($trans[$lc]) && count($trans[$lc]))
		{
			$tc = $trans[$lc];
			$tc_capt = $tbl_capt_trans[$lc];
			foreach($ret as $pn => $pd)
			{
				if ($tc[$pn] != "")
				{
					if ($pd["type"] === "text")
					{
						$ret[$pn]["value"] = $tc[$pn];
					}
					else
					{
						$ret[$pn]["caption"] = $tc[$pn];
					}
					$read_from_trans = false;
				}
				if ($tc_capt[$pn."_tbl_capt"] != "")
				{
					$ret[$pn]["emb_tbl_caption"] = $tc_capt[$pn."_tbl_capt"];
				}
				if($tc[$pn."_comment"] != "")
				{
					$ret[$pn]["comment"] = $tc[$pn."_comment"];
				}
			}
		}

		// also eval all controllers
		foreach((array)$o->prop("cfg_groups") as $grpn => $grpdat)
		{
			if (isset($grpdat["grpctl"]) && is_oid($grpdat["grpctl"]) && $this->can("view", $grpdat["grpctl"]))
			{
				$ctl = obj($grpdat["grpctl"]);
				$ctl_i = $ctl->instance();
				$ps = $ctl_i->check_property($grpdat, $ctl->id(), $_GET, $grpdat);
				foreach(safe_array($ps) as $pn => $pd)
				{
					$pd["group"] = $grpn;
					$pd["force_display"] = 1;
					$ret[$pn] = $pd;
				}
			}
		}

		//see v&auml;rk siis kontrollib, kas miskile kasutajale on mingi omadus &auml;kki maha keeratud
		$show_to_groups = $o->meta("show_to_groups");
		$user_group_list = aw_global_get("gidlist_oid");

		foreach($ret as $key => $val)
		{
			if ($key === "needs_translation" || $key === "is_translated")
			{
				unset($ret[$key]);
			}

			if(isset($show_to_groups[$key]))
			{
				$allowed_to_see = 0;

				foreach($user_group_list as $user_group)
				{
					if(!empty($show_to_groups[$key][$user_group]))
					{
						$allowed_to_see = 1;
						break;
					}
				}

				if(!$allowed_to_see)
				{
					unset($ret[$key]);
				}
			}
		}

		return $ret;
	}

/* replaced by self::_sort_groups()
	function __grp_s($a, $b)
	{
		if ($a["ord"] == $b["ord"])
		{
			return 0;
		}
		return $a["ord"] > $b["ord"];
	}
 */

	/** Returns the groups defined in the config form
		@attrib api=1

		@param id required type=oid
			The oid of the config form to load

		@returns
			array of group data for the config form

		@examples
			$cff = get_instance(CL_CFGFORM);
			foreach($cff->get_cfg_groups($cfgform_oid) as $group_name => $group_data)
			{
				echo "prop = $group_name , caption = $group_data[caption] \n";
			}
	**/
	function get_cfg_groups($id)
	{
		$o = obj($id);
		$this->cfg_groups = $o->meta("cfg_groups");
		$this->cfgview_grps = safe_array($o->prop("cfgview_grps"));

		// backward compatibility.
		if (!$o->meta("cfg_groups_sorted"))
		{
			$this->_sort_groups();
			$o->set_meta("cfg_groups", $this->cfg_groups);
			$o->set_meta("cfg_groups_sorted", 1);
			aw_disable_acl();
			$o->save();
			aw_restore_acl();
		}
		//

		$ret = $this->cfg_groups;

/* sorting is now done before saving meta.cfg_groups
		$has = false;
		foreach(safe_array($ret) as $k => $v)
		{
			if (isset($v["ord"]) && !empty($v["ord"]))
			{
				$has = true;
			}
		}

		if ($has)
		{
			uasort($ret, array(&$this, "__grp_s"));
		}
 */

		$lc = aw_ini_get("user_interface.default_language");
		$trans = $o->meta("grp_translations");
		$read_from_trans = true;
		if (isset($trans[$lc]) && is_array($trans[$lc]) && count($trans[$lc]))
		{
			$tc = $trans[$lc];
			foreach($ret as $pn => $pd)
			{
				if ($tc[$pn] != "")
				{
					$ret[$pn]["caption"] = $tc[$pn];
					$read_from_trans = false;
				}
			}
		}

		if ($read_from_trans && aw_global_get("LC") != $o->lang())
		{
			$tmp = obj();
			$tmp->set_class_id($o->subclass());
			foreach($tmp->get_group_list() as $gn => $gd)
			{
				// trick here is, that we do not need to redo the t() calls, because the translations are already loaded
				// so we just copy the captions
				if (isset($ret[$gn]))
				{
					$ret[$gn]["caption"] = $gd["caption"];
				}
			}
		}

		$si = __get_site_instance();
		$has_cb = method_exists($si, "callback_get_group_display");
		foreach($ret as $gn => $gd)
		{
			if (isset($gd["grp_d_ctl"]) and $this->can("view", $gd["grp_d_ctl"]))
			{
				$ctl = obj($gd["grp_d_ctl"]);
				$ctli = $ctl->instance();
				$rv = $ctli->check_property($ret[$gn], $ctl->id(), $gd);
				if ($rv == PROP_IGNORE)
				{
					unset($ret[$gn]);
				}
			}
			elseif ($has_cb)
			{
				if ($si->callback_get_group_display($o, $gn) == PROP_IGNORE)
				{
					unset($ret[$gn]);
				}
			}
			elseif (isset($_GET["awcb_display_mode"]) && "cfg_embed" === $_GET["awcb_display_mode"] and !in_array($gn, $this->cfgview_grps))
			{
				unset($ret[$gn]);
			}
		}

		if (isset($_GET["awcb_display_mode"]) && "cfg_embed" === $_GET["awcb_display_mode"])
		{ // set first available grp as default for cfgview group selection
			reset($ret);
			$gn = key($ret);
			$ret[$gn]["default"] = true;
		}

		foreach(safe_array($o->meta("buttons")) as $gn => $bts)
		{
			if (isset($ret[$gn]))
			{
				$ret[$gn]["back_button"] = $bts["back"];
				$ret[$gn]["forward_button"] = $bts["next"];
			}
		}
		return $ret;
	}

	/** Returns the site-wide default config form for the given class
		@attrib api=1 params=name

		@param clid required type=int
			The class id to return the default form for

		@returns
			The oid of the system default config form for the class or false if no form exists

		@examples
			$cf = get_instance(CL_CFGFORM);
			if (($form_oid = $cf->get_sysdefault(array("clid" => CL_MENU))) !== false)
			{
				echo "default cfgorm for CL_MENU is ".$form_oid."<br>";
			}
	**/
	function get_sysdefault($arr = array())
	{
		// 2 passes, because I need to know which element is active before
		// doing the table
		$active = false;
		$ol = new object_list(array(
			"class_id" => cfgform_obj::CLID,
			"subclass" => $arr["clid"],
			"lang_id" => array(),
			"flags" => array(
				"mask" => OBJ_FLAG_IS_SELECTED,
				"flags" => OBJ_FLAG_IS_SELECTED
			)
		));
		if (sizeof($ol->ids()) > 0)
		{
			$first = $ol->begin();
			$active = $first->id();
		};
		return $active;
	}


/** Deletes a group (tab).
	@attrib name=delete_grp
	@param id required type=int acl=view
	@param name required
	@param return_url optional
**/
	function delete_grp($arr)
	{
		$this_o = obj ($arr["id"]);
		$name = $arr["name"];
		$this->cfg_groups = $this_o->meta("cfg_groups");
		$this->cfg_proplist = $this_o->meta("cfg_proplist");
		$deleted_groups = array($name);

		// delete group
		unset($this->cfg_groups[$name]);

		// delete children if any
		foreach($this->cfg_groups as $key => $data)
		{
			if ($name === $data["parent"])
			{
				$deleted_groups[] = $key;
				unset($this->cfg_groups[$key]);
			}
		}

		// remove properties from deleted groups
		foreach ($this->cfg_proplist as $key => $data)
		{
			if (in_array($data["group"], $deleted_groups))
			{
				unset($this->cfg_proplist[$key]);
			}
		}

		// save
		$this->_save_cfg_groups($this_o);
		$this->_save_cfg_props($this_o);
		$this_o->save();

		return aw_url_change_var("just_saved", "1", $arr["return_url"]);
	}

	/**
		@attrib name=delete_layouts
		@param id required type=int
		@param selection required type=array
	**/
	function delete_layouts ($arr)
	{
		$sel = (array) $arr["selection"];

		if (count($sel))
		{
			error::raise_if((!$this->can("edit", $arr["id"])), array(
				"msg" => t("no edit access"),
				"fatal" => true
			));

			$this_o = new object($arr["id"]);
			$this->_init_cfgform_data($this_o);
			$i = 0;

			foreach ($sel as $deleted_layout_name)
			{
				unset($this->cfg_layout[$deleted_layout_name]);

				// remove deleted layout where it's a parent
				foreach ($this->cfg_layout as $name => $data)
				{
					if ($deleted_layout_name === $data["parent"])
					{
						$this->cfg_layout[$name]["parent"] = null;
					}
				}
			}

			$this->_save_cfg_layout($this_o);
			$this_o->save();
		}

		$return_url = $this->mk_my_orb("change", array(
			"id" => $arr["id"],
			"return_url" => $arr["return_url"],
			"group" => $arr["group"],
			"subgroup" => $arr["subgroup"],
		));
		return $return_url;
	}

	/**
		@attrib name=add_new_layout
		@param id required type=int
	**/
	function add_new_layout ($arr)
	{
		error::raise_if((!$this->can("edit", $arr["id"])), array(
			"msg" => t("no edit access"),
			"fatal" => true
		));

		$this_o = new object($arr["id"]);
		$this->_init_cfgform_data($this_o);
		$i = 0;

		do
		{
			$tmp_name = $this->default_new_layout_name . ++$i;
		}
		while (array_key_exists($tmp_name, $this->cfg_layout));

		$this->cfg_layout[$tmp_name] = array("type" => "hbox");
		$this->_save_cfg_layout($this_o);
		$this_o->save();

		$return_url = $this->mk_my_orb("change", array(
			"id" => $arr["id"],
			"return_url" => $arr["return_url"],
			"group" => $arr["group"],
			"subgroup" => $arr["subgroup"],
		));
		return $return_url;
	}

	/**
		@attrib name=merge
		@param id required type=int
		@param scope required type=string
		@param return_url optional type=string
	**/
	function merge_cfg_changes($arr)
	{
		error::raise_if((!$this->can("edit", $arr["id"])), array(
			"msg" => t("no edit access"),
			"fatal" => true
		));

		error::raise_if((!isset($this->cfg_load_scope_index[$arr["scope"]])), array(
			"msg" => t("invalid action scope"),
			"fatal" => true
		));

		$scopes = $this->cfg_load_scope_index[$arr["scope"]];
		$this_o = new object($arr["id"]);
		$this->_init_cfgform_data($this_o);
		$this->cff_init_from_class($this_o, $this_o->subclass(), false);

		foreach ($scopes as $scope)
		{
			switch ($scope)
			{
				case "properties":
					foreach ($this->prplist as $name => $data)
					{
						if (array_key_exists($name, $this->cfg_proplist))
						{ // add new options if any
							$this->prplist[$name] = $data + $this->cfg_proplist[$name];
						}
						else
						{ // remove property if removed in class definition (will conflict when/if cfgform-specific properties will be implemented)
							unset($this->prplist[$name]);
						}
					}

					$this->_save_cfg_props($this_o);
					break;

				// groups and layouts removed in cl. dfn. won't be removed from cfgform
				case "groups":
					$this->cfg_groups = $this->grplist + $this->cfg_groups;
					$this->_save_cfg_groups($this_o);
					break;

				case "layouts":
					$this->cfg_layout = $this->layout + $this->cfg_layout;
					$this->_save_cfg_layout($this_o);
					break;
			}
		}

		$this_o->save();
		return $arr["return_url"];
	}

	/**
		@attrib name=reload
		@param id required type=int
		@param scope required type=string
		@param return_url optional type=string
	**/
	function reload_cfg($arr)
	{
		error::raise_if((!$this->can("edit", $arr["id"])), array(
			"msg" => t("no edit access"),
			"fatal" => true
		));

		error::raise_if((!isset($this->cfg_load_scope_index[$arr["scope"]])), array(
			"msg" => t("invalid action scope"),
			"fatal" => true
		));

		$scopes = $this->cfg_load_scope_index[$arr["scope"]];
		$this_o = new object($arr["id"]);
		$this->_init_cfgform_data($this_o);
		$this->cff_init_from_class($this_o, $this_o->subclass(), false);

		foreach ($scopes as $scope)
		{
			switch ($scope)
			{
				case "properties":
					$this->_save_cfg_props($this_o);
					break;

				case "groups":
					$this->_save_cfg_groups($this_o);
					break;

				case "layouts":
					$this->_save_cfg_layout($this_o);
					break;
			}
		}

		$this_o->save();
		return $arr["return_url"];
	}

	function _edit_groups_tb($arr)
	{
		$this_o = $arr["obj_inst"];
		$toolbar = $arr["prop"]["vcl_inst"];

		// add groups
		$toolbar->add_button(array(
			"name" => "add",
			"url" => aw_url_change_var("cfgform_add_grp", "1"),
			"img" => "new.gif",
			"tooltip" => t("Lisa tab"),
		));

		// save
		$toolbar->add_button(array(
			"name" => "save",
			"url" => "javascript:submit_changeform()",
			"img" => "save.gif",
			"tooltip" => t("Salvesta"),
		));

		// delete for user defined groups
		$delete_url = $this->mk_my_orb("delete_grp", array(
			"id" => $this_o->id (),
			"name" => "cfgform_delete_grp_name",
			"return_url" => get_ru(),
		));

		$toolbar->add_menu_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"tooltip" => t("Kustuta tab"),
		));

		foreach ($this->grplist as $name => $data)
		{
			$toolbar->add_menu_item(array(
				"parent" => "delete",
				"text" => t("Kustuta ") . $data["caption"] . " ($name)",
				"link" => str_replace("cfgform_delete_grp_name", $name, $delete_url),
			));
		}
	}

	function _init_edit_groups_tbl(&$t)
	{
		$t->define_field(array(
			"name" => "grp",
			"caption" => t("Tab"),
			"chgbgcolor" => "bg_colour",
		));
		$t->define_field(array(
			"name" => "caption",
			"caption" => t("Pealkiri"),
			"chgbgcolor" => "bg_colour",
		));
		$t->define_field(array(
			"name" => "ord",
			"caption" => t("Jrk."),
			"chgbgcolor" => "bg_colour",
		));
		$t->define_field(array(
			"name" => "focus",
			"caption" => t("Fookus"),
			"chgbgcolor" => "bg_colour",
		));
		$t->define_field(array(
			"name" => "ctrl",
			"caption" => t("Sisu kontroller"),
			"chgbgcolor" => "bg_colour",
		));
		$t->define_field(array(
			"name" => "view_ctrl",
			"caption" => t("N&auml;itamise kontroller"),
			"chgbgcolor" => "bg_colour",
		));
		$t->define_field(array(
			"name" => "submit_btn_text",
			"caption" => t("Submit nupu tekst"),
			"chgbgcolor" => "bg_colour"
		));
		$t->define_field(array(
			"name" => "opt_view",
			"caption" => '<a href="javascript:selall(\'grpview\')">' . t("V") . '</a>',
			"tooltip" => t("Vaikimisi view vaade"),
			"chgbgcolor" => "bg_colour",
		));
		$t->define_field(array(
			"name" => "opt_show",
			"caption" => '<a href="javascript:selall(\'grphide\')">' . t("N") . '</a>',
			"tooltip" => t("N&auml;ita tabi"),
			"chgbgcolor" => "bg_colour",
		));
		$t->define_field(array(
			"name" => "opt_submit",
			"caption" => '<a href="javascript:selall(\'grpsubmit\')">' . t("S") . '</a>',
			"tooltip" => t("N&auml;ita submit nuppu"),
			"chgbgcolor" => "bg_colour",
		));
	 }

	function _edit_groups_tbl($arr)
	{
		$this_o = $arr["obj_inst"];
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_edit_groups_tbl($t);
		$grps = $this->grplist;

		$tps = array(
			"" => t("vaikestiil"),
			"stacked" => t("pealkiri yleval, sisu all"),
		);
		$ctr_list = new object_list($this_o->connections_from(array("type" => "RELTYPE_VIEWCONTROLLER")));

		// distribute props by group for selectbox options
		$props_by_grp = array();
		$focusable_prop_types = array(
			"textbox", "select", "password", "fileupload", "textarea", "checkbox"
		);

		foreach ($this->prplist as $name => $data)
		{
			if (in_array($data["type"], $focusable_prop_types))
			{
				$props_by_grp[$data["group"]][$name] = isset($data["caption"]) ? $data["caption"] : "";
			}

			if ("submit" === $data["type"] and !empty($data["group"]))
			{
				if (is_array($data["group"]))
				{
					foreach ($data["group"] as $grp)
					{
						$grps[$grp]["submit_btn_text"] = $data["caption"];
					}
				}
				else
				{
					$grps[$data["group"]]["submit_btn_text"] = $data["caption"];
				}
			}
		}

		foreach($grps as $gn => $gd)
		{
			$bg_colour = empty($gd["parent"]) ? "silver" : false;
			$t->define_data(array(
				"grp" => $gn . " <small>(" . $gd["caption"] . ")</small>",
				"caption" => html::textbox(array(
					"name" => "grpcaption[{$gn}]",
					"size" => 25,
					"value" => isset($gd["caption"]) ? $gd["caption"] : ""
				)),
				"ord" => html::textbox(array(
					"name" => "grpord[{$gn}]",
					"size" => 2,
					"value" => isset($gd["ord"]) ? $gd["ord"] : ""
				)),
				"submit_btn_text" => html::textbox(array(
					"name" => "grpsubmit_btn_text[{$gn}]",
					"size" => 5,
					"value" => isset($gd["submit_btn_text"]) ? $gd["submit_btn_text"] : ""
				)),
				"style" => html::select(array(
					"name" => "grpstyle[$gn]",
					"options" => $tps,
					"selected" => isset($gd["grpstyle"]) ? $gd["grpstyle"] : ""
				)),
				"focus" => html::select(array(
					"name" => "grpfocus[$gn]",
					"options" => array("" => "") + (isset($props_by_grp[$gn]) ? (array) $props_by_grp[$gn] : array()),
					"selected" => isset($gd["focus"]) ? $gd["focus"] : ""
				)),
				"ctrl" => html::select(array(
					"name" => "grpctl[$gn]",
					"value" => isset($gd["grpctl"]) ? $gd["grpctl"] : "",
					"options" => html::get_empty_option() + $ctr_list->names(),
				)),
				"view_ctrl" => html::select(array(
					"name" => "grp_d_ctl[{$gn}]",
					"value" => isset($gd["grp_d_ctl"]) ? $gd["grp_d_ctl"] : "",
					"options" => html::get_empty_option() + $ctr_list->names()
				)),
				"opt_view" => html::checkbox(array(
					"name" => "grpview[{$gn}]",
					"value" => 1,
					"checked" => isset($gd["grpview"]) ? $gd["grpview"] : ""
				)),
				"opt_show" => html::checkbox(array(
					"name" => "grphide[{$gn}]",
					"value" => 1,
					"checked" => isset($gd["grphide"]) ? (int) !$gd["grphide"] : 0
				)),
				"opt_submit" => html::checkbox(array(
					"name" => "grpsubmit[{$gn}]",
					"value" => 1,
					"checked" => isset($gd["submit"]) ? (int) ($gd["submit"] !== "no") : 1
				)),
				"bg_colour" => $bg_colour
			));
		}

		$t->set_sortable(false);
	}

	function _init_group_movement_t(&$t)
	{
		$t->define_field(array(
			"name" => "grp",
			"caption" => t("Tab"),
			"align" => "left",
			"chgbgcolor" => "bg_colour",
		));
		$t->define_field(array(
			"name" => "back_button",
			"caption" => t("Tagasi nupp"),
			"align" => "center",
			"chgbgcolor" => "bg_colour",
		));
		$t->define_field(array(
			"name" => "next_button",
			"caption" => t("Edasi nupp"),
			"align" => "center",
			"chgbgcolor" => "bg_colour",
		));
	}

	function _group_movement($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_group_movement_t($t);

		// list groups and let the user select groups that you go forward/back
		$grps = new aw_array($arr["obj_inst"]->meta("cfg_groups"));

		$sel = array("" => t("Ei ole nuppu"));
		foreach($grps->get() as $gn => $gd)
		{
			$sel[$gn] = $gd["caption"];
		}

		$buttons = $arr["obj_inst"]->meta("buttons");
		foreach($grps->get() as $gn => $gd)
		{
			$bg_colour = empty($gd["parent"]) ? "silver" : false;
			$t->define_data(array(
				"grp" => (!empty($gd["parent"]) ? html::space(5) : "").$gd["caption"],
				"back_button" => html::select(array(
					"name" => "bts[$gn][back]",
					"options" => $sel,
					"value" => $buttons[$gn]["back"]
				)),
				"next_button" => html::select(array(
					"name" => "bts[$gn][next]",
					"options" => $sel,
					"value" => $buttons[$gn]["next"]
				)),
				"bg_colour" => $bg_colour,
			));
		}
		$t->set_sortable(false);
	}

	function parse_alias($args = array())
	{
		$cff = obj($args["alias"]["target"]);
		if(is_object($cff) && $cff->cfgform_id_from_url && $this->can("view", $_GET["cfgform_id"]))
		{
			$args["alias"]["target"] = $_GET["cfgform_id"];
		}
		if(!empty($this->cfgclassview_alias_parsed))
		{
			$inst = new cfgform();
			$inst->embedded = 1;
		}
		else
		{
			$inst = $this;
			$this->cfgclassview_alias_parsed = true;
		}

		$result = $inst->get_class_cfgview(array(
			"id" => $args["alias"]["target"],
			"display_mode" => "cfg_embed",
		));
		flush();
		return $result;
	}

	// embed cfgform as alias and display configured class interface
	function get_class_cfgview($args)
	{
		// request vars
		if (empty($this->cfgview_vars))
		{
			$this->cfgview_vars = (array) $_GET + (array) $_POST + (isset($AW_GET_VARS) ? ((array) $AW_GET_VARS) : array());
		}

		if (!empty($args["submit_vars"]))
		{
			foreach($args["submit_vars"] as $k => $v)
			{
				$this->cfgview_vars[$k] = $v;
			}
		}
		// get view
		$content = "";
		$main_view = (empty($this->cfgview_vars["class"]) or $class === $this->cfgview_vars["class"]);

		if ($main_view)
		{
			$this_o = obj($args["id"]);
			$classes = aw_ini_get("classes");
			$class = strtolower(substr($classes[$this_o->prop("subclass")]["def"], 3));
			$this->_get_cfgview_params($this_o);
			$action = $this->cfgview_vars["action"];
			$this->cfgview_grps = safe_array($this_o->prop("cfgview_grps"));

			if ("new" !== $action and !empty($this->cfgview_vars["id"]) and !is_oid($this->cfgview_vars["id"]))
			{
				return "";
			}

			if ("new" === $action and empty($this->cfgview_vars["parent"]))
			{
				$this->cfgview_vars["parent"] = $this_o->id();
			}

			if ("new" === $action && !$this->can("add", $this->cfgview_vars["parent"]))
			{
				return "";
			}

			$this->cfgview_vars["cfgform"] = $args["id"];
			$this->cfgview_vars["class"] = $class;

			if (empty($this->cfgview_vars["group"]))
			{
				$this->cfgview_vars["group"] = reset($this->cfgview_grps);
			}

			$this->cfgview_vars["awcb_cfgform"] = $args["id"];
			$this->cfgview_vars["awcb_display_mode"] = $args["display_mode"];

			$orb = new orb();
			$orb->process_request(array(
				"class" => $class,
				"action" => $action,
				"reforb" => !empty($this->cfgview_vars["reforb"]),
				"user"	=> 1,//!!! whats that for?
				"vars" => $this->cfgview_vars,
				"silent" => false,
			));
			$content = $orb->get_data();
		}

		return $content;
	}

	function _get_cfgview_params($this_o)
	{
		$action = array_key_exists($this_o->prop("cfgview_action"), $this->cfgview_actions) ? $this_o->prop("cfgview_action") : "view";

		if ("cfgview_change_new" === $action or "cfgview_view_new" === $action)
		{
			$this->_load_cfgview_params($this_o, "new");

			if (!is_oid($this->cfgview_vars["id"]))
			{
				$action = "new";
			}
			elseif ("cfgview_change_new" === $action)
			{
				$action = "change";
				$this->_load_cfgview_params($this_o, $action);
			}
			elseif ("cfgview_view_new" === $action)
			{
				$action = "view";
				$this->_load_cfgview_params($this_o, $action);
			}
		}
		else
		{
			$this->_load_cfgview_params($this_o, $action);
		}

		$this->cfgview_vars["action"] = $action;
	}

	function _load_cfgview_params($this_o, $action)
	{
		$prop_name_indic = "cfgview_params_" . $action . "_loaded";

		if (empty($this->$prop_name_indic))
		{
			if($this->can("view", $this_o->prop("cfgview_" . $action . "_params_from_controller")))
			{
				$params = array();
				$controller_inst = obj($this_o->prop("cfgview_" . $action . "_params_from_controller"));
				eval($controller_inst->prop("formula"));
				$this->cfgview_vars = array_merge($this->cfgview_vars, $params);
				$_GET = array_merge($_GET, $params);
			}
			else
			{
				$params = explode("&", $this_o->prop("cfgview_" . $action . "_params"));
				foreach ($params as $param)
				{
					$param = explode("=", $param, 2);
					$this->cfgview_vars[$param[0]] = $param[1];
				}
			}

			$this->$prop_name_indic = true;
		}
	}

	/// submenus from object interface methods
	function make_menu_item($this_o, $level, $parent_o, $site_show_i,$cnt_menus)
	{
		if (empty($this->awcb_request_vars))
		{
			$this->awcb_request_vars = (array) $_GET + (array) $_POST + (array) $AW_GET_VARS;
		}

		if (empty($this->awcb_request_vars["class"]))
		{
			// init
			if (!isset($this->grplist))
			{
				$this->_init_cfgform_data($this_o);
				$this->cfgview_grps = safe_array($this_o->prop("cfgview_grps"));
				$this->_get_cfgview_params($this_o);
			}

			// no groups for new object form
			if (!is_oid($this->awcb_request_vars["id"]))
			{
				return false;
			}

			// get next group
			do
			{
				if (!isset($this->make_menu_item_counter))
				{
					$this->make_menu_item_counter = 0;
					$grp_name = current($this->cfgview_grps);
				}
				else
				{
					$grp_name = next($this->cfgview_grps);
				}
			}
			while (!empty($this->grplist[$grp_name]["grphide"]));

			++$this->make_menu_item_counter;

			// selected grp
			if ($this->awcb_request_vars["group"] == $grp_name)
			{
			}

			//
			if (false === $grp_name)
			{
				$this->make_menu_item_counter = NULL;
				return false;
			}
			else
			{
				$vars = array (
					"just_saved" => NULL,
					"group" => $grp_name
				);
				$link = aw_url_change_var($vars);

				return array(
					"text" => $this->grplist[$grp_name]["caption"],
					"link" => $link,
					// "section" => $o_91_2->id(),
					// "menu_edit" => $this->__helper_menu_edit($o_91_2),
					// "parent_section" => is_object($o_91_1) ? $o_91_1->id() : $o_91_2->parent(),
					// "comment" => "komment",
				);
			}
		}
		else
		{
			$this->make_menu_item_counter = null;
			return false;
		}
	}

	function _get_orb_settings($arr)
	{
		$t = $arr["prop"]["vcl_inst"];

		$t->define_field(array(
			"name" => "action",
			"caption" => "action",
			"align" => "center"
		));

		$o = $arr["obj_inst"];
		$groups_list = new object_list($o->connections_from(array("type" => "RELTYPE_VIEW_DFN_GRP")));

		if (!$groups_list->count())
		{
			$groups_list = new object_list(array(
				"class_id" => array(group_obj::CLID),
				"type" => new obj_predicate_compare(obj_predicate_compare::LESS, 1),
				"lang_id" => "%",
			));
		}

		foreach($groups_list->arr() as  $group_obj)
		{
			$t->define_field(array(
				"name" => $group_obj->id(),
				"caption" => html::href(array(
					"caption" => $group_obj->name(),
					"url" => "#",
					"onClick" => "aw_sel_chb(document.changeform,\"[".$group_obj->id()."]\");"
				))
			));
		}

		$orb = new orb();
		$clss = aw_ini_get("classes");
		$methods = $orb->get_class_actions(array(
			"class" => basename($clss[$arr["obj_inst"]->subclass()]["file"])
		));
		$dd = $o->meta("orb_acl");
		foreach($methods as $method)
		{
			$d = array(
				"action" => $method
			);
			foreach($groups_list->arr() as  $group_obj)
			{
				$d[$group_obj->id()] = html::checkbox(array(
					"name" => "d[$method][".$group_obj->id()."]",
					"value" => 1,
					"checked" => is_array($dd) ? $dd[$method][$group_obj->id()] == 1 : false,
				));
			}

			$t->define_data($d);
		}
		$t->set_caption(t("Vali, millised kasutajagrupid ei tohi milliseid actione kasutada."));
	}

	function _set_orb_settings($arr)
	{
		$arr["obj_inst"]->set_meta("orb_acl", $arr["request"]["d"]);
	}

	/** Checks if the current user has access to the given method in the given cfgform
		@attrib api=1 params=name
		@param action required type=string
		@param cfgform required type=int
	**/
	function check_user_orb_access($arr)
	{
		$cf = obj($arr["cfgform"]);
		$orb_data = $cf->meta("orb_acl");

		$gl = aw_global_get("gidlist_pri_oid");
                asort($gl);
                $gl = array_keys($gl);
                $grp = $gl[1];
                if (count($gl) == 1)
                {
                        $grp = $gl[0];
                }

		if (is_array($orb_data[$arr["action"]]) && $orb_data[$arr["action"]][$grp] == 1)
		{
			return false;
		}
		return true;
	}

	/**checks if class has a cfgform, if it has, get properties from that, otherwise gets them from the class itself
		@attrib api=1 params=clid
		@param clid required type=int
		@returns array of properties
	**/
	function get_property_list($clid)
	{
		$goodprops = array(0=>"");
		$manager_form = $this->get_sysdefault(array("clid"=>$clid));
		if($manager_form)
		{
			foreach($this->get_cfg_proplist($manager_form) as $pn=>$pd)
			{
				if($pd["caption"])
				{
					$goodprops[$pn] = $pd["caption"];
				}
			}
		}
		else
		{
			$o = obj();
			$o->set_class_id($clid);
			$list = $o->get_property_list();
			foreach($list as $lid => $li)
			{
				if(!empty($li["caption"]))
				{
				//if(strpos($li["type"], "text")>-1 && $li["caption"])
					$goodprops[$lid] = $li["caption"];
				}
			}
		}
		return $goodprops;
	}

	/**
		@attrib name=disable_property api=1
		@param id required type=int
		@param property required type=string
	**/
	function disable_property($arr)
	{
		extract($arr);
		if(!$this->can("view", $id))
			return false;

		$o = obj($id);
		$cfg_proplist = $o->meta("cfg_proplist");
		$cfg_proplist[$property]["disabled"] = 1;
		$o->set_meta("cfg_proplist", $cfg_proplist);
		$o->save();
	}

	/**
		@attrib name=hide_property api=1
		@param id required type=int
		@param property required type=string
	**/
	function hide_property($arr)
	{
		extract($arr);
		if(!$this->can("view", $id))
			return false;

		$o = obj($id);
		$cfg_proplist = $o->meta("cfg_proplist");
		$cfg_proplist[$property]["hidden"] = 1;
		$o->set_meta("cfg_proplist", $cfg_proplist);
		$o->save();
	}

	/**
		@attrib name=remove_property api=1
		@param id required type=int
		@param property required type=string
	**/
	function remove_property($arr)
	{
		extract($arr);
		if(!$this->can("view", $id))
			return false;

		$o = obj($id);
		$o->remove_property($property);
		$o->save();
	}

	private function add_group($o, $name = false, $parent = false, $caption = "")
	{
		if (!isset($this->cfg_groups))
		{
			$this->_init_cfgform_data($o);
		}

		if (false === $name)
		{
			$name = "userdefined_group_";
			$i = 1;

			while (array_key_exists($name . $i, $this->cfg_groups))
			{
				$i++;
			}

			$name .= $i;
		}
		else
		{
			$name = strtolower($name);

			if ((strlen($name) < 2) or preg_match("/[^a-z_]/", $name))
			{
				return t("Ebasobiv tabi nimi.");
			}

			if (array_key_exists($name, $this->cfg_groups))
			{
				return t("Selle nimega tab juba olemas.");
			}
		}

		if (!empty($parent) and (!array_key_exists($parent, $this->cfg_groups) or !empty($this->cfg_groups[$parent]["parent"])))
		{
			return t("Selle tabi alla pole v&otilde;imalik luua.");
		}

		$this->cfg_groups[$name] = array(
			"caption" => empty($caption) ? sprintf(t("Uus tab %s"), $i) : $caption,
			"user_defined"  => 1
		);

		if ($parent)
		{
			$this->cfg_groups[$name]["parent"] = $parent;
		}

		return true;
	}

	/** Returns default cfg form property list for the class id of the object/oid given. If no cfg form exists, returns the property list defined in the class header.
	@attrib name=get_default_cfg_proplist api=1 params=name

	@param o optional type=object acl=view

	@param oid optional type=oid acl=view

	@param clid optional type=class_id default=CL_CRM_PERSON
	**/
	public function get_default_proplist($arr)
	{
		$o = isset($arr["o"]) && is_object($arr["o"]) && $this->can("view", $arr["o"]->id()) ? $arr["o"] : (isset($arr["oid"]) && is_oid($arr["oid"]) ? obj($arr["oid"]) : obj());
		$clid = $this->can("view", $o->id()) ? $o->class_id() : (isset($arr["clid"]) && is_class_id($arr["clid"]) ? $arr["clid"] : crm_person_obj::CLID);

		if(!$this->can("view", $o->id()))
		{
			$o = obj();
			$o->set_class_id($clid);
		}

		$cffi = new cfgform();
		$cff = $cffi->get_sysdefault(array("clid" => $clid));
		return $this->can("view", $cff) ? $cffi->get_cfg_proplist($cff) : $o->get_property_list();
	}

	function is_active_property($cf, $prop)
	{
		$cfg_proplist = $cf->meta("cfg_proplist");
		return isset($cfg_proplist[$prop]);
	}

	/**
		@attrib name=cfadm_click_prop
		@param oid required
		@param prop required
	**/
	public function cfadm_click_prop($arr)
	{
		if (empty($_SESSION["cfg_admin_mode"]))
		{
			die("");
		}

		$o = obj($arr["oid"]);
		$this->clid = $o->class_id();
		$cfid = $this->get_cfgform_for_object(array(
			"args" => $arr,
			"obj_inst" => $o,
			"ignore_cfg_admin_mode" => 1
		));

		if (!$cfid)
		{
			$clss = aw_ini_get("classes");

			// create system default cf for this class

			$cf = obj(null, array(), cfgform_obj::CLID);
			$cf->set_parent($o->parent());
			$cf->set_name(sprintf(t("Seadete vorm klassile %s"), $clss[$o->class_id()]["name"]));
			$cf->set_flag(OBJ_FLAG_IS_SELECTED, 1);
			$cf->set_subclass($o->class_id());
			$this->cff_init_from_class($cf, $o->class_id());
			$cf->save();
		}
		else
		{
			$cf = obj($cfid);
		}


		// toggle prop in cf
		if ($this->is_active_property($cf, $arr["prop"]))
		{
			$cf->remove_property($arr["prop"]);
			$cf->save();
			die(aw_ini_get("baseurl")."/automatweb/images/icons/cfg_edit_red.png");
		}
		else
		{
			$cf->restore_property($arr["prop"]);
			$cf->save();
			die(aw_ini_get("baseurl")."/automatweb/images/icons/cfg_edit_green.png");
		}
	}

	/**
		@attrib name=cfadm_click_group
		@param oid required
		@param group required
	**/
	public function cfadm_click_group($arr)
	{
		if (!$_SESSION["cfg_admin_mode"])
		{
			die("");
		}

		$o = obj($arr["oid"]);
		$this->clid = $o->class_id();
		$cfid = $this->get_cfgform_for_object(array(
			"args" => $arr,
			"obj_inst" => $o,
			"ignore_cfg_admin_mode" => 1
		));

		if (!$cfid)
		{
			$clss = aw_ini_get("classes");

			// create system default cf for this class

			$cf = obj(null, array(), cfgform_obj::CLID);
			$cf->set_parent($o->parent());
			$cf->set_name(sprintf(t("Seadete vorm klassile %s"), $clss[$o->class_id()]["name"]));
			$cf->set_flag(OBJ_FLAG_IS_SELECTED, 1);
			$cf->set_subclass($o->class_id());
			$this->cff_init_from_class($cf, $o->class_id());
			$cf->save();
		}
		else
		{
			$cf = obj($cfid);
		}


		// toggle group in cf
		if ($cf->group_is_hidden($arr["group"]))
		{
			$cf->show_group($arr["group"]);
			$cf->save();
			die(aw_ini_get("icons.server")."cfg_edit_green.png");
		}
		else
		{
			$cf->hide_group($arr["group"]);
			$cf->save();
			die(aw_ini_get("icons.server")."cfg_edit_red.png");
		}
	}
}
