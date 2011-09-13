<div id="container">
	<div id="header">
		<div id="logo">
			<?if($seller_logo_url){?>
			<img src="<?=$seller_logo_url?>">
			<?}?>
		</div>
		<div id="contacts">
			<ul>
				<li class="caption"><?=$seller_name?></li>

				<?if($seller_reg_nr){?>
				<li><?=sprintf(t("Reg. nr.: %s", $lang_id), $seller_reg_nr)?></li>
				<?}?>

				<?if($seller_tax_reg_nr){?>
				<li><?=sprintf(t("Kmkr. nr.: %s", $lang_id), $seller_tax_reg_nr)?></li>
				<?}?>

				<?if($seller_bank_accounts[0]["account_nr"]){?>
				<li><?=sprintf(t("Arveldusarve: %s", $lang_id), $seller_bank_accounts[0]["account_nr"])?></li>
				<?}?>

				<?if($seller_address){?>
				<li><?=$seller_address?></li>
				<?}?>

				<?if($seller_phone){?>
				<li><?=$seller_phone?></li>
				<?}?>

				<?if($seller_url){?>
				<li><?=$seller_url?></li>
				<?}?>
			</ul>
		</div>
	</div>

	<div class="clear" />

	<div class="title">
		<h1><?=$document_name?></h1>
		<?if ($title){?><h2><?=$title?></h2><?}?>
	</div>

	<?=$heading?>

	<div class="clear" />

	<?=$content?>
</div>

<div class="clear" />

<?=$footer?>
