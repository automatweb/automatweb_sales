<script language='javascript'>

function varv(vrv) 
{
	document.ffrm.bgcolor.value="#"+vrv;
} 

function varvivalik() 
{
  aken=window.open("{VAR:baseurl}/automatweb/orb{VAR:ext}?class=css&action=colorpicker","varvivalik","HEIGHT=220,WIDTH=310");
 	aken.focus();
}

function setLink(li,title)
{
	document.ffrm.after_submit_link.value=li;
}

</script>
<table width="100%" cellpadding=1 cellspacing=0 border=0>

<form action='reforb{VAR:ext}' method=post name=ffrm>

<tr>
<td bgcolor="#FFFFFF">

<table width="100%" cellpadding=3 cellspacing=0 border=0>
<tr>
<td class="aste01">

<table cellpadding=3 cellspacing=0 border=0>
<tr class="aste01">
<td class="celltext">{VAR:LC_FORMS_TABLE_STYLE}:</td>
<td class="celltext"><select name='tablestyle' class="formselect2">{VAR:tablestyles}</select></td>
</tr>
<tr class="aste01">
<td class="celltext">{VAR:LC_FORMS_DEFAULT_STYLE}:</td>
<td class="celltext"><select name='def_style' class="formselect2"><option VALUE=''>{VAR:def_style}</select>
</td>
</tr>
<tr class="aste01">
<td class="celltext" colspan=2>{VAR:LC_FORMS_TRY_JF_DATA}: &nbsp;<input type='checkbox' name='try_fill' value=1 {VAR:try_fill}></td>
</tr>
<tr class="aste01">
<td class="celltext">{VAR:LC_FORMS_FORM_HAS_ALIASMGR}:</td>
<td class="celltext"><input type="checkbox" name="has_aliasmgr" value="1" {VAR:has_aliasmgr}></td>
</tr>
<tr class="aste01">
<td class="celltext">{VAR:LC_FORMS_ALLOW_HTML}:</td>
<td class="celltext"><input type='checkbox' name='allow_html' value=1 {VAR:allow_html}></td>
</tr>
<tr class="aste01">
<td class="celltext">{VAR:LC_FORMS_FORM_HAS_CONTROLLERS}:</td>
<td class="celltext"><input type='checkbox' name='has_controllers' value=1 {VAR:has_controllers}></td>
</tr>
<tr class="aste01">
<td class="celltext">Side kalendriga:</td>
<td class="celltext"><select name="calendar_role">{VAR:roles}></select></td>
</tr>
<tr class="aste01">
<td class="celltext">Vormi sisestustel on kalender:</td>
<td class="celltext"><input type='checkbox' name='has_calendar' value=1 {VAR:has_calendar}></td>
</tr>
<tr class="aste01">
<td class="celltext">Kalendri vea kontroller:</td>
<td class="celltext"><select name='calendar_controller'>{VAR:calendar_controllers}</select></td>
</tr>
<tr class="aste01">
<td class="celltext" colspan=2>{VAR:LC_FORMS_CAN_EMAIL_ACTION}: &nbsp;<input type='checkbox' name='email_form_action' value=1 {VAR:email_form_action}></td>
</tr>
<tr class="aste01">
<td class="celltext" colspan=2>{VAR:LC_FORMS_CONTROL_FORM_STATUS}?: &nbsp;<input type='checkbox' name='check_status' value=1 {VAR:check_status}>
</td></tr>
<tr><td class="celltext">{VAR:LC_FORMS_TEXT_DISPLAY_USER}:</td>
<td><input type="text" name="check_status_text" value="{VAR:check_status_text}" size="40" class="formtext"></td>
</tr>
<!-- SUB: NOSEARCH -->
<tr class="aste01">
<td class="celltext" colspan=2>{VAR:LC_FORMS_AFTER_FILLING}:</td>
</tr>
<tr class="aste01">
<td class="celltext"><input type='radio' NAME='after_submit' VALUE='1' {VAR:as_1}>{VAR:LC_FORMS_CHANGE_INPUT}</td>
<td class="celltext">&nbsp;</td>
</tr>
<tr class="aste01">
<td colspan="2" class="celltext"><input type='radio' NAME='after_submit' VALUE='3' {VAR:as_3}>{VAR:LC_FORMS_GOT_TO_ADDRESS}:</td>
</tr>
<!-- SUB: ASL_LANG -->
<tr class="aste01">
<td colspan="2" class="celltext">({VAR:lang_name}): <input class="formtext" type='text' NAME='after_submit_link[{VAR:lang_id}]' value='{VAR:after_submit_link}' size="30"> <a href="javascript:remote('no',500,400,'{VAR:search_doc}')">{VAR:LC_FORMS_INTRA_LINK}</a></td>
</tr>
<!-- END SUB: ASL_LANG -->
<tr class="aste01">
<td class="celltext"><input type='radio' NAME='after_submit' VALUE='4' {VAR:as_4}>{VAR:LC_FORMS_SHOW_ENTRIES}:</td>
<td class="celltext"><select name="after_submit_op">{VAR:ops}</select></td>
</tr>
<tr class="aste01">
<td class="celltext">{VAR:LC_FORMS_SQL_WRITER_WRITER}:</td>
<td class="celltext"><input type='checkbox'  NAME='sql_writer_writer' value='1' {VAR:sql_writer_writer}></td>
</tr>
<tr class="aste01">
<td class="celltext">{VAR:LC_FORMS_SQL_WRITER_WRITER_FORM}:</td>
<td class="celltext"><select class="formselect2" name='sql_writer_writer_form'>{VAR:sql_writer_writer_forms}</select></td>
</tr>
<!-- END SUB: NOSEARCH -->

