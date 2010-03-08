<div class="text">Otsisid '<b>{VAR:str}</b>', Leiti {VAR:count} dokument(i).<br>

<span class="text">
{VAR:PAGESELECTOR}

<!--{VAR:LC_SEARCH_SORT}
<!-- SUB: SORT_MODIFIED -->
<a href='{VAR:sort_modified}'>{VAR:LC_SEARCH_SORT_BY_MOD} </a> 
<!-- END SUB: SORT_MODIFIED -->

<!-- SUB: SORT_MODIFIED_SEL -->
 {VAR:LC_SEARCH_SORT_BY_MOD}
<!-- END SUB: SORT_MODIFIED_SEL -->

<!-- SUB: SORT_TITLE -->
 | <a href='{VAR:sort_title}'>{VAR:LC_SEARCH_SORT_BY_TITLE}</a> 
<!-- END SUB: SORT_TITLE -->

<!-- SUB: SORT_TITLE_SEL -->
 | {VAR:LC_SEARCH_SORT_BY_TITLE}
<!-- END SUB: SORT_TITLE_SEL -->

{VAR:LC_SEARCH_SORT2}<br><br>-->
<br>
<!-- SUB: MATCH -->
<table border=0 cellpadding=0 cellspacing=0 width=100%>
	<tr>
		<td class="text"><a href='{VAR:link}'><b>{VAR:title}</b> </a><!--&nbsp;-&nbsp;<i>{VAR:modified}</i>--></td>
	</tr>
	<tr>
		<td class="text">{VAR:lead}</td>
	</tr>
</table>
<br>
<!-- END SUB: MATCH -->

<!-- SUB: PAGESELECTOR -->
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

