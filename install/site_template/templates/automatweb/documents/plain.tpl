<!-- SUB: SHOW_TITLE -->
<h1>{VAR:title}</h1>
<!-- END SUB: SHOW_TITLE -->

{VAR:text}

<!-- SUB: PRINTANDSEND -->
<table width="100%" border="0">
	<tr>
		<td align="left">
			<!-- SUB: SHOW_MODIFIED -->
			<span class="kp"><i>{VAR:date_est_fullyear}</i></span>
			<!-- END SUB: SHOW_MODIFIED -->
		</td>
		<td align="right" width="1"> 
			<a href="{VAR:baseurl}/?class=document&amp;action=print&amp;section={VAR:docid}" target="_new"><img src="{VAR:baseurl}/img/icon_22_print.gif" border="0" name="print" alt="Print" /></a>
		</td>
	</tr>
</table>
<!-- END SUB: PRINTANDSEND -->




<!-- SUB: image -->
<div style="width: {VAR:width}px;" class="image image_{VAR:alignstr}">
<div style="width: {VAR:width}px;">
<span class="author">
	<!-- SUB: HAS_AUTHOR -->
	(FOTO: {VAR:author})
	<!-- END SUB: HAS_AUTHOR -->
</span>
<?php echo strlen('{VAR:bigurl}') > 0 ? '<a href="{VAR:bigurl}">' : ''; ?>
<img src="{VAR:imgref}" alt="{VAR:alt}"/>
<?php echo strlen('{VAR:bigurl}') > 0 ? '</a>' : ''; ?>
<p class="caption" style="width: {VAR:width}px;">{VAR:imgcaption}</p>
</div>
</div>
<!-- END SUB: image -->

<!-- SUB: image_linked -->
<div style="width: {VAR:width}px;" class="image image_{VAR:alignstr}">
<div style="width: {VAR:width}px;">
<span class="author">
	<!-- SUB: HAS_AUTHOR -->
	(FOTO: {VAR:author})
	<!-- END SUB: HAS_AUTHOR -->
</span>
<a href="{VAR:plink}" title="{VAR:imgcaption}"><img src="{VAR:imgref}" alt="{VAR:alt}"/></a>
<p class="caption" style="width: {VAR:width}px;">{VAR:imgcaption}</p>
</div>
</div>
<!-- END SUB: image_linked -->

<!-- SUB: image_has_big -->
<div style="width: {VAR:width}px;" class="image image_{VAR:alignstr}">
<div style="width: {VAR:width}px;">
<span class="author">
	<!-- SUB: HAS_AUTHOR -->
	(FOTO: {VAR:author})
	<!-- END SUB: HAS_AUTHOR -->
</span>
<a href="{VAR:bigurl}" title="{VAR:imgcaption}" class="thickbox" rel="gallery-aw"><img src="{VAR:imgref}" alt="Single Image"/></a>
<p class="caption" style="width: {VAR:width}px;">{VAR:imgcaption}</p>
</div>
</div>
<!-- END SUB: image_has_big -->
