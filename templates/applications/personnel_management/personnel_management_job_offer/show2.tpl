<!--
<!-- SUB: AUTOINFO -->
Kandideerimiseks vajuta allolevale "kandideerin" nupule v&otilde;i saada oma CV m&auml;rks&otilde;naga "{VAR:keywords}" koos palgasooviga e-postile {VAR:sect.email} v&otilde;i aadressile {VAR:company}, {VAR:sect}, {VAR:sect.contact.aadress}, {VAR:sect.contact.linn} {VAR:sect.contact.postiindeks}.
<!-- END SUB: AUTOINFO -->
-->

<table width="100%" border="0" align="center" cellpadding=3 cellspacing=2 class="text">
<!--
	<!-- SUB: COMPANY -->
	<tr>
		<td colspan="2" valing="top">
			{VAR:company}
		</td>
	</tr>
	<!-- END SUB: COMPANY -->
-->
<!--
	<!-- SUB: SECT -->
	<tr>
		<td colspan="2" valing="top">
			{VAR:sect}
		</td>
	</tr>
	<!-- END SUB: SECT -->
-->
	<!-- SUB: PROFESSION -->
	<tr>
		<td colspan="2">
			<h2>{VAR:profession}</h2>
		</td>
	</tr>
	<!-- END SUB: PROFESSION -->
	<!-- SUB: WORKINFO -->
	<tr>
		<td valign="top">
			<b>{VAR:workinfo.caption}{VAR:LC_JOB_DESCRIPTION}</b>
		</td>
		<td>
			{VAR:workinfo}<br />
		</td>
	</tr>
	<!-- END SUB: WORKINFO -->
	<!-- SUB: REQUIREMENTS -->
	<tr>
		<td valign="top">
			<b>{VAR:requirements.caption}</b>
		</td>
		<td>
			{VAR:requirements}<br />
		</td>
	</tr>
	<!-- END SUB: REQUIREMENTS -->
	<!-- SUB: SUPLEMENTARY -->
	<tr>
		<td valign="top">
			<b>{VAR:suplementary.caption}</b>
		</td>
		<td>
			{VAR:suplementary}<br />
		</td>
	</tr>
	<!-- END SUB: SUPLEMENTARY -->
	<!-- SUB: WEOFFER -->
	<tr>
		<td valign="top">
			<b>{VAR:weoffer.caption}</b>
		</td>
		<td>
			{VAR:weoffer}<br />
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
			{VAR:contact.firstname} {VAR:contact.lastname}, {VAR:contact.phone} {VAR:sect.contact.email}
		</td>
	</tr>
	<!-- END SUB: CONTACT -->
	<!-- SUB: END -->
	<tr>
		<td valign="top">
			<b>{VAR:end.caption}</b>
		</td>
		<td>
			{VAR:end}
		</td>
	</tr>
	<!-- END SUB: END -->
</table>
