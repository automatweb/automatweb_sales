<form method="GET" action="{VAR:baseurl}/index{VAR:ext}">
<table border=0 cellpadding=0 cellspacing=0>
<tr>
	<td class="body">Mille hulgast:&nbsp;</td>
	<td class="body" colspan=2><select class="body" name='s_parent'>{VAR:search_sel}</select></td>
</tr>
<tr>
	<td class="body">Pealkiri:</td>
	<td class="body">
		<select class="body" name='t_type'>
			<option {VAR:t_type1} value='1'>m&otilde;ni s&otilde;na
			<option {VAR:t_type2} value='2'>k&otilde;ik s&otilde;nad
			<option {VAR:t_type3} value='3'>fraasi
		</select>
		<input class="body" type="text" NAME="sstring_title" VALUE='{VAR:sstring_title}'>
	</td>
	<td class="body">
<!--		<select class="body" name='t2c_log'>
			<option {VAR:t2c_or} value='OR'>v&otilde;i
			<option {VAR:t2c_and} value='AND'>ja
		</select>-->
	</td>
</tr>

<tr>
	<td class="body">Autor:</td>
	<td class="body">
		<input type="text" NAME="sstring_author" VALUE='{VAR:sstring_author}'>
	</td>
	<td class="body">
<!--		<select class="body" name='a2c_log'>
			<option {VAR:a2c_or} value='OR'>v&otilde;i
			<option {VAR:a2c_and} value='AND'>ja
		</select>-->
	</td>
</tr>

<tr>
	<td >Kuup&auml;ev:</td>
	<td >
		{VAR:date_from} - {VAR:date_to}
	</td>
	<td >
<!--		<select class="body" name='d2k_log'>
			<option {VAR:d2k_or} value='OR'>v&otilde;i
			<option {VAR:d2k_and} value='AND'>ja
		</select>-->
	</td>
</tr>

<tr>
	<td>Sisu:</td>
	<td>
		<select name='c_type'>
			<option {VAR:c_type1} value='1'>m&otilde;ni s&otilde;na
			<option {VAR:c_type2} value='2'>k&otilde;ik s&otilde;nad
			<option {VAR:c_type3} value='3'>fraasi
		</select>
		<input type="text" NAME="sstring" VALUE='{VAR:sstring}'>
	</td>
	<td class="body">
<!--		<select class="body" name='t2c_log'>
			<option {VAR:t2c_or} value='OR'>v&otilde;i
			<option {VAR:t2c_and} value='AND'>ja
		</select>-->
	</td>
</tr>


<tr>
	<td colspan=3 >M&auml;rks&otilde;nad:</td>
</tr>
<tr>
	<td colspan=3 ><select size="10"  MULTIPLE NAME="s_keywords[]">{VAR:keywords}</select></td>
</tr>

<tr>
	<td >Mitu tulemust maksimaalselt:</td>
	<td colspan="2">
		<select NAME="max_results">{VAR:max_results}</select>
	</td>
</tr>

</table>
<br>
<input type="submit" value="OTSI" class="body">
{VAR:reforb}
</form>
<!-- SUB: SEARCH -->
{VAR:PAGESELECTOR}<br><br>

Sorteeri:
<!-- SUB: SORT_MODIFIED -->
<a href='{VAR:sort_modified}'>muutmise</a> 
<!-- END SUB: SORT_MODIFIED -->
<!-- SUB: SORT_MODIFIED_SEL -->
muutmise
<!-- END SUB: SORT_MODIFIED_SEL -->

<!-- SUB: SORT_TITLE -->
 | <a href='{VAR:sort_title}'>pealkirja</a> 
<!-- END SUB: SORT_TITLE -->
<!-- SUB: SORT_TITLE_SEL -->
 | pealkirja
<!-- END SUB: SORT_TITLE_SEL -->

<!-- SUB: SORT_CONTENT -->
 | <a href='{VAR:sort_content}'>sisu</a> 
<!-- END SUB: SORT_CONTENT -->
<!-- SUB: SORT_CONTENT_SEL -->
 | sisu
<!-- END SUB: SORT_CONTENT_SEL -->
j&auml;rgi.<br><br>
<!-- SUB: MATCH -->
<table border=0 cellpadding=0 cellspacing=0 width=100%>
	<tr>
		<td class="sisupealkiri"><a href='{VAR:baseurl}/index{VAR:ext}/section={VAR:section}'><b>{VAR:title}</b> </a>&nbsp;-&nbsp;<i>{VAR:modified}</i></td>
	</tr>
	<tr>
		<td class="body">{VAR:content}</td>
	</tr>
</table>
<br>
<!-- END SUB: MATCH -->

<!-- SUB: PAGESELECTOR -->
<br>
<!-- SUB: PREVIOUS -->
<a href='{VAR:prev}'>Eelmised</a> 
<!-- END SUB: PREVIOUS -->

<!-- SUB: PAGE -->
&nbsp;<a href='{VAR:page}'>{VAR:page_from} - {VAR:page_to}</a>&nbsp;
<!-- END SUB: PAGE -->

<!-- SUB: SEL_PAGE -->
&nbsp;{VAR:page_from} - {VAR:page_to}&nbsp;
<!-- END SUB: SEL_PAGE -->

<!-- SUB: NEXT -->
<a href='{VAR:next}'>J&auml;rgmised</a> 
<!-- END SUB: NEXT -->
<br>
<!-- END SUB: PAGESELECTOR -->

<!-- END SUB: SEARCH -->
</span>

