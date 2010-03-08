<?php
class warehouse_products_import extends warehouse_data_import
{

	/*
		This class should contain the implementation of products import stuff
		All the general logic of the import process should be implemented in warehouse_data_import class
	*/

	var $logname = '/tmp/taket_prod_imp_log.log'; // tmp !!!
	var $xml_file;
	function warehouse_products_import()
	{

	}

	function get_xml_data($o)
	{
		$ds = $this->get_datasource($o);
	/*
		$this->xml_file = $ds->fetch_product_xml(
			$this->wh_import,
			$this->callback,
			1,
			true
		);
	*/
		$xml_file_path = $ds->get_products_xml();
	//	$this->xml_file = aw_ini_get('site_basedir').'/files/warehouse_import/products.xml';
		return $xml_file_path;
	}

	// returns the folder where the xml files will be stored
	// it overrides the same function from warehouse_data_import class
	function get_data_folder()
	{
		$fn = aw_ini_get('site_basedir').'/files/warehouse_import/products';
		if (file_exists($fn) === false)
		{
			// create the directories recursively if they don't exist
			mkdir($fn, 0777, true);
		}
		return $fn;
	}

	function divide_to_pieces($file = '')
	{
		$this->write_log('start dividing into pieces');
		$xml = simplexml_load_file($this->xml_file);

		$counter = 0;

		$xml_elements = array();
		foreach ($xml->product as $p)
		{
			$counter++;
			$xml_elements[] = $p->asXML();
			if (($counter % 1000) == 0)
			{
				$this->write_xml_file(new SimpleXMLElement('<chunk>'.implode('', $xml_elements).'</chunk>'));
				$xml_elements = array();
			}
		}
		$this->write_xml_file(new SimpleXMLElement('<chunk>'.implode('', $xml_elements).'</chunk>'));

		$this->write_log('end dividing into pieces');

	}

	// this function should load an array of data which will be used to import data
	// the implementation can
	// NOTE: Ma peaksin siia tegema meetodi, mis tagastab laetud xml-i tykist 1 toote xml-i ja annab selle siis l2bi warehouse_data_import::step meetodi parammetrina siia tagasi ...
	function load_data($file)
	{
		$r = new XMLReader();
		$r->open($file);

		while ($r->read())
		{
			// If the node, in depth 1, is packet or product, then this is root for an item and split it up on that node
			// actually, for later testing, i don't need to check the node name, it should be enough to use the whatever 
			// node there is in depth 1 to use as item root
			if ( ( $r->name == 'packet' || $r->name == 'product' ) && $r->nodeType == XMLReader::ELEMENT && $r->depth == 1)
			{
				$item = $r->readOuterXML();
				$this->data[] = $item;
			}
		}
	}

