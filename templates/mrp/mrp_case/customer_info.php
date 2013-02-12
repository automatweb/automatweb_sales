<div id="customerInfoContainer">
	<div class="customerInfo" id="customerInfoContacts">
		<dl>
			<dt class="caption"><?php echo t("Tellija andmed", $lang_id)?></dt>
			<dd class="first"><?php echo $buyer_name?> <?php echo (!empty($buyer_corpform) ? $buyer_corpform : null)?></dd>
			<?php if (!empty($buyer_reg_nr)){?><dt class="caption">&nbsp;</dt><dd><?php echo sprintf(t("Reg. nr. %s"), $buyer_reg_nr)?></dd><?php }?>
			<?php if (!empty($buyer_street)){?><dt class="caption">&nbsp;</dt><dd><?php echo $buyer_street?></dd><?php }?>
			<?php if (!empty($buyer_index) or !empty($buyer_city)){?><dt class="caption">&nbsp;</dt><dd><?php if ($buyer_index){ echo $buyer_index . ","; }?> <?php echo $buyer_city?></dd><?php }?>
			<?php if (!empty($buyer_country)){?><dt class="caption">&nbsp;</dt><dd><?php echo $buyer_country?></dd><?php }?>
		</dl>
	</div>

	<div class="customerInfo" id="customerInfoInvoice">
		<dl>
			<dt class="caption"><?php echo t("Tellimus nr.", $lang_id)?></dt>
			<dd class="first"><?php echo $order_no?></dd>

			<dt class="caption"><?php echo t("Tellimuse kuup&auml;ev", $lang_id)?></dt>
			<dd><?php echo $date?></dd>
		</dl>
	</div>
</div>
