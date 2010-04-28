<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/groupware/task_quick_entry.aw,v 1.49 2009/02/05 09:31:49 kristo Exp $
// task_quick_entry.aw - Kiire toimetuse lisamine 
/*

@classinfo syslog_type=ST_TASK_QUICK_ENTRY no_comment=1 no_status=1 prop_cb=1 maintainer=markop

@groupinfo general caption=&Uuml;ldine default=1 icon=edit focus=customer

@default table=objects
@default group=general

@layout general_split type=hbox
@layout general_up type=vbox closeable=1 area_caption=&Uuml;ldinfo parent=general_split
@default parent=general_up


@property date type=datetime_select store=no
@caption Aeg

@property orderer type=textbox store=no
@caption Tellija

@property cust_type type=select
@caption Kliendi t&uuml;&uuml;p

@property customer type=textbox store=no
@caption Klient

//see selleks, et saaks vaid valida klient, kuid mitte lisada
@property customer_name type=textbox store=no
@caption Klient - vaid olemasolev mitte lisamiseks

@property custp_fn type=textbox
@caption Eesnimi

@property custp_ln type=textbox
@caption Perenimi

@property project type=textbox store=no
@caption Projekt

@property task type=textbox store=no
@caption Toimetus

@property duration type=textbox store=no size=5
@caption Kestvus

@property content type=textarea store=no rows=10 cols=50
@caption Sisu

@property submit_but type=submit 
@caption Salvesta

@property submit_and_add type=text 
@caption &nbsp;

	
@layout general_down type=vbox closeable=1 area_caption=Osalejad parent=general_split
@default parent=general_down


@property parts type=chooser multiple=1 orient=vertical
@caption Osalejad


*/

class task_quick_entry extends class_base
{
	const AW_CLID = 1070;

