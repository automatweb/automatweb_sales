<link rel="stylesheet" href="{VAR:baseurl}/automatweb/css/aw.css" />
<form action="{VAR:baseurl}/{VAR:section}" method="GET">
<table border="0" cellpadding="0" cellspacing="0">
{VAR:form}
</table>
{VAR:reforb}

</form>

<form action="{VAR:baseurl}/orb{VAR:ext}" method="POST">
<!-- SUB: NO_ERROR -->
{VAR:table}
<!-- END SUB: NO_ERROR -->

<!-- SUB: HAS_ERROR -->
<span style="color: red; font-size: 14px;">{VAR:errmsg}</span><br><br>
<!-- END SUB: HAS_ERROR -->

<!-- SUB: SUBMIT_BUTTON -->
<input type="submit" value="{VAR:submit_text}">
<!-- END SUB: SUBMIT_BUTTON -->

{VAR:reforb}
</form>