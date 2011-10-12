<form name="objlist" action="{VAR:baseurl}/orb{VAR:ext}" method="POST">

<script src="{VAR:baseurl}/automatweb/js/popup_menu.js" type="text/javascript"></script>
<link rel="stylesheet" href="{VAR:baseurl}/automatweb/css/obj_tree.css" />


<style type='text/css'>
.fgtext_bad {
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
<table border="0" width="100%" cellpadding="3" cellspacing="0">
	<tr bgcolor="{VAR:header_bgcolor}">
		<!-- SUB: HEADER_icon -->
		<td class="{VAR:header_css_class}">&nbsp;</td>
		<!-- END SUB: HEADER_icon -->

		<!-- SUB: HEADER_name -->
		<td class="{VAR:header_css_class}">Nimi</td>
		<!-- END SUB: HEADER_name -->

		<!-- SUB: HEADER_size -->
		<td class="{VAR:header_css_class}">Suurus</td>
		<!-- END SUB: HEADER_size -->

		<!-- SUB: HEADER_class_id -->
		<td class="{VAR:header_css_class}">T&uuml;&uuml;p</td>
		<!-- END SUB: HEADER_class_id -->

		<!-- SUB: HEADER_modified -->
		<td class="{VAR:header_css_class}" align="right">Muutmise kuup&auml;ev</td>
		<!-- END SUB: HEADER_modified -->

		<!-- SUB: HEADER_modifiedby -->
		<td class="{VAR:header_css_class}" align="right">Muutja</td>
		<!-- END SUB: HEADER_modifiedby -->

		<!-- SUB: HEADER_change -->
		<td class="{VAR:header_css_class}" align="right" >Muuda</td>
		<!-- END SUB: HEADER_change -->

		<!-- SUB: HEADER_select -->
		<!-- SUB: HEADER_HAS_TOOLBAR -->
		<td class="{VAR:header_css_class}" align="right" >Vali</td>
		<!-- END SUB: HEADER_HAS_TOOLBAR -->

		<!-- SUB: HEADER_NO_TOOLBAR -->
		<td class="{VAR:header_css_class}" align="right" >Kustuta</td>
		<!-- END SUB: HEADER_NO_TOOLBAR -->
		<!-- END SUB: HEADER_select -->
	</tr>
	<!-- SUB: FILE -->
	<tr bgcolor="{VAR:bgcolor}">
		<!-- SUB: FILE_icon -->
		<td >{VAR:icon}</td>
		<!-- END SUB: FILE_icon -->

		<!-- SUB: FILE_name -->
		<td class="{VAR:css_class}"><a {VAR:target} title="{VAR:comment}" href='{VAR:show}'>{VAR:name}</a></td>
		<!-- END SUB: FILE_name -->

		<!-- SUB: FILE_size -->
		<td class="{VAR:css_class}">{VAR:size}</td>
		<!-- END SUB: FILE_size -->

		<!-- SUB: FILE_class_id -->
		<td class="{VAR:css_class}">{VAR:type}</td>
		<!-- END SUB: FILE_class_id -->

		<!-- SUB: FILE_modified -->
		<td class="{VAR:css_class}" align="right">{VAR:mod_date}</td>
		<!-- END SUB: FILE_modified -->

		<!-- SUB: FILE_modifiedby -->
		<td class="{VAR:css_class}" align="right">{VAR:modder}</td>
		<!-- END SUB: FILE_modifiedby -->

		<!-- SUB: FILE_change -->
		<td class="{VAR:css_class}" align="right">{VAR:act}</td>
		<!-- END SUB: FILE_change -->

		<!-- SUB: FILE_select -->
		<!-- SUB: HAS_TOOLBAR -->
		<td class="{VAR:css_class}" align="right">
			<!-- SUB: DELETE -->
			<input type="checkbox" name="sel[]" value="{VAR:oid}">
			<!-- END SUB: DELETE -->
		</td>
		<!-- END SUB: HAS_TOOLBAR -->

		<!-- SUB: NO_TOOLBAR -->
		<td class="{VAR:css_class}" align="right">
			{VAR:delete}
		</td>
		<!-- END SUB: NO_TOOLBAR -->
		<!-- END SUB: FILE_select -->
	</tr>
	<!-- END SUB: FILE -->
</table>

{VAR:reforb}
</form>
