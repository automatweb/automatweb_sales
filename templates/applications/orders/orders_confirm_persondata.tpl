<div class="text"><font color="red">Palume Teil kontrollida kõiki sisestatud andmeid.<br>
Kui kõik on korras, vajutage nuppu “Saada tellimus”.</font></div>

<table border="0" width="100%" cellpadding="0" cellspacing="1">
<tr>
        <td class='aw04contentcellleft' width="30%" nowrap>
		Kliendi number:
		</td>
        <td class='aw04contentcellright'>
			{VAR:client_nr}
        </td>
</tr>
<tr>

        <td class='aw04contentcellleft' width="30%" nowrap>
		Sünnikuupäev:
		</td>
        <td class='aw04contentcellright'>
        {VAR:birthday}
        </td>
</tr>
<tr>
        <td class='aw04contentcellleft' width="30%" nowrap>
		* Perekonnanimi
		</td>

        <td class='aw04contentcellright'>
			{VAR:lastname}
        </td>
</tr>
<tr>
        <td class='aw04contentcellleft' width="30%" nowrap>
		* Eesnimi:
		</td>
        <td class='aw04contentcellright'>
		{VAR:firstname}
        </td>
</tr>
<tr>

        <td class='aw04contentcellleft' width="30%" nowrap>
		* Aadress:
		</td>
        <td class='aw04contentcellright'>
			{VAR:person_contact}
        </td>
</tr>
<tr>
        <td class='aw04contentcellleft' width="30%" nowrap>
		Postiindeks:
		</td>
        <td class='aw04contentcellright'>
        	{VAR:udef_textbox2}
        </td>
</tr>
<tr>
        <td class='aw04contentcellleft' width="30%" nowrap>
		Linn:
		</td>
        <td class='aw04contentcellright'>
        	{VAR:udef_textbox3}
        </td>
</tr>
<tr>
        <td class='aw04contentcellleft' width="30%" nowrap>

		E-mail:
		</td>
        <td class='aw04contentcellright'>
		{VAR:person_email}
        </td>
</tr>
<tr>
        <td class='aw04contentcellleft' width="30%" nowrap>
		Telefon kodus:
		</td>
        <td class='aw04contentcellright'>
		{VAR:person_phone}
        </td>

</tr>
<tr>
        <td class='aw04contentcellleft' width="30%" nowrap>
		Telefon tööl:
		</td>
        <td class='aw04contentcellright'>
        {VAR:udef_textbox4}
        </td>
</tr>
<tr>
        <td class='aw04contentcellleft' width="30%" nowrap>
		Mobiil:
		</td>

        <td class='aw04contentcellright'>
        {VAR:udef_textbox5}
        </td>
</tr>
<tr>
        <td class='aw04contentcellleft' width="30%" nowrap>
		Kliendi tüüp
		</td>
        <td class='aw04contentcellright'>
        {VAR:udef_textbox6}
        </td>
</tr>

<tr>
	<td colspan="2" align="left">
	<form method="GET">
		<input onClick="parent.location='{VAR:sendurl}'" class="formbutton" type='button' name='' value='Saada tellimus' accesskey="s">
	</form>
	<form method="GET">
		<input onClick="history.go(-1)" class="formbutton" type='button' name='' value='Soovin muuta tellimust' accesskey="s">
	</form>
	</td>
</tr>
</table>
<!--<a href="{VAR:sendurl}">Saada tellimus ära</a>-->