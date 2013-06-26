<!-- SUB: ROW -->
		<tr {VAR:row.data}>
			<!-- SUB: ROW.FIELD -->
			<{VAR:field.tag} {VAR:field.class} {VAR:field.data} {VAR:field.span} {VAR:field.style}>{VAR:field.value}</{VAR:field.tag}>
			<!-- END SUB: ROW.FIELD -->
		</tr>
<!-- END SUB: ROW -->

<h4>{VAR:caption}</h4>
<table id="{VAR:id}" class="table table-striped table-hover table-condensed">
	<!-- SUB: HEADER -->
	<thead>
		{VAR:HEADER.ROWS}
	</thead>
	<!-- END SUB: HEADER -->
	<!-- SUB: BODY -->
	<tbody>
		{VAR:BODY.ROWS}
	</tbody>
	<!-- END SUB: BODY -->
	<!-- SUB: FOOTER -->
	<tfoot>
		{VAR:FOOTER.ROWS}
	</tfoot>
	<!-- END SUB: FOOTER -->
</table>
<!-- SUB: REORDERABLE -->
<script type="text/javascript">
(function(){
	var selected_expandables = [];
	var visible_expandables = $([]);
	var onUpdateAjaxRequest;
	$("#{VAR:id} tbody").sortable({
		handle: 'td:first',
		axis: "y",
		cancel: "[data-expandable=true]",
		helper: function(e, tr) {
			var $originals = tr.children();
			var $helper = tr.clone();
			$helper.children().each(function(index) {
			  $(this).width($originals.eq(index).width());
			});
			selected_expandables = [];
			var expandable = tr.next();
			while (expandable.data("expandable")) {
				if (expandable.data("expandable")) {
					selected_expandables.push(expandable);
				}
				expandable = expandable.next();
			}
			return $helper;
		},
		start: function (event, ui) {
			visible_expandables = $(ui.item.siblings("[data-expandable=true]:visible")).hide();
		},
		update: function (event, ui) {
			while (true) {
				var next = ui.item.next();
				if (next.data("expandable")) {
					next.after(ui.item);
				} else {
					break;
				}
			}
			var prev = ui.item;
			for (var i in selected_expandables) {
				prev.after(selected_expandables[i]);
				prev = selected_expandables[i];
			}
			selected_expandables = [];
			visible_expandables.show();
			
			var rows = [];
			$("#{VAR:id} tbody tr:not([data-expandable=true])").each(function (index, row) {
				row = $(row);
				rows.push({
					id: row.data("object-id"),
					index: row.index()
				});
			});
			if (onUpdateAjaxRequest) {
				onUpdateAjaxRequest.abort();
			}
			onUpdateAjaxRequest = $.ajax({
				url: "{VAR:on-reorderable-update}",
				type: "POST",
				data: { order: rows }
			});
		},
	}).disableSelection();
})();
</script>
<!-- END SUB: REORDERABLE -->
