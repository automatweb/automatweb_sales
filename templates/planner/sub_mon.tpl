<table border="0" cellspacing="0" cellpadding="0" bgcolor="#DDDDDD" width="100%">
<tr>
<td>
	<table border="0" cellspacing="1" cellpadding="2" bgcolor="#FFFFFF" width="99%">
	<tr>
	<!-- SUB: header -->
		<td class="fgtitle" align="center">
			<b>{VAR:headline}</b>
		</td>
	<!-- END SUB: header -->
	</tr>
	<!-- SUB: line -->
	<tr>
		<!-- SUB: subline -->
		<td bgcolor="{VAR:bgcolor}" valign="top">
			<small>
			<center><b><a href="?class=planner&action=change&disp=month&id={VAR:did}&date={VAR:date}">{VAR:dayname}</a></b>
			<br>
			<!-- SUB: showday -->
			<a href="?class=planner&action=change&id={VAR:did}&date={VAR:date}">{VAR:LC_PLANNER_SHOW_DAY}</a>
			<!-- END SUB: showday -->
			</center>
			<br>
			<!-- SUB: element -->
				<font color="{VAR:color}"><i>{VAR:time}</i></font><br>"<a href="?class=planner&action&editevent&id={VAR:id}&date={VAR:date}"><font color="{VAR:color}">{VAR:title}</font></a>"
				<p>
			<!-- END SUB: element -->
			<br>
			</small>
		</td>
		<!-- END SUB: subline -->
		<td class="fgtitle" align="center" valign="top">
		<a href="?class=planner&action=change&disp=week&id={VAR:did}&date={VAR:date}">{VAR:LC_PLANNER_SHOW_WEEK}</a>
		</td>
		
	</tr>
	<!-- END SUB: line -->
	</table>
</td>
</tr>
</table
