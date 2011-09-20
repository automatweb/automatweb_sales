$.fn.dataTableExt.oApi.fnMultiRowspan = function ( oSettings, oSpannedColumns, bCaseSensitive ) {
    /*
     * Type:        Plugin for DataTables (http://datatables.net) JQuery plugin.
     * Name:        dataTableExt.oApi.fnMultiRowspan
     * Requires:    DataTables 1.6.0+
     * Version:     0.7
     * Description: Creates rowspan cells in one or more columns when there are two or more cells in a row with the same
     *              content.
     *
     * Inputs:      object:oSettings - dataTables settings object
     *              object:oSpannedColumns - the columns to fake rowspans in
     *              boolean:bCaseSensitive - whether the comparison is case-sensitive or not (default: false)
     * Returns:     JQuery
     * Usage:       $('#example').dataTable().fnMultiRowspan([0]);
     *              $('#example').dataTable().fnMultiRowspan({0: 0, 1: 0, 2: 0}, true);
     *              $('#example').dataTable().fnMultiRowspan({"engine.name": "engine.name", "grade": "grade"});
     *
     * Author:      Kaarel Nummert
     * Comment:     Based on the fnFakeRowspan (http://datatables.net/plug-ins/api#fnFakeRowspan) plug-in created by Fredrik Wendel.
     * Created:     2011-09-02
     * Language:    Javascript
     * License:     GPL v2 or BSD 3 point style
     */
    var oSettings = oSettings,
        oSpannedColumns = oSpannedColumns,
        bCaseSensitive = (typeof(bCaseSensitive) != 'boolean' ? false : bCaseSensitive);

    oSettings.aoDrawCallback.push({ "fn": fnMultiRowspan, "sName": "fnMultiRowspan" });

    function fnMultiRowspan () {
        /* Reset rowspans. Should probably check if any of those columns are meant to be hidden. There is that option in DataTables, you know. */
        oSettings.oInstance.children("tbody").find("td").removeAttr("rowspan").show();

        /* Reset values on new cell data. */
        var firstOccurance = {},
            value = {},
            rowspan = {};

        for (i = 0; i < oSettings.aiDisplay.length; i++) {
			oData = oSettings.aoData[oSettings.aiDisplay[i]];
            for (key in oSpannedColumns) {
				var index = fnCellIndexByKey(key);
				
				if (oSpannedColumns[key] === null || index === null) {
					continue;
				}
				
                var cell = $($(oData.nTr).children().get(index)),
					comparisonKey = oSpannedColumns[key],
					val = oSettings.oApi._fnGetObjectDataFn(comparisonKey).call(oData, oData._aData);

				/* Use lowercase comparison if not case-sensitive. */
				if (!bCaseSensitive) {
					val = val.toLowerCase();
				}

				if (typeof value[key] == "undefined" || val != value[key]) {
					value[key] = val;
					firstOccurance[key] = cell;
					rowspan[key] = 1;
				} else {
					rowspan[key]++;
				}
				
				if (typeof firstOccurance[key] != "undefined" && val == value[key] && rowspan[key] > 1) {
					cell.hide();
					firstOccurance[key].attr("rowspan", rowspan[key]);
				}
            }
        }
    }
	
	function fnCellIndexByKey (key) {
		for (index in oSettings.aoColumns) {
			if (oSettings.aoColumns[index].mDataProp == key) {
				return index;
			}
		}
		return null;
	}

    /* Ensure rowspanning is done even if the table has already been drawn. */
    fnMultiRowspan();

    return this;
}
