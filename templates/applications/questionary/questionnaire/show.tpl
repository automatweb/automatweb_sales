<!-- SUB: QUESTIONNAIRE -->
<form method="post" action="?qid=end">
<!-- SUB: PERSON_DATA_INSERTION -->
Palun sisestage oma andmed, et saaksime v&otilde;itjale anda $1,000,000!
<br />
{VAR:insertion_form}
<!-- K6ik isiku andmete salvestamiseks vajalikud v2ljad peavad olema nimega person[{property_nimi}] -->
Nimi: <input type="text" name="person[name]">
<!-- END SUB: PERSON_DATA_INSERTION -->
<!-- SUB: QUESTION -->
<table width="100%">
	<tr>
		<td valign="top">
			{VAR:question}
		</td>
	</tr>
	<tr>
		<td valign="top">
			<table width="100%" cellpadding="5">
				<tr>
					<td valign="top">
						<!-- SUB: ANSWER_RADIO -->
							<input type="radio" value="{VAR:answer_oid}" name="answer_{VAR:question_id}" {VAR:answer_checked}> {VAR:answer_caption}<br>
						<!-- END SUB: ANSWER_RADIO -->
						<!-- SUB: ANSWER_TEXTBOX -->
							<input type="textbox" name="answer_{VAR:question_id}" value="{VAR:answer_value}"><br>
						<!-- END SUB: ANSWER_TEXTBOX -->
					</td>
					<td valign="top">
						<!-- SUB: PICTURE -->
						{VAR:picture}
						<!-- END SUB: PICTURE -->
					</td>
				</tr>
			</table>
		</td>		
	</tr>
	<tr>
		<td valign="top">
			<table width="100%">
				<tr>
					<td width="40%" valign="top">
						{VAR:correct_vs_false}<br><br>
						<!-- SUB: CORRECT_ANSWERS -->
						{VAR:correct_answer_caption}<br>
						<!-- SUB: CORRECT_ANSWER -->
						{VAR:answer}<br>
						<!-- END SUB: CORRECT_ANSWER -->
						<!-- END SUB: CORRECT_ANSWERS -->
						<br>{VAR:acomment}<br><br>
						{VAR:qcomment}<br>
					</td>
					<td width="20%" valign="top">
						<!-- SUB: ANSWER_PICTURE -->
						{VAR:picture}
						<!-- END SUB: ANSWER_PICTURE -->
					</td>
					<td valign="top">
						{VAR:submit} <a href="{VAR:next_url}">{VAR:next_caption}</a>
					</td>
				</tr>
			</table>
		</td>		
	</tr>
</table>
<!-- END SUB: QUESTION -->
<input type="submit" value="Saada">
</form>
<!-- END SUB: QUESTIONNAIRE -->
<!-- SUB: RESULTS -->
<table width="100%">
	<tr>
		<td align="center" valign="top">
			&Otilde;igeid vastuseid {VAR:results_percent}%<br>
			&Otilde;igeid vastuseid {VAR:results_fraction}<br>
			{VAR:results_text}<br>
			<!-- SUB: RESULTS_TEXT_BY_PERCENT -->
			{VAR:results_text_by_percent}<br>
			<!-- END SUB: RESULTS_TEXT_BY_PERCENT -->
			<!-- SUB: RESULTS_ANSWERS -->
				<!-- RESULTS_ANSWERED on muutuja, kuhu pannakse SUB: RESULTS_CORRECTLY_ANSWERED ja SUB: RESULTS_WRONGLY_ANSWERED 6iges j2rjekorras kokku. -->
				{VAR:RESULTS_ANSWERED}
				<!-- SUB: RESULTS_CORRECTLY_ANSWERED -->
				K&uuml;simus: {VAR:results_question}<br />
				Sina vastasid: {VAR:results_my_answer}<br /><br />
				<!-- END SUB: RESULTS_CORRECTLY_ANSWERED -->

				<!-- SUB: RESULTS_WRONGLY_ANSWERED -->
				K&uuml;simus: {VAR:results_question}<br />
				Sina vastasid: {VAR:results_my_answer}<br />
				&Otilde;iged vastused: 
					<!-- SUB: RESULTS_CORRECT_ANSWER -->
					{VAR:results_correct_answer}<br />
					<!-- END SUB: RESULTS_CORRECT_ANSWER -->
				<br /><br />
				<!-- END SUB: RESULTS_WRONGLY_ANSWERED -->
			<!-- END SUB: RESULTS_ANSWERS -->
		</td>
	</tr>
</table>
<!-- END SUB: RESULTS -->