/**
* begin arr()
*/
function arr(arr)
{
	if ($("div#aw_arr_dump").length==0)
	{
		$("body").prepend("<div id='aw_arr_dump'></div>");
	}

	if (typeof arr == "string")
	{
		$("div#aw_arr_dump").append(arr+"<hr>");
	}
	else if (typeof arr == "object")
	{
		if (arr.constructor.toString().indexOf("Array") > 0)
		{
			tmp = "<pre>";
			tmp += aw_arr_get_array(arr, 0);
			tmp += "</pre>";
			$("div#aw_arr_dump").append(tmp+"<hr>");
		}
		else
		{
			tmp = "<pre>";
			tmp += aw_arr_get_obj(arr, 0);
			tmp += "</pre>";
			$("div#aw_arr_dump").append(tmp+"<hr>");
		}
	}
}

// used in arr to get array recursively
function aw_arr_get_array(arr,level)
{
	out = "";
	trepping = "";
	for(i=0;i<level;i++)
	{
		trepping += "        ";
	}
	
	out += "JS Array\n";
	out += trepping+"(\n";
	for (key in arr)
	{
		if (arr[key].constructor.toString().indexOf("Array") > 0)
		{
			out += trepping+"   (array) ["+key+"] => "+aw_arr_get_array(arr[key], level+1)+"\n";
		}
		else
		{
			out += trepping+"   ("+typeof arr[key]+") ["+key+"] => "+arr[key]+"\n";
		}
	}
	trepping_lastline = "";
	for(i=0;i<level;i++)
	{
		trepping_lastline += "        ";
	}

	out += trepping_lastline+")\n";
	return out;
}

// used in arr to get obj
// not recursive it's just way too big of an array to get
function aw_arr_get_obj(arr,level)
{
	out = "";
	trepping = "";
	for(i=0;i<level;i++)
	{
		trepping += "        ";
	}
	
	out += "JS Object\n";
	out += trepping+"(\n";
	for (key in arr)
	{
		out += trepping+"    ("+typeof arr[key]+") ["+key+"] => "+arr[key]+"\n";
	}
	trepping_lastline = "";
	for(i=0;i<level;i++)
	{
		trepping_lastline += "        ";
	}

	out += trepping_lastline+")\n";
	return out;
}
/**
* end arr()
*/
