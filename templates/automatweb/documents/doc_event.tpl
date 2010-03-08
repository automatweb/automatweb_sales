<table width="100%" border="0" cellpadding="0" cellspacing="0">

<tr>
   <td bgcolor="#CED1D3">
   
   <table width="100%" border="0" cellpadding="2" cellspacing="0">

   <tr><td class="aa_title">{VAR:edate}</td>
   <td align="right" class="textmiddle">
   <!-- SUB: PREV -->
   <a href="{VAR:prevlink}"><img SRC="{VAR:baseurl}/img/nool_back.gif" WIDTH="10" HEIGHT="10" BORDER="0" ALT=""><b>Previous</b></a>
   <!-- END SUB: PREV -->
   <!-- SUB: NO_PREV -->
   <img SRC="{VAR:baseurl}/img/nool_back.gif" WIDTH="10" HEIGHT="10" BORDER="0" ALT=""><b>Previous</b>
   <!-- END SUB: NO_PREV -->
   |
   <!-- SUB: NEXT -->
  <a href="{VAR:nextlink}"><b>Next</b><img SRC="{VAR:baseurl}/img/nool_ffd.gif" vspace="0"
WIDTH="10" HEIGHT="10" BORDER="0" ALT=""></a>
   <!-- END SUB: NEXT -->
   <!-- SUB: NO_NEXT -->
  <b>Next</b><img SRC="{VAR:baseurl}/img/nool_ffd.gif" vspace="0" WIDTH="10" HEIGHT="10" BORDER="0" ALT="">
   <!-- END SUB: NO_NEXT -->
  </td></tr>
  </table>

   </td>
  </tr>
  </table>

<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr>
<td>

<span class="text">
{VAR:lead}{VAR:text}<br><br>
{VAR:moreinfo}
</span>
</td>
</tr>
</table>

<!-- SUB: image -->
<table width="1%" border=0 cellpadding=0 cellspacing=10 {VAR:align}>
	<tr>
		<td><img src='{VAR:imgref}'></td>
	</tr>
	<tr>
		<td class="textsmall">{VAR:imgcaption}</td>
	</tr>
</table>
<!-- END SUB: image -->

<!-- SUB: image_linked -->
<table width="1%" border=0 cellpadding=0 cellspacing=10 {VAR:align}>
	<tr>
		<td><a {VAR:target} href='{VAR:plink}'><img border=0 src='{VAR:imgref}'></a></td>
	</tr>
	<tr>
		<td class="textsmall">{VAR:imgcaption}</td>
	</tr>
</table>
<!-- END SUB: image_linked -->

<!-- SUB: link -->
<a {VAR:target} href='{VAR:url}'>{VAR:caption}</a>
<!-- END SUB: link -->


<!-- SUB: image_has_big -->
<table width="1%" border=0 cellpadding=0 cellspacing=10 {VAR:align}>
	<tr>
		<td><a href="javascript:MM_openBrWindow({VAR:w_big_width},{VAR:w_big_height},'{VAR:bi_show_link}')"><img border="0" src='{VAR:imgref}'></a></td>
	</tr>
	<tr>
		<td class="textsmall">{VAR:imgcaption}</td>
	</tr>
</table>
<!-- END SUB: image_has_big -->


<!-- SUB: image_has_big_linked -->
<table width="1%" border=0 cellpadding=0 cellspacing=10 {VAR:align}>
	<tr>
		<td><a href='{VAR:bigurl}'><img border="0" src='{VAR:imgref}'></a></td>
	</tr>
	<tr>
		<td class="textsmall">{VAR:imgcaption}</td>
	</tr>
</table>
<!-- END SUB: image_has_big_linked -->

