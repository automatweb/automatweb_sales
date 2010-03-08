
<html>
<head>
<title>{VAR:browser_caption}</title>
<link REL="icon" HREF="{VAR:baseurl}/automatweb/images/icons/favicon.ico" TYPE="image/x-icon">
</head>
<body style="background-color: #E1E1E1; margin: 0px;" onLoad="generic_loader();">
<div style="background-color: #05a6e9; font-weight: bold; font-size: 13px; color: #fff; font-family: Arial,helvetica; padding: 5px;">
{VAR:help_caption}
</div>
<table border="1" width="100%" height="96%" cellspacing="2" cellpadding="0" style="border-collapse: collapse; background: #e1e1e1;">
<tr>
<td width="200" valign="top" style="background-color: #fff;">{VAR:help_content_tree}</td>
<td valign="top" style="background-color: #fff;">
<iframe src="{VAR:help_content}" name="helpcontent" frameborder="0" width="100%" height="500">
</iframe>
</td>
</tr>
</table>
</body>
</html>
