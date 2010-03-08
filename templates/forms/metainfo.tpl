<table width="100%" border=0 cellspacing=0 cellpadding=1>

<form action='reforb.{VAR:ext}' method=post name=ffrm>

<tr>
<td bgcolor="#FFFFFF">


<table width="100%" border=0 cellspacing=0 cellpadding=5>
<tr>
<td class="aste01">

<table border=0 cellspacing=1 cellpadding=3>
<tr>

<td class="celltext">{VAR:LC_FORMS_NAME}:</td>
<td class="celltext"><input type='text' NAME='name' VALUE='{VAR:form_name}' class="formtext"></td>
</tr>
<tr>
<td class="celltext" valign="top">{VAR:LC_FORMS_COMMENT}:</td>
<td class="celltext"><textarea NAME='comment' COLS=50 ROWS=5 wrap='soft' class="formtext">{VAR:form_comment}</textarea></td>
</tr>
<tr>
<td class="celltext">{VAR:LC_FORMS_CREATED}:</td>
<td class="celltext">{VAR:created}, {VAR:created_by}</td>
</tr>
<tr>
<td class="celltext">{VAR:LC_FORMS_CHANGED}:</td>
<td class="celltext">{VAR:modified}, {VAR:modified_by}</td>
</tr>
<tr>
<td class="celltext">{VAR:LC_FORMS_LOOKED}:</td>
<td class="celltext">{VAR:views} korda</td>
</tr>
<tr>
<td class="celltext">{VAR:LC_FORMS_FILLED}:</td>
<td class="celltext">{VAR:num_entries} korda</td>
</tr>
<tr>
<td class="celltext">{VAR:LC_FORMS_POSITION}:</td>
<td class="celltext">{VAR:position}</td>
</tr>
<tr>
<td></td>
<td class="celltext"><input class='formbutton' type='submit' VALUE='{VAR:LC_FORMS_SAVE}'></td>
</tr>
</table>
</td></tr></table>
</td></tr>

{VAR:reforb}
</form>

</table>

<br>
