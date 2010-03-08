<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/orders/orders_manager.aw,v 1.17 2008/10/29 15:55:13 markop Exp $
// orders_manager.aw - Tellimuste haldus 
/*

@classinfo syslog_type=ST_ORDERS_MANAGER relationmgr=yes maintainer=markop
@default table=objects

@property export_folder type=textbox field=meta method=serialize group=general
@caption Ekspordi kataloog

@property order_form type=relpicker reltype=RELTYPE_ORDERS_FORM field=meta method=serialize group=general
@caption Tellimuse vorm

@groupinfo ordermnager caption="Tellimused"

	@groupinfo ordermnager_unc caption="Kinnitamata" submit=no parent=ordermnager

	@groupinfo ordermnager_undone caption="T&auml;itmata" submit=no parent=ordermnager
		@property order_undone_tb type=toolbar store=no group=ordermnager_undone no_caption=1
		@property order_undone type=table store=no group=ordermnager_undone no_caption=1
	@groupinfo ordermnager_cf caption="Kinnitatud" submit=no parent=ordermnager

@default group=ordermnager_unc,ordermnager_cf

#otsing
@property find_name type=textbox store=no
@caption Nimi

@property find_start type=date_select
@caption Alates

@property find_end type=date_select store=no
@caption Kuni

@property do_find type=submit store=no
@caption Otsi


@property orders_toolbar type=toolbar no_caption=1
@caption Tellimuste toolbar

@property orders_table type=table no_caption=1
@caption Tellimuste tabel

@reltype CFGMANAGER value=1 clid=CL_CFGMANAGER
@caption Seadete haldur

@reltype ORDERS_FORM value=2 clid=CL_ORDERS_FORM
@caption Tellimuse vorm

*/

