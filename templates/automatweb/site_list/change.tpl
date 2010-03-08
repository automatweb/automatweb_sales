<form action='reforb.{VAR:ext}' method=post name="add">

<!--tabelraam-->
<table width="100%" cellspacing="0" cellpadding="1">
<tr><td class="tableborder">

	<!--tabelshadow-->
	<table width="100%" cellspacing="0" cellpadding="0">
	<tr><td width="1" class="tableshadow"><IMG SRC="images/trans.gif" WIDTH="1" HEIGHT="1" BORDER=0 ALT=""></td><td class="tableshadow"><IMG SRC="images/trans.gif" WIDTH="1" HEIGHT="1" BORDER=0 ALT=""><br>
		<!--tabelsisu-->
		<table width="100%" cellspacing="0" cellpadding="0">
		<tr><td><td class="tableinside" height="29">


<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr><td width="5"><IMG SRC="images/trans.gif" WIDTH="5" HEIGHT="1" BORDER=0 ALT=""></td>
<td>


<table border="0" cellpadding="0" cellspacing="0">
<tr><td class="icontext" align="center"><input type='image' src="{VAR:baseurl}/automatweb/images/blue/big_save.gif" width="32" height="32" border="0" VALUE='submit' CLASS="small_button"><br>
<a href="javascript:document.add.submit()">Salvesta</a></td></tr></table>


</td>
</tr>
</table>


<br>


<table class="aste01" cellpadding=3 cellspacing=1 border=0>
	<tr>
		<td class="celltext">ID:</td><td class="celltext">{VAR:id}</td>
	</tr>
	<tr>
		<td class="celltext">Nimi:</td><td class="celltext"><input type='text' NAME='name' size=50 VALUE='{VAR:name}' class="formtext"></td>
	</tr>
	<tr>
		<td class="celltext">URL:</td><td class="celltext"><input type='text' NAME='url' size=50 VALUE='{VAR:url}' class="formtext"></td>
	</tr>
	<tr>
		<td class="celltext">Server:</td><td class="celltext"><select NAME='server_id' class="formselect">{VAR:server_id}</select></td>
	</tr>
	<tr>
		<td class="celltext">Used?</td><td class="celltext"><input type='checkbox' NAME='site_used' VALUE='1' {VAR:site_used} class="formtext"></td>
	</tr>
	<tr>
		<td class="celltext">Code branch:</td><td class="celltext"><input type='text' NAME='code_branch' VALUE='{VAR:code_branch}' class="formtext" size="60"></td>
	</tr>
	<tr>
		<td class="celltext">Basedir:</td><td class="celltext">{VAR:basedir}</td>
	</tr>
	<tr>
		<td class="celltext">Updater:</td><td class="celltext">{VAR:updater_uid}</td>
	</tr>
	<tr>
		<td class="celltext">Last update:</td><td class="celltext">{DATE:last_update|d.m.Y / H:i}</td>
	</tr>
</table>







		</td>
		</tr>
		</table>

	</td>
	</tr>
	</table>

</td>
</tr>
</table>

{VAR:reforb}
</form>


