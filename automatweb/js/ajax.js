var httpRequest=null;
function _getConnector()
{
	var o = false;
	if (typeof XMLHttpRequest != 'undefined') 
	{
		o = new XMLHttpRequest();
	} 
	else 
	{
		try 
		{
			o = new ActiveXObject("Msxml2.XMLHTTP");
		} 
		catch (e) 
		{
			try 
			{
				o = new ActiveXObject("Microsoft.XMLHTTP");
			} 
			catch (E) 
			{
				o = false;
			}
		}
	}
	return o;
}


//function sendRequest (elem, suggestionsUrl, params, isTuple)
function sendRequest(url, params, callback)
{
	suggestions = new Array();

	if(httpRequest && httpRequest.readyState != 0)
	{
		// if something is not finished yet, then abort it
		httpRequest.abort();
	}

	httpRequest = _getConnector();

	if (httpRequest)
	{
		var requestParams = '';
		var requestSeparator = '';

		for (i in params)
		{
			paramElem = document.getElementsByName(params[i]);
			paramElem = paramElem.namedItem(params[i]);

			if (typeof(paramElem.name) == "string")
			{
				requestParams += '&' + paramElem.name + '=' + awUriEncode(paramElem.value);
			}
		}

		if (url.indexOf('?') < 0)
		{
			requestSeparator = '?';
		}

		url = url + requestSeparator + requestParams; 

		httpRequest.open("GET", url, true);
		httpRequest.onreadystatechange = function()
		{
			// see asi siin vastuse teksti muidugi ei kontrolli. aga ilmselt peaks
			if (httpRequest.readyState == 4 && httpRequest.responseText)
			{
				evalStr = callback + "(httpRequest.responseText)"; 
				// eval is probably bad. or not?
				eval(evalStr);
				//tmpSuggestions = httpRequest.responseText.split("\n");
				//res = document.getElementById('pingresult');
				//res.innerHTML = httpRequest.responseText;
				//alert(httpRequest.responseText);
			}
		};

		httpRequest.send(null);
	}

	return true;
}


