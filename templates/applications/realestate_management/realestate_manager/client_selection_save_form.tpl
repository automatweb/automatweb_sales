<form method="post" action="{VAR:baseurl}/orb{VAR:ext}">
<table border='0' class="aw04contenttable" align="center" cellpadding="0" cellspacing="0" width="100%">
{VAR:form}
<!-- SUB: SUB_TITLE -->
<tr>
	<td colspan='2' class='aw04contentcellsubtitle'>
	{VAR:value}
	</td>
</tr>
<!-- END SUB: SUB_TITLE -->
<!-- SUB: LINE -->
<tr>
        <td class='aw04contentcellleft' width='80' nowrap>
		{VAR:caption}
		</td>
        <td class='aw04contentcellright'>
		{VAR:element}
        </td>
</tr>
<!-- END SUB: LINE -->
<tr>
        <td class='aw04contentcellleft' width='80' nowrap>&nbsp;</td>
        <td class='aw04contentcellright'>
		<input type="submit" value='{VAR:button_name}' class='aw04formbutton'>
        </td>
</tr>
</table>
{VAR:reforb}
</form>
