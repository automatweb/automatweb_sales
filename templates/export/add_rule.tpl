<form name='q' method="POST" action='reforb{VAR:ext}'>
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




<td class="celltitle">&nbsp;<b>&nbsp;</td>
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
		<td class="celltext">Nimi:</td>
		<td class="celltext"><input type='text' class='formtext' name='name' value='{VAR:name}'></td>
	</tr>
	<tr class="aste05">
		<td class="celltext">Vali kataloogid:</td>
		<td class="celltext"><select multiple size="20" class='formselect' name='menus[]'>{VAR:menus}</select></td>
	</tr>
<!-- SUB: CHANGE -->
	<tr class="aste05">
		<td colspan="2" class="celltext"><a href='javascript:remote("no",500,500,"{VAR:sel_period}")'>Vali millal t&auml;idetakse</a></td>
	</tr>
	<tr class="aste05">
		<td colspan="2" class="celltext"><a href='{VAR:do_rule}'>T&auml;ida ruul</a></td>
	</tr>
	<tr class="aste05">
		<td colspan="2" class="celltext"><a href='{VAR:stop_rule}'>Peata taustal k&auml;iv eksport</a> - see v&otilde;ib v&otilde;tta kaua aega, kontrolli tehakse enne iga lehek&uuml;lje t&otilde;mbamist - see kehtib ainult konkreetse ruuli kohta.</td>
	</tr>
<!-- END SUB: CHANGE -->
</table>

</td>
</tr>
</table>
{VAR:reforb}
</form>