<?php
/*
@classinfo syslog_type=ST_SHOP_PRICE_LIST relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=instrumental

@tableinfo aw_shop_price_list master_index=brother_of master_table=objects index=aw_oid

@default table=aw_shop_price_list
@default group=general

	@groupinfo general2 caption="&Uuml;ldseaded" parent=general
	@default group=general2

		@property name type=textbox table=objects
		@caption Nimi

		@property jrk type=textbox size=4 table=objects
		@caption Jrk

		@property shop type=relpicker reltype=RELTYPE_SHOP field=aw_shop
		@caption E-pood

		@property valid_from type=date_select field=valid_from
		@caption Kehtib alates

		@property valid_to type=date_select field=valid_to
		@caption Kehtib kuni

	@groupinfo matrix_settings caption="Maatriksi seaded" parent=general
	@default group=matrix_settings
			
			@property matrix_col_order type=table store=no
			@caption Veeru gruppide j&auml;rjekord
			
			@property matrix_advanced type=checkbox field=aw_matrix_advanced
			@caption Power-user mode
		
		@property matrix_cols_subtitle type=text subtitle=1 store=no
		@caption Maatriksi veerud

			@property matrix_customer_categories type=relpicker reltype=RELTYPE_CUSTOMER_CATEGORY multiple=1 store=connect
			@caption Kliendikategooriad

			@property matrix_countries type=relpicker reltype=RELTYPE_COUNTRY multiple=1 store=connect
			@caption Riigid

		@property matrix_rows_subtitle type=text subtitle=1 store=no
		@caption Maatriksi read
		
			@property matrix_rows type=chooser field=aw_matrix_rows multiple=1 orient=vertical table=objects field=meta method=serialize
			@caption Maatriksi read
			
			@property matrix_product_categories type=relpicker reltype=RELTYPE_PRODUCT_CATEGORY multiple=1 store=connect
			@caption Tootekategooriad

		@property code type=hidden field=aw_code

@groupinfo matrix caption=Maatriks
@default group=matrix

	@property matrix_tlb type=toolbar no_caption=1 store=no

	@property matrix type=table no_caption=1 store=no

@groupinfo priorities caption=Prioriteedid
@default group=priorities

	@groupinfo priorities_customer_categories caption=Kliendigrupid parent=priorities
	@default group=priorities_customer_categories

		@property priorities_customer_categories_tbl type=table no_caption=1 store=no

	@groupinfo priorities_locations caption=Asukohad parent=priorities
	@default group=priorities_locations

		@property priorities_locations_tbl type=table no_caption=1 store=no

	@groupinfo priorities_product_categories caption=Tootegrupid parent=priorities
	@default group=priorities_product_categories

		@property priorities_product_categories_tbl type=table no_caption=1 store=no

@groupinfo debug caption=Hinnakalkulaator
@default group=debug

	@property debug_product type=relpicker reltype=RELTYPE_DEBUG_SHOP_PRODUCT automatic=1 table=objects field=meta method=serialize no_edit=1 search_button=1
	@caption Toode

	@property debug_product_packaging type=relpicker reltype=RELTYPE_DEBUG_SHOP_PRODUCT_PACKAGING automatic=1 table=objects field=meta method=serialize no_edit=1 search_button=1
	@caption Pakend

	@property debug_amount type=textbox size=6 table=objects field=meta method=serialize
	@caption Kogus

	@property debug_prices type=table store=no
	@caption Hinnad k&otilde;igis valuutades

	@property debug_product_category type=relpicker reltype=RELTYPE_DEBUG_SHOP_PRODUCT_CATEGORY multiple=1 automatic=1 table=objects field=meta method=serialize no_edit=1 search_button=1
	@caption Tootekategooria

	@property debug_customer_category type=relpicker reltype=RELTYPE_DEBUG_CRM_CATEGORY multiple=1 automatic=1 table=objects field=meta method=serialize no_edit=1 search_button=1
	@caption Kliendikategooria

	@property debug_location type=relpicker reltype=RELTYPE_DEBUG_LOCATION multiple=1 automatic=1 table=objects field=meta method=serialize no_edit=1 search_button=1
	@caption Asukoht

	@property debug_output type=text store=no
	@caption V&auml;ljund

@groupinfo price_lists caption="Teised hinnastajad"
@default group=price_lists

	@layout price_lists_split type=hbox width=25%:75%

		@layout price_lists_left type=vbox parent=price_lists_split area_caption=Vali&#44&nbsp;milliseid&nbsp;hinnastajaid&nbsp;kuvada

			@property price_lists_tree type=treeview store=no no_caption=1 parent=price_lists_left

		@layout price_lists_right type=vbox parent=price_lists_split no_padding=no closeable=1 area_caption=Hinnastajad

			@property price_lists_tbl type=table store=no no_caption=1 parent=price_lists_right

### RELTYPES

@reltype GROUP value=1 clid=CL_GROUP
@caption Grupp

@reltype ORG value=2 clid=CL_CRM_COMPANY
@caption Organisatsioon

@reltype PERSON value=3 clid=CL_CRM_PERSON
@caption Isik

@reltype CATEGORY value=4 clid=CL_SHOP_PRODUCT_CATEGORY
@caption Kaubagrupp

@reltype ORG_CAT value=5 clid=CL_CRM_CATEGORY
@caption Kliendigrupp

@reltype WAREHOUSE value=6 clid=CL_SHOP_WAREHOUSE
@caption Ladu

@reltype MATRIX_CATEGORY value=7 clid=CL_SHOP_PRODUCT_CATEGORY
@caption Kaubagrupp

@reltype MATRIX_ORG_CAT value=8 clid=CL_CRM_CATEGORY
@caption Kliendigrupp

@reltype SHOP value=9 clid=CL_SHOP_ORDER_CENTER
@caption Omanikorganisatsioon

@reltype CUSTOMER_CATEGORY value=10 clid=CL_CRM_CATEGORY
@caption Kliendikategooria, mida maatriksi veeruna kuvatakse

@reltype PRODUCT_CATEGORY value=11 clid=CL_SHOP_PRODUCT_CATEGORY
@caption Tootekategooria, mida maatriksi reana kuvatakse

@reltype PRIORITY value=12
@caption Prioriteet

@reltype COUNTRY value=13 clid=CL_COUNTRY
@caption Riik, mida maatriksi veeruna kuvatakse

@reltype DEBUG_SHOP_PRODUCT value=14 clid=CL_SHOP_PRODUCT
@caption Hinnakalkulaatori toode

@reltype DEBUG_SHOP_PRODUCT_CATEGORY value=15 clid=CL_SHOP_PRODUCT_CATEGORY
@caption Hinnakalkulaatori tootekategooria

@reltype DEBUG_CRM_CATEGORY value=16 clid=CL_CRM_CATEGORY
@caption Hinnakalkulaatori tootekategooria

@reltype DEBUG_LOCATION value=17 clid=CL_COUNTRY_CITY,CL_COUNTRY_CITYDISTRICT,CL_COUNTRY_ADMINISTRATIVE_UNIT
@caption Hinnakalkulaatori asukoht

@reltype DEBUG_SHOP_PRODUCT_PACKAGING value=18 clid=CL_SHOP_PRODUCT_PACKAGING
@caption Hinnakalkulaatori pakend

*/

