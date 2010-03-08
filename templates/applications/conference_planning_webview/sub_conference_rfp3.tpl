<table>
	<tr>
		<td>
			{VAR:LC_NEEDS_ROOMS}:
		</td>
		<td>
			<input name="sub[3][needs_rooms]" {VAR:needs_rooms} type="checkbox"/>
		</td>
	</tr>
	<tr>
		<td>
			{VAR:LC_SINGLE_ROOMS}:
		</td>
		<td>
			<select name="sub[3][single_count]">
				<!-- SUB: SINGLE_OPTION -->
				<option value="{VAR:value}" {VAR:single}>{VAR:caption}</option>
				<!-- END SUB: SINGLE_OPTION -->
			</select>
		</td>
	</tr>
	<tr>
		<td>
			{VAR:LC_DOUBLE_ROOMS}:
		</td>
		<td>
			<select name="sub[3][double_count]">
				<!-- SUB: DOUBLE_OPTION -->
				<option value="{VAR:value}" {VAR:double}>{VAR:caption}</option>
				<!-- END SUB: DOUBLE_OPTION -->
			</select>
		</td>
	</tr>
	<tr>
		<td>
			{VAR:LC_SUITES}:
		</td>
		<td>
			<select name="sub[3][suite_count]">
				<!-- SUB: SUITE_OPTION -->
				<option value="{VAR:value}" {VAR:suite}>{VAR:caption}</option>
				<!-- END SUB: SUITE_OPTION -->
			</select>
		</td>
	</tr>
	<tr>
		<td>
			{VAR:LC_MAIN_ARR_DATE}{VAR:required}
		</td>
		<td>
			<input name="sub[3][main_arrival_date]" value="{VAR:main_arrival_date}" type="text"/>
		</td>
	</tr>
	<tr>
		<td>
			{VAR:LC_MAIN_DEP_DATE}{VAR:required}
		</td>
		<td>
			<input name="sub[3][main_departure_date]" value="{VAR:main_departure_date}" type="text"/>
		</td>
	</tr>
</table>
