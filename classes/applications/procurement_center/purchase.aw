<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/procurement_center/purchase.aw,v 1.14 2007/11/23 11:05:13 markop Exp $
// purchase.aw - Ost 
/*

@tableinfo aw_purchase index=aw_oid master_index=brother_of master_table=objects maintainer=markop

@classinfo syslog_type=ST_PURCHASE relationmgr=yes no_comment=1 no_status=1 prop_cb=1

@default table=objects
@default group=general
@default field=meta

@property date type=date_select table=aw_purchase field=aw_date
@caption Kuup&auml;ev

@property buyer type=relpicker reltype=RELTYPE_BUYER table=aw_purchase field=aw_buyer
@caption Ostja

@property offerer type=relpicker reltype=RELTYPE_OFFERER table=aw_purchase field=aw_offerer
@caption Hankija

@property stat type=select table=aw_purchase field=aw_stat
@caption Staatus

@property deal_no type=textbox table=aw_purchase field=aw_deal_no
@caption Lepingu/arve nr

@property sum type=textbox table=aw_purchase field=aw_sum
@caption Summa

@groupinfo offers caption=Pakkumised submit=no 
@default group=offers

@property offers_add type=toolbar no_caption=1 store=no
@property offers type=table no_caption=1 store=no reltype=RELTYPE_OFFER

@groupinfo files caption=Failid 
@default group=files

@property files_tb type=toolbar no_caption=1 store=no

@property files type=text  no_caption=1
@caption Manused

@property files_table type=table no_caption=1

property files type=fileupload no_caption=1 store=yes table=objects field=meta method=serialize form=+emb
caption Failid 

@groupinfo purchases caption=Ostud
@default group=purchases

@property purchases type=table no_caption=1


Ost on selline objektitüüp, mis seob omavahel Ostja (reeglina meie ise), Hankija ning mingid pakkumised. Esialgu piisab järgmistest propertytest (edaspidi on võimalik Ostu juures Pakkumise ridade kuvamine ja kuupäevade valimine, millal mingi Ostu osa täideti):
Nimetus (siia kirjutatakse Ostu number)
Kuupäev
Ostja (vaikimisi minu organisatsioon, kuid saab otsida ka teisi)
Hankija (seos tarne teinud organisatsiooniga) 
Staatus (aktiivne, arhiveeritud)
Pakkumised (eraldi TAB, mille all on näha kõik pakkumised, mis on selle Ostuga seotud). Tabelis pakkumise kuupäev, pakkumise failid, vali. Toolbaril otsing, mille abil saab seostada teisi sama Hankija pakkumisi selle Ostu juurde. Samuti kustutamine (saab juba seostatud pakkumise eemaldada Ostu küljest).
Failid (eraldi TAB) ? saab uploadida samamoodi faile nagu Pakkumise juurde
Ostud (kuvatakse pakkumise read, mis on selle ostuga seotud)

@reltype BUYER value=1 clid=CL_CRM_PERSON,CL_CRM_COMPANY
@caption Ostja 

@reltype OFFER value=2 clid=CL_PROCUREMENT_OFFER
@caption Pakkumine 

@reltype OFFERER value=3 clid=CL_CRM_PERSON,CL_CRM_COMPANY
@caption Pakkuja
*/
class purchase extends class_base
{
	const AW_CLID = 1128;

	function purchase()
	{
		$this->init(array(
			"tpldir" => "applications/procurement_center/purchase",
			"clid" => CL_PURCHASE
		));
		$this->stats = array(0 => t("aktiivne") , 1 => t("arhiveeritud"));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "buyer":
				if(!$arr["obj_inst"] -> prop("buyer"))
				{
					$u = new user();
					$co = obj($u->get_current_company());
					$prop["options"][$co->id()] = $co->name();
					$prop["value"] = $co->id();
				}
				break;
			case "stat":
				$prop["options"] = $this->stats;
				break;
			
			
			case "files":
				$file_inst = new file();
				//$url = $file_inst->get_url($arr["obj_inst"]->prop($prop["name"]));
				$prop["value"] = "";
				foreach($arr["obj_inst"]->prop("files") as $id)
				{
					$caption = "";
					if(is_oid($id))
					{
						$file_obj = obj($id);
						$caption = $file_obj->name();
						$prop["value"].= html::href(array("url" => $file_inst->get_url($id,$name), "caption" => $caption))."<br>";
					}
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
					"action" => "remove_offers",
				));
				break;
			
