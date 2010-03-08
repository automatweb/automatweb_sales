<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr><td valign="top" class="caltableborderhele">

			
<table border="0" cellspacing="1" cellpadding="0" width="100%" height="100%">

<!-- SUB: header -->
<tr>
	<!-- header cells are repeated for each column -->

	<!-- SUB: header_cell -->
			<td width="{VAR:cellwidth}">

				<table width="100%"  border="0" cellpadding="0" cellspacing="0">
				<tr>
				<td class="caldayheadday">&nbsp;<a href="{VAR:dayorblink}">{VAR:hcell_weekday}</a></td>
				<td class="caldayheaddate"><!--<a href="{VAR:dayorblink}">{VAR:hcell_date}</a>&nbsp;--></td>
				</tr>
				</table>
			</td>
			<!--<td>{VAR:hcell}</td>-->
	<!-- END SUB: header_cell -->
</tr>
<!-- END SUB: header -->

<!-- SUB: content_row -->
<tr>
	<!-- SUB: content_cell -->
			<td width="{VAR:cellwidth}" bgcolor="#FFFFFF" valign="top">
				<table width="100%" border="0" cellpadding="0" cellspacing="5">
				<tr><td>
				<div align="right" style="font-family: Verdana,serif; font-weight: bold; color: #000; font-size: 12px;"><a href="{VAR:dayorblink}">{VAR:daynum}</a></div>
				{VAR:cell}
				</td></tr></table>

			</td>
	<!-- END SUB: content_cell -->
	<!-- SUB: content_cell_today -->
			<td width="{VAR:cellwidth}" bgcolor="#EEEEEE" valign="top">
				<table width="100%" border="0" cellpadding="0" cellspacing="5">
				<tr><td>
				<div align="right" style="font-family: Verdana,serif; font-weight: bold; color: #000; font-size: 12px;"><a href="{VAR:dayorblink}">{VAR:daynum}</a></div>
				{VAR:cell}
				</td></tr></table>

			</td>
	<!-- END SUB: content_cell_today -->
	<!-- SUB: content_cell_today_empty -->
			<td width="{VAR:cellwidth}" bgcolor="#EEEEEE" valign="top">
				<table width="100%" border="0" cellpadding="0" cellspacing="5">
				<tr><td>
				<div align="right" style="font-family: Verdana,serif; font-weight: bold; color: #000; font-size: 12px;"><a href="{VAR:dayorblink}">{VAR:daynum}</a></div>
				{VAR:cell}
				</td></tr></table>

			</td>
	<!-- END SUB: content_cell_today_empty -->
</tr>
<!-- END SUB: content_row -->
</table>

		</td>
		</tr>
</table>
