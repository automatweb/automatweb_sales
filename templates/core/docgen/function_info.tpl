<style type="text/css">

.text {
	font-family:  Verdana, Arial, sans-serif;
	font-size: 11px;
	color: #000000;
	line-height: 18px;
	text-decoration: none;
}

.text a {
	color: #058AC1;
	text-decoration:underline;
}

.text a:hover {
	color: #000000;
	text-decoration: underline;
}
p {
	margin-left: 30px;
}

table {
	font-size: inherit;
}

table.class_info {
	width: 50%;
	border-collapse: collapse;
	margin-left: 30px;
}

table.class_info td {
	border: 1px solid silver;
	padding: 3px;
}

table.class_info th {
	border: 1px solid silver;
	background-color: rgb(255, 255, 169);
	padding: 3px;
}
</style>


<table border="0" width="100%" cellpadding="2" cellspacing="0">
<tr>
		<td colspan="7" width="2" height="2" bgcolor="#000000"><img src='{VAR:baseurl}/automatweb/images/trans.gif'></td>
</tr>
<tr>
	<td colspan="3" rowspan="4" class="text" valign="top">
		<!-- SUB: HAS_API -->
		<b>API methods:</b><br>
		<!-- SUB: API_FUNCTION -->
		<a href='{VAR:view_func}'>{VAR:name}</a><br>
		<i>{VAR:short_comment}</i>
		<!-- END SUB: API_FUNCTION -->
		<br>
		<!-- END SUB: HAS_API -->

		<!-- SUB: HAS_ORB -->
		<B>ORB methods:</b><br>
		<!-- SUB: ORB_FUNCTION -->
		<a href='{VAR:view_func}'>{VAR:name}</a><br>
		<i>{VAR:short_comment}</i>
		<!-- END SUB: ORB_FUNCTION -->
		<br>
		<!-- END SUB: HAS_ORB -->

		<!-- SUB: HAS_CB -->
		<b>class_base methods:</b><br>
		<!-- SUB: CB_FUNCTION -->
		<a href='{VAR:view_func}'>{VAR:name}</a><br>
		<i>{VAR:short_comment}</i>
		<!-- END SUB: CB_FUNCTION -->
		<br>
		<!-- END SUB: HAS_CB -->

		<!-- SUB: HAS_OTHER -->
		<b>other public methods</b><br>
		<!-- SUB: OTHER_FUNCTION -->
		<a href='{VAR:view_func}'>{VAR:name}</a><br>
		<i>{VAR:short_comment}</i>
		<!-- END SUB: OTHER_FUNCTION -->
		<br>
		<!-- END SUB: HAS_OTHER -->

		<!-- SUB: HAS_PRIVATE -->
		<b>private methods</b><br>
		<!-- SUB: PRIVATE_FUNCTION -->
		<a href='{VAR:view_func}'>{VAR:name}</a><br>
		<i>{VAR:short_comment}</i>
		<!-- END SUB: PRIVATE_FUNCTION -->
		<br>
		<!-- END SUB: HAS_PRIVATE -->
	</td>
</tr>
</table>

<br><br><br>
<table border="0" width="100%" cellpadding="2" cellspacing="0">
<!-- SUB: LONG_FUNCTION -->
<tr>
		<td colspan="6" width="2" height="2" bgcolor="#000000"><img src='{VAR:baseurl}/automatweb/images/trans.gif'></td>
</tr>
<tr>
	<td class="text" colspan="6">
		<table border="0" width="100%">
			<tr>
				<td class="text"><a name='fn.{VAR:name}'></a><b>{VAR:proto}</b> - <a href='{VAR:view_usage}'>View usage</a> - <a href='{VAR:view_source}'>View source</a></td>
			</tr>
			<tr>
				<td class="text">
					<i>{VAR:short_comment}</i><br>
					<strong>Attributes:</strong>
					<table class="class_info">
						<tr>
							<th>Name</th>
							<th>Value</th>
						</tr>
					<!-- SUB: ATTRIB -->
						<tr>
							<td>{VAR:attrib_name}</td>
							<td>{VAR:attrib_value}</td>
						</tr>
					<!-- END SUB: ATTRIB -->
					</table>
					<strong>Parameters:</strong>
					<table class="class_info">
						<tr>
							<th>Name</th>
							<th>Type</th>
							<th>Default</th>
							<th>Comment</th>
							<th>Req.</th>
						</tr>
					<!-- SUB: PARAM -->
						<tr>
							<td>{VAR:param_name}</td>
							<td>{VAR:param_type}</td>
							<td>{VAR:param_default}</td>
							<td>{VAR:param_comment}</td>
							<td>{VAR:param_required}</td>
						</tr>
					<!-- END SUB: PARAM -->
					</table>

					<strong>Returns:</strong>
					<p class="returns">{VAR:returns}</p>
					<strong>Errors:</strong>
					<p class="errors">{VAR:errors}</p>
					<strong>Comment</strong>
					<p class="comment">{VAR:comment}</p>
					<strong>Examples:</strong>
					<p class="examples">{VAR:examples}</p>
				</td>
			</tr>
			<tr>
				<td class="text">{VAR:doc}</td>
			</tr>
		</table>
	</td>
</tr>
<!-- END SUB: LONG_FUNCTION -->
</table>
