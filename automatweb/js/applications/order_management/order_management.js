YUI().use("node", function(Y) {
	if (typeof(AW) == "undefined") {
		window.AW = { UI: {} };
	}
	if (typeof(AW.UI) == "undefined") {
		window.AW = {};
	}
	String.prototype.toFixed = function (d) {
		var f = parseFloat(this);
		return f.toFixed(d);
	};
	AW.UI.table = (function() {
		return {
			// FIXME: Make it such that could use data-expandable="true" or smth...
			toggleExpandable: function(expander) {
				var a = $(expander),
					i = a.children("i"),
					up = i.hasClass('icon-chevron-up');
				i.removeClass('icon-chevron-down icon-chevron-up');
				
				var target = $([]);
				var row = a.parent().parent().next();
				while(row.data("expandable")) {
					target = target.add(row);
					row = row.next();
				}
				
				if (up) {
					i.addClass('icon-chevron-down');
					target.hide();
				} else {
					i.addClass('icon-chevron-up');
					target.show();
				}
			}
		};
	})();
	AW.UI.order_management = (function() {
		var self = this;
		var modal_html = false;
		var modal_preloaded = false;
		var modal_preload_callbacks = [];
	
		var order_filter;
		var order_refresh_timeout;
		function execute_refreshing_orders () {
			reload_property("orders_table", order_filter);
		}
		
		function on_modal_preload_complete (callback) {
			if (modal_preloaded) {
				callback.call(self);
			}
			modal_preload_callbacks.push(callback);
		}
		
		var view;
	
		return {
			reset_search: function() {
				Y.all("#orders_filter_search input[type=text]").each(function(){
					this.set("value", "");
				});
			},
			update_date_filter: function(o) {
				var today = new Date();
				var currentYear = today.getFullYear();
				var currentMonth = today.getMonth();
				var dayOfWeek = today.getDay() > 0 ? today.getDay() - 1 : 6;
				var dayOfMonth = today.getDate();
				var time_periods = {
					1: { from: "+0d", to: "+0d" },
					2: { from: "-1d", to: "-1d" },
					3: { from: "-"+dayOfWeek+"d", to: "+"+(6 - dayOfWeek)+"d" },
					4: { from: "-"+(dayOfWeek + 7)+"d", to: "+"+(6 - dayOfWeek - 7)+"d" },
					5: { from: new Date(currentYear, currentMonth, 1), to: new Date(currentYear, currentMonth + 1, 0) },
					6: { from: new Date(currentYear, currentMonth - 1, 1), to: new Date(currentYear, currentMonth, 0) },
					7: { from: new Date(currentYear, currentMonth - currentMonth % 3, 1), to: new Date(currentYear, currentMonth - currentMonth % 3 + 3, 0) },
					8: { from: new Date(currentYear, currentMonth - currentMonth % 3 - 3, 1), to: new Date(currentYear, currentMonth - currentMonth % 3, 0) },
					9: { from: new Date(currentYear, 0, 1), to: new Date(currentYear, 11, 31) },
					10: { from: new Date(currentYear - 1, 0, 1), to: new Date(currentYear - 1, 11, 31) }
				};
				var time_period = $("[name='orders_filter_time_period'] input[type=checkbox]:checked").val();
				// TODO: To be replaced by YUI code once we replace datepicker with YUI datepicker!
				if (time_periods[time_period]) {
					$("input[name='orders_filter_search_date_from[date]']").datepick("setDate", time_periods[time_period].from);
					$("input[name='orders_filter_search_date_to[date]']").datepick("setDate",  time_periods[time_period].to);
				} else {
					// TODO: Handle missing time period!
					$("input[name='orders_filter_search_date_from[date]']").datepick("setDate", null);
					$("input[name='orders_filter_search_date_to[date]']").datepick("setDate",  null);
				}
			},
			refresh_orders: function() {
				var data = {};
				Y.all("#orders_filter input[type=checkbox]").each(function(){
					data[this.get("name")] = this.get("checked") ? this.get("value") : null;
				});
				Y.all("#orders_filter_search input[type=text]").each(function(){
					data[this.get("name")] = this.get("value") ? this.get("value") : "";
				});
				
				order_filter = data;
				
				clearTimeout(order_refresh_timeout);
				order_refresh_timeout = setTimeout(execute_refreshing_orders, 500);
			},
			initialize_modal: function() {
				AW.UI.modal.load("mrp_case_modal", {}, function () {
					modal_preloaded = true;
					for (var i in modal_preload_callbacks) {
						modal_preload_callbacks[i].call(self);
						delete modal_preload_callbacks[i];
					}
				});
				AW.UI.modal.load("modal_search_shop");
			},
			open_order_modal: function(order_id) {
				$.please_wait_window.show();
				var open_modal = function (order_data) {
					on_modal_preload_complete(function() {
						$.please_wait_window.hide();
						view = new AW.viewModel.order(order_data);
						AW.UI.modal.open(view).element.data("id", order_id);
					});
				};
				if (order_id) {
					var customer_data_loaded = false;
					$.ajax({
						url: "/automatweb/orb.aw?class=mrp_case_modal&action=get_data",
						dataType: "json",
						data: { oid: order_id },
						success: function(data) {
							open_modal(data);
						},
						error: function() {
							$.please_wait_window.hide();
							alert("Tellimuse andmete päring ebaõnnestus!");
						}
					});
				} else {
					open_modal({});
				}
			},
			save_order: function(post_save_callback) {
				var order = ko.toJS(view);
				var expanded = [];
				$("#components_table tbody tr:not([data-expandable=true])").each(function (index, row) {
					row = $(row);
					for (var i in order.rows) {
						if (order.rows[i].id == row.data("id")) {		
							order.rows[i].ord = row.index();
						}
					}
					if (row.next().is(":visible")) {
						expanded.push(row.data("id"));
					}
				});
				$.ajax({
					url: "/automatweb/orb.aw?class=mrp_case_modal&action=save",
					type: "POST",
					dataType: "json",
					data: {
						class_id: 828,
						data: order,
						removed: ko.toJS(view.deleted)
					},
					success: function(_data) {
						view.load(_data);
						for (var i in expanded) {
							$("tr[data-id=" + expanded[i] + "] .expander").click();
						}
					},
					error: function() {
						alert("Salvestamine ebaõnnestus!");
					},
					complete: function() {
						$.please_wait_window.hide();
						post_save_callback();
					}
				});
			},
			add_article: function(id, item) {
				var article_id = item ? item.id : $("#components_new").val();
				var article_name = item ? item.name : $("#components_new_name").val();
				var rows = view.rows();
				for (var i in rows) {
					if (rows[i].id() == id) {
						rows[i].article({ id: article_id, name: article_name });
						break;
					}
				}
			}
		};
	})();
});