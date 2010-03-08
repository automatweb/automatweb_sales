<form action='reforb.{VAR:ext}' method=post>
<table bgcolor="#CCCCCC" cellpadding=3 cellspacing=1 border=0>
<tr>
<td class="fform">UID</td><td class="fform">{VAR:LC_FORMS_WHEN}</td><td class="fform">{VAR:LC_FORMS_CHANGE}</td><td class="fform">{VAR:LC_FORMS_DELETE}</td>
</tr>
<!-- SUB: LINE -->
<tr>
<td class="fform">{VAR:uid}</td><td class="fform">{VAR:tm}</td><td class="fform"><a href='{VAR:change}'>{VAR:LC_FORMS_CHANGE}</a></td><td class="fform"><a href='{VAR:delete}'>{VAR:LC_FORMS_DELETE}</a></td>
</tr>
<!-- END SUB: LINE -->
</table>
{VAR:reforb}
</form>
