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
					<th class="caption"><?php echo t("K&auml;ibemaks", $lang_id)?></td>
				</tr>
			</thead>

			<tbody class="rows">
			<?php foreach($rows as $row){?>
				<tr>
					<td><?php echo $row["comment"]?></td>
					<td class="nowrap"><?php echo $row["quantity_str"]?></td>
					<td><?php echo $row["price"]?></td>
					<td><?php echo $row["sum"]?></td>
					<td><?php echo $row["tax_sum"]?></td>
				</tr>
			<?php }?>
			</tbody>

			<tbody class="sum">
				<tr>
					<td colspan="3" class="caption sumcaption"><?php echo t("KOKKU:", $lang_id)?></td>
					<td><?php echo $total_wo_tax?> <?php echo $currency_name?></td>
					<td><?php echo $tax?> <?php echo $currency_name?></td>
				</tr>
				<tr>
					<td colspan="3" class="caption sumcaption"><?php echo t("Summa koos k&auml;ibemaksuga:", $lang_id)?></td>
					<td colspan="2"><strong><?php echo $total?> <?php echo $currency_name?></strong></td>
				</tr>
				<tr>
					<td colspan="5"><span class="caption"><?php echo t("Summa s&otilde;nadega:", $lang_id)?></span> <?php echo $total_text?></td>
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
