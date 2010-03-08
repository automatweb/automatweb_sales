<table width="100%" border=0 cellspacing=0 cellpadding=1>

<form action='reforb.{VAR:ext}' method=post>

<tr>
<td bgcolor="#FFFFFF">

<table width="100%" border=0 cellspacing=0 cellpadding=5>
<tr>
<td class="aste01">



<table border=0 cellspacing=0 cellpadding=0>
<tr><td bgcolor="#FFFFFF">
<table cellpadding=3 cellspacing=1 border=0>
<tr class="aste01">

<td class="celltext" colspan=10><input type='submit' VALUE='{VAR:LC_FORMS_SAVE}' class="formbutton"></td>
</tr>
<tr class="aste01">
<td class="celltext"></td>
<!-- SUB: LANGH -->
<td class="celltext">{VAR:lang_name}:</td>
<!-- END SUB: LANGH -->
</tr>
<tr class="aste01">
<td colspan=10 class="celltext">{VAR:LC_FORMS_ELEMENTS_TEXTS}:</td>
</tr>
<!-- SUB: LROW -->
<tr class="aste01">
<td class="celltext">{VAR:name}</td>
<!-- SUB: LCOL -->
<td class="celltext"><input type='text' name='r[{VAR:row}][{VAR:col}][{VAR:lang_id}][{VAR:elid}]' value='{VAR:text}' class='formtext'></td>
<!-- END SUB: LCOL -->
</tr>
<!-- END SUB: LROW -->
<tr class="aste01">
<td colspan=10 class="celltext">{VAR:LC_FORMS_LISTBOXES_CONTENT}:</td>
</tr>
<!-- SUB: LROW1 -->
<tr class="aste01">
<td class="celltext">{VAR:name}</td>
<!-- SUB: LCOL1 -->
<td class="celltext"><input type='text' name='l[{VAR:row}][{VAR:col}][{VAR:lang_id}][{VAR:elid}][{VAR:item}]' value='{VAR:text}' class='formtext'></td>
<!-- END SUB: LCOL1 -->
</tr>
<!-- END SUB: LROW1 -->
<tr class="aste01">
<td colspan=10 class="celltext">{VAR:LC_FORMS_MLISTBX_CONTENT}:</td>
</tr>
<!-- SUB: LROW2 -->
<tr class="aste01">
<td class="celltext">{VAR:name}</td>
<!-- SUB: LCOL2 -->
<td class="celltext"><input type='text' name='m[{VAR:row}][{VAR:col}][{VAR:lang_id}][{VAR:elid}][{VAR:item}]' value='{VAR:text}' class='formtext'></td>
<!-- END SUB: LCOL2 -->
</tr>
<!-- END SUB: LROW2 -->
<tr class="aste01">
<td colspan=10 class="celltext">{VAR:LC_FORMS_SUBSCRIPTS_CONTENT}:</td>
</tr>
<!-- SUB: LROW3 -->
<tr class="aste01">
<td class="celltext">{VAR:name}</td>
<!-- SUB: LCOL3 -->
<td class="celltext"><input type='text' name='s[{VAR:row}][{VAR:col}][{VAR:lang_id}][{VAR:elid}]' value='{VAR:text}' class='formtext'></td>
<!-- END SUB: LCOL3 -->
</tr>
<!-- END SUB: LROW3 -->
<tr class="aste01">
<td colspan=10 class="celltext">{VAR:LC_FORMS_DEFAULT_TEXTS_CONTENT}:</td>
</tr>
<!-- SUB: LROW4 -->
<tr class="aste01">
<td class="celltext">{VAR:name}</td>
<!-- SUB: LCOL4 -->
<td class="celltext"><input type='text' name='d[{VAR:row}][{VAR:col}][{VAR:lang_id}][{VAR:elid}]' value='{VAR:text}' class='formtext'></td>
<!-- END SUB: LCOL4 -->
</tr>
<!-- END SUB: LROW4 -->
<tr class="aste01">
<td colspan=10 class="celltext">{VAR:LC_FORMS_ERRORS_CONTENT}:</td>
</tr>
<!-- SUB: LROW5 -->
<tr class="aste01">
<td class="celltext">{VAR:name}</td>
<!-- SUB: LCOL5 -->
<td class="celltext"><input type='text' name='e[{VAR:row}][{VAR:col}][{VAR:lang_id}][{VAR:elid}]' value='{VAR:text}' class='formtext'></td>
<!-- END SUB: LCOL5 -->
</tr>
<!-- END SUB: LROW5 -->
<tr class="aste01">
<td colspan=10 class="celltext">Tähemärkide arvu kontrolli veateated:</td>
</tr>
<!-- SUB: LROW8 -->
<tr class="aste01">
<td class="celltext">{VAR:name}</td>
<!-- SUB: LCOL8 -->
<td class="celltext"><input type='text' name='cl[{VAR:row}][{VAR:col}][{VAR:lang_id}][{VAR:elid}]' value='{VAR:text}' class='formtext'></td>
<!-- END SUB: LCOL8 -->
</tr>
<!-- END SUB: LROW8 -->
<tr class="aste01">
<td colspan=10 class="celltext">{VAR:LC_FORMS_BUTTONTS_TEXTS}:</td>
</tr>
<!-- SUB: LROW6 -->
<tr class="aste01">
<td class="celltext">{VAR:name}</td>
<!-- SUB: LCOL6 -->
<td class="celltext"><input type='text' name='b[{VAR:row}][{VAR:col}][{VAR:lang_id}][{VAR:elid}]' value='{VAR:text}' class='formtext'></td>
<!-- END SUB: LCOL6 -->
</tr>
<!-- END SUB: LROW6 -->

