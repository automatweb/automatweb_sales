(function ($) {
	if (typeof(AW) == "undefined") {
		window.AW = { UI: {} };
	}
	if (typeof(AW.UI) == "undefined") {
		window.AW = {};
	}
	AW.UI.object_treeview_v2 = (function() {
		var self = this;
		var customer_filter;
		var customer_refresh_timeout;
		var modal_html = {};
		var modal_preloaded = false;
		var modal_preload_callbacks = [];
		var data = {};
		
		var vmCore = function(_data, properties) {
			var self = this;
			if (!_data) { _data = {}; }
			for (var i in properties) {
				self[properties[i]] = typeof _data[properties[i]] !== "undefined" ? ko.observable(_data[properties[i]]) : ko.observable();
			}
		}
		
		var vmMenu = function(_data) {
			var self = this;
			var properties = ["id", "name", "comment", "alias", "ord", "status", "status_recursive"];
			vmCore.call(self, _data, properties);
		}
		
		var vmLink = function(_data) {
			var self = this;
			var properties = ["id", "name", "comment", "alt", "url", "newwindow"];
			vmCore.call(self, _data, properties);
		}
		
		var vmFile = function(_data) {
			var self = this;
			var properties = ["id", "name", "status", "comment", "alias", "file", "file_url", "ord", "type"];
			vmCore.call(self, _data, properties);
		}
		
		var vmDocument = function(_data) {
			var self = this;
			var properties = ["id", "title", "status", "lead", "content", "show_title", "showlead", "show_modified", "esilehel", "title_clickable"];
			vmCore.call(self, _data, properties);
			self.name = ko.computed(function(){
				return self.title();
			}, self);
		}
		
		var vmCustomerView = function() {
			var self = this;
			self.menu = ko.observable(new vmMenu());
			self.link = ko.observable(new vmLink());
			self.document = ko.observable(new vmDocument());
			self.file = ko.observable(new vmFile());
		};
		
		var customerView = new vmCustomerView();
		
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
				var modal_preload = [];
				modal_preload.push($.ajax({
					url: "/automatweb/orb.aw?class=link_modal&action=parse",
					success: function(html) {
						modal_html[21] = html;
					}
				}));
				modal_preload.push($.ajax({
					url: "/automatweb/orb.aw?class=menu_modal&action=parse",
					success: function(html) {
						modal_html[1] = html;
					}
				}));
				modal_preload.push($.ajax({
					url: "/automatweb/orb.aw?class=document_modal&action=parse",
					success: function(html) {
						modal_html[7] = html;
					}
				}));
				modal_preload.push($.ajax({
					url: "/automatweb/orb.aw?class=file_modal&action=parse",
					success: function(html) {
						modal_html[41] = html;
					}
				}));
				var waitForComplete = setInterval(function () {
					var inProgress = 0;
					for (var i in modal_preload) {
						if (modal_preload[i].readyState !== 4) {
							inProgress++;
						}
					}
					modal_preloaded = inProgress == 0;
					if (modal_preloaded) {
						clearInterval(waitForComplete);
						for (var i in modal_preload_callbacks) {
							modal_preload_callbacks[i].call(self);
							delete modal_preload_callbacks[i];
						}
					}
				}, 100);
			},
			open_modal: function(class_id, parent, oid) {
				data.parent = parent;
				data.class_id = class_id;
				switch (class_id) {
					case 7:
						data.key = "document";
						data.save_url = "/automatweb/orb.aw?class=document_modal&action=save";
						break;
					
					case 21:
						data.key = "link";
						data.save_url = "/automatweb/orb.aw?class=link_modal&action=save";
						break;
					
					case 1:
						data.key = "menu";
						data.save_url = "/automatweb/orb.aw?class=menu_modal&action=save";
						break;
					
					case 41:
						data.key = "file";
						data.save_url = "/automatweb/orb.aw?class=file_modal&action=save";
						break;
					
					default:
						data.key = null;
						data.save_url = null;
				}
//				$.please_wait_window.show();
				var object_data = {};
				var open_modal = function() {
					on_modal_preload_complete(function() {
//						$.please_wait_window.hide();
						if (modal_html[class_id] !== undefined) {
							$(".modal").remove();
							$("body").append(modal_html[class_id]);
							
							customerView.link(new vmLink(object_data));
							customerView.menu(new vmMenu(object_data));
							customerView.document(new vmDocument(object_data));
							customerView.file(new vmFile(object_data));
							ko.applyBindings(customerView);

							$(".modal").css("width", 1100).css("margin-left", -500).modal();
							/* $(".modal").draggable({
								handle: ".modal-header"
							}); */
							
							$(".modal .modal-footer [data-click-action~='save']").each(function() {
								var btn = $(this);
								btn.click(function(){
									var disabled = btn.attr("disabled");
									var close = btn.is("[data-click-action~='close']");
									if (typeof disabled === "undefined" || disabled === false) {
										$(".modal-footer a, .modal-footer button").attr('disabled', 'disabled');
										AW.UI.object_treeview_v2.save_object(function() {
											$(".modal-footer a, .modal-footer button").removeAttr('disabled');
											if (close) {
												$(".modal").modal("hide");
											}
										});
									}
								});
							});
						} else {
							throw "Error: no HTML for class '" + class_id + "'.";
						}
					});
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
			},
			save_object: function(post_save_callback) {
				function processData(_data) {
					for (var i in _data) {
						if (typeof _data[i] == "object") {
							_data[i] = processData(_data[i]);
						} else if (typeof _data[i] == "boolean") {
							_data[i] = _data[i] ? 1 : 0;
						}
					}
					return _data;
				}
//				$.please_wait_window.show({ target: $(".modal") });
				if (!data.key || !data.save_url) {
					alert("Andmete salvestamine ebaõnnestus!");
					throw "Error: no data.key or data.save_url set.";
				}
				var _data = ko.toJS(customerView[data.key]);
				_data.status = 2;
				$.ajax({
					url: data.save_url,
					type: "POST",
					dataType: "json",
					data: {
						class_id: data.class_id,
						parent: data.parent,
						data: processData(_data),
						removed: ko.toJS(customerView.removed)
					},
					success: function(_data) {
						customerView.link(new vmLink(_data));
						customerView.menu(new vmMenu(_data));
						customerView.document(new vmDocument(_data));
						customerView.file(new vmFile(_data));
					},
					error: function() {
						alert("Andmete salvestamine ebaõnnestus!");
					},
					complete: function() {
//						$.please_wait_window.hide();
						post_save_callback();
						execute_refreshing_customers();
					}
				});
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