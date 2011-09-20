<?php

class shop_delivery_note_obj extends _int_object
{
	const CLID = 1459;

	function awobj_get_delivery_date()
	{
		$conn = parent::connections_to(array(
			"from.class_id" => CL_MATERIAL_MOVEMENT_RELATION,
		));
		foreach($conn as $c)
		{
			$mmr = $c->from();
			$job = $mmr->prop("job");
			if($this->can("view", $job))
			{
				$jo = obj($job);
				return $jo->prop("starttime");
			}
		}
		return parent::prop("delivery_date");
	}

	function awobj_set_state($v)
	{
		if ($v == 1)
		{
			$this->set_prop("approved", 1);
		}
	}

	function update_dn(array $data)
	{
		$dno = $this;
		$dno->set_prop("from_warehouse", $data["from_warehouse"]);
		$dno->set_prop("customer", $data["customer"]);
		$dno->set_prop("impl", $data["impl"]);
		$dno->set_prop("currency", $data["currency"]);
		$dno->save();
		if(isset($data["rows"]) and is_array($data["rows"]))
		{
			$dno->update_dn_rows($data["rows"]);
		}
	}

	function update_dn_rows(array $data)
	{
		$dno = $this;
		$conn = $dno->connections_from(array(
			"type" => "RELTYPE_ROW",
		));
		$name = $dno->name();
		foreach($conn as $c)
		{
			$row = $c->to();
			$prod = $row->prop("product");
			if($data[$prod])
			{
				$set_prods[$prod] = $prod;
				$row->set_prop("amount", $data[$prod]["amount"]);
				$row->set_prop("unit", $data[$prod]["unit"]);
				$row->set_prop("price", $data[$prod]["price"]);
				$row->save();
			}
			else
			{
				$row->delete();
			}
		}
		foreach($data as $prod => $d)
		{
			if(!$set_prods[$prod])
			{
				$this->create_dn_row(array(
					"dno" => $dno,
					"unit" => $d["unit"],
					"amount" => $d["amount"],
					"price" => $d["price"],
					"name" => $name,
					"prod" => $prod,
				));
			}
		}
	}

	function create_dn($name, $parent, $data)
	{
		$dno = obj();
		$dno->set_class_id(CL_SHOP_DELIVERY_NOTE);
		$dno->set_parent($parent);
		$dno->set_name($name);
		$dno->set_prop("from_warehouse", $data["from_warehouse"]);
		$dno->set_prop("customer", $data["customer"]);
		$dno->set_prop("impl", $data["impl"]);
		$dno->set_prop("currency", $data["currency"]);
		$dno->save();
		foreach($data["rows"] as $prod => $d)
		{
			$this->create_dn_row(array(
				"dno" => $dno,
				"amount" => $d["amount"],
				"unit" => $d["unit"],
				"price" => $d["price"],
				"name" => $name,
				"prod" => $prod,
			));
		}
		return $dno;
	}

	function create_dn_row($arr)
	{
		extract($arr);
		$o = obj();
		$o->set_class_id(CL_SHOP_DELIVERY_NOTE_ROW);
		$o->set_parent($dno->id());
		$o->set_name(sprintf(t("%s rida"), $dno->name()));
		$o->set_prop("amount", $amount);
		$o->set_prop("unit", $unit);
		$o->set_prop("product", $prod);
		$o->set_prop("price", $price);
		$o->save();
		$dno->connect(array(
			"to" => $o,
			"reltype" => "RELTYPE_ROW",
		));
	}

