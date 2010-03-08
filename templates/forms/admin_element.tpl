<script language="javascript">
if (typeof(elements) == "undefined")
{
	var elements = new Array();
}
</script>
<!-- SUB: SEARCH_DEFS -->
<script language="javascript">
// elements = array(form_id, el_id,el_text);

<!-- SUB: ELDEFS -->
elements[{VAR:el_num}] = new Array({VAR:form_id},{VAR:el_id},"{VAR:el_text}");
<!-- END SUB: ELDEFS -->

</script>
<!-- END SUB: SEARCH_DEFS -->

<!-- SUB: TABLE_DEFS -->
<script language="javascript">

<!-- SUB: TBL -->
elements[{VAR:tbl_num}] = new Array("{VAR:table_name}","{VAR:col_name}","{VAR:col_name}");
<!-- END SUB: TBL -->

</script>
<!-- END SUB: TABLE_DEFS -->

<!-- SUB: SEARCH_SCRIPT -->
<script language="javascript">

function clearList(list)
{
	var listlen = list.length;

	for(i=0; i < listlen; i++)
		list.options[0] = null;
}

function ch(el, f_el,suf)
{
	var sf = f_el.options[f_el.selectedIndex].value;

	clearList(el);
	el.options[el.length] = new Option("","",false,false);
	for (i=1; i < (elements.length+1); i++)
	{
		if (typeof(elements[i-1]) != "undefined")
		{
			if (elements[i-1][0] == sf)
			{
				el.options[el.length] = new Option(elements[i-1][2],""+elements[i-1][1],false,false);
			}
		}
	}
}

function setsel(el,val)
{
	for (i=0; i < el.length; i++)
	{
		if (el.options[i].value==val)
		{
			el.options[i].selected = true;
			return;
		}
	}
}

function toggle_file_link_newwin()
{
	alert(document.f1.{VAR:cell_id}_filetype);
}
</script>
<!-- END SUB: SEARCH_SCRIPT -->

<table border="0" cellspacing="1" cellpadding="2" bgcolor="#CCCCCC" width=100%>
<!-- SUB: SEARCH_LB -->
<tr>
	<td class="fgtext">{VAR:LC_FORMS_FORM_WHERE_ELEMENT_IS_TAKEN}:</td>
	<td class="fgtext"><select class='small_button' NAME='{VAR:cell_id}_form' onChange="ch(document.f1.{VAR:cell_id}_element, this)">{VAR:forms}</select></td>
	<td class="fgtext">{VAR:LC_FORMS_ELEMENT_FROM_FORM}:</td>
	<td class="fgtext"><select class='small_button' NAME='{VAR:cell_id}_element'><option value=''></select>
	<script language="javascript">
		ch(document.f1.{VAR:cell_id}_element, document.f1.{VAR:cell_id}_form);
		setsel(document.f1.{VAR:cell_id}_element,"{VAR:linked_el}");
	</script>
	</td>
</tr>
<!-- END SUB: SEARCH_LB -->

<!-- SUB: TABLE_LB -->
<tr>
	<td class="fgtext">{VAR:LC_FORMS_TABLE}:</td>
	<td class="fgtext"><select class='small_button' NAME='{VAR:cell_id}_table_{VAR:num}' onChange="ch(document.f1.{VAR:cell_id}_tbl_col_{VAR:num}, this)">{VAR:tables}</select></td>
	<td class="fgtext">{VAR:LC_FORMS_COLUMN_IN_TABLE}:</td>
	<td class="fgtext"><select class='small_button' NAME='{VAR:cell_id}_tbl_col_{VAR:num}'><option value=''></select>
	<script language="javascript">
		ch(document.f1.{VAR:cell_id}_tbl_col_{VAR:num}, document.f1.{VAR:cell_id}_table_{VAR:num});
		setsel(document.f1.{VAR:cell_id}_tbl_col_{VAR:num},"{VAR:table_col}");
	</script>
	</td>
</tr>
<!-- END SUB: TABLE_LB -->

<!-- SUB: FILTER_PART_LB -->
<tr>
<td class="fgtext">{VAR:LC_FORMS_EL_REAL_FILTR_PART}:</td>
<td class="fgtext" colspan="3"><select class="small_button" name='{VAR:cell_id}_part'>{VAR:parts}</select></td>
</tr>
<!-- END SUB: FILTER_PART_LB -->
<tr>
	<td class="fgtext">{VAR:LC_FORM_TYPE}:</td>
	<td class="fgtext"><select class="small_button" NAME='{VAR:cell_id}_type'>
    <option  VALUE=''>{VAR:LC_FORMS_ORDINARY_TEXT}
    <option  VALUE=''>---------
		{VAR:types}
<!-- SUB: CAN_DELETE -->
    <option VALUE='delete'>{VAR:LC_FORMS_DELETE_THIS_ELEMENT}
<!-- END SUB: CAN_DELETE -->
    </select>
<!-- SUB: HAS_SUBTYPE -->
&nbsp;{VAR:LC_FORMS_SUBTYPE}:&nbsp;
<select name='{VAR:cell_id}_subtype' class="small_button">{VAR:subtypes}</select>
<!-- END SUB: HAS_SUBTYPE -->
		</td>
	<td class="fgtext">{VAR:LC_FORMS_NAME}:</td>
	<td class="fgtext"><input type='text' class="small_button" NAME='{VAR:cell_id}_name' VALUE='{VAR:cell_name}'></td>
</tr>
<tr>
	<td class="fgtext">{VAR:LC_FORMS_IGNORE_TEXT}:</td>
	<td class="fgtext"><input type='checkbox' name='{VAR:cell_id}_ignore_text' {VAR:ignore_text}></td>
	<td class="fgtext">{VAR:LC_FORMS_TYPE_NAME}:</td>
	<td class="fgtext"><input type='text' class="small_button" NAME='{VAR:cell_id}_type_name' VALUE='{VAR:cell_type_name}'></td>
</tr>
<!-- SUB: RELATION_LB -->
<tr>
	<td class="fgtext">{VAR:LC_FORMS_REAL_FORM}:</td>
	<td class="fgtext"><select class='small_button' NAME='{VAR:cell_id}_rel_form' onChange="ch(document.f1.{VAR:cell_id}_rel_element, this)">{VAR:rel_forms}</select></td>
	<td class="fgtext">{VAR:LC_FORMS_REAL_EL}:</td>
	<td class="fgtext"><select class='small_button' NAME='{VAR:cell_id}_rel_element'><option value=''></select>
	<script language="javascript">
		ch(document.f1.{VAR:cell_id}_rel_element, document.f1.{VAR:cell_id}_rel_form);
		setsel(document.f1.{VAR:cell_id}_rel_element,"{VAR:rel_el}");
	</script>
	</td>
