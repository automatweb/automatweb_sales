<div class="modal-footer-left pull-left" id="{VAR:prefix}modal-footer-left">
	<?php echo implode("", $left_buttons); ?>
</div>
<div class="modal-footer-right pull-right" id="{VAR:prefix}modal-footer-right">
	<?php echo implode("", $right_buttons); ?>
</div>
<?php foreach ($groups as $group) {
	if (!empty($group["subgroups"])) {
		foreach ($group["subgroups"] as $subgroup) {
			if (isset($subgroup["toolbar"])) {
			?>
				<div class="modal-toolbar" data-toolbar="{VAR:prefix}<?php echo $subgroup["id"]; ?>" style="display: none; text-align: center">
					<?php foreach($subgroup["toolbar"]["buttons"] as $button) {
						echo $button;
					} ?>
				</div>
			<?php
			}
		}
	} elseif (isset($group["toolbar"])) {
	?>
		<div class="modal-toolbar" data-toolbar="{VAR:prefix}<?php echo $group["id"]; ?>" style="display: none; text-align: center">
			<?php foreach($group["toolbar"]["buttons"] as $button) {
				echo $button;
			} ?>
		</div>
	<?php
	}
} ?>
