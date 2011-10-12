{VAR:shop_table}
<form action="/reforb{VAR:ext}" method="POST">
<table border="0" cellpadding="0" cellspacing="0" width="100%">

	<tbody>
	<tr>
		<td colspan="2">
		{VAR:udef_textbox1_error}
		</td>
	</tr>
	<tr>
        <td class="aw04contentcellleft" nowrap="nowrap" width="30%">
		Kliendi number:
		</td>
        <td class="aw04contentcellright">
			<input name="udef_textbox1" size="40" value="{VAR:udef_textbox1_value}" maxlength="" type="text">
        </td>
	</tr>
	
	<tr>
        <td nowrap="nowrap" colspan="2"><span class="textred10">
        {VAR:person_birthday_error}</span>
        </td>
	</tr>
	
	<tr>
        <td class="aw04contentcellleft" nowrap="nowrap" width="30%">

		Sünnikuupäev:
		</td>
        <td class="aw04contentcellright">
		<select name="person_birthday[day]">
			<option value="-1">---</option>
			<option {VAR:selected_day1} value="1">1</option>
			<option {VAR:selected_day2} value="2">2</option>
			<option {VAR:selected_day3} value="3">3</option>
			<option {VAR:selected_day4} value="4">4</option>
			<option {VAR:selected_day5} value="5">5</option>
			<option {VAR:selected_day6} value="6">6</option>
			<option {VAR:selected_day7} value="7">7</option>
			<option {VAR:selected_day8} value="8">8</option>
			<option {VAR:selected_day9} value="9">9</option>
			<option {VAR:selected_day10} value="10">10</option>
			<option {VAR:selected_day11} value="11">11</option>
			<option {VAR:selected_day12} value="12">12</option>
			<option {VAR:selected_day13} value="13">13</option>
			<option {VAR:selected_day14} value="14">14</option>
			<option {VAR:selected_day15} value="15">15</option>
			<option {VAR:selected_day16} value="16">16</option>
			<option {VAR:selected_day17} value="17">17</option>
			<option {VAR:selected_day18} value="18">18</option>
			<option {VAR:selected_day19} value="19">19</option>
			<option {VAR:selected_day20} value="20">20</option>
			<option {VAR:selected_day21} value="21">21</option>
			<option {VAR:selected_day22} value="22">22</option>
			<option {VAR:selected_day23} value="23">23</option>
			<option {VAR:selected_day24} value="24">24</option>
			<option {VAR:selected_day25} value="25">25</option>
			<option {VAR:selected_day26} value="26">26</option>
			<option {VAR:selected_day27} value="27">27</option>
			<option {VAR:selected_day28} value="28">28</option>
			<option {VAR:selected_day29} value="29">29</option>
			<option {VAR:selected_day30} value="30">30</option>
			<option {VAR:selected_day31} value="31">31</option>
		</select>
		
		<select name="person_birthday[month]">
			<option value="-1">---</option>
			<option {VAR:selected_month1} value="1">jaanuar</option>
			<option {VAR:selected_month2} value="2">veebruar</option>
			<option {VAR:selected_month3} value="3">märts</option>
			<option {VAR:selected_month4} value="4">aprill</option>
			<option {VAR:selected_month5} value="5">mai</option>
			<option {VAR:selected_month6} value="6">juuni</option>
			<option {VAR:selected_month7} value="7">juuli</option>
			<option {VAR:selected_month8} value="8">august</option>
			<option {VAR:selected_month9} value="9">september</option>
			<option {VAR:selected_month10} value="10">oktoober</option>
			<option {VAR:selected_month11} value="11">november</option>
			<option {VAR:selected_month12} value="12">detsember</option>
		</select>
		{VAR:year_select}

        </td>
</tr>
<tr>
        <td nowrap="nowrap" colspan="2"><span class="textred10">
        {VAR:lastname_error}</span>
        </td>
</tr>
<tr>
        <td class="aw04contentcellleft" nowrap="nowrap" width="30%">
		* Perekonnanimi
		</td>
        <td class="aw04contentcellright">
		<input name="lastname" size="40" value="{VAR:lastname_value}" maxlength="" type="text">
        </td>
</tr>

<tr>
        <td nowrap="nowrap" colspan="2"><span class="textred10">
        {VAR:firstname_error}</span>
        </td>
</tr>

<tr>
        <td class="aw04contentcellleft" nowrap="nowrap" width="30%">
		* Eesnimi:
		</td>
        <td class="aw04contentcellright">
		<input name="firstname" size="40" value="{VAR:firstname_value}" maxlength="" type="text">
        </td>
</tr>

