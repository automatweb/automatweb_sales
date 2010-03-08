<form name='boo' action='reforb.{VAR:ext}' method=post>
<table border="0" cellspacing="0" cellpadding="0" width=100%>
<tr>
<td bgcolor="#CCCCCC">
<table border="0" cellspacing="1" cellpadding="2" width=100%>
<tr>
<td height="15" colspan="15" class="fgtitle">&nbsp;<b>{VAR:LC_BANNER_CHOOSE_PROFILE_MAKE_FORM}: <a href='javascript:boo.submit()'>{VAR:LC_BANNER_SAVE}</a></b></td>
</tr>
<tr>
<td align="center" class="title">&nbsp;ID&nbsp;</td>
<td align="center" class="title">&nbsp;{VAR:LC_BANNER_NAME}&nbsp;</td>
<td align="center" class="title">&nbsp;&nbsp;</td>
</tr>
<!-- SUB: LINE -->
<tr>
<td class="fgtext">&nbsp;{VAR:id}&nbsp;</td>
<td class="fgtext">&nbsp;{VAR:name}&nbsp;</td>
<td class="fgtext">&nbsp;<input type='radio' name='sel' value='{VAR:id}' {VAR:sel}>&nbsp;</td>
</tr>
<!-- END SUB: LINE -->
</table>
</td>
</tr>
</table>
{VAR:reforb}
</form>
<Br><br>
{VAR:LC_BANNER_STATISTICS}: <br>
{VAR:LC_BANNER_TOG_BANNER_SHOW}: {VAR:t_views}<br>
{VAR:LC_BANNER_CLICKS_TOGETHER}: {VAR:t_clicks}<br>
{VAR:LC_BANNER_TOG_PROFILE}: {VAR:t_profiles}<br>
{VAR:LC_BANNER_TOG_BANNERS}: {VAR:t_banners}<br>
{VAR:LC_BANNER_TOG_CLIENTS}: {VAR:t_clients}<br>
{VAR:LC_BANNER_TOG_BANNER_USERS}: {VAR:t_busers}<br>
