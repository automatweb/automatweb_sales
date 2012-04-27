<?php
// currency.aw - Currency management

/*

@classinfo prop_cb=1

@default group=general
	@property ord type=textbox table=objects field=jrk size=5
	@caption J&auml;rjekord

	@property comment type=textbox table=objects field=comment
	@caption Kurss euro suhtes

	@property unit_name type=textbox table=objects field=meta method=serialize
	@caption Raha&uuml;hiku nimetus

	@property small_unit_name type=textbox table=objects field=meta method=serialize
	@caption Peenraha&uuml;hiku nimetus

	@property unit_name_morphology_spec type=textbox table=objects field=meta method=serialize
	@comment Reeglid &uuml;hiku nime kasutamiseks koos summa numbriga. N&auml;ide: 1 euro; * eurot
	@caption Raha&uuml;hiku morfoloogia

	@property small_unit_morphology_spec type=textbox table=objects field=meta method=serialize
	@comment Reeglid &uuml;hiku nime kasutamiseks koos summa numbriga. N&auml;ide: 1 sent; * senti
	@caption Peenraha&uuml;hiku morfoloogia

	@property symbol type=textbox size=2 table=objects field=meta method=serialize
	@caption S&uuml;mbol


@groupinfo rates caption=Kursid
@default group=rates
	@property rates type=callback callback=callback_get_rates
	@caption Kaustad kust otsida


@groupinfo transl caption=T&otilde;lgi
@default group=transl
	@property transl type=callback callback=callback_get_transl store=no
	@caption T&otilde;lgi

*/

define("RET_NAME",1);
define("RET_ARR",2);

class currency extends class_base
{
	protected $trans_props = array(
		"name",
		"small_unit_morphology_spec",
		"unit_name_morphology_spec"
	);

	private $default_currency;
	private $co_currency;

	function currency()
	{
		$this->init(array(
			"tpldir" => "currency",
			"clid" => CL_CURRENCY
		));
		$this->sub_merge = 1;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "rates":
				$this->submit_meta($arr);
				break;
		}
		return $retval;
	}

	function do_table($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "lang",
			"caption" => t("Keel"),
			"sortable" => 1,
		));
		$t->define_field(array(
			"name" => "unit",
			"caption" => t("&Uuml;hik"),
		));
		$t->define_field(array(
			"name" => "small_unit",
			"caption" => t("Peenraha"),
		));
		$t->define_field(array(
			"name" => "unit_after_sum",
			"caption" => t("&Uuml;hik summa j&auml;rel"),
		));
		$t->define_field(array(
			"name" => "small_unit_after_sum",
			"caption" => t("Peenraha summa j&auml;rel"),
		));

		$langdata = array();
		aw_global_set("output_charset", "utf-8");
		$lg = get_instance("languages");
		$langdata = languages::get_list(array("all_data" => 1,"ignore_status" => 1));

		$unit_meta = $arr["obj_inst"]->meta("unit");
		$small_unit_meta = $arr["obj_inst"]->meta("small_unit");
		$unit_after_sum_meta = $arr["obj_inst"]->meta("unit_after_sum");
		$small_unit_after_sum_meta = $arr["obj_inst"]->meta("small_unit_after_sum");

		//kui ei ole keelt valitud, siis ei tule eriti suurt valikut mitte
