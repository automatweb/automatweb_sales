<style>
body {
	font-family: Arial,sans-serif;
	font-size: 13px;
}
.head
{
	font-family:Arial;
	font-size:12px;
	font-weight:bold;
}
.filename
{
	font-family:Arial;
	font-size:12px;
	color:gray;
}
</style>

<form id="changeform" method="POST" action="{VAR:baseurl}/reforb{VAR:ext}">
<div style="background-color: #EEE; font-size: 14px; font-weight: bold; font-family: Arial,sans-serif; padding: 5px;">{VAR:failid}</div>

<table border='0'>
<!-- SUB: SUB_TRANSLATE -->
<tr><td class="filename">{VAR:filename}</td></tr>
<!-- END SUB: SUB_TRANSLATE -->
<!-- SUB: TAREA -->
<tr><td class="head">{VAR:seletus}</td></tr>

<tr><td>
	<textarea name="seletus" cols="40" rows="10"></textarea>
</td></tr>
<!-- END SUB: TAREA -->

<tr><td>{VAR:commit_link}<td></tr>
</table>
{VAR:reforb}
</form>
<script type="text/javascript">
function submit_changeform() 
{
	document.getElementById('changeform').submit();
}
</script>
