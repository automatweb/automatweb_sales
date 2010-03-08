{VAR:TABS}


<table width="100%" border="0" cellspacing="0" cellpadding="5">
<tr class="msgboardcolor3">

<td class="textmiddle" nowrap height="24"><b>{VAR:LC_MSGBOARD_SEARCH}</b></td>

</tr>
<tr class="msgboardcolor4">
          <td width="100%" class="msgboardrw1">


<table border="0" cellspacing="5" cellpadding="0">
	<form method="get" action="/index.{VAR:ext}" name="postsearch">
	<tr>
		<td align="right" class="textmiddle">{VAR:LC_MSGBOARD_SEARCH_NAME}:&nbsp;</td>
		<td><input type="msgboardtext" name="from" size="25" /></td>
	</tr>

	<tr>
		<td align="right" class="textmiddle">{VAR:LC_MSGBOARD_SEARCH_EMAIL}:&nbsp;</td>
		<td><input type="msgboardtext" NAME="email" size="25" /></td>
	</tr>

	<tr>
		<td align="right" class="textmiddle">{VAR:LC_MSGBOARD_SEARCH_SUBJECT}:&nbsp;</td>
		<td><input type="msgboardtext" NAME="subj" size="25" /></td>
	</tr>

	<tr>
		<td align="right" valign="top" class="textmiddle">{VAR:LC_MSGBOARD_SEARCH_CONTENT}:&nbsp;</td>
		<td valign="top"><textarea cols="30" name="comment" rows="8" wrap="virtual"></textarea></td>
	</tr>
	<tr>
		<td align="right" class="textmiddle">{VAR:LC_MSGBOARD_SEARCH_ARCHIVE}:&nbsp;</td>
		<td><input type="checkbox" name="in_archive" value="1" /></td>
	</tr>
	<tr>
	    <td></td>
		<td class="textmiddle">
		<!--[ <a href="#" onClick="document.postsearch.submit();"><b>SEARCH</b></a> ]&nbsp;&nbsp;-->
			<input type="submit" value="{VAR:LC_MSGBOARD_SEARCH_BTN}" class="formbutton" />
	{VAR:reforb}
		</td>
	</tr>


	</form>
</table>


	  </td>
</tr>
</table>
