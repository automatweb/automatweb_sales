<script type="text/javascript">
var chk_status = true;
function selall()
{
	len = document.changeform.elements.length;
	for (i = 0; i < len; i++)
	{
		document.changeform.elements[i].checked = chk_status;
	}
	chk_status = !chk_status;
}
</script>

<!-- SUB: avail -->
	<div style="margin-left: 50px;">
	<table border="1" style="border-collapse: collapse; font-size: 11px; border-color: #CCC;" cellpadding="3px">
		<!-- SUB: d_prp -->
		<tr>
			<td align="center">Nimi</td>
			<td width="100"><a href="javascript:selall()">Vali</a></td>
		</tr>
		<!-- END SUB: d_prp -->
		<!-- SUB: def_prop -->
		<tr>
			<td>{VAR:prp_name}</td>
			<td><input type="checkbox" id="mark[{VAR:prp_key}]" name="mark[{VAR:prp_key}]" value="1" style="border: 3px solid blue;"></td>
		</tr>
		<!-- END SUB: def_prop -->
	</table>
	<br />
	<table border="1" style="border-collapse: collapse; font-size: 11px; border-color: #CCC;" cellpadding="3px">
		<!-- SUB: av_props -->
		<tr>
			<td width="30" align="center">{VAR:type_cap}</td>
			<td width="100">{VAR:el_count_cap}</td>
			<td width="100">{VAR:usage_cap}</td>
		</tr>
		<!-- END SUB: av_props -->
		<!-- SUB: avail_property -->
		<tr>
			<td width="30" align="center">{VAR:prp_name}</td>
			<td width="100"><input type="text" id="mark[{VAR:prp_type}]" name="mark[{VAR:prp_type}]" style="width:50px"></td>
			<td width="100">{VAR:used_cap} {VAR:prp_used} / {VAR:unused_cap} {VAR:prp_unused}</td>
		</tr>
		<!-- END SUB: avail_property -->
	</table>
	</div>
<!-- END SUB: avail -->

