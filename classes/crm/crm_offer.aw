<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/crm/crm_offer.aw,v 1.66 2009/01/20 19:43:51 markop Exp $
// pakkumine.aw - Pakkumine 
/*

@classinfo syslog_type=ST_CRM_OFFER relationmgr=yes no_status=1 prop_cb=1 maintainer=markop

@tableinfo planner index=id master_table=objects master_index=brother_of
@tableinfo aw_crm_offer index=aw_oid master_table=objects master_index=oid

@default table=objects

@default group=general

	@property order_type type=relpicker table=aw_crm_offer datatype=int reltype=RELTYPE_TYPE field=order_type table=aw_crm_offer
	@caption Pakkumise t&uuml;&uuml;p

	@property orderer type=popup_search clid=CL_CRM_COMPANY,CL_CRM_PERSON style=autocomplete table=aw_crm_offer datatype=int reltype=RELTYPE_ORDERER
	@caption Tellija

	@property orderer_contact_person type=relpicker clid=CL_CRM_PERSON mode=autocomplete table=aw_crm_offer datatype=int reltype=RELTYPE_ORDERER_CONTACT_PERSON field=orderer_contact_person table=aw_crm_offer
	@caption Tellija kontaktisik

	@property cp_phone type=textbox store=no
	@caption Telefon

	@property cp_fax type=textbox store=no
	@caption Faks

	@property cp_mail type=textbox store=no
	@caption E-post


	@property project type=relpicker table=aw_crm_offer datatype=int reltype=RELTYPE_PROJECT
	@caption Projekt

	@property start1 type=datetime_select field=start table=planner
	@caption Algus

	@property accept_deadline type=date_select field=accept_deadline table=aw_crm_offer
	@caption Aktsepteerimist&auml;htaeg

	@property shipment_deadline type=date_select field=shipment_deadline table=aw_crm_offer
	@caption Tarne t&auml;htaeg

	@property preformer type=relpicker reltype=RELTYPE_PREFORMER table=aw_crm_offer 
	@caption T&auml;itja

	@property salesman type=select table=aw_crm_offer datatype=int
	@caption Pakkumise koostaja

	@property offer_status type=select table=aw_crm_offer datatype=int
	@caption Staatus

	@property content type=textarea cols=60 rows=20 table=planner field=description
	@caption Sisu

	@property prev_status type=hidden store=no

	@property sum type=textbox table=aw_crm_offer size=7 datatype=int
	@caption Hind (ilma KM)

	@property end type=datetime_select field=end table=planner
	@caption L&otilde;pp

	@property is_done type=checkbox table=objects field=flags method=bitmask ch_value=8 // OBJ_IS_DONE
	@caption Tehtud

	@default method=serialize
-------- Sisu ----
@default group=content

	@layout vbox_others type=hbox group=content width=20%:80%

	@layout vbox_tree type=vbox group=content parent=vbox_others
	@layout vbox_tbl type=vbox group=content parent=vbox_others 

	@property content_toolbar type=toolbar no_caption=1 store=no 

	@property content_tree type=treeview no_caption=1 store=no parent=vbox_tree
	@caption Puu

	@property content_list type=table store=no no_caption=1 parent=vbox_tbl
	@caption Pakkumised

-------- Tooted ----
@default group=products

	@property products_table type=table
	@caption Toodete tabel

-------- Kalendrid ----

	@property calendar_selector type=calendar_selector store=no group=calendars
	@caption Kalendrid

-------- Projektid -----

	@property project_selector type=project_selector store=no group=projects
	@caption Projektid


-------PAKKUMISE AJALUGU---------
@default group=history

	@property offer_history type=table no_caption=1 store=no group=history


@default group=offer

	@property offer type=text no_caption=1

@default group=parts

	@property parts_tb type=toolbar no_caption=1

	@property acts type=table store=no no_caption=1
	@caption Tegevused

@default group=files

	@property files type=text  no_caption=1
	@caption Manused

@groupinfo content caption="Sisu" submit=no
@groupinfo products caption="Tooted"
@groupinfo files caption="Failid" 
@groupinfo recurrence caption=Kordumine
@groupinfo calendars caption=Kalendrid
@groupinfo projects caption=Projektid
@groupinfo products_show caption=Tooted submit=no
@groupinfo history caption=Ajalugu submit=no
@groupinfo offer caption="Pakkumine" submit=no
@groupinfo parts caption="Osalejad" 
@groupinfo acl caption=&Otilde;igused
@default group=acl
	
	@property acl type=acl_manager store=no
	@caption &Otilde;igused

@reltype RECURRENCE value=1 clid=CL_RECURRENCE
@caption Kordus

@reltype ORDERER value=2 clid=CL_CRM_COMPANY,CL_CRM_PERSON
@caption Tellija

@reltype PREFORMER value=3 clid=CL_CRM_COMPANY
@caption T&auml;itja

@reltype SALESMAN value=4 clid=CL_CRM_PERSON
@caption Pakkumise koostaja

@reltype PRODUCT value=5 clid=CL_SHOP_PRODUCT
@caption Toode

@reltype OFFER_MGR value=7 clid=CL_CRM_OFFER_MGR
@caption Pakkumiste haldus

@reltype ACTION value=8 clid=CL_CRM_DOCUMENT_ACTION
@caption Tegevus

@reltype PROJECT value=9 clid=CL_PROJECT
@caption Projekt

@reltype FILE value=10 clid=CL_FILE
@caption File

@reltype TYPE value=11 clid=CL_CRM_OFFER_TYPE
@caption Pakkumise t&uuml;&uuml;p
*/