</tr>
<!-- END SUB: RELATION_LB -->

<!-- SUB: RELATION_LB_SHOW -->
<tr>
	<td class="fgtext">Elemendid mida n&auml;idatakse:</td>
	<td class="fgtext"><select class="small_button" MULTIPLE name="{VAR:cell_id}_rel_element_show[]">{VAR:rel_show_elements}</select></td>
	<td class="fgtext">&nbsp;</td>
	<td class="fgtext">&nbsp;</td>
</tr>
<tr>
	<td class="fgtext">Element</td>
	<td class="fgtext">J&auml;rjekord</td>
	<td class="fgtext">Eraldaja</td>
	<td class="fgtext">&nbsp;</td>
</tr>
<!-- SUB: REL_LINE -->
<tr>
	<td class="fgtext">{VAR:rel_el_n}</td>
	<td class="fgtext"><input type='text' name='{VAR:cell_id}_rel_element_show_order[{VAR:r_id}]' class='small_button' size='3' value='{VAR:r_el_ord}'></td>
	<td class="fgtext"><input type='text' name='{VAR:cell_id}_rel_element_show_sep[{VAR:r_id}]' class='small_button' size='3' value='{VAR:r_el_sep}'></td>
	<td class="fgtext">&nbsp;</td>
</tr>
<!-- END SUB: REL_LINE -->

<!-- END SUB: RELATION_LB_SHOW -->

<!-- SUB: SEARCH_RELATION -->
<tr>
	<td class="fgtext">{VAR:LC_FORMS_ONLY_UNIC}:</td>
	<td class="fgtext">&nbsp;<input type='checkbox' class='small_button' value='1' name='{VAR:cell_id}_unique' {VAR:unique}></td>
	<td class="fgtext">Ainult kasutaja enda sisestused:</td>
	<td class="fgtext">&nbsp;<input type='checkbox' class='small_button' value='1' name='{VAR:cell_id}_user_entries_only' {VAR:user_entries_only}></td>
</tr>
<tr>
	<td class="fgtext">&nbsp;</td>
	<td class="fgtext">&nbsp;</td>
	<td class="fgtext">Vali, kellele n&auml;idatakse k&otilde;iki:</td>
	<td class="fgtext">&nbsp;<select name="{VAR:cell_id}_user_entries_only_exclude[]" multiple=1>{VAR:user_entries_only_exclude}</select></td>
</tr>
<tr>
	<td class="fgtext">Ainult selle p&auml;rja sisestused:</td>
	<td class="fgtext">&nbsp;<input type='checkbox' class='small_button' value='1' name='{VAR:cell_id}_chain_entries_only' {VAR:chain_entries_only}></td>
	<td class="fgtext">&nbsp;</td>
	<td class="fgtext">&nbsp;</td>
</tr>
<!-- END SUB: SEARCH_RELATION -->

<!-- SUB: CONFIG_KEY -->
<tr>
	<!-- here we have to display all the config keys that match the current type -->
	<td class="fgtext">Konfiguratsioonivõti:</td>
	<td class="fgtext">&nbsp;<select class='small_button' name='{VAR:cell_id}_config_keyi'></select></td>
	<td class="fgtext">&nbsp;</td>
	<td class="fgtext">&nbsp;</td>
</tr>
<!-- END SUB: CONFIG_KEY -->

<!-- SUB: LB_MUL_DS -->
<tr>
	<td class="fgtext">Andmed formist:</td>
	<td class="fgtext"><select class="small_button"  name="{VAR:cell_id}_lb_data_from_form">{VAR:lb_data_from_form}</select></td>
<td class="fgtext">Andmete element:</td>
<td class="fgtext"><select class="small_button" name="{VAR:cell_id}_lb_data_from_el">{VAR:lb_data_from_el}</select></td>
</tr>
<tr>
	<td class="fgtext">Otsing alamstringist:</td>
	<td class="fgtext"><input type="checkbox" value="1" name="{VAR:cell_id}_lb_search_like" {VAR:lb_search_like}></td>
	<td class="fgtext">Sortimise element:</td>
	<td class="fgtext"><select class="small_button" name="{VAR:cell_id}_lb_data_from_el_sby">{VAR:lb_data_from_el_sby}</select></td>
</tr>
<!-- END SUB: LB_MUL_DS -->

<!-- SUB: MULTIPLE_OPTS -->
<tr>
	<td class="fgtext">Valitud ridade eraldaja:</td>
	<td class="fgtext"><input type='text' class='small_button' name='{VAR:cell_id}_mul_items_sep' value='{VAR:mul_items_sep}' size="3"></td>
	<td class="fgtext">&nbsp;</td>
	<td class="fgtext">&nbsp;</td>
</tr>
<!-- END SUB: MULTIPLE_OPTS -->


<!-- SUB: LISTBOX_SORT -->
<tr>
<td class="fgtext">Listboksi valik submitib formi:</td>
<td class="fgtext"><input class="small_button" type='checkbox' NAME='{VAR:cell_id}_submit_on_select' value='1' {VAR:submit_on_select}></td>
<td class="fgtext">onChange:</td>
<td class="fgtext"><input class="small_button" type="text" name="{VAR:cell_id}_onChange" value="{VAR:onChange}"></td>
</tr>
<tr>
<td class="fgtext">{VAR:LC_FORMS_SORTING}:</td>
<td class="fgtext"><input class="small_button" type='checkbox' NAME='{VAR:cell_id}_sort_order' value='1' {VAR:sort_by_order}>&nbsp;{VAR:LC_FORMS_BY_ORDER} <input type='checkbox' NAME='{VAR:cell_id}_sort_alpha' VALUE='1'  {VAR:sort_by_alpha}>&nbsp;{VAR:LC_FORMS_BY_ALPHABET}</td>
<td class="fgtext">{VAR:LC_FORMS_IMPORT}:</td>
<td class="fgtext"><input type='file' name='{VAR:cell_id}_import' class='small_button'></td>
</tr>
<tr>
<td class="fgtext">{VAR:LC_FORMS_IS_MARKED_ELEMENTS}:</td>
<td class="fgtext"><input class="small_button" type='radio' NAME='{VAR:cell_id}_lbitems_dowhat' value='del' >&nbsp;{VAR:LC_FORMS_WILL_BE_DELETED} <input type='radio' NAME='{VAR:cell_id}_lbitems_dowhat' VALUE='add'>&nbsp;{VAR:LC_FORMS_ADDING_NEW}</td>
<td class="fgtext">{VAR:LC_FORMS_SIZE}:</td>
<td class="fgtext"><input type="text" name="{VAR:cell_id}_lb_size" size=3 class='small_button' value='{VAR:lb_size}'></td>
</tr>
<tr>
	<td class="fgtext">&nbsp;</td>
	<td class="fgtext" valign="bottom">Sisu&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Default&nbsp;&nbsp;Jrk&nbsp;&nbsp;Vali
	<!-- SUB: LISTBOX_SORT_ACTIVITY -->
	&nbsp;Aktiivsuse pikendamine
	<!-- END SUB: LISTBOX_SORT_ACTIVITY -->
	</td>
	<!-- SUB: LB_ITEM_CONTROLLER -->
	<td class="fgtext">Vali listboxi elementide n&auml;itamise kontroller(id):</td>
	<td class="fgtext"><select multiple CLASS="small_button" NAME='{VAR:cell_id}_lb_item_controllers[]'>{VAR:lb_item_controllers}</select></td>
	<!-- END SUB: LB_ITEM_CONTROLLER -->

	<!-- SUB: NO_ITEM_CONTROLLER -->
	<td class="fgtext">&nbsp;</td>
	<td class="fgtext">&nbsp;</td>
	<!-- END SUB: NO_ITEM_CONTROLLER -->

