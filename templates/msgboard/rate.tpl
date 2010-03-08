<table border="0" cellspacing="5" cellpadding="0">
<form action="reforb.{VAR:ext}" method="POST">
<tr>
<td class="celltext" valign="bottom">Hinne: {VAR:rating}</td>

<!-- SUB: rate -->
<td class="celltext" valign="middle"><input type="radio" name="vote" value="{VAR:value}">{VAR:name}</td>
<!-- END SUB: rate -->

<td class="celltext" valign="middle">
<input type="submit" value="Hääleta" class="formbutton">
</td>
</tr>
</table>
{VAR:reforb}
</form>
