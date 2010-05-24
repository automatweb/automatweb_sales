
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
 *		}
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
 *			
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
 *	-kaarel 6.05.2010
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
	return 0;
}

awCrmOffer.calculateRow = function(row_id){
	var total = 0;
	awCrmOffer.sums[row_id] = new Array();
	row = awCrmOffer.rows[row_id];
	for (var i = 0; i < row.price_components.length; i++){
		var price_component_id = row.price_components[i];
		var price_component = awCrmOffer.price_components[price_component_id];
		if ($("#content_table_"+row_id+"__price_component__"+price_component_id+"__apply_").attr("type") != "hidden" && !$("#content_table_"+row_id+"__price_component__"+price_component_id+"__apply_").attr("checked")){
			var formula = "0";
		}
		else if (2 == price_component.type){	//	TYPE_UNIT
			var formula = "amount * value";
		}
		else if (3 == price_component.type){	//	TYPE_ROW
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
	$("input[name$='][amount]']").keyup(function(){
		awCrmOffer.calculateRow(this.name.replace(/[^0-9]/gi, ""));
	});
	$("input[name$='][apply]']").click(function(){
		awCrmOffer.calculateRow(this.name.substr(10, 20).replace(/[^0-9]/gi, ""));
	});
});