</tr>
<!-- END SUB: LISTBOX_SORT -->

<!-- SUB: LISTBOX_ITEMS -->
<tr>
<td class="fgtext">&nbsp;</td>
<td class="fgtext"><input class="small_button" type='text' NAME='{VAR:listbox_item_id}' VALUE='{VAR:listbox_item_value}'>&nbsp;<input type='radio' NAME='{VAR:listbox_radio_name}' VALUE='{VAR:listbox_radio_value}' {VAR:listbox_radio_checked}>&nbsp;<input type='text' name='{VAR:listbox_order_name}' value='{VAR:listbox_order_value}' class='small_button' size=4>&nbsp;<input type='checkbox' name='{VAR:cell_id}_sel[{VAR:num}]' value='1'>
<!-- SUB: LISTBOX_ITEMS_ACTIVITY -->
&nbsp;<input type='text' name='{VAR:listbox_activity_name}' value='{VAR:listbox_activity_value}' class='small_button' size=4>
<!-- END SUB: LISTBOX_ITEMS_ACTIVITY -->
</td>
<td class="fgtext">&nbsp;</td>
<td class="fgtext">&nbsp;</td>
</tr>
<!-- END SUB: LISTBOX_ITEMS -->

<!-- SUB: MULTIPLE_ITEMS -->
<tr>
<td class="fgtext">&nbsp;</td>
<td class="fgtext"><input CLASS="small_button" type='text' NAME='{VAR:multiple_item_id}' VALUE='{VAR:multiple_item_value}'>&nbsp;<input CLASS="small_button" type='checkbox' NAME='{VAR:multiple_check_name}' VALUE='{VAR:multiple_check_value}' {VAR:multiple_check_checked}>&nbsp;<input type='text' name='{VAR:multiple_order_name}' value='{VAR:multiple_order_value}' class='small_button' size=4>&nbsp;<input type='checkbox' name='{VAR:cell_id}_sel[{VAR:num}]' value='1'></td>
	<td class="fgtext">&nbsp;</td>
	<td class="fgtext">&nbsp;</td>
</tr>
<!-- END SUB: MULTIPLE_ITEMS -->

<!-- SUB: TEXTAREA_ITEMS -->
<tr>
<td class="fgtext">{VAR:LC_FORMS_SIZE}:</td>
<td class="fgtext">&nbsp;{VAR:LC_FORMS_WITDH}:&nbsp;<input CLASS="small_button" SIZE=3 type='text' NAME='{VAR:textarea_cols_name}' VALUE='{VAR:textarea_cols}'>{VAR:LC_FORMS_HIGHT}:&nbsp;<input CLASS="small_button" SIZE=3 type='text' NAME='{VAR:textarea_rows_name}' VALUE='{VAR:textarea_rows}'></td>
<td valign=top class="fgtext">{VAR:LC_FORMS_ORIGINAL_TEXT}:</td>
<td class="fgtext"><input type=text CLASS="small_button"  SIZE=45 NAME='{VAR:default_name}' VALUE='{VAR:default}'></td>
</tr>
<tr>
<td class="fgtext">WYSIWYG:</td>
<td class="fgtext">&nbsp;<input type='checkbox' name='{VAR:cell_id}_wysiwyg' value='1' {VAR:is_wysiwyg}></td>
<td valign=top class="fgtext">&nbsp;</td>
<td class="fgtext">&nbsp;</td>
</tr>
<!-- END SUB: TEXTAREA_ITEMS -->

