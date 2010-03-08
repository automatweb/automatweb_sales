{VAR:pages}
<form method="post" action="index.aw">
	<table style="border: 1px solid blue;">
		<tr>
			<td>Elekter 100V</td>
			<td>{VAR:electricity_110V_sel} {VAR:electricity_110V_sel_error}</td>
		</tr>
		<tr>
			<td>Elekter 100V info</td>
			<td>{VAR:electricity_110V_info} {VAR:electricity_110V_info_error}</td>
		</tr>
		<tr>
			<td>Lisainfo</td>
			<td>{VAR:additional_equipment_info} {VAR:additional_equipment_info_error}</td>
		</tr>
	</table>
	<input type="submit" name="prev" value="Tagasi" />
	<input type="submit" name="save" value="Salvesta" />
	<input type="submit" name="cancel" value="Katkesta" />
	<input type="submit" name="next" value="Edasi" />
	{VAR:reforb}
</form>
<!-- SUB: ERROR -->
See v&auml;li on kohustuslik
<!-- END SUB: ERROR -->
