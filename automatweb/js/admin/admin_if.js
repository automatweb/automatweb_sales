YUI().use("node", function(Y) {
	if (typeof(AW) == "undefined") {
		window.AW = { UI: {} };
	}
	if (typeof(AW.UI) == "undefined") {
		window.AW.UI = {};
	}
	AW.UI.admin_if = (function() {
		var self = this;
			
		var getSaveMethod = function (object) {
			return (function (post_save_callback) {
				function trySave () {
					if (object.canSave()) {
						$.ajax({
							url: "/automatweb/orb.aw",
							type: "POST",
							dataType: "json",
							data: {
								"class": object.class,
								"action": "save",
								"class_id": object.class_id,
								"parent": object.parent,
								"deleted": object.deleted ? ko.toJS(object.deleted) : null,
								"data": object.toJS ? object.toJS() : ko.toJS(object)
							},
							success: function(data) {
								object.load(data);
								post_save_callback(data);
							},
							error: function() {
								alert("Andmete salvestamine eba›nnestus!");
								post_save_callback();
							},
							complete: function() {
		//						$.please_wait_window.hide();
								reload_property(["o_tbl"]);
							}
						});
					} else {
						setTimeout(trySave, 100);
					}
				}
				trySave();
			});
		};
		
		return {
			open_modal: function (arr) {
				if (arr.id) {
					$.please_wait_window.show();
					$.ajax({
						url: "/automatweb/orb.aw?class=" + arr.modal + "&action=get_data",
						dataType: "json",
						data: { oid: arr.id },
						success: function(data) {
							AW.UI.modal.open(new AW.viewModel[arr.model](data));
							$.please_wait_window.hide();
						},
					});
				} else {
					AW.UI.modal.open(new AW.viewModel[arr.model]({ parent: arr.parent }), { save: getSaveMethod(model) });
				}
			},
			initialize: function () {
				function getQueryVariable (url, variable) {
					var query = url.split("?")[1];
					var vars = query ? query.split("&") : [];
					for (var i = 0; i < vars.length; i++) {
						var pair = vars[i].split("=");
						if (pair[0] == variable) { return pair[1]; }
					}
					return false;
				}
				
				AW.UI.modal.load("mini_gallery_modal");
				AW.UI.modal.load("document_modal");
				AW.UI.modal_search.load("modal_search_employee");
				$("body").on("click", "a", function (event) {
					var a = $(this),
						href = a.attr("href");
					if (["change", "new"].indexOf(getQueryVariable(href, "action")) != -1) {
						var match = true;
						switch (getQueryVariable(href, "class")) {
							case "doc":
								AW.UI.admin_if.open_modal({
									modal: "document_modal",
									model: "doc",
									id: getQueryVariable(href, "id")
								});
								break;
								
							case "mini_gallery":
								AW.UI.admin_if.open_modal({
									modal: "mini_gallery_modal",
									model: "mini_gallery",
									id: getQueryVariable(href, "id")
								});
								break;
								
							default:
								match = false;
						}
						if (match) {
							event.preventDefault();
						}
					}
				});
			}
		};
	})();
	$(document).ready(AW.UI.admin_if.initialize);
});