<div class="pull-left">
	<?php echo implode("", $left_buttons); ?>
</div>
<div class="pull-right">
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
<script type="text/javascript">
(function(){
	function pre_save_callback() {
		$(".modal-footer a, .modal-footer button").attr('disabled', 'disabled');
	}
	function post_save_callback() {
		$(".modal-footer a, .modal-footer button").removeAttr('disabled');
	}

	$("#modal-footer-save [data-click-action~='save']").click(function(){
		var disabled = $(this).attr("disabled");
		var close = $(this).is("[data-click-action~='close']");
		if (typeof disabled === "undefined" || disabled === false) {
			pre_save_callback();
			<?php echo $save_method; ?>(function(){
				AW.UI.modal.alert("Muudatused salvestatud!", "alert-success");
				setTimeout(function () {
					alert.fadeOut("slow");
				}, 2500);
				post_save_callback();
				if (close) {
					$(".modal").modal("hide");
				}
			});
		}
	});
	$('a[data-toggle="tab"]').on('shown', function (e) {
		var target = $(e.target).attr("href").substring(1);
		$(".modal-footer .modal-toolbar").hide();
		$(".modal-footer .modal-toolbar[data-toolbar='" + target + "']").show();
	});
})();
</script>