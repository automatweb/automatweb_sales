{VAR:menubar}
<style>
	.reptexttitle {
		font-family: Tahoma,Arial,Helvetica,sans-serif;
		font-size: 12px;
		font-weight: bold;
	}
	.reptext {
		font-family: Tahoma,Arial,Helvetica,sans-serif;
		font-size: 11px;
	}
	
	.repform {
		font-family: Tahoma,Arial,Helvetica,sans-serif;
		font-size: 11px;
	};
</style>
<table border="0" cellspacing="0" cellpadding="1" width="100%">
<form method="POST" name="repeater">
<tr bgcolor="#CCCCCC">
<td align="center" class="reptexttitle">Pri</td>
<td align="center" class="reptexttitle">Vali</td>
<td align="center" class="reptexttitle">Sisu</td>
</tr>
<tr>
<td align="center"><input type="text" name="pri[1]" class="repform" maxlength="2" value="{VAR:pri1}" size="2"></td>
<td align="center"><input type="checkbox" name="use[1]" class="repform" {VAR:use1}></td>
<td class="reptext">
	Iga <input type="text" name="skip[day]" class="repform" value="{VAR:dayskip}" size="2" maxlength="2"> päeva tagant
</td>
</tr>
<tr bgcolor="#EEEEEE">
<td align="center"><input type="text" name="pri[2]" class="repform" maxlength="2" value="{VAR:pri2}" size="2"></td>
<td align="center"><input type="checkbox" name="use[2]" class="repform" {VAR:use2}></td>
<td class="reptext">
	Iga <input type="text" name="skip[week]" value="{VAR:weekskip}" size="2" class="repform" maxlength="2"> nädala tagant
</td>
</tr>
<tr>
<td align="center"><input type="text" name="pri[3]" class="repform" maxlength="2" value="{VAR:pri3}" size="2"></td>
<td align="center"><input type="checkbox" class="repform" name="use[3]" {VAR:use3}></td>
<td class="reptext">
	Iga <input type="text" value="{VAR:monthskip}" name="skip[month]" size="2" class="repform" maxlength="2"> kuu tagant
</td>
</tr>
<tr bgcolor="#EEEEEE">
<td align="center"><input type="text" name="pri[4]" class="repform" maxlength="2" value="{VAR:pri4}" size="2"></td>
<td align="center"><input type="checkbox" name="use[4]" class="repform" {VAR:use4}></td>
<td class="reptext">
	Iga <input type="text" value="{VAR:yearskip}" size="2" name="skip[year]" class="repform" maxlength="2"> aasta tagant
</td>
</tr>
<tr>
<td align="center"><input type="text" name="pri[5]" class="repform" maxlength="2" value="{VAR:pri5}" size="2"></td>
<td align="center"><input type="checkbox" name="use[5]" {VAR:use5}></td>
<td class="reptext">
	Iga nädala nendel päevadel:
	<input type="checkbox" name="weekpwhen[1]" value="1" {VAR:weekpwhen1}>E |
	<input type="checkbox" name="weekpwhen[2]" value="2" {VAR:weekpwhen2}>T |
	<input type="checkbox" name="weekpwhen[3]" value="3" {VAR:weekpwhen3}>K |
	<input type="checkbox" name="weekpwhen[4]" value="4" {VAR:weekpwhen4}>N |
	<input type="checkbox" name="weekpwhen[5]" value="5" {VAR:weekpwhen5}>R |
	<input type="checkbox" name="weekpwhen[6]" value="6" {VAR:weekpwhen6}>L |
	<input type="checkbox" name="weekpwhen[7]" value="7" {VAR:weekpwhen7}>P 
</td>
</tr>
<tr bgcolor="#EEEEEE">
<td align="center"><input type="text" name="pri[6]" class="repform" maxlength="2" value="{VAR:pri6}" size="2"></td>
<td align="center"><input type="checkbox" name="use[6]" {VAR:use6}></td>
<td class="reptext">
	Iga kuu nendel nädalatel:
	<input type="checkbox" name="monpwhen[1]" value="1" {VAR:monpwhen1}>1 |
	<input type="checkbox" name="monpwhen[2]" value="2" {VAR:monpwhen2}>2 |
	<input type="checkbox" name="monpwhen[3]" value="2" {VAR:monpwhen3}>3 |
	<input type="checkbox" name="monpwhen[4]" value="4" {VAR:monpwhen4}>4 |
	<input type="checkbox" name="monpwhen[5]" value="5" {VAR:monpwhen5}>5 |
	<input type="checkbox" name="monpwhen[6]" value="6" {VAR:monpwhen6}>viimasel
</td>
</tr>
<tr>
<td align="center"><input type="text" name="pri[7]" class="repform" maxlength="2" value="{VAR:pri7}" size="2"></td>
<td align="center"><input type="checkbox" name="use[7]" {VAR:use7}></td>
<td class="reptext">
	Iga kuu nendel päevadel (nt 9,19,29) <input type="text" size="20" class="repform" name="monpwhen2" value="{VAR:mp2}">
</td>
</tr>
<tr bgcolor="#EEEEEE">
<td align="center"><input type="text" class="repform" name="pri[8]" maxlength="2" value="{VAR:pri8}" size="2"></td>
<td align="center"><input type="checkbox" name="use[8]" {VAR:use8}></td>
<td class="reptext">
	Iga aasta nendel kuudel <input type="text" size="20" class="repform" name="yearpwhen" value="{VAR:yearpwhen}">
</td>
</tr>
<tr>
<td class="reptext">
&nbsp;
</td>
<td colspan="2" valign="top" class="reptext">
<input type="radio" name="rep" value="1" checked>Korda kuni teisiti oeldakse (forever)<br>
<input type="radio" name="rep" value="2">Reserveeri <input type="text" class="repform" value="6" name="repeats" size="2"> järjestikust aega<br>
<input type="radio" name="rep" value="3">Korda kuni (dd/mm/yyyy)<input type="text" size="2" class="repform" name="repend[day]">/<input type="text" size="2" class="repform" name="repend[mon]">/<input type="text" size="4" class="repform" name="repend[year]"><br>
</td>
</tr>
<tr bgcolor="#EEEEEE">
<td class="reptext" align="center" colspan="3">
<input type="submit" value="Salvesta">
{VAR:reforb}
</td>
</tr>
</form>
</table>