	function process_item($item)
	{
		$dom = new DOMDocument();
		$dom->loadXML($item);

		// First of all I should get all the products from this piece of XML
	//	$product_nodes = $dom->getElementsByTagName('product');

		$product_codes = $dom->getElementsByTagName('code');
		foreach ($product_codes as $code)
		{
			$product_codes_list[] = $code->nodeValue;
		}

		$products_ol = new object_list(array(
			'class_id' => CL_SHOP_PRODUCT,
			'code' => $product_codes_list
		));

		$products_lut = array();	
		foreach ($products_ol->arr() as $o)
		{
			$products_lut[$o->prop('code')] = $o->id();
		}

		// create brands look-up-table
		$brands_ol = new object_list(array(
			'class_id' => CL_SHOP_BRAND
		));

		$brands_lut = array();
		foreach ($brands_ol->arr() as $brand_oid => $brand_obj)
		{
			$brand_codes = $brand_obj->prop('code');
			foreach (safe_array(explode(',', $brand_codes)) as $brand_code)
			{
				$brands_lut[$brand_code] = $brand_oid;
			}
		}

		// get packet
		$packets_ol = new object_list();
		if (!empty($products_lut))
		{
			$packets_ol = new object_list(array(
				'class_id' => CL_SHOP_PACKET,
				'CL_SHOP_PACKET.RELTYPE_PRODUCT' => $products_lut
			));
		}

		if ($packets_ol->count() == 0)
		{
			$packet_obj = new object();
			$packet_obj->set_class_id(CL_SHOP_PACKET);
			$packet_obj->set_parent($this->packets_folder);
			$packet_obj->save();
			echo "<strong>Add new packet ( ".$packet_obj->id()." )</strong><br />\n";
		}
		else
		{
			$packet_obj = $packets_ol->begin();
			echo "<strong>Load existing packet ( ".$packet_obj->id()." )</strong><br />\n";
		}

		$packets = $dom->getElementsByTagName('packet');
		foreach ($packets as $packet)
		{
			foreach ($packet->childNodes as $node)
			{
				if ($node->nodeName == 'name')
				{
					$packet_obj->set_name($node->nodeValue);
				}
				if ($node->nodeName == 'description')
				{
					$packet_obj->set_prop('description', $node->nodeValue);
				}

				// categories thingie have to go here as well
				if ($node->nodeName == 'categories')
				{
					echo " - Categories: <br />\n";
					$categories_lut = array();
					$category_conns = $packet_obj->connections_from(array(
						'type' => 'RELTYPE_CATEGORY'
					));
					foreach (safe_array($category_conns) as $category_conn)
					{
						$existing_cat_obj = $category_conn->to();
						$categories_lut[$existing_cat_obj->prop('code')] = $existing_cat_obj->id();
					}

					foreach ($node->childNodes as $cat_node)
					{
						echo " -- ".$cat_node->nodeValue;
						// first of all I need to check, if this category already exists or not
						$cat_ol = new object_list(array(
							'class_id' => CL_SHOP_PRODUCT_CATEGORY,
							'code' => $cat_node->nodeValue
						));
						
						if ($cat_ol->count() > 0)
						{
							$cat_obj = $cat_ol->begin();
							echo " exists (".$cat_obj->id().")";
						}
						else
						{
							$cat_obj = new object();
							$cat_obj->set_class_id(CL_SHOP_PRODUCT_CATEGORY);
							$cat_obj->set_parent($this->categories_folder);
							$cat_obj->set_name($cat_node->nodeValue);
							$cat_obj->set_prop('code', $cat_node->nodeValue);
							$cat_obj->save();
							echo " new (".$cat_obj->id().")";
						}

						if (empty($categories_lut[$cat_node->nodeValue]))
						{
							echo " not connected, connecting";
							$packet_obj->connect(array(
								'type' => 'RELTYPE_CATEGORY',
								'to' => $cat_obj->id()
							));
							$packet_obj->save();
						}
						else
						{
							echo " connected";
						}
						// lets unset the categories we just connected, so we can track which 
						// connected categories will remain, so we can disconnect them
						unset($categories_lut[$cat_node->nodeValue]);
						echo " [done]<br />\n";
					
						// brands:
						echo " -- Connect brand ";
						if (!empty($brands_lut[$cat_node->nodeValue]))
						{
							$brand_conns = $packet_obj->connections_from(array(
								'type' => 'RELTYPE_BRAND'
							));
							if (empty($brand_conns))
							{
								echo " - connect brand(s) ".$cat_noce->nodeValue." (".$brands_lut[$cat_node->nodeValue].") ";
								$packet_obj->connect(array(
									'type' => 'RELTYPE_BRAND',
									'to' => $brands_lut[$cat_node->nodeValue]
								));
							}
							else
							{
								// here i should implement the brand objects update logic, but let it be at the moment
							}
						}
						else
						{
							echo " - this category is not a brand! <br />\n";
						}
					}
				/*
				// I remove this part right now, cause if somebody adds some categories by hand, then they would be removed in next import
				// if the changes aren't in import files
				// There should be somewhere some import related configuration options where i can force to sync categories
					echo "I have to disconnect those categories: ".implode(', ', array_keys($categories_lut));
					foreach ($categories_lut as $cat_oid)
					{
						$packet_obj->disconnect(array('from' => $cat_oid));
					}
				*/	
					echo " [done]<br />\n";
				}

				if ($node->nodeName == 'products')
				{
					$existing_products_lut = array();
					$existing_products_conns = $packet_obj->connections_from(array(
						'type' => 'RELTYPE_PRODUCT'
					));

					foreach (safe_array($existing_products_conns) as $existing_products_conn)
					{
						$existing_product_obj = $existing_products_conn->to();
						if (!empty($existing_products_lut[$existing_product_obj->prop('code')]))
						{
							$existing_product_obj->delete(true);
						}
						else
						{
							$existing_products_lut[$existing_product_obj->prop('code')] = $existing_product_obj->id();
						}
					}
					
					foreach ($node->childNodes as $product_node)
					{
						// I need to get the damn code value from here
						$code = $product_node->getElementsByTagName('code');
						$code = $code->item(0)->nodeValue;

					//	if (!empty($products_lut[$code]))
						if (!empty($existing_products_lut[$code]))
						{
						//	$product_obj = new object($products_lut[$code]);
							$product_obj = new object($existing_products_lut[$code]);
						}
						else
						{
							echo " ---- Add new product <br />\n";
							$product_obj = new object();
							$product_obj->set_class_id(CL_SHOP_PRODUCT);
							$product_obj->set_parent($this->products_folder);
							$product_obj->save();
						}
						$this->process_product($product_obj, $product_node);
						
						$product_obj->set_name($packet_obj->name().' - '.$product_obj->prop('code'));
						$product_obj->save();
						$product_conns = $packet_obj->connections_from(array(
							'type' => 'RELTYPE_PRODUCT',
							'to' => $product_obj->id()
						));
						if (empty($product_conns))
						{
							$packet_obj->connect(array(
								'type' => 'RELTYPE_PRODUCT',
								'to' => $product_obj->id()
							));
							$packet_obj->save();
						}
					}
				}
			}
			$packet_obj->save();
		}
	}

