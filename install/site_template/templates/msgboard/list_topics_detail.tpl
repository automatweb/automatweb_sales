<!-- SUB: DELETE -->
<input type="checkbox" name="check[]" value="{VAR:id}" />
<!-- END SUB: DELETE -->


		{VAR:TABS}

	<!-- SUB: NEW_MSGS -->
	<font color="red"><!--{VAR:LC_MSGBOARD_NEW}-->
	<!-- END SUB: NEW_MSGS -->

	<table width="100%" border="0" cellspacing="0" cellpadding="3">
	  <tr class="msgboardcolor3">
          <td class="msgboardhdr" nowrap height="24">{VAR:LC_MSGBOARD_TOPICS}</td>
          <td class="msgboardhdr" nowrap height="24" align="center">&nbsp;</td>
          <td class="msgboardhdr" height="24" nowrap align="center">{VAR:LC_MSGBOARD_POSTS}</td>
          <td class="msgboardhdr" height="24" nowrap>&nbsp;</td>
	  <td class="msgboardhdr" height="24" nowrap>{VAR:LC_MSGBOARD_STARTED}</td>
	    <td class="msgboardhdr" height="24" nowrap>{VAR:LC_MSGBOARD_LAST_POST}</td>
		  <td class="msgboardhdr" height="24" nowrap>&nbsp;</td>
        </tr>

		<tr><td colspan="6"><img src="{VAR:baseurl}/img/trans.gif" width="1" height="1" border="0" alt="" /></td></tr>
	 
	 <!-- SUB: TOPIC -->
	 <tr  class="msgboardcolor4">
          <td width="100%" class="msgboardrw1" colspan="2">{VAR:NEW_MSGS}&raquo; <a href="{VAR:threaded_topic_link}">{VAR:topic}</a></td>
          <td align="center" class="msgboardrw1">{VAR:cnt} </td>
	  <td align="center" class="msgboardrw1">{VAR:createdby} </td>
          <td align="center" class="msgboardrw1">{VAR:created_date}</td>
          <td align="center" class="msgboardrw1" nowrap>{VAR:lastmessage}</td>
	  <td class="msgboardrw1" height="24" nowrap>{VAR:DEL_TOPIC}</td>
        </tr>
	<!-- SUB: message -->
	<tr  class="msgboardcolor4">
		  <td  class="msgboardrw1" align="right">{VAR:icons}</td>
          <td width="100%" class="msgboardrw1"><a href="{VAR:open_link}">{VAR:subj}</a></td>
          <td align="center" class="msgboardrw1"></td>
		 <td align="center" class="msgboardrw1">{VAR:from} </td>
          <td align="center" class="msgboardrw1"></td>
          <td align="center" class="msgboardrw1" nowrap>{VAR:time}</td>
		 <td class="msgboardrw1" height="24" nowrap>{VAR:DEL_TOPIC}</td>
        </tr>


			<!-- SUB: SHOW_COMMENT -->
			<tr class="msgboardcolor4">
			<td valign="top"></td>
			<td class="msgboardrw2" colspan="6">{VAR:comment}</td>
			</tr>

			<!-- END SUB: SHOW_COMMENT -->

	 


	  </td>
	</tr>

	<!-- END SUB: message -->
	<tr><td colspan="6"><img src="{VAR:baseurl}/img/trans.gif" width="1" height="1" border="0" alt="" /></td></tr>
	<!-- END SUB: TOPIC -->
	</table>










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

<input type="submit" class='formbutton' name="archive" value="{VAR:LC_MSGBOARD_ARCHIVE}" onClick="if (confirm('{VAR:LC_MSGBOARD_ARCHIVE_SELECTED_CONFIRM}')) {document.topicform.act.value = 'archive'; document.topicform.submit()} ;return false;" />

<!-- END SUB: TO_ARCHIVE -->

<!-- SUB: FROM_ARCHIVE -->

<!--[ <a href="#" onClick="if (confirm('{VAR:LC_MSGBOARD_RESTORE_SELECTED_CONFIRM}')) {document.topicform.act.value = 'activate'; document.topicform.submit()} ;return false;"><b>{VAR:LC_MSGBOARD_ACTIVATE}</b></a> ]&nbsp;&nbsp;-->

<input type="submit" class='formbutton' name="archive" value="{VAR:LC_MSGBOARD_ACTIVATE}" onClick="if (confirm('{VAR:LC_MSGBOARD_RESTORE_SELECTED_CONFIRM}')) {document.topicform.act.value = 'activate'; document.topicform.submit()} ;return false;" />
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

