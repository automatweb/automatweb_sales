<table border="0" cellspacing="1" cellpadding="0" width=100%>
<tr>
<td class="fgtitle">
<b>E-posti aadressid:</b>
<a href="{VAR:change_link}">Konfigureeri</a>
|
<a href="{VAR:rate_link}">Hinded</a>
</td>
</tr>
</table>


<form action='reforb.{VAR:ext}' method=post>
{VAR:table}
<table>
<tr>
<td class="hele_hall_taust" colspan=2>
<input type='submit' name="delete" VALUE='Kustuta'>
</td>
</tr>
<tr>
<td colspan="2" class="hele_hall_taust">
<b>Lisa uus aadress:</b>
</td>
</tr>
<tr>
<td class="hele_hall_taust">Nimi:</td>
<td class="hele_hall_taust"><input type="text" name="newname" size="50"</td>
</tr>
<tr>
<td class="hele_hall_taust">Aadress:</td>
<td class="hele_hall_taust"><input type="text" name="newaddress" size="50"</td>
</tr>
<tr>
<td class="hele_hall_taust" colspan=2>
<input type='submit' VALUE='Salvesta' CLASS="small_button">
</td>
</tr>
</table>
{VAR:reforb}
</form>
