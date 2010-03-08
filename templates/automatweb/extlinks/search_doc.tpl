<script language="javascript">
function ss(li,title)
{
	window.opener.setLink(li,title);
	window.close();
}
</script>
<form method="GET" action="orb.{VAR:ext}">
<table border=0 cellspacing=0 cellpadding=0>
<tr>
<td class="aste01" colspan="2">
<table border=0 cellspacing=0 cellpadding=5>
<tr>
<td colspan="2">

		<table border=0 cellspacing=0 cellpadding=2>
		<tr>
		
		<td class="celltext" align="right">{VAR:LC_EXTLINKS_SEARCH_FROM_NAME}</td>
		<td class="celltext"><input type="text" name="s_name" size="40" value='{VAR:s_name}'
		class="formtext"></td>
		</tr>
		<tr>
		<td class="celltext" align="right">{VAR:LC_EXTLINKS_SEARCH_FROM_CONTENT}</td>
		<td class="celltext"><input type="text" name="s_content" size="40" value='{VAR:s_content}' class="formtext"></td>
		</tr>
		<tr>
		    <td></td>
			<td class="celltext"><input type="submit" class="formbutton" value="{VAR:LC_EXTLINKS_SEARCH}"></td>
		</tr>
		</table>

</td></tr>
<tr class="aste06">
		<td class="celltext" colspan=2>{VAR:LC_EXTLINKS_RESULT}:</td>
		</tr>
{VAR:LINE}
</table>
</td></tr></table>
{VAR:reforb}
</form>
