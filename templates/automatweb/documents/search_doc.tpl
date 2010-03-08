<div id="pais"><!-- --></div>

<div id="sisu">
<form method="GET" action="orb.{VAR:ext}">

<table width="100%" cellspacing="0" cellpadding="0" border="0" id="awcbContentTblDefault"> 
<tr>
<td>
		<table border=0 cellspacing=0 cellpadding=0>
		<tr>
			<td width="100" id="linecaption">&nbsp;</td>
			<td id="lineelment">&nbsp;</td>
		</tr>
		<tr>
			<td width="100" id="linecaption">{VAR:LC_DOCUMENT_SEARCH_NAME}:</td>
			<td id="lineelment"><input type="text" name="s_name" size="40" value='{VAR:s_name}' class="formtext"></td>
		</tr>
		<tr>
			<td width="100" id="linecaption">{VAR:LC_DOCUMENT_SEARCH_CONTENT}:</td>
			<td id="lineelment"><input type="text" name="s_content" size="40" value='{VAR:s_content}' class="formtext"></td>
		</tr>
		<tr>
			<td width="100" id="linecaption">&nbsp;</td>
			<td id="lineelment"><input id="button" type="submit" value="{VAR:LC_DOCUMENT_SEARCH}"></td>
		</tr>
		<tr>
			<td width="100" id="linecaption">&nbsp;</td>
			<td id="lineelment">&nbsp;</td>
		</tr>
		</table>
</td>
</tr>
<tr>
<td>
	<table border=0 cellspacing=0 cellpadding=0>
	<tr>
		<td width="100" id="linecaption">{VAR:LC_DOCUMENT_FOUND_DOCS}:</td>
		<td>&nbsp;</td>
	</tr>
	<!-- SUB: LINE -->
	<tr class="aste01">
		<td width="100" id="linecaption"><a target="_blank" href='{VAR:change}'>{VAR:name}</a></td>
		<td id="lineelment"><a href='{VAR:brother}'>{VAR:LC_DOCUMENT_DO_BROTHER}</a></td>
	</tr>
	<!-- END SUB: LINE -->
	</table>

</td>
</tr>
</table>
{VAR:reforb}
</form>
</div>