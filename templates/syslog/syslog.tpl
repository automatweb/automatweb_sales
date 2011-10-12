<html>
<head>
<title>DR. ONLINE @ {VAR:pstring}</title>
<link rel="stylesheet" href="css/site.css">

<script language=javascript>
// formats a date - pads it with zeros if needed
//	d - object of type Date
function format_date(d) 
{
	t_days = new String(d.getDate());
	t_mon = new String(d.getMonth()+1);
	if (t_days.length == 1)
	{
		t_days = '0' + t_days;
	};
	if (t_mon.length == 1)
	{
		t_mon = '0' + t_mon;
	};
	ret = t_days + "-" + t_mon + "-" + d.getFullYear();
	return ret;
};
		
	
// sets todays range
function show_today()
{
	today = new Date();
	tomorrow = new Date(today.valueOf() + 24 * 3600000);
	document.subb.from.value = format_date(today);
	document.subb.to.value=format_date(tomorrow);
};

// sets yesterdays range
function show_yesterday()
{
	today = new Date();
	yesterday = new Date(today.valueOf() - 24 * 3600000);

	document.subb.from.value=format_date(yesterday);
	document.subb.to.value=format_date(today);
};

// removes range
function show_all()
{
	document.subb.from.value="";
	document.subb.to.value="";
};

// show statistics for this range
function show_stat()
{
	with (document.subb)
	{
		link = "class=stat&action=show&from=" + from.value + "&to=" + to.value;
	};
	document.location = "{VAR:self}?" + link;
};
</script>

<style type="text/css">
.fgtitle {

BACKGROUND: #eeeeee; COLOR: black; FONT-FAMILY: Verdana,Arial,Helvetica,sans-serif; FONT-SIZE: 0.6em; TEXT-DECORATION: none
}

.fgtext {

BACKGROUND: #FFFFFF; COLOR: black; FONT-FAMILY: Arial,Helvetica,sans-serif; FONT-SIZE: 0.6em; FONT-WEIGHT: normal; TEXT-DECORATION: none
}

.fgtext2 {

BACKGROUND: #eeeeee; COLOR: black; FONT-FAMILY: Arial,Helvetica,sans-serif; FONT-SIZE: 0.6em; TEXT-DECORATION: none
 }
 
</style>
</head>
<body bgcolor="#FFFFFF" link="blue" vlink="blue" marginwidth=0 marginheight=0>
<form name='subb' action='{VAR:baseurl}/orb{VAR:ext}' method=GET>
<table border="0" cellspacing="0" cellpadding="0" width=100%>
<tr>
<td bgcolor="#CCCCCC">
<table border=0 cellspacing=1 cellpadding=0 width=100%>
<tr>

<td colspan=6 class="fgtitle">
<b>DR. ONLINE @ {VAR:pstring}<br><a href='javascript:show_all()'>K&otilde;ik</a> |
<a href='javascript:show_yesterday()'>Eilsed</a> |
<a href='javascript:show_today()'>T&auml;nased</a> |
<a href='javascript:document.subb.submit()'>Reload</a> |
<a href='javascript:show_stat()'>Statistika</a> |
<a href='{VAR:self}?class=syslog&action=block'>IP block</a> | 
<a href='{VAR:self}?class=syslog&action=site_id'>Saidid</a> | 
<a href='{VAR:baseurl}'>Esilehele</a> 
</b> Uuendatakse iga
<input type="text" name="update" size="4"  class="plain_el" value="{VAR:update}">
minuti järel. N&auml;idatakse
<input type="text" name="number" size="4" class="plain_el" value="{VAR:number}">
rida</font>
</td>
</tr>

<tr>
<td colspan=6 class="fgtitle">
<font face="Verdana,Arial,Helvetica,sans-serif" size="-1">
<small>
{VAR:parts}
</small>
</font>
</td>
</tr>
<tr>

<td valign=bottom class="fgtitle">
<table border=0 cellpadding=0 cellspacing=0>
<tr>
	<td class="plain">Alates</td>
	<td class="plain">Kuni</td>
</tr>
<tr>
	<td class="plain">
		<input SIZE=10 class="plain_el" type=text name='from' VALUE='{VAR:from}'>
	</td>
	<td class="plain">
		<input SIZE=10 class="plain_el" type=text name='to' VALUE='{VAR:to}'>
	</td>
</tr>
</table>
</td>

<td valign=bottom class="fgtitle">
Kasutaja<br>
<select name='user' class='plain_el'>{VAR:user}</select><br>
</td>
<td valign=bottom class="fgtitle">
Aadress<br>
<input type='text' NAME='ip_addr' VALUE='{VAR:ip_addr}' class='plain_el' size=20>
</td>

<td valign=bottom class="fgtitle">
Kasutaja (c)<br>
<input type='text' NAME='uid_c' CLASS='plain_el' size=12 VALUE='{VAR:uid_c}'>
</td>

<td valign=bottom class="fgtitle">
E-mail (c)<br>
<input type='text' NAME='email_c' CLASS='plain_el' size=15 VALUE='{VAR:email_c}'>
</td>

<td valign=bottom class="fgtitle">
Mida tegi<br>
<input type='text' NAME='act' CLASS='plain_el' size=50 VALUE='{VAR:act}'>
</td>
</tr>

{VAR:LINE}
</table>
</td></tr></table>
{VAR:reforb}
</form>
</body>
</html>
