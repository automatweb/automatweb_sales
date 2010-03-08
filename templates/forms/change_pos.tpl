<form method=POST action='reforb.{VAR:ext}'>
<table border=0 cellspacing=1 cellpadding=2 bgcolor="#CCCCCC">
<tr>
	<td colspan=2 class="fform">{VAR:LC_FORMS_CHOOSE_CAT_WHERE_IS_ELEMENT}:</td>
</tr>
<tr>
	<td colspan=2 class="fform"><select name='parent' class='small_button'>{VAR:folders}</select></td>
</tr>
<tr>
	<td class="fform">{VAR:LC_FORMS_ELEMENT_NAME}:</td>
	<td class="fform"><input type="text" name="name" value="{VAR:name}"></td>
</tr>
<tr>
	<td class="fform" colspan=2> {VAR:LC_FORMS_CHOOSE_CELL_WHERE_ELEMENT}:</td>
</tr>
<tr>
	<td class="fform" colspan=2>
		<table border=1 cellpadding=0 cellspacing=0>
			<tr>
				<td class="fform">&nbsp;</td>
				<!-- SUB: COLNUM -->
				<td class="fform" align="center">{VAR:col}</td>
				<!-- END SUB: COLNUM -->
			</tr>
			<!-- SUB: ROW -->
			<tr>
				<td class="fform">{VAR:drow}</td>
				<!-- SUB: COL -->
					<td class="fform"><input type='radio' name='s_cell' value='{VAR:row}_{VAR:col}' {VAR:checked}></td>
				<!-- END SUB: COL -->
			</tr>
			<!-- END SUB: ROW -->
		</table>
	</td>
</tr>
<tr>
	<td class="fform" colspan=2>{VAR:LC_FORMS_CHOOSE_CELL_WHERE_ELMENT_COPY}:</td>
</tr>
<tr>
	<td class="fform" colspan=2>
		<table border=1 cellpadding=0 cellspacing=0>
			<tr>
				<td class="fform">&nbsp;</td>
				<!-- SUB: COLNUMC -->
				<td class="fform" align="center">{VAR:col}</td>
				<!-- END SUB: COLNUMC -->
			</tr>
			<!-- SUB: ROWC -->
			<tr>
				<td class="fform">{VAR:drow}</td>
				<!-- SUB: COLC -->
					<td class="fform"><input type='checkbox' name='c_cell[{VAR:cnt}]' value='{VAR:row}_{VAR:col}'}></td>
				<!-- END SUB: COLC -->
			</tr>
			<!-- END SUB: ROWC -->
		</table>
	</td>
</tr>
<tr>
	<td class="fform">{VAR:LC_FORMS_HOW_MANY_EL_MAKE}:</td>
	<td class="fform"><input type='text' name='el_count' value='1' size=3></td>
</tr>
<tr>
	<td class="fform" colspan="3" align="center">
	{VAR:reforb}
	<input type="submit" class='small_button' value="{VAR:LC_FORMS_SAVE}">
	</td>
</tr>
</table>
</form>
