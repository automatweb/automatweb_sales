<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/crm/crm_company_webview.aw,v 1.68 2009/03/12 11:51:28 instrumental Exp $
// crm_company_webview.aw - Organisatsioonid veebis 
/*

@classinfo syslog_type=ST_CRM_COMPANY_WEBVIEW relationmgr=yes no_comment=1 no_status=1 prop_cb=1

@default table=objects
@default group=general

@property company type=relpicker reltype=RELTYPE_COMPANY automatic=1 field=meta method=serialize
@caption Ettev&otilde;te
@comment Kui valitud, n&auml;itab vaid seda organisatsiooni.


@property show_title type=checkbox field=flags ch_value=32 method=bitmask
@caption Kuva pealkirja

@property crm_db type=relpicker reltype=RELTYPE_CRM_DB automatic=1 field=meta method=serialize
@caption Andmebaas, mille organisatsioone kuvatakse

@property limit_sector type=popup_search style=relpicker reltype=RELTYPE_LIMIT_SECTOR clid=CL_CRM_SECTOR field=meta method=serialize
@caption Tegevusala piirang
@comment Kui otsitud on mitu tegevusala, kuvatakse nende koigi firmasid, va. juhul, kui neist yks on valja valitud.

@property limit_city type=relpicker reltype=RELTYPE_LIMIT_CITY automatic=1 field=meta method=serialize
@caption Linna piirang

@property limit_county type=relpicker reltype=RELTYPE_LIMIT_COUNTY automatic=1 field=meta method=serialize
@caption Maakonna piirang

@property template type=select field=meta method=serialize
@caption Template

@property tabs type=table store=no
@caption Kaardid


@property ord1 type=select field=meta method=serialize
@caption J&auml;rjestamisprintsiip 1

@property ord2 type=select field=meta method=serialize
@caption J&auml;rjestamisprintsiip 2

@property ord3 type=select field=meta method=serialize
@caption J&auml;rjestamisprintsiip 3

@property clickable type=checkbox field=flags ch_value=16 method=bitmask
@caption Organistatsioonide nimed on klikitavad

@property field type=select field=meta method=serialize
@caption Tegevusala

@property only_active type=checkbox ch_value=1 field=meta method=serialize
@caption Ainult aktiivsed

@default group=transl
	
	@property transl type=callback callback=callback_get_transl
	@caption T&otilde;lgi

@groupinfo transl caption=T&otilde;lgi

@reltype LIMIT_SECTOR value=1 clid=CL_CRM_SECTOR
@caption Tegevusala piirang

@reltype LIMIT_CITY value=2 clid=CL_CRM_CITY
@caption Linna piirang

@reltype LIMIT_COUNTY value=3 clid=CL_CRM_COUNTY
@caption Maakonna piirang

@reltype CRM_DB value=4 clid=CL_CRM_DB
@caption Organisatsioonide andmebaas

@reltype COMPANY value=4 clid=CL_CRM_COMPANY
@caption Organisatsioon

*/

class crm_company_webview extends class_base
{
	function crm_company_webview()
	{
		// change this to the folder under the templates folder, where this classes templates will be, 
		// if they exist at all. Or delete it, if this class does not use templates
		$this->init(array(
			"tpldir" => "applications/crm/crm_company_webview",
			"clid" => CL_CRM_COMPANY_WEBVIEW
		));

		$this->trans_props = array(
			"name"
		);

		lc_site_load("crm_company_webview", $this);
	}

	//////
	// class_base classes usually need those, uncomment them if you want to use them
	function get_property($arr)
	{
		$prop = &$arr["prop"];

		if($prop["name"] != "company" && $prop["name"] != "name" && $prop["name"] != "template" && $prop["name"] != "tabs")
		{
			return PROP_IGNORE;
		}

		$retval = PROP_OK;
		switch($prop["name"])
		{
			//-- get_property --//
			case 'limit_city':
			case 'limit_county':
				if (!empty($prop['value']))
				{
					$excl_name = $prop['name'].'_excl';
					$val = $arr['obj_inst']->meta($excl_name);
					$prop['post_append_text'] = html::checkbox(array(
						'name' => $prop['name'].'_excl',
						'value' => 1,
						'caption' => t("V&auml;listav") .' / ',
						'checked' => $val,
					)) . $prop['post_append_text'];
				}
			break;
			case 'template':
				$inst = get_instance(CL_CRM_COMPANY_WEBVIEW);
				$sys_tpldir = $inst->adm_template_dir;
				$site_tpldir = $inst->site_template_dir;
			//	$prop['options'] = array('default.tpl' => t("Vaikimisi"));
				foreach (glob($site_tpldir.'/*.tpl') as $file)
				{
					$base = basename($file);
					$prop['options'][$base] = $base;
				}
			break;
			case 'ord1':
			case 'ord2':
			case 'ord3':
				$options = array(
					'jrk' => t("J&auml;rjekorranr"),
					'name' => t("Nimi"),
					'county' => t("Maakond"),
					'city' => t("Linn"),
				);
				$prop['options'] = $options;
			break;
			case 'field':
				$clss = aw_ini_get("classes");
				$prop['options'][0] = t("K&otilde;ik");
				foreach ($clss as $clid => $inf)
				{
					if (substr($inf['def'], 0, 13) == 'CL_CRM_FIELD_')
					{
						$prop['options'][$clid] = $inf['name'];
					}
				}
				// Instead of:
				/*
				// We find out possible classes from CRM_COMPANY's FIELD reltype classes
				$cfgu = get_instance("cfg/cfgutils");
				$cfgu->load_properties(array(
					'clid' => CL_CRM_COMPANY,
				));
				$cmp_reltypes = $cfgu->get_relinfo();
				$classes = $cmp_reltypes['RELTYPE_FIELD']['clid'];
				$prop['options'][0] = t('K&otilde;ik');
				foreach ($classes as $i => $class)
				{
					$prop['options'][$class] = $clss[$class]['name'];
				}
				*/
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
			case "transl":
				$this->trans_save($arr, $this->trans_props);
				break;

			//-- set_property --//
			case 'limit_city':
			case 'limit_county':
				$excl_name = $prop['name'].'_excl';
				$val = empty($arr['request'][$excl_name]) ? 0 : 1;
				$arr['obj_inst']->set_meta($excl_name, $val);
			break;
		}
		return $retval;
	}	

	////
	// !this will be called if the object is put in a document by an alias and the document is being shown
	// parameters
	//    alias - array of alias data, the important bit is $alias[target] which is the id of the object to show
	function parse_alias($arr = array())
	{
		return $this->show(array("id" => $arr["alias"]["target"]));
	}

	function parse_section($section)
	{
		$section_vars = array(
			"section_name" => $section->name()
		);
		$this->vars($section_vars);
		foreach($section_vars as $var => $val)
		{
			if(strlen($val) > 1)
			{
				$this->vars(array("HAS_".strtoupper($var) => $this->parse("HAS_".strtoupper($var))));
			}
			else
			{
				$this->vars(array("HAS_".strtoupper($var) => ""));
			}
		}

		$workers = $section->get_workers();
		$w_sub = "";
		foreach($workers->arr() as $worker)
		{
			$worker_vars = array(
			//	"worker_phone" => $worker->phones(),
				"section_worker_name" => $worker->name(),
				"section_worker_work_phone" => $worker->get_phone(null,null,"work"),
				"section_worker_mobile_phone" => $worker->get_phone(null,null,"mobile"),
				"section_worker_fax" => $worker->get_phone(null,null,"fax"),
				"section_worker_email" => $worker->get_mail(),
				"section_worker_profession" => reset($worker->get_profession_names()),
			);
			$this->vars($worker_vars);
			foreach($worker_vars as $var => $val)
			{
				if(strlen($val) > 1)
				{
					$this->vars(
						array(
						"HAS_".strtoupper($var) => $this->parse("HAS_".strtoupper($var))
						));
				}
				else
				{
					$this->vars(array("HAS_".strtoupper($var) => ""));
				}
			}
			$w_sub.= $this->parse("SECTION_WORKERS");
		}
		$this->vars(array("SECTION_WORKERS" => $w_sub));

/*------------- avamisajad ------------*/

		$oinst = new openhours();
		$o_sub = "";

		foreach($section->connections_from(array("type" => "RELTYPE_OPENHOURS")) as $c)
		{
			$oh = $c->to();
			$ohdata = $oh->meta('openhours');
			$oh_vars = array(
				"soh_name" => $oh->name(),
			);
			$this->vars($oh_vars);
			
			$oh_rows = "";

			if($ohdata && is_array($ohdata) && sizeof($ohdata))
			{
				foreach($ohdata as $ohrow)
				{
					$ohrow["day_short"] = $oinst->days_short[$ohrow["day1"]];
					$ohrow["day2_short"] = $oinst->days_short[$ohrow["day2"]];
					$this->vars($ohrow);
					$this->vars(array("SECTION_HAS_DAY2" =>  $ohrow["day2"] ? $this->parse("SECTION_HAS_DAY2"):""));
					$oh_rows.= $this->parse("SECTION_OPEN_HOURS_ROW");
				}
			}
			$this->vars(array("SECTION_OPEN_HOURS_ROW" => $oh_rows));
			$o_sub.= $this->parse("SECTION_OPEN_HOURS");
		}
		$this->vars(array("SECTION_OPEN_HOURS" => $o_sub));


		return $this->parse("SECTIONS");
	}


