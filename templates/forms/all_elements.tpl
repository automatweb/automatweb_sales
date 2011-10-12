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
<td bgcolor=#ffffff valign=bottom align=left colspan={VAR:colspan} rowspan={VAR:rowspan}>
<!-- SUB: SOME_ELEMENTS -->

<table width=100% height=100% border=0 cellpadding="2" cellspacing="0">
<tr class="aste01">
<td align=left class='celltext' colspan=3>&nbsp;<input type='checkbox' name='chk[{VAR:row}][{VAR:col}]' value=1>&nbsp;{VAR:style_name}</td>
</tr>

<tr>
	<td bgcolor=#ffffff class='celltext'><b>{VAR:LC_FORMS_NAME}</b></td>
	<td bgcolor=#ffffff class='celltext'><b>{VAR:LC_FORMS_TYPE}</b></td>
	<td bgcolor=#ffffff class='celltext'><b>{VAR:LC_FORMS_TEXT}</b></td>
</tr>
<!-- SUB: ELEMENT -->
<tr>
	<td bgcolor=#ffffff class='celltext'>{VAR:el_name}</td>
	<td bgcolor=#ffffff class='celltext'>{VAR:el_type}</td>
	<td bgcolor=#ffffff class='celltext'>{VAR:el_text} &nbsp;&nbsp;<input type='checkbox' name='selel[]' value='{VAR:element_id}'></td>
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
<span class="celltext">

{VAR:LC_FORMS_CHOOSE_STYLE}:<br>
<select name='setstyle' class='formselect2'>{VAR:styles}</select><br>
<img src="{VAR:baseurl}/automatweb/images/trans.gif" border="0" width="1" height="10" alt=""><br>

{VAR:LC_FORMS_CHOOSE_CALALOGUE_WHERE_MOVE_ELEMENT}:<br>
<select name='setfolder' class='formselect2'>{VAR:folders}</select><br>
<img src="{VAR:baseurl}/automatweb/images/trans.gif" border="0" width="1" height="10" alt=""><br>

{VAR:LC_FORMS_CHOOSE_ELEMENT_TYPE_WHAT_ADD}:<br>
<select name='addel' class='formselect2'>{VAR:types}</select><br>
<img src="{VAR:baseurl}/automatweb/images/trans.gif" border="0" width="1" height="10" alt=""><br>

Vali sisestuse kontrollerid, mis valitud elementidele m&auml;&auml;ratakse:<br>
<select multiple name='add_entry_controllers[]' class='formselect2'>{VAR:controllers}</select><br>
<img src="{VAR:baseurl}/automatweb/images/trans.gif" border="0" width="1" height="10" alt=""><br>

Vali n&auml;itamise kontrollerid, mis valitud elementidele m&auml;&auml;ratakse:<br>
<select multiple name='add_show_controllers[]' class='formselect2'>{VAR:controllers}</select><br>
<img src="{VAR:baseurl}/automatweb/images/trans.gif" border="0" width="1" height="10" alt=""><br>

Vali listboksi kontrollerid, mis valitud elementidele m&auml;&auml;ratakse:<br>
<select multiple name='add_lb_controllers[]' class='formselect2'>{VAR:controllers}</select><br>
<img src="{VAR:baseurl}/automatweb/images/trans.gif" border="0" width="1" height="10" alt=""><br>

Vali v&auml;&auml;rtuse kontrollerid:<br>
<select name='add_value_controller' class='formselect2'>{VAR:controllers}</select><br>
<img src="{VAR:baseurl}/automatweb/images/trans.gif" border="0" width="1" height="10" alt=""><br>

Vali default v&auml;&auml;rtuse kontrollerid:<br>
<select name='add_def_value_controller' class='formselect2'>{VAR:controllers}</select><br>
<img src="{VAR:baseurl}/automatweb/images/trans.gif" border="0" width="1" height="10" alt=""><br>

<br>
<input type="checkbox" value="1" name="add_controllers"> &Auml;ra kustuta olemasolevaid kontrollereid<Br>
<br>
<input type='submit' value='{VAR:LC_FORMS_SAVE}' class='formbutton'>&nbsp;&nbsp;
<input type='submit' name='diliit' value='Kustuta' class='formbutton'>
{VAR:reforb}

</span>
</td></tr></table>
</td></tr>

</form>

</table>

<br>
