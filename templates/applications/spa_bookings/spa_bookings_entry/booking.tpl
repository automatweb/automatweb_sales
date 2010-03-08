<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>{VAR:title}</title>
<style type="text/css">
<!--
body {padding: 0px; margin: 0px;}
.style6 {font-size: 12px; font-family: Arial, Helvetica, sans-serif; }
.style7 {border-top: solid 2px #054ea1; font-family: Arial, Helvetica, sans-serif; font-size: 12px}
.style8 {border-bottom: solid 2px #8bc640; font-family: Arial, Helvetica, sans-serif; font-size: 12px; font-weight: bold;}
.style9 {font-family: Arial, Helvetica, sans-serif; font-size: 12px;}
.style10 {border-top: solid 2px #8bc640; }
-->
</style>
</head>

<body>
<table width="100%" border="0" cellspacing="0" cellpadding="6">
  <tr>
    <td width="10%"><img src="{VAR:baseurl}/img/logo.gif" width="112" height="112" /></td>
    <td width="90%"><table border="0" cellspacing="2" >
      <tr>
        <td width="200" class="style6">{VAR:LC_BRON1}</td>
        <td width="550" class="style6">{VAR:bureau}</td>
      </tr>
      <tr>
        <td class="style6">{VAR:LC_KL_NAME}</td>
        <td class="style6"><b>{VAR:person}</b></td>
      </tr>
      <tr>
        <td class="style6">{VAR:LC_PAKETT}</td>
        <td class="style6">{VAR:package}</td>
      </tr>
      <tr>
        <td class="style6">{VAR:LC_SAABUMINE}</td>
        <td class="style6">{VAR:from}</td>
      </tr>
      <tr>
        <td class="style6">{VAR:LC_LAHKUMINE}</td>
        <td class="style6">{VAR:to}</td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td colspan="2" class="style7">{VAR:LC_BRON2}</td>
  </tr>
  <tr>
    <td colspan="2"><table width="100%" border="0" cellspacing="1" cellpadding="2">
      <tr>
        <td class="style8">{VAR:LC_PROTS}</td>
        <td class="style8">{VAR:LC_RUUM}</td>
        <td class="style8">{VAR:LC_KUUP}</td>
        <td class="style8">{VAR:LC_KELL}</td>
      </tr>
      <!-- SUB: BOOKING -->
      <tr>
        <td class="style9" bgcolor="#F7F7F7">Lorem ipsum dolor sit amet, consectetuer adipiscing elit.</td>
        <td class="style9" bgcolor="#F7F7F7">{VAR:r_room}</td>
        <td class="style9" bgcolor="#F7F7F7">{VAR:r_from}</td>
        <td class="style9" bgcolor="#F7F7F7">{VAR:r_to}</td>
      </tr>
      <!-- END SUB: BOOKING -->
      <tr>
        <td class="style10">&nbsp;</td>
        <td class="style10">&nbsp;</td>
        <td class="style10">&nbsp;</td>
        <td class="style10">&nbsp;</td>
      </tr>

    </table></td>
  </tr>
</table>
</body>
</html>
