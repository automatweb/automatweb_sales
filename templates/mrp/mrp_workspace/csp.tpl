<link rel="stylesheet" type="text/css" href="http://localhost/orb.aw?class=minify_js_and_css&amp;action=get_css&amp;name=aw_admin.css">
<script language="javascript">
function ss(title)
{
	window.opener.setLink(title);
	window.close();
}
</script>

<form method="GET" action="orb{VAR:ext}">
<table id="awcbContentTblDefault" width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td>
			<table id="awcbContentTblDefault" width="100%" cellspacing="0" cellpadding="0" border="0">
			<tr>
				<td id="linecaption" align="right">Otsi nimest</td>
				<td id="lineelment"><input type="text" name="s_name" size="40" value='{VAR:s_name}' class="formtext"></td>
			</tr>
			<tr>
				<td id="lineelment" colspan="2"><input style="margin: 5px;" type="submit" id="button" value="Otsi"></td>
			</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td id="linecaption" style="text-align: left;">Tulemused:</td>
	</tr>
	{VAR:LINE}
</table>
{VAR:reforb}
</form>