class shop_price_list extends shop_matrix
{
	function shop_price_list()
	{
		$this->init(array(
			"tpldir" => "applications/shop/shop_price_list",
			"clid" => CL_SHOP_PRICE_LIST
		));
	}

	public function callback_pre_edit($arr)
	{
		$this->obj = $arr["obj_inst"];
		return parent::callback_pre_edit($arr);
	}

	public function _get_debug_prices($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		$t->set_sortable(false);

		$t->define_field(array(
			"name" => "currency",
			"caption" => t("Valuutakurss"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "price",
			"caption" => t("Hind"),
			"align" => "center",
		));

		$ol = new object_list(array(
			"class_id" => CL_CURRENCY,
			"lang_id" => array(),
			"site_id" => array(),
			new obj_predicate_sort(array(
				"name" => "ASC",
			)),
		));
		$meta = $arr["obj_inst"]->meta("debug_prices");
		foreach($ol->names() as $oid => $name)
		{
			$t->define_data(array(
				"currency" => $name,
				"price" => html::textbox(array(
					"name" => "debug_prices[$oid]",
					"value" => isset($meta[$oid]) ? $meta[$oid] : 0,
					"size" => 6,
				)),
			));
		}
	}

	public function _set_debug_prices($arr)
	{
		$arr["obj_inst"]->set_meta("debug_prices", $arr["prop"]["value"]);
	}

	public function _get_debug_output($arr)
	{
		$prms = array(
			"shop" => $arr["obj_inst"]->prop("shop"),
			"product" => $arr["obj_inst"]->prop("debug_product"),
			"product_packaging" => $arr["obj_inst"]->prop("debug_product_packaging"),
			"amount" => $arr["obj_inst"]->prop("debug_amount"),
			"prices" => $arr["obj_inst"]->meta("debug_prices"),
			"product_category" => $arr["obj_inst"]->prop("debug_product_category"),
			"customer_category" => $arr["obj_inst"]->prop("debug_customer_category"),
			"location" => $arr["obj_inst"]->prop("debug_location"),
			"structure" => true
		);

		//need propertid saab ju samas vaates m22rata, kuid kui neid pole, siis annab errorit, - v2ike ebak6la
		if(!$arr["obj_inst"]->prop("debug_product") && !$arr["obj_inst"]->prop("debug_product_packaging"))
		{
			return;
		}

		$mtime = explode(' ', microtime()); 
		$starttime = $mtime[1] + $mtime[0];
		for($i = 0; $i < 1; $i++)
		{
			$result = shop_price_list_obj::price($prms);
			unset($prms["structure"]);
			$short_result = shop_price_list_obj::price($prms);
		}
		$mtime = explode(" ", microtime());
		$endtime = $mtime[1] + $mtime[0];

		$arr["prop"]["value"] = "<pre>".print_r(array(
			"p&auml;ringu parameetrid" => $prms,
			"l&uuml;hike tulemus" => $short_result,
			"tulemus" => $result,
			"hinnapäringuks kulunud aeg" => (($endtime - $starttime)/2)." sekundit",
		), true)."</pre>";
	}

	public function _get_matrix($arr)
	{
		$matrix = array();
		$odl = new object_data_list(
			array(
				"class_id" => CL_SHOP_PRICE_LIST_CONDITION,
				"price_list" => $arr["obj_inst"]->id(),
				"currency" => $this->currency,
				"lang_id" => array(),
				"site_id" => array(),
			),
			array(
				CL_SHOP_PRICE_LIST_CONDITION => array("row", "col", "type", "value", "bonus", "quantities"),
			)
		);
		foreach($odl->arr() as $cond)
		{
			$cond["col"] = is_oid($cond["col"]) ? $cond["col"] : "default";
			$cond["row"] = is_oid($cond["row"]) ? $cond["row"] : "default";
			$matrix[$cond["row"]][$cond["col"]]["type"] = $cond["type"];
			$matrix[$cond["row"]][$cond["col"]]["value"] = $cond["value"];
			$matrix[$cond["row"]][$cond["col"]]["bonus"] = $cond["bonus"];
			$matrix[$cond["row"]][$cond["col"]]["quantities"] = $cond["quantities"];
		}

		$this->draw_matrix(array(
			"table_inst" => &$arr["prop"]["vcl_inst"],
			"obj_inst" => &$arr["obj_inst"],
			"column_types" => $this->col_types,
			"matrix_data" => $matrix,
			"field_callback" => array(&$this, "modify_matrix_fields"),
			"data_cell_callback" => array(&$this, "draw_matrix_cell"),
			"data_callback" => array(&$this, "modify_matrix_row"),
		));
		$arr["prop"]["vcl_inst"]->set_caption(sprintf(t("Hinnastaja maatrix valuutakursile '%s'"), obj($this->currency)->name()));
	}

	public function modify_matrix_fields(&$t, $name)
	{
		if(!$this->obj->matrix_advanced)
		{
			$t->define_field(array(
				"name" => $name."_value",
				"caption" => "HV",
				"tooltip" => t("Hinna valem"),
				"parent" => $name,
				"chgbgcolor" => $name."_color",
			));
			$t->define_field(array(
				"name" => $name."_bonus",
				"caption" => "BV",
				"tooltip" => t("Boonuse valem"),
				"parent" => $name,
				"chgbgcolor" => $name."_color",
			));
			$t->define_field(array(
				"name" => $name."_quantities",
				"caption" => "K",
				"tooltip" => t("Kogused, millele valemirida rakendub (vaikimisi rakendub k&otilde;igile kogustele)"),
				"parent" => $name,
				"chgbgcolor" => $name."_color",
			));
			$t->define_field(array(
				"name" => $name."_type",
				"caption" => "VT",
				"tooltip" => t("Valemi t&uuml;&uuml;p"),
				"parent" => $name,
				"chgbgcolor" => $name."_color",
			));
		}
	}

	public function draw_matrix_cell($row, $field, $matrix)
	{
		$name = explode("_", $field["name"]);
		if(!$this->obj->matrix_advanced)
		{
			$type = array_pop($name);
			$col = array_pop($name);
			if($col === "self")
			{
				$col = array_pop($name);
			}

			switch($type)
			{
				case "type":
					static $options = array();
					if(empty($options))
					{
						$options = array(t("Auto"));
						$ol = new object_list(array(
							"class_id" => CL_SHOP_PRICE_LIST_CONDITION_TYPE,
						));
						$options = $options + $ol->names();
					}
					return html::select(array(
						"name" => "matrix[$row][$col][$type]",
						"value" => ifset($matrix, $row, $col, $type),
						"options" => $options,
					));

				case "value":
				case "bonus":
					return html::textbox(array(
						"name" => "matrix[$row][$col][$type]",
						"value" => ifset($matrix, $row, $col, $type),
						"size" => 4,
						"maxlength" => 30,
					));

				case "quantities":
					return html::textbox(array(
						"name" => "matrix[$row][$col][$type]",
						"value" => ifset($matrix, $row, $col, $type),
						"size" => 4,
						"maxlength" => 100,
					));
			}
		}
		else
		{
			$col = array_pop($name);
			if($col === "self")
			{
				$col = array_pop($name);
			}
			return html::href(array(
				"url" => "",
				"caption" => $col,
			));
		}
	}

	public function modify_matrix_row($t, $data)
	{
		static $i;
		$i++;

		$j = 0;
		foreach($t->get_defined_fields() as $field)
		{
			if(isset($field["chgbgcolor"]) && !isset($data[$field["chgbgcolor"]]))
			{
				$data[$field["chgbgcolor"]] = ($i + $j++) % 2 == 0 ? "#C4C4C4" : "#D7D7D7";
			}
		}
	}

	public function _set_matrix($arr)
	{
		$data = $arr["prop"]["value"];

		$odl = new object_data_list(
			array(
				"class_id" => CL_SHOP_PRICE_LIST_CONDITION,
				"price_list" => $arr["obj_inst"]->id(),
				"currency" => automatweb::$request->arg("currency"),
				"lang_id" => array(),
				"site_id" => array(),
			),
			array(
				CL_SHOP_PRICE_LIST_CONDITION => array("row", "col", "type", "value", "bonus", "quantities"),
			)
		);

		$change = $delete = array();
		foreach($odl->arr() as $cond_id => $cond)
		{
			if(!empty($data[$cond["row"]][$cond["col"]]) && shop_price_list_obj::store_condition($data[$cond["row"]][$cond["col"]]))
			{
				if(
					$data[$cond["row"]][$cond["col"]]["type"] != $cond["type"] ||
					$data[$cond["row"]][$cond["col"]]["value"] != $cond["value"] ||
					$data[$cond["row"]][$cond["col"]]["quantities"] != $cond["quantities"] ||
					$data[$cond["row"]][$cond["col"]]["bonus"] != $cond["bonus"]
				)
				{
					// Conditions have changed!
					$change[$cond_id] = $data[$cond["row"]][$cond["col"]];
				}
				unset($data[$cond["row"]][$cond["col"]]);
			}
			else
			{
				// No such conditions any more!
				$delete[$cond_id] = $cond_id;
			}
		}

		if(count($change))
		{
			$ol = new object_list(array(
				"oid" => array_keys($change),
				"lang_id" => array(),
				"site_id" => array(),
			));
			foreach($ol->arr() as $oid => $o)
			{
				$o->set_prop("type", $change[$oid]["type"]);
				$o->set_prop("value", $change[$oid]["value"]);
				$o->set_prop("bonus", $change[$oid]["bonus"]);
				$o->set_prop("quantities", $change[$oid]["quantities"]);
				$o->save();
			}
		}

		foreach($data as $row => $data)
		{
			foreach($data as $col => $val)
			{
				if(shop_price_list_obj::store_condition($val))
				{
					$o = obj();
					$o->set_parent($arr["obj_inst"]->id());
					$o->set_class_id(CL_SHOP_PRICE_LIST_CONDITION);
					$o->set_prop("price_list", $arr["obj_inst"]->id());
					$o->set_prop("currency", automatweb::$request->arg("currency"));
					$o->set_prop("row", $row);
					$o->set_prop("col", $col);
					$o->set_prop("type", $val["type"]);
					$o->set_prop("value", $val["value"]);
					$o->set_prop("bonus", $val["bonus"]);
					$o->set_prop("quantities", $val["quantities"]);
					$o->save();
				}
			}
		}

		if(count($delete) > 0)
		{
			$ol = new object_list(array(
				"oid" => $delete,
				"lang_id" => array(),
				"site_id" => array(),
			));
			$ol->delete();
		}

		$arr["obj_inst"]->update_code();
	}

	public function _get_price_lists_tbl($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "ord",
			"caption" => t("Jrk"),
			"width" => 75,
			"align" => "center",
			"sorting_field" => "ord_num",
		));
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"align" => "center",
		));

		foreach(shop_price_list_obj::get_price_lists(array("valid" => automatweb::$request->arg("price_list_span") != "invalid"))->arr() as $oid => $o)
		{
			$t->define_data(array(
				"ord" => html::textbox(array(
					"name" => "price_lists_tbl[{$oid}][ord]",
					"value" => $o->ord(),
					"size" => 4,
				)),
				"ord_num" => $o->ord(),
				"name" => html::obj_change_url($o),
			));
		}

		$t->set_default_sortby("ord");
	}

	public function _set_price_lists_tbl($arr)
	{
		foreach(safe_array($arr["prop"]["value"]) as $oid => $data)
		{
			$o = obj($oid);
			$o->set_ord($data["ord"]);
			$o->save();
		}
	}

	public function _get_price_lists_tree($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		$t->add_item(0, array(
			"id" => "valid",
			"name" => t("Kehtivad"),
			"reload" => array(
				"layouts" => array("price_lists_split"),
				"params" => array("price_list_span" => "valid"),
			),
		));
		$t->add_item(0, array(
			"id" => "invalid",
			"name" => t("Aegunud"),
			"reload" => array(
				"layouts" => array("price_lists_split"),
				"params" => array("price_list_span" => "invalid"),
			),
		));
		$id = automatweb::$request->arg("price_list_span");
		$t->set_selected_item(!empty($id) ? $id : "valid");
	}

	public function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_shop_price_list(aw_oid int primary key)");
			return true;
		}
		$ret = false;
		switch($f)
		{
			case "aw_shop":
			case "valid_from":
			case "valid_to":
			case "base_price":
			case "aw_matrix_advanced":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				$ret = true;
				break;

			case "groups":
			case "orgs":
			case "persons":
			case "categories":
			case "org_cats":
			case "discount":
			case "short_name":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "varchar(255)"
				));
				$ret = true;
				break;

			case "aw_code":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "text"
				));
				$ret = true;
				break;
		}

		switch($f)
		{
			case "groups":
			case "orgs":
			case "persons":
			case "categories":
			case "org_cats":
				$this->db_query("ALTER TABLE aw_shop_price_list ADD INDEX(".$f.")");
		}
		return $ret;
	}
}

?>
