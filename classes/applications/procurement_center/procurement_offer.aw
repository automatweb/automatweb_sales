<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/procurement_center/procurement_offer.aw,v 1.32 2007/12/06 14:33:50 kristo Exp $
// procurement_offer.aw - Pakkumine hankele
/*

@classinfo syslog_type=ST_PROCUREMENT_OFFER relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=markop

@tableinfo aw_procurement_offers index=aw_oid master_table=objects master_index=brother_of

@default table=objects
@default group=general

	@property procurement type=relpicker reltype=RELTYPE_PROCUREMENT table=aw_procurement_offers field=aw_procurement
	@caption Hange

	@property procurement_nr type=text submit=no
	@caption Hanke number

	@property offerer type=relpicker reltype=RELTYPE_OFFERER table=aw_procurement_offers field=aw_offerer
	@caption Pakkuja

	@property hr_price type=textbox size=10 table=aw_procurement_offers field=aw_hr_price
	@caption Tunni hind

	@property calc_price type=text store=no
	@caption Arvutatud koguhind

	@property price type=textbox size=10 table=aw_procurement_offers field=aw_price
	@caption Hind

	@property currency type=select table=aw_procurement_offers field=aw_currency
	@caption Valuuta

	@property discount type=textbox size=4 table=aw_procurement_offers field=aw_discount
	@caption Discount

	@property date type=date_select table=aw_procurement_offers field=aw_date
	@caption Pakkumise kuup&auml;ev

	@property accept_date type=date_select table=aw_procurement_offers field=aw_accept_date
	@caption Aktsepteerimist&auml;htaeg

	@property shipment_date type=date_select table=aw_procurement_offers field=aw_shipment_date
	@caption Tarne t&auml;htaeg

	@property completion_date type=date_select table=aw_procurement_offers field=aw_completion_date
	@caption Valmimist&auml;htaeg

	@property state type=select table=aw_procurement_offers field=aw_state
	@caption Staatus

@groupinfo products caption="Tooted"
@default group=products
	@property products type=table no_caption=1

@groupinfo files caption="Failid"
@default group=files

	@property files_tb type=toolbar no_caption=1 store=no

	@property files_table type=table no_caption=1

	@property files type=text  no_caption=1
	@caption Manused

@default group=r_list
	@property p_tb type=toolbar no_caption=1 store=no
	@layout p_l type=hbox width=30%:70%

		@property p_tr type=treeview no_caption=1 store=no parent=p_l

		@property p_tbl type=table no_caption=1 store=no parent=p_l

@default group=rejected

	@property rejected_table type=table store=no no_caption=1

@groupinfo r caption="N&otilde;uded" submit=no
@groupinfo r_list caption="N&otilde;uete nimekiri" submit=no parent=r
@groupinfo rejected caption="Tagasi l&uuml;katud" submit=no parent=r

@reltype PROCUREMENT value=1 clid=CL_PROCUREMENT
@caption Hange

@reltype OFFERER value=2 clid=CL_CRM_COMPANY,CL_CRM_PERSON
@caption Pakkuja

@reltype ROW value=3 clid=CL_PROCUREMENT_OFFER_ROW
@caption Pakkumise rida
*/


define("OFFER_STATE_NEW", 0);
define("OFFER_STATE_PUBLIC", 1);
define("OFFER_STATE_REJECTED", 2);
define("OFFER_STATE_ACCEPTED", 3);

class procurement_offer extends class_base
{
	function procurement_offer()
	{
		$this->init(array(
			"tpldir" => "applications/procurement_center/procurement_offer",
			"clid" => CL_PROCUREMENT_OFFER
		));

		$this->model = get_instance("applications/procurement_center/procurements_model");

		$this->offer_states = array(
			OFFER_STATE_NEW => t("Uus"),
			OFFER_STATE_PUBLIC => t("Avaldatud"),
			OFFER_STATE_REJECTED => t("Tagasi l&uuml;katud"),
			OFFER_STATE_ACCEPTED => t("Vastu v&otilde;etud")
		);

		$this->readyness_states = array(
			PO_IN_BASE => t("Kohe olemas"),
			PO_NEEDS_INSTALL => t("Vajab seadistamist"),
			PO_NEEDS_DEVELOPMENT => t("Uus arendus")
		);
	}

