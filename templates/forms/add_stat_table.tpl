<form action="reforb.{VAR:ext}" method="POST">
<table bgcolor="#CCCCCC" cellpadding=3 cellspacing=1 border=0>
<tr>
	<td class="fform">Nimi:</td>
	<td class="fform"><input type="text" name="name" value="{VAR:name}"></td>
</tr>
<tr>
	<td class="fform">Vali formid:</td>
	<td class="fform"><select name="forms[]" size="10" multiple class="small_button">{VAR:forms}</select></td>
</tr>
<tr>
	<td class="fform" colspan="2"><input type="submit" value="Salvesta"></td>
</tr>
<tr>
	<td class="fform" colspan="2">{VAR:preview}</td>
</tr>
</table>
{VAR:reforb}
</form>