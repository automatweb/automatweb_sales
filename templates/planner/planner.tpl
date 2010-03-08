<table width="100%" border="0" cellpadding="3" cellspacing="0">
<tr>
<td class="caldayheadday">
<a href="{VAR:prev}"><IMG SRC="{VAR:baseurl}/automatweb/images/blue/cal_nool_left.gif" WIDTH="19" HEIGHT="8" BORDER=0 ALT="&lt;&lt;"></a> {VAR:caption}  <a href="{VAR:next}"><IMG SRC="{VAR:baseurl}/automatweb/images/blue/cal_nool_right.gif" WIDTH="19" HEIGHT="8" BORDER=0 ALT="&gt;&gt;"></a></td>
</tr>
</table>

<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr><td width="99%" valign="top">
{VAR:content}
</td>
<!-- SUB: NAVPANEL -->
<td width="1" class="caltablebordertume"></td>
<td valign="top">

<!-- SUB: navigator -->
<table border="0" cellpadding="0" cellspacing="0">	
<tr>
<td valign="top" class="caldaysback">
{VAR:navi0}
</td>
</tr>
<tr>
<td valign="top" class="caldaysback">
{VAR:navi1}
</td>
</tr>
<tr>
<td valign="top" class="caldaysback">
{VAR:navi2}
</td>
</tr>
</table>
<!-- END SUB: navigator -->


<table border="0" width="100%" cellpadding="0" cellspacing="0">
<tr><td style="font-size: 10px; text-align: center; font-weight: bold;">Toimetused</td></tr>
{VAR:summary_pane}
<!-- SUB: summary_line -->
<tr><td style="font-size: 10px; padding: 3px;">
<a href="{VAR:url}">{VAR:caption}</a><br>
</td></tr>
<!-- END SUB: summary_line -->
</table>




</td>
<!-- END SUB: NAVPANEL -->
</tr>
</table>


<span class="header1">{VAR:menudef}</a>
<br>
<font color="red"><b>{VAR:status_msg}</b></font>
</span>
