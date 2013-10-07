if (typeof(AW) == "undefined") {
	window.AW = { UI: {} };
}
if (typeof(AW.UI) == "undefined") {
	window.AW.UI = {};
}

$.extend(window.AW, (function(){
	// FIXME: Does not work!
	function toJS (object) {
		if (object.toJS) { return object.toJS() }
		var JS = {};
		for (var i in object) {
			if ($.isArray(object[i])) {
				JS[i] = toJS(object[i]);
			} else if (object[i].toJS) {
				JS[i] = object[i].toJS();
			} else if (ko.toJS) {
				JS[i] = ko.toJS(object[i]);
			} else {
				JS[i] = object[i];
			}
		}
		return JS;
	}
	
	return {
		CLID: {
			menu: 1,
			file: 41,
			image: 6,
			extlink: 21,
			document: 7,
			mini_gallery: 318,
			crm_meeting: 224
		},
		toJS: toJS,
		util: (function () {
			return {
				isNumeric: function (n) {
					return !isNaN(parseFloat(n)) && isFinite(n);
				},
				dictSize: function(o) {
					var size = 0;
					for (var key in o) {
						if (o.hasOwnProperty(key)) size++;
					}
					return size;
				},
				time: function () {
					return new Date().getTime() / 1000;
				},
				convertTimestampToDate: function(unix_timestamp) {
					var date = new Date(unix_timestamp*1000);
					var day = date.getDate();
					var month = date.getMonth() + 1;
					return (day < 10 ? "0" : "") + day + "." + (month < 10 ? "0" : "") + month + "." + date.getFullYear();
				},
				formatTimestamp: function (timestamp) {
					function _0 (i) {
						return i < 10 ? "0" + i : i;
					}
					var date = new Date(timestamp * 1000),
						year = date.getFullYear(),
						month = date.getMonth() + 1,
						day = date.getDate(),
						hour = date.getHours(),
						minute = date.getMinutes(),
						second = date.getSeconds();
					// 27.12.2013 23:59
					return _0(day) + "." + _0(month) + "." + year + " " + _0(hour) + ":" + _0(minute);
				},
				formatFileSize: function (bytes) {
					if (!AW.util.isNumeric(bytes)) {
						return bytes;
					} else {
						bytes = parseFloat(bytes);
					}
					var kB = 1024,
						MB = 1024 * kB,
						GB = 1024 * MB;
					if (bytes < 1000) {
						return bytes + " B";
					} else if (bytes < 1000 * kB) {
						return (bytes / kB).toFixed(2) + " kB";
					} else if (bytes < 1000 * MB) {
						return (bytes / MB).toFixed(2) + " MB";
					} else {
						return (bytes / GB).toFixed(2) + " GB";
					}
				}
			}
		})(),
		viewModel: (function () {
			var newIdCount = 0;
			var newId = function () {
				return "new-" + newIdCount++;
			};
			var vmCore = function(data, properties) {
				var self = this;
				if (!data) { data = {}; }
				properties = ["id", "class_id", "parent", "name", "comment", "ord"].concat(properties);
				for (var i in properties) {
					self[properties[i]] = typeof data[properties[i]] !== "undefined" ? ko.observable(data[properties[i]]) : ko.observable();
				}
				self.id = ko.observable(data && data.id ? data.id : newId());
				self.load = function (newData) {
					for (var i in newData) {
						if (self[i]) self[i](newData[i]);
					}
				};
				self.reload = function (keys, callbacks) {
					if (!$.isArray(keys)) keys = [keys];
					var data = self.toJS();
					for (var i in keys) {
						if (data[keys[i]]) delete data[keys[i]];
					}
					$.ajax({
						url: "/automatweb/orb.aw",
						method: "POST",
						data: { "class": self.class ? self.class : "aw_modal", "action": "reload_data", "properties": keys, data: data },
						dataType: "json",
						success: function (data) {
							for (var i in keys) {
								if (self[keys[i]]) self[keys[i]](data[keys[i]]);
							}
							if (callbacks && callbacks.success) callbacks.success.call();
						},
						error: function () {
							if (callbacks && callbacks.error) callbacks.error.call();
						},
						complete: function () {
							if (callbacks && callbacks.complete) callbacks.complete.call();
						}
					});
				};
				self.toJS = function () {
					var data = {};
					for (var i in properties) {
						var value = self[properties[i]].toJS ? self[properties[i]].toJS() : ko.toJS(self[properties[i]]);
						if (value == null || $.isArray(value) && value.length == 0) value = "";
						data[properties[i]] = value;
					}
					return data;
				};
				self.canSave = function () { return true; };
				self.save = function (callbacks) {
					function processData(data) {
						for (var i in data) {
							if (typeof data[i] == "object") {
								data[i] = processData(data[i]);
							} else if (typeof data[i] == "boolean") {
								data[i] = data[i] ? 1 : 0;
							}
						}
						return data;
					}
					$.ajax({
						url: "/orb.aw",
						type: "POST",
						dataType: "json",
						data: {
							class: self.class,
							action: "save",
							class_id: self.class_id,
							parent: self.parent(),
							data: processData(self.toJS()),
							removed: self.removed ? ko.toJS(self.removed()) : null
						},
						success: function(data) {
							self.load(data);
							if (callbacks && callbacks.success) callbacks.success(data);
						},
						error: function() {
							alert("Andmete salvestamine ebaõnnestus!");
							if (callbacks && callbacks.error) callbacks.error();
						},
						complete: function() {
							if (callbacks && callbacks.complete) callbacks.complete();
						}
					});
				};
			}
			
			return {
				document: function (data) {
					// FIXME: Use a top level viewModel with trimmed list of properties, or move to top level viewModel
					var vmParticipant = function (data) {
						var self = this;
						var properties = ["chosen", "phone", "email", "organisation", "participantion_type", "permissions"];
						vmCore.call(self, data, properties);
					};
					// FIXME: Use a top level viewModel with trimmed list of properties, or move to top level viewModel
					var vmAttachment = function (data) {
						var self = this;
						var properties = ["type"];
						vmCore.call(self, data, properties);
					};
					
					function processParticipants (input) {
						var participants = [];
						for (var i in input) {
							participants.push(new vmParticipant(input[i]));
						}
						return participants;
					};
					
					function processAttachments (input) {
						var attachments = [];
						for (var i in input) {
							attachments.push(new vmAttachment(input[i]));
						}
						return attachments;
					};
					
					var self = this;
					var properties = ["title", "status", "document_status", "document_status_options", "lead", "content", "moreinfo",
									  "show_title", "showlead", "show_modified", "esilehel", "title_clickable", "participation_type_options",
									  "permission_options", "reg_date", "make_date", "parents", "editors", "authors", "participants", "attachments"];
					vmCore.call(self, data, properties);
					self.class = "document_modal";
					self.class_id = AW.CLID.document;
					self.name = ko.computed(function(){
						return self.title();
					}, self);
					self.editors = ko.observableArray(processParticipants(data && data.editors ? data.editors : []));
					self.authors = ko.observableArray(processParticipants(data && data.authors ? data.authors : []));
					self.participants = ko.observableArray(processParticipants(data && data.participants ? data.participants : []));
					self.selectEditors = function (document, event) {
						if (!event) return;
						AW.UI.modal_search.open("modal_search_employee", { defaultSource: null, multiple: true, onSelect: function (items) {
							for (var i in items) {
								var found = false;
								for (var j in self.editors()) {
									if (self.editors()[j].id() == items[i].id) {
										found = true;
										break;
									}
								}
								if (!found) {
									self.editors.push(new vmParticipant(items[i]));
								}
							}
						} });
					};
					self.selectAuthors = function (document, event) {
						if (!event) return;
						AW.UI.modal_search.open("modal_search_employee", { defaultSource: null, multiple: true, onSelect: function (items) {
							for (var i in items) {
								var found = false;
								for (var j in self.authors()) {
									if (self.authors()[j].id() == items[i].id) {
										found = true;
										break;
									}
								}
								if (!found) {
									self.authors.push(new vmParticipant(items[i]));
								}
							}
						} });
					};
					self.selectParticipatingColleague = function (document, event) {
						if (!event) return;
						AW.UI.modal_search.open("modal_search_employee", { defaultSource: null, multiple: true, onSelect: function (items) {
							for (var i in items) {
								var found = false;
								for (var j in self.participants()) {
									if (self.participants()[j].id() == items[i].id) {
										found = true;
										break;
									}
								}
								if (!found) {
									self.participants.push(new vmParticipant(items[i]));
								}
							}
						} });
					};
					self.selectParticipatingSection = function (document, event) {};
					self.selectParticipatingClientGroup = function (document, event) {};
					self.removeEditor = function (editor) {
						self.editors.remove(editor);
					};
					self.removeAuthor = function (author) {
						self.authors.remove(author);
					};
					self.removeParticipant = function (participant) {
						self.participants.remove(participant);
					};
					self.notifyParticipants = function (document, event) {
						if (typeof document == "undefined") {
							return;
						}
						var participants = [];
						$.each(document.participants(), function (i, participant) {
							if (participant.chosen()) {
								participants.push(participant);
							}
						});
						if (participants.length > 0) {
							$.ajax({
								url: "/automatweb/orb.aw?class=doc&action=notify_participants",
								data: { id: document.id(), participants: participants },
							});
						} else {
							alert("Osalejad valimata!");
						}
					};
					
					self.attachments = ko.observableArray(processAttachments(data && data.attachments ? data.attachments : []));
					self.createAttachmentFile = function (document, event) {
						if (typeof document == "undefined") { return; }
						AW.UI.modal.open(new AW.viewModel.file());
					};
					self.createAttachmentImage = function (document, event) {
						AW.UI.modal.open(new AW.viewModel.image());
					};
					self.createAttachmentLink = function (document, event) {
						AW.UI.modal.open(new AW.viewModel.link());
					};
					self.createAttachmentDocument = function (document, event) {
						AW.UI.modal.open(new AW.viewModel.document());
					};
					function addAttachments (items) {
						for (var i in items) {
							var found = false;
							for (var j in self.attachments()) {
								if (self.attachments()[j].id() == items[i].id) {
									found = true;
									break;
								}
							}
							if (!found) {
								self.attachments.push(new vmAttachment(items[i]));
							}
						}
					};
					self.searchAttachmentFile = function (document, event) {
						if (typeof document == "undefined") { return; }
						AW.UI.modal_search.open("modal_search", { clid: AW.CLID.file, multiple: true, onSelect: addAttachments });
					};
					self.searchAttachmentImage = function (document, event) {
						if (typeof document == "undefined") { return; }
						AW.UI.modal_search.open("modal_search", { clid: AW.CLID.image, multiple: true, onSelect: addAttachments });
					};
					self.searchAttachmentLink = function (document, event) {
						if (typeof document == "undefined") { return; }
						AW.UI.modal_search.open("modal_search", { clid: AW.CLID.extlink, multiple: true, onSelect: addAttachments });
					};
					self.searchAttachmentDocument = function (document, event) {
						if (typeof document == "undefined") { return; }
						AW.UI.modal_search.open("modal_search", { clid: AW.CLID.document, multiple: true, onSelect: addAttachments });
					};
					AW.UI.modal.load("file_modal");
					AW.UI.modal.load("link_modal");
					AW.UI.modal_search.load("modal_search");
				},
				link: function (data) {
					var self = this;
					var properties = ["alt", "url", "newwindow"];
					vmCore.call(self, data, properties);
					self.class = "link_modal";
					self.class_id = AW.CLID.extlink;
				},
				file: function (data) {
					var self = this;
					var properties = ["type", "status", "file", "alias", "file_url", "newwindow", "show_framed", "show_icon"];
					vmCore.call(self, data, properties);
					self.class = "file_modal";
					self.class_id = AW.CLID.file;
				},
				image: function (data) {
					var self = this;
					var properties = ["file", "size", "created", "author"];
					vmCore.call(self, data, properties);
					self.class = "image_modal";
					self.class_id = AW.CLID.image;
					self.inProgress = ko.observable(data && data.inProgress ? data.inProgress : false);
					self.url = ko.observable(data && data.url ? data.url : '');
					self.progress = ko.observable(0);
					self.jqXHR = null;
				},
				mini_gallery: function (data) {
					var self = this;
					var properties = ["folder", "images", "view_mode"];
					vmCore.call(self, data, properties);
					self.class = "mini_gallery_modal";
					self.class_id = AW.CLID.mini_gallery;
					self.deleted = ko.observableArray();
					
					self.folder = ko.observableArray(data && data.folder ? data.folder : []);
					self.removeFolder = function (folder) {
						self.folder.remove(folder);
					};
					self.selectFolders = function (gallery, event) {
						AW.UI.modal_search.open("modal_search", { clid: AW.CLID.menu, multiple: true, onSelect: function (items) {
							for (var i in items) {
								var found = false;
								for (var j in self.folder()) {
									if (self.folder()[j].id == items[i].id) {
										found = true;
										break;
									}
								}
								if (!found) {
									self.folder.push(items[i]);
								}
							}
						} });
					};
					self.createFolder = function (gallery, event) {
						AW.UI.modal.open(new AW.viewModel.menu({ parent: self.parent() })).on("save", function (data) {
							self.folder.push(data);
						});
					};
					
					self.imagesData = ko.observableArray();
					self.images = ko.computed({
						read: function () {
							return self.imagesData();
						},
						write: function (images) {
							self.imagesData.removeAll();
							$.each(images, function (i, imageData) {
								self.imagesData.push(new AW.viewModel.image(imageData));
							});
						},
						owner: self
					});
					self.images.toJS = function () {
						$.each(self.images(), function (i, image) {
							// FIXME: MIGHT FAIL IF MORE THAN ONE MINI_GALLERY_MODAL OPEN!!!
							image.ord($("table[id$='images_table'] tr[data-id*='" + image.id() + "']").index() * 100);
						});
						return $.map(self.images(), function (image) { return image.toJS() } );
					};
					self.images(data && data.images ? data.images : []);
					
					self.loadImages = function (callbacks) {
						self.reload("images", callbacks);
					}
					self.addImage = function (imageData) {
						var image = new AW.viewModel.image(imageData);
						self.imagesData.push(image);
						return image;
					};
					self.removeImage = function (image) {
						self.imagesData.remove(image);
						if (image.jqXHR) {
							image.jqXHR.abort();
							image.jqXHR = null;
						}
						// FIXME: Must be a better way for avoiding uploading data for removed new image files
						image.url = null;
						self.deleted.push(image);
					}
					
					self.canSave = function () {
						var canSave = true;
						$.each(self.images(), function (i, image) {
							if (image.jqXHR) {
								canSave = false;
							}
						});
						return canSave;
					};
					
					AW.UI.modal.load("menu_modal");
				},
				menu: function (data) {
					var self = this;
					var properties = ["status", "alias", "status_recursive"];
					vmCore.call(self, data, properties);
					self.class = "menu_modal";
					self.class_id = AW.CLID.menu;
				},
				email: function(_data) {
					var self = this;
					var properties = ["mail", "contact_type"];
					vmCore.call(self, _data, properties);
//					self.class = "XXX_modal";
//					self.class_id = AW.CLID.___;
					self.contact_type_caption = ko.computed(function() {
						// FIXME: Hard-coded ID
						return $("#contact-email-contact_type option[value=" + self.contact_type() + "]").html();
					}, self);
					self.name = ko.computed(function() {
						return self.mail();
					}, self);
				},
				phone: function(_data) {
					var self = this;
					var properties = ["type"];
					vmCore.call(self, _data, properties);
//					self.class = "XXX_modal";
//					self.class_id = AW.CLID.___;
					self.type_caption = ko.computed(function() {
						// FIXME: Hard-coded ID
						return $("#contact-phone-type option[value='" + self.type() + "']").html();
					}, self);
				},
				address: function(_data) {
					var self = this;
					var properties = ["street", "house", "apartment", "postal_code", "coord_x", "coord_y", "details", "section"];
					vmCore.call(self, _data, properties);
//					self.class = "XXX_modal";
//					self.class_id = AW.CLID.___;
					var administrative_units = ["vald", "city", "county", "country"];
					for (var i in administrative_units) {
						if (_data && typeof _data[administrative_units[i]] === "object" && _data[administrative_units[i]] !== null) {
							self[administrative_units[i]] = ko.observable(_data[administrative_units[i]].id);
							self[administrative_units[i] + "_caption"] = ko.observable(_data[administrative_units[i]].name);
						} else {
							self[administrative_units[i]] = ko.observable();
							self[administrative_units[i] + "_caption"] = ko.observable();
						}
					}
					self.name = ko.computed(function() {
						var address = [];
						if (self.street()) {
							address.push(self.street() + " ");
						}
						if (self.house()){
							address[0] += self.house();
							if (self.apartment()) {
								address[0] += "-" + self.apartment();
							}
						}
						if (self.details()) {
							address[0] += " (" + self.details() + ")";
						}
						for (var i in administrative_units) {
							if (self[administrative_units[i] + "_caption"]()) {
								address.push(self[administrative_units[i] + "_caption"]());
							}
						}
						return address.join(", ");
					});
					self.types_caption = ko.computed(function() {
						return null;
					}, self);
					self.section_caption = ko.computed(function() {
						return null;
					}, self);
					self.country_caption = ko.computed(function() {
						return "Eesti";//$("#contact-address-country option[value='" + self.country() + "']").html();
					}, self);
					self.type = ko.observableArray(_data && typeof _data.type !== "undefined" ? _data.type : []);
					self.type_caption = ko.computed(function() {
						var types = [];
						$("#contact-address-type option").each(function() {
							var option = $(this);
							if ($.inArray(option.attr("value"), self.type()) !== -1) {
								types.push(option.html());
							}
						});
						return types	.join(", ");
					}, self);
				},
				employee: function(_data) {
					var self = this;
					var properties = ["firstname", "lastname", "gender", "email", "phone"];
					vmCore.call(self, _data, properties);
//					self.class = "XXX_modal";
//					self.class_id = AW.CLID.___;
					self.name = ko.computed(function() {
						return self.firstname() + " " + self.lastname();
					}, self);
					self.gender_caption = ko.computed(function() {
						return $("#employee-gender option[value='" + self.gender() + "']").html();
					}, self);
					self.skills = ko.observableArray(_data && typeof _data.skills !== "undefined" ? _data.skills : []);
					self.skills_caption = ko.computed(function() {
						var skills = [];
						$("#employee-skills option").each(function() {
							var option = $(this);
							if ($.inArray(option.attr("value"), self.skills()) !== -1) {
								skills.push(option.data("caption"));
							}
						});
						return skills.join(", ");
					}, self);
				},
				customer_relation: function(_data) {
					var self = this;
					var properties = ["seller", "buyer", "client_manager", "categories"];
					vmCore.call(self, _data, properties);
//					self.class = "XXX_modal";
//					self.class_id = AW.CLID.___;
				},
				// FIXME: Abstract viewModel, should it be set 'private'?
				customer: function(_data, properties) {
					var self = this;
					if (!_data) { _data = {}; }
					vmCore.call(self, _data, properties);
//					self.class = "XXX_modal";
//					self.class_id = AW.CLID.___;
					
					self.customer_relation = ko.observable(new AW.viewModel.customer_relation(typeof _data.customer_relation !== "undefined" ? _data.customer_relation : {}));
					self.isBuyer = ko.computed(function() {
						if (self.id()) {
							if (self.customer_relation().buyer()) {
								return self.customer_relation().buyer() == self.id();
							}
						}
						return data.type == 2;
					}, self);
					self.isSeller = ko.computed(function() {
						return !self.isBuyer();
					}, self);
		
					self.emails = ko.observableArray();
					if (_data["emails"]) {
						for (var i in _data["emails"]) {
							self.emails.push(new AW.viewModel.email(_data["emails"][i]));
						}
					}
					self.email_selected = ko.observable(new AW.viewModel.email());
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
						self.email_selected(new AW.viewModel.email());
					}
		
					self.phones = ko.observableArray();
					if (_data["phones"]) {
						for (var i in _data["phones"]) {
							self.phones.push(new AW.viewModel.phone(_data["phones"][i]));
						}
					}
					self.phone_selected = ko.observable(new AW.viewModel.phone());
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
						self.phone_selected(new AW.viewModel.phone());
					}
		
					self.addresses = ko.observableArray();
					if (_data["addresses"]) {
						for (var i in _data["addresses"]) {
							self.addresses.push(new AW.viewModel.address(_data["addresses"][i]));
						}
					}
					self.address_selected = ko.observable(new AW.viewModel.address());
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
						self.address_selected(new AW.viewModel.address());
						$("#contact-address-edit").slideUp(200);
					}
				},
				// FIXME: Could I use person instead?
				customer_person: function(_data) {
					var self = this;
					var properties = ["firstname", "lastname", "personal_id", "birth_date"];
					AW.viewModel.customer.call(this, _data, properties);
					self.name = ko.computed(function() {
						return self.firstname() + " " + self.lastname();
					}, self);
					self.birth_date_show = ko.observable();
					self.birth_date_show(AW.util.convertTimestampToDate(self.birth_date()));
					self.birth_date_show.subscribe(function(date_string) {
						var year = date_string.substring(6, 10);
						var month = date_string.substring(3, 5) - 1;
						var day = date_string.substring(0, 2);
						var date = new Date(year, month, day);
						self.birth_date(Date.parse(date)/1000);
					});
				},
				// FIXME: Could I use company instead?
				customer_company: function(_data) {
					var self = this;
					if (!_data) { _data = {}; }
					var properties = ["short_name", "comment", "year_founded", "reg_nr", "ettevotlusvorm", "tax_nr", "sections"];
					AW.viewModel.customer.call(this, _data, properties);
					self.year_founded_show = ko.observable();
					self.year_founded_show(AW.util.convertTimestampToDate(self.year_founded()));
					self.year_founded_show.subscribe(function(date_string) {
						var year = date_string.substring(6, 10);
						var month = date_string.substring(3, 5) - 1;
						var day = date_string.substring(0, 2);
						var date = new Date(year, month, day);
						self.year_founded(Date.parse(date)/1000);
					});
					
					self.employees = ko.observableArray();
					if (_data["employees"]) {
						for (var i in _data["employees"]) {
							self.employees.push(new AW.viewModel.employee(_data["employees"][i]));
						}
					}
					self.employee_selected = ko.observable(new AW.viewModel.employee());
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
						$("#employees-edit").slideDown(200);
					}
					self.removeEmployee = function(employee, event) {
						if (!event) { return; }
						customerView.removed.push(employee.id());
						self.employees.remove(employee)
					}
					self.resetEmployee = function(customerView, event) {
						self.employee_selected(new AW.viewModel.employee());
						$("#employees-edit").slideUp(200);
					}
				},
				price_component: function(_data) {
					var self = this;
					var properties = ["value", "is_ratio", "vat", "applied"];
					vmCore.call(self, _data, properties);
//					self.class = "XXX_modal";
//					self.class_id = AW.CLID.___;
				},
				order_row: function(_data) {
					var self = this;
					var properties = ["title", "article", "article_name", "description", "price", "quantity", "unit"];
					vmCore.call(self, _data, properties);
//					self.class = "XXX_modal";
//					self.class_id = AW.CLID.___;
					this.price_components = ko.observable({});
					for (var key in _data.price_components) {
						this.price_components()[key] = new AW.viewModel.price_pomponent(_data.price_components[key]);
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
						AW.UI.modal_search.open("modal_search_shop", { defaultSource: 751743, onSelect: function (item) {
							if (item) {
								AW.UI.order_management.add_article(orderRow.id(), item);
							}
						} });
					};
				},
				order: function(_data) {
					var self = this;
					var properties = ["seller", "customer", "total"];
					vmCore.call(self, _data, properties);
//					self.class = "XXX_modal";
//					self.class_id = AW.CLID.___;
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
							self.rows.push(new AW.viewModel.order_row(_data.rows[i]));
						}
					}
					self.total = ko.computed(function(){
						var total = 0;
						for (var i in this.rows()) {
							total += this.rows()[i].total();
						}
						return total;
					}, self);
				},
				crm_meeting: function (_data) {
					var vmParticipant = function (_data) {
						var self = this;
						var properties = ["id", "impl", "time_guess", "time_real", "time_to_cust", "billable"];
						vmCore.call(self, _data, properties);
					};
					
					var self = this;
					if (_data && _data.start1) {
						_data.start1_show = AW.util.convertTimestampToDate(_data.start1);
					}
					if (_data && _data.end) {
						_data.end_show = AW.util.convertTimestampToDate(_data.end);
					}
					var properties = ["start1_show", "end_show", "content"];
					vmCore.call(self, _data, properties);
					self.class = "crm_meeting_modal";
					self.class_id = AW.CLID.crm_meeting;
					
					self.start1 = ko.computed(function(){
						var d = self.start1_show().match(/^(\d{2})\/(\d{2})\/(\d{4}) (\d{2}):(\d{2})$/);
						if (!d) {
							return null;
						}
						var date = new Date(d[3], d[2] - 1, d[1], d[4], d[5]);
						return date.getTime()/1000;
					}, self);
					self.end = ko.computed(function(){
						var d = self.end_show().match(/^(\d{2})\/(\d{2})\/(\d{4}) (\d{2}):(\d{2})$/);
						if (!d) {
							return null;
						}
						var date = new Date(d[3], d[2] - 1, d[1], d[4], d[5]);
						return date.getTime()/1000;
					}, self);
					
					var addParticipants = function (items) {
						for (var i in items) {
							var found = false;
							for (var j in self.participants()) {
								if (self.participants()[j].impl().id == items[i].id) {
									found = true;
									break;
								}
							}
							if (!found) {
								self.participants.push(new vmParticipant({ impl: items[i] }));
							}
						}
					};
					participants = [];
					if (_data && _data.participants) {
						for (var i in _data.participants) {
							participants.push(new vmParticipant(_data.participants[i]));
						}
					}
					self.participants = ko.observableArray(participants);
					self.selectParticipantsFromColleagues = function () {
						AW.UI.modal_search.open("modal_search_employee", { defaultSource: null, multiple: true, onSelect: addParticipants });
					};
					self.selectParticipantsFromClients = function () {
						AW.UI.modal_search.open("modal_search_customer_employee", { defaultSource: null, multiple: true, onSelect: addParticipants });
					};
					self.removeParticipant = function (participant) {
						self.participants.remove(participant);
					}
					
					self.attachments = ko.observableArray();
					self.selectAttachments = function () {
						AW.UI.modal_search.open("modal_search", { rootParent: 751702, multiple: true, onSelect: function (items) {
							for (var i in items) {
								var found = false;
								for (var j in self.attachments()) {
									if (self.attachments()[j].id == items[i].id) {
										found = true;
										break;
									}
								}
								if (!found) {
									self.attachments.push(items[i]);
								}
							}
						} });
					};
					self.removeAttachment = function (attachment) {
						self.attachments.remove(function(item) { return item.id == attachments.id; });
					}
					
					self.toJSON = function() {
						return {
							id: this.id(),
							name: this.name(),
							comment: this.comment(),
							content: this.content(),
							start1: this.start1(),
							end: this.end(),
							participants: ko.toJS(this.participants())
						};
					}
				}
			};
		})()
	};
})());
$.extend(window.AW.UI, (function(){
	return {
		layout: (function(){
			return {
				toggle: function (event) {
					var toggler = $(event.currentTarget);
					var layout = toggler.parents(".layout:first");
					var content = layout.find(".layout-content");
					if (content.is(":visible")) {
						content.hide();
						toggler.html('<i class="icon-chevron-down"></i>');
					} else {
						content.show();
						toggler.html('<i class="icon-chevron-up"></i>');
					}
				}
			};
		})(),
		sublayout: (function(){
			return {
				toggle: function (event) {
					var toggler = $(event.currentTarget);
					var layout = toggler.parents(".sublayout:first");
					var content = layout.find("li:not(.nav-header)");
					if (content.is(":visible")) {
						content.hide();
						toggler.html('<i class="icon-chevron-down"></i>');
					} else {
						content.show();
						toggler.html('<i class="icon-chevron-up"></i>');
					}
				}
			};
		})(),
		table: (function(){
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
				},
				chooser: (function(){
					var selected = false;
					return {
						toggle: function (self) {
							var choosers = $(self).parents("table:first").find("tbody td:first-child input[type='checkbox']");
							selected = !selected;
							choosers.attr("checked", selected);
						}
					}
				})()
			};
		})(),
		modal: (function(){
			var templates = {};
			var counter = 0;
			var modal = function (_model, _element, cfg) {
				this.model = _model;
				this.element = _element.filter(".modal");
				this.element.on("hidden", function () {
					_element.remove();
				});
				ko.applyBindings(this.model, this.element[0]);
				this.close = (function(self){
					return function() {
						self.element.modal("hide");
					};
				})(this);
				// Enabling callbacks
				var callbacks = { save: [] };
				this.on = function (eventType, callback) {
					if (callbacks[eventType]) {
						callbacks[eventType].push(callback);
					}
				};
				// Set up on save
				(function(self){
					self.element.on("click", ".modal-footer [data-click-action~='save']", function () {
						var saveMethod = cfg.save ? cfg.save : self.model.save;
						var disabled = $(this).attr("disabled");
						var close = $(this).is("[data-click-action~='close']");
						if (typeof disabled === "undefined" || disabled === false) {
							self.element.find(".modal-footer a, .modal-footer button").attr('disabled', 'disabled');
							saveMethod({
								success: function (data) {
									AW.UI.modal.alert("Muudatused salvestatud!", "alert-success");
								},
								complete: function () {
									self.element.find(".modal-footer a, .modal-footer button").removeAttr('disabled');
									for (var i in callbacks.save) {
										callbacks.save[i](data);
									}
									if (close) {
										self.close();
									}
								}
							});
						}
					});
				})(this);
				this.element.on("shown", 'a[data-toggle="tab"]', function (e) {
					var target = $(e.target).attr("href").substring(1);
					$(".modal-footer .modal-toolbar").hide();
					$(".modal-footer .modal-toolbar[data-toolbar='" + target + "']").show();
				});
			};
			return {
				open: function (viewModel, cfg) {
					var defaultCfg = { width: 1100, height: 450 };
					if (templates[viewModel.class] === undefined) {
						throw "ERROR: No template for modal of class '" + viewModel.class + "' is loaded!";
					}
					cfg = cfg ? $.extend(defaultCfg, cfg) : defaultCfg;
					
					var html = templates[viewModel.class].replace(/{VAR:prefix}/g, "modal-" + counter.toString() + "-");
					var modalElement = $(html);
					$("body").append(modalElement);
					modalObject = new modal(viewModel, modalElement, cfg);
					var id = "AW-UI-modal-" + counter;
					modalObject.element.attr("id", id);
					modalObject.element.css("width", cfg.width).css("margin-left", -1 * cfg.width / 2);
					modalObject.element.children(".modal-body").css("min-height", cfg.height);
					modalObject.element.modal({ show: true, backdrop: false });
					modalObject.element.draggable({ handle: ".modal-header" });
					modalObject.element.find("*").on("mousedown", (function(m){
						return function(){
							var max = 0;
							$(".modal").each(function (index, item) {
								if (item.id !== id) {
									var z = $(item).css("z-index") * 1;
									max = z > max ? z : max;
								}
							});
							if (max >= m.css("z-index") * 1) {
								m.css("z-index", max + 1);
							}
						};
					})(modalObject.element));
					
					counter++;
					
					return modalObject;
				},
				load: function (modalClass, data, callback) {
					if (templates[modalClass] !== undefined) {
						callback && callback.call();
					} else {
						$.ajax({
							url: "/automatweb/orb.aw?class=" + modalClass + "&action=parse",
							data: data ? data : {},
							success: function(html) {
								templates[modalClass] = html;
								if (callback) {
									callback && callback.call();
								}
							}
						});
					}
				},
				alert: function (msg, cssClass) {
					var alert = $('<div class="alert ' + cssClass + ' fade in text-center fixed-centered-500-40" style="z-index: 999999;"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>' + msg + '</strong></div>');
					$("body").append(alert);
					alert.alert();
					setTimeout(function() {
						alert.alert('close');
					}, 2500);
				},
				load_group: function (object, event) {
					var target = $(event.currentTarget),
						loader = object[target.data("loader")],
						tab = $(target.attr("href"));
					tab.addClass("modal-tab-loading").children().hide();
					var loaded = loader.call(object, {
						error: (function () { alert("Vaate uuendamine ebaõnnestus!"); }),
						complete: (function () { tab.removeClass("modal-tab-loading").children().show(); })
					});
				}
			};
		})(),
		modal_search: (function(){
			var modal;
			var config = {
				searchClass: "modal_search",
				multiple: false,
				width: 1000,
				height: 300,
				defaultSource: null,
				rootParent: null,
				onSelect: null,
				levels: [{}, {}, {}],
				clid: null
			};
			var vmSearchLevel = function (i) {
				this.index = ko.observable(i);
				this.items = ko.observableArray();
				this.loading = ko.observable(false);
				this.loaded = ko.computed(function(){
					return !this.loading();
				}, this);
				this.caption = config.levels[i] && config.levels[i].caption ? config.levels[i].caption : "";
				this.visible = ko.computed(function () {
					return this.loading() || this.index() == 0 || this.index() == config.levels.length - 1 || this.items().length > 0;
				}, this);
			};
			var vmSearch = function() {
				var self = this;
				
				// Note that the last "level" is not actually considered a level, as it is processed differently.
				var numberOfLevels = config.levels.length - 1;
				
				var parents = [];
				
				var levelLoader;
				self.loadLevel = function (level, skip) {
					if (level < 0) {
						return;
					}
					if (level === undefined) {
						level = config.levels.length - 1;
					}
					if (!skip) {
						self.levels()[level].loading(true);
					}
					parents[level] = [];
					for (var i = level + 1; i < self.levels().length; i++) {
						self.levels()[i].loading(false);
						self.levels()[i].items([]);
						parents[i] = [];
					}
					if (levelLoader) {
						levelLoader.abort();
					}
					var parent = level > 0 ? parents[level - 1] : config.rootParent;
					if (level == 0 || parent && parent.length > 0) {
						levelLoader = $.ajax({
							url: "/automatweb/orb.aw",
							data: {
								"class": config.searchClass,
								action: "children",
								source: self.source(),
								parent: parent,
								name: self.name(),
								oid: self.oid(),
								level: level,
								clid: config.clid
							},
							dataType: "json",
							success: function(children) {
								for (var i in children) {
									if (skip && children[i].level == level) {
										continue;
									}
									self.levels()[children[i].level].items(children[i].items);
									self.levels()[children[i].level].loading(false);
								}
//								$(".antiscroll-wrap").antiscroll();
							}
						});
					} else {
						self.loadLevel(level - 1, true);
//						self.levels()[level].items([]);
//						self.levels()[level].loading(false);
					}
//					$(".antiscroll-wrap").antiscroll();
				}
				
				self.source = ko.observable(config.defaultSource);
				self.source.subscribe(function(newValue) {
					self.loadLevel(0);
				});
				self.name = ko.observable();
				self.oid = ko.observable();
				
				levels = [];
				for (var i in config.levels) {
					levels.push(new vmSearchLevel(i));
				}
				self.levels = ko.observableArray(levels);
				
				self.selected = ko.observableArray([]);
				self.onReady = function(){
					if (config.onSelect) {
						config.onSelect.call(null, config.multiple ? self.selected() : self.selected()[0]);
					}
				};
				self.select = function (item, event) {
					if (!config.multiple) {
						self.selected([item]);
					} else {
						self.selected.push(item);
					}
				};
				self.isSelected = function(item){
					var selected = self.selected();
					for (var i in selected) {
						if (selected[i].id == item.id) {
							return true;
						}
					}
					return false;
				};
				self.remove = function(item, event){
					self.selected.remove(item);
				};
				self.toggle = function(item, event){
					var li = $(event.currentTarget).parent();
					var i = item.level !== undefined ? item.level : li.parent().parent().index();
					if (li.hasClass("active")) {
						li.removeClass("active");
						for (var j in parents[i]) {
							if (parents[i][j] == item.id) {
								parents[i].splice(j, 1);
							}
						}
					} else {
						parents[i].push(item.id);
						li.addClass("active");
					}
					if (i < self.levels().length - 1) {
						self.loadLevel(i + 1);
					}
				};
				self.css = ko.computed(function (a, b, c) {
					var numberOfLevelsVisible = 0;
					$.each(self.levels(), function (index, level) {
						if (level.visible()) {
							numberOfLevelsVisible++;
						}
					});
					return "hack-" + numberOfLevelsVisible;
				}, self);
				self.loadLevel(0);
			};
			return {
				load: function (searchClass, callback) {
					if (searchClass === undefined) {
						searchClass = "modal_search";
					}
					AW.UI.modal.load(searchClass, null, callback);
				},
				open: function (searchClass, custom_config) {
					if (searchClass == "modal_search_employee") {
						custom_config = custom_config || {};
						custom_config.levels = [
							{ caption: "Üksused" },
							{ caption: "Alamüksused" },
							{ caption: "Töötajad" }
						];
					} else if (searchClass == "modal_search_customer_employee") {
						custom_config = custom_config || {};
						custom_config.levels = [
							{ caption: "Kategooriad" },
							{ caption: "Alamkategooriad" },
							{ caption: "Organisatsioonid" },
							{ caption: "Üksused" },
							{ caption: "Alamüksused" },
							{ caption: "Töötajad" }
						];
						
					}
					if (custom_config) {
						$.extend(config, custom_config);
					}
					config.searchClass = searchClass;
					this.load(searchClass, function(){
						var searchView = new vmSearch();
						searchView.class = searchClass;
						AW.UI.modal.open(searchView, { width: config.width, height: config.height });
					});
				}
			};
		})(),
		calendar: (function(){
			var calendarID;
			var map = {};
			var eventDetails;
			var scheduler;
			
			return {
				initialize: function(id) {
					if ($("#" + id).size() === 0) {
						return;
					}
					calendarID = $("#" + id).data("calendar-id");
					$.ajax({
						url: "/automatweb/orb.aw?class=planner&action=get_events",
						data: { id: calendarID, tbtpl: true },
						dataType: "json",
						success: function (events) {
							for (var i in events) {
								events[i].startDate = new Date(events[i].start1*1000);
								events[i].endDate = new Date(events[i].end*1000);
								events[i].awContent = events[i].content;
								events[i].content = events[i].name;
							}
							YUI().use('aui-scheduler', function(Y) {					  
								var agendaView = new Y.SchedulerAgendaView({ string: { noEvents: 'Eelseisvad s&uuml;ndmused puuduvad' } });
								var dayView = new Y.SchedulerDayView({ string: { allDay: 'Kogu p&auml;ev' } });
								var monthView = new Y.SchedulerMonthView();
								var weekView = new Y.SchedulerWeekView();
								var eventRecorder = new Y.SchedulerEventRecorder({
									strings: {
										'delete': 'Kustuta',
										'description-hint': 'e.g., Kohtumine kliendiga',
										cancel: 'Loobu',
										description: 'Kirjeldus',
										edit: 'Muuda',
										save: 'Salvesta',
										when: 'Millal'
									},
									toolbar: {
										children2: [
											[
												{
													label: 'Detailvaade',
													on: {
														click: function () {
															alert("Yeah!");
														}
													}
												},
											]
										]
									}
								});
								
								function showModal (data) {
									eventDetails = new AW.viewModel.crm_meeting(data, {
										save: function (callback) {
											eventDetails.start1_show($("#start1_show").val());
											eventDetails.end_show($("#end_show").val());
											AW.UI.calendar.saveEvent(eventDetails.toJSON(), function () {
												// Insert into calendar
												callback && callback.success && callback.success();
												callback && callback.complete && callback.complete();
											}, false);
										}
									});
									AW.UI.modal.open(eventDetails);
								}
						  
								scheduler = new Y.Scheduler({
									activeView: weekView,
									boundingBox: "#" + id,
									eventRecorder: eventRecorder,
									firstDayOfWeek: 1,
									items: events,
									render: true,
									strings: {
										agenda: 'Agenda',
										day: 'P&auml;ev',
										month: 'Kuu',
										today: 'T&auml;na',
										week: 'N&auml;dal',
										year: 'Aasta'
									},
									views: [dayView, weekView, monthView, agendaView]
								});
								scheduler.on({
									'scheduler-event:change': function(event) {
										var itemData = event.target._state.data;
										AW.UI.calendar.saveEvent({
											id: itemData.id.value || map[itemData.clientId.value] || null,
											clientId: itemData.clientId.value,
											name: itemData.content.value,
											start1: itemData.startDate.value.getTime()/1000,
											end: itemData.endDate.value.getTime()/1000
										});
									},
									'scheduler-events:remove': function(event) {
										var item = scheduler.getEvents()[event.index]._state.data.id.value || null;
										if (item) {
											AW.UI.calendar.deleteEvent(item);
										}
									},
									'scheduler-base:click': function(event) {
										var toolbar = $(".aui-scheduler-event-recorder-overlay .yui3-widget-ft .aui-toolbar-content .aui-btn-group");
										if (!toolbar.data("custom-processed")) {
											var button = $('<button class="aui-btn">Detailid</button>');
											toolbar.append(button);
											button.click(function (event) {
												event.preventDefault();
												var eventData = (eventRecorder.get("event") || eventRecorder.clone())._state.data;
												showModal({
													id: eventData.id.value || map[eventData.clientId.value] || null,
													name: $(".aui-scheduler-event-recorder-overlay .aui-scheduler-event-recorder-overlay-content").val(),
													start1: eventData.startDate.value.getTime()/1000,
													end: eventData.endDate.value.getTime()/1000,
													comment: eventData.comment ? eventData.comment.lazy.value : "",
													content: eventData.awContent ? eventData.awContent.lazy.value : "",
													participants: eventData.participants ? eventData.participants.lazy.value : []
												});
												$(".aui-scheduler-event-recorder-overlay").addClass("yui3-overlay-hidden");
											});
											toolbar.data("custom-processed", true);
										}										
									},
								});
								
								var addNewButton = $('<a href="#" class="btn" style="position: relative; margin-left: 50%; left: -350px;"><i class="icon-plus-sign"></i> Lisa uus s&uuml;ndmus</a>');
								$("#" + id + " .aui-scheduler-base-controls").append(addNewButton);
								
								addNewButton.on("click", function (event) {
									event.preventDefault();
									showModal({ start1: new Date().getTime()/1000, end: new Date().getTime()/1000 + 3600 });
								});
								
								$("body").on("submit", "form", function (event) {
									event.preventDefault();
									return false;
								});
								
								AW.UI.scheduler = scheduler;
								AW.UI.eventRecorder = eventRecorder;
							});
						},
						error: function (a,b,c) {
							console && console.log("ERROR: ", a,b,c);
						}
					});
					
					AW.UI.modal.load("crm_meeting_modal");
					AW.UI.modal_search.load("modal_search");
					AW.UI.modal_search.load("modal_search_employee");
					AW.UI.modal_search.load("modal_search_customer_employee");
				},
				saveEvent: function (itemData, callback, updateUI) {
					$.ajax({
						url: "/automatweb/orb.aw?class=planner&action=save_event",
						data: { id: calendarID, data: itemData },
						dataType: "json",
						success: function (data) {
							map[itemData.clientId] = data.id;
							var updated = false;
							scheduler.eachEvent(function(event, index){
								if (event._state.data.id.value == data.id) {
									event.setContent(data.content);
									updated = true;
								}
							});
							if (updateUI && !updated) {
								scheduler.addEvents([{
									id: data.id,
									content: data.content,
									startDate: new Date(data.startDate*1000),
									endDate: new Date(data.endDate*1000),
									participants: data.participants
								}]);
								scheduler.syncEventsUI();
							}
							if (callback) {
//								eventDetails = new vmEvent(data);
								callback();
							}
						}
					});
				},
				deleteEvent: function (id) {
					$.ajax({
						url: "/automatweb/orb.aw?class=planner&action=delete_event",
						data: { id: id }
					});
				}
			};
		})()
	};
})());
$("*").on("click", "[data-toggle='layout']", AW.UI.layout.toggle);
$("*").on("click", "[data-toggle='sublayout']", AW.UI.sublayout.toggle);
$(document).ready(function(){ AW.UI.calendar.initialize("myScheduler"); });