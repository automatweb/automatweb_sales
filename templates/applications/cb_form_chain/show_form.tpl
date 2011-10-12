<span style="font-family: Verdana, Arial, sans-serif; font-size: 11px; color:#000000;">
<!-- SUB: TITLE -->
<a href='{VAR:title_url}'>{VAR:title}</a>
<!-- END SUB: TITLE -->

<!-- SUB: TITLE_SEL -->
<b>{VAR:title}</b>
<!-- END SUB: TITLE_SEL -->

<!-- SUB: TITLE_SEP -->
|
<!-- END SUB: TITLE_SEP -->

<!-- SUB: DATA_TABLE -->
<table border="1">
	<tr>
		<!-- SUB: DT_HEADER -->
		<td><span style="font-family: Verdana, Arial, sans-serif; font-size: 11px; color:#000000;">{VAR:col_name}</span>&nbsp;</td>
		<!-- END SUB: DT_HEADER -->
	</tr>

	<!-- SUB: DT_ROW -->
	<tr>
		<!-- SUB: DT_COL -->
		<td><span style="font-family: Verdana, Arial, sans-serif; font-size: 11px; color:#000000;">{VAR:content}</span>&nbsp;</td>
		<!-- END SUB: DT_COL -->
	</tr>
	<!-- END SUB: DT_ROW -->
</table>

<!-- SUB: DT_CHANGE_COL -->
<td><a href='{VAR:change_url}'>Muuda</a></td>
<!-- END SUB: DT_CHANGE_COL -->

<!-- SUB: DT_DEL_COL -->
<td><a href='{VAR:del_url}'>Kustuta</a></td>
<!-- END SUB: DT_DEL_COL -->

<!-- END SUB: DATA_TABLE -->


<table border="0" align="center" width="100%">
<form action="orb{VAR:ext}" method="POST" name="changeform" {VAR:form_target}>
{VAR:form}
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

<!-- SUB: PREV_PAGE -->
<input type="submit" name="goto_prev" value="<< Tagasi">
<!-- END SUB: PREV_PAGE -->

<!-- SUB: SAVE_BUTTON -->
<input type="submit" value="Salvesta">
<!-- END SUB: SAVE_BUTTON -->

<!-- SUB: NEXT_PAGE -->
<input type="submit" name="goto_next" value="Edasi >>">
<!-- END SUB: NEXT_PAGE -->

<!-- SUB: CONFIRM -->
<input type="submit" value="Saada" name="confirm">
<!-- END SUB: CONFIRM -->
</form>


<!-- SUB: FORM_HEADER -->
<tr>
	<td colspan="2"><span style="font-family: Verdana, Arial, sans-serif; font-size: 11px; color:#000000;">{VAR:form_name}</span></td>
</tr>
<!-- END SUB: FORM_HEADER -->

<!-- SUB: TABLE_FORM -->
<tr><td colspan="2">
<table border="1">
	<tr>
	<!-- SUB: HEADER -->
		<td class="aw04contentcellleft">{VAR:caption}</td>
	<!-- END SUB: HEADER -->
	</tr>

	<!-- SUB: FORM -->
	<tr>
		<!-- SUB: ELEMENT -->
		<td class="aw04contentcellleft">{VAR:element}</td>
		<!-- END SUB: ELEMENT -->
	</tr>
	<!-- END SUB: FORM -->

</table>
</td></tr>
<!-- END SUB: TABLE_FORM -->
<br>
<b>{VAR:gen_ctr_res}</b>
