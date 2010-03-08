<?php
// watercraft_management.aw - Vees6idukite haldus
/*

@classinfo syslog_type=ST_WATERCRAFT_MANAGEMENT relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=dragut
@tableinfo watercraft_management index=oid master_table=objects master_index=oid

@default table=objects
@default group=general

	@groupinfo sub_general caption="&Uuml;ldine" parent=general
	@default group=sub_general

		@property name type=textbox
		@caption Nimi

		@property keeper type=relpicker reltype=RELTYPE_KEEPER table=watercraft_management
		@caption Haldaja

		@property data type=relpicker reltype=RELTYPE_DATA table=watercraft_management
		@caption Vees&otilde;idukite andmed

		@property search type=relpicker reltype=RELTYPE_SEARCH table=watercraft_management
		@caption Otsing

		@property manufacturers type=relpicker reltype=RELTYPE_MANUFACTURERS table=watercraft_management
		@caption Tootjad

		@property locations type=relpicker reltype=RELTYPE_LOCATIONS table=watercraft_management
		@caption Asukohad

		@property max_days type=textbox table=watercraft_management
		@caption Kuvatavate kuulutuste maksimaalne vanus p&auml;evades

	@groupinfo manufacturers_management caption="Tootjate haldamine" parent=general
	@default group=manufacturers_management

		@property manufacturers_management_toolbar type=toolbar no_caption=1
		@caption Tootjate t&ouml;&ouml;riistariba

		@property manufacturers_management_table type=table no_caption=1
		@caption Tootjad

	@groupinfo locations_management caption="Asukohtade haldamine" parent=general
	@default group=locations_management

		@property locations_management_toolbar type=toolbar no_caption=1
		@caption Asukohtade t&ouml;&ouml;riistariba

		@property locations_management_table type=table no_caption=1
		@caption Asukohad

@groupinfo watercrafts caption="Vees&otilde;idukid"

	@groupinfo all caption="K&otilde;ik" parent=watercrafts
	@groupinfo motor_boat caption="Mootorpaadid" parent=watercrafts
	@groupinfo sailing_ship caption="Purjekad" parent=watercrafts
	@groupinfo dinghy caption="Kummipaat" parent=watercrafts
	@groupinfo rowing_boat caption="S&otilde;udepaadid" parent=watercrafts
	@groupinfo scooter caption="Skuutrid" parent=watercrafts
	@groupinfo sailboard caption="Purjelauad" parent=watercrafts
	@groupinfo canoe caption="Kanuud" parent=watercrafts
	@groupinfo fishing_boat caption="Kalapaadid" parent=watercrafts
	@groupinfo other caption="Muud" parent=watercrafts
	@groupinfo accessories caption="Varustus/tarvikud" parent=watercrafts

@groupinfo search caption="Otsing"
@default group=search

	@property watercrafts_toolbar type=toolbar no_caption=1 group=all,motor_boat,sailing_ship,dinghy,rowing_boat,scooter,sailboard,canoe,fishing_boat,other,accessories,search
	@caption Vees&otilde;idukite t&ouml;&ouml;riistariba

	@layout watercraft_search_frame type=hbox width=20%:80%

		@layout watercraft_search_frame_left type=vbox parent=watercraft_search_frame

			@property watercraft_type type=select store=no captionside=top parent=watercraft_search_frame_left
			@caption Aluse t&uuml;&uuml;p

			@property condition type=select store=no captionside=top parent=watercraft_search_frame_left
			@caption Seisukord

			@property body_material type=select store=no captionside=top parent=watercraft_search_frame_left
			@caption Kerematerjal

			@property location type=select store=no captionside=top parent=watercraft_search_frame_left
			@caption Asukoht

			@property length type=range store=no captionside=top parent=watercraft_search_frame_left
			@caption Pikkus

			@property width type=range store=no captionside=top parent=watercraft_search_frame_left
			@caption Laius

			@property height type=range store=no captionside=top parent=watercraft_search_frame_left
			@caption K&otilde;rgus

			@property weight type=range store=no captionside=top parent=watercraft_search_frame_left
			@caption Raskus

			@property draught type=range store=no captionside=top parent=watercraft_search_frame_left
			@caption S&uuml;vis

			@property creation_year type=range store=no captionside=top parent=watercraft_search_frame_left
			@caption Valmistamisaasta

			@property passanger_count type=range store=no captionside=top parent=watercraft_search_frame_left
			@caption Reisijaid

			@property additional_equipment type=textbox size=20 store=no captionside=top parent=watercraft_search_frame_left
			@caption Lisavarustus

			@property seller type=select store=no captionside=top parent=watercraft_search_frame_left
			@caption M&uuml;&uuml;ja

			@property price type=range store=no captionside=top parent=watercraft_search_frame_left
			@caption Hind

			@property watercraft_search_submit type=submit store=no no_caption=1 parent=watercraft_search_frame_left
			@caption Otsi

		@layout watercraft_search_frame_right type=vbox parent=watercraft_search_frame

	@property watercrafts_table type=table no_caption=1 group=all,motor_boat,sailing_ship,dinghy,rowing_boat,scooter,sailboard,canoe,fishing_boat,other,accessories,search parent=watercraft_search_frame_right
	@caption Vees&otilde;idukite tabel


@groupinfo activity caption=Aktiivsus

        @property activity type=table group=activity no_caption=1
        @caption Aktiivsus


@reltype KEEPER value=1 clid=CL_CRM_COMPANY
@caption Haldaja

@reltype DATA value=2 clid=CL_MENU
@caption Vees&otilde;idukite andmed

@reltype LOCATIONS value=3 clid=CL_MENU
@caption Asukohad

@reltype MANUFACTURERS value=4 clid=CL_MENU
@caption Tootjad

@reltype SEARCH value=5 clid=CL_WATERCRAFT_SEARCH
@caption Otsing

*/

