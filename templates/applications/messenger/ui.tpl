<html>
<head>
<title>V3</title>
<script type="text/javascript" src="{VAR:u1}"></script>
<script type="text/javascript" src="{VAR:u2}"></script>
<style>
#mailbox {
	font-family: Arial,sans-serif;
	font-size: 80%;
	border-left: 8px solid #C3D9FF;
	border-spacing: 0px;
	border-top: 5px solid #C3D9FF;
	table-layout: fixed;
	width: 100%;
	empty-cells: show;
}
#mailbox td {
	border-bottom: 1px solid #CCC;
	white-space: nowrap;
	overflow: hidden; 
	padding-right: 3px;
}

#mailbox th { 
	border-bottom: 1px solid #EEE;
}

.newmsg {
	font-weight: bold;
	background-color: #FFF;
}

.readmsg {
	background-color: #E8EEF7;
}

.selmsg {
	background-color: #FFFFCC;
}


#folders {
	border-collapse: collapse;
	table-layout: fixed;
	width: 16ex;
	margin-top: 30px;
	border: 0px;
}

#folders td {
	font-size: 80%;
	font-family: Arial,sans-serif;
	white-space: nowrap;
	overflow: hidden;
}

#folders td a {
	text-decoration: none;
}

#folders .unreadfolder {
	font-weight: bold;
}

#folders .openfolder {
	font-weight: bold;
	background-color: #C3D9FF;
}

#pager {
	font-size: 80%;
	float: right;
	margin-right: 5px;
}

.navi {
	border: 0px;
	padding: 5px;
	background-color: #C3D9FF;
	height: 3ex;
	vertical-align: top;
}

</style>
<head>
<body>
<div style="color: red; font-weight: bold; background-color: #FAD163" id="error"></div>
<form name="marker" id="marker">

<table style="border: 0px solid black; border-collapse: collapse; margin: 0px; padding: 0px;">
<tr>
<td rowspan="3" valign="top" style="border: 0px; padding: 0px;">
	<table id="folders">
	</table>
</td>
<td id="mheader">
<!-- accessing this.value from onClick gives an empty value, although this.nodeName works and has the correct result -->
</td>
</tr>
<tr>
<td valign="top" style="border: 0px; padding: 0px; margin: 0px; width: 650px;">
<div id="loading" style="font-size: 200%; font-weight: bold;">Loading ....</div>
<table id="mailbox">
<!-- marker -->
<colgroup>
<col style="width: 20px; text-align: right;">
<!-- from -->
<col style="width: 20ex;">
<!-- answered -->
<col style="width: 10px; text-align: right;">
<!-- subject -->
<col style="width: 32ex;">
<!-- attach -->
<col style="width: 20px;">
<!-- size -->
<!--<col style="width: 50px;">-->
<!-- date -->
<col style="width: 6ex;">
</colgroup>
<!--
<tr>
<th></th>
<th>From</th>
<th></th>
<th>Subject</th>
<th>Att</th>
<th>Sz</th>
<th>Date</th>
</tr>
-->
<tbody>
</tbody>
</table>

</tr>
<tr>
<td id="mfooter">
</td>
</tr>
</td>
</tr>
</table>

<script type="text/javascript">
var mb = new mailbox();
mb.query_server();
mb.query_folders();
</script>
</form>
</body>
</html>
