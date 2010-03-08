<br>
<br>
<table border=0 cellspacing=1 bgcolor=#cccccc cellpadding=2>
<tr>
<td class=title>{VAR:LC_FORMS_NAME}</td>
<td class=title>{VAR:LC_FORMS_COMMENT}</td>
<td class=title colspan=2 align=center>{VAR:LC_FORMS_ACTION}</td>
</tr>
<!-- SUB: LINE -->
<tr>
<td class=plain>{VAR:name}</td>
<td class=plain>{VAR:comment}</td>
<td class=plain>
<!-- SUB: CHANGE_OP -->
<a href='{VAR:change}'>{VAR:LC_FORMS_CHANGE}</a>
<!-- END SUB: CHANGE_OP -->
&nbsp;</td>
<td class=plain>
<!-- SUB: DELETE_OP -->
<a href="javascript:box2('{VAR:LC_FORMS_ARE_YOU_SURE_DEL_OUTPUT}?','{VAR:delete}')">{VAR:LC_FORMS_DELETE}</a>
<!-- END SUB: DELETE_OP -->
&nbsp;</td>
</tr>
<!-- END SUB: LINE -->
<tr>
<td class=plain colspan=4 align=center>
<!-- SUB: ADD_OP -->
<a href='{VAR:add}'>{VAR:LC_FORMS_ADD}</a>
<!-- END SUB: ADD_OP -->
</td>
</tr>
</table>
