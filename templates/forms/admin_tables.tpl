<table width="100%" cellpadding=1 cellspacing=0 border=0>

<form action='reforb.{VAR:ext}' method=post>

<tr>
<td bgcolor="#FFFFFF">

<table width="100%" border=0 cellspacing=0 cellpadding=5>
<tr>
<td class="aste01">

<font size="+2" color="red">{VAR:status_msg}</font>

<table cellpadding=3 cellspacing=1 border=0>
<td class="celltext" colspan="2"><input class='formbutton' type='submit' VALUE='{VAR:LC_FORMS_SAVE}'></td>
</tr>
<tr>
<td class="celltext" colspan="2"><b>{VAR:LC_FORMS_MINGI_HOIATAV_JUTT}!</b>
</td>
</tr>
<tr>
<td class="celltext" align="right" width="50%">{VAR:LC_FORMS_SAVE_DATA_TO_OTHER_TABLE}:</td>
<td class="celltext" width="50%"><input type='checkbox' NAME='save_table' VALUE='1' {VAR:save_table}></td>
</tr>
<tr>
<td colspan="2" class="celltext">{VAR:LC_FORMS_CHOOSE_TABLES_WH_CAN_SAVE_DATA}:</td>
</tr>
<tr>
<td colspan="2" class="celltext"><select class="formselect2" name="tables[]" size=20 multiple>{VAR:tables}</select></td>
</tr>
<!-- SUB: SOME_TABLES -->
<tr>
	<td class="celltext" width="50%" align="right">{VAR:LC_FORMS_WHERE_MAKE_OBJ}:</td>
	<td class="celltext" width="50%"><select name='objs_where' class='formselect2'>{VAR:objs_sel}</select></td>
</tr>
<!-- SUB: OBJ_SEL -->
<tr>
	<td class="celltext" width="50%" align="right">{VAR:LC_FORMS_WH_COLUMN_OBJ_ID}:</td>
	<td class="celltext" width="50%"><select name='obj_column' class='formselect2'>{VAR:obj_column}</select></td>
</tr>
<!-- END SUB: OBJ_SEL -->

<!-- END SUB: SOME_TABLES -->

<!-- SUB: TABLE -->
<tr>
	<td class="celltext" colspan="2">
		<br><br>
		<table cellpadding="3" cellspacing="1" border="0" width="100%">
			<tr>
				<td class="celltext" width="50%" align="right">{VAR:LC_FORMS_TABLE}:</td>
				<td class="celltext" width="50%">{VAR:usr_table_name}</td>
			</tr>
			<tr>
				<td class="celltext" width="50%" align="right">{VAR:LC_FORMS_UNIC_COL_IN_TABLE}:</td>
				<td class="celltext" width="50%"><select name="indexes[{VAR:table_name}]" class="formselect2">{VAR:cols}</select></td>
			</tr>
			<!-- SUB: HAS_OTHER_TABLES -->
			<tr>
				<td class="celltext" width="50%" align="right">{VAR:LC_FORMS_CHOOSE_TABLES_WH_RELATED}:</td>
				<td class="celltext" width="50%"><select class="formselect2" name='relations[{VAR:table_name}][]' multiple>{VAR:rel_tbls}</select></td>
			</tr>
			<!-- SUB: REL_TABLE -->
			<tr>
				<td class="celltext" colspan="2" align="center">{VAR:LC_FORMS_COLUMN} <select class="formselect2" name='rel_cols[{VAR:table_name}][{VAR:foreign_table}][from]'>{VAR:rel_f_cols}</select> {VAR:LC_FORMS_THIS_TABLE_REAL_COL} <select class="formselect2" name='rel_cols[{VAR:table_name}][{VAR:foreign_table}][to]'>{VAR:rel_t_cols}</select> tabelist {VAR:foreign_table}</td>
			</tr>
			<!-- END SUB: REL_TABLE -->

			<!-- END SUB: HAS_OTHER_TABLES -->
		</table>
	</td>
</tr>
<!-- END SUB: TABLE -->
<tr>
<td class="celltext" colspan="2"><input class='formbutton' type='submit' VALUE='{VAR:LC_FORMS_SAVE}'></td>
</tr>
</table>
{VAR:reforb}

</td></tr></table>
</td></tr>

</form>

</table>

<br>