	function process_product($product_obj, $node)
	{
		// looping through the <product> tag children
		echo " ---- Start importing products: <br /> \n";
		foreach ($node->childNodes as $n)
		{
			switch ($n->nodeName)
			{
				case 'type':
					$product_obj->set_prop('type_code', $n->nodeValue);
					break;
				case 'color':
					
					$color_conns = $product_obj->connections_from(array(
						'type' => 'RELTYPE_COLOR'
					));

					if (empty($color_conns))
					{
						$color = new object();
						$color->set_class_id(CL_SHOP_COLOUR);
						$color->set_parent($product_obj->id());
						$color->set_name($n->nodeValue);
						$color_id = $color->save();
						$product_obj->connect(array('to' => $color_id, 'type' => 'RELTYPE_COLOR'));
					}
					else
					{
						$color = reset($color_conns);
						$color = $color->to();
						$color->set_name($n->nodeValue);
						$color->save();
					}
					echo " ---- color: ".$color->name()."<br />\n";
					break;
				case 'code':
					$product_obj->set_prop('code', $n->nodeValue);
					echo " ---- code: ".$n->nodeValue."<br />\n";
					break;
				case 'images':
					$image_conns = $product_obj->connections_from(array(
						'type' => 'RELTYPE_IMAGE'
					));
					$existing_images_lut = array();
					foreach (safe_array($image_conns) as $image_conn)
					{
						$existing_image = $image_conn->to();
						$existing_images_lut[$existing_image->name()] = $existing_image->id();
					}
					foreach ($n->childNodes as $image_node)
					{
						if (pathinfo($image_node->nodeValue, PATHINFO_EXTENSION) == 'jpg')
						{	
							$image_name = basename($image_node->nodeValue);
						
							if (!empty($existing_images_lut[$image_name]))
							{
								$image_obj = new object($existing_images_lut[$image_name]);
							}
							else
							{
								$image_obj = new object();
								$image_obj->set_parent($product_obj->id());
								$image_obj->set_class_id(CL_IMAGE);
								$image_obj->set_status(STAT_ACTIVE);
								$image_obj->set_name($image_name);
								$image_oid = $image_obj->save();
								$product_obj->connect(array(
									'type' => 'RELTYPE_IMAGE',
									'to' => $image_oid
								));
							}
							$image_inst = new image();
							$image_data = $image_inst->add_image(array(
								'from' => 'url',
								'url' => $image_node->nodeValue,
								'orig_name' => $image_name,
								'id' => $image_obj->id()
							));

							$resize_params = array(
								'id' => $image_obj->id(),
								'file' => 'file',
								'width' => 164
							);
						
							$image_inst->resize_picture(&$resize_params);
							$image_obj->add_image_big($image_node->nodeValue);
							$image_obj->save();
							aw_cache_flush('get_image_by_id');

							// Let keep track of those images I have updated
							unset($existing_images_lut[$image_name]);
						}
					
					}
					// I can't delete stuff from product objects that easily
					// cause there are cases where one product is updated twice 
					// with different data and i need to keep it all
					foreach ($existing_images_lut as $existing_image)
					{
				//		$existing_image_obj = new object($existing_image);
				//		$existing_image_obj->delete(true);
					}
					break;
				case 'packagings':
					echo " ------ Start importing packagings: <br />\n";
					$packaging_conns = $product_obj->connections_from(array(
						'type' => 'RELTYPE_PACKAGING'
					));
				
					foreach (safe_array($packaging_conns) as $pc)
					{
						$packaging = $pc->to();

						// lets handle the duplicate sizes for now. I might need to rework this when we need to save the old sizes as well ...
						if (!empty($packaging_lut[$packaging->prop('size')]))
						{
							$packaging->delete(true);
						}
						else
						{
							$packaging_lut[$packaging->prop('size')] = $packaging->id();
						}
					}

					foreach ($n->childNodes as $packaging_node)
					{
						// I need to get this size node value, to check if there is an packaging object for that size or not:
						$sizes = $packaging_node->getElementsByTagName('size');
						$size = $sizes->item(0)->nodeValue;

						echo " ------ ".$size;

						if (!empty($packaging_lut[$size]))
						{
							$packaging_object = new object($packaging_lut[$size]);
							echo " existing (".$packaging_lut[$size].")";
						}
						else
						{
							$packaging_object = new object();
							$packaging_object->set_class_id(CL_SHOP_PRODUCT_PACKAGING);
							$packaging_object->set_parent($product_obj->id());
							$packaging_oid = $packaging_object->save();

							echo " new (".$packaging_oid.")";
							$product_obj->connect(array(
								'type' => 'RELTYPE_PACKAGING',
								'to' => $packaging_oid
							));
							
						}
						$this->process_packaging($packaging_object, $packaging_node);

						// lets put some meaningful name for packaging object as well:
						$packaging_object->set_name($product_obj->prop('code').' - '.$packaging_object->prop('size').' - '.$packaging_object->prop('price'));
						$packaging_object->save();
						// I need to mark here which packagings got updated during the import
						unset($packaging_lut[$packaging_object->prop('size')]);
						echo " - [done]".$packaging_object->prop('size')." - ".$packaging_object->prop('price')."<br />\n";
					}
					// I can't delete stuff from product object that easily cause there are
					// cases where one product object is updated twice with different data and 
					// i need to keep it all
				//	echo " ------ Those packagings were not updated: ";
					foreach (safe_array($packaging_lut) as $k => $v)
					{
					//	echo " ".$k." (".$v.") / ";
					//	$existing_packaging_obj = new object($v);
					//	$existing_packaging_obj->delete(true);
					}
					echo " ------ [done]<br />\n";
					break;
			}
		}
		return $product_obj->save();
	}

