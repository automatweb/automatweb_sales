<?php
/*
@classinfo syslog_type=ST_SHOP_WAREHOUSE_CONFIG relationmgr=yes maintainer=kristo
@tableinfo aw_shop_warehouse_config index=aw_oid master_table=objects master_index=brother_of

@default table=objects
@default group=general

	@layout split type=hbox 

		@layout left type=vbox area_caption=Seaded closeable=1 parent=split

			@property name type=textbox parent=left
			@caption Nimi
			@comment Objekti nimi

			@property comment type=textbox parent=left
			@caption Kommentaar
			@comment Vabas vormis tekst objekti kohta

			@property status type=status trans=1 default=1 parent=left
			@caption Aktiivne
			@comment Kas objekt on aktiivne

@default field=meta
@default method=serialize

			@property search_form type=relpicker reltype=RELTYPE_SEARCH_FORM parent=left
			@caption Lao otsinguvorm

			@property has_alternative_units type=chooser parent=left
			@caption Alternatiiv&uuml;hikud

			@property alternative_unit_levels type=textbox size=5 parent=left
			@caption Alternatiiv&uuml;hikute tasemeid

			@property def_price_list type=relpicker reltype=RELTYPE_DEF_PRICELIST parent=left automatic=1
			@caption Vaikimisi hinnakiri

			@property def_currency type=relpicker reltype=RELTYPE_DEF_CURRENCY parent=left automatic=1
			@caption Vaikimisi valuuta

			@property manager_cos type=relpicker reltype=RELTYPE_MANAGER_CO multiple=1 parent=left
			@caption Haldurfirmad

			@property owner type=relpicker reltype=RELTYPE_MANAGER_CO parent=left table=aw_shop_warehouse_config field=aw_owner method=fuck_you_serialize
			@caption Lao omanik

			@property sell_prods type=checkbox ch_value=1 parent=left
			@caption Ladu m&uuml;&uuml;b tooteid, mitte pakendeid

			@property no_packets type=checkbox ch_value=1 parent=left
			@caption Ladu ei m&uuml;&uuml; pakette

			@property no_count type=checkbox ch_value=1 parent=left
			@caption Toodetel puudub laoseis

			@property no_prod_tree type=checkbox ch_value=1 parent=left
			@caption Toodete kaustade puud ei kuvata

			@property no_prodg_tree type=checkbox ch_value=1 parent=left
			@caption Tootekategooriate puud ei kuvata
			
			@property arrival_company_folder type=relpicker reltype=RELTYPE_ARRIVAL_COMPANY_FOLDER multiple=1 size=3 field=meta method=serialize parent=left
			@caption Tarnefirmade kaust

			@property show_purveyance type=checkbox parent=left
			@caption Kuva tarnijaid

		@layout right type=vbox area_caption=Kaustad closeable=1 parent=split

			@property prod_fld type=relpicker reltype=RELTYPE_FOLDER parent=right
			@caption Toodete kataloog

			@property pkt_fld type=relpicker reltype=RELTYPE_FOLDER parent=right
			@caption Pakettide kataloog

			@property prod_cat_fld type=relpicker reltype=RELTYPE_FOLDER parent=right
			@caption Artiklikategooriate kataloog

			@property reception_fld type=relpicker reltype=RELTYPE_FOLDER parent=right
			@caption Lao sissetulekute kataloog

			@property export_fld type=relpicker reltype=RELTYPE_FOLDER parent=right
			@caption Lao v&auml;jaminekute kataloog

			@property prod_type_fld type=relpicker reltype=RELTYPE_FOLDER parent=right
			@caption Lao toodete t&uuml;&uuml;bid

			@property prod_type_cfgform type=relpicker reltype=RELTYPE_CFGFORM parent=right
			@caption Lao tootet&uuml;&uuml;pide lisamise vormi seadete vorm

			@property prod_conf_folder type=relpicker reltype=RELTYPE_FOLDER parent=right
			@caption Toodete lisaobjektide seadetevormide kataloog

			@property order_fld type=relpicker reltype=RELTYPE_FOLDER parent=right
			@caption Lao tellimuste kataloog

			@property buyers_fld type=relpicker reltype=RELTYPE_FOLDER parent=right
			@caption Lao tellijate kataloog

			@property purchase_order_mail type=chooser parent=right
			@caption Ostutellimuse meil

			@property purchase_order_mail_ctrl type=relpicker reltype=RELTYPE_PO_MAIL_CONTROLLER parent=right
			@caption Ostutellimuse meili kontroller

			@property short_code_ctrl type=relpicker reltype=RELTYPE_CODE_CONTROLLER parent=right
			@caption Toote l&uuml;hikoodi kontroller

			@property prod_tree_clids type=select multiple=1 size=4 parent=right
			@caption Klassid, mida toodete puus kuvatakse

@default group=units

	@property units_table type=table store=no no_caption=1


@groupinfo units caption="&Uuml;hikud"

	@layout units_split type=hbox width=20%:80%

		@layout units_tree_box type=vbox area_caption=Artiklikategooriad closeable=1 parent=units_split

			@property prodg_tree type=treeview store=no no_caption=1 parent=units_tree_box

		@layout units_tbl_box type=vbox parent=units_split area_caption=&Uuml;hikud closeable=1

			@property units_tbl type=table store=no no_caption=1 parent=units_tbl_box

@reltype FOLDER value=1 clid=CL_MENU
@caption kataloog

@reltype SEARCH_FORM value=2 clid=CL_CB_SEARCH
@caption otsinguvorm

@reltype MANAGER_CO value=3 clid=CL_CRM_COMPANY
@caption haldaja firma

@reltype CFGFORM value=4 clid=CL_CFGFORM
@caption Seadete vorm

@reltype DEF_PRICELIST value=5 clid=CL_SHOP_PRICE_LIST
@caption Hinnakiri

@reltype DEF_CURRENCY value=6 clid=CL_CURRENCY
@caption Valuuta

@reltype PO_MAIL_CONTROLLER value=7 clid=CL_CFG_VIEW_CONTROLLER
@caption Ostutellimuse meili kontroller

@reltype CODE_CONTROLLER value=8 clid=CL_CFGCONTROLLER
@caption Toote l&uuml;hikoodi kontroller

@reltype ARRIVAL_COMPANY_FOLDER value=9 clid=CL_MENU
@caption Organisatsioonide kaust
*/

