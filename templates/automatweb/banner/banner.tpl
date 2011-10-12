<form enctype="multipart/form-data" method=POST action='banner{VAR:ext}'>
<input type="hidden" name="MAX_FILE_SIZE" value="100000">
<table border="0" cellspacing="0" cellpadding="0" width=100%>
<tr>
<td bgcolor="#CCCCCC">

<table border="0" cellspacing="1" cellpadding="0" width=100%>
<tr>
<td height="15" colspan="11" class="fgtitle">&nbsp;<b>PERIOOD: 
<!-- SUB: PREV_ACT -->
<a href='banner{VAR:ext}?period=prev&op=banner'>Eelmine periood</a> |
<!-- END SUB: PREV_ACT -->

<!-- SUB: PREV -->
Eelmine periood |
<!-- END SUB: PREV -->

<!-- SUB: CUR_ACT -->
<a href='banner{VAR:ext}?op=banner'>Aktiivne periood</a> |
<!-- END SUB: CUR_ACT -->

<!-- SUB: CUR -->
Aktiivne periood |
<!-- END SUB: CUR -->

<!-- SUB: NEXT_ACT -->
<a href='banner{VAR:ext}?period=next&op=banner'>Järgmine periood</a>
<!-- END SUB: NEXT_ACT -->

<!-- SUB: NEXT -->
Järgmine periood
<!-- END SUB: NEXT -->
</b>
</td>
</tr>
<tr>
	<td class="fcaption">Praegune bänner</td>
	<td class="fcaption"><img src="{VAR:imgref}"></td>
</tr>
<tr>
	<td class="fcaption">Vali uus pilt</td>
	<td class="fform"><input type="file" size="40" name="pilt"></td>
</tr>
<tr>
	<td class="fform" colspan="2" align="center">
	<input type="hidden" name="op" value="upload_banner">
	<input type="hidden" name="type" value="{VAR:type}">
	<input type="hidden" name="period" value="{VAR:period}">
	<input type="submit" value="Salvesta uus pilt">
	</td>
</tr>
</table>
</td>
</tr>
</table>
</form>
