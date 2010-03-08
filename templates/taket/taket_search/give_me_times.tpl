<style type="text/css">
#give_me_times_table{
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 12px;
	color: #000000;
	border-collapse: collapse;
	margin: 5px;
	border: 1px solid #B9BED2;
	width: 100%;
}

#give_me_times_table th{
	background-color: #B9BED2;
	padding: 3px;
}

#give_me_times_table td.default_row{
	text-align: left;
	background-color: white;
	text-align: center;
}
#give_me_times_table td.new_row{
	text-align: left;
	background-color: #B9BED2;
	text-align: center;
	padding: 15px 0 15px 0;
}
</style>
<form method="post" action="index.aw">
<table id="give_me_times_table">
	<tr>
		<th>Hankija kood</th>
		<th>Tarnepäev</th>
		<!--<th>Saabumispäev</th>-->
		<th>Tarneaeg p&auml;evades</th>
		<th>Kuup&auml;ev 1</th>
		<th>Kuup&auml;ev 2</th>
		<th>Kustuta</th>
	</tr>
	{VAR:suppliers}
<!-- SUB: SUPPLIER -->
	<tr>
		<td class="{VAR:style}">{VAR:supplier_id}</td>
		<td class="{VAR:style}">{VAR:day1}</td>
		<!--<td class="{VAR:style}">{VAR:day2}</td>-->
		<td class="{VAR:style}">{VAR:days}</td>
		<td class="{VAR:style}">{VAR:date1}</td>
		<td class="{VAR:style}">{VAR:date2}</td>
		<td class="{VAR:style}">{VAR:delete}</td>
	</tr>
<!-- END SUB: SUPPLIER -->
</table>
{VAR:reforb}
<input type="submit" value="Salvesta">
</form>
