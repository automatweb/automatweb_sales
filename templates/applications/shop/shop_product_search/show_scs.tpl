<link rel="stylesheet" href="{VAR:baseurl}/automatweb/css/aw.css" />

<form action="{VAR:baseurl}/reforb{VAR:ext}" method="POST">
{VAR:table}
{VAR:reforb}
<input type="hidden" name="section" value="460">
<input type="submit" value="Lisa ostukorvi">
</form>

<form action="{VAR:baseurl}/index{VAR:ext}" method="GET">
<table border="0" cellpadding="0" cellspacing="0">
{VAR:form}
</table>
<input type="hidden" name="section" value="{VAR:section}">
{VAR:s_ro}
</form>
