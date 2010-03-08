<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
		   "http://www.w3.org/TR/html4/loose.dtd">
		
<html>
<head>
	<title>AW MP3 Pleier</title>
	<script type="text/javascript" src="{VAR:baseurl}/automatweb/js/jw_mp3_player/swfobject.js"></script>
			
	<script type="text/javascript">
	// some variables to save
	var currentPosition;
	var currentVolume;
	var currentItem;
	
	// these functions are caught by the JavascriptView object of the player.
	function sendEvent(typ,prm) { thisMovie("mpl").sendEvent(typ,prm); };
	function getUpdate(typ,pr1,pr2,pid) {
		if(typ == "time") { currentPosition = pr1; }
		else if(typ == "volume") { currentVolume = pr1; }
		else if(typ == "item") { currentItem = pr1; setTimeout("getItemData(currentItem)",100); }
		var id = document.getElementById(typ);
		id.innerHTML = typ+ ": "+Math.round(pr1);
		pr2 == undefined ? null: id.innerHTML += ", "+Math.round(pr2);
		if(pid != "null") {
			document.getElementById("pid").innerHTML = "(received from the player with id <i>"+pid+"</i>)";
		}
	};
	
	// These functions are caught by the feeder object of the player.
	function loadFile(obj) { thisMovie("mpl").loadFile(obj); };
	function addItem(obj,idx) { thisMovie("mpl").addItem(obj,idx); }
	function removeItem(idx) { thisMovie("mpl").removeItem(idx); }
	function getItemData(idx) {
		var obj = thisMovie("mpl").itemData(idx);
		var nodes = "";
		for(var i in obj) { 
			nodes += "<li>"+i+": "+obj[i]+"</li>"; 
		}
		document.getElementById("data").innerHTML = nodes;
	};
	
	// This is a javascript handler for the player and is always needed.
	function thisMovie(movieName) {
	    if(navigator.appName.indexOf("Microsoft") != -1) {
			return window[movieName];
		} else {
			return document[movieName];
		}
	};
	
	</script>
			
	<style type="text/css">
	body, p {margin: 0;padding: 0;}
	</style>
</head>
<body>
		
<p id="awplayer"><a href="http://www.macromedia.com/go/getflashplayer">Sikuta</a> flash pleier.</p>
<script type="text/javascript">
	var s2 = new SWFObject("{VAR:baseurl}/automatweb/js/jw_mp3_player/mp3player.swf", "mpl", "250", "450", "7");
	s2.addVariable("file","{VAR:baseurl}/orb.aw/class=awplayer/action=playlist/id={VAR:awplayer_oid}/playlist.xml");
	s2.addVariable("autostart", true);
	s2.addVariable("overstretch", "fit"); // scretch image
	s2.addVariable("repeat", "list");
	s2.addVariable("autoscroll", false);
	s2.addVariable("shownavigation", true);
	s2.addVariable("enablejs", true);
		s2.addVariable("javascriptid","mpl");
	// for some strange reason this does not work when i use ? after orb.aw and & marks. only / works
	s2.addVariable("callback", "{VAR:baseurl}/orb.aw/class=mp3/action=log_play_statistics/id={VAR:awplayer_oid}");
	//s2.addVariable("displaywidth", 150);
	//s2.addVariable("showeq", true);
	s2.addVariable("backcolor","0x00000");
	s2.addVariable("frontcolor","0xEECCDD");
	s2.addVariable("lightcolor","0xCC0066");
	s2.addVariable("displayheight","200");
	s2.addVariable("width","250");
	s2.addVariable("height","450");
	s2.write("awplayer");
</script>
		
</body>
</html>