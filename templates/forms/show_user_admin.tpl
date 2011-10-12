<table border="0" cellspacing="0" cellpadding="0">
<tr>
<td bgcolor="#CCCCCC">

<table border="0" cellspacing="1" cellpadding="2">
<tr>
<td height="15" colspan="15" class="fgtitle">&nbsp;<b>{VAR:LC_FORMS_BIG_ORDER}:</b></td>
</tr>
<tr bgcolor="#C9EFEF">
<td class="title" align=center colspan=2>{VAR:LC_FORMS_ACTION}</td>
<!-- SUB: ACTIONS -->
<td class="title" align=center colspan={VAR:colspan}>{VAR:action_name}</td>
<!-- END SUB: ACTIONS -->
</tr>
<tr>
<td class="plain"><a href="javascript:box2('{VAR:LC_FORMS_ARE_YOU_SURE_DEL_ENT}?','forms{VAR:ext}?type=delete_entry&id={VAR:form_id}&entry_id={VAR:entry_id}&parent={VAR:parent}&op_id={VAR:op_id}')">{VAR:LC_FORMS_DELETE}</a></td>
<td class="plain"><a href='forms{VAR:ext}?type=change_entry&id={VAR:form_id}&entry_id={VAR:entry_id}&parent={VAR:parent}&op_id={VAR:op_id}'>{VAR:LC_FORMS_CHANGE}</a></td>
<!-- SUB: ACTION_LINE -->
<td class="plain" align="center"><a href='forms{VAR:ext}?type=move_filled&from={VAR:entry_id}&to={VAR:action_target}&id={VAR:form_id}&parent={VAR:parent}&entry_id={VAR:entry_id}&op_id={VAR:op_id}'>{VAR:action_target_name}</a></td>
<!-- END SUB: ACTION_LINE -->
</tr>
</table></td></tr></table>
<br>

<table{VAR:form_border}{VAR:form_bgcolor}{VAR:form_cellpadding}{VAR:form_cellspacing}{VAR:form_height}{VAR:form_width}{VAR:form_hspace}{VAR:form_vspace}>
<!-- SUB: LINE -->
<tr>
<!-- SUB: COL -->
<td{VAR:cell_bgcolor}{VAR:cell_align}{VAR:cell_valign}{VAR:cell_height}{VAR:cell_width}{VAR:cell_nowrap}colspan={VAR:colspan} rowspan={VAR:rowspan}>{VAR:elements}</td>
<!-- END SUB: COL -->
</tr>
<!-- END SUB: LINE -->
</table>
