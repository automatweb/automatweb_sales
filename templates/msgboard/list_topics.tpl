
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
<input type="checkbox" name="check[]" value="{VAR:id}">
<!-- END SUB: DELETE -->
<form name="topicform" method="POST" action="reforb.{VAR:ext}">

{VAR:TABS}



			<!--tabelshadow-->
			<table width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td width="1" class="tableshadow"><IMG SRC="{VAR:baseurl}/automatweb/images/trans.gif" WIDTH="1" HEIGHT="1" BORDER=0 ALT=""></td>
					<td class="tableshadow"><IMG SRC="{VAR:baseurl}/automatweb/images/trans.gif" WIDTH="1" HEIGHT="1" BORDER=0 ALT=""><br>
						<!--tabelsisu-->
						<table width="100%" cellspacing="0" cellpadding="0">
							<tr>
								<td class="tableinside">


<table border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
<!-- SUB: actions -->
<td height="29">

<!--<IMG
SRC="{VAR:baseurl}/automatweb/images/trans.gif" WIDTH="4" HEIGHT="1" BORDER=0 ALT=""><a href="{VAR:add_link}"
onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('new','','{VAR:baseurl}/automatweb/images/blue/awicons/new_over.gif',1)"><img
name="new" alt="Lisa" border="0" SRC="{VAR:baseurl}/automatweb/images/blue/awicons/new.gif" width="25" height="25"></a>--><!--<IMG
SRC="{VAR:baseurl}/automatweb/images/trans.gif" WIDTH="4" HEIGHT="1" BORDER=0 ALT=""><a
href="javascript:ed_rep()" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('edit','','{VAR:baseurl}/automatweb/images/blue/awicons/edit_over.gif',1)"><img name="edit" alt="Muuda" border="0" SRC="{VAR:baseurl}/automatweb/images/blue/awicons/edit.gif" width="25" height="25"></a>--><IMG SRC="{VAR:baseurl}/images/trans.gif" WIDTH="4" HEIGHT="1" BORDER=0 ALT=""><a
href="this.document.topicform.submit();" onClick="if (confirm('Oled kindel, et soovid valitud teemad kustutada?')) {document.topicform.submit()} ;return false;"
onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('delete','','{VAR:baseurl}/automatweb/images/blue/awicons/delete_over.gif',1)"><img name="delete" alt="Kustuta valitud teemad" border="0" SRC="{VAR:baseurl}/automatweb/images/blue/awicons/delete.gif" width="25" height="25"></a></td>

{VAR:reforb}

<!-- END SUB: actions -->


<!-- SUB: PAGES -->
<td valign="bottom" align="right" height="29">
											
											<table border=0 cellpadding=0 cellspacing=0>
													<tr>
													   <td class="celltext">Vali lehekülg:&nbsp;</td>

														<!-- SUB: PAGE -->
														<td class="tab"><IMG SRC="images/blue/tab_left_begin.gif" WIDTH="8" HEIGHT="20" BORDER=0 ALT=""></td>
														<td nowrap background="{VAR:baseurl}/automatweb/images/blue/tab_taust.gif" class="tab" valign="bottom"><a href='{VAR:pagelink}'>{VAR:linktext}</a></td><td class="tab"><IMG SRC="images/blue/tab_right.gif" WIDTH="6" HEIGHT="20" BORDER=0 ALT=""></td>
														<!-- END SUB: PAGE -->

														<!-- SUB: SEL_PAGE -->
														<td class="tabsel"><IMG SRC="images/blue/tab_left_begin.gif" WIDTH="8" HEIGHT="20" BORDER=0 ALT=""></td>
														<td nowrap background="{VAR:baseurl}/automatweb/images/blue/tab_taust.gif" class="tabsel" valign="bottom"><a href='{VAR:pagelink}'><b>{VAR:linktext}</b></a></td><td class="tabsel"><IMG SRC="images/blue/tab_right.gif" WIDTH="6" HEIGHT="20" BORDER=0 ALT=""></td>
														<!-- END SUB: SEL_PAGE -->
														<td class="celltext">&nbsp;</td>
													</tr>
												</table>

</td>
<!-- END SUB: PAGES -->
</tr></table>

</td>
</tr>
</table>

</td>
</tr>
</table>



<table border="0" cellspacing="0" cellpadding="0" width=100%>
<tr>
<td class="aste02">



<table border="0" cellspacing="1" cellpadding="2" width="100%">


  <tr class="aste05">
	<td class="celltext" width="2%">&nbsp;</td>
	<td class="celltext" width="45%">Pealkiri</td>
	<td class="celltext" width="11%" align="center">Vastuseid</td>
	
    <td class="celltext" width="10%" align="center">Postitas</td>
	
	<td class="celltext" width="15%" align="center">Alustatud</td>
	<td class="celltext" width="15%" align="center">Vastatud</td>
	<td class="celltext" width="2%">&nbsp;</td>
  </tr>

<!-- SUB: TOPIC_EVEN -->
  <tr valign="top" clasS="aste00"> 
	<td width="6" class="celltext">

	<!-- SUB: NEW_MSGS -->
    <font face="Arial" size="1" color="red">uus</font>
	<!-- END SUB: NEW_MSGS -->

	</td>
    <td class="text" valign="middle"><a href="{VAR:threaded_topic_link2}">{VAR:topic}</a></td>
	<td class="celltext" align="center" valign="middle">{VAR:cnt}</td>
		     
    <td class="celltext" valign="middle">{VAR:createdby}</td>
		      
	<td nowrap align="center" valign="middle" class="celltext">{VAR:created_date}</td>
	<td nowrap align="center" valign="middle" class="celltext">{VAR:lastmessage}</td>
    <td>{VAR:DEL_TOPIC}
    <!--<div align="center" class="text">{VAR:rate}</div>-->
	</td>
  </tr>
                   
<!-- END SUB: TOPIC_EVEN -->


<!-- SUB: TOPIC_ODD -->									
  <tr valign="top" class="aste00"> 
	<td class="celltext" width="6">{VAR:NEW_MSGS}</td>
	<td class="text" valign="middle"><a 
href="{VAR:threaded_topic_link2}">{VAR:topic}</a></td>

	<td class="celltext" align="center" valign="middle">{VAR:cnt}</td>
	
	<td class="celltext" valign="middle">{VAR:from}</td>
	
	<td nowrap align="center" valign="middle" class="celltext">{VAR:created_date}</td>
	<td nowrap align="center" valign="middle"  class="celltext">{VAR:lastmessage}</td>
    <td>{VAR:DEL_TOPIC}
	<!-- <div align="center" class="text">{VAR:rate}</div> -->
    </td>
  </tr>
  
<!-- END SUB: TOPIC_ODD -->


<!--<tr><td colspan="7" class="text" align="right">
<input type="submit" class='doc_button' value="Kustuta valitud teemad" onClick="if (confirm('Oled kindel, et soovid valitud teemad kustutada?')) {document.topicform.submit()} ;return false;">
</td></tr>-->

</table>


</td>
</tr>
</table>


<table border="0" cellspacing="0" cellpadding="0" width="100%">
<tr clasS="aste01">

{VAR:actions}

{VAR:PAGES}

</tr>
<tr clasS="aste00"><td colspan="2"><IMG SRC="{VAR:baseurl}/automatweb/images/trans.gif" WIDTH="1" HEIGHT="1" BORDER=0 ALT=""></td></tr>
</table>
			<!--
			<input type="submit" value=" Hinda " class="mboardtextsmall">
			<input type="hidden" name="action" value="submit_votes"></TD>
			-->
			
</form>
