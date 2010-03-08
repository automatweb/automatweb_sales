(function($) {
	jQuery.fn.catch_keycombo = function()
	{
		var reset_field = true;
		var active_keys = new Array();
		
		$(this).keydown(function(e){
			switch (e.keyCode) {
			case 16:
			  s_key = "Shift";
			  break;
			case 17:
			  s_key = "Ctrl";
			  break;
			case 18:
			  s_key = "Alt";
			  break;
			default:
			  s_key = String.fromCharCode(e.keyCode);
			}
			
			// for bug #
			if (e.keyCode != 13)
			{
				active_keys[s_key] = String.fromCharCode(s_key);
				$(this).val("")
				for (key in active_keys)
				{
					$(this).val($(this).val()+key+"+".toLowerCase())
				}
			}
		});
		
		$(this).keyup(function(e){
			if (reset_field)
			{
				active_keys = new Array();
				
				input_val = $(this).val();
				if (input_val.substr(input_val.length-1,input_val.length) == "+")
				{
					$(this).val(input_val.substr(0,input_val.length-1))
				}
			}
		});
    };
})(jQuery);