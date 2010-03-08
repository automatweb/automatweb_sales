<script language="Javascript">
function iremote(oid) {
 var windowprops = "toolbar=0,location=1,directories=0,status=0, "+
"menubar=0,scrollbars=1,resizable=1,width=400,height=500";

OpenWindow = window.open("images.{VAR:ext}?type=list&parent=" + oid, "remote", windowprops);
}
</script>
<form method="POST" action="reforb.{VAR:ext}">
<table border=0 cellspacing=1 cellpadding=2 bgcolor="#CCCCCC">
<tr>
<td class="fform" colspan="2">
<input type="submit" value="Salvesta"> <a href='{VAR:weburl}'>Webile</a>
</td>
</tr>
<tr>
<td class="fcaption2" colspan=2><a href='{VAR:lburl}'>Seotud dokumendid:</a></td>
</tr>
<!-- SUB: DOC_BROS -->
<tr>
<td class="fcaption2">{VAR:lang_name}</td>
<td class="fform"><a href='{VAR:chbrourl}'>{VAR:bro_name}</a></td>
</tr>
<!-- END SUB: DOC_BROS -->
<tr>
<td class="fcaption2" colspan=2><a href='{VAR:menurl}'>Sektsioonid</a></td>
</tr>
<tr>
<td class="fcaption2">Pealkiri</td>
<td class="fform"><input type="text" name="title" size="80" value="{VAR:title}"></td>
</tr>
<tr>
<td class="fcaption2">Pikem pealkiri</td>
<td class="fform"><input type="text" name="long_title" size="80" value="{VAR:long_title}"></td>
</tr>
<tr>
<td class="fcaption2">Kuup&auml;ev</td>
<td class="fform"><input type="text" name="tm" size="20" value="{VAR:tm}"></td>
</tr>
<tr>
<td class="fcaption2">M‰‰rangud</td>
<td class="fform">
	Aktiivne:
	<select name="status">
	{VAR:status}
	</select>
	Paremal: <input type='checkbox' NAME='esilehel_uudis' VALUE=1 {VAR:esilehel_uudis}>
	Pealkiri n&auml;htav dokumendi sees: <input type='checkbox' NAME='show_title' VALUE=1 {VAR:show_title}>
	Copyright: <input type='checkbox' NAME='copyright' VALUE=1 {VAR:copyright}>
	N&auml;ita muudetud: <input type='checkbox' NAME='show_modified' VALUE=1 {VAR:show_modified}>
</td>
<tr>
	<td class="fcaption2">
		Pildid
	</td>
	<td class="fcaption2">
		{VAR:imglist}
		| <a href="javascript:iremote({VAR:id})">Kıik</a> | <a href="{VAR:add_img}">Lisa uus&gt;&gt;&gt;</a>
	</td>
</tr>
<tr>
	<td class="fcaption2">Tabelid</td>
	<td class="fcaption2">{VAR:tblist} | <a href="{VAR:addtable}">Lisa uus&gt;&gt;&gt;</a></td>
</tr>
<tr>
	<td class="fcaption2">Failid</td>
	<td class="fcaption2">{VAR:filelist} | <a href="{VAR:addfile}">Lisa uus&gt;&gt;&gt;</a></td>
</tr>
<tr>
	<td class="fcaption2">
		Lingid
	</td>
	<td class="fcaption2">
		{VAR:linklist} |
		<a href="{VAR:addlink}">Lisa uus&gt;&gt;&gt;</a>
	</td>
</tr>
<tr>
	<td class="fcaption2">
		Vormid
	</td>	
	<td class="fcaption2">
	{VAR:formlist}
	</td>
</tr>
<tr>
	<td class="fcaption2">
		Graafikud
	</td>	
	<td class="fcaption2">
	{VAR:graphlist}
	</td>
</tr>
<tr>
	<td class="fcaption2">
		Galeriid
	</td>	
	<td class="fcaption2">
	{VAR:gallist}
	</td>
</tr>
<tr>
<td class="fcaption2">Aliased</td>
<td class="fcaption2">
{VAR:alilist} | <a href="pickobject.{VAR:ext}?docid={VAR:id}&parent=0">Lisa uus objekt (alias) &gt;&gt;</a>
</td>
</tr>
<tr>
<td class="fcaption2" valign="top">Lead</td>
<td class="fform">
<textarea name="lead" cols="80" rows="5">{VAR:lead}</textarea>
</td>
</tr>
<tr>
<td class="fcaption2" valign="top">Sisu</td>
<td class="fform">
<textarea name="content" cols="80" rows="30">{VAR:content}</textarea>
</td>
</tr>
<tr>
<td class="fform" colspan="2">
<input type="submit" value="Salvesta">
{VAR:reforb}
</td>
</tr>
</table>
</form>
