<style type="text/css">
.minical_table {
	border-collapse: collapse;
	border: 0px;
	font-family: Arial,sans-serif;
	font-size: 11px;
	padding: 3px;
	text-align: center;
	color: #000;
	background-color: #EFEFEF;

}
.minical_table a {
	color: #000;
	text-decoration: none;
}

.minical_table a:hover {
	color: #000;
}

.minical_header {
	font-family: Arial,sans-serif;
	font-size: 11px;
	background-color: #FFFFFF;
	text-align: center;
	border: 0px solid black;
}
.minical_cell {
	font-family: Arial,sans-serif;
	font-size: 11px;
	background-color: #FFFFFF;
	border: 0px solid #BCDCF0;
	padding: 3px;
	text-align: center;
}

.minical_cellact {
	font-family: Arial,sans-serif;
	font-size: 11px;
	background-color: #FFFFFF;
	border: 0px solid #BCDCF0;
	padding: 3px;
	background: #E1E1E1;
	text-align: center;
}

.minical_cell_deact {
	font-family: Arial,sans-serif;
	font-size: 11px;
	background-color: #FFFFFF;
	border: 0px solid #BCDCF0;
	padding: 3px;
	text-align: center;
	color: #BDBDBD;
}

.minical_cell_today {
	font-family: Arial,sans-serif;
	font-size: 11px;
	border: 0px solid #BCDCF0;
	padding: 3px;
	text-align: center;
	background: #5FC000;
	color: #000000;
}

a.calNavLink
{
	margin-top: 10px;
	font-size: 14px ! important;
	position: relative;
	top: 1px;
}

a.calNavLink:hover
{
	text-decoration: none;
}

span.calCurrentPeriodCaption
{
}

.style1 {
	font-family: Arial, Helvetica, sans-serif;
	font-weight: bold;
	font-size: 11px;
}
.style4 {font-family: Arial, Helvetica, sans-serif; font-size: 11px; }
.style4 a {font-family: Arial, Helvetica, sans-serif; font-size: 11px; color: #0018af;}
a:link {
	color: #0018af;
}
a:visited {
	color: #0018af;
}
a:hover {
	color: #0018af;
}
a:active {
	color: #0018af;
}
</style>

<script type="text/javascript">
function navigate_to()
{
	var m = document.getElementById('navi_month').value;
	var y = document.getElementById('navi_year').value;
	// now that I have got that .. uh .. what do I do now?
	// window.location changes url ..and contains the current url
	var naviurl = '{VAR:naviurl}' + '&date=' + m + '-' + y;
	window.location = naviurl;
};

function hilight_event(el,tgt)
{
	tgtel = document.getElementById(tgt);
	// tgtel on div, mille taustavärvi on vaja muuta
	if (el.checked)
	{
		tgtel.setAttribute('oldback',tgtel.style.backgroundColor);
		tgtel.style.backgroundColor = "#FFF3C6";
	}
	else
	{
		tgtel.style.backgroundColor = tgtel.getAttribute('oldback');
	};
	//alert(tgtel);


}
</script>





<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="aw04kalender01">

<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="aw04kalender02" style="">
			<div class="style4" style="float: left; margin-left: 15px; padding-top: 2px;">
				<a href="{VAR:prevlink}" class="calNavLink">&lt;&lt;</a> <span class="calCurrentPeriodCaption">{VAR:caption}</span>  <a href="{VAR:nextlink}" class="calNavLink">&gt;&gt;</a>
			</div>

			<div>
			<div class="aw04kalender01" align="right">
	<table border="0" cellpadding="0" cellspacing="0">
		<tr>
			<!-- SUB: TODAY -->
		  <td>&nbsp;</td>
		  <td class="aw04tab2smallcontent" background="{VAR:baseurl}/automatweb/images/aw04/tab2small_back.gif"><a href="{VAR:today_url}" class="style1">{VAR:text}</a></td>

		  <!-- END SUB: TODAY -->

			<!-- SUB: PAGE -->
		  <td><span class="style4">&nbsp;|&nbsp;</span></td>
		  <td class="aw04tab2smallcontent" background="{VAR:baseurl}/automatweb/images/aw04/tab2small_back.gif"><a href="{VAR:link}" class="style4"><b>{VAR:text}</b></a></td>
			<!-- END SUB: PAGE -->

			<!-- SUB: SEL_PAGE -->
		  <td><span class="style4">&nbsp;|&nbsp;</span></td>
		  <td><span class="style4"><b>{VAR:text}</b></span></td>
		  <td>&nbsp;</td>
			<!-- END SUB: SEL_PAGE -->
			</tr>
			</table>
</div>
			</div>
		</td>
	<td style="padding-bottom: 5px;" align="right" valign="middle" width="211">

	  <select id='navi_month' name='month' style="border: 1px solid gray">{VAR:mnames}</select>
	<select id='navi_year' name='year'>{VAR:years}</select>
	<input type="button" class="aw04formbutton" value="Go!" onClick='navigate_to()'>

	</td>
	</tr>
</table>

</td></tr></table>


<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td rowspan="1" valign="top" width="90%" style="padding-left: 10px; padding-right: 10px;">

	{VAR:content}

	</td>

	<td valign="top" width="10%">
	<div class="aw04kalenderkast01">

	{VAR:overview}

	</div>

	<!-- SUB: TASKS -->
	<div class="aw04kalenderkast01">


	<div class="minical_table">{VAR:tasks_title}</div>

	<table border="0" cellpadding="0" cellspacing="0">
	<tr>
	<td class="aw04kalendermini">

		<table border="0" cellpadding="0" cellspacing="1">

		<!-- SUB: TASK -->
		<tr><td class="aw04kalendertask"><a href="{VAR:task_url}">{VAR:task_name}</a></td></tr>
		<!-- END SUB: TASK -->

		</table>

	</td></tr></table>
	</div>

	<!-- END SUB: TASKS -->

	</td>
</tr>
</table>
