<table border="1" width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse; border-color: #CCCCCC;">
<!-- SUB: dcell -->
<tr>
<td width="50" align="center" style="line-height: 1em;"><small>{VAR:dcellheader}</small></td>
<!-- SUB: duration_cell -->
<td width="2" bgcolor="{VAR:color}">
</td>
<!-- END SUB: duration_cell -->
<td style="font-size: 12px; font-weight: normal; padding: 3px; text-decoration: none;">
<!-- SUB: d_event -->
		{VAR:time_start} <input type="checkbox" name="mark[]" value="{VAR:id}"><img border="0" src="{VAR:event_icon_url}"><a href="{VAR:link}">{VAR:name}</a>
<!-- END SUB: d_event -->
</td>
</tr>
<!-- END SUB: dcell -->
</table>
