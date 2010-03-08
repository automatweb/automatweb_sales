<?php
/*
@classinfo  maintainer=kristo
*/

class budgeting_model extends core
{
	function budgeting_model()
	{
		$this->init();
	}

	function apply_taxes_on_money_transfer($transfer)
	{
		// get level and make transfer to next level, then relaunch
		
		$n_tax_from_acct = $transfer->prop("to_acct");
		$n_tax_to_acct = $this->get_next_propagation_level_from_acct($transfer->prop("to_acct"), $transfer);

		$used_money = 0;
		foreach($n_tax_to_acct as $tf_data)
		{
			if($tf_data["amount"] == $transfer->prop("amount"))//sellisel juhul peaks vist lahutama juba kuskile kantud raha
			{
				$tf_data["amount"] = $tf_data["amount"] - $used_money;
			}
			$used_money+= $tf_data["amount"];
			echo "from account ".$transfer->prop("to_acct.name")." to ".$tf_data["to_acct"]." amt = ".$tf_data["amount"]." <br>\n";
			flush();
			$to = $this->create_money_transfer(obj($transfer->prop("to_acct")), obj($tf_data["to_acct"]), $tf_data["amount"], array(
				"in_project" => $transfer->prop("in_project")
			));
			$this->apply_taxes_on_money_transfer($to);
		}
	}

	function create_money_transfer($from_acct, $to_acct, $amt, $data = null)
	{
		$from_balance = $this->get_account_balance($from_acct);
		$to_balance = $this->get_account_balance($to_acct);

		error::raise_if($from_balance < $amt, array(
			"id" => "ERR_NO_MONEY",
			"msg" => sprintf(t("Kontol %s ei ole piisavalt raha! Vaja on %s, kontol on %s"), $from_acct, $amt, $from_balance)
		));

		$this->start_transaction();
		$this->set_account_balance($to_acct, $to_balance+$amt);
		$this->set_account_balance($from_acct, $from_balance-$amt);
echo "set account balance ".$to_acct->name()." (".$to_acct->id().") => ".($to_balance+$amt)." <br>";
echo "set account balance ".$from_acct->name()." (".$from_acct->id().") => ".($from_balance-$amt)." <br>";
		if (!$this->end_transaction())
		{
			error::raise(array(
				"id" => "ERR_TRANSACTION_FAILED",
				"msg" => sprintf(t("Transaktsioonis esines viga %s "), join(",", $this->errmsg))
			));
		}

		$fo = obj($from_acct);

		$to = obj();
		$to->set_parent($fo->id());
		$to->set_class_id(CL_BUDGETING_TRANSFER);
		$to->set_name(sprintf(t("Kanne kontolt %s kontole %s %s"), "", "", date("d.m.Y H:i:s")));
		$to->set_prop("from_acct", $from_acct->id());
		$to->set_prop("to_acct", $to_acct->id());
		$to->set_prop("amount", $amt);
		$to->set_prop("when", time());
		$to->set_prop("in_project", $data["in_project"]);
		$to->save();
echo "created transaction ".$to->id()." <br>";
		return $to;
	}

	function start_transaction()
	{
		// this needs to halt all other threads until it is done and disable errors
		$tmpdir = aw_ini_get("server.tmpdir");
		$this->trans_lock_file = $tmpdir."/aw_transaction_lock";
		clearstatcache();
		
		while (file_exists($this->trans_lock_file) && filemtime($this->trans_lock_file) > (time() - 4))
		{
			sleep(1);
		}

		@unlink($this->trans_lock_file);
		touch($this->trans_lock_file);

		$this->_trans = aw_global_get("__from_raise_error");
		aw_global_set("__from_raise_error", 1);
	}

	function end_transaction()
	{
		unlink($this->trans_lock_file);
		// this can let other threads do transactions again and restore errors

		aw_global_set("__from_raise_error", $this->_trans);

		// also, if an error occurred, then return failed status
		return !$GLOBALS["aw_is_error"];
	}