<!-- SUB: DATE_ITEMS -->
<tr>
<td class="fgtext">{VAR:LC_FORMS_FROM_YEAR_TO_YEAR}:</td>
<td class="fgtext">&nbsp;<input CLASS="small_button" SIZE=5 type='text' NAME='{VAR:cell_id}_from_year' VALUE='{VAR:from_year}'>&nbsp;-&nbsp;<input type=text CLASS="small_button"  SIZE=5 NAME='{VAR:cell_id}_to_year' VALUE='{VAR:to_year}'></td>
<td valign=top class="fgtext" colspan="2">
<table border=0 cellpadding=0 cellspacing=0>
<tr>
<td class="fgtext">&nbsp;{VAR:LC_FORMS_YEAR}:</td>
<td class="fgtext"><input type='checkbox' name='{VAR:cell_id}_has_year' value='1' {VAR:has_year}></td>
<td class="fgtext">&nbsp;<input type='text' name='{VAR:cell_id}_year_ord' value='{VAR:year_ord}' size="2" class="small_button"></td>
<td class="fgtext">&nbsp;textbox <input type='checkbox' name='{VAR:cell_id}_year_textbox' value='1' {VAR:year_textbox}></td>
</tr>
<tr>
<td class="fgtext">&nbsp;{VAR:LC_FORMS_MONTH}:</td>
<td class="fgtext"><input type='checkbox' name='{VAR:cell_id}_has_month' value='1' {VAR:has_month}></td>
<td class="fgtext">&nbsp;<input type='text' name='{VAR:cell_id}_month_ord' value='{VAR:month_ord}' size="2" class="small_button"></td>
<td class="fgtext">&nbsp;textbox <input type='checkbox' name='{VAR:cell_id}_month_textbox' value='1' {VAR:month_textbox}></td>
</tr>
<tr>
<td class="fgtext">&nbsp;{VAR:LC_FORMS_DAY}:</td>
<td class="fgtext"><input type='checkbox' name='{VAR:cell_id}_has_day' value='1' {VAR:has_day}></td>
<td class="fgtext">&nbsp;<input type='text' name='{VAR:cell_id}_day_ord' value='{VAR:day_ord}' size="2" class="small_button"></td>
<td class="fgtext">&nbsp;textbox <input type='checkbox' name='{VAR:cell_id}_day_textbox' value='1' {VAR:day_textbox}></td>
</tr>
<tr>
<td class="fgtext">&nbsp;{VAR:LC_FORMS_HOUR}:</td>
<td class="fgtext"><input type='checkbox' name='{VAR:cell_id}_has_hr' value='1' {VAR:has_hr}></td>
<td class="fgtext">&nbsp;<input type='text' name='{VAR:cell_id}_hr_ord' value='{VAR:hr_ord}' size="2" class="small_button"></td>
<td class="fgtext">&nbsp;textbox <input type='checkbox' name='{VAR:cell_id}_hr_textbox' value='1' {VAR:hr_textbox}></td>
</tr>
<tr>
<td class="fgtext">&nbsp;{VAR:LC_FORMS_MINUT}:</td>
<td class="fgtext"><input type='checkbox' name='{VAR:cell_id}_has_minute' value='1' {VAR:has_minute}></td>
<td class="fgtext">&nbsp;<input type='text' name='{VAR:cell_id}_minute_ord' value='{VAR:minute_ord}' size="2" class="small_button"></td>
<td class="fgtext">&nbsp;textbox <input type='checkbox' name='{VAR:cell_id}_minute_textbox' value='1' {VAR:minute_textbox}></td>
</tr>
<tr>
<td class="fgtext">&nbsp;{VAR:LC_FORMS_SECUND}:</td>
<td class="fgtext"><input type='checkbox' name='{VAR:cell_id}_has_second' value='1' {VAR:has_second}></td>
<td class="fgtext">&nbsp;<input type='text' name='{VAR:cell_id}_second_ord' value='{VAR:second_ord}' size="2" class="small_button"></td>
<td class="fgtext">&nbsp;textbox <input type='checkbox' name='{VAR:cell_id}_second_textbox' value='1' {VAR:second_textbox}></td>
</tr>
</table>
</td>
</tr>
<tr>
<td class="fgtext">{VAR:LC_FORMS_DATE_FOMAT_SHOW}:</td>
<td class="fgtext"><input type='text' name='{VAR:cell_id}_date_format' VALUE='{VAR:date_format}' class='small_button'></td>
<td class="fgtext" colspan="2">&nbsp;
</td>
</tr>
<tr>
<td class="fgtext">{VAR:LC_FORMS_DEFAULT_DATE}</td>
<td class="fgtext" align="right">
&nbsp;<input type="radio" name="{VAR:cell_id}_def_date_type" VALUE="none" {VAR:date_none_checked}> T&uuml;hi <input type="radio" name="{VAR:cell_id}_def_date_type" VALUE="rel" {VAR:date_rel_checked}> {VAR:C_FORMS_DATE_IN_ELEMENT}
<select name='{VAR:cell_id}_def_date_rel' class='small_button'>{VAR:date_rel_els}</select>&nbsp;&nbsp;&nbsp;&nbsp;
<input type="radio" name="{VAR:cell_id}_def_date_type" VALUE="now" {VAR:date_now_checked}> {VAR:LC_FORMS_TIME} </td>
<td class="fgtext" colspan=2>pluss&nbsp;<input type="text" class="small_button" size="5" name="{VAR:cell_id}_def_date_num" value="{VAR:def_date_num}">&nbsp;<select name='{VAR:cell_id}_def_date_add_type' class="small_button">{VAR:add_types}</select>&nbsp;</td>
</tr>
<tr>
<td class="fgtext">Visuaal</td>
<td class="fgtext" colspan="3">
<input type="checkbox" name="{VAR:cell_id}_visual_use_textbox" {VAR:visual_use_textbox}> Kasuta kuupäevade sisestamiseks tekstiboksi
</td>
</tr>
<!-- END SUB: DATE_ITEMS -->

<!-- SUB: FILE_ITEMS -->
<tr>
<td class="fgtext">{VAR:LC_FORMS_DISPLAYING}:</td>
<td class="fgtext"><input CLASS="small_button" type='radio' NAME='{VAR:cell_id}_filetype' VALUE='1' {VAR:ftype_image_selected}> {VAR:LC_FORMS_AS_A_PICTURE} <input CLASS="small_button" type='radio' NAME='{VAR:cell_id}_filetype' VALUE='2' {VAR:ftype_file_selected}> {VAR:LC_FORMS_LINKED_AS_FILE}
<input type='checkbox' name='{VAR:cell_id}_file_new_win' value=1 {VAR:file_new_win}> {VAR:LC_FORMS_LINK_IN_NEW_WIN}
<input type="button" onClick="toggle_file_link_newwin()">
</td>
<td class="fgtext">{VAR:LC_FORMS_LINK_TEXT}:</td>
<td class="fgtext"><input CLASS="small_button" type='text' NAME='{VAR:cell_id}_file_link_text' VALUE='{VAR:file_link_text}'></td>
</tr>
<tr>
<td class="fgtext">&nbsp;</td>
<td class="fgtext"><input type='radio' NAME='{VAR:cell_id}_file_show' VALUE=1 {VAR:file_show}> {VAR:LC_FORMS_DISPLAY_NOW} <input type='radio' NAME='{VAR:cell_id}_file_show' VALUE=0 {VAR:file_alias}> {VAR:LC_FORMS_MAKING_ALIAS}</td>
<td class="fgtext">
Kustuta lingi tekst:
</td>
<td class="fgtext"><input CLASS="small_button" type='text' NAME='{VAR:cell_id}_file_delete_link_text' VALUE='{VAR:file_delete_link_text}'></td>
</tr>
<tr>
<td class="fgtext">CSS class</td>
<td class="fgtext"><input class='small_button' type='text' name='{VAR:cell_id}_button_css_class' value='{VAR:button_css_class}'></td>
<td class="fgtext">&nbsp;</td>
<td class="fgtext">&nbsp;</td>
</tr>

<!-- END SUB: FILE_ITEMS -->

<!-- SUB: HLINK_ITEMS -->
<tr>
<td class="fgtext">{VAR:LC_FORMS_LINK_TEXT}:</td>
<td class="fgtext"><input type='text' CLASS="small_button" NAME='{VAR:cell_id}_link_text' VALUE='{VAR:link_text}'></td>
<td class="fgtext">{VAR:LC_FORMS_DESCRIBE_TEXT}:</td>
<td class="fgtext"><input type='text' CLASS="small_button" NAME='{VAR:cell_id}_link_address' VALUE='{VAR:link_address}'></td>
</tr>
<tr>
<td class="fgtext">Uues aknas:</td>
<td class="fgtext"><input type='checkbox' NAME='{VAR:cell_id}_link_newwindow' VALUE='1' {VAR:link_newwindow}></td>
<td class="fgtext">&nbsp;</td>
<td class="fgtext">&nbsp;</td>
</tr>
<!-- END SUB: HLINK_ITEMS -->

