<!-- SUB: calstyles -->
<style type="text/css">
.caltable {
	border-collapse: collapse;
	border: 1px solid #BCDCF0;
	font-family: Arial,sans-serif;
	font-size: 11px;
	padding: 3px;
	color: #000;
}

.caltable a {
	color: #000;
	text-decoration: none;
}

.caltable a:hover {
	color: #000;
}

.calheader {
	background-color: #BCDCF0;
	text-align: center;
	border: 1px solid black;
}

.calcell {
	border: 1px solid #BCDCF0;
	padding: 3px;
	text-align: center;
}

.calcellact {
	border: 1px solid #BCDCF0;
	padding: 3px;
	background: #EEEEEE;
	text-align: center;
}

.calcellcurr {
	border: 1px solid #BCDCF0;
	padding: 3px;
	background: #BCDCF0;
	text-align: center;
}

.calcelltoday {
	border: 1px solid #BCDCF0;
	padding: 3px;
	background: #E0A2A2;
	text-align: center;
}

.calcelldeact {
	color: #CCC;
	border: 1px solid #BCDCF0;
	padding: 3px;
	text-align: center;
}
</style>
<!-- END SUB: calstyles -->
<table class="caltable" cellpadding="0" cellspacing="0">
<tr>
<td colspan="7" class="calheader">
<a href="{VAR:caption_url}">{VAR:caption}</a>
</td>
</tr>

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
	<!-- SUB: today_cell -->
	<td class="calcelltoday">
	<a href="{VAR:url}" {VAR:attribs}>{VAR:content}</a>
	</td>
	<!-- END SUB: today_cell -->

	<!-- SUB: cell -->
	<td class="calcell">
	<a href="{VAR:url}" {VAR:attribs}>{VAR:content}</a>
	</td>
	<!-- END SUB: cell -->

	<!-- SUB: empty_cell -->
	<td align="center">
	&nbsp;
	</td>
	<!-- END SUB: empty_cell -->

	<!-- SUB: active_cell -->
	<td class="calcellact">
	<a href="{VAR:url}" {VAR:attribs}>{VAR:content}</a>
	</td>
	<!-- END SUB: active_cell -->

	<!-- SUB: current_cell -->
	<td class="calcellcurr">
	<a href="{VAR:url}" {VAR:attribs}>{VAR:content}</a>
	</td>
	<!-- END SUB: current_cell -->

	<!-- SUB: deactive_cell -->
	<td class="calcelldeact">
	{VAR:content}
	</td>
	<!-- END SUB: deactive_cell -->
</tr>
<!-- END SUB: line -->
</table>
