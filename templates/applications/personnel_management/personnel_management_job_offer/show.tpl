<!-- SUB: AUTOINFO -->
Kandideerimiseks saada oma CV m&auml;rks&otilde;naga "{VAR:keywords}" koos palgasooviga e-postile {VAR:contact.email} või aadressile {VAR:sect}, {VAR:sect.contact}.
<!-- END SUB: AUTOINFO -->

<table width="520" border=0 align="center" cellpadding=3 cellspacing=2 class="text">
	<!-- SUB: COMPANY -->
	<tr>
		<td colspan="2" valing="top">
			{VAR:company}
		</td>
	</tr>
	<!-- END SUB: COMPANY -->
	<!-- SUB: SECT -->
	<tr>
		<td colspan="2" valing="top">
			{VAR:sect}
		</td>
	</tr>
	<!-- END SUB: SECT -->
	<!-- SUB: PROFESSION -->
	<tr>
		<td>
			<b>{VAR:profession.caption}</b>
		</td>
		<td>
			<b>{VAR:profession}</b>
		</td>
	</tr>
	<!-- END SUB: PROFESSION -->
	<!-- SUB: WORKINFO -->
	<tr>
		<td valign="top">
			<b>{VAR:workinfo.caption}</b>
		</td>
		<td>
			{VAR:workinfo}
		</td>
	</tr>
	<!-- END SUB: WORKINFO -->
	<!-- SUB: REQUIREMENTS -->
	<tr>
		<td valign="top">
			<b>{VAR:requirements.caption}</b>
		</td>
		<td>
			{VAR:requirements}
		</td>
	</tr>
	<!-- END SUB: REQUIREMENTS -->
	<!-- SUB: WEOFFER -->
	<tr>
		<td valign="top">
			<b>{VAR:weoffer.caption}</b>
		</td>
		<td>
			{VAR:weoffer}
		</td>
	</tr>
	<!-- END SUB: WEOFFER -->
	<!-- SUB: INFO -->
	<tr>
		<td valign="top">
			<b>{VAR:info.caption}</b>
		</td>
		<td>
			{VAR:info}
		</td>
	</tr>
	<!-- END SUB: INFO -->
	<!-- SUB: LOC_AREA -->
	<tr>
		<td valign="top">
			<b>{VAR:loc_area.caption}</b>
		</td>
		<td>
			{VAR:loc_area}
		</td>
	</tr>
	<!-- END SUB: LOC_AREA -->
	<!-- SUB: LOC_COUNTY -->
	<tr>
		<td valign="top">
			<b>{VAR:loc_county.caption}</b>
		</td>
		<td>
			{VAR:loc_county}
		</td>
	</tr>
	<!-- END SUB: LOC_COUNTY -->
	<!-- SUB: LOC_CITY -->
	<tr>
		<td valign="top">
			<b>{VAR:loc_city.caption}</b>
		</td>
		<td>
			{VAR:loc_city}
		</td>
	</tr>
	<!-- END SUB: LOC_CITY -->
	<!-- SUB: START_WORKING -->
	<tr>
		<td valign="top">
			<b>{VAR:start_working.caption}</b>
		</td>
		<td>
			{VAR:start_working}
		</td>
	</tr>
	<!-- END SUB: START_WORKING -->
	<!-- SUB: JOB_OFFER_FILE -->
	<tr>
		<td valign="top">
			<b>{VAR:job_offer_file.caption}</b>
		</td>
		<td>
			{VAR:job_offer_file}
		</td>
	</tr>
	<!-- END SUB: JOB_OFFER_FILE -->
	<!-- SUB: CONTACT -->
	<tr>
		<td valign="top">
			<b>{VAR:contact.caption}</b>
		</td>
		<td>
			{VAR:contact.firstname} {VAR:contact.lastname}, {VAR:contact.phone} {VAR:contact.email}
		</td>
	</tr>
	<!-- END SUB: CONTACT -->
	<!-- SUB: END -->
	<tr class="Grey">
		<td  valign="top" colspan="2" style="padding-left: 10px; padding-right:10px; padding-top: 10px; padding-bottom:10px;">
			{VAR:end.caption}: {VAR:end}
		</td>
	</tr>
	<!-- END SUB: END -->
	<!-- SUB: APPLY -->
	<tr>
		<td colspan="2">
			<a href='{VAR:apply_link}'>{VAR:apply.caption}</a>
		</td>
	</tr>
	<!-- END SUB: APPLY -->
</table>
