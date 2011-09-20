$(document).ready(function () {
	function __eval (o) {
		if ("string" === typeof o && "js:" === o.substr(0, 3)) {
			o = eval(o.substr(3));
		}
		else if ("object" === typeof o) {
			$.each(o, function(k, v){
				o[k] = __eval(v);
			});
		}
		return o;
	}
	var {VAR:id} = $("#{VAR:id}"),
		{VAR:id}_configuration = {VAR:configuration};
<!-- SUB: SUBGRID -->
<!-- END SUB: SUBGRID -->
	{VAR:id}_configuration = __eval({VAR:id}_configuration);
	{VAR:id}.jqGrid({VAR:id}_configuration);
<!-- SUB: DATA -->
<!-- SUB: DATA.JSON -->
	var data = __eval({VAR:data.json}),
		i = 0;
	$.each(data, function (k, row) {
		{VAR:id}.jqGrid('addRowData', i++, row);
	});
<!-- END SUB: DATA.JSON -->
<!-- END SUB: DATA -->
});