			case "offers_add":
				$tb =&$arr["prop"]["vcl_inst"];
				$pps = get_instance("applications/procurement_center/procurement_offer_search");
				$prop["value"] = $pps->get_popup_search_link(array(
					"pn" => "rows[$id][person]",
					"multiple" => 1,
					"clid" => array(CL_CRM_PERSON)
				)).$prop["value"];
				

//				$search_url = $this->mk_my_orb("do_search", array(
//						"id" => $arr["obj_inst"]->id(),
//						"pn" => "offers",
//						"clid" => array(CL_PROCUREMENT_OFFER),
//						"multiple" => "",
//					), "popup_search");
//
/*				$url = $pps->get_popup_search_link(array(
					"pn" => "offers",
					"multiple" => 1,
					"clid" => array(CL_PROCUREMENT_OFFER)
				));
				
*/				$url = $pps->mk_my_orb("do_search", array(
						"pn" => "offers",
						"clid" => array(
							CL_PROCUREMENT_OFFER,
						),
						"multiple" => 1,
						"s" => array("offerer" => $arr["obj_inst"]->prop_str("offerer")),
		//				"tbl_props" => array(0 => "offerer"),
		//				"search_props" => array("offerer"),
						));
				$tb->add_button(array(
					"name" => "search",
					"img" => "search.gif",
					"url" => "javascript:aw_popup_scroll('$url','".t("Otsi")."',550,500)",
//					"url" => $url,
					'tooltip' => t('Otsi'),
				));

				$tb->add_button(array(
					'name' => 'delete',
					'img' => 'delete.gif',
					'tooltip' => t('Kustuta'),
					"action" => "remove_offers",
				));
				break;
				
			case "offers":
				$t =& $arr["prop"]["vcl_inst"];
				$t->define_field(array(
					"name" => "name",
					"caption" => t("Nimi"),
					"align" => "center",
					"sortable" => 1
				));
				$t->define_field(array(
					"name" => "date",
					"caption" => t("Kuup&auml;ev"),
					"align" => "center",
					"sortable" => 1
				));
				$t->define_field(array(
					"name" => "files",
					"caption" => t("Pakkumise failid"),
					"align" => "center",
				));
				$t->define_chooser(array(
					"name" => "sel",
					"field" => "oid",
					"caption" => t("Vali"),
				));
				
