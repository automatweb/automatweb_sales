<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/watercraft_management/watercraft_add.aw,v 1.22 2009/02/13 12:29:51 dragut Exp $
// watercraft_add.aw - Vees&otilde;iduki lisamine 
/*

@classinfo syslog_type=ST_WATERCRAFT_ADD relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=dragut
@tableinfo watercraft_add index=oid master_table=objects master_index=oid

@default table=objects
@default group=general

	@property watercraft_type type=select table=watercraft_add
	@caption Aluse t&uuml;&uuml;p 

	@property watercraft_management type=relpicker reltype=RELTYPE_WATERCRAFT_MANAGEMENT table=watercraft_add
	@caption Vees&otilde;idukite haldus

	@property redir_after_publish type=textbox table=watercraft_add
	@caption Peale avaldamist suuna

@groupinfo required_fields caption="Kohustuslikud v&auml;ljad"
@default group=required_fields

	@property required_fields_table type=table no_caption=1
	@caption Kohustuslikud v&auml;ljad

@groupinfo pages caption="Lehek&uuml;ljed"
@default group=pages

	@property pages_table type=table no_caption=1
	@caption Lehek&uuml;ljed

@reltype WATERCRAFT_MANAGEMENT value=1 clid=CL_WATERCRAFT_MANAGEMENT
@caption Vees&otilde;idukite haldus
*/

class watercraft_add extends class_base
{

	var $watercraft_inst;

