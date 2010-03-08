<table border="0" cellspacing="0" cellpadding="0" width=100%>
<tr>
<form method="GET" action="orb.{VAR:ext}">
<td bgcolor="#CCCCCC">
<table border="0" cellspacing="1" cellpadding="2" width=100%>
<tr>
<td class="fgtext" colspan="2"><strong>Muuda scheduleri objekte</strong></td>
</tr>
<tr>
<td class="fgtext">Nimi:</td>
<td class="fgtext"><input type="text" name="name" size="30" value="{VAR:name}"></td>
</tr>
<td class="fgtext">Klassid:</td>
<td class="fgtext">
<!-- SUB: class -->
<input type="checkbox" name="clid[]" value="{VAR:cvalue}" {VAR:checked}> {VAR:cname}<br>
<!-- END SUB: class -->
</td>
</tr>
<tr>
<td colspan="2" class="fgtext">
{VAR:reforb}
<input type="submit" value="Otsi">
</td>
</tr>
<tr>
<td colspan="2" class="fgtext">
<strong>Tulemused<strong>
</td>
</tr>
<tr>
<td colspan="2" class="fgtext">
{VAR:table}
</td>
</tr>
<tr>
<td colspan="2" class="fgtext">
<input type="submit" name="save" value="Salvesta valitud objektid">
</td>
</tr>
</table>
</td>
</form>
</tr>
</table>