<!-- SUB: CALENDAR_LINK -->
<tr>
<td class="fgtext">Kalendri lingi tüüp</td>
<td class="fgtext"><select name="{VAR:cell_id}_clink_target">{VAR:clink_targets}</select></td>
<td class="fgtext">Saidi raamis?</td>
<td class="fgtext"><input type="checkbox" name="{VAR:cell_id}_clink_no_orb" {VAR:clink_no_orb}></td>
</tr>
<!-- END SUB: CALENDAR_LINK -->

<!-- SUB: RADIO_ITEMS -->
<tr>
<td class="fgtext">{VAR:LC_FORMS_GROUP}:</td>
<td class="fgtext"><input class="small_button" type='text' SIZE=1 NAME='{VAR:cell_id}_group' VALUE='{VAR:cell_group}'></td>
<td class="fgtext">{VAR:LC_FORMS_ORIGINALLY_SELECTED}:</td>
<td class="fgtext"><input type='checkbox' NAME='{VAR:default_name}' VALUE='1' {VAR:default_checked}></td>
</tr>
<tr>
<td class="fgtext">{VAR:LC_FORMS_RADIO_VALUE}</td>
<td class="fgtext"><input type='text' name='{VAR:cell_id}_ch_value' value='{VAR:ch_value}' class='small_button'></td>
<td class="fgtext">&nbsp;</td>
<td class="fgtext">&nbsp;</td>
</tr>
<!-- END SUB: RADIO_ITEMS -->

<!-- SUB: DEFAULT_TEXT -->
<tr>
<td class="fgtext">{VAR:LC_FORMS_ORIGINALLY_SELECTED}:</td>
<td class="fgtext"><input type='text' CLASS="small_button" NAME='{VAR:default_name}' VALUE='{VAR:default}'></td>
<td class="fgtext">{VAR:LC_FORMS_LENGTH}:</td>
<td class="fgtext"><input type='text' CLASS="small_button" SIZE=3 NAME='{VAR:cell_id}_length' VALUE='{VAR:length}'></td>
</tr>
<!-- END SUB: DEFAULT_TEXT -->

<!-- SUB: BUTTON_SUB_URL -->
<tr>
<td class="fgtext">URL:</td>
<td class="fgtext"><input type='text' CLASS="small_button" NAME='{VAR:cell_id}_burl' VALUE='{VAR:button_url}'></td>
<td class="fgtext">Kas nupu vajutamisel tehakse formi sisestus?</td>
<td class="fgtext"><input type="checkbox" name="{VAR:cell_id}_bt_redir_after_submit" {VAR:bt_redir_after_submit} value="1"></td>
</tr>
<!-- END SUB: BUTTON_SUB_URL -->

<!-- SUB: BUTTON_SUB_ORDER -->
<tr>
<td class="fgtext">Form, millele suunatakse:</td>
<td class="fgtext"><select CLASS="small_button" NAME='{VAR:cell_id}_order_form' >{VAR:order_form}</select></td>
<td class="fgtext">&nbsp;</td>
<td class="fgtext">&nbsp;</td>
</tr>
<!-- END SUB: BUTTON_SUB_ORDER -->

<!-- SUB: BUTTON_CONFIRM_TYPE -->
<tr>
<td class="fgtext">{VAR:LC_FORMS_FOLDER_TO_MOVE}:</td>
<td colspan="3" class="fgtext"><select name='{VAR:cell_id}_confirm_moveto' class='small_button'>{VAR:folders}</select></td>
</tr>
<tr>
<td class="fgtext">{VAR:LC_FORMS_URL_TO_MOVE}:</td>
<td colspan="3" class="fgtext"><input type='text' name='{VAR:cell_id}_confirm_redirect' class='small_button' value='{VAR:redirect}'></td>
</tr>
<!-- END SUB: BUTTON_CONFIRM_TYPE -->

<!-- SUB: BUTTON_SUB_OP -->
<tr>
<td class="fgtext">{VAR:LC_FORMS_OUTPUT}:</td>
<td class="fgtext"><select CLASS="small_button" NAME='{VAR:cell_id}_bop'>{VAR:bops}</select></td>
<td class="fgtext">&nbsp;</td>
<td class="fgtext">&nbsp;</td>
</tr>
<!-- END SUB: BUTTON_SUB_OP -->

<!-- SUB: BUTTON_ITEMS -->
<tr>
<td class="fgtext">{VAR:LC_FORMS_TEXT_ON_BUTTON}:</td>
<td class="fgtext"><input type='text' CLASS="small_button" NAME='{VAR:cell_id}_btext' VALUE='{VAR:button_text}'></td>
<td class="fgtext">{VAR:LC_FORMS_NOT_GO_FORW_IN_CHAIN}:</td>
<td class="fgtext"><input type="checkbox" name="{VAR:cell_id}_chain_forward" value="1" {VAR:chain_forward}></td>
</tr>
<tr>
<td class="fgtext">Pilt:</td>
<td class="fgtext">{VAR:button_img}  <input class='small_button' type='file' name='{VAR:cell_id}_button_img'> Kas kasutada pilti? <input type='checkbox' name='{VAR:cell_id}_use_button_img' value='1' {VAR:use_button_img}></td>
<td class="fgtext">{VAR:LC_FORMS_NOT_GO_BACK_IN_CHAIN}:</td>
<td class="fgtext"><input type="checkbox" name="{VAR:cell_id}_chain_backward" value="1" {VAR:chain_backward}></td>
</tr>

<tr>
<td class="fgtext">&nbsp;</td>
<td class="fgtext">&nbsp;</td>
<td class="fgtext">Mine p&auml;rja l&otilde;ppu:</td>
<td class="fgtext"><input type="checkbox" name="{VAR:cell_id}_chain_finish" value="1" {VAR:chain_finish}></td>
</tr>


<tr>
<td class="fgtext">CSS class</td>
<td class="fgtext"><input class='small_button' type='text' name='{VAR:cell_id}_button_css_class' value='{VAR:button_css_class}'></td>
<td class="fgtext">Nupp viib j&auml;rgmisele p&auml;rja formile</td>
<td class="fgtext"><input type="checkbox" value="1" name="{VAR:cell_id}_button_js_next_form_in_chain" {VAR:button_js_next_form_in_chain}></td>
</tr>
<!-- END SUB: BUTTON_ITEMS -->

