var get_property_data = $.gpnv_as_obj();

(function(){

	var ajax_requests = { property: {}, layout: {} };

	window.reload_property = function(props, params, no_ajax_loader) {
		insert_params(params, params);

		if(typeof props != "object") {
			props = [props];
		}

		for(var i = 0; i < props.length; i++) {
			property = props[i];
			(function(prop) {
				$("div[name='"+prop+"']").each(function(){
					var div = $(this);
					if(typeof no_ajax_loader == "undefined" || no_ajax_loader != true)
					{
						$.please_wait_window.show({
							"target": div
						});
					}
					if (ajax_requests.property[prop] != undefined) {
						ajax_requests.property[prop].abort();
					}
					ajax_requests.property[prop] = $.ajax({
						url: "orb.aw",
						data: $.extend({view_property: prop}, get_property_data),
						success: function(html){
							div.html(html);
							if(typeof no_ajax_loader == "undefined" || no_ajax_loader != true)
							{
								$.please_wait_window.hide();
							}
						}
					});
				});
			})(property);
		}
	}

	window.reload_layout = function(layouts, params, no_ajax_loader) {
		insert_params(params, params);

		if(typeof layouts != "object") {
			layouts = [layouts];
		}

		for(var i = 0; i < layouts.length; i++) {
			layout_ = layouts[i];
			(function(layout) {
				$("div[id='"+layout_+"_outer']").each(function(){
					div = $(this);
					if(typeof no_ajax_loader == "undefined" || no_ajax_loader != true)
					{
						$.please_wait_window.show({
							"target": div
						});
					}
					if (ajax_requests.layout[layout] != undefined) {
						ajax_requests.layout[layout].abort();
					}
					ajax_requests.layout[layout] = $.ajax({
						div: div,
						url: "orb.aw",
						data: $.extend({view_layout: layout}, get_property_data),
						success: function(html){
							this.div.html($(html).html());
							if(typeof no_ajax_loader == "undefined" || no_ajax_loader != true)
							{
								$.please_wait_window.hide();
							}
						}
					});
				});
			})(layout_);
		}
	}

	function insert_params(params, hidden_params) {
		var post_ru = $("input[type='hidden'][name='post_ru']");
		var return_url = $("input[type='hidden'][name='return_url']");
		if(typeof params == "object") {
			for(var i in params) {
				get_property_data[i] = params[i];
				post_ru.val($.sup(i, params[i], post_ru.val()));
				return_url.val($.sup(i, params[i], return_url.val()));
				if(typeof hidden_params != "undefined" && typeof hidden_params[i] != "undefined") {
					var hidden = $("input[type='hidden'][name='"+i+"']");
					if(hidden.size() == 0)
					{
						hidden_el = document.createElement('input');
						hidden = $(hidden_el);
						hidden.attr({
							"name": i,
							"type": "hidden"
						});
						$("span[id='reforb']").append(hidden);
					}
					hidden.val(hidden_params[i]);
				}
			}
		}
	}
})();