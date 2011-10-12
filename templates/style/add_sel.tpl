<form action='reforb{VAR:ext}' METHOD=post name="aa">
<!--tabelraam-->
<table width="100%" cellspacing="0" cellpadding="1">
<tr><td class="tableborder">

	<!--tabelshadow-->
	<table width="100%" cellspacing="0" cellpadding="0">
	<tr><td width="1" class="tableshadow"><IMG SRC="{VAR:baseurl}/automatweb/images/trans.gif" WIDTH="1" HEIGHT="1" BORDER=0 ALT=""></td><td class="tableshadow"><IMG SRC="{VAR:baseurl}/automatweb/images/trans.gif" WIDTH="1" HEIGHT="1" BORDER=0 ALT=""><br>
		<!--tabelsisu-->
		<table width="100%" cellspacing="0" cellpadding="0">
		<tr><td><td class="tableinside">


<table border="0" cellpadding="0" cellspacing="2">
<tr>
<td align="center" class="icontext"><IMG SRC="{VAR:baseurl}/images/trans.gif" WIDTH="2" HEIGHT="2" BORDER=0 ALT=""><br><a href="javascript:this.document.aa.submit();"
onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('save','','{VAR:baseurl}/automatweb/images/blue/awicons/save_over.gif',1)"><img name="save" alt="Salvesta" border="0" SRC="{VAR:baseurl}/automatweb/images/blue/awicons/save.gif" width="25" height="25"></a><br><a
href="javascript:this.document.aa.submit();">Salvesta</a>
</td></tr>
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


<table border=0 cellpadding=2 cellspacing=1>
<tr>
	<td align=center>


<table border=0 cellspacing=1 cellpadding=1>
	<tr>
		<td class="celltext">{VAR:LC_STYLE_NAME}:</td>
		<td class="celltext"><input type="text" name="name" VALUE=''></td>
	</tr>
	<tr>
		<td class="celltext">{VAR:LC_STYLE_COMMENT}:</td>
		<td class="celltext"><textarea name=comment cols=50 rows=5></textarea></td>
	</tr>
	<tr>
		<td class="celltext" colspan=2>{VAR:LC_STYLE_CHOOSE_TYPE}:</td>
	</tr>
	<tr>
		<td class="celltext">{VAR:LC_STYLE_TABLE_STYLE}</td>
		<td class="celltext"><input type="radio" name="type" VALUE='0' CHECKED></td>
	</tr>
	<tr>
		<td class="celltext">{VAR:LC_STYLE_CELL_STYLE}</td>
		<td class="celltext"><input type="radio" name="type" VALUE='1'></td>
	</tr>
</table>
</td>
</tr>
</table>
{VAR:reforb}
</form>
								