class orders_manager extends class_base
{
	function orders_manager()
	{
		$this->init(array(
			"clid" => CL_ORDERS_MANAGER
		));
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

	//////
	// class_base classes usually need those, uncomment them if you want to use them
	
	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "orders_table":
				$this->do_orders_table($arr);
				break;
			
			case "orders_toolbar":
				$this->do_orders_toolbar($arr);
				break;
			case "order_undone_tb":
				$this->do_order_undone_tb($arr);
				break;
			case "order_undone":
				$this->do_order_undone_tbl($arr);
				break;
			case "find_name":
			case "find_start":
			case "find_end":
				$search_data = $arr["obj_inst"]->meta("search_data");
				$prop["value"] = $search_data[$prop["name"]];
				break;

		};
		return $retval;
	}

	function set_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "find_name":
				unset($arr["request"]["rawdata"]["rawdata"]);
				$arr["obj_inst"]->set_meta("search_data" , $arr["request"]);
				break;
		}
		return $retval;
	}
	
	function do_order_undone_tb(&$arr)
	{
		$tb =& $arr["prop"]["vcl_inst"];

		$tb->add_button(array(
			"name" => "xls",
			"tooltip" => t("Exceli-tabeli vormis"),
			"url" => $this->mk_my_orb("undone_xls", array(
				"id" => $arr["obj_inst"]->id(),
				"return_url" => get_ru()
			), CL_ORDERS_MANAGER)
		));
		
/*		$tb->add_button(array(
			"name" => "print",
			"tooltip" => t("Prindi tellimused"),
			"img" => "print.gif",
			"url" => "javascript:document.changeform.target='_blank';javascript:submit_changeform('print_orders')",
//			"url" => $this->mk_my_orb("print_orders", array(
//				"id" => $arr["obj_inst"]->id(),
//				"return_url" => get_ru()
//			), CL_ORDERS_MANAGER)
		));*/

	}
	
	function do_order_undone_tbl(&$arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$cl = $arr["cl"];
		$this->_init_undone_tbl($t,$cl);
		$xls = $arr["xls"];
		// list orders from order folder
		$filter = array(
			"class_id" => CL_ORDERS_ORDER,
//			"confirmed" => 0
		);
		if($arr["client"])
		{
			$filter["orderer_company"] = $arr["client"];
		}
		if($cl)
		{
			$filter["createdby"] = aw_global_get("uid");
		}
		$ol = new object_list($filter);
		
		$undone_products = array();
		$ord_data = array();
		foreach($ol->arr() as $o)
		{
			if($arr["obj_inst"]->prop("order_form") && $arr["obj_inst"]->prop("order_form") != $o->meta("orders_form"))
			{
				continue;
			}
			foreach($o->connections_from(array("type" => "RELTYPE_ORDER")) as $it)
			{
				$item = $it->to();
				if(!$item->prop("product_count_undone")) continue;
//				arr($item->name());

				$pol = new object_list(array("user2" => $item->prop("product_code") , "class_id" => CL_SHOP_PRODUCT));
				if(is_array($pol) && sizeof($pol->arr()))
				{
					$product_obj = reset($pol->arr());
					if($product_obj->class_id() != CL_SHOP_PRODUCT)
					{
						$product_obj = "";
					}
					$id = $product_obj->id();
				}
				if(!is_object($product_obj))
				{
					if($item->prop("product_code"))
					{
						$id = $item->prop("product_code");
					}
					else
					{
						$id = $item->name();
					}
				}
				$undone_products[$id][$o->id()] = array(
					"product_count" => $item->prop("product_count"),
					"product_count_undone" => $item->prop("product_count_undone"),
					"name" => $item->name(),
					"product_code" => $item->prop("product_code"),
					"product_color" => $item->prop("product_color"),
					"product_size" => $item->prop("product_size"),
					"product_price" => $item->prop("product_price"),
					"product_page" => $item->prop("product_page"),
					"product_image" => $item->prop("product_image"),
					"product_unit" => $item->prop("product_unit"),
					"product_duedate" => $item->prop("product_duedate"),
					"udef_textbox1" => $item->prop("udef_textbox1"),
					"udef_textbox2" => $item->prop("udef_textbox2"),
					"udef_textbox3" => $item->prop("udef_textbox3"),
					"udef_textbox4" => $item->prop("udef_textbox4"),
					"udef_textbox5" => $item->prop("udef_textbox5"),
					"udef_textbox6" => $item->prop("udef_textbox6"),
					"udef_textbox7" => $item->prop("udef_textbox7"),
				);
			}
		}
		$upkeys = array_keys($undone_products);
//		usort($upkeys, array(&$this, "__br_sort"));
		foreach($upkeys as $product)
		{
			$product_data = array();
			$order = $undone_products[$product];
			$product_obj = "";
			if(is_oid($product) && $this->can("view" , $product))
			{
				$product_obj = obj($product);
				if($product_obj->class_id() != CL_SHOP_PRODUCT)
				{
					$product_obj = "";
				}
				else
				{
					$product_data["product"] = $cl?$product_obj->name():html::get_change_url($product, array("return_url" => get_ru()) , $product_obj->name());
					$product_data["code"] = $product_obj->prop("user2");
				}
			}

			if(!is_object($product_obj))
			{
				$o = reset($order);
				$product_data["product"] = $o["name"];
				$product_data["code"] = $o["product_code"];
			}
			$unit = "";
			if(!$xls) $t->define_data(array(
				"product" => $product_data["product"],
				"code" => $product_data["code"],
				"unit" => $unit,
				"color" => "#DDDDDD",
//				"packaging" => $product_obj->prop("user1"),
			));
			
			$prod_count = 0;
			
			foreach($order as $key => $amount)
			{
				if(!$this->can("view" , $key)) continue;
				$order = obj($key);
				$client = "";
				if($client_o = $order->get_first_obj_by_reltype(array("type" => "RELTYPE_PERSON")));
 				{
					if(is_object($client_o))
					{
						$client = html::get_change_url($client_o->id(), array("return_url" => get_ru()) , $client_o->name());
					}
				}
				$t->define_data(array(
					"product" => (!$xls)?"":$amount["name"],
					"code" => (!$xls)?"":($amount["product_code"]?$amount["product_code"] : " "),

					"order" => $cl?html::href(array("url" => $key, "caption" => $key)):html::get_change_url($key, array("return_url" => get_ru() , "group" => "orderitems") , $key),
					"client" => $client,
					"amount" => $amount["product_count_undone"],
					"color" => $order->prop("confirmed")?"":"#CCFFCC",
					"unit" => $amount["product_unit"],
					"packaging" => $amount["product_size"],
					"duedate" => $amount["product_duedate"],
					"date" => date("d/m/y" , $order->created()),
		//			"packaging" => $ord_data[$order->id()][$product]["user1"],
		//			"date" => $ord_data[$order->id()][$product]["duedate"],
					"bill" => $ord_data[$order->id()][$product]["bill"],
				));
				$prod_count+=$amount["product_count_undone"];
			}
			
			

			if(!$xls)$t->define_data(array(
				"product" => t("Kokku:"),
				"amount" => "<b>".$prod_count."</b>",

			));
		}

		$t->set_sortable(false);

//		$t->set_default_sortby("modified");
//		$t->set_default_sorder("DESC");
//		$t->sort_by();
	}


	function _init_undone_tbl(&$t,$cl)
	{
		$t->define_field(array(
			"name" => "code",
			"caption" => t("Kood"),
			"chgbgcolor" => "color",
		));

		$t->define_field(array(
			"name" => "product",
			"caption" => t("Toode"),
			"chgbgcolor" => "color",
		));

		$t->define_field(array(
			"name" => "unit",
			"caption" => t("M&otilde;&otilde;t&uuml;hik"),
			"align" => "center",
			"chgbgcolor" => "color",
		));


		$t->define_field(array(
			"name" => "packaging",
			"caption" => t("Pakend"),
			"align" => "center",
			"chgbgcolor" => "color",
		));

		$t->define_field(array(
			"name" => "amount",
			"caption" => t("Tellitav Kogus"),
			"align" => "center",
			"chgbgcolor" => "color",
		));

		$t->define_field(array(
			"name" => "duedate",
			"caption" => t("Soovitav tarne t&auml;itmine"),
			"align" => "center",
			"chgbgcolor" => "color",
		));

		$t->define_field(array(
			"name" => "date",
			"caption" => t("Tellimuse kuup&auml;ev"),
			"align" => "center",
			"chgbgcolor" => "color",
		));
		
		if(!$cl)$t->define_field(array(
			"name" => "client",
			"caption" => t("Klient"),
			"align" => "center",
			"chgbgcolor" => "color",
		));
		$t->define_field(array(
			"name" => "order",
			"caption" => t("Tellimuse nr."),
			"align" => "center",
			"chgbgcolor" => "color",
		));
	}
	
	function do_orders_toolbar($arr)
	{
		$tb = &$arr["prop"]["vcl_inst"];
		$tb->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"tooltip" => t("Kustuta tellimusi"),
			"action" => "delete_orders",
			"confirm" => t("Oled kindel, et soovid valitud tellimused kustutada?"),
		));

		if ($arr["request"]["group"] != "ordermnager_cf")
		{
			$tb->add_button(array(
				"name" => "confirm",
				"img" => "save.gif",
				"tooltip" => t("Kinnita tellimused"),
				"action" => "confirm_orders",
				"confirm" => t("Oled kindel, et soovid valitud tellimused kinnitada?"),
			));
		}
		$tb->add_button(array(
			"name" => "print",
			"tooltip" => t("Prindi tellimused"),
			"img" => "print.gif",
			"url" => "javascript:document.changeform.target='_blank';javascript:submit_changeform('print_orders')",
	//			"url" => $this->mk_my_orb("print_orders", array(
	//				"id" => $arr["obj_inst"]->id(),
	//				"return_url" => get_ru()
//			), CL_ORDERS_MANAGER)
		));
	}
	
	function do_orders_table($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "orderer",
			"caption" => t("Tellija")
		));

		$t->define_field(array(
			"name" => "date",
			"caption" => t("Kuup&auml;ev"),
			"sortable" => 1,
			"type" => "time",
			"format" => "H:i d-m-y",
			"width" => 80,
			"align" => "center",
		));

		$t->define_field(array(
			"name" => "view",
			"caption" => t("Vaata tellimust"),
			"width" => 80,
		));

		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid",
		));
		
		$filter = array(
			"class_id" => CL_ORDERS_ORDER,
			"order_completed" => 1,
			"sort_by" => "objects.created DESC",
		);

		$search_data = $arr["obj_inst"]->meta("search_data");
		if($search_data["find_name"])
		{
			$cond = array();
			
			foreach(explode(" " , $search_data["find_name"]) as $str)
			{
				$cond[] = new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					"CL_ORDERS_ORDER.RELTYPE_PERSON.firstname" => "%".$str."%",
					"CL_ORDERS_ORDER.RELTYPE_PERSON.lastname" => "%".$str."%",
					"CL_ORDERS_ORDER.RELTYPE_PERSON.name" => "%".$str."%",
				)));
			}

			$filter [] = new object_list_filter(array(
				"logic" => "AND",
				"conditions" => $cond
			));
		}

		if (!is_array($search_data))
		{
			$ts = mktime(0, 0, 0, date("m"), date("d")-7, date("Y"));
			$search_data["find_start"] = array("year" => date("Y", $ts), "month" => date("m", $ts), "day" => date("d", $ts));
		}
		if((date_edit::get_timestamp($search_data["find_start"]) > 1)|| (date_edit::get_timestamp($search_data["find_end"]) > 1))
		{
			if(date_edit::get_timestamp($search_data["find_start"]) > 1)
			{
				$from = date_edit::get_timestamp($search_data["find_start"]);
			}
			else{
				 $from = 0;
			}
			if(date_edit::get_timestamp($search_data["find_end"]) > 1)
			{
				$to = date_edit::get_timestamp($search_data["find_end"]);
			}
			else
			{
				$to = time();
			}
			$filter["created"] = new obj_predicate_compare(OBJ_COMP_BETWEEN, ($from - 1), ($to + 24*3600));
		}
