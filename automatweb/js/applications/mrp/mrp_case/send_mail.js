(function($){
	if (typeof(AW) == "undefined") {
		window.AW = { UI: {} };
	}
	AW.UI.mrp_case = (function() {
		var id = $.gpnv_as_obj().id;
		return {
			refresh_mail_text_changes: function() {
				// FIXME! Retrieve the data with a single request!
				
				// subject
				$.get('/automatweb/orb.aw', {class: 'mrp_case', action: 'ajax_parse_mail_text', id: id, text: $('#send_mail_subject').val()}, function (html) {
					x = document.getElementById('send_mail_subject_text_element');
					x.innerHTML = html;
				});
			
				// body
				$.get('/automatweb/orb.aw', {class: 'mrp_case', action: 'ajax_parse_mail_text', id: id, text: $('#send_mail_body').val()}, function (html) {
					x = document.getElementById('send_mail_body_text_element');
					x.innerHTML = html;
				});
			}
		};
	})();
})(jQuery);