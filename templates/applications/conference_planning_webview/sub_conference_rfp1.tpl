<table>
	<tr>
		<td>
			{VAR:LC_FUNCTION_NAME} *:
		</td>
		<td>
			<input name="sub[1][function_name]" value="{VAR:function_name}" type="text"/>
		</td>
	</tr>
	<tr>
		<td>
			{VAR:LC_MULTI_DAY_CAPTION}:
		</td>
		<td>
			<input type="radio" name="sub[1][multi_day]" {VAR:multi_day_1} value="1"/>{VAR:LC_ONE_DAY}<br/>
			<input type="radio" name="sub[1][multi_day]" {VAR:multi_day_2} value="2"/>{VAR:LC_MULTI_DAY}
		</td>
	</tr>
	<tr>
		<td>
			{VAR:LC_ORGANISATION_COMPANY}:
		</td>
		<td>
			<input name="sub[1][organisation_company]" value="{VAR:organisation_company}" type="text"/>
		</td>
	</tr>

	<tr>
		<td>
			{VAR:LC_RESPONSE_DATE}:
		</td>
		<td>
			<input name="sub[1][response_date]" value="{VAR:response_date}" type="text"/>
		</td>
	</tr>
	<tr>
		<td>
			{VAR:LC_DECISION_DATE}:
		</td>
		<td>
			<input name="sub[1][decision_date]" value="{VAR:decision_date}" type="text"/>
		</td>
	</tr>
	<tr>
		<td>
			{VAR:LC_ARR_DATE}:
		</td>
		<td>
			<input name="sub[1][arrival_date]" value="{VAR:arrival_date}" type="text"/>
		</td>
	</tr>
	<tr>
		<td>
			{VAR:LC_DEP_DATE}:
		</td>
		<td>
			<input name="sub[1][departure_date]" value="{VAR:departure_date}" type="text"/>
		</td>
	</tr>
	<tr>
		<td>
			{VAR:LC_ALTER_DATES}:
		</td>
		<td>
			<input name="sub[1][open_for_alternative_dates]" value="1" {VAR:open_for_alternative_dates} type="checkbox"/>
		</td>
	</tr>
	<tr>
		<td>
			{VAR:LC_HAVE_ACC_REQ}:
		</td>
		<td>
			<input name="sub[1][needs_rooms]" value="1" {VAR:needs_rooms} type="checkbox"/>
		</td>
	</tr>
</table>
