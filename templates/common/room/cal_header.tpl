{VAR:pop}
<table border=0>
	<tr>
		<td>{VAR:week_select_caption}</td>
		<td>{VAR:week_select}
<a href='#' onClick='window.location.reload()'><img src='{VAR:baseurl}/automatweb/images/icons/refresh.gif' border='0'></a></td>
		<td>
			{VAR:length_sel}
		</td>
	</tr>
	<tr>
		<td>{VAR:date_from_caption}</td>
		<td>{VAR:date_from}</td>
		<td>{VAR:ts_buttons}</td>
	</tr>
	<tr>
		<td>{VAR:date_to_caption}</td>
		<td>{VAR:date_to}</td>
		<td>{VAR:to_button}</td>
	</tr>
	<tr>
		<td>{VAR:without_detail_information}</td>
		<td colspan="2"><input type="checkbox" value="1" name="no_det_info" {VAR:no_det_info}></td>
		
	<tr>
		<td></td>
		<td></td>
<!-- SUB: ANCHOR_LINKS -->
<a href="{VAR:alink}"><input type='button' value='{VAR:acapt}'  /></a> 
<!-- END SUB: ANCHOR_LINKS -->
</td>
	</tr>			
		
</table>
