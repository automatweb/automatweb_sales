<script language="javascript">
var chk_status = true;

function selall()
{
	len = document.foo.elements.length;
	for (i=0; i < len; i++)
	{
		if (document.foo.elements[i].name.indexOf("sel") != -1)
		{
			document.foo.elements[i].checked=chk_status;
		}
	}
	chk_status = !chk_status;
	return false;
}
</script>
<form method=POST action='reforb{VAR:ext}' name="foo">
<table border=0 cellspacing=1 cellpadding=2 bgcolor="#CCCCCC">
<tr>
	<td class="fform">P&auml;rja nimi:</td>
	<td colspan="4" class="fform"><input type="text" name="name" value="{VAR:name}"></td>
</tr>

<!-- SUB: SEARCH -->
<tr>
	<td colspan="5" class="fform">Otsi objekte:</td>
</tr>
<tr>
	<td class="fform">Otsi nimest:</td>
	<td colspan="4" class="fform"><input type="text" name="s_name" value="{VAR:s_name}"> ID: <input type="text" name="s_id_from" size="4" value="{VAR:s_id_from}"> - <input type="text" name="s_id_to" size="4" value="{VAR:s_id_to}"></td>
</tr>
<tr>
	<td class="fform">Otsi kommentaarist:</td>
	<td colspan="4" class="fform"><input type="text" name="s_comment" value="{VAR:s_comment}"></td>
</tr>
<tr>
	<td class="fform">Mis kataloogi all:</td>
	<td colspan="4" class="fform"><select name="s_parent" class='small_button'>{VAR:s_parent}</select></td>
</tr>
<tr>
	<td class="fform">T&uuml;&uuml;p:</td>
	<td colspan="4" class="fform"><select multiple class="small_button" size="15" name='s_type[]'>{VAR:types}</select><input type='hidden' name='search' value='1'></td>
</tr>
<tr>
	<td class="fform">ID</td>
	<td class="fform">Nimi</td>
	<td class="fform">T&uuml;&uuml;p</td>
	<td class="fform">Asukoht</td>
	<td class="fform"><a href='javascript:void(0)' onClick="selall()">Valitud</a></td>
</tr>
<!-- SUB: S_RESULT -->
<tr>
	<td class="fform">{VAR:oid}</td>
	<td class="fform">{VAR:name}</td>
	<td class="fform">{VAR:type}</td>
	<td class="fform">{VAR:place}</td>
	<td valign="center" class="fform"><input type="checkbox" name="sel[{VAR:oid}]" value="1" {VAR:sel}></td>
</tr>
<!-- END SUB: S_RESULT -->

<!-- END SUB: SEARCH -->
<tr>
	<td class="fform" colspan="5" align="center"><input type="submit" value="Salvesta"></td>
</tr>
</table>
{VAR:reforb}
</form>
