<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/budgeting/budgeting_tax_matrix.aw,v 1.5 2008/05/14 15:45:20 markop Exp $
// budgeting_tax_matrix.aw - Eelarvestamise maksumaatriks 
/*

@classinfo syslog_type=ST_BUDGETING_TAX_MATRIX relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo

@default table=objects
@default group=general

@default group=taxes

	@property taxes_tb type=toolbar no_caption=1 store=no
	@property taxes_table type=table no_caption=1 store=no

@default group=accts

	@property accts_tb type=toolbar no_caption=1 store=no
	@property accts_table type=table no_caption=1 store=no

@default group=disp

	@property disp_table type=table store=no no_caption=1

@groupinfo taxes caption="Maksud" submit=no
@groupinfo accts caption="Kontod" submit=no 
@groupinfo disp caption="Maatriks"

@reltype TAX value=1 clid=CL_BUDGETING_TAX
@caption Maks
*/

class budgeting_tax_matrix extends class_base
{
	const AW_CLID = 1350;

	function budgeting_tax_matrix()
	{
		$this->init(array(
			"tpldir" => "applications/budgeting/budgeting_tax_matrix",
			"clid" => CL_BUDGETING_TAX_MATRIX
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
		};
		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
		}
		return $retval;
	}	

	function callback_post_save($arr)
	{	
		$ps = new popup_search();
		$ps->do_create_rels($arr["obj_inst"], $arr["request"]["add_tax"], "RELTYPE_TAX");

	}