	function get_account_balance($acct)
	{
		$o = obj($acct);
		return $o->prop("balance");
	}

	function set_account_balance($acct, $val)
	{
		$o = obj($acct);
		$rv = $o->prop("balance");
		$o->set_prop("balance", $val);
		$o->save();
		return $rv;
	}

	function get_next_propagation_level_from_acct($acct_id, $transfer)
	{
		// switch for account type and hardwire the next levels
		$acct_o = obj($acct_id);
		$rv = array();

		switch($acct_o->class_id())
		{
			case CL_CRM_CATEGORY:
				$from_place = array("area_".$acct_id);
				if ($this->can("view", $transfer->prop("in_project")))
				{
					$pt = $this->get_transfer_path_from_proj(obj($transfer->prop("in_project")));
					// now, go through the transfer path until we hit this category and then return the next in list
					foreach($pt as $idx => $pt_item)
					{
						if ($pt_item->id() == $acct_o->id())
						{
							$next_cat = $pt[$idx-1];
							if ($next_cat->class_id() == CL_CRM_CATEGORY)
							{
								$from_place[] = "area_".$next_cat->id();
							}
							else
							{
								$from_place[] = "cust_".$next_cat->id();
							}
							$rv[] = array(
								"to_acct" => $next_cat->id(),
								"amount" => $transfer->prop("amount")
							);
						}
					}
				}
				break;

			case CL_CRM_COMPANY:
				$cur = get_current_company();
				if ($cur->id() == $acct_id)
				{
					$pt = $this->get_transfer_path_from_proj(obj($transfer->prop("in_project")));
					$o = $pt[count($pt)-2];
					$from_place[] = "area_".$o->id();
					$rv[] = array(
						"to_acct" => $o->id(),
						"amount" => $transfer->prop("amount")
					);
				}
				else
				{
					$from_place = array("cust_".$acct_id, "projects_".$acct_id);
					if ($this->can("view", $transfer->prop("in_project")))
					{
						$po = obj($transfer->prop("in_project"));
						$from_place[] = "proj_".$po->id();
						$rv[] = array(
							"to_acct" => $po->id(),
							"amount" => $transfer->prop("amount")
						);
					}
				}
				break;

			case CL_PROJECT:
				$from_place = array("proj_".$acct_id);
				// distribute evenly over project tasks. 
				$ol = new object_list(array(
					"class_id" => array(CL_TASK),
					"lang_id" => array(),
					"site_id" => array(),
					"CL_TASK.RELTYPE_PROJECT" => $proj_id
				));

				if ($ol->count())
				{
					$tk = $ol->begin();
					$from_place[] = "task_".$tk->id();
					$rv[] = array(
						"to_acct" => $tk->id(),
						"amount" => $transfer->prop("amount")
					);
				}
				break;

			case CL_TASK:
				$from_place = array("task_".$acct_id);
				break;

			case CL_CRM_PERSON:
				$from_place = array("person_".$acct_id);
				break;

			case CL_BUDGETING_FUND:
				$from_place = array("fund_".$acct_id);
				break;

			case CL_SHOP_PRODUCT:
				$from_place = array("prod_".$acct_id);
				break;

			case CL_BUDGETING_ACCOUNT:
				$from_place = array("acct_".$acct_id);
				break;

		}

		$sum = $this->get_account_balance($acct_o->id());
		if ($from_place != "")
		{
			// get all taxes that go from the category
/*			$ol = new object_list(array(
				"class_id" => CL_BUDGETING_TAX,
				"lang_id" => array(),
				"site_id" => array(),
				"from_place" => $from_place,
				"sort_by" => "aw_budgeting_tax.aw_pri DESC"
			));*/
			$ol = $this->get_taxes_for_account($from_place);arr($from_place);arr($ol->names());
			foreach($ol->arr() as $tax)
			{
				$rv[$tax->prop("pri")] = array(
					"to_acct" => $tax->prop("to_acct"),
//					"amount" => $this->calculate_amount_to_transfer_from_tax($tax, $acct_o)
					"amount" => $tax->calculate_amount_to_transfer($acct_o , $sum)
				);
				$sum = $sum - $rv[$tax->prop("pri")]["amount"];
			}
		}
		krsort($rv);
		return $rv;
	}