	function parse_company($o)
	{

		$company = obj($o->prop("company"));
		$this->read_template($o->prop("template"));
		$this->vars($company->properties());

		$tabs = $o->meta("tabs");
		$ord = $o->meta("ord");
		$hide = $o->meta("hide");


		$vars = array();
		$vars["address"] = $company->get_address_string();
		$vars["email"] = $company->get_mail();
		$vars["phone"] = join(", " , $company->get_phones());

		foreach($tabs as $key => $val)
		{
			$vars["caption".$key] = $val;
		}
		foreach($hide as $key => $val)
		{
			$vars["class".$key] = "hide";
		}
//		$vars["workers"] = join(", ", $workers->names());
		$this->vars($vars);
/*----------------- aadress ------------------------*/
		
		$address_vars = array();
		$o = $company->get_first_obj_by_reltype("RELTYPE_ADDRESS_ALT");
		if($o)
		{
			foreach($o->properties() as $var => $val)
			{
				$address_vars["address_".$var] = $val;
			}

			if($o->prop("coord_x") && $o->prop("coord_y"))
			{
 				$address_vars["google_map_url"] = 'https://maps.google.com/maps?f=q&amp;source=s_q&amp;hl=en&amp;q='.urlencode($o->prop("street")).'+'.$o->prop("house").',+'.urlencode($o->prop("parent.name")).',+Eesti&amp;aq=0&amp;ie=UTF8&amp;hq=&amp;hnear='.urlencode($o->prop("street")).'+'.$o->prop("house").',+'.urlencode($o->prop("parent.name")).',+80042+'.urlencode($o->prop("parent.parent.name")).',+Estonia&amp;t=m&amp;z=13&amp;ll='.$o->prop("coord_y").','.$o->prop("coord_x").'&amp;output=embed';
			}
		}

		$this->vars($address_vars);
		$this->vars(array("GOOGLE_MAP" => empty($address_vars["google_map_url"]) ? "" : $this->parse("GOOGLE_MAP")));


/*---------------- t88tajad ------------------------*/
		$workers = $company->get_workers();
		$w_sub = "";
		foreach($workers->arr() as $worker)
		{
			$worker_vars = array(
			//	"worker_phone" => $worker->phones(),
				"worker_name" => $worker->name(),
				"worker_work_phone" => $worker->get_phone(null,null,"work"),
				"worker_mobile_phone" => $worker->get_phone(null,null,"mobile"),
				"worker_fax" => $worker->get_phone(null,null,"fax"),
				"worker_email" => $worker->get_mail(),
				"worker_profession" => reset($worker->get_profession_names()),
			);
		//	arr($worker_vars);
			$this->vars($worker_vars);
			foreach($worker_vars as $var => $val)
			{
				if(strlen($val) > 1)
				{
					$this->vars(
						array(
						"HAS_".strtoupper($var) => $this->parse("HAS_".strtoupper($var))
						));
				}
				else
				{
					$this->vars(array("HAS_".strtoupper($var) => ""));
				}
			}
			$w_sub.= $this->parse("WORKERS");
		}
		$this->vars(array("WORKERS" => $w_sub));

/*-------------- osakonnad ------------*/
		$sections = $company->get_sections();
		$s_sub = "";
		$cnt = 1;
		foreach($sections->arr() as $section)
		{
			$section_vars = array(
				"section_name" => $section->name(),
				"section_count" => $cnt,
			);
			$this->vars($section_vars);
			$cnt++;
			$s_sub.= $this->parse_section($section);
		}
		$this->vars(array("SECTIONS" => $s_sub));

/*------------- avamisajad ------------*/

		$oinst = new openhours();
		$o_sub = "";

		foreach($company->connections_from(array("type" => "RELTYPE_OPENHOURS")) as $c)
		{
			$oh = $c->to();
			$ohdata = $oh->meta('openhours');
			$oh_vars = array(
				"oh_name" => $oh->name(),
			);
			$this->vars($oh_vars);
			
			$oh_rows = "";

			if($ohdata && is_array($ohdata) && sizeof($ohdata))
			{
				foreach($ohdata as $ohrow)
				{
					$ohrow["day_short"] = $oinst->days_short[$ohrow["day1"]];
					$ohrow["day2_short"] = $oinst->days_short[$ohrow["day2"]];
					$this->vars($ohrow);
					$this->vars(array("HAS_DAY2" =>  $ohrow["day2"] ? $this->parse("HAS_DAY2"):""));
					$oh_rows.= $this->parse("OPEN_HOURS_ROW");
				}
			}
			$this->vars(array("OPEN_HOURS_ROW" => $oh_rows));
			$o_sub.= $this->parse("OPEN_HOURS");
		}
		$this->vars(array("OPEN_HOURS" => $o_sub));

		$content = "";
		$toolbar = "";

		$this->order = $ord;
		uksort($tabs, array($this, "sort_order"));

		foreach($tabs as $key => $val)
		{
			if(!empty($hide["key"]))
			{
				continue;
			}

			$toolbar.= $this->parse("TOOLBAR".$key);
			$content.= $this->parse("CONTENT".$key);
		}

		$this->vars(array("content" => $content));
		$this->vars(array("toolbar" => $toolbar));

		return $this->parse();
	}

	function sort_order($a, $b)
	{
		$ao = empty($this->order[$a]) ? 0 : $this->order[$a];
		$bo = empty($this->order[$b]) ? 0 : $this->order[$b];
		return $ao - $bo;
	}



	function _get_tabs($arr)
	{
		if(!$arr["obj_inst"]->prop("template"))
		{
			return PROP_IGNORE;
		}
		$this->read_site_template($arr["obj_inst"]->prop("template"));
		$tabs = $arr["obj_inst"]->meta("tabs");
		$ord = $arr["obj_inst"]->meta("ord");
		$hide = $arr["obj_inst"]->meta("hide");
		$t = $arr["prop"]["vcl_inst"];

		$possible_tabs = array();
		$x = 0;

		while($x < 100)
		{
			if($this->is_template("TAB".$x))
			{
				$possible_tabs[$x] =  $tabs[$x] ? $tabs[$x] : $this->parse("TAB".$x);
			}
			else
			{
				break;
			}
			$x++;
		}

		$this->order = $ord;
		uksort($possible_tabs, array($this, "sort_order"));

		foreach($possible_tabs as $key => $val)
		{
			$t->define_data(array(
				"caption" => html::textbox(array(
					"name" => "tabs[".$key."]",
					"value" => $val,
				)),
				"hide" => html::checkbox(array(
					"name" => "hide[".$key."]",
					"value" => 1,
					"checked" => $hide[$key],
				)),
				"ord" => html::textbox(array(
					"name" => "ord[".$key."]",
					"value" => $ord[$key] ? $ord[$key] : "",
					"size" => 3
				)),
			));
		}

		if($x)
		{
			$t->define_field(array(
				"name" => "caption",
				"caption" => t("Nimi")
			));

			$t->define_field(array(
				"name" => "hide",
				"caption" => t("Peida")
			));
			$t->define_field(array(
				"name" => "ord",
				"caption" => t("Jrk")
			));
		}
	}

	function _set_tabs($arr)
	{
		$arr["obj_inst"]->set_meta("tabs" , $arr["request"]["tabs"]);
		$arr["obj_inst"]->set_meta("ord" , $arr["request"]["ord"]);
		$arr["obj_inst"]->set_meta("hide" , $arr["request"]["hide"]);
	}

	////
	// !this shows the object. not strictly necessary, but you'll probably need it, it is used by parse_alias
	function show($arr)
	{
		if (!is_oid($arr['id']) || !$this->can('view', $arr['id']))
		{
			return;
		}
		$o = obj($arr['id']);
		if ($o->class_id() != CL_CRM_COMPANY_WEBVIEW)
		{
			return;
		}

		if(is_oid($o->prop("company")) && $this->can('view', $o->prop("company")))
		{
			return $this->parse_company($o);
		}

		$tmpl = $o->prop('template');
		if (!preg_match('/^[^\\\/]+\.tpl$/', $tmpl))
		{
			$tmpl = "default.tpl";
		}
		
		$this->read_template($tmpl);
		lc_site_load("crm_company_webview", $this);
		$org = ifset($_REQUEST, 'org');
		if (!$this->can('view', $org) || !($c = obj($org)) || $c->class_id() != CL_CRM_COMPANY)
		{
			$org = null;
		}
		$room = $_REQUEST["room"];
		$room = $this->can('view', $room)?$room:false;

	enter_function('crm_company_webview::show');
		if($room)
		{
			$ret = $this->_get_conference_room_html($room);
		}
		elseif (is_null($org))
		{
			// LIST COMPANIES
			$ret = $this->_get_companies_list_html(array('id' => $arr['id']));
			
		}
		else
		{
			// SHOW COMPANY
			$ret = $this->_get_company_show_html(array('list_id' => $arr['id'], 'company_id' => $org));;
		}
	exit_function('crm_company_webview::show');

		return $ret;
		
	}

