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
