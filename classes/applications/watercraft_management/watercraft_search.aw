<?php

namespace automatweb;

/*

@classinfo syslog_type=ST_WATERCRAFT_SEARCH relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=dragut
@tableinfo watercraft_search index=oid master_table=objects master_index=oid

@default table=objects
@default group=general

	@property results_on_page type=textbox table=watercraft_search
	@caption Tulemuste arv lehel

	@property max_results type=textbox table=watercraft_search
	@caption Maksimaalne tulemuste arv

	@property search_result_template type=select table=watercraft_search
	@caption Otsingu tulemuste templeit

	@property search_form_template type=select table=watercraft_search
	@caption Otsingu vormi templeit

	@property no_search_form type=checkbox ch_value=1 table=watercraft_search
	@caption &Auml;ra kuva otsinguvormi

	@property result_order type=table
	@caption Otsingutulemuste sorteerimine

	@property saved_search type=checkbox ch_value=1 table=watercraft_search
	@caption Salvestatud otsing

	@property section_id type=textbox table=watercraft_search
	@caption M&auml;&auml;ratud sektsiooni id

@groupinfo parameters caption="Parameetrid"
@default group=parameters

	@property search_form_conf type=chooser orient=vertical multiple=1 field=meta method=serialize
	@caption Otsinguvormis kuvatavad v&auml;ljad

@groupinfo search caption="Otsing"
@default group=search

	@property watercraft_type type=select table=watercraft_search
	@caption Aluse t&uuml;&uuml;p

	@property condition type=select table=watercraft_search
	@caption Seisukord

	@property body_material type=select table=watercraft_search
	@caption Kerematerjal

	@property location type=select table=watercraft_search
	@caption Asukoht

	@property length type=range table=watercraft_search
	@caption Pikkus

	@property width type=range table=watercraft_search
	@caption Laius

	@property height type=range table=watercraft_search
	@caption K&otilde;rgus

	@property weight type=range table=watercraft_search
	@caption Raskus

	@property draught type=range table=watercraft_search
	@caption S&uuml;vis

	@property creation_year type=range table=watercraft_search
	@caption Valmistamisaasta

	@property passanger_count type=range table=watercraft_search
	@caption Reisijaid

	@property additional_equipment type=textbox size=20 table=watercraft_search
	@caption Lisavarustus

	@property seller type=select table=watercraft_search
	@caption M&uuml;&uuml;ja

	@property contact_name type=textbox table=watercraft_search
	@caption Kontaktisik

	@property price type=range table=watercraft_search
	@caption Hind

	@property ad_id type=textbox size=15 table=watercraft_search
	@caption Kuulutuse id

	@property deal_type type=chooser multiple=1 table=watercraft_search
	@caption Tehingu t&uuml;&uuml;p

	@property watercraft_search_submit type=submit no_caption=1
	@caption Otsi

	@property search_result_table type=table store=no
	@caption Tulemused

*/

define('SELLER_TYPE_PERSON', 145);
define('SELLER_TYPE_COMPANY', 129);

class watercraft_search extends class_base
{
	const AW_CLID = 1119;


	var $search_form_elements;
	var $additional_equipment_elements;
	var $watercraft_inst;
	var $seller_type;

