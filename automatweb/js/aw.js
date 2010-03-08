// AW Javascript functions

// see on nö "core" funktsioon popuppide kuvamiseks. Interface juures on soovitav kasutada
// jargnenaid funktsioone

/*
window.onerror = js_error_dbg

function js_error_dbg(msg, url, line)
{
	return aw_get_url_contents('http://tarvo.dev.struktuur.ee/automatweb/orb.aw?class=file&action=handle_remote_dbg&type=JS-FATAL-ERROR&url=' + Url.encode(url) +'&line=' + line + '&msg=' + msg);
}

function f(msg)
{
	
	return aw_get_url_contents('http://tarvo.dev.struktuur.ee/automatweb/orb.aw?class=file&action=handle_remote_dbg&type=JS-DEBUG&dbg=' + msg);
}
*/

function _aw_popup(file,name,toolbar,location,status,menubar,scrollbars,resizable,width,height)
{
	 var wprops = 	"toolbar=" + toolbar + "," + 
	 		"location= " + location + "," +
			"directories=0," + 
			"status=" + status + "," +
	        	"menubar=" + menubar + "," +
			"scrollbars=" + scrollbars + "," +
			"resizable=" + resizable + "," +
			"width=" + width + "," +
			"height=" + height;

	openwindow = window.open(file,name,wprops);
};

function aw_popup(file,name,width,height)
{
	_aw_popup(file,name,0,1,0,0,0,1,width,height);
}

function aw_popup_s(file,name,width,height)
{
	_aw_popup(file,name,0,0,0,0,0,0,width,height);
};

function aw_popup_scroll(file,name,width,height)
{
	_aw_popup(file,name,0,0,0,0,1,1,width,height);
};

function aw_get_el(name,form)
{
    if (!form)
	{
        form = document.changeform;
	}
    for(i = 0; i < form.elements.length; i++)
	{
        el = form.elements[i];
        if (el.name.indexOf(name) == 0)
		{
			return el;
		}
	}
	// here's a fix for IE because in search (class) names are removed from select boxes
	if ($.gup("class")=="aw_object_search")
	{
	    	return $("select", form).each(function(){
			if (this.name_tmp == name)
			{
				this.name = this.name_tmp;
				return this;
			}
		});
	};
}

function list_preset(el,oid)
{
	var i = 1;
	elem = el + '_' + i;
	while(it = document.getElementById(elem))
	{
		it.style.color='blue';
	
		i+=1;
		elem = el + '_' + i;
	}
	document.getElementById(el).value=oid;
}

// set/changes cookie 
function set_cookie( name, value, expires, path, domain, secure )
{
	// set time, it's in milliseconds
	var today = new Date();
	today.setTime( today.getTime() );
	
	/*
	if the expires variable is set, make the correct
	expires time, the current script below will set
	it for x number of days, to make it for hours,
	delete * 24, for minutes, delete * 60 * 24
	*/
	if ( expires )
	{
	expires = expires * 1000 * 60 * 60 * 24;
	}
	var expires_date = new Date( today.getTime() + (expires) );
	
	document.cookie = name + "=" +escape( value ) +
	( ( expires ) ? ";expires=" + expires_date.toGMTString() : "" ) +
	( ( path ) ? ";path=" + path : "" ) +
	( ( domain ) ? ";domain=" + domain : "" ) +
	( ( secure ) ? ";secure" : "" );
}

// gets the value of a cookie
// this fixes an issue with the old method, ambiguous values
// with this test document.cookie.indexOf( name + "=" );
function get_cookie ( check_name )
{	
	// first we'll split this cookie up into name/value pairs
	// note: document.cookie only returns name=value, not the other components
	var a_all_cookies = document.cookie.split( ';' );
	var a_temp_cookie = '';
	var cookie_name = '';
	var cookie_value = '';
	var b_cookie_found = false; // set boolean t/f default f

	for ( i = 0; i < a_all_cookies.length; i++ )
	{
		// now we'll split apart each name=value pair
		a_temp_cookie = a_all_cookies[i].split( '=' );


		// and trim left/right whitespace while we're at it
		cookie_name = a_temp_cookie[0].replace(/^\s+|\s+$/g, '');

		// if the extracted name matches passed check_name
		if ( cookie_name == check_name )
		{
			b_cookie_found = true;
			// we need to handle case where cookie has no value but exists (no = sign, that is):
			if ( a_temp_cookie.length > 1 )
			{
				cookie_value = unescape( a_temp_cookie[1].replace(/^\s+|\s+$/g, '') );
			}
			// note that in cases where cookie is initialized but no value, null is returned
			return cookie_value;
			break;
		}
		a_temp_cookie = null;
		cookie_name = '';
	}
	if ( !b_cookie_found )
	{
		return null;
	}
}

