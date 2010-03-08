<table border="0" cellspacing="0" cellpadding="0" width=100%>
<tr>
<td bgcolor="#CCCCCC">

<table border="0" cellspacing="1" cellpadding="0" width=100%>
<form method="POST" name="fubar">
<tr>
<td height="15" colspan="11" class="fgtitle">&nbsp;<b>VÕTMESÕNAD: 
</b>
<a href="#" onClick="if (confirm('Kustutada margitud kirjed?')) {document.fubar.submit()};">Kustuta</a>
</td>
</tr>
<tr>
<td align="center" class="title">&nbsp;X&nbsp;</td>
<td align="center"  class="title">&nbsp;Võtmesõna&nbsp;</td>
<td align="center" class="title">&nbsp;Dokumente&nbsp;</td>
<td align="center" class="title">&nbsp;Huvilisi&nbsp;</td>
<td align="center" class="title" width="*">&nbsp;&nbsp;</td>
</tr>
<!-- SUB: HEADER -->
<tr>
<td class="fgtext" colspan="5">
<strong>{VAR:title}</strong>
</td>
</tr>
<!-- END SUB: HEADER -->
<!-- SUB: LINE -->
<tr>
<td class="fgtext" align="center"><input type="checkbox" name="check[{VAR:id}]" value="1"></td>
<td class="fgtext">&nbsp;{VAR:keyword}&nbsp;</td>
<td align="center" class="fgtext">&nbsp;<a href="{VAR:self}?class=keywords&action=doclist&id={VAR:id}">{VAR:doc_count}</a>&nbsp;</td>
<td align="center" class="fgtext">&nbsp;<a href="{VAR:self}?class=keywords&action=listmembers&id={VAR:list_id}">{VAR:people_count}</a>&nbsp;</td>
<td class="fgtext" align="center">&nbsp;&nbsp;</td>
</tr>
<!-- END SUB: LINE -->
</table>
</td>
</tr>
{VAR:reforb}
</form>
</table>
<br><br>


