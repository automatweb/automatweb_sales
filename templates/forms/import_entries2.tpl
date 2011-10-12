<form action='reforb{VAR:ext}' method=post>
<table bgcolor="#CCCCCC" cellpadding=3 cellspacing=1 border=0>
<tr>
<td class="fform">{VAR:LC_FORMS_MAKE_DATA_AND_FORMEL_EQU}:</td>
</tr>
<tr>
<td class="fform">
<table bgcolor="#CCCCCC" cellpadding=3 cellspacing=1 border=0>
<tr>
	<td class="fform">&nbsp;</td>
	<!-- SUB: FCOL -->
		<td class="fform" align="center" valign="top">{VAR:cnt}<br>{VAR:val}</td>
	<!-- END SUB: FCOL -->
	<td class="fform">{VAR:LC_FORMS_EMPTY}</td>
</tr>
<!-- SUB: ROW -->
<tr>
	<td class="fform">{VAR:el_name}<br><i>({VAR:el_type})</i></td>
	<!-- SUB: COL -->
	<td class="fform" align="center"><input type='radio' name='el[{VAR:el_id}]' value='{VAR:col}'></td>
	<!-- END SUB: COL -->
	<td class="fform" align="center"><input type='radio' name='el[{VAR:el_id}]' value='-1'></td>
</tr>
<!-- END SUB: ROW -->
</table>
</td>
</tr>
<tr>
<td class="fform" colspan=2><input class='small_button' type='submit' VALUE='Import'></td>
</tr>
</table>
{VAR:reforb}
</form>
