<table>
	<tr>
		<td>
			{VAR:LC_ADD_NO}:
		</td>
		<td>
			<input name="sub[2][no_dates_to_add]" type="text" /><a href="#" onClick="javascript:submit_changeform('add_dates');">{VAR:LC_ADD}</a>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<table border="1">
				<tr>
					<td>
						{VAR:LC_DATE_TYPE}
					</td>
					<td>
						{VAR:LC_ARR_DATE}
					</td>
					<td>
						{VAR:LC_DEP_DATE}
					</td>
					<td>
					</td>
				</tr>
				<!-- SUB: ROW -->
				<tr>					
					<td>
						<select name="sub[2][table_rows][{VAR:date_no}][date_type]">
							<option value="0" {VAR:date_type_normal}>Normal</option>
							<option value="1" {VAR:date_type_alternative}>Alternative</option>
						</select>
					</td>
					<td>
						<input type="text" name="sub[2][table_rows][{VAR:date_no}][arrival_date]" value="{VAR:arrival_date}"/>
					</td>
					<td>
						<input type="text" name="sub[2][table_rows][{VAR:date_no}][departure_date]" value="{VAR:departure_date}"/>
					</td>
					<td>
						<a href="{VAR:remove_url}">{VAR:LC_REMOVE}</a>
					</td>
				</tr>
				<!-- END SUB: ROW -->
			</table>
		</td>
	</tr>

	<tr>
		<td>
			{VAR:LC_FLEXIBLE_DATES}:
		</td>
		<td>
			<input name="sub[2][dates_are_flexible]" {VAR:dates_are_flexible} type="checkbox"/>
		</td>
	</tr>
	<tr>
		<td valign="top">
			{VAR:LC_PATTERN}:
		</td>
		<td>
			<input type="radio" name="sub[2][meeting_pattern]" {VAR:pattern_1} value="1"/>&nbsp;{VAR:LC_NOT_APP}<br/>
			<input type="radio" name="sub[2][meeting_pattern]" {VAR:pattern_2} value="2"/>
			<select name="sub[2][pattern_wday_from]">
				<!-- SUB: DAY_FROM -->
				<option value="{VAR:value}" {VAR:pattern_wday_from}>{VAR:caption}</option>
				<!-- END SUB: DAY_FROM -->
			</select>
				{VAR:LC_TO}
			<select name="sub[2][pattern_wday_to]">
				<!-- SUB: DAY_TO -->
				<option value="{VAR:value}" {VAR:pattern_wday_to}>{VAR:caption}</option>
				<!-- END SUB: DAY_TO -->
			</select>
			</br>
			<input type="radio" name="sub[2][meeting_pattern]" {VAR:pattern_3} value="3"/> {VAR:LC_ANY}
			<select name="sub[2][pattern_day]">
				<!-- SUB: DAY -->
				<option value="{VAR:value}" {VAR:pattern_day}>{VAR:caption}</option>
				<!-- END SUB: DAY -->
			</select>
			{VAR:LC_TO}
		</td>
	</tr>
	<tr>
		<td valign="top">
			{VAR:LC_DATE_COMMENTS}:
		</td>
		<td>
			<textarea name="sub[2][date_comments]">{VAR:date_comments}</textarea>
		</td>
	</tr>
</table>
