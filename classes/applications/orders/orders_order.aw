<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/orders/orders_order.aw,v 1.33 2008/07/14 12:49:21 markop Exp $
// orders_order.aw - Tellimus 
/*
@classinfo syslog_type=ST_ORDERS_ORDER relationmgr=yes maintainer=markop
@tableinfo orders_order index=oid master_table=objects master_index=brother_of 

@default table=orders_order
@default group=orderinfo

@property firstname type=textbox store=no
@caption Eesnimi

@property lastname type=textbox store=no
@caption Perekonnanimi

@property personal_id type=textbox store=no
@caption Isikukood

@property person_email type=textbox store=no
@caption E-mail

@property person_phone type=textbox store=no
@caption Telefon

@property person_contact type=textarea store=no 
@caption Aadress

@property person_birthday type=date_select year_from=1930 year_to=2010 default=-1 store=no
@caption S&uuml;nnip&auml;ev

@property order_completed type=hidden field=meta method=serialize table=objects
@property order_confirmed type=hidden field=meta method=serialize table=objects

@property udef_textbox1 type=textbox user=1
@property udef_textbox2 type=textbox user=1
@property udef_textbox3 type=textbox user=1
@property udef_textbox4 type=textbox user=1
@property udef_textbox5 type=textbox user=1
@property udef_textbox6 type=textbox user=1 field=meta method=serialize table=objects
@property udef_textbox7 type=textbox user=1 field=meta method=serialize table=objects


@property udef_textarea1 type=textarea user=1
@property udef_textarea2 type=textarea user=1
@property udef_textarea3 type=textarea user=1
@property udef_textarea4 type=textarea user=1
@property udef_textarea5 type=textarea user=1

@property udef_picker1 type=classificator user=1
@property udef_picker2 type=classificator user=1
@property udef_picker3 type=classificator user=1
@property udef_picker4 type=classificator user=1
@property udef_picker5 type=classificator user=1

@property udef_checkbox1 type=checkbox user=1 field=meta method=serialize table=objects

@property submit2 type=submit action=do_persondata_submit store=no group=orderinfo
@caption Kinnita tellimus

@property orderer_info type=text store=no group=orderitems
@caption Tellija

@property orders_table type=table store=no group=orderitems no_caption=1

@property submit_rows type=submit group=orderitems store=no
@caption Salvesta

@property childtitle1 type=text store=no subtitle=1 group=orderitems
@caption Uus tellimuse rida

@property orders type=releditor store=no props=name,product_code,product_color,product_size,product_count,product_count_undone,product_price reltype=RELTYPE_ORDER group=orderitems

@property add_order_button type=submit group=orderitems store=no no_caption=1
@caption Lisa tellimus

@property forward type=submit action=do_persondata_form group=orderitems store=no
@caption Edasi

@property info type=text store=no group=ordertext
@caption Tellimuse info

@reltype ORDER value=1 clid=CL_ORDERS_ITEM
@caption Tellitud asi

@reltype PERSON value=2 clid=CL_CRM_PERSON
@caption Tellija

@reltype CFGMANAGER value=3 clid=CL_CFGMANAGER
@caption Seadete haldur

@groupinfo orderinfo caption="Tellija andmed"
@groupinfo orderitems caption="Tellitud tooted"
@groupinfo ordertext caption="Tellimus"

*/

class orders_order extends class_base
{
	function orders_order()
	{
		// change this to the folder under the templates folder, where this classes templates will be, 
		// if they exist at all. Or delete it, if this class does not use templates
		$this->init(array(
			"tpldir" => "applications/orders",
			"clid" => CL_ORDERS_ORDER
		));

		$this->prod_statuses = array(
			"" => "t&auml;psustamisel",
			NULL => "t&auml;psustamisel",
			0 => "puudub",
			1 => "laos",
			2 => "pikk tarnet&auml;htaeg"
		);
	}
	
