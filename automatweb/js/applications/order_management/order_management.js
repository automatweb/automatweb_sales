YUI().use("node", function(Y) {
	if (typeof(AW) == "undefined") {
		window.AW = { UI: {} };
	}
	if (typeof(AW.UI) == "undefined") {
		window.AW = {};
	}
	AW.UI.order_management = (function() {
	
		var order_filter;
		var order_refresh_timeout;
		function execute_refreshing_orders () {
			reload_property("orders_table", order_filter);
		}
	
		return {
			update_date_filter: function() {
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
					7: { from: new Date(currentYear, 0, 1), to: new Date(currentYear, 11, 31) },
					8: { from: new Date(currentYear - 1, 0, 1), to: new Date(currentYear - 1, 11, 31) }
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
			}
		};
	})();
});