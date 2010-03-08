<!-- SUB: TEXTAREA -->
<!-- SUB: TEXTAREA_TEXT -->
{VAR:text}<br>
<!-- END SUB: TEXTAREA_TEXT -->
<textarea NAME='{VAR:element_id}' COLS='{VAR:textarea_cols}' ROWS='{VAR:textarea_rows}'>{VAR:element_value}</textarea>
<!-- END SUB: TEXTAREA -->
<!-- SUB: RADIOBUTTON -->
<input type='radio' NAME='{VAR:element_id}' VALUE='{VAR:radio_value}' {VAR:radio_checked}>{VAR:text}
<!-- END SUB: RADIOBUTTON -->
<!-- SUB: LISTBOX -->
<!-- SUB: LISTBOX_TEXT -->
{VAR:text}<br>
<!-- END SUB: LISTBOX_TEXT -->
<select NAME='{VAR:element_id}'>
<!-- SUB: LISTBOX_ITEMS -->
<option {VAR:listbox_option_selected} VALUE='{VAR:listbox_option_id}'>{VAR:listbox_option}
<!-- END SUB: LISTBOX_ITEMS -->
</select>
<!-- END SUB: LISTBOX -->
<!-- SUB: MULTIPLE -->
<!-- SUB: MULTIPLE_TEXT -->
{VAR:text}<br>
<!-- END SUB: MULTIPLE_TEXT -->
<select NAME='{VAR:element_id}[]' MULTIPLE>
<!-- SUB: MULTIPLE_ITEMS -->
<option {VAR:multiple_option_selected} VALUE='{VAR:multiple_option_id}'>{VAR:multiple_option}
<!-- END SUB: MULTIPLE_ITEMS -->
</select>
<!-- END SUB: MULTIPLE -->
<!-- SUB: CHECKBOX -->
<input type='checkbox' NAME='{VAR:element_id}' VALUE='1' {VAR:checkbox_checked}>{VAR:text}
<!-- END SUB: CHECKBOX -->
<!-- SUB: TEXTBOX -->
{VAR:text}<input type='text' NAME='{VAR:element_id}' VALUE='{VAR:element_value}'>
<!-- END SUB: TEXTBOX -->
<!-- SUB: TEXT -->
{VAR:text}
<!-- END SUB: TEXT -->
<!-- SUB: COMMENT -->
<br><font face='arial, geneva, helvetica' size="1">&nbsp;&nbsp;{VAR:info}</font>
<!-- END SUB: COMMENT -->
