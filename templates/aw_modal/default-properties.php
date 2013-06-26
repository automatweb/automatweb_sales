<?php
if (!function_exists("parse_modal_property")) {
	function parse_modal_property ($property) {
		switch ($property["type"]) {
			case "hidden":
			?>
				<input type="hidden" id="<?php echo $property["id"]; ?>" <?php echo aw_modal::implode_data_fields(ifset($property, "data")); ?> />
			<?php
				break;

			case "table":
				echo aw_modal::parse_table($property["table"]);
				break;
				
			case "textbox":
			?>
				<input type="text" id="<?php echo $property["id"]; ?>" <?php echo aw_modal::implode_data_fields(ifset($property, "data")); ?> placeholder="<?php echo isset($property["placeholder"]) ? $property["placeholder"] : ""; ?>" />
			<?php
				break;
			
			case "textarea":
			?>
				<textarea rows="3" id="<?php echo $property["id"]; ?>" <?php echo aw_modal::implode_data_fields(ifset($property, "data")); ?> placeholder="<?php echo isset($property["placeholder"]) ? $property["placeholder"] : ""; ?>"></textarea>
			<?php
				break;
			
			case "select":
			?>
				<select id="<?php echo $property["id"]; ?>" <?php echo aw_modal::implode_data_fields(ifset($property, "data")); ?>>
					<?php foreach ($property["options"] as $option_value => $option_caption) { ?>
					<option value="<?php echo $option_value ?>"><?php echo $option_caption ?></option>
					<?php } ?>
				</select>
			<?php
				break;
				
			case "datepicker":
			?>
				<input type="text" id="<?php echo $property["id"]; ?>" <?php echo aw_modal::implode_data_fields(ifset($property, "data")); ?> placeholder="<?php echo isset($property["placeholder"]) ? $property["placeholder"] : ""; ?>" />
			<?php
				break;
				
			case "datetimepicker":
			?>
				<div class="input-append" data-provide="datetimepicker">
					<input data-format="dd/MM/yyyy hh:mm" type="text" id="<?php echo $property["id"]; ?>" <?php echo aw_modal::implode_data_fields(ifset($property, "data")); ?> placeholder="<?php echo isset($property["placeholder"]) ? $property["placeholder"] : ""; ?>"></input>
					<span class="add-on">
						<i data-time-icon="icon-time" data-date-icon="icon-calendar"></i>
					</span>
				</div>
			<?php
				break;
				
			case "button":
			?>
				<button id="<?php echo $property["id"]; ?>" class="btn btn-primary" <?php echo aw_modal::implode_data_fields(ifset($property, "data")); ?>><?php echo $property["button"]["caption"]; ?></button>
			<?php
				break;
		}
	}
}
?>
<div <?php if (!empty($horizontal)) { echo 'class="form-horizontal"'; } ?>>
	<?php foreach ($properties as $property) { ?>
		<?php if ($property["type"] === "hidden" || $property["type"] === "table") { ?>
			<?php echo parse_modal_property($property); ?>
		<?php } else { ?>
			<div class="control-group">
				<label class="control-label" for="<?php echo $property["id"]; ?>"><?php echo (isset($property["caption"]) ? $property["caption"] : ""); ?></label>
				<div class="controls">
					<?php echo parse_modal_property($property); ?>
				</div>
			</div>
		<?php } ?>
	<?php } ?>
</div>