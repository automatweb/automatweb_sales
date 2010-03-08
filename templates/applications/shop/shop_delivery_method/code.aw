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

$valid = {VAR:enabled_by_default};
foreach(array_keys($rows) as $row)
{
	foreach(array_keys($cols) as $col)
	{
		switch ($row."_".$col)
		{
			<!-- SUB: HANDLE_CELL -->
			case "{VAR:row}_{VAR:col}":
				$valid = {VAR:enable};
				break;
			<!-- END SUB: HANDLE_CELL -->
		}
	}
}

return $valid;