				$conns = $arr["obj_inst"]->connections_from(array(
					'type' => "RELTYPE_OFFER",
				));
				$file_inst = new file();
				foreach($conns as $conn)
				{
					if(is_oid($conn->prop("to")))$row = obj($conn->prop("to"));
					else continue;
					$offer_obj = obj($conn->prop("to"));
					
					$file_conns = $offer_obj->connections_from(array(
						'class' => array(CL_FILE,CL_CRM_MEMO,CL_CRM_DOCUMENT,CL_CRM_DEAL,CL_CRM_OFFER,CL_FILE),
					));
					$files = "";
					
					foreach($file_conns as $file_conn)
					{
						$file_obj = obj($file_conn->prop("to"));
						
						if($file_obj->class_id() == CL_FILE)
						{
							$files.= html::href(array(
							"url" => $file_inst->get_url($file_conn->prop("to"),$name),
							//"url" => $f_obj->prop("file_url"),
							"caption" => $file_conn->prop("to.name")))
							."<br>";
						}
						foreach($file_obj->connections_from(array('type' => RELTYPE_FILE,)) as $f_conn)
						{
							$f_obj = obj($f_conn->prop("to"));
							$files.= html::href(array(
							"url" => $file_inst->get_url($f_conn->prop("to"),$name),
							//"url" => $f_obj->prop("file_url"),
							"caption" => $f_conn->prop("to.name")))
							."<br>";
						}
					}
					$t->define_data(array(
						"name" => $offer_obj->name(),
						"date" => date('d-m-y/h:m', $offer_obj->prop("accept_date")),
						"files" => $files,
						"oid" => $offer_obj->id(),
					));
				}
				break;
			case "purchases":
				$prop["value"] = $this->rows_table(&$arr["prop"]["vcl_inst"],$arr["obj_inst"]);
				break;
		};
		return $retval;
	}

	function _init_files_tbl(&$t)
	{
		$t->define_field(array(
			"caption" => t(""),
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
			"caption" => t(""),
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
		$pt = $this->_get_files_pt($arr);
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_files_tbl($t);


		$ol = new object_list($arr["obj_inst"]->connections_from(array()));
		
		classload("core/icons");
		$clss = aw_ini_get("classes");
		new file();
		foreach($ol->arr() as $o)
		{
			if(!(($o->class_id() == CL_FILE) || ($o->class_id() == CL_CRM_DOCUMENT) || ($o->class_id() == CL_CRM_DEAL) || ($o->class_id() == CL_CRM_OFFER) || ($o->class_id() == CL_CRM_MEMO))) continue;
			$pm = new popup_menu();
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

	
	
	function rows_table(&$t , $this_obj)
	{
		$t->define_field(array(
			"name" => "product",
			"caption" => t("Toode"),
		));
		$t->define_field(array(
			'name' => 'amount',
			'caption' => t('Kogus'),
		));
		$t->define_field(array(
        		'name' => 'price',
			'caption' => t('Hind'),
		));
		$t->define_field(array(
			'name' => 'unit',
			'caption' => t('&Uuml;hik'),
		));
		$t->define_field(array(
			'name' => 'b_amount',
			'caption' => t('Ostetav kogus'),
		));
		$t->define_field(array(
        		'name' => 'b_price',
			'caption' => t('&Uuml;hiku ostu hind'),
		));		
		$t->define_field(array(
        		'name' => 'sum',
			'caption' => t('Summa'),
		));
		$t->define_field(array(
			'name' => 'currency',
			'caption' => t('Valuuta'),
		));
		$t->define_field(array(
			'name' => 'shipment',
			'caption' => t('Tarneaeg'),
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
		
		$offers = $this_obj->connections_from(array(
			'type' => "RELTYPE_OFFER",
		));
		foreach($offers as $offer_conn)
		{
		$offer_obj = obj($offer_conn->prop("to"));
		$conns = $offer_obj->connections_to(array(
			'reltype' => 1,
			'class' => CL_PROCUREMENT_OFFER_ROW,
		));
		foreach($conns as $conn)
		{
			if(is_oid($conn->prop("from")))$row = obj($conn->prop("from"));
			else continue;
			if(!$row->prop("accept")) continue;
			$unit = ""; $currency = "";
			if(is_oid($row->prop("unit")))
			{
				$unit_obj = obj($row->prop("unit"));
				$unit = $unit_obj->prop("unit_code");
			}
			if(is_oid($row->prop("currency")))
			{
				$currency = obj($row->prop("currency"));
				$currency = $currency->name();
			}
			if(!$row->prop("b_price"))
			{
				$row->set_prop("b_price" ,$row->prop("price"));
			}
			if(!$row->prop("b_amount"))
			{
				$row->set_prop("b_amount" ,$row->prop("amount"));
			}
			$b_amount = $row->prop("b_amount");
			$b_price = $row->prop("b_price");
			if(is_array($b_amount))$b_amount = $b_price["amount"];
			if(is_array($b_price))$b_price = $b_price["price"];

			$t->define_data(array(
				"row_id" 	=> $row->id(),
				"product"	=> $row->prop("product"),
				"amount"	=> $row->prop("amount"),
				'price'		=> $row->prop("price"),
				'unit'		=> $unit,
				
				'b_amount'	=>html::textbox(array(
								"name" => "buyings[".$row->id()."][amount]",
								"value" => $row->prop("b_amount"),
								"size" => 5
							)),
						//$row->prop("amount"),
				'b_price'	=> html::textbox(array(
								"name" => "buyings[".$row->id()."][price]",
								"value" => $row->prop("b_price"),
								"size" => 5
							)),
						//$row->prop("price"),
				'sum'		=> $b_amount*$b_price,
				'currency'	=> $currency,
				'shipment'	=> date("d.m.Y", $row->prop("shipment")),
			));
		}
		}
		$t->set_sortable(false);
	}

	/**gives you sum of buyings
		@attrib name=get_sum
	**/
	function get_sum($o)
	{
		if(is_oid($o) && $this->can("view" , $o))
		{
			$o=obj($o);
		}
		if(!is_object($o))
		{
			return "";
		}
		if($o->prop("sum"))
		{
			return $o->prop("sum");
		}
		else 
		{
			$sum = 0;
			$offers = $o->connections_from(array(
				'type' => "RELTYPE_OFFER",
			));
			foreach($offers as $offer_conn)
			{
				$offer_obj = obj($offer_conn->prop("to"));
				$conns = $offer_obj->connections_to(array(
					'reltype' => 1,
					'class' => CL_PROCUREMENT_OFFER_ROW,
				));
				foreach($conns as $conn)
				{
					if(is_oid($conn->prop("from")))
					{
						$row = obj($conn->prop("from"));
						if(!$row->prop("accept")) continue;
						if(!$row->prop("b_price"))
						{
							$sum = $sum + (int)($row->prop("price") + $row->prop("amount"));
						}
						else
						{
							$b_amount = $row->prop("b_amount");
							$b_price = $row->prop("b_price");
							if(is_array($b_amount))$b_amount = $b_price["amount"];
							if(is_array($b_price))$b_price = $b_price["price"];
							$sum = $sum + $b_amount*$b_price;
						}
					}
				}
			}
		}
		return $sum;
	}

	/**
		@attrib name=remove_offers
	**/
	function remove_offers($arr)
	{
		$this_obj = obj($arr["id"]);
		foreach($arr["sel"] as $offer)
		{
			$off_obj = obj($offer);
			$this_obj->disconnect(array("from" => $offer));
		}
		return $arr["post_ru"];
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
/*			case "files":
					$prop["value"] = $arr["obj_inst"]->prop("files");
					if (isset($_FILES["files"]["tmp_name"]))
					{
						$src_file = $_FILES["files"]["tmp_name"];
						$ftype = $_FILES["files"]["type"];
					}
					else
					if (isset($prop["value"]["tmp_name"]))
					{
						$src_file = $prop["value"]["tmp_name"];
						$ftype = $prop["value"]["type"];
					};
					if (is_uploaded_file($src_file))
					{
						$_fi = new file();
						$file_data = $_fi->add_upload_image("files" , $arr["obj_inst"]->id());
						$prop["value"][] = $file_data["id"];
					}
				break;
*/
			case "files":
				$this->_set_files($arr);
				break;
		
			case "purchases":
				$this->_save_buyings($arr);
				break;
			//-- set_property --//
		}
		return $retval;
	}	

	function _save_buyings($arr)
	{
		foreach($arr["request"]["buyings"] as $key => $offer_row)
		{
//			$arr["obj_inst"]->set_meta("buyings" , $arr["request"]["buyings"]);
//			$arr["obj_inst"]->save();
			if(is_oid($key) && $this->can("view" , $key))
			{
				$row = obj($key);
				$row->set_prop("b_amount" ,$offer_row["amount"]);
				$row->set_prop("b_price", $offer_row["price"]);
				$row->save();
			}
		}
	}

	function callback_mod_reforb($arr)
	{
		$arr["offers"] = 0;
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
						$o->set_prop("customer", reset($t->prop("buyer")));
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


	function callback_post_save($arr)
	{
		if ($_POST["offers"] > 0)
		{
			foreach(explode(",", $_POST["offers"]) as $proj)
			{
				$arr["obj_inst"]->connect(array(
					"to" => $proj,
					"type" => "RELTYPE_OFFER"
				));
			}
		}
	}

	function do_db_upgrade($t, $f)
	{
		if ($t == "aw_purchase" && $f == "")
		{
			$this->db_query("CREATE TABLE aw_purchase (aw_oid int primary key, aw_sum double)");
			return true;
		}
		switch($f)
		{
			case "aw_date":
			case "aw_buyer":
			case "aw_offerer":
			case "aw_stat":
			case "aw_deal_no":
				$this->db_add_col($t, array("name" => $f, "type" => "int"));
				return true;
		}
	}

}
?>
