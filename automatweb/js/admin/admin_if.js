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
		var newIdCount = 0;
		var newId = function () {
			return "new-" + newIdCount++;
		};
		
		var vmCore = function(data, properties) {
			var self = this;
			if (!data) { data = {}; }
			properties = ["id", "class_id", "parent", "name"].concat(properties);
			for (var i in properties) {
				self[properties[i]] = typeof data[properties[i]] !== "undefined" ? ko.observable(data[properties[i]]) : ko.observable();
			}
			self.id = ko.observable(data && data.id ? data.id : newId());
			self.load = function (newData) {
				for (var i in newData) {
					if (self[i]) {
						self[i](newData[i]);
					}
				}
			};
			self.reload = function (keys, callbacks) {
				if (!$.isArray(keys)) keys = [keys];
				var data = self.toJS();
				for (var i in keys) {
					if (data[keys[i]]) {
						delete data[keys[i]];
					}
				}
				$.ajax({
					url: "/automatweb/orb.aw",
					method: "POST",
					data: { "class": self.class ? self.class : "aw_modal", "action": "reload_data", "properties": keys, data: data },
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
			self.canSave = function () {
				return true;
			};
		}
			
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
					var model = new viewModels.file(),
						modal = AW.UI.modal.open("file_modal", { save: getSaveMethod(model) });
					ko.applyBindings(model, modal.element[0]);
				};
				self.createAttachmentImage = function (document, event) {
//					AW.UI.modal.open("file_modal");
				};
				self.createAttachmentLink = function (document, event) {
					var model = new viewModels.link(),
						modal = AW.UI.modal.open("link_modal", { save: getSaveMethod(model) });
					ko.applyBindings(model, modal.element[0]);
				};
				self.createAttachmentDocument = function (document, event) {
					var model = new viewModels.doc(),
						modal = AW.UI.modal.open("document_modal", { save: getSaveMethod(model) });
					ko.applyBindings(model, modal.element[0]);
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
				var properties = ["ord", "comment", "file", "size", "created", "author"];
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
					var model = new viewModels.menu({ parent: self.parent() }),
						modal = AW.UI.modal.open("menu_modal", { save: getSaveMethod(model) });
					modal.on("save", function (data) {
						self.folder.push(data);
					});
					ko.applyBindings(model, modal.element[0]);
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
					return $.map(self.images(), function (image) { return image.toJS() } );
				};
				self.images(data && data.images ? data.images : []);
				
				self.loadImages = function (callbacks) {
					self.reload("images", callbacks);
				}
				self.addImage = function (imageData) {
					var image = new viewModels.image(imageData);
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
				var properties = ["status", "alias", "ord", "comment", "status_recursive"];
				vmCore.call(self, data, properties);
				self.class = "menu_modal";
				self.class_id = AW.CLID.menu;
			}
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
							var model = new viewModels[arr.model](data),
								modal = AW.UI.modal.open(arr.modal, { save: getSaveMethod(model) });
							ko.applyBindings(model, modal.element[0]);
							$.please_wait_window.hide();
						},
					});
				} else {
					var model = new viewModels[arr.model]({ parent: arr.parent }),
						modal = AW.UI.modal.open(arr.modal, { save: getSaveMethod(model) });
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
			}
		};
	})();
	$(document).ready(AW.UI.admin_if.initialize);
});