define('SELLER_TYPE_PERSON', 145);
define('SELLER_TYPE_COMPANY', 129);

class watercraft_management extends class_base
{
	var $watercraft_inst;
	var $watercraft_search_inst;
	var $search_obj;
	var $seller_type;

	function watercraft_management()
	{
		$this->init(array(
			"tpldir" => "applications/watercraft_management/watercraft_management",
			"clid" => CL_WATERCRAFT_MANAGEMENT
		));

		$this->watercraft_inst = get_instance(CL_WATERCRAFT);
		$this->watercraft_search_inst = get_instance(CL_WATERCRAFT_SEARCH);
		$this->seller_type = array(
			SELLER_TYPE_PERSON => t('Eraisik'),
			SELLER_TYPE_COMPANY => t('Firma')
		);

	}

	function get_property($arr)
	{
		if ( empty($this->search_obj) )
		{
			$search_oid = $arr['obj_inst']->prop('search');
			if ( $this->can('view', $search_oid) )
			{
				$this->search_obj = new object($search_oid);
			}
		}
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
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
				$locations = new object_list(array(
					'class_id' => CL_CRM_ADDRESS,
					'parent' => $arr['obj_inst']->prop('locations')
				));
				foreach ( $locations->arr() as $id => $location )
				{
					$prop['options'][$id] = $location->name();
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
				$prop['value'] = $arr['request']['additional_equipment'];
				break;
			case 'seller':
				$prop['options'] = array(t('K&otilde;ik')) + $this->seller_type;
				$prop['selected'] = $arr['request']['seller_type'];
				break;

		};

		if ( !empty($this->search_obj) )
		{
			if ( array_key_exists( $prop['name'], $this->watercraft_search_inst->search_form_elements ) )
			{
				$search_form_conf = $this->search_obj->prop('search_form_conf');
				if ( !in_array( $prop['name'],  $search_form_conf) )
				{
					$retval = PROP_IGNORE;
				}
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
		}
		return $retval;
	}


        function _get_activity($arr)
        {
                // this is supposed to return a list of all active polls
                // to let the user choose the active one
                $table = &$arr["prop"]["vcl_inst"];
                $table->parse_xml_def("activity_list");

                $pl = new object_list(array(
                        "class_id" => $this->clid
                ));
                for($o = $pl->begin(); !$pl->end(); $o = $pl->next())
                {
                        $actcheck = checked($o->flag(OBJ_FLAG_IS_SELECTED));
                        $act_html = "<input type='radio' name='active' $actcheck value='".$o->id()."'>";
                        $row = $o->arr();
                        $row["active"] = $act_html;
                        $table->define_data($row);
                };
        }

        function _set_activity($arr)
        {
                $ol = new object_list(array(
                        "class_id" => $this->clid,
                ));
                for ($o = $ol->begin(); !$ol->end(); $o = $ol->next())
                {
                        if ($o->flag(OBJ_FLAG_IS_SELECTED) && $o->id() != $arr["request"]["active"])
                        {
                                $o->set_flag(OBJ_FLAG_IS_SELECTED, false);
                                $o->save();
                        }
                        else
                        if ($o->id() == $arr["request"]["active"] && !$o->flag(OBJ_FLAG_IS_SELECTED))
                        {
                                $o->set_flag(OBJ_FLAG_IS_SELECTED, true);
                                $o->save();
                        }
                }
        }



	function _get_watercrafts_toolbar($arr)
	{
		$t = &$arr['prop']['vcl_inst'];

		$t->add_button(array(
			'name' => 'new',
			'img' => 'new.gif',
			'tooltip' => t('Uus vees&otilde;iduk'),
			'url' => $this->mk_my_orb('new', array(
				'parent' => $arr['obj_inst']->prop('data'),
				'return_url' => get_ru()
			), CL_WATERCRAFT),
		));

		$t->add_button(array(
			'name' => 'save',
			'img' => 'save.gif',
			'tooltip' => t('Salvesta'),
			'action' => '_save_objects',
		));

		$t->add_button(array(
			'name' => 'delete',
			'img' => 'delete.gif',
			'tooltip' => t('Kustuta'),
			'action' => '_delete_objects',
			'confirm' => t('Oled kindel et soovid valitud objektid kustutada?')
		));

		$search_oid = $arr['obj_inst']->prop('search');
		if ( !empty($search_oid) )
		{
			$t->add_button(array(
				'name' => 'settings',
				'img' => 'settings.gif',
				'tooltip' => t('Otsingu seaded'),
				'url' => $this->mk_my_orb('change', array(
					'id' => $search_oid,
					'group' => 'parameters',
					'return_url' => get_ru()
				), CL_WATERCRAFT_SEARCH)
			));
		}
		else
		{
			$t->add_button(array(
				'name' => 'settings',
				'img' => 'settings.gif',
				'tooltip' => t('Otsingu seaded'),
				'disabled' => true
			));
		}

		return PROP_OK;
	}

	function _get_watercrafts_table($arr)
	{
		$t = &$arr['prop']['vcl_inst'];
		$t->set_sortable(false);

		$t->define_chooser(array(
			'name' => 'selected_ids',
			'field' => 'select'
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
			'name' => 'created',
			'type' => 'time',
			'format' => 'h:i d.m.Y',
			'caption' => t('Lisamise aeg')
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


		if ( $arr['request']['group'] == 'search' )
		{
			$watercrafts = $this->watercraft_search_inst->search($arr);
		}
		else
		{
			$filter = array(
				'class_id' => CL_WATERCRAFT,
				'parent' => $arr['obj_inst']->prop('data'),
				'watercraft_type' => constant('WATERCRAFT_TYPE_'.strtoupper($arr['request']['group']))
			);
			$watercrafts = new object_list($filter);
		}


		foreach ($watercrafts->arr() as $id => $watercraft)
		{
			$name_str = html::href(array(
				'url' => $this->mk_my_orb('change', array(
					'id' => $id,
					'return_url' => get_ru()
				), CL_WATERCRAFT),
				'caption' => strlen($watercraft->name())?$watercraft->name():t("- nimetu -"),
			));

			$manufacturer_str = '';
			$manufacturer_oid = $watercraft->prop('manufacturer');
			if ($this->can('view', $manufacturer_oid))
			{
				$manufacturer = new object($manufacturer_oid);
				$manufacturer_str = html::href(array(
					'url' => $this->mk_my_orb('change', array(
						'id' => $manufacturer_oid,
						'return_url' => get_ru()
					), CL_CRM_COMPANY),
					'caption' => $manufacturer->name()
				));
			}
			$location_str = '';
			$location = $watercraft->prop('location');
			if ($this->can('view', $location))
			{
				$location = new object($location);
				$location_str = $location->name();
			}
			else
			{
				$location_str = $watercraft->prop('location_other');
			}

			$seller_str = "";
			$seller = $watercraft->prop('seller');
			if ($this->can('view', $seller))
			{
				$seller = new object($seller);
				$seller_str = html::href(array(
					'caption' => $seller->name(),
					'url' => $this->mk_my_orb('change', array('id' => $seller->id()), $seller->class_id())
				));
			}
			$t->define_data(array(
				'select' => $id,
				'name' => $name_str,
				'type' => $this->watercraft_inst->watercraft_type[$watercraft->prop('watercraft_type')],
				'manufacturer' => $manufacturer_str,
				'brand' => $watercraft->prop('brand'),
				'location' => $location_str,
				'seller' => $seller_str,
				'price' => $watercraft->prop('price'),
				'created' => $watercraft->prop('created'),
				'visible' => ($watercraft->prop('visible') == 1) ? t('Jah') : t('Ei'),
				'archive' => ($watercraft->prop('archived') == 1) ? t('Jah') : t('Ei'),
			));
		}

		return PROP_OK;
	}

	function _get_manufacturers_management_toolbar($arr)
	{
		$t = &$arr['prop']['vcl_inst'];

		$t->add_button(array(
			'name' => 'new',
			'img' => 'new.gif',
			'tooltip' => t('Uus Tootja'),
			'url' => $this->mk_my_orb('new', array(
				'parent' => $arr['obj_inst']->prop('manufacturers'),
				'return_url' => get_ru()
			), CL_CRM_COMPANY),
		));

		$t->add_button(array(
			'name' => 'delete',
			'img' => 'delete.gif',
			'tooltip' => t('Kustuta'),
			'action' => '_delete_objects',
			'confirm' => t('Oled kindel et soovid valitud objektid kustutada?')
		));

		return PROP_OK;
	}

	function _get_manufacturers_management_table($arr)
	{
		$t = &$arr['prop']['vcl_inst'];
		$t->set_sortable(false);

		$t->define_field(array(
			'name' => 'name',
			'caption' => t('Nimi'),
		));
		$t->define_field(array(
			'name' => 'select',
			'caption' => t('Vali'),
			'width' => '5%',
			'align' => 'center'
		));

		$manufacturers = new object_list(array(
			'class_id' => CL_CRM_COMPANY,
			'parent' => $arr['obj_inst']->prop('manufacturers'),
		));

		foreach ($manufacturers->arr() as $id => $manufacturer)
		{
			$t->define_data(array(
				'name' => html::href(array(
					'url' => $this->mk_my_orb('change', array(
						'id' => $id,
						'return_url' => get_ru()
					), CL_CRM_COMPANY),
					'caption' => $manufacturer->name()
				)),
				'select' => html::checkbox(array(
					'name' => 'selected_ids['.$id.']',
					'value' => $id
				))
			));
		}

		return PROP_OK;
	}

	function _get_locations_management_toolbar($arr)
	{
		$t = &$arr['prop']['vcl_inst'];

		$t->add_button(array(
			'name' => 'new',
			'img' => 'new.gif',
			'tooltip' => t('Uus asukoht'),
			'url' => $this->mk_my_orb('new', array(
				'parent' => $arr['obj_inst']->prop('locations'),
				'return_url' => get_ru()
			), CL_CRM_ADDRESS),
		));

		$t->add_button(array(
			'name' => 'delete',
			'img' => 'delete.gif',
			'tooltip' => t('Kustuta'),
			'action' => '_delete_objects',
			'confirm' => t('Oled kindel et soovid valitud objektid kustutada?')
		));

		return PROP_OK;
	}

	function _get_locations_management_table($arr)
	{
		$t = &$arr['prop']['vcl_inst'];
		$t->set_sortable(false);

		$t->define_field(array(
			'name' => 'name',
			'caption' => t('Nimi'),
		));
		$t->define_field(array(
			'name' => 'select',
			'caption' => t('Vali'),
			'width' => '5%',
			'align' => 'center'
		));

		$locations = new object_list(array(
			'class_id' => CL_CRM_ADDRESS,
			'parent' => $arr['obj_inst']->prop('locations'),
		));

		foreach ($locations->arr() as $id => $location)
		{
			$t->define_data(array(
				'name' => html::href(array(
					'url' => $this->mk_my_orb('change', array(
						'id' => $id,
						'return_url' => get_ru()
					), CL_CRM_ADDRESS),
					'caption' => $location->name()
				)),
				'select' => html::checkbox(array(
					'name' => 'selected_ids['.$id.']',
					'value' => $id
				))
			));
		}

		return PROP_OK;
	}

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function callback_mod_tab($arr)
	{
		if ($arr['id'] == 'manufacturers_management')
		{
			$manufacturers = $arr['obj_inst']->prop('manufacturers');
			if ( empty($manufacturers) )
			{
				return false;
			}
		}

		if ($arr['id'] == 'locations_management')
		{
			$locations = $arr['obj_inst']->prop('locations');
			if ( empty($locations) )
			{
				return false;
			}
		}
	}

	function callback_mod_retval($arr)
	{
	//	arr($arr);
		$arr['args']['watercraft_type'] = (int)$arr['request']['watercraft_type'];
		$arr['args']['condition'] = (int)$arr['request']['condition'];
		$arr['args']['body_material'] = (int)$arr['request']['body_material'];
		$arr['args']['location'] = (int)$arr['request']['location'];
		$arr['args']['length'] = $arr['request']['length'];
		$arr['args']['width'] = $arr['request']['width'];
		$arr['args']['height'] = $arr['request']['height'];
		$arr['args']['weight'] = $arr['request']['weight'];
		$arr['args']['draught'] = $arr['request']['draught'];
		$arr['args']['creation_year'] = $arr['request']['creation_year'];
		$arr['args']['passanger_count'] = $arr['request']['passanger_count'];
		$arr['args']['price'] = $arr['request']['price'];
		$arr['args']['seller'] = $arr['request']['seller'];
		$arr['args']['additional_equipment'] = $arr['request']['additional_equipment'];
	}

	function callback_post_save($arr)
	{
		if ( $arr['new'] == 1 )
		{
			$data = new object();
			$data->set_parent($arr['obj_inst']->id());
			$data->set_class_id(CL_MENU);
			$data->set_name(t('Vees&otilde;idukid'));
			$data_oid = $data->save();
			$arr['obj_inst']->connect(array(
				'to' => $data_oid,
				'type' => 'RELTYPE_DATA'
			));
			$arr['obj_inst']->set_prop('data', $data_oid);

			$manufacturers = new object();
			$manufacturers->set_parent($arr['obj_inst']->id());
			$manufacturers->set_class_id(CL_MENU);
			$manufacturers->set_name(t('Tootjad'));
			$manufacturers_oid = $manufacturers->save();
			$arr['obj_inst']->connect(array(
				'to' => $manufacturers_oid,
				'type' => 'RELTYPE_MANUFACTURERS'
			));
			$arr['obj_inst']->set_prop('manufacturers', $manufacturers_oid);

			$locations = new object();
			$locations->set_parent($arr['obj_inst']->id());
			$locations->set_class_id(CL_MENU);
			$locations->set_name(t('Asukohad'));
			$locations_oid = $locations->save();
			$arr['obj_inst']->connect(array(
				'to' => $locations_oid,
				'type' => 'RELTYPE_LOCATIONS'
			));
			$arr['obj_inst']->set_prop('locations', $locations_oid);

			$arr['obj_inst']->save();
		}
	}

	function get_expired_ads()
	{
		$wman = get_active(CL_WATERCRAFT_MANAGEMENT);
		$days = (int)($wman->prop("expire_notification_time"));
		if($days)
		{
			$sex_in_day = 60 * 60 * 24;
			$to = time() - ($days * $sex_in_day);
			$list = new object_list(array(
				"class_id" => CL_WATERCRAFT,
				"parent" => $wman->prop("data"),
				"visible" => 1,
				"status" => STAT_ACTIVE,
				"modified" => new obj_predicate_compare(OBJ_COMP_LESS, mktime(0,0,0, date("n", $to), date("j", $to), date("Y", $to))),
			));
		}
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

	/**
		@attrib name=_delete_objects
	**/
	function _delete_objects($arr)
	{

		foreach ($arr['selected_ids'] as $id)
		{
			if (is_oid($id) && $this->can("delete", $id))
			{
				$object = new object($id);
				$object->delete();
			}
		}
		return $arr['post_ru'];
	}

	function do_db_upgrade($table, $field, $query, $error)
	{
		if (empty($field))
		{
			$this->db_query('CREATE TABLE '.$table.' (
				oid INT PRIMARY KEY NOT NULL,
				keeper int,
				data int,
				locations int,
				manufacturers int,
				search int,
				max_days int
			)');
			return true;
		}

		switch ($field)
		{
			case 'keeper':
			case 'data':
			case 'locations':
			case 'manufacturers':
			case 'search':
			case 'max_days':
				$this->db_add_col($table, array(
					'name' => $field,
					'type' => 'int'
				));
				return true;
		}
		return false;
	}
}
?>