//if(aw_global_get("uid") == "otto")arr($filter);
		$ol = new object_list($filter);
		$cff_id = $arr["obj_inst"]->prop("order_form.orderform");
		
		foreach ($ol->arr() as $order)
		{
			if($arr["obj_inst"]->prop("order_form") && !(
				$arr["obj_inst"]->prop("order_form") == $order->meta("orders_form") ||
				$cff_id == $order->meta("cfgform_id")	
			))
			{
				continue;
			}
			if ($arr["request"]["group"] == "ordermnager_cf")
			{
				if ($order->prop("order_confirmed") != 1)
				{
					continue;
				}
			}
			else
			{
				if ($order->prop("order_confirmed") == 1)
				{
					continue;
				}
			}
			unset($person_name);
			if($person = $order->get_first_obj_by_reltype("RELTYPE_PERSON"))
			{
				$person_name = $person->prop("firstname")." ".$person->prop("lastname");
				if($company = $person->company())
				{
					$person_name .= " / ".$company->prop("to.name");
				}
			}
			$t->define_data(array(
				"oid" => $order->id(),
				"orderer" => $person_name,
				"date" => $order->created(),
				"view" => html::href(array(
					"caption" => t("Vaata tellimust"),
					"url" => $this->mk_my_orb("change", array("id" => $order->id(), "group" => "orderitems", "return_url" => get_ru()), CL_ORDERS_ORDER)
				)),
			));
		}
		$t->set_sortable(false);
	}

	/*
	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{

		}
		return $retval;
	}	
	*/
	
	/**

		@attrib name=delete_orders

		@param id required type=int acl=view
		@param group optional
		@param sel required
	**/
	function delete_orders($arr)
	{
		foreach(safe_array($arr["sel"]) as $sel)
		{
			if(is_oid($sel) && $this->can("delete", $sel))
			{
				$obj = obj($sel);
				if($obj->class_id() == CL_ORDERS_ORDER)
				{
					foreach($obj->connections_from(array("type" => "RELTYPE_ORDER")) as $it)
					{
						$item = $it->to();
						$item->delete();
					}
				}
				$obj->delete();
			}
		}
		return html::get_change_url($arr["id"], array("group" => $arr["group"]));
	}
	

	/** exports orders after the last batch to textfile

		@attrib name=export_to_file nologin=1

		@param id required type=int acl=view

	**/
	function export_to_file($arr)
	{
		$o = obj($arr["id"]);

		// get time of last export
		$last_export = (int)$this->get_cval("orders_manager::last_export_time");

		echo "fetch orders since ".date("d.m.Y H:i", $last_export)." <br>\n";
		flush();

		// get orders created since then
		$ol = new object_list(array(
			"class_id" => CL_ORDERS_ORDER,
			"created" => new obj_predicate_compare(OBJ_COMP_GREATER, $last_export),
			"lang_id" => aw_global_get("lang_id"),
			"site_id" => array(),
			"order_completed" => 1,
			"sort_by" => "objects.created"
		));

		echo "got ".$ol->count()." orders <br>\n";
		flush();

		// get todays counter
		$counter = (int)$this->get_cval("orders_manager::today_counter")+1;

		if ($counter > 4)
		{
			$counter = 1;
		}

		// make file name
		$fn = $o->prop("export_folder")."/".date("Y")."-".date("m")."-".date("d")."-".$counter.".csv";

		echo "export to file $fn <br>";

		$ex_props = array(
			"item" => array(
				"name" => t("Toote nimi"), 
				"product_code" => t("Kood"), 
				"product_color" => t("V&auml;rvus"),
				"product_size" => t("Suurus"),
				"product_count" => t("Kogus"),
				"product_price" => t("Hind"),
				"product_page" => t("Lehek&uuml;lg"),
				"product_image" => t("Pilt")
			),
			"person" => array(
				"firstname" => t("Eesnimi"),
				"lastname" => t("Perekonnanimi"),
				"comment" => t("Aadress"),
				"birthday" => t("S&uuml;nnip&auml;ev"),
				"email" => t("E-mail"),
				"phone" => t("Telefon"),
			),
			"order" => array(
				"udef_textbox1" => t("Kliendi number"),
				"udef_textbox2" => t("Postiindeks"),
				"udef_textbox3" => t("Linn"),
				"udef_textbox4" => t("Telefon t&ouml;&ouml;l"),
				"udef_textbox5" => t("Mobiil"),
				"udef_textbox6" => t("Kliendo t&uuml;&uuml;p"),
			)
		);

		$lines = array();
		$sep = ",";
		$first = true;
		$header = array(t("OID"), t("Millal"));

		// foreach orders
		foreach($ol->arr() as $order)
		{
			$person = $order->get_first_obj_by_reltype("RELTYPE_PERSON");
			if (!$person)
			{
				continue;
			}

			// foreach order items
			foreach($order->connections_from(array("type" => "RELTYPE_ORDER")) as $c)
			{
				$line = array(
					$order->id(),
					date("d.m.Y H:i", $order->created())
				);

				$item = $c->to();

				// write order line to file
				foreach($ex_props as $obj => $dat)
				{
					foreach($dat as $prop => $head)
					{
						if ($first)
						{
							$header[] = $head;
						}
						$line[] = str_replace(",", " ",$$obj->prop_str($prop));
					}
				}

				if ($first)
				{
					$lines[] = join($sep, $header);
				}
				$lines[] = join($sep, $line);
				$first = false;
			}
		}
	
		$this->put_file(array(
			"file" => $fn,
			"content" => join("\n", $lines)
		));

		// write last export date
		$this->set_cval("orders_manager::last_export_time", time());
		$this->set_cval("orders_manager::today_counter", $counter);
		

		die(t("all done!"));
	}

	/**
		@attrib name=undone_xls all_args=1
		@param undone_xls optional type=id acl=view
	**/
	function undone_xls($arr)
	{
		classload("vcl/table");
		$arr["prop"]["vcl_inst"] = new aw_table(array(
			"layout" => "generic"
		));
		$arr["obj_inst"] = obj($arr["id"]);
		$arr["xls"] = 1;
		$this->do_order_undone_tbl($arr);
		header("Content-type: application/csv");
		header("Content-disposition: inline; filename=undone.csv;");
		die($arr["prop"]["vcl_inst"]->get_csv_file());
	}
	

	/**

		@attrib name=confirm_orders  all_args=1

	**/
	function confirm_orders($arr)
	{
		if (is_array($arr["sel"]) && count($arr["sel"]))
		{
			$ol = new object_list(array(
				"oid" => $arr["sel"]
			));
			foreach($ol->arr() as $o)
			{
				$o->set_prop("order_confirmed", 1);
				$o->save();
			}
		}
		return $this->mk_my_orb("change", array("id" => $arr["id"], "group" => $arr["group"]));
	}

	/**

		@attrib name=print_orders all_args=1

	**/
	function print_orders($arr)
	{
		$res = "";
//		fopen("http://games.swirve.com/utopia/login.htm");
//		die();
		$oo = get_instance(CL_ORDERS_ORDER);
		if (is_array($arr["sel"]) && count($arr["sel"]))
		{
			foreach($arr["sel"] as $id)
			{;
/*				$link =  $this->mk_my_orb("print_orders", array("print_id" => $id));
				$res.= '<script name= javascript>window.open("'.$link.'","", "toolbar=no, directories=no, status=no, location=no, resizable=yes, scrollbars=yes, menubar=no, height=800, width=720")</script>';
				//"<script language='javascript'>setTimeout('window.close()',10000);window.print();if (navigator.userAgent.toLowerCase().indexOf('msie') == -1) {window.close(); }</script>";
*/
				if($this->can("view", $id))
				{
					$res.='<DIV style="page-break-after:always">';
					$res .= $oo->request_execute(obj($id));
					$res.='</DIV>';
				}

			}
				$res.= "<script name= javascript>setTimeout('window.close()',10000);window.print();</script>";
		}
/*		elseif($this->can("view", $arr["print_id"]))
		{
			
			$res .= $oo->request_execute(obj($arr["print_id"]));
			$res .= "
				<script language='javascript'>
					setTimeout('window.close()',5000);
					window.print();
				//	if (navigator.userAgent.toLowerCase().indexOf('msie') == -1) {window.close(); }
				</script>
			";
		}*/
		else
		{
			$res .= t("Pole midagi printida");
		}

//		$res .= "<script language='javascript'>setTimeout('window.close()',10000);window.print();if (navigator.userAgent.toLowerCase().indexOf('msie') == -1) {window.close(); }</script>";

		die($res);
	}

}
?>
