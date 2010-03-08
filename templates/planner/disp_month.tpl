<!-- generic calendar template -->
<!--
	there have to be 2 kinds of events, linked and a non-linked one
-->

<!-- <header> -->
<table border="1" cellspacing="1" cellpadding="1" width="100%">
<!-- header is a separate subtemplate because this way we can switch it off if we need to -->
<!-- SUB: header -->
<tr>
	<!-- header cells are repeated for each column -->
	<!-- SUB: header_cell -->
		<td>{VAR:hcell}</td>
	<!-- END SUB: header_cell -->
</tr>
<!-- END SUB: header -->
<!-- </header -->

<!-- contents -->
<!-- SUB: content_row -->
<tr>
	<!-- SUB: content_cell -->
	<td valign="top">
	<!-- nested table gives a greater flexibility -->
	<table border="0" width="100%" height="100%">
	<tr>
	<td>
	{VAR:cell}
	</td>
	</tr>
	</table>
	</td>
	<!-- END SUB: content_cell -->
</tr>
<!-- END SUB: content_row -->
</table>
