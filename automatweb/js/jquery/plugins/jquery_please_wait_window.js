/**
 * Show ajax loading layer
 */
(function($){
	$.please_wait_window = {
		hide: function() {
			$("#ajaxLoader").css("display", "none");
		},
		show : function(arr) {
			if ( typeof(arr) !== "undefined" ) {
				if (typeof(arr.target) === "object" ) {
					obj = arr.target;
				} else if (typeof(arr.target) === "undefined" ) {
					obj = "window";
				} else if (typeof(arr.target) === "string" ) {
					var obj = $(arr.target);
				} else {
					alert("$.please_wait_window does not undrestand what you try to give as parameter");
					return false;
				}
			} else {
				obj = "window";
			}
			
			if (obj !== "window") {
				$("#ajaxLoader")
					.css("top", obj.offset().top+obj.height()/2-34+"px")
					.css("left", obj.offset().left+obj.width()/2-130+"px")
					.css("display", "block");
			} else {
				$("#ajaxLoader")
					.css("top", $(window).height()/2-34+"px")
					.css("left", $(window).width()/2-130+"px")
					.css("display", "block");
			}
		}
	};
})(jQuery);