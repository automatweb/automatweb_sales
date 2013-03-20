YUI().use("node", function(Y) {
	if (typeof(AW) == "undefined") {
		window.AW = { UI: {} };
	}
	if (typeof(AW.UI) == "undefined") {
		window.AW = {};
	}
	AW.UI.crm_customer_view = (function() {
	
		var customer_filter;
		var customer_refresh_timeout;
		function execute_refreshing_customers () {
			reload_property(["my_customers_toolbar", "my_customers_table"], customer_filter);
		}
	
		return {
			refresh_customers: function() {
				var data = {};
				Y.all("#tree_search_split_outer input[type=checkbox]").each(function(){
					data[this.get("name")] = this.get("checked") ? this.get("value") : null;
				});
				
				customer_filter = data;
				
				clearTimeout(customer_refresh_timeout);
				customer_refresh_timeout = setTimeout(execute_refreshing_customers, 500);
			}
		};
	})();
});