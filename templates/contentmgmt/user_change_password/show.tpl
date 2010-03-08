<script language="javascript">
function gp()
{
	pwd = new String("");
	for (i = 0; i < 8; i++)
	{
		rv = Math.random()*(123-97);
		rn = parseInt(rv);
		rt = rn+97;
		pwd = pwd + String.fromCharCode(rt);
	}
	document.ua.pwd.value = pwd;
	document.ua.pwd2.value = pwd;
	document.ua.genpwd.value = pwd;
}
</script>

<form action='reforb.{VAR:ext}' method=post name="ua">
<font color="red">{VAR:error}</font>


<table cellpadding=3 cellspacing=1 border=0>
<tr>
<td class="tabletext">Parool:</td><td class="tabletext"><input type='password' NAME='pwd' >  (<a href='#' onClick='gp();'>Genereeri</a>)</td>
</tr>
<tr>
<td class="tabletext">Parool uuesti:</td><td class="tabletext"><input type='password' NAME='pwd2' ></td>
</tr>
<tr>
<td class="tabletext">Genereeritud parool:</td>
<td class="tabletext"><input type="text" name="genpwd"></td>
</tr>
<tr>
<td class="tabletext" colspan=2><input type="submit" value="Salvesta" border="0"></td>
</tr>
</table>
{VAR:reforb}
</form>
