<script language='javascript'>

el = "color";

function set_color(color) 
{
	if (el == "color")
		document.forms[0].elements[7].value="#"+color;
	else
	if (el == "bgcolor")
		document.forms[0].elements[8].value="#"+color;
} 

function pick_color(el_id) 
{
	el = el_id;
  win=window.open("{VAR:baseurl}/automatweb/orb{VAR:ext}?class=css&action=colorpicker","colorpicker","HEIGHT=220,WIDTH=310")
 	aken.focus()
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
<td class="celltext"><input class="formtext" type='text' NAME='name' VALUE='{VAR:name}'></td>
</tr>
<tr>
<td class="celltext">CSS class:</td>
<td class="celltext"><input class="formtext" type='text' NAME='st[css_class]' VALUE='{VAR:css_class}'></td>
</tr>
<tr>
<td class="celltext">{VAR:LC_STYLE_COMMENT}:</td>
<td class="celltext"><textarea class="formtext" NAME='comment' cols=50 rows=5>{VAR:comment}</textarea></td>
</tr>
	<tr>
		<td class="celltext">Font:</td>
		<td class="celltext">
			<select class="small_button"  NAME='st[font1]'>{VAR:font1}</select>
			<select class="small_button" NAME='st[font2]'>{VAR:font2}</select>
			<select class="small_button" NAME='st[font3]'>{VAR:font3}</select>
		</td>
	</tr>
	<tr>
		<td class="celltext">{VAR:LC_STYLE_FONT_SIZE}:</td>
		<td class="celltext">
			<select class="small_button" NAME='st[fontsize]'>{VAR:fontsize}</select>
		</td>
	</tr>
	<tr>
		<td class="celltext">{VAR:LC_STYLE_COLOR}</td>
		<td class="celltext"><input class="formtext" type="text" name="st[color]" VALUE='{VAR:color}'> <a href="#" onclick="pick_color('color');">{VAR:LC_STYLE_CHOOSE_COLOR}</a></td>
	</tr>
	<tr>
		<td class="celltext">{VAR:LC_STYLE_BACK_COLOR}:</td>
		<td class="celltext"><input type="text" class="formtext" name="st[bgcolor]" VALUE='{VAR:bgcolor}'> <a href="#" onclick="pick_color('bgcolor');">{VAR:LC_STYLE_CHOOSE_COLOR}</a></td>
	</tr>
	<tr>
		<td class="celltext">{VAR:LC_STYLE_FONT_STYLE}:</td>
		<td class="celltext">
			<select class="small_button" NAME='st[fontstyle]'>{VAR:fontstyles}</select>
		</td>
	</tr>
	<tr>
		<td class="celltext">Align:</td>
		<td class="celltext"><input type="radio" name="st[align]" VALUE='left' {VAR:align_left}>{VAR:LC_STYLE_LEFT} <input type="radio" name="st[align]" VALUE='center' {VAR:align_center}>{VAR:LC_STYLE_MIDDLE} <input type="radio" name="st[align]" VALUE='right' {VAR:align_right}>{VAR:LC_STYLE_RIGHT}  <input type="radio" name="st[align]" VALUE=''>Defineerimata</td>
	</tr>
	<tr>
		<td class="celltext">Valign:</td>
		<td class="celltext"><input type="radio" name="st[valign]" VALUE='top' {VAR:valign_top}>{VAR:LC_STYLE_UP} <input type="radio" name="st[valign]" VALUE='center' {VAR:valign_center}>{VAR:LC_STYLE_MIDDLE} <input type="radio" name="st[valign]" VALUE='bottom' {VAR:valign_bottom}>{VAR:LC_STYLE_DOWN}</td>
	</tr>
	<tr>
		<td class="celltext">{VAR:LC_STYLE_HEIGHT}:</td>
		<td class="celltext"><input class="formtext" type="text" name="st[height]" VALUE='{VAR:height}'></td>
	</tr>
	<tr>
		<td class="celltext">{VAR:LC_STYLE_WITHD}:</td>
		<td class="celltext"><input type="text" class="formtext" name="st[width]" VALUE='{VAR:width}'></td>
	</tr>
	<tr>
		<td class="celltext">Nowrap:</td>
		<td class="celltext"><input type="checkbox" name="st[nowrap]" VALUE=1 {VAR:nowrap}></td>
	</tr>
	<tr>
		<td class="celltext">K&uuml;lastatud lingi stiil:</td>
		<td class="celltext"><select class="small_button" name="st[visited]">{VAR:visited}</select></td>
	</tr>
	<tr>
		<td class="celltext">CSS spetsiifiline:</td>
		<td class="celltext"><textarea name="st[css_text]" cols="50" rows="5">{VAR:css_text}</textarea></td>
	</tr>
</table>
{VAR:reforb}
</form>
								
