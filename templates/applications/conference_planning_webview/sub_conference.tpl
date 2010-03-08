<script language="JavaScript1.2" type="text/javascript">
// adds one unit to a input with the ID given with the callout of the script
function form_minus(ID) {
	if (document.getElementById(ID).value>0) {
		document.getElementById(ID).value=document.getElementById(ID).value-1;
	}
}
// substracts one unit from a input with the ID given with the callout of the script
function form_plus(ID) {
	if (document.getElementById(ID).value<1) {
		document.getElementById(ID).value=0;
	}
	document.getElementById(ID).value=eval(document.getElementById(ID).value)+1;
}
</script>
<table>
<tr>
	<td>
		{VAR:LC_COUNTRY}:
	</td>
	<td>
		<select name="sub[0][country]"> 
			<!-- SUB: COUNTRY -->
			<option value="{VAR:value}" {VAR:country}>{VAR:caption}</option>
			<!-- END SUB: COUNTRY -->
		</select>
	</td>
</tr>

<tr>
	<td>{VAR:LC_NR_OF_ATTENDES}:</td>
	<td>
		<input type="text" name="sub[0][attendees_no]" id="t1"/>
		<!-- SUB: ATTENDES_JS -->
		<a href="javascript:form_minus('t1');">-</a>
		<a href="javascript:form_plus('t1');">+</a>
		<!-- END SUB: ATTENDES_JS -->
	</td>
</tr>
<tr>
	<td>{VAR:LC_SINGLE_ROOMS}:</td>
	<td>
		<select name="sub[0][single_count]">
			<!-- SUB: SINGLE_OPTION -->
				<option value="{VAR:value}">{VAR:caption}</option>
			<!-- END SUB: SINGLE_OPTION -->
		</select>
	</td>
</tr>
<tr>
<tr>
	<td>{VAR:LC_DOUBLE_ROOMS}:</td>
	<td>
		<select name="sub[0][double_count]">
			<!-- SUB: DOUBLE_OPTION -->
				<option value="{VAR:value}">{VAR:caption}</option>
			<!-- END SUB: DOUBLE_OPTION -->	
		</select>
	</td>
</tr>

<tr>
	<td>{VAR:LC_SUITES}:</td>
	<td>
		<select name="sub[0][suite_count]">
			<!-- SUB: SUITE_OPTION -->
				<option value="{VAR:value}">{VAR:caption}</option>
			<!-- END SUB: SUITE_OPTION -->
		</select>
	</td>
</tr>
</table>
<input type="button" onClick="javascript:submit_changeform('submit_forward');" value="{VAR:LC_SEARCH}" />
