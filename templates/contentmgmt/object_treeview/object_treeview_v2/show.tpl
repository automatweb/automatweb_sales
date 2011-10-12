<script src="{VAR:baseurl}/automatweb/js/popup_menu.js" type="text/javascript"></script>
<link rel="stylesheet" href="{VAR:baseurl}/automatweb/css/obj_tree.css" />

<style type='text/css'>
.fgtext_bad
{
	font-family: Verdana, Arial, sans-serif;
	font-size: 11px;
	color: #000000;
	text-decoration: none;
}
.fgtext_bad a {color: #006FC5; text-decoration:underline;}
.fgtext_bad a:hover {color: #04BDE3; text-decoration:underline;}
</style>

<!-- SUB: FOLDERS -->

<!-- END SUB: FOLDERS -->

<!-- SUB: TABLE -->
<form name="objlist" action="{VAR:baseurl}/orb{VAR:ext}" method="POST">
<a name="table"></a>
<table class="{VAR:table_css_class}" border="0" width="100%" cellpadding="3" cellspacing="0">
	<tr bgcolor="{VAR:header_bgcolor}" {VAR:style_text}>

		<!-- SUB: HEADER -->
		<td class="{VAR:header_css_class}">{VAR:h_text}</td>
		<!-- END SUB: HEADER -->

	</tr>
	<!-- SUB: FILE -->
	<tr bgcolor="{VAR:bgcolor}" {VAR:style_text}>

		<!-- SUB: COLUMN -->
		<td class="{VAR:css_class}">{VAR:content}</td>
		<!-- END SUB: COLUMN -->
	</tr>
	<!-- END SUB: FILE -->
	<!-- SUB: FILE_GROUP -->
	<tr bgcolor="{VAR:group_header_bgcolor}">
		<td class="{VAR:group_css_class}" colspan="{VAR:cols_count}">{VAR:content}</td>
	</tr>
	<!-- END SUB: FILE_GROUP -->
</table>
<p>
<!-- SUB: PAGE -->
<a href="{VAR:url}">{VAR:page}</a>
<!-- END SUB: PAGE -->
<!-- SUB: PAGE_SEL -->
<strong><a href="{VAR:url}">{VAR:page}</a></strong>
<!-- END SUB: PAGE_SEL -->
</p>
<center>
<!-- SUB: ALPHABET -->
<a href="{VAR:char_url}">{VAR:char}</a>&nbsp;&nbsp;
<!-- END SUB: ALPHABET -->
<!-- SUB: ALPHABET_SEL -->
<a href="{VAR:char_url}"><strong>{VAR:char}</strong></a>&nbsp;
<!-- END SUB: ALPHABET_SEL -->
</center>
{VAR:reforb}

</form>
<!-- END SUB: TABLE -->