/*
CREATE TABLE `aw_crm_offer` (
`aw_oid` INT UNSIGNED NOT NULL ,
`orderer` INT UNSIGNED NOT NULL ,
`preformer` INT UNSIGNED NOT NULL,
`salesman` INT UNSIGNED NOT NULL ,
`sum` INT NOT NULL ,
`offer_status` TINYINT NOT NULL ,
PRIMARY KEY ( `aw_oid` )
);
*/


define("OFFER_ON_PROCESS",1);
define("OFFER_IS_SENT",2);
define("OFFER_IS_PREFORMED",3);
define("OFFER_IS_DECLINED",4);
define("OFFER_IS_POSITIVE",4);
class crm_offer extends class_base
{
	const AW_CLID = 225;
		
	var $u_i;
	var $statuses;
	function crm_offer()
	{
		$this->init(array(
			"clid" => CL_CRM_OFFER,
			"tpldir" => "crm/crm_offer"
		));
		$this->u_i = new user();
		$this->statuses =  array(
			t("Koostamisel"), 
			t("Saadetud"), 
			t("Esitletud"), 
			t("Tagasil&uuml;katud"), 
			t("Positiivelt l&otilde;ppenud")
		);		

		$this->addable = array(
			CL_CRM_OFFER_CHAPTER => t("Peat&uuml;kk"), 
			CL_CRM_OFFER_GOAL => t("Eesm&auml;rk"), 
			CL_CRM_OFFER_PAYMENT_TERMS => t("Maksetingimused"),
			CL_CRM_OFFER_PRODUCTS_LIST => t("Toodete nimekiri"),
			CL_CRM_OFFER_COMPARE_TABLE => t("V&otilde;rdlustabel"),
			CL_PROJECT => t("Projekt")
		);
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "project":
				if($arr["new"] && $this->can("view" , $arr["request"]["project"]))
				{
					$prop["value"] = $arr["request"]["project"];
					$prop["options"] = array($prop["value"] => get_name($prop["value"]));
				}
				break;
			case "order_type":
				$ol = new object_list(array(
					"class_id" => CL_CRM_OFFER_TYPE,
					"site_id" => array(),
					"lang_id" => array(),
				));
				if(!is_array($prop["options"]))
				{
					$prop["options"] = array();
				}
				$prop["options"] = $prop["options"] + $ol->names();
				break;
			case "orderer_contact_person":
				if(!$this->can("view" , $arr["obj_inst"]->prop("orderer"))  || $arr["obj_inst"]->prop("orderer.class_id") == CL_CRM_PERSON)
				{
					return PROP_IGNORE;
				}
				$o = obj($arr["obj_inst"]->prop("orderer"));
				$emp = $o->get_employees();
				$prop["options"] = $emp->names();
				if(array_key_exists($arr["obj_inst"]->prop("orderer_contact_person") , $prop["options"]))
				{
					$prop["options"] = $prop["options"] + array("" => $arr["obj_inst"]->prop("orderer_contact_person.name"));
				}
				else
				{
					$prop["options"] = $prop["options"] + array("" => "");
				}
				$prop["value"] = $arr["obj_inst"]->prop("orderer_contact_person");

//				$prop["autocomplete_source"] = $this->mk_my_orb("customer_contact_person_options_autocomplete_source");
//				$prop["autocomplete_params"] = array("orderer" , "orderer_contact_person");
				break;
			case "cp_mail":
				if(!$this->can("view" , $arr["obj_inst"]->prop("orderer")) || !$this->can("view" , $arr["obj_inst"]->prop("orderer_contact_person")))
				{
					return PROP_IGNORE;
				}
				$person = obj($arr["obj_inst"]->prop("orderer_contact_person"));
				$prop["value"] = $person->prop("fake_email");
				break;
			case "cp_phone":
				if(!$this->can("view" , $arr["obj_inst"]->prop("orderer")) || !$this->can("view" , $arr["obj_inst"]->prop("orderer_contact_person")))
				{
					return PROP_IGNORE;
				}
				$person = obj($arr["obj_inst"]->prop("orderer_contact_person"));
				$prop["value"] = $person->prop("fake_phone");
				break;
			case "cp_fax":
				if(!$this->can("view" , $arr["obj_inst"]->prop("orderer")) || !$this->can("view" , $arr["obj_inst"]->prop("orderer_contact_person")))
				{
					return PROP_IGNORE;
				}
				$person = obj($arr["obj_inst"]->prop("orderer_contact_person"));
				$prop["value"] = $person->prop("fax");
				break;

			case "files":
				$this->_get_files($arr);
				break;

			case "acts":
				$i = get_instance("applications/crm/crm_document_base");
				return $i->get_property($arr);
			case "parts_tb":
				$i = get_instance("applications/crm/crm_document_base");
				return $i->get_property($arr);
			case "start1":
				$p = get_instance(CL_PLANNER);
				$cal = $p->get_calendar_for_user();
				if ($cal)
				{
					$calo = obj($cal);
					$data["minute_step"] = $calo->prop("minute_step");
				}
				break;

			case "preformer":
				if (($arr["new"] || $_GET["group"] == "add_event") && !$prop["value"])
				{
					$val = $arr["request"]["alias_to"];
					if (!$val)
					{
						$u = new user();
						$val = $u->get_current_company();
					}

					if ($this->can("view", $val))
					{
						$o = obj($val);
						$prop["options"] = array("" => t("--Vali--"), $o->id() => $o->name());
						$prop["value"] = $o->id();
					}
	
				}
				
				break;

			case "start1":
			//	return PROP_IGNORE;
			break;
		
			case "orderer":
				if($arr["new"] && $this->can("view" , $arr["request"]["project"]))
				{
					$project = obj($arr["request"]["project"]);
					$orderers = new object_list(array(
						"class_id" => array(CL_CRM_COMPANY, CL_CRM_PERSON),
						"oid" => $project->prop("orderer"),
						"lang_id" => array(),
						"site_id" => array()
					));
					$prop["options"] = $orderers->names();
					$prop["value"] = $project->get_orderer();
					break;
				}

				$my_org = false;

				if(is_object($arr["obj_inst"]))
				{
					$id = $arr["obj_inst"]->prop("preformer");
					if (is_oid($id) && $this->can("view", $id))
					{
						$my_org = obj($id);
					}
				}

				if (!$my_org)
				{
					$my_org = $this->u_i->get_current_company();
					$my_org = &obj($my_org);
				}
				$data = array();
				if($my_org)
				{
					$org_inst = get_instance(CL_CRM_COMPANY);
					$data = $my_org->get_customers_for_company();
				
				}
				foreach ($data as $key)
				{
					if ($this->can("view", $key))
					{
						$obj = &obj($key);
						$options[$key] = $obj->name();
					}
				}

				
						
				if($arr["request"]["alias_to_org"])
				{
					$prop["value"] = $arr["request"]["alias_to_org"];
				}
				else
				if(is_object($arr["obj_inst"]) && $arr["obj_inst"]->prop("orderer"))
				{
					$prop["value"] = $arr["obj_inst"]->prop("orderer");
				}
				if (!isset($options[$prop["value"]]) && $this->can("view", $prop["value"]))
				{
					$tmp = obj($prop["value"]);
					$options[$tmp->id()] = $tmp->name();
				}
				$prop["options"] = $options;
				break;
			
			case "offer_history":
				$this->do_offer_history($arr);
			break;
			
			case "salesman":
				$my_company = $this->u_i->get_current_company();
				$org = &obj($my_company);
				$workers = $org->get_workers();
				
				$prop["options"] = $workers->names();
				
				if(!$prop["value"])
				{
					$person_id = $this->u_i->get_current_person();
					$person_obj = &obj($person_id);
					$prop["value"] = $person_obj->id();
				}
				break;
			
			case "offer_status":
				$prop["options"] = $this->statuses;
				break;
			
			case "prev_status":
				if(is_object($arr["obj_inst"]))
				{
					$prop["value"] = $arr["obj_inst"]->prop("offer_status");
				}
				break;

			case "content_toolbar":
				$this->_content_toolbar($arr);
				break;

			case "content_tree":
				$this->_content_tree($arr);
				break;

			case "content_list":
				$this->_content_list($arr);
				break;

			case "offer";
				$prop["value"] = $this->generate_offer($arr["obj_inst"]);
				break;

			case "is_done":
				return PROP_IGNORE;

		};
		return $retval;
	}
	
	function set_property($arr)
	{
		$b = get_instance("applications/crm/crm_document_base");
		$retval = $b->set_property($arr);

		$data = &$arr["prop"];
		switch($data["name"])
		{
			case "files":
				$this->_set_files($arr);
				break;

//			case "acts":
//				$i = get_instance("applications/crm/crm_document_base");
//				return $i->set_property($arr);

			case "salesman":
				if($data["value"])
				{
					$arr["obj_inst"]->connect(array(
						"to" => $data["value"],
						"reltype" => "RELTYPE_SALESMAN",
					));
				}
				break;
			
			case "orderer":
				$data["value"] = $arr["request"]["orderer_awAutoCompleteTextbox"];
				if($data["value"] && !$this->can("view" , $data["value"]))
				{
					$ol = new object_list(array(
						"class_id" => CL_CRM_COMPANY,
						"lang_id" => array(),
						"site_id" => array(),
						"name" => $data["value"],
					));
					$data["value"] = reset($ol->ids());
				}
				if(!$data["value"])
				{
					return PROP_IGNORE;
				}
				if($data["value"])
				{
					$arr["obj_inst"]->connect(array(
						"to" => $data["value"],
						"reltype" => "RELTYPE_ORDERER",
					));
				}
				if($arr["obj_inst"]->prop("orderer") && $arr["obj_inst"]->prop("orderer") != $data["value"])
				{
					$this->c_changed = 1;
				}
				$arr["obj_inst"]->set_prop("orderer" , $data["value"]);
				$arr["obj_inst"]->save();
				return PROP_IGNORE;
				break;
			case "orderer_contact_person":
				if(!$this->can("view" , $arr["obj_inst"]->prop("orderer")) || !$data["value"] || $arr["obj_inst"]->prop("orderer.class_id") == CL_CRM_PERSON || $this->c_changed)
				{
					return PROP_IGNORE;
				}
				$o = obj($arr["obj_inst"]->prop("orderer"));
				$emp = $o->get_employees();
				$options = $emp->names();
				if(in_array($data["value"] , $options))
				{
					$data["value"] = array_search($data["value"], $options);
				}
				else
				{
					$data["value"] = $o->add_employees(array(
						"name" => $data["value"],
					));
				}
				if($arr["obj_inst"]->prop("orderer_contact_person") && $arr["obj_inst"]->prop("orderer_contact_person") != $data["value"])
				{
					$this->cp_changed = 1;
				}
				$arr["obj_inst"]->set_prop("orderer_contact_person" , $data["value"]);
				$arr["obj_inst"]->save();
				return PROP_IGNORE;
				break;
			case "cp_mail":
				if(!$this->can("view" , $arr["obj_inst"]->prop("orderer")) || !$this->can("view" , $arr["obj_inst"]->prop("orderer_contact_person")) || $this->cp_changed || $this->c_changed)
				{
					return PROP_IGNORE;
				}
				$person = obj($arr["obj_inst"]->prop("orderer_contact_person"));
				$person->set_prop("fake_email" , $data["value"]);
				break;
			case "cp_phone":
				if(!$this->can("view" , $arr["obj_inst"]->prop("orderer")) || !$this->can("view" , $arr["obj_inst"]->prop("orderer_contact_person")) || $this->cp_changed || $this->c_changed)
				{
					return PROP_IGNORE;
				}
				$person = obj($arr["obj_inst"]->prop("orderer_contact_person"));
				$person->set_prop("fake_phone" , $data["value"]);
				break;
			case "cp_fax":
				if(!$this->can("view" , $arr["obj_inst"]->prop("orderer")) || !$this->can("view" , $arr["obj_inst"]->prop("orderer_contact_person")) || $this->cp_changed || $this->c_changed)
				{
					return PROP_IGNORE;
				}
				$person = obj($arr["obj_inst"]->prop("orderer_contact_person"));
				$person->set_prop("fax" , $data["value"]);
				$person->save();
				break;
		};
		return $retval;
	}

	function _get_products_table($arr)
	{
		$t = &$arr['prop']['vcl_inst'];

		$t->define_field(array(
			'name' => 'product',
			'caption' => t('Toode')
		));
		$t->define_field(array(
			'name' => 'amount',
			'caption' => t('Kogus')
		));
		$t->define_field(array(
			'name' => 'unit',
			'caption' => t('&Uuml;hik')
		));
		$t->define_field(array(
			'name' => 'price',
			'caption' => t('Hind'),
			'align' => 'center'
		));
		$t->define_field(array(
			'name' => 'currency',
			'caption' => t('Valuuta')
		));
		$t->define_field(array(
			'name' => 'shipment_date',
			'caption' => t('Tarneaeg')
		));
		$t->define_field(array(
			'name' => 'accept',
			'caption' => t('Aktsept'),
			'width' => '5%',
			'align' => 'center'
		));

		// get units list:
		$units = array();
		$units_ol = new object_list(array(
			'class_id' => CL_CRM_BUILDING_MANAGEMENT_UNIT
		));
		foreach ($units_ol->arr() as $unit_obj)
		{
			$units[$unit_obj->id()] = $unit_obj->name().' ('.$unit_obj->prop('code').')';
		}


		// get currencies list:
		$cuurencies = array();
		$currency_ol = new object_list(array(
			'class_id' => CL_CURRENCY	
		));
		foreach ($currency_ol->arr() as $currency_obj)
		{
			$currencies[$currency_obj->id()] = $currency_obj->name();
		}

		//date select:
		$date_edit = get_instance('vcl/date_edit');
		$date_edit->configure(array(
			'year_textbox' => '',
			'month_textbox' => '',
			'day_textbox' => '',
		));

		$count = 0;
		for ($i = 0; $i < 10; $i++)
		{
			$t->define_data(array(
				'product' => html::textbox(array(
					'name' => 'products['.$count.'][product]'
				)),
				'amount' => html::textbox(array(
					'name' => 'products['.$count.'][amount]',
					'size' => 5
				)),
				'unit' => html::select(array(
					'name' => 'products['.$count.'][unit]',
					'options' => $units
				)),
				'price' => html::textbox(array(
					'name' => 'products['.$count.'][price]',
					'size' => 5
				)),
				'currency' => html::select(array(
					'name' => 'products['.$count.'][currency]',
					'options' => $currencies
				)),
				'shipment_date' => $date_edit->gen_edit_form('products['.$count.'][shipment_date]', -1),
				'accept' => html::checkbox(array(
					'name' => 'products['.$count.'][accept]'
				))
			));
			$count++;
		}

		return PROP_OK;
	}
	
	/**
		Returns offers ids made for company
	**/
	function get_offers_for_company($orderer_id, $preformer_id = false)
	{
		if($orderer_id)
		{
			$ol = new object_list(array(
				"class_id" => CL_CRM_OFFER,
				"orderer" => $orderer_id,
				"preformer" => $preformer_id,
			));
			return $ol;
		}
	}
	
	function callback_pre_save($arr)
	{
		if($arr["request"]["offer_status"] == 3 || $arr["request"]["offer_status"] == 4)
		{
			$arr["obj_inst"]->set_prop("is_done", OBJ_IS_DONE);
		}
		else 
		{
			$arr["obj_inst"]->set_prop("is_done", 0);
		}
		//If offer status has been changed then lets write to log about it.
		if($arr["request"]["prev_status"] != $arr["request"]["offer_status"])
		{
			$status_data = $arr["obj_inst"]->meta("statuslog");
			$status_data[time()] = array(
				"prev_status" => $arr["request"]["prev_status"],
				"new_status" => $arr["request"]["offer_status"], 
				"uid" => aw_global_get("uid"),
			);
			$arr["obj_inst"]->set_meta("statuslog", $status_data);
		}
	}
	
	function callback_post_save($arr)
	{
		if($arr["new"]==1)
		{
			$users = get_instance("users");
			$user = new object(aw_global_get("uid_oid"));
			$conns = $user->connections_to(array(
				"type" => 8, //RELTYPE_CALENDAR_OWNERSHIP
			));
			if(count($conns))
			{
				$conn = current($conns);
				$calender = &obj($conn->prop("from"));
				$parent = $calender->prop("event_folder");
				if($parent)
				{
					$arr["obj_inst"]->create_brother($parent);
				}
			}
			
			if(is_oid($arr["request"]["project"]) && $this->can("view" , $arr["request"]["project"]))
			{
				$project = obj($arr["request"]["project"]);
				foreach($project->prop("orderer") as $orderer)
				{
					$arr["obj_inst"]->connect(array("to" => $arr["request"]["project"], "reltype" => "ORDERER"));
				}
				$arr["obj_inst"]->connect(array("to" => $arr["request"]["project"], "reltype" => "PROJECT"));
				$arr["obj_inst"]->set_prop("project" , $arr["request"]["project"]);
			}
		}

		$pl = get_instance(CL_PLANNER);
		$pl->post_submit_event($arr["obj_inst"]);
	}
	
	function do_offer_history(&$arr)
	{
		$table = &$arr["prop"]["vcl_inst"];
		$table->define_field(array(
			"name" => "prev",
			"caption" => t("Algstaatus"),
			"sortable" => "1",
		));
		
		$table->define_field(array(
			"name" => "next",
			"caption" => t("L&otilde;ppstaatus"),
			"sortable" => "1",
		));
		
		$table->define_field(array(
			"name" => "time",
			"caption" => t("Muutuse aeg"),
			"sortable" => "1",
		));
	
		$table->define_field(array(
			"name" => "who",
			"caption" => t("Muutja"),
			"sortable" => "1",
		));
		
		$user = get_instance("users");
		if(!is_array($arr["obj_inst"]->meta("statuslog")))
		{
			return;
		}
		foreach ($arr["obj_inst"]->meta("statuslog") as $key => $logitem)
		{
			$uid = $user->get_oid_for_uid($logitem["uid"]);
			$user_obj = &obj($uid);
			$person_id = $this->u_i->get_person_for_user($user_obj);
			$person_obj = &obj($person_id);
			

			$table->define_data(array(
				"prev" => $this->statuses[$logitem['prev_status']],
				"next" => $this->statuses[$logitem['new_status']],
				"who" => $person_obj->name(),
				"time" => get_lc_date($key)." - kell: " .date("G:i", $key),
			));
		}
	}
	
	function _content_toolbar($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];

		$t->add_menu_button(array(
			"name" => "new",
			"tooltip" => t("Lisa")
		));

		$clss = aw_ini_get("classes");

		foreach($this->addable as $clid => $tx)
		{
			$t->add_menu_item(array(
				"parent" => "new",
				"text" => $tx,
				"link" => html::get_new_url($clid, $arr["request"]["tf"] ? $arr["request"]["tf"] : $arr["obj_inst"]->id(), array("return_url" => get_ru()))
			));
		}

		$omgr = get_instance(CL_CRM_OFFER_MGR);
		$mgr_o = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_OFFER_MGR");
		if ($mgr_o)
		{
			$typicals = $omgr->get_typical_components($mgr_o);
		}

		if (count($typicals))
		{
			$t->add_sub_menu(array(
				"parent" => "new",
				"name" => "new_tp",
				"text" => t("T&uuml;&uuml;pkomponendid")
			));

			foreach($typicals as $t_id => $t_nm)
			{
				$t->add_menu_item(array(
					"parent" => "new_tp",
					"text" => $t_nm,
					"link" => $this->mk_my_orb("add_based_on_typical", array(
						"id" => $arr["obj_inst"]->id(),
						"parent" => $arr["request"]["tf"],
						"based_on" => $t_id,
						"ru" => get_ru()
					))
				));
			}
		}		

		$t->add_button(array(
			"name" => "save",
			"img" => "save.gif",
			"action" => "save_cl",
			"tooltip" => t("Salvesta"),
		));

		$t->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"action" => "del_parts",
			"tooltip" => t("Kustuta valitud osad"),
			"confirm" => t("Oled kindel et soovid valitud osad kustutada?")
		));
	}

	function _content_tree($arr)
	{
		classload("core/icons");
		$arr["prop"]["vcl_inst"] = treeview::tree_from_objects(array(
			"tree_opts" => array(
				"type" => TREE_DHTML, 
				"persist_state" => true,
				"tree_id" => "offer_t",
			),
			"root_item" => $arr["obj_inst"],
			"ot" => new object_tree(array(
				"class_id" => array_keys($this->addable),
				"parent" => $arr["obj_inst"]->id(),
			)),
			"var" => "tf",
			"icon" => icons::get_icon_url(CL_MENU)
		));
	}

	/**

		@attrib name=save_cl

	**/
	function save_cl($arr)
	{
		foreach(safe_array($arr["dat"]) as $oid => $inf)
		{
			if (is_oid($oid) && $this->can("view", $oid))
			{
				$o = obj($oid);
				if ($o->ord() != $inf["ord"])
				{
					$o->set_ord($inf["ord"]);
					$o->save();
				}
			}
		}
		return $arr["post_ru"];
	}

	function _cb_cl_ord($arr)
	{
		return html::textbox(array(
			"name" => "dat[".$arr["oid"]."][ord]",
			"value" => $arr["ord"],
			"size" => 5
		));
	}

	function _init_content_list_t(&$t)
	{	
		$t->define_field(array(
			"name" => "ord",
			"caption" => t("J&auml;rjekord"),
			"align" => "center",
			"callback" => array(&$this, "_cb_cl_ord"),
			"callb_pass_row" => 1
		));

		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "class_id",
			"caption" => t("T&uuml;&uuml;p"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "change",
			"caption" => t("Muuda"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "typical",
			"align" => "center"
		));

		$t->define_chooser(array(
			"field" => "oid",
			"name" => "sel"
		));
	}

	function _content_list($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_content_list_t($t);

		$omgr = get_instance(CL_CRM_OFFER_MGR);
		$mgr_o = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_OFFER_MGR");
		if ($mgr_o)
		{
			$typicals = $omgr->get_typical_components($mgr_o);
		}

		$clss = aw_ini_get("classes");

		$ol = new object_list(array(
			"parent" => $arr["request"]["tf"] ? $arr["request"]["tf"] : $arr["obj_inst"]->id(),
			"class_id" => array_keys($this->addable)
		));
		foreach($ol->arr() as $o)
		{
			if (isset($typicals[$o->id()]))
			{
				$typical = html::href(array(
					"url" => $this->mk_my_orb("remove_from_typical_component_list", array(
						"id" => $arr["obj_inst"]->id(), 
						"co" => $o->id(), 
						"ru" => get_ru()
					)),
					"caption" => t("Eemalda t&uuml;&uuml;pkomonentide nimekirjast")
				));
			}
			else
			{
				$typical = html::href(array(
					"url" => $this->mk_my_orb("add_to_typical_component_list", array(
						"id" => $arr["obj_inst"]->id(), 
						"co" => $o->id(), 
						"ru" => get_ru()
					)),
					"caption" => t("Tee t&uuml;&uuml;pkomponendiks")
				));
			}
			$t->define_data(array(
				"ord" => $o->ord(),
				"name" => parse_obj_name($o->name()),
				"class_id" => $clss[$o->class_id()]["name"],
				"change" => html::get_change_url($o->id(), array("return_url" => get_ru()), parse_obj_name($o->name())),
				"typical" => $typical,
				"oid" => $o->id()
			));
		}
		$t->set_default_sortby("ord");
		$t->sort_by();
	}

	/**

		@attrib name=del_parts

	**/
	function del_parts($arr)
	{
		if (count(safe_array($arr["sel"])))
		{
			$ol = new object_list(array(
				"oid" => $arr["sel"]
			));
			$ol->delete();
		}
		return $arr["post_ru"];
	}


	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
		if(!$arr["id"])
		{
			$arr["project"] = $_GET["project"];
		}
	}

	function _get_wh($o)
	{
		$mgr = $o->get_first_obj_by_reltype("RELTYPE_OFFER_MGR");
		if ($mgr)
		{
			$wh = $mgr->get_first_obj_by_reltype("RELTYPE_WAREHOUSE");
			if ($wh)
			{
				return $wh;
			}
		}
	}

	function generate_offer($o)
	{
		// try pdf
		if ($_GET["pdf"] == 1)
		{
			$this->_try_pdf($o);
		}
		

		$this->read_template("offer_html.tpl");

		$html = "";

		// get offer subobjects
		$ot = new object_tree(array(
			"parent" => $o->id(),
			"class_id" => array_keys($this->addable),
			"sort_by" => "objects.jrk"
		));

		// go over tree and generate html
		$list = $ot->to_list();
		foreach($list->arr() as $item)
		{
			$item_i = $item->instance();
			$html .= $item_i->generate_html($o, $item);
		}
		
		$orderer = "";
		if (is_oid($o->prop("orderer")) && $this->can("view", $o->prop("orderer")))
		{
			$orderer_o = obj($o->prop("orderer"));
			$orderer = $orderer_o->name();
		}
		$implementor = "";
		$imp_o = $o->get_first_obj_by_reltype("RELTYPE_PREFORMER");
		$lg = "";
		if (is_object($imp_o))
		{
			$implementor = $imp_o->name();

			if (($lg = $imp_o->prop("logo")))
			{
				$lg = html::img(array(
					"url" => $lg
				));
			}
		}

		$this->vars(array(
			"content" => $html,
			"name" => $o->name(),
			"orderer" => $orderer,
			"implementor" => $implementor,
			"date" => aw_locale::get_lc_date(date(), LC_DATE_FORMAT_LONG),
			"logo" => $lg
		));

		return html::href(array("url" => aw_url_change_var("pdf", 1), "caption" => "PDF")).$this->parse();
	}

	/**

		@attrib name=add_to_typical_component_list

		@param id required type=int acl=view
		@param co required type=int acl=view
		@param ru required

	**/
	function add_to_typical_component_list($arr)
	{
		// get manager
		$o = obj($arr["id"]);
		$mgr = $o->get_first_obj_by_reltype("RELTYPE_OFFER_MGR");
		// connect to obj
		if (!$mgr->is_connected_to(array("to" => $arr["co"], "type" => "RELTYPE_TYPICAL_COMPONENT")))
		{
			$mgr->connect(array(
				"to" => $arr["co"],
				"reltype" => "RELTYPE_TYPICAL_COMPONENT"
			));
		}
		return $arr["ru"];
	}

	/**

		@attrib name=remove_from_typical_component_list

		@param id required type=int acl=view
		@param co required type=int acl=view
		@param ru required

	**/
	function remove_from_typical_component_list($arr)
	{
		// get manager
		$o = obj($arr["id"]);
		$mgr = $o->get_first_obj_by_reltype("RELTYPE_OFFER_MGR");

		// connect to obj
		if ($mgr->is_connected_to(array("to" => $arr["co"], "type" => "RELTYPE_TYPICAL_COMPONENT")))
		{
			$mgr->disconnect(array(
				"from" => $arr["co"],
			));
		}
		return $arr["ru"];
	}

	/**

		@attrib name=add_based_on_typical

		@param id required type=int
		@param parent optional
		@param based_on required type=int acl=view
		@param ru optional
	**/
	function add_based_on_typical($arr)
	{
		// copy object
		$mgr = get_instance(CL_CRM_OFFER_MGR);
		$new = $mgr->_copy_object(obj($arr["based_on"]), $arr["parent"] ? $arr["parent"] : $arr["id"]);
		return $arr["ru"];
	}

	function _try_pdf($o)
	{
		$this->read_template("offer_xsl.tpl");

		// get offer subobjects
		$ot = new object_tree(array(
			"parent" => $o->id(),
			"class_id" => array_keys($this->addable),
			"sort_by" => "objects.jrk"
		));

		// go over tree and generate html
		$list = $ot->to_list();
		$ce = "";
		foreach($list->arr() as $item)
		{
			$item_i = $item->instance();
			$cl = get_class($item_i);
			if ($cl == "crm_offer_chapter")
			{
				$html .= $item_i->generate_pdf($o, $item);

				$this->vars(array(
					"ch_name" => $item->name(),
					"ch_id" => $item->id()
				));
				$ce .= $this->parse("CONTENTS_ENTRY");
			}
		}


		$orderer = "";
		if (is_oid($o->prop("orderer")) && $this->can("view", $o->prop("orderer")))
		{
			$orderer_o = obj($o->prop("orderer"));
			$orderer = $orderer_o->name();
		}
		$implementor = "";
		$imp_o = $o->get_first_obj_by_reltype("RELTYPE_PREFORMER");
		if (is_object($imp_o))
		{
			$implementor = $imp_o->name();
		}

		$lg = $imp_o->prop("logo");

		$this->vars(array(
			"CONTENTS_ENTRY" => $ce,
			"name" => $o->name(),
			"orderer" => $orderer,
			"implementor" => $implementor,
			"date" => aw_locale::get_lc_date(date(), LC_DATE_FORMAT_LONG),
			"logo" => $lg,
			"content" => $html
		));

		$fo = $this->parse();

		// write to temp file
		$fn_in = tempnam(aw_ini_get("server.tmpdir"), "aw-offer-gen");
		$fn_out = tempnam(aw_ini_get("server.tmpdir"), "aw-offer-gen");
		$this->put_file(array(
			"file" => $fn_in,
			"content" => $fo
		));

		chdir(aw_ini_get("server.fop_dir"));

		$fop_cmd = aw_ini_get("server.fop_cmd")." ".$fn_in." ".$fn_out;
		$res = `$fop_cmd`;

		$ct = $this->get_file(array("file" => $fn_out));
		if (strlen($ct) == 0)
		{
		unlink($fn_in);
		unlink($fn_out);
			die(
	"<pre>".$res."\n\n".htmlentities($fo)."</pre>");
		}

		header("Content-type: application/pdf");
		echo $ct;

		unlink($fn_in);
		unlink($fn_out);
		die();
	}

	function new_change($arr)
	{
		aw_session_set('org_action',aw_global_get('REQUEST_URI'));
		return parent::new_change($arr);
	}

	function _get_files($arr)
	{
		$objs = array();

		if (is_object($arr["obj_inst"]) && is_oid($arr["obj_inst"]->id()))
		{
			$ol = new object_list($arr["obj_inst"]->connections_from(array(
				"type" => "RELTYPE_FILE"
			)));
			$objs = $ol->arr();
		}

		$objs[] = obj();
		$objs[] = obj();
		$objs[] = obj();

		$types = array(
			CL_FILE => t(""),
			CL_CRM_MEMO => t("Memo"),
			CL_CRM_DOCUMENT => t("CRM Dokument"),
			CL_CRM_DEAL => t("Leping"),
			CL_CRM_OFFER => t("Pakkumine")
		);

		$impl = get_current_company();
		$impl = $impl->id();

		if ($this->can("view", $impl))
		{
			$impl_o = obj($impl);
			if (!$impl_o->get_first_obj_by_reltype("RELTYPE_DOCS_FOLDER"))
			{
				$u = new user();
				$impl = $u->get_current_company();
			}
		}

		if ($this->can("view", $impl))
		{
			$implo = obj($impl);
			$f = get_instance("applications/crm/crm_company_docs_impl");
			$fldo = $f->_init_docs_fld(obj($impl));
			$ot = new object_tree(array(
				"parent" => $fldo->id(),
				"class_id" => CL_MENU
			));
			$folders = array($fldo->id() => $fldo->name());
			$this->_req_level = 0;
			$this->_req_get_folders($ot, $folders, $fldo->id());

			// add server folders if set
			$sf = $implo->get_first_obj_by_reltype("RELTYPE_SERVER_FILES");
			if ($sf)
			{
				$s = $sf->instance();
				$fld = $s->get_folders($sf);
				$t =& $arr["prop"]["vcl_inst"];

				usort($fld, create_function('$a,$b', 'return strcmp($a["name"], $b["name"]);'));

				$folders[$sf->id().":/"] = $sf->name();
				$this->_req_get_s_folders($fld, $sf, $folders, 0);
			}
		}
		else
		{
			$fldo = obj();
			$folders = array();
		}

		$clss = aw_ini_get("classes");
		foreach($objs as $idx => $o)
		{
			$this->vars(array(
				"name" => $o->name(),
				"idx" => $idx,
				"types" => $this->picker($types)
			));

			if (is_oid($o->id()))
			{
				$ff = $o->get_first_obj_by_reltype("RELTYPE_FILE");
				if (!$ff)
				{
					$ff = $o;
				}
				$fi = $ff->instance();
				$fu = html::href(array(
					"url" => $fi->get_url($ff->id(), $ff->name()),
					"caption" => $ff->name()
				));
				$data[] = array(
					"name" => html::get_change_url($o->id(), array("return_url" => get_ru()), $o->name()),
					"file" => $fu,
					"type" => $clss[$o->class_id()]["name"],
					"del" => html::href(array(
						"url" => $this->mk_my_orb("del_file_rel", array(
								"return_url" => get_ru(),
								"fid" => $o->id(),
								"from" => $arr["obj_inst"]->id()
						)),
						"caption" => t("Kustuta")
					)),
					"folder" => $o->path_str(array(
						"start_at" => $fldo->id(),
						"path_only" => true
					))
				);
			}
			else
			{
				$data[] = array(
					"name" => html::textbox(array(
						"name" => "fups_d[$idx][tx_name]",
						"size" => 15
					)),
					"file" => html::fileupload(array(
						"name" => "fups_".$idx
					)),
					"type" => html::select(array(
						"options" => $types,
						"name" => "fups_d[$idx][type]"
					)),
					"del" => "",
					"folder" => html::select(array(
						"name" => "fups_d[$idx][folder]",
						"options" => $folders
					))
				);
			}
		}

		classload("vcl/table");
		$t = new vcl_table(array(
			"layout" => "generic",
		));
		
		$t->define_field(array(
			"caption" => t("Nimi"),
			"name" => "name",
		));

		$t->define_field(array(
			"caption" => t("Fail"),
			"name" => "file",
		));

		$t->define_field(array(
			"caption" => t("T&uuml;&uuml;p"),
			"name" => "type",
		));

		$t->define_field(array(
			"caption" => t("Kataloog"),
			"name" => "folder",
		));

		$t->define_field(array(
			"caption" => t(""),
			"name" => "del",
		));

		foreach($data as $e)
		{
			$t->define_data($e);
		}

		$arr["prop"]["value"] = $t->draw();
	}

	function _set_files($arr)
	{
		$t = obj($arr["request"]["id"]);
		$u = new user();
		$co = obj($u->get_current_company());
		foreach(safe_array($_POST["fups_d"]) as $num => $entry)
		{
			if (is_uploaded_file($_FILES["fups_".$num]["tmp_name"]))
			{
				$f = get_instance("applications/crm/crm_company_docs_impl");
				$fldo = $f->_init_docs_fld($co);
				if ($this->can("add", $entry["folder"]))
				{
					$fldo = obj($entry["folder"]);
				}
				if (!$fldo)
				{
					return;
				}

				if ($entry["type"] == CL_FILE)
				{
					// add file
					$f = new file();

					$fs_fld = null;
					if (strpos($entry["folder"], ":") !== false)
					{
						list($sf_id, $sf_path) = explode(":", $entry["folder"]);
						$sf_o = obj($sf_id);
						$fs_fld = $sf_o->prop("folder").$sf_path;
					}
					$fil = $f->add_upload_image("fups_$num", $fldo->id(), 0, $fs_fld);

					if (is_array($fil))
					{
						$t->connect(array(
							"to" => $fil["id"],
							"reltype" => "RELTYPE_FILE"
						));
					}
				}
				else
				{
					$o = obj();
					$o->set_class_id($entry["type"]);
					$o->set_name($entry["tx_name"] != "" ? $entry["tx_name"] : $_FILES["fups_$num"]["name"]);

			
					$o->set_parent($fldo->id());
					if ($entry["type"] != CL_FILE)
					{
						$o->set_prop("project", $t->id());
						$o->set_prop("orderer", reset($t->prop("orderer")));
					}
					$o->save();

					// add file
					$f = new file();

					$fs_fld = null;
					if (strpos($entry["folder"], ":") !== false)
					{
						list($sf_id, $sf_path) = explode(":", $entry["folder"]);
						$sf_o = obj($sf_id);
						$fs_fld = $sf_o->prop("folder").$sf_path;
					}
					$fil = $f->add_upload_image("fups_$num", $o->id(), 0, $fs_fld);

					if (is_array($fil))
					{
						$o->connect(array(
							"to" => $fil["id"],
							"reltype" => "RELTYPE_FILE"
						));
						$t->connect(array(
							"to" => $o->id(),
							"reltype" => "RELTYPE_FILE"
						));
					}
				}
			}
		}
		return $arr["post_ru"];
	}
	/**
	@attrib name=del_file_rel all_args=1
	**/
	function del_file_rel($arr)
	{
		$offer = obj($arr["from"]);
		$offer->disconnect(array(
			"from" => $arr["fid"],
		));

		return $arr['return_url'];
	}

	function _req_get_folders($ot, &$folders, $parent)
	{
		$this->_req_level++;
		$objs = $ot->level($parent);
		foreach($objs as $o)
		{
			$folders[$o->id()] = str_repeat("&nbsp;&nbsp;&nbsp;", $this->_req_level).$o->name();
			$this->_req_get_folders($ot, $folders, $o->id());
		}
		$this->_req_level--;
	}

	function _req_get_s_folders($fld, $fldo, &$folders, $parent)
	{
		$this->_lv++;
		foreach($fld as $dat)
		{
			if ($dat["parent"] === $parent)
			{
				$folders[$fldo->id().":".$dat["id"]] = str_repeat("&nbsp;&nbsp;&nbsp;", $this->_lv).iconv("utf-8", aw_global_get("charset")."//IGNORE", $dat["name"]);
				$this->_req_get_s_folders($fld, $fldo, $folders, $dat["id"]);
			}
		}
		$this->_lv--;
	}

	/**
		@attrib name=remove_parts all_args=1
	**/
	function remove_parts($arr)
	{
		$obj = obj($arr["id"]);
		foreach($arr["remove"] as $id)
		{
			$obj->disconnect(array(
				"from" => $id,
			));
		}
		return $arr["post_ru"];
	}
	
	/**
		@attrib name=save_parts all_args=1
	**/
	function save_parts($arr)
	{
		$i = get_instance("applications/crm/crm_document_base");
		$i->_save_acts(array("request" => $arr, "obj_inst" => obj($arr["id"])));
		return $arr["post_ru"];
	}
	
	function do_db_upgrade($table, $field, $query, $error)
	{
		if (empty($field))
		{
			$this->db_query('CREATE TABLE '.$table.' (oid INT PRIMARY KEY NOT NULL)');
			return true;
		}

		switch ($field)
		{
			case 'accept_deadline':
			case 'shipment_deadline':
			case 'orderer_contact_person':
			case 'order_type':
				$this->db_add_col($table, array(
					'name' => $field,
					'type' => 'int'
				));
                                return true;
                }

		return false;
	}

	//see peab hakkama orderer muutujast tulevaid t88tajaid v6tma
	/**
		@attrib name=customer_contact_person_options_autocomplete_source all_args=1
	**/
	function customer_contact_person_options_autocomplete_source($arr)
	{
		$ac = get_instance("vcl/autocomplete");
		$arr = $ac->get_ac_params($arr);

		$ol = new object_list(array(
			"class_id" => CL_CRM_COMPANY,
			"name" => "%".$arr["orderer"]."%",
			"lang_id" => array(),
			"site_id" => array(),
			"limit" => 200
		));
		$emp = new object_list();
		foreach($ol->arr() as $o)
		{
			$emp->add($o->get_employees());
		}
		return $ac->finish_ac($arr);
	}

}
?>
