<table border="0" cellspacing="1" cellpadding="1">
<tr>
<td colspan="7" align="center">
<strong>{VAR:caption}</strong>
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

