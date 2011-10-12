<form action="reforb{VAR:ext}" method="post">
	<table>
		<!-- SUB: TEXT_FOR_LOGIN -->
		<tr>
			<td colspan="2">
				{VAR:logintext}
			</td>
		</tr>
		<!-- END SUB: TEXT_FOR_LOGIN -->
		<tr>
			<td>Kasutajanimi:</td>
			<td><input type="text" name="uid" value="{VAR:uid}" /></td>
		</tr>
		<tr>
			<td>Parool:</td>
			<td><input type="password" name="password" /></td>
		</tr>
		<tr>
			<td colspan="2"><input type="submit" value="Logi sisse" /></td>
		</tr>
	</table>
	<input type="hidden" name="class" value="users" />
	<input type="hidden" name="action" value="login" />
</form>