define("SEND_AW_MAIL", 1);
define("SEND_CTRL_MAIL", 2);

class shop_warehouse_config extends class_base
{
	function shop_warehouse_config()
	{
		$this->init(array(
			"tpldir" => "applications/shop/shop_warehouse_config",
			"clid" => CL_SHOP_WAREHOUSE_CONFIG
		));
	}

	function get_property($arr)
	{
		$data = &$arr["prop"];
		$retval = PROP_OK;
		switch($data["name"])
		{
			case "prod_tree_clids":
				$data["options"] = get_class_picker(array("field" => "name"));
				break;

			case "prodg_tree":
				$whi = get_instance(CL_SHOP_WAREHOUSE);
				$whi->prod_type_fld = $arr["obj_inst"]->prop("prod_type_fld");
				return $whi->mk_prodg_tree(&$arr);

			case "units_tbl":
				$this->_units_tbl(&$arr);
				break;

			case "has_alternative_units":
				$data["options"] = array(
					0 => t("Ei"),
					1 => t("Jah"),
				);
				if(empty($data["value"]))
				{
					$data["value"] = 0;
				}
				break;
			case "purchase_order_mail":
				$data["options"] = array(
					SEND_AW_MAIL => t("AW meil"),
					SEND_CTRL_MAIL => t("Kontrolleriga defineeritud"),
				);
				break;
		};
		return $retval;
	}

	function set_property($arr = array())
	{
		$data = &$arr["prop"];
		$retval = PROP_OK;
		switch($data["name"])
		{
			case "units_tbl":
				$this->_save_units(&$arr);
				break;
		}
		return $retval;
	}	

	function _units_tbl($arr)
	{
		if(!$this->can("view", $arr["request"]["pgtf"]))
		{
			return PROP_IGNORE;
		}
		$t = &$arr["prop"]["vcl_inst"];
		$units = $arr["obj_inst"]->prop("alternative_unit_levels");
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"align" => "center",
		));
		for($i=0;$i<=$units;$i++)
		{
			$t->define_field(array(
				"name" => "unit_".$i,
				"caption" => $i? sprintf(t("Alternatiiv&uuml;hik %s"), $i) : t("P&otilde;hi&uuml;hik"),
				"align" => "center",
			));
		}
		$ui = get_instance(CL_UNIT);
		$unitnames = $ui->get_unit_list(true);
		$catol = new object_list(array(
			"class_id" => CL_SHOP_PRODUCT_CATEGORY,
			"parent" => $arr["request"]["pgtf"],
			"site_id" => array(),
			"lang_id" => array(),
		));
		foreach($catol->arr() as $cat)
		{
			$unitdata = $cat->meta("units");
			if(!$unitdata)
			{
				$unitdata = array();
			}
			for($i=0;$i<=$units;$i++)
			{
				$data["unit_".$i] = html::select(array(
					"name" => "units[".$cat->id()."][".$i."]",
					"options" => $unitnames,
					"value" => $unitdata[$i],
				));
			}
			$data["name"] = $cat->name();
			$t->define_data($data);
		}
	}

	function _save_units($arr)
	{
		if($units = $arr["request"]["units"])
		{
			foreach($units as $cat=>$data)
			{
				$cato = obj($cat);
				$cato->set_meta("units", $data);
				$cato->save();
			}
		}
	}

	function callback_mod_tab($arr)
	{
		if($arr["id"] == "units" && !$arr["obj_inst"]->prop("has_alternative_units"))
		{
			return false;
		}
		return true;
	}

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = get_ru();
		$arr["pgtf"] = automatweb::$request->arg("pgtf");
	}

	function callback_mod_retval($arr)
	{
		$arr["args"]["pgtf"] = $arr["request"]["pgtf"];
	}

	function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_shop_warehouse_config(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "aw_owner":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				$ol = new object_list(array(
					"class_id" => CL_SHOP_WAREHOUSE_CONFIG,
					"lang_id" => array(),
					"site_id" => array(),
				));
				foreach($ol->arr() as $o)
				{
					$this->db_query(sprintf("INSERT INTO aw_shop_warehouse_config (aw_oid, aw_owner) VALUES ('%u', '%u')", $o->id(), $o->meta("owner")));
				}
				return true;
		}
	}
}
?>
