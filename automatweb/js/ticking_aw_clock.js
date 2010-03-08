/*
	ticking aw clock v1.0
	
	examples
	with jquery
	
	1. because the separator between hour and minute is not actually
	hidden but color is changed, then u can change those color in the constructor
	
	<script type="text/javascript" src="{VAR:baseurl}/automatweb/js/jquery.js"></script>
	<script type="text/javascript" src="{VAR:baseurl}/automatweb/js/ticking_aw_clock.js"></script>
	<script type="text/javascript">
	$(document).ready(function(){
		new aw_clock({
			"id":"clock",
			"invisible_color":"white",
			"visible_color":"red"
		}); 
		});
	</script>
	
	<div id="clock"></div>
	
	2. without invisible_color and visible_color white and black are used
	
	<script type="text/javascript" src="{VAR:baseurl}/automatweb/js/jquery.js"></script>
	<script type="text/javascript" src="{VAR:baseurl}/automatweb/js/ticking_aw_clock.js"></script>
	<script type="text/javascript">
	 $(document).ready(function(){
		new aw_clock({
			"id":"clock"
		}); 
	 });
	</script>
	
	<div id="clock"></div>
	
	3. and of course u don't have to use jquery
	
	<script type="text/javascript" src="{VAR:baseurl}/automatweb/js/ticking_aw_clock.js"></script>>
	window.onload = function()
	{
		new aw_clock({
			"id":"clock"
		}); 
		});
	}

	
*/
var aw_clock_container_id;
var aw_clock_invisible_color="white";
var aw_clock_visible_color="black";
function aw_clock(arr){
	if (arr["invisible_color"])
	{
		aw_clock_invisible_color = arr["invisible_color"];
	}
	if (arr["visible_color"])
	{
		aw_clock_visible_color = arr["visible_color"];
	}
	aw_clock_container_id = arr["id"];
	aw_clock_showhide();
	setInterval('aw_clock_showhide()',1000)
} 


aw_clock_showhide = function()
{
	el = document.getElementById(aw_clock_container_id);
	old = el.innerHTML;
	dt = new Date();
	newstr = aw_clock_zeropad(dt.getHours().toString()).concat(old.indexOf(aw_clock_visible_color)>0?"<span style='color: "+aw_clock_invisible_color+" ! important;'>:</span>":"<span style='color: "+aw_clock_visible_color+" ! important;'>:</span>",aw_clock_zeropad (dt.getMinutes().toString()));
	el.innerHTML = newstr;
} 

aw_clock_zeropad = function (str)
{
        return str.length == 1 ? 0 + str : str;
}