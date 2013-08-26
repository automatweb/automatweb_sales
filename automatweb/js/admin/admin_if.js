YUI().use("node", function(Y) {
	if (typeof(AW) == "undefined") {
		window.AW = { UI: {} };
	}
	if (typeof(AW.UI) == "undefined") {
		window.AW.UI = {};
	}
	if (typeof(AW.CLID) == "undefined") {
		window.AW.CLID = {
			file: 41,
			image: 6,
			extlink: 21,
			document: 7
		};
	}
	AW.UI.admin_if = (function() {
		var self = this;
		
		var vmCore = function(data, properties) {
			var self = this;
			if (!data) { data = {}; }
			for (var i in properties) {
				self[properties[i]] = typeof data[properties[i]] !== "undefined" ? ko.observable(data[properties[i]]) : ko.observable();
			}
		}
		
		var viewModels = {
			doc: function (data) {
				var vmParticipant = function (data) {
					var self = this;
					var properties = ["id", "chosen", "name", "phone", "email", "organisation", "participantion_type", "permissions"];
					vmCore.call(self, data, properties);
				};
				var vmAttachment = function (data) {
					var self = this;
					var properties = ["id", "name", "type"];
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
				var properties = ["id", "parent", "title", "status", "document_status", "document_status_options", "lead", "content", "moreinfo", "show_title", "showlead", "show_modified", "esilehel", "title_clickable", "participation_type_options", "permission_options", "reg_date", "make_date", "parents"];
				vmCore.call(self, data, properties);
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
					var modal = AW.UI.modal.open("file_modal");
					ko.applyBindings(new viewModels.file, modal.element[0]);
				};
				self.createAttachmentImage = function (document, event) {
//					AW.UI.modal.open("file_modal");
				};
				self.createAttachmentLink = function (document, event) {
					var modal = AW.UI.modal.open("link_modal");
					ko.applyBindings(new viewModels.link, modal.element[0]);
				};
				self.createAttachmentDocument = function (document, event) {
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
					AW.UI.modal_search.open("modal_search", { clid: AW.CLID.file, multiple: true, onSelect: addAttachments });
				};
				self.searchAttachmentImage = function (document, event) {
					AW.UI.modal_search.open("modal_search", { clid: AW.CLID.image, multiple: true, onSelect: addAttachments });
				};
				self.searchAttachmentLink = function (document, event) {
					AW.UI.modal_search.open("modal_search", { clid: AW.CLID.extlink, multiple: true, onSelect: addAttachments });
				};
				self.searchAttachmentDocument = function (document, event) {
					AW.UI.modal_search.open("modal_search", { clid: AW.CLID.document, multiple: true, onSelect: addAttachments });
				};
				AW.UI.modal.load("file_modal");
				AW.UI.modal.load("link_modal");
				AW.UI.modal_search.load("modal_search");
			},
			link: function (data) {
				var self = this;
				var properties = ["id", "parent", "name", "comment", "alt", "url", "newwindow"];
				vmCore.call(self, data, properties);
			},
			file: function (data) {
				var self = this;
				var properties = ["id", "parent", "name", "status", "file", "ord", "comment", "alias", "file_url", "newwindow", "show_framed", "show_icon"];
				vmCore.call(self, data, properties);
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
				
				AW.UI.modal.load("document_modal");
				AW.UI.modal_search.load("modal_search_employee");
				$("body").on("click", "a", function (event) {
					var a = $(this),
						href = a.attr("href");
					if (getQueryVariable(href, "class") == "doc" && getQueryVariable(href, "action") == "change") {
						event.preventDefault();
						AW.UI.admin_if.open_modal({
							modal: "document_modal",
							model: "doc",
							id: getQueryVariable(href, "id")
						});
					} else if (getQueryVariable(href, "class") == "doc" && getQueryVariable(href, "action") == "new") {
						event.preventDefault();
						AW.UI.admin_if.open_modal({
							modal: "document_modal",
							model: "doc",
							parent: getQueryVariable(href, "parent")
						});
					}
				});
			},
			save: function (post_save_callback) {
				$.ajax({
					url: "/automatweb/orb.aw?class=document_modal&action=save",
					type: "POST",
					dataType: "json",
					data: {
						class_id: 7,
						parent: model.parent,
//						removed: ko.toJS(model.removed),
						data: ko.toJS(model)
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