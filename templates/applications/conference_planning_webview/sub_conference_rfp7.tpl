<script type="text/javascript">
/* formi valideerimine, kasutab headis inkluuditud {VAR:baseurl}/css/js/gen_validatorv2.js scripti */
function handleClick1 ()
{
	submit_changeform('submit_forward');
}
</script>

<table class="form">
	<tr class="subheading2">
		<th colspan="2">{VAR:LC_GENERAL_INFORMATION}</th>
	</tr>
	<!-- SUB: COUNTRY -->
	<tr>
		<th><label for="iField1">{VAR:LC_COUNTRY}:</label></th>
		<td class="data">{VAR:country}</td>
	</tr>
	<!-- END SUB: COUNTRY -->
	<tr>
		<th><label for="iField1">{VAR:LC_FUNCTION_NAME}:</label></th>
		<td class="data">{VAR:function_name}</td>
	</tr>
	<tr>
		<th><label for="iField2">{VAR:LC_ORGANISATION_COMPANY}:</label></th>
		<td class="data">{VAR:organisation_company}</td>
	</tr>
	<tr>

		<th><label for="date2">{VAR:LC_RESPONSE_DATE}:</label></th>
		<td class="data">{VAR:response_date}</td>
	</tr>
	<tr>
		<th><label for="date3">{VAR:LC_DECISION_DATE}:</label></th>
		<td class="data">{VAR:decision_date}</td>
	</tr>

	<tr>
		<th><label for="date4">{VAR:LC_ARR_DATE}:</label></th>
		<td class="data">{VAR:arrival_date}</td>
	</tr>
	<tr>
		<th><label for="date5">{VAR:LC_DEP_DATE}:</label></th>
		<td class="data">{VAR:departure_date}</td>

	</tr>
	<tr>
		<th><label for="iField8a">{VAR:LC_ALTER_DATES}:</label></th>
		<td class="data">{VAR:open_for_alternative_dates}</td>
	</tr>
	<tr>
		<th><label for="iField8b">{VAR:LC_HAVE_ACC_REQ}:</label></th>
		<td class="data">{VAR:needs_rooms}</td>
	</tr>
	<tr>
		<th>{VAR:LC_MAIN_FUNCTION_MULTI_DAY}</th>
		<td>{VAR:multi_day}</td>
	</tr>
	<tr class="subheading2">
		<th colspan="2">{VAR:LC_ALTERNATIVE_DATES}</th>
	</tr>
</table>



<table class="data">
	<tr>
		<th>{VAR:LC_DATE_TYPE}</th>
		<th>{VAR:LC_ARR_DATE}</th>
		<th>{VAR:LC_DEP_DATE}</th>
	</tr>
	<!-- SUB: DATES_ROW -->
	<tr>
		<td>{VAR:date_type}</td>
		<td>{VAR:arrival_date}</td>
		<td>{VAR:departure_date}</td>
	</tr>
	<!-- END SUB: DATES_ROW -->
</table>

<table class="form">
	<!-- SUB: FLEXIBLE_DATES -->
	<tr>
		<th><label for="iField800b">{VAR:LC_FLEXIBLE_DATES}</label></th>
		<td class="data"></td>
	</tr>


	<tr>

		<th>{VAR:LC_PATTERN}:</th>
		<td class="data">
			<!-- SUB: PATTERN_NO_APP -->
				{VAR:LC_NOT_APP}
			<!-- END SUB: PATTERN_NO_APP -->
			<!-- SUB: PATTERN_WDAY -->
				{VAR:wday_from}&nbsp;{VAR:LC_TO}&nbsp;{VAR:wday_to}
			<!-- END SUB: PATTERN_WDAY -->
			<!-- SUB: PATTERN_DAYS -->
				{VAR:days} days.
			<!-- END SUB: PATTERN_DAYS -->
		</td>
	</tr>
	<!-- END SUB: FLEXIBLE_DATES -->

	<tr>
		<th><label for="iField90">{VAR:LC_DATE_COMMENTS}:</label></th>
		<td class="data">{VAR:date_comments}</td>
	</tr>

	<!-- SUB: NEEDS_ROOMS -->
	<tr class="subheading2">
		<th colspan="2">{VAR:LC_ACCOMMODATION}</th>
	</tr>
