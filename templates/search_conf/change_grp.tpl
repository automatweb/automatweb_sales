
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



<td valign="middle"><IMG
SRC="{VAR:baseurl}/automatweb/images/trans.gif" WIDTH="4" HEIGHT="1" BORDER=0 ALT=""><a href="javascript:this.document.b88.submit();"
onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('save','','{VAR:baseurl}/automatweb/images/blue/awicons/save_over.gif',1)"><img
name="save" alt="{VAR:LC_SEARCH_CONF_ADD}" border="0" SRC="{VAR:baseurl}/automatweb/images/blue/awicons/save.gif" width="25" height="25"></a></td>


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
<td>


<table border="0" cellspacing="0" cellpadding="2" width=100%>
<tr>


<td class="celltext" width="20%">&nbsp;{VAR:LC_SEARCH_CONF_NAME}:&nbsp;</td>
<td class="celltext" width="80%">&nbsp;<input type="text" name="name" value="{VAR:name}">&nbsp;</td>
</tr>
<tr>
<td class="celltext">&nbsp;Jrk:&nbsp;</td>
<td class="celltext">&nbsp;<input type="text" name="ord" value="{VAR:ord}">&nbsp;</td>
</tr>
<tr>
<td class="celltext">&nbsp;{VAR:LC_SEARCH_CONF_USONLY_NOLOG}:&nbsp;</td>
<td class="celltext">&nbsp;<input type="checkbox" name="no_usersonly" value="1" {VAR:no_usersonly}>&nbsp;</td>
</tr>
<tr>
<td class="celltext">&nbsp;Users only:&nbsp;</td>
<td class="celltext">&nbsp;<input type="checkbox" name="users_only" value="1" {VAR:users_only}>&nbsp;</td>
</tr>
<tr>
<td class="celltext">&nbsp;Min. märke:&nbsp;</td>
<td class="celltext">&nbsp;<input type="text" name="min_len" value="{VAR:min_len}" size="3" maxlength="3">&nbsp;</td>
</tr>
<tr>
<td class="celltext">&nbsp;Max. märke:&nbsp;</td>
<td class="celltext">&nbsp;<input type="text" name="max_len" value="{VAR:max_len}" size="3" maxlength="3">&nbsp;</td>
</tr>
<tr>
<td class="celltext">&nbsp;Mida teha tühja otsinguga:&nbsp;</td>
<td class="celltext">
<input type="radio" name="empty_search" value="1" {VAR:empty_no_docs}>&nbsp;
Ära näita ühtegi dokumenti:
<br>
<input type="radio" name="empty_search" value="2" {VAR:empty_all_docs}>&nbsp;
Näita kõiki dokumente:
</td>
</tr>
<tr>
<td class="celltext">&nbsp;Otsing staatilisest sisust:&nbsp;</td>
<td class="celltext">&nbsp;<input type="checkbox" name="static_search" value="1" {VAR:static_search}>&nbsp;</td>
</tr>
<tr>
<td class="celltext" valign="top">&nbsp;{VAR:LC_SEARCH_CONF_MENUS}:&nbsp;</td>
<td class="celltext">&nbsp;<select class='small_button' size=20 name='menus[]' multiple>{VAR:menus}</select>&nbsp;</td>
</tr>
<tr>
<td class="celltext" valign="top">&nbsp;Otsing formi:&nbsp;</td>
<td class="celltext">&nbsp;<select class='formselect' name='search_form'>{VAR:search_forms}</select>&nbsp;</td>
</tr>
<tr>
<td class="celltext" valign="top">&nbsp;Otsingu elemendid:&nbsp;</td>
<td class="celltext">&nbsp;<select class='formselect' multiple name='search_elements[]' size="10">{VAR:search_elements}</select>&nbsp;</td>
</tr>
</table>
</td>
</tr>
</table>
<!--<input type='submit' class='formbutton' value='{VAR:LC_SEARCH_CONF_SAVE}'>-->
<Br><br>
{VAR:reforb}
</form>
