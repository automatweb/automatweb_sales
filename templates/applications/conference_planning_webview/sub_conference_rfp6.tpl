<!-- SUB: MISSING_ERROR -->
<table>
	<!-- SUB: ERROR -->
	<tr>
		<td>
			{VAR:caption}
		<td>
	</tr>
	<!-- END SUB: ERROR -->
</table>
<!-- END SUB: MISSING_ERROR -->

<table>
	<tr>
		<td colspan="2">{VAR:LC_BILLING_DETAILS}</td>
	</tr>
	<tr>
		<td>
			{VAR:LC_COMPANY}
		</td>
		<td>
			<input type="text" name="sub[6][billing_company]" value="{VAR:billing_company}"/>
		</td>
	</tr>
	<tr>
		<td>
			{VAR:LC_CONTACT}
		</td>
		<td>
			<input type="text" name="sub[6][billing_contact]" value="{VAR:billing_contact}"/>
		</td>
	</tr>
	<tr>
		<td>
			{VAR:LC_STREET}
		</td>
		<td>
			<input type="text" name="sub[6][billing_street]" value="{VAR:billing_street}"/>
		</td>
	</tr>
	<tr>
		<td>
			{VAR:LC_CITY}
		</td>
		<td>
			<input type="text" name="sub[6][billing_city]" value="{VAR:billing_city}"/>
		</td>
	</tr>
	<tr>
		<td>
			{VAR:LC_ZIP}
		</td>
		<td>
			<input type="text" name="sub[6][billing_zip]" value="{VAR:billing_zip}"/>
		</td>
	</tr>
	<tr>
		<td>
			{VAR:LC_COUNTRY}
		</td>
		<td>
			<select name="sub[6][billing_country]">
				<!-- SUB: COUNTRY -->
				<option value="{VAR:value}" {VAR:billing_country}>{VAR:caption}</option>
				<!-- END SUB: COUNTRY -->
			</select>
		</td>
	</tr>
	<tr>
		<td colspan="2">{VAR:LC_CONTACT_INFORMATION}</td>
	</tr>
	<tr>
		<td>
			{VAR:LC_NAME}
		</td>
		<td>
			<input type="text" name="sub[6][billing_name]" value="{VAR:billing_name}"/>
		</td>
	</tr>
	<tr>
		<td>
			{VAR:LC_PHONE_NUMBER}
		</td>
		<td>
			<input type="text" name="sub[6][billing_phone_number]" value="{VAR:billing_phone_number}"/>
		</td>
	</tr>
	<tr>
		<td>
			{VAR:LC_EMAIL}
		</td>
		<td>
			<input type="text" name="sub[6][billing_email]" value="{VAR:billing_email}"/>
		</td>
	</tr>
	<tr>
		<td colspan="2">OTSINGUTULEMUSED</td>
	</tr>
	<!-- SUB: SEARCH_RESULT -->
	<tr>
		<td colspan="2">
			<input type="checkbox" name="sub[6][search_result][{VAR:value}]" {VAR:selected}/>{VAR:caption} {VAR:IMG_1} ({VAR:address}) - {VAR:single_count}/{VAR:double_count}/{VAR:suite_count}<br/>
			{VAR:info}
		</td>
	</tr>
	<!-- END SUB: SEARCH_RESULT -->
	<!-- SUB: SEARCH_RESULT_ERROR -->
	<tr>
		<td colspan="2" style="font-size:10px;color:red;">
			&nbsp;&nbsp;&nbsp;NB! {VAR:caption}
		</td>
	</tr>
	<!-- END SUB: SEARCH_RESULT_ERROR -->
	<tr>
		<td>
			Urgent	
		</td>
		<td>
			<input type="checkbox" name="sub[6][urgent]" {VAR:urgent} />
		</td>
	</tr>
	{VAR:all_search_results}
	</td>
</table>
