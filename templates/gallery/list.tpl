<table border="0" cellspacing="0" cellpadding="0"  width=100%>
<tr>
<td bgcolor="#CCCCCC">

<table border="0" cellspacing="1" cellpadding="2"  width=100%>
<tr>
<td height="15" colspan="15" class="fgtitle">&nbsp;<b>{VAR:LC_GALLERY_BIG_GALLERYS}:&nbsp;<a href='{VAR:self}?type=new'>{VAR:LC_GALLERY_ADD}</a></b></td>
</tr>
<tr>
<td align="center" class="title">&nbsp;{VAR:LC_GALLERY_NAME}&nbsp;</td>
<td align="center" class="title">&nbsp;{VAR:LC_GALLERY_COMM}&nbsp;</td>
<td align="center" colspan="3" class="title">{VAR:LC_GALLERY_ACTIVITY}</td>
</tr>
<!-- SUB: LINE -->
<tr>
<td class="fgtext">&nbsp;{VAR:name}&nbsp;</td>
<td class="fgtext">&nbsp;{VAR:comment}&nbsp;</td>
<td class="fgtext2">&nbsp;<a href='{VAR:self}?type=content&id={VAR:id}'>{VAR:LC_GALLERY_CONTENT}</a>&nbsp;</td>
<td class="fgtext2">&nbsp;<a href='{VAR:self}?type=change&id={VAR:id}'>{VAR:LC_GALLERY_CHANGE}</a>&nbsp;</td>
<td class="fgtext2">&nbsp;<a href="javascript:box2('Oled kindel, et soovid seda galeriid kustutada?','{VAR:self}?type=delete&id={VAR:id}')">{VAR:LC_GALLERY_DELETE}</a>&nbsp;</td>
<!-- END SUB: LINE -->
</tr>
</table>

</td></tr>
</table>
<br><br>