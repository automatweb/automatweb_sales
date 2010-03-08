<table>
	<tr>
		<td colspan="2">
			<table border="1">
				<tr>
					<td colspan="2">
						{VAR:LC_EVENT}
					</td>
				</tr>
				<!-- SUB: ROW -->
				<tr>					
					<td>
						{VAR:caption}
					</td>
					<td>
						<a href="{VAR:edit_url}">{VAR:LC_EDIT}</a> <a href="{VAR:remove_url}">{VAR:LC_REMOVE}</a>
					</td>
				</tr>
				<!-- END SUB: ROW -->
				<!-- SUB: ROW_ACTIVE -->
				<tr bgcolor="silver">
					<td>
						{VAR:caption}
					</td>
					<td>
						<a href="{VAR:edit_url}">{VAR:LC_EDIT}</a> <a href="{VAR:remove_url}">{VAR:LC_REMOVE}</a>
					</td>
				</tr>
				<!-- END SUB: ROW_ACTIVE -->
			</table>
		</td>
	</tr>
	<tr>
		<td>
			{VAR:LC_EVENT_TYPE}:
		</td>
		<td>
			<input type="radio" name="sub[5][event_type_chooser]" value="1" {VAR:event_type_chooser_1}/>
			<select name="sub[5][event_type_select]">
				<!-- SUB: EVT_TYPE -->
				<option value="{VAR:value}" {VAR:event_type_select}>{VAR:caption}</option>
				<!-- END SUB: EVT_TYPE -->
			</select>
			<br/>
			<input type="radio" name="sub[5][event_type_chooser]" value="2" {VAR:event_type_chooser_2}/>
			{VAR:LC_OTHER}:
			<input type="text" name="sub[5][event_type_text]" value="{VAR:event_type_text}"/>
		</td>
	</tr>
	<tr>
		<td colspan="2"><b>{VAR:LC_FUNCTION_ROOM}<b/></td>
	</tr>


	<tr>
		<td>
			{VAR:LC_DELEGATE_NO}:
		</td>
		<td>
			<input type="text" name="sub[5][delegates_no]" value="{VAR:delegates_no}"/>
		</td>
	</tr>
	<tr>
		<td>
			{VAR:LC_TABLE_FORM}:
		</td>
		<td>
			<select name="sub[5][table_form]">
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
			<input type="checkbox" name="sub[5][tech][{VAR:value}]" {VAR:tech}/>{VAR:caption}<br/>
			<!-- END SUB: TECH_EQUIP -->
		</td>
	</tr>
	<tr>
		<td>
			{VAR:LC_DOOR_SIGN}:
		</td>
		<td>
			<input type="text" name="sub[5][door_sign]" value="{VAR:door_sign}"/>
		</td>
	</tr>
	<tr>
		<td>
			{VAR:LC_PERSON_NO}:
		</td>
		<td>
			<input type="text" name="sub[5][persons_no]" value="{VAR:persons_no}"/>
		</td>
	</tr>
	<tr>
		<td>
			{VAR:LC_START_DATETIME}:
		</td>
		<td>
			<input type="text" size="10" name="sub[5][function_start_date]" value="{VAR:function_start_date}"/><input size="2" type="text" name="sub[5][function_start_time]" value="{VAR:function_start_time}"/> - <input size="2" type="text" name="sub[5][function_end_time]" value="{VAR:function_end_time}"/> {VAR:LC_TIME_FORMAT}
		</td>
	</tr>
	<tr>
		<td>
			{VAR:LC_24H}:
		</td>
		<td>
			<input type="checkbox" name="sub[5][24h]" {VAR:24h}/>
		</td>
	</tr>

	<tr>
		<td colspan="2"><b>{VAR:LC_CATERING}<b/></td>
	</tr>
	
	<!-- SUB: ADDITIONAL_CATERING -->
	<tr><td colspan="2">
		<table>
		<tr bgcolor="silver">
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
				{VAR:LC_ATTENDEE_NO}
			</td>
			<td>
			</td>
		</tr>
		<!-- SUB: CATERING_ROW -->
		<tr>
			<td>
				{VAR:cat_type}
			</td>
			<td>
				{VAR:cat_starttime}
			</td>
			<td>
				{VAR:cat_endtime}
			</td>
			<td>
				{VAR:cat_attendee_no}
			</td>
			<td>
				<a href="{VAR:cat_remove_url}">{VAR:LC_REMOVE}</a>&nbsp;<a href="{VAR:cat_edit_url}">{VAR:LC_EDIT}</a>
			</td>
		</tr>
		<!-- END SUB: CATERING_ROW -->
		<!-- SUB: CATERING_ROW_ACTIVE -->
		<tr>
			<td>
				{VAR:cat_type}
			</td>
			<td>
				{VAR:cat_starttime}
			</td>
			<td>
				{VAR:cat_endtime}
			</td>
			<td>
				{VAR:cat_attendee_no}
			</td>
			<td>
				<a href="{VAR:cat_remove_url}">{VAR:LC_REMOVE}</a>&nbsp;<a href="{VAR:cat_edit_url}">{VAR:LC_EDIT}</a>
			</td>
		</tr>
		<!-- END SUB: CATERING_ROW_ACTIVE -->
		</table>
	</td></tr>
	<!-- END SUB: ADDITIONAL_CATERING -->
	<tr>
		<td>
			{VAR:LC_TYPE}:
		</td>
		<td>
			<input type="radio" name="sub[5][catering_type_chooser]" value="1" {VAR:catering_type_chooser_1}/>
			<select name="sub[5][catering_type_select]">
				<!-- SUB: CATERING_TYPE -->
				<option value="{VAR:value}" {VAR:catering_type_select}>{VAR:caption}</option>
				<!-- END SUB: CATERING_TYPE -->
			</select>
			<br/>
			<input type="radio" name="sub[5][catering_type_chooser]" value="2" {VAR:catering_type_chooser_2}/>
			{VAR:LC_OTHER}:
			<input type="text" name="sub[5][catering_type_text]" value="{VAR:catering_type_text}"/>
		</td>
	</tr>
	<tr>
		<td>
			{VAR:LC_START_TIME}:
		</td>
		<td>
			<input type="text" name="sub[5][catering_start_time]" value="{VAR:catering_start_time}"/>
		</td>
	</tr>
	<tr>
		<td>
			{VAR:LC_END_TIME}:
		</td>
		<td>
			<input type="text" name="sub[5][catering_end_time]" value="{VAR:catering_end_time}"/>
		</td>
	</tr>
	<tr>
		<td>
			{VAR:LC_ATTENDEE_NO}:
		</td>
		<td>
			<input type="text" name="sub[5][catering_attendee_no]" value="{VAR:catering_attendee_no}"/>
		</td>
	</tr>

	<tr>
		<td></td>
		<td><a href="#" onClick="javascript:submit_changeform('add_fun_catering');">{VAR:LC_ADD_CATERING}</a></td>
	</tr>
	<tr>
		<td></td>
		<td><a href="#" onClick="javascript:submit_changeform('add_function');">{VAR:LC_ADD_FUNCTION}</a></td>
	</tr>
</table>
