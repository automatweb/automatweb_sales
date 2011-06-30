<div class="forum">
<p class="h1">Lisa kommentaar</p>
<table class="add">
<!-- SUB: ERROR -->
<tr>
<td class="error" colspan="2">{VAR:error_message}</td>
</tr>
<!-- END SUB: ERROR -->
<tr>
<td class="caption {VAR:style_form_text}">Pealkiri:</td>
<td class="text {VAR:style_form_text}"><input type="text" name="name" value="{VAR:title}" size="70" class="text {VAR:style_form_element}" /></td>
</tr>
<tr>
	<td class="caption {VAR:style_form_text}">Autori nimi:</td>
	<!-- SUB: a_name -->
	<td class="text {VAR:style_form_text}">{VAR:author}</td>
	<!-- END SUB: a_name -->
	<!-- SUB: a_name_logged -->
	<td class="text"><input type="text" name="uname" value="{VAR:author}" class="text {VAR:style_form_element}" /></td>
	<!-- END SUB: a_name_logged -->
</tr>
<tr>
	<td class="caption {VAR:style_form_text}">Autori e-mail:</td>
	<!-- SUB: a_email -->
	<td class="text {VAR:style_form_text}">{VAR:author_email}</td>
	<!-- END SUB: a_email -->
	<!-- SUB: a_email_logged -->
	<td class="text"><input type="text" name="uemail" value="{VAR:author_email}" class="text {VAR:style_form_element}" /></td>
	<!-- END SUB: a_email_logged -->
</tr>
<!-- SUB: IMAGE_UPLOAD_FIELD -->
<tr>
	<td class="caption {VAR:style_form_text}">Pilt:</td>
	<td class="text"><input type="file" name="uimage" class="text {VAR:style_form_element}" /></td>
</tr>
<!-- END SUB: IMAGE_UPLOAD_FIELD -->
<!-- SUB: IMAGE_VERIFICATION -->
  <tr>
	<td class="caption">Kontrollnumber:</td>
	<td class="text"><input type="text" name="ver_code" class="text" /></td>
</tr>
<tr>
	<td class="caption {VAR:style_fotm_text}"></td>
	<td class="text"><img src="{VAR:image_verification_url}" width="{VAR:image_verification_width}" height="{VAR:image_verification_height}" /></td>
</tr>
<!-- END SUB: IMAGE_VERIFICATION -->
<tr>
	<td class="text {VAR:style_form_text}" colspan="2"><textarea name="commtext" class="{VAR:style_form_element}" rows="10" cols="70">{VAR:commtext}</textarea></td>
</tr>
<tr>
	<td class="text {VAR:style_form_text}" colspan="2"><input type="submit" value="Lisa kommentaar" class="button" /></td>
</tr>
</table>
</div>
