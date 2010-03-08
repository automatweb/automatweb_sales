<form action="{VAR:baseurl}/index.{VAR:ext}" method="POST">
Ostukorv:<br>

<table border="0" width="100%">
<tr>
	<td>Nimi</td>
	<td >Hind</td>
	<td align="right">Kogus</td>
</tr>
<tr>
	<td colspan="3"><hr></td>
</tr>

<!-- SUB: PROD -->
{VAR:prod_html}
<!-- END SUB: PROD -->
<tr>
	<td colspan="2"><input type="submit" name="confirm_order" value="Kinnita tellimus"></td>
	<td align="right"><input type="submit" value="Uuenda korv"></td>
</tr>
</table>


{VAR:reforb}
</form>