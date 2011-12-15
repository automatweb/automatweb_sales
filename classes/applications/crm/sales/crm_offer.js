
/*
 *	This file must only be used from inside crm_offer::callback_generate_scripts().
 *	It assumes you have properly set up the variable OCCD in the following way:
 *	
 *	var AW_crm_offer = {
 *		"rows": {
 *			[crm_offer_row_oid]: {
 *				"id": [crm_offer_row_oid],
 *				"price_components": [[price_component_oid]]		//	MUST BE ORDERED!!!
 *			}
 *		},
 *		"price_components_for_total": [[price_component_oid]],	//	MUST BE ORDERED!!!
 *		"price_components": {
 *			[price_component_oid]: {
 *				"id": [price_component_oid],
 *				"type": [price_component_type],
 *				"is_ratio": [price_component_is_ratio],
 *				"prerequisites": {[price_component_oid]}
 *			}
 *		},
 *		//	WILL BE ADDED DYNAMICALLY, HERE JUST TO SHOW THE STRUCTURE:
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

console.log(AW_crm_offer);

$.extend(AW_crm_offer, {
	"__modified": false,
	"sums": {},
	"fn": {
		"get": function (key) {
			return $.jgrid.getAccessor(AW_crm_offer, key);
		},
		"set": function (key, value) {
			if (AW_crm_offer.fn.get(key) !== value) {
				if (key.indexOf(".") !== -1) {
					var _key = key.substr(0, key.lastIndexOf(".")),
						_obj = AW_crm_offer.fn.get(_key);
					_obj[key.substr(key.lastIndexOf(".") + 1)] = value;
				} else {
					this[key] = value;
				}
				AW_crm_offer.__modified = true;
			}
		},
		"fnPreEdit": function (iRowID, sCellName, sValue, iRow, iCol) {
			if ("unit_name" === sCellName) {
				var select = $(this).children("tbody").children("tr:eq(" + iRow + ")").children("td:eq(" + iCol + ")").children("select"),
					data = $(this).jqGrid("getLocalRow", iRowID),
					options = AW_crm_offer.rows[data["id"]].unit.options;
				select.children().remove();
				for (i in options) {
					select.append(new Option(options[i], i));
				}
				select.children("option[value=" + AW_crm_offer.rows[data["id"]].unit.id + "]").attr("selected", true);
			}
		},
		"fnPostEdit": function (iRowID, sCellName, sValue, iRow, iCol) {
			var id = $(this).jqGrid("getLocalRow", iRowID).id;
			switch (sCellName) {
				case "unit_name":
					AW_crm_offer.fn.set("rows." + id + ".unit.id", sValue);
					AW_crm_offer.fn.set("rows." + id + ".unit.name", AW_crm_offer.rows[id].unit.options[sValue]);
					break;

				case "name":
				case "comment":
				case "amount":
					AW_crm_offer.fn.set("rows." + id + "." + sCellName, sValue);
					break;

				case "customer_id":
				case "responsible_company_id":
				case "responsible_section_id":
				case "responsible_person_id":
					var input = sValue.split("::");
						key = sCellName.substr(0, sCellName.length - 3);
					AW_crm_offer.fn.set("rows." + id + "." + key + ".id", input[0]);
					AW_crm_offer.fn.set("rows." + id + "." + key + ".name", input.splice(1).join("::"));
			};
			AW_crm_offer.fn.calculateRow(id, true);
		},
		"fnPreEditPriceComponent": function (iRowID, sCellName, sValue, iRow, iCol) {
		},
		"fnPostEditPriceComponent": function (iRowID, sCellName, sValue, iRow, iCol) {
			var pID = $(this).jqGrid("getLocalRow", iRowID).id,
				rID = $(this).jqGrid("getLocalRow", iRowID).row_id;
			switch (sCellName) {
				case "value":
					AW_crm_offer.fn.set("rows." + rID + ".price_components." + pID + ".value", sValue);
					AW_crm_offer.fn.calculateRow(rID, true);
					break;

				case "apply":
					AW_crm_offer.fn.set("rows." + rID + ".price_components." + pID + ".apply", sValue !== false && sValue !== "false");
					AW_crm_offer.fn.calculateRow(rID, true);
					break;
			};
		},
		"findLatestApplicableSum": function (row_id, price_component) {
			if (typeof AW_crm_offer.sums[row_id] === "object") {
				for (var i = AW_crm_offer.sums[row_id].length -1; i >= 0; i--)
				{
					for (var j = 0; j < price_component.prerequisites.length; j++)
					{
						if(price_component.prerequisites[j] == AW_crm_offer.sums[row_id][i].price_component)
						{
							return AW_crm_offer.sums[row_id][i].sum;
						}
					}
				}
			}

			if ("total" == row_id){
				return $("input[type=hidden][name$='][price]']").sum();
			}
			else {
				return 0;
			}
		},
		"calculateTotalPrice": function(){
			var total = 0;
			$.each(AW_crm_offer.rows, function(i, row){
				total += row.price;
			});
			AW_crm_offer.fn.set("sum", total);
			/*
			$("#content_total_price_components_total_price").html(
				Number(
					$("#content_total_price_components_total_price_").val(
						$("input[type=hidden][name$='][price_change]']").sum()
					).val()
				).toFixed(2)
			);
			*/
		},
		"calculateRow": function (row_id, calculate_total) {
			var total = 0;
			AW_crm_offer.fn.set("sums." + row_id, []);
			if ("total" == row_id){
				var price_components = AW_crm_offer.price_components_for_total;
			}
			else {
				var price_components = AW_crm_offer.rows[row_id].price_components;
			}
			for (var i = 0; i < price_components.length; i++){
				var id = price_components[i].id,
					price_component = AW_crm_offer.price_components[id],
					sum = AW_crm_offer.fn.findLatestApplicableSum(row_id, price_component),
					formula = "0";
				if (AW_crm_offer.rows[row_id].price_components[i].apply) {
					if (1 == price_component.type || 2 == price_component.type){	//	TYPE_UNIT
						formula = "amount * value";
					}
					else if (3 == price_component.type || 4 == price_component.type){	//	TYPE_ROW or TYPE_TOTAL
						formula = "value";
					}
					if (price_component.is_ratio){
						formula += " * sum / 100";
					}
				}
				AW_crm_offer.fn.set("rows." + row_id + ".price_components." + i + ".price_change", $("<input>").calc(formula, {
						"amount": AW_crm_offer.rows[row_id].amount,
						"value": AW_crm_offer.rows[row_id].price_components[i].value,
						"sum": sum
					},
					function (s){
						s = s.toFixed(2);
						total = total + (s.valueOf() - 0);
						AW_crm_offer.sums[row_id].push({
							"price_component": id,
							"sum": Number(sum) + Number(s)
						});
						var sign = (s > 0) ? "+" : "";
						return sign + s.toLocaleString();
					}
				).val());
				$("table[name=price_components_" + row_id + "]").jqGrid("setCell", i, "price_change", AW_crm_offer.rows[row_id].price_components[i].price_change);
			}

			if (row_id !== "total") {
				AW_crm_offer.fn.set("rows." + row_id + ".price", Number(total).toFixed(2));
				$("table[name=content_table]").jqGrid("setCell", row_id, "price", AW_crm_offer.rows[row_id].price);
			}

			if (calculate_total) {
				AW_crm_offer.fn.calculateRow("total");
				AW_crm_offer.fn.calculateTotalPrice();
			}
		},
		"submit": function (display_please_wait_window) {
			if (AW_crm_offer.__modified && !AW_crm_offer.__saving_in_progress) {
				AW_crm_offer.__saving_in_progress = true;
				if (display_please_wait_window) {
					$.please_wait_window.show();
				}
				console.log("Saving...");
				(function (that) {
					$.ajax({
						"type": "POST",
						"url": "orb.aw?class=crm_offer&action=submit_content_table",
						"data": {
							"id": $("input[type=hidden][name=id]").val(),
							"data": $.extend({}, AW_crm_offer, {fn: null}),
							"charset": "utf8"
						},
						"success": function () {
							if (display_please_wait_window) {
								$.please_wait_window.hide();
							}
							console.log("Saved!");
							that.__saving_in_progress = false;
							that.__modified = false;
						}
					});
				})(AW_crm_offer);
			}
		}
	}
});

$(document).ready(function(){
	$.each(AW_crm_offer.rows, function(row_id, row){
		AW_crm_offer.fn.calculateRow(row_id);
	});
	AW_crm_offer.fn.calculateRow("total");
	AW_crm_offer.fn.calculateTotalPrice();

	$("input[name$='][amount]']").keyup(function(){
		AW_crm_offer.fn.calculateRow(this.name.replace(/[^0-9]/gi, ""));
		AW_crm_offer.fn.calculateRow("total");
		AW_crm_offer.fn.calculateTotalPrice();
	});
	$("input[name$='][apply]']").click(function(){
		var row_id = this.name.substr(10, 20).replace(/[^0-9]/gi, "");
		if ("" != row_id){
			AW_crm_offer.fn.calculateRow(row_id);
		}
		AW_crm_offer.fn.calculateRow("total");
		AW_crm_offer.fn.calculateTotalPrice();
	});
	function autosave () {
		AW_crm_offer.fn.submit();
		setTimeout(autosave, 50000);
	}
	setTimeout(autosave, 50000);
});
