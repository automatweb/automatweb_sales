<script language="Javascript">
function iremote(oid) {
 var windowprops = "toolbar=0,location=1,directories=0,status=0, "+
"menubar=0,scrollbars=1,resizable=1,width=400,height=500";

OpenWindow = window.open("images.{VAR:ext}?type=list&parent=" + oid, "remote", windowprops);
}
function remote2(url) {
OpenWindow = window.open(url);
}

</script>
<form method="POST" action="reforb.{VAR:ext}" name="doc">
<br>
<table border=0 cellspacing=1 cellpadding=2 bgcolor="#CCCCCC" width=100%>
<tr>
<td class="hele_hall_taust" colspan="2">
<input type="submit" class='doc_button' value="{VAR:LC_DOCUMENT_CHANGE}" onClick="window.location.href='{VAR:change}';return false;"> <input class='doc_button' type="submit" value="{VAR:LC_DOCUMENT_PREVIEW}" onClick="window.location.href='{VAR:preview}';return false;"> <input type="submit" class='doc_button' value="{VAR:LC_DOCUMENT_SECTIONS}" onClick="window.location.href='{VAR:menurl}';return false;"> <input type="submit" class='doc_button' value="{VAR:LC_DOCUMENT_TO_WEB}" onClick="remote2('{VAR:weburl}');return false;"> <input type="submit" class='doc_button' value="{VAR:LC_DOCUMENT_OTHER_LANG}" onClick="window.location.href='{VAR:lburl}';return false"> 
</td>
</tr>
<tr>
<td>
<form action="reforb.{VAR:ext}" method=post>
<table border=0 cellspacing=1 cellpadding=1 width=600>
	<tr>
		<td class="title">&nbsp;</td>
		<td class="fgtext_g"><select MULTIPLE SIZE=20 name="sections[]"><option value=''>{VAR:sections}</select></td>
	</tr>
	<tr>
		<td class="title" width=10% nowrap>&nbsp;</td>
		<td class="fgtext_g"><input type='submit' class='small_button' value='{VAR:LC_DOCUMENT_SAVE}'></td>
	</tr>
</table>
{VAR:reforb}
</form></td>
</tr>
</table>

