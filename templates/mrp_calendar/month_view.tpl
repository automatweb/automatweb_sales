<table border="0" width="100%" cellspacing="0" cellpadding="2">
<tr>
<!-- SUB: HEADER -->
	<!-- SUB: HEADER_CELL -->
	<th width="20%" class="aw04kalenderkast01">
		{VAR:dayname}
	</th>
	<!-- END SUB: HEADER_CELL -->
<!-- END SUB: HEADER -->
</tr>
<!-- SUB: WEEK -->
<tr>
	<!-- SUB: DAY -->
	<td width="20%" valign="top" class="aw04kalenderkast02">
	<div align="right" class="aw04kalendertextday"><a href="{VAR:daylink}">{VAR:daynum}</a></div>
	<span style="font-size: 11px;">
		{VAR:EVENT}
	</span>
	</td>
	<!-- END SUB: DAY -->
	<!-- SUB: TODAY -->
	<td width="20%" valign="top" class="aw04kalenderkast02today">
	<div align="right" class="aw04kalendertextday"><a name="today"></a><strong><a href="{VAR:daylink}">{VAR:daynum}</a></strong></div>
	<span style="font-size: 11px;">
		{VAR:EVENT}
	</span>
	</td>
	<!-- END SUB: TODAY -->
</tr>
<!-- END SUB: WEEK -->
</table>
