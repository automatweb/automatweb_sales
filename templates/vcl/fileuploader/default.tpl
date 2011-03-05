<script type="text/javascript">
$(document).ready(function() {
	$('#{VAR:name}AWFileupload').uploadify({
		'uploader'  : '/automatweb/js/jquery/plugins/uploadify/uploadify.swf',
		'script'    : '{VAR:upload_handler}',
		'scriptData'  : {VAR:params},
		'cancelImg' : '/automatweb/js/jquery/plugins/uploadify/cancel.png',
		'buttonText'    : '{VAR:LC_FILEUPLOADER_BROWSE}',
		'completedText' : '{VAR:LC_FILEUPLOADER_COMPLETED}',
		'errorText' : '{VAR:LC_FILEUPLOADER_ERROR}',
		'buttonText'    : '{VAR:LC_FILEUPLOADER_BROWSE}',
		'buttonText'    : '{VAR:LC_FILEUPLOADER_BROWSE}',
		'fileDataName' : '{VAR:fileupload_data_ref}',
		'removeCompleted' : false,
		'onComplete'  : function(event, ID, fileObj, response, data) {
			document.getElementById('{VAR:name}').value = response;
		},
		'auto'      : false
	});
});
</script>

<input id="{VAR:name}AWFileupload" name="{VAR:name}AWFileupload" type="file" />
<input id="{VAR:name}" name="{VAR:name}" type="hidden" />
<a href="javascript:$('#{VAR:name}AWFileupload').uploadifyUpload()">{VAR:LC_FILEUPLOADER_START}</a> | <a href="javascript:$('#{VAR:name}AWFileupload').uploadifyClearQueue()">{VAR:LC_FILEUPLOADER_CLEAR}</a>
