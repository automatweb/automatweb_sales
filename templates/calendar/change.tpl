<form method="POST" action="reforb.{VAR:ext}">
<table border="0" cellspacing="0" cellpadding="1">
<tr>
<td bgcolor=#CCCCCC>
<table border="0" cellspacing="0" cellpadding="3">
<tr>
<td class="fgtitle" colspan="2">
<a href="{VAR:doc_link}">Vali kontrollitav dokument</a>
</td>
</tr>
<tr>
<td class="fgtitle">Nimi</td>
<td class="fgtitle"><input type="text" name="name" size="30" value="{VAR:name}"></td>
</tr>
<tr>
<td class="fgtitle" valign="top">Kommentaar</td>
<td class="fgtitle"><textarea name="comment" cols="30" rows="5">{VAR:comment}</textarea></td>
</tr>
<tr>
<td class="fgtitle">Dokument</td>
<td class="fgtitle">{VAR:docid} {VAR:docname}</td>
</tr>
<tr>
<td class="fgtitle">Otsingud dokumendis</td>
<td class="fgtitle"><select name="search_el" {VAR:search_disabled}>{VAR:searches}</select></td>
</tr>
<tr>
<td class="fgtitle">P�ev algab</td>
<td class="fgtitle">{VAR:day_start}</td>
</tr>
<tr>
<td class="fgtitle">P�ev l�peb</td>
<td class="fgtitle">{VAR:day_end}</td>
</tr>
<tr>
<td class="fgtitle">Pealkirja stiil</td>
<td class="fgtitle"><select name="header_style">{VAR:header_styles}</select></td>
</tr>
<tr>
<td class="fgtitle">N�dalap�evade stiil</td>
<td class="fgtitle"><select name="weekday_style">{VAR:weekday_styles}</select></td>
</tr>
<tr>
<td class="fgtitle">N�dalap�evade stiil (weekend)</td>
<td class="fgtitle"><select name="weekend_style">{VAR:weekend_styles}</select></td>
</tr>
<tr>
<td class="fgtitle">P�evade stiil</td>
<td class="fgtitle"><select name="day_style">{VAR:day_styles}</select></td>
</tr>
<tr>
<td class="fgtitle">Aktiivse p�eva stiil</td>
<td class="fgtitle"><select name="act_day_style">{VAR:act_day_styles}</select></td>
</tr>
<tr>
<td class="fgtitle">Puhkep�evade stiil</td>
<td class="fgtitle"><select name="cel_day_style">{VAR:cel_day_styles}</select></td>
</tr>
<tr>
<td class="fgtitle" colspan="2" align="center">
<input type="submit" value="Salvesta">
</td>
</tr>
</table>
</td>
</tr>
</table>
{VAR:reforb}
</form>
