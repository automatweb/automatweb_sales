<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>Pakkumine nr {VAR:id}</title>

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

		#bill tr td.footer { border-top: 1px solid #cccccc; text-align: center; font-size: 11px; color: #666666; vertical-align: bottom; height: 2em; }
	</style>

	<style type="text/css" media="print">
		body { margin: 0; }
		#bill { width: 100%; }
		#bill tr td.info table tr td.content { border-bottom: 1px solid #aaaaaa; }
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
	</tr>
	<tr>
		<td colspan="2" class="heading">
			<h1>Pakkumine nr {VAR:id}</h1>
		</td>
	</tr>
	<tr>
		<td class="info">
			<table cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td class="caption">Tellija:</td>
					<td class="content">{VAR:customer}</td>
				</tr>
				<!-- 
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
				-->
			</table>
		</td>
		<td class="info">
			<table cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td class="caption">Pakkumise nr:</td>
					<td class="content">{VAR:id}</td>
				</tr>
				<tr>
					<td class="caption">Pakkumise kuupäev:</td>
					<td class="content">{DATE:date|d.m.y}</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="2" class="main">
			<table cellpadding="0" cellspacing="0" border="0">
				<tr>
					<th width="50%">Selgitus</th>
					<th width="10%">Ühik</th>
					<th width="10%">Kogus</th>
					<th width="10%">Hind</th>
					<th width="20%">Summa</th>
				</tr>
				<!-- SUB: ROW -->
				<tr>
					<td>{VAR:object}&nbsp;</td>
					<td>{VAR:unit}&nbsp;</td>
					<td>{VAR:amount}&nbsp;</td>
					<td>{VAR:price}&nbsp;</td>
					<td>{VAR:sum} {VAR:currency}&nbsp;</td>
				</tr>
				<!-- END SUB: ROW -->
				<tr>
					<td colspan="4" class="caption">Summa:</td>
					<td>{VAR:sum} {VAR:currency}</td>
				</tr>
				<tr>
					<td colspan="5" class="words"><strong>Summa sõnadega:</strong> {VAR:sum_text}</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
	<!-- SUB: CONFIRMATION -->
	<a href="{VAR:do_confirmation_url}">Kinnitan pakkumise</a>
	<!-- END SUB: CONFIRMATION -->
	</tr>
	<tr>
		<td colspan="2" class="footer">
			<p>www.automatweb.com</p>
		</td>
	</tr>
</table>

</body>
</html>
