<form action='reforb.{VAR:ext}' method=post>
<table cellpadding=3 cellspacing=0 border=0>
<tr class="aste01">
<td class="celltext">Meili from aadress:</td>
<td class="celltext"><input type='text' NAME='from_addr' VALUE='{VAR:from_addr}'></td>
</tr>
<tr class="aste01">
<td class="celltext">Meili from nimi:</td>
<td class="celltext"><input type='text' NAME='from_name' VALUE='{VAR:from_name}'></td>
</tr>
<tr class="aste01">
<td class="celltext">Kirja subjekt:</td>
<td class="celltext"><input class='small_button' type='text' NAME='subj' VALUE='{VAR:subj}'></td>
</tr>
<tr class="aste01">
<td class="celltext">Kirja sisu:</td>
<td class="celltext"><textarea NAME='content' cols=50 rows=5>{VAR:content}</textarea></td>
</tr>

<tr class="aste01">
<td class="celltext">Saada ka e-mailile, mis on elemendis:</td>
<td class="celltext"><select name="email_el" class="formselect">{VAR:email_el}</select></td>
</tr>
<tr class="aste01">
<td></td>
<td class="celltext"><input type='submit' class='formbutton' VALUE='{VAR:LC_FORMS_SAVE}'></td>
</tr>
</table>
{VAR:reforb}
</form>
