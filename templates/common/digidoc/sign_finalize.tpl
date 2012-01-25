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

<body onload="loadPlugin();">
<div id="pluginLocation"></div>
<div style="color: red" id="error"></div>
	<form method="post" action="" enctype="multipart/form-data">
	<input type="hidden" id="certId" name="certId" value="{VAR:certId}" />
	<input type="hidden" id="signatureId" name="signatureId" value="{VAR:signatureId}" />
	<input type="hidden" id="hashHex" name="hashHex" value="{VAR:hashHex}" />
	<input type="hidden" id="signatureHex" name="signatureHex" value="" />
	{VAR:reforb}
	<input type="submit" value="{LC:Allkirjasta}" class="button" />
	</form>

</body>
</html>
