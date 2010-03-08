<div class="text">Otsisid '<b>{VAR:sstring}</b>', Leiti {VAR:matches} dokumenti.<br>
Sorteerin <a href='{VAR:baseurl}/?parent={VAR:s_parent}&str={VAR:sstring}&class=document&action=search&section={VAR:section}&sortby=time&from={VAR:from}'>viimase muutmise </a> v&otilde;i <a href='{VAR:baseurl}/?parent={VAR:s_parent}&str={VAR:sstring}&class=document&action=search&section={VAR:section}&sortby=percent&from={VAR:from}'>t&auml;psuse </a> j&auml;rgi.</div>
<img src='{VAR:baseurl}/img/joon.gif' width=370 height=1 border=0>
<br>
{VAR:PAGESELECTOR}
<br>
<!-- SUB: MATCH -->
<table border=0 cellpadding=0 cellspacing=0 width=100%>
	<tr>
		<td class="text"><a href='{VAR:baseurl}/{VAR:section}'><b>{VAR:title}</b> </a>&nbsp;-&nbsp;{VAR:percent}% - <i>{VAR:modified}</i></td>
	</tr>
	<tr>
		<td class="text">{VAR:content}</td>
	</tr>
</table>
<br>
<!-- END SUB: MATCH -->

<!-- SUB: PAGESELECTOR -->
<span class='textSmall'>
<!-- SUB: PREVIOUS -->
<a href='{VAR:baseurl}/?parent={VAR:s_parent}&str={VAR:sstring}&class=document&action=search&section={VAR:section}&sortby={VAR:sortby}&from={VAR:from}'>Eelmised</a> 
<!-- END SUB: PREVIOUS -->
<!-- SUB: PAGE -->
&nbsp;<a href='{VAR:baseurl}/?parent={VAR:s_parent}&str={VAR:sstring}&class=document&action=search&section={VAR:section}&sortby={VAR:sortby}&from={VAR:from}'>{VAR:page_from} - {VAR:page_to}</a>&nbsp;
<!-- END SUB: PAGE -->
<!-- SUB: SEL_PAGE -->
&nbsp;<span class='textSmall'>{VAR:page_from} - {VAR:page_to}</span>&nbsp;
<!-- END SUB: SEL_PAGE -->
<!-- SUB: NEXT -->
<a href='{VAR:baseurl}/?parent={VAR:s_parent}&str={VAR:sstring}&class=document&action=search&section={VAR:section}&sortby={VAR:sortby}&from={VAR:from}'>J&auml;rgmised</a> 
<!-- END SUB: NEXT -->
</span>
<!-- END SUB: PAGESELECTOR -->
