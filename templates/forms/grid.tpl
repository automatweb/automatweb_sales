<form action='reforb{VAR:ext}' METHOD=post>
<input class='small_sub' type='submit' NAME='save' VALUE='{VAR:LC_FORMS_SAVE}!'>
<table border=0>
<tr>
<td bgcolor=#d0d0d0>
<table bgcolor=#ffffff border=0>
<tr>
<!-- SUB: DC -->
<td bgcolor=#ffffff align=left valign=bottom>
<table width=100%>
	<tr>
		<!-- SUB: FIRST_C -->
		<td align=left valign=bottom><a href='{VAR:add_col}'><img src='/automatweb/images/add_col_first.gif' border=0></a></td>
		<!-- END SUB: FIRST_C -->

		<!-- SUB: DELETE_COL -->
		<td align=right valign=bottom><input type='checkbox' NAME='dc_{VAR:form_col}' value=1>&nbsp;<a href="javascript:box2('{VAR:LC_FORMS_ARE_YOU_SURE_DEL_COLUMN}?','{VAR:del_col}')"><img src='/automatweb/images/del_col.gif' border=0></a></td>
		<!-- END SUB: DELETE_COL -->
		<td align=right valign=bottom><a href='{VAR:add_col}'><img src='/automatweb/images/add_col.gif' border=0></a></td>
	</tr>
</table>
</td>
<!-- END SUB: DC -->
<td>
<input class='small_sub' type='submit' NAME='save' VALUE='{VAR:LC_FORMS_SAVE}!'>
</td>
</tr>
<!-- SUB: LINE -->
<tr>
<!-- SUB: COL -->
<td bgcolor=#d0d0d0 valign=bottom align=left rowspan={VAR:rowspan} colspan={VAR:colspan}>
<table bgcolor=#f0f0f0 width=100% height=100% border=0>
<tr>
<!-- SUB: EXP_LEFT -->
<td bgcolor=#ffffff rowspan={VAR:num_els_plus3}><a href='{VAR:exp_left}'><img border=0 alt='Kustuta vasak cell' src='/automatweb/images/left_r_arr.gif'></a></td>
<!-- END SUB: EXP_LEFT -->

<td colspan=5 align=center height=5 bgcolor=#ffffff>
<!-- SUB: EXP_UP -->
<a href='{VAR:exp_up}'><img border=0 alt='Kustuta &uuml;lemine  cell' src='/automatweb/images/up_r_arr.gif'></a>
<!-- END SUB: EXP_UP -->
&nbsp;</td>
<!-- SUB: EXP_RIGHT -->
<td bgcolor=#ffffff rowspan={VAR:num_els_plus3}>
<a href='{VAR:exp_right}'><img border=0 alt='Kustuta parem cell' src='/automatweb/images/right_r_arr.gif'></a>
</td>
<!-- END SUB: EXP_RIGHT -->

</tr>
<tr>

<td class='fgen_text' colspan=5>
<a href='{VAR:admin_cell}'>{VAR:LC_FORMS_TOIMETA}</a>
|
<a href='{VAR:add_element}'>{VAR:LC_FORMS_ADD_ELEMENT}</a>
<!-- SUB: SPLIT_VERTICAL -->
&nbsp;| <a href='{VAR:split_ver}'><img alt='Jaga cell pooleks vertikaalselt' src='/automatweb/images/split_cell_left.gif' border=0></a>&nbsp;
<!-- END SUB: SPLIT_VERTICAL -->

<!-- SUB: SPLIT_HORIZONTAL -->
&nbsp;| <a href='{VAR:split_hor}'><img alt='Jaga cell pooleks horisontaalselt' src='/automatweb/images/split_cell_down.gif' border=0></a>
<!-- END SUB: SPLIT_HORIZONTAL -->

</td>

</tr>
<tr>
	<td bgcolor=#ffffff align=left class='fgen_text'><b>Grp</b></td>
	<td   bgcolor=#ffffff class='fgen_text'><b>{VAR:LC_FORMS_NAME}</b></td>
	<td   bgcolor=#ffffff class='fgen_text'><b>{VAR:LC_FORMS_TYPE}</b></td>
	<td   bgcolor=#ffffff align=left class='fgen_text' colspan=2><b>{VAR:LC_FORMS_TEXT}</b></td>
