<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
		"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head>
	<meta http-equiv="Content-Type" content="text/html; charset={VAR:charset}" />
	<title>Pakkumus nr {VAR:id}</title>

	<style type="text/css">
		body { font-size: 12px; font-family: Arial; margin: 2em 0 2em 0; }
		h1 { font-size: 16px; color: #05A6E9; padding: 1em 0 1em 0.4em; }
		h2 { font-size: 14px; color: #666666; padding: 0 0 0 0.4em; margin: 0; }
		p { margin: 0; }

		#bill { width: 900px; height: 100%; margin: auto; }

		#bill td.logo { padding-left: 0.4em; }
		#bill td.text { width: 50%; padding-right: 1em; border-right: 3px solid #D50000; text-align: right; font-size: 11px; color: #666666; height: 6em; }

		#bill tr td.heading { height: 3em; }

		#bill tr td.info { width: 50%; vertical-align: top; height: 4em; }
		#bill tr td.info table { font-size: 12px; font-family: Arial; }
		#bill tr td.info table tr td.caption { font-weight: bold; color: #666666; padding: 0.4em 1em 0.4em 0.4em; }
		#bill tr td.info table tr td.content { border-bottom: 1px solid #cccccc; padding: 0.4em; }
		#bill tr td.info table tr td.bottom { border: 0px; }

		#bill tr td.main { vertical-align: top; padding: 3em 0 3em 0; }
		#bill tr td.main div.comment { font-size: 10px; color: #666666; }
		#bill tr td.main table { width: 100%; font-size: 12px; font-family: Arial; }
		#bill tr td.main table tr th { text-align: left; border-bottom: 1px solid #cccccc; padding: 0.4em; color: #666666; }
		#bill tr td.main table tr td { border-bottom: 1px solid #cccccc; padding: 0.4em; vertical-align: top; }
		#bill tr td.main table tr td.caption { text-align: right; color: #666666; font-weight: bold; padding-right: 1em; border: 0px;}
		#bill tr td.main table tr td.words { border: 0px; text-align: right; }
		#bill tr td.main table tr td.words strong { color: #666666; padding-right: 1em; }

		#bill tr td.footer { border-top: 1px solid #cccccc; text-align: center; font-size: 11px; color: #666666; vertical-align: bottom; height: 2em; }

		#content { padding: 0 10px 0 0; }
		#confirmation { height: 100%; margin: auto; vertical-align: top; padding: 10px; border: 1px solid #05A6E9; }
		#confirmation label { font-size: 12px; font-family: Arial; display: block; clear: both; padding: 3px 0px; }
		#confirmation div.contract { font-size: 12px; font-family: Arial; padding: 10px 0px; }

	</style>

	<style type="text/css" media="print">
		body { margin: 0; }
		#bill { width: 100%; }
		#bill tr td.info table tr td.content { border-bottom: 1px solid #aaaaaa; }
		#bill tr td.main table tr th { border-bottom: 1px solid #aaaaaa; }
		#bill tr td.main table tr td { border-bottom: 1px solid #aaaaaa; }
		#bill tr td.footer { border-top: 1px solid #aaaaaa; }
	</style>
	<script type="text/javascript" src="http://localhost/automatweb/js/jquery/jquery-1.3.2.min.js"></script>
</head>

<body>

<table id="bill" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td id="content">
			<table>
				<tr class="header">
					<td class="logo">
						<img src="http://intranet.automatweb.com/img/bill/logo.png">
					</td>
				</tr>
				<tr>
					<td colspan="2" class="heading">
						<h1>Pakkumus nr {VAR:id}</h1>
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
								<td class="caption">Pakkumuse nr:</td>
								<td class="content">{VAR:id}</td>
							</tr>
							<tr>
								<td class="caption">Pakkumuse kuup&auml;ev:</td>
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
								<th width="10%">&Uuml;hik</th>
								<th width="10%">Kogus</th>
								<th width="10%">Hind</th>
								<th width="20%">Summa</th>
							</tr>
							<!-- SUB: ROW -->
							<tr>
								<td>
									{VAR:name}&nbsp;
									<!-- SUB: ROW_COMMENT -->
									<div class="comment">
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
								<td colspan="4" class="caption">Summa:</td>
								<td>{VAR:sum} {VAR:currency}</td>
							</tr>
							<tr>
								<td colspan="5" class="words"><strong>Summa s&otilde;nadega:</strong> {VAR:sum_text}</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td colspan="2" class="footer">
						<p>www.automatweb.com</p>
					</td>
				</tr>
			</table>
		</td>
		<!-- SUB: CONFIRMATION -->
		<td id="confirmation">
			<form name="confirm" method="get">
				<label for="firstname">Eesnimi:</label>
					<input type="text" name="firstname" id="firstname" /><br />
				<label for="firstname">Perekonnanimi:</label>
					<input type="text" name="lastname" id="lastname" /><br />
				<label for="firstname">Organisatsioon:</label>
					<input type="text" name="organisation" id="organisation" value="{VAR:customer}" /><br />
				<label for="firstname">Amet:</label>
					<input type="text" name="profession" id="profession" /><br />
				<label for="firstname">Telefon:</label>
					<input type="text" name="phone" id="phone" /><br />
				<label for="firstname">E-post:</label>
					<input type="text" name="email" id="email" value="{VAR:customer.mail}" /><br />

				<!-- SUB: CONTRACT -->
				<div class="contract">
				<input type="checkbox" name="contract[{VAR:contract.id}]" id="contract_{VAR:contract.id}" value="1" />
				N&otilde;ustun <a href="{VAR:contract.link}" target="_blank">lepingu nr {VAR:contract.id}</a> tingimustega.<br />
				</div>
				<!-- END SUB: CONTRACT -->

				<input type="hidden" name="class" value="crm_offer" />
				<input type="hidden" name="action" value="confirm" />
				<input type="hidden" name="id" value="{VAR:id}" />
				<input type="hidden" name="do_confirm" value="1" />

				<br /><br />

				<input type="submit" value="Kinnitan pakkumuse" id="submit">
			</form>
		</td>
		<!-- END SUB: CONFIRMATION -->
	</tr>
</table>

<script type="text/javascript"> 
$(document).ready(function(){
	$("#submit").click(function(){
		if($("input[type=checkbox][name^=contract]").size() - $("input[type=checkbox][name^=contract]:checked").size() > 0){
			alert("Pakkumuse kinnitamiseks peate kinnitama ka lepingu(te) tingimused!");
			return false;
		}
	});
});
</script> 

</body>
</html>
