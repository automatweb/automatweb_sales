<!-- SUB ERROR -->
{VAR:error_msg}
<!-- END SUB ERROR -->
<table width="100%" border="0" cellspacing="0" cellpadding="1">
<form method="post" action="reforb{VAR:ext}">
  <tr>
    <td align="right" class="text2">Nimi:</td>
    <td class="text2"><input type="text" size="50" NAME="name" value="{VAR:name}"></td>
  </tr>
  <tr>
    <td align="right" class="text2">E-mail:</td>
    <td class="text2"><input type="text" size="50" NAME="email" value="{VAR:mail}"></td>
  </tr>
  <tr>
    <td align="right" class="text2">Pealkiri:</td>
    <td class="text2"><input type="text" NAME="subj" VALUE='{VAR:subj}' size="50"></td>
  </tr>
  <tr>
    <td align="right" class="text2" valign="top">Sisu:</td>
    <td class="text2"><textarea name="comment" cols="50" rows="6" wrap="virtual">{VAR:comment}</textarea></td>

  </tr>
  <!-- SUB: reply -->
  <tr>
    <td align="right" class="text2">Vastus:</td>
    <td class="text2"><input type="checkbox" NAME="response" VALUE='1' {VAR:response}></td>
  </tr>
  <!-- END SUB: reply -->
  <!-- SUB: IMAGE_VERIFICATION -->
  <tr>
    <td align="right" class="textmiddle">{VAR:LC_MSGBOARD_IMAGE_VERIFICATION}:</td>
    <td class="textmiddle"><img src="{VAR:image_verification_url}" width="{VAR:image_verification_width}" height="{VAR:image_verification_height}" /><input type="text" name="ver_code" size="5" /></td>
  </tr>
  <!-- END SUB: IMAGE_VERIFICATION -->
  <tr>
    <td align="right" class="text2">Jäta nimi ja e-mail meelde:</td>
    <td class="text2"><input type="checkbox" NAME="remember_me" VALUE='1' {VAR:remember_me}></td>
  </tr>
  <tr>
    <td align="right" class="text2" valign="top">&nbsp;</td>
    <td class="text2"><b><input type="submit" class="text" value="Lisa kommentaar!" size="20"></b></td>
  </tr>
  {VAR:reforb}
</form>
</table>