<!--
	<tr>
		<th>{VAR:LC_NEEDS_ROOMS}</th>
		<td class="data"></td>
	</tr> -->

	<tr>
		<th>{VAR:LC_SINGLE_ROOMS}:</th>
		<td class="data">{VAR:single_count}</td>
	</tr>
	<tr>
		<th>{VAR:LC_DOUBLE_ROOMS}:</th>
		<td class="data">{VAR:double_count}</td>

	</tr>
	<tr>
		<th><label for="iField88">{VAR:LC_SUITES}:</label></th>
		<td class="data">{VAR:suite_count}</td>
	</tr>

	<!--<td>{VAR:dates_comments}</td>-->
	<tr>
		<th><label for="date10">{VAR:LC_MAIN_ARR_DATE}</label></th>

		<td class="data">{VAR:arrival_date}</td>
	</tr>
	<tr>
		<th><label for="date11">{VAR:LC_MAIN_DEP_DATE}</label></th>
		<td class="data">{VAR:departure_date}</td>
	</tr>
	<!-- END SUB: NEEDS_ROOMS -->


	<tr class="subheading2">
		<th colspan="2">{VAR:LC_MAIN_EVENT}</th>
	</tr>
	<!-- SUB: MAIN_FUNCTION_DAY -->
	<tr>
		<th>{VAR:LC_EVENT_TYPE}:</th>
		<td class="data">{VAR:main_event_type}</td>
	</tr>
	<tr class="subheading2">
		<th colspan="2">{VAR:LC_MAIN_FUNCTION_ROOM} - {VAR:main_start_date}</th>
	</tr>
	<!--<tr>
		<th>{VAR:LC_DELEGATE_NO}:</th>
		<td class="data">{VAR:main_delegates_no}</td>
	</tr>-->
	<tr>

		<th>{VAR:LC_TABLE_FORM}:</th>
		<td class="data">{VAR:main_table_form}</td>
	</tr>
	<tr>
		<th valign="top">{VAR:LC_TECH_EQUIP}:</th>
		<td class="data">
		<!-- SUB: MAIN_TECH_EQUIP -->
			{VAR:value}<br/>
		<!-- END SUB: MAIN_TECH_EQUIP -->
		</td>
	</tr>

	<tr>
		<th>{VAR:LC_DOOR_SIGN}:</th>
		<td class="data">{VAR:main_door_sign}</td>
	</tr>
	<tr>
		<th>{VAR:LC_PERSON_NO}:</th>
		<td class="data">{VAR:main_person_no}</td>

	</tr>
	<tr>
		<th>{VAR:LC_START_DATETIME}:</th>
		<td class="data">{VAR:main_start_date}</td>
	</tr>
	<tr>
		<th>{VAR:LC_TIME}:</th>
		<td class="data">{VAR:main_start_time} - {VAR:main_end_time}</td>
	</tr>
	<tr>
		<th>{VAR:LC_24H}:</th>
		<td class="data">{VAR:main_24h}</td>
	</tr>
	<tr class="subheading2">
		<th colspan="2">{VAR:LC_MAIN_CATERING}</th>
	</tr>
	<tr>
		<th colspan="2">
<!-- SUB: MAIN_CATERING -->
<table class="data">
	<tr>
		<th>{VAR:LC_TYPE}</th>
		<th>{VAR:LC_START_TIME}</th>

		<th>{VAR:LC_END_TIME}</th>
		<th>{VAR:LC_ATTENDEE_NO}</th>
	</tr>
	<!-- SUB: TIMES_ROW -->
	<tr>
		<td>{VAR:type}</td>
		<td>{VAR:start_time}</td>
		<td>{VAR:end_time}</td>

		<td>{VAR:attendee_no}</td>
	</tr>
	<!-- END SUB: TIMES_ROW -->
	
</table>
<!-- END SUB: MAIN_CATERING -->

<!-- END SUB: MAIN_FUNCTION_DAY -->
	</th>
</table>

<!-- SUB: ADDITIONAL_FUNCTIONS -->
<table class="form">
	<tr class="subheading2">
		<th colspan="2">{VAR:LC_ADD_EVENTS}</th>
	</tr>
</table>
	<!-- SUB: ADD_FUNCTION_ROW -->
<table class="form">
	<tr class="subheading">
		<th colspan="2">{VAR:type}</th>
	</tr>
	<tr>
		<th><label for="iField800b">{VAR:LC_START_TIME}:</label></th>
		<td class="data">{VAR:start_time}</td>
	</tr>
	<tr>
		<th><label for="iField800b">{VAR:LC_END_TIME}:</label></th>
		<td class="data">{VAR:end_time}</td>
	</tr>
	
	<!--<tr>
		<th><label for="iField800b">{VAR:LC_DELEGATE_NO}:</label></th>
		<td class="data">{VAR:delegates_no}</td>
	</tr>-->
	<tr>
		<th><label for="iField800b">{VAR:LC_PERSON_NO}:</label></th>
		<td class="data">{VAR:persons_no}</td>
	</tr>
	
	<tr>
		<th><label for="iField800b">{VAR:LC_TABLE_FORM}:</label></th>
		<td class="data">{VAR:table_form}</td>
	</tr>
	<tr>
		<th><label for="iField800b">{VAR:LC_TECH_EQUIP}:</label></th>
		<td class="data">
		<!-- SUB: ADD_FUN_TECH -->
			{VAR:value}<br/>
		<!-- END SUB: ADD_FUN_TECH -->
		</td>
	</tr>
	<tr>
		<th><label for="iField800b">{VAR:LC_DOOR_SIGN}:</label></th>
		<td class="data">{VAR:door_sign}</td>
	</tr>
	<tr>
		<th><label for="iField800b">{VAR:LC_24H}:</label></th>
		<td class="data">{VAR:24h}</td>
	</tr>	
