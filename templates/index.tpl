<html>
<head>
<title>new template</title>
</head>
<body bgcolor="#CCCCCC">
<font face="Verdana,Arial,Helvetica,sans-serif" size="+3">
Tegin yhe uue tyhja template selle saidi jaoks.
</font>
<p>
<!-- SUB: LOGIN -->
<table border="0" cellspacing="1" cellpadding="2">
<tr>
<td>
<font face="Verdana,Arial,Helvetica,sans-serif size="+1">
<form action="{VAR:baseurl}/reforb.{VAR:ext}" method="POST">
UID: <input type="text" name="uid" size="20"><br>
Pass: <input type="password" name="password" size="20"><br>
<input type="submit" value="Log in">
<input type="hidden" name="action" value="login">
<input type="hidden" name="class" value="users">
</form>
</td>
</tr>
</table>
<!-- END SUB: LOGIN -->
<!-- SUB: MENU -->
<table border="0" cellspacing="1" cellpadding="2">
<tr>
<td>
<font face="Verdana,Arial,Helvetica,sans-serif size="+1">
<b>{VAR:uid}</b><br>
<a href="{VAR:baseurl}/automatweb">Tee tööd</a><br>
<a href="{VAR:baseurl}/orb.{VAR:ext}?class=syslog">DR Online</a><br>
<a href="{VAR:baseurl}/orb.{VAR:ext}?class=users&action=logout"><br>
</td>
</tr>
</table>
<!-- END SUB: MENU -->
<p>
<font face="Verdana,Arial,Helvetica,sans-serif" size="-1">
<small>{VAR:qcount} | {VAR:qtime} | {VAR:tpl_load} | {VAR:tpl_parse}</small>
</font>
</body>
</html>
