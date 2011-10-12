<table width="100%" border=0 cellspacing=0 cellpadding=1>

<form action='reforb{VAR:ext}' method=POST>

<tr>
<td bgcolor="#FFFFFF">

<table width="100%" border=0 cellspacing=0 cellpadding=5>
<tr>
<td class="aste01">



<table cellpadding=1 cellspacing=0>
<tr class="aste05">
<td>&nbsp;</td>
<!-- SUB: HE -->
<td align="center">{VAR:col1}</td>
<!-- END SUB: HE -->
</tr>
<!-- SUB: LINE -->
<tr class="aste05">
<td>{VAR:row1}</td>
<!-- SUB: COL -->
<td bgcolor=#ffffff align=left valign="top" colspan={VAR:colspan} rowspan={VAR:rowspan}>
<!-- SUB: SOME_ELEMENTS -->

<table width=100% height=100% border=1 cellpadding="2" cellspacing="0">

<tr>
	<td bgcolor=#ffffff class='celltext'><b>{VAR:LC_FORMS_NAME}</b></td>
	<td bgcolor=#ffffff class='celltext'><b>{VAR:LC_FORMS_TYPE}</b></td>
	<td bgcolor=#ffffff class='celltext'><b>Change</b></td>
</tr>

<!-- SUB: ELEMENT -->
<tr>
	<td bgcolor=#ffffff valign="top" class='celltext'>{VAR:el_name}</td>
	<td bgcolor=#ffffff valign="top" class='celltext'>{VAR:el_type}</td>
	<td bgcolor=#ffffff valign="top" class='celltext'>
		<b>Metadata</b><Br>
		<!-- SUB: METADATA -->
			<input size="15" type="text" name="meta[{VAR:row}][{VAR:col}][{VAR:index}][idx_{VAR:meta_idx}][key]" size="15" value="{VAR:meta_idx}"> <input type="text" name="meta[{VAR:row}][{VAR:col}][{VAR:index}][idx_{VAR:meta_idx}][value]" size="15" value="{VAR:meta}"> <Br>
		<!-- END SUB: METADATA -->
		<!-- SUB: IS_TEXTBOX -->
		<b>Tekstboksi pikkus:</b><br>
		<input type="text" name="textbox[{VAR:row}][{VAR:col}][{VAR:index}]" value="{VAR:length}"><br>
		<!-- END SUB: IS_TEXTBOX -->
		<!-- SUB: IS_RADIO -->
		<b>Raadionupu v&auml;&auml;rtus:</b><br>
		<input type="text" name="radio[{VAR:row}][{VAR:col}][{VAR:index}]" value="{VAR:value}"><br>
		<!-- END SUB: IS_RADIO -->
		<!-- SUB: IS_CHECK -->
		<b>Checkboxi v&auml;&auml;rtus</b><br>
		<input type="text" name="checkb[{VAR:row}][{VAR:col}][{VAR:index}]" value="{VAR:value}"><br>
		<!-- END SUB: IS_CHECK -->
		<b>Tab order:</b> <br>
		<input type="text" name="taborder[{VAR:row}][{VAR:col}][{VAR:index}]" value="{VAR:taborder}"><br>
		<b>CSS stiil:</b> <br>
		<select name="css_style[{VAR:row}][{VAR:col}][{VAR:index}]">{VAR:css_style}</select><br>
	</td>
</tr>

<!-- END SUB: ELEMENT -->
</table>
<!-- END SUB: SOME_ELEMENTS -->
</td>
<!-- END SUB: COL -->
</tr>
<!-- END SUB: LINE -->
</table>
<img src="{VAR:baseurl}/autoamtweb/images/trans.gif" border="0" width="1" height="10" alt=""><br>

<input type='submit' value='{VAR:LC_FORMS_SAVE}' class='formbutton'>&nbsp;&nbsp;
{VAR:reforb}

</span>
</td></tr></table>
</td></tr>

</form>

</table>

<br>