<tr class="aste01">
<td colspan=10 class="celltext">Elementide metadata:</td>
</tr>
<!-- SUB: LROW7 -->
<tr class="aste01">
<td class="celltext">{VAR:name}</td>
<!-- SUB: LCOL7 -->
<td class="celltext"><input type='text' name='w[{VAR:row}][{VAR:col}][{VAR:elid}][{VAR:lang_id}][{VAR:mtk}]' value='{VAR:text}' class='formtext'></td>
<!-- END SUB: LCOL7 -->
</tr>
<!-- END SUB: LROW7 -->

<tr class="aste01">
<td colspan=10 class="celltext">Url nuppude aadressid:</td>
</tr>
<!-- SUB: LROW9 -->
<tr class="aste01">
<td class="celltext">{VAR:name}</td>
<!-- SUB: LCOL9 -->
<td class="celltext"><input type='text' name='bu[{VAR:row}][{VAR:col}][{VAR:elid}][{VAR:lang_id}]' value="{VAR:text}" class='formtext'></td>
<!-- END SUB: LCOL9 -->
</tr>
<!-- END SUB: LROW9 -->

<tr class="aste01">
<td colspan=10 class="celltext">Failide kustuta linkide tekstid:</td>
</tr>
<!-- SUB: LROW10 -->
<tr class="aste01">
<td class="celltext">{VAR:name}</td>
<!-- SUB: LCOL10 -->
<td class="celltext"><input type='text' name='dt[{VAR:row}][{VAR:col}][{VAR:elid}][{VAR:lang_id}]' value="{VAR:text}" class='formtext'></td>
<!-- END SUB: LCOL10 -->
</tr>
<!-- END SUB: LROW10 -->


<!-- do not use L*8, it's already in use
   .. and what the fsck is it with that naming scheme anyway? -->
<tr class="aste01">
<td class="celltext" colspan=10><input type='submit' VALUE='{VAR:LC_FORMS_SAVE}' class="formbutton"></td>
</tr>
</table>
</td></tr></table>
{VAR:reforb}

</td></tr></table>
</td></tr>

</form>

</table>
<br>