	/**
		@attrib name=remove_conn
	**/
	function remove_conn($arr)
	{
		$this_obj = obj($arr["id"]);
		foreach($arr["sel"] as $offer)
		{
			$off_obj = obj($offer);
			$this_obj->disconnect(array("from" => $offer));
			$off_obj->disconnect(array("from" => $arr["id"]));
		}
		return $arr["post_ru"];
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "currency":
				$curr_object_list = new object_list(array(
					"class_id" => CL_CURRENCY,
					"lang_id" => array(),
					"site_id" => array()
				));
				foreach($curr_object_list->arr() as $curr)
				{
					$prop["options"][$curr->id()] = $curr->name();
				}
				$u = get_instance(CL_USER);
				$company = obj($u->get_current_company());
				if(!$arr["obj_inst"]->prop("currency"))
				{
					$prop["value"] = $company->prop("currency");
				}
				break;
			case "files":
				$this->_get_files($arr);
				break;

			case "files_table":
				$this->_get_files_table($arr);
				break;

			case "files_tb":
				$tb =&$arr["prop"]["vcl_inst"];
				$tb->add_button(array(
					'name' => 'delete',
					'img' => 'delete.gif',
					'tooltip' => t('Kustuta'),
					"action" => "remove_conn",
				));
				break;

			case "rejected_table":
				$this->_rejected_table($arr);
				break;

			case "state":
				$prop["options"] = $this->get_offer_states();
				if ($arr["obj_inst"]->prop("state") != OFFER_STATE_NEW)
				{
					$prop["type"] = "text";
					$prop["value"] = $prop["options"][$arr["obj_inst"]->prop("state")];
				}
				break;

			case "price":
			case "hr_price":
				if ($arr["obj_inst"]->prop("state") != OFFER_STATE_NEW)
				{
					$prop["type"] = "text";
				}
				break;

			case "calc_price":
				$prop["value"] = number_format($this->calculate_price($arr["obj_inst"]), 2);
				break;

			case "procurement":
				// list all procs for the current
				$ol = $this->model->get_my_procurements();
				$prop["options"] = $ol->names();
				if (!is_oid($arr["obj_inst"]->id()) && $arr["request"]["proc"])
				{
					$prop["value"] = $arr["request"]["proc"];
				}
				if (!isset($prop["options"][$prop["value"]]) && $this->can("view", $prop["value"]))
				{
					$tmp = obj($prop["value"]);
					$prop["options"][$tmp->id()] = $tmp->name();
				}
				break;

//			case "task":
//				$prop["autocomplete_source"] = $this->mk_my_orb("product_autocomplete_source");
//				$prop["autocomplete_params"] = array("product");
//				break;

			case "p_tb":
				/*if ($arr["obj_inst"]->prop("state") != OFFER_STATE_NEW)
				{
					return PROP_IGNORE;
				}*/
				$this->_p_tb($arr);
				break;

			case "p_tr":
				$this->_p_tr($arr);
				break;

			case "p_tbl":
				$this->_p_tbl($arr);
				break;
				
			case "procurement_nr":
				$prop["value"] = $arr["obj_inst"]->prop("procurement.procurement_nr");
				break;

			case "products":
				$t = &$arr["prop"]["vcl_inst"];
				return $this->products_table($t , $arr["obj_inst"]);
				break;

// 			case "procurement":
// 				if($arr["new"])
// 				{
// 					$arr["obj_inst"]->set_parent($arr["request"]["parent"]);
// 					$arr["obj_inst"]->set_class_id(CL_PROCUREMENT_OFFER);
// 				if(is_oid($arr["request"]["proc"]) && $this->can("view" , $arr["request"]["proc"]))
// 					{
// 						$arr["obj_inst"]->set_prop("procurement" , $arr["request"]["proc"]);
// 					}
// 					$_GET["action"] = "change";
// 					$arr["obj_inst"]->save();
// 				}
// 				break;
// 			case "offerer":
// 				if($arr["new"])
// 				{
// 					$arr["obj_inst"]->set_parent($arr["request"]["parent"]);
// 					$arr["obj_inst"]->set_class_id(CL_PROCUREMENT_OFFER);					if(is_oid($arr["request"]["offerer"]) && $this->can("view" , $arr["request"]["offerer"]))
// 					{
// 						$arr["obj_inst"]->set_prop("offerer" , $arr["request"]["offerer"]);
// 					}
//
// 					$arr["obj_inst"]->save();
// 					$_GET["action"] = "change";
// 				}
// 				break;
		}
		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "state":
			case "price":
			case "hr_price":
				if ($arr["obj_inst"]->prop("state") != OFFER_STATE_NEW)
				{
					return PROP_IGNORE;
				}
				break;

