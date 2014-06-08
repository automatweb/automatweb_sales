ko.computed = (function (computed) {
	return function (b, c, d) {
		if (typeof b === "function") b = { read: b, write: function () {} }
		return computed(b, c, d);
	};
})(ko.computed);
ko.bindingHandlers.datepick = {
	init: function(element, valueAccessor, allBindingsAccessor, viewModel, bindingContext) {
        var options = allBindingsAccessor().datepickOptions || {};

        $(element).datepick({
			altFormat: $.datepick.TIMESTAMP,
			yearRange: "-0:+2",
			onSelect: function(date) {
				var observable = valueAccessor();
				observable($(this).val());
				if (options.altField) {
					var date = $(this).datepick("getDate");
					if (date.length > 0) {
						options.altField(date[0].getTime()/1000);
					}
				}
			},
			multiSelect: options.multiSelect ? options.multiSelect : 0
		});

        //handle the field changing
        ko.utils.registerEventHandler(element, "change", function () {
            var observable = valueAccessor();
            observable($(element).val());
			if (options.altField) {
				var date = $(this).datepick("getDate");
				if (date.length > 0) {
					options.altField(date[0].getTime()/1000);
				}
			}
        });

        //handle disposal (if KO removes by the template binding)
        ko.utils.domNodeDisposal.addDisposeCallback(element, function() {
            $(element).datepick("destroy");
        });
    },
    update: function(element, valueAccessor, allBindingsAccessor, viewModel, bindingContext) {
        var value = ko.utils.unwrapObservable(valueAccessor());

        //handle date data coming via json from Microsoft
        if (String(value).indexOf('/Date(') == 0) {
            value = new Date(parseInt(value.replace(/\/Date\((.*?)\)\//gi, "$1")));
        }

        var current = $(element).datepick("getDate");

        if (value - current !== 0) {
            $(element).datepick("setDate", value);
        }
    }
};

ko.bindingHandlers.datetimepicker = {
    init: function (element, valueAccessor, allBindingsAccessor, viewModel) {
        var value = ko.utils.unwrapObservable(valueAccessor()),
			options = allBindingsAccessor().datetimepickerOptions || {},
			$element = $(element),
			$date_div = $('<div class="input-append"></div>'),
			$time_div = $('<div class="input-append"></div>'),
			$date_input = $('<input data-format="dd/MM/yyyy" class="input-small" type="text"></input>'),
			$time_input = $('<input data-format="hh:mm" class="input-small" type="text"></input>');
			
		if (options.pickDate != false) {
			$date_div.append($date_input);
			$date_div.append('<span class="add-on" style="margin-right: 5px;"><i data-time-icon="icon-time" data-date-icon="icon-calendar" class="icon-calendar"></i></span>');
			$element.append($date_div);
			$date_div.datetimepicker({
				pickTime: false
			});
		}
		
		if (options.pickTime != false) {
			$time_div.append($time_input);
			$time_div.append('<span class="add-on"><i data-time-icon="icon-time" data-date-icon="icon-calendar" class="icon-calendar"></i></span>');
			$element.append($time_div);
			$time_div.datetimepicker({
				pickDate: false,
				pickSeconds: false
			});
		}
	
		if (value) {
			var datetime = AW.util.formatTimestamp(value, "/");
			options.pickDate != false && $date_div.datetimepicker('setValue', datetime.substr(0, 10));
			options.pickTime != false && $time_div.datetimepicker('setValue', datetime.substr(11, 5));
		}
		
		// FIXME: Surely, there must be a more elegant way for doing this?
		var oldVal = $date_input.val() + $time_input.val();
		setInterval(function () {
			var newVal = $date_input.val() + $time_input.val();
			if (oldVal !== newVal) {
				if (options.pickDate == false) {
					valueAccessor()(newVal);
				} else {
					if (options.pickTime == false) {
						var d = newVal.match(/^(\d{2})\/(\d{2})\/(\d{4})$/);
						var date = d ? new Date(d[3], d[2] - 1, d[1]) : null;
					} else {
						var d = newVal.match(/^(\d{2})\/(\d{2})\/(\d{4})(\d{2}):(\d{2})$/);
						var date = d ? new Date(d[3], d[2] - 1, d[1], d[4], d[5]) : null;
					}
					if (date) {
						valueAccessor()(date.getTime()/1000);
					} else {
						valueAccessor()(null);
					}
				}
				oldVal = newVal;
			}
		}, 100);
    },
    update: function (element, valueAccessor, allBindingsAccessor, viewModel) {
        var value = ko.utils.unwrapObservable(valueAccessor()),
			options = allBindingsAccessor().datetimepickerOptions || { pickDate: true, pickTime: true },
			$date_div = $(element).find("input[data-format='dd/MM/yyyy']").parent(),
			$time_div = $(element).find("input[data-format='hh:mm']").parent();
		if (value) {
			if (isNaN(value)) {
			options.pickTime != $time_div.datetimepicker('setValue', value);
			} else {
				var datetime = AW.util.formatTimestamp(value, "/");
				options.pickDate != false && $date_div.datetimepicker('setValue', datetime.substr(0, 10));
				options.pickTime != false && $time_div.datetimepicker('setValue', datetime.substr(11, 5));
			}
		} else {
			options.pickDate != false && $date_div.datetimepicker('setValue', null);
			if (options.pickTime != false) {
				console.log("Huh?");
				$time_div.val('');
				$time_div.change();
			}
		}
    }
};

ko.bindingHandlers.priceComponents = {
	init: function(element, valueAccessor, allBindingsAccessor, viewModel, bindingContext) {
		(function($) {
			$.fn.toggleDisabled = function() {
				return this.each(function() {
					var $this = $(this);
					if ($this.attr('disabled')) $this.removeAttr('disabled');
					else $this.attr('disabled', 'disabled');
				});
			};
		})(jQuery);
		var price_components = valueAccessor()();
		for (var i in price_components) {
			var price_component = price_components[i];
			var price_element = $("<div class=\"input-prepend input-append\"><a href=\"javascript:void(0)\" class=\"btn\">" + price_component.name() + "</a><input type=\"text\" class=\"input-mini\" style=\"text-align: right\" data-bind=\"value: price_components()[" + price_component.id() + "].value, valueUpdate: 'keyup'\" /><span class=\"add-on\"></span></div>");
			if (price_component.applied()) {
				price_element.children("a").addClass("btn-success");
			} else {
				price_element.children("input").attr("disabled", "disabled");
			}
			price_element.children("span").html(price_component.is_ratio() ? "%" : "&euro;");
			$(element).append(price_element);
			(function(_price_component){
				price_element.children("a").click(function(){
					if ($(this).hasClass("btn-success")) {
						_price_component.applied(false);
						$(this).removeClass("btn-success");
						$(this).siblings("input").attr("disabled", true);
					} else {
						_price_component.applied(true);
						$(this).addClass("btn-success");
						$(this).siblings("input").attr("disabled", false);
					}
				});
			})(price_component);
			price_element.css("margin-right", "10px");
		}
    },
    update: function(element, valueAccessor, allBindingsAccessor, viewModel, bindingContext) {
    }
};

ko.bindingHandlers.id = {
    init: function (element, valueAccessor, allBindingsAccessor, viewModel) {
    },
    update: function (element, valueAccessor, allBindingsAccessor, viewModel) {
        $(element).attr('id', valueAccessor());
    }
};

ko.bindingHandlers.chooser = {
    init: function (element, valueAccessor, allBindingsAccessor, viewModel) {
		var settings = ko.utils.unwrapObservable(allBindingsAccessor().chooserSettings || {});
		var options = ko.utils.unwrapObservable(settings.options || allBindingsAccessor().chooserOptions || {});
		for (var i in options) {
			(function (value, caption) {
				var button = $('<button value="' + value + '" class="btn btn-mini" style="margin-right: 5px;">' + caption + '</button>');
				button.on("click", function (event) {
					if (settings.multiple) {
						$(this).toggleClass("btn-primary");
						var values = (valueAccessor()() || []).filter(function(_) { return _ != value; });
						if ($(this).hasClass("btn-primary")) { values.push(value); }
						valueAccessor()(values);
					} else {
						$(this).siblings().removeClass("btn-primary");
						$(this).addClass("btn-primary");
						valueAccessor()(value);
					}
				});
				$(element).append(button);
			})(i, options[i]);
		}
    },
    update: function (element, valueAccessor, allBindingsAccessor, viewModel) {
		var settings = ko.utils.unwrapObservable(allBindingsAccessor().chooserSettings || {});
		var value = ko.utils.unwrapObservable(valueAccessor());
		$(element).children().removeClass("btn-primary");
        $(element).children().each(function () {
			if (settings.multiple && $.inArray($(this).val(), value) != -1 || $(this).val() == value) {
				$(this).addClass("btn-primary");
			}
		});
    }
};

ko.bindingHandlers.treeview = {
    init: function (element, valueAccessor, allBindingsAccessor, viewModel) {
    },
    update: function (element, valueAccessor, allBindingsAccessor, viewModel) {
		var options = ko.utils.unwrapObservable(allBindingsAccessor().treeviewOptions || {});
		var id = "#" + element.id;
		function createTreeview (children) {
			YUI().use(
				'aui-tree-view',
				function(Y) {
					var treeType = Boolean(options.draggable) ? Y.TreeViewDD : Y.TreeView;
					var tree = new treeType({
						boundingBox: id,
						children: children
					}).render();
					return;
					$(id).on("click", "i.aui-icon-minus", function (event) {
						var node_id = $(this).siblings("span").find("a").data("node-id");
						$.cookie("alloyui-treeview-" + id + "-" + node_id, "true");
					});
					$(id).on("click", "i.aui-icon-plus", function (event) {
						var node_id = $(this).siblings("span").find("a").data("node-id");
						$.cookie("alloyui-treeview-" + id + "-" + node_id, $(this).siblings("i.aui-icon-refresh").size() > 0 ? "true" : null);
					});
				}
			);
		}
		if (options.io) {
			$.ajax({
				url: options.io,
				dataType: "json",
				success: function (children) {
					createTreeview(children);
				}
			});
		} else {
			// FIXME: Get rid of this! Something super strange is happening if ko.unwrap(valueAccessor()) is used directly.
			// The following error occurs: "An attempt was made to use an object that is not, or is no longer, usable."
			function deepCopy (old) {
				var _new = [];
				for (var i in old) {
					_new.push({
						id: old[i].id,
						label: old[i].label,
						children: deepCopy(old[i].children),
						expanded: Boolean(old[i].expanded)
					});
				}
				return _new;
			}
			var children = deepCopy(ko.unwrap(valueAccessor()));
			createTreeview(children);
		}
    }
};

ko.bindingHandlers.fileupload = {
	init: function (element, valueAccessor, allBindingsAccessor, viewModel) {
		var options = valueAccessor();
		var $element = $(element).append("<input type='file' name='files[]' multiple>");
		var mapping = {};
		var doneCount = 0;
		$element.fileupload({
			url: options.url,
			dataType: 'json',
			maxFileSize: options.maxFileSize ? options.maxFileSize : 5000000,
			autoUpload: true,
			acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
			add: function (e, data) {
				$.each(data.files, function (i, file) {
					var reader = new FileReader();
					reader.onload = function (e) {
						var fileData = { name: file.name, url: e.target.result, inProgress: true, size: file.size, created: AW.util.time() };
						if (options.properties) {
							fileData = $.extend(fileData, options.properties);
						}
						mapping[file.name] = options.addHandler(fileData);
					}
					reader.readAsDataURL(file);
				});
				data.process().done(function () {
					var x = data.submit();
				});
			},
			progress: function (e, data) {
				var progress = parseInt(data.loaded / data.total * 100, 10);
				mapping[data.files[0].name].progress(progress);
				mapping[data.files[0].name].jqXHR = data;
			},
			done: function (e, data) {
				$.each(data.files, function (index, file) {
					mapping[file.name].file(data.result.files[file.name].file);
					mapping[file.name].inProgress(false);
					mapping[file.name].jqXHR = false;
					delete mapping[file.name];
					doneCount++;
				});
				if (AW.util.dictSize(mapping) === 0) {
					AW.UI.modal.alert(doneCount + " pilt" + (doneCount === 1 ? "" : "i") + " edukalt üles laetud!", "alert-success");
					doneCount = 0;
				}
			}
		}).prop('disabled', !$.support.fileInput).parent().addClass($.support.fileInput ? undefined : 'disabled');
	},
	update: function (element, valueAccessor, allBindingsAccessor, viewModel) {
	}
};