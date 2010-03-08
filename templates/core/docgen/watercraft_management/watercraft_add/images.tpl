{VAR:pages}
<!-- SUB: UPLOADED_IMAGES -->
<ul>
	<!-- SUB: UPLOADED_IMAGE -->
	<li><a href="{VAR:image_url}">{VAR:image_name}</a></li>
	<!-- END SUB: UPLOADED_IMAGE -->
</ul>
<!-- END SUB: UPLOADED_IMAGES -->
<form enctype="multipart/form-data" method="post" action="index.aw">
<!--<input type='hidden' NAME='MAX_FILE_SIZE' VALUE='100000000'>-->
	<table style="border: 1px solid blue;">
		<tr>
			<td>Pilt 1</td>
			<td>{VAR:image_upload_1}</td>
		</tr>
		<tr>
			<td>Pilt 2</td>
			<td>{VAR:image_upload_2}</td>
		</tr>
		<tr>
			<td>Pilt 3</td>
			<td>{VAR:image_upload_3}</td>
		</tr>
		<tr>
			<td>Pilt 4</td>
			<td>{VAR:image_upload_4}</td>
		</tr>
		<tr>
			<td>Pilt 5</td>
			<td>{VAR:image_upload_5}</td>
		</tr>
		<tr>
			<td>Pilt 6</td>
			<td>{VAR:image_upload_6}</td>
		</tr>
		<tr>
			<td>Pilt 7</td>
			<td>{VAR:image_upload_7}</td>
		</tr>
		<tr>
			<td>Pilt 8</td>
			<td>{VAR:image_upload_8}</td>
		</tr>
		<tr>
			<td>Pilt 9</td>
			<td>{VAR:image_upload_9}</td>
		</tr>
		<tr>
			<td>Pilt 10</td>
			<td>{VAR:image_upload_10}</td>
		</tr>
	</table>
	<input type="submit" name="prev" value="Tagasi" />
	<input type="submit" name="cancel" value="Katkesta" />
	<input type="submit" name="save" value="Salvesta" />
	<input type="submit" name="next" value="Edasi" />
	{VAR:reforb}
</form>
<!-- SUB: ERROR -->
See v&auml;li on kohustuslik
<!-- END SUB: ERROR -->
