YUI().use("node", function(Y) {
	if (typeof(AW) == "undefined") {
		window.AW = { UI: {} };
	}
	if (typeof(AW.UI) == "undefined") {
		window.AW.UI = {};
	}
	AW.UI.crm_customer_view = (function() {
		var self = this;
		var customer_filter;
		var customer_refresh_timeout;
		var modal_preloaded = false;
		var modal_preload_callbacks = [];
		var data = {};
		
		var vmCustomerView = function() {
			var self = this;
			self.customer = ko.observable(new AW.viewModel.customer_company());
			self.removed = ko.observableArray();
		};
		
		var customerView = new vmCustomerView();
		
		function on_modal_preload_complete (callback) {
			if (modal_preloaded) {
				callback.call(self);
			}
			modal_preload_callbacks.push(callback);
		}
		
		function execute_refreshing_customers () {
			reload_property(["my_customers_toolbar", "my_customers_table"], customer_filter);
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
			initialize_modals: function(company) {
				data.company = company;
				
				var modals_loaded = 0;
				
				function handle_modal_preloaded () {
					if (++modals_loaded == 2) {
						modal_preloaded = true;
						for (var i in modal_preload_callbacks) {
							modal_preload_callbacks[i].call(self);
							delete modal_preload_callbacks[i];
						}
					}
				}
				
				AW.UI.modal.load("crm_customer_modal_company", { company: data.company }, handle_modal_preloaded);
				AW.UI.modal.load("crm_customer_modal_person", { company: data.company }, handle_modal_preloaded);
				AW.UI.modal.load("modal_search", { company: data.company }, handle_modal_preloaded);
			},
			open_customer_modal: function(customer_class, customer_type, category, customer_id) {
				$.please_wait_window.show();
				data.type = customer_type;
				var customer_data = {};
				var open_modal = function() {
					on_modal_preload_complete(function() {
						var administrative_unit_map = {};
						var field;
						$.please_wait_window.hide();

						if (customer_class == "person") {
							data.clid = 145;
							customerView.customer(new AW.viewModel.customer_person(customer_data));
						} else {
							data.clid = 129;
							customerView.customer(new AW.viewModel.customer_company(customer_data));
						}
						customerView.customer().customer_relation().categories(category);
						
						AW.UI.modal.open(customerView.customer()).element.find('input[data-provide=typeahead]').typeahead({
							source: function(query, process) {
								field = this.$element.data("address-field");
								var input = {};
								input[field] = query;
								if (field == "city" || field == "vald") {
									input.county = customerView.customer().address_selected().county_caption();
								}
								$.ajax({
									url: "/automatweb/orb.aw?class=crm_company_cedit_impl",
									data: { action: "ac_" + field, cedit_adr: [input] },
									dataType: "json",
									success: function (response) {
										var options = [];
										$.each(response.options, function(id, name) {
											administrative_unit_map[name] = id;
											options.push(name);
										});
										process(options);
									}
								});
								return ["A"];
							},
							updater: function(item) {
								customerView.customer().address_selected()[field](administrative_unit_map[item]);
								return item;
							}
						});
					});
				};
				if (customer_id) {
					var customer_data_loaded = false;
					$.ajax({
						url: "/automatweb/orb.aw?class=crm_customer_modal&action=get_customer_data",
						dataType: "json",
						data: { company: data.company, customer: customer_id },
						success: function(_data) {
							customer_data = _data;
							open_modal();
						},
						error: function() {
							$.please_wait_window.hide();
							alert("Kliendi andmete päring ebaõnnestus!");
						}
					});
				} else {
					open_modal();
				}
			},
			save_customer: function(post_save_callback) {
				$.ajax({
					url: "/automatweb/orb.aw?class=crm_company&action=create_customer",
					type: "POST",
					dataType: "json",
					data: {
						company: data.company,
						clid: data.clid,
						type: data.type,
						id: customerView.customer().id(),
						name: customerView.customer().name(),
						data: ko.toJS(customerView.customer),
						removed: ko.toJS(customerView.removed)
					},
					success: function(_data) {
						switch (data.clid) {
							case 145:
								customerView.customer(new AW.viewModel.customer_person(_data));
								break;
							
							case 129:
								customerView.customer(new AW.viewModel.customer_company(_data));
								break;
						}
					},
					error: function() {
						alert("Salvestamine ebaõnnestus!");
					},
					complete: function() {
						$.please_wait_window.hide();
						post_save_callback();
						execute_refreshing_customers();
					}
				});
			}
		};
	})();
});