	function task_quick_entry()
	{
		$this->init(array(
			"tpldir" => "applications/groupware/task_quick_entry",
			"clid" => CL_TASK_QUICK_ENTRY
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "orderer":
				$prop["autocomplete_source"] = $this->mk_my_orb("orderer_autocomplete_source");
				$prop["autocomplete_params"] = array("customer", "customer_name", "orderer");
				break;
			case "customer_name":
				$prop["autocomplete_class_id"] = array(CL_CRM_PERSON, CL_CRM_COMPANY);
				$this->customer_name_set = 1;
				break;
			case "submit_but":
				$prop["type"] = "button";
				$prop["class"] = "sbtbutton";
				$prop["onclick"] = "
					if (aw_submit_handler() == false)
					{
						document.getElementById('button').disabled=false;
						return false;
					}
					submit_changeform('');";
				break;
			case "submit_and_add":
				$prop["value"] = html::button(array(
					"name" => "submit_and_add",
					"value" => t("Salvesta ja lisa uus"),
					"class" => "sbtbutton",
					"onclick" => "
						ret = aw_submit_handler();
						if (ret == false)
						{
							document.getElementById('button').disabled=false;
							return false;
						}
						document.changeform.button_p.value=1;submit_changeform('');"
				));
				break;

			case "name":
				return PROP_IGNORE;

			case "customer":
				$prop["autocomplete_source"] = $this->mk_my_orb("cust_autocomplete_source");
				$prop["autocomplete_params"] = array("customer");
				$this->customer_set = 1;
				break;

			case "project":
				$prop["autocomplete_source"] = $this->mk_my_orb("proj_autocomplete_source");
				$prop["autocomplete_params"] = array("customer_name", "project");
				if($this->customer_set) 
				$prop["autocomplete_params"] = array("customer", "project");
				break;

			case "task":
				$prop["autocomplete_source"] = $this->mk_my_orb("task_autocomplete_source");
				$prop["autocomplete_params"] = array("customer_name", "project", "task");
				if($this->customer_set) 
				$prop["autocomplete_params"] = array("customer", "project", "task");
				break;
			
			case "custp_ln":
				$prop["autocomplete_source"] = $this->mk_my_orb("name_autocomplete_source");
				$prop["autocomplete_params"] = array("custp_fn" , "custp_ln");
				break;
			
			case "cust_type":
				if($this->customer_name_set)
				{
					return PROP_IGNORE;
				}
				$prop["options"] = array(CL_CRM_COMPANY => t("Organisatsioon"), CL_CRM_PERSON => t("Isik"));
				$prop["onchange"] = "if (navigator.userAgent.toLowerCase().indexOf('msie')>=0)
					{
						d = 'block';
					}
					else 
					{
						d = 'table-row';
					}
					if (this.selectedIndex == 1) {
						document.getElementById('custp_fn').parentNode.parentNode.style.display = d;
						document.getElementById('custp_ln').parentNode.parentNode.style.display = d;
						document.getElementById('customer').parentNode.parentNode.style.display = 'none';
//						document.getElementById('cust_nAWAutoCompleteTextbox').parentNode.parentNode.style.display = 'none';
					}
					else 
					{
						document.getElementById('customer').parentNode.parentNode.style.display = d;
						document.getElementById('custp_fn').parentNode.parentNode.style.display = 'none';
						document.getElementById('custp_ln').parentNode.parentNode.style.display = 'none';
//						document.getElementById('cust_nAWAutoCompleteTextbox').parentNode.parentNode.style.display = d;
					}";
				break;
			
			case "ettevotlusvorm":
				$ol = new object_list(array(
					"class_id" => CL_CRM_CORPFORM,
					"lang_id" => array(),
					"site_id" => array()
				));
				$prop["options"] = array("" => t("--Vali--")) + $ol->names();
				break;

			case "cust_n":
				$prop["autocomplete_source"] = "/automatweb/orb.aw?class=crm_company&action=name_autocomplete_source";
				$prop["autocomplete_params"] = array("cust_n");
				break;

			case "parts":
				$co = get_instance(CL_CRM_COMPANY);
				$prop["options"] = $co->get_employee_picker(null, false, true);
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
			case "customer_name":
				if(($arr["request"]["custp_fn"] || $arr["request"]["custp_ln"] || $arr["request"]["customer"])) return $retval;
			case "customer":
				if(($arr["request"]["custp_fn"] || $arr["request"]["custp_ln"] || $arr["request"]["customer_name"])) return $retval;
			case "custp_fn":
				if($arr["request"]["customer"] || $arr["request"]["customer_name"]) return $retval;
			case "custp_ln":
				if($arr["request"]["customer"] || $arr["request"]["customer_name"]) return $retval;
			case "project":
			case "content":
//			case "orderer":
			case "duration":
				if ($prop["value"] == "")
				{
					if(!$this->empty_field_error) $prop["error"] = t("K&otilde;ik v&auml;ljad peavad olema t&auml;idetud!");$prop["error"] = $prop["name"];
					$this->empty_field_error = 1;
					return PROP_FATAL_ERROR;
				}
				break;
		}
		return $retval;
	}	

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
		$arr["button_p"] = 0;
	}

	/**
		@attrib name=cust_autocomplete_source
		@param customer optional
	**/
	function cust_autocomplete_source($arr)
	{
list($usec, $sec) = explode(" ", microtime());
$start = ((float)$usec + (float)$sec);
		$cl_json = get_instance("protocols/data/json");

		$errorstring = "";
		$error = false;
		$autocomplete_options = array();

		$option_data = array(
			"error" => &$error,// recommended
			"errorstring" => &$errorstring,// optional
			"options" => &$autocomplete_options,// required
			"limited" => false,// whether option count limiting applied or not. applicable only for real time autocomplete.
		);

		$ol = new object_list(array(
			"class_id" => array(CL_CRM_COMPANY),
			"name" => iconv("UTF-8", aw_global_get("charset"), $arr["customer"])."%",
			"lang_id" => array(),
			"site_id" => array(),
			"limit" => 500,
//			new object_list_filter(array(
//				"logic" => "OR",
//				"conditions" => array(
//					"class_id" => CL_CRM_COMPANY,
//					"is_customer" => 1
//				)
//			))
		));
		$autocomplete_options = $ol->names();
		foreach($autocomplete_options as $k => $v)
		{
			$autocomplete_options[$k] = iconv(aw_global_get("charset"), "UTF-8", parse_obj_name($v));
		}
		$asd = $cl_json->encode($option_data);
		list($usec, $sec) = explode(" ", microtime());
		$end = ((float)$usec + (float)$sec);
		
		$autocomplete_options[0] = $arr["customer"].substr(($end - $start), 0, 6);ksort($autocomplete_options);
		header("Content-type: text/html; charset=utf-8");
		exit ($cl_json->encode($option_data));
	}

