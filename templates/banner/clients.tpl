<form name='boo' action='reforb{VAR:ext}' method=post>
<table border="0" cellspacing="0" cellpadding="0" width=100%>
<tr>
<td bgcolor="#CCCCCC">
<table border="0" cellspacing="1" cellpadding="2" width=100%>
<tr>
<td height="15" colspan="15" class="fgtitle">&nbsp;<b>{VAR:LC_BANNER_BIG_CLIENTS}:&nbsp;<a href='javascript:document.boo.submit()'>Salvesta</a></b></td>
</tr>
<tr>
<td align="center" class="title">&nbsp;ID&nbsp;</td>
<td align="center" class="title">&nbsp;{VAR:LC_BANNER_NAME}&nbsp;</td>
<td colspan=2 align="center" class="title">&nbsp;&nbsp;</td>
</tr>
<!-- SUB: LINE -->
<tr>
<td class="fgtext">&nbsp;{VAR:id}&nbsp;</td>
<td class="fgtext">&nbsp;<input size=50 type='text' name='name[{VAR:id}]' value='{VAR:name}'>&nbsp;</td>
<td class="fgtext">&nbsp;<a href='{VAR:cl_banners}'>{VAR:LC_BANNER_SMALL_BANNERS}</a>&nbsp;</td>
<td class="fgtext">&nbsp;<a href='{VAR:cl_stats}'>{VAR:LC_BANNER_SAVE}</a>&nbsp;</td>
</tr>
<!-- END SUB: LINE -->
</table>
</td>
</tr>
</table>
{VAR:reforb}
</form>
<Br><br>
