<!-- generic calendar template -->
<!-- 1 -->
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr><td valign="top">

<!-- <header> -->
<table border="0" cellspacing="0" cellpadding="0" width="100%">
<!-- header is a separate subtemplate because this way we can switch it off if we need to -->
<!-- SUB: header -->
<tr>
	<!-- header cells are repeated for each column -->
		<td class="caldayheadday">{VAR:hcell}</td>
</tr>
<tr><td class="caltableborderhele"><IMG SRC="images/blue/trans.gif" WIDTH="1" HEIGHT="1" BORDER=0 ALT=""></td></tr>
<!-- END SUB: header -->
<!-- </header -->

<!-- contents -->
<!-- SUB: content -->
<tr>
	<td valign="top" bgcolor="#FFFFFF">
	<!-- nested table gives a greater flexibility -->
	<table border="0" width="100%" height="100%" cellpadding="5" cellspacing="0">
	<tr>
	<td>
	{VAR:cell}
	</td>
	</tr>
	</table>
	</td>
</tr>
<!-- END SUB: content -->
</table>


	</td>
	</tr>
	<tr><td class="caltablebordertume"><IMG SRC="images/blue/trans.gif" WIDTH="1" HEIGHT="1" BORDER=0 ALT=""></td></tr>
</table>

<!-- end 1-->