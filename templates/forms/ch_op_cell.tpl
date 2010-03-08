<form action=reforb.{VAR:ext} method=post>
<input class='small_sub' type='submit' NAME='save' VALUE='{VAR:LC_FORMS_SAVE}!'>&nbsp;&nbsp;&nbsp;
<table border=0 bgcolor=#f0f0f0>
<!-- SUB: ELEMENT -->
<tr>
<td align=right class=fgen_text>{VAR:LC_FORMS_ELEMENT}:</td>
<td><select class='small_button' name='elsel_{VAR:element_id}'>{VAR:elsel}</select></td>
</tr>
<!-- END SUB: ELEMENT -->
<tr>
<td align=right class=fgen_text>{VAR:LC_FORMS_STYLE}:</td>
<td class=fgen_text><select class='small_button' name='stylesel_{VAR:cell_id}'><option value=''>{VAR:stylesel}</select></td>
</tr>
</table>
<input class='small_sub' type='submit' NAME='save' VALUE='{VAR:LC_FORMS_SAVE}!'>
{VAR:reforb}
</form>