{VAR:pages}
<form method="post" action="index.aw">
	<table style="border: 1px solid blue;">
		<tr>
			<td>Nimi</td>
			<td>{VAR:name}{VAR:name_error}</td>
		</tr>
		<tr>
			<td>Tehingu t&uuml;&uuml;p</td>
			<td>{VAR:deal_type}{VAR:deal_type_error}</td>
		</tr>
		<tr>
			<td>Tootja</td>
			<td>{VAR:manufacturer}{VAR:manufacturer_error}</td>
		</tr>
		<tr>
			<td>Mark</td>
			<td>{VAR:brand}{VAR:brand_error}</td>
		</tr>
		<tr>
			<td>Kerematerjal</td>
			<td>{VAR:body_material}{VAR:body_material_error}</td>
		</tr>
		<tr>
			<td>Asukoht</td>
			<td>{VAR:location}{VAR:location_error}</td>
		</tr>
		<tr>
			<td>Muu asukoht</td>
			<td>{VAR:location_other}{VAR:location_other_error}</td>
		</tr>
		<tr>
			<td>Seisukord</td>
			<td>{VAR:condition}{VAR:condition_error}</td>
		</tr>
		<tr>
			<td>Lisainfo seisukorra kohta</td>
			<td>{VAR:condition_info}{VAR:condition_info_error}</td>
		</tr>
		<tr>
			<td>Hind</td>
			<td>{VAR:price}{VAR:price_error}</td>
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