	function callback_pre_save($arr)
	{
		$d = $arr["obj_inst"]->meta("accts");
		foreach($arr["request"] as $k => $v)
		{
			if (substr($k, 0, 7) == "taxfld_" && strlen($v) > 1)
			{
				list(, $_id) = explode("_", $k, 2);
				$d[$_id] = $v;
			}
		}
		$arr["obj_inst"]->set_meta("accts", $d);
	}

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
		$arr["add_tax"] = "0";
		foreach($this->_add_vars as $var)
		{
			$arr[$var] = "0";
		}
	}

	function _get_taxes_tb($arr)
	{
		$tb =& $arr["prop"]["vcl_inst"];
		$tb->add_search_button(array(
			"pn" => "add_tax",
			"clid" => CL_BUDGETING_TAX,
		));
		$tb->add_delete_rels_button();
	}

	function _get_taxes_table($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$arr["prop"]["vcl_inst"]->table_from_ol(
			new object_list($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_TAX"))),
			array("name", "comment", "to_acct", "amount", "pri", "max_deviation_minus", "max_deviation_plus", "tax_grp"),
			CL_BUDGETING_TAX
		);
	}

	function _init_accts_table(&$t)
	{
		$t->define_field(array(
			"name" => "hfrom",
			"caption" => t("Kust"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "hedit",
			"caption" => t("Muuda"),
			"align" => "center"
		));
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid"
		));
	}

	function _get_accts_table($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_accts_table($t);

		$d = $arr["obj_inst"]->meta("accts");
		if (!is_array($d))
		{
			$d[2] = "";
		}
		else
		{
			$d[] = "";
		}

		$m = get_instance("applications/budgeting/budgeting_model");
		foreach($d as $num => $item)
		{
			$this->_add_vars[] = "taxfld_".$num;
			$t->define_data(array(
				"hfrom" => $m->get_cat_id_description($item),
				"hedit" => html::popup(array(
					"url" => $this->mk_my_orb("select_tax_cat", array("id" => $arr["obj_inst"]->id(), "var" => "taxfld_".$num), "budgeting_workspace"),
					"caption" => html::img(array(
						"url" => aw_ini_get("baseurl")."/automatweb/images/icons/edit.gif",
						"border" => 0
					)),
					"scrollbars" => "auto"
				)),
				"oid" => $num
			));
		}
		$t->set_sortable(false);
	}

	function _get_accts_tb($arr)
	{
		$tb =& $arr["prop"]["vcl_inst"];
		$tb->add_button(array(
			"name" => "del",
			"caption" => t("Kustuta"),
			"img" => "delete.gif",
			"action" => ""
		));
	}

	function _set_accts_table($arr)
	{
		$d = $arr["obj_inst"]->meta("accts");
		foreach(safe_array($arr["request"]["sel"]) as $_id)
		{
			unset($d[$_id]);
		}
		$arr["obj_inst"]->set_meta("accts", $d);
	}

	function _get_disp_table($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$m = get_instance("applications/budgeting/budgeting_model");
		$t->define_field(array(
			"name" => "desc",
			"caption" => t("Maks"),
			"align" => "right"
		));
		foreach(safe_array($arr["obj_inst"]->meta("accts")) as $acct_num => $acct_id)
		{
			$t->define_field(array(
				"name" => $acct_id,
				"caption" => $m->get_cat_id_description($acct_id),
				"align" => "center"
			));
		}

		$this->props_selection = array("" => "");

		$tax2acct = array();

		foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_TAX")) as $c)
		{
			$d = array(
				"desc" => $c->prop("to.name")
			);
			$ol = new object_list(array(
				"class_id" => CL_BUDGETING_TAX_FOLDER_RELATION,
				"tax" => $c->prop("to"),
				"lang_id" => array(),
				"site_id" => array()
			));


			foreach($ol-> arr() as $o)
			{
				$this->props_selection[$o->id()] = $m->get_cat_id_description($o->prop("folder"));
			}

			foreach($ol->arr() as $o)
			{
//				$tax = obj($o->prop("tax"));
//				$tax_terms = $tax->get_terms($o->prop("folder"));
//				if(sizeof($tax_terms->ids()))
//				{
//					foreach($tax_terms->arr() as $tt)
//					{

						$tax2acct[$o->prop("tax")][$o->prop("folder")][] = array(
							"final_amount" => $o->prop("amount_final"),
							"amount" => $o->prop("amount"),
							"plus" => $o->prop("max_deviation_plus"),
							"minus" => $o->prop("max_deviation_minus"),
							"term" => $o->prop("term"),
							"priority" => $o->prop("pri"),
							"id" => $o->id(),
							"use_different_settings" => $o->prop("use_different_settings"),
							"acct_id" => $o->prop("folder"),
							"use_used_settings" => $o->prop("use_used_settings"),
						);
						if($o->prop("use_different_settings")) $this->use_different_settings = 1;
//					}
//				}
//				else
//				{
/*					$tax2acct[$o->prop("tax")][$o->prop("folder")][] = array(
						"final_amount" => "",
						"amount" => $tax->prop("amount"),
						"plus" => $tax->prop("max_deviation_plus"),
						"minus" => $tax->prop("max_deviation_minus"),
						"term" => "",
						"term_sum" => "",
						"priority" => "",
						"id" => $c->prop("to"),
						"use_tax_data" => 1,
					);*/
//				}
			}

		}
		//	arr($arr["obj_inst"]->meta("accts"));

		foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_TAX")) as $c)
		{
			$d = array(
				"desc" => $c->prop("to.name")
			);

			foreach(safe_array($arr["obj_inst"]->meta("accts")) as $acct_num => $acct_id)
			{//arr($acct_id);
				$d[$acct_id] = $this->_get_account_tax_table($tax2acct[$c->prop("to")][$acct_id]);
/*				$d[$acct_id] = html::textbox(array(
					"name" => "d[".$c->prop("to")."][$acct_id]",
					"size" => 5,
					"value" => ($tax2acct[$c->prop("to")][$acct_id] ? $tax2acct[$c->prop("to")][$acct_id]->prop("amount") : "")
				));*/
			}
			$t->define_data($d);
		}
	}

	function _init_accout_tax_table()
	{
		$t = new vcl_table;
		$t->define_field(array(
			"name" => "final_amount",
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
//		$t->define_field(array(
//			"name" => "priority",
//			"caption" => t("Prioriteet"),
//		));
		return $t;
	}

	function _get_account_tax_table($arr)
	{
		if(!sizeof($arr))
		{
			return "";
		}
		$t = $this->_init_accout_tax_table();
		$ret = "";



		foreach($arr as $accout_tax_data)
		{
			$acct_id = $accout_tax_data["acct_id"];
			if(!$accout_tax_data["use_different_settings"])
			{
				$d["final_amount"] = $accout_tax_data["final_amount"];
				$d["amount"]= $accout_tax_data["amount"];
				$d["minus"] = $accout_tax_data["minus"];
				$d["plus"] = $accout_tax_data["plus"];
				$d["term"] = $accout_tax_data["term"];
				$d["priority"]= $accout_tax_data["priority"];
				$d["use_different_settings"]= html::checkbox(array(
					"name" => "d[".$accout_tax_data["id"]."][$acct_id][use_different_settings]",
					"checked" => $accout_tax_data["use_different_settings"],
				)).
				html::hidden(array(
					"name" => "d[".$accout_tax_data["id"]."][$acct_id][id]",
					"value" => $accout_tax_data["id"],
				));
				$d["use_used_settings"] = html::select(array(
					"name" => "d[".$accout_tax_data["id"]."][$acct_id][use_used_settings]",
					"options" => $this->props_selection,
					"value" => $accout_tax_data["use_used_settings"],
				));
			}
			else
			{
				$d["final_amount"] = html::textbox(array(
					"name" => "d[".$accout_tax_data["id"]."][$acct_id][amount_final]",
					"size" => 5,
					"value" => $accout_tax_data["final_amount"],
				));
				$d["amount"]= html::textbox(array(
					"name" => "d[".$accout_tax_data["id"]."][$acct_id][amount]",
					"size" => 3,
					"value" => $accout_tax_data["amount"],
				));
				$d["minus"] = html::textbox(array(
					"name" => "d[".$accout_tax_data["id"]."][$acct_id][max_deviation_minus]",
					"size" => 3,
					"value" => $accout_tax_data["minus"],
				));
				$d["plus"] = html::textbox(array(
					"name" => "d[".$accout_tax_data["id"]."][$acct_id][max_deviation_plus]",
					"size" => 3,
					"value" => $accout_tax_data["plus"],
				));
				$d["term"] = html::textbox(array(
					"name" => "d[".$accout_tax_data["id"]."][$acct_id][term]",
					"size" => 5,
					"value" => $accout_tax_data["term"],
				));
				$d["priority"]= html::textbox(array(
					"name" => "d[".$accout_tax_data["id"]."][$acct_id][priority]",
					"size" => 1,
					"value" => $accout_tax_data["priority"],
				));
				$d["use_different_settings"]= html::checkbox(array(
					"name" => "d[".$accout_tax_data["id"]."][$acct_id][use_different_settings]",
					"checked" => $accout_tax_data["use_different_settings"],
				)).
				html::hidden(array(
					"name" => "d[".$accout_tax_data["id"]."][$acct_id][id]",
					"value" => $accout_tax_data["id"],
				));
				$d["use_used_settings"] = html::select(array(
					"name" => "d[".$accout_tax_data["id"]."][$acct_id][use_used_settings]",
					"options" => $this->props_selection,
					"value" => $accout_tax_data["use_used_settings"],
				));
			}
			$t->define_data($d);
		}
		return $t->draw();
	}

	function _set_disp_table($arr)
	{//arr($arr); die();
		foreach(safe_array($arr["request"]["d"]) as $tax_id => $d1)
		{
			foreach($d1 as $acct_id => $data)
			{
				$btfr = obj($data["id"]);
				if($data["use_different_settings"])
				{
					foreach($data as $prop => $val)
					{
						if($btfr->is_property($prop))
						{
							$btfr -> set_prop($prop , $val);
						}

					}
					$btfr->set_prop("use_used_settings" , 0);
				}
				else
				{
					$btfr -> set_prop("use_different_settings" , 0);
					if($data["use_used_settings"])
					{
						$btfr->set_prop("use_used_settings" , $data["use_used_settings"]);
					}
				}

				$btfr->save();
			}

/*
			$ol = new object_list(array(
				"class_id" => CL_BUDGETING_TAX_FOLDER_RELATION,
				"tax" => $tax_id,
				"lang_id" => array(),
				"site_id" => array()
			));
			$tax2acct = array();
			foreach($ol->arr() as $o)
			{
				$tax2acct[$o->prop("tax")][$o->prop("folder")] = $o;
			}
*/
/*			foreach($d1 as $acct_id => $data)
			{
				if (!(is_array($data) && sizeof($data)) && isset($tax2acct[$tax_id][$acct_id]))
				{
					$tax2acct[$tax_id][$acct_id]->delete();
				}
				else
				if ($tax_amt > 0  && isset($tax2acct[$tax_id][$acct_id]))
				{
					$to = obj($tax2acct[$tax_id][$acct_id]->prop("tax"));
					$to->set_prop("amount", $tax_amt);
					$to->save();
				}
				else
				if ($tax_amt > 0  && !isset($tax2acct[$tax_id][$acct_id]))
				{
					$to = obj($tax_id);
					$o = obj();
					$o->set_parent($tax_id);
					$o->set_class_id(CL_BUDGETING_TAX_FOLDER_RELATION);
					$o->set_name(sprintf(t("Seos maksu %s ja kausta %s vahel"), $to->name(), $acct_id));
					$o->set_prop("tax", $tax_id);
					$o->set_prop("folder", $acct_id);
					$o->save();
	
					$to->set_prop("amount", $tax_amt);
					$to->save();
				}
			}*/
		}
	}
}
?>
