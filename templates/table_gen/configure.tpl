{VAR:menu}
<table width="100%" border="0" cellpadding="5" cellspacing="0">
<tr><td class="tableborder">

<table border=0 cellpadding=2 bgcolor="#FFFFFF" cellspacing=1>

<tr>
	<td align=center class="aste01">



<form action='reforb{VAR:ext}' method=post>
<table border=0 cellspacing=1 cellpadding=2>
<tr>
<td class="celltext" align="right">Näita aliasi?</td>
<td class="celltext"><input type="checkbox" name="aliases" value="1" {VAR:aliases}></td>
</tr>
<tr>
<td class="celltext" align="right">Viimase muutmise kuupäev?</td>
<td class="celltext"><input type="checkbox" name="last_changed" value="1" {VAR:last_changed}></td>
</tr>
<tr>
<td class="celltext" align="right">{VAR:LC_TABLE_NAME}</td>
<td class="celltext"><input type="text" name="table_name" value="{VAR:table_name}" class="formtext"">
<input type='checkbox' name='show_title' VALUE=1 {VAR:show_title}>
</td>
</tr>
<tr>
<td class="celltext" align="right">{VAR:LC_TABLE_TABLE_HEADER}</td>
<td class="celltext"><textarea name="table_header" cols="50" rows="4" class="formtext">{VAR:table_header}</textarea></td>
</tr>
<tr>
<td class="celltext" align="right">{VAR:LC_TABLE_TABLE_FOOTER}</td>
<td class="celltext"><textarea name="table_footer" cols="50" rows="4" class="formtext">{VAR:table_footer}</textarea></td>
</tr>
<tr>
<td class="celltext" align="right">{VAR:LC_TABLE_CHOOSE_TABLE_STYLE}:</td>
<td class="celltext"><select name='table_style' class="formselect2"><option value=''>{VAR:tablestyle}</select>
</td>
</tr>
<tr>
<td class="celltext" align="right">{VAR:LC_TABLE_CHOOSE_CELL_STYLE}:</td>
<td class="celltext"><select name='default_style' class="formselect2"><option value=''>{VAR:defaultstyle}</select></td>
</tr>
<tr>
<td></td>
<td>
{VAR:reforb}
<input type="submit" value="Salvesta"  class="formbutton">
</td>
</tr>
</table>
</form>

</td>
</tr>
</table>


</td>
</tr>
</table>
<br>