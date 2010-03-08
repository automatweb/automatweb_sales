<form action='reforb.{VAR:ext}' method=post>
<table bgcolor="#CCCCCC" cellpadding=3 cellspacing=1 border=0>
<tr>
<td class="fcaption">Mitu &uuml;lesannet:</td><td class="fform"><input type='text' name='kysimusi' size=3></td>
</tr>
<tr>
<td class="fcaption">Raskusaste:</td><td class="fform"><select name='raskus'>{VAR:raskus}</select></td>
</tr>
<tr>
<td class="fcaption">Teemad:</td><td class="fform"><select MULTIPLE name='teemad[]'>{VAR:teemad}</select></td>
</tr>
<tr>
<td class="fcaption" colspan=2>Vali, kuidas test koostatkse:</td>
</tr>
<tr>
<td class="fcaption"><input type='radio' name='level' VALUE=2 checked></td><td class="fform">Valid &uuml;lesannete kupa, mis teemadest nad on</td>
</tr>
<tr>
<td class="fcaption"><input type='radio' name='level' VALUE=3></td><td class="fform">Valid teemade kaupa, mitu igast teemast</td>
</tr>
<tr>
<td class="fcaption"><input type='radio' name='level' VALUE=4></td><td class="fform">Suvaliselt valitud</td>
</tr>
<tr>
<td class="fcaption" colspan=2><input class='small_button' type='submit' VALUE='Edasi'></td>
</tr>
</table>
{VAR:reforb}
</form>
