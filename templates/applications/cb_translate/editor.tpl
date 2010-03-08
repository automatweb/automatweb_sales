<html>
<head>
<style>
.right_caption
{
	color:#FFFFFF;
}
</style>
<title>{VAR:browser_caption}</title>
<link REL="icon" HREF="{VAR:baseurl}/automatweb/images/icons/favicon.ico" TYPE="image/x-icon">
</head>
<body style="background-color: #E1E1E1; margin: 0px;">
<table width="100%" style="background-color: #fb150e; font-weight: bold; font-size: 13px; color: #fff; font-family: Arial,helvetica; padding: 5px;" cellpadding="0" cellspacing="0">
<td>
{VAR:editor_caption}
</td><td align="right">
{VAR:apply_link}
</td></table>
{VAR:toolbar}
<table border="1" width="100%" height="96%" cellspacing="2" cellpadding="0" style="border-collapse: collapse; background: #e1e1e1;">
<tr>
<td width="200" valign="top" style="background-color: #fff;">{VAR:editor_content_tree}</td>
<td valign="top" style="background-color: #fff;width:100%;height:100%">
<iframe src="{VAR:editor_content}" name="editorcontent" frameborder="0" width="100%" height="700">
</iframe>
</td>
</tr>
</table>
</body>
</html>
