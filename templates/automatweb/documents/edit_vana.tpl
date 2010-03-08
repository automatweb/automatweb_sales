<script language="Javascript">
function iremote(oid) {
 var windowprops = "toolbar=0,location=1,directories=0,status=0, "+
"menubar=0,scrollbars=1,resizable=1,width=400,height=500";

OpenWindow = window.open("images.{VAR:ext}?type=list&parent=" + oid, "remote", windowprops);
}
</script>
<form method="POST">
<table border=0 cellspacing=1 cellpadding=2 bgcolor="#CCCCCC">
<tr>
<td class="fform" colspan="2">
<input type="submit" value="Salvesta">
</td>
</tr>
<tr>
<td class="fcaption2">Asukoht</td>
<td class="fform">
</td>
</tr>
<tr>
<td class="fcaption2">Periood</td>
<td class="fform">{VAR:periood}</td>
</tr>
<tr>
<td class="fcaption2">Pealkiri</td>
<td class="fform"><input type="text" name="title" size="80" value="{VAR:title}"></td>
</tr>
<tr>
<td class="fcaption2">Tekst</td>
<td class="fform"><input type="text" name="author" size="80" value="{VAR:author}"></td>
</tr>
<tr>
<td class="fcaption2">Fotod</td>
<td class="fform"><input type="text" name="photos" size="80" value="{VAR:photos}"></td>
</tr>
<tr>
<td class="fcaption2">M‰‰rangud</td>
<td class="fform">
	N‰htav:
	<select name="visible">
	{VAR:visible}
	</select>
	Aktiivne:
	<select name="status">
	{VAR:status}
	</select>
</td>
<tr>
	<td class="fcaption2">
		Pildid
	</td>
	<td class="fcaption2">
		{VAR:imglist}
		| <a href="javascript:iremote({VAR:docid})">Kıik</a> | <a href="images.aw?type=add_image&docid={VAR:docid}">Lisa uus&gt;&gt;&gt;</a>
	</td>
</tr>
<tr>
	<td class="fcaption2">
		Tabelid
	</td>
	<td class="fcaption2">
		{VAR:tblist}
		|
		<a href="tables.aw?type=add_table&docid={VAR:docid}">Lisa uus&gt;&gt;&gt;</a>
	</td>
</tr>
<tr>
<td class="fcaption2" valign="top">Lead</td>
<td class="fform">
<textarea name="lead" cols="80" rows="5">
{VAR:lead}
</textarea>
</td>
</tr>
<tr>
<td class="fcaption2" valign="top">Sisu</td>
<td class="fform">
<textarea name="content" cols="80" rows="30">
{VAR:content}
</textarea>
</td>
</tr>
<tr>
<td class="fcaption2" valign="top">Votmesonad</td>
<td class="fform">
<input type="text" name="keywords" size="70" value="{VAR:keywords}">
</td>
</tr>
<!-- SUB: citeblock -->
<tr>
<td class="fcaption2" valign="top">Tsitaat</td>
<td class="fform">
<textarea name="cite" cols="80" rows="3" wrap=soft>
{VAR:cite}
</textarea>
</td>
</tr>
<!-- END SUB: citeblock -->
{VAR:cont}
<!-- SUB: textfield -->
<tr>
<td class="fcaption2" valign="top">{VAR:mcaption}</td>
<td class="fform">
<input type="text" size="{VAR:x}" name="moreinfo[{VAR:mname}]" value="{VAR:mval}">
</td>
</tr>
<!-- END SUB: textfield -->
<!-- SUB: textbox -->
<tr>
<td class="fcaption2" valign="top">{VAR:mcaption}</td>
<td class="fform">
<textarea name="moreinfo[{VAR:mname}]" cols="{VAR:x}" rows="{VAR:y}">
{VAR:mval}
</textarea>
</td>
</tr>
<!-- END SUB: textbox -->
<tr>
<td class="fform" colspan="2">
<input type="submit" value="Salvesta">
<input type="hidden" name="op" value="save">
<input type="hidden" name="docid" value="{VAR:docid}">
</td>
</tr>
</table>
</form>
