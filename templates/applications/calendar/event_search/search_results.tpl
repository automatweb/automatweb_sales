<div>
<!-- SUB: PREV_MONTH_URL -->
<a href="{VAR:prev_month_url}">Eelmine kuu</a>
<!-- END SUB: PREV_MONTH_URL -->
 {VAR:begin_month_name} {VAR:begin_year} 
<!-- SUB: NEXT_MONTH_URL -->
<a href="{VAR:next_month_url}">J&auml;rgmine kuu</a>
<!-- END SUB: NEXT_MONTH_URL -->
</div>
<div>
   <!-- SUB: next_weeks --> 
   	<a href="{VAR:week_url}">{VAR:week_nr}. n&auml;dal</a> - 
   <!-- END SUB: next_weeks -->
    <!-- SUB: next_weeks_b --> 
   	<a href="{VAR:week_url}"><b>{VAR:week_nr}. n&auml;dal</b></a> - 
   <!-- END SUB: next_weeks_b -->
   
   <!-- SUB: next_weeks_end --> 
   	<a href="{VAR:week_url}">{VAR:week_nr}. n&auml;dal</a>
   <!-- END SUB: next_weeks_end -->
      <!-- SUB: next_weeks_end_b --> 
   	<a href="{VAR:week_url}"><b>{VAR:week_nr}. n&auml;dal</b></a>
   <!-- END SUB: next_weeks_end_b -->
</div>
<table>
<tbody>
	<!-- SUB: COLHEADER -->
	<th>{VAR:colcaption}</th>
	<!-- END SUB: COLHEADER -->

	<!-- SUB: BLOCK -->
	<tr>
		<td class="cal_tulp" colspan="{VAR:col_count}" style="background-color:#0366a1;font-size:14px;color:#ffffff">{VAR:block_caption}</td>
	</tr>
	<!-- END SUB: BLOCK -->

	<!-- SUB: EVENT -->
	<tr class="rida{VAR:num}">
		<!-- SUB: CELL -->
		<td>{VAR:cell}</td>
		<!-- END SUB: CELL -->

		<!-- SUB: DELETE_EVENT_LINK -->
		<td><a href="{VAR:delete_url}">Kustuta</a></td>
		<!-- END SUB: DELETE_EVENT_LINK -->
	</tr>

	<!-- SUB: FULLTEXT -->
	<tr>
		<td colspan="{VAR:col_count}" class="cal_rida{VAR:num}"><!-- {VAR:fulltext_name}: -->{VAR:fulltext}</td>
	</tr>
	<!-- END SUB: FULLTEXT -->

	<!-- END SUB: EVENT -->
<tbody>
</table>
<div>
<!-- SUB: PAGE -->
<a href="{VAR:page_url}">{VAR:page_num}</a> 
<!-- END SUB: PAGE -->
<!-- SUB: ACTIVE_PAGE -->
<a href="{VAR:page_url}"><strong>{VAR:page_num}</strong></a>
<!-- END SUB: ACTIVE_PAGE -->
<!-- SUB: PAGE_SEPARATOR -->
 | 
<!-- END SUB: PAGE_SEPARATOR -->
</div>
