<html>
<head>
<title>Test results</title>
<style>
#resulttable {
	border-collapse: collapse;
	border: 1px solid #CCC;
	width: 100%;
}

#resulttable td {
	border: 1px solid #CCC;
	padding: 3px;
}

#resulttable th {
	border: 1px solid #CCC;
	background: #EEE;
	padding: 3px;
}

.resultPASS {
	color: green;
	font-weight: bold;
	text-align: center;
}

.resultFAIL {
	color: red;
	font-weight: bold;
	text-align: center;
}

#testsummary {
	background: #EEE;
	margin-top: 10px;
	border: 1px solid #CCC;
}

</style>
</head>
<body>
<h1>HTMLified test results</h1>
<table id="resulttable">
<tr>
<th>Name</th>
<th>Description</th>
<th>Result</th>
<th>Comment</th>
<!-- SUB: TEST -->
<tr>
<td>{VAR:name}</td>
<td>{VAR:description}</td>
<td class="result{VAR:result}">{VAR:result}</td>
<td>{VAR:comment}</td>
</tr>
<!-- END SUB: TEST -->
</tr>
</table>
<div id="testsummary">
Tests run: {VAR:test_count}<br>
Passed: {VAR:passed}<br>
Failed: {VAR:failed}<br>
</div>
</body>
</html>
