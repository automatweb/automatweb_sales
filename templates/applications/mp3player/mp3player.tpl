<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
		   "http://www.w3.org/TR/html4/loose.dtd">
		
<html>
<head>
	<title>MP3 Pleier</title>
	<link rel="stylesheet" type="text/css" href="{VAR:baseurl}/automatweb/js/jw_mp3_player/style.css"> 
	
	<script type="text/javascript" src="{VAR:baseurl}/automatweb/js/jquery/jquery-1.2.3.min.js"></script>
	<script type="text/javascript" src="{VAR:baseurl}/automatweb/js/jw_mp3_player/swfobject.js"></script>
	<script type="text/javascript" src="{VAR:baseurl}/automatweb/js/jw_mp3_player/interaction.js"></script>
	<script type="text/javascript">
	
	 $(document).ready(function(){ 
	 
	 	// update playlist
		$("#filter_playlist").keydown( function (e) { 
			if (e.keyCode == 13)
			{
				data = $("#filter_playlist").serialize();
				
				 $.ajax({
					type: "POST",
					url: "{VAR:baseurl}/?class=mp3player&action=update_playlist&mp3player_oid={VAR:mp3player_oid}",
					data: data,
					async: true,
					success: function(msg){
						loadFile({file:'{VAR:baseurl}/orb.aw/class=mp3player/action=playlist/id={VAR:mp3player_oid}/playlist.xml'})
					},
					error: function(msg){
					 alert( "Andmete salvestamine kahjuks ei õnnestunud");
					}

				});
			}
		});
	});
	
	</script>
</head>
<body>

Versioon {VAR:version}
<input type="text" name="str" id="filter_playlist" value="{VAR:search_string}">
<p id="awplayer"><a href="http://www.macromedia.com/go/getflashplayer">Sikuta</a> flash pleier.</p>
<script type="text/javascript">
	var s2 = new SWFObject("{VAR:baseurl}/automatweb/js/jw_mp3_player/mp3player.swf", "mpl", "250", "450", "7");
	s2.addVariable("file","{VAR:baseurl}/orb.aw/class=mp3player/action=playlist/id={VAR:mp3player_oid}/playlist.xml");
	s2.addVariable("autostart", true);
	s2.addVariable("overstretch", "fit"); // scretch image
	s2.addVariable("repeat", "list");
	s2.addVariable("shuffle", false);
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