	/**
	@attrib name=create_movement api=1

	@comment
		if called on a delivery note object, creates movements from its rows and changes the warehouse amounts
	@returns
		true everything is OK, false otherwise (error msg in session variable dn_error)
	**/
	function create_movement()
	{
		$conn = $this->connections_from(array(
			"type" => "RELTYPE_ROW",
		));
		$single_vars = array(
			0 => array(
				"prod_prop" => "serial_number_based",
				"err_word1" => t("Seerianumbri"),
				"err_word2" => t("seerianumber"),
				"row_prop" => "serial_no",
				"single_type" => "2",
			),
			1 => array(
				"prod_prop" => "order_based",
				"err_word1" => t("Partii numbri"),
				"err_word2" => t("partiinumber"),
				"row_prop" => "set_no",
				"single_type" => "1",
			),
		);
		$pi = get_instance(CL_SHOP_PRODUCT);
		$ufi = obj();
		$ufi->set_class_id(CL_SHOP_UNIT_FORMULA);
		$ci = get_instance(CL_CURRENCY);
		$wo = $this->prop("writeoff");
		$twh = $this->prop("to_warehouse");
		$fwh = $this->prop("from_warehouse");
		foreach($conn as $c)
		{
			$row = $c->to();
			if(!$row->prop("unit"))
			{
				$this->movement_error(t("Igal tootel tuleb &uuml;hik m&auml;&auml;rata."));
			}
			if(!$row->prop("amount"))
			{
				$this->movement_error(t("Igal tootel tuleb kogus m&auml;&auml;rata."));
			}
			if($wo)
			{
				if($twh || $row->prop("warehouse"))
				{
					$this->movement_error(t("Mahakandmist ei saa teostada, kuna on m&auml;&auml;ratud sihtladu"));
				}
			}
			else
			{
				if(!$twh && !$fwh && !$row->prop("warehouse"))
				{
					$this->movement_error(t("Ladu on m&auml;&auml;ramata"));
				}
			}
			if($twh && !$fwh)
			{
				$def_cur = obj($twh)->prop("conf.def_currency");
				if(!$def_cur)
				{
					$this->movement_error(t("Sihtlao seadetes puudub vaikimisi valuuta"));
				}
				$sum = $row->prop("price");
				$cur = $this->prop("currency");
				if(!$cur)
				{
					$this->movement_error(t("Valuuta on m&auml;&auml;ramata"));
				}
				$base_prices[$row->id()] = $sum;
				if($cur != $def_cur)
				{
					$newsum = $ci->convert(array(
						"sum" => $sum,
						"from" => $cur,
						"to" => $def_cur,
					));
					if($sum == $newsum && $sum)
					{
						$this->movement_error(sprintf(t("Puudub kurss valuutade %s ja %s vahel"), obj($cur)->name(), obj($def_cur)->name()));
					}
					$base_prices[$row->id()] = $newsum;
				}
			}
			$prod_id = $row->prop("product");
			$prod = obj($prod_id);
			$units = $pi->get_units($prod);
			if(!count($units))
			{
				$this->movement_error(sprintf(t("Tootel %s pole m&auml;&auml;ratud p&otilde;hi&uuml;hikut"), $prod->name()));
			}
			foreach($units as $i=>$unit)
			{
				if(!$i && !$this->can("view", $unit))
				{
					$this->movement_error(sprintf(t("Tootel %s pole m&auml;&auml;ratud p&otilde;hi&uuml;hikut"), $prod->name()));
				}
				if($this->prop("from_warehouse"))
				{
					$ch_amt = $this->get_wh_amount($row, $this, true, $unit);
					if(!is_numeric($ch_amt))
					{
						//aw_session_set("dn_err", sprintf(t("Tootel %s puudub l&auml;htelaos antud parameetritega laoseis"), $prod->name()));
						//return false;
					}
				}
				if($unit != $row->prop("unit") && $unit && $this->can("view", $unit))
				{
					$fo = $ufi->get_formula(array(
						"from_unit" => $row->prop("unit"),
						"to_unit" => $unit,
						"product" => $prod,
					));
					if($fo)
					{
						$amt = $ufi->calc_amount(array(
							"amount" => $row->prop("amount"),
							"prod" => $prod,
							"obj" => $fo,
						));
						$amts[$row->id()][$unit] = $amt;
					}
					else
					{
						$from_unit = obj($row->prop("unit"));
						$to_unit = obj($unit);
						$this->movement_error(sprintf(t("Tootel %s puudub arvutusvalem %s -> %s"), $prod->name(), $from_unit->name(), $to_unit->name()));
					}
				}
				elseif($unit == $row->prop("unit"))
				{
					$amts[$row->id()][$unit] = $row->prop("amount");
				}
			}
			$prod_units[$prod_id] = $units;
		}
		$err = aw_global_get("dn_err");
		if(count($err))
		{
			return false;
		}
		foreach($conn as $c)
		{
			$row = $c->to();
			$prod_id = $row->prop("product");
			$prod = obj($prod_id);
			$singles = array();
			$uses_single = 2;
			foreach($single_vars as $sv)
			{
				if($prod->prop($sv["prod_prop"]))
				{
					if(!($no = $row->prop($sv["row_prop"])))
					{
						aw_session_set("dn_err", sprintf(t("%s p&otilde;hise arvestusega tootel %s tuleb %s m&auml;&auml;rata."), $sv["err_word1"], $prod->name(), $sv["err_word2"]));
						return false;
					}
					$sp = array(
						"class_id" => CL_SHOP_PRODUCT_SINGLE,
						"code" => $no,
						"type" => $sv["single_type"],
						"product" => $prod_id,
						"site_id" => array(),
						"lang_id" => array(),
					);
					$find_ol = new object_list($sp);
					if($find_ol->count())
					{
						$singles[] = $find_ol->begin();
					}
					elseif(!$this->prop("from_warehouse"))
					{
						$o = obj();
						$o->set_class_id(CL_SHOP_PRODUCT_SINGLE);
						$o->set_parent($prod_id);
						$types = $o->instance()->get_types();
						$o->set_name(sprintf("%s %s", $prod->name(), $row->prop($sv["row_prop"])));
						$o->set_prop("product", $prod_id);
						$o->set_prop("type", $sv["single_type"]);
						$o->set_prop("code", $row->prop($sv["row_prop"]));
						$o->save();
						$singles[] = $o;
					}
				}
				else
				{
					$uses_single--;
				}
			}
			$params = array(
				"row" => $row,
				"units" => $prod_units[$prod_id],
				"obj_inst" => $this,
				"amounts" => $amts,
				"base_prices" => $base_prices,
			);
			if(!$uses_single)
			{
				$this->create_movement_from_param($params);
			}
			else
			{
				foreach($singles as $single)
				{
					$params["single"] = $single->id();
					$this->create_movement_from_param($params);
				}
			}
		}
		return true;
	}

