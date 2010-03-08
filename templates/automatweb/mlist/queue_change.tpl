<!-- SUB: viga -->
{VAR:teade}
<!-- END SUB: viga -->

<!-- SUB: sisu -->
<form action="reforb.aw" method="POST">
<table bgcolor="#CCCCCC" cellpadding=3 cellspacing=1 border=0>
<tr><td class="title" colspan="2">Muuda queue objekti:</td></tr>
<tr>
<td class="ftitle2">Mitu korraga:</td>
<td class="fgtext"><input type="text" name="patch_size" class="small_button" value="{VAR:patch_size}"></td>
</tr>

<tr>
<td class="ftitle2">Vahepeal oota:</td>
<td class="fgtext"><input type="text" name="delay" value="{VAR:delay}" class="small_button">(min)</td>
</tr>

<tr>
<td class="ftitle2" colspan="2">Saatmise aeg:</td>
</tr>
<tr>
<td class="fgtext" colspan="2"><input type="radio" name="timing" value="same" checked class="small_button">Jäta samaks</td>
</tr>
<tr>
<td class="fgtext" colspan="2"><input type="radio" name="timing" value="now" class="small_button">Saada nüüd</td>
</tr>
<tr>
<td class="fgtext"><input type="radio" name="timing" value="set" class="small_button">Aeg:</td>
<td class="fgtext">{VAR:settime}</td>
</tr>
<tr>
<td class="ftitle2" colspan="2" align="right"><input type="submit" value="Salvesta" class="small_button"></td>
</tr>
</table>
{VAR:reforb}
</form>
<!-- END SUB: sisu -->