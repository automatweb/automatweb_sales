<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
	<title>Insert link</title>
<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
<!--
function AddLink() {

	var oForm = document.linkForm;
	
	//validate form
	if (oForm.url.value == '') {
		alert('Sisesta URL ka.');
		return false;
	}
	
	var prefix = '<a href="' + document.linkForm.url.value + '"'; 
	if (oForm.newwin.checked)
	{
 		prefix = prefix + ' target="_new" ';
	}

	prefix = prefix + '>';

	var sufix = '</a>';
	
	window.opener.surroundHTML(prefix,sufix);
	window.close();
	return true;
}

//-->
</script>
</head>

<body style="margin: 10px; background: #FFF;">

<form name="linkForm">
<table cellpadding="4" cellspacing="0" border="0">
	<tr>
		<td align="right">URL:</td>
		<td><input name="url" type="text" id="url" size="40"></td>
	</tr>
	<tr>
		<td align="left">Uues aknas: </td>
		<td><input name="newwin" type="checkbox" id="newwin" value="1"></td>
	</tr>
	<tr>
		<td colspan="3" align="center">
			<input type="button" value="Tee link" onClick="AddLink();" />
			<input type="button" value="Cancel" onClick="window.close();" />
		</td>
	</tr>
</table>

</form>

</body>
</html>
