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

<script type="text/javascript">
	onload = function()
	{
		sign();
		document.forms["signform"].submit();
	}
</script>

</head>

<body onload="loadPlugin();">
<div id="pluginLocation"></div>
<div style="color: red" id="error"></div>

	<form method="post" action="" enctype="multipart/form-data" name="signform">
		<input type="hidden" id="certId" name="certId" value="{VAR:certId}" />
		<input type="hidden" id="signatureId" name="signatureId" value="{VAR:signatureId}" />
		<input type="hidden" id="hashHex" name="hashHex" value="{VAR:hashHex}" />
		<input type="hidden" id="signatureHex" name="signatureHex" value="" />
		{VAR:reforb}
		<input type="button" value="{LC:Allkirjasta}" class="button" onclick="sign(); this.form.submit();" />
	</form>

</body>
</html>
