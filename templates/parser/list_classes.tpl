<script language='javascript'>
var chk = 1;
function sel_all()
{
	for (i=0; i < document.boo.elements.length; i++)
	{
		if (document.boo.elements[i].type == "checkbox")
		{
			document.boo.elements[i].checked = chk;
		}
	}
	chk = !chk;
}
</script>
<form name='boo' action="reforb.{VAR:ext}" method="POST">
<table border=1>
<tr>
	<td>Nimi</td>
	<td>Kas parsitud</td>
	<td><a href='javascript:void(0)' onClick="sel_all()">Vali</a></td>
</tr>
<!-- SUB: LINE -->
<tr>
	<td>{VAR:name}</td>
	<td>{VAR:parsed}</td>
	<td><input type="checkbox" name="parse[]" VALUE="{VAR:name}" {VAR:checked}></td>
</tr>
<!-- END SUB: LINE -->
<tr>
	<td colspan="3"><input type='radio' name='paction' value='remove_enter_func' {VAR:rm_check}> V&otilde;ta enter/exit_function callid 2ra </td>
</tr>
<tr>
	<td colspan="3"><input type='radio' name='paction' value='add_enter_func' {VAR:add_check}> Lisa enter/exit_function callid </td>
</tr>
<tr>
	<td colspan="3"><input type='radio' name='paction' value='list_funcs' {VAR:list_check}> N&auml;ita nimekirja klassidest ja funktsioonidest</td>
</tr>
<tr>
	<td colspan="3"><input type="submit" value="Salvesta"></td>
</tr>
</table>
{VAR:reforb}
</form>