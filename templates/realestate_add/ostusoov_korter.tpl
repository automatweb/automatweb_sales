<script language="javascript">

function wait(delay){
	setTimeout("upd_vald_list({VAR:div3});",delay);
}			
			function upd_proj_list(division)
			{
				      aw_do_xmlhttprequest("{VAR:url}&admin_structure_id={VAR:admin_structure_id}&site=1&division="+division+"&parent="+document.kinnisvara.county.options[document.kinnisvara.county.selectedIndex].value, proj_fetch_callb);
			}

			function proj_fetch_callb()
			{
				if (req.readyState == 4)
				{
					// only if "OK"
					if (req.status == 200) 
					{
						response = req.responseXML.documentElement;
						items = response.getElementsByTagName("item");
						aw_clear_list(document.kinnisvara.city);
						aw_add_list_el(document.kinnisvara.city, "", "--vali--");

						for(i = 0; i < items.length; i++)
						{
							value = items[i].childNodes[0].firstChild.data;
							text = items[i].childNodes[1].firstChild.data;
							aw_add_list_el(document.kinnisvara.city, value, text);
						}
					} 
					else 
					{
						alert("There was a problem retrieving the XML data:\n" + req.statusText);
					}
				}
			}

			function upd_vald_list(division)
			{
				aw_do_xmlhttprequest("{VAR:url}&site=1&admin_structure_id={VAR:admin_structure_id}&division="+division+"&parent="+document.kinnisvara.county.options[document.kinnisvara.county.selectedIndex].value, proj_fetch_callb_vald);
			}

			function proj_fetch_callb_vald()
			{
				if (req.readyState == 4)
				{
					// only if "OK"
					if (req.status == 200) 
					{
						response = req.responseXML.documentElement;
						items = response.getElementsByTagName("item");
						aw_clear_list(document.kinnisvara.vald);
						aw_add_list_el(document.kinnisvara.vald, "", "--vali--");

						for(i = 0; i < items.length; i++)
						{
							value = items[i].childNodes[0].firstChild.data;
							text = items[i].childNodes[1].firstChild.data;
							aw_add_list_el(document.kinnisvara.vald, value, text);
						}
					} 
					else 
					{
						alert("There was a problem retrieving the XML data:\n" + req.statusText);
					}
				}
			}
			
			function upd_citypart_list(division)
			{
				aw_do_xmlhttprequest("{VAR:url}&site=1&admin_structure_id={VAR:admin_structure_id}&division="+division+"&parent="+document.kinnisvara.city.options[document.kinnisvara.city.selectedIndex].value, proj_fetch_callb_citypart);
			}

			function proj_fetch_callb_citypart()
			{
				if (req.readyState == 4)
				{
					// only if "OK"
					if (req.status == 200) 
					{
						response = req.responseXML.documentElement;
						items = response.getElementsByTagName("item");
						aw_clear_list(document.kinnisvara.citypart);
						aw_add_list_el(document.kinnisvara.citypart, "", "--vali--");

						for(i = 0; i < items.length; i++)
						{
							value = items[i].childNodes[0].firstChild.data;
							text = items[i].childNodes[1].firstChild.data;
							aw_add_list_el(document.kinnisvara.citypart, value, text);
						}
					} 
					else 
					{
						alert("There was a problem retrieving the XML data:\n" + req.statusText);
					}
				}
			}

			function upd_settlement_list(division)
			{
				
				      aw_do_xmlhttprequest("{VAR:url}&admin_structure_id={VAR:admin_structure_id}&site=1&division="+division+"&parent="+document.kinnisvara.vald.options[document.kinnisvara.vald.selectedIndex].value, proj_fetch_callb_settlement);
			}

			function proj_fetch_callb_settlement()
			{
				if (req.readyState == 4)
				{
					// only if "OK"
					if (req.status == 200) 
					{
						response = req.responseXML.documentElement;
						items = response.getElementsByTagName("item");
						aw_clear_list(document.kinnisvara.settlement);
						aw_add_list_el(document.kinnisvara.settlement, "", "--vali--");

						for(i = 0; i < items.length; i++)
						{
							value = items[i].childNodes[0].firstChild.data;
							text = items[i].childNodes[1].firstChild.data;
							aw_add_list_el(document.kinnisvara.settlement, value, text);
						}
					} 
					else 
					{
						alert("There was a problem retrieving the XML data:\n" + req.statusText);
					}
				}
			}


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
        if (el.name.indexOf(name) != -1)
		{
			return el;
		}
	}
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
function set_cookie(name,value)
{
        document.cookie = name+"="+value;
}


