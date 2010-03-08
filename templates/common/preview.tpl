<form action="#">

	<table class="form">
		<tr>
			<th>{VAR:LC_BRONEERITUD}:</th>
			<td class="data">{VAR:time_str}</td>
		</tr>
		<tr>
			<th>{VAR:LC_KYLASTUSAEG}:</th>

			<td class="data">{VAR:hours} h</td>
		</tr>
		<tr>
			<th>{VAR:LC_KYLASTAJAID}:</th>
			<td class="data">{VAR:people_value}</td>
		</tr>
				<tr>
			<th>{VAR:LC_SAUNA_MENU}Sauna menu:</th>
			<td class="data"></td>
		</tr>
		<!-- SUB: PROD -->
		<tr>
			<th></th>
			<td class="data">{VAR:prod_name} {VAR:prod_amount} X {VAR:prod_value} EEK</td>
		</tr>			
		<!-- END SUB: PROD -->
		
		
		<tr class="subheading">

			<th colspan="2">{VAR:LC_HIND}</th>
		</tr>
		<tr>
			<th>{VAR:LC_SAUN}:</th>
			<td class="data">{VAR:sum_wb}.-</td>
		</tr>
		<tr>
			<th>{VAR:LC_SOODUSTUS}:</th>
			<td class="data">{VAR:bargain}.-</td>
		</tr>
		
				


		<tr>
			<th>{VAR:LC_}Menu:</th>
			<td class="data">{VAR:menu_sum}.-</td>
		</tr>
		<tr>
			<th>{VAR:LC_TASUDA}:</th>
			<td class="data">{VAR:sum}.-</td>
		</tr>

		<tr class="subheading">
			<th colspan="2">{VAR:LC_ERISOOVID}</th>
		</tr>
		<tr>
			<th></th>
			<td class="data">{VAR:comment_value}</td>
		</tr>
		<tr class="subheading">

			<th colspan="2">{VAR:LC_TELLIJA_ANDMED}:</th>
		</tr>
		<tr>
			<th>{VAR:LC_NIMI}:</th>
			<td class="data">{VAR:name_value}</td>
		</tr>
		<tr>
			<th>{VAR:LC_TELEFON}:</th>
			<td class="data">{VAR:phone_value}</td>
		</tr>
		<tr>
			<th>{VAR:LC_EMAIL}:</th>
			<td class="data">{VAR:email_value}</td>
		</tr>
		<tr>
			<th>{VAR:LC_}</th>
			<td class="data">{VAR:status}</td>
		</tr>
	
	</table>
</form>
