{VAR:comment}

<script type="text/javascript" src="{VAR:baseurl}automatweb/js/jquery/plugins/blueimp/blueimp-gallery.js"></script>

<!-- SUB: IMAGE -->
<a href="{VAR:imgref}" title="{VAR:alt}" data-gallery="" data-comment="{VAR:imgcaption}" data-folder="{VAR:parent_name}" data-author="{VAR:author}">
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
<div id="blueimp-gallery" class="blueimp-gallery blueimp-gallery-controls">
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
        options = {
			index: link,
			event: event,
			onslide: function (index, slide) {
				var $a = $(this.list[index]);
				var $title = $(this.titleElement);
				var title = $a.data("folder") + "<br /><span style=\"font-size: 16px; font-weight: normal; line-height: 1.1em;\">" + ($a.data("comment") ? $a.data("comment").replace("||", "<br />") : $a.attr("title").substr(0, $a.attr("title").lastIndexOf("."))) + "</span>";
				if ($a.data("author")) {
					title += "<br /><span style=\"font-size: 12px; font-weight: normal; line-height: 1.5em;\"><i>Autor: </i><span style=\"text-transform: uppercase;\">" + $a.data("author") + "</span></div>"
				}
				$title.html(title);
			}
		},
        links = this.getElementsByTagName('a');
    blueimp.Gallery(links, options);
};
blueimp.Gallery(
    document.getElementById('links').getElementsByTagName('a'),
    {
        container: '#blueimp-gallery-carousel',
        carousel: true,
		onload: function (a,b,c) { console.log("load", this, a, b, c); },
		onslide: function (a,b,c) { console.log("slide", this, a, b, c); }
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
