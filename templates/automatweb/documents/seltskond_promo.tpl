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
<td class="fcaption2">Periood</td>
<td class="fform">{VAR:periood}</td>
</tr>
<tr>
<td class="fcaption2">Pealkiri</td>
<td class="fform"><input type="text" name="title" size="80" value="{VAR:title}"></td>
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
		| <a href="javascript:iremote({VAR:docid})">Kıik</a> | <a href="{VAR:add_image}">Lisa uus&gt;&gt;&gt;</a>
	</td>
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
<td class="fcaption2">Aliased</td>
<td class="fcaption2">
{VAR:alilist} | <a href="pickobject.{VAR:ext}?docid={VAR:docid}&parent=0">Lisa uus objekt (alias) &gt;&gt;</a>
</td>
<tr>
<td class="fcaption2" valign="top">Sisu</td>
<td class="fform">
<textarea name="lead" cols="40" rows="10">
{VAR:lead}
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
