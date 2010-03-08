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
<input type="submit" value="Salvesta">
</td>
</tr>
<tr>
<td class="fcaption2">Pealkiri</td>
<td class="fform"><input type="text" name="title" size="80" value="{VAR:title}"></td>
</tr>
<tr>
<td class="fcaption2">Autor</td>
<td class="fform"><input type="text" name="author" size="80" value="{VAR:author}"></td>
</tr>
<tr>
<td class="fcaption2">Fotod</td>
<td class="fform"><input type="text" name="photos" size="80" value="{VAR:photos}"></td>
</tr>
<tr>
<td class="fcaption2" rowspan="2" valign="top">M‰‰rangud</td>
<td class="fform">
	Aktiivne:
	<select name="status">
	{VAR:status}
	</select>
	N‰ita leadi: <input type='checkbox' name='showlead' value=1 {VAR:showlead}> |
	Foorum: <input type='checkbox' NAME='is_forum' VALUE=1 {VAR:is_forum}> 
	</td>
	</tr>
	<tr>
	<td class="fform">
	Esilehel stoori: <input type='checkbox' NAME='esilehel' VALUE=1 {VAR:esilehel}> <select name="jrk1">{VAR:jrk1}</select>|
	Esilehel uudis: <input type='checkbox' NAME='esilehel_uudis' VALUE=1 {VAR:esilehel_uudis}> <select name="jrk2">{VAR:jrk2}</select>|
	Esilehel &uuml;leval: <input type='checkbox' NAME='esileht_yleval' VALUE=1 {VAR:esileht_yleval}> <select name="jrk3">{VAR:jrk3}</select>
	Paremal: <input type='checkbox' NAME='yleval_paremal' VALUE=1 {VAR:yleval_paremal}>
</td>
</tr>
<tr>
	<td class="fcaption2">
		Pildid
	</td>
	<td class="fcaption2">
		{VAR:imglist}
		| <a href="javascript:iremote({VAR:docid})">Kıik</a> | <a href="{VAR:add_image}">Lisa uus&gt;&gt;&gt;</a>
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
<td class="fform" colspan="2">
<input type="submit" value="Salvesta">
{VAR:reforb}
</td>
</tr>
</table>
</form>
