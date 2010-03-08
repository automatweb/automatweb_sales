
	{VAR:TABS}


 <table width="100%" border="0" cellspacing="0" cellpadding="5">
<tr class="msgboardcolor3">

<td class="textmiddle" nowrap height="24"><b>{VAR:LC_MSGBOARD_SEARCH_RESULTS}</b> - {VAR:count} </b>{VAR:LC_MSGBOARD_SEARCH_RESULTS2}</td>

</tr>
</table>


<!-- SUB: message -->
<table width="100%" border="0" cellspacing="0" cellpadding="3" >

	<tr><td colspan="6"><img src="{VAR:baseurl}/img/trans.gif" width="1" height="1" border="0" alt="" /></td></tr>
	<tr class="msgboardcolor4">
          <td width="100%" class="msgboardrw2">
	<!--{VAR:LC_MSGBOARD_NAME}:-->
	<a href="{VAR:open_link2}"><b>{VAR:subj}</b></a> -

	<a href='mailto:{VAR:email}'>{VAR:from}</a> ({VAR:time})

	</td>
	<td class="msgboardrw2" align="right" nowrap>



		<a href="{VAR:open_link2}">{VAR:LC_MSGBOARD_READ}</a>

	  </td>
	</tr>

</table>
<!-- END SUB: message -->

<img src="{VAR:baseurl}/img/trans.gif" width="1" height="3" border="0" alt="" /><br />




		<!-- SUB: TOPIC_EVEN -->
	<table  border="0" cellspacing="0" cellpadding="3">
        <tr class="msgboardcolor4">
          <td width="100%" class="msgboardrw1">{VAR:NEW_MSGS}<a href="{VAR:threaded_topic_link}">{VAR:topic}</a></td>
          <td align="center" class="msgboardrw1">{VAR:createdby} </td>
          <td align="center" class="msgboardrw1">{VAR:created_date}</td>
          <td align="center" class="msgboardrw1" nowrap>{VAR:lastmessage}</td>
	  <td class="msgboardrw1" height="24" nowrap>{VAR:DEL_TOPIC}</td>
        </tr>
	</table>
		<!-- END SUB: TOPIC_EVEN -->

		<!-- SUB: TOPIC_ODD -->
	<table  border="0" cellspacing="0" cellpadding="3">
        <tr>
          <td width="100%" class="msgboardrw1">{VAR:NEW_MSGS}<a href="{VAR:threaded_topic_link}">{VAR:topic}</a></td>
          <td align="center" class="msgboardrw1">{VAR:createdby} </td>
          <td align="center" class="msgboardrw1">{VAR:created_date}</td>
          <td align="center" class="msgboardrw1" nowrap>{VAR:lastmessage}</td>
	  <td class="msgboardrw1" height="24" nowrap>{VAR:DEL_TOPIC}</td>
        </tr>
	</table>
		<!-- END SUB: TOPIC_ODD -->


<!-- SUB: PAGES -->
<img src="{VAR:baseurl}/img/trans.gif" width="1" height="3" border="0" alt="" /><br />
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr class="msgboardcolor3">


<td height="21" align="left" class="textmiddle">
{VAR:LC_MSGBOARD_PAGES}:

<!-- SUB: PAGE -->
<a href='{VAR:url}'>{VAR:ltext}</a>
<!-- END SUB: PAGE -->
<!-- SUB: SEL_PAGE -->
<a href='{VAR:url}'><b>{VAR:ltext}</b></a>
<!-- END SUB: SEL_PAGE -->

</td>

			<!--
			<input type="submit" value=" Hinda " class="mboardtextsmall">
			<input type="hidden" name="action" value="submit_votes"></TD>
			-->
</tr>
</table>
<!-- END SUB: PAGES -->
