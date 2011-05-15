<?php
/*
@classinfo relationmgr=yes no_comment=1 no_status=1 prop_cb=1
@tableinfo aw_shop_payment_type master_index=brother_of master_table=objects index=aw_oid

@default table=aw_shop_payment_type
@default group=general

	@property shop type=relpicker reltype=RELTYPE_SHOP field=aw_shop no_edit=1
	@caption E-pood

@groupinfo matrix caption="Maatriks"
	@groupinfo matrix_show caption="Maatriks" parent=matrix submit=no
	@default group=matrix_show

		@property matrix_tlb type=toolbar no_caption=1 store=no

		@property matrix type=table store=no no_caption=1

	@groupinfo matrix_settings caption="Maatriksi seaded" parent=matrix
	@default group=matrix_settings

			@property matrix_col_order type=table store=no
			@caption Veeru gruppide j&auml;rjekord

		@property matrix_cols type=text subtitle=1 store=no
		@caption Maatriksi veerud

			@property matrix_customer_categories type=relpicker reltype=RELTYPE_CUSTOMER_CATEGORY multiple=1 store=connect
			@caption Kliendikategooriad

			@property matrix_countries type=relpicker reltype=RELTYPE_COUNTRY multiple=1 store=connect
			@caption Riigid

		@property matrix_rows type=text subtitle=1 store=no
		@caption Maatriksi read

			@property matrix_product_categories type=relpicker reltype=RELTYPE_PRODUCT_CATEGORY multiple=1 store=connect
			@caption Tootekategooriad

		@property code type=hidden field=aw_code

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

@groupinfo advanced_layer caption="Advanced" submit=no
@default group=advanced_layer

	@layout advanced_layer type=vbox area_caption=J&auml;relmaksuseaded
		@layout advanced_layer_split type=hbox width=25%:75% parent=advanced_layer
			@layout advanced_layer_left type=vbox area_caption=J&auml;relmaksuseadete&nbsp;vahemikud parent=advanced_layer_split
				@property al_tree type=treeview parent=advanced_layer_left store=no no_caption=1
			@layout advanced_layer_right type=vbox area_caption=J&auml;relmaksuseaded parent=advanced_layer_split
				@layout advanced_layer_properties type=vbox parent=advanced_layer_right
					@property conditions type=callback callback=_get_conditions parent=advanced_layer_properties store=no
		@layout advanced_layer_buttons type=hbox width=33%:33%:34% parent=advanced_layer
			@property al_cancel type=button parent=advanced_layer_buttons store=no
			@caption Sulge
			@property al_delete type=button parent=advanced_layer_buttons store=no
			@caption Kustuta
			@property al_submit type=button parent=advanced_layer_buttons store=no
			@caption Salvesta

#### RELTYPES

@reltype CUSTOMER_CATEGORY value=1 clid=CL_CRM_CATEGORY
@caption Kliendikategooria, mida maatriksi veeruna kuvatakse

@reltype PRODUCT_CATEGORY value=2 clid=CL_SHOP_PRODUCT_CATEGORY
@caption Tootekategooria, mida maatriksi reana kuvatakse

@reltype PRIORITY value=3
@caption Prioriteet

@reltype COUNTRY value=4 clid=CL_COUNTRY
@caption Riik, mida maatriksi veeruna kuvatakse

@reltype SHOP value=5 clid=CL_SHOP_ORDER_CENTER
@caption E-pood

*/

class shop_payment_type extends shop_matrix
{
	function shop_payment_type()
	{
		$this->init(array(
			"tpldir" => "applications/shop/shop_payment_type",
			"clid" => CL_SHOP_PAYMENT_TYPE
		));
	}