<!-- SUB: SEARCH -->
<tr class="aste01">
<td class="celltext" colspan=2>{VAR:LC_FORMS_SEARCH_RESULTS_SHOW_TABLE}? <input type='checkbox' NAME='show_table' value='1' {VAR:show_table_checked}></td>
</tr>
<tr class="aste01">
<td class="celltext">{VAR:LC_FORMS_CHOOSE_TABLE}:</td>
<td class="celltext"><select name='table' class="formselect2">{VAR:tables}</select></td>
</tr>
<tr class="aste01">
<td class="celltext">{VAR:LC_FORMS_SQL_WRITER}:</td>
<td class="celltext"><input type='checkbox' NAME='sql_writer' value='1' {VAR:sql_writer}></td>
</tr>
<tr class="aste01">
<td class="celltext">{VAR:LC_FORMS_SQL_WRITER_FORM}:</td>
<td class="celltext"><select class="formselect2" name='sql_writer_form'>{VAR:forms}</select></td>
</tr>
<tr class="aste01">
<td class="celltext">Kuhu suunata p&auml;rast kirjutamist:</td>
<td class="celltext"><input type="text" name='sql_writer_redirect_after' class='formtext' value='{VAR:sql_writer_redirect_after}' size="50"></td>
</tr>
<tr class="aste01">
<td class="celltext">Form otsib ainult aktiivse keele alt:</td>
<td class="celltext"><input type="checkbox" name="search_act_lang_only" value="1" {VAR:search_act_lang_only}></td>
</tr>
<tr class="aste01">
<td class="celltext" colspan=2>{VAR:LC_FORMS_SHOW_FORM_WITH_RESULTS}: <input type='checkbox' NAME='show_form_with_results' value='1' {VAR:show_form_with_results}></td>
</tr>
<!-- SUB: SEARCH_OP -->
<tr class="aste01">
<td class="celltext">Sorteeri v&auml;ljundid v&auml;lja </td>
<td class="celltext"><select name="sort_op_by[{VAR:sop_nr}]">{VAR:s_op_elements}</select> j&auml;rgi <select name="sort_op_order[{VAR:sop_nr}]">{VAR:s_op_orders}</select> j&auml;rjekorras</td>
</tr>
<!-- END SUB: SEARCH_OP -->

<!-- END SUB: SEARCH -->
<tr class="aste01">
<td class="celltext" colspan=2>{VAR:LC_FORMS_CHOOSE_ELEMENT_WHAT_PUT_FORM_ENTRY}</td>
</tr>
<tr class="aste01">
<td colspan=2 class="celltext"><select class="formselect2" NAME='entry_name_el[]' multiple>{VAR:els}</select></td>
</tr>
<tr class="aste01">
<td class="celltext">Kas peita t&uuml;hjad read:</td>
<td class="celltext"><input type="checkbox" name="hide_empty_rows" value="1" {VAR:hide_empty_rows}></td>
</tr>
<tr class="aste01">
<td class="celltext">T&ouml;lgitav?:</td>
<td class="celltext"><input type="checkbox" name="is_translatable" value="1" {VAR:is_translatable}></td>
</tr>
<!-- SUB: IS_TRANSLATABLE -->

<tr class="aste01">
<td class="celltext">T&otilde;lkides {VAR:lang_name} keelde minnakse sektsiooni:</td>
<td class="celltext"><input type="text" name="trans_sect[{VAR:lang_id}]" value="{VAR:trans_sect}" class="formtext" size="10"></td>
</tr>

<!-- END SUB: IS_TRANSLATABLE -->

<tr class="aste01">
<td class="celltext">&Auml;ra n&auml;ita t&otilde;lkimise linki:</td>
<td class="celltext"><input type="checkbox" name="dont_show_trans" value="1" {VAR:dont_show_trans} ></td>
</tr>

<tr class="aste01">
<td class="celltext">&Auml;ra j&auml;ta meelde poolikut sisestust:</td>
<td class="celltext"><input type="checkbox" name="no_use_eid_once" value="1" {VAR:no_use_eid_once}></td>
</tr>
<tr class="aste01">
<td class="celltext">Javascriptiga m&auml;&auml;ratav default element:</td>
<td class="celltext"><select name="js_default_element" class="formselect2">{VAR:js_default_element}</select></td>
</tr>
<tr class="aste01">
<td class="celltext">Pessimistlik p&auml;ringute generaator:</td>
<td class="celltext"><input type="checkbox" name="join_optimizer_pessimist" value="1" {VAR:join_optimizer_pessimist}></td>
</tr>

<tr class="aste01">
<td></td>
<td class="celltext"><input class='formbutton' type='submit' NAME='save_form_settings' VALUE='{VAR:LC_FORMS_SAVE}'></td>
</table>
</td></tr></table>
{VAR:reforb}


</td></tr>

</form>

</table>
  <br>