/*		$t->define_data(array(
			"unit" => html::textbox(array(
				"name" => "unit[en]",
				"value" => $unit_meta[$lang["acceptlang"]],
				"size" => 10,
			)),
			"lang" => "Inglise",
			"small_unit" =>html::textbox(array(
				"name" => "small_unit[en]",
				"value" => $small_unit_meta["en"],
				"size" => 10,
			)),
		));
*/
		foreach($langdata as $id => $lang)
		{
			if($arr["obj_inst"]->lang_id() != $id)
			{
				$t->define_data(array(
					"lang" => $lang["name"],
					"unit" => html::textbox(array(
							"name" => "unit[".$lang["acceptlang"]."]",
							"value" => $unit_meta[$lang["acceptlang"]],
							"size" => 10,
					)),
					"small_unit" =>html::textbox(array(
							"name" => "small_unit[".$lang["acceptlang"]."]",
							"value" => $small_unit_meta[$lang["acceptlang"]],
							"size" => 10,
					)),
					"unit_after_sum" => html::textbox(array(
							"name" => "unit_after_sum[".$lang["acceptlang"]."]",
							"value" => $unit_after_sum_meta[$lang["acceptlang"]],
							"size" => 10,
					)),
					"small_unit_after_sum" =>html::textbox(array(
							"name" => "small_unit_after_sum[".$lang["acceptlang"]."]",
							"value" => $small_unit_after_sum_meta[$lang["acceptlang"]],
							"size" => 10,
					))
				));
			}
		}
	}


	function submit_meta($arr = array())
	{
		$arr["obj_inst"]->set_meta("rates", $arr["request"]["rates"]);
 	}

	function callback_get_rates($arr)
	{
		$rates = $arr["obj_inst"]->meta("rates");
		$count = sizeof($rates);
		if($count > 0 && !$rates[$count-1]["rate"])$count--;
		if($count > 0 && !$rates[$count-1]["rate"])$count--;

		$curr_object_list = new object_list(array(
			"class_id" => CL_CURRENCY,
			"lang_id" => array(),
		));

		$curr_opt = array();
		foreach($curr_object_list->arr() as $curr)
		{
			if($arr["obj_inst"]->id() != $curr->id()) $curr_opt[$curr->id()] = $curr->name();
		}

		$t = new aw_table(array(
			"layout" => "generic"
		));
		$t->define_field(array(
			"name" => "start_date",
			"caption" => t("Alguskuup&auml;ev"),
		));
		$t->define_field(array(
			"name" => "end_date",
			"caption" => t("L&otilde;ppkuup&auml;ev"),
		));
		$t->define_field(array(
			"name" => "currency",
			"caption" => t("Valuuta"),
		));
		$t->define_field(array(
			"name" => "rate",
			"caption" => t("Kurss"),
		));
		$t->define_field(array(
			"name" => "buy_rate",
			"caption" => t("Ostukurss"),
		));
		$t->define_field(array(
			"name" => "sell_rate",
			"caption" => t("M&uuml;&uuml;gikurss"),
		));
		$t->define_field(array(
			"name" => "current_currency",
			"caption" => "",
		));
		for($i = 0; $i < $count+1; $i++)
		{
			$t->define_data(array(
				"start_date" => html::date_select(array(
					"name" => "rates[".$i."][start_date]",
					"value" => $rates[$i]["start_date"])),
				"end_date" => html::date_select(array(
					"name" => "rates[".$i."][end_date]",
					"value" => $rates[$i]["end_date"])),
				"currency" => "1 ".html::select(array(
					"name" => "rates[".$i."][currency]",
					"options" => $curr_opt,
					"value" => $rates[$i]["currency"]))." = ",
				"rate"	=> html::textbox(array(
					"name" => "rates[".$i."][rate]",
					"value" => $rates[$i]["rate"],
					"size" => 5,
				)),
				"buy_rate" => html::textbox(array(
					"name" => "rates[".$i."][buy_rate]",
					"value" => $rates[$i]["buy_rate"],
					"size" => 5,
				)),
				"sell_rate" =>html::textbox(array(
					"name" => "rates[".$i."][sell_rate]",
					"value" => $rates[$i]["sell_rate"],
					"size" => 5,
				)),
				"current_currency" => " ". $arr["obj_inst"]->name(),
			));
		}
		$ret["rates"] = array(
			"name" => "rates",
			"caption" => t("Kursid"),
			"type" => "text",
			"value" => $t->draw(),
		);
		return $ret;
	}

	function get_list($type = RET_NAME)
	{
		$ret = array();
		$ol = new object_list(array(
			"class_id" => CL_CURRENCY,
			"lang_id" => array()
		));
		for($o = $ol->begin(); !$ol->end(); $o = $ol->next())
		{
			if ($type == RET_NAME)
			{
				$ret[$o->id()] = $o->name();
			}
			else
			if ($type == RET_ARR)
			{
				$ret[$o->id()] = array(
					"oid" => $o->id(),
					"name" => $o->name(),
					"rate" => $o->comment()
				);
			}
		}
		return $ret;
	}

	/** converts sum from one currency to another
	@attrib params=name api=1
	@param sum required type=int
		sum to convert
	@param from optional type=oid
		currency object id , default company currency
	@param to optional type=oid
		currency object id , default company currency
	@param date optional type=int
		timestamp... if you want to use old rates
	@returns int , converted sum
	**/
	function convert($args)
	{
		extract($args);
		if(!$sum)
		{
			return 0;
		}
		if(!$date)
		{
			$date = time();
		}
		if(!$from)
		{
			$from = $this->get_default_currency();
		}
		if(!$to)
		{
			$to = $this->get_default_currency();
		}
		if($from == $to)
		{
			return $sum;
		}
		if(!($this->can("view" , $to) && $this->can("view" , $from)))
		{
			return;
		}
		$to_obj = obj($to);
		$from_obj = obj($from);
		if(!($this->can("view" , $to) && $this->can("view" , $from)))
		{
			error::raise(array(
				"id" => ERR_FATAL,
				"msg" => sprintf(t("currency::convert - kas %s v&otilde;i %s pole valuutadaobjektide id'd"), $to, $from),
			));
		}

		$currency_rates = (array) $to_obj->meta("rates");
		$changed = false;
		foreach($currency_rates as $rate)
		{
			if($rate["currency"] == $from && $rate["rate"] && $this->_check_curr_date($date, $rate["start_date"],$rate["end_date"]))
			{
				$sum = $sum * $rate["rate"];
				$changed = true;
				continue;
			}
		}

		if(!$changed)//et kui ei saanud vahetuskurssi tulemuse valuuta juurest, siis vaatab teisest
		{
			$currency_rates = (array) $from_obj->meta("rates");
			foreach($currency_rates as $rate)
			{
				if($rate["currency"] == $to && $rate["rate"] && $this->_check_curr_date($date, $rate["start_date"],$rate["end_date"]))
				{
					$sum = $sum/$rate["rate"];
					$changed = true;
					continue;
				}
			}
		}
		return $sum;
	}

	/** returns company currency
		@attrib api=1
	**/
	function get_company_currency()
	{//TODO: ei kuulu siia
		if($this->co_currency)
		{
			return $this->co_currency;
		}
		$u = get_instance(CL_USER);
		$company = obj($u->get_current_company());
		$this->co_currency = $company->prop("currency");
		return $company->prop("currency");
	}

	function _check_curr_date($date , $start , $end)
	{
		$start = (mktime(0, 0, 0, $start["month"], $start["day"], $start["year"]));
		$end = (mktime(0, 0, 0, $end["month"], $end["day"], $end["year"]));
		if($date > $start && $date < $end)
		{
			return true;
		}
		else return false;
	}


	/** returns default currency
		@attrib api=1
	**/
	function get_default_currency_name()
	{
		$c = $this->get_default_currency();
		if(!acl_base::can("" , $c))
		{
			return "EEK";
		}

		$o = obj($c);
		return $o->name();
	}

	/** returns default currency
		@attrib api=1
	**/
	function get_default_currency()
	{
		if($this->default_currency)
		{
			return $this->default_currency;
		}

		if($this->get_company_currency())
		{
			$curr = $this->get_company_currency();
		}
		else
		{
			$cs = new object_list(array(
				"class_id" => CL_CURRENCY
			));
			$c = reset($cs->arr());
			if (!$c)
			{
				return false;
			}
			$curr = $c->id();
		}
		$this->default_currency = $curr;
		return $curr;
	}

	function get($id)
	{
		if (!is_array(aw_global_get("currency_cache")))
		{
			aw_global_set("currency_cache",$this->get_list(RET_ARR));
		}

		$_t = aw_global_get("currency_cache");
		return $_t[$id];
	}

	/** Returns the currency object, given the symbol
		@attrib api=1 params=pos

		@param symbol required type=string
			The currency symbol to find the object for
	**/
	static public function find_by_symbol($symbol)
	{
		if (empty($symbol))
		{
			throw new awex_err_param("The required parameter 1 (symbol) was not given!");
		}
		static $cache;
		if (isset($cache[$symbol]))
		{
			return $cache[$symbol];
		}
		$ol = new object_list(array(
			"class_id" => CL_CURRENCY,
			"name" => $symbol
		));
		if (!$ol->count())
		{
			throw new awex_currency_not_found("The currency you searched for was not found");
		}
		$cur = $ol->begin();
		$cache[$symbol] = $cur;
		return $cur;
	}
}

class awex_err_param extends aw_exception {}
class awex_currency extends aw_exception {}
class awex_currency_not_found extends awex_currency {}
