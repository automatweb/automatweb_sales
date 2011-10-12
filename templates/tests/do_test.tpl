<form action='reforb{VAR:ext}' method=post>
<table bgcolor="#CCCCCC" cellpadding=3 cellspacing=1 border=0>
<!-- SUB: QUESTION -->
<tr>
<td class="fcaption">K&uuml;simus {VAR:num}, punkte {VAR:max_punkte}, teema {VAR:teema}:</td>
</tr>
<tr>
<td class="fcaption">{VAR:text}</td>
</tr>
<tr>
<td class="fcaption"><textarea name='answers[{VAR:qid}]' cols=50 rows=10></textarea></td>
</tr>
<!-- END SUB: QUESTION -->
<tr>
<td class="fcaption" colspan=2><input class='small_button' type='submit' VALUE='Salvesta'></td>
</tr>
</table>
{VAR:reforb}
</form>
