
/*
 *	This file must only be used from inside crm_offer::callback_generate_scripts().
 *	It assumes you have properly set up the variable OCCD in the following way:
 *	
 *	var awCrmOffer = {
 *		"rows": {
 *			[crm_offer_row_oid]: {
 *				"oid": [crm_offer_row_oid],
 *				"price_components": [[price_component_oid]]		//	MUST BE ORDERED!!!
 *			}
 *		},
 *		"price_components_for_total": [[price_component_oid]],	//	MUST BE ORDERED!!!
 *		"price_components": {
 *			[price_component_oid]: {
 *				"oid": [price_component_oid],
 *				"type": [price_component_type],
 *				"is_ratio": [price_component_is_ratio],
 *				"prerequisites": {[price_component_oid]}
 *			}
 *		},
 *		//	WILL BE ADDED DYNAMICALLY, HERE JUST FOR TO SHOW THE STRUCTURE!
 *		"sums": {
 *			[row_id]: {
 *				[jrk]: {
 *					price_component: [price_component_oid],
 *					sum: [sum]
 *				}
 *			}
 *		}
 *	};
 *
 *	Types are given as integers as follows:
 *
 *	TYPE_NET_VALUE = 1;
 *	TYPE_UNIT = 2;
 *	TYPE_ROW = 3;
 *	TYPE_TOTAL = 4;
 *
 *	-kaarel 3.06.2010
 */

awCrmOffer.sums = {};
awCrmOffer.findLatestApplicableSum = function(row_id, price_component){
	for (var i = awCrmOffer.sums[row_id].length -1; i >= 0; i--)
	{
		for (var j = 0; j < price_component.prerequisites.length; j++)
		{
			if(price_component.prerequisites[j] == awCrmOffer.sums[row_id][i].price_component)
			{
				return awCrmOffer.sums[row_id][i].sum;
			}
		}
	}

	if ("total" == row_id){
		return $("input[type=hidden][name$='][price]']").sum();
	}
	else {
		return 0;
	}
}

awCrmOffer.calculateTotalPrice = function(){
	$("#content_total_price_components_total_price").html(
		Number(
			$("#content_total_price_components_total_price_").val(
				$("input[type=hidden][name$='][price_change]']").sum()
			).val()
		).toFixed(2)
	);
}

awCrmOffer.calculateRow = function(row_id){
	var total = 0;
	awCrmOffer.sums[row_id] = new Array();
	if ("total" == row_id){
		var price_components = awCrmOffer.price_components_for_total;
	}
	else {
		var price_components = awCrmOffer.rows[row_id].price_components;
	}
	for (var i = 0; i < price_components.length; i++){
		var price_component_id = price_components[i];
		var price_component = awCrmOffer.price_components[price_component_id];
		if ($("#content_table_"+row_id+"__price_component__"+price_component_id+"__apply_").attr("type") != "hidden" && !$("#content_table_"+row_id+"__price_component__"+price_component_id+"__apply_").attr("checked")){
			var formula = "0";
		}
		else if (1 == price_component.type || 2 == price_component.type){	//	TYPE_UNIT
			var formula = "amount * value";
		}
		else if (3 == price_component.type || 4 == price_component.type){	//	TYPE_ROW or TYPE_TOTAL
			var formula = "value";
		}
		var sum = awCrmOffer.findLatestApplicableSum(row_id, price_component);
		if (price_component.is_ratio){
			formula += "* sum / 100";
		}
		$("#content_table_"+row_id+"_price_component_"+price_component_id+"_price_change").html(
			$("#content_table_"+row_id+"__price_component__"+price_component_id+"__price_change_").calc(
			formula,
			{
				"amount": $("#content_table_"+row_id+"__amount_").val(),
				"value": $("input[name='content_table["+row_id+"][price_component]["+price_component_id+"][value]']"),
				"sum": sum
			},
			function (s){
				s = s.toFixed(2);
				total = total + (s.valueOf() - 0);
				awCrmOffer.sums[row_id][awCrmOffer.sums[row_id].length] = {
					"price_component": price_component_id,
					"sum": Number(sum) + Number(s)
				};
				var sign = "";
				if(s >= 0){
					sign = "+";
				}
				return sign + s.toLocaleString();
			}
		).val());
	}
	$("#content_table_"+row_id+"_price").html($("#content_table_"+row_id+"__price_").val(Number(total).toFixed(2)).val());
}

$(document).ready(function(){
	$.each(awCrmOffer.rows, function(row_id, row){
		awCrmOffer.calculateRow(row_id);
	});
	awCrmOffer.calculateRow("total");
	awCrmOffer.calculateTotalPrice();

	$("input[name$='][amount]']").keyup(function(){
		awCrmOffer.calculateRow(this.name.replace(/[^0-9]/gi, ""));
		awCrmOffer.calculateRow("total");
		awCrmOffer.calculateTotalPrice();
	});
	$("input[name$='][apply]']").click(function(){
		var row_id = this.name.substr(10, 20).replace(/[^0-9]/gi, "");
		if ("" != row_id){
			awCrmOffer.calculateRow(row_id);
		}
		awCrmOffer.calculateRow("total");
		awCrmOffer.calculateTotalPrice();
	});
});
