<form method="GET" action="orb.{VAR:ext}">
<input type="hidden" name="id" value="{VAR:id}">
<input type="hidden" name="manager" value="{VAR:manager}">
<input type="hidden" name="client_type" value="{VAR:client_type}">

<table border=0 cellspacing=0 cellpadding=0>
<tr>
<td class="aste01" colspan="2">
<table border=0 cellspacing=0 cellpadding=5>
<tr>
<td colspan="2">
	<table border=0 cellspacing=0 cellpadding=2>
		<tr>
			<td class="celltext">Eesnimi:</td>
			<td class="celltext"><input type="text" name="firstname" size="40" value='{VAR:firstname}' class="formtext"></td>
		</tr>
		<tr>
			<td class="celltext">Perenimi:</td>
			<td class="celltext"><input type="text" name="lastname" size="40" value='{VAR:lastname}' class="formtext"></td>
		</tr>
		<tr>
			<td></td>
			<td class="celltext"><input type="submit" class="formbutton" value="Otsi"></td>
		</tr>
	</table>
</td>
</tr>

<tr class="aste06">
<td class="celltext" colspan=2>Tulemused:</td>
<td>
{VAR:result}
</td>
</tr>
</table>
</td></tr></table>
{VAR:reforb}
</form>
