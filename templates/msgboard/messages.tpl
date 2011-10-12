
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


{VAR:TABS}

					
<table width="100%" cellpadding="1" cellspacing="0" border="0">
<tr><td bgcolor="#FFFFFF">

<table width="100%" cellpadding="10" cellspacing="0" border="0">
<!-- SUB: TOPIC -->

<tr class="aste05"> 
<td class="aste05">
<span class="text"><a href="{VAR:topic_link}"><b>{VAR:topic}</b></a></span><br>

<span class="celltext">Autor: <b>{VAR:from}</b>  ({VAR:created})</span><br>


</td>
<td valign="top" align="right" class="aste05">
<!-- SUB: CHANGE_TOPIC -->
<a href='{VAR:change_topic}'><img src="{VAR:baseurl}/automatweb/images/blue/obj_edit.gif" border="0" alt="Muuda"></a>
<!-- END SUB: CHANGE_TOPIC -->

<!-- SUB: DELETE -->
<a href="javascript:box2('Oled kindel, et soovid seda teemat
kustutada?','{VAR:del_topic}')"><img src="{VAR:baseurl}/automatweb/images/blue/obj_delete.gif" border="0" alt="Kustuta"></a>
<!-- END SUB: DELETE -->
<br>

{VAR:rated}
</td>
</tr>
<tr>
<td class="text">
{VAR:text}<br>

</td>
</tr>
</table>

</td></tr>
<form method="POST" name="commform" action="reforb{VAR:ext}">
</table>




<!--<tr> 
<td bgcolor="#ECECEC" class="textsmall"><img
src="/img/new/nool_hall.gif">&nbsp;&nbsp;<a href="#comments">Loe selle teema arvamusi</a>&nbsp;&nbsp;&nbsp;</td>
 &nbsp;
</td>
</tr>-->

<!--jooneke
<tr><td colspan="2" align="right"><img src="{VAR:baseurl}/img/forum_joon2.gif" border="0" width="100%" height="2" alt=""></td></tr>-->
							

<!-- END SUB: TOPIC -->








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

<td height="29">

<IMG SRC="{VAR:baseurl}/images/trans.gif" WIDTH="4" HEIGHT="1" BORDER=0 ALT=""><a
href="javascript:this.document.commform.submit();" onClick="if (confirm('Kustutada valitud kommentaarid?')) {document.commform.submit()} ;return false;"
onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('delete','','{VAR:baseurl}/automatweb/images/blue/awicons/delete_over.gif',1)"><img name="delete" alt="Kustuta valitud teemad" border="0" SRC="{VAR:baseurl}/automatweb/images/blue/awicons/delete.gif" width="25" height="25"></a></td>

{VAR:reforb}




<!-- SUB: PAGES -->
<td valign="bottom" align="right" height="29">
											
											<table border=0 cellpadding=0 cellspacing=0>
													<tr>
													   <td class="celltext">Vali lehekülg:&nbsp;</td>

														<!-- SUB: PAGE -->
														<td class="tab"><IMG SRC="images/blue/tab_left_begin.gif" WIDTH="8" HEIGHT="20" BORDER=0 ALT=""></td>
														<td nowrap background="{VAR:baseurl}/automatweb/images/blue/tab_taust.gif" class="tab" valign="bottom"><a href='/comments{VAR:ext}?action=topics&page={VAR:pagenum}&forum_id={VAR:forum_id}'>{VAR:ltext}</a></td><td class="tab"><IMG SRC="images/blue/tab_right.gif" WIDTH="6" HEIGHT="20" BORDER=0 ALT=""></td>
														<!-- END SUB: PAGE -->

														<!-- SUB: SEL_PAGE -->
														<td class="tabsel"><IMG SRC="images/blue/tab_left_begin.gif" WIDTH="8" HEIGHT="20" BORDER=0 ALT=""></td>
														<td nowrap background="{VAR:baseurl}/automatweb/images/blue/tab_taust.gif" class="tabsel" valign="bottom"><a href='/comments{VAR:ext}?action=topics&page={VAR:pagenum}&forum_id={VAR:forum_id}'><b>{VAR:linktext}</b></a></td><td class="tabsel"><IMG SRC="images/blue/tab_right.gif" WIDTH="6" HEIGHT="20" BORDER=0 ALT=""></td>
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






<TABLE width="100%" border="0" cellspacing="0" cellpadding="0">

<tr> 
<!--<td height="18" align="left" class="text">
Sorteeri <a href="{VAR:threaded_link}"><b>VASTUSTE</b></a> või <a href="{VAR:flat_link}"><b>AJA</b></a> järgi</span><br>
</td>-->

			<TD align="right" class="textesileht">



			</TD>
			<!--<TD align="right">
			<input type="submit" value=" Hinda " class="mboardtextsmall">
			<input type="hidden" name="action" value="submit_votes">
			&nbsp;
			</td>-->
			</TR>
			</TABLE>

                  


                </td>
              </tr>
            </table>
			<!--end 1-->




<a name="comments"></a>

<!--begin komment-->

<!-- SUB: message -->
<table width=100% border=0 cellpadding=0 cellspacing=0 class="text">
<tr>
<td width=1><img src='{VAR:baseurl}/img/trans.gif' width="{VAR:level}" height="1" alt="" border="0"></td>
<td>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="text">
  <tr>
    <td class="text" bgcolor="#cCcCcC"><!--<a name="c{VAR:id}" style="text-decoration: none;">-->Kes: <a href="mailto:{VAR:email}"><b>{VAR:from}</b></a> @ {VAR:time}</td>
    </tr>
  <tr>
    <td height="18" valign="top" class="text" bgcolor="{VAR:color}"><b>{VAR:subj}</b></td>
  </tr>
  <tr>
    <td bgcolor="{VAR:color}"><span class="text">{VAR:comment}</span></td>
	</tr>
	<tr>
<td align="right" height="18" bgcolor="{VAR:color}"><img src="/img/mboard_nool_hall.gif">&nbsp;<a href="{VAR:reply_link}"><b>Vasta</b></a>
		<!-- SUB: KUSTUTA -->
		<img src="/img/mboard_nool_hall.gif">&nbsp;<b>Vali:</b> <input type='checkbox' name='check[]' value='{VAR:id}'>
		<!-- END SUB: KUSTUTA -->
		</td>
</tr>
</table>
</td>
</tr>

</table>
<!-- END SUB: message -->

<!-- SUB: actions -->
<table width="100%" border="0" cellspacing="0" cellpadding="1">
<tr>
<td align='right'>
<input type="submit" class='doc_button' value="Kustuta valitud kommentaarid" onClick="if (confirm('Kustutada valitud kommentaarid?')) {document.commform.submit()} ;return false;">
{VAR:reforb}
</td>
</tr>
</table>
<!-- END SUB: actions -->







<table width="100%" border="0" cellspacing="0" cellpadding="1">
<tr>
<td class="text">


{VAR:PAGES}




</td>
</tr>
</table>

</form>

<table width="100%" border="0" cellspacing="0" cellpadding="2">
  <tr>
    <td bgcolor="#ECECEC" class="text"><b>Uus</b></td>
	</tr>
</table>

