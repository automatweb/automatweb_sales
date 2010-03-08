<table border="1" cellspacing="1" cellpadding="1" width="100%">
<tr>
<td colspan="7" align="center">
<strong>{VAR:caption}</strong>
</td>
</tr>
<!-- SUB: week -->
<tr>
<!-- SUB: header -->
<td align="center"><strong>{VAR:head}</strong></td>
<!-- END SUB: header -->
<!-- SUB: cell -->
<td valign="top" width="80">
<table border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
<td class="lefttab" align="center" bgcolor="#ccffcc"><strong>{VAR:nday}</strong></td>
</tr>
<tr>
<td class="lefttab" valign="top">
{VAR:contents}
</td>
</tr>
</table>
</td>
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

