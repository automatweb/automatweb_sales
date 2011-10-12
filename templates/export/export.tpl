<form name='q' method="POST" action='reforb{VAR:ext}'>
<!--tabelraam-->
<table width="100%" cellspacing="0" cellpadding="1">
<tr><td class="tableborder">

	<!--tabelshadow-->
	<table width="100%" cellspacing="0" cellpadding="0">
	<tr><td width="1" class="tableshadow"><IMG SRC="{VAR:baseurl}/automatweb/images/trans.gif" WIDTH="1" HEIGHT="1" BORDER=0 ALT=""></td><td class="tableshadow"><IMG SRC="{VAR:baseurl}/automatweb/images/trans.gif" WIDTH="1" HEIGHT="1" BORDER=0 ALT=""><br>
		<!--tabelsisu-->
		<table width="100%" cellspacing="0" cellpadding="0">
		<tr><td><td class="tableinside" height="29">


<table border="0" cellpadding="0" cellspacing="0">
<tr><td width="5"><IMG SRC="{VAR:baseurl}/images/trans.gif" WIDTH="5" HEIGHT="1" BORDER=0 ALT=""></td>




<td class="celltitle">&nbsp;<b>Saidi export | <a href='{VAR:gen_url}'>Ekspordi</a> | <a href='{VAR:pick_active}'>Vali aktiivne versioon</a> | <a href='{VAR:view_log}'>Vaata export
logi</a>&nbsp;| <a href='{VAR:iexp_url}'>Interaktiivne eksport</a></td>
<td align="left"><!--save--><IMG SRC="{VAR:baseurl}/images/trans.gif" WIDTH="4" HEIGHT="1" BORDER=0 ALT=""><a href="javascript:document.q.submit()" 
onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('save','','{VAR:baseurl}/automatweb/images/blue/awicons/save_over.gif',1)"><img name="save" alt="Salvesta" border="0" SRC="{VAR:baseurl}/automatweb/images/blue/awicons/save.gif" width="25" height="25"></a></td>
</tr></table>


		</td></tr></table>
	</td></tr></table>
</td></tr></table>


<table border="0" cellspacing="0" cellpadding="0" width=100%>
<tr>
<td bgcolor="#FFFFFF">

<table border="0" cellspacing="1" cellpadding="2" width=100%>
	<tr class="aste05">
		<td colspan="3" class="celltext">Faili ja katalooginimedes v&otilde;ib kasutada muutujaid:<Br>
		%y - aasta<Br>
		%m - kuu<br>
		%d - p&auml;ev<br>
		%h - tund<br>
		%n - minut<br>
		%s - sekund</td>
	</tr>
	<tr class="aste05">
		<td class="celltext">Kataloog serveris:</td>
		<td colspan="2" class="celltext"><input type='text' class='formtext' name='folder' value='{VAR:folder}'> URL: ({VAR:url})</td>
	</tr>
	<tr class="aste05">
		<td class="celltext">Fail serveris, kuhu export kokku pakkida:</td>
		<td colspan="2" class="celltext"><input type='text' class='formtext' name='zip_file' value='{VAR:zip_file}'></td>
	</tr>
	<tr class="aste05">
		<td class="celltext">Kataloog AW's, kuhu export kokku pakkida:</td>
		<td colspan="2" class="celltext"><select class='formselect' name='aw_zip_folder'>{VAR:aw_zip_folder}</select></td>
	</tr>
	<tr class="aste05">
		<td class="celltext">Objekti nimi AW's, kuhu export kokku pakkida:</td>
		<td colspan="2" class="celltext"><input type='text' class='formtext' name='aw_zip_fname' value='{VAR:aw_zip_fname}'></td>
	</tr>
	<tr class="aste05">
		<td class="celltext">Kas export tehakse automaatselt:</td>
		<td colspan="2" class="celltext"><input type='checkbox' class='formtext' name='automatic' value='1' {VAR:automatic}> <a href='javascript:remote("no",500,500,"{VAR:sel_period}")'>Vali kordused automaatseks ekspordiks</a></td>
	</tr>
	<tr class="aste05">
		<td class="celltext">Kas sait k&auml;ib staatilise koopia pealt:</td>
		<td colspan="2" class="celltext"><input type='checkbox' class='formtext' name='static_site' value='1' {VAR:static_site}></td>
	</tr>
	<tr class="aste05">
		<td class="celltext">Kuidas tehakse failide nimed:</td>
		<td colspan="2" class="celltext"><input type='radio' class='formradio' name='fn_type' value='1' {VAR:fn_type_1}> sektsiooni id <input type='radio' class='formradio' name='fn_type' value='2' {VAR:fn_type_2}> men&uuml;&uuml; nimi <input type='radio' class='formradio' name='fn_type' value='3' {VAR:fn_type_3}> hash <input type='radio' class='formradio' name='fn_type' value='4' {VAR:fn_type_4}> men&uuml;&uuml;aliased </td>
	</tr>
	<tr class="aste05">
		<td class="celltext">Kataloogid millele saab ruule teha:</td>
		<td colspan="2" class="celltext"><select class='formselect' name='rule_folders[]' multiple size=20>{VAR:rule_folders}</select></td>
	</tr>
	<tr class="aste05">
		<td class="celltext">Public kataloogi symlingi nimi:</td>
		<td colspan="2" class="celltext"><input type='text' class='formtext' name='public_symlink_name' value='{VAR:public_symlink_name}'></td>
	</tr>
</table>

</td>
</tr>
</table>
{VAR:reforb}
</form>