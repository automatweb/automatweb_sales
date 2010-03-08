<style>
body {
	font-family: Arial,sans-serif;
	font-size: 13px;
}
</style>
<div style="background-color: #EEE; font-size: 14px; font-weight: bold; font-family: Arial,sans-serif; padding: 5px;">{VAR:title}</div>
<form id="changeform" method="POST" action="{VAR:baseurl}/reforb.{VAR:ext}">
<table border='1' width='100%'>
<!-- SUB: SUB_TRANSLATE -->
<tr>
<td colspan='2' bgcolor='#eeeeee'><strong>{VAR:lang_name}</strong></td>
</tr>
<td>Nimi</td>
<td>
<input type="text" name="vars[{VAR:lang_short}][caption]" value="{VAR:lang_caption}" size="40">
</td>
</tr>
<!-- END SUB: SUB_TRANSLATE -->
</table>
<input type="hidden" name="caption" value="{VAR:caption}">
{VAR:reforb}
</form>
<script type="text/javascript">
function submit_changeform() 
{
	document.getElementById('changeform').submit();
}
</script>
