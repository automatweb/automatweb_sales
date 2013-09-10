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
			var modal = function (_element, cfg) {
				this.element = _element.filter(".modal");
				this.element.on("hidden", function () {
					_element.remove();
				});
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
						var saveMethod = cfg.save ? cfg.save : eval(self.element.find("input[name=save_method]").val());
						var disabled = $(this).attr("disabled");
						var close = $(this).is("[data-click-action~='close']");
						if (typeof disabled === "undefined" || disabled === false) {
							self.element.find(".modal-footer a, .modal-footer button").attr('disabled', 'disabled');
							saveMethod(function (data) {
								AW.UI.modal.alert("Muudatused salvestatud!", "alert-success");
								self.element.find(".modal-footer a, .modal-footer button").removeAttr('disabled');
								for (var i in callbacks.save) {
									callbacks.save[i](data);
								}
								if (close) {
									self.close();
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
				open: function (modalClass, cfg) {
					var defaultCfg = { width: 1100, height: 450 };
					if (templates[modalClass] === undefined) {
						throw "ERROR: No template for modal of class '" + modalClass + "' is loaded!";
					}
					cfg = cfg ? $.extend(defaultCfg, cfg) : defaultCfg;
					
					var html = templates[modalClass].replace(/{VAR:prefix}/g, "modal-" + counter.toString() + "-");
					var modalElement = $(html);
					$("body").append(modalElement);
					modalObject = new modal(modalElement, cfg);
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
						var search = AW.UI.modal.open(searchClass, { width: config.width, height: config.height });
						var searchView = new vmSearch();
						ko.applyBindings(searchView, search.element[0]);
					});
				}
			};
		})(),
		calendar: (function(){
			var calendarID;
			var map = {};
			var eventDetails;
			var scheduler;
			
			var convertTimestampToDate = function(unix_timestamp) {
				var date = new Date(unix_timestamp*1000);
				var day = date.getDate();
				var month = date.getMonth() + 1;
				var hours = date.getHours();
				var minutes = date.getMinutes();
				return (day < 10 ? "0" : "") + day + "/" + (month < 10 ? "0" : "") + month + "/" + date.getFullYear() + " " + (hours < 10 ? "0" : "") + hours + ":" + (minutes < 10 ? "0" : "") + minutes;
			};
			
			var vmCore = function(_data, properties) {
				var self = this;
				if (!_data) { _data = {}; }
				for (var i in properties) {
					self[properties[i]] = typeof _data[properties[i]] !== "undefined" ? ko.observable(_data[properties[i]]) : ko.observable();
				}
			}
			
			var vmParticipant = function (_data) {
				var self = this;
				var properties = ["id", "impl", "time_guess", "time_real", "time_to_cust", "billable"];
				vmCore.call(self, _data, properties);
			};
			
			var vmEvent = function (_data) {
				var self = this;
				if (_data && _data.start1) {
					_data.start1_show = convertTimestampToDate(_data.start1);
				}
				if (_data && _data.end) {
					_data.end_show = convertTimestampToDate(_data.end);
				}
				var properties = ["id", "name", "start1_show", "end_show", "comment", "content"];
				vmCore.call(self, _data, properties);
				
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
			};
			vmEvent.prototype.toJSON = function() {
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
									eventDetails = new vmEvent(data);
									
									var modal = AW.UI.modal.open("crm_meeting_modal");
									
									ko.applyBindings(eventDetails, modal.element[0]);
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
				},
				saveEventDetails: function (callback) {
					eventDetails.start1_show($("#start1_show").val());
					eventDetails.end_show($("#end_show").val());
					AW.UI.calendar.saveEvent(eventDetails.toJSON(), function () {
						// Insert into calendar
						callback();
					}, false);
				}
			};
		})()
	};
})());
$("*").on("click", "[data-toggle='layout']", AW.UI.layout.toggle);
$("*").on("click", "[data-toggle='sublayout']", AW.UI.sublayout.toggle);
$(document).ready(function(){ AW.UI.calendar.initialize("myScheduler"); });