	public function callback_pre_edit($arr)
	{
		if(!empty($_GET["debug"]))
		{
			$arr["obj_inst"]->update_code();
			$ot = new object_tree(array(
				"class_id" => array(CL_MENU, CL_DOCUMENT),
				"lang_id" => array(),
				"site_id" => array(),
				"parent" => 354721,
			));
			$ol = new object_list(array(
				"class_id" => CL_SHOP_PRODUCT_CATEGORY,
				"CL_SHOP_PRODUCT_CATEGORY.RELTYPE_CATEGORY(CL_PRODUCTS_SHOW).RELTYPE_ALIAS(CL_DOCUMENT)" => $ot->ids(),
				"lang_id" => array(),
				"site_id" => array(),
			));
			arr($ol->names());
			exit;
			arr(obj(379542)->valid_conditions(array("sum" => 1000, "currency" => 354831)));
			arr(obj(379540)->valid_conditions(array("sum" => 1000, "currency" => 354831)));
			exit;
			arr(obj(345974)->delivery_methods(array(
			)), true);
			arr(obj(obj(345920)->get_conditions(array(
				"sum" => 10000,
				"currency" => 346703,
				"product" => 346088,
			)))->properties(), true);
		}
		if(automatweb::$request->arg("group") == "advanced_layer")
		{
			if(automatweb::$request->arg_isset("condition") && ("new" === automatweb::$request->arg("condition") || $this->can("view", automatweb::$request->arg("condition"))))
			{
				$this->condition = (int)automatweb::$request->arg("condition");
			}
			else
			{
				$condition_ids = $arr["obj_inst"]->conditions(array(
					"row" => automatweb::$request->arg("row"),
					"col" => automatweb::$request->arg("col"),
					"currency" => automatweb::$request->arg("currency")
				))->ids();
				$this->condition = count($condition_ids) ? (int)reset($condition_ids) : "new";
			}
		}
		return parent::callback_pre_edit($arr);
	}

	public function callback_mod_tab($arr)
	{
		if("advanced_layer" == $arr["id"] and automatweb::$request->arg("group") !== "advanced_layer")
		{
			return false;
		}
	}

	public function callback_mod_layout(&$arr)
	{
		switch($arr["name"])
		{
			case "advanced_layer":
				$row_types = array(
					CL_SHOP_PRODUCT => t("toote"),
					CL_SHOP_PRODUCT_CATEGORY => t("tootekategooria"),
					CL_SHOP_PRODUCT_PACKAGING => t("tootepakendi"),
					"default" => t(""),
				);
				if(is_oid($col = automatweb::$request->arg("col")) && is_oid($col = automatweb::$request->arg("row")))
				{
					$row_obj = obj(automatweb::$request->arg("row"));
					$col_obj = obj($col);
					$col_types = array(
						CL_CRM_CATEGORY => t("kliendikategooria"),
						"default" => t("asukoha"),
					);

					$arr["area_caption"] = sprintf(t("J&auml;relmaksuseaded %s '%s' ja %s '%s' jaoks &nbsp;"),
						isset($row_types[$row_obj->class_id()]) ? $row_types[$row_obj->class_id()] : $row_types["default"],
						parse_obj_name($row_obj->name()),
						isset($col_types[$col_obj->class_id()]) ? $col_types[$col_obj->class_id()] : $col_types["default"],
						parse_obj_name($col_obj->name())
					);
				}
				elseif(is_oid($col = automatweb::$request->arg("row")))
				{
					$row_obj = obj(automatweb::$request->arg("row"));
					$arr["area_caption"] = sprintf(t("Vaikimisi j&auml;relmaksuseaded %s '%s' jaoks &nbsp;"),
						isset($row_types[$row_obj->class_id()]) ? $row_types[$row_obj->class_id()] : $row_types["default"],
						parse_obj_name($row_obj->name())
					);
				}
				else
				{
					$arr["area_caption"] = t("Vaikimisi j&auml;relmaksuseaded &nbsp;");
				}
				break;

			case "advanced_layer_right":
				if(isset($this->condition) && is_oid($this->condition) && $this->can("view", $this->condition))
				{
					$o = obj($this->condition);
					$arr["area_caption"] = sprintf(t("J&auml;relmaksuseaded vahemikus %s %s - %s %s"),
						$o->prop("currency.name"),
						$o->prop("min_amt"),
						$o->prop("currency.name"),
						$o->prop("max_amt")
					);
				}
				else
				{
					$arr["area_caption"] = t("Uus j&auml;relmaksuseadete vahemik");
				}
				break;
		}
		return true;
	}

	public function _get_shop($arr)
	{
		if(!empty($arr["new"]) && $this->can("view", $parent = automatweb::$request->arg("parent")) && obj($parent)->is_a(CL_SHOP_ORDER_CENTER))
		{
			$arr["prop"]["options"] = array($parent => obj($parent)->name());
		}
	}