	function process_packaging($packaging_obj, $node)
	{
		foreach ($node->childNodes as $n)
		{
			switch ($n->nodeName)
			{
				case 'price':
					$packaging_obj->set_prop('price', $n->nodeValue);
					break;
				case 'size':
					$packaging_obj->set_prop('size', $n->nodeValue);
					break;
				case 'type':
					$packaging_obj->set_comment($n->nodevalue);
					break;
			}
		}
		return $packaging_obj->save();
	}

	function is_new($item)
	{
	
	}

	// API: add item implementation
	function add_item($item)
	{
	
	}

	// API: update item implementation
	function update_item($item)
	{
	
	}

	// API: remove item implementation
	function delete_item()
	{
	
	}

	// API: Have to return true if an item needs to be added or updated
	function check_item()
	{
	
	}
	
	// I'll assume here, that the product object doesn't exist in aw
	// So I need to add a line in objects table, aw_shop_products table 
	/*
		product_name
		product_code
		search_term
		replacement_product_code // I should change it to type code
		short_code
	*/
	private function add_product_sql($data)
	{
		$obj_base = $this->db_obj->db_fetch_array("select * from objects where class_id = '".CL_SHOP_PRODUCT."' limit 1");
		$obj_base = reset($obj_base);
		$obj_base['oid'] = 0;
		$obj_base['createdby'] = '110';
		$obj_base['modifiedby'] = '110';

		$obj_base['name'] = addslashes($data['product_name']);
		$obj_base['comment'] = addslashes($data['product_code']);
		$sql = "
			INSERT INTO 
				objects 
			VALUES (".implode(',', map('"%s"', $obj_base)).");
		";

		$this->db_obj->db_query($sql);

		$oid = $this->db_obj->db_last_insert_id();

		// brother_of value has to be the same as oid
		$this->db_obj->db_query("UPDATE objects set brother_of = ".$oid." WHERE oid = ".$oid);

		$sql = "
			INSERT INTO 
				aw_shop_products 
			SET
				aw_oid = ".$oid.",
				code = '".addslashes($data['product_code'])."',
				search_term = '".addslashes($data['search_term'])."',
				type_code = '".addslashes($data['replacement_product_code'])."',
				short_code = '".$this->apply_controller($data['product_code'])."'
		";
		$this->db_obj->db_query($sql);
		return $oid;
	}

