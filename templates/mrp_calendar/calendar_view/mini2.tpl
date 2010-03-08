<!--<div class="minical_table">{VAR:caption}</div>-->

<table border="0" cellpadding="0" cellspacing="1" width="100%">

<!-- SUB: header -->
<tr>
	<!-- SUB: header_cell -->
	<td valign="middle" align="center">
	{VAR:header_content}
	</td>
	<!-- END SUB: header_cell -->
</tr>
<!-- END SUB: header -->

<!-- SUB: line -->
<tr>
	<!-- SUB: cell -->
	<td class="{VAR:daycell_style}">
	<a href="{VAR:day_url}">{VAR:daynum}</a>
	</td>
	<!-- END SUB: cell -->

	<!-- SUB: cell_deact -->
	<td class="minical_cell_deact">
	{VAR:daynum}
	</td>
	<!-- END SUB: cell_deact -->
</tr>
<!-- END SUB: line -->
</table>

