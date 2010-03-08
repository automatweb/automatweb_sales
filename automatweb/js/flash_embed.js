function f(url, w, h, wmode)
{
	document.writeln('<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" style="overflow:hidden; width:'+w+'px; height:'+h+'px;" width="'+w+'" height="'+h+'">');
	document.writeln('<param name="movie" value="'+url+'" />');
	document.writeln('<param name="quality" value="high" />');
	document.writeln('<param name="name" value="movie" />');
	document.writeln('<param name="swLiveConnect" value="true" />');
	if(typeof(wmode) != 'undefined')
	{
		document.writeln('<param name="wmode" value="transparent" />');
		document.writeln('<embed src="'+url+'" wmode="transparent" style="overflow:hidden; width:'+w+'px; height:'+h+'px;" quality="high" width="'+w+'" height="'+h+'" align="middle" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />');
	}
	else
	{
		document.writeln('<embed src="'+url+'" quality="high" width="'+w+'" height="'+h+'" align="middle" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />');
	}
	document.writeln('</object>');
}