<!-- SUB: CHECKBOX_ITEMS -->
<tr>
<td class="fgtext">{VAR:LC_FORMS_ORIGINALLY_SELECTED}:</td>
<td class="fgtext"><input type='checkbox' NAME='{VAR:default_name}' VALUE='1' {VAR:default_checked}></td>
<td class="fgtext">{VAR:LC_FORMS_CHECKBOX_VALUE}</td>
<td class="fgtext"><input type='text' name='{VAR:cell_id}_ch_value' value='{VAR:ch_value}' class='small_button'></td>
</tr>
<tr>
<td class="fgtext">Grupp:</td>
<td class="fgtext"><input class='small_button' type='text' NAME='{VAR:cell_id}_ch_grp' VALUE='{VAR:ch_grp}'></td>
<td class="fgtext">&nbsp;</td>
<td class="fgtext">&nbsp;</td>
</tr>
<!-- END SUB: CHECKBOX_ITEMS -->

<!-- SUB: SHOW_AS_TEXT -->
<tr>
<td class="fgtext">N&auml;ita tekstina:</td>
<td class="fgtext"><input class='small_button' type='checkbox' NAME='{VAR:cell_id}_show_as_text' VALUE='1' {VAR:show_as_text}></td>
<td class="fgtext">Ilma peidetud elemendita?</td>
<td class="fgtext"><input class='small_button' type='checkbox' value='1' name='{VAR:cell_id}_no_hidden_el' {VAR:no_hidden_el}></td>
</tr>
<!-- END SUB: SHOW_AS_TEXT -->

<!-- SUB: PRICE_ITEMS -->
<tr>
<td class="fgtext">{VAR:LC_FORMS_DEFAULT_PRICE}:</td>
<td class="fgtext"><input class='small_button' size=7 type='text' NAME='{VAR:cell_id}_price' VALUE='{VAR:price}'></td>
<td class="fgtext">{VAR:LC_FORMS_LENGTH}:</td>
<td class="fgtext"><input type='text' CLASS="small_button" SIZE=3 NAME='{VAR:cell_id}_length' VALUE='{VAR:length}'></td>
</tr>
<tr>
<td class="fgtext">{VAR:LC_FORMS_WHICH_CURRENCY_PRICE_SAVED}:</td>
<td class="fgtext"><select CLASS="small_button" NAME='{VAR:cell_id}_price_cur'>{VAR:price_cur}</select></td>
<td class="fgtext">{VAR:LC_FORMS_WHICH_CURRENCY_SHOW_PRICE}:</td>
<td class="fgtext"><select multiple CLASS="small_button" NAME='{VAR:cell_id}_price_show[]'>{VAR:price_show}</select></td>
</tr>
<tr>
<td class="fgtext">{VAR:LC_FORMS_PRICE_SEPARATOR}:</td>
<td class="fgtext"><input type='text' class='small_button' name='{VAR:cell_id}_price_sep' value='{VAR:price_sep}'></td>
<td class="fgtext">&nbsp;</td>
<td class="fgtext">&nbsp;</td>
</tr>
<!-- END SUB: PRICE_ITEMS -->
<tr>
<!-- SUB: EL_NOHLINK -->
<td valign=top class="fgtext">{VAR:LC_FORMS_TEXT}:</td>
<td valign=top class="fgtext"><input class="small_button" type='text' NAME='{VAR:cell_id}_text' VALUE="{VAR:cell_text}">&nbsp;{VAR:LC_FORMS_DISTANCE_FROM_ELEMENT}:&nbsp;<input class="small_button" type='text' NAME='{VAR:cell_id}_dist' size=3 VALUE='{VAR:cell_dist}'>&nbsp;pix</td>
<td class="fgtext">Disabled:</td>
<td class="fgtext"><input type='checkbox' name='{VAR:cell_id}_disabled' value='1' {VAR:disabled}></td>
</tr>
<tr>
<td valign=top class="fgtext">&nbsp;</td>
<td valign=top class="fgtext">&nbsp;</td>
<td class="fgtext">Peidetud:</td>
<td class="fgtext"><input type='checkbox' name='{VAR:cell_id}_hidden' value='1' {VAR:hidden}></td>
<!-- END SUB: EL_NOHLINK -->

<!-- SUB: ACTIVITY -->
<tr>
	<td class="fgtext">Aktiivsuse sisestatud arv on:</td>
	<td class="fgtext">
		<input type="radio" name="{VAR:cell_id}_activity_type" VALUE="hours" {VAR:activity_hours}> Tundides
		<input type="radio" name="{VAR:cell_id}_activity_type" VALUE="days" {VAR:activity_days}> P&auml;evades
		<input type="radio" name="{VAR:cell_id}_activity_type" VALUE="weeks" {VAR:activity_weeks}> N&auml;dalates
		<input type="radio" name="{VAR:cell_id}_activity_type" VALUE="months" {VAR:activity_months}> Kuudes
	</td>
	<td class="fgtext"><input type="radio" name="{VAR:cell_id}_activity_type" VALUE="date" {VAR:activity_date}> Kuup&auml;ev</td>
	<td class="fgtext">&nbsp;</td>
</tr>
<!-- END SUB: ACTIVITY -->

<!-- SUB: HAS_PERIOD -->
<tr>
	<td class="fgtext" valign="top">
		Perioodiühik:
	</td>
	<td class="fgtext" colspan="3">
		<select name="{VAR:cell_id}_period_type">{VAR:period_types}</select>&nbsp;
		Ühikuid:
		<input type="text" name="{VAR:cell_id}_period_items" value="{VAR:period_items}" size="3" maxlength="3">&nbsp;
		Max. ühikuid ühes perioodis:
		<input type="text" name="{VAR:cell_id}_max_period_items" value="{VAR:max_period_items}" size="3" maxlength="3">&nbsp;
	</td>
</tr>
<!-- END SUB: HAS_PERIOD -->

<!-- SUB: ALIASES -->
<tr>
	<td class="fgtext" valign="top">
		Aliased:
	</td>
	<td class="fgtext" colspan="3">
		<select name="{VAR:cell_id}_alias">
		<option value="0">--- Vali üks ---</option>
		{VAR:aliaslist}
		</select>
	</td>
</tr>
<tr>
<td class="fgtext">
	Tüüp
</td>
<td class="fgtext">
	<select name="{VAR:cell_id}_alias_type">
	{VAR:aliastype}
	</select>
</td>
<td colspan="2" class="fgtext">
&nbsp;
</td>
</tr>
<!-- END SUB: ALIASES -->

<!-- SUB: TIMESLICE -->
<tr>
	<td class="fgtext" valign="top">
		Ajaühiku pikkus
	</td>
	<td class="fgtext" colspan="3">
		<select class="small_button" name="{VAR:cell_id}_slicelength">
		<option value="0">--- Vali üks ---</option>
		{VAR:slicelengthlist}
		</select>
	</td>
</tr>
<!-- END SUB: TIMESLICE -->


