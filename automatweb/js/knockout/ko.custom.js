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
		var options = ko.utils.unwrapObservable(allBindingsAccessor().chooserOptions || {});
		for (var i in options) {
			(function (value, caption) {
				var button = $('<button value="' + value + '" class="btn btn-mini" style="margin-right: 5px;">' + caption + '</button>');
				button.on("click", function (event) {
					$(this).siblings().removeClass("btn-primary");
					$(this).addClass("btn-primary");
					valueAccessor()(value);
				});
				$(element).append(button);
			})(i, options[i]);
		}
    },
    update: function (element, valueAccessor, allBindingsAccessor, viewModel) {
		var value = ko.utils.unwrapObservable(valueAccessor());
		$(element).children().removeClass("btn-primary");
        $(element).children().each(function () {
			if ($(this).val() == value) {
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
		$.ajax({
			url: options.io,
			dataType: "json",
			success: function (children) {	
				YUI().use(
					'aui-tree-view',
					function(Y) {
						// Create a TreeView Component
						var tree = new Y.TreeView({
							boundingBox: id,
							children: children,
						}).render();
						console.log(tree);
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
		});
    }
};

ko.bindingHandlers.fileupload = {
	init: function (element, valueAccessor, allBindingsAccessor, viewModel) {
		var options = valueAccessor();
		var $element = $(element).append("<input type='file' name='files[]' multiple>");
		$element.fileupload({
			url: options.url,
			dataType: 'json',
			maxFileSize: options.maxFileSize ? options.maxFileSize : 5000000,
			autoUpload: true,
			acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
			add: function (e, data) {
				var reader = new FileReader();
				reader.onload = function (e) {
					options.addHandler && options.addHandler({ name: data.files[0].name, url: e.target.result });
				}
				reader.readAsDataURL(data.files[0]);
		//		uploadButton.clone(true).data(data).click();
			},
			done: function (e, data) {
				return;
				$.each(data.result.files, function (index, file) {
					console.log(index, file);
				});
			},
			progressall: function (e, data) {
				return;
				var progress = parseInt(data.loaded / data.total * 100, 10);
				$('#progress .progress-bar').css(
					'width',
					progress + '%'
				);
			}
		}).prop('disabled', !$.support.fileInput).parent().addClass($.support.fileInput ? undefined : 'disabled');
	},
	update: function (element, valueAccessor, allBindingsAccessor, viewModel) {
	}
};