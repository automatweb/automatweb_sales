<div id="customerInfoContainer">
	<div class="customerInfo" id="customerInfoContacts">
		<dl>
			<dt class="caption"><?=t("Tellija andmed", $lang_id)?></dt>
			<dd class="first"><?=$buyer_name?> <?=$buyer_corpform?></dd>
			<?if ($buyer_reg_nr){?><dt class="caption">&nbsp;</dt><dd><?=sprintf(t("Reg. nr. %s"), $buyer_reg_nr)?></dd><?}?>
			<?if ($buyer_street){?><dt class="caption">&nbsp;</dt><dd><?=$buyer_street?></dd><?}?>
			<?if ($buyer_index or $buyer_city){?><dt class="caption">&nbsp;</dt><dd><?if ($buyer_index){ echo $buyer_index . ","; }?> <?=$buyer_city?></dd><?}?>
			<?if ($buyer_country){?><dt class="caption">&nbsp;</dt><dd><?=$buyer_country?></dd><?}?>
		</dl>
	</div>

	<div class="customerInfo" id="customerInfoInvoice">
		<dl>
			<dt class="caption"><?=t("Arve nr.", $lang_id)?></dt>
			<dd class="first"><?=$invoice_no?></dd>

			<dt class="caption"><?=t("Arve kuup&auml;ev", $lang_id)?></dt>
			<dd><?=$date?></dd>

			<?if ($due_date){?>
			<dt class="caption"><?=t("Makset&auml;htp&auml;ev", $lang_id)?></dt>
			<dd><?=$due_date?></dd>
			<?}?>

			<?/*if ($due_days){?>
			<dt class="caption"><?=t("Makset&auml;htaeg", $lang_id)?></dt>
			<dd><?=sprintf(t("%s p&auml;eva", $lang_id), $due_days)?></dd>
			<?}*/?>

			<?if ($late_fee){?>
			<dt class="caption"><?=t("Viivis", $lang_id)?></dt>
			<dd><?=sprintf(t("%s%% p&auml;evas", $lang_id), $late_fee)?></dd>
			<?}?>

			<?if ($reminder_date){?>
			<dt class="caption"><?=t("Meeldetuletuse kuup&auml;ev", $lang_id)?></dt>
			<dd><?=$reminder_date?></dd>
			<?}?>

			<?if ($over_due_days){?>
			<dt class="caption"><?=t("P&auml;evi &uuml;le t&auml;htaja", $lang_id)?></dt>
			<dd><?=$over_due_days?></dd>
			<?}?>
		</dl>
	</div>
</div>
