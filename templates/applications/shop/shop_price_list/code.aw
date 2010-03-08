$prices = $args["prices"];
$bonuses = $args["bonuses"];
$log = array();

$parents = array(
<!-- SUB: PARENTS -->
	"{VAR:id}" => array({VAR:parents}),
<!-- END SUB: PARENTS -->
);

$priorities = array(
<!-- SUB: PRIORITIES -->
	"{VAR:id}" => "{VAR:priority}",
<!-- END SUB: PRIORITIES -->
);

$rows = array(0);
foreach($args["rows"] as $row)
{
	$rows[$row] = isset($priorities[$row]) ? $priorities[$row] : 0;
	if(isset($parents[$row]))
	{
		foreach($parents[$row] as $parent)
		{
			$rows[$parent] = isset($priorities[$parent]) ? $priorities[$parent] : 0;
		}
	}
}

$_cols = array();
foreach(array({VAR:passing_order}) as $k)
{
	$cols = array();
	if(isset($args[$k]) && is_array($args[$k]))
	{
		foreach($args[$k] as $col)
		{
			foreach(array_merge(array($col), isset($parents[$col]) ? $parents[$col] : array()) as $_col)
			{
				$cols[$_col] = isset($priorities[$_col]) ? $priorities[$_col] : 0;
			}
		}
		arsort($cols);
		foreach(array_keys($cols) as $col)
		{
			$_cols[$col] = $k;
		}
	}
}

foreach(array_keys($rows) as $row)
{
	foreach($args["currencies"] as $currency)
	{
		if(!isset($bonuses[$currency]))
		{
			$bonuses[$currency] = 0;
		}

		$done = array();
		foreach($_cols as $col => $type)
		{
			if(!empty($done[$type]) || $type === "default" && !empty($done))
			{
				continue;
			}

			switch($currency."_".$row."_".$col)
			{
				<!-- SUB: HANDLE_CELL -->
				case "{VAR:currency}_{VAR:row}_{VAR:col}":
					<!-- SUB: HANDLE_CELL_ROW_AUTO -->
					<!-- SUB: QUANTITY_CONDITION_START -->
					if(
						<!-- SUB: QUANTITY_CONDITION_FIRST -->
						{VAR:QUANTITY_CONDITION_SINGLE}
						{VAR:QUANTITY_CONDITION_RANGE}
						<!-- END SUB: QUANTITY_CONDITION_FIRST -->
						<!-- SUB: QUANTITY_CONDITION -->
						or
						<!-- SUB: QUANTITY_CONDITION_SINGLE -->
						$args["amount"] == {VAR:quantity}
						<!-- END SUB: QUANTITY_CONDITION_SINGLE -->
						<!-- SUB: QUANTITY_CONDITION_RANGE -->
						$args["amount"] <= {VAR:quantity_to} and $args["amount"] >= {VAR:quantity_from}
						<!-- END SUB: QUANTITY_CONDITION_RANGE -->
						<!-- END SUB: QUANTITY_CONDITION -->
					)
					{
					<!-- END SUB: QUANTITY_CONDITION_START -->
						list($new_price, $new_bonus) = shop_price_list_obj::evaluate_price_list_conditions_auto($prices["{VAR:currency}"], $bonuses["{VAR:currency}"], "{VAR:price_formula}", "{VAR:bonus_formula}");
						$log["{VAR:currency}"][] = array(
							"condition_id" => "{VAR:condition_id}",
							"type" => "{VAR:type}",
							"diff" => array(
								"price" => $new_price - $prices["{VAR:currency}"],
								"bonus" => $new_bonus - $bonuses["{VAR:currency}"],
							),
						);
						$prices["{VAR:currency}"] = $new_price;
						$bonuses["{VAR:currency}"] = $new_bonus;
						$done[$type] = true;
					<!-- SUB: QUANTITY_CONDITION_END -->
					}
					<!-- END SUB: QUANTITY_CONDITION_END -->
					<!-- END SUB: HANDLE_CELL_ROW_AUTO -->
					<!-- SUB: HANDLE_CELL_ROW_CUSTOM -->
			//		{VAR:}::{VAR:}($price, ....);
					<!-- END SUB: HANDLE_CELL_ROW_CUSTOM -->
					break;

				<!-- END SUB: HANDLE_CELL -->	
			}
		}
	}
}

$retval = array();
foreach($args["currencies"] as $currency)
{
	$retval[$currency] = array(
		"price" => array(
			"in" => $args["prices"][$currency],
			"out" => $prices[$currency],
		),
		"bonus" => array(
			"in" => isset($args["bonuses"][$currency]) ? $args["bonuses"][$currency] : 0,
			"out" => isset($bonuses[$currency]) ? $bonuses[$currency] : 0,
		),
		"log" => isset($log[$currency]) ? $log[$currency] : array(),
	);
}
return $retval;