<!-- SUB: EL_HLINK -->
<td class="fgtext">{VAR:LC_FORMS_CHOOSE_OUTPUT}:</td>
<td class="fgtext"><select name='{VAR:cell_id}_link_op' class='small_button'>{VAR:ops}</select></td>
<td class="fgtext">&nbsp;</td>
<td class="fgtext">&nbsp;</td>
<!-- END SUB: EL_HLINK -->
</tr>
<tr>
<td valign=top class="fgtext"><small>Subskript:</font></small></td>
<td class="fgtext" >
	<input type=text class="small_button" size=45 NAME='{VAR:cell_id}_info' value='{VAR:cell_info}'>
</td>
<Td class="fgtext">Selectrow grupp:</td>
<Td class="fgtext"><input type='text' name='{VAR:cell_id}_srow_grp' size=15 class='small_button' value='{VAR:srow_grp}'></td>
</tr>
<tr>
<td valign=top class="fgtext">{VAR:LC_FORMS_TEXT_POSITION}:</td>
<td valign=top class="fgtext"><input class="small_button" type='radio' NAME='{VAR:cell_id}_text_pos' VALUE='up' {VAR:text_pos_up}>&nbsp;{VAR:LC_FORMS_BIG_UP}&nbsp;<input class="small_button" type='radio' NAME='{VAR:cell_id}_text_pos' VALUE='down' {VAR:text_pos_down}>&nbsp;{VAR:LC_FORMS_BIG_DOWN}&nbsp;<input class="small_button" type='radio' NAME='{VAR:cell_id}_text_pos' VALUE='left' {VAR:text_pos_left}>&nbsp;{VAR:LC_FORMS_IN_LEFT}&nbsp;<input class="small_button" type='radio' NAME='{VAR:cell_id}_text_pos' VALUE='right' {VAR:text_pos_right}>&nbsp;{VAR:LC_FORMS_IN_RIGHT}&nbsp;</td>

<td valign=top class="fgtext"><a href='{VAR:changepos}'>{VAR:LC_FORMS_CHANGE_ELEMENT_POSITION}</a></td>
<td valign=top class="fgtext">&nbsp;</td>
</tr>
<tr>
<td class="fgtext"><img src='/images/transa.gif' height=1 width=85><br>{VAR:LC_FORMS_AFTER_ELEMENT}:</td>
<td class="fgtext" colspan=1><img src='/images/transa.gif' height=1 width=275><br><input class="small_button" type='radio' NAME='{VAR:cell_id}_separator_type' VALUE='1' {VAR:sep_enter_checked}>{VAR:LC_FORMS_ROW_EXCHANGE}&nbsp;&nbsp;
<input class="small_button" type='radio' NAME='{VAR:cell_id}_separator_type' VALUE='2' {VAR:sep_space_checked}>&nbsp;<input class="small_button" type='text' NAME='{VAR:cell_id}_sep_pixels' MAXLENGTH=10 SIZE=10 VALUE='{VAR:cell_sep_pixels}'>&nbsp;{VAR:LC_FORMS_PIXELS}</td>
<td class="fgtext"><img src='/images/transa.gif' height=1 width=85><br>{VAR:LC_FORMS_ORDER}:</td>
<td class="fgtext"><input class="small_button" type='text' size=2 NAME='{VAR:cell_id}_order' VALUE='{VAR:cell_order}'></td>
</tr>
<tr>
<td class="fgtext">{VAR:LC_FORMS_ACTIVE_SINCE}:</td>
<td class="fgtext">{VAR:act_from}</td>
<td class="fgtext">{VAR:LC_FORMS_ACTIVE_TILL}:</td>
<td class="fgtext">{VAR:act_to}</td>
</tr>
<tr>
<td class="fgtext">{VAR:LC_FORMS_HEH_ACT_DATE}: </td>
<td class="fgtext"><input type='checkbox' name='{VAR:cell_id}_has_act' value='1' {VAR:has_act}></td>
<td class="fgtext">Tabindex:</td>
<td class="fgtext"><input type="text" size="5" name="{VAR:cell_id}_el_tabindex" value="{VAR:el_tabindex}" class="small_button"></td>
</tr>

<!-- SUB: IS_NUMBER -->
<tr>
<td class="fgtext">Tuhandete eraldaja numbris: </td>
<td class="fgtext"><input type='text' class='small_button' name='{VAR:cell_id}_thousands_sep' value='{VAR:thousands_sep}' size='2'></td>
<td class="fgtext">&nbsp;</td>
<td class="fgtext">&nbsp;</td>
</tr>
<tr>
<td class="fgtext">Liitmise/lahutamise nupud: </td>
<td class="fgtext"><input type='checkbox' class='small_button' name='{VAR:cell_id}_up_down_button' {VAR:up_down_button}></td>
<td class="fgtext">Mitu liidetakse/lahutatakse</td>
<td class="fgtext"><input type='text' class='small_button' name='{VAR:cell_id}_up_down_count' value='{VAR:up_down_count}' size='2'></td>
</tr>
<!-- SUB: HAS_ADD_SUB_BUTTONS -->
<tr>
<td class="fgtext">Liitmise nupu pilt: </td>
<td class="fgtext">{VAR:up_button_img} <input type='file' class='small_button' name='{VAR:cell_id}_up_button_img'> Kas kasutada? <input type="checkbox" name="{VAR:cell_id}_up_button_use_img" value="1" {VAR:up_button_use_img}></td>
<td class="fgtext">Llahutamise nupu pilt: </td>
<td class="fgtext">{VAR:down_button_img} <input type='file' class='small_button' name='{VAR:cell_id}_down_button_img'> Kas kasutada? <input type="checkbox" name="{VAR:cell_id}_down_button_use_img" value="1" {VAR:down_button_use_img}></td>
</tr>
<!-- END SUB: HAS_ADD_SUB_BUTTONS -->
<tr>
<td class="fgtext">Form, kus on mitu liita/lahtuada element: </td>
<td class="fgtext"><select class='small_button' name="{VAR:cell_id}_up_down_count_el_form">{VAR:udcel_forms}</select></td>
<td class="fgtext">Element, kus on mitu liita/lahtuada element: </td>
<td class="fgtext"><select class='small_button' name="{VAR:cell_id}_up_down_count_el_el">{VAR:udcel_els}</select></td>
</tr>
<!-- END SUB: IS_NUMBER -->

<!-- SUB: HAS_SIMPLE_CONTROLLER -->
<tr>
<td class="fgtext">{VAR:LC_FORMS_SHOULD_BE_FILLED}:</td>
<td class="fgtext"><input type='checkbox' CLASS="small_button" NAME='{VAR:cell_id}_must_fill' VALUE='1' {VAR:must_fill_checked}></td>
<td class="fgtext">{VAR:LC_FORMS_ERROR_NOTE}:</td>
<td class="fgtext"><input type='text' CLASS="small_button" NAME='{VAR:cell_id}_must_error' VALUE='{VAR:must_error}'></td>
</tr>
<!-- END SUB: HAS_SIMPLE_CONTROLLER -->

