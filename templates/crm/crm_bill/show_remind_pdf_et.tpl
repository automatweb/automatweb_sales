<html>
<head>
	<title>Arve nr {VAR:bill_no}</title>
	<style>
	body { font-size: 9; }
	table { width: 100%; border: 1px solid white; border-collapse: collapse; }
	h1 { font-size: 14; color: #05A6E9; margin: 0; padding: 2em 0 0 0; }
	p { width: 100%; margin: 0; }
	.wrapper { margin: 2em 2em 2em 4em; width: 100%; height: 100%; }
	.header td { vertical-align: top; color: #666666; }
	.header td.logo { vertical-align: middle; }
	.remind { padding: 2em 0 0 0; text-align: right; }
	.info { margin: 3em 0 0 0; }
	.info td { border: 1px solid white; }
	.data { margin: 3em 0 0 0; }
	.data td { border: 1px solid white; }
	.heading { color: #666666; }
	.bill_text { border-top: 1px solid gray; margin: 2em; }
	.bill_text_div { padding: 1em; }
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
	$pdf->page_text($x/10, $y, "Arve nr {VAR:bill_no}", $font, $size, $color);
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
		Tel 6 558 334<br>
		www.automatweb.com
	</td>
</tr>
<tr>
	<td>
		<h1>Arve nr {VAR:bill_no}</h1>
	</td>
	<td class="remind" colspan="2">
		<img src="http://intranet.automatweb.com/img/bill/kordusarve.gif" width="227" height="70">
	</td>
</tr>
</table>

<table class="info" width="100%" cellspacing="0" cellpadding="2">
<tr>
	<td width="15%" class="heading"><strong>Tellija andmed:</strong></td>
	<td width="35%">{VAR:orderer_name} {VAR:orderer_corpform}</td>
	<td width="15%" class="heading"><strong>Arve nr:</strong></td>
	<td width="35%">{VAR:bill_no}</td>
</tr>
<tr>
	<td width="15%" class="heading"></td>
	<td width="35%">{VAR:orderer_street}</td>
	<td width="15%" class="heading"><strong>Arve kuupäev:</strong></td>
	<td width="35%">{DATE:bill_date|d.m.Y}</td>
</tr>
<tr>
	<td width="15%" class="heading"></td>
	<td width="35%">{VAR:orderer_index}, {VAR:orderer_city}</td>
	<td width="15%" class="heading"><strong>Maksetähtaeg:</strong></td>
	<td width="35%">{VAR:bill_due}</td>
</tr>
<tr>
	<td width="15%" class="heading"></td>
	<td width="35%">{VAR:orderer_country}</td>
	<td width="15%" class="heading">Viivis:</td>
	<td width="35%">{VAR:overdue}% päevas</td>
</tr>
</table>

<table class="data" width="100%" cellspacing="0" cellpadding="5">
<tr>
	<td width="40%" class="heading"><strong>Selgitus</strong></td>
	<td width="5%" class="heading"><strong>Ühik</strong></td>
	<td width="5%" class="heading"><strong>Kogus</strong></td>
	<td width="15%" class="heading"><strong>Hind</strong></td>
	<td width="20%" class="heading"><strong>Summa</strong></td>
	<td width="15%" class="heading"><strong>Käibemaks</strong></td>
</tr>
<!-- SUB: ROW -->
<tr bgcolor="#f5f5f5">
	<td>{VAR:desc}&nbsp;</td>
	<td>{VAR:unit}&nbsp;</td>
	<td>{VAR:amt}&nbsp;</td>
	<td>{VAR:price}&nbsp;</td>
	<td>{VAR:sum} {VAR:ord_currency_name}&nbsp;</td>
	<td>{VAR:row_tax} {VAR:ord_currency_name}&nbsp;</td>
</tr>
<!-- END SUB: ROW -->
<tr>
	<td colspan="4" class="heading" align="right"><strong>KOKKU:</strong></td>
	<td>{VAR:total_wo_tax} {VAR:ord_currency_name}</td>
	<td>{VAR:tax} {VAR:ord_currency_name}</td>
</tr>
<tr>
	<td colspan="6" class="heading" align="right">&nbsp;</td>
</tr>
<tr>
	<td colspan="4" class="heading" align="right"><strong>Summa koos käibemaksuga::</strong></td>
	<td colspan="2">{VAR:total} {VAR:ord_currency_name}</td>
</tr>
<tr>
	<td colspan="4" class="heading" align="right"><strong>Summa sõnadega:</strong></td>
	<td colspan="2">{VAR:total_text}</td>
</tr>
</table>

<table class="bill_text" width="100%" cellspacing="0" cellpadding="0" border="0">
<tr>
	<td>
		<div class="bill_text_div">{VAR:bill_text}</div>
	</td>
</tr>
</table>

</div>

</body>
</html>
