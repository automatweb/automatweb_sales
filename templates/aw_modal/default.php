<style type="text/css">
.nav-center > li {
	float: none;
	display: inline-block;
	text-align: center
	*display: inline; /* ie7 fix */
	zoom: 1; /* hasLayout ie7 trigger */
}
.nav-center {
	text-align: center;
}
.modal-body {
	padding-left: 0;
	padding-right: 0;
}
.horizontal-padding-15 {
	padding-left: 15px;
	padding-right: 15px;
}
</style>
<div class="modal fade" style="display: none">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<?php echo $header ?>
			</div>
			<div class="modal-body">
				<?php echo $content ?>
			</div>
			<div class="modal-footer">
				<?php echo $footer ?>
			</div>
		</div>
	</div>
</div>
