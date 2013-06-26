if (typeof(AW) == "undefined") {
	window.AW = { UI: {} };
}
if (typeof(AW.UI) == "undefined") {
	window.AW.UI = {};
}
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
			var modal = function (_element) {
				self = this;
				self.element = _element.filter(".modal");
				self.element.on("hidden", function () {
					_element.remove();
				});
				self.close = function() {
					self.element.modal("hide");
				};
			};
			return {
				open: function (modalClass, cfg) {
					if (templates[modalClass] === undefined) {
						throw "ERROR: No template for modal of class '" + modalClass + "' is loaded!";
					}
					cfg = cfg ? cfg : { width: 1100, height: 450 };
					
					var html = templates[modalClass].replace("{VAR:prefix}", counter);
					var modalElement = $(html);
					$("body").append(modalElement);
					modalObject = new modal(modalElement);
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
						callback.call();
					} else {
						$.ajax({
							url: "/automatweb/orb.aw?class=" + modalClass + "&action=parse",
							data: data ? data : {},
							success: function(html) {
								templates[modalClass] = html;
								if (callback) {
									callback.call();
								}
							}
						});
					}
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
				onSelect: null
			};
			var vmSearchLevel = function() {
				this.items = ko.observableArray();
				this.loading = ko.observable(false);
				this.loaded = ko.computed(function(){
					return !this.loading();
				}, this);
			};
			var vmSearch = function() {
				var self = this;
				
				var parents = [[],[]];
				
				var levelLoader;
				function loadLevel(level) {
					self.levels()[level].loading(true);
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
							data: { "class": config.searchClass, action: "children", source: self.source(), parent: parent },
							dataType: "json",
							success: function(children){
								self.levels()[level].items(children);
								self.levels()[level].loading(false);
								$(".antiscroll-wrap").antiscroll();
							}
						});
					} else {
						self.levels()[level].items([]);
						self.levels()[level].loading(false);
					}
					$(".antiscroll-wrap").antiscroll();
				}
				var resultsLoader;
				self.loadResults = function() {
					self.loading(true);
					if (resultsLoader) {
						resultsLoader.abort();
					}
					var parent;
					for (var i = 0; i < parents.length; i++) {
						if (parents[i].length > 0) {
							parent = parents[i];
						}
					}
					resultsLoader = $.ajax({
						url: "/automatweb/orb.aw",
						data: { "class": config.searchClass, action: "search", source: self.source(), parent: parent, name: self.name(), oid: self.oid() },
						dataType: "json",
						success: function(results){
							self.results(results);
							self.loading(false);
							$(".antiscroll-wrap").antiscroll();
						}
					});
					$(".antiscroll-wrap").antiscroll();
				}
				
				self.source = ko.observable(config.defaultSource);
				self.source.subscribe(function(newValue) {
					loadLevel(0);
					self.loadResults();
				});
				self.name = ko.observable();
				self.oid = ko.observable();
				self.levels = ko.observableArray([new vmSearchLevel(), new vmSearchLevel()]);
				self.results = ko.observableArray();
				self.loading = ko.observable(false);
				self.selected = ko.observableArray([]);
				self.onReady = function(){
					if (config.onSelect) {
						config.onSelect.call(null, config.multiple ? self.selected() : self.selected()[0]);
					}
				};
				self.select = function(item, event){
					if (!config.multiple) {
						self.selected([item]);
					} else {
						self.selected.push(item);
					}
/*					var li = $(event.currentTarget).parent();
					if (li.hasClass("active")) {
						li.removeClass("active");
						self.selected([]);
					} else {
						li.addClass("active").siblings().removeClass("active");
						self.selected.push(item);
					}
*/
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
					var i = li.parent().parent().index();
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
						loadLevel(i + 1);
					}
					self.loadResults();
				};
				loadLevel(0);
			};
			return {
				load: function (searchClass, callback) {
					if (searchClass === undefined) {
						searchClass = "modal_search";
					}
					AW.UI.modal.load(searchClass, null, callback);
				},
				open: function (searchClass, custom_config) {
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
			
			var vmEvent = function (_data) {
				var self = this;
				if (_data && _data.startDate && !_data.start1) {
					_data.start1 = _data.startDate;
				}
				if (_data && _data.endDate && !_data.end) {
					_data.end = _data.endDate;
				}
				if (_data && _data.start1) {
					_data.start1_show = convertTimestampToDate(_data.start1);
				}
				if (_data && _data.end) {
					_data.end_show = convertTimestampToDate(_data.end);
				}
				var properties = ["id", "name", "start1_show", "end_show"];
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
							if (self.participants()[j].id == items[i].id) {
								found = true;
								break;
							}
						}
						if (!found) {
							self.participants.push(items[i]);
						}
					}
				};
				
				self.participants = ko.observableArray(_data && _data.participants ? _data.participants : []);
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
					id: this.id,
					name: this.name,
					start1: this.start1,
					end: this.end,
					participants: this.participants()
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
								events[i].startDate = new Date(events[i].startDate*1000);
								events[i].endDate = new Date(events[i].endDate*1000);
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
										console && console.log(event.type);
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
										console && console.log(event.type);
										var item = scheduler.getEvents()[event.index]._state.data.id.value || null;
										if (item) {
											AW.UI.calendar.deleteEvent(item);
										}
									},
									'scheduler-event-recorder:edit': function(event) {
										return;
										console && console.log(event.type);
										var itemData = eventRecorder.getUpdatedSchedulerEvent()._state.data;
										AW.UI.calendar.saveEvent({
											id: itemData.id.value || map[itemData.clientId.value] || null,
											clientId: itemData.clientId.value,
											name: itemData.content.value,
											start1: itemData.startDate.value.getTime()/1000,
											end: itemData.endDate.value.getTime()/1000
										});
									},
									'scheduler-base:click': function(event) {
										console && console.log(event.type);
										var toolbar = $(".aui-scheduler-event-recorder-overlay .yui3-widget-ft .aui-toolbar-content .aui-btn-group");
										if (!toolbar.data("custom-processed")) {
											var button = $('<button class="aui-btn">Detailid</button>');
											toolbar.append(button);
											button.click(function (event) {
												event.preventDefault();
												var eventData = (eventRecorder.get("event") || eventRecorder.clone())._state.data;
												console && console.log(eventData);
												showModal({
													id: eventData.id.value || map[eventData.clientId.value] || null,
													name: $(".aui-scheduler-event-recorder-overlay .aui-scheduler-event-recorder-overlay-content").val(),
													start1: eventData.startDate.value.getTime()/1000,
													end: eventData.endDate.value.getTime()/1000,
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
								eventDetails = new vmEvent(data);
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