// checks whether value exists in arr
function aw_in_array(value,arr)
{
	for (i = 0; i < arr.length; i++)
		if (arr[i] == value)
			return 1;
	return 0;
}

// removes value from array, returns the new array
function aw_remove_arr_el(value,arr)
{
	rv = new Array();
	for (i = 0; i < arr.length; i++)
		if (arr[i] != value)
			rv.push(arr[i]);
	return rv;
};

function awlib_addevent(o,e,f)
{
	if(o.addEventListener)
		o.addEventListener(e,f,true);

	else if(o.attachEvent)
		o.attachEvent("on"+e,f);
	
	else
		eval("o.on"+e+"="+f)
};

var chk_status_nms = new Array();
var chk_status_sets = new Array();
var chk_status = 0;

function aw_sel_chb(form,elname)
{
	found = false;
	for(i = 0; i < chk_status_nms.length;i++)
	{
		if (chk_status_nms[i] == elname)
		{
			found = true;
			break;
		}
	}
	if (!found)
	{
		chk_status_nms.length++;
		chk_status_nms[chk_status_nms.length-1] = elname;
		i = chk_status_nms.length-1;
	}
	chs = !chk_status_sets[i];
	chk_status_sets[i] = chs ? true : false;

	len = form.elements.length;
	for(i = 0; i < len; i++)
	{
		if (form.elements[i].name.indexOf(elname) != -1)
		{
			form.elements[i].checked = chs;
		}
	}

	chk_status  = chk_status ? 0 : 1;
}


function aw_date_edit_clear(name)
{
    for(i = 0; i < document.changeform.elements.length; i++)
	{
        el = document.changeform.elements[i];
        if (el.name.indexOf(name) != -1)
		{
			if (el.options)
			{
				el.selectedIndex = 0;
			}
			else
			{
				el.value = '';
			}
		}
	}
} 

var cur_cal_el = null;

function aw_date_edit_show_cal(elname)
{
	cur_cal_el = elname;

	var cal16 = new CalendarPopup();
	cal16.setMonthNames("Jaanuar","Veebruar","M&auml;rts","Aprill","Mai","Juuni","Juuli","August","September","Oktoober","November","Detsember");
	cal16.setMonthAbbreviations("Jan","Veb","Mar","Apr","Mai","Jun","Jul","Aug","Sept","Okt","Nov","Dets");
	cal16.setDayHeaders("P","E","T","K","N","R","L");
	cal16.setWeekStartDay(1); // week is Monday - Sunday
	cal16.setTodayText("T&auml;na");
	cal16.showYearNavigation();

	var y_obj = aw_get_el(elname+"[year]");
	var m_obj = aw_get_el(elname+"[month]");
	var d_obj = aw_get_el(elname+"[day]");

	if (y_obj.value > 0)
	{
		var y = y_obj.value;
	}
	else
    if (y_obj.options && y_obj.selectedIndex > -1)
	{
		var y = y_obj.options[y_obj.selectedIndex].value;
	}


	if (m_obj.value > 0)
	{
		var m = m_obj.value;
	}
	else
    if (m_obj.options && m_obj.selectedIndex > -1)
	{
		var m = m_obj.options[m_obj.selectedIndex].value;
	}

	if (d_obj.value > 0)
	{
		var d = d_obj.value;
	}
	else
    if (d_obj.options && d_obj.selectedIndex > -1)
	{
		var d = d_obj.options[d_obj.selectedIndex].value;
	}

	if (d=="") 
	{ 
		d=1; 
	}
	if (y=="---" || m=="---" || y == undefined || m == undefined || y == "" || m == "") 
	{ 
		dt = null; 
	}
	else
	{
		dt = y+'-'+m+'-'+d;
	}
	cal16.setReturnFunction("aw_date_edit_set_val");
	cal16.showCalendar(elname,dt); 
}

