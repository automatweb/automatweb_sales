(function ($) {
	if (typeof(AW) == "undefined") {
		window.AW = { UI: {} };
	}
	if (typeof(AW.UI) == "undefined") {
		window.AW = {};
	}
	AW.UI.object_treeview_v2 = (function() {
		var self = this,
			customer_filter,
			customer_refresh_timeout,
			data = {},
			model;
		
		function on_modal_preload_complete (callback) {
			if (modal_preloaded) {
				callback.call(self);
			}
			modal_preload_callbacks.push(callback);
		}
		
		function execute_refreshing_customers () {
//			reload_property(["my_customers_toolbar", "my_customers_table"], customer_filter);
		}
	
		return {
			refresh_customers: function() {
				var filters = {};
				Y.all("#tree_search_split_outer input[type=checkbox]").each(function(){
					filters[this.get("name")] = this.get("checked") ? this.get("value") : null;
				});
				
				customer_filter = filters;
				
				clearTimeout(customer_refresh_timeout);
				customer_refresh_timeout = setTimeout(execute_refreshing_customers, 500);
			},
			initialize_modals: function() {
				AW.UI.modal.load("link_modal");
				AW.UI.modal.load("menu_modal");
				AW.UI.modal.load("document_modal");
				AW.UI.modal.load("file_modal");
			},
			open_modal: function(class_id, parent, oid) {
				var viewModel;
				switch (class_id) {
					case 7:
						data.key = "document";
						break;
					
					case 21:
						data.key = "link";
						break;
					
					case 1:
						data.key = "menu";
						break;
					
					case 41:
						data.key = "file";
						break;
					
					default:
						data.key = null;
				}
//				$.please_wait_window.show();
				var object_data = { parent: parent, status: 2 };
				var open_modal = function() {
					AW.UI.modal.open(new AW.viewModel[data.key](object_data))
				};
				if (oid) {
					var customer_data_loaded = false;
					var url;
					switch (class_id) {
						case 21:
							url = "/automatweb/orb.aw?class=link_modal&action=get_data";
							break;
						case 1:
							url = "/automatweb/orb.aw?class=menu_modal&action=get_data";
							break;
						case 7:
							url = "/automatweb/orb.aw?class=document_modal&action=get_data";
							break;
						case 41:
							url = "/automatweb/orb.aw?class=file_modal&action=get_data";
							break;
					}
					$.ajax({
						url: url,
						dataType: "json",
						data: { oid: oid },
						success: function(_data) {
							object_data = _data;
							open_modal();
						},
						error: function() {
//							$.please_wait_window.hide();
							alert("Objekti andmete päring ebaõnnestus!");
						}
					});
				} else {
					open_modal();
				}
			}
		};
	})();
	ko.bindingHandlers.upload = {
		init: function (element, valueAccessor) {
			$(element.form).after('<div class="progress progress-striped active"><div class="bar"></div></div><div class="progressError"></div>');
		},
		update: function (element, valueAccessor, allBindingsAccessor, viewModel, bindingContext) {
			var file = ko.utils.unwrapObservable(valueAccessor());
			$(element).change(function () {
				if (element.files.length) {
					var $this = $(this),
						fileName = $this.val();
					// this uses jquery.form.js plugin
					var form = $(element.form);
					form.ajaxSubmit({
						dataType: 'json',
						beforeSubmit: function () {
							$(".progress").show();
							$(".progressError").hide();
							$(".bar").width("0%").html("0%");
							form.hide();
						},
						uploadProgress: function (event, position, total, percentComplete) {
							var percentVal = percentComplete + "%";
							$(".bar").width(percentVal).html(percentVal);
						},
						success: function (response) {
							$(".progress").hide();
							$(".progressError").hide();
							
							file.file(response.file);
							file.name(response.name);
							file.type(response.type);
							form.show();
						},
						error: function (jqXHR, textStatus, errorThrown) {
							$(".progress").hide();
							form.show();
							$("div.progressError").html(jqXHR.responseText);
						}
					});
				}
			});
		}
	}
})(window.jQuery);