	//lykkas selle funktsionaalsuse budgeting_tax sisse, v6idab v2hemalt 5 symboli kirjutamise vaeva
	function calculate_amount_to_transfer_from_tax($tax, $from_acct_o)
	{
//		if (substr($tax->prop("amount"), -1) == "%")
//		{
			return $this->get_account_balance($from_acct_o->id()) * ((double)$tax->prop("amount") / 100.0); 
//		}
		return $tax->prop("amount");
	}

	function get_transfer_path_from_proj($p)
	{
		$ret = array();
		$ret[] = $p;
		$impl = $p->get_first_obj_by_reltype("RELTYPE_ORDERER");
		if (!$impl)
		{
			return $ret;
		}
		$ret[] = $impl;

		// now get category for customer
		$conns = $impl->connections_to(array(
			"from.class_id" => CL_CRM_CATEGORY,
			"type" => "RELTYPE_CUSTOMER"
		));
		if (count($conns))
		{
			$c = reset($conns);
			$cat = $c->from();
			while($cat->class_id() != CL_CRM_COMPANY && count($conns))
			{
				$ret[] = $cat;
				$conns = $cat->connections_to(array(
					"from.class_id" => CL_CRM_CATEGORY,
					"type" => "RELTYPE_CATEGORY"
				));
				$c = reset($conns);
				if ($c)
				{
					$cat = $c->from();
				}
			}
			$ret[] = get_current_company();
		}
		return $ret;
	}

	function get_taxes_for_account($account)
	{
		$btfrs = new object_list(array(
			"class_id" => CL_BUDGETING_TAX_FOLDER_RELATION,
			"lang_id" => array(),
			"site_id" => array(),
			"folder" => $account,
		));
		$taxes = new object_list();

		foreach($btfrs->arr() as $btfr)
		{
			if($btfr->prop("tax") && $btfr->prop("folder"))
			{
				if($this->can("view" , $btfr->prop("tax")))
				{
					$taxes->add($btfr->prop("tax")); 
				}
			}
		}
		$taxes->sort_by(array(
			"prop" => "pri",
			"order" => "desc"
		));
		return $taxes;
	}

	function get_all_taxes_above_project($p)
	{
		$path = $this->get_transfer_path_from_proj($p);
		$rv = array();
		$ids = array();
		$btfrs = array();
		foreach($path as $p_item)
		{
			$ids[] = $this->_get_cat_id_from_obj($p_item);
		}
		
		$btfrs = new object_list(array(
			"class_id" => CL_BUDGETING_TAX_FOLDER_RELATION,
			"lang_id" => array(),
			"site_id" => array(),
			"folder" => $ids,
		));
		$taxes = array();
		$added = array();

		foreach($btfrs->arr() as $btfr)
		{
			if($btfr->prop("tax") && $btfr->prop("folder"))
			{	
				if($this->can("view" , $btfr->prop("tax")))
				{
					if(!$added[$btfr->prop("account")][$btfr->prop("tax")])
					{
						$taxes[] = obj($btfr->prop("tax"));
					}
					$added[$btfr->prop("account")][$btfr->prop("tax")] = 1;
				}
			}
		}
//		$taxes->sort_by(array(
//			"prop" => "pri",
//			"order" => "desc"
//		));
		uasort($taxes, array(&$this, "sort_taxes"));//foreach($taxes as $tax){arr(array("name" => $tax->name(), "pri" => $tax->prop("pri") , "id" => $tax->id()));}
		return $taxes;
	}

	private function sort_taxes($a, $b)
	{
		$ret =  $b->prop("pri") - $a->prop("pri");
		if($ret == 0)
		{
			$ret = $a->id() - $b->id();
		}
		return $ret;
	}

