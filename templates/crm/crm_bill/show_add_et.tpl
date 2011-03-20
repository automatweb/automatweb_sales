<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title>Arve nr {VAR:bill_no} aruanne</title>

	<style type="text/css">
		body { font-size: 12px; font-family: Arial; margin: 2em 0 2em 0; text-align: center; }
		h1 { font-size: 16px; color: #05A6E9; padding: 1em 0 0 0.4em; margin: 0; }
		h2 { font-size: 14px; color: #666666; padding: 2em 0 0 0.4em; margin: 0; }
		p { margin: 0; }

		#bill { width: 760px; height: 100%; margin: auto; page-break-inside: avoid; }

		#bill td.logo { padding-left: 0.4em; }
		#bill td.text { width: 50%; padding-right: 1em; border-right: 3px solid #D50000; text-align: right; font-size: 11px; color: #666666; height: 6em; }

		#bill tr td.heading { height: 2em; }

		#bill tr td.main { vertical-align: top; padding: 0 0 3em 0; }
		#bill tr td.main table { width: 100%; font-size: 12px; font-family: Arial; }
		#bill tr td.main table tr th { text-align: left; border-bottom: 1px solid #cccccc; padding: 0.4em; color: #666666; }
		#bill tr td.main table tr td { border-bottom: 1px solid #cccccc; padding: 0.4em; }
		#bill tr td.main table tr td.caption { text-align: right; color: #666666; font-weight: bold; padding-right: 1em; border: 0px;}
		#bill tr td.main table tr td.words { border: 0px; text-align: right; }
		#bill tr td.main table tr td.words strong { color: #666666; padding-right: 1em; }

		#bill tr td.footer { border-top: 1px solid #cccccc; text-align: center; font-size: 11px; color: #666666; vertical-align: bottom; height: 2em; }
		#bill tr td.footer p { margin-top: 1em; }

		#footer { display: none; }

		div.nameGroupComment
		{
			font-size: 12px;
			padding: 0.3em 0 0 0.4em;
			margin: 0;
		}
	</style>

	<style type="text/css" media="print">
		body { margin: 0; }
		#bill { width: 100%; }
		#bill tr td.main table tr th { border-bottom: 1px solid #aaaaaa; }
		#bill tr td.main table tr td { border-bottom: 1px solid #aaaaaa; }
		#bill tr td.footer { display: none; }

		#footer { position: fixed; bottom: 0; display: table; width: 100%; font-family: Arial; font-size: 11px; }
		#footer tr td { border-top: 1px solid #aaaaaa; text-align: center; font-size: 11px; color: #666666; vertical-align: bottom; height: 2em; }
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
			<p>kmkr {VAR:impl_kmk_nr}</p>
			<p>Swedbank a/a {VAR:acct_no}</p>
			<p>{VAR:impl_street} {VAR:impl_index}, {VAR:impl_city}</p>
			<p>Tel {VAR:impl_phone}</p>
			<p>{VAR:impl_url}</p>
		</td>
	</tr>
	<tr>
		<td colspan="2" class="heading">
			<h1>Arve nr {VAR:bill_no} aruanne</h1>
		</td>
	</tr>
	<!-- SUB: HAS_COMMENT -->
	<tr>
		<td colspan="2" style="font-size: 12px; font-family: Arial; padding: 2em 0 1em 0; text-align: left;">
			{VAR:comment}
		</td>
	</tr>
	<!-- END SUB: HAS_COMMENT -->
	<tr>
		<td colspan="2" class="main">
			<!-- SUB: GROUP_ROWS -->
			<h2>{VAR:uniter}</h2>
			<!-- SUB: HAS_NAME_GROUP_COMMENT -->
			<div class="nameGroupComment">{VAR:name_group_comment}</div>
			<!-- END SUB: HAS_NAME_GROUP_COMMENT -->
			<table cellpadding="0" cellspacing="0" border="0" style="margin-top: 1em;">
				<tr>
					<th width="10%">ID</th>
					<th width="10%">Kuupäev</th>
					<th width="40%">Selgitus</th>
					<th width="5%">Kogus</th>
					<th width="10%">Hind</th>
					<th width="15%">Summa</th>
					<th width="10%">Käibemaks</th>
				</tr>
				<!-- SUB: ROW -->
				<tr>
					<td>{VAR:task_row_id}&nbsp;</td>
					<td>{VAR:date}&nbsp;</td>
					<td>{VAR:desc}&nbsp;</td>
					<td>{VAR:amt}&nbsp;</td>
					<td>{VAR:price}&nbsp;</td>
					<td>{VAR:sum} {VAR:ord_currency_name}&nbsp;</td>
					<td>{VAR:row_tax} {VAR:ord_currency_name}&nbsp;</td>
				</tr>
				<!-- END SUB: ROW -->
			</table>
			<!-- END SUB: GROUP_ROWS -->
		</td>
	</tr>
	<tr>
		<td colspan="2" class="footer">
			<p>{VAR:impl_name} | Reg. nr {VAR:impl_reg_nr} | Käibemaksukohuslase nr {VAR:impl_kmk_nr}</p> <p>{VAR:impl_street} {VAR:impl_index}, {VAR:impl_city} | Tel {VAR:impl_phone} | {VAR:impl_url}</p>
		</td>
	</tr>
</table>

<table id="footer" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td>
			<p>{VAR:impl_name} | Reg. nr {VAR:impl_reg_nr} | Käibemaksukohuslase nr {VAR:impl_kmk_nr}</p> <p>{VAR:impl_street} {VAR:impl_index}, {VAR:impl_city} | Tel {VAR:impl_phone} | {VAR:impl_url}</p>
		</td>
	</tr>
</table>

</body>
</html>
