{VAR:TABS}
		
<table width="100%" border="0" cellspacing="0" cellpadding="5">
<form action="/reforb{VAR:ext}" METHOD=POST name="addpost">
<tr class="msgboardcolor3">

<td class="textmiddle" nowrap height="24"><b>{VAR:LC_MSGBOARD_NEW_SUBJECT}</b></td>

</tr>
<tr>
          <td width="100%" class="msgboardcolor4">
	  
	  
<table border="0" cellspacing="5" cellpadding="0">
	<tr>
		<td valign="middle" align="right" class="textmiddle">{VAR:LC_MSGBOARD_SUBJECT}:</td>
		<td><input type="text" name="topic" size="40" value='{VAR:name}'></td>
	</tr>

	<tr>
	<td valign="middle" align="right" class="textmiddle">{VAR:LC_MSGBOARD_NAME}:</td><td><input type="text" name="from" size="40" /></td>
	</tr>

	<tr>
		<td valign="top" align="right" class="textmiddle">{VAR:LC_MSGBOARD_DETAILS}:</td><td><textarea name="text" cols="40" rows="10">{VAR:comment}</textarea></td>
	</tr>
	<tr>
	<td></td>
		<td><input type="submit" class="formbutton" value='{VAR:LC_MSGBOARD_SAVE}' />
		<!--[ <a href="#" onClick="document.addpost.submit();"><b>{VAR:LC_MSGBOARD_SAVE}</b></a> ]&nbsp;&nbsp;-->
		</td>
	</tr>
</table>
{VAR:reforb}
</form>


	  </td>
</tr>
</table>
