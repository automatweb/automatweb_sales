<table border="0" cellspacing="1" cellpadding="2" bgcolor="#CCCCCC" width=100%>
<tr>
	<td class="fgtext">{VAR:LC_FORMS_TYPE}:</td>
	<td class="fgtext"><select class="small_button" NAME='{VAR:cell_id}_type'>
    <option  VALUE=''>{VAR:LC_FORMS_ORDINARY_TEXT}
    <option  VALUE=''>---------
    <option {VAR:type_active_textbox} VALUE='textbox'>{VAR:LC_FORMS_TEXTBOX}
    <option {VAR:type_active_textarea} VALUE='textarea'>{VAR:LC_FORMS_MULTILINE_TEXT}
    <option {VAR:type_active_checkbox} VALUE='checkbox'>Checkbox
    <option {VAR:type_active_radiobutton} VALUE='radiobutton'>Radiobutton
    <option {VAR:type_active_listbox} VALUE='listbox'>Listbox
    <option {VAR:type_active_multiple} VALUE='multiple'>Multiple listbox
    <option {VAR:type_active_file} VALUE='file'>{VAR:LC_FORMS_ADDING_FILE}
    <option {VAR:type_active_link} VALUE='link'>{VAR:LC_FORMS_HYPERLINK}
    <option {VAR:type_active_button} VALUE='button'>{VAR:LC_FORMS_BUTTON}
    <option {VAR:type_active_price} VALUE='price'>{VAR:LC_FORMS_PRICE}
    <option {VAR:type_active_date} VALUE='date'>{VAR:LC_FORMS_DATE}
<!-- SUB: CAN_DELETE -->
    <option VALUE='delete'>{VAR:LC_FORMS_DELETE_THIS_ELEMENT}
<!-- END SUB: CAN_DELETE -->
    </select>
<!-- SUB: HAS_SUBTYPE -->
&nbsp;Alamt&uuml;&uuml;p:&nbsp;
<select name='{VAR:cell_id}_subtype' class="small_button">{VAR:subtypes}</select>
<!-- END SUB: HAS_SUBTYPE -->
		</td>
	<td class="fgtext">{VAR:LC_FORMS_NAME}:</td>
	<td class="fgtext"><input type='text' class="small_button" NAME='{VAR:cell_id}_name' VALUE='{VAR:cell_name}'></td>
</tr>
<!-- SUB: LISTBOX_SORT -->
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
<!-- END SUB: LISTBOX_SORT -->

<!-- SUB: LISTBOX_ITEMS -->
<tr>
<td class="fgtext">&nbsp;</td>
<td class="fgtext"><input class="small_button" type='text' NAME='{VAR:listbox_item_id}' VALUE='{VAR:listbox_item_value}'>&nbsp;<input type='radio' NAME='{VAR:listbox_radio_name}' VALUE='{VAR:listbox_radio_value}' {VAR:listbox_radio_checked}>&nbsp;<input type='text' name='{VAR:listbox_order_name}' value='{VAR:listbox_order_value}' class='small_button' size=4>&nbsp;<input type='checkbox' name='{VAR:cell_id}_sel[{VAR:num}]' value='1'></td>
<td class="fgtext">&nbsp;</td>
<td class="fgtext">&nbsp;</td>
</tr>
<!-- END SUB: LISTBOX_ITEMS -->

<!-- SUB: MULTIPLE_ITEMS -->
<tr>
<td class="fgtext">&nbsp;</td>
<td class="fgtext"><input CLASS="small_button" type='text' NAME='{VAR:multiple_item_id}' VALUE='{VAR:multiple_item_value}'>&nbsp;<input CLASS="small_button" type='checkbox' NAME='{VAR:multiple_check_name}' VALUE='{VAR:multiple_check_value}' {VAR:multiple_check_checked}>&nbsp;<input type='text' name='{VAR:multiple_order_name}' value='{VAR:multiple_order_value}' class='small_button' size=4>&nbsp;<input type='checkbox' name='{VAR:cell_id}_sel[{VAR:num}]' value='1'></td>
<td class="fgtext">&nbsp;</td>
<td class="fgtext">&nbsp;
</tr>
<!-- END SUB: MULTIPLE_ITEMS -->

<!-- SUB: TEXTAREA_ITEMS -->
<tr>
<td class="fgtext">Suurus:</td>
<td class="fgtext">&nbsp;Laius:&nbsp;<input CLASS="small_button" SIZE=3 type='text' NAME='{VAR:textarea_cols_name}' VALUE='{VAR:textarea_cols}'>{VAR:LC_FORMS_HIGHT}:&nbsp;<input CLASS="small_button" SIZE=3 type='text' NAME='{VAR:textarea_rows_name}' VALUE='{VAR:textarea_rows}'></td>
<td valign=top class="fgtext">{VAR:LC_FORMS_ORIGINAL_TEXT}:</td>
<td class="fgtext"><input type=text CLASS="small_button"  SIZE=45 NAME='{VAR:default_name}' VALUE='{VAR:default}'></td>
</tr>
<!-- END SUB: TEXTAREA_ITEMS -->

