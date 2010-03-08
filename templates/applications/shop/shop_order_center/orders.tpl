<form action="{VAR:baseurl}/reforb.{VAR:ext}" method="POST">
Minu tellimused:
<table border="0">
<tr>
	<td>Nimi</td>
	<td>Millal</td>
	<td>hind</td>
	<td>Vaata</td>
	<td>Vali</td>
</tr>
<!-- SUB: LINE -->
<tr>
	<td>{VAR:name}</td>
	<td>{DATE:tm|m.d.Y H:i}</td>
	<td>{VAR:sum} EEK</td>
	<td><a href='{VAR:view_link}'>Vaata</a></td>
	<td><input type="checkbox" name="sel[]" value="{VAR:id}" ></td>
</tr>
<!-- END SUB: LINE -->
<tr>
	<td colspan="5"><input type="submit" value="Alusta uut tellimust valitud tellimuste p&otilde;hjal" name="makenew"></td>
</tr>
</table>
{VAR:reforb}
</form>