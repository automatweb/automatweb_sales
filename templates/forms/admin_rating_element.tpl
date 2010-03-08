<table cellpadding=3 bgcolor=#BADBAD>
<tr><td>
<table border=0 cellspacing=1 cellpadding=2 bgcolor="#CCCCCC">
<tr>
<td class="fcaption">{VAR:LC_FORMS_TEXT}:</td>
<td class="fform"><input type='text' NAME='{VAR:element_id}_text' VALUE='{VAR:element_text}'></td>
</tr>
<tr>
<td class="fcaption">{VAR:LC_FORMS_TYPE}:</td>
<td class="fform"><select NAME='{VAR:element_id}_type'>
    <option  VALUE=''>{VAR:LC_FORMS_EXPLONATION}Seletus
    <option  VALUE=''>---------
    <option {VAR:type_active_checkbox} VALUE='checkbox'>Checkbox
    <option {VAR:type_active_radiobutton} VALUE='radiobutton'>Radiobutton
    <option {VAR:type_active_listbox} VALUE='listbox'>Listbox
    <option {VAR:type_active_multiple} VALUE='multiple'>Multiple list
    </select></td>
</tr>
<!-- SUB: LISTBOX_ITEMS -->
<tr>
<td class="fcaption">&nbsp;</td>
<td class="fform"><input type='text' NAME='{VAR:listbox_item_id}' VALUE='{VAR:listbox_item_value}'>&nbsp;V:<input type='text' SIZE=2 NAME='{VAR:listbox_item_id}_value' VALUE='{VAR:listbox_item_num}'>&nbsp;<input type='radio' NAME='{VAR:listbox_radio_name}' VALUE='{VAR:listbox_radio_value}' {VAR:listbox_radio_checked}></td>
</tr>
<!-- END SUB: LISTBOX_ITEMS -->
<!-- SUB: MULTIPLE_ITEMS -->
<tr>
<td class="fcaption">&nbsp;</td>
<td class="fform"><input type='text' NAME='{VAR:multiple_item_id}' VALUE='{VAR:multiple_item_value}'>&nbsp;V:<input type='text' SIZE=2 NAME='{VAR:multiple_item_id}_value' VALUE='{VAR:multiple_item_num}'>&nbsp;<input type='checkbox' NAME='{VAR:multiple_check_name}' VALUE='{VAR:multiple_check_value}' {VAR:multiple_check_checked}></td>
</tr>
<!-- END SUB: MULTIPLE_ITEMS -->
<!-- SUB: GROUP -->
<tr>
<td class="fcaption">{VAR:LC_FORMS_GROUP}:</td>
<td class="fform"><input type='text' SIZE=1 NAME='{VAR:element_id}_group' VALUE='{VAR:element_group}'></td>
</tr>
<!-- END SUB: GROUP -->
<!-- SUB: VALUE -->
<tr>
<td class="fcaption">Value:</td>
<td class="fform"><input type='text' SIZE=1 NAME='{VAR:element_id}_value' VALUE='{VAR:element_value}'></td>
</tr>
<!-- END SUB: VALUE -->
<tr>
<td class="fcaption"><small>{VAR:LC_FORMS_EXPLONATION}:</small></td>
<td class="fform"><textarea rows=3 cols=20 NAME='{VAR:element_id}_info'>{VAR:element_info}</textarea></td>
</tr>
<tr>
<td class="fcaption">{VAR:LC_FORMS_ORDER}:</td>
<td class="fform"><input type='text' size=2 NAME='{VAR:element_id}_order' VALUE='{VAR:element_order}'></td>
</tr>
</table>
</td></tr></table><br>