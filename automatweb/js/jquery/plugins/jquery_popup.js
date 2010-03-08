jQuery.fn.popup = function() 
{
	var els = this;

 	h = get_y();
	els.css("top",(h-els.height() - 4)+"px");

	els.fadeOut(1);
	els.fadeIn(2000);
	els.css("visibility","visible");

	$("a#msg_popup_close").click(function(){
		els.fadeOut(600);
	});

	$(window).resize(function(){
		h = get_y();
		els.css("top",(h-els.height() - 4)+"px");
	});

	function get_y()
	{
		if(typeof(window.innerWidth) == 'number')
		{
			h = window.innerHeight;
		}
		else if(document.documentElement && (document.documentElement.clientWidth || document.documentElement.clientHeight))
		{
			h = document.documentElement.clientHeight;
		}
		else if(document.body && (document.body.clientWidth || document.body.clientHeight))
		{
			h = document.body.clientHeight;
		}
		return h;
	}	

	return this;
};