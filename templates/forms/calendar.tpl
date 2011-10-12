<form action='reforb{VAR:ext}' method=post name=ffrm>
<table bgcolor="#CCCCCC" cellpadding=3 cellspacing=1 border=0>
<!--
<tr class="aste01">
<td class="celltext">Vormi roll</td>
<td class="celltext"><select name="calendar_role">{VAR:roles}</select></td>
</tr>
-->
<!-- SUB: GENERAL -->
<tr class="aste01">
<td class="celltext" colspan=2><strong>Valige selle vormi roll!</strong></td>
</tr>
<!-- END SUB: GENERAL -->
<!-- SUB: ENTRY -->
<tr class="aste01">
<td class="celltext" colspan=2><strong>Sündmuste kalendrid</strong></td>
</tr>
<tr class="aste01">
<td class="celltext" colspan="2">
<a href="{VAR:newlink}">Lisa uus</a>
</td>
</tr>
<tr class="aste01">
<td class="celltext" colspan="2">
<table width="100%" border="0" cellspacing="2" cellpadding="2">
<tr class="aste03">
<td class="celltext" align="center">Objekt</td>
<td class="celltext" align="center">Seoseelement</td>
<td class="celltext" align="center">Algus</td>
<td class="celltext" align="center">Lõpp</td>
<td class="celltext" align="center">Arv</td>
<td class="celltext" align="center">Tabel</td>
<td class="celltext" colspan="2" align="center">Tegevus</td>
</tr>
<!-- SUB: LINE -->
<tr class="aste04">
<td class="celltext">{VAR:name}</td>
<td class="celltext">{VAR:rel}</td>
<td class="celltext">{VAR:start}</td>
<td class="celltext">{VAR:end}</td>
<td class="celltext">{VAR:cnt}</td>
<td class="celltext">{VAR:table}</td>
<td class="celltext"><a href="{VAR:ch_link}">Muuda</a></td>
<td class="celltext"><a href="javascript:box2('Kustutada see relatsioon?','{VAR:del_link}')">Kustuta</a></td>
</tr>
<!-- END SUB: LINE -->
</table>
</td>
</tr>
<!-- END SUB: ENTRY -->
<!-- SUB: DEFINE -->
<tr class="aste01">
<td class="celltext" colspan=2><strong>Ajavahemike defineerimine</strong></td>
</tr>
<tr class="aste01">
<td class="celltext">Alguskuupäeva element:</td>
<td class="celltext"><select name="el_event_start" class="formselect2" {VAR:start_disabled}>{VAR:els_start}</select></td>
</tr>
<tr class="aste01">
<td class="celltext">Lõpukuupäeva element:</td>
<td class="celltext"><select name="el_event_end" class="formselect2" {VAR:end_disabled}>{VAR:els_end}</select></td>
</tr>
<tr class="aste01">
<td class="celltext">Koguse element:</td>
<td class="celltext"><select name="el_event_count" class="formselect2" {VAR:count_disabled}>{VAR:els_count}</select></td>
</tr>
<tr class="aste01">
<td class="celltext">Perioodi element:</td>
<td class="celltext"><select name="el_event_period" class="formselect2" {VAR:period_disabled}>{VAR:els_period}</select></td>
</tr>
<tr class="aste01">
<td class="celltext">Release perioodi element:</td>
<td class="celltext"><select name="el_event_release" class="formselect2" {VAR:release_disabled}>{VAR:els_release}</select></td>
</tr>
<!-- END SUB: DEFINE -->
<!-- SUB: DEFINE2 -->
<tr class="aste01">
<td class="celltext" colspan=2><strong>Kalendri defineerimine</strong></td>
</tr>
<tr class="aste01">
<td class="celltext">Alguskuupäev:</td>
<td class="celltext">{VAR:start}</td>
</tr>
<tr class="aste01">
<td class="celltext">Lõpukuupäev:</td>
<td class="celltext">{VAR:end}</td>
</tr>
<tr class="aste01">
<td class="celltext">Kogus:</td>
<td class="celltext"><input type="text" name="count" size="4" value="{VAR:count}"></td>
</tr>
<tr class="aste01">
<td class="celltext">Perioodi pikkus:</td>
<td class="celltext"><input type="text" name="period" size="4" value="{VAR:period}"><select><option>päev</option></select></td>
</tr>
<!-- END SUB: DEFINE2 -->
<tr class="aste01">
<td class="celltext" colspan=2><input class='small_button' type='submit' VALUE='{VAR:LC_FORMS_SAVE}'></td>
</tr>
</table>
{VAR:reforb}
</form>
  
