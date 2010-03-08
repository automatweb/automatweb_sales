<table border="1" width="100%">
<tr>
	<td class="aw04kalendersubevent">&nbsp;</td>
<!-- SUB: DAY_H -->
	<td class="aw04kalendersubevent">{VAR:day_name}</td>
<!-- END SUB: DAY_H -->
</tr>
<!-- SUB: HOUR -->
<tr>
	<td class="aw04kalendersubevent">{VAR:hour}</td>
	<!-- SUB: DAY -->
	<td>
		<table border="1" width="100%">
			<tr>
			<!-- SUB: RESOURCE_H -->
				<td width="10%" class="aw04kalendersubevent">{VAR:res_name}</td>
			<!-- END SUB: RESOURCE_H -->
			</tr>
			<tr>
			<!-- SUB: RESOURCE -->
				<td width="10%" class="aw04kalendersubevent">{VAR:job_name}</td>
			<!-- END SUB: RESOURCE -->
			</tr>
		</table>
	</td>
	<!-- END SUB: DAY -->
	</td>
</tr>
<!-- END SUB: HOUR -->
</table>