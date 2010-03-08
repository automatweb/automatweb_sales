<form action="{VAR:form_handler}" name="changeform" id="changeform" method="{VAR:method}" enctype="multipart/form-data" {VAR:form_target}>
<table class="{VAR:webform_form}">
{VAR:content}

<!-- SUB: ERROR -->
<tr>
	<td colspan="2" class="{VAR:form_error}">{VAR:error_text}</td>
</tr>
<!-- END SUB: ERROR -->

<!-- SUB: PROP_ERR_MSG -->
<tr>
	<td class="{VAR:webform_caption}"></td>
	<td class="{VAR:webform_errmsg}">{VAR:err_msg}</td>
</tr>	
<!-- END SUB: PROP_ERR_MSG -->

<!-- SUB: LINE -->
<tr>
	<td class="{VAR:webform_caption}">
	{VAR:caption}
	</td>
	<td class="{VAR:webform_element}">
	{VAR:element}
	</td>
</tr>
<!-- END SUB: LINE -->
<!-- SUB: LINE_TOP -->
<tr>
	<td class="{VAR:webform_caption}" colspan="2">
	{VAR:caption}
	</td>
</tr>
<tr>
	<td class="{VAR:webform_element}" colspan="2">
	{VAR:element}
	</td>
</tr>
<!-- END SUB: LINE_TOP -->
<!-- SUB: LINE_BOTTOM -->
<tr>
	<td class="{VAR:webform_caption}" colspan="2">
	{VAR:element}
	</td>
</tr>
<tr>
	<td class="{VAR:webform_element}" colspan="2">
	{VAR:caption}
	</td>
</tr>
<!-- END SUB: LINE_BOTTOM -->
<!-- SUB: LINE_RIGHT -->
<tr>
	<td class="{VAR:webform_element}">
	{VAR:element}
	</td>
	<td class="{VAR:webform_caption}">
	{VAR:caption}
	</td>
</tr>
<!-- END SUB: LINE_RIGHT -->
<!-- SUB: LINE_IN -->
<tr>
<td colspan="2" class="{VAR:webform_element}">{VAR:element}</td>
</tr>
<!-- END SUB: LINE_IN -->
<!-- SUB: HEADER -->
<tr>
	<td class="{VAR:webform_header}" colspan="2">
	{VAR:caption}
	</td>
</tr>
<!-- END SUB: HEADER -->

<!-- SUB: SUB_TITLE -->
<tr>
	<td colspan="2" class="{VAR:webform_subtitle}">
	{VAR:value}
	</td>
</tr>
<!-- END SUB: SUB_TITLE -->

<!-- SUB: CONTENT -->
<tr>
	<td colspan="2" class="{VAR:webform_content}">
	{VAR:value}
	</td>
</tr>
<!-- END SUB: CONTENT -->

<!-- SUB: SUBMIT -->
<tr>
	<td class="{VAR:webform_element}" colspan="2">
		<input type="submit" name="{VAR:name}" value="{VAR:sbt_caption}" class="{VAR:webform_element}" onclick="self.disabled=true;submit_changeform(''); return false;">
	</td>
</tr>
<!-- END SUB: SUBMIT -->

<!-- SUB: SUBMIT_RIGHT -->
<tr>
	<td class="{VAR:webform_caption}">
	</td>
	<td class="{VAR:webform_element}">
		<input type="submit" name="{VAR:name}" value="{VAR:sbt_caption}" class="{VAR:webform_element}" onclick="submit_changeform();return false;">
	</td>
</tr>
<!-- END SUB: SUBMIT_RIGHT -->

<!-- SUB: SUBITEM -->
<span style="padding-left: {VAR:space}px;">
	{VAR:element}
	{VAR:caption}
</span>
<!-- END SUB: SUBITEM -->

<!-- SUB: SUBITEM2 -->
<span style="padding-left: {VAR:space}px;">
	{VAR:caption}
	{VAR:element}
</span>
<!-- END SUB: SUBITEM2 -->

<!-- SUB: GRIDITEM -->
	<div  class="{VAR:webform_caption}">{VAR:caption} {VAR:element}</div>
<!-- END SUB: GRIDITEM -->

<!-- SUB: GRIDITEM -->
	<div class="{VAR:webform_caption}">
	<!-- SUB: CAPTION_TOP -->
	{VAR:caption}<br/>
	<!-- END SUB: CAPTION_TOP -->
	<!-- SUB: CAPTION_LEFT -->
	{VAR:caption}
	<!-- END SUB: CAPTION_LEFT -->
	{VAR:element}</div>
<!-- END SUB: GRIDITEM -->

<!-- SUB: GRIDITEM_NO_CAPTION -->
	<div  class="{VAR:webform_caption}">{VAR:element}</div>
<!-- END SUB: GRIDITEM_NO_CAPTION -->

<!-- SUB: GRID_HBOX -->
<!-- hbox -->
<table border="0" cellspacing="0" cellpadding="0" width='100%'>
<tr>
<!-- SUB: GRID_HBOX_ITEM -->
<td valign='top' {VAR:item_width} style="padding-left: 5px;">
{VAR:item}
</td>
<!-- END SUB: GRID_HBOX_ITEM -->
</tr>
</table>
<!-- END SUB: GRID_HBOX -->

<!-- SUB: GRID_HBOX_OUTER -->
<!-- SUB: GRID_HBOX -->
<!-- hbox -->
<table border="0" cellspacing="0" cellpadding="0" width='100%'>
<tr>
<!-- SUB: GRID_HBOX_ITEM -->
<td valign='top' {VAR:item_width} style="padding-left: 5px;">
{VAR:item}
</td>
<!-- END SUB: GRID_HBOX_ITEM -->
</tr>
</table>
<!-- END SUB: GRID_HBOX -->
<!-- END SUB: GRID_HBOX_OUTER -->

<!-- SUB: GRID_VBOX -->
<!-- vbox -->
<table border="0" cellspacing="0" cellpadding="0" width="100%">
<!-- SUB: GRID_VBOX_ITEM -->
<tr>
<td valign="top">{VAR:item}</td>
</tr>
<!-- END SUB: GRID_VBOX_ITEM -->
</table>
<!-- END SUB: GRID_VBOX -->

{VAR:reforb}
<script type="text/javascript">
function submit_changeform()
{
	document.changeform.submit();
}
</script>
</table>
</form>
