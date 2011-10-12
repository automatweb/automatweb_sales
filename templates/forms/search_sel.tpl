



<script language="javascript">
function ssrch()
{
/*	document.selsrch.elements[2].disabled=true;
	if (document.selsrch.form_op)
	{
		document.selsrch.form_op.disabled=true;
	}
	document.selsrch.search_chain.disabled=true;
	if (document.selsrch.chain_op)
	{
		document.selsrch.chain_op.disabled=true;
	}

	if (document.selsrch.search_from[0].checked)
	{
		document.selsrch.elements[2].disabled=false;
		if (document.selsrch.form_op)
		{
			document.selsrch.form_op.disabled=false;
		}
	}
	else
	if (document.selsrch.search_from[1].checked)
	{
		document.selsrch.search_chain.disabled=false;
		if (document.selsrch.chain_op)
		{
			document.selsrch.chain_op.disabled=false;
		}
	}*/
}
</script>

<font color="red">{VAR:status_msg}</font>
<form action='reforb{VAR:ext}' method="POST" name='selsrch'>
{VAR:LC_FORMS_USE_NEW_SEARCH}: <input type="checkbox" name="use_new_search" value="1" {VAR:use_new_search}><br>
Kas tulemustes on link csv failile: <input type="checkbox" name="show_csv_link" value="1" {VAR:show_csv_link}><br>
N&auml;ita otsingutulemusi sisestusvormidena: <input type='checkbox' value='1' name='show_s_res_as_forms' {VAR:show_s_res_as_forms}>
<table bgcolor="#CCCCCC" cellpadding=3 cellspacing=1 border=0>
	<tr>
		<td class="plain" width="1"><input type='radio' name='search_from' value='forms' {VAR:forms_search} onClick="ssrch();return true;"></td>
		<td class="plain" colspan="3">&nbsp;&nbsp;{VAR:LC_FORMS_SEARCH_FROM_FORMAS}</td>
	</tr>
	<tr>
		<td class="plain"  width="1">&nbsp;</td>
		<td class="plain">
			{VAR:LC_FORMS_CHOOSE_FORMS_WH_SEARCH}:<br>
			<select name='forms[]' size="10" class='small_button' multiple>{VAR:forms}</select>
		</td>
		<td class="plain" valign="top" colspan="2">
			<!-- SUB: FORM_SEL -->
				{VAR:LC_FORMS_CHOOSE_OUTPUT_WH_DP_RESULTS}:<br>
				<select name='form_op' class='small_button'>{VAR:form_op}</select>
			<!-- END SUB: FORM_SEL -->
    &nbsp;</td>
	</tr>
	<tr>
		<td class="plain"><input type='radio' name='search_from' value='chain' {VAR:chain_search} onClick="ssrch();return true;"></td>
		<td class="plain" colspan="3">&nbsp;&nbsp;{VAR:LC_FORMS_SEARCH_FROM_FORMCHAINS}</td>
	</tr>
	<tr>
		<td class="plain"  width="1">&nbsp;</td>
		<td class="plain">
			{VAR:LC_FORMS_CHOOSE_CHAIN_WH_SEARCH}:<br>
			<select name='search_chain' class='small_button'>{VAR:nchains}</select>
		</td>
		<td class="plain" valign="top">
			<!-- SUB: CHAIN_SEL -->
				{VAR:LC_FORMS_CHOOSE_OUTPUT_WH_DP_RESULTS}:<br>
				<select name='chain_op' class='small_button'>{VAR:chain_op}</select>
			<!-- END SUB: CHAIN_SEL -->
    &nbsp;</td>
		<td class="plain" valign="top">
			<!-- SUB: CHAIN_SEL2 -->
				Vali korduv form p&auml;rjas, mis tulemustes kordub:<br>
				<select name='chain_repeater' class='small_button'>{VAR:chain_repeater}</select>
			<!-- END SUB: CHAIN_SEL2 -->
    &nbsp;</td>
	</tr>
	<tr>
		<td colspan="4" class="plain"><input class='small_button' type='submit' value='{VAR:LC_FORMS_SAVE}'></td>
	</tr>
</table>

{VAR:reforb}
</form>

<script language="javascript">
ssrch();
</script>


<br><br>
-------------------------------------------------------------------------<br>
NB! edasist paluks kasutada ainult vana otsingut n6udvate asjade jaoks (dyn grupid, meilinglistid) <Br><br>






<script language=javascript>
var st=1;
function selall()
{
<!-- SUB: SELLINE -->
	document.forms[0].elements[{VAR:row}].checked=st;
<!-- END SUB: SELLINE -->
st = !st;
return false;
}
</script>
<form action='reforb{VAR:ext}' METHOD=post>
{VAR:LC_FORMS_PAGE}: 
<!-- SUB: PAGE -->
<a href='{VAR:pageurl}'>{VAR:from} - {VAR:to}</a> |
<!-- END SUB: PAGE -->

<!-- SUB: SEL_PAGE -->
{VAR:from} - {VAR:to} |
<!-- END SUB: SEL_PAGE -->

<br>{VAR:LC_FORMS_CHOOSE_WHTA_INPUT_FORM_FILL}:<br>
<table bgcolor="#CCCCCC" cellpadding=3 cellspacing=1 border=0>
<tr bgcolor="#C9EFEF">
<td class="title">ID</td>
<td class="title">{VAR:LC_FORMS_NAME}</td>
<td class="title">{VAR:LC_FORMS_COMMENT}</td>
<td class="title">{VAR:LC_FORMS_POSITION}</td>
<td class="title"><a href='#' onClick="selall();return false;">{VAR:LC_FORMS_ALL}</a></td>
<td class="title">{VAR:LC_FORMS_WHAT_OUTPUT_TO_USE}</td>
</tr>

<!-- SUB: LINE -->
<tr>
<td class="plain">{VAR:form_id}</td>
<td class="plain"><a href='{VAR:form_change}'>{VAR:form_name}</a></td>
<td class="plain">{VAR:form_comment}</td>
<td class="plain">{VAR:form_location}</td>
<td class="chkbox"><input type='checkbox' NAME='ch_{VAR:form_id}' VALUE=1 {VAR:checked}><input type='hidden' name='inpage[{VAR:form_id}]' value='1'><input type='hidden' name='prev[{VAR:form_id}]' value='{VAR:prev}'></td>
<td class="chkbox"><SELECT class='small_button' NAME='sel_{VAR:form_id}'>{VAR:ops}</select>
</td>
</tr>
<!-- END SUB: LINE -->
</table>
{VAR:LC_FORMS_SEARCH_ONLY_FRM_FORM}: <input type='checkbox' name='formsonly' value=1 {VAR:formsonly}><br><br>
{VAR:LC_FORMS_SEARCH_FROM_CHAIN}: <select name='se_chain'>{VAR:chains}</select><Br><br>
<input type=submit NAME='save' VALUE='{VAR:LC_FORMS_SAVE}'>
{VAR:reforb}
</form>
