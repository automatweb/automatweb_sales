<html>

<head>
	<style>
		.listItem {
			font-family: Arial, Verdana, sans-serif;
			font-size: 11px;
			color: #000000;
			background:#FCFCF4
		}

		BODY{ 
			scrollbar-face-color : #A4A4A4; 
			scrollbar-shadow-color : #A4A4A4; 
			scrollbar-highlight-color : #FFFFFF; 
			scrollbar-3dlight-color : #000000; 
			scrollbar-darkshadow-color : #000000; 
			scrollbar-arrow-color : #FFFFFF;
			margin:0px;
		}

		.listTitle {
			font-family: Arial, Verdana, sans-serif;
			font-size: 11px;
			color: #000000;
			background-color: #478EB6;
		}

		.awformtext {
			font-family: verdana, sans-serif;
			font-size: 10px;
		}
	</style>
</head>

<body>

<form method='POST' action='index.aw'>
<table border=1 bgcolor="#ffffff" width="100%">
	<tr>
		<td class='listTitle'>
			Firma nimi
		</td>
		<td class='listTitle'>
			Kasutajanimi
		</td>
		<td class='listTitle'>
			Parool
		</td>
		<td class='listTitle'>
			Muuda parooli
		</td>
	</tr>
	<tr>
		<td colspan=4 align='left'>
			<input type=submit value='Muuda paroolid' class='formBox'>
		</td>
	</tr>
	<!-- SUB: klient -->
	<tr>
		<td class="listItem">{VAR:firmanimi}&nbsp;</td>
		<td class="listItem">{VAR:kasutajanimi}&nbsp;</td>
		<td class="listItem">{VAR:password}&nbsp;</td>
		<td class="listItem">
			<input type='text' name='password[]' class='awformtext'>
			<input type='hidden' name='username[]' value='{VAR:kasutajanimi}'>
		</td>
	</tr>
	<!-- END SUB: klient -->
	{VAR:reforb}
	<tr>
		<td colspan=4 align='left'>
			<input type=submit value='Muuda paroolid' class='formBox'>
		</td>
	</tr>
</table>
</form>

</body>
</html>
