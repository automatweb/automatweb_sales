<!-- SUB: SHOW_TITLE -->
<h1>{VAR:title}</h1>
<!-- END SUB: SHOW_TITLE -->

<!-- SUB: PRINTANDSEND -->
<p class="printlink"><a href="{VAR:printlink}" target="_new"></a></p>
<!-- END SUB: PRINTANDSEND -->

{VAR:text}

<!-- SUB: ablock -->
<p class="author">teksti autor: {VAR:author} {VAR:modified}</p>
<!-- END SUB: ablock -->



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

<!-- SUB: youtube_link -->
<object width="425" height="344"><param name="movie" value="http://www.youtube.com/v/{VAR:video_id}&hl=en"></param><param name="allowFullScreen" value="true"></param><embed src="http://www.youtube.com/v/{VAR:video_id}&hl=en" type="application/x-shockwave-flash" allowfullscreen="true" width="425" height="344"></embed></object>
<!-- END SUB: youtube_link -->
