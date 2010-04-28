<?php

namespace automatweb;

// shop_warehouse_favor.aw - Soodustus
/*

@classinfo syslog_type=ST_SHOP_WAREHOUSE_FAVOR relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo

@tableinfo shop_warehouse_favor index=oid master_table=objects master_index=oid

@default table=objects
@default group=general

	@property start type=date_select table=shop_warehouse_favor
	@caption Kehtivuse algus

	@property end type=date_select table=shop_warehouse_favor
	@caption Kehtivuse l&otilde;pp

@groupinfo products caption="Tooted"
@default group=products

	@groupinfo product_groups caption="Tootegrupid" parent=products
	@default group=product_groups

		@layout product_groups_frame type=hbox width=20%:80%

			@layout product_group_search_params_frame type=vbox parent=product_groups_frame

				@property product_group_name type=textbox store=no parent=product_group_search_params_frame
				@caption Tootegrupi nimi

			@layout product_group_search_results_frame type=vbox parent=product_groups_frame

				@property product_group_result type=table parent=product_group_search_results_frame
				@caption Tootegrupid

	@groupinfo product_items caption="Tooted" parent=products
	@default group=product_items

		@layout products_frame type=hbox width=20%:80%

			@layout product_search_params_frame type=vbox parent=products_frame

				@property product_name type=textbox store=no parent=product_search_params_frame
				@caption Toote nimetus

				@property product_code type=textbox store=no parent=product_search_params_frame
				@caption Tootekood

				@property product_barcode type=textbox store=no parent=product_search_params_frame
				@caption Ribakood

			@layout product_search_results_frame type=vbox parent=products_frame

				@property product_result type=table parent=product_search_results_frame
				@caption Tooted

@groupinfo clients caption="Kliendid"
@default group=clients

	@groupinfo client_groups caption="Kliendigrupid" parent=clients
	@default group=client_groups

		@layout client_groups_frame type=hbox width=20%:80%

			@layout client_group_search_params_frame type=vbox parent=client_groups_frame

				@property client_group_name type=textbox store=no parent=client_group_search_params_frame
				@caption Kliendigrupi nimi

			@layout client_group_search_results_frame type=vbox parent=client_groups_frame

				@property client_group_result type=table parent=client_group_search_results_frame
				@caption Kliendi grupid

	@groupinfo client_items caption="Kliendid" parent=clients
	@default group=client_items

		@layout clients_frame type=hbox width=20%:80%

			@layout client_search_params_frame type=vbox parent=clients_frame

				@property client_name type=textbox store=no parent=client_search_params_frame
				@caption Kliendi nimi

				@property client_group type=textbox store=no parent=client_search_params_frame
				@caption Kliendi grupp


			@layout client_search_results_frame type=vbox parent=clients_frame

				@property client_result type=table parent=client_search_results_frame
				@caption Kliendid
*/

class shop_warehouse_favor extends class_base
{
	const AW_CLID = 1042;

	function shop_warehouse_favor()
	{
		// change this to the folder under the templates folder, where this classes templates will be,
		// if they exist at all. Or delete it, if this class does not use templates
		$this->init(array(
			"tpldir" => "applications/shop/shop_warehouse_favor",
			"clid" => CL_SHOP_WAREHOUSE_FAVOR
		));
	}

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	////////////////////////////////////
	// the next functions are optional - delete them if not needed
	////////////////////////////////////

	////
	// !this will be called if the object is put in a document by an alias and the document is being shown
	// parameters
	//    alias - array of alias data, the important bit is $alias[target] which is the id of the object to show
	function parse_alias($arr = array())
	{
		return $this->show(array("id" => $arr["alias"]["target"]));
	}

	////
	// !this shows the object. not strictly necessary, but you'll probably need it, it is used by parse_alias
	function show($arr)
	{
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");
		$this->vars(array(
			"name" => $ob->prop("name"),
		));
		return $this->parse();
	}

	function _get_product_group_result($arr)
	{
		$t = $arr['prop']['vcl_inst'];
		$t->set_sortable(false);

		$t->define_field(array(
			"name" => "product_group",
			"caption" => t("Tootegrupp")
		));
		$t->define_field(array(
			"name" => "discount_percent",
			"caption" => t("Soodustus (%)"),
		));
		$t->define_field(array(
			"name" => "discount_sum",
			"caption" => t("Soodustus (summa)"),
		));
	}

	function _get_product_result($arr)
	{
		$t = &$arr['prop']['vcl_inst'];
		$t->set_sortable(false);

		$t->define_field(array(
			"name" => "product",
			"caption" => t("Toode")
		));
		$t->define_field(array(
			"name" => "discount_percent",
			"caption" => t("Soodustus (%)"),
		));
		$t->define_field(array(
			"name" => "discount_sum",
			"caption" => t("Soodustus (summa)"),
		));
	}

	function _get_client_group_result($arr)
	{
		$t = &$arr['prop']['vcl_inst'];
		$t->set_sortable(false);

		$t->define_field(array(
			"name" => "client_group",
			"caption" => t("Kliendigrupp"),
		));
		$t->define_field(array(
			"name" => "settings",
			"caption" => t("Seaded"),
		));
		$t->define_field(array(
			"name" => "select",
			"caption" => t("Vali"),
		));
	}

	function _get_client_result($arr)
	{
		$t = &$arr['prop']['vcl_inst'];
		$t->set_sortable(false);

		$t->define_field(array(
			"name" => "client_name",
			"caption" => t("Kliendi nimi"),
		));
		$t->define_field(array(
			"name" => "client_group",
			"caption" => t("Kliendigrupp"),
		));
		$t->define_field(array(
			"name" => "settings",
			"caption" => t("Seaded"),
		));
		$t->define_field(array(
			"name" => "select",
			"caption" => t("Vali"),
		));
	}

	/**
		DB UPGRADE
	**/
	function do_db_upgrade($table, $field, $query, $error)
	{
		// this should be the way to detect, if table exist:
		if (empty($field))
		{
			$this->db_query('CREATE TABLE '.$table.' (oid INT PRIMARY KEY NOT NULL)');
			return true;
		}

		switch ($field)
		{
			case 'start':
			case 'end':
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
