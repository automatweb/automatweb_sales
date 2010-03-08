<script language="javascript">
function box2(caption,url)
{
	var answer=confirm(caption);
	if (answer)
	{
		window.location=url
	}
}
</script>
<!-- SUB: DELETE -->
<input type="checkbox" name="check[]" value="{VAR:id}" />
<!-- END SUB: DELETE -->

<form name="topicform" method="POST" action="/reforb.{VAR:ext}">
<input type="hidden" name="act" value="" />



			{VAR:TABS}

   <!-- SUB: NEW_MSGS -->
	<font color="red"><!--{VAR:LC_MSGBOARD_NEW}">-->
	<!-- END SUB: NEW_MSGS -->






<table width="100%" border="0" cellspacing="0" cellpadding="3">
        <tr class="msgboardcolor3">
          <td class="msgboardhdr" nowrap height="24">{VAR:LC_MSGBOARD_TOPICS}</td>
          <td class="msgboardhdr" nowrap height="24" align="center">{VAR:LC_MSGBOARD_POSTS}</td>
          <td class="msgboardhdr" height="24" nowrap align="center">{VAR:LC_MSGBOARD_STARTED}</td>
          <td class="msgboardhdr" height="24" nowrap>{VAR:LC_MSGBOARD_LAST_POST}</td>
	  <td class="msgboardhdr" height="24" nowrap>&nbsp;</td>
        </tr>

		<!-- SUB: TOPIC_EVEN -->
        <tr class="msgboardcolor4">
          <td width="100%" class="msgboardrw2">{VAR:NEW_MSGS}&raquo; <a href="{VAR:threaded_topic_link}">{VAR:topic}</a></td>
          <td align="center" class="msgboardrw2">{VAR:cnt} </td>
          <td align="center" class="msgboardrw2">{VAR:created_date}</td>
          <td align="center" class="msgboardrw2" nowrap>{VAR:lastmessage}</td>
	  <td class="msgboardrw2" height="24" nowrap>{VAR:DEL_TOPIC}</td>
        </tr>
		<!-- END SUB: TOPIC_EVEN -->

		<!-- SUB: TOPIC_ODD -->
        <tr>
          <td width="100%" class="msgboardrw1">{VAR:NEW_MSGS}&raquo; <a href="{VAR:threaded_topic_link}">{VAR:topic}</a></td>
          <td align="center" class="msgboardrw1">{VAR:cnt} </td>
          <td align="center" class="msgboardrw1">{VAR:created_date}</td>
          <td align="center" class="msgboardrw1" nowrap>{VAR:lastmessage}</td>
	  <td class="msgboardrw1" height="24" nowrap>{VAR:DEL_TOPIC}</td>
        </tr>
		<!-- END SUB: TOPIC_ODD -->
      </table>






<img src="{VAR:baseurl}/img/trans.gif" width="1" height="3" border="0" alt="" /><br />
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr class="msgboardcolor3">

<!-- SUB: PAGES -->
<td height="21" align="left" class="textmiddle">&nbsp;&nbsp;
{VAR:LC_MSGBOARD_PAGES}:
				
<!-- SUB: PAGE -->
<a href='{VAR:pagelink}'>{VAR:linktext}</a>
<!-- END SUB: PAGE -->
<!-- SUB: SEL_PAGE -->
<a href='{VAR:pagelink}'><b>{VAR:linktext}</b></a>
<!-- END SUB: SEL_PAGE -->

</td>
<!-- END SUB: PAGES -->


<!-- SUB: actions -->
<td height="21" align="right" class="textmiddle">


<!--[ <a href="#" onClick="if (confirm('{VAR:LC_MSGBOARD_DELETE_SELECTED_CONFIRM}')) {document.topicform.act.value = 'delete'; document.topicform.submit()} ;return false;"><b>{VAR:LC_MSGBOARD_DELETE}</b></a> ] &nbsp;&nbsp;-->

<input type="submit" class='formbutton' name="delete" value="{VAR:LC_MSGBOARD_DELETE}" onClick="if (confirm('{VAR:LC_MSGBOARD_DELETE_SELECTED_CONFIRM}')) {document.topicform.act.value = 'delete'; document.topicform.submit()} ;return false;" />
<!-- SUB: TO_ARCHIVE -->

<!--[ <a href="#" onClick="if (confirm('{VAR:LC_MSGBOARD_ARCHIVE_SELECTED_CONFIRM}')) {document.topicform.act.value = 'archive'; document.topicform.submit()} ;return false;"><b>{VAR:LC_MSGBOARD_ARCHIVE}</b></a> ]&nbsp;&nbsp;-->

<input type="submit" class='formbutton' onClick="if (confirm('{VAR:LC_MSGBOARD_ARCHIVE_SELECTED_CONFIRM}')) {document.topicform.act.value = 'archive'; document.topicform.submit()} ;return false;" name="archive" value="{VAR:LC_MSGBOARD_ARCHIVE}" />
<!-- END SUB: TO_ARCHIVE -->

<!-- SUB: FROM_ARCHIVE -->

<!--[ <a href="#" onClick="if (confirm('{VAR:LC_MSGBOARD_RESTORE_SELECTED_CONFIRM}')) {document.topicform.act.value = 'activate'; document.topicform.submit()} ;return false;"><b>{VAR:LC_MSGBOARD_ACTIVATE}</b></a> ]&nbsp;&nbsp;-->

<input type="submit" onClick="if (confirm('{VAR:LC_MSGBOARD_RESTORE_SELECTED_CONFIRM}')) {document.topicform.act.value = 'activate'; document.topicform.submit()} ;return false;" class='formbutton' name="archive" value="{VAR:LC_MSGBOARD_ACTIVATE}" />
<!-- END SUB: FROM_ARCHIVE -->
{VAR:reforb}

</td>
<!-- END SUB: actions -->

			<!--
			<input type="submit" value=" Hinda " class="mboardtextsmall">
			<input type="hidden" name="action" value="submit_votes"></TD>
			-->
</tr>
</table>

</form>
