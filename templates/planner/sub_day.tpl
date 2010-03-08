<form method="POST">
<table border="0" cellspacing="0" cellpadding="0" bgcolor="#DDDDDD" width="100%">
<tr>
<td>
	<table border="0" cellspacing="1" cellpadding="2" bgcolor="#FFFFFF" width="100%">
	<tr>
	<td class="header1" align="center" width="20">#</td>
	<td class="header1" align="center" width="100">{VAR:LC_PLANNER_TIME}</td>
	<td class="header1" align="center" width="200">{VAR:LC_PLANNER_TITLE}</td>
	<td class="header1" align="center" width="*">{VAR:LC_PLANNER_CONTENT}</td>
	</tr>
	<!-- SUB: line -->
	<tr>
	<td class="fform" width="20" align="center" valign="top"><input type="checkbox" name="chk[{VAR:id}]" value="1"></td>
	<td class="fform" width="100" valign="top">{VAR:time}</td>
	<td class="fform" width="200" valign="top"><a href="?class=planner&action=editevent&id={VAR:id}"><font color="{VAR:color}">{VAR:title}</font></a></td>
	<td class="fform" width="*"><font color="{VAR:color}">{VAR:contents}</font></td>
	</tr>
	<!-- END SUB: line -->	
	<tr>
	<td class="fform" colspan="4">
		<b>{VAR:total}</b> eventit
	</td>
	</tr>
	</table>
</td>
</tr>
</table>
</form>
