<style type="text/css">
#file_upload_box{
	text-align: left;
	width: 100%;
	border: 1px solid silver;
	background: white;
	margin-top: 10;
}

</style>
<div id="file_upload_box" class="text">
<span style="margin: 10px; margin-left: 20px;">{VAR:file_csv_help}.</span>
<form method="post" action="index.aw" enctype='multipart/form-data'>
<input type="file" name="csv_file" id="csv_file" style="margin: 3px; margin-left: 20px;" class="formBox" size="28"><br>
{VAR:reforb}
<input class="formButton" type="submit" value="{VAR:file_csv_help_save}" style="margin: 10px; margin-left: 20px;">
</div>
</form>
