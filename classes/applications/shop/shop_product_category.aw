<?php
/*
@classinfo relationmgr=yes no_comment=1 no_status=1 prop_cb=1
@tableinfo aw_shop_product_category master_index=brother_of master_table=objects index=aw_oid

@default table=aw_shop_product_category

@default group=general
	@property desc type=textarea rows=10 cols=50 field=aw_desc
	@caption Kirjeldus

	@property code type=textbox
	@caption Kood

	@property images type=relpicker reltype=RELTYPE_IMAGE multiple=1 store=connect
	@caption Pildid

	@property unit_formula type=relpicker reltype=RELTYPE_UNIT_FORMULA store=connect multiple=1
	@caption &Uuml;hikute valemid

	@property types type=relpicker reltype=RELTYPE_CATEGORY_TYPES store=connect multiple=1
	@caption Seotud kategooriate t&uuml;&uuml;bid

	@property doc type=relpicker reltype=RELTYPE_DOC field=aw_doc
	@caption Dokument

	@property folders_tb type=toolbar store=no no_caption=1

	@property folders type=table store=no no_caption=1


@default group=transl
	@property transl type=callback callback=callback_get_transl store=no
	@caption T&otilde;lgi


@groupinfo transl caption=T&otilde;lgi


@reltype IMAGE value=1 clid=CL_IMAGE
@caption Pilt

@reltype DOC value=2 clid=CL_FILE
@caption Dokument

@reltype DISPLAY_FOLDER value=3 clid=CL_MENU
@caption Kuvamise kaust

@reltype UNIT_FORMULA value=4 clid=CL_SHOP_UNIT_FORMULA
@caption &Uuml;hikute valem

@reltype FOLDER value=5 clid=CL_MENU
@caption Kaust

@reltype CATEGORY value=6 clid=CL_SHOP_PRODUCT_CATEGORY
@caption Tootekategooria kuhu alla kategooria kuulub

@reltype CATEGORY_TYPES value=7 clid=CL_SHOP_PRODUCT_CATEGORY_TYPE
@caption Kategooria t&uuml;&uuml;p mille kategooriaid valida saab toodete otsingus

@reltype PURVEYOR value=8 clid=CL_CRM_COMPANY
@caption Tarnija

@reltype WAREHOUSE value=9 clid=CL_SHOP_WAREHOUSE
@caption Ladu

*/

class shop_product_category extends class_base
{
	function shop_product_category()
	{
		$this->init(array(
			"tpldir" => "applications/shop/shop_product_category",
			"clid" => CL_SHOP_PRODUCT_CATEGORY
		));

		$this->trans_props = array("name", "desc");
	}

	function callback_mod_reforb($arr)
	{
		$arr["add_folder"] = 0;
	}

	function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_shop_product_category(aw_oid int, aw_desc text, aw_doc int)");
			return true;
		}
		switch($f)
		{
			case "code":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "varchar(255)"
				));
				return true;
		}
	}

	function _get_folders_tb($arr)
	{
		$tb = $arr["prop"]["vcl_inst"];
		$tb->add_search_button(array(
			"pn" => "add_folder",
			"clid" => CL_MENU,
			"multiple" => 1,
		));
		$tb->add_save_button();
		$tb->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"tooltip" => t("Eemalda valitud seosed"),
			"confirm" => t("Oled kindel, et soovid valitud seosed kustutada?"),
			"action" => "del_folders",
		));
	}

	function _get_folders($arr)
	{
		if($arr["new"])
		{
			return PROP_IGNORE;
		}
		$t = $arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "display",
			"caption" => t("Kuvamise kaust"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "default",
			"caption" => t("Salvestamise kaust"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"align" => "center",
			"width" => "70%",
		));
		$t->define_chooser(array(
			"field" => "oid",
			"name" => "sel",
		));
		$conn = $arr["obj_inst"]->connections_from(array(
			"type" => "RELTYPE_FOLDER",
		));
		$def = $arr["obj_inst"]->meta("def_fld");
		foreach($conn as $c)
		{
			$f = $c->prop("to");
			$fo = $c->to();
			$t->define_data(array(
				"display" => html::checkbox(array(
					"name" => "display[".$f."]",
					"value" => 1,
					"checked" => $arr["obj_inst"]->is_connected_to(array(
						"type" => "RELTYPE_DISPLAY_FOLDER",
						"to" => $f,
					)),
				)),
				"default" => html::radiobutton(array(
					"name" => "def_fld",
					"value" => $f,
					"checked" => ($f == $def),
				)),
				"name" => $fo->name(),
				"oid" => $f,
			));
		}
	}

	/**
	@attrib name=del_folders
	**/
	function del_folders($arr)
	{
		$o = obj($arr["id"]);
		$types = array("RELTYPE_DISPLAY_FOLDER", "RELTYPE_FOLDER");
		if(is_array($arr["sel"]))
		{
			foreach($arr["sel"] as $oid)
			{
				foreach($types as $type)
				{
					if($o->is_connected_to(array("to" => $oid, "type" => $type)))
					{
						$o->disconnect(array(
							"from" => $oid,
							"type" => $type,
						));
					}
				}
			}
		}
		return $arr["post_ru"];
	}

	function callback_post_save($arr)
	{
		if(!empty($arr["request"]["add_folder"]))
		{
			$fs = $arr["request"]["add_folder"];
			$tmp = explode(",", $fs);
			foreach($tmp as $f)
			{
				$arr["obj_inst"]->connect(array(
					"type" => "RELTYPE_FOLDER",
					"to" => $f,
				));
			}
		}

		$conn = $arr["obj_inst"]->connections_from(array(
			"type" => "RELTYPE_FOLDER",
		));

		foreach($conn as $c)
		{
			$f = $c->prop("to");
			if($arr["request"]["display"][$f])
			{
				$arr["obj_inst"]->connect(array(
					"to" => $f,
					"type" => "RELTYPE_DISPLAY_FOLDER"
				));
			}
			elseif($arr["obj_inst"]->is_connected_to(array("to" => $f, "type" => "RELTYPE_DISPLAY_FOLDER")))
			{
				$arr["obj_inst"]->disconnect(array(
					"from" => $f,
				));
			}
		}

		if (!empty($arr["request"]["def_fld"]))
		{
			$arr["obj_inst"]->set_meta("def_fld", $arr["request"]["def_fld"]);
		}

		if($arr["new"])
		{
			$po = obj($arr["obj_inst"]->parent());

			if($po->class_id() == CL_SHOP_PRODUCT_CATEGORY)
			{
				if(empty($arr["request"]["def_fld"]) && acl_base::can("", $po->meta("def_fld")))
				{
					$arr["obj_inst"]->set_meta("def_fld", $po->meta("def_fld"));
					$arr["obj_inst"]->connect(array(
						"type" => "RELTYPE_FOLDER",
						"to" => $po->meta("def_fld"),
					));
				}

				foreach($po->connections_from(array("type" => "RELTYPE_FOLDER")) as $c)
				{
					$arr["obj_inst"]->connect(array(
						"to" => $c->prop("to"),
						"type" => "RELTYPE_FOLDER",
					));

					if($po->is_connected_to(array("type" => "RELTYPE_DISPLAY_FOLDER", "to" => $c->prop("to"))))
					{
						$arr["obj_inst"]->connect(array(
							"to" => $c->prop("to"),
							"type" => "RELTYPE_DISPLAY_FOLDER",
						));
					}
				}
				$arr["obj_inst"]->set_meta("units", $po->meta("units"));
			}
		}

		$arr["obj_inst"]->save();
	}
}
