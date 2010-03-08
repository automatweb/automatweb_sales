$(document).ready(function(){
	$("#sum_core").change(function(){
		var select = $("#rent_period").get(0);
		$.getJSON("orb.aw", {
			"class": "shop_rent_calculator",
			"action": "get_rent_periods",
			"id": $("input[type=hidden][name=id]").val(),
			"format": "json",
			"sum": $(this).val()
		}, function(options){
			aw_clear_list(select);
			var cnt = 0;
			for(i in options){
				aw_add_list_el(select, i, options[i]);
				cnt++;
			}
			if(cnt == 0)
			{
				alert("Valitud summa jaoks ei leidu j&auml;relmaksuperioode!");
			}
		});
	});
});