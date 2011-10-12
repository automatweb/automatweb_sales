<form action="reforb{VAR:ext}" method="POST">
	<table>
		<tr>
			<td>UID:</td>
			<td><input type="text" name="uid" /></td>
		</tr>
		<tr>
			<td>Password:</td>
			<td><input type="password" name="password" /></td>
		</tr>
		<tr>
			<td colspan="2"><input type="submit" value="Logi sisse" /></td>
		</tr>
	</table>
	<input type="hidden" name="class" value="users" />
	<input type="hidden" name="action" value="login" />
</form>
