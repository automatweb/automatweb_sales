<form method="POST" ACTION='{VAR:baseurl}/index{VAR:ext}'>
{VAR:error}
<table border=0 cellspacing=1 cellpadding=2>
<tr>
<td class="fcaption">{VAR:LC_USERS_USERNAME}:</td>
<td class="fcaption"><input type="text" name="a_uid" VALUE='{VAR:uid}'></td>
</tr>
<tr>
<td class="fcaption">{VAR:LC_USERS_EMAIL}:</td>
<td class="fcaption"><input type="text" name="email" VALUE='{VAR:email}'></td>
</tr>
<tr>
<td class="fcaption">{VAR:LC_USERS_PW}:</td>
<td class="fform"><input type="password" name="pass"></td>
</tr>
<tr>
<td class="fcaption">{VAR:LC_USERS_PW_AGAIN}:</td>
<td class="fform"><input type="password" name="pass2"></td>
</tr>
<tr>
<td class="fform" align="center" colspan="2">
<input type="submit" value="{VAR:LC_USERS_SAVE}">
{VAR:reforb}
</td>
</tr>
</table>
</form>
