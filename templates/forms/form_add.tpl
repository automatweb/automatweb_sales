<form action="reforb{VAR:ext}" method="post">

<table border=0 cellspacing=0 cellpadding=0>
<tr><td class="aste01">

<table border=0 cellspacing=0 cellpadding=2>
<tr>
<td class="celltext" width="20%" align="right">{VAR:LC_FORM_NAME}:</td>
<td class="celltext"><input type='text' NAME='name' class="formtext" size="40"></td>
</tr>
<tr>
<td class="celltext" align="right">{VAR:LC_FORM_COMM}:</td>
<td class="celltext"><textarea cols=40 rows=5 NAME=comment></textarea></td>
</tr>
<tr>
<td class="celltext" align="right">{VAR:LC_FORM_BASE}</td>
<td class="celltext"><select name='base' class="formselect2">
{VAR:forms}
</select></td>
</tr>
<tr>
<td class="celltext" align="right">{VAR:LC_FORM_TYPE}:</td>
<td class="celltext"><select name="type" class="formselect2">
{VAR:types}
</select></td>
</tr>
<tr>
<td class="celltext" align="right">{VAR:LC_FORM_EL_DEFAULT_FOLDER}:</td>
<td class="celltext"><select name="el_default_folder" class="formselect2">
{VAR:el_default_folders}
</select></td>
</tr>
<tr>
<td>&nbsp;</td>
<td class="celltext"><input class='formbutton' type='submit' VALUE='{VAR:LC_FORMS_SAVE}'></td>
</tr>
</table>
</td></tr></TABLE>
{VAR:reforb}
</form>