	// Return html for company display
	function _get_company_show_html ($arr)
	{
		enter_function("crm_company_webview::_get_company_show_html");
		// If request parameter 'l' is set, show only that webview
		if (isset($_REQUEST['l']) && is_oid($_REQUEST['l']) && $arr['list_id'] != $_REQUEST['l'])
		{
			exit_function("crm_company_webview::_get_company_show_html");
			return "";
		}
		$this->sub_merge = 0;
		$org = ifset($arr, 'company_id');
		if (!$this->can('view', $org) || !($c = obj($org)) || $c->class_id() != CL_CRM_COMPANY)
		{
			exit_function("crm_company_webview::_get_company_show_html");
			return "";
		}
		$this->vars(array("company_id" => $org, "modified" => date("d/m/y", $c->modified())));

		$this->add_hit($org);
		
		$webview = obj($arr['list_id']);
		$crm_field = null;
		if (empty($_REQUEST['allfields']))
		{
			$crm_field = $webview->prop('field'); // contains applicable CRM_FIELD_ class ID
		}

		$do_link = $this->can('edit', $c->id());
		$this->vars(array(
			'company_name' => $do_link ? html::href(array(
					'url' => $url . $c->id() . '&l='.$arr['id'],
					'caption' => $c->name()))
					: $c->name(),
			'company_name_url' => $url . $c->id() . '&l=' .$arr['id'],
			'company_name_text' => $c->trans_get_val("name"),
			'company_changeurl' => $this->can('edit', $c->id()) ? html::href(array(
					'caption' => '('.t("Muuda").')',
					'url' => $this->mk_my_orb('change',array(
							'id' => $c->id(),
						),CL_CRM_COMPANY, true),
					))
					: '',
			'address' => $c->prop("contact.name"),
		));
		// All possible line_* values are defined here
		$datafields = array(
			'sectors' => '',
			'address' => 'contact',
			'address_separated' => 'contact',
			'phone' => 'phone_id',
			'openhours' => '',
			'fax' => 'telefax_id',
			'email' => 'email_id',
			'url' => 'url_id',
			'comment' => 'comment',
			'name' => 'name',
			'founded' => 'year_founded',
			'specialoffers' => 'special_offers',
			'specialoffers_long' => 'special_offers',
			'extrafeatures' => '',
			'num_rooms' => '',
			'num_beds' => '',
			'prices' => 'price_txt',
			'description' => 'tegevuse_kirjeldus',
			'type' => '',
			'moreinfo_link' => '',
			'userta1' => '',
			'userta2' => '',
			'userta3' => '',
			'userta4' => '',
			'userta5' => '',
			'logo' => 'logo',
		);
		
		// Name is not obligatory - will go to template as {VAR:key}
		$fieldnames = array(
			'address' => t("Aadress"),
			'address_separated' => t("Aadress"),
			'phone' => t("Tel"),
			'fax' => t("Faks"),
			'openhours' => t("Avatud"),
			'email' => t("E-post"),
			'url' => t("Veebiaadress"),
			'sectors' => t("Tegevusalad"),
			'founded' => t("Asutatud"),
			'specialoffers' => t("Eripakkumised"),
			'specialoffers_long' => t("Eripakkumised"),
			'extrafeatures' => t("Lisav&otilde;imalused"),
			'num_beds' => t("Kohti"),
			'num_rooms' => t("Toad"),
			'prices' => t("Hinnad"),
			'type' => t("T&uuml;&uuml;p"),
			'logo' => t("Logo"),
		);
		$crm_field_titles = array(
			CL_CRM_FIELD_ACCOMMODATION => t("Majutusinfo"),
			CL_CRM_FIELD_FOOD => t("Toitlustusinfo"),
			CL_CRM_FIELD_ENTERTAINMENT => ("Meelelahutusinfo"),
			CL_CRM_FIELD_CONFERENCE_ROOM => t("Konverentsiinfo"),
		);
		$exinf_remap = array(
			'price_level' => array(
				'price_A' => 'A',
				'price_B' => 'B',
				'price_C' => 'C',
				'price_D' => 'D',
				'price_E' => 'E',
			),
			'location' => array(
				'loc_city' => t("Kesklinnas"),
				'loc_outside' => t("V&auml;ljaspool kesklinna"),
 				'loc_country' => t("V&auml;ljaspool linna"),
			),
			'languages' => array(),
			'type' => array( // copied from class/applications/crm/crm_field_accommodation get_property->type
				'tp_hotel' => t("Hotell"),
				'tp_motel' => t("Motell"),
				'tp_guesthouse' => t("K&uuml;lalistemaja"),
				'tp_hostel' => t("Hostel"),
				'tp_camp' => t("Puhkek&uuml;la ja -laager"),
				'tp_wayhouse' => t("Puhkemaja"),
				'tp_apartment' => t("K&uuml;laliskorter"),
				'tp_homestay' => t("Kodumajutus"),
			),
			'national_cuisine' => array(
				'est' => t("Eesti"),
				'rus' => t("Vene"),
				'gru' => t("Gruusia"),
				'chi' => t("Hiina"),
				'ita' => t("Itaalia"),
				'tai' => t("Tai"),
			),
			'caption' => array(
				'phone_id' => t("Tel"),
				'telefax_id' => t("Fax"),
				'email_id' => t("E-post"),
			),
		);
		$langs = aw_ini_get('languages.list');
		foreach ($langs as $lang)
		{
			$exinf_remap['languages'][$lang['acceptlang']] = t($lang['name']);
		}	
		$extrainfo_ignorefields = array('name', 'comment', 'status', 'type', 'price_txt'); // crm_field_ properties which are not displayed automatically if set
		

		$extrainfo = array(); // crm_field_{type} objects in type => array('o'=>obj,'p'=>properties)  array (type is class id) (see reltype FIELD on crm_company)
		$used_fields = $this->v2_name_map;
		classload("crm/crm_company");
		$images_conns = null; // Variable for connections to image objects, if this is set before parsing images, allows overriding image selection
		foreach ($datafields as $item => $mapped)
		{

			$value_array_into_separate_vars = false;
			// Skip parsing for values which are not used anyway
			if (!isset($used_fields['line_'.$item]))
			{
				continue;
			}
			$key = $value = $reltype = $html_value = "";
			switch ($item)
			{
				case 'logo':
					$logoo = $c->get_first_obj_by_reltype("RELTYPE_ORGANISATION_LOGO");
					if ($logoo)
					{
						$img_i = $logoo->instance();
						$value = $img_i->make_img_tag_wl($logoo->id());
					}
					break;

				case "phone":
					$value = $c->prop("phone_id.name");
					break;

				case "fax":
					$value = $c->prop("telefax_id.name");
					break;

				case 'phones': // Display all phone numbers, not selected one
					$reltype = 'RELTYPE_PHONE';
				case 'faxes':
					$reltype = empty($reltype) ? 'RELTYPE_TELEFAX' : $reltype;
				
					$conns = $c->connections_from(array(
						'type' => $reltype,
					));
					$ph_inst = get_instance(CL_CRM_PHONE);
					foreach ($conns as $conn)
					{
						$thing = $conn->conn['to'];
						if ($this->can('view', $thing))
						{
							$value[] = $ph_inst->show(array(
								'oid' => $thing,
							));
						}
					}
				break;

				case 'sectors':
					$value = array();
					if(count($c->prop("pohitegevus")) > 0)
					{
						$ol = new object_list(array(
							"class_id" => CL_CRM_SECTOR,
							"oid" => $c->prop("pohitegevus"),
							"lang_id" => array(),
							"site_id" => array(),
						));
						foreach($ol->names() as $oid => $name)
						{
							if ($_GET["action"] == "show_co")
							{
								$value[] = html::href(array(
									"caption" => $name,
									"class" => "ccwSectLink",
									"url" => $this->mk_my_orb("show_sect", array(
										"section" => $oid,
										"wv" => $_GET["wv"]
									), "crm_company_webview")
								));
							}
							else
							{
								$value[] = $name;
							}
						}
					}
				break;
				case 'openhours':
					$inst = get_instance(CL_OPENHOURS);
					$o_item = $c->get_first_obj_by_reltype('RELTYPE_OPENHOURS');
					if (!is_object($o_item))
					{
						continue;
					}
					$value = $inst->show(array(
						'id' => $o_item->id(),
					));
				break;
				case 'moreinfo_link':
					$conns = $c->connections_from(array(
						'type' => 'RELTYPE_DESCRIPTION',
						'to.lang_id' => aw_global_get('lang_id'),
					));
					if (count($conns))
					{
						$conn = array_shift($conns);
						$value = $conn->to();
						//$value = $c->get_first_obj_by_reltype('RELTYPE_DESCRIPTION');
						if ($value)
						{
							$value = html::href(array(
								'caption' => t("Veel lisainfot"),
								'url' => '/'.$value->id(),
							));
						}
					}
				break;
				case 'name':
				case 'comment':
				case 'description':
					$value = nl2br(str_replace(array("'", "\""), array("&#39;", "&quot;"), $c->trans_get_val($mapped)));
				break;
				case 'founded':
					if ($c->prop($mapped) > 0)
					{
						$value = date('d-m-Y', $c->prop($mapped));
					}
				break;
				case 'specialoffers':
				case 'specialoffers_long':
					$conns = $c->connections_from(array(
						'type' => 'RELTYPE_SPECIAL_OFFERS',
					));
					$url = '/specialoffers/?offer=';
					$instance = get_instance(CL_CRM_SPECIAL_OFFER);
					foreach ($conns as $con)
					{
						$offer = $con->to();
						if ($item == 'specialoffers_long')
						{
							$sp = get_instance(CL_CRM_SPECIAL_OFFER);
							
							$value[] = $sp->show(array(
								'short' => true,
								'id' => $offer->id(),
							));
						}
						else
						{
							$value[] = html::href(array(
								'url' => $url.$offer->id(),
								'caption' => $offer->name(),
							));
						}
					}
					$value = implode(', ', $value);
				break;
				case 'extrafeatures':
					$sm = $this->sub_merge;
					$this->sub_merge = 0;
	
					// We find out possible classes by naming pattern
					$classes = array();
					$clss = aw_ini_get("classes");
					foreach ($clss as $clid => $inf)
					{
						if (substr($inf['def'], 0, 13) == 'CL_CRM_FIELD_')
						{
							$classes[] = $clid;
						}
					}
					$has_otherfields = false;
					foreach ($classes as $type)
					{
						// If field was chosen in webview properties, show only information for that field
						if (!empty($crm_field))
						{
							if ($type != $crm_field)
							{
								$has_otherfields = true;
								continue;
							}
						}
						// checkboxes in crm_field_accommodation
 						if (!array_key_exists($type, $extrainfo))
						{
							$extrainfo[$type]['o'] = crm_company::find_crm_field_obj(array(
								'oid' => $c->id(),
								'clid' => $type,
							));
							if (is_object($extrainfo[$type]['o']))
							{
								$extrainfo[$type]['p'] = $extrainfo[$type]['o']->properties();
							}
						}
					
						if (is_object($extrainfo[$type]['o']))
						{
							$innervalue = "";
							$pval = $extrainfo[$type]['p']; // Property values
							$pl = $extrainfo[$type]['o']->get_property_list();
							
							// Add phone / fax / email fields first
							foreach (array('phone_id', 'telefax_id', 'email_id') as $name)
							{
								$thisval = "";
								if (!empty($pval[$name]) && is_oid($pval[$name]) && $this->can('view', $pval[$name]))
								{
									$muki = obj($pval[$name]);
									if ($name == 'email_id')
									{
										$thisval = $muki->prop('mail');
										$thisval = html::href(array(
											'url' => 'mailto:'.$thisval,
											'caption' => $thisval,
										));
									}
									else
									{
										$thisval = $muki->name();
									}
									$thisval = ifset($exinf_remap['caption'],$name).': '.$thisval;
								}
								if (!empty($thisval))
								{
 									$this->vars(array(
 										'extraf_name' => $thisval,
									));
									$innervalue .= $this->parse('extraf_value');
								}
							}
							
							
							// Get all checkbox properties and values
							foreach ($pl as $name => $pinf)
							{
								if (in_array($name, $extrainfo_ignorefields))
								{
									continue;
								}
								$thisval = "";
								// types we show: checkbox, textbox, chooser (multiple=1), select 
								if ($pinf['type'] == 'checkbox' && $pval[$name])
								{
									$thisval = t($pinf['caption']);
								}
								else if ($pinf['type'] == 'textbox' && !empty($pval[$name]))
								{
									$thisval = t($pinf['caption']).': '.htmlspecialchars($pval[$name]);
								}
								else if ($pinf['type'] == 'chooser' || $pinf['type'] == 'select')
								{
									// location, languages, price_level, 
									$values = array();
									if (!is_array($pval[$name]))
									{
										$pval[$name] = array($pval[$name]);
									}
									foreach ($pval[$name] as $n => $v)
									{
										if (empty($v))
										{
											continue;
										}
										if (isset($exinf_remap[$name]) && isset($exinf_remap[$name][$v]))
										{
											$values[] = ifset($exinf_remap, $name, $v);
										}
										else
										{
											$values[] = $v;
										}
									}
									if (count($values))
									{
										$thisval = t($pinf['caption']).': '.implode(', ',$values);
									}
								}
								if ($pinf["type"] == "releditor" && $pinf["reltype"] == "RELTYPE_ROOM")
								{
									$conns = $extrainfo[$type]["o"]->connections_from(array(
										"type" => RELTYPE_ROOM,
									));
									$count = 1;
									$header_done = false;
									$odd_even = array("_odd", "_even", "");
									foreach($conns as $cdata)
									{
										$room = obj($cdata->to());
										if(!$room_type_props)
										{
											$props = $room->get_property_list();
											foreach($props as $n => $d)
											{
												if(substr($n, 0, 5) == "type_")
												{
													$room_type_props[$d["name"]] = "";
												}
											}
										}
										$propvals = $room->properties();
										foreach($room_type_props as $n => $v)
										{
											$room_type_props[$n] = $propvals[$n];
										}
										unset($cols, $cols_odd, $cols_even);
										foreach ($room_type_props as $name => $val)
										{
											if(!$cols)
											{
												foreach($odd_even as $odd_even_type)
												{
													$this->vars(array(
														"room_name".$odd_even_type => $room->name(),
														"room_comment".$odd_even_type => $room->prop("description"),
														"room_popurl" => $url = "/".aw_global_get('section')."?room=".$room->id(),
													));
													${"croom_name".$odd_even_type} = $this->parse("croom_name_col".$odd_even_type);
													$this->vars(array(
														"area_value".$odd_even_type => $room->prop("area"),
													));
													${"croom_area".$odd_even_type} = $this->parse("croom_area_col".$odd_even_type);
												}
											}
											foreach($odd_even as $odd_even_type)
											{
												$this->vars(array(
													"col_value".$odd_even_type => ($val < 1)?t("-"):$val,
												));
												${"cols".$odd_even_type} .= $this->parse("croom_table_col".$odd_even_type);
											}

											// for table header
											if(!$header_done)
											{
												$this->vars(array(
													"col_value_header" => $name,
													"col_value_header_title" => $props[$name]["caption"],
													"col_value_header_alt" => $props[$name]["caption"],
												));
												$croom_tab_header .= $this->parse("croom_header_table_col");
											}
										}
										$header_done = true;
										$t="_even";
										foreach($odd_even as $odd_even_type)
										{
											$this->vars(array(
												"croom_table_col".$odd_even_type => ${"cols".$odd_even_type},
												"croom_name_col".$odd_even_type => ${"croom_name".$odd_even_type},
												"croom_area_col".$odd_even_type => ${"croom_area".$odd_even_type},
											));
										}
										$count++;
										if($count % 2)
										{
											$rows_odd_even .= $this->parse("croom_table_row_odd");
										}
										else
										{
											$rows_odd_even .= $this->parse("croom_table_row_even");
										}
										$rows .= $this->parse("croom_table_row");
										
									}

									// table header
									$this->vars(array("room_name_header" => t("Ruum")));
									$name = $this->parse("croom_header_name_col");
									$this->vars(array("area_value_header" => t("pind")));
									$area = $this->parse("croom_header_area_col");
									$this->vars(array(
										"croom_header_name_col" => $name,
										"croom_header_area_col" => $area,
										"croom_header_table_col" => $croom_tab_header,
									));
									$tab_header = $this->parse("croom_table_header");

									$this->vars(array(
										"croom_table_header" => $tab_header,
										"croom_table_row" => $rows_odd_even?$rows_odd_even:$rows, 
									));
									$croom_table = $this->parse("conference_room_table");
								}
								
								if (!empty($thisval))
								{
 									$this->vars(array(
 										'extraf_name' => $thisval,
									));
									$innervalue .= $this->parse('extraf_value');
								}
							}

							
							if (!empty($innervalue) || !empty($croom_table))
							{
								// find if the field object has been set it's own title, too
								$fields_title = $pval['name'];
								if (substr($fields_title, -7, 7) == ' andmed') // has the default value
								{
									$fields_title = "";
								}
								
								$this->vars(array(
									'extraf_title' => ifset($crm_field_titles, $type) . ($fields_title==""?"":(": ".$fields_title)),
									'extraf_value' => $innervalue,
									"conference_room_table" => $croom_table,
								));
								$value .= $this->parse('extrafeatures');
							}	


							// If we're displaying only one field and it has images, we override company's default images
							if (!empty($crm_field))
							{
								$images_conns = $extrainfo[$type]['o']->connections_from(array(
									'type' => 'RELTYPE_IMAGE',
									'to.status' => STAT_ACTIVE,
									'class' => CL_IMAGE,
								));
							}
						}
					}
					
					// If field is limited but there exists info for other fields, give link to show that
					if (!empty($crm_field) && $has_otherfields)
					{
						$thisval = html::href(array(
							'caption' => t('Teised tegevusalad'),
							//'caption' => t('N&auml;ita k&otilde;iki tegevusalasid'),
							'url' => aw_url_change_var('allfields', 1)
						));
						$this->vars(array(
							'extraf_title' => $thisval,
							'extraf_value' => '',
						));
						$value .= $this->parse('extrafeatures');
					}
				
					$this->sub_merge = $sm;
				break;
					// Find the following from appropriate crm_field_ object
				case 'num_rooms':
				case 'num_beds':
				case 'prices':
				case 'type':
					$type = CL_CRM_ACCOMMODATION;

				// case 'whatever':
					if (!array_key_exists($type, $extrainfo))
					{
						$extrainfo[$type]['o'] = crm_company::find_crm_field_obj(array(
							'oid' => $c->id(),
							'clid' => $type,
						));
						if (is_object($extrainfo[$type]['o']))
						{
							$extrainfo[$type]['p'] = $extrainfo[$type]['o']->properties();
						}
					}
					
					if (is_array($extrainfo[$type]['p']))
					{
						$use = $item;
						if (!empty($mapped))
						{
							$use = $mapped;
						}
						if ($item == 'prices' && !empty($extrainfo[$type]['p']['price_level']))
						{
							$value[] = $exinf_remap['price_level'][$extrainfo[$type]['p']['price_level']];
						}
						$value[] = $extrainfo[$type]['p'][$use];
						if ($item == 'type')
						{
							$value[count($value)-1] = $exinf_remap['type'][$value[count($value)-1]];
						}
					}
				break;
				case "userta1":
				case "userta2":
				case "userta3":
				case "userta4":
				case "userta5":
					$value = nl2br($c->trans_get_val($item));
					break;
				case "email":
					$rels = $c->connections_from(array(
						"type" => "RELTYPE_EMAIL",
					));
					foreach($rels as $rel)
					{
						$_t = $rel->to();
						if(!strlen($_t->prop("mail")))
						{
							continue;
						}
						$value[] = html::href(array(
							"url" => "mailto:".$_t->prop("mail"),
							"caption" => $_t->prop("mail"),
						));
					}
					break;
				case "url":
					$rels = $c->connections_from(array(
						"type" => "RELTYPE_URL",
					));
					foreach($rels as $rel)
					{
						$_t = $rel->to();
						$val = $_t->prop("url");
						$val = (substr($val, 0, 7) == "http://")?$val:"http://".$val;
						$value[] = html::href(array(
							"url" => htmlspecialchars($val, ENT_QUOTES),
							"caption" => htmlspecialchars($val, ENT_QUOTES),
							"target" => "_blank",
						));

					}
					break;
				default:
					$oid = $c->prop($mapped);
					if ($this->can("view", $oid) && ($o_item = obj($oid)) && is_object($o_item) && is_numeric($o_item->id()) )
					{
						if ($item == 'address')
						{
							$props = array("aadress", "aadress2", "postiindeks");
							$objs = array("linn", "maakond", "piirkond", "riik");
							$rows = array(
								array("aadress", "postiindeks"),
								array("linn", "aadress2"),
								array("maakond", "piirkond", "riik"),
							);
							$value = "";
							foreach($rows as $row)
							{
								$address_line = "";
								foreach($row as $prop)
								{
									if(strlen($val = $o_item->prop((in_array($prop,$objs)?$prop.".name":$prop))))
									{
										$address_line .= strlen($address_line) > 0 ? ", " : "";
										$address_line .= $val;
									}
								}
								$value .= strlen($address_line) > 0 ? $address_line."<br />" : "";
							}
							/*
							foreach(array_merge($props, $objs) as $prop)
							{
								if(strlen($val = $o_item->prop((in_array($prop,$objs)?$prop.".name":$prop))))
								{
									$value[] = $val;
								}
							}
							*/
							
							/*
							$idx = $o_item->prop('postiindeks');
							$value = $o_item->name();
							if (strlen($idx) && strpos($value, $idx) === FALSE)
							{
								$value .= ", $idx";
							}
							*/
							
							// the proper, templateroaming version:
							//$inst = $o_item->instance();
							//$value = $inst->request_execute($o_item);
						}
						elseif($item == 'address_separated')
						{
							$props = array("aadress", "postiindeks");
							$objs = array("linn", "maakond", "piirkond", "riik");
							foreach(array_merge($props, $objs) as $prop)
							{
								if(strlen($val = $o_item->prop((in_array($prop,$objs)?$prop.".name":$prop))))
								{
									$value[$prop] = $val;
									$value_array_into_separate_vars = true;
								}
							}
						}
						else
						{
							$value = $o_item->name();
						}
					
					}
				break;
			}
			$key = ifset($fieldnames, $item);
			if (is_array($value) && !$value_array_into_separate_vars)
			{
				$value = join(', ', $value);
			}
			elseif(is_array($value) && $value_array_into_separate_vars)
			{
				$this->vars($value);
			}

			if (!empty($value))
			{
				$this->vars(array(
					'key' => $key,
					'value' => $value,
				));
				$this->vars(array('line_'.$item => $this->parse('line_'.$item)));
			}
		}

		// External links
		$value = "";
		foreach($c->connections_from(array("type" => "RELTYPE_EXTERNAL_LINKS")) as $conn)
		{
			$_t = $conn->to();
			$val = $_t->prop("url");
			$val = (substr($val, 0, 7) == "http://")?$val:"http://".$val;
			$this->vars(array(
				"link" => $val,
				"text" => isset($_GET["print"]) && $_GET["print"] ? $val : $_t->name(),
			));
			$value .= $this->parse("external_links");
		}
		$this->vars(array(
			"external_links" => $value,
		));
		if(strlen($value) > 0)
		{
			$this->vars(array(
				"EXTERNAL_LINKS" => $this->parse("EXTERNAL_LINKS"),
			));
		}


		// Images
		$inst_img = get_instance(CL_IMAGE);

		// Logo
		if($this->can("view", $c->logo) && file_exists($c->prop("logo.file")))
		{
			$this->vars(array(
				"imgref" => $inst_img->get_url_by_id($c->logo),
				"imgref_big" => file_exists($c->prop("logo.file2")) ? $inst_img->get_big_url_by_id($c->logo) : $inst_img->get_url_by_id($c->logo),
				"imghtml" => $inst_img->make_img_tag_wl($c->logo),
			));
			$this->vars(array(
				"logo" => $this->parse("logo"),
			));
		}

		// Pictures with reltype RELTYPE_IMAGE
		if (!is_array($images_conns) || !count($images_conns))
		{
			$images_conns = $c->connections_from(array(
				'type' => 'RELTYPE_IMAGE',
				'to.status' => STAT_ACTIVE,
			));
		}	
		$ims = array();
		foreach ($images_conns as $conn)
		{
			$image = $conn->to();
			$ims[$image->id()] = $image->ord();
			$tmp = $inst_img->parse_alias(array(
				'alias' => array(
					'target' => $image->id(),
				),
			));
			$images[] = $tmp['replacement']; // No, replacement is not a logical name in this context. However, it works!
		}
		$images_html = join('<br><br>', $images);

		asort($ims);
		$f_im = reset(array_keys($ims));
		$all_imstr = "";
		if ($f_im)
		{
			$this->vars(array(
				"imgref" => $inst_img->get_url_by_id($f_im),
				"imgref_big" => file_exists(obj($f_im)->prop("file2")) ? $inst_img->get_big_url_by_id($f_im) : $inst_img->get_url_by_id($f_im),
				"imghtml" => $inst_img->make_img_tag_wl($f_im),
			));
			$all_imstr .= $this->parse("all_images");
			$this->vars(array(
				"first_image" => $this->parse("first_image")
			));
		}
		
		$f = true;
		$imstr = "";
		foreach($ims as $im_id => $ord)
		{
			if ($f)
			{
				$f = false;
				continue;
			}
			$this->vars(array(
				"imgref" => $inst_img->get_url_by_id($im_id),
				"imgref_big" => $inst_img->get_big_url_by_id($im_id),
				"imghtml" => $inst_img->make_img_tag_wl($im_id),
			));
			$imstr .= $this->parse("other_images");
			$all_imstr .= $this->parse("all_images");
		}
		$this->vars(array(
			"other_images" => $imstr,
			"all_images" => $all_imstr,
		));
	
		// Rating, show results
		$rate_inst = get_instance(CL_RATE);
	 	$scale_inst = get_instance(CL_RATE_SCALE);
		$scales = $scale_inst->get_scale_objs_for_obj($c->id());
		$sm = $this->sub_merge;
		$this->sub_merge = 0;
		$value = $innervalue = "";
		
		foreach ($scales as $scale)
		{
			$val = $rate_inst->get_rating_for_object($c->id(), RATING_AVERAGE, $scale);
			if ($val>0)
			{
				$scale_obj = obj($scale);
				$title = $scale_obj->prop('comment');
 				$this->vars(array(
 					'extraf_name' => $title . ': '. $val,
				));
				$innervalue .= $this->parse('extraf_value');
			}
		}
		if (!empty($innervalue))
		{
			$this->vars(array(
				'extraf_title' => t("Asutusele antud hinnangud"),
				'extraf_value' => $innervalue,
			));
			$value = $this->parse('extrafeatures');
			$this->vars(array(
				'key' => "",
				'value' => $value,
			));
			$this->vars_merge(array('line_extrafeatures' => $this->parse('line_extrafeatures')));
		}

		$this->sub_merge = $sm;
		
		// Rating, show link 
		$rating = "";
		$ro = aw_global_get('rated_objs');
		if (!is_array($ro) || !isset($ro[$c->id()]))
		{
			$url = $this->mk_my_orb("rate_popup", array(
				"oid" => $c->id(),
			), CL_RATE, true);
			$rating = html::href(array(
				"url" => "javascript:aw_popup(\"$url\",\"Hinda objekti\",350,300)",
				"caption" => t("Hinda"),
				"title" => t("Hinda")
			));
		}
		$this->vars(array(
		//	'rating_form_vars' => $rating_form,
			'rating' => $rating,
			'images' => $images_html,
		));

		if (aw_global_get("uid") != "")
		{
			$this->vars(array(
				"logged" => $this->parse("logged")
			));
		}

		$kw = "";
		//v6tmes6nad
//		arr($c->prop("keywords2"));

		foreach($c->connections_from(array("type" => "RELTYPE_KEYWORD")) as $conn)
//		foreach(explode("," , $c->prop("activity_keywords")) as $key_word)
		{
			$key = $conn->to();
			$key_word = $key->name();
		
			$this->vars(array(
				"keyname" => trim($key_word),
				"keyurl" => $url = "/".aw_global_get('section')."?class=site_search_content&action=do_search&search_all=1no_reforb=1&keyword%5B".trim(strtolower($key_word))."%5D=1",
/*			$this->mk_my_orb('do_search',array(
						"search_all" => 1,
						"field" => 0,
						"area" => 0,
						"x" => 0,
						"y" => 0,
						"no_reforb" => 1,
						"section" => aw_global_get("section"),
					),
					CL_SITE_SEARCH_CONTENT, true
				),
*/			));
			$kw.=  $this->parse("keywords");
		}
		$this->vars(array(
			"keywords" => $kw,
		));

		// Alrighty then, parse your arse away
		$ret = $this->parse('company_show');
		exit_function("crm_company_webview::_get_company_show_html");
		return $ret;
	}

