<form action='reforb{VAR:ext}' method=post>
<table cellpadding=3 cellspacing=0 border=0>
<tr class="aste01">
<td class="celltext">{VAR:LC_FORMS_EMAIL}:</td>
<td class="celltext"><input type='text' NAME='email' VALUE='{VAR:email}'></td>
</tr>
<tr class="aste01">
<td class="celltext">Kirja subjekt:</td>
<td class="celltext">

<table cellpadding=3 cellspacing=1 border=0>
<tr class="aste01">
<!-- SUB: T_LANG -->
<td class="celltext">{VAR:lang_name}</td>
<!-- END SUB: T_LANG -->
</tr>
<tr class="aste01">
<!-- SUB: LANG -->
<td class="celltext"><input class='small_button' type='text' NAME='subj[{VAR:lang_id}]' VALUE='{VAR:subj}'></td>
<!-- END SUB: LANG -->
</tr>
</table>

</td>
</tr>
<tr class="aste01">
<td class="celltext">{VAR:LC_FORMS_OUTPUT_STYLE}:</td>
<td class="celltext"><select name='op_id'>{VAR:ops}</select></td>
</tr>
<tr class="aste01">
<td class="celltext">{VAR:LC_FORMS_WH_MENU_LINK_IS}:</td>
<td class="celltext"><select class='small_button' name='l_section'>{VAR:sec}</select></td>
</tr>
<tr class="aste01">
<td class="celltext">Kas saata meil ainult sisestuse loomisel:</td>
<td class="celltext"><input type="checkbox" name="no_mail_on_change" value="1" {VAR:no_mail_on_change}></td>
</tr>
<tr class="aste01">
<td class="celltext">Kas link viitab sisestuse muutmisele:</td>
<td class="celltext"><input type="checkbox" name="link_to_change" value="1" {VAR:link_to_change}></td>
</tr>
<tr class="aste01">
<td class="celltext">Lingi tekst (kui see on t&auml;idetud, siis saadetakse HTML mail):</td>
<td class="celltext"><input type="text" name="link_caption" value="{VAR:link_caption}"></td>
</tr>
 <tr class="aste01">
<td class="celltext">Saada alati tekstip&otilde;hine e-mail:</td>
<td class="celltext"><input type="checkbox" name="text_only" value="1" {VAR:text_only}></td>
</tr>
<tr class="aste01">
<td class="celltext">E-maili "from" aadress v&otilde;ta elemendist:</td>
<td class="celltext"><select name="from_email_el" class="formselect">{VAR:from_email_el}</select></td>
</tr>

<tr class="aste01">
<td class="celltext">Lisa mailile PDF formaadis sisu:</td>
<td class="celltext"><input type="checkbox" name="add_pdf" value="1" {VAR:add_pdf}></td>
</tr>
<tr class="aste01">
<td class="celltext">Saada ka e-mailile, mis on elemendis:</td>
<td class="celltext"><select name="email_el" class="formselect">{VAR:email_el}</select></td>
</tr>
<tr class="aste01">
<td class="celltext">&Auml;ra lisa kasutaja info linki:</td>
<td class="celltext"><input type="checkbox" value="1" {VAR:no_user_info_link} name="no_user_info_link" ></td>
</tr>
<tr class="aste01">
<td class="celltext">HTML Meil:</td>
<td class="celltext"><input type="checkbox" value="1" {VAR:send_html_mail} name="send_html_mail" ></td>
</tr>
<tr class="aste01">
<td class="celltext">Maili From aadress:</td>
<td class="celltext"><input type="textbox" value="{VAR:from_addr}" name="from_addr" ></td>
</tr>
<tr class="aste01">
<td class="celltext">Maili From nimi:</td>
<td class="celltext"><input type="textbox" value="{VAR:from_name}" name="from_name" ></td>
</tr>
<tr class="aste01">
<td></td>
<td class="celltext"><input type='submit' class='formbutton' VALUE='{VAR:LC_FORMS_SAVE}'></td>
</tr>
</table>
{VAR:reforb}
</form>
