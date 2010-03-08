<table border="1" cellspacing="1" cellpadding="1">
<tr>
<td align="center"><strong><a href="{VAR:self}?{VAR:prefix}&year={VAR:prevyear}&mon={VAR:prevmon}{VAR:add}">&lt;&lt;</a></strong></td>
<td colspan="5" align="center">
<strong><a href="{VAR:self}?year={VAR:year}&mon={VAR:mon}{VAR:add}">{VAR:caption}</a></strong>
</td>
<td align="center"><strong><a href="{VAR:self}?{VAR:prefix}&year={VAR:nextyear}&mon={VAR:nextmon}{VAR:add}">&gt;&gt;</a></strong></td>
</tr>
<!-- SUB: week -->
<tr>
<!-- SUB: header -->
<td align="center"><strong>{VAR:head}</strong></td>
<!-- END SUB: header -->
<!-- SUB: cell -->
<td align="center">&nbsp;<a href="{VAR:self}?year={VAR:year}&mon={VAR:mon}&day={VAR:day}{VAR:add}">{VAR:nday}</a>&nbsp;</td>
<!-- END SUB: cell -->
<!-- SUB: empty -->
<td align="center">&nbsp;</td>
<!-- END SUB: empty -->
<!-- SUB: activecell -->
<td align="center">&nbsp;<strong><font color="red">{VAR:nday}</font></strong>&nbsp;</td>
<!-- END SUB: activecell -->
</tr>
<!-- END SUB: week -->
</table>