	// Return sorted list of companies to display
	function _list_companies ($arr)
	{
		enter_function('crm_company_webview::list');
		$orgs = array(); // return value
		if (!empty($arr["id"]))
		{
			$ob = new object($arr["id"]);
			$db = $ob->prop('crm_db');
		}
		elseif (!empty($arr['crm_db']))
		{
			$db = $arr['crm_db'];
			$ob  = null;
		}
		$crm_db = obj($db);
		$df = $crm_db->prop('dir_firma');
		if (is_array($df))
		{
			$df = reset($df);
		}
		$dir = is_oid($df) ? $df : $crm_db->prop('dir_default');
		$objs = array();

		// Get configuration
		$limited = false;
	
		if (!is_null($ob))
		{
	
			// Limit by sector
			$sector = $ob->prop('limit_sector');
			if (is_oid($sector) && ($osector = obj($sector)) && $osector->class_id() == CL_CRM_SECTOR)
			{
				$limit_sector = array($sector);
			}

			// If none is selected, limit by any connected sector
			if (!isset($limit_sector))
			{
				$limit_sector = array();
				foreach ($ob->connections_from(array('type' => 'RELTYPE_LIMIT_SECTOR')) as $con)
				{
					$limit_sector[] = $con->prop('to');
				}
			}
		
			// Setup limit by location - county
			$limit_city = $limit_county = null;
			$county = $ob->prop('limit_county');
			if (is_oid($county) && ($ocounty = obj($county)) && $ocounty->class_id() == CL_CRM_COUNTY)
			{
				$limit_county = $county;
				$limit_county_excl = $ob->meta('limit_county_excl'); // Exclusive
			}
		
			// Setup limit by location - city
			$city = $ob->prop('limit_city');
			if (is_oid($city) && ($ocity = obj($city)) && $ocity->class_id() == CL_CRM_CITY)
			{
				$limit_city = $city;
				$limit_city_excl = $ob->meta('limit_city_excl'); // Exclusive
			}
		}

		/// okay, I'm sorry, this is just SO badly done, I'm rewriting this completely.
		$filt = array(
			'class_id' => CL_CRM_COMPANY,
			'parent' => $dir,
			'lang_id' => array(),
		);
		
		if(!empty($arr["field"]) && is_oid($arr["field"]))
		{
			if(!is_array($limit_sector))
			{
				$limit_sector = array($arr["field"]);
			}
			else
			{
				$limit_sector[] = $arr["field"];
			}
		}
		if(!empty($arr["area"]))
		{
			$filt["CL_CRM_COMPANY.RELTYPE_ADDRESS.name"] = "%".$arr["area"]."%";
		}
		if(!empty($arr["county"]))
		{
			$filt["CL_CRM_COMPANY.contact.maakond"] = $arr["county"];
		}
		if(!empty($arr["city"]))
		{
			$filt["CL_CRM_COMPANY.contact.linn"] = $arr["city"];
		}
		if(isset($arr["keyword"]) && is_array($arr["keyword"]) && sizeof($arr["keyword"]))
		{
			$keyword_array = array();
			foreach($arr["keyword"] as $key => $val)
			{
				$keyword_array[] = "%".$key."%";
			}
			//$filt["activity_keywords"] = "%".$arr["keyword"]."%";
			$filt["CL_CRM_COMPANY.RELTYPE_KEYWORD.name"] = $keyword_array;
		}
	
		if ($arr["pohitegevus"])
		{
			$filt["pohitegevus"] = $arr["pohitegevus"];
			unset($filt["parent"]);
		}
		if (isset($limit_sector) && is_array($limit_sector) && count($limit_sector))
		{			
			if($arr["sector_recursive"])
			{
				foreach($limit_sector as $sector)
				{
					if(!is_oid($sector))
					{
						continue;
					}
					$ot = new object_tree(array(
						"parent" => $sector,
						"class_id" => CL_CRM_SECTOR,
						"lang_id" => array(),
						"site_id" => array(),
					));
					$limit_sector = array_merge($limit_sector, $ot->ids());
				}
			}
			$filt["CL_CRM_COMPANY.RELTYPE_TEGEVUSALAD"] = $limit_sector;
		}
		if (empty($limit_city_excl) && !empty($limit_city))
		{
			$filt["CL_CRM_COMPANY.contact.linn"] = $limit_city;
		}
		if (!empty($limit_city_excl) && !empty($limit_city))
		{
			$filt["CL_CRM_COMPANY.contact.linn"] = new obj_predicate_not($limit_city);
		}
		if (empty($limit_county_excl) && !empty($limit_county))
		{
			$filt["CL_CRM_COMPANY.contact.maakond"] = $limit_county;
		}
		if (!empty($limit_county_excl) && !empty($limit_county))
		{
			$filt["CL_CRM_COMPANY.contact.maakond"] = new obj_predicate_not($limit_county);
		}


		if ((($ob && $ob->prop("only_active")) || !empty($arr["only_active"])) && is_oid($crm_db->prop("owner_org")) && $this->can("view", $crm_db->prop("owner_org")))
		{
//			$filt["status"] = STAT_ACTIVE;
			$filt["CL_CRM_COMPANY.RELTYPE_BUYER(CL_CRM_COMPANY_CUSTOMER_DATA).show_in_webview"] = 1;
			$filt["CL_CRM_COMPANY.RELTYPE_BUYER(CL_CRM_COMPANY_CUSTOMER_DATA).status"] = array(object::STAT_ACTIVE, object::STAT_NOTACTIVE);
			$filt["CL_CRM_COMPANY.RELTYPE_BUYER(CL_CRM_COMPANY_CUSTOMER_DATA).seller"] = $crm_db->prop("owner_org");
			$filt["CL_CRM_COMPANY.RELTYPE_BUYER(CL_CRM_COMPANY_CUSTOMER_DATA).buyer"] = new obj_predicate_prop("id");
		}
	
		if (!empty($arr['limit_plaintext']))
		{
			$value = '%'.$arr['limit_plaintext'].'%';
			$conditions = array();
			foreach (array('name', 'comment') as $c) // Fields to search from listed here! here! here!
			{
				$conditions[$c] = $value;
			}
			$filt[] = new object_list_filter(array(
				'logic' => "OR",
				'conditions' => $conditions,
			));
		}

		$o_lut = array(
			'jrk' => "objects.jrk",
			'name' => "objects.name",
			'county' => "kliendibaas_address_129_contact.maakond",
			'city' => "kliendibaas_address_129_contact.linn",
		);
		$order = array();
		if (!is_null($ob))
		{
			for ($i=1; $i<4; $i++)
			{
				if ($ob->prop('ord'.$i))
				{
					$order[] = $o_lut[$ob->prop('ord'.$i)];
				}
			}
		}
		if (count($order))
		{
			$filt["sort_by"] = join(", ", $order);
		}
		$ol = new object_list($filt);
		$retval = $ol->arr();

		// Order the companies by 
		if($ol->count() > 0 && (isset($arr["pohitegevus"]) || (isset($limit_sector) && is_array($limit_sector) && count($limit_sector))))
		{
			$jrk_odl = new object_data_list(
				array(
					"class_id" => CL_CRM_COMPANY_SECTOR_MEMBERSHIP,
					"CL_CRM_COMPANY_SECTOR_MEMBERSHIP.RELTYPE_COMPANY" => $ol->ids(),
					"CL_CRM_COMPANY_SECTOR_MEMBERSHIP.RELTYPE_SECTOR" => isset($arr["pohitegevus"]) ? $arr["pohitegevus"] : $limit_sector,
					"lang_id" => array(),
					"site_id" => array(),
				),
				array(
					CL_CRM_COMPANY_SECTOR_MEMBERSHIP => array("jrk", "company"),
				)
			);
			foreach($jrk_odl->arr() as $jrk_odata)
			{
				$jrks[$jrk_odata["company"]] = $jrk_odata["jrk"];
			}
			asort($jrks, SORT_NUMERIC);
			$this->jrks = $jrks;
			uasort($retval, array($this, "cmp_orgs"));
		}
		exit_function('crm_company_webview::list');
		return $retval;
	}

