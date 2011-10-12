<form action="orb{VAR:ext}" method="POST" name="changeform" {VAR:form_target}>
{VAR:forms}
{VAR:reforb}


<input type="button" onClick="window.location.href='{VAR:prev_link}'" value="<< Tagasi">
<input type="submit" value="Saada">
</form>


<!-- SUB: FORM -->
<table border="1">
	<!-- SUB: PROPERTY -->
		<tr>
			<td>{VAR:caption}</td>
			<td>{VAR:value}</td>
		</tr>
	<!-- END SUB: PROPERTY -->
</table>
<!-- END SUB: FORM -->
<br>
<b>{VAR:gen_ctr_res}</b>
