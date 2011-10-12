<form action='reforb{VAR:ext}' METHOD=post name="savexml">
<table bgcolor="#CCCCCC" cellpadding=3 cellspacing=1 border=0>
<tr>
<td class="fcaption" colspan="5">
<a href="javascript:document.savexml.submit()">{VAR:LC_FORMS_SAVE}</a>
|
<a href="{VAR:edurl}">{VAR:LC_FORMS_CHANGE}</a>
</td>
</tr>
<!-- SUB: form -->
<tr>
<td class="fcaption"><b>Form:</b></td><td class="fcaption" colspan="4"><b>{VAR:fname}</b></td>
</tr>
<tr>
<td class="fcaption">{VAR:LC_FORMS_ELEMENT}</td>
<td class="fcaption">{VAR:LC_FORMS_TYPE}</td>
<td class="fcaption">Ext. nimi</td>
<td class="fcaption">Akt.</td>
</tr>
<!-- SUB: element -->
<tr>
<td class="fform">{VAR:name}</td>
<td class="fform">{VAR:type}</td>
<td class="fform"><input type="text" name="extname[{VAR:id}]" size="20" maxlength="40" value="{VAR:extname}"></td>
<td class="fform" align="center"><input type="checkbox" name="active[{VAR:id}]" value="1" {VAR:checked}></td>
<input type="hidden" name="exists[{VAR:id}]" value="1">
<input type="hidden" name="type[{VAR:id}]" value="{VAR:type}">
<input type="hidden" name="form[{VAR:id}]" value="{VAR:form_id}">
</tr>
<!-- END SUB: element -->
<tr>
<td class="fcaption" colspan="5">
&nbsp;
</td>
</tr>
<!-- END SUB: form -->
<tr>
<td class="fcaption" colspan="5">
<input type="submit" value="{VAR:LC_FORMS_SAVE}">
</td>
</tr>
</table>
{VAR:reforb}
</form>
