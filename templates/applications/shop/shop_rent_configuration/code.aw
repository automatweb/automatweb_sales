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
asort($rows);

$cols = array();
foreach(array({VAR:passing_order}) as $k)
{
	$_cols = array();
	if(isset($args[$k]) && is_array($args[$k]))
	{
		foreach($args[$k] as $col)
		{
			foreach(array_merge(array($col), isset($parents[$col]) ? $parents[$col] : array()) as $_col)
			{
				$_cols[$_col] = isset($priorities[$_col]) ? $priorities[$_col] : 0;
			}
		}
		asort($_cols);
		foreach(array_keys($_cols) as $col)
		{
			$cols[$col] = $k;
		}
	}
}

$condition = NULL;
foreach(array_keys($rows) as $row)
{
	foreach(array_keys($cols) as $col)
	{
		switch($args["currency"]."_".$row."_".$col)
		{
			<!-- SUB: HANDLE_CELL -->
			case "{VAR:currency}_{VAR:row}_{VAR:col}":
				<!-- SUB: HANDLE_CELL_ROW -->
				if($args["sum"] <= {VAR:maximum_sum} and $args["sum"] >= {VAR:minimum_sum})
				{
					$condition = {VAR:condition_id};
				}
				<!-- END SUB: HANDLE_CELL_ROW -->
				break;
			<!-- END SUB: HANDLE_CELL -->
		}
	}
}

return $condition;