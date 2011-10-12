<form action='reforb{VAR:ext}' method=post>
<table bgcolor="#CCCCCC" cellpadding=3 cellspacing=1 border=0>
<tr>
	<td class="fform" colspan="2">Sisestuskontroller:</td>
</tr>
<!-- SUB: ENTRY_ELEMENT -->
<tr>
	<td class="fform">{VAR:form_name}.{VAR:element_name}</td>
	<td class="fform"><input type='hidden' name='entryels[{VAR:form_id}][{VAR:el_id}]' value='1'><input type='checkbox' name='entryels_n[{VAR:form_id}][{VAR:el_id}]' value='1' checked></td>
</tr>
<!-- END SUB: ENTRY_ELEMENT -->
<tr>
	<td class="fform" colspan="2">N&auml;itamiskontroller:</td>
</tr>
<!-- SUB: SHOW_ELEMENT -->
<tr>
	<td class="fform">{VAR:form_name}.{VAR:element_name}</td>
	<td class="fform"><input type='hidden' name='showels[{VAR:form_id}][{VAR:el_id}]' value='1'><input type='checkbox' name='showels_n[{VAR:form_id}][{VAR:el_id}]' value='1' checked></td>
</tr>
<!-- END SUB: SHOW_ELEMENT -->
<tr>
	<td class="fform" colspan="2">Listboksikontroller:</td>
</tr>
<!-- SUB: LB_ELEMENT -->
<tr>
	<td class="fform">{VAR:form_name}.{VAR:element_name}</td>
	<td class="fform"><input type='hidden' name='lbels[{VAR:form_id}][{VAR:el_id}]' value='1'><input type='checkbox' name='lbels_n[{VAR:form_id}][{VAR:el_id}]' value='1' checked></td>
</tr>
<!-- END SUB: LB_ELEMENT -->
<tr>
	<td class="fform" colspan="2">Default v&auml;&auml;rtuse kontroller:</td>
</tr>
<!-- SUB: DEFVL_ELEMENT -->
<tr>
	<td class="fform">{VAR:form_name}.{VAR:element_name}</td>
	<td class="fform"><input type='hidden' name='defvlels[{VAR:form_id}][{VAR:el_id}]' value='1'><input type='checkbox' name='defvlels_n[{VAR:form_id}][{VAR:el_id}]' value='1' checked></td>
</tr>
<!-- END SUB: DEFVL_ELEMENT -->
<tr>
	<td class="fform" colspan="2">V&auml;&auml;rtuse kontroller:</td>
</tr>
<!-- SUB: VL_ELEMENT -->
<tr>
	<td class="fform">{VAR:form_name}.{VAR:element_name}</td>
	<td class="fform"><input type='hidden' name='vlels[{VAR:form_id}][{VAR:el_id}]' value='1'><input type='checkbox' name='vlels_n[{VAR:form_id}][{VAR:el_id}]' value='1' checked></td>
</tr>
<!-- END SUB: VL_ELEMENT -->
<tr>
<td class="fform" colspan=2><input class='small_button' type='submit' VALUE='Salvesta'></td>
</tr>
</table>
{VAR:reforb}
</form>