	/**
		@attrib name=proj_autocomplete_source
		@param customer optional
		@param customer_name optional
		@param project optional
	**/
	function proj_autocomplete_source($arr)
	{
		$cl_json = get_instance("protocols/data/json");
		if(!$arr["customer"])
		{
			$arr["customer"] = $arr["customer_name"];
		}
		$errorstring = "";
		$error = false;
		$autocomplete_options = array();

		$option_data = array(
			"error" => &$error,// recommended
			"errorstring" => &$errorstring,// optional
			"options" => &$autocomplete_options,// required
			"limited" => false,// whether option count limiting applied or not. applicable only for real time autocomplete.
		);
		$ol = new object_list(array(
			"class_id" => array(CL_PROJECT),
			"name" => iconv("UTF-8", aw_global_get("charset"), $arr["project"])."%",
			"CL_PROJECT.RELTYPE_ORDERER.name" => iconv("UTF-8", aw_global_get("charset"), $arr["customer"])."%",
			"lang_id" => array(),
			"site_id" => array(),
			"state" => new obj_predicate_not(PROJ_DONE)
		));
		$autocomplete_options = $ol->names();
             $autocomplete_options = $ol->names();
                foreach($autocomplete_options as $k => $v)
                {
                        $autocomplete_options[$k] = iconv(aw_global_get("charset"), "UTF-8", parse_obj_name($v));
                }
		header("Content-type: text/html; charset=utf-8");
		exit ($cl_json->encode($option_data));
	}

	/**
		@attrib name=orderer_autocomplete_source
		@param customer optional
		@param customer_name optional
		@param orderer optional
	**/
	function orderer_autocomplete_source($arr)
	{
		$cl_json = get_instance("protocols/data/json");
		if(!$arr["customer"])
		{
			$arr["customer"] = $arr["customer_name"];
		}
		$errorstring = "";
		$error = false;
		$autocomplete_options = array();

		$option_data = array(
			"error" => &$error,// recommended
			"errorstring" => &$errorstring,// optional
			"options" => &$autocomplete_options,// required
			"limited" => false,// whether option count limiting applied or not. applicable only for real time autocomplete.
		);
		$customers = new object_list(array(
			"class_id" => array(CL_CRM_COMPANY),
			"name" => iconv("UTF-8", aw_global_get("charset"), $arr["customer"])."%",
			"lang_id" => array(),
			"site_id" => array(),
			"limit" => 3,
		));
		$orderers = new object_list();
		foreach($customers->arr() as $cust)
		{
			$orderers->add($cust->get_workers());
		}

		$ol = new object_list(array(
			"class_id" => array(CL_PERSON),
			"name" => iconv("UTF-8", aw_global_get("charset"), $arr["orderer"])."%",
			"oid" => $orderers->ids(),
			"lang_id" => array(),
			"site_id" => array(),
		));
		$autocomplete_options = $orderers->names();

                foreach($autocomplete_options as $k => $v)
                {
                        $autocomplete_options[$k] = iconv(aw_global_get("charset"), "UTF-8", parse_obj_name($v));
                }
		header("Content-type: text/html; charset=utf-8");
		exit ($cl_json->encode($option_data));
	}


	/**
		@attrib name=name_autocomplete_source
		@param custp_fn optional
		@param custp_ln optional
	**/
	function name_autocomplete_source($arr)
	{
		$cl_json = get_instance("protocols/data/json");

		$errorstring = "";
		$error = false;
		$autocomplete_options = array();

		$option_data = array(
			"error" => &$error,// recommended
			"errorstring" => &$errorstring,// optional
			"options" => &$autocomplete_options,// required
			"limited" => false,// whether option count limiting applied or not. applicable only for real time autocomplete.
		);

		$ol = new object_list(array(
			"class_id" => array(CL_CRM_PERSON),
			"lastname" => iconv("UTF-8", aw_global_get("charset"), $arr["custp_ln"])."%",
			"firstname" => iconv("UTF-8", aw_global_get("charset"), $arr["custp_fn"])."%",
			"lang_id" => array(),
			"site_id" => array(),
		));
		$autocomplete_options = $ol->names();
                foreach($ol->arr() as $k => $v)
               	{ 
                        $autocomplete_options[$k] = iconv(aw_global_get("charset"), "UTF-8", $v->prop("lastname"));
                }
		header("Content-type: text/html; charset=utf-8");
		exit ($cl_json->encode($option_data));
	}



