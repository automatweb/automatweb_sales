<table border="0" align="center" width="100%">
<form action="orb{VAR:ext}" method="POST" name="changeform" {VAR:form_target}>
{VAR:form}
{VAR:reforb}
<script type="text/javascript">
function submit_changeform(action)
{
	{VAR:submit_handler}
	if (typeof action == "string" && action.length>0)
	{
		document.changeform.action.value = action;
	};
	document.changeform.submit();
}
</script>
</form>
</table>