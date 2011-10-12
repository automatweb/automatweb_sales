<style type="text/css">
.awtab {
font-family: verdana, sans-serif;
font-size: 11px;
font-weight: bold;
color: #1664B9;
background-color: #CDD5D9;
}
.awtab a {color: #1664B9; text-decoration:none;}
.awtab a:hover {color: #000000; text-decoration:none;}

.awtabdis {
font-family: verdana, sans-serif;
font-size: 11px;
font-weight: bold;
color: #686868;
background-color: #CDD5D9;
}

.awtabsel {
font-family: verdana, sans-serif;
font-size: 11px;
font-weight: bold;
color: #FFFFFF;
background-color: #478EB6;
}
.awtabsel a {color: #FFFFFF; text-decoration:none;}
.awtabsel a:hover {color: #000000; text-decoration:none;}

.awtabseltext {
font-family: verdana, sans-serif;
font-size: 11px;
font-weight: bold;
color: #FFFFFF;
background-color: #478EB6;
}
.awtabseltext a {color: #FFFFFF; text-decoration:none;}
.awtabseltext a:hover {color: #000000; text-decoration:none;}

.awtablecellbackdark {
font-family: verdana, sans-serif;
font-size: 10px;
background-color: #478EB6;
}

.awtablecellbacklight {
background-color: #DAE8F0;
}

.awtableobjectid {
font-family: verdana, sans-serif;
font-size: 10px;
text-align: left;
color: #DBE8EE;
background-color: #478EB6;
}


</style>

<form method="GET" action="orb{VAR:ext}" name="searchform">
<!--tabelraam-->

<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr>

<td>

{VAR:toolbar}


<script type="text/javascript">
var redir_targets = new Array();
<!-- SUB: redir_target -->
redir_targets[{VAR:clid}] = '{VAR:url}';
<!-- END SUB: redir_target -->
</script>

<script type="text/javascript">
function refresh_page(arg)
{
	if (!document.searchform.special)
	{
		return false;
	}
	if (!document.searchform.special.checked)
	{
		return false;
	}
	idx = arg.options[arg.selectedIndex].value;
	if (redir_targets[idx])
	{
		window.location = redir_targets[idx];
	};
}

function submit(val)
{
	document.resulttable.subaction.value=val;
	document.resulttable.submit();
}

function mk_group(text)
{
	res = prompt(text);
	if (res)
	{
		document.resulttable.subaction.value = 'mkgroup';
		document.resulttable.grpname.value = res;
		document.resulttable.submit();
	};
}

function assign_config()
{
	document.resulttable.subaction.value='assign_config';
	document.resulttable.action = '{VAR:baseurl}/automatweb/orb{VAR:ext}';
	document.resulttable.submit();
}

var chk_status = true;
function search_selall()
{
	len = document.resulttable.elements.length;
	for (i=0; i < len; i++)
	{
		if (document.resulttable.elements[i].name.indexOf("sel") != -1)
		{
			document.resulttable.elements[i].checked=chk_status;
		}
	}
	chk_status = !chk_status;
}

function selall()
{
	len = document.resulttable.elements.length;
	for (i=0; i < len; i++)
	{
		if (document.resulttable.elements[i].name.indexOf("sel") != -1)
		{
			document.resulttable.elements[i].checked=chk_status;
		}
	}
	chk_status = !chk_status;
}

</script>


<table border=0 cellspacing=1 cellpadding=2>
<!-- SUB: field -->
<tr>
	<td class='chformleftcol' width='160' nowrap>
	{VAR:caption}
	</td>
	<td class='chformrightcol'>
	{VAR:element}
	</td>
</tr>
<!-- END SUB: field -->
<!-- SUB: hidden -->
<tr>
	<td class='chformleftcol' width='160' nowrap>
	hidden
	</td>
	<td class='chformrightcol'>
	{VAR:element}
	</td>
</tr>
<!-- END SUB: hidden -->
<tr>
	<td class='chformleftcol' width='160' nowrap></td>
	<td class='chformrightcol'>
	<input type='submit' value='Otsi'/>
</td>
</tr>

</table>

{VAR:reforb}
</form>
{VAR:table}
{VAR:treforb}
{VAR:ef}
