
<a href='{VAR:add_pk_url}&section=11149'>Alustan broneerimist</a><br><br>
<!-- SUB: BOOKING -->
<div style='display: {VAR:disp_main};' id='bk{VAR:booking_id}'>
<table border="0" width="100%">
<tr><td>&nbsp;</td>
</tr>
<tr>
	<td width="100%" height="26" align="center" style="background-image:url(http://www.kalevspa.ee/img/taust_pealkiri.gif)" class="bronpealingid"><a href='javascript:void(0)' onClick='
el=document.getElementById("bk{VAR:booking_id}");
el.style.display == "none" ? el.style.display = "block" : el.style.display = "none";

el=document.getElementById("bka{VAR:booking_id}");
el.style.display == "none" ? el.style.display = "block" : el.style.display = "none";
'><font color="white"><b>{VAR:booking}</b></font></a></td>
</tr>
<tr>
	<td>
		<table style="border: 1px solid black; " width="100%">
			<!-- SUB: BOOK_LINE -->
			<tr>
				<td style="padding-bottom: 10px; padding-top: 10px; border-bottom: 1px dotted black; line-height: 25px;">{VAR:name}</td>
				<td style="border-bottom: 1px dotted black; line-height: 25px;">{VAR:when}</td>
			</tr>
			<!-- END SUB: BOOK_LINE -->
		</table>
	</td>

</tr>
</table>

</div>
<div  id='bka{VAR:booking_id}' style='display: {VAR:disp_short}'>
<table border="0" width="100%">
<tr><td>&nbsp;</td>
</tr>
<tr>
	<td ><a href='javascript:void(0)' onClick='
el=document.getElementById("bk{VAR:booking_id}");
el.style.display == "none" ? el.style.display = "block" : el.style.display = "none";

el=document.getElementById("bka{VAR:booking_id}");
el.style.display == "none" ? el.style.display = "block" : el.style.display = "none";
'><b>{VAR:booking}</b></a></td>
</tr>
</table>
</div>
<!-- END SUB: BOOKING -->
