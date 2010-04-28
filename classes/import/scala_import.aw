<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/import/scala_import.aw,v 1.22 2008/01/31 13:54:39 kristo Exp $
// scala_import.aw - Scala import 
/*

@classinfo syslog_type=ST_SCALA_IMPORT relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=hannes
@tableinfo scala_import index=oid master_table=objects master_index=oid

@default table=objects
@default group=general

	@property warehouse type=relpicker reltype=RELTYPE_WAREHOUSE table=scala_import
	@caption Ladu
	@comment Ladu kuhu alla imporditud tooted pannakse

	@property user_group type=relpicker reltype=RELTYPE_USER_GROUP table=scala_import
	@caption Kasutajagrupp
	@comment Kasutajagrupp kuhu hakkavad imporditud kasutajad kuuluma

	@property config_form type=relpicker reltype=RELTYPE_CONFIG_FORM table=scala_import
	@caption Seadete vorm
	@comment Seadete vorm toote sisestamiseks

	@property import_sections type=chooser multiple=1 field=meta method=serialize
	@caption Mida importida

	@property do_import type=text store=no
	@caption Teosta import

	@property import_time type=text store=no
	@caption Viimase impordi l&otilde;ppemise aeg:

@groupinfo ftp_config caption="FTP seaded"
@default group=ftp_config

	@property ftp_host type=textbox table=scala_import
	@caption FTP aadress
	@comment FTP serveri aadress

	@property ftp_user type=textbox table=scala_import
	@caption FTP kasutaja
	@comment Kasutajanimi, millega FTP serverisse logitakse

	@property ftp_password type=password table=scala_import
	@caption FTP parool
	@comment Parool FTP kasutajale

	@property ftp_file_location_pricing type=textbox table=scala_import size=70
	@caption Pricing.xml

	@property ftp_file_location_customer type=textbox table=scala_import size=70
	@caption Customers.xml

	@property ftp_file_location_availability type=textbox table=scala_import size=70
	@caption Availability.xml 

@groupinfo import_config caption="Impordi seaded"
@default group=import_config

	// This is not going to be at the moment, so commenting out
	@groupinfo prices caption="Hinnad" parent=import_config
	@default group=prices

		@property prices_config_table type=table 
		@caption Hindade seadete tabel

	@groupinfo users caption="Kasutajad" parent=import_config
	@default group=users
		
		@property users_config_table type=table
		@caption Kasutajate seadete tabel

	@groupinfo categories caption="Kategooriad" parent=import_config
	@default group=categories
		
		@property categories_config_table type=table
		@caption Kategooriate seadete tabel

	@groupinfo availability caption="Laoseis" parent=import_config
	@default group=availability

		@property availability_config_table type=table
		@caption Laoseisu seadete tabel

@reltype WAREHOUSE value=1 clid=CL_SHOP_WAREHOUSE
@caption Ladu

@reltype USER_GROUP value=2 clid=CL_GROUP
@caption Ladu

@reltype CONFIG_FORM value=3 clid=CL_CFGFORM
@caption Seadete vorm

@reltype RECURRENCE value=4 clid=CL_RECURRENCE
@caption Kordused

*/

class scala_import extends class_base
{
	const AW_CLID = 1137;

//	var $db_table_name = 'scala_prices_to_customers';
	var $import_sections;
	var $log_str = '';
	var $log_file_name = '';

