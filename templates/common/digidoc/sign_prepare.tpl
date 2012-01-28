<html>
<head>

<meta http-equiv="Content-Type" content="text/html; charset={VAR:charset}" />
<title>{LC:Dokumendi %s allkirjastamine|{VAR:ddoc_name}}</title>

<style type="text/css" media="all">
@import "/automatweb/css/common/digidoc/ddoc/sign.css";
</style>

<script type="text/javascript">
var pluginLanguage = '{VAR:plugin_language}';
var appletsUrl = '{VAR:applets_url}';
var LC_ErrorCode = '{LC:Veakood}';
var LC_Error = '{LC:Viga}';
</script>

<script type="text/javascript" src="{VAR:applets_url}idCard.js"></script>
<script type="text/javascript" src="{VAR:applets_url}signingHelpers.js"></script>
</head>

<body onload="loadPlugin();">
<div id="pluginLocation"></div>
<div style="color: red" id="error"></div>

<body>

	<form method="post" action="" enctype="multipart/form-data">
		<p>
		<table>
			<tr>
				<td colspan="2" class="title">{LC:Allkirjastamise asukoht (valikuline)}</td>
			</tr>
			<tr>
				<td>{LC:Linn}</td>
				<td><input type="text" name="city"/></td>
			</tr>
			<tr>
				<td>{LC:Maakond}</td>
				<td><input type="text" name="state"/></td>
			</tr>
			<tr>
				<td>{LC:Postiindeks}</td>
				<td><input type="text" name="postal_code"/></td>
			</tr>
			<tr>
				<td>{LC:Riik}</td>
				<td><input type="text" name="country"/></td>
			</tr>
			<tr>
				<td>{LC:Roll}</td>
				<td><input type="text" name="role"/></td>
			</tr>
		</table>
		</p>

		<input type="hidden" id="certId" name="certId" value="" />
		<input type="hidden" id="certHex" name="certHex" value="" />

		{VAR:reforb}

		<input type="button" value="{LC:Alusta allkirjastamist}" class="button" onclick="getCert(); setTimeout(function(){}, 2000); if (document.getElementById('certId').value) { this.form.submit(); } else { alert ('{LC:Sertifikaat valimata}'); }" />
	</form>

</body>
</html>
