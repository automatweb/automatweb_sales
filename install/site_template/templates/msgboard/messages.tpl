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
<form method="post" name="commform" action="/reforb{VAR:ext}">


{VAR:TABS}


         <!-- SUB: NEW_MSGS -->
	*
	<!-- END SUB: NEW_MSGS -->

        <!-- SUB: READ_MSGS -->
       >>
        <!-- END SUB: READ_MSGS -->


<table width="100%" border="0" cellspacing="0" cellpadding="3">
<!-- SUB: TOPIC -->
        <tr class="msgboardcolor3">
          <td class="msgboardrw2" nowrap height="24"><b>{VAR:from}</b>  ({VAR:created})


	  </td>
	  <td align="right" class="msgboardrw2">
	  <!-- SUB: CHANGE_TOPIC -->
	<a href='{VAR:change_topic}'>Change</a>
	<!-- END SUB: CHANGE_TOPIC -->
</td>
        </tr>

        <tr class="msgboardcolor3">
          <td width="100%" class="msgboardrw2" colspan="2"><b>{VAR:topic}</b><br />{VAR:text}</td>
      </tr>


<!-- END SUB: TOPIC -->
</table>

<img src="{VAR:baseurl}/img/trans.gif" width="1" height="6" border="0" alt=""><br />

	<table width="100%" border="0" cellspacing="0" cellpadding="3">
	<!-- SUB: message -->
	<tr class="msgboardcolor4">
          <td width="100%" class="msgboardrw2">
	{VAR:new} <a href="mailto:{VAR:email}"><b>{VAR:from}</b></a> ({VAR:time})
	</td>
	<td class="msgboardrw2" align="right" nowrap>


		<!-- SUB: REPLY -->
		<a href="{VAR:reply_link}">{VAR:LC_MSGBOARD_REPLY}</a>
		<!-- END SUB: REPLY -->



		<!-- SUB: KUSTUTA -->

		<!--{VAR:LC_MSGBOARD_SELECT}: --><input type='checkbox' name='check[]' value='{VAR:id}'>

		<!-- END SUB: KUSTUTA -->



	  </td>
	</tr>
	<tr class="msgboardcolor4">
          <td width="100%" class="msgboardrw1" colspan="2">
		<b>{VAR:subj}</b><br />
		{VAR:comment}
	  </td>
	</tr>
	<tr><td width="100%" colspan="2"><img src="{VAR:baseurl}/img/trans.gif" width="1" height="1" border="0" alt="" /></td></tr>
	<!-- END SUB: message -->

	</table>


<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>

<!-- SUB: PAGES -->
<td height="21" align="left" class="textmiddle">

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
<td height="21" align="right"  class="textmiddle">

<!--[ <a href="#" onClick="if (confirm('{VAR:LC_MSGBOARD_DELETE_SELECTED}?')) {document.commform.submit()} ;return false;"><b>{VAR:LC_MSGBOARD_DELETE}</b></a> ]&nbsp;&nbsp;-->

<input type="submit" class='formbutton' value="{VAR:LC_MSGBOARD_DELETE}" onClick="if (confirm('{VAR:LC_MSGBOARD_DELETE_SELECTED}?')) {document.commform.submit()} ;return false;" />


{VAR:reforb}


</td>
<!-- END SUB: actions -->

			<!--
			<input type="submit" value=" Hinda " class="mboardtextsmall">
			<input type="hidden" name="action" value="submit_votes"></TD>
			-->
</tr>
</form>
</table>

<img src="{VAR:baseurl}/img/trans.gif" width="1" height="7" border="0" alt="" /><br />

<table width="100%" border="0" cellspacing="0" cellpadding="5">
<form action="/reforb{VAR:ext}" method="post" name="addpost">
<tr class="msgboardcolor3">

<td class="textmiddle" nowrap height="24"><b>{VAR:LC_MSGBOARD_ADD_NEW_COMM}</b></td>

</tr>
</table>
