<table border="1" cellpadding="1" cellspacing="1">
<form method="GET" action="orb{VAR:ext}">
<tr>
	<td bgcolor="#DDDDDD" colspan="2">
	<strong>Uus sait...</strong>
	<br>
	<font color="red">
	{VAR:message}
	</font>
	</td>
</tr>
<tr>
	<td colspan="2" bgcolor="#FFFFCC">
	<strong>Virtuaalhosti konfiguratsioon</strong>
	</td>
</tr>
<tr>
	<td>Saidi nimi<br>(näiteks aw.struktuur.ee, ServerName Apache konfis):</td>
	<td><input type="text" name="ServerName" size="30" value="{VAR:ServerName}"></td>
</tr>
<tr>
	<td>SITE_ID<br>(peab olema unikaalne):</td>
	<td><input type="text" name="SITE_ID" size="30" value="{VAR:SITE_ID}"></td>
</tr>
<tr>
	<td>Serveri IP<br>(Default väärtus on {VAR:ServerAddr}:</td>
	<td><input type="text" name="ServerAddr" size="30" value="{VAR:ServerAddr}" disabled></td>
</tr>
<tr>
	<td colspan="2" bgcolor="#FFFFCC">
	<strong>MySQLi konfiguratsioon</strong>
	</td>
<tr>
	<td>Andmebaasi nimi:<br>Näiteks aw. Tohib sisaldada <b>ainult</b> tähti ja numbreid.</td>
	<td><input type="text" name="dbname" size="30" value="{VAR:dbname}"></td>
</tr>
<tr>
	<td>Andmebaasi host:<br>Millises serveris baas käib</td>
	<td><input type="text" name="dbhost" size="30" value="{VAR:dbhost}" disabled></td>
</tr>
<tr>
	<td>Andmebaasi kasutaja:<br>Millise kasutajanimega baasiühendus luuakse</td>
	<td><input type="text" name="dbuser" size="30" value="{VAR:dbuser}"></td>
</tr>
<tr>
	<td>Andmebaasi parool:<br>Millist parooli ühenduse loomisel kasutatakse</td>
	<td><input type="text" name="dbpass" size="30" value="{VAR:dbpass}"></td>
</tr>
<tr>
	<td colspan="2" bgcolor="#FFFFCC">
	<strong>Saidi konfiguratsioon</strong>
	</td>
<tr>
<tr>
	<td>Saidi folder<br>Kataloog, millesse saidi jaoks vajalikud failid kopeeritakse:</td>
	<td>{VAR:docroot}<input type="text" name="folder" size="30" value="{VAR:folder}"></td>
</tr>
<tr>
	<td>Default kasutaja:</td>
	<td><input type="text" name="default_user" size="30" value="{VAR:default_user}"></td>
</tr>
<tr>
	<td>Default parool:</td>
	<td><input type="text" name="default_pass" size="30" value="{VAR:default_pass}"></td>
</tr>
<tr>
	<td valign="top">Saidi tüüp:</td>
	<td>
	<input type="radio" name="type" value="default" checked>
	Default<br>
	<font color="#cccccc">
	<input type="radio" name="type" value="2" disabled>
	Raamidega<br>
	<input type="radio" name="type" value="3" disabled>
	Ultralight (ainult tekst)<br>
	</font>
</td>
</tr>
<tr>
	<td colspan="2" align="center">
		<input type="submit" value="Lisa">
		{VAR:reforb}
	</td>
</tr>
</form>
</table>
