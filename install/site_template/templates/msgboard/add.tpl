<table width="100%" border="0" cellspacing="0" cellpadding="5">
<tr><td class="msgboardcolor4">

<table border="0" cellspacing="5" cellpadding="0">
<form method="post" action="/reforb{VAR:ext}" name="addpost">
  <tr>
    <td align="right" class="textmiddle">{VAR:LC_MSGBOARD_NAME}:</td>
    <td class="textmiddle"><input type="text" size="40" NAME="name" value="{VAR:name}"></td>
  </tr>
  <tr>
    <td align="right" class="textmiddle">{VAR:LC_MSGBOARD_EMAIL}:</td>
    <td class="textmiddle"><input type="text" size="40" NAME="email" value="{VAR:mail}"></td>
  </tr>
  <tr>
    <td align="right" class="textmiddle">{VAR:LC_MSGBOARD_SUBJECT}:</td>
    <td class="textmiddle"><input type="text" NAME="subj" VALUE='{VAR:subj}' size="40"></td>
  </tr>
  <tr>
    
    <td class="textmiddle" colspan="2">{VAR:LC_MSGBOARD_COMMENTARY}:<br /><textarea name="comment" cols="40" rows="10" wrap="virtual">{VAR:comment}</textarea></td>

  </tr>
  <!-- SUB: reply -->
  <tr>
    <td align="right" class="textmiddle">{VAR:LC_MSGBOARD_REPLY}:</td>
    <td class="textmiddle"><input type="checkbox" name="response" value="0" {VAR:response}></td>
  </tr>
  <!-- END SUB: reply -->
  <tr>
    <td class="textmiddle" colspan="2">{VAR:LC_MSGBOARD_SET_COOKIE}:<input type="checkbox" name="remember_me" VALUE="1" {VAR:remember_me} /></td>
  </tr>
  <tr>
    <td align="right" class="textmiddle" valign="top">&nbsp;</td>
    <td class="textmiddle">
	<!--[ <a href="#" onClick="document.addpost.submit();"><b>SAVE</b></a> ]&nbsp;&nbsp;-->

	<input type="submit" class="formbutton" value="{VAR:LC_MSGBOARD_SAVE}" size="20" /></td>
  </tr>
  {VAR:reforb}
</form>
</table>

</td></tr></table>
