<script language="javascript">
var sel_el;
function setLink(li,title)
{
	sel_el.value=li;
}
</script>

<form action='reforb{VAR:ext}' method=post name="b88" enctype="multipart/form-data">
<input type="hidden" name="MAX_FILE_SIZE" VALUE="1000000">
<table bgcolor="#CCCCCC" cellpadding=3 cellspacing=1 border=0>
<!-- SUB: AFTER_LOGIN -->
<tr bgcolor="#C9EFEF">
<td class="plain">Aadress, kuhu suunatakse p&auml;rast sisse logimist ({VAR:lang}):</td>
<td class="plain"><input type='text' name='after_login_{VAR:lang_id}' value='{VAR:after_login}'><a href="#" onclick="sel_el=document.b88.after_login_{VAR:lang_id};remote('no',500,400,'{VAR:search_doc}')">Saidi sisene link</a></td>
</tr>
<!-- END SUB: AFTER_LOGIN -->

<!-- SUB: MUSTLOGIN -->
<tr bgcolor="#C9EFEF">
<td class="plain">Aadress, kuhu suunatakse kui on vaja sisse logida ({VAR:lang}):</td>
<td class="plain"><input type='text' name='mustlogin_{VAR:lang_id}' value='{VAR:mustlogin}'><a href="#" onclick="sel_el=document.b88.mustlogin_{VAR:lang_id};remote('no',500,400,'{VAR:search_doc}')">Saidi sisene link</a></td>
</tr>
<!-- END SUB: MUSTLOGIN -->

<!-- SUB: ERROR_REDIRECT -->
<tr bgcolor="#C9EFEF">
<td class="plain">Aadress, kuhu suunatakse kui tuleb veateade ({VAR:lang}):</td>
<td class="plain"><input type='text' name='error_redirect_{VAR:lang_id}' value='{VAR:error_redirect}'><a href="#" onclick="sel_el=document.b88.error_redirect_{VAR:lang_id};remote('no',500,400,'{VAR:search_doc}')">Saidi sisene link</a></td>
</tr>
<!-- END SUB: ERROR_REDIRECT -->
<tr bgcolor="#C9EFEF">
<td class="plain">Uploadi "favorites icon":</td>
<td class="plain">{VAR:favicon} <input type="file" name="favicon"></td>
</tr>
<tr bgcolor="#C9EFEF">
<td class="plain">Kas p&auml;rast lisamist logitakse kasutaja sisse:</td>
<td class="plain"><input type="checkbox" name="autologin" value='1' {VAR:autologin}></td>
</tr>
<tr bgcolor="#C9EFEF">
<td class="plain">IP Aadressite default kataloog:</td>
<td class="plain"><select name='ipp'>{VAR:ipp}</select></td>
</tr>
<tr bgcolor="#C9EFEF">
<td class="plain" colspan=2><input type='submit' value='Salvesta'></td>
</tr>
</table>
{VAR:reforb}
</form>