	function get_project_taxes_data($p)
	{
		$path = $this->get_transfer_path_from_proj($p);
		$rv = array();
		$ids = array();
		$btfrs = array();
		foreach($path as $p_item)
		{
			$ids[] = $this->_get_cat_id_from_obj($p_item);
		}
		$btfrs = new object_list(array(
			"class_id" => CL_BUDGETING_TAX_FOLDER_RELATION,
			"lang_id" => array(),
			"site_id" => array(),
			"folder" => $ids,
		));
		$ret = array();
		foreach($btfrs->arr() as $btfr)
		{
			if($btfr->prop("tax") && $btfr->prop("folder"))
			{
				if(!$added[$btfr->prop("account")][$btfr->prop("tax")])
				{
					$ret[] = array(
						"tax" => obj($btfr->prop("tax")),
						"account" => $btfr->prop("folder"),
					);
					$added[$btfr->prop("account")][$btfr->prop("tax")] = 1;
				}
			}
		}
		uasort($ret, array(&$this, "sort_taxes_data"));
		return $ret;
	}

	private function sort_taxes_data($a, $b)
	{
		$ret =  $b["tax"]->prop("pri") - $a["tax"]->prop("pri");
		if($ret == 0)
		{
			return $a["tax"]->id() - $b["tax"]->id();
		}
		return $ret;
	}

	function get_account_object($a)
	{
		if(is_object($a))
		{
			return $a;
		}
		if(!is_oid($a))
		{
			$ad = explode("_" , $a);
			$a = $ad[1];
		}
		if(is_oid($a))
		{
			$a = obj($a);
		}
		return $a;
	}

	function _get_cat_id_from_obj($o)
	{
		switch($o->class_id())
		{
			case CL_CRM_CATEGORY:
				return "area_".$o->id();

			case CL_CRM_COMPANY:
				$cur = get_current_company();
				if ($o->id() == $cur->id())
				{
					return "area_".$o->id();
				}
				return "cust_".$o->id();

			case CL_PROJECT:
				return "proj_".$o->id();

			case CL_TASK:
				return "task_".$o->id();

			case CL_CRM_PERSON:
				return "person_".$o->id();

			case CL_BUDGETING_FUND:
				return "fund_".$o->id();

			case CL_SHOP_PRODUCT:
				return "prod_".$o->id();

			case CL_BUDGETING_ACCOUNT:
				return "acct_".$o->id();
		}
		return $o->id();
	}

