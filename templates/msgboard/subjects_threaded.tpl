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
<!-- SUB: NEW_MSG -->
<small><font color='red'>*</font></small>
<!-- END SUB: NEW_MSG -->







			<!--1-->
            <table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#e2e2e2">
              <tr> 
                <td>

					<!--2-->
			{VAR:TABS}

					<!--4-->
                  <table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#3e5f94" height="1">
                    <tr> 
                      <td><img src="{VAR:baseurl}/img/trans.gif" width="435" height="1"></td>
                    </tr>
                  </table>
				  <!--end 4-->

<table width="100%" cellpadding="10" cellspacing="0" border="0">

<!-- SUB: TOPIC -->
<tr> 
<td bgcolor="#ECECEC" class="text">
<a href="{VAR:topic_link}"><b>{VAR:topic}</b></a><br>
Autor: <b>{VAR:from}</b>  ({VAR:created})<br>
<img src="{VAR:baseurl}/img/trans.gif" border="0" width="1" height="10" alt=""><br>
{VAR:text}
</td>
</tr>
<tr>
<td>
{VAR:rated}
</td>
</tr>


<!--<tr><td bgcolor="#ECECEC" class="text"><img
src="/img/new/nool_hall.gif">&nbsp;&nbsp;<a href="#comments">Loe selle teema arvamusi</a>&nbsp;&nbsp;&nbsp;Hinne: {VAR:rate}&nbsp;</td></tr>-->

<!--jooneke
<tr><td align="right"><img src="{VAR:baseurl}/img/forum_joon2.gif" border="0" width="100%" height="2" alt=""></td></tr>-->
							
<!-- END SUB: TOPIC -->
</table>





			<TABLE width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr> 
			<TD align="right" class="textesileht">

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






<img src='{VAR:baseurl}/img/trans.gif' width="1" height="5" alt="" border="0"><br>
<form method="POST" name="commform" action="reforb{VAR:ext}">

<a name="comments"></a>

<!--begin komment-->

<table width="100%" border="0" cellspacing="0" cellpadding="0" class="text">
<!-- SUB: message -->
  <tr>
    <td bgcolor="{VAR:color}" width="60%">
	<table border="0" cellpadding="0" cellspacing="0">
	<tr><td>{VAR:icons}</td>
	<td class="text">&nbsp;<b><a href="{VAR:open_link2}">{VAR:subj}</a></b>{VAR:new}</td></tr></table>
	</td>
	<td width="1" bgcolor="#ffffff"><img src="{VAR:baseurl}/img/trans.gif" width="1" height="1"></td>
    <td class="text" bgcolor="{VAR:color}" width="20%">&nbsp;&nbsp;<a href="mailto:{VAR:email}">{VAR:from}</a></td>
	<td width="1" bgcolor="#ffffff"><img src="{VAR:baseurl}/img/trans.gif" width="1" height="1"></td>
    <td class="text" bgcolor="{VAR:color}" width="20%">&nbsp;&nbsp;{VAR:time}</td>
    </tr>
<!-- END SUB: message -->
</table>
<!--end komment-->
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

