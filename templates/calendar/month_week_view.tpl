<script type="text/javascript">
var chk_status = true;
function selall()
{
	len = document.changeform.elements.length;
	for (i = 0; i < len; i++)
	{
		document.changeform.elements[i].checked = chk_status;
	}
	chk_status = !chk_status;
}
</script>
<!-- SUB: DCHECK -->
<div class="aw04kalendersubevent">
<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr class="aw04kalendersubevent">
<td width="90%">
</td>
<td width="10%" align="right">
<a href="javascript:selall()">Vali</a>
</td>
</tr>
</table>
</div>
<!-- END SUB: DCHECK -->
<table border="0" width="100%" cellpadding="0" cellspacing="0">
<!-- SUB: WEEK -->
<tr>
<td valign="top" class="aw04kalenderkast04">
<strong>{VAR:monthvar}</strong>
</td>
</tr>
<!-- SUB: DAY -->
<tr>
<td valign="top" class="aw04kalenderkast01">
<span class="aw04kalendertext"><strong><a href="{VAR:daylink}">{VAR:lc_weekday}, {VAR:daynum}. {VAR:lc_month}</a></strong>
</span>
</td>
</tr>
<tr>
<td valign="top"  class="aw04kalenderkast03">
<span style="font-size: 11px;">
	{VAR:EVENT}
</span>
</td>
</tr>
<!-- END SUB: DAY -->
<!-- SUB: TODAY -->
<tr>
<td valign="top" class="aw04kalenderkast01today">
<span class="aw04kalendertext"><strong>
<a style="text-decoration: none; color: black;" href="{VAR:daylink}">{VAR:lc_weekday}, {VAR:daynum}. {VAR:lc_month}</a></strong>
</span>
</td>
</tr>
<tr>
<td valign="top" class="aw04kalenderkast03today">
<span style="font-size: 11px;">
	{VAR:EVENT}
</span>
</td>
</tr>
<!-- END SUB: TODAY -->
<!-- END SUB: WEEK -->
</table>
