<table width="100%" border=0 cellspacing=0 cellpadding=1>

<form action='reforb{VAR:ext}' method=post name=ffrm>

<tr>
<td bgcolor="#FFFFFF">

<table width="100%" border=0 cellspacing=0 cellpadding=5>
<tr>
<td class="aste01">

<table cellpadding=3 cellspacing=1 border=0>
<tr>
<td class="celltext" colspan=2>Vali kataloogid, mille alamkatalooge valida saab:</td>
</tr>
<tr>
<td colspan=2 class="celltext"><select name='main_folders[]' multiple size="20" class='formselect2'>{VAR:main_folders}</select></td>
</tr>
<tr>
<td class="celltext" colspan=2>{VAR:LC_FORMS_CHOOSE_CATALOGUE_WHERE_SAVES_FORM_INFO}:</td>
</tr>
<tr>
<td colspan=2 class="celltext"><select name='ff_folder' class='formselect2'>{VAR:ff_folder}</select></td>
</tr>
<tr>
<td class="celltext" colspan=2>{VAR:LC_FORMS_CHOOSE_CATALOGUE_WHERE_ADD_TYPELEMENT}:</td>
</tr>
<tr>
<td colspan=2 class="celltext"><select name='newel_parent' class='formselect2'>{VAR:ne_folder}</select></td>
</tr>
<tr>
<td class="celltext" colspan=2>{VAR:LC_FORMS_CHOOSE_CATALOGUE_WHERE_SAVES_FORM_EL}:</td>
</tr>
<tr>
<td colspan=2 class="celltext"><select name='tear_folder' class='formselect2'>{VAR:tear_folder}</select></td>
</tr>
<tr>
<td class="celltext" colspan=2>{VAR:LC_FORMS_CHOOSE_CATALOGUE_WHERE_CAN_SAVE_NEW_EL}:</td>
</tr>
<tr>
<td colspan=2 class="celltext"><select class='formselect2' NAME='el_menus[]' size=20 multiple>{VAR:el_menus}</select></td>
</tr>
<tr>
<td class="celltext" colspan=2>{VAR:LC_FORMS_TABLE_ADD_COL}:</td>
</tr>
<tr>
<td colspan=2 class="celltext"><select class='formselect2' NAME='el_menus2[]' size=20 multiple>{VAR:el_menus2}</select></td>
</tr>
<tr>
<td class="celltext" colspan=2>{VAR:LC_FORMS_CHOOSE_MOVE_FOLDERS}:</td>
</tr>
<tr>
<td colspan=2 class="celltext"><select class='formselect2' NAME='el_move_menus[]' size=20 multiple>{VAR:el_move_menus}</select></td>
</tr>
<tr>
<td class="celltext" colspan=2>Kontrollerite kataloogid:</td>
</tr>
<tr>
<td colspan=2 class="celltext"><select class='formselect2' NAME='form_controller_folders[]' size=20 multiple>{VAR:form_controller_folders}</select></td>
</tr>
<tr>
<td class="celltext" colspan=2>{VAR:LC_FORMS_CHOOSE_TIEELEMENTFORMS}:</td>
</tr>
<tr>
<td colspan=2 class="celltext"><select class='formselect2' NAME='relation_forms[]' size=10 multiple>{VAR:relation_forms}</select></td>
</tr>
<tr>
<td class="celltext" colspan=2>{VAR:LC_FORM_EL_DEFAULT_FOLDER}:</td>
</tr>
<tr>
<td colspan=2 class="celltext"><select class='formselect2' NAME='el_default_folder'>{VAR:el_default_folders}</select></td>
</tr>
<tr>
<td class="celltext" colspan=2><input class='formbutton' type='submit' NAME='save_form_settings' VALUE='{VAR:LC_FORMS_SAVE} form'></td>
</table>
{VAR:reforb}

</td></tr></table>
</td></tr>

</form>

</table>

<br>