	function cmp_orgs($a, $b)
	{
		$a_jrk = isset($this->jrks[$a->id()]) ? $this->jrks[$a->id()] : 0;
		$b_jrk = isset($this->jrks[$b->id()]) ? $this->jrks[$b->id()] : 0;
		if($a_jrk == $b_jrk)
		{
			if($a->ord() == $b->ord())
			{
				return strcmp($a->trans_get_val("name"), $b->trans_get_val("name"));
			}
			else
			{
				return $a->ord() > $b->ord() ? 1 : -1;
			}
		}
		else
		{
			return $a_jrk > $b_jrk ? 1 : -1;
		}
	}

	// Returns html for companies list
	function _get_companies_list_html($arr)
	{
		$this->sub_merge = 1;
		$do_link = !empty($arr['do_link']);
		$show_title = !empty($arr['show_title']);
		$title = ifset($arr, 'title');
		if (!empty($arr['id']))
		{
			$ob = new object($arr["id"]);
			$do_link = $ob->prop('clickable');
			$show_title = $ob->prop('show_title');
			if ($show_title)
			{
				$title = $this->trans_get_val($ob, "name");
			}
	
			$orgs = $this->_list_companies(array('id' => $arr['id'], "pohitegevus" => $arr["pohitegevus"]));
		}
		elseif (!empty($arr['list']) && is_array($arr['list']))
		{
			$orgs = $arr['list'];
		}
		else
		{
			return;
		}

		// Prepare for output
		$this->vars(array(
			'company_list_title' => '',
			'company_list_item' => '',
			'company_list' => '',
			'txt_address' => t("Aadress"),
			'txt_phone' => t("Tel"),
			'txt_fax' => t("Faks"),
			'txt_openhours' => t("Avatud"),
			'txt_email' => t("E-post"),
			'txt_web' => t("Koduleht"),
		));
		
		if ($show_title)
		{
			$this->vars(array(
				'title' => $title,
			));	
			$this->parse('company_list_title');
		}
		$datalist = array(
			'address' => 'contact',
			'address_separated' => 'contact',
			'phone' => '',
			'openhours' => '',
			'fax' => '',
			'email' => 'email_id',
			'web' => 'url_id',
			'images' => '',
			'logo' => '',
			'userta1' => '',
			'userta2' => '',
			'userta3' => '',
			'userta4' => '',
			'userta5' => '',
		);

		if (!empty($arr['url']))
		{
			$url = $arr['url'];
		}
		else
		{
			$url = '/'.aw_global_get('section').'?org=';
		}
		$used_fields = $this->v2_name_map;

		// Output company list
		$oh_inst = get_instance(CL_OPENHOURS);
		$img_inst = get_instance(CL_IMAGE);
		$ph_inst = get_instance(CL_CRM_PHONE);
		$cnt = 0;
		if(isset($_GET["page"]) && is_array($_GET["page"]) && isset($_GET["page"][$arr["id"]]))
		{
			$page = int($_GET["page"][$arr["id"]]);
		}
		elseif(isset($_GET["page"]) && !is_array($_GET["page"]))
		{
			$page = (int)($_GET["page"] - 1);
		}
		else
		{
			$page = 0;
		}
		foreach ($orgs as $o)
		{
			$cnt++;
			if(isset($page) && (($cnt-1) >= ($page+1)*20 || ($cnt-1) < $page*20))
			{
				continue;
			}
			$address = $phone = $fax = $openhours = $email = $web = "";
			$name = $o->trans_get_val("name");
			$tmp_ad = $this->can("view", $o->prop("contact"))?obj($o->prop("contact")):false;
			$tmp_cty = $tmp_ad?$tmp_ad->prop("maakond.name"):false;
			$cty_cap = $tmp_cty?$name." (".$tmp_cty.")":$name;
			
			$tmp_city = array();
			$tmp_cty?array_push($tmp_city, $tmp_cty):"";
			$tmp_ad?array_push($tmp_city, $tmp_ad->prop("linn.name")):"";
			$city_cap = $name.(count($tmp_city)?" (".join(", ", $tmp_city).")":"");

			$address = array();
			if($o->prop("contact.aadress"))
			{
				$address[] = $o->prop("contact.aadress");
			}
			if($o->prop("contact.aadress2"))
			{
				$address[] = $o->prop("contact.aadress2");
			}
			if($o->prop("contact.linn.name"))
			{
				$address[] = $o->prop("contact.linn.name");
			}
			if($o->prop("contact.maakond.name"))
			{
				$address[] = $o->prop("contact.maakond.name");
			}
			if($o->prop("contact.riik.name"))
			{
				$address[] = $o->prop("contact.riik.name");
			}
			$mail_links = array();
			foreach($o->get_mails() as $ml)
			{
				$mail_links[] = html::href(array(
					"caption" => $ml,
					"url" => "mailto:".$ml,
				));
			}
			$fake_email = html::href(array(
				"caption" => $o->prop("fake_email"),
				"url" => "mailto:".$o->prop("fake_email"),
			));

			$url_links = array();
			foreach($o->connections_from(array("type" => "RELTYPE_URL")) as $conn)
			{
				$to = $conn->to();
				if(strlen($to->url) === 0)
				{
					continue;
				}
				$url_links[] = html::href(array(
					"caption" => $to->url,
					"url" => substr($to->url, 0, 7) === "http://" ? $to->url : "http://".$to->url,
				));
			}
			$fake_url = html::href(array(
				"caption" => $o->prop("fake_url"),
				"url" => substr($o->prop("fake_url"), 0, 7) === "http://" ? $o->prop("fake_url") : "http://".$o->prop("fake_url"),
			));
			$fake_url_broken_into_rows = strlen($o->prop("fake_url")) > 0? html::href(array(
				"caption" => $this->brake_into_rows($o->prop("fake_url"), 35),
				"url" => substr($o->prop("fake_url"), 0, 7) === "http://" ? $o->prop("fake_url") : "http://".$o->prop("fake_url"),
			)) : "";

			$this->vars(array(
				'company_id' => $o->id(),
				'company_name' => $do_link ? html::href(array(
						'url' => $url . $o->id() . '&l='.$arr['id'],
						'caption' => $name))
						: $name,
				'company_name_with_county' => $do_link ? html::href(array(
						'url' => $url . $o->id() . '&l='.$arr['id'],
						'caption' => $cty_cap))
						: $cty_cap,
				'company_name_with_county_and_city' => $do_link ? html::href(array(
						'url' => $url . $o->id() . '&l='.$arr['id'],
						'caption' => $city_cap))
						: $city_cap,
				'company_name_url' => $url . $o->id() . '&l=' .$arr['id'],
				'company_name_text' => $name,
				'company_changeurl' => $this->can('edit', $o->id()) ? html::href(array(
						'caption' => '('.t("Muuda").')',
						'url' => $this->mk_my_orb('change',array(
								'id' => $o->id(),
							),CL_CRM_COMPANY, true),
						))
						: '',

				'address' => join(", " ,$address),//$o->prop("contact.name"),
				'country' => $o->prop("contact.riik.name"),
				'county' => $o->prop("contact.maakond.name"),
				'city' => $o->prop("contact.linn.name"),
				'aadress' => $o->prop("contact.aadress"),
				'mails' => join(", " , $o->get_mails()),
				'mail_links' => join(", " , $mail_links),
				'fake_email' => $fake_email,
				"fake_url" => $fake_url,
				"fake_url_broken_into_rows" => $fake_url_broken_into_rows,
				"url_links" => join(", ", $url_links),
				'fax' => join(", " , $o->get_faxes()),
				'phones' => join(", " , $o->get_phones()),
			));

			if(sizeof($o->get_phones()))
			{
				$this->vars(array("PHONES_SUB" => $this->parse("PHONES_SUB")));
			}

			foreach ($datalist as $item => $mapped)
			{
				// Skip parsing for values which are not used anyway
				if (!isset($used_fields['company_item_'.$item]))
				{
					unset($datalist[$item]); // and don't come here again!
					continue;
				}
				$this->vars(array('company_item_'.$item => ''));
				if (!empty($mapped))
				{
					$oid = $o->prop($mapped);
				}
				if (empty($mapped) || ($this->can("view", $oid) && $o_item = obj($oid)) && (is_object($o_item) && is_oid($o_item->id())))
				{
					if ($item == 'email')
					{
						$value = html::href(array(
							'url' => 'mailto:'.$o_item->prop('mail'),
							'caption' => $o_item->prop('mail'),
						));
					}
					elseif ($item == 'web')
					{
						$value = $o_item->name();
						$this->vars(array("company_web_url" => $value));
						$value = html::href(array(
							'url' => $value,
							'caption' => $value,
						));
					}
					elseif ($item == 'openhours')
					{
						$o_item = $o->get_first_obj_by_reltype('RELTYPE_OPENHOURS');
						if (!is_object($o_item))
						{
							continue;
						}
						$value = $oh_inst->show(array(
							'id' => $o_item->id(),
							'style' => 'short',
						));
					}
					elseif ($item == 'address')
					{
						$idx = $o_item->prop('postiindeks');
						$value = $o_item->name();
						if (strlen($idx) && strpos($value, $idx) === FALSE)
						{
							$value .= ", $idx";
						}
					}
					elseif ($item == 'logo')
					{
						$value = "";
						$logoo = $o->get_first_obj_by_reltype("RELTYPE_ORGANISATION_LOGO");
						if ($logoo)
						{
							$img_i = $logoo->instance();
							$value = $img_i->make_img_tag_wl($logoo->id());
						}
					}
					elseif ($item == 'images')
					{
						// Images
						$conns = $o->connections_from(array(
							'type' => 'RELTYPE_IMAGE',
						));
						$images = array();
						$i = 0;
						foreach ($conns as $conn)
						{
							if ($i++ == 3) // Limit number of images
							{
								break;
							}
							$image = $conn->to();
							if ($image->prop('status') != STAT_ACTIVE)
							{
								continue;
							}
							$tmp = $img_inst->parse_alias(array(
								'alias' => array(
									'target' => $image->id(),
								),
							));
							$images[] = $tmp['replacement']; // No, replacement is not a logical name in this context. However, it works!
						}
						$value = join('<br>', $images);
					}
					elseif ($item == 'phone' || $item == 'fax')
					{
						$reltype = 'RELTYPE_PHONE';
						if ($item == 'fax')
						{
							$reltype = 'RELTYPE_TELEFAX';
						}
						
						$conns = $o->connections_from(array(
							'type' => $reltype,
						));
						$value = array();
						foreach ($conns as $conn)
						{
							$thing = $conn->conn['to'];
							if ($this->can('view', $thing))
							{
								$value[] = $ph_inst->show(array(
									'oid' => $thing,
								));
							}	
						}
						$value = join(', ', $value);
					}
					elseif(substr($item, 0, 6) == 'userta')
					{
						$value = $o->trans_get_val($item);
					}
					else
					{
						$value = $o_item->name();
					}
					if (empty($value))
					{
						continue;
					}
					$this->vars(array('company_'.$item => $value));
					$this->parse('company_item_'.$item);
				}
			}

			if (aw_global_get("uid") != "")
			{
				$this->vars(array(
					"logged" => $this->parse("logged")
				));
			}
			$this->parse('company_list_item');
		}

		if (aw_global_get("uid") != "")
		{
			$this->vars(array(
				"logged" => $this->parse("logged")
			));
		}
		
		//tulemuste vahemiku linkide tekitamine
		if($cnt > 20)
		{
			if($cnt/20 > 5)
			{
				$bord = 1;
			}
			$n = 0;
			$b_s = "";
			$before_points = "";
			$after_points = "";
			if($bord && $page > 2)
			{
				$this->vars(array(
					"from_to" => "<<",
					"between_url" => aw_url_change_var('page', (int)($page) - 2),
				));
				$b_s.= $this->parse("between");
			}
			while(1)
			{
				if(!($n<$cnt/20))
				{
					break;
				}
				$to = (($n+1)*20);
				if($to >= $cnt)
				{
					$to = $cnt;
					if($after_points) $b_s.= $this->parse("points");
				}
				//see vaatab et oleks yle kolmanda lehekylje, ja selectitud oleks rohkem kui 1 lk tagasi
				if(!($n == (int)($cnt/20)) && !($n < 3) && $bord && $page+1 < $n)
				{
					$after_points = 1;
					$n++;
					continue;
				}
				//vaatab et selectitud oleks rohkem kui 1 lk edasi
				if(!($n == 0) && $bord && $page-1 > $n)
				{
					//see paneb punktiiri esimese valiku j2rele, kui vaja
					if($n == 1) 
					{
						$b_s.= $this->parse("points");
					}
					
					$before_points = 1;
					$n++;
					continue;
				}
				
				
				$this->vars(array(
					"from_to" => ($n*20 + 1)." - ".$to,
					"between_url" => aw_url_change_var('page', $n+1),
				));
				if($page == $n)
				{
					$b_s.= $this->parse("between_selected");
				}
				else
				{
					$b_s.= $this->parse("between");
				}
				$n++;
			}
			if($bord && $page+2 < (int)($cnt/20) && !($page == 0))
			{
				$this->vars(array(
					"from_to" => ">>",
					"between_url" => aw_url_change_var('page', (int)($page) + 4),
				));
				$b_s.= $this->parse("between");
			}
			$this->vars(array(
				"between" => $b_s,
				"between_selected" => "",
				"points" => "",
			));
		}
		$this->parse('company_list');
		return $this->parse();
	}

