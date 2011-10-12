<form action='reforb{VAR:ext}' METHOD=post>
Lehek&uuml;lg: 
<!-- SUB: PAGE -->
<a href='{VAR:pageurl}'>{VAR:from} - {VAR:to}</a> |
<!-- END SUB: PAGE -->

<!-- SUB: SEL_PAGE -->
{VAR:from} - {VAR:to} |
<!-- END SUB: SEL_PAGE -->

<br>Vali millist filtrit kasutatakse <!-- proposed LC_ constant: LC_FORMS_CHOOSE_WHTA_FUCKA_FORM_FILL_FLTER}-->:<br>
<table bgcolor="#CCCCCC" cellpadding=3 cellspacing=1 border=0>
<tr bgcolor="#C9EFEF">
<td class="title">ID</td>
<td class="title">{VAR:LC_FORMS_NAME}</td>
<td class="title">{VAR:LC_FORMS_COMMENT}</td>
<td class="title">{VAR:LC_FORMS_POSITION}</td>
<td class="title">Vali</td>
</tr>

<!-- SUB: LINE -->
<tr>
<td class="plain">{VAR:flt_id}</td>
<td class="plain">{VAR:flt_name}</td>
<td class="plain">{VAR:flt_comment}</td>
<td class="plain">{VAR:flt_location}</td>
<td class="chkbox"><input type='radio' NAME='sel' VALUE='{VAR:flt_id}' {VAR:checked}>
<input type='hidden' name='inpage[{VAR:flt_id}]' value='1'>
</td>
</tr>
<!-- END SUB: LINE -->
</table>
<input type=submit NAME='save' VALUE='{VAR:LC_FORMS_SAVE}' class="small_button">
{VAR:reforb}
</form>
    
