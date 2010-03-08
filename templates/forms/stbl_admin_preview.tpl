<table border="1">
<tr>
		<td class="fform" colspan="{VAR:sp_colspan}" rowspan="{VAR:sp_rowspan}">&nbsp;</td>
		<td class="fform" >Vali tulpade grupeerimiselemendid</td>
</tr>
<!-- SUB: C_LINE -->
<tr>
	<!-- SUB: C_COL -->
	<td class="fform" colspan="{VAR:colspan}"><select class='small_button' name='col_el[{VAR:num}]'>{VAR:content}</select></td>
	<!-- END SUB: C_COL -->
</tr>
<!-- END SUB: C_LINE -->

<tr>
	<!-- SUB: FIRST_R -->
	<td rowspan="{VAR:r_rowspan}" class="fform">Vali ridade grupeerimiselemendid</td>
	<!-- END SUB: FIRST_R -->

	<!-- SUB: R_LINE -->

	<!-- SUB: R_COL -->
	<td rowspan="{VAR:rc_rowspan}" class="fform"><select class="small_button" name="row_el[{VAR:num}]">{VAR:content}</select></td>
	<!-- END SUB: R_COL -->

	<!-- END SUB: R_LINE -->

	<!-- SUB: DAT_COL -->
	<td class="fform">{VAR:content}</td>
	<!-- END SUB: DAT_COL -->
</tr>
</table>