	private function movement_error($msg)
	{
		$err = aw_global_get("dn_err");
		if(!is_array($err))
		{
			$err = array($msg);
		}
		else
		{
			$err[] = $msg;
		}
		aw_session_set("dn_err", array_unique($err));
	}

	function create_movement_from_param($arr)
	{
		$row = $arr["row"];
		$from_wh_id = $this->prop("from_warehouse");
		if(is_oid($from_wh_id) && $from_wh_id)
		{
			$from_wh = obj($from_wh_id);
		}
		$twh = $row->prop("warehouse");
		if(is_oid($twh))
		{
			$to_wh = obj($twh);
		}
		$to_wh_id = $this->prop("to_warehouse");
		if(is_oid($to_wh_id))
		{
			$to_wh = obj($to_wh_id);
		}
		if($to_wh && $from_wh && $to_wh->id() == $from_wh->id())
		{
			return;
		}
		$wh_vars = array(
			0 => array(
				"amt_mod" => -1,
				"var" => "from_wh",
			),
			1 => array(
				"amt_mod" => 1,
				"var" => "to_wh",
			),
		);
		$prod_id = $row->prop("product");
		$prod = obj($prod_id);
		$sid = $arr["single"];
		$pi = $prod->instance();
		foreach($wh_vars as $whv)
		{
			if(${$whv["var"]})
			{
				foreach($arr["units"] as $i=>$unit)
				{
					if($unit && $this->can("view", $unit))
					{
						$amt = $arr["amounts"][$row->id()][$unit];
						$is_default = 0;
						if($i === 0)
						{
							$defamt = $amt;
							$is_default = 1;
						}
						$amount = $pi->get_amount(array(
							"unit" => $unit,
							"prod" => $prod_id,
							"single" => $sid,
							"warehouse" => ${$whv["var"]}->id(),
						));
						if(!$amount->count())
						{
							$amto = obj();
							$amto->set_class_id(CL_SHOP_WAREHOUSE_AMOUNT);
							$amto->set_parent($prod_id);
							$amto->set_prop("warehouse", ${$whv["var"]}->id());
							$amto->set_prop("product", $prod_id);
							$amto->set_prop("single", $sid);
							$amto->set_prop("amount", ($whv["amt_mod"] * $amt));
							$amto->set_prop("unit", $unit);
							$amto->set_prop("is_default", $is_default);
							$amto->set_name(sprintf(t("%s laoseis"), $prod->name()));
							$amto->save();
						}
						else
						{
							$amto = $amount->begin();
							$amto->set_prop("amount", $amto->prop("amount") + $whv["amt_mod"] * $amt);
							$amto->save();
						}
					}
				}
			}
		}
		$mvo = obj();
		$mvo->set_class_id(CL_SHOP_WAREHOUSE_MOVEMENT);
		$mvo->set_prop("from_wh", $from_wh?$from_wh->id():null);
		$mvo->set_prop("to_wh", $to_wh?$to_wh->id():null);
		$mvo->set_prop("product", $prod_id);
		$mvo->set_prop("single", $sid);
		$mvo->set_prop("amount", $defamt);
		$mvo->set_prop("unit", $arr["units"][0]);
		$mvo->set_prop("price", $row->prop("price"));
		$mvo->set_prop("base_price", $arr["base_prices"][$row->id()]);
		$mvo->set_prop("transport", $this->prop("transport"));
		$mvo->set_prop("customs", $this->prop("customs"));
		$mvo->set_prop("date", $this->prop("delivery_date"));
		$mvo->set_prop("delivery_note", $this->id());
		$mvo->set_prop("currency", $this->prop("currency"));
		$mvo->set_parent($prod_id);
		$mvo->set_name(sprintf(t("%s liikumine"), $prod->name()));
		$mvo->save();
		$pi->calc_fifo_price($prod);
	}

