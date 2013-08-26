<div class="modal hide fade" id="nav-editor">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3>Kaustade nimekirja muutmine</h3>
	</div>
	<div class="modal-body">
		<script id="formElementNodeTemplate" type="text/html">
			<tr>
				<td><i class="icon-move"></i></td>
				<td data-bind="style: { 'padding-left': (depth * 24 + 8) + 'px' }">
					<input type="text" data-bind="value: name, valueUpdate:'afterkeydown'" class="input-large" />
				</td>
				<td><a href="#" data-bind="click: $parent.removeChild"><i class="icon-trash"></i></a></td>
			</tr>
			<!-- ko template: { name: 'formElementNodeTemplate', foreach: children } -->
			<!-- /ko -->
		</script>
		<table id="navigation-editor-table" class="table">
			<thead>
				<tr>
					<th>Kaust</th>
					<th>Valikud</th>
				</tr>
			</thead>
			<tbody data-bind="foreach: folders">
				<tr>
					<td><i class="icon-move"></td>
					<td>
						<input type="text" data-bind="value: name, valueUpdate:'afterkeydown'" class="input-large" />
					</td>
					<td><a href="#" data-bind="click: $parent.removeFolder"><i class="icon-trash"></a></td>
				</tr>
				<!-- ko template: { name: 'formElementNodeTemplate', foreach: children } -->
				<!-- /ko -->
			</tbody>
		</table>
		<script type="text/javascript" src="{VAR:baseurl}js/jquery/jquery-sortable-min.js"></script>
		<script type="text/javascript">
			if (false)
			$('#navigation-editor-table').sortable({
				containerSelector: 'table',
				itemPath: '> tbody',
				itemSelector: 'tr',
				handle: 'i.icon-move',
				onDrop: function  (item, container, _super) {
					console.log(item, container, _super);
					return;
					var field,
					newIndex = item.index()
					
					if(newIndex != oldIndex)
						item.closest('table').find('tbody tr').each(function (i, row) {
							row = $(row)
							field = row.children().eq(oldIndex)
							if(newIndex)
								field.before(row.children()[newIndex])
							else
								row.prepend(field)
						})
				
					_super(item)
				}
			});
		</script>
	</div>
	<div class="modal-footer">
		<div class="pull-left">
			<a href="#" class="btn" data-bind="click: $root.new"><i class="icon-plus"></i> Lisa uus kaust</a>
		</div>
		<div class="pull-right">
			<a href="#" class="btn" data-dismiss="modal">Katkesta</a>
			<a href="#" class="btn btn-primary" data-bind="click: $root.save">Salvesta</a>
		</div>
	</div>
</div>
