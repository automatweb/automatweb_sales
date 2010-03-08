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
<td class="celltext">{VAR:LC_LANGUAGES_NAME}:</td><td class="celltext"><input type='text' NAME='name' VALUE='{VAR:name}' class="formtext"></td>
</tr>
<tr>
<td class="celltext">{VAR:LC_LANGUAGES_LANGUAGE_ID}:</td><td class="celltext"><input type='text' NAME='acceptlang' VALUE='{VAR:acceptlang}' class="formtext"></td>
</tr>
<tr>
<td class="celltext">{VAR:LC_LANGUAGES_CHARSET}:</td><td class="celltext"><input type='text' NAME='charset' VALUE='{VAR:charset}' class="formtext"></td>
</tr>
<tr>
<td class="celltext" colspan=2>



</td>
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


