<br>
<script language='javascript'>

function varv(vrv) 
{
	document.forms[0].bgcolor.value="#"+vrv;
} 

function varvivalik() 
{
  aken=window.open("{VAR:baseurl}/automatweb/orb.aw?class=css&action=colorpicker","varvivalik","HEIGHT=220,WIDTH=310")
 	aken.focus()
}
</script>
<form action=reforb.{VAR:ext} method=post>
<table bgcolor="#CCCCCC" cellpadding=3 cellspacing=1 border=0>
<tr>
<td class="fcaption">{VAR:LC_FORMS_BACK_COL}:</td>
<td class="fform"><input type='text' NAME='bgcolor' VALUE='{VAR:form_bgcolor}'> <a href="#" onclick="varvivalik();">{VAR:LC_FORMS_CHOOSE_COL}</a></td>
</tr>
<tr>
<td class="fcaption">{VAR:LC_FORMS_SIDE_WIDTH}:</td>
<td class="fform"><input type='text' NAME='border' VALUE='{VAR:form_border}'></td>
</tr>
<tr>
<td class="fcaption">cellpadding:</td>
<td class="fform"><input type='text' NAME='cellpadding' VALUE='{VAR:form_cellpadding}'></td>
</tr>
<tr>
<td class="fcaption">cellspacing:</td>
<td class="fform"><input type='text' NAME='cellspacing' VALUE='{VAR:form_cellspacing}'></td>
</tr>
<tr>
<td class="fcaption">{VAR:LC_FORMS_HIGHT}:</td>
<td class="fform"><input type='text' NAME='height' VALUE='{VAR:form_height}'></td>
</tr>
<tr>
<td class="fcaption">{VAR:LC_FORMS_WITDH}:</td>
<td class="fform"><input type='text' NAME='width' VALUE='{VAR:form_width}'>(max 316)</td>
</tr>
<tr>
<td class="fcaption">Hspace:</td>
<td class="fform"><input type='text' NAME='hspace' VALUE='{VAR:form_hspace}'></td>
</tr>
<tr>
<td class="fcaption">Vspace:</td>
<td class="fform"><input type='text' NAME='vspace' VALUE='{VAR:form_vspace}'></td>
</tr>
<tr>
<td class="fcaption">{VAR:LC_FORMS_DEFAULT_STYLE}:</td>
<td class="fform"><select NAME='def_style'><option value=''>{VAR:def_style}</select>
</td>
</tr>
<tr>
<td class="fcaption" colspan=2><input class='small_button' type='submit' NAME='save' VALUE='{VAR:LC_FORMS_SAVE}!'></td>
</tr>
</table>
{VAR:reforb}
</form>
