<?php if (isset($group["on_demand_click"])) { ?>
<script type="text/javascript">
	(function(){
		$("a[data-toggle='tab'][href='#{VAR:prefix}<?php echo $group["id"]; ?>']").attr("data-bind", "click: AW.UI.modal.load_group").attr("data-loader", "<?php echo $group["on_demand_click"]; ?>");
	})();
</script>
<?php echo $layouts.$properties; ?>
<?php } elseif (isset($group["on_demand_url"])) { ?>
<script type="text/javascript">
	(function(){
		$("a[data-toggle='tab'][href='#{VAR:prefix}<?php echo $group["id"]; ?>']").on('shown', function (e) {
			$("#{VAR:prefix}<?php echo $group["id"]; ?>").html("").addClass("modal-tab-loading");
			$.ajax({
				url: "<?php echo $group["on_demand_url"]; ?>",
				data: { id: $("#{VAR:prefix}<?php echo $group["id"]; ?>").parents(".modal").data("id") },
				success: function (html) {
					$("#{VAR:prefix}<?php echo $group["id"]; ?>").html(html);
				},
				error: function () {
					alert("Vaate laadimine ebaõnnestus!");
				},
				complete: function () {
					$("#{VAR:prefix}<?php echo $group["id"]; ?>").removeClass("modal-tab-loading");
				}
			});
		});
	})();
</script>
<?php } else { ?>
	VIGA: Nõudmiseni vaate URL seadistamata!
<?php } ?>
