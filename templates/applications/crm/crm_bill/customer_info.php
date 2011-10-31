<div id="customerInfoContainer">
	<div class="customerInfo" id="customerInfoContacts">
		<dl>
			<dt class="caption"><?php echo t("Tellija andmed", $lang_id)?></dt>
			<dd class="first"><?php echo $buyer_name?> <?php echo $buyer_corpform?></dd>
			<?php if ($buyer_reg_nr){?><dt class="caption">&nbsp;</dt><dd><?php echo sprintf(t("Reg. nr. %s"), $buyer_reg_nr)?></dd><?php }?>
			<?php if ($buyer_street){?><dt class="caption">&nbsp;</dt><dd><?php echo $buyer_street?></dd><?php }?>
			<?php if ($buyer_index or $buyer_city){?><dt class="caption">&nbsp;</dt><dd><?php if ($buyer_index){ echo $buyer_index . ","; }?> <?php echo $buyer_city?></dd><?php }?>
			<?php if ($buyer_country){?><dt class="caption">&nbsp;</dt><dd><?php echo $buyer_country?></dd><?php }?>
		</dl>
	</div>

	<div class="customerInfo" id="customerInfoInvoice">
		<dl>
			<dt class="caption"><?php echo t("Arve nr.", $lang_id)?></dt>
			<dd class="first"><?php echo $invoice_no?></dd>

			<dt class="caption"><?php echo t("Arve kuup&auml;ev", $lang_id)?></dt>
			<dd><?php echo $date?></dd>

			<?php if ($due_date){?>
			<dt class="caption"><?php echo t("Makset&auml;htp&auml;ev", $lang_id)?></dt>
			<dd><?php echo $due_date?></dd>
			<?php }?>

			<?php /*if ($due_days){?>
			<dt class="caption"><?php echo t("Makset&auml;htaeg", $lang_id)?></dt>
			<dd><?php echo sprintf(t("%s p&auml;eva", $lang_id), $due_days)?></dd>
			<?php }*/?>

			<?php if ($late_fee){?>
			<dt class="caption"><?php echo t("Viivis", $lang_id)?></dt>
			<dd><?php echo sprintf(t("%s%% p&auml;evas", $lang_id), $late_fee)?></dd>
			<?php }?>

			<?php if ($reminder_date){?>
			<dt class="caption"><?php echo t("Meeldetuletuse kuup&auml;ev", $lang_id)?></dt>
			<dd><?php echo $reminder_date?></dd>
			<?php }?>

			<?php if ($over_due_days){?>
			<dt class="caption"><?php echo t("P&auml;evi &uuml;le t&auml;htaja", $lang_id)?></dt>
			<dd><?php echo $over_due_days?></dd>
			<?php }?>
		</dl>
	</div>
</div>
