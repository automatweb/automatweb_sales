<script language="Javascript">
function ask(parent)
{
	name = prompt("Folderi nimi:","");
	if (name)
	{
		url = '{VAR:baseurl}/?class=planner&action=add_todo_menu&parent=' + parent + '&name=' + name;
		window.location.href = url;
	}
};
</script>
	<div class="text"><font color="red">{VAR:status_msg}</font></div>
	<br><br>
	<div class="text">
			<a href="javascript:ask({VAR:parent})">Lisa uus folder</a> |
			<a href="?class=planner&action=add_todo_item&parent={VAR:parent}">Lisa uus task</a>
	</div>
	<br><br>
<table border="0" cellspacing="0" cellpadding="0" bgcolor="#eeeeee" width="100%">
<form method="POST">
<tr>
<td>


	<span class="text"><strong>{VAR:fullpath}</strong></span>
	<table border="0" cellspacing="1" cellpadding="2" bgcolor="#FFFFFF" width="100%">
	<!--
	<tr>
		<td colspan="4" align="center"><strong>Folderid</strong></td>
	</tr>
	-->
	<!-- SUB: folderline -->
	<tr>
		<td colspan="4" class="text"><a href="{VAR:baseurl}/?class=planner&action=todo&parent={VAR:oid}">{VAR:name}</a></td>
	</tr>
	<!-- END SUB: folderline -->
	<tr>
		<td colspan="4" align="center">&nbsp;&nbsp;</td>
	</tr>
	<tr>
		<td colspan="4" align="center" bgcolor="#EEEEEE"><strong>TODO</strong></td>
	</tr>
	<tr>
		<td class="text" align="center"> # </td>
		<td class="text" align="center"> X </td>
		<td class="text" align="center">Pealkiri</td>
		<td class="text" align="center">Sisu</td>
	</tr>
	<!-- SUB: todoline -->
	<tr>
		<td class="text">&nbsp;{VAR:num}</td>
		<td class="text" align="center"><input type="checkbox" name="check[]" value="{VAR:id}"></td>
		<td class="text"><a href="?class=planner&action=edit_todo_item&id={VAR:id}">{VAR:title}</a></td>
		<td class="text">{VAR:content}</td>
	</tr>
	<!-- END SUB: todoline -->
	<tr>
		<td class="text" colspan="4"><select name="folder">
		{VAR:targetlist}
		</select>
		<input type="submit" value=" Liiguta ">
		{VAR:reforb}
		</td>
	</tr>
	</table>
</td>
</tr>
</form>
</table>
