
Sisesta aadressid kuhu suunatakse kui kasutaja sisse logib:
<form action='reforb.{VAR:ext}' method='POST' name='b88'>
<table border="0" cellspacing="0" cellpadding="0" width=100%>
<tr>
<td bgcolor="#CCCCCC">
<table border="0" cellspacing="1" cellpadding="2" width=100%>
<tr>
<td align="center" class="title">&nbsp;Grupp&nbsp;</td>
<td align="center" class="title">&nbsp;Prioriteet&nbsp;</td>
<td align="center" class="title">&nbsp;Aadress&nbsp;</td>
</tr>
<!-- SUB: LINE -->
<tr>
<td class="fgtext">&nbsp;{VAR:grp_name}&nbsp;</td>
<td class="fgtext">&nbsp;<input size=3 type='text' name='grps[{VAR:grp_id}][pri]' value='{VAR:priority}' class='small_button'>&nbsp;</td>
<td class="fgtext">&nbsp;<input type='text' name='grps[{VAR:grp_id}][url]' value='{VAR:url}' class='small_button'>&nbsp;</td>
</tr>
<!-- END SUB: LINE -->
</table>
</td>
</tr>
</table>
<input type='submit' class='small_button' value='Salvesta'>
<Br><br>
{VAR:reforb}
</form>