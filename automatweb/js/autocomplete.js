var last_key = '';
function awActb(obj, valueObj)
{
	/* ---- Public Variables ---- */
	this.actb_timeOut = -1; // Autocomplete Timeout in ms (-1: autocomplete never time out)
	this.actb_lim = 20; // Number of elements autocomplete can show (-1: no limit)
	this.actb_firstText = true; // should the auto complete be limited to the beginning of keyword?
	this.actb_mouse = true; // Enable Mouse Support
	this.actb_delimiter = new Array();  // Delimiter strings for multiple part autocomplete. Set it to empty array for single autocomplete
	this.actb_startcheck = 0; // Show widget only after this number of characters is typed in.
	this.actb_setOptions = actb_setOptions;// Initial options. If valueObj then associative.
	this.actb_setOptionUrl = actb_setOptionUrl;// http URL to retrieve options from. Response expected in JSON format (http://www.json.org/)(classes/protocols/data/aw_json). Response is an array:
	/*
	array(
		"error" => boolean,// recommended
		"errorstring" => error string description,// optional
		"options" => array(value1 => text1, ...),// required
		"limited" => boolean,// whether option count limiting applied or not. applicable only for real time autocomplete.
	)
	*/

	this.actb_setParams = actb_setParams;// Names of form elements whose values will be posted with optionURL. If self form element name included, real time autocomplete options retrieving enabled (i.e. for each key typed, if not in cache).
	this.actb_optionClassName = "awActbOption";
	this.actb_activeOptionClassName = "awActbActiveOption";
	this.actb_matchClassName = "awActbTypedTextMatch";
	this.actb_listClassName = "awActbOptionList";
	this.actb_fontSize = false;// If specified, will override font size css definition for list, but not for option
	/* ---- Public Variables ---- */

	/* ---- Private Variables ---- */
	var actb_options = new Array();
	var actb_limited = true;
	var actb_httpDisplayError = true;
	var actb_optionUrl = false;
	var actb_urlParams = new Array();
	var actb_urlWaitRealtime = 400;// ms
	var actb_delimwords = new Array();
	var actb_cdelimword = 0;
	var actb_delimchar = new Array();
	var actb_display = false;
	var actb_pos = 0;
	var actb_total = 0;
	var actb_curr = null;
	var actb_rangeu = 0;
	var actb_ranged = 0;
	var actb_bool = new Array();
	var actb_pre = 0;
	var actb_toid;
	var actb_tomake = false;
	var actb_getpre = "";
	var actb_mouse_on_list = 0;
	var actb_kwcount = 0;
	var actb_caretmove = false;
	var actb_form = obj.form;
	var actb_input = "";
	var actb_lastKey = "";
	var actb_mode = "";// realtime|static|dynamic|monocarpic
	var actb_optionCache = new awActbOptionCache();
	var actb_valueCache = new Array();
	/* ---- Private Variables---- */

	var actb_self = this;

	var actb_curr = obj;
	var actb_currVal = valueObj;

	function actb_setOptions(options)
	{
		actb_mode = "static";
		actb_options = options;
	}

	function actb_setParams(params)
	{
		actb_mode = "dynamic";

		// determine if real time autocomplete
		if ("object" == typeof actb_currVal)
		{
			elemName = actb_currVal.name;
		}
		else
		{
			elemName = actb_curr.name;
		}

		for (i in params)
		{
			if (params[i] == elemName)
			{
				actb_mode = "realtime";
			}
		}

		actb_urlParams = params;
	}

	function actb_setOptionUrl(url)
	{
		if ("dynamic" != actb_mode && "realtime" != actb_mode)
		{
			actb_mode = "monocarpic";
		}
		actb_optionUrl = url;
	}

	addEvent(actb_curr,"focus",actb_setup);
	function actb_setup()
	{
		if ("dynamic" == actb_mode)
		{
			actb_loadOptions();
		}

		addEvent(document,"keydown",actb_checkkey);
		addEvent(actb_curr,"blur",actb_clear);
		addEvent(document,"keypress",actb_keypress);
	}

	function actb_clear(evt)
	{
		if (actb_mouse_on_list)
		{
			return;
		}

		if (actb_currVal)
		{
			actb_insertValue();
		}

		removeEvent(document,"keydown",actb_checkkey);
		removeEvent(actb_curr,"blur",actb_clear);
		removeEvent(document,"keypress",actb_keypress);
		actb_removedisp();
		actb_httpDisplayError = true;

		if ("dynamic" == actb_mode || "realtime" == actb_mode)
		{
			actb_options = new Array();
		}
	}

	function actb_parse(n)
	{
		if (actb_self.actb_delimiter.length > 0)
		{
			var t = actb_delimwords[actb_cdelimword].trim().addslashes();
			var plen = actb_delimwords[actb_cdelimword].trim().length;
		}
		else
		{
			var t = actb_curr.value.addslashes();
			var plen = actb_curr.value.length;
		}

		var tobuild = '';
		var i;

		if (actb_self.actb_firstText)
		{
			var re = new RegExp("^" + t, "i");
		}
		else
		{
			var re = new RegExp(t, "i");
		}

		var p = n.search(re);

		for (i=0;i<p;i++)
		{
			tobuild += n.substr(i,1);
		}

		tobuild += "<span class='"+(actb_self.actb_matchClassName)+"'>"

		for (i=p;i<plen+p;i++)
		{
			tobuild += n.substr(i,1);
		}

		tobuild += "</span>";

		for (i=plen+p;i<n.length;i++)
		{
			tobuild += n.substr(i,1);
		}

		return tobuild;
	}

	function actb_loadOptions()
	{
		// get param values
		var params = new Array();

		for (i in actb_urlParams)
		{
			params[actb_urlParams[i]] = document.getElementsByName(actb_urlParams[i])[0].value;
		}

		// look in cache
		actb_options = actb_optionCache.getOptions(actb_curr.value, params);

		if ("object" == typeof actb_options)
		{
			return;
		}

		// get options
		try
		{
			actb_getOptions(params);
		}
		catch (e)
		{
			alert(e);
			return;
		}

		// cache options
		actb_optionCache.cacheOptions(actb_options, actb_curr.value, params, actb_limited);
	}

	function actb_getOptions(params)
	{
		actb_options = false;

		// compose options source url
		requestSeparator = '';
		paramSeparator = '&';
		if (actb_optionUrl.indexOf('?') < 0)
		{
			requestSeparator = '?';
			paramSeparator = '';
		}

		if (actb_self.actb_valueElem)
		{
			elemName = actb_currVal.name;
		}
		else
		{
			elemName = actb_curr.name;
		}

		requestParams = paramSeparator + 'requester=' + awUriEncode(elemName);

		for (name in params)
		{
			requestParams += '&' + name + '=' + awUriEncode(params[name]);
		}

		url = actb_optionUrl + requestSeparator + requestParams;

//~ /* dbg */ dbgdiv = document.getElementById("help_layer"); dbgdiv.style.display = "block"; dbgdiv.innerHTML = url;

		// get http response
		var awHttpRequest;

		if(awHttpRequest && awHttpRequest.readyState != 0)
		{
			awHttpRequest.abort();
		}

		try
		{
			awHttpRequest = awGetHttp();
		}
		catch (e)
		{
			throw "Http connection error: " + e;
		}

		awHttpRequest.open("GET", url, true);
		//~ awHttpRequest.setRequestHeader("Content-Type", "application/x-javascript; charset=ISO-8859-15");

		awHttpRequest.onreadystatechange = function()
		{
			if (awHttpRequest.readyState == 4)
			{
				var error = false;

				if (!awHttpRequest.responseText)
				{
					throw "Http connection error: empty response";
				}

				if (awHttpRequest.status < 300 && awHttpRequest.status > 199)
				{
					var tmp = '';
					eval("tmp = " + awHttpRequest.responseText);
					actb_options = tmp["options"];
					actb_limited = tmp["limited"];

					if ("realtime" == actb_mode && actb_lastKey != '')
					{
						actb_tocomplete(actb_lastKey);
					}

					if (tmp["error"])
					{
						error = "Autocomplete: server error. Description: " + tmp["errorstring"] + ". Property: " + actb_curr.name;
					}
				}
				else
				{
					error = "Http request unsuccessful. Status: " + awHttpRequest.status;
				}

				if (error && actb_httpDisplayError)
				{
					alert(error);
					actb_httpDisplayError = false;
				}
			}
		};

		awHttpRequest.send(null);
	}

	function actb_generate()
	{
		if (document.getElementById('tat_table')){ actb_display = false;document.body.removeChild(document.getElementById('tat_table')); }
		if (actb_kwcount == 0){
			actb_display = false;
			return;
		}
		a = document.createElement('table');
		a.className = actb_self.actb_listClassName;
		a.style.position='absolute';

		if (actb_self.actb_fontSize)
		{
			a.style.fontSize = actb_self.actb_fontSize;
		}

		a.style.top = eval(curTop(actb_curr) + actb_curr.offsetHeight) + "px";
		a.style.left = curLeft(actb_curr) + "px";
		a.cellSpacing='0px';
		a.cellPadding='0px';
		a.id = 'tat_table';
		document.body.appendChild(a);
		var i;
		var first = true;
		var j = 1;

		if (actb_self.actb_mouse)
		{
			a.onmouseout = actb_table_unfocus;
			a.onmouseover = actb_table_focus;
		}
		var counter = 0;

		for (i in actb_options)
		{
			if (actb_bool[i])
			{
				counter++;
				r = a.insertRow(-1);
				r.id = 'tat_tr'+(j);
				c = r.insertCell(-1);
				c.className = actb_self.actb_optionClassName;
				c.innerHTML = actb_parse(actb_options[i]);
				c.id = 'tat_td'+(j);

				if (first && !actb_tomake)
				{
					c.className = actb_self.actb_activeOptionClassName;
					first = false;
					actb_pos = counter;
				}
				else if(actb_pre == i)
				{
					c.className = actb_self.actb_activeOptionClassName;
					first = false;
					actb_pos = counter;
				}
				else
				{
					c.className = actb_self.actb_optionClassName;
				}

				c.setAttribute('pos',j);
				if (actb_self.actb_mouse){
					c.style.cursor = 'pointer';
					c.onclick=actb_mouseclick;
					c.onmouseover = actb_table_highlight;
				}
				j++;
			}

			if (j - 1 == actb_self.actb_lim && j < actb_total)
			{
				r = a.insertRow(-1);
				c = r.insertCell(-1);
				c.className = actb_self.actb_optionClassName;
				replaceHTML(c,'\\/');

				if (actb_self.actb_mouse)
				{
					c.style.cursor = 'pointer';
					c.onclick = actb_mouse_down;
				}
				break;
			}
		}
		actb_rangeu = 1;
		actb_ranged = j-1;
		actb_display = true;
		if (actb_pos <= 0) actb_pos = 1;
	}

	function actb_remake()
	{
		document.body.removeChild(document.getElementById('tat_table'));
		a = document.createElement('table');
		a.className = actb_self.actb_listClassName;

		if (actb_self.actb_fontSize)
		{
			a.style.fontSize = actb_self.actb_fontSize;
		}

		a.cellSpacing='0px';
		a.cellPadding='0px';
		a.style.position='absolute';
		a.style.top = eval(curTop(actb_curr) + actb_curr.offsetHeight) + "px";
		a.style.left = curLeft(actb_curr) + "px";
		a.id = 'tat_table';
		if (actb_self.actb_mouse){
			a.onmouseout= actb_table_unfocus;
			a.onmouseover=actb_table_focus;
		}
		document.body.appendChild(a);
		var i;
		var first = true;
		var j = 1;

		if (actb_rangeu > 1)
		{
			r = a.insertRow(-1);
			c = r.insertCell(-1);
			c.className = actb_self.actb_optionClassName;
			replaceHTML(c,'/\\');
			if (actb_self.actb_mouse){
				c.style.cursor = 'pointer';
				c.onclick = actb_mouse_up;
			}
		}

		for (i in actb_options)
		{
			if (actb_bool[i])
			{
				if (j >= actb_rangeu && j <= actb_ranged){
					r = a.insertRow(-1);
					r.id = 'tat_tr'+(j);
					c = r.insertCell(-1);
					c.className = actb_self.actb_optionClassName;
					c.innerHTML = actb_parse(actb_options[i]);
					c.id = 'tat_td'+(j);
					c.setAttribute('pos',j);
					if (actb_self.actb_mouse){
						c.style.cursor = 'pointer';
						c.onclick=actb_mouseclick;
						c.onmouseover = actb_table_highlight;
					}
					j++;
				}else{
					j++;
				}
			}
			if (j > actb_ranged) break;
		}
		if (j-1 < actb_total){
			r = a.insertRow(-1);
			c = r.insertCell(-1);
			c.className = actb_self.actb_optionClassName;
			c.align='center';
			replaceHTML(c,'\\/');
			if (actb_self.actb_mouse){
				c.style.cursor = 'pointer';
				c.onclick = actb_mouse_down;
			}
		}
	}

	function actb_goup()
	{
		if (!actb_display) return;
		if (actb_pos == 1) return;
		document.getElementById('tat_td'+actb_pos).className = actb_self.actb_optionClassName;
		actb_pos--;
		if (actb_pos < actb_rangeu) actb_moveup();
		document.getElementById('tat_td'+actb_pos).className = actb_self.actb_activeOptionClassName;
		if (actb_toid) clearTimeout(actb_toid);
		if (actb_self.actb_timeOut > 0) actb_toid = setTimeout(function(){actb_mouse_on_list=0;actb_removedisp();},actb_self.actb_timeOut);
	}

	function actb_godown()
	{
		if (!actb_display) return;
		if (actb_pos == actb_total) return;
		document.getElementById('tat_td'+actb_pos).className = actb_self.actb_optionClassName;
		actb_pos++;
		if (actb_pos > actb_ranged) actb_movedown();
		document.getElementById('tat_td'+actb_pos).className = actb_self.actb_activeOptionClassName;
		if (actb_toid) clearTimeout(actb_toid);
		if (actb_self.actb_timeOut > 0) actb_toid = setTimeout(function(){actb_mouse_on_list=0;actb_removedisp();},actb_self.actb_timeOut);
	}
	function actb_movedown(){
		actb_rangeu++;
		actb_ranged++;
		actb_remake();
	}
	function actb_moveup(){
		actb_rangeu--;
		actb_ranged--;
		actb_remake();
	}

	/* Mouse */
	function actb_mouse_down()
	{
		document.getElementById('tat_td'+actb_pos).className = actb_self.actb_optionClassName;
		actb_pos++;
		actb_movedown();
		document.getElementById('tat_td'+actb_pos).className = actb_self.actb_activeOptionClassName;
		actb_curr.focus();
		actb_mouse_on_list = 0;
		if (actb_toid) clearTimeout(actb_toid);
		if (actb_self.actb_timeOut > 0) actb_toid = setTimeout(function(){actb_mouse_on_list=0;actb_removedisp();},actb_self.actb_timeOut);
	}

	function actb_mouse_up(evt)
	{
		if (!evt) evt = event;
		if (evt.stopPropagation){
			evt.stopPropagation();
		}else{
			evt.cancelBubble = true;
		}
		document.getElementById('tat_td'+actb_pos).className = actb_self.actb_optionClassName;
		actb_pos--;
		actb_moveup();
		document.getElementById('tat_td'+actb_pos).className = actb_self.actb_activeOptionClassName;
		actb_curr.focus();
		actb_mouse_on_list = 0;
		if (actb_toid) clearTimeout(actb_toid);
		if (actb_self.actb_timeOut > 0) actb_toid = setTimeout(function(){actb_mouse_on_list=0;actb_removedisp();},actb_self.actb_timeOut);
	}

	function actb_mouseclick(evt)
	{
		if (!evt) evt = event;
		if (!actb_display) return;
		actb_mouse_on_list = 0;
		actb_pos = this.getAttribute('pos');
		actb_penter();
		actb_clear();
	}

	function actb_table_focus()
	{
		actb_mouse_on_list = 1;
	}

	function actb_table_unfocus()
	{
		actb_mouse_on_list = 0;
		if (actb_toid) clearTimeout(actb_toid);
		if (actb_self.actb_timeOut > 0) actb_toid = setTimeout(function(){actb_mouse_on_list = 0;actb_removedisp();},actb_self.actb_timeOut);
	}
	function actb_table_highlight(){
		actb_mouse_on_list = 1;
		document.getElementById('tat_td'+actb_pos).className = actb_self.actb_optionClassName;
		actb_pos = this.getAttribute('pos');
		while (actb_pos < actb_rangeu) actb_moveup();
		while (actb_pos > actb_ranged) actb_movedown();
		document.getElementById('tat_td'+actb_pos).className = actb_self.actb_activeOptionClassName;
		if (actb_toid) clearTimeout(actb_toid);
		if (actb_self.actb_timeOut > 0) actb_toid = setTimeout(function(){actb_mouse_on_list = 0;actb_removedisp();},actb_self.actb_timeOut);
	}
	/* ---- */

	function actb_insertword(a)
	{
		if (actb_self.actb_delimiter.length > 0){
			str = '';
			l=0;
			for (i=0;i<actb_delimwords.length;i++){
				if (actb_cdelimword == i){
					prespace = postspace = '';
					gotbreak = false;
					for (j=0;j<actb_delimwords[i].length;++j){
						if (actb_delimwords[i].charAt(j) != ' '){
							gotbreak = true;
							break;
						}
						prespace += ' ';
					}
					for (j=actb_delimwords[i].length-1;j>=0;--j){
						if (actb_delimwords[i].charAt(j) != ' ') break;
						postspace += ' ';
					}
					str += prespace;
					str += a;
					l = str.length;
					if (gotbreak) str += postspace;
				}
				else{
					str += actb_delimwords[i];
				}
				if (i != actb_delimwords.length - 1){
					str += actb_delimchar[i];
				}
			}
			actb_curr.value = str;
			setCaret(actb_curr,l);
		}
		else{
			actb_curr.value = a;
		}
		actb_mouse_on_list = 0;
		actb_removedisp();
	}

	function actb_insertValue()
	{
		if (actb_self.actb_delimiter.length > 0)
		{
			var words = new Array(actb_curr.value);
			var newSplits = null;
			var delimiter = null;
			var value = new Array();

			for (i in actb_self.actb_delimiter)
			{
				newSplits = new Array();

				for (j in words)
				{
					newSplits = newSplits.concat(words[j].split(actb_self.actb_delimiter[i]));
				}

				words = newSplits;
				delimiter = actb_self.actb_delimiter[i];
			}

			for (i in words)
			{
				if ("undefined" == typeof actb_valueCache[words[i]])
				{
					value.push(words[i]);
				}
				else
				{
					value.push(actb_valueCache[words[i]]);
				}
			}

			value = value.join(delimiter);
		}
		else
		{
			if ("undefined" == typeof actb_valueCache[actb_curr.value])
			{
				value = actb_curr.value;
			}
			else
			{
				value = actb_valueCache[actb_curr.value];
			}
		}

		actb_currVal.value = value;
	}

	function actb_penter()
	{
		if (!actb_display) return;
		actb_display = false;
		var word = '';
		var value = '';
		var c = 0;

		for (i in actb_options)
		{
			if (actb_bool[i]) c++;
			if (c == actb_pos){
				word = actb_options[i];
				value = i;
				break;
			}
		}

		actb_insertword(word);
		actb_valueCache[word] = value;
		l = getCaretStart(actb_curr);
	}

	function actb_removedisp()
	{
		//~ if (actb_mouse_on_list==0)
		//~ {
			actb_display = 0;
			if (document.getElementById('tat_table')) document.body.removeChild(document.getElementById('tat_table'));
			if (actb_toid) clearTimeout(actb_toid);
		//~ }
	}

	function actb_keypress(e)
	{
		if (actb_caretmove) stopEvent(e);
		return !actb_caretmove;
	}

	function actb_checkkey(evt)
	{
		if (!evt) evt = event;
		a = evt.keyCode;
		caret_pos_start = getCaretStart(actb_curr);
		actb_caretmove = 0;
		rv = null;

		switch (a)
		{
			case 38:// up arrow
				actb_goup();
				actb_caretmove = 1;
				rv = false;
				break;

			case 40:// down arrow
				actb_godown();
				actb_caretmove = 1;
				rv = false;

				if (!actb_display)
				{
					if ("dynamic" == actb_mode)
					{
						actb_loadOptions();
					}
					actb_lastKey = -1;
				}
				break;

			case 9:// tab
				actb_mouse_on_list = 0;
				actb_removedisp();
				break;

			case 27:
				actb_removedisp();
				break;

			case 8:
				setTimeout(function(){
					if ("dynamic" == actb_mode)
					{
						actb_loadOptions();
					}
					actb_lastKey = -1;
				}, 40);
				return true;

			case 13:// enter
				if (actb_display)
				{
					actb_caretmove = 1;
					actb_penter();
					rv = false;
				}
				else
				{
					rv = true;
				}
				break;

			default:
				if ("realtime" == actb_mode /*&& actb_curr.value.length*/)
				{
					actb_loadOptions();
					actb_lastKey = a;
				}
				else
				{
					actb_lastKey = a;
					setTimeout(function(){actb_tocomplete(a)}, 50);
				}
				break;
		}

		return rv;
	}

	function actb_tocomplete(kc)
	{
		if (kc == 38 || kc == 40 || kc == 13 || kc == 9) return;
		var i;


		if (actb_display)
		{
			var word = 0;
			var c = 0;

			for (var i in actb_options)
			{
				if (actb_bool[i]) c++;

				if (c == actb_pos)
				{
					word = i;
					break;
				}
			}

			actb_pre = word;
		}
		else
		{
			actb_pre = -1;
		}

		if (false && actb_curr.value == '')
		{
			actb_mouse_on_list = 0;
			actb_removedisp();
			return;
		}

		if (actb_self.actb_delimiter.length > 0)
		{
			caret_pos_start = getCaretStart(actb_curr);
			caret_pos_end = getCaretEnd(actb_curr);

			delim_split = '';

			for (i=0;i<actb_self.actb_delimiter.length;i++)
			{
				delim_split += actb_self.actb_delimiter[i];
			}

			delim_split = delim_split.addslashes();
			delim_split_rx = new RegExp("(["+delim_split+"])");
			c = 0;
			actb_delimwords = new Array();
			actb_delimwords[0] = '';

			for (i=0,j=actb_curr.value.length;i<actb_curr.value.length;i++,j--)
			{
				if (actb_curr.value.substr(i,j).search(delim_split_rx) == 0)
				{
					ma = actb_curr.value.substr(i,j).match(delim_split_rx);
					actb_delimchar[c] = ma[1];
					c++;
					actb_delimwords[c] = '';
				}
				else
				{
					actb_delimwords[c] += actb_curr.value.charAt(i);
				}
			}

			var l = 0;
			actb_cdelimword = -1;

			for (i=0;i<actb_delimwords.length;i++)
			{
				if (caret_pos_end >= l && caret_pos_end <= l + actb_delimwords[i].length)
				{
					actb_cdelimword = i;
				}
				l+=actb_delimwords[i].length + 1;
			}
			var ot = actb_delimwords[actb_cdelimword].trim();
			var t = actb_delimwords[actb_cdelimword].addslashes().trim();
		}
		else
		{
			var ot = actb_curr.value;
			var t = actb_curr.value.addslashes();
		}

		if (ot.length == 0)
		{
			actb_mouse_on_list = 0;
			actb_removedisp();
		}

		if (ot.length < actb_self.actb_startcheck) return this;

		if (actb_self.actb_firstText)
		{
			var re = new RegExp("^" + t, "i");
		}
		else
		{
			var re = new RegExp(t, "i");
		}

		actb_total = 0;
		actb_tomake = false;
		actb_kwcount = 0;

		for (i in actb_options)
		{
			actb_bool[i] = false;

			if (re.test(actb_options[i]))
			{
				actb_total++;
				actb_bool[i] = true;
				actb_kwcount++;
				if (actb_pre == i) actb_tomake = true;
			}
		}

		if (actb_toid) clearTimeout(actb_toid);
		if (actb_self.actb_timeOut > 0) actb_toid = setTimeout(function(){actb_mouse_on_list = 0;actb_removedisp();},actb_self.actb_timeOut);
		actb_generate();
	}

	return this;
}

function awActbOptionCache()
{
	this.cacheOptions = function(options, text, params, limited)
	{
	};

	this.getOptions = function(text, params)
	{
		options = Array();
		return false;
		return options;
	};
}