	// the product object exists in the system, so i need to update the data
	/*
		oid

		product_name
		product_code
		search_term
		replacement_product_code // I should change it to type code
		short_code
	*/
	private function update_product_sql($oid, $data)
	{
		$sql = "
			UPDATE 
				objects
			SET
				name = '".addslashes($data['product_name'])."',
				comment = '".addslashes($data['product_code'])."',
				brother_of = ".$oid."
			WHERE
				oid = ".$oid."
		";
		$this->db_obj->db_query($sql);

		$sql = "
			UPDATE
				aw_shop_products
			SET
				code = '".addslashes($data['product_code'])."',
				search_term = '".addslashes($data['search_term'])."',
				type_code = '".addslashes($data['replacement_product_code'])."',
				short_code = '".$this->apply_controller($data['product_code'])."'
			WHERE
				aw_oid = ".$oid."
		";
		$this->db_obj->db_query($sql);
	}

	// maybe i don't have to make this one with sql-s?
	private function delete_product_sql($oid)
	{
		$sql = "DELETE FROM objects WHERE oid = ".$oid;
		$this->db_obj->db_query($sql);

		$sql = "DELETE FROM aw_shop_products WHERE aw_oid = ".$oid;
		$this->db_obj->db_query($sql);
	}
}
?>
