<style>
body {
	font-family: Arial,sans-serif;
	font-size: 13px;
}
</style>
<div style="background-color: #EEE; font-size: 14px; font-weight: bold; font-family: Arial,sans-serif; padding: 5px;">{VAR:groupname}</div>
<form id="changeform" method="POST" action="{VAR:baseurl}/reforb{VAR:ext}">
<table border='1' width='100%'>
<!-- SUB: SUB_TRANSLATE -->
<tr>
<td colspan='2' bgcolor='#eeeeee'><strong>{VAR:lang_name}</strong></td>
</tr>
<td>Nimi</td>
<td>
<input type="text" name="caption[{VAR:group_id}]" value="{VAR:group_lang_name}" size="40">
</td>
</tr>
<tr>
<td>Kommentaar</td>
<td><textarea name="comment[{VAR:group_id}]" cols="40">{VAR:group_lang_comment}</textarea></td>
</tr>
<tr>
<td>Abitekst</td>
<td><textarea name="help[{VAR:group_id}]" cols="40">{VAR:group_lang_help}</textarea></td>
</tr>
<!-- END SUB: SUB_TRANSLATE -->
</table>
{VAR:reforb}
</form>
<script type="text/javascript">
function submit_changeform() 
{
	document.getElementById('changeform').submit();
}
</script>
