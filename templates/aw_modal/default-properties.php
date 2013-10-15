<?php
if (!function_exists("parse_modal_property")) {
	function parse_modal_property ($property) {
		switch ($property["type"]) {
			case "text":
			?>
				<div id="{VAR:prefix}<?php echo $property["id"]; ?>" <?php echo aw_modal::implode_data_fields(ifset($property, "data")); ?>>
					<?php echo ifset($property, "value"); ?>
				</div>
			<?php
				break;
			
			case "hidden":
			?>
				<input type="hidden" id="{VAR:prefix}<?php echo $property["id"]; ?>" <?php echo aw_modal::implode_data_fields(ifset($property, "data")); ?> />
			<?php
				break;

			case "table":
				echo aw_modal::parse_table(isset($property["table"]) ? $property["table"] : null);
				break;
			
			case "checkbox":
			?>
				<input type="checkbox" id="{VAR:prefix}<?php echo $property["id"]; ?>" <?php echo aw_modal::implode_data_fields(ifset($property, "data")); ?> value="1" /> <?php echo $property["caption"]; ?>
			<?php
				break;
				
			case "textbox":
			?>
				<input type="text" id="{VAR:prefix}<?php echo $property["id"]; ?>" <?php echo aw_modal::implode_data_fields(ifset($property, "data")); ?> placeholder="<?php echo isset($property["placeholder"]) ? $property["placeholder"] : ""; ?>" />
			<?php
				break;
			
			case "textarea":
			?>
				<textarea rows="<?php echo (!empty($property["rows"]) ? $property["rows"] : 3); ?>" id="{VAR:prefix}<?php echo $property["id"]; ?>" <?php echo aw_modal::implode_data_fields(ifset($property, "data")); ?> placeholder="<?php echo isset($property["placeholder"]) ? $property["placeholder"] : ""; ?>" class="<?php echo isset($property["class"]) ? $property["class"] : ""; ?>"></textarea>
			<?php
				break;
			
			case "select":
			?>
				<select id="{VAR:prefix}<?php echo $property["id"]; ?>" <?php echo aw_modal::implode_data_fields(ifset($property, "data")); ?>>
					<?php foreach ($property["options"] as $option_value => $option_caption) { ?>
					<option value="<?php echo $option_value ?>"><?php echo $option_caption ?></option>
					<?php } ?>
				</select>
			<?php
				break;
				
			case "datepicker":
			?>
				<input type="text" id="{VAR:prefix}<?php echo $property["id"]; ?>" <?php echo aw_modal::implode_data_fields(ifset($property, "data")); ?> placeholder="<?php echo isset($property["placeholder"]) ? $property["placeholder"] : ""; ?>" />
			<?php
				break;
				
			case "datetimepicker":
			?>
				<div id="{VAR:prefix}<?php echo $property["id"]; ?>" <?php echo aw_modal::implode_data_fields(ifset($property, "data")); ?>></div>
			<?php
				break;
				
			case "button":
			?>
				<button id="{VAR:prefix}<?php echo $property["id"]; ?>" class="btn btn-primary" <?php echo aw_modal::implode_data_fields(ifset($property, "data")); ?>><?php echo $property["button"]["caption"]; ?></button>
			<?php
				break;
			
			case "chooser":
			?>
				<div id="{VAR:prefix}<?php echo $property["id"]; ?>" <?php echo aw_modal::implode_data_fields(ifset($property, "data")); ?>></div>
			<?php
				break;
			
			case "yui-chooser":
				$chooser = new yui_chooser();
				$processed = $chooser->init_vcl_property(array("property" => $property));
				echo $processed[$property["name"]]["value"];
				break;

			case "fileupload":
			?>
				<form method="POST" action="orb.aw?class=file&action=upload" enctype="multipart/form-data">
					<input type="file" id="<?php echo $property["id"]; ?>" name="file" data-bind="upload: file" />
				</form>
			<?php
				break;
			
			case "treeview":
				?>
				<div id="{VAR:prefix}<?php echo $property["id"]; ?>" <?php echo aw_modal::implode_data_fields(ifset($property, "data")); ?>></div>
				<?php
				break;
		}
	}
}
?>
<div <?php if (!empty($horizontal)) { echo 'class="form-horizontal"'; } ?> data-dummy="jj">
	<?php foreach ($properties as $property) { ?>
		<?php if ($property["type"] === "hidden" || $property["type"] === "table" || !empty($property["no_caption"])) { ?>
			<?php echo parse_modal_property($property); ?>
		<?php } else { ?>
			<div class="control-group">
				<?php if ($property["type"] !== "checkbox") { ?>
					<label class="control-label" for="<?php echo $property["id"]; ?>"><?php echo (isset($property["caption"]) ? $property["caption"] : ""); ?></label>
				<?php } ?>
				<div class="controls">
					<?php echo parse_modal_property($property); ?>
				</div>
			</div>
		<?php } ?>
	<?php } ?>
</div>