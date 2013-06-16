<!-- SUB: ROW -->
		<tr>
			<!-- SUB: ROW.FIELD -->
			<{VAR:field.tag} {VAR:field.class} {VAR:field.data} {VAR:field.span} {VAR:field.style}>{VAR:field.value}</{VAR:field.tag}>
			<!-- END SUB: ROW.FIELD -->
		</tr>
<!-- END SUB: ROW -->

<h4>{VAR:caption}</h4>
<table id="{VAR:id}" class="table table-striped table-hover table-condensed">
	<!-- SUB: HEADER -->
	<thead>
		{VAR:HEADER.ROWS}
	</thead>
	<!-- END SUB: HEADER -->
	<!-- SUB: BODY -->
	<tbody>
		{VAR:BODY.ROWS}
	</tbody>
	<!-- END SUB: BODY -->
	<!-- SUB: FOOTER -->
	<tfoot>
		{VAR:FOOTER.ROWS}
	</tfoot>
	<!-- END SUB: FOOTER -->
</table>
