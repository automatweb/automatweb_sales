<form action="orb{VAR:ext}" method="GET" name="aa">
<table>
<tr>
	<td>Vali olemasolev sisestus:</td>
</tr>
<tr>
	<td><select name="ex_entry" onChange="document.aa.submit()">{VAR:entries}</select></td>
</tr>
<tr>
	<td>Tee uus sisestus:</td>
</tr>
<tr>
	<td>{VAR:form}</td>
</tr>
</table>
{VAR:reforb}
</form>