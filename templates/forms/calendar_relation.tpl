<form action='reforb{VAR:ext}' method=post name=ffrm>
<table bgcolor="#CCCCCC" cellpadding=3 cellspacing=1 border=0>
<tr class="aste01">
<td class="celltext" colspan=3><strong>Kalender&lt;-&gt;sündmus relatsioon</strong></td>
</tr>
<tr class="aste01">
<td class="celltext">Vorm või pärg:</td>
<td class="celltext" colspan="2"><select name="cal_id" {VAR:objects_disabled}>{VAR:target_objects}</select></td>
</tr>
<tr class="aste01">
<td class="celltext">Seoseelement:</td>
<td class="celltext" colspan="2"><select name="el_relation" {VAR:relation_disabled}>{VAR:relation_els}</select></td>
</tr>
<tr class="aste01">
<td class="celltext">Kasuta pärja sisestuse ID-d kalendrisse kirjutamisel:</td>
<td class="celltext" colspan="2"><input type="checkbox"  name="el_use_chain_entry_id" value="1" {VAR:el_use_chain_entry_id}"></td>
</tr>
<tr class="aste01">
<td class="celltext">Luba ülebroneeringuid:</td>
<td class="celltext" colspan="2"><input type="checkbox"  name="el_allow_exceed" value="1" {VAR:el_allow_exceed}"></td>
</tr>
<tr class="aste01">
<td class="celltext">Sündmuse alguse element:</td>
<td class="celltext" colspan="2"><select name="el_start" {VAR:start_disabled}>{VAR:start_els}</select></td>
</tr>
<tr class="aste01">
<td class="celltext" rowspan="2" valign="top">Sündmuse lõpp:</td>
<td class="celltext"><input type="radio" name="end_type" value="1" {VAR:end_type_el}> Vali element</td>
<td class="celltext"><select name="el_end" {VAR:end_disabled}>{VAR:end_els}</select></td>
</tr>
<tr class="aste01">
<td class="celltext"><input type="radio" name="end_type" value="2" {VAR:end_type_shift}> või nihe alguse suhtes</td>
<td class="celltext"><input type="text" name="end" size="3" value="{VAR:end}"><select name="end_mp">{VAR:end_mp}</select>
</td>
</tr>
<tr class="aste01">
<td class="celltext">Vormitabel kalendris:</td>
<td class="celltext" colspan="2"><select name="ev_table" {VAR:tables_disabled}>{VAR:ev_tables}</select></td>
</tr>
<tr class="aste02">
<td class="celltext" colspan="3">Kogus ja seoseelemendid</td>
</tr>
<!-- SUB: count_line -->
<tr class="aste01">
<td class="celltext">{VAR:count_el_name}</td>
<td class="celltext" colspan="2"><select name="amount_el[{VAR:count_el_id}]">{VAR:cnt_els}</select></td>
</tr>
<!-- END SUB: count_line -->
<tr class="aste01">
<td class="celltext"><input type="text" name="amount_number[0]" value="{VAR:amount_number}" size="4"></td>
<td class="celltext" colspan="2"><select name="amount_el2[0]">{VAR:cnt_els2}</select></td>
</tr>
<!--
<tr class="aste01">
<td class="celltext" rowspan="2" valign="top">Sündmuste arv:</td>
<td class="celltext"><input type="radio" name="cnt_type" value="1" {VAR:cnt_type_el}> Vali element</td>
<td class="celltext"><select name="el_cnt" {VAR:count_disabled}>{VAR:cnt_els}</select></td>
</tr>
<tr class="aste01">
<td class="celltext"><input type="radio" name="cnt_type" value="2" {VAR:cnt_type_cnt}> või sisesta sündmuste arv</td>
<td class="celltext"><input type="text" name="count" size="3" value="{VAR:count}"></td>
</tr>
-->
<tr class="aste01">
<td class="celltext" colspan="3" align="center"><input class='small_button' type='submit' VALUE='{VAR:LC_FORMS_SAVE}'></td>
</tr>
</table>
{VAR:reforb}
</form>
  
