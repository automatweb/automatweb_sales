<form action="{VAR:baseurl}/{VAR:section}" method="GET">
	<table border="0" cellpadding="0" cellspacing="0">
	{VAR:form}
	</table>
	<input type="hidden" name="section" value="{VAR:section}">
	<input type='hidden' name='MAX_FILE_SIZE' value='1000000' />
</form>

<form action="{VAR:baseurl}/reforb.{VAR:ext}" method="POST">
	{VAR:table}
	{VAR:reforb}
	<input type="hidden" name="section" value="460">
	<input type="submit" value="Lisa ostukorvi">
</form>