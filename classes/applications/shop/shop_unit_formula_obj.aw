<?php

class shop_unit_formula_obj extends _int_object
{
	function get_nums_from_formula($code)
	{
		preg_match_all("/([0-9.]{1,})[\s\-\*\+\/\;\(\)\[]{1,}/U", $code, $vars, PREG_PATTERN_ORDER);
		return $vars[1];
	}

	function calc_amount($arr)
	{
		$a = $arr["amount"];
		$o = $arr["obj"];
		$prod = $arr["prod"];
		$cid = $o->prop("complex_formula");
		if($cid && $prod->can("view", $cid))
		{
			$co = obj($cid);
			$code = $co->prop("formula");
			if($set_vals = $o->meta("formula_vars"))
			{
				foreach($set_vals as $var => $val)
				{
					$tmp = str_replace("num_", "" ,$var);
					$old = str_replace("_", ".", $tmp);
					$code = str_replace($old, $val, $code);
				}
			}
			$b = self::eval_code($a, $prod, $code);
		}
		else
		{
			$mod = $o->prop("simple_formula");
			$b = $a * $mod;
		}
		return $b;
	}

	function eval_code($a, $prod, $code)
	{
		eval($code);
		return $b;
	}

	function get_formula($arr)
	{
		$prod = $arr["product"];
		$conn = $prod->connections_from(array(
			"type" => "RELTYPE_UNIT_FORMULA",
		));
		foreach($conn as $c)
		{
			$fo = $c->to();
			if($fo->prop("from_unit") == $arr["from_unit"] && $fo->prop("to_unit") == $arr["to_unit"])
			{
				return $fo;
			}
		}
		$conn = $prod->connections_from(array(
			"type" => "RELTYPE_CATEGORY",
		));
		$cats = array();
		foreach($conn as $c)
		{
			$cat = $c->to();
			$conn2 = $cat->connections_from(array(
				"type" => "RELTYPE_UNIT_FORMULA",
			));
			foreach($conn2 as $c2)
			{
				$fo = $c2->to();
				if($fo->prop("from_unit") == $arr["from_unit"] && $fo->prop("to_unit") == $arr["to_unit"])
				{
					return $fo;
				}
			}
		}
		
		
	}
}

?>
