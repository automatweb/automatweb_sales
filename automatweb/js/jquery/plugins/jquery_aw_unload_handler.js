(function($){
    $.extend({
        aw_unload_handler: function(arr){
			if (
					$.gup("class") == "admin_if" ||
					$.gup("class") == "relationmgr" )
			{
				return false;
			}
		
			var aw_form_changed = false;
			$("form input, form textarea, form select").change( function() {
				aw_form_changed = true;
			});
			
			$("form input[type='submit']").click( function() {
				aw_form_changed = false;
			});
			
			$(window).unload( function () {
				AWPageUnloadHandler();
			});
			
			function AWPageUnloadHandler()
			{
				if ( is_fck_loaded() )
				{
					return true;
				}
			
				if ( $.gup("class") == "doc" && $.gup("group")=="general" )
				{
					return true;
				}
			
				if( aw_form_changed )
				{
					var prompt = arr["msg_unload_leave_notice"];
					if(confirm(prompt))
					{
						 $.ajax({
							type: "POST",
							url: "orb.aw",
							data: $("form").serialize()+"&"+"posted_by_js=1",
							async: false,
							success: function(msg){
								//alert( "Data Saved: " + msg );
							},
							error: function(msg){
								alert( arr["msg_unload_save_error"] );
							}
			
						});
					}
				}
				
				function is_fck_loaded()
				{
					try {
						var testobject = new FCKeditor("test");
						return true;
					} catch(e) { return false; }
				}
			}
        }
    });
})(jQuery);