	function _get_conference_room_html($oid)
	{
		$this->read_template("company_popup_show.tpl");
		lc_site_load("crm_company_webview", $this);
		$o = obj($oid);
		$props = $o->get_property_list();
		foreach($props as $pn => $pd)
		{
			if(substr($pn, 0 , 5) == "type_")
			{
				$this->vars(array(
					"caption" => $pd["caption"],
					"type_amount" => ($o->prop($pn) < 1)?t("-"):$o->prop($pn),
				));
				$types_html .= $this->parse("TYPES");
			}
		}
		$this->vars(array(
			"TYPES" => $types_html,
			"name" => $o->name(),
			"desc" => strlen($o->prop("description"))?$o->prop("description"):t("-"),
			"total_area" => ($o->prop("area") < 1)?t("-"):$o->prop("area"),
			"total_area_cap" => $props["area"]["caption"],
			"name_cap" => $props["name"]["caption"],
			"desc_cap" => $props["description"]["caption"],
		));
		die($this->parse());
	}

	function callback_mod_tab($arr)
	{
		if ($arr["id"] == "transl" && aw_ini_get("user_interface.content_trans") != 1)
		{
			return false;
		}
		return true;
	}

	function callback_get_transl($arr)
	{
		return $this->trans_callback($arr, $this->trans_props);
	}

