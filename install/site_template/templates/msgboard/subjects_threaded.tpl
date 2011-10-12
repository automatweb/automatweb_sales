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
         
        <!-- END SUB: READ_MSGS -->



<table width="100%" border="0" cellspacing="0" cellpadding="3">
<!-- SUB: TOPIC -->
        <tr class="msgboardcolor3">
          <td class="textmiddle" nowrap height="24"><b>{VAR:from}</b>  ({VAR:created})


	  </td>
        </tr>

        <tr class="msgboardcolor3">
          <td width="100%" class="textmiddle"><b>{VAR:topic}</b><br />{VAR:text}</td>
      </tr>


<!-- END SUB: TOPIC -->
</table>


<img src="{VAR:baseurl}/img/trans.gif" width="1" height="3" border="0" alt="" /><br />




<!-- SUB: message -->
<table width=100% border=0 cellpadding=0 cellspacing=0>
<tr>
<td width=1><img src='{VAR:baseurl}/img/trans.gif' width="{VAR:level}" height="1" alt="" border="0"></td>

<td>


	<table width="100%" border="0" cellspacing="0" cellpadding="3">
	<tr class="msgboardcolor4">
	  <td  class="rw1" align="right">{VAR:icons}</td>
          <td width="100%" class="msgboardrw1">{VAR:new} <a href="{VAR:open_link2}">{VAR:subj}</a></td>
          <td align="center" class="msgboardrw1"></td>
	  <td align="center" class="msgboardrw1">{VAR:from} </td>
          <td align="center" class="msgboardrw1"></td>
          <td align="center" class="msgboardrw1" nowrap>{VAR:time}</td>
	  <td class="msgboardrw1" height="24" nowrap>
	  <!-- SUB: KUSTUTA -->
	<input type="checkbox" name="check[]" value="{VAR:id}" />
		<!-- END SUB: KUSTUTA -->
		</td>
        </tr>
	</table>

</td></tr></table>
<img src='{VAR:baseurl}/img/trans.gif' width="1" height="7" alt="" border="0" /><br />
<!-- END SUB: message -->

<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>

<!-- SUB: PAGES -->
<td height="21" align="left" class="msgboardrw2">
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
<td height="21" align="right" valign="middle" class="msgboardrw2">


<!--[ <a href="#" onClick="if (confirm('{VAR:LC_MSGBOARD_DELETE_SELECTED}?')) {document.commform.submit()} ;return false;"><b>{VAR:LC_MSGBOARD_DELETE}</b></a> ]&nbsp;&nbsp;-->

<input type="submit" class="formbutton" value="{VAR:LC_MSGBOARD_DELETE}" onClick="if (confirm('{VAR:LC_MSGBOARD_DELETE_SELECTED}?')) {document.commform.submit()} ;return false;" />




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
