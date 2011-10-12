<form action='reforb{VAR:ext}' method=post>
<table bgcolor="#CCCCCC" cellpadding=3 cellspacing=1 border=0 style="margin-top: 100px;">
<tr>
	<td class="fform">Nimi:</td>
	<td class="fform"><input size="50" type='text' NAME='name' VALUE='{VAR:name}'></td>
</tr>
<!-- SUB: CHANGE -->
<tr>
	<td colspan="2" class="fform">
		Valemis kasutatavad muutujad:
		<table bgcolor="#CCCCCC" cellpadding=3 cellspacing=1 border=0 width="90%">
			<tr>
				<td class="fform" colspan="5"><a href='{VAR:add_var}'>Lisa</a></td>
			</tr>
			<tr>
				<td class="fform">Nimi</td>
				<td class="fform">Asukoht</td>
				<td class="fform">V&auml;&auml;rtus</td>
				<td class="fform">Muuda</td>
				<td class="fform">Kustuta</td>
			</tr>
			<!-- SUB: VAR_LINE -->
			<tr>
				<td class="fform">{VAR:var_name}</td>
				<td class="fform">{VAR:ref}</td>
				<td class="fform">{VAR:var_value}</td>
				<td class="fform"><a href='{VAR:ch_var}'>Muuda</a></td>
				<td class="fform"><a href='{VAR:del_var}'>Kustuta</a></td>
			</tr>
			<!-- END SUB: VAR_LINE -->
		</table>
		<Br>
		Lisaks nimekirjas olevatele muutujatele on ka olemas parajasti <br>
		teostatava formi sisestuse muutujad, <br>
		iga elemendi kohta yks muutuja, elemendi nimega. <Br><br>
		Valemisse tuleb muutujate nimed kirjutada nurksulgude vahele, <Br>muutuja nimega el on alati defineeritud ja sisaldab kontrollitava <Br>elemendi v22rtust. <Br><br>
		Muutujate v&auml;&auml;rtusi saab kasutada ka veateadetes.<Br>
	</td>
</tr>
<!-- END SUB: CHANGE -->
<tr>
	<td class="fform">Valem:</td>
	<td class="fform"><textarea class="codepress php" id="eq" name="eq" cols="80" rows="50" >{VAR:eq}</textarea>
	<br />
	<input type="checkbox" onClick="eq.toggleAutoComplete();" checked /> AutoComplete
	</td>
</tr>
<!-- SUB: LANG -->
<tr>
	<td class="fform">Veateade ({VAR:lang_name}):</td>
	<td class="fform"><input size="50" type='text' NAME='errmsg[{VAR:lang_id}]' VALUE='{VAR:errmsg}'></td>
</tr>
<!-- END SUB: LANG -->
<tr>
	<td class="fform" colspan="2">Kas n&auml;itamise kontroller n&auml;itab elemendi asemel veateadet? <input type='checkbox' name='show_errors_showctl' value='1' {VAR:show_errors}></td>
</tr>
<tr>
	<td class="fform" colspan="2">Kas sisestuse kontroller hoiatab, kuid teeb siiski sisestuse &auml;ra? <input type='checkbox' name='warn_only_entry_controller' value='1' {VAR:warn_only_entry_controller}></td>
</tr>
<!-- SUB: CHANGE2 -->
<tr>
	<td class="fform" colspan="2"><a href='{VAR:form_list}'>Vaata millistes elementides see kontroller kasutusel on</a></td>
</tr>
<!-- END SUB: CHANGE2 -->
<tr>
	<td class="fform" colspan="2">&Auml;ra asenda kontrolleris muutujaid: <input type='checkbox' name='no_var_replace' value='1' {VAR:no_var_replace}></td>
</tr>
<tr>
	<td class="fform" colspan="2">Kontrolleri veateade js popupis: <input type='checkbox' name='error_js_pop' value='1' {VAR:error_js_pop}></td>
</tr>
<tr>
	<td class="fform" colspan="2">Kontrolleri veateade ikooni alt tekstina: <input type='checkbox' name='error_icon' value='1' {VAR:error_icon}></td>
</tr>
<tr>
<td class="fform" colspan=2><input class='small_button' type='submit' VALUE='Salvesta'></td>
</tr>
</table>
{VAR:reforb}
</form>