// gets the value of a cookie
function get_cookie(name)
{
        if (document.cookie.length > 0)
        {
                // we can have multiple cookies on a domain
                begin = document.cookie.indexOf(name+"=");
                if (begin != -1)
                {
                        begin += name.length+1;
                        end = document.cookie.indexOf(";", begin);
                        if (end == -1) end = document.cookie.length;
                        return document.cookie.substring(begin, end);
                }
        }
        else
        {
                return -1;
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

var chk_status;

function aw_sel_chb(form,elname)
{
	chs = !chk_status;

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

function aw_do_xmlhttprequest(url, finish_callb)
{
	if (window.XMLHttpRequest) 
	{
        req = new XMLHttpRequest();
        req.onreadystatechange = finish_callb;
        req.open("GET", url, true);
        req.send(null);
	} 
	else 
	if (window.ActiveXObject) 
	{
		req = new ActiveXObject("Microsoft.XMLHTTP");
		if (req) 
		{
            req.onreadystatechange = finish_callb;
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
</script>

<form name = "kinnisvara">
<div class="tabs">
<!-- SUB: ACT_LEVEL -->
<a class="akt" href="{VAR:level_url}">{VAR:level_name}</a>
<!-- END SUB: ACT_LEVEL -->
<!-- SUB: LEVEL -->
<span class="pass">{VAR:level_name}</span>
<!-- END SUB: LEVEL -->
</div>
<span class="apk">Asukoht</span>
<table>

<tr><td>Maakond</td>
<td>
<select name="county" id="county" onChange="upd_proj_list({VAR:div1});wait(1500);">
<option  value="">--vali--</option>
<!-- SUB: county -->
<option  value="{VAR:division_id}" {VAR:selected}>{VAR:division}</option>
<!-- END SUB: county -->
</select>
</td></tr>

<tr><td>Linn</td><td>
<select name="city" id="city" onChange="upd_citypart_list({VAR:div2});"><option  value="">--vali--</option>
<!-- SUB: city -->
<option  value="{VAR:division_id}" {VAR:selected}>{VAR:division}</option>
<!-- END SUB: city -->
</select>
</td></tr>

<tr><td>Linnaosa</td><td>
<select name="citypart" id="citypart"><option  value="">--vali--</option>
<!-- SUB: citypart -->
<option  value="{VAR:division_id}" {VAR:selected}>{VAR:division}</option>
<!-- END SUB: citypart -->
</select>
</td></tr>

<tr><td>Vald</td><td>
<select name="vald" id="vald" onChange="upd_settlement_list({VAR:div4});">><option  value="">--vali--</option>
<!-- SUB: vald -->
<option  value="{VAR:division_id}" {VAR:selected}>{VAR:division}</option>
<!-- END SUB: vald -->
</select>
</td></tr>

<tr><td>Asula</td><td>
<select name="settlement" id="settlement"><option  value="">--vali--</option>
<!-- SUB: settlement -->
<option  value="{VAR:division_id}" {VAR:selected}>{VAR:division}</option>
<!-- END SUB: settlement -->
</select>
</td></tr>

<tr><td>Kohanimi</td><td><input type="text" name="place_name" value="{VAR:place_name_value}" /></td></tr>

<tr><td>Tänav</td><td><input type="text" name="street" value="{VAR:street_value}" /></td></tr>

<tr><td style="height:0;width:100px;"></td><td></td></tr>
</table>
<span class="apk">Kirjeldus</span>
<table>

<tr>
	<td colspan="2">
		<textarea id='additional_info_et' name='additional_info_et' cols='45' rows='5' wrap='soft' >{VAR:additional_info_et_value}</textarea>
	</td>
</tr>
<tr><td style="height:0;width:100px;"></td><td></td></tr>

<tr><td colspan="2">
	<input type="hidden" name='transaction_type' value='300' />
	<input type='hidden' id='is_visible' name='is_visible' value='0' />
	<input type="image" src="/img/lk/nupp_saada.gif">
</td></tr>
</table>

{VAR:reforb}

</form>