	/**
		@attrib name=task_autocomplete_source
		@param customer optional
		@param customer_name optional
		@param project optional
		@param task optional
	**/
	function task_autocomplete_source($arr)
	{
		$cl_json = get_instance("protocols/data/json");
		if(!$arr["customer"])
		{
			$arr["customer"] = $arr["customer_name"];
		}
		$errorstring = "";
		$error = false;
		$autocomplete_options = array();

		$option_data = array(
			"error" => &$error,// recommended
			"errorstring" => &$errorstring,// optional
			"options" => &$autocomplete_options,// required
			"limited" => false,// whether option count limiting applied or not. applicable only for real time autocomplete.
		);
		$ol = new object_list(array(
			"class_id" => array(CL_TASK),
			"CL_TASK.project.name" =>  iconv("UTF-8", aw_global_get("charset"), $arr["project"])."%",
			"CL_TASK.customer.name" => iconv("UTF-8", aw_global_get("charset"), $arr["customer"])."%",
			"name" => $arr["task"]."%",
			"lang_id" => array(),
			"site_id" => array(),
			"is_done" => new obj_predicate_not(8),
			"brother_of" => new obj_predicate_prop("id")
		));
		$autocomplete_options = $ol->names();
             $autocomplete_options = array(); //$ol->names();
                foreach($ol->names() as $k => $v)
                {
			$v = iconv(aw_global_get("charset"), "UTF-8", $v);
			if (!in_array($v, $autocomplete_options))
			{
                        	$autocomplete_options[$k] = parse_obj_name($v);
                	}
		}
		header("Content-type: text/html; charset=utf-8");
		exit ($cl_json->encode($option_data));
	}

