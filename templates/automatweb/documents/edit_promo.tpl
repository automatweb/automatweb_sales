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
<input type="submit" value="{VAR:LC_DOCUMENT_SAVE}">
</td>
</tr>
<tr>
<td class="fcaption2">{VAR:LC_DOCUMENT_PERIOD}</td>
<td class="fform">{VAR:periood}</td>
</tr>
<tr>
<td class="fcaption2">{VAR:LC_DOCUMENT_HEADLINE}</td>
<td class="fform"><input type="text" name="title" size="80" value="{VAR:title}"></td>
</tr>
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
</td>
<tr>
	<td class="fcaption2">
		{VAR:LC_DOCUMENT_IMAGES}
	</td>
	<td class="fcaption2">
		{VAR:imglist}
		| <a href="javascript:iremote({VAR:docid})">{LC_DOCUMENT_ALL}</a> | <a href="{VAR:add_image}">{VAR:LC_DOCUMENT_ADD_NEW}&gt;&gt;&gt;</a>
	</td>
</tr>
<tr>
	<td class="fcaption2">
		{VAR:LC_DOCUMENT_LINKS}
	</td>
	<td class="fcaption2">
		{VAR:linklist} |
		<a href="links.{VAR:ext}?op=addform&docid={VAR:docid}">{VAR:LC_DOCUMENT_ADD_NEW}&gt;&gt;&gt;</a>
	</td>
</tr>
<tr> 
  <td class="fcaption2">
    {VAR:LC_DOCUMENT_FORMS}
  </td>    
  <td class="fcaption2">
  {VAR:formlist}
  </td>
</tr>
<tr>
<td class="fcaption2">{VAR:LC_DOCUMENT_ALIASES}</td>
<td class="fcaption2">
{VAR:alilist} | <a href="pickobject.{VAR:ext}?docid={VAR:docid}&parent=0">{VAR:LC_DOCUMENT_ADD_NEW} (alias) &gt;&gt;</a>
</td>
<tr>
<td class="fcaption2" valign="top">{VAR:LC_DOCUMENT_CONTENT}</td>
<td class="fform">
<textarea name="lead" cols="40" rows="10">
{VAR:lead}
</textarea>
</td>
</tr>
<tr>
<td class="fform" colspan="2">
<input type="submit" value="{VAR:LC_DOCUMENT_SAVE}">
{VAR:reforb}
</td>
</tr>
</table>
</form>
