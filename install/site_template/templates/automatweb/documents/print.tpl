<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{VAR:lang_code}" lang="{VAR:lang_code}">
<head>
	<title>Firmanimi</title>
	<meta http-equiv="Content-Type" content="text/html; charset={VAR:charset}" />
	<link href="{VAR:baseurl}/automatweb/css/print.css" rel="stylesheet" type="text/css" />
</head>
<body>

<div id="content">

<table id="header">
	<tr>
		<td class="l"><img src="{VAR:baseurl}/automatweb/images/aw06/aw_logo.gif" alt="" /></td>
		<td class="r">Lehte viimati muudetud:<br />{VAR:modified}</td>
	</tr>
</table>

<hr />

<h1>{VAR:title}</h1>
{VAR:text}

<br class="break" />
<hr />

<table id="footer">
	<tr>
		<td class="l">
		OÜ Struktuur Meedia<br />
		Pärnu maantee 158b<br />
		11317, Tallinn
		</td>
		<td class="r">
		Müügiinfo: (+372) 6 558 336<br />
		Klienditugi: (+372) 6 558 334<br />
		info@struktuur.ee
		</td>
	</tr>
</table>

</div>


</body>
</html>
<!-- SUB: image -->
<div class="image image_{VAR:alignstr}">
<p style="width: {VAR:width}px; ">
<img src="{VAR:imgref}" alt="{VAR:alt}" title="{VAR:alt}" width="{VAR:width}" height="{VAR:height}" />
<br />
{VAR:imgcaption}
</p>
</div>
<!-- END SUB: image -->
<!-- SUB: image_linked -->
<div class="image image_{VAR:alignstr}">
<a href="{VAR:plink}"><img src="{VAR:imgref}" alt="{VAR:alt}" title="{VAR:alt}" class="image image_{VAR:alignstr}" width="{VAR:width}" height="{VAR:height}" /></a>
<br />
{VAR:imgcaption}
</div>
<!-- END SUB: image_linked --> 
<!-- SUB: image_has_big -->
<div class="image image_{VAR:alignstr}">
<a href="#" onClick="window.open('{VAR:bigurl}','popup','width={VAR:big_width},height={VAR:big_height}');">
<img src="{VAR:imgref}" alt="{VAR:alt}" title="{VAR:alt}" width="{VAR:width}" height="{VAR:height}" /></a>
<br />
{VAR:imgcaption}
</div>
<!-- END SUB: image_has_big -->
<!-- SUB: link -->
<a {VAR:target} href="{VAR:url}">{VAR:caption}</a>
<!-- END SUB: link -->