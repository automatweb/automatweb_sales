<div class="forum">
<form action="{VAR:baseurl}/reforb.{VAR:ext}" method="post">
<p class="h1">Lisa teema</p>
<table class="add">
<tr>
	<td class="caption {VAR:style_form_text}">Teema:</td>
	<td class="text"><input type="text" name="name" class="text {VAR:style_form_element}" /></td>
</tr>
<tr>
	<td class="caption {VAR:style_form_text}">Autori nimi:</td>
	<!-- SUB: a_name -->
	<td class="text"><input type="text" name="author_name" class="text {VAR:style_form_element}" /></td>
	<!-- END SUB: a_name -->
	<!-- SUB: a_name_logged -->
	<td class="text {VAR:style_form_text}">{VAR:author}</td>
	<!-- END SUB: a_name_logged -->
</tr>
<tr>
	<td class="caption {VAR:style_form_text}">E-mail:</td>
	<!-- SUB: a_email -->
	<td class="text"><input type="text" name="author_email" class="text {VAR:style_form_element}" /></td>
	<!-- END SUB: a_email -->
	<!-- SUB: a_email_logged -->
	<td class="text {VAR:style_form_text}">{VAR:author_email}</td>
	<!-- END SUB: a_email_logged -->
</tr>
<tr>
	<td class="caption {VAR:style_form_text}">Soovin vastuseid e-mailile:</td>
	<td class="text"><input type="checkbox" name="answers_to_mail" value="" class="{VAR:style_form_element}" /></td>
</tr>

<!-- SUB: IMAGE_VERIFICATION -->
  <tr>
	<td class="caption {VAR:style_form_text}">Kontrollnumber:</td>
	<td class="text"><input type="text" class="text" name="ver_code"></td>
</tr>
<tr>
	<td class="{VAR:style_fotm_text}"></td>
	<td class="text"><img src="{VAR:image_verification_url}" width="{VAR:image_verification_width}" height="{VAR:image_verification_height}" alt="" /></td>
</tr>
<!-- END SUB: IMAGE_VERIFICATION -->
<tr>
	<td class="caption {VAR:style_form_text}" colspan="2">Sisu</td>
</tr>
<tr>
	<td class="text" colspan="2"><textarea name="comment" class="{VAR:style_form_element}"></textarea></td>
</tr>
<tr>
	<td class="text" colspan="2"><input type="submit" value="Lisa teema" class="button" /></td>
</tr>
{VAR:reforb}
</table>
</form>
</div>