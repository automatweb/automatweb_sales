<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<title>{LC:Dokumendi %s allkirjastamine|{VAR:ddoc_name}}</title>

<style type="text/css" media="all">
@import "/automatweb/css/common/digidoc/ddoc/sign.css";
</style>

<meta http-equiv="Content-Type" content="text/html; charset={VAR:charset}" />
<meta http-equiv="imagetoolbar" content="false" />

<script type="text/javascript">
var pluginLanguage = '{VAR:plugin_language}';
var LC_ErrorCode = '{LC:Veakood}';
var LC_Error = '{LC:Viga}';
</script>

<script type="text/javascript" src="{VAR:applets_dir}idCard.js"></script>
<script type="text/javascript" src="{VAR:applets_dir}signingHelpers.js"></script>

</head>

<body onload="loadPlugin();getCert();">
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

		{VAR:reforb}

		<input type="submit" value="{LC:Alusta allkirjastamist}" class="button">
	</form>
</body>
</html>
