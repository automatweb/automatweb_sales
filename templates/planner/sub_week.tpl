<table border="0" cellspacing="0" cellpadding="0" bgcolor="#dddddd" width="100%">
<tr>
<td>
	<table border="0" cellspacing="1" cellpadding="2" width="100%">
	<tr>
	<!-- SUB: header -->
	<td width="15" class="header1" align="center" bgcolor="{VAR:bgcolor}">
	<a href="?class=planner&action=change&id={VAR:hid}&disp={VAR:disp}&date={VAR:date}">{VAR:head}</a>
	<br>
	{VAR:dateinfo}
	</td>
	<!-- END SUB: header -->
	</tr>
	<tr>
	<!-- SUB: line -->
	<td width="15%" valign="top" bgcolor="{VAR:bgcolor}">
		<small>
		<a href="?class=planner&action=change&id={VAR:did}&date={VAR:date}">{VAR:LC_PLANNER_SHOW_DAY}</a>
		<br>
		<!-- SUB: event -->
		<font color="{VAR:color}">
		<i>{VAR:time}</i><br>
		</font>
		<b><a href="?class=planner&action=editevent&id={VAR:id}&date={VAR:date}"><font color="{VAR:color}">{VAR:title}</font></a></b>
		<p>
		<!-- END SUB: event -->
		</small>
		&nbsp;
	</td>
	<!-- END SUB: line -->
	</tr>
	</table>
</td>
</tr>
</table>
