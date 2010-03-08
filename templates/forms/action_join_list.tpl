<form action='reforb.{VAR:ext}' method=post>
<table border=0 cellspacing=0 cellpadding=0>
<tr><td class="aste01">

<table border=0 cellspacing=0 cellpadding=2>
<tr>

<td class="celltext">{VAR:LC_FORMS_AFTER_FILLING_FORM_IF} <select name='j_checkbox' class="formselect2">{VAR:checkbox}</select>{VAR:LC_FORMS_IS_MARK_ADD_TO_LIST} <select name='j_list' class="formselect2">{VAR:list}</select> {VAR:LC_FORMS_USER_WHOS_EMAIL} <select NAME='j_textbox' class="formselect2">{VAR:textbox}</select> {VAR:LC_FORMS_AND_NAME_IN_CHECKBOX} <select NAME='j_name_tb' class="formselect2">{VAR:name_tb}</select></td>
</tr>
<tr>
<td class="celltext" ><input class='formbutton' type='submit' VALUE='{VAR:LC_FORMS_FORWARD}'></td>
</tr>
</table>
{VAR:reforb}

</td></tr></table>
</form>
