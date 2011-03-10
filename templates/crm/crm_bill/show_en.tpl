<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-4">
	<title>Invoice nr {VAR:bill_no}</title>

	<style type="text/css">
		body { font-size: 12px; font-family: Arial; margin: 2em 0 2em 0; text-align: center; }
		h1 { font-size: 16px; color: #05A6E9; padding: 1em 0 1em 0.4em; }
		h2 { font-size: 14px; color: #666666; padding: 0 0 0 0.4em; margin: 0; }
		p { margin: 0; }

		#bill { width: 760px; height: 100%; margin: auto; }

		#bill td.logo { padding-left: 0.4em; }
		#bill td.text { width: 50%; padding-right: 1em; border-right: 3px solid #D50000; text-align: right; font-size: 11px; color: #666666; height: 6em; }

		#bill tr td.heading { height: 3em; }

		#bill tr td.info { width: 50%; vertical-align: top; height: 4em; }
		#bill tr td.info table { font-size: 12px; font-family: Arial; }
		#bill tr td.info table tr td.caption { font-weight: bold; color: #666666; padding: 0.4em 1em 0.4em 0.4em; }
		#bill tr td.info table tr td.content { border-bottom: 1px solid #cccccc; padding: 0.4em; }
		#bill tr td.info table tr td.bottom { border: 0px; }

		#bill tr td.main { vertical-align: top; padding: 3em 0 3em 0; }
		#bill tr td.main table { width: 100%; font-size: 12px; font-family: Arial; }
		#bill tr td.main table tr th { text-align: left; border-bottom: 1px solid #cccccc; padding: 0.4em; color: #666666; }
		#bill tr td.main table tr td { border-bottom: 1px solid #cccccc; padding: 0.4em; }
		#bill tr td.main table tr td.caption { text-align: right; color: #666666; font-weight: bold; padding-right: 1em; border: 0px;}
		#bill tr td.main table tr td.words { border: 0px; text-align: right; }
		#bill tr td.main table tr td.words strong { color: #666666; padding-right: 1em; }

		#bill tr td.footer { border-top: 1px solid #cccccc; text-align: center; font-size: 11px; color: #666666; vertical-align: bottom; height: 3em; }
		#bill tr td.footer p { margin-top: 1em; }

		#footer { display: none; }
	</style>

	<style type="text/css" media="print">
		body { margin: 0; }
		#bill { width: 100%; }
		#bill tr td.info table tr td.content { border-bottom: 1px solid #aaaaaa; }
		#bill tr td.main table tr th { border-bottom: 1px solid #aaaaaa; }
		#bill tr td.main table tr td { border-bottom: 1px solid #aaaaaa; }
		#bill tr td.footer { display: none; }

		#footer { position: fixed; bottom: 0; display: table; width: 100%; font-family: Arial; font-size: 11px; }
		#footer tr td { border-top: 1px solid #aaaaaa; text-align: center; font-size: 11px; color: #666666; vertical-align: bottom; height: 3em; }
		#footer tr td p { margin-top: 1em; }
	</style>
</head>

<body>

<table id="bill" cellpadding="0" cellspacing="0" border="0">
	<tr class="header">
		<td class="logo">
			<!-- SUB: HAS_IMPL_LOGO -->
			<img src="{VAR:impl_logo_url}">
			<!-- END SUB: HAS_IMPL_LOGO -->
		</td>
		<td class="text">
			<p><strong>{VAR:impl_ou} {VAR:impl_name}</strong></p>
			<p>Reg. nr {VAR:impl_reg_nr}</p>
			<p>VAT nr {VAR:impl_kmk_nr}</p>
			<p>Bank: Swedbank (Estonia) {VAR:acct_no}</p>
			<p>SWIFT HABAEE2X</p>
			<p>IBAN EE652200221044517419</p>
			<p>{VAR:impl_street} {VAR:impl_index}, {VAR:impl_city}</p>
			<p>Tel +372 6 558 334</p>
			<p>{VAR:impl_url}</p>
		</td>
	</tr>
	<tr>
		<td colspan="2" class="heading">
			<h1>Invoice nr {VAR:bill_no}</h1>
		</td>
	</tr>
	<tr>
		<td class="info">
			<table cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td class="caption">Customer:</td>
					<td class="content">{VAR:orderer_name} {VAR:orderer_corpform}</td>
				</tr>
				<tr>
					<td class="caption"></td>
					<td class="content">{VAR:orderer_street}</td>
				</tr>
				<tr>
					<td class="caption"></td>
					<td class="content">{VAR:orderer_index}, {VAR:orderer_city}</td>
				</tr>
				<tr>
					<td class="caption"></td>
					<td class="content bottom">{VAR:orderer_country}</td>
				</tr>
			</table>
		</td>
		<td class="info">
			<table cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td class="caption">Invoice nr:</td>
					<td class="content">{VAR:bill_no}</td>
				</tr>
				<tr>
					<td class="caption">Invoice date:</td>
					<td class="content">{DATE:bill_date|d.m.Y}</td>
				</tr>
				<tr>
					<td class="caption">Due date:</td>
					<td class="content">{VAR:bill_due}</td>
				</tr>
				<tr>
					<td class="caption"></td>
					<td class="content bottom"></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="2" class="main">
			<table cellpadding="0" cellspacing="0" border="0">
				<tr>
					<th width="50%">Specification</th>
					<th width="5%">Unit</th>
					<th width="5%">Amount</th>
					<th width="10%">Price</th>
					<th width="15%">Sum</th>
					<th width="15%">VAT</th>
				</tr>
				<!-- SUB: ROW -->
				<tr>
					<td>{VAR:desc}&nbsp;</td>
					<td>{VAR:unit}&nbsp;</td>
					<td>{VAR:amt}&nbsp;</td>
					<td>{VAR:price}&nbsp;</td>
					<td>{VAR:sum} {VAR:ord_currency_name}&nbsp;</td>
					<td>{VAR:row_tax} {VAR:ord_currency_name}&nbsp;</td>
				</tr>
				<!-- END SUB: ROW -->
				<tr>
					<td colspan="4" class="caption">SUM:</td>
					<td>{VAR:total_wo_tax} {VAR:ord_currency_name}</td>
					<td>{VAR:tax} {VAR:ord_currency_name}</td>
				</tr>
				<tr>
					<td colspan="6" class="caption">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="4" class="caption">To pay:</td>
					<td colspan="2"><strong>{VAR:total} {VAR:ord_currency_name}</strong></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="2" class="footer">
			<p>{VAR:impl_name} | Reg. nr {VAR:impl_reg_nr} | VAT nr {VAR:impl_kmk_nr}</p> <p>{VAR:impl_street} {VAR:impl_index}, {VAR:impl_city} | Tel +372 6 558 334 | {VAR:impl_url}</p>
		</td>
	</tr>
</table>

<table id="footer" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td>
			<p>{VAR:impl_name} | Reg. nr {VAR:impl_reg_nr} | VAT nr {VAR:impl_kmk_nr}</p> <p>{VAR:impl_street} {VAR:impl_index}, {VAR:impl_city} | Tel +372 6 558 334 | {VAR:impl_url}</p>
		</td>
	</tr>
</table>

</body>
</html>
