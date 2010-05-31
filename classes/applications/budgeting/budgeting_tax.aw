<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/budgeting/budgeting_tax.aw,v 1.10 2008/05/14 14:05:05 markop Exp $
// budgeting_tax.aw - Eelarvestamise maks 
/*

@classinfo syslog_type=ST_BUDGETING_TAX relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo

@tableinfo aw_budgeting_tax master_table=objects master_index=brother_of index=aw_oid

@default table=aw_budgeting_tax
@default group=general

	@property from_place type=textbox field=aw_from_place
	@caption Kust

	@property to_acct type=relpicker field=aw_to_acct reltype=RELTYPE_TO_ACCT
	@caption Kontole

	@property amount type=textbox size=5 field=aw_amt
	@caption Summa (Kui l&otilde;peb % m&auml;rgiga, siis protsentides)

	@property max_deviation_minus type=textbox size=5 field=aw_max_deviation_minus
	@caption Maksimaalne projektip&otilde;hine muudatus -

	@property max_deviation_plus type=textbox size=5 field=aw_max_deviation_plus
	@caption Maksimaalne projektip&otilde;hine muudatus +

	@property pri type=textbox size=5 field=aw_pri
	@caption Prioriteet

	@property when_type type=chooser field=aw_when_type
	@caption Aja t&uuml;&uuml;p

	@property when_date type=datetime_select default=-1 field=aw_when_date
	@caption Millal

	@property penalty_pct type=textbox size=5 field=aw_penalty_pct
	@caption Viivis (%)

	@property tax_grp type=relpicker  field=aw_tax_grp automatic=1 reltype=RELTYPE_TAX_GRP
	@caption Maksugrupp

	@property tax_scenario type=relpicker  field=aw_tax_scenario automatic=1 reltype=RELTYPE_SCENARIO
	@caption Stsenaarium

@groupinfo locs caption="Kust" submit=no
@default group=locs

	@property from_place_toolbar type=toolbar store=no no_caption=1
	@caption Kust toolbar
	
	@property from_place_table type=table store=no no_caption=1
	@caption Kust
	
@groupinfo flow caption="Rahavood" submit=no
@default group=flow
	@property flow_tb type=toolbar no_caption=1 store=no

	@layout flow_split type=hbox width="20%:80%" 

		@layout flow_left type=vbox parent=flow_split 
			@layout flow_group type=vbox parent=flow_left closeable=1 area_caption=Otsing

				@property start type=date_select store=no parent=flow_group captionside=top
				@caption Alates

				@property to type=date_select store=no parent=flow_group captionside=top
				@caption Kuni

				@property flow_s_sbt type=submit store=no parent=flow_group size=15 captionside=top no_caption=1
				@caption Otsi


		@layout flow_table parent=flow_split type=vbox

			@property flow_table type=table parent=flow_table store=no no_caption=1


@reltype FROM_ACCT value=1 clid=CL_BUDGETING_ACCOUNT
@caption Kontolt

@reltype TO_ACCT value=2 clid=CL_BUDGETING_ACCOUNT,CL_CRM_PERSON,CL_CRM_COMPANY,CL_CRM_SECTOR,CL_PROJECT,CL_BUDGETING_FUND
@caption Kontole

@reltype TAX_GRP value=3 clid=CL_BUDGETING_TAX_GROUP
@caption Maksugrupp

@reltype SCENARIO value=4 clid=CL_BUDGETING_SCENARIO
@caption Stsenaarium
*/

class budgeting_tax extends class_base
{
	const AW_CLID = 1206;

	function budgeting_tax()
	{
		$this->init(array(
			"tpldir" => "applications/budgeting/budgeting_tax",
			"clid" => CL_BUDGETING_TAX
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "from_place":
				if ($arr["request"]["place"])
				{
					$prop["value"] = $arr["request"]["place"];
				}
				else
				{
					$prop["type"] = "text";
					$m = get_instance("applications/budgeting/budgeting_model");
					$prop["value"] = /*$prop["value"]." ".*/$m->get_cat_id_description($prop["value"]);
					$prop["value"] .= " ".html::popup(array(
						"url" => $this->mk_my_orb("select_tax_cat", array("id" => $arr["obj_inst"]->id()), "budgeting_workspace"),
						"caption" => html::img(array(
							"url" => aw_ini_get("baseurl")."/automatweb/images/icons/edit.gif",
							"border" => 0
						)),
						"scrollbars" => "auto"
					));
				}
				break;
			case "start":
				if(!$arr["request"][$prop["name"]])
				{
					$prop["value"] = time() - 365*24*3600;
				}
			case "to":
				if(!$prop["value"])
				{
					$prop["value"] = $arr["request"][$prop["name"]];
				}
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
			case "from_place":
				if(strlen($prop["value"]) < 2)
				{
					if (strlen($arr["request"]["set_tax_fld"]) < 2)
					{
						return PROP_IGNORE;
					}
					$prop["value"] = $arr["request"]["set_tax_fld"];
				}
				break;
		}
		return $retval;
	}	

	function callback_mod_reforb(&$arr)
	{
		$arr["post_ru"] = post_ru();
		$arr["set_tax_fld"] = "0";
		foreach($this->_add_vars as $var)
		{
			$arr[$var] = "0";
		}
	}

	function do_db_upgrade($t,$f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_budgeting_tax (aw_oid int primary key,aw_from_acct int, aw_to_acct int,aw_amt double,aw_when_type int,aw_when_date int, aw_penalty_pct double)");
			return true;
		}

		switch($f)
		{
			case "aw_from_place":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "varchar(255)"
				));
				return true;

