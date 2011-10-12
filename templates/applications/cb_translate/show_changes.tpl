<style>
body {
	font-family: Arial,sans-serif;
	font-size: 13px;
}
.apply
{
	font-family: Arial,sans-serif;
	color: #000000;
	font-size:14px;
	font-weight:bold;
}
.filename
{
	font-family:Arial;
	font-size:12px;
	color:gray;
}
</style>
<div style="background-color: #EEE; font-size: 14px; font-weight: bold; font-family: Arial,sans-serif; padding: 5px;">{VAR:caption}</div>
<form id="changeform" method="POST" action="{VAR:baseurl}/reforb{VAR:ext}">
<table border='1' width='100%'>
<!-- SUB: NO_CHANGE -->
<tr>
	<td colspan="2" class="filename">{VAR:nochange}</td>
</tr>
<!-- END SUB: NO_CHANGE -->
<!-- SUB: SUB_CHANGE -->
<tr bgcolor='#eeeeee'>
	<td>Keel</td>
	<td>{VAR:lang}</td>
</tr>
<tr>
	<td>Muudetud objekt / tekst</td>
	<td>{VAR:object}</td>
</tr>
<tr>
	<td>Eelnev tekst</td>
	<td>{VAR:prev}</td>
</tr>
<tr>
	<td>Uus tekst</td>
	<td>{VAR:new}</td>
</tr>
<!-- END SUB: SUB_CHANGE -->
</table>
<br>
<div align="right">{VAR:apply_link}</div>
</form>