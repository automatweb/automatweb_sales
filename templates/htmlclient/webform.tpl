<form action="{VAR:form_handler}" method="{VAR:method}" enctype="multipart/form-data" name="changeform" id="changeform" {VAR:form_target}>
<table class="webform_form" border="0" cellspacing="0" cellpadding="0">
{VAR:content}

<!-- SUB: ERROR -->
<tr>
	<td colspan="2" class="form_error">{VAR:error_text}</td>
</tr>
<!-- END SUB: ERROR -->

<!-- SUB: PROP_ERR_MSG -->
<tr>
	<td class="webform_caption"></td>
	<td class='webform_errmsg'>{VAR:err_msg}</td>
</tr>	
<!-- END SUB: PROP_ERR_MSG -->

<!-- SUB: LINE -->
<tr>
	<td class='webform_caption'>
	{VAR:caption}
	</td>
	<td class='webform_element'>
	{VAR:element}
	</td>
</tr>
<!-- END SUB: LINE -->
<!-- SUB: HEADER -->
<tr>
	<td class='webform_header' colspan="2">
	{VAR:caption}
	</td>
</tr>
<!-- END SUB: HEADER -->

<!-- SUB: SUB_TITLE -->
<tr>
	<td colspan='2' class='webform_subtitle'>
	{VAR:value}
	</td>
</tr>
<!-- END SUB: SUB_TITLE -->

<!-- SUB: CONTENT -->
<tr>
	<td id="{VAR:cell_id}" colspan='2' class='webform_content'>
	{VAR:value}
	</td>
</tr>
<!-- END SUB: CONTENT -->

<!-- SUB: SUBMIT -->
<tr>
	<td class='webform_content'>
	</td>
	<td class="webfrom_submit_cell">
		<input type="submit" name="{VAR:name}" value="{VAR:sbt_caption}" class="webform_submit" />
	</td>
</tr>
<!-- END SUB: SUBMIT -->

<!-- SUB: SUBITEM -->
	<span style='color: red'>{VAR:err_msg}</span>
        {VAR:element}
        {VAR:caption}
	&nbsp;
<!-- END SUB: SUBITEM -->

<!-- SUB: SUBITEM2 -->
	<span style='color: red'>{VAR:err_msg}</span>
        {VAR:caption}
        {VAR:element}
	&nbsp;
<!-- END SUB: SUBITEM2 -->

{VAR:reforb}
<script type="text/javascript">
function submit_changeform(action)
{
	{VAR:submit_handler}
	if (typeof action == "string" && action.length>0)
	{
		document.changeform.action.value = action;
	};
	document.changeform.submit();
}
</script>
</table>
</form>