<tr>
        <td nowrap="nowrap" colspan="2">
        {VAR:person_contact_error}
        </td>
</tr>
<tr>
        <td class="aw04contentcellleft" nowrap="nowrap" width="30%">
		* Aadress:
		</td>
        <td class="aw04contentcellright">
		<input name="person_contact" size="40" value="{VAR:person_contact_value}" maxlength="" type="text">
        </td>
</tr>

<tr>
        <td nowrap="nowrap" colspan="2">
        {VAR:udef_textbox2_error}
        </td>
</tr>

<tr>
        <td class="aw04contentcellleft" nowrap="nowrap" width="30%">
		Postiindeks:
		</td>

        <td class="aw04contentcellright">
		<input name="udef_textbox2" size="40" value="{VAR:udef_textbox2_value}" maxlength="" type="text">

        </td>
</tr>

<tr>
        <td nowrap="nowrap" colspan="2">
        {VAR:udef_textbox3_error}
        </td>
</tr>

<tr>
        <td class="aw04contentcellleft" nowrap="nowrap" width="30%">
		Linn:
		</td>
        <td class="aw04contentcellright">
		<input name="udef_textbox3" size="40" value="{VAR:udef_textbox3_value}" maxlength="" type="text">

        </td>
</tr>
<tr>
        <td nowrap="nowrap" colspan="2">
        {VAR:person_email_error}
        </td>
</tr>
<tr>
        <td class="aw04contentcellleft" nowrap="nowrap" width="30%">
		E-mail:
		</td>
        <td class="aw04contentcellright">
		<input name="person_email" size="40" value="{VAR:person_email_value}" maxlength="" type="text">
        </td>

</tr>

<tr>
        <td nowrap="nowrap" colspan="2">
        {VAR:person_phone_error}
        </td>
</tr>
<tr>
        <td class="aw04contentcellleft" nowrap="nowrap" width="30%">
		Telefon kodus:
		</td>
        <td class="aw04contentcellright">
		<input name="person_phone" size="40" value="{VAR:person_phone_value}" maxlength="" type="text">
        </td>
</tr>

<tr>
        <td nowrap="nowrap" colspan="2">
        {VAR:udef_textbox4_error}
        </td>
</tr>

<tr>
        <td class="aw04contentcellleft" nowrap="nowrap" width="30%">
		Telefon tööl:
		</td>
        <td class="aw04contentcellright">
		<input name="udef_textbox4" size="40" value="{VAR:udef_textbox4_value}" maxlength="" type="text">
        </td>
</tr>
<tr>
        <td nowrap="nowrap" colspan="2">
        {VAR:udef_textbox5_error}
        </td>
</tr>
<tr>
        <td class="aw04contentcellleft" nowrap="nowrap" width="30%">
		Mobiil:
		</td>
        <td class="aw04contentcellright">
		<input name="udef_textbox5" size="40" value="{VAR:udef_textbox5_value}" maxlength="" type="text">
        </td>
</tr>
<tr>
        <td class="aw04contentcellleft" nowrap="nowrap" width="30%">
		Kliendi tüüp
		</td>
        <td class="aw04contentcellright">
	
		{VAR:customer_type1}
		
 püsiklient{VAR:customer_type2}
		
 esmakordselt
        </td>
</tr>

<tr>
	<td colspan="2" class="text"><span class="textred11"><font color="red">{VAR:udef_checkbox1_error}</font></span><br>
	 {VAR:udef_checkbox1} Olen tutvunud OTTO interneti-kataloogi <a href='http://www.otto.ee/856' target="_blank"> tellimistingimustega</a> ning nendega nõus. OTTO interneti-kataloogi  tellimistingimused on käesoleva tellimuse
alusel sõlmitava ostu-müügilepingu lahutamatuks osaks tellimuse esitamise hetke redaktsioonis.<br>Palume Teil täita * tähistatud väljad. Samuti palume Teil täita vähemalt üks telefoninumbri väli.</td>
</tr>
<tr>
	<td colspan="2" align="left">
	<input name="final_confirm_order" value="Kinnita tellimus" class="formbutton" type="submit">
	</td>
</tr>
</tbody></table>
	<input type='hidden' name='class' value='orders_order' />
	<input type='hidden' name='action' value='do_persondata_submit' />
	<input type='hidden' name='reforb' value='1' />
	<input type='hidden' name='id' value='{VAR:id}' />
	<input type='hidden' name='group' value='orderinfo' />
</form>
<form method="GET">
	<input onClick="history.go(-1)" class="formbutton" type='button' name='' value='Soovin muuta tellimust' accesskey="s">
</form>
