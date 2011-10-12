<br><br><br><br>
 <form action='orb{VAR:ext}' method=GET>
<table bgcolor="#CCCCCC" cellpadding=3 cellspacing=1 border=0>
<tr>
<td class="fcaption">Vali form:</td>
<td class="fform"><select name='sf'>{VAR:sfs}</select></td>
</tr>
<tr>
<td class="fcaption" colspan=2><input type='submit' VALUE='N&auml;ita'></td>
</tr>
</table>
{VAR:reforb}
</form>
<br>
<!-- SUB: show_form -->
T&auml;ida form:
{VAR:form}
<br><br>
<!-- SUB: results -->
Otsingu tulemused: <Br>
{VAR:entry}
<br>
<form action='reforb{VAR:ext}' method="POST">
Kui oled tulemustega rahul, siis <input type='submit' value='vajuta siia'> aliase tegemiseks.
{VAR:a_reforb}
</form>
<!-- END SUB: results -->

<!-- END SUB: show_form -->
