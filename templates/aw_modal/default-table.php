<?php
if (!function_exists("parse_table_row")) {
	function parse_table_row($row, $table, $content = true, $tag = "td") {
		if (!empty($table["content"]["expandable"])) {
			if ($content) { ?>
				<td><a href="javascript:void(0)" class="expander" onclick="AW.UI.table.toggleExpandable(this);"><i class="icon-chevron-down"></a></td>
			<?php } else {
				echo "<{$tag}></{$tag}>";
			}
		}
		$skip = 0;
		foreach ($table["fields"] as $field_id) {
			if (--$skip > 0) {
				continue;
			}
			$field = isset($row[$field_id]) ? $row[$field_id] : null;
			if (isset($field["colspan"])) {
				$skip = $field["colspan"];
			}
			echo "<{$tag} " . (isset($field["align"]) && $field["align"] === "right" ? " class=\"text-right\"" : "") . (isset($field["data"]) ? aw_modal::implode_data_fields($field["data"]) : "") . (isset($field["colspan"]) && $field["colspan"] > 1 ? " colspan=\"{$field["colspan"]}\"" : "") . ">" . (is_array($field) ? (isset($field["value"]) ? $field["value"] : "") : $field) . "</{$tag}>";
		}
	}
}
?>
<h4><?php echo $table["caption"]; ?></h4>
<table id="<?php echo $table["id"]; ?>" class="table table-hover table-condensed">
	<thead>
		<tr>
			<?php parse_table_row($table["header"]["fields"], $table, false, "th"); ?>
		</tr>
	</thead>
	<tbody <?php echo aw_modal::implode_data_fields(ifset($table, "content", "data")); ?>>
		<tr <?php echo aw_modal::implode_data_fields(ifset($table, "content", "data-row")); ?>>
			<?php parse_table_row($table["content"]["fields"], $table); ?>
		</tr>
		<?php if (!empty($table["content"]["expandable"])) { ?>
			<?php foreach ($table["content"]["expandable_rows"] as $expandable_row) { ?>
				<tr style="display: none" data-expandable="true">
					<?php parse_table_row($expandable_row, $table, false); ?>
				</tr>
			<?php } ?>
		<?php } ?>
	</tbody>
	<?php if (isset($table["footer"])) { ?>
		<tfoot>
			<tr>
				<?php parse_table_row($table["footer"]["fields"], $table, false); ?>
			</tr>
		</tfoot>
	<?php } ?>
</table>
<?php if (!empty($table["reorderable"])) { ?>
<script type="text/javascript">
(function(){
	var selected_expandables = [];
	var visible_expandables = $([]);
	$("#<?php echo $table["id"]; ?> tbody").sortable({
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
		},
	}).disableSelection();
})();
</script>
<?php } ?>