	/// submenus from object interface methods
	function get_folders_as_object_list($object, $level, $parent_o)
	{
		$this->_webview = $object;
		$this->_menu_parent_object = $parent_o;
		$crm_db = obj($object->prop("crm_db"));

		if ($level == 0)
		{
/*			$flds = new object_tree (array(
				"parent" => reset($crm_db->prop("dir_tegevusala")),
				"class_id" => CL_CRM_SECTOR,
				"lang_id" => array(),
				"site_id" => array()
			));

			$ret = new object_list(array(
				"class_id" => CL_CRM_SECTOR,
				"parent" => $flds->ids(),//$crm_db->prop("dir_tegevusala"),
				"sort_by" => "objects.jrk,objects.name"
			));*/
			$ret = new object_list(array(
				"class_id" => CL_CRM_SECTOR,
				"parent" => $crm_db->prop("dir_tegevusala"),
				"sort_by" => "objects.jrk,objects.name"
			));

		}
		else
		if (!$parent_o)
		{
			$ret = new object_list();
		}
		else
		{
			$ret = new object_list(array(
				"class_id" => CL_CRM_SECTOR,
				"parent" => $parent_o->id(),
				"sort_by" => "objects.jrk,objects.name",
//				"limit" => 4,
			));
		}

		$si = __get_site_instance();
//		arr(method_exists($si,'get_company_webview_folders'));
		if(method_exists($si,'get_company_webview_folders'))
		{
			return $si->get_company_webview_folders($ret , $object, $level, $parent_o);
		}
		return $ret;
	}