	public function _get_matrix($arr)
	{
		load_javascript("applications/shop/matrix.js");
		load_javascript("reload_properties_layouts.js");

		$matrix = array();
		$odl = new object_data_list(
			array(
				"class_id" => CL_SHOP_PAYMENT_TYPE_CONDITIONS,
				"payment_type" => $arr["obj_inst"]->id(),
				"currency" => $this->currency,
				"lang_id" => array(),
				"site_id" => array(),
			),
			array(
				CL_SHOP_PAYMENT_TYPE_CONDITIONS => array("row", "col"),
			)
		);
		foreach($odl->arr() as $oid => $data)
		{
			$matrix[is_oid($data["row"]) ? $data["row"] : "default"][is_oid($data["col"]) ? $data["col"] : "default"] = $oid;
		}

		$this->draw_matrix(array(
			"table_inst" => &$arr["prop"]["vcl_inst"],
			"obj_inst" => &$arr["obj_inst"],
			"column_types" => $this->col_types,
			"matrix_data" => $matrix,
			"data_cell_callback" => array(&$this, "draw_matrix_cell"),
		));
		$arr["prop"]["vcl_inst"]->set_caption(sprintf(t("J&auml;relmaksukonfiguratsioon valuutakursile '%s'"), obj($this->currency)->name()));
	}

	public function draw_matrix_cell($oid, $field, $matrix)
	{
		$name = explode("_", $field["name"]);
		$col = array_pop($name);
		if($col === "self")
		{
			$col = array_pop($name);
		}

		return html::href(array(
			"name" => "{$oid}_{$col}",
			"caption" => $this->cell_description(array(
				"payment_type" => $this->obj->id(),
				"row" => $oid,
				"col" => $col,
				"currency" => $this->currency,
			), true),
			"url" => "javascript:shop_matrix.open_layer('$oid', '$col');",
		));
	}

	public function _get_conditions($arr)
	{
		$ret = array();

		if(is_oid($this->condition) && $this->can("view", $this->condition))
		{
			$o = obj($this->condition);
		}
		else
		{
			$o = obj(NULL, array(), CL_SHOP_PAYMENT_TYPE_CONDITIONS);
		}

		foreach($o->get_property_list() as $k => $v)
		{
			if(!in_array($k, array("name", "comment", "status", "payment_type", "col", "row", "ignore_min_amt", "ignore_max_amt", "ignore_min_payment")))
			{
				$v["captionside"] = "top";
				$v["group"] = "advanced_layer";
				$v["parent"] = "advanced_layer_properties";
				$v["value"] = $o->prop($k);

				if(in_array($k, array("min_amt", "max_amt", "min_payment")))
				{
					$v["post_append_text"] = " ".html::checkbox(array(
						"name" => "ignore_".$k,
						"value" => 1,
						"checked" => $o->prop("ignore_".$k),
					))." ".t("Ei arvestata, ainult informatiivne");
				}

				$ret[$k] = $v;
			}
		}
		$ret["period_step"]["post_append_text"] = html::hidden(array(
			"name" => "condition",
			"value" => $this->condition
		));

		return $ret;
	}

	public function _get_al_cancel($arr)
	{
		$arr["prop"]["onclick"] = "shop_matrix.close_layer();";
	}

	public function _get_al_delete($arr)
	{
		$arr["prop"]["onclick"] = "$('input[type=hidden][name=delete_conditions]').val('1'); shop_matrix.submit_layer();";
	}

	public function _get_al_submit($arr)
	{
		$arr["prop"]["onclick"] = "shop_matrix.submit_layer();";
		$arr["prop"]["post_append_text"] = html::hidden(array(
			"name" => "row",
			"value" => automatweb::$request->arg("row"),
		)).html::hidden(array(
			"name" => "col",
			"value" => automatweb::$request->arg("col"),
		)).html::hidden(array(
			"name" => "payment_type",
			"value" => $arr["obj_inst"]->id(),
		)).html::hidden(array(
			"name" => "delete_conditions",
			"value" => 0,
		));
		/*
		$arr["prop"]["reload"] = array(
			"submit" => array(
//				"url" => "",
				"forms" => array("shop_matrix"),
//				"props" => array(),
			),
			"layouts" => array("advanced_layer_right", "advanced_layer_left"),
		);
		*/
	}

