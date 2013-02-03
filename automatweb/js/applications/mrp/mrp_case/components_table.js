(function($){
	if (typeof(AW) == "undefined") {
		window.AW = { UI: {} };
	}
	AW.UI.mrp_case = (function() {
		var id = $.gpnv_as_obj().id;
		var viewModel;
				
		function RowViewModel (row) {
			for (var key in row) {
				this[key] = ko.observable(row[key]);
			}
			this.unit = ko.observable({ id: row.unit.id, name: row.unit.name });
		}
		function OrderViewModel (rows, units) {
			this.availableUnits = [];
			this.rows = ko.observable({});
			for (var i in rows) {
				this.rows()[rows[i].id] = new RowViewModel(rows[i]);
			}
			var self = this;
			$.getJSON("/automatweb/orb.aw?class=mrp_case&action=get_units&id=" + id, function(units){
				for (var i in units) {
					self.availableUnits.push(units[i]);
				}
			});
		}
		function bindElementTo (td, index, property) {
			return td.attr("data-bind", "text: rows()[" + index + "]." + property);
		}
		function rowEditForm(nTr) {
			var aData = $('#example').dataTable().fnGetData(nTr);
			var search = '<a href="javascript:aw_popup_scroll(\'http://ehta.dev.automatweb.com/automatweb/orb.aw?class=popup_search&action=do_search&no_submit=1&pn=components_new&npn=components_new_name&id=' + id + '&in_popup=1&start_empty=1&jcb=window.opener.AW.UI.mrp_case.add_article('+ aData.id +')\',\'Otsing\',800,500)" alt="Otsi" title="Otsi"><img src="/automatweb/images/icons/magnifier.png" border="0"></a>'
			var html = '<table class="expandable">';
			html += '<tr><td class="caption">Pealkiri:</td><td><input type="text" data-bind="value: rows()[' + aData.id + '].name, valueUpdate:\'afterkeydown\'" /></td><td class="caption">Komponent:</td><td><span data-bind="text: rows()[' + aData.id + '].article_name"></span>' + search + '</td><td class="caption">Kogus:</td><td><input type="text" data-bind="value: rows()[' + aData.id + '].quantity" /></td></tr>';
			html += '<tr><td class="caption">Alapealkiri:</td><td colspan="3"><input type="text" data-bind="value: rows()[' + aData.id + '].title, valueUpdate:\'afterkeydown\'" /></td><td class="caption">Ühik:</td><td><select data-bind="options: availableUnits, optionsText: \'name\', value: rows()[' + aData.id + '].unit"></select></td></tr>';
			html += '<tr><td class="caption">Kokkuvõte:</td><td colspan="5"><textarea data-bind="value: rows()[' + aData.id + '].description, valueUpdate:\'afterkeydown\'"></textarea></td></tr>';
			html += '</table>';
			
			return html;
		}
		function applyBindings() {
			ko.applyBindings(viewModel);
		}
		function activateExpandableRows() {			
			var dataTable = $('#example').dataTable();
			$(".dataTables-expand").live('click', function(){
				var nTr = $(this).parents('tr')[0];
				if (dataTable.fnIsOpen(nTr)) {
					/* This row is already open - close it */
					this.src = "../examples_support/details_open.png";
					dataTable.fnClose(nTr);
					// TODO: Should save here!
				} else {
					/* Open this row */
					this.src = "../examples_support/details_close.png";
					dataTable.fnOpen(nTr, rowEditForm(nTr), "align-left");
					applyBindings();
				}
			});
		}
		return {
			initialize_components: function() {
				$.getJSON("/automatweb/orb.aw?class=mrp_case&action=get_components&id=" + id, function(components){
					viewModel = new OrderViewModel(components);
					var dataTable = $('#example').dataTable( {
						"aaData": components,
						"iDisplayLength": 50,
						"bAutoWidth": false,
						"aoColumns": [
							{ "sTitle": "", "mData": null, "sClass": "dataTables-expand", "sWidth": "4%" },
							{ "sTitle": "Nimetus", "mData": null, "sClass": "align-left", "sWidth": "24%" },
							{ "sTitle": "Artikkel", "mData": null, "sClass": "align-left", "sWidth": "24%" },
							{ "sTitle": "", "mData": null, "sClass": "align-right", "sWidth": "24%" },
							{ "sTitle": "Katted", "mData": null, "sClass": "align-left", "sWidth": "24%" }
						],
						"fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
							// attr("data-bind", "text: rows()[" + iDisplayIndexFull + "].formattedDescription")
							$("td:eq(1)", nRow).html('')
											   .append(bindElementTo($("<b></b>"), aData.id, "name")).append($("<br />"))
											   .append(bindElementTo($("<i></i>"), aData.id, "title")).append($("<br />"))
											   .append(bindElementTo($("<span></span>"), aData.id, "description"));
							$("td:eq(2)", nRow).html('').append(bindElementTo($("<span></span>"), aData.id, "article_name"));
							$("td:eq(3)", nRow).html('').append(bindElementTo($("<span></span>"), aData.id, "unit().name"));
						},
						"bFilter": false,
						"bSort": false,
						"fnDrawCallback": function() {
							applyBindings();
						}
					} );
					activateExpandableRows();
				});
			},
			add_component: function() {
				var dataTable = $("#example").dataTable();
				var new_component = { id: -dataTable.fnGetData().length, name: "Uus komponent", title: "", description: "", unit: { id: 0, name: "" } };
				viewModel.rows()[new_component.id] = new RowViewModel(new_component);
				dataTable.fnAddData(new_component);
				applyBindings();
				$.getJSON("/automatweb/orb.aw?class=mrp_case&action=add_component&id=" + id, function(component) {
					viewModel.rows()[new_component.id].id(component.id);
				});
			},
			update_components: function() {
				var data = { components: ko.toJS(viewModel).rows };
				for (var i in data.components) {
					data.components[i].unit = data.components[i].unit.id;
				}
				$.post("/automatweb/orb.aw?class=mrp_case&action=update_components&id=" + id, data);
			},
			add_article: function(id) {
				var article = $("input[name=components_new]").val();
				viewModel.rows()[id].article(article);
				var article_name = $("input[name=components_new_name]").val();
				viewModel.rows()[id].article_name(article_name);
			}
		};
	})();
	$(document).ready(function(){
		AW.UI.mrp_case.initialize_components();
	});
})(jQuery);