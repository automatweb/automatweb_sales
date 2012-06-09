<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/shop/shop_product_search.aw,v 1.24 2010/02/09 11:07:22 dragut Exp $
// shop_product_search.aw - Lao toodete otsing 
/*

@classinfo syslog_type=ST_SHOP_PRODUCT_SEARCH relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo

@default group=general
@default table=objects

@default field=meta 
@default method=serialize

	@property wh type=relpicker reltype=RELTYPE_WAREHOUSE automatic=1 multiple=1 store=connect
	@caption Ladu

	@property oc type=relpicker reltype=RELTYPE_OC automatic=1 
	@caption Tellimiskeskkond

	@property search_category_root type=relpicker reltype=RELTYPE_SEARCH_CATEGORY_ROOT
	@caption Otsingu kategooriate juurikas

	@property objs_in_res type=select 
	@caption Tulemuseks on 

	@property forward_single_product_to_detailview type=checkbox
	@caption Kui leitakse ainult &uuml;ks toode, suuna detailvaatesse

	@property find_only_active type=checkbox
	@caption Leitakse ainult aktiivseid tooteid

@groupinfo folders caption="Otsingu l&auml;htekohad"
@default group=folders

	@property fld_tb type=toolbar store=no no_caption=1

	@property fld_tbl type=table store=no no_caption=1

@groupinfo s_form caption="Koosta otsinguvorm"
@default group=s_form
	@property s_form type=table no_caption=1

	@property search_btn_caption type=textbox 
	@caption Otsi nupu tekst

@groupinfo search_form caption="Otsinguvorm [dev]"
@default group=search_form

	@property search_form_elements_toolbar type=toolbar

	@property search_form_elements type=table
	@caption Otsinguvormi elemendid

@groupinfo s_res caption="Koosta tulemuste tabel"
@default group=s_res
	@property s_tbl type=table no_caption=1

@groupinfo s_res_ctr caption="Kontrollerid"
@default group=s_res_ctr

	@property s_tbl_ctr type=relpicker reltype=RELTYPE_CONTROLLER
	@caption Tulemuste andmete n&auml;itamise kontroller

	@property s_tbl_ctr2 type=relpicker reltype=RELTYPE_CONTROLLER
	@caption Tulemuste tabeli kontroller

	@property products_object_list_filter_controller type=relpicker reltype=RELTYPE_CFGCONTROLLER
	@caption Toodete object listi filtri kontroller
	@comment Kasutada kaht muutujat - $args (object listi filter) ja $no_products (boolean, kui tõene, siis tagastatakse tühi object list).

@groupinfo search caption="Otsi" submit_method=get submit=no
@default group=search

	@property search_form type=callback callback=callback_gen_search_form

	@property s_res type=text no_caption=1

@reltype WAREHOUSE value=1 clid=CL_SHOP_WAREHOUSE
@caption ladu

@reltype CONTROLLER value=2 clid=CL_FORM_CONTROLLER
@caption kontroller

@reltype OC value=3 clid=CL_SHOP_ORDER_CENTER
@caption tellimiskeskkond

@reltype TRANSFORM value=4 clid=CL_OTV_DATA_FILTER
@caption Muundaja

@reltype FOLDER value=5 clid=CL_MENU
@caption Otsingu kaust

@reltype SEARCH_CATEGORY_ROOT value=6 clid=CL_MENU
@caption Otsingu kaust

@reltype CFGCONTROLLER value=7 clid=CL_CFGCONTROLLER
@caption Kontroller (class_base liidesega)

*/

class shop_product_search extends class_base
{
	function shop_product_search()
	{
		$this->init(array(
			"tpldir" => "applications/shop/shop_product_search",
			"clid" => CL_SHOP_PRODUCT_SEARCH
		));
	}

	function get_property($arr)
	{
		$prop = $arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "s_form":
				$this->_s_form($arr);
				break;

			case "s_tbl":
				$this->_s_tbl($arr);
				break;

			case "s_res":
				$this->_s_res($arr);
				break;

			case "s_res_tb":
				$this->_s_res_tb($arr);
				break;

			case "fld_tb":
				$this->_fld_tb($arr);
				break;

			case "fld_tbl":
				$this->_fld_tbl($arr);
				break;

			case "objs_in_res":
				$prop["options"] = array(
					CL_SHOP_PACKET => "Paketid",
					CL_SHOP_PRODUCT => "Tooted",
					CL_SHOP_PRODUCT_PACKAGING => "Pakendid"
				);
				break;
		};
		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = $arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "s_form":
				$this->_save_s_form($arr);
				break;

