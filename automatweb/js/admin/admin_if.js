YUI().use("node", function(Y) {
	if (typeof(AW) == "undefined") {
		window.AW = { UI: {} };
	}
	if (typeof(AW.UI) == "undefined") {
		window.AW.UI = {};
	}
	if (typeof(AW.CLID) == "undefined") {
		window.AW.CLID = {
			menu: 1,
			file: 41,
			image: 6,
			extlink: 21,
			document: 7,
			mini_gallery: 318
		};
	}
	AW.UI.admin_if = (function() {
		var self = this;
		
		var vmCore = function(data, properties) {
			var self = this;
			if (!data) { data = {}; }
			properties = ["id", "class_id", "parent", "name"].concat(properties);
			for (var i in properties) {
				self[properties[i]] = typeof data[properties[i]] !== "undefined" ? ko.observable(data[properties[i]]) : ko.observable();
			}
			self.reload = function (keys, callbacks) {
				if (!$.isArray(keys)) keys = [keys];
				$.ajax({
					url: "/automatweb/orb.aw",
					method: "POST",
					data: { "class": self.class ? self.class : "aw_modal", "action": "reload_data", "properties": keys, data: self.toJS() },
					dataType: "json",
					success: function (data) {
						for (var i in keys) {
							if (self[keys[i]]) {
								self[keys[i]](data[keys[i]]);
							}
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
					if (value == null || $.isArray(value) && value.length == 0) {
						value = "";
					}
					data[properties[i]] = value;
				}
				return data;
			};
		}
		
		var viewModels = {
			doc: function (data) {
				var vmParticipant = function (data) {
					var self = this;
					var properties = ["chosen", "phone", "email", "organisation", "participantion_type", "permissions"];
					vmCore.call(self, data, properties);
				};
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
					var modal = AW.UI.modal.open("file_modal");
					ko.applyBindings(new viewModels.file, modal.element[0]);
				};
				self.createAttachmentImage = function (document, event) {
					if (typeof document == "undefined") { return; }
//					AW.UI.modal.open("file_modal");
				};
				self.createAttachmentLink = function (document, event) {
					if (typeof document == "undefined") { return; }
					var modal = AW.UI.modal.open("link_modal");
					ko.applyBindings(new viewModels.link, modal.element[0]);
				};
				self.createAttachmentDocument = function (document, event) {
					if (typeof document == "undefined") { return; }
					var modal = AW.UI.modal.open("document_modal");
					ko.applyBindings(new viewModels.doc, modal.element[0]);
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
				var properties = ["comment", "alt", "url", "newwindow"];
				vmCore.call(self, data, properties);
				self.class = "link_modal";
				self.class_id = AW.CLID.extlink;
			},
			file: function (data) {
				var self = this;
				var properties = ["status", "file", "ord", "comment", "alias", "file_url", "newwindow", "show_framed", "show_icon"];
				vmCore.call(self, data, properties);
				self.class = "file_modal";
				self.class_id = AW.CLID.file;
			},
			image: function (data) {
				var self = this;
				var properties = ["ord", "comment", "url"];
				vmCore.call(self, data, properties);
				self.class = "image_modal";
				self.class_id = AW.CLID.image;
			},
			mini_gallery: function (data) {
				var self = this;
				var properties = ["comment", "folder", "images", "view_mode"];
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
					alert("Implementeerimata!");
					return;
					var modal = AW.UI.modal.open("menu_modal");
					ko.applyBindings(new viewModels.menu, modal.element[0]);
				};
				
				self.imagesData = ko.observableArray();
				self.images = ko.computed({
					read: function () {
						return self.imagesData();
					},
					write: function (images) {
						self.imagesData.removeAll();
						$.each(images, function (i, imageData) {
							self.imagesData.push(new viewModels.image(imageData));
						});
					},
					owner: self
				});
				self.images.toJS = function () {
					$.each(self.images(), function (i, image) {
						// FIXME: MIGHT FAIL IF MORE THAN ONE MINI_GALLERY_MODAL OPEN!!!
						image.ord($("table[id$='images_table'] tr[data-id*='" + image.id() + "']").index() * 100);
					});
					return ko.toJS(self.images());
				};
				self.images(data && data.images ? data.images : []);
				
				self.loadImages = function (callbacks) {
					self.reload("images", callbacks);
				}
				self.addImage = function (imageData) {
					self.imagesData.push(new viewModels.image(imageData));
				};
				self.removeImage = function (image) {
					self.images.remove(image);
					self.deleted.push(image);
				}
//				AW.UI.modal.load("menu_modal");
			}
		};
		
		var model,
			modal;
		
		return {
			open_modal: function (arr) {
				if (arr.id) {
					$.please_wait_window.show();
					$.ajax({
						url: "/automatweb/orb.aw?class=" + arr.modal + "&action=get_data",
						dataType: "json",
						data: { oid: arr.id },
						success: function(data) {
							model = new viewModels[arr.model](data);
							modal = AW.UI.modal.open(arr.modal);
							ko.applyBindings(model, modal.element[0]);
							$.please_wait_window.hide();
						},
					});
				} else {
					model = new viewModels[arr.model]({ parent: arr.parent });
					modal = AW.UI.modal.open(arr.modal);
					ko.applyBindings(model, modal.element[0]);
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
			},
			save: function (post_save_callback) {
				$.ajax({
					url: "/automatweb/orb.aw",
					type: "POST",
					dataType: "json",
					data: {
						"class": model.class,
						"action": "save",
						"class_id": model.class_id,
						"parent": model.parent,
						"deleted": model.deleted ? ko.toJS(model.deleted) : null,
						"data": model.toJS ? model.toJS() : ko.toJS(model)
					},
					success: function(data) {
						model.id(data.id);
//						customerView.file(new vmFile(_data));
					},
					error: function() {
						alert("Andmete salvestamine eba›nnestus!");
					},
					complete: function() {
//						$.please_wait_window.hide();
						post_save_callback();
						reload_property(["o_tbl"]);
					}
				});
			}
		};
	})();
	$(document).ready(AW.UI.admin_if.initialize);
});