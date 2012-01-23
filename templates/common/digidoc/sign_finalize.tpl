<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<title>{LC:Dokumendi %s allkirjastamine|{VAR:ddoc_name}}</title>

<style type="text/css" media="all">
@import "/automatweb/css/common/digidoc/ddoc/sign.css";
</style>

<meta http-equiv="Content-Type" content="text/html; charset={VAR:charset}" />
<meta http-equiv="imagetoolbar" content="false" />

{VAR:HTML_HEAD_HTML}

</head>
<body>

	<form method="post" action="" enctype="multipart/form-data" name="xmlsrc">
	{VAR:HTML_FORM_BEGIN_HTML}
	{VAR:reforb}
	{VAR:HTML_FORM_END_HTML}
	</form>
	{VAR:HTML_BODY_HTML}

</body>
</html>
