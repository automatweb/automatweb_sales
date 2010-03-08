<!-- SUB: RE_SEARCH_FORM -->
<form name="searchform" method="get" action="">
<table cellspacing="0" cellpadding="0">
<tr>
	<td class="txt10px"><b class="colhead">{VAR:caption_ci}</b></td>
	<td class="txt10px"><b class="colhead">{VAR:caption_tt}</b></td>
	<td class="txt10px"><b class="colhead">{VAR:caption_a1}</b></td>
	<td class="txt10px"><b class="colhead">{VAR:caption_a2}</b></td>
</tr>
<tr>
	<td class="txt10px">{VAR:form_ci}</td>
	<td class="txt10px">{VAR:form_tt}</td>
	<td class="txt10px">{VAR:form_a1}</td>
	<td class="txt10px">{VAR:form_a2}</td>
</tr>
<tr>
	<td colspan="4"></td>
</tr>
<tr>
	<td class="txt10px">{VAR:form_agent}</td>
	<td class="txt10px"><b class="colhead">{VAR:caption_tfamin} {VAR:caption_tfamax}</b></td>
	<td class="txt10px"><b class="colhead">{VAR:caption_tpmin} {VAR:caption_tpmax}</b></td>
	<td class="txt10px"><b class="colhead">{VAR:caption_a3}</b></td>
</tr>
<tr>
	<td class="txt10px" colspan="2" style="white-space: nowrap;">{VAR:form_tfamin} {VAR:form_tfamax}</td>
	<td class="txt10px" style="white-space: nowrap;">{VAR:form_tpmin} {VAR:form_tpmax}</td>
	<td class="txt10px">{VAR:form_a3}</td>
</tr>
<tr>
	<td class="txt10px"><b class="colhead">{VAR:caption_nor}</b></td>
	<td class="txt10px"><b class="colhead">{VAR:caption_fd}</b></td>
	<td class="txt10px"><b class="colhead">{VAR:caption_up}</b></td>
	<td class="txt10px"><b class="colhead">{VAR:caption_c}</b></td>
</tr>
<tr>
	<td class="txt10px">{VAR:form_nor}</td>
	<td class="txt10px" style="white-space: nowrap;">{VAR:form_fd}</td>
	<td class="txt10px">{VAR:form_up}</td>
	<td class="txt10px">{VAR:form_c}</td>
</tr>
<tr>
	<td colspan="4" class="txt10px" align="right">
	<input type="hidden" name="realestate_srch" value="1" />
	<input type="submit" value="Otsi" style="display: {VAR:buttondisplay}" />
	</td>
</tr>
</table>
</form>
<!-- END SUB: RE_SEARCH_FORM -->

<style type="text/css">
{VAR:table_style}
</style>

{VAR:result}