	function callback_pre_save($arr)
	{
		// find the task referenced and add row to it.
		// if needed add customer/project/task
		$cur_co = get_current_company();
		$cur_p = get_current_person();
		/*
		$tcust = $arr["request"]["customer"];
		if (mb_detect_encoding($arr["request"]["customer"], "UTF-8,ISO-8859-1") == "UTF-8")
		{
			$arr["request"]["customer"] = iconv("UTF-8", aw_global_get("charset")."//TRANSLIT", $arr["request"]["customer"]);
			if ($arr["request"]["customer"] == "")
			{
				$arr["request"]["customer"] = $tcust;
			}
		}*/

		$tproj = $arr["request"]["project"];


		/*if (mb_detect_encoding($arr["request"]["project"], "UTF-8,ISO-8859-1") == "UTF-8")
		{
			$arr["request"]["project"] = iconv("UTF-8", aw_global_get("charset")."//TRANSLIT", $arr["request"]["project"]);
			if ($arr["request"]["project"] == "")
			{
				$arr["request"]["project"] = $tproj;
			}
		}*/

		$ttask = $arr["request"]["task"];
		/*if (mb_detect_encoding($arr["request"]["task"], "UTF-8,ISO-8859-1") == "UTF-8")
		{
			$arr["request"]["task"] = iconv("UTF-8", aw_global_get("charset")."//TRANSLIT", $arr["request"]["task"]);
			if ($arr["request"]["task"] == "")
			{
				$arr["request"]["task"] = $ttask;
			}
		}*/
		if ($arr["request"]["task"] == "")
		{
			$arr["request"]["task"] = $arr["request"]["project"];
		}
		//$arr["request"]["content"] = iconv("UTF-8", aw_global_get("charset"), $arr["request"]["content"]);
		$arr["request"]["duration"] = str_replace(",", ".", $arr["request"]["duration"]);
		
		//et ta kuramus IEga topelt ei teeks mingi valemiga
		$rows = new object_list(array(
			"class_id" => array(CL_TASK_ROW),
			"createdby" => aw_global_get("uid"),
			"lang_id" => array(),
			"site_id" => array(),
			"content" => $arr["request"]["content"],
			"created" =>  new obj_predicate_compare(OBJ_COMP_GREATER_OR_EQ, (time() - 10)),
		));

	if(!(sizeof($rows->ids()) > 0)) {
		if($arr["request"]["customer_name"])//sellise propi puhul lubab kasutada selle nimelist klienti kuid mitte lisada
		{
			$ol = new object_list(array(
				"class_id" => array(CL_CRM_COMPANY,CL_CRM_PERSON),
				"name" => $arr["request"]["customer_name"],
				"lang_id" => array(),
				"site_id" => array()
			));
			$c = $ol->begin();
			if(!is_object($c) || strlen($arr["request"]["customer_name"]) < 2)
			{
				return;
				//MIS SIIS TEHA KUI SELLIST POLE?
			}
		}
		elseif($arr["request"]["customer"])
		{
			$ol = new object_list(array(
				"class_id" => array(CL_CRM_COMPANY),
				"name" => $arr["request"]["customer"],
				"lang_id" => array(),
				"site_id" => array()
			));
			if (!$ol->count())
			{
				$c = obj();
				$c->set_class_id(CL_CRM_COMPANY);
				$c->set_parent($cur_co->parent());
				$c->set_name($arr["request"]["customer"]);
				$c->save();
				$cur_co->connect(array(
					"type" => "RELTYPE_CUSTOMER",
					"to" => $c->id()
				));
			}
			else
			{
				$c = $ol->begin();
			}
		}
		else
		{
			$ol = new object_list(array(
				"class_id" => array(CL_CRM_PERSON),
			//	"name" => $arr["request"]["customer"],
				"firstname" => $arr["request"]["custp_fn"],
				"lastname" => $arr["request"]["custp_ln"],
				"lang_id" => array(),
				"site_id" => array()
			));
			if (!$ol->count())
			{
				$c = obj();
				$c->set_class_id(CL_CRM_PERSON);
				$c->set_parent($cur_co->parent());
				$c->set_name($arr["request"]["custp_fn"].' '.$arr["request"]["custp_ln"]);
				$c->set_prop("firstname", $arr["request"]["custp_fn"]);
				$c->set_prop("lastname" , $arr["request"]["custp_ln"]);
				$c->set_prop("is_customer" , 1);
				$c->save();
				$cur_co->connect(array(
					"type" => "RELTYPE_CUSTOMER",
					"to" => $c->id()
				));
			}
			else
			{
				$c = $ol->begin();
			}
			$arr["request"]["customer"] = $arr["request"]["custp_fn"].' '.$arr["request"]["custp_ln"];
		}
		
		// if project exists
		$ol = new object_list(array(
			"class_id" => array(CL_PROJECT),
			"name" => $arr["request"]["project"],
			"CL_PROJECT.RELTYPE_ORDERER" => $c->id(),
			"lang_id" => array(),
			"site_id" => array(),
			"state" => new obj_predicate_not(PROJ_DONE)
		));
		if (!$ol->count())
		{
			$p = obj();
			$p->set_class_id(CL_PROJECT);
			$p->set_parent($cur_co->parent());
			$p->set_name($arr["request"]["project"]);
			$p->set_prop("orderer", array($c->id(), $c->id()));
			$p->set_prop("implementor", $cur_co->id());
			$p->set_prop("participants", array($cur_p->id() => $cur_p->id()));
			$p->save();
		}
		else
		{
			$p = $ol->begin();
		}

		// if orderer exists
		$ol = new object_list(array(
			"class_id" => array(CL_CRM_PERSON),
			"name" => $arr["request"]["orderer"],
			"lang_id" => array(),
			"site_id" => array(),
		));
		if (!$ol->count())
		{
			$o = obj();
			$o->set_class_id(CL_CRM_PERSON);
			$o->set_parent($c->id());
			$o->set_name($arr["request"]["orderer"]);
			$o->save();
		}
		else
		{
			$o = $ol->begin();
		}
		
		// if task exists
		$ol = new object_list(array(
			"class_id" => array(CL_TASK),
			"name" => $arr["request"]["task"],
			"CL_TASK.project" => $p->id(),
			"CL_TASK.customer" => $c->id(),
			"lang_id" => array(),
			"site_id" => array(),
			"is_done" => new obj_predicate_not(8),
			"brother_of" => new obj_predicate_prop("id")
		));
		if (!$ol->count())
		{
			// set stuff as task props
			$t = obj();
			$t->set_class_id(CL_TASK);
			$t->set_parent($cur_co->parent());
			$t->set_name($arr["request"]["task"]);
			$t->set_prop("customer", $c->id());
			$t->set_prop("project", $p->id());
			$t->set_prop("start1", date_edit::get_timestamp($arr["request"]["date"]));
			$t->set_prop("end", date_edit::get_timestamp($arr["request"]["date"]) + $arr["request"]["duration"]*3600);
			$t->set_prop("content", $arr["request"]["content"]);
			$t->save();

			$t_i = $t->instance();
			$t_i->add_participant($t, $cur_p);

			foreach(safe_array($arr["request"]["parts"]) as $part)
			{
				$t_i->add_participant($t, obj($part));
			}


			$t_i->get_property(array(
				"prop" => array(
					"name" => "hr_price",
				),
				"obj_inst" => $t
			));

			// add row to task
//			$r = obj();
//			$r->set_class_id(CL_TASK_ROW);
//			$r->set_parent($t->id());
			$r = $t->add_row();
			if($arr["request"]["orderer"])
			{
				$r->set_prop("orderer", $o->id());
			}
			$r->set_prop("content", $arr["request"]["content"]);
			$r->set_prop("date", date_edit::get_timestamp($arr["request"]["date"]));
			$r->set_prop("time_guess", $arr["request"]["duration"]);
			$r->set_prop("time_real", $arr["request"]["duration"]);
			$r->set_prop("time_to_cust", $arr["request"]["duration"]);
			$r->set_prop("done", 1);
			$r->set_prop("on_bill", 1);
			$r->set_prop("impl", $cur_p->id());
			$r->set_prop("ord", $max_ord);
			$r->save();

//			$t->connect(array(
//				"to" => $r->id(),
//				"type" => "RELTYPE_ROW"
//			));
		}
		else
		{
			$t = $ol->begin();
			$t_i = $t->instance();
			//jarjekorranumbri andmine
			$max_ord = 0;
			foreach($t->connections_from(array("type" => 7)) as $row)
			{
				if($this->can("view", $row->prop("to")))
				{
					$row_obj = obj($row->prop("to"));
					if(($row_obj->prop("ord") >= $max_ord) || ($row_obj->prop("ord") == null && $max_ord == 0)) $max_ord = $row_obj->prop("ord") + 10;
				}
			}
			// add row to task
			$r = $t->add_row();
//			$r->set_class_id(CL_TASK_ROW);
//			$r->set_parent($t->id());

			if($arr["request"]["orderer"])
			{
				$r->set_prop("orderer", $o->id());
			}
			
			$r->set_prop("content", $arr["request"]["content"]);
			$r->set_prop("date", date_edit::get_timestamp($arr["request"]["date"]));
			$r->set_prop("time_guess", $arr["request"]["duration"]);
			$r->set_prop("time_real", $arr["request"]["duration"]);
			$r->set_prop("time_to_cust", $arr["request"]["duration"]);
			$r->set_prop("done", 1);
			$r->set_prop("on_bill", 1);
			$r->set_prop("impl", $cur_p->id());
			$r->set_prop("ord", $max_ord);
			$r->save();
			foreach(safe_array($arr["request"]["parts"]) as $part)
			{
				$t_i->add_participant($t, obj($part));
			}

//			$t->connect(array(
//				"to" => $r->id(),
//				"type" => "RELTYPE_ROW"
//			));
		}
		$t_id = $t->id();
	}
	else
	{
		$row = reset($rows->arr());
		$t_id = $row->prop("task");
	}
		if ($arr["request"]["submit_and_add"] != "" || $arr["request"]["button_p"])
		{
			header("Location: ".$arr["request"]["post_ru"]);
			die();
		}
		else
		{//die(html::get_change_url($t_id, array("group" => "rows", "return_url" => "javascript:history.go(-1)")));
			die("<script language=javascript>window.opener.location='"
			.html::get_change_url($t_id, array("group" => "rows", "return_url" => "javascript:history.go(-1)")).
			"';window.close();</script>");
		}
	}