			case "aw_pri":
			case "aw_tax_grp":
			case "aw_tax_scenario":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				return true;

			case "aw_max_deviation":
			case "aw_max_deviation_minus":
			case "aw_max_deviation_plus":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "double"
				));
				return true;
		}
	}

	function _get_when_type($arr)
	{
		$arr["prop"]["options"] = $this->get_when_types();
	}

	function get_when_types()
	{
		return array(
			1 => t("&Uuml;hekordne"),
			2 => t("Korduv")
		);
	}

	function callback_post_save($arr)
	{
		$ol = new object_list(array(
			"class_id" => CL_BUDGETING_TAX_FOLDER_RELATION,
			"tax" => $arr["obj_inst"]->id(),
			"site_id" => array(),
			"lang_id" => array(),
		));
		if (!$ol->count())
		{
			$o = obj();
			$o->set_parent($arr["obj_inst"]->id());
			$o->set_class_id(CL_BUDGETING_TAX_FOLDER_RELATION);
			$o->set_name(sprintf(t("Seos maksu %s ja kausta %s vahel"), $arr["obj_inst"]->name(), $arr["obj_inst"]->prop("from_place")));
			$o->set_prop("tax", $arr["obj_inst"]->id());
			$o->set_prop("folder", $arr["obj_inst"]->prop("from_place"));
			$o->save();
		}
		else
		{
			$o = $ol->begin();
			$o->set_prop("folder", $arr["obj_inst"]->prop("from_place"));
			$o->save();
		}

		foreach($arr["request"] as $k => $v)
		{
			if (substr($k, 0, 7) == "taxfld_" && strlen($v) > 1)
			{
				list(, $_id) = explode("_", $k);
				$o = obj($_id);
				$o->set_prop("folder", $v);
				$o->save();
			}
		}
	}

	function callback_mod_retval($arr)
	{
		$arr["args"]["to"] = $arr["request"]["to"];
		$arr["args"]["start"] = $arr["request"]["start"];
	}

	function _init_from_place_table(&$t)
	{
		$t->define_field(array(
			"name" => "hfrom",
			"caption" => t("Kust"),
			"align" => "left",
			"sortable" => 1
		));

		$t->define_field(array(
			"name" => "amount_final",
			"caption" => t("T&auml;isarvuline summa"),
		));
		$t->define_field(array(
			"name" => "amount",
			"caption" => t("Summa protsentides"),
		));
		$t->define_field(array(
			"name" => "minus",
			"caption" => t("Miinus tolerants"),
		));
		$t->define_field(array(
			"name" => "plus",
			"caption" => t("Pluss tolerants"),
		));
		$t->define_field(array(
			"name" => "term",
			"caption" => t("Tingimus"),
		));
		$t->define_field(array(
			"name" => "use_different_settings",
			"caption" => t("Kasuta eraldi seadeid"),
		));

		$t->define_field(array(
			"name" => "use_used_settings",
			"caption" => t("Kasuta olemasolevaid seadeid"),
		));

		$t->define_field(array(
			"name" => "hedit",
			"caption" => t("Muuda"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid"
		));
	}

	function _set_from_place_table($arr)
	{
		foreach($arr["request"]["d"] as $id => $data)
		{
			$o = obj($id);
			if($data["use_different_settings"])
			{
				foreach($data as $prop => $val)
				{
					if($o->is_property($prop))
					{
						$o->set_prop($prop , $val);
					}
				}
				$o->set_prop("use_used_settings" , 0);
			}
			else
			{
				$o -> set_prop("use_different_settings"  , 0);
				if($data["use_used_settings"])
				{
					$o->set_prop("use_used_settings" , $data["use_used_settings"]);
				}
			}
			$o -> save();
		}
	}

	function _get_from_place_table($arr)
	{
		$ol = new object_list(array(
			"class_id" => CL_BUDGETING_TAX_FOLDER_RELATION,
			"lang_id" => array(),
			"site_id" => array(),
			"tax" => $arr["obj_inst"]->id()
		));
		$t = new vcl_table();
		$this->_init_from_place_table($t);

		$m = get_instance("applications/budgeting/budgeting_model");

		$props_selection = array("" => "");
		foreach($ol-> arr() as $o)
		{
			$props_selection[$o->id()] = $m->get_cat_id_description($o->prop("folder"));
		}

		foreach($ol->arr() as $o)
		{
			$this->_add_vars[] = "taxfld_".$o->id();
			$table_row = array(
				"hfrom" => html::get_change_url($o->id(),array("return_url" => get_ru()),$m->get_cat_id_description($o->prop("folder"))?$m->get_cat_id_description($o->prop("folder")):"-"),
				"hedit" => html::popup(array(
					"url" => $this->mk_my_orb("select_tax_cat", array("id" => $arr["obj_inst"]->id(), "var" => "taxfld_".$o->id()), "budgeting_workspace"),
					"caption" => html::img(array(
						"url" => aw_ini_get("baseurl")."/automatweb/images/icons/edit.gif",
						"border" => 0
					)),
					"scrollbars" => "auto"
				)),
				"oid" => $o->id(),
				"use_used_settings" => html::select(array(
					"name" => "d[".$o->id()."][use_used_settings]",
					"options" => $props_selection,
					"value" => $o->prop("use_used_settings"),
				)),
			);

			if(!$o->prop("use_different_settings"))
			{
				$table_row["amount_final"] = $o->prop("amount_final");
				$table_row["amount"]= $o->prop("amount");
				$table_row["minus"] = $o->prop("max_deviation_minus");
				$table_row["plus"] = $o->prop("max_deviation_plus");
				$table_row["term"] = $o->prop("term");
				$table_row["priority"]= $o->prop("priority");
				$table_row["use_different_settings"]= html::checkbox(array(
					"name" => "d[".$o->id()."][use_different_settings]",
					"checked" => $o->prop("use_different_settings"),
				));
			}
			else
			{
				$table_row["amount_final"] = html::textbox(array(
					"name" => "d[".$o->id()."][amount_final]",
					"size" => 5,
					"value" => $o->prop("amount_final"),
				));
				$table_row["amount"]= html::textbox(array(
					"name" => "d[".$o->id()."][amount]",
					"size" => 3,
					"value" => $o->prop("amount"),
				));
				$table_row["minus"] = html::textbox(array(
					"name" => "d[".$o->id()."][max_deviation_minus]",
					"size" => 3,
					"value" => $o->prop("max_deviation_minus"),
				));
				$table_row["plus"] = html::textbox(array(
					"name" => "d[".$o->id()."][max_deviation_plus]",
					"size" => 3,
					"value" => $o->prop("max_deviation_plus"),
				));
				$table_row["term"] = html::textbox(array(
					"name" => "d[".$o->id()."][term]",
					"size" => 5,
					"value" => $o->prop("term"),
				));
				$table_row["priority"]= html::textbox(array(
					"name" => "d[".$o->id()."][priority]",
					"size" => 1,
					"value" => $accout_tax_data["priority"],
				));
				$table_row["use_different_settings"]= html::checkbox(array(
					"name" => "d[".$o->id()."][use_different_settings]",
					"checked" => $o->prop("use_different_settings"),
				));
			}

			$t->define_data($table_row);
		}
		$arr["prop"]["vcl_inst"] = $t;
	}

	function _get_from_place_toolbar($arr)
	{
		$tb =& $arr["prop"]["vcl_inst"];
		$tb->add_button(array(
			"name" => "new",
			"img" => "new.gif",
			"action" => "create_new_rel"
		));
		$tb->add_delete_button();
		$tb->add_save_button();
	}

	function _init_flow_t(&$t)
	{
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
			"name" => "sum",
			"caption" => t("Summa"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid"
		));
	}

	function _get_flow_table($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_flow_t($t);
		if(!$arr["request"]["start"])
		{
			$arr["request"]["start"] = time() - 365*3600*24;
		}
		if(!$arr["request"]["to"])
		{
			$arr["request"]["to"] = time();
		}
		$ol = new object_list(array(
			"class_id" => CL_BUDGETING_TRANSFER,
			"lang_id" => array(),
			"site_id" => array(),
			"sort_by" => "created desc",
			"limit" => 1,
			"created" => new obj_predicate_compare(OBJ_COMP_BETWEEN_INCLUDING, (date_edit::get_timestamp($arr["request"]["start"])) , date_edit::get_timestamp($arr["request"]["to"])),
			"to_acct" =>  $arr["obj_inst"]->prop("to_acct"),
		));
		$sum = 0;
		foreach($ol->arr() as $o)
		{
			$t->define_data(array(
				"name" => html::obj_change_url($o),
				"sum" => $o->prop("amount"),
				"date" => date("d.m.Y" , $o->created()),
				"oid" => $o->id(),
			));
			$sum += $o->prop("amount");
		}
		$t->set_sortable(false);
		$t->define_data(array(
			"sum" => $sum,
			"date" => t("Kokku"),
		));
	}

	/**
		@attrib name=create_new_rel
	**/
	function create_new_rel($arr)
	{
		$f = obj($arr["id"]);

		$o = obj();
		$o->set_parent($arr["id"]);
		$o->set_class_id(CL_BUDGETING_TAX_FOLDER_RELATION);
		$o->set_name(sprintf(t("Seos maksu %s ja kausta %s vahel"), $f->name(), ""));
		$o->set_prop("tax", $f->id());
		$o->set_prop("folder", "");
		$o->save();

		return $arr["post_ru"];
	}
}
?>