	function get_cat_id_description($id)
	{
		if ($id == "area")
		{
			return t("K&otilde;ik valdkonnad");
		}
		if ($id == "funds")
		{
			return t("K&otilde;ik fondid");
		}
		if ($id == "worker")
		{
			return t("K&otilde;ik t&ouml;&ouml;tajad");
		}
		if ($id == "service_type")
		{
			return t("K&otilde;ik teenuse liigid");
		}
		if ($id == "prod_families")
		{
			return t("K&otilde;ik tooteperekonnad");
		}
		if ($id == "accts")
		{
			return t("K&otilde;ik kontod");
		}
		if ($id == "accts_families")
		{
			return t("K&otilde;ik kontod");
		}
		if ($id == "main")
		{
			return t("K&otilde;ik");
		}
		if ($id == "worker")
		{
			return t("K&otilde;ik t&ouml;&ouml;tajad");
		}
		if (substr($id, 0, 14)  == "projprodfamily")
		{
			list(, $cust_id, $cat_id) = explode("_", $id);
			$c = obj($cust_id);
			$t = obj($cat_id);
			return sprintf(t("Projekti %s toodete kategooria %s"), $c->name(), $t->name());
		}
		if (substr($id, 0, 14)  == "custprodfamily")
		{
			list(, $cust_id, $cat_id) = explode("_", $id);
			$c = obj($cust_id);
			$t = obj($cat_id);
			return sprintf(t("Kliendi %s toodete kategooria %s"), $c->name(), $t->name());
		}
		if (substr($id, 0, 12)  == "projprodcats")
		{
			list(, $cust_id) = explode("_", $id);
			$c = obj($cust_id);
			return sprintf(t("Projekti %s toodete kategooriad"), $c->name());
		}
		if (substr($id, 0, 12)  == "projstypecat")
		{
			list(, $cust_id, $cat_id) = explode("_", $id);
			$c = obj($cust_id);
			$t = obj($cat_id);
			return sprintf(t("Projekti %s toodete kategooria %s"), $c->name(), $t->name());
		}
		if (substr($id, 0, 12)  == "custprodcats")
		{
			list(, $cust_id) = explode("_", $id);
			$c = obj($cust_id);
			return sprintf(t("Kliendi %s toodete kategooriad"), $c->name());
		}
		if (substr($id, 0, 12)  == "custstypecat")
		{
			list(, $cust_id, $cat_id) = explode("_", $id);
			$c = obj($cust_id);
			$t = obj($cat_id);
			return sprintf(t("Kliendi %s teenuste t&uuml;&uuml;pide kategooria %s"), $c->name(), $t->name());
		}
		if (substr($id, 0, 10)  == "projstypes")
		{
			list(, $cust_id) = explode("_", $id);
			$c = obj($cust_id);
			return sprintf(t("Projekti %s teenuste liigid"), $c->name());
		}
		if (substr($id, 0, 10)  == "custstypes")
		{
			list(, $cust_id) = explode("_", $id);
			$c = obj($cust_id);
			return sprintf(t("Kliendi %s teenuse liigid"), $c->name());
		}
		if (substr($id, 0, 10)  == "prodfamily")
		{
			list(, $cust_id) = explode("_", $id);
			$c = obj($cust_id);
			return sprintf(t("Toodete kategooria %s"), $c->name());
		}
		if (substr($id, 0, 8)  == "stypecat")
		{
			list(, $cust_id) = explode("_", $id);
			$c = obj($cust_id);
			return sprintf(t("Teenuste liigi kategooria %s"), $c->name());
		}
		if (substr($id, 0, 8)  == "projprod")
		{
			list(, $cust_id, $cat_id) = explode("_", $id);
			$c = obj($cust_id);
			$t = obj($cat_id);
			return sprintf(t("Projekti %s toode %s"), $c->name(), $t->name());
		}
		if (substr($id, 0, 8)  == "projects")
		{
			list(, $cust_id) = explode("_", $id);
			$c = obj($cust_id);
			return sprintf(t("Kliendi %s projektid"), $c->name());
		}
		if (substr($id, 0, 8)  == "custprod")
		{
			list(, $cust_id, $cat_id) = explode("_", $id);
			$c = obj($cust_id);
			$t = obj($cat_id);
			return sprintf(t("Kliendi %s toode %s"), $c->name(), $t->name());
		}
		if (substr($id, 0, 4)  == "area")
		{
			list(, $cust_id) = explode("_", $id);
			$c = obj($cust_id);
			return sprintf(t("Kliendikategooria %s"), $c->name());
		}
		if (substr($id, 0, 4)  == "cust")
		{
			list(, $cust_id) = explode("_", $id);
			$c = obj($cust_id);
			return sprintf(t("Klient %s"), $c->name());
		}

		if (substr($id, 0, 4)  == "proj")
		{
			list(, $cust_id) = explode("_", $id);
			$c = obj($cust_id);
			return sprintf(t("Projekt %s"), $c->name());
		}
		if (substr($id, 0, 4)  == "task")
		{
			list(, $cust_id) = explode("_", $id);
			$c = obj($cust_id);
			return sprintf(t("Toimetus %s"), $c->name());
		}
		if (substr($id, 0, 4)  == "prod")
		{
			list(, $cust_id) = explode("_", $id);
			$c = obj($cust_id);
			return sprintf(t("Toode %s"), $c->name());
		}
		return $id;
	}
}

?>
