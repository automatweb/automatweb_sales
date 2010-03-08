<table>
	<tr style="font-size:10px;font-weight:bold;">
		<td>
			Name
		</td>
		<td>
			Time
		</td>
		<td>
			Attendees
		</td>
		<td>
			Hotel(s)
		</td>
		<td>
			Copy and update
		</td>
		<td>
			Remove
		</td>
	</tr>
	<!-- SUB: RFP -->
	<tr style="font-size:10px;">
		<td>
			{VAR:name}
		</td>
		<td>
			{VAR:time}
		</td>
		<td>
			{VAR:attendees}
		</td>
		<td>
			<!-- SUB: HOTEL -->
			{VAR:hotel}
			<!-- END SUB: HOTEL -->
			<!-- SUB: HOTEL_SEP -->
			,
			<!-- END SUB: HOTEL_SEP -->
		</td>
		<td>
			<a href="{VAR:copy_url}">copy</a>
		</td>
		<td>
			<a href="{VAR:remove_url}">remove</a>
		</td>
	</tr>
	<!-- END SUB: RFP -->
</table>
