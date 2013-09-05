{VAR:comment}

<script type="text/javascript" src="{VAR:baseurl}automatweb/js/jquery/plugins/blueimp/blueimp-gallery.js"></script>

<!-- SUB: IMAGE -->
<a href="{VAR:imgref}" title="{VAR:alt}" data-gallery="">
	<img src="{VAR:imgref}" alt="{VAR:alt}" style="max-width: 100px; max-height: 100px;">
</a>
<!-- END SUB: IMAGE -->

<!-- SUB: THUMBNAILS -->
<!-- The container for the list of example images -->
<div id="links" class="text-center">
	<!-- SUB: ROW -->
		<!-- SUB: COL -->
			{VAR:imgcontent}
		<!-- END SUB: COL -->
	<!-- END SUB: ROW -->
</div>

<!-- The Gallery as lightbox dialog, should be a child element of the document body -->
<div id="blueimp-gallery" class="blueimp-gallery">
    <div class="slides"></div>
    <h3 class="title"></h3>
    <a class="prev">‹</a>
    <a class="next">›</a>
    <a class="close">×</a>
    <a class="play-pause"></a>
    <ol class="indicator"></ol>
</div>

<script>
document.getElementById('links').onclick = function (event) {
    event = event || window.event;
    var target = event.target || event.srcElement,
        link = target.src ? target.parentNode : target,
        options = {index: link, event: event},
        links = this.getElementsByTagName('a');
	console.log(links, options);
    blueimp.Gallery(links, options);
};
blueimp.Gallery(
    document.getElementById('links').getElementsByTagName('a'),
    {
        container: '#blueimp-gallery-carousel',
        carousel: true
    }
);
</script>
<!-- END SUB: THUMBNAILS -->


<!-- SUB: SLIDESHOW -->
<style>
.blueimp-gallery-carousel {
	-moz-box-shadow: 0 0 5px rgba(0, 0, 0, 0.5);
	-webkit-box-shadow: 0 0 5px rgba(0, 0, 0, 0.5);
	box-shadow: 0 0 5px rgba(0, 0, 0, 0.5);
}
</style>

<div id="links" style="display: none">
	<!-- SUB: ROW -->
		<!-- SUB: COL -->
			{VAR:imgcontent}
		<!-- END SUB: COL -->
	<!-- END SUB: ROW -->
</div>

<div id="blueimp-gallery-carousel" class="blueimp-gallery blueimp-gallery-carousel">
    <div class="slides"></div>
    <h3 class="title"></h3>
    <a class="prev">‹</a>
    <a class="next">›</a>
    <a class="play-pause"></a>
</div>

<script>
blueimp.Gallery(
    document.getElementById('links').getElementsByTagName('a'),
    {
        container: '#blueimp-gallery-carousel',
        carousel: true
    }
);
</script>
<!-- SUB: SLIDESHOW -->
