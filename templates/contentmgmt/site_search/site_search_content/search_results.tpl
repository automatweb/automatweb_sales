<div style="text-align:left;margin-bottom: 20px;">
<span class="text">
{VAR:PAGESELECTOR}

<hr noshade width="100%" size="1">
{VAR:LC_SEARCH_CONF_SEARCH_SORTING}
<!-- SUB: SORT_MODIFIED -->
<a href='{VAR:sort_modified}'>{VAR:LC_SEARCH_CONF_SEARCH_LAST_MOD}</a> 
<!-- END SUB: SORT_MODIFIED -->

<!-- SUB: SORT_MODIFIED_SEL -->
{VAR:LC_SEARCH_CONF_SEARCH_LAST_MOD}
<!-- END SUB: SORT_MODIFIED_SEL -->

<!-- SUB: SORT_TITLE -->
 | <a href='{VAR:sort_title}'>{VAR:LC_SEARCH_CONF_SORT_TITLE}</a> 
<!-- END SUB: SORT_TITLE -->

<!-- SUB: SORT_TITLE_SEL -->
 | {VAR:LC_SEARCH_CONF_SORT_TITLE}
<!-- END SUB: SORT_TITLE_SEL -->

{VAR:LC_SEARCH_CONF_SEARCH_BY}<br><br>
<br>
<!-- SUB: MATCH -->
<table border=0 cellpadding=0 cellspacing=0 width=100%>
	<tr>
		<td class="text"><a href='{VAR:link}'><b>{VAR:title}</b> </a>&nbsp;-&nbsp;<i>{VAR:modified}</i></td>
	</tr>
	<tr>
		<td class="text">{VAR:content}</td>
	</tr>
</table>
<br>
<!-- END SUB: MATCH -->

<!-- SUB: PAGESELECTOR -->
<hr noshade width="100%" size="1">
<!-- SUB: PREVIOUS -->
<a href='{VAR:prev}'>&lt;&lt;</a> 
<!-- END SUB: PREVIOUS -->

<!-- SUB: PAGE -->
&nbsp;<a href='{VAR:page}'>{VAR:page_from} - {VAR:page_to}</a>&nbsp;
<!-- END SUB: PAGE -->

<!-- SUB: SEL_PAGE -->
&nbsp;{VAR:page_from} - {VAR:page_to}&nbsp;
<!-- END SUB: SEL_PAGE -->

<!-- SUB: NEXT -->
<a href='{VAR:next}'>&gt;&gt;</a> 
<!-- END SUB: NEXT -->
<br>
<!-- END SUB: PAGESELECTOR -->

<!-- END SUB: SEARCH -->

<!-- SUB: NO_RESULTS -->
<span class="text">{VAR:LC_SEARCH_NOT_FOUND}</span>
<!-- END SUB: NO_RESULTS -->

</span>

			</p>
		</td>
	</tr>
</table>
</div>