	function watercraft_add()
	{
		$this->init(array(
			"tpldir" => "applications/watercraft_management/watercraft_add",
			"clid" => CL_WATERCRAFT_ADD
		));

		$this->watercraft_inst = get_instance('applications/watercraft_management/watercraft');
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case 'watercraft_type':
				$prop['options'] = $this->watercraft_inst->watercraft_type;
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

	function _get_required_fields_table($arr)
	{
		$t = &$arr['prop']['vcl_inst'];
		$t->set_caption($arr['prop']['caption']);
		$t->set_sortable(false);

		$t->define_field(array(
			'name' => 'required',
			'caption' => t('Kohustuslik'),
			'width' => '5%',
			'align' => 'center'
		));
		$t->define_field(array(
			'name' => 'is_numeric',
			'caption' => t('Arv'),
			'width' => '5%',
			'align' => 'center'
		));
		$t->define_field(array(
			'name' => 'name',
			'caption' => t('Nimi')
		));
		$t->define_field(array(
			'name' => 'tpl_var',
			'caption' => t('Templeidi muutuja')
		));

		$o = new object();
		$o->set_class_id(CL_WATERCRAFT);
		$all_properties = $this->get_watercraft_properties(array(
			'type' => $arr['obj_inst']->prop('watercraft_type') // lets get the watercraft type from watercraft_add obj.
		));

		$all_groups = $o->get_group_list();

		$saved = $arr['obj_inst']->meta('required_fields');
		foreach ($all_properties as $prop_name => $prop_data)
		{

			if (empty($prop_data['caption']) || ($prop_data['caption'] == 'Info' && $prop_data['group'] == 'additional_equipment'))
			{
				$name_str = '<em>'.$prop_data['name'] .'</em> '. $prop_data['caption'];
			}
			else
			{
				$name_str = $prop_data['caption'];
			}

			$t->define_data(array(
				'required' => html::checkbox(array(
					'name' => 'sel['.$prop_name.'][required]',
					'value' => $prop_name,
					'checked' => (empty($saved[$prop_name]['required'])) ? '' : $prop_name
				)),
				'is_numeric' => html::checkbox(array(
					'name' => 'sel['.$prop_name.'][is_numeric]',
					'value' => $prop_name,
					'checked' => (empty($saved[$prop_name]['is_numeric'])) ? '' : $prop_name
				)),
				'name' => $name_str,
				'tpl_var' => '{VAR:'.$prop_name.'}',
				'group' => '<strong>'.$all_groups[$prop_data['group']]['caption'].'</strong>',
			));
		}

		$t->set_rgroupby(array(
			'group' => 'group'
		));
		return PROP_OK;
	}

	function _set_required_fields_table($arr)
	{
		$arr['obj_inst']->set_meta('required_fields', $arr['request']['sel']);
		return PROP_OK;
	}

	function _get_pages_table($arr)
	{
		$t = &$arr['prop']['vcl_inst'];
		$t->set_caption($arr['prop']['caption']);
		$t->set_sortable(false);
		$t->define_field(array(
			'name' => 'page',
			'caption' => t('Lehek&uuml;lg'),
			'width' => '10%',
			'align' => 'center'
		));
		$t->define_field(array(
			'name' => 'title',
			'caption' => t('Pealkiri'),
			'align' => 'center'
		));
		$t->define_field(array(
			'name' => 'overview',
			'caption' => t('&Uuml;levaade'),
			'align' => 'center',
		));
		$t->define_field(array(
			'name' => 'template',
			'caption' => t('Templeit'),
			'align' => 'center'
		));

		$saved = safe_array($arr['obj_inst']->meta('pages'));
		$count = 0;
		foreach ($saved as $page)
		{
			$t->define_data(array(
				'page' => $count,
				'title' => html::textbox(array(
					'name' => 'pages['.$count.'][title]',
					'value' => $saved[$count]['title']
				)),
				'overview' => html::checkbox(array(
					'name' => 'pages['.$count.'][overview]',
					'checked' => $saved[$count]['overview']?true:false,
				)),
				'template' => html::textbox(array(
					'name' => 'pages['.$count.'][template]',
					'value' => $saved[$count]['template']
				)),
			));
			$count++;
		}

		$t->define_data(array(
			'page' => $count,
			'title' => html::textbox(array(
				'name' => 'pages['.$count.'][title]',
			)),
			'template' => html::textbox(array(
				'name' => 'pages['.$count.'][template]',
			)),
		));

		return PROP_OK;

	}

	function _set_pages_table($arr)
	{
		$valid_rows = array();
		foreach ($arr['request']['pages'] as $page)
		{
			if (!empty($page['title']))
			{
				$valid_rows[] = $page;
			}
		}
		$arr['obj_inst']->set_meta('pages', $valid_rows);
		return PROP_OK;
	}

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	////////////////////////////////////
	// the next functions are optional - delete them if not needed
	////////////////////////////////////
	/** Change the realestate object info.
			
		@attrib name=parse_alias is_public="1" caption="Change"

	**/
	function parse_alias($arr)
	{
		return $this->show(array(
			'id' => $arr['alias']['to'],
			'doc_id' => $arr['alias']['from']
		));
	}

	/** this will get called whenever this object needs to get shown in the website, via alias in document **/
	function show($arr)
	{

		enter_function('watercraft_add::show');

		/*
			so this is how it works ...
			i'll create  a new object when user passes the first page
			i'll save the properties everytime form is submitted and passed (no errors)
			while showing already submitted forms, i'll ask the object the properties
			images are saved under watercraft object, no connections at the moment 
		*/

		// watercraft_add object
		$o = new object($arr["id"]);

		// ask the type from watercraft_add object
		$type = $o->prop('watercraft_type'); 

		// lets get the property elements:
		$elements = $this->get_watercraft_properties(array(
			'type' => $type,
		));

		$pages = $o->meta('pages');
		$saved_pages = $_SESSION['watercraft_input_data']['saved_pages'];
		$page = (int)$_GET['page'];
		$vars = array();


		// if the watercraft id is set in the url, then we should load that watercraft obj. 
		if ($this->can('view', $_SESSION['watercraft_input_data']['watercraft_id']))
		{
			$watercraft_obj = new object($_SESSION['watercraft_input_data']['watercraft_id']);

			// if the watercraft type in the session doesn't match the one which comes from the watercraft_add
			// object, then we need to add all new watercraft object, so lets reset the watercraft data in session
			if ($watercraft_obj->prop('watercraft_type') != $type)
			{
				$watercraft_obj = '';
				$saved_pages = array();
				$_SESSION['watercraft_input_data'] = array();
			}
		}
		else
		{

			if ($this->can('view', $_GET['watercraft_id']))
			{
				$watercraft_obj = new object($_GET['watercraft_id']);
				$_SESSION['watercraft_input_data']['watercraft_id'] = $_GET['watercraft_id'];
				$saved_pages = array_keys($pages);
				$_SESSION['watercraft_input_data']['saved_pages'] = $saved_pages;
			}
		}

		// temp hack for seller
		$u = get_instance(CL_USER);
		if($watercraft_obj && !$watercraft_obj->prop("seller") && $u->get_current_person())
		{
			$watercraft_obj->set_prop("seller", $u->get_current_person());
			$watercraft_obj->save();
		}

		// add 10 imageupload fields also
		for ($i = 1; $i <= 10; $i++)
		{
			$elements['image_upload_'.$i] = array(
				'name' => 'image_upload_'.$i,
				'id' => 'image_upload_'.$i,
				'type' => 'fileupload'
			);
		}


		// draw pages: 
		$vars['pages'] = $this->draw_pages(array(
			'saved_pages' => $saved_pages,
			'pages' => $pages,
			'page' => $page
		));

		// load template
		if (empty($pages))
		{
			$this->read_template("show.tpl");
		}
		else
		{
			if (empty($page))
			{
				$page_data = reset($pages); 
			}
			else
			{
				$page_data = $pages[$page];
			}
			$this->read_template($page_data['template']);
		}

		// if there are any errors, then show them
		$required_fields = $o->meta('required_fields');
		if (!empty($_SESSION['watercraft_input_data']['errors']))
		{
			foreach ($_SESSION['watercraft_input_data']['errors'] as $key => $value)
			{
				$vars[$key.'_error'] = '';
				if (isset($required_fields[$key]['required']) && empty($value))
				{
					$vars[$key.'_error'] .= $this->parse('REQUIRED_ERROR');
				}
				if (isset($required_fields[$key]['is_numeric']) && !is_numeric($value))
				{
					$vars[$key.'_error'] .= $this->parse('NUMERIC_ERROR');
				}
				
			}
		}

		if($pages[$page]["overview"] && $watercraft_obj)
		{
			$inst = get_instance(CL_WATERCRAFT);
			$inst->init(array(
				"tpldir" => "applications/watercraft_management/watercraft_add",
				"clid" => CL_WATERCRAFT
			));
			$vars["page"] = $inst->show(array(
				"id" => $watercraft_obj->id(),
			));
		}

		// draw elements:
		classload('cfg/htmlclient');
		$wc_inst = get_instance(CL_WATERCRAFT);
		foreach ($elements as $name => $prop)
		{
			if ($this->template_has_var($name))
			{
				if (!empty($watercraft_obj))
				{
					switch($name)
					{
						case "sail_table":
							$prop["value"] = $watercraft_obj->meta($name);
							break;
						default:
							$prop['value'] = $prop['selected'] = $watercraft_obj->prop($name);
							break;
					}
				}
				// ugly quick hack for sail_table property by taiu
				if($name == "sail_table")
				{
					$inst = get_instance(CL_WATERCRAFT);
					classload("vcl/table");
					$prop["vcl_inst"] = new aw_table();
					$inst->_get_sail_table(array(
						"obj_inst" => $watercraft_obj,
						"prop" => &$prop,
					));
					$vars[$name] = $prop["vcl_inst"]->draw();
				}
				else
				{
					if($watercraft_obj && $this->can("view", $watercraft_obj->prop("seller")))
					{
						$p = obj($watercraft_obj->prop("seller"));
					}
					else
					{
						$p = get_current_person();
					}
					if($name == "contact_name" && !strlen($prop["value"]))
					{
						$prop["value"] = $p->name();
					}
					if($name == "contact_phone" && !strlen($prop["value"]))
					{
						$prop["value"] = $p->prop("phone.name");
					}
					if($name == "contact_email" && !strlen($prop["value"]))
					{
						$prop["value"] = $p->prop("email.mail");
					}
					$vars[$name] = htmlclient::draw_element($prop);
				}
			}
		}

		// generate uploaded images list
		if ($this->template_has_var('UPLOADED_IMAGES'))
		{
			if (!empty($watercraft_obj))
			{
				$images = new object_list(array(
					'parent' => $watercraft_obj->id(),
					'class_id' => CL_IMAGE
				));

				$image_inst = get_instance('image');
				$images_str = '';
				foreach ($images->arr() as $image_oid => $image_obj)
				{
					$d = $image_inst->get_image_by_id($image_oid);
					
					$fl = $image_obj->prop("file");
					if(!empty($fl))
					{
						// rewrite $fl to be correct if site moved
						$fl = basename($fl);
						$fl = $this->cfg["site_basedir"]."/files/".$fl{0}."/".$fl;
						$sz = @getimagesize($fl);
						$sm_w = $sz[0];
						$sm_h = $sz[1];
					}
					
					$fl = $image_obj->prop("file2");
					if(!empty($fl))
					{
						// rewrite $fl to be correct if site moved
						$fl = basename($fl);
						$fl = $this->cfg["site_basedir"]."/files/".$fl{0}."/".$fl;
						$sz = @getimagesize($fl);
						$bg_w = $sz[0];
						$bg_h = $sz[1];
					}
						
					$this->vars(array(
						'image_url' => $d["url"],
						'image_name' => $image_obj->name(),
						//'image_big_url' => $d["big_url"]?$d["big_url"]:$d["url"],
						'image_big_url' => $this->mk_my_orb("show_big", array("id" => $image_oid), CL_IMAGE),
						'image_id' => $image_oid,
						'delete_element_name' => "remove_img[".$image_oid."]",
						'image_width' => $sm_w,
						'image_height' => $sm_h,
						'big_image_width' => $d["big_url"]?$bg_w:$sm_w,
						'big_image_height' => $d["big_url"]?$bg_h:$sm_h,
					));
					$images_str .= $this->parse('UPLOADED_IMAGE');
				}
				$this->vars(array(
					'UPLOADED_IMAGE' => $images_str
				));

				$vars['UPLOADED_IMAGES']= $this->parse('UPLOADED_IMAGES');
			}
		}

		$vars['watercraft_id'] = $watercraft_obj?$watercraft_obj->id():0;
		$vars['reforb'] = $this->mk_reforb('submit_data', array(
			'section' => aw_global_get('section'),
			'return_url' => post_ru(),
			'id' => $arr['id'],
			'page' => $page
		));

		$this->vars($vars);

		exit_function('watercraft_add::show');
		return $this->parse();
	}

        /** submit_data
                @attrib name=submit_data 
                @param id required type=int acl=view
                @param rel_id required type=int 
		@param redir_to optional type=string
		@param keep_obj_on_cancel type=bool default=false
        **/
	function submit_data($arr)
	{

		if (isset($arr['cancel']))
		{
			$arr["keep_obj_on_cancel"] = isset($arr["keep_obj_on_cancel"])?$arr["keep_obj_on_cancel"]:false;
			if ($this->can('view', $_SESSION['watercraft_input_data']['watercraft_id']) && !$arr["keep_obj_on_cancel"])
			{
				$watercraft_obj = new object($_SESSION['watercraft_input_data']['watercraft_id']);
				$watercraft_obj->delete(true);
			}
			unset($_SESSION['watercraft_input_data']);
			if(strlen($arr["redir_to"]) && substr($arr["redir_to"], 0, 4) == "http")
			{
				return $arr["redir_to"];
			}
			return aw_ini_get('baseurl').'/'.aw_global_get('section');
		}

		// watercraft_add object
		$o = new object($arr['id']);

		// get the required fields
		$required_fields = $o->meta('required_fields');

		$return_url = $arr['return_url'];
		unset($arr['return_url']);

		$remove_imgs = $arr["remove_img"];
		unset($arr["remove_img"]);
		foreach($remove_imgs as $img => $flag)
		{
			if($this->can("view", $img))
			{
				$o = obj($img);
				if($o->class_id() == CL_IMAGE)
				{
					$o->delete();
				}
			}
		}

		// check for errors
		$_SESSION['watercraft_input_data']['errors'] = array();
		foreach ($arr as $key => $value)
		{
			if (empty($value) && isset($required_fields[$key]['required']))
			{
				$_SESSION['watercraft_input_data']['errors'][$key] = $value;
			}
			if (isset($required_field[$key]['is_numeric']) && !is_numeric($value))
			{
				$_SESSION['watercraft_input_data']['errors'][$key] = $value;
			}
		}

		// if we got any errors (required field is not filled) then redirect the user 
		// back to the page where errors occurred
		if (!empty($_SESSION['watercraft_input_data']['errors']))
		{
			//return $return_url;
			// i made a change in here because, data needed to be saved wheater any errors occur or not
			// i also added err_url to two places below: taiu
			$err_url = $return_url;
		}

		$page = $arr['page'];

		// remember which pages are visited and saved:
		$_SESSION['watercraft_input_data']['saved_pages'][$page] = $page;

		// if the next button is pressed
		if (isset($arr['next']))
		{
			$page = $arr['page'] + 1;
		}
		// if the prev button is pressed
		if (isset($arr['prev']))
		{
			$page = $arr['page'] - 1;
		}
		

		$return_url = aw_url_change_var('page', $page, $return_url);
		$return_url = aw_url_change_var('watercraft_id', NULL, $return_url);

		// save object when everything is ok
		if (empty($_SESSION['watercraft_input_data']['watercraft_id']))
		{
			$watercraft_management_oid = $o->prop('watercraft_management');
			if (!$this->can('view', $watercraft_management_oid))
			{
				return $err_url ? $err_url : $return_url;
			}
			$watercraft_management_obj = new object($watercraft_management_oid);
			$watercraft_obj = new object();
			$watercraft_obj->set_class_id(CL_WATERCRAFT);
			$watercraft_obj->set_parent($watercraft_management_obj->prop('data'));
			$watercraft_obj->set_meta('added_from_section', aw_global_get('section'));
			$watercraft_obj->set_prop('watercraft_type', $o->prop('watercraft_type'));
		}
		else
		{
			$watercraft_obj = new object($_SESSION['watercraft_input_data']['watercraft_id']);
			$watercraft_obj->set_name($watercraft_obj->prop('brand') . ' - ' . $watercraft_obj->prop('engine_model'));
		}

		// here i can upload/save images
		if (!empty($_FILES))
		{
			$image_inst = get_instance('image');
			foreach ($_FILES as $field_name => $image_data)
			{
				if ($image_data['error'] == 0)
				{
					$image = $image_inst->add_upload_image($field_name, $watercraft_obj->id(), 0, true, true);
					$tmp = array(
						"id" => $image["id"],
						"file" => "file",
						"width" => 120,
					);
					$image_inst->resize_picture($tmp);
					$tmp["file"] = "file2";
					$tmp["width"] = "800";
					$image_inst->resize_picture($tmp);
				}
			}
		}

		// here i set the visible checkbox to zero if it isn't selected. the thing is, that html doesn't return info about checkbox when it isn't checked. so, one can not uncheck it .. cool
		// actually, maybe better approach here would be this: take object properties, take values from $arr and set new values.. or smth. this would also eliminate the possibility of wrong propname
		// taiu
		if(empty($arr["visible"]))
		{
			$arr["visible"] = 0;
		}
		$comma_replace = array(
			"length",
			"width",
			"height",
			"weight",
			"draught",
		);
		$just_published = false;

		// so, here i should have an watercraft_obj, so set the properties:
		foreach ($arr as $prop_name => $prop_value)
		{
			if($prop_name == "visible" AND $prop_value == 1 AND $watercraft_obj->prop("visible") == 0)
			{
				$just_published = true;
			}
			// another really ugly hack for sail_table property
			if($prop_name == "sail_table")
			{
				$watercraft_obj->set_meta($prop_name, $prop_value);
			}
			elseif ($watercraft_obj->is_property($prop_name))
			{
				$prop_value = in_array($prop_name, $comma_replace)?str_replace(",", ".", $prop_value):$prop_value;
				$watercraft_obj->set_prop($prop_name, $prop_value);
			}
		}

		$page_keys = array_keys($o->meta('pages'));
		$saved_pages = $_SESSION['watercraft_input_data']['saved_pages'];
		$pages_diff = array_diff($page_keys, $saved_pages);
		// so, if the diff is empty, then all the pages have been saved
		if (empty($pages_diff) && isset($arr['send']))
		{
			$watercraft_obj->set_status(STAT_ACTIVE);
			$watercraft_obj->set_name($watercraft_obj->prop('brand') . ' - ' . $watercraft_obj->prop('engine_model'));
			$watercraft_obj->save();
			unset($_SESSION['watercraft_input_data']);
			$return_url = aw_ini_get('baseurl').'/'.aw_global_get('section');
		}
		else
		{
			$_SESSION['watercraft_input_data']['watercraft_id'] = $watercraft_obj->save();
		}

		if($just_published && !$err_url)
		{
			return $this->submit_data(array(
				"cancel" => true,
				"keep_obj_on_cancel" => true,
				"redir_to" => $o->prop("redir_after_publish"),
			));
		}
		return $err_url ? $err_url : $return_url;
	}

	// draw's pages and returns it as string
	function draw_pages($arr)
	{

		$saved_pages = $arr['saved_pages'];
		$pages = (array)$arr['pages'];
		$page = $arr['page'];

		$this->read_template('pages.tpl');

		$pages_str = '';
		foreach ($pages as $key => $value)
		{
			$this->vars(array(
				'title' => $value['title'],
				'url' => aw_url_change_var('page', $key)
			));
			if ($page == $key)
			{
				$pages_str .= $this->parse('PAGE_ACT');
			}
			else
			if ($page > $key || isset($saved_pages[$key]))
			{
				$pages_str .= $this->parse('PAGE');
			}
			else
			{
				$pages_str .= $this->parse('PAGE_DISABLED');
			}
		}

		$this->vars(array(
			'PAGE' => $pages_str
		));
		return $this->parse();
	}

	function get_watercraft_properties($arr)
	{
		$type = $arr['type'];

		$o = new object();
		$o->set_class_id(CL_WATERCRAFT);
		$o->set_prop('watercraft_type', $type);

		$props = $o->get_property_list();
		$elements = array();
		foreach ($props as $name => $prop)
		{
			// let the watercraft class handle which properties should be shown:
			$prop_retval = $this->watercraft_inst->get_property(array(
				'prop' => &$prop,
				'obj_inst' => $o,
			));
			$tab_retval = $this->watercraft_inst->callback_mod_tab(array(
				'id' => $prop['group'],
				'obj_inst' => $o
			));

			if ($prop_retval == PROP_OK && $tab_retval === true)
			{
				$elements[$name] = $prop;
			}
		}
		return $elements;
	}
	/** Generate a list of watercraft objects added by user 
		
		@attrib name=my_watercraft_list is_public="1" caption="Minu vees&otilde;idukid"

	**/
	function my_watercraft_list($arr)
	{
		$this->read_template('list.tpl');

		$uid = aw_global_get('uid');

		$items = new object_list(array(
			'class_id' => CL_WATERCRAFT,
			'createdby' => $uid,
		));
		/*
		foreach ($list->arr() as $o)
		{
			$watercraft_name = $o->name();
			$this->vars(array(
				'name' => ( empty($watercraft_name) ) ? t('(Nimetu)') : $watercraft_name,
				'change_url' => aw_ini_get('baseurl').'/'.$o->meta('added_from_section').'?watercraft_id='.$o->id()
			));

			$result .= $this->parse('ITEM'); 
		}

		$this->vars(array(
			'ITEM' => $result
		));
		*/
		
		//$inst = get_instance(CL_WATERCRAFT_SEARCH);
		//$arr["prepared_list"] = $ol;
		//return $inst->show($arr);

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
//		$watercraft_property_list = array();
		$watercraft_inst = get_instance(CL_WATERCRAFT);
		foreach ($items->arr() as $item_id => $item)
		{
			$properties = array();
			foreach ($item->properties() as $name => $value)
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

			$image_inst = get_instance(CL_IMAGE);
			$images_count = count($images_lut[$item_id]);
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
				/*
				'watercraft_view_url' => aw_url_change_var(array(
					'section' => "",
					'class' => "",
					'action' => "",
					'watercraft_id' => $item_id,
					'return_url' => (!empty($_GET['return_url'])) ? $_GET['return_url'] : get_ru()
				)),
				*/
				'watercraft_edit_url' => aw_ini_get('baseurl').'/'.$item->meta('added_from_section').'?watercraft_id='.$item->id(),
				'watercraft_images_count' => $images_count,
				'WATERCRAFT_IMAGE' => $image_str
				) + $properties
			);
			
			$items_str .= $this->parse('SEARCH_RESULT_ITEM');
		}

		$this->vars(array(
			'SEARCH_RESULT_ITEM' => $items_str
		));

		$search_results = $this->parse('SEARCH_RESULTS');

		$this->vars(array(
			'SEARCH_FORM_BOX' => $search_form_str,
			'SEARCH_RESULTS' => $search_results,
		));

		return $this->parse();
	}

	function do_db_upgrade($table, $field, $query, $error)
	{

		if (empty($field))
		{
			// db table doesn't exist, so lets create it:
			$this->db_query('CREATE TABLE '.$table.' (
				oid INT PRIMARY KEY NOT NULL,
				
				watercraft_type int,
				watercraft_management int
			)');
			return true;
		}

		switch ($field)
		{
			case 'redir_after_publish':
				$this->db_add_col($table, array(
					"name" => $field,
					"type" => "varchar(255)",
				));
				return true;
			case 'watercraft_type':
			case 'watercraft_management':
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
