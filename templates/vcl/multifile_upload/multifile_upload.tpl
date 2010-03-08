<style type="text/css">
#files_list input {border: 0; padding: 0; font-size: 10px; color: white; background: #05A6E9; margin-left: 1px;}
#multifile_upload .delete {background: #05A6E9; color: white; border: 0; font-size: 10px; padding: 1px 6px 1px 6px}
#multifile_upload .files {width: 30%;}
</style>

<script src="{VAR:baseurl}/automatweb/js/vcl/multifile.js"></script>

<div id="multifile_upload">
<div id="multifile_upload_form" class="form">
	<input id="my_file_element" type="file" name="file[]">
</div>

<table class="files">
<tbody id="multifile_upload_files_list">
<!-- SUB: file -->
<tr id="multifile_{VAR:id}">
	<td>{VAR:counter}.</td>
	<td><a href="{VAR:file_url}" target="_blank">{VAR:file_name}</a></td>
	<td><input type="button" value="muuda" class="delete" onClick="window.top.location = '{VAR:edit_url}'"> <input type="button" value="kustuta" class="delete" onClick="multifile_delete ('{VAR:id}')"></td></tr>
<!-- END SUB: file -->
</tbody>
</table>

<script>
	var multi_selector = new MultiSelector( document.getElementById( 'multifile_upload_files_list' ), {VAR:max} );
	multi_selector.counter = '{VAR:counter}';
	multi_selector.addElement( document.getElementById( 'my_file_element' ) );
</script>

</div><!-- multifile_upload -->


