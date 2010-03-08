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
</style>

<div><small><form method="GET" name="mininavigator">
<!-- SUB: MINIMONYEAR_2IN2 -->
<select id="minimon" name="minimon">{VAR:mnames}</select>
<select id="miniyear" name="miniyear">{VAR:years}</select>
<!-- END SUB: MINIMONYEAR_2IN2 -->
<!-- SUB: MINIMONYEAR_2IN1 -->
<select id="minimonyear" name="minimonyear">{VAR:monyears}</select>
<!-- END SUB: MINIMONYEAR_2IN1 -->
<input type="hidden" id="mininaviurl" name="mininaviurl" value="{VAR:mininaviurl}">
<input type="button" value="Ava" onClick="mini_navigate()">
<!-- SUB: MINIMONYEAR_2IN2_JS -->
<script type="text/javascript">
function mini_navigate()
{
	var m = document.getElementById('minimon').value;
	var y = document.getElementById('miniyear').value;
	var newurl = document.getElementById('mininaviurl').value + "&date=" + m + "-" + y;
	window.location.href = newurl;
}
</script>
<!-- END SUB: MINIMONYEAR_2IN2_JS -->
<!-- SUB: MINIMONYEAR_2IN1_JS -->
<script type="text/javascript">
function mini_navigate()
{
	var my = document.getElementById('minimonyear').value;
	var newurl = document.getElementById('mininaviurl').value + "&date=" + my;
	window.location.href = newurl;
}
</script>
<!-- END SUB: MINIMONYEAR_2IN1_JS -->
</form></small></div>
<div class="minical_table">
<a href="{VAR:today_url}">T&auml;na: {VAR:today_date}</a> <br>
			<a href="{VAR:prevlink}"><img SRC="{VAR:baseurl}/automatweb/images/blue/cal_nool_left.gif" WIDTH="19" HEIGHT="8" BORDER=0 ALT="&lt;&lt;"></a> {DATE:act_day_tm|d.m.Y}  <a href="{VAR:nextlink}"><img SRC="{VAR:baseurl}/automatweb/images/blue/cal_nool_right.gif" WIDTH="19" HEIGHT="8" BORDER=0 ALT="&gt;&gt;"></a>
</div>
<table width="100%" cellpadding="0" cellspacing="0">
<tr><td bgcolor="#EFEFEF">
	{VAR:overview}
</td></tr></table>

	{VAR:content}
