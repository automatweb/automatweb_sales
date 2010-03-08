<div style='background: #FFFFFF;'>
<!-- SUB: ROOM -->
<div style='font-weight: bold; border-bottom: 1px solid black;'>{VAR:room_name} <!--{VAR:date_from} - {VAR:date_to}--></div>
<div style='padding-left: 10px;'>
	<!-- SUB: DAY -->
		<span style='font-weight: bold; font-size: 15px;'>{VAR:date}:</span><br>
		<div style='padding-left: 10px; font-size: 13px; padding-bottom: 30px;'>

		<!-- SUB: BOOKING -->
			{VAR:time_from} - {VAR:time_to}: {VAR:customer} / {VAR:products_wo_amount} / {VAR:comment} / {VAR:content}/  {VAR:cust_arrived}<Br>
		<!-- END SUB: BOOKING -->
		</div>
	<!-- END SUB: DAY -->
</div>

<!-- END SUB: ROOM -->
</div>
