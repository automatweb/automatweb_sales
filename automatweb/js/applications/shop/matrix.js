var shop_matrix = {
	current: {row: 0, col: 0, currency: 0},
	initialize: function(){
		shop_matrix.current.currency = $('input[type=hidden][name=currency]').val();
		shop_matrix.form = $(document.createElement("form"));
		shop_matrix.div = $(document.createElement("div"))
			.attr("id", "matrixAdvancedLayer")
			.hide()
			.append(shop_matrix.form);
		$(document.body).append(shop_matrix.div);
	},
	open_layer: function(row, col){
		if(typeof(get_property_data) != "undefined")
		{
			get_property_data.group = "advanced_layer";
		}
		shop_matrix.current.row = row;
		shop_matrix.current.col = col;
		$.please_wait_window.show();
		$.ajax({
			url: "orb.aw",
			data: {
				"class": $.gup("class"),
				"action": "change",
				"id": $.gup("id"),
				"currency": shop_matrix.current.currency,
				"row": row,
				"col": col,
				"group": "advanced_layer",
				"view_layout": "advanced_layer"
			},
			success: function(html){
				shop_matrix.form.html(html);
				$.please_wait_window.hide();
				shop_matrix.div
					.css("top", ($(window).height()-shop_matrix.div.height())/2+"px")
					.css("left", ($(window).width()-shop_matrix.div.width())/2+"px")
					.css("display", "block");
			}
		});
	},
	close_layer: function(){
		$.get('orb.aw', {
				"class": $.gup("class"),
				"action": "cell_description",
				"rent_configuration": $.gup("id"),
				"currency": shop_matrix.current.currency,
				"row": shop_matrix.current.row,
				"col": shop_matrix.current.col,
			}, function(description){
			shop_matrix.update_description(description);
			shop_matrix.div.css("display", "none");
			shop_matrix.current.row = 0;
			shop_matrix.current.col = 0;
		});
	},
	submit_layer: function(){
		$.post("orb.aw", "class="+$.gup("class")+"&action=submit_advanced_layer&"+shop_matrix.form.serialize()+"&currency="+shop_matrix.current.currency+"&id="+$("input[type='hidden'][name='condition']").val(), function(condition){
			if($('input[type=hidden][name=delete_conditions]').val() == '1'){
				reload_layout(['advanced_layer_left','advanced_layer_right'],{'row':shop_matrix.current.row,'col':shop_matrix.current.col,'currency':shop_matrix.current.currency});
				$('input[type=hidden][name=delete_conditions]').val('');
			}
			else{
				reload_layout(['advanced_layer_left','advanced_layer_right'],{'row':shop_matrix.current.row,'col':shop_matrix.current.col,'currency':shop_matrix.current.currency,'condition':condition});
			}
		});
	},
	update_description: function(description){
		$("a[name='"+shop_matrix.current.row+"_"+shop_matrix.current.col+"']").html(description);
	}
};
$(document).ready(function(){
	shop_matrix.initialize();
});