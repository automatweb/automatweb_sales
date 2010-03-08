<script language="javascript">
var e_{VAR:form_id}_elname="",e_{VAR:form_id}_elname2="";
function setLink(li,title)
{
	for(i=0; i < document.fm_{VAR:form_id}.elements.length; i++)
	{
		if (document.fm_{VAR:form_id}.elements[i].name == e_{VAR:form_id}_elname)
		{
			document.fm_{VAR:form_id}.elements[i].value = title;
		}
		if (document.fm_{VAR:form_id}.elements[i].name == e_{VAR:form_id}_elname2)
		{
			document.fm_{VAR:form_id}.elements[i].value = li;
		}
	}
}

function check_submit()
{
	{VAR:checks}

	return true;
}

function form_reset()
{
	document.fm_{VAR:form_id}.reset();
}
var form_changed = 0;
</script>
<form name='fm_{VAR:form_id}' action='{VAR:form_action}' METHOD="{VAR:method}" enctype='multipart/form-data' onSubmit="check_submit()">
<input type='hidden' NAME='MAX_FILE_SIZE' VALUE='10000000'>

<table {VAR:tblstring}>

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

</form>