	public function _get_al_tree($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		foreach($arr["obj_inst"]->conditions(array(
			"row" => automatweb::$request->arg("row"),
			"col" => automatweb::$request->arg("col"),
			"currency" => automatweb::$request->arg("currency"),
		))->arr() as $o)
		{
			$t->add_item(0, array(
				"id" => (int)$o->id(),
				"name" => sprintf(t("%s %s - %s %s"),
					$o->prop("currency.name"),
					$o->prop("min_amt"),
					$o->prop("currency.name"),
					$o->prop("max_amt")
				),
				"reload" => array(
					"params" => array(
						"condition" => $o->id(),
					),
					"layouts" => array("advanced_layer_right"),
				),
			));
		}
		$t->add_item(0, array(
			"id" => "new",
			"name" => sprintf("Lisa uus vahemik"),
			"reload" => array(
				"params" => array(
					"condition" => "new",
				),
				"layouts" => array("advanced_layer_right"),
			),
		));
		$t->set_selected_item($this->condition);
	}

	function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_shop_payment_type(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "aw_shop":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				return true;

			case "aw_code":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "text"
				));
				return true;
		}
	}

	/**
		@attrib name=submit_advanced_layer all_args=1 params=name
		@param id required type=int/string
		@param payment_type required type=int
		@param row optional type=int/string
		@param col optional type=int/string
		@param currency required type=int acl=view
	**/
	public function submit_advanced_layer($arr)
	{
		if(!empty($arr["delete_conditions"]))
		{
			if(is_oid($arr["id"]) && $this->can("view", $arr["id"]))
			{
				obj($arr["id"])->delete();
			}
			$o = obj(NULL, array(), CL_SHOP_PAYMENT_TYPE_CONDITIONS);
			$id = NULL;
		}
		else
		{
			if(is_oid($arr["id"]) && $this->can("view", $arr["id"]))
			{
				$o = obj($arr["id"]);
			}
			else
			{
				$o = obj(NULL, array(), CL_SHOP_PAYMENT_TYPE_CONDITIONS);
				$o->set_name(sprintf(t("Makseviisi '%s' tingimused"), parse_obj_name(obj($arr["payment_type"])->name())));
				$o->set_prop("payment_type", $arr["payment_type"]);
				$o->set_prop("row", is_oid($arr["row"]) ? $arr["row"] : 0);
				$o->set_prop("col", is_oid($arr["col"]) ? $arr["col"] : 0);

				$o->set_parent($arr["payment_type"]);
			}
			foreach(array_merge(array("ignore_min_amt" => 0, "ignore_max_amt" => 0, "ignore_min_payment" => 0), $arr) as $k => $v)
			{
				switch ($k)
				{
					case "min_amt":
					case "max_amt":
					case "min_payment":
					case "ignore_min_amt":
					case "ignore_max_amt":
					case "ignore_min_payment":
					case "prepayment_interest":
					case "yearly_interest":
					case "period_min":
					case "period_max":
					case "period_step":
					case "currency":
						$o->set_prop($k, $v);
						break;
				}
			}
			$id = $o->save();
		}

		obj($arr["payment_type"])->update_code();

		die((string)$id);
//		die(iconv(aw_global_get("charset"), "UTF-8", $o->description()));
	}

	/**
		@attrib name=cell_description all_args=1 params=name
		@param payment_type required type=int acl=view
		@param row required type=int/string acl=view
		@param col required type=int/string acl=view
		@param currency required type=int acl=view
	**/
	public function cell_description($arr, $oh_please_dont_die = false)
	{
		$str = "";
		foreach(obj($arr["payment_type"])->conditions(array(
			"row" => isset($arr["row"]) ? $arr["row"] : NULL,
			"col" => isset($arr["col"]) ? $arr["col"] : NULL,
			"currency" => isset($arr["currency"]) ? $arr["currency"] : NULL,
		))->arr() as $o)
		{
			$str .= "<p>".$o->description()."</p>";
		}

		if(strlen($str) === 0)
		{
			$str = t("M&auml;&auml;ramata");
		}

		if($oh_please_dont_die)
		{
			return $str;
		}
		else
		{
			die(iconv(aw_global_get("charset"), "UTF-8", $str));
		}
	}
}
