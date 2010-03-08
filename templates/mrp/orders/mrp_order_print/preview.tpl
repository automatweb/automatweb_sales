<font size="2" face="Arial">

<!-- demo header -->

<table border="0" cellpadding="4" cellspacing="0" width="100%">
	<tr>
		<td width="50%"><img src="http://www.reusner.ee/img/logo.png" width="162" height="55"></td>
		<td width="50%">
			<strong>Reusner AS</strong><br>
			Lõuka 10, 13521 Tallinn<br>
			Tel: 6 807 100<br>
			Faks: 6 517 575<br>
			E-mail: info@reusner.ee 
		</td>
	</tr>
</table>

<br>

<h1>{VAR:name}</h1>

<br>

<!-- tellija logo: {VAR:orderer_logo} -->

<table id="orderer" bgcolor="#efefef" border="1" bordercolor="#ffffff" cellpadding="4" cellspacing="0" width="100%" style="border-collapse: collapse;">
	<tr bgcolor="#bbbbbb">
		<th colspan="2">Tellija andmed</th>
	</tr>
	<tr class="even">
		<td class="caption" bgcolor="#dddddd" width="25%"><strong>Tellija:</strong></td>
		<td class="value" width="75%">{VAR:orderer_name}&#32;</td>
	</tr>
	<tr>
		<td class="caption" bgcolor="#dddddd" width="25%"><strong>Aadress:</strong></td>
		<td class="value" width="75%">{VAR:orderer_address}&#32;</td>
	</tr>
	<tr class="even">
		<td class="caption" bgcolor="#dddddd" width="25%"><strong>Telefon:</strong></td>
		<td class="value" width="75%">{VAR:orderer_phone}&#32;</td>
	</tr>
	<tr>
		<td class="caption" bgcolor="#dddddd" width="25%"><strong>Faks:</strong></td>
		<td class="value" width="75%">{VAR:orderer_fax}&#32;</td>
	</tr>
	<tr class="even">
		<td class="caption" bgcolor="#dddddd" width="25%"><strong>Kontaktisik:</strong></td>
		<td class="value" width="75%">{VAR:orderer_contact}&#32;</td>
	</tr>
</table>

<br><br><br>

