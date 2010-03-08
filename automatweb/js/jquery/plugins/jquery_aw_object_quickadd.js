/**
 * AW Object Quickadder
 *
 */
var awoq_initDone = false;

(function($) {
	jQuery.fn.aw_object_quickadd = function(items, options)
	{
		var settings = {
			maxresults     : 4,
			blank_gif      : "automatweb/images/aw06/blank.gif"
		};

        	// define defaults and override with options, if available
	        // by extending the default settings, we don't modify the argument
        	if(options) 
		{
			jQuery.extend(settings, options);
		};
		settings.parent = Number(settings.parent);

		jQuery.hotkeys.add(aw_shortcut_db['aw_object_quickadd']["handle_object_quickadd_activation"], handle_object_quickadd_activation);
		jQuery.hotkeys.add(aw_shortcut_db['aw_object_quickadd']["handle_object_quickadd_exit"], handle_object_quickadd_exit);
		
		var d_quickadd_box = this;
		
		// handles the pop up of quickadd layer
		// and document.location upon enter
		function handle_object_quickadd_activation()
		{
	        	var d_input = $("#"+d_quickadd_box.attr("id")+" input.text");
			var b_object_quickadd_isopen = false;
			var i_selected_index = 0;
		
			set_object_quickadd_locations();

			if (awoq_initDone)
			{
				if (b_object_quickadd_isopen===false)
				{
						if ($("#"+d_quickadd_box.attr("id")).css("display") == "block")
						{
					
						}
						else if ($("#"+d_quickadd_box.attr("id")).css("display") == "none")
						{
							$("#"+d_quickadd_box.attr("id")).css("display", "block");
							$("#"+d_quickadd_box.attr("id")+" input.text").focus();
						}
					b_object_quickadd_isopen = true
				}
				return;
			}
			
			$.getJSON("/automatweb/orb.aw?class=aw_object_quickadd&action=get_objects", {}, function (items) 
				{
					awoq_initDone = true;
					d_input.autocomplete(items, 
						{
							minChars: 0,
							width: 310,
							matchContains: true,
							autoFill: true,
							formatItem: function(row, i, max) 
							{
								return row.name+" ( "+row['class']+" )";
							},
							formatMatch: function(row, i, max) 
							{
								return row.name+" ( "+"CL_"+row['class']+" )";
							},
							formatResult: function(row) 
							{			
								return row.name+" ( "+"CL_"+row['class']+" )";
							}
						}
					).result(function(event, data, formatted) 
						{
							url = settings.baseurl+"/automatweb/"+data.url_obj.replace("--p--", settings.parent)
							$(this).parent().remove();
							document.location = url
						}
					);

					if (b_object_quickadd_isopen===false)
					{
							if ($("#"+d_quickadd_box.attr("id")).css("display") == "block")
							{
						
							}
							else if ($("#"+d_quickadd_box.attr("id")).css("display") == "none")
							{
								$("#"+d_quickadd_box.attr("id")).css("display", "block");
								$("#"+d_quickadd_box.attr("id")+" input.text").focus();
							}
						b_object_quickadd_isopen = true
					}
				}
			)
		}
		
		// handles the closing of quickadd layer
		function handle_object_quickadd_exit()
		{
			if ($("#"+d_quickadd_box.attr("id")).css("display") == "block")
			{
				$("#"+d_quickadd_box.attr("id")+" input.text").val("");
				$("#"+d_quickadd_box.attr("id")).css("display", "none");
				b_object_quickadd_isopen = false;
			}
		}
		
		// sets location of the quickadd on the screen
		function set_object_quickadd_locations()
		{
			// quicklaunch box
			d = $("#"+d_quickadd_box.attr("id"));
			d.css("left", $(window).width()/2-d.width()/2);
			d.css("top", $(window).height()/3);
			
			// quicklaunch result
			d2 = $("#aw_object_quickadd_results");
			d2.css("left", ($(window).width()/2-d.width()/2)+18+"px");
			d2.css("top", $(window).height()/3+d.height()-19+"px");
		}
    };
})(jQuery);
