<script language="javascript">
var sel_el;
function setLink(li,title)
{
	sel_el.value=li;
}
</script>

<form action='reforb.{VAR:ext}' method=post name='b88'>
<table bgcolor="#CCCCCC" cellpadding=3 cellspacing=1 border=0>
<tr>
<td class="fform">{VAR:LC_FORMS_NAME}:</td><td colspan=2 class="fform"><input type='text' NAME='name' VALUE='{VAR:name}'></td>
</tr>
<tr>
<td class="fform">{VAR:LC_FORMS_COMMENT}:</td><td colspan=2 class="fform"><textarea NAME='comment' cols=50 rows=5>{VAR:comment}</textarea></td>
</tr>
<tr>
<td class="fform">{VAR:LC_FORMS_FILL_ONE_TIME}:</td><td colspan=2 class="fform"><input type='checkbox' NAME='fillonce' VALUE='1' {VAR:fillonce}></td>
</tr>
<tr>
<td class="fform">{VAR:LC_FORMS_CHOOSE_FORMS}:</td><td colspan=2 class="fform"><select name='forms[]' multiple size=10>{VAR:forms}</select></td>
</tr>
</table>
<table bgcolor="#CCCCCC" cellpadding=3 cellspacing=1 border=0>
<tr>
<!-- SUB: LANG_H -->
<td class="fform">{VAR:LC_FORMS_NAME} {VAR:lang_name}</td>
<!-- END SUB: LANG_H -->
<td class="fform">{VAR:LC_FORMS_ORDER}</td><td class="fform">{VAR:LC_FORMS_AFTER_FILLING_GO_FORWARD}</td>
<td class="fform">Korduv?</td>
<td class="fform">Sisestused tabelis?</td>
<td class="fform">Tabel</td>
<td class="fform">V&auml;ljund tabelis</td>
<td class="fform">Form ainult n&auml;itamiseks</td>
<td class="fform">Formi n&auml;itamise kontrollerid</td>
</tr>
<!-- SUB: FORM -->
<tr>
<!-- SUB: LANG -->
<td class="fform"><input type='text' name='fname[{VAR:form_id}][{VAR:lang_id}]' value='{VAR:fname}' size=20 class='small_button'></td>
<!-- END SUB: LANG -->
<td class="fform"><input type='text' name='fjrk[{VAR:form_id}]' value='{VAR:fjrk}' size=3 class='small_button'></td>
<td class="fform"><input type='checkbox' name='fgoto[{VAR:form_id}]' value='1' {VAR:fgoto} class='small_button'></td>
<td class="fform"><input type='checkbox' name='rep[{VAR:form_id}]' value='1' {VAR:rep} class='small_button'></td>
<td class="fform"><input type='checkbox' name='show_reps[{VAR:form_id}]' value='1' {VAR:show_reps} class='small_button'></td>
<td class="fform"><select name='rep_tbls[{VAR:form_id}]' class='small_button'>{VAR:rep_tbls}</select></td>
<td class="fform"><select name='rep_ops[{VAR:form_id}]' class='small_button'>{VAR:rep_ops}</select></td>
<td class="fform"><input type="checkbox" class="small_button" value="1" name="no_load[{VAR:form_id}]" {VAR:no_load}></td>
<td class="fform"><select multiple name='controllers[{VAR:form_id}][]' class="small_button">{VAR:controllers}</select></td>
</tr>
<!-- END SUB: FORM -->
<tr>
<td class="fform" colspan=20><input type="checkbox" name="after_show_entry" value="1" {VAR:after_show_entry}> {VAR:LC_FORMS_AFTER_FILLING_LAST_FORM_SHOW_OUTPUT} <select class="small_button" name="after_show_op">{VAR:ops}</select></td>
</tr>
<tr>
<td class="fform" colspan=20><input type="checkbox" name="during_show_entry" value="1" {VAR:during_show_entry}> {VAR:LC_FORMS_FILLING_FORM_SHOW_OUTPUT}<input type="radio" name="op_pos" value="up" {VAR:op_up}> {VAR:LC_FORMS_UP} <input type="radio" name="op_pos" value="down" {VAR:op_down}> {VAR:LC_FORMS_DOWN}<input type="radio" name="op_pos" value="right" {VAR:op_right}> {VAR:LC_FORMS_IN_RIGHT} <input type="radio" name="op_pos" value="left" {VAR:op_left}> {VAR:LC_FORMS_IN_LEFT} <select class="small_button" name="during_show_op">{VAR:d_ops}</select></td>
</tr>
<tr>
<td class="fform" colspan=20><input type="checkbox" name="after_redirect" value="1" {VAR:after_redirect}> P&auml;rast t&auml;itmist Suuna &uuml;mber</td>
</tr>

<!-- SUB: REDIR_LANG -->
<tr>
<td class="fform" colspan=20>Aadress {VAR:lang_name}: <input type='text' name='after_redirect_url[{VAR:lang_id}]' value='{VAR:after_redirect_url}'> <a href="#" onclick="sel_el=document.b88.after_redirect_url;remote('no',500,400,'{VAR:search_doc}')">Saidi sisene link</a></td>
</tr>
<!-- END SUB: REDIR_LANG -->
<tr>
	<td class="fform" colspan="30">P&auml;rja sisesused salvestatakse kataloogi <select name='save_folder'>{VAR:folders}</select></td>
</tr>
<tr>
<td class="fform" colspan=30><input class='small_button' type='submit' VALUE='{VAR:LC_FORMS_SAVE}'></td>
</tr>
<tr>
<td class="fform" colspan=30><a href='{VAR:import}'>{VAR:LC_FORMS_IMPORT_ENTRIES}</a></td>
</tr>
<tr>
<td class="fform" colspan=30><a href='{VAR:entries}'>{VAR:LC_FORMS_ENTRIES}</a></td>
</tr>
<tr>
<td class="fform" colspan=30>
<table width="100%" cellspacing="1" border="0" bgcolor="#CCCCCC">
<tr>
<td class="fform" width="30%">
Pärja sisestustel on kalender
</td>
<td class="fform">
<input type="checkbox" name="has_calendar" value="1" {VAR:has_calendar}>
</td>
</tr>
<!--
<tr>
<td class="fform" width="30%">
Kalendri vorm<br>
<i>(see mille sisse on kalendri alias lisatud)</i>
</td>
<td class="fform">
<select name="cal_form">{VAR:cal_forms}</select>
</td>
</tr>
-->
<tr>
<td class="fform" width="30%">
Kalendri kontrollervorm<br>
<i>(vakantside defineerimine)</i>
</td>
<td class="fform">
<select name="cal_controller">{VAR:cal_controllers}</select>
</td>
</tr>
<!--
<tr>
<td class="fform" width="30%">
Eventite sisestusvorm<br>
<i>(millise vormi sisestusi kalendris arvestatakse)</i>
</td>
<td class="fform">
<select name="cal_entry_form">{VAR:cal_entry_forms}</select>
</td>
</tr>
-->
</table>
</td>
</tr>
</table>
{VAR:reforb}
</form>
