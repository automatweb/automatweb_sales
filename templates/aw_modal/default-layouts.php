<?php
if (!function_exists("parse_modal_layout")) {
	function parse_modal_layout ($layout) {
		function parse_modal_layout_styles($width) {
			if (preg_match("/(\d+(\.\d+)?)(px)/", $width)) {
				return "class=\"span\" style=\"width: {$width}; margin-left: 0;\"";
			} else {
				return "class=\"span{$width}\"";
			}
		}
		switch ($layout["type"]) {
			case "horizontal":
				$width = explode(":", $layout["width"]);
				$child_counter = 0;
				?>
				<div class="row-fluid">
					<?php foreach ($layout["sublayouts"] as $sublayout_id => $sublayout) { ?>
						<div <?php echo parse_modal_layout_styles($width[$child_counter++]); ?>>
							<?php parse_modal_layout($sublayout); ?>
						</div>
					<?php } ?>
					<?php foreach ($layout["properties"] as $property_id => $property) { ?>
						<div <?php echo parse_modal_layout_styles($width[$child_counter++]); ?>>
							<?php echo aw_modal::parse_properties(array($property_id => $property), ifset($layout, "captionside") === "left"); ?>
						</div>
					<?php } ?>
				</div>
				<?php
				break;
			case "vertical":
				echo aw_modal::parse_properties($layout["properties"], false);
				break;
		}
	}
}
foreach ($layouts as $layout) {
	parse_modal_layout($layout);
}
?>