	function make_menu_link($sect_obj, $ref = NULL)
	{
		//selle jaoks kui miski viimane menyy on vaid selleks et rohkem 2ra ei mahu
		if($sect_obj->name() == "..." && is_object($this->_menu_parent_object) && $this->_webview)
		{
			return $this->mk_my_orb("show_sect", array("section" => $this->_menu_parent_object->id(), "wv" => $this->_webview->id()));
		}

		if ($sect_obj->trans_get_val("link") != "")
		{
			return $sect_obj->trans_get_val("link");
		}
		if ($sect_obj->alias() != "")
		{
			$ss = get_instance("contentmgmt/site_show");
			return $ss->make_menu_link($sect_obj);
		}
		if ($ref)
		{
			$link = $this->mk_my_orb("show_sect", array("section" => $sect_obj->id(), "wv" => $ref->id()));
		}
		if ($this->_webview)
		{
			$link = $this->mk_my_orb("show_sect", array("section" => $sect_obj->id(), "wv" => $this->_webview->id()));
		}
		return $link;
	}
	
	/**
		@attrib name=show_sect nologin="1"
		@param section required type=int acl=view
		@param wv required type=int acl=view
	**/
	function show_sect($arr)
	{
		//see aitab otsingus jne v6tta miski default v22rtuse
		$_SESSION["active_section"] = $arr["section"];
		$this->sub_merge = 1;

		$o = obj($arr['wv']);
		if ($o->class_id() != CL_CRM_COMPANY_WEBVIEW)
		{
			return;
		}
		$tmpl = $o->prop('template');
		if (!preg_match('/^[^\\\/]+\.tpl$/', $tmpl))
		{
			$tmpl = "default.tpl";
		}
		$this->read_template($tmpl);
		lc_site_load("crm_company_webview", $this);
		$ar = array();
		$si = get_instance("contentmgmt/site_show");
		$si->_init_path_vars($ar);
		$this->vars(array(
			"doc_content" => $si->show_documents($ar)
		));		

		$ret = $this->_get_companies_list_html(array(
			"id" => $o->id(),
			"pohitegevus" => $arr["section"],
			"do_link" => 1,
			"url" => $this->mk_my_orb("show_co", array(
				"section" => $arr["section"],
				"wv" => $arr["wv"]
			))."&org="
		));

		return $ret;
	}

	/**
		@attrib name=show_co nologin="1"
		@param section required type=int acl=view
		@param wv required type=int acl=view
		@param org required		
	**/
	function show_co($arr)
	{
		enter_function("crm_company_webview::show_co");
		$this->sub_merge = 1;

		$o = obj($arr['wv']);
		if ($o->class_id() != CL_CRM_COMPANY_WEBVIEW)
		{
			exit_function("crm_company_webview::show_co");
			return;
		}
		$tmpl = $o->prop('template');
		if (!preg_match('/^[^\\\/]+\.tpl$/', $tmpl))
		{
			$tmpl = "default.tpl";
		}
		$this->read_template($tmpl);
		lc_site_load("crm_company_webview", $this);
		$this->vars(array(
			"company_id" => $arr["org"],
			"print_link" => aw_url_change_var("print", 1)
		));
		$ret = $this->_get_company_show_html(array(
			"company_id" => $arr["org"],
			"list_id" => $_REQUEST["l"]

		));
		exit_function("crm_company_webview::show_co");
		return $ret;
	}

	/**
		@attrib name=show_sect_doc nologin="1"
	**/ 
	function show_sect_doc($arr)
	{
		$so = obj(aw_global_get("section"));
		$doc = $so->prop("info_document");
		if (is_oid($doc))
		{
			$ss = get_instance("contentmgmt/site_show");
			$ss->_init_path_vars($arr);
			return $ss->_int_show_documents($doc);
		}
	}

	public function brake_into_rows($str, $maks = 25)
	{
		for($k = 0; $k + $maks < strlen($str); $k = $l + 7)
		{
			$l = max(
				strrpos(substr($str, 0, $k + $maks), "@"),
				strrpos(substr($str, 0, $k + $maks), "."),
				strrpos(substr($str, 0, $k + $maks), "&"),
				strrpos(substr($str, 0, $k + $maks), "?"),
				strrpos(substr($str, 0, $k + $maks), "="),
				strrpos(substr($str, 0, $k + $maks), "/"),
				strrpos(substr($str, 0, $k + $maks), "-")
			);
			if($l <= 0 || $l <= strrpos(substr($str, 0, $k + $maks), ">"))
			{
				$l = min(
					strrpos(substr($str, $k + $maks), "@"),
					strrpos(substr($str, $k + $maks), "."),
					strrpos(substr($str, $k + $maks), "&"),
					strrpos(substr($str, $k + $maks), "?"),
					strrpos(substr($str, $k + $maks), "="),
					strrpos(substr($str, $k + $maks), "/"),
					strrpos(substr($str, $k + $maks), "-")
				);
				if($l <= 0)
				{
					break;
				}
				$l = $l <= 0 ? $maks : $l;
			}
			$str = substr_replace($str, '<br />', $l + 1, 0);
		}
		return $str;
	}
}
?>
