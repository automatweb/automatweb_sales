<form action='reforb.{VAR:ext}' method=post>
<table bgcolor="#CCCCCC" cellpadding=3 cellspacing=1 border=0>
<tr>
	<td class="fform">Nimi:</td>
	<td class="fform"><input size="50" type='text' NAME='var_name' VALUE='{VAR:var_name}'></td>
</tr>
<!-- SUB: CHANGE -->
<tr>
	<td colspan="2" class="fform">Vali form:</td>
</tr>
<tr>
	<td colspan="2" class="fform"><select class='small_button' NAME='sel_form'>{VAR:forms}</select></td>
</tr>
<!-- SUB: FORM_SEL -->
<tr>
	<td colspan="2" class="fform">Vali element formis:</td>
</tr>
<tr>
	<td colspan="2" class="fform"><select class='small_button' NAME='sel_el'>{VAR:elements}</select></td>
</tr>
<!-- SUB: EL_SEL -->
<tr>
	<td colspan="2" class="fform">Kust v&otilde;etakse elemendi v&auml;&auml;rtus:</td>
</tr>
<tr>
	<td class="fform"><input type='radio' name='entry_type' value='entry_id' {VAR:et_entry_id}></td>
	<td class="fform">Kindel selle formi sisestus</td>
</tr>
<!-- SUB: ET_ENTRY_ID -->
<tr>
	<td class="fform">Vali sisestus:</td>
	<td class="fform"><select class='small_button' NAME='sel_entry_id'>{VAR:entries}</select>
		<!-- SUB: CHANGE_ENTRY -->
		<a href='{VAR:change_entry}'>Muuda sisestust</a>
		<!-- END SUB: CHANGE_ENTRY -->
	</td>
</tr>
<!-- END SUB: ET_ENTRY_ID -->
<tr>
	<td class="fform"><input type='radio' name='entry_type' value='user_data' {VAR:et_user_data}></td>
	<td class="fform">Valitud formi sisestus kasutaja infost</td>
</tr>
<tr>
	<td class="fform"><input type='radio' name='entry_type' value='user_entry' {VAR:et_user_entry}></td>
	<td class="fform">Kasutaja esimene selle formi sisestus</td>
</tr>
<tr>
	<td class="fform"><input type='radio' name='entry_type' value='same_chain' {VAR:et_same_chain}></td>
	<td class="fform">Sisestatava p&auml;rja teise formi siestus</td>
</tr>
<tr>
	<td class="fform"><input type='radio' name='entry_type' value='session' {VAR:et_session}></td>
	<td class="fform">Formi viimane sisestus sessioonist</td>
</tr>
<tr>
	<td class="fform"><input type='radio' name='entry_type' value='writer_entry' {VAR:et_writer_entry}></td>
	<td class="fform">SQL kirjutaja k&auml;esolev sisestus</td>
</tr>
<tr>
	<td class="fform"><input type='radio' name='entry_type' value='element_sum' {VAR:et_element_sum}></td>
	<td class="fform">K&otilde;ik vaadataval lehel olevate selle elemendi sisestuste summa</td>
</tr>
<tr>
	<td class="fform"><input type='radio' name='entry_type' value='other_chain' {VAR:et_other_chain}></td>
	<td class="fform">Sisestatava p&auml;rjaga seotud teise p2rja teise formi siestus</td>
</tr>
<!-- SUB: OTHER_ELEMENT -->
<tr>
	<td colspan="2" class="fform">Vali selle p&auml;rja seose form:</td>
</tr>
<tr>
	<td colspan="2" class="fform"><select class='small_button' NAME='other_sel_form'>{VAR:other_forms}</select></td>
</tr>
<tr>
	<td colspan="2" class="fform">Vali seoseelement:</td>
</tr>
<tr>
	<td colspan="2" class="fform"><select class='small_button' NAME='other_sel_el'>{VAR:other_elements}</select></td>
</tr>
<!-- END SUB: OTHER_ELEMENT -->


<!-- END SUB: EL_SEL -->

<!-- END SUB: FORM_SEL -->

<!-- END SUB: CHANGE -->
<tr>
<td class="fform" colspan=2><input class='small_button' type='submit' VALUE='Salvesta'></td>
</tr>
</table>
{VAR:reforb}
</form>
