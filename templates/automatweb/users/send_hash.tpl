<form method="POST" action="/index{VAR:ext}">
<span style="font-family: Arial, Helvetica; font-size: 12px">
<strong></strong>
{VAR:LC_USER_ENTER}.<br><br>
{VAR:LC_USER_PROBLEMS} <a href="mailto:{VAR:webmaster}">{VAR:webmaster}</a>.
</span>
<br><br>
<table border="0">
<tr style="font-family: Arial, Helvetica; font-size: 12px">
<td>
<input type="radio" name="type" value="email" checked>
</td>
<td>
{VAR:LC_USER_MAIL}:
</td>
<td><input type="text" name="email" size="30">
</td>
</tr>
<tr style="font-family: Arial, Helvetica; font-size: 12px">
<td>
<input type="radio" name="type" value="uid">
</td>
<td>
{VAR:LC_USER_NAME}:
</td>
<td><input type="text" name="uid" size="30">
</td>
</tr>
<tr style="font-family: Arial, Helvetica; font-size: 12px">
<td colspan="3" align="center">
<input type="submit" value="{VAR:LC_USER_BUTTON}">
</td>
</tr>
<tr style="font-family: Arial, Helvetica; font-size: 12px">
<td colspan="3" align="center">
<font color="red"><b>{VAR:status_msg}</b></font><br>
</td>
</tr>
</table>
{VAR:reforb}
</p>
</form>
