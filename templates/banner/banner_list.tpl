<form name='boo' action='reforb{VAR:ext}' method=post>
<table border="0" cellspacing="0" cellpadding="0" width=100%>
<tr>
<td bgcolor="#CCCCCC">
<table border="0" cellspacing="1" cellpadding="2" width=100%>
<tr>
<td height="15" colspan="15" class="fgtitle">&nbsp;<b>{VAR:LC_BANNER_BANNERS}:&nbsp;<a href='{VAR:add}'>{VAR:LC_BANNER_ADD}</a></b></td>
</tr>
<!-- SUB: LINE -->
<tr>
<td colspan=5 class="fgtext">{VAR:img}</td>
</tr>
<tr>
<td class="fgtext">&nbsp;{VAR:id}&nbsp;</td>
<td class="fgtext">&nbsp;<a href='{VAR:url}' target=_blank>{VAR:url}</a>&nbsp;</td>
<!-- SUB: ACTIVE -->
<td class="fgtext">&nbsp;<a href='{VAR:deactivate}'>{VAR:LC_BANNER_CHANGE_NOT_ACTIVE}</a>&nbsp;</td>
<!-- END SUB: ACTIVE -->
<!-- SUB: DEACTIVE -->
<td class="fgtext">&nbsp;<a href='{VAR:activate}'>{VAR:LC_BANNER_CHANGE_ACTIVE}</a>&nbsp;</td>
<!-- END SUB: DEACTIVE -->
<td class="fgtext">&nbsp;<a href='{VAR:change}'>{VAR:LC_BANNER_CHANGE}</a>&nbsp;</td>
<td class="fgtext">&nbsp;<a href='{VAR:delete}'>{VAR:LC_BANNER_DELETE}</a>&nbsp;</td>
</tr>
<!-- END SUB: LINE -->
</table>
</td>
</tr>
</table>
{VAR:reforb}
</form>
<Br><br>
