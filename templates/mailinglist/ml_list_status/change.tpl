<script language="javascript">
function Do(what)
{
document.add.action.value=what;
document.add.submit();
};
</script>

<form action='reforb.{VAR:ext}' method=post name="add">
<!--tabelraam-->
{VAR:toolbar}










<table bgcolor="#CCCCCC" cellpadding=3 cellspacing=1 border=0>
	<tr>
		<td class="fform">Nimi:</td><td class="fform"><input type='text' NAME='name' VALUE='{VAR:name}' class="formtext"></td>
	</tr>
	<tr>
		<td class="fform">Listid:</td><td class="fform"><select NAME='lists[]' multiple size="10" class="formselect">{VAR:lists}</select></td>
	</tr>
</table>

									{VAR:res_tbl}

{VAR:reforb}
</form>


