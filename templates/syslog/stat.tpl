<html>
<head>
<title>Statistika</title>
<link rel="stylesheet" href="{VAR:baseurl}/css/site.css">
<script language="Javascript">
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

function ipexplorer(ip)
{
 var windowprops = "toolbar=0,location=1,directories=0,status=0, "+
"menubar=0,scrollbars=1,resizable=1,width=400,height=500";

OpenWindow = window.open("{VAR:baseurl}/ipexplorer.{VAR:ext}?ip=" + ip, "remote", windowprops);
}
function compare()
{
 var wprops = "toolbar=0,location=1,directories=0,status=0,"+
 	"menubar=0,scrollbars=1,resizable=1,width=500,height=300";
	CWindow = window.open("{VAR:baseurl}/orb.{VAR:ext}?class=stat&action=compare","compare",wprops);
}

function show_today()
{
	today = new Date();
	tomorrow = new Date(today.valueOf() + 24 * 3600000);
	document.stat.from.value = format_date(today);
	document.stat.to.value=format_date(tomorrow);
};

function show_thisweek()
{
	today = new Date();
	tomorrow = new Date(today.valueOf() + 24 * 3600000);
	document.stat.from.value = format_date(today);
	document.stat.to.value=format_date(tomorrow);
};

function show_thismonth()
{
	today = new Date();
	tomorrow = new Date(today.valueOf() + 24 * 3600000);
	document.stat.from.value = format_date(today);
	document.stat.to.value=format_date(tomorrow);
};

</script>
</head>
<body bgcolor="#FFFFFF" marginwidth="0" marginheight="0">
<table border="0" cellspacing="1" cellpadding="2" width="100%">
<form name="stat" action="{VAR:baseurl}/orb.{VAR:ext}" method="GET">
<tr>
<td class="fgtitle">
<b>
<a href="{VAR:self}?class=syslog">DR. ONLINE</a> |
<a href="javascript:show_today()">Täna</a> |
<a href="javascript:show_thisweek()">See nädal</a> |
<a href="javascript:show_thismonth()">See kuu</a> |
<a href="#" onClick="javascript:compare()">Võrdle perioode</a>
</b><br>
Alates (pp-kk-aaaa):
<input type="text" size="10" maxlength="10" name="from" value="{VAR:from}">
Kuni (pp-kk-aaaa):
<input type="text" size="10" maxlength="10" name="to" value="{VAR:to}">
<input type="submit" value="Näita">
{VAR:reforb}
</td>
</tr>
<tr>
<td>
<small>
{VAR:parts}
</small>
</td>
</tr>
</form>
</table>

<table border="0" cellspacing="0" cellpadding="2" width="100%">
<tr>
<td valign="top" width="50%">
	{VAR:left}
</td>
<td rowspan=2 width="50%" valign="top">
	{VAR:right}
</td>
</tr>
<tr>
<td valign="top" width="50%">
	{VAR:left1}
</td>
</tr>
</table>
</body>
</html>