	function callback_on_load($arr)
	{
		if(is_oid($arr["request"]["id"]) && $this->can("view", $arr["request"]["id"]))
		{
			$obj = obj($arr["request"]["id"]);
			if($cfgmanager = $obj->get_first_conn_by_reltype("RELTYPE_CFGMANAGER"))
			{
				$this->cfgmanager = $cfgmanager->prop("to");
			}
		}
	}
	
	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "orders_table":
				$this->save_rows($arr);
				break;
		}
		return $retval;
	}	

	function save_rows($arr)
	{
		foreach($arr["request"]["rows"] as $row => $data)
                {
			if(!is_oid($row) || !$this->can("edit" , $row)) continue;
			$ro = obj($row);
			foreach($data as $prop => $val)
			{
				$ro->set_prop($prop , $val);
			}
			$ro->save();
                }
	}

	//////
	// class_base classes usually need those, uncomment them if you want to use them

	function callback_get_default_group($arr)
	{
		if ($_GET["action"] != "new")
		{
			return "ordertext";
		}
		return "general";
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		
		if (is_oid($arr["obj_inst"]->id()))
		{
			$person = $arr["obj_inst"]->get_first_obj_by_reltype('RELTYPE_PERSON');
		}
		switch($prop["name"])
		{
			case "forward":
				return PROP_IGNORE;
				break;
				
			case "firstname":
				if($person)
				{
					$prop["value"] = $person->prop("firstname");
				}
				break;
			
			case "orderer_info":
				$oi_data = array();
				if($person)
				{
				$oi_data[] = $person->name();
					if($person->prop("personal_id")) $oi_data[] = $person->prop("personal_id");
					if($person->prop("email")) $oi_data[] = $person->prop("email.mail");
					if($person->prop("phone")) $oi_data[] = $person->prop("phone.name");
					if($person->prop("comment")) $oi_data[] = $person->prop("comment");
				}	
				$prop["value"] = join (", " , $oi_data);
				break;
			
			case "info":
				$oi_data = array();
				$ordercfgform = $arr["obj_inst"]->meta("cfgform_id");
				$cfgform_i = get_instance(CL_CFGFORM);
				$props2 = $cfgform_i->get_props_from_cfgform(array("id" => $ordercfgform));

				if($person)
				{
					$oi_data[] = $person->prop("firstname") . " " .$person->prop("lastname");
					if($person->prop("personal_id")) $oi_data[] = (($props2["personal_id"]["caption"])?$props2["personal_id"]["caption"].": ":"").$person->prop("personal_id");
					if($person->prop("email")) $oi_data[] = (($props2["person_email"]["caption"])?$props2["person_email"]["caption"].": ":"").$person->prop("email.mail");
					if($person->prop("phone")) $oi_data[] = (($props2["person_phone"]["caption"])?$props2["person_phone"]["caption"].": ":"").$person->prop("phone.name");
					if($person->prop("comment")) $oi_data[] = (($props2["person_contact"]["caption"])?$props2["person_contact"]["caption"].": ":"").$person->prop("comment");
				}
				$x = 0;
				while($x < 8)
				{
					if($arr["obj_inst"]->prop("udef_textbox".$x))
					{
						$oi_data[] = (($props2["udef_textbox".$x]["caption"])?$props2["udef_textbox".$x]["caption"].": ":"").$arr["obj_inst"]->prop("udef_textbox".$x);
					}
					$x++;
				}

				$x = 0;
				while($x < 5)
				{
					if($arr["obj_inst"]->prop("udef_textarea".$x))
					{
						$oi_data[] = (($props2["udef_textarea".$x]["caption"])?$props2["udef_textarea".$x]["caption"].": ":"").$arr["obj_inst"]->prop("udef_textarea".$x);
					}
					$x++;
				}

				$prop["value"] = join ("<br>\n" , $oi_data);

				
				$table = new aw_table(array(
					"layout" => "generic"
				));
				$conns = $arr["obj_inst"]->connections_from(array(
					"type" => "RELTYPE_ORDER"
				));
				$ol = new object_list($conns);
				$o = $ol->begin();
				if(is_object($o)) $cfgform = $o->meta("cfgform_id");
				$this->_init_product_table(&$table, $cfgform);
				$this->define_table_data(&$table , $ol,1);
				$prop["value"].=$table->draw();
//arr($arr["obj_inst"]->meta());
				break;

			case "lastname":
				if($person)
				{
					$prop["value"] = $person->prop("lastname");
				}
				break;
			
			case "personal_id":
				if($person)
				{
					$prop["value"] = $person->prop("personal_id");
				}
				break;
				
			case "person_email":
				if($person && $person->prop("email"))
				{
					$email = &obj($person->prop("email"));
					$prop["value"] = $email->prop("mail");
				}
				break;
			
			case "person_phone":
				if($person && $person->prop("phone"))
				{
					$phone = &obj($person->prop("phone"));
					$prop["value"] = $phone->prop("name");
				}
				break;
				
			case "person_contact":
				if($person)
				{
					$prop["value"] = $person->prop("comment");
				}
				break;
			
			case "person_birthday":
				if ($person)
				{
					$birthday_parts = explode('-', $person->prop('birthday'));
					$prop["value"] = mktime(0, 0, 0, $birthday_parts[1], $birthday_parts[2], $birthday_parts[0]);
				}
				break;
			case "orders_table":
				$conns = $arr["obj_inst"]->connections_from(array(
					"type" => "RELTYPE_ORDER"
				));
				$ol = new object_list($conns);
				$o = $ol->begin();
				if(is_object($o)) $cfgform = $o->meta("cfgform_id");
				$table = &$prop["vcl_inst"];
				$this->_init_product_table(&$table, $cfgform);
				$this->define_table_data(&$table , $ol);
				break;
			case "submit":
				$prop["name"] = "add_order_button";
				break;
		};
		return $retval;
	}

	function _init_product_table(&$table, $cfgform)
	{
		if(is_oid($cfgform) && $this->can("view", $cfgform))
		{
			$cfgform_i = get_instance(CL_CFGFORM);
			$props2 = $cfgform_i->get_props_from_cfgform(array("id" => $cfgform));
			foreach($props2 as $prop2)
			{
				$table->define_field(array(
					"name" => $prop2["name"],
					"caption" => $prop2["caption"],
					"chgbgcolor"=>"color",
				));
			}
			$table->define_field(array(
				"name" => "product_count_undone",
				"caption" => t("Tarnimata kogus"),
				"chgbgcolor"=>"color",
			));
		}
		else
		{
			$table->define_field(array(
				"name" => "product_code",
				"caption" => t("Artikli kood"),
				"chgbgcolor"=>"color",
			));
			$table->define_field(array(
				"name" => "name",
				"caption" => t("Toode"),
				"chgbgcolor"=>"color",
			));
			$table->define_field(array(
				"name" => "product_count",
				"caption" => t("Kogus"),
				"chgbgcolor"=>"color",
			));
			$table->define_field(array(
				"name" => "product_size",
				"caption" => t("Tellitav_kogus"),
				"chgbgcolor"=>"color",
			));
			$table->define_field(array(
				"name" => "product_duedate",
				"caption" => t("Soovitav tarne t&auml;itmine"),
				"chgbgcolor"=>"color",
			));
			$table->define_field(array(
				"name" => "product_bill",
				"caption" => t("Tarne t&auml;itmine/arve nr"),
				"chgbgcolor"=>"color",
			));
			$table->define_field(array(
				"name" => "product_count_undone",
				"caption" => t("Tarnimata kogus"),
				"chgbgcolor"=>"color",
			));
			$table->define_field(array(
				"name" => "product_color",
				"caption" => t("V&auml;rvikaart"),
				"chgbgcolor"=>"color",
			));
			$table->define_field(array(
				"name" => "product_price",
				"caption" => t("Erihind"),
				"chgbgcolor"=>"color",
			));
		}
	}

	function define_table_data(&$table , $ol,$unchangable = 0)
	{
		foreach ($ol->arr() as $obj)
		{
			$data = array();
			foreach($obj->properties() as $prop => $val)
			{
				if($unchangable) $data[$prop] = $val;
				else
				{
					$data[$prop] = html::textbox(array(
						"name" => "rows[".$obj->id()."][".$prop."]",
						"value" => $val,
						"size" => ($prop == "name") ? 30:9,
					));
					$data["product_unit"] = html::textbox(array(
						"name" => "rows[".$obj->id()."][product_unit]",
						"value" => $obj->prop("product_unit"),
						"size" => ($prop == "name") ? 30:9,
					));
					if($prop == "product_duedate" || $prop == "product_bill")
					{
						$data[$prop].= '<a href="javascript:void(0);" onClick="var cal = new CalendarPopup();
	cal.select(changeform.rows_'.$obj->id().'__'.$prop.'_,\'anchor'.$obj->id().'\',\'dd/MM/yy\'); return false;" title="Vali kuup&auml;ev" name="anchor'.$obj->id().'" id="anchor'.$obj->id().'">vali</a>';
					}
				}
			}
			if($obj->prop("product_count_undone"))
			{
				$data["color"] = "#FFCCCC";
			}
			$table->define_data($data);
		}
	}

	function callback_post_save($arr)
	{
		$props = $arr["request"];
		if($props["firstname"] || $props["lastname"] || $props["person_id"] || $props["person_email"] || $props["person_phone"] || $props["person_contact"])
		{
			if($person = $arr["obj_inst"]->get_first_obj_by_reltype('RELTYPE_PERSON'))
			{
				$person->set_prop("firstname", $props["firstname"]);
				$person->set_prop("lastname", $props["lastname"]);
				$person->set_prop("personal_id", $props["personal_id"]);
				$person->set_prop("comment", $props["person_contact"]);
				
				$person->set_prop("birthday", sprintf("%04d-%02d-%02d", $props["person_birthday"]["year"], $props["person_birthday"]["month"], $props["person_birthday"]["day"]));
				
				$person->save();
				
				if($props["person_email"])
				{
					$email = obj();
					$email->set_parent($person->id());
					$email->set_class_id(CL_ML_MEMBER);
					$email->set_prop("mail", $props["person_email"]);
					$email->save();
					
					$person->set_prop("email", $email->id());
					$person->save();
					$person->connect(array(
						"to" => $email->id(),
						"reltype" => "RELTYPE_EMAIL",
					));
				}
				
				if($props["person_phone"])
				{
					
					$phone = obj();
					$phone->set_parent($person->id());
					$phone->set_class_id(CL_CRM_PHONE);
					$phone->set_prop("name", $props["person_phone"]);
					$phone->save();
					
					$person->set_prop("phone", $phone->id());
					$person->save();
					$person->connect(array(
						"to" => $phone->id(),
						"reltype" => "RELTYPE_PHONE",
					));
				}
				
			}
			else
			{
				$person = obj();
				$person->set_parent($arr["obj_inst"]->id());
				$person->set_class_id(CL_CRM_PERSON);
						$person->set_name($props["firstname"]." ".$props["lastname"]);
				$person->set_prop("firstname", $props["firstname"]);
				$person->set_prop("lastname", $props["lastname"]);
				$person->set_prop("personal_id", $props["personal_id"]);
				$person->set_prop("comment", $props["person_contact"]);
				$person->set_prop("birthday", sprintf("%04d-%02d-%02d", $props["person_birthday"]["year"], $props["person_birthday"]["month"], $props["person_birthday"]["day"]));
				$person->save();
				
				if($props["person_email"])
				{
					$email = obj();
					$email->set_parent($person->id());
					$email->set_class_id(CL_ML_MEMBER);
					$email->set_prop("mail", $props["person_email"]);
					$email->save();
					
					$person->set_prop("email", $email->id());
					$person->save();
					$person->connect(array(
						"to" => $email->id(),
						"reltype" => "RELTYPE_EMAIL",
					));
				}
				
				if($props["person_phone"])
				{
					
					$phone = obj();
					$phone->set_parent($person->id());
					$phone->set_class_id(CL_CRM_PHONE);
					$phone->set_prop("name", $props["person_phone"]);
					$phone->save();
					
					$person->set_prop("phone", $phone->id());
					$person->save();
					$person->connect(array(
						"to" => $phone->id(),
						"reltype" => "RELTYPE_PHONE",
					));
				}
				
				$arr["obj_inst"]->connect(array(
					"to" => $person->id(),
					"reltype" => "RELTYPE_PERSON",
				));
			}
		}
	}
	
/**
	@attrib name=add_to_cart nologin=1 all_args=1
**/
	function add_to_cart($arr)
	{
		//This solutions sucks, but cant find better one now
		$arr["orders"] = safe_array($arr["orders"]);
		$num = false;
		foreach(safe_array($arr["orders"]) as $key => $val)
		{
			if(is_numeric($key))
			{
				$num = true;
			}
		}
		if(!$num)
		{
			$arr["orders"] = array($arr["orders"]);
		}
		$submit_data_a = new aw_array($arr["orders"]);
		foreach($submit_data_a->get() as $key => $submit_data)
		{
			$_tmp = $submit_data;
			$submit_data["class"] = "orders_item";
			$submit_data["group"] = "general";
			$submit_data["parent"] = $_SESSION["order_cart_id"];
			$oform = &obj($_SESSION["order_form_id"]);
			$check["cfgform_id"] = $oform->prop("itemform");
			$check["request"] = $submit_data;
			$errors = $this->validate_data($check);
			if(!$errors && strlen(implode("", $_tmp)) > 0)
			{
				if(is_oid($_SESSION["order_eoid"]) && $this->can("view", $_SESSION["order_eoid"]))
				{
					$oid = $_SESSION["order_eoid"];
				}
				else
				{
					$oid = null;
				}
				$item = obj($oid);
				$item->set_class_id(CL_ORDERS_ITEM);
				$item->set_parent($_SESSION["order_cart_id"]);
				$item->set_prop("name", $submit_data["name"]);
				$item->set_prop("product_code", $submit_data["product_code"]);
				$item->set_prop("product_color", $submit_data["product_color"]);
				$item->set_prop("product_size", $submit_data["product_size"]);
				$item->set_prop("product_count", $submit_data["product_count"]);
				$item->set_prop("product_unit", $submit_data["product_unit"]);
				$item->set_prop("comment", $submit_data["comment"]);
				$item->set_prop("product_count_undone", $submit_data["product_count"]);
				$item->set_prop("product_price", $submit_data["product_price"]);
				$item->set_prop("product_image", $submit_data["product_image"]);
				$item->set_prop("product_page", $submit_data["product_page"]);
				$item->set_prop("product_duedate", $submit_data["product_duedate"]);
				$item->set_prop("product_bill", $submit_data["product_bill"]);
				$item->set_prop("udef_textbox7", $submit_data["udef_textbox7"]);
				$item->set_prop("udef_textbox6", $submit_data["udef_textbox6"]);
				$item->set_prop("udef_textbox5", $submit_data["udef_textbox5"]);
				$item->set_prop("udef_textbox4", $submit_data["udef_textbox4"]);
				$item->set_prop("udef_textbox3", $submit_data["udef_textbox3"]);
				$item->set_prop("udef_textbox2", $submit_data["udef_textbox2"]);
				$item->set_prop("udef_textbox1", $submit_data["udef_textbox1"]);

				$item->set_meta("cfgform_id" , $_SESSION["order_item_form_id"]);
				$item->save();
				$conn = new connection();
				
				$conn->load(array(
					"from" => $_SESSION["order_cart_id"],
					"to" => $item->id(),
					"reltype" => 1,
				));
				$conn->save();
				unset($_SESSION["order_eoid"]);
			}
			else
			{
				$_SESSION["order_form_errors"]["items"][$key] = $errors;
				$_SESSION["order_form_values"][$key] = $arr["orders"][$key];
			}
		}

		return $this->mk_my_orb("change", array(
			"id" => $_SESSION["order_form_id"],
			"section" => $_SESSION["orders_section"],
			), CL_ORDERS_FORM);
	}
	
/**
	@attrib name=submit nologin=1 all_args=1
**/
	/*function submit($arr)
	{
		parent::submit($arr);
		/*return str_replace("orb.aw","",$this->mk_my_orb("change", 
			array(
				"id" => $_SESSION["order_form_id"],
				"group" => "ordering",
			), CL_ORDERS_FORM));
	}*/
	
/**
	@attrib name=do_persondata_form nologin=1
**/
	function do_persondata_form($arr)
	{
	
		return $this->mk_my_orb("change", 
			array(
				"id" => $_SESSION["order_form_id"],
				"group" => "ordering",
				"persondata" => 1,
				
			), CL_ORDERS_FORM);
	}
	
/**
	@attrib name=do_persondata_submit nologin=1 all_args=1
**/
	function do_persondata_submit($arr)
	{
		$oform = &obj($_SESSION["order_form_id"]);
		$arr["cfgform"] = $oform->prop("orderform");
		parent::submit($arr);
		$_SESSION["no_cache"] = 1;
		
		$_SESSION["orders_form"]["payment"]["type"] = $arr["payment_method"];

		$oform = &obj($_SESSION["order_form_id"]);
		$arr["cfgform"] = $oform->prop("orderform");
		$arr["cfgform_id"] = $arr["cfgform"];
		parent::submit($arr);

		$_SESSION["person_form_values"] = $arr;

		if(!$arr["udef_checkbox1"])
		{
			$_SESSION["udef_checkbox1_error"] = t("Tellimiseks peate n&ouml;ustuma tellimistingimustega!");
			$cv = aw_global_get("cb_values");
			$cv["udef_checkbox1"]["error"] = t("Tellimiseks peate n&otilde;ustuma tellimistingimustega");
			aw_session_set("cb_values", $cv);
		}


		if(aw_global_get("cb_values"))
		{	
			return $this->mk_my_orb("change", 
			array(
				"id" => $_SESSION["order_form_id"],
				"group" => "persondata",
			), CL_ORDERS_FORM);
			
		}

		// if use selected payent type as rent, go through the rent settings
		if ($arr["payment_method"] == "rent")
		{
			return $this->mk_my_orb("rent_step_1", array(
					"id" => $_SESSION["order_form_id"],
					"group" => "confirmpage",
					"section" => $_SESSION["orders_section"],
				)
			);
		}

		return $this->mk_my_orb("change", 
			array(
				"id" => $_SESSION["order_form_id"],
				"group" => "confirmpage",
				"section" => $_SESSION["orders_section"],
			), CL_ORDERS_FORM);
	}
	
	/**
		@attrib name=change nologin=1 all_args=1
	**//*
	function change($arr)
	{	
		//If admin side then dont use templates
		if(strstr($_SERVER['REQUEST_URI'],"/automatweb"))
		{
			return parent::change($arr);
		}
		
		$this->read_template("orders_order_item.tpl");
	
		return $this->parse();	
	}*/
	
	function send_mail_to_admin($admin_mail = NULL)
	{
		$form = obj($_SESSION["order_form_id"]);
		if ($admin_mail == NULL)
		{
			$admin_mail = $form->prop("orders_post_to");
		}
		$form_inst = get_instance(CL_ORDERS_FORM);
		$order = obj($_SESSION["order_cart_id"]);
		$vars = array("order" => $order);
		if($form->prop("no_pdata_check") == 1)
		{
			if(aw_global_get("uid") != "")
			{
				$user = obj(aw_global_get("uid_oid"));
				$person = $user->get_first_obj_by_reltype("RELTYPE_PERSON");
			}
			if(!$order->is_connected_to(array(
				"type" => "RELTYPE_PERSON",
				"to" => $person->id(),
			)))
			{
				$order->connect(array(
					"to" => $person->id(),
					"reltype" => "RELTYPE_PERSON",
				));
			}
			$vars["no_pdata_check"] = 1;
		}
		else
		{
			$person = $order->get_first_obj_by_reltype("RELTYPE_PERSON");
		}
		if (!$person)
		{
			return;
		}
		if($person && $person->prop("email"))
		{
			$person_email = obj($person->prop("email"));
			$froma = $person_email->prop("mail");
			$person_name = $person->name();
		}
		
			
		$_SESSION["show_order"] = 1;
		$vars["show_order"] = 1;
		$content = $form_inst->get_confirm_persondata($vars)."<br />".($_SESSION["orders_form"]["payment"]["type"] == "rent" ? $form_inst->get_rent_table() : $form_inst->get_cart_table());
		unset($_SESSION["show_order"]);
		
		$awm = get_instance("protocols/mail/aw_mail");

		$awm->create_message(array(
			"froma" => $froma,
			"subject" => $form->name(),
			"to" => $admin_mail,
			"body" => t("Kahjuks sinu meililugeja ei oska nidata HTML formaadis kirju"),
		));
		$awm->htmlbodyattach(array(
			"data" => $content,
		));
		if($form->prop("add_attach") == 1)
		{
			$awm->fattach(array(
				"contenttype" => "application/vnd.ms-excel",
				"name" => $order->id()."_".date("dmy")."_$person_name.xls",
				"content" => $content,
			));
		}
		$awm->gen_mail();
	}
	
	function send_mail_to_orderer()
	{
		$form = &obj($_SESSION["order_form_id"]);
		$mail_obj = &obj($form->prop("ordemail"));
		$order = &obj($_SESSION["order_cart_id"]);
		if($form->prop("no_pdata_check") == 1)
		{
			if(aw_global_get("uid") != "")
			{
				$user = obj(aw_global_get("uid_oid"));
				$person = $user->get_first_obj_by_reltype("RELTYPE_PERSON");
			}
		}
		else
		{
			$person = $order->get_first_obj_by_reltype('RELTYPE_PERSON');
		}
		if(!$person || !$person->prop("email"))
		{
			return;
		}
		$email = &obj($person->prop("email"));
		$mail_obj->set_prop("mto", $email->prop("mail"));
		$mail_obj->set_prop("mfrom", $form->prop("orders_post_from"));
		$mail_obj->save();
		$mail_inst = get_instance(CL_MESSAGE);	
		$mail_inst->send_message(array(
			"id" => $mail_obj->id(),
		));
	}
	
	/**
		@attrib name=send_order nologin=1
	**/
	function send_order($arr)
	{
		$order_form = &obj($_SESSION["order_form_id"]);
		$order = &obj($_SESSION["order_cart_id"]);
		if ($order->class_id() != CL_ORDERS_ORDER)
		{
			return;
		}
		$order->set_prop("order_completed", 1);
		if($order_form->prop("orders_to_mail"))
		{
			$this->send_mail_to_orderer();
		}
		$this->send_mail_to_admin();
		$order->save();
		unset($_SESSION["order_form_id"]);
		unset($_SESSION["order_cart_id"]);
		return aw_ini_get("baseurl")."/".$order_form->prop("thankudoc");
	}

	/**

		@attrib name=rent_step_1 nologin=1

		@param id required
		@param section required
	**/
	function rent_step_1($arr)
	{
		$this->read_template("rent_step_1.tpl");

		$of_i = get_instance(CL_ORDERS_FORM);
		$states = $of_i->get_states();	

		$o = obj($arr["id"]);
		$inf = $o->meta("rent_data");
		$item_types = array();
		foreach(safe_array($inf) as $idx => $dat)
		{
			$item_types[$idx] = $dat["type"];
		}

		// get items in cart
		$f = get_instance(CL_ORDERS_FORM);
		$items = $f->get_cart_items();

		foreach($items->arr() as $item)
		{
			$this->_insert_item_inf($item, $states);
			$this->vars(array(
				"item_types" => html::select(array(
					"name" => "rent_items[".$item->id()."]",
					"options" => $item_types,
					"selected" => $_SESSION["orders_form"]["payment"]["itypes"][$item->id()]
				))
			));

			$rent_item .= $this->parse("RENT_ITEM");
		}

		$this->vars(array(
			"RENT_ITEM" => $rent_item,
			"reforb" => $this->mk_reforb("submit_rent_step_1", $arr),
			"back" => $this->mk_my_orb("change", array("id" => $arr["id"], "section" => $arr["section"], "group" => "persondata"), "orders_form")
		));		

		return $this->parse();
	}

	function _insert_item_inf($item, $states = NULL)
	{
		$name = $item->name();
		if (false && isset($states[$item->prop("product_code")]))
		{
			$str = $this->prod_statuses[$states[$item->prop("product_code")]];
			$name = "<a href='javascript:void(0)' alt='$str' title='$str'>$name</a>";
		}
		$this->vars(array(
			"udef_textbox1" => $item->prop("udef_textbox1"),
			"udef_textbox2" => $item->prop("udef_textbox2"),
			"udef_textbox3" => $item->prop("udef_textbox3"),
			"udef_textbox4" => $item->prop("udef_textbox4"),
			"udef_textbox5" => $item->prop("udef_textbox5"),
			"udef_textbox6" => $item->prop("udef_textbox6"),
			"udef_textbox7" => $item->prop("udef_textbox7"),
			"product_unit" => $item->prop("product_unit"),
			"product_code" => $item->prop("product_code"),
			"product_color" => $item->prop("product_color"),
			"product_size" => $item->prop("product_size"),
			"product_count" => $item->prop("product_count"),
			"product_price" => $item->prop("product_price"),
			"product_image" => $item->prop("product_image"),
			"product_page" => $item->prop("product_page"),
			"comment" => $item->prop("comment"),
			"product_bill" => $item->prop("product_bill"),
			"product_duedate" => $item->prop("product_duedate"),
			"product_sum" => $item->prop("product_count") * str_replace(",", ".", $item->prop("product_price")),
			"name" => $name,
		));
	}

	/**

		@attrib name=submit_rent_step_1 nologin=1

	**/
	function submit_rent_step_1($arr)
	{
		// save item types
		foreach(safe_array($arr["rent_items"]) as $id => $type)
		{
			$_SESSION["orders_form"]["payment"]["itypes"][$id] = $type;
		}

		if ($arr["save_only"] != "")
		{
			return $this->mk_my_orb("rent_step_1", array("id" => $arr["id"], "section" => $arr["section"]));
		}
		return $this->mk_my_orb("rent_step_2", array("id" => $arr["id"], "section" => $arr["section"]));
	}

	/**

		@attrib name=rent_step_2 nologin=1

		@param id required
		@param section required

	**/
	function rent_step_2($arr)
	{
		$this->read_template("rent_step_2.tpl");

		$of_i = get_instance(CL_ORDERS_FORM);
		$states = $of_i->get_states();	

		$o = obj($arr["id"]);
		$inf = $o->meta("rent_data");

		// get items in cart
		$f = get_instance(CL_ORDERS_FORM);
		$items = $f->get_cart_items();

		$cats = array();
		foreach($items->arr() as $item)
		{
			$cats[(int)$_SESSION["orders_form"]["payment"]["itypes"][$item->id()]][$item->id()] = $item;
		}

		// display cats
		$item_cat = "";
		foreach($cats as $cat => $items)
		{
			$item_in_cat = "";
			$tot_price = 0;
			foreach($items as $item)
			{
				$this->_insert_item_inf($item, $states);

				$item_in_cat .= $this->parse("ITEM_IN_CAT");

				$tot_price += $item->prop("product_count") * str_replace(",", ".", $item->prop("product_price"));
			}

			$dat = $inf[(int)$_SESSION["orders_form"]["payment"]["itypes"][$item->id()]];
			$lengths = array();
			for($i = $dat["min_mons"]; $i <= $dat["max_mons"]; $i++)
			{
				$lengths[$i] = $i." kuud";
			}

			$prepayment = (($tot_price / 100.0) * (float)$inf[$cat]["prepayment"]);
			$num_payments = max($_SESSION["orders_form"]["payment"]["lengths"][$item->id()], $dat["min_mons"]);

			$cp = $tot_price - $prepayment;

			$percent = $inf[$cat]["interest"];

			$payment = ($cp+($cp*$num_payments*(1+($percent/100))/100))/($num_payments+1);

			$rent_price = $payment * ($num_payments+1) + $prepayment;

			$this->vars(array(
				"catalog_price" => number_format($tot_price, 2),
				"prepayment_price" => number_format($prepayment,2),
				"prepayment" => (int)$inf[$cat]["prepayment"],
				"sel_period" => html::select(array(
					"name" => "rent_lengths[".$item->id()."]",
					"options" => $lengths,
					"selected" => $num_payments
				)),
				"num_payments" => $num_payments+1,
				"rent_payment" => number_format($payment,2),
				"total_rent_price" => number_format($rent_price,2),
			));

			$this->vars(array(
				"cat_name" => $inf[$cat]["type"],
				"ITEM_IN_CAT" => $item_in_cat,
				"HAS_PREPAYMENT" => ($inf[$cat]["prepayment"] > 0 ? $this->parse("HAS_PREPAYMENT") : ""),
				
			));

			$item_cat .= $this->parse("ITEM_CAT");
		}

		$this->vars(array(
			"rent_payment_error" => ($_SESSION["orders_form"]["payment"]["errors"]["too_small"] ? $o->prop("rent_min_amt_payment_text") : "")
		));
		
		$this->vars(array(
			"ITEM_CAT" => $item_cat,
			"reforb" => $this->mk_reforb("submit_rent_step_2", $arr),
			"back" => $this->mk_my_orb("rent_step_1", array("id" => $arr["id"], "section" => $arr["section"])),
			"PAYMENT_ERR" => ($_SESSION["orders_form"]["payment"]["errors"]["too_small"] ? $this->parse("PAYMENT_ERR") : "")
		));		
		unset($_SESSION["orders_form"]["payment"]["errors"]["too_small"]);

		return $this->parse();
	}

	/**

		@attrib name=submit_rent_step_2 nologin=1

	**/
	function submit_rent_step_2($arr)
	{
		foreach(safe_array($arr["rent_lengths"]) as $item => $len)
		{
			$_SESSION["orders_form"]["payment"]["lengths"][$item] = $len;
		}

		$o = obj($arr["id"]);
		$inf = $o->meta("rent_data");

		// get items in cart
		$f = get_instance(CL_ORDERS_FORM);
		$items = $f->get_cart_items();

		$cats = array();
		foreach($items->arr() as $item)
		{
			$cats[(int)$_SESSION["orders_form"]["payment"]["itypes"][$item->id()]][$item->id()] = $item;
		}

		// display cats
		$tot_pm = 0;
		foreach($cats as $cat => $items)
		{
			$tot_price = 0;
			foreach($items as $item)
			{
				$tot_price += $item->prop("product_count") * str_replace(",", ".", $item->prop("product_price"));
			}
			$prepayment = (($tot_price / 100.0) * (float)$inf[$cat]["prepayment"]);
			$num_payments = max($_SESSION["orders_form"]["payment"]["lengths"][$item->id()], $dat["min_mons"]);
			$cp = $tot_price - $prepayment;
			$percent = $inf[$cat]["interest"];
			$payment = ($cp+($cp*$num_payments*(1+($percent/100))/100))/($num_payments+1);

			$tot_pm += $payment;
		}

		if ($tot_pm < $o->prop("rent_min_amt_payment"))
		{
			$_SESSION["orders_form"]["payment"]["errors"]["too_small"] = 1;
			$stay = true;
		}

		if ($arr["save_only"] != "" || $stay)
		{
			return $this->mk_my_orb("rent_step_2", array("id" => $arr["id"], "section" => $arr["section"]));
		}
		return $this->mk_my_orb("change", array("id" => $arr["id"], "section" => $arr["section"], "group" => "confirmpage"), "orders_form");
	}
	
	function request_execute($obj)
	{
		$form_i = get_instance(CL_ORDERS_FORM);
		$_SESSION["order_cart_id"] = $obj->id();
		if(is_oid($obj->meta("orders_form")) && $this->can("view" , $obj->meta("orders_form")))
		{
			$form = obj($obj->meta("orders_form"));
		}
		else
		{
			$form = new object_list(array(
				"class_id" => CL_ORDERS_FORM,
			));
			$form = reset($form->arr());
		}

//foreach($form->arr() as $a) arr($a->meta());
		$_SESSION["order_form_id"] = $form->id();
		$_SESSION["show_order"] = 1;
		$val = $form_i->change(array("show_order" => 1));
		unset($_SESSION["show_order"]);
		unset($_SESSION["order_form_id"]);
		unset($_SESSION["order_cart_id"]);
		//unset($_SESSION["order_form_id"]);
		
		return $val;
	}
}
?>