</table>
<table class="data">
	<!-- SUB: ADD_FUNCTION_CATERING -->
	
				<tr>
					<th>{VAR:LC_CATERING}</th>
					<th>{VAR:LC_START_TIME}</th>
					<th>{VAR:LC_END_TIME}</th>
					<th>{VAR:LC_ATTENDEE_NO}</th>
				</tr>
				<!-- SUB: ADD_FUNCTION_CATERING_ROW -->
				<tr>
					<td>{VAR:type}</td>
					<td>{VAR:start_time}</td>
					<td>{VAR:end_time}</td>
					<td>{VAR:attendee_no}</td>
				</tr>
				<!-- END SUB: ADD_FUNCTION_CATERING_ROW -->
		</td>
	</tr>
	<!-- END SUB: ADD_FUNCTION_CATERING -->
	<!-- END SUB: ADD_FUNCTION_ROW -->
</table>
<!-- END SUB: ADDITIONAL_FUNCTIONS -->


<table class="form">
	<tr class="subheading2">
		<th colspan="2">{VAR:LC_BILLING_DETAILS}</th>
	</tr>
	<tr>
		<th>{VAR:LC_COMPANY}:</th>
		<td class="data">{VAR:billing_company}</td>
	</tr>
	<tr>
		<th>{VAR:LC_CONTACT}:</th>
		<td class="data">{VAR:billing_contact}</td>
	</tr>
	<tr>

		<th>{VAR:LC_STREET}:</th>
		<td class="data">{VAR:billing_street}</td>
	</tr>
	<tr>
		<th>{VAR:LC_CITY}:</th>
		<td class="data">{VAR:billing_city}</td>
	</tr>

	<tr>
		<th>{VAR:LC_ZIP}:</th>
		<td class="data">{VAR:billing_zip}</td>
	</tr>
	<tr>
		<th>{VAR:LC_COUNTRY}:</th>
		<td class="data">{VAR:billing_country}</td>

	</tr>
	<tr class="subheading2">
		<th colspan="2">{VAR:LC_CONTACT_INFORMATION}</th>
	</tr>
	<tr>
		<th>{VAR:LC_NAME}:</th>
		<td class="data">{VAR:billing_name}</td>

	</tr>
	<tr>
		<th>{VAR:LC_PHONE_NUMBER}:</th>
		<td class="data">{VAR:billing_phone_number}</td>
	</tr>
	<tr>
		<th>{VAR:LC_EMAIL}:</th>

		<td class="data">{VAR:billing_email}</td>
	</tr>
	<tr class="subheading2">
		<th colspan="2">{VAR:LC_HOTELS}</th>
	</tr>
</table>




<!-- SUB: SEARCH_RESULT -->
<h3>{VAR:caption}&nbsp;{VAR:IMG_1}</h3>
<table class="hdata">
	<tr>
		<td class="illustr-small">
			<img src="{VAR:photo_uri}" alt="" title="" />
		</td>
		<td>

			<div class="hdesc">
				<div class="hdata">
					<div class="hdata1">
						{VAR:address}<br />
						<a href="{VAR:LC_CONF_INFO_URL}">{VAR:LC_CONF_INFO}</a> | <a href="javascript:void(0)" onClick="javascript:aw_popup_scroll('{VAR:map_uri}', 'Map', 500, 500);">{VAR:LC_LOC_MAP}</a>
					</div>

					<div class="hdata2">
						Phone: {VAR:phone}<br />
						Fax: {VAR:fax}<br /> 
						<!-- SUB: RES_EMAIL -->
						<a href="mailto:{VAR:email}">{VAR:email}</a>
						<!-- END SUB: RES_EMAIL -->
					</div>
					<div class="clear1">&nbsp;</div>
					<p>{VAR:info}<p>
				</div>
			</div>
		</td>
	</tr>
</table>
<!-- END SUB: SEARCH_RESULT -->


<table class="form">
	<tr>
		<th>{VAR:LC_INF_ABOVE_CORRECT}</th>
		<td><input type="checkbox" name="confirm_rfp_submit" value="c1" id="{VAR:confirm_ch_id}" class="rs" /></td>
	</tr>
</table>
