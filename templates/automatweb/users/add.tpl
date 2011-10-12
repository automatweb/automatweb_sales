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
	document.ua.pass.value = pwd;
	document.ua.pass2.value = pwd;
	document.ua.genpwd.value = pwd;
}
</script>
<form method="POST" ACTION='reforb{VAR:ext}' name='ua'>
{VAR:error}

<style>
.tablehead {
font-family: Verdana;
font-size: 10px;
font-weight: bold;
background-color: #00988F;
text-align: center;
}

.tablehead a {color: #FFFFFF; text-decoration:none;}
.tablehead a:hover {color: #FFFFFF; text-decoration:underline;}

.tabletext {
font-family: Verdana;
font-size: 10px;
background-color: #FFFFFF;
}
.tabletext a {color: #191F58; text-decoration:underline;}
.tabletext a:hover {color: #00988F; text-decoration:underline;}

.tabletext2 {
font-family: Verdana;
font-size: 10px;
background-color: #EFEFEF;
}
.tabletext2 a {color: #191F58; text-decoration:underline;}
.tabletext2 a:hover {color: #00988F; text-decoration:underline;}
</style>

<table border=0 cellspacing=1 cellpadding=2 bgcolor="#A5DAD8">
<tr>
<td class="tabletext">Nimi liitumisformist:</td>
<td class="tabletext">{VAR:name}</td>
</tr>
<tr>
<td class="tabletext">Kasutajanimi:</td>
<td class="tabletext"><input type="text" name="a_uid" VALUE='{VAR:uid}'></td>
</tr>
<tr>
<td class="tabletext">E-mail:</td>
<td class="tabletext"><input type="text" name="email" VALUE='{VAR:email}'></td>
</tr>
<tr>
<td class="tabletext">Parool:</td>
<td class="tabletext"><input type="password" name="pass"> (<a href='#' onClick='gp();'>Genereeri</a>)</td>
</tr>
<tr>
<td class="tabletext">Parool veelkord:</td>
<td class="tabletext"><input type="password" name="pass2"></td>
</tr>
<tr>
<td class="tabletext">Genereeritud parool:</td>
<td class="tabletext"><input type="text" name="genpwd"></td>
</tr>
<tr>
<td class="tabletext">Saada tervitusmeil:</td>
<td class="tabletext"><input type="checkbox" name="send_welcome_mail" value="1"></td>
</tr>
<tr>
<td class="tabletext" align="center" colspan="2">
<input type="submit" value="Edasi">
{VAR:reforb}
</td>
</tr>
</table>
</form>
