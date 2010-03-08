<form action='reforb.{VAR:ext}' method=post>
<table border=0 cellspacing=0 cellpadding=2>
<tr>
<td class="aste01">

<table border=0 cellspacing=0 cellpadding=2>
<tr>
<td class="celltext" align="right">{VAR:LC_FORMS_NAME}:</td>
<td class="celltext"><input type='text' NAME='name' VALUE='{VAR:name}' class="formtext"></td>
</tr>
<tr>
<td class="celltext" align="right" valign="top">{VAR:LC_FORMS_COMMENT}:</td>
<td class="celltext"><textarea NAME='comment' cols=50 rows=5 class="formtext">{VAR:comment}</textarea></td>
</tr>
<tr>
<td colspan="2" class="celltext" align="right" valign="top">Action t&auml;idetakse ainult nende nuppude vajutusel (t&uuml;hjaks j&auml;ttes t&auml;idetakse k&otilde;ikide nuppude puhul):</td>
</tr>
<tr>
<td colspan="2" class="celltext"><select name='activate_on_button[]' multiple class='formselect'>{VAR:activate_on_button}</select></td>
</tr>

<tr>
<td colspan="2" class="celltext" align="right" valign="top">Action t&auml;idetakse ainult siis kui m&otilde;ni nendest kontrolleritest tagastab false:</td>
</tr>
<tr>
<td colspan="2" class="celltext"><select name='controllers[]' multiple class='formselect'>{VAR:controllers}</select></td>
</tr>

<tr>
<td class="celltext" colspan=2>{VAR:LC_FORMS_TYPE}:</td>
</tr>
<tr>
<td class="celltext"><input type='radio' NAME='type' VALUE='email' {VAR:email_selected}></td>
<td class="celltext">{VAR:LC_FORMS_SEND_FORM_TO_EMAIL_AFTER_FILLING}</td>
</tr>
<tr>
<td class="celltext"><input type='radio' NAME='type' VALUE='email_form' {VAR:email_form}></td>
<td class="celltext">Saada vorm teises vormis olevatele aadressidele</td>
</tr>
<!--<tr>
<td class="fcaption"><input type='radio' NAME='type' VALUE='move_filled' {VAR:move_filled_selected}></td>
<td class="celltext">{VAR:LC_FORMS_MOVE_FORM_ENTRIES_OTHER_CATEGORY}</td>
</tr>-->
<tr>
<td class="celltext"><input type='radio' NAME='type' VALUE='after_submit_controller' {VAR:after_submit_controller}></td>
<td class="celltext">T&auml;ida kontroller p&auml;rast formi submittimist</td>
</tr>
<tr>
<td class="celltext"><input type='radio' NAME='type' VALUE='join_list' {VAR:join_list_selected}></td>
<td class="celltext">{VAR:LC_FORMS_ESPOUSE_MAILLIST}</td>
</tr>
<tr>
<td class="celltext"><input type='radio' NAME='type' VALUE='email_confirm' {VAR:email_confirm_selected}></td>
<td class="celltext">Saada tellimuse kinnitusmeil</td>
</tr>
<tr>
<td></td>
<td class="celltext"><input class='formbutton' type='submit' VALUE='{VAR:LC_FORMS_FORWARD}'></td>
</tr>
</table>
{VAR:reforb}
</form>

</td></tr></table>
<br>
