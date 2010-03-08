<table id="sisu" width="100%" ><tr><td>
<form name="changeform" action="reforb.{VAR:ext}" method="POST">
{VAR:reforb}

{VAR:picker}
{VAR:toolbar}
<table cellpadding="5" cellspacing="10" border="0" width="100%">
<tr>
	<!-- SUB: CAL -->
	<td valign="top" align="left" style="border: 1px black solid;">
		{VAR:cal}
	</td>
	<!-- END SUB: CAL -->
</tr>
</table>
<script language="javascript">
function submit_changeform()
{
	document.changeform.submit();
}
</script>
</form>
</div>
</td></tr></table>
