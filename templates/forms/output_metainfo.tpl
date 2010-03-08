<br>
<form action='reforb.{VAR:ext}' method=post>
<table bgcolor="#CCCCCC" cellpadding=3 cellspacing=1 border=0>
<tr>
<td class="fcaption">{VAR:LC_FORMS_NAME}:</td>
<td class="fform"><input type='text' NAME='name' VALUE='{VAR:op_name}'></td>
</tr>
<tr>
<td class="fcaption">{VAR:LC_FORMS_COMMENT}:</td>
<td class="fform"><textarea NAME='comment' COLS=50 ROWS=5 wrap='soft'>{VAR:op_comment}</textarea></td>
</tr>
<tr>
<td class="fcaption">{VAR:LC_FORMS_CREATED}:</td>
<td class="fform">{VAR:created_by} @ {VAR:created}</td>
</tr>
<tr>
<td class="fcaption">{VAR:LC_FORMS_CHANGED}:</td>
<td class="fform">{VAR:modified_by} @ {VAR:modified}</td>
</tr>
<tr>
<td class="fcaption">{VAR:LC_FORMS_LOOKED}:</td>
<td class="fform">{VAR:views} {VAR:LC_FORMS_TIMES}</td>
</tr>
<tr>
<td class="fcaption" colspan=2><input class='small_button' type='submit' VALUE='{VAR:LC_FORMS_SAVE}'></td>
</tr>
</table>
{VAR:reforb}
</form>
