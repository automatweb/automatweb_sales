$(document).ready(function(){
	function fnStringToFunction (fnName) {
		var namespaces = fnName.split("."),
			method = namespaces.pop(),
			context = window;
		for(var i = 0; i < namespaces.length; i++) {
			if (typeof context[namespaces[i]] !== "undefined") {
				context = context[namespaces[i]];
			}
		}
		return context[method];
	}

	var configuration = {VAR:configuration};
	/* Hack to convert function names gives as string to actual functions: */
	for (i in configuration.aoColumns) {
		oColumn = configuration.aoColumns[i];
		if (typeof oColumn.fnRender !== "undefined") {
			oColumn.fnRender = fnStringToFunction(oColumn.fnRender);
		}
	}
	var {VAR:id} = $("#{VAR:id}").dataTable(configuration);
<!-- SUB: DATA -->
<!-- SUB: DATA.JSON -->
	{VAR:id}.fnAddData({VAR:data.json});
<!-- END SUB: DATA.JSON -->
<!-- END SUB: DATA -->
<!-- SUB: EDITABLE -->
	var editable_configuration = {VAR:editable.configuration};
	/* Hack to convert function names gives as string to actual functions: */
	for (i in editable_configuration.aoColumns) {
		oColumn = editable_configuration.aoColumns[i];
		if (oColumn !== null && typeof oColumn.onedit !== "undefined") {
			oColumn.onedit = fnStringToFunction(oColumn.onedit);
		}
		if (oColumn !== null && typeof oColumn.onsubmit !== "undefined") {
			oColumn.onsubmit = fnStringToFunction(oColumn.onsubmit);
		}
	}
	{VAR:id}.dataTable().makeEditable(editable_configuration);
<!-- END SUB: EDITABLE -->
<!-- SUB: VERTICAL_GROUPING -->
	{VAR:id}.dataTable().fnMultiRowspan({VAR:fnMultiRowspan.aSpannedColumns}, {VAR:fnMultiRowspan.bCaseSensitive});
<!-- END SUB: VERTICAL_GROUPING -->
});
