<form name='q' method="POST" action='reforb.{VAR:ext}'>
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
<tr><td width="5"><IMG SRC="{VAR:baseurl}/images/trans.gif" WIDTH="5" HEIGHT="1" BORDER=0 ALT=""></td>




<td class="celltitle">&nbsp;<b><a href='{VAR:exp_url}'>Saidi export</a> | <a href='{VAR:gen_url}'>Ekspordi</a> | <a href='{VAR:pick_active}'>Vali aktiivne versioon</a> | <a href='{VAR:view_log}'>Vaata export
logi</a>&nbsp;| Interaktiivne eksport</td>
<td align="left"><!--save--><IMG SRC="{VAR:baseurl}/images/trans.gif" WIDTH="4" HEIGHT="1" BORDER=0 ALT=""><a href="javascript:document.q.submit()" 
onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('save','','{VAR:baseurl}/automatweb/images/blue/awicons/save_over.gif',1)"><img name="save" alt="Salvesta" border="0" SRC="{VAR:baseurl}/automatweb/images/blue/awicons/save.gif" width="25" height="25"></a></td>
</tr></table>


		</td></tr></table>
	</td></tr></table>
</td></tr></table>


<table border="0" cellspacing="0" cellpadding="0" width=100%>
<tr>
<td bgcolor="#FFFFFF">

<table border="0" cellspacing="1" cellpadding="2" width=100%>
	<tr class="aste05">
		<td class="celltext" colspan="2">Sisesta aadressid, mida eksportida
(aadresside eraldaja on reavahetus):</td>
	</tr>
	<tr class="aste05">
		<td colspan="2" class="celltext"><textarea name="urls" cols=80 rows=10></textarea></td>
	</tr>
</table>

</td>
</tr>
</table>
{VAR:reforb}
</form>