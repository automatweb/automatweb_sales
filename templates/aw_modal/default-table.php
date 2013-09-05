<?php /* TEMPORARY */ if (!empty($table["hack"])) { ?>
<style>
.hack-wrap {
	width: 705px;
/*	display: inline-block; */
}
.hack-2 {
	width: 350.5px;
	display: inline-block;
}
.hack-3 {
	width: 233px;
	display: inline-block;
}
.hack-4 {
	width: 174.25px;
	display: inline-block;
}
.hack-5 {
	width: 139px;
	display: inline-block;
}
.hack-6 {
	width: 115.5px;
	display: inline-block;
}
.hack-wrap h5 {
	display: inline-block;
}
.hack-col {
	float: left;
	height: 200px;
	border: 1px solid #e5e5e5;
	border-right: 0;
}
.hack-inner {
	height: 200px;
	width: 100%;
}
.hack-col:first-child {
	-webkit-border-radius: 6px 0 0 6px;
	-moz-border-radius: 6px 0 0 6px;
	border-radius: 6px 0 0 6px;
}
.hack-col:last-child {
	-webkit-border-radius: 0 6px 6px 0;
	-moz-border-radius: 0 6px 6px 0;
	border-radius: 0 6px 6px 0;
	border-right: 1px solid #e5e5e5;
}
/* This causes jQuery to crash - WTF?!
.hack-col:only-child {
	width: 235px;
	-webkit-border-radius: 6px 6px 6px 6px;
	-moz-border-radius: 6px 6px 6px 6px;
	border-radius: 6px 6px 6px 6px;
	border-right: 1px solid #e5e5e5;
}
*/
.hack-selected {
	float: right;
}
.bs-docs-sidenav {
	margin: 0;
}
.bs-docs-sidenav > li > a {
	border-top: 0;
	border-right: 0;
	border-left: 0;
}
.bs-docs-sidenav > li:first-child > a, .bs-docs-sidenav > li:last-child > a {
	-webkit-border-radius: 0 0 0 0;
	-moz-border-radius: 0 0 0 0;
	border-radius: 0 0 0 0;
}
</style>
<div class="hack-selected">
	<h5>Valitud objektid</h5>
	<div>
		<div class="hack-col antiscroll-wrap">
			<div class="hack-inner antiscroll-inner">
				<ul class="nav nav-list bs-docs-sidenav hack-3" data-bind="foreach: selected">
					<li><a href="javascript:void(0)" data-bind="click: $root.remove"><i class="icon-chevron-right"></i> <span data-bind="text: name ? name : '(nimetu)'"></span></a></li>
				</ul>
			</div>
		</div>
	</div>
</div>
<div class="hack-wrap" data-bind="foreach: levels">
	<div class="pull-left" data-bind="css: $root.css, visible: visible" style="margin: 0 1px">
		<h5 data-bind="text: caption"></h5>
	</div>
</div>
<div class="hack-wrap" data-bind="foreach: levels">
	<div class="hack-col antiscroll-wrap" data-bind="css: $root.css, visible: visible">
		<div class="hack-inner antiscroll-inner">
			<ul class="nav nav-list bs-docs-sidenav" data-bind="visible: loaded, foreach: items, css: $root.css">
				<li data-bind="visible: !$root.isSelected($data)"><a href="javascript:void(0)" data-bind="click: $parent.index() == $root.levels().length - 1 ? $root.select : $root.toggle"><i class="icon-chevron-right"></i> <span data-bind="text: name ? name : '(nimetu)'"></span></a></li>
			</ul>
			<div class="progress progress-striped active" style="width: 50%; margin: auto; margin-top: 90px;" data-bind="visible: loading">
				<div class="bar" style="width: 100%;"></div>
			</div>
		</div>
	</div>
	<!--
	<div class="hack-col antiscroll-wrap">
		<div class="hack-inner antiscroll-inner">
			<ul class="nav nav-list bs-docs-sidenav" data-bind="visible: !loading(), foreach: results">
				<li data-bind="visible: !$root.isSelected($data)"><a href="javascript:void(0)" data-bind="click: $root.select"><i class="icon-chevron-right"></i> <span data-bind="text: name ? name : '(nimetu)'"></span></a></li>
			</ul>
			<div class="progress progress-striped active" style="width: 50%; margin: auto; margin-top: 90px;" data-bind="visible: loading">
				<div class="bar" style="width: 100%;"></div>
			</div>
		</div>
	</div>
	-->
</div>
<?php } else { ?>
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

if (is_array($table)) {
?>
<h4><?php echo $table["caption"]; ?></h4>
<table id="{VAR:prefix}<?php echo $table["id"]; ?>" class="table table-hover table-condensed">
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
	$("#{VAR:prefix}<?php echo $table["id"]; ?> tbody").sortable({
		handle: "<?php echo isset($table["reorderable-handle"]) ? $table["reorderable-handle"] : "td:first"; ?>",
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
			<?php echo isset($table["reorderable-start"]) ? "{$table["reorderable-start"]}(event, ui)" : ""; ?>
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
			<?php echo isset($table["reorderable-update"]) ? "{$table["reorderable-update"]}(event, ui)" : ""; ?>
		},
	}).disableSelection();
})();
</script>
<?php
	}
}
?>
<?php /* TEMPORARY */ } ?>