			case "products":
				$_SESSION["procurement"]["accept"] = $arr["request"]["accept"];
				$_SESSION["procurement"]["val"] = $arr["request"]["products"];
				$popup = "<script name= javascript>window.open('".$this->mk_my_orb("set_type", array("id" => $arr["obj_inst"]->id(), "return_url" => $arr["request"]["return_url"]))."','', 'toolbar=no, directories=no, status=no, location=no, resizable=yes, scrollbars=yes, menubar=no, height=400, width=600')
				</script>";
				die($popup);
				break;
		}
		return $retval;
	}

	function _init_files_tbl(&$t)
	{
		$t->define_field(array(
			"caption" => t("&nbsp;"),
			"name" => "icon",
			"align" => "center",
			"sortable" => 0,
			"width" => 1
		));

		$t->define_field(array(
			"caption" => t("Nimi"),
			"name" => "name",
			"align" => "center",
			"sortable" => 1
		));

		$t->define_field(array(
			"caption" => t("Looja"),
			"name" => "createdby",
			"align" => "center",
			"sortable" => 1
		));

		$t->define_field(array(
			"caption" => t("Loodud"),
			"name" => "created",
			"align" => "center",
			"sortable" => 1,
			"numeric" => 1,
			"type" => "time",
			"format" => "d.m.Y H:i"
		));

		$t->define_field(array(
			"caption" => t("Muudetud"),
			"name" => "modified",
			"align" => "center",
			"sortable" => 1,
			"numeric" => 1,
			"type" => "time",
			"format" => "d.m.Y H:i"
		));

		$t->define_field(array(
			"caption" => t("&nbsp;"),
			"name" => "pop",
			"align" => "center"
		));

		$t->define_chooser(array(
			"field" => "oid",
			"name" => "sel"
		));
	}

	function _get_files_table($arr)
	{
		if(!sizeof($arr["obj_inst"]->connections_from(array(
			"class" => array(CL_CRM_DOCUMENT, CL_CRM_MEMO, CL_CRM_DEAL, CL_FILE, CL_CRM_OFFER),
		))))
		{
			return ;
		}
		$pt = $this->_get_files_pt($arr);
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_files_tbl($t);


		$ol = new object_list($arr["obj_inst"]->connections_from(array()));

		classload("core/icons");
		$clss = aw_ini_get("classes");
		get_instance(CL_FILE);
		foreach($ol->arr() as $o)
		{
			if(!(($o->class_id() == CL_FILE) || ($o->class_id() == CL_CRM_DOCUMENT) || ($o->class_id() == CL_CRM_DEAL) || ($o->class_id() == CL_CRM_OFFER) || ($o->class_id() == CL_CRM_MEMO))) continue;
			$pm = get_instance("vcl/popup_menu");
			$pm->begin_menu("sf".$o->id());


			if ($o->class_id() == CL_FILE)
			{
				$pm->add_item(array(
					"text" => $o->name(),
					"link" => file::get_url($o->id(), $o->name())
				));
			}
			else
			{
				foreach($o->connections_from(array("type" => "RELTYPE_FILE")) as $c)
				{
					$pm->add_item(array(
						"text" => $c->prop("to.name"),
						"link" => file::get_url($c->prop("to"), $c->prop("to.name"))
					));
				}
			}

			$t->define_data(array(
				"icon" => $pm->get_menu(array(
					"icon" => icons::get_icon_url($o)
				)),
				"name" => html::obj_change_url($o),
				"class_id" => $clss[$o->class_id()]["name"],
				"createdby" => $o->createdby(),
				"created" => $o->created(),
				"modifiedby" => $o->modifiedby(),
				"modified" => $o->modified(),
				"oid" => $o->id()
			));
		}

		$t->set_default_sortby("created");
		$t->set_default_sorder("desc");
	}

	function _get_files_pt($arr)
	{
		if ($arr["request"]["tf"] && $arr["request"]["tf"] != "unsorted")
		{
			return $arr["request"]["tf"];
		}
		$ff = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_FILES_FLD");
		if (!$ff)
		{
			$ff = obj();
			$ff->set_class_id(CL_MENU);
			$ff->set_parent($arr["obj_inst"]->id());
			$ff->set_name(sprintf(t("%s failid"), $arr["obj_inst"]->name()));
			$ff->save();
			$arr["obj_inst"]->connect(array(
				"to" => $ff->id(),
				"type" => "RELTYPE_FILES_FLD"
			));
		}
		return $ff->id();
	}

	function _get_sub_folder_objects($obj)
	{
		$parents = array();
		$ol = new object_list(array(
			"lang_id" => array(),
			"parent" => $obj->id(),
			"class_id" => CL_MENU,
		));
		$parents[] = $obj;
		foreach($ol->arr() as $folder)
		{
			$parents = array_merge($parents,$this->_get_sub_folder_objects($folder));
		}
		return $parents;
	}

	//kuna ühendus on mõlemapoolne, siis paluks seda funktsiooni kasutada
	/** Connects row with offer
		@attrib name=connect_offer_and_row params=name api=1
		@param offer required type=id/object
			offer id or object
		@param row optional type=id/object
			row id or object
		@returns false if bad row or offer
		@example
			$offer = get_instance(CL_PROCUREMENT_OFFER);
			$offer->connect_offer_and_row()
	**/
	function connect_offer_and_row($arr)
	{
		extract($arr);
		if(is_oid($offer) && $this->can("view" , $offer))
		{
			$offer = obj($offer);
		}
		if(is_oid($row) && $this->can("view" , $row))
		{
			$row = obj($row);
		}
		if(!is_object($row) || !is_object($offer))
		{
			return false;
		}
		$row->connect(array(
			"to" => $offer->id(),
			"type" => "RELTYPE_OFFER",
		));
		$offer->connect(array(
			"to" => $row->id(),
			"type" => "RELTYPE_ROW"
		));
		return true;
	}
	
	/** Disconnects row and offer
		@attrib name=disconnect_offer_and_row params=name api=1
		@param offer required type=id/object
			offer
		@param row optional type=id/object
			If this is set, then no new phone number object is created, but this one is added to the organisation instead
		@returns false if bad row or offer
		@example
			$offer = get_instance(CL_PROCUREMENT_OFFER);
			$offer->connect_offer_and_row()
	**/
	function disconnect_offer_and_row($arr)
	{
		extract($arr);
		if(is_oid($offer) && $this->can("view" , $offer))
		{
			$offer = obj($offer);
		}
		if(is_oid($row) && $this->can("view" , $row))
		{
			$row = obj($row);
		}
		if(!is_object($row) || !is_object($offer))
		{
			return false;
		}
		$row->disconnect(array(
			"from" => $offer->id(),
		));
		$offer->disconnect(array(
			"from" => $row->id(),
		));
		return true;
	}
	

	/**
		@attrib name=set_type
	**/
	function set_type($arr)
	{
		if($_GET["return_url"])
		{
			$_SESSION["return_url"] = $_GET["return_url"];
		}
		$this_object = obj($_GET["id"]);
		classload("vcl/table");
		$t = new aw_table(array(
			"layout" => "generic"
		));
			$t->define_field(array(
			"name" => "name",
			"sortable" => 1,
			"caption" => t("Nimi")
		));
			$t->define_field(array(
			"name" => "type",
			"sortable" => 1,
			"caption" => t("T&uuml;&uuml;p")
		));

		$t->define_field(array(
			"name" => "menu",
			"sortable" => 1,
			"caption" => t("Kataloog")
		));
		$t->set_default_sortby("name");

		if(is_oid($this_object->prop("procurement")) && $this->can("view" , $this_object->prop("procurement")))
		{
			$procurement = obj($this_object->prop("procurement"));
			$co = obj($procurement->prop("orderer"));
			$warehouse = $co->get_first_obj_by_reltype("RELTYPE_WAREHOUSE");
		}
		if(is_object($warehouse))
		{
			$warehouse->config = obj($warehouse->prop("conf"));
			$parent = $warehouse->config->prop("prod_fld");
		}
		else $parent = $_GET["id"];

		$types = new object_list(array(
				"class_id" => array(CL_SHOP_PRODUCT_TYPE),
				"lang_id" => array(),
				"site_id" => array(),
		));
		$options = $types->names();
		$menu_opt = $this->_get_sub_folder_objects(obj($parent));
		foreach($menu_opt as $opt)
		{
			$menu_options[$opt->id()] = $opt->name();
		}

		asort($menu_options);
		asort($options);

		if($_POST["types"])
		{
			foreach($_SESSION["procurement"]["val"] as $key=>$product)
			{
				$ol = new object_list(array(
					"class_id" => array(CL_SHOP_PRODUCT),
					"name" => $product["product"],
					"lang_id" => array(),
					"site_id" => array(),
				));

				if (!$ol->count())
				{
					if(is_oid($_POST["types"][$product["product"]]))
					{
						$type_object = obj($_POST["types"][$product["product"]]);
						if(is_oid($type_object->prop("default_product_folder")))
						{
							$folder = obj($type_object->prop("default_product_folder"));
							if($folder->class_id() == CL_MENU)
							{
								$parent = $folder->id();
							}
						}
					}
					if(is_oid($_POST["parent"][$product["product"]]))
					{
						$parent = $_POST["parent"][$product["product"]];
					}
					$p = obj();
					$p->set_class_id(CL_SHOP_PRODUCT);
					$p->set_parent($parent);
					$p->set_name($product["product"]);
					$p->set_prop("item_type" ,$_POST["types"][$product["product"]]);
					$p->save();
				}

				if(is_oid($product["row_id"]))
				{
					$o = obj($product["row_id"]);
				}
				else
				{
					if((strlen($product["product"]) > 1) && ($product["available"]))
					{
						$o = obj();
						$o->set_class_id(CL_PROCUREMENT_OFFER_ROW);
						$o->set_parent($this_object->id());
						$o->set_name(sprintf(t("%s rida"), $this_object->name()));
						$o->save();
						$this->connect_offer_and_row(array(
							"offer" => $this_object->id(),
							"row" => $o->id()
						));
					}
					else continue;
				}
				$o->set_prop("accept", $product["accept"]);
				if(array_key_exists($key , $_SESSION["procurement"]["accept"]))
				{
					$o->set_prop("accept",1);
				}
				else
				{
					$o->set_prop("accept",null);
				}
				if(is_array($product["shipment"]))
				{
					$product["shipment"] = mktime(0,0,0,$product["shipment"]["month"], $product["shipment"]["day"] , $product["shipment"]["year"]);
				}
//				if(!$product["shipment"]) $product["shipment"] = $this_object->prop("shipment_date");
				foreach($product as $key=>$val)
				{
					switch ($key)
					{
						case "accept":
			//			case "shipment":
							break;

						case "price":
							$o->set_prop($key, str_replace(",", ".", str_replace(" ", "",$val)));
							break;

						default:
							if($o->is_property($key)) $o->set_prop($key, $val);
					}
				}
				$o->save();
			}
				$_SESSION["procurement"] = null;
			die("<script type='text/javascript'>
			window.opener.location.href='".$this->mk_my_orb("change", array("id"=>$_GET["id"] , "group" => "products", "return_url" => $_GET["return_url"]))."';
			window.close();
			</script>");
		}
		$new_products = 0;
		foreach($_SESSION["procurement"]["val"] as $product)
		{
			if($product["product"] == "") continue;
			$ol = new object_list(array(
				"class_id" => array(CL_SHOP_PRODUCT),
				"name" => $product["product"],
				"lang_id" => array(),
				"site_id" => array(),
			));
			if (!$ol->count())
			{
				$new_products = 1;
				$dat = array(
					"name" => $product["product"],
					"type" => html::select(array("options" => $options, "name" => "types[".$product["product"]."]")),
					"menu" => html::select(array("options" => $menu_options, "name" => "parent[".$product["product"]."]")),
				);
				if(strlen($product["product"]) > 0) $t->define_data($dat);
			}
		}

		if(!$new_products)
		{
			foreach($_SESSION["procurement"]["val"] as $key=>$product)
			{
				if(is_oid($product["row_id"]))
				{
					$o = obj($product["row_id"]);
					if($product["product"] == "")
					{
						$this->disconnect_offer_and_row(array(
							"offer" => $this_object->id(),
							"row" => $o->id(),
						));
					}
				}
				else
				{
					if((strlen($product["product"]) > 1) && ($product["available"]))
					{
						$o = obj();
						$o->set_class_id(CL_PROCUREMENT_OFFER_ROW);
						$o->set_parent($this_object->id());
						$o->set_name(sprintf(t("%s rida"), $this_object->name()));
						$o->set_prop("shipment", $this_object->prop("shipment_date"));
						$o->save();
						$this->connect_offer_and_row(array(
							"offer" => $this_object->id(),
							"row" => $o->id(),
						));
					}
					else continue;
				}
				$o->set_prop("accept", $product["accept"]);
				if(array_key_exists($product["row_id"] , $_SESSION["procurement"]["accept"]))
				{
					$o->set_prop("accept",1);
				}
				else
				{
					$o->set_prop("accept",null);
				}
				if(is_array($product["shipment"]))
				{
					$product["shipment"] = mktime(0,0,0,$product["shipment"]["month"], $product["shipment"]["day"] , $product["shipment"]["year"]);
				}
				if(!$product["shipment"])
				{
					$product["shipment"] = $o->prop("shipment");
				}
//				if(!$product["shipment"]) $product["shipment"] = $this_object->prop("shipment_date");
				foreach($product as $key=>$val)
				{
					switch ($key)
					{
						case "accept":
				//			case "shipment":
							break;

						case "price":
							$o->set_prop($key, str_replace(",", ".",  str_replace(" ", "",$val)));
							break;

						default:
							if($o->is_property($key)) $o->set_prop($key, $val);
					}
				}
				$o->save();
			}
			$_SESSION["procurement"] = null;
			die("<script type='text/javascript'>
			window.opener.location.href='".$this->mk_my_orb("change", array("id"=>$_GET["id"] , "group" => "products", "return_url" => $_GET["return_url"]))."';
			window.close();
			</script>");
		}
		$t->define_data(array("type" => html::submit(array("name" => "submit", "class" => "submit" , "value" => "submit" , "onclick"=>"self.disabled=true;submit_changeform(''); return false;") )));
		$t->set_sortable(false);
		return "<form action='".$this->mk_my_orb("set_type", array(
			"val" => $_GET["val"],
			"id" => $_GET["id"] ,
			"accept" => $_GET["accept"],
			"return_url" => $_GET["return_url"]))."' method='POST' name='changeform' enctype='multipart/form-data' >".$t->draw()."</form>";
	}

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
		$arr["d_id"] = $_GET["d_id"];
		if(!$arr["id"])
		{
			$arr["offerer"] = $_GET["offerer"];
		}
	}

	function do_db_upgrade($t, $f)
	{
		if ($f == "" && $t == "aw_procurement_offers")
		{
			$this->db_query("CREATE TABLE aw_procurement_offers (aw_oid int primary key, aw_price double)");
			return true;
		}

		switch($f)
		{
			case "aw_procurement":
			case "aw_offerer":
			case "aw_state":
			case "aw_completion_date":
			case "aw_accept_date":
			case "aw_currency":
			case "aw_shipment_date":
			case "aw_date":
			case "aw_discount":
				$this->db_add_col($t, array("name" => $f, "type" => "int"));
				return true;

			case "aw_hr_price":
				$this->db_add_col($t, array("name" => $f, "type" => "double"));
				return true;
		}
	}

	function _p_tb($arr)
	{
		$tb =& $arr["prop"]["vcl_inst"];

		$parent = $arr["request"]["d_id"];
		$po = obj($parent);
		if ($po->class_id() == CL_PROCUREMENT_REQUIREMENT)
		{
			$tb->add_button(array(
				'name' => 'new',
				'img' => 'new.gif',
				'tooltip' => t('Lisa'),
				"url" => html::get_new_url(
					CL_PROCUREMENT_REQUIREMENT_SOLUTION,
					$parent,
					array(
						"return_url" => get_ru(),
						"set_requirement" => $parent,
						"set_offer" => $arr["request"]["id"]
					)
				)
			));
		}

		$tb->add_button(array(
			'name' => 'save',
			'img' => 'save.gif',
			'tooltip' => t('Salvesta'),
			"action" => "save_data"
		));

		$tb->add_button(array(
			'name' => 'delete',
			'img' => 'delete.gif',
			'tooltip' => t('Kustuta'),
			"action" => "delete_solutions"
		));
	}

	function _p_tr($arr)
	{
		classload("core/icons");
		$arr["prop"]["vcl_inst"] = treeview::tree_from_objects(array(
			"tree_opts" => array(
				"type" => TREE_DHTML,
				"persist_state" => true,
				"tree_id" => "procurement_offer",
			),
			"root_item" => obj($arr["obj_inst"]->prop("procurement")),
			"ot" => new object_tree(array(
				"class_id" => array(CL_MENU,CL_PROCUREMENT_REQUIREMENT),
				"parent" => $arr["obj_inst"]->prop("procurement"),
				"lang_id" => array(),
				"site_id" => array()
			)),
			"var" => "d_id"
		));
	}

	function _init_p_tbl(&$t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"align" => "center",
			"sortable" => 1
		));

		$t->define_field(array(
			"name" => "readyness",
			"caption" => t("Valmidus"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "price",
			"caption" => t("Hind"),
			"align" => "center",
			"sortable" => 1,
		));

		$t->define_field(array(
			"name" => "time_to_install",
			"caption" => t("Seadistamise aeg"),
			"align" => "center",
			"sortable" => 1,
		));
		$t->define_field(array(
			"name" => "solution",
			"caption" => t("Kommentaar"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "default",
			"caption" => t("Eelistatud"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid"
		));
	}

	function _p_tbl($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_p_tbl($t);

		$parent = $arr["request"]["d_id"];
		if (!$parent)
		{
			return;
		}

		$data = $arr["obj_inst"]->meta("defaults");
		$default = $data[$parent];
		$ol = new object_list(array(
			"class_id" => CL_PROCUREMENT_REQUIREMENT_SOLUTION,
			"requirement" => $parent,
			"offer" => $arr["request"]["id"],
			"lang_id" => array(),
			"site_id" => array()
		));
		foreach($ol->arr() as $o)
		{
			$t->define_data(array(
				"name" => html::obj_change_url($o),
				"readyness" => $this->readyness_states[$o->prop("readyness")],
				"price" => number_format($o->prop("price"), 2),
				"time_to_install" => $o->prop("time_to_install"),
				"solution" => $o->prop("solution"),
				"default" => html::radiobutton(array(
					"name" => "default",
					"value" => $o->id(),
					"checked" => $default == $o->id()
				)),
				"oid" => $o->id()
			));
		}
	}

	function calculate_price($o)
	{
		if(is_oid($o->prop("procurement")) && $this->can("view", $o->prop("procurement")))
		{
			$reqs = $this->model->get_requirements_from_procurement(obj($o->prop("procurement")));
		}
		else return;
		$hrs = 0;
		$pr = 0;
		$data = $o->meta("defaults");
		foreach($reqs->arr() as $req)
		{
			if ($this->can("view", $data[$req->id()]))
			{
				$of = obj($data[$req->id()]);
				$hrs += $of->prop("time_to_install");
				$pr += $of->prop("price");
			}
		}

		$retval =  $pr + ($o->prop("hr_price") * $hrs);

		// also add the products list price
		$this_obj = $o;
		$conns = $this_obj->connections_to(array(
			'reltype' => 1,
			'class' => CL_PROCUREMENT_OFFER_ROW,
		));
		$procurement_inst = get_instance(CL_PROCUREMENT);
		$total_sum = 0;
		if(!sizeof($conns) && is_oid($this_obj->prop("procurement")))
		{
			$procurement = obj($this_obj->prop("procurement"));
			foreach($procurement->meta("products") as $product)
			{//arr($product);
				if(!$product["product"])
				{
					continue;
				}
				//$total_sum += $product["amount"] * $row->prop("price");
			}
		}
		foreach($conns as $conn)
		{
			if(is_oid($conn->prop("from")))
			{
				$row = obj($conn->prop("from"));
			}
			else
			{
				continue;
			}
			$total_sum += $row->prop("amount") * $row->prop("price");
		}

		return $retval + $total_sum;
	}

	/**
		@attrib name=save_data
	**/
	function save_data($arr)
	{
		if ($arr["default"])
		{
			$o = obj($arr["id"]);
			$d = $o->meta("defaults");
			$d[$arr["d_id"]] = $arr["default"];
			$o->set_meta("defaults", $d);
			$o->save();
		}
		return $arr["post_ru"];
	}

	function get_offer_states()
	{
		return $this->offer_states;
	}

	/**
		@attrib name=delete_solutions
	**/
	function delete_solutions($arr)
	{
		object_list::iterate_list($arr["sel"], "delete");
		return $arr["post_ru"];
	}

	function _init_rejected_table(&$t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"align" => "center"
		));
	}

	function _rejected_table($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_rejected_table($t);

		$reqs = $this->model->get_requirements_from_procurement(obj($arr["obj_inst"]->prop("procurement")));
		foreach($reqs->arr() as $req)
		{
			$ns = safe_array($req->meta("nonsuitable"));
			foreach($ns as $n => $tmp)
			{
				$t->define_data(array(
					"name" => html::obj_change_url($n)
				));
			}
		}
	}

	function products_table(&$t , $this_obj)
	{
		$u = get_instance(CL_USER);
		$co = obj($u->get_current_company());
		if(is_oid($this_obj->prop("procurement")))
		{
			$procurement_obj = obj($this_obj->prop("procurement"));
			$show_shipment_date = $procurement_obj->prop("shipment_date_req");
			$company_id = $procurement_obj->prop("orderer");
			if(is_oid($company_id) && $this->can("view", $company_id)) $co = obj($company_id);
		}

	//	$t->set_id("products_table");
	//	$t->define_field(array(
	//		"name" => "jrk",
	//		"caption" => t("Id"),
	//		"numeric" => 1,
	//	));

		$t->define_field(array(
			"name" => "product",
			"caption" => t("Toode"),
		));

		$t->define_field(array(
			'name' => 'amount',
			'caption' => t('Kogus'),
		));

		$t->define_field(array(
			'name' => 'unit',
			'caption' => t('&Uuml;hik'),
		));

		$t->define_field(array(
        		'name' => 'price_amount',
			'caption' => t('Min. kogus'),
		));

		$t->define_field(array(
        		'name' => 'price',
			'caption' => t('Hind'),
		));

		$t->define_field(array(
			'name' => 'currency',
			'caption' => t('Valuuta'),
		));
		$t->define_field(array(
			'name' => 'total',
			'caption' => t('Kogusumma'),
		));

		if($show_shipment_date)
		{
			$t->define_field(array(
				'name' => 'shipment',
				'caption' => t('Tarneaeg'),
			));
		}
/*		$t->define_field(array(
			'name' => 'accept',
			'caption' => t('Aktsepteeritud'),
		));*/

		$t->define_chooser(array(
			"name" => "accept",
			"field" => "oid",
			'caption' => t('Aktsepteeritud'),
		));
		$t->define_field(array(
			"name" => "available",
			"caption" => sprintf("<a href='javascript:aw_sel_chb(document.changeform,\"products\")'>%s</a>", t("Olemas")),
		));

		$unit_list = new object_list(array(
			"class_id" => CL_UNIT
		));
		$unit_opts = array();
		foreach($unit_list->arr() as $unit)
		{
			$unit_opts[$unit->id()] = $unit->prop("unit_code");
		}

		$curr_list = new object_list(array(
			"class_id" => CL_CURRENCY
		));
		$curr_opts = $curr_list->names();

		$conns = $this_obj->connections_to(array(
			'reltype' => 1,
			'class' => CL_PROCUREMENT_OFFER_ROW,
		));
		$procurement_inst = get_instance(CL_PROCUREMENT);
		$max_x = 1;$x = 1;
		$prod_rows_data = array();
		if(!sizeof($conns) && is_oid($this_obj->prop("procurement")))
		{
			$procurement = obj($this_obj->prop("procurement"));
			foreach($procurement->meta("products") as $product)
			{
				if(!$product["product"]) continue;
				$prod_rows_data[] = array(
					"x" => $x,
					"max_x" => $max_x,
// 				//	"row" => $x,
					"product" => $product["product"],
				//	"id" => $x,
					"amount" => $product["amount"],
					"unit" => $product["unit"],
				//	"price" => $row->prop("price"),
					"currency" => $this_obj->prop("currency"),
					"shipment" => $this_obj->prop("shipment_date"),
					"oid" => $x,
				);
				$x++;
				$max_x++;
			}
		}
		foreach($conns as $conn)
		{
			if(is_oid($conn->prop("from")))$row = obj($conn->prop("from"));
			else continue;
			$x = $conn->prop("from");
			if($x > $max_x) $max_x = $x;
			$accept = "";
			if($row->prop("accept"))
			{
				$accept = html::checkbox(array(
					"name" => "products[".$x."][accept]",
					"value" => $row->prop("accept"),
					"checked" => $row->prop("accept"),
				));
			}
			if(!$row->prop("price_amount"))
			{
				$min_amount = $row->prop("amount");
			}
			else
			{
				$min_amount = $row->prop("price_amount");
			}

			$prod_rows_data[] = array(
				"date" => date("d.m.Y", $row->prop("shipment")),
				"accept" => $accept,
				"x" => $x,
				"max_x" => $max_x,
				"row" => $row,
				"product" => $row->prop("product"),
				"id" => $row->id(),
				"amount" => $row->prop("amount"),
				"unit" => $row->prop("unit"),
				"price" => $row->prop("price"),
				"currency" => $row->prop("currency"),
				"shipment" => $row->prop("shipment"),
				"price_amount" => $min_amount,
			);
		}
		foreach($prod_rows_data as $prod_row_data)
		{
			extract($prod_row_data);
			$t->define_data(array(
				"jrk"		=> $x+1,
//				"row_id" 	=> $row->id(),
				"available" 	=> $available,
				"product"	=> html::textbox(array(
							"name" => "products[".$x."][product]",
							"size" => "40",
							"value" => $product,
							"autocomplete_source" => $procurement_inst->mk_my_orb("product_autocomplete_source", array("buyer" =>$co->id()), CL_PROCUREMENT, false, true),
							"autocomplete_params" => "products[".$x."][product]",
							"tabindex" => $x,
							))
						.html::hidden(array(
								"name" => "products[".$x."][row_id]",
								"value" => $id)),

				"amount"	=> html::textbox(array(
							"name" => "products[".$x."][amount]",
							"size" => "6",
							"value" =>  $amount,
							"tabindex" => $x,
							)),
				'unit'		=> html::select(array(
							"name" => "products[".$x."][unit]",
							"options" => $unit_opts,
							"value" => $unit,
							"tabindex" => $x,
							)),
				'price'		=> html::textbox(array(
							"name" => "products[".$x."][price]",
							"size" => "6",
							"value" => number_format($price, 2, ".", " "),
							"tabindex"=>$x,
							)),
				'price_amount'		=> html::textbox(array(
							"name" => "products[".$x."][price_amount]",
							"size" => "6",
							"value" => $price_amount,
							"tabindex"=>$x,
							)),
				'currency'	=> html::select(array(
							"name" => "products[".$x."][currency]",
							"options" => $curr_opts,
							"value" => $currency,
							)),
				'shipment'	=> html::date_select(array(
							"name" => "products[".$x."][shipment]",
							"format" => array("day_textbox", "month_textbox", "year_textbox"),
							"value" => $shipment,
						//	"size" => "6",
							)),
	//			html::textbox(array(
	//						"name" => "products[".$x."][shipment]",
	//						"size" => "6",
	//						"value" => $row->prop("shipment"),
	//						)),
				'accept'	=> $accept,
				"oid"		=> $id,
				"total" => number_format($amount * $price, 2, ".", " "),
				"available" => html::checkbox(array(
					"name" => "products[".$x."][available]",
					"value" => 1,
					"checked" => 1,
				)),

			));
			$total_sum += $amount * $price;
		}
		//lisaread
		//$x = -10;
		$x = $max_x + 1;
		$lisa = $x + 10;
		if(is_object($co))$curr_val = $co->prop("currency");
		if($this_obj->prop("currency"))
		{
			$curr_val = $this_obj->prop("currency");
		}
		while($x < $lisa)
		{
			$t->define_data(array(
			//	"jrk"		=> $x+2,
				"product"	=> html::textbox(array(
							"name" => "products[".$x."][product]",
							"size" => "40",
							"autocomplete_source" => $procurement_inst->mk_my_orb ("product_autocomplete_source", array("buyer" =>$co->id()), CL_PROCUREMENT, false, true),
							"autocomplete_params" => "products[".$x."][product]",
							)),
				"amount"	=> html::textbox(array(
							"name" => "products[".$x."][amount]",
							"size" => "6",
							)),
				'unit'		=> html::select(array(
							"name" => "products[".$x."][unit]",
							"options" => $unit_opts,
							)),
				'price'		=> html::textbox(array(
							"name" => "products[".$x."][price]",
							"size" => "6",
							)),
				'price_amount'	=> html::textbox(array(
							"name" => "products[".$x."][price_amount]",
							"size" => "6",
							)),

				'currency'	=> html::select(array(
							"name" => "products[".$x."][currency]",
							"options" => $curr_opts,
							"value" => $curr_val,
							)),
				'shipment'	=> html::date_select(array(
							"name" => "products[".$x."][shipment]",
							"format" => array("day_textbox", "month_textbox", "year_textbox"),							"value" => $this_obj->prop("shipment_date"),
						//	"size" => "6",
							)),
//				'accept'	=> html::checkbox(array(
//							"name" => "products[".$x."][accept]",
//							)),
				'oid'		=> $x,
				"available" => html::checkbox(array(
					"name" => "products[".$x."][available]",
					"value" => 1,
					"checked" => 1,
				)),
			));
			$x++;
		}

	/*
Ühik (lb, süsteemi Ühikute koodidega), Valuuta (lb, süsteemi valuutadega, vaikimisi Minu Organisatsiooni vaikimisi valitud valuuta), Tarneaeg (kp tekstiväljana, kus lõpus on ?vali? link), Aktsept (cb). Tooteväli on Autocomplete põhimõttel ehitatud, loetakse tooteid seotud laost. Kui sisestatakse tootenimetus, mida varem laos ei ole, siis salvestatakse see uue tootena, kuid enne küsitakse popup aknas tootekategooria (kui mitu uut toodet, siis on küsimise tabelis mitu rida). Tootekategooria kuvatakse listboxina, erinevad tasemed on trepitud (tähestiku järjekord). Peale 10 rea salvestamist tekib võimalus uue 10 rea sisestamiseks. Juhul, kui Tarneaeg jäetakse toote taga tühjaks, kuvatakse peale salvestamist sinna sama kuupäev, kui Pakkumises määratud tarne tähtaeg. */
		$t->set_sortable(false);

		$t->define_data(array(
			"currency" => t("<b>Summa:</b>"),
			"total" => number_format($total_sum, 2, ".", " ")
		));
		//$t->set_default_sortby("jrk");
	}


	function callback_post_save($arr)
	{
		if($arr["new"]==1 && is_oid($arr["request"]["offerer"]) && $this->can("view" , $arr["request"]["offerer"]))
		{
			$arr["obj_inst"]->set_prop("offerer" , $arr["request"]["offerer"]);
// 		arr($arr);
// 		arr($arr["obj_inst"]->prop("procurement"));
		}
	}

	function get_avg_score($offer)
	{
		// get all prefered solutions in offer and their scores
		$reqs = $this->model->get_requirements_from_procurement(obj($offer->prop("procurement")));
		$sum = 0;
		$cnt = 0;
		$def = $offer->meta("defaults");
		foreach($reqs->arr() as $req)
		{
			// get the preferred solution for this requirement in this offer
			$sol = $def[$req->id()];
			if ($sol)
			{
				$ass = $req->meta("assessments");
				$sum += $ass[$sol];
			}
			$cnt++;
		}
		return $sum / $cnt;
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
			CL_FILE => t("&nbsp;"),
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
				$u = get_instance(CL_USER);
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
			"caption" => t("&nbsp;"),
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
		$u = get_instance(CL_USER);
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
					$f = get_instance(CL_FILE);

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
					$procurement = obj($t->prop("procurement"));

					if ($entry["type"] != CL_FILE)
					{
						$o->set_prop("project", $t->id());
					//	$o->set_prop("customer", reset($procurement->prop("orderer")));
					}
					$o->save();

					// add file
					$f = get_instance(CL_FILE);

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

	function callback_generate_scripts($arr)
	{
/*		if($arr["obj_inst"]->id() > 0)
		$conns = $arr["obj_inst"]->connections_to(array(
			'class' => CL_PROCUREMENT_OFFER_ROW,
		));
		else return;
		$procurement_inst = get_instance(CL_PROCUREMENT);
		$max_x = 0;
		$xs = array();
		foreach($conns as $conn)
		{
			$x = $conn->prop("from");
			$xs[] = $x;
			if($max_x < $x) $max_x = $x;
		}

		$ret = "

		function changeContent(id,sb_val)
		{
		var x=document.getElementById(id).rows[0].cells
		x[0].innerHTML=sb_val
		}

		function aw_submit_handler() {".
		""."var url = '".$procurement_inst->mk_my_orb("check_existing")."';";
		// fetch list of companies with that name and ask user if count > 0
		$procurement_inst = get_instance(CL_PROCUREMENT);
		foreach($xs as $s)
		{
			$ret.= "url = url + '&p[".$s."]=' + escape(document.changeform.products_".$s."__product_.value);
			";
		}
		$max_x++;
		$size = sizeof($max_x + 10);
		$x = $max_x;
		while($x < $size)
		{
			$ret.= "url = url + '&p[".$x."]=' + escape(document.changeform.products_".$x."__product_.value);
				";
			$x++;
		}

		$ret.= "num= aw_get_url_contents(url);".
		"if (num != \"\")
		{
			var ansa = confirm(num);
			if (ansa)
			{
				return true;
			}
			return false;
		}".
		"return true;}
		";
		return $ret;
*/	}

}
?>
