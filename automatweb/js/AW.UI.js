if (typeof(AW) == "undefined") {
	window.AW = { UI: {} };
}
if (typeof(AW.UI) == "undefined") {
	window.AW.UI = {};
}
$.extend(window.AW.UI, (function(){
	return {
		modal: (function(){
			var templates = {};
			var counter = 0;
			var modal = function (_element) {
				self = this;
				self.element = _element;
				self.element.on('hidden', function () {
					self.element.remove();
				});
				self.close = function() {
					self.element.modal("hide");
				};
			};
			return {
				open: function (modalClass, data) {
					if (templates[modalClass] === undefined) {
						throw "Error: no HTML for crm_customer_modal of class '" + customer_class + "'.";
					}
					
					var html = templates[modalClass].replace("{VAR:prefix}", counter++);
					var modalElement = $(html);
					$("body").append(modalElement);
					modalElement.css("width", 1100).css("margin-left", -500).modal({ show: true, backdrop: false });
					
					return new modal(modalElement);
				},
				load: function (modalClass, data, callback) {
					$.ajax({
						url: "/automatweb/orb.aw?class=" + modalClass + "&action=parse",
						data: data ? data : {},
						success: function(html) {
							templates[modalClass] = html;
							if (callback) {
								callback.call();
							}
						}
					});
				}
			};
		})()
	};
})());