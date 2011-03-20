<html>
<head>
	<title>Arve nr {VAR:bill_no} aruanne</title>
	<style>
	body { font-size: 9; margin: 2em 2em 2em 4em; }
	table { width: 100%; border: 1px solid white; border-collapse: collapse; }
	h1 { font-size: 14; color: #05A6E9; margin: 0; padding: 2em 0 0 0; }
	h2 { font-size: 12; color: #666666; margin: 0; padding: 0; }
	p { width: 100%; margin: 0; }
	.wrapper { width: 100%; height: 100%; }
	.header td { vertical-align: top; color: #666666; }
	.header td.logo { vertical-align: middle; }
	.data { margin: 3em 0 0 0; }
	.data td { border: 1px solid white; }
	.heading { color: #666666; }

	div.nameGroupComment
	{
		font-size: 12px;
		padding: 0.3em 0 0 0.4em;
		margin: 0;
	}
	</style>
</head>

<body>

<div class="wrapper">

<script type="text/php">
if(isset($pdf))
{
	$font = Font_Metrics::get_font("Arial");
	$size = 9;
	$color = array("00", "00", "00");
	$text_height = Font_Metrics::get_font_height($font, $size);
	$w = $pdf->get_width();
	$h = $pdf->get_height();
	$y = $h - $text_height - 24;
	/* $text = "{PAGE_NUM}/{PAGE_COUNT}"; */
	$width = Font_Metrics::get_text_width("1/2", $font, $size);
	$x = $w - $width;
	$pdf->page_text($x/10, $y, "Arve nr {VAR:bill_no} aruanne", $font, $size, $color);
	$pdf->page_text(($x-30), $y, $text, $font, $size, $color);
}
</script>

<table class="header">
<tr>
	<td class="logo" width="40%">
		<!-- SUB: HAS_IMPL_LOGO -->
		<img src="{VAR:impl_logo_url}">
		<!-- END SUB: HAS_IMPL_LOGO -->
	</td>
	<td width="35%">
		<strong>{VAR:impl_ou} {VAR:impl_name}</strong><br>
		Reg. nr {VAR:impl_reg_nr}<br>
		kmkr {VAR:impl_kmk_nr}<br>
		Swedbank a/a {VAR:acct_no}
	</td>
	<td width="25%">
		{VAR:impl_street}<br>
		{VAR:impl_index}, {VAR:impl_city}<br>
		Tel {VAR:impl_phone}<br>
		{VAR:impl_url}
	</td>
</tr>
<tr>
	<td colspan="3">
		<h1>Arve nr {VAR:bill_no} aruanne</h1>
	</td>
</tr>
<!-- SUB: HAS_COMMENT -->
<tr>
	<td colspan="3" style="font-size: 12px; font-family: Arial; padding: 2em 0 1em 0; text-align: left;">
		{VAR:comment}
	</td>
</tr>
<!-- END SUB: HAS_COMMENT -->
</table>

<!-- SUB: GROUP_ROWS -->
<table class="data" width="100%" cellspacing="0" cellpadding="5">
<tr>
	<td colspan="8">
		<h2>{VAR:uniter}</h2>
		<!-- SUB: HAS_NAME_GROUP_COMMENT -->
		<div class="nameGroupComment">{VAR:name_group_comment}</div>
		<!-- END SUB: HAS_NAME_GROUP_COMMENT -->
	</td>
</tr>
<tr>
	<td width="5%" class="heading"><strong>ID</strong></td>
	<td width="10%" class="heading"><strong>Kuupäev</strong></td>
	<td width="40%" class="heading"><strong>Selgitus</strong></td>
	<td width="5%" class="heading"><strong>Ühik</strong></td>
	<td width="5%" class="heading"><strong>Kogus</strong></td>
	<td width="10%" class="heading"><strong>Hind</strong></td>
	<td width="15%" class="heading"><strong>Summa</strong></td>
	<td width="10%" class="heading"><strong>Käibemaks</strong></td>
</tr>
<!-- SUB: ROW -->
<tr bgcolor="#f5f5f5">
	<td>{VAR:task_row_id}&nbsp;</td>
	<td>{VAR:date}&nbsp;</td>
	<td>{VAR:desc}&nbsp;</td>
	<td>{VAR:unit}&nbsp;</td>
	<td>{VAR:amt}&nbsp;</td>
	<td>{VAR:price}&nbsp;</td>
	<td>{VAR:sum} {VAR:ord_currency_name}&nbsp;</td>
	<td>{VAR:row_tax} {VAR:ord_currency_name}&nbsp;</td>
</tr>
<!-- END SUB: ROW -->
</table>
<!-- END SUB: GROUP_ROWS -->

</div>

</body>
</html>