<table id="data" bgcolor="#efefef" border="1" bordercolor="#ffffff" cellpadding="4" cellspacing="0" width="100%" style="border-collapse: collapse;">
	<tr bgcolor="#bbbbbb">
		<th colspan="2">Tellimuse andmed</th>
	</tr>
	<tr>
		<td class="caption" bgcolor="#dddddd" width="25%"><strong>Tellimuse nimi:</strong></td>
		<td class="value" width="75%">{VAR:name}&#32;</td>
	</tr>
	<tr class="even">
		<td class="caption" bgcolor="#dddddd" width="25%"><strong>T&auml;htaeg:</strong></td>
		<td class="value" width="75%">{VAR:deadline}&#32;</td>
	</tr>
	<tr class="even">
		<td class="caption" bgcolor="#dddddd" width="25%"><strong>Tr&uuml;kiarv:</strong></td>
		<td class="value" width="75%">{VAR:amount}&#32;</td>
	</tr>
	<tr>
		<td class="caption" bgcolor="#dddddd" width="25%"><strong>Maht:</strong></td>
		<td class="value" width="75%">{VAR:page_count}&#32;</td>
	</tr>
	<tr>
		<td class="caption" bgcolor="#dddddd" width="25%"><strong>V&auml;rviliste lehtede maht:</strong></td>
		<td class="value" width="75%">{VAR:colour_page_count}&#32;</td>
	</tr>
	<tr class="even">
		<td class="caption" bgcolor="#dddddd" width="25%"><strong>Formaat:</strong></td>
		<td class="value" width="75%">{VAR:e_format}&#32;</td>
	</tr>
	<tr>
		<td class="caption" bgcolor="#dddddd" width="25%"><strong>Kaaned:</strong></td>
		<td class="value" width="75%">{VAR:e_covers}&#32;</td>
	</tr>
	<tr class="even">
		<td class="caption" bgcolor="#dddddd" width="25%"><strong>Paber (kaaned):</strong></td>
		<td class="value" width="75%">{VAR:e_cover_paper}&#32;</td>
	</tr>
	<tr>
		<td class="caption" bgcolor="#dddddd" width="25%"><strong>Paber (sisu):</strong></td>
		<td class="value" width="75%">{VAR:e_main_paper}&#32;</td>
	</tr>
	<tr class="even">
		<td class="caption" bgcolor="#dddddd" width="25%"><strong>Värvilisus (kaaned):</strong></td>
		<td class="value" width="75%">{VAR:e_cover_colour}&#32;</td>
	</tr>
	<tr>
		<td class="caption" bgcolor="#dddddd" width="25%"><strong>V&auml;rvilisus (sisu):</strong></td>
		<td class="value" width="75%">{VAR:e_main_colour}&#32;</td>
	</tr>
	<tr class="even">
		<td class="caption" bgcolor="#dddddd" width="25%"><strong>K&ouml;ide v&otilde;i kinnitus:</strong></td>
		<td class="value" width="75%">{VAR:e_binding}&#32;</td>
	</tr>
	<tr>
		<td class="caption" bgcolor="#dddddd" width="25%"><strong>M&otilde;&otilde;dud:</strong></td>
		<td class="value" width="75%">{VAR:e_measures}&#32;</td>
	</tr>
	<tr class="even">
		<td class="caption" bgcolor="#dddddd" width="25%"><strong>Materjalid:</strong></td>
		<td class="value" width="75%">{VAR:e_materials}&#32;</td>
	</tr>
	<tr>
		<td class="caption" bgcolor="#dddddd" width="25%"><strong>J&auml;relt&ouml;&ouml;tlus:</strong></td>
		<td class="value" width="75%">{VAR:e_post_processing}&#32;</td>
	</tr>
	<tr class="even">
		<td class="caption price" bgcolor="#dddddd" width="25%"><strong><font color="red">Hind:</font></strong></td>
		<td class="value price" width="75%"><strong><font color="red">{VAR:price}&#32;</font></strong></td>
	</tr>
</table>
<br><br>
<table id="data" bgcolor="#efefef" border="1" bordercolor="#ffffff" cellpadding="4" cellspacing="0" width="100%" style="border-collapse: collapse;">
	<tr bgcolor="#bbbbbb">
		<th colspan="3">Materjalide kulu</th>
	</tr>
	<tr>
		<td class="value" ><strong>Nimi</strong></td>
		<td class="value" ><strong>Kogus</strong></td>
		<td class="value" ><strong>Hind</strong></td>
	</tr>

	<!-- SUB: MATERIAL_LINE -->
	<tr>
		<td class="value" >{VAR:mat_name}</td>
		<td class="value" >{VAR:mat_amt} kg</td>
		<td class="value" >{VAR:mat_price}</td>
	</tr>
	<!-- END SUB: MATERIAL_LINE -->
</table>

<br><br>
<table id="data" bgcolor="#efefef" border="1" bordercolor="#ffffff" cellpadding="4" cellspacing="0" width="100%" style="border-collapse: collapse;">
	<tr bgcolor="#bbbbbb">
		<th colspan="3">Resursside kulu</th>
	</tr>
	<tr>
		<td class="value" ><strong>Nimi</strong></td>
		<td class="value" ><strong>Tundide arv</strong></td>
		<td class="value" ><strong>Hind</strong></td>
	</tr>

	<!-- SUB: RESOURCE_LINE -->
	<tr>
		<td class="value" >{VAR:resource_name}</td>
		<td class="value" >{VAR:resource_hrs} tundi</td>
		<td class="value" >{VAR:resource_price}</td>
	</tr>
	<!-- END SUB: MATERIAL_LINE -->
</table>
</font>