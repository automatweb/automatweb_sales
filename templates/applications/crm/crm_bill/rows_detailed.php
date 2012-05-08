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

			<?php foreach($rows as $row_group){?>
			<tbody class="rows">
				<tr class="headerRow">
					<td colspan="<?php echo $tax ? "5" : "4" ?>" class="rowgroup">
						<?php /* printspacers are used to set margins and paddings in pdf print visual layout because for unknown reasons mpdf doesn't understand css here */ ?>
						<?php if(isset($first_rowgroup_rendered)){ ?><p class="printspacer">&nbsp;</p><?php } else { $first_rowgroup_rendered = true; }?>
						<?php if($row_group["name"]){?>
						<h3 class="caption"><?php echo $row_group["name"]?></h3>
						<?php }?>
						<?php if($row_group["name_group_comment"]){?>
						<p class="printspacer">&nbsp;</p>
						<p class="nameGroupComment"><?php echo $row_group["name_group_comment"]?></p>
						<?php }?>
					</td>
				</tr>

				<?php foreach($row_group["rows"] as $row){?>
				<tr class="titleRow">
					<td>
						<?php if($row["row_title"]){?>
						<p class="caption">
							<?php echo $row["row_title"]?>
						</p>
						<?php }?>
					</td>
					<td class="nowrap"><?php echo $row["quantity_str"]?></td>
					<td><?php echo $row["price"]?></td>
					<td><?php echo $row["sum"]?></td>
					<?php if ($tax) {?><td><?php echo $row["tax_sum"]?></td><?php }?>
				</tr>
				<tr class="descriptionRow">
					<td class="descriptionText">
						<?php if($row["desc"]){?>
						<p class="descriptionText">
							<?php echo $row["desc"]?>
						</p>
						<?php }?>
						<p class="printspacer">&nbsp;</p>
						<p class="descriptionText">
							<span class="caption"><?php echo t("Kuup&auml;ev:", $lang_id)?></span> <?php echo $row["date"]?>
							<span class="caption"><?php echo t("ID:", $lang_id)?></span> <?php echo $row["oid"]?>
						</p>
					</td>
					<td colspan="<?php echo $tax ? "4" : "3" ?>"></td>
				</tr>
				<?php }?>
			</tbody>
			<?php }?>
		</table>
	</p>
</div>
