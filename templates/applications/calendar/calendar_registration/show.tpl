<table border="0">
<!-- SUB: CALENDAR -->
<tr>
	<td><b>{VAR:person} {VAR:person_rank} {VAR:person_mail} {VAR:person_phone}, {VAR:person_address}</b></td>
</tr>
<tr>
	<td>
		<table class="CalendarReservation">
			<!-- SUB: VACANCY -->
				<tr>
					<td bgcolor="#ddffff">{VAR:date} {VAR:time_from} - {VAR:time_to}</td>
					<td align="left" bgcolor="#ddffff">&nbsp;&nbsp;<a href='{VAR:reg_link}'>Registreeru</a></td>
				</tr>
			<!-- END SUB: VACANCY -->

			<!-- SUB: TAKEN -->
				<tr>
					<td bgcolor="#ffddff">{VAR:date} {VAR:time_from} - {VAR:time_to}</td>
					<td bgcolor="#ffddff">&nbsp;&nbsp;Kinni</td>
				</tr>
			<!-- END SUB: TAKEN -->
		</table>
	</td>
</tr>
<!-- END SUB: CALENDAR -->

</table>
