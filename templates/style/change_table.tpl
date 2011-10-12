<script language='javascript'>

function set_color(color) 
{
	document.forms[0].elements[2].value="#"+color;
} 

function pick_color() 
{
  win=window.open("{VAR:baseurl}/automatweb/orb{VAR:ext}?class=css&action=colorpicker","colorpicker","HEIGHT=220,WIDTH=310")
 	win.focus()
}
</script>

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
<td class="celltext"><input type='text' NAME='name' class="formtext" VALUE='{VAR:name}'></td>
</tr>
<tr>
<td class="celltext">{VAR:LC_STYLE_COMMENT}:</td>
<td class="celltext"><textarea NAME='comment' class="formtext" cols=50 rows=5>{VAR:comment}</textarea></td>
</tr>
<tr>
<td class="celltext">{VAR:LC_STYLE_BACK_COLOR}:</td>
<td class="celltext"><input type='text' NAME='st[bgcolor]' class="formtext" VALUE='{VAR:bgcolor}'> <a href="#" onclick="pick_color();">{VAR:LC_STYLE_CHOOSE_COLOR}</a></td>
</tr>
<tr>
<td class="celltext">{VAR:LC_STYLE_EDGE_WIDTH}:</td>
<td class="celltext"><input type='text' NAME='st[border]' class="formtext" VALUE='{VAR:border}'></td>
</tr>
<tr>
<td class="celltext">cellpadding:</td>
<td class="celltext"><input type='text' NAME='st[cellpadding]' class="formtext" VALUE='{VAR:cellpadding}'></td>
</tr>
<tr>
<td class="celltext">cellspacing:</td>
<td class="celltext"><input type='text' NAME='st[cellspacing]' class="formtext" VALUE='{VAR:cellspacing}'></td>
</tr>
<tr>
<td class="celltext">{VAR:LC_STYLE_HEIGHT}:</td>
<td class="celltext"><input type='text' NAME='st[height]' class="formtext" VALUE='{VAR:height}'></td>
</tr>
<tr>
<td class="celltext">{VAR:LC_STYLE_WITHD}:</td>
<td class="celltext"><input type='text' NAME='st[width]' class="formtext" VALUE='{VAR:width}'></td>
</tr>
<tr>
<td class="celltext">Hspace:</td>
<td class="celltext"><input type='text' NAME='st[hspace]' class="formtext" VALUE='{VAR:hspace}'></td>
</tr>
<tr>
<td class="celltext">Vspace:</td>
<td class="celltext"><input type='text' NAME='st[vspace]' class="formtext" VALUE='{VAR:vspace}'></td>
</tr>
<tr>
<td class="celltext">{VAR:LC_STYLE_FIRST} <input class="formtext" size=2 type='text' name='st[num_frows]' value='{VAR:num_frows}'> {VAR:LC_STYLE_ROW_DEFAULT}:</td>
<td class="celltext"><select class="small_button" name='st[frow_style]'><option value=''>{VAR:frow_style}</select></td>
</tr>
<tr>
<td class="celltext">{VAR:LC_STYLE_FIRST} <input class="formtext" size=2 type='text' name='st[num_fcols]' value='{VAR:num_fcols}'> {VAR:LC_STYLE_COL_DEFAULT}:</td>
<td class="celltext"><select class="small_button" name='st[fcol_style]'><option value=''>{VAR:fcol_style}</select></td>
</tr>
<tr>
<td class="celltext">Header {VAR:LC_STYLE_STYLE}:</td>
<td class="celltext"><select class="small_button" name='st[header_style]'><option value=''>{VAR:header_style}</select></td>
</tr>
<tr>
<td class="celltext">Footer {VAR:LC_STYLE_STYLE}:</td>
<td class="celltext"><select class="small_button" name='st[footer_style]'><option value=''>{VAR:footer_style}</select></td>
</tr>
<tr>
<td class="celltext">Paaritu rea stiil:</td>
<td class="celltext"><select class="small_button" name='st[odd_style]'><option value=''>{VAR:odd_style}</select></td>
</tr>
<tr>
<td class="celltext">Paaris rea stiil:</td>
<td class="celltext"><select class="small_button" name='st[even_style]'><option value=''>{VAR:even_style}</select></td>
</tr>
</table>
</td>
</tr>
</table>
{VAR:reforb}
</form>
