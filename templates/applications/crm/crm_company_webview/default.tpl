{VAR:res_count_row}


<!-- SUB: company_list -->
<ul>
<!-- SUB: company_list_item -->
<li> <b>{VAR:company_name}</b> {VAR:company_changeurl}<br>
<!-- SUB: company_item_address -->
{VAR:company_address}<br>
<!-- END SUB: company_item_address -->
<!-- SUB: company_item_phone -->
{VAR:txt_phone}: {VAR:company_phone}<br>
<!-- END SUB: company_item_phone -->
<!-- SUB: company_item_fax -->
{VAR:txt_fax}: {VAR:company_fax}<br>
<!-- END SUB: company_item_fax -->
<!-- SUB: company_item_openhours -->
{VAR:txt_openhours}: {VAR:company_openhours}<br>
<!-- END SUB: company_item_openhours -->
<!-- SUB: company_item_email -->
{VAR:txt_email}: {VAR:company_email}<br>
<!-- END SUB: company_item_email -->
<!-- SUB: company_item_web -->
{VAR:company_web}<br>
<!-- END SUB: company_item_web -->
<br>
<!-- END SUB: company_list_item -->
</ul>
<!-- END SUB: company_list -->

<!-- SUB: company_show -->
<script type="text/javascript" src="{VAR:baseurl}automatweb/js/aw.js"></script>
<!-- SUB: line_name -->
<h1><a href="">{VAR:value}</a></h1>
<!-- END SUB: line_name -->
<table>
	<tr>
		<td>
			<table>
			<!-- SUB: line_type -->
				<tr> <td valign=top>{VAR:key}:</td> <td>{VAR:value}</td></tr>
			<!-- END SUB: line_type -->
			<!-- SUB: line_address -->
				<tr> <td valign=top>{VAR:key}:</td> <td>{VAR:value}</td></tr>
			<!-- END SUB: line_address -->
			<!-- SUB: line_openhours -->
				<tr> <td valign=top>{VAR:key}:</td> <td>{VAR:value}</td></tr>
			<!-- END SUB: line_openhours -->
			<!-- SUB: line_phone -->
				<tr> <td valign=top>{VAR:key}:</td> <td>{VAR:value}</td></tr>
			<!-- END SUB: line_phone -->
			<!-- SUB: line_fax -->
				<tr> <td valign=top>{VAR:key}:</td> <td>{VAR:value}</td></tr>
			<!-- END SUB: line_fax -->
			<!-- SUB: line_email -->
				<tr> <td valign=top>{VAR:key}:</td> <td>{VAR:value}</td></tr>
			<!-- END SUB: line_email -->
			<!-- SUB: line_url -->
				<tr> <td colspan=2> {VAR:value} </td> </tr>
			<!-- END SUB: line_url -->
			<!-- SUB: line_founded -->
				<tr> <td valign=top>{VAR:key}:</td> <td>{VAR:value}</td></tr>
			<!-- END SUB: line_founded -->
			<!-- SUB: line_sectors -->
				<tr> <td valign=top>{VAR:key}:</td> <td>{VAR:value}</td><br><br></tr>
			<!-- END SUB: line_sectors -->
			<!-- SUB: line_specialoffers -->
				<tr> <td colspan=2> {VAR:value} </td> </tr>
			<!-- END SUB: line_specialoffers -->
			<!-- SUB: Xline_num_rooms -->
				<tr> <td valign=top>{VAR:key}:</td> <td>{VAR:value}</td><br><br></tr>
			<!-- END SUB: Xline_num_rooms -->
			<!-- SUB: Xline_num_beds -->
				<tr> <td valign=top>{VAR:key}:</td> <td>{VAR:value}</td><br><br></tr>
			<!-- END SUB: Xline_num_beds -->
			<!-- SUB: Xline_prices -->
				<tr> <td valign=top>{VAR:key}:</td> <td>{VAR:value}</td><br><br></tr>
			<!-- END SUB: Xline_prices -->
				{VAR:custom_values}
			</table>
                        <!-- SUB: line_extrafeatures -->
			{VAR:value}
                        <!-- END SUB: line_extrafeatures -->
			<!-- SUB: extrafeatures -->
			<b>{VAR:extraf_title}</b>
			<ul>
				<!-- SUB: extraf_value -->
					<li>{VAR:extraf_name}</li>
				<!-- END SUB: extraf_value -->
			</ul>
				<!-- SUB: conference_room_table -->
					<table border="1">
						<!-- SUB: croom_table_header -->
							<tr>
								<!-- SUB: croom_header_name_col -->
									<td>{VAR:room_name_header}</td>
								<!-- END SUB: room_header_name_col -->
								<!-- SUB: croom_header_area_col -->
									<td>{VAR:area_value_header}</td>
								<!-- END SUB: croom_header_area_col -->
								<!-- SUB: croom_header_table_col -->
									<td><img width="50" src="img/{VAR:col_value_header}_default.jpg" title="{VAR:col_value_header_title}" alt="{VAR:col_value_header_alt}"></td>
								<!-- END SUB: croom_header_table_col -->
							</tr>
						<!-- END SUB: croom_table_header -->
						<!-- SUB: croom_table_row -->
						<!-- END SUB: croom_table_row -->
						<!-- SUB: croom_table_row_odd -->
							<tr bgcolor="silver">
								<!-- SUB: croom_name_col_odd -->
									<td><a href="#" onClick="aw_popup_scroll('{VAR:room_popurl}','Ruum', 500, 500);">{VAR:room_name_odd}</a>{VAR:room_comment_odd}</td>
								<!-- END SUB: room_name_col_odd -->
								<!-- SUB: croom_area_col_odd -->
									<td>{VAR:area_value_odd}</td>
								<!-- END SUB: room_area_col_odd -->
								<!-- SUB: croom_table_col_odd -->
									<td>{VAR:col_value_odd}</td>
								<!-- END SUB: croom_table_col_odd -->
							</tr>
						<!-- END SUB: croom_table_row_odd -->
						<!-- SUB: croom_table_row_even -->
							<tr bgcolor="lightblue">
								<!-- SUB: croom_name_col_even -->
									<td><a href="#" onClick="aw_popup_scroll('{VAR:room_popurl}','Ruum', 500, 500);">{VAR:room_name_even}</a>{VAR:room_comment_even}</td>
								<!-- END SUB: room_name_col_even -->
								<!-- SUB: croom_area_col_even -->
									<td>{VAR:area_value_even}</td>
								<!-- END SUB: room_area_col_even -->
								<!-- SUB: croom_table_col_even -->
									<td>{VAR:col_value_even}</td>
								<!-- END SUB: croom_table_col_even -->
							</tr>
						<!-- END SUB: croom_table_row_even -->
					</table>
				<!-- END SUB: conference_room_table -->

			<!-- END SUB: extrafeatures -->
		</td>
		<td width=20>&nbsp;</td>
		<td valign=top width=200>
			{VAR:images}
			<!-- SUB: line_comment -->
			<p>{VAR:value}</p>
			<!-- END SUB: line_comment -->
			<!-- SUB: line_description -->
			<p>{VAR:value}</p>
			<!-- END SUB: line_description -->
			<!-- SUB: line_moreinfo_link -->
			{VAR:value}
			<!-- END SUB: line_moreinfo_link -->
		</td>
	</tr>
	<tr>
		<td colspan="3">
			<form method="post" action="/orb.aw">
			<!-- SUB: rating -->
				<b>{VAR:rating_caption}:</b><br>
			<!-- SUB: rating_value -->
			<input type='radio' name='{VAR:rating_value_name}' value='{VAR:rating_value_value}'>{VAR:rating_value_caption}</input><br>
			<!-- END SUB: rating_value -->
			<!-- END SUB: rating -->
			{VAR:rating_form_vars}
			</form>
		</td>
	</tr>
</table>
<!-- END SUB: company_show -->
