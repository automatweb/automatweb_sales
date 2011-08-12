<?php

class currency_obj extends _int_object
{
	const CLID = 67;

	/**	Returns sum with currency symbol
		@attrib api=1 params=pos
		@param sum optional type=real default=0
			The sum to be formatted.
		@param precision optional type=int default=2	
			The number of decimal places to be shown.
	**/
	public function sum_with_currency($sum = 0, $precision = 2)
	{
		return sprintf("%.{$precision}f %s", $sum, $this->prop("symbol"));
	}
}
