<!-- SUB: SHOW_TITLE -->
<p class="title"><a href="{VAR:document_link}">{VAR:title}</a></p>
<!-- END SUB: SHOW_TITLE -->

<!-- SUB: ablock -->
<p class="author">teksti autor: {VAR:author} {VAR:modified}</p>
<!-- END SUB: ablock -->

{VAR:text}


<!-- SUB: image -->
<div style="width: {VAR:width}px;" class="image image_{VAR:alignstr}">
<div style="width: {VAR:width}px;">
<span class="author">
	<!-- SUB: HAS_AUTHOR -->
	(FOTO: {VAR:author})
	<!-- END SUB: HAS_AUTHOR -->
</span>
<a href="<?php echo strlen('{VAR:bigurl}') > 0 ? '{VAR:bigurl}' : '{VAR:imgref}'; ?>" title="{VAR:imgcaption}" class="thickbox" rel="gallery-aw"><img src="{VAR:imgref}" alt="{VAR:alt}" /></a>
{VAR:imgcaption}
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
<a href="{VAR:plink}" title="{VAR:imgcaption}"><img src="{VAR:imgref}" alt="{VAR:alt}" /></a>
{VAR:imgcaption}
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
<a href="{VAR:bigurl}" title="{VAR:imgcaption}" class="thickbox" rel="gallery-aw"><img src="{VAR:imgref}" alt="{VAR:alt}" /></a>
{VAR:imgcaption}
</div>
</div>
<!-- END SUB: image_has_big -->
 
<!-- SUB: file -->
<img alt="faili ikoon" title="faili ikoon" src="{VAR:file_icon}">
<a href="{VAR:file_url}">{VAR:file_name}</a>
<!-- END SUB: file -->