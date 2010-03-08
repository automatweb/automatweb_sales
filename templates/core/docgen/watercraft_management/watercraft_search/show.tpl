<h1>{VAR:name}</h1>
<!-- SUB: SEARCH_FORM_BOX -->
<table>
{VAR:search_form}
</table>
<!-- END SUB: SEARCH_FORM_BOX -->

<!-- SUB: SEARCH_RESULTS -->
<h1>Otsingu tulemused</h1>
<table>
	<tr>
		<th>Nimi</th>
		<th>Hind</th>
		<th>Pikkus</th>
	</tr>
	<!-- SUB: SEARCH_RESULT_ITEM -->
	<tr>
		<td><a href="{VAR:watercraft_view_url}">{VAR:watercraft_name}</a></td>
		<td>{VAR:watercraft_price}</td>
		<td>{VAR:watercraft_lenght}</td>
	</tr>
	<!-- END SUB: SEARCH_RESULT_ITEM -->
</table>
<!-- END SUB: SEARCH_RESULTS -->
<!-- SUB: PAGES -->
<p>
	<!-- SUB: PREV_PAGE -->
		<a href="{VAR:prev_page_url}">Eelmine leht</a>
	<!-- END SUB: PREV_PAGE -->

	<!-- SUB: PAGE -->
		<a href="{VAR:page_url}">{VAR:page_num}</a>
	<!-- END SUB: PAGE -->

	<!-- SUB: SEL_PAGE -->
		<strong><a href="{VAR:page_url}">{VAR:page_num}</a></strong>
	<!-- END SUB: SEL_PAGE -->

	<!-- SUB: NEXT_PAGE -->
		<a href="{VAR:next_page_url}">J&auml;rgmine leht</a>
	<!-- END SUB: NEXT_PAGE -->
</p>
<!-- END SUB: PAGES -->
