<form action="reforb{VAR:ext}" METHOD="POST" name="fr">
<script language="jAVAscrIPT">
function sendcmd(blah)
{
	fr.subaction.value=blah;
	fr.submit();
};
</script>
<input type="hidden" name="subaction" value="">
{VAR:reforb}
<table border="0" cellspacing="0" cellpadding="0" >
<tr><td class="title"></td></tr>
<tr>
<td bgcolor="#CCCCCC">
<table border="0" cellspacing="1" cellpadding="2" width="100%">

<tr><td class="fgtitle" colspan="4"><label for=stat_show><input type="checkbox" name="stat_show" id="stat_show" {VAR:chkstat_show} class="small_button">Näita stat tabelit</label></td></tr>
<tr><td class="fgtitle" colspan="1">Kaugus andmetest</td><td class="fgtext" colspan="3"><input type="text" name="stat_pix" value="{VAR:stat_pix}" class="small_button">pix</td></tr>
<tr><td class="fgtext" colspan="1"><input type="submit" name="save_data"  value="Salvesta" class="small_button"></td>
<td class="fgtext" align="right" colspan="3">
<input type="submit"   value="Kustuta" class="small_button" OnClick="sendcmd('delpart');">
</td>
</tr>

<tr>
<td class="title" align="center"><b><a>Alias</a></b></td>
<td class="title" width="50%" colspan="2" align="center">Andmed</td>
<td class="title" align="center">Vali</td>
</tr>
<!-- SUB: statd -->
<tr>
<td class="title">{VAR:alias}</td>
<td class="title" colspan="2">{VAR:display}</td>
<td class="title" align="center"><input type="checkbox" name="sel[]" value="{VAR:nr}" class="small_button"></td>
</tr>
<!-- END SUB: statd -->

<tr><td class="fgtext" colspan="4" >&nbsp;</td></tr>

<tr>
<td class="ftitle2" colspan="4">Lisa </td></tr>
<tr><td class="fgtitle" colspan="2">funktsioon</td><td class="fgtext" colspan="2">
<select name="func" class="small_button">
<option value="sum">sum</option>
<option value="avg">avg</option>
<option value="min">min</option>
<option value="max">max</option>
</select></td></tr>

<tr><td class="fgtitle" colspan="2">väli</td><td class="fgtext" colspan="2">
<select name="field" class="small_button">
{VAR:fields}
</select>
</td></tr>
<tr><td class="fgtext" colspan="4">
<input type="submit" value="Lisa" OnClick="sendcmd('addpart');" class="small_button">
</td></tr>
</table>

</table>
</form>