{VAR:parent}_configuration.subGrid = true;
{VAR:parent}_configuration.subGridRowExpanded = function (subgrid_id, row_id) {
	var table_id = subgrid_id + "_subgrid_" + row_id,
		{VAR:parent}_subgrid = $("<table id='" + table_id + "' name='{VAR:name}_" + row_id + "'></table>"),
		subdata = data[row_id].{VAR:data_index},
		i = 0;
	$("#" + subgrid_id).append({VAR:parent}_subgrid);
	{VAR:id}_configuration = __eval({VAR:configuration});
	{VAR:id} = {VAR:parent}_subgrid.jqGrid({VAR:id}_configuration);
	$.each(subdata, function (k, row) {
		{VAR:parent}_subgrid.jqGrid('addRowData', i++, row);
	});
};
