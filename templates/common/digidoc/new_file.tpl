<!-- SUB: UPLOAD -->
<html>
<body>
	Palun valige fail!
	<form action="" method="post" enctype="multipart/form-data">
		<input type="file" name="new_file"/><br>
		<input type="submit" value="Saada"/>
		<input type="hidden" name="ddoc" value="{VAR:ddoc}"/>
	</form>
</body>
</html>
<!-- END SUB: UPLOAD -->
<!-- SUB: DONE -->
<html>
<head>
	<script language="javascript">
		window.opener.location.reload(true);
		setTimeout("window.close()", 1000);
	</script>
</head>
<body>
	Fail edukalt lisatud!
</body>
</html>
<!-- END SUB: DONE -->
