{VAR:pages}
<form method="post" action="index.aw">
	<table style="border: 1px solid blue;">
		<tr>
			<td>Mootori tootja</td>
			<td>{VAR:engine_manufacturer}{VAR:engine_manufacturer_error}</td>
		</tr>
		<tr>
			<td>Mudel</td>
			<td>{VAR:engine_model}{VAR:engine_model_error}</td>
		</tr>
		<tr>
			<td>Mootorite arv</td>
			<td>{VAR:engine_count}{VAR:engine_count_error}</td>
		</tr>
		<tr>
			<td>T&uuml;&uuml;p</td>
			<td>{VAR:engine_type}{VAR:engine_type_error}</td>
		</tr>
		<tr>
			<td>T&ouml;&ouml;maht (cm<sup>3</sup>)</td>
			<td>{VAR:engine_capacity}{VAR:engine_capacity_error}</td>
		</tr>
		<tr>
			<td>K&uuml;tusepaak</td>
			<td>{VAR:fuel_tank}{VAR:fuel_tank_error}</td>
		</tr>
		<tr>
			<td>K&uuml;tus</td>
			<td>{VAR:fuel}{VAR:fuel_error}</td>
		</tr>
		<tr>
			<td>V&otilde;imsus</td>
			<td>{VAR:engine_power}{VAR:engine_power_error}</td>
		</tr>
		<tr>
			<td>Jahutus</td>
			<td>{VAR:engine_cooling}{VAR:engine_cooling_error}</td>
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