<!-- SUB: DATE_ITEMS -->
<tr>
<td class="fgtext">{VAR:LC_FORMS_FROM_YEAR_TO_YEAR}:</td>
<td class="fgtext">&nbsp;<input CLASS="small_button" SIZE=5 type='text' NAME='{VAR:cell_id}_from_year' VALUE='{VAR:from_year}'>&nbsp;-&nbsp;<input type=text CLASS="small_button"  SIZE=5 NAME='{VAR:cell_id}_to_year' VALUE='{VAR:to_year}'></td>
<td valign=top class="fgtext">&nbsp;</td>
<td class="fgtext">&nbsp;</td>
</tr>
<!-- END SUB: DATE_ITEMS -->

<!-- SUB: FILE_ITEMS -->
<tr>
<td class="fgtext">{VAR:LC_FORMS_DISPLAYING}:</td>
<td class="fgtext"><input CLASS="small_button" type='radio' NAME='{VAR:cell_id}_filetype' VALUE='1' {VAR:ftype_image_selected}> {VAR:LC_FORMS_AS_A_PICTURE} <input CLASS="small_button" type='radio' NAME='{VAR:cell_id}_filetype' VALUE='2' {VAR:ftype_file_selected}> {VAR:LC_FORMS_LINKED_AS_FILE}</td>
<td class="fgtext">{VAR:LC_FORMS_LINK_TEXT}:</td>
<td class="fgtext"><input CLASS="small_button" type='text' NAME='{VAR:cell_id}_file_link_text' VALUE='{VAR:file_link_text}'></td>
</tr>
<tr>
<td class="fgtext">&nbsp;</td>
<td class="fgtext"><input type='radio' NAME='{VAR:cell_id}_file_show' VALUE=1 {VAR:file_show}> {VAR:LC_FORMS_DISPLAY_NOW} <input type='radio' NAME='{VAR:cell_id}_file_show' VALUE=0 {VAR:file_alias}> {VAR:LC_FORMS_MAKING_ALIAS}</td>
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
<!-- END SUB: HLINK_ITEMS -->

<!-- SUB: RADIO_ITEMS -->
<tr>
<td class="fgtext">{VAR:LC_FORMS_GROUP}:</td>
<td class="fgtext"><input class="small_button" type='text' SIZE=1 NAME='{VAR:cell_id}_group' VALUE='{VAR:cell_group}'></td>
<td class="fgtext">{VAR:LC_FORMS_ORIGINALLY_SELECTED}:</td>
<td class="fgtext"><input type='checkbox' NAME='{VAR:default_name}' VALUE='1' {VAR:default_checked}></td>
</tr>
<!-- END SUB: RADIO_ITEMS -->

<!-- SUB: DEFAULT_TEXT -->
<tr>
<td class="fgtext">{VAR:LC_FORMS_ORIGINAL_TEXT}:</td>
<td class="fgtext"><input type='text' CLASS="small_button" NAME='{VAR:default_name}' VALUE='{VAR:default}'></td>
<td class="fgtext">{VAR:LC_FORMS_LENGTH}:</td>
<td class="fgtext"><input type='text' CLASS="small_button" SIZE=3 NAME='{VAR:cell_id}_length' VALUE='{VAR:length}'></td>
</tr>
<!-- END SUB: DEFAULT_TEXT -->

<!-- SUB: BUTTON_SUB_URL -->
<tr>
<td class="fgtext">URL:</td>
<td class="fgtext"><input type='text' CLASS="small_button" NAME='{VAR:cell_id}_burl' VALUE='{VAR:button_url}'></td>
<td class="fgtext">&nbsp;</td>
<td class="fgtext">&nbsp;</td>
</tr>
<!-- END SUB: BUTTON_SUB_URL -->

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
<td class="fgtext">&nbsp;</td>
<td class="fgtext">&nbsp;</td>
</tr>
<!-- END SUB: BUTTON_ITEMS -->

<!-- SUB: CHECKBOX_ITEMS -->
<tr>
<td class="fgtext">{VAR:LC_FORMS_ORIGINALLY_SELECTED}:</td>
<td class="fgtext"><input type='checkbox' NAME='{VAR:default_name}' VALUE='1' {VAR:default_checked}></td>
<td class="fgtext">&nbsp;</td>
<td class="fgtext">&nbsp;</td>
</tr>
<!-- END SUB: CHECKBOX_ITEMS -->
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
<!-- END SUB: EL_NOHLINK -->

<!-- SUB: EL_HLINK -->
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
<!-- SUB: HAS_SIMPLE_CONTROLLER -->
<tr>
<td class="fgtext">{VAR:LC_FORMS_SHOULD_BE_FILLED}:</td>
<td class="fgtext"><input type='checkbox' CLASS="small_button" NAME='{VAR:cell_id}_must_fill' VALUE='1' {VAR:must_fill_checked}></td>
<td class="fgtext">{VAR:LC_FORMS_ERROR_NOTE}:</td>
<td class="fgtext"><input type='text' CLASS="small_button" NAME='{VAR:cell_id}_must_error' VALUE='{VAR:must_error}'></td>
</tr>
<!-- END SUB: HAS_SIMPLE_CONTROLLER -->
</table>
