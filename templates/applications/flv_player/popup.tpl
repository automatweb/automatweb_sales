<a class="thickbox" href="#TB_inline?height={VAR:popup_height}&width={VAR:popup_width}&inlineId=container_{VAR:id}">{VAR:name}</a>
<div id="container_{VAR:id}" style="display: none; margin-top: 10px;">
<script type="text/javascript" src="{VAR:baseurl}/automatweb/js/jw_flv_player/swfobject.js"></script>

<p id="{VAR:id}" style="margin-top: 4px;"><a href="http://www.macromedia.com/go/getflashplayer">Flash pleier</a> ei ole installeeritud.</p>
<script type="text/javascript">
	var s1 = new SWFObject("{VAR:baseurl}/automatweb/js/jw_flv_player/flvplayer.swf","single","{VAR:width}","{VAR:height}","7");
	s1.addParam("allowfullscreen","true");
	s1.addVariable("file","{VAR:file}");
	s1.addVariable("width","{VAR:width}");
	s1.addVariable("height","{VAR:height}");
	s1.addVariable("image","{VAR:image_url}");
	s1.write("{VAR:id}");
</script>
</div>