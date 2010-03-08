{VAR:TABS}
		



<form action="reforb.{VAR:ext}" METHOD=POST>
<span class="textpealkiri"><b>Uus teema</b></span>

<table  border="0" cellspacing="0" cellpadding="4">
	<tr > 
		<td valign="middle" align="right"  class="text">Teema:</td>
		<td><input type="text" NAME="topic" SIZE="20" value='{VAR:name}'></td>
	</tr>
	<!--
	<tr >
			<td valign="middle"  class="text">Nimi:</td><td><input type="text" NAME="from" SIZE="20"></td>
	</tr>
	-->
	<tr> 
		<td valign="top" class="text">Pikemalt:</td><td><textarea name="text" cols="30" rows="10">{VAR:comment}</textarea></td>
	</tr>
	<tr>
		<td colspan=2 align=right><input type='submit' class="promo_title" VALUE='Lisa'></td>
	</tr>
</table>
{VAR:reforb}
</form>
