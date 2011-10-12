<br>
<form action='reforb{VAR:ext}' METHOD=post name=f1 enctype='multipart/form-data'>
<input type='hidden' name='MAX_FILE_SIZE' value='10000000'>
<table border="0" cellspacing="0" cellpadding="0" width=100%>
<tr>
<td bgcolor="#CCCCCC" width=100%>

<table border="0" cellspacing="1" cellpadding="0" width=100%>
<tr>
<td height="15" colspan="15" class="fgtitle">&nbsp;<b>{VAR:LC_FORMS_BIG_ELEMENTS}:
<a href="javascript:document.f1.submit()">{VAR:LC_FORMS_SAVE}</a>
&nbsp;|&nbsp;
<!-- SUB: CAN_ADD -->
<a href='{VAR:add_el}'>{VAR:LC_FORMS_ADD}</a> | 
<!-- END SUB: CAN_ADD -->

</b>Celli stiil: <select name="cell_style" class="formselect">{VAR:cell_style}</select>

</td>
</tr>

<!-- SUB: ELEMENT_LINE -->
<tr><td><a name='el_{VAR:after}'>{VAR:element}</td></tr>
<tr>
<td height="15" colspan="15" class="fgtitle"><a href="javascript:document.f1.submit()">{VAR:LC_FORMS_SAVE}</a>
</td>
</tr>
<!-- END SUB: ELEMENT_LINE -->

</table>

</td></tr></table>

<font face='tahoma, arial, geneva, helvetica' size="2">
{VAR:reforb}
</font>
</form>
