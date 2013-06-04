<?php if (isset($group["on_demand_url"])) { ?>
<script type="text/javascript">
	(function(){
		$("a[data-toggle='tab'][href='#<?php echo $group["id"]; ?>']").on('shown', function (e) {
			$("#<?php echo $group["id"]; ?>").html("").addClass("modal-tab-loading");
			$.ajax({
				url: "<?php echo $group["on_demand_url"]; ?>",
				data: { id: $("#<?php echo $group["id"]; ?>").parents(".modal").data("id") },
				success: function (html) {
					$("#<?php echo $group["id"]; ?>").html(html);
				},
				error: function () {
					alert("Vaate laadimine ebaõnnestus!");
				},
				complete: function () {
					$("#<?php echo $group["id"]; ?>").removeClass("modal-tab-loading");
				}
			});
		});
	})();
</script>
<?php } else { ?>
	VIGA: Nõudmiseni vaate URL seadistamata!
<?php } ?>
