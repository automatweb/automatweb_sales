<form method="POST" action="/index.{VAR:ext}">
<table border="0">
<tr style="font-family: Arial, Helvetica; font-size: 12px">
<td colspan="2">{VAR:LC_USERNAME} <strong>{VAR:uid}</strong></td>
</tr>
<tr style="font-family: Arial, Helvetica; font-size: 12px">
<td>{VAR:LC_PASS}:</td>
<td><input type="password" name="pass1" size="30"</td>
</tr>
<tr style="font-family: Arial, Helvetica; font-size: 12px">
<td>{VAR:LC_PASS_AGAIN}:</td>
<td><input type="password" name="pass2" size="30"</td>
</tr>
<tr style="font-family: Arial, Helvetica; font-size: 12px">
<td colspan="2" align="center">
<input type="submit" value="{VAR:LC_CONFIRM_BUTTON}">
{VAR:reforb}
</td>
</tr>
<tr style="font-family: Arial, Helvetica; font-size: 12px">
<td colspan="2" align="center">
<span style="color: red"><strong>{VAR:status_msg}</strong></span>
</td>
</tr>
</table>
</form>
