<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
	<title>Insert Table</title>
<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
<!--
function AddTable() {
	var widthType = (document.tableForm.widthType.value == "pixels") ? "" : "%";
	var html = '<table border="' + document.tableForm.border.value + '" cellpadding="' + document.tableForm.padding.value + '" ';
	
	html += 'cellspacing="' + document.tableForm.spacing.value + '" width="' + document.tableForm.width.value + widthType + '">\n';
	for (var rows = 0; rows < document.tableForm.rows.value; rows++) {
		html += "<tr>\n";
		for (cols = 0; cols < document.tableForm.columns.value; cols++) {
			html += "<td class='text'>&nbsp;</td>\n";
		}
		html+= "</tr>\n";
	}
	html += "</table>\n";
	
	window.opener.insertHTML(html);
	window.close();
}
//-->
</script>
</head>

<body style="margin: 10px; background: #FFF;">

<form name="tableForm">
<table cellpadding="4" cellspacing="0" border="0">
	<tr>
		<td align="right">Ridu:</td>
		<td><input name="rows" type="text" id="rows" value="2" size="4"></td>
		<td align="left">Veerge: <input name="columns" type="text" id="columns" value="2" size="4"></td>
	</tr>
	<tr>
		<td align="right">Laius:</td>
		<td><input name="width" type="text" id="width" value="100" size="4"></td>
		<td align="left">
			<select name="widthType" id="widthType">
				<option value="pixels">punkti</option>
				<option value="percent" selected>protsenti</option>
			</select>
		</td>
	</tr>
	<tr>
		<td align="right">Tabeli ‰‰ris:</td>
		<td><input name="border" type="text" id="border" value="1" size="4"></td>
		<td align="left">punkti</td>
	</tr>
	<tr>
		<td align="right">Lahtri ‰‰ris:</td>
		<td><input name="padding" type="text" id="padding" value="4" size="4"></td>
		<td>Lahtrite vahe: <input name="spacing" type="text" id="0" value="0" size="4"></td>
	</tr>
	<tr>
		<td colspan="3" align="center">
			<input type="button" value="Tee tabel" onClick="AddTable();" />
			<input type="button" value="Cancel" onClick="window.close();" />
		</td>
	</tr>
</table>

</form>

</body>
</html>
