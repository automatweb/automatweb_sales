<div id="VclGanttChartBox{VAR:chart_id}">
<div id="tablebox" style="width: 100%;">
<!-- SUB: chart_caption -->
<div class="pais">
	<div class="caption">{VAR:caption}</div>
</div>
<!-- END SUB: chart_caption -->
<div class="sisu">
	<table cellspacing="0" cellpadding="0" id="VclGanttChartTable{VAR:chart_id}" width="100%">
	<!-- SUB: chart_navigation -->
	<tr>
		<td class="awmenuedittablehead">{VAR:navigation}</td>
	</tr>
	<!-- END SUB: chart_navigation -->
	<tr>
	<td class="awmenuedittableframeclass">
	<table border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td class="awmenuedittablehead" align="center" rowspan="{VAR:row_dfn_span}">{VAR:row_dfn}</td>

		<!-- SUB: column_head_link -->
		<td colspan="{VAR:subdivisions}" class="awmenuedittablehead VclGanttHeader" align="center" style="width: {VAR:column_width};"><a href="{VAR:uri}" target="{VAR:target}" style="white-space: nowrap;">{VAR:title}</a></td>
		<!-- END SUB: column_head_link -->
		<!-- SUB: column_head -->
		<td colspan="{VAR:subdivisions}" class="awmenuedittablehead VclGanttHeader" align="center" style="width: {VAR:column_width};">{VAR:title}</td>
		<!-- END SUB: column_head -->
		</tr>

		<!-- SUB: subdivision_row -->
		<tr>
		<!-- SUB: subdivision_head -->
					<!-- SUB: subdivision -->
					<td class="awmenuedittablehead VclGanttTimespan" align="left">{VAR:time}</td>
					<!-- END SUB: subdivision -->
		<!-- END SUB: subdivision_head -->
		</tr>
		<!-- END SUB: subdivision_row -->

	</tr>

	<!-- SUB: data_row -->
	<tr>
	<td class="awmenuedittabletext {VAR:row_name_class}"><a href="{VAR:row_uri}" class="VclGanttLink" target="{VAR:row_uri_target}">{VAR:row_name}</a></td>

	<!-- SUB: data_cell_column -->
	<td class="VclGanttColumn">
	<!-- SUB: cell_contents -->
	<!-- SUB: bar_normal_start -->
	<a href="{VAR:bar_uri}" target="{VAR:bar_uri_target}"><span>{VAR:title}</span><img src="{VAR:baseurl}/automatweb/images/ganttbar.gif" width="{VAR:length}" class="VclGanttStartBar" style="background-color: {VAR:bar_colour};"></a>
	<!-- END SUB: bar_normal_start -->
	<!-- SUB: bar_normal_continue -->
	<a href="{VAR:bar_uri}" target="{VAR:bar_uri_target}"><span>{VAR:title}</span><img src="{VAR:baseurl}/automatweb/images/ganttbar.gif" width="{VAR:length}" style="background-color: {VAR:bar_colour};"></a>
	<!-- END SUB: bar_normal_continue -->
	<!-- SUB: bar_empty -->
	<img src="{VAR:baseurl}/automatweb/images/ganttbar.gif" width="{VAR:length}" alt="">
	<!-- END SUB: bar_empty -->
	<!-- END SUB: cell_contents -->
	</td>
	<!-- END SUB: data_cell_column -->

	<!-- SUB: data_cell_subdivision -->
	<td class="VclGanttSubdivision">
	<!-- SUB: cell_contents -->
	<!-- SUB: bar_normal_start -->
	<a href="{VAR:bar_uri}" target="{VAR:bar_uri_target}"><span>{VAR:title}</span><img src="{VAR:baseurl}/automatweb/images/ganttbar.gif" width="{VAR:length}" class="VclGanttStartBar" style="background-color: {VAR:bar_colour};"></a>
	<!-- END SUB: bar_normal_start -->
	<!-- SUB: bar_normal_continue -->
	<a href="{VAR:bar_uri}" target="{VAR:bar_uri_target}"><span>{VAR:title}</span><img src="{VAR:baseurl}/automatweb/images/ganttbar.gif" width="{VAR:length}" style="background-color: {VAR:bar_colour};"></a>
	<!-- END SUB: bar_normal_continue -->
	<!-- SUB: bar_empty -->
	<img src="{VAR:baseurl}/automatweb/images/ganttbar.gif" width="{VAR:length}" alt="">
	<!-- END SUB: bar_empty -->
	<!-- END SUB: cell_contents -->
	</td>
	<!-- END SUB: data_cell_subdivision -->

	</tr>
	<!-- END SUB: data_row -->



	<!-- SUB: separator_row -->
	<tr>
	<td class="awmenuedittabletext VclGanttRowName" style="vertical-align: middle; font-weight: bold;" colspan="{VAR:colspan}"><a href="{VAR:expand_collapse_link}"><img src="{VAR:baseurl}/automatweb/images/{VAR:row_state}node_small.gif" style="border: none;" alt="{VAR:expand_collapse_title}"></a>&nbsp;{VAR:row_title}</td>
	</tr>
	<!-- END SUB: separator_row -->

	</table>
	</td>
	</tr>
	</table>
<!-- SUB: chart_footer -->
<div>
	<div>{VAR:footer}</div>
</div>
<!-- END SUB: chart_footer -->
</div>
</div>
</div>

<script type="text/javascript">
tblWidth = document.getElementById("VclGanttChartTable{VAR:chart_id}").clientWidth;
if (tblWidth > {VAR:chart_width})
{
	document.getElementById("VclGanttChartBox{VAR:chart_id}").style.width = (tblWidth+2) + "px";
}
</script>
