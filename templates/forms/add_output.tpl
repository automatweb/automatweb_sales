

<form action='reforb.{VAR:ext}' METHOD={VAR:meth}>



<table border="0" cellspacing="0" cellpadding="2" width="100%">
<tr>
<td class="aste01">

<table border="0" cellspacing="5" cellpadding="2">
<tr>

<td class="celltext" align="right">{VAR:LC_FORMS_NAME}:</td>
<td class="celltext"><input type='text' NAME='name' VALUE='{VAR:name}' class="formtext"></td>
</tr>
<tr>
<td class="celltext" align="right">{VAR:LC_FORMS_COMMENT}:</td>
<td class="celltext"><textarea cols=50 rows=5 NAME=comment class="formtext">{VAR:comment}</textarea></td>
</tr>
<tr>
<td valign="top" class="celltext" align="right">{VAR:LC_FORMS_CHOOSE_FORMS}:</td>
<td class="celltext"><select name='forms[]' multiple class="formselect2">{VAR:forms}</select></td>
</tr>
<tr>
<td class="celltext" align="right">{VAR:LC_FORMS_CHOOSE_TABLE_STYLE}:</td>
<td class="celltext"><select name='table_style' class="formselect2">{VAR:styles}</select></td>
</tr>
<!-- SUB: ADD -->
<tr>
<td class="celltext" align="right">{VAR:LC_FORMS_CHOOSE_SUBFORM}:</td>
<td class="celltext"><select multiple name='baseform[]' class="formselect2">{VAR:forms2}</select></td>
</tr>
<!-- END SUB: ADD -->

<!-- SUB: ADD_2_LINE -->
<tr>
<td class="celltext" align="right">{VAR:form_name}</td>
<td class="celltext"><input type='text' name='ord[{VAR:form_id}]' size=3></td>
</tr>
<!-- END SUB: ADD_2_LINE -->

<!-- SUB: ADD2 -->
<tr>
	<td class="celltext" align="right">{VAR:LC_FORMS_CHOOSE_ELEMENTS}:</td>
	<td class="celltext"><select name='elements[]' multiple  class="formselect2" size="20">{VAR:els}</select></td>
</tr>
<!-- END SUB: ADD2 -->
<tr>
	<td class="celltext" align="right">{VAR:LC_FORMS_ALIASMGR}:</td>
	<td class="celltext"><input type="checkbox" name="has_aliasmgr" value="1" {VAR:has_aliasmgr}></td>
</tr>
<tr>
	<td class="celltext" align="right">Kontrollerid:</td>
	<td class="celltext"><input type="checkbox" name="has_controllers" value="1" {VAR:has_controllers}></td>
</tr>
<tr>
	<td class="celltext" align="right">V&auml;&auml;rtus sessioonist:</td>
	<td class="celltext"><input type="checkbox" name="session_value" value="1" {VAR:session_value}></td>
</tr>
<tr>
	<td class="celltext" align="right">Sessiooni formid:</td>
	<td class="celltext"><select class="formselect2" name="session_form">{VAR:session_form}</select></td>
</tr>
<tr>
	<td class="celltext" colspan="2">Vali kataloogid, kuhu elemente lisatakse:</td>
</tr>
<tr>
	<td class="celltext" colspan="2"><select name="el_menus[]" multiple=1>{VAR:el_menus}</select></td>
</tr>
<tr>
	<td class="celltext" colspan="2">Vali kataloogid, kust elemente v&otilde;etakse:</td>
</tr>
<tr>
	<td class="celltext" colspan="2"><select name="el_menus2[]" multiple=1>{VAR:el_menus2}</select></td>
</tr>
<!-- SUB: CHANGE -->
<tr>
<td class="celltext" colspan=2><a href='{VAR:admin}'>{VAR:LC_FORMS_ADMIN}</a></td>
</tr>
<!-- END SUB: CHANGE -->
<tr>
<td colspan=2 class="celltext" align="center">
<input class='formbutton' type='submit' VALUE='{VAR:LC_FORMS_SAVE}'>
<input class='formbutton' type='reset' VALUE='{VAR:LC_FORMS_RESET}'>
</td>
</tr>
</table>

</td></tr></table>

{VAR:reforb}
</form>
