<form action='reforb{VAR:ext}' method=POST name='q'>

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
<tr><td width="5"><IMG SRC="{VAR:baseurl}/automatweb/images/trans.gif" WIDTH="5" HEIGHT="1" BORDER=0 ALT=""></td>




<td class="celltitle">&nbsp;<b>{VAR:LC_LANGUAGES_BIG_LANGUAGES}:&nbsp;</td>
<td align="left"><!--add--><IMG
SRC="{VAR:baseurl}/automatweb/images/trans.gif" WIDTH="4" HEIGHT="1" BORDER=0 ALT=""><a href="{VAR:add}"
onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('new','','{VAR:baseurl}/automatweb/images/blue/awicons/new_over.gif',1)"><img
name="new" alt="{VAR:LC_LANGUAGES_ADD}" border="0" SRC="{VAR:baseurl}/automatweb/images/blue/awicons/new.gif" width="25" height="25"></a><!--save--><IMG SRC="{VAR:baseurl}/automatweb/images/trans.gif" WIDTH="4" HEIGHT="1" BORDER=0 ALT=""><a href="javascript:document.q.submit()" 
onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('save','','{VAR:baseurl}/automatweb/images/blue/awicons/save_over.gif',1)"><img name="save" alt="{VAR:LC_LANGUAGES_SAVE}" border="0" SRC="{VAR:baseurl}/automatweb/images/blue/awicons/save.gif" width="25" height="25"></a></td>
</tr></table>


		</td></tr></table>
	</td></tr></table>
</td></tr></table>









<table border="0" cellspacing="0" cellpadding="0" width=100%>
<tr>
<td bgcolor="#FFFFFF">





<table border="0" cellspacing="1" cellpadding="2" width=100%>
<tr class="aste05">

		<td align=center class="celltext" width="20%">&nbsp;{VAR:LC_LANGUAGES_NAME}&nbsp;</td>
		<td align=center class="celltext" width="20%">&nbsp;{VAR:LC_LANGUAGES_LANGUAGE_ID}&nbsp;</td>
		<td align=center class="celltext" width="20%">&nbsp;{VAR:LC_LANGUAGES_CHARSET}&nbsp;</td>
		<td align=center class="celltext" width="20%">&nbsp;Muutja&nbsp;</td>
		<td align=center class="celltext" width="20%">&nbsp;Muudetud&nbsp;</td>
		<td align=center class="celltext" width="10%">&nbsp;{VAR:LC_LANGUAGES_CHOSEN}&nbsp;</td>
		<td align=center class="celltext" width="10%">&nbsp;{VAR:LC_LANGUAGES_ADMIN}&nbsp;</td>
		<td align=center class="celltext" width="10%">&nbsp;{VAR:LC_LANGUAGES_ACTIVE}?&nbsp;</td>
		<td align=center class="celltext" width="10%">&nbsp;Saidid&nbsp;</td>
		<td align="center" colspan="1" width="10%" class="celltext">&nbsp;{VAR:LC_LANGUAGES_ACTION}&nbsp;</td>
	</tr>
<!-- SUB: LINE -->
<tr class="aste07"	>
<td class="celltext" align=center>&nbsp;{VAR:name}&nbsp;</td>
<td class="celltext" align=center>&nbsp;{VAR:acceptlang}&nbsp;</td>
<td class="celltext" align=center>&nbsp;{VAR:charset}&nbsp;</td>
<td class="celltext" align=center>&nbsp;{VAR:modifiedby}&nbsp;</td>
<td class="celltext" align=center>&nbsp;{VAR:modified}&nbsp;</td>
<td class="celltext" align="center">&nbsp;<input type="radio" name="selected" value='{VAR:id}' {VAR:selected}>&nbsp;</td>
<td class="celltext" align="center">&nbsp;<input type='radio' name='adminlang' value='{VAR:id}' {VAR:check}>&nbsp;</td>
<td class="celltext" align="center">&nbsp;<input type="checkbox" name="act[{VAR:id}]" value='1' {VAR:active}>&nbsp;</td>
<td class="celltext" align="center">&nbsp;{VAR:sites}&nbsp;</td>
<td class="celltext" align=center><a href='{VAR:change}'><IMG SRC="{VAR:baseurl}/automatweb/images/blue/obj_edit.gif" WIDTH="16" HEIGHT="16" BORDER=0 ALT="{VAR:LC_LANGUAGES_CHANGE}"></a></td>
<!--<td class="celltext" align=center><a href='javascript:box2("Are You sure you wish to delete language?","{VAR:delete}")'><IMG SRC="{VAR:baseurl}/automatweb/images/blue/obj_delete.gif" WIDTH="16" HEIGHT="16" BORDER=0 ALT="{VAR:LC_LANGUAGES_DELETE}"></a></td>-->
</tr>
<!-- END SUB: LINE -->
</table>
</td></tr></table>
{VAR:reforb}
</form>
