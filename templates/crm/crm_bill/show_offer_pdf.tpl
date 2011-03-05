<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title>Pakkumus nr {VAR:bill_no}</title>

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

		#bill tr td.caption{ font-weight: bold; color: #666666; padding: 0.4em 1em 0.4em 0.4em; font-size:12px; }
		#bill tr td.koostaja {text-align: left; font-size:12px; padding-left:10px; padding:0.4em;}
    #bill tr td.koostaja div.signature {width: 300px; float:left; border-bottom: 1px solid; font-size:11px;}
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
			<img src="http://intranet.automatweb.com/img/automatweb.jpg">
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
			<h1>Pakkumus nr {VAR:bill_no}</h1>
		</td>
	</tr>
	<tr>
		<td class="info">
			<table cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td class="caption">Kliendi andmed:</td>
					<td class="content">{VAR:orderer_name}</td>
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
					<td class="caption">Pakkumus nr:</td>
					<td class="content">{VAR:bill_no}</td>
				</tr>
				<tr>
					<td class="caption">Pakkumuse kuupäev:</td>
					<td class="content">{DATE:bill_date|d.m.Y}</td>
				</tr>
				<tr>
					<td class="caption">Kehtivus:</td>
					<td class="content">{VAR:payment_due_days} päeva</td>
				</tr>
				<tr>
					<td class="caption"></td>
					<td class="content bottom">{VAR:bill_due}</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="2" class="main">
			<table cellpadding="0" cellspacing="0" border="0">
				<tr>
					<th width="50%">Nimetus</th>
					<th width="10%">Ühik</th>
					<th width="10%">Kogus</th>
					<th width="10%">Hind</th>
					<th width="20%">Summa</th>
				</tr>
				<!-- SUB: ROW -->
				<tr>
					<td>{VAR:desc}&nbsp;</td>
					<td>{VAR:unit}&nbsp;</td>
					<td>{VAR:amt}&nbsp;</td>
					<td>{VAR:price}&nbsp;</td>
					<td>{VAR:sum} {VAR:ord_currency_name}&nbsp;</td>
				</tr>
				<!-- END SUB: ROW -->
				<tr>
					<td colspan="4" class="caption">Kokku:</td>
					<td>{VAR:total_wo_tax} {VAR:ord_currency_name}</td>
				</tr>
				<tr>
					<td colspan="4" class="caption">Käibemaks 20%:</td>
					<td>{VAR:tax} {VAR:ord_currency_name}</td>
				</tr>
				<tr>
					<td colspan="4" class="caption">Summa:</td>
					<td><strong>{VAR:total} {VAR:ord_currency_name}</strong></td>
				</tr>
				<tr>
					<td colspan="5" class="words"><strong>Summa sõnadega:</strong> {VAR:total_text}</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
    <td class="koostaja">
      {VAR:creator.name}<br>
      {VAR:creator.current_job_edit}<br><br>
      <div class="signature">
      Allkiri
      </div>
    </td>
    <td class="koostaja">
  	 {VAR:orderer_contact}<br>
  	 {VAR:orderer_contact.current_job_edit}<br><br>
  	  <div class="signature">
      Allkiri
      </div>
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
