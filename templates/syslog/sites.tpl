<html>
<head>
<title>Saidid</title>
<style>
.header {
	font-family: Verdana;
	font-size: 12px;
	background: #FFCCAA;
}
.line {
	font-family: Verdana;
	font-size: 12px;
};
</style>
</head>
<body bgcolor="#FFFFFF">
Vali sait, mille logi Dr. Online näitab<br>
<a href="orb.{VAR:ext}?class=syslog">tagasi</a>
<p>
<table border="0" cellspacing="0" cellpadding="0" bgcolor="#CCCCCC">
<tr>
<td>
	<form action="reforb.{VAR:ext}" method="POST">
	<table border="1" cellspacing="2" cellpadding="2" bgcolor="#FFFFFF">
	<tr>
		<td colspan="3" class="header">Saidid</td>
	</tr>
	<tr>
		<td class="line" align="center"><strong>Saidi ID</strong></center></td>
		<td class="line" align="center"><strong>Nimi</strong></center></td>
		<td class="line" align="center"><strong>Aktiivne</strong></center></td>
	</tr>
	<!-- SUB: line -->
	<tr>
		<td class="line">{VAR:id}&nbsp;</td>
		<td class="line" align="center"><input type="text" name="name[{VAR:id}]" value="{VAR:name}" size="40"></td>
		<td class="line" align="center"><input type="radio" name="syslog_site_id" value="{VAR:id}" {VAR:active}></td>
	</tr>
	<!-- END SUB: line -->
	<tr>
		<td class="line">&nbsp;</td>
		<td class="line" align="center"><input type="text" name="name[-1]" value="K&otilde;ik" size="40"></td>
		<td class="line" align="center"><input type="radio" name="syslog_site_id" value="-1" {VAR:active}></td>
	</tr>
	<tr>
		<td class="line" colspan="3" align="center">
		<input type="submit" value="Salvesta">
		{VAR:reforb}
		</td>
	</tr>
	</table>
	</form>
</td>
</tr>
</table>
</body>
</html>

