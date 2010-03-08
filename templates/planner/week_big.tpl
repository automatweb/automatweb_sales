<div align="center">
<span class="lefttab">
<a href="/?class=planner&action=show_week&date={VAR:prev}">&lt;&lt;</a>
<big><b>{VAR:LC_PLANNER_WEEKK}</b></big>
<a href="/?class=planner&action=show_week&date={VAR:next}">&gt;&gt;</a>
</span>
</div>
{VAR:navigator}
<table border="0" cellspacing="1" cellpadding="1" width="100%">
<tr>
<!-- SUB: line -->
	<td valign="top" class="lefttab" width="80" bgcolor="{VAR:bgcolor}">
	<table border="0" cellspacing="0" cellpadding="0" width="100%">
	<tr>
		<td bgcolor="#ccffcc" align="center" valign="top" class="lefttab">
		<a href="/?class=planner&date={VAR:date}">{VAR:day2}</a>
		<br>
		<strong>{VAR:wday}</strong>
		</td>
	</tr>
	<tr>
		<td valign="top" class="lefttab">
			{VAR:contents}
		</td>
	</tr>
	</table>
	</td>
<!-- END SUB: line -->
</tr>
</table>
<!-- SUB: event -->
<span class="lefttab">
<i>{VAR:start}-{VAR:end}</i>
<a href="/?class=planner&action=edit_event&id={VAR:id}">{VAR:title}</a>
</span>
<br><br>
<!-- END SUB: event -->
