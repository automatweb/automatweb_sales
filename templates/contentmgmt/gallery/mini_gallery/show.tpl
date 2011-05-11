<style type="text/css">
.mgalimg td{
	font-family: Trebuchet MS,Tahoma,sans-serif;
	font-size: 10px;
	color: #000000;
}
</style>

<!-- SUB: PAGESELECTOR -->
Vali lehek&uuml;lg:
<!-- SUB: PAGE -->
<a href='{VAR:page_link}'>{VAR:page_nr}</a>
<!-- END SUB: PAGE -->

<!-- SUB: PAGE_SEL -->
{VAR:page_nr}
<!-- END SUB: PAGE_SEL -->

<!-- SUB: PAGE_SEPARATOR -->
|
<!-- END SUB: PAGE_SEPARATOR -->

<!-- END SUB: PAGESELECTOR -->

<table border="0" cellpadding="0" cellspacing="10" width="100%" >
<!-- SUB: ROW -->
	<tr>
		<!-- SUB: COL -->
		<td valign="top" align="center" class="mgalimg">{VAR:imgcontent}</td>
		<!-- END SUB: COL -->
	</tr>
<!-- END SUB: ROW -->

<!-- SUB: FOLDER_CHANGE -->
<tr>
<td colspan="{VAR:col_count}"><b>{VAR:folder_name}</b></td
>
</tr>
<!-- END SUB: FOLDER_CHANGE -->

</table>



<!-- SUB: IMAGE -->
<img title="{VAR:alt}" alt="{VAR:alt}" src="{VAR:imgref}">
<!-- END SUB: IMAGE -->

<!-- SUB: IMAGE_BIG_LINKED -->
<a {VAR:target} href="{VAR:plink}"><img title="{VAR:alt}" alt="{VAR:alt}" src="{VAR:imgref}"></a>
<!-- END SUB: IMAGE_BIG_LINKED -->

<!-- SUB: IMAGE_HAS_BIG -->
<a href="JavaScript: void(0)" onclick="window.open('{VAR:bi_show_link}','popup','width={VAR:big_width},height={VAR:big_height}');"><img src="{VAR:imgref}" alt="{VAR:alt}" title="{VAR:alt}" border="0"></a>
<!-- END SUB: IMAGE_HAS_BIG -->

<!-- SUB: IMAGE_LINKED -->
<a {VAR:target} href="{VAR:plink}"><img title="{VAR:alt}" alt="{VAR:alt}" src="{VAR:imgref}"></a>
<!-- END SUB: IMAGE_LINKED -->
