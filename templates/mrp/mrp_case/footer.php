<div id="footer">
	<p><?php echo $seller_name?><?php if($seller_reg_nr) echo " | " . $seller_reg_nr?><?php if($seller_tax_reg_nr) echo "|" . $seller_tax_reg_nr?></p>
	<p><?php echo $seller_street?><?php if($seller_index) echo " " . $seller_index?><?php if($seller_city) echo ", " . $seller_city?><?php if($seller_phone) echo "|" . $seller_phone?><?php if($seller_url) echo "|" . $seller_url?></p>
	<p class="pageNumber">{PAGENO} / {nb}</p>
</div>
