<div id="content">
	<?php if ($intro_text) {?>
	<p id="documentComment">
		<?php echo $intro_text?>
	</p>
	<?php }?>

	<p>
		<table id="rows" cellspacing="0" cellpadding="0" border="0">
			<thead>
				<tr>
					<th class="caption"><?php echo t("Selgitus", $lang_id)?></td>
					<th class="caption"><?php echo t("Kogus", $lang_id)?></td>
					<th class="caption"><?php echo t("Hind", $lang_id)?></td>
					<th class="caption"><?php echo t("Summa", $lang_id)?></td>
					<?php if ($tax) {?><th class="caption"><?php echo t("KM", $lang_id)?></td><?php }?>
				</tr>
			</thead>

			<tbody class="rows">
			<?php foreach($rows as $row){?>
				<tr>
					<td><?php echo $row->name?></td>
					<td class="nowrap"><?php echo rtrim($row->quantity, ".0")?></td>
					<td><?php echo number_format($row->price, 2, ".", " ")?></td>
					<td><?php echo number_format((double)$row->total, 2, ".", " ")?></td>
					<?php if ($tax) {?><td><?php echo number_format($row->vat, 2, ".", " ")?></td><?php }?>
				</tr>
			<?php }?>
			</tbody>

			<tbody class="sum">
				<?php if($discount_pct) { ?>
				<tr>
					<td colspan="3" class="caption sumcaption"><?php echo sprintf(t("Soodushindlus %s%%:", $lang_id), $discount_pct)?></td>
					<td><?php echo $discount?> <?php echo $currency_name?></td>
					<?php if ($tax) {?><td></td><?php } ?>
				</tr>
				<?php }?>
				<tr>
					<td colspan="3" class="caption sumcaption"><?php echo t("KOKKU:", $lang_id)?></td>
					<td><?php echo $total_wo_tax?> <?php echo $currency_name?></td>
					<?php if ($tax) {?><td><?php echo number_format($tax, 2,".", " ")?> <?php echo $currency_name?></td><?php } ?>
				</tr>
				<tr>
					<td colspan="3" class="caption sumcaption"><?php echo t($tax ? "Summa koos k&auml;ibemaksuga:" : "Arve summa:", $lang_id)?></td>
					<td colspan="2"><strong><?php echo $total?> <?php echo $currency_name?></strong></td>
				</tr>
				<tr>
					<td colspan="<?php echo $tax ? "5" : "4" ?>"><span class="caption"><?php echo t("Summa s&otilde;nadega:", $lang_id)?></span> <?php echo $total_text?></td>
				</tr>
			</tbody>
		</table>
	</p>

	<div class="signatureContainer">
		<div class="signer">
			<h4 class="caption"><?php echo t("Arve koostaja:", $lang_id)?></h4>
			<p><strong><?php echo $seller_signer_name?></strong></p>
			<p><?php echo $seller_signer_profession?></p>
		</div>

		<div class="signer">
			<h4 class="caption"><?php echo t("Kliendi kontaktisik:", $lang_id)?></h4>
			<p><strong><?php echo $buyer_signer_name?></strong></p>
			<p><?php echo $buyer_signer_profession?></p>
		</div>

		<div class="clear" />

		<div class="signer">
			<p class="signature">
			<?php echo t("Allkiri", $lang_id)?>
			</p>
		</div>

		<div class="signer">
			<p class="signature">
			<?php echo t("Allkiri", $lang_id)?>
			</p>
		</div>
	</div>
</div>
