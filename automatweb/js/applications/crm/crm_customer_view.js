YUI().use("node", function(Y) {
	if (typeof(AW) == "undefined") {
		window.AW = { UI: {} };
	}
	if (typeof(AW.UI) == "undefined") {
		window.AW = {};
	}
	AW.UI.crm_customer_view = (function() {
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
		
		var vmEmail = function(_data) {
			var self = this;
			var properties = ["id", "mail", "contact_type"];
			vmCore.call(self, _data, properties);
			self.contact_type_caption = ko.computed(function() {
				return $("#contact-email-contact_type option[value=" + self.contact_type() + "]").html();
			}, self);
			self.name = ko.computed(function() {
				return self.mail();
			}, self);
		}
		
		var vmPhone = function(_data) {
			var self = this;
			var properties = ["id", "name", "type"];
			vmCore.call(self, _data, properties);
			self.type_caption = ko.computed(function() {
				return $("#contact-phone-type option[value='" + self.type() + "']").html();
			}, self);
		}
		
		var vmAddress = function(_data) {
			var self = this;
			var properties = ["id", "street", "house", "apartment", "postal_code", "coord_x", "coord_y", "details"];
			vmCore.call(self, _data, properties);
			var administrative_units = ["country", "county", "city", "vald", ];
			for (var i in administrative_units) {
				if (_data && typeof _data[administrative_units[i]] === "object" && _data[administrative_units[i]] !== null) {
					self[administrative_units[i]] = ko.observable(_data[administrative_units[i]].id);
					self[administrative_units[i] + "_caption"] = ko.observable(_data[administrative_units[i]].name);
				} else {
					self[administrative_units[i]] = ko.observable();
					self[administrative_units[i] + "_caption"] = ko.observable();
				}
			}
			self.country_caption = ko.computed(function() {
				return "Eesti";//$("#contact-address-country option[value='" + self.country() + "']").html();
			}, self);
		}
		
		var vmEmployee = function(_data) {
			var self = this;
			var properties = ["id", "name", "gender", "email", "phone"];
			vmCore.call(self, _data, properties);
			self.gender_caption = ko.computed(function() {
				return $("#contact-employee-gender option[value='" + self.gender() + "']").html();
			}, self);
			self.skills = ko.observableArray(_data && typeof _data.skills !== "undefined" ? _data.skills : []);
			self.skills_caption = ko.computed(function() {
				var skills = [];
				$("#contact-employee-skills option").each(function() {
					var option = $(this);
					if ($.inArray(option.attr("value"), self.skills()) !== -1) {
						skills.push(option.data("caption"));
					}
				});
				return skills.join(", ");
			}, self);
		}
		
		var vmCustomerRelation = function(_data) {
			var self = this;
			var properties = ["id", "categories"];
			vmCore.call(self, _data, properties);
		}
		
		var vmCustomer = function(_data, properties) {
			var self = this;
			if (!_data) { _data = {}; }
			vmCore.call(self, _data, properties);

			self.emails = ko.observableArray();
			if (_data["emails"]) {
				for (var i in _data["emails"]) {
					self.emails.push(new vmEmail(_data["emails"][i]));
				}
			}
			self.email_selected = ko.observable(new vmEmail());
			self.saveEmail = function(customerView, event) {
				if (!event) { return; }
				if (!self.email_selected().id()) {
					self.email_selected().id("new");
					self.emails.push(self.email_selected());
				}
				self.resetEmail();
			}
			self.editEmail = function(email, event) {
				if (!event) { return; }
				self.email_selected(email);
			}
			self.removeEmail = function(email, event) {
				if (!event) { return; }
				customerView.removed.push(email.id());
				self.emails.remove(email)
			}
			self.resetEmail = function(customerView, event) {
				self.email_selected(new vmEmail());
			}

			self.phones = ko.observableArray();
			if (_data["phones"]) {
				for (var i in _data["phones"]) {
					self.phones.push(new vmPhone(_data["phones"][i]));
				}
			}
			self.phone_selected = ko.observable(new vmPhone());
			self.savePhone = function(customerView, event) {
				if (!event) { return; }
				if (!self.phone_selected().id()) {
					self.phone_selected().id("new");
					self.phones.push(self.phone_selected());
				}
				self.resetPhone();
			}
			self.editPhone = function(phone, event) {
				if (!event) { return; }
				self.phone_selected(phone);
			}
			self.removePhone = function(phone, event) {
				if (!event) { return; }
				customerView.removed.push(phone.id());
				self.phones.remove(phone)
			}
			self.resetPhone = function(customerView, event) {
				self.phone_selected(new vmPhone());
			}

			self.employees = ko.observableArray();
			if (_data["employees"]) {
				for (var i in _data["employees"]) {
					self.employees.push(new vmEmployee(_data["employees"][i]));
				}
			}
			self.employee_selected = ko.observable(new vmEmployee());
			self.saveEmployee = function(customerView, event) {
				if (!event) { return; }
				if (!self.employee_selected().id()) {
					self.employee_selected().id("new");
					self.employees.push(self.employee_selected());
				}
				self.resetEmployee();
			}
			self.editEmployee = function(employee, event) {
				if (!event) { return; }
				self.employee_selected(employee);
			}
			self.removeEmployee = function(employee, event) {
				if (!event) { return; }
				customerView.removed.push(employee.id());
				self.employees.remove(employee)
			}
			self.resetEmployee = function(customerView, event) {
				self.employee_selected(new vmEmployee());
			}

			self.addresses = ko.observableArray();
			if (_data["addresses"]) {
				for (var i in _data["addresses"]) {
					self.addresses.push(new vmAddress(_data["addresses"][i]));
				}
			}
			self.address_selected = ko.observable(new vmAddress());
			self.saveAddress = function(customerView, event) {
				if (!event) { return; }
				if (!self.address_selected().id()) {
					self.address_selected().id("new");
					self.addresses.push(self.address_selected());
				}
				self.resetAddress();
			}
			self.editAddress = function(address, event) {
				if (!event) { return; }
				self.address_selected(address);
				$("#contact-address-edit").slideDown(200);
			}
			self.removeAddress = function(address, event) {
				if (!event) { return; }
				customerView.removed.push(address.id());
				self.addresses.remove(address)
			}
			self.resetAddress = function(customerView, event) {
				self.address_selected(new vmAddress());
				$("#contact-address-edit").slideUp(200);
			}

			self.customer_relation = ko.observable(new vmCustomerRelation(typeof _data.customer_relation !== "undefined" ? _data.customer_relation : {}));
		}
		
		var vmCustomerPerson = function(_data) {
			var self = this;
			var properties = ["id", "name"];
			vmCustomer.call(this, _data, properties);
		};
		
		var vmCustomerCompany = function(_data) {
			var self = this;
			var properties = ["id", "name", "reg_nr", "ettevotlusvorm", "tax_nr"];
			vmCustomer.call(this, _data, properties);
		};
		
		var vmCustomerView = function() {
			var self = this;
			self.customer = ko.observable(new vmCustomerCompany());
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
				
				modal_preload = $.ajax({
					url: "/automatweb/orb.aw?class=crm_customer_modal&action=parse",
					success: function(html) {
						modal_html.company = html;
						modal_preloaded = true;
						for (var i in modal_preload_callbacks) {
							modal_preload_callbacks[i].call(self);
							delete modal_preload_callbacks[i];
						}
					}
				});
			},
			open_customer_modal: function(customer_class, customer_type, category, customer_id) {
				$.please_wait_window.show();
				var customer_data = {};
				var open_modal = function() {
					on_modal_preload_complete(function() {
						var administrative_unit_map = {};
						var field;
						$.please_wait_window.hide();
						if (modal_html[customer_class] !== undefined) {
							$(".modal").remove();
							$("body").append(modal_html[customer_class]);

							if (customer_class == "person") {
								data.clid = 145;
								customerView.customer(new vmCustomerPerson(customer_data));
							} else {
								data.clid = 129;
								customerView.customer(new vmCustomerCompany(customer_data));
							}
							customerView.customer().customer_relation().categories(category);
							ko.applyBindings(customerView);

							$(".modal").css("width", 1100).css("margin-left", -500).modal();
							/* $(".modal").draggable({
								handle: ".modal-header"
							}); */
							$('.modal input[data-provide=typeahead]').typeahead({
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
						} else {
							throw "Error: no HTML for crm_customer_modal of class '" + customer_class + "'.";
						}
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
				$.please_wait_window.show({ target: $(".modal") });
				$.ajax({
					url: "/automatweb/orb.aw?class=crm_company&action=create_customer",
					type: "POST",
					dataType: "json",
					data: {
						company: data.company,
						clid: data.clid,
						id: customerView.customer().id(),
						name: customerView.customer().name(),
						data: ko.toJS(customerView.customer),
						removed: ko.toJS(customerView.removed)
					},
					success: function(_data) {
						customerView.customer(new vmCustomerCompany(_data));
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