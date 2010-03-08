<form action="reforb.{VAR:ext}" method="post">
<input class='small_sub' type='submit' NAME='save' VALUE='{VAR:LC_FORMS_SAVE}!'>&nbsp;&nbsp;&nbsp;
<a href='{VAR:change}'>{VAR:LC_FORMS_OUTPUT_SETTINGS}</a> | 
<!-- SUB: PREVIEW -->
<a href='{VAR:preview}'>{VAR:LC_FORMS_PREVIEW}</a> | 
<!-- END SUB: PREVIEW -->

<!-- SUB: ALIASMGR -->
<a href='{VAR:aliasmgr}'>{VAR:LC_FORMS_ALIASMGR}</a> | 
<!-- END SUB: ALIASMGR -->

<a href='{VAR:translate}'>{VAR:LC_FORMS_TRANSLATE}</a>

<table border=0>
<tr>
<td bgcolor="#d0d0d0">
<table border=0 bgcolor="#f0f0f0">
<tr>
<!-- SUB: DC -->
<td bgcolor="#ffffff" align=left valign=bottom>
<table width=100% border=0>
<tr>
<!-- SUB: FIRST_C -->
<td align=left valign=bottom><a href='{VAR:add_col}'><img src='/automatweb/images/add_col_first.gif' border=0></a></td>
<!-- END SUB: FIRST_C -->
<td align=right valign=bottom><a href='{VAR:del_col}'><img src='/automatweb/images/del_col.gif' border=0></a></td>
<td align=right valign=bottom><a href='{VAR:add_col}'><img src='/automatweb/images/add_col.gif' border=0></a></td>
</tr>
</table>
</td>
<!-- END SUB: DC -->
</tr>
<!-- SUB: LINE -->
<tr>
<!-- SUB: COL -->
<!-- col begins -->
<td bgcolor=#d0d0d0 colspan="{VAR:colspan}" rowspan="{VAR:rowspan}">
<table border=0 bgcolor=#ffffff height=100% width=100% hspace=0 vspace=0 cellpadding=2 cellspacing=0>
<tr>
<td rowspan={VAR:num_els_plus3}>&nbsp;
<!-- SUB: EXP_LEFT -->
<a href='{VAR:exp_left}'><img border=0 alt='{VAR:LC_FORMS_DELETE_LEFT_CELL}' src='{VAR:baseurl}/automatweb/images/left_r_arr.gif'></a>
<!-- END SUB: EXP_LEFT -->
</td>
<td colspan=2 align=center>&nbsp;
<!-- SUB: EXP_UP -->
<a href='{VAR:exp_up}'><img border=0 alt='{VAR:LC_FORMS_DELETE_UP_CELL}' src='{VAR:baseurl}/automatweb/images/up_r_arr.gif'></a>
<!-- END SUB: EXP_UP -->
</td>
<td rowspan={VAR:num_els_plus3}>&nbsp;
<!-- SUB: EXP_RIGHT -->
<a href='{VAR:exp_right}'><img border=0 alt='{VAR:LC_FORMS_DELETE_RIGHT_CELL}' src='{VAR:baseurl}/automatweb/images/right_r_arr.gif'></a>
<!-- END SUB: EXP_RIGHT -->
</td>
</tr>
<tr>
<td class="fgen_text" colspan=2><a href='{VAR:ch_cell}'>{VAR:LC_FORMS_CHANGE}</a> | <a href='{VAR:addel}'>{VAR:LC_FORMS_ADD_ELEMENT}</a></td>
</tr>
<tr>
<td class=fgen_text>{VAR:LC_FORM_NAME}</td>
<td class=fgen_text>{VAR:LC_FORMS_TEXT}</td>
</tr>
<!-- SUB: ELEMENT -->
<tr>
<td align=right class=fgen_text><input class='tekstikast_n' size=15 type='text' NAME='names[{VAR:row}][{VAR:col}][{VAR:el_cnt}]' VALUE='{VAR:el_name}'></td>
<td><input class='tekstikast_n' size=15 type='text' NAME='texts[{VAR:row}][{VAR:col}][{VAR:el_cnt}]' VALUE='{VAR:el_text}'>&nbsp;<input type='checkbox' name='elsel[{VAR:row}][{VAR:col}][{VAR:el_cnt}]' value=1></td>
</tr>
<!-- END SUB: ELEMENT -->
<tr>
<td align=right class=fgen_text>{VAR:LC_FORMS_STYLE}:</td>
<td class=fgen_text>{VAR:style_name} <input type='checkbox' name='sel[{VAR:row}][{VAR:col}]' value='1'>
</td>
</tr>
<!-- SUB: SPLITS -->
<tr>
<td colspan=2 align="center">&nbsp;
<!-- SUB: SPLIT_VERTICAL -->
&nbsp;| <a href='{VAR:split_ver}'><img alt='{VAR:LC_FORMS_DEV_CELL_VERT}' src='/automatweb/images/split_cell_left.gif' border=0></a>&nbsp;
<!-- END SUB: SPLIT_VERTICAL -->

