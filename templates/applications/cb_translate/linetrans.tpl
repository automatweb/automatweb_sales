<style>
body {
	font-family: Arial,sans-serif;
	font-size: 13px;
}
</style>
<div style="background-color: #EEE; font-size: 14px; font-weight: bold; font-family: Arial,sans-serif; padding: 5px;">{VAR:title}</div>
<form id="changeform" method="POST" action="{VAR:baseurl}/reforb.{VAR:ext}">
<table border='1' width='100%'>
{VAR:contents}
<!-- SUB: SUB_TRANSLATE -->
<tr><td style="font-weight:bold;font-size:14px;color:red;">{VAR:text}</td></tr>
<!-- END SUB: SUB_TRANSLATE -->
<!-- SUB: LANG_TRANSLATE -->
<tr>
<td bgcolor="#eeeeee"><strong>{VAR:lang_name}</strong></td>
</tr>
<tr>
<td>
<textarea name="vars[{VAR:lang_short}][{VAR:text}]" rows="3" cols="60">{VAR:lang_caption}</textarea>
</td>
</tr>
<!-- END SUB: LANG_TRANSLATE -->
</table>
{VAR:reforb}
</form>
<script type="text/javascript">
function submit_changeform() 
{
	document.getElementById('changeform').submit();
}
</script>
