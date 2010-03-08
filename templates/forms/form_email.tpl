 <form action='reforb.{VAR:ext}' method=post>
<table border=0 cellspacing=0 cellpadding=2>
<tr><td class="aste01">

<table border=0 cellspacing=0 cellpadding=3>
<tr>

<td class="celltext" align="right">Vali vorm, kust meiliaadresse võtta:</td>
<td class="celltext"><select name="srcform" class="formselect2">{VAR:srcforms}</select></td>
</tr>
<tr>
<td class="celltext" align="right">Vormi element, milles meiliaadress(id) asuvad:</td>
<td class="celltext"><select name="srcfield" class="formselect2">{VAR:srcfields}</select></td>
</tr>
<tr>
<td class="celltext" align="right">Millist väljundit kasutada:</td>
<td class="celltext"><select name="output" class="formselect2">{VAR:outputs}</select></td>
</tr>
<tr>
<td class="celltext" align="right">Bind to submit button:</td>
<td class="celltext"><select name="sbt_bind" class="formselect2">{VAR:sbt_binds}</select></td>
</tr>
<tr>
<td class="celltext" align="right">Kirja subjekt:</td>
<td class="celltext"><input type="text" name="subject" value="{VAR:subject}" size="30" class="formtext"></td>
</tr>
<tr>
<td class="celltext" align="right">From:</td>
<td class="celltext"><input type="text" name="from" value="{VAR:from}" size="30" class="formtext"></td>
</tr>
<tr>
<td></td>
<td class="celltext"><input type='submit' NAME='save_form_actions' VALUE='{VAR:LC_FORMS_SAVE}' class="formbutton"></td>
</tr>
</table>
{VAR:reforb}
</td></tr></table>
</form>
