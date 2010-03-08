<form action='reforb.{VAR:ext}' method=post name=ffrm>
<table bgcolor="#CCCCCC" cellpadding=3 cellspacing=1 border=1>
<tr class="aste02">
<td class="celltext" colspan=3><strong>Ajavahemike defineerimine</strong></td>
</tr>
<tr class="aste01">
<td class="celltext" colspan="2">Alguskuupäeva element:</td>
<td class="celltext"><select name="el_event_start" class="formselect2" {VAR:start_disabled}>{VAR:els_start}</select></td>
</tr>
<tr class="aste01">
<td class="celltext" colspan="2">Lõpukuupäeva element:</td>
<td class="celltext"><select name="el_event_end" class="formselect2" {VAR:end_disabled}>{VAR:els_end}</select></td>
</tr>
<tr class="aste02">
<td class="celltext" colspan="3">
<strong>Koguse elemendid</strong>
</td>
</tr>
<!-- SUB: count_lines -->
<tr class="aste01">
<td class="celltext" colspan="2">&nbsp;&nbsp;{VAR:el}</td>
<td class="celltext"><select name="amount_el[{VAR:el_id}]">{VAR:els}</select></td>
</tr>
<!-- END SUB: count_lines -->
<tr class="aste02">
<td class="celltext" colspan="3">
<strong>Perioodi element</strong>
</td>
</tr>
<tr class="aste01">
<td class="celltext" colspan="2"><input type="radio" name="period_type" value="1" {VAR:per_type_1_check}>Vali vormielement</td>
<td class="celltext"><select name="el_event_period" class="formselect2" {VAR:period_disabled}>{VAR:els_period}</select></td>
</tr>
<tr class="aste01">
<td class="celltext" colspan="2"><input type="radio" name="period_type" value="2" {VAR:per_type_2_check}>Sisesta</td>
<td class="celltext">Ühikud: <input type="text" name="per_amount" size="4" value="{VAR:per_amount}"> Tüüp: <select name="per_unit_type" class="formselect2">{VAR:per_unit_type}</select></td>
</tr>
<tr class="aste02">
<td class="celltext" colspan="3">
<strong>Release period</strong>
</td>
</tr>
<tr class="aste01">
<td class="celltext" colspan="2"><input type="radio" name="release_type" value="1" {VAR:rel_type_1_check}>Vali vormielement</td>
<td class="celltext"><select name="el_event_release" class="formselect2" {VAR:release_disabled}>{VAR:els_release}</select></td>
</tr>
<tr class="aste01">
<td class="celltext" colspan="2"><input type="radio" name="release_type" value="2" {VAR:rel_type_2_check}>Vali tekstikast ja ühiku tüüp</td>
<td class="celltext">Tekstikast: <select name="release_textbox">{VAR:textboxes}</select> Ajaühik: <select name="release_unit_type" class="formselect2">{VAR:release_unit_type}</select></td>
</tr>
<tr class="aste01">
<td class="celltext" colspan=3><input class='small_button' type='submit' VALUE='{VAR:LC_FORMS_SAVE}'></td>
</tr>
</table>
{VAR:reforb}
</form>
  
