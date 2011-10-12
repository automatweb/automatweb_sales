<form action="reforb{VAR:ext}" method="post" name="q">
<table border="0" cellspacing="0" cellpadding="0">
<tr>
<td bgcolor="#CCCCCC">

<table border="0" cellspacing="1" cellpadding="0">
	<tr>
		<td height="15" colspan="11" class="fgtitle">&nbsp;<b>Gruppide action menüüd:&nbsp;<a href='javascript:document.q.submit()'>Salvesta</a></b></td>
	</tr>
	<tr>
		<td align="center" class="title">&nbsp;GID&nbsp;</td>
		<td align="center" class="title">&nbsp;Pri&nbsp;</td>
		<td align=center class="title">&nbsp;Grupp&nbsp;</td>
		<td align=center class="title">&nbsp;Menüü&nbsp;</td>
<!--		<td align=center class="title">&nbsp;Otsi&nbsp;</td>-->
	</tr>
<!-- SUB: LINE -->
<tr>
<td class="fgtext" align=center>&nbsp;{VAR:gid}&nbsp;</td>
<td class="fgtext" align=center>&nbsp;<input type="text" name="pri[{VAR:gid}]" value="{VAR:priority}" size="2" maxlength="2">&nbsp;</td>
<td class="fgtext">&nbsp;{VAR:group}&nbsp;</td>
<td class="fgtext">&nbsp;
<select name='login_menu[{VAR:gid}]'>{VAR:login_menu}</select>
&nbsp;</td>
<!-- <td class="fgtext">&nbsp;
<a href="{VAR:search}">Otsi uus..<a>

&nbsp;</td> -->
<!-- END SUB: LINE -->
</table>
</td></tr></table>
{VAR:reforb}
</form>
