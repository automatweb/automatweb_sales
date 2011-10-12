<html>
<head>
<title>Perioodide võrdlemine</title>
<link rel="stylesheet" href="css/site.css">
</head>
<body bgcolor="#FFFFFF">
<table border="0" cellspacing="0" cellpadding="2" width="100%" bgcolor="#CCCCCC">
<form method="GET" name="cform" action="orb{VAR:ext}">
<tr>
<td>
	<table border="0" cellspacing="1" cellpadding="2" width="100%" bgcolor="#CCCCCC">
	<tr>
	<td colspan="3" class="fgtitle"><b><a href="javascript:document.cform.submit()">Näita graafikut</a></b></a>
	</tr>
	<tr>
	<td colspan="3" class="fgtext">Periood: <b>Päevad</b><input type="radio" name="period" checked></td>
	</tr>
	<tr>
	<td class="fgtitle">Päev (pp-kk-aaaa)</td>
	<td colspan="2" align="right" class="fgtitle">Värv</td>
	</tr>
	<!-- SUB: line -->
	<tr>
	<td class="fgtext">
	<input type="text" name="day[{VAR:cnt}]" size="10" maxlength="10" value="{VAR:day}">
	</td>
	<td bgcolor="{VAR:color}">
	&nbsp;
	</td>
	<td class="fgtext">
	<input type="text" name="color[{VAR:cnt}]" size="7" maxlength="7" value="{VAR:color}">
	</td>
	</tr>
	<!-- END SUB: line -->
	</table>
</td>
</tr>
{VAR:reforb}
</form>
</table>
<!-- SUB: graph -->
Graafik:<br>
<img src="{VAR:self}?class=stat&action=cgraph">
<!-- END SUB: graph -->
</body>
</html>
