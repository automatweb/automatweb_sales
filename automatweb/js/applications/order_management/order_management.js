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
		
		var vmCore = function(_data, properties) {
			var self = this;
			if (!_data) { _data = {}; }
			for (var i in properties) {
				self[properties[i]] = typeof _data[properties[i]] !== "undefined" ? ko.observable(_data[properties[i]]) : ko.observable();
			}
		}
		
		var vmPriceComponent = function(_data) {
			var self = this;
			var properties = ["id", "name", "value", "is_ratio", "vat", "applied"];
			vmCore.call(self, _data, properties);
		}
		
		var vmOrderRow = function(_data) {
			var self = this;
			var properties = ["id", "name", "title", "article", "article_name", "description", "price", "quantity", "unit"];
			vmCore.call(self, _data, properties);
			this.price_components = ko.observable({});
			for (var key in _data.price_components) {
				this.price_components()[key] = new vmPriceComponent(_data.price_components[key]);
			}
			self.total = ko.computed(function(){
				var total = this.price() * this.quantity();
				var vat = 0;
				for (var i in this.price_components()) {
					if (!this.price_components()[i].applied()) {
						continue;
					}
					/*
					 * const TYPE_UNIT = 2;
					 * const TYPE_ROW = 3;
					*/
					if (this.price_components()[i].vat()) {
						vat = this.price_components()[i].value() * 1;
					} else if (this.price_components()[i].is_ratio()) {
						total *= (100 + this.price_components()[i].value() * 1) / 100;
					} else {
						total += this.price_components()[i].value() * (this.price_components()[i].value() == 2 ? this.quantity() : 1);
					}
				}
				return total * (100 + vat) / 100;
			}, self);
			self.changeArticle = function(orderRow, event) {
				if (!event) { return; }
				var url = "/automatweb/orb.aw?class=popup_search&action=do_search&no_submit=1&pi=components_new&npi=components_new_name&in_popup=1&start_empty=1";
				aw_popup_scroll(url + "&jcb=window.opener.AW.UI.order_management.add_article(" + orderRow.id() + ")", "Otsing", 800, 500);
			};
		}
		
		var vmOrder = function(_data) {
			var self = this;
			var properties = ["id", "name", "comment", "seller", "customer", "total"];
			vmCore.call(self, _data, properties);
			self.availableUnits = ko.observableArray();
			if (_data && _data.availableUnits) {
				for (var i in _data.availableUnits) {
					self.availableUnits.push(_data.availableUnits[i]);
				}
			}
			self.rows = ko.observableArray();
			if (_data && _data.rows) {
				for (var i in _data.rows) {
					for (var j in self.availableUnits()) {
						if (_data.rows[i].unit && _data.rows[i].unit.id == self.availableUnits()[j].id) {
							_data.rows[i].unit = self.availableUnits()[j];
						}
					}
					self.rows.push(new vmOrderRow(_data.rows[i]));
				}
			}
			self.total = ko.computed(function(){
				var total = 0;
				for (var i in this.rows()) {
					total += this.rows()[i].total();
				}
				return total;
			}, self);
		}
		
		function on_modal_preload_complete (callback) {
			if (modal_preloaded) {
				callback.call(self);
			}
			modal_preload_callbacks.push(callback);
		}
		
		var vmView = function() {
			var self = this;
			self.order = ko.observable(new vmOrder());
			self.removed = ko.observableArray();
		};
		
		var view = new vmView();
	
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
				var time_period = Y.one("#orders_filter_time_period input[type=checkbox]:checked").get("value");
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
				$.ajax({
					url: "/automatweb/orb.aw?class=mrp_case_modal&action=parse",
					success: function(html) {
						modal_html = html;
						modal_preloaded = true;
						for (var i in modal_preload_callbacks) {
							modal_preload_callbacks[i].call(self);
							delete modal_preload_callbacks[i];
						}
					}
				});
			},
			open_order_modal: function(order_id) {
				$.please_wait_window.show();
				var order_data = {};
				var open_modal = function() {
					on_modal_preload_complete(function() {
						var administrative_unit_map = {};
						var field;
						$.please_wait_window.hide();
						if (modal_html !== false) {
							$(".modal").remove();
							$("body").append(modal_html);
							view.order(new vmOrder(order_data));
							ko.applyBindings(view);

							$(".modal").css("width", 1100).css("margin-left", -500).modal({ show: true });
							$(".modal").data("id", order_id);
							/* $(".modal").draggable({
								handle: ".modal-header"
							}); */
						} else {
							throw "Error: no HTML for crm_customer_modal of class '" + customer_class + "'.";
						}
					});
				};
				if (order_id) {
					var customer_data_loaded = false;
					$.ajax({
						url: "/automatweb/orb.aw?class=mrp_case_modal&action=get_data",
						dataType: "json",
						data: { oid: order_id },
						success: function(_data) {
							order_data = _data;
							open_modal();
						},
						error: function() {
							$.please_wait_window.hide();
							alert("Tellimuse andmete päring ebaõnnestus!");
						}
					});
				} else {
					open_modal();
				}
			},
			save_order: function(post_save_callback) {
				$.please_wait_window.show({ target: $(".modal") });
				var order = ko.toJS(view.order);
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
						removed: ko.toJS(view.removed)
					},
					success: function(_data) {
						view.order(new vmOrder(_data));
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
			add_article: function(id) {
				var article_id = $("#components_new").val();
				var article_name = $("#components_new_name").val();
				var rows = view.order().rows();
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