	function scala_import()
	{
		$this->init(array(
			"tpldir" => "import/scala_import",
			"clid" => CL_SCALA_IMPORT
		));

		$this->import_sections = array(
			'users' => t('Kasutajad'),
			'categories' => t('Kategooriad'),
			'availability' => t('Lao seis'),
			'pricing' => t('Hinnad'),
		);
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case 'import_sections':
				$prop['options'] = $this->import_sections;
				break;
			case 'do_import':
				$saved_import_sections = $arr['obj_inst']->prop('import_sections');
				$url_params = array();
				foreach ($saved_import_sections as $key => $value)
				{
					$sections[$key] = $this->import_sections[$key];
					$url_params[$key] = 1;
					
				}
				$import_url = $this->mk_my_orb('do_import', array(
					'id' => $arr['obj_inst']->id()
				) + $url_params, CL_SCALA_IMPORT);

				$prop['value'] = html::href(array(
					'caption' => sprintf(t('Impordi %s'), implode(', ', $sections)),
					'url' => str_replace('automatweb/', '', $import_url),
				));
				break;
			case 'import_time':
				$prop['value'] = date('d.m.Y H:i:s', $arr['obj_inst']->meta('import_end_time'));
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

	function _get_prices_config_table($arr)
	{
		$t = &$arr['prop']['vcl_inst'];
		$t->set_sortable(false);

		$t->define_field(array(
			'name' => 'property',
			'caption' => t('Property'),
			'width' => '30%'
		));
		$t->define_field(array(
			'name' => 'xml_tag',
			'caption' => t('XML tagide nimed')
		));
/*
		$o = new object();
		$o->set_class_id(CL_SHOP_PRODUCT);

		$config_form = $arr['obj_inst']->prop('config_form');
		if (!empty($config_form))
		{
			$cfgform_inst = get_instance('cfg/cfgform');
			$all_properties = $cfgform_inst->get_props_from_cfgform(array(
				'id' => $arr['obj_inst']->prop('config_form'),
			));
			
		}
		else
		{
			$all_properties = $o->get_property_list();
		}
*/
		$all_properties = array(
			'price' => array(
				'caption' => t('Hind'),
				'name' => 'price'
			),
			'code' => array(
				'caption' => t('Tootekood'),
				'name' => 'code'
			)
		);

		$xml_file = $arr['obj_inst']->prop('ftp_file_location_pricing');

		$format = t('Toote objektidele hinnad XML fail (%s) p&otilde;hjal');
		$t->set_caption(sprintf($format, basename($xml_file)));
	
		$saved_config = $arr['obj_inst']->meta('prices_config');

		foreach ( $all_properties as $name => $data )
		{
			$t->define_data(array(
				'property' => $data['caption'],
				'xml_tag' => html::textbox(array(
					'name' => 'prices_config['.$data['name'].']',
					'value' => $saved_config[$data['name']]
				))
			));
		}

		return PROP_OK;
	}

	function _set_prices_config_table($arr)
	{
		$arr['obj_inst']->set_meta('prices_config', $arr['request']['prices_config']);
		return PROP_OK;
	}

	function _get_users_config_table($arr)
	{
		$t = &$arr['prop']['vcl_inst'];
		$t->set_sortable(false);

		$t->define_field(array(
			'name' => 'property',
			'caption' => t('AW objekti v&auml;ljad')
		));
		$t->define_field(array(
			'name' => 'xml_tag',
			'caption' => t('XML tagide nimed')
		));

		$xml_file = $arr['obj_inst']->prop('ftp_file_location_customer');

		$format = t('Kasutaja objektid XML faili %s p&otilde;hjal');
		$t->set_caption(sprintf($format, basename($xml_file)));

		$o = new object();
		$o->set_class_id(CL_USER);
		$all_properties = $o->get_property_list();

		$saved_config = $arr['obj_inst']->meta('users_config');

		$show_properties = array(
			'uid',
			'real_name'
		);

		foreach ( $show_properties as $name )
		{
			$t->define_data(array(
				'property' => $all_properties[$name]['caption'],
				'xml_tag' => html::textbox(array(
					'name' => 'users_config['.$name.']',
					'value' => $saved_config[$name]	
				))
			));
		}

		return PROP_OK;
	}

	function _set_users_config_table($arr)
	{
		$arr['obj_inst']->set_meta('users_config', $arr['request']['users_config']);
		return PROP_OK;
	}

	function _get_categories_config_table($arr)
	{
		$t = &$arr['prop']['vcl_inst'];
		$t->set_sortable(false);

		$t->define_field(array(
			'name' => 'property',
			'caption' => t('AW objekti v&auml;ljad')
		));
		$t->define_field(array(
			'name' => 'xml_tag',
			'caption' => t('XML tagide nimed')
		));

		$o = new object();
		$o->set_class_id(CL_MENU);
		$all_properties = $o->get_property_list();

		$show_properties = array(
			'name',
			'comment'
		);

		$saved_config = $arr['obj_inst']->meta('categories_config');

		foreach ( $show_properties as $name )
		{
			$t->define_data(array(
				'property' => $all_properties[$name]['caption'],
				'xml_tag' => html::textbox(array(
					'name' => 'categories_config['.$name.']',
					'value' => $saved_config[$name]
				))
			));
		}

		return PROP_OK;
	}

	function _set_categories_config_table($arr)
	{
		$arr['obj_inst']->set_meta('categories_config', $arr['request']['categories_config']);
		return PROP_OK;
	}

	function _get_availability_config_table($arr)
	{
		$t = &$arr['prop']['vcl_inst'];
		$t->set_sortable(false);

		$t->define_field(array(
			'name' => 'property',
			'caption' => t('AW objekti v&auml;ljad')
		));
		$t->define_field(array(
			'name' => 'xml_tag',
			'caption' => t('XML tagide nimed')
		));

		$o = new object();
		$o->set_class_id(CL_SHOP_PRODUCT);

		$config_form = $arr['obj_inst']->prop('config_form');
		if (!empty($config_form))
		{
			$cfgform_inst = get_instance('cfg/cfgform');
			$all_properties = $cfgform_inst->get_props_from_cfgform(array(
				'id' => $arr['obj_inst']->prop('config_form'),
			));
			
		}
		else
		{
			$all_properties = $o->get_property_list();
		}


		$xml_file = $arr['obj_inst']->prop('ftp_file_location_availability');

		$format = t('Lao toote objektid XML faili %s p&otilde;hjal');
		$t->set_caption(sprintf($format, basename($xml_file)));

		
		$saved_config = $arr['obj_inst']->meta('availability_config');

		foreach ( $all_properties as $name => $data)
		{
			$t->define_data(array(
				'property' => $data['caption'],
				'xml_tag' => html::textbox(array(
					'name' => 'availability_config['.$name.']',
					'value' => $saved_config[$name]	
				))
			));
		}

		return PROP_OK;
	}

	function _set_availability_config_table($arr)
	{
		$arr['obj_inst']->set_meta('availability_config', $arr['request']['availability_config']);
		return PROP_OK;
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

	/** 
		@attrib name=do_import nologin=1

		@param id required type=int acl=view
			Scala import object id
		@param pricing optional type=int 
			Import pricing data
		@param users optional type=int 
			Import users
		@param categories optional type=int
			Import categories
		@param availability optional type=int 
			Import availability data
        **/
	function do_import($arr)
	{
		aw_set_exec_time(AW_LONG_PROCESS);

		$this->log_str = '';
		$this->log_file_name = 'scala_import_log_'.date('d-m-Y-H-i-s').'.log';
		if ( $this->can('view', $arr['id']) )
		{
			$o = new object($arr['id']);
		}
		else
		{
			$this->log_str .= "[ error ] Scala Import object is not accessible\n";
			$this->write_log();
			return false;
		}

		// we need ftp connection

		$ftp = get_instance('protocols/file/ftp');

		$connection = $ftp->connect(array(
			'host' => $o->prop('ftp_host'),
			'user' => $o->prop('ftp_user'),
			'pass' => $o->prop('ftp_password')
		));


		aw_disable_acl();
		// import users
		if ( $arr['users'] )
		{
			$this->log_str .= "[ info ] Import users\n";
			$customer_xml = $o->prop('ftp_file_location_customer');
		//	$raw_xml = file_get_contents($customer_xml);
			$raw_xml = $ftp->get_file($customer_xml);

			if ( !empty($raw_xml) )
			{
				$this->log_str .= "[ info ] Got the XML file (".$customer_xml.")\n";
				$this->_import_users(array(
					'raw_xml' => $raw_xml,
					'obj_inst' => $o
				));
			}
			else
			{
				$this->log_str .= "[ error ] Couldn\"t get the ".$customer_xml." file, so will not import users\n";
			}
		}


		// import categories and availability (availability)
		if ( $arr['categories'] || $arr['availability'] )
		{
			// lets disable the acl checks for better performance during import

			$warehouse_inst = get_instance(CL_SHOP_WAREHOUSE);
			$warehouse = $o->prop('warehouse');
			
			if ( $this->can('view', $warehouse) )
			{
				$warehouse = new object($warehouse);
			}
			else
			{
				$this->log_str .= "[ error ] Warehouse object is not accessible\n";
				$this->write_log();
			}

			$products_folder = $warehouse_inst->get_products_folder($warehouse);
			// exit if the products folder isn't found
			if ( empty($products_folder) )
			{
				$this->log_str .= "[ error ] Products folder object is not accessible\n";
				$this->write_log();
				return false;
			}


			$availability_xml = $o->prop('ftp_file_location_availability');
		//	$raw_xml = file_get_contents($availability_xml);
			$raw_xml = $ftp->get_file($availability_xml);

			// exit if the xml file content isn't available
			if ( empty($raw_xml) )
			{
				$this->log_str .= "[ error ] Couldn\"t get the ".$availability_xml." file, so will not import availability (warehouse status) data\n";
				$this->write_log();
				return false;
			}
			else
			{
				$this->log_str .= "[ info ] Got the XML file (".$availability_xml.")\n";
			}
			// import categories
			if ( $arr['categories'] )
			{
				$this->log_str .= "[ info ] Import categories\n";
				$this->_import_categories(array(
					'obj_inst' => $o,
					'raw_xml' => $raw_xml,
					'products_folder' => $products_folder
				));
			}

			// import warehouse status
			if ( $arr['availability'] )
			{
				$this->log_str .= "[ info ] Import availability\n";
				$this->_import_availability(array(
					'obj_inst' => $o,
					'raw_xml' => $raw_xml,
					'products_folder' => $products_folder,
					'warehouse' => $warehouse,
					'warehouse_inst' => $warehouse_inst
				));
			}
			// lets restore the acl checking after import
		}
		
		// import pricing
		if ( $arr['pricing'] )
		{
			$this->log_str .= "[ info ] Import pricing\n";
			$pricing_xml = $o->prop('ftp_file_location_pricing');
		//	$raw_xml = file_get_contents($pricing_xml);
			$raw_xml = $ftp->get_file($pricing_xml);

			if ( !empty($raw_xml) )
			{
				$this->log_str .= "[ info ] Got the XML file (".$pricing_xml.")\n";
				$this->_import_prices(array(
					'raw_xml' => $raw_xml,
					'obj_inst' => $o
				));
			}
			else
			{
				$this->log_str .= "[ error ] Couldn\"t get the ".$pricing_xml." file, so will not import pricing data\n";
			}
			
		}

		echo "Import complete";
		// save the import ending time into meta, so we can show it in interface and provide
		// an easy way to check if the cron has executed the import and has it completed or not
		$o->set_meta('import_end_time', time());
		$o->save();
		aw_restore_acl();
		$this->write_log();

	}

	function _import_prices($arr)
	{
		list($parsed_xml['values'], $parsed_xml['tags']) = parse_xml_def(array('xml' => $arr['raw_xml']));

		$config = array_flip($arr['obj_inst']->meta('prices_config'));
		unset($config['']);

		// lets collect the product codes:
		$products_price_data = array();
		foreach ($parsed_xml['values'] as $data)
		{
			if ($data['tag'] == 'Line' && $data['type'] == 'open')
			{
				$product_code = '';
				$product_price = '';
			}

			if ($data['type'] == 'complete')
			{

				if ( array_key_exists($data['tag'], $config) )
				{
					switch ($config[$data['tag']])
					{
						case 'price':
							$product_price = $data['value'];
							break;
						case 'code':
							$product_code = $data['value'];
							break;
					}
				}
			}

			if ($data['tag'] == 'Line' && $data['type'] == 'close')
			{
				if (!empty($product_code))
				{
					$products_price_data[$product_code] = $product_price;
				}
			}
		}

		$product_codes = array_keys($product_price_data);
		$products = new object_list(array(
			'class_id' => CL_SHOP_PRODUCT,
			'code' => array_keys($products_price_data),
			'status' => STAT_ACTIVE
		));
		foreach ($products->arr() as $product)
		{
			$product->set_prop('price', $products_price_data[$product->prop('code')]);
			echo "[hind] toote (".$product->id().") \"".$product->name()."\" hind on \"".$product->prop("price")."\" -- hind XML-ist: ".$products_price_data[$product->prop('code')];
			flush();
			$product->save();
			echo " [saved]<br>\n";
			flush();
		}
		$this->log_str .= "[ ok ] Pricing info import is complete\n";
		return true;
	}

	function _import_users($arr)
	{
		// mh, xml parser don't like plain '&' characters, so i'll replace them with entities:
		$arr['raw_xml'] = str_replace('&', '&amp;', $arr['raw_xml']);

		list($xml_values, $xml_tags) = parse_xml_def(array('xml' => $arr['raw_xml']));
		
		$config = array_flip($arr['obj_inst']->meta('users_config'));

		$user_inst = new user();
		$group_inst = get_instance(CL_GROUP);

		$group = $arr['obj_inst']->prop('user_group');
		if ( $this->can('view', $group) )
		{
			$group = new object($group);
		}
		else
		{
			// so exit if the group isn't set
			$this->log_str .= "[ error ] Users group isn't set \n";
			return false;
		}

		$data = $this->_parse_data_from_xml(array(
			'xml_values' => $xml_values,
			'config' => $config,
			'key' => 'uid'
		));
		foreach ($data as $user_data)
		{
			if (!$user_inst->username_is_taken($user_data['uid']))
			{
				$user = $user_inst->add_user($user_data);
				$group_inst->add_user_to_group($user, $group);
			}
		}
		$this->log_str .= "[ ok ] Users import is complete\n";
		return true;
	}

	function _import_categories($arr)
	{
		$arr['raw_xml'] = str_replace('&', '&amp;', $arr['raw_xml']);
		list($xml_values, $xml_tags) = parse_xml_def(array('xml' => $arr['raw_xml']));
		$config = array_flip($arr['obj_inst']->meta('categories_config'));
		$data = $this->_parse_data_from_xml(array(
			'xml_values' => $xml_values,
			'config' => $config,
			'key' => 'name'
		));

		// new categories
		$categories = array();
		foreach ($data as $cat)
		{
			preg_match('/^\D+/', $cat['name'], $matches);
			$categories[$matches[0]][] = $cat;
		}

		// existing categories:
		$existing_categories = $this->get_categories(array(
			'parent' => $arr['products_folder']
		));

		foreach ($categories as $cat_name => $sub_categories)
		{
			$parent = array_search($cat_name, $existing_categories);
			if (!$parent)
			{
				// create folder
				$o = new object();
				$o->set_parent($arr['products_folder']);
				$o->set_class_id(CL_MENU);
				$o->set_name($cat_name);
				$o->set_status(STAT_ACTIVE);
				$parent = $o->save();
			}
			else
			{
				$o = new object($parent);
				if ($o->status() != STAT_ACTIVE)
				{
					$o->set_status(STAT_ACTIVE);
					$o->save();
				}
				
				unset($existing_categories[$parent]);
			}

			foreach ($sub_categories as $sub_category)
			{
				$sub_cat = array_search($sub_category['name'], $existing_categories);
				if (!$sub_cat)
				{
					// create folder
					$o = new object();
					$o->set_parent($parent);
					$o->set_class_id(CL_MENU);
					$o->set_name($sub_category['name']);
					$o->set_status(STAT_ACTIVE);
					$o->save();
				}
				else
				{
					$o = new object($sub_cat);
					if ($o->status() != STAT_ACTIVE)
					{
						$o->set_status(STAT_ACTIVE);
						$o->save();
					}
					unset($existing_categories[$sub_cat]);
				}
			}

		}

		// those categories which are in aw but not in xml, we set their status to not active
		foreach ($existing_categories as $oid => $name)
		{
			$o = new object($oid);
			if ($o->status() == STAT_ACTIVE)
			{
				$o->set_status(STAT_NOTACTIVE);
				$o->save();
			}
		}
		$this->log_str .= "[ ok ] Categories import is complete\n";
		return true;
	}

	function _import_availability($arr)
	{
		$arr['raw_xml'] = str_replace('&', '&amp;', $arr['raw_xml']);
		list($xml_values, $xml_tags) = parse_xml_def(array('xml' => $arr['raw_xml']));

		$availability_config = $arr['obj_inst']->meta('availability_config');
		$config = array_flip($availability_config);

		$properties = $config; // in properties array, there will be only properties that can be saved via $o->set_prop() method

		$product_config_form = $arr['obj_inst']->prop('config_form');

		// category name and product name both go into the objects name property, so
		// i add prefix cat_ to category config property name, so they won't conflict
		// while parsing xml
		$cat_config = array_flip($arr['obj_inst']->meta('categories_config'));
		foreach ($cat_config as $key => $value)
		{
			$config[$key] = 'cat_'.$value;
			unset($properties[$key]);
		}
		// other elements that will not be saved:
		unset($properties[''], $properties[$availability_config['name']]);

		$data = $this->_parse_data_from_xml(array(
			'xml_values' => $xml_values,
			'config' => $config,
			'key' => 'code'
		));
		// categories
		$categories = $this->get_categories(array(
			'parent' => $arr['products_folder']
		));

		// existing products 
		$existing_products = array();

		$warehouse_products = $arr['warehouse_inst']->get_packet_list(array(
			'id' => $arr['warehouse']->id(),
			'parent' => array_keys($categories) 
		));

		foreach ($warehouse_products as $o)
		{
			$existing_products[$o->id()] = $o->prop('code');
			echo "-- existing product: ".$o->id().", code: ".$o->prop('code').", name: ".$o->name()." [".$o->status()."]<br>\n";
		}
		// products from xml
		foreach ($data as $product_code => $product_data)
		{
			$product = array_search($product_code, $existing_products);
			if (!$product)
			{
				$category = array_search($product_data['cat_name'], $categories);
				if ($category)
				{
					echo "## creating new product ".$product_data['name']."<br>\n";
					// create a new product
					$o = new object();
					$o->set_class_id(CL_SHOP_PRODUCT);
					$o->set_parent($category);
					$o->set_status(STAT_ACTIVE);
					$o->set_meta('cfgform_id', $product_config_form);
					$o->set_name($product_data['name']);
					foreach ($properties as $property)
					{
						$o->set_prop($property, $product_data[$property]." ");
					}
					$o->save();
				}
			}
			else
			{
				// product exists
				$data_changed = false; // lets check if data has been changed

				echo "## product exists: ".$product_data['name']." <br />\n";
				$o = new object($product);
				if (trim($o->name()) != trim($product_data['name']))
				{
					$o->set_name($product_data['name']);
					echo "\t#### changed name from ".$o->name()." to ".$product_data['name']."<br />\n";
					$data_changed = true;
				}
				if ($o->status() != STAT_ACTIVE)
				{
					$o->set_status(STAT_ACTIVE);
					echo "\t#### set status to active<br />\n";
					$data_changed = true;
				}
				
				// the properties:
				foreach ($properties as $property)
				{
					if (trim($o->prop($property)) != trim($product_data[$property]))
					{
						$o->set_prop($property, trim($product_data[$property])." ");
						echo "\t#### changed property ".$property." value from ".$o->prop($property)." to ".$product_data[$property]."<br />\n";
						$data_changed = true;
					}
				}

				// save the object only when data is changed
				if ($data_changed)
				{
					echo "\t#### data has been changed, so SAVE the object <br />\n";
					$o->save();
				}
				unset($existing_products[$product]);
			}
		}
		echo "end of new products<br>\n";
		echo "setting others not active<br>\n";
		// those products which are in aw but not in xml, we set their status to not active
		foreach ($existing_products as $oid => $code)
		{
			$o = new object($oid);
			if ($o->status() == STAT_ACTIVE)
			{
				$o->set_status(STAT_NOTACTIVE);
				$o->save();
			}
		}
		arr('availability import complete');
		$this->log_str .= "[ ok ] Availability import is complete\n";
		return true;
	}

	function _parse_data_from_xml($arr)
	{
		$xml_values = $arr['xml_values']; // xml values
		$config = $arr['config']; // xml to prop name mapping
		$key = $arr['key']; // the property name, which value is going to be the key in the result array
		$data = array();
		foreach ( $xml_values as $value )
		{
			if ($value['tag'] == 'Line' && $value['type'] == 'open')
			{
				$params = array();
			}

			if ($value['type'] == 'complete')
			{
				if ( array_key_exists(trim($value['tag']), $config) )
				{
					$params[ $config[ $value['tag'] ] ] = $value['value'];
				}
			}

			if ($value['tag'] == 'Line' && $value['type'] == 'close')
			{
				if (empty($key))
				{
					$data[] = $params;
				}
				else
				{
					$data[$params[$key]] = $params;
				}
			}
		}
		return $data;
	}

	function get_categories($arr)
	{
		$categories = array();
		$ot = new object_tree(array(
			'class_id' => CL_MENU,
			'parent' => $arr['parent'],
		));

		$ol = $ot->to_list();
		
		foreach ($ol->arr() as $o)
		{
			$categories[$o->id()] = $o->name();
		}

		return $categories;
	}

	function write_log($arr)
	{
		// write the log file:
		$logs_dir = aw_ini_get('site_basedir').'/files/scala_import_logs';
		if ( !is_dir($logs_dir) )
		{
			mkdir($logs_dir);
		}
		$log_file = $logs_dir.'/'.$this->log_file_name;

		$file = fopen($log_file, 'a');
		fwrite($file, $this->log_str);
		fclose($file);

		return true;
	}


	function do_db_upgrade($table, $field, $query, $error)
	{
		$int = array(
			'warehouse',
			'user_group',
			'config_form',
		);

		$varchar_255 = array(
			'ftp_host',
			'ftp_user',
			'ftp_password',
			'ftp_file_location_pricing',
			'ftp_file_location_customer',
			'ftp_file_location_availability'
		);

		if (empty($field))
		{
			$sql = 'create table '.$table.' (oid int primary key not null ';
			foreach ( $varchar_255 as $value )
			{
				$sql .= ', '.$value.' varchar(255) ';
			}

			foreach ( $int as $value )
			{
				$sql .= ', '.$value.' int ';
			}

			$sql .= ')';
			$this->db_query($sql);
			return true;
		}
		if (in_array($field, $varchar_255))
		{
			$this->db_add_col($table, array(
				'name' => $field,
				'type' => 'varchar(255)'
			));
			return true;
		}
		if (in_array($field, $int))
		{
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
