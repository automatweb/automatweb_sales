<dl class="news"><dt>T&auml;itke enda kohta andmed:</dt></dl>

<form action="{VAR:baseurl}/reforb.{VAR:ext}" method="POST">
<table border="0" cellpadding="2" cellspacing="0">
	<tr>
		<td colspan="2"><b>{VAR:person} {VAR:person_rank} {VAR:person_mail} {VAR:person_phone} {VAR:person_address}</b></td>
	<tr>
	<tr>
		<td colspan="2"><b>{VAR:date} {VAR:time_from} - {VAR:time_to}</b></td>
	</tr>
	<!-- SUB: FAIL_first_name -->
	<tr>
		<td colspan="2"><font color="red">J&auml;rgnev v&auml;li peab olema t&auml;idetud!</font></td>
	</tr>
	<!-- END SUB: FAIL_first_name -->
	<tr>
		<td width="100">* Eesnimi:</td>
		<td><input class="Input" type="text" name="reg[first_name]" value="{VAR:first_name}"></td>
	</tr>
	<!-- SUB: FAIL_last_name -->
	<tr>
		<td colspan="2"><font color="red">J&auml;rgnev v&auml;li peab olema t&auml;idetud!</font></td>
	</tr>
	<!-- END SUB: FAIL_last_name -->
	<tr>
		<td width="100">* Perekonnanimi:</td>
		<td><input type="text" class="Input" name="reg[last_name]" value="{VAR:last_name}"></td>
	</tr>

	<!-- SUB: FAIL_phone -->
	<tr>
		<td colspan="2"><font color="red">J&auml;rgnev v&auml;li peab olema t&auml;idetud!</font></td>
	</tr>
	<!-- END SUB: FAIL_phone -->
	<tr>
		<td width="100">* Telefon:</td>
		<td><input type="text" class="Input" name="reg[phone]" value="{VAR:phone}"></td>
	</tr>
	<!-- SUB: FAIL_email -->
	<tr>
		<td colspan="2"><font color="red">J&auml;rgnev v&auml;li peab olema t&auml;idetud!</font></td>
	</tr>
	<!-- END SUB: FAIL_email -->
	<tr>
		<td width="100">* E-mail:</td>
		<td><input type="text" class="Input" name="reg[email]" value="{VAR:email}"></td>
	</tr>

	<!-- SUB: FAIL_code -->
	<tr>
		<td colspan="2"><font color="red">J&auml;rgnev v&auml;li peab olema t&auml;idetud!</font></td>
	</tr>
	<!-- END SUB: FAIL_code -->
	<tr>
		<td width="100">* Isikukood:</td>
		<td><input type="text" class="Input" name="reg[code]" value="{VAR:code}"></td>
	</tr>
	<tr>
		<td colspan="2">Sisu:</td>
	</tr>
	<tr>
		<td colspan="2"><textarea class="Input" name="reg[content]" rows="10" cols="50">{VAR:content}</textarea></td>
	</tr>
	<tr>
		<td colspan="2"><input class="Input" type="submit" value="Registreeru"></td>
	</tr>
</table>
{VAR:reforb}
</form>