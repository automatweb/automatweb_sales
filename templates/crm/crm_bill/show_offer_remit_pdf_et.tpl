<html>
<head>
	<title>Pakkumus nr {VAR:bill_no}</title>
	<style>
	body { font-size: 9; margin: 2em 2em 2em 4em; }
	table { width: 100%;  border: 1px solid white; border-collapse: collapse; }
	h1 { font-size: 14; margin: 0; padding: 2em 0 0 0; text-align: center; }
	h2 { font-size: 9; margin: 0; padding: 0; }
	p { width: 100%; margin: 0; }
	.wrapper { width: 100%; height: 100%; }
	.header td { vertical-align: top; }
	.header td.logo { vertical-align: middle; }
	.subtitle { margin: 0 0 1em 0; text-align: center; }
	.date { margin: 2em 0 2em 0; text-align: right; }
	.data { margin: 0 0 2em 0; }
	.data td { border: 1px solid white; }
	.total { margin: 1em 0 0 0; }
	.info { margin: 0 0 2em 0; }
	.contacts { padding: 2em 0 0 0; }
	.signature { padding: 3em 0 4em 0; }
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
	$width = Font_Metrics::get_text_width("1/2", $font, $size);
	$x = $w - $width;
	$pdf->page_text($x/10, $y, "Pakkumus nr {VAR:bill_no}", $font, $size, $color);
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
	<td colspan="3">
		<h1>Töö üleandmis-vastuvõtmisakt nr {VAR:bill_no}-1</h1>
		<p class="subtitle">Hoolduslepingu PÄA 7.1-12ML/132ML juurde</p>
	</td>
</tr>
<tr>
	<td colspan="3">
		<p class="date"><em>Kuupäev: {DATE:bill_date|d.m.Y}</em></p>
		<p>Töövõtja poolt teostatud tööde loetelu ja maht:</p>
	</td>
</table>

<!-- SUB: GROUP_ROWS -->
<table class="data" width="100%" cellspacing="0" cellpadding="5">
<tr>
	<td colspan="8"><h2>{VAR:uniter}</h2></td>
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

<table class="info">
<tr>
	<td colspan="2">
		<p>Arvestamisele kuuluv tasu: {VAR:total_wo_tax} Eesti krooni.</p>
		<p>Summale lisandub käibemaks (20%): {VAR:tax} Eesti krooni</p>
		<p class="total"><strong>Kokku: {VAR:total} Eesti krooni</strong>
		<p><strong>Summa sõnadega:</strong> {VAR:total_text}</p>
	</td>
<tr>
	<td class="contacts" width="50%">
		Töö üle andnud:<br />
		Töövõtja: {VAR:impl_ou} {VAR:impl_name}<br />
		Esindaja: {VAR:impl_rep}
	</td>
	<td class="contacts" width="50%">
		Töö vastu võtnud:<br />
		Klient: {VAR:orderer_name}<br />
		Esindaja: {VAR:orderer_contact}<br />
	</td>
</tr>
<tr>
	<td class="signature" width="50%">
		<!-- SUB: IMPL_DIG_SIGNATURE -->
		Digitaalselt allkirjastatud
		<!-- END SUB: IMPL_DIG_SIGNATURE -->
		<!-- SUB: IMPL_SIGNATURE -->
		------------------------------
		<!-- END SUB: IMPL_SIGNATURE -->
	</td>
	<td class="signature" width="50%">
		<!-- SUB: ORD_DIG_SIGNATURE -->
		Digitaalselt allkirjastatud
		<!-- END SUB: ORD_DIG_SIGNATURE -->
		<!-- SUB: ORD_SIGNATURE -->
		------------------------------
		<!-- END SUB: ORD_SIGNATURE -->
	</td>
</tr>
</table>

</div>

</body>
</html>
