<script language="Javascript">
function iremote(oid) {
 var windowprops = "toolbar=0,location=1,directories=0,status=0, "+
"menubar=0,scrollbars=1,resizable=1,width=400,height=500";

OpenWindow = window.open("images{VAR:ext}?type=list&parent=" + oid, "remote", windowprops);
}
</script>
<form method="POST">
<table border=0 cellspacing=1 cellpadding=2 bgcolor="#CCCCCC">
<tr>
<td class="fform" colspan="2">
<input type="submit" value="{VAR:LC_DOCUMENT_SAVE}">
</td>
</tr>
<!-- SUB: location -->
<tr>
<td class="fcaption2">{VAR:LC_DOCUMENT_LOCATION}</td>
<td class="fform">
</td>
</tr>
<!-- END SUB: location -->
<!-- SUB: periood -->
<tr>
<td class="fcaption2">{VAR:LC_DOCUMENT_PERIOD}</td>
<td class="fform">{VAR:periood}</td>
</tr>
<!-- END SUB: periood -->
<!-- SUB: pealkiri -->
<tr>
<td class="fcaption2">{VAR:LC_DOCUMENT_HEADLINE}</td>
<td class="fform"><input type="text" name="title" size="80" value="{VAR:title}"></td>
</tr>
<!-- END SUB: pealkiri -->
<!-- SUB: tekst -->
<tr>
<td class="fcaption2">{VAR:LC_DOCUMENT_TEXT}</td>
<td class="fform"><input type="text" name="author" size="80" value="{VAR:author}"></td>
</tr>
<!-- END SUB: tekst -->
<!-- SUB: fotod -->
<tr>
<td class="fcaption2">{VAR:LC_DOCUMENT_PHOTOS}</td>
<td class="fform"><input type="text" name="photos" size="80" value="{VAR:photos}"></td>
</tr>
<!-- END SUB: fotod -->
<!-- SUB: options -->
<tr>
<td class="fcaption2">{VAR:LC_DOCUMENT_SETTINGS}</td>
<td class="fform">
	{VAR:LC_DOCUMENT_VISIBLE}:
	<select name="visible">
	{VAR:visible}
	</select>
	{VAR:LC_DOCUMENT_ACTIVE}:
	<select name="status">
	{VAR:status}
	</select>
	{VAR:LC_DOCUMENT_FORUM}: <input type='checkbox' NAME='is_forum' VALUE=1 {VAR:is_forum}> 
	| {VAR:LC_DOCUMENT_SHOW_LEAD}: <input type='checkbox' name='showlead' value=1 {VAR:showlead}> |
	{VAR:LC_DOCUMENT_FRONTPAGE}: <input type='checkbox' NAME='esilehel' VALUE=1 {VAR:esilehel}> <select name="jrk1">{VAR:jrk1}</select>|
	{VAR:LC_DOCUMENT_DOWN_RIGHT}: <input type='checkbox' NAME='esilehel_uudis' VALUE=1 {VAR:esilehel_uudis}> <select name="jrk2">{VAR:jrk2}</select>|
</td>
<tr>
	<td class="fcaption2">
		{VAR:LC_DOCUMENT_IMAGES}
	</td>
	<td class="fcaption2">
		{VAR:imglist}
		| <a href="javascript:iremote({VAR:docid})">{VAR:LC_DOCUMENT_ALL}</a> | <a href="images.aw?type=add_image&docid={VAR:docid}">{VAR:LC_DOCUMENT_ADD_NEW}&gt;&gt;&gt;</a>
	</td>
</tr>
<!-- END SUB: options -->
<!-- SUB: lead -->
<tr>
<td class="fcaption2" valign="top">Lead</td>
<td class="fform">
<textarea name="lead" cols="80" rows="5">
{VAR:lead}
</textarea>
</td>
</tr>
<!-- END SUB: lead -->
<!-- SUB: content -->
<tr>
<td class="fcaption2" valign="top">{VAR:LC_DOCUMENT_CONTENT}</td>
<td class="fform">
<textarea name="content" cols="80" rows="30">
{VAR:content}
</textarea>
</td>
</tr>
<!-- END SUB: content -->
<!-- SUB: keywords -->
<tr>
<td class="fcaption2" valign="top">{VAR:LC_DOCUMENT_KEYWORDS}</td>
<td class="fform">
<input type="text" name="keywords" size="70" value="{VAR:keywords}">
</td>
</tr>
<!-- END SUB: keywords -->
<tr>
<td class="fform" colspan="2">
<input type="submit" value="{VAR:LC_DOCUMENT_SAVE}">
<input type="hidden" name="op" value="save">
<input type="hidden" name="docid" value="{VAR:docid}">
</td>
</tr>
</table>
</form>
