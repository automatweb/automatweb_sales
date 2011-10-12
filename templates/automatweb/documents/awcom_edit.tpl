<form method="POST" action="reforb{VAR:ext}" name="doc" onSubmit="doSubmit();return true;">
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

<!--
<td align="center" class="icontext"><IMG SRC="{VAR:baseurl}/images/trans.gif" WIDTH="2" HEIGHT="2" BORDER=0 ALT=""><br><a href="{VAR:menurl}"
onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('brothering','','{VAR:baseurl}/automatweb/images/blue/awicons/brothering_over.gif',1)"><img name="brothering" alt="Vennastamine" border="0" SRC="{VAR:baseurl}/automatweb/images/blue/awicons/brothering.gif" width="25" height="25"></a><br><a
href="{VAR:menurl}">Vennastamine</a></td>
<td><IMG SRC="{VAR:baseurl}/images/trans.gif" WIDTH="10" HEIGHT="2" BORDER=0 ALT=""></td>


<td align="center" class="icontext"><IMG SRC="{VAR:baseurl}/images/trans.gif" WIDTH="2" HEIGHT="2" BORDER=0 ALT=""><br><a href="#" onClick="javascript:if (confirm('Teavitada liste?')) { window.location.href='{VAR:self}?class=keywords&action=notify&id={VAR:docid}';}" 
onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('lists','','{VAR:baseurl}/automatweb/images/blue/awicons/lists_over.gif',1)"><img name="lists" alt="Teavita liste" border="0" SRC="{VAR:baseurl}/automatweb/images/blue/awicons/lists.gif" width="25" height="25"></a><br><a href="#"
onClick="javascript:if (confirm('Teavitada liste?')) { window.location.href='{VAR:self}?class=keywords&action=notify&id={VAR:docid}';}">Teavita liste</a></td>
<td><IMG SRC="{VAR:baseurl}/images/trans.gif" WIDTH="10" HEIGHT="2" BORDER=0 ALT=""></td>


<td align="center" class="icontext"><IMG SRC="{VAR:baseurl}/images/trans.gif" WIDTH="2" HEIGHT="2" BORDER=0 ALT=""><br><a href="orb{VAR:ext}?class=document&action=archive&docid={VAR:id}" 
onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('archive','','{VAR:baseurl}/automatweb/images/blue/awicons/archive_over.gif',1)"><img name="archive" alt="Arhiiv" border="0" SRC="{VAR:baseurl}/automatweb/images/blue/awicons/archive.gif" width="25" height="25"></a><br><a
href="orb{VAR:ext}?class=document&action=archive&docid={VAR:id}">Arhiiv</a></td>
<td><IMG SRC="{VAR:baseurl}/images/trans.gif" WIDTH="10" HEIGHT="2" BORDER=0 ALT=""></td>
-->

<td align="center" class="icontext"><IMG SRC="{VAR:baseurl}/images/trans.gif" WIDTH="2" HEIGHT="2" BORDER=0 ALT=""><br><a target="_blank" href="{VAR:baseurl}/index.aw?section={VAR:id}" 
onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('preview','','{VAR:baseurl}/automatweb/images/blue/awicons/preview_over.gif',1)"><img name="preview" alt="Eelvaade" border="0" SRC="{VAR:baseurl}/automatweb/images/blue/awicons/preview.gif" width="25" height="25"></a><br><a
target="_blank" href="{VAR:baseurl}/index.aw?section={VAR:id}">Eelvaade</a></td>
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










<table border=0 cellspacing=1 cellpadding=2 width="100%">

<!--<input type="submit" class='doc_button' value="Salvesta">-->

<!--<input class='doc_button' type="submit" value="Eelvaade" onClick="window.location.href='{VAR:preview}';return false;">-->

<!--<input type="submit" class='doc_button' value="Sektsioonid" onClick="window.location.href='{VAR:menurl}';return false;">-->


<!--<input type="submit" class='doc_button' value="Webile" onClick="window.open('{VAR:baseurl}/index{VAR:ext}?section={VAR:id}');return false;">-->



<!--<input type="button" class="doc_button" value="Teavita liste" onClick="if (confirm('Teavitada liste?')) { window.location.href='{VAR:self}?class=keywords&action=notify&id={VAR:docid}';}">-->

<!--<input type="button" class="doc_button" value="Arhiiv" onClick="window.location.href='orb{VAR:ext}?class=document&action=archive&docid={VAR:id}'">-->

<!-- SUB: DOC_BROS -->
<tr>
<td class="celltext">{VAR:lang_name}</td>
<td class="celltext"><a href='{VAR:chbrourl}'>{VAR:bro_name}</a></td>
</tr>
<!-- END SUB: DOC_BROS -->
<tr>
<td COLSPAN=2>


<table border=0 cellpadding=0 cellspacing=0>
	<tr>
		<td colspan=3><img src='{VAR:baseurl}/images/transa.gif' width=1 height=10 border=0></td>
	</tr>
	<tr>
		<td class="celltext"><img src='{VAR:baseurl}/images/transa.gif' width=113 height=1 border=0><br><B>&nbsp;M‰‰rangud&nbsp;</b></td>
		<td class="fcaption2_nt" bgcolor="#CCCCCC"><img src='{VAR:baseurl}/images/transa.gif' width=1 height=10 border=0></td>
		<td class="celltext">&nbsp;	
			Aktiivne: <input type='checkbox' name='status' value='2' {VAR:cstatus}>&nbsp;&nbsp;&nbsp;
	N‰ita leadi: <input type='checkbox' name='showlead' value=1 {VAR:showlead}>&nbsp;&nbsp;&nbsp;
	Pealkiri klikitav: <input type='checkbox' name="title_clickable" {VAR:title_clickable} value=1>&nbsp;&nbsp;&nbsp;
	T&uuml;hista stiilid:	<input type='checkbox' name="clear_styles" value=1>&nbsp;&nbsp;&nbsp;
	Esilehel:	<input type='checkbox' name="esilehel" value=1 {VAR:esilehel}>&nbsp;&nbsp;&nbsp;
		</td>
	</tr>
