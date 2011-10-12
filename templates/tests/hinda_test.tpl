<form action='reforb{VAR:ext}' method=post>
<table bgcolor="#CCCCCC" cellpadding=3 cellspacing=1 border=0>
<tr>
<td class="fcaption" colspan=2>Testi t&auml;itis {VAR:whodunit} @ {VAR:when}, raskusaste {VAR:raskus}, kysimusi {VAR:questions}.</td>
</tr>
<!-- SUB: QUESTION -->
<tr>
<td class="fcaption" colspan=2>K&uuml;simus {VAR:num}, teema {VAR:teema}:</td>
</tr>
<tr>
<td class="fcaption" colspan=2>{VAR:text}</td>
</tr>
<tr>
<td class="fcaption">Vastus:</td>
<td class="fcaption">&Otilde;ige vastus:</td>
</tr>
<tr>
<td class="fcaption">{VAR:answer}</td>
<td class="fcaption">{VAR:correct_answer}</td>
</tr>
<tr>
<td colspan=2 class="fcaption">Hinne: <select name='punkte[{VAR:vid}]'>{VAR:punkte}</select></td>
</tr>
<!-- END SUB: QUESTION -->
<tr>
<td class="fcaption" colspan=2><input class='small_button' type='submit' VALUE='Salvesta'></td>
</tr>
</table>
{VAR:reforb}
</form>
