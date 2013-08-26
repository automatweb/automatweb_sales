<!-- SUB : PAGESELECTOR -->
Vali lehek&uuml;lg:
<!-- SUB : PAGE -->
<a href='{VAR:page_link}'>{VAR:page_nr}</a>
<!-- END SUB : PAGE -->

<!-- SUB : PAGE_SEL -->
{VAR:page_nr}
<!-- END SUB : PAGE_SEL -->

<!-- SUB : PAGE_SEPARATOR -->
|
<!-- END SUB : PAGE_SEPARATOR -->

<!-- END SUB : PAGESELECTOR -->

<style type="text/style" src="{VAR:baseurl}automatweb/css/jquery/blueimp/css/blueimp-gallery.min.css"></style>
<script type="text/javascript" src="{VAR:baseurl}automatweb/js/jquery/plugins/blueimp/jquery.blueimp-gallery.min.js"></script>

<!-- The Gallery as inline carousel, can be positioned anywhere on the page -->
<div id="blueimp-gallery-carousel" class="blueimp-gallery blueimp-gallery-carousel">
    <div class="slides"></div>
    <h3 class="title"></h3>
    <a class="prev">Ü</a>
    <a class="next">Ý</a>
    <a class="play-pause"></a>
    <ol class="indicator"></ol>
</div>

<div id="links">
    <a href="http://intra.notar.dev.automatweb.com/orb.aw/class=image/action=show/fastcall=1/file=56e67e3d5da5ddbb11bec85c97e87576.jpeg" title="Banana">
        <img src="http://intra.notar.dev.automatweb.com/orb.aw/class=image/action=show/fastcall=1/file=56e67e3d5da5ddbb11bec85c97e87576.jpeg" alt="Banana">
    </a>
    <a href="http://intra.notar.dev.automatweb.com/orb.aw/class=image/action=show/fastcall=1/file=20ae1add98b889a569fb90c4a5423b19.jpeg" title="Apple">
        <img src="http://intra.notar.dev.automatweb.com/orb.aw/class=image/action=show/fastcall=1/file=20ae1add98b889a569fb90c4a5423b19.jpeg" alt="Apple">
    </a>
    <a href="http://intra.notar.dev.automatweb.com/orb.aw/class=image/action=show/fastcall=1/file=f63a0a423cfea08931712b4405473379.jpeg" title="Orange">
        <img src="http://intra.notar.dev.automatweb.com/orb.aw/class=image/action=show/fastcall=1/file=f63a0a423cfea08931712b4405473379.jpeg" alt="Orange">
    </a>
</div>

<script>
document.getElementById('links').onclick = function (event) {
    event = event || window.event;
    var target = event.target || event.srcElement,
        link = target.src ? target.parentNode : target,
        options = {index: link, event: event},
        links = this.getElementsByTagName('a');
    blueimp.Gallery(links, options);
};
</script><script>
blueimp.Gallery(
    document.getElementById('links').getElementsByTagName('a'),
    {
        container: '#blueimp-gallery-carousel',
        carousel: true
    }
);
</script>

<table border="0" cellpadding="0" cellspacing="10" width="100%" >
<!-- SUB: ROW -->
	<tr>
		<!-- SUB: COL -->
		<td valign="top" align="center" class="mgalimg">{VAR:imgcontent}</td>
		<!-- END SUB: COL -->
	</tr>
<!-- END SUB: ROW -->

<!-- SUB: FOLDER_CHANGE -->
<tr>
<td colspan="{VAR:col_count}"><b>{VAR:folder_name}</b></td
>
</tr>
<!-- END SUB: FOLDER_CHANGE -->

</table>



<!-- SUB: IMAGE -->
<img title="{VAR:alt}" alt="{VAR:alt}" src="{VAR:imgref}">
<!-- END SUB: IMAGE -->

<!-- SUB: IMAGE_BIG_LINKED -->
<a {VAR:target} href="{VAR:plink}"><img title="{VAR:alt}" alt="{VAR:alt}" src="{VAR:imgref}"></a>
<!-- END SUB: IMAGE_BIG_LINKED -->

<!-- SUB: IMAGE_HAS_BIG -->
<a href="JavaScript: void(0)" onclick="window.open('{VAR:bi_show_link}','popup','width={VAR:big_width},height={VAR:big_height}');"><img src="{VAR:imgref}" alt="{VAR:alt}" title="{VAR:alt}" border="0"></a>
<!-- END SUB: IMAGE_HAS_BIG -->

<!-- SUB: IMAGE_LINKED -->
<a {VAR:target} href="{VAR:plink}"><img title="{VAR:alt}" alt="{VAR:alt}" src="{VAR:imgref}"></a>
<!-- END SUB: IMAGE_LINKED -->