<!-- SUB: CHECK_LENGTH -->
<tr>
<td class="fgtext">Tähemärkide arvu piirang (javascript):</td>
<td class="fgtext"><input type='checkbox' CLASS="small_button" NAME='{VAR:cell_id}_check_length' VALUE='1' {VAR:check_length}>
&nbsp; &nbsp; &nbsp; Max tähemärke:
<input type='text' CLASS="small_button" NAME='{VAR:cell_id}_max_length' VALUE='{VAR:max_length}'>
</td>
<td class="fgtext">Veatede, kui välja sisu on pikem:
</td>
<td class="fgtext">
<input type='text' CLASS="small_button" NAME='{VAR:cell_id}_check_length_error' VALUE='{VAR:check_length_error}'>
</td>
</tr>
<!-- END SUB: CHECK_LENGTH -->

<!-- SUB: SEARCH_PROPS -->
<tr>
<td class="fgtext">Otsingul t&auml;pne vaste:</td>
<td class="fgtext"><input type='checkbox' CLASS="small_button" NAME='{VAR:cell_id}_search_all_text' VALUE='1' {VAR:search_all_text}></td>
<td class="fgtext">Jaga otsing s&otilde;nadeks:</td>
<td class="fgtext"><input type='checkbox' CLASS="small_button" NAME='{VAR:cell_id}_search_separate_words' VALUE='1' {VAR:search_separate_words}> s&otilde;nade eraldaja: <input type='text' name='{VAR:cell_id}_search_separate_words_sep' value='{VAR:search_separate_words_sep}' class='small_button' size='2'></td>
</tr>
<tr>
<td class="fgtext">Otsing on loogiline lause:</td>
<td class="fgtext"><input type='checkbox' CLASS="small_button" NAME='{VAR:cell_id}_search_logical' VALUE='1' {VAR:search_logical}></td>
<td class="fgtext">Otsing FIELD_IN_SET:</td>
<td class="fgtext"><input type='checkbox' CLASS="small_button" NAME='{VAR:cell_id}_search_field_in_set' VALUE='1' {VAR:search_field_in_set}></td>
</tr>
<tr>
<td class="fgtext">Loogiline lause enne sisestust:</td>
<td class="fgtext"><input type='text' CLASS="small_button" NAME='{VAR:cell_id}_search_logical_prepend' VALUE='{VAR:search_logical_prepend}'></td>
<td class="fgtext">Loogiline lause p&auml;rast sisestust:</td>
<td class="fgtext"><input type='text' CLASS="small_button" NAME='{VAR:cell_id}_search_logical_append' VALUE='{VAR:search_logical_append}'></td>
</tr>
<!-- END SUB: SEARCH_PROPS -->

<!-- SUB: IS_TEXTBOX_ITEMS -->
<tr>
<td class="fgtext">Kas elemendil on javascripti default v&auml;&auml;rtus?</td>
<td class="fgtext"><input type="checkbox" value="1" name="{VAR:cell_id}_js_flopper" {VAR:js_flopper}></td>
<td class="fgtext">Js Default v&auml;&auml;rtuse tekst:</td>
<td class="fgtext"><input class="small_button" type="text" name="{VAR:cell_id}_js_flopper_value" value="{VAR:js_flopper_value}"></td>
</tr>
<!-- END SUB: IS_TEXTBOX_ITEMS -->

<!-- SUB: IS_TRANSLATABLE -->
<tr>
<td class="fgtext">Kas element on t&otilde;lgitav?</td>
<td class="fgtext"><input type="checkbox" value="1" name="{VAR:cell_id}_is_translatable" {VAR:is_translatable}></td>
<td class="fgtext">&nbsp;</td>
<td class="fgtext">&nbsp;</td>
</tr>
<!-- END SUB: IS_TRANSLATABLE -->

<!-- SUB: HAS_CONTROLLER -->
<tr>
<td class="fgtext">Vali elemendi sisestuse kontroller(id):</td>
<td class="fgtext"><select size="10" multiple CLASS="small_button" NAME='{VAR:cell_id}_entry_controllers[]'>{VAR:entry_controllers}</select></td>
<td class="fgtext">Vali elemendi n&auml;itamise kontroller(id):</td>
<td class="fgtext"><select size="10" multiple CLASS="small_button" NAME='{VAR:cell_id}_show_controllers[]'>{VAR:show_controllers}</select></td>
</tr>
<!-- END SUB: HAS_CONTROLLER -->

<!-- SUB: HAS_ONLY_SHOW_CONTROLLER -->
<tr>
<td class="fgtext">&nbsp;</td>
<td class="fgtext">&nbsp;</td>
<td class="fgtext">Vali elemendi n&auml;itamise kontroller(id):</td>
<td class="fgtext"><select size="10" multiple CLASS="small_button" NAME='{VAR:cell_id}_show_controllers[]'>{VAR:show_controllers}</select></td>
</tr>
<!-- END SUB: HAS_ONLY_SHOW_CONTROLLER -->

<!-- SUB: HAS_DEFAULT_CONTROLLER -->
<tr>
<td class="fgtext">Vali default v&auml;&auml;rtuse kontroller:</td>
<td class="fgtext"><select CLASS="small_button" NAME='{VAR:cell_id}_default_controller'>{VAR:default_controller}</select></td>
<td class="fgtext">Vali v&auml;&auml;rtuse kontroller:</td>
<td class="fgtext"><select CLASS="small_button" NAME='{VAR:cell_id}_value_controller'>{VAR:value_controller}</select></td>
</tr>
<!-- END SUB: HAS_DEFAULT_CONTROLLER -->

<tr>
	<td class="fgtext" colspan="4">Metadata:</td>
</tr>
<tr>
	<td class="fgtext">Nimi</td>
	<td class="fgtext">V&auml;&auml;rtus</td>
	<td class="fgtext">Elemendi CSS Stiil:</td>
	<td class="fgtext">&nbsp;<select name="{VAR:cell_id}_el_css_style">{VAR:el_css_style}</select></td>
</tr>
<!-- SUB: METADATA -->
<tr>
	<td class="fgtext"><input type='text' name='{VAR:cell_id}_metadata_name[]' value='{VAR:metadata_name}' class='small_button'></td>
	<td class="fgtext"><input type='text' name='{VAR:cell_id}_metadata_value[]' value='{VAR:metadata_value}' class='small_button'></td>
	<td class="fgtext">&nbsp;</td>
	<td class="fgtext">&nbsp;</td>
</tr>
<!-- END SUB: METADATA -->
</table>