			case "s_tbl":
				$this->_save_s_tbl($arr);
				break;
			case "fld_tbl":
				$this->_save_fld_tbl($arr);
				break;
		}
		return $retval;
	}	

	function callback_generate_scripts($arr)
	{
		$js = "";
		if(!empty($arr['request']['group']))
		{
			switch($arr["request"]["group"])
			{
				// mm, not using at the moment, lets try to make a quicker solution to compose the form ... --dragut
				case "search_form":
					$js = "
						function add_form_element(el)
						{
							$('#form_elements_table tbody>tr:last').clone(true).insertAfter('#form_elements_table tbody>tr:last');
						}
					";
			}
		}

		return $js;
	}

	function callback_mod_reforb($arr)
	{
		$arr["add_fld"] = 0;
		$arr["post_ru"] = post_ru();
	}

	function parse_alias($arr = array())
	{
		return $this->show(array("id" => $arr["alias"]["target"]));
	}

	/** 
		@attrib name=show nologin="1" default="1"

		@param id required type=int acl=view
	**/
	function show($arr)
	{
		aw_session_set("no_cache", 1);
		$o = obj($arr["id"]);

		$request = array(
		//	"MAX_FILE_SIZE" => ( !empty($_GET["do_search"]) ) ? $_GET["do_search"] : '', // why the heck is this needed here ?
			's' => ( !empty($_GET["s"]) && is_array($_GET["s"]) ) ? $_GET["s"] : array(),
		);

		$props =  $this->callback_gen_search_form(array(
			"obj_inst" => $o,
			"request" => $request
		));

		$htmlc = get_instance("cfg/htmlclient");
		$htmlc->start_output();
		foreach($props as $pn => $pd)
		{
			$htmlc->add_property($pd);
		}

		$htmlc->finish_output();

		$html = $htmlc->get_result(array(
			"raw_output" => 1
		));

		$prop = array();

		// It should be somehow configurable, should the search page use vcl/table or templates
		// I think it is rather reasonable to use htmlclient for form drawing ... or not? --dragut@19.08.2009
		if (true)
		{
			$table = $this->draw_search_results_with_templates(array('obj_inst' => $o, 'request' => $request));
		}
		else
		{
			$this->_s_res(array(
				"obj_inst" => $o,
				"request" => $request,
				"prop" => $prop
			));
			$table = $prop["value"];
		}

		$this->read_template("show.tpl");
		lc_site_load("shop", $this);
		$this->vars(array(
			"form" => $html,
			"section" => aw_global_get("section"),
			"table" => $table,
			"results" => $table, // need to refactor it
			"reforb" => $this->mk_reforb("submit_add_cart", array(
				"oc" => $o->prop("oc"),
				"MAX_FILE_SIZE" => 1000000,
			), "shop_order_cart")
		));

		if (!empty($_GET["die"]))
		{
			die($this->parse());
		}
		return $this->parse();
	}

	// this can be called from site.aw as well, to draw simple search form, but it should be more generic solution, which will be able to draw any kind of searchform according to a template
	// or according to the configuration
	function draw_search_form($arr)
	{
		if (!empty($arr['template']))
		{
			$this->read_template($arr['template']);
		}
		else
		{
			$this->read_template('form.tpl');
		}

		lc_site_load("shop", $this);

		// actually it is a pretty bold move to get the first object of this type and to expect, that this is the right one
		$ol = new object_list(array(
			'class_id' => CL_SHOP_PRODUCT_SEARCH,
			new obj_predicate_limit(1)
		));
		$search_obj = $ol->begin();

		// No search object found from the system
		if (empty($search_obj))
		{
			return $this->parse();
		}

		$root = $search_obj->get_first_obj_by_reltype('RELTYPE_SEARCH_CATEGORY_ROOT');
		$categories = new object_list(array(
			'class_id' => CL_MENU,
			'parent' => is_object($root) ? $search_obj->get_first_obj_by_reltype('RELTYPE_SEARCH_CATEGORY_ROOT')->id() : "",
			'sort_by' => 'objects.jrk'
		));
		$categories_str = '';
		foreach ($categories->arr() as $cat_id => $cat)
		{
			$this->vars(array(
				'search_category_name' => $cat->name(),
				'search_category_id' => $cat_id,
			));
			$categories_str .= $this->parse('SEARCH_CATEGORY'.(automatweb::$request->arg("search_category") == $cat_id ? "_SELECTED" : ""));
		}
		$this->vars(array(
			"search_term" => htmlentities(automatweb::$request->arg("search_term"), ENT_COMPAT, aw_global_get('charset')),
			'SEARCH_CATEGORY' => $categories_str,
			"SEARCH_CATEGORY_SELECTED" => "",
		));
		return $this->parse();
	}

	function _fld_tb($arr)
	{
		$tb = $arr["prop"]["vcl_inst"];
		$tb->add_search_button(array(
			"pn" => "add_fld",
			"clid" => CL_MENU,
			"multiple" => 1,
		));
		$tb->add_delete_rels_button();
		$tb->add_save_button();
	}

	function _fld_tbl($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "subs",
			"align" => "center",
			"caption" => t("Ka alamkaustad"),
		));
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid",
		));
		$subs = $arr["obj_inst"]->meta("subfolders");
		$conn = $arr["obj_inst"]->connections_from(array(
			"type" => "RELTYPE_FOLDER",
		));
		foreach($conn as $c)
		{
			$fld = $c->to();
			$t->define_data(array(
				"oid" => $fld->id(),
				"name" => $fld->name(),
				"subs" => html::checkbox(array(
					"name" => "subs[".$fld->id()."]",
					"value" => 1,
					"checked" => $subs[$fld->id()]?1:0,
				)),
			));
		}
	}

	function _save_fld_tbl($arr)
	{
		$add = $arr["request"]["add_fld"];
		if($add)
		{
			$tmp = explode(",", $add);
			foreach($tmp as $fld)
			{
				$arr["obj_inst"]->connect(array(
					"to" => $fld,
					"type" => "RELTYPE_FOLDER"
				));
			}
		}
		$arr["obj_inst"]->set_meta("subfolders", $arr["request"]["subs"]);
		$arr["obj_inst"]->save();
	}

	function _init_s_form_t($t)
	{
		$t->define_field(array(
			"name" => "class",
			"caption" => t("Klass"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "prop",
			"caption" => t("Omadus"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "in_form",
			"caption" => t("Vormis?"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "ord",
			"caption" => t("J&auml;rjekord"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "caption",
			"caption" => t("Tekst"),
			"align" => "center"
		));
		foreach($this->get_trans_languages() as $langid => $capt)
		{
			$t->define_field(array(
				"name" => "caption_".$langid,
				"caption" => t("Tekst")." - " . $capt,
				"align" => "center",
			));
		}
	}

	function _s_form($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_s_form_t($t);

		$props = $this->_get_prod_props($arr["obj_inst"]);
		$dat = $arr["obj_inst"]->meta("s_form");

		$clss = aw_ini_get("classes");
		foreach($props as $clid => $ps)
		{
			foreach($ps as $pn => $pd)
			{
				// make property name string
				$capts = array();
				foreach($pd as $real_pd)
				{
					if(isset($real_pd["caption"]))
					{
						$capts[] = $real_pd["caption"];
					}
				}
				
				$data = array(
					"class" => $clss[$clid]["name"],
					"prop" => join("/", array_unique($capts))." ($pn)",
					"in_form" => html::checkbox(array(
						"name" => "dat[$clid][$pn][in_form]",
						"value" => 1,
						"checked" => isset($dat[$clid][$pn]["in_form"])
					)),
					"caption" => html::textbox(array(
						"name" => "dat[$clid][$pn][caption]",
						"value" => isset($dat[$clid][$pn]) ? $dat[$clid][$pn]["caption"] : $capts[0]
					)),
					"ord" => html::textbox(array(
						"name" => "dat[$clid][$pn][ord]",
						"value" => $dat[$clid][$pn]["ord"],
						"size" => 5
					))
				);
				foreach($this->get_trans_languages() as $langid => $capt)
				{
					$data["caption_".$langid] = html::textbox(array(
						"name" => "dat[$clid][$pn][caption_".$langid."]",
						"value" => isset($dat[$clid][$pn]) ? $dat[$clid][$pn]["caption_".$langid] : $capts[0]
					));
				}
				$t->define_data($data);
			}
		}
		$t->set_sortable(false);
	}

	function _get_search_form_elements_toolbar($arr)
	{
		$t = $arr['prop']['vcl_inst'];
		$t->add_menu_button(array(
			'name' => 'elements_menu',
			'tooltip' => t('Elementide men&uuml;&uuml;'),
			'img' => 'new.gif'
		));
		$t->add_menu_item(array(
			'parent' => 'elements_menu',
			'text' => t('Tekstikast'),
			'title' => t('Tekstikast'),
			'onClick' => 'add_form_element(\'textbox\')',
			'url' => '#'
		));
		$t->add_menu_item(array(
			'parent' => 'elements_menu',
			'text' => t('Valik'),
			'title' => t('Valik'),
			'onClick' => 'add_form_element(\'select\')',
			'url' => '#'
		));
		return PROP_OK;
	}

	function _get_search_form_elements($arr)
	{
		$t = $arr['prop']['vcl_inst'];
		$t->set_dom_id('form_elements_table');

		$t->define_field(array(
			'name' => 'ord',
			'caption' => t('Jrk'),
			'size' => '5%'
		));
		$t->define_field(array(
			'name' => 'form_field',
			'caption' => t('Vormi v&auml;li'),
			'size' => '10%'
		));
		$t->define_field(array(
			'name' => 'classes',
			'caption' => t('Klassid')
		));
		$t->define_field(array(
			'name' => 'obj_fields',
			'caption' => t('Objektide v&auml;ljad')
		));

		// add new form element:
		$classes = array(
			CL_SHOP_PACKET,
			CL_SHOP_PRODUCT,
			CL_SHOP_PRODUCT_PACKAGING,
			CL_SHOP_COLOUR,
			CL_SHOP_PRODUCT_PURVEYANCE
		);

		$t->define_data(array(
			'ord' => html::textbox(array(
				'name' => 'form_element[new][ord]',
				'size' => 5
			)),
			'form_field' => html::select(array(
				'name' => 'form_element[new][form_field]',
				'options' => array(
					'textbox' => t('Tekstikast'),
					'select' => t('Valik')
				)
			)),
			'classes' => html::select(array(
				'name' => 'form_element[new][obj_fields_class]',
				'options' => get_class_picker(array('class_ids' => $classes))
			)),
			'obj_fields' => ''
		));
		return PROP_OK;
	}

	/** 
		@attrib name=add_element 

		@param id required type=int acl=view

		@param element required type=string
	**/
	function add_element($arr)
	{
		arr($arr);
		arr('lisaks elemendi siis');
	}

	function _set_search_form_elements($arr)
	{
		arr($arr);
	}

	function _get_prod_props($o)
	{
		// get warehouse from object
		$wh = $o->prop("wh");
		if (is_array($wh))
		{
			$wh = reset($wh);
		}
		if (!is_oid($wh) || !$this->can("view", $wh))
		{
			return array();
		}
		$wh = obj($wh);
		$wh_i = $wh->instance();

		$props = array(
			CL_SHOP_PACKET => array(),
			CL_SHOP_PRODUCT => array(),
			CL_SHOP_PRODUCT_PACKAGING => array()
		);

		$cf = get_instance(CL_CFGFORM);

		// get product props from warehouse
		$cfgforms = $wh_i->get_prod_add_config_forms(array("warehouse" => $wh->id()));
		foreach($cfgforms as $formid)
		{
			$ps = $cf->get_props_from_cfgform(array("id" => $formid));
			foreach($ps as $pn => $pd)
			{
				$props[CL_SHOP_PRODUCT][$pn][] = $pd;
			}
		}

		// get packaging props
		$cfgforms = $wh_i->get_prod_packaging_add_config_forms(array("warehouse" => $wh->id()));
		foreach($cfgforms as $formid)
		{
			$ps = $cf->get_props_from_cfgform(array("id" => $formid));
			foreach($ps as $pn => $pd)
			{
				$props[CL_SHOP_PRODUCT_PACKAGING][$pn][] = $pd;
			}
		}

		// get packet props
		// currently no cfgforms can be set for packets, so return default props from class
		$cu = get_instance("cfg/cfgutils");
		$ps = $cu->load_properties(array(
			"clid" => CL_SHOP_PACKET,
			"file" => "shop_packet"
		));
		foreach($ps as $pn => $pd)
		{
			$props[CL_SHOP_PACKET][$pn][] = $pd;
		}

		$cu = get_instance("cfg/cfgutils");
		$ps = $cu->load_properties(array(
			"clid" => CL_SHOP_PRODUCT,
			"file" => "shop_product"
		));
		foreach($ps as $pn => $pd)
		{
			$props[CL_SHOP_PRODUCT][$pn][] = $pd;
		}

		return $props;
	}

	function _save_s_form($arr)
	{
		$arr["obj_inst"]->set_meta("s_form", $arr["request"]["dat"]);
	}

	function _init_s_tbl_t($t)
	{
		$t->define_field(array(
			"name" => "class",
			"caption" => t("Klass"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "prop",
			"caption" => t("Omadus"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "in_form",
			"caption" => t("Tabelis?"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "ord",
			"caption" => t("J&auml;rjekord"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "caption",
			"caption" => t("Tulba pealkiri"),
			"align" => "center"
		));


		if(sizeof($this->get_trans_languages()))
		{
			foreach($this->get_trans_languages() as $langid => $capt)
			{
				$t->define_field(array(
					"name" => "caption_".$langid,
					"caption" => t("Tulba pealkiri")." - " . $capt,
					"align" => "center",
//					"parent" => "translations",
				));
			}
		}

		$t->define_field(array(
			"name" => "transform",
			"caption" => t("Muundaja"),
			"align" => "center"
		));
	}

	function get_trans_languages()
	{
		$res = array();
		$lan = get_instance("languages");
		foreach($lan->get_list() as $key => $val)
		{
			if($key != aw_global_get("lang_id"))
			{
				$res[$key] = $val;
			}
		}
		return $res;
	}

	function _s_tbl($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_s_tbl_t($t);

		$props = $this->_get_prod_props($arr["obj_inst"]);
		$dat = $arr["obj_inst"]->meta("s_tbl");

		$transforms = array("" => t("--vali--"));
		foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_TRANSFORM")) as $c)
		{
			$transforms[$c->prop("to")] = $c->prop("to.name");
		}
		$clss = aw_ini_get("classes");
		foreach($props as $clid => $ps)
		{
			foreach($ps as $pn => $pd)
			{
				// make property name string
				$capts = array();
				foreach($pd as $real_pd)
				{
					$capts[] = ( !empty($real_pd["caption"]) ) ? $real_pd["caption"] : '';
				}

				$data = array(
					"class" => $clss[$clid]["name"],
					"prop" => join("/", array_unique($capts))." ($pn)",
					"in_form" => html::checkbox(array(
						"name" => "dat[$clid][$pn][in_form]",
						"value" => 1,
						"checked" => (!empty($dat[$clid][$pn]["in_form"]) && $dat[$clid][$pn]["in_form"] == 1) ? true : false
					)),
					"caption" => html::textbox(array(
						"name" => "dat[$clid][$pn][caption]",
						"value" => isset($dat[$clid][$pn]) ? $dat[$clid][$pn]["caption"] : $capts[0]
					)),
					"ord" => html::textbox(array(
						"name" => "dat[$clid][$pn][ord]",
						"value" => ( !empty($dat[$clid][$pn]["ord"]) ) ? $dat[$clid][$pn]["ord"] : '',
						"size" => 5
					)),
					"transform" => html::select(array(
						"name" => "dat[$clid][$pn][transform]",
						"value" => ( !empty($dat[$clid][$pn]["transform"]) ) ? $dat[$clid][$pn]["transform"] : '',
						"options" => $transforms
					))
				);
				foreach($this->get_trans_languages() as $langid => $capt)
				{
					$data["caption_".$langid] = html::textbox(array(
						"name" => "dat[$clid][$pn][caption_".$langid."]",
						"value" => isset($dat[$clid][$pn]) ? $dat[$clid][$pn]["caption_".$langid] : $capts[0]
					));
				}
				$t->define_data($data);
			}
		}

		$clid = -1;
		$efs = array(
			"__result_state" => array("caption" => t("Asendus v&otilde;i ei")),
			"__retail_price" => array("caption" => t("Jaehind")),
			"__discount_pct" => array("caption" => t("Allahindluse %")),
			"__final_price" => array("caption" => t("L&otilde;pphind")),
			"__special_price" => array("caption" => t("Erihind")),
			"__amount" => array("caption" => t("Kogus")),
			"__warehouse_amounts" => array("caption" => t("Lao staatus")),
		);
			foreach($efs as $pn => $pd)
			{
				$data = array(
					"class" => ( !empty($clss[$clid]["name"]) ) ? $clss[$clid]["name"] : '',
					"prop" => $pd["caption"]." ($pn)",
					"in_form" => html::checkbox(array(
						"name" => "dat[$clid][$pn][in_form]",
						"value" => 1,
						"checked" => ( !empty($dat[$clid][$pn]["in_form"]) && $dat[$clid][$pn]["in_form"] == 1 ) ? true : false
					)),
					"caption" => html::textbox(array(
						"name" => "dat[$clid][$pn][caption]",
						"value" => isset($dat[$clid][$pn]) ? $dat[$clid][$pn]["caption"] : $pd["caption"]
					)),
					"ord" => html::textbox(array(
						"name" => "dat[$clid][$pn][ord]",
						"value" => $dat[$clid][$pn]["ord"],
						"size" => 5
					)),
					"transform" => html::select(array(
						"name" => "dat[$clid][$pn][transform]",
						"value" => $dat[$clid][$pn]["transform"],
						"options" => $transforms
					))
				);
				foreach($this->get_trans_languages() as $langid => $capt)
				{
					$data["caption_".$langid] = html::textbox(array(
						"name" => "dat[$clid][$pn][caption_".$langid."]",
						"value" => isset($dat[$clid][$pn]) ? $dat[$clid][$pn]["caption_".$langid] : $pd["caption"]
					));
				}
				$t->define_data($data);
			}

		$t->set_sortable(false);
	}

	function _save_s_tbl($arr)
	{
		$arr["obj_inst"]->set_meta("s_tbl", $arr["request"]["dat"]);
	}

	function callback_gen_search_form($arr)
	{
		$o = $arr["obj_inst"];

		$ret = array();	

		$cu = get_instance("cfg/cfgutils");

		$form_props = safe_array($o->meta("s_form"));
		foreach($form_props as $clid => $ps)
		{
			$r_props = $cu->load_properties(array("clid" => $clid));
			foreach($ps as $pn => $pd)
			{
				if (!empty($pd["in_form"]))
				{
					$nm = "s[".$clid."][".$pn."]";
					$ret[$nm] = array(
						"name" => $nm,
						"type" => $r_props[$pn]["type"] == "checkbox" ? "checkbox" : "textbox",
						"caption" => !empty($pd["caption_".aw_global_get("ct_lang_id")]) ? $pd["caption_".aw_global_get("ct_lang_id")] : $pd["caption"],
						"store" => "no",
						"value" => ( !empty($arr["request"]["s"][$clid][$pn]) ) ? $arr["request"]["s"][$clid][$pn] : '',
						"ch_value" => 1,
						"_ord" => $pd["ord"]
					);
				}
			}
		}
		uasort($ret, create_function('$a,$b', 'return $a["_ord"] - $b["_ord"];'));

		$ret["do_search"] = array(
			"name" => "do_search",
			"type" => "submit",
			"caption" => $arr["obj_inst"]->prop("search_btn_caption")
		);

		return $ret;
	}

	function _s_res($arr)
	{
		
		$t = new aw_table(array("layout" => "generic"));

		$cols = safe_array($arr["obj_inst"]->meta("s_tbl"));
		$flds = array();
		$transforms = array();
		$clids = array();
		foreach($cols as $clid => $cold)
		{
			foreach($cold as $coln => $coli)
			{
				if (!empty($coli["in_form"]))
				{
					$flds[] = array(
						"name" => $clid."_".$coln,
						"caption" => !empty($coli["caption_".aw_global_get("ct_lang_id")]) ? $coli["caption_".aw_global_get("ct_lang_id")] : $coli["caption"],
						"_ord" => $coli["ord"]
					);
					if ($this->can("view", $coli["transform"]))
					{
						$transforms[$clid."_".$coln] = obj($coli["transform"]);
					}
					$clids[$clid] = 1;
				}
			}
		}

		$warehouses = safe_array($arr["obj_inst"]->prop("wh"));

		uasort($flds, create_function('$a,$b', 'return $a["_ord"] - $b["_ord"];'));
		foreach($flds as $fld)
		{
			if ($fld["name"] == "-1___warehouse_amounts")
			{
				foreach($warehouses as $wh)
				{
					$t->define_field(array(
						"name" => $fld["name"]."_".$wh,
						"caption" => obj($wh)->name(),
						"align" => "center"
					));
				}
			}
			else
			{
				$t->define_field($fld);
			}
		}
		$t->define_field(array(
			"name" => "add_to_cart",
			"caption" => t("Vali")
		));

		$ctr = NULL;
		if (is_oid($ctr_id = $arr["obj_inst"]->prop("s_tbl_ctr")) && $this->can("view", $ctr_id))
		{
			$ctr = $ctr_id;
		}

		$ctr_i = get_instance(CL_FORM_CONTROLLER);
		$tr_i = get_instance(CL_OTV_DATA_FILTER);

		if (array_key_exists("MAX_FILE_SIZE", $arr["request"]))
		{
			$results = $this->get_search_results($arr["obj_inst"], $arr["request"]["s"]);
			foreach($results as $o)
			{
				$clid = $o->class_id();
				$data = array();

				$packet = $prod = $pk = NULL;

				switch($clid)
				{
					case CL_SHOP_PACKET:
						$packet = $o;
						$prod = $o->get_first_obj_by_reltype("RELTYPE_PRODUCT");
						if ($prod)
						{
							$pk = $prod->get_first_obj_by_reltype("RELTYPE_PACKAGING");
						}
						if (!$prod)
						{
							$prod = obj();
							$pk = obj();
						}
						else
						if (!$pk)
						{
							$pk = obj();
						}
						break;
					
					case CL_SHOP_PRODUCT:
						$prod = $o;
						if ($clids[CL_SHOP_PACKET])
						{
							$packet = reset($o->connections_to(array(
								"from.class_id" => CL_SHOP_PACKET
							)));
							if ($packet)
							{
								$packet = $packet->from();
							}
						}

						if ($clids[CL_SHOP_PRODUCT_PACKAGING])
						{
							$pk = $prod->get_first_obj_by_reltype("RELTYPE_PACKAGING");
						}
						if (!$packet)
						{
							$packet = obj();
						}
						if (!$pk)
						{
							$pk = obj();
						}
						break;

					case CL_SHOP_PRODUCT_PACKAGING:
						$pk = $o;
						$prod_c = reset($pk->connections_to(array(
							"from.class_id" => CL_SHOP_PRODUCT
						)));
						if ($prod_c)
						{
							$prod = $prod_c->from();
						}
						if (!$prod)
						{
							$prod = obj();
							$packet = obj();
						}
						else
						{
							$packet_c = reset($prod->connections_to(array(
								"from.class_id" => CL_SHOP_PACKET
							)));
							if ($packet_c)
							{
								$packet = $packet_c->from();
							}
							else
							{
								$packet = obj();
							}
						}
						break;
				}

				$prod_discount_pct = $prod->get_discount(2573380, array(
					"prod_category" => $this->_pcat2oid($prod->prop("user5")),
					"crm_category" => 2815413
				));

				foreach(safe_array($cols[CL_SHOP_PACKET]) as $coln => $cold)
				{
					if ($cold["in_form"] == 1)
					{
						$data[CL_SHOP_PACKET."_".$coln] = $packet->prop_str($coln);
					}
				}
				foreach(safe_array($cols[CL_SHOP_PRODUCT]) as $coln => $cold)
				{
					if ($cold["in_form"] == 1)
					{
						$data[CL_SHOP_PRODUCT."_".$coln] = $prod->prop_str($coln);
					}
				}
				foreach(safe_array($cols[CL_SHOP_PRODUCT_PACKAGING]) as $coln => $cold)
				{
					if ($cold["in_form"] == 1)
					{
						$data[CL_SHOP_PRODUCT_PACKAGING."_".$coln] = $pk->prop_str($coln);
					}
				}

				$rsc = str_ireplace(array(" ","-"), array("",""), $_GET["s"][CL_SHOP_PRODUCT]["code"]);
				foreach(safe_array($cols[-1]) as $coln => $cold)
				{
					if ($cold["in_form"] == 1)
					{
						switch($coln)
						{
							case "__result_state":
								if ($this->_code_matches($rsc, $prod->prop("code")) || $this->_code_matches($rsc, $prod->prop("short_code")))
								{
									$data["-1_".$coln] = t("K&uuml;situd");
								}
								else
								{
									$data["-1_".$coln] = t("Asendus");
								}
								break;

							case "__retail_price":
								$data["-1_".$coln] = number_format($prod->instance()->calc_price($prod), 2);
								break;

							case "__discount_pct":
								$data["-1_".$coln] = $prod_discount_pct." %";
								break;
				
							case "__final_price":
								if ($prod->prop("special_price") > 0)
								{
									$pr = $prod->prop("special_price");
								}
								else
								{
									$cpr = $prod->instance()->calc_price($prod);
									$pr = $cpr - (($prod_discount_pct * $cpr) / 100.0);
								}
								$data["-1_".$coln] = number_format($pr, 2);
								break;

							case "__special_price":
								$data["-1_".$coln] = number_format($prod->prop("special_price"), 2);
								break;

							case "__warehouse_amounts":
								foreach($warehouses as $wh)
								{
									$data["-1_".$coln."_".$wh] = $prod->get_amount($wh);
									if ($data["-1_".$coln."_".$wh] < 1)
									{
										$data["-1_".$coln."_".$wh] = $prod->get_availability_time($wh);
									}
								}
								break;

							case "__amount":
								$data["-1_".$coln] = html::textbox(array(
									"name" => "amount[".$prod->id()."]",
									"size" => 3,
									"value" => 1
								));
								break;
						}
						
					}
				}

				$data[CL_SHOP_PACKET."_oid"] = $packet->id();
				$data[CL_SHOP_PRODUCT."_oid"] = $prod->id();
				$data[CL_SHOP_PRODUCT_PACKAGING."_oid"] = $pk->id();
				$data["add_to_cart"] = html::checkbox(array(
					"name" => "add_to_cart[".$data[$arr["obj_inst"]->prop("objs_in_res")."_oid"]."]",
					"value" => 1
				));
				if ($ctr)
				{
					$ctr_i->eval_controller_ref($ctr, $cols, $data, $data);
				}
				foreach($transforms as $coln => $tr)
				{
					$tr_i->transform($tr, $data[$coln], $data);
				}
				$t->define_data($data);
			}
		}
		$ctr = NULL;
		if (is_oid($ctr_id = $arr["obj_inst"]->prop("s_tbl_ctr2")) && $this->can("view", $ctr_id))
		{
			$ctr_i->eval_controller_ref($ctr_id, $foo, $foo, $t);
		}
		$html = $t->draw();
		$arr["prop"]["value"] = $html;
	}

	function draw_search_results_with_templates($arr)
	{
		// get the order center object from shop_product_search
		$oc = $arr['obj_inst']->get_order_center();

		/*
		$this->read_template($ob->get_template());
		$this->vars(array(
			"name" => $ob->prop("name"),
		));
		*/

		// is it required?
		lc_site_load("shop", $this);

		$products = $arr["obj_inst"]->get_search_results();
		if ($products->count() === 0)
		{
			$this->read_template('no_results.tpl');
			$this->vars(array(
				'search_term' => htmlentities(automatweb::$request->arg('search_term'), ENT_COMPAT, aw_global_get('charset'))
			));
			return $this->parse();
		}
		else
		{
			$this->read_template('results.tpl');
		}
		if($products->count() === 1 && $arr["obj_inst"]->prop("forward_single_product_to_detailview"))
		{
			$product = $products->begin();
			header("Location: /".reset($product->get_pask())."?product=".$product->id()."&oc=".$oc->id()."&search_term=".automatweb::$request->arg("search_term")."&search_category=".automatweb::$request->arg("search_category"));
		}
		$this->vars(array(
			"count" => $products->count(),
		));

		$GLOBALS["order_center"] = $oc->id();
		
		$prod = "";//templeiti muutuja PRODUCT v22rtuseks
		$rows = "";
		
		$max = 4;//default
		$per_page = 16;//default products per page
		$page = empty($_GET["page"]) ? 0 : $_GET["page"];
		if($oc->prop("per_page"))
		{
			$per_page = $oc->prop("per_page");
		}

	// The idea here should be, that if the search will be by product code and therefore only one packet will be found, then 
	// I should redirect the user right to the product detail view
	// But I need to be sure somehow, that there is product code in the search field - how do i know that? are there only numbers maybe?
	//	if ($products->count() == 1)
	//	{
	//		$product = $products->begin();
	//		$url = "/".reset($product->get_pask())."?product=".$product->id()."&oc=".$oc->id();
	//		header("Location: ".$url);
	//		exit();
	//	}

		$count = $count_all = 0;
		foreach($products->ids() as $product_id)
		{
			$count_all++;
			if($count_all <= ($per_page * $page))
			{
				continue;
			}
			$product = obj($product_id);
			$count++;
			$data_params = array("image_url" => 1 , "min_price" => 1,"product_id" => 1, "brand_name" => 1, "special_prices" => 1);
			$product_data = $product->get_data($data_params);

			// this one should be coming from the get_data() fn. probably, but i don't know at the moment how to make that object data list query to work
			// so i just use this one here:
			$min_special_price = min($product_data['special_prices']);
			$product_data['PRODUCT_SPECIAL_PRICE'] = '';
			$product_data['special_price_visibility'] = '';
			if ($min_special_price > 0)
			{
				$product_data['special_price_visibility'] = '_specialPrice';
				$this->vars(array(
					'min_special_price' => $min_special_price
				));
				$product_data['PRODUCT_SPECIAL_PRICE'] = $this->parse('PRODUCT_SPECIAL_PRICE');
			}

			$product_data["product_link"] = "/".reset($product->get_pask())."?product=".$product->id()."&oc=".$oc->id();
			$ids = $product->get_categories()->ids();
			$category = reset($ids);

		//	$product_data["menu"] = $ob->get_category_menu($category);
			$product_data["menu_name"] = get_name($product_data["menu"]);
			$this->vars($product_data);

			if($count >= $max && $this->is_template("ROW"))//viimane tulp yksk6ik mis reas
			{
				$count = 0;
				if($this->is_template("PRODUCT_END"))
				{
					$prod.= $this->parse("PRODUCT_END");
				}
				else
				{
					$prod.= $this->parse("PRODUCT");
				}
				$this->vars(array("PRODUCT" => $prod));
				$rows.= $this->parse("ROW");
				$prod = "";
			}
			elseif($count_all >= $products->count() && $this->is_template("ROW"))//viimane rida
			{
				$prod.= $this->parse("PRODUCT");
				$this->vars(array("PRODUCT" => $prod));
				$rows.= $this->parse("ROW");
			}
			else
			{
				$prod.= $this->parse("PRODUCT");
			}

			if($count_all >= $per_page * ($page + 1))
			{
				break;
			}
		}
		$this->vars(array(
			"ROW" => $rows
		));

		$pages = $products->count() / $per_page;
		$pages = (int)$pages;
		if($products->count() % $per_page) $pages++;
		if($pages > 1)
		{
			if($page > 2)
			{
				$this->vars(array("pager_url" => aw_url_change_var("page", $page - 1)));
				$this->vars(array("PAGE_PREV" => $this->parse("PAGE_PREV")));
			}
			if($page < ($pages-3))
			{
				$this->vars(array("pager_url" => aw_url_change_var("page", $page + 1)));
				$this->vars(array("PAGE_NEXT" => $this->parse("PAGE_NEXT")));
			}

			$page_str = "";
			
			$x = max(array(0,$page - 2));
			$y = 0;
			if($x+$y > 1)
			{
				$page_str.= $this->parse("PAGE_SEP");
			}
			while($y < 5)
			{
				if($x+$y >= $pages)
				{
					break;
				}
				$this->vars(array("pager_url" => aw_url_change_var("page", ($x+$y))));
				$this->vars(array("pager_nr" => ($x + $y + 1)));

				
				if($x+$y == $page)
				{
					$page_str.= $this->parse("PAGE_SEL");
				}
				else
				{
					$page_str.= $this->parse("PAGE");
				}
				$y++;
			}

			if($x+$y + 1 < $pages)
			{
				$page_str.= $this->parse("PAGE_SEP");
			}

			$this->vars(array(
				"PAGE" => $page_str,
				"PAGE_SEL" => " ",
			));
			$this->vars(array("PAGER" => $this->parse("PAGER")));
		}

		$data = array();
		$cart_inst = get_instance(CL_SHOP_ORDER_CART);

		$data["section"] = aw_global_get("section");
		$this->vars($data);
		return $this->parse();
	}

	function get_search_results($o, $params)
	{
	/*
		// this here is mainly for taket at this point of time
		// Actually, i think I whould ask from warehouse, if search index is supported or not
		// but for now, i need to get this search thing working for otto quickly, so it will
		// for future development
		if (!empty($params[295]["code"]) || !empty($params[295]["name"]))
		{
			$r = new aw_request;
			$r->set_arg("prod_s_code", $params[295]["code"]);
			$r->set_arg("prod_s_name", $params[295]["name"]);
			$int = get_instance("applications/shop/shop_warehouse");

			$wh = $o->prop("wh");
			if (is_array($wh))
			{
				$wh = reset($wh);
			}
			$int->config = obj(obj($wh)->prop("conf"));
			$tmp = $int->get_products_list_from_index(array(), $r);
			return $tmp["ol"]->arr();
		}
	*/
		$wh_i = get_instance(CL_SHOP_WAREHOUSE);
		$conn = $o->connections_from(array(
			"type" => "RELTYPE_FOLDER",
		));
		$folders = array();
		if(count($conn))
		{
			$subs = $o->meta("subfolders");
			$folders = array();
			foreach($conn as $c)
			{
				$id = $c->prop("to");
				if($subs[$id])
				{
					$ot = new object_tree(array(
						"parent" => $id,
						"class_id" => CL_MENU,
					));
					$folders = array_merge($ot->ids(), $folders);
				}
				$folders[$id] = $id;
			}
		}
		else
		{
			/*
			$wh = $o->prop("wh");
			if (is_array($wh))
			{
				$wh = reset($wh);
			}
			list($main_fld, $subs) = $wh_i->get_packet_folder_list(array("id" => $wh));
			$folders = $subs->ids();
			$folders[] = $main_fld->id();
			*/
		}
		$res_type = $o->prop("objs_in_res");

		$filt = array(
			"parent" => $folders,
			"class_id" => $res_type,
		);

		foreach(safe_array($params) as $clid => $opts)
		{
			if ($clid == "_fulltext")
			{
				$this->_insert_ft_search($o, $params, $opts, $filt);
				continue;
			}
			
			foreach($opts as $pn => $pv)
			{
				if ($pv == "")
				{
					continue;
				}

				$v = "%".$pv."%";
				// now, based on the result object we must calc the way to search
				$this->_get_filt_param($clid, $res_type, $pn, $v, $filt);
				arr($filt);
			}
		}

		$ol = new object_list($filt);
		$r = $ol->arr();
		return $r;
	}

	function _get_filt_param($clid, $res_type, $pn, $v, &$filt)
	{
		if ($clid == CL_SHOP_PACKET)
		{
			switch($res_type)
			{	
				case CL_SHOP_PACKET:
					$filt[$pn] = $v;
					break;

				case CL_SHOP_PRODUCT:
					$filt["CL_SHOP_PACKET.RELTYPE_PRODUCT.$pn"] = $v;
					break;

				case CL_SHOP_PRODUCT_PACKAGING:
					$filt["CL_SHOP_PACKET.RELTYPE_PRODUCT.RELTYPE_PACKAGING.$pn"] = $v;
					break;
			}
		}
		else
		if ($clid == CL_SHOP_PRODUCT)
		{
			switch($res_type)
			{	
				case CL_SHOP_PACKET:
					break;

				case CL_SHOP_PRODUCT:
					$filt[$pn] = $v;
					break;

				case CL_SHOP_PRODUCT_PACKAGING:
					$filt["CL_SHOP_PRODUCT.RELTYPE_PACKAGING.$pn"] = $v;
					break;
			}
		}
		else
		if ($clid == CL_SHOP_PRODUCT_PACKAGING)
		{
			switch($res_type)
			{	
				case CL_SHOP_PACKET:
					break;

				case CL_SHOP_PRODUCT:
					break;

				case CL_SHOP_PRODUCT_PACKAGING:
					$filt[$pn] = $v;
					break;
			}
		}
	}

	function _insert_ft_search($o, $params, $str, &$filt)
	{
		$cu = get_instance("cfg/cfgutils");

		$ftf = array();

		$form_props = safe_array($o->meta("s_form"));
		foreach($form_props as $clid => $ps)
		{
			$r_props = $cu->load_properties(array("clid" => $clid));
			foreach($ps as $pn => $pd)
			{
				if ($pd["in_form"] == 1)
				{
					$this->_get_filt_param($clid, $o->prop("objs_in_res"), $pn, "%".$str."%", $ftf);
				}
			}
		}
		$filt[] = new object_list_filter(array(
			"logic" => "OR",
			"conditions" => $ftf
		));
	}

	function scs_get_search_results($arr)
	{
		// emulate fulltext search
		return array(1); //$this->get_search_results(obj($arr["group"]), array("_fulltext" => $arr["str"]));
	}

	function scs_display_search_results($arr)
	{
		$request = array(
			"MAX_FILE_SIZE" => 1,
			"s" => array("_fulltext" => $arr["str"])
		);

		$o = obj($arr["group"]);
		$props = $this->callback_gen_search_form(array(
			"obj_inst" => $o,
			"request" => $request
		));

		$htmlc = get_instance("cfg/htmlclient");
		$htmlc->start_output();
		foreach($props as $pn => $pd)
		{
			$htmlc->add_property($pd);
		}
		$htmlc->finish_output();

		$html = $htmlc->get_result(array(
			"raw_output" => 1
		));

		$prop = array("value" => "");
		$arr = array(
			"obj_inst" => $o,
			"request" => $request,
			"prop" => $prop
		);
		$this->_s_res($arr);
		$table =  $arr["prop"]["value"];

		$this->read_template("show_scs.tpl");
		$this->vars(array(
			"form" => $html,
			"section" => aw_global_get("section"),
			"table" => $table,
			"reforb" => $this->mk_reforb("submit_add_cart", array(
				"oc" => $o->prop("oc"),
				"MAX_FILE_SIZE" => 1000000,
			), "shop_order_cart"),
			"s_ro" => $this->mk_reforb("show", array(
				"id" => $o->id(),
				"no_reforb" => 1
			))
		));
		return $this->parse();
	}

	private function _code_matches($c1, $c2)
	{
		return stripos($c2, $c1) !== FALSE;
	}

	private function _pcat2oid($p)
	{
		$ol = new object_list(array(
			"class_id" => CL_SHOP_PRODUCT_CATEGORY,
			"name" => $p,
			"lang_id" => array(),
			"site_id" => array()
		));
		if ($ol->count())
		{
			return $ol->begin()->id();
		}
		return null;
	}
}
?>
