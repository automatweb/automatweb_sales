<table>
	<!-- SUB: DAYS -->
	<tr>
		<td colspan="2">
			<table border="1">
				<tr>
					<td>
						{VAR:LC_DATE}
					</td>
					<td>
						{VAR:LC_EVENT_TYPE}
					</td>
					<td>
						{VAR:LC_TIME}
					</td>
					<td>
					</td>
				</tr>
				<!-- SUB: DAY -->
				<tr>
					<td>{VAR:function_start_date}</td>
					<td>{VAR:event_type}</td>
					<td>{VAR:function_start_time} - {VAR:function_end_time}</td>
					<td>
						<a href="{VAR:edit_url}">{VAR:LC_EDIT}</a>&nbsp;&nbsp;<a href="{VAR:remove_url}">{VAR:LC_REMOVE}</a>
					</td>
				</tr>
				<!-- END SUB: DAY -->
				<!-- SUB: DAY_ACTIVE -->
				<tr bgcolor="silver">
					<td>{VAR:function_start_date}</td>
					<td>{VAR:event_type}</td>
					<td>{VAR:function_start_time} - {VAR:function_end_time}</td>
					<td>
						<a href="{VAR:edit_url}">{VAR:LC_EDIT}</a>&nbsp;&nbsp;<a href="{VAR:remove_url}">{VAR:LC_REMOVE}</a>
					</td>
				</tr>
				<!-- END SUB: DAY_ACTIVE -->
			</table>
		</td>
	</tr>
	<!-- END SUB: DAYS -->
	<tr>
		<td>
			{VAR:LC_EVENT_TYPE}:
		</td>
		<td>
			<input type="radio" name="sub[4][event_type_chooser]" value="1" {VAR:event_type_chooser_1}/>
			<select name="sub[4][event_type_select]">
				<!-- SUB: EVT_TYPE -->
				<option value="{VAR:value}" {VAR:event_type_select}>{VAR:caption}</option>
				<!-- END SUB: EVT_TYPE -->
			</select>
			<br/>
			<input type="radio" name="sub[4][event_type_chooser]" value="2" {VAR:event_type_chooser_2}/>
			{VAR:LC_OTHER}:
			<input type="text" name="sub[4][event_type_text]" value="{VAR:event_type_text}"/>
		</td>
	</tr>
	<tr>
		<td colspan="2"><b>{VAR:LC_MAIN_FUNCTION_ROOM}<b/></td>
	</tr>

	<tr>
		<td>
			{VAR:LC_DELEGATE_NO}:
		</td>
		<td>
			<input type="text" name="sub[4][delegates_no]" value="{VAR:delegates_no}"/>
		</td>
	</tr>
	<tr>
		<td>
			{VAR:LC_TABLE_FORM}:
		</td>
		<td>
			<select name="sub[4][table_form]">
				<!-- SUB: TABLE_FORM -->
				<option value="{VAR:value}" {VAR:table_form}>{VAR:caption}</option>
				<!-- END SUB: TABLE_FORM -->
			</select>
		</td>
	</tr>
	<tr>
		<td>
			{VAR:LC_TECH_EQUIP}:
		</td>
		<td>
			<!-- SUB: TECH_EQUIP -->
			<input type="checkbox" name="sub[4][tech][{VAR:value}]" {VAR:tech}/>{VAR:caption}<br/>
			<!-- END SUB: TECH_EQUIP -->
		</td>
	</tr>
	<tr>
		<td>
			{VAR:LC_DOOR_SIGN}:
		</td>
		<td>
			<input type="text" name="sub[4][door_sign]" value="{VAR:door_sign}"/>
		</td>
	</tr>
	<tr>
		<td>
			{VAR:LC_PERSON_NO}:
		</td>
		<td>
			<input type="text" name="sub[4][persons_no]" value="{VAR:persons_no}"/>
		</td>
	</tr>
	<tr>
		<td>
			{VAR:LC_START_DATETIME}:
		</td>
		<td>
			<input type="text" size="10" name="sub[4][function_start_date]" value="{VAR:function_start_date}"/><input size="2" type="text" name="sub[4][function_start_time]" value="{VAR:function_start_time}"/> - <input size="2" type="text" name="sub[4][function_end_time]" value="{VAR:function_end_time}"/> {VAR:LC_TIME_FORMAT}
		</td>
	</tr>
	<tr>
		<td>
			{VAR:LC_24H}:
		</td>
		<td>
			<input type="checkbox" name="sub[4][24h]" {VAR:24h}/>
		</td>
	</tr>
	<tr>
		<td colspan="2"><b>{VAR:LC_MAIN_CATERING}<b/></td>
	</tr>
	
	<tr>
		<td colspan="2">
		<table>
		<tr>
			<td>
			{VAR:LC_TYPE}
			</td>
			<td>
			{VAR:LC_START_TIME}
			</td>
			<td>
			{VAR:LC_END_TIME}
			</td>
			<td>
			{VAR:LC_NO_ATTENDEES}
			</td>
			<td>
			</td>
		</tr>
		<!-- SUB: MAIN_CATERING_ROW -->
		<tr>
			<td>
				{VAR:catering_row_type}
			</td>
			<td>
				{VAR:catering_row_start_time}
			</td>
			<td>
				{VAR:catering_row_end_time}
			</td>
			<td>
				{VAR:catering_row_attendees_no}
			</td>
			<td>
				<a href="{VAR:remove_url}">{VAR:LC_REMOVE}</a>&nbsp;&nbsp;&nbsp;<a href="{VAR:edit_url}">{VAR:LC_EDIT}</a>
			</td>
		</tr>
		<!-- END SUB: MAIN_CATERING_ROW -->
		<!-- SUB: MAIN_CATERING_ROW_ACTIVE -->
		<tr bgcolor="silver">
			<td>
				{VAR:catering_row_type}
			</td>
			<td>
				{VAR:catering_row_start_time}
			</td>
			<td>
				{VAR:catering_row_end_time}
			</td>
			<td>
				{VAR:catering_row_attendees_no}
			</td>
			<td>
				<a href="{VAR:remove_url}">{VAR:LC_REMOVE}</a>&nbsp;&nbsp;&nbsp;<a href="{VAR:edit_url}">{VAR:LC_EDIT}</a>
			</td>
		</tr>
		<!-- END SUB: MAIN_CATERING_ROW_ACTIVE -->
		</table>
		</td>
	</tr>

	<!-- start catering form -->
	<input type="hidden" name="main_catering_id" value="{VAR:catering_id}"/> 
	<tr width="100%" height="1" bgcolor="silver"><td width="100%" colspan="2" bgbolor="silver" height="1"></td></tr>
	<tr>
		<td>
			{VAR:LC_TYPE}:
		</td>
		<td>
			<input type="radio" name="sub[4][catering_type_chooser]" value="1" {VAR:catering_type_chooser_1}/>
			<select name="sub[4][catering_type_select]">
				<!-- SUB: CATERING_TYPE -->
				<option value="{VAR:value}" {VAR:catering_type_select}>{VAR:caption}</option>
				<!-- END SUB: CATERING_TYPE -->
			</select>
			<br/>
			<input type="radio" name="sub[4][catering_type_chooser]" value="2" {VAR:catering_type_chooser_2}/>
			{VAR:LC_OTHER}:
			<input type="text" name="sub[4][catering_type_text]" value="{VAR:catering_type_text}"/>
		</td>
	</tr>
	<tr>
		<td>
			{VAR:LC_START_TIME}:
		</td>
		<td>
			<input type="text" name="sub[4][catering_start_time]" value="{VAR:catering_start_time}"/>
		</td>
	</tr>
	<tr>
		<td>
			{VAR:LC_END_TIME}:
		</td>
		<td>
			<input type="text" name="sub[4][catering_end_time]" value="{VAR:catering_end_time}"/>
		</td>
	</tr>
	<tr>
		<td>
			{VAR:LC_ATTENDEE_NO}:
		</td>
		<td>
			<input type="text" name="sub[4][catering_attendees_no]" value="{VAR:catering_attendees_no}"/>
		</td>
	</tr>

	<!-- end main catering -->
	
	<tr>
		<td colspan="2" align="right">
			<a href="#" onClick="javascript:submit_changeform('add_catering');">Add/Save catering</a>
		</td>
	</tr>
	<!-- SUB: ADD_DAY -->
	<tr>
		<td colspan="2" align="right">
			<a href="#" onClick="javascript:submit_changeform('add_fun');">Add/Save day</a>
		</td>
	</tr>
	<!-- END SUB: ADD_DAY -->
</table>