</tr>
<!-- SUB: ELEMENT -->
<tr>
	<td   bgcolor=#ffffff ><input class='tekstikast_n' size=2 type='text' NAME='element_{VAR:element_id}_grp' VALUE='{VAR:form_cell_grp}'></td>
	<td   bgcolor=#ffffff class='fgen_text'><input class='tekstikast_n' size=20 type='text' NAME='element_{VAR:element_id}_name' VALUE='{VAR:el_name}'></td>
	<td  bgcolor=#ffffff class='fgen_text'>{VAR:el_type}</td>
	<td  bgcolor=#ffffff  colspan=2><input class='tekstikast_n' size=15 type='text' NAME='element_{VAR:element_id}_text' VALUE='{VAR:form_cell_text}'><span class='fgen_text'><a href='{VAR:chpos}'>M</a><input type='checkbox' name='sel[]' value='{VAR:element_id}'></span></td>
</tr>
<!-- END SUB: ELEMENT -->

<!-- SUB: ELEMENT_NOEDIT -->
<tr>
	<td bgcolor=#ffffff >{VAR:form_cell_text}&nbsp;&nbsp;{VAR:form_cell_order}</td>
	<td bgcolor=#ffffff  align=right class='fgen_text'>{VAR:}:</td>
</tr>
<!-- END SUB: ELEMENT_NOEDIT -->
<tr>
<td  bgcolor=#ffffff colspan=5 align=center>
<!-- SUB: EXP_DOWN -->
<a href='{VAR:exp_down}'><img border=0 alt='Kustuta alumine cell' src='/automatweb/images/down_r_arr.gif'></a>
<!-- END SUB: EXP_DOWN -->
&nbsp;</td></tr>
</table>
</td>
<!-- END SUB: COL -->
<td bgcolor=#ffffff valign=bottom align=left>
<table height=100% border=0 cellspacing=0 cellpadding=0 hspace=0 vspace=0>
<!-- SUB: FIRST_R -->
<tr><td valign=top><a href='{VAR:add_row}'><img src='/automatweb/images/add_row_first.gif' BORDER=0></a></td></tr>
<!-- END SUB: FIRST_R -->

<!-- SUB: DELETE_ROW -->
<tr><td valign=bottom><a href="javascript:box2('{VAR:LC_FORMS_ARE_YOU_SURE_DEL_ROW}?','{VAR:del_row}')"><img src='/automatweb/images/del_row.gif' BORDER=0></a><input type='checkbox' NAME='dr_{VAR:cell_row}' value=1></td></tr>
<!-- END SUB: DELETE_ROW -->
<tr><td valign=bottom><a href='{VAR:add_row}'><img src='/automatweb/images/add_row.gif' BORDER=0></a></td></tr>
</table>
</td>
</tr>
<!-- END SUB: LINE -->
</table>
</td>
</tr>
</table>
<table width="100%">
<tr>
<td><input class='small_sub' type='submit' NAME='save' VALUE='{VAR:LC_FORMS_SAVE}!'></td>
<td align="right"><input class='small_sub' type='submit' NAME='save' VALUE='{VAR:LC_FORMS_SAVE}!'></td>
</tr>
</table>
{VAR:reforb}
</form>

<form action="reforb{VAR:ext}" METHOD="POST">
{VAR:addr_reforb}
<input type="submit" class="small_sub" VALUE="{VAR:LC_FORMS_ADD}"> <input type="text" NAME="count" size=2> {VAR:LC_FORMS_ROW}
</form>

<form action="reforb{VAR:ext}" METHOD="POST">
{VAR:addc_reforb}
<input type="submit" class="small_sub" VALUE="{VAR:LC_FORMS_ADD}"> <input type="text" NAME="count" size=2> {VAR:LC_FORMS_COLUMN} 
</form>

