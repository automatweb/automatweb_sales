<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="et" lang="et">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-4" />
	<link rel="shortcut icon" href="{VAR:baseurl}/automatweb/images/aw06/favicon.ico" />

	<title>AutomatWeb - Culture in Database</title>

	<style type="text/css">
	body { font-family: Arial, Sans-Serif; font-size: 11px; }
	img { border: 0; }
	a { color: #0018af; text-decoration: none; }
	a:hover { text-decoration: underline; }
	p { margin: 0; }

	#wrapper {
		border: 0;
		border-spacing: 1px;
		margin: auto;
		margin-top: 100px;
		width: 450px;
	}

	#wrapper tr td.top { border: 1px solid #e1e1e1; border-spacing: 1px; }
	#wrapper tr td.top form { margin: 0; }
	#wrapper tr td.top table.inner { background: #fcfcfc url("{VAR:baseurl}/automatweb/images/login/top.gif") no-repeat bottom; width: 446px; }
	#wrapper tr td.top table.inner tr td.client { height: 72px; padding-left: 36px; vertical-align: middle; }
	#wrapper tr td.top table.inner tr td.errors { padding-left: 36px; }
	#wrapper tr td.top table.inner tr td.errors p { font-weight: bold; margin: 0 0 16px 0; }
	#wrapper tr td.top table.inner tr td.caption { padding: 2px; text-align: right; width: 106px; }
	#wrapper tr td.top table.inner tr td.element { padding: 2px; width: 340px; }
	#wrapper tr td.top table.inner tr td.element select {
		background-color: #ffffff;
		border-top: 1px solid #808080;
		border-left: 1px solid #808080;
		border-bottom: 1px solid #d4d0c8;
		border-right: 1px solid #d4d0c8;
		font-family: Verdana, Arial, sans-serif;
		margin: 0;
	}
	#wrapper tr td.top table.inner tr td.element input.textbox {
		background-color: #ffffff;
		border-top: 1px solid #808080;
		border-left: 1px solid #808080;
		border-bottom: 1px solid #d4d0c8;
		border-right: 1px solid #d4d0c8;
		font-family: Verdana, Arial, sans-serif;
		margin: 0;
		padding: 2px 3px 2px 3px;
		width: 300px;
	}
	#wrapper tr td.top table.inner tr td.element table { width: 340px; }
	#wrapper tr td.top table.inner tr td.element table tr td.submit { padding: 4px 0 16px 0; vertical-align: top; width: 154px; }
	#wrapper tr td.top table.inner tr td.element table tr td.submit p.id { padding-top: 8px; }
	#wrapper tr td.top table.inner tr td.element table tr td.submit input.button {
		background: #05a6e9;
		border-top: 1px solid #ffffff;
		border-left: 1px solid #ffffff;
		border-bottom: 1px solid #0373ac;
		border-right: 1px solid #0373ac;
		color: #ffffff;
		font-family: Arial, sans-serif;
		font-size: 11px;
		font-weight: bold;
		padding: 2px 2px 1px 2px;
	}
	#wrapper tr td.top table.inner tr td.element table tr td.links { font-weight: bold; padding: 4px 0 16px 0; vertical-align: top; width: 186px; }

	#wrapper tr td.bottom { border: 1px solid #e1e1e1; border-spacing: 1px; }
	#wrapper tr td.bottom table.inner { background: url("{VAR:baseurl}/automatweb/images/login/bottom.gif") no-repeat; height: 114px; width: 446px; }
	#wrapper tr td.bottom table.inner tr td { height: 55px; }
	#wrapper tr td.bottom table.inner tr td.logo { padding-left: 36px; vertical-align: bottom; }
	#wrapper tr td.bottom table.inner tr td.left { vertical-align: middle; width: 260px; }
	#wrapper tr td.bottom table.inner tr td.left p { padding-left: 36px; }
	#wrapper tr td.bottom table.inner tr td.right { vertical-align: middle; width: 186px; }
	</style>
</head>

<body>

<table id="wrapper">
	<tr>
		<td class="top">
			<form name="login" method="post" action="{VAR:baseurl}/automatweb/reforb.{VAR:ext}">
			<table class="inner" cellpadding="0" cellspacing="0">
				<tr>
					<td colspan="2" class="client">

					</td>
				</tr>
				<tr>
					<td class="errors" colspan="2">
						<p>{VAR:login_msg}</p>
					</td>
				</tr>
				<!-- SUB: SERVER_PICKER -->
				<tr>
					<td class="caption">Server:</td>
					<td class="element">
						<select name="server" class="select">
							{VAR:servers}
						</select>
					</td>
				</tr>
				<!-- END SUB: SERVER_PICKER -->
				<tr>
					<td class="caption">Kasutajanimi:</td>
					<td class="element">
						<input type="text" name="uid" size="30" class="textbox" />
					</td>
				</tr>
				<tr>
					<td class="caption">Parool:</td>
					<td class="element">
						<input type="password" name="password" size="30" class="textbox" />
					</td>
				</tr>
				<tr>
					<td class="caption"></td>
					<td class="element">
						<table cellpadding="0" cellspacing="0">
							<tr>
								<td class="submit">
									<p>
										{VAR:reforb}
										<input type="submit" value="Sisene" class="button" />
										<script type="text/javascript">
											document.login.uid.focus();
										</script>
									</p>
									<!-- SUB: ID_LOGIN -->
									<p class="id">
										<a href="{VAR:id_login_url}"><img src="{VAR:baseurl}/automatweb/images/login/id.gif" alt="ID" /></a>
									</p>
									<!-- END SUB: ID_LOGIN -->
								</td>
								<td class="links">
									<p><a href="{VAR:baseurl}?class=users&amp;action=send_hash">Unustasid parooli?</a></p>
									<p><a href="http://support.automatweb.com">Abikeskkond</a></p>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			</form>
		</td>
	</tr>
	<!-- SUB: aw_info -->
	<tr>
		<td class="bottom">
			<table class="inner" cellpadding="0" cellspacing="0">
				<tr>
					<td colspan="2" class="logo">
						<img src="{VAR:baseurl}/automatweb/images/login/aw_logo.gif" alt="AutomatWeb" />
					</td>
				</tr>
				<tr>
					<td class="left">
						<p>O&Uuml; Struktuur Varahaldus</p>
						<p>Aadress: P&auml;rnu mnt. 158b, 11317, Tallinn</p>
					</td>
					<td class="right">
						<p>Infotelefon: +372 5558 0898</p>
						<p>E-post: <a href="mailto:info@automatweb.com">info@automatweb.com</a></p>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<!-- END SUB: aw_info -->
</table>

</body>
</html>
