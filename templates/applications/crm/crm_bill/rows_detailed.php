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

			<?php foreach($rows as $row_group){?>
			<tbody class="rows">
				<tr class="headerrow">
					<td colspan="7" class="rowgroup">
						<p class="printspacer">&nbsp;</p>
						<h3 class="caption"><?php echo $row_group["name"]?></h3>
						<?php if($row_group["name_group_comment"]){?>
						<p><?php echo $row_group["name_group_comment"]?></p>
						<?php }?>
					</td>
				</tr>

				<?php foreach($row_group["rows"] as $row){?>
				<tr>
					<td class="descriptionText">
						<?php if($row["row_title"]){?>
						<p class="descriptionText caption">
							<?php echo $row["row_title"]?>
						</p>
						<?php }?>
						<?php if($row["desc"]){?>
						<p class="descriptionText">
							<?php echo $row["desc"]?>
						</p>
						<?php }?>
						<p class="descriptionText">
							<span class="caption"><?php echo t("Kuup&auml;ev:", $lang_id)?></span> <?php echo $row["date"]?>
							<span class="caption"><?php echo t("ID:", $lang_id)?></span> <?php echo $row["oid"]?>
						</p>
					</td>
					<td class="nowrap"><?php echo $row["quantity_str"]?></td>
					<td><?php echo $row["price"]?></td>
					<td><?php echo $row["sum"]?></td>
					<td><?php echo $row["tax_sum"]?></td>
				</tr>
				<?php }?>
			</tbody>
			<?php }?>
		</table>
	</p>
</div>
