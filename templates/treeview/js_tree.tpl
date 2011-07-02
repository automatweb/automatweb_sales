<div id="{VAR:tree_id}"></div>

<script type="text/javascript">

var jsTreeNodeClickUrl = "";
$(function () {
	$('#{VAR:tree_id}').jstree({
		'json_data' : {
			'ajax': {
				'type': 'GET',
				'url': '{VAR:data_source_url}',
				'async': true,
				'data': function(n) {
					return { 'node': n.attr ? n.attr('id') : -1 };
				}
			}
		},
		"ui" : {
			"select_limit" : 1
		},
		'themes': { 'theme': 'default', 'url': '/automatweb/js/jquery/plugins/jsTree/themes/automatweb/style.css' },
		'plugins' : ['json_data','themes','ui','cookies']
	})
	.bind("select_node.jstree", function (event, data) {
		jsTreeNodeClickUrl = data.rslt.obj.attr("url");
	})
	.delegate("a", "click", function (event) {
		window.location = jsTreeNodeClickUrl;
		event.preventDefault();
	});

	// correct cookies plugin selection to reflect state on the aw application server side
	$('#{VAR:tree_id}').bind("reselect.jstree", function (event, data) {
		if (!'{VAR:selected_item}') {
			// deselect item reselected by cookies plugin since php application has not specified a selected item
			data.inst.deselect_all();
		}
		else {
			selectedItem = data.inst.get_selected();
			selectedItemId = selectedItem.attr("id");
			if ('{VAR:selected_item}' != selectedItemId) {
				// select different item if php application has altered the view by means other than this js tree instance
				// inspect();
				data.inst.deselect_node(selectedItem);
				data.inst.select_node($("#{VAR:tree_id} li#{VAR:selected_item}"));
			}
		}
	})
});


</script>
