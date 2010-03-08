<form action='reforb.{VAR:ext}' method='POST' name='b88'>



<!--tabelraam-->
<table width="100%" cellspacing="0" cellpadding="1">
<tr><td class="tableborder">

	<!--tabelshadow-->
	<table width="100%" cellspacing="0" cellpadding="0">
	<tr><td width="1" class="tableshadow"><IMG SRC="{VAR:baseurl}/automatweb/images/trans.gif" WIDTH="1" HEIGHT="1" BORDER=0 ALT=""></td><td class="tableshadow"><IMG SRC="{VAR:baseurl}/automatweb/images/trans.gif" WIDTH="1" HEIGHT="1" BORDER=0 ALT=""><br>
		<!--tabelsisu-->
		<table width="100%" cellspacing="0" cellpadding="0">
		<tr><td><td class="tableinside" height="29">


<table border="0" cellpadding="0" cellspacing="0">
<tr>
<!--<td width="5"><IMG SRC="{VAR:baseurl}/images/trans.gif" WIDTH="5" HEIGHT="1" BORDER=0 ALT=""></td>-->
<td class="celltext" nowrap>&nbsp;<b>Otsingu conf:&nbsp;</b></td>

<td valign="middle"><IMG
SRC="{VAR:baseurl}/automatweb/images/trans.gif" WIDTH="4" HEIGHT="1" BORDER=0 ALT=""><a href="{VAR:add}"
onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('new','','{VAR:baseurl}/automatweb/images/blue/awicons/new_over.gif',1)"><img
name="new" alt="{VAR:LC_SEARCH_CONF_ADD}" border="0" SRC="{VAR:baseurl}/automatweb/images/blue/awicons/new.gif" width="25" height="25"></a></td>



<td><IMG
SRC="{VAR:baseurl}/automatweb/images/trans.gif" WIDTH="4" HEIGHT="1" BORDER=0 ALT=""><a href="{VAR:s_log}"
onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('log','','{VAR:baseurl}/automatweb/images/blue/awicons/log_over.gif',1)"><img
name="log" alt="Log" border="0" SRC="{VAR:baseurl}/automatweb/images/blue/awicons/log.gif" width="25" height="25"></a></td>


<td><IMG
SRC="{VAR:baseurl}/automatweb/images/trans.gif" WIDTH="4" HEIGHT="1" BORDER=0 ALT=""><a href="javascript:document.b88.submit()"
onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('save','','{VAR:baseurl}/automatweb/images/blue/awicons/save_over.gif',1)"><img
name="log" alt="Log" border="0" SRC="{VAR:baseurl}/automatweb/images/blue/awicons/save.gif" width="25" height="25"></a></td>



</tr>
</table>





		</td></tr>
		</table>

	</td></tr>
	</table>

</td></tr>
</table>








<table border="0" cellspacing="0" cellpadding="0" width=100%>
<tr>
<td bgcolor="#FFFFFF">


<table border="0" cellspacing="1" cellpadding="2" width=100%>
<tr class="aste05">


<td align="center" class="celltext">&nbsp;{VAR:LC_SEARCH_CONF_NAME}&nbsp;</td>
<td align="center" class="celltext">&nbsp;Jrk&nbsp;</td>
<td align="center" class="celltext" colspan=2>&nbsp;{VAR:LC_SEARCH_CONF_ACTION}&nbsp;</td>
<td align="center" class="celltext">Aktiivne</td>
</tr>

<!-- SUB: LINE -->
<tr class="aste07">
<td class="celltext" width="85%">&nbsp;{VAR:name}&nbsp;</td>
<td class="celltext" width="5%">&nbsp;{VAR:ord}&nbsp;</td>
<td class="celltext" width="5%" align="center"><a href='{VAR:change}'><img src="{VAR:baseurl}/automatweb/images/blue/obj_edit.gif" alt="{VAR:LC_SEARCH_CONF_CHANGE}" border="0"></a></td>
<td class="celltext" width="5%" align="center"><a href='{VAR:delete}'><img src="{VAR:baseurl}/automatweb/images/blue/obj_delete.gif" border="0" alt="{VAR:LC_SEARCH_CONF_DELETE}"></a></td>
<td class="celltext" width="5%" align="center"><input type="radio" name="act_search" value="{VAR:grpid}" {VAR:checked}></td>
</tr>
<!-- END SUB: LINE -->
<tr class="aste07">
	<td class="celltext" colspan="4">&nbsp;</td>
	<td class="celltext" width="5%" align="center"><input type="radio" name="act_search" value="" {VAR:no_act_search}></td>
</tr>
</table>
</td>
</tr>



</table>
<Br><br>
{VAR:reforb}
</form>
