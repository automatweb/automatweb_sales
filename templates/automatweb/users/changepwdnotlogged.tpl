Te peate oma parooli muutma.
<form action='/reforb{VAR:ext}' method="POST">
<span class="textred12">
	{VAR:error}
</span>
<table cellpadding="0" cellspacing="2" class="text12">
	<tr>
		<td>Kasutajanimi</td>
		<td>
			<input type="hidden" name="username" value='{VAR:user_oid}'>
			{VAR:username}
		</td>
	<tr>
	
	<tr>
		<td>Vana parool</td>
		<td>
			<input type="password" name="old_pass">
		</td>
	<tr>
	
	<tr>
		<td>Uus parool</td>
		<td>
			<input type="password" name="new_pass">
		</td>
	<tr>
	
	<tr>
		<td>Korda uut parooli&nbsp;</td>
		<td>
			<input type="password" name="new_pass_repeat">
		</td>
	<tr>

	<tr>
		<td colspan="2" align="center">
		<input name="submit" type="submit" value="Muuda parool">
	</td>
	<tr>
</table>
	<input name="class" type="hidden" value="users" />
	<input name="action" type="hidden" value="submit_change_password_not_logged" />
</form>