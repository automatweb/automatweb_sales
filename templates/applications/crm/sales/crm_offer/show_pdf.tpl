<html>
<head>
	<title>Pakkumus nr {VAR:id}</title>
	<style>
	body { font-size: 9; margin: 2em 2em 2em 4em; }
	table { width: 100%; border: 1px solid white; border-collapse: collapse; }
	h1 { font-size: 14; color: #05A6E9; margin: 0; padding: 2em 0 0 0; }
	p { width: 100%; margin: 0; }
	.wrapper { width: 100%; height: 100%; }
	.header td { vertical-align: top; color: #666666; }
	.header td.logo { vertical-align: middle; }
	.info { margin: 3em 0 0 0; }
	.info td { border: 1px solid white; }
	.data { margin: 3em 0 0 0; }
	.data td { border: 1px solid white; }
	.row_comment { font-size: 8px; color: #666666; }
	.heading { color: #666666; }
	.bill_text { border-top: 1px solid gray; margin: 2em; }
	.bill_text_div { padding: 1em; }
	.signature {width: 150px; border-bottom: 0.01em solid; font-size:8px;}
	.koostaja {padding:0.4em}
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
	$pdf->page_text($x/10, $y, "Pakkumus nr {VAR:id}", $font, $size, $color);
	$pdf->page_text(($x-30), $y, $text, $font, $size, $color);
}
</script>

<table class="header">
<tr>
	<td width="40%" class="logo">
		<img src="http://intranet.automatweb.com/img/automatweb.jpg" alt="AutomatWeb" width="277" height="48">
	</td>
	<td width="35%">
		<strong>{VAR:salesorg.ou} {VAR:salesorg.name}</strong><br>
		Reg. nr {VAR:salesorg.reg_nr}<br>
		kmkr {VAR:salesorg.kmk_nr}<br>
		Swedbank a/a {VAR:salesorg.acct_no}
	</td>
	<td width="25%">
		{VAR:salesorg.address.street}<br>
		{VAR:salesorg.address.index}, {VAR:salesorg.address.city}<br>
		Tel 6 558 334<br>
		www.automatweb.com
	</td>
</tr>
<tr>
	<td colspan="3">
		<h1>Pakkumus nr {VAR:id}</h1>
	</td>
</tr>
</table>

<table class="info" width="100%" cellspacing="0" cellpadding="2">
<tr>
	<td width="15%" class="heading"><strong>Kliendi andmed:</strong></td>
	<td width="35%">{VAR:customer.name}</td>
	<td width="15%" class="heading"><strong>Pakkumuse nr:</strong></td>
	<td width="35%">{VAR:id}</td>
</tr>
<tr>
	<td width="15%" class="heading"></td>
	<td width="35%">{VAR:customer.address.street}</td>
	<td width="15%" class="heading"><strong>Kuup�ev:</strong></td>
	<td width="35%">{DATE:date|d.m.Y}</td>
</tr>
<tr>
	<td width="15%" class="heading"></td>
	<td width="35%">{VAR:customer.address.index}, {VAR:customer.address.city}</td>
	<td width="15%">&nbsp;</td>
	<td width="35%">&nbsp;</td>
</tr>
<tr>
	<td width="15%" class="heading"></td>
	<td width="35%">{VAR:customer.address.country}</td>
	<td width="15%">&nbsp;</td>
	<td width="35%">&nbsp;</td>
</tr>
</table>

<table class="data" width="100%" cellspacing="0" cellpadding="5">
<tr>
	<td width="55%" class="heading"><strong>Selgitus</strong></td>
	<td width="5%" class="heading"><strong>�hik</strong></td>
	<td width="5%" class="heading"><strong>Kogus</strong></td>
	<td width="15%" class="heading"><strong>Hind</strong></td>
	<td width="20%" class="heading"><strong>Summa</strong></td>
</tr>
<!-- SUB: ROW -->
<tr bgcolor="#f5f5f5">
	<td>
		{VAR:name}&nbsp;
		<!-- SUB: ROW_COMMENT -->
		<div class="row_comment">
			{VAR:comment}&nbsp;
		</div>
		<!-- END SUB: ROW_COMMENT -->
	</td>
	<td>{VAR:unit}&nbsp;</td>
	<td>{VAR:amount}&nbsp;</td>
	<td>{VAR:price}&nbsp;</td>
	<td>{VAR:sum} {VAR:currency}&nbsp;</td>
</tr>
<!-- END SUB: ROW -->
<tr>
	<td colspan="4" class="heading" align="right"><strong>KOKKU:</strong></td>
	<td>{VAR:total} {VAR:currency}</td>
</tr>
<tr>
	<td colspan="5" class="heading" align="right">&nbsp;</td>
</tr>
<tr>
	<td colspan="3" class="heading" align="right"><strong>Summa koos k�ibemaksuga:</strong></td>
	<td colspan="2">{VAR:total} {VAR:currency}</td>
</tr>
<tr>
	<td colspan="3" class="heading" align="right"><strong>Summa s�nadega:</strong></td>
	<td colspan="2">{VAR:total_text}</td>
</tr>
<table  width="100%" cellspacing="0" cellpadding="5">
  <tr>
		<td  class="caption"><br><br>
			M��giesindaja:
		</td>
		<td  class="caption"><br><br>
			Kliendi kontaktisik:
		</td>
	</tr>
  <tr>
    <td  class="koostaja">
      {VAR:salesman.name}<br>
      {VAR:salesman.profession}<br><br><br>
      <div class="signature">
      Allkiri
      </div>
    </td>
    <td  class="koostaja">
  	 {VAR:customer.director.name}<br>
  	 {VAR:customer.director.profession}<br><br><br>
  	  <div class="signature">
      Allkiri
      </div>
    </td>
  </tr>
  </table>

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
