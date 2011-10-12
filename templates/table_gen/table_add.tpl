<form action='reforb{VAR:ext}' method=post>

<table border=0 cellspacing=0 cellpadding=0>
<tr><td class="aste01">

<table border=0 cellspacing=0 cellpadding=2>
<tr>

			<td class="celltext" align="right">{VAR:LC_TABLE_NAME}:</td>
			<td class="celltext"><input type='text' NAME='name' class="formtext" size="40"></td>
		</tr>
	<tr>
		<td class="celltext" align="right">{VAR:LC_TABLE_COMM}:</td>
		<td class="celltext"><textarea name=comment cols=40 rows=5 class="formtext"></textarea></td>
	</tr>
		<tr>
		<td>&nbsp;</td>
			<td class="celltext"><input type='submit' class='formbutton' VALUE='{VAR:LC_TABLE_SAVE}'></td>
		</tr>
	</table>

</td></tr></table>
	{VAR:reforb}
</form>