<!-- SUB: SPLIT_HORIZONTAL -->
&nbsp;| <a href='{VAR:split_hor}'><img alt='{VAR:LC_FORMS_DEV_CELL_HOR}' src='/automatweb/images/split_cell_down.gif' border=0></a>
<!-- END SUB: SPLIT_HORIZONTAL -->
</td>
</tr>
<!-- END SUB: SPLITS -->
<tr>
<td colspan=2 align=center>&nbsp;
<!-- SUB: EXP_DOWN -->
<a href='{VAR:exp_down}'><img border=0 alt='{VAR:LC_FORMS_DELETE_LOWER_CELL}' src='/automatweb/images/down_r_arr.gif'></a>
<!-- END SUB: EXP_DOWN -->
</td></tr>
</table>
</td>
<!-- col ends -->
<!-- END SUB: COL -->
<td bgcolor=#ffffff valign=bottom align=left>
<table height=100% border=0 cellspacing=0 cellpadding=0 hspace=0 vspace=0>
<!-- SUB: FIRST_R -->
<tr><td valign=top><a href='{VAR:add_row}'><img src='/automatweb/images/add_row_first.gif' BORDER=0></a></td></tr>
<!-- END SUB: FIRST_R -->
<tr><td valign=bottom><a href='{VAR:del_row}'><img src='/automatweb/images/del_row.gif' BORDER=0></a></td></tr>
<tr><td valign=bottom><a href='{VAR:add_row}'><img src='/automatweb/images/add_row.gif' BORDER=0></a></td></tr>
</table>
</td>
</tr>
<!-- END SUB: LINE -->
</table></td></tr></table>
Vali stiil: <select name='selstyle' >{VAR:styles}</select><br>
{VAR:LC_FORMS_CHOOSE_CALALOGUE_WHERE_MOVE_ELEMENT}:<select name='setfolder' class='small_button'>{VAR:folders}</select><br>
Vali elementidele m&auml;&auml;ratav css stiil:<select name='setcss' class='small_button'>{VAR:css_styles}</select><br>
<input class='small_sub' type='submit' NAME='save' VALUE='{VAR:LC_FORMS_SAVE}!'>
&nbsp;&nbsp;<input type='submit' name='diliit' value='Kustuta' class='small_button'>
{VAR:reforb}
</form>
<form action='reforb.{VAR:ext}' method=POST>
<input type='submit' class='small_sub' value='{VAR:LC_FORMS_ADD}'> <input type='text' name='nrows' size=3 class='small_button'> {VAR:LC_FORMS_ROW_ROW} 
{VAR:addr_reforb}
</form>
<form action='reforb.{VAR:ext}' method=POST>
<input type='submit' class='small_sub' value='{VAR:LC_FORMS_ADD}'> <input type='text' name='ncols' size=3 class='small_button'> {VAR:LC_FORMS_COLUMN} 
{VAR:addc_reforb}
</form>
