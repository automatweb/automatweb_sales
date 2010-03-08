<script src="{VAR:baseurl}/automatweb/js/jquery/jquery-1.2.3.min.js" type="text/javascript"></script>
<script src="{VAR:baseurl}/automatweb/js/jquery/plugins/jquery.jqDnR.js" type="text/javascript"></script>
<style type="text/css">
.jqHandle {
	 height:15px;
}

.jqDrag {
	width: 100%;
	cursor: move;
	background: url(http://www.hanneskirsman.com/automatweb/images/aw06/layout_t.gif) repeat-x;
	height: 25px;
}

.jqResize {
	 width: 12px;
	 height: 12px;
	 position: absolute;
	 bottom: 0;
	 right: 0;
	 cursor: se-resize;
	 background: url(/automatweb/images/resize.gif);
}

.jqDnR {
    z-index: 3;
    position: relative;
    
    width: 180px;
    font-size: 0.77em;
    color: #618d5e;
    margin: 5px 10px 10px 10px;
    padding: 8px;
    background-color: #5FC000;
}
.content {padding: 10px 30px 15px 15px; line-height: 18px ! important;}
</style>

<div id="tpl_equals_1" style="position: absolute; margin: 0 auto; border: 1px solid #0373AC; background: white; color: black; margin-top: 35px; background: white; width: auto; z-index: 9999; font-size: 10px; font-family: Verdana;">
<div class="jqHandle jqDrag"><!-- --></div>
<br />
<div class="content">
<!-- SUB: TEMPLATE -->
using <a href="{VAR:link}" target="_blank">{VAR:text}</a> ({VAR:count})<br />
<!-- END SUB: TEMPLATE -->
</div>
</div>
<script>
$('#tpl_equals_1').jqDrag('.jqDrag').jqResize('.jqResize');
</script>