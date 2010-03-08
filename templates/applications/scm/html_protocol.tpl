<!-- SUB: COMPETITION -->
	{VAR:competition_caption} : {VAR:competition_name}<br>
<!-- END SUB: COMPETITION -->
<!-- SUB: TOURNAMENT -->
	{VAR:tournament_caption} : {VAR:tournament_name}<br>
<!-- END SUB: TOURNAMENT -->
<!-- SUB: ORGANIZER -->
	{VAR:organizer_caption} : {VAR:organizer_name}<br>
<!-- END SUB: ORGANIZER -->
<!-- SUB: LOCATION -->
	{VAR:location_caption} : {VAR:location_name}<br>
<!-- END SUB: LOCATION -->
{VAR:time_caption} : {VAR:time_name}<br>
<!-- SUB: EVENT -->
	{VAR:event_caption} : {VAR:event_name}<br>
<!-- END SUB: EVENT -->
	{VAR:group_caption}:{VAR:group_name}<br>
	<br>
	<!-- SUB: HAS_RESULTS -->
	<table border=1>
		<tr>
			<td>{VAR:type}</td>
			<td>tulemus</td>
			<td>koht</td>
			<td>punktid</td>
		</tr>
	<!-- SUB: ROW -->
		<tr>
			<!-- SUB: INDIVIDUAL -->
				<td>{VAR:first_name}</td>
			<!-- END SUB: INDIVIDUAL -->
			<!-- SUB: TEAM -->
				<td>{VAR:team}</td>
			<!-- END SUB: TEAM -->
			<td>{VAR:result}</td>
			<td>{VAR:place}</td>
			<td>{VAR:points}</td>
		</tr>
	<!-- END SUB: ROW -->
<!-- END SUB: HAS_RESULTS -->
<!-- SUB: HASNT_RESULTS -->
tulemusi pole selles võistlusklassis
<!-- END SUB: HASNT_RESULTS -->