</table>

</td>
</tr>

<script language="javascript">
function doSubmit()
{
	document.doc.submit();
	return true;
}
</script>

<tr>
<td class="celltext"><img src='{VAR:baseurl}/images/transa.gif' width=110 height=1><Br><B>&nbsp;Pealkiri&nbsp;</b></td>
<td class="celltext"><input class='tekstikast' type="text" name="title" size="80" value="{VAR:title}"></td>
</tr>


<tr>
<td class="celltext" valign="top"><b>&nbsp;Lead&nbsp;</b></td>
<td class="celltext">
<textarea name="lead" cols="70" rows="5">{VAR:lead}</textarea>
</td>
</tr>

<tr>
<td class="celltext" valign="top"><b>&nbsp;Sisu&nbsp;</b></td>
<td class="celltext"><textarea name="content" cols="70" rows="30">{VAR:content}</textarea>
</td>
</tr>

<tr>
<td class="celltext" valign="top"><b>&nbsp;Toimetamata&nbsp;</b></td>
<td class="celltext"><textarea name="moreinfo" cols="70" rows="30">{VAR:moreinfo}</textarea>
</td>
</tr>

<input type='hidden' name='nobreaks' value='0'>

</table>


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
onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('save2','','{VAR:baseurl}/automatweb/images/blue/awicons/save_over.gif',1)"><img name="save2" alt="Salvesta" border="0" SRC="{VAR:baseurl}/automatweb/images/blue/awicons/save.gif" width="25" height="25"></a><br><a
href="javascript:dosubmit();">Salvesta</a></td>
<td><IMG SRC="{VAR:baseurl}/images/trans.gif" WIDTH="10" HEIGHT="2" BORDER=0 ALT=""></td>

<td align="center" class="icontext"><IMG SRC="{VAR:baseurl}/images/trans.gif" WIDTH="2" HEIGHT="2" BORDER=0 ALT=""><br><a href="{VAR:preview}"
onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('preview2','','{VAR:baseurl}/automatweb/images/blue/awicons/preview_over.gif',1)"><img name="preview2" alt="Eelvaade" border="0" SRC="{VAR:baseurl}/automatweb/images/blue/awicons/preview.gif" width="25" height="25"></a><br><a
href="{VAR:preview}">Eelvaade</a></td>
<td><IMG SRC="{VAR:baseurl}/images/trans.gif" WIDTH="10" HEIGHT="2" BORDER=0 ALT=""></td>
<!--
<td align="center" class="icontext"><IMG SRC="{VAR:baseurl}/images/trans.gif" WIDTH="2" HEIGHT="2" BORDER=0 ALT=""><br><a href="{VAR:menurl}"
onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('brothering2','','{VAR:baseurl}/automatweb/images/blue/awicons/brothering_over.gif',1)"><img name="brothering2" alt="Vennastamine" border="0" SRC="{VAR:baseurl}/automatweb/images/blue/awicons/brothering.gif" width="25" height="25"></a><br><a
href="{VAR:menurl}">Vennastamine</a></td>
<td><IMG SRC="{VAR:baseurl}/images/trans.gif" WIDTH="10" HEIGHT="2" BORDER=0 ALT=""></td>


<td align="center" class="icontext"><IMG SRC="{VAR:baseurl}/images/trans.gif" WIDTH="2" HEIGHT="2" BORDER=0 ALT=""><br><a href="#" onClick="javascript:if (confirm('Teavitada liste?')) { window.location.href='{VAR:self}?class=keywords&action=notify&id={VAR:docid}';}" 
onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('lists2','','{VAR:baseurl}/automatweb/images/blue/awicons/lists_over.gif',1)"><img name="lists2" alt="Teavita liste" border="0" SRC="{VAR:baseurl}/automatweb/images/blue/awicons/lists.gif" width="25" height="25"></a><br><a href="#"
onClick="javascript:if (confirm('Teavitada liste?')) { window.location.href='{VAR:self}?class=keywords&action=notify&id={VAR:docid}';}">Teavita liste</a></td>
<td><IMG SRC="{VAR:baseurl}/images/trans.gif" WIDTH="10" HEIGHT="2" BORDER=0 ALT=""></td>


<td align="center" class="icontext"><IMG SRC="{VAR:baseurl}/images/trans.gif" WIDTH="2" HEIGHT="2" BORDER=0 ALT=""><br><a href="orb{VAR:ext}?class=document&action=archive&docid={VAR:id}" 
onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('archive2','','{VAR:baseurl}/automatweb/images/blue/awicons/archive_over.gif',1)"><img name="archive2" alt="Arhiiv" border="0" SRC="{VAR:baseurl}/automatweb/images/blue/awicons/archive.gif" width="25" height="25"></a><br><a
href="orb{VAR:ext}?class=document&action=archive&docid={VAR:id}">Arhiiv</a></td>
<td><IMG SRC="{VAR:baseurl}/images/trans.gif" WIDTH="10" HEIGHT="2" BORDER=0 ALT=""></td>
-->
</tr>

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



<!--<input type="submit" class='doc_button' value="Salvesta">

<input class='doc_button' type="submit" value="Eelvaade" onClick="window.location.href='{VAR:preview}';return false;">
<input type="submit" class='doc_button' value="Sektsioonid" onClick="window.location.href='{VAR:menurl}';return false;">

<input type="submit" class='doc_button' value="Webile" onClick="remote2('{VAR:weburl}')">-->



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
