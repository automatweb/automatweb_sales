<table border="0" cellspacing="1" cellpadding="2">
<!-- SUB: line -->
<tr>
<td>
<table border="0" cellspacing="1" cellpadding="2">
<!-- SUB: TRANSITION_ROW -->
<tr>
	<td>
	<!-- SUB: TRANSITION -->
	<select name="transition_for[{VAR:action}]">{VAR:transitions}</select>
	<!-- END SUB: TRANSITION -->
	</td>
</tr>
<!-- END SUB: TRANSITION_ROW -->

<!-- SUB: element -->
<td>
Tegevus:<br><strong>{VAR:caption}</strong><br>
Järgmised:<br> <select name="next[{VAR:id}][]" size="5">{VAR:actlist}</select>
</td>
<!-- END SUB: element -->
</tr>
</table>
</td>
</tr>
<!-- END SUB: line -->
</table>
