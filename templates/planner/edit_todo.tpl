{VAR:menu}
<form method="POST">
<table border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
	<td align="center" colspan="2"><strong>{VAR:ftitle}</strong></td>
</tr>
<tr>
	<td>Folder:</td>
	<td><select name="parent">
	{VAR:targetlist}
	</select>
	</td>
</tr>
<tr>
	<td>Pealkiri:</td>
	<td><input type="text" name="title" size="30" value="{VAR:title}">
</tr>
<tr>
	<td valign="top">Sisu:</td>
<td><textarea name="description" cols="40" rows="10">
{VAR:description}
</textarea></td>
</tr>
<tr>
	<td colspan="2" align="center">
		<input type="submit" value=" Salvesta ">
	</td>
</tr>
</table>
{VAR:reforb}
</form>

