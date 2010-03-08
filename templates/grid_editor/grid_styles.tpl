
<table border=1 cellspacing=1 cellpadding=2 {VAR:tb_style}>
	<tr>
		<!-- SUB: DC -->
			<td bgcolor="#FFFFFF" class="celltext">
				<input type='checkbox' NAME='dc_{VAR:col}' value=1>
			</td>
		<!-- END SUB: DC -->
		<td bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<!-- SUB: LINE -->
	<tr>
		<!-- SUB: COL -->
		<td {VAR:td_style}>
			{VAR:style_icon}<input type="checkbox" name="sel_row={VAR:row};col={VAR:col}">{VAR:content}
		</td>
		<!-- END SUB: COL -->

		<td bgcolor=#ffffff valign=bottom align=left>
			<input type='checkbox' NAME='dr_{VAR:row}' value=1><br>
		</td>
	</tr>
	<!-- END SUB: LINE -->
</table>


<input type="hidden" name="ge_action" value="">
