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
		<table>
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
	{VAR:reforb}
	{VAR:HTML_FORM_END_HTML}
	</form>

{VAR:HTML_BODY_HTML}

</body>
</html>
