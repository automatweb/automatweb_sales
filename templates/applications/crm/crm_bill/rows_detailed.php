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

			<?foreach($rows as $row_group){?>
			<tbody class="rows">
				<tr class="headerrow">
					<td colspan="7" class="rowgroup">
						<p class="printspacer">&nbsp;</p>
						<h3 class="caption"><?=$row_group["name"]?></h3>
						<?if($row_group["name_group_comment"]){?>
						<p><?=$row_group["name_group_comment"]?></p>
						<?}?>
					</td>
				</tr>

				<?foreach($row_group["rows"] as $row){?>
				<tr>
					<td class="descriptionText">
						<?if($row["row_title"]){?>
						<p class="descriptionText caption">
							<?=$row["row_title"]?>
						</p>
						<?}?>
						<?if($row["desc"]){?>
						<p class="descriptionText">
							<?=$row["desc"]?>
						</p>
						<?}?>
						<p class="descriptionText">
							<span class="caption"><?=t("Kuup&auml;ev:", $lang_id)?></span> <?=$row["date"]?>
							<span class="caption"><?=t("ID:", $lang_id)?></span> <?=$row["oid"]?>
						</p>
					</td>
					<td class="nowrap"><?=$row["quantity_str"]?></td>
					<td><?=$row["price"]?></td>
					<td><?=$row["sum"]?></td>
					<td><?=$row["tax_sum"]?></td>
				</tr>
				<?}?>
			</tbody>
			<?}?>
		</table>
	</p>
</div>
