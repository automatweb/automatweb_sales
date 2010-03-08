<table border="0" cellspacing="0" cellpadding="0" width=100%>
<tr>
<form method="POST" action="reforb.{VAR:ext}">
<td bgcolor="#CCCCCC">
<table border="0" cellspacing="1" cellpadding="2" width=100%>
<tr>
<td class="fgtext" colspan="2">
<a href="{VAR:search_objs}">Otsi objekte</a>
|
<a href="{VAR:set_time}">M‰‰ra kellaajad</a>
</td>
</tr>
<tr>
<td class="fgtext">Nimi:</td>
<td class="fgtext"><input type="text" name="name" size="30" value="{VAR:name}"></td>
</tr>
<tr>
<td valign="top" class="fgtext">Kommentaar:</td>
<td class="fgtext"><textarea name="comment" cols="30" rows="5">{VAR:comment}</textarea></td>
</tr>
<tr>
<td valign="top" class="fgtext">UID (sisselogimiseks):</td>
<td class="fgtext"><input type="text" name="uid" size="30" value="{VAR:uid}"></td>
</tr>
<tr>
<td valign="top" class="fgtext">Parool (sisselogimiseks):</td>
<td class="fgtext"><input type="password" name="password" size="30" value="{VAR:password}"></td>
</tr>
<tr>
<td class="fgtext" colspan="2">
<strong>Valitud objektid</strong>
</td>
</tr>
<tr>
<td class="fgtext" colspan="2" align="center">
{VAR:table}
</td>
</tr>
<tr>
<td class="fgtext" colspan="2">
{VAR:reforb}
<input type="submit" value="Salvesta">
</td>
</tr>
</table>
</td>
</form>
</tr>
</table>
