<big><strong><a href="{VAR:prevlink}">&lt;&lt;</a> [ {VAR:date} ] <a href="{VAR:nextlink}">&gt;&gt;</a></strong></big>
<table border=1>
<tr>
<!--
<th><small>Vali</small></th>
<th><small>Aeg</small></th>
-->
<!-- SUB: one_calendar -->
<th>
<small>
{VAR:calendar_name}
</th>
</small>
<!-- END SUB: one_calendar -->
</tr>
<tr>
<!-- SUB: one_calendar_content -->
<td valign="top">
<table border=0 width=100%>
<!-- SUB: cell -->
<tr>
<!-- SUB: selector_cell -->
<td><small><input type="radio" name="emb[start_time]" value="{VAR:event_sel_id}" {VAR:checked}></small></td>
<!-- END SUB: selector_cell -->
<td bgcolor="{VAR:bgcolor}"><small>{VAR:time}</small></td>
<td height="50" bgcolor="{VAR:bgcolor}"><small>{VAR:event}</small></td>
</tr>
<!-- END SUB: cell -->
</table>
</td>
<!-- END SUB: one_calendar_content -->
</tr>
</table>
