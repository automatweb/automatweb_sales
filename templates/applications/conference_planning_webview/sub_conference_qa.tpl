<table>
	<tr>
		<td>
			{VAR:LC_SALUTATION}:
		</td>
		<td>
			<select name="sub[qa][salutation]">
				<option value="1" {VAR:salutation_1}>Mr</option>
				<option value="2" {VAR:salutation_2}>Mrs</option>
				<option value="3" {VAR:salutation_3}>Ms</option>
			</select>
		</td>
	</tr>
	<tr>
		<td>
			{VAR:LC_FIRSTNAME}:
		</td>
		<td>
			<input name="sub[qa][firstname]" type="text" value="{VAR:firstname}"/>
		</td>
	</tr>
	<tr>
		<td>
			{VAR:LC_LASTNAME}:
		</td>
		<td>
			<input name="sub[qa][lastname]" type="text" value="{VAR:lastname}"/>
		</td>
	</tr>

	<tr>
		<td>
			{VAR:LC_COMPANY_ASSOCATION}:
		</td>
		<td>
			<input name="sub[qa][company_assocation]" type="text" value="{VAR:company_assocation}"/>
		</td>
	</tr>
	<tr>
		<td>
			{VAR:LC_TITLE}:
		</td>
		<td>
			<input name="sub[qa][title]" type="text" value="{VAR:title}"/>
		</td>
	</tr>
	<tr>
		<td>
			{VAR:LC_PHONE_NUMBER}:
		</td>
		<td>
			<input name="sub[qa][phone_number]" type="text" value="{VAR:phone_number}"/>
		</td>
	</tr>
	<tr>
		<td>
			{VAR:LC_FAX_NUMBER}:
		</td>
		<td>
			<input name="sub[qa][fax_number]" type="text" value="{VAR:fax_number}"/>
		</td>
	</tr>
	<tr>
		<td>
			{VAR:LC_EMAIL}:
		</td>
		<td>
			<input name="sub[qa][email]" type="text" value="{VAR:email}"/>
		</td>
	</tr>
	<tr>
		<td>
			{VAR:LC_CONTACT_PREFERENCE}:
		</td>
		<td>
			<select name="sub[qa][contact_preference]">
				<option value="1" {VAR:contact_preference_1}>{VAR:LC_EMAIL}</option>
				<option value="2" {VAR:contact_preference_2}>{VAR:LC_PHONE}</option>
				<option value="3" {VAR:contact_preference_3}>{VAR:LC_FAX}</option>
			</select>
		</td>
	</tr>
</table>
<input type="button" onClick="javascript:submit_changeform('submit_user_data');" value="{VAR:LC_SEARCH}" />
