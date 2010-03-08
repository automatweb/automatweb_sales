<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-4">
<title>arve</title>
<style type="text/css" media="all">
<!--
html {
	height: 100%
}
body {
	font-family: Arial, Helvetica, sans-serif;
	color: #000;
	font-size: 12px;
	margin-left: 15px;
	margin-top: 60px;
}
.suur {
	font-size: 18px;
}
.border {
	border-bottom: 1px solid #000;
}
.style3 {
	font-family: "Times New Roman", Times, serif;
	font-size: 18px;
	font-weight: bold;
}
.style6 {
	font-family: "Times New Roman", Times, serif;
	font-size: 16px;
}
.style8 {font-size: 14px}
-->
</style>
</head>

<body>
<table width="20" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="25">&nbsp;</td>
  </tr>
</table>

<table width="690" border="1" cellpadding="0" cellspacing="0" bordercolor="#000000">
  
  
  <tr>
    <td height="48" colspan="6" align="left" valign="middle" bgcolor="#CCCCCC" class="style3" style="padding-left:111px">Lisa nr. 1 arvele nr. {VAR:bill_no}<br>
    {VAR:time_spent_desc} </td>
  </tr>

  
  <tr valign="middle">
    <td width="96" height="44" align="center"><span class="style3">Kuup&auml;ev</span></td>
    <td width="465" align="left" style="padding-left:6px"><span class="style3"> </span></td>
    <td width="465" align="left" style="padding-left:6px"><span class="style3"> T&ouml;&ouml; sisu </span></td>
    <td width="97" align="center"><span class="style3">Kulunud aeg</span> </td>
  </tr>
  <!-- SUB: ROW -->
  <tr valign="middle">
    <td height="28" align="center"><span class="style8">{VAR:date}</span></td>
    <td height="28" align="center"><span class="style8">{VAR:comment}</span></td>
    <td align="left" style="padding-left: 6px"><span class="style8">{VAR:desc}</span></td>
   
    <td align="center"><span class="style8">{VAR:amt}</span></td>
  </tr>
    <!-- END SUB: ROW -->
  <tr valign="middle">  <td height="28" colspan="3" align="left" bgcolor="#CCCCCC"></td>

    <td height="28" colspan="3" align="left" bgcolor="#CCCCCC">



<table width="99" border="0" align="right" cellpadding="0" cellspacing="0">
      <tr>
        <td width="99"><div align="center" class="style3">{VAR:tot_amt} t</div></td>
      </tr>
    </table></td>
  </tr>
</table>
<br>
<br>
<table width="690" border="0" cellspacing="2" cellpadding="1">
  <tr>
    <td><span class="style6">{VAR:comment} </span></td>
  </tr>
</table>
<p>&nbsp;</p>
</body>
</html>