	function callback_generate_scripts($arr)
	{
		$check_url = $this->mk_my_orb("is_there_customer", array("customer" => " "));
		$customer_check = "
			el_value = document.changeform.customer_name.value;
			if(el_value.length > 1)
			{
				el=aw_get_url_contents('".$check_url."'+escape(el_value));
				if(!(el>0))
				{
					alert('".t("klient").' '."' + el_value + '".' '.t("puudub andmebaasist!")."');
					return false;
				}
			}
		";
		return
		"function aw_submit_handler() {".
		"".
		(($this->customer_name_set) ? $customer_check : "").
		// fetch list of companies with that name and ask user if count > 0
		"var url = '".$this->mk_my_orb("check_existing")."';".
		(($this->customer_set) ?"url = url + '&c=' + escape(document.changeform.customer.value);" : "").
		"url = url + '&p=' + escape(document.changeform.project.value);".
		"url = url + '&t=' + escape(document.changeform.task.value);".
		"num= aw_get_url_contents(url);".
		"if (num != \"\")
		{
			var ansa = confirm(num);
			if (ansa)
			{
				return true;
			}
			return false;
		}".
		"return true;}"
.((!$this->customer_name_set) ? "
		if (navigator.userAgent.toLowerCase().indexOf('msie')>=0)
			{d = 'block';}
		else 
		{
			d = 'table-row';
		} 
		document.getElementById('customer').parentNode.parentNode.style.display = d;
		document.getElementById('custp_fn').parentNode.parentNode.style.display = 'none';
		document.getElementById('custp_ln').parentNode.parentNode.style.display = 'none';
//		document.getElementById('cust_nAWAutoCompleteTextbox').parentNode.parentNode.style.display = d;
		" : "");
	}

	/**
		@attrib name=is_there_customer
		@param customer optional
	**/
	function is_there_customer($arr)
	{
		$arr["customer"] = substr($arr["customer"],1);
		$ol = new object_list(array(
			"class_id" => array(CL_CRM_PERSON,CL_CRM_COMPANY),
			"lang_id" => array(),
			"site_id" => array(),
			"name" => $arr["customer"],
		));
		$res = sizeof($ol->ids());
		header("Content-type: text/html; charset=utf-8");
		exit ($res."");
	}


	/**
		@attrib name=check_existing
		@param c optional
		@param p optional
		@param t optional
	**/
	function check_existing($arr)
	{
		$ctmp = $arr["c"];
		/*if (mb_detect_encoding($arr["c"],"UTF-8,ISO-8859-1") == "UTF-8")
		{
			$arr["c"] = iconv("UTF-8", aw_global_get("charset"), $arr["c"]);
			if ($arr["c"] == "")
			{
				$arr["c"] = $ctmp;
			}
		}*/
		$ret = "";
		// if customer exists
		$ol = new object_list(array(
			"class_id" => array(CL_CRM_COMPANY, CL_CRM_PERSON),
			"name" => $arr["c"],
			"lang_id" => array(),
			"site_id" => array()
		));
		if (!$ol->count())
		{
			$ret .= sprintf(t("Klienti nimega %s ei ole olemas, kui vajutate ok, lisatakse\n"), $arr["c"]);
		}

		$ptmp = $arr["p"];
		/*if (mb_detect_encoding($arr["p"],"UTF-8,ISO-8859-1") == "UTF-8")
		{
			$arr["p"] = iconv("UTF-8", aw_global_get("charset"), $arr["p"]);
			if ($arr["p"] == "")
			{
				$arr["p"] = $ptmp;
			}
		}*/
		// if project exists
		$ol = new object_list(array(
			"class_id" => array(CL_PROJECT),
			"name" => $arr["p"],
			"lang_id" => array(),
			"site_id" => array(),
			"state" => new obj_predicate_not(PROJ_DONE)
		));
		if (!$ol->count())
		{
			$ret .= sprintf(t("Projekti nimega %s ei ole olemas, kui vajutate ok, lisatakse\n"), $arr["p"]);
		}

		$ttmp = $arr["t"];
		/*if (mb_detect_encoding($arr["t"],"UTF-8,ISO-8859-1") == "UTF-8")
		{
			$arr["t"] = iconv("UTF-8", aw_global_get("charset"), $arr["t"]);
			if ($arr["t"] == "")
			{
				$arr["t"] = $ttmp;
			}
		}*/
		// if task exists
		$ol = new object_list(array(
			"class_id" => array(CL_TASK),
			"name" => $arr["t"],
			"lang_id" => array(),
			"site_id" => array(),
			"is_done" => new obj_predicate_not(8),
			"brother_of" => new obj_predicate_prop("id")
		));
		if (!$ol->count())
		{
			$ret .= sprintf(t("Toimetust nimega %s ei ole olemas, kui vajutate ok, lisatakse\n"), $arr["t"]);
		}
		header("Content-type: text/html; charset=".aw_global_get("charset"));
		die(/*iconv(aw_global_get("charset"), "UTF-8",*/ ($ret));
	}
	
}
?>
