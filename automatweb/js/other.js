function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}

function generic_loader()
{
	// don't do anything. screw you.
}

function generic_loader2()
{
	// el bondo mondo
}

function check_generic_loader()
{
	if (generic_loader)
	{
		generic_loader();
	}
	if (generic_loader2)
	{
		generic_loader2();
	}
};

function generic_unloader()
{
	// don't do anything. screw you.
}

function check_generic_unloader()
{
	if (generic_unloader)
	{
		generic_unloader();
	}
};

















window.onscroll = function ()
{
	el=document.getElementById('floatlayer');
	if (el)
	{
		el.style.position='absolute';
		if (document.body.scrollLeft) // opera, ff etc
		{
			el.style.left=document.body.scrollLeft+800+"px";
			el.style.top=document.body.scrollTop+200+"px";
		}
		else if (document.documentElement.scrollTop) //ie
		{
			el.style.left=document.documentElement.scrollLeft+800+"px";
			el.style.top=document.documentElement.scrollTop+200+"px";
		}
	}
}




$(window).unload( function () { 
	check_generic_unloader();
});


$(document).ready(function(){
	// in controller change tab behaviour in textarea so tabs insert tab characters
	if ($.gup("class")=="form_controller")
	{
		$("textarea[name=eq]").EnableTabs();
	}
});