<form method="POST" action="reforb{VAR:ext}" name="doc">

<!--tabelraam-->
<table width="100%" cellspacing="0" cellpadding="1">
<tr><td class="tableborder">

	<!--tabelshadow-->
	<table width="100%" cellspacing="0" cellpadding="0">
	<tr><td width="1" class="tableshadow"><IMG SRC="{VAR:baseurl}/automatweb/images/trans.gif" WIDTH="1" HEIGHT="1" BORDER=0 ALT=""></td><td class="tableshadow"><IMG SRC="{VAR:baseurl}/automatweb/images/trans.gif" WIDTH="1" HEIGHT="1" BORDER=0 ALT=""><br>
		<!--tabelsisu-->
		<table width="100%" cellspacing="0" cellpadding="0">
		<tr><td><td class="tableinside">


<table border="0" cellpadding="0" cellspacing="2">

<tr>
<td align="center" class="icontext"><IMG SRC="{VAR:baseurl}/images/trans.gif" WIDTH="2" HEIGHT="2" BORDER=0 ALT=""><br><a href="javascript:doSubmit();" 
onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('save','','{VAR:baseurl}/automatweb/images/blue/awicons/save_over.gif',1)"><img name="save" alt="Salvesta" border="0" SRC="{VAR:baseurl}/automatweb/images/blue/awicons/save.gif" width="25" height="25"></a><br><a
href="javascript:doSubmit()" >Salvesta</a></td>
<td><IMG SRC="{VAR:baseurl}/images/trans.gif" WIDTH="10" HEIGHT="2" BORDER=0 ALT=""></td>

<td align="center" class="icontext"><IMG SRC="{VAR:baseurl}/images/trans.gif" WIDTH="2" HEIGHT="2" BORDER=0 ALT=""><br><a href="{VAR:preview}"
onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('preview','','{VAR:baseurl}/automatweb/images/blue/awicons/preview_over.gif',1)"><img name="preview" alt="Eelvaade" border="0" SRC="{VAR:baseurl}/automatweb/images/blue/awicons/preview.gif" width="25" height="25"></a><br><a
href="{VAR:preview}">Eelvaade</a></td>
<td><IMG SRC="{VAR:baseurl}/images/trans.gif" WIDTH="10" HEIGHT="2" BORDER=0 ALT=""></td>

<td align="center" class="icontext"><IMG SRC="{VAR:baseurl}/images/trans.gif" WIDTH="2" HEIGHT="2" BORDER=0 ALT=""><br><a href="{VAR:menurl}"
onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('brothering','','{VAR:baseurl}/automatweb/images/blue/awicons/brothering_over.gif',1)"><img name="brothering" alt="Vennastamine" border="0" SRC="{VAR:baseurl}/automatweb/images/blue/awicons/brothering.gif" width="25" height="25"></a><br><a
href="{VAR:menurl}">Vennastamine</a></td>
<td><IMG SRC="{VAR:baseurl}/images/trans.gif" WIDTH="10" HEIGHT="2" BORDER=0 ALT=""></td>


<td align="center" class="icontext"><IMG SRC="{VAR:baseurl}/images/trans.gif" WIDTH="2" HEIGHT="2" BORDER=0 ALT=""><br><a href="#" onClick="javascript:if (confirm('Teavitada liste?')) { window.location.href='{VAR:self}?class=keywords&action=notify&id={VAR:id}';}" 
onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('lists','','{VAR:baseurl}/automatweb/images/blue/awicons/lists_over.gif',1)"><img name="lists" alt="Teavita liste" border="0" SRC="{VAR:baseurl}/automatweb/images/blue/awicons/lists.gif" width="25" height="25"></a><br><a href="#"
onClick="javascript:if (confirm('Teavitada liste?')) { window.location.href='{VAR:self}?class=keywords&action=notify&id={VAR:id}';}">Teavita liste</a></td>
<td><IMG SRC="{VAR:baseurl}/images/trans.gif" WIDTH="10" HEIGHT="2" BORDER=0 ALT=""></td>


<td align="center" class="icontext"><IMG SRC="{VAR:baseurl}/images/trans.gif" WIDTH="2" HEIGHT="2" BORDER=0 ALT=""><br><a href="orb{VAR:ext}?class=document&action=archive&docid={VAR:id}" 
onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('archive','','{VAR:baseurl}/automatweb/images/blue/awicons/archive_over.gif',1)"><img name="archive" alt="Arhiiv" border="0" SRC="{VAR:baseurl}/automatweb/images/blue/awicons/archive.gif" width="25" height="25"></a><br><a
href="orb{VAR:ext}?class=document&action=archive&docid={VAR:id}">Arhiiv</a></td>
<td><IMG SRC="{VAR:baseurl}/images/trans.gif" WIDTH="10" HEIGHT="2" BORDER=0 ALT=""></td>

<td align="center" class="icontext"><IMG SRC="{VAR:baseurl}/images/trans.gif" WIDTH="2" HEIGHT="2" BORDER=0 ALT=""><br><a target="_blank" href="{VAR:baseurl}/index.aw?section={VAR:id}" 
onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('archive','','{VAR:baseurl}/automatweb/images/blue/awicons/archive_over.gif',1)"><img name="archive" alt="Webile" border="0" SRC="{VAR:baseurl}/automatweb/images/blue/awicons/archive.gif" width="25" height="25"></a><br><a
target="_blank" href="{VAR:baseurl}/index.aw?section={VAR:id}">Webile</a></td>
<td><IMG SRC="{VAR:baseurl}/images/trans.gif" WIDTH="10" HEIGHT="2" BORDER=0 ALT=""></td>

</table>


		</td>
		</tr>
		</table>


	</td>
	</tr>
	</table>

</td>
</tr>
</table>

{VAR:content}

<table width="100%" border=0 cellspacing=1 cellpadding=2 bgcolor="#CCCCCC">
<tr>
<td>
<iframe width="100%" height="800" frameborder="0" src="{VAR:aliasmgr_link}">
</iframe>
</td>
</tr>
</table>
{VAR:reforb}
</form>

<script language="javascript">
function doSubmit()
{
	check_submit();
	doc.submit();
}
</script>