	function get_wh_amount($row, $o, $set_chk = false, $unit = null)
	{
		if($fwh = $o->prop("from_warehouse"))
		{
			$prod = $row->prop("product");
			$po = obj($prod);
			$serial = $po->prop("serial_number_based");
			$set = $po->prop("order_based");
			if($serial)
			{
				$code = $row->prop("serial_no");
			}
			elseif($set)
			{
				$code = $row->prop("set_no");
				$set_checked = 1;
			}
			else
			{
				$nocode = 1;
			}
			if(isset($code))
			{
				if($code)
				{
					$params["singlecode"] = $code;
				}
				elseif(!$nocode)
				{
					return;
				}
			}
			$params["prod"] = $prod;
			$params["warehouse"] = $fwh;
			$params["unit"] = $unit ? $unit : $row->prop("unit");
			$pi = get_instance(CL_SHOP_PRODUCT);
			$ol = $pi->get_amount($params);
			if($ol && $ol->count() > 0)
			{
				$amount = $ol->begin();
				if($set_chk && !$set_checked && $set)
				{
					$params["singlecode"] = $row->prop("set_no");
					$ol2 = $pi->get_amount($params);
					if(!$ol2 || !$ol2->count())
					{
						return false;
					}
				}
				return $amount->prop("amount");
			}
		}
	}

	function _get_warehouse_chooser()
	{
		$ol = new object_list(array(
			"class_id" => CL_SHOP_WAREHOUSE,
			"site_id" => array(),
			"lang_id" => array(),
		));
		$whs = array();
		foreach($ol->arr() as $oid => $o)
		{
			$whs[$oid] = $o->prop("short_name") ? $o->prop("short_name") : $o->name();
		}
		$whs = array(0=>t("--vali--")) + $whs;
		natcasesort($whs);
		return $whs;
	}

	function _get_article_code_chooser()
	{
		$ol = new object_list(array(
			"class_id" => CL_SHOP_PRODUCT,
			"site_id" => array(),
			"lang_id" => array(),
		));
		$res = array();
		foreach($ol->arr() as $o)
		{
			if($code = $o->prop("code"))
			{
				$res[$o->id()] = $code;
			}
		}
		$res[0] = " ".t("--vali--");
		natcasesort($res);
		return $res;
	}

	public function get_sum()
	{
		$sum = 0;
		foreach($this->connections_from(array("type" => "RELTYPE_ROW")) as $c)
		{
			$row = $c->to();
			$c_sum = $row->amount * $row->price;
			$sum += $c_sum;
		}
		return $sum;
	}
}

?>
