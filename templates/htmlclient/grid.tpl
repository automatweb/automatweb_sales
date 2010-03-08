<table border="0" cellspacing="0" cellpadding="0" width="100%">
<!-- SUB: GRID_ROW -->
	<tr>
		<!-- SUB: GRID_CELL -->
		<td valign="top" align="center" colspan="{VAR:colspan}" rowspan="{VAR:rowspan}">
			<div class="aw04contentcellleft">{VAR:caption}</div>
			<div class="aw04contentcellright">{VAR:element}</div>
		</td>
		<!-- END SUB: GRID_CELL -->
		
		<!-- SUB: GRID_CELL_NO_CAPTION -->
		<td valign="top" align="center" colspan="{VAR:colspan}" rowspan="{VAR:rowspan}">
			<div class="aw04contentcellright">{VAR:element}</div>
		</td>
		<!-- END SUB: GRID_CELL_NO_CAPTION -->

		<!-- SUB: GRID_EMPTY_CELL -->
		<td valign="top">
		this cell intentionally left empty
		</td>
		<!-- END SUB: GRID_EMPTY_CELL -->
	</tr>
<!-- END SUB: GRID_ROW -->
</table>
