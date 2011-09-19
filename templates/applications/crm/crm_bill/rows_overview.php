<div id="content">
	<?if ($intro_text) {?>
	<p id="documentComment">
		<?=$intro_text?>
	</p>
	<?}?>

	<p>
		<table id="rows" cellspacing="0" cellpadding="0" border="0">
			<thead>
				<tr>
					<th class="caption"><?=t("Selgitus", $lang_id)?></td>
					<th class="caption"><?=t("Kogus", $lang_id)?></td>
					<th class="caption"><?=t("Hind", $lang_id)?></td>
					<th class="caption"><?=t("Summa", $lang_id)?></td>
					<th class="caption"><?=t("K&auml;ibemaks", $lang_id)?></td>
				</tr>
			</thead>

			<tbody class="rows">
			<?foreach($rows as $row){?>
				<tr>
					<td><?=$row["comment"]?></td>
					<td class="nowrap"><?=$row["quantity_str"]?></td>
					<td><?=$row["price"]?></td>
					<td><?=$row["sum"]?></td>
					<td><?=$row["tax_sum"]?></td>
				</tr>
			<?}?>
			</tbody>

			<tbody class="sum">
				<tr>
					<td colspan="3" class="caption sumcaption"><?=t("KOKKU:", $lang_id)?></td>
					<td><?=$total_wo_tax?> <?=$currency_name?></td>
					<td><?=$tax?> <?=$currency_name?></td>
				</tr>
				<tr>
					<td colspan="3" class="caption sumcaption"><?=t("Summa koos k&auml;ibemaksuga:", $lang_id)?></td>
					<td colspan="2"><strong><?=$total?> <?=$currency_name?></strong></td>
				</tr>
				<tr>
					<td colspan="5"><span class="caption"><?=t("Summa s&otilde;nadega:", $lang_id)?></span> <?=$total_text?></td>
				</tr>
			</tbody>
		</table>
	</p>

	<div class="signatureContainer">
		<div class="signer">
			<h4 class="caption"><?=t("Arve koostaja:", $lang_id)?></h4>
			<p><strong><?=$seller_signer_name?></strong></p>
			<p><?=$seller_signer_profession?></p>
		</div>

		<div class="signer">
			<h4 class="caption"><?=t("Kliendi kontaktisik:", $lang_id)?></h4>
			<p><strong><?=$buyer_signer_name?></strong></p>
			<p><?=$buyer_signer_profession?></p>
		</div>

		<div class="clear" />

		<div class="signer">
			<p class="signature">
			<?=t("Allkiri", $lang_id)?>
			</p>
		</div>

		<div class="signer">
			<p class="signature">
			<?=t("Allkiri", $lang_id)?>
			</p>
		</div>
	</div>
</div>
