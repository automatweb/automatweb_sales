<a href="javascript:void(0)" class="btn" data-dismiss="modal" style="float: left;">Katkesta</a>
<div id="save-customer-modal" class="btn-group" style="float: right">
	<button data-click-action="save" class="btn btn-primary">Salvesta</button>
	<button class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
		&nbsp;<span class="caret" style="margin-left: -5px"></span>
	</button>
	<ul class="dropdown-menu" style="text-align: left">
		<li><a data-click-action="save close" href="javascript:void(0)">Salvesta ja sulge</a></li>
<!--		<li><a data-click-action="save" href="javascript:void(0)">Salvesta ja duplikeeri</a></li> -->
	</ul>
</div>
<div class="modal-toolbar" data-toolbar="contact-address" style="display: none; text-align: center">
	<a href="javascript:void(0)" class="btn" onclick="$('#contact-address-edit').slideDown(200);" ><i class="icon-plus"></i> Lisa uus aadress</a>
</div>
<div class="modal-toolbar" data-toolbar="employees" style="display: none; text-align: center">
	<a href="javascript:void(0)" class="btn" onclick="$('#employees-edit').slideDown(200);" ><i class="icon-plus"></i> Lisa uus t&ouml;&ouml;taja</a>
</div>
<script type="text/javascript">
(function(){
	function pre_save_callback() {
		$(".modal-footer a, .modal-footer button").attr('disabled', 'disabled');
	}
	function post_save_callback() {
		$(".modal-footer a, .modal-footer button").removeAttr('disabled');
	}

	$("#save-customer-modal [data-click-action~='save']").click(function(){
		var disabled = $(this).attr("disabled");
		var close = $(this).is("[data-click-action~='close']");
		if (typeof disabled === "undefined" || disabled === false) {
			pre_save_callback();
			AW.UI.crm_customer_view.save_customer(function(){
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