function aw_set_lb_val(el, val)
{
    if (!el.options)
	{
	    return;
	}
	for(i = 0;  i < el.options.length; i++)
	{
		if (el.options[i].value == val)
		{
			el.selectedIndex = i;
			return;
		}
	}
}

function aw_date_edit_set_val(y,m,d)
{
	var y_el = aw_get_el(cur_cal_el+"[year]");
	aw_set_lb_val(y_el, y);
	y_el.value = y;

	var m_el = aw_get_el(cur_cal_el+"[month]");
	aw_set_lb_val(m_el, m);
	m_el.value = m;

	var d_el = aw_get_el(cur_cal_el+"[day]");
	aw_set_lb_val(d_el, d);
	d_el.value = d;
}

function aw_get_url_contents(url)
{
	var req;
	if (window.XMLHttpRequest) 
	{
		req = new XMLHttpRequest();
		req.open('GET', url, false);
		req.send(null);
	} 
	else 
	if (window.ActiveXObject) 
	{
		req = new ActiveXObject('Microsoft.XMLHTTP');
		if (req) 
		{
			req.open('GET', url, false);
			req.send();
		}
	}
	return req.responseText;
}

function aw_post_url_contents(url, params)
{
	var req;
	if(window.XMLHttpRequest)
	{
		req = new XMLHttpRequest();
		req.overrideMimeType('text/html');
		req.open('POST', url, false);
		req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		req.setRequestHeader("Content-length", params.length);
		req.setRequestHeader("Connection", "close");
		req.send(params);
	}
	else
	if(window.ActiveXObject)
	{
		req = new ActiveXObject('Microsoft.XMLHTTP');
		if(req)
		{
			req.overrideMimeType('text/html');
			req.open('POST', url, false);
			req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			req.setRequestHeader("Content-length", params.length);
			req.setRequestHeader("Connection", "close");
			req.send(params);
		}
	}
	return req.responseText;
}

var aw_xmlhttpr_cb;

function aw_handle_xml_data()
{
	if (req.readyState == 4)
	{
		// only if "OK"
		if (req.status == 200 && aw_xmlhttpr_cb) 
		{
			aw_xmlhttpr_cb();
		}
	}
}

function aw_do_xmlhttprequest(url, finish_callb)
{
	aw_xmlhttpr_cb = finish_callb;
	if (window.XMLHttpRequest) 
	{
        req = new XMLHttpRequest();
        req.onreadystatechange = aw_handle_xml_data;
        req.open("GET", url, true);
        req.send(null);
	} 
	else 
	if (window.ActiveXObject) 
	{
		req = new ActiveXObject("Microsoft.XMLHTTP");
		if (req) 
		{
            req.onreadystatechange = aw_handle_xml_data;
			req.open("GET", url, true);
			req.send();
		}
	}
}


function aw_clear_list(list)
{
	var listlen = list.length;

	for(i=0; i < listlen; i++)
		list.options[0] = null;
}

function aw_add_list_el(list, value, text)
{
	list.options[list.options.length] = new Option(text,""+value,false,false);
}

var aw_timers = new Array();
function aw_timer(timer)
{
	if(aw_timers[timer])
	{
		tmp = aw_timers[timer];
		aw_timers[timer] = false;
		return (new Date().getTime()) - tmp;
	}
	else
	{
		aw_timers[timer] = new Date().getTime();
		return true;
	}
}

/* best flash embed there is */
function f(url, w, h, wmode)
{
	document.writeln('<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" style="overflow:hidden; width:'+w+'px; height:'+h+'px;" width="'+w+'" height="'+h+'">');
	document.writeln('<param name="movie" value="'+url+'" />');
	document.writeln('<param name="quality" value="high" />');
	document.writeln('<param name="name" value="movie" />');
	document.writeln('<param name="swLiveConnect" value="true" />');
	if(typeof(wmode) != 'undefined')
	{
		document.writeln('<param name="wmode" value="transparent" />');
		document.writeln('<embed src="'+url+'" wmode="transparent" style="overflow:hidden; width:'+w+'px; height:'+h+'px;" quality="high" scale="noscale" width="'+w+'" height="'+h+'" align="middle" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />');
	}
	else
	{
		document.writeln('<embed src="'+url+'" quality="high" scale="noscale" width="'+w+'" height="'+h+'" align="middle" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />');
	}
	document.writeln('</object>');
}