	function watercraft_search()
	{
		$this->init(array(
			"tpldir" => "applications/watercraft_management/watercraft_search",
			"clid" => CL_WATERCRAFT_SEARCH
		));

		$this->search_form_elements = array(
			'watercraft_type' => t('Aluse t&uuml;&uuml;p'),
			'condition' => t('Seisukord'),
			'body_material' => t('Kerematerjal'),
			'location' => t('Asukoht'),
			'length' => t('Pikkus'),
			'width' => t('Laius'),
			'height' => t('K&otilde;rgus'),
			'weight' => t('Raskus'),
			'draught' => t('S&uuml;vis'),
			'creation_year' => t('Valmistamisaasta'),
			'passanger_count' => t('Reisijaid'),
			'additional_equipment' => t('Lisavarustus'),
			'seller' => t('M&uuml;&uuml;ja'),
			'contact_name' => t('Kontaktisik'),
			'price' => t('Hind'),
			'ad_id' => t('Kuulutuse ID'),
			'deal_type' => t('Tehingu t&uuml;&uuml;p'),
		);

		$this->additional_equipment_elements = array(
			'electricity_110V' => t('Elekter 110V'),
			'electricity_220V' => t('Elekter 220V'),
			'radio_station' => t('Raadiojaam'),
			'stereo' => t('Stereo'),
			'cd' => t('CD'),
			'waterproof_speakers' => t('Veekindlad k&otilde;larid'),
			'burglar_alarm' => t('Signalisatsioon'),
			'navigation_system' => t('Navigatsioonis&uuml;steem'),
			'navigation_lights' => t('Navigatsioonituled'),
			'trailer' => t('Treiler'),
			'toilet' => t('Tualett'),
			'shower' => t('Dush'),
			'lifejacket' => t('P&auml;&auml;stevest'),
			'swimming_ladder' => t('Ujumisredel'),
			'awning' => t('Varikatus'),
			'kitchen_cooker' => t('K&ouml;&ouml;k/Pliit'),
			'vendrid' => t('Vendrid'),
			'fridge' => t('K&uuml;lmkapp'),
			'anchor' => t('Ankur'),
			'oars' => t('Aerud'),
			'tv_video' => t('TV-video'),
			'fuel' => t('K&uuml;te'),
			'water_tank' => t('Veepaak'),
			'life_boat' => t('P&auml;&auml;stepaat'),
		);

		$this->seller_type = array(
			SELLER_TYPE_PERSON => t('Eraisik'),
			SELLER_TYPE_COMPANY => t('Firma')
		);

		$this->sortable_props = array(
			"jrk" => t("J&auml;rjekorranumber"),
			"price" => t("Hind"),
			"creation_year" => t("Valmistamisaasta"),
			"created" => t("Lisatud"),
		);

		$this->watercraft_inst = get_instance(CL_WATERCRAFT);
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case 'result_order':
				$t = &$prop["vcl_inst"];
				$t->define_field(array(
					"name" => "sort_type",
					"caption" => t("Sorteeritav omadus"),
				));
				$t->define_field(array(
					"name" => "sort_direction",
					"caption" => t("Sorteerimise suund"),
				));


				$t->define_data(array(
					"sort_type" => html::select(array(
						"name" => "sortable_fields[new][type]",
						"options" => array(0 => t("-- Valige omadus --")) + $this->sortable_props,
					)),
					"sort_direction" => html::select(array(
						"name" => "sortable_fields[new][dir]",
						"options" => array(
							"0" => t("-- Valige suund --"),
							"asc" => t("V&auml;iksemad (vanemad) ennem"),
							"desc" => t("Suuremad (uuemad) ennem"),
						),
					)),
				));
				$fafa = array_reverse($arr["obj_inst"]->meta("result_order"));
				foreach($fafa as $key => $row)
				{
					$t->define_data(array(
						"sort_type" => html::select(array(
							"name" => "sortable_fields[".$key."][type]",
							"options" => $this->sortable_props,
							"selected" => $row["type"],
						)),
						"sort_direction" => html::select(array(
							"name" => "sortable_fields[".$key."][dir]",
							"options" => array(
								"asc" => t("V&auml;iksemad (vanemad) ennem"),
								"desc" => t("Suuremad (uuemad) ennem"),
							),
							"selected" => $row["dir"],
						)),
					));
				}
				break;
			case 'results_on_page':
				if ( $arr['new'] == 1 )
				{
					$prop['value'] = 50;
				}
				break;
			case 'max_results':
				if ( $arr['new'] == 1 )
				{
					$prop['value'] = 500;
				}
				break;
			case 'search_result_template':
				$t = get_instance("templatemgr");
				$prop["options"] = array("" => t("--vali--")) + $t->template_picker(array("folder" => "applications/watercraft_management/watercraft_search"));
				if ( $arr['new'] == 1 )
				{
					$prop['selected'] = 'show.tpl';
				}
				break;
			case 'search_form_template':
				$t = get_instance("templatemgr");
				$prop["options"] = array("" => t("--vali--")) + $t->template_picker(array("folder" => "applications/watercraft_management/watercraft_search"));
				if ( $arr['new'] == 1 )
				{
					$prop['selected'] = 'search_form.tpl';
				}
				break;
			case 'search_form_conf':
				$prop['options'] = $this->search_form_elements;
				break;

			case 'deal_type':
				if(is_array($arr["request"]["deal_type"]))
				{
					$prop["value"] = $arr["request"]["deal_type"];
				}
				else
				{
					$prop["value"] = aw_unserialize($prop["value"]);
				}
				$prop['options'] = array(t("K&otilde;ik")) + $this->watercraft_inst->deal_type;
				break;
			case 'watercraft_type':
				$prop['options'] = array(t('K&otilde;ik')) + $this->watercraft_inst->watercraft_type;
				$prop['selected'] = $arr['request']['watercraft_type'];
				break;
			case 'condition':
				$prop['options'] = array(t('K&otilde;ik')) + $this->watercraft_inst->condition;
				$prop['selected'] = $arr['request']['condition'];
				break;
			case 'body_material':
				$prop['options'] = array(t('K&otilde;ik')) + $this->watercraft_inst->body_material;
				$prop['selected'] = $arr['request']['body_material'];
				break;
			case 'location':
				$prop['options'][0] = t('K&otilde;ik');

				$watercraft_management = get_active(CL_WATERCRAFT_MANAGEMENT);

				if (!empty($watercraft_management))
				{
					$locations = new object_list(array(
						'class_id' => CL_CRM_ADDRESS,
						'parent' => $watercraft_management->prop('locations')
					));
					foreach ( $locations->arr() as $id => $location )
					{
						$prop['options'][$id] = $location->name();
					}
				}
				$prop['selected'] = $arr['request']['location'];

				break;
			case 'length':
			case 'width':
			case 'height':
			case 'weight':
			case 'draught':
			case 'creation_year':
			case 'passanger_count':
			case 'price':
				$range = &$prop['vcl_inst'];
				$range->set_range($arr['request'][$prop['name']]);

				break;
			case 'additional_equipment':
			case 'contact_name':
			case 'ad_id':
				$prop['value'] = $arr['request'][$prop["name"]];
				break;
			case 'seller':
				$prop['options'] = array(t('K&otilde;ik')) + $this->seller_type;
				$prop['selected'] = $arr['request']['seller_type'];
				break;
		};

