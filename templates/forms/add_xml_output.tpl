<form action='reforb{VAR:ext}' METHOD=post>

<table border="0" cellspacing="0" cellpadding="2" width="100%">
<tr>
<td class="aste01">

<table border="0" cellspacing="5" cellpadding="2">
<tr>

<td class="celltext" align="right">Alias:</td>
<td class="celltext"><input type='text' NAME='name' VALUE='{VAR:name}' class="formtext"></td>
</tr>
<tr>
<td class="celltext" align="right" valign="top">{VAR:LC_FORMS_COMMENT}:</td>
<td class="celltext"><textarea cols=50 rows=5 NAME=comment class="formtext">{VAR:comment}</textarea></td>
</tr>
<tr>
<td class="celltext" align="right" valign="top">{VAR:LC_FORMS_CHOOSE_FORMS}:</td>
<td class="celltext"><select name='forms[]' size="20" multiple class="formselect2">{VAR:forms}</select></td>
</tr>
<!-- SUB: admin -->
<tr>
<td colspan=2 class="celltext"><a href="{VAR:adminurl}">{VAR:LC_FORMS_MAKE_OUTPUT}</a></td>
</tr>
<!-- END SUB: admin -->
<tr>
<td></td>
<td class="celltext"><input class='formbutton' type='submit' VALUE='{VAR:LC_FORMS_SAVE}'></td>
</tr>
</table>
</td></tr></table>
{VAR:reforb}
</form>
