
<!-- SUB: DELETE -->
<!--
<br><a href="javascript:box2('Oled kindel, et soovid seda teemat 
kustutada?','comments.aw?action=delete_topic&id={VAR:topic_id}&forum_id={VAR:forum_id}')"><font size="1">[del]</font></a>
-->
<!-- END SUB: DELETE -->
			<!--1-->
            <table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#e2e2e2">
              <tr> 
                <td>
		{VAR:TABS}

					<!--4-->
                  <table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#3e5f94" height="1">
                    <tr> 
                      <td><img src="{VAR:baseurl}/img/trans.gif" width="435" height="1"></td>
                    </tr>
                  </table>
				  <!--end 4-->

				  <!--5-->
                  <table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#ebebeb" height="20">
				  <tr>
					<td width="80%">&nbsp;</td>
					<td width="1" bgcolor="#ffffff"><img src="{VAR:baseurl}/img/trans.gif" width="1" height="1"></td>
                      <td width="20%">&nbsp;&nbsp;<b class="text">Alustatud</b></td>
                    </tr>
					<tr><td colspan="3" bgcolor="#ffffff"><img src="{VAR:baseurl}/img/trans.gif" width="1" height="1"></td></tr>
                  </table>

				  <!--end 5-->




<!-- SUB: TOPIC -->

				<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#ebebeb">
	
				<tr> 
				<td width="3%" valign="middle">
				<!-- SUB: NEW_MSGS -->
				<font face="Arial" size="1" color="red">uus</font>&nbsp;
				<!-- END SUB: NEW_MSGS -->
				</td>
                <td width="77%" align="left"><span class="text"><b><a href="{VAR:threaded_topic_link}">{VAR:topic}</a></b></span>{VAR:DELETE}</td>
				 <td width="1" bgcolor="#ffffff"><img src="{VAR:baseurl}/img/trans.gif" width="1" height="1"></td>
				<td class="text" width="20%" align="left">&nbsp;&nbsp;{VAR:created_date}</td>
				</tr>
				<tr valign="top"> 
                <td colspan="4" height="2"><img src="{VAR:baseurl}/img/trans.gif" width="1" height="2"></td>
                </tr>
				</table>

				<table width="100%" border="0" cellspacing="0" cellpadding="0">
		    <!-- SUB: message -->
                    <tr valign="top" bgcolor="{VAR:color}"> 
                      <td class="text" width="60%">

				<table border="0" cellpadding="0" cellspacing="0"><tr>
	<td valign="top">{VAR:icons}</td>
	<td class="text">&nbsp;&nbsp;<a href="{VAR:open_link}">{VAR:subj}</a></td></tr></table>
					</td>
					<td width="1" bgcolor="#ffffff"><img src="{VAR:baseurl}/img/trans.gif" width="1" height="1"></td>
		      <td class="text" nowrap width="20%">&nbsp;&nbsp;{VAR:from}&nbsp;</td>
			  <td width="1" bgcolor="#ffffff"><img src="{VAR:baseurl}/img/trans.gif" width="1" height="1"></td>
		      <td class="text" width="20%">&nbsp;&nbsp;{VAR:time}</td>
                    </tr>
					 <!-- SUB: SHOW_COMMENT -->
					<tr>
				     <td colspan="5" class="text">
				     <i>
				     {VAR:comment}
				     </i>
				     </td>
				    </tr>
		    <!-- END SUB: SHOW_COMMENT -->

		    <!-- END SUB: message -->
		
                  </table>
		
<!-- END SUB: TOPIC -->


<TABLE width="100%" border="0" cellspacing="0" cellpadding="0">
			<TR>
			<TD align="left" class="text">

			<!-- SUB: PAGES -->
			Vali lehekülg:&nbsp;

			<!-- SUB: PAGE -->
			<a href='{VAR:pagelink}'>{VAR:linktext}</a>&nbsp;&nbsp;
			<!-- END SUB: PAGE -->
			<!-- SUB: SEL_PAGE -->
			<a href='{VAR:pagelink}'><b>&gt;{VAR:linktext}&lt;</b></a>&nbsp;&nbsp;
			<!-- END SUB: SEL_PAGE -->
		
			<!-- END SUB: PAGES -->


			</TD>
			<TD align="right">
			<!--
			<input type="submit" value=" Hinda " class="mboardtextsmall">
			<input type="hidden" name="action" value="submit_votes"></TD>
			-->&nbsp;
			</TR>
			</TABLE>

                  


                </td>
              </tr>
            </table>
			<!--end 1-->

			<br>


</form>

















