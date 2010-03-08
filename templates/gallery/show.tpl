<script language="Javascript">
function rremote(ff,w,h) 
{
	var wprops = "toolbar=no,location=0,directories=0,status=0,menubar=0,scrollbars=0,resizable=0,width="+w+",height="+h;
	openwindow = window.open(ff,"remote",wprops);
}
</script>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr><td  bgcolor="#FFFFFF" class="body">
<br>

<!-- SUB: LINE -->
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr align="center" valign="center"> 
		<!-- SUB: IMAGE -->

		<td align="center" valign="center">
		<a {VAR:target} href='{VAR:url}'><img src="{VAR:tnurl}" border="0" color="#FFFFFF"></a><!--	<a href='javascript:rremote("{VAR:bigurl}",{VAR:xsize},{VAR:ysize})'><img src="{VAR:tnurl}" border="1"></a> --></td>
		<!-- END SUB: IMAGE -->
	</tr>
</table>
<br>
<!-- END SUB: LINE -->

</td></tr></table>
<span class="body">
<!-- SUB: PAGES -->
								<br>
								<!-- SUB: PREVIOUS -->
								<a href='{VAR:url}'>
								eelmine </a>
								<!-- END SUB: PREVIOUS -->
								<!-- SUB: PAGE -->
								<a href='{VAR:url}'>{VAR:num} | </a>
								<!-- END SUB: PAGE -->
								<!-- SUB: NEXT -->
								<a href='{VAR:url}'>j&auml;rgmine</a>
								<!-- END SUB: NEXT --></td>

<!-- END SUB: PAGES -->
</span>
