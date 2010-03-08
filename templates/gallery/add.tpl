<form action = 'reforb.{VAR:ext}' method=post>
<table border="0" cellpadding="5" cellspacing="0">
<tr class="aste01">
<td class="celltext">{VAR:LC_GALLERY_NAME}:</td><td class="aste01"><input type='text' NAME='name' VALUE='{VAR:name}' class="formtext"></td>
</tr>
<tr class="aste01">
<td class="celltext">{VAR:LC_GALLERY_COMM}:</td><td class="aste01"><input type='text' NAME='comment' VALUE='{VAR:comment}' class="formtext"></td>
</tr>
<!-- SUB: CHANGE -->
<tr  class="aste05">
<td class="celltext" colspan=2><a href='{VAR:content}'>{VAR:LC_GALLERY_CONTENT}</a></td>
</tr>
<!-- END SUB: CHANGE -->
<tr>
<td class="celltext" colspan=2><input class='formbutton' type='submit' VALUE='{VAR:LC_GALLERY_SAVE}'></td>
</tr>
</table>
{VAR:reforb}
</form>
