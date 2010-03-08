<script language="javascript">
var e_{VAR:form_id}_elname="",e_{VAR:form_id}_elname2="";
function setLink(li,title)
{
	for(i=0; i < document.{VAR:formtag_name}.elements.length; i++)
	{
		if (document.{VAR:formtag_name}.elements[i].name == e_{VAR:form_id}_elname)
		{
			document.{VAR:formtag_name}.elements[i].value = title;
		}
		if (document.{VAR:formtag_name}.elements[i].name == e_{VAR:form_id}_elname2)
		{
			document.{VAR:formtag_name}.elements[i].value = li;
		}
	}
}

function check_submit()
{
	{VAR:checks}

	return true;
}
</script>

<table{VAR:form_border}{VAR:form_bgcolor}{VAR:form_cellpadding}{VAR:form_cellspacing}{VAR:form_height}{VAR:form_width}{VAR:form_hspace}{VAR:form_vspace}>

<!-- SUB: LINE -->

<tr>
{VAR:COL}
</tr>

<!-- END SUB: LINE -->

</table>
{VAR:reforb}

<!-- SUB: EXTRAIDS -->

<input type='hidden' NAME='{VAR:var_name}' VALUE='{VAR:var_value}'>

<!-- END SUB: EXTRAIDS -->


