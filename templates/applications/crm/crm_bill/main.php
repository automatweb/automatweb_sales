<div id="container">
	<div id="header">
		<div id="logo">
			<?php echo $seller_logo?>
		</div>
		<div id="contacts">
			<ul>
				<li class="caption"><?php echo $seller_name?></li>

				<?php if($seller_reg_nr){?>
				<li><?php echo sprintf(t("Reg. nr.: %s", $lang_id), $seller_reg_nr)?></li>
				<?php }?>

				<?php if($seller_tax_reg_nr){?>
				<li><?php echo sprintf(t("Kmkr. nr.: %s", $lang_id), $seller_tax_reg_nr)?></li>
				<?php }?>

				<?php if(!empty($seller_bank_accounts[0]["account_nr"])){?>
				<li><?php echo sprintf(t("Arveldusarve: %s", $lang_id), $seller_bank_accounts[0]["account_nr"])?></li>
				<?php }?>

				<?php if($seller_address){?>
				<li><?php echo $seller_address?></li>
				<?php }?>

				<?php if($seller_phone){?>
				<li><?php echo $seller_phone?></li>
				<?php }?>

				<?php if($seller_url){?>
				<li><?php echo $seller_url?></li>
				<?php }?>
			</ul>
		</div>
	</div>

	<div class="clear" />

	<div class="title">
		<h1><?php echo $document_name?></h1>
		<?php if ($title){?><h2><?php echo $title?></h2><?php }?>
	</div>

	<?php echo $heading?>

	<div class="clear" />

	<?php echo $content?>
</div>

<div class="clear" />

<?php echo $footer?>