		if ( array_key_exists( $prop['name'], $this->search_form_elements ) )
		{
			$search_form_conf = $arr['obj_inst']->prop('search_form_conf');
			if ( !in_array( $prop['name'],  $search_form_conf) )
			{
				$retval = PROP_IGNORE;
			}
		}

		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			//-- set_property --//
			case "deal_type":
				$prop["value"] = aw_serialize($prop["value"], SERIALIZE_NATIVE);
				break;
			case "result_order":
				$fields = $arr["request"]["sortable_fields"];
				$new = $fields["new"];
				unset($fields["new"]);
				$cur = $arr["obj_inst"]->meta("result_order");
				if($new["type"] && $new["dir"])
				{
					$fields[] = $new;
				}
				$arr["obj_inst"]->set_meta("result_order", $fields);
				$arr["obj_inst"]->save();
				break;
		}
		return $retval;
	}

	function _get_search_result_table($arr)
	{
		$t = &$arr['prop']['vcl_inst'];
		$t->set_sortable(false);

		$t->set_caption(t('Otsingu tulemused'));

		$t->define_chooser(array(
			'name' => 'selected_ids',
			'field' => 'select'
		));
		$t->define_field(array(
			"name" => "id",
			"caption" => t("Kuulutuse ID"),
		));
		$t->define_field(array(
			'name' => 'name',
			'caption' => t('Nimi'),
		));
		$t->define_field(array(
			'name' => 'type',
			'caption' => t('T&uuml;&uuml;p')
		));
		$t->define_field(array(
			'name' => 'manufacturer',
			'caption' => t('Tootja')
		));
		$t->define_field(array(
			'name' => 'brand',
			'caption' => t('Mark')
		));
		$t->define_field(array(
			'name' => 'location',
			'caption' => t('Asukoht')
		));
		$t->define_field(array(
			'name' => 'seller',
			'caption' => t('M&uuml;&uuml;ja')
		));
		$t->define_field(array(
			'name' => 'price',
			'caption' => t('Hind')
		));
		$t->define_field(array(
			'name' => 'visible',
			'caption' => t('N&auml;htav'),
			'align' => 'center',
			'width' => '5%'
		));
		$t->define_field(array(
			'name' => 'archive',
			'caption' => t('Arhiivis'),
			'align' => 'center',
			'width' => '5%'
		));

		$property_data = $arr['obj_inst']->get_property_list();
		$properties = $arr['obj_inst']->properties();

		$search_params = array();
		foreach ($property_data as $property)
		{
			if ($property['group'] == 'search' && !empty($properties[$property['name']]))
			{
				if($property["name"] == "deal_type")
				{
					$properties[$property["name"]] = aw_unserialize($properties[$property["name"]]);
				}
				$search_params[$property['name']] = $properties[$property['name']];
			}
		}

		$items  = $this->search(array(
			'obj_inst' => $arr['obj_inst'],
			'request' => $search_params
		));

		foreach ($items->arr() as $id => $item)
		{
			$name_str = html::href(array(
				'url' => $this->mk_my_orb('change', array(
					'id' => $id,
					'return_url' => get_ru()
				), CL_WATERCRAFT),
				'caption' => strlen($name =htmlentities($item->name()))?$name:t("- nimetu -"),
			));

			$manufacturer_str = '';
			$manufacturer_oid = $item->prop('manufacturer');
			if ($this->can('view', $manufacturer_oid))
			{
				$manufacturer = new object($manufacturer_oid);
				$manufacturer_str = html::href(array(
					'url' => $this->mk_my_orb('change', array(
						'id' => $manufacturer_oid,
						'return_url' => get_ru()
					), CL_CRM_COMPANY),
					'caption' => htmlentities($manufacturer->name())
				));
			}
			$location_str = '';
			$location = $item->prop('location');
			if ($this->can('view', $location))
			{
				$location = new object($location);
				$location_str = $location->name();
			}
			else
			{
				$location_str = $item->prop('location_other');
			}

			$seller_str = "";
			$seller = $item->prop('seller');
			if ($this->can('view', $seller))
			{
				$seller = new object($seller);
				$seller_str = html::href(array(
					'caption' => htmlentities($seller->name()),
					'url' => $this->mk_my_orb('change', array('id' => $seller->id()), $seller->class_id())
				));
			}
			$t->define_data(array(
				'select' => $id,
				"ad_id" => $id,
				'name' => $name_str,
				'type' => $this->item_inst->item_type[$item->prop('item_type')],
				'manufacturer' => $manufacturer_str,
				'brand' => htmlentities($item->prop('brand')),
				'location' => htmlentities($location_str),
				'seller' => $seller_str,
				'price' => htmlentities($item->prop('price')),
				'visible' => ($item->prop('visible') == 1) ? t('Jah') : t('Ei'),
				'archive' => ($item->prop('archived') == 1) ? t('Jah') : t('Ei'),
			));
		}

		return PROP_OK;
	}


	function callback_mod_reforb(&$arr)
	{
		$arr["post_ru"] = post_ru();
	}

	////////////////////////////////////
	// the next functions are optional - delete them if not needed
	////////////////////////////////////

	/**
		@attrib name=show nologin="1" all_args="1"
	**/
	function show($arr)
	{
		$obj = new object($arr["id"]);

		$active_page = (int)$_GET['page'];
		$results_on_page = (int)$obj->prop('results_on_page');
		$max_results = (int)$obj->prop('max_results');
		$watercraft_id = (int)$_GET['watercraft_id'];

		$watercraft_inst = get_instance(CL_WATERCRAFT);
		if ($this->can('view', $watercraft_id))
		{
			return $watercraft_inst->show(array(
				'id' => $watercraft_id,
			));
		}

		$show_tpl = $obj->prop('search_result_template');
		if (empty($show_tpl))
		{
			$this->read_template("show.tpl");
		}
		else
		{
			$this->read_template($show_tpl);
		}

		$do_search = false;
		if ($_GET['do_search'] == 1)
		{
			$do_search = true;
		}

		if ($obj->prop('no_search_form') == 1)
		{
			$search_form_str = '';
		}
		else
		{
			$this->vars(array(
				 'search_form' => $this->draw_search_form(array(
					'ob' => $obj
				)),
			));
			$search_form_str = $this->parse('SEARCH_FORM_BOX');
		}

		$search_params = array();
		if ($obj->prop('saved_search') == 1)
		{
			$property_data = $obj->get_property_list();
			$properties = $obj->properties();
			foreach ($property_data as $property)
			{
				if ($property['group'] == 'search')
				{
					// deal_type value is saved as serialized data
					if ($property['name'] == 'deal_type')
					{
						$search_params[$property['name']] = aw_unserialize($properties[$property['name']]);
					}
					else
					{
						$search_params[$property['name']] = $properties[$property['name']];
					}
				}
			}
			$do_search = true;
		}
		else
		{
			$search_params = $_GET;
		}

		if ($do_search === true || $arr["prepared_list"])
		{
			if($arr["prepared_list"])
			{
				$items = $arr["prepared_list"];
			}
			else
			{
				$items_ol = $this->search(array(
					'obj_inst' => $obj,
					'request' => $search_params,
					'limit' => $max_results,
					'only_visible' => true,
				));
				$items_count = $items_ol->count();

				$this->quote(&$_GET['sortby']);
				$this->quote(&$_GET['order']);
				$items = $this->search(array(
					'obj_inst' => $obj,
					'request' => $search_params,
					'limit' => ($active_page * $results_on_page).', '.$results_on_page,
					'sort_by' => ($_GET["sortby"] && $_GET["order"])?"watercraft.".$_GET['sortby']." ".$_GET['order']:"",
					'only_visible' => true,
				));
			}

			$items_str = '';
			$images = new object_list(array(
				'class_id' => CL_IMAGE,
				'parent' => $items->ids()
			));
			$images_lut = array();
			foreach ($images->arr() as $image)
			{
				$images_lut[$image->parent()][] = $image->id();
			}
			foreach ($items->arr() as $item_id => $item)
			{
				$properties = array();
				$proplist = $item->properties();
				foreach ($proplist as $name => $value)
				{
					if (!empty($watercraft_inst->$name))
					{
						$var = $watercraft_inst->$name;
						//$properties['watercraft_'.$name] = htmlentities($var[$value]);
						$properties['watercraft_'.$name] = $var[$value];
					}
					else
					{
						//$properties['watercraft_'.$name] = htmlentities($value);
						$properties['watercraft_'.$name] = $value;
					}
				}

				$properties["watercraft_location"] = array();
				if($this->can("view", $proplist["location"]))
				{
					$o = obj($proplist["location"]);
					$properties["watercraft_location"][] = $o->name();
				}
				if(strlen($proplist["location_other"]))
				{
					$properties["watercraft_location"][] = $proplist["location_other"];
				}
				$properties["watercraft_location"] = ($_t = join(", ", $properties["watercraft_location"]))?$_t:"-";

				$images_count = count($images_lut[$item_id]);

				$this->vars(array(
					'watercraft_view_url' => aw_url_change_var(array(
						'section' => $obj->prop('section_id') ? $obj->prop('section_id') : aw_global_get('section'),
						'watercraft_id' => $item_id,
						'return_url' => (!empty($_GET['return_url'])) ? $_GET['return_url'] : get_ru()
					)),
					'watercraft_images_count' => $images_count,
					) + $properties
				);

				$image_inst = get_instance(CL_IMAGE);
				$image_str = '';
				if ($images_count > 0)
				{
					$image_id = reset($images_lut[$item_id]);
					$image_data = $image_inst->get_image_by_id($image_id);
					$image_url = $image_inst->get_url_by_id($image_id);
					$this->vars(array(
						'watercraft_image_name' => $image_data['name'],
						'watercraft_image_url' => $image_url,
						'watercraft_image_tag' => $image_inst->make_img_tag_wl($image_id)
					));
					$image_str .= $this->parse('WATERCRAFT_IMAGE');

				}
				else
				{
					$image_str .= $this->parse('WATERCRAFT_NO_IMAGE');
				}

				$this->vars(array(
					'WATERCRAFT_IMAGE' => $image_str
				));

				$items_str .= $this->parse('SEARCH_RESULT_ITEM');
			}

			$watercraft_property_list = array("price", "creation_year");
			foreach ($watercraft_property_list as $watercraft_property)
			{
				$prop = str_replace('watercraft_', '', $watercraft_property);
				if ($prop == $_GET['sortby'])
				{
					$order = ($_GET['order'] == 'asc') ? 'desc' : 'asc';
				}
				else
				{
					$order = 'asc';
				}
				$sorting_links[$watercraft_property.'_sort_url'] = aw_url_change_var(array(
					'sortby' => $prop,
					'order' => $order
				));
			}
			$this->vars(array(
				'SEARCH_RESULT_ITEM' => $items_str
			));
			$this->vars($sorting_links);

			$search_results = $this->parse('SEARCH_RESULTS');

			$pages_str = $this->draw_page_selector(array(
				'obj_inst' => $obj,
				'items_count' => $items_count,
				'active_page' => $active_page,
				'results_on_page' => $results_on_page
			));
		}

		$this->vars(array(
			'SEARCH_FORM_BOX' => $search_form_str,
			'SEARCH_RESULTS' => $search_results,
			'PAGES' => $pages_str,
			'name' => $obj->prop('name'),
		));

		return $this->parse();
	}

	function draw_page_selector($arr)
	{
		$page_count = ((int)$arr['items_count'] / (int)$arr['results_on_page']);
		for ($i = 0; $i < $page_count; $i++)
		{
			$this->vars(array(
				'page_url' => aw_url_change_var('page', $i),
				'page_num' => ($i + 1)
			));
			if ($arr['active_page'] == $i)
			{
				$pages_str .= $this->parse('SEL_PAGE');
			}
			else
			{
				$pages_str .= $this->parse('PAGE');
			}
		}

		$prev_page_link = '';
		if (!empty($arr['active_page']))
		{
			$this->vars(array(
				'prev_page_url' => aw_url_change_var( 'page', ($arr['active_page'] - 1) ),
			));
			$prev_page_link = $this->parse('PREV_PAGE');
		}

		$next_page_link = '';
		if (($arr['active_page'] + 1) < $page_count )
		{
			$this->vars(array(
				'next_page_url' => aw_url_change_var( 'page', ($arr['active_page'] + 1) ),
			));
			$next_page_link = $this->parse('NEXT_PAGE');
		}

		$this->vars(array(
			'PAGE' => $pages_str,
			'PREV_PAGE' => $prev_page_link,
			'NEXT_PAGE' => $next_page_link
		));
		return $this->parse('PAGES');
	}


	function draw_search_form($arr)
	{
		$ob = $arr['ob'];
		classload('cfg/htmlclient');

		$form_tpl = $ob->prop('search_form_template');
		if (empty($form_tpl))
		{
			$form_tpl = "search_form.tpl";
		}

		$htmlclient = new htmlclient(array(
			'tpldir' => 'applications/watercraft_management/watercraft_search',
			'template' => $form_tpl
		));
		$htmlclient->start_output();
		$watercraft_search_inst = $ob->instance();
		$range_vcl_inst = get_instance('vcl/range');

		foreach ($ob->get_property_list() as $property)
		{

			if ($property['group'] == 'search' && $property['type'] != 'table')
			{

				if ($property['type'] == 'range')
				{


					$property = $range_vcl_inst->init_vcl_property(array(
						'property' => $property,
						'obj_inst' => $ob
					));
					$property = reset($property);

					if ($ob->prop('saved_search') != 1)
					{
						// as the init_vcl_property sets the property value too for range,
						// then i need to reset it when the search is not saved search to
						// prevent the saved search values appear in the form -dragut
						$property['vcl_inst']->set_range(array(
							'from' => '',
							'to' => ''
						));
					}
				//	$property['vcl_inst'] = $range_vcl_inst;
				}

				$retval = $watercraft_search_inst->get_property(array(
					'prop' => &$property,
					'obj_inst' => &$ob,
					'request' => ($ob->prop('saved_search') == 1) ? $ob->properties() : $_GET
				));

				if ($retval === PROP_OK)
				{
					$htmlclient->add_property($property);
				}
			}
		}

		$htmlclient->finish_output(array(
			"data" => array(
				"class" => "",
				"section" => aw_global_get("section"),
				"action" => "show",
				"id" => $ob->id(),
				"alias" => "event_search",
				"return_url" => get_ru(),
				"do_search" => 1,
			),
			"method" => "get",
			"form_handler" => aw_ini_get("baseurl")."/".aw_global_get("section")."#search",
			"submit" => "no"
		));

		return $htmlclient->get_result();
	}

	function search($arr)
	{
		if (empty($arr['obj_inst']))
		{
			$arr['obj_inst'] = new object($arr['id']);
		}


		$filter = array(
			'class_id' => CL_WATERCRAFT,
			'parent' => $arr['obj_inst']->prop('data'),
		);
		if($arr["only_visible"])
		{
			$filter["visible"] = "1";
		}

		if (!empty($arr['limit']))
		{
			$filter['limit'] = $arr['limit'];
		}

		// this here is .. a temperory line. really, i do have a plan to make it better one day!!
		if (!empty($arr['sort_by']))
		{
			$this->quote(&$arr['sort_by']);
			$filter['sort_by'] = $arr['sort_by'];
		}
		else
		{
			$ro = $arr["obj_inst"]->meta("result_order");
			if(is_array($ro) && count($ro))
			{
				$objects_table = array(
					"jrk", "created"
				);
				foreach($ro as $k)
				{
					$table = (in_array($k["type"], $objects_table))?"objects":"watercraft";
					$order[] = $table.".".$k["type"]." ".$k["dir"];
				}
				if(count($order))
				{
					$filter["sort_by"] = join(",", $order);
				}
			}
		}
		if($filter["sort_by"])
		{
			$filter["price"] = new obj_predicate_not("struudel");
		}


		$man = get_active(CL_WATERCRAFT_MANAGEMENT);
		$d = (int)($man->prop("max_days"));
		$t = false;
		// calculates the days backwards and sets the filter to 'modified' prop from beginning of that day
		if($d)
		{
			$d_in_s = 60 * 60 * 24;
			$back = time() - ($d_in_s * $d);
			$filter["modified"] = new obj_predicate_compare(OBJ_COMP_GREATER, mktime(0,0,0, date("n", $back), date("j", $back), date("Y", $back)));
		}


		foreach ($this->search_form_elements as $name => $caption)
		{
			// if it is range or chooser:
			if ( is_array($arr['request'][$name]) )
			{
				$from = (float)$arr['request'][$name]['from'];
				$to = (float)$arr['request'][$name]['to'];

				// if both are empty, then we have an empty range or a chooser:
				if ( empty($from) && empty($to) )
				{
					// chooser:
					if($name == "deal_type")
					{
						if($arr["request"][$name][0])
						{
							continue;
						}
						$wc = get_instance(CL_WATERCRAFT);
						foreach($wc->deal_type as $const => $capt)
						{
							if($arr["request"][$name][$const])
							{
								$deal_t[] = $const;
							}
						}
						if(count($deal_t))
						{
							$filter[$name] = $deal_t;
						}
					}

					continue;
				}
				else
				if ( empty($from) )
				{
					// we have only $to value:
					$filter[$name] = new obj_predicate_compare(OBJ_COMP_LESS_OR_EQ, $to);
				}
				else
				if ( empty($to) )
				{
					// we have only $from value
					$filter[$name] = new obj_predicate_compare(OBJ_COMP_GREATER_OR_EQ, $from);
				}
				else
				{
					// and finally we have them both:
					$filter[$name] = new obj_predicate_compare(OBJ_COMP_BETWEEN_INCLUDING, $from, $to);
				}
			}
			else
			{
				if ( !empty($arr['request'][$name]) )
				{

					switch ($name)
					{
						case 'seller':
							$filter['CL_WATERCRAFT.RELTYPE_SELLER.class_id'] = $arr['request'][$name];
							break;
						case 'additional_equipment':

							$value = trim($arr['request'][$name]);
							$conditions = array();
							// we have this additional equipment search field, which content have to searched from
							// all additional equipment fields
							foreach ($this->additional_equipment_elements as $element_name => $element_caption)
							{
								// if the search string is present in the elements caption
								// this should cover that when the additional equipment element
								// is only selected, then it will be found, and maybe there are more
								// than one word:
								$words = array();
								foreach (explode(' ', $value) as $word)
								{
									if (stristr($element_caption, htmlentities($word)) !== false)
									{
										$conditions[$element_name.'_sel'] = 1;
									}
									else
									{
										$conditions[] = new object_list_filter(array(
											'logic' => 'AND',
											'conditions' => array(
												$element_name.'_info' => '%'.$word.'%',
												$element_name.'_sel' => 1
											),
										));
									}
								}
							}

							break;
						case 'ad_id':
							$filter["oid"] = (int)($arr["request"][$name]);
							break;
						default:
							$filter[$name] = $arr['request'][$name];
					}

				}
			}
		}
		$filter[] = new object_list_filter(array(
			'logic' => 'OR',
			'conditions' => $conditions
		));
		$watercrafts = new object_list($filter);
		return $watercrafts;
	}

	function do_db_upgrade($table, $field, $query, $error)
	{
		if (empty($field))
		{
			$this->db_query('CREATE TABLE '.$table.' (
				oid INT PRIMARY KEY NOT NULL,

				results_on_page int,
				max_results int,
				no_search_form int,
				save_search int,
				watercraft_type int,
				`condition` int,
				body_material int,
				location int,
				length_from int,
				length_to int,
				width_from int,
				width_to int,
				height_from int,
				height_to int,
				weight_from int,
				weight_to int,
				draught_from int,
				draught_to int,
				creation_year_from int,
				creation_year_to int,
				passanger_count_from int,
				passanger_count_to int,
				seller int,
				price_from int,
				price_to int,
				section_id int,
				ad_id int,
				deal_type text,

				additional_equipment varchar(255)

			)');
			return true;
		}

		switch ($field)
		{
			case 'results_on_page':
			case 'max_results':
			case 'no_search_form':
			case 'saved_search':
			case 'watercraft_type':
			case 'condition':
			case 'body_material':
			case 'location':
			case 'length':
			case 'length_from':
			case 'length_to':
			case 'width':
			case 'width_from':
			case 'width_to':
			case 'height':
			case 'height_from':
			case 'height_to':
			case 'weight':
			case 'weight_from':
			case 'weight_to':
			case 'draught':
			case 'draught_from':
			case 'draught_to':
			case 'creation_year':
			case 'creation_year_from':
			case 'creation_year_to':
			case 'passanger_count':
			case 'passanger_count_from':
			case 'passanger_count_to':
			case 'seller':
			case 'price':
			case 'ad_id':
			case 'price_from':
			case 'price_to':
			case 'section_id':
				$this->db_add_col($table, array(
					'name' => $field,
					'type' => 'int'
				));
                                return true;
			case 'additional_equipment':
			case 'search_result_template':
			case 'search_form_template':
			case 'contact_name':
				$this->db_add_col($table, array(
					'name' => $field,
					'type' => 'varchar(255)'
				));
                                return true;
			case 'deal_type':
				$this->db_add_col($table, array(
					'name' => $field,
					'type' => 'text'
				));
                                return true;
                }

		return false;
	}

}
?>
