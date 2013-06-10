if (typeof(AW) == "undefined") {
	window.AW = { UI: {} };
}
if (typeof(AW.UI) == "undefined") {
	window.AW.UI = {};
}
$.extend(window.AW.UI, (function(){
	return {
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
							}
						});
					} else {
						self.levels()[level].items([]);
						self.levels()[level].loading(false);
					}
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
						}
					});
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
				self.onSelect = ko.computed(function(){
					if (config.onSelect) {
						config.onSelect.call(null, config.multiple ? self.selected() : self.selected()[0]);
					}
				}, self);
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
		})()
	};
})());