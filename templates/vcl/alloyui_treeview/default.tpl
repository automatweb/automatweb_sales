<div id="just-in-case-{VAR:id}"></div>

<script type="text/javascript">
YUI().use(
	'aui-tree-view',
	function(Y) {
		// Create an array object for the tree root and child nodes
		var children = {VAR:json};
		var inProgress = {};
		var XHRs = {};

		// Create a TreeView Component
<!-- SUB: DRAGGABLE -->
		new Y.TreeViewDD({
<!-- END SUB: DRAGGABLE -->
<!-- SUB: NON-DRAGGABLE -->
		new Y.TreeView({
<!-- END SUB: NON-DRAGGABLE -->
			boundingBox: '#just-in-case-{VAR:id}',
			children: children,
			after: {
				"tree-node-io:move": function (event) {
					var parent = event.currentTarget.nodeContent._node.parentNode.id;
					var id = event.tree.node._state.data.id.value;
					if (!inProgress[id]) {
						inProgress[id] = true;
						if (XHRs[id]) {
							XHRs[id].abort();
						}
						XHRs[id] = $.ajax({
							url: "/automatweb/orb.aw?class=admin_if&action=move",
//							url: "{VAR:on-drag-url}",
							data: { id: id, parent: parent }
						});
						setTimeout(function(){
							inProgress[id] = false;
						}, 100);
					}
				}
			}
		}).render();
		$("#just-in-case-{VAR:id}").on("click", "i.aui-icon-minus", function (event) {
			var id = $(this).siblings("span").find("a").data("node-id");
			$.cookie("alloyui-treeview-{VAR:id}-" + id, "true");
		});
		$("#just-in-case-{VAR:id}").on("click", "i.aui-icon-plus", function (event) {
			var id = $(this).siblings("span").find("a").data("node-id");
			$.cookie("alloyui-treeview-{VAR:id}-" + id, $(this).siblings("i.aui-icon-refresh").size() > 0 ? "true" : null);
		});
	}
);
</script>