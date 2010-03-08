<style type="text/css">
	#img_popup { display: none; position: absolute; }
</style>

<script type="text/javascript">
	<!--
	function showThumb (event, strImagePath)
	{
		var el = document.getElementById ("img_popup");
		el.src = strImagePath;
		el.style.left = mouseX(event)+25+"px";
		el.style.top = mouseY(event)-10+"px";
		el.style.display = "block";
	}

	function hideThumb ()
	{
		var el = document.getElementById ("img_popup");
		el.src = '';
		el.style.display = "none";
	}

	function mouseX(evt){
		if (evt.pageX) return evt.pageX;
		else if (evt.clientX)
			return evt.clientX + (document.documentElement.scrollLeft ? document.documentElement.scrollLeft : document.body.scrollLeft);
		else return null;
	}

	function mouseY(evt) {
		if (evt.pageY) return evt.pageY;
		else if (evt.clientY)
			return evt.clientY + (document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop );
		else return null;
	}
	-->
</script>
<img src="" id="img_popup" alt="" width="100" />
{VAR:body}