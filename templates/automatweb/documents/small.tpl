<script language="Javascript">
function iremote(oid) {
 var windowprops = "toolbar=0,location=1,directories=0,status=0, "+
"menubar=0,scrollbars=1,resizable=1,width=400,height=500";

OpenWindow = window.open("imlist.aw?oid=" + oid, "remote", windowprops);
}
</script>
<form method="POST">
<table border=0 cellspacing=1 cellpadding=2 bgcolor="#CCCCCC">
<tr>
<td class="fform" colspan="2">
<input type="submit" value="Salvesta">
</td>
</tr>
<td class="fcaption">Pildid</td>
<td class="fcaption">
{VAR:imglist}
| <a href="javascript:iremote({VAR:docid})">Kõik</a> | <a href="images.aw?action=newpic&docid={VAR:docid}">Lisa uus&gt;&gt;&gt;</a>
</td>
</tr>
<tr>
<td class="fcaption">Tabelid</td>
<td class="fcaption">
{VAR:tblist}
|
<a href="tables.aw?type=newtable&docid={VAR:docid}">Lisa uus&gt;&gt;&gt;</a>
</td>
</tr>
<tr>
<td class="fcaption">Vormid</td>
<td class="fcaption">
{VAR:formlist} | <a href="javascript:alert('FormGen on installeerimata. Ei saa vorme lisada')">Lisa uus&gt;&gt;</a>
</td>
</tr>
<tr>
<td class="fcaption" valign="top">Sisu</td>
<td class="fform">
<textarea name="content" cols="80" rows="30">
{VAR:content}
</textarea>
</td>
</tr>
<tr>
<td class="fform" colspan="2">
<input type="submit" value="Salvesta">
<input type="hidden" name="section" value="{VAR:section}">
<input type="hidden" name="op" value="save">
<input type="hidden" name="docid" value="{VAR:docid}">
</td>
</tr>
</table>
</form>
