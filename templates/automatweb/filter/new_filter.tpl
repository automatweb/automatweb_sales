<form action = 'reforb{VAR:ext}' method=post>
<table bgcolor="#CCCCCC" cellpadding=3 cellspacing=1 border=0>
<tr>
<td class="fgtitle">Nimi:</td>
<td class="fgtext"><input type='text' class='small_button' NAME='name' VALUE='{VAR:name}'></td>
</tr>
<tr>
<td class="fgtitle">Kommentaar:</td><td class="fgtext"><input type='text' class='small_button' NAME='comment' VALUE='{VAR:comment}'></td>
</tr>

<tr>
<td class="fgtitle"><input type="radio" class="small_button" name="type" value="form" checked>Otsi formist:</td>
<td class="fgtext"><select class='small_button' NAME='target_id_f'>{VAR:formlist}</select></td>
</tr>

<tr>
<td class="fgtitle"><input type="radio" class="small_button" name="type" value="chain">Otsi pärjast:</td>
<td class="fgtext"><select class='small_button' NAME='target_id_c[]' multiple>{VAR:chainlist}</select></td>
</tr>


<td class="fgtitle" colspan="3" align="right"><input class='small_button' type='submit' VALUE='Edasi >>'></td>
</tr>
</table>
{VAR:reforb}
</form>
