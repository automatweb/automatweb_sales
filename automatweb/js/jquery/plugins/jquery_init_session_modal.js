/**
 * @version 1
 * Displays modal window before 5 minutes before session end
 * Directs user to frontpage after session end
 */
(function($){
    $.extend({
        init_session_modal: function(arr){
		return true;
            return new $.init_session_modal(arr);
        },
        init_session_modal: function(arr){
			 return true;
			// display 5 mins before session end
			session_minus_5_mins = (arr["session_length"]-5*60)*1000;
			$.timer(session_minus_5_mins, function (timer) {
				jQuery.init_session_modal_window(arr["session_end_msg"],
											arr["btn_session_end_continue"],
											arr["btn_session_end_cancel"]);
				timer.stop();
			});
			$.timer(arr["session_length"]*1000, function (timer) {
				$.ajax({
					type: "POST",
					url: "/orb.aw?class=users&action=logout",
					success: function(msg){
						document.location = "/"
					}
				});
				timer.stop();
			});
        },
		session_modal_window_handler: function(v,m){
			switch(v)
			{
				case "continue_session":
					$.ajax({
						type: "POST",
						url: document.location+""
					});
					break;
				case "end_session":
					$.ajax({
						type: "POST",
						url: "/orb.aw?class=users&action=logout",
						success: function(msg){
							document.location = "/"
						}
					});
					break;
			}
		},
		init_session_modal_window : function(msg, btn1, btn2)
		{
			code = ""+
			"$.prompt(\""+msg+"\", {\n"+
			"	buttons			: { "+btn1+" : 'continue_session', "+btn2+" : 'end_session' },\n"+
			"	promptspeed		: 0,\n"+
			"	overlayspeed	: 500,\n"+
			"	opacity			: 0.5,\n"+
			"	callback		: jQuery.session_modal_window_handler\n"+
			"})";
			eval (code);
		}
    });
})(jQuery);
