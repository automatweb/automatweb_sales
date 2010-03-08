<?php

/** Debug print variable values
	@attrib api=1 params=pos

	@param arr required type=mixed
		The value to output

	@param die optional type=bool
		If set to true, script execution is stopped after outputting the value, defaults to false

	@param see_html optional type=bool
		If set to true, the value displayed is fed through htmlspecialchars, so you can see the html tags in yer browser

	@comment
		Use this to output any value to the user in a pretty way, basically wraps print_r. The value is printed directly to the browser, not returned. Does nothing when in automatweb::MODE_PRODUCTION mode

**/
function arr($arr, $die=false, $see_html=false)
{
	if (isset(automatweb::$instance) and automatweb::MODE_PRODUCTION === automatweb::$instance->mode())
	{
		return;
	}

	echo "<hr/>\n";
	$tmp = '';
	ob_start();
	print_r($arr);
	$tmp = ob_get_contents();
	ob_end_clean();
	echo "<pre style=\"text-align: left;\">\n" . ($see_html ? htmlspecialchars($tmp) : $tmp) . "</pre>\n<hr/>";

	if ($die)
	{
		exit;
	}
}

?>
