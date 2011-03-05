<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title>Pakkumuse nr {VAR:bill_no} n�uded</title>

	<style type="text/css">
		body { font-size: 12px; font-family: Arial; margin: 2em 0 2em 0; text-align: center; }
		h1 { font-size: 16px; color: #05A6E9; padding: 1em 0 0 0.4em; margin: 0; }
		h2 { font-size: 14px; color: #666666; padding: 2em 0 1em 0.4em; margin: 0; }
		p { margin: 0; }

		#bill { width: 760px; height: 100%; margin: auto; }

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
	</style>

	<style type="text/css" media="print">
		body { margin: 0; }
		#bill { width: 100%; }
		#bill tr td.main table tr th { border-bottom: 1px solid #aaaaaa; }
		#bill tr td.main table tr td { border-bottom: 1px solid #aaaaaa; }
		#bill tr td.footer { border-top: 1px solid #aaaaaa; }
	</style>
</head>

<body>

<table id="bill" cellpadding="0" cellspacing="0" border="0">
	<tr class="header">
		<td class="logo">
			<img src="http://intranet.automatweb.com/img/bill/logo.png">
		</td>
		<td class="text">
			<p><strong>{VAR:impl_name}</strong></p>
			<p>Reg. nr. {VAR:impl_reg_nr}</p>
			<p>a/a {VAR:acct_no}</p>
			<p>{VAR:impl_street} {VAR:impl_index}, {VAR:impl_city}</p>
			<p>Tel 6 558 334</p>
			<p>www.automatweb.com</p>
		</td>
	</tr>
	<tr>
		<td colspan="2" class="heading">
			<h1>Pakkumuse nr {VAR:bill_no} n�uded</h1>
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
			<table cellpadding="0" cellspacing="0" border="0">
				<tr>
					<th width="10%">ID</th>
					<th width="10%">T�htp�ev</th>
					<th width="40%">Selgitus</th>
					<th width="10%">Kogus</th>
					<th width="10%">Hind</th>
					<th width="20%">Summa</th>
				</tr>
				<!-- SUB: ROW -->
				<tr>
					<td>{VAR:task_row_id}&nbsp;</td>
					<td>{VAR:date}&nbsp;</td>
					<td>{VAR:desc}&nbsp;</td>
					<td>{VAR:amt}&nbsp;</td>
					<td>{VAR:price}&nbsp;</td>
					<td>{VAR:sum} {VAR:ord_currency_name}&nbsp;</td>
				</tr>
				<!-- END SUB: ROW -->
			</table>
			<!-- END SUB: GROUP_ROWS -->
		</td>
	</tr>
	<tr>
		<td colspan="2" class="footer">
			<p>{VAR:impl_name} | Reg. nr. {VAR:impl_reg_nr} | {VAR:impl_street} {VAR:impl_index}, {VAR:impl_city} | Tel 6 558 334 | www.automatweb.com</p>
		</td>
	</tr>
</table>

</body>
</html>
