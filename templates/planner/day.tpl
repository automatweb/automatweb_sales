<h1>{VAR:LC_PLANNER_DAYPLAN}</h1>
<form method="POST" action="/index.{VAR:ext}">
{VAR:navigator}
<table border=1 cellspacing=0 cellpadding=1 width="100%">
<tr>
<td class="lefttab" colspan="2">
<center>
<table border="0" cellspacing="2" cellpadding="0">
<tr>
<td class="lefttab" align="center"><strong><a href="{VAR:self}?class=planner&date={VAR:prev}">&lt;&lt;</a></strong></td>
<td class="lefttab">&nbsp;</td>
<td class="lefttab" align="center"><strong>{VAR:today}</strong></td>
<td class="lefttab">&nbsp;</td>
<td class="lefttab" align="center"><strong><a href="{VAR:self}?class=planner&date={VAR:next}">&gt;&gt;</a></strong></td>
</tr>
</table>
</center>
</td>
</tr>
<!-- SUB: line -->
<tr bgcolor="{VAR:bgcolor}">
<td class="lefttab" width="70" align="center" valign="top"><a href="/?class=planner&action=add_event&date={VAR:date}&time={VAR:timeslice}">{VAR:time}</a></td>
<!-- SUB: data -->
<td class="lefttab" rowspan="{VAR:rowspan}" valign="top">{VAR:content}&nbsp;</td>
<!-- END SUB: data -->
</tr>
<!-- END SUB: line -->
</table>
<!-- SUB: event -->
<input type="checkbox" name="check[{VAR:id}]" value="1" class="lefttab">
&nbsp;
<i>{VAR:start} - {VAR:end}</i>
<b><a href="{VAR:self}?class=planner&action=edit_event&id={VAR:id}">{VAR:title}</a></b> {VAR:stat}
&nbsp;
<!--
<a href="/index.{VAR:ext}?class=messenger&action=write&attach={VAR:id}"><small>[saada]</small></a>
-->
<br>
<!-- END SUB: event -->
<select name="action">
<option value="delete_events">{VAR:LC_PLANNER_DELETE}</option>
</select>
<input type="submit" value="{VAR:LC_PLANNER_SCRACH_YOUR_BACK}">
<input type="hidden" name="class" value="planner">
<input type="hidden" name="reforb" value="1">
<input type="hidden" name="date" value="{VAR:thisdate}">
</form>
