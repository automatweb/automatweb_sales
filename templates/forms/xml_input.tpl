<form action='reforb{VAR:ext}' METHOD=post>
<!-- SUB: admin -->
<span class="textsmall"><a href="{VAR:adminurl}">{VAR:LC_FORMS_ADMIN}</a></span>
<!-- END SUB: admin -->

<table border=0 cellspacing=0 cellpadding=0>
<tr><td class="aste01">

<table border=0 cellspacing=0 cellpadding=2>
<tr>

<td class="celltext" align="right">Alias:</td>
<td class="celltext"><input type='text' NAME='name' VALUE='{VAR:name}' class="formtext" size="40"></td>
</tr>
<tr>
<td class="celltext" align="right">{VAR:LC_FORM_COMM}:</td>
<td class="celltext"><textarea cols=40 rows=5 NAME=comment class="formtext">{VAR:comment}</textarea></td>
</tr>
<tr class="aste06">
<td colspan="2" class="celltext"><strong>{VAR:LC_FORMS_SMALLDS_FORMS}</strong></td>
</tr>
<!-- SUB: line -->
<tr>
<td class="celltext" align="center"><input type="radio" name="select" value="{VAR:oid}" {VAR:checked}></td>
<td class="celltext">&nbsp;{VAR:ename}</td>
</tr>
<!-- END SUB: line -->
<tr class="aste06">
<td colspan="2" class="celltext"><strong>{VAR:LC_FORMS_FORMCHAINS}</strong></td>
</tr>
<!-- SUB: line2 -->
<tr>
<td class="celltext" align="center"><input type="radio" name="select" value="{VAR:oid}" {VAR:checked}></td>
<td class="celltext">&nbsp;{VAR:ename}</td>
</tr>
<!-- END SUB: line2 -->
<tr>
<td></td>
<td class="celltext"><input class='formbutton' type='submit' VALUE='{VAR:LC_FORMS_SAVE}'></td>
</tr>
</table>
</td></tr></table>
{VAR:reforb}
</form>
