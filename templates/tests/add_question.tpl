<form action='reforb.{VAR:ext}' method=post>
<table bgcolor="#CCCCCC" cellpadding=3 cellspacing=1 border=0>
<tr>
<td class="fcaption">Teema:</td><td class="fform"><select name='teema'>{VAR:teema}</select></td>
</tr>
<tr>
<td class="fcaption">Raskusaste:</td><td class="fform"><select name='raskus'>{VAR:raskus}</select></td>
</tr>
<tr>
<td class="fcaption">Max punkte:</td><td class="fform"><select name='max_punkte'>{VAR:max_punkte}</select></td>
</tr>
<tr>
<td class="fcaption">Sobib kasutamiseks eksamil:</td><td class="fform"><input type='checkbox' value=1 name='examix' {VAR:examix}></td>
</tr>
<tr>
<td class="fcaption">Pealkiri:</td><td class="fform"><input type='text' NAME='name' VALUE='{VAR:name}'></td>
</tr>
<tr>
<td class="fcaption">Sisu:</td><td class="fform"><textarea name='text' cols=50 rows=20>{VAR:text}</textarea></td>
</tr>
<tr>
<td class="fcaption">Lahendus:</td><td class="fform"><textarea name='lahendus' cols=50 rows=20>{VAR:lahendus}</textarea></td>
</tr>
<tr>
<td class="fcaption" colspan=2><input class='small_button' type='submit' VALUE='Salvesta'></td>
</tr>
</table>
{VAR:reforb}
</form>
