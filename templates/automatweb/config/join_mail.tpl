<br>
<form action='reforb.{VAR:ext}' method=POST>
<table bgcolor="#CCCCCC" cellpadding=3 cellspacing=1 border=0>
<!-- SUB: LANG -->
<tr bgcolor="#C9EFEF">
<td class="plain">E-maili sisu, mis saadetakse kasutajale liitumisel (kasutajanime alias #kasutaja#, parooli alias #parool# ja parooli muutmise lingi alias #pwd_hash#). ({VAR:name})</td>
</tr>
<tr bgcolor="#C9EFEF">
<td class="plain">Subject: <input type='text' name='join_mail_subj[{VAR:acceptlang}]' value='{VAR:join_mail_subj}'></td>
</tr>
<tr bgcolor="#C9EFEF">
<td class="plain"><textarea name='join_mail[{VAR:acceptlang}]' cols=70 rows=20 wrap=hard>{VAR:join_mail}</textarea></td>
</tr>
<tr bgcolor="#C9EFEF">
<td class="plain">E-maili sisu, mis saadetakse kasutajale kui tal on parool meelest l2inud (kasutajanime alias #kasutaja# ja parooli alias #parool#). ({VAR:name})</td>
</tr>
<tr bgcolor="#C9EFEF">
<td class="plain">Subject: <input type='text' name='pwd_mail_subj[{VAR:acceptlang}]' value='{VAR:pwd_mail_subj}'></td>
</tr>
<tr bgcolor="#C9EFEF">
<td class="plain"><textarea name='pwd_mail[{VAR:acceptlang}]' cols=70 rows=20 wrap=hard>{VAR:pwd_mail}</textarea></td>
</tr>
<tr bgcolor="#C9EFEF">
<td class="plain">Mis kataloogi alla viitab parooli muutmise link:</td>
</tr>
<tr bgcolor="#C9EFEF">
<td class="plain"><input size=30 type='text' name='join_hash_section[{VAR:acceptlang}]' value='{VAR:join_hash_section}'></td>
</tr>
<!-- END SUB: LANG -->
<tr bgcolor="#C9EFEF">
<td class="plain">Mis aadressile saadetakse liitujatele saadetud e-mailid:</td>
</tr>
<tr bgcolor="#C9EFEF">
<td class="plain"><input size=30 type='text' name='join_send_also' value='{VAR:join_send_also}'></td>
</tr>

<tr bgcolor="#C9EFEF">
<td class="plain"><input type='submit' value='Salvesta'></td>
</tr>
{VAR:reforb}
</form>
