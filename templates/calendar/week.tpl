<table border="1" cellspacing="1" cellpadding="2" bgcolor="#FFFFFF">
<!-- SUB: line -->
<tr>
	<td><a href="{VAR:self}?date={VAR:date}&day={VAR:day}&mon={VAR:mon}{VAR:add}">{VAR:weekday}</a></td>
	<td align="right">{VAR:day}.{VAR:month}</td>
</tr>
<!-- END SUB: line -->
<!-- SUB: active -->
<tr>
	<td><font color="#FFCCAA"><strong><a href="{VAR:self}?date={VAR:date}&day={VAR:day}&mon={VAR:mon}{VAR:add}">{VAR:weekday}</a></strong></font></td>
	<td align="right"><font color="#FFCCAA"><strong>{VAR:day}.{VAR:month}</strong></font></td>
</tr>
<!-- END SUB: active -->
<tr>
	<td><a href="{VAR:self}?date={VAR:prev}{VAR:add}">{VAR:LC_CALENDAR_LAST}<br>{VAR:LC_CALENDAR_WEEK}</a></td>
	<td align="right"><a href="{VAR:self}?date={VAR:next}{VAR:add}">{VAR:LC_CALENDAR_NEXT}<br>{VAR:LC_CALENDAR_WEEK}</a></td>
</tr>
<tr>
	<td colspan="2" align="center"><a href="{VAR:self}?{VAR:add}">{VAR:LC_CALENDAR_TODAY}</a></td>
</tr>
</table>
